<?php

namespace App\PHP;

use Core\{RestController, RequestMapping, RequestMethod, Autowired};

#[RestController]
class MemberController 
{
  #[Autowired]
  private MemberService $member_service; 
  #[Autowired]
  private BoardgameService $boardgame_service; 
  #[Autowired]
  private FavouriteService $favourite_service; 

  #[RequestMapping(value: '/member', method: RequestMethod::GET)]
  public function get_member_id()
  {
    if ($_SESSION['USER']->get_authority() == 'ADMIN')
    {
      $url = $_SERVER['HTTP_REFERER'];
      $parts = explode('/', $url);
      $mid = $parts[array_key_last($parts)];
      return $this->member_service->get_member($mid);
    }
    return $this->member_service->get_member_by_uid($_SESSION['USER']->get_uid())[0];
  }

  #[RequestMapping(value: '/members', method: RequestMethod::GET)]
  public function get_members()
  {
    return array_map([$this, 'load_username'],$this->member_service->get_all_members());
  }

  public function load_username($member)
  {
    $obj = new \stdClass();
    $obj->mid = $member->get_mid();
    $obj->username = $member->get_user()->get_username();
    $obj->email = $member->get_email();
    $obj->phone = $member->get_phone();
    $obj->role = $member->get_user()->get_role();
    return $obj;
  }

  #[RequestMapping(value: '/member/boardgames', method: RequestMethod::GET)]
  public function get_member_fav()
  {
    if ($_SESSION['USER']->get_authority() == 'ADMIN')
    {
      $url = $_SERVER['HTTP_REFERER'];
      $parts = explode('/', $url);
      $mid = $parts[array_key_last($parts)];
      $favs = $this->favourite_service->get_favourite_by_mid($mid);
      return array_map([$this, 'load_fav_boardgames'], $favs);
    }
    $mid = $this->member_service->get_member_by_uid($_SESSION['USER']->get_uid())[0]->get_mid();
    $favs = $this->favourite_service->get_favourite_by_mid($mid);
    return array_map([$this, 'load_fav_boardgames'], $favs);
  }

  #[RequestMapping(value: '/user-role', method: RequestMethod::GET)]
  function get_role()
  {
    $role = new \stdClass();
    $role->role = isset($_SESSION['USER']) ? $_SESSION['USER']->get_authority() : 'ANONYMOUS';
    return $role;
  }

  private function load_fav_boardgames($fav)
  {
    return $this->boardgame_service->get_boardgame($fav->get_bid());
  }

  #[RequestMapping(value: '/member/img', method: RequestMethod::GET)]
  public function get_member_image()
  {
    $obj = new \stdClass();
    if ($_SESSION['USER']->get_authority() == 'ADMIN')
    {
      $url = $_SERVER['HTTP_REFERER'];
      $parts = explode('/', $url);
      $mid = $parts[array_key_last($parts)];
      $img = $this->member_service->get_member($mid)->get_img();
      if (file_exists(__IMAGE__.$img) && !empty($img))
      {
        $obj->image = base64_encode((file_get_contents(__IMAGE__.$img)));
        return $obj;
      }
    }
    $mid = $this->member_service->get_member_by_uid($_SESSION['USER']->get_uid())[0]->get_mid();
    $img = $this->member_service->get_member($mid)->get_img();
    if (file_exists(__IMAGE__.$img) && !empty($img))
    {
      $obj->image = base64_encode((file_get_contents(__IMAGE__.$img)));
      return $obj;
    }
  }
}
?>