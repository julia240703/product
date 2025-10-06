<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Motor;
use App\Models\Category;
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

class PublicControllerSatu extends Controller
{
    // --- HOME / LANDING ---
    public function home($categoryName = null)
    {
        // Banner Home aktif
        $banners = Banner::where('status', 'active')
            ->whereHas('bannerTemplate', fn($q) => $q->where('name', 'Home'))
            ->orderBy('order')
            ->get();

        // urutan kategori apa adanya
        $categories = Category::all();

        // Query dasar motor => hanya published
        $baseQuery = Motor::with(['category', 'colors', 'features'])
            ->where('status', 'published');

        // === MODE KATEGORI SPESIFIK: limit 4 ===
        if ($categoryName) {
            $category = Category::where('name', $categoryName)->firstOrFail();

            $motors = (clone $baseQuery)
                ->where('category_id', $category->id)
                ->orderByDesc('is_new')
                ->orderBy('name')
                ->limit(4)
                ->get();

            $seeMoreUrl = route('produk.category', $category->name);

            return view('pages.public.home', [
                'banners'      => $banners,
                'categories'   => $categories,
                'categoryName' => $categoryName,
                'motors'       => $motors,
                'seeMoreUrl'   => $seeMoreUrl,
            ]);
        }

        // === MODE SEMUA: 1 per kategori mengikuti urutan $categories, lalu isi sampai 6 ===
        $picked   = collect();
        $pickedId = [];

        foreach ($categories as $cat) {
            if ($picked->count() >= 6) break;

            $one = (clone $baseQuery)
                ->where('category_id', $cat->id)
                ->orderByDesc('is_new')
                ->orderBy('name')
                ->first();

            if ($one) {
                $picked->push($one);
                $pickedId[] = $one->id;
            }
        }

        // Lengkapi jika < 6
        $need = 6 - $picked->count();
        if ($need > 0) {
            $fillers = (clone $baseQuery)
                ->when(!empty($pickedId), fn($q) => $q->whereNotIn('id', $pickedId))
                ->orderByDesc('is_new')
                ->orderBy('name')
                ->limit($need)
                ->get();

            $picked = $picked->concat($fillers)->take(6);
        } else {
            $picked = $picked->take(6);
        }

        $seeMoreUrl = route('produk'); // SEMUA

        return view('pages.public.home', [
            'banners'      => $banners,
            'categories'   => $categories,
            'categoryName' => null,
            'motors'       => $picked,
            'seeMoreUrl'   => $seeMoreUrl,
        ]);
    }

    // --- PRODUK (SEMUA MOTOR / PER KATEGORI) ---
    public function produk($categoryName = null)
    {
        $categories = Category::all();
        $motors = Motor::with(['category', 'colors', 'features'])
            ->where('status', 'published'); // hanya published

        if ($categoryName) {
            $category = Category::where('name', $categoryName)->firstOrFail();
            $motors = $motors->where('category_id', $category->id);
        }

        // urutkan: yang baru dulu
        $motors = $motors->orderByDesc('is_new')->orderBy('name')->get();

        return view('pages.public.product', compact('motors', 'categories', 'categoryName'));
    }

    // --- MOTOR DETAIL ---
    public function motorDetail(Request $request, $id)
    {
        // penting: batasi ke published + gunakan firstOrFail (bukan findOrFail)
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

        $recommended = Motor::with(['colors' => fn($q) => $q->oldest()])
            ->where('category_id', $motor->category_id ?? null)
            ->where('id', '!=', $motor->id)
            ->where('status', 'published')
            ->latest('updated_at')
            ->take(2)
            ->get();

        $showBack = $request->query('return_to') === 'compare';
        $backUrl  = $showBack ? route('compare.result') : null;

        // === URL GIF 360°
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
            $motors = Motor::query()
                ->where('status', 'published')
                ->where('category_id', $activeCatId)
                ->orderBy('name')
                ->paginate(12)
                ->through(function($m){
                    $m->image_url     = $m->image_url ?? $this->imgUrl($m->thumbnail ?? null);
                    $m->display_price = $m->price ?? 0;
                    return $m;
                });
            $showAll = false;
        } else {
            $motors = Motor::with('category')
                ->where('status', 'published')
                ->orderBy('name')
                ->get()
                ->map(function($m){
                    $m->image_url     = $m->image_url ?? $this->imgUrl($m->thumbnail ?? null);
                    $m->display_price = $m->price ?? 0;
                    return $m;
                });
            $showAll = true;
        }

