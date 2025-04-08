<?php

namespace App\Http\Controllers\Dashbaord;

use App\Http\Controllers\Controller;
use App\Models\About;
use App\Models\FollowupCategory;
use App\Models\Reunion;
use App\Models\SalesPipeline;
use App\Models\Student;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{ 
    public function dashboard() { 
        $total_student = Student::count();
     
        $reunion_register = Reunion::where('is_active', true)->count();
     
        $reunion_unregister = Student::whereDoesntHave('reunion')->count();
     
        $topPassingYearStudents = DB::table('students')
            ->select('dakhil_passing_year', DB::raw('count(*) as student_count'))
            ->groupBy('dakhil_passing_year')
            ->orderByDesc('student_count')
            ->limit(10)
            ->get();
     
        $topBatchReunion = DB::table('reunions')
            ->join('students', 'reunions.student_id', '=', 'students.id')
            ->select('students.dakhil_passing_year', DB::raw('count(reunions.id) as reunion_count'))
            ->groupBy('students.dakhil_passing_year')
            ->orderByDesc('reunion_count')
            ->limit(5)
            ->get();
     
        $setting = About::where('key', 'first_batch_year')->first(); 
        $first_batch_year = $setting ? $setting->value : null;
     
        $yearWiseData = [];
     
        if ($first_batch_year) {
            $currentYear = now()->year;
     
            for ($year = $currentYear; $year >= $first_batch_year; $year--) { 
                $studentCount = Student::where('dakhil_passing_year', $year)->count();
     
                $reunionCount = Reunion::join('students', 'reunions.student_id', '=', 'students.id')
                    ->where('students.dakhil_passing_year', $year)
                    ->count();
     
                $yearWiseData[] = [
                    'batch' => $year,
                    'student' => $studentCount,
                    'reunion' => $reunionCount,
                ];
            }
        }
     
        return success_response([
            "total_student" => $total_student,
            "reunion_register" => $reunion_register,
            "reunion_unregister" => $reunion_unregister,
            "topPassingYearStudents" => $topPassingYearStudents,
            "topBatchReunion" => $topBatchReunion,
            "year_wise_data" => $yearWiseData,
        ]);
    }
    
    
}
