<?php

namespace App\Http\Controllers;

use App\Http\Requests\CarCategoryRequest;
use App\Http\Traits\HttpResponseTrait;
use App\Models\CarCategory;
use Illuminate\Http\Request;

class CarCategoryController extends Controller
{
    use HttpResponseTrait;
    /**
     * Display a listing of the car category.
     */
    public function index()
    {
        //
        $bus_categories = CarCategory::paginate(5);
        return $this->success([
            'all_category' => $bus_categories,
            'message' =>"Paginated Cars Category"
        ]);
    }


    /**
     * Store a newly created car category in storage.
     */
    public function store(CarCategoryRequest $request)
    {
        $request->validated($request->all());
        $car_category = CarCategory::create([
            'name' => $request->name,
            'desc' => $request->desc,
        ]);
        return $this->success([
            'car_category' => $car_category,
            'message' =>"car category created successfully"

        ]);
    }

    /**
     * Display the specified car category.
     */
    public function show(string $id)
    {
        //
         $car_category = CarCategory::findOrFail($id);
         return $this->success([
            '$car_category'=>$car_category,
            'message' =>"car category"." ".$id
         ]);


    }


    /**
     * Update the specified car category in storage.
     */
    public function update(CarCategoryRequest $request, string $id)
    {
        //
        $request->validated($request->all());
        $car_category = CarCategory::findOrFail($id);
        $car_category->name = $request->name;
        $car_category->desc = $request->desc;
        $car_category->update();
        return $this->success([
            'category'=>$car_category,
            'message' =>"Cars Category Updated successfully"
        ]);



    }

    /**
     * Remove the specified car category from storage.
     */
    public function destroy(string $id)
    {
        //
        $car_category = CarCategory::findOrFail($id);
        $car_category->delete();
        return $this->success([

            'message' =>"Cars Category"." ".$id." "."deleted successfully"
        ]);

    }
}
