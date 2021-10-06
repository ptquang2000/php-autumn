axios.all([
  axios.get(`/boardgames${window.location.search}`),
  axios.get(`/favourite`),
  axios.get(`/member`),
]).then(axios.spread((res1, res2, res3)=>{

var boardgames = res1.data
var fav = res2.data ? res2.data : []
var member = res3.data instanceof Object ? res3.data.mid : false

new Vue(
{
  el:'#main-container',
  data:{
    boardgames: boardgames,
    member: member,
    fav: fav,
    role: role
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
    option: function(key, value)
    {
      var regex = new RegExp(`${key}=[^&]*`)
      var params = window.location.search.substring(1)
      var new_params = params.replace(regex, `${key}=${value}`)
      return params == new_params ? params + `&${key}=${value}` : new_params
    },
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
      }).then(favs => {
        this.fav = favs ? favs : []
      })
    },
    add_fav: function(idx)
    {
      axios.post("/add-favourite", Qs.stringify({
        bid: this.boardgames[idx].bid,
        mid: this.member,
      }),{
        headers:{"Content-Type": "application/x-www-form-urlencoded",}
      }).then(function (response) {
          return response.data
        }).then(favs => {
          this.fav = favs ? favs : []
      })
    }
  }
})

}))