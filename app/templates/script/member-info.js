var member = <?= json_encode($member)?>;
var image = '<?= file_exists(__IMAGE__.$member['img']) && !empty($member['img']) ? base64_encode(file_get_contents(__IMAGE__.$member['img'])) : 'false'?>';
var boardgames = <?= json_encode(serialize_object($boardgames))?>;
var fav = <?= isset($fid) ? json_encode(serialize_object($fid)) : 'false'?>;

new Vue(
  {
    el:'#info',
    data:{
      member: member,
      role: role,
      image: image,
      fav: fav,
      boardgames: boardgames,
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
        }).then(function () {
        }).then(() => {
          this.fav.splice(idx, 1)
          this.boardgames.splice(idx, 1)
        })
      },
    }
  }
)