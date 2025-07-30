<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profile;
use App\Models\Quiz;
use App\Models\Result;

class PublicController extends Controller
{

    public function userResults(Request $request)
    {
        $users = User::where('type', 0)->with(['results.quiz', 'profile'])->get();
        $totalQuizzes = Quiz::count();
    
        if ($request->ajax()) {
            $data = Result::selectRaw('users.id AS id, users.name AS name, profiles.branch_location AS branch_location, profiles.education AS education, ROUND(AVG(results.score), 2) AS average_score, COUNT(DISTINCT results.quiz_id) AS quizzes_taken, profiles.applied_position AS applied_position')
                ->join('users', 'users.id', '=', 'results.user_id')
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
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
    
        return view('/pages/public/result', compact('users'));
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
    
        return view('/pages/public/resultDetail', compact('user', 'profile', 'labels', 'scores'));
    }
    
}