        $selectedIds   = collect($request->session()->get('compare_slots', []))->values()->all();
        $selectedCount = count($selectedIds);

        return view('pages.public.comparePick', [
            'categories'    => $categories,
            'activeCatId'   => $activeCatId,
            'motors'        => $motors,
            'showAll'       => $showAll,
            'selectedIds'   => $selectedIds,
            'selectedCount' => $selectedCount,
        ]);
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
        $slots = $request->session()->get('compare_slots', []); // [slotIndex => motor_id]
        $ids   = collect($slots)->sortKeys()->values();         // urut sesuai slot 0..5

        if ($ids->count() < 2) {
            return redirect()->route('compare.pick')
                ->with('error', 'Pilih minimal 2 model terlebih dahulu.');
        }

        // ==== AMBIL MOTOR (hanya published) ====
        $motorsRaw = Motor::where('status', 'published')
            ->whereIn('id', $ids->all())
            ->get();

        // Jika ada ID yang tidak ditemukan (sudah di-unpublish), bersihkan dari session
        $foundIds = $motorsRaw->pluck('id')->all();
        $missing  = $ids->diff($foundIds);
        if ($missing->isNotEmpty()) {
            $newSlots = [];
            foreach ($slots as $slotIdx => $mid) {
                if (in_array($mid, $foundIds)) $newSlots[$slotIdx] = $mid;
            }
            $request->session()->put('compare_slots', $newSlots);
            // perbarui $ids agar rapi
            $ids = collect($newSlots)->sortKeys()->values();
        }

        $motors = $motorsRaw
            ->map(function ($m) {
                return (object)[
                    'id'         => $m->id,
                    'name'       => $m->name,
                    'price'      => $m->price ?? 0,
                    'image_url'  => $m->image_url ?? $this->imgUrl($m->thumbnail ?? null),
                    'detail_url' => route('motor.detail', ['id' => $m->id, 'return_to' => 'compare']),
                ];
            })
            ->sortBy(fn($m) => $ids->search($m->id)) // jaga urutan kolom
            ->values();

        $motorMap = $motors->keyBy('id');

        // ==== ALIAS INDEX utk gabung baris ====
        $aliasIndex  = $this->aliasIndex();      // normalized_variation => canonical_key
        $displayName = $this->attrDisplayMap();  // canonical_key => label tampil

        // ==== AMBIL & GABUNG SPESIFIKASI ====
        $rows = MotorSpecification::query()
            ->whereIn('motor_id', $ids->all())
            ->orderBy('category')->orderBy('order')->orderBy('atribut')
            ->select(['motor_id', 'category', 'atribut', 'detail as val'])
            ->get();

        // per kategori accordion
        $specs = $rows->groupBy('category')->map(function ($items) use ($ids, $aliasIndex, $displayName) {

            // group by canonical key (via normalizer + alias)
            $byCanon = $items->groupBy(function ($r) use ($aliasIndex) {
                $norm = $this->normalizeAttr($r->atribut);
                return $aliasIndex[$norm] ?? $norm;  // jatuh ke norm kalau tak ada alias
            });

            // bentuk baris final
            return $byCanon->map(function ($groupRows, $canonKey) use ($ids, $displayName) {

                // label baris
                $label = $displayName[$canonKey]
                    ?? ($groupRows->first()->atribut ?? ucfirst($canonKey));

                // sel per motor sesuai urutan $ids
                $cells = $ids->map(function ($mid) use ($groupRows) {
                    $r = $groupRows->firstWhere('motor_id', $mid);
                    return $r ? ($r->val ?? '—') : '—';
                })->values()->all();

                return ['atribut' => $label, 'cells' => $cells];
            })->values();
        });

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
            // masukkan nama kanoniknya sendiri juga
            $idx[$this->normalizeAttr($canon)] = $canon;

