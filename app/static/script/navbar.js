axios.get('user-role')
.then(res=>{
new Vue(
  {
    el: '#navbar',
    data: {
      login: res.data == 'ANONYMOUS' ? false : true,
      url: window.location.pathname
    }
  }
)
})
