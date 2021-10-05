var boardgames = <?= json_encode(serialize_object($boardgames))?>;

new Vue(
  {
    el:'#main-container',
    data:{
      boardgames: boardgames,
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
      }
    }
  }
)