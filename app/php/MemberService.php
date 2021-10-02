<?php

namespace App\PHP;
use Core\{Service, Autowired};
use App\PHP\{MemberRepository, Member};

#[Service]
class MemberService
{
  #[Autowired]
  private MemberRepository $member_repository;

  public function get_all_members()
  {
    return $this->member_repository->find_all();
  }

  public function get_member($id)
  {
    return $this->member_repository->find_by_id($id);
  }

  public function get_member_by_uid($uid)
  {
    return $this->member_repository->find_by_uid($uid);
  }

  public function delete_member(Member $member)
  {
    return $this->member_repository->delete($member);
  }

  public function save_member(Member $member)
  {
    return $this->member_repository->save($member);
  }
}

?>