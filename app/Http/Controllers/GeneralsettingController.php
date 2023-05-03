<?php

namespace App\Http\Controllers;

use App\Models\Free_subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GeneralsettingController extends Controller
{
    public function index()
    {
        return view('generalsetting');
    }

    public function free_subcheck(Request $request)
    {
        $this->validate($request, [
            'number' => 'required',
        ]);
        $number = $request->number;
        $free_subscriptionData = Free_subscription::where('is_active', 1)->orderBy('id', 'DESC')->latest()->first();
        if($free_subscriptionData){
            $numberofUser = $free_subscriptionData->number_of_users;
            $currentDate = date('Y-m-d h:m:s');
            $cal_currentDate = strtotime($currentDate);
            $recordEndDate = strtotime($free_subscriptionData->end_date);
            if($cal_currentDate <= $recordEndDate && $number <= $numberofUser){ 
                return response()->json(['status' => 1, 'message' => 'You already given free subscription to '.$numberofUser.' users. Please select higher number to update number of users.']);
            }elseif($cal_currentDate <= $recordEndDate && $number >= $numberofUser){
                return response()->json(['status' => 2, 'message' => 'You already given free subscription to '.$numberofUser.' users. Are sure you want to update number of users.']);
            }else{
                return response()->json(['status' => 3, 'message' => 'success']);
            }
        }else{
            return response()->json(['status' => 3, 'message' => 'success']);
        }

    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'number_of_user' => 'required',
        ]);
        $user_id = Auth::user()->id;
        $user_number = $request->number_of_user;
        $currentDate = date('Y-m-d h:m:s');
        $startDate = strtotime($currentDate);
        $calDate = strtotime("+3 month", $startDate);
        $endDate = date('Y-m-d h:m:s', $calDate);

        $free_subscriptionData = Free_subscription::where('is_active', 1)->orderBy('id', 'DESC')->latest()->first();
        if($free_subscriptionData != NULL){
            $recordEndDate = strtotime($free_subscriptionData->end_date);
            if($startDate > $recordEndDate){
                $free_subscription_records = Free_subscription::create([
                    'user_id' => $user_id,
                    'number_of_users' => $user_number,
                    'start_date' => $currentDate,
                    'end_date' => $endDate,
                ]);
                if($free_subscription_records){
                    return redirect()->route('general.index')
                                ->with('success','Setting updated successfully');
                }else{
                    return "Error";
                }
            }elseif($startDate < $recordEndDate && $user_number > $free_subscriptionData->number_of_users){
                $free_subscriptionData->number_of_users = $user_number;
                $free_subscriptionData->save();
            }elseif($startDate < $recordEndDate && $user_number <= $free_subscriptionData->number_of_users){
                // return "Error";
                return redirect()->route('general.index')
                                ->with('error','You can not select less than '.$free_subscriptionData->number_of_users.' number of users successfully');
            }
        }else{
            $free_subscription_records = Free_subscription::create([
                'user_id' => $user_id,
                'number_of_users' => $user_number,
                'start_date' => $currentDate,
                'end_date' => $endDate,
            ]);
            if($free_subscription_records){
                return redirect()->route('general.index')
                            ->with('success','Setting updated successfully');
            }else{
                return "Error";
            }
        }
    }
}
