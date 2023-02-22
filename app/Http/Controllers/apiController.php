<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\apiFunctions\InputValidationApi;

class apiController extends Controller
{
    public function parking_req(Request $request,InputValidationApi $val)
    {
        $input = $request->all();
        $validate = $val->validateParkingreq($input);
       return $validate;
    }
    public function checkout_update(Request $request,InputValidationApi $val)
    {
        $input = $request->all();
        $validate = $val->validateCheckout($input);
       return $validate;
    }
    public function list(InputValidationApi $val)
    {
        $list = $val->getList();  
        $amount = $val->getamount(); 
        return view('list', ['del' =>   $list,'amount'=>$amount]);
    }
}
