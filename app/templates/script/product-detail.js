var boardgame = <?= json_encode(serialize_object($boardgame))?>;
var fav = <?= isset($fid) ? json_encode(serialize_object($fid)) : 'false'?>;
var member = <?= isset($mid) ? json_encode($mid) : 'false'?>;
var comments = <?=json_encode($comments)?>;

new Vue(
  {
    el:'#main-container',
    data:{
      boardgame: boardgame,
      role: role,
      fav: fav,
      member: member,
    },
    methods:{
      delete_fav: function(e)
      {
        e.preventDefault()
        this.fav = axios.post("/delete-favourite", Qs.stringify({
          bid: this.fav.bid,
          mid: this.fav.mid,
          fid: this.fav.fid,
        }),{
          headers:{"Content-Type": "application/x-www-form-urlencoded",}
        }).then(function (response) {
          return false
        }).then(fav => {
          this.fav = fav
        })
      },
      add_fav: function(e)
      {
        e.preventDefault()
        this.fav = axios.post("/add-favourite", Qs.stringify({
          bid: this.boardgame.bid,
          mid: this.member,
        }),{
          headers:{"Content-Type": "application/x-www-form-urlencoded",}
        }).then(function (response) {
          var regex = new RegExp('var fav = .*;')
          var fav = response.data.match(regex)[0]
          return JSON.parse(fav.substring(10, fav.length-1))[0]
        }).then(fav => {
          this.fav = fav
        })
      }
    }
  }
)

new Vue(
  {
    el:'#comments-container',
    data:{
      boardgame: boardgame,
      role: role,
      member: member,
      comments: comments,
      content: '',
    },
    methods:{
      submit_comment: function(e){
        e.preventDefault()
        if (this.content)
        this.comments = axios.post("/add-comment", Qs.stringify({
          bid: this.boardgame.bid,
          mid: this.member,
          content: this.content
        }),{
          headers:{"Content-Type": "application/x-www-form-urlencoded",}
        }).then(function (response) {
          var regex = new RegExp('var comments = .*;')
          var comments = response.data.match(regex)[0]
          return JSON.parse(comments.substring(15, comments.length-1))
        }).then(
          comments => {
            this.comments = comments
            this.content = ''
        })
      },
      edit_comment: function(e, idx){
        e.preventDefault()
        this.comments = axios.post("/edit-comment", Qs.stringify({
          cid: this.comments[idx].cid,
          bid: this.boardgame.bid,
          mid: this.member,
          content: this.comments[idx].content
        }),{
          headers:{
            "Content-Type": "application/x-www-form-urlencoded",
          }
        }).then(function (response) {
          var regex = new RegExp('var comments = .*;')
          var comments = response.data.match(regex)[0]
          return JSON.parse(comments.substring(15, comments.length-1))
        }).then(comments => {
          this.comments = comments
          this.content = ''
        })
      },
      delete_comment: function(e, idx){
        e.preventDefault()
        this.comments = axios.post("/delete-comment", Qs.stringify({
          cid: this.comments[idx].cid,
          bid: this.boardgame.bid,
          mid: this.member,
          content: this.comments[idx].content
        }),{
          headers:{"Content-Type": "application/x-www-form-urlencoded",}
        }).then(function (response) {
          var regex = new RegExp('var comments = .*;')
          var comments = response.data.match(regex)[0]
          return JSON.parse(comments.substring(15, comments.length-1))
        }).then(comments => {
          this.comments = comments
          this.content = ''
        })
      }
    }
  }
)