            foreach ($variants as $v) {
                $idx[$this->normalizeAttr($v)] = $canon;
            }
        }
        return $idx;
    }

    // ===== Alias & normalisasi nama atribut =====
    private function attrAliasMap(): array
    {
        return [
            'starter'      => ['starter','sistem starter','tipe starter'],
            'kopling'      => ['kopling','tipe kopling'],
            'tipe_mesin'   => ['tipe mesin','jenis mesin','mesin'],

            // === Suplai/bahan bakar
            'fuel_supply'  => [
                'sistem suplai bahan bakar','sistem supply bahan bakar',
                'supply bahan bakar','suplai bahan bakar','sistem bahan bakar','fuel system',
            ],

            // === Transmisi (gabung semua variasi)
            'transmisi'    => [
                'transmisi','sistem transmisi','tipe transmisi','tipe tranmisi',
                'transmission','transmission type','type transmission',
            ],

            // === Pendingin
            'sistem_pendingin' => ['sistem pendingin','sistem pendingin mesin','cooling system'],

            // === Torsi
            'torsi_maks' => ['torsi maksimum','torsi maksimal','maximum torque','torsi puncak'],

            // === Berat kosong
            'berat_kosong' => ['berat kosong','curb weight'],

            // === Baterai / aki
            'baterai_aki' => ['tipe baterai','tipe baterai atau aki','tipe battery/aki','tipe battery','battery','aki'],

            // === Rasio kompresi
            'rasio_kompresi' => ['rasio kompresi','perbandingan kompresi','compression ratio','ratio kompresi'],

            // === Pelumas
            'pelumas' => [
                'jenis pelumas','pelumas','sistem pelumasan','pelumasan','tipe pelumas',
                'tipe minyak pelumas','minyak pelumas','jenis oli',
                'oil type','lubrication system','lubrication',
            ],

            'ground_clearance' => [
                'jarak terendah ke tanah',
                'jarak terendah tanah',
                'jarak bebas tanah',
                'jarak bebas ke tanah',
                'ground clearance',
                'clearance ground',
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

    /**
     * Kunci kanonik untuk menggabungkan label atribut yang serupa.
     */
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

    /**
     * Normalisasi string atribut → token terurut & unik.
     */
    private function normalizeAttr(string $s): string
    {
        $x = mb_strtolower($s);
        $x = preg_replace('/[\/\-\_\–—]+/u', ' ', $x);
        $x = preg_replace('/\b(sistem|tipe|jenis|atau|dan|yang|yg|untuk|pada|dengan)\b/u', ' ', $x);

        $syn = [
            'aki'           => 'baterai',
            'accu'          => 'baterai',
            'battery'       => 'baterai',
            'batterai'      => 'baterai',
            'supply'        => 'suplai',
            'compression'   => 'kompresi',
            'ratio'         => 'rasio',
            'cooling'       => 'pendingin',
            'tranmisi'      => 'transmisi',
            'transmission'  => 'transmisi',
            'oli'           => 'pelumas',
            'oil'           => 'pelumas',
            'lubrication'   => 'pelumas',
            'pelumasan'     => 'pelumas',
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

        // Tambahkan tab General Item
        $tabs[] = ['key' => 'general', 'name' => 'General Item'];

        $defaultKey = $categories->isNotEmpty() ? (string) $categories->first()->id : 'general';
        $activeKey  = $key ?: $defaultKey;

        $motors = collect();
        $generalAccessories = collect();

        if ($activeKey === 'general') {
            // GeneralAccessory
            $generalAccessories = GeneralAccessory::with([
                    'images' => fn($q) => $q->orderBy('sort')->orderBy('id'),
                ])
                ->orderBy('name')
                ->get()
                ->map(function ($g) {
                    $src = $g->cover_image ?: optional($g->images->first())->image;
                    $g->image_url     = $this->imgUrl($src);
                    $g->display_price = $g->price ?? 0;
                    return $g;
                });
        } elseif (ctype_digit((string) $activeKey)) {
            $cat = $categories->firstWhere('id', (int) $activeKey);
            abort_if(!$cat, 404);

            // hanya motor published
            $motors = Motor::with('category')
                ->where('status', 'published')
                ->where('category_id', $cat->id)
                ->orderBy('name')
                ->get();
        } else {
            // fallback jika key tak valid → kembali ke default
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
                        $g->display_price = $g->price ?? 0;
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

    // --- DETAIL AKSESORIS UNTUK MOTOR TERTENTU (bukan general) ---
    public function accessoriesMotorDetail($id)
    {
        // hanya motor yang published bisa dibuka
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
            $acc->display_price = $acc->price ?? 0;
            return $acc;
        });

        return view('pages.public.accessoriesMotorDetail', compact(
            'motor', 'stageImage', 'hotspots', 'accessories'
        ));
    }

    // --- DETAIL SATU AKSESORIS (MotorAccessory / non-general) ---
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

        return view('pages.public.accessoryDetail', [
            'gallery'   => $gallery,
            'accessory' => (object) $data,
            'otherAccs' => $other,
        ]);
    }

    // --- DETAIL GENERAL ITEM (GeneralAccessory) ---
    public function generalAccessoryDetail($id)
    {
        $acc = GeneralAccessory::with([
                'images' => fn($q) => $q->orderBy('sort')->orderBy('id'),
            ])->findOrFail($id);

        // hero & thumbs
        $hero = $acc->cover_image ? $this->imgUrl($acc->cover_image) : null;
        $thumbs = $acc->images
            ->pluck('image')
            ->filter()
            ->map(fn($p) => $this->imgUrl($p))
            ->unique()
            ->values();
        if (!$hero) { $hero = $thumbs->first() ?? asset('placeholder.png'); }

        // lainnya (general juga)
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

        // banner (tetap)
        $banners = collect();
        foreach (['Apparel', 'Apparels', 'Home'] as $tplName) {
            $b = Banner::where('status', 'active')
                ->whereHas('bannerTemplate', fn($q) => $q->where('name', $tplName))
                ->orderBy('order')
                ->get();
            if ($b->isNotEmpty()) { $banners = $b; break; }
        }

        // kategori & tabs
        $apparelCategories = ApparelCategory::orderBy('id')->get();
        $tabs = $apparelCategories->map(fn($c) => ['key'=>(string)$c->id, 'name'=>$c->name])->values()->all();
        $defaultKey = $apparelCategories->isNotEmpty() ? (string)$apparelCategories->first()->id : null;
        $activeKey  = $key ?: $defaultKey;

        // list apparel per kategori
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
                    $a->display_price = $a->price ?? 0;
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

        // HERO = cover → legacy image → first gallery → placeholder
        $hero = null;
        if ($apparel->cover_image) {
            $hero = $this->imgUrl($apparel->cover_image);
        } elseif ($apparel->image) {
            $hero = $this->imgUrl($apparel->image);
        }

        // THUMBS = semua gambar gallery (tanpa mengulang cover)
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

        $sizes     = $this->splitList($apparel->size ?? '');
        $materials = $this->splitList($apparel->material ?? '', '/\r\n|\r|\n/u');

        // Apparel lain
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

    // === PARTS (LIST) — tanpa General Item & tanpa halaman detail parts ===
public function parts(\Illuminate\Http\Request $request)
{
    $key = $request->query('key');

    // Banner: coba template 'Parts' → fallback 'Home'
    $banners = collect();
    foreach (['Parts', 'Part', 'Home'] as $tplName) {
        $b = \App\Models\Banner::where('status', 'active')
            ->whereHas('bannerTemplate', fn($q) => $q->where('name', $tplName))
            ->orderBy('order')
            ->get();
        if ($b->isNotEmpty()) { $banners = $b; break; }
    }

    // Ambil kategori, drop "big bike" bila ada
    $categories = \App\Models\Category::all()
        ->filter(fn($c) => strtolower(trim($c->name)) !== 'big bike')
        ->values();

    // Tabs = kategori saja
    $tabs = $categories->map(fn($c) => [
        'key'  => (string) $c->id,
        'name' => $c->name,
    ])->values()->all();

    // default tab = kategori pertama
    $defaultKey = $categories->isNotEmpty() ? (string) $categories->first()->id : null;
    $activeKey  = $key ?: $defaultKey;

    // Ambil motor published per kategori terpilih
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
            // case-insensitive
            $kwLower = mb_strtolower($qText, 'UTF-8');
            $kwLike  = '%' . strtr($kwLower, ['%'=>'\%','_'=>'\_','\\'=>'\\\\']) . '%';

            // subsequence: "cia" => "%c%i%a%"
            $chars   = preg_split('//u', preg_replace('/\s+/u', '', $kwLower), -1, PREG_SPLIT_NO_EMPTY);
            $seqLike = '%' . implode('%', array_map(
                fn($c) => strtr($c, ['%'=>'\%','_'=>'\_','\\'=>'\\\\']),
            $chars)) . '%';

            $q->where(function ($w) use ($kwLike, $seqLike) {
                // HANYA name + city.name
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
        // ambil ?category=..., kalau kosong pakai kategori pertama
        $activeCatId = (int) $request->query('category', 0);
        if ($activeCatId === 0 && $categories->isNotEmpty()) {
            $activeCatId = (int) $categories->first()->id;
        }

        $rows = \App\Models\PriceList::query()
            ->leftJoin('motors', 'motors.name', '=', 'price_lists.motorcycle_name')
            ->where('motors.status', 'published') // hanya motor published
            ->when($activeCatId > 0, fn($q) => $q->where('motors.category_id', $activeCatId))
            ->orderBy('price_lists.motorcycle_name')
            ->orderBy('price_lists.motor_type')
            ->get([
                'price_lists.id','price_lists.motorcycle_name','price_lists.motor_type','price_lists.price',
                'motors.id as motor_id','motors.category_id as cat_id',
            ]);

        $groups = $rows->groupBy('motorcycle_name')->map(function ($items) {
            $f = $items->first();
            return (object)[
                'motor_name' => $f->motorcycle_name,
                'motor_id'   => $f->motor_id,
                'types'      => $items->map(fn($r)=>(object)['type'=>$r->motor_type,'price'=>(float)$r->price])->values(),
            ];
        })->values();

        return view('pages.public.priceList', compact('categories','activeCatId','groups'));
    }

    // --- SIMULASI KREDIT ---
    public function creditSimulator(Request $request)
    {
        $motorId    = (int) $request->query('motor_id'); // opsional dari tombol di detail
        $categories = Category::orderBy('id', 'asc')->get(['id','name']);
        // hanya motor published
        $motors     = Motor::where('status', 'published')
                        ->orderBy('name')
                        ->get(['id','name','category_id','thumbnail']);

        // seleksi awal
        $selectedMotor    = $motorId ? $motors->firstWhere('id', $motorId) : null;
        $selectedCategory = $selectedMotor->category_id ?? ($categories->first()->id ?? null);

        // varian (PriceList by nama motor)
        $variants = collect();
        $otr      = 0.0;
        if ($selectedMotor) {
            $variants = PriceList::where('motorcycle_name', $selectedMotor->name)
                        ->orderBy('motor_type')
                        ->get(['motor_type','price']);
            $otr = (float) optional($variants->first())->price ?: 0.0;
        }

        // default aturan simulasi
        $defaults = (object)[
            'min_dp_percent' => 10,
            'interest_year'  => 10.0,
            'tenor_options'  => [11,17,23,29,35],
        ];

        // dataset untuk JS
        $priceList = PriceList::orderBy('motorcycle_name')
                    ->get(['motorcycle_name','motor_type','price'])
                    ->map(fn($p)=>[
                        'motorcycle_name' => $p->motorcycle_name,
                        'motor_type'      => $p->motor_type,
                        'price'           => (float)$p->price,
                    ])->values();

        $dataset = [
            'motors' => $motors->map(function($m){
                return [
                    'id'          => $m->id,
                    'name'        => $m->name,
                    'category_id' => $m->category_id,
                    'thumb'       => $m->thumbnail
                                        ? asset('storage/'.$m->thumbnail)
                                        : asset('placeholder.png'),
                ];
            })->values(),
            'priceList' => $priceList,
        ];

        return view('pages.public.creditSimulator', [
            'categories'       => $categories,
            'motors'           => $motors,
            'selectedCategory' => $selectedCategory,
            'selectedMotor'    => $selectedMotor,
            'variants'         => $variants,
            'initialOTR'       => $otr,
            'defaults'         => $defaults,
            'dataset'          => $dataset,
        ]);
    }
}