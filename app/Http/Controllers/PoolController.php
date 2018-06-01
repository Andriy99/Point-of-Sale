<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


use App\Items;
use App\ShoppingCart;
use App\Transactions;
use App\Drawer;
use App\Tax;
use App\User;
use App\Logs;
use App\Catg;
use App\SubCatg;
use App\Goods;
use App\PurchaseOrder;
use App\Branches;
use App\Customers;
use App\Tournaments;
use App\Drawings;
use App\Clubs;
use App\Events;
use App\Accounts;
use App\Pool;
use App\Libraries\FPDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PhpOffice\PhpSpreadsheet\IOFactory;

use File;
use Auth;

class PoolController extends Controller
{

    public function send_to_pool(Request $request){

        $id = $request->id;
        $event = $request->event;
        $sc = ShoppingCart::select('item','qty','customer','branch','tid')->where('id',$id)->orderBy('id','DESC')->limit(1)->first();
        
        $p = new Pool();
        $p->item = $sc->item;
        $p->qty = $sc->qty;
        $p->customer = $sc->customer;
        $p->sc_id = $id;
        $p->p_time = time();
        $p->branch = $sc->branch;
        $p->event = $event;
        $p->club = Events::find($event)->club;
        $p->type = 'purchase';
        $p->user = $request->session()->get('uid');
        $p->save();
    
        $up_sc = ShoppingCart::find($id);
        $up_sc->type = 'pool';
        $up_sc->save();
    
        echo json_encode(array('status'=>1,'tid'=>$sc->tid));
    }
    
    
    public function pool_activities(){

        $first_day_morn = strtotime(date('01-m-Y') . " 06:00:00");
        $first_day_eve = strtotime(date('01-m-Y') . " 23:59:59");
        $last_day_eve = strtotime(date('t-m-Y') . " 23:59:59");

    
        $pool = Pool::select('id','status','up_id','item','customer','qty','p_time','branch','user','type')->whereBetween('p_time',array($first_day_morn,$last_day_eve))->orderBy('id','DESC')->get();

        if($pool->count() > 0){
            foreach($pool as $i=>$val){
                $d[$i]['id'] = $val->id;
                $d[$i]['up_id'] = $val->up_id;
                $d[$i]['status'] = $val->status;
                $d[$i]['type'] = ucfirst($val->type);
                $d[$i]['item'] = Items::find($val->item)->item_desc;
                $d[$i]['customer'] = (Customers::find($val->customer)->org) ? Customers::find($val->customer)->org : Customers::find($val->customer)->f_name . ' ' . Customers::find($val->customer)->s_name;
                $d[$i]['qty'] = abs($val->qty);
                $d[$i]['p_time'] = date("d-m-y h:i:s",$val->p_time);
                $d[$i]['branch'] = Branches::find($val->branch)->branch;
                $d[$i]['user'] = User::find($val->user)->lname;
            }
        }else{
            $d = [];
        }
        
    
        echo json_encode($d);
    
    }

