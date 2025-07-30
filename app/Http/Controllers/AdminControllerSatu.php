<?php

namespace App\Http\Controllers;

use App\Models\Motor;
use App\Models\Banner;
use App\Models\Branch;
use App\Models\Apparel;
use App\Models\Position;
use App\Models\TestRide;
use App\Models\MotorPart;
use App\Models\BranchSatu;
use App\Models\MotorColor;
use Illuminate\Support\Str;
use App\Models\MotorFeature;
use App\Models\PartCategory;
use Illuminate\Http\Request;
use App\Models\BranchService;
use App\Models\MotorCategory;
use App\Models\BannerTemplate;
use App\Models\MotorAccessory;
use App\Models\ApparelCategory;
use App\Models\CreditSimulation;
use App\Models\AccessoryCategory;
use App\Models\MotorSpecification;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;


class AdminControllerSatu extends Controller
{

    // --- MOTOR ---
    public function adminMotor()
    {
        return view('pages.admin.motorDataTables'); // ganti sesuai struktur blade kamu
    }

    public function getMotorData()
    {
        $data = Motor::select('motors.*');

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('price', function ($row) {
                return 'Rp' . number_format($row->price, 0, ',', '.');
            })
            ->addColumn('category', function ($row) {
                return $row->category ?? '-';
            })
            ->addColumn('action', function ($row) {
                return '
                    <button class="btn btn-sm btn-primary editBtn" 
                            data-id="' . $row->id . '" 
                            data-name="' . $row->name . '" 
                            data-category="' . $row->category . '" 
                            data-price="' . $row->price . '" 
                            data-color="' . $row->color . '">
                        <i class="fas fa-pen"></i>
                    </button>

                    <button class="btn btn-sm btn-danger deleteBtn" 
                            data-id="' . $row->id . '" 
                            data-name="' . $row->name . '">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function motorsIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = Motor::with('category')->latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('category', function ($row) {
                    return $row->category->name ?? '-';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route("admin.motors.edit", $row->id) . '" class="btn btn-sm btn-primary">Edit</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.motors.index');
    }

    public function motorsCreate()
    {
        $categories = MotorCategory::all();
        return view('admin.motors.create', compact('categories'));
    }

    public function motorsStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:motor_categories,id',
        ]);

        Motor::create($data);
        return back()->with('success', 'Motor created.');
    }

    public function motorsEdit($id)
    {
        $motor = Motor::findOrFail($id);
        $categories = MotorCategory::all();
        return view('admin.motors.edit', compact('motor', 'categories'));
    }

    public function updateMotor(Request $request)
    {
        // Validasi input
        $request->validate([
            'user_id' => 'required|exists:motors,id',
            'name' => 'required|string',
            'category' => 'required|string',
            'price' => 'required|numeric',
            'color' => 'required|string',
        ]);

        // Update data
        $motor = Motor::findOrFail($request->user_id);
        $motor->name = $request->name;
        $motor->category = $request->category;
        $motor->price = $request->price;
        $motor->color = $request->color;
        $motor->save();

        return redirect()->back()->with('success', 'Data motor berhasil diperbarui.');
    }

    public function deleteMotor(Request $request)
    {
        $motorId = $request->input('motor_id');
        Motor::findOrFail($motorId)->delete();

        return back()->with('success', 'Motor berhasil dihapus.');
    }

    public function motorCategoryIndex()
    {
        return view('pages.admin.manageMotorCategories');
    }

    public function getMotorCategories(Request $request)
    {
        if ($request->ajax()) {
            $data = MotorCategory::select(['id', 'name']);
            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('nama_kategori', function ($row) {
                    return $row->name;
                })
                ->addColumn('action', function ($row) {
                    return '
                    <button class="btn btn-sm btn-primary editBtn" data-id="' . $row->id . '" data-name="' . $row->name . '">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button class="btn btn-sm btn-danger deleteBtn" data-id="' . $row->id . '" data-name="' . $row->name . '">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                ';
                })
                ->rawColumns(['action']) // biar tombol HTML-nya dirender
                ->make(true);
        }
    }

