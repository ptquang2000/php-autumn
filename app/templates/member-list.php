<?php include __TEMPLATE__.'html\\head.html'?>
<?php include __TEMPLATE__.'html\\navbar.php'?>

<section id="members" class="bg-light">
  <div class="container-lg">
    <div class="text-center mb-5">
      <h2 class="display-4">
        Members List
      </h2>
    </div>
    <div class="row justify-content-center text-center mx-5">
      <div class="col-lg-2 col-md-2 h2 fw-bold">username</div>
      <div class="col-lg-4 col-md-4 h2 fw-bold">email</div>
      <div class="col-lg-4 col-md-4 h2 fw-bold">phone</div>
      <div class="col-lg-1 col-md-1 h2 fw-bold"></div>
    </div>
    <div class="row justify-content-center text-center mx-5 my-4 py-4" v-for="member in members">
      <div class="col-lg-2 col-md-2 lead">
        {{member.username}}
      </div>
      <div class="col-lg-4 col-md-4 lead">{{member.email}}</div>
      <div class="col-lg-4 col-md-4 lead">{{member.phone}}</div>
      <i v-on:click="redirect(member.mid)" class="col-lg-1 col-md-1 bi bi-arrow-right-square-fill fa-lg"></i>
    </div>
  </div>
</section>

<script type="text/javascript" src="/script/member-list.js"></script>
<?php include __TEMPLATE__.'html\\footer.html'?>