let TabTransDash = React.createClass({
    componentDidMount() {

        var ctx = document.getElementById("hourlyChart").getContext('2d');

        var ctxh = document.getElementById("dailyChart").getContext('2d');

        $.ajax({
            url:server_url+"/hourly_graph",
            data:{"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let res = JSON.parse(data);

                var myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['7 AM','8 AM','9 AM','10 AM','11 AM','12 AM','1 PM','2 PM','3 PM','4 PM','5 PM','6 PM','7 PM','8 PM','9 PM','10 PM'],
                        datasets: [{
                            label: 'Hourly Transactions',
                            data: [res[0], res[1], res[2], res[3], res[4], res[5],res[6], res[7], res[8], res[9], res[10], res[11], res[12], res[13]],
                            backgroundColor: [
                                'rgba(54, 162, 235, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(54, 162, 235, 1)'
                            ]
                        }]
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero:true
                                }
                            }]
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
            url:server_url+"/daily_graph",
            data:{"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                console.log(data);
                let res = JSON.parse(data);

                var myChart = new Chart(ctxh, {
                    type: 'bar',
                    data: {
                        labels: ['Mon','Tue','Wed','Thur','Fri','Sat','Sun'],
                        datasets: [{
                            label: 'Daily Transactions',
                            data: [res[0], res[1], res[2], res[3], res[4], res[5]],
                            backgroundColor: [
                                'rgba(255, 159, 64, 1)',
                                'rgba(255, 159, 64, 1)',
                                'rgba(255, 159, 64, 1)',
                                'rgba(255, 159, 64, 1)',
                                'rgba(255, 159, 64, 1)',
                                'rgba(255, 159, 64, 1)'
                                
                            ]
                        }]
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero:true
                                }
                            }]
                        }
                    }
                });
               
            }
        });

        

        //Loads on load
        $.ajax({
            url:server_url+"/dash_totals",
            data:{"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let res = JSON.parse(data);
                this.setState({dashTotalCash:res.cash});
                this.setState({dashTotalMpesa:res.mpesa});
                this.setState({dashTotalCard:res.card});
                this.setState({dashTotalAcc:res.acc});
                this.setState({dashTotalColl:res.cheque});
                this.setState({dashTotal:res.total});

                this.setState({dashQtyCash:res.cash_qty});
                this.setState({dashQtyMpesa:res.mpesa_qty});
                this.setState({dashQtyCard:res.card_qty});
                this.setState({dashQtyAcc:res.acc_qty});
                this.setState({dashQtyColl:res.cheque_qty});
                this.setState({dashQty:res.total_qty});

                
                
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });
       
    },
    getInitialState:function(){
        return{
            totalQty:"",
            totalCashSales:"",
            totalTax:"",
            cashAtHand:"",
            totalDrawings:"",
            dashTotalCash:"",
            dashTotalMpesa:"",
            dashTotalCard:"",
            dashTotalAcc:"",
            dashTotalColl:"",
            dashTotal:"",
            dashQtyCash:"",
            dashQtyMpesa:"",
            dashQtyCard:"",
            dashQtyAcc:"",
            dashQtyColl:"",
            dashQty:""


        }
    },
    render:function(){
        return(
            <div className='ad_wraps'>

                <div id="dash_totals">
                <div className="dashCards">
                        <div className="dashCardsHead">
                            Cash
                        </div>
                        <div className="dashCardsBody">
                            <li><label>Total</label></li>
                            <li><b>{this.state.dashTotalCash}</b></li>
                        </div>
                        <div className="dashCardsFooter">
                            <li><label>Qty</label></li>
                            <li><b>{this.state.dashQtyCash}</b></li>
                        </div>
                    </div>
                    <div className="dashCards">
                        <div className="dashCardsHead">
                            MPESA
                        </div>
                        <div className="dashCardsBody">
                            <li><label>Total</label></li>
                            <li><b>{this.state.dashTotalMpesa}</b></li>
                        </div>
                        <div className="dashCardsFooter">
                            <li><label>Qty</label></li>
                            <li><b>{this.state.dashQtyMpesa}</b></li>
                        </div>
                    </div>
                    <div className="dashCards">
                        <div className="dashCardsHead">
                            CARD 
                        </div>
                        <div className="dashCardsBody">
                            <li><label>Total</label></li>
                            <li><b>{this.state.dashTotalCard}</b></li>
                        </div>
                        <div className="dashCardsFooter">
                            <li><label>Qty</label></li>
                            <li><b>{this.state.dashQtyCard}</b></li>
                        </div>
                    </div>
                    <div className="dashCards">
                        <div className="dashCardsHead">
                            ACCOUNT
                        </div>
                        <div className="dashCardsBody">
                            <li><label>Total</label></li>
                            <li><b>{this.state.dashTotalAcc}</b></li>
                        </div>
                        <div className="dashCardsFooter">
                            <li><label>Qty</label></li>
                            <li><b>{this.state.dashQtyAcc}</b></li>
                        </div>
                    </div>
                    <div className="dashCards">
                        <div className="dashCardsHead">
                            CHEQUE
                        </div>
                        <div className="dashCardsBody">
                            <li><label>Total</label></li>
                            <li><b>{this.state.dashTotalColl}</b></li>
                        </div>
                        <div className="dashCardsFooter">
                            <li><label>Qty</label></li>
                            <li><b>{this.state.dashQtyColl}</b></li>
                        </div>
                    </div>
                    <div className="dashCards">
                        <div className="dashCardsHead">
                            TOTAL
                        </div>
                        <div className="dashCardsBody">
                            <li><label>Total</label></li>
                            <li><b>{this.state.dashTotal}</b></li>
                        </div>
                        <div className="dashCardsFooter">
                            <li><label>Qty</label></li>
                            <li><b>{this.state.dashQty}</b></li>
                        </div>
                    </div>
                
                    
                </div>

                <div className="dashboard_graph" id="dashboard_graph">
                  
                        <canvas id="hourlyChart" width="100" height="75"></canvas>
                   
                </div>
                <div className="dashboard_graph" id="sec_dashboard_graph">
                        <canvas id="dailyChart" width="100" height="71"></canvas>
                </div>
            </div>
        );
    }
});


let TabTransCustReports = React.createClass({
    componentDidMount() {
        
        let optional_config = {
            mode: "range",
            dateFormat: "d-m-Y"
        };

        $(".datePick").flatpickr(optional_config);

        $(".detDatePick").flatpickr(optional_config);

        $.ajax({
            url:server_url+"/todays_trans_reports",
            data:{filter:"customer","_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let res = JSON.parse(data);
                let arr = [];
                                        
                $.each(res,function(index,value){
                    arr.push(res[index]);
                });

                this.setState({rowItem:arr});
            
            }
        });

        $.ajax({
            url:server_url+"/todays_trans_totals",
            data:{filter:"customer","_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let res = JSON.parse(data);
                this.setState({total_qty:res.qty});
                this.setState({totals:res.total});
                this.setState({total_tax:res.tax});
                this.setState({gross:res.gross});
                this.setState({net:res.net});
                this.setState({total_cost:res.total_cost});
                this.setState({total_discount:res.total_discount});
                    
            
            }
        });

        $.ajax({
            url:server_url+"/get_active_users",
            data:{"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                let res = JSON.parse(data);
                let arr = [];
                                        
                $.each(res,function(index,value){
                    arr.push(res[index]);
                });

                this.setState({rowUser:arr});
                
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }

        });

        $.ajax({
            url:server_url+"/get_active_branch_rprt_data",
            data:{"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                let res = JSON.parse(data);
                let arr = [];
                                        
                $.each(res,function(index,value){
                    arr.push(res[index]);
                });

                this.setState({rowBranch:arr});
                
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }

        });


        $.ajax({
            url:server_url+"/get_active_events",
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

                this.setState({optItem:arr});
            
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

    },
    getInitialState:function(){
        return{
            rowItem:[],
            rowUser:[],
            rowBranch:[],
            total_qty:"",
            total_tax:"",
            totals:"",
            gross:"",
            net:"",
            total_cost:"",
            total_discount:"",
            detsItem:[],
            detsTtlQty:"",
            detsTtlTax:"",
            detsTtl:"",
            detsChange:"",
            detsCash:"",
            detsReceiptNo:"",
            detsRefNo:"",
            detsTransType:"",
            detsTransComment:"",
            poolScIt:"",
            optItem:[],
            chkStatus: true,
            chkStatusM: true,
            chkStatusM: true,
            chkStatusR: true,
            chkStatusRT: true,
            chkStatusPDQ:true,
            chkStatusCheque:true

        }
    },
    eachOptRow:function(item,i){
        return(
            <option key={i} value={item.id}>
                {item.event}       
            </option>
        )
    },
    getTransDets:function(i){

        //Full screen Logic
        var el = document.documentElement, rfs = el.requestFullScreen || el.webkitRequestFullScreen || el.mozRequestFullScreen;   
        rfs.call(el);
        
        $.ajax({
            url:server_url+"/get_trans_dets",
            data:{id:i,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let res = JSON.parse(data);
                let arr = [];
                   
                  
                $.each(res,function(index,value){
                    arr.push(res[index]);
                });

                this.setState({detsItem:arr});
                
                $('.roll_details').fadeIn("slow");
                
            }

        });

        $.ajax({
            url:server_url+"/get_trans_dets_totals",
            data:{id:i,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let res = JSON.parse(data);
                //'cash','change','total_tax','total'
                this.setState({detsCash:res.cash});
                this.setState({detsTtlTax:res.total_tax});
                this.setState({detsTtlQty:res.ttl_qty});
                this.setState({detsTtl:res.total});
                this.setState({detsChange:res.change});
                this.setState({detsReceiptNo:res.receipt_no});
                this.setState({detsRefNo:res.ref_no});
                this.setState({detsTransType:res.type});
                this.setState({detsTransComment:res.comment});
                this.setState({detsDiscount:res.discount});
                //detsTtlDisc
            }

        });
        
    },
    getTransUpDets:function(i){
        
        var el = document.documentElement, rfs = el.requestFullScreen || el.webkitRequestFullScreen || el.mozRequestFullScreen;   
        rfs.call(el);

        
         let qry = "SELECT * FROM shopping_cart WHERE tid='"+i+"'";
        //server_url+"/get_trans_dets"
            $.ajax({
                url:"http://www.ababuapps.com/up_pos_test/custom_reports.php",
                data:{id:i,rprt:"transaction_dets",qry_str:qry,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    console.log(data);
                    let res = JSON.parse(data);
                    let arr = [];
                       
                      
                    $.each(res,function(index,value){
                        arr.push(res[index]);
                    });
    
                    this.setState({detsItem:arr});
                    
                    $('.roll_details').fadeIn("slow");
                    
                }
    
            });
            

            let ttl_qry = " SELECT * FROM transactions WHERE up_id='"+i+"'";
            $.ajax({
                url:"http://www.ababuapps.com/up_pos/custom_reports.php",
                data:{id:i,rprt:"transaction_dets_totals",qry_str:ttl_qry,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);
                    //'cash','change','total_tax','total'
                    this.setState({detsCash:res.cash});
                    this.setState({detsTtlTax:res.total_tax});
                    this.setState({detsTtlQty:res.ttl_qty});
                    this.setState({detsTtl:res.total});
                    this.setState({detsChange:res.change});
                    this.setState({detsReceiptNo:res.receipt_no});
                    this.setState({detsRefNo:res.ref_no});
                    this.setState({detsTransType:res.type});
                    this.setState({detsTransComment:res.comment});
                    this.setState({detsDiscount:res.discount});
                    //detsTtlDisc
                }
    
            });

    },
    selectEvent:function(id){
        this.setState({poolScIt:id});
        $('.pool_window').fadeIn('slow');
    },
    sendToPool:function(){
        let id = this.state.poolScIt;
        let event = $('.sel_pool_event').val();

        if(event !=''){
            $.ajax({
                url:server_url+"/send_to_pool",
                data:{id:id,event:event,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);
                    $('.msg_wrap').empty();
                    $('.sel_pool_event').val('');
                    $('.pool_window').fadeOut('slow');
                    $.ajax({
                        url:server_url+"/get_trans_dets",
                        data:{id:res.tid,"_token":cs_token},
                        type:"POST",
                        context: this,
                        success:function(data){
                            //console.log(data);
                            let res = JSON.parse(data);
                            let arr = [];
                               
                              
                            $.each(res,function(index,value){
                                arr.push(res[index]);
                            });
            
                            this.setState({detsItem:arr});
                            
                            $('.roll_details').fadeIn("slow");
                            
                        }
            
                    });

                    let qry = "UPDATE shopping_cart SET type='pool' WHERE up_id='"+id+"' ";

                    console.log(qry);

                    $.ajax({
                        url:"http://www.ababuapps.com/up_pos_test/customs.php",
                        data:{qry_str:qry,"_token":cs_token},
                        type:"POST",
                        context: this,
                        success:function(data){
                            //console.log(data);
                        }
                    });
                    
                }
    
            });
        }else{
            $('.msg_wrap').html("<div class='err'>Error: Please Select Event</div>")
        }
        
        
    },
    eachDet:function(item,i){
        if(item.type=='pool'){
            return(
                <tr key={i}>
                    <td>{item.item}</td>
                    <td>{item.qty}</td>
                    <td>{item.tax}</td>
                    <td>{item.price}</td>
                    <td>{item.total}</td>
                    <td></td>
                                
                </tr>
            )
        }else{
            return(
                <tr key={i}>
                    <td>{item.item}</td>
                    <td>{item.qty}</td>
                    <td>{item.tax}</td>
                    <td>{item.price}</td>
                    <td>{item.total}</td>
                    <td><a className="rprt_link" href='#' onClick={this.selectEvent.bind(this,item.id)}>Pool</a></td>
                                
                </tr>
            )
        }
        
    },
    eachUser:function(item,i){
        return(
            <option key={i} value={item.id}>
                    {item.fname + ' ' + item.lname}          
            </option>
        )
    },
    eachBranch:function(item,i){
        return(
            <option key={i} value={item.id}>
                    {item.branch}          
            </option>
        )
    },
    eachRow:function(item,i){
        
            if(item.invoice =='' || item.invoice ==null){
                return(
                    <tr key={i}>
        
                        <td>{item.no}</td>
                        <td>{item.trans_time}</td>
                        <td>{item.receipt_no}</td>
                        <td>{item.no_items}</td>
                        <td>{item.total}</td>
                        <td>{item.customer}</td>
                        <td>{item.user}</td>
                        <td>{item.branch}</td>
                        <td><a className="rprt_link" onClick={this.getTransDets.bind(this,item.id)} href='#'>Details</a></td>
                        <td><a className="rprt_link" onClick={this.printInvoice.bind(this,item.id)} href='#'>Invoice</a></td>
                        <td></td>
                                     
                    </tr>
                )
            }else{
                return(
                    <tr key={i}>
        
                        <td>{item.no}</td>
                        <td>{item.trans_time}</td>
                        <td>{item.receipt_no}</td>
                        <td>{item.no_items}</td>
                        <td>{item.total}</td>
                        <td>{item.customer}</td>
                        <td>{item.user}</td>
                        <td>{item.branch}</td>
                        <td><a className="rprt_link" onClick={this.getTransDets.bind(this,item.id)} href='#'>Details</a></td>
                        <td><a className="rprt_link" target='_blank' href={server_url+'/'+item.invoice}>Download</a></td>
                        <td></td>
                                     
                    </tr>
                )
            }

    },
    printInvoice:function(i){
        $.ajax({
            url:server_url+"/create_invoice",
            data:{id:i,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                console.log(data); 

                $.ajax({
                    url:server_url+"/todays_trans_reports",
                    data:{filter:"customer","_token":cs_token},
                    type:"POST",
                    context: this,
                    success:function(data){
                        console.log(data);
                        let res = JSON.parse(data);
                        let arr = [];
                                                
                        $.each(res,function(index,value){
                            arr.push(res[index]);
                        });
        
                        this.setState({rowItem:arr});
                    
                    }
                });
                
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });
    },
    clsDialog:function(i,event){
        $("."+i).fadeOut('slow');
        $('.msg').empty();
        $('.detDatePick').val('');
    },
    searchTrans:function(){
        let dates = $('.datePick').val();

        if(dates !=""){

            if(dates.indexOf("to")){
 
            $.ajax({
                url:server_url+"/search_transactions",
                data:{filter:"customers",rec_no:"",dates:dates,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    
                    let res = JSON.parse(data);
                    let arr = [];
                                            
                    $.each(res,function(index,value){
                        arr.push(res[index]);
                    });

                    this.setState({rowItem:arr});
                    
                }
            });

            $.ajax({
                url:server_url+"/search_trans_totals",
                data:{filter:"customers",rec_no:"",dates:dates,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){

                    let res = JSON.parse(data);
                    this.setState({total_qty:res.qty});
                    this.setState({totals:res.total});
                    this.setState({total_tax:res.tax});
                    this.setState({gross:res.gross});
                    this.setState({net:res.net});
                    this.setState({total_cost:res.total_cost});
                    this.setState({total_discount:res.total_discount});
                    
                }
            });

        }

        }
        
    },
    detailedTransSearch:function(){
        $('.roll_search').fadeIn('slow');
    }
    /*
    ,
    detSearchBtnAct:function(){
        console.log("check");
        let dates = $('.detDatePick').val();
        let receipt_no = $('.detReceiptNo').val();
        let ref_no = $('.detRefNo').val();
        let users = $('.selUsers').val();
        let branch = $('.selBranch').val();
    
        let cash = this.state.chkStatus ? 1 : 0;
        let mpesa = this.state.chkStatusM ? 1 : 0;
        let rtrn = this.state.chkStatusR ? 1 : 0;
        let rtrn_tender = this.state.chkStatusRT ? 1 : 0;
        let pdq = this.state.chkStatusPDQ ? 1 : 0;
       
        if(dates !=""){
            $.ajax({
                url:server_url+"/alt_detailed_search_transactions",
                data:{users:users,branch:branch,pdq:pdq,cash:cash,mpesa:mpesa,rtrn:rtrn,rtrn_tender:rtrn_tender,ref_no:ref_no,receipt_no:receipt_no,dates:dates,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);
                    if(res.status !=0){
                        let res = JSON.parse(data);
                        let arr = [];
                                                
                        $.each(res,function(index,value){
                            arr.push(res[index]);
                        });
        
                        this.setState({rowItem:arr});

                        $('.roll_search').fadeOut('slow');
                    }else{
                        $('.msg').html("<div class='err'>Error: No Results</div>");
                    }
                    
                   $('.msg').empty();
                },error: function(xhr, status, text) {
    
                    if(xhr.status ==419){
                        window.location = server_url;
                    }
    
                }
            });

            $.ajax({
                url:server_url+"/alt_detailed_total_search_transactions",
                data:{users:users,branch:branch,pdq:pdq,cash:cash,mpesa:mpesa,rtrn:rtrn,rtrn_tender:rtrn_tender,ref_no:ref_no,receipt_no:receipt_no,dates:dates,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    console.log(data);
                    let res = JSON.parse(data);
                    this.setState({total_qty:res.qty});
                    this.setState({totals:res.total});
                    this.setState({total_tax:res.tax});
                    this.setState({gross:res.gross});
                    this.setState({net:res.net});
                    this.setState({total_cost:res.total_cost});
                    this.setState({total_discount:res.total_discount});
                   $('.msg').empty();
                },error: function(xhr, status, text) {
    
                    if(xhr.status ==419){
                        window.location = server_url;
                    }
    
                }
            });
        }else{
            $('.msg').html("<div class='err'>Error: Select Dates</div>");
        }
        

    }
    */,
    render:function(){
        return(
            <div className='ad_wraps'>

                <div className='rprt_top_title'>
                    <input type="text" className="datePick" placeholder="Select Dates" />
                    <div className='search_btn' onClick={this.searchTrans}></div>
                   
               </div>
                    
                <div className='reports_table_wrap_trans'>

                    <table>
                    <thead>
                        <tr><th>No.</th><th>Date</th><th>Receipt No.</th><th>Qty</th><th>Total</th><th>Customer</th><th>User</th><th>Branch</th><th></th><th></th></tr>
                                            
                        </thead>
                        <tbody>
                        {
                            this.state.rowItem.map(this.eachRow)
                            }
                        </tbody>
                    </table>
                </div>
                <div className='reports_totals_wrap'>
                    <table>
                        <tbody>
                        <tr>
                            <th><p>Quantity</p></th>
                            <th><p>Total Tax</p></th>
                            <th><p>Total Cost</p></th>
                            <th><p>Discount</p></th>
                            <th><p>Amount</p></th>
                            <th><p>Gross Profit</p></th>
                            <th><p>Net Profit</p></th>
                        </tr>
                        <tr>
                            <td><b>{this.state.total_qty}</b></td>
                            <td><b>{this.state.total_tax}</b></td>
                            <td><b>{this.state.total_cost}</b></td>
                            <td><b>{this.state.total_discount}</b></td>
                            <td><b>{this.state.totals}</b></td>
                            <td><b>{this.state.gross}</b></td>
                            <td><b>{this.state.net}</b></td>
                        </tr>
                        </tbody>
                    </table>
                 </div>


                <div className='roll_details'>
                            <div className="cls_blk" onClick={this.clsDialog.bind(this,"roll_details")}></div>
                            
                            <div className='roll_details_head'>
                                
                                <table>
                                    <tr>
                                        <th>Receipt No.</th><th>Ref No.</th><th>Type</th><th>Comment</th>
                                    </tr>
                                    <tr>
                                        <td><b>{this.state.detsReceiptNo}</b></td><td><b>{this.state.detsRefNo}</b></td><td><b>{this.state.detsTransType}</b></td><td><b>{this.state.detsTransComment}</b></td>
                                    </tr>
                                </table>
                            </div>

                            <div id="rprt_rolls_dets" className="roll_dets_cont">
                                <table>

                                    <tbody>
                                        <tr>
                                            <th>Item Description</th><th>Qty</th><th>Tax</th><th>Price</th><th>Total</th><th></th>
                                        </tr>
                                         {this.state.detsItem.map(this.eachDet)}
                                    </tbody>
                                </table>
                            </div>

              
                                <div id="rprt_rolls_ttls" className="roll_ttls">
                                <table>

                                    <tbody>
                                            <tr>
                                            <th>Total Qty</th><th>Total Tax</th><th>Discount</th><th>Total</th><th>Cash</th><th>Change</th>
                                            </tr>
                                            <tr>
                                            <td><b>{this.state.detsTtlQty}</b></td><td><b>{this.state.detsTtlTax}</b></td><td><b>{this.state.detsDiscount}</b></td><td><b>{this.state.detsTtl}</b></td><td><b>{this.state.detsCash}</b></td><td><b>{this.state.detsChange}</b></td>
                                            </tr>
                                        </tbody>
                                </table>
                            </div>
                           
                            

                        </div>

                        <div className="pool_window">
                            <label>Select Event</label>
                            <div className="cls" onClick={this.clsDialog.bind(this,"pool_window")}></div>
                           <div className='msg_wrap'></div>
                            <div className="qty_ctrls_wrapper">	
                                
                                <li>
                                
                                    <select className='sel_pool_event'>
                                        <option></option>
                                        {this.state.optItem.map(this.eachOptRow)}

                                    </select>
                                
                                </li>
                                <li>
                                        <input type='button' onClick={this.sendToPool} value='Send to Pool' />
                                </li>
                            </div>
           
                        </div>
            </div>
        );
    }
});

let TabTransReports = React.createClass({
    componentDidMount() {
        
        $.ajax({
            url:server_url+"/get_admin_user_priviledges",
            data:{"_token": cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let resx = JSON.parse(data);

                if(resx.remote_branch_access ==0){
                    $('.up_branch').hide();
                }else{
                    $('.up_branch').show();
                }

            }
        });

        let optional_config = {
            mode: "range",
            dateFormat: "d-m-Y"
        };

        $(".datePick").flatpickr(optional_config);

        $(".detDatePick").flatpickr(optional_config);

        $(".branch_reports_dates").flatpickr(optional_config);

        $.ajax({
            url:server_url+"/todays_trans_reports",
            data:{filter:"all","_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let res = JSON.parse(data);
                let arr = [];
                                        
                $.each(res,function(index,value){
                    arr.push(res[index]);
                });

                this.setState({rowItem:arr});
            
            }
        });

        $.ajax({
            url:server_url+"/todays_trans_totals",
            data:{filter:"all","_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let res = JSON.parse(data);
                this.setState({total_qty:res.qty});
                this.setState({totals:res.total});
                this.setState({total_tax:res.tax});
                this.setState({gross:res.gross});
                this.setState({net:res.net});
                this.setState({total_cost:res.total_cost});
                this.setState({total_discount:res.total_discount});
                    
            
            }
        });

        $.ajax({
            url:server_url+"/get_active_users",
            data:{"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                let res = JSON.parse(data);
                let arr = [];
                                        
                $.each(res,function(index,value){
                    arr.push(res[index]);
                });

                this.setState({rowUser:arr});
                
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }

        });

        $.ajax({
            url:server_url+"/get_active_branch_rprt_data",
            data:{"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                let res = JSON.parse(data);
                let arr = [];
                                        
                $.each(res,function(index,value){
                    arr.push(res[index]);
                });

                this.setState({rowBranch:arr});
                
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }

        });

    },
    getInitialState:function(){
        return{
            rowItem:[],
            rowUser:[],
            rowBranch:[],
            total_qty:"",
            total_tax:"",
            totals:"",
            gross:"",
            net:"",
            total_cost:"",
            total_discount:"",
            detsItem:[],
            detsTtlQty:"",
            detsTtlTax:"",
            detsTtl:"",
            detsChange:"",
            detsCash:"",
            detsReceiptNo:"",
            detsRefNo:"",
            detsTransType:"",
            detsTransComment:"",
            chkStatus: true,
            chkStatusM: true,
            chkStatusM: true,
            chkStatusR: true,
            chkStatusRT: true,
            chkStatusPDQ:true,
            chkStatusCheque:true
            

        }
    },
    getTransUpDets:function(i){
        //Main Transactions Reports
        var el = document.documentElement, rfs = el.requestFullScreen || el.webkitRequestFullScreen || el.mozRequestFullScreen;   
        rfs.call(el);

        
         let qry = "SELECT * FROM shopping_cart WHERE tid='"+i+"'";
        //server_url+"/get_trans_dets"
            $.ajax({
                url:"http://www.ababuapps.com/up_pos_test/custom_reports.php",
                data:{id:i,rprt:"transaction_dets",qry_str:qry,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    console.log(data);
                    let res = JSON.parse(data);
                    let arr = [];
                       
                      
                    $.each(res,function(index,value){
                        arr.push(res[index]);
                    });
    
                    this.setState({detsItem:arr});
                    
                    $('.roll_details').fadeIn("slow");
                    
                }
    
            });

            let ttl_qry = " SELECT * FROM transactions WHERE up_id='"+i+"'";
            $.ajax({
                url:"http://www.ababuapps.com/up_pos_test/custom_reports.php",
                data:{id:i,rprt:"transaction_dets_totals",qry_str:ttl_qry,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);
                    //'cash','change','total_tax','total'
                    this.setState({detsCash:res.cash});
                    this.setState({detsTtlTax:res.total_tax});
                    this.setState({detsTtlQty:res.ttl_qty});
                    this.setState({detsTtl:res.total});
                    this.setState({detsChange:res.change});
                    this.setState({detsReceiptNo:res.receipt_no});
                    this.setState({detsRefNo:res.ref_no});
                    this.setState({detsTransType:res.type});
                    this.setState({detsTransComment:res.comment});
                    this.setState({detsDiscount:res.discount});
                    //detsTtlDisc
                }
    
            });

    },
    getTransDets:function(i, event){
        //Main Transactions Reports
        $.ajax({
            url:server_url+"/get_trans_dets",
            data:{id:i,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let res = JSON.parse(data);
                let arr = [];
                   
                  
                $.each(res,function(index,value){
                    arr.push(res[index]);
                });

                this.setState({detsItem:arr});
                
                $('.roll_details').fadeIn("slow");
                
            }

        });

        $.ajax({
            url:server_url+"/get_trans_dets_totals",
            data:{id:i,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let res = JSON.parse(data);
                //'cash','change','total_tax','total'
                this.setState({detsCash:res.cash});
                this.setState({detsTtlTax:res.total_tax});
                this.setState({detsTtlQty:res.ttl_qty});
                this.setState({detsTtl:res.total});
                this.setState({detsChange:res.change});
                this.setState({detsReceiptNo:res.receipt_no});
                this.setState({detsRefNo:res.ref_no});
                this.setState({detsTransType:res.type});
                this.setState({detsTransComment:res.comment});
                this.setState({detsDiscount:res.discount});
                //detsTtlDisc
            }

        });
        
    },
    eachDet:function(item,i){
        return(
            <tr key={i}>
                <td>{item.item}</td>
                <td>{item.qty}</td>
                <td>{item.tax}</td>
                <td>{item.price}</td>
                <td>{item.total}</td>
                <td></td>
                            
            </tr>
        )
    },
    eachUser:function(item,i){
        return(
            <option key={i} value={item.id}>
                    {item.fname + ' ' + item.lname}          
            </option>
        )
    },
    eachBranch:function(item,i){
        return(
            <option key={i} value={item.id}>
                    {item.branch}          
            </option>
        )
    },
    eachRow:function(item,i){
        
        if(item.up_id =='' || item.up_id ==null){

            return(
                <tr key={i}>

                    <td>{item.no}</td>
                    <td>{item.trans_time}</td>
                    <td>{item.receipt_no}</td>
                    <td>{item.no_items}</td>
                    <td>{item.total}</td>
                    <td>{item.type}</td>
                    <td>{item.ref_no}</td>
                    <td>{item.user}</td>
                    <td>{item.branch}</td>
                    <td><a className="rprt_link" onClick={this.getTransDets.bind(this,item.id)} href='#'>Details</a></td>
                    <td></td>
                                
                </tr>
            )

        }else{

            return(
                <tr key={i}>

                    <td>{item.no}</td>
                    <td>{item.trans_time}</td>
                    <td>{item.receipt_no}</td>
                    <td>{item.no_items}</td>
                    <td>{item.total}</td>
                    <td>{item.type}</td>
                    <td>{item.ref_no}</td>
                    <td>{item.user}</td>
                    <td>{item.branch}</td>
                    <td><a className="rprt_link" onClick={this.getTransUpDets.bind(this,item.up_id)} href='#'>Details</a></td> 
                    <td></td>
                                
                </tr>
            )

        }
    },
    clsDialog:function(i,event){
        $("."+i).fadeOut('slow');
        $('.msg').empty();
        $('.detDatePick').val('');
    },
    searchTrans:function(){
        let dates = $('.datePick').val();

        if(dates !=""){

            if(dates.indexOf("to")){
 
            $.ajax({
                url:server_url+"/search_transactions",
                data:{filter:"all",rec_no:"",dates:dates,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    
                    let res = JSON.parse(data);
                    let arr = [];
                                            
                    $.each(res,function(index,value){
                        arr.push(res[index]);
                    });

                    this.setState({rowItem:arr});
                    
                }
            });

            $.ajax({
                url:server_url+"/search_trans_totals",
                data:{filter:"all",rec_no:"",dates:dates,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){

                    let res = JSON.parse(data);
                    this.setState({total_qty:res.qty});
                    this.setState({totals:res.total});
                    this.setState({total_tax:res.tax});
                    this.setState({gross:res.gross});
                    this.setState({net:res.net});
                    this.setState({total_cost:res.total_cost});
                    this.setState({total_discount:res.total_discount});
                    
                }
            });

        }

        }
        
    },
    detailedTransSearch:function(){
        $('.roll_search').fadeIn('slow');
    },
    handleCash:function(event){
        const target = event.target;
        const value = target.type === 'checkbox' ? target.checked : target.value;
        const name = target.name;
        this.setState({chkStatus: value});
    },
    handleMpesa:function(event){
        const target = event.target;
        const value = target.type === 'checkbox' ? target.checked : target.value;
        const name = target.name;
        this.setState({chkStatusM: value});
    },
    handleReturnTender:function(event){
        const target = event.target;
        const value = target.type === 'checkbox' ? target.checked : target.value;
        const name = target.name;
        this.setState({chkStatusRT: value});
    },
    handleReturn:function(event){
        const target = event.target;
        const value = target.type === 'checkbox' ? target.checked : target.value;
        const name = target.name;
        this.setState({chkStatusR: value});
    },
    handleReturnPDQ:function(event){
        const target = event.target;
        const value = target.type === 'checkbox' ? target.checked : target.value;
        const name = target.name;
        this.setState({chkStatusPDQ: value});
    },
    handleCheque:function(event){
        const target = event.target;
        const value = target.type === 'checkbox' ? target.checked : target.value;
        const name = target.name;
        this.setState({chkStatusCheque: value});
    },
    detSearchBtnAct:function(){
        
        let dates = $('.detDatePick').val();
        let receipt_no = $('.detReceiptNo').val();
        let ref_no = $('.detRefNo').val();
        let users = $('.selUsers').val();
        let branch = $('.selBranch').val();
    
        let cash = this.state.chkStatus ? 1 : 0;
        let mpesa = this.state.chkStatusM ? 1 : 0;
        let rtrn = this.state.chkStatusR ? 1 : 0;
        let rtrn_tender = this.state.chkStatusRT ? 1 : 0;
        let pdq = this.state.chkStatusPDQ ? 1 : 0;
        let cheque = this.state.chkStatusCheque ? 1 : 0;
        
        if(dates !=""){
            $.ajax({
                url:server_url+"/alt_detailed_search_transactions",
                data:{users:users,branch:branch,cheque:cheque,pdq:pdq,cash:cash,mpesa:mpesa,rtrn:rtrn,rtrn_tender:rtrn_tender,ref_no:ref_no,receipt_no:receipt_no,dates:dates,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    console.log(data);
                    let res = JSON.parse(data);
                    if(res.status !=0){
                        let res = JSON.parse(data);
                        let arr = [];
                                                
                        $.each(res,function(index,value){
                            arr.push(res[index]);
                        });

                        $('.detDatePick').val('');
                        $('.detReceiptNo').val('');
                        $('.detRefNo').val('');
                        $('.selUsers').val('');
                        $('.selBranch').val('');
        
                        this.setState({rowItem:arr});

                        $('.roll_search').fadeOut('slow');
                    }else{
                        $('.msg').html("<div class='err'>Error: No Results</div>");
                    }
                    
                   $('.msg').empty();
                },error: function(xhr, status, text) {
    
                    if(xhr.status ==419){
                        window.location = server_url;
                    }
    
                }
            });

            $.ajax({
                url:server_url+"/alt_detailed_total_search_transactions",
                data:{users:users,branch:branch,cheque:cheque,pdq:pdq,cash:cash,mpesa:mpesa,rtrn:rtrn,rtrn_tender:rtrn_tender,ref_no:ref_no,receipt_no:receipt_no,dates:dates,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    console.log(data);
                    let res = JSON.parse(data);
                    this.setState({total_qty:res.qty});
                    this.setState({totals:res.total});
                    this.setState({total_tax:res.tax});
                    this.setState({gross:res.gross});
                    this.setState({net:res.net});
                    this.setState({total_cost:res.total_cost});
                    this.setState({total_discount:res.total_discount});
                   $('.msg').empty();
                },error: function(xhr, status, text) {
    
                    if(xhr.status ==419){
                        window.location = server_url;
                    }
    
                }
            });
        }else{
            $('.msg').html("<div class='err'>Error: Select Dates</div>");
        }
        

    },
    selUpBranch:function(){
        $('.roll_branch').fadeIn('slow');
    },
    fetchUpBranchData:function(id){
        //check not to get current branch data;

        //console.log(id);

        let dates = $('.branch_reports_dates').val();

        if(dates !=""){

            $.ajax({
                url:server_url+"/up_reports_check",
                data:{"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);

                    if(id=="all"){

                            $('.roll_branch').fadeOut('slow');
                            //console.log(data);
                
                            let qry = "SELECT * FROM transactions WHERE ";
        
                            $.ajax({
                                url:"http://www.ababuapps.com/up_pos/custom_reports.php",
                                data:{dates:dates,dates_col:"trans_time",qry_str:qry,rprt:"all_transactions","_token":cs_token},
                                type:"POST",
                                context: this,
                                success:function(data){
                                    console.log(data);
                                    $('.branch_reports_dates').val('');
                                    let res = JSON.parse(data);
                                    let arr = [];
                                                            
                                    $.each(res,function(index,value){
                                        arr.push(res[index]);
                                    });
                    
                                    this.setState({rowItem:arr});
                                }
                            });
        
                            let ttl_qry = "SELECT SUM(total) AS total, SUM(total_tax) AS tax, SUM(no_items) AS qty, SUM(discount) AS total_discount, SUM(total_cost) AS total_cost FROM transactions WHERE ";
        
                            $.ajax({
                                url:"http://www.ababuapps.com/up_pos/custom_reports.php",
                                data:{dates:dates,dates_col:"trans_time",qry_str:ttl_qry,rprt:"all_total_transactions","_token":cs_token},
                                type:"POST",
                                context: this,
                                success:function(data){
                                    console.log(data);
                                    let res = JSON.parse(data);
                                    this.setState({total_qty:res.qty});
                                    this.setState({totals:res.total});
                                    this.setState({total_tax:res.tax});
                                    this.setState({gross:res.gross});
                                    this.setState({net:res.net});
                                    this.setState({total_cost:res.total_cost});
                                    this.setState({total_discount:res.total_discount});
                            
                                }
                            });


                    }else{


                        if(res.curr_branch != id){
                            $('.roll_branch').fadeOut('slow');
                            //console.log(data);
                            //let qry = "SELECT * FROM transactions WHERE up_branch='"+id+"'";
                            let qry = "SELECT * FROM transactions WHERE up_branch='"+id+"'";
        
                            $.ajax({
                                url:"http://www.ababuapps.com/up_pos/custom_reports.php",
                                data:{dates:dates,dates_col:"trans_time",qry_str:qry,rprt:"transactions","_token":cs_token},
                                type:"POST",
                                context: this,
                                success:function(data){
                                    //console.log(data);
                                    $('.branch_reports_dates').val('');
                                    let res = JSON.parse(data);
                                    let arr = [];
                                                            
                                    $.each(res,function(index,value){
                                        arr.push(res[index]);
                                    });
                    
                                    this.setState({rowItem:arr});
                                }
                            });
        
                            let ttl_qry = "SELECT SUM(total) AS total, SUM(total_tax) AS tax, SUM(no_items) AS qty, SUM(discount) AS total_discount, SUM(total_cost) AS total_cost FROM transactions WHERE up_branch='"+id+"'";
        
                            $.ajax({
                                url:"http://www.ababuapps.com/up_pos/custom_reports.php",
                                data:{dates:dates,dates_col:"trans_time",qry_str:ttl_qry,rprt:"total_transactions","_token":cs_token},
                                type:"POST",
                                context: this,
                                success:function(data){
                                    //console.log(data);
                                    let res = JSON.parse(data);
                                    this.setState({total_qty:res.qty});
                                    this.setState({totals:res.total});
                                    this.setState({total_tax:res.tax});
                                    this.setState({gross:res.gross});
                                    this.setState({net:res.net});
                                    this.setState({total_cost:res.total_cost});
                                    this.setState({total_discount:res.total_discount});
                            
                                }
                            });
        
                        }

                    }
    
                }
            });

        }else{
            $('.msg').html("<div class='err'>Error: Select Dates</div>")
        }


    },
    render:function(){
        return(
            <div className='ad_wraps'>

                        

                <div className='rprt_top_title'>
                    <input type="text" className="datePick" placeholder="Select Dates" />
                    <div className='search_btn' onClick={this.searchTrans}></div>
                    <div className='detailed_search' onClick={this.detailedTransSearch}></div>
                    <div className='up_branch' onClick={this.selUpBranch}></div>

               </div>
                    
                <div className='reports_table_wrap_trans'>

                    <table>
                    <thead>
                        <tr><th>No.</th><th>Date</th><th>Receipt No.</th><th>Qty</th><th>Total</th><th>Type</th><th>Ref No.</th><th>User</th><th>Branch</th><th></th></tr>
                                            
                        </thead>
                        <tbody>
                        {
                            this.state.rowItem.map(this.eachRow)
                            }
                        </tbody>
                    </table>
                </div>
                <div className='reports_totals_wrap'>
                    <table>
                        <tbody>
                        <tr>
                            <th><p>Quantity</p></th>
                            <th><p>Total Tax</p></th>
                            <th><p>Total Cost</p></th>
                            <th><p>Discount</p></th>
                            <th><p>Amount</p></th>
                            <th><p>Gross Profit</p></th>
                            <th><p>Net Profit</p></th>
                        </tr>
                        <tr>
                            <td><b>{this.state.total_qty}</b></td>
                            <td><b>{this.state.total_tax}</b></td>
                            <td><b>{this.state.total_cost}</b></td>
                            <td><b>{this.state.total_discount}</b></td>
                            <td><b>{this.state.totals}</b></td>
                            <td><b>{this.state.gross}</b></td>
                            <td><b>{this.state.net}</b></td>
                        </tr>
                        </tbody>
                    </table>
                 </div>


                <div className='roll_details'>
                            <div className="cls_blk" onClick={this.clsDialog.bind(this,"roll_details")}></div>
                            <div className='roll_details_head'>
                                
                                <table>
                                    <tr>
                                        <th>Receipt No.</th><th>Ref No.</th><th>Type</th><th>Comment</th>
                                    </tr>
                                    <tr>
                                        <td><b>{this.state.detsReceiptNo}</b></td><td><b>{this.state.detsRefNo}</b></td><td><b>{this.state.detsTransType}</b></td><td><b>{this.state.detsTransComment}</b></td>
                                    </tr>
                                </table>
                            </div>
                            <div id="rprt_rolls_dets" className="roll_dets_cont">
                                <table>

                                    <tbody>
                                        <tr>
                                            <th>Item Description</th><th>Qty</th><th>Tax</th><th>Price</th><th>Total</th>
                                        </tr>
                                        
                                         
                                         { this.state.detsItem.map(this.eachDet) }
                                    
                                    </tbody>
                                </table>
                            </div>

                           
                        
                            <div id="rprt_rolls_ttls" className="roll_ttls">
                                <table>

                                    <tbody>
                                            <tr>
                                            <th>Total Qty</th><th>Total Tax</th><th>Discount</th><th>Total</th><th>Cash</th><th>Change</th>
                                            </tr>
                                            <tr>
                                            <td><b>{this.state.detsTtlQty}</b></td><td><b>{this.state.detsTtlTax}</b></td><td><b>{this.state.detsDiscount}</b></td><td><b>{this.state.detsTtl}</b></td><td><b>{this.state.detsCash}</b></td><td><b>{this.state.detsChange}</b></td>
                                            </tr>
                                        </tbody>
                                </table>
                            </div>
                            
                            
                        

                        </div>

                        <div className='roll_search'>
                        <div className="cls_blk" onClick={this.clsDialog.bind(this,"roll_search")}></div>
                            <div className='msg'></div>
                            <li>Dates</li>
                            <li><input className='detDatePick' type='text' /></li>
                            <li>Receipt No</li>
                            <li><input type='text' className='detReceiptNo' /></li>
                            <li>Ref No</li>
                            <li><input type='text' className='detRefNo' /></li>
                            {/**
                                <li>Branch</li>
                            <li>
                                <select className='selBranch'>
                                    <option></option>
                                    {
                                        this.state.rowBranch.map(this.eachBranch)
                                        }
                                </select>
                            </li>
                                */}
                            
                            <li>Users</li>
                            <li>
                                <select className='selUsers'>
                                    <option></option>
                                        {
                                        this.state.rowUser.map(this.eachUser)
                                        }
                                </select>
                            </li>
                            <table>
                                <tr>
                                    <td><input type='checkbox' className='chkCashSearch' name='chkStatus' checked={this.state.chkStatus} onChange={this.handleCash} />Cash</td>
                                    <td><input type='checkbox' className='chkMPESASearch' checked={this.state.chkStatusM} onChange={this.handleMpesa} />MPESA</td>
                                </tr>
                                <tr>
                                    <td><input type='checkbox' className='chkReturnSearch' checked={this.state.chkStatusR} onChange={this.handleReturn} />Return</td>
                                    <td><input type='checkbox' className='chkReturnTenderSearch' checked={this.state.chkStatusRT} onChange={this.handleReturnTender} />Return Tender</td>
                                </tr>
                                <tr>
                                    <td><input type='checkbox' className='chkPDQSearch' checked={this.state.chkStatusPDQ} onChange={this.handleReturnPDQ} />Card Tender</td>
                                    <td><input type='checkbox' className='chkPDQSearch' checked={this.state.chkStatusCheque} onChange={this.handleCheque} />Cheque</td>
                                </tr>
                            </table>

                            <li><input type='button' value='Search' onClick={this.detSearchBtnAct}/></li>
                        </div>
                        
                        <div className='roll_branch'>
                            <div className="cls_blk" onClick={this.clsDialog.bind(this,"roll_branch")}></div>
                                <div className='msg'></div>
                                        <li><p>Select Dates</p></li>
                                        <li><input type='text' className='branch_reports_dates' /></li>
                                        <li><input type='button' value='Ngong Road Shop' onClick={this.fetchUpBranchData.bind(this,"1")} /></li>
                                        <li><input type='button' value='Kiambu Road Shop' onClick={this.fetchUpBranchData.bind(this,"2")} /></li>
                                        <li><input type='button' value='Vetlab Shop' onClick={this.fetchUpBranchData.bind(this,"3")} /></li>
                                        <li><input type='button' value='All' onClick={this.fetchUpBranchData.bind(this,"all")} /></li>
                                        
                        </div>

                        

            </div>
        );
    }
});


