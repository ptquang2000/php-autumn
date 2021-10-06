<?php include __TEMPLATE__.'html\\head.html'?>
<?php include __TEMPLATE__.'html\\navbar.php'?>

<section id="main-container" class="m-5">
  <!-- Admin -->
  <div v-if="role=='ADMIN'" class="container-lg">
    <form method="POST" enctype="multipart/form-data" class="form-inline row justify-content-center align-items-center">
      <input name="bid" class="d-none" v-bind:value="boardgame.bid"/>
      <input name="img" class="d-none" v-bind:value="boardgame.img"/>
      <div class="col-md-6 text-center">
        <img class="img-fluid" v-bind:src="'img/' + boardgame.img"/>
      </div>
      <div class="col-md-6 justify-content-center">
        <label for="name" class="form-label">Name</label>
        <input name="name" class="form-control" v-bind:value="boardgame.name"/>
        <label class="form-label">Age</label>
        <div class="input-group">
          <span class="input-group-text">Max</span>
          <input class="form-control" name="age_max" v-bind:value="boardgame.age_max"/>
          <span class="input-group-text">Min</span>
          <input class="form-control" name="age_min" v-bind:value="boardgame.age_min"/>
        </div>
        <label class="form-label">PLayers</label>
        <div class="input-group">
          <span class="input-group-text">Max</span>
          <input class="form-control" name="player_max" v-bind:value="boardgame.player_max"/>
          <span class="input-group-text">Min</span>
          <input class="form-control" name="player_min" v-bind:value="boardgame.player_min"/>
        </div>
        <label class="form-label">Time</label>
        <div class="input-group">
          <span class="input-group-text">Max</span>
          <input class="form-control" name="time_max" v-bind:value="boardgame.time_max"/>
          <span class="input-group-text">Min</span>
          <input class="form-control" name="time_min" v-bind:value="boardgame.time_min"/>
        </div>
        <label for="" class="form-label">Level</label>
        <input class="form-control" name="level" v-bind:value="boardgame.level"/>
        <label for="" class="form-label">Price</label>
        <input class="form-control" name="price" v-bind:value="boardgame.price"/>
        <label for="image-file" class="form-label">File</label>
        <input type="file" name="image-file" class="form-control"/>
      </div>
      <div class="mb-2 mt-2 text-center">
        <button type="submit" class="btn btn-success" formaction="edit-product">
          <i class="bi bi-pencil"></i>
        </button>
        <button type="submit" class="btn btn-danger" formaction="delete-product">
          <i class="bi bi-trash"></i>
        </button>
      </div>
    </form>
  </div>
  <!-- Normal user -->
  <div v-else class="container-lg">
    <div class="row justify-content-center align-items-center">
      <div class="col-md-6 text-center">
        <img class="img-fluid" v-bind:src="'img/' + boardgame.img"/>
      </div>
      <div class="col-md-6 text-center">
        <h1>
          <form method="POST" @submit.prevent>
            <button type="submit" v-if="fav" @click="delete_fav" class="btn btn-primary">
              <i class="bi bi-bookmark-fill"></i>
            </button>
            <button type="submit" v-else-if="role=='MEMBER'" @click="add_fav" class="btn btn-primary">
              <i class="bi bi-bookmark"></i>
            </button>
          </form>
          <div class="display-3">{{boardgame.name}}</div>
          <div class="display-5 fw-bold">{{boardgame.price}} VND</div>
          <div class="display-5 text-muted" v-if="boardgame.age_max == 0"><i class="fas fa-greater-than-equal fa-xs"></i> {{boardgame.age_min}} age</div>
          <div class="display-5 text-muted" v-else>{{boardgame.age_min}}-{{boardgame.age_max}} age</div>
          <div class="display-5 text-muted">
            <i class="bi bi-person"></i>
            {{boardgame.player_min}}-{{boardgame.player_max}} players
          </div>
          <div class="display-5 text-muted">
            <i class="bi bi-hourglass-bottom"></i>
            {{boardgame.time_min}}-{{boardgame.time_max}} minutes
          </div>
          <div class="display-5 text-muted"> 
            Level:
            <i v-for="index in boardgame.level" class="bi bi-star-fill">
            </i><i v-for="index in (3-boardgame.level)" class="bi bi-star"></i>
          </div>
        </h1>
      </div>
    </div>
  </div>
</section>

<section id="comments-container">
  <div class="container-lg bg-light p-4">

    <!-- Admin -->
    <div v-if="role=='ADMIN'" class="row justify-content-center">
      <div class="col-8">
        <form method="POST" v-for="(comment, idx) in comments">
          <input type="text" class="d-none" name="cid" v-model="comments[idx].cid"/>
          <label for="content" class="lead fw-bold form-label ms-2">{{comment.username}}</label>
          <div class="input-group">
            <input type="text" name="content" class="form-control" v-model="comments[idx].content"/>
            <button type="submit" @click="edit_comment($event, idx)">
              <i class="bi bi-pencil"></i>
            </button>
            <button type="submit" @click="delete_comment($event, idx)">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Normal user -->
    <div v-else class="row justify-content-center">
      <div class="col-8">
        <div class="list-group">
          <div class="list-group-item py-2" v-for="comment in comments">
            <h5 class="lead fw-bold mb-1 ms-2">{{comment.username}}</h5>
            <p class="mb-1">{{comment.content}}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Member only -->
    <form v-if="role=='MEMBER'" @submit="submit_comment" class="row justify-content-center mt-2" action="/add-comment" method="POST">
      <div class="col-8">
        <div class="input-group">
          <input type="text" class="form-control" name="content" v-model="content"/>
          <button type="submit" class="btn btn-secondary">Comment</button>
        </div>
      </div>
    </form>

  </div>
</section>


<script type="module" > 
  <?php include __TEMPLATE__."script".DL."product-detail.js" ?>
</script>
<?php include __TEMPLATE__.'html\\footer.html'?>