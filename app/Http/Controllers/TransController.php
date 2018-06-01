<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

use App\Items;
use App\ShoppingCart;
use App\Transactions;
use App\Drawings;
use App\Drawer;
use App\User;
use App\Goods;
use App\Branches;
use App\Customers;
use App\Accounts;
use App\Tax;
use App\Pool;

use Auth;

class TransController extends Controller
{
    
    
    public function tender_trans(Request $request){

        $br = Branches::select('id')->where('curr',1)->limit(1)->first();
        
        $held_id = $request->held_id;
       
        if(!empty($request->customer)){
            
            $arr_cust = explode(" ",$request->customer);
            $arr_cust2 = explode("-",$request->customer);
           
            if($arr_cust[0] !="-"){
                    
                    if(count($arr_cust) >= 2){
                        $res_cust = Customers::select('id')->where('f_name',$arr_cust[0])->where('s_name',$arr_cust[1])->orderBy('id','desc')->first();
                        if($res_cust){
                            $cust_id = $res_cust->id;
                        }else{
                            $cust_id = "";
                        }
                    }else{
                        $cust_id = "";
                    }
                
                    
                }else{
                    $res_cust = Customers::select('id')->where('org',$arr_cust[1])->orderBy('id','desc')->first();
                    if($res_cust){
                        $cust_id = $res_cust->id;
                    }else{
                        $cust_id = "";
                    }
                }
            
            }else{
                $cust_id = "";
            }
        

        if(empty($held_id)){
            $sum_qty = ShoppingCart::where('status',1)->where('type','tender')->where('uid',$request->session()->get('uid'))->sum('qty');
            $sum_total = ShoppingCart::where('status',1)->where('type','tender')->where('uid',$request->session()->get('uid'))->sum('total');
            $sum_tax = ShoppingCart::where('status',1)->where('type','tender')->where('uid',$request->session()->get('uid'))->sum('tax');
            $sum_cost = ShoppingCart::where('status',1)->where('type','tender')->where('uid',$request->session()->get('uid'))->sum('cost');
        
        }else{
            $sum_qty = ShoppingCart::where('tid',$held_id)->sum('qty');
            $sum_total = ShoppingCart::where('tid',$held_id)->sum('total');
            $sum_tax = ShoppingCart::where('tid',$held_id)->sum('tax');
            $sum_cost = ShoppingCart::where('tid',$held_id)->sum('cost');
        
        }

        $cash = $request->cash;
        $change = $cash - $sum_total;
        
        if(empty($held_id)){
            $trans = new Transactions();
            $trans->cash = $cash;
            $trans->change = $change;
            $trans->total_gross = $sum_total;
            $trans->total_tax = $sum_tax;
            $trans->total_cost = $sum_cost;
            $trans->customer = $cust_id;
            $trans->trans_time = time();
            $trans->branch = $br->id; 
            $trans->type = "cash tender";
            $trans->user = $request->session()->get('uid');
            $trans->status = 1;
            $trans->no_items = $sum_qty;
            $trans->total = $sum_total;
            $trans->save();

            $trans_chk = Transactions::select('id')->where('user',$request->session()->get('uid'))->where('status',1)->orderBy('id','desc')->first();

            $get_sc = ShoppingCart::select('id','item','qty')->where('type','tender')->where('uid',$request->session()->get('uid'))->where('status',1)->get();
            
            foreach($get_sc as $val_sc){
                
                $up_sc = ShoppingCart::find($val_sc->id);
                $up_sc->tid = $trans_chk->id;
                $up_sc->status = 2;
                $up_sc->save();

            }

            $up_trans = Transactions::find($trans_chk->id);
            $receipt = str_pad(date('m',time()) . $trans_chk->id, 7, "0", STR_PAD_LEFT);
            $up_trans->receipt_no = $receipt;
            $up_trans->save();

        }else{

            $trans = Transactions::find($held_id);
            $trans->cash = $cash;
            $trans->change = $change;
            $trans->total_gross = $sum_total;
            $trans->total_tax = $sum_tax;
            $trans->total_cost = $sum_cost;
            $trans->trans_time = time();
            $trans->customer = $cust_id;
            $trans->branch = $br->id;
            $trans->type = "cash tender";
            $trans->user = $request->session()->get('uid');
            $trans->status = 1;
            $trans->no_items = $sum_qty;
            $trans->total = $sum_total;
            $trans->save();

            $receipt = $trans->receipt_no;

        }
        
        $datax =  str_pad("NOVEL GOLF SHOP", 40, " ", STR_PAD_BOTH). PHP_EOL;
        $datax .= str_pad("P.O. BOX 52399 - 00100", 40, " ", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("EMAIL: novelgolf@gmail.com", 40, " ", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("_", 39, "_", STR_PAD_BOTH) . PHP_EOL;
        $datax .= "                                        " . PHP_EOL;
        $datax .= str_pad("SALE RECEIPT - CASH", 40, " ", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("_", 39, "_", STR_PAD_BOTH) . PHP_EOL;
        $datax .= "                                        " . PHP_EOL;
        $datax .= str_pad("DATE: " . date("d-m-Y",time()) . " TIME: " . date("h:i:s",time()),40, " ", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("RECEIPT #: " .  $receipt, 40, " ", STR_PAD_BOTH) . PHP_EOL;
        
        $datax .= str_pad("-", 39, "-", STR_PAD_BOTH) . PHP_EOL;
        $datax .= "ITEM                QTY   PRICE  AMOUNT" . PHP_EOL;
        $datax .= str_pad("-", 39, "-", STR_PAD_BOTH) . PHP_EOL;
        
        if(empty($held_id)){
            $get_sc_rcpt = ShoppingCart::select('item','qty','price','total','tax')->where('type','tender')->where('tid',$trans_chk->id)->where('status',2)->get();
        }else{
            $get_sc_rcpt = ShoppingCart::select('item','qty','price','total','tax')->where('type','tender')->where('tid',$held_id)->get();
        }
       
        foreach($get_sc_rcpt as $val_sc_rcpt){
            $rcpt_item = $val_sc_rcpt->item;
           
            $item_desc = Items::find($rcpt_item);
            $rcpt_qty = $val_sc_rcpt->qty;
            $rcpt_price = $val_sc_rcpt->price;
            $rcpt_total = $val_sc_rcpt->total;
            $rcpt_tax = $val_sc_rcpt->tax;
        $datax .= wordwrap($item_desc->item_desc . PHP_EOL, 40, PHP_EOL, true);
        $datax .= str_pad($rcpt_qty . " x " . $rcpt_price . "   " .$rcpt_total . ".00", 40, " ", STR_PAD_LEFT) . PHP_EOL;

            /*
            *Update Item quantity
            */
            $item = Items::find($rcpt_item);

            $new_item_qty = $item->qty - $rcpt_qty;
            //$item->qty = $new_item_qty;
            //$item->save();
            
            
        }
        $datax .= str_pad("=", 39, "=", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("TOTAL NO OF ITEMS:", 20, " ", STR_PAD_RIGHT) . str_pad($sum_qty, 20, " ", STR_PAD_LEFT) . PHP_EOL;
        $datax .= str_pad("TENDER:", 20, " ", STR_PAD_RIGHT) . str_pad($sum_total.".00", 20, " ", STR_PAD_LEFT) . PHP_EOL;
        $datax .= str_pad("-", 39, "-", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("CASH:", 20, " ", STR_PAD_RIGHT) . str_pad($cash.".00", 20, " ", STR_PAD_LEFT) . PHP_EOL;

        $datax .= str_pad("CHANGE:", 20, " ", STR_PAD_RIGHT) . str_pad($change.".00", 20, " ", STR_PAD_LEFT) . PHP_EOL;

        
        /**
         * TAX LOGIC HERE
         * SELECT item, SUM(tax) AS total_tax FROM shopping_cart WHERE tid='156' GROUP BY tax;
         */
        $datax .= str_pad("-", 39, "-", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("You were served by  : " . ucfirst($request->session()->get('fname')) . " " . ucfirst($request->session()->get('lname')), 40, " ",  STR_PAD_RIGHT) . PHP_EOL;
        $datax .= str_pad("-", 39, "-", STR_PAD_BOTH) . PHP_EOL;

        if($get_sc_rcpt->count() > 0){

            $time = time();
            $myfile = fopen('C:\Users\oscar\Documents\pool\in\ ' . $time . '.txt', "w") or die("Unable to open file!");
            fwrite($myfile, $datax);
            fclose($myfile);

        }
  
        return json_encode(array('status'=>1,'change'=>$change,'new_item_qty'=>$new_item_qty));
    }

    public function new_tender_trans(Request $request){
        
        $br = Branches::select('id','branch')->where('curr',1)->limit(1)->first();
        $held_id = $request->held_id;
        $type = $request->type;
        $discount = $request->discount;
        $customer = $request->customer;
        $cust_arr = explode(" ",$customer);
        $cust_arr2 = explode("-",$customer);
        
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

        
        

        if(empty($held_id)){
            $sum_qty = ShoppingCart::where('status',1)->where('type','tender')->where('uid',$request->session()->get('uid'))->sum('qty');
            $sum_total = ShoppingCart::where('status',1)->where('type','tender')->where('uid',$request->session()->get('uid'))->sum('total');
            $sum_tax = ShoppingCart::where('status',1)->where('type','tender')->where('uid',$request->session()->get('uid'))->sum('tax');
            $sum_cost = ShoppingCart::where('status',1)->where('type','tender')->where('uid',$request->session()->get('uid'))->sum('cost');
        
        }else{
            $sum_qty = ShoppingCart::where('tid',$held_id)->sum('qty');
            $sum_total = ShoppingCart::where('tid',$held_id)->sum('total');
            $sum_tax = ShoppingCart::where('tid',$held_id)->sum('tax');
            $sum_cost = ShoppingCart::where('tid',$held_id)->sum('cost');
        
        }

       

        if($type == "cash"){

            $cash = $request->pay_val;
            $change = $cash - ($sum_total - $discount);
            $ref_no = "";
        }elseif($type == "account"){
            $cash = 0;
            $ref_no = "";
            $change = 0;
        }elseif($type == "xchange"){
            $gds = Goods::select('cost','qty')->where('receipt_no',$request->pay_val)->get();
            foreach($gds as $val_gds){
                $cost = $val_gds->cost;
                $goods_qty = $val_gds->qty;
                $total_cost = $goods_qty * $cost;
                $cash = $total_cost;
                $ref_no = strtoupper($request->pay_val);
                $change = $total_cost - ($sum_total - $discount);
            }
        }else{
            $cash = $sum_total;
            $ref_no = strtoupper($request->pay_val);
            $change = 0;
        }


        if(empty($held_id)){

            $trans = new Transactions();
            $trans->cash = $cash;
            $trans->change = $change;
            $trans->total_gross = $sum_total;
            $trans->total_tax = $sum_tax;
            $trans->total_cost = $sum_cost;
            $trans->customer = $cust_id;
            $trans->trans_time = time();
            $trans->branch = $br->id; 
            $trans->type = $type . " tender";
            $trans->user = $request->session()->get('uid');
            $trans->status = 1;
            $trans->discount = $request->discount;
            $trans->ref_no = $ref_no;
            $trans->no_items = $sum_qty;
            $trans->total = $sum_total;
            $trans->save();

            $trans_chk = Transactions::select('id')->where('user',$request->session()->get('uid'))->where('status',1)->orderBy('id','desc')->first();

            $get_sc = ShoppingCart::select('id','item','qty')->where('type','tender')->where('uid',$request->session()->get('uid'))->where('status',1)->get();
            
            foreach($get_sc as $val_sc){
                
                $up_sc = ShoppingCart::find($val_sc->id);
                $up_sc->tid = $trans_chk->id;
                $up_sc->customer = $cust_id;
                $up_sc->status = 2;
                $up_sc->save();

            }

            $up_trans = Transactions::find($trans_chk->id);
            $receipt = str_pad(date('m',time()) . $br->id . $request->session()->get('uid') . $trans_chk->id, 9, "0", STR_PAD_LEFT);
            $up_trans->receipt_no = $receipt;
            $up_trans->save();

        }else{

            $trans = Transactions::find($held_id);
            $trans->cash = $cash;
            $trans->change = $change;
            $trans->total_gross = $sum_total;
            $trans->total_tax = $sum_tax;
            $trans->total_cost = $sum_cost;
            $trans->trans_time = time();
            $trans->customer = $cust_id;
            $trans->branch = $br->id;
            $trans->type = $type . " tender";
            $trans->user = $request->session()->get('uid');
            $trans->status = 1;
            $trans->no_items = $sum_qty;
            $trans->total = $sum_total;
            $trans->save();

            $receipt = $trans->receipt_no;

        }
        
        $datax =  str_pad("NOVEL GOLF SHOP", 50, " ", STR_PAD_BOTH). PHP_EOL;
        $datax .= str_pad("P.O. BOX 52399 - 00100", 50, " ", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("EMAIL: novelgolf@gmail.com", 50, " ", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("_", 43, "_", STR_PAD_BOTH) . PHP_EOL;
        $datax .= "                                        " . PHP_EOL;
        $datax .= str_pad("SALE RECEIPT - " . strtoupper($type), 50, " ", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("_", 43, "_", STR_PAD_BOTH) . PHP_EOL;
        $datax .= "                                        " . PHP_EOL;
        $datax .= str_pad("DATE: " . date("d-m-Y",time()) . " TIME: " . date("h:i:s",time()),50, " ", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("RECEIPT #: " .  $receipt, 50, " ", STR_PAD_BOTH) . PHP_EOL;
        
        if($type != "cash"){

            $datax .= str_pad("REF #: " .  $ref_no, 50, " ", STR_PAD_BOTH) . PHP_EOL;
                
        }

        $datax .= str_pad("BRANCH: " .  $br->branch, 40, " ", STR_PAD_BOTH) . PHP_EOL;
        
        $datax .= str_pad("-", 43, "-", STR_PAD_BOTH) . PHP_EOL;
        $datax .= "ITEM                     QTY   PRICE  AMOUNT" . PHP_EOL;
        $datax .= str_pad("-", 43, "-", STR_PAD_BOTH) . PHP_EOL;
        
        if(empty($held_id)){
            $get_sc_rcpt = ShoppingCart::select('item','qty','price','total','tax')->where('type','tender')->where('tid',$trans_chk->id)->where('status',2)->get();
        }else{
            $get_sc_rcpt = ShoppingCart::select('item','qty','price','total','tax')->where('type','tender')->where('tid',$held_id)->get();
        }
       
        foreach($get_sc_rcpt as $val_sc_rcpt){
            $rcpt_item = $val_sc_rcpt->item;
           
            $item_desc = Items::find($rcpt_item);
            $rcpt_qty = $val_sc_rcpt->qty;
            $rcpt_price = $val_sc_rcpt->price;
            $rcpt_total = $val_sc_rcpt->total;
            $rcpt_tax = $val_sc_rcpt->tax;
        $datax .= wordwrap($item_desc->item_desc . PHP_EOL, 43, PHP_EOL, true);
        $datax .= str_pad($rcpt_qty . " x " . number_format($rcpt_price) . "   " .number_format($rcpt_total) . ".00", 49, " ", STR_PAD_LEFT) . PHP_EOL;
   
            
        }
        $datax .= str_pad("=", 27, "=", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("TOTAL NO OF ITEMS:", 29, " ", STR_PAD_RIGHT) . str_pad(number_format($sum_qty), 20, " ", STR_PAD_LEFT) . PHP_EOL;
        if($discount !=0){
            $datax .= str_pad("DISCOUNT:", 29, " ", STR_PAD_RIGHT) . str_pad("(".number_format($discount).".00)", 20, " ", STR_PAD_LEFT) . PHP_EOL;
        }
        $datax .= str_pad("TENDER:", 34, " ", STR_PAD_RIGHT) . str_pad(number_format($sum_total - $discount).".00", 20, " ", STR_PAD_LEFT) . PHP_EOL;
        $datax .= str_pad("-", 39, "-", STR_PAD_BOTH) . PHP_EOL;
        if($type != "cash"){

            $datax .= str_pad("TOTAL:", 34, " ", STR_PAD_RIGHT) . str_pad(number_format($sum_total - $discount).".00", 20, " ", STR_PAD_LEFT) . PHP_EOL;

        }else{
            $datax .= str_pad("CASH:", 29, " ", STR_PAD_RIGHT) . str_pad(number_format($cash).".00", 20, " ", STR_PAD_LEFT) . PHP_EOL;

        }
        
        $datax .= str_pad("CHANGE:", 34, " ", STR_PAD_RIGHT) . str_pad(number_format($change).".00", 20, " ", STR_PAD_LEFT) . PHP_EOL;

        
        /**
         * TAX LOGIC HERE
         * SELECT item, SUM(tax) AS total_tax FROM shopping_cart WHERE tid='156' GROUP BY tax;
         */
        $datax .= str_pad("-", 43, "-", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("You were served by  : " . ucfirst($request->session()->get('fname')) . " " . ucfirst($request->session()->get('lname')), 40, " ",  STR_PAD_RIGHT) . PHP_EOL;
        $datax .= str_pad("-", 43, "-", STR_PAD_BOTH) . PHP_EOL;

        if($get_sc_rcpt->count() > 0){

            $time = time();
            $myfile = fopen('C:\Users\oscar\Documents\pool\in\ ' . $time . '.txt', "w") or die("Unable to open file!");
            fwrite($myfile, $datax);
            fclose($myfile);

        }
  
        return json_encode(array('status'=>1,'change'=>$change));
        
    }

    

    public function new_return_trans(Request $request){

        $pay_val = $request->pay_val;
        $rtrn_opt = $request->rtrn_opt;
        $scnd_pay_val = $request->scnd_pay_val;
        $refund_amt = $request->refund_amt;
        $discount = $request->discount;
        $customer = $request->customer;
        $type = $request->type;

        

        $br = Branches::select('id','branch')->where('curr',1)->limit(1)->first();
        

            $customer = $request->customer;
            $cust_arr = explode(" ",$customer);
            $cust_arr2 = explode("-",$customer);
            
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

            $sum_qty = ShoppingCart::where('status',1)->where('type','tender')->where('uid',$request->session()->get('uid'))->sum('qty');
            $sum_total = ShoppingCart::where('status',1)->where('type','tender')->where('uid',$request->session()->get('uid'))->sum('total');
            $sum_tax = ShoppingCart::where('status',1)->where('type','tender')->where('uid',$request->session()->get('uid'))->sum('tax');
            $sum_cost = ShoppingCart::where('status',1)->where('type','tender')->where('uid',$request->session()->get('uid'))->sum('cost');
            
            

            if($rtrn_opt !=""){

                
                $amt_one = $refund_amt;
                $amt_two = ($sum_total - $discount) - $refund_amt;

                if($amt_one > $amt_two){

                    $ratio = $amt_one  / $amt_two;

                }else{
                    $ratio = $amt_two  / $amt_one;
                }

                $ttl_ratio = $ratio + 1;
                $first_cost = round((1 * $sum_cost) / $ttl_ratio);
                $second_cost = round(($ratio * $sum_cost) / $ttl_ratio);
                $first_tax = round((1 * $sum_tax) / $ttl_ratio);
                $second_tax = round(($ratio * $sum_tax) / $ttl_ratio);
                $first_qty = round((1 * $sum_qty) / $ttl_ratio);
                $second_qty = round(($ratio * $sum_qty) / $ttl_ratio);
                $first_discount = round((1 * $discount) / $ttl_ratio);
                $second_discount = round(($ratio * $discount) / $ttl_ratio);

                if($rtrn_opt =="cash tender"){

                    $change = $scnd_pay_val - $amt_two;

                }else{

                    $change = 0;

                }


                    $trans = new Transactions();
                    $trans->cash = $amt_one;
                    $trans->change = $change;
                    $trans->total_gross = $sum_total;
                    $trans->total_tax = $first_tax;
                    $trans->total_cost = $first_cost;
                    $trans->customer = $cust_id;
                    $trans->trans_time = time();
                    $trans->branch = $br->id; 
                    $trans->type = $type . " tender";
                    $trans->user = $request->session()->get('uid');
                    $trans->status = 1;
                    $trans->no_items = $first_qty;
                    $trans->discount = $first_discount;
                    $trans->total = $amt_one;
                    $trans->ref_no = strtoupper($pay_val);
                    $trans->save();
                    
                    $trans_chk = Transactions::select('id')->where('user',$request->session()->get('uid'))->where('status',1)->orderBy('id','desc')->first();

                    $get_sc = ShoppingCart::select('id','item','qty')->where('type','tender')->where('uid',$request->session()->get('uid'))->where('status',1)->get();
                    
                    foreach($get_sc as $val_sc){

                        $up_sc = ShoppingCart::find($val_sc->id);
                        $up_sc->tid = $trans_chk->id;
                        $up_sc->status = 2;
                        $up_sc->save();

                    }

                    $up_trans = Transactions::find($trans_chk->id);
                    $receipt = str_pad(date('m',time()) . $br->id . $request->session()->get('uid') . $trans_chk->id, 9, "0", STR_PAD_LEFT);
                    $up_trans->receipt_no = $receipt;
                    $up_trans->save();

                    
                    

                    $trans_two = new Transactions();
                    $trans_two->cash = $amt_two;
                    $trans_two->change = 0;
                    $trans_two->total_gross = 0;
                    $trans_two->total_tax = $second_tax;
                    $trans_two->total_cost = $second_cost;
                    $trans_two->customer = $cust_id;
                    $trans_two->trans_time = time();
                    $trans_two->branch = $br->id; 
                    $trans_two->type = $rtrn_opt;
                    $trans_two->user = $request->session()->get('uid');
                    $trans_two->status = 1;
                    $trans_two->no_items = $second_qty;
                    $trans_two->discount = $second_discount;
                    $trans_two->total = $amt_two;
                    $trans_two->receipt_no = $receipt;
                    $trans_two->comment = $pay_val;
                    if($rtrn_opt !="cash tender"){
                        $trans_two->ref_no = strtoupper($scnd_pay_val);
                    }
                    
                    $trans_two->save();

                    $datax =  str_pad("NOVEL GOLF SHOP", 40, " ", STR_PAD_BOTH). PHP_EOL;
                    $datax .= str_pad("P.O. BOX 52399 - 00100", 40, " ", STR_PAD_BOTH) . PHP_EOL;
                    $datax .= str_pad("EMAIL: novelgolf@gmail.com", 40, " ", STR_PAD_BOTH) . PHP_EOL;
                    $datax .= str_pad("_", 39, "_", STR_PAD_BOTH) . PHP_EOL;
                    $datax .= "                                        " . PHP_EOL;
                    $datax .= str_pad("SALE RECEIPT - " . strtoupper($type), 40, " ", STR_PAD_BOTH) . PHP_EOL;
                    $datax .= str_pad("_", 39, "_", STR_PAD_BOTH) . PHP_EOL;
                    $datax .= "                                        " . PHP_EOL;
                    $datax .= str_pad("DATE: " . date("d-m-Y",time()) . " TIME: " . date("h:i:s",time()),40, " ", STR_PAD_BOTH) . PHP_EOL;
                    $datax .= str_pad("RECEIPT #: " .  $receipt, 40, " ", STR_PAD_BOTH) . PHP_EOL;
                    $datax .= str_pad("BRANCH : " .  $br->branch, 40, " ", STR_PAD_BOTH) . PHP_EOL;
                    
                    $datax .= str_pad("-", 39, "-", STR_PAD_BOTH) . PHP_EOL;
                    $datax .= "ITEM                QTY   PRICE  AMOUNT" . PHP_EOL;
                    $datax .= str_pad("-", 39, "-", STR_PAD_BOTH) . PHP_EOL;
                    
                    if(empty($held_id)){
                        $get_sc_rcpt = ShoppingCart::select('item','qty','price','total','tax')->where('type','tender')->where('tid',$trans_chk->id)->where('status',2)->get();
                    }else{
                        $get_sc_rcpt = ShoppingCart::select('item','qty','price','total','tax')->where('type','tender')->where('tid',$held_id)->get();
                    }
                
                    foreach($get_sc_rcpt as $val_sc_rcpt){
                        $rcpt_item = $val_sc_rcpt->item;
                    
                        $item_desc = Items::find($rcpt_item);
                        $rcpt_qty = $val_sc_rcpt->qty;
                        $rcpt_price = $val_sc_rcpt->price;
                        $rcpt_total = $val_sc_rcpt->total;
                        $rcpt_tax = $val_sc_rcpt->tax;
                    $datax .= wordwrap($item_desc->item_desc . PHP_EOL, 40, PHP_EOL, true);
                    $datax .= str_pad($rcpt_qty . " x " . number_format($rcpt_price) . "   " .number_format($rcpt_total) . ".00", 40, " ", STR_PAD_LEFT) . PHP_EOL;

                        /*
                        *Update Item quantity
                        */
                        $item = Items::find($rcpt_item);

                        $new_item_qty = $item->qty - $rcpt_qty;
                        //$item->qty = $new_item_qty;
                    //$item->save();
                    
                    
                    }
                    $datax .= str_pad("=", 39, "=", STR_PAD_BOTH) . PHP_EOL;
                    $datax .= str_pad("TOTAL NO OF ITEMS:", 20, " ", STR_PAD_RIGHT) . str_pad($sum_qty, 20, " ", STR_PAD_LEFT) . PHP_EOL;
                    $datax .= str_pad("TENDER:", 20, " ", STR_PAD_RIGHT) . str_pad("(" . $discount . ")".".00", 20, " ", STR_PAD_LEFT) . PHP_EOL;
                    $datax .= str_pad("TENDER:", 20, " ", STR_PAD_RIGHT) . str_pad($sum_total - $discount.".00", 20, " ", STR_PAD_LEFT) . PHP_EOL;
                    $datax .= str_pad("-", 39, "-", STR_PAD_BOTH) . PHP_EOL;
                    $datax .= str_pad(strtoupper($type) . " TENDER : ", 20, " ", STR_PAD_RIGHT) . str_pad($amt_one .".00", 20, " ", STR_PAD_LEFT) . PHP_EOL;
                    $datax .= str_pad(strtoupper($rtrn_opt) . " : ", 20, " ", STR_PAD_RIGHT) . str_pad($amt_two .".00", 20, " ", STR_PAD_LEFT) . PHP_EOL;

                    $datax .= str_pad("CHANGE:", 20, " ", STR_PAD_RIGHT) . str_pad($change.".00", 20, " ", STR_PAD_LEFT) . PHP_EOL;

                    
                    /**
                     * TAX LOGIC HERE
                     * SELECT item, SUM(tax) AS total_tax FROM shopping_cart WHERE tid='156' GROUP BY tax;
                     */
                    $datax .= str_pad("-", 39, "-", STR_PAD_BOTH) . PHP_EOL;
                    $datax .= str_pad("You were served by  : " . ucfirst($request->session()->get('fname')) . " " . ucfirst($request->session()->get('lname')), 40, " ",  STR_PAD_RIGHT) . PHP_EOL;
                    $datax .= str_pad("-", 39, "-", STR_PAD_BOTH) . PHP_EOL;

                    if($get_sc_rcpt->count() > 0){

                        $time = time();
                        $myfile = fopen('C:\Users\oscar\Documents\pool\in\ ' . $time . '.txt', "w") or die("Unable to open file!");
                        fwrite($myfile, $datax);
                        fclose($myfile);

                    }

                    return json_encode(array('status'=>1,'change'=>$change));
        
            }else{
                    //Only Return Tender
                   
                    
                        $trans = new Transactions();
                        $trans->cash = $sum_total;
                        $trans->change = 0;
                        $trans->total_gross = $sum_total;
                        $trans->total_tax = $sum_tax;
                        $trans->total_cost = $sum_cost;
                        $trans->customer = $cust_id;
                        $trans->trans_time = time();
                        $trans->branch = $br->id; 
                        $trans->type = "return tender";
                        $trans->user = $request->session()->get('uid');
                        $trans->status = 1;
                        $trans->discount = $request->discount;
                        $trans->ref_no = $pay_val;
                        $trans->no_items = $sum_qty;
                        $trans->total = $sum_total;
                        $trans->save();

                        $trans_chk = Transactions::select('id')->where('user',$request->session()->get('uid'))->where('status',1)->orderBy('id','desc')->first();

                        $get_sc = ShoppingCart::select('id','item','qty')->where('type','tender')->where('uid',$request->session()->get('uid'))->where('status',1)->get();
                        
                        foreach($get_sc as $val_sc){
                        
                            $up_sc = ShoppingCart::find($val_sc->id);
                            $up_sc->tid = $trans_chk->id;
                            $up_sc->status = 2;
                            $up_sc->save();

                        }

                        $up_trans = Transactions::find($trans_chk->id);
                        $receipt = str_pad(date('m',time()) . $br->id . $request->session()->get('uid') . $trans_chk->id, 9, "0", STR_PAD_LEFT);
                        $up_trans->receipt_no = $receipt;
                        $up_trans->save();



                        $datax =  str_pad("NOVEL GOLF SHOP", 40, " ", STR_PAD_BOTH). PHP_EOL;
                        $datax .= str_pad("P.O. BOX 52399 - 00100", 40, " ", STR_PAD_BOTH) . PHP_EOL;
                        $datax .= str_pad("EMAIL: novelgolf@gmail.com", 40, " ", STR_PAD_BOTH) . PHP_EOL;
                        $datax .= str_pad("_", 39, "_", STR_PAD_BOTH) . PHP_EOL;
                        $datax .= "                                        " . PHP_EOL;
                        $datax .= str_pad("SALE RECEIPT - RETURN TENDER", 40, " ", STR_PAD_BOTH) . PHP_EOL;
                        $datax .= str_pad("_", 39, "_", STR_PAD_BOTH) . PHP_EOL;
                        $datax .= "                                        " . PHP_EOL;
                        $datax .= str_pad("DATE: " . date("d-m-Y",time()) . " TIME: " . date("h:i:s",time()),40, " ", STR_PAD_BOTH) . PHP_EOL;
                        $datax .= str_pad("RECEIPT #: " .  $receipt, 40, " ", STR_PAD_BOTH) . PHP_EOL;
                        $datax .= str_pad("RETURN REF #: " .  $pay_val, 40, " ", STR_PAD_BOTH) . PHP_EOL;
                        
                        $datax .= str_pad("BRANCH : " .  $br->branch, 40, " ", STR_PAD_BOTH) . PHP_EOL;
                         
                        $datax .= str_pad("-", 39, "-", STR_PAD_BOTH) . PHP_EOL;
                        $datax .= "ITEM                QTY   PRICE  AMOUNT" . PHP_EOL;
                        $datax .= str_pad("-", 39, "-", STR_PAD_BOTH) . PHP_EOL;
                        
                        if(empty($held_id)){
                            $get_sc_rcpt = ShoppingCart::select('item','qty','price','total','tax')->where('type','tender')->where('tid',$trans_chk->id)->where('status',2)->get();
                        }else{
                            $get_sc_rcpt = ShoppingCart::select('item','qty','price','total','tax')->where('type','tender')->where('tid',$held_id)->get();
                        }
                    
                        foreach($get_sc_rcpt as $val_sc_rcpt){
                            $rcpt_item = $val_sc_rcpt->item;
                        
                            $item_desc = Items::find($rcpt_item);
                            $rcpt_qty = $val_sc_rcpt->qty;
                            $rcpt_price = $val_sc_rcpt->price;
                            $rcpt_total = $val_sc_rcpt->total;
                            $rcpt_tax = $val_sc_rcpt->tax;
                        $datax .= wordwrap($item_desc->item_desc . PHP_EOL, 40, PHP_EOL, true);
                        $datax .= str_pad($rcpt_qty . " x " . number_format($rcpt_price) . "   " .number_format($rcpt_total) . ".00", 40, " ", STR_PAD_LEFT) . PHP_EOL;
                
                            
                        }
                        $datax .= str_pad("=", 39, "=", STR_PAD_BOTH) . PHP_EOL;
                        $datax .= str_pad("TOTAL NO OF ITEMS:", 20, " ", STR_PAD_RIGHT) . str_pad($sum_qty, 20, " ", STR_PAD_LEFT) . PHP_EOL;
                        if($discount !=0){
                            $datax .= str_pad("DISCOUNT:", 20, " ", STR_PAD_RIGHT) . str_pad("(".$discount.".00)", 20, " ", STR_PAD_LEFT) . PHP_EOL;
                        }
                        $datax .= str_pad("TENDER:", 20, " ", STR_PAD_RIGHT) . str_pad($sum_total - $discount.".00", 20, " ", STR_PAD_LEFT) . PHP_EOL;
                        $datax .= str_pad("-", 39, "-", STR_PAD_BOTH) . PHP_EOL;
                        $datax .= str_pad("CASH:", 20, " ", STR_PAD_RIGHT) . str_pad( $sum_total .".00", 20, " ", STR_PAD_LEFT) . PHP_EOL;

                        $datax .= str_pad("CHANGE:", 20, " ", STR_PAD_RIGHT) . str_pad(number_format(0).".00", 20, " ", STR_PAD_LEFT) . PHP_EOL;

                        
                        /**
                         * TAX LOGIC HERE
                         * SELECT item, SUM(tax) AS total_tax FROM shopping_cart WHERE tid='156' GROUP BY tax;
                         */
                        $datax .= str_pad("-", 39, "-", STR_PAD_BOTH) . PHP_EOL;
                        $datax .= str_pad("You were served by  : " . ucfirst($request->session()->get('fname')) . " " . ucfirst($request->session()->get('lname')), 40, " ",  STR_PAD_RIGHT) . PHP_EOL;
                        $datax .= str_pad("-", 39, "-", STR_PAD_BOTH) . PHP_EOL;

                        if($get_sc_rcpt->count() > 0){

                            $time = time();
                            $myfile = fopen('C:\Users\oscar\Documents\pool\in\ ' . $time . '.txt', "w") or die("Unable to open file!");
                            fwrite($myfile, $datax);
                            fclose($myfile);

                        }
                
                        return json_encode(array('status'=>1,'change'=>0));
                        

            }
    }

    public function tender_split_trans(Request $request){
        
        $amt_one = $request->amt_one;
        $amt_two = $request->amt_two;
        $change = $request->change;
        $typeOne = $request->typeOne;
        $typeTwo = $request->typeTwo;
        $refOne = $request->refOne;
        $refTwo = $request->refTwo;
        $discount = $request->discount;

        /*
        $arr = array(
            'amt_one'=>$amt_one,
            'amt_two'=>$amt_two,
            'change'=>$change,
            'typeOne'=>$typeOne,
            'typeTwo'=>$typeTwo,
            'refOne'=>$refOne,
            'refTwo'=>$refTwo
        );
        */
        

        

        $br = Branches::select('id','branch')->where('curr',1)->limit(1)->first();
        
       
            $customer = $request->customer;
            $cust_arr = explode(" ",$customer);
            $cust_arr2 = explode("-",$customer);
            
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
            

            $sum_qty = ShoppingCart::where('status',1)->where('type','tender')->where('uid',$request->session()->get('uid'))->sum('qty');
            $sum_total = ShoppingCart::where('status',1)->where('type','tender')->where('uid',$request->session()->get('uid'))->sum('total');
            $sum_tax = ShoppingCart::where('status',1)->where('type','tender')->where('uid',$request->session()->get('uid'))->sum('tax');
            $sum_cost = ShoppingCart::where('status',1)->where('type','tender')->where('uid',$request->session()->get('uid'))->sum('cost');
        
           
            if($amt_one > $amt_two){
                $ratio = $amt_one  / $amt_two;

                $ttl_ratio = $ratio + 1;

                $first_cost = round(($ratio * $sum_cost) / $ttl_ratio);
                $second_cost  = round((1 * $sum_cost) / $ttl_ratio);
                $first_tax = round(($ratio * $sum_tax) / $ttl_ratio) ;
                $second_tax = round((1 * $sum_tax) / $ttl_ratio);
                $first_qty = round(($ratio * $sum_qty) / $ttl_ratio);
                $second_qty = round((1 * $sum_qty) / $ttl_ratio);
                $first_discount = round(($ratio * $discount) / $ttl_ratio);
                $second_discount = round((1 * $discount) / $ttl_ratio);

            }else{
                $ratio = $amt_two  / $amt_one;

                $ttl_ratio = $ratio + 1;

                $first_cost = round((1 * $sum_cost) / $ttl_ratio);
                $second_cost = round(($ratio * $sum_cost) / $ttl_ratio);
                $first_tax = round((1 * $sum_tax) / $ttl_ratio);
                $second_tax = round(($ratio * $sum_tax) / $ttl_ratio);
                $first_qty = round((1 * $sum_qty) / $ttl_ratio);
                $second_qty = round(($ratio * $sum_qty) / $ttl_ratio);
                $first_discount = round((1 * $discount) / $ttl_ratio);
                $second_discount = round(($ratio * $discount) / $ttl_ratio);
                
            }
            
           
            

            $trans = new Transactions();
            $trans->cash = $amt_one;
            $trans->change = $change;
            $trans->total_gross = $sum_total;
            $trans->total_tax = $first_tax;
            $trans->total_cost = $first_cost;
            $trans->customer = $cust_id;
            $trans->trans_time = time();
            $trans->branch = $br->id; 
            $trans->type = $typeOne . " tender";
            $trans->user = $request->session()->get('uid');
            $trans->status = 1;
            $trans->no_items = $first_qty;
            $trans->discount = $first_discount;
            $trans->total = $amt_one;
            $trans->ref_no = strtoupper($refOne);
            $trans->save();
            
            $trans_chk = Transactions::select('id')->where('user',$request->session()->get('uid'))->where('status',1)->orderBy('id','desc')->first();

            $get_sc = ShoppingCart::select('id','item','qty')->where('type','tender')->where('uid',$request->session()->get('uid'))->where('status',1)->get();
            
            foreach($get_sc as $val_sc){

                $up_sc = ShoppingCart::find($val_sc->id);
                $up_sc->tid = $trans_chk->id;
                $up_sc->status = 2;
                $up_sc->save();

            }

            $up_trans = Transactions::find($trans_chk->id);
            $receipt = str_pad(date('m',time()) . $br->id . $request->session()->get('uid') . $trans_chk->id, 9, "0", STR_PAD_LEFT);
            $up_trans->receipt_no = $receipt;
            $up_trans->save();

            

            $trans_two = new Transactions();
            $trans_two->cash = $amt_two;
            $trans_two->change = 0;
            $trans_two->total_gross = 0;
            $trans_two->total_tax = $second_tax;
            $trans_two->total_cost = $second_cost;
            $trans_two->customer = $cust_id;
            $trans_two->trans_time = time();
            $trans_two->branch = $br->id; 
            $trans_two->type = $typeTwo . " tender";
            $trans_two->user = $request->session()->get('uid');
            $trans_two->status = 1;
            $trans_two->no_items = $second_qty;
            $trans_two->discount = $second_discount;
            $trans_two->total = $amt_two;
            $trans_two->receipt_no = $receipt;
            $trans_two->ref_no = strtoupper($refTwo);
            $trans_two->save();


        $datax =  str_pad("NOVEL GOLF SHOP", 40, " ", STR_PAD_BOTH). PHP_EOL;
        $datax .= str_pad("P.O. BOX 52399 - 00100", 40, " ", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("EMAIL: novelgolf@gmail.com", 40, " ", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("_", 39, "_", STR_PAD_BOTH) . PHP_EOL;
        $datax .= "                                        " . PHP_EOL;
        $datax .= str_pad("SALE RECEIPT - SPLIT ", 40, " ", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("_", 39, "_", STR_PAD_BOTH) . PHP_EOL;
        $datax .= "                                        " . PHP_EOL;
        $datax .= str_pad("DATE: " . date("d-m-Y",time()) . " TIME: " . date("h:i:s",time()),40, " ", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("RECEIPT #: " .  $receipt, 40, " ", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("BRANCH : " .  $br->branch, 40, " ", STR_PAD_BOTH) . PHP_EOL;
        
        $datax .= str_pad("-", 39, "-", STR_PAD_BOTH) . PHP_EOL;
        $datax .= "ITEM                QTY   PRICE  AMOUNT" . PHP_EOL;
        $datax .= str_pad("-", 39, "-", STR_PAD_BOTH) . PHP_EOL;
        
        if(empty($held_id)){
            $get_sc_rcpt = ShoppingCart::select('item','qty','price','total','tax')->where('type','tender')->where('tid',$trans_chk->id)->where('status',2)->get();
        }else{
            $get_sc_rcpt = ShoppingCart::select('item','qty','price','total','tax')->where('type','tender')->where('tid',$held_id)->get();
        }
       
        foreach($get_sc_rcpt as $val_sc_rcpt){
            $rcpt_item = $val_sc_rcpt->item;
           
            $item_desc = Items::find($rcpt_item);
            $rcpt_qty = $val_sc_rcpt->qty;
            $rcpt_price = $val_sc_rcpt->price;
            $rcpt_total = $val_sc_rcpt->total;
            $rcpt_tax = $val_sc_rcpt->tax;
        $datax .= wordwrap($item_desc->item_desc . PHP_EOL, 40, PHP_EOL, true);
        $datax .= str_pad($rcpt_qty . " x " . number_format($rcpt_price) . "   " .number_format($rcpt_total) . ".00", 40, " ", STR_PAD_LEFT) . PHP_EOL;

            /*
            *Update Item quantity
            */
            $item = Items::find($rcpt_item);

            $new_item_qty = $item->qty - $rcpt_qty;
            //$item->qty = $new_item_qty;
            //$item->save();
            
            
        }
        $datax .= str_pad("=", 39, "=", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("TOTAL NO OF ITEMS:", 20, " ", STR_PAD_RIGHT) . str_pad($sum_qty, 20, " ", STR_PAD_LEFT) . PHP_EOL;
        $datax .= str_pad("DISCOUNT:", 20, " ", STR_PAD_RIGHT) . str_pad("(" . $discount . ")".".00", 20, " ", STR_PAD_LEFT) . PHP_EOL;
        $datax .= str_pad("TENDER:", 20, " ", STR_PAD_RIGHT) . str_pad(number_format($sum_total - $discount).".00", 20, " ", STR_PAD_LEFT) . PHP_EOL;
        $datax .= str_pad("-", 39, "-", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad(strtoupper($typeOne) . " : ", 20, " ", STR_PAD_RIGHT) . str_pad(number_format($amt_one) .".00", 20, " ", STR_PAD_LEFT) . PHP_EOL;
        $datax .= str_pad(strtoupper($typeTwo) . " : ", 20, " ", STR_PAD_RIGHT) . str_pad(number_format($amt_two) .".00", 20, " ", STR_PAD_LEFT) . PHP_EOL;

        $datax .= str_pad("CHANGE:", 20, " ", STR_PAD_RIGHT) . str_pad($change.".00", 20, " ", STR_PAD_LEFT) . PHP_EOL;

        
        /**
         * TAX LOGIC HERE
         * SELECT item, SUM(tax) AS total_tax FROM shopping_cart WHERE tid='156' GROUP BY tax;
         */
        $datax .= str_pad("-", 39, "-", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("You were served by  : " . ucfirst($request->session()->get('fname')) . " " . ucfirst($request->session()->get('lname')), 40, " ",  STR_PAD_RIGHT) . PHP_EOL;
        $datax .= str_pad("-", 39, "-", STR_PAD_BOTH) . PHP_EOL;

        if($get_sc_rcpt->count() > 0){

            $time = time();
            $myfile = fopen('C:\Users\oscar\Documents\pool\in\ ' . $time . '.txt', "w") or die("Unable to open file!");
            fwrite($myfile, $datax);
            fclose($myfile);

        }

        return json_encode(array('status'=>1));
    }

    public function hold_trans(Request $request){

        //ttl:ttl,cust:cust,type:type
        $br = Branches::select('id')->where('curr',1)->limit(1)->first();
        
        $customer = $request->customer;
        $cust_arr = explode(" ",$customer);
        $cust_arr2 = explode("-",$customer);
        
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

        $sum_qty = ShoppingCart::where('status',1)->where('type','tender')->where('uid',$request->session()->get('uid'))->sum('qty');
        $sum_total = ShoppingCart::where('status',1)->where('type','tender')->where('uid',$request->session()->get('uid'))->sum('total');
        $sum_tax = ShoppingCart::where('status',1)->where('type','tender')->where('uid',$request->session()->get('uid'))->sum('tax');
        $sum_cost = ShoppingCart::where('status',1)->where('type','tender')->where('uid',$request->session()->get('uid'))->sum('cost');
       
        
                    
                $change = 0;
                
                $trans = new Transactions();
                $trans->cash = $sum_total;
                $trans->change = $change;
                $trans->total_gross = $sum_total;
                $trans->total_tax = $sum_tax;
                $trans->total_cost = $sum_cost;
                $trans->trans_time = time();
                $trans->ref_no = $request->ttl;
                $trans->type = $cust_type->c_type;
                $trans->customer = $cust_id->id;
                $trans->branch = $br->id;
                $trans->user = $request->session()->get('uid');
                $trans->status = 0;
                $trans->no_items = $sum_qty;
                $trans->total = $sum_total;
                $trans->save();
               
                $trans_chk = Transactions::select('id')->where('user',$request->session()->get('uid'))->where('status',0)->orderBy('id','desc')->first();

                $get_sc = ShoppingCart::select('id','item','qty')->where('type','tender')->where('uid',$request->session()->get('uid'))->where('status',1)->get();
                
                foreach($get_sc as $val_sc){
                    
                    $up_sc = ShoppingCart::find($val_sc->id);
                    $up_sc->tid = $trans_chk->id;
                    $up_sc->status = 2;
                    $up_sc->save();

                }

                $up_trans = Transactions::find($trans_chk->id);
                $receipt = str_pad(date('m',time()) . $trans_chk->id, 7, "0", STR_PAD_LEFT);
                $up_trans->receipt_no = $receipt;
                $up_trans->save();


                return json_encode(array('status'=>1,'change'=>$change));
                
         
    }

    

    public function drawings_transactions(Request $request){

        $br = Branches::where('curr',1)->select('id')->orderBy('id','DESC')->limit(1)->first();
            
        $dr_chk = Transactions::select('id','trans_time','total')->where('type','drawings')->where('branch',$br->id)->orderBy('id','desc')->first();

        $rem = Drawings::select('remainder_amt')->where('branch',$br->id)->orderBy('id','DESC')->limit(1)->first();

        if($dr_chk){

            $ttl_qry = Transactions::where('trans_time', '>', $dr_chk->trans_time)->where('branch',$br->id)->where('type','cash tender')->sum('total');
            if($rem){
                $sls_ttl = $ttl_qry + $rem->remainder_amt;
            }else{
                $sls_ttl = $ttl_qry;
            }
            
            
        }else{
            $ttl_qry = Transactions::where('type','cash tender')->where('branch',$br->id)->sum('total');
            if($rem){
                $sls_ttl = $ttl_qry + $rem->remainder_amt;
            }else{
                $sls_ttl = $ttl_qry;
            }
        
        }

        if($request->amt <= $sls_ttl){
            
            $trans = new Transactions();
            $trans->cash = $request->amt;
            $trans->total = $request->amt;
            $trans->branch = $br->id;
            $trans->trans_time = time();
            $trans->type = "drawings";
            $trans->user = $request->session()->get('uid');
            $trans->status = 2;
            $trans->save();


            $trans_chk = Transactions::select('id')->where('type','drawings')->where('branch',$br->id)->orderBy('id','desc')->first();

            $draw = new Drawings();
            $draw->comment = $request->comm;
            $draw->amount = $request->amt;
            $draw->branch = $br->id;
            $draw->uid = $request->session()->get('uid');
            $draw->tid = $trans_chk->id;
            $draw->sales_amt = $ttl_qry;
            $draw->dr_time = time();
            $draw->remainder_amt = $sls_ttl - $request->amt;
            $draw->save();

            return json_encode(array('status'=>1));
        }else{
            return json_encode(array('status'=>0));
        }
        
        
    }

    public function fetch_return_txn(Request $request){

        $tr_type = Transactions::select('type')->where('id', $request->txn)->first()->type;
        

        if($tr_type=='collection'){
            $p = Pool::select('qty','item')->where('sc_id', $request->txn)->first(); 
            $tr_chk_ttl = abs($p->qty) * Items::find($p->item)->sell_price;
        }else{
            $tr_chk_ttl = Transactions::where('id', $request->txn)->sum('total'); 
        }

        $tr_chk_rtrn = Transactions::where('ref_no', $request->txn_no)->where('type','return tender')->sum('total'); 
          

        $sc = ShoppingCart::select('id','qty','item','price','total')->where('tid',$request->txn)->where('status',2)->get();
        
        foreach($sc as $i=>$val){
            $d[$i]['id'] = $val->id;
            $qty_3 = ShoppingCart::where('item',$val->item)->where('status','3')->where('type','return')->sum('qty');
            $qty_4 = ShoppingCart::where('item',$val->item)->where('status','4')->where('type','return')->where('rid',$request->txn)->sum('qty');
            $d[$i]['qty'] = $val->qty - abs($qty_3) - abs($qty_4);
            $d[$i]['item'] = Items::find($val->item)->item_desc;
            $d[$i]['price'] = number_format($val->price);
            $d[$i]['total'] = number_format($val->total);
        }

        if($tr_chk_rtrn < $tr_chk_ttl){
            return json_encode($d);
        }else{
            return json_encode(array('status'=>0));
        }
        
    }

    public function fetch_return_txn_sec(Request $request){

        $sc = ShoppingCart::select('id','qty','item','price','total')->where('tid',$request->txn)->where('status',3)->get();

        foreach($sc as $i=>$val){
            $d[$i]['id'] = $val->id;
            $d[$i]['qty'] = $val->qty;
            $d[$i]['item'] = Items::find($val->item)->item_desc;
            $d[$i]['price'] = number_format($val->price);
            $d[$i]['total'] = number_format($val->total);
        }

        return json_encode($d);
    }


    public function return_txns(){

        $br = Branches::select('id')->where('curr',1)->orderBy('id', 'DESC')->limit(1)->first();
                                            
        $from = strtotime(date("d-m-Y",time()) . " 01:00:00");
        $to = strtotime(date("d-m-Y",time()) . " 23:59:59");

        $res = Transactions::select('id','type','trans_time','user','total','total_cost','receipt_no','type','status','no_items','type')->where('branch',$br->id)->orderBy('id','DESC')->whereBetween('trans_time',array($from,$to))->where('type', '!=', 'drawings')->where('type', '!=', 'return')->get();

        if($res->count() > 0){
            $no = 1;
            foreach($res as $i=>$val){
                $d[$i]['no'] = $no;
                $d[$i]['id'] = $val->id;
                $d[$i]['trans_time'] = date("d/m/y h:i",$val->trans_time);
                if($val->type =='collection'){
                    $pool = Pool::select('qty','item')->where('sc_id',$val->id)->first();
                    $d[$i]['no_items'] = $pool->qty;
                    $d[$i]['total'] = number_format($pool->qty * Items::find($pool->item)->sell_price);
                }else{
                    $d[$i]['total'] = number_format($val->total) . ".00";
                    $d[$i]['no_items'] = $val->no_items;
                }
                
                $d[$i]['receipt_no'] = $val->receipt_no;
                
                $d[$i]['type'] = $val->type;
                $user_dets = User::find($val->user);
                $d[$i]['user'] = substr($user_dets->fname, 0, 1) . ". ". $user_dets->lname;
                if($val->status ==0){
                    $d[$i]['status'] = "Deleted";
                }else if($val->status ==1){
                    $d[$i]['status'] = "Complete";
                }
                
                $d[$i]['type'] = $val->type;
                $no++;
            }

            
        }else{
            $d = [];
        }

        return json_encode($d);
        
    }

    public function cust_acc_txns(Request $request){

        $br = Branches::select('id')->where('curr',1)->orderBy('id', 'DESC')->limit(1)->first();
                                            
        $from = strtotime(date("d-m-Y",time()) . " 01:00:00");
        $to = strtotime(date("d-m-Y",time()) . " 23:59:59");

        $customer = $request->customer;
        $cust_arr = explode(" ",$customer);
        $cust_arr2 = explode("-",$customer);
        
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
        
       $res = Transactions::select('id','trans_time','user','total','total_cost','receipt_no','type','status','no_items','type')->whereBetween('trans_time',array($from,$to))->where('branch',$br->id)->orderBy('id','DESC')->where('customer',$cust_id)->where('type','collection')->get();

        if($res->count() > 0){
            $no = 1;
            foreach($res as $i=>$val){
                $d[$i]['no'] = $no;
                $d[$i]['id'] = $val->id;
                $pool = Pool::select('id','qty','item')->where('sc_id',$val->id)->first();
                $d[$i]['trans_time'] = date("d/m/y h:i",$val->trans_time);
                $d[$i]['total'] = number_format($val->total) . ".00";
                $d[$i]['receipt_no'] = $val->receipt_no;
                $d[$i]['item'] = Items::find($pool->item)->item_desc;
                $d[$i]['no_items'] = $pool->qty;
                $d[$i]['type'] = $val->type;
                $user_dets = User::find($val->user);
                $d[$i]['user'] = substr($user_dets->fname, 0, 1) . ". ". $user_dets->lname;
                if($val->status ==0){
                    $d[$i]['status'] = "Deleted";
                }else if($val->status ==1){
                    $d[$i]['status'] = "Complete";
                }
                
                $d[$i]['type'] = $val->type;
                $no++;
            }

            return json_encode($d);
        }else{
            return [];
        }
        
    }


    

    public function return_all_for_xchange_txns(Request $request){
        
        $br = Branches::select('id')->where('curr',1)->orderBy('id', 'DESC')->limit(1)->first();
        
        $qty = ShoppingCart::where('type','return')->where('status',1)->sum('qty');
        $tax = ShoppingCart::where('type','return')->where('status',1)->sum('tax');
        $total = ShoppingCart::where('type','return')->where('status',1)->sum('total');
        $cost = ShoppingCart::where('type','return')->where('status',1)->sum('cost');
        $sc = ShoppingCart::select('customer')->where('type','return')->where('status',1)->get();
        foreach($sc as $val){
            $customer = $val->customer;
        }

        $tr = new Transactions();
        $tr->cash = 0;
        $tr->change = 0;
        $tr->total_tax = $tax;
        $tr->total_tax = $tax;
        $tr->total = $total;
        $tr->no_items = $qty;
        $tr->trans_time = time();
        $tr->customer = $customer;
        $tr->user = $request->session()->get('uid');
        $tr->total_cost = $cost;
        $tr->type = "return";
        $tr->status = 1;
        $tr->branch = $br->id;
        $tr->save();

        $trans_chk = Transactions::select('id')->where('user',$request->session()->get('uid'))->where('status',1)->orderBy('id','desc')->first();

        $get_sc = ShoppingCart::select('id','item','qty')->where('type','return')->where('uid',$request->session()->get('uid'))->where('status',1)->get();
            
        foreach($get_sc as $val_sc){
            
            $up_sc = ShoppingCart::find($val_sc->id);
            $up_sc->tid = $trans_chk->id;
            $up_sc->customer = $customer;
            $up_sc->status = 2;
            $up_sc->save();

        }

        $up_trans = Transactions::find($trans_chk->id);
        $receipt = str_pad(date('m',time()) . $trans_chk->id, 7, "0", STR_PAD_LEFT);
        $up_trans->receipt_no = $receipt;
        $up_trans->save();

        return json_encode(array('status'=>1));

    }   

    public function issue_acc_items(Request $request){

        $br = Branches::select('id','branch')->where('curr',1)->limit(1)->first();
        
        $customer = $request->customer;
        $cust_arr = explode(" ",$customer);
        $cust_arr2 = explode("-",$customer);
        
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

        $trans = new Transactions();
        $trans->cash = 0;
        $trans->change = 0;
        $trans->total_gross = 0;
        $trans->total_tax = 0;
        $trans->total_cost = 0;
        $trans->customer = $cust_id;
        $trans->trans_time = time();
        $trans->branch = $br->id; 
        $trans->type = "collection";
        $trans->user = $request->session()->get('uid');
        $trans->status = 1;
        $trans->no_items = 0;
        $trans->total = 0;
        $trans->save();
        

        $trans_chk = Transactions::select('id')->where('user',$request->session()->get('uid'))->where('status',1)->where('branch',$br->id)->orderBy('id','desc')->first();

        $get_sc = ShoppingCart::select('id','item','qty')->where('type','tender')->where('uid',$request->session()->get('uid'))->where('branch',$br->id)->where('status',1)->get();
      
        foreach($get_sc as $val_sc){
           
            $up_sc = ShoppingCart::find($val_sc->id);
            $up_sc->tid = $trans_chk->id;
            $up_sc->type = 'tender';
            $up_sc->status = 2;
            $up_sc->save();

        }

        $up_trans = Transactions::find($trans_chk->id);
        $receipt = str_pad(date('m',time()) . $br->id . $request->session()->get('uid') . $trans_chk->id, 9, "0", STR_PAD_LEFT);
        $up_trans->receipt_no = $receipt;
        $up_trans->save();
        
        //Update Accounts too
        $get_acc = Accounts::select('id','customer','item','event','qty')->where('status',0)->where('branch',$br->id)->get();
        foreach($get_acc as $val_acc){

            $up_acc = Accounts::find($val_acc->id);
            $up_acc->status = 1;
            $up_acc->save();

            $pool = new Pool();
            $pool->customer = $val_acc->customer;
            $pool->item = $val_acc->item;
            $pool->event = $val_acc->event;
            $pool->sc_id = $trans_chk->id;
            $pool->qty = $val_acc->qty;
            $pool->p_time = time();
            $pool->branch = $br->id;
            $pool->user = $request->session()->get('uid');
            $pool->type = 'collection';
            $pool->save();
            
        }

        $datax =  str_pad("NOVEL GOLF SHOP", 40, " ", STR_PAD_BOTH). PHP_EOL;
        $datax .= str_pad("P.O. BOX 52399 - 00100", 40, " ", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("EMAIL: novelgolf@gmail.com", 40, " ", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("_", 39, "_", STR_PAD_BOTH) . PHP_EOL;
        $datax .= "                                        " . PHP_EOL;
        $datax .= str_pad("SALE RECEIPT - COLLECTION ", 40, " ", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("_", 39, "_", STR_PAD_BOTH) . PHP_EOL;
        $datax .= "                                        " . PHP_EOL;
        $datax .= str_pad("DATE: " . date("d-m-Y",time()) . " TIME: " . date("h:i:s",time()),40, " ", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("RECEIPT #: " .  $receipt, 40, " ", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("BRANCH : " .  $br->branch, 40, " ", STR_PAD_BOTH) . PHP_EOL;
        
        $datax .= str_pad("-", 39, "-", STR_PAD_BOTH) . PHP_EOL;
        $datax .= "ITEM                QTY   PRICE  AMOUNT" . PHP_EOL;
        $datax .= str_pad("-", 39, "-", STR_PAD_BOTH) . PHP_EOL;
       
        $sum_total = ShoppingCart::where('type','tender')->where('tid',$trans_chk->id)->where('status',2)->sum('total');
        $sum_qty = ShoppingCart::where('type','tender')->where('tid',$trans_chk->id)->where('status',2)->sum('qty');

        $get_sc_rcpt = ShoppingCart::select('item','qty','price','total','tax')->where('type','tender')->where('tid',$trans_chk->id)->where('status',2)->get();
        foreach($get_sc_rcpt as $val_sc_rcpt){
            $rcpt_item = $val_sc_rcpt->item;
           
            $item_desc = Items::find($rcpt_item);
            $rcpt_qty = $val_sc_rcpt->qty;
            $rcpt_price = $val_sc_rcpt->price;
            $rcpt_total = $val_sc_rcpt->total;
            $rcpt_tax = $val_sc_rcpt->tax;
        $datax .= wordwrap($item_desc->item_desc . PHP_EOL, 40, PHP_EOL, true);
        $datax .= str_pad($rcpt_qty . " x " . number_format($rcpt_price) . "   " .number_format($rcpt_total) . ".00", 40, " ", STR_PAD_LEFT) . PHP_EOL;
   
            
        }

        $datax .= str_pad("=", 39, "=", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("TOTAL NO OF ITEMS:", 20, " ", STR_PAD_RIGHT) . str_pad(number_format($sum_qty), 20, " ", STR_PAD_LEFT) . PHP_EOL;
      
        $datax .= str_pad("COLLECTION VALUE :", 20, " ", STR_PAD_RIGHT) . str_pad(number_format($sum_total).".00", 20, " ", STR_PAD_LEFT) . PHP_EOL;
        
        
        /**
         * TAX LOGIC HERE
         * SELECT item, SUM(tax) AS total_tax FROM shopping_cart WHERE tid='156' GROUP BY tax;
         */
        $datax .= str_pad("-", 39, "-", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("You were served by  : " . ucfirst($request->session()->get('fname')) . " " . ucfirst($request->session()->get('lname')), 40, " ",  STR_PAD_RIGHT) . PHP_EOL;
        $datax .= str_pad("-", 39, "-", STR_PAD_BOTH) . PHP_EOL;

        if($get_sc_rcpt->count() > 0){

            $time = time();
            $myfile = fopen('C:\Users\oscar\Documents\pool\in\ ' . $time . '.txt', "w") or die("Unable to open file!");
            fwrite($myfile, $datax);
            fclose($myfile);

        }

        return json_encode(array('status'=>1));

    }


    public function issue_xchange_items(Request $request){
        //credit:credit,acc_val:acc_val,xchange_val:xchange_val,customer:customer
        $customer = $request->customer;
        $cust_arr = explode(" ",$customer);
        $cust_arr2 = explode("-",$customer);
        
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

        

        $sum_qty = ShoppingCart::where('status',1)->where('type','xchange')->where('uid',$request->session()->get('uid'))->sum('qty');
        $sum_tender_total = ShoppingCart::where('status',1)->where('type','tender')->where('uid',$request->session()->get('uid'))->sum('total');
        $sum_total = ShoppingCart::where('status',1)->where('type','xchange')->where('uid',$request->session()->get('uid'))->sum('total');
        $sum_tax = ShoppingCart::where('status',1)->where('type','xchange')->where('uid',$request->session()->get('uid'))->sum('tax');
        $sum_cost = ShoppingCart::where('status',1)->where('type','xchange')->where('uid',$request->session()->get('uid'))->sum('cost');
        
        $trans = new Transactions();
        $trans->cash = 0;
        $trans->change = 0;
        $trans->total_gross = $sum_total;
        $trans->total_tax = $sum_tax;
        $trans->total_cost = $sum_cost;
        $trans->customer = $cust_id->id;
        $trans->trans_time = time();
        $trans->branch = $br->id; 
        $trans->type = "xchange";
        $trans->user = $request->session()->get('uid');
        $trans->status = 1;
        $trans->no_items = $sum_qty;
        $trans->total = $sum_total;
        if($sum_total > $sum_tender_total){
            $trans->comment = $sum_total - $sum_tender_total;
        }
        $trans->save();

        $trans_chk = Transactions::select('id')->where('user',$request->session()->get('uid'))->where('status',1)->orderBy('id','desc')->first();

        $up_trans = Transactions::find($trans_chk->id);
        $receipt = str_pad(date('m',time()) . $trans_chk->id, 7, "0", STR_PAD_LEFT);
        $up_trans->receipt_no = $receipt;
        $up_trans->save();

        $trans_chk = Transactions::select('id')->where('user',$request->session()->get('uid'))->where('status',1)->where('branch',$br->id)->orderBy('id','desc')->first();

        $get_sc = ShoppingCart::select('id','item','qty')->where('uid',$request->session()->get('uid'))->where('branch',$br->id)->where('status',1)->get();
      
        foreach($get_sc as $val_sc){
           
            $up_sc = ShoppingCart::find($val_sc->id);
            $up_sc->tid = $trans_chk->id;
            $up_sc->status = 2;
            $up_sc->save();

        }

        $cust_up = Customers::find($cust_id->id);
        $cust_up->credit = $cust_up->credit + $request->credit;
        $cust_up->save();

        //Update Accounts too
        $get_acc = Accounts::select('id')->where('status',0)->where('branch',$br->id)->get();
        foreach($get_acc as $val_acc){
            $up_acc = Accounts::find($val_acc->id);
            $up_acc->status = 1;
            $up_acc->save();
        }

        return json_encode(array('status'=>1,'customer'=>$cust_id->id));

    }


    

    public function check_customer(Request $request){
        $customer = $request->customer;
        $cust_arr = explode(" ",$customer);
        $cust_arr2 = explode("-",$customer);
        
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
        
        if($cust_id){
            return json_encode(array('customer'=>$cust_id->id,'fname'=>$cust_arr[0],'lname'=>$cust_arr[1],'org'=>$cust_arr2[1]));
        }else{
            return json_encode(array('status'=>0));
        }
    }

    public function search_trans_reports(Request $request){
        
        $res = Transactions::select('id','trans_time','user','total','receipt_no','type','status','no_items','type')->where('receipt_no',$request->search)->get();
        
        
        if(!empty($request->search)){

            $res = Transactions::select('id','trans_time','user','total','receipt_no','type','status','no_items','type')->where('receipt_no', 'like', '%' . $request->search . '%')->get();

        }else{

             $from = strtotime(date("d-m-Y",time()) . " 01:00:00");
             $to = strtotime(date("d-m-Y",time()) . " 23:59:59");

             $res = Transactions::select('id','trans_time','user','total','receipt_no','type','status','no_items','type')->orderBy('id','DESC')->whereBetween('trans_time',array($from,$to))->get();

        }
        
        
        if($res->count() > 0){
            $no = 1;
            foreach($res as $i=>$val){
                $d[$i]['no'] = $no;
                $d[$i]['id'] = $val->id;
                $d[$i]['trans_time'] = date("d/m/y h:i",$val->trans_time);
                $d[$i]['total'] = number_format($val->total) . ".00";
                $d[$i]['receipt_no'] = $val->receipt_no;
                $d[$i]['no_items'] = $val->no_items;
                $d[$i]['type'] = $val->type;
                $user_dets = User::find($val->user);
                $d[$i]['user'] = substr($user_dets->fname, 0, 1) . ". ". $user_dets->lname;
                if($val->status ==0){
                    $d[$i]['status'] = "Deleted";
                }else if($val->status ==1){
                    $d[$i]['status'] = "Complete";
                }
                
                $d[$i]['type'] = $val->type;
                $no++;
            }

            return json_encode($d);
        }else{
            return json_encode(array('status'=>0));
        }
 
        
    }

    

    public function return_all_items(Request $request){

            $br = Branches::select('id')->where('curr',1)->limit(1)->first();
        
            $sc = ShoppingCart::select('item','id','qty','total','tax','cost')->where('tid',$request->txn_id)->where('status','3')->where('type','return')->get();

            $sum_sc_total_cost = ShoppingCart::where('tid',$request->txn_id)->where('status','3')->where('type','return')->sum('cost');
            $sum_sc_total = ShoppingCart::where('tid',$request->txn_id)->where('status','3')->where('type','return')->sum('total');
            $sum_sc_qty = ShoppingCart::where('tid',$request->txn_id)->where('status','3')->where('type','return')->sum('qty');
            $sum_sc_tax = ShoppingCart::where('tid',$request->txn_id)->where('status','3')->where('type','return')->sum('tax');
            $trans = Transactions::find($request->txn_id);
            
            if($sc->count() > 0){

                foreach($sc as $val){
                    $id = $val->id;
                    $qty = $val->qty;
                    $total = $val->total;
                    $item = $val->item;
                    $cost = $val->cost;
                }

                $tr = new Transactions();
                $tr->total_tax = $sum_sc_tax;
                $tr->total = $sum_sc_total;
                $tr->total_cost = $sum_sc_total_cost;
                $tr->no_items = $sum_sc_qty;
                $tr->trans_time = time();
                $tr->ref_no = $trans->receipt_no;
                $tr->branch = $br->id;
                $tr->user = $request->session()->get('uid');
                $tr->type = "return";
                $tr->status = 1;
                $tr->comment = $request->fname . ' ' . $request->lname;
                $tr->save();


               $trans_chk = Transactions::select('id')->where('user',$request->session()->get('uid'))->where('status',1)->where('type','return')->orderBy('id','desc')->first();
               $up_trans = Transactions::find($trans_chk->id);
               $receipt = str_pad(date('m',time()) . $br->id . $request->session()->get('uid') . $trans_chk->id, 9, "0", STR_PAD_LEFT);
               $up_trans->receipt_no = $receipt;
               $up_trans->save();

               $sc_ret_up = ShoppingCart::select('id','tid','item')->where('status',3)->where('type','return')->get();
               
               foreach($sc_ret_up as $val_ret_up){
                    $sc_up = ShoppingCart::find($val_ret_up->id);
                    $sc_up->tid = $trans_chk->id;
                    $sc_up->status = 4;
                    $sc_up->save();
               }

               echo json_encode(array('status'=>1));

            }

    }

    
    public function remove_remaining_returns(){
        $br = Branches::select('id')->where('curr',1)->limit(1)->first();
        
        $sc = ShoppingCart::select('id')->where('status',3)->where('branch',$br->id)->where('type','return')->get();
        foreach($sc as $val){
            $id = $val->id;
            $up_sc = ShoppingCart::find($id);
            $up_sc->status = 0;
            $up_sc->save();
        }
    }

    public function remove_remaining_sc(){
        $br = Branches::select('id')->where('curr',1)->limit(1)->first();
        
        $sc = ShoppingCart::select('id')->where('status',1)->where('branch',$br->id)->get();
        foreach($sc as $val){
            $id = $val->id;
            $up_sc = ShoppingCart::find($id);
            $up_sc->status = 0;
            $up_sc->save();
        }
    }

    public function remove_remaining_acc_sc(){
        $sc = ShoppingCart::select('id')->where('status',0)->where('branch',$br->id)->where('type','tender')->get();
        foreach($sc as $val){
            $id = $val->id;
            $up_sc = ShoppingCart::find($id);
            $up_sc->status = 0;
            $up_sc->save();
        }

    }

    public function remove_remaining_xchange_sc(){

        $br = Branches::select('id')->where('curr',1)->limit(1)->first();
        
        $sc = ShoppingCart::select('id')->where('status',1)->where('branch',$br->id)->where('type','xchange')->get();
        foreach($sc as $val){
            $id = $val->id;
            $up_sc = ShoppingCart::find($id);
            $up_sc->status = 0;
            $up_sc->save();
        }

    }

   

    public function print_duplicate_receipt(Request $request){
        
        $sum_qty = ShoppingCart::where('tid',$request->id)->where('type','tender')->where('uid',$request->session()->get('uid'))->sum('qty');
        $sum_total = ShoppingCart::where('tid',$request->id)->where('type','tender')->where('uid',$request->session()->get('uid'))->sum('total');
        $sum_tax = ShoppingCart::where('tid',$request->id)->where('type','tender')->where('uid',$request->session()->get('uid'))->sum('tax');
        $sum_cost = ShoppingCart::where('tid',$request->id)->where('type','tender')->where('uid',$request->session()->get('uid'))->sum('cost');
        
        $trans = Transactions::find($request->id);
        $cash = $trans->cash;
        $change = $trans->change;
        
        $datax =  str_pad("NOVEL GOLF SHOP", 40, " ", STR_PAD_BOTH). PHP_EOL;
        $datax .= str_pad("P.O. BOX 52399 - 00100", 40, " ", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("EMAIL: novelgolf@gmail.com", 40, " ", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("_", 39, "_", STR_PAD_BOTH) . PHP_EOL;
        $datax .= "                                        " . PHP_EOL;
        $datax .= str_pad("DUPLICATE RECEIPT - " . strtoupper($trans->type), 40, " ", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("_", 39, "_", STR_PAD_BOTH) . PHP_EOL;
        $datax .= "                                        " . PHP_EOL;
        $datax .= str_pad("DATE: " . date("d-m-Y",$trans->trans_time) . " TIME: " . date("h:i:s",$trans->trans_time),40, " ", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("RECEIPT #: " .  $trans->receipt_no, 40, " ", STR_PAD_BOTH) . PHP_EOL;
        
        $datax .= str_pad("-", 39, "-", STR_PAD_BOTH) . PHP_EOL;
        $datax .= "ITEM                QTY   PRICE  AMOUNT" . PHP_EOL;
        $datax .= str_pad("-", 39, "-", STR_PAD_BOTH) . PHP_EOL;

        $get_sc_rcpt = ShoppingCart::select('item','qty','price','total','tax')->where('tid',$request->id)->where('status',2)->get();
        foreach($get_sc_rcpt as $val_sc_rcpt){
            $rcpt_item = $val_sc_rcpt->item;
           
            $item_desc = Items::find($rcpt_item);
            $rcpt_qty = $val_sc_rcpt->qty;
            $rcpt_price = $val_sc_rcpt->price;
            $rcpt_total = $val_sc_rcpt->total;
            $rcpt_tax = $val_sc_rcpt->tax;
        $datax .= str_pad($item_desc->item_desc, 40, " ", STR_PAD_RIGHT) . PHP_EOL;
        $datax .= str_pad($rcpt_qty . " x " . $rcpt_price . "   " .$rcpt_total . ".00", 40, " ", STR_PAD_LEFT) . PHP_EOL;

            /*
            *Update Item quantity
            */
            $item = Items::find($rcpt_item);

            $new_item_qty = $item->qty - $rcpt_qty;
            //$item->qty = $new_item_qty;
            //$item->save();
            
            
        }
        $datax .= str_pad("=", 39, "=", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("TOTAL NO OF ITEMS:", 20, " ", STR_PAD_RIGHT) . str_pad($sum_qty, 20, " ", STR_PAD_LEFT) . PHP_EOL;
        $datax .= str_pad("TENDER:", 20, " ", STR_PAD_RIGHT) . str_pad($sum_total.".00", 20, " ", STR_PAD_LEFT) . PHP_EOL;
        $datax .= str_pad("-", 39, "-", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("CASH:", 20, " ", STR_PAD_RIGHT) . str_pad($cash.".00", 20, " ", STR_PAD_LEFT) . PHP_EOL;

        $datax .= str_pad("CHANGE:", 20, " ", STR_PAD_RIGHT) . str_pad($change.".00", 20, " ", STR_PAD_LEFT) . PHP_EOL;

        
        /**
         * TAX LOGIC HERE
         * SELECT item, SUM(tax) AS total_tax FROM shopping_cart WHERE tid='156' GROUP BY tax;
         */
        $datax .= str_pad("-", 39, "-", STR_PAD_BOTH) . PHP_EOL;
        $datax .= str_pad("You were served by  : " . ucfirst($request->session()->get('fname')) . " " . ucfirst($request->session()->get('lname')), 40, " ",  STR_PAD_RIGHT) . PHP_EOL;
        $datax .= str_pad("-", 39, "-", STR_PAD_BOTH) . PHP_EOL;

        if($get_sc_rcpt->count() > 0){

            $time = time();
            $myfile = fopen('C:\Users\oscar\Documents\pool\in\ ' . $time . '.txt', "w") or die("Unable to open file!");
            fwrite($myfile, $datax);
            fclose($myfile);

        }
  
    }

    public function get_held_trans_data(Request $request){

        //$trans = Transactions::select('id','cash','total_tax','total_gross','total','total','no_items','customer','total_cost','reciept_no','ref_no')->where('status',0)->where('id',$request->tid)->get();

        $sc_ret_res = ShoppingCart::where('status',2)->where('tid',$request->tid)->orderBy('id','DESC')->select('id','qty','item','price','total')->get();
                                
        foreach($sc_ret_res as $i=>$val_ret_res){
            $d[$i]['id'] = $val_ret_res->id;
            $d[$i]['qty'] = $val_ret_res->qty;
            $itemx = Items::find($val_ret_res->item);
            $d[$i]['code'] = $itemx->look_up;
            $d[$i]['item'] = $val_ret_res->id;
            $d[$i]['item_name'] = $itemx->item_desc;
            $d[$i]['price'] = $val_ret_res->price;
            $d[$i]['total'] = $val_ret_res->total;
            
        }

        if($sc_ret_res->count() > 0){
            return json_encode($d);
        }else{
            return "[]";
        }

    }

    public function sel_sc_totals(Request $request){
        
        $sum_qty = ShoppingCart::where('tid',$request->tid)->sum('qty');
        $sum_total = ShoppingCart::where('tid',$request->tid)->sum('total');
        $sum_tax = ShoppingCart::where('tid',$request->tid)->sum('tax');

        return json_encode(array('sum_qty'=>number_format(round($sum_qty,2)),'sum_total'=>number_format(round($sum_total,2)),'sum_tax'=>number_format(round($sum_tax),2)));
    }

    public function check_xchange(Request $request){
            $pay_val = $request->pay_val;
            $total = $request->total;

            $gds = Goods::select('cost','qty')->where('status',1)->where('receipt_no',$pay_val)->get();

            if($gds->count() > 0){
                foreach($gds as $val){
                    $cost = $val->cost;
                    $qty = $val->qty;
                    $total_cost = $cost * $qty;
                        
                        $tr_sum = Transactions::select('total')->where('ref_no',$pay_val)->sum('total');
                        $tr_disc_sum = Transactions::select('total')->where('ref_no',$pay_val)->sum('discount');
                        
                        if($total_cost >= ($tr_sum - $tr_disc_sum)){

                            echo json_encode(array('status'=>1,'cost'=>$total_cost,'diff'=>$total_cost - $total));
            
                        }else{
                             //Ref No. Used and exhausted 
                             echo json_encode(array('status'=>3));
                        }
                        
                }
            }else{
                echo json_encode(array('status'=>0));
            }
    }


}

?>



