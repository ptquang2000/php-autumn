const error = <?= isset($_SESSION['LOGIN-ERROR']) ? json_encode($_SESSION['LOGIN-ERROR']) : 'false'?>;

new Vue({
  el: '#login-section',
  data:{
    error: error,
    username: '',
    password: '',
  },
  methods:{
    form_validate: function(e){
      if (!this.username || !this.password)
      {
        this.error = 'Please input missing values'
        e.preventDefault()
      }
    }
  }
})