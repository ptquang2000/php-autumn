<nav id="navbar" class="navbar navbar-expand navbar-light bg-light">
  <div id="main-nav" class="navbar-collapse justify-content-end align-center">
    <ul class="navbar-nav">
        <li v-if="login" class="nav-item">
          <a href="/logout" class="nav-link">Logout</a>
        </li>
        <template v-else>
        <li v-if="url != '/login'" class="nav-item">
          <a class="nav-link" href="/login">Login</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/register">Register</a>
        </li>
        </template>
    </ul>
  </div>
</nav>

<script type="text/javascript" > 
  <?php include __TEMPLATE__."script".DL."navbar.js" ?>
</script>