<?php
declare (strict_types = 1);

namespace App\Repositories;

use Hyperf\Di\Annotation\Inject;
use App\Model\User;
use App\Exception\WorkException;
use App\Constants\ErrorCode;

class UserRepo extends BaseRepo
{

    #[Inject]
	protected User $oUser;

	public function findByUsername($sUsername)
	{
		return $this->oUser->where('username', $sUsername)->first();
	}

	public function findById($iId)
	{
		return $this->oUser->where('id', $iId)->first();
	}

	public function findByIdOrFail($iId)
	{
		$oUser = $this->oUser->where('id', $iId)->first();
		if (! $oUser) {
			throw new WorkException(ErrorCode::USER_NOT_FOUND_ERROR);
		}
	}

	public function create($sUsername, $sPassword): User
	{
		$oUser = new $this->oUser();
		$oUser->username = $sUsername;
		$oUser->password = $sPassword;
		$bDbResult = $oUser->save();
		if (! $bDbResult) {
			throw new WorkException(ErrorCode::USER_CREATE_ERROR);
		}
		return $oUser;
	}

	public function getByIds($aUserIds, $aColumn = ['*'])
	{
		return $this->oUser->select($aColumn)->whereIn('id', $aUserIds)->get();
	}

}
