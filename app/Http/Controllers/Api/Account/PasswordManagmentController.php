<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\Password\ForgotPasswordRequest;
use App\Http\Requests\Api\Password\ResetPasswordRequest;
use App\Services\API\Password\PasswordService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse; 

class PasswordManagmentController extends Controller
{
    
    //
       public function __construct(
        private PasswordService $passwordService,
    ) {
    }
    

        public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
{
    $result = $this->passwordService->forgotPassword(
        $request->identifier
    );

    if(!$result['success']){
            return ApiResponse::error(
        $result['message'],
        $result['code'],
        $result['errors'],
        $result['nextEndpoint'],
    ); 
    }
    return ApiResponse::success(
        $result['data'],
        $result['message'],
        $result['code'],
        $result['nextEndpoint'],
    );
}
    

}
