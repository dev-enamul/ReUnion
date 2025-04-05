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

    public function dashboardData(){
        $total_student = Student::where('is_active')->get();
        $total_registerd = Reunion::where('is_active')->get();
    }

    public function leadChart(){  
    for ($i = 5; $i >= 0; $i--) {
            $startDate = Carbon::now()->subMonths($i)->startOfMonth()->toDateString();
            $endDate = Carbon::now()->subMonths($i)->endOfMonth()->toDateString();

            $leadsData[] = [
                'month' => Carbon::now()->subMonths($i)->format('F Y'),
                'leads' => SalesPipeline::whereBetween('created_at', [$startDate, $endDate])->count()
            ];
        }

        return success_response($leadsData);
    }

    public function cardData(Request $request){
        try{
            $startDate = $request->start_date 
                ? Carbon::parse($request->start_date)->startOfDay()  
                : Carbon::today()->startOfDay();  

            $endDate = $request->end_date 
                ? Carbon::parse($request->end_date)->endOfDay()  
                : Carbon::today()->endOfDay(); 
            $employee_id = $request->user_id ?? Auth::user()->id;

            $data  = SalesPipeline::where('assigned_to',$employee_id)->whereBetween('created_at', [$startDate, $endDate]);
            $return_data = [
                'lead_collect' => $data->count(),
                'lead_active' => $data->where('status','Active')->count(),
                'lead_rejected' => $data->where('status','Rejected')->count(),
                'deal_close' => $data->where('status','Salsed')->count(),
            ]; 
            return success_response($return_data);
        }catch(Exception $e){
            return error_response($e);
        }
    }
}
