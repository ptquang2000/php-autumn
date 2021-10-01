<?php

namespace Core;

interface UserDetails 
{
  public function get_authority();
  public function get_password();
  public function get_username();
  public function is_account_expired();
  public function is_account_non_locked();
  public function is_credentials_non_expired();
}

?>