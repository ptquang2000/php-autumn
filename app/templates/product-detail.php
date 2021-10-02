Product detail
<br>
<?php if (isset($_SESSION['USER']) && $_SESSION['USER']->get_authority() == 'ADMIN'): ?>
  <form id="<?=$boardgame->get_bid();?>">
    <input type="text" name='name' value="<?= $boardgame->get_name();?>"  />
    <br>
    <input type="text" name="age_min" value="<?= $boardgame->get_age_min();?>"  />
    <br>
    <input type="text" name="age_max" value="<?= $boardgame->get_age_max();?>"  />
    <br>
    <input type="text" name="player_min" value="<?=$boardgame->get_player_min();?>" />
    <br>
    <input type="text" name="player_max" value="<?=$boardgame->get_player_max();?>" />
    <br>
    <input type="text" name="time_min" value="<?= $boardgame->get_time_min()?>" />
    <br>
    <input type="text" name="time_max" value="<?= $boardgame->get_time_max()?>" />
    <br>
    <input type="text" name="level" value="<?= $boardgame->get_level();?>"  />
    <br>
    <input type="text" name="price" value="<?= $boardgame->get_price();?>"  />
  </form>
<?php else: ?>
  <div id="<?=$boardgame->get_bid();?>">
    <h4> name:<?= $boardgame->get_name();?> </h4>
      <?php if (!$boardgame->get_age_max()): ?>
        <h4>>=<?=$boardgame->get_age_min()?></h4>
      <?php else: ?>
        <h4><?=$boardgame->get_age_min().'-'.$boardgame->get_age_max()?></h4>
      <?php endif; ?>
    <h4> player:<?=$boardgame->get_player_min().'-'.$boardgame->get_player_max();?> </h4>
    <h4> time:<?= $boardgame->get_time_min().'-'.$boardgame->get_time_max();?> </h4>
    <h4> level:<?= $boardgame->get_level();?>  </h4>
    <h4> price:<?= $boardgame->get_price();?>  </h4>
  </div>
<?php endif;?>
<br>
Favourite Section
<br>
<?php if (isset($_SESSION['USER']) && $_SESSION['USER']->get_authority() == 'MEMBER'): ?>
  <form id="favourite" method="POST">
    <?php if ($fid): ?>
      fid:<input type="text" name="fid" value="<?=$fid[0]->get_fid()?>"/>
      <br>
      bid:<input type="text" name="bid" value="<?=$boardgame->get_bid();?>"/>
      <br>
      mid:<input type="text" name="mid" value="<?=$mid?>"/>
      <br>
      <input type="submit" formaction="/delete-favourite" value="remove favourite"/>
    <?php else: ?>
      bid:<input type="text" name="bid" value="<?=$boardgame->get_bid();?>"/>
      <br>
      mid:<input type="text" name="mid" value="<?=$mid?>"/>
      <br>
      <input type="submit" formaction="/add-favourite" value="favourite"/>
    <?php endif; ?>
  </form>
<?php endif;?>
Comment Section
<br>
<?php foreach($comments as $comment):?>
  <div>
    <?php if (isset($_SESSION['USER']) && $_SESSION['USER']->get_authority() == 'ADMIN'): ?>
      <span><?=$comment['username']?>:</span>
      <form method="POST">
        cid:<input type="text" name="cid" value="<?=$comment['cid']?>"/>
        <br>
        bid:<input type="text" name="bid" value="<?=$boardgame->get_bid()?>"/>
        <br>
        mid:<input type="text" name="mid" value="<?=$mid?>"/>
        <br>
        content:<input type="text" name="content" value="<?=$comment['content']?>"/>
        <br>
        <input type="submit" formaction="/edit-comment" value="edit">
        <input type="submit" formaction="/delete-comment" value="delete">
      </form>
    <?php else: ?>
    <p>
      <span><?=$comment['username']?>:</span>
      <?=$comment['content']?>
    </p>
  </div>
<?php endif;?>
<?php endforeach?>
<?php if (isset($_SESSION['USER']) && $_SESSION['USER']->get_authority() == 'MEMBER'): ?>
  <form id="comment" action="/add-comment" method="POST">
    bid:<input type="text" name="bid" value="<?=$boardgame->get_bid();?>"/>
    <br>
    mid:<input type="text" name="mid" value="<?=$mid?>"/>
    <br>
    content:<input type="text" name="content"/>
    <br>
    <input type="submit" value="comment"/>
  </form>
<?php endif;?>