let TabTransInventoryReport = React.createClass({
    componentDidMount() {
        
        $.ajax({
            url:server_url+"/get_admin_user_priviledges",
            data:{"_token": cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let resx = JSON.parse(data);

                if(resx.remote_branch_access ==0){
                    $('.up_branch').hide();
                }else{
                    $('.up_branch').show();
                }

            }
        });

        let optional_config = {
            mode: "range",
            dateFormat: "d-m-Y"
        };

        $(".datePick").flatpickr(optional_config);
        $(".detItemDatePick").flatpickr(optional_config);


        $(".branch_inventory_reports_dates").flatpickr(optional_config);
        
        $('.rprt_loader').fadeIn();

        $.ajax({
            url:server_url+"/todays_inventory_reports",
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

                this.setState({rowItem:arr});
                $('.rprt_loader').fadeOut('slow');
            }
        });

        $.ajax({
            url:server_url+"/todays_inventory_totals",
            data:{"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                console.log(data);
                let res = JSON.parse(data);
                
                this.setState({open_goods_qty:res.open_goods_qty});
                this.setState({goods_qty_recieved:res.goods_qty_recieved});
                this.setState({sc_qty:res.sc_qty});
                this.setState({curr_qty:res.curr_qty});
                this.setState({qty_goods_sold:res.qty_goods_sold});
                this.setState({cost:res.cost});
                this.setState({sales:res.sales});
                this.setState({tax:res.tax});
                this.setState({profit:res.profit});
                this.setState({rtrn:res.rtrn});
                
                
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }

        });

        

        //itemSalesReportItem
    },
    getInitialState:function(){
        return{
            rowItem:[],
            total_qty:"",
            total_tax:"",
            totals:"",
            detsItem:[],
            detsTtlQty:"",
            detsTtlTax:"",
            detsTtlDisc:"",
            detsTtl:"",
            detsChange:"",
            detsDiscount:"",
            detsCash:"",
            open_goods_qty:"",
            goods_qty_recieved:"",
            sc_qty:"",
            curr_qty:"",
            qty_goods_sold:"",
            cost:"",
            sales:"",
            tax:"",
            profit:"",
            rtrn:""

        }
    },
    getTransDets:function(i, event){
        
        $.ajax({
            url:server_url+"/get_trans_dets",
            data:{id:i,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let res = JSON.parse(data);
                let arr = [];
                   
                  
                $.each(res,function(index,value){
                    arr.push(res[index]);
                });

                this.setState({detsItem:arr});
                
                $('.roll_details').fadeIn("slow");
                
            }

        });

        $.ajax({
            url:server_url+"/get_trans_dets_totals",
            data:{id:i,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let res = JSON.parse(data);
                //'cash','change','total_tax','total'
                this.setState({detsCash:res.cash});
                this.setState({detsTtlTax:res.total_tax});
                this.setState({detsTtlQty:res.ttl_qty});
                this.setState({detsTtl:res.total});
                this.setState({detsChange:res.change});
                this.setState({detsDiscount:res.discount});
                //detsTtlDisc
                
            }

        });
        
    },
    eachDet:function(item,i){
        return(
            <tr key={i}>
                <td>{item.item}</td>
                <td>{item.qty}</td>
                <td>{item.tax}</td>
                <td>{item.price}</td>
                <td>{item.total}</td>
                            
            </tr>
        )
    },
    eachRow:function(item,i){
        return(
            <div className="inv_rprt_row" key={i}>

                <div className="inv_rprt_row_item">
                    <b>{item.item_desc}</b>
                </div>
                <div className="inv_rprt_row_item">
                    <div className="inv_rprt_row_item_col">{item.open_goods_qty}</div>
                    <div className="inv_rprt_row_item_col">{item.goods_qty_recieved}</div>
                    <div className="inv_rprt_row_item_col">{item.qty_goods_sold}</div>
                    <div className="inv_rprt_row_item_col">{item.curr_qty}</div>
                    <div className="inv_rprt_row_item_col">{item.cost}</div>
                    <div className="inv_rprt_row_item_col">{item.sales}</div>
                    <div className="inv_rprt_row_item_col">{item.rtrn}</div>
                    <div className="inv_rprt_row_item_col">{item.tax}</div>
                    <div className="inv_rprt_row_item_col">{item.profit}</div>
                </div>
                             
            </div>
        )
    },
    clsDialog:function(i,event){
        $("."+i).fadeOut('slow');
    },
    searchTrans:function(){
        let dates = $('.datePick').val();

        if(dates !=""){

            if(dates.indexOf("to")){
 
            $.ajax({
                url:server_url+"/search_todays_inventory_reports",
                data:{rec_no:"",dates:dates,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);
                    let arr = [];
                                            
                    $.each(res,function(index,value){
                        arr.push(res[index]);
                    });

                    this.setState({rowItem:arr});
                    
                }
            });

            $.ajax({
                url:server_url+"/search_todays_inventory_totals",
                data:{rec_no:"",dates:dates,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);
                    this.setState({open_goods_qty:res.open_goods_qty});
                    this.setState({goods_qty_recieved:res.goods_qty_recieved});
                    this.setState({sc_qty:res.sc_qty});
                    this.setState({curr_qty:res.curr_qty});
                    this.setState({qty_goods_sold:res.qty_goods_sold});
                    this.setState({cost:res.cost});
                    this.setState({sales:res.sales});
                    this.setState({tax:res.tax});
                    this.setState({profit:res.profit});
                    this.setState({rtrn:res.rtrn});
                    
                }
            });

        }

        }
        
    },
    selUpBranch:function(){
        $('.roll_branch').fadeIn('slow');
    },
    fetchUpBranchData: function(id){
        let dates = $('.branch_inventory_reports_dates').val();

        if(dates !=""){
            $('.rprt_loader').show();
            $.ajax({
                url:server_url+"/up_reports_check",
                data:{"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);
    
                    if(res.curr_branch != id){
                        $('.roll_branch').fadeOut('slow');
                        console.log(data);
                        //let qry = "SELECT * FROM accounts WHERE up_branch='"+id+"'";
                        let qry = "SELECT * FROM shopping_cart WHERE up_branch='"+id+"'";
                        $.ajax({
                            url:"http://www.ababuapps.com/up_pos_test/custom_reports.php",
                            data:{dates:dates,dates_col:"time",qry_str:qry,rprt:"inventory","_token":cs_token},
                            type:"POST",
                            context: this,
                            success:function(data){
                                $('.branch_acc_act_reports_dates').val('');
                                //console.log(data);
                                let res = JSON.parse(data);
                                let arr = [];
                                                        
                                $.each(res,function(index,value){
                                    arr.push(res[index]);
                                });
                
                                this.setState({rowItem:arr});
                                $('.rprt_loader').fadeOut();
                            }
                        });


                        $.ajax({
                            url:"http://www.ababuapps.com/up_pos_test/custom_reports.php",
                            data:{dates:dates,dates_col:"time",qry_str:qry,rprt:"inventory_totals","_token":cs_token},
                            type:"POST",
                            context: this,
                            success:function(data){
                                console.log(data);
                                let res = JSON.parse(data);
                                
                                this.setState({open_goods_qty:res.open_goods_qty});
                                this.setState({goods_qty_recieved:res.goods_qty_recieved});
                                this.setState({sc_qty:res.sc_qty});
                                this.setState({curr_qty:res.curr_qty});
                                this.setState({qty_goods_sold:res.qty_goods_sold});
                                this.setState({cost:res.cost});
                                this.setState({sales:res.sales});
                                this.setState({tax:res.tax});
                                this.setState({profit:res.profit});
                                this.setState({rtrn:res.rtrn});
                                
                                
                            },error: function(xhr, status, text) {
                
                                if(xhr.status ==419){
                                    window.location = server_url;
                                }
                
                            }
                
                        });
    
                    }
    
                }
            });  
        }

    },
    detailedItemsRprtSearch:function(){
        $('.roll_search').fadeIn('slow');

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

                    $(".itemSalesReportItem").easyAutocomplete(options);

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
        

    },
    detItemRprtSearchBtnAct:function(){

        let dates = $('.detItemDatePick').val();
        let item = $('.itemSalesReportItem').val();

        if(dates !="" && item !=""){

            $('.rprt_loader').fadeIn('slow');
            $('.roll_search').hide();

            $.ajax({
                url:server_url+"/dets_search_todays_inventory_reports",
                data:{item:item,dates:dates,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);
                    let arr = [];
                    $('.rprt_loader').fadeOut('slow');
                    
                                            
                    $.each(res,function(index,value){
                        arr.push(res[index]);
                    });

                    this.setState({rowItem:arr});
                    
                }
            });

            $.ajax({
                url:server_url+"/dets_search_todays_inventory_totals",
                data:{item:item,dates:dates,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);
                    this.setState({open_goods_qty:res.open_goods_qty});
                    this.setState({goods_qty_recieved:res.goods_qty_recieved});
                    this.setState({sc_qty:res.sc_qty});
                    this.setState({curr_qty:res.curr_qty});
                    this.setState({qty_goods_sold:res.qty_goods_sold});
                    this.setState({cost:res.cost});
                    this.setState({sales:res.sales});
                    this.setState({tax:res.tax});
                    this.setState({profit:res.profit});
                    this.setState({rtrn:res.rtrn});
                    
                }
            });

        }else{
            $('.msg').html("<div class='err'>Error: Value(s) Missing</div>")
        }
    },
    render:function(){
        return(
            <div className='ad_wraps'>

                <div className='rprt_top_title'>
                    <input type="text" className="datePick" placeholder="Select Dates" />
                    <div className='search_btn' onClick={this.searchTrans}></div>
                    <div className='detailed_search' onClick={this.detailedItemsRprtSearch}></div>
                    <div className='up_branch' onClick={this.selUpBranch}></div>
               </div>
                
               <div className='inv_rprt_row_head'>
                        <div className="inv_rprt_row_item_head">Opening Qty</div>
                        <div className="inv_rprt_row_item_head">Qty Received</div>
                        <div className="inv_rprt_row_item_head">Qty Sold</div>
                        <div className="inv_rprt_row_item_head">Current Stock</div>
                        <div className="inv_rprt_row_item_head">Total Cost</div>
                        <div className="inv_rprt_row_item_head">Total Sales</div>
                        <div className="inv_rprt_row_item_head">Returns</div>
                        <div className="inv_rprt_row_item_head">Total Tax</div>
                        <div className="inv_rprt_row_item_head">Total Profit</div>
               </div>
                <div className='inv_rprt_hold'>

                    
                        {
                            this.state.rowItem.map(this.eachRow)
                            }
                        
                </div>
                <div className='reports_totals_wrap'>
                    <div className='inv_rprt_row_head'>
                        <div className="inv_rprt_row_item_head">Opening Qty</div>
                        <div className="inv_rprt_row_item_head">Qty Received</div>
                        <div className="inv_rprt_row_item_head">Qty Sold</div>
                        <div className="inv_rprt_row_item_head">Current Stock</div>
                        <div className="inv_rprt_row_item_head">Total Cost</div>
                        <div className="inv_rprt_row_item_head">Total Sales</div>
                        <div className="inv_rprt_row_item_head">Returns</div>
                        <div className="inv_rprt_row_item_head">Total Tax</div>
                        <div className="inv_rprt_row_item_head">Total Profit</div>
                    </div>
                    <div className="inv_rprt_row_item">
                   
                        <div className="inv_rprt_row_item_foot">{this.state.open_goods_qty}</div>
                        <div className="inv_rprt_row_item_foot">{this.state.goods_qty_recieved}</div>
                        <div className="inv_rprt_row_item_foot">{this.state.sc_qty}</div>
                        <div className="inv_rprt_row_item_foot">{this.state.curr_qty}</div>
                        <div className="inv_rprt_row_item_foot">{this.state.cost}</div>
                        <div className="inv_rprt_row_item_foot">{this.state.sales}</div>
                        <div className="inv_rprt_row_item_foot">{this.state.rtrn}</div>
                        <div className="inv_rprt_row_item_foot">{this.state.tax}</div>
                        <div className="inv_rprt_row_item_foot">{this.state.profit}</div>
                       
                    </div>
                 </div>


                <div className='roll_details'>
                            <div className="cls" onClick={this.clsDialog.bind(this,"roll_details")}>X</div>
                            <div className='roll_details_head'>
                                
                                <b></b>
                            </div>
                            <div id="rprt_rolls_dets" className="roll_dets_cont">
                                <table>

                                    <tbody>
                                        <tr>
                                            <th>Item Description</th><th>Qty</th><th>Tax</th><th>Price</th><th>Total</th>
                                        </tr>
                                         {this.state.detsItem.map(this.eachDet)}
                                    </tbody>
                                </table>
                            </div>

                            <div id="rprt_rolls_ttls" className="roll_ttls">
                            <table>

                                <tbody>
                                        <tr>
                                        <th>Total Qty</th><th>Total Tax</th><th>Discount</th><th>Total</th><th>Cash</th><th>Change</th>
                                        </tr>
                                        <tr>
                                        <td><b>{this.state.detsTtlQty}</b></td><td><b>{this.state.detsDiscount}</b></td><td><b>{this.state.detsTtlTax}</b></td><td><b>{this.state.detsTtl}</b></td><td><b>{this.state.detsCash}</b></td><td><b>{this.state.detsChange}</b></td>
                                        </tr>
                                    </tbody>
                            </table>
                            </div>

                        </div>

                        <div className='roll_search'>
                            <div className="cls_blk" onClick={this.clsDialog.bind(this,"roll_search")}></div>
                            <div className='msg'></div>
                            <li>Dates</li>
                            <li><input className='detItemDatePick' type='text' /></li>
                            <li>Item</li>
                            <li><input className='itemSalesReportItem' type='text' /></li>
                            <li><input type='button' value='Search' onClick={this.detItemRprtSearchBtnAct}/></li>
                        </div>

                        <div className='roll_branch'>
                            <div className="cls_blk" onClick={this.clsDialog.bind(this,"roll_branch")}></div>
                                <div className='msg'></div>
                                        <li><p>Select Dates</p></li>
                                        <li><input type='text' className='branch_inventory_reports_dates' /></li>
                                        <li><input type='button' value='Ngong Road Shop' onClick={this.fetchUpBranchData.bind(this,"1")} /></li>
                                        <li><input type='button' value='Kiambu Road Shop' onClick={this.fetchUpBranchData.bind(this,"2")} /></li>
                                        <li><input type='button' value='Vetlab Shop' onClick={this.fetchUpBranchData.bind(this,"3")} /></li>
                        </div>

                        <div className='rprt_loader'><b>Loading...</b></div>
                        
            </div>
        );
    }
});