    public function detailed_search_activities_report(Request $request){

        $customer = $request->customer;
        $type = strtolower($request->type);
        $cust_arr = explode(" ",$customer);
        $cust_arr2 = explode("-",$customer);

        $exp_dates = explode("to",$request->dates);
        $from = strtotime(str_replace(' ', '', $exp_dates[0]). " 06:00:00");
        $to = strtotime(str_replace(' ', '', $exp_dates[1]). " 23:59:59");

        if(!empty($customer)){
            if($cust_arr[0] != "-"){
                if(!empty($cust_arr2[2])){
                    $cust_id = Customers::select('id')->where('member_no',ltrim($cust_arr2[2]))->where('f_name',$cust_arr[0])->where('s_name',$cust_arr[1])->orderBy('id','DESC')->limit(1)->first()->id;
                }else{
                    $cust_id = Customers::select('id')->where('f_name',$cust_arr[0])->where('s_name',$cust_arr[1])->orderBy('id','DESC')->limit(1)->first()->id;
                }
            }else{
                //Trim white space on the left side
                $cust_id = Customers::select('id')->where('org',ltrim($cust_arr2[1]))->orderBy('id','DESC')->limit(1)->first()->id;
            }  
        }else{
            $cust_id = "";
        }
          
        if(!empty($type)){
            if(!empty($customer)){
                $pool = Pool::select('id','status','item','customer','qty','p_time','branch','user','type')->where('type',$type)->where('customer',$cust_id)->whereBetween('p_time',array($from,$to))->orderBy('id','DESC')->get();
            }else{
                $pool = Pool::select('id','status','item','customer','qty','p_time','branch','user','type')->where('type',$type)->whereBetween('p_time',array($from,$to))->orderBy('id','DESC')->get();
            }
        }else{
            if(!empty($customer)){
                $pool = Pool::select('id','status','item','customer','qty','p_time','branch','user','type')->where('customer',$cust_id)->whereBetween('p_time',array($from,$to))->orderBy('id','DESC')->get();
            }else{
                $pool = Pool::select('id','status','item','customer','qty','p_time','branch','user','type')->whereBetween('p_time',array($from,$to))->orderBy('id','DESC')->get();
            }
        }
           

        if($pool->count() > 0){
            foreach($pool as $i=>$val){
                $d[$i]['id'] = $val->id;
                $d[$i]['up_id'] = $val->up_id;
                $d[$i]['status'] = $val->status;
                $d[$i]['type'] = ucfirst($val->type);
                $d[$i]['item'] = Items::find($val->item)->item_desc;
                $d[$i]['customer'] = (Customers::find($val->customer)->org) ? Customers::find($val->customer)->org : Customers::find($val->customer)->f_name . ' ' . Customers::find($val->customer)->s_name;
                $d[$i]['qty'] = abs($val->qty);
                $d[$i]['p_time'] = date("d-m-Y H:i:s",$val->p_time);
                $d[$i]['branch'] = Branches::find($val->branch)->branch;
                $d[$i]['user'] = User::find($val->user)->lname;
            }
        }else{
            $d = [];
        }
        
    
        echo json_encode($d);
    }

    public function search_activities_report(Request $request){

        $customer = $request->customer;
        $cust_arr = explode(" ",$customer);
        $cust_arr2 = explode("-",$customer);
        
        if($cust_arr[0] != "-"){
            if(!empty($cust_arr2[2])){
                $cust_id = Customers::select('id')->where('member_no',ltrim($cust_arr2[2]))->where('f_name',$cust_arr[0])->where('s_name',$cust_arr[1])->orderBy('id','DESC')->limit(1)->first()->id;
            }else{
                $cust_id = Customers::select('id')->where('f_name',$cust_arr[0])->where('s_name',$cust_arr[1])->orderBy('id','DESC')->limit(1)->first()->id;
            }
        }else{
            //Trim white space on the left side
            $cust_id = Customers::select('id')->where('org',ltrim($cust_arr2[1]))->orderBy('id','DESC')->limit(1)->first()->id;
        }    

        $pool = Pool::select('id','status','item','customer','qty','p_time','branch','user','type')->where('customer',$cust_id)->orderBy('id','DESC')->get();
        
        if($pool->count() > 0){
            foreach($pool as $i=>$val){
                $d[$i]['id'] = $val->id;
                $d[$i]['up_id'] = $val->up_id;
                $d[$i]['status'] = $val->status;
                $d[$i]['type'] = ucfirst($val->type);
                $d[$i]['item'] = Items::find($val->item)->item_desc;
                $d[$i]['customer'] = (Customers::find($val->customer)->org) ? Customers::find($val->customer)->org : Customers::find($val->customer)->f_name . ' ' . Customers::find($val->customer)->s_name;
                $d[$i]['qty'] = abs($val->qty);
                $d[$i]['p_time'] = date("d-m-Y H:i:s",$val->p_time);
                $d[$i]['branch'] = Branches::find($val->branch)->branch;
                $d[$i]['user'] = User::find($val->user)->lname;
            }
        }else{
            $d = [];
        }
    
        echo json_encode($d);
    }

