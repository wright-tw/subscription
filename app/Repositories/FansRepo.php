<?php
declare (strict_types = 1);

namespace App\Repositories;

use Hyperf\Di\Annotation\Inject;
use App\Model\Fans;
use App\Exception\WorkException;
use App\Constants\ErrorCode;

class FansRepo extends BaseRepo
{
    #[Inject]
	protected Fans $oFans;

	public function getByUserIdWithPage($iUserId, $iPage, $iSize)
	{
		$oQuery = $this->oFans->where('user_id', $iUserId);
	 	return $this->paginator($oQuery, $iPage, $iSize);
	} 

	public function getByFansUserIdWithPage($iFansUserId, $iPage, $iSize)
	{
	 	$oQuery = $this->oFans->where('fans_user_id', $iFansUserId);
	 	return $this->paginator($oQuery, $iPage, $iSize);
	}

	public function subscript($iUserId, $iFansUserId)
	{
		return $this->oFans
			->create(['user_id' => $iUserId, 'fans_user_id' => $iFansUserId]);
	}

	public function unSubscript($iUserId, $iFansUserId)
	{
		return $this->oFans
			->where('user_id', $iUserId)
			->where('fans_user_id', $iFansUserId)
			->delete();
	}

	public function checkSubscript($iUserId, $iFansUserId): bool
	{
		$oFans = $this->oFans
			->select(['id'])
			->where('user_id', $iUserId)
			->where('fans_user_id', $iFansUserId)
			->first();

		return $oFans != null;
	}


}