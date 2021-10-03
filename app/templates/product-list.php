<?php include __TEMPLATE__.'logout.php'?>
Product List
<br>
<?php foreach($boardgames as $boardgame): ?>
  <div id="<?=$boardgame->get_bid()?>">
  <h4>name: <?=$boardgame->get_name() ?> </h4>
  </div>
<?php endforeach?>