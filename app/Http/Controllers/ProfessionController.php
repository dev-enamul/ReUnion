<?php

namespace App\Http\Controllers;

use App\Models\Profession;
use Illuminate\Http\Request;

class ProfessionController extends Controller
{
    public function index()
    {
        $professions = Profession::latest()->select('id','name')->get();
        return success_response($professions); 
    } 

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:professions,name',
        ]);

        $profession = Profession::create([
            'name' => $request->name,
            'slug' => getSlug(new Profession(),$request->name),
            'description' => $request->description,
            'is_active' => $request->is_active ?? true,
        ]);  
        return success_response(null,"Profession created successfully"); 
    }

    public function show($id)
    {
        $profession = Profession::findOrFail($id);
        return success_response($profession); 
    } 

    public function update(Request $request, $id)
    {
        $profession = Profession::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:professions,name,' . $profession->id,
        ]);

        $profession->update([
            'name' => $request->name,
            'slug' => getSlug(new Profession(),$request->name),
            'description' => $request->description,
            'is_active' => $request->is_active ?? true,
        ]);

        return success_response(null,"Profession updated successfully"); 
    } 

    public function destroy($id)
    {
        $profession = Profession::findOrFail($id);
        $profession->delete();

        return success_response(null,"Profession deleted successfully"); 
    }
}
