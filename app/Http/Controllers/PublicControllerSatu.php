<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Motor;
use App\Models\Category;
use App\Models\MotorColor;
use App\Models\MotorFeature;
use App\Models\MotorSpecification;
use App\Models\MotorAccessory;
use App\Models\AccessoryCategory;
use App\Models\MotorPart;
use App\Models\Apparel;
use App\Models\ApparelCategory;
use App\Models\Branch;
use App\Models\Banner;
use App\Models\TestRide;
use App\Models\CreditSimulation;

class PublicControllerSatu extends Controller
{
    // --- HOME / LANDING ---
    public function home()
    {
        // Ambil hanya banner untuk template 'Home', status active, urutkan berdasarkan 'order'
        $banners = Banner::where('status', 'active')
                        ->whereHas('bannerTemplate', function ($query) {
                            $query->where('name', 'Home');
                        })
                        ->orderBy('order')
                        ->get();
        $motors = Motor::with(['category', 'colors', 'features'])->get();
        $categories = Category::all();

        return view('home', compact('banners', 'motors', 'categories'));
    }

    // --- MOTOR DETAIL ---
    public function motorDetail($id)
    {
        $motor = Motor::with([
            'category',
            'colors',
            'features',
            'specifications',
            'parts',
        ])->findOrFail($id);

        return view('public.motor-detail', compact('motor'));
    }

    // --- MOTOR PER CATEGORY ---
    public function motorsByCategory($name)
    {
        $category = Category::where('name', $name)->firstOrFail(); // Menggunakan tabel 'categories'
        $motors = Motor::with('category')->where('category_id', $category->id)->get();

        return view('public.motors-category', compact('motors', 'category'));
    }

    // --- BANDINGKAN MOTOR (MAX 5) ---
    public function compare(Request $request)
    {
        $motorIds = $request->input('motor_ids', []);
        $motors = Motor::with(['specifications', 'category'])->whereIn('id', $motorIds)->get();

        if (count($motorIds) > 5) {
        return back()->with('error', 'Maksimal 5 motor bisa dibandingkan.');
    }

        return view('public.compare', compact('motors'));
    }

    // --- AKSESORIS MOTOR ---
    public function accessories()
    {
        $categories = AccessoryCategory::with('accessories')->get();
        return view('public.accessories', compact('categories'));
    }

    // --- APPAREL ---
    public function apparels()
    {
        $categories = ApparelCategory::with('apparels')->get();
        return view('public.apparels', compact('categories'));
    }

    // --- CABANG / DEALER ---
    public function branches()
    {
        $branches = Branch::with('services')->get();
        return view('public.branches', compact('branches'));
    }

    // --- PRICE LIST ---
    public function priceList()
    {
        $categories = MotorCategory::with('motors')->get();
        return view('public.price-list', compact('categories'));
    }

    // --- FORM TEST RIDE ---
    public function showTestRideForm()
    {
        $categories = MotorCategory::all();
        $branches = Branch::all();
        return view('public.test-ride-form', compact('categories', 'branches'));
    }

    public function submitTestRide(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'city' => 'required|string',
            'branch_id' => 'required|exists:branches,id',
            'schedule_date' => 'required|date',
            'schedule_time' => 'required|date_format:H:i',
            'motor_category_id' => 'required|exists:motor_categories,id',
            'motor_id' => 'required|exists:motors,id',
        ]);

        TestRide::create($data);
        return back()->with('success', 'Form test ride berhasil dikirim.');
    }

    // --- FORM SIMULASI KREDIT ---
    public function showCreditForm()
    {
        $categories = MotorCategory::with('motors')->get();
        return view('public.credit-form', compact('categories'));
    }

    public function submitCreditSimulation(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'address' => 'required|string',
            'province' => 'required|string',
            'city' => 'required|string',
            'motor_category_id' => 'required|exists:motor_categories,id',
            'motor_type' => 'required|string',
            'motor_variant' => 'required|string',
            'otr_price' => 'required|numeric',
            'down_payment' => 'required|numeric',
            'tenor' => 'required|numeric',
        ]);

        CreditSimulation::create($data);
        return back()->with('success', 'Simulasi kredit berhasil dikirim.');
    }
}