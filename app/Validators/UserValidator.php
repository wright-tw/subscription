<?php
declare (strict_types = 1);

namespace App\Validators;

use Exception;
use App\Exception\WorkException;
use App\Constants\ErrorCode as Code;

class UserValidator extends AbstractValidator
{

    public function userRegisterCheck($aParams)
    {
        $oValidator = $this->oValidate->make(
            $aParams,
            [
                'username' => 'required|string',
                'password' => 'required|string',
            ],
            [
                'username.required' => 'username 為必填.',
                'password.required' => 'password 為必填.',
            ]
        );

        if ($oValidator->fails()){
            $sErrorMsg = $oValidator->errors()->first();  
            throw new WorkException(Code::USER_REGISTER_PARAMTER_ERROR, $sErrorMsg);
        }
    }

    public function userLoginCheck()
    {
        $oValidator = $this->oValidate->make(
            $aParams,
            [
                'username' => 'required|string',
                'password' => 'required|string',
            ],
            [
                'username.required' => 'username 為必填.',
                'password.required' => 'password 為必填.',
            ]
        );

        if ($oValidator->fails()){
            $sErrorMsg = $oValidator->errors()->first();  
            throw new WorkException(Code::USER_LOGIN_PARAMATER_ERROR, $sErrorMsg);
        }
    }

    public function pageParamaterCheck($aParams)
    {
        $oValidator = $this->oValidate->make(
            $aParams,
            [
                'page' => 'integer|min:1',
                'size' => 'integer|min:1',
            ],
            [
                'page.integer' => 'page 必須為整數.',
                'size.integer' => 'size 必須為整數.',
                'page.min' => 'page 必須大於等於 1.',
                'size.min' => 'size 必須大於等於 1.',
            ]
        );

        if ($oValidator->fails()){
            $sErrorMsg = $oValidator->errors()->first();  
            throw new WorkException(Code::USER_PAGE_PARAMATER_ERROR, $sErrorMsg);
        }
    }

    public function userIdFormatCheckCheck($aParams)
    {
        $oValidator = $this->oValidate->make(
            $aParams,
            [
                'user_id' => 'required|integer|min:1',
            ],
            [
                'user_id.required' => 'user_id 為必填.',
                'user_id.integer' => 'user_id 必須為整數.',
                'user_id.min' => 'user_id 必須大於等於 1.',
            ]
        );

        if ($oValidator->fails()){
            $sErrorMsg = $oValidator->errors()->first();  
            throw new WorkException(Code::USER_SUBSCRIPT_PARAMATER_ERROR, $sErrorMsg);
        }
    }

}
