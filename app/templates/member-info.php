Member Info
<br>
<form action="/save-info" method="POST">
<input type="text" name='mid' value="<?= $member['mid']?>">
<br>
<input type="text" name='username' value="<?= $member['username']?>">
<br>
<input type="text" name='email' value="<?= $member['email']?>">
<br>
<input type="text" name="phone" value="<?= $member['phone']?>">
<br>
<input type="text" name="address" value="<?= $member['address']?>">
<br>
<input type="text" name="birth" value="<?= $member['birth']?>">
<br>
<input type="text" name="img" value="<?= $member['img']?>">
<br>
<input type="submit" value="save">
</form>