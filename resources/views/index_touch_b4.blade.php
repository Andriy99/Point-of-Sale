<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <!--<link href="{{asset('css/main.css')}}" rel="stylesheet" type="text/css" media="all">-->
        <link href="{{asset('css/main_touch.css')}}" rel="stylesheet" type="text/css" media="all">
        <link href="{{asset('css/admin.css')}}" rel="stylesheet" type="text/css" media="all">
        <link rel="icon" href="{{asset('img/fav.png')}}" type="image/png" >
        <script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
       <!-- <script src="{{asset('jquery-ui-1.12.1/jquery-ui.min.js')}}"></script>-->
        <script src="{{asset('js/ext_jq.js')}}"></script>
        <script src="{{asset('react/react.js')}}"></script>
        <script src="{{asset('react/react-dom.js')}}"></script>
        <script src="{{asset('react/browser.min.js')}}"></script>
        <script type="text/babel" src="{{asset('js/teller.js')}}"></script>
        <script type="text/babel" src="{{asset('js/admin.js')}}"></script>
        
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
        <div id="teller">

        </div>

        <div id="admin">
            <div class="admin_back">
                    <div class="menu" id="menu_hold">
                        

                    </div>

                    <div class="admin_top" id="link_trans">
                        <div class='title_link' id="lnk_trans_dash">
                            <div class='title_link_act' id='act_trans_dash'></div>
                            <b>Dashboard</b>
                        </div>

                        <div class='title_link' id="lnk_trans_reports" >
                            <div class='title_link_act' id='act_trans_reports'></div>
                            <b>Reports</b></div>

                        <div class='title_link' id="lnk_trans_drawer">
                            <div class='title_link_act' id='act_trans_drawer'></div>
                            <b>Drawer</b>
                        
                        </div>

                        <div class='title_link' id="lnk_trans_drawer">
                            <div class='title_link_act' id='act_trans_drawer'></div>
                            <b>Drawer Reports</b>
                        </div>
                        
                        </div>
                        
                        

                    <div class="admin_top" id="link_items">
                        <div class='title_link' id="lnk_new_item" >
                            <div class='title_link_act' id='act_new_item'></div>
                            <b>New Item</b>
                        </div>
                        <div class='title_link' id="lnk_item_reports">
                            <div class='title_link_act' id='act_item_reports'></div>
                            <b>Reports</b>
                        </div>
                        
                        <div class='search_link'></div>
                    </div>

                    <div class="admin_top" id="link_users">
                        <div class='title_link' id="lnk_new_user">
                            <div class='title_link_act'></div>
                            <b>New User</b>
                        </div>
                        <div class='title_link' id="lnk_users_rprts">
                            <div class='title_link_act'></div>
                            <b>Reports</b>
                        </div>
                        
                        <div class='search_link'></div>
                    </div>

                    <div class="admin_top" id="link_taxes">
                        <div class='title_link'>
                            <div class='title_link_act'></div>
                            <b>New Tax</b>
                        </div>
                        <div class='title_link'><b>Reports</b></div>
                        <div class='title_link'></div>
                        <div class='title_link'></div>
                        <div class='title_link'></div>
                        <div class='search_link'></div>
                    </div>

                    
                    <div class="dashboard">
                        <!--Transactions-->
                        <div class='ad_wraps' id='dash_wrap' style='background:blue;'>

                            <div class='ad_wraps' id="trans_dash">

                                    <div id="dash_totals">

                                    </div>

                                    <div class="dashboard_graph" id="dashboard_graph">

                                    </div>
                                    <div class="dashboard_graph" id="sec_dashboard_graph">

                                    </div>
                                </div>

                                <div class='ad_wraps' id="trans_reports">
                                    <div class='reports_table_wrap'>
                                        <table>
                                                <thead>
                                                <tr>
                                                    <th>No.</th><th>Date</th><th>Receipt No</th><th>Amount</th><th>Type</th><th>Status</th><th></th>
                                                </tr>
                                                <thead>
                                                <tbody id="tbl_trans_reports">
                                                
                                                </tbody>
                                        </table>
                                    </div>
                                    <div class='reports_totals_wrap'>
                                        <table id="rprt_ttls">
                                            
                                        </table>
                                    </div>
                                    
                                </div>

                                <div class='ad_wraps' id="trans_drawer">
                                    <div class='drawer_form'>
                                        
                                        <table>
                                        <tr>
                                            <td><div class='drawer_msg'></div></td>
                                        <td><b>Opening Amount</b></td>
                                        <td><input type='text' class='dr_op_txt' /></td>
                                        <td><input type='button' value='Save' class='btn_save_drawer'/></td>
                                        </tr>
                                        </table>
                                    </div>
                                    <div class='reports_table_wrap'>
                                        <table>
                                            <tr><th>Opening Date</th><th>Closing Date</th><th>Opening Amount</th><th>Closing Amount</th><th>Status</th><th>User</th><th></th>
                                            
                                            <tbody id="tbl_rprt_drawer">

                                            </tbody>
                                            
                                            
                                        </table>
                                    </div>
                                </div>
                                
                            </div>

                           <!--End of Transactions--> 

                           <!--Items-->

                        <div class='ad_wraps' id='new_items_wrap' style='background:red;'>
                            
                     
                                

                                <div class='ad_wraps' id="item_reports">
                                    <div class='reports_table_wrap'>
                                        <table>

                                            <thead>
                                            <tr>
                                                <th>Code</th><th>Item Description</th><th>Price</th><th>Qty</th><th>Tax</th><th>Status</th><th></th>
                                            </tr>
                                            </thead>

                                            <tbody id='tbl_admin_items_rprt'>
                                                
                                            </tbody>
                                        <table>
                                    </div>
                                </div>
                        </div>  
                      
                    <h2>klhklhklhlkh</h2> 
                        <!--End of Items-->
