<?php
declare (strict_types = 1);

namespace App\Services\Api;

use Hyperf\Di\Annotation\Inject;
use Exception;
use App\Repositories\UserRepo;
use App\Repositories\FansRepo;
use App\Repositories\FriendRepo;
use Hyperf\Redis\Redis;
use App\Exception\WorkException;
use App\Constants\ErrorCode as Code;
use Hyperf\DbConnection\Db;

class UserService extends BaseApiService
{
    #[Inject]
    protected UserRepo $oUserRepo;
    
    #[Inject]
    protected FansRepo $oFansRepo;
    
    #[Inject]
    protected FriendRepo $oFriendRepo;

    public function register($sUsername, $sPasssword)
    {
        $oUser = $this->oUserRepo->findByUsername($sUsername);
        if ($oUser) {
            throw new WorkException(Code::USER_USERNAME_REPEAT_ERROR);
        }

        $sHashPwd = $this->makePasswordHash($sUsername, $sPasssword);
        $oUser    = $this->oUserRepo->create($sUsername, $sHashPwd);
        $sToken   = $this->makeToken($oUser);
        $this->setToken($sToken, $oUser->id);
        return $sToken;
    }

    public function login($sUsername, $sPasssword)
    {
        $oUser = $this->oUserRepo->findByUsername($sUsername);
        if (!$oUser) {
            throw new WorkException(Code::USER_LOGIN_USERNAME_OR_PASSWORD_ERROR);
        }

        $sHashPwd = $this->makePasswordHash($sUsername, $sPasssword);
        if ($sHashPwd != $oUser->password) {
            throw new WorkException(Code::USER_LOGIN_USERNAME_OR_PASSWORD_ERROR);
        }

        $sToken = $this->makeToken($oUser);

        $this->flushToken($oUser->id);
        $this->setToken($sToken, $oUser->id);

        return $sToken;
    }

    public function makePasswordHash($sUsername, $sPasssword)
    {
        return hash('sha512', $sUsername . $sPasssword . md5($sPasssword));
    }

    public function makeToken($oUser)
    {
        return hash('sha512', $oUser->id . $oUser->username . md5((string) microtime(true)));
    }

    public function setToken($sToken, $iUserId)
    {
        $sTokenKey       = $this->getRedisTokenKey($sToken);
        $sUserIdTokenKey = $this->getRedisUserIdTokenKey($iUserId);
        $this->oRedis->setex($sTokenKey, config('user.token_expire_time'), $iUserId);
        $this->oRedis->setex($sUserIdTokenKey, config('user.token_expire_time'), $sToken);
    }

    public function flushToken($iUserId)
    {
        $sUserIdTokenKey = $this->getRedisUserIdTokenKey($iUserId);
        $sToken          = $this->oRedis->get($sUserIdTokenKey);
        $sTokenKey       = $this->getRedisTokenKey($sToken);
        if ($sToken) {
            $this->oRedis->del($sTokenKey);
            $this->oRedis->del($sUserIdTokenKey);
        }
    }

    public function refreshToken($iUserId)
    {
        $oUser  = $this->oUserRepo->findById($iUserId);
        $sToken = $this->makeToken($oUser);
        $this->flushToken($iUserId);
        $this->setToken($sToken, $iUserId);
        return $sToken;
    }

    public function addTokenExpireTime($sToken)
    {
        $iUserId         = $this->getUserIdByToken($sToken);
        $sTokenKey       = $this->getRedisTokenKey($sToken);
        $sUserIdTokenKey = $this->getRedisUserIdTokenKey($iUserId);
        $this->oRedis->expire($sTokenKey, config('user.token_expire_time'));
        $this->oRedis->expire($sUserIdTokenKey, config('user.token_expire_time'));
    }

    public function addTokenExpireTimeByFd($iFd)
    {
        $iUserId = $this->getFdDataByFd($iFd)['user_id'] ?? -1;
        $sToken  = $this->getTokenByUserId($iUserId);
        $this->addTokenExpireTime($sToken);
    }

    public function logout($iUserId)
    {
        // 清token
        $this->flushToken($iUserId);
    }

    public function subscriptList($iUserId, $iPage, $iSize)
    {
        $aFans = $this->oFansRepo->getByFansUserIdWithPage($iUserId, $iPage, $iSize);
        $aSubscriptedUserIds = $aFans['list']->pluck('user_id');
        $aUsers = $this->oUserRepo->getByIds($aSubscriptedUserIds, ['id', 'username']);
        
        $aResult = $aFans;
        $aResult['list'] = $aUsers;
        return $aResult;
    }

    public function subscript($iUserId, $iFansUserId)
    {
        Db::transaction(function () use ($iUserId, $iFansUserId) {

            // 確認是否重複
            $bCheck = $this->oFansRepo->checkSubscript($iUserId, $iFansUserId);
            if ($bCheck) {
                throw new WorkException(Code::USER_SUBSCRIPT_REPEAT);
            }

            // 新增關注
            $this->oFansRepo->subscript($iUserId, $iFansUserId);
            $bSubscripted = $this->oFansRepo->checkSubscript($iFansUserId, $iUserId);            
            if ($bSubscripted) {
                $this->oFriendRepo->create($iUserId, $iFansUserId);
                $this->oFriendRepo->create($iFansUserId, $iUserId);
            }

        });

        return true;
    }

    public function cancelSubscript($iUserId, $iFansUserId)
    {
        Db::transaction(function () use ($iUserId, $iFansUserId) {
            // 刪除關注
            $bDbresult = $this->oFansRepo->unSubscript($iUserId, $iFansUserId);
            if ($bDbresult) {
                $this->oFriendRepo->delete($iUserId, $iFansUserId);
                $this->oFriendRepo->delete($iFansUserId, $iUserId);
            }
        });
        return true;
    }

    public function fans($iUserId, $iPage, $iSize)
    {
        $oFans = $this->oFansRepo->getByUserIdWithPage($iUserId, $iPage, $iSize);
        $aUserIds = $oFans->pluck('fans_user_id');
        $aUsers = $this->oUserRepo->getByIds($aUserIds, ['id', 'username']);
        return $aUsers;
    }

    public function friends($iUserId, $iPage, $iSize)
    {
        $oFriends = $this->oFriendRepo->getByUserIdWithPage($iUserId, $iPage, $iSize);
        $aUserIds = $oFriends->pluck('friend_user_id');
        $aUsers = $this->oUserRepo->getByIds($aUserIds, ['id', 'username']);
        return $aUsers;
    }

}
