let DashTotals = React.createClass({
    componentDidMount() {

        //Loads on load
        $.ajax({
            url:server_url+"/dash_totals",
            data:{"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let res = JSON.parse(data);
                this.setState({totalCashSales:res.total});
                this.setState({totalQty:res.qty});
                this.setState({totalTax:res.total_tax});
                this.setState({cashAtHand:res.cash_at_hand});
                
                
            }
        });
       
    },
    getInitialState:function(){
        return{
            totalQty:"",
            totalCashSales:"",
            totalTax:"",
            cashAtHand:""
        }
    },
    render:function(){
        return(
            <table>
                <tbody>
                    <tr>
                        <th>Cash at Hand</th><th>Cash Sales</th><th>Total Tax</th><th>Total Qty</th>
                    </tr>
                    <tr>

                        <td>{this.state.cashAtHand}.00</td><td>{this.state.totalCashSales}.00</td><td>{this.state.totalTax}.00</td><td>{this.state.totalQty}</td>
                    </tr>
                </tbody>
            </table>
        );
    }
});

let Test = React.createClass({
    render:function(){
        return <h1>Hello Young World</h1>;
    }
});

let MenuItems = React.createClass({
    componentDidMount() {

        //Loads on load
        $('#dash_wrap').fadeIn('slow');
        $("#link_trans").fadeIn("slow");
    },
    getInitialState:function(){
        return{
           menuItemVal:""
        }
    },
    menuItem:function(i,event){

        this.setState({menuItemVal:i});
    
        /*
        if(i == "transactions"){

            this.setState({menuItemVal:i});

            $("#link_trans").fadeIn("slow");
            $("#link_items").hide();
            $("#link_users").hide();
            $("#link_taxes").hide();
           
            $('#dash_wrap').fadeIn('slow');
            $('#items_wrap').hide();
            $('#users_wrap').hide();
            $('#taxes_wrap').hide();
        }else if(i =="items"){
            $("#link_trans").hide();
            $("#link_items").fadeIn("slow");
            $("#link_users").hide();
            $("#link_taxes").hide();
            
            $('#dash_wrap').hide();
            $('#item_reports').fadeIn('slow');
            $('#new_items_wrap').fadeIn('slow');
            $('#users_wrap').hide();
            $('#taxes_wrap').hide();

            $.ajax({
                url:server_url+"/admin_items_report",
                data:{"_token":cs_token},
                type:"POST",
                context: this,
                success:function(data){
                    //console.log(data);
                    let res = JSON.parse(data);

                    let tbl = "";
                    let no = 1;

                    $.each(res,function(index,value){
                        tbl = tbl + `<tr>
                            <td>${value.look_up}</td><td>${value.item_desc}</td>
                            <td>${value.sell_price}</td><td>${value.qty}</td><td>${value.tax}</td><td>${value.status}</td>
                            <td><a href='#'>Edit</a></td>
                        `;
                        no++;
                    });

                    $('#tbl_admin_items_rprt').html(tbl);
                }
            });

        }else if(i =="users"){
            $("#link_trans").hide();
            $("#link_items").hide();
            $("#link_users").fadeIn("slow");
            $("#link_taxes").hide();

            $('#dash_wrap').hide();
            $('#items_wrap').hide();
            $('#users_wrap').fadeIn('slow');
            $('#taxes_wrap').hide(); 
        }else if(i =="taxes"){
            $("#link_trans").hide();
            $("#link_items").hide();
            $("#link_users").hide();
            $("#link_taxes").fadeIn("slow");

            $('#dash_wrap').hide();
            $('#items_wrap').hide();
            $('#users_wrap').hide();
            $('#taxes_wrap').fadeIn('slow');
        }
        */
    },
    cashLink:function(){
        $('#admin').hide();
        $('#teller').fadeIn('slow');
        $('.search_txt').focus();
    },
    render:function(){
        return(
                <div>
                        <div className="cashier_link" onClick={this.cashLink}>
                                <b>Cashier</b>
                        </div>

                        <div className="menu_item" onClick={this.menuItem.bind(this,"transactions")}>
                            <div className="menu_active_wrap"></div>
                            <div className="menu_icon">
                                <img src="../img/tiles_txns.png" />
                            </div>
                            <div className="menu_txt">
                                <b>Transactions</b>
                            </div>
                        </div>

                        <div className="menu_item" onClick={this.menuItem.bind(this,"items")}>
                            <div className="menu_active_wrap"></div>
                            <div className="menu_icon">
                                <img src="../img/tiles_items.png" />
                            </div>
                            <div className="menu_txt">
                                <b>Items</b>
                            </div>
                        </div>

                        <div className="menu_item" onClick={this.menuItem.bind(this,"users")}>
                            <div className="menu_active_wrap"></div>
                            <div className="menu_icon">
                                <img src="../img/tiles_users.png" />
                            </div>
                            <div className="menu_txt">
                                <b>Users</b>
                            </div>
                        </div>

                        <div className="menu_item" onClick={this.menuItem.bind(this,"taxes")}>
                            <div className="menu_active_wrap"></div>
                            <div className="menu_icon">
                                <img src="../img/tax.png" />
                            </div>
                            <div className="menu_txt">
                                <b>Taxes</b>
                            </div>
                        </div>

                        <Test />
                </div>
        );
        
    }
});
/*
let Items = React.createClass({
    componentDidMount() {

        //Loads on load
       
    },
    getInitialState:function(){
        return{

        }
    },
    render:function(){
        return(
               <div></div> 
        );
    }
});

ReactDOM.render(
    <Items />
,document.getElementById('tbl_item_reports'));
*/
ReactDOM.render(
       <MenuItems />
,document.getElementById('menu_hold'));

ReactDOM.render(
       <DashTotals />
,document.getElementById('dash_totals'));
