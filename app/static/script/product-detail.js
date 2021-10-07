const bid = (new URL(window.location.href)).searchParams.get("id")

axios.all([
  axios.get(`/boardgame/${bid}`),
  axios.get(`/favourite/${bid}`),
  axios.get(`/member`),
  axios.get(`/comment/${bid}`),
  axios.get('user-role'),
]).then(axios.spread((res1, res2, res3, res4, res5)=>{

var boardgame = res1.data
var fav = res2.data instanceof Object ? res2.data : false
var member = res3.data instanceof Object ? res3.data.mid : false
var comments = res4.data
var role = res5.data

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
      delete_fav: function()
      {
        axios.post("/delete-favourite", Qs.stringify({
          bid: this.fav.bid,
          mid: this.fav.mid,
          fid: this.fav.fid,
        }),{
          headers:{"Content-Type": "application/x-www-form-urlencoded",}
        }).then(function (response) {
          return response.data
        }).then(favs => {
          if (!favs) this.fav = false
          else {
            fav = favs.filter(fav=>fav.bid==this.boardgame.bid)
            if (fav.length == 0) this.fav = false
            else this.fav = fav[0]
          }
        })
      },
      add_fav: function()
      {
        axios.post("/add-favourite", Qs.stringify({
          bid: this.boardgame.bid,
          mid: this.member,
        }),{
          headers:{"Content-Type": "application/x-www-form-urlencoded",}
        }).then(function (response) {
          return response.data
        }).then(favs => {
          this.fav = favs.filter(fav=>fav.bid==this.boardgame.bid)[0]
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
        axios.post("/add-comment", Qs.stringify({
          bid: this.boardgame.bid,
          mid: this.member,
          content: this.content
        }),{
          headers:{"Content-Type": "application/x-www-form-urlencoded",}
        }).then(function (response) {
          return response.data
        }).then(
          comments => {
            this.comments = comments
            this.content = ''
        })
      },
      edit_comment: function(e, idx){
        e.preventDefault()
        axios.post("/edit-comment", Qs.stringify({
          cid: this.comments[idx].cid,
          bid: this.boardgame.bid,
          mid: this.member,
          content: this.comments[idx].content
        }),{
          headers:{
            "Content-Type": "application/x-www-form-urlencoded",
          }
        }).then(function (response) {
          return response.data
        }).then(comments => {
          this.comments = comments
        })
      },
      delete_comment: function(e, idx){
        e.preventDefault()
        axios.post("/delete-comment", Qs.stringify({
          cid: this.comments[idx].cid,
          bid: this.boardgame.bid,
          mid: this.member,
          content: this.comments[idx].content
        }),{
          headers:{"Content-Type": "application/x-www-form-urlencoded",}
        }).then(function (response) {
          return response.data
        }).then(comments => {
          this.comments = comments
        })
      }
    }
  }
)

}))