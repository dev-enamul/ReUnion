<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReunionRequest;
use App\Models\About;
use App\Models\Reunion;
use Illuminate\Http\Request;

class ReUnionController extends Controller
{
    public function index(Request $request)
    {
        $query = Reunion::with('student');

        // Reunion filters
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->filled('payment_to')) {
            $query->where('payment_to', 'like', '%' . $request->payment_to . '%');
        }
        if ($request->filled('t_shirt_size')) {
            $query->where('t_shirt_size', $request->t_shirt_size);
        }
        if ($request->filled('is_interest_memorial')) {
            $query->where('is_interest_memorial', $request->is_interest_memorial);
        }
        if (!is_null($request->status)) {
            $query->where('is_active', $request->status);
        }

        // Student-related filters (via relationship)
        $query->whereHas('student', function ($q) use ($request) {
            if ($request->filled('dakhil_passing_year')) {
                $q->where('dakhil_passing_year', $request->dakhil_passing_year);
            }
            if ($request->filled('alim_passing_year')) {
                $q->where('alim_passing_year', $request->alim_passing_year);
            }
            if ($request->filled('fazil_passing_year')) {
                $q->where('fazil_passing_year', $request->fazil_passing_year);
            }
            if ($request->filled('blood_group')) {
                $q->where('blood_group', $request->blood_group);
            }
            if ($request->filled('phone')) {
                $q->where('phone', 'like', '%' . $request->phone . '%');
            }
            if ($request->filled('profession_id')) {
                $q->where('profession_id', $request->profession_id);
            }
        });

        // Pagination
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




    public function register(StoreReunionRequest $request)
    {
        try {
            $paymentPhotoPath = null; 
            if ($request->hasFile('payment_photo')) {
                $file = $request->file('payment_photo');
                $filename = time() . '_' . $file->getClientOriginalName(); 
                $path = $file->storeAs('uploads/payment_photos', $filename, 'public');
 
                $paymentPhotoPath = asset('storage/' . $path);
            } 
            $fee = About::where('key','fee')->first(); 

            Reunion::create([
                'student_id'        => $request->student_id,
                'fee'               => $fee->value,
                'payment_method'    => $request->payment_method,
                'payment_number'    => $request->payment_number,
                'payment_photo'     => $paymentPhotoPath,
                't_shirt_size'      => $request->t_shirt_size,
                'payment_to'        => $request->payment_to,
                'is_interest_memorial' => $request->is_interest_memorial,
            ]); 
            return success_response(null, "Your request has been submitted. We will contact you as soon as possible.");

        } catch (\Exception $e) {
            return error_response($e->getMessage());
        }
    }



    public function approve($id){
        $reunion = Reunion::find($id);
        if(!$reunion){
            return error_response(null,404,"Not found");
        } 
        $reunion->is_active = true;
        $reunion->save();
        return success_response(null,"Successfully approved");
    }

}
