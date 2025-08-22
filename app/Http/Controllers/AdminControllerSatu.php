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
use App\Models\BranchLocation;
use App\Models\MotorColor;
use Illuminate\Support\Str;
use App\Models\MotorFeature;
use App\Models\PartCategory;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\MotorType;
use App\Models\BannerTemplate;
use App\Models\MotorAccessory;
use App\Models\ApparelCategory;
use App\Models\CreditSimulation;
use App\Models\PriceList;
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

    /* =========================
       KELOLA MOTOR
    ========================== */

    public function motorsIndex(Request $request)
    {
        $publishedCount = Motor::where('status', 'published')->count();
        $unpublishedCount = Motor::where('status', 'unpublished')->count();

        return view('pages.admin.motorDataTables', [
            'publishedCount' => $publishedCount,
            'unpublishedCount' => $unpublishedCount,
            'categories' => Category::all(),
            'types' => MotorType::all(),
        ]);
    }

    public function motorsPublished(Request $request)
    {
        if ($request->ajax()) {
            $data = Motor::where('status', 'published')->with('category', 'type');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('product', function ($row) {
                    $html = '
                        <div style="text-align:center">
                            <img src="' . asset('storage/' . $row->thumbnail) . '" 
                                alt="' . e($row->name) . '" 
                                style="max-width:150px; margin-bottom:10px; display:block; margin-left:auto; margin-right:auto; cursor:pointer;"
                                class="image-preview" 
                                data-image="' . asset('storage/' . $row->thumbnail) . '" 
                                data-title="' . e($row->name) . '">
                            <div style="font-weight:bold; margin-bottom:10px; font-size:16px;">' . e($row->name) . '</div>
                        </div>
                        <table style="width:100%; border: 1px solid black; border-collapse:collapse; margin-bottom:10px; font-size:14px; text-align:center;">
                            <thead style="background:#f8f9fa;">
                                <tr>
                                    <th style="padding:8px; border: 1px solid black;">Motor Code</th>
                                    <th style="padding:8px; border: 1px solid black;">WMS Code</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="padding:8px; border: 1px solid black;">' . e($row->motor_code_otr) . '</td>
                                    <td style="padding:8px; border: 1px solid black;">' . e($row->wms_code) . '</td>
                                </tr>
                            </tbody>
                        </table>
                        <table style="width:100%; border: 1px solid black; border-collapse:collapse; font-size:14px; text-align:center;">
                            <thead style="background:#f8f9fa;">
                                <tr>
                                    <th style="padding:8px; border: 1px solid black;">Aksesoris</th>
                                    <th style="padding:8px; border: 1px solid black;">Warna</th>
                                    <th style="padding:8px; border: 1px solid black;">Spesifikasi</th>
                                    <th style="padding:8px; border: 1px solid black;">Fitur</th>
                                    <th style="padding:8px; border: 1px solid black;">Sparepart</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="padding:8px; border: 1px solid black;"><a href="' . route('admin.accessories.index', $row->id) . '" title="Aksesoris"><i class="fas fa-cogs"></i></a></td>
                                    <td style="padding:8px; border: 1px solid black;"><a href="' . route('admin.colors.index', $row->id) . '" title="Warna"><i class="fas fa-tint"></i></a></td>
                                    <td style="padding:8px; border: 1px solid black;"><a href="' . route('admin.specifications.index', $row->id) . '" title="Spesifikasi"><i class="fas fa-list"></i></a></td>
                                    <td style="padding:8px; border: 1px solid black;"><a href="' . route('admin.features.index', $row->id) . '" title="Fitur"><i class="fas fa-star"></i></a></td>
                                    <td style="padding:8px; border: 1px solid black;"><a href="' . route('admin.spareparts.index', $row->id) . '" title="Part"><i class="fas fa-wrench"></i></a></td>
                                </tr>
                            </tbody>
                        </table>
                    ';
                    return $html;
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                        <div class="btn-group">
                            <button class="btn btn-sm btn-primary me-1 editBtn" 
                                data-id="' . $row->id . '" 
                                data-name="' . e($row->name) . '" 
                                data-motor_code_otr="' . e($row->motor_code_otr) . '" 
                                data-motor_code_credit="' . e($row->motor_code_credit) . '" 
                                data-wms_code="' . e($row->wms_code) . '" 
                                data-category_id="' . $row->category_id . '" 
                                data-type_id="' . $row->type_id . '" 
                                data-description="' . e($row->description) . '" 
                                data-status="' . e($row->status) . '" 
                                data-thumbnail="' . ($row->thumbnail ? asset('storage/' . $row->thumbnail) : '') . '" 
                                data-accessory_thumbnail="' . ($row->accessory_thumbnail ? asset('storage/' . $row->accessory_thumbnail) : '') . '">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button class="btn btn-sm btn-danger deleteBtn" 
                                data-id="' . $row->id . '" 
                                data-name="' . e($row->name) . '">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    ';
                    return $btn;
                })
                ->rawColumns(['product', 'action'])
                ->make(true);
        }

        return view('pages.admin.motorPublished', [
            'categories' => Category::all(),
            'types' => MotorType::all(),
        ]);
    }

    public function motorsUnpublished(Request $request)
    {
        if ($request->ajax()) {
            $data = Motor::where('status', 'unpublished')->with('category', 'type');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('product', function ($row) {
                    $html = '
                        <div style="text-align:center">
                            <img src="' . asset('storage/' . $row->thumbnail) . '" 
                                alt="' . e($row->name) . '" 
                                style="max-width:150px; margin-bottom:10px; display:block; margin-left:auto; margin-right:auto; cursor:pointer;"
                                class="image-preview" 
                                data-image="' . asset('storage/' . $row->thumbnail) . '" 
                                data-title="' . e($row->name) . '">
                            <div style="font-weight:bold; margin-bottom:10px; font-size:16px;">' . e($row->name) . '</div>
                        </div>
                        <table style="width:100%; border: 1px solid black; border-collapse:collapse; margin-bottom:10px; font-size:14px; text-align:center;">
                            <thead style="background:#f8f9fa;">
                                <tr>
                                    <th style="padding:8px; border: 1px solid black;">Motor Code</th>
                                    <th style="padding:8px; border: 1px solid black;">WMS Code</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="padding:8px; border: 1px solid black;">' . e($row->motor_code_otr) . '</td>
                                    <td style="padding:8px; border: 1px solid black;">' . e($row->wms_code) . '</td>
                                </tr>
                            </tbody>
                        </table>
                        <table style="width:100%; border: 1px solid black; border-collapse:collapse; font-size:14px; text-align:center;">
                            <thead style="background:#f8f9fa;">
                                <tr>
                                    <th style="padding:8px; border: 1px solid black;">Aksesoris</th>
                                    <th style="padding:8px; border: 1px solid black;">Warna</th>
                                    <th style="padding:8px; border: 1px solid black;">Spesifikasi</th>
                                    <th style="padding:8px; border: 1px solid black;">Fitur</th>
                                    <th style="padding:8px; border: 1px solid black;">Sparepart</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="padding:8px; border: 1px solid black;"><a href="' . route('admin.accessories.index', $row->id) . '" title="Aksesoris"><i class="fas fa-cogs"></i></a></td>
                                    <td style="padding:8px; border: 1px solid black;"><a href="' . route('admin.colors.index', $row->id) . '" title="Warna"><i class="fas fa-tint"></i></a></td>
                                    <td style="padding:8px; border: 1px solid black;"><a href="' . route('admin.specifications.index', $row->id) . '" title="Spesifikasi"><i class="fas fa-list"></i></a></td>
                                    <td style="padding:8px; border: 1px solid black;"><a href="' . route('admin.features.index', $row->id) . '" title="Fitur"><i class="fas fa-star"></i></a></td>
                                    <td style="padding:8px; border: 1px solid black;"><a href="' . route('admin.spareparts.index', $row->id) . '" title="Part"><i class="fas fa-wrench"></i></a></td>
                                </tr>
                            </tbody>
                        </table>
                    ';
                    return $html;
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                        <div class="btn-group">
                            <button class="btn btn-sm btn-primary me-1 editBtn" 
                                data-id="' . $row->id . '" 
                                data-name="' . e($row->name) . '" 
                                data-motor_code_otr="' . e($row->motor_code_otr) . '" 
                                data-motor_code_credit="' . e($row->motor_code_credit) . '" 
                                data-wms_code="' . e($row->wms_code) . '" 
                                data-category_id="' . $row->category_id . '" 
                                data-type_id="' . $row->type_id . '" 
                                data-description="' . e($row->description) . '" 
                                data-status="' . e($row->status) . '" 
                                data-thumbnail="' . ($row->thumbnail ? asset('storage/' . $row->thumbnail) : '') . '" 
                                data-accessory_thumbnail="' . ($row->accessory_thumbnail ? asset('storage/' . $row->accessory_thumbnail) : '') . '">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button class="btn btn-sm btn-danger deleteBtn" 
                                data-id="' . $row->id . '" 
                                data-name="' . e($row->name) . '">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    ';
                    return $btn;
                })
                ->rawColumns(['product', 'action'])
                ->make(true);
        }

        return view('pages.admin.motorUnpublished', [
            'categories' => Category::all(),
            'types' => MotorType::all(),
        ]);
    }

    public function motorsStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'motor_code_otr' => 'nullable|string|max:255',
            'motor_code_credit' => 'nullable|string|max:255',
            'wms_code' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'type_id' => 'required|exists:motor_types,id',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'accessory_thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:published,unpublished',
        ]);

        $data['thumbnail'] = $this->uploadFile($request, 'thumbnail', 'thumbnails');
        $data['accessory_thumbnail'] = $this->uploadFile($request, 'accessory_thumbnail', 'accessory_thumbnails');

        $motor = Motor::create($data);

        if ($motor->status === 'published') {
            return redirect()->route('admin.motors.published')->with('success', 'Motor berhasil ditambahkan.');
        } else {
            return redirect()->route('admin.motors.unpublished')->with('success', 'Motor berhasil ditambahkan.');
        }
    }

    public function updateMotor(Request $request, $id)
    {
        $motor = Motor::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'motor_code_otr' => 'nullable|string|max:255',
            'motor_code_credit' => 'nullable|string|max:255',
            'wms_code' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'type_id' => 'required|exists:motor_types,id',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'accessory_thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:published,unpublished',
        ]);

        $data['thumbnail'] = $this->uploadFile($request, 'thumbnail', 'thumbnails', $motor->thumbnail);
        $data['accessory_thumbnail'] = $this->uploadFile($request, 'accessory_thumbnail', 'accessory_thumbnails', $motor->accessory_thumbnail);

        $motor->update($data);

        if ($motor->status === 'published') {
            return redirect()->route('admin.motors.published')->with('success', 'Motor berhasil diperbarui.');
        } else {
            return redirect()->route('admin.motors.unpublished')->with('success', 'Motor berhasil diperbarui.');
        }
    }

    public function deleteMotor($id)
    {
        $motor = Motor::findOrFail($id);
        $status = $motor->status;

        if ($motor->thumbnail && Storage::exists('public/' . $motor->thumbnail)) {
            Storage::delete('public/' . $motor->thumbnail);
        }

        if ($motor->accessory_thumbnail && Storage::exists('public/' . $motor->accessory_thumbnail)) {
            Storage::delete('public/' . $motor->accessory_thumbnail);
        }

        $motor->delete();

        if ($status === 'published') {
            return redirect()->route('admin.motors.published')->with('success', 'Motor berhasil dihapus.');
        } else {
            return redirect()->route('admin.motors.unpublished')->with('success', 'Motor berhasil dihapus.');
        }
    }

    public function getTypesByCategory($categoryId)
    {
        $types = MotorType::where('category_id', $categoryId)->get();
        return response()->json($types);
    }

    /* =========================
       KELOLA AKSESORIS MOTOR
   ========================== */
    public function accessoriesIndex(Request $request, $motorId)
    {
        $motor = Motor::findOrFail($motorId);

        if ($request->ajax()) {
            $data = MotorAccessory::where('motor_id', $motorId);

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('image', function($row) {
                    return $row->image 
                        ? asset('storage/' . $row->image) 
                        : null;
                })
                ->rawColumns(['image'])
                ->make(true);
        }

        return view('pages.admin.motorAccessories', [
            'motor' => $motor
        ]);
    }

    public function accessoriesStore(Request $request, $motorId)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'part_number' => 'nullable|string|max:255',
            'dimension' => 'nullable|string|max:255',
            'weight' => 'nullable|numeric',
            'description' => 'nullable|string',
        ]);

        $data['motor_id'] = $motorId;
        $data['image'] = $this->uploadFile($request, 'image', 'motor_accessories');

        MotorAccessory::create($data);

        return back()->with('success', 'Aksesoris berhasil ditambahkan.');
    }

    public function accessoriesUpdate(Request $request, $motorId, $id)
    {
        $accessory = MotorAccessory::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'part_number' => 'nullable|string|max:255',
            'dimension' => 'nullable|string|max:255',
            'weight' => 'nullable|numeric',
            'description' => 'nullable|string',
        ]);

        $data['image'] = $this->uploadFile($request, 'image', 'motor_accessories', $accessory->image);

        $accessory->update($data);

        return redirect()->route('admin.accessories.index', $motorId)->with('success', 'Aksesoris berhasil diperbarui.');
    }

    public function accessoriesDelete($motorId, $id)
    {
        $accessory = MotorAccessory::findOrFail($id);

        if ($accessory->image && Storage::exists('public/' . $accessory->image)) {
            Storage::delete('public/' . $accessory->image);
        }

        $accessory->delete();

        return redirect()->route('admin.accessories.index', $motorId)->with('success', 'Aksesoris berhasil dihapus.');
    }

    // ========================
    // KELOLA WARNA MOTOR
    // ========================

    // Tampilkan view dan data JSON untuk DataTables
    public function colorsIndex(Request $request, $motor)
    {
        $motorModel = Motor::findOrFail($motor);

        if ($request->ajax()) {
            $colors = MotorColor::where('motor_id', $motor)->latest();
            return DataTables::of($colors)
                ->addIndexColumn()
                ->editColumn('image', function ($color) {
                    return $color->image ? asset('storage/' . $color->image) : null;
                })
                ->rawColumns(['image'])
                ->make(true);
        }

        return view('pages.admin.motorColor', compact('motorModel'));
    }

    // Simpan data warna baru
    public function colorsStore(Request $request, $motor)
    {
        $data = $request->validate([
            'color_code' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        $data['motor_id'] = $motor;
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadFile($request, 'image', 'colors');
        }

        MotorColor::create($data);

        return redirect()->route('admin.colors.index', $motor)->with('success', 'Warna berhasil ditambahkan.');
    }

    // Update data warna
    public function colorsUpdate(Request $request, $motor, $id)
    {
        $color = MotorColor::findOrFail($id);
        $data = $request->validate([
            'color_code' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        $data['image'] = $this->uploadFile($request, 'image', 'colors', $color->image);

        $color->update($data);

        return redirect()->route('admin.colors.index', $motor)->with('success', 'Warna berhasil diperbarui.');
    }

    // Hapus data warna
    public function colorsDelete($motor, $id)
    {
        $color = MotorColor::findOrFail($id);

        if ($color->image && Storage::disk('public')->exists($color->image)) {
            Storage::disk('public')->delete($color->image);
        }

        $color->delete();

        return redirect()->route('admin.colors.index', $motor)->with('success', 'Warna berhasil dihapus.');
    }

     /* =========================
       KELOLA SPAREPART MOTOR
    ========================== */
    public function sparepartsIndex(Request $request, $motorId)
    {
        $motor = Motor::findOrFail($motorId);

        if ($request->ajax()) {
            $data = MotorPart::where('motor_id', $motorId);

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('image', function($row) {
                    return $row->image 
                        ? asset('storage/' . $row->image) 
                        : null;
                })
                ->rawColumns(['image'])
                ->make(true);
        }

        return view('pages.admin.motorSpareparts', [
            'motor' => $motor
        ]);
    }

    public function sparepartsStore(Request $request, $motorId)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'category' => 'required|in:electric,engine,frame',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'dimension' => 'required|string|max:255',
            'weight' => 'required|numeric',
            'part_number' => 'required|string|max:255',
        ]);

        $data['motor_id'] = $motorId;
        $data['image'] = $this->uploadFile($request, 'image', 'motor_spareparts');

        MotorPart::create($data);

        return back()->with('success', 'Sparepart berhasil ditambahkan.');
    }

    public function sparepartsEdit($motorId, $id)
    {
        $sparepart = MotorPart::findOrFail($id);
        $motor = Motor::findOrFail($motorId);
        return view('pages.admin.motorSpareparts', compact('sparepart', 'motor'));
    }

    public function sparepartsUpdate(Request $request, $motorId, $id)
    {
        $sparepart = MotorPart::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'category' => 'required|in:electric,engine,frame',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'dimension' => 'required|string|max:255',
            'weight' => 'required|numeric',
            'part_number' => 'required|string|max:255',
        ]);

        $data['image'] = $this->uploadFile($request, 'image', 'motor_spareparts', $sparepart->image);

        $sparepart->update($data);

        return redirect()->route('admin.spareparts.index', $motorId)->with('success', 'Sparepart berhasil diperbarui.');
    }

    public function sparepartsDelete($motorId, $id)
    {
        $sparepart = MotorPart::findOrFail($id);

        if ($sparepart->image && Storage::exists('public/' . $sparepart->image)) {
            Storage::delete('public/' . $sparepart->image);
        }

        $sparepart->delete();

        return redirect()->route('admin.spareparts.index', $motorId)->with('success', 'Sparepart berhasil dihapus.');
    }

    /* =========================
       KELOLA SPESIFIKASI
    ========================== */
   public function specificationsIndex(Request $request, $motor)
    {
        try {
            $motor = Motor::findOrFail($motor);

            if ($request->ajax()) {
                $specifications = MotorSpecification::where('motor_id', $motor->id)
                    ->select(['id', 'category', 'atribut', 'detail']);

                return DataTables::of($specifications)
                    ->addIndexColumn()
                    ->addColumn('category', fn($row) => $row->category)
                    ->addColumn('atribut', fn($row) => $row->atribut)
                    ->addColumn('detail', fn($row) => $row->detail)
                    ->addColumn('action', function ($row) use ($motor) {
                        return '
                            <div class="btn-group">
                                <button class="btn btn-sm btn-primary me-1 editBtn" data-id="' . $row->id . '">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button class="btn btn-sm btn-danger deleteBtn" data-id="' . $row->id . '" data-atribut="' . $row->atribut . '">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }

            return view('pages.admin.motorSpecification', compact('motor'));
        } catch (\Exception $e) {
            Log::error('Error in specificationsIndex: ' . $e->getMessage());
            return response()->json(['error' => 'Data tidak ditemukan atau terjadi kesalahan server.'], 500);
        }
    }

    public function specificationsStore(Request $request, $motor)
    {
        try {
            $motor = Motor::findOrFail($motor);

            $validated = $request->validate([
                'category' => 'required|in:Rangka,Mesin,Dimensi,Kelistrikan,Kapasitas',
                'atribut' => 'required|string|max:255',
                'detail' => 'required|string|max:255',
            ]);

            $validated['motor_id'] = $motor->id;
            MotorSpecification::create($validated);

            return response()->json(['success' => 'Spesifikasi berhasil ditambahkan.'], 200);
        } catch (\Exception $e) {
            Log::error('Error in specificationsStore: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menambahkan spesifikasi.'], 500);
        }
    }

    public function specificationsEdit(Request $request, $motor, $id)
    {
        try {
            $motor = Motor::findOrFail($motor);
            $specification = MotorSpecification::where('motor_id', $motor->id)->findOrFail($id);

            return response()->json([
                'id' => $specification->id,
                'category' => $specification->category,
                'atribut' => $specification->atribut,
                'detail' => $specification->detail,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in specificationsEdit: ' . $e->getMessage());
            return response()->json(['error' => 'Data spesifikasi tidak ditemukan.'], 404);
        }
    }

    public function specificationsUpdate(Request $request, $motor, $id)
    {
        try {
            $motor = Motor::findOrFail($motor);
            $specification = MotorSpecification::where('motor_id', $motor->id)->findOrFail($id);

            $validated = $request->validate([
                'category' => 'required|in:Rangka,Mesin,Dimensi,Kelistrikan,Kapasitas',
                'atribut' => 'required|string|max:255',
                'detail' => 'required|string|max:255',
            ]);

            $specification->update($validated);

            return response()->json(['success' => 'Spesifikasi berhasil diubah.'], 200);
        } catch (\Exception $e) {
            Log::error('Error in specificationsUpdate: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal mengubah spesifikasi.'], 500);
        }
    }

    public function specificationsDelete($motor, $id)
    {
        try {
            $motor = Motor::findOrFail($motor);
            $specification = MotorSpecification::where('motor_id', $motor->id)->findOrFail($id);
            $specification->delete();

            return response()->json(['success' => 'Spesifikasi berhasil dihapus.'], 200);
        } catch (\Exception $e) {
            Log::error('Error in specificationsDelete: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menghapus spesifikasi.'], 500);
        }
    }

    /* =========================
       KELOLA FITUR MOTOR
   ========================== */
    public function featuresIndex(Request $request, $motorId)
    {
        $motor = Motor::findOrFail($motorId);

        if ($request->ajax()) {
            $data = MotorFeature::where('motor_id', $motorId);

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('image', function($row) {
                    return $row->image 
                        ? asset('storage/' . $row->image) 
                        : null;
                })
                ->addColumn('position', function($row) {
                    return $row->x_position . ',' . $row->y_position;
                })
                ->rawColumns(['image', 'position'])
                ->make(true);
        }

        return view('pages.admin.motorFeature', [
            'motor' => $motor
        ]);
    }

    public function featuresStore(Request $request, $motorId)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'description' => 'nullable|string',
            'x_position' => 'required|numeric',
            'y_position' => 'required|numeric',
        ]);

        $data['motor_id'] = $motorId;
        $data['image'] = $this->uploadFile($request, 'image', 'motor_features');

        MotorFeature::create($data);

        return back()->with('success', 'Fitur berhasil ditambahkan.');
    }

    public function featuresUpdate(Request $request, $motorId, $id)
    {
        $feature = MotorFeature::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'description' => 'nullable|string',
            'x_position' => 'required|numeric',
            'y_position' => 'required|numeric',
        ]);

        $data['image'] = $this->uploadFile($request, 'image', 'motor_features', $feature->image);

        $feature->update($data);

        return redirect()->route('admin.features.index', $motorId)->with('success', 'Fitur berhasil diperbarui.');
    }

    public function featuresDelete($motorId, $id)
    {
        $feature = MotorFeature::findOrFail($id);

        if ($feature->image && Storage::exists('public/' . $feature->image)) {
            Storage::delete('public/' . $feature->image);
        }

        $feature->delete();

        return redirect()->route('admin.features.index', $motorId)->with('success', 'Fitur berhasil dihapus.');
    }


    /* =========================
       HELPER UPLOAD
    ========================== */
    private function uploadFile($request, $key, $path, $oldFile = null)
    {
        if ($request->hasFile($key)) {
            if ($oldFile && \Storage::disk('public')->exists($oldFile)) {
                \Storage::disk('public')->delete($oldFile);
            }
            return $request->file($key)->store($path, 'public');
        }
        return $oldFile;
    }
    
    // CATEGORY (Motor, aksesoris, part)
    // Index untuk kategori (dengan filter type)
    public function categoryIndex($type = 'motor')
    {
        return view('pages.admin.manageCategories', compact('type'));
    }

    // DataTable untuk kategori berdasarkan type
   public function categoryData(Request $request, $type = 'motor')
    {
        try {
            $data = Category::where('type', $type)->select(['id', 'name', 'type']);
            \Log::info('Category Data for type ' . $type . ': ' . json_encode($data->get()));
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', fn($row) => $row->name)
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-sm btn-primary editBtn" data-id="' . $row->id . '" data-name="' . $row->name . '" data-type="' . $row->type . '">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="' . $row->id . '" data-name="' . $row->name . '">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $e) {
            \Log::error('DataTable Error: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan server'], 500);
        }
    }

    // Simpan kategori baru
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:motor,accessories,parts',
        ]);

        Category::create([
            'name' => $request->name,
            'type' => $request->type,
        ]);

        return response()->json(['success' => true, 'message' => 'Kategori berhasil ditambahkan']);
    }

    // Update kategori
    public function updateCategory(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255', // Ubah dari nama_kategori ke name
            'type' => 'required|in:motor,accessories,parts',
        ]);

        $category = Category::findOrFail($request->id);
        $category->update([
            'name' => $request->name,
            'type' => $request->type,
        ]);

        return response()->json(['success' => true, 'message' => 'Kategori berhasil diperbarui']);
    }

    // Hapus kategori
    public function deleteCategory(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:categories,id',
        ]);

        $category = Category::findOrFail($request->id);
        $category->delete();

        return response()->json(['success' => true, 'message' => 'Kategori berhasil dihapus']);
    }

    // MOTOR TYPE
    public function motorTypeIndex()
    {
        $categories = \App\Models\Category::all();
        return view('pages.admin.manageMotorType', compact('categories'));
    }

    public function getMotorType(Request $request)
    {
        if ($request->ajax()) {
            $data = MotorType::with('category')->select(['id', 'name', 'category_id']);

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('tipe', function ($row) {
                    return $row->category ? $row->category->name : '-';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-sm btn-primary editBtn"
                            data-id="' . $row->id . '"
                            data-name="' . htmlspecialchars($row->name) . '"
                            data-category_id="' . $row->category_id . '">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button class="btn btn-sm btn-danger deleteBtn"
                            data-id="' . $row->id . '"
                            data-name="' . htmlspecialchars($row->name) . '">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return response()->json(['error' => 'Not Ajax'], 400);
    }

    public function storeMotorType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        MotorType::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
        ]);

        return redirect()->back()->with('success', 'Tipe motor berhasil ditambahkan');
    }

    public function updateMotorType(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:motor_types,id',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        $motorType = MotorType::findOrFail($request->id);
        $motorType->name = $request->name;
        $motorType->category_id = $request->category_id;
        $motorType->save();

        return response()->json(['message' => 'Tipe motor berhasil diperbarui']);
    }

    public function deleteMotorType(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:motor_types,id',
        ]);

        $motorType = MotorType::findOrFail($request->id);
        $motorType->delete();

        return redirect()->back()->with('success', 'Tipe motor berhasil dihapus');
    }


    // APPAREL CATEGORY
    public function apparelCategoryIndex()
    {
        return view('pages.admin.apparelCategories');
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

        return redirect()->back()->with('success', 'Kategori berhasil diperbarui.');
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

    // --- APPAREL ---
    public function apparelsIndex()
    {
        $categories = ApparelCategory::all();
        return view('pages.admin.allApparel', compact('categories'));
    }

    public function apparelsData(Request $request)
    {
        $data = Apparel::with('category')->orderBy('created_at', 'asc');

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('image', function ($row) {
                return $row->image ? asset('storage/' . $row->image) : null;
            })
            ->addColumn('category', function ($row) {
                return $row->category ? $row->category->name : '-';
            })
            ->rawColumns(['image'])
            ->make(true);
    }

    public function apparelsStore(Request $request)
    {
        $request->validate([
            'name_apparel' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'category_id' => 'required|exists:apparel_categories,id',
            'description' => 'nullable|string',
            'dimensions' => 'nullable|string',
            'weight' => 'nullable|string',
            'color' => 'nullable|string',
            'size' => 'nullable|string',
            'part_number' => 'nullable|string',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('apparels', 'public');
        }

        Apparel::create([
            'name_apparel' => $request->name_apparel,
            'image' => $path,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'dimensions' => $request->dimensions,
            'weight' => $request->weight,
            'color' => $request->color,
            'size' => $request->size,
            'part_number' => $request->part_number,
        ]);

        return redirect()->back()->with('success', 'Apparel berhasil ditambahkan!');
    }

    public function apparelsUpdate(Request $request, $id)
    {
        $apparel = Apparel::findOrFail($id);

        $request->validate([
            'name_apparel' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'category_id' => 'required|exists:apparel_categories,id',
            'description' => 'nullable|string',
            'dimensions' => 'nullable|string',
            'weight' => 'nullable|string',
            'color' => 'nullable|string',
            'size' => 'nullable|string',
            'part_number' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            if ($apparel->image && Storage::disk('public')->exists($apparel->image)) {
                Storage::disk('public')->delete($apparel->image);
            }
            $path = $request->file('image')->store('apparels', 'public');
            $apparel->image = $path;
        }

        $apparel->update([
            'name_apparel' => $request->name_apparel,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'dimensions' => $request->dimensions,
            'weight' => $request->weight,
            'color' => $request->color,
            'size' => $request->size,
            'part_number' => $request->part_number,
        ]);

        return redirect()->back()->with('success', 'Apparel berhasil diperbarui!');
    }

    public function apparelsDelete($id)
    {
        $apparel = Apparel::findOrFail($id);

        if ($apparel->image && Storage::disk('public')->exists($apparel->image)) {
            Storage::disk('public')->delete($apparel->image);
        }

        $apparel->delete();
        return redirect()->back()->with('success', 'Apparel berhasil dihapus!');
    }

        // --- BRANCH LIST + DATATABLES ---
    public function branchesIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = Branch::with(['area', 'city'])->select('branches.*')->orderBy('order');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nama', fn($row) => $row->name)
                ->addColumn('alamat', fn($row) => $row->address)
                ->editColumn('kode', function ($row) {
                    return '
                        <div>
                            <div style="margin-bottom: 4px;">Kode cabang: <strong>' . e($row->code) . '</strong></div>
                            <div>Wanda dealer id: <strong>' . e($row->wanda_dealer_id ?? '-') . '</strong></div>
                        </div>
                    ';
                })
                ->editColumn('order', fn($row) => $row->order)
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-sm btn-primary editBtn" data-id="' . $row->id . '"><i class="fa fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="' . $row->id . '"><i class="fa fa-trash"></i></button>
                    ';
                })
                ->rawColumns(['kode', 'action'])
                ->make(true);
        }

        $areas = BranchLocation::where('type', 'area')->get();
        $cities = BranchLocation::where('type', 'kota')->get();

        return view('pages.admin.manageBranch', compact('areas', 'cities'));
    }

    // --- STORE (ADD BRANCH) ---
    public function branchesStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'code' => 'required|string|unique:branches,code',
            'price_status' => 'required|in:reguler,khusus',
            'area_id' => 'required|exists:branch_locations,id',
            'city_id' => 'required|exists:branch_locations,id',
            'tax_number' => 'nullable|string',
            'ranking' => 'nullable|string',
            'service' => 'nullable|string',
            'address' => 'nullable|string',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'url' => 'nullable|string',
            'phone' => 'nullable|string',
            'phone2' => 'nullable|string',
            'phone3' => 'nullable|string',
            'fax' => 'nullable|string',
            'wanda_dealer_id' => 'nullable|string',
            'wanda_api_key' => 'nullable|string',
            'wanda_api_secret' => 'nullable|string',
            'ahass_code' => 'nullable|string',
        ]);

        $lastOrder = Branch::max('order') ?? 0;

        $data = $request->only([
            'name', 'code', 'price_status', 'area_id', 'city_id',
            'tax_number', 'ranking', 'service', 'address',
            'latitude', 'longitude', 'url', 'phone', 'phone2', 'phone3',
            'fax', 'wanda_dealer_id', 'wanda_api_key', 'wanda_api_secret',
            'ahass_code',
        ]);
        $data['order'] = $lastOrder + 1;

        Branch::create($data);

        return redirect()->back()->with('success', 'Branch created successfully.');
    }

    // --- EDIT (JSON DATA) ---
    public function branchesEdit($id)
    {
        $branch = Branch::findOrFail($id);
        return response()->json([
            'id' => $branch->id,
            'name' => $branch->name,
            'code' => $branch->code ?? '',
            'tax_number' => $branch->tax_number ?? '',
            'price_status' => $branch->price_status,
            'area_id' => $branch->area_id,
            'city_id' => $branch->city_id,
            'ranking' => $branch->ranking ?? '',
            'service' => $branch->service ?? '',
            'address' => $branch->address ?? '',
            'latitude' => $branch->latitude ?? '',
            'longitude' => $branch->longitude ?? '',
            'url' => $branch->url ?? '',
            'phone' => $branch->phone ?? '',
            'phone2' => $branch->phone2 ?? '',
            'phone3' => $branch->phone3 ?? '',
            'fax' => $branch->fax ?? '',
            'wanda_dealer_id' => $branch->wanda_dealer_id ?? '',
            'wanda_api_key' => $branch->wanda_api_key ?? '',
            'wanda_api_secret' => $branch->wanda_api_secret ?? '',
            'ahass_code' => $branch->ahass_code ?? '',
        ]);
    }

    // --- UPDATE ---
    public function branchesUpdate(Request $request, $id)
    {
        $branch = Branch::findOrFail($id);

        $request->validate([
            'name' => 'required|string',
            'code' => 'required|string|unique:branches,code,' . $id,
            'price_status' => 'required|in:reguler,khusus',
            'area_id' => 'required|exists:branch_locations,id',
            'city_id' => 'required|exists:branch_locations,id',
            'tax_number' => 'nullable|string',
            'ranking' => 'nullable|string',
            'service' => 'nullable|string',
            'address' => 'nullable|string',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'url' => 'nullable|string',
            'phone' => 'nullable|string',
            'phone2' => 'nullable|string',
            'phone3' => 'nullable|string',
            'fax' => 'nullable|string',
            'wanda_dealer_id' => 'nullable|string',
            'wanda_api_key' => 'nullable|string',
            'wanda_api_secret' => 'nullable|string',
            'ahass_code' => 'nullable|string',
            'order' => 'nullable|integer|min:1',
        ]);

        $branch->update($request->only([
            'name', 'code', 'tax_number', 'price_status', 'area_id', 'city_id',
            'ranking', 'service', 'address', 'latitude', 'longitude', 'url',
            'phone', 'phone2', 'phone3', 'fax',
            'wanda_dealer_id', 'wanda_api_key', 'wanda_api_secret',
            'ahass_code', 'order',
        ]));

        return response()->json(['message' => 'Branch updated successfully.']);
    }

    // --- DELETE ---
    public function branchesDelete($id)
    {
        $branch = Branch::findOrFail($id);
        $branch->delete();

        return response()->json(['message' => 'Branch deleted successfully.']);
    }

    // --- UPDATE ORDER ---
    public function updateBranchOrder(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:branches,id',
            'order' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $branch = Branch::findOrFail($request->id);
            $currentOrder = $branch->order;
            $newOrder = $request->order;

            if ($currentOrder == $newOrder) {
                return response()->json(['success' => true, 'message' => 'Order unchanged.']);
            }

            $maxOrder = Branch::max('order');
            if ($newOrder > $maxOrder) {
                $newOrder = $maxOrder;
            }

            $branch->update(['order' => 0]);

            if ($currentOrder < $newOrder) {
                Branch::where('order', '>', $currentOrder)->where('order', '<=', $newOrder)->decrement('order');
            } else {
                Branch::where('order', '>=', $newOrder)->where('order', '<', $currentOrder)->increment('order');
            }

            $branch->update(['order' => $newOrder]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Order berhasil diperbarui.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update order: ' . $e->getMessage()], 500);
        }
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

        // Jika new order lebih besar dari max order, batasi ke max order
        if ($newOrder > $maxOrder) {
            $newOrder = $maxOrder;
        }

        // Sementara set order banner yang dipindah ke 0 untuk menghindari konflik
        $banner->update(['order' => 0]);

        if ($currentOrder < $newOrder) {
            // Moving down (dari order kecil ke besar): 
            // geser semua banner yang ordernya antara currentOrder+1 sampai newOrder ke atas (kurangi 1)
            Banner::where('banner_template_id', $templateId)
                ->where('order', '>', $currentOrder)
                ->where('order', '<=', $newOrder)
                ->decrement('order');
        } else {
            // Moving up (dari order besar ke kecil):
            // geser semua banner yang ordernya antara newOrder sampai currentOrder-1 ke bawah (tambah 1)
            Banner::where('banner_template_id', $templateId)
                ->where('order', '>=', $newOrder)
                ->where('order', '<', $currentOrder)
                ->increment('order');
        }

        // Set order banner yang dipindah ke posisi baru
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

    /* =========================
       KELOLA SIMULASI KREDIT
   ========================== */
    // INDEX + DATATABLE
    public function creditSimulationIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = CreditSimulation::with(['category', 'motorType'])->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('category_name', function($row){
                    return $row->category ? $row->category->name : '-';
                })
                ->addColumn('motor_type_name', function($row){
                    return $row->motorType ? $row->motorType->name : '-';
                })
                ->editColumn('otr_price', function ($row) {
                    return 'Rp ' . number_format($row->otr_price, 0, ',', '.');
                })
                ->editColumn('minimum_dp', function ($row) {
                    return 'Rp ' . number_format($row->minimum_dp, 0, ',', '.');
                })
                ->addColumn('action', function($row){
                    return '
                        <button class="btn btn-sm btn-primary editBtn" data-id="'.$row->id.'">Edit</button>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="'.$row->id.'">Hapus</button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $categories = Category::where('type', 'motor')->get();
        $motorTypes = MotorType::all();

        return view('pages.admin.creditSimulation', compact('categories', 'motorTypes'));
    }

    // STORE
    public function creditSimulationStore(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'motor_type_id' => 'required|exists:motor_types,id',
            'motorcycle_variant' => 'required|string|max:255',
            'otr_price' => 'required|numeric',
            'minimum_dp' => 'required|numeric',
            'loan_term' => 'required|integer|min:1|max:60',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        CreditSimulation::create($data);

        return redirect()->back()->with('success', 'Data Simulasi Kredit berhasil ditambahkan!');
    }

    // UPDATE
    public function creditSimulationUpdate(Request $request, $id)
    {
        $simulation = CreditSimulation::findOrFail($id);

        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'motor_type_id' => 'required|exists:motor_types,id',
            'motorcycle_variant' => 'required|string|max:255',
            'otr_price' => 'required|numeric',
            'minimum_dp' => 'required|numeric',
            'loan_term' => 'required|integer|min:1|max:60',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $simulation->update($data);

        return redirect()->back()->with('success', ' Data Simulasi Kredit berhasil diperbarui!');
    }

    // DELETE
    public function creditSimulationDelete($id)
    {
        $simulation = CreditSimulation::findOrFail($id);
        $simulation->delete();

        return redirect()->back()->with('success', 'Simulasi Kredit berhasil dihapus!');
    }

    /* =========================
       KELOLA PRICE LIST
    ========================== */
    // INDEX + DATATABLE
    public function priceListIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = PriceList::select(['id', 'motorcycle_name', 'motor_type', 'price']);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('price_display', function ($row) {
                    return 'Rp ' . number_format($row->price, 0, ',', '.');
                })
                ->addColumn('action', function ($row) {
                    return '
                        <div class="btn-group">
                            <button class="btn btn-sm btn-primary me-1 editBtn" 
                                data-id="' . $row->id . '" 
                                data-motorcycle-name="' . e($row->motorcycle_name) . '" 
                                data-motor-type="' . e($row->motor_type) . '" 
                                data-price="' . $row->price . '">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button class="btn btn-sm btn-danger deleteBtn" 
                                data-id="' . $row->id . '" 
                                data-motorcycle-name="' . e($row->motorcycle_name) . '">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.admin.priceList');
    }

    // STORE
    public function priceListStore(Request $request)
    {
        $data = $request->validate([
            'motorcycle_name' => 'required|string|max:255',
            'motor_type' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        PriceList::create($data);

        return redirect()->back()->with('success', 'Data Price List berhasil ditambahkan!');
    }

    // UPDATE
    public function priceListUpdate(Request $request, $id)
    {
        $priceList = PriceList::findOrFail($id);

        $data = $request->validate([
            'motorcycle_name' => 'required|string|max:255',
            'motor_type' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        $priceList->update($data);

        return redirect()->back()->with('success', 'Data Price List berhasil diperbarui!');
    }

    // DELETE
    public function priceListDelete($id)
    {
        $priceList = PriceList::findOrFail($id);
        $priceList->delete();

        return redirect()->back()->with('success', 'Data Price List berhasil dihapus!');
    }

    // --- BRANCH AREA ---
    public function branchAreaIndex()
    {
        return view('pages.admin.manageBranchArea');
    }

    public function getBranchAreaData()
    {
        $data = BranchLocation::where('type', 'area')->select(['id', 'name', 'type']);

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('nama_area', function ($row) {
                return $row->name;
            })
            ->make(true);
    }

    public function storeBranchArea(Request $request)
    {
        $request->validate([
            'nama_area' => 'required|string|max:255',
        ]);

        BranchLocation::create([
            'type' => 'area',
            'name' => $request->nama_area,
        ]);

        return redirect()->back()->with('success', 'Area cabang berhasil ditambahkan.');
    }

    public function updateBranchArea(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:branch_locations,id',
            'nama_area' => 'required|string|max:255',
        ]);

        BranchLocation::where('id', $request->id)
            ->where('type', 'area')
            ->update([
                'name' => $request->nama_area,
            ]);

        return response()->json(['success' => true]);
    }

    public function deleteBranchArea(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:branch_locations,id',
        ]);

        BranchLocation::where('id', $request->id)
            ->where('type', 'area')
            ->delete();

        return redirect()->back()->with('success', 'Area cabang berhasil dihapus.');
    }

    // --- BRANCH CITY ---
    public function branchCityIndex()
    {
        return view('pages.admin.manageBranchCity');
    }

    public function getBranchCityData()
    {
         $data = BranchLocation::where('type', 'kota')
        ->select(['id', 'name', 'type'])
        ->orderBy('created_at', 'asc'); 

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('nama_kota', function ($row) {
                return $row->name;
            })
            ->addColumn('action', function ($row) {
                return '
                    <button class="btn btn-primary editBtn btn-sm" data-id="'.$row->id.'" data-name="'.$row->name.'">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button class="btn btn-danger deleteBtn btn-sm" data-id="'.$row->id.'" data-name="'.$row->name.'">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function storeBranchCity(Request $request)
    {
        $request->validate([
            'nama_kota' => [
                'required',
                'string',
                'max:255',
                Rule::unique('branch_locations', 'name')->where(function ($query) {
                    return $query->where('type', 'kota');       
                }),
            ],
        ]);

        BranchLocation::create([
            'type' => 'kota',                                   
            'name' => $request->nama_kota,
        ]);

        return redirect()->back()->with('success', 'Kota cabang berhasil ditambahkan.');
    }

    public function updateBranchCity(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:branch_locations,id',
            'nama_kota' => [
                'required',
                'string',
                'max:255',
                Rule::unique('branch_locations', 'name')
                    ->ignore($request->id)
                    ->where(function ($query) {
                        return $query->where('type', 'kota');  
                    }),
            ],
        ]);

        BranchLocation::where('id', $request->id)
            ->where('type', 'kota')                          
            ->update([
                'name' => $request->nama_kota,
            ]);

        return response()->json(['success' => true]);
    }

    public function deleteBranchCity(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:branch_locations,id',
        ]);

        BranchLocation::where('id', $request->id)
            ->where('type', 'kota')                             
            ->delete();

        return redirect()->back()->with('success', 'Kota cabang berhasil dihapus.');
    }
}