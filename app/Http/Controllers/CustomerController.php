<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\LoanAccount;
use App\Models\MtnServerUser;
use App\Models\OutputServerUser;
use App\Traits\ServerResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        $message = 'Something went wrong, please try again';
        try {
            $response = array(
                'status' => 'not_eligible',
                'action' => 'stop',
                'description' => 'Please try again'
            );
            // Check if customer is registered
            $customer = Customer::where('msisdn', $msisdn_number)->first();
            if ($customer) {
                $response['status'] = 'registered';
                $message = 'This customer is registered.';
                $response['description'] = $message;

                // Check the user if has a loan
                $userLoan = LoanAccount::where('customer_id', $customer->id)->first();
                if ($userLoan) {
                    $response['action'] = 'stop';
                    $response['description'] = 'Come on, Finish the loan first';
                }
                return $this->res(200, $message, $response);
            }
            // If not registered, check the eligibility via OutPutServer/API
            $response = $this->checkEligibility($msisdn_number);
            $message = 'The request has failed, please try again or contact Intelligra';
            if ($response['status'] == 'eligible') {
                $response['action'] = 'create_account_pending';
                $response['description'] = 'Something went wrong MTN';

                // Request a KYC from MTN servers, and then Register a new customer
                $mtnUser = $this->requestKYC($msisdn_number);
                if ($mtnUser['status'] === 'Found') {
                    // Register a new customer
                    $response['action'] = 'account_created';
                    $response['accountStatus'] = 'active';
                    $response['loanStatus'] = 'pending';
                    $response['creditScore'] = $response['score'];
                    Customer::create([
                        'names' => $mtnUser['user']->names,
                        'msisdn' => $mtnUser['user']->phoneNumber,
                        'age' => rand(35,80),
                        'location' => $mtnUser['user']->location,
                        'kyc' => $mtnUser['user']->kyc,
                    ]);
                    $response['description'] = 'This customer is registered.';
                }
            }
            $message = $response['description'];
            return $this->res(200, $message, $response);
        } catch (Exception $error) {
            return $this->res(500, $message, $error->getMessage());
        }
    }
    /**
     * A method that plays a role for OutputServer API
     *
     * @param $userMsisdnNumber string
     * @return string[]
     */
    private function checkEligibility(string $userMsisdnNumber): array
    {
        $response = array(
            'status' => 'not_eligible',
            'description' => 'Not eligible',
        );
        try {
            $user = OutputServerUser::where('msisdn', $userMsisdnNumber)->first();
            if ($user && $user->score > 35) {
                $nDevices = floor($user->score / 35) + 1;
                $response['status'] = 'eligible';
                $response['score'] = $user->score;
                $response['devices'] = $this->mockedDevices($nDevices);
                $response['message'] = 'This user is eligible for the above devices';
                $response['description'] = 'Eligible';
            } else {
                $response['description'] = 'We could not find your number';
            }
            return $response;
        } catch (Exception $error) {
            return $response;
        }
    }

    /**
     * A method that plays a role for MTNServer API
     *
     * @param $userMsisdnNumber string
     * @return string[]
     */
    private function requestKYC(string $userMsisdnNumber): array
    {
        $response = array('status' => 'Not found');
        try {
            $user = MtnServerUser::where('phoneNumber', $userMsisdnNumber)->first();
            if ($user) {
                $response['status'] = 'Found';
                $response['description'] = 'Success';
                $response['user'] = $user;
            }
            return $response;
        } catch (Exception $error) {
            $response['description'] = $error->getMessage();
            return $response;
        }
    }

    /**
     * Mocked devices
     */
    private function mockedDevices(int $nDevices = 3): array
    {
        $devices = array();
        for ($i = 1; $i < $nDevices; $i++) {
            $randNum = $i * rand(1, 3);
            $devices[] = array(
                'deviceName' => 'LG G' . $randNum,
                'emiAmount' => array('amount' => rand(100, 999), 'currency' => 'usd'),
            );
        }
        return $devices;
    }
}
