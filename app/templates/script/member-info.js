var member = <?= json_encode($member)?>;
var image = '<?= file_exists(__IMAGE__.$member['img']) && !empty($member['img']) ? base64_encode(file_get_contents(__IMAGE__.$member['img'])) : 'false'?>';

new Vue(
  {
    el:'#info',
    data:{
      member: member,
      role: role,
      image: image,
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