let TabTransDrawer = React.createClass({
    componentDidMount() {

        $(".dr_op_txt").numeric();
        $(".dr_op_txt").numeric(false, function() { alert("Integers only"); this.value = ""; this.focus(); });
        $(".dr_op_txt").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
        $(".dr_op_txt").numeric({ decimal: false, negative: false }, function() { alert("Positive integers only"); this.value = ""; this.focus(); });

        $(".closingAmt").numeric();
        $(".closingAmt").numeric(false, function() { alert("Integers only"); this.value = ""; this.focus(); });
        $(".closingAmt").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
        $(".closingAmt").numeric({ decimal: false, negative: false }, function() { alert("Positive integers only"); this.value = ""; this.focus(); });

        this.loadReport();

        let optional_config = {
            mode: "range",
            dateFormat: "d-m-Y"
        };

        $(".dr_dates_txt").flatpickr(optional_config);
        

    },
    loadReport(){

        $.ajax({
            url:server_url+"/drawer_report",
            data:{"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
               // console.log(data);
                let res = JSON.parse(data);
                let arr = [];
                                        
                $.each(res,function(index,value){
                    arr.push(res[index]);
                });

                this.setState({rowItem:arr});
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });
    },
    searchDrawerReports:function(){
        let dates = $('.dr_dates_txt').val();

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

                this.setState({rowItem:arr});
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

    },
    getInitialState:function(){
        return{
            rowItem:[],
            closeDrawerVal:""
        }
    },
    btnCloseDrawer:function(i,event){
       // console.log(i);
        this.setState({closeDrawerVal:i});
        $('.closingAmt').focus();
        $('.close_drawer_conf').fadeIn('slow');
    },  
    eachRow:function(item,i){
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
                        this.loadReport();
                    }
                }
            });
        }
        
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
                        this.loadReport();
                    }else{
                        $('.drawer_msg').html("<div class='err'>Error: Drawer still open!</div>");
                    }
                }
            });
        }else{
            $('.drawer_msg').html("<div class='err'>Error: Value(s) Missing</div>");
        }
    },
    clsDialog:function(i,event){
        $("."+i).fadeOut('slow');
    },
    render:function(){
        return(
            <div className='ad_wraps'>
                <div className='drawer_form'>

                    

                    <table>
                        <tbody>
                            <tr>
                                <td><div className='drawer_msg'></div></td>
                            <td><b>Opening Amount</b></td>
                            <td><input type='text' className='dr_op_txt' /></td>
                            <td><input type='button' value='Save' onClick={this.openDrawer} className='btn_save_drawer'/></td>
                            </tr>
                        </tbody>
                    </table>

                    <table>
                        <tbody>
                            <tr>
                                <td><input type='text' placeholder="Select Dates" className='dr_dates_txt' /></td>
                                <td>
                                    <div className='search_btn' onClick={this.searchDrawerReports}></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    
                </div>
                
                <div id="rprt_drawer_table" className='reports_table_wrap'>
                    <table>
                        <thead>
                            <tr><th>Opening Date</th><th>Opening Amount</th><th>Closing Date</th><th>Closing Amount</th><th>Status</th><th>User</th><th></th></tr>
                        </thead>
                        <tbody>
                            {
                                this.state.rowItem.map(this.eachRow)
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
        );
    }
});


