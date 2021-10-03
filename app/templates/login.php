<form action='/do-login' method="POST">
Username:<input type="text" id="username" name="username">
<br>
Password:<input type="password" id="password" name="password">
<br>
<input type="submit">
</form>
<?= $_SESSION['LOGIN-ERROR'] ?? null?>