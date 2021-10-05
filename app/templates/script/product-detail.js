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
        if (!this.content)
          e.preventDefault()
      }
    }
  }
)