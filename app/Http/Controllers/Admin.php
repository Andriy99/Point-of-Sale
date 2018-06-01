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

class Admin extends Controller
{

    public function dash_totals(){

  
        $from = strtotime(date("d-m-Y",time()) . " 06:00:00");
        $to = strtotime(date("d-m-Y",time()) . " 23:59:59");

        $br = Branches::where('curr',1)->select('id')->orderBy('id','DESC')->limit(1)->first();
        
        $cash_tr_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cash tender')->where('branch',$br->id)->sum('total');
        $mpesa_tr_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('type','mpesa tender')->where('branch',$br->id)->sum('total');
        $card_tr_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('type','card tender')->where('branch',$br->id)->sum('total');
        $acc_tr_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('type','account tender')->where('branch',$br->id)->sum('total');
        $cheque_tr_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cheque tender')->where('branch',$br->id)->sum('total');
        $all_tr_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$br->id)->where('type', '!=','return')->sum('total');
        $rtrn_tr_ttl = $cash_tr_ttl + $mpesa_tr_ttl + $card_tr_ttl + $acc_tr_ttl + $cheque_tr_ttl;

        $cash_tr_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cash tender')->where('branch',$br->id)->sum('no_items');
        $mpesa_tr_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','mpesa tender')->where('branch',$br->id)->sum('no_items');
        $card_tr_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','card tender')->where('branch',$br->id)->sum('no_items');
        $acc_tr_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','account tender')->where('branch',$br->id)->sum('no_items');
        $cheque_tr_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cheque tender')->where('branch',$br->id)->sum('no_items');
        $all_tr_qty = $cash_tr_qty +  $mpesa_tr_qty + $card_tr_qty + $acc_tr_qty + $cheque_tr_qty;
        $rtrn_tr_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$br->id)->where('type','return')->sum('no_items');


        return json_encode(array(
            'cash'=>number_format($cash_tr_ttl),
            'mpesa'=>number_format($mpesa_tr_ttl),
            'card'=>number_format($card_tr_ttl),
            'acc'=>number_format($acc_tr_ttl),
            'cheque'=>number_format($cheque_tr_ttl),
            'total'=>number_format($all_tr_ttl),
            'cash_qty'=>number_format($cash_tr_qty),
            'mpesa_qty'=>number_format($mpesa_tr_qty),
            'card_qty'=>number_format($card_tr_qty),
            'acc_qty'=>number_format($acc_tr_qty),
            'cheque_qty'=>number_format($cheque_tr_qty),
            'total_qty'=>number_format($all_tr_qty)
        ));
        
    }

    public function todays_trans_reports(Request $request){

        $filter = $request->filter;

        $from = strtotime(date("d-m-Y",time()) . " 06:00:00");
        $to = strtotime(date("d-m-Y",time()) . " 23:59:59");
        
        if($filter=="all"){
            $res = Transactions::select('id','up_id','customer','trans_time','user','discount','branch','total','receipt_no','type','status','no_items','ref_no','type','invoice')->orderBy('id','DESC')->whereBetween('trans_time',array($from,$to))->where('type','!=','collection')->get();

        }else{
            $res = Transactions::select('id','up_id','customer','trans_time','user','discount','branch','total','receipt_no','type','status','no_items','ref_no','type','invoice')->where('type','account tender')->orderBy('id','DESC')->whereBetween('trans_time',array($from,$to))->get();

        }
        
        if($res->count() > 0){
            $no = 1;
            foreach($res as $i=>$val){
                $d[$i]['no'] = $no;
                $d[$i]['id'] = $val->id;
                $d[$i]['trans_time'] = date("d/m/y h:i",$val->trans_time);
                $d[$i]['total'] = number_format($val->total - $val->discount) . ".00";
                $d[$i]['receipt_no'] = $val->receipt_no;
                $d[$i]['branch'] = Branches::find($val->branch)->branch;
                $d[$i]['customer'] = ($val->customer) ? (Customers::find($val->customer)->lname) ? Customers::find($val->customer)->lname : Customers::find($val->customer)->org : "";
                $d[$i]['ref_no'] = $val->ref_no;
                $d[$i]['invoice'] = $val->invoice;
                $d[$i]['no_items'] = $val->no_items;
                $d[$i]['up_id'] = $val->up_id;
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

    public function todays_trans_totals(Request $request){

        $from = strtotime(date("d-m-Y",time()) . " 06:00:00");
        $to = strtotime(date("d-m-Y",time()) . " 23:59:59");

        $filter = $request->filter;

        if($filter=="all"){
            $total = Transactions::whereBetween('trans_time',array($from,$to))->sum('total');
            $tax = Transactions::whereBetween('trans_time',array($from,$to))->sum('total_tax');
            $qty = Transactions::whereBetween('trans_time',array($from,$to))->sum('no_items');
            $total_discount = Transactions::whereBetween('trans_time',array($from,$to))->sum('discount');
            $total_cost = Transactions::whereBetween('trans_time',array($from,$to))->sum('total_cost');
        
        }else{
            $total = Transactions::whereBetween('trans_time',array($from,$to))->where('type','account tender')->sum('total');
            $tax = Transactions::whereBetween('trans_time',array($from,$to))->where('type','account tender')->sum('total_tax');
            $qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','account tender')->sum('no_items');
            $total_discount = Transactions::whereBetween('trans_time',array($from,$to))->where('type','account tender')->sum('discount');
            $total_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('type','account tender')->sum('total_cost');
        
        }

        
        $gross = ($total - $total_discount) - $total_cost;
        $net = ($total - $total_discount) - $total_cost - $tax;

        return json_encode(array('total_cost'=>number_format($total_cost)  ,'net'=>number_format($net) ,'gross'=>number_format($gross) ,'total'=>number_format($total - $total_discount),'tax'=>number_format($tax),'total_discount'=>number_format($total_discount) ,'qty'=>$qty));
    
    }

    public function open_drawer(Request $request){

        $val = $request->op_amt;

        $chk_drawer = Drawer::select('id')->where('status','open')->get();
       
        if($chk_drawer->count() ==0){
            
            $br = Branches::where('curr',1)->select('id')->orderBy('id','DESC')->limit(1)->first();
            
            if($request->session()->has('uid')){

                $drawer = new Drawer();
                $drawer->opening_amt = $val;
                $drawer->start_time = time();
                $drawer->branch = $br->id;
                $drawer->user = $request->session()->get('uid');
                $drawer->status = 'open';
                $drawer->save();

                $logs = new Logs();
                $logs->log_time = time();
                $logs->desc = 'Opened New Drawer, Opening Amount: ' . $val;
                $logs->uid = $request->session()->get('uid');
                $logs->save();

                return json_encode(array('status'=>1));

            }

        }else{
            return json_encode(array('status'=>0));
        }
        

    }

    public function drawer_report(){

        $first_day_morn = strtotime(date('01-m-Y') . " 06:00:00");
        $first_day_eve = strtotime(date('01-m-Y') . " 23:59:59");
        $last_day_eve = strtotime(date('t-m-Y') . " 23:59:59");

        $res = Drawer::select('id','opening_amt','closing_amt','start_time','stop_time','status','user')->limit(31)->orderBy('id','DESC')->get();
        foreach($res as $i=>$val){
            $d[$i]['id'] = $val->id;
            $d[$i]['opening_amt'] = $val->opening_amt;
            $d[$i]['closing_amt'] = $val->closing_amt; 
            $d[$i]['start_time'] = ($val->start_time) ? date("d-m-Y H:i:s",$val->start_time) : '';
            $d[$i]['stop_time'] = ($val->stop_time) ? date("d-m-Y H:i:s",$val->stop_time) : '';
            $d[$i]['status'] = $val->status;
            $d[$i]['user'] = substr(User::find($val->user)->fname, 0, 1) . ". " . User::find($val->user)->lname;
        }

        if($res->count() > 0){
            return json_encode($d);
        }else{
            return json_encode(array('status'=>0));
        }
    }

    public function search_drawer_report(Request $request){
        
        $exp_dates = explode("to",$request->dates);
        $from = strtotime(str_replace(' ', '', $exp_dates[0]). " 06:00:00");
        $to = strtotime(str_replace(' ', '', $exp_dates[1]). " 23:59:59");
        
        $res = Drawer::select('id','opening_amt','closing_amt','start_time','stop_time','status','user')->whereBetween('start_time',array($from,$to))->orderBy('id','DESC')->get();
        
        if($res->count() > 0){
            foreach($res as $i=>$val){
                $d[$i]['id'] = $val->id;
                $d[$i]['opening_amt'] = $val->opening_amt;
                $d[$i]['closing_amt'] = $val->closing_amt; 
                $d[$i]['start_time'] = ($val->start_time) ? date("d-m-Y H:i:s",$val->start_time) : '';
                $d[$i]['stop_time'] = ($val->stop_time) ? date("d-m-Y H:i:s",$val->stop_time) : '';
                $d[$i]['status'] = $val->status;
                $d[$i]['user'] = substr(User::find($val->user)->fname, 0, 1) . ". " . User::find($val->user)->lname;
            }
        }else{
            $d = [];
        }

        return json_encode($d);

    }

    public function drawings_report(){

        $res = Drawings::select('id','amount','branch','uid','comment','dr_time','sales_amt','remainder_amt')->orderBy('id','DESC')->get();
        
        if($res->count() > 0){

            foreach($res as $i=>$val){
                $d[$i]['id'] = $val->id;
                $d[$i]['dr_time'] = date("d-m-y H:i:s",$val->dr_time);
                $d[$i]['user'] = User::find($val->uid)->lname;
                $d[$i]['comment'] = $val->comment;
                $d[$i]['branch'] = Branches::find($val->branch)->branch;
                $d[$i]['amount'] = number_format($val->amount);
                $d[$i]['sales_amt'] = number_format($val->sales_amt);
                $d[$i]['remainder_amt'] = number_format($val->remainder_amt);
                
            }
            
        }else{
            $d= [];
        }

        return json_encode($d);
    }

    public function get_tax_options(){
        $tax  = Tax::select('id','tax_desc','perc')->where('status',1)->get();
        return(json_encode($tax));
    }

    public function new_tax(Request $request){

        $chk_tax = Tax::select('id')->where('tax_desc',$request->tax_title)->where('status',1)->get();

        if($chk_tax->count() ==0){

            $tax = new Tax();
            $tax->tax_desc = $request->tax_title;
            $tax->perc = $request->tax_perc / 100;
            $tax->status = 1;
            $tax->save();

            $logs = new Logs();
            $logs->log_time = time();
            $logs->desc = 'Created a new Tax: ' . $request->tax_title . ', Percentage: ' . $request->tax_perc . '%';
            $logs->uid = $request->session()->get('uid');
            $logs->save();

            return json_encode(array('status'=>1));
        }else{
            return json_encode(array('status'=>0));
        }
       
        

    }

    public function update_user_priviledges(Request $request){
          
            $users = User::find($request->uid);
            
            $users->tender = $request->tender;
            $users->del_item = $request->del_item;
            $users->draw = $request->draw;
            $users->admin_link = $request->ad_lnk;
            $users->return_item = $request->rtrn_it;
            $users->mng_taxes = $request->mng_tax;
            $users->mng_goods = $request->mng_gds;
            $users->drawer = $request->mng_drawer;
            $users->mng_branches = $request->mng_brnch;
            $users->mng_users = $request->mng_user;
            $users->mng_item = $request->mng_item;

            $users->mng_customers = $request->mng_customers;
            $users->mng_clubs = $request->mng_clubs;
            $users->mng_events = $request->mng_events;
            $users->mng_accounts = $request->mng_accounts;
            $users->ball_pool = $request->mng_pool;
            $users->tender_accounts = $request->tender_accounts;
            $users->offer_discount = $request->offer_discount;
            $users->credit_sale = $request->credit_sale;
            $users->remote_branch_access = $request->remote_branch;
            
            $users->save();

            $logs = new Logs();
            $logs->log_time = time();
            $logs->desc = 'Updated User Access: ' . $users->fname . ' ' . $users->lname;
            $logs->uid = $request->session()->get('uid');
            $logs->save();

            if($request->uid == $request->session()->get('uid')){
                return json_encode(array('status'=>2));
            }else{
                return json_encode(array('status'=>1));
            }

            return json_encode(array('status'=>1));


    }

    public function new_user(Request $request){

    
        $chk_users = User::select('id')->where('phone',$request->phone)->where('status',1);
    
        if($chk_users->count() ==0){

            $users = new User();
            $users->fname = strtolower($request->fname);
            $users->lname = strtolower($request->lname);
            $users->email = $request->email;
            $users->phone = $request->phone;
            $users->password = Hash::make($request->passd);
            $users->status = 1;

            $users->tender = $request->tender;
            $users->del_item = $request->del_item;
            $users->draw = $request->draw;
            $users->admin_link = $request->ad_lnk;
            $users->return_item = $request->rtrn_it;
            $users->mng_taxes = $request->mng_tax;
            $users->mng_goods = $request->mng_gds;
            $users->drawer = $request->mng_drawer;
            $users->mng_branches = $request->mng_brnch;
            $users->mng_users = $request->mng_user;
            $users->mng_item = $request->mng_item;

            $users->mng_customers = $request->mng_cust;
            $users->mng_clubs = $request->mng_clubs;
            $users->mng_events = $request->mng_events;
            $users->mng_accounts = $request->mng_accs;
            $users->ball_pool = $request->ball_pool;
            $users->cust_access = $request->cust_access;
            $users->offer_discount = $request->offer_discount;
            $users->credit_sale = $request->credit_sale;
            $users->remote_branch_access = $request->remote_branch;
            
            $users->save();

            $logs = new Logs();
            $logs->log_time = time();
            $logs->desc = 'Created a new User: ' . $request->fname . ' ' . $request->lname . ', email: ' . $request->email . '. Phone: ' . $request->phone;
            $logs->uid = $request->session()->get('uid');
            $logs->save();

            return json_encode(array('status'=>1));

        }else{
            return json_encode(array('status'=>0));
        }

    }

    public function new_item(Request $request){
       
        $item_chk = Items::select('id')->where('item_desc',$request->title);

        if($item_chk->count() ==0){

            $item = new Items();
            $item->code_no = $request->code;
            $item->look_up = $request->m_code;
            $item->item_desc = strtolower($request->title);
            $item->reorder_level = $request->re_order_level;
            $item->tax = $request->tax;
            $item->ceil_price = $request->ceil_price;
            $item->floor_price = $request->floor_price;
            $item->buy_price = $request->buy_price;
            $item->sell_price = $request->price;
            $item->catg = $request->catg;
            $item->sub_catg = $request->sub_catg;
            $item->status = 1;
            $item->reg_time = time();
            $item->save();

            $logs = new Logs();
            $logs->log_time = time();
            $logs->desc = 'Created a new Item: ' . $request->title . ', Price: ' . $request->price . '. Qty: ' . $request->qty . ', Tax: ' . $request->tax . ' Re-order level: ' . $item->reorder_level;
            $logs->uid = $request->session()->get('uid');
            $logs->save();

            return json_encode(array('status'=>1));
        }else{
            return json_encode(array('status'=>0));
        }

    }

    public function admin_items_report(Request $request){

        $res = Items::select('id','code_no','up_id','look_up','item_desc','sell_price','qty','tax','reg_time','status')->orderBy('id','DESC')->get();
        if($res->count() > 0){

            foreach($res as $i=>$val){
                $d[$i]['id'] = $val->id;
                $d[$i]['look_up'] = $val->look_up;
                $d[$i]['code_no'] = $val->code_no;
                $d[$i]['item_desc'] = $val->item_desc;
                $d[$i]['up_id'] = $val->up_id;
                $d[$i]['sell_price'] = number_format($val->sell_price);
                $sum_goods_qty = Goods::where('item',$val->id)->where('status',1)->sum('qty');
                $sum_sc_qty = ShoppingCart::where('item',$val->id)->where('type','tender')->where('status','2')->sum('qty');
                $sc_ret = ShoppingCart::where('item',$val->id)->where('status',4)->where('type','return')->sum('qty');
                $d[$i]['qty'] = number_format(ceil($sum_goods_qty - ($sum_sc_qty + $sc_ret)));
                $d[$i]['tax'] = ($val->tax) ? Tax::find($val->tax)->tax_desc : '';
                $d[$i]['reg_time'] = ($val->reg_time) ? date("d-m-Y H:i:s",$val->reg_time) : '';
                if($val->status == 1){
                    $d[$i]['status'] = "Active";
                }else{
                    $d[$i]['status'] = "InActive";
                }
                
                
            }

        
            
        }else{
            $d = [];
        }

        return json_encode($d);

    }

    public function tax_reports(){

        $tax = Tax::select('id','tax_desc','status','perc')->get();

        if($tax->count() > 0){

            foreach($tax as $i=>$val){
                $d[$i]['id'] = $val->id;
                $d[$i]['tax_desc'] = $val->tax_desc;
                $d[$i]['perc'] = $val->perc;
                if($val->status ==1){
                    $d[$i]['status'] = "Active";
                }else{
                    $d[$i]['status'] = "In-Active";
                }
            }

            return json_encode($d);
        }else{
            return json_encode(array('status'=>0));
        }
    }

    public function users_reports(){
        $users = User::select('id','fname','lname','phone','email','status')->get();

        foreach($users as $i=>$val){

            $d[$i]['id'] = $val->id;
            $d[$i]['fname'] = $val->fname;
            $d[$i]['lname'] = $val->lname;
            $d[$i]['phone'] = $val->phone;
            $d[$i]['email'] = $val->email;

            if($val->status ==1){
                $d[$i]['status'] = "Active";
            }else{
                $d[$i]['status'] = "InActive";
            }
            
        }

        if($users->count() > 0){
            return json_encode($d);
        }else{
            return json_encode(array('status'=>0));
        }
    }

    
    public function hourly_graph(){

        $graph_trans = Transactions::select('total','trans_time','id')->where('type','cash tender')->orWhere('type','mpesa tender')->orWhere('type','card tender')->orWhere('type','cheque tender')->where('status',1)->get();
        $date_time_today = date("d-m-Y",time());

        $seven = "07";$eight = "08";$nine = "09";$ten = "10";$eleven = "11";$noon = "12";$one = "13";$two = "14";
        $three = "15";$four = "16";$five = "17";$six = "18";$seven_pm = "19";$eight_pm = "20";$nine_pm = "21";$ten_pm = "22";

        $seven_val = 0;$eight_val = 0;$nine_val = 0;$ten_val = 0;$eleven_val = 0;$noon_val = 0;$one_val = 0;$two_val = 0;
        $three_val = 0;$four_val = 0;$five_val = 0;$six_val = 0;$seven_pm_val = 0;$eight_pm_val = 0;$nine_pm_val = 0;$ten_pm_val = 0;
        
        
        foreach($graph_trans as $val_graph_trans){
            //Hourly 
            if($date_time_today == date("d-m-Y",$val_graph_trans->trans_time)){

                $trans_hour = date("H",$val_graph_trans->trans_time);

                if($seven == $trans_hour){
                    $seven_val += $val_graph_trans->total;
                    
                }elseif($eight == $trans_hour){
                    $eight_val += $val_graph_trans->total;
                    
                }elseif($nine == $trans_hour){
                    $nine_val += $val_graph_trans->total;
                  
                }elseif($ten == $trans_hour){
                    $ten_val += $val_graph_trans->total;
                    
                }elseif($eleven == $trans_hour){
                    $eleven_val += $val_graph_trans->total;
                }elseif($noon == $trans_hour){
                    $noon_val += $val_graph_trans->total;
                }elseif($one == $trans_hour){
                    $one_val += $val_graph_trans->total;
                }elseif($two == $trans_hour){
                    $two_val += $val_graph_trans->total;
                }elseif($three == $trans_hour){
                    $three_val += $val_graph_trans->total;
                }elseif($four == $trans_hour){
                    $four_val += $val_graph_trans->total;
                }elseif($five == $trans_hour){
                    $five_val += $val_graph_trans->total;
                }elseif($six == $trans_hour){
                    $six_val += $val_graph_trans->total;
                }elseif($seven_pm == $trans_hour){
                    $seven_pm_val += $val_graph_trans->total;
                }elseif($eight_pm == $trans_hour){
                    $eight_pm_val += $val_graph_trans->total;
                }elseif($nine_pm == $trans_hour){
                    $nine_pm_val += $val_graph_trans->total;
                }elseif($ten_pm == $trans_hour){
                    $ten_pm_val += $val_graph_trans->total;
                }

            }

            
            
            
        }

        $hourly_figures = '["' . $seven_val . '","' . $eight_val . '","' . $nine_val . '","' . $ten_val . '","' . $eleven_val . '","' . $noon_val . '","' . $one_val . '","' . $two_val . '","' . $three_val . '","' . $four_val . '","' . $five_val . '","' . $six_val . '","' . $seven_pm_val . '","' . $eight_pm_val . '","' . $nine_pm_val . '","' . $ten_pm_val . '"]';

        return $hourly_figures;



    }


    public function daily_graph(){


        $graph_trans = Transactions::select('total','trans_time','id')->where('type','cash tender')->orWhere('type','mpesa tender')->orWhere('type','card tender')->orWhere('type','cheque tender')->where('status',1)->get();
        $date_time_today = date("d-m-Y",time());

        $seven = "07";$eight = "08";$nine = "09";$ten = "10";$eleven = "11";$noon = "12";$one = "13";$two = "14";
        $three = "15";$four = "16";$five = "17";$six = "18";$seven_pm = "19";$eight_pm = "20";$nine_pm = "21";$ten_pm = "22";

        $seven_val = 0;$eight_val = 0;$nine_val = 0;$ten_val = 0;$eleven_val = 0;$noon_val = 0;$one_val = 0;$two_val = 0;
        $three_val = 0;$four_val = 0;$five_val = 0;$six_val = 0;$seven_pm_val = 0;$eight_pm_val = 0;$nine_pm_val = 0;$ten_pm_val = 0;
        
        $mon= 0;$tue=0;$wed=0;$thr=0;$fri=0;$sat=0;$sun=0;

        foreach($graph_trans as $val_graph_trans){

            //Weekly
            $d['monday'] = date( 'd-m-Y', strtotime( 'monday this week' ) );
            $d['tuesday'] = date( 'd-m-Y', strtotime( 'tuesday this week' ) );
            $d['wedesday'] = date( 'd-m-Y', strtotime( 'wednesday this week' ) );
            $d['thursday'] = date( 'd-m-Y', strtotime( 'thursday this week' ) );
            $d['friday'] = date( 'd-m-Y', strtotime( 'friday this week' ) );
            $d['saturday'] = date( 'd-m-Y', strtotime( 'saturday this week' ) );
            $d['sunday'] = date( 'd-m-Y', strtotime( 'sunday this week' ) );
        
            
       

            if($d['monday'] == date("d-m-Y",$val_graph_trans->trans_time)){
                $mon += $val_graph_trans->total;
            }elseif($d['tuesday'] == date("d-m-Y",$val_graph_trans->trans_time)){
                $tue += $val_graph_trans->total;
            }elseif($d['wedesday'] == date("d-m-Y",$val_graph_trans->trans_time)){
                $wed += $val_graph_trans->total;
            }elseif($d['thursday'] == date("d-m-Y",$val_graph_trans->trans_time)){
                $thr += $val_graph_trans->total;
            }elseif($d['friday'] == date("d-m-Y",$val_graph_trans->trans_time)){
                $fri += $val_graph_trans->total;
            }elseif($d['saturday'] == date("d-m-Y",$val_graph_trans->trans_time)){
                $sat += $val_graph_trans->total;
            }elseif($d['sunday'] == date("d-m-Y",$val_graph_trans->trans_time)){
                $sun += $val_graph_trans->total;
            }
            
            
        }

        $weekly_figures = '["' . $mon . '","' . $tue . '","' . $wed . '","' . $thr . '","' . $fri . '","' . $sat . '","' . $sun . '"]';

        return $weekly_figures;
    }


    public function search_transactions(Request $request){

        $exp_dates = explode("to",$request->dates);
        $from = strtotime(str_replace(' ', '', $exp_dates[0]). " 06:00:00");
        $to = strtotime(str_replace(' ', '', $exp_dates[1]). " 23:59:59");
        $filter = $request->filter;
        $rec = $request->rec_no;

        if($filter =="all"){

            $res = Transactions::select('id','trans_time','customer','branch','total','user','ref_no','receipt_no','status','type','no_items','invoice')->whereBetween('trans_time',array($from,$to))->where('type','!=','collection')->get();
         
        }else{

            $res = Transactions::select('id','trans_time','customer','branch','total','user','ref_no','receipt_no','status','type','no_items','invoice')->where('type','account tender')->whereBetween('trans_time',array($from,$to))->get();
           
        }

        if($res->count() > 0){

            $no = 1;
            foreach($res as $i=>$val){
                $d[$i]['no'] = $no;
                $d[$i]['id'] = $val->id;
                $d[$i]['trans_time'] = date("d-m-Y h:i:s",$val->trans_time);
                $d[$i]['total'] = number_format($val->total) . ".00";
                $d[$i]['receipt_no'] = $val->receipt_no;
                $d[$i]['customer'] = ($val->customer) ? (Customers::find($val->customer)->lname) ? Customers::find($val->customer)->lname : Customers::find($val->customer)->org : "";
                $user_dets = User::find($val->user);
                $d[$i]['branch'] = Branches::find($val->branch)->branch;
                $d[$i]['user'] = substr($user_dets->fname, 0, 1) . ". ". $user_dets->lname;
                $d[$i]['ref_no'] = $val->ref_no;
                $d[$i]['invoice'] = $val->invoice;
                $d[$i]['no_items'] = $val->no_items;
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

    public function search_trans_totals(Request $request){
        
        $exp_dates = explode("to",$request->dates);
        $from = strtotime(str_replace(' ', '', $exp_dates[0]). " 06:00:00");
        $to = strtotime(str_replace(' ', '', $exp_dates[1]). " 23:59:59");

        $filter = $request->filter;

        if($filter =='all'){
            $total = Transactions::whereBetween('trans_time',array($from,$to))->sum('total');
            $tax = Transactions::whereBetween('trans_time',array($from,$to))->sum('total_tax');
            $qty = Transactions::whereBetween('trans_time',array($from,$to))->sum('no_items');
            $total_discount = Transactions::whereBetween('trans_time',array($from,$to))->sum('discount');
            $total_cost = Transactions::whereBetween('trans_time',array($from,$to))->sum('total_cost');
        
        }else{
            $total = Transactions::whereBetween('trans_time',array($from,$to))->where('type','account tender')->sum('total');
            $tax = Transactions::whereBetween('trans_time',array($from,$to))->where('type','account tender')->sum('total_tax');
            $qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','account tender')->sum('no_items');
            $total_discount = Transactions::whereBetween('trans_time',array($from,$to))->where('type','account tender')->sum('discount');
            $total_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('type','account tender')->sum('total_cost');
        
        }

        
        $gross = ($total - $total_discount) - $total_cost;
        $net = ($total - $total_discount) - $total_cost - $tax;
            
        return json_encode(array('total_cost'=>number_format($total_cost),'net'=>number_format($net),'gross'=>number_format($gross),'total'=>number_format($total - $total_discount),'total_discount'=>number_format($total_discount),'tax'=>number_format($tax),'qty'=>number_format($qty)));
    
         
        
    }

    public function get_trans_dets(Request $request){
        
        $sc = ShoppingCart::select('id','qty','item','price','total','status','tax','type')->where('tid',$request->id)->get();

       
        foreach($sc as $i=>$val){

            $d[$i]['id'] = $val->id;
            $d[$i]['qty'] = number_format($val->qty);
            $d[$i]['item'] = Items::find($val->item)->item_desc;
            $d[$i]['price'] = number_format($val->price);
            $d[$i]['total'] = number_format($val->total);
            $d[$i]['tax'] = number_format($val->tax);
            $d[$i]['status'] = $val->status;
            $d[$i]['type'] = $val->type;
        }

        

        echo json_encode($d);
        
    }

    public function get_trans_dets_totals(Request $request){

        $trans = Transactions::select('type','ref_no','discount','cash','change','total_tax','total','no_items','receipt_no','comment')->where('id',$request->id)->get();


        foreach($trans as $val_t){

            $t['cash'] = number_format($val_t->cash);
            $t['change'] = number_format($val_t->change);
            $t['total_tax'] = number_format($val_t->total_tax);
            $t['total'] = number_format($val_t->total);
            $t['ttl_qty'] = number_format($val_t->no_items);
            $t['receipt_no'] = $val_t->receipt_no;
            $t['comment'] = $val_t->comment;
            $t['type'] = $val_t->type;
            $t['ref_no'] = $val_t->ref_no;
            $t['discount'] = $val_t->discount;
        }

        echo json_encode($t);
    }

    public function get_single_item(Request $request){

        $items = Items::find($request->id);

        $d = array(
            'buy_price'=>number_format($items->buy_price),
            'item_desc'=>$items->item_desc,
            'sell_price'=>number_format($items->sell_price),
            'floor_price'=>number_format($items->floor_price),
            'ceil_price'=>number_format($items->ceil_price),
            'look_up'=>$items->look_up,
            'code_no'=>$items->code_no,
            'reorder_level'=>$items->reorder_level,
            'catg'=>$items->catg,
            'sub_catg'=>$items->sub_catg,
            'tax'=>$items->tax,
            'id'=>$items->id

        );

        return json_encode($d);
    }

    public function get_single_item_goods(Request $request){

        $goods = Goods::select('id','date_received','item','received_by','receipt_no','qty','cost','price','ceil_price','floor_price')->orderBy('id','DESC')->where('item',$request->id)->get();

        if($goods->count() > 0){
            foreach($goods as $i=>$val){
                $g[$i]['id'] = $val->id;
                $g[$i]['date_received'] = date("d-m-y",$val->date_received);
                $g[$i]['item'] = Items::find($val->item)->item_desc;
                $g[$i]['received_by'] = ($val->received_by) ? User::find($val->received_by)->lname : '';
                $g[$i]['receipt_no'] = $val->receipt_no;
                $g[$i]['qty'] = number_format($val->qty);
                $g[$i]['cost'] = number_format($val->cost);
                $g[$i]['price'] = number_format($val->price);
                $g[$i]['ceil_price'] = number_format($val->ceil_price);
                $g[$i]['floor_price'] = number_format($val->floor_price);
            }
        }else{
            $g = [];
        }
        

        return json_encode($g);
    }

    public function update_item_values(Request $request){
       
        $items = Items::find($request->id);
        $items->look_up = $request->code;
        $items->item_desc = $request->title;
        $items->tax = $request->tax;
        $items->sell_price = $request->price;
        $items->buy_price = $request->cost;
        $items->sub_catg = $request->sub_category;
        $items->catg = $request->category;
        $items->floor_price = $request->floor_price;
        $items->ceil_price = $request->ceil_price;
        $items->code_no = $request->m_code;
        $items->reorder_level = $request->re_order;

        $items->save();

        $logs = new Logs();
        $logs->log_time = time();
        $logs->desc = 'Updated Item :' . $request->title . ' Price: ' . $request->price . ' Qty: ' . $request->qty;
        $logs->uid = $request->session()->get('uid');
        $logs->save();
 
        return json_encode(array('status'=>1));

    }

    public function login(Request $request){

        $user = User::select('id','fname','lname','password')->where('status',1)->where('fname',$request->username)->get();
       
        
        if($user->count() > 0){
            
            foreach($user as $val){

                $id = $val->id;
                $lname = $val->lname;
                $fname = $val->fname;
                $password = $val->password;

                if(Hash::check($request->passd, $password)){
                    //
                    $request->session()->put('uid', $id);
                    $request->session()->put('fname', $fname);
                    $request->session()->put('lname', $lname);

                    $logs = new Logs();
                    $logs->log_time = time();
                    $logs->desc = 'Logged In';
                    $logs->uid = $id;
                    $logs->save();

                    /*
                    $cart = ShoppingCart::select('id')->where('status',1)->get();

                    if($cart->count() > 0){

                        foreach($cart as $val_cart){
                            //echo $cart->count();
                            $up_cart = ShoppingCart::find($val_cart->id);
                            $up_cart->status = 0;
                            $up_cart->save();
                        }
                    }
                    */
                    
                    return json_encode(array('status'=>1,'uid'=>$id,'fname'=>$fname,'lname'=>$lname));
                    
                }else{
                    return json_encode(array('status'=>0));
                }

            }

            
        }else{
            return json_encode(array('status'=>0));
        }
    }

    public function close_drawer(Request $request){

        //jtlk28944 - 254747585100

        if($request->session()->has('uid')){
        
            $drawer = Drawer::find($request->drawer_id);
            $drawer->status = "close";
            $drawer->closing_amt = $request->cls_amt;
            $drawer->stop_time = time();
            $drawer->closed_by = $request->session()->get('uid');
            $drawer->save();

            $logs = new Logs();
            $logs->log_time = time();
            $logs->desc = 'Closed Drawer ';
            $logs->uid = $request->session()->get('uid');
            $logs->save();

            
            return json_encode(array('status'=>1));

        }
        
    }

    public function get_my_acc_dets(Request $request){

        $user = User::find($request->session()->get('uid'));

        return json_encode($user);
    }

    public function update_acc_info(Request $request){
        
        $user = User::find($request->session()->get('uid'));
        $user->fname = $request->up_fname;
        $user->lname = $request->up_lname;
        $user->phone = $request->up_phone;
        $user->email = $request->up_email;
        $user->save();

        $logs = new Logs();
        $logs->log_time = time();
        $logs->desc = 'Updated Account Info for: ' . $request->up_fname . ' ' . $user->lname . ' Phone: ' . $request->up_phone . ' Email: ' . $request->up_email;
        $logs->uid = $request->session()->get('uid');
        $logs->save();

        return json_encode(array('status'=>1));

    }

    public function update_passd(Request $request){

        $user = User::find($request->session()->get('uid'));
        $user->password = Hash::make($request->passd);
        $user->save();

        $logs = new Logs();
        $logs->log_time = time();
        $logs->desc = 'Updated Password ';
        $logs->uid = $request->session()->get('uid');
        $logs->save();

        return json_encode(array('status'=>1));

    }

    public function check_drawer(){
        
        $drawer = Drawer::select('id','start_time')->where('status','open')->get();
        
        
        if($drawer->count() > 0){
            foreach($drawer as $val){
                $id = $val->id;
                $start_time = $val->start_time;

                if(date("d",time()) == date("d",$start_time)){
                    return json_encode(array('status'=>2));
                }else{
                    return json_encode(array('status'=>1));
                }
                //return $diff;

            }
        }else{
            return json_encode(array('status'=>0));
        }
        
    }

    public function logout(Request $request){

        $request->session()->pull('uid');
        $request->session()->pull('fname');
        $request->session()->pull('lname');


        return json_encode(array('status'=>1));
    }

    public function item_sales_report(){

        $from = strtotime(date("d-m-Y",time()) . " 06:00:00");
        $to = strtotime(date("d-m-Y",time()) . " 23:59:59");

        $grp = ShoppingCart::groupBy('item')->get();
        $no = 1;
        foreach($grp as $i=>$val){
            
            $tender_qty = ShoppingCart::where('item',$val->item)->where('status',2)->whereBetween('time',array($from,$to))->where('type','tender')->sum('qty');
            $return_qty = ShoppingCart::where('item',$val->item)->where('status',4)->whereBetween('time',array($from,$to))->where('type','return')->sum('qty');
            $tender_ttl = ShoppingCart::where('item',$val->item)->where('status',2)->whereBetween('time',array($from,$to))->where('type','tender')->sum('total');
            $return_ttl = ShoppingCart::where('item',$val->item)->where('status',4)->whereBetween('time',array($from,$to))->where('type','return')->sum('total');
            if($tender_qty > 0){
                $d[$i]['no'] = $no;
                $d[$i]['item'] = Items::find($val->item)->item_desc;
                $d[$i]['total_qty'] = number_format($tender_qty - $return_qty);
                $d[$i]['total_total'] = number_format($tender_ttl - $return_ttl);
                $no++;
            }
           

        }   

        return json_encode($d);

    }

    public function catg_sales_report(){

        $from = strtotime(date("d-m-Y",time()) . " 06:00:00");
        $to = strtotime(date("d-m-Y",time()) . " 23:59:59");

        $sc_catg = ShoppingCart::select('catg')->groupBy('catg')->get();

        $no = 1;
        foreach($sc_catg as $i=>$val_catg){

            $qty_tender = ShoppingCart::where('catg',$val_catg->catg)->whereBetween('time',array($from,$to))->where('status','2')->where('type','tender')->sum('qty');
            $ttl_tender = ShoppingCart::where('catg',$val_catg->catg)->whereBetween('time',array($from,$to))->where('status','2')->where('type','tender')->sum('total');
            $qty_return = ShoppingCart::where('catg',$val_catg->catg)->whereBetween('time',array($from,$to))->where('status','4')->where('type','return')->sum('qty');
            $ttl_return = ShoppingCart::where('catg',$val_catg->catg)->whereBetween('time',array($from,$to))->where('status','4')->where('type','return')->sum('total');
            
            if($qty_tender > 0){

                $d[$i]['item'] = Catg::find($val_catg->catg)->catg_name;
                $d[$i]['no'] = $no;
                $d[$i]['total_qty'] = number_format($qty_tender - abs($qty_return));
                $d[$i]['total_total'] = number_format($ttl_tender - abs($ttl_return));
                $no++;

            }
            
        }
    
        return json_encode($d);
    }


    public function search_catg_sales_report(){

        $exp_dates = explode("to",$request->dates);
        $from = strtotime(str_replace(' ', '', $exp_dates[0]). " 06:00:00");
        $to = strtotime(str_replace(' ', '', $exp_dates[1]). " 23:59:59");
        
        $sc_catg = ShoppingCart::select('catg')->groupBy('catg')->get();

        $no = 1;
        foreach($sc_catg as $i=>$val_catg){

            $qty_tender = ShoppingCart::where('catg',$val_catg->catg)->whereBetween('time',array($from,$to))->where('status','2')->where('type','tender')->sum('qty');
            $ttl_tender = ShoppingCart::where('catg',$val_catg->catg)->whereBetween('time',array($from,$to))->where('status','2')->where('type','tender')->sum('total');
            $qty_return = ShoppingCart::where('catg',$val_catg->catg)->whereBetween('time',array($from,$to))->where('status','4')->where('type','return')->sum('qty');
            $ttl_return = ShoppingCart::where('catg',$val_catg->catg)->whereBetween('time',array($from,$to))->where('status','4')->where('type','return')->sum('total');
            
            $d[$i]['item'] = Catg::find($val_catg->catg)->catg_name;
            $d[$i]['no'] = $no;
            $d[$i]['total_qty'] = number_format($qty_tender - abs($qty_return));
            $d[$i]['total_total'] = number_format($ttl_tender - abs($ttl_return));
            $no++;
        }
    
        return json_encode($d);

    }

    public function filter_items_sales(Request $request){

        $exp_dates = explode("to",$request->dates);
        $from = strtotime(str_replace(' ', '', $exp_dates[0]). " 06:00:00");
        $to = strtotime(str_replace(' ', '', $exp_dates[1]). " 23:59:59");

        $grp = ShoppingCart::groupBy('item')->get();
        $no = 1;
        foreach($grp as $i=>$val){
            
            $tender_qty = ShoppingCart::where('item',$val->item)->where('status',2)->whereBetween('time',array($from,$to))->where('type','tender')->sum('qty');
            $return_qty = ShoppingCart::where('item',$val->item)->where('status',4)->whereBetween('time',array($from,$to))->where('type','return')->sum('qty');
            $tender_ttl = ShoppingCart::where('item',$val->item)->where('status',2)->whereBetween('time',array($from,$to))->where('type','tender')->sum('total');
            $return_ttl = ShoppingCart::where('item',$val->item)->where('status',4)->whereBetween('time',array($from,$to))->where('type','return')->sum('total');
            if($tender_qty > 0){
                $d[$i]['no'] = $no;
                $d[$i]['item'] = Items::find($val->item)->item_desc;
                $d[$i]['total_qty'] = number_format($tender_qty - $return_qty);
                $d[$i]['total_total'] = number_format($tender_ttl - $return_ttl);
                $no++;
            }
           

        }     

        return json_encode($d);
        
    }


    public function users_logs(){
        
        $from = strtotime(date("d-m-Y",time()) . " 06:00:00");
        $to = strtotime(date("d-m-Y",time()) . " 23:59:59");
        
        $logs = Logs::select('log_time','desc','uid')->whereBetween('log_time',array($from,$to))->get();
        foreach($logs as $i=>$val){
            $d[$i]['log_time'] = date('d/m/y h:i:s',$val->log_time);
            $d[$i]['desc'] = $val->desc;
            $d[$i]['user'] = User::find($val->uid)->lname;
        }

        if($logs->count() > 0){
            return json_encode($d);
        }else{
            return json_encode(array('status'=>0));
        }
    }

    public function search_logs(Request $request){

        $exp_dates = explode("to",$request->dates);
        $from = strtotime(str_replace(' ', '', $exp_dates[0]). " 06:00:00");
        $to = strtotime(str_replace(' ', '', $exp_dates[1]). " 23:59:59");

        $from = strtotime(date("d-m-Y",time()) . " 06:00:00");
        $to = strtotime(date("d-m-Y",time()) . " 23:59:59");
        
        $logs = Logs::select('log_time','desc','uid')->whereBetween('log_time',array($from,$to))->get();
        foreach($logs as $i=>$val){
            $d[$i]['log_time'] = date('d/m/y h:i:s',$val->log_time);
            $d[$i]['desc'] = $val->desc;
            $d[$i]['user'] = User::find($val->uid)->lname;
        }

        if($logs->count() > 0){
            return json_encode($d);
        }else{
            return json_encode(array('status'=>0));
        }

    }

    public function admin_catg_report(Request $request){

        $catg = Catg::select('id','catg_name','status')->where('status',1)->get();
        $no = 1;

        foreach($catg as $i=>$val){

            $d[$i]['no'] = $no;
            $d[$i]['id'] = $val->id;
            $d[$i]['catg_name'] = $val->catg_name;
            if($val->status ==1){
                $d[$i]['status'] = "Active";
            }else{
                $d[$i]['status'] = "InActive";
            }
            
            $no++;
        }

        return json_encode(array($d));

    }

    public function new_item_catg(Request $request){

  
        
        $catg_chk = Catg::select('id')->where('catg_name',$request->catg)->get();
        
        if($catg_chk->count() == 0){
            
            $catg = new Catg();
            $catg->catg_name = $request->catg;
            $catg->status = 1;
            $catg->time = time();
            $catg->save();
            
            return json_encode(array('status'=>1));
           
        }else{
            return json_encode(array('status'=>0));
        }
      
        
    }

    public function catg_trans_by_items(Request $request){
        
       $sc =  DB::select('SELECT SUM(shopping_cart.total) AS total, items.catg FROM shopping_cart RIGHT JOIN items ON shopping_cart.item=items.id GROUP BY items.catg');
        
       
       
        if(count($sc) > 0 ){
          
            foreach($sc as $i=>$val){
               
                $d[$i]['total'] = number_format($val->total);
                $d[$i]['catg'] = Catg::find($val->catg)->catg_name;
             
            }

            return json_encode($d);
           
        }else{
            return json_encode(array('status'=>0));
        }
        
    }

    public function savePO(Request $request){

        $sum_qty = ShoppingCart::where('status',1)->where('type','po')->where('uid',$request->session()->get('uid'))->sum('qty');
        $sum_total = ShoppingCart::where('status',1)->where('type','po')->where('uid',$request->session()->get('uid'))->sum('total');
        $sum_tax = ShoppingCart::where('status',1)->where('type','po')->where('uid',$request->session()->get('uid'))->sum('tax');
        
        $po = new PurchaseOrder();
        $po->no_items = $sum_qty;
        $po->total_tax = $sum_tax;
        $po->supplier = 1;
        $po->total_cost = $sum_total;
        $po->status = 0;
        $po->uid = $request->session()->get('uid');
        $po->trans_time = time();
        $po->save();

        $po_chk = PurchaseOrder::select('id')->where('uid',$request->session()->get('uid'))->where('status',0)->orderBy('id','desc')->first();


        $get_sc = ShoppingCart::select('id')->where('type','po')->where('uid',$request->session()->get('uid'))->where('status',1)->get();
        
        foreach($get_sc as $val_sc){
            
            $up_sc = ShoppingCart::find($val_sc->id);
            $up_sc->tid = $po_chk->id;
            $up_sc->status = 2;
            $up_sc->save();

        }
        
        echo json_encode(array('status'=>1));

    }


    public function get_catg_options(Request $request){
        $catg  = Catg::select('id','catg_name')->where('status',1)->get();
        return(json_encode($catg));
    }

    public function new_item_sub_catg(Request $request){
        //catg:catg,sub_catg:sub_catg

        $chk_sub = SubCatg::select('id')->where('sub',$request->sub_catg)->get();
        if($chk_sub->count() ==0){

            $sub = new SubCatg();
            $sub->catg = $request->catg;
            $sub->sub = $request->sub_catg;
            $sub->status = 1;
            $sub->time = time();
            $sub->save();

            return(json_encode(array('status'=>1)));
        }else{
            return(json_encode(array('status'=>0)));
        }
        
    }

    public function get_sub_catg_options(Request $request){
        $sub  = SubCatg::select('id','sub')->where('catg',$request->catg)->where('status',1)->get();
        return(json_encode($sub));
    }

    public function get_all_sub_catg_options(Request $request){
        $sub  = SubCatg::select('id','sub')->where('status',1)->get();
        return(json_encode($sub));
    }

    public function todays_inventory_reports(){
        
        
        $first_day_morn = strtotime(date('01-m-Y') . " 06:00:00");
        $first_day_eve = strtotime(date('01-m-Y') . " 23:59:59");
        $last_day_eve = strtotime(date('t-m-Y') . " 23:59:59");
       
        $items = Items::select('id','item_desc')->get();

        foreach($items as $i=>$val){
            $d[$i]['item_desc'] = $val->item_desc;
            $d[$i]['item_id'] = $val->id;

            //$open_goods_qty = Goods::where('item',$val->id)->whereBetween('date_received',array($first_day_morn,$first_day_eve))->sum('qty');
            $open_sales_qty = ShoppingCart::where('item',$val->id)->where('time','<',$first_day_morn)->where('type','tender')->sum('qty');
            $open_goods_qty = Goods::where('item',$val->id)->where('status',1)->where('date_received','<',$first_day_morn)->sum('qty');
            $goods_qty_recieved = Goods::where('item',$val->id)->where('status',1)->whereBetween('date_received',array($first_day_morn,$last_day_eve))->sum('qty');
            $sc_qty = ShoppingCart::where('item',$val->id)->where('status','2')->where('type','tender')->whereBetween('time',array($first_day_morn,$last_day_eve))->sum('qty');
            $qty_goods_sold = $goods_qty_recieved - $sc_qty;
            //$cost = Goods::where('item',$val->id)->where('date_received','<',$first_day_morn)->sum('qty');
            $cost = ShoppingCart::where('item',$val->id)->where('status','2')->where('type','tender')->whereBetween('time',array($first_day_morn,$last_day_eve))->sum('cost');

            $sales = ShoppingCart::where('item',$val->id)->where('status','2')->where('type','tender')->whereBetween('time',array($first_day_morn,$last_day_eve))->sum('total');
            $tax = ShoppingCart::where('item',$val->id)->where('status','2')->where('type','tender')->whereBetween('time',array($first_day_morn,$last_day_eve))->sum('tax');
            $profit = ($sales - $cost) - $tax;
            $curr_stock_qty = Goods::where('item',$val->id)->where('status',1)->sum('qty') - ShoppingCart::where('item',$val->id)->where('status',2)->where('type','tender')->sum('qty');

            $rtrn = ShoppingCart::where('item',$val->id)->where('type','return')->whereBetween('time',array($first_day_morn,$last_day_eve))->sum('qty');

            $d[$i]['open_goods_qty'] = number_format($open_goods_qty - $open_sales_qty);
            $d[$i]['goods_qty_recieved'] = number_format($goods_qty_recieved);
            $d[$i]['sc_qty'] = number_format($sc_qty);
            $d[$i]['curr_qty'] = number_format($curr_stock_qty);
            $d[$i]['qty_goods_sold'] = number_format($sc_qty);
            $d[$i]['cost'] = number_format($cost);
            $d[$i]['sales'] = number_format($sales);
            $d[$i]['tax'] = number_format($tax);
            $d[$i]['profit'] = number_format($profit);
            $d[$i]['rtrn'] = number_format(abs($rtrn));
            

        }

        echo json_encode($d);
    }


    public function todays_inventory_totals(){

        $first_day_morn = strtotime(date('01-m-Y') . " 06:00:00");
        $first_day_eve = strtotime(date('01-m-Y') . " 23:59:59");
        $last_day_eve = strtotime(date('t-m-Y') . " 23:59:59");

        //$open_goods_qty = Goods::whereBetween('date_received',array($first_day_morn,$first_day_eve))->sum('qty');
        $open_goods_qty = Goods::where('date_received','<',$first_day_morn)->where('status',1)->sum('qty');
        $goods_qty_recieved = Goods::whereBetween('date_received',array($first_day_morn,$last_day_eve))->where('status',1)->sum('qty');
        $sc_qty = ShoppingCart::where('status','2')->where('type','tender')->whereBetween('time',array($first_day_morn,$last_day_eve))->sum('qty');
        $qty_goods_sold = $goods_qty_recieved - $sc_qty;
        $cost = ShoppingCart::where('status','2')->where('type','tender')->whereBetween('time',array($first_day_morn,$last_day_eve))->sum('cost');
        $sales = ShoppingCart::where('status','2')->where('type','tender')->whereBetween('time',array($first_day_morn,$last_day_eve))->sum('total');
        $tax = ShoppingCart::where('status','2')->where('type','tender')->whereBetween('time',array($first_day_morn,$last_day_eve))->sum('tax');
        $profit = $sales - $cost - $tax;

        $rtrn = ShoppingCart::where('type','return')->whereBetween('time',array($first_day_morn,$last_day_eve))->sum('qty');

        
        $d['open_goods_qty'] = number_format($open_goods_qty);
        $d['goods_qty_recieved'] = number_format($goods_qty_recieved);
        $d['sc_qty'] = number_format($sc_qty);
        $d['curr_qty'] = 0;
        $d['qty_goods_sold'] = number_format($sc_qty);
        $d['cost'] = number_format($cost);
        $d['sales'] = number_format($sales);
        $d['tax'] = number_format($tax);
        $d['profit'] = number_format($profit);
        $d['rtrn'] = number_format(abs($rtrn));

        echo json_encode($d);
    }
    
    public function search_todays_inventory_reports(Request $request){
        $exp_dates = explode("to",$request->dates);
        $first_day_morn = strtotime(str_replace(' ', '', $exp_dates[0]). " 06:00:00");
        $first_day_eve = strtotime(str_replace(' ', '', $exp_dates[0]). " 23:59:59");
        $last_day_eve = strtotime(str_replace(' ', '', $exp_dates[1]). " 23:59:59");

        
        $items = Items::select('id','item_desc')->get();

        foreach($items as $i=>$val){
            $d[$i]['item_desc'] = $val->item_desc;
            $d[$i]['item_id'] = $val->id;

            //$open_goods_qty = Goods::where('item',$val->id)->whereBetween('date_received',array($first_day_morn,$first_day_eve))->sum('qty');
            $open_goods_qty = Goods::where('item',$val->id)->where('status',1)->where('date_received','<',$first_day_morn)->sum('qty');
            $goods_qty_recieved = Goods::where('item',$val->id)->where('status',1)->whereBetween('date_received',array($first_day_morn,$last_day_eve))->sum('qty');
            $sc_qty = ShoppingCart::where('item',$val->id)->where('status','2')->whereBetween('time',array($first_day_morn,$last_day_eve))->sum('qty');
            $qty_goods_sold = $goods_qty_recieved - $sc_qty;
            $cost = ShoppingCart::where('item',$val->id)->where('status','2')->whereBetween('time',array($first_day_morn,$last_day_eve))->sum('cost');
            $sales = ShoppingCart::where('item',$val->id)->where('status','2')->whereBetween('time',array($first_day_morn,$last_day_eve))->sum('total');
            $tax = ShoppingCart::where('item',$val->id)->where('status','2')->whereBetween('time',array($first_day_morn,$last_day_eve))->sum('tax');
            $profit = $sales - $cost - $tax;
            $curr_stock_qty = Goods::where('item',$val->id)->where('status',1)->sum('qty') - ShoppingCart::where('item',$val->id)->where('status',2)->sum('qty');
        
            $rtrn = ShoppingCart::where('item',$val->id)->where('type','return')->whereBetween('time',array($first_day_morn,$last_day_eve))->sum('qty');


            $d[$i]['open_goods_qty'] = number_format($open_goods_qty);
            $d[$i]['goods_qty_recieved'] = number_format($goods_qty_recieved);
            $d[$i]['sc_qty'] = number_format($sc_qty);
            $d[$i]['curr_qty'] = number_format($curr_stock_qty);
            $d[$i]['qty_goods_sold'] = number_format($sc_qty);
            $d[$i]['cost'] = number_format($cost);
            $d[$i]['sales'] = number_format($sales);
            $d[$i]['tax'] = number_format($tax);
            $d[$i]['rtrn'] = number_format(abs($rtrn));
            $d[$i]['profit'] = number_format($profit);
            

        }

        echo json_encode($d);
        
    }

    public function search_todays_inventory_totals(Request $request){
       
        $exp_dates = explode("to",$request->dates);
        $first_day_morn = strtotime(str_replace(' ', '', $exp_dates[0]). " 06:00:00");
        $first_day_eve = strtotime(str_replace(' ', '', $exp_dates[0]). " 23:59:59");
        $last_day_eve = strtotime(str_replace(' ', '', $exp_dates[1]). " 23:59:59");

        $open_goods_qty = Goods::where('date_received','<',$first_day_morn)->where('status',1)->sum('qty');
        //$open_goods_qty = Goods::whereBetween('date_received',array($first_day_morn,$first_day_eve))->sum('qty');
        $goods_qty_recieved = Goods::whereBetween('date_received',array($first_day_morn,$last_day_eve))->where('status',1)->sum('qty');
        $sc_qty = ShoppingCart::where('status','2')->whereBetween('time',array($first_day_morn,$last_day_eve))->sum('qty');
        $qty_goods_sold = $goods_qty_recieved - $sc_qty;
        $cost = ShoppingCart::where('status','2')->whereBetween('time',array($first_day_morn,$last_day_eve))->sum('cost');
        $sales = ShoppingCart::where('status','2')->whereBetween('time',array($first_day_morn,$last_day_eve))->sum('total');
        $tax = ShoppingCart::where('status','2')->whereBetween('time',array($first_day_morn,$last_day_eve))->sum('tax');
        $profit = $sales - $cost - $tax;
        $curr_stock_qty = Goods::where('item',$item)->where('status',1)->sum('qty') - ShoppingCart::where('item',$item)->sum('qty');
        

        $rtrn = ShoppingCart::where('type','return')->whereBetween('time',array($first_day_morn,$last_day_eve))->sum('qty');

        $d['open_goods_qty'] = number_format($open_goods_qty);
        $d['goods_qty_recieved'] = number_format($goods_qty_recieved);
        $d['sc_qty'] = number_format($sc_qty);
        $d['curr_qty'] = 0;
        $d['qty_goods_sold'] = number_format($sc_qty);
        $d['cost'] = number_format($cost);
        $d['sales'] = number_format($sales);
        $d['tax'] = number_format($tax);
        $d['rtrn'] = number_format(abs($tax));
        $d['profit'] = number_format($profit);

        echo json_encode($d);
    }

    public function dets_search_todays_inventory_reports(Request $request){
        $exp_dates = explode("to",$request->dates);
        $first_day_morn = strtotime(str_replace(' ', '', $exp_dates[0]). " 06:00:00");
        $first_day_eve = strtotime(str_replace(' ', '', $exp_dates[0]). " 23:59:59");
        $last_day_eve = strtotime(str_replace(' ', '', $exp_dates[1]). " 23:59:59");

        
        $items = Items::select('id','item_desc')->where('item_desc',$request->item)->get();

        foreach($items as $i=>$val){
            $d[$i]['item_desc'] = $val->item_desc;
            $d[$i]['item_id'] = $val->id;

            //$open_goods_qty = Goods::where('item',$val->id)->whereBetween('date_received',array($first_day_morn,$first_day_eve))->sum('qty');
            $open_goods_qty = Goods::where('item',$val->id)->where('status',1)->where('date_received','<',$first_day_morn)->sum('qty');
            $goods_qty_recieved = Goods::where('item',$val->id)->where('status',1)->whereBetween('date_received',array($first_day_morn,$last_day_eve))->sum('qty');
            $sc_qty = ShoppingCart::where('item',$val->id)->where('status','2')->whereBetween('time',array($first_day_morn,$last_day_eve))->sum('qty');
            $qty_goods_sold = $goods_qty_recieved - $sc_qty;
            $cost = ShoppingCart::where('item',$val->id)->where('status','2')->whereBetween('time',array($first_day_morn,$last_day_eve))->sum('cost');
            $sales = ShoppingCart::where('item',$val->id)->where('status','2')->whereBetween('time',array($first_day_morn,$last_day_eve))->sum('total');
            $tax = ShoppingCart::where('item',$val->id)->where('status','2')->whereBetween('time',array($first_day_morn,$last_day_eve))->sum('tax');
            $profit = $sales - $cost - $tax;
            $curr_stock_qty = Goods::where('item',$val->id)->where('status',1)->sum('qty') - ShoppingCart::where('item',$val->id)->sum('qty');
        
            $rtrn = ShoppingCart::where('item',$val->id)->where('type','return')->whereBetween('time',array($first_day_morn,$last_day_eve))->sum('qty');


            $d[$i]['open_goods_qty'] = number_format($open_goods_qty);
            $d[$i]['goods_qty_recieved'] = number_format($goods_qty_recieved);
            $d[$i]['sc_qty'] = number_format($sc_qty);
            $d[$i]['curr_qty'] = number_format($curr_stock_qty);
            $d[$i]['qty_goods_sold'] = number_format($sc_qty);
            $d[$i]['cost'] = number_format($cost);
            $d[$i]['sales'] = number_format($sales);
            $d[$i]['tax'] = number_format($tax);
            $d[$i]['rtrn'] = number_format(abs($rtrn));
            $d[$i]['profit'] = number_format($profit);
            

        }

        echo json_encode($d);
        
    }

    public function dets_search_todays_inventory_totals(Request $request){
       
        $exp_dates = explode("to",$request->dates);
        $first_day_morn = strtotime(str_replace(' ', '', $exp_dates[0]). " 06:00:00");
        $first_day_eve = strtotime(str_replace(' ', '', $exp_dates[0]). " 23:59:59");
        $last_day_eve = strtotime(str_replace(' ', '', $exp_dates[1]). " 23:59:59");

        $item = Items::select('id')->where('item_desc',$request->item)->limit(1)->first()->id;

        
        $open_goods_qty = Goods::where('date_received','<',$first_day_morn)->where('status',1)->where('item',$item)->sum('qty');
        $goods_qty_recieved = Goods::whereBetween('date_received',array($first_day_morn,$last_day_eve))->where('status',1)->where('item',$item)->sum('qty');
        $sc_qty = ShoppingCart::where('status','2')->whereBetween('time',array($first_day_morn,$last_day_eve))->where('item',$item)->sum('qty');
        $qty_goods_sold = $goods_qty_recieved - $sc_qty;
        $cost = ShoppingCart::where('status','2')->whereBetween('time',array($first_day_morn,$last_day_eve))->where('item',$item)->sum('cost');
        $sales = ShoppingCart::where('status','2')->whereBetween('time',array($first_day_morn,$last_day_eve))->where('item',$item)->sum('total');
        $tax = ShoppingCart::where('status','2')->whereBetween('time',array($first_day_morn,$last_day_eve))->where('item',$item)->sum('tax');
        $profit = $sales - $cost - $tax;
        $curr_stock_qty = Goods::where('item',$item)->where('status',1)->sum('qty') - ShoppingCart::where('item',$item)->sum('qty');
        

        $rtrn = ShoppingCart::where('type','return')->whereBetween('time',array($first_day_morn,$last_day_eve))->where('item',$item)->sum('qty');

        $d['open_goods_qty'] = number_format($open_goods_qty);
        $d['goods_qty_recieved'] = number_format($goods_qty_recieved);
        $d['sc_qty'] = number_format($sc_qty);
        $d['curr_qty'] = $curr_stock_qty;
        $d['qty_goods_sold'] = number_format($sc_qty);
        $d['cost'] = number_format($cost);
        $d['sales'] = number_format($sales);
        $d['tax'] = number_format($tax);
        $d['rtrn'] = number_format(abs($tax));
        $d['profit'] = number_format($profit);

        echo json_encode($d);
    }

    public function get_category_rprt_data(){
        $catg = Catg::select('id','catg_name','time')->get();

        echo json_encode($catg);
    }

    public function get_sub_category_rprt_data(){
        $sub = SubCatg::select('id','sub','catg','time')->get();
        
        if($sub->count() > 0){
            foreach($sub as $i=>$val){
                $d[$i]['id'] = $val->id;
                $d[$i]['sub'] = $val->sub;
                $d[$i]['catg'] = Catg::find($val->catg)->catg_name;
            }
        }else{
            $d = [];
        }
        
        echo json_encode($d);
    }

    public function disable_item(Request $request){
        $item = Items::find($request->id);
        $item->status = 0;
        $item->save();

        $logs = new Logs();
        $logs->log_time = time();
        $logs->desc = 'Disabled Item: ' . $item->item_desc;
        $logs->uid = $request->session()->get('uid');
        $logs->save();
    }

    public function enable_item(Request $request){
        $item = Items::find($request->id);
        $item->status = 1;
        $item->save();

        $logs = new Logs();
        $logs->log_time = time();
        $logs->desc = 'Enabled Item: ' . $item->item_desc;
        $logs->uid = $request->session()->get('uid');
        $logs->save();
    }

    public function disable_user(Request $request){
        $item = User::find($request->id);
        $item->status = 0;
        $item->save();

        $logs = new Logs();
        $logs->log_time = time();
        $logs->desc = 'Disabled Account for: ' . $item->fname . ' ' . $item->lname;
        $logs->uid = $request->session()->get('uid');
        $logs->save();
    }

    public function enable_user(Request $request){
        $item = User::find($request->id);
        $item->status = 1;
        $item->save();

        $logs = new Logs();
        $logs->log_time = time();
        $logs->desc = 'Enabled Account for: ' . $item->fname . ' ' . $item->lname;
        $logs->uid = $request->session()->get('uid');
        $logs->save();
    }

    public function search_item_reports(Request $request){
       
        if(empty($request->item)){
            $item = Items::select('id','up_id','code_no','look_up','item_desc','sell_price','qty')->where('status',1)->limit(10)->get();
       
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

          foreach($item as $i=>$val){
                $d[$i]['id'] = $val->id;
                $d[$i]['code_no'] = $val->code_no;
                $d[$i]['look_up'] = $val->look_up;
                $d[$i]['item_desc'] = $val->item_desc;
                $d[$i]['up_id'] = $val->up_id;
                $d[$i]['sell_price'] = number_format($val->sell_price);
                $gds = Goods::where('item',$val->id)->where('status',1)->sum('qty');
                $sc = ShoppingCart::where('item',$val->id)->where('status',2)->where('type','tender')->sum('qty');
                
                $sc_ret = ShoppingCart::where('item',$val->id)->where('status',4)->where('type','return')->sum('qty');
                $d[$i]['qty'] = round($gds - ($sc + $sc_ret));
                $d[$i]['tax'] = Tax::find($val->tax)->tax_desc;
                $d[$i]['reg_time'] = ($val->reg_time) ? date("d-m-Y H:i:s",$val->reg_time) : '';
                if($val->status == 1){
                    $d[$i]['status'] = "Active";
                }else{
                    $d[$i]['status'] = "InActive";
                }
                
                
            }

        
       
        return json_encode($d);
        
        
    }


    public function alt_detailed_search_transactions(Request $request){
        
         $exp_dates = explode("to",$request->dates);
         $from = strtotime(str_replace(' ', '', $exp_dates[0]). " 06:00:00");
         $to = strtotime(str_replace(' ', '', $exp_dates[1]). " 23:59:59");
         
         $arr = array(
             $request->cash,
             $request->mpesa,
             $request->rtrn,
             $request->rtrn_tender,
             $request->ref_no,
             $request->receipt_no,
             $request->pdq,
             $request->cheque
         );

         
         
         $d = [];
         $z = [];
         $y = [];
         $w = [];
         $v = [];
         $u = [];
         
         for($x = 0; $x < count($arr); $x++){
             
             if($arr[1] ==1){
                
                if(empty($request->users) && empty($request->branch)){
                    
                    if(empty($arr[4]) && empty($arr[5])){
                        
                        $res_mpesa = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('type','mpesa tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && empty($arr[5])){
                        
                        $res_mpesa = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('ref_no',$arr[4])->where('type','mpesa tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $res_mpesa = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->where('type','mpesa tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                    elseif(empty($arr[4]) && !empty($arr[5])){
                        
                        $res_mpesa = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('receipt_no',$arr[5])->where('type','mpesa tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                }elseif(!empty($request->users) && empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $res_mpesa = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('type','mpesa tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $res_mpesa = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('ref_no',$arr[4])->where('type','mpesa tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $res_mpesa = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->where('type','mpesa tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                    elseif(empty($arr[4]) && !empty($arr[5])){
                        
                        $res_mpesa = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('receipt_no',$arr[5])->where('type','mpesa tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                }elseif(empty($request->users) && !empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $res_mpesa = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('branch',$request->branch)->where('type','mpesa tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $res_mpesa = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('branch',$request->branch)->where('ref_no',$arr[4])->where('type','mpesa tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $res_mpesa = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('branch',$request->branch)->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->where('type','mpesa tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                    elseif(empty($arr[4]) && !empty($arr[5])){
                        
                        $res_mpesa = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('branch',$request->branch)->where('receipt_no',$arr[5])->where('type','mpesa tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                }elseif(!empty($request->users) && !empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $res_mpesa = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('branch',$request->branch)->where('type','mpesa tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $res_mpesa = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('branch',$request->branch)->where('ref_no',$arr[4])->where('type','mpesa tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $res_mpesa = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('branch',$request->branch)->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->where('type','mpesa tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                    elseif(empty($arr[4]) && !empty($arr[5])){
                        
                        $res_mpesa = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('branch',$request->branch)->where('receipt_no',$arr[5])->where('type','mpesa tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                }
                
                if($res_mpesa->count() > 0){
                    foreach($res_mpesa as $b=>$val_mpesa){
                        $z[$b]['id'] = $val_mpesa->id;
                        
                        $z[$b]['trans_time'] = date("d/m/y h:i",$val_mpesa->trans_time);
                        $z[$b]['total'] = number_format($val_mpesa->total) . ".00";
                        $z[$b]['receipt_no'] = $val_mpesa->receipt_no;
                        $z[$b]['ref_no'] = $val_mpesa->ref_no;
                        $z[$b]['no_items'] = $val_mpesa->no_items;
                        $z[$b]['branch'] = Branches::find($val_mpesa->branch)->branch;
                        $z[$b]['type'] = $val_mpesa->type;
                        $z[$b]['up_id'] = $val_mpesa->up_id;
                
                        $user_dets = User::find($val_mpesa->user);
                        $z[$b]['user'] = substr($user_dets->fname, 0, 1) . ". ". $user_dets->lname;
                        if($val_mpesa->status ==0){
                            $z[$b]['status'] = "Deleted";
                        }else if($val_mpesa->status ==1){
                            $z[$b]['status'] = "Complete";
                        }
                        
                        $z[$b]['type'] = $val_mpesa->type;
                       
                     }
                }else{
                    $z = [];
                }
                
                
                
                
             }
            
            
             if($arr[2] ==1){

                if(empty($request->users) && empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $res_return = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('type','return')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $res_return = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('ref_no',$arr[4])->where('type','return')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $res_return = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->where('type','return')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                    elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $res_return = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('receipt_no',$arr[5])->where('type','return')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                }elseif(!empty($request->users) && empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $res_return = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('type','return')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $res_return = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('ref_no',$arr[4])->where('type','return')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $res_return = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->where('type','return')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                    elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $res_return = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('receipt_no',$arr[5])->where('type','return')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                }elseif(empty($request->users) && !empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $res_return = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('branch',$request->branch)->where('type','return')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $res_return = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('branch',$request->branch)->where('ref_no',$arr[4])->where('type','return')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $res_return = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('branch',$request->branch)->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->where('type','return')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                    elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $res_return = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('branch',$request->branch)->where('receipt_no',$arr[5])->where('type','return')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                }elseif(!empty($request->users) && !empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $res_return = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('branch',$request->branch)->where('type','return')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $res_return = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('ref_no',$arr[4])->where('type','return')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $res_return = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->where('type','return')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                    elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $res_return = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('receipt_no',$arr[5])->where('type','return')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                }
                 
                
                    if($res_return->count() > 0){
                        foreach($res_return as $c=>$val_return){
                            $y[$c]['id'] = $val_return->id;
                            $y[$c]['trans_time'] = date("d/m/y h:i",$val_return->trans_time);
                            $y[$c]['total'] = number_format($val_return->total) . ".00";
                            $y[$c]['receipt_no'] = $val_return->receipt_no;
                            $y[$c]['ref_no'] = $val_return->ref_no;
                            $y[$c]['branch'] = Branches::find($val_return->branch)->branch;
                            $y[$c]['no_items'] = $val_return->no_items;
                            $y[$c]['type'] = $val_return->type;
                            $y[$c]['up_id'] = $val_return->up_id;
                            
                            $user_dets = User::find($val_return->user);
                            $y[$c]['user'] = substr($user_dets->fname, 0, 1) . ". ". $user_dets->lname;
                            if($val_return->status ==0){
                                $y[$c]['status'] = "Deleted";
                            }else if($val_return->status ==1){
                                $y[$c]['status'] = "Complete";
                            }
                            
                            $y[$c]['type'] = $val_return->type;
                         }
                    }else{
                        $y = [];
                    }
                 
             }
              
             if($arr[3] ==1){


                if(empty($request->users) && empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $res_return_tender = Transactions::select('up_id','type','branch','status','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('type','return tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $res_return_tender = Transactions::select('up_id','type','branch','status','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('ref_no',$arr[4])->where('type','return tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $res_return_tender = Transactions::select('up_id','type','branch','status','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->where('type','return tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                    elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $res_return_tender = Transactions::select('up_id','type','branch','status','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('receipt_no',$arr[5])->where('type','return tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                }elseif(!empty($request->users) && empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $res_return_tender = Transactions::select('up_id','type','branch','status','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('type','return tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $res_return_tender = Transactions::select('up_id','type','branch','status','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('ref_no',$arr[4])->where('type','return tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $res_return_tender = Transactions::select('up_id','type','branch','status','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->where('type','return tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                    elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $res_return_tender = Transactions::select('up_id','type','branch','status','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('receipt_no',$arr[5])->where('type','return tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                }elseif(empty($request->users) && !empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $res_return_tender = Transactions::select('up_id','type','branch','status','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('branch',$request->branch)->where('type','return tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $res_return_tender = Transactions::select('up_id','type','branch','status','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('branch',$request->branch)->where('ref_no',$arr[4])->where('type','return tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $res_return_tender = Transactions::select('up_id','type','branch','status','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('branch',$request->branch)->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->where('type','return tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                    elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $res_return_tender = Transactions::select('up_id','type','branch','status','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('branch',$request->branch)->where('receipt_no',$arr[5])->where('type','return tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                }elseif(!empty($request->users) && !empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $res_return_tender = Transactions::select('up_id','type','branch','status','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('branch',$request->branch)->where('type','return tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $res_return_tender = Transactions::select('up_id','type','branch','status','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('branch',$request->branch)->where('ref_no',$arr[4])->where('type','return tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $res_return_tender = Transactions::select('up_id','type','branch','status','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('branch',$request->branch)->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->where('type','return tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                    elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $res_return_tender = Transactions::select('up_id','type','branch','status','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('branch',$request->branch)->where('receipt_no',$arr[5])->where('type','return tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                }

                if($res_return_tender->count() > 0){
                    foreach($res_return_tender as $e=>$val_return_tender){
                        $w[$e]['id'] = $val_return_tender->id;
                        $w[$e]['trans_time'] = date("d/m/y h:i",$val_return_tender->trans_time);
                        $w[$e]['total'] = number_format($val_return_tender->total) . ".00";
                        $w[$e]['receipt_no'] = $val_return_tender->receipt_no;
                        $w[$e]['ref_no'] = $val_return_tender->ref_no;
                        $w[$e]['no_items'] = $val_return_tender->no_items;
                        $w[$e]['branch'] = Branches::find($val_return_tender->branch)->branch;
                        $w[$e]['type'] = $val_return_tender->type;
                        $w[$e]['up_id'] = $val_return_tender->up_id;
                        
                        $user_dets = User::find($val_return_tender->user);
                        $w[$e]['user'] = substr($user_dets->fname, 0, 1) . ". ". $user_dets->lname;
                        if($val_return_tender->status ==0){
                            $w[$e]['status'] = "Deleted";
                        }else if($val_return_tender->status ==1){
                            $w[$e]['status'] = "Complete";
                        }
                        
                        $w[$e]['type'] = $val_return_tender->type;
                     }
                }else{
                    $w = [];
                }
                 
                
             }


             if($arr[6] ==1){


                if(empty($request->users) && empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $res_pdq = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('type','pdq tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $res_pdq = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('ref_no',$arr[4])->where('type','pdq tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $res_pdq = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->where('type','pdq tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                    elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $res_pdq = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('receipt_no',$arr[5])->where('type','pdq tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                }elseif(!empty($request->users) && empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $res_pdq = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('type','pdq tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $res_pdq = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('ref_no',$arr[4])->where('type','pdq tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $res_pdq = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->where('type','pdq tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                    elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $res_pdq = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('receipt_no',$arr[5])->where('type','pdq tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                }elseif(empty($request->users) && !empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $res_pdq = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('branch',$request->branch)->where('type','pdq tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $res_pdq = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('branch',$request->branch)->where('ref_no',$arr[4])->where('type','pdq tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $res_pdq = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('branch',$request->branch)->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->where('type','pdq tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $res_pdq = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('branch',$request->branch)->where('receipt_no',$arr[5])->where('type','pdq tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                }elseif(!empty($request->users) && !empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $res_pdq = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('branch',$request->branch)->where('user',$request->users)->where('type','pdq tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $res_pdq = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('branch',$request->branch)->where('user',$request->users)->where('ref_no',$arr[4])->where('type','pdq tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $res_pdq = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('branch',$request->branch)->where('user',$request->users)->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->where('type','pdq tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $res_pdq = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('branch',$request->branch)->where('user',$request->users)->where('receipt_no',$arr[5])->where('type','pdq tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                }
                
                
                if($res_pdq->count() > 0){
                    foreach($res_pdq as $b=>$val_pdq){
                        $v[$b]['id'] = $val_pdq->id;
                        
                        $v[$b]['trans_time'] = date("d/m/y h:i",$val_pdq->trans_time);
                        $v[$b]['total'] = number_format($val_pdq->total) . ".00";
                        $v[$b]['receipt_no'] = $val_pdq->receipt_no;
                        $v[$b]['ref_no'] = $val_pdq->ref_no;
                        $v[$b]['branch'] = Branches::find($val_pdq->branch)->branch;
                        $v[$b]['no_items'] = $val_pdq->no_items;
                        $v[$b]['type'] = $val_pdq->type;
                        $v[$b]['up_id'] = $val_pdq->up_id;
                        
                        $user_dets = User::find($val_pdq->user);
                        $v[$b]['user'] = substr($user_dets->fname, 0, 1) . ". ". $user_dets->lname;
                        if($val_pdq->status ==0){
                            $v[$b]['status'] = "Deleted";
                        }else if($val_pdq->status ==1){
                            $v[$b]['status'] = "Complete";
                        }
                        
                        $v[$b]['type'] = $val_pdq->type;
                       
                     }
                }else{
                    $v = [];
                }
                
                
                
             }



             if($arr[7] ==1){


                if(empty($request->users) && empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $res_cheque = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('type','cheque tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $res_cheque = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('ref_no',$arr[4])->where('type','cheque tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $res_cheque = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->where('type','cheque tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                    elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $res_cheque = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('receipt_no',$arr[5])->where('type','cheque tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                }elseif(!empty($request->users) && empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $res_cheque = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('type','cheque tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $res_cheque = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('ref_no',$arr[4])->where('type','cheque tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $res_cheque = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->where('type','cheque tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $res_cheque = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('receipt_no',$arr[5])->where('type','cheque tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                }elseif(empty($request->users) && !empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $res_cheque = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('branch',$request->branch)->where('type','cheque tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $res_cheque = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('branch',$request->branch)->where('ref_no',$arr[4])->where('type','cheque tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $res_cheque = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('branch',$request->branch)->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->where('type','cheque tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $res_cheque = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('branch',$request->branch)->where('receipt_no',$arr[5])->where('type','cheque tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                }elseif(!empty($request->users) && !empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $res_cheque = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('branch',$request->branch)->where('user',$request->users)->where('type','cheque tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $res_cheque = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('branch',$request->branch)->where('user',$request->users)->where('ref_no',$arr[4])->where('type','cheque tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $res_cheque = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('branch',$request->branch)->where('user',$request->users)->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->where('type','cheque tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $res_cheque = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('branch',$request->branch)->where('user',$request->users)->where('receipt_no',$arr[5])->where('type','cheque tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                }
                
                if($res_cheque->count() > 0){
                    foreach($res_cheque as $h=>$val_cheque){
                        $u[$h]['id'] = $val_cheque->id;
                        
                        $u[$h]['trans_time'] = date("d/m/y h:i",$val_cheque->trans_time);
                        $u[$h]['total'] = number_format($val_cheque->total) . ".00";
                        $u[$h]['receipt_no'] = $val_cheque->receipt_no;
                        $u[$h]['ref_no'] = $val_cheque->ref_no;
                        $u[$h]['branch'] = Branches::find($val_cheque->branch)->branch;
                        $u[$h]['no_items'] = $val_cheque->no_items;
                        $u[$h]['type'] = $val_cheque->type;
                        $u[$h]['up_id'] = $val_cheque->up_id;
                        
                        $user_dets = User::find($val_cheque->user);
                        $u[$h]['user'] = substr($user_dets->fname, 0, 1) . ". ". $user_dets->lname;
                        if($val_cheque->status ==0){
                            $u[$h]['status'] = "Deleted";
                        }else if($val_cheque->status ==1){
                            $u[$h]['status'] = "Complete";
                        }
                        
                        $u[$h]['type'] = $val_cheque->type;
                       
                     }
                }else{
                    $u = [];
                }
                
                
                
             }


           
            if($arr[0] ==1){


                if(empty($request->users) && empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $res_cash = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('type','cash tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $res_cash = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('ref_no',$arr[4])->where('type','cash tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $res_cash = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->where('type','cash tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $res_cash = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('receipt_no',$arr[5])->where('type','cash tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                }elseif(!empty($request->users) && empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $res_cash = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('type','cash tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $res_cash = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('ref_no',$arr[4])->where('type','cash tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $res_cash = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->where('type','cash tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $res_cash = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('receipt_no',$arr[5])->where('type','cash tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                }elseif(empty($request->users) && !empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $res_cash = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('branch',$request->branch)->where('type','cash tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $res_cash = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('branch',$request->branch)->where('ref_no',$arr[4])->where('type','cash tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $res_cash = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('branch',$request->branch)->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->where('type','cash tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $res_cash = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('branch',$request->branch)->where('receipt_no',$arr[5])->where('type','cash tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                }elseif(!empty($request->users) && !empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $res_cash = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('branch',$request->branch)->where('type','cash tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $res_cash = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('branch',$request->branch)->where('ref_no',$arr[4])->where('type','cash tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $res_cash = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('branch',$request->branch)->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->where('type','cash tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $res_cash = Transactions::select('up_id','type','status','branch','trans_time','ref_no','id','no_items','total_tax','total','receipt_no','user')->where('user',$request->users)->where('branch',$request->branch)->where('receipt_no',$arr[5])->where('type','cash tender')->whereBetween('trans_time',array($from,$to))->get();
                    
                    }
                }

                
                if($res_cash->count() > 0){
                    foreach($res_cash as $a=>$val_cash){
                        $d[$a]['id'] = $val_cash->id;
                        $d[$a]['trans_time'] = date("d/m/y h:i",$val_cash->trans_time);
                        $d[$a]['total'] = number_format($val_cash->total) . ".00";
                        $d[$a]['receipt_no'] = $val_cash->receipt_no;
                        $d[$a]['ref_no'] = $val_cash->ref_no;
                        $d[$a]['branch'] = Branches::find($val_cash->branch)->branch;
                        $d[$a]['no_items'] = $val_cash->no_items;
                        $d[$a]['type'] = $val_cash->type;
                        $d[$a]['up_id'] = $val_cash->up_id;
                        
                        $user_dets = User::find($val_cash->user);
                        $d[$a]['user'] = substr($user_dets->fname, 0, 1) . ". ". $user_dets->lname;
                        if($val_cash->status ==0){
                            $d[$a]['status'] = "Deleted";
                        }else if($val_cash->status ==1){
                            $d[$a]['status'] = "Complete";
                        }
                        
                        $d[$a]['type'] = $val_cash->type;
                     }
                }else{
                    $d = [];
                }
                

              
            }
             
         }
        
        
         echo json_encode(array_merge($d,$z,$y,$w,$v,$u));
         
     }

     public function alt_detailed_total_search_transactions(Request $request){
        
        $exp_dates = explode("to",$request->dates);
        $from = strtotime(str_replace(' ', '', $exp_dates[0]). " 06:00:00");
        $to = strtotime(str_replace(' ', '', $exp_dates[1]). " 23:59:59");
        


        $arr = array(
            $request->cash,
            $request->mpesa,
            $request->rtrn,
            $request->rtrn_tender,
            $request->ref_no,
            $request->receipt_no,
            $request->pdq,
            $request->cheque
        );

        $cash_ttl = 0;
        $cash_ttl_tax = 0;
        $cash_ttl_cost = 0;
        $cash_ttl_qty = 0;
        $cash_ttl_disc = 0;

        $mpesa_ttl = 0;
        $mpesa_ttl_tax = 0;
        $mpesa_ttl_cost = 0;
        $mpesa_ttl_qty = 0;
        $mpesa_ttl_disc = 0;

        $return_ttl = 0;
        $return_ttl_tax = 0;
        $return_ttl_cost = 0;
        $return_ttl_qty = 0;
        $return_ttl_disc = 0;

        $return_tender_ttl = 0;
        $return_tender_ttl_tax = 0;
        $return_tender_ttl_cost = 0;
        $return_tender_ttl_qty = 0;
        $return_tender_ttl_disc = 0;

        $pdq_ttl = 0;
        $pdq_ttl_tax = 0;
        $pdq_ttl_cost = 0;
        $pdq_ttl_qty = 0;
        $pdq_tender_ttl_disc = 0;

        $cheque_ttl = 0;
        $cheque_ttl_tax = 0;
        $cheque_ttl_cost = 0;
        $cheque_ttl_qty = 0;
        $cheque_tender_ttl_disc = 0;

        for($x = 0; $x < count($arr); $x++){

            if($arr[0] ==1){
                
                if(empty($request->users) && empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){
                        
                        $cash_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cash tender')->sum('total');
                        $cash_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cash tender')->sum('total_tax');
                        $cash_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cash tender')->sum('total_cost');
                        $cash_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cash tender')->sum('no_items');
                        $cash_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cash tender')->sum('discount');
                    
                    }elseif(!empty($arr[4]) && empty($arr[5])){
                        
                        $cash_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cash tender')->where('ref_no',$arr[4])->sum('total');
                        $cash_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cash tender')->where('ref_no',$arr[4])->sum('total_tax');
                        $cash_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cash tender')->where('ref_no',$arr[4])->sum('total_cost');
                        $cash_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cash tender')->where('ref_no',$arr[4])->sum('no_items');
                        $cash_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cash tender')->where('ref_no',$arr[4])->sum('discount');
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
                        
                        $cash_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cash tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total');
                        $cash_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cash tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_tax');
                        $cash_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cash tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_cost');
                        $cash_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cash tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('no_items');
                        $cash_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cash tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('discount');
                    
                    }elseif(empty($arr[4]) && !empty($arr[5])){
                        
                        $cash_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cash tender')->where('receipt_no',$arr[5])->sum('total');
                        $cash_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cash tender')->where('receipt_no',$arr[5])->sum('total_tax');
                        $cash_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cash tender')->where('receipt_no',$arr[5])->sum('total_cost');
                        $cash_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cash tender')->where('receipt_no',$arr[5])->sum('no_items');
                        $cash_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cash tender')->where('receipt_no',$arr[5])->sum('discount');
                    
                    }
                }elseif(!empty($request->users) && empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){
                        
                        $cash_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','cash tender')->sum('total');
                        $cash_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','cash tender')->sum('total_tax');
                        $cash_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','cash tender')->sum('total_cost');
                        $cash_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','cash tender')->sum('no_items');
                        $cash_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','cash tender')->sum('discount');
                    
                    }elseif(!empty($arr[4]) && empty($arr[5])){
                        
                        $cash_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','cash tender')->where('ref_no',$arr[4])->sum('total');
                        $cash_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','cash tender')->where('ref_no',$arr[4])->sum('total_tax');
                        $cash_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','cash tender')->where('ref_no',$arr[4])->sum('total_cost');
                        $cash_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','cash tender')->where('ref_no',$arr[4])->sum('no_items');
                        $cash_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','cash tender')->where('ref_no',$arr[4])->sum('discount');
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
                        
                        $cash_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','cash tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total');
                        $cash_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','cash tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_tax');
                        $cash_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','cash tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_cost');
                        $cash_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','cash tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('no_items');
                        $cash_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','cash tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('discount');
                    
                    }elseif(empty($arr[4]) && !empty($arr[5])){
                        
                        $cash_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','cash tender')->where('receipt_no',$arr[5])->sum('total');
                        $cash_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','cash tender')->where('receipt_no',$arr[5])->sum('total_tax');
                        $cash_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','cash tender')->where('receipt_no',$arr[5])->sum('total_cost');
                        $cash_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','cash tender')->where('receipt_no',$arr[5])->sum('no_items');
                        $cash_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','cash tender')->where('receipt_no',$arr[5])->sum('discount');
                    
                    }
                }elseif(empty($request->users) && !empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $cash_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cash tender')->sum('total');
                        $cash_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cash tender')->sum('total_tax');
                        $cash_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cash tender')->sum('total_cost');
                        $cash_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cash tender')->sum('no_items');
                        $cash_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cash tender')->sum('discount');
                    
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $cash_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cash tender')->where('ref_no',$arr[4])->sum('total');
                        $cash_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cash tender')->where('ref_no',$arr[4])->sum('total_tax');
                        $cash_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cash tender')->where('ref_no',$arr[4])->sum('total_cost');
                        $cash_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cash tender')->where('ref_no',$arr[4])->sum('no_items');
                        $cash_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cash tender')->where('ref_no',$arr[4])->sum('discount');
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $cash_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cash tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total');
                        $cash_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cash tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_tax');
                        $cash_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cash tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_cost');
                        $cash_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cash tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('no_items');
                        $cash_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cash tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('discount');
                    
                    }elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $cash_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cash tender')->where('receipt_no',$arr[5])->sum('total');
                        $cash_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cash tender')->where('receipt_no',$arr[5])->sum('total_tax');
                        $cash_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cash tender')->where('receipt_no',$arr[5])->sum('total_cost');
                        $cash_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cash tender')->where('receipt_no',$arr[5])->sum('no_items');
                        $cash_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cash tender')->where('receipt_no',$arr[5])->sum('discount');
                    
                    }
                }elseif(!empty($request->users) && !empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $cash_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cash tender')->sum('total');
                        $cash_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cash tender')->sum('total_tax');
                        $cash_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cash tender')->sum('total_cost');
                        $cash_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cash tender')->sum('no_items');
                        $cash_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cash tender')->sum('discount');
                    
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $cash_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cash tender')->where('ref_no',$arr[4])->sum('total');
                        $cash_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cash tender')->where('ref_no',$arr[4])->sum('total_tax');
                        $cash_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cash tender')->where('ref_no',$arr[4])->sum('total_cost');
                        $cash_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cash tender')->where('ref_no',$arr[4])->sum('no_items');
                        $cash_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cash tender')->where('ref_no',$arr[4])->sum('discount');
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $cash_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cash tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total');
                        $cash_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cash tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_tax');
                        $cash_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cash tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_cost');
                        $cash_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cash tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('no_items');
                        $cash_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cash tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('discount');
                    
                    }elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $cash_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cash tender')->where('receipt_no',$arr[5])->sum('total');
                        $cash_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cash tender')->where('receipt_no',$arr[5])->sum('total_tax');
                        $cash_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cash tender')->where('receipt_no',$arr[5])->sum('total_cost');
                        $cash_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cash tender')->where('receipt_no',$arr[5])->sum('no_items');
                        $cash_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cash tender')->where('receipt_no',$arr[5])->sum('discount');
                    
                    }
                }

               
            }
            if($arr[1] ==1){

                if(empty($request->users) && empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $mpesa_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('type','mpesa tender')->sum('total');
                        $mpesa_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('type','mpesa tender')->sum('total_tax');
                        $mpesa_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('type','mpesa tender')->sum('total_cost');
                        $mpesa_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','mpesa tender')->sum('no_items');
                        $mpesa_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('type','mpesa tender')->sum('discount');
                        
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $mpesa_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('type','mpesa tender')->where('ref_no',$arr[4])->sum('total');
                        $mpesa_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('type','mpesa tender')->where('ref_no',$arr[4])->sum('total_tax');
                        $mpesa_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('type','mpesa tender')->where('ref_no',$arr[4])->sum('total_cost');
                        $mpesa_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','mpesa tender')->where('ref_no',$arr[4])->sum('no_items');
                        $mpesa_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('type','mpesa tender')->where('ref_no',$arr[4])->sum('discount');
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $mpesa_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('type','mpesa tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total');
                        $mpesa_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('type','mpesa tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_tax');
                        $mpesa_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('type','mpesa tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_cost');
                        $mpesa_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','mpesa tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('no_items');
                        $mpesa_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('type','mpesa tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('discount');
                    
                    }
                    elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $mpesa_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('type','mpesa tender')->where('receipt_no',$arr[5])->sum('total');
                        $mpesa_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('type','mpesa tender')->where('receipt_no',$arr[5])->sum('total_tax');
                        $mpesa_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('type','mpesa tender')->where('receipt_no',$arr[5])->sum('total_cost');
                        $mpesa_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','mpesa tender')->where('receipt_no',$arr[5])->sum('no_items');
                        $mpesa_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('type','mpesa tender')->where('receipt_no',$arr[5])->sum('discount');
                    
                    }
                }elseif(!empty($request->users) && empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $mpesa_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','mpesa tender')->sum('total');
                        $mpesa_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','mpesa tender')->sum('total_tax');
                        $mpesa_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','mpesa tender')->sum('total_cost');
                        $mpesa_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','mpesa tender')->sum('no_items');
                        $mpesa_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','mpesa tender')->sum('discount');
                        
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $mpesa_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','mpesa tender')->where('ref_no',$arr[4])->sum('total');
                        $mpesa_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','mpesa tender')->where('ref_no',$arr[4])->sum('total_tax');
                        $mpesa_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','mpesa tender')->where('ref_no',$arr[4])->sum('total_cost');
                        $mpesa_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','mpesa tender')->where('ref_no',$arr[4])->sum('no_items');
                        $mpesa_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','mpesa tender')->where('ref_no',$arr[4])->sum('discount');
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $mpesa_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','mpesa tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total');
                        $mpesa_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','mpesa tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_tax');
                        $mpesa_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','mpesa tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_cost');
                        $mpesa_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','mpesa tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('no_items');
                        $mpesa_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','mpesa tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('discount');
                    
                    }elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $mpesa_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','mpesa tender')->where('receipt_no',$arr[5])->sum('total');
                        $mpesa_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','mpesa tender')->where('receipt_no',$arr[5])->sum('total_tax');
                        $mpesa_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','mpesa tender')->where('receipt_no',$arr[5])->sum('total_cost');
                        $mpesa_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','mpesa tender')->where('receipt_no',$arr[5])->sum('no_items');
                        $mpesa_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','mpesa tender')->where('receipt_no',$arr[5])->sum('discount');
                    
                    }
                }elseif(empty($request->users) && !empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $mpesa_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','mpesa tender')->sum('total');
                        $mpesa_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','mpesa tender')->sum('total_tax');
                        $mpesa_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','mpesa tender')->sum('total_cost');
                        $mpesa_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','mpesa tender')->sum('no_items');
                        $mpesa_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','mpesa tender')->sum('discount');
                        
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $mpesa_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','mpesa tender')->where('ref_no',$arr[4])->sum('total');
                        $mpesa_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','mpesa tender')->where('ref_no',$arr[4])->sum('total_tax');
                        $mpesa_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','mpesa tender')->where('ref_no',$arr[4])->sum('total_cost');
                        $mpesa_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','mpesa tender')->where('ref_no',$arr[4])->sum('no_items');
                        $mpesa_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','mpesa tender')->where('ref_no',$arr[4])->sum('discount');
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $mpesa_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','mpesa tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total');
                        $mpesa_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','mpesa tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_tax');
                        $mpesa_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','mpesa tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_cost');
                        $mpesa_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','mpesa tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('no_items');
                        $mpesa_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','mpesa tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('discount');
                    
                    }
                    elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $mpesa_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','mpesa tender')->where('receipt_no',$arr[5])->sum('total');
                        $mpesa_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','mpesa tender')->where('receipt_no',$arr[5])->sum('total_tax');
                        $mpesa_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','mpesa tender')->where('receipt_no',$arr[5])->sum('total_cost');
                        $mpesa_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','mpesa tender')->where('receipt_no',$arr[5])->sum('no_items');
                        $mpesa_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','mpesa tender')->where('receipt_no',$arr[5])->sum('discount');
                    
                    }
                }elseif(!empty($request->users) && !empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $mpesa_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','mpesa tender')->sum('total');
                        $mpesa_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','mpesa tender')->sum('total_tax');
                        $mpesa_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','mpesa tender')->sum('total_cost');
                        $mpesa_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','mpesa tender')->sum('no_items');
                        $mpesa_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','mpesa tender')->sum('discount');
                        
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $mpesa_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','mpesa tender')->where('ref_no',$arr[4])->sum('total');
                        $mpesa_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','mpesa tender')->where('ref_no',$arr[4])->sum('total_tax');
                        $mpesa_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','mpesa tender')->where('ref_no',$arr[4])->sum('total_cost');
                        $mpesa_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','mpesa tender')->where('ref_no',$arr[4])->sum('no_items');
                        $mpesa_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','mpesa tender')->where('ref_no',$arr[4])->sum('discount');
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $mpesa_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','mpesa tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total');
                        $mpesa_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','mpesa tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_tax');
                        $mpesa_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','mpesa tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_cost');
                        $mpesa_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','mpesa tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('no_items');
                        $mpesa_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','mpesa tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('discount');
                    
                    }elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $mpesa_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','mpesa tender')->where('receipt_no',$arr[5])->sum('total');
                        $mpesa_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','mpesa tender')->where('receipt_no',$arr[5])->sum('total_tax');
                        $mpesa_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','mpesa tender')->where('receipt_no',$arr[5])->sum('total_cost');
                        $mpesa_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','mpesa tender')->where('receipt_no',$arr[5])->sum('no_items');
                        $mpesa_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','mpesa tender')->where('receipt_no',$arr[5])->sum('discount');
                    
                    }
                }
                

            }
            if($arr[6] ==1){
                
                if(empty($request->users) && empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $pdq_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('type','pdq tender')->sum('total');
                        $pdq_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('type','pdq tender')->sum('total_tax');
                        $pdq_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('type','pdq tender')->sum('total_cost');
                        $pdq_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','pdq tender')->sum('no_items');
                        $pdq_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('type','pdq tender')->sum('discount');
                        
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $pdq_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('type','pdq tender')->where('ref_no',$arr[4])->sum('total');
                        $pdq_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('type','pdq tender')->where('ref_no',$arr[4])->sum('total_tax');
                        $pdq_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('type','pdq tender')->where('ref_no',$arr[4])->sum('total_cost');
                        $pdq_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','pdq tender')->where('ref_no',$arr[4])->sum('no_items');
                        $pdq_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('type','pdq tender')->where('ref_no',$arr[4])->sum('discount');
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $pdq_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('type','pdq tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total');
                        $pdq_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('type','pdq tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_tax');
                        $pdq_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('type','pdq tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_cost');
                        $pdq_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','pdq tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('no_items');
                        $pdq_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('type','pdq tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('discount');
                    
                    }elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $pdq_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('type','pdq tender')->where('receipt_no',$arr[5])->sum('total');
                        $pdq_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('type','pdq tender')->where('receipt_no',$arr[5])->sum('total_tax');
                        $pdq_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('type','pdq tender')->where('receipt_no',$arr[5])->sum('total_cost');
                        $pdq_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','pdq tender')->where('receipt_no',$arr[5])->sum('no_items');
                        $pdq_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('type','pdq tender')->where('receipt_no',$arr[5])->sum('discount');
                    
                    }
                }elseif(!empty($request->users) && empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $pdq_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','pdq tender')->sum('total');
                        $pdq_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','pdq tender')->sum('total_tax');
                        $pdq_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','pdq tender')->sum('total_cost');
                        $pdq_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','pdq tender')->sum('no_items');
                        $pdq_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','pdq tender')->sum('discount');
                        
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $pdq_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','pdq tender')->where('ref_no',$arr[4])->sum('total');
                        $pdq_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','pdq tender')->where('ref_no',$arr[4])->sum('total_tax');
                        $pdq_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','pdq tender')->where('ref_no',$arr[4])->sum('total_cost');
                        $pdq_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','pdq tender')->where('ref_no',$arr[4])->sum('no_items');
                        $pdq_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','pdq tender')->where('ref_no',$arr[4])->sum('discount');
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $pdq_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('type','pdq tender')->where('user',$request->users)->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total');
                        $pdq_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('type','pdq tender')->where('user',$request->users)->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_tax');
                        $pdq_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('type','pdq tender')->where('user',$request->users)->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_cost');
                        $pdq_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','pdq tender')->where('user',$request->users)->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('no_items');
                        $pdq_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','pdq tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('discount');
                    
                    }elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $pdq_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('type','pdq tender')->where('user',$request->users)->where('receipt_no',$arr[5])->sum('total');
                        $pdq_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('type','pdq tender')->where('user',$request->users)->where('receipt_no',$arr[5])->sum('total_tax');
                        $pdq_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('type','pdq tender')->where('user',$request->users)->where('receipt_no',$arr[5])->sum('total_cost');
                        $pdq_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','pdq tender')->where('user',$request->users)->where('receipt_no',$arr[5])->sum('no_items');
                        $pdq_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','pdq tender')->where('receipt_no',$arr[5])->sum('discount');
                    
                    }
                }elseif(empty($request->users) && !empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $pdq_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','pdq tender')->sum('total');
                        $pdq_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','pdq tender')->sum('total_tax');
                        $pdq_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','pdq tender')->sum('total_cost');
                        $pdq_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','pdq tender')->sum('no_items');
                        $pdq_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','pdq tender')->sum('discount');
                        
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $pdq_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','pdq tender')->where('ref_no',$arr[4])->sum('total');
                        $pdq_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','pdq tender')->where('ref_no',$arr[4])->sum('total_tax');
                        $pdq_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','pdq tender')->where('ref_no',$arr[4])->sum('total_cost');
                        $pdq_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','pdq tender')->where('ref_no',$arr[4])->sum('no_items');
                        $pdq_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','pdq tender')->where('ref_no',$arr[4])->sum('discount');
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $pdq_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','pdq tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total');
                        $pdq_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','pdq tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_tax');
                        $pdq_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','pdq tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_cost');
                        $pdq_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','pdq tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('no_items');
                        $pdq_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','pdq tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('discount');
                    
                    }elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $pdq_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','pdq tender')->where('receipt_no',$arr[5])->sum('total');
                        $pdq_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','pdq tender')->where('receipt_no',$arr[5])->sum('total_tax');
                        $pdq_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','pdq tender')->where('receipt_no',$arr[5])->sum('total_cost');
                        $pdq_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','pdq tender')->where('receipt_no',$arr[5])->sum('no_items');
                        $pdq_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','pdq tender')->where('receipt_no',$arr[5])->sum('discount');
                        
                    }

                }elseif(!empty($request->users) && !empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $pdq_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','pdq tender')->sum('total');
                        $pdq_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','pdq tender')->sum('total_tax');
                        $pdq_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','pdq tender')->sum('total_cost');
                        $pdq_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','pdq tender')->sum('no_items');
                        $pdq_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','pdq tender')->sum('discount');
                        
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $pdq_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','pdq tender')->where('ref_no',$arr[4])->sum('total');
                        $pdq_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','pdq tender')->where('ref_no',$arr[4])->sum('total_tax');
                        $pdq_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','pdq tender')->where('ref_no',$arr[4])->sum('total_cost');
                        $pdq_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','pdq tender')->where('ref_no',$arr[4])->sum('no_items');
                        $pdq_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','pdq tender')->where('ref_no',$arr[4])->sum('discount');
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $pdq_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','pdq tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total');
                        $pdq_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','pdq tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_tax');
                        $pdq_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','pdq tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_cost');
                        $pdq_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','pdq tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('no_items');
                        $pdq_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','pdq tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('discount');
                    
                    }elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $pdq_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','pdq tender')->where('receipt_no',$arr[5])->sum('total');
                        $pdq_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','pdq tender')->where('receipt_no',$arr[5])->sum('total_tax');
                        $pdq_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','pdq tender')->where('receipt_no',$arr[5])->sum('total_cost');
                        $pdq_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','pdq tender')->where('receipt_no',$arr[5])->sum('no_items');
                        $pdq_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','pdq tender')->where('receipt_no',$arr[5])->sum('discount');
                    
                    }
                }

                

            }
            if($arr[7] ==1){
                
                if(empty($request->users) && empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $cheque_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cheque tender')->sum('total');
                        $cheque_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cheque tender')->sum('total_tax');
                        $cheque_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cheque tender')->sum('total_cost');
                        $cheque_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cheque tender')->sum('no_items');
                        $cheque_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cheque tender')->sum('discount');
                        
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $cheque_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cheque tender')->where('ref_no',$arr[4])->sum('total');
                        $cheque_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cheque tender')->where('ref_no',$arr[4])->sum('total_tax');
                        $cheque_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cheque tender')->where('ref_no',$arr[4])->sum('total_cost');
                        $cheque_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cheque tender')->where('ref_no',$arr[4])->sum('no_items');
                        $cheque_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cheque tender')->where('ref_no',$arr[4])->sum('discount');
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
                        
                        $cheque_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cheque tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total');
                        $cheque_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cheque tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_tax');
                        $cheque_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cheque tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_cost');
                        $cheque_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cheque tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('no_items');
                        $cheque_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cheque tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('discount');
                    
                    }elseif(empty($arr[4]) && !empty($arr[5])){
                        
                        $cheque_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cheque tender')->where('receipt_no',$arr[5])->sum('total');
                        $cheque_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cheque tender')->where('receipt_no',$arr[5])->sum('total_tax');
                        $cheque_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cheque tender')->where('receipt_no',$arr[5])->sum('total_cost');
                        $cheque_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cheque tender')->where('receipt_no',$arr[5])->sum('no_items');
                        $cheque_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cheque tender')->where('receipt_no',$arr[5])->sum('discount');
                    
                    }
                }elseif(!empty($request->users) && empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $cheque_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','cheque tender')->sum('total');
                        $cheque_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','cheque tender')->sum('total_tax');
                        $cheque_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','cheque tender')->sum('total_cost');
                        $cheque_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','cheque tender')->sum('no_items');
                        $cheque_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','cheque tender')->sum('discount');
                        
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $cheque_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','cheque tender')->where('ref_no',$arr[4])->sum('total');
                        $cheque_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','cheque tender')->where('ref_no',$arr[4])->sum('total_tax');
                        $cheque_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','cheque tender')->where('ref_no',$arr[4])->sum('total_cost');
                        $cheque_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','cheque tender')->where('ref_no',$arr[4])->sum('no_items');
                        $cheque_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','cheque tender')->where('ref_no',$arr[4])->sum('discount');
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
                        
                        $cheque_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cheque tender')->where('user',$request->users)->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total');
                        $cheque_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cheque tender')->where('user',$request->users)->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_tax');
                        $cheque_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cheque tender')->where('user',$request->users)->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_cost');
                        $cheque_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cheque tender')->where('user',$request->users)->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('no_items');
                        $cheque_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','cheque tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('discount');
                    
                    }elseif(empty($arr[4]) && !empty($arr[5])){
                        
                        $cheque_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cheque tender')->where('user',$request->users)->where('receipt_no',$arr[5])->sum('total');
                        $cheque_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cheque tender')->where('user',$request->users)->where('receipt_no',$arr[5])->sum('total_tax');
                        $cheque_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cheque tender')->where('user',$request->users)->where('receipt_no',$arr[5])->sum('total_cost');
                        $cheque_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','cheque tender')->where('user',$request->users)->where('receipt_no',$arr[5])->sum('no_items');
                        $cheque_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','cheque tender')->where('receipt_no',$arr[5])->sum('discount');
                    
                    }
                }elseif(empty($request->users) && !empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        
                        $cheque_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cheque tender')->sum('total');
                        $cheque_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cheque tender')->sum('total_tax');
                        $cheque_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cheque tender')->sum('total_cost');
                        $cheque_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cheque tender')->sum('no_items');
                        $cheque_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cheque tender')->sum('discount');
                        
                    }elseif(!empty($arr[4]) && empty($arr[5])){
                        
                        $cheque_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cheque tender')->where('ref_no',$arr[4])->sum('total');
                        $cheque_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cheque tender')->where('ref_no',$arr[4])->sum('total_tax');
                        $cheque_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cheque tender')->where('ref_no',$arr[4])->sum('total_cost');
                        $cheque_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cheque tender')->where('ref_no',$arr[4])->sum('no_items');
                        $cheque_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cheque tender')->where('ref_no',$arr[4])->sum('discount');
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
                        
                        $cheque_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cheque tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total');
                        $cheque_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cheque tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_tax');
                        $cheque_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cheque tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_cost');
                        $cheque_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cheque tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('no_items');
                        $cheque_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cheque tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('discount');
                    
                    }elseif(empty($arr[4]) && !empty($arr[5])){
                        
                        $cheque_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cheque tender')->where('receipt_no',$arr[5])->sum('total');
                        $cheque_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cheque tender')->where('receipt_no',$arr[5])->sum('total_tax');
                        $cheque_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cheque tender')->where('receipt_no',$arr[5])->sum('total_cost');
                        $cheque_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cheque tender')->where('receipt_no',$arr[5])->sum('no_items');
                        $cheque_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','cheque tender')->where('receipt_no',$arr[5])->sum('discount');
                    
                    }
                }elseif(!empty($request->users) && !empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){
                       
                        $cheque_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cheque tender')->sum('total');
                        $cheque_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cheque tender')->sum('total_tax');
                        $cheque_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cheque tender')->sum('total_cost');
                        $cheque_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cheque tender')->sum('no_items');
                        $cheque_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cheque tender')->sum('discount');
                        
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $cheque_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cheque tender')->where('ref_no',$arr[4])->sum('total');
                        $cheque_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cheque tender')->where('ref_no',$arr[4])->sum('total_tax');
                        $cheque_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cheque tender')->where('ref_no',$arr[4])->sum('total_cost');
                        $cheque_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cheque tender')->where('ref_no',$arr[4])->sum('no_items');
                        $cheque_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cheque tender')->where('ref_no',$arr[4])->sum('discount');
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
                         
                        $cheque_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cheque tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total');
                        $cheque_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cheque tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_tax');
                        $cheque_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cheque tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_cost');
                        $cheque_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cheque tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('no_items');
                        $cheque_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cheque tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('discount');
                    
                    }elseif(empty($arr[4]) && !empty($arr[5])){
                         
                        $cheque_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cheque tender')->where('receipt_no',$arr[5])->sum('total');
                        $cheque_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cheque tender')->where('receipt_no',$arr[5])->sum('total_tax');
                        $cheque_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cheque tender')->where('receipt_no',$arr[5])->sum('total_cost');
                        $cheque_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cheque tender')->where('receipt_no',$arr[5])->sum('no_items');
                        $cheque_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','cheque tender')->where('receipt_no',$arr[5])->sum('discount');
                    
                    }
                }

                

            }
            if($arr[2] ==1){

                if(empty($request->users) && empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $return_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return')->sum('total');
                        $return_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return')->sum('total_tax');
                        $return_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return')->sum('total_cost');
                        $return_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return')->sum('no_items');
                        $return_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return')->sum('discount');
                        
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $return_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return')->where('ref_no',$arr[4])->sum('total');
                        $return_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return')->where('ref_no',$arr[4])->sum('total_tax');
                        $return_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return')->where('ref_no',$arr[4])->sum('total_cost');
                        $return_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return')->where('ref_no',$arr[4])->sum('no_items');
                        $return_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return')->where('ref_no',$arr[4])->sum('no_items');
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $return_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total');
                        $return_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_tax');
                        $return_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_cost');
                        $return_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('no_items');
                        $return_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('no_items');
                    
                    }elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $return_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return')->where('receipt_no',$arr[5])->sum('total');
                        $return_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return')->where('receipt_no',$arr[5])->sum('total_tax');
                        $return_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return')->where('receipt_no',$arr[5])->sum('total_cost');
                        $return_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return')->where('receipt_no',$arr[5])->sum('no_items');
                        $return_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return')->where('receipt_no',$arr[5])->sum('no_items');
                    
                    }
                }elseif(!empty($request->users) && empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $return_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->user)->where('type','return')->sum('total');
                        $return_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->user)->where('type','return')->sum('total_tax');
                        $return_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->user)->where('type','return')->sum('total_cost');
                        $return_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->user)->where('type','return')->sum('no_items');
                        $return_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->user)->where('type','return')->sum('discount');
                        
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $return_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->user)->where('type','return')->where('ref_no',$arr[4])->sum('total');
                        $return_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->user)->where('type','return')->where('ref_no',$arr[4])->sum('total_tax');
                        $return_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->user)->where('type','return')->where('ref_no',$arr[4])->sum('total_cost');
                        $return_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->user)->where('type','return')->where('ref_no',$arr[4])->sum('no_items');
                        $return_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->user)->where('type','return')->where('ref_no',$arr[4])->sum('no_items');
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $return_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->user)->where('type','return')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total');
                        $return_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->user)->where('type','return')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_tax');
                        $return_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->user)->where('type','return')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_cost');
                        $return_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->user)->where('type','return')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('no_items');
                        $return_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->user)->where('type','return')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('no_items');
                    
                    }elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $return_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->user)->where('type','return')->where('receipt_no',$arr[5])->sum('total');
                        $return_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->user)->where('type','return')->where('receipt_no',$arr[5])->sum('total_tax');
                        $return_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->user)->where('type','return')->where('receipt_no',$arr[5])->sum('total_cost');
                        $return_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->user)->where('type','return')->where('receipt_no',$arr[5])->sum('no_items');
                        $return_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->user)->where('type','return')->where('receipt_no',$arr[5])->sum('no_items');
                    
                    }
                }elseif(empty($request->users) && !empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $return_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return')->sum('total');
                        $return_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return')->sum('total_tax');
                        $return_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return')->sum('total_cost');
                        $return_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return')->sum('no_items');
                        $return_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return')->sum('discount');
                        
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $return_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return')->where('ref_no',$arr[4])->sum('total');
                        $return_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return')->where('ref_no',$arr[4])->sum('total_tax');
                        $return_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return')->where('ref_no',$arr[4])->sum('total_cost');
                        $return_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return')->where('ref_no',$arr[4])->sum('no_items');
                        $return_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return')->where('ref_no',$arr[4])->sum('no_items');
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $return_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total');
                        $return_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_tax');
                        $return_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_cost');
                        $return_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('no_items');
                        $return_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('no_items');
                    
                    }elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $return_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return')->where('receipt_no',$arr[5])->sum('total');
                        $return_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return')->where('receipt_no',$arr[5])->sum('total_tax');
                        $return_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return')->where('receipt_no',$arr[5])->sum('total_cost');
                        $return_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return')->where('receipt_no',$arr[5])->sum('no_items');
                        $return_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return')->where('receipt_no',$arr[5])->sum('no_items');
                    
                    }
                }elseif(!empty($request->users) && !empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $return_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','return')->sum('total');
                        $return_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','return')->sum('total_tax');
                        $return_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','return')->sum('total_cost');
                        $return_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','return')->sum('no_items');
                        $return_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','return')->sum('discount');
                        
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $return_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','return')->where('ref_no',$arr[4])->sum('total');
                        $return_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','return')->where('ref_no',$arr[4])->sum('total_tax');
                        $return_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','return')->where('ref_no',$arr[4])->sum('total_cost');
                        $return_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','return')->where('ref_no',$arr[4])->sum('no_items');
                        $return_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','return')->where('ref_no',$arr[4])->sum('no_items');
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $return_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','return')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total');
                        $return_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','return')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_tax');
                        $return_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','return')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_cost');
                        $return_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','return')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('no_items');
                        $return_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','return')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('no_items');
                    
                    }elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $return_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','return')->where('receipt_no',$arr[5])->sum('total');
                        $return_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','return')->where('receipt_no',$arr[5])->sum('total_tax');
                        $return_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','return')->where('receipt_no',$arr[5])->sum('total_cost');
                        $return_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','return')->where('receipt_no',$arr[5])->sum('no_items');
                        $return_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('branch',$request->branch)->where('type','return')->where('receipt_no',$arr[5])->sum('no_items');
                    
                    }
                }
             

            }
            if($arr[3] ==1){

                if(empty($request->users) && empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $return_tender_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return tender')->sum('total');
                        $return_tender_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return tender')->sum('total_tax');
                        $return_tender_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return tender')->sum('total_cost');
                        $return_tender_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return tender')->sum('no_items');
                        $return_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return tender')->sum('discount');
                        
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $return_tender_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return tender')->where('ref_no',$arr[4])->sum('total');
                        $return_tender_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return tender')->where('ref_no',$arr[4])->sum('total_tax');
                        $return_tender_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return tender')->where('ref_no',$arr[4])->sum('total_cost');
                        $return_tender_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return tender')->where('ref_no',$arr[4])->sum('no_items');
                        $return_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return tender')->where('ref_no',$arr[4])->sum('no_items');
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $return_tender_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total');
                        $return_tender_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_tax');
                        $return_tender_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_cost');
                        $return_tender_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('no_items');
                        $return_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('no_items');
                    
                    }elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $return_tender_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return tender')->where('receipt_no',$arr[5])->sum('total');
                        $return_tender_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return tender')->where('receipt_no',$arr[5])->sum('total_tax');
                        $return_tender_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return tender')->where('receipt_no',$arr[5])->sum('total_cost');
                        $return_tender_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return tender')->where('receipt_no',$arr[5])->sum('no_items');
                        $return_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('type','return tender')->where('receipt_no',$arr[5])->sum('no_items');
                    
                    }
                }elseif(!empty($request->users) && empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $return_tender_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','return tender')->sum('total');
                        $return_tender_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','return tender')->sum('total_tax');
                        $return_tender_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','return tender')->sum('total_cost');
                        $return_tender_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','return tender')->sum('no_items');
                        $return_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','return tender')->sum('discount');
                        
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $return_tender_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','return tender')->where('ref_no',$arr[4])->sum('total');
                        $return_tender_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','return tender')->where('ref_no',$arr[4])->sum('total_tax');
                        $return_tender_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','return tender')->where('ref_no',$arr[4])->sum('total_cost');
                        $return_tender_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','return tender')->where('ref_no',$arr[4])->sum('no_items');
                        $return_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','return tender')->where('ref_no',$arr[4])->sum('no_items');
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $return_tender_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','return tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total');
                        $return_tender_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','return tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_tax');
                        $return_tender_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','return tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_cost');
                        $return_tender_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','return tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('no_items');
                        $return_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','return tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('no_items');
                    
                    }elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $return_tender_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','return tender')->where('receipt_no',$arr[5])->sum('total');
                        $return_tender_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','return tender')->where('receipt_no',$arr[5])->sum('total_tax');
                        $return_tender_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','return tender')->where('receipt_no',$arr[5])->sum('total_cost');
                        $return_tender_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','return tender')->where('receipt_no',$arr[5])->sum('no_items');
                        $return_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('user',$request->users)->where('type','return tender')->where('receipt_no',$arr[5])->sum('no_items');
                    
                    }
                }elseif(empty($request->users) && !empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $return_tender_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return tender')->sum('total');
                        $return_tender_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return tender')->sum('total_tax');
                        $return_tender_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return tender')->sum('total_cost');
                        $return_tender_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return tender')->sum('no_items');
                        $return_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return tender')->sum('discount');
                        
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $return_tender_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return tender')->where('ref_no',$arr[4])->sum('total');
                        $return_tender_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return tender')->where('ref_no',$arr[4])->sum('total_tax');
                        $return_tender_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return tender')->where('ref_no',$arr[4])->sum('total_cost');
                        $return_tender_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return tender')->where('ref_no',$arr[4])->sum('no_items');
                        $return_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return tender')->where('ref_no',$arr[4])->sum('no_items');
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $return_tender_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total');
                        $return_tender_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_tax');
                        $return_tender_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_cost');
                        $return_tender_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('no_items');
                        $return_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('no_items');
                    
                    }elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $return_tender_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return tender')->where('receipt_no',$arr[5])->sum('total');
                        $return_tender_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return tender')->where('receipt_no',$arr[5])->sum('total_tax');
                        $return_tender_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return tender')->where('receipt_no',$arr[5])->sum('total_cost');
                        $return_tender_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return tender')->where('receipt_no',$arr[5])->sum('no_items');
                        $return_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('type','return tender')->where('receipt_no',$arr[5])->sum('no_items');
                    
                    }
                }elseif(!empty($request->users) && !empty($request->branch)){
                    if(empty($arr[4]) && empty($arr[5])){

                        $return_tender_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('user',$request->user)->where('type','return tender')->sum('total');
                        $return_tender_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('user',$request->user)->where('type','return tender')->sum('total_tax');
                        $return_tender_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('user',$request->user)->where('type','return tender')->sum('total_cost');
                        $return_tender_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('user',$request->user)->where('type','return tender')->sum('no_items');
                        $return_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('user',$request->user)->where('type','return tender')->sum('discount');
                        
                    }elseif(!empty($arr[4]) && empty($arr[5])){
    
                        $return_tender_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('user',$request->user)->where('type','return tender')->where('ref_no',$arr[4])->sum('total');
                        $return_tender_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('user',$request->user)->where('type','return tender')->where('ref_no',$arr[4])->sum('total_tax');
                        $return_tender_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('user',$request->user)->where('type','return tender')->where('ref_no',$arr[4])->sum('total_cost');
                        $return_tender_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('user',$request->user)->where('type','return tender')->where('ref_no',$arr[4])->sum('no_items');
                        $return_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('user',$request->user)->where('type','return tender')->where('ref_no',$arr[4])->sum('no_items');
                    
                    }elseif(!empty($arr[4]) && !empty($arr[5])){
    
                        $return_tender_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('user',$request->user)->where('type','return tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total');
                        $return_tender_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('user',$request->user)->where('type','return tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_tax');
                        $return_tender_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('user',$request->user)->where('type','return tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('total_cost');
                        $return_tender_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('user',$request->user)->where('type','return tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('no_items');
                        $return_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('user',$request->user)->where('type','return tender')->where('ref_no',$arr[4])->where('receipt_no',$arr[5])->sum('no_items');
                    
                    }elseif(empty($arr[4]) && !empty($arr[5])){
    
                        $return_tender_ttl = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('user',$request->user)->where('type','return tender')->where('receipt_no',$arr[5])->sum('total');
                        $return_tender_ttl_tax = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('user',$request->user)->where('type','return tender')->where('receipt_no',$arr[5])->sum('total_tax');
                        $return_tender_ttl_cost = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('user',$request->user)->where('type','return tender')->where('receipt_no',$arr[5])->sum('total_cost');
                        $return_tender_ttl_qty = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('user',$request->user)->where('type','return tender')->where('receipt_no',$arr[5])->sum('no_items');
                        $return_tender_ttl_disc = Transactions::whereBetween('trans_time',array($from,$to))->where('branch',$request->branch)->where('user',$request->user)->where('type','return tender')->where('receipt_no',$arr[5])->sum('no_items');
                    
                    }

                }

                
                
            }
            

        }
       
        $total_cost = $cheque_ttl_cost + $pdq_ttl_cost + $cash_ttl_cost + $mpesa_ttl_cost + $return_ttl_cost + $return_tender_ttl_cost;  
        $total = $cheque_ttl + $pdq_ttl + $cash_ttl + $mpesa_ttl + $return_ttl + $return_tender_ttl;  
        $tax = $cheque_ttl_tax + $pdq_ttl_tax + $cash_ttl_tax + $mpesa_ttl_tax + $return_ttl_tax + $return_tender_ttl_tax;  
        $qty = $cheque_ttl_qty + $pdq_ttl_qty + $cash_ttl_qty + $mpesa_ttl_qty + $return_ttl_qty + $return_tender_ttl_qty;  
        $total_discount = $cheque_tender_ttl_disc + $pdq_tender_ttl_disc + $cash_ttl_disc + $mpesa_ttl_disc + $return_ttl_disc + $return_tender_ttl_disc;  
        
        $gross = $total - $total_cost;
        $net = $total - $total_cost - $tax;
        
        return json_encode(array('total_cost'=>number_format($total_cost) . '.00','net'=>number_format($net) . '.00','gross'=>number_format($gross) . '.00','total'=>number_format($total) . '.00','tax'=>$tax . '.00','total_discount'=>$total_discount . '.00','qty'=>$qty));
        
    }

    public function detailed_total_search_transactions(Request $request){

        $exp_dates = explode("to",$request->dates);
        $from = strtotime(str_replace(' ', '', $exp_dates[0]). " 06:00:00");
        $to = strtotime(str_replace(' ', '', $exp_dates[1]). " 23:59:59");
        


        $arr = array(
            $request->cash,
            $request->mpesa,
            $request->rtrn,
            $request->rtrn_tender,
            $request->ref_no,
            $request->receipt_no
        );
       
        for($x = 0; $x < count($arr); $x++){
            $str = "SELECT SUM(total) AS total, SUM(total_tax) AS total_tax, SUM(no_items) AS no_items, SUM(total_cost) AS total_cost  FROM transactions WHERE (trans_time BETWEEN " . $from . " AND " . $to . ") ";
            
            if($arr[0] ==1 || $arr[1] ==1 || $arr[2] ==1 || $arr[3] ==1){
                $str .= " AND ";
            }

            if($arr[0] ==1){
                $str .= " type='cash tender' ";
            }
            if($arr[1] ==1){
                if($x > 1 && $arr[0] ==1){
                    $str .= " OR ";
                }
                $str .= " type='mpesa tender' ";
            }
            if($arr[2] ==1){
                if($x > 2 || $arr[1] ==1){
                    $str .= " OR ";
                }
                $str .= " type='return' ";
            }
            if($arr[3] ==1){
                if($x > 3 || $arr[2] ==1){
                    $str .= " OR ";
                }
                $str .= " type='return tender' ";
            }
            if(!empty($arr[4])){
                if($x > 4){
                    $str .= " AND ";
                }
                $str .= " ref_no='" . $request->ref_no . "' ";
            }
            if(!empty($arr[5])){
                if($x == 5){
                    $str .= " AND ";
                }
                $str .= " receipt_no='" . $request->receipt_no . "' ";
            }

        }

    }

    public function new_branch(Request $request){
        $chk = Branches::select('id')->where('branch',$request->branch)->get();
        if($chk->count() ==0){
            $br = new Branches();
            $br->branch = $request->branch;
            $br->status = 1;
            $br->save();
            return json_encode(array('status'=>1));
        }else{
            return json_encode(array('status'=>0));
        }
        
    }


    public function get_branch_rprt_data(){
        $branch = Branches::select('id','branch','status')->get();
        if($branch->count() > 0){
            foreach($branch as $i=>$val){
                $d[$i]['id'] = $val->id;
                $d[$i]['branch'] = $val->branch;
                if($val->status==1){
                    $d[$i]['status'] = "Active";
                }else{
                    $d[$i]['status'] = "InActive";
                }
                
            }
            return json_encode($d);
        }else{
            return json_encode(array('status'=>0));
        }
    }

    public function get_active_users(){
        $users = User::select('id','fname','lname')->where('status',1)->get();
        
        return json_encode($users);
    }

    public function get_active_branch_rprt_data(){
        $branch = Branches::select('id','branch')->where('status',1)->get();
        
        return json_encode($branch);
        
    }

    public function get_user_priviledges(Request $request){
        $u = User::find($request->uid);
        return json_encode($u);
       
    }

    public function get_admin_user_priviledges(Request $request){
        $u = User::find($request->session()->get('uid'));
        return json_encode($u);
    }

    public function new_customer(Request $request){
        $c = new Customers();
        $c->f_name = strtolower($request->fname);
        $c->s_name = strtolower($request->lname);
        $c->phone = $request->phone;
        $c->email = $request->email;
        $c->c_type = $request->type;
        $c->member_no = $request->member_no;
        $c->org = strtolower($request->org);
        $c->status = 1;
        $c->save();

        return json_encode(array('status'=>1));
       
    }

    public function customers_reports(Request $request){
        $res = Customers::select('id','f_name','s_name','phone','email','c_type','org','member_no')->get();
        return json_encode($res);
    }

    public function search_customers_reports(Request $request){
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
        $res = Customers::select('id','f_name','s_name','phone','email','c_type','org','member_no')->where('id',$cust_id)->get();
        return json_encode($res);
    }

    public function new_tourn(Request $request){
        
        $chk = Tournaments::select('id')->where('title',$request->tourn_title)->get();
        if($chk->count() == 0){
            $t = new Tournaments();
            $t->title = $request->tourn_title;
            $t->t_date = $request->tourn_date;
            $t->status = 1;
            $t->save();
            return json_encode(array('status'=>1));
        }else{
            return json_encode(array('status'=>0));
        }
        

    }

    public function tourn_reports(Request $request){
        $res = Tournaments::select('id','title','t_date','status')->get();
        foreach($res as $i=>$val){
            $d[$i]['id'] = $val->id;
            $d[$i]['title'] = $val->title;
            $d[$i]['t_date'] = $val->t_date;
            if($val->status ==1){
                $d[$i]['status'] = "Active";
            }else{
                $d[$i]['status'] = "InActive";
            }
            
        }

        return json_encode($d);
    }

    public function fetch_tournaments_data(Request $request){
        $res = Tournaments::select('id','title','t_date')->where('status',1)->get();
        foreach($res as $i=>$val){
            $d[$i]['id'] = $val->id;
            $d[$i]['title'] = $val->title;
            $d[$i]['t_date'] = $val->t_date;
            
            
        }

        return json_encode($d);
    }

    public function fetch_customer_data(Request $request){
        $res = Customers::select('id','f_name','s_name','phone','email','c_type','org')->where('status',1)->get();
        return json_encode($res);
    }

    public function fetch_held_data(Request $request){
        $res = Transactions::select('id','cash','total_tax','total','total_tax','no_items','trans_time','customer','total_cost','type','ref_no')->where('status','0')->where('type','Individual')->orWhere('type','=','Corporate')->get();
        
        foreach($res as $i=>$val){

            $d[$i]['id'] = $val->id;
            $d[$i]['cash'] = $val->cash;
            $d[$i]['total_tax'] = $val->total_tax;
            $d[$i]['total'] = $val->total;
            $d[$i]['total_tax'] = $val->total_tax;
            $d[$i]['no_items'] = $val->no_items;
            $d[$i]['trans_time'] = date("d-m-Y H:s",$val->trans_time);
            $d[$i]['total_cost'] = $val->total_cost;
            $d[$i]['type'] = $val->type;
            $d[$i]['customer'] = Customers::find($val->customer)->f_name . ' ' . Customers::find($val->customer)->s_name;
            /*
            if($val->type =='Corporate'){
                $d[$i]['customer'] = Customers::find($val->customer)->f_name . ' ' . Customers::find($val->customer)->s_name;
            }elseif($val->type =='tournament'){
                $d[$i]['customer'] = Tournaments::find($val->customer)->title;
            }
            */
            
            $d[$i]['ref_no'] = $val->ref_no;
            
        }

        return json_encode($d);
    }

    public function sel_curr_branch(Request $request){
        $chk = Branches::select('id')->where('curr',1)->limit(1)->first();
        $curr_cancel = Branches::find($chk->id);
        $curr_cancel ->curr = 0;
        $curr_cancel->save();

        $br = Branches::find($request->branch);
        $br->curr = 1;
        $br->save();
        return json_encode(array('status'=>1,'branch'=>$br->branch));
    }

    public function get_current_branch(){
        $br = Branches::select('branch')->where('curr',1)->limit(1)->first();
        return json_encode(array('branch'=>$br->branch));
    }

    public function new_club(Request $request){
        //club:club,loc:loc,email:email,phone:phone
        $c_chk = Clubs::select('id')->where('club',$request->club)->get();
        if($c_chk->count() == 0){
            $c = new Clubs();
            $c->club = strtolower($request->club);
            $c->location = $request->loc;
            $c->email = $request->email;
            $c->phone = $request->phone;
            $c->save();

            return json_encode(array('status'=>1));
        }else{
            return json_encode(array('status'=>0));
        }
        
    }

    public function club_reports(){
        $clbs = Clubs::select('id','club','location','email','phone','status')->get();
        foreach($clbs as $i=>$val){
            $d[$i]['id'] = $val->id;
            $d[$i]['club'] = $val->club;
            $d[$i]['location'] = $val->location;
            $d[$i]['email'] = $val->email;
            $d[$i]['phone'] = $val->phone;
            if($val->status ==1){
                $d[$i]['status'] = "Active";
            }else{
                $d[$i]['status'] = "InActive";
            }
            
        }
        return json_encode($d);
    }

    public function new_event(Request $request){
        //event:event,e_date:e_date,club:club,sponsor:sponsore_type
        $chk_ev = Events::select('id')->where('event',$request->event)->get();
        if($chk_ev->count() ==0){
            $ev = new Events();
            $ev->event = strtolower($request->event);
            $ev->e_date = $request->e_date;
            $ev->club = $request->club;
            $ev->sponsor = $request->sponsor;
            $ev->type = $request->e_type;
            $ev->user = $request->session()->get('uid');
            $br = Branches::where('curr',1)->select('id')->orderBy('id','DESC')->limit(1)->first();
            $ev->branch = $br->id;
            $ev->save();
            return json_encode(array('status'=>1));
        }else{
            return json_encode(array('status'=>0));
        }
        
        
    }

    public function get_active_clubs(Request $request){
        $clbs = Clubs::select('id','club')->where('status',1)->get();
        
        return json_encode($clbs);
    }

    public function events_reports(Request $request){
        $evnts = Events::select('id','status','event','e_date','club','sponsor','type','status','branch','user')->orderBy('id','desc')->get();
        
        if($evnts->count()){

            foreach($evnts as $i=>$val){
                $d[$i]['id'] = $val->id;
                $d[$i]['event'] = $val->event;
                $d[$i]['e_date'] = $val->e_date;
                $d[$i]['club'] = ($val->club) ? Clubs::find($val->club)->club : '';
                $d[$i]['sponsor'] = ($val->sponsor) ? Customers::find($val->sponsor)->org : '';
                $d[$i]['type'] = $val->type;
                $d[$i]['status'] = $val->status;
                
                $d[$i]['branch'] = Branches::find($val->branch)->branch;
                $d[$i]['user'] = User::find($val->user)->lname;
            }

        }else{
            $d = [];
        }
        
        return json_encode($d);
    }

    public function search_events_reports(Request $request){

        $evnts = Events::select('id','status','event','e_date','club','sponsor','type','status','branch','user')->where('event', 'like', '%' . $request->event . '%')->get();
        
        if($evnts->count()){

            foreach($evnts as $i=>$val){
                $d[$i]['id'] = $val->id;
                $d[$i]['event'] = $val->event;
                $d[$i]['e_date'] = $val->e_date;
                $d[$i]['club'] = ($val->club) ? Clubs::find($val->club)->club : '';
                $d[$i]['sponsor'] = ($val->sponsor) ? Customers::find($val->sponsor)->org : '';
                $d[$i]['type'] = $val->type;
                $d[$i]['status'] = $val->status;
                $d[$i]['branch'] = Branches::find($val->branch)->branch;
                $d[$i]['user'] = User::find($val->user)->lname;
            }
            
        }else{

            $d = [];

        }
        
        return json_encode($d);
    }

    public function archive_events(Request $request){
       
        $up_eve = Events::find($request->id);
        $up_eve->status = 0;
        $up_eve->save();
    }

    public function get_active_sponsors(Request $request){
        $sps = Customers::select('id','org')->where('c_type','Corporate')->where('status',1)->get();
        return json_encode($sps);
    }

    public function get_active_events(Request $request){
        $eve = Events::select('id','event')->where('status',1)->get();
        return json_encode($eve);
    }

    public function get_all_events(Request $request){
        $events = Events::select('id','event')->get();
        if($events->count() > 0){

            foreach($events as $val){
                    $d[] = $val->event;
            }

            return(json_encode($d));

        }else{
            return json_encode(array('status'=>0));
        }
    }

    public function new_check_customer(Request $request){
        $customer = $request->customer;
        
        $cust_arr = explode(" ",$customer);
        $cust_arr2 = explode("-",$customer);
        
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

        if(empty($cust_id)){
            return json_encode(array('status'=>0));
        }else{
            return json_encode(array('customer'=>$cust_id,'fname'=>$cust_arr[0],'lname'=>$cust_arr[1],'org'=>$cust_arr2[1]));
        }
    }

    public function accounts_txns(Request $request){

        $customer = $request->customer;

        $br = Branches::select('id')->where('curr',1)->limit(1)->first();
        
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
       
        //$acc = DB::select("SELECT SUM(qty) AS ttl_qty,id, item, event, qty, customer, status, branch FROM accounts WHERE customer='$cust_id' AND status='1' GROUP BY item");

        $acc = Accounts::select('qty','id','item','event','qty','customer','status','branch')->where('customer',$cust_id)->where('status',1)->get(); 
        
   
        if(count($acc) > 0){
            $no = 1;
            foreach($acc as $i=>$val){

                $ttl_qty = Accounts::where('customer',$cust_id)->where('event',$val->event)->where('item',$val->item)->sum('qty');

                if(strpos($val->qty,"-") ===false && $ttl_qty !=0){

                    $d[$i]['no'] = $no;
                    $d[$i]['id'] = $val->id;
                    $d[$i]['customer'] = $val->customer;
                    $d[$i]['event_name'] = Events::find($val->event)->event;
                    $d[$i]['event'] = $val->event;
                    $d[$i]['item'] = Items::find($val->item)->item_desc;
                    $d[$i]['item_id'] = $val->item;
                    $d[$i]['ttl_qty'] = $ttl_qty;

                    $no++;
                } 
            }
            
        }else{
            $d = [];
        }

        return json_encode($d);
        
    }

    public function accounts_txns_update(Request $request){
        $cust_id = $request->customer;

        $br = Branches::select('id')->where('curr',1)->limit(1)->first();
        
        
        $acc = Accounts::select('qty','id','item','event','qty','customer','status','branch')->where('customer',$cust_id)->where('status',1)->get(); 
        
        if(count($acc) > 0){
            $no = 1;
            foreach($acc as $i=>$val){

                $sc_ttl = ShoppingCart::where('status',1)->where('customer',$cust_id)->where('item',$val->item)->sum('qty');

                $acc_qty = Accounts::where('customer',$cust_id)->where('event',$val->event)->where('item',$val->item)->sum('qty');

                $ttl_qty = $acc_qty - abs($sc_ttl);

                if(strpos($val->qty,"-") ===false){

                    $d[$i]['no'] = $no;
                    $d[$i]['id'] = $val->id;
                    $d[$i]['customer'] = $val->customer;
                    $d[$i]['event_name'] = Events::find($val->event)->event;
                    $d[$i]['event'] = $val->event;
                    $d[$i]['item'] = Items::find($val->item)->item_desc;
                    $d[$i]['item_id'] = $val->item;
                    $d[$i]['ttl_qty'] = $acc_qty;

                    $no++;
                } 
            }
            
        }else{
            $d = [];
        }

        return json_encode($d);
        
    }

    public function get_active_customers(Request $request){

        $cust = Customers::select('id','f_name','s_name','org')->where('status',1)->get();
        
        foreach($cust as $i=>$val){

            $d[$i]['id'] = $val->id;
            $d[$i]['org'] = ($val->org) ? $val->org : '';
            $d[$i]['s_name'] = ($val->s_name) ? $val->s_name : '';
            $d[$i]['f_name'] = ($val->f_name) ? $val->f_name : '';
            
        }

        return json_encode($d);
    }

    public function search_customers_auto_comp(Request $request){
        
        $cust = Customers::select('f_name','s_name','org','member_no')->where('status',1)->get();
        if($cust->count() > 0){

            foreach($cust as $val){
                    $d[] =   $val->f_name . ' ' . $val->s_name . ' - ' . $val->org . ' - ' . $val->member_no;
            }

            return(json_encode($d));

        }else{
            return json_encode(array('status'=>0));
        }
    }

    public function new_acc_entry(Request $request){

        $i = Items::select('id')->where('item_desc',$request->item)->orderBy('id','DESC')->limit(1)->first();
        
        if($i){
            $ac = new Accounts ();
            $ac->customer = $request->cust;
            $ac->item = $i->id;
            $ac->event = $request->event;
            $ac->qty = $request->qty;
            $ac_chk = Accounts::where('item',$i->id)->where('status',1)->where('customer',$request->cust)->sum('qty');
            $ac->ttl_qty = $ac_chk + $request->qty;
            $br = Branches::where('curr',1)->select('id')->orderBy('id','DESC')->limit(1)->first();
            $ac->branch = $br->id;
            $ac->acc_date = time();
            $ac->save();

            return json_encode(array('status'=>1));
        }else{
            return json_encode(array('status'=>0));
        }
        

    }

    public function acc_entry_rprts(Request $request){

        $acc = Accounts::select('id','ttl_qty','customer','item','event','qty','acc_date','branch','status')->orderBy('id','DESC')->get();
        
        if($acc->count() > 0){
            foreach($acc as $i=>$val){
                $d[$i]['id'] = $val->id;
                $d[$i]['item'] = Items::find($val->item)->item_desc;
                $d[$i]['event'] = ($val->event) ? Events::find($val->event)->event : '' ;
                $d[$i]['customer'] = (Customers::find($val->customer)->s_name) ? Customers::find($val->customer)->s_name : Customers::find($val->customer)->org;
                $d[$i]['e_date'] = ($val->event) ? Events::find($val->event)->e_date : '';
                $d[$i]['qty'] = $val->qty;
                $d[$i]['ttl_qty'] = $val->ttl_qty;
                $d[$i]['status'] = $val->status;
                $d[$i]['acc_date'] = date("d-m-y H:i:s",$val->acc_date);
                $d[$i]['branch'] = Branches::find($val->branch)->branch;
            }
        }else{
            $d = [];
        }
        

        return json_encode($d);

    }

    public function del_acc_entry(Request $request){
        $ac = Accounts::find($request->id);
        $ac->status = 0;
        $ac->save();
        return json_encode(array('status'=>1));
    }   


    public function search_auto_comp_customers(Request $request){
        $items = Customers::select('f_name','s_name','org','member_no')->where('status',1)->get();
        if($items->count() > 0){

            foreach($items as $val){
                //$d[] = ($val->f_name) ? $val->f_name : '' . " " . ($val->s_name) ? $val->s_name : '' . " " . ($val->org) ? $val->org : '';
                $fname = ($val->f_name) ? $val->f_name : '';
                $lname = ($val->s_name) ? $val->s_name : '';
                $org = ($val->s_name) ? $val->org : '';
                $d[] =  $fname . ' '. $lname . ' - ' . $org . ' - ' . $val->member_no;
                    
                }

            return(json_encode($d));

        }else{
            return json_encode(array('status'=>0));
        }
    }


    public function search_acc_rprts(Request $request){
        $customer = $request->cust;
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

        $acc = Accounts::where('customer',$cust_id)->get();
        
        if($acc->count() !=0){

            foreach($acc as $i=>$val){
                $d[$i]['id'] = $val->id;
                $d[$i]['item'] = Items::find($val->item)->item_desc;
                $d[$i]['customer'] = Customers::find($val->customer)->s_name;
                $d[$i]['event'] = ($val->event) ? Events::find($val->event)->event : '';
                $d[$i]['e_date'] = ($val->event) ? Events::find($val->event)->e_date : '';
                $d[$i]['qty'] = $val->qty;
                $d[$i]['ttl_qty'] = $val->ttl_qty;
                $d[$i]['status'] = $val->status;
                $d[$i]['acc_date'] = date("d-m-y H:i:s",$val->acc_date);
                $d[$i]['branch'] = Branches::find($val->branch)->branch;
            }
    
            
        }else{
            $d = [];
        }

        return json_encode($d);
        
    }


    public function det_search_acc_rprts(Request $request){

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

        $event = $request->event;
       
        if($customer =="" && $event ==""){
            $acc = Accounts::select('id','ttl_qty','customer','item','event','qty','acc_date','branch','status')->whereBetween('acc_date',array($from,$to))->get();
        
        }elseif($customer !="" && $event !=""){
            $acc = Accounts::select('id','ttl_qty','customer','item','event','qty','acc_date','branch','status')->where('customer',$cust_id)->where('event',$event)->whereBetween('acc_date',array($from,$to))->get();
        
        }elseif($customer =="" && $event !=""){
            $acc = Accounts::select('id','ttl_qty','customer','item','event','qty','acc_date','branch','status')->where('event',$event)->whereBetween('acc_date',array($from,$to))->get();
        
        }elseif($customer !="" && $event ==""){
            $acc = Accounts::select('id','ttl_qty','customer','item','event','qty','acc_date','branch','status')->where('customer',$cust_id)->whereBetween('acc_date',array($from,$to))->get();
        
        }
        
        if($acc->count() !=0){
            foreach($acc as $i=>$val){
                $d[$i]['id'] = $val->id;
                $d[$i]['item'] = Items::find($val->item)->item_desc;
                $d[$i]['customer'] = Customers::find($val->customer)->s_name;
                $d[$i]['event'] = Events::find($val->event)->event;
                $d[$i]['e_date'] = Events::find($val->event)->e_date;
                $d[$i]['qty'] = $val->qty;
                $d[$i]['ttl_qty'] = $val->ttl_qty;
                $d[$i]['status'] = $val->status;
                $d[$i]['acc_date'] = date("d-m-y H:i:s",$val->acc_date);
                $d[$i]['branch'] = Branches::find($val->branch)->branch;
            }

        }else{
            $d = [];
        }

        return json_encode($d);
        
    }

    public function amt_drawings_left(){

        $br = Branches::select('id')->where('curr',1)->orderBy('id', 'DESC')->limit(1)->first();
        
        $dr_curr = Drawings::select('dr_time','remainder_amt')->where('branch',$br->id)->orderBy('id','DESC')->limit(1)->first();
        if($dr_curr){
            $qr_curr = Transactions::where('trans_time', '>', $dr_curr->dr_time)->where('branch',$br->id)->where('type','cash tender')->sum('total');
            $tr_curr = $qr_curr + $dr_curr->remainder_amt;
        }else{
            $qr_curr = Transactions::where('branch',$br->id)->where('type','cash tender')->sum('total');
            $tr_curr = $qr_curr;
            
        }

        /*
        $dr_1 = Drawings::select('dr_time','remainder_amt')->where('branch',1)->orderBy('id','DESC')->limit(1)->first();
        if($dr_1){
            $qr_1 = Transactions::where('trans_time', '>', $dr_1->dr_time)->where('branch',1)->where('type','cash tender')->sum('total');
            $tr_1 = $qr_1 + $dr_1->remainder_amt;
        }else{
            $qr_1 = Transactions::where('branch',1)->where('type','cash tender')->sum('total');
            
            $tr_1 = $qr_1;
        }
        
        $dr_2 = Drawings::select('dr_time')->where('branch',2)->orderBy('id','DESC')->limit(1)->first();
        if($dr_2){
            $qr_2 = Transactions::where('trans_time', '>', $dr_1->dr_time)->where('branch',2)->where('type','cash tender')->sum('total');
            $tr_2 = $qr_2 + $dr_2->remainder_amt;
        }else{
            $qr_2 = Transactions::where('branch',2)->where('type','cash tender')->sum('total');
            
            $tr_2 = $qr_2;
        }
        $dr_3 = Drawings::select('dr_time')->where('branch',3)->orderBy('id','DESC')->limit(1)->first();
        if($dr_3){
            $qr_3 =  Transactions::where('trans_time', '>', $dr_1->dr_time)->where('branch',3)->where('type','cash tender')->sum('total');
            $tr_3 = $qr_3 + $dr_3->remainder_amt;
        }else{
            $qr_3 =  Transactions::where('branch',3)->where('type','cash tender')->sum('total');
            
            $tr_3 = $qr_3;
            
        }

        echo json_encode(array('br_1_dr'=>number_format($tr_1),'br_2_dr'=>number_format($tr_2),'br_3_dr'=>number_format($tr_3),'br_curr_dr'=>number_format($tr_curr)));
        */

        echo json_encode(array('br_curr_dr'=>number_format($tr_curr)));

    }

    

    public function get_goods_comment(Request $request){
        $gds = Goods::select('comments')->find($request->id);
        return json_encode($gds);
    }


    public function create_invoice(Request $request){

        $id = $request->id;

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(130,5,'NOVEL GOLF SHOP',0,0);
        $pdf->Cell(59,5,'INVOICE',0,1);

        $pdf->SetFont('Arial','',12);
        $pdf->Cell(130,5,'P.O. Box 74750-00200',0,0);
        $pdf->Cell(59,5,'',0,1);

        $pdf->Cell(130,5,'Nairobi',0,0);
        $pdf->Cell(25,5,'Date',0,0);
        $pdf->Cell(34,5,date("d-m-Y",time()),0,1);

        $pdf->Cell(130,5,'Phone +12345678',0,0);
        $pdf->Cell(25,5,'Time',0,0);
        $pdf->Cell(34,5,' ' . date("H:i:s",time()),0,1);

        $pdf->Cell(130,5,'Invoice Number:                '  ,0,0);


        //make dummy cell
        $pdf->Cell(189,10,'',0,1);

        $tr = Transactions::select('id','total','discount','total_tax','no_items','customer')->where('id',$id)->get();

        foreach($tr as $val_tr){
            $tr_id = $val_tr->id;
            $total = $val_tr->total;
            $total_tax = $val_tr->total_tax;
            $no_items = $val_tr->no_items;
            $discount = $val_tr->discount;
            $custx = Customers::find($val_tr->customer);

            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(130,5,'Bill To: ',0,1);
            $pdf->Cell(189,1,'',0,1);
            $pdf->SetFont('Arial','',10);
            if(!empty($custx->org) || $custx->org !=null){
                $pdf->Cell(160,5,$custx->org,0,1);
            }else{
                $pdf->Cell(160,5, $custx->f_name . " " . $custx->s_name ,0,1);
            }
            $pdf->Cell(130,5,$custx->phone,0,1);
            $pdf->Cell(130,5,$custx->email,0,1);


            //make dummy cell
            $pdf->Cell(189,10,'',0,1);

            //invoice contents
            $pdf->SetFont('Arial','B',10);

            $pdf->Cell(15,6,'No.',1,0, 'R');
            $pdf->Cell(101,6,'Item',1,0, '');
            $pdf->Cell(15,6,'Qty',1,0, 'R');
            $pdf->Cell(30,6,'Price',1,0, 'R');
            $pdf->Cell(30,6,'Total',1,1, 'R');

            $sc = ShoppingCart::select('qty','tax','price','item','total')->where('tid',$tr_id)->get();
            $no = 1;
            foreach($sc as $val_sc){
                $qty = $val_sc->qty;
                $tax = $val_sc->tax;
                $item = $val_sc->item;
                $price = $val_sc->price;
                $sc_total = $val_sc->total;
                
                $pdf->SetFont('Arial','',10);
                $pdf->Cell(15,6,$no,1,0, 'R');
                $pdf->Cell(101,6,Items::find($item)->item_desc,1,0, '');
                $pdf->Cell(15,6,number_format($qty),1,0, 'R');
                $pdf->Cell(30,6,number_format($price),1,0, 'R');
                $pdf->Cell(30,6,number_format($sc_total),1,1, 'R');
               
                $no++;
            }

            //make dummy cell
            $pdf->Cell(189,10,'',0,1);

            $pdf->SetFont('Arial','',12);
            $pdf->Cell(131,7,'',0,0,'R');
            $pdf->Cell(30,7,'Total Qty',1,0,'R');
            $pdf->Cell(30,7,$no_items,1,1,'R');
            $pdf->Cell(131,7,'',0,0,'R');
            $pdf->Cell(30,7,'Total Tax',1,0,'R');
            $pdf->Cell(30,7,number_format($total_tax),1,1,'R');
            $pdf->Cell(131,7,'',0,0,'R');
            $pdf->Cell(30,7,'Subtotal',1,0,'R');
            $pdf->Cell(30,7,number_format($total),1,1,'R');
            $pdf->Cell(131,7,'',0,0,'R');
            $pdf->Cell(30,7,'Discount',1,0,'R');
            $pdf->Cell(30,7,'('.number_format($discount).')',1,1,'R');
            $pdf->Cell(131,7,'',0,0,'R');
            $pdf->Cell(30,7,'Total',1,0,'R');
            $pdf->Cell(30,7, number_format($total - $discount) ,1,1,'R');

        }

        //make dummy cell
        $pdf->Cell(189,10,'',0,1);

        $pdf->SetFont('Arial','B',6);
        $pdf->Cell(130,5,'',0,0);
        $pdf->Cell(59,5,'Printed By ' . ucfirst(User::find($request->session()->get('uid'))->fname) . ' ' .ucfirst(User::find($request->session()->get('uid'))->lname) ,0,1,'R');


        $filename= "prints/invoice_" . time() . ".pdf";
        $pdf->Output($filename,'F');

        $up_tr = Transactions::find($id);
        $up_tr->invoice = $filename;
        $up_tr->save();

        echo json_encode(array('status'=>1));
    
}


