<?php
declare (strict_types = 1);

namespace App\Services;

use App\Exception\WorkException;
use App\Constants\ErrorCode;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Redis\Redis;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Contract\StdoutLoggerInterface;
use App\Tasks\MongoClientTask;
use MongoDB\BSON\ObjectId;

class BaseService
{
    protected $oLogger = null;

    #[Inject]
    protected Redis $oRedis;

    #[Inject]
    protected StdoutLoggerInterface $oStdLogger;

    public function __construct(LoggerFactory $oLoggerFactory) 
    {
        $this->oLogger = $oLoggerFactory->get();
    }

	public function getRedisTokenKey($sToken)
    {
        return sprintf(config('user.token_key'), $sToken);
    }

    public function getRedisUserIdTokenKey($iUserId)
    {
        return sprintf(config('user.user_id_token_key'), $iUserId);
    }
	
	public function getUserIdByToken($sToken): int
	{
        $sTokenKey = $this->getRedisTokenKey($sToken);
        return (int) $this->oRedis->get($sTokenKey);
	}

	public function checkTokenOrFail($sToken)
    {
        if (! $this->checkToken($sToken)) {
            throw new WorkException(ErrorCode::USER_TOKEN_ERROR);
        }
    }

    public function checkToken($sToken)
    {
        $sTokenKey = $this->getRedisTokenKey($sToken);
        return $this->oRedis->exists($sTokenKey);
    }

    public function getFdDataByFd($iFd)
    {
        $sCollecName = config('room.user_fd_collect');
        return $this->oMongo->find($sCollecName, ['fd' => $iFd]);
    }

    public function getFdDataByUserId(int $iUserId)
    {
        $sCollecName = config('room.user_fd_collect');
        $aUserFdData = $this->oMongo->find($sCollecName, ['user_id' => $iUserId]);
        return $aUserFdData;
    }

    public function getRoomDataByRoomId($sRoomId)
    {
        $sCollecName = config('room.rooms_collect');
        $aRoomData = $this->oMongo->find($sCollecName, ['_id' => new ObjectId($sRoomId)]);
        return $aRoomData;
    }

    public function getRoomUsersDataByRoomId(string $sRoomId, int $iUserId)
    {
        $sCollecName = config('room.room_users_collect');
        $aData = $this->oMongo->find($sCollecName, ['room_id' => $sRoomId, 'id' => (int)$iUserId]);
        return $aData;
    }

    public function getTokenByUserId($iUserId)
    {
        $sUserIdTokenKey = $this->getRedisUserIdTokenKey($iUserId);
        return $this->oRedis->get($sUserIdTokenKey);
    }
}
