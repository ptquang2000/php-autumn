<?php include __TEMPLATE__.'html\\head.html'?>
<?php include __TEMPLATE__.'html\\navbar.php'?>


<section id="info" >
  <form  method="POST" enctype="multipart/form-data" class="row my-5 p-3 align-items-center justify-content-center bg-light gy-3">
    <div class="col-lg-3 col-md-6 col-sm-6">
      <img v-bind:src="'data:image/png;base64,'+image" alt="avatar" class="img-fluid">
      <input type="file" name="image-file" class="form-control mt-2"/>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6">
        <input type="text" v-if="role=='ADMIN'" v-show="false" name='mid' v-bind:value="member.mid">
        <input type="text" v-if="role=='ADMIN'" v-show="false" name='uid' v-bind:value="member.uid">
        <input type="text" v-show="false" name='img' v-bind:value="member.img">

        <label for="name" class="form-label">Name</label>
        <div class="input-group">
          <span class="input-group-text">
            <i class="bi bi-card-text"></i>
          </span>
          <input type="text" class="form-control" name='name' v-bind:value="member.name">
        </div>
        <label for="email" class="form-label">Email</label>
        <div class="input-group">
          <span class="input-group-text">
            <i class="bi bi-at"></i>
          </span>
          <input type="text" class="form-control" name='email' v-bind:value="member.email">
        </div>
        <label for="phone" class="form-label">Phone</label>
        <div class="input-group">
          <span class="input-group-text">
            <i class="bi bi-phone"></i>
          </span>
          <input type="text" class="form-control" name='phone' v-bind:value="member.phone">
        </div>
        <label for="address" class="form-label">Address</label>
        <div class="input-group">
          <span class="input-group-text">
            <i class="bi bi-geo-alt-fill"></i>
          </span>
          <input type="text" class="form-control" name='address' v-bind:value="member.address">
        </div>
        <label for="birth" class="form-label">Birthday</label>
        <div class="input-group">
          <span class="input-group-text">
            <i class="bi bi-calendar4"></i>
          </span>
          <input type="text" class="form-control" name='birth' v-bind:value="member.birth">
        </div>
        <div class="text-center m-3">
          <button type="submit" class="btn btn-primary" formaction="/save-info">
            <i class="bi bi-pencil"></i>
          </button>
          <button v-if="role=='ADMIN'" type="submit" class="btn btn-secondary" formaction="/delete-member">
            <i class="bi bi-trash"></i>
          </button>
        </div>
    </div>
  </form>
</section>

<script type="text/javascript" > 
  <?php include __TEMPLATE__."script".DL."member-info.js" ?>
</script>
<?php include __TEMPLATE__.'html\\footer.html'?>