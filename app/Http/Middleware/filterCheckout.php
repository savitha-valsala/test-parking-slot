<?php

namespace App\Http\Middleware;
use Illuminate\Http\Request;

use Closure;

class filterCheckout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $input = $request->all(); 
        $validator = \Validator::make(
            $input,
            [                
                'customerMobile' => 'required|numeric|digits:10',
                'slot' => 'required',
                'AdmitNo' => "required",                
            ],
            [               
                'customerMobile.required' => 'Please provide Customer Mobile Number',
                'customerMobile.numeric' => 'Invalid Customer Mobile Number',
                'customerMobile.digits' => 'Invalid Customer Mobile Number',
                'slot.required' => 'Please provide slot',
                'AdmitNo.required' => 'Please provide AdmitNo'
                
            ]
        );
        if ($validator->passes())
        { 
        return $next($request);
        }else{
            
            $data['message'] = implode(',', $validator->errors()->all());
            $data['status'] = "Error";
            return response(json_encode($data));
        }
    }
}
