<?php

namespace App\Http\Controllers;

use App\Models\Meter;
use App\Models\MeterReadings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response as HttpResponse;

class MeterReadingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
                "readings" => "required",
                "meter_id" => "required|integer",
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()->all(),
            ], HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $meter = Meter::find($request->meter_id);
        if ($meter) {
            $lastReadings = MeterReadings::latest()->where('meter_id', $request->meter_id)->first();
            $lastVolume = 0;
            if ($lastReadings != null) {
                $lastVolume = $lastReadings->readings;
            }
            $volume = $request->readings - $lastVolume;
            if ($volume > 0) {
                $data = new MeterReadings;
                $data->readings = $request->readings;
                $data->volume = $volume;
                $data->meter_id = $request->meter_id;
                $data->created_at = now();
                $data->updated_at = null;
                $data->save();
                return response()->json(['message' => 'Imibre a konteri yagiye muri system'], 200);
            } else {
                return response()->json(["errors" => "Imibare iri muri konteri ntago ari $request->readings"], 200);
            }
        } else {
            return response()->json(['errors' => 'Konteri ntibonetse'], 200);
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
