<?php

namespace App\Http\Controllers;

use App\Models\Motor;
use App\Models\Banner;
use App\Models\Branch;
use App\Models\Apparel;
use App\Models\ApparelImage;
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
use App\Models\CreditProvider;
use App\Models\CreditHeader;
use App\Models\CreditItem;
use App\Models\BannerTemplate;
use App\Models\MotorAccessory;
use App\Models\GeneralAccessory;
use App\Models\GeneralAccessoryVariant;
use App\Models\GeneralAccessoryImage;
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
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;


class AdminControllerSatu extends Controller
{

   /* =========================
       KELOLA MOTOR
   ========================== */

public function motorsIndex(Request $request)
{
    $publishedCount   = Motor::where('status', 'published')->count();
    $unpublishedCount = Motor::where('status', 'unpublished')->count();

    return view('pages.admin.motorDataTables', [
        'publishedCount'   => $publishedCount,
        'unpublishedCount' => $unpublishedCount,
        'categories'       => Category::all(),
        'types'            => MotorType::all(),
    ]);
}

public function motorsPublished(Request $request)
{
    if ($request->ajax()) {
        $data = Motor::where('status', 'published')->with('category', 'type');

        return DataTables::of($data)
            ->addIndexColumn()
            ->filter(function ($query) use ($request) {
                $search = $request->input('search.value');
                if (!empty($search)) {
                    $query->where(function ($q) use ($search) {
                        $q->where('motors.name', 'like', "%{$search}%")
                          ->orWhere('motors.motor_code_otr', 'like', "%{$search}%")
                          ->orWhere('motors.motor_code_credit', 'like', "%{$search}%")
                          ->orWhere('motors.wms_code', 'like', "%{$search}%")
                          ->orWhereHas('category', fn($qc)=>$qc->where('name','like',"%{$search}%"))
                          ->orWhereHas('type', fn($qt)=>$qt->where('name','like',"%{$search}%"));
                    });
                }
            })
            ->addColumn('product', function ($row) {
                $html = '
                    <div style="text-align:center; position:relative">
                        ' . ($row->is_new ? '<div style="position:absolute; right:6px; top:4px; color:#E11D2B; font-weight:700;">New !</div>' : '') . '
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
                                <td style="padding:8px; border: 1px solid black;">' . e($row->motor_code_otr ?: '-') . '</td>
                                <td style="padding:8px; border: 1px solid black;">' . e($row->wms_code ?: '-') . '</td>
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
                                <th style="padding:8px; border: 1px solid black;">Kredit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding:8px; border: 1px solid black;"><a href="' . route('admin.accessories.index', $row->id) . '" class="btn btn-sm btn-outline-primary" title="Aksesoris"><i class="fas fa-cogs"></i></a></td>
                                <td style="padding:8px; border: 1px solid black;"><a href="' . route('admin.colors.index', $row->id) . '" class="btn btn-sm btn-outline-primary" title="Warna"><i class="fas fa-tint"></i></a></td>
                                <td style="padding:8px; border: 1px solid black;"><a href="' . route('admin.specifications.index', $row->id) . '" class="btn btn-sm btn-outline-primary" title="Spesifikasi"><i class="fas fa-list"></i></a></td>
                                <td style="padding:8px; border: 1px solid black;"><a href="' . route('admin.features.index', $row->id) . '" class="btn btn-sm btn-outline-primary" title="Fitur"><i class="fas fa-star"></i></a></td>
                                <td style="padding:8px; border: 1px solid black;"><a href="' . route('admin.spareparts.index', $row->id) . '" class="btn btn-sm btn-outline-primary" title="Part"><i class="fas fa-wrench"></i></a></td>
                                <td style="padding:8px; border: 1px solid black;"><a href="' . route('admin.credits.index', ['motor' => $row->id]) . '" class="btn btn-sm btn-outline-primary" title="Kelola Kredit"><i class="fas fa-calculator"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                ';
                return $html;
            })
            ->addColumn('action', function ($row) {
                return '
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
                            data-is_new="' . ($row->is_new ? 1 : 0) . '"
                            data-thumbnail="' . ($row->thumbnail ? asset('storage/' . $row->thumbnail) : '') . '" 
                            data-accessory_thumbnail="' . ($row->accessory_thumbnail ? asset('storage/' . $row->accessory_thumbnail) : '') . '"
                            data-feature_thumbnail="' . ($row->feature_thumbnail ? asset('storage/' . $row->feature_thumbnail) : '') . '"
                            data-spin_gif="' . e($row->spin_gif ?? '') . '">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button class="btn btn-sm btn-danger deleteBtn" 
                            data-id="' . $row->id . '" 
                            data-name="' . e($row->name) . '">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['product', 'action'])
            ->make(true);
    }

    return view('pages.admin.motorPublished', [
        'categories' => Category::all(),
        'types'      => MotorType::all(),
    ]);
}

public function motorsUnpublished(Request $request)
{
    if ($request->ajax()) {
        $data = Motor::where('status', 'unpublished')->with('category', 'type');

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('product', function ($row) {
                // …(sama persis seperti di atas, hanya link kreditnya juga ke route('admin.credits.index', $row->id))
                return '
                    <div style="text-align:center; position:relative">
                        ' . ($row->is_new ? '<div style="position:absolute; right:6px; top:4px; color:#E11D2B; font-weight:700;">New !</div>' : '') . '
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
                                <td style="padding:8px; border: 1px solid black;">' . e($row->motor_code_otr ?: '-') . '</td>
                                <td style="padding:8px; border: 1px solid black;">' . e($row->wms_code ?: '-') . '</td>
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
                                <th style="padding:8px; border: 1px solid black;">Kredit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding:8px; border: 1px solid black;"><a href="' . route('admin.accessories.index', $row->id) . '" class="btn btn-sm btn-outline-primary"><i class="fas fa-cogs"></i></a></td>
                                <td style="padding:8px; border: 1px solid black;"><a href="' . route('admin.colors.index', $row->id) . '" class="btn btn-sm btn-outline-primary"><i class="fas fa-tint"></i></a></td>
                                <td style="padding:8px; border: 1px solid black;"><a href="' . route('admin.specifications.index', $row->id) . '" class="btn btn-sm btn-outline-primary"><i class="fas fa-list"></i></a></td>
                                <td style="padding:8px; border: 1px solid black;"><a href="' . route('admin.features.index', $row->id) . '" class="btn btn-sm btn-outline-primary"><i class="fas fa-star"></i></a></td>
                                <td style="padding:8px; border: 1px solid black;"><a href="' . route('admin.spareparts.index', $row->id) . '" class="btn btn-sm btn-outline-primary"><i class="fas fa-wrench"></i></a></td>
                                <td style="padding:8px; border: 1px solid black;"><a href="' . route('admin.credits.index', ['motor' => $row->id]) . '" class="btn btn-sm btn-outline-primary"><i class="fas fa-calculator"></i></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                ';
            })
            ->addColumn('action', fn($row)=> /* sama seperti published */ '
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
                        data-is_new="' . ($row->is_new ? 1 : 0) . '">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button class="btn btn-sm btn-danger deleteBtn" data-id="' . $row->id . '" data-name="' . e($row->name) . '">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            ')
            ->rawColumns(['product', 'action'])
            ->make(true);
    }

    return view('pages.admin.motorUnpublished', [
        'categories' => Category::all(),
        'types'      => MotorType::all(),
    ]);
}

public function motorsStore(Request $request)
{
    $data = $request->validate([
        'name'                => 'required|string|max:255',
        'motor_code_otr'      => 'nullable|string|max:255',
        'motor_code_credit'   => 'nullable|string|max:255',
        'wms_code'            => 'nullable|string|max:255',
        'price'               => 'nullable|integer|min:0', // <-- harga OTR
        'category_id'         => 'required|exists:categories,id',
        'type_id'             => 'required|exists:motor_types,id',
        'description'         => 'nullable|string',
        'thumbnail'           => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'accessory_thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'feature_thumbnail'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'spin_gif'            => 'nullable|file|mimes:gif|max:8192',
        'status'              => 'required|in:published,unpublished',
        'is_new'              => 'nullable|boolean',
    ]);

    $data['thumbnail']           = $this->uploadFile($request, 'thumbnail', 'thumbnails');
    $data['accessory_thumbnail'] = $this->uploadFile($request, 'accessory_thumbnail', 'accessory_thumbnails');
    $data['feature_thumbnail']   = $this->uploadFile($request, 'feature_thumbnail', 'feature_thumbnails');
    $data['spin_gif']            = $this->uploadFile($request, 'spin_gif', 'spin_gifs');
    $data['is_new']              = $request->boolean('is_new');

    $motor = Motor::create($data);

    return redirect()
        ->route($motor->status === 'published' ? 'admin.motors.published' : 'admin.motors.unpublished')
        ->with('success', 'Motor berhasil ditambahkan.');
}

public function updateMotor(Request $request, $id)
{
    $motor = Motor::findOrFail($id);

    $data = $request->validate([
        'name'                => 'required|string|max:255',
        'motor_code_otr'      => 'nullable|string|max:255',
        'motor_code_credit'   => 'nullable|string|max:255',
        'wms_code'            => 'nullable|string|max:255',
        'price'               => 'nullable|integer|min:0', // <-- harga OTR
        'category_id'         => 'required|exists:categories,id',
        'type_id'             => 'required|exists:motor_types,id',
        'description'         => 'nullable|string',
        'thumbnail'           => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'accessory_thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'feature_thumbnail'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'spin_gif'            => 'nullable|file|mimes:gif|max:8192',
        'status'              => 'required|in:published,unpublished',
        'is_new'              => 'nullable|boolean',
    ]);

    $data['thumbnail']           = $this->uploadFile($request, 'thumbnail', 'thumbnails', $motor->thumbnail);
    $data['accessory_thumbnail'] = $this->uploadFile($request, 'accessory_thumbnail', 'accessory_thumbnails', $motor->accessory_thumbnail);
    $data['feature_thumbnail']   = $this->uploadFile($request, 'feature_thumbnail', 'feature_thumbnails', $motor->feature_thumbnail);
    $data['spin_gif']            = $this->uploadFile($request, 'spin_gif', 'spin_gifs', $motor->spin_gif);
    $data['is_new']              = $request->boolean('is_new');

    $motor->update($data);

    return redirect()
        ->route($motor->status === 'published' ? 'admin.motors.published' : 'admin.motors.unpublished')
        ->with('success', 'Motor berhasil diperbarui.');
}

public function deleteMotor($id)
{
    $motor  = Motor::findOrFail($id);
    $status = $motor->status;

    if ($motor->thumbnail && Storage::exists('public/' . $motor->thumbnail)) {
        Storage::delete('public/' . $motor->thumbnail);
    }
    if ($motor->accessory_thumbnail && Storage::exists('public/' . $motor->accessory_thumbnail)) {
        Storage::delete('public/' . $motor->accessory_thumbnail);
    }
    if ($motor->feature_thumbnail && Storage::exists('public/' . $motor->feature_thumbnail)) {
        Storage::delete('public/' . $motor->feature_thumbnail);
    }
    if ($motor->spin_gif && Storage::exists('public/' . $motor->spin_gif)) {
        Storage::delete('public/' . $motor->spin_gif);
    }

    $motor->delete();

    return redirect()
        ->route($status === 'published' ? 'admin.motors.published' : 'admin.motors.unpublished')
        ->with('success', 'Motor berhasil dihapus.');
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
            ->addColumn('position', function($row) {
                // tampilkan "x,y" kalau ada
                if (!is_null($row->x_percent) && !is_null($row->y_percent)) {
                    return $row->x_percent . ',' . $row->y_percent;
                }
                return null;
            })
            ->rawColumns(['image', 'position'])
            ->make(true);
    }

    return view('pages.admin.motorAccessories', [
        'motor' => $motor
    ]);
}

public function accessoriesStore(Request $request, $motorId)
{
    $data = $request->validate([
        'name'         => 'required|string|max:255',
        'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        'part_number'  => 'nullable|string|max:255',
        'dimension'    => 'nullable|string|max:255',
        'weight'       => 'nullable|numeric',
        'description'  => 'nullable|string',
        'color'        => 'nullable|string|max:100',
        'material'     => 'nullable|string|max:100',
        'stock'        => 'nullable|integer|min:0',
        'x_percent'    => 'nullable|numeric|between:0,100',
        'y_percent'    => 'nullable|numeric|between:0,100',
    ]);

    $data['motor_id'] = $motorId;
    $data['image']    = $this->uploadFile($request, 'image', 'motor_accessories');

    MotorAccessory::create($data);

    return back()->with('success', 'Aksesoris berhasil ditambahkan.');
}

public function accessoriesUpdate(Request $request, $motorId, $id)
{
    $accessory = MotorAccessory::findOrFail($id);

    $data = $request->validate([
        'name'         => 'required|string|max:255',
        'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        'part_number'  => 'nullable|string|max:255',
        'dimension'    => 'nullable|string|max:255',
        'weight'       => 'nullable|numeric',
        'description'  => 'nullable|string',
        'color'        => 'nullable|string|max:100',
        'material'     => 'nullable|string|max:100',
        'stock'        => 'nullable|integer|min:0',
        'x_percent'    => 'nullable|numeric|between:0,100',
        'y_percent'    => 'nullable|numeric|between:0,100',
    ]);

    $data['image'] = $this->uploadFile($request, 'image', 'motor_accessories', $accessory->image);

    $accessory->update($data);

    return redirect()->route('admin.accessories.index', $motorId)
                     ->with('success', 'Aksesoris berhasil diperbarui.');
}

public function accessoriesDelete($motorId, $id)
{
    $accessory = MotorAccessory::findOrFail($id);

    if ($accessory->image && Storage::exists('public/' . $accessory->image)) {
        Storage::delete('public/' . $accessory->image);
    }

    $accessory->delete();

    return redirect()->route('admin.accessories.index', $motorId)
                     ->with('success', 'Aksesoris berhasil dihapus.');
}

// =============== KELOLA GENERAL AKSESORIS ===============

// LIST + VIEW
public function accessoriesGeneralIndex()
{
    return view('pages.admin.generalAccessories');
}

public function accessoriesGeneralData()
{
    // tidak perlu withCount variants; cukup kirim ringkasan teks varian
    $q = GeneralAccessory::query();

    return DataTables::of($q)
        ->addIndexColumn()
        ->editColumn('cover_image', fn($r) => $r->cover_image ? asset('storage/'.$r->cover_image) : null)
        ->addColumn('variant_excerpt', function ($r) {
            return Str::limit(trim(strip_tags($r->variant ?? '')), 60);
        })
        ->make(true);
}

// STORE
public function accessoriesGeneralStore(Request $r)
{
    $data = $r->validate([
        'name'         => 'required|string|max:255',
        'cover_image'  => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        'part_number'  => 'nullable|string|max:255',
        'dimension'    => 'nullable|string|max:255',
        'weight'       => 'nullable|numeric',
        'price'        => 'nullable|numeric',
        'description'  => 'nullable|string',
        'variant'      => 'nullable|string', // <-- varian teks
        'material'     => 'nullable|string|max:100',
        'color'        => 'nullable|string|max:100',
        'stock'        => 'nullable|integer|min:0',

        // gallery[]
        'gallery.*'    => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
    ]);

    // cover
    $data['cover_image'] = $this->uploadFile($r, 'cover_image', 'general_accessories');

    $acc = GeneralAccessory::create($data);

    // gallery images
    if ($r->hasFile('gallery')) {
        foreach ($r->file('gallery') as $idx => $file) {
            $path = $file->store('general_accessories/gallery', 'public');
            $acc->images()->create([
                'image'   => $path,
                'caption' => $r->input("gallery_captions.$idx"),
                'sort'    => $idx,
            ]);
        }
    }

    return back()->with('success', 'Aksesoris (General) berhasil ditambahkan.');
}

// EDIT (JSON utk modal)
public function accessoriesGeneralEdit($id)
{
    $acc = GeneralAccessory::with('images')->findOrFail($id);
    return response()->json($acc);
}

// UPDATE
public function accessoriesGeneralUpdate(Request $r, $id)
{
    $acc = GeneralAccessory::with('images')->findOrFail($id);

    $data = $r->validate([
        'name'         => 'required|string|max:255',
        'cover_image'  => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        'part_number'  => 'nullable|string|max:255',
        'dimension'    => 'nullable|string|max:255',
        'weight'       => 'nullable|numeric',
        'price'        => 'nullable|numeric',
        'description'  => 'nullable|string',
        'variant'      => 'nullable|string', // <-- varian teks
        'material'     => 'nullable|string|max:100',
        'color'        => 'nullable|string|max:100',
        'stock'        => 'nullable|integer|min:0',
        'gallery.*'    => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
    ]);

    $data['cover_image'] = $this->uploadFile($r, 'cover_image', 'general_accessories', $acc->cover_image);
    $acc->update($data);

    // tambah gallery baru (opsional)
    if ($r->hasFile('gallery')) {
        $start = ($acc->images()->max('sort') ?? 0) + 1;
        foreach ($r->file('gallery') as $k => $file) {
            $path = $file->store('general_accessories/gallery', 'public');
            $acc->images()->create([
                'image'   => $path,
                'caption' => $r->input("gallery_captions.$k"),
                'sort'    => $start + $k,
            ]);
        }
    }

    return back()->with('success', 'Aksesoris (General) berhasil diperbarui.');
}

// DELETE
public function accessoriesGeneralDelete($id)
{
    $acc = GeneralAccessory::with('images')->findOrFail($id);

    if ($acc->cover_image && \Storage::exists('public/'.$acc->cover_image)) {
        \Storage::delete('public/'.$acc->cover_image);
    }
    foreach ($acc->images as $im) {
        if ($im->image && \Storage::exists('public/'.$im->image)) {
            \Storage::delete('public/'.$im->image);
        }
    }

    $acc->delete();
    return back()->with('success', 'Aksesoris (General) dihapus.');
}

public function accessoriesGeneralDeleteImage($imageId)
{
    $img = GeneralAccessoryImage::findOrFail($imageId);

    // hapus file fisik
    if ($img->image && Storage::exists('public/'.$img->image)) {
        Storage::delete('public/'.$img->image);
    }

    // hapus record
    $img->delete();

    return response()->json(['status' => 'ok']);
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

    // Tidak perlu DataTables lagi (hanya 1 file PDF per motor)
    return view('pages.admin.motorSpareparts', [
        'motor' => $motor,
    ]);
}

public function sparepartsStore(Request $request, $motorId)
{
    $motor = Motor::findOrFail($motorId);

    $request->validate([
        'parts_pdf' => 'required|file|mimes:pdf|max:51200', // 50MB
    ]);

    // Simpan/replace file PDF. Helper uploadFile milikmu sudah dipakai di tempat lain.
    // Arg ke-4 = oldPath untuk dihapus otomatis (kalau helper-mu memang handle).
    $path = $this->uploadFile($request, 'parts_pdf', 'motor_catalogs', $motor->parts_pdf);

    $motor->update([
        'parts_pdf' => $path,
    ]);

    return back()->with('success', 'Katalog PDF berhasil diunggah.');
}

/**
 * Opsional: kalau tetap mau pakai route PUT / update, logic-nya sama dengan store (replace).
 */
public function sparepartsUpdate(Request $request, $motorId, $id = null)
{
    $motor = Motor::findOrFail($motorId);

    $request->validate([
        'parts_pdf' => 'required|file|mimes:pdf|max:51200',
    ]);

    $path = $this->uploadFile($request, 'parts_pdf', 'motor_catalogs', $motor->parts_pdf);

    $motor->update([
        'parts_pdf' => $path,
    ]);

    return redirect()->route('admin.spareparts.index', $motorId)->with('success', 'Katalog PDF berhasil diganti.');
}

/**
 * Hapus PDF (abaikan $id dari route lama).
 */
public function sparepartsDelete($motorId, $id = null)
{
    $motor = Motor::findOrFail($motorId);

    if ($motor->parts_pdf && Storage::exists('public/' . $motor->parts_pdf)) {
        Storage::delete('public/' . $motor->parts_pdf);
    }

    $motor->update([
        'parts_pdf' => null,
    ]);

    return redirect()->route('admin.spareparts.index', $motorId)->with('success', 'Katalog PDF berhasil dihapus.');
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
        'name'        => 'required|string|max:255',
        'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        'description' => 'nullable|string',
        'x_position'  => 'required|numeric|between:0,100',  // persen
        'y_position'  => 'required|numeric|between:0,100',  // persen
    ]);

