<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <!--<link href="{{asset('css/main.css')}}" rel="stylesheet" type="text/css" media="all">-->
        <link href="{{asset('css/main_touch.css')}}" rel="stylesheet" type="text/css" media="all">

        <script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
        <script src="{{asset('react/react.js')}}"></script>
        <script src="{{asset('react/react-dom.js')}}"></script>
        <script src="{{asset('react/browser.min.js')}}"></script>
        <script type="text/babel" src="{{asset('js/Comments.js')}}"></script>

        <?php 
	//get the base URL and pass it in a Javascript Variable
		echo "<script type=\"text/javascript\">";
		echo "var server_url = '". url('/') ."'";
        echo "</script>";

        echo "<script type=\"text/javascript\">";
		echo "var cs_token = '". csrf_token()  ."'";
        echo "</script>";
        
        
        
	?>
    

        <title>Point of Sale</title>

       
        
    </head>
    <body>
        <div class='search_box'>
            <div class="user_sname" />o. ababu</div>
            <input type='text' class='search_txt' />
        </div>

        <div class="wrap">
            <div class='desc'>
                <table>
                    <tr class="mbox_container">
                        <td>
                            <div class='mbox'>
                                <div class='mbox_del'  ></div>
                                <div class='mbox_items_ttl_top'>Item Description</div>
                                <div class='mbox_items_desc'>sadasdasdsadas 500gms</div>
                                <div class='mbox_price_ttl'>Price</div>
                                <div class='mbox_items_price'>1,999.00</div>
                                <div class='mbox_qty'><div class='mbox_qty_ins_ttl'  >Qty: </div><div class='mbox_qty_ins'>11</div></div>
                                <div class='mbox_items_ttl'>Total</div>
                                <div class='mbox_items_total'>1,999.00</div>
                            </div>
                        </td>
                        <td>
                            <div class='mbox'>
                                <div class='mbox_del'  ></div>
                                <div class='mbox_items_ttl_top'>Item Description</div>
                                <div class='mbox_items_desc'>sadasdasdsadas 500gms</div>
                                <div class='mbox_price_ttl'>Price</div>
                                <div class='mbox_items_price'>1,999.00</div>
                                <div class='mbox_qty'><div class='mbox_qty_ins_ttl'  >Qty: </div><div class='mbox_qty_ins'>11</div></div>
                                <div class='mbox_items_ttl'>Total</div>
                                <div class='mbox_items_total'>1,999.00</div>
                            </div>
                        </td>
                        <td>
                            <div class='mbox'>
                                <div class='mbox_del'  ></div>
                                <div class='mbox_items_ttl_top'>Item Description</div>
                                <div class='mbox_items_desc'>sadasdasdsadas 500gms</div>
                                <div class='mbox_price_ttl'>Price</div>
                                <div class='mbox_items_price'>1,999.00</div>
                                <div class='mbox_qty'><div class='mbox_qty_ins_ttl'  >Qty: </div><div class='mbox_qty_ins'>11</div></div>
                                <div class='mbox_items_ttl'>Total</div>
                                <div class='mbox_items_total'>1,999.00</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="wrap">
        <div class="total">
            <div class="one">
                
                <div class="total_vat">
                    <p>Total VAT</p>
                    <b>560.00</b>
                </div>
                <div class="user_details">

                    <p>Oscar Ababu</p>
                    <p> Email: oscarababu@gmail.com</p>
                    <p> Phone: +254729659092</p>
                    <p> Id No.: 24713156</p>
                    <p> Points: 234</p>
                </div>
                <div class="hold">
                    <label># on Hold:</label>
                    <p>1</p>
                </div>
            </div>
           <!-- <label>Total</label>-->
            
            <div class="act_total">
                <div class="total_figure">
                    <p>Total</p>
                    <b>48,999.00</b>
                </div>
                <!--
                <input type="text" disabled="disabled" some="" id="total_ipt" value="3,100.00"/>
                -->
                
                </div>
                <!--<div class="batch">
                    <p>open</p>
                </div>-->
            </div>
					
            </div>
        </div>
        <div class = "login">
            <div class="inside">
                <div class="message"></div>
            <ul>
            <li><input type="text" id="username_ipt" /></li>
            <li><input type="password" id="passwd_ipt" /></li>
            <li><input type="button" id="login_btn" class="login_btn" value="Login" /></li>
            </ul>
            
            
            </div>
		</div>

    </body>
</html>
