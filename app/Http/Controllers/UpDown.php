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
use App\Checker;

use File;
use Auth;

class UpDown extends Controller
{

    
    public function upload_data(){

        $br = Branches::select('id')->where('curr',1)->limit(1)->first();

        $users = User::select('id','fname','lname','phone','email')->where('up','0')->limit(1)->get();

        if($users->count() > 0){
            foreach($users as $i=>$val_u){
                $u[$i]['id'] = $val_u->id;
                $u[$i]['fname'] = $val_u->fname;
                $u[$i]['lname'] = $val_u->lname;
                $u[$i]['phone'] = $val_u->phone;
                $u[$i]['email'] = $val_u->email;
                $u[$i]['up_branch'] = $br->id;
    
                //$up_user = User::find($val_u->id);
                //$up_user->up = 1;
                //$up_user->save();
    
            }
        }else{
            $u = "";
        }
        

        $catg = Catg::select('id','catg_name')->where('up','0')->limit(1)->get();
        if($catg->count() > 0){
            foreach($catg as $i=>$val_c){
                $c[$i]['id'] = $val_c->id;
                $c[$i]['catg_name'] = $val_c->catg_name;
                $c[$i]['up_branch'] = $br->id;

                //$up_catg = Catg::find($val_c->id);
                //$up_catg->up = 1;
                //$up_catg->save();

            }
        }else{
            $c = "";
        }

        $sub_catg = SubCatg::where('up','0')->limit(1)->get();
        if($sub_catg->count() > 0){
            foreach($sub_catg as $i=>$val_sbc){
                $sbc[$i]['id'] = $val_sbc->id;
                $sbc[$i]['sub'] = $val_sbc->sub;
                $sbc[$i]['catg'] = $val_sbc->catg;
                $sbc[$i]['up_branch'] = $br->id;

                //$up_catg = SubCatg::find($val_sbc->id);
                //$up_catg->up = 1;
                //$up_catg->save();

            }
        }else{
            $sbc = "";
        }

        $draw = Drawings::limit(1)->where('up','0')->get();
        if($draw->count() > 0){
            foreach($draw as $i=>$val_dr){
                $dr[$i]['id'] = $val_dr->id;
                $dr[$i]['comment'] = $val_dr->comment;
                $dr[$i]['amount'] = $val_dr->amount;
                $dr[$i]['dr_time'] = $val_dr->dr_time;
                $dr[$i]['uid'] = $val_dr->uid;
                $dr[$i]['tid'] = $val_dr->tid;
                $dr[$i]['branch'] = $val_dr->branch;
                $dr[$i]['sales_amt'] = $val_dr->sales_amt;
                $dr[$i]['remainder_amt'] = $val_dr->remainder_amt;
                $dr[$i]['up_branch'] = $br->id;

                //$up_draw = Drawings::find($val_dr->id);
                //$up_draw->up = 1;
                //$up_draw->save();

            }
        }else{
            $dr = "";
        }

        $items = Items::where('up','0')->limit(1)->get();
        if($items->count() > 0){
            foreach($items as $i=>$val_i){
                $it[$i]['id'] = $val_i->id;
                $it[$i]['look_up'] = $val_i->look_up;
                $it[$i]['item_desc'] = $val_i->item_desc;
                $it[$i]['sell_price'] = $val_i->sell_price;
                $it[$i]['buy_price'] = $val_i->buy_price;
                $it[$i]['ceil_price'] = $val_i->ceil_price;
                $it[$i]['reorder_level'] = $val_i->reorder_level;
                $it[$i]['floor_price'] = $val_i->floor_price;
                $it[$i]['tax'] = $val_i->tax;
                $it[$i]['sub_catg'] = $val_i->sub_catg;
                $it[$i]['qty'] = ($val_i->qty) ? $val_i->qty : 0;
                $it[$i]['catg'] = $val_i->catg;
                $it[$i]['code_no'] = $val_i->code_no;
                $it[$i]['up_branch'] = $br->id;

                //$up_items = Items::find($val_i->id);
                //$up_items->up = 1;
                //$up_items->save();

            }
        }else{
            $it = "";
        }

        

        $trans = Transactions::where('up','0')->limit(1)->get();
        if($trans->count() > 0){
            foreach($trans as $i=>$val_tr){

                $tr[$i]['id'] = $val_tr->id;
                $tr[$i]['cash'] = $val_tr->cash;
                $tr[$i]['change'] = $val_tr->change;
                $tr[$i]['total_tax'] = $val_tr->total_tax;
                $tr[$i]['total_gross'] = $val_tr->total_gross;
                $tr[$i]['total'] = $val_tr->total;
                $tr[$i]['no_items'] = $val_tr->no_items;
                $tr[$i]['trans_time'] = $val_tr->trans_time;
                $tr[$i]['type'] = $val_tr->type;
                $tr[$i]['receipt_no'] = $val_tr->receipt_no;
                $tr[$i]['customer'] = $val_tr->customer;
                $tr[$i]['discount'] = $val_tr->discount;
                $tr[$i]['branch'] = $val_tr->branch;
                $tr[$i]['comment'] = $val_tr->comment;
                $tr[$i]['user'] = $val_tr->user;
                $tr[$i]['status'] = $val_tr->status;
                $tr[$i]['invoice'] = $val_tr->invoice;
                $tr[$i]['ref_no'] = $val_tr->ref_no;
                $tr[$i]['total_cost'] = $val_tr->total_cost;
                $tr[$i]['up_branch'] = $br->id;



                $sc = ShoppingCart::where('up','0')->where('tid',$val_tr->id)->get();
                    if($sc->count() > 0){
                        foreach($sc as $i=>$val_sc){
                            $s[$i]['id'] = $val_sc->id;
                            $s[$i]['qty'] = $val_sc->qty;
                            $s[$i]['item'] = $val_sc->item;
                            $s[$i]['tid'] = $val_sc->tid;
                            $s[$i]['uid'] = $val_sc->uid;
                            $s[$i]['price'] = $val_sc->price;
                            $s[$i]['total'] = $val_sc->total;
                            $s[$i]['catg'] = $val_sc->catg;
                            $s[$i]['status'] = $val_sc->status;
                            $s[$i]['branch'] = $val_sc->branch;
                            $s[$i]['time'] = $val_sc->time;
                            $s[$i]['tax'] = $val_sc->tax;
                            $s[$i]['customer'] = $val_sc->customer;
                            $s[$i]['cost'] = $val_sc->cost;
                            $s[$i]['type'] = $val_sc->type;
                            $s[$i]['up_branch'] = $br->id;

                        }

                    }else{
                        $s = "";
                    }

                }
        }else{
            $tr = "";
            $s = "";
        }

        $goods = Goods::where('up','0')->limit(1)->get();
        if($goods->count() > 0){
            foreach($goods as $i=>$val_goods){
                $g[$i]['id'] = $val_goods->id;
                $g[$i]['date_received'] = $val_goods->date_received;
                $g[$i]['item'] = $val_goods->item;
                $g[$i]['status'] = $val_goods->status;
                $g[$i]['received_by'] = $val_goods->received_by;
                $g[$i]['receipt_no'] = $val_goods->receipt_no;
                $g[$i]['qty'] = $val_goods->qty;
                $g[$i]['cost'] = $val_goods->cost;
                $g[$i]['price'] = $val_goods->price;
                $g[$i]['ceil_price'] = $val_goods->ceil_price;
                $g[$i]['floor_price'] = $val_goods->floor_price;
                $g[$i]['comments'] = $val_goods->comments;
                $g[$i]['branch'] = $val_goods->branch;
                $g[$i]['transfer_date'] = $val_goods->transfer_date;
                $g[$i]['transfer_by'] = $val_goods->transfer_by;
                $g[$i]['delivery_note_no'] = $val_goods->delivery_note_no;
                $g[$i]['dn_print_time'] = $val_goods->dn_print_time;
                $g[$i]['from_branch'] = $val_goods->from_branch;
                $g[$i]['dn_printed_by'] = $val_goods->dn_printed_by;
                $g[$i]['down'] = ($val_goods->down) ? $val_goods->down : 0;
                $g[$i]['up_branch'] = $br->id;
               

            }
        }else{
                $g = "";
        }

        $customers = Customers::where('up','0')->limit(1)->get();

        if($customers->count() > 0){
            foreach($customers as $i=>$val_cu){
                $cu[$i]['id'] = $val_cu->id;
                $cu[$i]['f_name'] = $val_cu->f_name;
                $cu[$i]['s_name'] = $val_cu->s_name;
                $cu[$i]['id_no'] = $val_cu->id_no;
                $cu[$i]['phone'] = $val_cu->phone;
                $cu[$i]['email'] = $val_cu->email;
                $cu[$i]['club'] = ($val_cu->club) ? $val_cu->club : 0;
                $cu[$i]['org'] = $val_cu->org;
                $cu[$i]['up_branch'] = $br->id;
               
            }
        }else{
            $cu = "";
        }

        $pool = Pool::where('up','0')->limit(1)->get();

        if($pool->count() > 0){
            foreach($pool as $i=>$val_p){
                $p[$i]['id'] = $val_p->id;
                $p[$i]['item'] = $val_p->item;
                $p[$i]['customer'] = $val_p->customer;
                $p[$i]['qty'] = $val_p->qty;
                $p[$i]['event'] = $val_p->event;
                $p[$i]['sc_id'] = $val_p->sc_id;
                $p[$i]['p_time'] = $val_p->p_time;
                $p[$i]['branch'] = $val_p->branch;
                $p[$i]['user'] = $val_p->user;
                $p[$i]['club'] = $val_p->club;
                $p[$i]['type'] = $val_p->type;
                $p[$i]['up_branch'] = $br->id;
               
            }
        }else{
            $p = "";
        }

        $event = Events::where('up','0')->limit(1)->get();

        if($event->count() > 0){
            foreach($event as $i=>$val_e){
                $e[$i]['id'] = $val_e->id;
                $e[$i]['event'] = $val_e->event;
                $e[$i]['e_date'] = $val_e->e_date;
                $e[$i]['club'] = $val_e->qty;
                $e[$i]['sponsor'] = $val_e->sponsor;
                $e[$i]['type'] = $val_e->type;
                $e[$i]['status'] = $val_e->status;
                $e[$i]['branch'] = $val_e->branch;
                $e[$i]['user'] = $val_e->user;
                $e[$i]['up_branch'] = $br->id;
               
            }
        }else{
            $e = "";
        }

        $accs = Accounts::where('up','0')->where('status','1')->where('status','1')->limit(1)->get();

        if($accs->count() > 0){
            foreach($accs as $i=>$val_accs){
                $ac[$i]['id'] = $val_accs->id;
                $ac[$i]['customer'] = $val_accs->customer;
                $ac[$i]['item'] = $val_accs->item;
                $ac[$i]['event'] = $val_accs->event;
                $ac[$i]['qty'] = $val_accs->qty;
                $ac[$i]['acc_date'] = $val_accs->acc_date;
                $ac[$i]['status'] = $val_accs->status;
                $ac[$i]['branch'] = $val_accs->branch;
                $ac[$i]['ttl_qty'] = $val_accs->ttl_qty;
                $ac[$i]['credit'] = $val_accs->credit;
                $ac[$i]['up_branch'] = $br->id;
               
            }
        }else{
            $ac = "";
        }

        $data['users'] = $u;
        $data['sub_catg'] = $sbc;
        $data['categories'] = $c;
        $data['drawings'] = $dr;
        $data['items'] = $it;
        $data['shopping_cart'] = $s;
        $data['transactions'] = $tr;
        $data['goods'] = $g;
        $data['customers'] = $cu;
        $data['pool'] = $p;
        $data['events'] = $e;
        $data['accounts'] = $ac;

        return json_encode($data);

    }

    
    public function update_data(Request $request){
        
        $update_data = $request->update_data;
        if(!empty($update_data['goods'])){
            foreach($update_data['goods'] as $val_goods){
               // echo $val_goods['date_received'];
               $up_gds = Goods::find($val_goods['id']);
               $up_gds->up = 1;
               $up_gds->save();
            }
        }

        if(!empty($update_data['shopping_cart'])){

            foreach($update_data['shopping_cart'] as $val_sc){
                $up_sc = ShoppingCart::find($val_sc['id']);
                $up_sc->up = 1;
                $up_sc->save();
            }
           
        }

        if(!empty($update_data['transactions'])){

            foreach($update_data['transactions'] as $val_tr){
                $up_tr = Transactions::find($val_tr['id']);
                $up_tr->up = 1;
                $up_tr->save();
            }
           
        }

        if(!empty($update_data['pool'])){

            foreach($update_data['pool'] as $val_p){
                $up_p = Pool::find($val_p['id']);
                $up_p->up = 1;
                $up_p->save();
            }
           
        }

        if(!empty($update_data['accounts'])){

            foreach($update_data['accounts'] as $val_acc){
                $up_acc = Accounts::find($val_acc['id']);
                $up_acc->up = 1;
                $up_acc->save();
            }
           
        }

        if(!empty($update_data['customers'])){

            foreach($update_data['customers'] as $val_cu){
                $up_cu = Customers::find($val_cu['id']);
                $up_cu->up = 1;
                $up_cu->save();
            }
           
        }

        if(!empty($update_data['events'])){

            foreach($update_data['events'] as $val_ev){
                $up_ev = Events::find($val_ev['id']);
                $up_ev->up = 1;
                $up_ev->save();
            }
           
        }

        if(!empty($update_data['items'])){

            foreach($update_data['items'] as $val_i){
                $up_i = Items::find($val_i['id']);
                $up_i->up = 1;
                $up_i->save();
            }
           
        }

        if(!empty($update_data['drawings'])){

            foreach($update_data['drawings'] as $val_dr){
                $up_dr = Drawings::find($val_dr['id']);
                $up_dr->up = 1;
                $up_dr->save();
            }
           
        }

        if(!empty($update_data['users'])){

            foreach($update_data['users'] as $val_u){
                $up_u = User::find($val_u['id']);
                $up_u->up = 1;
                $up_u->save();
            }
           
        }

        print_r($update_data);
        
    }

