<?php include __TEMPLATE__.'html\\head.html'?>
<?php include __TEMPLATE__.'html\\navbar.php'?>


<section id="info" v-cloak>
  <div class="container d-flex align-items-start my-5">
    <!-- Tab button -->
    <div class="nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
      <button class="nav-link active" id="v-pills-profile-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profile" type="button" role="tab" aria-controls="v-pills-profile" aria-selected="true">Profile</button>
      <button class="nav-link" id="v-pills-account-tab" data-bs-toggle="pill" data-bs-target="#v-pills-account" type="button" role="tab" aria-controls="v-pills-account" aria-selected="false">Account</button>
      <button class="nav-link" id="v-pills-fav-tab" data-bs-toggle="pill" data-bs-target="#v-pills-fav" type="button" role="tab" aria-controls="v-pills-fav" aria-selected="false">Favourite</button>
    </div>
    <!-- Tab content -->
    <div class="tab-content container" id="v-pills-tabContent">
      <!-- Favourite -->
      <div class="tab-pane fade" id="v-pills-fav" role="tabpanel" aria-labelledby="v-pills-fav-tab">
        <div class="row mt-5 mb-5 g-5">
          <div class="card-group col-xl-3 col-lg-4 col-8" v-for="(boardgame, idx) in boardgames" v-bind:id="boardgame.bid">
            <div class="card border-2" style="width: 18rem;">
              <div class="card-header bg-white d-flex align-items-start justify-content-between h-100 border-0">
                <h5>{{boardgame.name}}</h5>
                <button type="submit" v-if="fav && favs[idx]" @click="delete_fav(idx)" class="btn btn-primary">
                  <i class="bi bi-bookmark-fill"></i>
                </button>
              </div>
              <div class="card-body">
                <img class="card-img-top" v-bind:src="'/img/' + boardgame.img"/>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Profile -->
      <div class="tab-pane fade show active container" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
        <form  method="POST" enctype="multipart/form-data" class="row p-3 align-items-center justify-content-center bg-light gy-3">
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
      </div>
      <!-- Account -->
      <div class="tab-pane container" id="v-pills-account" role="tabpanel" aria-labelledby="v-pills-account-tab">
        <form  method="POST" enctype="multipart/form-data" class="row p-3 align-items-center justify-content-center bg-light gy-3">
          <div class="col-lg-4 col-md-6 col-sm-6">
            <input type="text" v-if="role=='ADMIN'" v-show="false" name='uid' v-bind:value="member.uid">

            <label for="name" class="form-label">Username</label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="bi bi-file-person"></i>
              </span>
              <input type="text" class="form-control" name='username' v-bind:value="member.user.username">
            </div>
            <label v-if="role=='ADMIN'" for="phone" class="form-label">Role</label>
            <div v-if="role=='ADMIN'" class="input-group">
              <span class="input-group-text">
                <i class="bi bi-info-circle-fill"></i>
              </span>
              <select name="role" class="form-select" v-model="member.user.role">
                <option value="ROLE_ADMIN">ADMIN</option>
                <option value="ROLE_MEMBER">MEMBER</option>
              </select>
            </div>
            <label v-show="role=='MEMBER'" for="password" class="form-label">Password</label>
            <div v-show="role=='MEMBER'" class="input-group">
              <span class="input-group-text">
                <i class="bi bi-key-fill"></i>
              </span>
              <input type="password" class="form-control" name='password'>
            </div>
            <div class="text-center m-3">
              <button type="submit" class="btn btn-primary" formaction="/save-user">
                <i class="bi bi-pencil"></i>
              </button>
            </div>
            <div v-if="error" class="row justify-content-center my-0">
              <div class="col card border-0" style="max-width: 18rem;">
                <div class="card-body">
                  <p class="card-text text-center">{{error}}</p>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>

    </div>
  </div>
</section>

<script type="text/javascript" src="/script/member-info.js"></script>
<?php include __TEMPLATE__.'html\\footer.html'?>