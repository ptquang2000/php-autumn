Product detail
<br>
<?php
echo '<div id="'.$boardgame->get_bid().'">';
echo '<h4>name: ' . $boardgame->get_name() . '</h4>';
if (!$boardgame->get_age_max())
  echo '<h4>age: >=' . $boardgame->get_age_min() . '</h4>';
else
  echo '<h4>' . $boardgame->get_age_min().'-'.$boardgame->get_age_max() . '</h4>';
echo '<h4>player: ' . $boardgame->get_player_min().'-'.$boardgame->get_player_max() .'</h4>';
echo '<h4>time: ' . $boardgame->get_time_min().'-'.$boardgame->get_time_max() .'</h4>';
echo '<h4>level: ' . $boardgame->get_level() . '</h4>';
echo '<h4>price: ' . $boardgame->get_price() . '</h4>';
echo '</div>';
?>