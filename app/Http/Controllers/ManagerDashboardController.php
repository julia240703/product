<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\User;
use App\Models\Branch;
use App\Models\Result;
use App\Models\Profile;
use App\Models\JobPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class ManagerDashboardController extends Controller
{
    //
    public function managerBranch(Request $request)
    {
        $selectedOption = $request->input('duration', 'all');
        $selectedJobPosition = $request->input('job_position', '');
    
        $user = Auth::user();
        $isSubAdmin = !is_null($user->branch_id);
        $branchCount = Branch::count();
        $jobPositionCount = JobPosition::count();
        $jobPositions = JobPosition::all();
    
        $userCountJakarta = $userCountTangerang = $userCountLuarKota = 0;
        $userCountsJakarta = $userCountsTangerang = $userCountsLuarKota = [];
    
        if ($isSubAdmin) {
            // Sub-admin hanya boleh melihat data cabangnya sendiri
            $branch = Branch::find($user->branch_id);
            $city = $branch->city ?? '';
    
            if (str_contains($city, 'Jakarta')) {
                [$userCountJakarta, $userCountsJakarta] = $this->mGetUserCounts(null, $selectedJobPosition, $selectedOption, $user->branch_id);
            } elseif (str_contains($city, 'Tangerang')) {
                [$userCountTangerang, $userCountsTangerang] = $this->mGetUserCounts(null, $selectedJobPosition, $selectedOption, $user->branch_id);
            } else {
                [$userCountLuarKota, $userCountsLuarKota] = $this->mGetUserCounts(null, $selectedJobPosition, $selectedOption, $user->branch_id);
            }            
        } else {
            // HO bisa lihat semuanya
            [$userCountJakarta, $userCountsJakarta] = $this->mGetUserCounts('Jakarta', $selectedJobPosition, $selectedOption);
            [$userCountTangerang, $userCountsTangerang] = $this->mGetUserCounts('Tangerang', $selectedJobPosition, $selectedOption);
            [$userCountLuarKota, $userCountsLuarKota] = $this->mGetUserCounts('Luar Kota', $selectedJobPosition, $selectedOption);
        }
    
        return view('/pages/manager/dashboard/branchDashboard', compact(
            'userCountJakarta',
            'userCountTangerang',
            'userCountLuarKota',
            'userCountsJakarta',
            'userCountsTangerang',
            'userCountsLuarKota',
            'branchCount',
            'jobPositionCount',
            'selectedOption',
            'selectedJobPosition',
            'jobPositions'
        ));
    }
    
    private function mGetUserCounts($location, $selectedJobPosition, $selectedOption, $branchId = null)
    {
        $avgScoreFilePath = public_path('/txt/avgScore.txt');
        $avgScore = (float) File::get($avgScoreFilePath);
    
        $user = Auth::user();
        $isSubAdmin = !is_null($user->branch_id);
    
        $userCountQuery = Profile::join('branches', 'profiles.branch_location', '=', 'branches.id')
            ->join('users', 'users.id', '=', 'profiles.user_id')
            ->where(function ($query) {
                $query->whereNull('profiles.follow_up')
                      ->orWhere('profiles.follow_up', '');
            })
            
            ->whereHas('user.results', function ($query) use ($avgScore, $selectedOption) {
                $query->select('user_id')
                    ->groupBy('user_id')
                    ->havingRaw('AVG(score) >= ?', [$avgScore])
                    ->havingRaw('COUNT(DISTINCT quiz_id) = ?', [Quiz::count()]);
    
                if ($selectedOption === '1days') {
                    $query->whereDate('results.created_at', '>=', now()->subDays(1));
                } elseif ($selectedOption === '7days') {
                    $query->whereDate('results.created_at', '>=', now()->subDays(7));
                } elseif ($selectedOption === '30days') {
                    $query->whereDate('results.created_at', '>=', now()->subDays(30));
                } elseif ($selectedOption === '60days') {
                    $query->whereDate('results.created_at', '>=', now()->subDays(60));
                } elseif ($selectedOption === '90days') {
                    $query->whereDate('results.created_at', '>=', now()->subDays(90));
                }
            })
            ->when($isSubAdmin, function ($query) use ($branchId) {
                $query->where('profiles.branch_location', $branchId);
            }, function ($query) use ($location) {
                $query->where('branches.city', 'LIKE', "%$location%");
            })
            ->when(!empty($selectedJobPosition), function ($query) use ($selectedJobPosition) {
                $query->where('applied_position', $selectedJobPosition);
            });
    
        $userCount = $userCountQuery->count();
    
        $userCounts = (clone $userCountQuery)
            ->selectRaw($isSubAdmin
                ? 'branches.location as branch_label, DATE(profiles.created_at) as date'
                : 'branches.city as branch_label, DATE(profiles.created_at) as date')
            ->groupBy('branch_label', 'date')
            ->get()
            ->mapToGroups(function ($item) {
                return [$item->branch_label . ' - ' . $item->date => $item];
            })
            ->map(function ($groupedItems) {
                return $groupedItems->count();
            });
    
        return [$userCount, $userCounts];
    }


    public function managerJKTBranch(Request $request)
    {
        $selectedOption = $request->input('duration', 'all');
        $selectedJobPosition = $request->input('job_position', '');
        $user = Auth::user();
        $branchId = $user && $user->profile ? $user->profile->branch_location : null;
    
        $jobPositionCount = JobPosition::count();
        $jobPositions = JobPosition::all();
    
        $dataAHASS = new Collection();
        $dataWAHANA = new Collection();
    
        $branches = Branch::where('city', 'Jakarta')->get();
    
        foreach ($branches as $branch) {
            if (!is_null($branchId) && $branch->id !== $branchId) {
                continue;
            }
    
            $userCount = $this->mGetUserCountJKT($selectedOption, $selectedJobPosition, $branch->location, $branchId);
    
            if (strpos($branch->location, 'AHASS WAHANA') !== false) {
                $dataAHASS->push([
                    'branch' => $branch,
                    'userCount' => $userCount,
                ]);
            } else {
                $dataWAHANA->push([
                    'branch' => $branch,
                    'userCount' => $userCount,
                ]);
            }
        }
    
        // Sorting
        $dataAHASS = $dataAHASS->sortByDesc('branch.location');
        $dataWAHANA = $dataWAHANA->sortBy('branch.location');
        $mergedData = $dataWAHANA->merge($dataAHASS);
    
        return view('/pages/manager/dashboard/jakartaBranch', compact(
            'mergedData',
            'jobPositionCount',
            'selectedOption',
            'selectedJobPosition',
            'jobPositions'
        ));
    }
    

    private function mGetUserCountJKT($selectedOption, $selectedJobPosition, $branchLocation, $branchId = null)
    {
        $avgScoreFilePath = public_path('/txt/avgScore.txt');
        $avgScore = (float) File::get($avgScoreFilePath);
        $totalQuizzes = Quiz::count();
    
        $query = Result::join('users', 'results.user_id', '=', 'users.id')
            ->join('profiles', 'users.id', '=', 'profiles.user_id')
            ->join('branches', 'branches.id', '=', 'profiles.branch_location')
            ->whereNull('profiles.follow_up')
            ->where('branches.location', '=', $branchLocation)
            ->when(!is_null($branchId), function ($query) use ($branchId) {
                $query->where('profiles.branch_location', $branchId);
            })
            ->when(!empty($selectedJobPosition), function ($query) use ($selectedJobPosition) {
                $query->where('profiles.applied_position', $selectedJobPosition);
            })
            ->when($selectedOption, function ($query) use ($selectedOption) {
                if ($selectedOption === '1days') {
                    $query->whereDate('results.created_at', '>=', now()->subDays(1));
                } elseif ($selectedOption === '7days') {
                    $query->whereDate('results.created_at', '>=', now()->subDays(7));
                } elseif ($selectedOption === '30days') {
                    $query->whereDate('results.created_at', '>=', now()->subDays(30));
                } elseif ($selectedOption === '60days') {
                    $query->whereDate('results.created_at', '>=', now()->subDays(60));
                } elseif ($selectedOption === '90days') {
                    $query->whereDate('results.created_at', '>=', now()->subDays(90));
                }
            })
            ->groupBy('users.id', 'users.name', 'profiles.branch_location', 'profiles.education')
            ->havingRaw('COUNT(DISTINCT results.quiz_id) = ?', [$totalQuizzes]) // 100% progress
            ->havingRaw('AVG(results.score) >= ?', [$avgScore]) // min avg score
            ->selectRaw('users.id AS id, users.name AS name, profiles.branch_location AS branch_location, profiles.education AS education');
    
        return $query->count();
    }
    
    


    public function managerTGRBranch(Request $request)
    {
        $selectedOption = request()->input('duration', 'all');
        $selectedJobPosition = $request->input('job_position', '');
    
        $user = Auth::user();
        $branchId = $user && $user->profile ? $user->profile->branch_location : null;
    
        $branches = Branch::where('city', 'Tangerang')->get();
        $jobPositionCount = JobPosition::count();
    
        $dataAHASS = new Collection();
        $dataWAHANA = new Collection();
    
        foreach ($branches as $branch) {
            if (is_null($branchId) || $branchId === $branch->id) {
                $userCount = $this->mGetUserCountTGR($selectedOption, $selectedJobPosition, $branch->location, $branchId);
    
                if (strpos($branch->location, 'AHASS WAHANA') !== false) {
                    $dataAHASS->push([
                        'branch' => $branch,
                        'userCount' => $userCount,
                    ]);
                } else {
                    $dataWAHANA->push([
                        'branch' => $branch,
                        'userCount' => $userCount,
                    ]);
                }
            }
        }
    
        // Sorting
        $dataAHASS = $dataAHASS->sortBy('branch.location');
        $dataWAHANA = $dataWAHANA->sortBy('branch.location');
        $mergedData = $dataWAHANA->merge($dataAHASS);
    
        $jobPositions = JobPosition::all();
    
        return view('/pages/manager/dashboard/tangerangBranch', compact(
            'mergedData', 'dataAHASS', 'dataWAHANA', 'jobPositionCount', 'selectedOption', 'selectedJobPosition', 'jobPositions'
        ));
    }
    

    private function mGetUserCountTGR($selectedOption, $selectedJobPosition, $branchLocation, $branchId = null)
    {
        $userCount = Result::join('users', 'results.user_id', '=', 'users.id')
            ->join('profiles', 'users.id', '=', 'profiles.user_id')
            ->join('branches', 'branches.id', '=', 'profiles.branch_location')
            ->whereNull('profiles.follow_up')
            ->where(function ($query) use ($branchLocation, $branchId) {
                if (!is_null($branchId)) {
                    $query->where('profiles.branch_location', $branchId);
                } else {
                    $query->where('branches.location', '=', $branchLocation);
                }
            })
            ->when(!empty($selectedJobPosition), function ($query) use ($selectedJobPosition) {
                $query->where('profiles.applied_position', $selectedJobPosition);
            });
    
        // Filter by duration
        if ($selectedOption === '1days') {
            $userCount->whereDate('results.created_at', '>=', now()->subDays(1));
        } elseif ($selectedOption === '7days') {
            $userCount->whereDate('results.created_at', '>=', now()->subDays(7));
        } elseif ($selectedOption === '30days') {
            $userCount->whereDate('results.created_at', '>=', now()->subDays(30));
        } elseif ($selectedOption === '60days') {
            $userCount->whereDate('results.created_at', '>=', now()->subDays(60));
        } elseif ($selectedOption === '90days') {
            $userCount->whereDate('results.created_at', '>=', now()->subDays(90));
        }
    
        return $userCount
            ->groupBy('users.id', 'users.name', 'profiles.branch_location', 'profiles.education')
            ->havingRaw('COUNT(results.id) = ?', [Quiz::count()]) // 100% progress
            ->havingRaw('ROUND(AVG(results.score), 2) >= 50')    // Avg score >= 50
            ->selectRaw('users.id AS id, users.name AS name, profiles.branch_location AS branch_location, profiles.education AS education')
            ->count();
    }
    
    
    
    

    public function managerOthersBranch(Request $request)
    {
        $selectedOption = request()->input('duration', 'all');
        $selectedJobPosition = $request->input('job_position', '');
    
        $user = Auth::user();
        $branchId = $user && $user->profile ? $user->profile->branch_location : null;
    
        $branches = Branch::where('city', 'Luar Kota')->get();
        $jobPositionCount = JobPosition::count();
    
        $dataAHASS = new Collection();
        $dataWAHANA = new Collection();
    
        foreach ($branches as $branch) {
            if (is_null($branchId) || $branch->id === $branchId) {
                $userCount = $this->mGetUserCountOthers($selectedOption, $selectedJobPosition, $branch->location, $branchId);
    
                if (strpos($branch->location, 'AHASS WAHANA') !== false) {
                    $dataAHASS->push([
                        'branch' => $branch,
                        'userCount' => $userCount,
                    ]);
                } else {
                    $dataWAHANA->push([
                        'branch' => $branch,
                        'userCount' => $userCount,
                    ]);
                }
            }
        }
    
        $dataAHASS = $dataAHASS->sortBy('branch.location');
        $dataWAHANA = $dataWAHANA->sortBy('branch.location');
        $mergedData = $dataWAHANA->merge($dataAHASS);
    
        $jobPositions = JobPosition::all();
    
        return view('/pages/manager/dashboard/othersBranch', compact(
            'mergedData', 'jobPositionCount', 'selectedOption', 'selectedJobPosition', 'jobPositions'
        ));
    }
    

    private function mGetUserCountOthers($selectedOption, $selectedJobPosition, $branchLocation, $branchId = null)
    {
        $userCount = Result::join('users', 'results.user_id', '=', 'users.id')
            ->join('profiles', 'users.id', '=', 'profiles.user_id')
            ->join('branches', 'branches.id', '=', 'profiles.branch_location')
            ->where('branches.location', '=', $branchLocation)
            ->whereNull('profiles.follow_up')
            ->when(!is_null($branchId), function ($query) use ($branchId) {
                $query->where('profiles.branch_location', $branchId);
            })
            ->when(!empty($selectedJobPosition), function ($query) use ($selectedJobPosition) {
                $query->where('applied_position', $selectedJobPosition);
            });
    
        $userCount->whereHas('user', function ($query) use ($selectedOption) {
            if ($selectedOption === '1days') {
                $query->whereDate('results.created_at', '>=', now()->subDays(1));
            } elseif ($selectedOption === '7days') {
                $query->whereDate('results.created_at', '>=', now()->subDays(7));
            } elseif ($selectedOption === '30days') {
                $query->whereDate('results.created_at', '>=', now()->subDays(30));
            } elseif ($selectedOption === '60days') {
                $query->whereDate('results.created_at', '>=', now()->subDays(60));
            } elseif ($selectedOption === '90days') {
                $query->whereDate('results.created_at', '>=', now()->subDays(90));
            }
        });
    
        return $userCount
            ->groupBy('users.id', 'users.name', 'profiles.branch_location', 'profiles.education')
            ->havingRaw('COUNT(results.id) = ?', [Quiz::count()]) // Filter users with 100% progress
            ->havingRaw('ROUND(AVG(results.score), 2) >= 50') // Filter users with average score >= 50
            ->selectRaw('users.id AS id, users.name AS name, profiles.branch_location AS branch_location, profiles.education AS education')
            ->count();
    }
    


    public function managerBranchDetail(Request $request, $branch_location)
    {
        $branch = Branch::where('location', 'LIKE', '%' . $branch_location . '%')->firstOrFail();
    
        $avgScoreFilePath = public_path('/txt/avgScore.txt');
        $avgScore = (float) File::get($avgScoreFilePath);

        $selectedOption = $request->input('duration', 'all-time');
    
        $users = User::where('type', 0)
            ->with([
                'results.quiz',
                'profile' => function ($query) {
                    $query->whereNull('follow_up')->orWhere('follow_up', '');
                },
                'results' => function ($query) {
                    $query->latest('created_at')->select('user_id', 'created_at');
                }
            ])
            ->get();
    
        $totalQuizzes = Quiz::count();
    
        if ($request->ajax()) {
            $data = Result::selectRaw('users.id AS id, users.name AS name, profiles.branch_location AS branch_location, profiles.education AS education, profiles.applied_position AS applied_position, ROUND(AVG(results.score), 2) AS average_score, COUNT(DISTINCT results.quiz_id) AS quizzes_taken')
                ->join('users', 'users.id', '=', 'results.user_id')
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->join('branches', 'branches.id', '=', 'profiles.branch_location')
                ->where('branches.location', 'LIKE', '%' . $branch_location . '%')
                ->where(function ($query) {
                    $query->whereNull('profiles.follow_up')->orWhere('profiles.follow_up', '');
                })
                ->when($selectedOption, function ($query, $selectedOption) {
                    if ($selectedOption === 'today') {
                        $query->whereDate('results.created_at', Carbon::today());
                    } elseif ($selectedOption === '7days') {
                        $query->whereDate('results.created_at', '>=', Carbon::today()->subDays(7));
                    } elseif ($selectedOption === '30days') {
                        $query->whereDate('results.created_at', '>=', Carbon::today()->subDays(30));
                    } elseif ($selectedOption === '60days') {
                        $query->whereDate('results.created_at', '>=', Carbon::today()->subDays(60));
                    } elseif ($selectedOption === '90days') {
                        $query->whereDate('results.created_at', '>=', Carbon::today()->subDays(90));
                    }
                })
                ->groupBy('users.id', 'users.name', 'profiles.branch_location', 'profiles.education', 'profiles.applied_position')
                ->get();
    
            // Filter the data based on users who have 100% progress and average_score >= 50
            $filteredData = $data->filter(function ($item) use ($totalQuizzes) {
                $progress = round($item->quizzes_taken / $totalQuizzes * 100, 2);
                return $progress >= 100 && $item->average_score >= 50;
            });
    
            // Add the row number to the data
            $filteredData->transform(function ($item, $index) {
                $item->row_number = $index + 1;
                return $item;
            });
    
            // Remove commas from education field
            $filteredData->transform(function ($item) {
                $item->education = str_replace(',', '', $item->education);
                return $item;
            });
    
            return DataTables::of($filteredData)
                ->addColumn('action', function ($row) {
                    // Add your custom action code here
                })
                ->addColumn('progress', function ($row) use ($totalQuizzes) {
                    $progress = round($row->quizzes_taken / $totalQuizzes * 100, 2);
                    return $progress . '%';
                })
                ->addColumn('latest_result_created_at', function ($row) use ($users) {
                    // Find the user in the $users collection
                    $user = $users->firstWhere('id', $row->id);
    
                    if ($user && $user->results->isNotEmpty()) {
                        return $user->results->first()->created_at->format('Y-m-d');
                    }
                    return '-';
                })
                ->rawColumns(['action', 'progress'])
                ->make(true);
        }
    
        return view('/pages/manager/dashboard/detailBranch', compact('branch', 'users', 'selectedOption'));
    }
    
}
