<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Quiz;
use Illuminate\Support\Facades\DB; // Import DB class
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class CalonKandidatExport implements FromQuery, WithHeadings, ShouldAutoSize, WithColumnFormatting
{
    use Exportable;

    private $addPercentage = false;

    public function setAddPercentage($addPercentage)
    {
        $this->addPercentage = $addPercentage;
    }

    public function headings(): array
    {
        $quizHeadings = Quiz::pluck('name', 'id')->toArray();
        
        return [
            'Nama',
            'Telah di FU oleh',
            'Pendidikan Terakhir',
            'Cabang yang dilamar',
            'Posisi yang dilamar',
            'Sumber Informasi',
            'Progress',
            'Hasil Akhir',
            ...$quizHeadings, // Add dynamic quiz headings
        ];
    }    

    public function query()
    {
        $quizColumns = Quiz::pluck('id')->map(function ($quizId) {
            return DB::raw("COALESCE(ROUND(AVG(CASE WHEN results.quiz_id = $quizId THEN results.score END), 2), 'tidak mengerjakan') AS quiz_$quizId");
        })->toArray();
    
        $selectColumns = [
            'users.name AS name',
            'profiles.follow_up as "Telah di FU oleh"',
            'profiles.education AS education',
            'profiles.branch_location AS "Cabang yang dilamar"',
            'profiles.applied_position AS "Posisi yang dilamar"',
            'profiles.recruitment_source as "Sumber Informasi"',
            'ROUND(COUNT(DISTINCT results.quiz_id) / ' . Quiz::count() . ' * 100, 2) AS progress',
            'ROUND(AVG(results.score), 2) AS average_score',
            ...$quizColumns,
        ];
    
        return User::where('type', 0)
            ->selectRaw(implode(', ', $selectColumns))
            ->join('results', 'results.user_id', '=', 'users.id')
            ->join('profiles', 'profiles.user_id', '=', 'users.id')
            ->groupBy('users.id', 'users.name', 'profiles.follow_up', 'profiles.education', 'profiles.branch_location', 'profiles.applied_position', 'profiles.recruitment_source')
            ->havingRaw('progress = 100')
            ->havingRaw('average_score >= 50')
            ->orderBy('Cabang yang dilamar', 'ASC')
            ->orderBy('Posisi yang dilamar', 'ASC');
    }
        

    public function columnFormats(): array
    {
        if ($this->addPercentage) {
            return [
                'E' => '#0.00\%',
                'F' => '#0.00\/100',
            ];
        }
        
        return [];
    }    

    public function map($row): array
    {
        $quizIds = Quiz::pluck('id');
        $quizScores = [];
    
        foreach ($quizIds as $quizId) {
            $quizScores[] = $row["quiz_$quizId"];
        }
    
        return [
            $row['Nama'],
            $row['Telah di FU oleh'],
            $row['Pendidikan'],
            $row['Cabang yang dilamar'],
            $row['Posisi yang dilamar'],
            $row['Sumber Informasi'],
            $row['progress'],
            $row['Nilai Akhir'],
            ...$quizScores,
        ];
    }     
}