    $data['motor_id'] = $motorId;
    $data['image']    = $this->uploadFile($request, 'image', 'motor_features');

    MotorFeature::create($data);
    return back()->with('success', 'Fitur berhasil ditambahkan.');
}

public function featuresUpdate(Request $request, $motorId, $id)
{
    $feature = MotorFeature::findOrFail($id);

    $data = $request->validate([
        'name'        => 'required|string|max:255',
        'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        'description' => 'nullable|string',
        'x_position'  => 'required|numeric|between:0,100',  // persen
        'y_position'  => 'required|numeric|between:0,100',  // persen
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

    // KELOLA MOTOR TYPE
    public function motorTypeIndex()
    {
        $categories = Category::orderBy('name')->get();
        return view('pages.admin.manageMotorType', compact('categories'));
    }

    public function getMotorType(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'Not Ajax'], 400);
        }

        // LEFT JOIN categories agar kolom 'tipe' bisa di-search & sort
        $data = MotorType::query()
            ->leftJoin('categories', 'categories.id', '=', 'motor_types.category_id')
            ->select([
                'motor_types.id',
                'motor_types.name',
                'motor_types.category_id',
                'motor_types.cover_image',     // <-- penting untuk preview
                'categories.name as tipe',
            ])
            ->orderBy('categories.name')
            ->orderBy('motor_types.name');

        return Datatables::of($data)
            ->addIndexColumn()
            // kolom thumbnail (server-side, agar ringan di Blade)
            ->addColumn('thumb', function ($row) {
                $src = $row->cover_image
                    ? asset('storage/' . $row->cover_image)
                    : asset('no-image.png');
                return '<img src="' . $src . '" alt="cover" style="height:48px;border-radius:8px">';
            })
            ->addColumn('action', function ($row) {
                return '
                    <button class="btn btn-sm btn-primary editBtn"
                        data-id="' . $row->id . '"
                        data-name="' . e($row->name) . '"
                        data-category_id="' . $row->category_id . '"
                        data-cover_image="' . e($row->cover_image ?? '') . '">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button class="btn btn-sm btn-danger deleteBtn"
                        data-id="' . $row->id . '"
                        data-name="' . e($row->name) . '">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                ';
            })
            ->rawColumns(['thumb','action'])
            ->make(true);
    }

    public function storeMotorType(Request $request)
    {
        $validated = $request->validate([
            'name'        => [
                'required','string','max:255',
                // unik per kategori
                Rule::unique('motor_types')->where(function ($q) use ($request) {
                    return $q->where('category_id', $request->category_id);
                }),
            ],
            'category_id' => ['required','exists:categories,id'],
            'cover_image' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
        ]);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('motor_types', 'public');
        }

        MotorType::create($validated);

        return redirect()->back()->with('success', 'Tipe motor berhasil ditambahkan');
    }

    public function updateMotorType(Request $request)
    {
        $validated = $request->validate([
            'id'          => ['required','exists:motor_types,id'],
            'name'        => [
                'required','string','max:255',
                // unik per kategori, abaikan diri sendiri
                Rule::unique('motor_types')->ignore($request->id)->where(function ($q) use ($request) {
                    return $q->where('category_id', $request->category_id);
                }),
            ],
            'category_id' => ['required','exists:categories,id'],
            'cover_image' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
        ]);

        $row = MotorType::findOrFail($validated['id']);
        $row->name        = $validated['name'];
        $row->category_id = $validated['category_id'];

        if ($request->hasFile('cover_image')) {
            // hapus file lama kalau ada
            if (!empty($row->cover_image)) {
                Storage::disk('public')->delete($row->cover_image);
            }
            $row->cover_image = $request->file('cover_image')->store('motor_types', 'public');
        }

        $row->save();

        return response()->json(['message' => 'Tipe motor berhasil diperbarui']);
    }

    public function deleteMotorType(Request $request)
    {
        $request->validate([
            'id' => ['required','exists:motor_types,id'],
        ]);

        $row = MotorType::findOrFail($request->id);

        if (!empty($row->cover_image)) {
            Storage::disk('public')->delete($row->cover_image);
        }

        $row->delete();

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

    // --- KELOLA APPAREL ---
public function apparelsIndex()
{
    $categories = ApparelCategory::all();
    return view('pages.admin.allApparel', compact('categories'));
}

public function apparelsData(Request $request)
{
    $data = Apparel::with([
        'category:id,name',
        'images' => fn($q) => $q->orderBy('sort')->orderBy('id'),
    ])->orderBy('created_at', 'asc');

    return DataTables::of($data)
        ->addIndexColumn()
        ->editColumn('image', function ($row) {
            // urutan prioritas sumber gambar
            $src = $row->cover_image ?: $row->image ?: optional($row->images->first())->image;
            $url = $src ? asset('storage/'.$src) : asset('no-image.png');

            $title    = e($row->name_apparel);
            $category = e(optional($row->category)->name ?? '-');

            $desc  = $row->description ? '<div><strong>Deskripsi:</strong> '.e($row->description).'</div>' : '';
            $size  = $row->size        ? '<div><strong>Ukuran:</strong> '.e($row->size).'</div>'             : '';
            $color = $row->color       ? '<div><strong>Warna:</strong> '.e($row->color).'</div>'             : '';

            // badge NEW seperti di Motor
            $badge = $row->is_new
                ? '<div style="position:absolute; right:6px; top:4px; color:#E11D2B; font-weight:700;">New !</div>'
                : '';

            // gambar di-center, teks di bawah (kaya sebelumnya)
            return '
                <div style="position:relative; text-align:center; margin-bottom:6px;">
                    '.$badge.'
                    <img src="'.$url.'" alt="'.$title.'"
                         style="width:60px;height:60px;object-fit:cover;border-radius:6px;cursor:pointer"
                         class="image-preview"
                         data-image="'.$url.'"
                         data-title="'.$title.'"
                         data-category="'.$category.'">
                </div>
                <div style="font-size:14px;line-height:1.4;text-align:left;">'.$desc.$size.$color.'</div>
            ';
        })
        ->addColumn('image_url', function ($row) {
            $src = $row->cover_image ?: $row->image ?: optional($row->images->first())->image;
            return $src ? asset('storage/'.$src) : asset('no-image.png');
        })
        ->addColumn('category', fn($row) => optional($row->category)->name ?? '-')
        ->rawColumns(['image'])
        ->make(true);
}

public function apparelsStore(Request $request)
{
    $request->validate([
        'name_apparel' => 'required|string|max:255',
        'category_id'  => 'required|exists:apparel_categories,id',
        'description'  => 'nullable|string',
        'material'     => 'nullable|string',
        'dimensions'   => 'nullable|string',
        'weight'       => 'nullable|string',
        'color'        => 'nullable|string',
        'size'         => 'nullable|string',
        'part_number'  => 'nullable|string',
        'stock'        => 'nullable|integer|min:0',
        // file
        'cover_image'  => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        'gallery.*'    => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        // NEW
        'is_new'       => 'nullable|boolean',
    ]);

    $coverPath = $request->hasFile('cover_image')
        ? $request->file('cover_image')->store('apparels/cover', 'public')
        : null;

    $singlePath = $request->hasFile('image')
        ? $request->file('image')->store('apparels', 'public')
        : null;

    $apparel = Apparel::create([
        'name_apparel' => $request->name_apparel,
        'category_id'  => $request->category_id,
        'description'  => $request->description,
        'material'     => $request->material,
        'dimensions'   => $request->dimensions,
        'weight'       => $request->weight,
        'color'        => $request->color,
        'size'         => $request->size,
        'part_number'  => $request->part_number,
        'stock'        => $request->stock,
        'cover_image'  => $coverPath,
        'image'        => $singlePath, // fallback/legacy
        'is_new'       => $request->boolean('is_new'), // <-- simpan flag NEW
    ]);

    if ($request->hasFile('gallery')) {
        foreach ($request->file('gallery') as $idx => $file) {
            $path = $file->store('apparels/gallery', 'public');
            $apparel->images()->create([
                'image'   => $path,
                'caption' => $request->input("gallery_captions.$idx"),
                'sort'    => $idx,
            ]);
        }
    }

    return back()->with('success', 'Apparel berhasil ditambahkan!');
}

// JSON untuk modal edit
public function apparelsEdit($id)
{
    $apparel = Apparel::with('images')->findOrFail($id);
    return response()->json($apparel);
}

public function apparelsUpdate(Request $request, $id)
{
    $apparel = Apparel::with('images')->findOrFail($id);

    $request->validate([
        'name_apparel' => 'required|string|max:255',
        'category_id'  => 'required|exists:apparel_categories,id',
        'description'  => 'nullable|string',
        'material'     => 'nullable|string',
        'dimensions'   => 'nullable|string',
        'weight'       => 'nullable|string',
        'color'        => 'nullable|string',
        'size'         => 'nullable|string',
        'part_number'  => 'nullable|string',
        'stock'        => 'nullable|integer|min:0',
        // file
        'cover_image'  => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        'gallery.*'    => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        // NEW
        'is_new'       => 'nullable|boolean',
    ]);

    // cover (replace file lama bila ada)
    if ($request->hasFile('cover_image')) {
        if ($apparel->cover_image && Storage::disk('public')->exists($apparel->cover_image)) {
            Storage::disk('public')->delete($apparel->cover_image);
        }
        $apparel->cover_image = $request->file('cover_image')->store('apparels/cover', 'public');
    }

    // single image (legacy)
    if ($request->hasFile('image')) {
        if ($apparel->image && Storage::disk('public')->exists($apparel->image)) {
            Storage::disk('public')->delete($apparel->image);
        }
        $apparel->image = $request->file('image')->store('apparels', 'public');
    }

    $apparel->update([
        'name_apparel' => $request->name_apparel,
        'category_id'  => $request->category_id,
        'description'  => $request->description,
        'material'     => $request->material,
        'dimensions'   => $request->dimensions,
        'weight'       => $request->weight,
        'color'        => $request->color,
        'size'         => $request->size,
        'part_number'  => $request->part_number,
        'stock'        => $request->stock,
        'is_new'       => $request->boolean('is_new'), // <-- update flag NEW
    ]);

    // Tambah gallery baru (append)
    if ($request->hasFile('gallery')) {
        $start = ($apparel->images()->max('sort') ?? 0) + 1;
        foreach ($request->file('gallery') as $k => $file) {
            $path = $file->store('apparels/gallery', 'public');
            $apparel->images()->create([
                'image'   => $path,
                'caption' => $request->input("gallery_captions.$k"),
                'sort'    => $start + $k,
            ]);
        }
    }

    return back()->with('success', 'Apparel berhasil diperbarui!');
}

public function apparelsDelete($id)
{
    $apparel = Apparel::with('images')->findOrFail($id);

    if ($apparel->cover_image && Storage::disk('public')->exists($apparel->cover_image)) {
        Storage::disk('public')->delete($apparel->cover_image);
    }
    if ($apparel->image && Storage::disk('public')->exists($apparel->image)) {
        Storage::disk('public')->delete($apparel->image);
    }
    foreach ($apparel->images as $im) {
        if ($im->image && Storage::disk('public')->exists($im->image)) {
            Storage::disk('public')->delete($im->image);
        }
    }

    $apparel->delete();
    return back()->with('success', 'Apparel berhasil dihapus!');
}

// Hapus satu gambar gallery
public function apparelsDeleteImage($imageId)
{
    $img = ApparelImage::findOrFail($imageId);

    if ($img->image && Storage::disk('public')->exists($img->image)) {
        Storage::disk('public')->delete($img->image);
    }

    $img->delete();
    return response()->json(['status' => 'ok']);
}

    /* =========================
       KELOLA CABANG (DEALER)
   ========================== */
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

    // === KELOLA BANNER ===
    /*------------------------------------------
    --------------------------------------------
    Banner Template Management
    --------------------------------------------*/

    // Halaman utama banner
    public function adminbanner()
    {
        return view('pages.admin.manageBanner');
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
   KREDIT PER MOTOR (HALAMAN)
========================== */
public function creditsIndex($motorId)
{
    $motor     = Motor::findOrFail($motorId);
    $providers = CreditProvider::orderBy('name')->get(['id','name']);

    // default tenor yang kamu pakai
    $defaultTenors = [11,17,23,27,29,33,35,41];

    return view('pages.admin.motorCredits', [
        'motor'     => $motor,
        'providers' => $providers,
        'tenors'    => $defaultTenors,
    ]);
}

/* =========================
   DATATABLE: DP × TENOR (latest header)
========================== */
public function creditsData(Request $request, $motorId)
{
    $motor = Motor::findOrFail($motorId);

    $header = CreditHeader::where('motor_id', $motor->id)
        ->orderByDesc('valid_from')
        ->orderByDesc('id')
        ->first();

    $rows = [];
    $tenors = [11,17,23,27,29,33,35,41];

    if ($header) {
        $items = CreditItem::where('header_id', $header->id)->get();

        // group by DP
        $byDp = [];
        foreach ($items as $it) {
            $byDp[$it->dp_amount][$it->tenor_months] = $it->installment;
            if (!in_array($it->tenor_months, $tenors)) $tenors[] = $it->tenor_months;
        }
        sort($tenors);

        // bentuk baris untuk DataTables (tanpa "Rp ")
        foreach ($byDp as $dp => $cols) {
            $row = ['dp' => number_format($dp, 0, ',', '.')];
            foreach ($tenors as $t) {
                $row[(string)$t] = isset($cols[$t]) ? number_format($cols[$t],0,',','.') : '-';
            }
            $rows[] = $row;
        }
    }

    return DataTables::of($rows)->with([
        'header' => $header ? [
            'id'          => $header->id,
            'provider_id' => $header->credit_provider_id,
            'valid_from'  => optional($header->valid_from)->format('Y-m-d'),
            'valid_to'    => optional($header->valid_to)->format('Y-m-d'),
            'note'        => $header->note,
            'tenors'      => $tenors,
        ] : null,
    ])->make(true);
}

/* =========================
   STORE (header + matrix items)
========================== */
public function creditsStore(Request $request, $motorId)
{
    $motor = Motor::findOrFail($motorId);

    $data = $request->validate([
        'provider_id' => 'nullable|exists:credit_providers,id',
        'valid_from'  => 'nullable|date',
        'valid_to'    => 'nullable|date|after_or_equal:valid_from',
        'note'        => 'nullable|string|max:255',
        'tenors'      => 'required|array|min:1',
        'tenors.*'    => 'integer|min:1',
        'rows'        => 'required|array|min:1',
    ]);

    $header = CreditHeader::create([
        'motor_id'           => $motor->id,
        'credit_provider_id' => $data['provider_id'] ?? null,
        'valid_from'         => $data['valid_from'] ?? null,
        'valid_to'           => $data['valid_to'] ?? null,
        'note'               => $data['note'] ?? null,
    ]);

    $bulk = [];
    foreach ($data['rows'] as $r) {
        if (!array_key_exists('dp', $r)) continue;
        $dp = $this->parseMoney($r['dp']);
        if ($dp <= 0) continue;

        foreach ($data['tenors'] as $t) {
            $key = (string) $t;
            if (!array_key_exists($key, $r)) continue;
            $inst = $this->parseMoney($r[$key]);
            if ($inst <= 0) continue;

            $bulk[] = [
                'header_id'    => $header->id,
                'dp_amount'    => $dp,
                'tenor_months' => (int)$t,
                'installment'  => $inst,
                'created_at'   => now(),
                'updated_at'   => now(),
            ];
        }
    }
    if ($bulk) CreditItem::insert($bulk);

    return back()->with('success', 'Simulasi kredit tersimpan.');
}

/* =========================
   EDIT (load satu header utk form)
========================== */
public function creditsEdit($motorId, $headerId)
{
    $header = CreditHeader::where('motor_id', $motorId)
        ->with(['items','provider:id,name'])
        ->findOrFail($headerId);

    // satukan items jadi struktur rows mirip input form
    $rows = [];
    $group = [];
    foreach ($header->items as $it) {
        $group[$it->dp_amount][(string)$it->tenor_months] = number_format($it->installment,0,',','.');
    }
    foreach ($group as $dp => $cols) {
        $rows[] = [
            'dp'   => number_format($dp,0,',','.'),
            'cols' => $cols
        ];
    }

    return response()->json([
        'header' => [
            'id'          => $header->id,
            'provider_id' => $header->credit_provider_id,
            'valid_from'  => optional($header->valid_from)->format('Y-m-d'),
            'valid_to'    => optional($header->valid_to)->format('Y-m-d'),
            'note'        => $header->note,
        ],
        'rows' => $rows,
    ]);
}

/* =========================
   UPDATE (replace items)
========================== */
public function creditsUpdate(Request $request, $motorId, $headerId)
{
    $header = CreditHeader::where('motor_id', $motorId)->findOrFail($headerId);

    $data = $request->validate([
        'provider_id' => 'nullable|exists:credit_providers,id',
        'valid_from'  => 'nullable|date',
        'valid_to'    => 'nullable|date|after_or_equal:valid_from',
        'note'        => 'nullable|string|max:255',
        'tenors'      => 'required|array|min:1',
        'tenors.*'    => 'integer|min:1',
        'rows'        => 'required|array|min:1',
    ]);

    $header->update([
        'credit_provider_id' => $data['provider_id'] ?? null,
        'valid_from'         => $data['valid_from'] ?? null,
        'valid_to'           => $data['valid_to'] ?? null,
        'note'               => $data['note'] ?? null,
    ]);

    // hapus items lama -> insert baru
    CreditItem::where('header_id', $header->id)->delete();

    $bulk = [];
    foreach ($data['rows'] as $r) {
        if (!array_key_exists('dp', $r)) continue;
        $dp = $this->parseMoney($r['dp']);
        if ($dp <= 0) continue;

        foreach ($data['tenors'] as $t) {
            $key = (string)$t;
            if (!array_key_exists($key, $r)) continue;
            $inst = $this->parseMoney($r[$key]);
            if ($inst <= 0) continue;

            $bulk[] = [
                'header_id'    => $header->id,
                'dp_amount'    => $dp,
                'tenor_months' => (int)$t,
                'installment'  => $inst,
                'created_at'   => now(),
                'updated_at'   => now(),
            ];
        }
    }
    if ($bulk) CreditItem::insert($bulk);

    return back()->with('success', 'Simulasi kredit diperbarui.');
}

/* =========================
   DELETE HEADER (items ikut terhapus via FK CASCADE)
========================== */
public function creditsDelete($motorId, $headerId)
{
    $header = CreditHeader::where('motor_id', $motorId)->findOrFail($headerId);
    $header->delete();

    return back()->with('success', 'Simulasi kredit dihapus.');
}

/* =========================
   LIST UNTUK TABEL RINGKAS:
   1 baris = 1 DP dalam 1 header (periode)
========================== */
public function creditsHeadersData($motorId)
{
    $headers = CreditHeader::where('motor_id', $motorId)
        ->orderByDesc('valid_from')->orderByDesc('id')
        ->get(['id','valid_from','valid_to','credit_provider_id','note']);

    $rows = [];

    foreach ($headers as $h) {
        $items = CreditItem::where('header_id', $h->id)
            ->get(['dp_amount','tenor_months']);

        if ($items->isEmpty()) {
            $rows[] = [
                'header_id'   => $h->id,
                'tenors_text' => '-',
                'dp_text'     => '-',
                'aksi'        =>
                    '<div class="btn-group">
                        <button class="btn btn-sm btn-primary js-edit" data-id="'.$h->id.'">
                          <i class="fa fa-pen"></i>
                        </button>
                        <button class="btn btn-sm btn-danger js-del" data-id="'.$h->id.'">
                          <i class="fa fa-trash"></i>
                        </button>
                     </div>',
            ];
            continue;
        }

        // Tenor unik untuk kolom "Tenor (bln)"
        $tenors = $items->pluck('tenor_months')->unique()->sort()->values()->all();
        $tenorsText = implode(',', $tenors);

        // SETIAP DP → 1 baris
        $dpList = $items->pluck('dp_amount')->unique()->sort()->values();
        foreach ($dpList as $dpRaw) {
            $rows[] = [
                'header_id'   => $h->id,
                'tenors_text' => $tenorsText,
                // tampilkan DP tanpa "Rp": 6500 -> "6.500"
                'dp_text'     => number_format((int)$dpRaw, 0, ',', '.'),
                'aksi'        =>
                    '<div class="btn-group">
                        <button class="btn btn-sm btn-primary js-edit"
                                data-id="'.$h->id.'"
                                data-dp="'.$dpRaw.'">
                          <i class="fa fa-pen"></i>
                        </button>
                        <button class="btn btn-sm btn-danger js-del" data-id="'.$h->id.'">
                          <i class="fa fa-trash"></i>
                        </button>
                     </div>',
            ];
        }
    }

    return \DataTables::of(collect($rows))
        ->addIndexColumn()
        ->rawColumns(['aksi'])
        ->make(true);
}

/** ========================
    Download template XLSX
======================== */
public function creditsTemplate($motorId)
{
    $motor  = Motor::findOrFail($motorId);

    // Atur tenor default (boleh kamu ubah/ambil dari DB)
    $tenors = [11,17,23,27,29,33,35];

    $ss    = new Spreadsheet();
    $sheet = $ss->getActiveSheet();
    $sheet->setTitle('Simulasi');

    // ukuran kolom/baris
    $sheet->getColumnDimension('A')->setWidth(14);
    for ($i=0; $i<count($tenors); $i++) {
        $col = Coordinate::stringFromColumnIndex(2+$i); // B..?
        $sheet->getColumnDimension($col)->setWidth(8.5);
    }
    $sheet->getRowDimension(1)->setRowHeight(20);
    $sheet->getRowDimension(2)->setRowHeight(20);

    // header kiri: UANG / MUKA
    $sheet->setCellValue('A1', 'UANG');
    $sheet->setCellValue('A2', 'MUKA');
    $sheet->getStyle('A1:A2')->applyFromArray([
        'font' => ['bold' => true],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical'=>Alignment::VERTICAL_CENTER],
        'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]
    ]);

    // header atas: JANGKA WAKTU
    $lastHeaderCol = Coordinate::stringFromColumnIndex(1 + count($tenors));
    $sheet->mergeCells("B1:{$lastHeaderCol}1");
    $sheet->setCellValue('B1', 'JANGKA WAKTU');
    $sheet->getStyle("B1:{$lastHeaderCol}1")->applyFromArray([
        'font' => ['bold' => true],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical'=>Alignment::VERTICAL_CENTER],
        'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]
    ]);

    // baris 2: angka tenor
    foreach ($tenors as $i => $t) {
        $col = Coordinate::stringFromColumnIndex(2 + $i); // mulai dari B
        $sheet->setCellValue("{$col}2", $t);
        $sheet->getStyle("{$col}2")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical'=>Alignment::VERTICAL_CENTER],
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]
        ]);
    }

    // area input
    $startRow = 3; $rows = 30; $endRow = $startRow + $rows - 1;
    $range = "A{$startRow}:{$lastHeaderCol}{$endRow}";
    $sheet->getStyle($range)->applyFromArray([
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical'=>Alignment::VERTICAL_CENTER],
    ]);

    // hint
    $sheet->setCellValue("A" . ($endRow + 2), "Isi kolom A baris 3 ke bawah dengan DP, dan isi angsuran di kolom tenor. Kosong = di-skip.");
    $sheet->getStyle("A" . ($endRow + 2))->getFont()->setSize(9);
    $sheet->mergeCells("A" . ($endRow + 2) . ":" . $lastHeaderCol . ($endRow + 2));

    $filename = 'template_simulasi_' . $motor->slug . '.xlsx';
    if (ob_get_length()) ob_end_clean();

    $writer = new Xlsx($ss);
    return response()->streamDownload(function() use ($writer) {
        $writer->save('php://output');
    }, $filename, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ]);
}

