List User
<br>
<?php foreach($members as $member): ?>
  <div id="<?=$member['mid']?>">
    <h4>username:<?=$member['username']?></h4>
    <h4>email:<?=$member['email']?></h4>
    <h4>phone:<?=$member['phone']?></h4>
    <h4>address:<?=$member['address']?></h4>
    <h4>birth:<?=$member['birth']?></h4>
    <h4>img:<?=$member['img']?></h4>
  </div>
<?php endforeach?>
