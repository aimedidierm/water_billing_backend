<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Services\Sms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Paypack\Paypack;
use Illuminate\Http\Response as HttpResponse;

class BillingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bills = Billing::latest()->latest()->get();
        $bills->load('user', 'meter');
        return response()->json($bills, 200);
    }

    public function clientListing()
    {
        $bills = Billing::latest()->latest()->where('user_id', Auth::id())->get();
        $bills->load('user', 'meter');
        return response()->json($bills, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "billing_id" => "required",
                "phone" => "required",
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()->all(),
            ], HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
        $billing = Billing::find($request->billing_id);
        $billing->status = 'payed';
        $billing->update();
        $message = "Dear client thank for sending your payment";
        $sms = new Sms();
        $sms->recipients([$billing->phone])
            ->message($message)
            ->sender(env('SMS_SENDERID'))
            ->username(env('SMS_USERNAME'))
            ->password(env('SMS_PASSWORD'))
            ->apiUrl("www.intouchsms.co.rw/api/sendsms/.json")
            ->callBackUrl("");
        $sms->send();
        $paypackInstance = $this->paypackConfig()->Cashin([
            "amount" => $billing->amount,
            "phone" => $request->phone,
        ]);
        return response()->json([
            $billing
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Billing $billing)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Billing $billing)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Billing $billing)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Billing $billing)
    {
        //
    }

    public function paypackConfig()
    {
        $paypack = new Paypack();

        $paypack->config([
            'client_id' => env('PAYPACK_CLIENT_ID'),
            'client_secret' => env('PAYPACK_CLIENT_SECRET'),
        ]);

        return $paypack;
    }
}