    public function delete_activity_purchase(Request $request){
            $pool = Pool::find($request->id);
            

            $pool_chk = Pool::where('type','allocation')->where('event',$pool->event)->where('item',$pool->item)->where('status',1)->sum('qty');

            if($pool_chk == 0){

                $pool->status = 0;
                $pool->save();

                $sc = ShoppingCart::find($pool->sc_id);
                $sc->type = "tender";
                $sc->save();
                echo json_encode($pool);

            }else{
                echo json_encode(array('status'=>0));
            }
            
    }

    public function delete_activity_allocation(Request $request){

        $pool = Pool::find($request->id);
        $pool->status = 0;
        $pool->save();

        $pool_chk = Pool::select('id')->where('customer',$pool->customer)->where('type','collection')->where('event',$pool->event)->where('item',$pool->item)->get();
        if($pool_chk->count() == 0){

            $acc = Accounts::select('id','status')->where('customer',$pool->customer)->where('item',$pool->item)->where('event',$pool->event)->orderBy('id','desc')->first();
            $acc->status = 0;
            $acc->save();
            
            echo json_encode($pool);
        }else{
            echo json_encode(array('status'=>0));

        }

    }

    public function pool_reports(){
    
        $pool = Pool::select('id','item')->groupBy('item')->get();

        $br = Branches::select('id')->where('curr',1)->limit(1)->first();
        
        if($pool->count() > 0){
            foreach($pool as $i=>$val){

                $d[$i]['id'] = $val->id;
                $d[$i]['item'] = Items::find($val->item)->item_desc;
    
                $sc_qty_ret = ShoppingCart::where('item',$val->item)->where('branch',$br->id)->where('status',4)->where('type','return')->sum('qty');
                $sc_qty = ShoppingCart::where('item',$val->item)->where('branch',$br->id)->where('status',2)->where('type','tender')->sum('qty');
                $gds_qty = Goods::where('item',$val->item)->where('branch',$br->id)->where('status',1)->sum('qty');
                
                $in_stock = $gds_qty - $sc_qty + $sc_qty_ret;
                $collected = abs(Pool::where('item',$val->item)->where('branch',$br->id)->where('type','collection')->where('status',1)->sum('qty'));
                $d[$i]['in_stock'] = number_format($in_stock + abs($collected));
                $d[$i]['purchased'] = number_format(Pool::where('item',$val->item)->where('branch',$br->id)->where('status',1)->where('type','purchase')->sum('qty'));
                $purchased = Pool::where('item',$val->item)->where('branch',$br->id)->where('type','purchase')->where('status',1)->sum('qty');
                $d[$i]['allocated'] = number_format(Pool::where('item',$val->item)->where('branch',$br->id)->where('status',1)->where('type','allocation')->sum('qty'));
                $d[$i]['collected'] = number_format(abs(Pool::where('item',$val->item)->where('branch',$br->id)->where('status',1)->where('type','collection')->sum('qty')));
                
                $d[$i]['act_in_stock'] =  number_format($in_stock);
                //Changed to Unissued
                $d[$i]['in_store'] =  number_format($purchased - abs($collected));
                
                
            }
        }else{
            $d = [];
        }
        
    
        echo json_encode($d);
    
    }