let TabTransDrawings = React.createClass({
    componentDidMount() {

        $(".dr_op_txt").numeric();
        $(".dr_op_txt").numeric(false, function() { alert("Integers only"); this.value = ""; this.focus(); });
        $(".dr_op_txt").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
        $(".dr_op_txt").numeric({ decimal: false, negative: false }, function() { alert("Positive integers only"); this.value = ""; this.focus(); });

        $(".closingAmt").numeric();
        $(".closingAmt").numeric(false, function() { alert("Integers only"); this.value = ""; this.focus(); });
        $(".closingAmt").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
        $(".closingAmt").numeric({ decimal: false, negative: false }, function() { alert("Positive integers only"); this.value = ""; this.focus(); });

        this.loadReport();

    },
    loadReport(){

        $.ajax({
            url:server_url+"/drawings_report",
            data:{"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
               // console.log(data);
                let res = JSON.parse(data);
                let arr = [];
                                        
                $.each(res,function(index,value){
                    arr.push(res[index]);
                });

                this.setState({rowItem:arr});
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

        $.ajax({
            url:server_url+"/amt_drawings_left",
            data:{"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                console.log(data);
                let res = JSON.parse(data);
                
                this.setState({br_curr_dr:res.br_curr_dr});
                
                

                
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });


        let qry = "";
       
            $.ajax({
                url:"http://www.ababuapps.com/up_pos_test/custom_reports.php",
                data:{id:"",rprt:"drawings_left",qry_str:qry,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    console.log(data);
                    let res = JSON.parse(data);
                    
                    this.setState({br_1_dr:res.tr_1});
                    this.setState({br_2_dr:res.tr_2});
                    this.setState({br_3_dr:res.tr_3});
                    
                    
                }
    
            });

    },
    getInitialState:function(){
        return{
            rowItem:[],
            closeDrawerVal:"",
            br_1_dr:"",
            br_2_dr:"",
            br_3_dr:"",
            br_curr_dr:""
        }
    },
    btnCloseDrawer:function(i,event){
       // console.log(i);
        this.setState({closeDrawerVal:i});

        $('.close_drawer_conf').fadeIn('slow');
        $('.closingAmt').focus();
    },  
    eachRow:function(item,i){
        
            return(
                <tr key={i}>
    
                     <td>{item.dr_time}</td>
                     <td>{item.sales_amt}</td>
                     <td>{item.amount}</td>
                     <td>{item.remainder_amt}</td>
                     <td>{item.user}</td>
                     <td>{item.branch}</td>
                    <td>{item.comment}</td>
                    
                    <td></td>
                                 
                </tr>
            )
       
    },
    clsDialog:function(i,event){
        $("."+i).fadeOut('slow');
    },
    render:function(){
        return(
            <div className='ad_wraps'>
                <div className='rprt_top_title'>
                    <div className="drawings_left_tbl">
                    <table>
                        <thead>
                            <tr>
                                   
                                <th>Uncollected</th><th>Ngong Road Shop</th><th>Kiambu Road Shop</th><th>Vetlab Shop</th>
                                 
                                    
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{this.state.br_curr_dr}</td><td>{this.state.br_1_dr}</td><td>{this.state.br_2_dr}</td><td>{this.state.br_3_dr}</td>
                                
                               
                            </tr>
                        </tbody>
                    </table>
                    </div>
                </div>
                
                <div id="rprt_drawer_table" className='reports_table_wrap'>
                    <table>
                        <thead>
                            <tr><th>Date</th><th>Sales</th><th>Amount Drawn</th><th>Remainder</th><th>User</th><th>Branch</th><th>Comment</th></tr>
                        </thead>
                        <tbody>
                            {
                                this.state.rowItem.map(this.eachRow)
                            }
                        </tbody>
                        
                        
                    </table>

                    
                </div>
            </div>
        );
    }
});

let TabItemUploads = React.createClass({
    
    
    upload_items_xls:function(){

        var formData = new FormData($('#upload_form')[0]);
        formData.append('items_file', $('input[type=file]')[0].files[0]);
        
        if($('input[type=file]')[0].files[0]){
            $.ajax({
                url:server_url+"/upload_items_xls",
                data:formData,
                type:"POST",
                context: this,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN':cs_token
                },
                xhr: function () {
                    $('.msg').html("<div class='loading'>Loading.....</div>");
                    var xhr = new XMLHttpRequest();
    
                    xhr.upload.onprogress = function (e) {
                      var percent = '0';
                      var percentage = '0%';
                      
                      if (e.lengthComputable) {
                        
                        percent = Math.round((e.loaded / e.total) * 100);
                        percentage = percent + '%';
                        //$progressBar.width(percentage).attr('aria-valuenow', percent).text(percentage);
                        //console.log(percentage);
                        }
                    };
    
                    return xhr;
                  },
                success:function(data){
                    console.log(data);
                    let res = JSON.parse(data);
                    
                    if(res.status ==1){
                        $('.msg').html("<div class='info'>Success: Upload Complete</div>");
                    }else if(res.status ==0){
                        $('.msg').html("<div class='err'>Error: Incorrect Content Format</div>");
                    }else if(res.status ==-1){
                        $('.msg').html("<div class='err'>Error: File not .xls Format</div>");
                    }
                    
                    
                },error: function() {
                    console.log("Error");
                },complete: function () {
                    //$progress.hide();
                }
            });
        }else{
            $('.msg').html("<div class='err'>Error: Select File</div>");
        }
        

        
       
    },
    render:function(){
        return(

            <div className='ad_wraps'>
            
                <div className="admin_top">
                        <div className='msg'>

                        </div>
                      
                    </div>
                 
                    
                   
                  
                    <div className='form'>
                        <table>
                            <tbody>
                               
                                
                                <tr>
                                    <td><label>Upload Items</label></td>
                                </tr>

                                <tr>
                                    <td><label></label></td>
                                </tr>
                                
                                    <form id="upload_form" enctype="multipart/form-data">
                                        <tr>
                                           
                                            <td><input type='file' id='file_items'  name='file_items' /></td>
                                        </tr>
                                        
                                    </form>
                                <tr>
                                    <td><input type='button' onClick={this.upload_items_xls} value='Upload' /></td>
                                </tr>
                                
                            </tbody>
                        </table>
                </div>

                <div className='form'>
                        <table>
                            <tbody>
                                
                                <tr>
                                <td><label></label></td>
                                </tr>
                                <tr>
                                    <td><a href={server_url+'/download_items_xls'}>Download Items Template</a></td>
                                </tr>
                            </tbody>
                        </table>
                </div>
                         
            </div>
        )
    }
});

let TabNewItem = React.createClass({
    componentDidMount() {
        $.ajax({
            url:server_url+"/get_tax_options",
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

                this.setState({optItem:arr});
            
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

        $.ajax({
            url:server_url+"/get_catg_options",
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

                this.setState({optCatgItem:arr});
            
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

        /*
        $.ajax({
            url:server_url+"/get_sub_catg_options",
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

                this.setState({optSubCatgItem:arr});
            
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });
        */

        $(".txt_qty").numeric();
        $(".txt_qty").numeric(false, function() { alert("Integers only"); this.value = ""; this.focus(); });
        $(".txt_qty").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
        $(".txt_qty").numeric({ decimal: false, negative: false }, function() { alert("Positive integers only"); this.value = ""; this.focus(); });
       
        $(".txt_price").numeric();
        $(".txt_price").numeric(false, function() { alert("Integers only"); this.value = ""; this.focus(); });
        $(".txt_price").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
        $(".txt_price").numeric({ decimal: false, negative: false }, function() { alert("Positive integers only"); this.value = ""; this.focus(); });
       
        $(".txt_buying_price").numeric();
        $(".txt_buying_price").numeric(false, function() { alert("Integers only"); this.value = ""; this.focus(); });
        $(".txt_buying_price").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
        $(".txt_buying_price").numeric({ decimal: false, negative: false }, function() { alert("Positive integers only"); this.value = ""; this.focus(); });
        
        $(".txt_ceil_price").numeric();
        $(".txt_ceil_price").numeric(false, function() { alert("Integers only"); this.value = ""; this.focus(); });
        $(".txt_ceil_price").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
        $(".txt_ceil_price").numeric({ decimal: false, negative: false }, function() { alert("Positive integers only"); this.value = ""; this.focus(); });
        
        $(".txt_floor_price").numeric();
        $(".txt_floor_price").numeric(false, function() { alert("Integers only"); this.value = ""; this.focus(); });
        $(".txt_floor_price").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
        $(".txt_floor_price").numeric({ decimal: false, negative: false }, function() { alert("Positive integers only"); this.value = ""; this.focus(); });
       

    },
    getInitialState:function(){
        return{
            optItem:[],
            optCatgItem:[],
            optSubCatgItem:[],
            chosenCatg:""
        }
    },
    catgOption:function(item,i){
        return(
            
                <option key={i} value={item.id}>{item.catg_name}</option>
                
        )
    },
    catgSubOption:function(item,i){
        return(
            
                <option key={i} value={item.id}>{item.sub}</option>
                
        )
    },
    new_item: function(){
         let code = $('.txt_code').val();
         let m_code = $('.txt_m_code').val();
         let title = $('.txt_new_item_title').val();
         let re_order_level = $('.txt_re_order_level').val();
         let tax = $('.sel_tax').val();
         let sub_catg = $('.new_item_sub_catg_val').val();
         let catg = $('.new_item_catg_val').val();
         let price = $('.txt_price').val();
         let buy_price = $('.txt_buying_price').val();
         let ceil_price = $('.txt_ceil_price').val();
         let floor_price = $('.txt_floor_price').val();
         
         if(title !="" && price !="" && buy_price !="" && floor_price !="" && ceil_price !="" &&  catg !="" && title !="" && re_order_level !="" && tax !=""){

                $.ajax({
                    url:server_url+"/new_item",
                    data:{ceil_price:ceil_price,floor_price:floor_price,buy_price:buy_price,price:price,catg:catg,sub_catg:sub_catg,m_code:m_code,code:code,title:title,re_order_level:re_order_level,tax:tax,"_token":cs_token},
                    type:"POST",
                    context: this,
                    success:function(data){
                        //console.log(data);
                        let res = JSON.parse(data);
                        if(res.status==1){
                            $('.txt_code').val('');
                            $('.txt_new_item_title').val('');
                            $('.txt_m_code').val('');
                            $('.txt_re_order_level').val('');
                            $('.sel_tax').val('');
                            $('.new_item_sub_catg_val').val('');
                            $('.new_item_catg_val').val('');
                            $('.txt_price').val('');
                            $('.txt_buying_price').val('');
                            $('.txt_ceil_price').val('');
                            $('.txt_floor_price').val('');
                            $('.msg').html(`<div class='info'>Success: ${title} Saved</div>`);
                        
                        }else{
                            $('.msg').html(`<div class='err'>Error: ${title} Already Exists</div>`);
                        }
                    },error: function(xhr, status, text) {
    
                        if(xhr.status ==419){
                            window.location = server_url;
                        }
    
                    }
                 });
            
         }else{

            $('.msg').html(`<div class='err'>Error: Value(s) Missing</div>`);

         }
    },
    taxOption:function(item,i){
        let percConv = parseFloat(item.perc) * 100;
        return(
            
                <option key={i} value={item.id}>{item.tax_desc +' - '+ percConv + ' %'}</option>
                
        )
    },
    getSubCatg:function(){

        let catg = $('.new_item_catg_val').val();

        if(catg !=""){

            $.ajax({
                url:server_url+"/get_sub_catg_options",
                data:{catg:catg,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);
                    let arr = [];
                                            
                    $.each(res,function(index,value){
                        arr.push(res[index]);
                    });
    
                    this.setState({optSubCatgItem:arr});
                
                },error: function(xhr, status, text) {
    
                    if(xhr.status ==419){
                        window.location = server_url;
                    }
    
                }
            });

        }
    },
    render:function(){
        return(
            
                <div className='ad_wraps'>
                    <div className="admin_top">
                        <div className='msg'>

                        </div>
                         
        
                    </div>
                    
                   
                    <div className='form'>

                    <table>
                        <tbody>
                            <tr>
                                <td><label>Code</label></td>
                            </tr>
                            <tr>
                                <td><input type='text' className='txt_code'/></td>
                            </tr>
                            <tr>
                                <td><label>Manufacture's Code</label></td>
                            </tr>
                            <tr>
                                <td><input type='text' className='txt_m_code'/></td>
                            </tr>
                            <tr>
                                <td><label>Item Title</label></td>
                            </tr>
                            <tr>
                                <td><input type='text' className='txt_new_item_title' /></td>
                            </tr>
                            <tr>
                                <td><label>Cost</label></td>
                            </tr>
                            <tr>
                                <td><input type='text' className='txt_buying_price'  /></td>
                            </tr>
                            <tr>
                                <td><label>Price</label></td>
                            </tr>
                            <tr>
                                <td><input type='text' className='txt_price'  /></td>
                            </tr>

                            <tr>
                                <td><label>Ceiling Price</label></td>
                                </tr>
                                <tr>
                                    <td><input type='text' className='txt_ceil_price'  /></td>
                            </tr>
                            
                            
                        </tbody>
                    </table>
                        
                    </div>

                    <div className='form'>

                            <table>
                                <tbody>

                                    <tr>
                                        <td><label>Floor Price</label></td>
                                    </tr>
                                    <tr>
                                        <td><input type='text' className='txt_floor_price'  /></td>
                                    </tr>

                                    <tr>
                                        <td><label>Re-Order Level</label></td>
                                    </tr>
                                    <tr>
                                        <td><input type='text' className='txt_re_order_level' /></td>
                                    </tr>
                                    
                                    <tr>
                                        <td><label>Tax</label></td>
                                    </tr>
                                    <tr>
                                        <td><select className='sel_tax'>
                                            <option></option>
                                            {this.state.optItem.map(this.taxOption)}

                                        </select></td>
                                    </tr>

                                    <tr>
                                        <td><label>Category</label></td>
                                    </tr>
                                    <tr>
                                        <td><select className='new_item_catg_val' onChange={this.getSubCatg}>
                                            <option></option>
                                            {this.state.optCatgItem.map(this.catgOption)}

                                        </select></td>
                                    </tr>

                                    <tr>
                                        <td><label>Sub Category</label></td>
                                    </tr>
                                    <tr>
                                        <td><select className='new_item_sub_catg_val'>
                                            <option></option>
                                            {this.state.optSubCatgItem.map(this.catgSubOption)}

                                        </select></td>
                                    </tr>
                                    
                                    <tr>
                                        <td><input type='button' className='btn_new_item' onClick={this.new_item} value='Save' /></td>
                                    </tr>
                                    
                                </tbody>
                            </table>

                    </div>
                </div>
                        
        );
    }
});

let TabNewItemCatg = React.createClass({
    componentDidMount() {
        $.ajax({
            url:server_url+"/get_category_rprt_data",
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

                this.setState({rowItem:arr});
                
            }
        });
    },
    eachRow:function(item,i){
        return(
            <tr key={i}>
                <td>{item.catg_name}</td>          
            </tr>
        )
    },
    newItemCatg:function(){
        let catg = $('.txt_new_catg').val();
        
        $.ajax({
            url:server_url+"/new_item_catg",
            data:{catg:catg,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let res = JSON.parse(data);
                if(res.status ==1){
                    
                    $('.msg').html(`<div class='info'>Success: ${catg} Saved</div>`);
                    $('.txt_new_catg').val('');

                    $.ajax({
                        url:server_url+"/get_category_rprt_data",
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
            
                            this.setState({rowItem:arr});
                            
                        }
                    });

                }else{
                    $('.msg').html(`<div class='err'> ${catg} Already exists</div>`);
                    
                }
            
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

    },
    getInitialState:function(){
        return{
            rowItem:[]
        }
    },
    render:function(){
        return(
            <div className='ad_wraps'>
                <div className='msg'>
                    
                </div>

                <div className='form'>
                    <table>
                        <tbody>
                            <tr>
                                <td><label>New Category</label></td>
                            </tr>
                            <tr>
                                <td><input type='text' className='txt_new_catg' /></td>
                            </tr>
                            <tr>
                                <td><input type='button' onClick={this.newItemCatg} value='Save' /></td>
                            </tr>
                        </tbody>
                    </table>
                </div>   
                <div className='form' id='catgRprtTbl'>
                    <table>
                        <thead>
                            <tr>
                                <th>Category</th>
                            </tr>
                        </thead>
                        <tbody>
                            {
                                this.state.rowItem.map(this.eachRow)
                            }
                        </tbody>
                    </table>
                </div>   
            </div>

            
                         
        );
    }
});

let Branches = React.createClass({
    componentDidMount() {

        this.setState({tabVal:"new_branch"});
    },
    getInitialState:function(){
        return{
           tabVal:""
        }
    },
    tabs:function(i,events){
        this.setState({tabVal:i});
        
    },
    render:function(){

        let tabCont;
        if(this.state.tabVal =="new_branch"){
            tabCont = <TabNewBranch/>
        }
        return(
            <div>
                <div className="admin_top">
                  
        
                </div>
                <div className="dashboard">
                        
                        <div>{tabCont}</div>
                        
                </div>
            </div>
        );
    }
});


let TabNewBranch = React.createClass({
    componentDidMount() {
        $.ajax({
            url:server_url+"/get_branch_rprt_data",
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

                this.setState({rowItem:arr});
                
            }
        });

        $.ajax({
            url:server_url+"/get_active_branch_rprt_data",
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

                this.setState({optItem:arr});
                
            }
        });

        $.ajax({
            url:server_url+"/get_current_branch",
            data:{"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                console.log(data);
               
                let res = JSON.parse(data);
                this.setState({currBranchVal:res.branch});
            }
        });

    },
    eachRow:function(item,i){
        return(
            <tr key={i}>
                <td>{item.branch}</td>          
                <td>{item.status}</td>          
            </tr>
        )
    },
    eachOption:function(item,i){
        return(
            <option key={i} value={item.id}>{item.branch}</option>
        )
    },
    newBranch:function(){
        let branch = $('.txt_new_branch').val();

        if(branch !=""){

            $.ajax({
                url:server_url+"/new_branch",
                data:{branch:branch,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    console.log(data);
                    let res = JSON.parse(data);
                    if(res.status ==1){
                        
                        $('.msg').html(`<div class='info'>Success: ${branch} Saved</div>`);
                        $('.txt_new_branch').val('');
    
                        $.ajax({
                            url:server_url+"/get_branch_rprt_data",
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
                
                                this.setState({rowItem:arr});

                                
                            }
                        });
    
                    }else{
                        $('.msg').html(`<div class='err'> ${catg} Already exists</div>`);
                        
                    }
                
                },error: function(xhr, status, text) {
    
                    if(xhr.status ==419){
                        window.location = server_url;
                    }
    
                }
            });

        }else{
            $('.msg').html(`<div class='err'> Value(s) Missing!</div>`);
                        
        }
        
        

    },
    getInitialState:function(){
        return{
            rowItem:[],
            optItem:[],
            currBranchVal:""
        }
    },
    setCurrentBranch:function(){
        let branch = $('.selCurrBranch').val();

        if(branch !=""){
            $.ajax({
                url:server_url+"/sel_curr_branch",
                data:{branch:branch,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                   
                    let res = JSON.parse(data);
                    this.setState({currBranchVal:res.branch});
                    if(res.status ==1){
                        $('.msg').html(`<div class='info'>Success: ${res.branch} set as current Branch</div>`);
                    }
                    
                }
            });
        }else{
            $('.msg').html("<div class='err'>Error: Branch not selected</div>");
        }
        
    },
    render:function(){
        return(
          
                <div className='ad_wraps'>
                    <div className='msg'>
                        
                    </div>

                    <div className='form'>
                        <table>
                            <tbody>
                                <tr>
                                    <td><label>New Branch</label></td>
                                </tr>
                                <tr>
                                    <td><input type='text' className='txt_new_branch' /></td>
                                </tr>
                                <tr>
                                    <td><input type='button' onClick={this.newBranch} value='Save' /></td>
                                </tr>
                                <tr><td></td></tr>
                                <tr><td></td></tr>
                                <tr><td></td></tr>
                                <tr><td></td></tr>
                                <tr><td></td></tr>
                                <tr><td></td></tr>
                                <tr><td></td></tr>
                                <tr><td><b>Current Branch: {this.state.currBranchVal}</b></td></tr>
                                <tr><td>
                                    <select className='selCurrBranch'>
                                        <option></option>
                                        {
                                                this.state.optItem.map(this.eachOption)
                                        }
                                    </select>    
                                </td></tr>
                                <tr><td><input type='button' onClick={this.setCurrentBranch} value='Set Branch' /></td></tr>
                            </tbody>
                        </table>
                    </div>   
                    <div className='form' id='branchRprtTbl'>
                        <table>
                            <thead>
                                <tr>
                                    <th>Branch</th><th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                {
                                    this.state.rowItem.map(this.eachRow)
                                }
                            </tbody>
                        </table>
                    </div>   
                </div>
          
        );
    }
});


let TabNewItemSubCatg = React.createClass({
    componentDidMount() {

        $.ajax({
            url:server_url+"/get_catg_options",
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

                this.setState({optItem:arr});
            
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

        $.ajax({
            url:server_url+"/get_sub_category_rprt_data",
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

                this.setState({rowItem:arr});
                
            }
        });

    },
    catgOption:function(item,i){
        return(
            
                <option key={i} value={item.id}>{item.catg_name}</option>
                
        )
    },
    eachRow:function(item,i){
        return(
            <tr key={i}>
                <td>{item.sub}</td>
                <td>{item.catg}</td>         
            </tr>
        )
    },
    newItemCatg:function(){
        let catg = $('.new_catg_val').val();
        let sub_catg = $('.txt_new_sub_catg').val();
        
        if(catg !="" && sub_catg !=""){
            $.ajax({
                url:server_url+"/new_item_sub_catg",
                data:{catg:catg,sub_catg:sub_catg,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);
                    if(res.status ==1){
    
                        $('.msg').html(`<div class='info'>Success: ${sub_catg} Saved</div>`);
                        $('.txt_new_sub_catg').val('');

                        $.ajax({
                            url:server_url+"/get_sub_category_rprt_data",
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
                
                                this.setState({rowItem:arr});
                                
                            }
                        });

                    }else{
                        $('.msg').html(`<div class='err'> ${sub_catg} Already exists</div>`);
                        
                    }
                
                },error: function(xhr, status, text) {
    
                    if(xhr.status ==419){
                        window.location = server_url;
                    }
    
                }
            });
        }else{
            $('.msg').html(`<div class='err'> Value(s) Missing</div>`);
                        
        }
        

    },
    getInitialState:function(){
        return{
            optItem:[],
            rowItem:[]
        }
    },
    render:function(){
        return(
            <div className='ad_wraps'>
                <div className='msg'>

                </div>

                <div className='form'>
                    <table>
                        <tbody>
                            <tr>
                                <td><label>New Category</label></td>
                            </tr>
                            <tr>
                                <td><select className='new_catg_val'>
                                    <option></option>
                                    {this.state.optItem.map(this.catgOption)}

                                </select></td>
                            </tr>
                            <tr>
                                <td><label>New Sub Category</label></td>
                            </tr>
                            <tr>
                                <td><input type='text' className='txt_new_sub_catg' /></td>
                            </tr>
                            <tr>
                                <td><input type='button' onClick={this.newItemCatg} value='Save' /></td>
                            </tr>
                        </tbody>
                    </table>
                </div>  

                <div className='form' id='subCatgRprtTbl'>
                <table>
                        <thead>
                            <tr>
                                <th>Sub Category</th><th>Category</th>
                            </tr>
                        </thead>
                        <tbody>
                            {
                                this.state.rowItem.map(this.eachRow)
                            }
                        </tbody>
                    </table>
                </div>    
            </div>
                         
        );
    }
});


let TabItemCatg = React.createClass({
    componentDidMount() {

        $.ajax({
            url:server_url+"/admin_catg_report",
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
                
                this.setState({rowItem:arr});
                
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

    },
    eachRow:function(item,i){
        return(
            <tr key={i}>

                <td>{item.no}</td>
                <td>{item.catg_name}</td>
                <td>{item.status}</td>
                         
            </tr>
        )   
    },
    getInitialState:function(){
        return{
            rowItem:[]
        }
    },
    render:function(){
        return(
            <div className='ad_wraps'>

                <div className='reports_table_wrap'>
                        
                    <table>
                        <thead>
                        <tr>
                            <th>No.</th><th>Category Name</th><th>Status</th>
                        </tr>
                        </thead>

                        <tbody id='tbl_admin_items_rprt'>
                            {
                                this.state.rowItem.map(this.eachRow)
                            }
                        </tbody>
                    </table>
                </div>
        </div>
                         
        );
    }
});


let TabItemReports = React.createClass({
    componentDidMount() {

        $.ajax({
            url:server_url+"/get_admin_user_priviledges",
            data:{"_token": cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let resx = JSON.parse(data);

                if(resx.remote_branch_access ==0){
                    $('.up_branch').hide();
                }else{
                    $('.up_branch').show();
                }

            }
        });

        $('.rprt_loader').show();
        $.ajax({
            url:server_url+"/admin_items_report",
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

                this.setState({rowItem:arr});
                $('.rprt_loader').fadeOut('slow');
                
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

        $.ajax({
            url:server_url+"/get_tax_options",
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

                this.setState({optItem:arr});
            
            }
        });

        $.ajax({
            url:server_url+"/get_catg_options",
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

                this.setState({catgItem:arr});
            
            }
        });

        $.ajax({
            url:server_url+"/get_all_sub_catg_options",
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

                this.setState({subCatgItem:arr});
            
            }
        });

       

    },
    disableItem:function(i,event){
        $('.rprt_loader').fadeIn('slow');
        $.ajax({
            url:server_url+"/disable_item",
            data:{id:i,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                
                $.ajax({
                    url:server_url+"/admin_items_report",
                    data:{"_token":cs_token},
                    type:"POST",
                    context: this,
                    success:function(data){
                        //console.log(data);
                        $('.rprt_loader').fadeOut('slow');
                        let res = JSON.parse(data);
                        let arr = [];
                                                
                        $.each(res,function(index,value){
                            arr.push(res[index]);
                        });
        
                        this.setState({rowItem:arr});
                        
                    },error: function(xhr, status, text) {
        
                        if(xhr.status ==419){
                            window.location = server_url;
                        }
        
                    }
                });

            }
        });
    },
    enableItem:function(i,event){
        $('.rprt_loader').fadeIn('slow');
        $.ajax({
            url:server_url+"/enable_item",
            data:{id:i,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                
                $.ajax({
                    url:server_url+"/admin_items_report",
                    data:{"_token":cs_token},
                    type:"POST",
                    context: this,
                    success:function(data){
                        //console.log(data);
                        $('.rprt_loader').fadeOut('slow');
                        let res = JSON.parse(data);
                        let arr = [];
                                                
                        $.each(res,function(index,value){
                            arr.push(res[index]);
                        });
        
                        this.setState({rowItem:arr});
                        
                    },error: function(xhr, status, text) {
        
                        if(xhr.status ==419){
                            window.location = server_url;
                        }
        
                    }
                });

            }
        });
    },
    eachRow:function(item,i){
        if(item.up_id =="" || item.up_id ==null){
            if(item.status =='Active'){
                return(
                    <tr key={i}>
        
                        <td>{item.code_no}</td>
                        <td>{item.item_desc}</td>
                        <td>{item.sell_price}</td>
                        <td>{item.qty}</td>
                        <td>{item.tax}</td>
                        <td>{item.status}</td>
                        <td><a href='#' onClick={this.onLoadEditWindow.bind(this,item.id)} className='rprt_link'>Edit</a></td>
                        <td><a href='#' onClick={this.onLoadDetailsWindow.bind(this,item.id)} className='rprt_link'>Details</a></td>
                        <td><a href='#' onClick={this.disableItem.bind(this,item.id)} className='del_rprt_link'>Disable</a></td>         
                    </tr>
                ) 
            }else{
                return(
                    <tr key={i}>
        
                        <td>{item.code_no}</td>
                        <td>{item.item_desc}</td>
                        <td>{item.sell_price}</td>
                        <td>{item.qty}</td>
                        <td>{item.tax}</td>
                        <td>{item.status}</td>
                        <td><a href='#' onClick={this.onLoadEditWindow.bind(this,item.id)} className='rprt_link'>Edit</a></td>
                        <td><a href='#' onClick={this.onLoadDetailsWindow.bind(this,item.id)} className='rprt_link'>Details</a></td>
                        <td><a href='#' onClick={this.enableItem.bind(this,item.id)} className='del_rprt_link'>Enable</a></td>         
                    </tr>
                ) 
            }
        }else{
            return(
                <tr key={i}>
    
                    <td>{item.code_no}</td>
                    <td>{item.item_desc}</td>
                    <td>{item.sell_price}</td>
                    <td>{item.qty}</td>
                    <td>{item.tax}</td>
                    <td>{item.status}</td>
                    <td></td>
                    <td></td>
                    <td></td>         
                </tr>
            ) 
        }
        
          
    },
    eachDetRow:function(item,i){
        return(
            <tr key={i}>
                <td>{item.date_received}</td>
                <td>{item.item}</td>
                <td>{item.received_by}</td>
                <td>{item.receipt_no}</td>
                <td>{item.qty}</td>
                <td>{item.cost}</td>
                <td>{item.price}</td>
                <td>{item.ceil_price}</td>
                <td>{item.floor_price}</td>
            </tr>
        ) 
    },
    onLoadDetailsWindow:function(i,event){

        $('.items_details_window').show('slow');
        
        $.ajax({
            url:server_url+"/get_single_item_goods",
            data:{id:i,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                
                let res = JSON.parse(data);
                let arr = [];
                                        
                $.each(res,function(index,value){
                    arr.push(res[index]);
                });

                this.setState({detsItem:arr});

            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

        $.ajax({
            url:server_url+"/get_single_item",
            data:{id:i,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                console.log(data);
                
                let res = JSON.parse(data);

               
                this.setState({detsCost:res.buy_price});
                this.setState({detsItemDesc:res.item_desc});
                this.setState({detsPrice:res.sell_price});
                this.setState({detsFloorPrice:res.floor_price});
                this.setState({detsCeilPrice:res.ceil_price});
                this.setState({detsCode:res.look_up});
                this.setState({detsManCode:res.code_no});

            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });
    },
    onLoadEditWindow:function(i,event){
        
        $.ajax({
            url:server_url+"/get_single_item",
            data:{id:i,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                
                let res = JSON.parse(data);
               
                //res.reorder_level.replace(",","")
                $('.txt_edit_item_code').val(res.code_no);
                $('.txt_edit_manf_code').val(res.look_up);
                $('.txt_edit_item_title').val(res.item_desc);
                
                $('.txt_edit_price').val(res.sell_price.replace(",",""));
                $('.txt_edit_cost').val(res.buy_price.replace(",",""));
                
                $('.txt_edit_ceil_price').val(res.ceil_price.replace(",",""));
                $('.txt_edit_floor_price').val(res.floor_price.replace(",",""));
                
                $('.txt_edit_re_order_level').val(res.reorder_level);
                $('.txt_edit_qty').val(res.qty);
                //alert("jkhkhjkhkjhjkhkhkjhkjhkjhkjhjk");
                $('.sel_edit_tax').val(res.tax);
                
                this.setState({upCatg:res.catg});
                this.setState({upSubCatg:res.sub_catg});
                this.setState({upId:res.id});
                
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

        $(".edit_window").fadeIn('slow');
    },
    clsDialog:function(i,event){

        $.ajax({
            url:server_url+"/admin_items_report",
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

                this.setState({rowItem:arr});
                
            }
        });

        $("."+i).fadeOut('slow');
        $('.msg').empty();

    },
    subCatgOption:function(item,i){

        let item_sub_catg = this.state.upSubCatg;

        if(item_sub_catg == item.id){
            return(
         
                <option key={i} value={item.id} selected>{item.sub}</option>
            )
        }else{
            return(
         
                <option key={i} value={item.id}>{item.sub}</option>
            )
        }
       
    },
    catgOption:function(item,i){

        let item_catg = this.state.upCatg;
        if(item_catg == item.id){
            return(
         
                <option key={i} value={item.id} selected>{item.catg_name}</option>
            )
        }else{
            return(
         
                <option key={i} value={item.id}>{item.catg_name}</option>
            )
        }
       
    },
    taxOption:function(item,i){

        let item_tax = this.state.upTax;

        if(item_tax == item.id){
            return(
         
                <option key={i} value={item.id} selected>{item.tax_desc}</option>
            )
        }else{
            return(
         
                <option key={i} value={item.id}>{item.tax_desc}</option>
            )
        }
       
    },
    btnEditItem:function(){
        let code = $('.txt_edit_item_code').val();
        let m_code = $('.txt_edit_manf_code').val();
        let title = $('.txt_edit_item_title').val();
        let price = $('.txt_edit_price').val();
        let cost = $('.txt_edit_cost').val();
        let ceil_price = $('.txt_edit_ceil_price').val();
        let floor_price = $('.txt_edit_floor_price').val();
        let re_order = $('.txt_edit_re_order_level').val();
        let category = $('.sel_edit_category').val();
        let sub_category = $('.sel_edit_sub_category').val();
        let tax = $('.sel_edit_tax').val();

        if(tax !="" && re_order !="" && floor_price !="" && ceil_price !="" &&  cost !="" && title !="" && price !=""){

            $.ajax({
                url:server_url+"/update_item_values",
                data:{tax:tax,sub_category:sub_category,category:category,re_order:re_order,floor_price:floor_price,ceil_price:ceil_price,cost:cost,price:price,title:title,m_code:m_code,code:code,id:this.state.upId,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);
                    if(res.status==1){
                        $('.msg').html(`<div style='font-size:12px' class='info'>${title} Updated</div>`);
                        $.ajax({
                            url:server_url+"/admin_items_report",
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
                
                                this.setState({rowItem:arr});
                                
                            },error: function(xhr, status, text) {
                
                                if(xhr.status ==419){
                                    window.location = server_url;
                                }
                
                            }
                        });
                    }
                }
            });

        }else{
            $('.msg').html("<div class='err'>Error: Value(s) Missing</div>");
        }
    },
    getInitialState:function(){
        return{
            rowItem:[],
            optItem:[],
            upItemTtl:"",
            upItemCode:"",
            upPrice:"",
            upQty:"",
            upTax:"",
            upCatg:"",
            upSubCatg:"",
            upStatus:"",
            detsItem:[],
            detsItemDesc:"",
            detsCost:"",
            detsPrice:"",
            detsFloorPrice:"",
            detsCeilPrice:"",
            detsCode:"",
            detsManCode:"",
            catgItem:[],
            subCatgItem:[],
            upId:""
        }
    },
    searchItemRprt:function(){
        let item = $('.txt_srch_admin_rprts').val();
        
        if(item.length !=0){
            $.ajax({
                url:server_url+"/search_item_reports",
                data:{item:item,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    console.log(data); 
                    let res = JSON.parse(data);
                    let arr = [];
                              
                    $.each(res,function(index,value){
                        arr.push(res[index]);
                    });
    
                    this.setState({rowItem:arr});
                }
            });

        }else{
          
            $.ajax({
                url:server_url+"/admin_items_report",
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
    
                    this.setState({rowItem:arr});
                    
                },error: function(xhr, status, text) {
    
                    if(xhr.status ==419){
                        window.location = server_url;
                    }
    
                }
            });
        }
        
    },
    fetchUpBranchData: function(id){
     
        let item = $('.txt_branch_items').val();
        
       
            $('.rprt_loader').show();
 
                    $.ajax({
                        url:server_url+"/up_reports_check",
                        data:{"_token":cs_token},
                        type:"POST",
                        context: this,
                        success:function(data){
                            //console.log(data);
                            let res = JSON.parse(data);
            
                            if(res.curr_branch != id){
                                $('.roll_branch').fadeOut('slow');
                                let qry;
                                //let qry = "SELECT * FROM accounts WHERE up_branch='"+id+"'";
                                if(item !=""){
                                    qry = "SELECT * FROM items WHERE up_branch='1' AND item_desc='" +item+  "'";
                                }else{
                                    qry = "SELECT * FROM items WHERE up_branch='1'";
                                }
               
                                $.ajax({
                                    url:"http://www.ababuapps.com/up_pos_test/custom_reports.php",
                                    data:{dates:"",dates_col:"time",qry_str:qry,rprt:"items","_token":cs_token},
                                    type:"POST",
                                    context: this,
                                    success:function(data){
                                        $('.branch_acc_act_reports_dates').val('');
                                        $('.txt_branch_items').val('');
                                        console.log(data);
                                        let res = JSON.parse(data);
                                        let arr = [];
                                                                
                                        $.each(res,function(index,value){
                                            arr.push(res[index]);
                                        });
                        
                                        this.setState({rowItem:arr});
                                        $('.rprt_loader').fadeOut();
                                    }
                                });
            
                            }
            
                        }
                    });


    
    },
    selUpBranch:function(){
        $('.roll_branch').fadeIn('slow');

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

                    $(".txt_branch_items").easyAutocomplete(options);

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

    },
    render:function(){
        return(
            <div className='ad_wraps'>
    
            <div id="items_rprt_tbl" className='reports_table_wrap'>
                    <div className='rprt_top_title'>

                        <input type="text" onKeyUp={this.searchItemRprt} placeholder='Search Items...' className="txt_srch_admin_rprts" />
                        <div className='up_branch' onClick={this.selUpBranch}></div>
                    </div>
                <table>

                    <thead>
                    <tr>
                        <th>Code</th><th>Item Description</th><th>Price</th><th>Qty</th><th>Tax</th><th>Status</th><th></th><th></th><th></th>
                    </tr>
                    </thead>

                    <tbody id='tbl_admin_items_rprt'>
                        {
                            this.state.rowItem.map(this.eachRow)
                        }
                    </tbody>
                </table>
            </div>

            <div className='edit_window'>
                    <div className="cls_blk" onClick={this.clsDialog.bind(this,"edit_window")}></div>
                    <div className='edit_window_head'>
                        
                        <b></b>
                    </div>
                    <div className='msg'></div>
                    
                    <div className='edit_window_ins'>
                        <table> 
                        
                            <tbody>
                                <tr><td><label>Code</label></td></tr>
                                <tr><td><input type='text'  className="txt_edit_item_code" /></td></tr>
                                <tr><td><label>Manufacture's Code</label></td></tr>
                                <tr><td><input type='text'  className="txt_edit_manf_code" /></td></tr>
                                <tr><td><label>Item Title</label></td></tr>
                                <tr><td><input type='text'  className="txt_edit_item_title" /></td></tr>
                                <tr><td><label>Cost</label></td></tr>
                                <tr><td><input type='text'  className="txt_edit_cost" /></td></tr>
                                <tr><td><label>Price</label></td></tr>
                                <tr><td><input type='text'  className="txt_edit_price" /></td></tr>
                                <tr><td><label>Ceil Price</label></td></tr>
                                <tr><td><input type='text'  className="txt_edit_ceil_price" /></td></tr>
                           
                            </tbody>
                        </table>
                </div>

                <div className='edit_window_ins'>
                        <table> 
                                <tbody>
                                <tr><td><label>Floor Price</label></td></tr>
                                <tr><td><input type='text'  className="txt_edit_floor_price" /></td></tr>
                                <tr><td><label>Re-Order Level</label></td></tr>
                                <tr><td><input type='text'  className="txt_edit_re_order_level" /></td></tr>
                                 
                                    <tr><td><label>Category</label></td></tr>
                                    <tr><td>
                                        <select className='sel_edit_category'>
                                            <option></option>
                                            {this.state.catgItem.map(this.catgOption)}

                                        </select>
                                    </td></tr>

                                    <tr><td><label>Sub Category</label></td></tr>
                                    <tr><td>
                                        <select className='sel_edit_sub_category'>
                                            <option></option>
                                            {this.state.subCatgItem.map(this.subCatgOption)}

                                        </select>
                                    </td></tr>

                                    <tr><td><label>Tax</label></td></tr>
                                    <tr><td>
                                        <select className='sel_edit_tax'>
                                            <option></option>
                                            {this.state.optItem.map(this.taxOption)}

                                        </select>
                                    </td></tr>
                                
                                
                                <tr><td><input type='button' onClick={this.btnEditItem} value='Edit Item' /></td></tr>
                                </tbody>
                        </table>
                </div>
            </div> 


            <div className='items_details_window'>
                <div className="cls_blk" onClick={this.clsDialog.bind(this,"items_details_window")}></div>
                <div className="act_item_dets_wrap">
                        <table>
                            <tr><td><p><b>Code:</b>  {this.state.detsManCode}</p></td><td><p><b>Item:</b> {this.state.detsItemDesc}</p></td><td><p><b>Cost:</b> {this.state.detsCost}</p></td><td><p><b>Price:</b> {this.state.detsPrice}</p></td></tr>
                            <tr><td><p><b>Manufacture's Code:</b> {this.state.detsCode}</p></td><td><p><b>Floor Price:</b> {this.state.detsFloorPrice}</p></td><td><p><b>Ceil Price:</b> {this.state.detsCeilPrice}</p></td></tr>
                            
                        </table>
                      
                </div>
                <div className="act_item_goods_wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th><th>Item</th><th>By</th><th>Receipt</th><th>Qty</th><th>Cost</th><th>Price</th><th>Ceil</th><th>Floor</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                                {
                                    this.state.detsItem.map(this.eachDetRow)
                                }
                            
                        </tbody>

                    </table>
                </div>
                 
            </div>

            <div className='roll_branch'>
                <div className="cls_blk" onClick={this.clsDialog.bind(this,"roll_branch")}></div>
                    <div className='msg'></div>
                    <li><p>Item</p></li>
                    <li><input type='text' className='txt_branch_items' /></li>
                    <li><input type='button' value='Ngong Road Shop' onClick={this.fetchUpBranchData.bind(this,"1")} /></li>
                    <li><input type='button' value='Kiambu Road Shop' onClick={this.fetchUpBranchData.bind(this,"2")} /></li>
                    <li><input type='button' value='Vetlab Shop' onClick={this.fetchUpBranchData.bind(this,"3")} /></li>
            </div>

            <div className='rprt_loader'><b>Loading...</b></div>

        </div>
                         
        );
    }
});



let TabNewUser = React.createClass({
    new_user:function(){
       
        let tender = $('.priv_tender').is(':checked') ? 1 : 0;
        let del_item = $('.priv_del_item').is(':checked') ? 1 : 0;
        let draw = $('.priv_draw').is(':checked') ? 1 : 0;
        let ad_lnk = $('.priv_ad_lnk').is(':checked') ? 1 : 0;
        let rtrn_it = $('.priv_rtrn_it').is(':checked') ? 1 : 0;
        let mng_user = $('.priv_mng_user').is(':checked') ? 1 : 0;
        let mng_drawer = $('.priv_mng_drawer').is(':checked') ? 1 : 0;
        let mng_tax = $('.priv_mng_tax').is(':checked') ? 1 : 0;
        let mng_gds = $('.priv_mng_gds').is(':checked') ? 1 : 0;
        let mng_brnch = $('.priv_mng_brnch').is(':checked') ? 1 : 0;
        let mng_item = $('.priv_mng_item').is(':checked') ? 1 : 0;
        
        let mng_cust = $('.priv_mng_cust').is(':checked') ? 1 : 0;
        let mng_clubs = $('.priv_mng_clubs').is(':checked') ? 1 : 0;
        let mng_events = $('.priv_mng_events').is(':checked') ? 1 : 0;
        let mng_accs = $('.priv_mng_accs').is(':checked') ? 1 : 0;
        let ball_pool = $('.priv_ball_pool').is(':checked') ? 1 : 0;
        
        let cust_access = $('.priv_cust_access').is(':checked') ? 1 : 0;
        let offer_discount = $('.priv_offer_discount').is(':checked') ? 1 : 0;
        let credit_sale = $('.priv_credit_sale').is(':checked') ? 1 : 0;
        let remote_branch = $('.priv_remote_branch').is(':checked') ? 1 : 0;
        
          
           
        let fname = $('.txt_fname').val();
        let lname = $('.txt_lname').val();
        let email = $('.txt_email').val();
        let phone = $('.txt_phone').val();
        let passd = $('.txt_passd').val();
        let conf_passd = $('.txt_passd_conf').val();

        if(fname !="" && lname !="" && email !="" && phone !="" && passd !="" && conf_passd !=""){

            if(passd == conf_passd){
                $.ajax({
                    url:server_url+"/new_user",
                    data:{fname:fname,lname:lname,email:email,
                        phone:phone,passd:passd,conf_passd:conf_passd,cust_access:cust_access,
                        offer_discount:offer_discount,credit_sale:credit_sale,
                        mng_brnch:mng_brnch,mng_gds:mng_gds,mng_tax:mng_tax,mng_drawer:mng_drawer,
                        mng_user:mng_user,rtrn_it:rtrn_it,ad_lnk:ad_lnk,mng_item:mng_item,remote_branch:remote_branch,
                        mng_cust:mng_cust,mng_clubs:mng_clubs,mng_events:mng_events,mng_accs:mng_accs,ball_pool:ball_pool,
                        draw:draw,del_item:del_item,tender:tender,"_token":cs_token},
                    type:"POST",
                    context: this,
                    success:function(data){
                        //console.log(data);
                        let res = JSON.parse(data);
                        if(res.status==1){
                            $('.txt_fname').val('');
                            $('.txt_lname').val('');
                            $('.txt_email').val('');
                            $('.txt_phone').val('');
                            $('.txt_passd').val('');
                            $('.txt_passd_conf').val('');
                            
                            $('.priv_tender').prop('checked', false);
                            $('.priv_del_item').prop('checked', false);
                            $('.priv_draw').prop('checked', false);
                            $('.priv_ad_lnk').prop('checked', false);
                            $('.priv_rtrn_it').prop('checked', false);
                            $('.priv_mng_user').prop('checked', false);
                            $('.priv_mng_tax').prop('checked', false);
                            $('.priv_mng_drawer').prop('checked', false);
                            $('.priv_mng_gds').prop('checked', false);
                            $('.priv_mng_brnch').prop('checked', false);
                            $('.priv_mng_item').prop('checked', false);

                            $('.priv_mng_cust').prop('checked', false);
                            $('.priv_mng_clubs').prop('checked', false);
                            $('.priv_mng_events').prop('checked', false);
                            $('.priv_mng_accs').prop('checked', false);
                            $('.priv_ball_pool').prop('checked', false);
                            $('.priv_cust_access').prop('checked', false);
                            $('.priv_offer_discount').prop('checked', false);
                            $('.priv_credit_sale').prop('checked', false);
                            $('.priv_remote_branch').prop('checked', false);

                            $('.msg').html(`<div class='info'>Success: ${fname} ${lname} Saved</div>`);
                        }else{
                            $('.msg').html(`<div class='err'>Error: ${fname} ${lname} Already Exists</div>`);
                        }
                    },error: function(xhr, status, text) {

                        if(xhr.status ==419){
                            window.location = server_url;
                        }
    
                    }
                });
            }else{
                $('.msg').html(`<div class='err'>Error: Passwords do not match</div>`);
            }
            

        }else{
            $('.msg').html(`<div class='err'>Error: Value(s) Missing</div>`);
        }
       

    },
    render:function(){
        return(
            <div className='ad_wraps'>
                            <div className='msg'></div>
                            <div className='form' >
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td><label>First Name</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text' className='txt_fname' /></td>
                                            </tr>
                                            <tr>
                                                <td><label>Last Name</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text' className='txt_lname' /></td>
                                            </tr>
                                            <tr>
                                                <td><label>Email</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text' className='txt_email' /></td>
                                            </tr>
                                            <tr>
                                                <td><label>Phone</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text' className="txt_phone"  /></td>
                                            </tr>
                                            <tr>
                                                <td><label>Password</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='password' className='txt_passd' /></td>
                                            </tr>
                                            <tr>
                                                <td><label>Confirm Password</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='password' className='txt_passd_conf' /></td>
                                            </tr>
                                            
                                            <tr>
                                                <td></td>
                                            </tr>
                                            
                                        </tbody>
                                    </table>
                                </div>

                                <div className='form' id='frm_user_priv_chks'>
                                    <table>
                                        <tr>
                                            <td><input type='checkbox' className='priv_tender' />Tender</td>
                                            <td><input type='checkbox' className='priv_del_item' />Delete Item</td>
                                        </tr>
                                        <tr>
                                            <td><input type='checkbox' className='priv_draw' />Drawings</td>
                                            <td><input type='checkbox' className='priv_ad_lnk' />Admin Link</td>
                                        </tr>
                                        <tr>
                                            <td><input type='checkbox' className='priv_rtrn_it' />Return Items</td>
                                            <td><input type='checkbox' className='priv_mng_user' />Manage Users</td>
                                        </tr>
                                        <tr>
                                            <td><input type='checkbox' className='priv_mng_drawer' />Manage Drawer</td>
                                            <td><input type='checkbox' className='priv_mng_item' />Manage Items</td>
                                        </tr>
                                        <tr>
                                            <td><input type='checkbox' className='priv_mng_tax' />Manage Taxes</td>
                                            <td><input type='checkbox' className='priv_mng_gds' />Manage Inventory</td>
                                        </tr>
                                        <tr>
                                            <td><input type='checkbox' className='priv_mng_brnch' />Manage Branches</td>
                                            <td><input type='checkbox' className='priv_mng_cust' />Manage Customers</td>
                                        </tr>
                                        <tr>
                                            <td><input type='checkbox' className='priv_mng_clubs' />Manage Clubs</td>
                                            <td><input type='checkbox' className='priv_mng_events' />Manage Events</td>
                                        </tr>
                                        <tr>
                                            <td><input type='checkbox' className='priv_mng_accs' />Accounts Activity</td>
                                            <td><input type='checkbox' className='priv_ball_pool' />Ball Pooll</td>
                                        </tr>
                                        <tr>
                                            <td><input type='checkbox' className='priv_cust_access' />Customer Account Access</td>
                                            <td><input type='checkbox' className='priv_offer_discount' />Offer Discount</td>
                                        </tr>
                                        <tr>
                                            <td><input type='checkbox' className='priv_credit_sale' />Credit Sale</td>
                                            <td><input type='checkbox' className='priv_remote_branch' />Remote Branch Access</td>
                                            
                                        </tr>
                                    </table>
                                    
                                    <ul>
                                        <li><input type='button' className='btn_new_user' onClick={this.new_user} value='Save' /></li>
                                    </ul>
                                   
                                </div>
                            </div>
                         
        );
    }
});



let TabUserLogs = React.createClass({
    componentDidMount() {

        let optional_config = {
            mode: "range",
            dateFormat: "d-m-Y"
        };

        $(".datePick").flatpickr(optional_config);

        $.ajax({
            url:server_url+"/users_logs",
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

                this.setState({rowItem:arr});
            
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });
    },
    getInitialState:function(){
        return{
            rowItem:[]
        }
    },
    eachRow:function(item,i){
        return(
            <tr key={i}>
                <td>{item.log_time}</td>
                <td>{item.user}</td>
                <td>{item.desc}</td>
                             
            </tr>
        )
    },
    searchLogs:function(){
        let dates = $('.datePick').val();

        if(dates !=""){

            if(dates.indexOf("to")){
 
                $.ajax({
                    url:server_url+"/search_logs",
                    data:{dates:dates,"_token":cs_token},
                    type:"POST",
                    context: this,
                    success:function(data){
                        console.log(data);
                        
                        let res = JSON.parse(data);
                        let arr = [];
                                                
                        $.each(res,function(index,value){
                            arr.push(res[index]);
                        });

                        this.setState({rowItem:arr});
                        
                    }
                });

            }

        }

    },
    render:function(){
        return(
            <div className='ad_wraps'>

                <div className='rprt_top_title'>
                    <input type="text" placeholder="Select Dates" className="datePick" />
                    <div className='search_btn' onClick={this.searchLogs}></div>

               </div>

                <div id="rprt_user_logs" className='reports_table_wrap'>
                    <table>
                    <thead>
                        <tr>
                            <th>Date/Time</th><th>User</th><th>Description</th>
                        </tr>
                                            
                        </thead>
                        <tbody>
                        {
                            this.state.rowItem.map(this.eachRow)
                            }
                        </tbody>
                    </table>
                </div>
                
            </div>
                         
        );
    }
});

let TabUserReports = React.createClass({
    componentDidMount() {

        $.ajax({
            url:server_url+"/users_reports",
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

                this.setState({rowItem:arr});
            
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });
    },
    getInitialState:function(){
        return{
            rowItem:[],
            selUserId:"",
            selFname:"",
            selLname:""
        }
    },
    enableUser:function(item){
        $.ajax({
            url:server_url+"/enable_user",
            data:{id:item,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                
                $.ajax({
                    url:server_url+"/users_reports",
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
        
                        this.setState({rowItem:arr});
                    
                    },error: function(xhr, status, text) {
        
                        if(xhr.status ==419){
                            window.location = server_url;
                        }
        
                    }
                });

            }
        });
    },
    disableUser:function(item){
        $.ajax({
            url:server_url+"/disable_user",
            data:{id:item,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                
                $.ajax({
                    url:server_url+"/users_reports",
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
        
                        this.setState({rowItem:arr});
                    
                    },error: function(xhr, status, text) {
        
                        if(xhr.status ==419){
                            window.location = server_url;
                        }
        
                    }
                });

            }
        });
    },
    userAccessWindow:function(item){
        $('.roll_search').fadeIn('slow');
        this.setState({selUserId:item});
        
        $.ajax({
            url:server_url+"/get_user_priviledges",
            data:{uid:item,"_token": cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let resx = JSON.parse(data);

                this.setState({selFname:resx.fname});
                this.setState({selLname:resx.lname});

                if(resx.tender ==1){
                    $('.up_priv_tender').prop('checked', true);
                }else{
                    $('.up_priv_tender').prop('checked', false);
                }

                if(resx.del_item ==1){
                    $('.up_priv_del_item').prop('checked', true);
                }else{
                    $('.up_priv_del_item').prop('checked', false);
                }

                if(resx.draw ==1){
                    $('.up_priv_draw').prop('checked', true);
                }else{
                    $('.up_priv_draw').prop('checked', false);
                }

                if(resx.admin_link ==1){
                    $('.up_priv_ad_lnk').prop('checked', true);
                }else{
                    $('.up_priv_ad_lnk').prop('checked', false);
                }

                if(resx.drawer ==1){
                    $('.up_priv_mng_drawer').prop('checked', true);
                }else{
                    $('.up_priv_mng_drawer').prop('checked', false);
                }

                if(resx.return_item ==1){
                    $('.up_priv_rtrn_it').prop('checked', true);
                }else{
                    $('.up_priv_rtrn_it').prop('checked', false);
                }

                if(resx.mng_item ==1){
                    $('.up_priv_mng_item').prop('checked', true);
                }else{
                    $('.up_priv_mng_item').prop('checked', false);
                }

                if(resx.mng_users ==1){
                    $('.up_priv_mng_user').prop('checked', true);
                }else{
                    $('.up_priv_mng_user').prop('checked', false);
                }

                if(resx.mng_taxes ==1){
                    $('.up_priv_mng_tax').prop('checked', true);
                }else{
                    $('.up_priv_mng_tax').prop('checked', false);
                }

                if(resx.mng_goods ==1){
                    $('.up_priv_mng_gds').prop('checked', true);
                }else{
                    $('.up_priv_mng_gds').prop('checked', false);
                }

                if(resx.mng_branches ==1){
                    $('.up_priv_mng_brnch').prop('checked', true);
                }else{
                    $('.up_priv_mng_brnch').prop('checked', false);
                }

                if(resx.mng_customers ==1){
                    $('.up_priv_mng_customers').prop('checked', true);
                }else{
                    $('.up_priv_mng_customers').prop('checked', false);
                }

                if(resx.mng_clubs ==1){
                    $('.up_priv_mng_clubs').prop('checked', true);
                }else{
                    $('.up_priv_mng_clubs').prop('checked', false);
                }

                if(resx.mng_events ==1){
                    $('.up_priv_mng_events').prop('checked', true);
                }else{
                    $('.up_priv_mng_events').prop('checked', false);
                }

                if(resx.mng_accounts ==1){
                    $('.up_priv_mng_accounts').prop('checked', true);
                }else{
                    $('.up_priv_mng_accounts').prop('checked', false);
                }

                if(resx.ball_pool ==1){
                    $('.up_priv_ball_pool').prop('checked', true);
                }else{
                    $('.up_priv_ball_pool').prop('checked', false);
                }

                if(resx.tender_accounts ==1){
                    $('.up_priv_cust_acc_access').prop('checked', true);
                }else{
                    $('.up_priv_cust_acc_access').prop('checked', false);
                }

                if(resx.offer_discount ==1){
                    $('.up_priv_offer_discount').prop('checked', true);
                }else{
                    $('.up_priv_offer_discount').prop('checked', false);
                }

                if(resx.credit_sale ==1){
                    $('.up_priv_credit_sale').prop('checked', true);
                }else{
                    $('.up_priv_credit_sale').prop('checked', false);
                }

                if(resx.remote_branch_access ==1){
                    $('.up_priv_remote_branch').prop('checked', true);
                }else{
                    $('.up_priv_remote_branch').prop('checked', false);
                }

            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });
    },
    eachRow:function(item,i){
        if(item.status =='Active'){
            return(
                <tr key={i}>
                    <td>{item.fname}</td>
                    <td>{item.lname}</td>
                    <td>{item.phone}</td>
                    <td>{item.email}</td>
                    <td>{item.status}</td>
                    <td><a className='rprt_link' onClick={this.userAccessWindow.bind(this,item.id)}>Access</a></td>
                    <td></td>
                    <td><a className='del_rprt_link' onClick={this.disableUser.bind(this,item.id)}>Disable</a></td>
                           
                </tr>
            )
        }else{
            return(
                <tr key={i}>
                    <td>{item.fname}</td>
                    <td>{item.lname}</td>
                    <td>{item.phone}</td>
                    <td>{item.email}</td>
                    <td>{item.status}</td>
                    <td><a className='rprt_link' onClick={this.userAccessWindow.bind(this,item.id)}>Access</a></td>
                    <td></td>
                    <td><a className='del_rprt_link' onClick={this.enableUser.bind(this,item.id)}>Enable</a></td>
                           
                </tr>
            )
        }
        
    },
    clsDialog:function(i,event){
        $("."+i).fadeOut('slow');
    },
    updateUserPriv:function(){
        let uid = this.state.selUserId;
        
        let tender = $('.up_priv_tender').is(':checked') ? 1 : 0;
        let del_item = $('.up_priv_del_item').is(':checked') ? 1 : 0;
        let draw = $('.up_priv_draw').is(':checked') ? 1 : 0;
        let ad_lnk = $('.up_priv_ad_lnk').is(':checked') ? 1 : 0;
        let rtrn_it = $('.up_priv_rtrn_it').is(':checked') ? 1 : 0;
        let mng_user = $('.up_priv_mng_user').is(':checked') ? 1 : 0;
        let mng_tax = $('.up_priv_mng_tax').is(':checked') ? 1 : 0;
        let mng_gds = $('.up_priv_mng_gds').is(':checked') ? 1 : 0;
        let mng_brnch = $('.up_priv_mng_brnch').is(':checked') ? 1 : 0;
        let mng_item = $('.up_priv_mng_item').is(':checked') ? 1 : 0;
        let mng_drawer = $('.up_priv_mng_drawer').is(':checked') ? 1 : 0;
        let mng_customers = $('.up_priv_mng_customers').is(':checked') ? 1 : 0;
        let mng_clubs = $('.up_priv_mng_clubs').is(':checked') ? 1 : 0;
        let mng_events = $('.up_priv_mng_events').is(':checked') ? 1 : 0;
        let mng_accounts = $('.up_priv_mng_accounts').is(':checked') ? 1 : 0;
        let mng_pool = $('.up_priv_ball_pool').is(':checked') ? 1 : 0;
        let tender_accounts = $('.up_priv_cust_acc_access').is(':checked') ? 1 : 0;
        let offer_discount = $('.up_priv_offer_discount').is(':checked') ? 1 : 0;
        let credit_sale = $('.up_priv_credit_sale').is(':checked') ? 1 : 0;
        let remote_branch = $('.up_priv_remote_branch').is(':checked') ? 1 : 0;

        
          
        $.ajax({
            url:server_url+"/update_user_priviledges",
            data:{uid:uid,
                mng_clubs:mng_clubs,mng_customers:mng_customers,remote_branch:remote_branch,
                mng_pool:mng_pool,mng_accounts:mng_accounts,mng_events:mng_events,
                credit_sale:credit_sale,offer_discount:offer_discount,tender_accounts:tender_accounts,
                mng_brnch:mng_brnch,mng_gds:mng_gds,mng_tax:mng_tax,mng_drawer:mng_drawer,
                mng_user:mng_user,rtrn_it:rtrn_it,ad_lnk:ad_lnk,mng_item:mng_item,
                draw:draw,del_item:del_item,tender:tender,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let res = JSON.parse(data);
                if(res.status==1){
                    
                    $('.msg').html(`<div class='info'>Success: Access Updated</div>`);
                
                }else{
                    $('#admin').hide();
                    $('#teller').fadeIn('slow');
                    
                    $('.drawer_msg').empty();
                    $('.msg').empty();

                    $(".login").fadeIn('slow');
                    $("#username_ipt").focus();
                }
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });
        
    },
    render:function(){
        return(
            <div className='ad_wraps'>

                <div id="new_rprt_users_tbl" className='reports_table_wrap'>
                    
                    <table>
                        <thead>
                        <tr>
                            <th>First Name</th><th>Last Name</th><th>Phone</th><th>Email</th><th>Status</th><th>Access</th><th></th><th></th>
                        </tr>
                                            
                        </thead>
                        <tbody>
                        {
                            this.state.rowItem.map(this.eachRow)
                            }
                        </tbody>
                    </table>
                </div>

                    <div className='roll_search'>
                    <div className="cls_blk" onClick={this.clsDialog.bind(this,"roll_search")}></div>
                        <div className='msg'></div>
                        <li><b>{this.state.selFname} {this.state.selLname}</b></li>
                        <li><input type='checkbox' className='up_priv_tender' />Tender</li>
                        <li><input type='checkbox' className='up_priv_del_item' />Delete Item</li>
                        <li><input type='checkbox' className='up_priv_draw' />Drawings</li>
                        <li><input type='checkbox' className='up_priv_ad_lnk' />Admin Link</li>
                        <li><input type='checkbox' className='up_priv_rtrn_it' />Return Items</li>
                        <li><input type='checkbox' className='up_priv_mng_item' />Manage Items</li>
                        <li><input type='checkbox' className='up_priv_mng_drawer' />Manage Drawer</li>
                        <li><input type='checkbox' className='up_priv_mng_user' />Manage Users</li>
                        <li><input type='checkbox' className='up_priv_mng_tax' />Manage Taxes</li>
                        <li><input type='checkbox' className='up_priv_mng_gds' />Manage Inventory</li>
                        <li><input type='checkbox' className='up_priv_mng_brnch' />Manage Branches</li>
                        <li><input type='checkbox' className='up_priv_mng_customers' />Manage Customers</li>
                        <li><input type='checkbox' className='up_priv_mng_clubs' />Manage Clubs</li>
                        <li><input type='checkbox' className='up_priv_mng_events' />Manage Events</li>
                        <li><input type='checkbox' className='up_priv_mng_accounts' />Accounts Activity</li>
                        <li><input type='checkbox' className='up_priv_ball_pool' />Ball Pool</li>
                        <li><input type='checkbox' className='up_priv_cust_acc_access' />Customer Accounts Access</li>
                        <li><input type='checkbox' className='up_priv_offer_discount' />Offer Discount</li>
                        <li><input type='checkbox' className='up_priv_credit_sale' />Account Sale</li>
                        <li><input type='checkbox' className='up_priv_remote_branch' />Remote Branch</li>
        
                        <li><input type='button' onClick={this.updateUserPriv} value='Update Access' /></li>
                </div>
                
            </div>
                         
        );
    }
});

let TabMyAccount = React.createClass({
    componentDidMount() {

        $.ajax({
            url:server_url+"/get_my_acc_dets",
            data:{"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let res = JSON.parse(data);
                $('.txt_fname').val(res.fname);
                $('.txt_lname').val(res.lname);
                $('.txt_phone').val(res.phone);
                $('.txt_email').val(res.email);
                /*
                let res = JSON.parse(data);
                this.setState({fname:res.fname});
                this.setState({lname:res.lname});
                this.setState({phone:res.phone});
                this.setState({email:res.email});
                */
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

    },
    update_acc_info:function(){
        let up_fname = $('.txt_fname').val();
        let up_lname = $('.txt_lname').val();
        let up_phone = $('.txt_phone').val();
        let up_email = $('.txt_email').val();

        if(up_fname !="" && up_lname !="" && up_phone !="" && up_email !=""){

            $.ajax({
                url:server_url+"/update_acc_info",
                data:{up_fname:up_fname,up_lname:up_lname,up_phone:up_phone,up_email:up_email,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);
                    if(res.status == 1){
                        $('.msg').html("<div class='info'>Account Updated Successfully</div>");
                    }
                    
                },error: function(xhr, status, text) {

                    if(xhr.status ==419){
                        window.location = server_url;
                    }

                }
            });

        }else{
            $('.msg').html("<div class='err'>Error: Value(s) Missing</div>");
        }

    },
    updatePassd: function(){
        let passd = $('.txt_passd').val();
        let passd_conf = $('.txt_passd_conf').val();

        if(passd !="" && passd_conf !=""){

            if(passd == passd_conf){

                $.ajax({
                    url:server_url+"/update_passd",
                    data:{passd:passd,"_token":cs_token},
                    type:"POST",
                    context: this,
                    success:function(data){
                        //console.log(data);
                        let res = JSON.parse(data);
                        if(res.status == 1){
                            $('.txt_passd').val('');
                            $('.txt_passd_conf').val('');
                            $('.msg').html("<div class='info'>Password Updated Successfully</div>");
                        }
                        
                    }
                });

            }else{
                $('.msg').html("<div class='err'>Error: Passwords Don't Match</div>");
            }

        }else{
            $('.msg').html("<div class='err'>Error: Value(s) Missing</div>");
        }
    },
    render:function(){
        return(
            <div className='ad_wraps'>
            <div className='msg'></div>
            <div className='form' >
                    <table>
                        <tbody>
                            <tr>
                                <td><label>First Name</label></td>
                            </tr>
                            <tr>
                                <td><input type='text'  className='txt_fname' /></td>
                            </tr>
                            <tr>
                                <td><label>Last Name</label></td>
                            </tr>
                            <tr>
                                <td><input type='text'  className='txt_lname' /></td>
                            </tr>
                            <tr>
                                <td><label>Email</label></td>
                            </tr>
                            <tr>
                                <td><input type='text'  className='txt_email' /></td>
                            </tr>
                            <tr>
                                <td><label>Phone</label></td>
                            </tr>
                            <tr>
                                <td><input type='text'  className="txt_phone"  /></td>
                            </tr>
                            <tr>
                                <td><input type='button' className='btn_edit_info' onClick={this.update_acc_info} value='Update Account' /></td>
                            </tr>
                            
                        </tbody>
                    </table>
                </div>

                <div className='form'>
                    <table>
                        <tbody>
                            <tr>
                                <td><label>New Password</label></td>
                            </tr>
                            <tr>
                                <td><input type='password' className='txt_passd' /></td>
                            </tr>
                            <tr>
                                <td><label>Confirm Password</label></td>
                            </tr>
                            <tr>
                                <td><input type='password' className='txt_passd_conf' /></td>
                            </tr>
                            
                            <tr>
                                <td><input type='button' onClick={this.updatePassd} className='btn_update_passd' value='Update Password' /></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
                         
        );
    }
});


let TabNewTax = React.createClass({
    componentDidMount() {

        $(".taxPerc").numeric();
        $(".taxPerc").numeric(false, function() { alert("Integers only"); this.value = ""; this.focus(); });
        $(".taxPerc").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
        $(".taxPerc").numeric({ decimal: false, negative: false }, function() { alert("Positive integers only"); this.value = ""; this.focus(); });

    },
    new_tax:function(){
        let tax_title = $('.txt_tax_title').val();
        let tax_perc = $('.taxPerc').val();

        if(tax_title !="" && tax_perc !=""){

            $.ajax({
                url:server_url+"/new_tax",
                data:{tax_title:tax_title,tax_perc:tax_perc,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    let res = JSON.parse(data);
                    if(res.status==1){
                        $('.txt_tax_title').val('');
                        $('.taxPerc').val('');
                        $('.msg').html(`<div class='info'>Success: ${tax_title} Saved</div>`);
                    }else{
                        $('.msg').html(`<div class='err'>Error: ${tax_title} Already exists</div>`);
                    }
                },error: function(xhr, status, text) {

                    if(xhr.status ==419){
                        window.location = server_url;
                    }

                }
            });

        }else{
            $('.msg').html(`<div class='err'>Error: Value(s) Missing</div>`);
        }
    },
    render:function(){
        return(
            <div className='ad_wraps' >
                                <div className='form' >
                                    <div className='msg'></div>
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td><label>Tax Title</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text' className='txt_tax_title' /></td>
                                            </tr>
                                            <tr>
                                                <td><label>Percentage</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text' className="taxPerc"  /></td>
                                            </tr>
                                            <tr>
                                                <td><input type='button' className='btn_new_tax' onClick={this.new_tax} value='Save' /></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                </div>
                         
        );
    }
});

let TabTaxReports = React.createClass({
    componentDidMount() {

        $.ajax({
            url:server_url+"/tax_reports",
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

                this.setState({rowItem:arr});
            
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

        

    },
    getInitialState:function(){
        return{
            rowItem:[]
        }
    },
    eachRow:function(item,i){
        return(
            <tr key={i}>

                <td>{item.tax_desc}</td>
                <td>{item.perc}</td>
                <td>{item.status}</td>
                <td></td>
             
            </tr>
        )
    },
    render:function(){
        return(
            <div className='ad_wraps'>
                <div id='rprt_tax_tbl' className='reports_table_wrap'>
                <table>
                    <thead>
                        <tr><th>Tax Title</th><th>Percentage</th><th>Status</th><th></th></tr>
                                            
                        </thead>
                        <tbody>
                        {
                            this.state.rowItem.map(this.eachRow)
                            }
                        </tbody>
                    </table>
                </div>
            </div>
                         
        );
    }
});


let TabNewCustomers = React.createClass({
    componentDidMount() {
    
    },
    newCustomer:function(){
        let fname = $('.cust_fname').val();
        let lname = $('.cust_lname').val();
        let phone = $('.cust_phone').val();
        let email = $('.cust_email').val();
        let type = $('.sel_cust_type').val();
        let org = $('.cust_org').val();
        let member_no = $('.cust_member_no').val();
        
        
        if(type !=""){
            $.ajax({
                url:server_url+"/new_customer",
                data:{member_no:member_no,fname:fname,lname:lname,phone:phone,email:email,type:type,org:org,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    let res = JSON.parse(data);
                    if(res.status==1){
                        
                        $('.cust_fname').val('');
                        $('.cust_lname').val('');
                        $('.cust_phone').val('');
                        $('.cust_email').val('');
                        $('.sel_cust_type').val('');
                        $('.cust_org').val('');
                        $('.cust_member_no').val('');
          

                        $('.msg').html(`<div class='info'>Success: Customer Saved</div>`);
                    
                    }
                },error: function(xhr, status, text) {

                    if(xhr.status ==419){
                        window.location = server_url;
                    }

                }
            });
        }else{
            $('.msg').html(`<div class='err'>Error: Customer Type not selected</div>`);

        }
            

       
    },
    render:function(){
        return(
            <div className='ad_wraps' >
                                <div className='form' >
                                    <div className='msg'></div>
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td><label>First Name</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text' className='cust_fname' /></td>
                                            </tr>
                                            <tr>
                                                <td><label>Last Name</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text' className="cust_lname"  /></td>
                                            </tr>
                                            <tr>
                                                <td><label>Phone No</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text' className="cust_phone"  /></td>
                                            </tr>
                                            <tr>
                                                <td><label>Email</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text' className="cust_email"  /></td>
                                            </tr>
                                            <tr>
                                                <td><label>Type</label></td>
                                            </tr>
                                            <tr>
                                                <td>

                                                    <select className='sel_cust_type'>
                                                        <option></option>
                                                        <option>Corporate</option>
                                                        <option>Individual</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            
                                        </tbody>
                                    </table>
                                </div>

                                <div className='form' >
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td><label>Organization Name</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text' className='cust_org' /></td>
                                            </tr>
                                            <tr>
                                                <td><label>Member No </label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text' className='cust_member_no' /></td>
                                            </tr>
                                            
                                            <tr>
                                                <td><input type='button' className='btn_new_customer' onClick={this.newCustomer} value='Save' /></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                </div>
                         
        );
    }
});

let TabCustomersReports = React.createClass({
    componentDidMount() {
        $('.rprt_loader').fadeIn('slow');
        $.ajax({
            url:server_url+"/customers_reports",
            data:{"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let res = JSON.parse(data);
                let arr = [];

                $('.rprt_loader').fadeOut('slow');
                                        
                $.each(res,function(index,value){
                    arr.push(res[index]);
                });

                this.setState({rowItem:arr});
            
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

        $.ajax({
            url:server_url+"/search_auto_comp_customers",
            data:{"_token":cs_token},
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

                    $(".txt_cust_search").easyAutocomplete(options);

                   
                }
                

            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

    },
    getInitialState:function(){
        return{
            rowItem:[]
        }
    },
    eachRow:function(item,i){
        return(
            <tr key={i}>

                <td>{item.f_name}</td>
                <td>{item.s_name}</td>
                <td>{item.org}</td>
                <td>{item.phone}</td>
                <td>{item.email}</td>
                <td>{item.c_type}</td>
                <td>{item.member_no}</td>
                
                
                            
            </tr>
        )
    },
    searchReports:function(){
        let customer = $('.txt_cust_search').val();

        if(customer !=""){

            $('.rprt_loader').fadeIn('slow');
            $.ajax({
                url:server_url+"/search_customers_reports",
                data:{customer:customer,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    let res = JSON.parse(data);
                    let arr = [];
                                            
                    $.each(res,function(index,value){
                        arr.push(res[index]);
                    });
    
                    this.setState({rowItem:arr});

                    $('.rprt_loader').fadeOut('slow');
                
                },error: function(xhr, status, text) {
    
                    if(xhr.status ==419){
                        window.location = server_url;
                    }
    
                }
            });
        }
        
    },
    render:function(){
        return(
            <div className='ad_wraps'>
                <div className='rprt_top_title'>
                    <input type="text" placeholder="Type Customer Name..." className="txt_cust_search" />
                    <div className='search_btn' onClick={this.searchReports}></div>
                </div>

                <div id='rprt_cust_tbl' className='reports_table_wrap'>
                <table>
                    <thead>
                        <tr><th>First Name</th><th>Last Name</th><th>Org</th><th>Phone</th><th>Email</th><th>Type</th><th>Member No.</th></tr>
                                            
                        </thead>
                        <tbody>
                        {
                            this.state.rowItem.map(this.eachRow)
                            }
                        </tbody>
                    </table>
                    
                <div className='rprt_loader'><b>Loading...</b></div>
           
                </div>
            </div>
                         
        );
    }
});

let TabNewTournaments = React.createClass({
    componentDidMount() {
        let optional_config = {
            dateFormat: "d-m-Y"
        };

        $(".txt_tourn_date").flatpickr(optional_config);
    },  
    newTourn:function(){
        let tourn_title = $('.txt_tourn_title').val();
        let tourn_date = $('.txt_tourn_date').val();

        if(tourn_title !="" && tourn_date !=""){

            $.ajax({
                url:server_url+"/new_tourn",
                data:{tourn_title:tourn_title,tourn_date:tourn_date,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    let res = JSON.parse(data);
                    if(res.status==1){
                        $('.txt_tourn_title').val('');
                        $('.txt_tourn_date').val('');
                        $('.msg').html(`<div class='info'>Success: ${tourn_title} Saved</div>`);
                    }else{
                        $('.msg').html(`<div class='err'>Error: ${tourn_title} Already exists</div>`);
                    }
                },error: function(xhr, status, text) {

                    if(xhr.status ==419){
                        window.location = server_url;
                    }

                }
            });

        }else{
            $('.msg').html(`<div class='err'>Error: Value(s) Missing</div>`);
        }
    },
    render:function(){
        return(
            <div className='ad_wraps' >
                                <div className='form' >
                                    <div className='msg'></div>
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td><label>Tournament Title</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text' className='txt_tourn_title' /></td>
                                            </tr>
                                            <tr>
                                                <td><label>Date</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text' className="txt_tourn_date"  /></td>
                                            </tr>
                                            <tr>
                                                <td><input type='button' className='btn_new_tourn' onClick={this.newTourn} value='Save' /></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                </div>
                         
        );
    }
});

let TabTournamentReports = React.createClass({
    componentDidMount() {

        $.ajax({
            url:server_url+"/tourn_reports",
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

                this.setState({rowItem:arr});
            
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

        

    },
    getInitialState:function(){
        return{
            rowItem:[]
        }
    },
    eachRow:function(item,i){
        return(
            <tr key={i}>

                <td>{item.t_date}</td>
                <td>{item.title}</td>
                <td>{item.status}</td>
                <td>Edit</td>
                <td>Enable</td>
                
                            
            </tr>
        )
    },
    render:function(){
        return(
            <div className='ad_wraps'>
                <div id='rprt_tourn_tbl' className='reports_table_wrap'>
                <table>
                    <thead>
                        <tr><th>Date</th><th>Title</th><th>Status</th><th></th><th></th></tr>
                                            
                        </thead>
                        <tbody>
                        {
                            this.state.rowItem.map(this.eachRow)
                            }
                        </tbody>
                    </table>
                </div>
            </div>
                         
        );
    }
});

let TabGoodsUploads = React.createClass({
    
    
    upload_items_xls:function(){

        var formData = new FormData($('#upload_form')[0]);
        formData.append('items_file', $('input[type=file]')[0].files[0]);
        
        if($('input[type=file]')[0].files[0]){
            $.ajax({
                url:server_url+"/upload_goods_xls",
                data:formData,
                type:"POST",
                context: this,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN':cs_token
                },
                xhr: function () {
                    $('.msg').html("<div class='loading'>Loading.....</div>");
                    var xhr = new XMLHttpRequest();
    
                    xhr.upload.onprogress = function (e) {
                      var percent = '0';
                      var percentage = '0%';
                      
                      if (e.lengthComputable) {
                        
                        percent = Math.round((e.loaded / e.total) * 100);
                        percentage = percent + '%';
                        //$progressBar.width(percentage).attr('aria-valuenow', percent).text(percentage);
                        //console.log(percentage);
                        }
                    };
    
                    return xhr;
                  },
                success:function(data){
                    console.log(data);
                    let res = JSON.parse(data);
                    
                    if(res.status ==1){
                        $('.msg').html("<div class='info'>Success: Upload Complete</div>");
                    }else if(res.status ==0){
                        $('.msg').html("<div class='err'>Error: Incorrect Content Format</div>");
                    }else if(res.status ==2){
                        $('.msg').html("<div class='err'>Error: Item Not Found</div>");
                    }
                    
                    
                },error: function() {
                    console.log("Error");
                },complete: function () {
                    //$progress.hide();
                }
            });
        }else{
            $('.msg').html("<div class='err'>Error: Select File</div>");
        }
        

        
       
    },
    render:function(){
        return(

            <div className='ad_wraps'>
            
                <div className="admin_top">
                        <div className='msg'>

                        </div>
                      
                    </div>
                 
                    
                   
                  
                    <div className='form'>
                        <table>
                            <tbody>
                               
                                
                                <tr>
                                    <td><label>Upload Inventory</label></td>
                                </tr>

                                <tr>
                                    <td><label></label></td>
                                </tr>
                                
                                    <form id="upload_form" enctype="multipart/form-data">
                                        <tr>
                                           
                                            <td><input type='file' id='file_items'  name='file_items' /></td>
                                        </tr>
                                        
                                    </form>
                                <tr>
                                    <td><input type='button' onClick={this.upload_items_xls} value='Upload' /></td>
                                </tr>
                                
                            </tbody>
                        </table>
                </div>

                <div className='form'>
                        <table>
                            <tbody>
                                
                                <tr>
                                <td><label></label></td>
                                </tr>
                                <tr>
                                    <td><a href={server_url+'/download_goods_xls'}>Download Goods Template</a></td>
                                </tr>
                            </tbody>
                        </table>
                </div>
                         
            </div>
        )
    }
});

let TabGoodsReceive = React.createClass({
    componentDidMount() {

        $(".taxPerc").numeric();
        $(".taxPerc").numeric(false, function() { alert("Integers only"); this.value = ""; this.focus(); });
        $(".taxPerc").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
        $(".taxPerc").numeric({ decimal: false, negative: false }, function() { alert("Positive integers only"); this.value = ""; this.focus(); });

        $(".txt_goods_qty").numeric();
        $(".txt_goods_qty").numeric(false, function() { alert("Integers only"); this.value = ""; this.focus(); });
        $(".txt_goods_qty").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
        $(".txt_goods_qty").numeric({ decimal: false, negative: false }, function() { alert("Positive integers only"); this.value = ""; this.focus(); });

        
    },
    new_goods:function(){
        
         let receipt_no = $('.txt_goods_receipt_no').val(); 
         let qty = $('.txt_goods_qty').val();
         let cost = $('.txt_goods_cost').val();
         let price = $('.txt_goods_price').val();
         let ceil = $('.txt_goods_ceil_price').val();
         let floor = $('.txt_goods_floor_price').val();
         let comm = $('.txt_goods_comm').val();
         let item = this.state.itemId;

         if(item !="" && receipt_no !="" && qty !="" && cost !="" && price !="" && ceil !="" && floor !="" && comm !=""){

            $.ajax({
                url:server_url+"/new_goods_received",
                data:{item:item,receipt_no:receipt_no,qty:qty,cost:cost,price:price,ceil:ceil,floor:floor,comm:comm,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    console.log(data);
                    let res = JSON.parse(data);
                    if(res.status==1){
                        this.setState({itemId:""});
                        $('.txt_goods_receipt_no').val('');
                        $('.txt_goods_qty').val('');
                        $('.txt_goods_cost').val('');
                        $('.txt_goods_price').val('');
                        $('.txt_goods_ceil_price').val('');
                        $('.txt_goods_floor_price').val('');
                        $('.txt_goods_comm').val('');

                        $('#goods_msg').html(`<div class='info'>Success: ${receipt_no} Saved</div>`);
                    
                    }else{
                        $('#goods_msg').html(`<div class='err'>Error: ${receipt_no} Already exists</div>`);
                    }
                },error: function(xhr, status, text) {

                    if(xhr.status ==419){
                        window.location = server_url;
                    }

                }
            });

         }else{
            $('#goods_msg').html(`<div class='err'>Error: Value(s) Missing</div>`);
         }

    },
    getInitialState:function(){
        return{
            itemsTable:[],
            itemId:"",
        }
    },
    clsDialog:function(i,event){
        $("."+i).fadeOut('slow');
    },
    addItemBtn:function(i,event){
        //'item_desc','buy_price','sell_price','floor_price'
         $.ajax({
            url:server_url+"/fetch_goods_item",
            data:{item_id:i,type:"tender","_token": cs_token},
            type:"POST",
            context: this,
            success:function(data){
                let res = JSON.parse(data);
                $('.itemPullUp').fadeOut('slow');
                $.each(res,function(index,value){
                    $('.goods_item_pull_up').val('');
                    $('.txt_goods_cost').val(value.buy_price);
                    $('.txt_goods_price').val(value.sell_price);
                    $('.txt_goods_ceil_price').val(value.ceil_price);
                    $('.txt_goods_floor_price').val(value.floor_price);
                    $('#act_goods_desc').html("<p>"+value.item_desc+"</p>");
                    
                });
                this.setState({itemId:i});
            }
         });

    },
    eachItemRow:function(item,i){
        return(
            <tr key={i}>
                <td>{item.code_no}</td><td>{item.item_desc}</td><td>{item.qty}</td><td>{item.sell_price}.00</td><td><div className="add_tray" onClick={this.addItemBtn.bind(this,item.id)}></div></td>
            </tr>
        )
    },
    searchItemPullUp:function(){
        var item = $('.goods_item_pull_up').val();
        
        $.ajax({
            url:server_url+"/search_item_pull_up",
            data:{item:item,"_token": cs_token},
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
    ItemSelectWindow:function(){

        $('.itemPullUp').fadeIn('slow');
        $('.goods_item_pull_up').focus();

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
    render:function(){
        return(
            <div className='ad_wraps' >
                                <div className='form'>
                                    
                                    <div className="goods_item_hold">
                                        <div onClick={this.ItemSelectWindow} className="goods_item_hold" id="act_goods_desc"><p>Select Item</p></div>
                                       {/*<div className="add_goods_item" onClick={this.ItemSelectWindow}><b> Item</b></div>*/} 
                                    </div>
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td><label>Receipt No.</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text' className='txt_goods_receipt_no' /></td>
                                            </tr>
                                            <tr>
                                                <td><label>Quantity</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text' className="txt_goods_qty"  /></td>
                                            </tr>
                                            <tr>
                                                <td><label>Cost</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text' className="txt_goods_cost"  /></td>
                                            </tr>
                                            <tr>
                                                <td><label>Price</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text'  className="txt_goods_price"  /></td>
                                            </tr>
                                            <tr>
                                                <td><label>Ceil Price</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text'  className="txt_goods_ceil_price"  /></td>
                                            </tr>
                                            
                                        </tbody>
                                    </table>

                                    <div className="itemPullUp" id="itemPullUp">
                    
                                        <div className="cls" onClick={this.clsDialog.bind(this,"itemPullUp")}></div>
                                        <input type="text" onChange={this.searchItemPullUp} className="goods_item_pull_up" />
                                        <div className="itemPullUpTblWrap" id="itemPullUpNewGoodsTbl">
                                            <table>
                                                <tbody>
                                                    
                                                    <tr>
                                                        <th>Code</th><th>Item Description</th><th>Qty</th><th>Price</th><th></th>
                                                    </tr>
                                                    {
                                                        this.state.itemsTable.map(this.eachItemRow)
                                                    }
                                                </tbody>
                                            </table>
                                        </div>              
                                    </div>

                                </div>

                                <div className='form' >
                                    <div className='msg' id='goods_msg'></div>
                                    
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td><label>Floor Price</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text' className='txt_goods_floor_price' /></td>
                                            </tr>
                                            <tr>
                                                <td><label>Comments</label></td>
                                            </tr>
                                            <tr>
                                                <td><textarea className='txt_goods_comm'></textarea></td>
                                            </tr>
                                            
                                            
                                            
                                            <tr>
                                                <td><input type='button' className='btn_new_goods' onClick={this.new_goods} value='Save' /></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                </div>
                         
        );
    }
});


let TabGoodsReports = React.createClass({
    componentDidMount() {

        $.ajax({
            url:server_url+"/get_admin_user_priviledges",
            data:{"_token": cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let resx = JSON.parse(data);

                if(resx.remote_branch_access ==0){
                    $('.up_branch').hide();
                }else{
                    $('.up_branch').show();
                }

            }
        });

        $(".taxPerc").numeric();
        $(".taxPerc").numeric(false, function() { alert("Integers only"); this.value = ""; this.focus(); });
        $(".taxPerc").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
        $(".taxPerc").numeric({ decimal: false, negative: false }, function() { alert("Positive integers only"); this.value = ""; this.focus(); });
        $('.rprt_loader').show();
        $.ajax({
            url:server_url+"/goods_reports_data",
            data:{"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data); 
                let arr = [];
                let res = JSON.parse(data);                      
                $.each(res,function(index,value){
                    arr.push(res[index]);
                });
                this.setState({goodsTable:arr});
                $('.rprt_loader').fadeOut('slow');
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });


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

                    $(".txt_srch_item_admin_rprts").easyAutocomplete(options);

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

        let optional_config = {
            mode: "range",
            dateFormat: "d-m-Y"
        };

        $(".txt_srch_dates_goods_rprts").flatpickr(optional_config);

        $(".branch_reports_dates").flatpickr(optional_config);
        
    },
    fetchUpBranchData:function(id){
        let dates = $('.branch_reports_dates').val();

        if(dates !=""){
            $('.rprt_loader').show('slow')
            $.ajax({
                url:server_url+"/up_reports_check",
                data:{"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);
                    if(res.curr_branch != id){
                        $('.roll_branch').fadeOut('slow');

                        let qry = "SELECT * FROM goods WHERE up_branch='"+id+"'";
    
                        $.ajax({
                            url:"http://www.ababuapps.com/up_pos_test/custom_reports.php",
                            data:{dates:dates,dates_col:"date_received",qry_str:qry,rprt:"goods_reports","_token":cs_token},
                            type:"POST",
                            context: this,
                            success:function(data){
                                console.log(data);
                                $('.branch_reports_dates').val('');
                                let arr = [];
                                let res = JSON.parse(data);
                                                   
                                $.each(res,function(index,value){
                                    arr.push(res[index]);
                                });
                                this.setState({goodsTable:arr});
                                $('.rprt_loader').fadeOut('slow');
                            }
                        });
                    }
                }
            });

        }
    },
    deleteGoodsRow:function(id,receipt_no){
        $('.close_drawer_conf').fadeIn('slow');
        this.setState({goodsId:id});
        this.setState({goodsReceiptNo:receipt_no});
        
    },
    confDelGoodsBtn:function(){
        let id = this.state.goodsId;
        
        $.ajax({
            url:server_url+"/delete_from_goods",
            data:{id:id,"_token": cs_token},
            type:"POST",
            context: this,
            success:function(data){
                
                $('.close_drawer_conf').fadeOut('slow');

                $.ajax({
                    url:server_url+"/goods_reports_data",
                    data:{"_token":cs_token},
                    type:"POST",
                    context: this,
                    success:function(data){
                        //console.log(data); 
                        let arr = [];
                        let res = JSON.parse(data);                      
                        $.each(res,function(index,value){
                            arr.push(res[index]);
                        });
                        this.setState({goodsTable:arr});
                        $('.rprt_loader').fadeOut('slow');
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


        let qry = "UPDATE goods WHERE up_id='"+id+"' SET status='0'";

        $.ajax({
            url:"http://www.ababuapps.com/up_pos_test/customs.php",
            data:{qry_str:qry,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
            }
        });
    },
    showComment:function(id){
        $('.roll_search').fadeIn('slow');

        $.ajax({
            url:server_url+"/get_goods_comment",
            data:{id:id,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let res = JSON.parse(data);
                
                this.setState({goodsComment:res.comments});
                
            }
        });
    },  
    eachRow:function(item,i){
        if(item.up_id =="" || item.up_id ==null){
        
            if(item.status ==1){
                return(
                    <tr key={i}>
                        <td>{item.date_received}</td>
                        <td>{item.item}</td>
                        <td>{item.received_by}</td>
                        <td>{item.receipt_no}</td>
                        <td>{item.qty}</td>
                        <td>{item.cost}</td>
                        <td>{item.price}</td>
                        <td>{item.ceil_price}</td>
                        <td>{item.floor_price}</td>  
                        <td><a href='#' onClick={this.showComment.bind(this,item.id)} className='rprt_link' >Comm</a></td>           
                        <td><a href='#' onClick={this.deleteGoodsRow.bind(this,item.id,item.receipt_no)} className='del_rprt_link' >Delete</a></td> 
                                  
                    </tr>
                )
            }else{
                return(
                    <tr key={i}>
                        <td>{item.date_received}</td>
                        <td>{item.item}</td>
                        <td>{item.received_by}</td>
                        <td>{item.receipt_no}</td>
                        <td>{item.qty}</td>
                        <td>{item.cost}</td>
                        <td>{item.price}</td>
                        <td>{item.ceil_price}</td>
                        <td>{item.floor_price}</td>  
                        <td><a href='#' className='rprt_link' >Comm</a></td>           
                        <td>Deleted</td> 
                                    
                    </tr>
                )
            }

        }else{

            return(
                <tr key={i}>
                    <td>{item.date_received}</td>
                    <td>{item.item}</td>
                    <td>{item.received_by}</td>
                    <td>{item.receipt_no}</td>
                    <td>{item.qty}</td>
                    <td>{item.cost}</td>
                    <td>{item.price}</td>
                    <td>{item.ceil_price}</td>
                    <td>{item.floor_price}</td>            
                    <td>{item.status}</td>            
                </tr>
            )

        }

        
        
    },
    getInitialState:function(){
        return{
            goodsTable:[],
            goodsComment:"",
            goodsId:"",
            goodsReceiptNo:""
        }
    },
    new_tax:function(){
        let tax_title = $('.txt_tax_title').val();
        let tax_perc = $('.taxPerc').val();

        if(tax_title !="" && tax_perc !=""){

            $.ajax({
                url:server_url+"/new_tax",
                data:{tax_title:tax_title,tax_perc:tax_perc,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    let res = JSON.parse(data);
                    if(res.status==1){
                        $('.txt_tax_title').val('');
                        $('.taxPerc').val('');
                        $('.msg').html(`<div class='info'>Success: ${tax_title} Saved</div>`);
                    }else{
                        $('.msg').html(`<div class='err'>Error: ${tax_title} Already exists</div>`);
                    }
                },error: function(xhr, status, text) {

                    if(xhr.status ==419){
                        window.location = server_url;
                    }

                }
            });

        }else{
            $('.msg').html(`<div class='err'>Error: Value(s) Missing</div>`);
        }
    },
    searchGoods:function(){
        let dates = $('.txt_srch_dates_goods_rprts').val();
        
        if(dates !=""){

            $('.rprt_loader').show();

            if(dates.indexOf("to")){
                $.ajax({
                    url:server_url+"/search_goods_rprt",
                    data:{dates:dates,"_token":cs_token},
                    type:"POST",
                    context: this,
                    success:function(data){
                        console.log(data); 
                        let arr = [];
                        let res = JSON.parse(data);                      
                        $.each(res,function(index,value){
                            arr.push(res[index]);
                        });
                        this.setState({goodsTable:arr});
                        $('.rprt_loader').fadeOut('slow');
                    },error: function(xhr, status, text) {
        
                        if(xhr.status ==419){
                            window.location = server_url;
                        }
        
                    }
                });
            }
        }
    },
    clsDialog:function(i,event){
        $("."+i).fadeOut('slow');
        $('.msg').empty();
        $('.branch_inventory_reports_dates').val('');


    },
    selUpBranch:function(){
        $('.roll_branch').fadeIn('slow');
    },
    render:function(){
        return(
            <div className='ad_wraps' >
                  <div className='rprt_top_title2'>
                    {
                        /**
                         *   <input type="text"  placeholder='Item...' className="txt_srch_item_admin_rprts" />
                        */
                    }
                  
                    <input type="text"  placeholder='Dates...' className="txt_srch_dates_goods_rprts" />
                    <div className='search_btn' onClick={this.searchGoods}></div>
                    <div className='up_branch' onClick={this.selUpBranch}></div>
                  </div>
                  <div className='goodsReceivedReports'>
                    <table>
                        <thead>
                            <tr><th>Date</th><th>Item</th><th> By</th><th>Receipt</th><th>Qty</th><th>Cost</th><th>Price</th><th>Ceil</th><th>Floor</th><th></th><th></th></tr>
                                                
                        </thead>
                        <tbody>
                            {
                                this.state.goodsTable.map(this.eachRow)
                            }
                        </tbody>
                    </table>
                    <div className='roll_branch'>
                        <div className="cls_blk" onClick={this.clsDialog.bind(this,"roll_branch")}></div>
                        <div className='msg'></div>
                        <li><p>Select Dates</p></li>
                        <li><input type='text' className='branch_reports_dates' /></li>
                        <li><input type='button' value='Ngong Road Shop' onClick={this.fetchUpBranchData.bind(this,"1")} /></li>
                        <li><input type='button' value='Kiambu Road Shop' onClick={this.fetchUpBranchData.bind(this,"2")} /></li>
                        <li><input type='button' value='Vetlab Shop' onClick={this.fetchUpBranchData.bind(this,"3")} /></li>
                    </div>
                    <div className='roll_search'>
                        <div className="cls_blk" onClick={this.clsDialog.bind(this,"roll_search")}></div>
                        <li>
                            <b>Comment</b>
                        </li>
                        <li>
                            <p>{this.state.goodsComment}</p>
                        </li>
                    </div>
                </div> 

                <div className="close_drawer_conf">
                        <div className="cls" onClick={this.clsDialog.bind(this,"close_drawer_conf")}></div>
                        <li><p>Are you sure you want to permanently delete entry {this.state.goodsReceiptNo}?</p></li>
                       
                        <li><input type='button' onClick={this.confDelGoodsBtn} value='Yes' /></li>
                </div>  

                <div className='rprt_loader'><b>Loading...</b></div>
           
            </div>              
        );
    }
});



let TabGoodsTransfer = React.createClass({
    componentDidMount() {

        $(".taxPerc").numeric();
        $(".taxPerc").numeric(false, function() { alert("Integers only"); this.value = ""; this.focus(); });
        $(".taxPerc").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
        $(".taxPerc").numeric({ decimal: false, negative: false }, function() { alert("Positive integers only"); this.value = ""; this.focus(); });

        this.loadReport();

        $.ajax({
            url:server_url+"/get_active_branch_rprt_data",
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

                this.setState({optItem:arr});
                
            }
        });

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

                    $(".txt_srch_item_admin_rprts").easyAutocomplete(options);

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

        /*
        let optional_config = {
            mode: "range",
            dateFormat: "d-m-Y"
        };

        $(".txt_srch_dates_goods_rprts").flatpickr(optional_config);
        */

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

                    $(".txt_srch_dates_goods_rprts").easyAutocomplete(options);

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

    },
    loadReport(){
        $.ajax({
            url:server_url+"/goods_transfer_reports_data",
            data:{"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data); 
                let arr = [];
                let res = JSON.parse(data);                      
                $.each(res,function(index,value){
                    arr.push(res[index]);
                });
                this.setState({goodsTable:arr});

            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });
    },
    eachOption:function(item,i){
        return(
            <option key={i} value={item.id}>{item.branch}</option>
        )
    },
    transferGoods:function(id,item,qty,cost,price,ceilPrice,floorPrice,currBranch,receiptNo,itemId,branchId,actQty){
        //item.id,item.item,item.qty,item.cost,item.price
        //console.log("First Branch"+ branchId)
        $('.roll_search').fadeIn('slow');
        //alert("Here "+branchId);
        this.setState({itemId:id});
        this.setState({itemName:item});
        this.setState({itemId:itemId});
        this.setState({itemBranchId:branchId});
        //this.setState({itemQty:qty});
        this.setState({itemQty:actQty});
        this.setState({actItemQty:actQty});
        //this.setState({itemEdQty:actQty});
        this.setState({cBranch:currBranch});
        this.setState({rNo:receiptNo});
        this.setState({itemCost:cost.replace(",","")});
        this.setState({itemPrice:price.replace(",","")});
        this.setState({itemCeilPrice:ceilPrice.replace(",","")});
        this.setState({itemFloorPrice:floorPrice.replace(",","")});
        
        $.ajax({
            url:server_url+"/get_active_branch_rprt_data",
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

                this.setState({optItem:arr});
                
            }
        });

    },
    receiveGoods:function(i){
        
        $.ajax({
            url:server_url+"/receive_goods",
            data:{id:i,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);

                this.loadReport();
               
                let res = JSON.parse(data);
                let arr = [];
                                        
                $.each(res,function(index,value){
                    arr.push(res[index]);
                });

                this.setState({optItem:arr});
                
            }
        });
       
    },
    delGoods:function(i){
        
        $.ajax({
            url:server_url+"/delete_goods",
            data:{id:i,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let res = JSON.parse(data); 
                if(res.status ==1){

                    this.loadReport();
                }

                let arr = [];
                                        
                $.each(res,function(index,value){
                    arr.push(res[index]);
                });

                this.setState({optItem:arr});
                
            }
        });
        
        let qry = "UPDATE goods WHERE up_id='"+i+"' SET status='0'";

        $.ajax({
            url:"http://www.ababuapps.com/up_pos_test/customs.php",
            data:{qry_str:qry,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
            }
        });
        
    },
    eachRow:function(item,i){

        if(item.status==0){

            if(item.date_received =="" || item.date_received ==null){
                if(item.qty.indexOf("-")){
                    //Actual Receive Option Here
                    return(
                        <div className='gdsWrp' key={i}>
                        <div className='gdsTopRow'>
                            <div className='gdsDate'>
                                    <p>Date Received</p>
                                    <b>{item.date_received}</b>
                            </div>
                            <div className='gdsItem'>
                                <p>Item</p>
                                    <b>{item.item}</b>
                            </div>
                            <div className='gdsBranch'>
                                    <p>From</p>
                                    <b>{item.from_branch}</b>
                            </div>
                            <div className='gdsBranch'>
                                <p>To</p>
                                <b>{item.branch}</b>
                            </div>
                            
                            <div className='gdsChkBx'>
                               
                            </div>
                            
                        </div>
                    
                        <div className='gdsBottomRow'>
                            <div className='gdsBottomDate'>
                                    <b>{item.transfer_date}</b>
                            </div>
                            <div className='gdsUser'>
                                    <b>{item.received_by}</b>
                            </div>
                            <div className='gdsUser'>
                                    <b>{item.transfer_by}</b>
                            </div>
                    
                            <div className='gdsNos'>
                                    <b>{item.qty}</b>
                            </div>
                            <div className='gdsNos'>
                                <b>{item.cost}</b>
                            </div>
                            <div className='gdsNos'>
                                <b>{item.price}</b>
                            </div>
                            <div className='gdsNos'>
                                <b>{item.ceil_price}</b>
                            </div>
                            <div className='gdsNos'>
                                <b>{item.floor_price}</b>
                            </div>
                            <div className='gdsDelBtn'>
                                <a className='del_rprt_link' onClick={this.delGoods.bind(this,item.id)}>Delete</a>
                            </div>
                            <div className='gdsDelBtn'>
                                <a className='or_rprt_link' onClick={this.receiveGoods.bind(this,item.id)}>Receive</a>
                            </div>
                        </div>
                    </div>)
                }

            }else{

                return(
                    <div className='gdsWrp' key={i}>
                    <div className='gdsTopRow'>
                        <div className='gdsDate'>
                                <p>Date Received</p>
                                <b>{item.date_received}</b>
                        </div>
                        <div className='gdsItem'>
                            <p>Item</p>
                                <b>{item.item}</b>
                        </div>
                        <div className='gdsBranch'>
                                <p>From</p>
                                <b>{item.from_branch}</b>
                        </div>
                        <div className='gdsBranch'>
                            <p>To</p>
                            <b>{item.branch}</b>
                        </div>
                        
                        <div className='gdsChkBx'>
                           
                        </div>
                        
                    </div>
                
                    <div className='gdsBottomRow'>
                        <div className='gdsBottomDate'>
                                <b>{item.transfer_date}</b>
                        </div>
                        <div className='gdsUser'>
                                <b>{item.received_by}</b>
                        </div>
                        <div className='gdsUser'>
                                <b>{item.transfer_by}</b>
                        </div>
                
                        <div className='gdsNos'>
                                <b>{item.qty}</b>
                        </div>
                        <div className='gdsNos'>
                            <b>{item.cost}</b>
                        </div>
                        <div className='gdsNos'>
                            <b>{item.price}</b>
                        </div>
                        <div className='gdsNos'>
                            <b>{item.ceil_price}</b>
                        </div>
                        <div className='gdsNos'>
                            <b>{item.floor_price}</b>
                        </div>
                        <div className='gdsDelBtn'>
                           <b>Deleted</b>
                        </div>
                        <div className='gdsDelBtn'>
                           
                        </div>
                    </div>
                </div>)
            }
            

        }else{

            if(item.date_received =="" || item.date_received ==null){

                if(item.delivery_note_no =="" || item.delivery_note_no ==null){
                    if(item.qty.indexOf("-")){

                        return(
                            <div className='gdsWrp' key={i}>
                            <div className='gdsTopRow'>
                                <div className='gdsDate'>
                                        <p>Date Received</p>
                                        <b>{item.date_received}</b>
                                </div>
                                <div className='gdsItem'>
                                    <p>Item</p>
                                        <b>{item.item}</b>
                                </div>
                                <div className='gdsBranch'>
                                        <p>From</p>
                                        <b>{item.from_branch}</b>
                                </div>
                                <div className='gdsBranch'>
                                    <p>To</p>
                                    <b>{item.branch}</b>
                                </div>
                                
                                <div className='gdsChkBx'>
                                   
                                </div>
                                
                            </div>
                        
                            <div className='gdsBottomRow'>
                                <div className='gdsBottomDate'>
                                        <b>{item.transfer_date}</b>
                                </div>
                                <div className='gdsUser'>
                                        <b>{item.received_by}</b>
                                </div>
                                <div className='gdsUser'>
                                        <b>{item.transfer_by}</b>
                                </div>
                        
                                <div className='gdsNos'>
                                        <b>{item.qty}</b>
                                </div>
                                <div className='gdsNos'>
                                    <b>{item.cost}</b>
                                </div>
                                <div className='gdsNos'>
                                    <b>{item.price}</b>
                                </div>
                                <div className='gdsNos'>
                                    <b>{item.ceil_price}</b>
                                </div>
                                <div className='gdsNos'>
                                    <b>{item.floor_price}</b>
                                </div>
                                <div className='gdsDelBtn'>
                                    <a className='del_rprt_link' onClick={this.delGoods.bind(this,item.id)}>Delete</a>
                                </div>
                                <div className='gdsDelBtn'>
                                    <a className='or_rprt_link' onClick={this.receiveGoods.bind(this,item.id)}>Receive</a>
                                </div>
                            </div>
                        </div>)

                    }else{

                        return(
                            <div className='gdsWrp' key={i}>
                            <div className='gdsTopRow'>
                                <div className='gdsDate'>
                                        <p>Date Received</p>
                                        <b>{item.date_received}</b>
                                </div>
                                <div className='gdsItem'>
                                    <p>Item</p>
                                        <b>{item.item}</b>
                                </div>
                                <div className='gdsBranch'>
                                        <p>From</p>
                                        <b>{item.from_branch}</b>
                                </div>
                                <div className='gdsBranch'>
                                    <p>To</p>
                                    <b>{item.branch}</b>
                                </div>
                                
                                <div className='gdsChkBx'>
                                     <input type='checkbox' id={item.id} className='selDeliveryNote' />
                                </div>
                                
                            </div>
                        
                            <div className='gdsBottomRow'>
                                <div className='gdsBottomDate'>
                                        <b>{item.transfer_date}</b>
                                </div>
                                <div className='gdsUser'>
                                        <b>{item.received_by}</b>
                                </div>
                                <div className='gdsUser'>
                                        <b>{item.transfer_by}</b>
                                </div>
                        
                                <div className='gdsNos'>
                                        <b>{item.qty}</b>
                                </div>
                                <div className='gdsNos'>
                                    <b>{item.cost}</b>
                                </div>
                                <div className='gdsNos'>
                                    <b>{item.price}</b>
                                </div>
                                <div className='gdsNos'>
                                    <b>{item.ceil_price}</b>
                                </div>
                                <div className='gdsNos'>
                                    <b>{item.floor_price}</b>
                                </div>
                                <div className='gdsDelBtn'>

                                </div>
                                <div className='gdsDelBtn'>

                                </div>
                            </div>
                        </div>
                        )

                    }
                    

                }else{
                    if(item.qty.indexOf("-")){
                        return(
                            <div className='gdsWrp' key={i}>
                            <div className='gdsTopRow'>
                                <div className='gdsDate'>
                                        <p>Date Received</p>
                                        <b>{item.date_received}</b>
                                </div>
                                <div className='gdsItem'>
                                    <p>Item</p>
                                        <b>{item.item}</b>
                                </div>
                                <div className='gdsBranch'>
                                        <p>From</p>
                                        <b>{item.from_branch}</b>
                                </div>
                                <div className='gdsBranch'>
                                    <p>To</p>
                                    <b>{item.branch}</b>
                                </div>
                                
                                <div className='gdsChkBx'>
                                    
                                </div>
                                
                            </div>
                        
                            <div className='gdsBottomRow'>
                                <div className='gdsBottomDate'>
                                        <b>{item.transfer_date}</b>
                                </div>
                                <div className='gdsUser'>
                                        <b>{item.received_by}</b>
                                </div>
                                <div className='gdsUser'>
                                        <b>{item.transfer_by}</b>
                                </div>
                        
                                <div className='gdsNos'>
                                        <b>{item.qty}</b>
                                </div>
                                <div className='gdsNos'>
                                    <b>{item.cost}</b>
                                </div>
                                <div className='gdsNos'>
                                    <b>{item.price}</b>
                                </div>
                                <div className='gdsNos'>
                                    <b>{item.ceil_price}</b>
                                </div>
                                <div className='gdsNos'>
                                    <b>{item.floor_price}</b>
                                </div>
                                <div className='gdsDelBtn'>
                                    <a className='rprt_link' onClick={this.transferGoods.bind(this,item.id,item.item,item.qty,item.cost,item.price,item.ceil_price,item.floor_price,item.branch,item.receipt_no,item.item_id,item.branch_id,item.act_qty)}>Transfer</a>
                                </div>
                                <div className='gdsDelBtn'>
                                   
                                </div>
                            </div>
                        </div>
                        )
                    }else{
                        return(
                            <div className='gdsWrp' key={i}>
                            <div className='gdsTopRow'>
                                <div className='gdsDate'>
                                        <p>Date Received</p>
                                        <b>{item.date_received}</b>
                                </div>
                                <div className='gdsItem'>
                                    <p>Item</p>
                                        <b>{item.item}</b>
                                </div>
                                <div className='gdsBranch'>
                                        <p>From</p>
                                        <b>{item.from_branch}</b>
                                </div>
                                <div className='gdsBranch'>
                                    <p>To</p>
                                    <b>{item.branch}</b>
                                </div>
                                
                                <div className='gdsChkBx'>
                                    
                                </div>
                                
                            </div>
                        
                            <div className='gdsBottomRow'>
                                <div className='gdsBottomDate'>
                                        <b>{item.transfer_date}</b>
                                </div>
                                <div className='gdsUser'>
                                        <b>{item.received_by}</b>
                                </div>
                                <div className='gdsUser'>
                                        <b>{item.transfer_by}</b>
                                </div>
                        
                                <div className='gdsNos'>
                                        <b>{item.qty}</b>
                                </div>
                                <div className='gdsNos'>
                                    <b>{item.cost}</b>
                                </div>
                                <div className='gdsNos'>
                                    <b>{item.price}</b>
                                </div>
                                <div className='gdsNos'>
                                    <b>{item.ceil_price}</b>
                                </div>
                                <div className='gdsNos'>
                                    <b>{item.floor_price}</b>
                                </div>
                                <div className='gdsDelBtn'>
                                
                                </div>
                                <div className='gdsDelBtn'>

                                </div>
                            </div>
                        </div>
                        )
                    }
                    
                }
                

            }else{
                return(
                <div className='gdsWrp' key={i}>
                    <div className='gdsTopRow'>
                        <div className='gdsDate'>
                                <p>Date Received</p>
                                <b>{item.date_received}</b>
                        </div>
                        <div className='gdsItem'>
                            <p>Item</p>
                                <b>{item.item}</b>
                        </div>
                        <div className='gdsBranch'>
                                <p>From</p>
                                <b>{item.from_branch}</b>
                        </div>
                        <div className='gdsBranch'>
                            <p>To</p>
                            <b>{item.branch}</b>
                        </div>
                        
                        <div className='gdsChkBx'>
                            
                        </div>
                        
                    </div>
                
                    <div className='gdsBottomRow'>
                        <div className='gdsBottomDate'>
                                <b>{item.transfer_date}</b>
                        </div>
                        <div className='gdsUser'>
                                <b>{item.received_by}</b>
                        </div>
                        <div className='gdsUser'>
                                <b>{item.transfer_by}</b>
                        </div>
                
                        <div className='gdsNos'>
                                <b>{item.qty}</b>
                        </div>
                        <div className='gdsNos'>
                            <b>{item.cost}</b>
                        </div>
                        <div className='gdsNos'>
                            <b>{item.price}</b>
                        </div>
                        <div className='gdsNos'>
                            <b>{item.ceil_price}</b>
                        </div>
                        <div className='gdsNos'>
                            <b>{item.floor_price}</b>
                        </div>
                        <div className='gdsDelBtn'>
                            <a className='rprt_link' onClick={this.transferGoods.bind(this,item.id,item.item,item.qty,item.cost,item.price,item.ceil_price,item.floor_price,item.branch,item.receipt_no,item.item_id,item.branch_id,item.act_qty)}>Transfer</a>
                        </div>
                        <div className='gdsDelBtn'>
                        </div>
                    </div>
                </div>
                )
            }

        }
                
            
    },
    getInitialState:function(){
        return{
            goodsTable:[],
            optItem:[],
            itemId:"",
            itemName:"",
            itemQty:"",
            actItemQty:"",
            itemCost:"",
            itemPrice:"",
            itemCeilPrice:"",
            itemFloorPrice:"",
            itemEdQty:"",
            cBranch:"",
            rNo:"",
            itemBranchId:"",
            itemId:"",
        }
    },
    searchGoods:function(){
        let item = $('.txt_srch_dates_goods_rprts').val();
        
        if(item !=""){
          
                $.ajax({
                    url:server_url+"/search_goods",
                    data:{item:item,"_token":cs_token},
                    type:"POST",
                    context: this,
                    success:function(data){
                        //console.log(data); 
                        let arr = [];
                        let res = JSON.parse(data);                      
                        $.each(res,function(index,value){
                            arr.push(res[index]);
                        });
                        this.setState({goodsTable:arr});
        
                    },error: function(xhr, status, text) {
        
                        if(xhr.status ==419){
                            window.location = server_url;
                        }
        
                    }
                });
    
        }
    },
    clsDialog:function(i,event){
        $("."+i).fadeOut('slow');
        $('.msg').empty();
        $('.selTransBranch').val();
    },
    handleChangeCost: function(event) {
        this.setState({
            itemCost: event.target.value
        });
      },
      handleChangePrice: function(event) {
        this.setState({
            itemPrice: event.target.value
        });
      },
      handleChangeCeilPrice: function(event) {
        this.setState({
            itemCeilPrice: event.target.value
        });
      },
      handleChangeFloorPrice: function(event) {
        this.setState({
            itemFloorPrice: event.target.value
        });
      },
      handleQty: function(event) {
        this.setState({
            itemEdQty: event.target.value
        });
      },
      printDeliveryNote:function(){
            $.ajax({
                url:server_url+"/create_delivery_note",
                data:{"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data); 
                    this.loadReport();
                   
                },error: function(xhr, status, text) {

                    if(xhr.status ==419){
                        window.location = server_url;
                    }

                }
            });
      },
      createDeliveryNote:function(){

        var goods = "";
        $('.selDeliveryNote').each(function () {
            //var sThisVal = (this.checked ? "1" : "0");
            if(this.checked){
                var sThisVal = $(this).attr('id');
                goods += (goods=="" ? sThisVal : "," + sThisVal);
            }
            
        });

        let note_name = Math.random().toString(36).substring(10);
        
        $.ajax({
            url:server_url+"/create_delivery_note",
            data:{note_name:note_name,goods:goods,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data); 
                let resb = JSON.parse(data); 
              
                if(resb.status ==1){

                    this.loadReport();

                }
               
               
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

      },
      transferAct:function(){
        
        let branch = $('.selTransBranch').val();
            
            if(this.state.itemEdQty !="" && branch !="" && this.state.itemCost !="" && this.state.itemPrice !="" && this.state.itemCeilPrice !="" && this.state.itemFloorPrice !=""){
                
                if(this.state.actItemQty >= this.state.itemEdQty){
                    
                    if(branch != this.state.itemBranchId)
                    {
                        $.ajax({
                            url:server_url+"/transfer_goods_branch",
                            data:{item:this.state.itemId,receipt_no:this.state.rNo,c_branch:this.state.itemBranchId,qty:this.state.itemEdQty,branch:branch,cost:this.state.itemCost,price:this.state.itemPrice,ceil:this.state.itemCeilPrice,floor:this.state.itemFloorPrice,"_token":cs_token},
                            type:"POST",
                            context: this,
                            success:function(data){
                                //console.log(data); 
                               let res = JSON.parse(data); 
        
                               if(res.status ==1){
                                    $('.msg').html("<div style='font-size:12px' class='info'>Success: Goods Transfered</div>");

                                    this.loadReport();
                               }
                            },error: function(xhr, status, text) {
            
                                if(xhr.status ==419){
                                    window.location = server_url;
                                }
            
                            }
                        });

                        /*
                        let qry = "";

                        $.ajax({
                            url:"http://www.ababuapps.com/up_pos_test/customs.php",
                            data:{qry_str:qry,"_token":cs_token},
                            type:"POST",
                            context: this,
                            success:function(data){
                                console.log(data);
                               
                                
                                
                            }
                        });
                        */
                    }else{
                        $('.msg').html("<div class='err'>Error: Cannot Transfer to the same branch </div>");   
                    }
                    
                }else{
                    $('.msg').html("<div style='font-size:12px' class='err'>Error: Transfer Qty too High </div>");
                }
                
                

            }else{
                $('.msg').html("<div class='err'>Error: Value(s) Missing</div>");
            }

            

      },
    render:function(){
        return(
            <div className='ad_wraps' >
                  <div className='rprt_top_title2'>
                    
                    <input type="text"  placeholder='Enter Item ' className="txt_srch_dates_goods_rprts" />
                    <div className='search_btn' onClick={this.searchGoods}></div>
                    <a className='rprt_link' id='create_delivery_note' onClick={this.createDeliveryNote}>Create Delivery Note</a>
                  </div>
                  <div className='goodsTransferReportsHeader'>
                  <div className='gdsHeaderRow'>
                                <div className='gdsBottomDate'>
                                        <b>Transfer Date</b>
                                </div>
                                <div className='gdsUser'>
                                        <b>Received By</b>
                                </div>
                                <div className='gdsUser'>
                                        <b>Transfer By</b>
                                </div>
                           
                                <div className='gdsNos'>
                                        <b>Qty</b>
                                </div>
                                <div className='gdsNos'>
                                    <b>Cost</b>
                                </div>
                                <div className='gdsNos'>
                                    <b>Price</b>
                                </div>
                                <div className='gdsNos'>
                                    <b>Ceil</b>
                                </div>
                                <div className='gdsNos'>
                                    <b>Floor</b>
                                </div>
                                <div className='gdsDelBtn'>
                                    
                                </div>
                                <div className='gdsDelBtn'>
                                    
                                </div>
                            </div>
                  </div>

                  <div className='goodsTransferReports'>
                            {
                                this.state.goodsTable.map(this.eachRow)
                            }

                     <div className='roll_search'>
                     
                    <div className="cls_blk" onClick={this.clsDialog.bind(this,"roll_search")}></div>
                         
                        
                            <div className='msg'></div>
                            <li>Item: {this.state.itemName}</li>
                            <li>Qty: {this.state.actItemQty}</li>
                            <table>
                                <tbody>
                                    <tr>
                                        <th>Cost</th><th>Price</th>
                                    </tr>
                                    <tr>
                                        <td><input type="text" value={this.state.itemCost} onChange={this.handleChangeCost} /></td><td><input type="text" value={this.state.itemPrice} onChange={this.handleChangePrice} /></td>
                                    </tr>
                                    <tr>
                                        <th>Ceiling Price</th><th>Floor Price </th>
                                    </tr>
                                    <tr>
                                        <td><input type="text" value={this.state.itemCeilPrice} onChange={this.handleChangeCeilPrice} /></td><td><input type="text" value={this.state.itemFloorPrice} onChange={this.handleChangeFloorPrice} /></td>
                                    </tr>
                                    <tr>
                                        <th>Qty</th><th> Branch </th>
                                    </tr>
                                    <tr>
                                    <td><input type="text" value={this.state.itemEdQty} onChange={this.handleQty} /></td>
                                   
                                        <td>
                                            <select className='selTransBranch'>
                                                <option></option>
                                                {
                                                        this.state.optItem.map(this.eachOption)
                                                }
                                            </select> 
                                        </td>
                                        
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td >
                                            <input type='button' value='Transfer' id="transferBtn" onClick={this.transferAct} />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                           
                            <li></li>
                        </div>
                </div>               
            </div>              
        );
    }
});


let TabDeliveryNotes = React.createClass({
    componentDidMount() {

        $.ajax({
            url:server_url+"/delivery_notes_reports",
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

                this.setState({rowItem:arr});
            
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

        

    },
    getInitialState:function(){
        return{
            rowItem:[]
        }
    },
    eachRow:function(item,i){
        return(
            <tr key={i}>
                
                <td>{item.transfer_date}</td>
                <td>{item.transfer_by}</td>
                <td>{item.delivery_note_no}</td>
                <td>{item.dn_print_time}</td>
                <td>{item.dn_printed_by}</td>
                <td><a href={server_url+'/public/prints/'+item.delivery_note_no+".pdf"} className='rprt_link'  target='_blank'>Note</a></td>
                     
            </tr>
        )
    },
    render:function(){
        return(
            <div className='ad_wraps'>
                <div id='rprt_tax_tbl' className='reports_table_wrap'>
                <table>
                    <thead>
                        <tr><th>Transfer Date</th><th>Transfered By</th><th>Note No.</th><th>Printed At</th><th>Printed By</th><th></th></tr>
                                            
                        </thead>
                        <tbody>
                        {
                            this.state.rowItem.map(this.eachRow)
                            }
                        </tbody>
                    </table>
                </div>
            </div>
                         
        );
    }
});




let TabTransCatgItems = React.createClass({
    componentDidMount() {

        $.ajax({
            url:server_url+"/catg_trans_by_items",
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

                this.setState({rowItem:arr});
                
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

        

    },
    getInitialState:function(){
        return{
            rowItem:[]
        }
    },
    eachRow:function(item,i){
        return(
            <tr key={i}>
                <td>{item.catg}</td>
                <td>{item.total}</td>      
            </tr>
        )
    },
    render:function(){
        return(
            <div className='ad_wraps'>
                <div className='reports_table_wrap'>
                <table>
                    <thead>
                        <tr><th>Category</th><th>Amount</th></tr>
                                            
                        </thead>
                        <tbody>
                        {
                            this.state.rowItem.map(this.eachRow)
                            }
                        </tbody>
                    </table>
                </div>
            </div>
                         
        );
    }
});

let TopActiveMenu = React.createClass({
    render:function(){
        return(<div className='title_link_act'></div>)
    }
});

let Transactions = React.createClass({
    componentDidMount() {
        
        this.setState({tabVal:"dashboard"});
    },
    getInitialState:function(){
        return{
           tabVal:""
        }
    },
    tabs:function(i,events){
        this.setState({tabVal:i});
        
    },
    searchWindow:function(){
        this.setState({tabVal:"trans_reports"});
        $('.date_range_window').fadeIn('slow');
    },
    render:function(){
        let tabCont;
        let dash = "";
        let reports = "";
        let drawer = "";
        let catg = "";
        let drawings = "";
        let inv = "";
        let customers = "";
        let catgSales;
        let itemSales;
        if(this.state.tabVal =="dashboard"){
            tabCont = <TabTransDash />
            dash = <TopActiveMenu />;
            reports = "";
            drawer = "";
            catg = "";
            inv = "";
            drawings = "";
            customers = "";
            catgSales = "";
            itemSales = "";
        }else if(this.state.tabVal =="trans_reports"){
            tabCont = <TabTransReports />
            dash = "";
            reports = <TopActiveMenu />;
            drawer = "";
            catg = "";
            inv = "";
            drawings = "";
            customers = "";
            catgSales = "";
            itemSales = "";
        }else if(this.state.tabVal =="trans_cust_reports"){
            tabCont = <TabTransCustReports />
            dash = "";
            reports = "";
            drawer = "";
            catg = "";
            inv = "";
            drawings = "";
            customers = <TopActiveMenu />;
            catgSales = "";
            itemSales = "";
        }
        else if(this.state.tabVal =="trans_drawer"){
            tabCont = <TabTransDrawer />
            dash = "";
            reports = "";
            drawer = <TopActiveMenu />;
            catg = "";
            inv = "";
            drawings = "";
            customers = "";
            catgSales = "";
            itemSales = "";
        }else if(this.state.tabVal =="trans_trans_catg_items"){
            tabCont = <TabTransCatgItems />
            dash = "";
            reports = "";
            drawer = "";
            catg = <TopActiveMenu />;
            inv = "";
            drawings = "";
            customers = "";
            catgSales = "";
            itemSales = "";
        }else if(this.state.tabVal =="trans_inventory_report"){
            tabCont = <TabTransInventoryReport />
            dash = "";
            reports = "";
            drawer = "";
            catg = "";
            inv = <TopActiveMenu />;
            drawings = "";
            customers = "";
            catgSales = "";
            itemSales = "";
        }else if(this.state.tabVal =="trans_drawings"){
            tabCont = <TabTransDrawings />
            dash = "";
            reports = "";
            drawer = "";
            catg = "";
            inv = "";
            drawings = <TopActiveMenu />;
            customers = "";
            catgSales = "";
            itemSales = "";
        }else if(this.state.tabVal =="trans_item_sales"){
            tabCont = <TabItemSales />
            dash = "";
            reports = "";
            drawer = "";
            catg = "";
            inv = "";
            drawings = "";
            customers = "";
            catgSales = "";
            itemSales = <TopActiveMenu />;
        }else if(this.state.tabVal =="trans_catg_sales"){
            tabCont = <TabCatgSales />
            dash = "";
            reports = "";
            drawer = "";
            catg = "";
            inv = "";
            drawings = "";
            customers = "";
            catgSales = <TopActiveMenu />;
            itemSales = "";
        }
        
        return(
            <div>
                <div className="admin_top">
                        <div className='title_link'  onClick={this.tabs.bind(this,"dashboard")}>
                            {dash}
                            <b>Dashboard</b>
                            
                        </div>

                        <div className='title_link' onClick={this.tabs.bind(this,"trans_reports")}>
                            {reports}
                            <b>Reports</b>
                        </div>

                        <div className='title_link' onClick={this.tabs.bind(this,"trans_cust_reports")}>
                            {customers}
                            <b>Customers</b>
                        </div>

                        
                        
                        
                        {/* 
                        <div className='title_link' onClick={this.tabs.bind(this,"trans_drawer")}>
                            {drawer}
                            <b>Drawer</b>
                        
                        </div>
                        */}

                        <div className='title_link' onClick={this.tabs.bind(this,"trans_drawings")}>
                            {drawings}
                            <b>Drawings</b>
                        
                        </div>

                        {/* 
                            <div className='title_link' onClick={this.tabs.bind(this,"trans_trans_catg_items")}>
                                <div className='title_link_act'></div>
                                <b>By Category</b>  
                            </div>
                        */}
                        

                        <div className='title_link' onClick={this.tabs.bind(this,"trans_inventory_report")}>
                            {inv}
                            <b>Inventory Report</b>  
                        </div>

                        <div className='title_link' onClick={this.tabs.bind(this,"trans_item_sales")}>
                            {itemSales}
                            <b>Item Sales</b>
                        </div>

                        <div className='title_link' onClick={this.tabs.bind(this,"trans_catg_sales")}>
                            {catgSales}
                            <b>Category Sales</b>
                        </div>


                </div>
                <div className="dashboard">
                        
                        <div>{tabCont}</div>
                        
                </div>
            </div>
        );
    }
});

let Items = React.createClass({
    componentDidMount() {

        this.setState({tabVal:"item_reports"});
    },
    getInitialState:function(){
        return{
           tabVal:""
        }
    },
    tabs:function(i,events){
        this.setState({tabVal:i});
        
    },
    searchWindow:function(){
        this.setState({tabCont:"item_sales"});
        $('.date_range_window').fadeIn('slow');
    },
    render:function(){

        let tabCont;
        let actnewItem;
        let actrprts;
        let actitemCatg;
        let actsubCatg;
        let actItemUplds;
       
        
        if(this.state.tabVal =="item_new_item"){
            tabCont = <TabNewItem />;
            actnewItem = <TopActiveMenu />;
            actrprts = "";
            actitemCatg = "";
            actsubCatg = "";
            actItemUplds = "";
            
        }else if(this.state.tabVal =="item_uploads"){
            tabCont = <TabItemUploads />;
            actnewItem = "";
            actrprts = "";
            actitemCatg = "";
            actsubCatg = "";
            actItemUplds = <TopActiveMenu />;
            
        }else if(this.state.tabVal =="item_reports"){
            tabCont = <TabItemReports />;
            actnewItem = "";
            actrprts = <TopActiveMenu />;
            actitemCatg = "";
            actsubCatg = "";
            actItemUplds = "";
            
        }else if(this.state.tabVal =="item_catg"){
            tabCont = <TabItemCatg/>;
            
        }else if(this.state.tabVal =="new_item_catg"){
            tabCont = <TabNewItemCatg/>;
            actnewItem = "";
            actrprts = "";
            actitemCatg = <TopActiveMenu />;
            actsubCatg = "";
            actItemUplds = "";
            
        }else if(this.state.tabVal =="new_item_sub_catg"){
            tabCont = <TabNewItemSubCatg/>;
            actnewItem = "";
            actrprts = "";
            actitemCatg = "";
            actsubCatg = <TopActiveMenu />;
            actItemUplds = "";
            
        }

        return(
            <div>
              

                <div className="admin_top">
                    <div className='title_link' onClick={this.tabs.bind(this,"item_new_item")}>
                        {actnewItem}
                        <b>New Item</b>
                    </div>

                    <div className='title_link' onClick={this.tabs.bind(this,"item_uploads")}>
                        {actItemUplds}
                        <b>Uploads</b>
                    </div>

                    <div className='title_link' onClick={this.tabs.bind(this,"item_reports")}>
                        {actrprts}
                        <b>Reports</b>
                    </div>

                    <div className='title_link' onClick={this.tabs.bind(this,"new_item_catg")}>
                        {actitemCatg}
                        <b> Category</b>
                    </div>
                    
                    <div className='title_link' onClick={this.tabs.bind(this,"new_item_sub_catg")}>
                        {actsubCatg}
                        <b> Sub Category</b>
                    </div>
                   
                </div>
                <div className="dashboard">
                        
                        <div>{tabCont}</div>
                        
                </div>
            </div>
        );
    }
});

let TabCatgSales = React.createClass({
    componentDidMount() {

        let optional_config = {
            mode: "range",
            dateFormat: "d-m-Y"
        };

        $(".datePick").flatpickr(optional_config);


        $.ajax({
            url:server_url+"/catg_sales_report",
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

                this.setState({rowItem:arr});
               
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });
    },
    getInitialState:function(){
        return{
            rowItem:[]
        }
    },
    
    eachRow:function(item,i){
        return(
            <tr key={i}>

                <td>{item.no}</td>
                <td>{item.item}</td>
                <td>{item.total_qty}</td>
                <td>{item.total_total}</td>
                             
            </tr>
        )
    },
    clsDialog:function(i,event){
        $("."+i).fadeOut('slow');
    },
    searchItemsSales:function(){
        let dates = $('.datePick').val();

        if(dates !=""){
            if(dates.indexOf("to")){
                
                $.ajax({
                    url:server_url+"/search_catg_sales_report",
                    data:{dates:dates,"_token":cs_token},
                    type:"POST",
                    context: this,
                    success:function(data){
                        console.log(data);
                        let res = JSON.parse(data);
                        if(res.status !=0){
                            let arr = [];
                                                
                            $.each(res,function(index,value){
                                arr.push(res[index]);
                            });

                            this.setState({rowItem:arr});
                        }
                        
                    }
                });
            }
        }
        
    },
    render:function(){
        return(
            <div className='ad_wraps'>

                <div className='rprt_top_title'>
                    <input type="text" placeholder="Select Dates" className="datePick" />
                    <div className='search_btn' onClick={this.searchItemsSales}></div>

               </div>
                <div id="item_sales_rprt_tbl" className='reports_table_wrap'>
                    <table>
                    <thead>
                        <tr><th>No.</th><th>Category Title</th><th>Qty Sold</th><th>Total</th></tr>
                                            
                        </thead>
                        <tbody>
                        {
                            this.state.rowItem.map(this.eachRow)
                            }
                        </tbody>
                    </table>
                </div>
                
            </div>
                         
        );
    }
});


let TabItemSales = React.createClass({
    componentDidMount() {

        let optional_config = {
            mode: "range",
            dateFormat: "d-m-Y"
        };

        $(".datePick").flatpickr(optional_config);


        $.ajax({
            url:server_url+"/item_sales_report",
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

                this.setState({rowItem:arr});
               
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });
    },
    getInitialState:function(){
        return{
            rowItem:[]
        }
    },
    
    eachRow:function(item,i){
        return(
            <tr key={i}>

                <td>{item.no}</td>
                <td>{item.item}</td>
                <td>{item.total_qty}</td>
                <td>{item.total_total}</td>
                             
            </tr>
        )
    },
    clsDialog:function(i,event){
        $("."+i).fadeOut('slow');
    },
    searchItemsSales:function(){
        let dates = $('.datePick').val();

        if(dates !=""){
            if(dates.indexOf("to")){
                
                $.ajax({
                    url:server_url+"/filter_items_sales",
                    data:{dates:dates,"_token":cs_token},
                    type:"POST",
                    context: this,
                    success:function(data){
                        //console.log(data);
                        let res = JSON.parse(data);
                        if(res.status !=0){
                            let arr = [];
                                                
                            $.each(res,function(index,value){
                                arr.push(res[index]);
                            });

                            this.setState({rowItem:arr});
                        }
                        
                    }
                });
            }
        }
        
    },
    render:function(){
        return(
            <div className='ad_wraps'>

                <div className='rprt_top_title'>
                    <input type="text" placeholder="Select Dates" className="datePick" />
                    <div className='search_btn' onClick={this.searchItemsSales}></div>

               </div>
                <div id="item_sales_rprt_tbl" className='reports_table_wrap'>
                    <table>
                    <thead>
                        <tr><th>No.</th><th>Item Title</th><th>Qty Sold</th><th>Total</th></tr>
                                            
                        </thead>
                        <tbody>
                        {
                            this.state.rowItem.map(this.eachRow)
                            }
                        </tbody>
                    </table>
                </div>
                
            </div>
                         
        );
    }
});


let Users = React.createClass({
    componentDidMount() {

        this.setState({tabVal:"user_reports"});
    },
    getInitialState:function(){
        return{
           tabVal:""
        }
    },
    tabs:function(i,events){
        this.setState({tabVal:i});
        
    },
    render:function(){

        let tabCont;
        let actUsers;
        let actRprts;
        let actAcc;
        let actLogs;

        if(this.state.tabVal =="user_new_user"){
            tabCont = <TabNewUser />

            actUsers = <TopActiveMenu />;
            actRprts = "";
            actAcc = "";
            actLogs = "";
        }else if(this.state.tabVal =="user_reports"){
            tabCont = <TabUserReports />

            actUsers = "";
            actRprts = <TopActiveMenu />;
            actAcc = "";
            actLogs = "";
        }else if(this.state.tabVal =="user_my_acc"){
            tabCont = <TabMyAccount />

            actUsers = "";
            actRprts = "";
            actAcc = <TopActiveMenu />;
            actLogs = "";
        }else if(this.state.tabVal =="user_logs"){
            tabCont = <TabUserLogs />
            
            actUsers = "";
            actRprts = "";
            actAcc = "";
            actLogs = <TopActiveMenu />;
        }

        return(
            <div>
                <div className="admin_top">
                    <div className='title_link' onClick={this.tabs.bind(this,"user_new_user")}>
                        {actUsers}
                        <b>New User</b>
                    </div>

                    <div className='title_link' onClick={this.tabs.bind(this,"user_reports")}>
                        {actRprts}
                        <b>Reports</b>
                    </div>

                     <div className='title_link' onClick={this.tabs.bind(this,"user_logs")}>
                        {actLogs}
                        <b>User Logs</b>
                    </div>

                    <div className='title_link' onClick={this.tabs.bind(this,"user_my_acc")}>
                        {actAcc}
                        <b>My Account</b>
                    </div>
        
                </div>
                <div className="dashboard">
                        
                        <div>{tabCont}</div>
                        
                </div>
            </div>
        );
    }
});

let Taxes = React.createClass({
    componentDidMount() {

        this.setState({tabVal:"tax_reports"});
    },
    getInitialState:function(){
        return{
           tabVal:""
        }
    },
    tabs:function(i,events){
        this.setState({tabVal:i});
        
    },
    render:function(){
        let tabCont;
        let actnewTax;
        let actTaxReport;
        
        if(this.state.tabVal =="tax_new_tax"){
            tabCont = <TabNewTax />
            actnewTax = <TopActiveMenu />;
            actTaxReport = "";
        }else if(this.state.tabVal =="tax_reports"){
            actnewTax = "";
            actTaxReport = <TopActiveMenu />;
            tabCont = <TabTaxReports />
        }
        return(
            <div>
                <div className="admin_top">
                    <div className='title_link' onClick={this.tabs.bind(this,"tax_new_tax")}>
                        {actnewTax}
                        <b>New Tax</b>
                    </div>

                    <div className='title_link' onClick={this.tabs.bind(this,"tax_reports")}>
                        {actTaxReport}
                        <b>Reports</b>
                    </div>
        
                </div>
                <div className="dashboard">
                        
                        <div>{tabCont}</div>
                        
                </div>
            </div>
        );
    }
});


let Events = React.createClass({
    componentDidMount() {

        this.setState({tabVal:"tax_reports"});
    },
    getInitialState:function(){
        return{
           tabVal:""
        }
    },
    tabs:function(i,events){
        this.setState({tabVal:i});
        
    },
    render:function(){
        let tabCont;
        let actnewTax;
        let actTaxReport;
        
        if(this.state.tabVal =="tax_new_tax"){
            tabCont = <TabNewEvents />
            actnewTax = <TopActiveMenu />;
            actTaxReport = "";
        }else if(this.state.tabVal =="tax_reports"){
            actnewTax = "";
            actTaxReport = <TopActiveMenu />;
            tabCont = <TabEventsReports />
        }
        return(
            <div>
                <div className="admin_top">
                    <div className='title_link' onClick={this.tabs.bind(this,"tax_new_tax")}>
                        {actnewTax}
                        <b>New Events</b>
                    </div>

                    <div className='title_link' onClick={this.tabs.bind(this,"tax_reports")}>
                        {actTaxReport}
                        <b>Reports</b>
                    </div>
        
                </div>
                <div className="dashboard">
                        
                        <div>{tabCont}</div>
                        
                </div>
            </div>
        );
    }
});



let TabNewEvents = React.createClass({
    componentDidMount() {

        $(".taxPerc").numeric();
        $(".taxPerc").numeric(false, function() { alert("Integers only"); this.value = ""; this.focus(); });
        $(".taxPerc").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
        $(".taxPerc").numeric({ decimal: false, negative: false }, function() { alert("Positive integers only"); this.value = ""; this.focus(); });

        let optional_config = {
            dateFormat: "d-m-Y"
        };

        $(".txt_event_date").flatpickr(optional_config);

        $.ajax({
            url:server_url+"/get_active_clubs",
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

                this.setState({rowItem:arr});
            
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });


        $.ajax({
            url:server_url+"/get_active_sponsors",
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

                this.setState({optItem:arr});
            
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });
    
    },
    getInitialState:function(){
        return{
            rowItem:[],
            optItem:[]
        }
    },
    eachRow:function(item,i){
        return(
            <option key={i} value={item.id}>
                {item.club}       
            </option>
        )
    },
    eachOpt:function(item,i){
        return(
            <option key={i} value={item.id}>
                {item.org}       
            </option>
        )
    },
    newEvent:function(){
        let event = $('.txt_event_title').val();
        let e_date = $('.txt_event_date').val();
        let e_type = $('.sel_event_type').val();
        let club = $('.txt_event_club').val();
        let sponsor = $('.txt_event_sponsor').val();
        
        if(event !="" && e_type !="" && e_date !=""){

            $.ajax({
                url:server_url+"/new_event",
                data:{event:event,e_type:e_type,e_date:e_date,club:club,sponsor:sponsor,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    let res = JSON.parse(data);
                    if(res.status==1){
                        $('.txt_event_title').val('');
                    $('.txt_event_date').val('');
                    $('.sel_event_type').val('');
                     $('.txt_event_club').val('');
                    $('.txt_event_sponsor').val('');
                        $('.msg').html(`<div class='info'>Success: ${event} Saved</div>`);
                    }else{
                        $('.msg').html(`<div class='err'>Error: ${event} Already exists</div>`);
                    }
                },error: function(xhr, status, text) {

                    if(xhr.status ==419){
                        window.location = server_url;
                    }

                }
            });

        }else{
            $('.msg').html(`<div class='err'>Error: Value(s) Missing</div>`);
        }
    },
    eventOption:function(){
        let type = $('.sel_event_type').val();
        if(type !=""){
            if(type=='Club'){
                $('.hdn_club').fadeIn('slow');
                $('.hdn_sponsor').hide();
            }else if(type=='Tournament'){
                $('.hdn_club').hide();
                $('.hdn_sponsor').fadeIn('slow');
            }
        }
    },
    render:function(){
        return(
            <div className='ad_wraps' >
                                <div className='form' >
                                    <div className='msg'></div>
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td><label>Event Title</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text' className='txt_event_title' /></td>
                                            </tr>
                                            <tr>
                                                <td><label>Event Date</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text' className="txt_event_date"  /></td>
                                            </tr>
                                            <tr>
                                                <td><label>Type</label></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <select className='sel_event_type' onChange={this.eventOption}>
                                                        <option></option>
                                                        <option>Club</option>
                                                        <option>Tournament</option>
                                                    </select>
                                                </td>
                                            </tr>

                                          
                                            <tr className='hdn_club'>
                                                <td><label>Club</label></td>
                                            </tr>
                                            <tr className='hdn_club'>
                                                
                                                <td><select className='txt_event_club'>
                                                    <option></option>
                                                    {this.state.rowItem.map(this.eachRow)}
                                                </select>
                                                </td>
                                            </tr>

                                            
                                            <tr className='hdn_sponsor'>
                                                <td><label>Sponsor</label></td>
                                            </tr>
                                            <tr className='hdn_sponsor'>
                                                <td><select className='txt_event_sponsor'>
                                                    <option></option>
                                                    {this.state.optItem.map(this.eachOpt)}
                                                </select>
                                                </td>
                                            </tr>
                                            
                                            
                                            
                                            <tr>
                                                <td><input type='button' className='btn_new_event' onClick={this.newEvent} value='Save' /></td>
                                            </tr>
                                            
                                        </tbody>
                                    </table>
                                </div>
                                
                </div>
                         
        );
    }
});


let TabEventsReports = React.createClass({
    componentDidMount() {

        $.ajax({
            url:server_url+"/events_reports",
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

                this.setState({rowItem:arr});
            
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

    },
    getInitialState:function(){
        return{
            rowItem:[]
        }
    },
    archiveEvent:function(id){
        $.ajax({
            url:server_url+"/archive_events",
            data:{id:id,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                
                $.ajax({
                    url:server_url+"/events_reports",
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
        
                        this.setState({rowItem:arr});
                    
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
    },
    eachRow:function(item,i){
        if(item.status =="1"){
            return(
                <tr key={i}>
    
                    <td>{item.e_date}</td>
                    <td>{item.event}</td>
                    <td>{item.type}</td>
                    <td>{item.sponsor}</td>
                    <td>{item.club}</td>
                    <td>{item.branch}</td>
                    <td>{item.user}</td>       
                    <td><a href="#" className="del_rprt_link" onClick={this.archiveEvent.bind(this,item.id)}>Archive</a></td>       
                </tr>
            )
        }else{
            return(
                <tr key={i}>
    
                    <td>{item.e_date}</td>
                    <td>{item.event}</td>
                    <td>{item.type}</td>
                    <td>{item.sponsor}</td>
                    <td>{item.club}</td>
                    <td>{item.branch}</td>
                    <td>{item.user}</td>       
                    <td><b>Archived</b></td>       
                </tr>
            )
        }
        
    },
    searchEvents:function(){
        let event = $('.txt_search_event').val();

        if(event !=""){
            $.ajax({
                url:server_url+"/search_events_reports",
                data:{event:event,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);
                    let arr = [];
                                            
                    $.each(res,function(index,value){
                        arr.push(res[index]);
                    });
    
                    this.setState({rowItem:arr});
                
                },error: function(xhr, status, text) {
    
                    if(xhr.status ==419){
                        window.location = server_url;
                    }
    
                }
            });
        }else{

            $.ajax({
                url:server_url+"/events_reports",
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
    
                    this.setState({rowItem:arr});
                
                },error: function(xhr, status, text) {
    
                    if(xhr.status ==419){
                        window.location = server_url;
                    }
    
                }
            });
        }
    },
    render:function(){
        return(
            <div className='ad_wraps'>
                 <div className='rprt_top_title'>
                    <input type="text" placeholder="Enter Event Title" onKeyUp={this.searchEvents} className="txt_search_event" />
                    
                  
                 </div>
                <div id='rprt_events_tbl' className='reports_table_wrap'>
                <table>
                    <thead>
                        <tr><th>Date</th><th>Event</th><th>Type</th><th>Sponsor</th><th>Club</th><th>Branch</th><th>User</th><th></th></tr>
                                            
                        </thead>
                        <tbody>
                        {
                            this.state.rowItem.map(this.eachRow)
                            }
                        </tbody>
                    </table>
                </div>
            </div>
                         
        );
    }
});

let Clubs = React.createClass({
    componentDidMount() {

        this.setState({tabVal:"clubs_reports"});
    },
    getInitialState:function(){
        return{
           tabVal:""
        }
    },
    tabs:function(i,events){
        this.setState({tabVal:i});
        
    },
    render:function(){
        let tabCont;
        let actNewClub;
        let actClubReports;
        
        if(this.state.tabVal =="new_club"){
            tabCont = <TabNewClub />
            actNewClub = <TopActiveMenu />;
            actClubReports = "";
        }else if(this.state.tabVal =="clubs_reports"){
            actNewClub = "";
            actClubReports = <TopActiveMenu />;
            tabCont = <TabClubReports />
        }
        return(
            <div>
                <div className="admin_top">
                    <div className='title_link' onClick={this.tabs.bind(this,"new_club")}>
                        {actNewClub}
                        <b>New Club</b>
                    </div>

                    <div className='title_link' onClick={this.tabs.bind(this,"clubs_reports")}>
                        {actClubReports}
                        <b>Reports</b>
                    </div>
        
                </div>
                <div className="dashboard">
                        
                        <div>{tabCont}</div>
                        
                </div>
            </div>
        );
    }
});


let TabNewClub = React.createClass({
    componentDidMount() {

        $(".taxPerc").numeric();
        $(".taxPerc").numeric(false, function() { alert("Integers only"); this.value = ""; this.focus(); });
        $(".taxPerc").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
        $(".taxPerc").numeric({ decimal: false, negative: false }, function() { alert("Positive integers only"); this.value = ""; this.focus(); });

    },
    new_club:function(){

        let club = $('.txt_club_title').val();
        let loc = $('.txt_club_loc').val();
        let email = $('.txt_club_email').val();
        let phone = $('.txt_club_phone').val();
        
        

        if(club !="" && loc !="" && email !="" && phone !=""){

            $.ajax({
                url:server_url+"/new_club",
                data:{club:club,loc:loc,email:email,phone:phone,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    let res = JSON.parse(data);
                    if(res.status==1){
                        $('.txt_club_title').val('');
                        $('.txt_club_loc').val('');
                        $('.txt_club_email').val('');
                        $('.txt_club_phone').val('');

                        $('.msg').html(`<div class='info'>Success: ${club} Saved</div>`);
                    }else{
                        $('.msg').html(`<div class='err'>Error: ${club} Already exists</div>`);
                    }
                },error: function(xhr, status, text) {

                    if(xhr.status ==419){
                        window.location = server_url;
                    }

                }
            });

        }else{
            $('.msg').html(`<div class='err'>Error: Value(s) Missing</div>`);
        }
    },
    render:function(){
        return(
            <div className='ad_wraps' >
                                <div className='form' >
                                    <div className='msg'></div>
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td><label>Club Title</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text' className='txt_club_title' /></td>
                                            </tr>
                                            <tr>
                                                <td><label>Location</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text' className="txt_club_loc"  /></td>
                                            </tr>
                                            <tr>
                                                <td><label>Email</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text' className="txt_club_email"  /></td>
                                            </tr>
                                            <tr>
                                                <td><label>Phone</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text' className="txt_club_phone"  /></td>
                                            </tr>

                                            <tr>
                                                <td><input type='button' className='btn_new_club' onClick={this.new_club} value='Save' /></td>
                                            </tr>
                                            
                                        </tbody>
                                    </table>
                                </div>
                                
                </div>
                         
        );
    }
});


let TabClubReports = React.createClass({
    componentDidMount() {

        $.ajax({
            url:server_url+"/club_reports",
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

                this.setState({rowItem:arr});
            
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

    },
    getInitialState:function(){
        return{
            rowItem:[]
        }
    },
    eachRow:function(item,i){
        return(
            <tr key={i}>

                <td>{item.club}</td>
                <td>{item.location}</td>
                <td>{item.email}</td>
                <td>{item.phone}</td>
                <td>{item.status}</td>
                
                            
            </tr>
        )
    },
    render:function(){
        return(
            <div className='ad_wraps'>
                <div id='rprt_club_tbl' className='reports_table_wrap'>
                <table>
                    <thead>
                        <tr><th>Club</th><th>Location</th><th>Email</th><th>Phone</th><th>Status</th></tr>
                                            
                        </thead>
                        <tbody>
                        {
                            this.state.rowItem.map(this.eachRow)
                            }
                        </tbody>
                    </table>
                </div>
            </div>
                         
        );
    }
});

let Accounts = React.createClass({
    componentDidMount() {

        this.setState({tabVal:"accounts_reports"});
    },
    getInitialState:function(){
        return{
           tabVal:""
        }
    },
    tabs:function(i,events){
        this.setState({tabVal:i});
        
    },
    render:function(){
        let tabCont;
        let actAccounts;
        let actAccountsEntry;
        
        if(this.state.tabVal =="accounts_reports"){
            tabCont = <TabAccountsReports />
            actAccounts = <TopActiveMenu />;
            actAccountsEntry = "";
        }else if(this.state.tabVal =="accounts_entry"){
            tabCont = <TabAccountsEntry />
            actAccounts = "";
            actAccountsEntry = <TopActiveMenu />;
        }
        
        return(
            <div>
                <div className="admin_top">
                    {/**
                        <div className='title_link' onClick={this.tabs.bind(this,"accounts_entry")}>
                            {actAccountsEntry}
                            <b>Account Entry</b>
                        </div>
                    */}
                     
                    
                    <div className='title_link' onClick={this.tabs.bind(this,"accounts_reports")}>
                        {actAccounts}
                        <b>Accounts Activity</b>
                    </div>

                     
        
                </div>
                <div className="dashboard">
                        
                        <div>{tabCont}</div>
                        
                </div>
            </div>
        );
    }
});


let TabAccountsEntry = React.createClass({
    componentDidMount() {

        $(".txt_acc_qty").numeric();
        $(".txt_acc_qty").numeric(false, function() { alert("Integers only"); this.value = ""; this.focus(); });
        $(".txt_acc_qty").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
        $(".txt_acc_qty").numeric({ decimal: false, negative: false }, function() { alert("Positive integers only"); this.value = ""; this.focus(); });


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

                    $(".txt_acc_item").easyAutocomplete(options);

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

        $.ajax({
            url:server_url+"/get_active_customers",
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

                this.setState({rowItem:arr});
            
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

        $.ajax({
            url:server_url+"/get_active_events",
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

                this.setState({optItem:arr});
            
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

    },
    getInitialState:function(){
        return{
            rowItem:[],
            optItem:[]
        }
    },
    eachRow:function(item,i){
        return(
            <option key={i} value={item.id}>
                {item.event}       
            </option>
        )
    },
    optRow:function(item,i){
        return(
            <option key={i} value={item.id}>
                {item.f_name + ' '+item.s_name + ' '+item.org}       
            </option>
        )
    },
    newAccEntry:function(){
        let cust = $('.sel_acc_cust').val();
        let item = $('.txt_acc_item').val();
        let event = $('.sel_acc_event').val();
        let qty = $('.txt_acc_qty').val();

        if(cust !="" && item !="" && event !="" && qty !=""){

            $.ajax({
                url:server_url+"/new_acc_entry",
                data:{cust:cust,item:item,event:event,qty:qty,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    let res = JSON.parse(data);
                    if(res.status==1){

                        $('.sel_acc_cust').val('');
                        $('.txt_acc_item').val('');
                        $('.sel_acc_event').val('');
                        $('.txt_acc_qty').val('');

                        $('.msg').html(`<div class='info'>Success: ${item} Saved</div>`);
                    }else{
                        $('.msg').html(`<div class='err'>Error: ${item} Already exists</div>`);
                    }
                },error: function(xhr, status, text) {

                    if(xhr.status ==419){
                        window.location = server_url;
                    }

                }
            });

        }else{
            $('.msg').html(`<div class='err'>Error: Value(s) Missing</div>`);
        }
    },
    render:function(){
        return(
            <div className='ad_wraps' >
                                <div className='form' >
                                    <div className='msg'></div>
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td><label>Customer</label></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <select className='sel_acc_cust'>
                                                        <option></option>
                                                        {this.state.rowItem.map(this.optRow)}

                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label>Item</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text' className="txt_acc_item"  /></td>
                                            </tr>
                                            
                                            <tr>
                                                <td><label>Event</label></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <select className='sel_acc_event'>
                                                        <option></option>
                                                        {this.state.optItem.map(this.eachRow)}

                                                    </select>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td><label>Qty</label></td>
                                            </tr>
                                            <tr>
                                                <td><input type='text' className="txt_acc_qty"  /></td>
                                            </tr>
                                            
                                            <tr>
                                                <td><input type='button' className='btn_new_tax' onClick={this.newAccEntry} value='Save' /></td>
                                            </tr>
                                            
                                        </tbody>
                                    </table>
                                </div>
                                
                </div>
                         
        );
    }
});


let TabAccountsReports = React.createClass({
    componentDidMount() {

        $.ajax({
            url:server_url+"/get_admin_user_priviledges",
            data:{"_token": cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let resx = JSON.parse(data);

                if(resx.remote_branch_access ==0){
                    $('.up_branch').hide();
                }else{
                    $('.up_branch').show();
                }

            }
        });

        let optional_config = {
            mode: "range",
            dateFormat: "d-m-Y"
        };

        $(".detDatePick").flatpickr(optional_config);

        $(".branch_acc_act_reports_dates").flatpickr(optional_config);

        this.loadReport();

        $('.rprt_loader').show();
        
        $.ajax({
            url:server_url+"/search_auto_comp_customers",
            data:{"_token":cs_token},
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

                    $(".txt_acc_cust").easyAutocomplete(options);

                   
                }
                

            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

        $.ajax({
            url:server_url+"/get_active_events",
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

                this.setState({optItem:arr});
            
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

        $.ajax({
            url:server_url+"/get_active_customers",
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

                this.setState({optCustItem:arr});
            
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

    },
    getInitialState:function(){
        return{
            rowItem:[],
            optItem:[],
            optCustItem:[]
        }
    },
    eachOpt:function(item,i){
        return(
            <option key={i} value={item.id}>
                {item.event}       
            </option>
        )
    },
    eachCustOpt:function(item,i){
        return(
            <option key={i} value={item.id}>
                {item.f_name + ' ' + item.s_name + ' - ' + item.org}       
            </option>
        )
    },
    loadReport(){
        $.ajax({
            url:server_url+"/acc_entry_rprts",
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

                this.setState({rowItem:arr});

                $('.rprt_loader').fadeOut('slow');
            
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });
    },
    delEntry:function(i){

        $.ajax({
            url:server_url+"/del_acc_entry",
            data:{id:i,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let res = JSON.parse(data);
                if(res.status ==1){
                    this.loadReport();
                }
            
            },error: function(xhr, status, text) {

                if(xhr.status ==419){
                    window.location = server_url;
                }

            }
        });

    },
    eachRow:function(item,i){
        if(item.status ==1){
            return(
                <tr key={i}>
                    <td>{item.acc_date}</td>
                    <td>{item.event}</td>
                    <td>{item.e_date}</td>
                    <td>{item.item}</td>
                    <td>{item.customer}</td>
                    <td>{item.qty}</td>
                    <td>{item.ttl_qty}</td>
                    <td>{item.branch}</td>
                    {/**
                        <td><a className='del_rprt_link' onClick={this.delEntry.bind(this,item.id)}>Delete</a></td>
                     
                    */}
                               
                </tr>
            )
        }else{
            return(
                <tr key={i}>
                    <td>{item.acc_date}</td>
                    <td>{item.item}</td>
                    <td>{item.event}</td>
                    <td>{item.e_date}</td>
                    <td>{item.customer}</td>
                    <td>{item.qty}</td>
                    <td>{item.ttl_qty}</td>
                    <td>{item.branch}</td>
                    {/**
                    <td>Deleted</td>
                     */}          
                </tr>
            )
        }
        
    },
    searchAcc:function(){
        let cust = $('.txt_acc_cust').val();

        if(cust !=""){
            $.ajax({
                url:server_url+"/search_acc_rprts",
                data:{cust:cust,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    console.log(data);
                    let res = JSON.parse(data);
                    let arr = [];
                                            
                    $.each(res,function(index,value){
                        arr.push(res[index]);
                    });
    
                    this.setState({rowItem:arr});
                
                },error: function(xhr, status, text) {
    
                    if(xhr.status ==419){
                        window.location = server_url;
                    }
    
                }
            });
        }
    },
    clsDialog:function(i,event){
        $("."+i).fadeOut('slow');
    },
    detailedAccSearch:function(){
        $('.roll_search').fadeIn('slow');

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

                    $(".txt_det_acc_cust").easyAutocomplete(options);

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
    },
    detAccSearchBtnAct:function(){
        let dates = $('.detDatePick').val();
        let customer = $('.txt_det_acc_cust').val();
        let event = $('.sel_acc_event').val();

        if(dates !=""){
            $.ajax({
                url:server_url+"/det_search_acc_rprts",
                data:{dates:dates,customer:customer,event:event,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);
                    let arr = [];
                    $('.roll_search').fadeOut('slow');    
                    $.each(res,function(index,value){
                        arr.push(res[index]);
                    });
    
                    this.setState({rowItem:arr});
                
                },error: function(xhr, status, text) {
    
                    if(xhr.status ==419){
                        window.location = server_url;
                    }
    
                }
            });
        }else{
            $('.msg').html("<div class='err'>Error: Dates not selected</div>")
        }
        
        
    },
    selUpBranch:function(){
        $('.roll_branch').fadeIn('slow');
    },
    fetchUpBranchData:function(id){
        let dates = $('.branch_acc_act_reports_dates').val();

        if(dates !=""){

            $.ajax({
                url:server_url+"/up_reports_check",
                data:{"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);
    
                    if(res.curr_branch != id){
                        $('.roll_branch').fadeOut('slow');
                        console.log(data);
                        //let qry = "SELECT * FROM accounts WHERE up_branch='"+id+"'";
                        let qry = "SELECT * FROM accounts WHERE up_branch='"+id+"'";
                        $.ajax({
                            url:"http://www.ababuapps.com/up_pos_test/custom_reports.php",
                            data:{dates:dates,dates_col:"acc_date",qry_str:qry,rprt:"accounts","_token":cs_token},
                            type:"POST",
                            context: this,
                            success:function(data){
                                $('.branch_acc_act_reports_dates').val('');
                                console.log(data);
                                let res = JSON.parse(data);
                                let arr = [];
                                                        
                                $.each(res,function(index,value){
                                    arr.push(res[index]);
                                });
                
                                this.setState({rowItem:arr});
                            }
                        });
    
                    }
    
                }
            });  
        }
    },
    render:function(){
        return(
            <div className='ad_wraps'>

                 <div className='rprt_top_title'>
                    
                    <input type="text" className="txt_acc_cust" placeholder="Type Customer Name" />
                    <div className='search_btn' onClick={this.searchAcc}></div>
                  
                    <div className='detailed_search' onClick={this.detailedAccSearch}></div>
                    <div className='up_branch' onClick={this.selUpBranch}></div>


               </div>

                <div id='rprt_acc_tbl' className='reports_table_wrap'>
                <table>
                    <thead>
                        <tr><th>Date</th><th>Event</th><th>Event Date</th><th>Item</th><th>Customer</th><th>Qty</th><th>Total Qty</th><th>Branch</th><th></th></tr>
                                            
                        </thead>
                        <tbody>
                            {
                                this.state.rowItem.map(this.eachRow)
                            }
                        </tbody>
                    </table>
                </div>

                <div className='roll_search'>
                        <div className="cls_blk" onClick={this.clsDialog.bind(this,"roll_search")}></div>
                            <div className='msg'></div>
                            <li>Dates</li>
                            <li><input className='detDatePick' type='text' /></li>
                            <li>Customer</li>
                            <li><input className='txt_det_acc_cust' type='text' /></li>
                            <li>Event</li>
                            <li>
                           
                            <select className='sel_acc_event'>
                                <option></option>
                                {this.state.optItem.map(this.eachOpt)}

                            </select>
                                               
                            </li>
                          
                            <li><input type='button' value='Search' onClick={this.detAccSearchBtnAct}/></li>
                        </div>

                        <div className='rprt_loader'>Loading...</div>

                        <div className='roll_branch'>
                            <div className="cls_blk" onClick={this.clsDialog.bind(this,"roll_branch")}></div>
                                <div className='msg'></div>
                                        <li><p>Select Dates</p></li>
                                        <li><input type='text' className='branch_acc_act_reports_dates' /></li>
                                        <li><input type='button' value='Ngong Road Shop' onClick={this.fetchUpBranchData.bind(this,"1")} /></li>
                                        <li><input type='button' value='Kiambu Road Shop' onClick={this.fetchUpBranchData.bind(this,"2")} /></li>
                                        <li><input type='button' value='Vetlab Shop' onClick={this.fetchUpBranchData.bind(this,"3")} /></li>
                        </div>
            </div>
                         
        );
    }
});


let Customers = React.createClass({
    componentDidMount() {

        this.setState({tabVal:"cust_reports"});
    },
    getInitialState:function(){
        return{
           tabVal:""
        }
    },
    tabs:function(i,events){
        this.setState({tabVal:i});
        
    },
    render:function(){
        let tabCont;
        let actnewCust;
        let actCustReport;
        
        if(this.state.tabVal =="cust_new_cust"){
            tabCont = <TabNewCustomers />
            actnewCust = <TopActiveMenu />;
            actCustReport = "";
        }else if(this.state.tabVal =="cust_reports"){
            actnewCust = "";
            actCustReport = <TopActiveMenu />;
            tabCont = <TabCustomersReports />
        }
        return(
            <div>
                <div className="admin_top">
                    <div className='title_link' onClick={this.tabs.bind(this,"cust_new_cust")}>
                        {actnewCust}
                        <b>New Customers</b>
                    </div>

                    <div className='title_link' onClick={this.tabs.bind(this,"cust_reports")}>
                        {actCustReport}
                        <b>Reports</b>
                    </div>
        
                </div>
                <div className="dashboard">
                        
                        <div>{tabCont}</div>
                        
                </div>
            </div>
        );
    }
});


let Tournaments = React.createClass({
    componentDidMount() {

        this.setState({tabVal:"tourn_reports"});
    },
    getInitialState:function(){
        return{
           tabVal:""
        }
    },
    tabs:function(i,events){
        this.setState({tabVal:i});
        
    },
    render:function(){
        let tabCont;
        let actnewTourn;
        let actTournReport;
        
        if(this.state.tabVal =="new_tourn"){
            tabCont = <TabNewTournaments />
            actnewTourn = <TopActiveMenu />;
            actTournReport = "";
        }else if(this.state.tabVal =="tourn_reports"){
            actnewTourn = "";
            actTournReport = <TopActiveMenu />;
            tabCont = <TabTournamentReports />
        }
        return(
            <div>
                <div className="admin_top">
                    <div className='title_link' onClick={this.tabs.bind(this,"new_tourn")}>
                        {actnewTourn}
                        <b>New Tournament</b>
                    </div>

                    <div className='title_link' onClick={this.tabs.bind(this,"tourn_reports")}>
                        {actTournReport}
                        <b>Reports</b>
                    </div>
        
                </div>
                <div className="dashboard">
                        
                        <div>{tabCont}</div>
                        
                </div>
            </div>
        );
    }
});


let Goods = React.createClass({
    componentDidMount() {

        this.setState({tabVal:"goods_reports"});
    },
    getInitialState:function(){
        return{
           tabVal:""
        }
    },
    tabs:function(i,events){
        this.setState({tabVal:i});
        
    },
    render:function(){
        let tabCont;
        let actRGoods;
        let actGoodsRprts;
        let actGoodsTrans;
        let actNewGoodsTrans;
        let actGoodsUploads;
        if(this.state.tabVal =="goods_received"){
            tabCont = <TabGoodsReceive />
            actRGoods = <TopActiveMenu />;
            actGoodsRprts = "";
            actGoodsTrans = "";
            actNewGoodsTrans = "";
            actGoodsUploads = "";
        }else if(this.state.tabVal =="goods_reports"){
            tabCont = <TabGoodsReports />
            actRGoods = "";
            actGoodsRprts = <TopActiveMenu />;
            actGoodsTrans = "";
            actNewGoodsTrans = "";
            actGoodsUploads = "";
        }else if(this.state.tabVal =="goods_transfer"){
            tabCont = <TabGoodsTransfer />;
            actRGoods = "";
            actGoodsRprts = "";
            actGoodsTrans = <TopActiveMenu />;
            actNewGoodsTrans = ""; 
            actGoodsUploads = "";
        }else if(this.state.tabVal=="delivery_note"){
            tabCont = <TabDeliveryNotes />
            actRGoods = "";
            actGoodsRprts = "";
            actGoodsTrans = "";
            actGoodsUploads = "";
            actNewGoodsTrans = <TopActiveMenu />;
        }else if(this.state.tabVal=="uploads"){
            tabCont = <TabGoodsUploads />
            actRGoods = "";
            actGoodsRprts = "";
            actGoodsTrans = "";
            actGoodsUploads = <TopActiveMenu />;
            actNewGoodsTrans = "";
        }
        return(
            <div>
                <div className="admin_top">
                    <div className='title_link' onClick={this.tabs.bind(this,"goods_received")}>
                        {actRGoods}
                        <b>Receive Goods</b>
                    </div>

                    <div className='title_link' onClick={this.tabs.bind(this,"uploads")}>
                        {actGoodsUploads}
                        <b>Uploads</b>
                    </div>

                    <div className='title_link' onClick={this.tabs.bind(this,"goods_reports")}>
                        {actGoodsRprts}
                        <b>Reports</b>
                    </div>

                    <div className='title_link' onClick={this.tabs.bind(this,"goods_transfer")}>
                        {actGoodsTrans}
                        <b>Transfer </b>
                    </div>
                    
                    <div className='title_link' onClick={this.tabs.bind(this,"delivery_note")}>
                        {actNewGoodsTrans}
                        <b>Delivery Notes</b>
                    </div>

                    
           
                </div>
                <div className="dashboard">
                        
                        <div>{tabCont}</div>
                        
                </div>
            </div>
        );
    }
});



let Tills = React.createClass({
    componentDidMount() {

        this.setState({tabVal:"tax_reports"});
    },
    getInitialState:function(){
        return{
           tabVal:""
        }
    },
    tabs:function(i,events){
        this.setState({tabVal:i});
        
    },
    render:function(){
        let tabCont;
        if(this.state.tabVal =="tax_new_tax"){
            tabCont = <TabNewTax />
        }else if(this.state.tabVal =="tax_reports"){
            tabCont = <TabTaxReports />
        }
        return(
            <div>
                <div className="admin_top">
                    <div className='title_link' onClick={this.tabs.bind(this,"tax_new_tax")}>
                        <div className='title_link_act'></div>
                        <b>New Tax</b>
                    </div>

                    <div className='title_link' onClick={this.tabs.bind(this,"tax_reports")}>
                        <div className='title_link_act'></div>
                        <b>Reports</b>
                    </div>
        
                </div>
                <div className="dashboard">
                        
                        <div>{tabCont}</div>
                        
                </div>
            </div>
        );
    }
});

let Suppliers = React.createClass({
    componentDidMount() {

        this.setState({tabVal:"tax_reports"});
    },
    getInitialState:function(){
        return{
           tabVal:""
        }
    },
    tabs:function(i,events){
        this.setState({tabVal:i});
        
    },
    render:function(){
        let tabCont;
        if(this.state.tabVal =="tax_new_tax"){
            tabCont = <TabNewSupplier />
        }else if(this.state.tabVal =="tax_reports"){
            tabCont = <TabSupplierReports />
        }
        return(
            <div>
                <div className="admin_top">
                    <div className='title_link' onClick={this.tabs.bind(this,"tax_new_tax")}>
                        <div className='title_link_act'></div>
                        <b>New Tax</b>
                    </div>

                    <div className='title_link' onClick={this.tabs.bind(this,"tax_reports")}>
                        <div className='title_link_act'></div>
                        <b>Reports</b>
                    </div>
        
                </div>
                <div className="dashboard">
                        
                        <div>{tabCont}</div>
                        
                </div>
            </div>
        );
    }
});

let ActiveMenu = React.createClass({
    render:function(){
        return(<div className="menu_active"></div>)                
    }
});

let Admin = React.createClass({
    componentDidMount() {

        this.setState({menuItemVal:"transactions"});
    },
    getInitialState:function(){
        return{
           menuItemVal:""
        }
    },
    menuItem:function(i,event){

        this.setState({menuItemVal:i});
        
    },
    cashLink:function(){
        $('#admin').hide();
        $('#teller').fadeIn('slow');
        $('.search_txt').focus();
        $('.drawer_msg').empty();
        $('.msg').empty();
    },
    poolLink:function(){

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

        $('#admin').hide();
        $('#pool').fadeIn();
    },
    render:function(){

        let menu = this.state.menuItemVal;
        let comp;
        let act_trans;
        let act_items;
        let act_users;
        let act_taxes;
        let act_goods;
        let act_branches;
        let act_customers;
        let act_tournaments;
        let act_events;
        let act_accounts;
        let act_clubs;

        if(menu=="transactions"){
            comp = <Transactions  />;
            act_trans = <ActiveMenu  />;
            act_items = "";
            act_users = "";
            act_taxes = "";
            act_goods = "";
            act_branches = "";
            act_customers = "";
            act_tournaments = "";
            act_events = "";
            act_accounts = "";
            act_clubs = "";
        }else if(menu =="items"){
            comp = <Items  />;
            act_trans = "";
            act_items = <ActiveMenu  />;
            act_users = "";
            act_taxes = "";
            act_goods = "";
            act_branches = "";
            act_customers = "";
            act_tournaments = "";
            act_events = "";
            act_accounts = "";
            act_clubs = "";
        }else if(menu =="users"){
            comp = <Users  />;
            act_trans = "";
            act_items = "";
            act_users = <ActiveMenu  />;
            act_taxes = "";
            act_goods = "";
            act_branches = "";
            act_customers = "";
            act_tournaments = "";
            act_events = "";
            act_accounts = "";
            act_clubs = "";
        }else if(menu =="taxes"){
            comp = <Taxes  />;
            act_trans = "";
            act_items = "";
            act_users = "";
            act_taxes = <ActiveMenu  />;
            act_goods = "";
            act_branches = "";
            act_customers = "";
            act_tournaments = "";
            act_events = "";
            act_accounts = "";
            act_clubs = "";
        }else if(menu =="tills"){
            comp = <Tills />;
        }else if(menu =="goods"){
            comp = <Goods />;
            act_trans = "";
            act_items = "";
            act_users = "";
            act_taxes = "";
            act_goods = <ActiveMenu  />;
            act_branches = "";
            act_customers = "";
            act_tournaments = "";
            act_events = "";
            act_accounts = "";
            act_clubs = "";
        }else if(menu =="branches"){
            comp = <Branches />;
            act_trans = "";
            act_items = "";
            act_users = "";
            act_taxes = "";
            act_goods = "";
            act_customers = "";
            act_tournaments = "";
            act_branches = <ActiveMenu  />;
            act_events = "";
            act_accounts = "";
            act_clubs = "";
        }else if(menu =="customers"){
            comp = <Customers />;
            act_trans = "";
            act_items = "";
            act_users = "";
            act_taxes = "";
            act_goods = "";
            act_customers = <ActiveMenu  />;
            act_tournaments = "";
            act_branches = "";
            act_events = "";
            act_accounts = "";
            act_clubs = "";
        }else if(menu =="tournaments"){
            comp = <Tournaments />;
            act_trans = "";
            act_items = "";
            act_users = "";
            act_taxes = "";
            act_goods = "";
            act_customers = "";
            act_tournaments = <ActiveMenu  />;
            act_branches = "";
            act_events = "";
            act_accounts = "";
            act_clubs = "";
        }
        else if(menu =="events"){
            comp = <Events />;
            act_trans = "";
            act_items = "";
            act_users = "";
            act_taxes = "";
            act_goods = "";
            act_customers = "";
            act_tournaments = "";
            act_branches = "";
            act_events = <ActiveMenu  />;
            act_accounts = "";
            act_clubs = "";
        }else if(menu =="accounts"){
            comp = <Accounts />;
            act_trans = "";
            act_items = "";
            act_users = "";
            act_taxes = "";
            act_goods = "";
            act_customers = "";
            act_tournaments = "";
            act_branches = "";
            act_events = "";
            act_accounts = <ActiveMenu  />;
            act_clubs = "";
        }else if(menu =="clubs"){
            comp = <Clubs />;
            act_trans = "";
            act_items = "";
            act_users = "";
            act_taxes = "";
            act_goods = "";
            act_customers = "";
            act_tournaments = "";
            act_branches = "";
            act_events = "";
            act_accounts = "";
            act_clubs = <ActiveMenu  />;
        }

        return(<div className="admin_back">
                <div className="menu">
                        <div className="cashier_link" onClick={this.cashLink}>
                                <b>Cashier</b>
                        </div>

                        <div className="menu_wrapper">
                             
                                <div className="menu_item" onClick={this.menuItem.bind(this,"transactions")}>
                                    
                                    <div className="menu_icon">
                                        <img src="../img/tiles_txns.png" />
                                    </div>
                                    <div className="menu_txt">
                                        <b>Sales</b>
                                    </div>
                                    {act_trans}
                                </div>

                                <div className="menu_item" id="items_menu" onClick={this.menuItem.bind(this,"items")}>
                                    
                                    <div className="menu_icon">
                                        <img src="../img/tiles_items.png" />
                                    </div>
                                    <div className="menu_txt">
                                        <b>Items</b>
                                    </div>
                                    {act_items}
                                </div>

                                <div className="menu_item" id="users_menu" onClick={this.menuItem.bind(this,"users")}>
                                    
                                    <div className="menu_icon">
                                        <img src="../img/tiles_users.png" />
                                    </div>
                                    <div className="menu_txt">
                                        <b>Users</b>
                                    </div>
                                    {act_users}
                                </div>

                                <div className="menu_item" id="taxes_menu" onClick={this.menuItem.bind(this,"taxes")}>
                                    
                                    <div className="menu_icon">
                                        <img src="../img/tax.png" />
                                    </div>
                                    <div className="menu_txt">
                                        <b>Taxes</b>
                                    </div>
                                {act_taxes}
                                </div>

                                <div className="menu_item" id="goods_menu" onClick={this.menuItem.bind(this,"goods")}>
                                    <div className="menu_icon">
                                        <img src="../img/po.png" />
                                    </div>

                                    <div className="menu_txt">
                                        <b>Inventory</b>
                                    </div>
                                {act_goods}
                                </div>
                                
                                {/*
                                    <div className="menu_item" onClick={this.menuItem.bind(this,"tills")}>
                                    
                                    <div className="menu_icon">
                                        <img src="../img/tiles_tills.png" />
                                    </div>
                                    <div className="menu_txt">
                                        <b>Tills</b>
                                    </div>
                                </div>
                                */}
                        

                                <div className="menu_item" id="branches_menu" onClick={this.menuItem.bind(this,"branches")}>
                                    
                                    <div className="menu_icon">
                                        <img src="../img/branch.png" />
                                    </div>
                                    <div className="menu_txt">
                                        <b>Branches</b>
                                    </div>
                                    {act_branches}
                                </div>

                                <div className="menu_item"  id="customers_menu" onClick={this.menuItem.bind(this,"customers")}>
                                    
                                
                                    <div className="menu_txt">
                                        <b>Customers</b>
                                    </div>
                                    {act_customers}
                                </div>
                                
                                {
                                    /**
                                     *  
                                     * 
                                     * <div className="menu_item" onClick={this.menuItem.bind(this,"tournaments")}>
                                    
                                    <div className="menu_txt">
                                        <b>Tournaments</b>
                                    </div>
                                    {act_tournaments}
                                </div>
                                    */
                                }

                                <div className="menu_item" id="clubs_menu" onClick={this.menuItem.bind(this,"clubs")}>
                                <div className="menu_icon">
                                        <img src="../img/clubs.png" />
                                    </div>
                                    <div className="menu_txt">
                                        <b>Clubs</b>
                                    </div>
                                    {act_clubs}
                                </div>

                                <div className="menu_item" id="events_menu" onClick={this.menuItem.bind(this,"events")}>
                                <div className="menu_icon">
                                        <img src="../img/events.png" />
                                    </div>
                                    <div className="menu_txt">
                                        <b>Events</b>
                                    </div>
                                    {act_events}
                                </div>

                                <div className="menu_item" id="accounts_menu" onClick={this.menuItem.bind(this,"accounts")}>
                                <div className="menu_icon">
                                        <img src="../img/accounts.png" />
                                    </div>
                                    <div className="menu_txt">
                                        <b>Accounts</b>
                                    </div>
                                    {act_accounts}
                                </div>
                        </div>


                        <div className="pool_link" id="pool_menu" onClick={this.poolLink}>
                                <b>Ball Pool</b>
                        </div>
                        

                </div>

                
                { comp }
                 
                
            </div>)
    }
});



ReactDOM.render(
       <Admin />
,document.getElementById('admin'));

