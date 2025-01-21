<?php

namespace App\Http\Controllers\AdminEmployee;

use App\Helpers\ReportingService;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeStoreResource;
use App\Models\DesignationLog;
use App\Models\Employee;
use App\Models\EmployeeDesignation;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserContact;
use App\Models\UserReporting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{ 
    public function index()
    {
        try {
            $data = DB::table('users')
                ->leftJoin('employees', 'users.id', '=', 'employees.user_id')  
                ->leftJoin('employee_designations', function ($join) {
                    $join->on('employees.id', '=', 'employee_designations.employee_id')
                        ->whereNull('employee_designations.end_date');
                })  
                ->leftJoin('designations', 'employee_designations.designation_id', '=', 'designations.id')  
                ->select( 
                    'employees.id as id',
                    'users.id as user_id', 
                    'employees.employee_id', 
                    'users.profile_image', 
                    'users.name',  
                    'users.phone', 
                    'users.email', 
                    'users.senior_user', 
                    'users.junior_user',
                    'designations.title as designation'
                )
                ->where('user_type','employee')
                ->where('users.user_type', 'employee') // Filter only employee users
                ->get();

            return success_response($data);


        } catch (\Exception $e) {   
            return error_response($e->getMessage(), 500);
        }
    }
 
    public function store(EmployeeStoreResource $request)
    {
        DB::beginTransaction();
        try {
            $profilePicPath = null;
            if ($request->hasFile('profile_image')) {
                $profilePicPath = $request->file('profile_image')->store('profile_images', 'public');
            }
 
            $auth_user = User::find(auth()->id);
            $user = User::create([
                'name'          => $request->name,
                'email'         => $request->email,
                'phone'         => $request->phone,
                'password'      => Hash::make("12345678"),
                'user_type'     => 'employee',  
                'profile_image' => $profilePicPath,  
                'dob'           => $request->dob, 
                'blood_group'   => $request->blood_group, 
                'gender'        => $request->gender, 
            ]); 
 
            UserContact::create([
                'user_id'           => $user->id,
                'name'              => $request->name,
                'relationship_or_role' => "Employee",
                'office_phone'      => $request->office_phone,
                'personal_phone'    => $request->personal_phone,
                'office_email'      => $request->office_email,
                'personal_email'    => $request->personal_email,
                'whatsapp'          => $request->whatsapp,
                'imo'               => $request->imo,
                'facebook'          => $request->facebook,
                'linkedin'          => $request->linkedin,
            ]);
 
            UserAddress::create([
                'user_id' => $user->id,
                'address_type'      => "permanent",
                'country'           => $request->permanent_country,
                'division'          => $request->permanent_division,
                'district'          => $request->permanent_district,
                'upazila_or_thana'  => $request->permanent_upazila_or_thana,
                'address'           => $request->permanent_address,
                "is_same_present_permanent" => $request->is_same_present_permanent
            ]);   

            if(!$request->is_same_present_permanent){
                UserAddress::create([
                    'user_id' => $user->id,
                    'address_type'      => "present",
                    'country'           => $request->present_country,
                    'division'          => $request->present_division,
                    'district'          => $request->present_district,
                    'upazila_or_thana'  => $request->present_upazila_or_thana,
                    'address'           => $request->present_address,
                    "is_same_present_permanent" => $request->is_same_present_permanent
                ]);
            }
 
            $employee = Employee::create([
                'user_id' => $user->id,
                'employee_id' => Employee::generateNextEmployeeId(),
                'designation_id'=> $request->designation_id, 
                'status' => 1,
            ]); 

            // Create Employee Designation
            DesignationLog::create([
                'user_id' => $user->id,
                'employee_id' => $employee->id,
                'designation_id' => $request->designation_id,
                'start_date' => now() 
            ]); 

            UserReporting::create([
                'user_id' => $user->id, 
                'reporting_user_id' => $request->reporting_user_id,
                'start_date' => now() 
            ]);

            $user->senior_user = json_encode(ReportingService::getAllSenior($user->id));
            $user->junior_user = json_encode(ReportingService::getAllJunior($user->id));
            $user->save();
            
            DB::commit();  
            return success_response(null,'Employee has been created'); 

        } catch (\Exception $e) { 
            DB::rollBack();  
            return error_response($e->getMessage(), 500);
        }
    }

    public function show(string $id)
    {
        //
    }

    
    public function update(Request $request, string $id)
    {
        //
    }

    
    public function destroy(string $id)
    {
        //
    }
}
