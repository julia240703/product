<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Motor;
use App\Models\Category;
use App\Models\MotorType;
use App\Models\MotorColor;
use App\Models\MotorFeature;
use App\Models\MotorSpecification;
use App\Models\MotorAccessory;
use App\Models\AccessoryCategory;
use App\Models\GeneralAccessory;
use App\Models\MotorPart;
use App\Models\Apparel;
use App\Models\ApparelCategory;
use App\Models\Branch;
use App\Models\PriceList;
use App\Models\BranchLocation;
use App\Models\Banner;
use App\Models\TestRide;
use App\Models\CreditSimulation;
use App\Models\CreditHeader;
use App\Models\CreditItem;

class PublicControllerSatu extends Controller
{
    // --- HOME / LANDING ---
    public function home($categoryName = null)
    {
        $banners = Banner::where('status', 'active')
            ->whereHas('bannerTemplate', fn($q) => $q->where('name', 'Home'))
            ->orderBy('order')
            ->get();

        $categories = Category::all();

        // helper buat mapping properti tampilan (image + min OTR)
        $hydrateType = function (MotorType $t) {
            // gambar: cover → thumbnail varian pertama → placeholder
            $firstThumb   = optional($t->motors->first())->thumbnail;
            $t->image_url = $this->imgUrl($t->cover_image ?: $firstThumb);

            // harga dari PriceList (min di semua VARIAN/TYPE NAME = nama TIPE)
            // ambil teks persis seperti di price list, bandingkannya pakai angka
            $rows   = PriceList::where('motorcycle_name', $t->name)->get(['price']);
            $minRow = $rows->sortBy(fn($r) => $this->priceToInt($r->price))->first();
            $t->display_price_from_text = $minRow?->price ?? null;             // aslinya
            $t->display_price_from_fmt  = $this->fmtPriceText($minRow?->price); // yg sudah diformat

            // info tambahan opsional
            $t->variant_count = $t->motors->count();

            return $t;
        };

        // === MODE KATEGORI TERTENTU (limit 4 tipe) ===
        if ($categoryName) {
            $category = Category::where('name', $categoryName)->firstOrFail();

            $types = MotorType::with(['motors' => fn($q) => $q
                        ->where('status','published')
                        ->orderBy('name')])
                    ->where('category_id', $category->id)
                    ->whereHas('motors', fn($q) => $q->where('status','published'))
                    ->orderBy('name')
                    ->limit(4)
                    ->get()
                    ->map($hydrateType);

            $seeMoreUrl = route('produk.category', $category->name);

            return view('pages.public.home', [
                'banners'      => $banners,
                'categories'   => $categories,
                'categoryName' => $categoryName,
                'types'        => $types,
                'seeMoreUrl'   => $seeMoreUrl,
            ]);
        }

        // === MODE SEMUA: 1 tipe per kategori, lalu isi sampai 6 ===
        $picked    = collect();
        $pickedIds = [];

        foreach ($categories as $cat) {
            if ($picked->count() >= 6) break;

            $one = MotorType::with(['motors' => fn($q) => $q
                        ->where('status','published')
                        ->orderBy('name')])
                    ->where('category_id', $cat->id)
                    ->whereHas('motors', fn($q) => $q->where('status','published'))
                    ->orderBy('name')
                    ->first();

            if ($one) {
                $picked->push($one);
                $pickedIds[] = $one->id;
            }
        }

        $need = 6 - $picked->count();
        if ($need > 0) {
            $fillers = MotorType::with(['motors' => fn($q) => $q
                            ->where('status','published')
                            ->orderBy('name')])
                        ->whereHas('motors', fn($q) => $q->where('status','published'))
                        ->when(!empty($pickedIds), fn($q) => $q->whereNotIn('id', $pickedIds))
                        ->orderBy('name')
                        ->limit($need)
                        ->get();
            $picked = $picked->concat($fillers)->take(6);
        } else {
            $picked = $picked->take(6);
        }

        $types = $picked->map($hydrateType);

        $seeMoreUrl = route('produk');

        return view('pages.public.home', [
            'banners'      => $banners,
            'categories'   => $categories,
            'categoryName' => null,
            'types'        => $types,
            'seeMoreUrl'   => $seeMoreUrl,
        ]);
    }

    // --- PRODUK (LIST TIPE / PER KATEGORI) ---
    public function produk($categoryName = null)
    {
        $categories = Category::all();

        // ambil TYPE yang punya minimal 1 motor published
        $types = MotorType::with([
                'category:id,name',
                'motors' => fn($q) => $q->where('status','published')->orderBy('name'),
            ])
            ->when($categoryName, function ($q) use ($categoryName) {
                $cat = Category::where('name', $categoryName)->firstOrFail();
                $q->where('category_id', $cat->id);
            })
            ->whereHas('motors', fn($q) => $q->where('status','published'))
            ->orderBy('name')
            ->get();

        // === Hitung "Harga Mulai" per TYPE dari price_lists dgn teks asli ===
        $typeNames = $types->pluck('name')->unique()->values();
        $priceRows = PriceList::whereIn('motorcycle_name', $typeNames)
                        ->get(['motorcycle_name','price']);

        // Map: nama_tipe -> teks harga termurah (bandingkan sebagai angka)
        $minTextByType = $priceRows->groupBy('motorcycle_name')->map(function ($rows) {
            return optional(
                $rows->sortBy(fn($r) => $this->priceToInt($r->price))->first()
            )->price;
        });

        // set properti tampilan per TYPE
        $types = $types->map(function ($t) use ($minTextByType) {
            $hero          = $t->cover_image ?: optional($t->motors->first())->thumbnail;
            $t->image_url  = $this->imgUrl($hero);
            $t->display_price_from_text = $minTextByType[$t->name] ?? null;
            $t->display_price_from_fmt  = $this->fmtPriceText($t->display_price_from_text);
            $t->variant_count           = $t->motors->count();
            return $t;
        });

        return view('pages.public.product', [
            'types'        => $types,
            'categories'   => $categories,
            'categoryName' => $categoryName,
        ]);
    }

    // --- GATE: dari TIPE → (langsung DETAIL varian | PILIH VARIAN) ---
    public function typeGate($id)
    {
        $type = MotorType::with([
            'motors' => fn($q) => $q->where('status', 'published')->orderBy('name')
        ])->findOrFail($id);

        if ($type->motors->isEmpty()) {
            abort(404);
        }

        if ($type->motors->count() === 1) {
            return redirect()->route('motor.detail', $type->motors->first()->id);
        }

        // Ambil harga teks/angka dari price_lists
        $variantNames = $type->motors->pluck('name')->unique()->values();
        $priceMap     = PriceList::whereIn('motor_type', $variantNames)
                            ->pluck('price','motor_type');

        $motors = $type->motors->map(function ($m) use ($priceMap) {
            $m->image_url = $this->imgUrl($m->thumbnail ?? null);

            $raw = $priceMap[$m->name] ?? null;          // bisa numeric atau string
            $m->display_price_raw = $raw;                // simpan kalau perlu
            $m->display_price     = $this->fmtPriceText($raw); // -> "Rp 18.168.000"

            return $m;
        });

        return view('pages.public.typeVariants', [
            'type'   => $type,
            'motors' => $motors,
        ]);
    }

