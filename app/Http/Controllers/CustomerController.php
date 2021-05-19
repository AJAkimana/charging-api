<?php

namespace App\Http\Controllers;

use App\Models\OutputServerUser;
use App\Models\User;
use App\Traits\ServerResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    use ServerResponse;

    public function checkCustomerStatus(Request $request): JsonResponse
    {

        $validator = Validator::make($request->all(), [
            'msisdn_number' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->res(400, $validator->errors()->first());
        }
        $msisdn_number = $request->input('msisdn_number');
        try {
            $response = array(
                'status' => 'not_eligible',
                'action' => 'stop',
                'description' => 'Please try again'
            );
            // Check if customer is registered
            $customer = User::where('msisdn', $msisdn_number)->first();
            if ($customer) {
                $response['status'] = 'registered';
                if ($customer->kyc) {
                    $response['action'] = 'continue';
                    $response['description'] = 'You are already registered';
                } else {
                    $response['action'] = 'request_kyc';
                    $response['description'] = 'Registration is not complete';
                }
                return $this->res(200,'Success', $response);
            }
            // If not registered, check the eligibility via OutPutServer/API
            $outApiResponse = $this->checkEligibility($msisdn_number);


            $response['status'] = $outApiResponse['status'];
            if($outApiResponse['status'] == 'eligible'){
                $response['action'] = 'create_account';
                $response['description'] = 'You are eligible. Continue to create account';
            }
            return $this->res(200, 'Success', $response);
        } catch (Exception $error) {
            return $this->res(500, $error->getMessage());
        }
    }

    private function checkEligibility($userMsisdnNumber): array
    {
        $response = array(
            'status' => 'not_eligible',
            'description' => 'Not eligible',
        );
        try {
            $user = OutputServerUser::where('msisdn', $userMsisdnNumber)->first();
            if ($user && $user->age > 35) {
                $response['status'] = 'eligible';
                $response['description'] = 'Eligible';
            }
            return $response;
        } catch (Exception $error) {
            return $response;
        }
    }
}
