<?php

namespace App\PHP;
use Core\{Controller, RequestMapping, RequestMethod, EnableSecurity, Autowired};

#[Controller]
class MemberPageController
{
  #[Autowired]
  private MemberService $member_service; 
  #[Autowired]
  private MyUserDetailsService $userdetails_service; 

  #[RequestMapping(value: '/member-info', method: RequestMethod::GET)]
  #[EnableSecurity(role: ['MEMBER', 'ADMIN'])]
  function get_member_info()
  {
    if ($_SESSION['USER']->get_authority() == 'ADMIN')
      return 'Location: /member-list';
    return 'member-info.php';
  }

  #[RequestMapping(value: '/save-user', method: RequestMethod::POST)]
  #[EnableSecurity(role: ['MEMBER', 'ADMIN'])]
  function post_save_user()
  {
    $url = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
    $user = form_model('User');
    if ($_SESSION['USER']->get_authority() == 'ADMIN')
      $this->userdetails_service->save_user($user);
    if ($_SESSION['USER']->get_authority() == 'MEMBER')
      try{
        $this->userdetails_service->save_member($user);
      }catch (\mysqli_sql_exception $e)
      {
        if (preg_match('/^Duplicate entry \'.*\' for key \'username\'$/', $e->getMessage()) == 1)
          return 'Location: '.$url.'?error=Username has already been used';
        throw $e;
      }
    return 'Location: '.$url;
  }

  #[RequestMapping(value: '/save-info', method: RequestMethod::POST)]
  #[EnableSecurity(role:['MEMBER', 'ADMIN'])]
  function post_save_info()
  {
    $member = form_model('Member');
    if ($_SESSION['USER']->get_authority() == 'MEMBER')
    {
      $uid = $_SESSION['USER']->get_uid();
      $mid = $this->member_service->get_member_by_uid($uid)[0]->get_mid();
      $member->set_uid($uid);
      $member->set_mid($mid);
    }

    if (is_uploaded_file($_FILES['image-file']['tmp_name']) &&
    getimagesize($_FILES['image-file']['tmp_name']))
    {
      if (file_exists(__IMAGE__.$member->get_img()))
        unlink(__IMAGE__.$member->get_img());
      $file_type = '.'.pathinfo($_FILES['image-file']['name'], PATHINFO_EXTENSION);
      $file_name = $member->get_mid().$file_type;
      move_uploaded_file($_FILES['image-file']['tmp_name'], __IMAGE__.$file_name);
      $member->set_img($file_name);
    }

    // save member and redirect
    $member = $this->member_service->save_member($member);
    if ($_SESSION['USER']->get_authority() == 'ADMIN')
      return 'Location: /member-info/'.$member->get_mid();
    return 'Location: /member-info';
  }

  #[RequestMapping(value: '/member/img', method: RequestMethod::GET)]
  #[EnableSecurity(role:['MEMBER', 'ADMIN'])]
  public function get_member_image()
  {
    if ($_SESSION['USER']->get_authority() == 'ADMIN')
    {
      $url = $_SERVER['HTTP_REFERER'];
      $parts = explode('/', $url);
      $mid = $parts[array_key_last($parts)];
      $img = $this->member_service->get_member($mid)->get_img();
      if (file_exists(__IMAGE__.$img) && !empty($img))
        return base64_encode((file_get_contents(__IMAGE__.$img)));
    }
    $mid = $this->member_service->get_member_by_uid($_SESSION['USER']->get_uid())[0]->get_mid();
    $img = $this->member_service->get_member($mid)->get_img();
    if (file_exists(__IMAGE__.$img) && !empty($img))
      return base64_encode((file_get_contents(__IMAGE__.$img)));
  }

}

?>