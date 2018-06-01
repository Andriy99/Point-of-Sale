<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <!--<link href="{{asset('css/main.css')}}" rel="stylesheet" type="text/css" media="all">-->
        <link href="{{asset('css/main_touch.css')}}" rel="stylesheet" type="text/css" media="all">
        <link href="{{asset('css/main_touch.css')}}" rel="stylesheet" type="text/css" media="all">
        <link href="{{asset('css/admin.css')}}" rel="stylesheet" type="text/css" media="all">
        
        <link rel="icon" href="{{asset('img/fav.png')}}" type="image/png" >
        <script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
        <script src="{{asset('jquery_ui/jquery-ui.min.css')}}"></script>
        <script src="{{asset('jquery_ui/jquery-ui.min.js')}}"></script>
     
        <link rel="stylesheet" href="{{asset('datepicker/dark.css')}}">
        <script src="{{asset('datepicker/flatpickr.js')}}"></script>

        <script src="{{asset('js/numeric.js')}}"></script>
        <script src="{{asset('js/ext_jq.js')}}"></script>
        <script src="{{asset('js/chart.js')}}"></script>
        <script src="{{asset('react/react.js')}}"></script>
        <script src="{{asset('react/react-dom.js')}}"></script>
        <script src="{{asset('react/browser.min.js')}}"></script>
        <script type="text/babel" src="{{asset('js/teller.js')}}"></script>
        <script type="text/babel" src="{{asset('js/admin.js')}}"></script>
        <script type="text/babel" src="{{asset('js/pool.js')}}"></script>
        
        
      
        <link rel="stylesheet" href="{{asset('autocomplete/easy-autocomplete.css')}}">
        <link rel="stylesheet" href="{{asset('autocomplete/easy-autocomplete.themes.css')}}"> 
        
        <script src="{{asset('autocomplete/jquery.easy-autocomplete.js')}}"></script>

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

        </div>

        <div id="pool">

        </div>
    
	
    </body>
</html>
