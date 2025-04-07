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

class DashboardController extends Controller
{ 
    public function dashboard(){
        $total_student = Student::count();
        $reunion_register = Reunion::where('is_active')->count();
        $reunion_unregister = Student::whereDoesntHave('reunion')
            ->count();
        return success_response(["total_student" => $total_student,"reunion_register" => $reunion_register,"reunion_unregister"=> $reunion_unregister]); 
    }
}
