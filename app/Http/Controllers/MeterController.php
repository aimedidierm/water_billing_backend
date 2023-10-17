<?php

namespace App\Http\Controllers;

use App\Models\Meter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response as HttpResponse;

class MeterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $meters = Meter::latest()->get();
        return response()->json($meters, 200);
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
                "client" => "required|string",
                "country" => "required",
                "province" => "required",
                "district" => "required",
                "sector" => "required",
                "cell" => "required",
                "village" => "required",
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()->all(),
            ], HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $randomNumber = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $exists = Meter::where('meterId', $randomNumber)->exists();
        while ($exists) {
            $randomNumber = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $exists = Meter::where('meterId', $randomNumber)->exists();
        }
        $meter = new Meter;
        $meter->meterId = $randomNumber;
        $meter->client = $request->client;
        $meter->country = $request->country;
        $meter->province = $request->province;
        $meter->district = $request->district;
        $meter->sector = $request->sector;
        $meter->cell = $request->cell;
        $meter->village = $request->village;
        $meter->created_at = now();
        $meter->updated_at = null;
        $meter->save();
        return response()->json(['message' => 'Konteri yagiye muri system'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Meter $meter)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Meter $meter)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Meter $meter)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Meter $meter)
    {
        //
    }
}