    public function up_reports_check(Request $request){

        $br = Branches::select('id')->where('curr',1)->limit(1)->first();
        
        echo json_encode(array('curr_branch'=>$br->id));

    }

    public function download_data(Request $request){

        $goods = $request->down_data['goods'];
       
        if($goods !=""){
            foreach($goods as $val_gds){

                $chk_gds = Goods::select('id')->where('down_branch',$val_gds['up_branch'])->where('down_id',$val_gds['up_id'])->get();
    
                if($chk_gds->count() ==0){

                    $prv_g = Goods::select('qty','cost','price','ceil_price','floor_price')->where('item',$val_gds['item'])->where('status',1)->orderBy('id','DESC')->limit(1)->first();
                    if($prv_g){

                        if($val_gds['cost'] != $prv_g->cost){

                            $sum_gds = Goods::where('status',1)->where('item',$val_gds['item'])->sum('qty');
                            $sum_sc = ShoppingCart::where('status',2)->where('type','tender')->where('item',$val_gds['item'])->sum('qty');
                            $sum_rtrn = ShoppingCart::where('status',4)->where('type','return')->where('item',$val_gds['item'])->sum('qty');
                            $stock = $sum_gds - ($sum_sc - $sum_rtrn);
        
                            $weighted_avg = round((($stock * $prv_g->cost) + ($request->qty * $request->cost)) / ($prv_g->qty + $request->qty));
                        
                            $up_item = Items::find($val_gds['item']);
                            $up_item->buy_price = $weighted_avg;
                            $up_item->save();

                            if($val_gds['price'] != $prv_g->price){
                                $up_item = Items::find($val_gds['item']);
                                $up_item->sell_price = $val_gds['price'];
                                $up_item->save();
                            }
            
                            if($val_gds['ceil_price'] != $prv_g->ceil_price){
                                $up_item = Items::find($val_gds['item']);
                                $up_item->ceil_price = $val_gds['ceil_price'];
                                $up_item->save();
                            }
            
                            if($val_gds['floor_price'] != $prv_g->floor_price){
                                $up_item = Items::find($val_gds['item']);
                                $up_item->floor_price = $val_gds['floor_price'];
                                $up_item->save();
                            }

                        }

                    }


                    $scnd_chk_goods = Goods::select('id')->where('down_id',$val_gds['up_id'])->get();
                    
                    if($scnd_chk_goods->count() ==0){

                        $gds = new Goods();
                        $gds->date_received = $val_gds['date_received'];
                        $gds->item = $val_gds['item'];
                        $gds->received_by = $val_gds['received_by'];
                        $gds->qty = abs($val_gds['qty']);
                        $gds->cost = $val_gds['cost'];
                        $gds->price = $val_gds['price'];
                        $gds->ceil_price = $val_gds['ceil_price'];
                        $gds->floor_price = $val_gds['floor_price'];
                        $gds->comments = $val_gds['comments'];
                        $gds->branch = $val_gds['branch'];
                        $gds->transfer_date = $val_gds['transfer_date'];
                        $gds->transfer_by = $val_gds['transfer_by'];
                        $gds->delivery_note_no = $val_gds['delivery_note_no'];
                        $gds->dn_print_time = $val_gds['dn_print_time'];
                        $gds->from_branch = $val_gds['from_branch'];
                        $gds->dn_printed_by = $val_gds['dn_printed_by'];
                        $gds->down_branch = $val_gds['up_branch'];
                        $gds->down_id = $val_gds['up_id'];
                        $gds->status = 0;
                        $gds->up = 1;
                        $gds->save();
                        
                    }
                    
                    

                    echo json_encode(array('status'=>1,'up_branch'=>$val_gds['up_branch'],'up_id'=>$val_gds['up_id']));
                }
            }
        }
        

    }