    // Simpan kategori baru
    public function storeMotorCategory(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string|max:255',
        ]);

        MotorCategory::create([
            'name' => $request->kategori,
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan');
    }

    // Update kategori
    public function updateMotorCategory(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:motor_categories,id',
            'nama_kategori' => 'required|string|max:255',
        ]);

        $category = MotorCategory::findOrFail($request->id);
        $category->name = $request->nama_kategori;
        $category->save();

        return response()->json(['message' => 'Kategori berhasil diperbarui']);
    }

    // Hapus kategori
    public function deleteMotorCategory(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:motor_categories,id',
        ]);

        $category = MotorCategory::findOrFail($request->id);
        $category->delete();

        return redirect()->back()->with('success', 'Kategori berhasil dihapus');
    }

    public function accessoriesCategoryIndex()
    {
        return view('pages.admin.manageAccessoriesCategories');
    }

    public function getAccessoriesCategories(Request $request)
    {
        if ($request->ajax()) {
            $data = AccessoryCategory::select(['id', 'name']);
            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('nama_kategori', function ($row) {
                    return $row->name;
                })
                ->addColumn('action', function ($row) {
                    return '
                    <button class="btn btn-sm btn-primary editBtn" data-id="' . $row->id . '" data-name="' . $row->name . '">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button class="btn btn-sm btn-danger deleteBtn" data-id="' . $row->id . '" data-name="' . $row->name . '">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                ';
                })
                ->rawColumns(['action']) // biar tombol HTML-nya dirender
                ->make(true);
        }
    }

    // Simpan kategori baru
    public function storeAccessoriesCategory(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string|max:255',
        ]);

        AccessoryCategory::create([
            'name' => $request->kategori,
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan');
    }

    // Update kategori
    public function updateAccessoriesCategory(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:accessories_categories,id',
            'nama_kategori' => 'required|string|max:255',
        ]);

        $category = AccessoryCategory::findOrFail($request->id);
        $category->name = $request->nama_kategori;
        $category->save();

        return response()->json(['message' => 'Kategori berhasil diperbarui']);
    }

    // Hapus kategori
    public function deleteAccessoriesCategory(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:accessories_categories,id',
        ]);

        $category = AccessoryCategory::findOrFail($request->id);
        $category->delete();

        return redirect()->back()->with('success', 'Kategori berhasil dihapus');
    }

    public function apparelCategoryIndex()
    {
        return view('pages.admin.manageApparelCategories');
    }

    public function getApparelCategories(Request $request)
    {
        if ($request->ajax()) {
            $data = ApparelCategory::select(['id', 'name']);
            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('nama_kategori', function ($row) {
                    return $row->name;
                })
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-sm btn-primary editBtn" data-id="' . $row->id . '" data-name="' . $row->name . '">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="' . $row->id . '" data-name="' . $row->name . '">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['action']) // biar tombol HTML-nya dirender
                ->make(true);
        }
    }

    // Simpan kategori baru
    public function storeApparelCategory(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string|max:255',
        ]);

        ApparelCategory::create([
            'name' => $request->kategori,
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan');
    }

    // Update kategori
    public function updateApparelCategory(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:apparel_categories,id',
            'nama_kategori' => 'required|string|max:255',
        ]);

        $category = ApparelCategory::findOrFail($request->id);
        $category->name = $request->nama_kategori;
        $category->save();

        return response()->json(['message' => 'Kategori berhasil diperbarui']);
    }

    // Hapus kategori
    public function deleteApparelCategory(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:apparel_categories,id',
        ]);

        $category = ApparelCategory::findOrFail($request->id);
        $category->delete();

        return redirect()->back()->with('success', 'Kategori berhasil dihapus');
    }

    public function partsCategoryIndex()
    {
        return view('pages.admin.managePartsCategories');
    }

    public function getPartsCategories(Request $request)
    {
        if ($request->ajax()) {
            $data = PartCategory::select(['id', 'name']);
            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('nama_kategori', function ($row) {
                    return $row->name;
                })
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-sm btn-primary editBtn" data-id="' . $row->id . '" data-name="' . $row->name . '">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="' . $row->id . '" data-name="' . $row->name . '">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    // Simpan kategori baru
    public function storePartsCategory(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string|max:255',
        ]);

        ApparelCategory::create([
            'name' => $request->kategori,
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan');
    }

    // Update kategori
    public function updatePartsCategory(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:parts_categories,id',
            'nama_kategori' => 'required|string|max:255',
        ]);

        $category = PartCategory::findOrFail($request->id);
        $category->name = $request->nama_kategori;
        $category->save();

        return response()->json(['message' => 'Kategori berhasil diperbarui']);
    }

    // Hapus kategori
    public function deletePartsCategory(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:parts_categories,id',
        ]);

        $category = PartCategory::findOrFail($request->id);
        $category->delete();

        return redirect()->back()->with('success', 'Kategori berhasil dihapus');
    }

    // --- MOTOR FEATURE ---
    public function featuresStore(Request $request)
    {
        $data = $request->validate([
            'motor_id' => 'required|exists:motors,id',
            'title' => 'required',
            'description' => 'nullable',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'position_x' => 'nullable|numeric',
            'position_y' => 'nullable|numeric',
        ]);

        $data['image_url'] = $this->uploadImage($request, 'image', 'features');
        MotorFeature::create($data);
        return back()->with('success', 'Feature added.');
    }

    public function featuresEdit($id)
    {
        $feature = MotorFeature::findOrFail($id);
        $motors = Motor::all();
        return view('admin.features.edit', compact('feature', 'motors'));
    }

    public function featuresUpdate(Request $request, $id)
    {
        $feature = MotorFeature::findOrFail($id);
        $data = $request->validate([
            'motor_id' => 'required|exists:motors,id',
            'title' => 'required',
            'description' => 'nullable',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'position_x' => 'nullable|numeric',
            'position_y' => 'nullable|numeric',
        ]);

        if ($request->hasFile('image')) {
            $data['image_url'] = $this->uploadImage($request, 'image', 'features');
        }

        $feature->update($data);
        return back()->with('success', 'Feature updated.');
    }

    public function featuresDelete($id)
    {
        MotorFeature::findOrFail($id)->delete();
        return back()->with('success', 'Feature deleted.');
    }

    // --- MOTOR COLOR ---
    public function colorsStore(Request $request)
    {
        $data = $request->validate([
            'motor_id' => 'required|exists:motors,id',
            'color_name' => 'required',
            'hex_color' => 'nullable',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data['image_url'] = $this->uploadImage($request, 'image', 'colors');
        MotorColor::create($data);
        return back()->with('success', 'Color added.');
    }

    public function colorsEdit($id)
    {
        $color = MotorColor::findOrFail($id);
        $motors = Motor::all();
        return view('admin.colors.edit', compact('color', 'motors'));
    }

    public function colorsUpdate(Request $request, $id)
    {
        $color = MotorColor::findOrFail($id);
        $data = $request->validate([
            'motor_id' => 'required|exists:motors,id',
            'color_name' => 'required',
            'hex_color' => 'nullable',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image_url'] = $this->uploadImage($request, 'image', 'colors');
        }

        $color->update($data);

        return back()->with('success', 'Color updated.');
    }

    public function colorsDelete($id)
    {
        MotorColor::findOrFail($id)->delete();
        return back()->with('success', 'Color deleted.');
    }

    // --- MOTOR SPECIFICATION ---
    public function specsStore(Request $request)
    {
        $data = $request->validate([
            'motor_id' => 'required|exists:motors,id',
            'category' => 'required',
            'name' => 'required',
            'value' => 'required',
        ]);

        MotorSpecification::create($data);
        return back()->with('success', 'Specification added.');
    }

    public function specsEdit($id)
    {
        $spec = MotorSpecification::findOrFail($id);
        $motors = Motor::all();
        return view('admin.specs.edit', compact('spec', 'motors'));
    }

    public function specsUpdate(Request $request, $id)
    {
        $spec = MotorSpecification::findOrFail($id);
        $data = $request->validate([
            'motor_id' => 'required|exists:motors,id',
            'category' => 'required',
            'name' => 'required',
            'value' => 'required',
        ]);

        $spec->update($data);
        return back()->with('success', 'Specification updated.');
    }

    public function specsDelete($id)
    {
        MotorSpecification::findOrFail($id)->delete();
        return back()->with('success', 'Specification deleted.');
    }

    // --- MOTOR ACCESSORY ---
    public function accessoriesStore(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:accessory_categories,id',
            'name' => 'required',
            'function' => 'nullable',
            'color' => 'nullable',
            'material' => 'nullable',
            'part_number' => 'nullable',
            'price' => 'nullable|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'description' => 'nullable',
        ]);

        $data['image_url'] = $this->uploadImage($request, 'image', 'accessories');
        MotorAccessory::create($data);
        return back()->with('success', 'Accessory added.');
    }

    public function accessoriesEdit($id)
    {
        $accessory = MotorAccessory::findOrFail($id);
        $categories = AccessoryCategory::all();
        return view('admin.accessories.edit', compact('accessory', 'categories'));
    }

    public function accessoriesUpdate(Request $request, $id)
    {
        $accessory = MotorAccessory::findOrFail($id);
        $data = $request->validate([
            'category_id' => 'required|exists:accessory_categories,id',
            'name' => 'required',
            'function' => 'nullable',
            'color' => 'nullable',
            'material' => 'nullable',
            'part_number' => 'nullable',
            'price' => 'nullable|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'description' => 'nullable',
        ]);

        if ($request->hasFile('image')) {
            $data['image_url'] = $this->uploadImage($request, 'image', 'accessories');
        }

        $accessory->update($data);
        return back()->with('success', 'Accessory updated.');
    }

    public function accessoriesDelete($id)
    {
        MotorAccessory::findOrFail($id)->delete();
        return back()->with('success', 'Accessory deleted.');
    }

    // --- MOTOR PART ---
    public function partsStore(Request $request)
    {
        $data = $request->validate([
            'motor_id' => 'required|exists:motors,id',
            'name' => 'required',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'compatibility_notes' => 'nullable',
        ]);

        $data['image_url'] = $this->uploadImage($request, 'image', 'parts');
        MotorPart::create($data);
        return back()->with('success', 'Part added.');
    }

    public function partsEdit($id)
    {
        $part = MotorPart::findOrFail($id);
        $motors = Motor::all();
        return view('admin.parts.edit', compact('part', 'motors'));
    }

    public function partsUpdate(Request $request, $id)
    {
        $part = MotorPart::findOrFail($id);
        $data = $request->validate([
            'motor_id' => 'required|exists:motors,id',
            'name' => 'required',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'compatibility_notes' => 'nullable',
        ]);

        if ($request->hasFile('image')) {
            $data['image_url'] = $this->uploadImage($request, 'image', 'parts');
        }

        $part->update($data);
        return back()->with('success', 'Part updated.');
    }

    public function partsDelete($id)
    {
        MotorPart::findOrFail($id)->delete();
        return back()->with('success', 'Part deleted.');
    }

    // --- APPAREL ---
    public function apparelsStore(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:apparel_categories,id',
            'name' => 'required',
            'price' => 'required|numeric',
            'size' => 'nullable',
            'color' => 'nullable',
            'material' => 'nullable',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data['image_url'] = $this->uploadImage($request, 'image', 'apparels');
        Apparel::create($data);
        return back()->with('success', 'Apparel added.');
    }

    public function apparelsEdit($id)
    {
        $apparel = Apparel::findOrFail($id);
        $categories = ApparelCategory::all();
        return view('admin.apparels.edit', compact('apparel', 'categories'));
    }

    public function apparelsUpdate(Request $request, $id)
    {
        $apparel = Apparel::findOrFail($id);
        $data = $request->validate([
            'category_id' => 'required|exists:apparel_categories,id',
            'name' => 'required',
            'price' => 'required|numeric',
            'size' => 'nullable',
            'color' => 'nullable',
            'material' => 'nullable',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image_url'] = $this->uploadImage($request, 'image', 'apparels');
        }

        $apparel->update($data);

        return back()->with('success', 'Apparel updated.');
    }

    public function apparelsDelete($id)
    {
        Apparel::findOrFail($id)->delete();
        return back()->with('success', 'Apparel deleted.');
    }

    // --- BRANCH ---
    public function branchesStore(Request $request)
    {
        $data = $request->validate([
            'area' => 'required',
            'name' => 'required',
            'city' => 'required',
            'address' => 'required',
            'phone' => 'required',
        ]);

        $branch = Branch::create($data);

        if ($request->has('services')) {
            foreach ($request->services as $service) {
                $branch->services()->create(['service_type' => $service]);
            }
        }

        return back()->with('success', 'Branch added.');
    }

    public function branchesEdit($id)
    {
        $branch = Branch::with('services')->findOrFail($id);
        return view('admin.branches.edit', compact('branch'));
    }

    public function branchesUpdate(Request $request, $id)
    {
        $branch = Branch::findOrFail($id);
        $data = $request->validate([
            'area' => 'required',
            'name' => 'required',
            'city' => 'required',
            'address' => 'required',
            'phone' => 'required',
        ]);

        $branch->update($data);

        $branch->services()->delete();
        if ($request->has('services')) {
            foreach ($request->services as $service) {
                $branch->services()->create(['service_type' => $service]);
            }
        }

        return back()->with('success', 'Branch updated.');
    }

    public function branchesDelete($id)
    {
        $branch = Branch::findOrFail($id);
        $branch->services()->delete();
        $branch->delete();
        return back()->with('success', 'Branch deleted.');
    }


    // === BANNER ===
    /*------------------------------------------
    --------------------------------------------
    Banner Template Management
    --------------------------------------------*/

    // Halaman utama banner
    public function adminbanner()
    {
        return view('pages.admin.managebanner');
    }

    // Get data banner templates untuk display dan AJAX
    public function manageBannerTemplate(Request $request, $id = null)
    {
        if ($id) {
            // Get specific template for edit
            $template = BannerTemplate::findOrFail($id);
            return response()->json($template);
        }

        // Get all templates with banners count
        $templates = BannerTemplate::withCount('banners')->orderBy('name', 'asc')->get();

        if ($request->ajax()) {
            return response()->json($templates);
        }

        return $templates;
    }

    // Store template baru (HANYA TEMPLATE, BUKAN BANNER)
    public function storeBannerTemplate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:banner_templates,name',
        ]);

        try {
            $template = BannerTemplate::create([
                'name' => $request->name,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Template berhasil ditambahkan!',
                    'data' => $template
                ]);
            }

            return redirect()->back()->with('success', 'Template berhasil ditambahkan!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan template: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal menambahkan template: ' . $e->getMessage());
        }
    }

    // Edit template name
    public function editBannerTemplate(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:banner_templates,id',
            'name' => 'required|string|max:255',
        ]);

        try {
            $template = BannerTemplate::findOrFail($request->template_id);

            // Check if name is unique (except current template)
            $existingTemplate = BannerTemplate::where('name', $request->name)
                ->where('id', '!=', $template->id)
                ->first();

            if ($existingTemplate) {
                return redirect()->back()->with('error', 'Nama template sudah digunakan.');
            }

            $template->update([
                'name' => $request->name,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Template berhasil diperbarui!',
                    'data' => $template
                ]);
            }

            return redirect()->back()->with('success', 'Template berhasil diperbarui!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui template: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal memperbarui template: ' . $e->getMessage());
        }
    }

    // Hapus template
    public function deleteBannerTemplate(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:banner_templates,id'
        ]);

        DB::beginTransaction();
        try {
            $template = BannerTemplate::findOrFail($request->id);

            // Check if template has banners
            if ($template->banners()->count() > 0) {
                return redirect()->back()->with('error', 'Template tidak dapat dihapus karena masih memiliki banner.');
            }

            $template->delete();

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Template berhasil dihapus!'
                ]);
            }

            return redirect()->back()->with('success', 'Template berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus template: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal menghapus template: ' . $e->getMessage());
        }
    }

    /*------------------------------------------
    --------------------------------------------
    Banner CRUD Management
    --------------------------------------------*/

    // Store banner baru
    public function storeBanner(Request $request)
    {
        $request->validate([
            'banner_template_id' => 'required|exists:banner_templates,id',
            'title' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB
            'status' => 'required|in:active,inactive',
            // Hapus validasi order
        ]);

        DB::beginTransaction();
        try {
            $imagePath = null;

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('public/banners', $imageName);
                $imagePath = '/storage/banners/' . $imageName;
            }

            // Get next order number untuk template ini
            $nextOrder = Banner::where('banner_template_id', $request->banner_template_id)->max('order') + 1;

            $banner = Banner::create([
                'banner_template_id' => $request->banner_template_id,
                'title' => $request->title,
                'image_path' => $imagePath,
                'status' => $request->status,
                'order' => $nextOrder, // Auto increment order
            ]);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Banner berhasil ditambahkan!',
                    'data' => $banner
                ]);
            }

            return redirect()->back()->with('success', 'Banner berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan banner: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal menambahkan banner: ' . $e->getMessage());
        }
    }

    // Edit banner
    public function editBanner(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:banners,id',
            'banner_template_id' => 'required|exists:banner_templates,id',
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'status' => 'required|in:active,inactive',
            // Hapus validasi order
        ]);

        DB::beginTransaction();
        try {
            $banner = Banner::findOrFail($request->id);

            $updateData = [
                'banner_template_id' => $request->banner_template_id,
                'title' => $request->title,
                'status' => $request->status,
                // Hapus order dari update data
            ];

            // Handle image upload if new image provided
            if ($request->hasFile('image')) {
                // Delete old image
                if ($banner->image_path) {
                    $oldImagePath = str_replace('/storage/', 'public/', $banner->image_path);
                    if (Storage::exists($oldImagePath)) {
                        Storage::delete($oldImagePath);
                    }
                }

                // Upload new image
                $image = $request->file('image');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('public/banners', $imageName);
                $updateData['image_path'] = '/storage/banners/' . $imageName;
            }

            $banner->update($updateData);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Banner berhasil diperbarui!',
                    'data' => $banner
                ]);
            }

            return redirect()->back()->with('success', 'Banner berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui banner: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal memperbarui banner: ' . $e->getMessage());
        }
    }

    // Delete banner
    public function deleteBanner(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:banners,id'
        ]);

        DB::beginTransaction();
        try {
            $banner = Banner::findOrFail($request->id);
            $templateId = $banner->banner_template_id;
            $deletedOrder = $banner->order;

            // Delete image file
            if ($banner->image_path) {
                $imagePath = str_replace('/storage/', 'public/', $banner->image_path);
                if (Storage::exists($imagePath)) {
                    Storage::delete($imagePath);
                }
            }

            $banner->delete();

            // Reorder banners setelah yang dihapus
            Banner::where('banner_template_id', $templateId)
                ->where('order', '>', $deletedOrder)
                ->decrement('order');

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Banner berhasil dihapus!'
                ]);
            }

            return redirect()->back()->with('success', 'Banner berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus banner: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal menghapus banner: ' . $e->getMessage());
        }
    }
    public function updateBannerOrder(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:banners,id',
            'order' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $banner = Banner::findOrFail($request->id);
            $templateId = $banner->banner_template_id;
            $currentOrder = $banner->order;
            $newOrder = $request->order;

            // Jika order sama, tidak perlu update
            if ($currentOrder == $newOrder) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order tidak berubah'
                ]);
            }

            // Get max order untuk template ini
            $maxOrder = Banner::where('banner_template_id', $templateId)->max('order');

            // Jika new order lebih besar dari max order, set ke max order + 1
            if ($newOrder > $maxOrder) {
                $newOrder = $maxOrder;
            }

            if ($currentOrder < $newOrder) {
                // Moving down: geser yang di antara current dan new order ke atas
                Banner::where('banner_template_id', $templateId)
                    ->where('order', '>', $currentOrder)
                    ->where('order', '<=', $newOrder)
                    ->decrement('order');
            } else {
                // Moving up: geser yang di antara new dan current order ke bawah
                Banner::where('banner_template_id', $templateId)
                    ->where('order', '>=', $newOrder)
                    ->where('order', '<', $currentOrder)
                    ->increment('order');
            }

            // Update banner yang dipindah
            $banner->update(['order' => $newOrder]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Urutan berhasil diubah!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah urutan: ' . $e->getMessage()
            ], 500);
        }
    }
    /*------------------------------------------
    --------------------------------------------
    Banner Data Table Management
    --------------------------------------------*/
    public function manageBanner(Request $request, $templateId = null)
    {
        if ($request->ajax()) {
            $query = Banner::with('bannerTemplate')
                ->select(['banners.*'])
                ->join('banner_templates', 'banners.banner_template_id', '=', 'banner_templates.id')
                ->addSelect('banner_templates.name as template_name');

            // Filter by template if specified and not 'all'
            if ($templateId && $templateId !== 'all') {
                $query->where('banners.banner_template_id', $templateId);
            }

            $query->orderBy('banner_templates.name')
                ->orderBy('banners.order');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('template_name', function ($row) {
                    return $row->template_name;
                })
                ->make(true);
        }
    }

    // Helper untuk upload gambar (opsional, sudah ada di kode sebelumnya)
    private function uploadImage($request, $fieldName, $folder)
    {
        if ($request->hasFile($fieldName)) {
            return $request->file($fieldName)->store($folder, 'public');
        }
        return null;
    }

    // --- TEST RIDE ---
    public function testRidesIndex()
    {
        $rides = TestRide::latest()->paginate(10);
        return view('admin.test_rides.index', compact('rides'));
    }

    public function testRidesShow($id)
    {
        $ride = TestRide::findOrFail($id);
        return view('admin.test_rides.show', compact('ride'));
    }

    public function testRidesDelete($id)
    {
        TestRide::findOrFail($id)->delete();
        return back()->with('success', 'Test Ride entry deleted.');
    }

    // --- SIMULASI KREDIT ---
    public function creditsIndex()
    {
        $simulations = CreditSimulation::latest()->paginate(10);
        return view('admin.credits.index', compact('simulations'));
    }

    public function creditsShow($id)
    {
        $simulation = CreditSimulation::findOrFail($id);
        return view('admin.credits.show', compact('simulation'));
    }

    public function creditsDelete($id)
    {
        CreditSimulation::findOrFail($id)->delete();
        return back()->with('success', 'Credit Simulation entry deleted.');
    }
}