public function create_pdf_receipt(Request $request){

    $id = $_GET['id'];

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(130,5,'NOVEL GOLF SHOP',0,0);
    $pdf->Cell(59,5,'RECEIPT',0,1);

    $pdf->SetFont('Arial','',12);
    $pdf->Cell(130,5,'P.O. Box 74750-00200',0,0);
    $pdf->Cell(59,5,'',0,1);


    //make dummy cell
    $pdf->Cell(189,10,'',0,1);

    $tr = Transactions::select('ref_no','change','cash','type','id','total','discount','total_tax','no_items','customer','receipt_no','trans_time')->where('id',$id)->get();


    foreach($tr as $val_tr){

        $tr_id = $val_tr->id;
        $total = $val_tr->total;
        $total_tax = $val_tr->total_tax;
        $no_items = $val_tr->no_items;
        $discount = $val_tr->discount;
        $receipt_no = $val_tr->receipt_no;
        $trans_time = $val_tr->trans_time;
        $type = strtoupper(str_replace("tender","",$val_tr->type));
        $cash = $val_tr->cash;
        $change = $val_tr->change;
        $ref_no = $val_tr->ref_no;

        $pdf->Cell(130,5,'Nairobi',0,0);
        $pdf->Cell(25,5,'Date',0,0);
        $pdf->Cell(34,5,date("d-m-Y",$trans_time),0,1);

        $pdf->Cell(130,5,'Phone +12345678',0,0);
        $pdf->Cell(25,5,'Time',0,0);
        $pdf->Cell(34,5,' ' . date("H:i:s",$trans_time),0,1);

        //make dummy cell
        $pdf->Cell(189,10,'',0,1);

        $pdf->Cell(130,5,'Receipt Number: ' . $receipt_no  ,0,0);

        //make dummy cell
        $pdf->Cell(189,10,'',0,1);

        $pdf->Cell(130,5,'REF Number: ' . $ref_no  ,0,0);
        

        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(130,5,'',0,1);
        $pdf->Cell(189,1,'',0,1);
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(160,5,'',0,1);
        /*
        if($custx){
            if(!empty($custx->org) || $custx->org !=null){
                $pdf->Cell(160,5,$custx->org,0,1);
            }else{
                $pdf->Cell(160,5, $custx->f_name . " " . $custx->s_name ,0,1);
            }
        }

          $pdf->Cell(130,5,$custx->phone,0,1);
        $pdf->Cell(130,5,$custx->email,0,1);
        */
        $pdf->Cell(130,5,'');
        $pdf->Cell(130,5,'');


        //make dummy cell
        $pdf->Cell(189,10,'',0,1);

        //invoice contents
        $pdf->SetFont('Arial','B',10);

        $pdf->Cell(15,6,'No.',1,0, 'R');
        $pdf->Cell(101,6,'Item',1,0, '');
        $pdf->Cell(15,6,'Qty',1,0, 'R');
        $pdf->Cell(30,6,'Price',1,0, 'R');
        $pdf->Cell(30,6,'Total',1,1, 'R');

        $sc = ShoppingCart::select('qty','tax','price','item','total')->where('tid',$tr_id)->get();
        $no = 1;
        foreach($sc as $val_sc){
            $qty = $val_sc->qty;
            $tax = $val_sc->tax;
            $item = $val_sc->item;
            $price = $val_sc->price;
            $sc_total = $val_sc->total;
            
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(15,6,$no,1,0, 'R');
            $pdf->Cell(101,6,Items::find($item)->item_desc,1,0, '');
            $pdf->Cell(15,6,number_format($qty),1,0, 'R');
            $pdf->Cell(30,6,number_format($price),1,0, 'R');
            $pdf->Cell(30,6,number_format($sc_total),1,1, 'R');
           
            $no++;
        }

        //make dummy cell
        $pdf->Cell(189,10,'',0,1);

        $pdf->SetFont('Arial','',10);
        $pdf->Cell(131,7,'',0,0,'R');
        $pdf->Cell(30,7,'Total Qty',1,0,'R');
        $pdf->Cell(30,7,$no_items,1,1,'R');
        $pdf->Cell(131,7,'',0,0,'R');
        $pdf->Cell(30,7,'Total Tax',1,0,'R');
        $pdf->Cell(30,7,number_format($total_tax),1,1,'R');
        $pdf->Cell(131,7,'',0,0,'R');
        $pdf->Cell(30,7,'Subtotal',1,0,'R');
        $pdf->Cell(30,7,number_format($total),1,1,'R');
        $pdf->Cell(131,7,'',0,0,'R');
        $pdf->Cell(30,7,'Discount',1,0,'R');
        $pdf->Cell(30,7,'('.number_format($discount).')',1,1,'R');
        $pdf->Cell(131,7,'',0,0,'R');
        $pdf->Cell(30,7,'Total',1,0,'R');
        $pdf->Cell(30,7, number_format($total - $discount) ,1,1,'R');


        //make dummy cell
        $pdf->Cell(189,10,'',0,1);

        $pdf->SetFont('Arial','',10);
        $pdf->Cell(131,7,'',0,0,'R');
        $pdf->Cell(30,7,'Type',1,0,'R');
        $pdf->Cell(30,7,$type,1,1,'R');
        $pdf->Cell(131,7,'',0,0,'R');
        $pdf->Cell(30,7,'Cash',1,0,'R');
        $pdf->Cell(30,7,number_format($cash),1,1,'R');
        $pdf->Cell(131,7,'',0,0,'R');
        $pdf->Cell(30,7,'Change',1,0,'R');
        $pdf->Cell(30,7,number_format($change),1,1,'R');
        

    }

    //make dummy cell
    $pdf->Cell(189,10,'',0,1);

    $pdf->SetFont('Arial','B',6);
    $pdf->Cell(130,5,'',0,0);
    $pdf->Cell(59,5,'Served By ' . ucfirst(User::find($request->session()->get('uid'))->fname) . ' ' .ucfirst(User::find($request->session()->get('uid'))->lname) ,0,1,'R');


    $filename= "receipt_" . time() . ".pdf";
    $pdf->Output('D',$filename);

    echo json_encode(array('status'=>1));

}



}


?>