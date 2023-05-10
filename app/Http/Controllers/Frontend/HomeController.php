<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\EngineCapacity;
use App\Models\FuelType;
use App\Models\Make;
use App\Models\MakeModel;
use App\Models\Mechanic;
use App\Models\Service;
use App\Models\TyreProfile;
use App\Models\TyreRim;
use Illuminate\Http\Request;
use Stripe;
use Session;
class HomeController extends Controller
{
    public function index()
    {

        return view('frontend.home');
    }
    public function howItWork()
    {
        return view('frontend.how-it-works');
    }
    public function about()
    {
        return view('frontend.about');
    }
    public function bookingCar(Request $request)
    {
        //$request->session()->forget('details');
        $makes = Make::all();
        $details=$request->session()->get('details');
       // return $details;
        return view('frontend.booking_car',compact('makes','details'));
    }
    public function postBookingCar(Request $request)
    {
        $validatedData = $request->validate([
            'make' => 'required|numeric',
            'model' => 'required|numeric',
            'fuel' => 'required|numeric',
            'year' => 'required|numeric',
            'postcode'=>'required'
        ]);
        if(empty($request->session()->get('details'))){
            $details=$validatedData;
            $request->session()->put('details', $details);
        }else{
            $details = $request->session()->get('details');
            $details=array_merge($details,$validatedData);
            $request->session()->put('details', $details);
        }
        return redirect()->route('workdetails');
    }
    public function workDetails(Request $request)
    {
        $details=$request->session()->get('details');
        //dd(json_decode($details['categories']));
        $services=Service::all();
        $categories=Category::where('parent_id',null)->inRandomOrder()->limit(5)->get();
        $mechanics=Mechanic::inRandomOrder()->limit(3)->get();
        return view('frontend.work-details',compact('services','categories','details','mechanics'));
    }
    public function postworkDetails(Request $request)
    {
        $validatedData=$request->validate([
            'categories'=>'required',
            'total_price'=>'required'
        ]);
        $details = $request->session()->get('details');
        $details=array_merge($details,$validatedData);
        $request->session()->put('details', $details);
        return redirect()->route('bookingdetails');

    }
    public function bookingDetails(Request $request)
    {
        $details=$request->session()->get('details');
        return view('frontend.booking_details',compact('details'));
    }
    public function postBookingDetails(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required',
            'work_details' => 'required',
            'street_address_1'=>'required',
            'street_address_2'=>'required',
            'city'=>'required',
            'phone_number'=>'required',
            'seller_name'=>'required',
            'seller_phone_number'=>'required',
            'car_registration_number'=>'required',

        ]);
        $details=$request->session()->get('details');
        $details=array_merge($details,$validatedData);
        $request->session()->put('details',$details);
        return redirect()->route('paymentdetails');
    }
    public function paymentDetails(Request $request)
    {
        $details=$request->session()->get('details');
        return view('frontend.booking_payment',compact('details'));
    }
    public function postPaymentDetails(Request $request)
    {
        $validateddata=([
            'name_on_card'=>'required|string',
            'card_number'=>'required',
            'cvc'=>'required',
            'expiry_month'=>'required',
            'expiry_year'=>'required',
        ]);
        $details=$request->session()->get('details');
        $details=array_merge($details,$validateddata);
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        Stripe\Charge::create ([
                "amount" => $details['total_price'],
                "currency" => "USD",
                "source" => $request->stripeToken,
                "description" => "This payment is testing purpose of webexert",
        ]);




    }
    public function fetchModel(Request $request)
    {
        $data['models'] = MakeModel::where("make_id", $request->make_id)
                                ->get(["title", "id"]);

        return response()->json($data);
    }
    public function fetchFuel(Request $request)
    {
        $data['fuels'] = FuelType::where("model_id", $request->model_id)
                                ->get(["title", "id"]);
        $data['engines'] = EngineCapacity::where("model_id", $request->model_id)
        ->get(["title", "id"]);

        return response()->json($data);
    }
    public function fetchProfile(Request $request)
    {
        $data['models'] = TyreProfile::where("tyre_widths_id", $request->make_id)
                                ->get(["title", "id"]);

        return response()->json($data);
    }
    public function fetchRim(Request $request)
    {
        $data['fuels'] = TyreRim::where("tyre_profiles_id", $request->model_id)
                                ->get(["title", "id"]);


        return response()->json($data);
    }

}