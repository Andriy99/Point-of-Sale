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
use App\Catg;
use App\Tax;
use App\Pool;

use Auth;

class NewTransController extends Controller
{

    public function return_sc_item(Request $request){

            $br = Branches::select('id')->where('curr',1)->orderBy('id', 'DESC')->limit(1)->first();
                                            
            $sc = ShoppingCart::select('item','tid','qty','price','cost','total')->where('id',$request->sc_id)->get();

            $new_qty = $request->qty;

            

            foreach($sc as $val){
                $item = $val->item;
                $qty = $val->qty;
                $price = $val->price;
                $cost = Goods::select('cost')->where('item',$val->item)->orderBy('id','DESC')->limit(1)->first()->cost;
                $tid = $val->tid;
                $total = $val->total;
                $itemx = Items::find($val->item);
                $tax_perc = Tax::find($itemx->tax)->perc;

                

                //Prevent double returns
                $chk_dbl_rtrn = ShoppingCart::select('id')->where('item',$item)->where('rid',$tid)->sum('qty');
                
                if(abs($chk_dbl_rtrn) < $qty){

                        $removed_qty = ShoppingCart::where('tid',$tid)->where('item',$item)->where('status','3')->where('type','return')->sum('qty');

                        $tendered_qty = ShoppingCart::where('tid',$tid)->where('item',$item)->where('status','2')->where('type','tender')->sum('qty');

                        
                        if($tendered_qty >= (abs($removed_qty) + $new_qty)){

                            $chk_ret_sc = ShoppingCart::select('id','qty','total','tax','cost')->where('item',$item)->where('tid',$tid)->where('status','3')->get();

                            if($chk_ret_sc->count() ==0){


                                if($request->session()->has('uid')){
                                    $new_sc = new ShoppingCart();
                                    $new_sc->qty = "-" . $new_qty;
                                    $new_sc->item = $item;
                                    $new_sc->price = $price;
                                    $new_sc->branch = $br->id;
                                    $new_sc->catg = Items::select('catg')->find($item)->catg;
                                    $new_sc->tid = $tid;
                                    $new_sc->rid = $tid;
                                    $new_sc->status = 3;
                                    $new_sc->total = "-".$price * $new_qty;
                                    $new_sc->cost = "-".$cost;
                                    $new_sc->time = time();

                                }
                                
                                
                                if($tax_perc !=0){
                                    $new_sc->tax = "-" . ($tax_perc * ($price * $new_qty));
                                }else{
                                    $new_sc->tax = 0;
                                }
                                
                                $new_sc->uid = $request->session()->get('uid');
                                $new_sc->type = 'return';
                                $new_sc->save();

                            }else{

                                foreach($chk_ret_sc as $val_sc){

                                    $curr_id = $val_sc->id;
                                    $curr_qty = $val_sc->qty;
                                    $curr_total = $val_sc->total;
                                    $curr_tax = $val_sc->tax;
                                    $curr_cost = $val_sc->cost;

                                    $up_ret_sc = ShoppingCart::find($curr_id);

                                    $up_ret_sc->qty = $curr_qty - $new_qty;
                                    $up_ret_sc->total = $curr_total - $price;
                                    $up_ret_sc->cost = $curr_cost - $cost;

                                    if($curr_tax ==0){

                                        $up_ret_sc->tax = 0;
                                        
                                    }else{
                                        $temp_tax = "-" . ($tax_perc * ($price * $new_qty));
                                        $up_ret_sc->tax = $curr_tax - $temp_tax;
                                    }

                                    $up_ret_sc->save();

                                }

                            }

                            }

                        $sc_ret = ShoppingCart::select('id','qty','item','price','total')->where('tid',$tid)->where('status','3')->get();

                        if($sc_ret->count() > 0){
                            foreach($sc_ret as $i=>$val_ret){
                                $d[$i]['id'] = $val_ret->id;
                                $d[$i]['qty'] = $val_ret->qty;
                                $d[$i]['item'] = Items::find($val_ret->item)->item_desc;
                                $d[$i]['price'] = $val_ret->price;
                                $d[$i]['total'] = $val_ret->total;
                            }
                        }else{
                            $d = [];
                        }
                        

                    return json_encode($d);
                    
                }

            }
            
    }  


