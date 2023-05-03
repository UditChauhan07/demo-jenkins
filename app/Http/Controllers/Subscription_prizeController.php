<?php

namespace App\Http\Controllers;

use App\Models\Stripemode;
use App\Models\Subscription_prize;
use Illuminate\Http\Request;

class Subscription_prizeController extends Controller
{
    public function index()
    {
        $prize_list = Subscription_prize::where('is_active', 1)->orderBy('id', 'DESC')->paginate(20);
        return view('subscription_prize.index', compact('prize_list'));
    }

    public function create()
    {
        return view('subscription_prize.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'prize' => 'required',
        ]);
        $subscription_prize = new Subscription_prize();
        $subscription_prize->prize = $request->prize;
        $subscription_prize->subscription_time = 1;
        $subscription_prize->save();
        if($subscription_prize){
            return redirect()->route('subscription_prize.index')
            ->with('success', 'Prize created successfully');
        }
        
    }

    public function show($id)
    {
        $prediction = Subscription_prize::find($id);

        return view('subscription_prize.show', compact('prediction'));
    }

    public function stripe_mode()
    {
        $stripemodeData = Stripemode::all();
        return view('stripesetting', compact('stripemodeData'));
    }

    public function stripemode_status(Request $request, $id)
    {
        if($id == 1){
            $id1 = 2;
        }else{
            $id1 = 1;
        }

        $updateStatus = Stripemode::find($id);
        $updateStatus->current_status = 1;
        $updateStatus->save();

        $update = Stripemode::find($id1);
        $update->current_status = 0;
        $update->save();
        
        return redirect()->route('stripe.mode')
                        ->with('success','Stripe Setting updated successfully');
    }

    

}