    // --- MOTOR DETAIL ---
    public function motorDetail(Request $request, $id)
    {
        $motor = Motor::with([
                'category',
                'colors',
                'features',
                'specifications' => fn($q) => $q->orderBy('category')->orderBy('order')->orderBy('atribut'),
                'accessories',
                'parts',
            ])
            ->where('status', 'published')
            ->where('id', $id)
            ->firstOrFail();

        $banner = Banner::where('status', 'active')
            ->whereHas('bannerTemplate', fn($q) => $q->where('name', 'Detail Produk'))
            ->where('title', 'like', '%'.$motor->name.'%')
            ->orderBy('order')
            ->first();

        $accessories = $motor->accessories->sortBy('name')->values();

        // ===== Rekomendasi: tambahkan relasi type utk fallback price =====
        $recommended = Motor::with([
                'colors' => fn($q) => $q->oldest(),
                'type:id,name',
            ])
            ->where('category_id', $motor->category_id ?? null)
            ->where('id', '!=', $motor->id)
            ->where('status', 'published')
            ->latest('updated_at')
            ->take(2)
            ->get();

        // ===== Hydrate harga "Harga Mulai" (VARIAN → TIPE) dari price_lists =====
        if ($recommended->isNotEmpty()) {
            $variantNames = $recommended->pluck('name')->unique()->values();
            $typeNames    = $recommended->pluck('type.name')->filter()->unique()->values();

            $pv = \App\Models\PriceList::whereIn('motor_type', $variantNames)
                    ->get(['motor_type','price']);
            $pt = \App\Models\PriceList::whereIn('motorcycle_name', $typeNames)
                    ->get(['motorcycle_name','price']);

            $minByVariant = $pv->groupBy('motor_type')->map(
                fn($g) => optional($g->sortBy(fn($r)=>$this->priceToInt($r->price))->first())->price
            );
            $minByType = $pt->groupBy('motorcycle_name')->map(
                fn($g) => optional($g->sortBy(fn($r)=>$this->priceToInt($r->price))->first())->price
            );

            $recommended = $recommended->map(function ($m) use ($minByVariant, $minByType) {
                $rawText = $minByVariant[$m->name] ?? ($minByType[optional($m->type)->name] ?? null);

                // simpan dua versi seperti di Home/Produk
                $m->rec_price_from_text = $rawText;                 // teks asli dari price_lists
                $m->rec_price_from_fmt  = $this->fmtPriceText($rawText); // "Rp 18.168.000" | null

                return $m;
            });
        }

        $showBack = $request->query('return_to') === 'compare';
        $backUrl  = $showBack ? route('compare.result') : null;

        $spinUrl = null;
        if (!empty($motor->spin_gif)) {
            $isFull = \Illuminate\Support\Str::startsWith($motor->spin_gif, ['http://', 'https://']);
            $spinUrl = $isFull ? $motor->spin_gif : asset('storage/'.$motor->spin_gif);
        }

        return view('pages.public.productDetail', compact(
            'motor',
            'banner',
            'accessories',
            'recommended',
            'showBack',
            'backUrl',
            'spinUrl',
        ));
    }

    // --- MOTOR PER CATEGORY ---
    public function motorsByCategory($name)
    {
        $category = Category::where('name', $name)->firstOrFail();
        $motors = Motor::with('category')
            ->where('status', 'published')
            ->where('category_id', $category->id)
            ->get();

        return view('public.motors.by.category', compact('motors', 'category'));
    }

    // === BANDINGKAN MOTOR — LANDING (6 slot) ===
    public function compareMenu(Request $request)
    {
        $request->session()->forget('compare_slots');
        $selectedCount = 0;
        $nextSlot      = 0;

        return view('pages.public.compareMenu', [
            'selectedCount' => $selectedCount,
            'nextSlot'      => $nextSlot,
        ]);
    }

    // PILIH MODEL
    public function comparePick(Request $request)
{
    $categories   = Category::orderBy('id')->get(['id','name']);
    $activeCatId  = (int) $request->query('category', 0);

    if ($activeCatId > 0) {
        // --- MODE: per kategori (paginate) ---
        $motors = Motor::with(['type:id,name'])
            ->where('status','published')
            ->where('category_id',$activeCatId)
            ->orderBy('name')
            ->paginate(12);

        // kumpulkan nama varian & tipe untuk query batch ke price_lists
        $variants = $motors->getCollection()->pluck('name')->unique()->values();
        $types    = $motors->getCollection()->pluck('type.name')->filter()->unique()->values();

        // ambil semua baris price list yang relevan
        $pv = PriceList::whereIn('motor_type', $variants)->get(['motor_type','price']);
        $pt = PriceList::whereIn('motorcycle_name', $types)->get(['motorcycle_name','price']);

        // map harga minimum (pakai angka utk sort → ambil yang terkecil)
        $minByVariant = $pv->groupBy('motor_type')->map(
            fn($g) => optional($g->sortBy(fn($r)=>$this->priceToInt($r->price))->first())->price
        );
        $minByType = $pt->groupBy('motorcycle_name')->map(
            fn($g) => optional($g->sortBy(fn($r)=>$this->priceToInt($r->price))->first())->price
        );

        // hydrate properti tampilan
        $motors->getCollection()->transform(function ($m) use ($minByVariant, $minByType) {
            $m->image_url = $this->imgUrl($m->thumbnail ?? null);

            // 1) harga per VARIAN; 2) fallback harga per TIPE
            $raw = $minByVariant[$m->name] ?? ($minByType[optional($m->type)->name] ?? null);

            $m->display_price = $this->fmtPriceText($raw); // "Rp 18.168.000" atau null
            return $m;
        });

        $showAll = false;

    } else {
        // --- MODE: semua (collection) ---
        $motors = Motor::with(['category','type:id,name'])
            ->where('status','published')
            ->orderBy('name')
            ->get();

        $variants = $motors->pluck('name')->unique()->values();
        $types    = $motors->pluck('type.name')->filter()->unique()->values();

        $pv = PriceList::whereIn('motor_type', $variants)->get(['motor_type','price']);
        $pt = PriceList::whereIn('motorcycle_name', $types)->get(['motorcycle_name','price']);

        $minByVariant = $pv->groupBy('motor_type')->map(
            fn($g) => optional($g->sortBy(fn($r)=>$this->priceToInt($r->price))->first())->price
        );
        $minByType = $pt->groupBy('motorcycle_name')->map(
            fn($g) => optional($g->sortBy(fn($r)=>$this->priceToInt($r->price))->first())->price
        );

        $motors = $motors->map(function ($m) use ($minByVariant, $minByType) {
            $m->image_url = $this->imgUrl($m->thumbnail ?? null);

            $raw = $minByVariant[$m->name] ?? ($minByType[optional($m->type)->name] ?? null);

            $m->display_price = $this->fmtPriceText($raw);
            return $m;
        });

        $showAll = true;
    }

    $selectedIds   = collect($request->session()->get('compare_slots', []))->values()->all();
    $selectedCount = count($selectedIds);

    return view('pages.public.comparePick', compact(
        'categories','activeCatId','motors','showAll','selectedIds','selectedCount'
    ));
}