    public function new_search_trans_reports(Request $request){
        
        $res = Transactions::select('id','trans_time','user','total','receipt_no','type','status','no_items','type')->where('type', '!=', 'drawings')->where('receipt_no',$request->search)->get();
        
        
        if(!empty($request->search)){
            
            $exp_dates = explode("to",$request->search);
            $from = strtotime(str_replace(' ', '', $exp_dates[0]). " 06:00:00");
            $to = strtotime(str_replace(' ', '', $exp_dates[1]). " 23:59:59");


        }else{

            $from = strtotime(date("d-m-Y",time()) . " 01:00:00");
            $to = strtotime(date("d-m-Y",time()) . " 23:59:59");
  
        }

        $res = Transactions::select('id','trans_time','user','total','receipt_no','type','status','no_items','type')->orderBy('id','DESC')->where('type', '!=', 'drawings')->whereBetween('trans_time',array($from,$to))->get();

        
        
        if($res->count() > 0){
            $no = 1;
            foreach($res as $i=>$val){
                $d[$i]['no'] = $no;
                $d[$i]['id'] = $val->id;
                $d[$i]['trans_time'] = date("d/m/y h:i",$val->trans_time);
                
                $d[$i]['receipt_no'] = $val->receipt_no;
                if($val->type =="collection"){
                    $p = Pool::select('qty','item')->where('sc_id',$val->id)->first();
                    $d[$i]['no_items'] = $p->qty;
                    $d[$i]['total'] = number_format($p->qty * Items::find($p->item)->sell_price);
                }else{
                    $d[$i]['no_items'] = $val->no_items;
                    $d[$i]['total'] = number_format($val->total) . ".00";
                }
                
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


    public function search_accounts_txns(Request $request){

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

        $exp_dates = explode("to",$request->dates);
        $from = strtotime(str_replace(' ', '', $exp_dates[0]). " 06:00:00");
        $to = strtotime(str_replace(' ', '', $exp_dates[1]). " 23:59:59");
        
        
        //$acc = DB::select("SELECT SUM(qty) AS ttl_qty,id, item, event, qty, customer, status, branch FROM accounts WHERE acc_date BETWEEN '$from' AND '$to' AND customer='$cust_id' AND status='1' GROUP BY item");
        
        $acc = Accounts::select('qty','id','item','event','qty','customer','status','branch')->whereBetween('acc_date',array($from,$to))->where('customer',$cust_id)->where('status',1)->get(); 
        
        if(count($acc) > 0){
            $no = 1;
            foreach($acc as $i=>$val){
                
                    $d[$i]['no'] = $no;
                    $d[$i]['id'] = $val->id;
                    $d[$i]['customer'] = $val->customer;
                    $d[$i]['event'] = $val->event;
                    $d[$i]['item'] = Items::find($val->item)->item_desc;
                    $d[$i]['item_id'] = $val->item;
                    $d[$i]['ttl_qty'] = $val->qty;
                    $no++;
                
            }
            
        }else{
            $d = [];
        }

        return json_encode($d);
        
        
    }

    public function search_cust_acc_txns(Request $request){

        $br = Branches::select('id')->where('curr',1)->orderBy('id', 'DESC')->limit(1)->first();
        
        $exp_dates = explode("to",$request->dates);
        $from = strtotime(str_replace(' ', '', $exp_dates[0]). " 06:00:00");
        $to = strtotime(str_replace(' ', '', $exp_dates[1]). " 23:59:59");

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

        $res = Transactions::select('id','trans_time','user','total','total_cost','receipt_no','type','status','no_items')->where('branch',$br->id)->orderBy('id','DESC')->where('customer',$cust_id)->where('type','collection')->get();

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

            
        }else{
            $d = [];
        }

        return json_encode($d);
        
    }



}


?>