<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Meter;
use App\Models\MeterReadings;
use App\Services\Sms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;

class MeterReadingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $readings = MeterReadings::latest()->where('user_id', Auth::id())
            ->get();
        $readings->load('meter', 'user');
        return response()->json(['readings' => $readings], 200);
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
                "readings" => "required|numeric",
                "meter_id" => "required|string",
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()->all(),
            ], HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $meter = Meter::where('meterId', $request->meter_id)->first();
        if ($meter) {
            $lastReadings = MeterReadings::latest()->where('meter_id', $meter->id)->first();
            $lastVolume = 0;
            if ($lastReadings != null) {
                $lastVolume = $lastReadings->readings;
            }
            $volume = $request->readings - $lastVolume;
            if ($volume > 0) {

                if ($volume < 5) {
                    $amount = $volume * 402;
                } elseif ($volume < 20) {
                    $amount = $volume * 852;
                } elseif ($volume < 50) {
                    $amount = $volume * 990;
                } else {
                    $amount = $volume * 1030;
                }

                $data = new MeterReadings;
                $data->readings = $request->readings;
                $data->volume = $volume;
                $data->meter_id = $meter->id;
                $data->user_id = Auth::id();
                $data->created_at = now();
                $data->updated_at = null;
                $data->save();

                $payment = new Billing;
                $payment->amount = $amount;
                $payment->reading_id = $data->id;
                $payment->status = 'pending';
                $payment->user_id = Auth::id();
                $payment->meter_id = $meter->id;
                $payment->created_at = now();
                $payment->updated_at = null;
                $payment->save();

                $message = "Dear client " . $meter->client . " thank for sending your meter status you must pay " . $amount . " Rwf Thank you.";
                $sms = new Sms();
                $sms->recipients([Auth::user()->phone])
                    ->message($message)
                    ->sender(env('SMS_SENDERID'))
                    ->username(env('SMS_USERNAME'))
                    ->password(env('SMS_PASSWORD'))
                    ->apiUrl("www.intouchsms.co.rw/api/sendsms/.json")
                    ->callBackUrl("");
                $sms->send();
                return response()->json(['message' => 'Imibare a konteri yagiye muri system'], 200);
            } else {
                return response()->json(["errors" => "Imibare iri muri konteri ntago ari $request->readings"], 403);
            }
        } else {
            return response()->json(['errors' => 'Konteri ntibonetse'], 403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(MeterReadings $meterReadings)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MeterReadings $meterReadings)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MeterReadings $meterReadings)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MeterReadings $meterReadings)
    {
        //
    }
}
