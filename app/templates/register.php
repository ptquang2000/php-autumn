Register Page
<br>
<form method="POST">
  username<input type="text" name="username">
  <br>
  password<input type="password" name="password">
  <br>
  <input type="submit" formaction="do-register" name="register">
</form>
<?php if (isset($register_error)):?>
<?= $register_error ?>
<?php endif?>