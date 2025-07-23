<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Result;
use App\Models\Branch;
use App\Models\JobPosition;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;



class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $quizCount = Quiz::count();
        $quizAttempt = 0;
        $profile = Auth::user()->profile;

        if ($user) {
            $quizAttempt = Result::where('user_id', $user->id)->count();
        }

        $quizNotAttempt = $quizCount - $quizAttempt;

        $fillableFields = [
            'name',
            'email',
            'national_id',
            'address',
            'domicile',
            'birthdate',
            'gender',
            'marital_status',
            'religion',
            'applied_position',
            'mobile_number',
            'education',
            'branch_location',
            'job_status',
            'able_to_work',
            'cv',
            'photo'
        ];

        $filledFields = 0;
        if ($profile) { // profile not null
            foreach ($fillableFields as $field) {
                if (!empty($profile->$field)) {
                    $filledFields++;
                }
            }
        }        

        $completionPercentage = ($filledFields / count($fillableFields)) * 100;
        $completionPercentage = round($completionPercentage);

        return view('home', compact('quizCount', 'quizAttempt', 'quizNotAttempt', 'completionPercentage', 'profile'));
    }


    public function exam()
    {
        $user_id = Auth::user()->id; // Assuming you are using authentication
        $quizzes = Quiz::oldest()->get();
        $editableContent = file_get_contents(public_path('edited_content.txt'));


        $attemptedQuizzes = Result::where('user_id', $user_id)
            ->pluck('quiz_id')
            ->toArray();

        return view('/pages/user/exam', compact('quizzes', 'attemptedQuizzes', 'editableContent'));
    }


    public function detailQuiz($name)
    {
        $quiz = Quiz::where('name', $name)->firstOrFail();
        $quizLimit = $quiz->quiz_limit;

        $questions = Question::where('quiz_id', $quiz->id)
            ->where('is_example', 0)
            ->inRandomOrder()
            ->limit($quizLimit)
            ->get();

        return view('/pages/user/examDetail')
            ->with('quiz', $quiz)
            ->with('questions', $questions);
    }


    public function instruction($name)
    {
        $quiz = Quiz::where('name', $name)->firstOrFail();
        $quizLimit = $quiz->quiz_limit;

        $questions = Question::where('quiz_id', $quiz->id)
            ->where('is_example', 1)
            ->inRandomOrder()
            ->limit($quizLimit)
            ->get();

        return view('/pages/user/examInstruction', compact('quiz', 'questions'));
    }


    public function answersStore(Request $request, $quizId)
    {
        $data = $request->validate([
            'answers' => 'required|array',
            // 'answers.*' => 'required|in:A,B,C,D,E',
        ]);

        $user_id = Auth::user()->id; // Assuming you are using authentication

        // Check if the user has already attempted the quiz
        $attempt = Result::where('user_id', $user_id)
            ->where('quiz_id', $quizId)
            ->first();

        if ($attempt) {
            return redirect('/exam')->with('error', 'Anda telah mencoba quiz ini.');
        }

        // // Create attempt record
        // Attempt::create([
        //     'user_id' => $user_id,
        //     'quiz_id' => $quizId,
        // ]);

        $quiz = Quiz::findOrFail($quizId); // Retrieve the Quiz object

        $correctAnswersCount = 0; // Initialize the variable

        foreach ($data['answers'] as $question_id => $selectedAnswer) {
            $question = Question::find($question_id);

            Answer::create([
                'question_id' => $question_id,
                'user_id' => $user_id,
                'answer' => $selectedAnswer,
            ]);

            // Check if the selected answer is correct
            $correctAnswers = explode(', ', $question->is_correct);
            if (in_array($selectedAnswer, $correctAnswers)) {
                $correctAnswersCount++;
            }
        }

        $quizLimit = $quiz->quiz_limit;

        // Calculate the score based on the correct answers count
        $score = ($correctAnswersCount / $quizLimit) * 100;

        // Update the result score for the associated user and quiz
        Result::updateOrCreate(
            ['user_id' => $user_id, 'quiz_id' => $quizId],
            ['score' => $score]
        );

        return redirect('/exam')->with('success', 'Jawaban berhasil dikirim.');
    }


    public function showResults()
    {
        $user_id = Auth::user()->id; // Assuming you are using authentication

        $results = Result::where('user_id', $user_id)
            ->with('quiz')
            ->get();

        return view('/pages/user/result', compact('results'));
    }


    public function inputProfileData()
    {
        $user = Auth::user();
        $profile = null;
        $branchCities = [];
        $branches = [];
        $jobPosition = [];

        if ($user) {
            $profile = Profile::where('user_id', $user->id)->first();
            $branchCities = Branch::pluck('city')->unique();
            $branches = Branch::all();
            $jobPosition = JobPosition::pluck('position');
        }

        return view('pages/user/inputProfileData', compact('profile', 'branchCities', 'branches', 'jobPosition'));
    }


    public function storeProfileData(Request $request)
    {
        // Validation rules
        $request->validate([
            'image' => 'required|max:2048',
            'national_id' => 'required|string|max:255|unique:profiles',
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
            'branch' => 'required',
            'job_status' => 'required|string|max:255',
            'able_to_work' => 'required|string|max:255',
            'recruitment_source' => 'required|string|max:255',
            'other_source' => 'required_if:recruitment_source,Other',
            'cv' => 'required|max:2048',
        ]);
    
        // Retrieve authenticated user
        $user = Auth::user();
    
        if (!$user) {
            return redirect()->back()->with('error', 'Pengguna tidak diautentikasi');
        }
    
        // Retrieve associated profile
        $profile = Profile::where('user_id', $user->id)->first();
    
        if (!$profile) {
            return redirect()->back()->with('error', 'Profil tidak ditemukan');
        }
    
        // Ambil ID cabang dari tabel Branch berdasarkan city dan location
        $branchLocation = Branch::where('city', $request->input('branch_city'))
                                ->where('location', $request->input('branch'))
                                ->value('id');
    
        if (!$branchLocation) {
            return redirect()->back()->with('error', 'Cabang tidak ditemukan');
        }
    
        // Format tanggal lahir
        $birthplace = $request->input('birthplace');
        $inputBirthdate = $request->input('birthdate');
        $formattedBirthdate = Carbon::createFromFormat('Y-m-d', $inputBirthdate)->format('Y-m-d');
        $ttl = $birthplace . ', ' . $formattedBirthdate;
    
        // Format pendidikan
        $edu = $request->input('education') . ', ' . $request->input('major');
    
        // Format tanggal mulai kerja
        $ableToWork = $request->input('able_to_work');
        $formattedAbleToWork = Carbon::createFromFormat('Y-m-d', $ableToWork)->format('Y-m-d');
    
        // Rekrutmen source
        $recruitmentSource = $request->input('recruitment_source');
    
        // Upload foto
        if ($request->hasFile('image')) {
            $uploadedFile = $request->file('image');
            $allowedMimeTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            $fileMimeType = $uploadedFile->getClientMimeType();
    
            if (in_array($fileMimeType, $allowedMimeTypes)) {
                $fileName = time() . '.' . $uploadedFile->getClientOriginalExtension();
                $filePath = $uploadedFile->storeAs('public/files/photo', $fileName);
                $profile->photo = basename($filePath);
            } else {
                return redirect('/input-profile-data')->with('error', 'Format file tidak valid. Hanya JPG, JPEG, PNG yang diperbolehkan');
            }
        }
    
        // Update profile values
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
        $profile->branch_location = $branchLocation; // Simpan ID cabang, bukan string
        $profile->job_status = $request->input('job_status');
        $profile->able_to_work = $formattedAbleToWork;
    
        if ($recruitmentSource === 'Other') {
            $customSource = $request->input('other_source');
            $profile->recruitment_source = $customSource;
        } else {
            $profile->recruitment_source = $recruitmentSource;
        }
    
        // Simpan profile
        $profile->save();
    
        // Upload CV
        if ($request->hasFile('cv')) {
            $uploadedFile = $request->file('cv');
            $allowedMimeTypes = ['application/pdf'];
            $fileMimeType = $uploadedFile->getClientMimeType();
    
            if (in_array($fileMimeType, $allowedMimeTypes)) {
                $fileName = time() . '.' . $uploadedFile->getClientOriginalExtension();
                $filePath = $uploadedFile->storeAs('public/files/cv', $fileName);
                $profile->cv = basename($filePath);
            } else {
                return redirect('/input-profile-data')->with('error', 'Format file tidak valid. Hanya PDF yang diizinkan');
            }
        }
    
        // Simpan ulang setelah CV diperbarui
        $profile->save();
    
        return redirect('/home')->with('success', 'Data pribadi Anda telah berhasil diperbarui');
    }
    


    public function changePassword()
    {
        return view('/pages/user/change_password');
    }


    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        // $user = Auth::user();
        $user = User::find(Auth::id()); // Gunakan find() untuk mendapatkan instance User

        if (!Hash::check($request->old_password, $user->password)) {
            return redirect()->route('changePassword')->with('error', 'Password lama salah.');
        }

        $user->update(['password' => bcrypt($request->new_password)]);
        return redirect()->route('changePassword')->with('success', 'Password berhasil diubah.');
    }
}
