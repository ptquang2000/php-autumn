<?php

namespace App\PHP;
use Core\{Controller, RequestMapping, RequestMethod, EnableSecurity, Autowired, Model};

#[Controller]
class MemberPageController
{
  #[Autowired]
  private MemberService $member_service; 
  #[Autowired]
  private CommentService $comment_service; 
  #[Autowired]
  private FavouriteService $favourite_service; 
  #[Autowired]
  private BoardgameService $boardgame_service; 

  #[RequestMapping(value: '/member-info', method: RequestMethod::GET)]
  #[EnableSecurity(role: ['MEMBER'])]
  function get_member_info(Model $model)
  {
    $user = $_SESSION['USER'];
    $member = $this->member_service->get_member_by_uid($user->get_uid())[0];

    $favs = $this->favourite_service->get_favourite_by_mid($member->get_mid());
    $model->add_attribute('fid', $favs);
    $boardgames = array_map([$this->boardgame_service, 'get_boardgame'], 
      array_map(function($fav){return $fav->get_bid();},$favs));
    $model->add_attribute('boardgames', $boardgames);

    $member = [
      'name' => $member->get_name(),
      'email' => $member->get_email(),
      'phone' => $member->get_phone(),
      'address' => $member->get_address(),
      'birth' => $member->get_birth(),
      'img' => $member->get_img()
    ];
    $model->add_attribute('member', $member);

    return 'member-info.php';
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

  #[RequestMapping(value: '/add-favourite', method: RequestMethod::POST)]
  #[EnableSecurity(role:['MEMBER'])]
  function post_add_favourite()
  {
    $new_favourite = form_model('Favourite');
    $member = $this->member_service->get_member_by_uid($_SESSION['USER']->get_uid())[0];
    if ($member->get_mid() == $new_favourite->get_mid())
      $new_favourite = $this->favourite_service->save_favourite($new_favourite);
    return 'Location: '.$this->last_url();
  }

  #[RequestMapping(value: '/delete-favourite', method: RequestMethod::POST)]
  #[EnableSecurity(role:['MEMBER'])]
  function post_delete_favourite()
  {
    $deleted_favourite = form_model('Favourite');
    $member = $this->member_service->get_member_by_uid($_SESSION['USER']->get_uid())[0];
    if ($member->get_mid() == $deleted_favourite->get_mid())
      $this->favourite_service->delete_favourite($deleted_favourite);
    return 'Location: '.$this->last_url();
  }

  private function last_url()
  {
    $url = parse_url($_SERVER['HTTP_REFERER']);
    $url = isset($url['query']) ? $url['path'].'?'.$url['query'] : $url['path'];
    return $url;
  }
}

?>