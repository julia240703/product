<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;
use App\Models\JobPosition;
use App\Models\Branch;
use Carbon\Carbon;

class EditUserProfileController extends Controller
{
    //
    public function profile()
    {
        $user = Auth::user();
        $profile = null;
        
        if ($user) {
            $profile = Profile::where('user_id', $user->id)->first();
            
            if ($profile) {
                $profile->education = str_replace(',', '', $profile->education);
            }
        }
    
        return view('/pages/user/userProfile', compact('profile'));
    }
    

    public function profileEdit()
    {
        $user = Auth::user();
        if ($user) {
            $profile = Profile::where('user_id', $user->id)->first();
            if ($profile) {
                // Assuming you have retrieved other necessary data for the view here
                $jobPositions = JobPosition::all();
                $branches = Branch::all();
                $branchCities = Branch::pluck('city')->unique();
    
                // Split the profile->birthdate into birthplace and birthdate
                $birthdateParts = explode(', ', $profile->birthdate);
                $birthplace = $birthdateParts[0] ?? '';
                $birthdate = $birthdateParts[1] ?? '';
    
            // Ambil kota dan lokasi berdasarkan ID
            $selectedBranchCity = null;
            $selectedBranch = null;

            if ($profile->branch_location) {
                $selectedBranchData = Branch::find($profile->branch_location);
                if ($selectedBranchData) {
                    $selectedBranchCity = $selectedBranchData->city;
                    $selectedBranch = $selectedBranchData->location;
                }
            }
                return view('/pages/user/userProfileEdit', compact('profile', 'birthplace', 'birthdate', 'jobPositions', 'branchCities', 'branches','selectedBranchCity', 'selectedBranch'));
            }
        }
    
        return redirect()->route('login'); // Redirect to login if the user is not authenticated
    }
    

    
    public function profileUpdate(Request $request)
    {
    // Validation rules
    $request->validate([
        'image' => 'nullable|mimes:jpeg,jpg,png|max:2048',
        'name' => 'required|max:1024',
        'national_id' => 'required|string|max:255',
        'address' => 'required|string|max:255',
        'domicile' => 'required|string|max:255',
        'birthplace' => 'required|string|max:255',
        'birthdate' => 'required|date',
        'gender' => 'required|string|max:255',
        'marital_status' => 'required|string|max:255',
        'religion' => 'required|string|max:255',
        'applied_position' => 'required|string|max:255',
        'mobile_number' => 'required|string|max:255',
        'education' => 'required|string|max:255',
        'branch_city' => 'required',
        'branch' => 'required|integer',
        'job_status' => 'required|string|max:255',
        'able_to_work' => 'required|string|max:255',
        'cv' => 'max:2048',

    ]);

    // Retrieve authenticated user
    $user = Auth::user();

    // combine 2 input into 1 field
    $birthplace = $request->input('birthplace');
    $inputBirthdate = $request->input('birthdate');

    // Convert the input birthdate to "Y-m-d" format using Carbon
    $formattedBirthdate = Carbon::createFromFormat('Y-m-d', $inputBirthdate)->format('Y-m-d');

    // Concatenate birthplace and formatted birthdate
    $ttl = $birthplace . ', ' . $formattedBirthdate;

    $education = $request->input('education');
    $major = $request->input('major');
    $edu = $education;

    $ableToWork = $request->input('able_to_work');

    // Convert the input able_to_work date to "Y-m-d" format using Carbon
    $formattedAbleToWork = Carbon::createFromFormat('Y-m-d', $ableToWork)->format('Y-m-d');

    if (!empty($major)) {
        $edu .= ', ' . $major;
    } 
    
    // Retrieve associated profile
    if (!$user) {
        return redirect()->route('login');
    }

    $profile = Profile::where('user_id', $user->id)->first();

    if ($profile) {
        // Check if a file is uploaded
        if ($request->hasFile('image')) {
            // Delete the existing photo if it exists
            if ($profile->photo) {
                Storage::delete('public/files/photo/' . $profile->photo);
            }

            $uploadedFile = $request->file('image');

            // Check the MIME type of the file
            $allowedMimeTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            $fileMimeType = $uploadedFile->getClientMimeType();

            if (in_array($fileMimeType, $allowedMimeTypes)) {
                // Generate a unique file name
                $fileName = time() . '.' . $uploadedFile->getClientOriginalExtension();

                // Store the file in the storage/app/public/files/photo directory
                $filePath = $uploadedFile->storeAs('public/files/photo', $fileName);
                $profile->photo = basename($filePath);
            } else {
                return redirect()->back()->with('error', 'Foto profil hanya boleh dalam format gambar (JPEG, JPG, PNG)');
            }
        }
        $branch = Branch::find($request->input('branch'));

        if (!$branch) {
            return redirect()->back()->with('error', 'Branch tidak ditemukan.');
        }
        $branchLocation = $branch ? $branch->id : null;

        
        $profile->name = $request->input('name');
        $profile->national_id = $request->input('national_id');
        $profile->address = $request->input('address');
        $profile->domicile = $request->input('domicile');
        $profile->birthdate = $ttl;
        $profile->gender = $request->input('gender');
        $profile->marital_status = $request->input('marital_status');
        $profile->religion = $request->input('religion');
        $profile->applied_position = $request->input('applied_position');
        $profile->landline_phone = $request->input('landline_phone');
        $profile->mobile_number = $request->input('mobile_number');
        $profile->education = $edu;
        $profile->branch_location = $branchLocation; 
        $profile->job_status = $request->input('job_status');
        $profile->able_to_work = $formattedAbleToWork;



        // Check if a file is uploaded
        if ($request->hasFile('cv')) {
            $uploadedFile = $request->file('cv');

            // Check the MIME type of the file
            $allowedMimeTypes = ['application/pdf'];
            $fileMimeType = $uploadedFile->getClientMimeType();

            if (in_array($fileMimeType, $allowedMimeTypes)) {
                // Generate a unique file name
                $fileName = time() . '.' . $uploadedFile->getClientOriginalExtension();

                // Store the file in the storage/app/public/files/cv directory
                $filePath = $uploadedFile->storeAs('public/files/cv', $fileName);
                $profile->cv = basename($filePath);
                
            } else {
                return redirect()->back()->with('error', 'Upload CV hanya dapat menerima format file .pdf');
            }
        }
        
        
        // Save the updated profile
        $profile->save();

        return redirect('profile')->with('success', 'Profil Anda telah berhasil diperbarui');
    }
    

    // Handle the case where the profile does not exist
    // ...
    }

    public function deleteCV()
    {
        // Retrieve the authenticated user
        $user = Auth::user();

        // Retrieve the associated profile
        if ($user) {
            $profile = Profile::where('user_id', $user->id)->firstOrFail();
            if ($profile) {
                echo $profile->name;
            }
        }
        // Get the CV file name
        $cvFileName = $profile->cv;

        // Delete the CV file from storage
        Storage::delete('public/files/cv/' . $cvFileName);

        // Clear the CV column value in the database
        $profile->cv = null;
        $profile->save();

        return redirect('/profile')->with('success', 'File CV berhasil dihapus.');
    }
}
