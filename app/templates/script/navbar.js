var login = <?= json_encode(isset($_SESSION['USER'])) ?>;
var role = <?= isset($_SESSION['USER']) ? json_encode($_SESSION['USER']->get_authority()) : "'ANONYMOUS'"?>;

new Vue(
  {
    el: '#navbar',
    data: {
      login: login,
      url: window.location.pathname
    }
  }
)