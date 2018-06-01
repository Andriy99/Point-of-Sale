let SearchBox = React.createClass({
    searcher:function(){
        this.props.sbFunction(this.refs.search_txt.value);
    },
    render: function(){
        return(
            <div className='search_box'>
                        <div className="user_sname" >o. ababu</div>
                        <input type='text' ref="search_txt" onChange={this.searcher} className='search_txt' />
                
            </div>
        )
    }
});

let Teller = React.createClass({
    getInitialState:function(){
        return{
            titles:[],
            mboxIndex:"",
            mboxQty:"",
            totalVat:0,
            totalPrice:0,
            totalItemQty:0
        }
    }, 
    showQtyDialog: function(i, event){
        this.setState({mboxIndex:i.item_id});
        this.setState({mboxQty:i.qty});
        $(".item_qty").fadeIn('slow');
    },
    sbPropFunc:function(search_val){
        //console.log(`Hello... ${search_val}`);
        
        $.ajax({
            url:server_url+"/search_items",
            data:{search_val:search_val,"_token": cs_token},
            type:"POST",
            context: this,
            success:function(data){
                //console.log(data);
                let res = JSON.parse(data);
                if(res.status !=0){
                    this.popMbox(res);
                    this.popTotals();
                }
                
            }
        });
        
    },
    eachTitle:function(item,i){
        return(
            <td key={i} >
                    <div className='mbox'>
                    <div className='mbox_del'></div>
                    <div className='mbox_items_ttl_top'>Item Description</div>
                    <div className='mbox_items_desc' >{item.item}</div>
                    <div className='mbox_price_ttl'>Price</div>
                    <div className='mbox_items_price'>{item.price}.00</div>
                    <div className='mbox_qty' data-key={item.id} onClick={this.showQtyDialog.bind(this, {"item_id":item.item,"qty":item.qty})}>
                        <div className='mbox_qty_ins_ttl'  >
                            Qty: 
                        </div>
                        <div className='mbox_qty_ins'>{item.qty}</div>
                    </div>
                    <div className='mbox_items_ttl'>Total</div>
                    <div className='mbox_items_total'>{item.total}.00</div>
                </div>
            </td>
        )
    },
    clsDialog:function(i,event){
        $("."+i).fadeOut('slow');
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
                
                let res = JSON.parse(data);
                this.setState({totalVat:res.sum_tax});
                this.setState({totalPrice:res.sum_total});
                this.setState({totalItemQty:res.sum_qty});
                
            }
        });

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
                    let res = JSON.parse(data);
                    this.popMbox(res);
                    this.popTotals();
                }
            });
        }
    },delItem:function(){
        //console.log(this.state.mboxIndex);
        $.ajax({
            url:server_url+"/del_sc_item",
            data:{item:this.state.mboxIndex,"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                let res = JSON.parse(data);
                this.popMbox(res);
                this.popTotals();
            }
        });
    },tender:function(){

        $.ajax({
            url:server_url+"/check_sc",
            data:{"_token":cs_token},
            type:"POST",
            context: this,
            success:function(data){
                let res = JSON.parse(data);
                if(res.cart_count > 0){
                    $('.search_txt').val('');
                    $('.tender').fadeIn('slow');
                    $('.cashInput').focus(); 
                }
                //this.popMbox(res);
                //this.popTotals();
            }
        });

        

    },tenderTrans:function(e){
        if (e.key === 'Enter'){
            let cash = $('.cashInput').val();
            
            if(parseInt(cash) >= parseInt(this.state.totalPrice)){
                
                $.ajax({
                    url:server_url+"/tender_trans",
                    data:{cash:cash,"_token":cs_token},
                    type:"POST",
                    context: this,
                    success:function(data){
                        console.log(data);
                        
                    }
                });

            }else{
                $('.msg_wrap').html("<div class='err'><b>Error: Cash Not Enough</b></div>");
            }
          }
    },
    dialPadBtn:function(i,event){
            if (document.activeElement) {
                var output = document.getElementById ("output");
                output = document.activeElement.tagName.innerHTML;
                console.log(output);
            }
            let srch_val = $('.search_txt').val();
            if(i =='<-'){
                $('.search_txt').val(srch_val.slice(0,-1));
            }else{
                $('.search_txt').val(srch_val + i);
            }
    },
    render: function(){
        return(
            <div>    
                <SearchBox sbFunction={this.sbPropFunc} />

                    <div className="wrap">
                        <div className='desc'>
                            <table>
                                <tbody>
                                    <tr className="mbox_container">
                                        {
                                            this.state.titles.map(this.eachTitle)
                                        }
                                        
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        

                        <div className="item_qty">
                            <label>Update Quantity</label>
                            <div className="cls" onClick={this.clsDialog.bind(this,"item_qty")}></div>
                            <div className="item_del" onClick={this.delItem}><b>delete</b>	</div>
                            <div className="qty_ctrls_wrapper">	
                                
                                <div className="qty_ctrl" onClick={this.qtyUp.bind(this, "-")}>	
                                    <b>-</b>
                                </div>
                                <div className="qty_ctrl_val">	
                                        <b>{this.state.mboxQty}</b>
                                </div>
                                <div className="qty_ctrl" onClick={this.qtyUp.bind(this, "+")}>	
                                    <b>+</b>
                                </div>
                                
                            </div>
                            
                                        
                        </div>


                        

                    </div>

                    <div className="wrap">
                    <div className="total">
                        
                        <div className="one">

                            <div className="total_qty">
                                <p>Total Qty</p>
                                <b>{this.state.totalItemQty}</b>
                            </div>
                            <div className="total_vat">
                                <p>Total VAT</p>
                                <b>{this.state.totalVat}.00</b>
                            </div>
                            <div className="act_total">
                                <div className="total_figure">
                                    <p>Total</p>
                                    <b>{this.state.totalPrice}.00</b>
                                </div>
                        
                            
                            </div>
                        </div>
            
            <div className="calc">
                <div className="tender_btns_wrap">
                    <div className="calc_btn">
                        <b>i</b>
                    </div>
                    <div className="tender_btn" onClick={this.tender}>
                        <b>/=</b>
                    </div>
                </div>

                <div className="calc_wrap">
                            <div className="calc_btn" onClick={this.dialPadBtn.bind(this,"<-")}>
                            <b> {`<-`}</b>
                            </div>
                            <div className="calc_btn" onClick={this.dialPadBtn.bind(this,"9")}>
                                <b> 9</b>
                            </div>
                            <div className="calc_btn" onClick={this.dialPadBtn.bind(this,"8")}>
                                <b> 8</b>
                            </div>
                            <div className="calc_btn" onClick={this.dialPadBtn.bind(this,"7")}>
                                <b> 7</b>
                            </div>
                            <div className="long_calc_btn" onClick={this.dialPadBtn.bind(this,"0")}>
                                <b>0 </b>
                            </div>
                            <div className="calc_btn" onClick={this.dialPadBtn.bind(this,"6")}>
                                <b> 6</b>
                            </div>
                            <div className="calc_btn" onClick={this.dialPadBtn.bind(this,"5")}>
                                <b> 5</b>
                            </div>
                            <div className="calc_btn" onClick={this.dialPadBtn.bind(this,"4")}>
                                <b> 4</b>
                            </div>
                            <div className="calc_btn" onClick={this.dialPadBtn.bind(this,"1")}>
                                <b> 1</b>
                            </div>
                            <div className="calc_btn" onClick={this.dialPadBtn.bind(this,"2")}>
                                <b> 2</b>
                            </div>
                            <div className="calc_btn" onClick={this.dialPadBtn.bind(this,"3")}>
                                <b> 3</b>
                            </div>
                 </div>           
            </div>
                            
            <div className="tender">
                            <label>Tender</label>
                            <div className="cls" onClick={this.clsDialog.bind(this,"tender")}></div>
                                <div className="tender_inputs_wrap">
                                    <div className="msg_wrap"></div>
                                    <li><b>Cash</b></li>
                                    <li><input type="text" className="cashInput" onKeyPress={this.tenderTrans} /></li>
                                   
                                </div>    
                        </div>   
                        
                                
                        </div>
                    </div>

                    <div className = "login">
                        <div className="inside">
                            <div className="message"></div>
                        <ul>
                        <li><input type="text" id="username_ipt" /></li>
                        <li><input type="password" id="passwd_ipt" /></li>
                        <li><input type="button" id="login_btn" className="login_btn" value="Login" /></li>
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