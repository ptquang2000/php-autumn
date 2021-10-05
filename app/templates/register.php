<?php include __TEMPLATE__.'html\\head.html'?>
<?php include __TEMPLATE__.'html\\navbar.php'?>


<section id="register-section" class="container-lg mt-5">
  <div class="row justify-content-center">
    <form action="/do-register" class="col-lg-4 col-6" method="POST" @submit="form_validate">
      <!-- Username -->
      <label for="username" class="form-label">Username</label>
      <div class="input-group">
        <span class="input-group-text">
          <i class="bi bi-file-person"></i>
        </span>
        <input type="text" class="form-control" name="username" v-model="username">
      </div>
      <!-- Password -->
      <label for="password" class="form-label">Password</label>
      <div class="input-group">
        <span class="input-group-text">
          <i class="bi bi-key-fill"></i>
        </span>
        <input type="password" class="form-control" name="password" v-model="password">
      </div>
      <!-- Name -->
      <label for="phone" class="form-label">Name</label>
      <div class="input-group">
        <span class="input-group-text">
          <i class="bi bi-card-text"></i>
        </span>
        <input type="text" class="form-control" name="name" v-model="name">
      </div>
      <!-- Phone number -->
      <label for="phone" class="form-label">Phone</label>
      <div class="input-group">
        <span class="input-group-text">
          <i class="bi bi-phone"></i>
        </span>
        <input type="text" class="form-control" name="phone" v-model="phone">
      </div>
      <!-- Email -->
      <label for="email" class="form-label">Email</label>
      <div class="input-group">
        <span class="input-group-text">
          <i class="bi bi-at"></i>
        </span>
        <input type="text" class="form-control" name="email" v-model="email">
      </div>
      <div class="my-2 text-center">
        <button type="submit" class="btn btn-success text-center">Register</button>
      </div>
    </form>
  </div>
  <div v-if="error" class="row justify-content-center my-0">
    <div class="col-lg-4 col-6 card border-0" style="max-width: 18rem;">
      <div class="card-body">
        <p class="card-text text-center">{{error}}</p>
      </div>
    </div>
  </div>
</section>

<script type="text/javascript" > 
  <?php include __TEMPLATE__."script".DL."register.js" ?>
</script>
<?php include __TEMPLATE__.'html\\footer.html'?>