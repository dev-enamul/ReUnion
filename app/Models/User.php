<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;  

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */ 

    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'profile_image',
        'user_type',
        'dob',
        'blood_group',
        'gender',
        'senior_user',
        'junior_user',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
    

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ]; 

     public function employee(){
        return $this->hasOne(Employee::class, 'user_id');
     }

     public function customer()
     {
         return $this->hasOne(Customer::class, 'user_id');
     }
  

    public function address()
    {
        return $this->hasMany(UserAddress::class, 'user_id');
    }

    public function contact()
    {
        return $this->hasOne(UserContact::class, 'user_id');
    }

    public function educations()
    {
        return $this->hasMany(UserEducation::class, 'user_id');
    }

    public function document()
    {
        return $this->hasOne(UserFile::class, 'user_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }  

    public function salesPipelines()
    {
        return $this->hasMany(SalesPipeline::class);
    }

    public function seniorUsers()
    {
        return $this->hasMany(User::class, 'id', 'senior_user');
    }

    public function directJuniors()
    {
        return $this->hasMany(UserReporting::class, 'reporting_user_id');
    }

    public function userEnglishes()
    {
        return $this->hasMany(UserEnglish::class);
    }


    public function reportingUser()
    {
        return $this->hasOne(UserReporting::class, 'user_id')
                    ->where(function ($query) {
                        $query->whereNull('end_date')
                            ->orWhere('end_date', '>', now());
                    });
    }

 
 

}