    public function search_pool_report(Request $request){
        
        $item = Items::select('id')->where('item_desc',$request->item)->get();
        if($item->count() > 0){

            foreach($item as $i=>$val){
                $pool = Pool::select('id','item')->groupBy('item')->get();

                $br = Branches::select('id')->where('curr',1)->limit(1)->first();
                
                    $d[$i]['id'] = $val->id;
                    $d[$i]['item'] = Items::find($val->id)->item_desc;

                    $sc_qty_ret = ShoppingCart::where('item',$val->item)->where('branch',$br->id)->where('status',4)->where('type','return')->sum('qty');
                    $sc_qty = ShoppingCart::where('item',$val->id)->where('branch',$br->id)->where('status',2)->where('type','tender')->sum('qty');
                    $gds_qty = Goods::where('item',$val->id)->where('branch',$br->id)->where('status',1)->sum('qty');
                    
                    $in_stock = $gds_qty - $sc_qty + $sc_qty_ret;
                    $collected = abs(Pool::where('item',$val->id)->where('branch',$br->id)->where('type','collection')->where('status',1)->sum('qty'));
                    $d[$i]['in_stock'] = number_format($in_stock + abs($collected));
                    $d[$i]['purchased'] = number_format(Pool::where('item',$val->id)->where('branch',$br->id)->where('status',1)->where('type','purchase')->sum('qty'));
                    $purchased = Pool::where('item',$val->id)->where('branch',$br->id)->where('type','purchase')->where('status',1)->sum('qty');
                    $d[$i]['allocated'] = number_format(Pool::where('item',$val->id)->where('branch',$br->id)->where('status',1)->where('type','allocation')->sum('qty'));
                    $d[$i]['collected'] = number_format(abs(Pool::where('item',$val->id)->where('branch',$br->id)->where('status',1)->where('type','collection')->sum('qty')));
                    
                    $d[$i]['act_in_stock'] =  number_format($in_stock);
                     //Changed to Unissued
                    $d[$i]['in_store'] =  number_format($purchased);
                    
                    
                }
            
                echo json_encode($d);
            

        }
    }

    public function pool_reports_by_events(){

        $event = Pool::select('id','event')->groupBy('event')->where('status',1)->orderBy('id','desc')->get();

        $br = Branches::select('id')->where('curr',1)->limit(1)->first();

        if($event->count() > 0){

            foreach($event as $j=>$val_event){


                if(!empty($val_event->event)){
                
                    $pool = Pool::select('id','item')->groupBy('item')->get();
                
                    foreach($pool as $i=>$val){
    
                        $d[$j][$i]['id'] = $val->id;
                        $d[$j][$i]['item'] = Items::find($val->item)->item_desc;
    
                        $sc_qty_ret = ShoppingCart::where('item',$val->item)->where('branch',$br->id)->where('type','return')->where('status',4)->sum('qty');
                        $sc_qty = ShoppingCart::where('item',$val->item)->where('branch',$br->id)->where('type','tender')->where('status',2)->sum('qty');
                        $gds_qty = Goods::where('item',$val->item)->where('branch',$br->id)->where('status',1)->sum('qty');
                        
                        $in_stock = $gds_qty - $sc_qty + $sc_qty_ret;
                        $d[$j][$i]['in_stock'] = number_format($in_stock);
                        $d[$j][$i]['event'] = Events::find($val_event->event)->event;
                        $d[$j][$i]['purchased'] = number_format(Pool::where('item',$val->item)->where('event',$val_event->event)->where('branch',$br->id)->where('type','purchase')->where('status',1)->sum('qty'));
                        $d[$j][$i]['allocated'] = number_format(Pool::where('item',$val->item)->where('event',$val_event->event)->where('branch',$br->id)->where('type','allocation')->where('status',1)->sum('qty'));
                        $d[$j][$i]['collected'] = number_format(abs(Pool::where('item',$val->item)->where('event',$val_event->event)->where('branch',$br->id)->where('type','collection')->where('status',1)->sum('qty')));
                        $d[$j][$i]['act_in_stock'] =  number_format($in_stock - abs($d[$j][$i]['purchased']));
                        $d[$j][$i]['in_store'] =  number_format($in_stock - abs($d[$j][$i]['allocated']));
                       
                    }
    
                }
    
            }
        }else{
            $d = [];
        }
        
    
        echo json_encode($d);

    }