    public function new_mail_reorder(Request $request){

        $br = Branches::select('id','branch')->where('curr',1)->limit(1)->first();
        
        $init_chk = Checker::first();

        if($init_chk){

            $start_time = $init_chk->check_time;
            $end_time = time();
            $start_time -= $start_time % 86400;
            $end_time -= $end_time % 86400;
            $days = ($end_time - $start_time) / 86400;
            
            Checker::getQuery()->delete();

            if($days > 6){
    
                $items = Items::select('id','reorder_level','item_desc')->where('status',1)->get();
    
                foreach($items as $i=>$val_items){
                    $gds_ttl_qty = Goods::where('item',$val_items->id)->where('status',1)->sum('qty');
                    $sc_ttl_qty = ShoppingCart::where('item',$val_items->id)->where('status',2)->where('type','tender')->sum('qty');
                    $sc_rtrn_ttl_qty = ShoppingCart::where('item',$val_items->id)->where('status',4)->where('type','return')->sum('qty');
                    $ttl_qty = $gds_ttl_qty - ($sc_ttl_qty + $sc_rtrn_ttl_qty);
                    
                    if($ttl_qty < $val_items->reorder_level){
    
                        $d[$i]['item_desc'] = $val_items->item_desc;
                        $d[$i]['qty'] = $ttl_qty;
                        $d[$i]['reorder'] = $val_items->reorder_level;
    
                        $n_chk = new Checker();
                        $n_chk->item = $val_items->id;
                        $n_chk->qty = $ttl_qty;
                        $n_chk->check_time = time();
                        $n_chk->type = "reorder";
                        $n_chk->save();
    
                    }
    
                }
    
            }

           
        }else{

            $items = Items::select('id','reorder_level','item_desc')->where('status',1)->get();
    
            foreach($items as $i=>$val_items){
                $gds_ttl_qty = Goods::where('item',$val_items->id)->where('status',1)->sum('qty');
                $sc_ttl_qty = ShoppingCart::where('item',$val_items->id)->where('status',2)->where('type','tender')->sum('qty');
                $sc_rtrn_ttl_qty = ShoppingCart::where('item',$val_items->id)->where('status',4)->where('type','return')->sum('qty');
                $ttl_qty = $gds_ttl_qty - ($sc_ttl_qty + $sc_rtrn_ttl_qty);
                
                if($ttl_qty < $val_items->reorder_level){

                    $d[$i]['item_desc'] = $val_items->item_desc;
                    $d[$i]['qty'] = $ttl_qty;
                    $d[$i]['reorder'] = $val_items->reorder_level;

                    $n_chk = new Checker();
                    $n_chk->item = $val_items->id;
                    $n_chk->qty = $ttl_qty;
                    $n_chk->check_time = time();
                    $n_chk->type = "reorder";
                    $n_chk->save();

                }

            }

        }
        

        $snd_chk = Checker::get();
        
        if($snd_chk->count() > 0){

            foreach($snd_chk as $j=>$val_snd_chk){
                //$f[$j]['check_time'] = $val_snd_chk->check_time;
    
                $f[$j]['item_desc'] = Items::select('item_desc')->find($val_snd_chk->item)->item_desc;
                $f[$j]['reorder_level'] = Items::select('reorder_level')->find($val_snd_chk->item)->reorder_level;
                $f[$j]['qty'] = $val_snd_chk->qty;
                
            }

        }else{
            $f = [];
        }
        

        echo json_encode($f);
    }

