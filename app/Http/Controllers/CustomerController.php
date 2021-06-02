<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Kyc;
use App\Models\LoanAccount;
use App\Models\MtnServerUser;
use App\Models\NextOfKeen;
use App\Models\Offer;
use App\Models\OutputServerUser;
use App\Traits\ServerResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    use ServerResponse;

    /**
     * A handler for checking a customer status and register a customer
     *
     * @param Request $request
     * @return JsonResponse
     */
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
        $response = array(
            'status' => 400,
            'action' => 'stop',
            'description' => $message
        );
        try {
            // Check if customer is registered
            $customer = Customer::where('msisdn', $msisdn_number)->first();
            if ($customer) {
                $response['status'] = 200;
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

                    $customerKyc = Kyc::create($mtnUser['user']->toArray());
                    Customer::create([
                        'msisdn' => $mtnUser['user']->phoneNumber,
                        'location' => $mtnUser['user']->address,
                        'kyc_id' => $customerKyc->id,
                    ]);
                    $response['description'] = 'This customer is registered.';
                }
            }
            $message = $response['description'];
            $response['status'] == 200;
            return $this->res(200, $message, $response);
        } catch (Exception $error) {
            $response['message'] = 'Something went wrong, please try again';
            return $this->res(500, $message, $error->getMessage());
        }
    }

    /**
     * Save a customer offer preference
     *
     * @param Request $request
     * @param $msisdn
     * @return JsonResponse
     */
    public function saveCustomerOffer(Request $request, $msisdn): JsonResponse
    {
        try {
            $offer = Offer::find($request->input('offer_id'));
            if (!$offer) {
                return $this->res(400, 'We could not find your choice');
            }
            $customer = Customer::where('msisdn', $msisdn)->first();
            if ($customer) {
                $customer->offer_id = $request->input('offer_id');
                $customer->save();
                $customer->offer = $offer;
                return $this->res(200, "Offer added, Please go to the service center to pick it up", $customer);
            }
            return $this->res(404, 'The customer not found');
        } catch (Exception $error) {
            return $this->res(500, $error->getMessage());
        }
    }

    /**
     * Save a customer offer preference
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getCustomerDetails(Request $request): JsonResponse
    {
        try {
            $customer = Customer::where('msisdn', $request->route('msisdn'))
                ->with('kyc')->with('offer')->with('nextOfKin')
                ->first();
            if ($customer) {
                return $this->res(200, "Success", $customer);
            }
            return $this->res(404, 'The customer not found');
        } catch (Exception $error) {
            return $this->res(500, $error->getMessage());
        }
    }
    /**
     * Save a customer offer preference
     *
     * @param Request $request
     * @param $msisdn
     * @return JsonResponse
     */
    public function updateCustomerKyc(Request $request, $msisdn): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'picture' => 'required|mimes:png,jpg|max:2048',
            'form' => 'required|mimes:pdf|max:2048',
            'nextOfKinNames' => 'required',
            'nextOfKinAddress' => 'required',
            'nextOfKinIdNumber' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->res(400, $validator->errors()->first());
        }
        try {
            $profilePath = $request->picture->store('uploads/profiles');
            $formPath = $request->form->store('uploads/forms');

            $customer = Customer::where('msisdn', $msisdn)->first();
            if ($customer){
                $nextOfKin = NextOfKeen::firstOrCreate([
                    'names' => $request->input('nextOfKinNames'),
                    'address' => $request->input('nextOfKinAddress'),
                    'id_number' => $request->input('nextOfKinIdNumber')
                ]);
                $customer->next_of_kin_id = $nextOfKin->id;
                $customer->picture = $profilePath;
                $customer->application_form = $formPath;

                $customer->save();

                return $this->res(200, "KYC updated", $customer);
            }
            return $this->res(400, 'User not found');
        } catch (Exception $error) {
            return $this->res(500, $error->getMessage());
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
    private function mockedDevices(int $nDevices = 3)
    {
        $devices = array();
        try {
            return Offer::inRandomOrder()->limit($nDevices)->get();
        } catch (Exception $error) {
            return $devices;
        }
    }
}