    public function search_pool_event_report(Request $request){

        
        $br = Branches::select('id')->where('curr',1)->limit(1)->first();

            $ev = Events::select('id')->where('event',$request->event)->first();

            if($ev){
                $ev_id = $ev->id;
            }else{
                $ev_id = "";
            }
            
            $pool = Pool::select('id','item')->groupBy('item')->orderBy('id','desc')->get();
            
            if($pool->count() > 0){
                foreach($pool as $i=>$val){

                    $d[$i]['id'] = $val->id;
                    $d[$i]['item'] = Items::find($val->item)->item_desc;
    
                    $sc_qty_ret = ShoppingCart::where('item',$val->item)->where('branch',$br->id)->where('type','return')->where('status',4)->sum('qty');
                    $sc_qty = ShoppingCart::where('item',$val->item)->where('branch',$br->id)->where('type','tender')->where('status',2)->sum('qty');
                    $gds_qty = Goods::where('item',$val->item)->where('branch',$br->id)->where('status',1)->sum('qty');
                    
                    $in_stock = $gds_qty - $sc_qty + $sc_qty_ret;
                    $d[$i]['in_stock'] = number_format($in_stock);
                    $d[$i]['event'] = Events::find($ev_id)->event;
                    $d[$i]['purchased'] = number_format(Pool::where('item',$val->item)->where('event',$ev_id)->where('branch',$br->id)->where('status',1)->where('type','purchase')->sum('qty'));
                    $d[$i]['allocated'] = number_format(Pool::where('item',$val->item)->where('event',$ev_id)->where('branch',$br->id)->where('status',1)->where('type','allocation')->sum('qty'));
                    $d[$i]['collected'] = number_format(abs(Pool::where('item',$val->item)->where('event',$ev_id)->where('status',1)->where('branch',$br->id)->where('type','collection')->sum('qty')));
                    $d[$i]['act_in_stock'] =  number_format($in_stock - abs($d[$i]['purchased']));
                    $d[$i]['in_store'] =  number_format($in_stock - abs($d[$i]['allocated']));
            
    
                }
            }else{
                $d = [];
            }

            

        echo json_encode($d);
    }
    
