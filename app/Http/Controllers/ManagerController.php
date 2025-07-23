<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Quiz;
use App\Models\User;
use App\Models\Result;
use App\Models\Profile;
use App\Models\Question;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class ManagerController extends Controller
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
    public function managerHome()
    {
        $selectedOption = request()->input('duration', 'all');
        $quizCount = Quiz::count();
        $questionCount = Question::where('is_example', 0)->count();
    
        $userCounts = $this->getUserCountsM($selectedOption);
        $userFollowingCountM = $this->getUserFollowingCountM($selectedOption, $quizCount);
        $followingCount = $this->getFollowingCountM($selectedOption, $quizCount);
        $usersData = $this->getUsersDataM($selectedOption, $quizCount);

        $alreadyFollowingCount = $this->getAlreadyFollowingCountM($selectedOption, $quizCount);
    
        $user = Auth::user();

        $usersWithAverageScore = User::whereIn('id', $usersData->keys())
            ->whereHas('profile', function ($query) use ($user) {
                if (!is_null($user->branch_id)) {
                    $query->where('branch_location', $user->branch_id);
                }
            })
            ->count();
                $passedNotFollowingCount = $usersWithAverageScore - $alreadyFollowingCount;
    
        $chartData = $this->getChartDataM($userCounts);
    
        $labels = $chartData['labels'];
        $counts = $chartData['counts'];
    
        // Update the query for totalUserCount based on selectedOption
        $totalUserCount = $this->getTotalUserCountM($selectedOption);
    
        return view('managerHome', compact(
            'questionCount',
            'quizCount',
            'totalUserCount',
            'followingCount',
            'passedNotFollowingCount',
            'usersWithAverageScore',
            'labels',
            'counts',
            'selectedOption',
            'userFollowingCountM'
        ));
    }
    
    private function getTotalUserCountM($selectedOption)
    {
        $user = Auth::user();
        $query = Profile::join('users', 'users.id', '=', 'profiles.user_id')
                        ->where('users.type', 0);
        // Jika user bukan HO, filter berdasarkan branch_location
        if (!is_null($user->branch_id)) {
            $query->where('profiles.branch_location', $user->branch_id);
        }
    
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

        return $query->count();
    }
    
    private function getUserCountsM($selectedOption)
    {
        $user = Auth::user();
        $query = Profile::join('users', 'users.id', '=', 'profiles.user_id')
                         ->where('users.type', 0);
        // Jika user bukan HO, filter berdasarkan branch_location
        if (!is_null($user->branch_id)) {
            $query->where('profiles.branch_location', $user->branch_id);
        }
        $dateColumn = 'profiles.created_at';
        if ($selectedOption === 'today') {
            $query->whereDate($dateColumn, '>=', now()->subDays(1));
        } elseif ($selectedOption === '7days') {
            $query->whereDate($dateColumn, '>=', now()->subDays(7));
        } elseif ($selectedOption === '30days') {
            $query->whereDate($dateColumn, '>=', now()->subDays(30));
        } elseif ($selectedOption === '60days') {
            $query->whereDate($dateColumn, '>=', now()->subDays(60));
        } elseif ($selectedOption === '90days') {
            $query->whereDate($dateColumn, '>=', now()->subDays(90));
        }
    
        return $query->select(
            DB::raw('DATE(' . $dateColumn . ') AS date'),
            DB::raw('COUNT(*) AS count')
        )->groupBy('date')->get();
    }
    
    private function getFollowingCountM($selectedOption, $quizCount)
    {
        $user = Auth::user();
        $query = Profile::whereHas('user.results', function ($query) use ($selectedOption) {
        $dateColumn = 'results.processed_at';
    
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

        // Filter berdasarkan branch_location jika user bukan HO
        if (!is_null($user->branch_id)) {
            $query->where('branch_location', $user->branch_id);
        }
    
        return $query->where(function ($query) {
            $query->where('follow_up', Auth::user()->name)
                ->orWhereNotNull('follow_up');
        })->count();
    }

    private function getUserFollowingCountM($selectedOption, $quizCount)
    {
        $user = Auth::user();
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
    
        // Filter berdasarkan branch_location jika user bukan HO
        if (!is_null($user->branch_id)) {
            $query->where('profiles.branch_location', $user->branch_id);
        }
        return $query->where(function ($query) {
            $query->where('follow_up', Auth::user()->name);
        })->count();
    }

     private function getAlreadyFollowingCountM($selectedOption, $quizCount)
    {
        $user = Auth::user();
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
    
            // Filter berdasarkan branch_location jika user bukan HO
        if (!is_null($user->branch_id)) {
            $query->where('profiles.branch_location', $user->branch_id);
        }
        return $query->where(function ($query) {
            $query->where('follow_up', Auth::user()->name)
                  ->orWhereNotNull('follow_up');
        })
        ->count();
    }
    
    private function getUsersDataM($selectedOption, $quizCount)
    {
        $query = Result::select('user_id', DB::raw('AVG(score) as average_score'))
            ->join('users', 'results.user_id', '=', 'users.id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(results.id) = ?', [$quizCount])
            ->havingRaw('AVG(score) >= 50');
    
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

    private function getChartDataM($userCounts)
    {
        $labels = [];
        $counts = [];
    
        foreach ($userCounts as $userCount) {
            $labels[] = date('M d', strtotime($userCount->date));
            $counts[] = $userCount->count;
        }
    
        return compact('labels', 'counts');
    }


    public function mFollowedUser(Request $request)
    {
        $loggedInUser = Auth::user(); // Ambil user yang sedang login
        $selectedOption = $request->input('duration', 'all-time');
    
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
    
        // Filter berdasarkan branch jika user bukan HO
        if (!is_null($loggedInUser->branch_id)) {
            $query->where(function ($q) use ($loggedInUser) {
                $q->where('profiles.branch_location', $loggedInUser->branch_id)
                ->orWhereNull('profiles.branch_location');
            });
        }
    
        // **Filter Berdasarkan Durasi**
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
            'branches.location',
            'profiles.branch_location', 
            'profiles.education', 
            'profiles.applied_position', 
            'profiles.able_to_work', 
            'profiles.follow_up'
        )
        
        ->get();
    
        // **Ambil jumlah kuis yang dikerjakan setiap user**
        $userQuizCounts = Result::select('user_id', DB::raw('COUNT(DISTINCT quiz_id) as quizzes_taken'))
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');
    
        $totalQuizzes = Quiz::count(); // Total kuis yang tersedia
    
        // **Filter hanya user dengan progress 100% dan skor >= 50**
        $filteredData = $data->filter(function ($item) use ($totalQuizzes, $userQuizCounts) {
            $progress = isset($userQuizCounts[$item->id]) 
                ? round($userQuizCounts[$item->id]->quizzes_taken / $totalQuizzes * 100, 2) 
                : 0;

                Log::info("Filtering user ID: {$item->id}, Progress: $progress%, Score: {$item->average_score}");
            return $progress >= 100 && $item->average_score >= 50;
        });
    
        // **Hapus koma dari education**
        $filteredData->transform(function ($item) {
            $item->education = str_replace(',', '', $item->education);
            return $item;
        });
    
        if ($request->ajax()) {
            return DataTables::of($filteredData)
                ->addColumn('row_number', function ($row) use ($filteredData) {
                    return $filteredData->search($row) + 1;
                })
                ->addColumn('progress', function ($row) use ($totalQuizzes, $userQuizCounts) {
                    $progress = isset($userQuizCounts[$row->id]) 
                        ? round($userQuizCounts[$row->id]->quizzes_taken / $totalQuizzes * 100, 2) 
                        : 0;
                    return $progress . '%';
                })
                ->addColumn('latest_result_created_at', function ($row) {
                    // **Cegah error jika user belum punya results**
                    $latestResult = Result::where('user_id', $row->id)->latest('created_at')->first();
                    return $latestResult ? $latestResult->created_at->format('Y-m-d') : '-';
                })
                ->rawColumns(['progress'])
                ->make(true);
        }
    
        return view('/pages/manager/followedUser', compact('selectedOption', 'loggedInUser'));
    }
    


    public function mUserFollowingCount(Request $request)
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
    
            // Filter berdasarkan follow_up dan durasi
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
                return $progress >= 100 && $item->average_score >= 50;
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
                    return '<a href="' . route("Muser.results.detail", $row->id) . '" class="btn btn-primary btn-sm"><i class="fas fa-info"></i></a>';
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
    
        return view('/pages/manager/userYouFollow', compact('users', 'selectedOption'));
    }
    


    public function managerProfile()
    {
        return view('/pages/manager/managerProfile');
    }

    
    public function managerUsers(Request $request)
    {
        $selectedOption = $request->input('duration', 'all');
        $branchId = Auth::user()->branch_id;
        $query = Profile::with('branch')
                        ->join('users', 'users.id', '=', 'profiles.user_id')
                        ->select('profiles.*', 'users.name', 'users.email', 'users.branch_id')
                        ->where('users.type', 0)
                        ->latest();

        // Jika pengguna **bukan** HO (memiliki branch_id), filter berdasarkan branch_location
        if (!is_null($branchId)) {
            $query->where('profiles.branch_location', $branchId);
        }
        // Filter berdasarkan durasi
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
        
        if ($request->ajax()) {
            $data = $query->get();
            $data = $data->reverse()->values()->map(function ($profile, $index) {
                $profile->row_number = $index + 1;
                return $profile;
            });
    
            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    return '<a href="/admin/edit/' . $row->user_id . '" class="btn btn-sm btn-primary">Edit</a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        
        return view('/pages/manager/userDataTables', compact('selectedOption'));
    }

    public function managerShowUsers(User $user)
    {
        $profile = $user->profile; // Use the relationship to fetch the profile
    
        if ($profile) {
            $profile->education = str_replace(',', '', $profile->education);
        }
        
        return view('/pages/manager/managerUserDetail', compact('user', 'profile'));
    }

    public function manageExam(Request $request)
    {
        if ($request->ajax()) {
            $data = Quiz::select("*")
                ->from("quizzes")
                ->latest()
                ->get();

            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $editBtn = '<a href="'. route('manage.exam.edit', $row->id) .'" class="btn btn-primary">Edit</a>';
                    $deleteBtn = '<a href="" class="btn btn-danger">Delete</a>';

                    // '. route('manage.exam.edit', $row->id) .'
                    // '. route('manage.exam.delete', $row->id) .'

                    return $editBtn . ' ' . $deleteBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        
        return view('/pages/admin/manageExam');
    }


    public function followUp(User $user)
    {
        $followerName = Auth::user()->name;
        $profile = $user->profile;
    
        // Check if it has been followed up
        if ($profile->follow_up) {
            return redirect()->back()->with('error', "Peserta ini telah ditindaklanjuti oleh {$profile->follow_up}.");
        }
    
        // Take the minimum average value of the file
        $avgScoreFilePath = public_path('/txt/avgScore.txt');
        $avgScore = (float) File::get($avgScoreFilePath);
        $quizCount = Quiz::count();
    
        // Retrieve participant result statistics
        $resultStats = Result::where('user_id', $user->id)
            ->selectRaw('AVG(score) as average_score, COUNT(DISTINCT quiz_id) as quiz_done')
            ->first();
    
        // Check eligibility for follow-up
        if (!$resultStats || $resultStats->quiz_done < $quizCount || $resultStats->average_score < $avgScore) {
            return redirect()->back()->with('error', 'Peserta ini tidak memenuhi kriteria kelulusan dan tidak dapat ditindaklanjuti.');
        }
    
        // Do a follow-up
        $profile->follow_up = $followerName;
        $profile->processed_at = now();
        $profile->save();
    
        return redirect()->back()->with('success', 'Berhasil menindaklanjuti pengguna');
    }
    


    public function unfollow(User $user)
    {
        $followerName = null;
        $profile = $user->profile;
    
        $profile->follow_up = $followerName;
        $profile->save();
    
        return redirect()->back()->with('success', 'Berhasil berhenti mengikuti pengguna.');
    }


    public function managerPreviewQuiz($name)
    {
        $quiz = Quiz::where('name', $name)->firstOrFail();
        $quizLimit = $quiz->quiz_limit;

        $questions = Question::where('quiz_id', $quiz->id)
        ->where('is_example', 0)
        ->inRandomOrder()
        ->limit($quizLimit)
        ->get();
        
        return view('/pages/manager/previewQuiz', compact('quiz', 'questions'));
    }

    
    public function MinstructionQuiz($name)
    {
        $quiz = Quiz::where('name', $name)->firstOrFail();
        $quizLimit = $quiz->quiz_limit;

        $questions = Question::where('quiz_id', $quiz->id)
        ->where('is_example', 1)
        ->inRandomOrder()
        ->limit($quizLimit)
        ->get();
    
        return view('/pages/manager/examInstruction', compact('quiz', 'questions'));
    }


    public function MuserResults(Request $request)
    {
        $users = User::where('type', 0)->with(['results.quiz', 'profile'])->get();
        $totalQuizzes = Quiz::count();
    
        $branchId = Auth::user()->branch_id;

        if ($request->ajax()) {
            $query = Result::selectRaw('users.id AS id, users.name AS name, CONCAT(branches.city, " - ", branches.location) AS branch_location, profiles.education AS education, profiles.applied_position AS applied_position, ROUND(AVG(results.score), 2) AS average_score, COUNT(DISTINCT results.quiz_id) AS quizzes_taken, DATE_FORMAT(MAX(results.created_at), "%Y-%m-%d") as created_at_formatted')
                ->join('users', 'users.id', '=', 'results.user_id')
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->leftJoin('branches', 'branches.id', '=', 'profiles.branch_location');
    
            // Filter berdasarkan branch jika sub admin
            if (!is_null($branchId)) {
                $query->where('profiles.branch_location', $branchId);
            }
    
            $data = $query->groupBy('users.id', 'users.name', 'branches.location', 'profiles.education', 'profiles.applied_position')
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
    
        return view('/pages/manager/result', compact('users'));
    }
    
    
    public function MuserResultsDetail(User $user)
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
    
        return view('/pages/manager/resultDetail', compact('user', 'profile', 'labels', 'scores'));
    }


    public function mCandidate(Request $request)
    {
        $selectedOption = $request->input('duration', 'all-time');
    
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

            $user = Auth::user();
            if (!is_null($user->branch_id)) {
                $data = $data->where('profiles.branch_location', $user->branch_id);
            }
            // Uncomment if you want to apply duration filter
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
    
            $filteredData = $data->filter(function ($item) use ($totalQuizzes, $userQuizCounts) {
                $progress = isset($userQuizCounts[$item->id])
                    ? round($userQuizCounts[$item->id]->quizzes_taken / $totalQuizzes * 100, 2)
                    : 0;
                return $progress >= 100 && $item->average_score >= 50;
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
    
        return view('/pages/manager/candidate', compact('users', 'selectedOption'));
    }
    


    public function managerChangePassword()
    {
        return view('/pages/manager/change_password');
    }

    
    public function managerUpdatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();
        $currentPassword = $user->password;

        if (Hash::check($request->old_password, $currentPassword)) {
            $user()->update([
                'password' => Hash::make($request->new_password),
            ]);

            return redirect()->route('manager.changePassword')->with('success', 'Password berhasil diubah.');
        }

        return redirect()->route('manager.changePassword')->with('error', 'Password lama salah.');
    }
}
