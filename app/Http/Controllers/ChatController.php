<?php

namespace App\Http\Controllers;

use App\Models\model_has_role;
use App\Models\User;
use App\Models\User_prediction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:chat-list|chat-create|chat-edit|chat-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:chat-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:chat-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:chat-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $reciverid = 6;
        $allmessage_lists = User_prediction::where('receiver_id', '=', $reciverid)->where('sender_id', '!=', $reciverid)->orderBy('id', 'DESC')->get();
        $userids = array();
        foreach ($allmessage_lists as $allmessage_list) {
            if (!in_array($allmessage_list->sender_id, $userids, true)) {
                array_push($userids, $allmessage_list->sender_id);
            }
        }
        $message_lists = array();
        $unseen_msg_data = array();
        $unseen_msgCountList = array();

        foreach ($userids as $userid) {
            $user = User::Find($userid);
            $message_list = User_prediction::where('sender_id', '=', $userid)->where('receiver_id', '=', $reciverid)->with('user')->orderBy('id', 'DESC')->first();
            $unseen_message = User_prediction::where('sender_id', '=', $userid)->where('receiver_id', '=', $reciverid)->where('is_seen', '=', 0)->get();
            $unseen_messageCount = count($unseen_message);
            $unseen_msg_data['userid'] = $userid;
            $unseen_msg_data['count'] = $unseen_messageCount;
            array_push($message_lists, $message_list);
            array_push($unseen_msgCountList, $unseen_msg_data);
        }

        // return count($unseen_msgCountList);
        return view('chat.chat', compact('message_lists', 'unseen_msgCountList'));
    }

    public function chat(Request $request)
    {
        $id = $request->id;
        $user = User::find($id);
        $reciverid = 6;
        if(Auth::user()->id == 6){
        $messageseen = User_prediction::where([
            ['sender_id', '=', $user->id],
            ['receiver_id', '=', $reciverid],
        ])->update(['is_seen' => 1]);
        }
        $message_lists = User_prediction::where([
            ['sender_id', '=', $user->id],
            ['receiver_id', '=', $reciverid],
        ])->orWhere([
            ['sender_id', '=', $reciverid],
            ['receiver_id', '=', $user->id],
        ])
            ->with('user')->get();

        $latestmessage = User_prediction::where([
            ['sender_id', '=', $user->id],
            ['receiver_id', '=', $reciverid],
        ])->orWhere([
            ['sender_id', '=', $reciverid],
            ['receiver_id', '=', $user->id],
        ])->orderBy('id', 'DESC')->first();

        return response()->json(['user' => $user, 'message_lists' => $message_lists, 'latestmessage' => $latestmessage]);
    }

    public function questionanswer(Request $request)
    {
        $userid = $request->userid;
        $answer = $request->questionanswer;

        if (Auth::user()->id == 6) {
            $userprediction = new User_prediction();
            $userprediction->sender_id = Auth::user()->id;
            $userprediction->receiver_id = $userid;
            $userprediction->message = $answer;
            $userprediction->save();
            $userId = $userprediction->receiver_id;
            if ($userprediction) {
                return response()->json(['status' => 'success', 'message' => 'Message sent successfull.', 'userId' => $userId]);
            } else {
                return response()->json(['status' => 'error', 'message' => 'error']);
            }
        }
    }

    public function searchuser(Request $request)
    {
        $input = $request->input;
        $inputfilter = $request->inputfilter;
        $reciverid = 6;
        if ($input != '' && $inputfilter == "") {
            $message_lists = array();
            $usersDatas = User::where('name', 'LIKE', '%' . $input . '%')->get();
            $allmessage_lists = User_prediction::where('receiver_id', '=', $reciverid)->where('sender_id', '!=', $reciverid)->with('user')->orderBy('id', 'DESC')->get();
            foreach ($usersDatas as $userDataes) {
                // $usersdata = model_has_role::query()->where('role_id', '=', 23)->with('user', 'userprofile')->get();
                foreach ($allmessage_lists as $userdata) {
                    $userid = $userdata->user->id;
                    if ($userDataes->id == $userid) {
                        $message_list = User_prediction::where('sender_id', '=', $userid)->where('receiver_id', '=', $reciverid)->with('user')->orderBy('id', 'DESC')->first();
                        if (!in_array($message_list, $message_lists)) {
                            array_push($message_lists, $message_list);
                        }
                    }
                }
            }
            return response()->json(['status' => 'success', 'message' => 'User found', 'message_list' => $message_lists]);
        } elseif ($inputfilter != "" && $input == '') {

            $date = date('Y-m-d',strtotime($inputfilter));
            $message_lists = array();
            $allmessage_lists = User_prediction::where('receiver_id', '=', $reciverid)->where('created_at', 'LIKE', '%' . $date . '%')->with('user')->orderBy('id', 'DESC')->get();
            $userids = array();
            foreach ($allmessage_lists as $allmessage_list) {
                if (!in_array($allmessage_list->sender_id, $userids, true)) {
                    array_push($userids, $allmessage_list->sender_id);
                }
            }
            $message_lists = array();
            $unseen_msg_data = array();
            $unseen_msgCountList = array();

            foreach ($userids as $userid) {
                $user = User::Find($userid);
                $message_list = User_prediction::where('sender_id', '=', $userid)->where('receiver_id', '=', $reciverid)->with('user')->orderBy('id', 'DESC')->first();
                $unseen_message = User_prediction::where('sender_id', '=', $userid)->where('receiver_id', '=', $reciverid)->where('is_seen', '=', 0)->get();
                $unseen_messageCount = count($unseen_message);
                $unseen_msg_data['userid'] = $userid;
                $unseen_msg_data['count'] = $unseen_messageCount;
                array_push($message_lists, $message_list);
                array_push($unseen_msgCountList, $unseen_msg_data);
            }
            return response()->json(['status' => 'success', 'message' => 'User found', 'message_list' => $message_lists, 'unseen_msgCountList'=>$unseen_msgCountList]);
        }elseif($input != '' && $inputfilter != ""){

            $date = date('Y-m-d',strtotime($inputfilter));
            $message_lists = array();
            $usersDatas = User::where('name', 'LIKE', '%' . $input . '%')->get();
            $allmessage_lists = User_prediction::where('receiver_id', '=', $reciverid)->where('created_at', 'LIKE', '%' . $date . '%')->with('user')->orderBy('id', 'DESC')->get();
            foreach ($usersDatas as $userDataes) {
                // $usersdata = model_has_role::query()->where('role_id', '=', 23)->with('user', 'userprofile')->get();
                foreach ($allmessage_lists as $userdata) {
                    $userid = $userdata->user->id;
                    if ($userDataes->id == $userid) {
                        $message_list = User_prediction::where('sender_id', '=', $userid)->where('receiver_id', '=', $reciverid)->with('user')->orderBy('id', 'DESC')->first();
                        if (!in_array($message_list, $message_lists)) {
                            array_push($message_lists, $message_list);
                        }
                    }
                }
            }
            return response()->json(['status' => 'success', 'message' => 'User found', 'message_list' => $message_lists]);
        }
        else
        {
            $reciverid = 6;
            $allmessage_lists = User_prediction::where('receiver_id', '=', $reciverid)->where('sender_id', '!=', $reciverid)->orderBy('id', 'DESC')->get();
        $userids = array();
        foreach ($allmessage_lists as $allmessage_list) {
            if (!in_array($allmessage_list->sender_id, $userids, true)) {
                array_push($userids, $allmessage_list->sender_id);
            }
        }
        $message_lists = array();
        $unseen_msg_data = array();
        $unseen_msgCountList = array();

        foreach ($userids as $userid) {
            $user = User::Find($userid);
            $message_list = User_prediction::where('sender_id', '=', $userid)->where('receiver_id', '=', $reciverid)->with('user')->orderBy('id', 'DESC')->first();
            $unseen_message = User_prediction::where('sender_id', '=', $userid)->where('receiver_id', '=', $reciverid)->where('is_seen', '=', 0)->get();
            $unseen_messageCount = count($unseen_message);
            $unseen_msg_data['userid'] = $userid;
            $unseen_msg_data['count'] = $unseen_messageCount;
            array_push($message_lists, $message_list);
            array_push($unseen_msgCountList, $unseen_msg_data);
        }
        }
        return response()->json(['status' => 'success', 'message' => 'User found', 'message_list' => $message_lists, 'unseen_msgCountList' => $unseen_msgCountList]);
    }

    public function filteruser(Request $request)
    {
        $input = $request->inputfilter;
        if ($input != "") {
            $date = date('Y-m-d',strtotime($input));
            $reciverid = 6;
            $message_lists = array();
            $allmessage_lists = User_prediction::where('receiver_id', '=', $reciverid)->where('created_at', 'LIKE', '%' . $date . '%')->with('user')->orderBy('id', 'DESC')->get();
            $userids = array();
            foreach ($allmessage_lists as $allmessage_list) {
                if (!in_array($allmessage_list->sender_id, $userids, true)) {
                    array_push($userids, $allmessage_list->sender_id);
                }
            }
            $message_lists = array();
            $unseen_msg_data = array();
            $unseen_msgCountList = array();

            foreach ($userids as $userid) {
                $user = User::Find($userid);
                $message_list = User_prediction::where('sender_id', '=', $userid)->where('receiver_id', '=', $reciverid)->with('user')->orderBy('id', 'DESC')->first();
                $unseen_message = User_prediction::where('sender_id', '=', $userid)->where('receiver_id', '=', $reciverid)->where('is_seen', '=', 0)->get();
                $unseen_messageCount = count($unseen_message);
                $unseen_msg_data['userid'] = $userid;
                $unseen_msg_data['count'] = $unseen_messageCount;
                array_push($message_lists, $message_list);
                array_push($unseen_msgCountList, $unseen_msg_data);
            }
            return response()->json(['status' => 'success', 'message' => 'User found', 'message_list' => $message_lists, 'unseen_msgCountList'=>$unseen_msgCountList]);
        } else {
            $reciverid = 6;
            $allmessage_lists = User_prediction::where('receiver_id', '=', $reciverid)->where('sender_id', '!=', $reciverid)->orderBy('id', 'DESC')->get();
            $userids = array();
            foreach ($allmessage_lists as $allmessage_list) {
                if (!in_array($allmessage_list->sender_id, $userids, true)) {
                    array_push($userids, $allmessage_list->sender_id);
                }
            }
            $message_lists = array();
            $unseen_msg_data = array();
            $unseen_msgCountList = array();

            foreach ($userids as $userid) {
                $user = User::Find($userid);
                $message_list = User_prediction::where('sender_id', '=', $userid)->where('receiver_id', '=', $reciverid)->with('user')->orderBy('id', 'DESC')->first();
                $unseen_message = User_prediction::where('sender_id', '=', $userid)->where('receiver_id', '=', $reciverid)->where('is_seen', '=', 0)->get();
                $unseen_messageCount = count($unseen_message);
                $unseen_msg_data['userid'] = $userid;
                $unseen_msg_data['count'] = $unseen_messageCount;
                array_push($message_lists, $message_list);
                array_push($unseen_msgCountList, $unseen_msg_data);
            }
        }
        return response()->json(['status' => 'success', 'message' => 'User found', 'message_list' => $message_lists, 'unseen_msgCountList' => $unseen_msgCountList]);
    }
}
