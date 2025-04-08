<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentRegisterRequest;
use App\Models\Student;
use Exception;
use Illuminate\Http\Request;

class StudentController extends Controller
{

    public function index(Request $request)
    {
        $query = Student::query(); 

        if ($request->filled('dakhil_passing_year')) {
            $query->where('dakhil_passing_year', $request->dakhil_passing_year);
        }

        if ($request->filled('alim_passing_year')) {
            $query->where('alim_passing_year', $request->alim_passing_year);
        }

        if ($request->filled('fazil_passing_year')) {
            $query->where('fazil_passing_year', $request->fazil_passing_year);
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('blood_group')) {
            $query->where('blood_group', $request->blood_group);
        }

        if ($request->filled('present_zila')) {
            $query->where('present_zila', $request->present_zila);
        }

        if ($request->filled('permanent_zila')) {
            $query->where('permanent_zila', $request->permanent_zila);
        }

        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }

        if ($request->filled('profession_id')) {
            $query->where('profession_id', $request->profession_id);
        }

        $perPage = $request->input('per_page', 20);  
        $page = $request->input('page', 1);  

        $allStudents = $query->latest()->get();  
        $total = $allStudents->count();  

        $students = $allStudents->forPage($page, $perPage)->values();  

        $return_data = [
            "students" => $students,
            "meta" => [
                'total' => $total,
                'current_page' => (int)$page,
                'per_page' => (int)$perPage,
                'last_page' => ceil($total / $perPage)
            ]
        ]; 
        return success_response($return_data);
    }

    

    public function register(StudentRegisterRequest $request){ 
        try{ 

            $old_student = Student::where('phone', $request->phone)->first();
            if ($old_student) {
                return success_response($old_student, "You are already registered as a student.");
            }


            $input = $request->all();
            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('profile_pictures', $filename, 'public');

                $fullUrl = asset('storage/' . $path);
                $input['profile_picture'] = $fullUrl; 
            }else{
                $input['profile_picture'] = null;
            }
            $data = Student::create($input); 
            return success_response($data,"Your registration has been successfylly");
        }catch(Exception $e){
            return error_response($e->getMessage());
        }
    } 

    public function findByPhone(Request $request){
        $phone = $request->phone;
        $student = Student::where('phone',$phone)->first();
        if($student){
            return success_response($student);
        }else{
            return error_response(null, 404, "You Haven't register yet");
        }
    }
}
