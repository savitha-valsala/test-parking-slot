<?php

namespace App\Http\Middleware;
use Illuminate\Http\Request;

use Closure;

class filterParking
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
        $todayDate = date('Y-m-d'); 
        $validator = \Validator::make(
            $input,
            [
                'customerName' => 'required|min:5|max:200',
                'customerMobile' => 'required|numeric|digits:10',
                'VehicleNo' => 'required|max:200',
                'parkingDate' => "required|date_format:Y-m-d|after_or_equal:$todayDate",
                'License' => 'required',
            ],
            [
                'customerName.required' => 'Please provide Customer Name',
                'customerName.min' => 'Invalid Customer Name',
                'customerName.max' => 'Invalid Customer Name',
                'customerMobile.required' => 'Please provide Customer Mobile Number',
                'customerMobile.numeric' => 'Invalid Customer Mobile Number',
                'customerMobile.digits' => 'Invalid Customer Mobile Number',
                'VehicleNo.required' => 'Please provide Vehicle Number',
                'parkingDate.required' => 'Please provide Parking Date',
                'License.required' => 'Please provide License Document'
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
