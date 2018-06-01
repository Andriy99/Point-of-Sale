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
    },
    render: function(){
        return(
            <div>
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




