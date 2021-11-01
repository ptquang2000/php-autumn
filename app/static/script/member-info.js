
axios.all([
  axios.get(`/member/boardgames`),
  axios.get(`/favourite`),
  axios.get(`/member`),
  axios.get(`/member/img`),
  axios.get('/user-role'),
]).then(axios.spread((res1, res2, res3, res4, res5)=>{
var boardgames = res1.data ? res1.data : []
var fav = res2.data ? res2.data : []
var member = res3.data instanceof Object ? res3.data : false
var image = res4.data.image
var error = (new URLSearchParams(window.location.search)).get('error')
var role = res5.data.role


new Vue(
  {
    el:'#info',
    data:{
      member: member,
      role: role,
      image: image,
      fav: fav,
      boardgames: boardgames,
      error: error,
    },
    computed:{
      favs() {
        return this.boardgames.map(boardgame => {
          if (this.fav.filter(fav => fav.bid == boardgame.bid).length == 1) return true
          return false
        })
      }
    },
    methods:{
      redirect: function(id){
        window.location.href = `/product-detail?id=${id}`
      },
      func_swap_img: function(event){
          const [file] = event.target.files
          if (file != null) {
            document.getElementById('preview-img').src = URL.createObjectURL(file)
          }
      },
      drop_img: function(event){
          event.preventDefault()
          const [file] = event.dataTransfer.files
          // console.log(file)
          if (file != null) {
            document.getElementById('preview-img').src = URL.createObjectURL(file)
            document.getElementById('input-file-now').files = event.dataTransfer.files
          }
      },
      save_member_info: function(){
        var form = new FormData();
        form.append('')
      }
      ,
      delete_fav: function(idx)
      {
        var fav = this.fav.filter(fav => fav.bid == this.boardgames[idx].bid)[0]
        axios.post("/delete-favourite", Qs.stringify({
          bid: fav.bid,
          mid: fav.mid,
          fid: fav.fid,
        }),{
          headers:{"Content-Type": "application/x-www-form-urlencoded",}
        }).then(function (response) {
          return response.data
        }).then((favs) => {
          this.fav = favs ? favs : []
          this.boardgames.splice(idx, 1)
        })
      },
    }
  }
)
}))


