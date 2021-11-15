<?php

namespace App\PHP;

use Core\{RestController, RequestMapping, RequestMethod, Autowired};

#[RestController]
class FavouriteController 
{
  #[Autowired]
  private FavouriteService $favourite_service; 
  #[Autowired]
  private MemberService $member_service; 

  #[RequestMapping(value: '/favourite', method: RequestMethod::GET)]
  public function get_favourites()
  {
    $mid = $this->member_service->get_member_by_uid($_SESSION['USER']->get_uid())[0]->get_mid();
    return $this->favourite_service->get_favourite_by_mid($mid);
  }

  #[RequestMapping(value: '/favourite/$bid', method: RequestMethod::GET)]
  public function get_favourite($bid)
  {
    $mid = $this->member_service->get_member_by_uid($_SESSION['USER']->get_uid())[0]->get_mid();
    return $this->favourite_service->get_favourite_by_member($mid, $bid)[0];
  }

  #[RequestMapping(value: '/add-favourite', method: RequestMethod::POST)]
  function post_add_favourite()
  {
    $new_favourite = form_model('Favourite');
    $member = $this->member_service->get_member_by_uid($_SESSION['USER']->get_uid())[0];
    if ($member->get_mid() == $new_favourite->get_mid())
      $new_favourite = $this->favourite_service->save_favourite($new_favourite);
    return $this->favourite_service->get_favourite_by_mid($member->get_mid());
  }

  #[RequestMapping(value: '/delete-favourite', method: RequestMethod::POST)]
  function post_delete_favourite()
  {
    $deleted_favourite = form_model('Favourite');
    $member = $this->member_service->get_member_by_uid($_SESSION['USER']->get_uid())[0];
    if ($member->get_mid() == $deleted_favourite->get_mid())
      $this->favourite_service->delete_favourite($deleted_favourite);
    return $this->favourite_service->get_favourite_by_mid($member->get_mid());
  }

}

?>