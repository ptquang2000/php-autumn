var members = <?= json_encode($members)?>;

new Vue(
  {
    el:'#members',
    data:{
      members: members,
    },
    methods:{
      redirect: function(id){
        window.location.href = `/member-info/${id}`
      }
    }
  }
)