    // === Simpan pilihan motor (tanpa slot) ===
    public function compareStore(Request $request)
    {
        $data = $request->validate([
            'motor_id' => 'required|exists:motors,id',
            'slot'     => 'nullable|integer|min:0|max:5',
        ]);

        $slots = $request->session()->get('compare_slots', []);

        if (in_array((int)$data['motor_id'], $slots, true)) {
            return back()->with('info', 'Model ini sudah dipilih.');
        }

        $slotIndex = null;
        if ($request->filled('slot') && !array_key_exists((int)$data['slot'], $slots)) {
            $slotIndex = (int)$data['slot'];
        } else {
            for ($i = 0; $i < 6; $i++) {
                if (!array_key_exists($i, $slots)) { $slotIndex = $i; break; }
            }
        }
        if ($slotIndex === null) {
            return back()->with('error', 'Maksimal 6 model.');
        }

        $slots[$slotIndex] = (int)$data['motor_id'];
        $request->session()->put('compare_slots', $slots);

        return back()->with('success', 'Ditambahkan.');
    }

    // === Hapus motor dari daftar (by motor_id) ===
    public function compareRemove(Request $request)
    {
        $data = $request->validate([
            'motor_id' => 'required|integer|exists:motors,id',
        ]);

        $slots = $request->session()->get('compare_slots', []);
        $idx = array_search((int)$data['motor_id'], $slots, true);
        if ($idx !== false) {
            unset($slots[$idx]);
            $request->session()->put('compare_slots', $slots);
            $request->session()->flash('open_slot', $idx);
        }
        return back()->with('success', 'Model dihapus.');
    }

    // === HASIL PERBANDINGAN ===
    public function compareResult(Request $request)
{
    $slots = $request->session()->get('compare_slots', []);      // [slotIndex => motor_id]
    $ids   = collect($slots)->sortKeys()->values();              // urut sesuai slot 0..5

    if ($ids->count() < 2) {
        return redirect()->route('compare.pick')
            ->with('error', 'Pilih minimal 2 model terlebih dahulu.');
    }

    // Ambil motor + tipe (perlu nama tipe utk fallback price)
    $motorsRaw = Motor::with(['type:id,name'])
        ->where('status', 'published')
        ->whereIn('id', $ids->all())
        ->get();

    // Validasi slot yang hilang
    $foundIds = $motorsRaw->pluck('id')->all();
    $missing  = $ids->diff($foundIds);
    if ($missing->isNotEmpty()) {
        $newSlots = [];
        foreach ($slots as $slotIdx => $mid) {
            if (in_array($mid, $foundIds)) $newSlots[$slotIdx] = $mid;
        }
        $request->session()->put('compare_slots', $newSlots);
        $ids = collect($newSlots)->sortKeys()->values();
    }

    // === Tarik harga dari price_lists (prioritas varian → fallback tipe) ===
    $variantNames = $motorsRaw->pluck('name')->unique()->values();
    $typeNames    = $motorsRaw->pluck('type.name')->filter()->unique()->values();

    $pv = \App\Models\PriceList::whereIn('motor_type', $variantNames)
        ->get(['motor_type','price']);
    $pt = \App\Models\PriceList::whereIn('motorcycle_name', $typeNames)
        ->get(['motorcycle_name','price']);

    $minByVariant = $pv->groupBy('motor_type')->map(
        fn($g) => optional($g->sortBy(fn($r)=>$this->priceToInt($r->price))->first())->price
    );
    $minByType = $pt->groupBy('motorcycle_name')->map(
        fn($g) => optional($g->sortBy(fn($r)=>$this->priceToInt($r->price))->first())->price
    );

    // Bentuk objek untuk FE + harga terformat
    $motors = $motorsRaw->map(function ($m) use ($minByVariant, $minByType, $ids) {
            $raw = $minByVariant[$m->name] ?? ($minByType[optional($m->type)->name] ?? null);
            return (object)[
                'id'            => $m->id,
                'name'          => $m->name,
                'image_url'     => $this->imgUrl($m->thumbnail ?? null),
                'detail_url'    => route('motor.detail', ['id' => $m->id, 'return_to' => 'compare']),
                'display_price' => $this->fmtPriceText($raw), // "Rp 18.168.000" | null
            ];
        })
        ->sortBy(fn($mm) => $ids->search($mm->id))
        ->values();

    $motorMap = $motors->keyBy('id');

    // === Spesifikasi ===
    $aliasIndex  = $this->aliasIndex();
    $displayName = $this->attrDisplayMap();

    $rows = \App\Models\MotorSpecification::query()
        ->whereIn('motor_id', $ids->all())
        ->orderBy('category')->orderBy('order')->orderBy('atribut')
        ->select(['motor_id', 'category', 'atribut', 'detail as val'])
        ->get();

    $specs = $rows->groupBy('category')->map(function ($items) use ($ids, $aliasIndex, $displayName) {
        $byCanon = $items->groupBy(function ($r) use ($aliasIndex) {
            $norm = $this->normalizeAttr($r->atribut);
            return $aliasIndex[$norm] ?? $norm;
        });

        return $byCanon->map(function ($groupRows, $canonKey) use ($ids, $displayName) {
            $label = $displayName[$canonKey]
                ?? ($groupRows->first()->atribut ?? ucfirst($canonKey));

            $cells = $ids->map(function ($mid) use ($groupRows) {
                $r = $groupRows->firstWhere('motor_id', $mid);
                return $r ? ($r->val ?? '—') : '—';
            })->values()->all();

            return ['atribut' => $label, 'cells' => $cells];
        })->values();
    });

    // Tidak ada kategori/baris "Harga" di tabel spesifikasi
    $categories = $specs->keys()->values();
    $openSlot   = $request->session()->pull('open_slot', null);

    return view('pages.public.compareResult', [
        'slots'      => $slots,
        'motorMap'   => $motorMap,
        'motors'     => $motors,
        'motorIds'   => $ids->all(),
        'categories' => $categories,
        'specs'      => $specs,
        'openSlot'   => $openSlot,
    ]);
}

