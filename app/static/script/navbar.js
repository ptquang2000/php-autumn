axios.get('/user-role')
.then(res=>{
new Vue(
  {
    el: '#navbar',
    data: {
      login: res.data.role == 'ANONYMOUS' ? false : true,
      url: window.location.pathname,
      role: res.data.role
    }
  }
)
})
