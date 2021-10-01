<?php

namespace Core;

interface UserDetailsService
{
  public function load_user_by_username($username);
}

?>