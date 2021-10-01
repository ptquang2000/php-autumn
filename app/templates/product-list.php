Product List
<br>
<?php

foreach($model->attribute('boardgames') as $boardgame)
{
  echo '<div id="'.$boardgame->get_bid().'">';
  echo '<h4>name: ' . $boardgame->get_name() . '</h4>';
  echo '</div>';
}

?>