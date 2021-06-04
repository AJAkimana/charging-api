<?php

namespace App\Http\Controllers;

use App\Models\OutputServerUser;
use App\Traits\InstallmentHelper;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MockController extends Controller
{
    use InstallmentHelper;

    public function checkEligibility(Request $request): JsonResponse {
        $status = 'not_eligible';
        $description = 'Not eligible';
        $resMessage = 'Error. Please try again';
        try{
            $user = OutputServerUser::where(
                'msisdn', $request->input('msisdn_number'))->first();
            $statusCode = 400;
            if($user && $user->age > 35){
                $status = 'eligible';
                $description = 'Eligible';
                $resMessage = 'Success';
                $statusCode = 200;
            }
            return $this->res($statusCode, $resMessage,  compact('status', 'description'));
        }catch (Exception $error){
            return $this->res(500, $resMessage, compact('status', 'description'));
        }
    }
}