    private function aliasIndex(): array
    {
        $idx = [];
        foreach ($this->attrAliasMap() as $canon => $variants) {
            $idx[$this->normalizeAttr($canon)] = $canon;
            foreach ($variants as $v) {
                $idx[$this->normalizeAttr($v)] = $canon;
            }
        }
        return $idx;
    }

    private function attrAliasMap(): array
    {
        return [
            'starter'      => ['starter','sistem starter','tipe starter'],
            'kopling'      => ['kopling','tipe kopling'],
            'tipe_mesin'   => ['tipe mesin','jenis mesin','mesin'],
            'fuel_supply'  => [
                'sistem suplai bahan bakar','sistem supply bahan bakar',
                'supply bahan bakar','suplai bahan bakar','sistem bahan bakar','fuel system',
            ],
            'transmisi'    => [
                'transmisi','sistem transmisi','tipe transmisi','tipe tranmisi',
                'transmission','transmission type','type transmission',
            ],
            'sistem_pendingin' => ['sistem pendingin','sistem pendingin mesin','cooling system'],
            'torsi_maks'       => ['torsi maksimum','torsi maksimal','maximum torque','torsi puncak'],
            'berat_kosong'     => ['berat kosong','curb weight'],
            'baterai_aki'      => ['tipe baterai','tipe baterai atau aki','tipe battery/aki','tipe battery','battery','aki'],
            'rasio_kompresi'   => ['rasio kompresi','perbandingan kompresi','compression ratio','ratio kompresi'],
            'pelumas'          => [
                'jenis pelumas','pelumas','sistem pelumasan','pelumasan','tipe pelumas',
                'tipe minyak pelumas','minyak pelumas','jenis oli',
                'oil type','lubrication system','lubrication',
            ],
            'ground_clearance' => [
                'jarak terendah ke tanah','jarak terendah tanah','jarak bebas tanah',
                'jarak bebas ke tanah','ground clearance','clearance ground',
            ],
        ];
    }

    private function attrDisplayMap(): array
    {
        return [
            'starter'          => 'Starter',
            'kopling'          => 'Kopling',
            'tipe_mesin'       => 'Tipe Mesin',
            'fuel_supply'      => 'Sistem Suplai Bahan Bakar',
            'transmisi'        => 'Transmisi',
            'pelumas'          => 'Jenis Pelumas',
            'pelumasan'        => 'Jenis Pelumas',
            'sistem_pendingin' => 'Sistem Pendingin',
            'torsi_maks'       => 'Torsi Maksimum',
            'berat_kosong'     => 'Berat Kosong',
            'baterai_aki'      => 'Tipe Baterai / Aki',
            'rasio_kompresi'   => 'Rasio Kompresi',
            'ground_clearance' => 'Jarak Terendah Ke Tanah',
        ];
    }

    private function canonicalAttrKey(string $label): string
    {
        $norm = $this->normalizeAttr($label);
        if ($norm === '') return $label;

        $alias = $this->attrAliasMap();
        foreach ($alias as $canon => $alts) {
            $targets = array_merge([$canon], $alts);
            foreach ($targets as $alt) {
                if ($norm === $this->normalizeAttr($alt)) {
                    return $canon;
                }
            }
        }
        return $norm;
    }

    private function normalizeAttr(string $s): string
    {
        $x = mb_strtolower($s);
        $x = preg_replace('/[\/\-\_\–—]+/u', ' ', $x);
        $x = preg_replace('/\b(sistem|tipe|jenis|atau|dan|yang|yg|untuk|pada|dengan)\b/u', ' ', $x);

        $syn = [
            'aki' => 'baterai','accu' => 'baterai','battery' => 'baterai','batterai' => 'baterai',
            'supply' => 'suplai','compression' => 'kompresi','ratio' => 'rasio','cooling' => 'pendingin',
            'tranmisi' => 'transmisi','transmission' => 'transmisi','oli' => 'pelumas','oil' => 'pelumas',
            'lubrication' => 'pelumas','pelumasan' => 'pelumas',
        ];
        foreach ($syn as $from => $to) {
            $x = preg_replace('/\b'.$from.'\b/u', $to, $x);
        }

        $x = trim(preg_replace('/\s+/u', ' ', $x));
        if ($x !== '') {
            $tokens = preg_split('/\s+/u', $x, -1, PREG_SPLIT_NO_EMPTY);
            $tokens = array_values(array_unique($tokens));
            sort($tokens, SORT_STRING);
            return implode(' ', $tokens);
        }
        return $s;
    }

    // --- AKSESORIS (LIST) ---
    public function accessories(Request $request)
    {
        $key = $request->query('key');

        $banners = collect();
        foreach (['Aksesoris', 'Accessories', 'Home'] as $tplName) {
            $b = Banner::where('status', 'active')
                ->whereHas('bannerTemplate', fn($q) => $q->where('name', $tplName))
                ->orderBy('order')
                ->get();

            if ($b->isNotEmpty()) { $banners = $b; break; }
        }

        $categories = Category::all()
            ->filter(fn($c) => strtolower(trim($c->name)) !== 'big bike')
            ->values();

        $tabs = $categories->map(fn($c) => [
            'key'  => (string) $c->id,
            'name' => $c->name,
        ])->values()->all();

        $tabs[] = ['key' => 'general', 'name' => 'General Item'];

        $defaultKey = $categories->isNotEmpty() ? (string) $categories->first()->id : 'general';
        $activeKey  = $key ?: $defaultKey;

        $motors = collect();
        $generalAccessories = collect();

        if ($activeKey === 'general') {
            $generalAccessories = GeneralAccessory::with([
                    'images' => fn($q) => $q->orderBy('sort')->orderBy('id'),
                ])
                ->orderBy('name')
                ->get()
                ->map(function ($g) {
                    $src = $g->cover_image ?: optional($g->images->first())->image;
                    $g->image_url     = $this->imgUrl($src);
                    $g->display_price = $g->price;
                    return $g;
                });
        } elseif (ctype_digit((string) $activeKey)) {
            $cat = $categories->firstWhere('id', (int) $activeKey);
            abort_if(!$cat, 404);

            $motors = Motor::with('category')
                ->where('status', 'published')
                ->where('category_id', $cat->id)
                ->orderBy('name')
                ->get();
        } else {
            $activeKey = $defaultKey;
            if ($activeKey === 'general') {
                $generalAccessories = GeneralAccessory::with([
                        'images' => fn($q) => $q->orderBy('sort')->orderBy('id'),
                    ])
                    ->orderBy('name')
                    ->get()
                    ->map(function ($g) {
                        $src = $g->cover_image ?: optional($g->images->first())->image;
                        $g->image_url     = $this->imgUrl($src);
                        $g->display_price = $g->price;
                        return $g;
                    });
            } else {
                $motors = Motor::with('category')
                    ->where('status', 'published')
                    ->where('category_id', (int)$activeKey)
                    ->orderBy('name')->get();
            }
        }

        return view('pages.public.accessories', compact(
            'banners', 'tabs', 'activeKey', 'motors', 'generalAccessories'
        ));
    }

