<?php

namespace App\Http\Controllers\Dashbaord;

use App\Http\Controllers\Controller;
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
    public function dashboard(){
        $total_student = Student::count();
        $reunion_register = Reunion::where('is_active')->count();
        $reunion_unregister = Student::whereDoesntHave('reunion')
            ->count();
        
        $topPassingYearStudents = DB::table('students')
            ->select('passing_year', DB::raw('count(*) as student_count'))
            ->groupBy('passing_year')
            ->orderByDesc('student_count')
            ->limit(10)
            ->get();

        return success_response(["total_student" => $total_student,"reunion_register" => $reunion_register,"reunion_unregister"=> $reunion_unregister]); 
    }
}
