<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;  
    protected $fillable = [
        'name',
        'profile_picture',
        'phone',
        'whatsapp',
        'guardiant',
        'gender',
        'blood_group',
        'dakhil_passing_year',
        'alim_passing_year',
        'fazil_passing_year',
        'max_education',
        'profession_id',
        'profession_details',
        'present_village',
        'present_post',
        'present_upazila',
        'present_zila',
        'permanent_village',
        'permanent_post',
        'permanent_upazila',
        'permanent_zila',
        'is_active',
    ];
    

    public function reunion(){
        return $this->hasOne(Reunion::class);
    }
}