/** ========================
    Helper angka rupiah
======================== */
private function moneyToInt($v): int
{
    return (int) preg_replace('/[^\d]/', '', (string) $v);
}
private function parseMoney($v): int
{
    // alias supaya panggilan lama tetap jalan
    return $this->moneyToInt($v);
}

/* =========================
   BULK IMPORT dari Excel (langsung simpan)
========================== */
public function creditsImport(Request $request, $motorId)
{
    $motor = Motor::findOrFail($motorId);

    $data = $request->validate([
        'file'        => ['required','file','mimes:xls,xlsx,csv','max:10240'],
        'provider_id' => 'nullable|exists:credit_providers,id',
        'valid_from'  => 'nullable|date',
        'valid_to'    => 'nullable|date|after_or_equal:valid_from',
        'note'        => 'nullable|string|max:255',
        'mode'        => 'nullable|in:skip,overwrite', // duplikat DP
    ]);
    $mode = $data['mode'] ?? 'skip';

    // 1) Load sheet
    $path  = $request->file('file')->getRealPath();
    $sheet = IOFactory::load($path)->getActiveSheet();

    // 2) Temukan baris header tenor (angka di kolom B→)
    $headerRow = null; $tenors = []; // [colIndex => months]
    foreach ($sheet->getRowIterator(1, 60) as $row) {
        $r = $row->getRowIndex();
        $maybe = [];
        for ($c=2; $c<=200; $c++) {
            $raw = trim((string) $sheet->getCellByColumnAndRow($c, $r)->getCalculatedValue());
            if ($raw === '') continue;
            if (!preg_match('/^\d+$/', $raw)) { $maybe = []; break; }
            $maybe[$c] = (int) $raw;
        }
        if ($maybe) { $headerRow = $r; $tenors = $maybe; break; }
    }
    if (!$headerRow || !$tenors) {
        return back()->with('error','Header tenor tidak ditemukan (baris dengan 11,17,23,...)');
    }

    // 3) Buat header periode
    $header = CreditHeader::create([
        'motor_id'           => $motor->id,
        'credit_provider_id' => $data['provider_id'] ?? null,
        'valid_from'         => $data['valid_from'] ?? null,
        'valid_to'           => $data['valid_to']   ?? null,
        'note'               => $data['note']       ?? null,
    ]);

    // 4) Baca baris DP + angsuran per tenor
    $rows = [];
    $maxR = $sheet->getHighestRow();
    for ($r = $headerRow + 1; $r <= $maxR; $r++) {
        $dpRaw = trim((string) $sheet->getCellByColumnAndRow(1, $r)->getCalculatedValue());
        if ($dpRaw === '') continue;
        $dp = $this->moneyToInt($dpRaw);
        if ($dp <= 0) continue;

        $any = false;
        foreach ($tenors as $col => $months) {
            $cell = trim((string) $sheet->getCellByColumnAndRow($col, $r)->getCalculatedValue());
            if ($cell === '') continue;
            $angs = $this->moneyToInt($cell);
            if ($angs <= 0) continue;

            $rows[] = ['dp'=>$dp, 'tenor'=>(int)$months, 'angs'=>$angs];
            $any = true;
        }
        if (!$any) continue;
    }
    if (!$rows) {
        $header->delete();
        return back()->with('error','Tidak ada data DP × tenor yang terbaca.');
    }

    // 5) Kebijakan duplikat DP dalam periode ini
    $dpFile = collect($rows)->pluck('dp')->unique()->values();

    if ($mode === 'skip') {
        $existingDP = CreditItem::where('header_id',$header->id)
            ->whereIn('dp_amount',$dpFile)->pluck('dp_amount');

        if ($existingDP->isNotEmpty()) {
            $rows = collect($rows)->reject(fn($r)=>$existingDP->contains($r['dp']))->values()->all();
        }
    }

    if (!$rows) {
        $header->delete();
        return back()->with('error','Semua DP di file sudah pernah dimasukkan pada periode ini.');
    }

    // 6) Simpan
    $now = now();
    if ($mode === 'overwrite') {
        $payload = [];
        foreach ($rows as $r) {
            $payload[] = [
                'header_id'    => $header->id,
                'dp_amount'    => $r['dp'],
                'tenor_months' => $r['tenor'],
                'installment'  => $r['angs'],
                'created_at'   => $now,
                'updated_at'   => $now,
            ];
        }
        DB::table('credit_items')->upsert(
            $payload,
            ['header_id','dp_amount','tenor_months'],
            ['installment','updated_at']
        );
    } else {
        foreach (array_chunk($rows, 800) as $chunk) {
            $bulk = [];
            foreach ($chunk as $r) {
                $bulk[] = [
                    'header_id'    => $header->id,
                    'dp_amount'    => $r['dp'],
                    'tenor_months' => $r['tenor'],
                    'installment'  => $r['angs'],
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ];
            }
            DB::table('credit_items')->insertOrIgnore($bulk);
        }
    }

    return back()->with('success','Import selesai. Periode baru dibuat dan data berhasil dimasukkan.');
}

