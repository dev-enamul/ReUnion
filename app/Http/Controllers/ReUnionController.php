<?php

namespace App\Http\Controllers;

use App\Models\Reunion;
use Illuminate\Http\Request;

class ReUnionController extends Controller
{ 

    public function index(Request $request)
    { 
        $passing_year = $request->input('passing_year');
        $exam_name = $request->input('exam_name');
        $phone = $request->input('phone');
        $profession_id = $request->input('profession_id');
        $payment_method = $request->input('payment_method');
        $t_shirt_size = $request->input('t_shirt_size'); 
        $query = Reunion::with('student');

        // Apply filters dynamically
        if ($passing_year) {
            $query->whereHas('student', function($query) use ($passing_year) {
                $query->where('passing_year', $passing_year);
            });
        }
        if ($exam_name) {
            $query->whereHas('student', function($query) use ($exam_name) {
                $query->where('exam_name', $exam_name);
            });
        }
        if ($phone) {
            $query->whereHas('student', function($query) use ($phone) {
                $query->where('phone', $phone);
            });
        }
        if ($profession_id) {
            $query->whereHas('student', function($query) use ($profession_id) {
                $query->where('profession_id', $profession_id);
            });
        }
        if ($payment_method) {
            $query->where('payment_method', $payment_method);
        }
        if ($t_shirt_size) {
            $query->where('t_shirt_size', $t_shirt_size);
        }
 
        $perPage = $request->input('per_page', 20);   
        $page = $request->input('page', 1);     
 
        $reunions = $query->latest()->paginate($perPage);
 
        $return_data = [
            "registers" => $reunions->items(),
            "meta" => [
                'total' => $reunions->total(),
                'current_page' => $reunions->currentPage(),
                'per_page' => $reunions->perPage(),
                'last_page' => $reunions->lastPage()
            ]
        ]; 
        return success_response($return_data);
    }



    public function register(Request $request)
    { 
        Reunion::create([
            'student_id' => $request->student_id,
            'fee' => $request->fee,
            'payment_method' => $request->payment_method,
            'payment_number' => $request->payment_number,
            'payment_photo' => $request->payment_photo,
            't_shirt_size' => $request->t_shirt_size,
        ]); 
        return success_response(null, "Your request has been submitted. We will contact you as soon as possible.");
    }

}
