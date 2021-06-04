<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\InstallmentPaid;
use App\Traits\InstallmentHelper;
use App\Traits\ServerResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InstallmentController extends Controller
{
    use InstallmentHelper, ServerResponse;

    /**
     * Add the first payment of an offer
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function initialDeposit(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'installment_period' => 'required|in:daily,weekly,monthly'
        ]);
        if ($validator->fails()) {
            return $this->res(400, $validator->errors()->first());
        }
        $msisdnNumber = $request->route('msisdn');
        try{
            $customer = Customer::where('msisdn', $msisdnNumber)->with('offer:id,initial_deposit')->first();
            if(!$customer)
                return $this->res(400, 'Customer not found');
            if($customer->loan_status  !== 'active'){
                /**
                 * Ericson call for initial deposit
                 * the update the customer on successful response
                 */
                $customer->loan_status = 'active';
                $customer->customer_status = 'full_customer';
                $customer->monthversary_date = date("Y-m-d");
                $customer->installment_period = $request->input('installment_period');
                $customer->installment_amount = $customer->offer->initial_deposit;

                $customer->save();

                InstallmentPaid::create([
                    'amount' => $customer->offer->initial_deposit,
                    'amount_not_paid' => 0,
                    'added_amount' => 0,
                    'customer_id' => $customer->id
                ]);
                return $this->res(200, 'Success', $customer);
            }
            return $this->res(404, 'The customer has to be active');
        }catch(Exception $error){
            return $this->res(500, $error->getMessage());
        }
    }

    /**
     * Pay an installment
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function addInstallment(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'amount' => 'required'
        ]);
        if ($validator->fails())
            return $this->res(400, $validator->errors()->first());
        $msisdnNumber = $request->route('msisdn');
        try{
            $customer = Customer::where('msisdn', $msisdnNumber)->with('offer:id,amount,initial_deposit')->first();
            if(!$customer)
                return $this->res(400, 'Customer not found');
            if($customer->loan_status  === 'active'){
                /**
                 * Ericson call for initial deposit
                 * the update the customer on successful response
                 */
                $installMent = $this->aggregatedAmount($customer, $request->input('amount'));
                InstallmentPaid::create([
                    'amount' => $installMent['amount'],
                    'amount_not_paid' => $installMent['amount_not_paid'],
                    'added_amount' => $installMent['added_amount'],
                    'customer_id' => $customer->id
                ]);
                // if the payment is the last change the customer statuses
                if($installMent['is_last']){
                    $customer->loan_status = 'completed';
                    $customer->customer_status = 'post_customer';
                    $customer->save();
                }
                return $this->res(200, 'Success', $installMent);
            }
            return $this->res(404, 'The customer has to be active');
        }catch(Exception $error){
            return $this->res(500, $error->getMessage());
        }
    }
}
