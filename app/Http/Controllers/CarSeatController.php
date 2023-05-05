<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSeatRequest;
use App\Http\Traits\HttpResponseTrait;
use App\Models\CarSeat;
use Illuminate\Http\Request;

class CarSeatController extends Controller
{
    use HttpResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $seats = CarSeat::paginate('10');
        return $this->success([
            'all seats'=>$seats,
            'message'=>"All Seats fetched successfully"
        ]);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSeatRequest $request)
    {
        //
        $request->validated($request->all());
        $seat = CarSeat::create([
            'car_id'=>$request->car_id,
            'seat_name'=>$request->seat_name,
            'status' => 0
        ]);
        return $this->success([
            'seat'=> $seat,
            'message' => "seat created successfully"
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $seat = CarSeat::findOrFail($id);
        return $this->success([
             'seat'=> $seat,

        ]);

    }



    /**
     * Update the specified resource in storage.
     */
    public function update(StoreSeatRequest $request, string $id)
    {
        //
        $request->validated($request->all());

        $seat = CarSeat::findOrFail($id);
        $seat->car_id = $request->car_id;
        $seat->seat_name = $request->seat_name;
        $seat->status  = $request->status;
        $seat->update();
        return $this->success([
             'seat'=> $seat,
             'message' => "seat was updated succesfully",

        ]);


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
         $seat = CarSeat::findOrFail($id);
         $seat->delete();
         return $this->success([
         'message'=>"seat deleted successfully"
         ]);

    }
}