    public function upload_results_xls(Request $request){
       
        $file_name = $request->items_file->getClientOriginalName();
        $file_extension = File::extension($request->items_file->getClientOriginalName());
        $file_tmp_path = $request->items_file->getRealPath();
        $upload_file = 'uploads/' . time() . '_' . $file_name;

        $status_held = 0;
        
        if (move_uploaded_file($file_tmp_path, $upload_file)) {
            
            $objReader = IOFactory::createReaderForFile($upload_file);
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($upload_file);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            
            //echo $objWorksheet->getCell('G2')->getCalculatedValue();
            $n = 2;
            $tt_qty = 0;
            
            foreach ($objWorksheet->getRowIterator() as $row) {
                $qty =  $objWorksheet->getCell('I'.$n)->getCalculatedValue();
                $up_event = $objWorksheet->getCell('F'.$n);
                $up_item = $objWorksheet->getCell('H'.$n);

                if(!empty($qty)){
                    $tt_qty += $qty;
                }
                $n++;
            }

            $up_event = $objWorksheet->getCell('F2');
            $up_item = $objWorksheet->getCell('H2');
         

            $eventx = Events::select('id')->where('event',$up_event)->first();
            
            if($eventx){

                $itemx = Items::select('id')->where('item_desc',ltrim($up_item))->first();
                
                if($itemx){

                    $chk_pool_pur = Pool::where('event',$eventx->id)->where('item',$itemx->id)->where('type','purchase')->where('status',1)->sum('qty');
                    $chk_pool_alloc = Pool::where('event',$eventx->id)->where('item',$itemx->id)->where('type','allocation')->where('status',1)->sum('qty');

                    $chk_diff = $chk_pool_pur -  $chk_pool_alloc;
                    
                    if($chk_diff >= $tt_qty){
                       
                                                            ////////////********************DOUBLE CHECKS******************/////////////////////////////////////////
                                    
                                                                                                            
                                                            $x = 0;
                                                            $y = 0;
                                                            $status_ttl = "";
                                                            
                                                            foreach ($objWorksheet->getRowIterator() as $row) {
                                                                $cellIterator = $row->getCellIterator();
                                                                $cellIterator->setIterateOnlyExistingCells(false);
                                                                $data = array();
                                                                
                                                    
                                                                $member_no_ttl = "";
                                                                $member_fname_ttl = "";
                                                                $member_lname_ttl = "";
                                                                $member_email_ttl = "";
                                                                $member_phone_ttl = "";
                                                                $event_ttl = "";
                                                                $club_ttl = "";
                                                                $item_ttl = "";
                                                                $qty_ttl = "";
                                                                
                                                                
                                                                foreach ($cellIterator as $cell) {
                                                                    $data[] = $cell->getValue();
                                                                    
                                                                }   
            
                                                                if($x < 1){
                                                                    
                                                                    $member_no_ttl = $data[0];
                                                                    $member_fname_ttl = $data[1];
                                                                    $member_lname_ttl = $data[2];
                                                                    $member_email_ttl = $data[3];
                                                                    $member_phone_ttl = $data[4];
                                                                    $event_ttl = $data[5];
                                                                    $club_ttl = $data[6];
                                                                    $item_ttl = $data[7];
                                                                    $qty_ttl = $data[8];
                                                                    
                                                                }else{
                                                                    
                                                                        $member_no = str_replace(",","",$data[0]);
                                                                        $fname = strtolower(html_entity_decode($data[1], ENT_QUOTES, "UTF-8"));
                                                                        $lname = strtolower(html_entity_decode($data[2], ENT_QUOTES, "UTF-8"));
                                                                        $email = strtolower(html_entity_decode($data[3], ENT_QUOTES, "UTF-8"));
                                                                        $phone = strtolower(html_entity_decode($data[4], ENT_QUOTES, "UTF-8"));
                                                                        $event = strtolower(html_entity_decode($data[5], ENT_QUOTES, "UTF-8"));
                                                                        $club = strtolower(html_entity_decode($data[6], ENT_QUOTES, "UTF-8"));
                                                                        $item = strtolower(html_entity_decode($data[7], ENT_QUOTES, "UTF-8"));
                                                                        $qty = ($data[8]) ? str_replace(",","",$data[8]) : 0;
            
                                                                        if(!empty($item)){

                                                                            //Check if Item exists
                                                                            $itemx = Items::select('id')->where('item_desc',ltrim($item))->first();
                                        
                                                                            if($itemx){
    
                                                                                $item_id = $itemx->id;
                                                                                $clubx = Clubs::select('id')->where('club',$club)->first();
                                                        
                                                                                if($clubx){
                                                        
                                                                                    $club_id = $clubx->id;
                                                        
                                                                                }else{
                                                                                    //Create new Club if it doesnt exist
                                                                                    $new_club = new Clubs();
                                                                                    $new_club->club = $club;
                                                                                    $new_club->status = 1;
                                                                                    $new_club->save();
                                                        
                                                                                    $club_id = Clubs::select('id')->where('status',1)->orderBy('id','DESC')->limit(1)->first()->id;
                                                                                
                                                                                }
                                                        
                                                                                if(!empty($member_no)){
                                                                                    $custx = Customers::select('id')->where('member_no',$member_no)->first();
                                                                                }else{
                                                                                    $custx = Customers::select('id')->where('f_name',$fname)->where('s_name',$lname)->first();
                                                                                }
                                                                                
                                                                                if($custx){
                                                        
                                                                                    $cust_id = $custx->id;
                                                        
                                                                                }else{
                                                                                    //Create Customer if they dont exist
                                                                                    $new_cust = new Customers();
                                                                                    $new_cust->f_name = strtolower($fname);
                                                                                    $new_cust->s_name = strtolower($lname);
                                                                                    $new_cust->phone = strtolower($phone);
                                                                                    $new_cust->email = strtolower($email);
                                                                                    $new_cust->member_no = $member_no;
                                                                                    $new_cust->status = 1;
                                                                                    $new_cust->save();
                                                        
                                                                                    $cust_id = Customers::select('id')->where('status',1)->orderBy('id','DESC')->limit(1)->first()->id;
                                                                                
                                                        
                                                                                }
                                                        
                                                                                $eventx = Events::select('id')->where('event',$event)->first();
                                                        
                                                                                if($eventx){
    
                                                                                    $event_id = $eventx->id;
    
    
                                                                                    $br = Branches::where('curr',1)->select('id')->orderBy('id','DESC')->limit(1)->first();
                                                                    
                                                                                
                                                                                    $chk_pool_pur = Pool::where('event',$event_id)->where('item',$item_id)->where('type','purchase')->where('status',1)->sum('qty');
                                                                                    $chk_pool_alloc = Pool::where('event',$event_id)->where('item',$item_id)->where('type','allocation')->where('status',1)->sum('qty');
    
                                                                                    $chk_diff = $chk_pool_pur -  $chk_pool_alloc;
    
                                                                                    if($chk_pool_pur !=0){
    
                                                                                        if($chk_diff > 0){
    
                                                                                            if($chk_diff >= $qty){
    
                                                                                                $pool = new Pool();
                                                                                                $pool->item = $item_id;
                                                                                                $pool->event = $event_id;
                                                                                                $pool->customer = $cust_id;
                                                                                                $pool->qty = $qty;
                                                                                                $pool->club = $club_id;
                                                                                                $pool->p_time = time();
                                                                                                $pool->branch = $br->id;
                                                                                                $pool->type = 'allocation';
                                                                                                $pool->user = $request->session()->get('uid');
                                                                                                $pool->save();
                                                                    
                                                                                                $ac = new Accounts();
                                                                                                $ac->customer = $cust_id;
                                                                                                $ac->item = $item_id;
                                                                                                $ac->event = $event_id;
                                                                                                $ac->qty = $qty;
                                                                                                $ac->acc_date = time();
                                                                                                $ac->branch = $br->id;
                                                                                                $ac->status = 1;
                                                                                                $ac_chk = Accounts::where('item',$item_id)->where('status',1)->where('customer',$cust_id)->sum('qty');
                                                                                                $ac->ttl_qty = $ac_chk + $qty;
                                                                                                $ac->save();

                                                                                                $status_held = 1;
                                                                                                //echo json_encode(array('status'=>1));
    
                                                                                            }else{
                                                                                                //Allocation Quantity
                                                                                                $status_held = 6;
                                                                                            }
                                                                                            
                                                                                        }else{
                                                                                            //Allocation Not Made
                                                                                            $status_held = 5;    
                                                                                        }
    
                                                                                    }else{
                                                                                        //No Purchase Made
                                                                                        $status_held = 4; 
                                                                                    }
    
                                                                                }else{
                                                                                    //Event not Found
                                                                                    $status_held = 2;
                                                                                }
    
                                                                            }else{
                                                                                //Item not found
                                                                                $status_held = 3;
                                                                            }
                                                                                    
                                                                        }
                                                    
                                                                }
                                                                
                                                                $x++;
                                                            }
                                                
                                                ////////////***********************DOUBLE CHECKS***************/////////////////////////////////////////
            

                    }else{ 
                        //Check Purchased qty 
                        $status_held = 7; 
                    }
                }else{
                    $status_held = 8;
                    //Item does not exist
                }
              
            }else{
                $status_held = 9;
                //Event does not exist
            }

            
        } else {
            $status_held = 0;
          
        }

        echo json_encode(array('status'=>$status_held));
        
    }

    
    
}

?>