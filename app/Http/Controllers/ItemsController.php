<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

use App\Items;
use App\ShoppingCart;
use App\Csr;
use App\Tax;
use App\Drawer;
use App\Goods;
use App\User;
use App\Catg;
use App\SubCatg;
use App\Branches;
use App\Accounts;
use App\Customers;
use App\Transactions;
use App\Libraries\FPDF;
use App\Libraries\PhpExcelReader;
use App\Pool;
use PHPExcel; 

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PhpOffice\PhpSpreadsheet\IOFactory;

use PHPExcel_IOFactory;

use File;
use Auth;

class ItemsController extends Controller
{

    public function search_items(Request $request){

        if($request->session()->has('uid')){

            //check drawer
            $drawer = Drawer::select('id','start_time')->where('status','open')->get();
            if($drawer->count() > 0){

                foreach($drawer as $val){
                    $id = $val->id;
                    $start_time = $val->start_time;
    
                    if(date("d",time()) == date("d",$start_time)){
                        /*
                        if($request->val_type =="number"){

                            $res = Items::where('look_up',$request->search_val)->select('look_up','item_desc','id','sell_price','tax')->where('status',1)->get();

                        }else{
                            $res = Items::where('item_desc',$request->search_val)->select('look_up','item_desc','id','sell_price','tax')->where('status',1)->get();

                        }
                        */

                        $res = Items::where('look_up',$request->search_val)->orWhere('code_no',$request->search_val)->select('look_up','item_desc','id','sell_price','buy_price','tax')->where('status',1)->get();

                        if($res->count() > 0){

                            foreach($res as $val){
                                //Goods Received
                                $res_gds = Goods::where('item',$val->id)->select('cost','id','price')->orderBy('id','DESC')->limit(1)->first();

                                if($res_gds){

                                        $sc_res = ShoppingCart::where('item',$val->id)->where('status',1)->select('id','qty','cost','tax')->get();
                        
                                        if($sc_res->count() > 0){
                                          
                                            //Update Shopping Cart
                                            foreach($sc_res as $val_update){
                        
                                                $sc_update = ShoppingCart::find($val_update->id);
                        
                                                $sc_update->qty = $val_update->qty + 1;
                                                $sc_update->item = $val->id;
                                                $sc_update->tax = ceil(Tax::find($val->tax)->perc * $res_gds->price) + $val_update->tax;
                                                $sc_update->price = $val->sell_price;
                                                $sc_update->cost = $val_update->cost + $val->buy_price;
                                                $sc_update->total = ($val_update->qty + 1) * $val->sell_price;
                                                $sc_update->save();
                        
                                            }
                        
                                        }else{
                                            $br = Branches::select('id')->where('curr',1)->orderBy('id', 'DESC')->limit(1)->first();
                                            //Insert Shopping Cart
                                           
                                            $sc = new ShoppingCart();
                                            $sc->qty = 1;
                                            $sc->item = $val->id;
                                            $sc->catg = Items::select('catg')->find($val->id)->catg;
                                            $sc->price = $val->sell_price;
                                            $sc->type = "tender";
                                            $sc->in_stock = $res_gds->id;
                                            $sc->cost = $val->buy_price;
                                            $sc->branch = $br->id;
                                            $sc->uid = $request->session()->get('uid');
                                            $sc->total = $val->sell_price;
                                            $sc->tax = ceil(Tax::find($val->tax)->perc * $val->sell_price);
                                            $sc->status = 1;
                                            $sc->time = time();
                                            $sc->save();
                                        }
                        
                                        $sc_ret_res = ShoppingCart::where('status',1)->orderBy('id','DESC')->select('id','qty','item','price','total')->get();
                                        
                                        foreach($sc_ret_res as $i=>$val_ret_res){

                                            $itemx = Items::find($val_ret_res->item);
                                            //$gds = Goods::where('item',$val_ret_res->item)->select('ceil_price','floor_price')->orderBy('id','DESC')->limit(1)->first();
                                            $d[$i]['ceil_price'] = $itemx->ceil_price;
                                            $d[$i]['floor_price'] = $itemx->floor_price;
                                            $d[$i]['code'] = $itemx->look_up;
                                            $d[$i]['code_no'] = $itemx->code_no;
                                            $d[$i]['id'] = $val_ret_res->id;
                                            $d[$i]['qty'] = $val_ret_res->qty;
                                            $d[$i]['item'] = $val_ret_res->id;
                                            $d[$i]['item_name'] = $itemx->item_desc;
                                            $d[$i]['price'] = $val_ret_res->price;
                                            $d[$i]['total'] = $val_ret_res->total;
                                            
                                        }
                                        
                                        
                                        return json_encode($d);
                                }else{
                                    return json_encode(array('status'=>'G'));
                                }
                            }

                        }else{
                            //Item does not exist
                            return json_encode(array('status'=>0));
                        }

                    }else{
                        return json_encode(array('status'=>'Y'));
                    }
                    //return $diff;
    
                }  
    
            }else{
                //Drawer does not exist
                return json_encode(array('status'=>'X'));
            }

        }
        
    }


    public function search_auto_comp_values(Request $request){

        //$items = Items::select('item_desc')->where('status', 1)->where('item_desc', 'like' , '%' . $request->search_val . '%')->get();
        $items = Items::select('item_desc')->where('status',1)->get();
        if($items->count() > 0){

            foreach($items as $val){
                    $d[] = $val->item_desc;
            }

            return(json_encode($d));

        }else{
            return json_encode(array('status'=>0));
        }
        
    }


    public function update_sc_qty(Request $request){
        $br = Branches::select('id')->where('curr',1)->limit(1)->first();
        
        $sc_res = ShoppingCart::where('id',$request->item)->where('status',1)->where('branch',$br->id)->select('id','qty','price','cost','item')->get();
        
        foreach($sc_res as $val_update){

            $sc_update = ShoppingCart::find($request->item);
            $itemx = Items::find($val_update->item);
            $tax = Tax::find($itemx->tax)->perc;
            $sc_update->qty = $request->qty;
            $sc_update->total = ceil($request->qty * $sc_update->price);
            $sc_update->cost = ceil($request->qty * $itemx->buy_price);
            $sc_update->tax = ceil($request->qty * ($tax * $sc_update->price));
            $sc_update->save();

            $sc_ret_res = ShoppingCart::where('status',1)->orderBy('id','DESC')->select('id','qty','item','price','total')->get();
                                
            foreach($sc_ret_res as $i=>$val_ret_res){
                $d[$i]['id'] = $val_ret_res->id;
                $d[$i]['qty'] = $val_ret_res->qty;
                $itemx = Items::find($val_ret_res->item);
                //$gds = Goods::where('item',$val_ret_res->item)->select('ceil_price','floor_price')->orderBy('id','DESC')->limit(1)->first();
                $d[$i]['ceil_price'] = $itemx->ceil_price;
                $d[$i]['floor_price'] = $itemx->floor_price;
                $d[$i]['code'] = $itemx->look_up;
                $d[$i]['code_no'] = $itemx->code_no;
                $d[$i]['item'] = $val_ret_res->id;
                $d[$i]['item_name'] = $itemx->item_desc;
                $d[$i]['price'] = $val_ret_res->price;
                $d[$i]['total'] = $val_ret_res->total;
                
            }
                                
            return json_encode($d);
           
        }
       

    }

