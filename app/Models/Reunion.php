<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reunion extends Model
{
    use HasFactory;  
    protected $fillable = [
        'student_id',
        'fee',
        'payment_method',
        'payment_number',
        'payment_photo',
        'payment_to',
        'is_interest_memorial',
        't_shirt_size',
        'is_active',
    ];
    

    public function student(){
        return $this->belongsTo(Student::class);
    }
}
