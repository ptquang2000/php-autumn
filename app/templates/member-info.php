Member Info
<br>
<?php

echo '<h4>mid: '.$model->attribute('member')->get_mid().'</h4>';
echo '<h4>uid: '.$model->attribute('member')->get_username().'</h4>';
echo '<h4>name: '.$model->attribute('member')->get_name().'</h4>';
echo '<h4>email: '.$model->attribute('member')->get_email().'</h4>';
echo '<h4>phone: '.$model->attribute('member')->get_phone().'</h4>';
echo '<h4>address: '.$model->attribute('member')->get_address().'</h4>';
echo '<h4>birth: '.$model->attribute('member')->get_birth().'</h4>';
echo '<h4>img: '.$model->attribute('member')->get_img().'</h4>';

?>