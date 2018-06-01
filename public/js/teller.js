let TopActiveMenu = React.createClass({
    render:function(){
        return(<div className='acc_title_link_act'></div>)
    }
});


let Teller = React.createClass({
    componentDidMount() {

        
        setInterval(function(){ 

            $.ajax({
                url:server_url+"/upload_data",
                data:{"_token": cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    let jsonStr = data;
                    let res = JSON.parse(data);
                    
                    
                    if(res.customers !="" || res.sub_catg !="" || res.drawings !="" || res.events !="" || res.pool !="" || res.account !="" || res.goods !="" || res.users !="" || res.categories !="" || res.drawings !="" || res.items !="" || res.shopping_cart !="" || res.transactions !=""){
                        
                        $.ajax({
                            url:"http://www.ababuapps.com/up_pos_test/up_golf.php",
                            contentType: 'application/json; charset=utf-8',
                            headers : {
                                'Content-Type' : 'application/x-www-form-urlencoded; charset=UTF-8'
                            },
                            data:{local_data:jsonStr},
                            type:"GET",
                            success:function(data){
                                console.log(data);
                                let res = JSON.parse(data);

                                $.ajax({
                                    url:server_url+"/update_data",
                                    data:{update_data:res,"_token": cs_token},
                                    type:"POST",
                                    context: this,
                                    success:function(data){
                                            //console.log(data);
                                    }
                                });
        
                            },error: function(xhr, status, text) {
                                console.log("Error "+xhr.status);
                            }
                        });

                    }

                    
                    $.ajax({
                        url:"http://www.ababuapps.com/up_pos_test/down_golf.php?branch=1",
                        contentType: 'application/json; charset=utf-8',
                        headers : {
                            'Content-Type' : 'application/x-www-form-urlencoded; charset=UTF-8'
                        },
                        type:"GET",
                        success:function(data){
                            //console.log(data);
                            let res = JSON.parse(data);
                            //console.log(res.users);

                            $.each(res.goods,function(index,value){
                                let qry = "UPDATE goods SET down='2' WHERE up_id='" + value.up_id + "' AND up_branch='"+value.up_branch+"' ";
                                        
                                $.ajax({
                                    url:"http://www.ababuapps.com/up_pos_test/customs.php",
                                    data:{qry_str:qry,"_token":cs_token},
                                    type:"POST",
                                    context: this,
                                    success:function(data){
                                        //console.log(data);
                                    }
                                });
                                

                            });

                            

                             $.ajax({
                                url:server_url+"/download_data",
                                data:{down_data:res,"_token": cs_token},
                                type:"POST",
                                context: this,
                                success:function(data){
                                    
                                    
                                },error: function(xhr, status, text) {

                                    if(xhr.status ==419){
                                        window.location = server_url;
                                    }

                                }
                            });
                           
                        },error: function(xhr, status, text) {
                            console.log("Error "+xhr.status);
                        }
                    });

                    /*

                    $.ajax({
                        url:server_url+"/mail_reorder",
                        data:{"_token": cs_token},
                        type:"GET",
                        context: this,
                        success:function(data){
                            //console.log(data);
                            let res = JSON.parse(data);
                                
                            if(res.data !=[]){
                                
                                $.ajax({
                                    url:"http://www.ababuapps.com/up_pos_test/mail.php",
                                    data:{str:res,"_token":cs_token},
                                    type:"GET",
                                    context: this,
                                    success:function(data){
                                        console.log(data);
                                    }
                                });
                            }
                            
                            
                        },error: function(xhr, status, text) {

                            if(xhr.status ==419){
                                window.location = server_url;
                            }

                        }
                    });
                    */
                    
                    
                },error: function(xhr, status, text) {
                    console.log("Error "+xhr.status);
                }
            });
            
           
          
        }, 3000);

       

        //Loads on load
        $('#username_ipt').focus();

        /*
        $.ajax({
            url:server_url+"/search_auto_comp_values",
            data:{"_token": cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data); 
                let res  =JSON.parse(data);
                
                if(res.status !=0){
                    let arr = [];
                            
                    $.each(res,function(index,value){
                        arr.push(res[index]);
                    });

                    var options = {

                        data: arr,
                
                    list: {
                        maxNumberOfElements: 8,
                        match: {
                            enabled: true
                        },
                        sort: {
                            enabled: true
                        }
                    },
                        theme: "square"
                    };

                    $(".search_txt").easyAutocomplete(options);

                    //Full screen Logic
                    var el = document.documentElement, rfs = el.requestFullScreen || el.webkitRequestFullScreen || el.mozRequestFullScreen;   
                    rfs.call(el);
                     
                }
                

            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });
        */

       $.ajax({
        url:server_url+"/search_customers_auto_comp",
        data:{"_token": cs_token},
        type:"POST",
        context: this,
        success:function(data){
            //console.log(data); 
            let res  =JSON.parse(data);
            
            if(res.status !=0){
                let arr = [];
                        
                $.each(res,function(index,value){
                    arr.push(res[index]);
                });

                var options = {

                    data: arr,
            
                list: {
                    maxNumberOfElements: 8,
                    match: {
                        enabled: true
                    },
                    sort: {
                        enabled: true
                    }
                },
                    theme: "square"
                };

                $(".txt_add_cust_wrap").easyAutocomplete(options);

                //Full screen Logic
                //var el = document.documentElement, rfs = el.requestFullScreen || el.webkitRequestFullScreen || el.mozRequestFullScreen;   
                //rfs.call(el);
                 
            }
            

        },error: function(xhr, status, text) {

            if(xhr.status ==419){
                window.location = server_url;
            }

        }
    });

       
        $.ajax({
            url:server_url+"/remove_remaining_returns",
            data:{"_token": cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                this.setState({scReturnTxns:[]});
                this.setState({scReturnTxnsSec:[]});
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

        $.ajax({
            url:server_url+"/remove_remaining_xchange_sc",
            data:{"_token": cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                this.setState({scXchange:[]});
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });


        $.ajax({
            url:server_url+"/remove_remaining_sc",
            data:{"_token": cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                this.setState({scXchange:[]});
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

        let optional_config = {
            mode:"range",
            dateFormat: "d-m-Y"
        };

        $(".newReturnPullUpTxt").flatpickr(optional_config);
        $(".returnPullUpTxt").flatpickr(optional_config);
        $(".dr_dates_txt").flatpickr(optional_config);

        $(".two_cash_txt").numeric();
        $(".two_cash_txt").numeric(false, function() { alert("Integers only"); this.value = ""; this.focus(); });
        $(".two_cash_txt").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
        //$(".two_cash_txt").numeric({ decimal: false, negative: false }, function() { alert("Positive integers only"); this.value = ""; this.focus(); });
        
        $(".one_cash_txt").numeric();
        $(".one_cash_txt").numeric(false, function() { alert("Integers only"); this.value = ""; this.focus(); });
        $(".one_cash_txt").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
        //$(".one_cash_txt").numeric({ decimal: false, negative: false }, function() { alert("Positive integers only"); this.value = ""; this.focus(); });
       
        $(".amt_one").numeric();
        $(".amt_one").numeric(false, function() { alert("Integers only"); this.value = ""; this.focus(); });
        $(".amt_one").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
        //$(".amt_one").numeric({ decimal: false, negative: false }, function() { alert("Positive integers only"); this.value = ""; this.focus(); });
        
        $(".amt_two").numeric();
        $(".amt_two").numeric(false, function() { alert("Integers only"); this.value = ""; this.focus(); });
        $(".amt_two").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
        //$(".amt_two").numeric({ decimal: false, negative: false }, function() { alert("Positive integers only"); this.value = ""; this.focus(); });
       
        $(".split_discount").numeric();
        $(".split_discount").numeric(false, function() { alert("Integers only"); this.value = ""; this.focus(); });
        $(".split_discount").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
        //$(".split_discount").numeric({ decimal: false, negative: false }, function() { alert("Positive integers only"); this.value = ""; this.focus(); });
       
        $(".return_add_cash_amt").numeric();
        $(".return_add_cash_amt").numeric(false, function() { alert("Integers only"); this.value = ""; this.focus(); });
        $(".return_add_cash_amt").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
        //$(".return_add_cash_amt").numeric({ decimal: false, negative: false }, function() { alert("Positive integers only"); this.value = ""; this.focus(); });
       

        $(".cashInput").numeric();
        $(".cashInput").numeric(false, function() { alert("Integers only"); this.value = ""; this.focus(); });
        $(".cashInput").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
        //$(".cashInput").numeric({ decimal: false, negative: false }, function() { alert("Positive integers only"); this.value = ""; this.focus(); });
       
        $(".discountInput").numeric();
        $(".discountInput").numeric(false, function() { alert("Integers only"); this.value = ""; this.focus(); });
        $(".discountInput").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
        //$(".discountInput").numeric({ decimal: false, negative: false }, function() { alert("Positive integers only"); this.value = ""; this.focus(); });
       

        $(".txt_up_qty").numeric();
        $(".txt_up_qty").numeric(false, function() { alert("Integers only"); this.value = ""; this.focus(); });
        $(".txt_up_qty").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
        $(".txt_up_qty").numeric({ decimal: false, negative: false }, function() { alert("Positive integers only"); this.value = ""; this.focus(); });
       
        $(".drawing_txt").numeric();
        $(".drawing_txt").numeric(false, function() { alert("Integers only"); this.value = ""; this.focus(); });
        $(".drawing_txt").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
        $(".drawing_txt").numeric({ decimal: false, negative: false }, function() { alert("Positive integers only"); this.value = ""; this.focus(); });
        
        $(".new_txt_up_qty").numeric();
        $(".new_txt_up_qty").numeric(false, function() { alert("Integers only"); this.value = ""; this.focus(); });
        $(".new_txt_up_qty").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
        $(".new_txt_up_qty").numeric({ decimal: false, negative: false }, function() { alert("Positive integers only"); this.value = ""; this.focus(); });
        
        $(".acc_txt_up_qty").numeric();
        $(".acc_txt_up_qty").numeric(false, function() { alert("Integers only"); this.value = ""; this.focus(); });
        $(".acc_txt_up_qty").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
        $(".acc_txt_up_qty").numeric({ decimal: false, negative: false }, function() { alert("Positive integers only"); this.value = ""; this.focus(); });
        
        $(".acc_trans_txt_up_qty").numeric();
        $(".acc_trans_txt_up_qty").numeric(false, function() { alert("Integers only"); this.value = ""; this.focus(); });
        $(".acc_trans_txt_up_qty").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
        $(".acc_trans_txt_up_qty").numeric({ decimal: false, negative: false }, function() { alert("Positive integers only"); this.value = ""; this.focus(); });
        
        $(".rtrn_txt_up_qty").numeric();
        $(".rtrn_txt_up_qty").numeric(false, function() { alert("Integers only"); this.value = ""; this.focus(); });
        $(".rtrn_txt_up_qty").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
        $(".rtrn_txt_up_qty").numeric({ decimal: false, negative: false }, function() { alert("Positive integers only"); this.value = ""; this.focus(); });
        
        
    },
    getInitialState:function(){
        return{
            titles:[],
            mboxIndex:"",
            mboxQty:"",
            mboxPrice:"",
            totalVat:0,
            totalPrice:0,
            totalItemQty:0,
            searchTxtVal:"",
            itemsTable:[],
            uid:"",
            fname:"",
            lname:"",
            drawerStatus:"",
            drawerError:"",
            autoCompVals:[],
            returnTxns:[],
            custAccTxns:[],
            scReturnTxns:[],
            scReturnTxnsSec:[],
            scXchange:[],
            scAccTxns:[],
            accTxns:[],
            scRetTxnId:"",
            scRetTxnNo:"",
            scRetAccTxnId:"",
            prvTender:"",
            prvDelItem:"",
            prvDraw:"",
            prvAdminLnk:"",
            prvDrawer:"",
            prvReturnItem:"",
            prvMngItem:"",
            prvMngUsers:"",
            prvMngTaxes:"",
            prvMngGoods:"",
            prvMngBranches:"",
            prvMngRemoteBranches:"",
            customerData:[],
            tornamentData:[],
            heldCustTtl:"",
            heldCustId:"",
            heldCustType:"",
            heldDataRprt:[],
            heldTransId:"",
            change: {},
            accTabVal:"",
            accFname:"",
            accLName:"",
            accOrg:"",
            accValue:"",
            accId:"",
            accItem:"",
            accCust:"",
            accTtlQty:"",
            accEvent:"",
            accXchangeValue:"",
            paymentType:"",
            paymentTypeTitle:"",
            upCeilPrice:"",
            upFloorPrice:"",
            oneSplitType:"",
            onSplitTitle:"",
            twoSplitType:"",
            twoSplitTitle:"",
            returnAddTitle:"",
            rtrnScId:"",
            rowDrawerItem:[]
        }
    },
    showQtyDialog: function(i, event){
        $(".item_qty").fadeIn('slow');
        $('.new_txt_up_qty').focus();
       
        this.setState({mboxIndex:i.item_id});
        this.setState({mboxQty:i.qty});
         /*
        $(".item_qty").fadeIn('slow');
        $('.new_txt_up_qty').focus();
        */
    },showPriceDialog: function(i, event){
        $(".price_qty").fadeIn('slow');
        $(".txt_up_qty").focus();
        this.setState({mboxIndex:i.item_id});
        this.setState({mboxPrice:i.price});
        this.setState({upFloorPrice:i.floor});
        this.setState({upCeilPrice:i.ceil});
    },
    sbPropFunc:function(){

        if(this.state.prvTender ==1){
            let search_val = $('.search_txt').val();
            let search_len = search_val.length;
            let val_type;

        if(isNaN(search_val)){
            
            val_type = "string";
        }else{
 
            val_type = "number";
        }
        
        if(search_len >= 5){

            $.ajax({
                url:server_url+"/search_items",
                data:{search_val:search_val,val_type:val_type,"_token": cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);
    
                    if(res.status !='0' && res.status !='X' && res.status !='Y' && res.status !='G'){
                        this.popMbox(res);
                        this.popTotals();
                    }else if(res.status =='X'){
                        $('.itemPullUpTxt').val('');
                        $('.close_drawer_conf').show();
                        $(".top_msg").html("<p>Error: Drawer Closed</p>");
                    }else if(res.status =='Y'){
                        $('.itemPullUpTxt').val('');
                        $('.close_drawer_conf').show();
                        $(".top_msg").html("<p>Error: Drawer Expired</p>");
                    }else if(res.status =='G'){
                        $('.itemPullUpTxt').val('');
                        $('.close_drawer_conf').show();
                        $(".top_msg").html("<p>Error: Item Not Received In Store</p>");
                    
                    }
    
                },error: function(xhr, status, text) {
    
                    if(xhr.status ==419){
                        window.location = server_url;
                    }
    
                }
            });

        }
        

        }else{
            $('.close_drawer_conf').show();
            $(".top_msg").html("<p>Error: You are not permitted to Tender</p>");
                
        }
        
       
    },
    upPriceEntBtn:function(e){
        if (e.key === 'Enter'){
            let new_price = $('.txt_up_qty').val();
            let sc_id = this.state.mboxIndex;
           
            if(new_price !=""){
                $.ajax({
                    url:server_url+"/update_price",
                    data:{new_price:new_price,sc_id:sc_id,"_token":cs_token},
                    type:"POST",
                    context: this,
                    success:function(data){
                        console.log(data);
                        
                        let res = JSON.parse(data);
                        this.popMbox(res);
                        this.popTotals();

                        $(".itemPullUpTxt").focus();
                        $(".price_qty").fadeOut('slow');
                        $('.txt_up_qty').val('');
                        
                    },error: function(xhr, status, text) {

                        if(xhr.status ==419){
                            window.location = server_url;
                        }
    
                    }
                });
            }
        }
    },
    transEnterBtn:function(e){
        if (e.key === 'Enter'){

            let held_id = this.state.heldTransId;

            if(held_id ==""){
                $.ajax({
                    url:server_url+"/check_sc",
                    data:{"_token":cs_token},
                    type:"POST",
                    context: this,
                    success:function(data){
                        let res = JSON.parse(data);
                        if(res.cart_count > 0){
                            $('.search_txt').val('');
                            $('.new_tender').fadeIn('slow');
                            $('.cashInput').focus(); 
    
                            //Full screen Logic
                            var el = document.documentElement, rfs = el.requestFullScreen || el.webkitRequestFullScreen || el.mozRequestFullScreen;   
                            rfs.call(el);
                        }
                    }
                });

            }else{
                $.ajax({
                    url:server_url+"/check_held_sc",
                    data:{held_id:held_id,"_token":cs_token},
                    type:"POST",
                    context: this,
                    success:function(data){
                        let res = JSON.parse(data);
                        if(res.cart_count > 0){
                            $('.search_txt').val('');
                            $('.new_tender').fadeIn('slow');
                            $('.cashInput').focus(); 
                        }
                    }
                });
            }
     
            
            $.ajax({
                url:server_url+"/csr_update",
                data:{csr_val:1,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                }
            });

        }  
    },
    handlePriceChange:function(propertyName, event){
        this.setState({ [event.target.name]: event.target.value });
        if(event.target.value !=""){
            if(event.target.value !=0){
                
                let qty = $("input[name=m_qty_item_"+propertyName+"]").val(); 
                
                $.ajax({
                    url:server_url+"/mbox_price_update",
                    data:{qty:qty,sc_id:propertyName,price:event.target.value,"_token":cs_token},
                    type:"POST",
                    context: this,
                    success:function(data){
                        console.log(data);
                        let res = JSON.parse(data);
                        this.popMbox(res);
                        this.popTotals();
                    },error: function(xhr, status, text) {

                        if(xhr.status ==419){
                            window.location = server_url;
                        }
        
                    }
                });
                
            }
        }
    },
    handleChange: function (propertyName, event) {
    
        if(event.target.value !=""){
            if(event.target.value !=0){
                $.ajax({
                    url:server_url+"/mbox_qty_update",
                    data:{sc_id:propertyName,qty:event.target.value,"_token":cs_token},
                    type:"POST",
                    context: this,
                    success:function(data){
                        console.log(data);
                        let res = JSON.parse(data);
                        this.popMbox(res);
                        this.popTotals();
                    },error: function(xhr, status, text) {

                        if(xhr.status ==419){
                            window.location = server_url;
                        }
        
                    }
                });
            }
        }
    },
    newDelItem:function(item,i){
        $.ajax({
            url:server_url+"/del_sc_item",
            data:{item:item,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                console.log(data);
                let res = JSON.parse(data);
                this.popMbox(res);
                this.popTotals();
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        }); 
    },
    highlightValues:function(propertyName, event){
        
        document.getElementById("m_qty_item_"+propertyName).focus();
        document.getElementById("m_qty_item_"+propertyName).select();
       
    },
    eachTitle:function(item,i){
        if(this.state.prvDelItem ==1){

            return(
                <div key={i} className='wrap_fake_row_desc'>
                    <div className='fake_tbl_row_desc' >
                        <div className='fake_col_desc'>{item.code_no}</div>
                        <div className='fake_col_item_desc'>{item.item_name}</div>
                        <div className='fake_col_del'><div onClick={this.newDelItem.bind(this, item.id)} className="del_mbox_item"></div></div>
                           
                        
                    </div>
    
                    <div className='fake_tbl_row_desc' >
                        <div className='fake_col_desc'>
                            <div onClick={this.showQtyDialog.bind(this, {"item_id":item.item,"price":item.price})} className='item_qty_holder'>{item.qty}</div>
                        </div>
                        <div className='fake_col_right_price'>
                            <div onClick={this.showPriceDialog.bind(this, {"item_id":item.item,"price":item.price,"ceil":item.ceil_price,"floor":item.floor_price})} className='item_price_holder'>{item.price}</div>
                        </div>
                        <div className='fake_col_right'>
                            <div className='item_total_holder'>{item.total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}</div>
                        </div>
                        
                            
                       
                   
                    </div>
                </div>
            )

        }else{

            return(
                <div key={i} className='wrap_fake_row_desc'>
                    <div className='fake_tbl_row_desc' >
                        <div className='fake_col_desc'>{item.code_no}</div>
                        <div className='fake_col_item_desc'>{item.item_name}</div>
                         
                        
                    </div>
    
                    <div className='fake_tbl_row_desc' >
                        <div className='fake_col_desc'>
                            <div onClick={this.showQtyDialog.bind(this, {"item_id":item.item,"price":item.price})} className='item_qty_holder'>{item.qty}</div>
                        </div>
                        <div className='fake_col_right_price'>
                            <div onClick={this.showPriceDialog.bind(this, {"item_id":item.item,"price":item.price,"ceil":item.ceil_price,"floor":item.floor_price})} className='item_price_holder'>{item.price}</div>
                        </div>
                        <div className='fake_col_right'>
                            <div className='item_total_holder'>{item.total}</div>
                        </div>
                        
                            
                       
                   
                    </div>
                </div>
            )
        }
        
        /*
        
        /*
        if(this.state.prvDelItem ==1){
            return( 
                <tr key={i}>
                    <td>{item.code_no}</td><td>{item.item_name}</td><td onClick={this.showQtyDialog.bind(this, {"item_id":item.item,"price":item.price})}><div className='item_qty_holder'>{item.qty }</div></td><td></td><td onClick={this.showPriceDialog.bind(this, {"item_id":item.item,"price":item.price,"ceil":item.ceil_price,"floor":item.floor_price})}><div className='item_price_holder'>{item.price}</div></td><td>{item.total}.00</td><td><div onClick={this.newDelItem.bind(this, item.id)} className="del_mbox_item"></div></td>      
                </tr>)
        }else{
            return( 
                <tr key={i}>
                    <td>{item.code_no}</td><td>{item.item_name}</td><td onClick={this.showQtyDialog.bind(this, {"item_id":item.item,"price":item.price})}><div className='item_qty_holder'>{item.qty }</div></td><td></td><td onClick={this.showPriceDialog.bind(this, {"item_id":item.item,"price":item.price,"ceil":item.ceil_price,"floor":item.floor_price})}><div className='item_price_holder'>{item.price}</div></td><td>{item.total}.00</td><td></td>      
                </tr>)
        }
        */
    },
    selReturnAccTxn:function(i,txn_no){
        $('#scReturnTxnNo').html(`<b>Receipt No: ${txn_no} </b>`);
        
        this.setState({scRetAccTxnId:i});
        console.log(i);
        $.ajax({
            url:server_url+"/fetch_return_txn",
            data:{txn:i,txn_no:txn_no,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let res = JSON.parse(data);
                
                let arr = [];
                                
                $.each(res,function(index,value){
                    arr.push(res[index]);
                });

                this.setState({scAccTxns:arr});
            

            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        }); 
    },
    selReturnTxn:function(i,txn_no){
        
        $('#scReturnTxnNo').html(`<b>Receipt No: ${txn_no} </b>`);
      
        this.setState({scRetTxnId:i});
        this.setState({scRetTxnNo:txn_no});

        
        $.ajax({
            url:server_url+"/fetch_return_txn",
            data:{txn:i,txn_no:txn_no,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                console.log(data);
                let res = JSON.parse(data);
                if(res.status != 0){
                    let arr = [];
                                
                    $.each(res,function(index,value){
                        arr.push(res[index]);
                    });
    
                    this.setState({scReturnTxns:arr});
                }
               
            

            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        }); 
        
    },
    returnAccTransBtn:function(){
       
        $.ajax({
            url:server_url+"/return_all_for_xchange_txns",
            data:{"_token":cs_token},
            type:"POST",
            context:this,
            success:function(data){
                //console.log(data);
                this.setState({scXchange:[]});
                $('.msg_wrp').html("<div class='info'>Success</div>");
    
                //scXchange
            }
        });

    },
    qtyUpAccTransClick:function(){
        let qty = $('.acc_trans_txt_up_qty').val();
        let sc_id = this.state.accItem;
        if(qty !=""){

            $.ajax({
                url:server_url+"/return_for_xchange",
                data:{sc_id:sc_id,qty:qty,"_token":cs_token},
                type:"POST",
                context:this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);

                    $('.acc_trans_item_qty').fadeOut('slow');
                    $('.acc_trans_txt_up_qty').val('');

                    $('.hdn_return_acc_btn').show();
                    
                    let arr = [];
                      
                    $.each(res,function(index,value){
                        arr.push(res[index]);
                    });
                    this.setState({scXchange:arr});
                
    
                },error: function(xhr, status, text) {
    
                    if(xhr.status ==419){
                        window.location = server_url;
                    }
    
                }
            }); 

        }
    },
    removeTransSCItem:function(item){
        
        this.setState({accItem:item});
        $('.acc_trans_item_qty').fadeIn('slow');
        $('.acc_trans_txt_up_qty').focus();
    },
    removeAccSCItem:function(item){
        $.ajax({
            url:server_url+"/remove_acc_sc_item",
            data:{sc_id:item,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                console.log(data);

                let customer = $('.txt_add_cust_wrap').val();

                $.ajax({
                    url:server_url+"/accounts_txns",
                    data:{customer:customer,"_token": cs_token},
                    type:"POST",
                    context: this,
                    success:function(data){
                        
                        let res = JSON.parse(data);
                        //console.log(res.fname);
                        
                        let arr = [];
                                        
                        $.each(res,function(index,value){
                            arr.push(res[index]);
                        });
    
                        this.setState({accTxns:arr});
                    
                    }
                });

                $.ajax({
                    url:server_url+"/fetch_acc_sc_items",
                    data:{sc_id:item,"_token":cs_token},
                    type:"POST",
                    context: this,
                    success:function(data){
                        let res = JSON.parse(data);
                    
                        let arr = [];
                                        
                        $.each(res,function(index,value){
                            arr.push(res[index]);
                        });
                        
                        this.setState({scAccTxns:arr});
                    }
                });
                

            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        }); 
    },
    qtyUpRtrnClick:function(){
        let sc_id = this.state.rtrnScId;
        let qty = $('.rtrn_txt_up_qty').val();
        
        if(qty !="" && qty !=0){

            $.ajax({
                url:server_url+"/return_sc_item",
                data:{sc_id:sc_id,qty:qty,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    console.log(data);
                    let res = JSON.parse(data);
                    $('.rtrn_txt_up_qty').val('');
                    let arr = [];
                                    
                    $.each(res,function(index,value){
                        arr.push(res[index]);
                    });
    
                    this.setState({scReturnTxnsSec:arr});
                
    
                },error: function(xhr, status, text) {
    
                    if(xhr.status ==419){
                        window.location = server_url;
                    }
    
                }
            });

            let txnId = this.state.scRetTxnId;
            let txnNo = this.state.scRetTxnNo;
            
            $.ajax({
                url:server_url+"/fetch_return_txn",
                data:{txn:txnId,txn_no:txnNo,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);
                    if(res.status != 0){
                        let arr = [];
                                    
                        $.each(res,function(index,value){
                            arr.push(res[index]);
                        });
        
                        this.setState({scReturnTxns:arr});
                    }
                
                

                },error: function(xhr, status, text) {

                    if(xhr.status ==419){
                        window.location = server_url;
                    }

                }
            }); 

            $('.rtrn_item_qty').fadeOut('slow');

        }
         

    },
    returnSCItem:function(item){
        
        $('.rtrn_item_qty').fadeIn('slow');
        $('.rtrn_txt_up_qty').focus();
        this.setState({rtrnScId:item});
    
    },
    qtyUpAccClick:function(){
        
        let id = this.state.accId;
        let item = this.state.accItem;
        let customer = this.state.accCust;
        let ttl_qty = this.state.accTtlQty;
        let event = this.state.accEvent;
        
        let qty = $('.acc_txt_up_qty').val();

        if(qty !="" && qty !=0){

            $.ajax({
                url:server_url+"/new_add_sc_acc_item",
                data:{event:event,qty:qty,customer:customer,id:id,item:item,ttl_qty:ttl_qty,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);
                    $('.acc_item_qty').fadeOut('slow');
                    let arr = [];

                    $('.acc_txt_up_qty').val('');
                                    
                    $.each(res,function(index,value){
                        arr.push(res[index]);
                    });
                    
                    this.setState({scAccTxns:arr});

                    
                
    
                },error: function(xhr, status, text) {
    
                    if(xhr.status ==419){
                        window.location = server_url;
                    }
    
                }
            });
            

            $.ajax({
                url:server_url+"/accounts_txns_update",
                data:{customer:customer,"_token": cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    let res = JSON.parse(data);
                    //console.log(res.fname);
                    
                    let arr = [];
                                    
                    $.each(res,function(index,value){
                        arr.push(res[index]);
                    });

                    this.setState({accTxns:arr});
                
                }
            });

        }
        
    },
    accSCItem:function(id,item,customer,ttl_qty,event){
        
        $('.acc_item_qty').fadeIn('slow');
        $('.acc_txt_up_qty').focus();
        
        this.setState({accId:id});
        this.setState({accItem:item});
        this.setState({accCust:customer});
        this.setState({accTtlQty:ttl_qty});
        this.setState({accEvent:event});
            
    },
    removeXchange:function(item){
        $.ajax({
            url:server_url+"/remove_xchange_sc",
            data:{item:item,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                $.ajax({
                    url:server_url+"/sc_items",
                    data:{"_token": cs_token},
                    type:"POST",
                    context: this,
                    success:function(data){
                        //console.log(data);
                        let res = JSON.parse(data);
                        $('.accItemPullUp').val('');
                        if(res.status !='0' && res.status !='X' && res.status !='Y' && res.status !='G'){
                            //this.popMbox(res);
                            //this.popTotals();

                            let arr = [];
                            $.each(res,function(index,value){
                                arr.push(res[index]);
                            });
                            this.setState({scXchange:arr});
        
                            $('.accItemPullUp').fadeOut('slow');
                            $(".msg_holder").empty();
                        }
                    },error: function(xhr, status, text) {

                        if(xhr.status ==419){
                            window.location = server_url;
                        }
    
                    }
                });
            
                $.ajax({
                    url:server_url+"/xchange_sc_totals",
                    data:{"_token": cs_token},
                    type:"POST",
                    context: this,
                    success:function(data){
                        let res = JSON.parse(data);
                        this.setState({accValue:res.acc_total});
                        this.setState({accXchangeValue:res.xchange});
        
                    }
                });

            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        }); 
    },
    accRow:function(item,i){
        return(
            <tr key={i}>
                <td>{item.no}</td><td>{item.item}</td><td>{item.event_name}</td><td>{item.ttl_qty}</td><td><div onClick={this.accSCItem.bind(this,item.id,item.item_id,item.customer,item.ttl_qty,item.event)} className="add_tray"></div></td>         
            </tr>)
    },
    accSecondRow:function(item,i){
        return(
            <tr key={i}>
                <td>{item.item}</td><td>{item.qty}</td><td>{item.total}</td><td><div onClick={this.removeAccSCItem.bind(this,item.id)} className="remove_tray"></div></td>         
            </tr>)
    },
    transSecondRow:function(item,i){
        return(
            <tr key={i}>
                <td>{item.item}</td><td>{item.qty}</td><td>{item.total}</td><td><div onClick={this.removeTransSCItem.bind(this,item.id)} className="remove_tray"></div></td>         
            </tr>)
    },
    returnRowDets:function(item,i){
        return(
            <tr key={i}>
                <td>{item.item}</td><td>{item.qty}</td><td>{item.total}</td><td><div onClick={this.returnSCItem.bind(this,item.id)} className="remove_tray"></div></td>         
            </tr>)
    },
    returnRowDetsTwo:function(item,i){
        return(
            <tr key={i}>
                <td>{item.item}</td><td>{item.qty}</td><td>{item.total}</td><td></td>         
            </tr>)
    },
    xChangeRow:function(item,i){
        return(
            <tr key={i}>
                <td>{item.item_name}</td><td>{item.qty}</td><td>{item.total}</td><td><div onClick={this.removeXchange.bind(this,item.id)} className="remove_tray"></div></td>         
            </tr>)
    },
    newSearchReturnPullUp:function(){
        var search = $('.newReturnPullUpTxt').val();
           
        if(search !=""){
            
            $.ajax({
                url:server_url+"/new_search_trans_reports",
                data:{search:search,"_token": cs_token},
                type:"POST",
                context: this,
                success:function(data){
                   // console.log(data);
                   
                    let res = JSON.parse(data);
                    
                    let arr = [];
                                    
                    $.each(res,function(index,value){
                        arr.push(res[index]);
                    });
    
                    this.setState({returnTxns:arr});
                    
                }
            });

        }
    },
    searchReturnPullUp:function(){
        var search = $('.returnPullUpTxt').val();
           
        if(search !=""){
            
            $.ajax({
                url:server_url+"/search_trans_reports",
                data:{search:search,"_token": cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    console.log(data);
                   
                    let res = JSON.parse(data);
                    
                    let arr = [];
                                    
                    $.each(res,function(index,value){
                        arr.push(res[index]);
                    });
    
                    this.setState({returnTxns:arr});
                    
                }
            });

        }
       
    },
    printTrans:function(item){
        
        $.ajax({
           // url:server_url+"/print_duplicate_receipt",
            url:server_url+"/create_pdf_receipt",
            data:{id:item,"_token": cs_token},
            type:"GET",
            context: this,
            success:function(data){
                console.log(data);
               
            }
        });

    },
    delAccItem:function(id){
        $.ajax({
            url:server_url+"/del_sc_acc_item",
            data:{id:id,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let res = JSON.parse(data);
                
                let arr = [];
                 if(res.status !=0){
                    $.each(res,function(index,value){
                        arr.push(res[index]);
                    });
                    
                    this.setState({scAccTxns:arr});
                 }else{
                    this.setState({scAccTxns:[]});
                 }            
               
            

            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        }); 
    },
    scAccRow:function(item,i){
        return(
            <tr key={i}>
                <td>{item.item}</td><td>{item.qty}</td><td>{item.total}</td><td><div onClick={this.delAccItem.bind(this,item.id)} className="remove_tray"></div></td>         
            </tr>)
    },
    returnRow:function(item,i){
        if(this.state.prvReturnItem == 1){
            return(
                <tr key={i}>
                    <td>{item.trans_time}</td><td>{item.receipt_no}</td><td>{item.no_items}</td><td>{item.total}</td><td><div onClick={this.selReturnTxn.bind(this,item.id,item.receipt_no)} className="add_tray"></div></td><td><a href={server_url+"/create_pdf_receipt?id="+item.id} className='rprt_link'>Print</a></td>         
                </tr>)
        }else{
            return(
                <tr key={i}>
                    <td>{item.trans_time}</td><td>{item.receipt_no}</td><td>{item.no_items}</td><td>{item.total}</td><td></td><td><a href={server_url+"/create_pdf_receipt?id="+item.id} className='rprt_link'>Print</a></td>         
                </tr>)
        }
        
    },
    returnAccRow:function(item,i){
        if(this.state.prvReturnItem == 1){
            return(
                <tr key={i}>
                    <td>{item.trans_time}</td><td>{item.receipt_no}</td><td>{item.no_items}</td><td>{item.item}</td><td>{/*<div onClick={this.selReturnAccTxn.bind(this,item.id,item.receipt_no)} className="add_tray"></div> */}</td><td></td>         
                </tr>)
        }else{
            return(
                <tr key={i}>
                    <td>{item.trans_time}</td><td>{item.receipt_no}</td><td>{item.no_items}</td><td>{item.item}</td><td></td><td></td>         
                </tr>)
        }
        
    },
    clsMinDialog:function(i,event){

        $('.rtrn_txt_up_qty').val('');
        $('.acc_txt_up_qty').val('');
        $('.acc_trans_txt_up_qty').val('');

        $("."+i).fadeOut('slow');
    },
    clsAltDialog:function(i,event){
        $('.txt_add_cust_wrap').val('');

        $(".tblTabTxns").hide();
        $("#tblSecondTrans").hide();

        $('.tblTabAcc').show();
        $("#tblSecondAcc").show();

        $('.newReturnPullUpTxt').val('');
        
        $("."+i).fadeOut('slow');

        $(".msg_wrp").empty();

        $('.top_msg').empty();
        $('.drawer_msg').empty();
        $('#scReturnTxnNo').empty();

        

        $('.itemPullUpTxt').focus();
       
        $.ajax({
            url:server_url+"/remove_remaining_xchange_sc",
            data:{"_token": cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                this.setState({scXchange:[]});
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

        $.ajax({
            url:server_url+"/remove_remaining_returns",
            data:{"_token": cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                this.setState({scReturnTxns:[]});
                this.setState({scReturnTxnsSec:[]});
                this.setState({accTxns:[]});
            }
        });
        
        $.ajax({
            url:server_url+"/remove_remaining_sc",
            data:{"_token": cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                this.setState({scReturnTxns:[]});
                this.setState({scReturnTxnsSec:[]});
                this.setState({titles:[]}); 
            }
        });

        $.ajax({
            url:server_url+"/sc_totals",
            data:{"_token": cs_token},
            type:"POST",
            context: this,
            success:function(data){
                console.log(data);
                let res = JSON.parse(data);
                this.setState({totalVat:res.sum_tax});
                this.setState({totalPrice:res.sum_total});
                this.setState({totalItemQty:res.sum_qty});
                
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

    },
    clsDialog:function(i,event){
        $("."+i).fadeOut('slow');
        $('.itemPullUpTxt').focus();
        $('.returnPullUpTxt').val('');
        
        $('.newReturnPullUpTxt').val('');
        $('.cashInput').val('');
        $('.mpesaInput').val('');
        $('.chequeInput').val('');
        $('.returnInput').val('');
        $('.drawing_txt').val('');
        $('.drawing_comm').val('');
        $('.txt_up_qty').val();
        $('.msg_wrp').empty();
        $('.txt_add_cust_wrap').val('');
        $('.itemPullUpTxt').val('');
                        
        $('#scReturnTxnNo').empty();
        $(".msg_holder").empty();

        this.setState({scAccTxns:[]});

        $('.payment_value').hide();
        $('.payment_split').hide();
        $('.payment_split_opt').hide();
        $('.split_one').hide();
        $('.split_two').hide();
        $('.paymentChoices').show();
        $('.msg_wrap').empty();
        
        $('.rtrn_add_opts').val('');
        $('.return_add_cash_amt').val('');
        $('.return_add_ref_no').val('');
        $('.return_cover').hide();
        $('.return_add_cash_amt').hide();
        $('.return_add_cash_amt').hide();

        /*
        $.ajax({
            url:server_url+"/remove_remaining_returns",
            data:{"_token": cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                this.setState({scReturnTxns:[]});
                this.setState({scReturnTxnsSec:[]});
                this.setState({accTxns:[]});
            }
        });
        
        $.ajax({
            url:server_url+"/remove_remaining_sc",
            data:{"_token": cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                this.setState({scReturnTxns:[]});
                this.setState({scReturnTxnsSec:[]});
            }
        });
        */
        $.ajax({
            url:server_url+"/remove_remaining_xchange_sc",
            data:{"_token": cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                this.setState({scXchange:[]});
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

        $('.tblTabAcc').show();
        $('.tblTabTxns').hide();

    },
    popMbox(res){
        
        let arr = [];
                                
        $.each(res,function(index,value){
            arr.push(res[index]);
        });
       
        this.setState({titles:arr}); 
        $('.search_txt').val('');
    },popTotals(){

        $.ajax({
            url:server_url+"/sc_totals",
            data:{"_token": cs_token},
            type:"POST",
            context: this,
            success:function(data){
                console.log(data);
                let res = JSON.parse(data);
                this.setState({totalVat:res.sum_tax});
                this.setState({totalPrice:res.sum_total});
                this.setState({totalItemQty:res.sum_qty});
                
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

    },selPopTotals(tid){

        $.ajax({
            url:server_url+"/sel_sc_totals",
            data:{tid:tid,"_token": cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let res = JSON.parse(data);
                this.setState({totalVat:res.sum_tax});
                this.setState({totalPrice:res.sum_total});
                this.setState({totalItemQty:res.sum_qty});
                
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

    },
    priceUp:function(i, event){
        let new_price;
        if(i =='-'){

        }else if(i =='+'){

        }
    },
    upQtyEntBtn:function(e){
        if (e.key === 'Enter'){
            let new_qty = $('.new_txt_up_qty').val();

            if(new_qty !="" && new_qty !=0){
                this.setState({mboxQty:new_qty});
                $.ajax({
                    url:server_url+"/update_sc_qty",
                    data:{qty:new_qty,item:this.state.mboxIndex,"_token":cs_token},
                    type:"POST",
                    context: this,
                    success:function(data){
                        //console.log(data);
                        let res = JSON.parse(data);
                        this.popMbox(res);
                        this.popTotals();
                        $('.new_txt_up_qty').val('');
                        $('.item_qty').fadeOut();
                        $('.itemPullUpTxt').focus();
                    }
                });
            }
            
        }
    },
    qtyUpClick:function(){
        let new_qty = $('.new_txt_up_qty').val();

            if(new_qty !="" && new_qty !=0){
                this.setState({mboxQty:new_qty});
                $.ajax({
                    url:server_url+"/update_sc_qty",
                    data:{qty:new_qty,item:this.state.mboxIndex,"_token":cs_token},
                    type:"POST",
                    context: this,
                    success:function(data){
                        //console.log(data);
                        let res = JSON.parse(data);
                        this.popMbox(res);
                        this.popTotals();
                        $('.new_txt_up_qty').val('');
                        $('.item_qty').fadeOut();
                        $('.itemPullUpTxt').focus();
                    }
                });
            }
    },
    qtyUpEntBtn:function(e){
        if (e.key === 'Enter'){

            let new_qty = $('.new_txt_up_qty').val();

            if(new_qty !=""){
                this.setState({mboxQty:new_qty});
                $.ajax({
                    url:server_url+"/update_sc_qty",
                    data:{qty:new_qty,item:this.state.mboxIndex,"_token":cs_token},
                    type:"POST",
                    context: this,
                    success:function(data){
                        console.log(data);
                        let res = JSON.parse(data);
                        this.popMbox(res);
                        this.popTotals();
                        $('.new_txt_up_qty').val('');
                        $('.item_qty').fadeOut();
                        $('.itemPullUpTxt').focus();
                    }
                });
            }
            
        }
        

    },
    qtyUp:function(i, event){
        
        let new_qty;
        if(i =='-'){
            if(this.state.mboxQty > 1){
                new_qty = this.state.mboxQty - 1;
                //console.log(new_qty);
                this.setState({mboxQty:new_qty});
                $.ajax({
                    url:server_url+"/update_sc_qty",
                    data:{qty:new_qty,item:this.state.mboxIndex,"_token":cs_token},
                    type:"POST",
                    context: this,
                    success:function(data){
                        //console.log(data);
                        let res = JSON.parse(data);
                        this.popMbox(res);
                        this.popTotals();
                    }
                });
            }
        }else if(i =='+'){
            new_qty = this.state.mboxQty + 1;
            //console.log(new_qty);
            this.setState({mboxQty:new_qty});
            $.ajax({
                url:server_url+"/update_sc_qty",
                data:{qty:new_qty,item:this.state.mboxIndex,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    console.log(data);
                    let res = JSON.parse(data);
                    this.popMbox(res);
                    this.popTotals();
                }
            });
        }
    },delItem:function(){
        $.ajax({
            url:server_url+"/del_sc_item",
            data:{item:this.state.mboxIndex,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                console.log(data);
                let res = JSON.parse(data);
                this.popMbox(res);
                this.popTotals();
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });
    },tender:function(){
        var cash = $('.cashInput').val().replace(",","");
        let held_id = this.state.heldTransId;
        let customer = $('.txt_add_cust_wrap').val();
       
        if(cash ==""){

            $.ajax({
                url:server_url+"/check_sc",
                data:{"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    let res = JSON.parse(data);
                    if(res.cart_count > 0){
                        $('.search_txt').val('');
                        $('.new_tender').fadeIn('slow');
                        
                        $('.cashInput').focus(); 
                    }
                },error: function(xhr, status, text) {

                    if(xhr.status ==419){
                        window.location = server_url;
                    }

                }
            });

            $.ajax({
                url:server_url+"/csr_update",
                data:{csr_val:1,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                }
            });

            if($('.changeInput').val() !=""){

                $('.change').fadeOut('slow');
                $('.itemPullUpTxt').focus();
                $('.changeInput').val('');

                $.ajax({
                    url:server_url+"/csr_update",
                    data:{csr_val:0,"_token":cs_token},
                    type:"POST",
                    context: this,
                    success:function(data){
                        //console.log(data);
                    }
                });

                $.ajax({
                    url:server_url+"/sc_items",
                    data:{"_token": cs_token},
                    type:"POST",
                    context: this,
                    success:function(data){
                        //console.log(data);
                        let res = JSON.parse(data);
                        if(res.status !=0){
                            this.popMbox(res);
                            this.popTotals();
                            
                            $('.msg_wrap').empty();
    
                        }
                    },error: function(xhr, status, text) {

                        if(xhr.status ==419){
                            window.location = server_url;
                        }
    
                    }
                });

            }

        }else{            
               
            if(parseInt(cash.replace(",","")) >= parseInt(this.state.totalPrice.replace(",",""))){
                            
                            $.ajax({
                                url:server_url+"/tender_trans",
                                data:{customer:customer,held_id:held_id,cash:cash,"_token":cs_token},
                                type:"POST",
                                context: this,
                                success:function(data){
                                    //console.log(data);
                                   
                                    var res = JSON.parse(data);
                                    if(res.status ==1){
            
                                        $('.cashInput').val('');
                                        $('.new_tender').hide();
                                        $('.txt_add_cust_wrap').val('');
                                        $('.msg_wrap').html("<div class='info'><p>Transaction Completed</p></div>");
                                        $('.change').fadeIn('slow');
                                        $('.changeInput').focus();
                                        $('.changeInput').val(res.change);
            
                                    }
                                    
                                },error: function(xhr, status, text) {

                                    if(xhr.status ==419){
                                        window.location = server_url;
                                    }
                
                                }
                            });
                        

            }else{
                $('.msg_wrap').html("<div class='err'><b>Error: Cash Not Enough</b></div>");
            }
  
        }
        

    },tenderTrans:function(e){

    
        if (e.key === 'Enter'){
            
            let cash = $('.cashInput').val().replace(",","");
            let held_id = this.state.heldTransId;
            let customer = $('.txt_add_cust_wrap').val();
           
            if(parseInt(cash.replace(",", "")) >= parseInt(this.state.totalPrice.replace(",", ""))){
                
                $.ajax({
                    url:server_url+"/tender_trans",
                    data:{customer:customer,held_id:held_id,cash:cash,"_token":cs_token},
                    type:"POST",
                    context: this,
                    success:function(data){
                        //console.log(data);
                        var res = JSON.parse(data);
                        if(res.status ==1){
                            
                            $('.cashInput').val('');
                            $('.new_tender').hide();
                            $('.msg_wrap').html("<div class='info'><p>Transaction Completed</p></div>");
                            $('.change').fadeIn('slow');
                            $('.changeInput').focus();
                            $('.txt_add_cust_wrap').val('');
                            $('.changeInput').val(res.change);
                        }
                    },error: function(xhr, status, text) {

                        if(xhr.status ==419){
                            window.location = server_url;
                        }
    
                    }
                });

            }else{
                $('.msg_wrap').html("<div class='err'><b>Error: Cash Not Enough</b></div>");
            }
            
          }
          
    },
    transChangeClick:function(){

        $('.change').fadeOut('slow');
        $('.itemPullUpTxt').focus();
        $('.changeInput').val('');
        $('.txt_add_cust_wrap').val('');

        $.ajax({
            url:server_url+"/csr_update",
            data:{csr_val:0,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
            }
        });

        $.ajax({
            url:server_url+"/sc_items",
            data:{"_token": cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let res = JSON.parse(data);
                if(res.status !=0){
                    this.popMbox(res);
                    this.popTotals();
                    
                    $('.msg_wrap').empty();

                }
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });


        $.ajax({
            url:server_url+"/fetch_items",
            data:{"_token": cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let res = JSON.parse(data);

                let arr = [];           
                $.each(res,function(index,value){
                    arr.push(res[index]);
                });
                this.setState({itemsTable:arr}); 

                
               
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        }); 

    },
    transChange:function(e){
        if (e.key === 'Enter'){
            $('.change').fadeOut('slow');
            $('.itemPullUpTxt').focus();
            $('.changeInput').val('');
            $('.txt_add_cust_wrap').val('');

            $.ajax({
                url:server_url+"/csr_update",
                data:{csr_val:0,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                }
            });

            $.ajax({
                url:server_url+"/sc_items",
                data:{"_token": cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);
                    if(res.status !=0){
                        this.popMbox(res);
                        this.popTotals();
                        
                        $('.msg_wrap').empty();

                    }
                },error: function(xhr, status, text) {

                    if(xhr.status ==419){
                        window.location = server_url;
                    }

                }
            });

            $.ajax({
                url:server_url+"/fetch_items",
                data:{"_token": cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);
    
                    let arr = [];           
                    $.each(res,function(index,value){
                        arr.push(res[index]);
                    });
                    this.setState({itemsTable:arr}); 
    
                    
                   
                },error: function(xhr, status, text) {
    
                    if(xhr.status ==419){
                        window.location = server_url;
                    }
    
                }
            }); 

        }
    },
    dialPadBtn:function(i,event){
            let srch_val = $('.search_txt').val();
            let cash_val = $('.cashInput').val().replace(",","");
            let output_val;
        
            $.ajax({
                url:server_url+"/csr_check",
                data:{csr_val:1,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);

                    if(res.val ==1){

                        if(i =='<-'){
                            output_val = cash_val.slice(0,-1);
                            $('.cashInput').val(output_val).replace(",","");
                            this.setState({searchTxtVal:output_val});
                        }else{
                            output_val = cash_val + i;
                            $('.cashInput').val(output_val).replace(",","");
                            this.setState({searchTxtVal:output_val});
                            
                        }

                    }else{

                        if(i =='<-'){
                            output_val = srch_val.slice(0,-1);
                            $('.search_txt').val(output_val);
                            this.setState({searchTxtVal:output_val});
                        }else{
                            output_val = srch_val + i;
                            $('.search_txt').val(output_val);
                            this.setState({searchTxtVal:output_val});
                            
                        }

                    }

                    //console.log(output_val);
                    $.ajax({
                        url:server_url+"/search_items",
                        data:{search_val:output_val,"_token": cs_token},
                        type:"POST",
                        context: this,
                        success:function(data){
                            //console.log(data);
                            let res = JSON.parse(data);
                            if(res.status !=0){
                                this.popMbox(res);
                                this.popTotals();
                            }
                            
                        },error: function(xhr, status, text) {

                            if(xhr.status ==419){
                                window.location = server_url;
                            }
        
                        }
                    });

                }
            });

    },
    itemsWindow: function(){

        //Full screen Logic
        var el = document.documentElement, rfs = el.requestFullScreen || el.webkitRequestFullScreen || el.mozRequestFullScreen;   
        rfs.call(el);

        $('.itemPullUp').fadeIn('slow');

        $('.itemPullUpTxt').focus();

        $.ajax({
            url:server_url+"/fetch_items",
            data:{"_token": cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let res = JSON.parse(data);

                let arr = [];           
                $.each(res,function(index,value){
                    arr.push(res[index]);
                });
                this.setState({itemsTable:arr}); 

                
               
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        }); 
        
    },
    addItemBtn:function(i,event){
        let customer = $('.txt_add_cust_wrap').val();
        $('.top_msg').html("");
        $.ajax({
            url:server_url+"/add_item_to_sc",
            data:{customer:customer,item_id:i,type:"tender","_token": cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let res = JSON.parse(data);
                
                if(res.status ==1){
                    $('.itemPullUpTxt').focus();
                    $.ajax({
                        url:server_url+"/sc_items",
                        data:{"_token": cs_token},
                        type:"POST",
                        context: this,
                        success:function(data){
                            //console.log(data);
                            let res = JSON.parse(data);
                            $('.itemPullUpTxt').val('');
                            if(res.status !='0' && res.status !='X' && res.status !='Y' && res.status !='G'){
                                this.popMbox(res);
                                this.popTotals();
                                $('.itemPullUp').fadeOut('slow');
                                $(".msg_holder").empty();

                                $.ajax({
                                    url:server_url+"/fetch_items",
                                    data:{"_token": cs_token},
                                    type:"POST",
                                    context: this,
                                    success:function(data){
                                        //console.log(data);
                                        let res = JSON.parse(data);
                        
                                        let arr = [];           
                                        $.each(res,function(index,value){
                                            arr.push(res[index]);
                                        });
                                        this.setState({itemsTable:arr}); 
                        
                                        
                                       
                                    },error: function(xhr, status, text) {
                        
                                        if(xhr.status ==419){
                                            window.location = server_url;
                                        }
                        
                                    }
                                });

                            }
                        },error: function(xhr, status, text) {

                            if(xhr.status ==419){
                                window.location = server_url;
                            }
        
                        }
                    });

                }else if(res.status =='X'){
                    $('.itemPullUpTxt').val('');
                    $(".top_msg").html("<div class='err'>Error: Drawer Closed</div>");
                }else if(res.status =='Y'){
                    $('.itemPullUpTxt').val('');

                    $(".top_msg").html("<div class='err'>Error: Drawer Expired</div>");
                }else if(res.status =='G'){
                    $('.itemPullUpTxt').val('');
                 
                    $(".top_msg").html("<div class='err'>Error: Item Not Received In Store</div>");
                }
            }
        });
    },
    eachAccItemRow:function(item,i){
        if(this.state.prvTender ==1){
            return(
                <tr key={i}>
                    <td>{item.look_up}</td><td>{item.item_desc}</td><td>{item.qty}</td><td>{item.sell_price}.00</td><td><div className="add_tray" onClick={this.addAccItemBtn.bind(this,item.id)}></div></td>
                </tr>
            )
        }else{
            return(
                <tr key={i}>
                    <td>{item.look_up}</td><td>{item.item_desc}</td><td>{item.qty}</td><td>{item.sell_price}.00</td><td></td>
                </tr>
            )
        }
        
    },
    addAccItemBtn:function(item){
        //console.log(item);

        let customer = $('.txt_add_cust_wrap').val();
       
        $.ajax({
            url:server_url+"/add_item_to_sc",
            data:{customer:customer,item_id:item,type:"xchange","_token": cs_token},
            type:"POST",
            context: this,
            success:function(data){
                let res = JSON.parse(data);
                console.log(data);
                if(res.status ==1){
                    $('.itemPullUpTxt').focus();
                    $.ajax({
                        url:server_url+"/xchange_only_sc_items",
                        data:{"_token": cs_token},
                        type:"POST",
                        context: this,
                        success:function(data){
                            //console.log(data);
                            let res = JSON.parse(data);
                            $('.accItemPullUp').val('');
                            if(res.status !='0' && res.status !='X' && res.status !='Y' && res.status !='G'){
                                //this.popMbox(res);
                                //this.popTotals();

                                let arr = [];
                                $.each(res,function(index,value){
                                    arr.push(res[index]);
                                });
                                this.setState({scXchange:arr});
            
                                $('.accItemPullUp').fadeOut('slow');
                                $(".msg_holder").empty();
                            }
                        },error: function(xhr, status, text) {

                            if(xhr.status ==419){
                                window.location = server_url;
                            }
        
                        }
                    });


                    $.ajax({
                        url:server_url+"/xchange_sc_totals",
                        data:{customer:res.customer,"_token": cs_token},
                        type:"POST",
                        context: this,
                        success:function(data){
                            let res = JSON.parse(data);
                            this.setState({accValue:res.acc_total});
                            this.setState({accXchangeValue:res.xchange});
            
                        }
                    });

                }else if(res.status =='X'){
                    $('.search_txt').val('');
                    $(".msg_holder").html("<div class='err'>Error: Drawer Closed</div>");
                }else if(res.status =='Y'){
                    $('.search_txt').val('');

                    $(".msg_holder").html("<div class='err'>Error: Drawer Expired</div>");
                }else if(res.status =='G'){
                    $('.search_txt').val('');
                 
                    $(".msg_holder").html("<div class='err'>Error: Item Not Received In Store</div>");
                }
            }
        });
    },
    hoverRow:function(id){
        $("#"+id).addClass('highlighted_row');
    },
    hoverRowLeave:function(id){
        $("#"+id).removeClass('highlighted_row');
    },
    eachItemRow:function(item,i){
        
        if(this.state.prvTender ==1){
            
            return(
                <div key={i} className='wrap_fake_row' onClick={this.addItemBtn.bind(this,item.id)}  id={"hov_"+item.id+""} onMouseLeave={this.hoverRowLeave.bind(this,"hov_"+item.id+"")} onMouseEnter={this.hoverRow.bind(this,"hov_"+item.id+"")}>
                    <div className='fake_tbl_row' >
                        
                           {item.item_desc}
                        
                    </div>

                    <div className='fake_tbl_row' >
                        <div className='fake_col'>
                            {item.code_no}
                        </div>
                        <div className='fake_col_right'>
                            {item.qty}
                        </div>
                        <div className='fake_col_right'>
                            {item.sell_price}
                        </div>
                        
                            
                       
                   
                    </div>
                </div>
            )
        }else{
            return(
                <div key={i} className='wrap_fake_row'  id={"hov_"+item.id+""} onMouseLeave={this.hoverRowLeave.bind(this,"hov_"+item.id+"")} onMouseEnter={this.hoverRow.bind(this,"hov_"+item.id+"")}>
                    <div className='fake_tbl_row' >
                        
                           {item.item_desc}
                        
                    </div>

                    <div className='fake_tbl_row' >
                        <div className='fake_col'>
                            {item.code_no}
                        </div>
                        <div className='fake_col_right'>
                            {item.qty}
                        </div>
                        <div className='fake_col_right'>
                            {item.sell_price}
                        </div>
                        
                            
                       
                   
                    </div>
                </div>
            )
        }
        /*
        if(this.state.prvTender ==1){
            return(
                <tr key={i} onClick={this.addItemBtn.bind(this,item.id)} className='item_row'>
                    <td>{item.code_no}</td><td>{item.item_desc}</td><td>{item.qty}</td><td>{item.sell_price}.00</td><td><div className="add_tray" ></div></td>
                </tr>
            )
        }else{
            return(
                <tr key={i}>
                    <td>{item.code_no}</td><td>{item.item_desc}</td><td>{item.qty}</td><td>{item.sell_price}.00</td><td></td>
                </tr>
            )
        }
        */
    },
    searchAccItemPullUp:function(){
        var item = $('.itemAccPullUpTxt').val();
        
        if(item.length > 0){
            $.ajax({
                url:server_url+"/search_item_pull_up",
                data:{item:item,"_token": cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    console.log(data);
                    let res = JSON.parse(data);
    
                    let arr = [];           
                    $.each(res,function(index,value){
                        arr.push(res[index]);
                    });
                    this.setState({itemsTable:arr}); 
                    
                },error: function(xhr, status, text) {
    
                    if(xhr.status ==419){
                        window.location = server_url;
                    }
    
                }
            });
        }else{
            $.ajax({
                url:server_url+"/fetch_items",
                data:{"_token": cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);
    
                    let arr = [];           
                    $.each(res,function(index,value){
                        arr.push(res[index]);
                    });
                    this.setState({itemsTable:arr}); 
                   
                },error: function(xhr, status, text) {
    
                    if(xhr.status ==419){
                        window.location = server_url;
                    }
    
                }
            }); 
        }
        

    },
    searchItemPullUp:function(){
        var item = $('.itemPullUpTxt').val();
        
        if(item.length > 0){
            $.ajax({
                url:server_url+"/search_item_pull_up",
                data:{item:item,"_token": cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    console.log(data);
                    let res = JSON.parse(data);
    
                    let arr = [];           
                    $.each(res,function(index,value){
                        arr.push(res[index]);
                    });
                    this.setState({itemsTable:arr}); 
                    
                },error: function(xhr, status, text) {
    
                    if(xhr.status ==419){
                        window.location = server_url;
                    }
    
                }
            });
        }else{
            $.ajax({
                url:server_url+"/fetch_items",
                data:{"_token": cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);
    
                    let arr = [];           
                    $.each(res,function(index,value){
                        arr.push(res[index]);
                    });
                    this.setState({itemsTable:arr}); 
                   
                },error: function(xhr, status, text) {
    
                    if(xhr.status ==419){
                        window.location = server_url;
                    }
    
                }
            }); 
        }
        

    },
    drawingsWindow:function(){

        //Full screen Logic
        var el = document.documentElement, rfs = el.requestFullScreen || el.webkitRequestFullScreen || el.mozRequestFullScreen;   
        rfs.call(el);

        $('.drawer').fadeIn('slow');
        $('.drawing_txt').focus();
        $('.drawer_msg').empty();
    },
    submitDrawings:function(){
        let amt = $('.drawing_txt').val();
        let comm = $('.drawing_comm').val();

        if(amt !="" && comm !=""){
            
            $.ajax({
                url:server_url+"/drawings_transactions",
                data:{amt:amt,comm:comm,"_token": cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    console.log(data);
                    let res = JSON.parse(data);
                    if(res.status ==1){
                        $('.drawing_comm').val('');
                        $('.drawing_txt').val('');

                        $('.drawer_msg').html("<div class='info'><p>Drawing Successful</p></div>");
                    }else if(res.status ==0){
                        $('.drawer_msg').html("<div class='err'><p>Error: Drawing More than is in the register</p></div>");
                    }   
                    
                },error: function(xhr, status, text) {

                    if(xhr.status ==419){
                        window.location = server_url;
                    }

                }
            });

        }else{

            $('.drawer_msg').html("<div class='err'>Error: Value(s) Missing</div>");
        }
    },
    poolDeck:function(){
        //Full screen Logic
        var el = document.documentElement, rfs = el.requestFullScreen || el.webkitRequestFullScreen || el.mozRequestFullScreen;   
        rfs.call(el);

        $.ajax({
            url:server_url+"/search_customers_auto_comp",
            data:{"_token": cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data); 
                let res  =JSON.parse(data);
                
                if(res.status !=0){
                    let arr = [];
                            
                    $.each(res,function(index,value){
                        arr.push(res[index]);
                    });

                    var options = {

                        data: arr,
                
                    list: {
                        maxNumberOfElements: 8,
                        match: {
                            enabled: true
                        },
                        sort: {
                            enabled: true
                        }
                    },
                        theme: "square"
                    };

                    $(".pool_activities_txt").easyAutocomplete(options);

                    //Full screen Logic
                    var el = document.documentElement, rfs = el.requestFullScreen || el.webkitRequestFullScreen || el.mozRequestFullScreen;   
                    rfs.call(el);
                     
                }
                

            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

        $('#teller').hide();
        $('#pool').fadeIn();
    },
    adminDeck:function(){

        //Full screen Logic
        var el = document.documentElement, rfs = el.requestFullScreen || el.webkitRequestFullScreen || el.mozRequestFullScreen;   
        rfs.call(el);

        $('#teller').hide();
        $('#admin').fadeIn('slow');

        $.ajax({
            url:server_url+"/get_admin_user_priviledges",
            data:{"_token": cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let resx = JSON.parse(data);

                if(resx.mng_item ==0){
                    $('#items_menu').hide();
                }else{
                    $('#items_menu').show();
                }

                if(resx.mng_users ==0){
                    $('#users_menu').hide();
                   
                }else{
                    $('#users_menu').show();
                }

                if(resx.mng_taxes ==0){
                    $('#taxes_menu').hide();
                }else{
                    $('#taxes_menu').show();
                }

                if(resx.mng_goods ==0){
                    $('#goods_menu').hide();
                }else{
                    $('#goods_menu').show();
                }

                if(resx.mng_branches ==0){
                    $('#branches_menu').hide();
                }else{
                    $('#branches_menu').show();
                }

                if(resx.mng_customers ==0){
                    $('#customers_menu').hide();
                }else{
                    $('#customers_menu').show();
                }

                if(resx.mng_clubs ==0){
                    $('#clubs_menu').hide();
                }else{
                    $('#clubs_menu').show();
                }

                if(resx.mng_events ==0){
                    $('#events_menu').hide();
                }else{
                    $('#events_menu').show();
                }

                if(resx.mng_accounts ==0){
                    $('#accounts_menu').hide();
                }else{
                    $('#accounts_menu').show();
                }

                if(resx.ball_pool ==0){
                    $('#pool_menu').hide();
                }else{
                    $('#pool_menu').show();
                }

                if(resx.remote_branch_access ==0){
                    $('.up_branch').hide();
                }else{
                    $('.up_branch').show();
                }

                this.setState({prvTender:resx.tender});
                this.setState({prvDelItem:resx.del_item});
                this.setState({prvDraw:resx.draw});
                this.setState({prvAdminLnk:resx.admin_link});
                this.setState({prvDrawer:resx.drawer});
                this.setState({prvReturnItem:resx.return_item});
                this.setState({prvMngItem:resx.mng_item});
                this.setState({prvMngUsers:resx.mng_users});
                this.setState({prvMngTaxes:resx.mng_taxes});
                this.setState({prvMngGoods:resx.mng_goods});
                this.setState({prvMngBranches:resx.mng_branches});
                this.setState({prvMngRemoteBranches:resx.remote_branch_access});
                
               
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });
    },
    loginLogicFnc(username,passd){
       
        if(username !="" && passd !=""){

            //Full screen Logic
            var el = document.documentElement, rfs = el.requestFullScreen || el.webkitRequestFullScreen || el.mozRequestFullScreen;   
            rfs.call(el);
            
            $.ajax({
                url:server_url+"/login",
                data:{username:username,passd:passd,"_token": cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);

                    if(res.status==1){
                        this.setState({uid:res.uid});
                        this.setState({fname:res.fname});
                        this.setState({lname:res.lname});
                        
                        $('.login').fadeOut('');
                        $('.itemPullUpTxt').focus();

                        $('#username_ipt').val('');
                        $('#passwd_ipt').val('');
                        
                        $('.message').empty();

                        
        $.ajax({
            url:server_url+"/get_user_priviledges",
            data:{uid:res.uid,"_token": cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let resx = JSON.parse(data);

                if(resx.admin_link ==0){
                    $('.admin_btn').hide();
                }else{
                    $('.admin_btn').show();
                }

                if(resx.draw ==0){
                    $('.calc_btn').hide();
                }else{
                    $('.calc_btn').show();
                }

                if(resx.tender ==0){
                    $('.new_tender_btn').hide();
                }else{
                    $('.new_tender_btn').show();
                }

                if(resx.tender_accounts ==0){
                    $('.xchange_btn').hide();
                }else{
                    $('.xchange_btn').show();
                }

                if(resx.offer_discount ==0){
                    $('.disc_cover').hide();
                }else{
                    $('.disc_cover').show();
                }

                if(resx.credit_sale ==0){
                    $('.btn_account_sale').hide();
                }else{
                    $('.btn_account_sale').show();
                }
                

                this.setState({prvTender:resx.tender});
                this.setState({prvDelItem:resx.del_item});
                this.setState({prvDraw:resx.draw});
                this.setState({prvAdminLnk:resx.admin_link});
                this.setState({prvDrawer:resx.drawer});
                this.setState({prvReturnItem:resx.return_item});
                this.setState({prvMngItem:resx.mng_item});
                this.setState({prvMngUsers:resx.mng_users});
                this.setState({prvMngTaxes:resx.mng_taxes});
                this.setState({prvMngGoods:resx.mng_goods});
                this.setState({prvMngBranches:resx.mng_branches});
                this.setState({prvMngRemoteBranches:resx.remote_branch_access});
                
                $.ajax({
                    url:server_url+"/sc_items",
                    data:{"_token":cs_token},
                    type:"POST",
                    context: this,
                    success:function(data){
                        
                        let res = JSON.parse(data);
                        this.popMbox(res);
                        this.popTotals();

                    },error: function(xhr, status, text) {

                        if(xhr.status ==419){
                            window.location = server_url;
                        }
    
                    }
                });


                
               
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });


        $.ajax({
            url:server_url+"/fetch_items",
            data:{"_token": cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let res = JSON.parse(data);

                let arr = [];           
                $.each(res,function(index,value){
                    arr.push(res[index]);
                });
                this.setState({itemsTable:arr}); 
               
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        }); 
        
                        
                    }else{
                        $('.message').html("<div class='err'>Error: Invalid User credentials</div>");
                    }
                    //let res = JSON.parse(data);
                    
                },error: function(xhr, status, text) {

                    if(xhr.status ==419){
                        window.location = server_url;
                    }

                }
            });

            
            
        }else{
            $('.message').html("<div class='err'>Value(s) Missing</div>");
        }
    },
    btnLogin:function(){
        

            let username = $('#username_ipt').val();
            let passd = $('#passwd_ipt').val();
            this.loginLogicFnc(username,passd);
            

    },
    loginLogic:function(e){
        if (e.key === 'Enter'){
            let username = $('#username_ipt').val();
            let passd = $('#passwd_ipt').val();

            this.loginLogicFnc(username,passd);

        }
    },
    logOutBtn:function(){
        $.ajax({
                url:server_url+"/logout",
                data:{"_token": cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    let res = JSON.parse(data);
                    if(res.status==1){
                        $(".login").fadeIn('slow');
                        $("#username_ipt").focus();
                    }
                }
        });
    },
    returnWindow:function(){

        //Full screen Logic
        var el = document.documentElement, rfs = el.requestFullScreen || el.webkitRequestFullScreen || el.mozRequestFullScreen;   
        rfs.call(el);

        $('.returnPullUp').fadeIn('slow');

        
        $.ajax({
            url:server_url+"/return_txns",
            data:{"_token": cs_token},
            type:"POST",
            context: this,
            success:function(data){
                let res = JSON.parse(data);
                //console.log(data);
                let arr = [];
                                
                $.each(res,function(index,value){
                    arr.push(res[index]);
                });

                this.setState({returnTxns:arr});
            
            }
        });

    },
    accountsWindow:function(){

        let customer = $('.txt_add_cust_wrap').val();
        if(customer !=""){
            
            $.ajax({
                url:server_url+"/new_check_customer",
                data:{customer:customer,"_token":cs_token},
                type:"POST",
                context:this,
                success:function(data){
                    console.log(data);
                    
                    let res = JSON.parse(data);
        
                    if(res.status!=0){

                        this.setState({accFname:res.fname});
                        this.setState({accLName:res.lname});
                        this.setState({accOrg:res.org});
                        
                        $('.accPullUp').fadeIn('slow');
                        $('.tblTabAcc').show();
                        this.setState({accTabVal:'acc'});

                        $.ajax({
                            url:server_url+"/accounts_txns",
                            data:{customer:customer,"_token":cs_token},
                            type:"POST",
                            context: this,
                            success:function(data){
                                console.log(data);
                                let res = JSON.parse(data);
                            
                                let arr = [];
                                                
                                $.each(res,function(index,value){
                                    arr.push(res[index]);
                                });
            
                                this.setState({accTxns:arr});
                            
                            }
                        });

                        
                        $.ajax({
                            url:server_url+"/xchange_sc_totals",
                            data:{customer:res.customer,"_token": cs_token},
                            type:"POST",
                            context: this,
                            success:function(data){
                                let res = JSON.parse(data);
                                this.setState({accValue:res.acc_total});
                                this.setState({accXchangeValue:res.xchange});
                
                            }
                        });
                        
                    }
                    
                 
                }
            });

        
        }
        
    },
    returnItemsBtn:function(){

        //Full screen Logic
        var el = document.documentElement, rfs = el.requestFullScreen || el.webkitRequestFullScreen || el.mozRequestFullScreen;   
        rfs.call(el);

        $('.returnCustomerDets').fadeIn('slow');
        $('.txt_return_fname').focus();
        
    },
    newTenderBtnAct: function(){
        
        //Full screen Logic
        var el = document.documentElement, rfs = el.requestFullScreen || el.webkitRequestFullScreen || el.mozRequestFullScreen;   
        rfs.call(el);

        let held_id = this.state.heldTransId;


        if(held_id ==""){

            $.ajax({
                url:server_url+"/check_sc",
                data:{"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    let res = JSON.parse(data);
                    if(res.cart_count > 0){
                        $('.search_txt').val('');
                        $('.bread_crumbs').html("<b> Options > </b>");
                        $('.new_tender').fadeIn('slow');
                        $('.payment_value').hide();
                        $('.paymentChoices').show();
                        $('.cashInput').val('');
                        $('.refNoInput').val(''); 
                    }
                }
            });

        }else{

            $.ajax({
                url:server_url+"/check_held_sc",
                data:{held_id:held_id,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    let res = JSON.parse(data);
                    if(res.cart_count > 0){
                        $('.search_txt').val('');
                        $('.new_tender').fadeIn('slow');
                        $('.payment_value').hide();
                        $('.paymentChoices').show();
                        $('.cashInput').val('');
                        $('.refNoInput').val('');
                    }
                }
            });

        }
       
        
        $.ajax({
            url:server_url+"/csr_update",
            data:{csr_val:1,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
            }
        });
    },
    selCustomerHold:function(i,custType,custName){
        $('.holdPullUp').fadeOut('slow');
        this.setState({heldCustTtl:custName});
        this.setState({heldCustId:i});
        this.setState({heldCustType:custType});
        
    },
    eachCustRow:function(item,i){
        return(
            <tr key={i}>

                <td>{item.f_name}</td>
                <td>{item.s_name}</td>
                <td>{item.org}</td>
                <td><div onClick={this.selCustomerHold.bind(this,item.id,'customer',item.f_name + ' '+item.s_name)} className="add_tray"></div></td>        
            </tr>
        )
    },
    eachTournRow:function(item,i){
        return(
            <tr key={i}>
                <td>{item.t_date}</td>
                <td>{item.title}</td>
                <td><div onClick={this.selCustomerHold.bind(this,item.id,'tournament',item.title)} className="add_tray"></div></td>        
            </tr>
        )
    },
    selHeldTrans:function(i){
        console.log(i);
        $.ajax({
            url:server_url+"/get_held_trans_data",
            data:{tid:i,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                console.log(data);
                    $('.holdTransPullUp').fadeOut('slow');
                    let res = JSON.parse(data);
                    this.popMbox(res);
                    this.setState({heldTransId:i});
                    this.selPopTotals(i);

                },error: function(xhr, status, text) {

                    if(xhr.status ==419){
                        window.location = server_url;
                    }
    
                }
            
            });
    },
    eachHeldTransRow:function(item,i){
        return(
            <tr key={i}>
                <td>{item.trans_time}</td>
                <td>{item.customer}</td>
                <td>{item.type}</td>
                <td>{item.total}</td>
                <td><div onClick={this.selHeldTrans.bind(this,item.id)} className="add_tray"></div></td>        
            </tr>
        )
    },
    fetchCustomer:function(){

        $('.holdPullUp').fadeIn('slow');
        
        $.ajax({
            url:server_url+"/fetch_customer_data",
            data:{csr_val:1,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let res = JSON.parse(data);
                let arr = [];
                                        
                $.each(res,function(index,value){
                    arr.push(res[index]);
                });

                this.setState({customerData:arr});
            
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

        $.ajax({
            url:server_url+"/fetch_tournaments_data",
            data:{csr_val:1,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                console.log(data);
                let res = JSON.parse(data);
                let arr = [];
                                        
                $.each(res,function(index,value){
                    arr.push(res[index]);
                });

                this.setState({tornamentData:arr});
            
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });


    },
    holdItemsAct:function(){
        /*
        let type = this.state.heldCustType;
        let cust = this.state.heldCustId;
        let ttl = this.state.heldCustTtl;

        */
       let cust  = $('.txt_add_cust_wrap').val();
        
        if(cust !=""){
            $.ajax({
                url:server_url+"/hold_trans",
                data:{cust:cust,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    var res = JSON.parse(data);
                    if(res.status ==1){

                        /*
                    this.setState({heldCustTtl:""});
                    this.setState({heldCustId:""});
                    this.setState({heldCustType:""});
                        */
                        $('.txt_add_cust_wrap').val('');
                        $('.cashInput').val('');
                        $('.mpesaInput').val('');
                        $('.pdqInput').val('');
                        $('.new_tender').hide();
                        $('.msg_wrap').html("<div class='info'><p>Transaction Completed</p></div>");
                        $('.change').fadeIn('slow');
                        $('.changeInput').focus();
                        $('.changeInput').val(res.change);
                    }
                }
            });
        }else{
            $('.msg_wrap').html("<div class='err'><p>Error: Select Customer</p></div>");
        }
        
        
    },
    holderWindow: function(){

        $.ajax({
            url:server_url+"/fetch_held_data",
            data:{"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                
                let res = JSON.parse(data);
                let arr = [];
                                        
                $.each(res,function(index,value){
                    arr.push(res[index]);
                });

                this.setState({heldDataRprt:arr});
                
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

            $('.holdTransPullUp').fadeIn('slow');
    },
    returnCustomerDetsAct:function(){
        
        let fname = $('.txt_return_fname').val();
        let lname = $('.txt_return_lname').val();

        if(fname !="" && lname !=""){
            $.ajax({
                url:server_url+"/return_all_items",
                data:{fname:fname,lname:lname,txn_id:this.state.scRetTxnId,"_token": cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    let res = JSON.parse(data);
                    //alert(data);
    
                   
                    if(res.status==1){
                        $('.txt_return_fname').val('');
                        $('.txt_return_lname').val('');
                        $('.msg_wrap').empty();
                        this.setState({scReturnTxns:[]});
                        $('.returnCustomerDets').fadeOut('slow');
                        this.setState({scReturnTxnsSec:[]});
                        $(".top_msg").html("<div class='info'>Success: Item(s) Returned</div>")
    
                        $('.itemPullUpTxt').focus();
                        $('.returnPullUp').fadeOut('slow');
                    }
    
                  
                    
                }
            });
        }else{
            $(".msg_wrap").html("<div class='err'>Error: Value(s) Missing</div>")
        }
        
    },
    accTabsAct:function(i){
        this.setState({accTabVal:i});
        let customer = $('.txt_add_cust_wrap').val();
        $(".msg_wrp").empty();
        if(i=='trans'){
            $('.tblTabTxns').show();
            $('.tblTabAcc').hide();

            $("#tblSecondAcc").hide();
            $("#tblSecondTrans").show();
            
            $.ajax({
                url:server_url+"/cust_acc_txns",
                data:{customer:customer,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);
                    
                    let arr = [];
                                    
                    $.each(res,function(index,value){
                        arr.push(res[index]);
                    });
    
                    this.setState({custAccTxns:arr});
                
                }
            });
        }else if(i=='acc'){
            $('.tblTabAcc').show();
            $('.tblTabTxns').hide();

            $("#tblSecondAcc").show();
            $("#tblSecondTrans").hide();

            $.ajax({
                url:server_url+"/accounts_txns",
                data:{customer:customer,"_token": cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    console.log(data);
                    let res = JSON.parse(data);
                    
                    let arr = [];
                                    
                    $.each(res,function(index,value){
                        arr.push(res[index]);
                    });

                    this.setState({accTxns:arr});
                
                }
            });
        }
    },
    issueAccItems:function(){
        let customer = $('.txt_add_cust_wrap').val();
        let acc_val = parseInt(this.state.accValue.replace(",",""));
        let xchange_val = parseInt(this.state.accXchangeValue.replace(",",""));
        
         if(xchange_val ==""){

            $.ajax({
                url:server_url+"/issue_acc_items",
                    data:{customer:customer,"_token": cs_token},
                    type:"POST",
                    context:this,
                    success:function(data){
                        //console.log(data);
                        let res = JSON.parse(data);
                        
                        this.setState({scAccTxns:[]});
                        $('.top_msg').html("<div class='info'>Success</div>");
                        $('.accPullUp').fadeOut('slow');
                        $('.itemPullUpTxt').focus();
                        $('.txt_add_cust_wrap').val('');

                        $.ajax({
                            url:server_url+"/accounts_txns",
                            data:{customer:customer,"_token": cs_token},
                            type:"POST",
                            context: this,
                            success:function(data){
                                //console.log(data);
                                let res = JSON.parse(data);
                                
                                let arr = [];
                                                
                                $.each(res,function(index,value){
                                    arr.push(res[index]);
                                });
            
                                this.setState({accTxns:arr});
                            
                            }
                        });
    
                    }
            });

            $.ajax({
                url:server_url+"/accounts_txns",
                data:{customer:customer,"_token": cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);
                    
                    let arr = [];
                                    
                    $.each(res,function(index,value){
                        arr.push(res[index]);
                    });

                    this.setState({accTxns:arr});
                
                }
            });

         }else if(xchange_val !=""){
            if(xchange_val <= acc_val){
                let credit = acc_val - xchange_val;
                $.ajax({
                    url:server_url+"/issue_xchange_items",
                    data:{credit:credit,acc_val:acc_val,xchange_val:xchange_val,customer:customer,"_token": cs_token},
                    type:"POST",
                    context:this,
                    success:function(data){
                        
                        let res = JSON.parse(data);
                        if(res.status ==1){
                            $('.top_msg').html("<div class='info'>Success</div>");
                            $.ajax({
                                url:server_url+"/xchange_sc_totals",
                                data:{customer:res.customer,"_token": cs_token},
                                type:"POST",
                                context: this,
                                success:function(data){
                                    let res = JSON.parse(data);
                                    this.setState({accValue:res.acc_total});
                                    this.setState({accXchangeValue:res.xchange});
                                    this.setState({scAccTxns:[]});
                                    this.setState({scXchange:[]});
                                    
                                }
                            });

                        }
                        
                        
                    },error: function(xhr, status, text) {

                        if(xhr.status ==419){
                            window.location = server_url;
                        }
        
                    }
                    
                });

            }else{
                $('.top_msg').html("<div class='err'>Value Exceeds Account</div>");

            }
            

         }

        
    },
    accItemWindow:function(){
        $('.accItemPullUp').fadeIn('slow');

        $('.itemAccPullUpTxt').focus();

        $.ajax({
            url:server_url+"/fetch_items",
            data:{"_token": cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let res = JSON.parse(data);

                let arr = [];           
                $.each(res,function(index,value){
                    arr.push(res[index]);
                });
                this.setState({itemsTable:arr}); 

                
               
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        }); 
    },
    selAddReturnOptions:function(){
        let opt = $('.rtrn_add_opts').val();
        if(opt !=""){
            if(opt=="CASH"){
                
                $('.return_add_cash_amt').show();
                $('.return_add_ref_no').hide();
                this.setState({returnAddTitle:"Additional Return Amount"});
                //returnAddTitle
            }else{
                this.setState({returnAddTitle:opt+" REF No."});
                $('.return_add_cash_amt').hide();
                $('.return_add_ref_no').show();
            }
        }
    },
    backToPayChoices:function(){
        
        $('.payment_value').hide();
        $('.payment_split').hide();

        $('.bread_crumbs').html("<b> Options > </b>");


        $('.cashInput').hide();
        $('.refNoInput').hide();

        $('.paymentChoices').fadeIn('slow');
        $('.return_cover').hide();
        $('.cashInput').val('');
        $('.refNoInput').val('');
        $('.discountInput').val('');
        this.setState({splitPaymentAmt:2});
        
    },
    splitPayOptions:function(){
        $('.payment_split').hide();
        let split_amt = this.state.splitPaymentAmt;
    },
    saleBtn:function(type){
        this.setState({paymentType:type});

        if(type !='split'){

                $('.paymentChoices').hide();

                $('.payment_value').fadeIn('slow');

                $('.bread_crumbs').html("<b> Options > "+type.toUpperCase()+ " > </b>");

                if(type =='cash'){
                    
                    $('.cashInput').show();
                    $('.cashInput').focus();
                    $('.refNoInput').hide();
                    

                    this.setState({paymentTypeTitle:type + " Amount"});
                }else if(type =='account'){
                    $('.discountInput').focus();
                    this.setState({paymentTypeTitle:"Account Sale"});
                    $('.refNoInput').hide();
                    $('.cashInput').hide();
                }else if(type =='return'){
                    this.setState({paymentTypeTitle:"RETURN RECEIPT NO."});
                    $('.return_cover').show();
                    $('.refNoInput').show();
                    $('.refNoInput').focus();
                    $('.cashInput').hide();
                    
                }else if(type =='xchange'){
                    
                    this.setState({paymentTypeTitle:"EXCHANGE RECEIPT NO."});
                    $('.return_cover').show();
                    $('.refNoInput').show();
                    $('.refNoInput').focus();
                    $('.cashInput').hide();

                }else{
                    
                    $('.cashInput').hide();
                    $('.refNoInput').show();
                    $('.refNoInput').focus();
                    
                
                    this.setState({paymentTypeTitle:type + " REF No."});
                }

        }else{
            $('.bread_crumbs').html("<b> Options > Split > </b>");

            $('.paymentChoices').hide();
            $('.payment_split').fadeIn('slow');
        }
        
    },
    splitBack:function(i){
        $('.msg').empty();
        if(i=='one'){

            $('.bread_crumbs').html("<b> Options > Split > </b>");

            $('.payment_split').fadeIn('slow');
            $('.payment_split_opt').hide();
            $('.split_one').hide();
            this.setState({returnAddTitle:""});
            $('#one_mpesa_btn').show();
            $('#one_cheque_btn').show();
            $('#one_card_btn').show();
            $('#one_cash_btn').show();
            $('.one_cash_txt').val('');
            $('.one_ref_txt').val('');
            $('.amt_one').val('');
        }else if(i =='two'){
            $('.split_two').hide();
            $('.split_one').fadeIn('slow');

            $('.bread_crumbs').html("<b> Options > Split > "+this.state.oneSplitType.toUpperCase()+" > </b>");

            $('.two_cash_txt').val('');
            $('.two_ref_txt').val('');
            $('.two_cash_txt').hide();
            $('.two_ref_txt').hide();
            $('.amt_two').val('');
        }
    },
    splitSaleBtn:function(i){

        $('.payment_split').hide();
        $('.payment_split_opt').fadeIn('slow');
        $('.split_one').fadeIn('slow');
        

        this.setState({oneSplitType:i});

        $('.bread_crumbs').html("<b> Options > Split > "+i.toUpperCase()+"</b>");

        if(i=='mpesa'){
            $('#one_mpesa_btn').hide();
            $('.one_ref_txt').show();
            $('.one_ref_txt').focus();
            this.setState({onSplitTitle:"MPESA REF No."});
        }else if(i=='cheque'){
            $('#one_cheque_btn').hide();
            $('.one_ref_txt').show();
            $('.one_ref_txt').focus();
            this.setState({onSplitTitle:"CHEQUE REF No."});
        }else if(i=='card'){
            $('#one_card_btn').hide();
            $('.one_ref_txt').show();
            $('.one_ref_txt').focus();
            this.setState({onSplitTitle:"CARD AUTH CODE"});
        }else if(i=='cash'){
            $('#one_cash_btn').hide();
            $('.one_cash_txt').show();
            $('.one_cash_txt').focus();
            this.setState({onSplitTitle:"CASH AMOUNT"});
        }
    },
    splitSaleBtnOne:function(i){
       
        let amt_one = ($('.amt_one').val()) ? parseInt($('.amt_one').val().replace(",","")) : 0;
        let ttl_price = parseInt(this.state.totalPrice.replace(",",""));
        let discount = ($('.split_discount').val()) ? $('.split_discount').val().replace(",","") : 0;
        let amt_diff = ttl_price - amt_one - discount;
        $('.amt_two').val(amt_diff);

        this.setState({twoSplitType:i});
        let oneType = this.state.oneSplitType;
        let payVal;

        
        
        if(oneType =='cash'){
            payVal = $('.one_cash_txt').val().replace(",","");
        }else{
            payVal = $('.one_ref_txt').val();
        }
        
        if(payVal !='' && amt_one != 0){
            
            if((amt_one - discount) < ttl_price){
                $('.split_one').hide();
                $('.split_two').fadeIn('slow');

                $('.bread_crumbs').html("<b> Options > Split > "+oneType.toUpperCase()+" > "+i.toUpperCase()+"</b>");

                if(i =='cash'){
                    $('.two_ref_txt').hide();
                    $('.two_cash_txt').show();
                    this.setState({twoSplitTitle:"CASH AMOUNT"});
                }else if(i=='mpesa'){
                    $('.two_cash_txt').hide();
                    $('.two_ref_txt').show();
                    this.setState({twoSplitTitle:"MPESA REF No."});
                }else if(i=='cheque'){
                    $('.two_cash_txt').hide();
                    $('.two_ref_txt').show();
                    this.setState({twoSplitTitle:"CHEQUE REF No."});
                }else if(i=='card'){
                    $('.two_cash_txt').hide();
                    $('.two_ref_txt').show();
                    this.setState({twoSplitTitle:"CARD AUTH CODE."});
                }
                $('.msg_wrap').empty();
            }else{
                $('.msg_wrap').html("<div class='err'>Error: Invalid Amount</div>");
            }

           
            
        }else{
            $('.msg_wrap').html("<div class='err'>Error: Value(s) Missing</div>");
        }
    },
    submitSplitSaleBtn:function(){
        let typeTwo = this.state.twoSplitType;
        let amt_one = parseInt($('.amt_one').val().replace(",",""));
        let ttl_price = parseInt(this.state.totalPrice.replace(",",""));
        let typeOne = this.state.oneSplitType;
        let customer = $('.txt_add_cust_wrap').val();
        let payVal;
        let actPayVal;
        let change = 0;
        let refOne = $('.one_ref_txt').val();
        let refTwo = $('.two_ref_txt').val();
        let discount = ($('.split_discount').val()) ? $('.split_discount').val().replace(",","") : 0;
        
        if(typeTwo =='cash'){
            payVal = $('.two_cash_txt').val().replace(",","");
            actPayVal = $('.amt_two').val().replace(",","");
        }else{
            payVal = $('.two_ref_txt').val();
            actPayVal = $('.amt_two').val().replace(",","");
            refTwo = $('.two_ref_txt').val();
           
        }
        
        if(payVal !=''){

            if(typeTwo =='cash'){
                change = (parseInt(payVal) + parseInt(amt_one) + parseInt(discount)) - ttl_price;
            }else{
                if(typeOne=='cash'){
                    change = (parseInt(payVal) + parseInt(amt_one) + parseInt(discount)) - ttl_price;
                }else{
                    change = 0;
                }
            }

           
            $.ajax({
                url:server_url+"/tender_split_trans",
                data:{discount:discount,customer:customer,amt_one:amt_one,amt_two:actPayVal,change:change,typeOne:typeOne,typeTwo:typeTwo,refOne:refOne,refTwo:refTwo,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){

                        console.log(data);
                        let res = JSON.parse(data);
                        if(res.status==1){
                            $('.split_change').val(change);
                            $('.split_two').hide();
                            $('.split_three').fadeIn('slow');
                        }
                },error: function(xhr, status, text){
            
                    if(xhr.status ==419){
                        window.location = server_url;
                    }

                }
            });

            $('.split_change').val(change);
        }else{
            $('.msg_wrap').html("<div class='err'>Error: Value(s) Missing</div>");
        }
    },
    submitSplitChangeBtn:function(){
        $('.change').fadeOut('slow');
            $('.itemPullUpTxt').focus();
            $('.changeInput').val('');
            $('.txt_add_cust_wrap').val('');
            $('.one_ref_txt').val('');
            $('.two_ref_txt').val('');
            $('.two_cash_txt').val('');
            $('.amt_one').val('');
            $('.amt_two').val('');
            $('.split_three').hide();
            $('.new_tender').fadeOut('');
            $('.split_discount').val('');

            $('.btn_mpesa_sale').show();
            $('.btn_cheque_sale').show();
            $('.btn_card_sale').show();
            $('.btn_cash_sale').show();
            

            $.ajax({
                url:server_url+"/csr_update",
                data:{csr_val:0,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                }
            });

            $.ajax({
                url:server_url+"/sc_items",
                data:{"_token": cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);
                    if(res.status !=0){
                        this.popMbox(res);
                        this.popTotals();
                        
                        $('.msg_wrap').empty();

                    }
                },error: function(xhr, status, text) {

                    if(xhr.status ==419){
                        window.location = server_url;
                    }

                }
            });

            $.ajax({
                url:server_url+"/fetch_items",
                data:{"_token": cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);
    
                    let arr = [];           
                    $.each(res,function(index,value){
                        arr.push(res[index]);
                    });
                    this.setState({itemsTable:arr}); 
    
                    
                   
                },error: function(xhr, status, text) {
    
                    if(xhr.status ==419){
                        window.location = server_url;
                    }
    
                }
            }); 

    },
    submitSaleBtn:function(){
       
        let type = this.state.paymentType;
        let discount = ($('.discountInput').val() == "") ? 0 : $('.discountInput').val().replace(",","");
        let pay_val;
        if(type =='cash'){
            pay_val = $('.cashInput').val().replace(",",""); 
        }else{
            pay_val = $('.refNoInput').val(); 
        }

        let held_id = this.state.heldTransId;
        let customer = $('.txt_add_cust_wrap').val();
       
            if(type=="cash"){

                if(pay_val !=""){

                    if((parseInt(pay_val.replace(",","")) + parseInt(discount)) >= parseInt(this.state.totalPrice.replace(",",""))){
    
        
                            $.ajax({
                                url:server_url+"/new_tender_trans",
                                data:{customer:customer,held_id:held_id,pay_val:pay_val,type:type,discount:discount,"_token":cs_token},
                                type:"POST",
                                context: this,
                                success:function(data){
                                    //console.log(data);
                                
                                    var res = JSON.parse(data);
                                    if(res.status ==1){
                                       
                                        $('.cashInput').val('');
                                        $('.refNoInput').val('');
                                        $('.refNoInput').hide();
                                        $('.discountInput').val('');
                                        $('.payment_value').hide();
                                        $('.paymentChoices').show();
                                        $('.new_tender').hide();
                                        $('.txt_add_cust_wrap').val('');
                                        $('.msg_wrap').html("<div class='info'><p>Transaction Completed</p></div>");
                                        $('.change').fadeIn('slow');
                                        $('.changeInput').focus();
                                        $('.changeInput').val(res.change);
                                        this.setState({returnAddTitle:""});
                                    }
                                    
                                },error: function(xhr, status, text){
            
                                    if(xhr.status ==419){
                                        window.location = server_url;
                                    }
                
                                }
                            });
        
                        
                        
        
                    }else{
                        $('.msg_wrap').html("<div class='err'><p>Error: Cash Not Enough</p></div>");
                    }

                }else{
                    $('.msg_wrap').html("<div class='err'><p>Error: Value(s) Missing</p></div>");
                }

            }else if(type=="account"){

                if(customer !=""){
                            $.ajax({
                                url:server_url+"/new_tender_trans",
                                data:{customer:customer,held_id:held_id,pay_val:0,type:type,discount:discount,"_token":cs_token},
                                type:"POST",
                                context: this,
                                success:function(data){
                                    console.log(data);
                                
                                    var res = JSON.parse(data);
                                    if(res.status ==1){
                                        
                                        $('.cashInput').val('');
                                        $('.refNoInput').val('');
                                        $('.discountInput').val('');
                                        $('.payment_value').hide();
                                        $('.paymentChoices').show();
                                        $('.new_tender').hide();
                                        $('.txt_add_cust_wrap').val('');
                                        $('.msg_wrap').html("<div class='info'><p>Transaction Completed</p></div>");
                                        $('.change').fadeIn('slow');
                                        $('.changeInput').focus();
                                        $('.changeInput').val(res.change);
            
                                    }
                                    
                                },error: function(xhr, status, text){
            
                                if(xhr.status ==419){
                                    window.location = server_url;
                                }
            
                            }
                        });
                }else{
                    $('.msg_wrap').html("<div class='err'><p>Error: Select Customer</p></div>");
                }

                

            }else if(type=="return"){
                
                let rtrn_opt = $('.rtrn_add_opts').val();
                let pay_val = $('.refNoInput').val();
                let total = parseInt(this.state.totalPrice.replace(",",""));
                let scnd_pay_val = "";
                let act_rtrn_option;

                if(pay_val !=""){

                    $.ajax({
                        url:server_url+"/check_return",
                        data:{pay_val:pay_val,"_token":cs_token},
                        type:"POST",
                        context: this,
                        success:function(data){
                            console.log(data);
                            let res = JSON.parse(data);
                            if(res.status !=0){
                                if((total - discount) > parseInt(res.total)){
                                    if(rtrn_opt !=""){

                                        if(rtrn_opt =="CASH"){
                                            act_rtrn_option = "cash tender";  
                                        }else if(rtrn_opt =="MPESA"){
                                            act_rtrn_option = "mpesa tender";  
                                        }else if(rtrn_opt =="CHEQUE"){
                                            act_rtrn_option = "cheque tender";  
                                        }else if(rtrn_opt =="CARD"){
                                            act_rtrn_option = "card tender";  
                                        }

                                        if(act_rtrn_option=="cash tender"){
                                            scnd_pay_val = $('.return_add_cash_amt').val().replace(",",""); 
                                        }else{
                                            scnd_pay_val = $('.return_add_ref_no').val(); 
                                        }

                                        if(rtrn_opt =="CASH" && (parseInt(scnd_pay_val) + total - discount) >= parseInt(res.total)){

            
                                            $.ajax({
                                                url:server_url+"/new_return_trans",
                                                data:{customer:customer,discount:discount,rtrn_opt:act_rtrn_option,pay_val:pay_val,scnd_pay_val:scnd_pay_val,refund_amt:res.total,type:"return","_token":cs_token},
                                                type:"POST",
                                                context: this,
                                                success:function(data){
                                                    console.log(data);
                                                    let res = JSON.parse(data);
                                                    if(res.status==1){

                                                        

                                                        $('.return_add_cash_amt').val('');
                                                        $('.return_add_ref_no').val('');
                                                        $('.rtrn_add_opts').val('');
                                                       
                                                        $('.return_cover').hide();
                                                        $('.return_add_cash_amt').hide();
                                                        $('.return_add_ref_no').hide();

                                                        $('.cashInput').val('');
                                                        $('.refNoInput').val('');
                                                        $('.discountInput').val('');
                                                        $('.payment_value').hide();
                                                        $('.paymentChoices').show();
                                                        $('.new_tender').hide();
                                                        $('.txt_add_cust_wrap').val('');
                                                        $('.msg_wrap').html("<div class='info'><p>Transaction Completed</p></div>");
                                                        $('.change').fadeIn('slow');
                                                        $('.changeInput').focus();
                                                        $('.changeInput').val(res.change);
                                                        this.setState({returnAddTitle:""});
                                                    }
                                                }
                                            });

                                        }else if(rtrn_opt !="CASH"){

                                            $.ajax({
                                                url:server_url+"/new_return_trans",
                                                data:{customer:customer,discount:discount,rtrn_opt:act_rtrn_option,pay_val:pay_val,scnd_pay_val:scnd_pay_val,refund_amt:res.total,type:'return',"_token":cs_token},
                                                type:"POST",
                                                context: this,
                                                success:function(data){
                                                    //console.log(data);
                                                    let res = JSON.parse(data);
                                                    if(res.status==1){

                                                       

                                                        $('.return_add_cash_amt').val('');
                                                        $('.return_add_ref_no').val('');
                                                        $('.rtrn_add_opts').val('');
                                                        
                                                        $('.return_cover').hide();
                                                        $('.return_add_cash_amt').hide();
                                                        $('.return_add_ref_no').hide();

                                                        $('.cashInput').val('');
                                                        $('.refNoInput').val('');
                                                        $('.discountInput').val('');
                                                        $('.payment_value').hide();
                                                        $('.paymentChoices').show();
                                                        $('.new_tender').hide();
                                                        $('.txt_add_cust_wrap').val('');
                                                        $('.msg_wrap').html("<div class='info'><p>Transaction Completed</p></div>");
                                                        $('.change').fadeIn('slow');
                                                        $('.changeInput').focus();
                                                        $('.changeInput').val(res.change);
                                                    }
                                                }
                                            });

                                        }

                                    }else{
                                        $('.msg_wrap').html("<div class='err'>Error: Select Additional Mode</div>");
                                    }
                                }else{
                                    
                                    $.ajax({
                                        url:server_url+"/new_return_trans",
                                        data:{customer:customer,discount:discount,rtrn_opt:rtrn_opt,pay_val:pay_val,scnd_pay_val:scnd_pay_val,type:'return',"_token":cs_token},
                                        type:"POST",
                                        context: this,
                                        success:function(data){
                                            //console.log(data);
                                            let res = JSON.parse(data);
                                            if(res.status==1){

                                                
                                                
                                                $('.return_add_cash_amt').val('');
                                                $('.return_add_ref_no').val('');
                                                $('.rtrn_add_opts').val('');
                                                
                                                $('.return_cover').hide();
                                                $('.return_add_cash_amt').hide();
                                                $('.return_add_ref_no').hide();

                                                $('.cashInput').val('');
                                                $('.refNoInput').val('');
                                                $('.discountInput').val('');
                                                $('.payment_value').hide();
                                                $('.paymentChoices').show();
                                                $('.new_tender').hide();
                                                $('.txt_add_cust_wrap').val('');
                                                $('.msg_wrap').html("<div class='info'><p>Transaction Completed</p></div>");
                                                $('.change').fadeIn('slow');
                                                $('.changeInput').focus();
                                                $('.changeInput').val(res.change);
                                                this.setState({returnAddTitle:""});
                                            }
                                        }
                                    });

                                }
                                //$('.msg_wrap').html("<div class='info'>Total: "+res.total+"</div>");
                            }else{
                                $('.msg_wrap').html("<div class='err'>Error: Invalid Receipt No.</div>");
                            }
                            console.log(data);

                        },error: function(xhr, status, text){

                            if(xhr.status ==419){
                                window.location = server_url;
                            }
        
                        }
                    });

                }else{
                    $('.msg_wrap').html("<div class='err'>Error: enter Receipt No.</div>");
                }
                //End Return
            }else if(type=="xchange"){

                
                let rtrn_opt = $('.rtrn_add_opts').val();
                let pay_val = $('.refNoInput').val();
                let total = parseInt(this.state.totalPrice.replace(",",""));
                let scnd_pay_val = "";
                let act_rtrn_option;
                
                if(pay_val !=""){
                    
                    $.ajax({
                        url:server_url+"/check_xchange",
                        data:{pay_val:pay_val,total:total,"_token":cs_token},
                        type:"POST",
                        context: this,
                        success:function(data){
                            console.log(data);
                            let res = JSON.parse(data);
                            
                            if(res.status ==1){
                                if((total - discount) > parseInt(res.cost)){
                                    if(rtrn_opt !=""){

                                        if(rtrn_opt =="CASH"){
                                            act_rtrn_option = "cash tender";  
                                        }else if(rtrn_opt =="MPESA"){
                                            act_rtrn_option = "mpesa tender";  
                                        }else if(rtrn_opt =="CHEQUE"){
                                            act_rtrn_option = "cheque tender";  
                                        }else if(rtrn_opt =="CARD"){
                                            act_rtrn_option = "card tender";  
                                        }

                                        if(act_rtrn_option=="cash tender"){
                                            scnd_pay_val = $('.return_add_cash_amt').val().replace(",",""); 
                                        }else{
                                            scnd_pay_val = $('.return_add_ref_no').val(); 
                                        }

                                        if(rtrn_opt =="CASH" && (parseInt(scnd_pay_val) + total - discount) >= parseInt(res.cost)){

            
                                            $.ajax({
                                                url:server_url+"/new_return_trans",
                                                data:{customer:customer,discount:discount,rtrn_opt:act_rtrn_option,pay_val:pay_val,scnd_pay_val:scnd_pay_val,refund_amt:res.cost,type:'xchange',"_token":cs_token},
                                                type:"POST",
                                                context: this,
                                                success:function(data){
                                                    //console.log(data);
                                                    let res = JSON.parse(data);
                                                    if(res.status==1){


                                                        $('.return_add_cash_amt').val('');
                                                        $('.return_add_ref_no').val('');
                                                        $('.rtrn_add_opts').val('');
                                                       
                                                        $('.return_cover').hide();
                                                        $('.return_add_cash_amt').hide();
                                                        $('.return_add_ref_no').hide();

                                                        $('.cashInput').val('');
                                                        $('.refNoInput').val('');
                                                        $('.discountInput').val('');
                                                        $('.payment_value').hide();
                                                        $('.paymentChoices').show();
                                                        $('.new_tender').hide();
                                                        $('.txt_add_cust_wrap').val('');
                                                        $('.msg_wrap').html("<div class='info'><p>Transaction Completed</p></div>");
                                                        $('.change').fadeIn('slow');
                                                        $('.changeInput').focus();
                                                        $('.changeInput').val(res.change);
                                                    }
                                                }
                                            });

                                        }else if(rtrn_opt !="CASH"){

                                            $.ajax({
                                                url:server_url+"/new_return_trans",
                                                data:{customer:customer,discount:discount,rtrn_opt:act_rtrn_option,pay_val:pay_val,scnd_pay_val:scnd_pay_val,refund_amt:res.cost,type:'xchange',"_token":cs_token},
                                                type:"POST",
                                                context: this,
                                                success:function(data){
                                                    console.log(data);
                                                    let res = JSON.parse(data);
                                                    if(res.status==1){
                                                        $('.return_add_cash_amt').val('');
                                                        $('.return_add_ref_no').val('');
                                                        $('.rtrn_add_opts').val('');
                                                        
                                                        $('.return_cover').hide();
                                                        $('.return_add_cash_amt').hide();
                                                        $('.return_add_ref_no').hide();

                                                        $('.cashInput').val('');
                                                        $('.refNoInput').val('');
                                                        $('.discountInput').val('');
                                                        $('.payment_value').hide();
                                                        $('.paymentChoices').show();
                                                        $('.new_tender').hide();
                                                        $('.txt_add_cust_wrap').val('');
                                                        $('.msg_wrap').html("<div class='info'><p>Transaction Completed</p></div>");
                                                        $('.change').fadeIn('slow');
                                                        $('.changeInput').focus();
                                                        $('.changeInput').val(res.change);
                                                    }
                                                }
                                            });

                                        }

                                    }else{
                                        $('.msg_wrap').html("<div class='err'>Error: Add " +res.diff+ "</div>");
                                    }
                                }else{
                                    
                                    $.ajax({
                                        url:server_url+"/new_return_trans",
                                        data:{customer:customer,discount:discount,rtrn_opt:rtrn_opt,pay_val:pay_val,scnd_pay_val:scnd_pay_val,type:"xchange","_token":cs_token},
                                        type:"POST",
                                        context: this,
                                        success:function(data){
                                            console.log(data);
                                            let res = JSON.parse(data);
                                            if(res.status==1){
                                                console.log("am here now");
                                                $('.return_add_cash_amt').val('');
                                                $('.return_add_ref_no').val('');
                                                $('.rtrn_add_opts').val('');
                                                
                                                $('.return_cover').hide();
                                                $('.return_add_cash_amt').hide();
                                                $('.return_add_ref_no').hide();

                                                $('.cashInput').val('');
                                                $('.refNoInput').val('');
                                                $('.discountInput').val('');
                                                $('.payment_value').hide();
                                                $('.paymentChoices').show();
                                                $('.new_tender').hide();
                                                $('.txt_add_cust_wrap').val('');
                                                $('.msg_wrap').html("<div class='info'><p>Transaction Completed</p></div>");
                                                $('.change').fadeIn('slow');
                                                $('.changeInput').focus();
                                                $('.changeInput').val(res.change);
                                            }
                                        }
                                    });

                                }
                                //$('.msg_wrap').html("<div class='info'>Total: "+res.total+"</div>");
                            }else if(res.status ==2){
                                $('.msg_wrap').html("<div class='err'><p>Error: Exchange Amount Not Enough</p></div>");
                            }else if(res.status ==0){
                                $('.msg_wrap').html("<div class='err'><p>Error: Invalid REF No.</p></div>"); 
                            }else if(res.status ==3){
                                $('.msg_wrap').html("<div class='err'><p>Error: REF No. Already Used</p></div>"); 
                            }
             

                        },error: function(xhr, status, text){

                            if(xhr.status ==419){
                                window.location = server_url;
                            }
        
                        }
                    });

                }else{
                    $('.msg_wrap').html("<div class='err'>Error: enter Receipt No.</div>");
                }
                //End Exchange
            }else{

                if(pay_val !=""){

                    if((parseInt(discount) <= parseInt(this.state.totalPrice.replace(",","")))){
                            if(type =="xchange"){
                                
                                /*
                                $.ajax({
                                    url:server_url+"/check_xchange",
                                    data:{pay_val:pay_val,total:parseInt(this.state.totalPrice.replace(",","")),"_token":cs_token},
                                    type:"POST",
                                    context: this,
                                    success:function(data){
                                            console.log(data);
                                            let res = JSON.parse(data);
                                            if(res.status ==1){

                                                $.ajax({
                                                    url:server_url+"/new_tender_trans",
                                                    data:{customer:customer,held_id:held_id,pay_val:pay_val,type:type,discount:discount,"_token":cs_token},
                                                    type:"POST",
                                                    context: this,
                                                    success:function(data){
                                                        //console.log(data);
                                                    
                                                        var res = JSON.parse(data);
                                                        if(res.status ==1){
                    
                                                            $('.cashInput').val('');
                                                            $('.refNoInput').val('');
                                                            $('.discountInput').val('');
                                                            $('.payment_value').hide();
                                                            $('.paymentChoices').show();
                                                            $('.new_tender').hide();
                                                            $('.txt_add_cust_wrap').val('');
                                                            $('.msg_wrap').html("<div class='info'><p>Transaction Completed</p></div>");
                                                            $('.change').fadeIn('slow');
                                                            $('.changeInput').focus();
                                                            $('.changeInput').val(res.change);
                    
                                                        }
                                                        
                                                    },error: function(xhr, status, text){
                    
                                                    if(xhr.status ==419){
                                                        window.location = server_url;
                                                    }
                                
                                                }
                                                });

                                            }else if(res.status ==2){
                                                $('.msg_wrap').html("<div class='err'><p>Error: Exchange Amount Not Enough</p></div>");
                                            }else if(res.status ==0){
                                                $('.msg_wrap').html("<div class='err'><p>Error: Invalid REF No.</p></div>"); 
                                            }else if(res.status ==3){
                                                $('.msg_wrap').html("<div class='err'><p>Error: REF No. Already Used</p></div>"); 
                                            }

                                    }
                                });
                                */
                            }else{

                                $.ajax({
                                    url:server_url+"/new_tender_trans",
                                    data:{customer:customer,held_id:held_id,pay_val:pay_val,type:type,discount:discount,"_token":cs_token},
                                    type:"POST",
                                    context: this,
                                    success:function(data){
                                        //console.log(data);
                                    
                                        var res = JSON.parse(data);
                                        if(res.status ==1){
    
                                            $('.cashInput').val('');
                                            $('.refNoInput').val('');
                                            $('.discountInput').val('');
                                            $('.payment_value').hide();
                                            $('.paymentChoices').show();
                                            $('.new_tender').hide();
                                            $('.txt_add_cust_wrap').val('');
                                            $('.msg_wrap').html("<div class='info'><p>Transaction Completed</p></div>");
                                            $('.change').fadeIn('slow');
                                            $('.changeInput').focus();
                                            $('.changeInput').val(res.change);
    
                                        }
                                        
                                    },error: function(xhr, status, text){
    
                                    if(xhr.status ==419){
                                        window.location = server_url;
                                    }
                
                                }
                            });

                        }
                        

                }else{
                    $('.msg_wrap').html("<div class='err'><p>Error: Cash Not Enough</p></div>");
                }

            }else{
                $('.msg_wrap').html("<div class='err'><p>Error: Value(s) Missing</p></div>");
            }

        }
            
        

    },
    searchAccTrans:function(){
        let dates = $('.returnPullUpTxt').val();
        let tab = this.state.accTabVal;
        let customer = $('.txt_add_cust_wrap').val();
        if(dates !=""){
            if(tab=="acc"){
                
                $.ajax({
                    url:server_url+"/search_accounts_txns",
                    data:{dates:dates,customer:customer,"_token": cs_token},
                    type:"POST",
                    context: this,
                    success:function(data){
                        let res = JSON.parse(data);
                        console.log(data);
                        let arr = [];
                                        
                        $.each(res,function(index,value){
                            arr.push(res[index]);
                        });
        
                        this.setState({accTxns:arr});
                        
                    }
                });
                
            }else if(tab == "trans"){

                $.ajax({
                    url:server_url+"/search_cust_acc_txns",
                    data:{dates:dates,customer:customer,"_token": cs_token},
                    type:"POST",
                    context: this,
                    success:function(data){
                        console.log(data);
                        let res = JSON.parse(data);
                        
                        let arr = [];
                                        
                        $.each(res,function(index,value){
                            arr.push(res[index]);
                        });
    
                        this.setState({custAccTxns:arr});
                    
                    }
                });
            }
        }
    },
    addCommas:function(){
        let cash = $('.cashInput').val();
        let cash_one = $('.one_cash_txt').val();
        let cash_two = $('.two_cash_txt').val();
        let amt_one = $('.amt_one').val();
        let amt_two = $('.amt_two').val();
        let split_discount = $('.split_discount').val();
        let discountInput = $('.discountInput').val();
        let return_add_cash_amt = $('.return_add_cash_amt').val();

        if(return_add_cash_amt !=""){

            // skip for arrow keys
            if(event.which >= 37 && event.which <= 40){
                event.preventDefault();
            }
            
            $('.return_add_cash_amt').val(function(index, value) {
                value = value.replace(/,/g,'');
                var parts = value.toString().split(".");
                    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    return parts.join(".");
            });

        }

        if(discountInput !=""){

            // skip for arrow keys
            if(event.which >= 37 && event.which <= 40){
                event.preventDefault();
            }
            
            $('.discountInput').val(function(index, value) {
                value = value.replace(/,/g,'');
                var parts = value.toString().split(".");
                    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    return parts.join(".");
            });

        }


        if(amt_one !=""){

            // skip for arrow keys
            if(event.which >= 37 && event.which <= 40){
                event.preventDefault();
            }
            
            $('.amt_one').val(function(index, value) {
                value = value.replace(/,/g,'');
                var parts = value.toString().split(".");
                    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    return parts.join(".");
            });

        }
        
        if(split_discount !=""){

            // skip for arrow keys
            if(event.which >= 37 && event.which <= 40){
                event.preventDefault();
            }
            
            $('.split_discount').val(function(index, value) {
                value = value.replace(/,/g,'');
                var parts = value.toString().split(".");
                    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    return parts.join(".");
            });

        }

        if(amt_two !=""){

            // skip for arrow keys
            if(event.which >= 37 && event.which <= 40){
                event.preventDefault();
            }
            
            $('.amt_two').val(function(index, value) {
                value = value.replace(/,/g,'');
                var parts = value.toString().split(".");
                    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    return parts.join(".");
            });

        }

        if(cash !=""){

            // skip for arrow keys
            if(event.which >= 37 && event.which <= 40){
                event.preventDefault();
            }
            
            $('.cashInput').val(function(index, value) {
                value = value.replace(/,/g,'');
                var parts = value.toString().split(".");
                    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    return parts.join(".");
            });

        }

        if(cash_one !=""){
            if(event.which >= 37 && event.which <= 40){
                event.preventDefault();
            }
            
            $('.one_cash_txt').val(function(index, value) {
                value = value.replace(/,/g,'');
                var parts = value.toString().split(".");
                    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    return parts.join(".");
            });
        }

        if(cash_two !=""){
            if(event.which >= 37 && event.which <= 40){
                event.preventDefault();
            }
            
            $('.two_cash_txt').val(function(index, value) {
                value = value.replace(/,/g,'');
                var parts = value.toString().split(".");
                    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    return parts.join(".");
            });
        }

    },
    drawerWindow:function(){
        this.loadDrawerReport();
        $('.drawerPullUp').fadeIn('slow');
        $('.dr_op_txt').focus();

        let optional_config = {
            mode:"range",
            dateFormat: "d-m-Y"
        };
        
        $(".dr_dates_txt").flatpickr(optional_config);

    },
    openDrawer:function(){
        
        let op_amt = $('.dr_op_txt').val();
        if(op_amt !=""){
            $.ajax({
                url:server_url+"/open_drawer",
                data:{op_amt:op_amt,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    $('.dr_op_txt').val('');
                    let res  = JSON.parse(data);
                    if(res.status == 1){
                        $('.drawer_msg').html("<div class='info'>Success: Drawer Open</div>");
                        this.loadDrawerReport();
                    }else{
                        $('.drawer_msg').html("<div class='err'>Error: Drawer still open!</div>");
                    }
                }
            });
        }else{
            $('.drawer_msg').html("<div class='err'>Error: Value(s) Missing</div>");
        }
    },
    confClsBtn: function(){

        let drawer_id = this.state.closeDrawerVal;
        let cls_amt = $('.closingAmt').val();

        if(cls_amt !=""){
            $.ajax({
                url:server_url+"/close_drawer",
                data:{cls_amt:cls_amt,drawer_id:drawer_id,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res  = JSON.parse(data);
                    if(res.status == 1){
                        $('.dr_op_txt').focus();
                        $('.close_drawer_conf').fadeOut('slow');
                        this.loadDrawerReport();
                    }
                }
            });
        }
        
    },
    searchDrawerReports:function(){
        let dates = $('.dr_dates_txt').val();

        if(dates !=""){
            $.ajax({
                url:server_url+"/search_drawer_report",
                data:{dates:dates,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                   // console.log(data);
                    let res = JSON.parse(data);
                    let arr = [];
                                            
                    $.each(res,function(index,value){
                        arr.push(res[index]);
                    });
    
                    this.setState({rowDrawerItem:arr});
                },error: function(xhr, status, text) {
    
                    if(xhr.status ==419){
                        window.location = server_url;
                    }
    
                }
            });
        }
        

    },
    loadDrawerReport(){

        $.ajax({
            url:server_url+"/drawer_report",
            data:{"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                console.log(data);
                let res = JSON.parse(data);
                let arr = [];
                                        
                $.each(res,function(index,value){
                    arr.push(res[index]);
                });

                this.setState({rowDrawerItem:arr});
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });
    },
    btnCloseDrawer:function(i,event){
        // console.log(i);
         this.setState({closeDrawerVal:i});
 
         $('.close_drawer_conf').fadeIn('slow');
         $('.closingAmt').focus();
     },  
    eachDrawerRow:function(item,i){
        
        if(item.status == 'open'){
            return(
                <tr key={i}>
    
                     <td>{item.start_time}</td>
                     <td>{item.opening_amt}</td>
                     <td>{item.stop_time}</td>
                    <td>{item.closing_amt}</td>
                    <td>{item.status}</td>
                    <td>{item.user}</td>
                    <td><a className="rprt_link" onClick={this.btnCloseDrawer.bind(this,item.id)} href='#'>Close</a></td>
                                 
                </tr>
            )
        }else{
            return(
                <tr key={i}>
    
                     <td>{item.start_time}</td>
                     <td>{item.opening_amt}</td>
                     <td>{item.stop_time}</td>
                    <td>{item.closing_amt}</td>
                    <td>{item.status}</td>
                    <td>{item.user}</td>
                    <td></td>         
                </tr>
            )
        }
        
    },
    render: function(){
        let tabAcc;
        let tabTrans;
        if(this.state.accTabVal =='acc'){
            tabAcc = <TopActiveMenu />;
            tabTrans = "";
        }else if(this.state.accTabVal =='trans'){
            tabAcc = "";
            tabTrans = <TopActiveMenu />;
        }
        return(
            <div>  

                <div className='search_box'>
                        <div className="user_sname" >{this.state.lname}</div>
                        <div className='top_msg'></div>
                        {/**
                            <input type='text' onKeyPress={this.transEnterBtn} onChange={this.sbPropFunc} className='search_txt' />
                        */}
                        
                        <div className='log_out' onClick={this.logOutBtn} >Log Out</div>
                       {/**  <div className='op_cls_drawer_btn' onClick={this.drawerWindow} >Drawer</div> */} 
                </div>  
                
                    <div className="wrap">
                        <div className='desc'>
                            <table>
                
                                <thead>
                                    <tr>
                                        <th>Qty</th><th>Unit Price</th><th>Total</th>
                                    </tr>
                                    
                                </thead>
                                
                                
                                <tbody>

                                    {
                                            
                                        this.state.titles.map(this.eachTitle)
                                    }

                                     
                                </tbody>

                            </table>
                        </div>

                        <div className="item_drawer">
                                <div className="add_cust_wrap">
                                    <input type="text" className='itemPullUpTxt' onKeyUp={this.searchItemPullUp} placeholder="Search Items"/>
                                </div>

                                <div className='desc_items'>
                                    <table>
                        
                                        <thead>
                                            <tr>
                                                <th>Code</th><th>Qty</th><th></th>
                                            </tr>
                                            
                                        </thead>

                                        <tbody>
                                        
                                            {
                                                this.state.itemsTable.map(this.eachItemRow)
                                            }
                                            
                                        </tbody>

                                    </table>
                                </div>
                            </div>

                        <div className="total">

                            <div className="calc">
                                <div className="tender_btns_wrap">
                                    {/**

                                    <div className="item_btn" onClick={this.itemsWindow}>
                                        <b>i</b>
                                        <div className="item_btn_title"><p>Items</p></div>
                                    
                                    </div>

                                    <div className="hold_btn" onClick={this.holderWindow}>
                                        <b>H</b>
                                        <div className="hold_btn_title"><p>Held</p></div>
                                    </div>
                                        
                                        */}
                                    
                                    <div className="calc_btn" onClick={this.returnWindow}>
                                        <b>R</b>
                                        <div className="calc_btn_title"><p>Return</p></div>
                                    </div>
                                    <div className="drop_btn" onClick={this.drawingsWindow}>
                                        <b>D</b>
                                        <div className="drop_btn_title"><p>Draw</p></div>
                                    </div>

                                   <div className="pool_calc_btn" onClick={this.poolDeck}>
                                        <b>P</b>
                                        <div className="pool_calc_btn_title"><p>Pool</p></div>
                                    </div>
                                   
                                    <div className="admin_btn" onClick={this.adminDeck}>
                                        <b>A</b>
                                        <div className="admin_btn_title"><p>Admin</p></div>
                                    </div>
                                    
                                </div>
                                <div className="tender_btns_wrap">
                                    <div className="new_tender_btn" onClick={this.newTenderBtnAct}>
                                            <p>TENDER</p>
                                    </div>
                                    <div className="xchange_btn" onClick={this.accountsWindow}>
                                        <b>C</b>
                                        <div className="xchange_btn_title"><p>Accounts</p></div>
                                    </div>
                                    <div className="xchange_btn" onClick={this.drawerWindow}>
                                        <b>Dr</b>
                                        <div className="xchange_btn_title"><p>Drawer</p></div>
                                    </div>
                                    
                                    
                                </div>

                                
          
                            </div>

                            
                            {/**
                            <div className="org_title">
                                        <p>NOVEL GOLF SHOP</p>
                            </div>
                             */}
                            
                        
                            <div className="one">

                                <div className="add_cust_wrap">
                                    <input type="text" className="txt_add_cust_wrap" placeholder="Search Customer"/>
                                </div>

                                <div className="total_qty">
                                    <p>Total Quantity</p>
                                    <b>{this.state.totalItemQty}</b>
                                </div>
                                <div className="total_vat">
                                    <p>Total VAT</p>
                                    <b>{this.state.totalVat}</b>
                                </div>
                                <div className="act_total">
                                    <div className="total_figure">
                                        <p>Total</p>
                                        <b>{this.state.totalPrice}.00</b>
                                    </div>  
                                </div>
                            </div>

                            

                        </div>
                        

                        <div className="item_qty">
                            <label>Update Quantity</label>
                            <div className="cls" onClick={this.clsDialog.bind(this,"item_qty")}></div>
                           
                            <div className="qty_ctrls_wrapper">	
                                
                               <li><input type='text' className='new_txt_up_qty' onKeyPress={this.qtyUpEntBtn} /></li> 
                                <li><input type='button' value='Update' onClick={this.qtyUpClick} /></li>
                            </div>
           
                        </div>


                        <div className="price_qty">
                            <label>Update Price</label>
                            <div className="cls" onClick={this.clsDialog.bind(this,"price_qty")}></div>
                            
                            <div className="qty_ctrls_wrapper">	
                                <li><p>Highest Price: <b>{this.state.upCeilPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}</b> Lowest Price: <b>{this.state.upFloorPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}</b></p></li>
                                
                                <li><input type="text" className="txt_up_qty" onKeyPress={this.upPriceEntBtn} /></li>
                                
                            </div>
           
                        </div>


                        <div className="qty_upd">
                            <label>Update Qty</label>
                            <div className="cls" onClick={this.clsDialog.bind(this,"price_qty")}></div>
                            
                            <div className="qty_ctrls_wrapper">	
                                
                                <li><input type="text" className="txt_up_price" onKeyPress={this.upQtyEntBtn} /></li>
                                
                            </div>
           
                        </div>


                    </div>

                    <div className="wrap">

                        <div className="new_tender">
                            <div className="cls"  onClick={this.clsDialog.bind(this,"new_tender")}></div>
                            <div className="msg_wrap"></div>
                            <div className="bread_crumbs"></div>
      
                            <div className="paymentChoices">
                                    
                                <div className="row_tender">
                                    
                                    <li><input type='button' className='btn_cash_sale' onClick={this.saleBtn.bind(this,'cash')} value='CASH' /></li>
                                
                                    
                                </div> 

                                <div className="row_tender">
                                    <div className="row_tender_part">
                                        <li><input type='button' className='btn_account_sale' onClick={this.saleBtn.bind(this,'mpesa')} value='MPESA' /></li>
                                    </div> 
                                    <div className="row_tender_part">
                                    <li><input type='button' className='btn_account_sale' onClick={this.saleBtn.bind(this,'cheque')} value='CHEQUE' /></li>
                                    </div> 
                                    
                                </div> 

                                <div className="row_tender">
                                    <div className="row_tender_part">
                                        <li><input type='button' className='btn_account_sale' onClick={this.saleBtn.bind(this,'card')} value='CARD TENDER' /></li>
                                    </div> 
                                    <div className="row_tender_part">
                                    <li><input type='button' className='btn_account_sale' onClick={this.saleBtn.bind(this,'return')} value='RETURN' /></li>
                                    </div> 
                                    
                                </div> 
                                
                                <div className="row_tender">
                                    {/**
                                        <div className="row_tender_part">
                                            <li><input type='button' className='btn_mpesa_sale' onClick={this.saleBtn.bind(this,'xchange')} value='EXCHANGE' /></li>
                                        </div> 
                                    */}
                                    <div className="row_tender_part">
                                    <li><input type='button' className='btn_account_sale' onClick={this.saleBtn.bind(this,'account')} value='ACCOUNT SALE' /></li>
                                    </div>
                                   
                                    <div className="row_tender_part">
                                        <li><input type='button' className='btn_account_sale' onClick={this.saleBtn.bind(this,'split')} value='SPLIT' /></li>
                                    </div> 
                                    
                                </div> 

                               
                            </div>

                            <div className="payment_value">
                                <div className="row_tender">
                                    <li><input type='button' className='btn_back_choices' onClick={this.backToPayChoices} value='BACK' /></li>
                                </div>      
                                <div className="row_tender">
                                    <li><b>{this.state.paymentTypeTitle.toUpperCase()}</b></li>
                                    <li><input type="text" className="cashInput"  onKeyUp={this.addCommas} /></li>         
                                    <li><input type="text" className="refNoInput" /></li>  
                                    
                                    <div className='return_cover'>  
                                        
                                        <li>Select Mode Of Additional Payment</li>
                                        <li><select className='rtrn_add_opts' onChange={this.selAddReturnOptions}>
                                                <option></option>
                                                <option >CASH</option>
                                                <option>MPESA</option>
                                                <option>CARD</option>
                                                <option >CHEQUE</option>
                                            </select>
                                        </li>
                                        <li>{this.state.returnAddTitle}</li>
                                        <li><input type="text" className="return_add_cash_amt"  /></li>
                                        <li><input type="text" className="return_add_ref_no"  /></li>
                                    </div>  

                                    <div className='disc_cover'>
                                        <li><b>Discount</b></li>
                                        <li><input type="text" className="discountInput" onKeyUp={this.addCommas} /></li>         
                                    </div>
                                    
                                    <li><input type='button' className='btn_submit_sale' onClick={this.submitSaleBtn} value='SUBMIT' /></li>
                                </div> 
                                  
                            </div>

                            <div className="payment_split">
                                <div className="payment_opt_no">
                                    <div className="row_tender">
                                        <li><input type='button' className='btn_back_choices' onClick={this.backToPayChoices} value='BACK' /></li>
                                    </div>  

                                    <div className="row_tender">
                                    <div className="row_tender_part">
                                        <li><input type='button' className='btn_mpesa_sale' onClick={this.splitSaleBtn.bind(this,'mpesa')} value='MPESA' /></li>
                                    </div> 
                                    <div className="row_tender_part">
                                    <li><input type='button' className='btn_cheque_sale' onClick={this.splitSaleBtn.bind(this,'cheque')} value='CHEQUE' /></li>
                                    </div> 
                                    
                                </div> 
                                <div className="row_tender">
                                   
                                        <div className="row_tender_part">
                                            <li><input type='button' className='btn_card_sale' onClick={this.splitSaleBtn.bind(this,'card')} value='CARD TENDER' /></li>
                                        </div>
                                     
                                    {/**
                                        <div className="row_tender_part">
                                    <li><input type='button' className='btn_cash_sale' onClick={this.splitSaleBtn.bind(this,'cash')} value='CASH' /></li>
                                    </div> 
                                    */}
                                    
                                    
                                </div> 
                                    
                                    
                                   
                                </div>   
                            </div>

                            <div className="payment_split_opt">
                                <div className="split_one">
                                    <div className="row_tender">
                                            <li><input type='button' className='btn_back_choices' onClick={this.splitBack.bind(this,'one')} value='BACK' /></li>
                                    </div> 
                                    <div className="row_tender">
                                        <div className="row_tender_part">
                                            <li className='righty'><label>{this.state.oneSplitType.toUpperCase()} Amount</label></li>
                                            <li><input type='text' className='amt_one' onKeyUp={this.addCommas} /></li>
                                        </div>
                                        <div className="row_tender_part">
                                            <li className='righty'><label>{this.state.onSplitTitle}</label></li>
                                            <li><input type='text' className='one_cash_txt' onKeyUp={this.addCommas} /></li>
                                            <li><input type='text' className='one_ref_txt' /></li>
                                        </div>
                                    </div>

                                    <div className="row_tender">
                                        <div className='disc_cover'>
                                            <li><label>Discount</label></li>
                                            <li><input type='text' className='split_discount' onKeyUp={this.addCommas} /></li>
                                        </div>
                                        
                                    </div>
                                    
                                    <div className="row_tender">
                                        <div className="row_tender_part">
                                            <li><input type='button' className='btn_mpesa_sale' id='one_mpesa_btn' onClick={this.splitSaleBtnOne.bind(this,'mpesa')} value='MPESA' /></li>
                                        </div>
                                        <div className="row_tender_part">
                                            <li><input type='button' className='btn_cheque_sale' id='one_cheque_btn' onClick={this.splitSaleBtnOne.bind(this,'cheque')} value='CHEQUE' /></li>
                                        </div>
                                    </div>
                                   
                                        
                                    <div className="row_tender">
                                        <div className="row_tender_part">
                                        <li><input type='button' className='btn_card_sale' id='one_card_btn' onClick={this.splitSaleBtnOne.bind(this,'card')} value='CARD TENDER' /></li>
                                        </div>
                                        <div className="row_tender_part">
                                            <li><input type='button' className='btn_cash_sale' id='one_cash_btn' onClick={this.splitSaleBtnOne.bind(this,'cash')} value='CASH' /></li>
                                        </div>
                                    </div>
                                    
                                </div>

                                <div className="split_two">
                                    <div className="row_tender">
                                            <li><input type='button' className='btn_back_choices' onClick={this.splitBack.bind(this,'two')} value='BACK' /></li>
                                    </div> 
                                    <div className="row_tender">
                                        <div className="row_tender_part">
                                            <li><label>{this.state.twoSplitType.toUpperCase()} Amount</label></li>
                                            <li><input type='text' className='amt_two' disabled /></li>
                                            
                                        </div>
                                        
                                        <div className="row_tender_part">
                                            <li><label>{this.state.twoSplitTitle} </label></li>
                                            <li><input type='text' className='two_cash_txt' onKeyUp={this.addCommas} /></li>
                                            <li><input type='text' className='two_ref_txt' /></li>
                                        </div>
                                    </div>
                                    
                                    <div className="row_tender">
                                        <li><input type='button' className='btn_submit_sale' onClick={this.submitSplitSaleBtn} value='SUBMIT' /></li>
                                    </div> 
                                    
                                </div>

                                <div className="split_three">
                                    <div className="row_tender">
                                        <li><label>Change</label></li>
                                        <li><input type='text' className='split_change' disabled /></li>
                                    </div>
                                    <div className="row_tender">
                                    <li><input type='button' className='btn_submit_sale' onClick={this.submitSplitChangeBtn} value='SUBMIT' /></li>
                                    </div>
                                </div>
                                
                            </div>

                        </div>
                    

                    <div className="change">
                        <label>Change</label>
                        
                                <div className="tender_inputs_wrap">
                                    <div className="msg_wrap"></div>
                                    <li><input type="text" className="changeInput" onKeyPress={this.transChange} /></li>
                                    <li><input type='button' className='btn_submit_sale' onClick={this.transChangeClick} value='COMPLETE' /></li>
                                </div>    
                        </div> 
                    </div>

                    <div className="itemPullUp" id="itemPullUp">
                    
                    <div className="cls" onClick={this.clsDialog.bind(this,"itemPullUp")}></div>
                        <input type="text" onKeyUp={this.searchItemPullUp} className="itemPullUpTxt" />
                        <iput type="button" value="" />
                        <div className='msg_holder'>
                     
                        </div> 
                     <div className="itemPullUpTblWrap" id="itemPullUpNewTbl">
                        <table>
                            <thead>
                                <tr>
                                    <th>Code</th><th>Item Description</th><th>Qty</th><th>Price</th><th></th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                
                                {
                                   
                                   this.state.itemsTable.map(this.eachItemRow)
                                }
                            </tbody>
                        </table>
                     </div>
                                  
                    </div>


                    <div className="holdTransPullUp" >
                        <div className="cls" onClick={this.clsDialog.bind(this,"holdTransPullUp")}></div>
                        <input type="text" onKeyUp={this.searchItemPullUp} className="itemPullUpTxt" />
                        <div className="itemPullUpTblWrap" id="holdTransPullUpNewTbl">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Date</th><th>Customer</th><th>Type</th><th>Total</th><th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    {
                                        this.state.heldDataRprt.map(this.eachHeldTransRow)
                                    }
                                    </tbody>
                                </table>
                        </div>
                    </div>

                    <div className="drawer">
                    
                        <div className="cls" onClick={this.clsDialog.bind(this,"drawer")}></div>
                          
                          <label>Drawings</label> 
                          <div className='drawer_msg'></div>
                                 <p>Drawings Amount</p>
                                  
                                  <input type='text' className='drawing_txt'/>
                                  <p>Comment</p>
                                  
                                  <input type='text' className='drawing_comm'/>
                                  <input type='button' className='drawer_btn' onClick={this.submitDrawings} value='Draw' />  
                              
                    </div>

                    <div className="returnPullUp">

                        <div className="rtrnTopWrap">
                            
                            <input type="text" placeholder="Select Date" className="newReturnPullUpTxt" />
                            <div className='white_btn' onClick={this.newSearchReturnPullUp}></div>
                            
                            
                            <div className="cls" onClick={this.clsAltDialog.bind(this,"returnPullUp")}></div>
                            <input type="button" value="Return" onClick={this.returnItemsBtn} className="returnItemsBtn" />
                            {/**
                                <input type="text" onChange={this.searchReturnPullUp} className="returnPullUpTxt" />
                            
                            */}

                            <div id="scReturnTxnNo"></div>

                        </div>
                        
                        <div className="rtrnWrap">
                        

                            <div className="returnPullUpTblWrap" id="returnPullUpLeft">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Date/Time</th><th>Receipt No.</th><th>Qty</th><th>Total</th><th></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                       
                                        {
                                            this.state.returnTxns.map(this.returnRow)
                                        }
                                    </tbody>
                                </table>
                            </div> 

                            <div className="newReturnPullUpTblWrap" id="newReturnPullUpRight">
                            

                                <table>
                                        <thead>
                                            <tr>
                                                <th>Item</th><th>Qty</th><th>Amount</th><th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        {
                                            this.state.scReturnTxns.map(this.returnRowDets)
                                        }
                                        </tbody>
                                </table> 
                            </div> 

                            <div className="newReturnPullUpTblWrap" id="returnPullUpRightTwo">
                            

                            <table>
                                    <thead>
                                        <tr>
                                            <th>Item</th><th>Qty</th><th>Amount</th><th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    {
                                        this.state.scReturnTxnsSec.map(this.returnRowDetsTwo)
                                    }
                                    </tbody>
                            </table> 
                        </div>
                        </div>
                    </div>

                    <div className="drawerPullUp">
                        <div  className="drawerTopWrpr">
                         
                            <table>
                                <tbody>
                                    <tr>
                                        <td><div className='drawer_msg'></div></td>
                                    <td><b>Opening Amount</b></td>
                                    <td><input type='text' className='dr_op_txt' /></td>
                                    <td><input type='button' value='Save' onClick={this.openDrawer} className='btn_save_drawer'/></td>
                                    <td><div className="cls_blk" onClick={this.clsAltDialog.bind(this,"drawerPullUp")}></div></td>
                                    </tr>
                                </tbody>
                            </table> 

                            <table>
                                <tbody>
                                    <tr>
                                    <td><input type='text' placeholder="Select Dates" className='dr_dates_txt' /></td>
                                    <td><div className='search_btn_2' onClick={this.searchDrawerReports}></div></td>
                                    </tr>
                                </tbody>
                            </table>           
                        </div>         
                        <div id="rprt_drawer_table" className="drawerPullUpTblWrp" >
                            <table>
                                <thead>
                                    <tr><th>Opening Date</th><th>Opening Amount</th><th>Closing Date</th><th>Closing Amount</th><th>Status</th><th>User</th><th></th></tr>
                                </thead>
                                <tbody>
                                    {
                                        this.state.rowDrawerItem.map(this.eachDrawerRow)
                                    }
                                </tbody>
                            </table>

                            <div className="close_drawer_conf">
                                <div className="cls" onClick={this.clsDialog.bind(this,"close_drawer_conf")}></div>
                                <li><p>Closing Amount</p></li>
                                <li><input type='text' className='closingAmt' /></li>
                                <li><input type='button' onClick={this.confClsBtn} value='Close' /></li>
                            </div>
                        </div>
                    </div>

                    {/** Accounts */}
                    <div className="accPullUp">
                        <div className="accTopWrpr">
                            <div className="cls" onClick={this.clsAltDialog.bind(this,"accPullUp")}></div>
                            
                            
                            <input type="button" value="Issue" onClick={this.issueAccItems} className="returnItemsBtn" />
                            
                            <input type="text" placeholder="Select Dates" className="returnPullUpTxt" />
                            <div className="white_btn" onClick={this.searchAccTrans}></div>
                            <div className="goods_item_hold" id="scReturnTxnNo">
                                        
                            </div>
                        </div>
                        <div className="accTopWrpr">
                        
                             <div className="accTitle" onClick={this.accTabsAct.bind(this,"acc")}>
                                    <b>Account</b>
                                    {tabAcc} 
                            </div>
                        
                            <div className="accTitle" onClick={this.accTabsAct.bind(this,"trans")}>
                                    <b>Collections</b>
                                    {tabTrans} 
                            </div>
                       
                            <div className="accCustomerName">
                                
                                { this.state.accFname+" " } { this.state.accLName+" " } { this.state.accOrg } 
                                   
                            </div>
                            <div className="accValue">
                                {/* <li><p>Account Value</p></li>
                                <li><b>this.state.accValue </b></li>*/}
                            </div>
                            <div className="accValue">
                                <input type='button' value='Return' onClick={this.returnAccTransBtn} className='hdn_return_acc_btn' />
                           
                            {/* <li><p>XChange Value</p></li>
                                <li><b>this.state.accXchangeValue </b></li>*/}
                            </div>
                            {/**
                                 <div className="add_new" onClick={this.accItemWindow}></div>
                            */}
                            
                        </div>
                        <div className="accBottomWrpr">
                        
                                    <div className="tblTabAcc">

                                         <div className="accPullUpTblWrap" id="accPullUpLeft">
                                
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <th>No</th><th>Item</th><th>Event</th><th>Qty</th><th></th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                
                                                    {
                                                        this.state.accTxns.map(this.accRow)
                                                    }
                                                </tbody>
                                            </table>
                                        </div>
                                    
                                    </div>

                                    <div className="tblTabTxns">
                                                    
                                    <div className="returnPullUpTblWrap" id="returnPullUpLeft">
                                

                                            <table>
                                                <thead>
                                                    <tr>
                                                        <th>Date/Time</th><th>Receipt No.</th><th>Qty</th><th>Item</th><th></th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                
                                                    {
                                                         
                                                         this.state.custAccTxns.map(this.returnAccRow)
                                                    }
                                                </tbody>
                                            </table>
                                        </div> 
                                    
                                    </div>

                            

                             <div className="accPullUpTblWrap" id="returnPullUpRight">
                            

                                <table id="tblSecondAcc">
                                        <thead>
                                            <tr>
                                                <th>Item</th><th>Qty</th><th>Amount</th><th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        {   
                                            this.state.scAccTxns.map(this.accSecondRow)
                                        }
                                        </tbody>
                                </table> 
                                
                                <table id="tblSecondTrans">
                                        <thead>
                                            <tr>
                                                <th>Items</th><th>Qty</th><th>Amount</th><th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        {   
                                            this.state.scAccTxns.map(this.transSecondRow)
                                        }
                                        </tbody>
                                </table> 

                            </div> 
                            
                            {/**
                                
                                <div className="accPullUpTblWrap" id="returnPullUpRightTwo">
                            
                                <table>
                                        <thead>
                                            <tr>
                                                <th>Item</th><th>Qty</th><th>Amount</th><th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        {
                                            this.state.scXchange.map(this.xChangeRow)
                                        }
                                        </tbody>
                                </table> 
                            </div>
                                
                                */}
                            
                           
                            
                        </div>
                        <div className="msg_wrp"></div> 
                    </div>
                    

                    <div className="accItemPullUp" id="accItemPullUp">
                    
                        <div className="cls" onClick={this.clsAltDialog.bind(this,"accItemPullUp")}></div>
                            <input type="text" onKeyUp={this.searchAccItemPullUp} className="itemAccPullUpTxt" />
                            <iput type="button" value="" />
                            <div className='msg_holder'>
                        
                            </div> 
                        <div className="itemPullUpTblWrap" id="itemPullUpNewTbl">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Code</th><th>Item Description</th><th>Qty</th><th>Price</th><th></th>
                                    </tr>
                                </thead>

                                  
                                    <tbody>
                                    {
                                        this.state.itemsTable.map(this.eachAccItemRow)
                                    }
                                </tbody>
                                
                                
                            </table>
                        </div>
                                  
                    </div>

                    {/**Accounts */}
                    
                    <div className="returnCustomerDets">
                    <div className="cls" onClick={this.clsDialog.bind(this,"returnCustomerDets")}></div>
                        
                        <div className="msg_wrap">
                                    
                        </div>

                        <li>
                            <label>First Name</label>
                        </li>
                        <li>
                            <input className='txt_return_fname' type='text' />
                        </li>
                        <li>
                            <label>Last Name</label>
                        </li>
                        <li>
                            <input className='txt_return_lname' type='text' />
                        </li>
                        <li>
                            <input type='button' value='Save' onClick={this.returnCustomerDetsAct} />
                        </li>
                    </div>

                    <div className="holdPullUp">
                 
                        <div className="cls" onClick={this.clsDialog.bind(this,"holdPullUp")}></div>
                        <input type="button" value="Hold" onClick={this.returnItemsBtn} className="returnItemsBtn" />
                        <input type="text" placeholder='Search Customer' onChange={this.searchReturnPullUp} className="returnPullUpTxt" />
                        <input type="text" placeholder='Search Tournament' onChange={this.searchReturnPullUp} className="returnPullUpTxt" />
                            
                            <div className="holdPullUpTblWrap" id="holdPullUpCust">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>First Name</th><th>Last Name</th><th>Organization</th><th></th><th></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                    {
                                        this.state.customerData.map(this.eachCustRow)
                                    }
                                       
                                        
                                    </tbody>
                                </table>
                            </div> 

                            <div className="holdPullUpTblWrap" id="holdPullUpTourn">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Date</th><th>Tournament</th><th></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                    {
                                        this.state.tornamentData.map(this.eachTournRow)
                                    }
                                      
                                        
                                    </tbody>
                                </table>
                            </div> 

                          

                    </div>


                    <div className="rtrn_item_qty">
                            <label>Quantity</label>
                            <div className="cls" onClick={this.clsMinDialog.bind(this,"acc_item_qty")}></div>
                           
                            <div className="qty_ctrls_wrapper">	
                                
                               <li><input type='text' className='rtrn_txt_up_qty'  /></li> 
                                <li><input type='button' value='Update' onClick={this.qtyUpRtrnClick} /></li>
                            </div>
           
                    </div>
                   
                   <div className="acc_item_qty">
                            <label>Quantity</label>
                            <div className="cls" onClick={this.clsMinDialog.bind(this,"acc_item_qty")}></div>
                           
                            <div className="qty_ctrls_wrapper">	
                                
                               <li><input type='text' className='acc_txt_up_qty'  /></li> 
                                <li><input type='button' value='Update' onClick={this.qtyUpAccClick} /></li>
                            </div>
           
                    </div>

                    <div className="acc_trans_item_qty">
                            <label>Quantity</label>
                            <div className="cls" onClick={this.clsMinDialog.bind(this,"acc_trans_item_qty")}></div>
                           
                            <div className="qty_ctrls_wrapper">	
                                
                               <li><input type='text' className='acc_trans_txt_up_qty'  /></li> 
                                <li><input type='button' value='Update' onClick={this.qtyUpAccTransClick} /></li>
                            </div>
           
                    </div>

                    {/**
                         <div className="close_drawer_conf">
                            <div className="cls" onClick={this.clsDialog.bind(this,"close_drawer_conf")}></div>
                            <b className="drawer_info"></b>
                         </div>
                    */}
                   

                    <div className = "login">
                        
                        <div className="inside">
                            <div className="logo">

                        
                            </div>
                            <div className="message"></div>
                        <ul>
                        <li><input type="text" placeholder="Username" id="username_ipt" onKeyPress={this.loginLogic} /></li>
                        <li><input type="password" placeholder="Password" id="passwd_ipt" onKeyPress={this.loginLogic}  /></li>
                        <li><input type="button" id="login_btn" onClick={this.btnLogin}  className="login_btn" value="Login" /></li>
                        </ul>
                        
                        
                        </div>
                    </div>

                    
            </div>
        );
    }
});

ReactDOM.render(
    <div>
        <Teller />
    </div>
,document.getElementById('teller'));