    // --- DETAIL AKSESORIS UNTUK MOTOR TERTENTU ---
    public function accessoriesMotorDetail($id)
    {
        $motor = Motor::with(['accessories' => fn ($q) => $q->orderBy('name')])
            ->where('status', 'published')
            ->where('id', $id)
            ->firstOrFail();

        $stageImage = $motor->accessory_thumbnail
            ? $this->imgUrl($motor->accessory_thumbnail)
            : $this->imgUrl((string) $motor->thumbnail);

        $hotspots = [];
        foreach ($motor->accessories as $acc) {
            $rx = $acc->x_percent ?? $acc->hotspot_x ?? $acc->pos_x ?? null;
            $ry = $acc->y_percent ?? $acc->hotspot_y ?? $acc->pos_y ?? null;
            if ($rx === null || $ry === null) continue;

            $x = (float) str_replace(',', '.', $rx);
            $y = (float) str_replace(',', '.', $ry);
            if (!is_finite($x) || !is_finite($y)) continue;

            $x = max(0, min(100, $x));
            $y = max(0, min(100, $y));

            $hotspots[] = [
                'id'          => $acc->id,
                'name'        => $acc->name,
                'x'           => $x,
                'y'           => $y,
                'image'       => $this->imgUrl($acc->image ?? null),
                'description' => $acc->description ?? null,
                'side'        => $acc->preferred_side ?? 'auto',
            ];
        }

        $accessories = $motor->accessories->map(function ($acc) {
    $acc->image_url     = $this->imgUrl($acc->image ?? null);
    // biarkan apa adanya: null kalau belum diisi
    $acc->display_price = $acc->price;
    return $acc;
});

        return view('pages.public.accessoriesMotorDetail', compact(
            'motor', 'stageImage', 'hotspots', 'accessories'
        ));
    }

    // --- DETAIL SATU AKSESORIS ---
public function accessoryDetail($id)
{
    $accessory = MotorAccessory::with('motor')->findOrFail($id);

    $gallery = collect([$accessory->image ?? null, $accessory->image_2 ?? null, $accessory->image_3 ?? null])
        ->filter()
        ->map(fn($p) => $this->imgUrl($p))
        ->values();
    if ($gallery->isEmpty()) $gallery = collect([asset('placeholder.png')]);

    $other = MotorAccessory::query()
        ->when($accessory->motor_id, fn($q) => $q->where('motor_id', $accessory->motor_id))
        ->when(!$accessory->motor_id, fn($q) => $q->whereNull('motor_id')->orWhere('is_general', true))
        ->where('id', '!=', $accessory->id)
        ->orderBy('name')->take(9)->get()
        ->map(function ($a) {
            $a->image_url     = $this->imgUrl($a->image ?? null);
            $a->display_price = $a->price ?? 0;
            return $a;
        });

    $data = [
        'id'           => $accessory->id,
        'name'         => $accessory->name,
        'function'     => $accessory->description ?? '-',
        'color'        => $accessory->color ?? '-',
        'material'     => $accessory->material ?? '-',
        'part_number'  => $accessory->part_number ?? '-',
        'price'        => $accessory->price ?? 0,
        'stock'        => $accessory->stock ?? $accessory->qty ?? null,
        'motor'        => $accessory->motor,
    ];

    // URL pemesanan dari back office (tanpa fallback). Boleh null.
    $orderUrl = optional($accessory->motor)->accessory_url;

    return view('pages.public.accessoryDetail', [
        'gallery'   => $gallery,
        'accessory' => (object) $data,
        'otherAccs' => $other,
        'orderUrl'  => $orderUrl, // dipakai Blade -> data-order-url
    ]);
}

    // --- DETAIL GENERAL ITEM ---
    public function generalAccessoryDetail($id)
    {
        $acc = GeneralAccessory::with([
                'images' => fn($q) => $q->orderBy('sort')->orderBy('id'),
            ])->findOrFail($id);

        $hero = $acc->cover_image ? $this->imgUrl($acc->cover_image) : null;
        $thumbs = $acc->images
            ->pluck('image')
            ->filter()
            ->map(fn($p) => $this->imgUrl($p))
            ->unique()
            ->values();
        if (!$hero) { $hero = $thumbs->first() ?? asset('placeholder.png'); }

        $other = GeneralAccessory::with(['images' => fn($q) => $q->orderBy('sort')->orderBy('id')])
            ->where('id', '!=', $acc->id)
            ->orderBy('name')
            ->take(9)
            ->get()
            ->map(function ($g) {
                $src = $g->cover_image ?: optional($g->images->first())->image;
                $g->display_name  = $g->name ?? '-';
                $g->image_url     = $this->imgUrl($src);
                $g->display_price = $g->price ?? 0;
                return $g;
            });

        $data = (object) [
            'id'          => $acc->id,
            'name'        => $acc->name ?? '-',
            'function'    => $acc->description ?? null,
            'variant'     => $acc->variant ?? null,
            'color'       => $acc->color ?? null,
            'material'    => $acc->material ?? null,
            'dimension'   => $acc->dimension ?? null,
            'weight'      => $acc->weight ?? null,
            'part_number' => $acc->part_number ?? null,
            'price'       => $acc->price ?? 0,
            'stock'       => $acc->stock ?? null,
        ];

        return view('pages.public.generalAccessoryDetail', [
            'hero'      => $hero,
            'thumbs'    => $thumbs,
            'acc'       => $data,
            'otherList' => $other,
        ]);
    }

    // helper url gambar
    private function imgUrl(?string $path): string
    {
        if (!$path) return asset('placeholder.png');
        if (Str::startsWith($path, ['http://','https://'])) return $path;
        $p = ltrim($path, '/');
        return Str::startsWith($p, 'storage/') ? asset($p) : asset('storage/'.$p);
    }

