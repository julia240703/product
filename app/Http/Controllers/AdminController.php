<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Motor;
use App\Models\MotorCategory;
use App\Models\MotorFeature;
use App\Models\MotorColor;
use App\Models\MotorSpecification;
use App\Models\MotorAccessory;
use App\Models\AccessoryCategory;
use App\Models\MotorPart;
use App\Models\Apparel;
use App\Models\ApparelCategory;
use App\Models\Branch;
use App\Models\BranchService;
use App\Models\Banner;
use App\Models\TestRide;
use App\Models\CreditSimulation;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class AdminController extends Controller
{

    public function adminHome()
    {
        $motorCount = Motor::count();
        $accessoryCount = MotorAccessory::count();
        $apparelCount = Apparel::count();
        $branchCount = Branch::count();
        $testRideCount = TestRide::count();
        $creditSimCount = CreditSimulation::count();

        return view('pages.admin.home', compact(
            'motorCount',
            'accessoryCount',
            'apparelCount',
            'branchCount',
            'testRideCount',
            'creditSimCount'
        ));
    }
    
    // --- MOTOR ---
    public function motorsIndex() 
    {
        $motors = Motor::with('category')->latest()->paginate(10);
        return view('admin.motors.index', compact('motors'));
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

    public function motorsUpdate(Request $request, $id) 
    {
        $motor = Motor::findOrFail($id);
        $data = $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:motor_categories,id',
        ]);

        $motor->update($data);
        return back()->with('success', 'Motor updated.');
    }

    public function motorsDelete($id) 
    {
        Motor::findOrFail($id)->delete();
        return back()->with('success', 'Motor deleted.');
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

    // --- BANNER ---
    public function bannersIndex()
    {
        $banners = Banner::latest()->paginate(10);
        return view('admin.banners.index', compact('banners'));
    }

    public function bannersCreate()
    {
        return view('admin.banners.create');
    }

    public function bannersStore(Request $request)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        $data['image_url'] = $this->uploadImage($request, 'image', 'banners');
        Banner::create($data);
        return back()->with('success', 'Banner added.');
    }

    public function bannersEdit($id)
    {
        $banner = Banner::findOrFail($id);
        return view('admin.banners.edit', compact('banner'));
    }

    public function bannersUpdate(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $data['image_url'] = $this->uploadImage($request, 'image', 'banners');
        }

        $banner->update($data);
        return back()->with('success', 'Banner updated.');
    }

    public function bannersDelete($id)
    {
        Banner::findOrFail($id)->delete();
        return back()->with('success', 'Banner deleted.');
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