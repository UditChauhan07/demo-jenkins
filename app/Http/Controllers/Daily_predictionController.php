<?php

namespace App\Http\Controllers;

use App\Models\Daily_prediction;
use App\Models\model_has_role;
use App\Models\User;
use App\Models\User_prediction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpParser\Builder\Function_;
use PhpParser\Node\Expr\FuncCall;

class Daily_predictionController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:dailyprediction-list|dailyprediction-create|dailyprediction-edit|dailyprediction-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:dailyprediction-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:dailyprediction-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:dailyprediction-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $reciverid = 6;
        $predictions = Daily_prediction::where('is_active', '=', 1)->where('publish_status', '!=', 3)->with('users')->orderBy('id', 'DESC')->get();
        $msgcount = array();
        $messagecounts = array();
        foreach($predictions as $prediction){
            $pdate = $prediction->prediction_date;
            $unseen_message = User_prediction::where('receiver_id', '=', $reciverid)->where('created_at', 'LIKE', '%' .$pdate. '%')->get();
            $count = count($unseen_message);
            $msgcount['predictionId'] = $prediction->id;
            $msgcount['count'] = $count;
            array_push($messagecounts, $msgcount);
        }
        return view('dailyprediction.index', compact('predictions', 'messagecounts'));
    }

    public function create()
    {
        return view('dailyprediction.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'prediction_date' => 'required',
            'prediction' => 'required',
        ]);
        $findprediction = Daily_prediction::where('prediction_date', $request->prediction_date)->where('publish_status', '!=', 3)->get();
        if (count($findprediction) == 0) {
            if ($request->prediction_date == date('Y-m-d')) {
                $publish_status = 1;
            } else {
                $publish_status = 2;
            }
            $input = new Daily_prediction();
            $input->user_id = Auth::user()->id;
            $input->prediction_date = $request->prediction_date;
            $input->prediction = $request->prediction;
            $input->publish_status = $publish_status;
            $input->save();
            if ($publish_status == 1) {
                $id = $input->id;
                $users = model_has_role::where('role_id', 23)->with('user', 'userprofile')->get();
                $data = Daily_prediction::find($id);
                foreach ($users as $user) {
                    $userprediction = new User_prediction();
                    $userprediction->sender_id = Auth::user()->id;
                    $userprediction->receiver_id = $user->user->id;
                    $userprediction->dailyprediction_id = $data->id;
                    $userprediction->message = $data->prediction;
                    $userprediction->save();
                }
                return redirect()->route('dailyprediction.index')
                    ->with('success', 'Daily Prediction is Published successfully');
            } else {
                return redirect()->route('dailyprediction.index')
                    ->with('success', 'Something went wrong');
            }
        } else {
            return redirect()->route('dailyprediction.index')
                ->with('success', 'Today daily prediction is already Published/Scheduled. Please try with another date.');
        }
    }

    public function predictiondate(Request $request)
    {
        $prediction_date = $request->prediction_Date;
        $findprediction = Daily_prediction::where('prediction_date', $prediction_date)->where('publish_status', '!=', 3)->get();
        if (count($findprediction) > 0) {
            if ($prediction_date == date('Y-m-d')) {
                return response()->json(['status' => 0, 'message' => 'Today daily prediction is already Published. Please try with another date.']);
            } else {
                return response()->json(['status' => 0, 'message' => 'Prediction of selected date is already Scheduled. Please try with another date.']);
            }
        } else {
            if ($prediction_date == date('Y-m-d')) {
                return response()->json(['status' => 1, 'message' => 'Are you sure you want to publish this prediction?']);
            } else {
                return response()->json(['status' => 1, 'message' => 'Are you sure you want to Schedule this prediction?']);
            }
        }
    }

    public function show($id)
    {
        $prediction = Daily_prediction::find($id);

        return view('dailyprediction.show', compact('prediction'));
    }

    public function publish($id)
    {
        $users = model_has_role::where('role_id', 23)->with('user')->get();

        foreach ($users as $user) {
            $data = Daily_prediction::find($id);
            $userprediction = new User_prediction();
            $userprediction->sender_id = Auth::user()->id;
            $userprediction->receiver_id = $user->user->id;
            $userprediction->dailyprediction_id = $data->id;
            $userprediction->message = $data->prediction;
            $userprediction->save();
        }
        if ($userprediction) {

            $data = Daily_prediction::find($id);
            $data->publish_status = 1;
            $data->save();

            return redirect()->route('dailyprediction.index')
                ->with('success', 'Daily Prediction publish successfully');
        }
    }

    public function cancelpublish($id)
    {
        $data = Daily_prediction::find($id);
        if ($data) {
            $data->publish_status = 3;
            $data->save();
            return redirect()->route('dailyprediction.index')
                ->with('success', 'Daily Prediction Canceled successfully');
        } else {
            return redirect()->route('dailyprediction.index')
                ->with('error', 'Something went wrong!');
        }
    }

    public function report($id)
    {
        $prediction = Daily_prediction::find($id);
        $seen_prediction = User_prediction::where('dailyprediction_id', $id)
            ->where('is_seen', '=', 1)->get();
        $like_prediction = User_prediction::where('dailyprediction_id', $id)
            ->where('is_like', '=', 1)->get();
        $dislike_prediction = User_prediction::where('dailyprediction_id', $id)
            ->where('is_like', '=', 1)->get();
        return view('dailyprediction.report', compact('prediction', 'seen_prediction', 'like_prediction', 'dislike_prediction'));
    }

    public function destroy($id)
    {
        return $id;
        $data = Daily_prediction::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('dailyprediction.index')
            ->with('success', 'Prediction Deleted successfully');
    }
}
