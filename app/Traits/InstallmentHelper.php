<?php

namespace App\Traits;


use App\Models\Customer;
use App\Models\InstallmentPaid;
use Error;
use Exception;
use Illuminate\Support\Facades\DB;

trait InstallmentHelper
{
    /**
     * @param $customer Customer Customer object with offer
     * @param $amount integer
     */
    public function payInstallment(Customer $customer, int $amount)
    {
        try {
            /**
             * Aggregate sum of [amount, access_amount, amount_not_paid
             * From the InstallnentPaid model
             */
            $aggregateAmount = array('amount' => 0, 'access_amount' => 0, 'amount_not_paid' => 0);

        } catch (Exception $error) {
            throw new Error($error);
        }
    }

    /**
     * @param $customer Customer Customer object with offer
     * @param $amount integer
     */
    public function aggregatedAmount(Customer $customer, int $postedAmount)
    {
        try {
            /**
             * Aggregate sum of [amount, added_amount, amount_not_paid
             * From the InstallnentPaid model
             */
            $aggAmount = array('amount' => 0, 'added_amount' => 0, 'amount_not_paid' => 0, 'is_last' => false);
            $amountTobePaid = $customer->offer->amount + $customer->offer->initial_amount;
            $installmentsSum = InstallmentPaid::where('customer_id', $customer->id)
                ->select(
                    DB::raw('SUM(amount) AS amount'),
                    DB::raw('SUM(added_amount) AS added_amount'),
                    DB::raw('SUM(amount_not_paid) AS amount_not_paid'))
                ->groupBy('customer_id')
                ->first();

            $amountPaid = $installmentsSum->amount + $installmentsSum->added_amount;
            $amountNotPaid = $installmentsSum->amount_not_paid;
            if ($amountPaid - $amountNotPaid + $postedAmount >= $amountTobePaid) {
                $aggAmount['is_last'] = true;
                if ($amountPaid - $amountNotPaid < $amountTobePaid) {
                    $aggAmount['amount'] = $amountPaid - $amountNotPaid + $postedAmount - $amountTobePaid;
                    $aggAmount['added_amount'] = $postedAmount - $aggAmount['amount'];
                } else if ($amountPaid - $amountNotPaid + $postedAmount === $amountTobePaid) {
                    $aggAmount['amount'] = $postedAmount;
                }
            } else {
                // Devide amount into 3: amount tobe paid, not paid, and added
                $aggAmount['amount'] = $postedAmount > $customer->installment_amount ? $customer->installment_amount : $postedAmount;
                $aggAmount['added_amount'] = $postedAmount > $customer->installment_amount ? $postedAmount - $customer->installment_amount : 0;
                $aggAmount['amount_not_paid'] = $postedAmount < $customer->installment_amount ?  $customer->installment_amount - $postedAmount : 0;
            }
            return $aggAmount;
        } catch (Exception $error) {
            throw new Error($error->getMessage());
        }
    }

}

