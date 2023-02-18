<?php
declare (strict_types = 1);

namespace App\Repositories;

use Hyperf\Di\Annotation\Inject;
use App\Model\Friend;
use App\Exception\WorkException;
use App\Constants\ErrorCode;

class FriendRepo extends BaseRepo
{
    #[Inject]
	protected Friend $oFriend;

	public function create($iUserId, $iFriendUserId)
	{
		return $this->oFriend
			->create([
				'user_id' => $iUserId, 
				'friend_user_id' => $iFriendUserId
			]);
	}

	public function delete($iUserId, $iFriendUserId)
	{
		return $this->oFriend
			->where('user_id', $iUserId)
			->where('friend_user_id', $iFriendUserId)
			->delete();
	}

	public function getByUserIdWithPage($iUserId, $iPage, $iSize)
	{
	 	return $this->oFriend->where('user_id', $iUserId)->get();
	}
}