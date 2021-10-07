const error = (new URLSearchParams(window.location.search)).get('error');

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