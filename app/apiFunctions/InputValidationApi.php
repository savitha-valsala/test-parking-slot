<?php
namespace  App\apiFunctions;
use Illuminate\Support\Facades\DB;
use App\checkoutDet;
use App\parkingDet;
use DateTime;
Class InputValidationApi {

  private $tot_slot =130;
  private $min_fee =10;
  private $hr_fee =5;
  private $hr_fee_nt =100;


    public function validateParkingreq($inputs)
    {

            $res_check_1 = parkingDet::where('customer_mobile','=',$inputs['customerMobile'] )
                                            ->where('iny_status','!=',2)->count();
              if( $res_check_1 ==0)   
               {
                $reqtime=date_create($inputs['parkingDate']);
                $prop_date_in= date_format($reqtime,"Y-m-d H:m:i");
                date_add($reqtime,date_interval_create_from_date_string("3 hours"));
                 $prop_date_out= date_format($reqtime,"Y-m-d H:m:i");
                
                $slot = $this->getSlot($prop_date_in); //return $slot;
                $ap_no =$this->getApNo( $slot ,$prop_date_in );
                $prop_fee = 10;

                /*******************save */
                $tb_park = new parkingDet();
                $tb_park->customer_name =$inputs['customerName'];
                $tb_park->customer_mobile =$inputs['customerMobile'];
                $tb_park->vehicle_no =$inputs['VehicleNo'];
                $tb_park->request_time =$prop_date_in;
                $tb_park->proposed_time_in = $prop_date_in;
                $tb_park->proposed_time_out =$prop_date_out;
                $tb_park->slot =$slot;
                $tb_park->ap_no =$ap_no ;
                $tb_park->license =$inputs['License'];
                $tb_park->proposed_fee =$prop_fee ;
                $tb_park->iny_status =1 ;
                $tb_park->status_time =date("Y-m-d H:i:s"); 
                $tb_park->save();
                $res["data"]=json_encode(["AppointNo"=>$ap_no ,"slot"=>$slot,"ExpectedFee"=>$prop_fee ]);
                $res["message"]="Slot alloted";
                $res["status"]="ok";
                               
              }    
              else{
                $res["data"]=null;
                $res["message"]="Unable to allow slot";
                $res["status"]="Error";
                
              }                      
              return json_encode($res);
    }
    protected function getSlot($reqtime){
      $slot_arr = $this->getSlotArr();

      $res_count = parkingDet::where('request_time','=',$reqtime )->count();   
      //echo    $res_count; exit;
      return  $slot_arr[$res_count] ;
    }
   protected function getSlotArr()
   {
      foreach(range('A','Z') as $v){
        
        $arr[]=str_repeat( $v, 5);
      }  
      
      for($i=0;$i<count($arr);$i++){
        $arr_each[$i] = str_split($arr[$i]);
      }
      
      for($k=0;$k<count($arr_each) ;$k++){
        for($m=0 ; $m<count($arr_each[$k]);$m++)
        {
              $arr_org[] = $arr_each[$k][$m].sprintf( '%02d', $m+1  );
        }
      }
      return $arr_org;  
   
   }
   protected function getApNo($slot,$reqtime )
   {
    $apNo_arr=$this->getApnoArr(); 
    $res_count = parkingDet::where('request_time','=',$reqtime )->count();  
    $apNo_org = $slot.$apNo_arr[$res_count]; 
    return $apNo_org;
   }
   protected function getApnoArr()
   {
    foreach(range('A','Z') as $v){        
      $arr[]=$v;
    }
    $arr_1 =$arr_2= $arr;
    for ($i=0 ; $i<count($arr_1);$i++)
    {
      for ($j=0 ; $j<count($arr_2);$j++)
       {
        for ($k=0 ; $k<count($arr);$k++)
        {
            $arr_org[]=$arr_1[$i].$arr_2[$j].$arr[$k];
        }
      
       }
    }
    return  $arr_org;
   }

   public function validateCheckout($inputs)
   {
    $res_check_1 = parkingDet::where('customer_mobile','=',$inputs['customerMobile'] )
                                             ->where('slot','=',$inputs['slot'])
                                             ->where('ap_no','=',$inputs['AdmitNo'])
                                            ->where('iny_status','=',1)->count();
    if($res_check_1 ==1)
    {
      $res_data = parkingDet::where('customer_mobile','=',$inputs['customerMobile'] )
      ->where('slot','=',$inputs['slot'])
      ->where('ap_no','=',$inputs['AdmitNo'])
     ->where('iny_status','=',1)->first();
     
     $req_id = $res_data->id;
     $time_in = $res_data->proposed_time_in; 
     $actual_time_out=date("Y-m-d H:i:s");   
     
     $date1 = new DateTime($time_in );
     $date2 = new DateTime($actual_time_out);
     $interval = $date1->diff($date2);
     $hrs = $interval->h;
     $actual_fee = $this->min_fee;
     $ext =0 ;
     
     if ($hrs < 4 ){
      $ext =0 ;
     }
     else{
      if($hrs >12){
        $ext = $this->hr_fee_nt;
      }
      else{
        $ext = ($hrs - 3) * $this->hr_fee;
      } 

     }
     $actual_fee =  $actual_fee + $ext;
   //  return $actual_fee ;

     //save in db

     DB::beginTransaction();  

      $checkout = new checkoutDet();
      $checkout->req_id =$req_id;
      $checkout->actual_time_in =$time_in;
      $checkout->actual_time_out =$actual_time_out;
      $checkout->actual_fee =$actual_fee;
      $checkout->save();

      $obj_log =  parkingDet::where('id', $req_id)->first();
      $obj_log->iny_status = 2;
       $obj_log->status_time = date("Y-m-d H:i:s");  
       $obj_log->save();
       DB::commit();   
                $res["amount"]=$actual_fee ;
                $res["message"]="checkout Successfully";
                $res["status"]="ok";

    
    }
    else{
      $res["amount"]=null;
      $res["message"]="Unable to proceed";
      $res["status"]="error";
    }
    return json_encode($res);
   }
   public function getList(){
    $res_data = parkingDet::where('iny_status','=',2)->get();
    return $res_data ;
   }
   public function getamount(){
    $res_data = checkoutDet::sum('actual_fee');
    return $res_data ;
   }
   
 }

?>
