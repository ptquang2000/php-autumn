<?php if (isset($_SESSION['USER'])): ?>
<form action="/logout" method="POST">
  <input type="submit" value="logout">
</form>
<?php else:?>
<form action="/login" method="GET">
  <input type="submit" value="login">
</form>
<?php endif ?>