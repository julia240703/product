<?php

namespace App\Http\Controllers;

use Yajra\DataTables\DataTables;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\Branch;
use App\Models\Result;
use App\Models\User;
use App\Models\Quiz;
use App\Models\JobPosition;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class AdminDashboardController extends Controller
{
    //
    public function adminBranch(Request $request)
    {
        $selectedOption = $request->input('duration', 'all');
        $selectedJobPosition = $request->input('job_position', '');
    
        $branchCount = Branch::count();
        $jobPositionCount = JobPosition::count();
    
        [$userCountJakarta, $userCountsJakarta] = $this->getUserCounts('Jakarta', $selectedJobPosition, $selectedOption);
        [$userCountTangerang, $userCountsTangerang] = $this->getUserCounts('Tangerang', $selectedJobPosition, $selectedOption);
        [$userCountLuarKota, $userCountsLuarKota] = $this->getUserCounts('Luar Kota', $selectedJobPosition, $selectedOption);
    
        $jobPositions = JobPosition::all();
    
        return view('/pages/admin/dashboard/branchDashboard', compact(
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
    
    private function getUserCounts($location, $selectedJobPosition, $selectedOption)
    {
        $avgScoreFilePath = public_path('/txt/avgScore.txt');
        $avgScore = (float) File::get($avgScoreFilePath);

        $userCountQuery = Profile::join('branches', 'profiles.branch_location', '=', 'branches.id')
            ->whereNull('profiles.follow_up')
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
            ->where('branches.city', 'LIKE', "%$location%")
            ->when(!empty($selectedJobPosition), function ($query) use ($selectedJobPosition) {
                $query->where('applied_position', $selectedJobPosition);
            });

        $userCount = $userCountQuery->count();

        $userCounts = (clone $userCountQuery)
            ->selectRaw('branches.city as branch_city, DATE(profiles.created_at) as date')
            ->groupBy('branches.city', 'date')
            ->get()
            ->mapToGroups(function ($item) {
                return [$item->branch_city . ' - ' . $item->date => $item];
            })
            ->map(function ($groupedItems) {
                return $groupedItems->count();
            });
    
        return [$userCount, $userCounts];
    }
    
    

    public function manageBranch(Request $request)
    {
        if ($request->ajax()) {
            $data = Branch::orderBy('city', 'asc')->get();
    
            $data = $data->map(function ($branch, $index) {
                $branch->row_number = $index + 1;
                return $branch;
            });
    
            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    // Add your custom action code here
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        
        return view('/pages/admin/manageBranch');
    }


    public function editBranch(Request $request)
    {
        $branchId = $request->input('id');
    
        // Retrieve the branch data based on the provided ID
        $branch = Branch::find($branchId);
    
        // Check if the branch exists
        if (!$branch) {
            abort(404);
        }
    
        // Update the quiz data with the new values
        $branch->city = $request->input('city');
        $branch->location = $request->input('location');
        $branch->initials = $request->input('initials');

        $branch->save();
    
        // You can choose how to handle the response after updating the quiz
        // For example, you can redirect to a different page or return a JSON response
    
        return redirect('/admin/branch/manage-branch');
    
        // Return a JSON response
        // return response()->json(['message' => 'Quiz updated successfully'], 200);
    }


    public function deleteBranch(Request $request)
    {
        $branchId = $request->input('id');
    
        $branch = Branch::find($branchId);
    
        if (!$branch) {
            abort(404);
        }
    
        $branch->delete();
    
        return redirect('/admin/branch/manage-branch')->with('success', 'Cabang berhasil dihapus.');
    }
    


    public function storeBranch(Request $request)
    {
        $this->validate($request,[
            'kota' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'inisial' => 'required|string|max:255',
            
        ]);

        $branch = new Branch;

        $branch->city = $request->input('kota');
        $branch->location = $request->input('lokasi');
        $branch->initials = $request->input('inisial');
        
        $branch->save();
        return redirect('/admin/branch/manage-branch')->with('success', 'Cabang baru berhasil ditambahkan');

        
    }


    public function adminJKTBranch(Request $request)
    {
        $selectedOption = request()->input('duration', 'all');
        $selectedJobPosition = $request->input('job_position', '');
    
        $branches = Branch::where('city', 'Jakarta')->get();
        $jobPositionCount = JobPosition::count();
    
        $dataAHASS = new Collection();
        $dataWAHANA = new Collection();
    
        foreach ($branches as $branch) {
            $userCount = $this->getUserCountJKT($selectedOption, $selectedJobPosition, $branch->location);
    
            // Determine which category (AHASS WAHANA or WAHANA) the branch belongs to
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
    
        // Sort dataAHASS in descending order based on location
        $dataAHASS = $dataAHASS->sortByDesc('branch.location');
    
        // Sort dataWAHANA in ascending order based on location
        $dataWAHANA = $dataWAHANA->sortBy('branch.location');
    
        // Merge the two collections
        $mergedData = $dataWAHANA->merge($dataAHASS);
    
        $jobPositions = JobPosition::all();
    
        return view('/pages/admin/dashboard/jakartaBranch', compact('mergedData', 'jobPositionCount', 'selectedOption', 'selectedJobPosition', 'jobPositions'));
    }

    private function getUserCountJKT($selectedOption, $selectedJobPosition, $branchLocation)
    {
        $avgScoreFilePath = public_path('/txt/avgScore.txt');
        $avgScore = (float) File::get($avgScoreFilePath);
    
        $userCount = Result::join('users', 'results.user_id', '=', 'users.id')
            ->join('profiles', 'users.id', '=', 'profiles.user_id')
            ->join('branches', 'branches.id', '=', 'profiles.branch_location')
            ->where('branches.location', '=', $branchLocation)
            ->whereNull('profiles.follow_up');
    
        if (!empty($selectedJobPosition)) {
            $userCount->where('profiles.applied_position', $selectedJobPosition);
        }
    
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
            ->havingRaw('COUNT(results.id) = ?', [Quiz::count()])
            ->havingRaw('ROUND(AVG(results.score), 2) >= ?', [$avgScore])
            ->selectRaw('users.id, users.name, profiles.branch_location, profiles.education')
            ->count();
    }
    
    


    public function adminTGRBranch(Request $request)
    {
        $selectedOption = $request->input('duration', 'all');
        $selectedJobPosition = $request->input('job_position', '');
        
        $branches = Branch::where('city', 'Tangerang')->get();
        $jobPositionCount = JobPosition::count();
    
        $dataAHASS = new Collection();
        $dataWAHANA = new Collection();
    
        foreach ($branches as $branch) {
            // Note: send $branch->id because that is what is stored in profiles.branch_location
            $userCount = $this->getUserCountTGR($selectedOption, $selectedJobPosition, $branch->id);
    
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
    
        $dataAHASS = $dataAHASS->sortBy('branch.location');
        $dataWAHANA = $dataWAHANA->sortBy('branch.location');
        $mergedData = $dataWAHANA->merge($dataAHASS);
    
        $jobPositions = JobPosition::all();
    
        return view('/pages/admin/dashboard/tangerangBranch', compact(
            'mergedData', 'dataAHASS', 'dataWAHANA',
            'jobPositionCount', 'selectedOption', 'selectedJobPosition', 'jobPositions'
        ));
    }
    
    

    private function getUserCountTGR($selectedOption, $selectedJobPosition, $branchId)
    {
        $avgScoreFilePath = public_path('/txt/avgScore.txt');
        $avgScore = (float) File::get($avgScoreFilePath);
    
        $userCount = Result::join('users', 'results.user_id', '=', 'users.id')
            ->join('profiles', 'users.id', '=', 'profiles.user_id')
            ->where('profiles.branch_location', $branchId) // use exact match because ID
            ->whereNull('profiles.follow_up')
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
    
        $userCount = $userCount
            ->groupBy('users.id', 'users.name', 'profiles.branch_location', 'profiles.education')
            ->havingRaw('COUNT(results.id) = ?', [Quiz::count()])
            ->havingRaw('ROUND(AVG(results.score), 2) >= ?', [$avgScore])
            ->selectRaw('users.id AS id, users.name AS name, profiles.branch_location AS branch_location, profiles.education AS education')
            ->count();
    
        return $userCount;
    }
    
    

    public function adminOthersBranch(Request $request)
    {
        $selectedOption = $request->input('duration', 'all');
        $selectedJobPosition = $request->input('job_position', '');
    
        $branches = Branch::where('city', 'Luar Kota')->get();
        $jobPositionCount = JobPosition::count();
    
        $dataAHASS = new Collection();
        $dataWAHANA = new Collection();
    
        foreach ($branches as $branch) {
            // Use $branch->id because that is what is stored in profiles.branch_location
            $userCount = $this->getUserCountOthers($selectedOption, $selectedJobPosition, $branch->id);
    
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
    
        $dataAHASS = $dataAHASS->sortBy('branch.location');
        $dataWAHANA = $dataWAHANA->sortBy('branch.location');
        $mergedData = $dataWAHANA->merge($dataAHASS);
    
        $jobPositions = JobPosition::all();
    
        return view('/pages/admin/dashboard/othersBranch', compact(
            'mergedData', 'jobPositionCount', 'selectedOption', 'selectedJobPosition', 'jobPositions'
        ));
    }    

    private function getUserCountOthers($selectedOption, $selectedJobPosition, $branchId)
    {
        $avgScoreFilePath = public_path('/txt/avgScore.txt');
        $avgScore = (float) File::get($avgScoreFilePath);
    
        $userCount = Result::join('users', 'results.user_id', '=', 'users.id')
            ->join('profiles', 'users.id', '=', 'profiles.user_id')
            ->where('profiles.branch_location', $branchId) // Exact match by branch ID
            ->whereNull('profiles.follow_up')
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
    
        $userCount = $userCount
            ->groupBy('users.id', 'users.name', 'profiles.branch_location', 'profiles.education')
            ->havingRaw('COUNT(results.id) = ?', [Quiz::count()])
            ->havingRaw('ROUND(AVG(results.score), 2) >= ?', [$avgScore])
            ->selectRaw('users.id AS id, users.name AS name, profiles.branch_location AS branch_location, profiles.education AS education')
            ->count();
    
        return $userCount;
    }
    



    public function adminBranchDetail(Request $request, $branch_location)
    {
        $branch = Branch::where('location', 'LIKE', '%' . $branch_location . '%')->firstOrFail();

        // Read the average score from avgScore.txt
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
                ->whereNull('profiles.follow_up')
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
    
            // Filter the data based on users who have 100% progress and average_score >= $avgScore
            $filteredData = $data->filter(function ($item) use ($totalQuizzes, $avgScore) {
                $progress = round($item->quizzes_taken / $totalQuizzes * 100, 2);
                return $progress >= 100 && $item->average_score >= $avgScore;
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
                    $user = $users->firstWhere('id', $row->id);
                    if ($user && $user->results->isNotEmpty()) {
                        return $user->results->first()->created_at->format('Y-m-d');
                    }
                    return '-';
                })
                

                ->rawColumns(['action', 'progress'])
                ->make(true);
        }
    
        return view('/pages/admin/dashboard/detailBranch', compact('branch', 'users', 'selectedOption'));
    }

}
