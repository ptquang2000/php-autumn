new Vue({
  el: '#main-container',
  data: {
    name: '',
    age_max: 0,
    age_min: 0,
    player_max: 0,
    player_min: 0,
    time_max: 0,
    time_min: 0,
    level: 1,
    price: 0,
    error: '',
  },
  methods: {
    save_boardgame() {
      var form = new FormData();
      form.append('name',this.name)
      form.append('age_max',this.age_max)
      form.append('age_min',this.age_min)
      form.append('player_max',this.player_max)
      form.append('player_min',this.player_min)
      form.append('time_max',this.time_max)
      form.append('time_min',this.time_min)
      form.append('level',this.level)
      form.append('price',this.price)
      form.append('image',this.$refs.image.files[0])
      axios.post('save-boardgame', form
      ).then((response)=>{
        var data = response.data
        if (typeof data.error === 'undefined')
          window.location.href = `/product-detail?id=${data.bid}`  
        this.error = data.error
      })
    },
    drop_img(event){
      event.preventDefault()
      const [file] = event.dataTransfer.files
      if (file != null) {
        document.getElementById('product_img').files = event.dataTransfer.files
      }
    },
  }
})