/* =========================
   EDIT BARIS (DP) - SHOW
   GET /admin/{motor}/credits/{header}/row?dp=6500000
========================== */
public function creditsRowShow(Request $request, $motorId, $headerId)
{
    $header = CreditHeader::where('motor_id', $motorId)->findOrFail($headerId);

    $dp = (int) $request->query('dp', 0);
    if ($dp <= 0) {
        return response()->json(['message' => 'DP tidak valid.'], 422);
    }

    // semua tenor pada header (urut)
    $allTenors = CreditItem::where('header_id', $header->id)
        ->pluck('tenor_months')->unique()->sort()->values()->all();

    // nilai angsuran untuk DP ini
    $pairs = CreditItem::where('header_id', $header->id)
        ->where('dp_amount', $dp)
        ->pluck('installment','tenor_months');

    return response()->json([
        'dp'     => $dp,
        'tenors' => $allTenors,
        'values' => $pairs, // map tenor => installment
    ]);
}

/* =========================
   EDIT BARIS (DP) - UPDATE
   PUT /admin/{motor}/credits/{header}/row
   body: old_dp, dp (baru), tenor[11]=..., tenor[17]=...
========================== */
public function creditsRowUpdate(Request $request, $motorId, $headerId)
{
    $header = CreditHeader::where('motor_id', $motorId)->findOrFail($headerId);

    $data = $request->validate([
        'old_dp' => 'required|integer|min:1',
        'dp'     => 'required|string',
        'tenor'  => 'nullable|array',
    ]);

    $oldDp = (int) $data['old_dp'];
    $newDp = $this->parseMoney($data['dp']);
    if ($newDp <= 0) return response()->json(['message'=>'DP baru tidak valid.'], 422);

    $tenorMap = collect($data['tenor'] ?? [])
        ->mapWithKeys(function($v,$k){
            $months = (int) $k;
            $val    = $this->parseMoney($v);
            return $months>0 && $val>0 ? [$months => $val] : [];
        })->all();

    if (empty($tenorMap)) {
        return response()->json(['message'=>'Isi minimal satu tenor.'], 422);
    }

    DB::transaction(function() use ($header, $oldDp, $newDp, $tenorMap){
        // 1) Jika DP berubah, update semua baris dp_amount tsb
        if ($newDp !== $oldDp) {
            CreditItem::where('header_id', $header->id)
                ->where('dp_amount', $oldDp)
                ->update(['dp_amount' => $newDp]);
        }

        // 2) Upsert nilai angsuran untuk tenor yg diisi
        $now = now();
        $payload = [];
        foreach ($tenorMap as $months => $angs) {
            $payload[] = [
                'header_id'    => $header->id,
                'dp_amount'    => $newDp,
                'tenor_months' => (int) $months,
                'installment'  => (int) $angs,
                'created_at'   => $now,
                'updated_at'   => $now,
            ];
        }

        DB::table('credit_items')->upsert(
            $payload,
            ['header_id','dp_amount','tenor_months'],
            ['installment','updated_at']
        );

        // (opsional) hapus tenor yang dikosongkan → uncomment jika mau:
        /*
        $keepTenors = array_keys($tenorMap);
        CreditItem::where('header_id', $header->id)
            ->where('dp_amount', $newDp)
            ->whereNotIn('tenor_months', $keepTenors)
            ->delete();
        */
    });

    return response()->json(['ok'=>true]);
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