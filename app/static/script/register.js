const error = (new URLSearchParams(window.location.search)).get('error');

new Vue({
  el: '#register-section',
  data:{
    error: error,
    username: '',
    password: '',
    name: '',
    email: '',
    phone: '',
  },
  methods:{
    form_validate: function(e){
      if (!this.username || !this.password || !this.name || !this.email)
      {
        this.error = 'Please input missing values'
        e.preventDefault()
      }
      if (this.email)
      {
        var regex = new RegExp('[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9.]+')
        if (!this.email.match(regex))
        {
          this.error = 'Invalid email address'
          e.preventDefault()
        }
      }
    }
  }
})