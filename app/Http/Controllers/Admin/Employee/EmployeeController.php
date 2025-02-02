<?php

namespace App\Http\Controllers\Admin\Employee;

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
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{ 
    public function index()
    {
        try {
            $data = User::where('user_type', 'employee')
                ->join('employees', 'users.id', '=', 'employees.user_id')
                ->join('designations', 'employees.designation_id', '=', 'designations.id')
                ->join('user_reportings', 'users.id', '=', 'user_reportings.user_id')
                ->join('users', 'user_reportings.reporting_user_id', '=', 'users.id')
                ->select('users.id','users.name', 'users.phone', 'users.email', 'users.profile_image', 'designations.title as designation')
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
                'created_by'    => Auth::user()->id,
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
 
            if(isset($request->permanent_country) || isset($request->permanent_zip_code) ||  isset($request->permanent_address)){
                UserAddress::create([
                    'user_id' => $user->id,
                    'address_type'      => "permanent",
                    'country'           => $request->permanent_country,
                    'division'          => $request->permanent_division,
                    'district'          => $request->permanent_district,
                    'upazila_or_thana'  => $request->permanent_upazila_or_thana,
                    "zip_code"          => $request->permanent_zip_code,
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
                        "zip_code"          => $request->present_zip_code,
                        'address'           => $request->present_address, 
                        "is_same_present_permanent" => $request->is_same_present_permanent
                    ]);
                }
            }
            
 
            $employee = Employee::create([
                'user_id' => $user->id,
                'employee_id' => Employee::generateNextEmployeeId(),
                'designation_id'=> $request->designation_id, 
                'status' => 1,
                'created_by'    => Auth::user()->id
                
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
        try{
            $data = User::with(['employee', 'address', 'contact'])->find($id);
            return success_response($data);
        }catch(Exception $e){
            return error_response($e->getMessage(), $e->getCode());
        }
        

    }

    
    public function update(EmployeeStoreResource $request, string $id)
    {
        DB::beginTransaction();
        try { 
            $user = User::findOrFail($id); 
            $profilePicPath = $user->profile_image; 
            if ($request->hasFile('profile_image')) { 
                if ($profilePicPath) {
                    Storage::disk('public')->delete($profilePicPath);
                } 
                $profilePicPath = $request->file('profile_image')->store('profile_images', 'public');
            }
     
            $user->update([
                'name'          => $request->name,
                'email'         => $request->email,
                'phone'         => $request->phone,
                'profile_image' => $profilePicPath,
                'dob'           => $request->dob,
                'blood_group'   => $request->blood_group,
                'gender'        => $request->gender,
                'updated_by'    => auth()->id(),
            ]);
     
            $user->contact()->updateOrCreate(
                ['user_id' => $user->id],  
                [
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
                ]
            );
     
            $user->address()->updateOrCreate(
                ['user_id' => $user->id, 'address_type' => 'permanent'],  
                [
                    'country'           => $request->permanent_country,
                    'division'          => $request->permanent_division,
                    'district'          => $request->permanent_district,
                    'upazila_or_thana'  => $request->permanent_upazila_or_thana,
                    "zip_code"          => $request->permanent_zip_code,
                    'address'           => $request->permanent_address,
                    "is_same_present_permanent" => $request->is_same_present_permanent,
                ]
            );
     
            if (!$request->is_same_present_permanent) {
                $user->address()->updateOrCreate(
                    ['user_id' => $user->id, 'address_type' => 'present'],
                    [
                        'country'           => $request->present_country,
                        'division'          => $request->present_division,
                        'district'          => $request->present_district,
                        'upazila_or_thana'  => $request->present_upazila_or_thana,
                        "zip_code"          => $request->present_zip_code,
                        'address'           => $request->present_address,
                        "is_same_present_permanent" => $request->is_same_present_permanent,
                    ]
                );
            }
     
            $user->employee()->update([
                'designation_id' => $request->designation_id,
                'updated_by'     => auth()->id(),
            ]);
     
            // $user->employee->designationLog()->updateOrCreate( 
            //     ['employee_id' => $user->employee->id,  'designation_id' => $request->designation_id],
            //     ['start_date' => now()]
            // );
     
            $user->reporting()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'reporting_user_id' => $request->reporting_user_id,
                    'start_date'        => now(),
                ]
            );
     
            $user->senior_user = json_encode(ReportingService::getAllSenior($user->id));
            $user->junior_user = json_encode(ReportingService::getAllJunior($user->id));
            $user->save();
    
            DB::commit();
            return success_response(null, 'Employee details have been updated successfully.');
    
        } catch (\Exception $e) {
            DB::rollBack();
            return error_response($e->getMessage(), 500);
        }
    }
    

    
    public function destroy(string $id)
    {
        //
    }
}
