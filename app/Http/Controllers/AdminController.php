<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Quiz;
use App\Models\User;
use App\Models\Branch;
use App\Models\Result;
use App\Models\Profile;
use App\Models\Question;
use App\Models\JobPosition;
use App\Models\Motor;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class AdminController extends Controller
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
    public function adminHome()
    {
        $selectedOption = request()->input('duration', 'all');
        $quizCount = Quiz::count();
        $questionCount = Question::where('is_example', 0)->count();

        $userCounts = $this->getUserCounts($selectedOption);
        $followingCount = $this->getFollowingCount($selectedOption, $quizCount);
        $userFollowingCount = $this->getUserFollowingCount($selectedOption, $quizCount);
        $usersData = $this->getUsersData($selectedOption, $quizCount);

        $usersWithAverageScore = User::whereIn('id', $usersData->keys())->count();
        $passedNotFollowingCount = $usersWithAverageScore - $followingCount;

        // to do reduce by followingCount who had same as limit AVG Score
        //$passedNotFollowingCount = $usersWithAverageScore;

        $chartData = $this->getChartData($userCounts);

        $labels = $chartData['labels'];
        $counts = $chartData['counts'];

        // Update the query for totalUserCount based on selectedOption
        $totalUserCount = $this->getTotalUserCount($selectedOption);

        return view('adminHome', compact(
            'questionCount',
            'quizCount',
            'totalUserCount',
            'followingCount',
            'passedNotFollowingCount',
            'usersWithAverageScore',
            'labels',
            'counts',
            'selectedOption',
            'userFollowingCount'
        ));
    }

    private function getTotalUserCount($selectedOption)
    {
        $query = User::where('type', 0);

        if ($selectedOption === 'today') {
            $query->whereDate('created_at', '>=', Carbon::today()->subDays(1));
        } elseif ($selectedOption === '7days') {
            $query->whereDate('created_at', '>=', Carbon::today()->subDays(7));
        } elseif ($selectedOption === '30days') {
            $query->whereDate('created_at', '>=', Carbon::today()->subDays(30));
        } elseif ($selectedOption === '60days') {
            $query->whereDate('created_at', '>=', Carbon::today()->subDays(60));
        } elseif ($selectedOption === '90days') {
            $query->whereDate('created_at', '>=', Carbon::today()->subDays(90));
        }

        return $query->count();
    }

    private function getUserCounts($selectedOption)
    {
        $query = User::where('type', 0);
        $dateColumn = 'created_at';

        if ($selectedOption === 'today') {
            $query->whereDate($dateColumn, '>=', Carbon::today()->subDays(1));
        } elseif ($selectedOption === '7days') {
            $query->whereDate($dateColumn, '>=', Carbon::today()->subDays(7));
        } elseif ($selectedOption === '30days') {
            $query->whereDate($dateColumn, '>=', Carbon::today()->subDays(30));
        } elseif ($selectedOption === '60days') {
            $query->whereDate($dateColumn, '>=', Carbon::today()->subDays(60));
        } elseif ($selectedOption === '90days') {
            $query->whereDate($dateColumn, '>=', Carbon::today()->subDays(90));
        }

        return $query->select(
            DB::raw('DATE(' . $dateColumn . ') AS date'),
            DB::raw('COUNT(*) AS count')
        )->groupBy('date')->get();
    }

    private function getFollowingCount($selectedOption, $quizCount)
    {
        $query = Profile::whereHas('user.results', function ($query) use ($selectedOption) {
            $dateColumn = 'results.created_at';

            if ($selectedOption === 'today') {
                $query->whereDate($dateColumn, '>=', Carbon::today()->subDays(1));
            } elseif ($selectedOption === '7days') {
                $query->whereDate($dateColumn, '>=', Carbon::today()->subDays(7));
            } elseif ($selectedOption === '30days') {
                $query->whereDate($dateColumn, '>=', Carbon::today()->subDays(30));
            } elseif ($selectedOption === '60days') {
                $query->whereDate($dateColumn, '>=', Carbon::today()->subDays(60));
            } elseif ($selectedOption === '90days') {
                $query->whereDate($dateColumn, '>=', Carbon::today()->subDays(90));
            }
        });

        return $query->where(function ($query) {
            $query->where('follow_up', Auth::user()->name)
                ->orWhereNotNull('follow_up');
        })->count();
    }

    private function getUserFollowingCount($selectedOption, $quizCount)
    {
        $query = Profile::where(function ($query) use ($selectedOption) {
            $dateColumn = 'processed_at';

            if ($selectedOption === 'today') {
                $query->whereDate($dateColumn, '>=', Carbon::today()->subDays(1));
            } elseif ($selectedOption === '7days') {
                $query->whereDate($dateColumn, '>=', Carbon::today()->subDays(7));
            } elseif ($selectedOption === '30days') {
                $query->whereDate($dateColumn, '>=', Carbon::today()->subDays(30));
            } elseif ($selectedOption === '60days') {
                $query->whereDate($dateColumn, '>=', Carbon::today()->subDays(60));
            } elseif ($selectedOption === '90days') {
                $query->whereDate($dateColumn, '>=', Carbon::today()->subDays(90));
            }
        });

        return $query->where(function ($query) {
            $query->where('follow_up', Auth::user()->name);
        })->count();
    }

    private function getUsersData($selectedOption, $quizCount)
    {
        // Read the average score from avgScore.txt
        $avgScoreFilePath = public_path('/txt/avgScore.txt');
        $avgScore = (float) File::get($avgScoreFilePath);

        $query = Result::select('user_id', DB::raw('AVG(score) as average_score'))
            ->join('users', 'results.user_id', '=', 'users.id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(results.id) = ?', [$quizCount])
            ->havingRaw('AVG(score) >= ?', [$avgScore]);

        if ($selectedOption === 'today') {
            $query->whereDate('results.created_at', '>=', Carbon::today()->subDays(1));
        } elseif ($selectedOption === '7days') {
            $query->whereDate('results.created_at', '>=', Carbon::today()->subDays(7));
        } elseif ($selectedOption === '30days') {
            $query->whereDate('results.created_at', '>=', Carbon::today()->subDays(30));
        } elseif ($selectedOption === '60days') {
            $query->whereDate('results.created_at', '>=', Carbon::today()->subDays(60));
        } elseif ($selectedOption === '90days') {
            $query->whereDate('results.created_at', '>=', Carbon::today()->subDays(90));
        }

        return $query->pluck('average_score', 'user_id')
            ->map(function ($averageScores) {
                return [
                    'average_score' => $averageScores,
                    'progress' => '100%',
                ];
            });
    }

    private function getChartData($userCounts)
    {
        $labels = [];
        $counts = [];

        foreach ($userCounts as $userCount) {
            $labels[] = date('M d', strtotime($userCount->date));
            $counts[] = $userCount->count;
        }

        return compact('labels', 'counts');
    }


    public function adminProfile()
    {
        return view('/pages/admin/adminProfile');
    }


    public function adminUsers(Request $request)
    {
        $selectedOption = $request->input('duration', 'all');
        $user = Auth::user(); // Get the currently logged-in user

        $query = Profile::join('users', 'users.id', '=', 'profiles.user_id')
            ->select('profiles.*', 'users.name', 'users.email')
            ->where('users.type', '=', '0'); // Only show users (participants)

        if ($selectedOption === 'today') {
            $query->whereDate('profiles.created_at', '>=', now()->subDays(1));
        } elseif ($selectedOption === '7days') {
            $query->whereDate('profiles.created_at', '>=', now()->subDays(7));
        } elseif ($selectedOption === '30days') {
            $query->whereDate('profiles.created_at', '>=', now()->subDays(30));
        } elseif ($selectedOption === '60days') {
            $query->whereDate('profiles.created_at', '>=', now()->subDays(60));
        } elseif ($selectedOption === '90days') {
            $query->whereDate('profiles.created_at', '>=', now()->subDays(90));
        }


        // If the user is an admin (type = 1) or a sub-admin without branch_id, show all data (no filtering)

        if ($request->ajax()) {
            $data = $query->latest()->get();

            $data = $data->reverse()->values(); // Reverse to display older records first

            $data = $data->map(function ($profile, $index) {
                $profile->row_number = $index + 1;
                return $profile;
            });

            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('users.show', $row->id) . '" class="btn btn-primary btn-sm">
                            <i class="fas fa-info"></i>
                        </a>
                        <button class="btn btn-danger deleteBtn btn-sm" data-id="' . $row->id . '">
                            <i class="fa-solid fa-trash" style="color: #ffffff;"></i>
                        </button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.admin.userDataTables', compact('selectedOption'));
    }


    public function showUsers(User $user)
    {
        $profile = $user->profile; // Use the relationship to fetch the profile

        if ($profile) {
            $profile->education = str_replace(',', '', $profile->education);
        }

        return view('/pages/admin/adminUserDetail', compact('user', 'profile'));
    }



    public function adminManager(Request $request)
    {
        if ($request->ajax()) {
            $data = Profile::join('users', 'users.id', '=', 'profiles.user_id')
                ->leftJoin('branches', 'profiles.branch_location', '=', 'branches.id')
                ->select(
                    'users.id as user_id',
                    'profiles.id as profile_id',
                    'profiles.*',
                    'profiles.branch_location as branch_id',
                    'users.name',
                    'users.email',
                    'branches.id as branch_id',
                    'branches.city as branch_city',
                    'branches.location as branch_location'
                )

                ->where('users.type', '=', '2')
                ->latest()
                ->get();


            $data = $data->reverse()->values(); // Reverse collection untuk menampilkan yang paling lama di atas

            $data = $data->map(function ($profile, $index) {
                $profile->row_number = $index + 1;
                return $profile;
            });

            return DataTables::of($data)
                ->addColumn('branch', function ($row) {
                    if ($row->branch_city && $row->branch_location) {
                        return $row->branch_city . ' - ' . $row->branch_location;
                    }
                    return 'HO';
                })

                ->addColumn('action', function ($row) {
                    return '
                    <button class="btn btn-primary btn-sm me-1 editBtn"
                        data-id="' . $row->user_id . '"
                        data-name="' . $row->name . '"
                        data-email="' . $row->email . '"
                        data-branch="' . $row->branch_id . '">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-warning btn-sm me-1 resetPasswordBtn"
                        data-id="' . $row->user_id . '"
                        data-name="' . $row->name . '">
                        <i class="fa fa-key"></i>
                    </button>
                    <button class="btn btn-danger deleteBtn btn-sm" data-id="' . $row->user_id . '">
                        <i class="fa-solid fa-trash" style="color: #ffffff;"></i>
                    </button>';
                })


                ->rawColumns(['action'])
                ->make(true);
        }

        // Retrieve branch list for dropdown
        $branches = Branch::all();

        return view('pages.admin.motorDataTables', compact('branches'));
    }


    public function showManager(User $user)
    {
        $profile = Profile::join('users', 'users.id', '=', 'profiles.user_id')
            ->select('profiles.*')
            ->where('users.id', $user->id)
            ->first();

        return view('/pages/admin/adminManagerDetail', compact('user', 'profile'));
    }


    public function userResults(Request $request)
    {
        $users = User::where('type', 0)->with(['results.quiz', 'profile'])->get();
        $totalQuizzes = Quiz::count();

        if ($request->ajax()) {
            $data = Result::selectRaw('users.id AS id, users.name AS name, CONCAT(branches.city, " - ", branches.location) AS branch_location, profiles.education AS education, profiles.applied_position AS applied_position, ROUND(AVG(results.score), 2) AS average_score, COUNT(DISTINCT results.quiz_id) AS quizzes_taken, DATE_FORMAT(MAX(results.created_at), "%Y-%m-%d") as created_at_formatted')
                ->join('users', 'users.id', '=', 'results.user_id')
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->leftJoin('branches', 'branches.id', '=', 'profiles.branch_location')
                ->groupBy('users.id', 'users.name', 'profiles.branch_location', 'profiles.education', 'profiles.applied_position')
                ->get();

            // Remove commas from education field
            $data->transform(function ($item) {
                $item->education = str_replace(',', '', $item->education);
                return $item;
            });

            return DataTables::of($data)
                ->addColumn('row_number', function ($row) use ($data) {
                    return $data->search($row) + 1;
                })
                ->addColumn('action', function ($row) {})
                ->addColumn('progress', function ($row) use ($totalQuizzes) {
                    $progress = round($row->quizzes_taken / $totalQuizzes * 100, 2);
                    return $progress . '%';
                })
                ->rawColumns(['action', 'progress'])
                ->make(true);
        }

        return view('/pages/admin/result', compact('users'));
    }


    public function userResultsDetail(User $user)
    {
        $user->load('results.quiz');

        $profile = Profile::join('users', 'users.id', '=', 'profiles.user_id')
            ->select('*')
            ->where('users.id', $user->id)
            ->first();

        if ($profile) {
            $profile->education = str_replace(',', '', $profile->education);
        }

        // Prepare data for the line chart
        $labels = $user->results->map(function ($result) {
            $words = explode(' ', $result->quiz->name, 3);
            return implode(' ', array_slice($words, 0, 2));
        })->toArray();

        $scores = $user->results->pluck('score')->toArray();

        return view('/pages/admin/resultDetail', compact('user', 'profile', 'labels', 'scores'));
    }

    public function resetQuiz(User $user)
    {
        $user->results()->delete();

        return redirect()->route('user.results')->with('success', 'Quiz user berhasil di-reset.');
    }


    public function addManager(Request $request)
    {
        $request->validate([
        'name' => 'required|string|max:255',
        'category' => 'required|string|max:255',
        'price' => 'required|numeric',
        'color' => 'required|string',
    ]);

    // Create the new motor record
    $motor = new Motor; // Pastikan Anda memiliki model Motor
    $motor->name = $request->name;
    $motor->category = $request->category;
    $motor->price = $request->price;
    $motor->color = $request->color;
    $motor->save();

    return redirect()->back()->with('success', 'Motor berhasil ditambahkan!');
    }

    public function updateManager(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->user_id,
            'branch_id' => 'nullable|exists:branches,id'
        ]);

        $user = User::find($request->user_id);
        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan.');
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->branch_id = $request->branch_id;
        $user->save();

        $profile = Profile::where('user_id', $user->id)->first();
        if ($profile) {
            $profile->name = $request->name;
            $profile->email = $request->email;
            $profile->branch_location = $request->branch_id;
            $profile->save();
        }

        return redirect()->route('admin.manager')->with('success', 'Sub-Admin berhasil diperbarui.');
    }

    public function resetManagerPassword(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::find($request->user_id);

        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan.');
        }

        $newPassword = Str::random(6);
        $user->password = Hash::make($newPassword);
        $user->save();

        return redirect()->route('admin.manager')->with('success', 'Password berhasil direset. Password baru: ' . $newPassword);
    }


    public function deleteManager(Request $request)
    {
        $userId = $request->input('user_id');

        $user = User::find($userId);

        if ($user) {
            // Delete the user
            $user->delete();

            return redirect()->back()->with('success', 'Sub-Admin berhasil dihapus!');
        }

        return redirect()->back()->with('error', 'Sub-Admin tidak ditemukan.');
    }


    public function deleteUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->user_id);

        if ($user) {
            $profile = $user->profile;

            if ($profile) {
                Storage::delete([
                    'public/files/cv/' . $profile->cv,
                    'public/files/photo/' . $profile->photo,
                ]);
                $profile->delete();
            }

            $user->delete();

            return redirect()->route('admin.users')->with('success', 'User berhasil dihapus.');
        }

        return redirect()->back()->with('error', 'User tidak ditemukan.');
    }




    public function deleteAllUsers()
    {
        // Get all users with type 0
        $usersToDelete = User::where('type', 0)->get();

        foreach ($usersToDelete as $user) {
            // Retrieve the associated profile
            $profile = $user->profile;

            if ($profile) {
                // Delete the associated files
                Storage::delete([
                    'public/files/cv/' . $profile->cv,
                    'public/files/photo/' . $profile->photo,
                ]);

                // Delete the profile
                $profile->delete();
            }

            // Delete the user
            $user->delete();
        }

        return redirect()->back()->with('success', 'Semua pengguna berhasil dihapus.');
    }


    public function followUp(User $user)
    {
        $followerName = Auth::user()->name;
        $profile = $user->profile;

        // If already followed-up
        if ($profile->follow_up) {
            return redirect()->back()->with('error', "This user has been followed up by {$profile->follow_up}.");
        }

        // Retrieve the average passing score from the file
        $avgScoreFilePath = public_path('/txt/avgScore.txt');
        $avgScore = (float) File::get($avgScoreFilePath);
        $quizCount = Quiz::count();

        // Calculate the average user score and the number of quizzes done
        $resultStats = Result::where('user_id', $user->id)
            ->selectRaw('AVG(score) as average_score, COUNT(DISTINCT quiz_id) as quiz_done')
            ->first();

        if (!$resultStats || $resultStats->quiz_done < $quizCount || $resultStats->average_score < $avgScore) {
            return redirect()->back()->with('error', 'This user has not met the passing criteria and cannot be followed up.');
        }

        // Save follow-up
        $profile->follow_up = $followerName;
        $profile->processed_at = now();
        $profile->save();

        return redirect()->back()->with('success', 'Berhasil menindaklanjuti pengguna.');
    }



    public function unfollow(User $user)
    {
        $followerName = null;
        $profile = $user->profile;

        $profile->follow_up = $followerName;
        $profile->save();

        return redirect()->back()->with('success', 'Berhasil berhenti mengikuti pengguna.');
    }


    public function previewQuiz($name)
    {
        $quiz = Quiz::where('name', $name)->firstOrFail();
        $quizLimit = $quiz->quiz_limit;

        $questions = Question::where('quiz_id', $quiz->id)
            ->where('is_example', 0)
            ->inRandomOrder()
            ->limit($quizLimit)
            ->get();

        return view('/pages/admin/previewQuiz', compact('quiz', 'questions'));
    }


    public function instructionQuiz($name)
    {
        $quiz = Quiz::where('name', $name)->firstOrFail();
        $quizLimit = $quiz->quiz_limit;

        $questions = Question::where('quiz_id', $quiz->id)
            ->where('is_example', 1)
            ->inRandomOrder()
            ->limit($quizLimit)
            ->get();

        return view('/pages/admin/examInstruction', compact('quiz', 'questions'));
    }


    public function followedUser(Request $request)
    {
        $loggedInUser = Auth::user();
        $selectedOption = $request->input('duration', 'all-time');

        $users = User::where('type', 0)
            ->with([
                'results.quiz',
                'profile' => function ($query) {
                    $query->whereNotNull('follow_up');
                },
                'results' => function ($query) {
                    $query->latest('created_at')->select('user_id', 'created_at');
                }
            ])
            ->get();

        if ($request->ajax()) {
            $query = Result::selectRaw("
                users.id AS id,
                users.name AS name,
                profiles.follow_up AS follow_up,
                CONCAT(branches.city, ' - ', branches.location) AS branch_location,
                profiles.education AS education,
                profiles.applied_position AS applied_position,
                ROUND(AVG(results.score), 2) AS average_score,
                CASE 
                    WHEN profiles.able_to_work REGEXP '^[0-9]{2}-[0-9]{2}-[0-9]{4}$' 
                        THEN DATE_FORMAT(STR_TO_DATE(profiles.able_to_work, '%d-%m-%Y'), '%Y-%m-%d')
                    WHEN profiles.able_to_work REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}$' 
                        THEN DATE_FORMAT(STR_TO_DATE(profiles.able_to_work, '%Y-%m-%d'), '%Y-%m-%d')
                    ELSE NULL
                END AS able_to_work
            ")
                ->join('users', 'users.id', '=', 'results.user_id')
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->leftJoin('branches', 'branches.id', '=', 'profiles.branch_location')
                ->whereNotNull('profiles.follow_up');

            // Apply duration filter
            if ($selectedOption === 'today') {
                $query->whereDate('profiles.processed_at', Carbon::today());
            } elseif ($selectedOption === '7days') {
                $query->whereDate('profiles.processed_at', '>=', Carbon::today()->subDays(7));
            } elseif ($selectedOption === '30days') {
                $query->whereDate('profiles.processed_at', '>=', Carbon::today()->subDays(30));
            } elseif ($selectedOption === '60days') {
                $query->whereDate('profiles.processed_at', '>=', Carbon::today()->subDays(60));
            } elseif ($selectedOption === '90days') {
                $query->whereDate('profiles.processed_at', '>=', Carbon::today()->subDays(90));
            }

            $data = $query
                ->groupBy(
                    'users.id',
                    'users.name',
                    'profiles.follow_up',
                    'branches.location',
                    'profiles.education',
                    'profiles.applied_position',
                    'profiles.able_to_work'
                )
                ->get();

            $userQuizCounts = Result::select('user_id', DB::raw('COUNT(DISTINCT quiz_id) as quizzes_taken'))
                ->groupBy('user_id')
                ->get()
                ->keyBy('user_id');

            $totalQuizzes = Quiz::count();

            $filteredData = $data->filter(function ($item) use ($totalQuizzes, $userQuizCounts) {
                $progress = isset($userQuizCounts[$item->id])
                    ? round($userQuizCounts[$item->id]->quizzes_taken / $totalQuizzes * 100, 2)
                    : 0;
                return $progress >= 100;
            });

            $filteredData->transform(function ($item) {
                $item->education = str_replace(',', '', $item->education);
                return $item;
            });

            return DataTables::of($filteredData)
                ->addColumn('row_number', function ($row) use ($filteredData) {
                    return $filteredData->search($row) + 1;
                })
                ->addColumn('action', function ($row) {})
                ->addColumn('progress', function ($row) use ($totalQuizzes, $userQuizCounts) {
                    $progress = isset($userQuizCounts[$row->id])
                        ? round($userQuizCounts[$row->id]->quizzes_taken / $totalQuizzes * 100, 2)
                        : 0;
                    return $progress . '%';
                })
                ->addColumn('latest_result_created_at', function ($row) use ($users) {
                    $user = $users->firstWhere('id', $row->id);
                    return $user && $user->results->first()
                        ? $user->results->first()->created_at->format('Y-m-d')
                        : '-';
                })
                ->rawColumns(['action', 'progress'])
                ->make(true);
        }

        return view('/pages/admin/followedUser', compact('users', 'selectedOption'));
    }



    public function userFollowingCount(Request $request)
    {
        $loggedInUser = Auth::user();
        $selectedOption = $request->input('duration', 'all-time');

        $users = User::where('type', 0)
            ->with([
                'results.quiz',
                'profile' => function ($query) use ($loggedInUser) {
                    $query->where('follow_up', $loggedInUser->name);
                },
                'results' => function ($query) {
                    $query->latest('created_at')->select('user_id', 'created_at');
                }
            ])
            ->get();

        if ($request->ajax()) {
            $query = Result::selectRaw("
                users.id AS id,
                users.name AS name,
                profiles.follow_up AS follow_up,
                CONCAT(branches.city, ' - ', branches.location) AS branch_location,
                profiles.education AS education,
                profiles.applied_position AS applied_position,
                ROUND(AVG(results.score), 2) AS average_score,
                CASE 
                    WHEN profiles.able_to_work REGEXP '^[0-9]{2}-[0-9]{2}-[0-9]{4}$' 
                        THEN DATE_FORMAT(STR_TO_DATE(profiles.able_to_work, '%d-%m-%Y'), '%Y-%m-%d')
                    WHEN profiles.able_to_work REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}$' 
                        THEN DATE_FORMAT(STR_TO_DATE(profiles.able_to_work, '%Y-%m-%d'), '%Y-%m-%d')
                    ELSE NULL
                END AS able_to_work
            ")
                ->join('users', 'users.id', '=', 'results.user_id')
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->leftJoin('branches', 'branches.id', '=', 'profiles.branch_location');

            $query->where('profiles.follow_up', $loggedInUser->name);

            if ($selectedOption === 'today') {
                $query->whereDate('profiles.processed_at', Carbon::today());
            } elseif ($selectedOption === '7days') {
                $query->whereDate('profiles.processed_at', '>=', Carbon::today()->subDays(7));
            } elseif ($selectedOption === '30days') {
                $query->whereDate('profiles.processed_at', '>=', Carbon::today()->subDays(30));
            } elseif ($selectedOption === '60days') {
                $query->whereDate('profiles.processed_at', '>=', Carbon::today()->subDays(60));
            } elseif ($selectedOption === '90days') {
                $query->whereDate('profiles.processed_at', '>=', Carbon::today()->subDays(90));
            }

            $data = $query
                ->groupBy(
                    'users.id',
                    'users.name',
                    'profiles.follow_up',
                    'branches.location',
                    'profiles.education',
                    'profiles.applied_position',
                    'profiles.able_to_work'
                )
                ->get();

            $userQuizCounts = Result::select('user_id', DB::raw('COUNT(DISTINCT quiz_id) as quizzes_taken'))
                ->groupBy('user_id')
                ->get()
                ->keyBy('user_id');

            $totalQuizzes = Quiz::count();

            $filteredData = $data->filter(function ($item) use ($totalQuizzes, $userQuizCounts) {
                $progress = isset($userQuizCounts[$item->id])
                    ? round($userQuizCounts[$item->id]->quizzes_taken / $totalQuizzes * 100, 2)
                    : 0;
                return $progress >= 100;
            });

            $filteredData->transform(function ($item) {
                $item->education = str_replace(',', '', $item->education);
                return $item;
            });

            return DataTables::of($filteredData)
                ->addColumn('row_number', function ($row) use ($filteredData) {
                    return $filteredData->search($row) + 1;
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route("user.results.detail", $row->id) . '" class="btn btn-primary btn-sm"><i class="fas fa-info-circle"></i></a>';
                })
                ->addColumn('progress', function ($row) use ($totalQuizzes, $userQuizCounts) {
                    $progress = isset($userQuizCounts[$row->id])
                        ? round($userQuizCounts[$row->id]->quizzes_taken / $totalQuizzes * 100, 2)
                        : 0;
                    return $progress . '%';
                })
                ->addColumn('latest_result_created_at', function ($row) use ($users) {
                    $user = $users->firstWhere('id', $row->id);
                    return $user && $user->results->first()
                        ? $user->results->first()->created_at->format('Y-m-d')
                        : '-';
                })
                ->rawColumns(['action', 'progress'])
                ->make(true);
        }

        return view('/pages/admin/userYouFollow', compact('users', 'selectedOption', 'loggedInUser'));
    }


    public function candidate(Request $request)
    {
        $selectedOption = $request->input('duration', 'all-time');

        // Read the average score from avgScore.txt
        $avgScoreFilePath = public_path('/txt/avgScore.txt');
        $avgScore = (float) File::get($avgScoreFilePath);

        $users = User::where('type', 0)
            ->with([
                'results.quiz',
                'profile' => function ($query) {
                    $query->where(function ($q) {
                        $q->whereNull('follow_up')->orWhere('follow_up', '');
                    });
                },
                'results' => function ($query) {
                    $query->latest('created_at')->select('user_id', 'created_at');
                }
            ])
            ->get();

        if ($request->ajax()) {
            $data = Result::selectRaw("
                users.id AS id,
                users.name AS name,
                CONCAT(branches.city, ' - ', branches.location) AS branch_location,
                profiles.education AS education,
                profiles.applied_position AS applied_position,
                ROUND(AVG(results.score), 2) AS average_score,
                CASE 
                    WHEN profiles.able_to_work REGEXP '^[0-9]{2}-[0-9]{2}-[0-9]{4}$' 
                        THEN DATE_FORMAT(STR_TO_DATE(profiles.able_to_work, '%d-%m-%Y'), '%Y-%m-%d')
                    WHEN profiles.able_to_work REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}$' 
                        THEN DATE_FORMAT(STR_TO_DATE(profiles.able_to_work, '%Y-%m-%d'), '%Y-%m-%d')
                    ELSE NULL
                END AS able_to_work
            ")
                ->join('users', 'users.id', '=', 'results.user_id')
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->leftJoin('branches', 'branches.id', '=', 'profiles.branch_location')
                ->where(function ($query) {
                    $query->whereNull('profiles.follow_up')->orWhere('profiles.follow_up', '');
                });

            // Optional: If you want to enable time filtering later
            // if ($selectedOption === 'today') {
            //     $query->whereDate('results.created_at', Carbon::today());
            // } elseif ($selectedOption === '7days') {
            //     $query->whereDate('results.created_at', '>=', Carbon::today()->subDays(7));
            // }

            $data = $data->groupBy(
                'users.id',
                'users.name',
                'branches.location',
                'profiles.education',
                'profiles.applied_position',
                'profiles.able_to_work'
            )->get();

            $userQuizCounts = Result::select('user_id', DB::raw('COUNT(DISTINCT quiz_id) as quizzes_taken'))
                ->groupBy('user_id')
                ->get()
                ->keyBy('user_id');

            $totalQuizzes = Quiz::count();

            $filteredData = $data->filter(function ($item) use ($totalQuizzes, $userQuizCounts, $avgScore) {
                $progress = isset($userQuizCounts[$item->id])
                    ? round($userQuizCounts[$item->id]->quizzes_taken / $totalQuizzes * 100, 2)
                    : 0;
                return $progress >= 100 && $item->average_score >= $avgScore;
            });

            $filteredData->transform(function ($item) {
                $item->education = str_replace(',', '', $item->education);
                return $item;
            });

            return DataTables::of($filteredData)
                ->addColumn('row_number', function ($row) use ($filteredData) {
                    return $filteredData->search($row) + 1;
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route("user.results.detail", $row->id) . '" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>';
                })
                ->addColumn('progress', function ($row) use ($totalQuizzes, $userQuizCounts) {
                    $progress = isset($userQuizCounts[$row->id])
                        ? round($userQuizCounts[$row->id]->quizzes_taken / $totalQuizzes * 100, 2)
                        : 0;
                    return $progress . '%';
                })
                ->addColumn('latest_result_created_at', function ($row) use ($users) {
                    $user = $users->firstWhere('id', $row->id);
                    return $user && $user->results->first()
                        ? $user->results->first()->created_at->format('Y-m-d')
                        : '-';
                })
                ->rawColumns(['action', 'progress'])
                ->make(true);
        }

        return view('/pages/admin/candidate', compact('users', 'selectedOption'));
    }



    public function adminChangePassword()
    {
        return view('/pages/admin/change_password');
    }


    public function adminUpdatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        // $user = Auth::user();
        // $currentPassword = $user->password;

        // if (Hash::check($request->old_password, $currentPassword)) {
        //     $user->update([
        //         'password' => Hash::make($request->new_password),
        //     ]);

        //     return redirect()->route('admin.changePassword')->with('success', 'Password changed successfully.');
        // }
        // Retrieve users based on the ID that is currently logged in
        $user = User::find(Auth::id());

        if (!$user) {
            return redirect()->route('admin.changePassword')->with('error', 'User tidak ditemukan');
        }

        // Check if the old password matches
        if (Hash::check($request->old_password, $user->password)) {
            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            return redirect()->route('admin.changePassword')->with('success', 'Kata sandi berhasil diubah.');
        }
        return redirect()->route('admin.changePassword')->with('error', 'Kata sandi lama salah.');
    }


    public function adminControl()
    {
        $editableContent = file_get_contents(public_path('/txt/pengumuman.txt'));

        // Get the count of records
        $recordCount = DB::table('answers')->count();

        // Get the total size of records in bytes
        $totalSizeBytes = DB::table('answers')->sum(DB::raw('LENGTH(id) + LENGTH(question_id) + LENGTH(user_id) + LENGTH(answer) + LENGTH(created_at) + LENGTH(updated_at)')); // Replace column1, column2, ...

        // Convert total size to human-readable format
        $totalSizeHumanReadable = $this->formatBytes($totalSizeBytes);

        return view('/pages/admin/controlPanel', compact('recordCount', 'totalSizeHumanReadable', 'editableContent'));
    }

    // Helper function to format bytes into human-readable format
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public function updateAnnouncement(Request $request)
    {
        $content = $request->input('content');
        file_put_contents(public_path('/txt/pengumuman.txt'), $content);

        return redirect()->route('admin.control')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function resetAnswers()
    {
        // Truncate the table to remove all data
        DB::table('answers')->truncate();

        // Reset the auto-increment value to 1
        Schema::disableForeignKeyConstraints();
        DB::table('answers')->delete();
        DB::statement('ALTER TABLE answers AUTO_INCREMENT = ' . (1));
        Schema::enableForeignKeyConstraints();

        return redirect()->route('admin.control')->with('success', 'Tabel jawaban telah diatur ulang.');
    }


    public function adminUpload()
    {
        $files = Storage::files('public/images');

        return view('/pages/admin/upload', compact('files'));
    }


    public function adminUploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_name' => 'required|string|max:255', // Add validation for the image name field
        ]);

        $imageName = Str::slug($request->input('image_name'), '-') . '.' . $request->file('image')->getClientOriginalExtension();

        // Move the uploaded image to a public folder (e.g., storage/app/public/images)
        $path = $request->file('image')->storeAs('public/images', $imageName);

        // You can save the image path in the database if you need to track it later

        return back()->with('success', 'Gambar berhasil diunggah!');
    }


    public function imageDelete(Request $request)
    {
        $filename = $request->input('filename');
        $path = 'public/images/' . $filename;

        if (Storage::exists($path)) {
            Storage::delete($path);
            return response()->json(['message' => 'Gambar berhasil dihapus.']);
        }

        return response()->json(['message' => 'Gambar tidak ditemukan.'], 404);
    }


    public function downloadMultipleChoiceXlsx()
    {
        $exampleXlsxPath = public_path('files/Format_bulk_upload_pertanyaan_pilihan_ganda.xlsx'); // Update the path

        return response()->download($exampleXlsxPath, 'Format_bulk_upload_pertanyaan_pilihan_ganda.xlsx');
    }


    public function downloadEssayXlsx()
    {
        $exampleXlsxPath = public_path('files/Format_bulk_upload_pertayaan_essay.xlsx'); // Update the path

        return response()->download($exampleXlsxPath, 'Format_bulk_upload_pertayaan_essay.xlsx');
    }


    public function manageScore(Request $request)
    {
        // Display the form
        $avgScoreFilePath = public_path('/txt/avgScore.txt');
        $baikSekaliFilePath = public_path('/txt/baikSekali.txt');
        $baik_baFilePath = public_path('/txt/baik_ba.txt');
        $baik_bbFilePath = public_path('/txt/baik_bb.txt');
        $cukup_baFilePath = public_path('/txt/cukup_ba.txt');
        $cukup_bbFilePath = public_path('/txt/cukup_bb.txt');

        $avgScore = File::get($avgScoreFilePath);
        $baikSekali = File::get($baikSekaliFilePath);
        $baik_ba = File::get($baik_baFilePath);
        $baik_bb = File::get($baik_bbFilePath);
        $cukup_ba = File::get($cukup_baFilePath);
        $cukup_bb = File::get($cukup_bbFilePath);

        return view('/pages/admin/manageScore', [
            'avgScore' => $avgScore,
            'baikSekali' => $baikSekali,
            'baik_ba' => $baik_ba,
            'baik_bb' => $baik_bb,
            'cukup_ba' => $cukup_ba,
            'cukup_bb' => $cukup_bb
        ]);
    }


    public function updateAvgScore(Request $request)
    {
        $newAvgScore = $request->input('new_avg_score');

        $avgScoreFilePath = public_path('/txt/avgScore.txt');
        File::put($avgScoreFilePath, $newAvgScore);

        return redirect()->route('admin.manageScore')->with('success', 'Skor rata-rata berhasil diperbarui!');
    }

    public function updateBaikSekali(Request $request)
    {
        $newBaikSekali = $request->input('new_baik_sekali');

        $baikSekaliFilePath = public_path('/txt/baikSekali.txt');
        File::put($baikSekaliFilePath, $newBaikSekali);

        return redirect()->route('admin.manageScore')->with('success', 'Skor minimum yang sangat baik berhasil diperbarui!');
    }

    public function updateBaik(Request $request)
    {
        $newBaik_ba = $request->input('new_baik_ba');
        $newBaik_bb = $request->input('new_baik_bb');

        $baik_baFilePath = public_path('/txt/baik_ba.txt');
        File::put($baik_baFilePath, $newBaik_ba);

        $baik_bbFilePath = public_path('/txt/baik_bb.txt');
        File::put($baik_bbFilePath, $newBaik_bb);

        return redirect()->route('admin.manageScore')->with('success', 'Skor minimum yang baik berhasil diperbarui!');
    }

    public function updateCukup(Request $request)
    {
        $newCukup_ba = $request->input('new_cukup_ba');
        $newCukup_bb = $request->input('new_cukup_bb');

        $cukup_baFilePath = public_path('/txt/cukup_ba.txt');
        File::put($cukup_baFilePath, $newCukup_ba);

        $cukup_bbFilePath = public_path('/txt/cukup_bb.txt');
        File::put($cukup_bbFilePath, $newCukup_bb);

        return redirect()->route('admin.manageScore')->with('success', 'Skor minimum yang diperlukan berhasil diperbarui!');
    }

    public function toggleRegistration()
    {
        $filePath = public_path('txt/registration.txt'); // Update the file path

        // Read the current content of the file
        $currentStatus = file_get_contents($filePath);

        // Toggle the status (1 to 0, 0 to 1)
        $newStatus = $currentStatus === "1" ? "0" : "1";

        // Write the new status back to the file
        file_put_contents($filePath, $newStatus);

        // Update .env file's REGISTRATION_ENABLED setting
        $envFilePath = base_path('.env');
        file_put_contents($envFilePath, str_replace(
            'REGISTRATION_ENABLED=' . ($currentStatus ? 'true' : 'false'),
            'REGISTRATION_ENABLED=' . ($newStatus === "1" ? 'true' : 'false'),
            file_get_contents($envFilePath)
        ));

        return redirect()->route('admin.control')->with('success', 'Status pendaftaran berhasil diperbarui!.');
    }
}