    // --- APPAREL (LIST + TABS) ---
    public function apparels(Request $request)
    {
        $key = $request->query('key');

        $banners = collect();
        foreach (['Apparel', 'Apparels', 'Home'] as $tplName) {
            $b = Banner::where('status', 'active')
                ->whereHas('bannerTemplate', fn($q) => $q->where('name', $tplName))
                ->orderBy('order')
                ->get();
            if ($b->isNotEmpty()) { $banners = $b; break; }
        }

        $apparelCategories = ApparelCategory::orderBy('id')->get();
        $tabs = $apparelCategories->map(fn($c) => ['key'=>(string)$c->id, 'name'=>$c->name])->values()->all();
        $defaultKey = $apparelCategories->isNotEmpty() ? (string)$apparelCategories->first()->id : null;
        $activeKey  = $key ?: $defaultKey;

        $apparels = collect();
        if ($activeKey && ctype_digit((string)$activeKey)) {
            $cat = $apparelCategories->firstWhere('id', (int)$activeKey);
            abort_if(!$cat, 404);

            $apparels = Apparel::with([
                    'category:id,name',
                    'images' => fn($q) => $q->orderBy('sort')->orderBy('id'),
                ])
                ->where('category_id', $cat->id)
                ->orderByDesc('is_new')
                ->orderBy('name_apparel')
                ->get()
                ->map(function ($a) {
                    $src = $a->cover_image ?: $a->image ?: optional($a->images->first())->image;
                    $a->display_name  = $a->name_apparel ?? '-';
                    $a->image_url     = $src ? $this->imgUrl($src) : asset('placeholder.png');
                    $a->display_price = $a->price;
                    $a->is_new        = (bool)($a->is_new ?? false);
                    return $a;
                });
        }

        return view('pages.public.apparels', [
            'banners'    => $banners,
            'tabs'       => $tabs,
            'activeKey'  => $activeKey,
            'apparels'   => $apparels,
            'categories' => $apparelCategories,
        ]);
    }

    // --- APPAREL DETAIL ---
public function apparelDetail($id)
{
    $apparel = Apparel::with([
            'category:id,name',
            'images' => fn($q) => $q->orderBy('sort')->orderBy('id'),
        ])->findOrFail($id);

    // Hero
    $hero = null;
    if ($apparel->cover_image) {
        $hero = $this->imgUrl($apparel->cover_image);
    } elseif ($apparel->image) {
        $hero = $this->imgUrl($apparel->image);
    }

    // Thumbs
    $thumbs = $apparel->images
        ->pluck('image')
        ->filter()
        ->map(fn($p) => $this->imgUrl($p))
        ->unique()
        ->values();

    if (!$hero) { $hero = $thumbs->first() ?? asset('placeholder.png'); }
    if ($thumbs->isEmpty() && $apparel->image && (!$apparel->cover_image || $apparel->image !== $apparel->cover_image)) {
        $thumbs = collect([$this->imgUrl($apparel->image)]);
    }

    // Parse list
    $sizes     = $this->splitList($apparel->size ?? '');
    $materials = $this->splitList($apparel->material ?? '', '/\r\n|\r|\n/u');

    // Lainnya
    $other = Apparel::with(['images' => fn($q) => $q->orderBy('sort')->orderBy('id')])
        ->when($apparel->category_id ?? null, fn($q) => $q->where('category_id', $apparel->category_id))
        ->where('id', '!=', $apparel->id)
        ->orderBy('name_apparel')
        ->take(9)
        ->get()
        ->map(function($a){
            $src = $a->cover_image ?: $a->image ?: optional($a->images->first())->image;
            $a->display_name  = $a->name_apparel ?? $a->name ?? '-';
            $a->image_url     = $src ? $this->imgUrl($src) : asset('placeholder.png');
            $a->display_price = $a->price ?? 0;
            return $a;
        });

    // URL order dari back office (kolom apparel_url)
    $orderUrl = null;
    if (!empty($apparel->apparel_url) && filter_var($apparel->apparel_url, FILTER_VALIDATE_URL)) {
        $orderUrl = $apparel->apparel_url;
    }

    $data = (object)[
        'id'            => $apparel->id,
        'category_id'   => $apparel->category_id ?? optional($apparel->category)->id,
        'name'          => $apparel->name_apparel ?? $apparel->name ?? '-',
        'description'   => $apparel->description ?? null,
        'sizes'         => $sizes,
        'material_list' => $materials,
        'material_raw'  => $apparel->material ?? null,
        'color'         => $apparel->color ?? null,
        'part_number'   => $apparel->part_number ?? $apparel->sku ?? null,
        'price'         => $apparel->price ?? 0,
        'stock'         => $apparel->stock ?? $apparel->qty ?? null,
        'order_url'     => $orderUrl, // <-- dipakai di QR
    ];

    return view('pages.public.apparelDetail', [
        'hero'      => $hero,
        'thumbs'    => $thumbs,
        'apparel'   => $data,
        'otherList' => $other,
    ]);
}

    /** Pecah string daftar (koma/semicolon/slash/baris baru) → array unik */
    private function splitList(?string $raw, string $pattern = '/[\r\n,;\/|]+/u'): array
    {
        if (!$raw) return [];

        $items = preg_split($pattern, $raw) ?: [];
        $items = array_map('trim', $items);
        $items = array_filter($items, fn($s) => $s !== '');

        return array_values(array_unique($items));
    }

    // === PARTS (LIST)
    public function parts(\Illuminate\Http\Request $request)
    {
        $key = $request->query('key');

        $banners = collect();
        foreach (['Parts', 'Part', 'Home'] as $tplName) {
            $b = \App\Models\Banner::where('status', 'active')
                ->whereHas('bannerTemplate', fn($q) => $q->where('name', $tplName))
                ->orderBy('order')
                ->get();
            if ($b->isNotEmpty()) { $banners = $b; break; }
        }

        $categories = \App\Models\Category::all()
            ->filter(fn($c) => strtolower(trim($c->name)) !== 'big bike')
            ->values();

        $tabs = $categories->map(fn($c) => [
            'key'  => (string) $c->id,
            'name' => $c->name,
        ])->values()->all();

        $defaultKey = $categories->isNotEmpty() ? (string) $categories->first()->id : null;
        $activeKey  = $key ?: $defaultKey;

        $motors = collect();
        if ($activeKey && ctype_digit((string) $activeKey)) {
            $cat = $categories->firstWhere('id', (int) $activeKey);
            abort_if(!$cat, 404);

            $motors = \App\Models\Motor::query()
                ->where('status', 'published')
                ->where('category_id', $cat->id)
                ->orderBy('name')
                ->get();
        }

        return view('pages.public.parts', compact(
            'banners', 'tabs', 'activeKey', 'motors'
        ));
    }

