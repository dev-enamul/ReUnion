<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingStoreRequest;
use App\Models\About;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function settingUpdate(SettingStoreRequest $request){
        $settings = [
            'school_name'        => $request->school_name,
            'first_batch_year'   => $request->first_batch_year,
            'fee'                => $request->fee,
            'event_date'         => $request->event_date,
        ]; 
        foreach ($settings as $key => $value) {
            $existing = About::where('key', $key)->first();
            if ($existing) {
                $existing->value = $value;
                $existing->save();
            } else {
                About::create([
                    'key'   => $key,
                    'value' => $value,
                ]);
            }
        } 
        return success_response(null, "settings have been updated successfully.");
    }

    public function setting(){
        $keys = ['school_name', 'first_batch_year', 'fee', 'event_date']; 
        $settings = About::whereIn('key', $keys)->pluck('value', 'key'); 
        return success_response($settings);
    }
}
