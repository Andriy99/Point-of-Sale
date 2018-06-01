
let Activities = React.createClass({
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

        this.setState({tabVal:"dashboard"});

        $('.rprt_loader').fadeIn('slow');

        $.ajax({
            url:server_url+"/pool_activities",
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

        let optional_config = {
            mode: "range",
            dateFormat: "d-m-Y"
        };

        $(".branch_reports_dates").flatpickr(optional_config);
        
        $(".det_activity_dates").flatpickr(optional_config);
    },
    confDelActivityBtn:function(){
        let id = this.state.activityId;
        let type = this.state.activityType;

        $('.rprt_loader').fadeIn('slow');

        if(type=="Purchase"){

            $.ajax({
                url:server_url+"/delete_activity_purchase",
                data:{id:id,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);

                    $('.close_drawer_conf').fadeOut('slow');
                    
                        let qry = "UPDATE pool SET status='0' WHERE up_id='"+id+"' ";

                        $.ajax({
                            url:"http://www.ababuapps.com/up_pos/customs.php",
                            data:{qry_str:qry,"_token":cs_token},
                            type:"POST",
                            context: this,
                            success:function(data){
                                //console.log(data);
                            }
                        });
    
                        let scnd_qry = "UPDATE shopping_cart SET type='tender' WHERE up_id='"+res.sc_id+"'";
    
                        $.ajax({
                            url:"http://www.ababuapps.com/up_pos/customs.php",
                            data:{qry_str:scnd_qry,"_token":cs_token},
                            type:"POST",
                            context: this,
                            success:function(data){
                                //console.log(data);
                            }
                        });
    
                        $.ajax({
                            url:server_url+"/pool_activities",
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
                    
                }
            });

        }else if(type=="Allocation"){

            $.ajax({
                url:server_url+"/delete_activity_allocation",
                data:{id:id,"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);

                    $('.close_drawer_conf').fadeOut('slow');
         
                        let qry = "UPDATE pool SET status='0' WHERE up_id='"+id+"' ";
                        console.log(qry);
                        $.ajax({
                            url:"http://www.ababuapps.com/up_pos/customs.php",
                            data:{qry_str:qry,"_token":cs_token},
                            type:"POST",
                            context: this,
                            success:function(data){
                                //console.log(data);
                            }
                        });
                        
                        /**
                         * 
                         *  $acc = Accounts::select('id','status')->where('customer',$pool->customer)->where('item',$pool->item)->where('event',$pool->event)->get();
            
                         */
                        let scnd_qry = "UPDATE accounts SET status='0' WHERE customer='"+res.customer+"' AND item='"+res.item+"' AND event='"+res.event+"'";
                        console.log(scnd_qry);
                        $.ajax({
                            url:"http://www.ababuapps.com/up_pos/customs.php",
                            data:{qry_str:scnd_qry,"_token":cs_token},
                            type:"POST",
                            context: this,
                            success:function(data){
                                //console.log(data);
                            }
                        });
    
                        $.ajax({
                            url:server_url+"/pool_activities",
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

                    
                }
            });

        }  

    },
    deleteActivity:function(type,id){

        $('.close_drawer_conf').fadeIn('slow');

        this.setState({activityId:id});
        this.setState({activityType:type});
        
    },
    eachRow:function(item,i){
        if(item.status ==1){
            if(item.type =="Allocation"){

                return(
                    <tr key={i}>
                        <td>{item.p_time}</td>
                        <td>{item.item}</td>
                        <td>{item.customer}</td>
                        <td>{item.qty}</td>
                        <td>{item.branch}</td>
                        <td>{item.user}</td>
                        <td>{item.type}</td>
                        <td><a href="#" onClick={this.deleteActivity.bind(this,item.type,item.id)} className="del_rprt_link" >Delete</a></td>
                    </tr>
                )
            }else if(item.type =="Purchase"){

                return(
                    <tr key={i}>
                        <td>{item.p_time}</td>
                        <td>{item.item}</td>
                        <td>{item.customer}</td>
                        <td>{item.qty}</td>
                        <td>{item.branch}</td>
                        <td>{item.user}</td>
                        <td>{item.type}</td>
                        <td><a href="#" onClick={this.deleteActivity.bind(this,item.type,item.id)} className="del_rprt_link" >Delete</a></td>
                    </tr>
                )
            }else if(item.type =="Collection"){

                return(
                    <tr key={i}>
                        <td>{item.p_time}</td>
                        <td>{item.item}</td>
                        <td>{item.customer}</td>
                        <td>{item.qty}</td>
                        <td>{item.branch}</td>
                        <td>{item.user}</td>
                        <td>{item.type}</td>
                        <td></td>
                    </tr>
                )
            }
            
        }else{
            return(
                <tr key={i}>
                    <td>{item.p_time}</td>
                    <td>{item.item}</td>
                    <td>{item.customer}</td>
                    <td>{item.qty}</td>
                    <td>{item.branch}</td>
                    <td>{item.user}</td>
                    <td>{item.type}</td>
                    <td><b>Deleted</b></td>
                </tr>
            )
        }
        
    },
    detSearchActivityRprtBtn:function(){
        let dates = $('.det_activity_dates').val();
        let customer = $('.txt_act_cust').val();
        let type = $('.sel_acc_type').val();
        
        if(dates !=""){

            $('.rprt_loader').fadeIn('slow');
            
            $.ajax({
                url:server_url+"/detailed_search_activities_report",
                data:{dates:dates,customer:customer,type:type,"_token": cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    
                    $('.det_activity_dates').val('');
                    $('.txt_act_cust').val('');
                    $('.sel_acc_type').val('');
                    $('.roll_search').fadeOut('slow');
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

        }else{
            $('.msg').html("<div class='err'>Select Dates</div>")
        }
    },
    searchActivityReports:function(){
        let customer = $('.pool_activities_txt').val();
        $('.rprt_loader').fadeIn('slow');
        if(customer !=""){
            $.ajax({
                url:server_url+"/search_activities_report",
                data:{customer:customer,"_token": cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    $('.rprt_loader').fadeOut('slow');
                    $('.pool_activities_txt').val('');
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
    getInitialState:function(){
        return{
           tabVal:"",
           rowItem:[],
           activityId:"",
           activityType:""
        }
    },
    selUpBranch:function(){
        $('.roll_branch').fadeIn('slow');
    },
    fetchUpBranchData: function(id){
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
                    if(res.curr_branch != id){
                        $('.roll_branch').fadeOut('slow');

                        let qry = "SELECT * FROM pool WHERE up_branch='1'";
    
                        $.ajax({
                            url:"http://www.ababuapps.com/up_pos/custom_reports.php",
                            data:{dates:dates,dates_col:"p_time",qry_str:qry,rprt:"pool_activities","_token":cs_token},
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
                    }
                }
            });

        }
    },
    clsDialog:function(i,event){
        $("."+i).fadeOut('slow');
        $('.msg').empty();
        $('.det_activity_dates').val('');
    },
    detailedActivitySearch:function(){
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

                    $(".txt_act_cust").easyAutocomplete(options);

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
    clsDialog:function(i,event){
        $("."+i).fadeOut('slow');
    },
    render:function(){
        return(
            <div>
            <div className="admin_top">
                <div className='title_link'>
                     
                    <b>Ball Pool</b>
                </div>
            </div>
            <div className="dashboard">
                <div className='ad_wraps'>   
                    <div className='rprt_top_title'>
                        <input type="text" className="pool_activities_txt" placeholder="Type Customer Name" />
                        <div className='search_btn' onClick={this.searchActivityReports}></div>
                        <div className='detailed_search' onClick={this.detailedActivitySearch}></div>
                        <div className='up_branch' onClick={this.selUpBranch}></div>
                    </div> 
                    <div id='rprt_pool_tbl' className='reports_table_wrap'>
                    <table>
                    <thead>
                        <tr><th>Date</th><th>Item</th><th>Customer</th><th>Qty</th><th>Branch</th><th>User</th><th>Type</th><th></th></tr>
                                            
                        </thead>
                        <tbody>
                        {
                            this.state.rowItem.map(this.eachRow)
                            }
                        </tbody>
                    </table>
                    </div>
                </div>

                <div className='roll_search'>
                        <div className="cls_blk" onClick={this.clsDialog.bind(this,"roll_search")}></div>
                            <div className='msg'></div>
                            <li>Dates</li>
                            <li><input className='det_activity_dates' type='text' /></li>
                            <li>Customer</li>
                            <li>
                            <li><input className='txt_act_cust' type='text' /></li>
                            </li>
                            <li>
                            <li>Type</li>
                            <li>
                                <select className='sel_acc_type'>
                                    <option></option>
                                    <option>Purchase</option>
                                    <option>Allocation</option>
                                    <option>Collection</option>
                                </select>
                            </li>
                            </li>
                            
                            <li><input type='button' value='Search' onClick={this.detSearchActivityRprtBtn}/></li>
                </div>

                <div className='roll_branch'>
                    <div className="cls_blk" onClick={this.clsDialog.bind(this,"roll_branch")}></div>
                        <div className='msg'></div>
                        <li><p>Select Dates</p></li>
                        <li><input type='text' className='branch_reports_dates' /></li>
                        <li><input type='button' value='Upper Hill' onClick={this.fetchUpBranchData.bind(this,"1")} /></li>
                        <li><input type='button' value='Kiambu Road' onClick={this.fetchUpBranchData.bind(this,"2")} /></li>
                        <li><input type='button' value='Ngong Road' onClick={this.fetchUpBranchData.bind(this,"3")} /></li>
                </div>

                <div className="close_drawer_conf">
                        <div className="cls" onClick={this.clsDialog.bind(this,"close_drawer_conf")}></div>
                        <li><p>Are you sure you want to permanently delete entry ?</p></li>
                       
                        <li><input type='button' onClick={this.confDelActivityBtn} value='Yes' /></li>
                </div> 

                <div className='rprt_loader'>Loading...</div>
            </div>
        </div>)
    }

});


let Reports = React.createClass({
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

        this.setState({tabVal:"dashboard"});

        $.ajax({
            url:server_url+"/pool_reports",
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

                    $(".pool_report_txt").easyAutocomplete(options);

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

        $(".branch_reports_dates").flatpickr(optional_config);
        


    },
    eachRow:function(item,i){
        return(
            <tr key={i}>
                <td>{item.item}</td>
                <td>{item.in_stock}</td>
                <td>{item.purchased}</td>
                <td>{item.allocated}</td>
                <td>{item.collected}</td>
                <td>{item.act_in_stock}</td>
                <td>{item.in_store}</td>
            </tr>
        )
    },
    getInitialState:function(){
        return{
           tabVal:"",
           rowItem:[]
        }
    },
    searchPoolReports:function(){
        let item = $('.pool_report_txt').val();
        if(item !=""){

            $.ajax({
                url:server_url+"/search_pool_report",
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
        $('.msg').empty();
        $('.branch_inventory_reports_dates').val('');


    },
    selUpBranch:function(){
        $('.roll_branch').fadeIn('slow');
    },
    fetchUpBranchData:function(id){
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
                    if(res.curr_branch != id){
                        $('.roll_branch').fadeOut('slow');

                        let qry = "SELECT id, item FROM pool WHERE up_branch='1' GROUP BY item";
    
                        $.ajax({
                            url:"http://www.ababuapps.com/up_pos/custom_reports.php",
                            data:{dates:dates,dates_col:"p_time",qry_str:qry,rprt:"pool_reports","_token":cs_token},
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
                    }
                }
            });

        }
    },
    render:function(){
        return(            <div>
            <div className="admin_top">
                <div className='title_link'>
                     
                    <b>Ball Pool</b>
                </div>
            </div>
            <div className="dashboard">
                <div className='ad_wraps'>  
                <div className='rprt_top_title'>
                    <input type="text" className="pool_report_txt" placeholder="Type Item Name..." />
                    <div className='search_btn' onClick={this.searchPoolReports}></div>
                    <div className='up_branch' onClick={this.selUpBranch}></div>
               </div> 
                    <div id='act_rprt_pool_tbl' className='reports_table_wrap'>
                    <table>
                    <thead>
                        <tr><th>Item</th><th>Opening Stock</th><th>Purchased</th><th>Allocated</th><th>Collected</th><th>Actual Stock</th><th>Unissued Stock</th></tr>
                                            
                        </thead>
                        <tbody>
                        {
                            this.state.rowItem.map(this.eachRow)
                            }
                        </tbody>
                    </table>
                    </div>
                </div>

                <div className='roll_branch'>
                    <div className="cls_blk" onClick={this.clsDialog.bind(this,"roll_branch")}></div>
                        <div className='msg'></div>
                        <li><p>Select Dates</p></li>
                        <li><input type='text' className='branch_reports_dates' /></li>
                        <li><input type='button' value='Upper Hill' onClick={this.fetchUpBranchData.bind(this,"1")} /></li>
                        <li><input type='button' value='Kiambu Road' onClick={this.fetchUpBranchData.bind(this,"2")} /></li>
                        <li><input type='button' value='Ngong Road' onClick={this.fetchUpBranchData.bind(this,"3")} /></li>
                </div>

            </div>
        </div>)
    }

});


let Events = React.createClass({
    componentDidMount() {
        
        this.setState({tabVal:"dashboard"});

        $.ajax({
            url:server_url+"/pool_reports_by_events",
            data:{"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let res = JSON.parse(data);
                let arr = [];
                                        
                $.each(res,function(index,value){
                    $.each(value,function(index2,value2){
                        arr.push(value[index2]);
                    });
                });

                this.setState({rowItem:arr});
            
            }
        });


        $.ajax({
            url:server_url+"/get_all_events",
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

                    $(".sel_pool_event_rprt").easyAutocomplete(options);

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

                    $(".pool_report_txt").easyAutocomplete(options);

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
    eachRow:function(item,i){
        return(
            <tr key={i}>
                <td>{item.event}</td>
                <td>{item.item}</td>
                <td>{item.purchased}</td>
                <td>{item.allocated}</td>
                <td>{item.collected}</td>
            </tr>
        )
    },
    eachOpt:function(item,i){
        return(
            <option value={item.id} key={i}>
                {item.event}
            </option>
        )
    },
    getInitialState:function(){
        return{
           tabVal:"",
           rowItem:[],
           optItem:[]
        }
    },
    searchPoolEventReports:function(){

        let event = $('.sel_pool_event_rprt').val();

        if(event !=""){

            $.ajax({
                url:server_url+"/search_pool_event_report",
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
                
                }
            });

        }

    },
    searchPoolReports:function(){
        let item = $('.pool_report_txt').val();
        if(item !=""){

            $.ajax({
                url:server_url+"/search_pool_report",
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
        return(            <div>
            <div className="admin_top">
                <div className='title_link'>
                     
                    <b>Ball Pool</b>
                </div>
            </div>
            <div className="dashboard">
                <div className='ad_wraps'>  
                <div className='rprt_top_title'>

                <input type='text' className='sel_pool_event_rprt' placeholder="Type Event Title..." />
                   
                    <div className='search_btn' onClick={this.searchPoolEventReports}></div>
                   
               </div> 
                    <div id='act_rprt_pool_by_event_tbl' className='reports_table_wrap'>
                    <table>
                    <thead>
                        <tr><th>Event</th><th>Item</th><th>Purchased</th><th>Allocated</th><th>Collected</th></tr>
                                            
                        </thead>
                        <tbody>
                            {
                            this.state.rowItem.map(this.eachRow)
                            }
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>)
    }

});



let Uploads = React.createClass({
    componentDidMount() {
        
        this.setState({tabVal:"dashboard"});

        $.ajax({
            url:server_url+"/pool_reports",
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
                <td>{item.p_time}</td>
                <td>{item.item}</td>
                <td>{item.customer}</td>
                <td>{item.qty}</td>
                <td>{item.branch}</td>
                <td>{item.user}</td>
            </tr>
        )
    },
    getInitialState:function(){
        return{
           tabVal:"",
           rowItem:[]
        }
    },
    upload_results_xls:function(){
        
        var formData = new FormData($('#upload_results_form')[0]);
        formData.append('items_file', $('input[type=file]')[0].files[0]);
        

        $.ajax({
            url:server_url+"/upload_results_xls",
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
                    $('.msg').html("<div class='err'>Error: Event Not Found</div>");
                }else if(res.status ==3){
                    $('.msg').html("<div class='err'>Error: Item Not Found</div>");
                }else if(res.status ==4){
                    $('.msg').html("<div class='err'>Error: Item Not Purchased</div>");
                }else if(res.status ==5){
                    $('.msg').html("<div class='err'>Error: Check Item Purchase Quantity</div>");
                }else if(res.status ==6){
                    $('.msg').html("<div class='err'>Error: Check Item Purchase Quantity</div>");
                }else if(res.status ==7){
                    $('.msg').html("<div class='err'>Error: Check Item Purchase Quantity</div>");
                }else if(res.status ==8){
                    $('.msg').html("<div class='err'>Error: Item Not Found</div>");
                }else if(res.status ==9){
                    $('.msg').html("<div class='err'>Error: Event Not Found</div>");
                }
                
                
                
                
            },error: function() {
                console.log("Error");
            },complete: function () {
                //$progress.hide();
            }
        });
    },
    render:function(){
        return(            <div>
            <div className="admin_top">
                <div className='title_link'>
                     
                    <b>Ball Pool</b>
                </div>
            </div>
            <div className="dashboard">
                <div className='ad_wraps'>   
                    <div className="admin_top">
                        <div className="msg"></div>
                    </div>
                    <div className="form">
                        <table>
                            <tbody>
                                <tr>
                                    <td><label>Upload Competition Results File</label></td>
                                </tr>
                                <tr>
                                    <td><p></p></td>
                                </tr>
                                <form id="upload_results_form" enctype="multipart/form-data">
                                        <tr>
                                            {/** <td><a href={server_url+'/tester'}>Download</a></td>*/}
                                            
                                            <td><input type='file' id='file_results' name='file_items' /></td>
                                        </tr>
                                        <tr>
                                            
                                        </tr>
                                </form>
                                <tr>
                                    <td><p></p></td>
                                </tr>
                                <tr>
                                    <td><input type='button' value='Upload' onClick={this.upload_results_xls} className='btn_upld_results' /></td>
                                </tr>
                            </tbody>
                        </table>
                        
                    </div>
                </div>
            </div>
        </div>)
    }

});

let ActiveMenu = React.createClass({
    render:function(){
        return(<div className="menu_active"></div>)                
    }
});

let Pool = React.createClass({
    componentDidMount() {

        this.setState({menuItemVal:"activities"});
    },
    getInitialState:function(){
        return{
           menuItemVal:""
        }
    },menuItem:function(i){
        this.setState({menuItemVal:i});  
    },
    adminLink:function(){
        $('#admin').fadeIn();
        $('#pool').hide();
    },
    cashierLink:function(){
        $('#teller').fadeIn();
        $('#pool').hide();
        $('.search_txt').focus();
        $('.drawer_msg').empty();
        $('.msg').empty();
    },
    render:function(){
        let menu = this.state.menuItemVal;
        let act_activities;
        let act_reports;
        let act_uploads;
        let act_events;
        let comp;
        if(menu=="activities"){
            comp = <Activities/>;
            act_reports = "";
            act_activities = <ActiveMenu />;
            act_uploads = "";
            act_events = "";
        }else if(menu=="upload"){
            comp = <Uploads/>;
            act_reports = "";
            act_activities = "";
            act_uploads = <ActiveMenu />;
            act_events = "";
        }else if(menu=="reports"){
            comp = <Reports/>;
            act_reports = <ActiveMenu />;
            act_activities = "";
            act_uploads = "";
            act_events = "";
        }else if(menu=="events"){
            comp = <Events/>;
            act_reports = "";
            act_activities = "";
            act_uploads = "";
            act_events = <ActiveMenu />;
        }

        return(
            <div className="pool_back">
                    <div className="menu">

                        <div className="pool_link" onClick={this.adminLink}>
                                <b>Admin</b>
                        </div>

                        <div className="cashier_link" onClick={this.cashierLink}>
                                <b>Cashier</b>
                        </div>

                        <div className="menu_wrapper">

                                <div className="menu_item" onClick={this.menuItem.bind(this,"activities")}>
                                    
                                    <div className="menu_icon">
                                        <img src="../img/tiles_txns.png" />
                                    </div>
                                    <div className="menu_txt">
                                        <b>Activities</b>
                                    </div>
                                    {act_activities}
                                </div>

                                <div className="menu_item" onClick={this.menuItem.bind(this,"upload")}>
                                    
                                    <div className="menu_icon">
                                        <img src="../img/upload.png" />
                                    </div>
                                    <div className="menu_txt">
                                        <b>Upload</b>
                                    </div>
                                    {act_uploads}
                                </div>

                                <div className="menu_item" onClick={this.menuItem.bind(this,"reports")}>
                                    
                                    <div className="menu_icon">
                                        <img src="../img/tiles_txns.png" />
                                    </div>
                                    <div className="menu_txt">
                                        <b>Reports</b>
                                    </div>
                                    {act_reports}
                                </div>

                                <div className="menu_item" onClick={this.menuItem.bind(this,"events")}>
                                    
                                    <div className="menu_icon">
                                        <img src="../img/events.png" />
                                    </div>
                                    <div className="menu_txt">
                                        <b>Events</b>
                                    </div>
                                    {act_events}
                                </div>

                        </div>
                        
                    </div>
                    { comp }
                </div>
                );
                
            }

});

ReactDOM.render(
    <Pool />
,document.getElementById('pool'));