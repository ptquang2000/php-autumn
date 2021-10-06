<?php include __TEMPLATE__.'html\\head.html'?>
<?php include __TEMPLATE__.'html\\navbar.php'?>

<section id="main-container" class="container-lg m-5">
  <div id="filter" class="row navbar navbar-expand navbar-light bg-light">
    <div class="container-fluid">
      <a class="navbar-brand" v-bind:href="'/product-list?'+option('level','1')">Level 1</a>
      <a class="navbar-brand" v-bind:href="'/product-list?'+option('level','2')">Level 2</a>
      <a class="navbar-brand" v-bind:href="'/product-list?'+option('level','3')">Level 3</a>
      <a class="navbar-brand" v-bind:href="'/product-list?'+option('level','')">All Level</a>
      <div class="justify-content-end collapse navbar-collapse" id="sort">
        <ul class="navbar-nav">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Sort by
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
              <li><a class="dropdown-item" v-bind:href="'/product-list?'+option('price','asc')">Low Price</a></li>
              <li><a class="dropdown-item" v-bind:href="'/product-list?'+option('price','desc')">High Price</a></li>
              <li><a class="dropdown-item" v-bind:href="'/product-list?'+option('name','asc')">Name A-Z</a></li>
              <li><a class="dropdown-item" v-bind:href="'/product-list?'+option('name','desc')">Name Z-A</a></li>
            </ul>
          </li>
        </ul>
      </div>
      <form class="d-flex">
        <input class="form-control me-2" name="search" type="search" placeholder="Search by name" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">Search</button>
      </form>
    </div>
  </div>

  <div id="boardgames-container" class="row mt-5 mb-5 g-5">
    <div class="card-group col-lg-3 col-md-4 col-sm-5 col-8" v-for="(boardgame, idx) in boardgames" v-bind:id="boardgame.bid" v-cloak>
      <div class="card border-2" style="width: 18rem;">
        <div class="card-header bg-white h-100 border-0 d-flex align-items-start justify-content-between">
          <h5>{{boardgame.name}}</h5>
          <button type="submit" v-if="fav && favs[idx]" @click="delete_fav(idx)" class="btn btn-primary">
            <i class="bi bi-bookmark-fill"></i>
          </button>
          <button type="submit" v-else-if="role=='MEMBER'" @click="add_fav(idx)" class="btn btn-primary"> 
            <i class="bi bi-bookmark"></i>
          </button>
        </div>
        <div class="card-body">
          <img class="card-img-top" v-on:click="redirect(boardgame.bid)" v-bind:src="'img/' + boardgame.img"/>
          <p class="card-text mt-2 d-flex align-items-center justify-content-center">{{boardgame.price}} VND</p>
        </div>
      </div>
    </div>
  </div>

</section>

<script type="text/javascript" src="/script/product-list.js"></script>
<?php include __TEMPLATE__.'html\\footer.html'?>