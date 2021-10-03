<?php include __TEMPLATE__.'logout.php'?>
Member Info
<br>
<form action="/save-info" method="POST" enctype="multipart/form-data">
<?php if ($_SESSION['USER']->get_authority() == 'ADMIN'):?>
mid:<input type="text" name='mid' value="<?= $member['mid']?>">
<br>
uid:<input type="text" name='uid' value="<?= $member['uid']?>">
<br>
<?php endif?>
name:<input type="text" name='name' value="<?= $member['name']?>">
<br>
email:<input type="text" name='email' value="<?= $member['email']?>">
<br>
phone:<input type="text" name="phone" value="<?= $member['phone']?>">
<br>
address:<input type="text" name="address" value="<?= $member['address']?>">
<br>
birth:<input type="text" name="birth" value="<?= $member['birth']?>">
<br>
<?php if (file_exists(__IMAGE__.$member['img'])):?>
<img src="data:image/png;base64,
<?= base64_encode(file_get_contents(__IMAGE__.$member['img']))?>"
width="5%">
<br>
<?php endif?>
img:<input type="file" name="image-file">
<br>
<input type="submit" value="save">
<?php if ($_SESSION['USER']->get_authority() == 'ADMIN'):?>
<input type="submit" value="delete" formaction="/delete-member">
<?php endif?>
</form>