<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Http\Requests\PaymentRequest;
use App\Http\Traits\HttpResponseTrait;
use App\Models\Booking;
use App\Models\Cars;
use App\Models\CarSeat;
use App\Models\payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Unicodeveloper\Paystack\Facades\Paystack;
// use Paystack;

class CarBookingController extends Controller
{
    use HttpResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $bookings = Booking::paginate('10');
        return $this->success([
            'data' => $bookings,
            'meassage' => "All Bookings"
        ]);
    }
    public function userBooking()
    {
        # code...
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(BookingRequest $request)
    {
        $request->validated($request->all());
        // try {

        $car = Cache::remember('car_' . $request->car_id, 60, function () use ($request) {
            return Cars::findOrFail($request->car_id);
        });
        $total_price = $car->price * $request->kilometer;
        // DB::beginTransaction();
        $request->merge([
            'email' => Auth::user()->email,
            'amount' => $total_price * 100,
            "reference" => paystack()->genTranxRef(),
            "first_name" => Auth::user()->name,
            "currency" => "NGN",
            "orderID" => 'WMV' . Auth::user()->id . random_int(100000, 999999999999),
            "description" => 'Payment For Bus Ticket',
            'metadata' => [

                'booking_user_id' => Auth::id(),
                'booking_car_id' => $request->car_id,
                'booking_seat_id' => $request->seat_id,
                'booking_name' => $request->name,
                'booking_phone' => $request->phone,
                'booking_departure_city' => $request->departure_city,
                'booking_arrival_city' => $request->arrival_city,
                'booking_kilometer' => $request->kilometer,
                'booking_date' => $request->date,
                'booking_total_price' => $total_price,
            ],
        ]);
        // Initiate the payment using Paystack
        try {
            $url = paystack()->getAuthorizationUrl()->url;

            return $this->success([
                "url" => $url
            ]);
        } catch (\Exception $e) {
            // dd($e->getMessage());
            return $this->error([
                'data' => $e->getMessage(),
                'msg' => 'The paystack Tokeen has expired .Please refresh and try again.'
            ]);
        }
    }

    public function confirmPayment()
    {
        // $request->validated($request->all());
        // $reference = $request->reference;
        $paymentDetails = Paystack::getPaymentData();

        $status = $paymentDetails['data']['status'];
        $amount = $paymentDetails['data']['amount'];
        $booking_data = $paymentDetails['data']['metadata'];
        //

        switch ($status) {
            case 'success':
                // Payment was successful
                // Perform necessary actions and save booking details
                 DB::beginTransaction();
                try {
                    $booking = new Booking();
                    $booking->user_id = Auth::id();
                    $booking->car_id = $booking_data['booking_car_id'];
                    $booking->seat_id = $booking_data['booking_seat_id'];
                    $booking->name = $booking_data['booking_name'];
                    $booking->phone = $booking_data['booking_phone'];
                    $booking->departure_city = $booking_data['booking_departure_city'];
                    $booking->arrival_city = $booking_data['booking_arrival_city'];
                    $booking->kilometer = $booking_data['booking_kilometer'];
                    $booking->date = $booking_data['booking_date'];
                    $booking->total_price = $amount / 100;
                    $booking->save();
                    $payment = new payment();
                    $payment->user_id =  $booking->user_id;
                    $payment->booking_id = $booking->id;
                    $payment->status = $status;
                    $payment->booking_no = 'WMV' . $booking->id  . random_int(100000, 999999999999);
                    // $car = Cache::remember('car_' . $request->car_id, 60, function () use ($request) {
                    //     return Cars::findOrFail($booking_data['booking_car_id']));
                    // });

                    $carSeat = CarSeat::where('car_id', $booking->car_id)
                        ->where('id', $booking->seat_id)
                        ->where('status', 0)
                        ->firstOrFail();
                    $carSeat->status = 1;
                    $carSeat->save();
                    DB::commit();
                    return $this->success([
                        "data" => $booking,
                        "pay" => $payment,
                        'message' => 'Booking Payment Successfull',
                    ]);
                } catch (\Exception $e) {
                    DB::rollback();

                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage(),
                    ], 500);
                }

                break;
            case 'failed':
                // Payment failed
                // Handle the failed payment scenario
                 DB::beginTransaction();
                try {
                    $booking = new Booking();
                    $booking->user_id = Auth::id();
                    $booking->car_id = $booking_data['booking_car_id'];
                    $booking->seat_id = $booking_data['booking_seat_id'];
                    $booking->name = $booking_data['booking_name'];
                    $booking->phone = $booking_data['booking_phone'];
                    $booking->departure_city = $booking_data['booking_departure_city'];
                    $booking->arrival_city = $booking_data['booking_arrival_city'];
                    $booking->kilometer = $booking_data['booking_kilometer'];
                    $booking->date = $booking_data['booking_date'];
                    $booking->total_price = $amount / 100;
                    $booking->save();
                    $payment = new payment();
                    $payment->user_id =  $booking->user_id;
                    $payment->booking_id = $booking->id;
                    $payment->status = $status;
                    $payment->booking_no = 'WMV' . $booking->id  . random_int(100000, 999999999999);

                    DB::commit();
                    return $this->success([
                        "data" => $booking,
                        "pay" => $payment,
                        'message' => 'Booking Payment' . ' ' . $status,
                    ]);
                } catch (\Exception $e) {
                    DB::rollback();

                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage(),
                    ], 500);
                }
                break;
            case 'abandoned':
                // Payment was abandoned or not completed by the user
                // Handle the abandoned payment scenario
                 DB::beginTransaction();
                try {
                    $booking = new Booking();
                    $booking->user_id = Auth::id();
                    $booking->car_id = $booking_data['booking_car_id'];
                    $booking->seat_id = $booking_data['booking_seat_id'];
                    $booking->name = $booking_data['booking_name'];
                    $booking->phone = $booking_data['booking_phone'];
                    $booking->departure_city = $booking_data['booking_departure_city'];
                    $booking->arrival_city = $booking_data['booking_arrival_city'];
                    $booking->kilometer = $booking_data['booking_kilometer'];
                    $booking->date = $booking_data['booking_date'];
                    $booking->total_price = $amount / 100;
                    $booking->save();
                    $payment = new payment();
                    $payment->user_id =  $booking->user_id;
                    $payment->booking_id = $booking->id;
                    $payment->status = $status;
                    $payment->booking_no = 'WMV' . $booking->id  . random_int(100000, 999999999999);


                    DB::commit();
                    return $this->success([
                        "data" => $booking,
                        "pay" => $payment,
                        'message' => 'Booking Payment' . ' ' . $status,
                    ]);
                } catch (\Exception $e) {
                    DB::rollback();

                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage(),
                    ], 500);
                }
                break;
            case 'pending':
                // Payment is still pending
                // Handle the pending payment scenario
                 DB::beginTransaction();
                try {
                    $booking = new Booking();
                    $booking->user_id = Auth::id();
                    $booking->car_id = $booking_data['booking_car_id'];
                    $booking->seat_id = $booking_data['booking_seat_id'];
                    $booking->name = $booking_data['booking_name'];
                    $booking->phone = $booking_data['booking_phone'];
                    $booking->departure_city = $booking_data['booking_departure_city'];
                    $booking->arrival_city = $booking_data['booking_arrival_city'];
                    $booking->kilometer = $booking_data['booking_kilometer'];
                    $booking->date = $booking_data['booking_date'];
                    $booking->total_price = $amount / 100;
                    $booking->save();
                    $payment = new payment();
                    $payment->user_id =  $booking->user_id;
                    $payment->booking_id = $booking->id;
                    $payment->status = $status;
                    $payment->booking_no = 'WMV' . $booking->id  . random_int(100000, 999999999999);


                    DB::commit();
                    return $this->success([
                        "data" => $booking,
                        "pay" => $payment,
                        'message' => 'Booking Payment' . ' ' . $status,
                    ]);
                } catch (\Exception $e) {
                    DB::rollback();

                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage(),
                    ], 500);
                }
                break;
            case 'timeout':
                // Payment request timed out
                // Handle the timeout scenario
                 DB::beginTransaction();
                try {
                    $booking = new Booking();
                    $booking->user_id = Auth::id();
                    $booking->car_id = $booking_data['booking_car_id'];
                    $booking->seat_id = $booking_data['booking_seat_id'];
                    $booking->name = $booking_data['booking_name'];
                    $booking->phone = $booking_data['booking_phone'];
                    $booking->departure_city = $booking_data['booking_departure_city'];
                    $booking->arrival_city = $booking_data['booking_arrival_city'];
                    $booking->kilometer = $booking_data['booking_kilometer'];
                    $booking->date = $booking_data['booking_date'];
                    $booking->total_price = $amount / 100;
                    $booking->save();
                    $payment = new payment();
                    $payment->user_id =  $booking->user_id;
                    $payment->booking_id = $booking->id;
                    $payment->status = $status;
                    $payment->booking_no = 'WMV' . $booking->id  . random_int(100000, 999999999999);


                    DB::commit();
                    return $this->success([
                        "data" => $booking,
                        "pay" => $payment,
                        'message' => 'Booking Payment' . ' ' . $status,
                    ]);
                } catch (\Exception $e) {
                    DB::rollback();

                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage(),
                    ], 500);
                }
                break;
            default:
                // Unknown payment status
                // Handle the unknown scenario or throw an error
                 DB::beginTransaction();
                try {
                    $booking = new Booking();
                    $booking->user_id = Auth::id();
                    $booking->car_id = $booking_data['booking_car_id'];
                    $booking->seat_id = $booking_data['booking_seat_id'];
                    $booking->name = $booking_data['booking_name'];
                    $booking->phone = $booking_data['booking_phone'];
                    $booking->departure_city = $booking_data['booking_departure_city'];
                    $booking->arrival_city = $booking_data['booking_arrival_city'];
                    $booking->kilometer = $booking_data['booking_kilometer'];
                    $booking->date = $booking_data['booking_date'];
                    $booking->total_price = $amount / 100;
                    $booking->save();
                    $payment = new payment();
                    $payment->user_id =  $booking->user_id;
                    $payment->booking_id = $booking->id;
                    $payment->status = $status;
                    $payment->booking_no = 'WMV' . $booking->id  . random_int(100000, 999999999999);


                    DB::commit();
                    return $this->success([
                        "data" => $booking,
                        "pay" => $payment,
                        'message' => 'Booking Payment' . ' ' . $status,
                    ]);
                } catch (\Exception $e) {
                    DB::rollback();

                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage(),
                    ], 500);
                }
                break;
        }
    }




    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BookingRequest  $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