    public function partsDetail($id)
{
    $motor = Motor::query()
        ->where('status', 'published')
        ->with('category')
        ->findOrFail($id);

    // gambar hero
    $imageUrl = $this->imgUrl($motor->thumbnail ?? null);

    // URL PDF dari accessor (selalu /storage/..., same-origin)
    $pdfUrl = $motor->parts_pdf_url; // <- asset('storage/'.$motor->parts_pdf) atau null

    return view('pages.public.partsDetail', [
        'motor'    => $motor,
        'imageUrl' => $imageUrl,
        'pdfUrl'   => $pdfUrl,
    ]);
}

    // --- CABANG / DEALER ---
    public function branches(Request $request)
    {
        $areas  = BranchLocation::where('type', 'area')->orderBy('name')->get(['id','name']);
        $cities = BranchLocation::where('type', 'kota')->orderBy('name')->get(['id','name']);

        $serviceTokens = Branch::query()
            ->pluck('service')->filter()
            ->flatMap(function ($s) {
                $parts = preg_split('/[;,\/\|]+/u', (string) $s);
                $parts = array_map('trim', $parts);
                return array_filter($parts, fn($x) => $x !== '');
            })
            ->unique()->sort()->values();

        $data = $request->validate([
            'area'    => 'nullable|integer|min:1',
            'cabang'  => 'nullable|integer|min:1',
            'layanan' => 'nullable|string',
            'q'       => 'nullable|string',
        ]);

        $areaId  = (int) ($data['area']   ?? 0);
        $cityId  = (int) ($data['cabang'] ?? 0);
        $layanan = trim($data['layanan'] ?? '');
        $qText   = trim($data['q'] ?? '');

        $active = ['area'=>$areaId,'cabang'=>$cityId,'layanan'=>$layanan,'q'=>$qText];

        $branches = Branch::with(['area','city'])
            ->when($areaId > 0,  fn($q) => $q->where('area_id', $areaId))
            ->when($cityId > 0,  fn($q) => $q->where('city_id', $cityId))
            ->when($layanan !== '', fn($q) => $q->where('service', 'like', "%{$layanan}%"))
            ->when($qText !== '', function ($q) use ($qText) {
                $kwLower = mb_strtolower($qText, 'UTF-8');
                $kwLike  = '%' . strtr($kwLower, ['%'=>'\%','_'=>'\_','\\'=>'\\\\']) . '%';
                $chars   = preg_split('//u', preg_replace('/\s+/u', '', $kwLower), -1, PREG_SPLIT_NO_EMPTY);
                $seqLike = '%' . implode('%', array_map(
                    fn($c) => strtr($c, ['%'=>'\%','_'=>'\_','\\'=>'\\\\']),
                $chars)) . '%';

                $q->where(function ($w) use ($kwLike, $seqLike) {
                    $w->whereRaw('LOWER(name) LIKE ?', [$kwLike])
                      ->orWhereRaw('LOWER(name) LIKE ?', [$seqLike])
                      ->orWhereHas('city', function ($c) use ($kwLike, $seqLike) {
                          $c->whereRaw('LOWER(name) LIKE ?', [$kwLike])
                            ->orWhereRaw('LOWER(name) LIKE ?', [$seqLike]);
                      });
                });
            })
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $markers = $branches->map(function (Branch $b) {
            $lat = is_numeric($b->latitude)  ? (float) $b->latitude  : null;
            $lng = is_numeric($b->longitude) ? (float) $b->longitude : null;

            $mapsUrl = $b->url ?: (($lat && $lng)
                ? "https://www.google.com/maps?q={$lat},{$lng}"
                : 'https://www.google.com/maps/search/?api=1&query=' .
                  rawurlencode(trim(($b->name ?? ''))));

            return [
                'name'    => $b->name,
                'address' => $b->address,
                'phone'   => $b->phone ?? $b->phone2 ?? $b->phone3 ?? null,
                'lat'     => $lat,
                'lng'     => $lng,
                'mapsUrl' => $mapsUrl,
            ];
        })->values();

        $center = ['lat' => -6.200000, 'lng' => 106.816666];
        foreach ($markers as $m) { if ($m['lat'] && $m['lng']) { $center = ['lat'=>$m['lat'], 'lng'=>$m['lng']]; break; } }

        $mapsKey = config('services.google.maps_key');

        return view('pages.public.branches', compact(
            'areas','cities','serviceTokens','branches','markers','active','center','mapsKey'
        ));
    }

    // --- PRICE LIST ---
    public function priceList(Request $request)
{
    $categories  = Category::orderBy('id')->get(['id','name']);
    $activeCatId = (int) $request->query('category', 0);
    if ($activeCatId === 0 && $categories->isNotEmpty()) {
        $activeCatId = (int) $categories->first()->id;
    }

    $returnUrl = $request->query('return'); // <--- ambil param return

    $rows = \App\Models\PriceList::query()
        ->leftJoin('motors', 'motors.name', '=', 'price_lists.motor_type')
        ->where('motors.status', 'published')
        ->when($activeCatId > 0, fn($q) => $q->where('motors.category_id', $activeCatId))
        ->orderBy('price_lists.motorcycle_name')
        ->orderBy('price_lists.motor_type')
        ->get([
            'price_lists.id',
            'price_lists.motorcycle_name',
            'price_lists.motor_type',
            'price_lists.price',
            'motors.id as motor_id',
            'motors.category_id as cat_id',
        ]);

    $groups = $rows->groupBy('motorcycle_name')->map(function ($items) {
        $f = $items->first();
        return (object)[
            'motor_name' => $f->motorcycle_name,
            'motor_id'   => $f->motor_id,
            'types'      => $items->map(fn($r)=>(object)[
                'type'=>$r->motor_type,
                'price'=>$r->price,
            ])->values(),
        ];
    })->values();

    // lempar $returnUrl ke view
    return view('pages.public.priceList', compact('categories','activeCatId','groups','returnUrl'));
}