    public function del_sc_item(Request $request){
        
        $sc_res = ShoppingCart::where('id',$request->item)->where('status',1)->select('id','qty','price')->get();
        
        foreach($sc_res as $val_update){

            $sc_update = ShoppingCart::find($val_update->id);
            
            $sc_update->status = 0;
            $sc_update->save();

            $sc_ret_res = ShoppingCart::where('status',1)->orderBy('id','DESC')->select('id','qty','item','price','total')->get();
                                
            foreach($sc_ret_res as $i=>$val_ret_res){
                $d[$i]['id'] = $val_ret_res->id;
                $d[$i]['qty'] = $val_ret_res->qty;
                $itemx = Items::find($val_ret_res->item);
                //$gds = Goods::where('item',$val_ret_res->item)->select('ceil_price','floor_price')->orderBy('id','DESC')->limit(1)->first();
                $d[$i]['ceil_price'] = $itemx->ceil_price;
                $d[$i]['floor_price'] = $itemx->floor_price;
                $d[$i]['code_no'] = $itemx->code_no;
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

        if($sc_res->count() ==0){
            return "[]";
        }
      
    }

    public function sc_items(){

        $sc_ret_res = ShoppingCart::where('status',1)->orderBy('id','DESC')->select('id','qty','item','price','total')->get();
                                
        foreach($sc_ret_res as $i=>$val_ret_res){
            $itemx = Items::find($val_ret_res->item);
            $d[$i]['id'] = $val_ret_res->id;
            $d[$i]['qty'] = $val_ret_res->qty;
            $d[$i]['ceil_price'] = $itemx->ceil_price;
            $d[$i]['floor_price'] = $itemx->floor_price;
            $d[$i]['code'] = $itemx->look_up;
            $d[$i]['code_no'] = $itemx->code_no;
            $d[$i]['item'] = $val_ret_res->id;
            $d[$i]['item_name'] = $itemx->item_desc;
            $d[$i]['price'] = number_format($itemx->sell_price);
            $d[$i]['total'] = $val_ret_res->total;
        }

        if($sc_ret_res->count() > 0){
            return json_encode($d);
        }else{
            return "[]";
        }
        
    }

    public function xchange_only_sc_items(){
        $sc_ret_res = ShoppingCart::where('status',1)->where('type','xchange')->orderBy('id','DESC')->select('id','qty','item','price','total')->get();
                                
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

    public function return_for_xchange(Request $request){
        $br = Branches::select('id')->where('curr',1)->orderBy('id', 'DESC')->limit(1)->first();
        
        
        $qty = $request->qty; 
        $sc_id = $request->sc_id;

        $sc = ShoppingCart::find($sc_id);

        if($qty <= $sc->qty){

            $rtrn_chk = ShoppingCart::where('type','return')->where('item',$sc->item)->where('status',1)->get();

            if($rtrn_chk->count() ==0){

                $new_sc = new ShoppingCart();
                $new_sc->qty = $qty;
                $new_sc->item = $sc->item;
                $itemx = Items::find($sc->item);
                $new_sc->price = $itemx->sell_price;
                $new_sc->total = $itemx->sell_price * $qty;
                $new_sc->tax = ($itemx->sell_price * $qty) * Tax::find($itemx->tax)->perc;
                $new_sc->status = 1;
                $new_sc->customer = $sc->customer;
                $new_sc->cost = $itemx->buy_price * $qty;
                $new_sc->time = time();
                $new_sc->uid = $request->session()->get('uid');
                $new_sc->type = "return";
                $new_sc->branch = $br->id;
                $new_sc->save();

            }else{

                foreach($rtrn_chk as $val_chk){
                    $new_sc = ShoppingCart::find($val_chk->id);

                    if(($qty + $new_sc->qty) <= $sc->qty){
    
                        $new_sc->qty = $qty + $new_sc->qty;
                        $new_sc->item = $sc->item;
                        $itemx = Items::find($sc->item);
                        $new_sc->price = $itemx->sell_price;
                        $new_sc->total = $itemx->sell_price * ($qty + $new_sc->qty);
                        $new_sc->tax = ($itemx->sell_price * ($qty + $new_sc->qty)) * Tax::find($itemx->tax)->perc;
                        $new_sc->status = 1;
                        $new_sc->customer = $new_sc->customer;
                        $new_sc->cost = $itemx->buy_price * ($qty + $new_sc->qty);
                        $new_sc->time = time();
                        $new_sc->uid = $request->session()->get('uid');
                        $new_sc->type = "return";
                        $new_sc->branch = $br->id;
                        $new_sc->save();
    
                    }
                }
                

            }
            

        }
        

        $sc_res = ShoppingCart::where('status',1)->where('type','return')->where('uid',$request->session()->get('uid'))->get();
        foreach($sc_res as $i=>$val_sc_res){
            $d[$i]['id'] = $val_sc_res->id;
            $d[$i]['qty'] = $val_sc_res->qty;
            $itemx = Items::find($val_sc_res->item);
            $d[$i]['code'] = $itemx->look_up;
            $d[$i]['item'] = $val_sc_res->id;
            $d[$i]['item_name'] = $itemx->item_desc;
            $d[$i]['price'] = $val_sc_res->price;
            $d[$i]['total'] = $val_sc_res->total;
            
        }
        echo json_encode($d);
    }

    public function sc_items_xchange(){

        $sc_ret_res = ShoppingCart::where('status',1)->where('type','xchange')->orderBy('id','DESC')->select('id','qty','item','price','total')->get();
                                
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

    public function xchange_sc_totals(Request $request){
        $br = Branches::select('id')->where('curr',1)->orderBy('id', 'DESC')->limit(1)->first();
        
        $xchange = ShoppingCart::where('type','xchange')->where('status','1')->where('branch',$br->id)->sum('total');
        $sc_acc = ShoppingCart::where('type','tender')->where('status','1')->where('branch',$br->id)->sum('total');
        if($request->customer){
            $cust = Customers::find($request->customer);
            $acc = $sc_acc + $cust->credit;

        }else{
            $acc = $sc_acc;
        }
        
        return json_encode(array('xchange'=>number_format(round($xchange,2)),'acc_total'=>number_format(round($acc,2))));
    
    }

    public function sc_totals(){

        $br = Branches::select('id')->where('curr',1)->orderBy('id', 'DESC')->limit(1)->first();
        

        $sum_qty = ShoppingCart::where('status',1)->where('branch',$br->id)->sum('qty');
        $sum_total = ShoppingCart::where('status',1)->where('branch',$br->id)->sum('total');
        $sum_tax = ShoppingCart::where('status',1)->where('branch',$br->id)->sum('tax');

        return json_encode(array('sum_qty'=>number_format(round($sum_qty,2)),'sum_total'=>number_format(round($sum_total,2)),'sum_tax'=>number_format(round($sum_tax),2)));
    
    }

    public function check_sc(){

        $sc_check = ShoppingCart::where('status',1)->select('id')->get();

        return json_encode(array('cart_count'=>$sc_check->count()));
        

    }

    public function check_held_sc(Request $request){
        $sc_check = ShoppingCart::where('tid',$request->held_id)->select('id')->get();

        return json_encode(array('cart_count'=>$sc_check->count()));
        
    }

    public function fetch_items(){


        $sc =  DB::select("SELECT SUM(qty) AS qty, item FROM shopping_cart WHERE status='2' AND type='tender' GROUP BY item ORDER BY qty DESC LIMIT 15");
        
        if(count($sc) > 5){
            foreach($sc as $i=>$val){
                $itemx = Items::find($val->item);
                if($itemx->status ==1){
                    $d[$i]['id'] = $val->item;
                    $d[$i]['look_up'] = $itemx->look_up;
                    $d[$i]['sell_price'] = number_format($itemx->sell_price);
                    $d[$i]['code_no'] = $itemx->code_no;
                    $d[$i]['item_desc'] = ucfirst($itemx->item_desc);
                    $gds = Goods::where('item',$val->item)->where('status',1)->sum('qty');
                    $sc = ShoppingCart::where('item',$val->item)->where('status',2)->where('type','tender')->sum('qty');
                    $sc_ret = ShoppingCart::where('item',$val->item)->where('status',4)->where('type','return')->sum('qty');
                    $d[$i]['qty'] = number_format(round($gds - ($sc + $sc_ret)));
                }
                
            }
        }else{

            $items = Items::select('item_desc','code_no','look_up','id','sell_price')->limit(15)->where('status',1)->get();

            foreach($items as $i=>$val){
                $d[$i]['id'] = $val->id;
                $d[$i]['look_up'] = $val->look_up;
                $d[$i]['sell_price'] = number_format($val->sell_price);
                $d[$i]['item_desc'] = ucfirst($val->item_desc);
                $d[$i]['code_no'] = $val->code_no;
                $gds = Goods::where('item',$val->id)->where('status',1)->sum('qty');
                $sc = ShoppingCart::where('item',$val->id)->where('status',2)->where('type','tender')->sum('qty');
                $sc_ret = ShoppingCart::where('item',$val->id)->where('status',4)->where('type','return')->sum('qty');
                $d[$i]['qty'] = number_format(round($gds - ($sc + $sc_ret)));
            }
        }

        return json_encode($d);
    }

    public function add_item_to_sc(Request $request){

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


        $items = Items::find($request->item_id);
        
        $res_gds = Goods::where('item',$request->item_id)->select('cost','id','price')->orderBy('id','DESC')->limit(1)->first();

        //check drawer
        $drawer = Drawer::select('id','start_time')->where('status','open')->get();
        if($drawer->count() > 0){

            foreach($drawer as $val){
                $id = $val->id;
                $start_time = $val->start_time;

                if(date("d",time()) == date("d",$start_time)){

                    if($res_gds){
                        $br = Branches::select('id')->where('curr',1)->orderBy('id', 'DESC')->limit(1)->first();
                             
                        $sc_check = ShoppingCart::select('id','qty')->where('branch',$br->id)->where('type',$request->type)->where('status',1)->where('item',$request->item_id)->get();
                        
                        if($sc_check->count() == 0){
                                $sc = new ShoppingCart();
                                $sc->item = $request->item_id;
                                $sc->price = $items->sell_price;
                                $sc->type = $request->type;
                                $sc->catg = Items::select('catg')->find($request->item_id)->catg;
                                $sc->in_stock = $res_gds->id;
                                $sc->branch = $br->id;
                                $sc->cost = $items->buy_price;
                                $sc->customer  = $cust_id;
                                $sc->tax = ceil(Tax::find($items->tax)->perc * $items->sell_price);
                                $sc->total = $items->sell_price;
                                $sc->uid = $request->session()->get('uid');
                                $sc->time = time();
                                $sc->qty = 1;
                                $sc->status = 1;
                                $sc->save(); 
            
                                echo json_encode(array('status'=>1,'customer'=>$cust_id));
            
                        }else{
            
                            foreach($sc_check as $val_sc_check){
                                
                                $curr_sc_qty = $val_sc_check->qty;
                                $curr_sc_id = $val_sc_check->id;
                                
                                $curr_sc_up = ShoppingCart::find($curr_sc_id);
                                $curr_sc_up->qty = $curr_sc_qty + 1;
                                $curr_sc_up->cost = $curr_sc_up->cost + $items->buy_price;
                                $curr_sc_up->tax = ceil(Tax::find($items->tax)->perc * $items->sell_price) + $curr_sc_up->tax;
                                $curr_sc_up->total = $items->sell_price * ($curr_sc_qty + 1);
                                $curr_sc_up->save();
            
                                echo json_encode(array('status'=>1,'customer'=>$cust_id));
            
                            }
            
                        }
                    
                    }else{
                        return json_encode(array('status'=>'G'));
                    }

                }else{
                    return json_encode(array('status'=>'Y'));
                }

            }

        }else{
            return json_encode(array('status'=>'X'));
        }

    }

    public function search_item_pull_up(Request $request){

        if(empty($request->item)){
            $item = Items::select('id','code_no','look_up','item_desc','sell_price','qty')->where('status',1)->limit(10)->get();
       
        }else{
            $ex_item = array(explode(' ',$request->item));
            //print_r($ex_item);
            $qry = "SELECT * FROM items WHERE ";
            for($x = 0;$x < count($ex_item[0]); $x++){
                
                
                //print_r($ex_item[0][$x]) . "<br>";

                $qry .= " item_desc LIKE '%" . $ex_item[0][$x] . "%' ";
                if($x < count($ex_item[0]) - 1){
                    $qry .= " AND ";
                }
                
            }

            $qry .= " OR code_no LIKE '%" . $request->item . "%'";

            $qry .= " LIMIT 10";
            
            $item = DB::select($qry);

            //$item = Items::select('id','look_up','code_no','item_desc','sell_price','qty')->where('look_up', 'like', '%' . $request->item . '%')->orWhere('item_desc', 'like' , '%' . $request->item . '%')->orWhere('code_no', 'like' , '%' . $request->item . '%')->where('status',1)->limit(10)->get();
       
        }

        if($item){

            foreach($item as $i=>$val){
                $d[$i]['id'] = $val->id;
                $d[$i]['code_no'] = $val->code_no;
                $d[$i]['look_up'] = ucfirst($val->look_up);
                $d[$i]['item_desc'] = $val->item_desc . ' - ' .$val->code_no;
                $d[$i]['sell_price'] = number_format($val->sell_price);
                $gds = Goods::where('item',$val->id)->where('status',1)->sum('qty');
                $sc = ShoppingCart::where('item',$val->id)->where('status',2)->where('type','tender')->sum('qty');
                $sc_ret = ShoppingCart::where('item',$val->id)->where('status',4)->where('type','return')->sum('qty');
                $d[$i]['qty'] = number_format(round($gds - ($sc + abs($sc_ret))));
            }
        }else{
            $d = [];
        }

        
        
        return json_encode($d);
    }

    public function csr_update(Request $request){
        
        $csr = Csr::find(1);
        $csr->csr_val = $request->csr_val;
        $csr->save();
    }

    public function csr_check(){
        $csr = Csr::find(1);
        return json_encode(array('val'=>$csr->csr_val));
    }

    public function update_price(Request $request){
        //new_price:new_price,sc_id:this.state.mboxIndex

        
        $sc = ShoppingCart::find($request->sc_id);
        //$res_gds = Goods::where('item',$sc->item)->select('cost','id','price')->orderBy('id','DESC')->limit(1)->first();
        $item = Items::find($sc->item);
        $price = $item->sell_price;
        $cost = $item->buy_price;
        $qty = $sc->qty;
        

        

        if($request->new_price <= $item->ceil_price && $request->new_price >= $item->floor_price ){
            
            
            $tax = Tax::find($item->tax)->perc;
            //$item = Items::find($sc->item)->perc;
            
            $new_price = ceil($qty * $request->new_price);
            $new_tax = ceil($tax * ($new_price * $qty));
            $new_cost = ceil($cost * $qty);
            
            $sc->price = $request->new_price;
            $sc->total = $new_price;
            $sc->cost = $new_cost;
            $sc->tax = $new_tax;
            $sc->qty = $qty;
            $sc->save();

            $sc_ret_res = ShoppingCart::where('status',1)->orderBy('id','DESC')->get();
                                    
            foreach($sc_ret_res as $i=>$val_ret_res){
                $d[$i]['id'] = $val_ret_res->id;
                $d[$i]['qty'] = $val_ret_res->qty;
                $itemx = Items::find($val_ret_res->item);
                //$gds = Goods::where('item',$val_ret_res->item)->select('ceil_price','floor_price')->orderBy('id','DESC')->limit(1)->first();
                $d[$i]['ceil_price'] = $itemx->ceil_price;
                $d[$i]['floor_price'] = $itemx->floor_price;
                $d[$i]['code'] = $itemx->look_up;
                $d[$i]['code_no'] = $itemx->code_no;
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
       
    }

    public function mbox_qty_update(Request $request){
        //sc_id:propertyName,qty:event.target.value,
        
        $sc = ShoppingCart::find($request->sc_id);
        //$res_gds = Goods::where('item',$sc->item)->select('cost','id','price')->orderBy('id','DESC')->limit(1)->first();
        $items = Items::where('item',$sc->item)->select('buy_price','id','sell_price')->orderBy('id','DESC')->limit(1)->first();
        $price = $items->sell_price;
        $cost = $items->buy_price;
        $item_tax = Items::find($sc->item)->tax;
        $tax = Tax::find($item_tax)->perc;
       
        $new_price = ceil($price * $request->qty);
        $new_tax = ceil($tax * ($price * $request->qty));
        $new_cost = ceil($cost * $request->qty);
       
        $sc->total = $new_price;
        $sc->tax = $new_tax;
        $sc->qty = $request->qty;
        $sc->cost = $new_cost; 
        $sc->save();

        $sc_ret_res = ShoppingCart::where('status',1)->orderBy('id','DESC')->select('id','qty','item','price','total')->get();
                                
            foreach($sc_ret_res as $i=>$val_ret_res){
                $d[$i]['id'] = $val_ret_res->id;
                $d[$i]['qty'] = $val_ret_res->qty;
                $itemx = Items::find($val_ret_res->item);
                //$gds = Goods::where('item',$val_ret_res->item)->select('ceil_price','floor_price')->orderBy('id','DESC')->limit(1)->first();
                $d[$i]['ceil_price'] = $itemx->ceil_price;
                $d[$i]['floor_price'] = $itemx->floor_price;
                $d[$i]['code'] = $itemx->look_up;
                $d[$i]['code_no'] = $itemx->code_no;
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

    public function mbox_price_update(Request $request){
        //sc_id:propertyName,qty:event.target.value,
        
        $sc = ShoppingCart::find($request->sc_id);
        //$res_gds = Goods::where('item',$sc->item)->select('cost','id','price')->orderBy('id','DESC')->limit(1)->first();
        $items = Items::find($sc->item);
        $price = $items->sell_price;
        $cost = $items->buy_price;
        $qty = $sc->qty;
        //$item = Items::find($sc->item);

        
        if($request->price <= $items->ceil_price && $request->price >= $items->floor_price ){
            //$item = Items::find($sc->item);
            $tax = Tax::find($item->tax)->perc;
            //$item = Items::find($sc->item)->perc;
            
            $new_price = ceil($qty * $request->price);
            $new_tax = ceil($tax * ($new_price * $qty));
            $new_cost = ceil($cost * $qty);
            
            $sc->price = $request->price;
            $sc->total = $new_price;
            $sc->cost = $new_cost;
            $sc->tax = $new_tax;
            $sc->qty = $request->qty;
            $sc->save();

            $sc_ret_res = ShoppingCart::where('status',1)->orderBy('id','DESC')->select('id','qty','item','price','total')->get();
                                    
            foreach($sc_ret_res as $i=>$val_ret_res){
                $d[$i]['id'] = $val_ret_res->id;
                $d[$i]['qty'] = $val_ret_res->qty;
                $itemx = Items::find($val_ret_res->item);
                //$gds = Goods::where('item',$val_ret_res->item)->select('ceil_price','floor_price')->orderBy('id','DESC')->limit(1)->first();
                $d[$i]['ceil_price'] = $itemx->ceil_price;
                $d[$i]['floor_price'] = $itemx->floor_price;
                $d[$i]['code'] = $itemx->look_up;
                $d[$i]['code_no'] = $itemx->code_no;
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


    }


    public function new_goods_received(Request $request){
        
        //$chk_g = Goods::where('receipt_no',$request->receipt_no)->where('status',1)->select('id')->get();
        $br = Branches::where('curr',1)->select('id')->orderBy('id','DESC')->limit(1)->first();
            
       // if($chk_g->count() ==0){
            
            $prv_g = Goods::select('qty','cost','price','ceil_price','floor_price')->where('item',$request->item)->where('status',1)->orderBy('id','DESC')->limit(1)->first();
            if($prv_g){
                if($request->cost != $prv_g->cost){
                    $sum_gds = Goods::where('status',1)->where('item',$request->item)->sum('qty');
                    $sum_sc = ShoppingCart::where('status',2)->where('type','tender')->where('item',$request->item)->sum('qty');
                    $sum_rtrn = ShoppingCart::where('status',4)->where('type','return')->where('item',$request->item)->sum('qty');
                    $stock = $sum_gds - ($sum_sc - $sum_rtrn);

                    $weighted_avg = round((($stock * $prv_g->cost) + ($request->qty * $request->cost)) / ($prv_g->qty + $request->qty));
                
                    $up_item = Items::find($request->item);
                    $up_item->buy_price = $weighted_avg;
                    $up_item->save();

                    
                }

                if($request->price != $prv_g->price){
                    $up_item = Items::find($request->item);
                    $up_item->sell_price = $request->price;
                    $up_item->save();
                }

                if($request->ceil != $prv_g->ceil_price){
                    $up_item = Items::find($request->item);
                    $up_item->ceil_price = $request->ceil;
                    $up_item->save();
                }

                if($request->floor != $prv_g->floor_price){
                    $up_item = Items::find($request->item);
                    $up_item->floor_price = $request->floor;
                    $up_item->save();
                }

            }

            
            $g = new Goods();
            $g->date_received = time();
            $g->item = $request->item;
            $g->received_by = $request->session()->get('uid');
            $g->receipt_no = $request->receipt_no;
            $g->cost = $request->cost;
            $g->qty = $request->qty;
            $g->branch = $br->id;
            $g->price = $request->price;
            $g->ceil_price = $request->ceil;
            $g->floor_price = $request->floor;
            $g->status = 1;
            $g->comments = $request->comm;
            $g->save();

            
            echo json_encode(array('status'=>1));

        /*
        }else{
            echo json_encode(array('status'=>0));
        }
        */

    }

    public function fetch_goods_item(Request $request){
        
        $res = Items::where('id',$request->item_id)->select('item_desc','buy_price','sell_price','floor_price','ceil_price')->get();

        echo json_encode($res);
    }

    public function goods_reports_data(Request $request){

        $first_day_morn = strtotime(date('01-m-Y') . " 06:00:00");
        $first_day_eve = strtotime(date('01-m-Y') . " 23:59:59");
        $last_day_eve = strtotime(date('t-m-Y') . " 23:59:59");


        $res = Goods::select('id','up_id','date_received','item','received_by','status','receipt_no','qty','cost','price','ceil_price','floor_price')->whereBetween('date_received',array($first_day_morn,$last_day_eve))->orderBy('id','desc')->get();
        
        if($res->count() > 0){
            foreach($res as $i=>$val){
                $d[$i]['id'] = $val->id;
                $d[$i]['date_received'] = ($val->date_received) ? date("d-m-y",$val->date_received) : '';
                $d[$i]['item'] = ($val->item) ? Items::find($val->item)->item_desc : "";
                //$d[$i]['item'] = $val->item;
               
                $d[$i]['received_by'] = ($val->received_by) ? User::find($val->received_by)->lname : '';
                $d[$i]['receipt_no'] = $val->receipt_no;
                $d[$i]['status'] = $val->status;
                $d[$i]['up_id'] = $val->up_id;
                $d[$i]['qty'] = number_format($val->qty);
                $d[$i]['cost'] = number_format($val->cost);
                $d[$i]['price'] = number_format($val->price);
                $d[$i]['ceil_price'] = number_format($val->ceil_price);
                $d[$i]['floor_price'] = number_format($val->floor_price);
            }

        }else{
            $d = [];
        }
        

        echo json_encode($d);
    }

    public function search_goods_rprt(Request $request){

         $exp_dates = explode("to",$request->dates);
         $from = strtotime(str_replace(' ', '', $exp_dates[0]). " 06:00:00");
         $to = strtotime(str_replace(' ', '', $exp_dates[1]). " 23:59:59");
         
        $res = Goods::select('id','up_id','date_received','item','received_by','status','receipt_no','qty','cost','price','ceil_price','floor_price')->whereBetween('date_received',array($from,$to))->orderBy('id','desc')->get();
        
        if($res->count() > 0){
            foreach($res as $i=>$val){
                $d[$i]['id'] = $val->id;
                $d[$i]['date_received'] = ($val->date_received) ? date("d-m-y",$val->date_received) : '';
                $d[$i]['item'] = ($val->item) ? Items::find($val->item)->item_desc : "";
                //$d[$i]['item'] = $val->item;
               
                $d[$i]['received_by'] = ($val->received_by) ? User::find($val->received_by)->lname : '';
                $d[$i]['receipt_no'] = $val->receipt_no;
                $d[$i]['status'] = $val->status;
                $d[$i]['up_id'] = $val->up_id;
                $d[$i]['qty'] = number_format($val->qty);
                $d[$i]['cost'] = number_format($val->cost);
                $d[$i]['price'] = number_format($val->price);
                $d[$i]['ceil_price'] = number_format($val->ceil_price);
                $d[$i]['floor_price'] = number_format($val->floor_price);
            }

        }else{
            $d = [];
        }
        

        echo json_encode($d);
    }

    public function goods_transfer_reports_data(Request $request){
        $res = Goods::select('id','status','transfer_received_by','transfer_by','transfer_date','branch','from_branch','date_received','item','received_by','delivery_note_no','receipt_no','qty','cost','price','ceil_price','floor_price')->orderBy('id','desc')->limit(15)->get();
        $br = Branches::where('curr',1)->select('id')->orderBy('id','DESC')->limit(1)->first();
        

        if($res->count() > 0){
            foreach($res as $i=>$val){
                $d[$i]['id'] = $val->id;
                
                $d[$i]['date_received'] = ($val->date_received) ? date("d-m-y",$val->date_received) : '';
                $d[$i]['transfer_date'] = ($val->transfer_date) ? date("d-m-y",$val->transfer_date) : '';
                $d[$i]['item'] = Items::find($val->item)->item_desc;
                $d[$i]['item_id'] = $val->item;
                $ttl_goods = Goods::where('item',$val->item)->where('status',1)->sum('qty');
                $ttl_sc = ShoppingCart::where('item',$val->item)->where('status',2)->where('type','tender')->sum('qty') - ShoppingCart::where('item',$val->item)->where('type','return')->where('status',4)->sum('qty');
                $d[$i]['act_qty'] = $ttl_goods - $ttl_sc;
                 //$d[$i]['reg_time'] = ($val->reg_time) ? date("d-m-Y H:i:s",$val->reg_time) : '';
                 $d[$i]['received_by'] = ($val->received_by) ? User::find($val->received_by)->lname : '';
                 $d[$i]['transfer_recieved_by'] = ($val->transfer_recieved_by) ? User::find($val->transfer_recieved_by)->lname : '';
                $d[$i]['transfer_by'] = ($val->transfer_by) ? User::find($val->transfer_by)->lname : '';
                $d[$i]['from_branch'] = ($val->from_branch) ? Branches::find($val->from_branch)->branch : '';
                $d[$i]['branch'] = ($val->branch) ? Branches::find($val->branch)->branch : '';
                $d[$i]['curr_branch'] = $br->id;
                $d[$i]['branch_id'] = $val->branch;
                $d[$i]['status'] = $val->status;
                $d[$i]['receipt_no'] = $val->receipt_no;
                $d[$i]['delivery_note_no'] = $val->delivery_note_no;
                $d[$i]['qty'] = number_format($val->qty);
                $d[$i]['cost'] = number_format($val->cost);
                $d[$i]['price'] = number_format($val->price);
                $d[$i]['ceil_price'] = number_format($val->ceil_price);
                $d[$i]['floor_price'] = number_format($val->floor_price);
            
            }
        }else{
            $d = [];
        }

        

        echo json_encode($d);
    }

    public function delete_from_goods(Request $request){
            
            $gds = Goods::find($request->id);
            $gds->status = 0;
            $gds->save();
           
    }

    public function search_goods(Request $request){
       
        
        $res_item = Items::select('id')->where('item_desc',$request->item)->first();

        if($res_item){
            $br = Branches::where('curr',1)->select('id')->orderBy('id','DESC')->limit(1)->first();
        

            $res = Goods::orderBy('id','desc')->where('item',$res_item->id)->get();
            
            if($res->count() > 0){

                foreach($res as $i=>$val){

                    $d[$i]['id'] = $val->id;
                    
                    $d[$i]['date_received'] = ($val->date_received) ? date("d-m-y",$val->date_received) : '';
                    $d[$i]['transfer_date'] = ($val->transfer_date) ? date("d-m-y",$val->transfer_date) : '';
                    $d[$i]['item'] = Items::find($val->item)->item_desc;
                    $d[$i]['item_id'] = $val->item;
                    $ttl_goods = Goods::where('item',$val->item)->where('status',1)->sum('qty');
                    $ttl_sc = ShoppingCart::where('item',$val->item)->where('status',2)->where('type','tender')->sum('qty') - ShoppingCart::where('item',$val->item)->where('type','return')->where('status',4)->sum('qty');
                    $d[$i]['act_qty'] = $ttl_goods - $ttl_sc;
                    //$d[$i]['reg_time'] = ($val->reg_time) ? date("d-m-Y H:i:s",$val->reg_time) : '';
                    $d[$i]['received_by'] = ($val->received_by) ? User::find($val->received_by)->lname : '';
                    $d[$i]['transfer_recieved_by'] = ($val->transfer_recieved_by) ? User::find($val->transfer_recieved_by)->lname : '';
                    $d[$i]['transfer_by'] = ($val->transfer_by) ? User::find($val->transfer_by)->lname : '';
                    $d[$i]['from_branch'] = ($val->from_branch) ? Branches::find($val->from_branch)->branch : '';
                    $d[$i]['branch'] = ($val->branch) ? Branches::find($val->branch)->branch : '';
                    $d[$i]['curr_branch'] = $br->id;
                    $d[$i]['branch_id'] = $val->branch;
                    $d[$i]['status'] = $val->status;
                    $d[$i]['receipt_no'] = $val->receipt_no;
                    $d[$i]['delivery_note_no'] = $val->delivery_note_no;
                    $d[$i]['qty'] = number_format($val->qty);
                    $d[$i]['cost'] = number_format($val->cost);
                    $d[$i]['price'] = number_format($val->price);
                    $d[$i]['ceil_price'] = number_format($val->ceil_price);
                    $d[$i]['floor_price'] = number_format($val->floor_price);
                
                }

            }else{
                
                $d = [];
            }
            

        }else{

            $d = [];

        }
         

        echo json_encode($d);
        
    }

    public function transfer_goods_branch(Request $request){
        
        $gds = new Goods();
        $gds->receipt_no = $request->receipt_no;
       
        $gds->from_branch = $request->c_branch;
        $gds->qty = "-".$request->qty;
        $gds->branch = $request->branch;
        $gds->item = $request->item;
        $gds->cost = $request->cost;
        $gds->price = $request->price;
        $gds->floor_price = $request->floor;
        $gds->ceil_price = $request->ceil;
        $gds->transfer_date = time();
        $gds->down = 1;
        $gds->status = 1;
        $gds->transfer_by = $request->session()->get('uid');
        $gds->save();
       
        echo json_encode(array('status'=>1));

    }

    public function create_delivery_note(Request $request){

            $note_name = strtoupper($request->note_name). substr(time(),2,-1);
            $goods = $request->goods;

            $gds_arr = explode(",",$goods);
           
            

            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial','B',16);
            $pdf->Cell(130,5,'NOVEL GOLF SHOP',0,0);
            $pdf->Cell(59,5,'DELIVERY NOTE',0,1);

            $pdf->SetFont('Arial','',12);
            $pdf->Cell(130,5,'P.O. Box 12345',0,0);
            $pdf->Cell(59,5,'',0,1);

            $pdf->Cell(130,5,'Nairobi',0,0);
            $pdf->Cell(25,5,'Date',0,0);
            $pdf->Cell(34,5,date("d-m-Y",time()),0,1);

            $pdf->Cell(130,5,'Phone +12345678',0,0);
            $pdf->Cell(25,5,'Time',0,0);
            $pdf->Cell(34,5,' ' . date("H:i:s",time()),0,1);

            $pdf->Cell(130,5,'Delivery Note Number: ' . $note_name,0,0);


            //make dummy cell
            $pdf->Cell(189,10,'',0,1);


            //make dummy cell
            $pdf->Cell(189,10,'',0,1);

            //invoice contents
            $pdf->SetFont('Arial','B',10);

            $pdf->Cell(15,6,'Qty',1,0, 'R');
            $pdf->Cell(15,6,'Cost',1,0, 'R');
            $pdf->Cell(15,6,'Price',1,0, 'R');
            $pdf->Cell(15,6,'Ceil',1,0, 'R');
            $pdf->Cell(15,6,'Floor',1,0, 'R');
            $pdf->Cell(58,6,'From',1,0, 'R');
            $pdf->Cell(58,6,'To',1,1, 'R');

            $pdf->SetFont('Arial','',10);

            for($x = 0; $x <= (count($gds_arr) - 1); $x++){
               
                $up_gds = Goods::find($gds_arr[$x]);
                $up_gds->delivery_note_no = $note_name;
                $up_gds->dn_print_time = time();
                $up_gds->up = 0;
                $up_gds->dn_printed_by = $request->session()->get('uid');
                $up_gds->save();

                $pdf->Cell(191,6,Items::find($up_gds->item)->item_desc,1,1, '');

                $pdf->Cell(15,6,abs($up_gds->qty),1,0, 'R');
                $pdf->Cell(15,6,number_format($up_gds->cost),1,0, 'R');
                $pdf->Cell(15,6,number_format($up_gds->price),1,0, 'R');
                $pdf->Cell(15,6,number_format($up_gds->ceil_price),1,0, 'R');
                $pdf->Cell(15,6,number_format($up_gds->floor_price),1,0, 'R');
                $pdf->Cell(58,6,($up_gds->from_branch) ? Branches::find($up_gds->from_branch)->branch : '',1,0, 'R');
                $pdf->Cell(58,6,($up_gds->branch) ? Branches::find($up_gds->branch)->branch : '',1,1, 'R');

            }

           
            //make dummy cell
            $pdf->Cell(189,10,'',0,1);

            $pdf->SetFont('Arial','B',12);
            $pdf->Cell(130,5,'',0,0);
            $pdf->Cell(59,5,'Printed By ' . ucfirst(User::find($request->session()->get('uid'))->fname) . ' ' .ucfirst(User::find($request->session()->get('uid'))->lname) ,0,1);


            $filename= "prints/" . $note_name . ".pdf";
            $pdf->Output($filename,'F');

            echo json_encode(array('status'=>1));
        
    }

    public function receive_goods(Request $request){

        $gds = Goods::find($request->id);
        $gds->date_received = time();
        $gds->received_by = $request->session()->get('uid');
        $gds->status = 1;
        //$gds->up = 0;
        $gds->save();

        /*
        $n_gds = new Goods();
        $n_gds->date_received = time();
        $n_gds->item = $gds->item;
        $n_gds->received_by = $request->session()->get('uid');
        $n_gds->qty = abs($gds->qty);
        $n_gds->cost = $gds->cost;
        $n_gds->price = $gds->price;
        $n_gds->ceil_price = $gds->ceil_price;
        $n_gds->floor_price = $gds->floor_price;
        $n_gds->branch = $gds->branch;
        $n_gds->from_branch = $gds->from_branch;
        $n_gds->save();
        */

        return json_encode(array('status'=>1));

    }

    public function delete_goods(Request $request){
        $gds = Goods::find($request->id);
        $gds->status = 0;
        $goods->date_received = time();
        $goods->received_by = $request->session()->get('uid');
        $gds->save();
        return json_encode(array('status'=>1));
    }

    public function remove_xchange_sc(Request $request){
        $sc = ShoppingCart::find($request->item);
        $sc->status = 0;
        $sc->save();
    }

    public function delivery_notes_reports(Request $request){
        $dn = Goods::select('transfer_date','transfer_by','delivery_note_no','dn_print_time','dn_printed_by')->orderBy('id','DESC')->groupBy('delivery_note_no')->get();
        if($dn->count() > 0){
            foreach($dn as $i=>$val){
                    if(!empty($val->delivery_note_no)){
                        $d[$i]['transfer_date'] = date("d-m-y h:i:s",$val->transfer_date);
                        $d[$i]['transfer_by'] = ($val->transfer_by) ? User::find($val->transfer_by)->lname : '';
                        $d[$i]['delivery_note_no'] = $val->delivery_note_no;
                        $d[$i]['dn_print_time'] = date("d-m-y h:i:s",$val->dn_print_time);
                        $d[$i]['dn_printed_by'] = ($val->dn_printed_by) ? User::find($val->dn_printed_by)->lname : '';
                    
                    
                    }
                    
            }

        }else{
            $d = [];
        }
        
        return json_encode($d);
    }

    public function remove_acc_sc_item(Request $request){
            $sc = ShoppingCart::find($request->sc_id);
            $sc->status = 0;
            $sc->save();

           $ac = Accounts::where('customer',$sc->customer)->where('item',$sc->item)->where('status',0)->orderBy('id','DESC')->limit(1)->first();
           $ac->delete();
           return json_encode(array('status'=>1));
    }

    public function new_add_sc_acc_item(Request $request){
        $id = $request->id;
        $item = $request->item;
        $customer = $request->customer;
        $ttl_qty = $request->ttl_qty;
        $qty = $request->qty;
        $event = $request->event;
        

        $tx = Tax::find((Items::find($item)->tax))->perc;
        $itemx = Items::find($item);
        $price = $itemx->sell_price;
        $cost = $itemx->buy_price;
        
        //$stock = Goods::select('id')->where('item',$item)->orderBy('id', 'DESC')->limit(1)->first()->id;
        $br = Branches::select('id')->where('curr',1)->orderBy('id', 'DESC')->limit(1)->first();

        $sc_chk = ShoppingCart::select('id','qty','tax','price','cost','tax','total')->where('uid',$request->session()->get('uid'))->where('item',$item)->where('status',1)->where('branch',$br->id)->get();
        
        

            if($sc_chk->count() == 0){

                if($qty <= $ttl_qty){

                    if($request->session()->has('uid')){

                        $sc = new ShoppingCart();
                        $sc->qty = $qty;
                        $sc->item = $item;
                        $sc->price = $price;
                        $sc->cost = $cost;
                        $sc->catg = Items::select('catg')->find($item)->catg;
                        $sc->total = $price * $qty;
                        $sc->status = 1;
                        $sc->tax = $tx * ($price * $qty);
                        $sc->customer = $customer;
                        $sc->time = time();
                        $sc->uid = $request->session()->get('uid');
                        $sc->type = "tender";
                        $sc->branch = $br->id;
                        $sc->save();

                        }

                }

            }else{

                foreach($sc_chk as $val){

                    $sc_id = $val->id;
                    $sc_qty = $val->qty;
                    $sc_price = $val->price;
                    $sc_cost = $val->cost;
                    $sc_tax = $val->tax;
                    $sc_total = $val->total;
                    
                    if($qty <= $ttl_qty){
                        
                        if($request->session()->has('uid')){


                            $up_sc = ShoppingCart::find($sc_id);
                            $up_sc->qty = $sc_qty + $qty;
                            $up_sc->total = ($sc_qty + $qty) * $price;
                            $up_sc->cost = $sc_cost + $cost;
                            $up_sc->tax = ($sc_price + $price) * $tx;
                            $up_sc->save();
    
                            }
                    }
                    
                }

                /*
                $up_acc_chk = Accounts::select('id','ttl_qty','qty')->where('status',0)->where('event',$event)->where('item',$item)->where('customer',$customer)->orderBy('id','desc')->limit(1)->get();
                
                foreach($up_acc_chk as $val_acc_chk){

                    if($val_acc_chk->ttl_qty > 0){
                        $up_acc = Accounts::find($val_acc_chk->id);
                        $up_acc->qty = $val_acc_chk->qty - $qty; 
                        $up_acc->ttl_qty = $val_acc_chk->ttl_qty - $qty;
                        $up_acc->save();
                    } 
                }
                */

            }

            $up_acc_chk = Accounts::select('id','ttl_qty','qty')->where('status',0)->where('event',$event)->where('item',$item)->where('customer',$customer)->orderBy('id','desc')->limit(1)->get();
                
            if( $up_acc_chk->count() ==0){

                $chk_acc = Accounts::select('ttl_qty')->where('item',$item)->where('customer',$customer)->orderBy('id','desc')->limit(1)->first();
            
                if($request->session()->has('uid')){

                $acc = new Accounts();
                $acc->customer = $customer; 
                $acc->item = $item; 
                $acc->qty = "-" . $qty; 
                $acc->acc_date = time(); 
                $acc->status = 0; 
                $acc->event = $event;
                $acc->branch = $br->id; 
                $acc->ttl_qty = $chk_acc->ttl_qty - $qty;
                
                $acc->save();

                }

            }else{

                $up_acc_chk = Accounts::select('id','ttl_qty','qty')->where('status',0)->where('event',$event)->where('item',$item)->where('customer',$customer)->orderBy('id','desc')->limit(1)->get();
                
                foreach($up_acc_chk as $val_acc_chk){

                    if($val_acc_chk->ttl_qty > 0){

                        if($request->session()->has('uid')){
                            $up_acc = Accounts::find($val_acc_chk->id);
                            $up_acc->qty = $val_acc_chk->qty - $qty; 
                            $up_acc->ttl_qty = $val_acc_chk->ttl_qty - $qty;
                            $up_acc->save();
                        }
                    } 
                }

            }

            



        $sc_ret = ShoppingCart::select('id','qty','item','price','total')->where('uid',$request->session()->get('uid'))->where('status',1)->where('type','tender')->where('branch',$br->id)->get();

        foreach($sc_ret as $i=>$val_ret){
            $d[$i]['id'] = $val_ret->id;
            $d[$i]['qty'] = $val_ret->qty;
            $d[$i]['item'] = Items::find($val_ret->item)->item_desc;
            $d[$i]['price'] = $val_ret->price;
            $d[$i]['total'] = $val_ret->total;
        }

        return json_encode($d);
        
    }


    public function fetch_acc_sc_items(Request $request){
        $br = Branches::select('id')->where('curr',1)->limit(1)->first();
        $sc_ret = ShoppingCart::select('id','qty','item','price','total')->where('uid',$request->session()->get('uid'))->where('status',1)->where('type','tender')->where('branch',$br->id)->get();

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

    public function add_sc_acc_item(Request $request){
        //id:id,item:item
        $id = $request->id;
        $item = $request->item;
        $customer = $request->customer;
        $ttl_qty = $request->ttl_qty;
        
        $tx = Tax::find((Items::find($item)->tax))->perc;

        $price = Goods::select('price')->where('item',$item)->orderBy('id', 'DESC')->limit(1)->first()->price;
        $cost = Goods::select('cost')->where('item',$item)->orderBy('id', 'DESC')->limit(1)->first()->cost;
        $stock = Goods::select('id')->where('item',$item)->orderBy('id', 'DESC')->limit(1)->first()->id;
        $br = Branches::select('id')->where('curr',1)->orderBy('id', 'DESC')->limit(1)->first();

        $sc_chk = ShoppingCart::select('id','qty','tax','price','cost','tax','total')->where('uid',$request->session()->get('uid'))->where('item',$item)->where('status',1)->where('branch',$br->id)->get();
        
        if($sc_chk->count() ==0){
            $sc = new ShoppingCart();
            $sc->qty = 1;
            $sc->item = $item;
            $sc->price = $price;
            $sc->catg = Items::select('catg')->find($item)->catg;
            $sc->cost = $cost;
            $sc->total = $price;
            $sc->status = 1;
            $sc->tax = $tx * $price;
            $sc->customer = $customer;
            $sc->time = time();
            $sc->uid = $request->session()->get('uid');
            $sc->in_stock = $stock;
            $sc->type = "tender";
            $sc->branch = $br->id;
            $sc->save();


            $chk_acc = Accounts::select('ttl_qty')->where('status',1)->where('item',$item)->where('customer',$customer)->orderBy('id','desc')->limit(1)->first();
            
            $acc = new Accounts();
            $acc->customer = $customer; 
            $acc->item = $item; 
            $acc->qty = -1; 
            $acc->acc_date = time(); 
            $acc->status = 0; 
            $acc->branch = $br->id; 
            $acc->ttl_qty = $chk_acc->ttl_qty - 1;
            $acc->save();

        }else{

            foreach($sc_chk as $val){
                $sc_id = $val->id;
                $sc_qty = $val->qty;
                $sc_price = $val->price;
                $sc_cost = $val->cost;
                $sc_tax = $val->tax;
                $sc_total = $val->total;
                
                if($sc_qty < $ttl_qty){
                    
                    $up_sc = ShoppingCart::find($sc_id);
                    $up_sc->qty = $sc_qty + 1;
                    $up_sc->total = ($sc_qty + 1) * $price;
                    $up_sc->cost = $sc_cost + $cost;
                    $up_sc->tax = ($sc_price + $price) * $tx;
                    $up_sc->save();

                    
                }
                
            }

            $up_acc_chk = Accounts::select('id','ttl_qty','qty')->where('status',0)->where('item',$item)->where('customer',$customer)->orderBy('id','desc')->limit(1)->get();
                
            foreach($up_acc_chk as $val_acc_chk){

                if($val_acc_chk->ttl_qty > 0){
                    $up_acc = Accounts::find($val_acc_chk->id);
                    $up_acc->qty = $val_acc_chk->qty - 1; 
                    $up_acc->ttl_qty = $val_acc_chk->ttl_qty - 1;
                    $up_acc->save();
                } 
            }
                    
        }


        $sc_ret = ShoppingCart::select('id','qty','item','price','total')->where('uid',$request->session()->get('uid'))->where('status',1)->where('type','tender')->where('branch',$br->id)->get();

        foreach($sc_ret as $i=>$val_ret){
            $d[$i]['id'] = $val_ret->id;
            $d[$i]['qty'] = $val_ret->qty;
            $d[$i]['item'] = Items::find($val_ret->item)->item_desc;
            $d[$i]['price'] = $val_ret->price;
            $d[$i]['total'] = $val_ret->total;
        }

        return json_encode($d);
        
    }

    public function del_sc_acc_item(Request $request){

        $del_sc = ShoppingCart::find($request->id);
        $del_sc->status = 0;
        $del_sc->save();

        $br = Branches::select('id')->where('curr',1)->orderBy('id', 'DESC')->limit(1)->first();


        $chk_sc = ShoppingCart::find($request->id);
        $del_acc = Accounts::select('id')->where('item',$chk_sc->item)->where('branch',$br->id)->where('status',1)->orderBy('id', 'DESC')->limit(1)->first();
        foreach($del_acc as $val_acc){
            $act_del_acc = Accounts::find($val_acc->id);
            $act_del_acc->status = 0;
            $act_del_acc->save();
        }
        

        $sc_ret = ShoppingCart::select('id','qty','item','price','total')->where('uid',$request->session()->get('uid'))->where('status',1)->where('branch',$br->id)->get();

        if($sc_ret->count() > 0){
            foreach($sc_ret as $i=>$val_ret){
                $d[$i]['id'] = $val_ret->id;
                $d[$i]['qty'] = $val_ret->qty;
                $d[$i]['item'] = Items::find($val_ret->item)->item_desc;
                $d[$i]['price'] = $val_ret->price;
                $d[$i]['total'] = $val_ret->total;
            }
    
            return json_encode($d);
        }else{
            return json_encode(array('status'=>0));
        }
        
    }

    public function check_return(Request $request){
        
        $tr  = Transactions::select('id','total','receipt_no')->where('ref_no',$request->pay_val)->where('type','return')->get();
        $tr_rtrn_sum  = Transactions::where('ref_no',$request->pay_val)->where('type','return')->sum('total');
        if($tr->count() > 0){
            $tr_type = Transactions::select('type')->where('receipt_no',$request->pay_val)->first()->type;
            if($tr_type =="collection"){
                foreach($tr as $val){
                    $d['id'] = $val->id;
                    $d['receipt_no'] = $val->receipt_no;
                    
                    //get actual tr id
                    $act_tr_id = Transactions::select('id')->where('receipt_no',$request->pay_val)->first()->id;
                    
                    $chk_rtrn_tr = Transactions::where('ref_no',$request->pay_val)->where('type','return tender')->sum('total');
                    $p = Pool::select('qty','item')->where('sc_id',$act_tr_id)->first();
                    $rtrn_coll_ttl = abs($p->qty) * Items::find($p->item)->sell_price;
                    if($chk_rtrn_tr >= $rtrn_coll_ttl){
                        return json_encode(array('status'=>0));
                    }else{
                        $d['total'] = $rtrn_coll_ttl - $chk_rtrn_tr;
                        return json_encode($d);
                    }
                    
                }
            }else{
                foreach($tr as $val){
                    $d['id'] = $val->id;
                    $d['receipt_no'] = $val->receipt_no;
                    $chk_rtrn_tr = Transactions::where('ref_no',$request->pay_val)->where('type','return tender')->sum('total');
                    if($chk_rtrn_tr >= abs($tr_rtrn_sum)){
                        return json_encode(array('status'=>0));
                    }else{
                        $d['total'] = abs($tr_rtrn_sum) - $chk_rtrn_tr;
                        return json_encode($d);
                    }
                    
                }
            }
            
           
        }else{
            return json_encode(array('status'=>0));
        }
    }


    public function tester(Request $request){
               
        // Create new PHPExcel object
            $objPHPExcel = new PHPExcel();

            // Set document properties
            $objPHPExcel->getProperties()->setCreator("NOVEL GOLF SHOP")
                                        ->setLastModifiedBy("NOVEL GOLF SHOP")
                                        ->setTitle("Office 2007 XLSX Test Document")
                                        ->setSubject("Office 2007 XLSX Test Document")
                                        ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                                        ->setKeywords("office 2007 openxml php")
                                        ->setCategory("Test result file");


            // Add some data
            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A1', 'look_up')
                        ->setCellValue('B1', 'item_desc')
                        ->setCellValue('C1', 'buy_price')
                        ->setCellValue('D1', 'sell_price')
                        ->setCellValue('E1', 'floor_price')
                        ->setCellValue('F1', 'ceil_price')
                        ->setCellValue('G1', 'tax')
                        ->setCellValue('H1', 'code_no')
                        ->setCellValue('I1', 'catg')
                        ->setCellValue('J1', 'sub_catg')
                        ->setCellValue('K1', 'reorder_level');

            $items = Items::select('look_up','item_desc','buy_price','sell_price','floor_price','ceil_price','tax','code_no','catg','sub_catg','reorder_level')->get();
            $i = 2;
            foreach($items as $val){
                $look_up = $val->look_up;
                $item_desc = $val->item_desc;
                $buy_price = $val->buy_price;
                $sell_price = $val->sell_price;
                $floor_price = $val->floor_price;
                $ceil_price = $val->ceil_price;
                $tax = $val->tax;
                $code_no = $val->code_no;
                $catg = $val->catg;
                $sub_catg = $val->sub_catg;
                $reorder_level = $val->reorder_level;

                // Miscellaneous glyphs, UTF-8
                $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $look_up)
                ->setCellValue('B'.$i, $item_desc)
                ->setCellValue('C'.$i, $buy_price)
                ->setCellValue('D'.$i, $sell_price)
                ->setCellValue('E'.$i, $floor_price)
                ->setCellValue('F'.$i, $ceil_price)
                ->setCellValue('G'.$i, $tax)
                ->setCellValue('H'.$i, $code_no)
                ->setCellValue('I'.$i, $catg)
                ->setCellValue('J'.$i, $sub_catg)
                ->setCellValue('K'.$i, $reorder_level);

                $i++;
            }
            

            // Rename worksheet
            $objPHPExcel->getActiveSheet()->setTitle('Simple');


            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);


            // Redirect output to a client’s web browser (Excel5)
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="01simple.xls"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');

            // If you're serving to IE over SSL, then the following may be needed
            header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header ('Pragma: public'); // HTTP/1.0

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;

    }


    public function download_goods_xls(Request $request){
            // Create new PHPExcel object
            $objPHPExcel = new PHPExcel();

            // Set document properties
            $objPHPExcel->getProperties()->setCreator("NOVEL GOLF SHOP")
                                        ->setLastModifiedBy("NOVEL GOLF SHOP")
                                        ->setTitle("Office 2007 XLSX Test Document")
                                        ->setSubject("Office 2007 XLSX Test Document")
                                        ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                                        ->setKeywords("office 2007 openxml php")
                                        ->setCategory("Test result file");


            // Add some data
            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A1', 'look_up')
                        ->setCellValue('B1', 'code_no')
                        ->setCellValue('C1', 'item_desc')
                        ->setCellValue('D1', 'buy_price')
                        ->setCellValue('E1', 'sell_price')
                        ->setCellValue('F1', 'floor_price')
                        ->setCellValue('G1', 'ceil_price')
                        ->setCellValue('H1', 'qty')
                        ->setCellValue('I1', 'comments')
                        ->setCellValue('J1', 'receipt_no');

            $items = Items::select('look_up','item_desc','buy_price','sell_price','floor_price','ceil_price','tax','code_no','catg','sub_catg','reorder_level')->get();
            $i = 2;
            foreach($items as $val){
                $look_up = $val->look_up;
                $item_desc = $val->item_desc;
                $buy_price = $val->buy_price;
                $sell_price = $val->sell_price;
                $floor_price = $val->floor_price;
                $ceil_price = $val->ceil_price;
                $tax = $val->tax;
                $code_no = $val->code_no;
                $catg = $val->catg;
                $sub_catg = $val->sub_catg;
                $reorder_level = $val->reorder_level;

                // Miscellaneous glyphs, UTF-8
                $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $look_up)
                ->setCellValue('B'.$i, $code_no)
                ->setCellValue('C'.$i, $item_desc)
                ->setCellValue('D'.$i, $buy_price)
                ->setCellValue('E'.$i, $sell_price)
                ->setCellValue('F'.$i, $floor_price)
                ->setCellValue('G'.$i, $ceil_price)
                ->setCellValue('H'.$i, '')
                ->setCellValue('I'.$i, '')
                ->setCellValue('J'.$i, '');

                $i++;
            }
            

            // Rename worksheet
            $objPHPExcel->getActiveSheet()->setTitle('Simple');


            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);


            // Redirect output to a client’s web browser (Excel5)
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="goods_template.xls"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');

            // If you're serving to IE over SSL, then the following may be needed
            header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header ('Pragma: public'); // HTTP/1.0

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
    }

    public function download_items_xls(Request $request){
               
        // Create new PHPExcel object
            $objPHPExcel = new PHPExcel();

            // Set document properties
            $objPHPExcel->getProperties()->setCreator("NOVEL GOLF SHOP")
                                        ->setLastModifiedBy("NOVEL GOLF SHOP")
                                        ->setTitle("Office 2007 XLSX Test Document")
                                        ->setSubject("Office 2007 XLSX Test Document")
                                        ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                                        ->setKeywords("office 2007 openxml php")
                                        ->setCategory("Test result file");


            // Add some data
            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A1', 'look_up')
                        ->setCellValue('B1', 'item_desc')
                        ->setCellValue('C1', 'buy_price')
                        ->setCellValue('D1', 'sell_price')
                        ->setCellValue('E1', 'floor_price')
                        ->setCellValue('F1', 'ceil_price')
                        ->setCellValue('G1', 'tax')
                        ->setCellValue('H1', 'code_no')
                        ->setCellValue('I1', 'catg')
                        ->setCellValue('J1', 'sub_catg')
                        ->setCellValue('K1', 'reorder_level');

            $items = Items::select('look_up','item_desc','buy_price','sell_price','floor_price','ceil_price','tax','code_no','catg','sub_catg','reorder_level')->get();
            $i = 2;
            foreach($items as $val){
                $look_up = $val->look_up;
                $item_desc = $val->item_desc;
                $buy_price = $val->buy_price;
                $sell_price = $val->sell_price;
                $floor_price = $val->floor_price;
                $ceil_price = $val->ceil_price;
                $tax = $val->tax;
                $code_no = $val->code_no;
                $catg = $val->catg;
                $sub_catg = $val->sub_catg;
                $reorder_level = $val->reorder_level;

                // Miscellaneous glyphs, UTF-8
                $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $look_up)
                ->setCellValue('B'.$i, $item_desc)
                ->setCellValue('C'.$i, $buy_price)
                ->setCellValue('D'.$i, $sell_price)
                ->setCellValue('E'.$i, $floor_price)
                ->setCellValue('F'.$i, $ceil_price)
                ->setCellValue('G'.$i, $tax)
                ->setCellValue('H'.$i, $code_no)
                ->setCellValue('I'.$i, $catg)
                ->setCellValue('J'.$i, $sub_catg)
                ->setCellValue('K'.$i, $reorder_level);

                $i++;
            }
            

            // Rename worksheet
            $objPHPExcel->getActiveSheet()->setTitle('Simple');


            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);


            // Redirect output to a client’s web browser (Excel5)
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="items_template.xls"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');

            // If you're serving to IE over SSL, then the following may be needed
            header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header ('Pragma: public'); // HTTP/1.0

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;

    }


    public function upload_items_xls(Request $request){
        
         $file_name = $request->items_file->getClientOriginalName();
         $file_extension = File::extension($request->items_file->getClientOriginalName());
         $file_tmp_path = $request->items_file->getRealPath();

         $upload_file = 'uploads/' . time() . '_' . $file_name;

         if (move_uploaded_file($file_tmp_path, $upload_file)) {

            $objReader = IOFactory::createReaderForFile($upload_file);
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($upload_file);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $x = 0;
            $status_ttl = "";
            foreach ($objWorksheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $data = array();

                $look_up_ttl = "";
                $item_desc_ttl = "";
                $buy_price_ttl = "";
                $sell_price_ttl = "";
                $floor_price_ttl = "";
                $ceil_price_ttl = "";
                $tax_ttl = "";
                $code_no_ttl = "";
                $catg_ttl = "";
                $sub_catg_ttl = "";
                $reorder_level_ttl = "";

                foreach ($cellIterator as $cell) {
                    $data[] = $cell->getValue();
                }

                if($x < 1){

                    $look_up_ttl = $data[0];
                    $item_desc_ttl = $data[1];
                    $buy_price_ttl = $data[2];
                    $sell_price_ttl = $data[3];
                    $floor_price_ttl = $data[4];
                    $ceil_price_ttl = $data[5];
                    $tax_ttl = $data[6];
                    $code_no_ttl = $data[7];
                    $catg_ttl = $data[8];
                    $sub_catg_ttl = $data[9];
                    $reorder_level_ttl = $data[10];
                    
                }else{
                   
                        $look_up = $data[0];
                        $item_desc = $data[1];
                        $buy_price = $data[2];
                        $sell_price = $data[3];
                        $floor_price = $data[4];
                        $ceil_price = $data[5];
                        $tax = $data[6];
                        $code_no = $data[7];
                        $catg = $data[8];
                        $sub_catg = $data[9];
                        $reorder_level = $data[10];


                        if($item_desc !=""){

                                $items = new Items();
                                $items->look_up = $look_up;
                                $items->item_desc = strtolower(html_entity_decode($item_desc, ENT_QUOTES, "UTF-8"));
                                $items->buy_price = round(str_replace(",","",$buy_price));
                                $items->sell_price = str_replace(",","",$sell_price);
                                $items->floor_price = str_replace(",","",$floor_price);
                                $items->ceil_price = str_replace(",","",$ceil_price);
                                $items->tax = isset($tax) ? Tax::where('tax_desc', $tax)->first()['id'] : '';
                                $items->code_no = $code_no;
                                $items->catg = isset($catg) ? Catg::where('catg_name', $catg)->first()['id'] : '';
                                $items->sub_catg = isset($sub_catg) ? SubCatg::where('sub', $sub_catg)->first()['id'] : '';
                                $items->reorder_level = $reorder_level;
                                $items->save();
                        
                        }
                     

                    }
                    $x++;
                }
               
            }
            echo json_encode(array('status'=>1));
        
    }


    public function upload_goods_xls(Request $request){
        
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
           $x = 0;
           $status_ttl = "";
           foreach ($objWorksheet->getRowIterator() as $row) {
               $cellIterator = $row->getCellIterator();
               $cellIterator->setIterateOnlyExistingCells(false);
               $data = array();

               $look_up_ttl = "";
               $item_desc_ttl = "";
               $buy_price_ttl = "";
               $sell_price_ttl = "";
               $floor_price_ttl = "";
               $ceil_price_ttl = "";
               $tax_ttl = "";
               $code_no_ttl = "";
               $catg_ttl = "";
               $sub_catg_ttl = "";
               $reorder_level_ttl = "";

               foreach ($cellIterator as $cell) {
                   $data[] = $cell->getValue();
               }

               

               if($x < 1){

                

                   $look_up_ttl = $data[0];
                   $code_no_ttl = $data[1];
                   $item_desc_ttl = $data[2];
                   $buy_price_ttl = $data[3];
                   $sell_price_ttl = $data[4];
                   $floor_price_ttl = $data[5];
                   $ceil_price_ttl = $data[6];
                   $qty_ttl = $data[7];
                   $comment_ttl = $data[8];
                   $receipt_no_ttl = $data[9];
                
                   
               }else{
                  
                       $look_up = $data[0];
                       $code_no = $data[1];
                       $item_desc = $data[2];
                       $buy_price = round(str_replace(",","",$data[3]));
                       $sell_price = str_replace(",","",$data[4]);
                       $floor_price = str_replace(",","",$data[5]);
                       $ceil_price = str_replace(",","",$data[6]);
                       $qty = str_replace(",","",$data[7]);
                       $comment = $data[8];
                       $receipt_no = $data[9];
                       

                       if($item_desc !="" && $qty !=0){

                               $items_chk = Items::select('id')->where('code_no',$code_no)->first();

                               if($items_chk){

                                $br = Branches::where('curr',1)->select('id')->orderBy('id','DESC')->limit(1)->first();

                                    $prv_g = Goods::select('qty','cost','price','ceil_price','floor_price')->where('item',$items_chk->id)->where('status',1)->orderBy('id','DESC')->limit(1)->first();
                                    if($prv_g){

                                        if($buy_price != $prv_g->cost){

                                            $sum_gds = Goods::where('status',1)->where('item',$items_chk->id)->sum('qty');
                                            $sum_sc = ShoppingCart::where('status',2)->where('type','tender')->where('item',$items_chk->id)->sum('qty');
                                            $sum_rtrn = ShoppingCart::where('status',4)->where('type','return')->where('item',$items_chk->id)->sum('qty');
                                            $stock = $sum_gds - ($sum_sc - $sum_rtrn);
                        
                                            $weighted_avg = round((($stock * $prv_g->cost) + ($request->qty * $request->cost)) / ($prv_g->qty + $request->qty));
                                        
                                            $up_item = Items::find($items_chk->id);
                                            $up_item->buy_price = $weighted_avg;
                                            $up_item->save();
                        
                                        }

                                        if($sell_price != $prv_g->price){
                                            $up_item = Items::find($items_chk->id);
                                            $up_item->sell_price = $sell_price;
                                            $up_item->save();
                                        }
                        
                                        if($ceil_price != $prv_g->ceil_price){
                                            $up_item = Items::find($items_chk->id);
                                            $up_item->ceil_price = $ceil_price;
                                            $up_item->save();
                                        }
                        
                                        if($floor_price != $prv_g->floor_price){
                                            $up_item = Items::find($items_chk->id);
                                            $up_item->floor_price = $floor_price;
                                            $up_item->save();
                                        }

                                    }

                                    $gds = new Goods();
                                    $gds->date_received = time();
                                    $gds->item = $items_chk->id;
                                    $gds->cost = $buy_price;
                                    $gds->price = $sell_price;
                                    $gds->floor_price = $floor_price;
                                    $gds->ceil_price = $ceil_price;
                                    $gds->qty = $qty;
                                    $gds->status = 1;
                                    $gds->received_by = $request->session()->get('uid');        
                                    $gds->branch = $br->id;
                                    $gds->comments = $comment;
                                    $gds->receipt_no = $receipt_no;
                                    
                                    $gds->save();

                                    $status_held = 1;

                               }else{
                                    $status_held = 2;
                               }
                               
                       
                       }else{
                            $status_held = 0;
                       }
                    

                   }
                   $x++;
               }
              
           }

           echo json_encode(array('status'=>$status_held));
       
   }


    

}

?>