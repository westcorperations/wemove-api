<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCarsRequest;
use App\Http\Traits\HttpResponseTrait;
use App\Models\Cars;
use Illuminate\Http\Request;

class CarController extends Controller
{
    use HttpResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $cars = Cars::paginate('10');
        return $this->success([
            'all_cars' => $cars,
            'message'=>"All Cars Fetched Successfully",
        ]);

    }




    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCarsRequest $request)
    {
        //
        $request->validated($request->all());

        $car = Cars::create([
            "name" => $request->name,
            "category_id"=>$request->category_id,
            "seat_no"=>$request->seat_no,
            "brand"=>$request->brand,
            "model"=>$request->model,
            "price"=>$request->price,
            "status"=>0,
        ]);
        return $this->success([
            'car' => $car,
            'message' => "Car"." "."$car->name"." "."created successfully",
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $car = Cars::findOrFail($id);
        return $this->success([
            "car" => $car,
            "message" => "Car"." ".$car->name
        ]);

    }
    /**
     * Display the specified resource.
     */
    public function allSeats(string $id)
    {
        $car = Cars::findOrFail($id);
        $seats = $car->seats;
        return $this->success([
            "All car Seats" => $seats,
            "message" => "All Car seats for"." ".$car->name." "."fetched successfull"
        ]);

    }



    /**
     * Update the specified resource in storage.
     */
    public function update(StoreCarsRequest $request, string $id)
    {
        //
        $request->validated($request->all());
        $car = Cars::findOrFail($id);
        $car->name  =  $request->name;
        $car->category_id = $request->category_id;
        $car->seat_no = $request->seat_no;
        $car->brand = $request->brand;
        $car->model = $request->model;
        $car->price = $request->price;
        $car->status = $request->status;
        $car->update();
        return $this->success([
            'car' => $car,
            'meassage' => "Car Updated successfully"
        ]);


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $car = Cars::findOrFail($id);
        $car->delete();
        return $this->success([

            'meassage' => "Car deleted successfully"
        ]);
    }
}