    /** Ambil matrix kredit terbaru utk motor (varian) tertentu */
    private function buildCreditMatrix(int $motorId): array
    {
        $header = CreditHeader::where('motor_id', $motorId)
            ->orderByDesc('valid_from')->orderByDesc('id')
            ->first();

        // Fallback: ada kasus motor_id di BO beda dengan yang dipilih FE (duplikat tipe/varian).
        if (!$header) {
            $motor = Motor::find($motorId);
            if ($motor) {
                $header = CreditHeader::whereHas('motor', function($q) use ($motor) {
                        $q->where('name', $motor->name);
                    })
                    ->orderByDesc('valid_from')->orderByDesc('id')
                    ->first();
            }
        }

        $tenors = [];
        $rows   = [];
        $dpList = [];
        $itemCount = 0;

        if ($header) {
            $items = CreditItem::where('header_id', $header->id)
                ->get(['dp_amount','tenor_months','installment']);
            $itemCount = $items->count();

            // tenor unik + sort numeric
            $tset = [];
            foreach ($items as $it) {
                $tset[(int)$it->tenor_months] = true;
            }
            $tenors = array_keys($tset);
            sort($tenors, SORT_NUMERIC);

            // group per DP
            $byDp = [];
            foreach ($items as $it) {
                $dp    = (int)$it->dp_amount;
                $tenor = (int)$it->tenor_months;
                $byDp[$dp] ??= [];
                $byDp[$dp][(string)$tenor] = (int)$it->installment;
            }
            ksort($byDp, SORT_NUMERIC);

            foreach ($byDp as $dp => $cols) {
                $rows[]   = ['dp' => (int)$dp, 'cols' => $cols];
                $dpList[] = (int)$dp;
            }
        }

        return [
            'tenors'  => $tenors,
            'rows'    => $rows,
            'dp_list' => array_values(array_unique($dpList, SORT_NUMERIC)),
            // meta debug agar mudah cek di console FE
            'meta'    => [
                'header_id'  => $header->id ?? null,
                'motor_id'   => $motorId,
                'items'      => $itemCount,
            ]
        ];
    }

    // --- SIMULASI KREDIT (page + endpoint JSON matrix) ---
public function creditSimulator(Request $request)
{
    // Endpoint JSON matrix (tanpa perubahan)
    if ($request->get('mode') === 'matrix' && $request->filled('motor_id')) {
        return response()->json($this->buildCreditMatrix((int)$request->query('motor_id')));
    }

    // motor yang punya data kredit
    $creditMotorIds = CreditHeader::whereHas('items')
        ->pluck('motor_id')->unique()->values();

    // === tambahkan motor_url di SELECT ===
    $motors = Motor::where('status', 'published')
        ->whereIn('id', $creditMotorIds)
        ->orderBy('name')
        ->get(['id','name','type_id','thumbnail','price','motor_url']); // <— HERE

    $usedTypeIds = $motors->pluck('type_id')->unique()->values();

    $types = MotorType::whereIn('id', $usedTypeIds)
        ->orderBy('name')
        ->get(['id','name','category_id']);

    $usedCatIds = $types->pluck('category_id')->unique()->values();
    $categories = Category::whereIn('id', $usedCatIds)
        ->orderBy('name')
        ->get(['id','name']);

    // harga dari price_lists (tetap)
    $variantNames   = $motors->pluck('name')->unique()->values();
    $typeNameById   = $types->pluck('name','id');
    $typeNames      = $typeNameById->values()->unique();

    $pv = PriceList::whereIn('motor_type', $variantNames)->get(['motor_type','price']);
    $pt = PriceList::whereIn('motorcycle_name', $typeNames)->get(['motorcycle_name','price']);

    $minByVariant = $pv->groupBy('motor_type')->map(
        fn($g) => optional($g->sortBy(fn($r)=>$this->priceToInt($r->price))->first())->price
    );
    $minByType = $pt->groupBy('motorcycle_name')->map(
        fn($g) => optional($g->sortBy(fn($r)=>$this->priceToInt($r->price))->first())->price
    );

    $typeCat = $types->pluck('category_id','id'); // [type_id => category_id]

    // === kirim order_url ke FE ===
    $datasetMotors = $motors->map(function($m) use ($typeCat, $typeNameById, $minByVariant, $minByType){
        $typeName = $typeNameById[$m->type_id] ?? null;
        $rawText  = $minByVariant[$m->name] ?? ($typeName ? ($minByType[$typeName] ?? null) : null);

        $otrFromPL = $rawText !== null ? $this->priceToInt($rawText) : 0;
        $otr       = $otrFromPL > 0 ? $otrFromPL : (int)($m->price ?? 0);

        return [
            'id'          => (int)$m->id,
            'name'        => $m->name,
            'type_id'     => (int)$m->type_id,
            'category_id' => (int)($typeCat[$m->type_id] ?? 0),
            'otr'         => $otr,
            'thumb'       => $this->imgUrl($m->thumbnail ?? null),
            'order_url'   => $m->motor_url ?: null,   // <— HERE
        ];
    })->values();

    $defaults = (object)[
        'min_dp_percent' => 10,
        'interest_year'  => 10.0,
    ];

    $dataset = [
        'categories' => $categories->map(fn($c)=>['id'=>$c->id,'name'=>$c->name])->values(),
        'types'      => $types->map(fn($t)=>['id'=>$t->id,'name'=>$t->name,'category_id'=>$t->category_id])->values(),
        'motors'     => $datasetMotors,
    ];

    return view('pages.public.creditSimulator', [
        'categories' => $categories,
        'defaults'   => $defaults,
        'dataset'    => $dataset,
    ]);
}

    /** Format harga apa pun (string/angka) menjadi "Rp 18.980.000" */
private function fmtPriceText($val): ?string
{
    if ($val === null) return null;

    // Kalau sudah ada "Rp", anggap sudah sedia tampil (biarkan apa adanya)
    $s = trim((string)$val);
    if (stripos($s, 'rp') !== false) return $s;

    // Jika nilai numerik (int/float/decimal "18980000.00"), format langsung
    if (is_numeric($val)) {
        $num = (float)$val;
        if ($num <= 0) return null;
        return 'Rp ' . number_format($num, 0, ',', '.');
    }

    // Normalisasi string angka umum: "18.980.000", "18.980.000,00", "18980000,00", dsb.
    $norm = preg_replace('/[^\d,\.]/', '', $s);

    // Buang bagian desimal kalau ada (".00" atau ",00" di belakang)
    $norm = preg_replace('/[\,\.]\d{1,2}$/', '', $norm);

    // Hilangkan pemisah ribuan (titik/koma di tengah)
    $norm = str_replace(['.', ','], '', $norm);

    if ($norm === '' || !ctype_digit($norm)) return null;

    return 'Rp ' . number_format((float)$norm, 0, ',', '.');
}

/** Konversi harga ke integer untuk perbandingan/sorting tanpa menggelembungkan 2 nol */
private function priceToInt($raw): int
{
    if ($raw === null) return 0;

    if (is_numeric($raw)) {
        return (int) round((float)$raw);
    }

    $s = trim((string)$raw);
    // Ambil hanya digit, koma, titik
    $s = preg_replace('/[^\d,\.]/', '', $s);
    // Jika ada desimal di belakang, buang desimalnya (kita butuh nilai bulat)
    $s = preg_replace('/[\,\.]\d{1,2}$/', '', $s);
    // Hilangkan pemisah ribuan
    $s = str_replace(['.', ','], '', $s);

    return $s === '' ? 0 : (int) $s;
}
}