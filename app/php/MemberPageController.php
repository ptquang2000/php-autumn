<?php

namespace App\PHP;
use Core\{Controller, RequestMapping, RequestMethod, Autowired};

#[Controller]
class MemberPageController
{
  #[Autowired]
  private MemberService $member_service; 
  #[Autowired]
  private MyUserDetailsService $userdetails_service; 

  #[RequestMapping(value: '/member-info', method: RequestMethod::GET)]
  function get_member_info()
  {
    if ($_SESSION['USER']->get_authority() == 'ADMIN')
      return 'Location: /member-list';
    return 'member-info.html';
  }

  #[RequestMapping(value: '/save-user', method: RequestMethod::POST)]
  function post_save_user()
  {
    $url = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
    $user = form_model('User');

    if ($_SESSION['USER']->get_authority() == 'ADMIN'){
      $this->userdetails_service->save_user($user);
      return 'Location: '.$url.'?error=Thay đổi quyền thành công!!';
    }
    if ($_SESSION['USER']->get_authority() == 'MEMBER'){
      if (! password_verify(htmlspecialchars_decode($_POST['old_password']), $_SESSION['USER']->get_password())){
        return 'Location: '.$url.'?error=Nhập sai mật khẩu';
      }

      if (htmlspecialchars_decode($_POST['password']) != htmlspecialchars_decode($_POST['retype_password'])){
        return 'Location: '.$url.'?error=Mật khẩu không trùng khớp';
      }

      try{
        $this->userdetails_service->save_member($user);
        $user = $this->userdetails_service->load_user_by_username($_SESSION['USER']->get_username());
        if ($user)
          $_SESSION['USER'] = $user;
      }catch (\mysqli_sql_exception $e)
      {
        if (preg_match('/^Duplicate entry \'.*\' for key \'username\'$/', $e->getMessage()) == 1)
          return 'Location: '.$url.'?error=Tên đăng nhập đã được sử dụng';
        throw $e;
      }
      return 'Location: '.$url.'?error=Đổi mật khẩu thành công!!';
    }
    

    
  }

  #[RequestMapping(value: '/save-info', method: RequestMethod::POST)]
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

}

?>