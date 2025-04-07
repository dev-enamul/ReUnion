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
    
        if ($request->filled('passing_year')) {
            $query->where('passing_year', $request->passing_year);
        }  
    
        if ($request->filled('exam_name')) {
            $query->where('exam_name', $request->exam_name);
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
    

    public function register(StudentRegisterRequest $request)
    {
        try {
            $input = $request->all();
    
            // Check if there is a file uploaded
            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
    
                // Generate a unique filename using the current timestamp and the original file name
                $filename = time() . '_' . $file->getClientOriginalName();
    
                // Define the path where you want to store the file
                $destinationPath = public_path('upload');  // 'public/upload' folder
    
                // Move the file to the desired folder
                $file->move($destinationPath, $filename);
    
                // Generate the full URL for the uploaded file
                $fileUrl = url('upload/' . $filename);  // full URL based on the public path
    
                // Store the full URL in the database
                $input['profile_picture'] = $fileUrl;
            }
    
            // Create the student record
            Student::create($input);
    
            // Return success response
            return success_response(null, "Your registration has been successfully completed.");
        } catch (Exception $e) {
            // Catch any errors and return error response
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