<!--
                        <div class='ad_wraps' id='users_wrap'>

                            <div class='ad_wraps' id='new_users'>
                            <div class='msg'></div>
                            <div class='form' >
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td><label>First Name</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text' class='txt_fname' /></td>
                                            </tr>
                                            <tr>
                                                <td><label>Last Name</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text' class='txt_lname' /></td>
                                            </tr>
                                            <tr>
                                                <td><label>Email</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text' class='txt_email' /></td>
                                            </tr>
                                            <tr>
                                                <td><label>Phone</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text' class="txt_phone"  /></td>
                                            </tr>
                                            
                                        </tbody>
                                    </table>
                                </div>

                                <div class='form'>
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td><label>Password</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='password' class='txt_passd' /></td>
                                            </tr>
                                            <tr>
                                                <td><label>Confirm Password</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='password' class='txt_passd_conf' /></td>
                                            </tr>
                                            
                                            <tr>
                                                <td><input type='button' class='btn_new_user' value='Save' /></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class='ad_wraps' id='users_report'>

                            </div>
                        </div>
                                                                -->
                      <!--  <div class='ad_wraps' id='taxes_wrap'>
                                <div class='form' id='new_tax_form'>
                                    <div class='msg'></div>
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td><label>Tax Title</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text' class='txt_tax_title' /></td>
                                            </tr>
                                            <tr>
                                                <td><label>Percentage</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text' class="taxPerc"  /></td>
                                            </tr>
                                            <tr>
                                                <td><input type='button' class='btn_new_tax' value='Save' /></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                        </div>-->

                        
                    </div>

                

            </div>
        </div>

        <?php

            use App\Transactions;

            $graph_trans = Transactions::select('total','trans_time','id')->where('type','cash tender')->where('status',1)->get();
            $date_time_today = date("d-m-Y",time());

            $seven = "07";$eight = "08";$nine = "09";$ten = "10";$eleven = "11";$noon = "12";$one = "13";$two = "14";
            $three = "15";$four = "16";$five = "17";$six = "18";$seven_pm = "19";$eight_pm = "20";$nine_pm = "21";$ten_pm = "22";

            $seven_val = 0;$eight_val = 0;$nine_val = 0;$ten_val = 0;$eleven_val = 0;$noon_val = 0;$one_val = 0;$two_val = 0;
            $three_val = 0;$four_val = 0;$five_val = 0;$six_val = 0;$seven_pm_val = 0;$eight_pm_val = 0;$nine_pm_val = 0;$ten_pm_val = 0;
            
            $mon= 0;$tue=0;$wed=0;$thr=0;$fri=0;$sat=0;$sun=0;

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

                //Weekly
                $d['monday'] = date( 'd-m-Y', strtotime( 'monday this week' ) );
                $d['tuesday'] = date( 'd-m-Y', strtotime( 'tuesday this week' ) );
                $d['wedesday'] = date( 'd-m-Y', strtotime( 'wednesday this week' ) );
                $d['thursday'] = date( 'd-m-Y', strtotime( 'thursday this week' ) );
                $d['friday'] = date( 'd-m-Y', strtotime( 'friday this week' ) );
                $d['sarturday'] = date( 'd-m-Y', strtotime( 'sarturday this week' ) );
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
                }elseif($d['sarturday'] == date("d-m-Y",$val_graph_trans->trans_time)){
                    $sat += $val_graph_trans->total;
                }elseif($d['sunday'] == date("d-m-Y",$val_graph_trans->trans_time)){
                    $sun += $val_graph_trans->total;
                }
                
                
            }

            $weekly_figures = $mon . ',' . $tue . ',' . $wed . ',' . $thr . ',' . $fri . ',' . $sat . ',' . $sun;

            $hourly_figures = $seven_val . ',' . $eight_val . ',' . $nine_val . ',' . $ten_val . ',' . $eleven_val . ',' . $noon_val . ',' . $one_val . ',' . $two_val . ',' . $three_val . ',' . $four_val . ',' . $five_val . ',' . $six_val . ',' . $seven_pm_val . ',' . $eight_pm_val . ',' . $nine_pm_val . ',' . $ten_pm_val;

            //$hourly_figures = '49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4';
        ?>

    
		<script type="text/javascript">

            
            Highcharts.chart('dashboard_graph', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Hourly Transaction Totals'
                },
                xAxis: {
                    categories: [
                        '7 AM',
                        '8 AM',
                        '9 AM',
                        '10 AM',
                        '11 AM',
                        '12 AM',
                        '1 PM',
                        '2 PM',
                        '3 PM',
                        '4 PM',
                        '5 PM',
                        '6 PM',
                        '7 PM',
                        '8 PM',
                        '9 PM',
                        '10 PM'
                    ],
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Ksh'
                    }
                },
                tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:0"><b>{point.y:.2f}</b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                series: [{
                    name: 'Transactions',
                    data: [<?php echo $hourly_figures; ?>]

                }]
            });


            Highcharts.chart('sec_dashboard_graph', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Daily Transaction Totals'
                },
                xAxis: {
                    categories: [
                        'Mon',
                        'Tue',
                        'Wed',
                        'Thur',
                        'Fri',
                        'Sat',
                        'Sun'
                    ],
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Ksh'
                    }
                },
                tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:0"><b>{point.y:.2f} </b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                series: [{
                    name: 'Transactions',
                    data: [<?php echo $weekly_figures; ?>]

                }]
            });
        </script>

    </body>
</html>
