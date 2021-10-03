<?php include __TEMPLATE__.'logout.php'?>
List Member
<br>
<?php foreach($members as $member): ?>
  <div id="<?=$member['mid']?>">
    <h4>mid:<?=$member['mid']?></h4>
    <h4>uid:<?=$member['uid']?></h4>
    <h4>username:<?=$member['username']?></h4>
    <h4>email:<?=$member['email']?></h4>
    <h4>phone:<?=$member['phone']?></h4>
    <hr>
  </div>
<?php endforeach?>