    public function mail_reorder(Request $request){

        $to = "oscarababu@gmail.com";
        $subject = "ITEMS OUT OF STOCK FROM ";

        $message = "
        <html>
        <head>
        <title>HTML email</title>
        </head>
        <body>
        <p>This email contains HTML Tags!</p>
        <table>
        <tr>
        <th>Firstname</th>
        <th>Lastname</th>
        </tr>
        <tr>
        <td>John</td>
        <td>Doe</td>
        </tr>
        </table>
        </body>
        </html>
        ";

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
        $headers .= "From: oscar@ababuapps.com" . "\r\n" .
        "Reply-To: oscar@ababuapps@gmail.com" . "\r\n" .
        "X-Mailer: PHP/" . phpversion();

        mail($to,$subject,$message,$headers);

            if ( function_exists( 'mail' ) )
            {
                echo 'mail() is available';
            }
            else
            {
                echo 'mail() has been disabled';
            }

        exit();

        $br = Branches::select('id','branch')->where('curr',1)->limit(1)->first();
        
        
        $init_chk = Checker::where('type','reorder')->orderBy('id','DESC')->first();

        if($init_chk){

            $start_time = $init_chk->check_time;
            $end_time = time();
            $start_time -= $start_time % 86400;
            $end_time -= $end_time % 86400;
            $days = ($end_time - $start_time) / 86400;

            if($days > 6){


                    $items = Items::select('id','reorder_level','item_desc')->where('status',1)->get();
                
                    if($items->count() > 0){

                        foreach($items as $i=>$val_items){
                            $gds_ttl_qty = Goods::where('item',$val_items->id)->where('status',1)->sum('qty');
                            $sc_ttl_qty = ShoppingCart::where('item',$val_items->id)->where('status',2)->where('type','tender')->sum('qty');
                            $sc_rtrn_ttl_qty = ShoppingCart::where('item',$val_items->id)->where('status',4)->where('type','return')->sum('qty');
                            $ttl_qty = $gds_ttl_qty - ($sc_ttl_qty + $sc_rtrn_ttl_qty);
                            
                            if($ttl_qty < $val_items->reorder_level){
            
                                $d[$i]['item_desc'] = $val_items->item_desc;
                                $d[$i]['qty'] = $ttl_qty;
                                $d[$i]['reorder'] = $val_items->reorder_level;
            
                            }
            
                        }

                    }else{
                        $d = [];
                    }

                    
                    $up_chk = Checker::find($init_chk->id);
                    $up_chk->check_time = time();
                    $up_chk->save();
    
            }else{

                $d = [];
            }

            
            $start_time = $init_chk->check_time;
            $end_time = time();
            $start_time -= $start_time % 86400;
            $end_time -= $end_time % 86400;
            $days = ($end_time - $start_time) / 86400;



        }else{

            $in_chk = new Checker();
            $in_chk->check_time = time();
            $in_chk->type = "reorder";
            $in_chk->save();

            $items = Items::select('id','reorder_level','item_desc')->where('status',1)->get();
            
            if($items->count() > 0){
                foreach($items as $i=>$val_items){
                    $gds_ttl_qty = Goods::where('item',$val_items->id)->where('status',1)->sum('qty');
                    $sc_ttl_qty = ShoppingCart::where('item',$val_items->id)->where('status',2)->where('type','tender')->sum('qty');
                    $sc_rtrn_ttl_qty = ShoppingCart::where('item',$val_items->id)->where('status',4)->where('type','return')->sum('qty');
                    $ttl_qty = $gds_ttl_qty - ($sc_ttl_qty + $sc_rtrn_ttl_qty);
                    
                    if($ttl_qty < $val_items->reorder_level){
    
                        $d[$i]['item_desc'] = $val_items->item_desc;
                        $d[$i]['qty'] = $ttl_qty;
                        $d[$i]['reorder'] = $val_items->reorder_level;
    
                    }
    
                }

            }else{
                $d = [];
            }

        }

        $all['data'] = $d;
        $all['branch'] = array($br->branch);

        echo json_encode($all);

    }

}

?>