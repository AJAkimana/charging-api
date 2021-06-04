<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use App\Traits\InstallmentHelper;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UploadCustomerFiles
{
    use InstallmentHelper;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $validator = Validator::make($request->all(), [
            'picture' => 'required|mimes:png,jpg|max:2048',
            'form' => 'required|mimes:pdf|max:2048',
        ]);
        if ($validator->fails()) {
            return $this->res(400, $validator->errors()->first());
        }
        $msisdn = $request->route('msisdn');
        try {
            $profilePath = $request->picture->store('public/profiles');
            $formPath = $request->form->store('public/forms');

            $customer = Customer::where('msisdn', $msisdn)->first();
            if ($customer) {
                $customer->picture = $profilePath;
                $customer->application_form = $formPath;

                $customer->save();

                return $next($request);
            }
            return $this->res(400, 'User not found');
        }catch (Exception $error){
            return $this->res(500, $error->getMessage());
        }
    }
}
