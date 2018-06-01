let Comment = React.createClass({
  getInitialState: function(){
      return {editing:false}
  },
  edit:function(){
      this.setState({editing:true});
  },
  delete:function(){
      alert("delete");
      this.props.deleteFrontBoard(this.props.index);
  },
  save:function(){
      this.props.updateCommentText(this.refs.newText.value,this.props.index);
      this.setState({editing:false});
  },
  renderNormal: function(){
      return(
          <div className="commentContainer">
              <div className="commentText">{this.props.children}</div>
              <button className="btn" onClick={this.edit}>Edit</button>
              <button className="btn" onClick={this.delete}>Remove</button>
          </div>
      )
  },
  renderForm: function(){
      return(
          <div className="commentContainer">
              <textarea ref="newText" defaultValue={this.props.children}></textarea>
              <button className="btn" onClick={this.save}>Save</button>
          </div>
      )
  },
  render:function(){
      if(this.state.editing){
          return this.renderForm();
      }else{
          return this.renderNormal();
      }
      
  }
});

let Board = React.createClass({

  getInitialState: function(){
      return{
          comments:[
              //'Bacon',
              //'Icecream',
             // 'Cake'
          ]
      }
  },
  add: function(text){
      let arr = this.state.comments;
      arr.push(text);
      this.setState({comments:arr});
  },
  removeComment: function(i){
      alert('Comment: '+i);
      let arr = this.state.comments;
      arr.splice(i, 1);
      this.setState({comments:arr});
  },
  updateComment: function(newText, i){
      let arr = this.state.comments;
      arr[i] = newText;
      this.setState({comments:arr});
  },
  eachComment:function(item, i){
      return(
              <Comment key={i} index={i} updateCommentText={this.updateComment} deleteFrontBoard={this.removeComment}>
                  {item}
              </Comment>
          );
  },handleKeyPress:function(){
      let val = this.refs.egText.value;
      
          $.ajax({
              url:server_url + "/users_eg",
              data:{val:val,"_token": $('#token').val()},
              type:"POST",
              success:function(data){
                console.log(data);
              }
          });
      
  },
  render: function(){
      return(
          <div>
            <input type="hidden" name="_token" id="token" value={ cs_token } />
              <input type="text" ref="egText" onKeyPress={this.handleKeyPress} className="btn" />
              <button onClick={this.add.bind(null,'Oscar is awsome')} className="btn">Add New</button>
              <div className="board">
                  {
                      this.state.comments.map(this.eachComment)
                  }
              </div>
          </div>
      );
  }

});


ReactDOM.render(
    <div>
        <Board />
    </div>
,document.getElementById('example'));
