<?php
declare (strict_types = 1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use App\Validators\UserValidator;
use Hyperf\Di\Annotation\Inject;
use App\Libs\Log;

class UserController extends AbstractController
{
    #[Inject]
    protected UserValidator $oUserValidator;

    public function register()
    {
        $this->oUserValidator->userRegisterCheck($this->oRequest->all());
        $sUsername  = $this->oRequest->input('username');
        $sPasssword = $this->oRequest->input('password');
        $sToken     = $this->oUserService->register($sUsername, $sPasssword);
        return $this->success(['token' => $sToken]);
    }

    public function login()
    {
        $this->oUserValidator->userLoginCheck($this->oRequest->all());
        $sUsername  = $this->oRequest->input('username');
        $sPasssword = $this->oRequest->input('password');
        $sToken     = $this->oUserService->login($sUsername, $sPasssword);
        Log::info($sUsername . ' login success!');
        return $this->success(['token' => $sToken]);
    }

    public function updateToken()
    {
        $iUserId = $this->getUserId();
        $sToken  = $this->oUserService->updateToken($iUserId);
        return $this->success(['token' => $sToken]);
    }

    public function info()
    {
        return $this->success();
    }

    public function subscriptList()
    {
        $iUserId = $this->getUserId();
        $iPage  = $this->oRequest->input('page', 1); 
        $iSize = $this->oRequest->input('size', 20);
        $aData = $this->oUserService->subscriptList($iUserId, $iPage, $iSize);
        return $this->success($aData);
    }

    public function subscript()
    {
        // 用戶自己ID
        $iFansUserId = $this->getUserId(); 

        // 想訂閱的用戶ID
        $iUserId  = $this->oRequest->input('user_id'); 
        $this->oUserService->subscript($iUserId, $iFansUserId);
        return $this->success();
    }

    public function cancelSubscript()
    {
        // 用戶自己ID
        $iFansUserId = $this->getUserId();

        // 想退訂閱的用戶ID
        $iUserId  = $this->oRequest->input('user_id');
        $this->oUserService->cancelSubscript($iUserId, $iFansUserId);
        return $this->success();
    }

    public function fans()
    {
        $iUserId = $this->getUserId();
        $iPage  = $this->oRequest->input('page', 1); 
        $iSize = $this->oRequest->input('size', 20);
        $aData = $this->oUserService->fans($iUserId, $iPage, $iSize);
        return $this->success($aData);
    }

    public function friends()
    {
        $iUserId = $this->getUserId();
        $iPage  = $this->oRequest->input('page', 1); 
        $iSize = $this->oRequest->input('size', 20);
        $aData = $this->oUserService->friends($iUserId, $iPage, $iSize);
        return $this->success($aData);
    }

}
