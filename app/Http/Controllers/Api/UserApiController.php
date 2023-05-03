<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Alphasystem_type;
use App\Models\Cancel_user_subscription;
use App\Models\Compatibility_description;
use App\Models\Compatible_partner;
use App\Models\Daily_coach;
use App\Models\Fav_unfav_parameter;
use App\Models\Life_change;
use App\Models\Life_cycle;
use App\Models\Luckiest_parameter;
use App\Models\model_has_role;
use App\Models\Module_description;
use App\Models\Personal_parameter;
use App\Models\Planet_number;
use App\Models\Primaryno_type;
use App\Models\Universal_perameter;
use App\Models\User;
use App\Models\Useronboarding;
use App\Models\Zodic_sign;
use App\Models\User_namereading;
use App\Models\User_compatiblecheck;
use App\Models\Compatibility_percentage;
use App\Models\Partner_relationship;
use App\Models\Possesion;
use App\Models\User_historyname;
use App\Models\Daily_prediction;
use App\Models\User_prediction;
use App\Models\Dailycoach_type;
use App\Models\Free_subscription;
use App\Models\User_travel;
use App\Models\Share_data;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Models\Lifecoach_description;
use App\Models\Stripemode;
use App\Models\Subscription_prize;
use App\Models\User_payment;

class UserApiController extends Controller
{
    
    // On boarding step-1
    public function onboardingstepone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'signupby' => 'required',
            'username' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $otp = substr(time(), -6);
        $name = $request->name;
        $username = $request->username;
        $signupby = $request->signupby;
        // check user
        $users = User::where(function ($query) use ($username) {
            $query->where('email', '=', $username)
                ->orWhere('phoneno', '=', $username);
        })->get();
        //print_r($users);
       
        //$users = User::where('email', $username)->get();
        if (count($users) == 0) {
            $userprofile = new User();
            $userprofile->name = $name;
            $userprofile->signupby = $signupby;
            if($signupby == 2){
                $userprofile->phoneno = $username;
            }
            else{
                $userprofile->email = $username;
            }
            $userprofile->otp = $otp;
            $userprofile->save();
            $userprofile->assignRole('User');
            if ($userprofile->id && $signupby == 1) {

                $subject = 'ASTAR8: Verification Code';
                $from = "notification@designersx.us";
                $msg = "Verification Code is <b>" . $otp . "</b>.";
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.smtp2go.com/v3/email/send",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30000,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => "{\r\n    \"api_key\": \"api-22898DD4CFC911EDB2B6F23C91C88F4E\",\r\n    \"to\": [\"$username>\"],\r\n    \"sender\": \"$from\",\r\n    \"subject\": \"$subject\",\r\n    \"text_body\": \"$msg\",\r\n    \"html_body\": \"$msg\",\r\n    \"custom_headers\": [\r\n      {\r\n        \"header\": \"Reply-To\",\r\n        \"value\": \"$from>\"\r\n      }\r\n    ]\r\n}",
                    CURLOPT_HTTPHEADER => array(
                        // Set here Laravel Post required headers
                        "cache-control: no-cache",
                        "content-type: application/json"
                    ),
                ));

                $results = curl_exec($curl);
                $resErrors = curl_error($curl);

                curl_close($curl);

                $userstep = new Useronboarding();
                $userstep->user_id = $userprofile->id;
                $userstep->step_1 = 1;
                $userstep->save();

                return response()->json(['status' => 1, 'message' => 'Verification code has been successfully sent to your registered Email Id.', 'user_id' => $userprofile->id, 'Fullname' => $name, 'username' => $username, 'otp' => $otp, 'signupby' => $signupby, 'onboardingstatus' => 1, 'nextboardingstep' => 'Step-2']);
            } 
            elseif ($userprofile->id && $signupby == 2) {

                $userstep = new Useronboarding();
                $userstep->user_id = $userprofile->id;
                $userstep->step_1 = 1;
                $userstep->save();
                return response()->json(['status' => 1, 'message' => 'Verification code has been successfully sent to your registered Phone number.', 'user_id' => $userprofile->id, 'Fullname' => $name, 'username' => $username, 'otp' => 'smsfb',  'signupby' => $signupby, 'onboardingstatus' => 1, 'nextboardingstep' => 'Step-2']);

            }
            else {
                return response()->json(['status' => 0, 'message' => 'Profile is not created', 'user_id' => $userprofile->id, 'Fullname' => $name, 'username' => $username, 'onboardingstatus' => 0, 'nextboardingstep' => 'Step-1']);
            }
        } else {

            if($signupby == 1){
                return response()->json([
                    'status' => 0,
                    'message' => 'The Email id is exist. So please try another Email id.',
                ]);
            }
            elseif($signupby == 2){
                return response()->json([
                    'status' => 0,
                    'message' => 'The Phone Number is exist. So please try another Phone Number.',
                ]);
            }
            else{
                return response()->json([
                    'status' => 0,
                    'message' => 'The Email id/Phone Number is exist. So please try another Email id/Phone Number.',
                ]);
            }
        }
        
    }

    public function onboardingsteptwo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'otp' => 'required',
            'signupby' => 'required',
            'typeuid' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $user_id = $request->user_id;
        $otp = $request->otp;
        $signupby = $request->signupby;
        $typeuid = $request->typeuid; // For SMS(Firbase ID ie dynamic) or Email(Static)


        $users = User::find($user_id);
        //Name reading
        $name = $users->name;
        $finalname = str_replace(' ', '', $name);
        $strname = strtoupper($finalname);
        $splitname = str_split($strname, 1);
        foreach ($splitname as $letter) {
            $pytha_number[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $letter . '%')
                ->where('systemtype_id', 1)
                ->value('number');
            $chald_number[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $letter . '%')
                ->where('systemtype_id', 2)
                ->value('number');
        }
        $pytha_no_sum = array_sum($pytha_number);
        $chald_no_sum = array_sum($chald_number);

        while (strlen($pytha_no_sum) != 1 || strlen($chald_no_sum) != 1) {
            $pytha_no_sum = str_split($pytha_no_sum, 1);
            $pytha_no_sum = array_sum($pytha_no_sum);
            $chald_no_sum = str_split($chald_no_sum, 1);
            $chald_no_sum = array_sum($chald_no_sum);
        }
        $pytha_description = Module_description::where('moduletype_id', 1)
            ->where('number', $pytha_no_sum)
            ->value('description');
        $pytha_description = strip_tags($pytha_description);
        $chald_description = Module_description::where('moduletype_id', 1)
            ->where('number', $chald_no_sum)
            ->value('description');
        $explodenamereading_desc = explode('||', $chald_description);
        $positive_desc = $explodenamereading_desc[0];
        $negative_desc = $explodenamereading_desc[1];
        $namereadingdesc = array("positive_title" => "Positive", "positive_desc" => $positive_desc, "negative_title" => "Negative", "negative_desc" => $negative_desc);

        /* if($chald_no_sum != 9)
		{
			$namereadingdesc = $chald_description;
		}else
		{
			$namereadingdesc= $pytha_description;
		} */

        $verifyotp = $users->otp;
        if ($verifyotp == $otp || $signupby == 2) {
            if($signupby == 1){
                $verify_otp = User::where(['id' => $user_id, 'otp' => $otp])->update(['otp' => NULL, 'is_otp_verify' => 1,  'typeuid' => $typeuid]);
            }else{
                $verify_otp = User::where(['id' => $user_id])->update(['otp' => NULL, 'is_otp_verify' => 1,  'typeuid' => $typeuid]);
            }
            
            if ($verify_otp) {
                $updateonboardingdata = Useronboarding::where(['user_id' => $user_id, 'step_1' => 1])->update(['step_2' => 1]);
                $generate_user_token = uniqid();
                $update_usertoken = User::where(['id' => $user_id])->update(['user_token' => $generate_user_token]);
                return response()->json([
                    'status' => 1,
                    'message' => 'This OTP is verified. ',
                    'user_id' => $user_id,
                    'onboardingstatus' => 1,
                    'nextboardingstep' => 'Step-3',
                    'Fullname' => $users->name,
                    'namenumber' => $chald_no_sum,
                    'namereadingdesc' => $namereadingdesc,
                    'user_token' => $generate_user_token,
                ]);
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'This OTP is not verified. Please try again.',
                    'user_id' => $user_id,
                    'onboardingstatus' => 0,
                    'nextboardingstep' => 'Step-2'
                ]);
            }
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'This OTP is not verified. Please try again.',
                'user_id' => $user_id,
                'onboardingstatus' => 0,
                'nextboardingstep' => 'Step-2'
            ]);
        }
    }

    public function onboardingstepthree(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'dob' => 'required',
            'gender' => 'required',
            'relationship' => 'required',
            'occupation' => 'required',
            'current_date' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $user_id = $request->user_id;
        $dob = $request->dob;
        $gender = $request->gender;
        $relationship = $request->relationship;
        $occupation = $request->occupation;
        $current_date = $request->current_date;
        $cal_current_date = strtotime($current_date);
        $users = User::find($user_id);
        if ($users) {
            $update_userdata = User::where(['id' => $user_id])->update(['dob' => $dob, 'gender' => $gender, 'relationship' => $relationship, 'occupation' => $occupation]);
            if ($update_userdata) {
                $updateonboardingdata = Useronboarding::where(['user_id' => $user_id, 'step_1' => 1, 'step_2' => 1])->update(['step_3' => 1, 'overall_status' => 1]);
                $free_subscriptionData = Free_subscription::where('is_active', 1)->orderBy('id', 'DESC')->latest()->first();
                if($free_subscriptionData != null){
                $free_subscripion_startDate = strtotime($free_subscriptionData->start_date); 
                $free_subscripion_endDate = strtotime($free_subscriptionData->end_date);
                if($cal_current_date >= $free_subscripion_startDate && $cal_current_date <= $free_subscripion_endDate)
                {
                    $user = User::find($user_id);
                    $user->subscription_status = 9;
                    $user->save();
                    $price = Subscription_prize::where('is_active', 1)->orderBy('id', 'DESC')->latest()->first();
                    if($price){
                        $amount = $price->prize;
                    }else{
                        $amount = 299;
                    }
                    $end_date = strtotime("+3 month", $cal_current_date);
                    $endDate_format = date('Y-m-d h:m:s', $end_date);
                    $userpayment = User_payment::create([
                        'user_id' => $user_id,
                        'subscription_status' => 9,
                        'plan_name' => 'Standard',
                        'amount' => $amount,
                        'start_date' => $current_date,
                        'renewal_date' => $endDate_format,
                        'status' => 'ByAdmin',
                    ]);
                }
                }
                return response()->json([
                    'status' => 1,
                    'message' => 'Successfully Registered',
                    'user_id' => $user_id,
                    'onboardingstatus' => 1,
                    'nextboardingstep' => 'finish',
                    'Fullname' => $users->name,
                ]);
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'Something went wrong. Please try again.',
                    'user_id' => $user_id,
                    'onboardingstatus' => 0,
                    'nextboardingstep' => 'Step-3'
                ]);
            }
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong. Please try again.',
                'user_id' => $user_id,
                'onboardingstatus' => 0,
                'nextboardingstep' => 'Step-3'
            ]);
        }
    }

    // User Login
    // On boarding step-1
    public function userlogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'signupby' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $otp = substr(time(), -6);
        $username = $request->username;
        $signupby = $request->signupby;
        $check_users = User::where(function ($query) use ($username) {
            $query->where('email', '=', $username)
                ->orWhere('phoneno', '=', $username);
        })->first();

        if ($check_users) {
            if ($check_users->is_active == 1) {
                if ($signupby == 1) {
                    $update_otp = User::where(['id' => $check_users->id])->update(['otp' => $otp]);
                    $subject = 'ASTAR8: Verification Code';
                    $from = "notification@designersx.us";
                    $msg = "Verification Code is <b>" . $otp . "</b>.";
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://api.smtp2go.com/v3/email/send",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30000,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => "{\r\n    \"api_key\": \"api-22898DD4CFC911EDB2B6F23C91C88F4E\",\r\n    \"to\": [\"$username>\"],\r\n    \"sender\": \"$from\",\r\n    \"subject\": \"$subject\",\r\n    \"text_body\": \"$msg\",\r\n    \"html_body\": \"$msg\",\r\n    \"custom_headers\": [\r\n      {\r\n        \"header\": \"Reply-To\",\r\n        \"value\": \"$from>\"\r\n      }\r\n    ]\r\n}",
                        CURLOPT_HTTPHEADER => array(
                            // Set here Laravel Post required headers
                            "cache-control: no-cache",
                            "content-type: application/json"
                        ),
                    ));

                    $results = curl_exec($curl);
                    $resErrors = curl_error($curl);

                    curl_close($curl);

                    return response()->json(['status' => 1, 'message' => 'Verification code has been successfully sent to your registered Email id.', 'user_id' => $check_users->id, 'Fullname' => $check_users->name, 'otp' => $otp, 'signupby' => $signupby, 'onboardingstatus' => 1, 'username' => $username, 'nextboardingstep' => 'Step-2', 'subscription_status' => $check_users->subscription_status]);
                }
                elseif ($signupby == 2) {
                    return response()->json(['status' => 1, 'message' => 'Verification code has been successfully sent to your registered Phone number.', 'user_id' => $check_users->id, 'Fullname' => $check_users->name, 'otp' => 'smsfb', 'signupby' => $signupby,'onboardingstatus' => 1, 'username' => $username, 'nextboardingstep' => 'Step-2', 'subscription_status' => $check_users->subscription_status]);
                }
                
                else {
                    return response()->json([
                        'status' => 0,
                        'message' => 'Invalid Username.',
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'You are blocked by Admin.',
                ]);
            }
        } else {

            if($signupby == 1){
                return response()->json([
                    'status' => 0,
                    'message' => 'The Email id is not exist. So please login with registered Email id.',
                ]);
            }
            elseif($signupby == 2){
                return response()->json([
                    'status' => 0,
                    'message' => 'The Phone Number is not exist. So please login with registered Phone Number.',
                ]);
            }
            else{
                return response()->json([
                    'status' => 0,
                    'message' => 'The Email id/Phone Number is not exist. So please login with registered Email id/Phone Number.',
                ]);
            }
            
        }
    }


    public function verifyloginotp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'otp' => 'required',
            'signupby' => 'required',
            'typeuid' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $user_id = $request->user_id;
        $otp = $request->otp;
        $signupby = $request->signupby;
        $typeuid = $request->typeuid; // For SMS(Firbase ID ie dynamic) or Email(Static)

        $users = User::find($user_id);
        
        //Name reading
        $name = $users->name;
        $verifyotp = $users->otp;
        $demouser = $users->id;

       
        if ($verifyotp == $otp || $demouser == 315 || $signupby == 2) {

            $verify_otp = User::where(['id' => $user_id])->update(['otp' => NULL, 'is_loginotp_verify' => 1,  'typeuid' => $typeuid]);
           
            //$verify_otp = User::where(['id' => $user_id, 'otp' => $otp])->update(['otp' => NULL, 'is_loginotp_verify' => 1]);
            $update_step = Useronboarding::where(['user_id' => $user_id, 'step_1' => 1])->update(['step_2' => 1]);
            if ($verify_otp && $update_step) {
                $generate_user_token = uniqid();
                $update_usertoken = User::where(['id' => $user_id])->update(['user_token' => $generate_user_token]);
                $check_useronBoardingStep = Useronboarding::where(['user_id' => $user_id, 'step_1' => 1, 'step_2' => 1])->value('step_3');
                if ($check_useronBoardingStep == 1) {
                    $user = User::find($user_id);

                    // DOB reading 
                    $dob = $user->dob;
                    $date = explode('-', $dob);
                    $day = $date[2];
                    $month = $date[1];
                    $year = $date[0];
                    $dayno = str_split($day, 1);
                    $dayno = array_sum($dayno);
                    $dayno = intval($dayno);
                    while (strlen($dayno) != 1) {
                        $dayno = str_split($dayno);
                        $dayno = array_sum($dayno);
                    }
                    $dobdesc = Module_description::where('moduletype_id', 2)
                        ->where('number', $dayno)
                        ->value('description');
                    $dobdesc = strip_tags($dobdesc);
                    $birthdayreading = array("module_type" => 2, "module_name" => "DOB Reading", "number" => $dayno, "description" => $dobdesc);

                    //elemental number
                    $elementaldesc = Module_description::where('moduletype_id', 4)
                        ->where('number', $dayno)
                        ->value('description');
                    $elementaldesc = strip_tags($elementaldesc);
                    $elementalno = array("module_type" => 4, "module_name" => "Elemental Number", "number" => $dayno, "description" => $elementaldesc);

                    //basic health reading
                    $basichealthdesc = Module_description::where('moduletype_id', 5)
                        ->where('number', $dayno)
                        ->value('description');
                    $basichealthdesc = strip_tags($basichealthdesc);

                    $basichealthreading = array("module_type" => 5, "module_name" => "Basic Health Reading", "number" => $dayno, "description" => $basichealthdesc);

                    //health precaution
                    $precautiondesc = Module_description::where('moduletype_id', 6)
                        ->where('number', $dayno)
                        ->value('description');
                    $precautiondesc = strip_tags($precautiondesc);
                    $healthprecaution = array("module_type" => 6, "module_name" => "Health Precaution", "number" => $dayno, "description" => $precautiondesc);

                    //basic Parenting
                    $basicparentingdesc = Module_description::where('moduletype_id', 12)
                        ->where('number', $dayno)
                        ->value('description');
                    $basicparentingdesc = strip_tags($basicparentingdesc);

                    $basicparenting = array(
                        "module_type" => 12, "module_name" => "Basic Parent Reading",
                        "number" => $dayno, "description" => $basicparentingdesc
                    );

                    //detail Parenting  
                    $detailparentingdesc = Module_description::where('moduletype_id', 13)
                        ->where('number', $dayno)
                        ->value('description');
                    $detailparentingdesc = strip_tags($detailparentingdesc);

                    $detailparenting = array(
                        "module_type" => 13, "module_name" => "Detailed Parent Reading",
                        "number" => $dayno, "description" => $detailparentingdesc
                    );

                    //basic money 
                    $basicmoneydesc = Module_description::where('moduletype_id', 14)
                        ->where('number', $dayno)
                        ->value('description');
                    $basicmoneydesc = strip_tags($basicmoneydesc);

                    $basicmoneymatter = array(
                        "module_type" => 14, "module_name" => "Basic Money Matters",
                        "number" => $dayno, "description" => $basicmoneydesc
                    );

                    //detail money 
                    $detailmoneydesc = Module_description::where('moduletype_id', 15)
                        ->where('number', $dayno)
                        ->value('description');
                    $detailmoneydesc = strip_tags($detailmoneydesc);

                    $detailmoneymatter = array(
                        "module_type" => 15, "module_name" => "Detailed Money Matters",
                        "number" => $dayno, "description" => $detailmoneydesc
                    );

                    //destiny number
                    $monthno = str_split($month, 1);
                    $monthno = array_sum($monthno);
                    while (strlen($monthno) != 1) {
                        $monthno = str_split($monthno);
                        $monthno = array_sum($monthno);
                    }
                    $yearno = str_split($year, 1);
                    $yearno = array_sum($yearno);
                    while (strlen($yearno) != 1) {
                        $yearno = str_split($yearno);
                        $yearno = array_sum($yearno);
                    }
                    $yearno = intval($yearno);
                    $destiny_no = $dayno + $monthno + $yearno;
                    while (strlen($destiny_no) != 1) {
                        $destiny_no = str_split($destiny_no);
                        $destiny_no = array_sum($destiny_no);
                    }
                    $destinynodesc = Module_description::where('moduletype_id', 16)
                        ->where('number', $destiny_no)
                        ->value('description');
                    $destinynodesc = strip_tags($destinynodesc);
                    $explode_destinynodesc = explode('||', $destinynodesc);
                    $learn_desc = $explode_destinynodesc[0];
                    $notlearn_desc = $explode_destinynodesc[1];

                    $destinynumber = array(
                        "module_type" => 16, "module_name" => "Destiny Number", "number" => $destiny_no, "description" => $destinynodesc,
                        "learn_title" => "Learn To Be", "learn_desc" => $learn_desc, "notlearn_title" => "Learn Not To Be", "notlearn_desc" => $notlearn_desc
                    );

                    $dobreadingdetail = [
                        $birthdayreading, $elementalno, $basichealthreading, $healthprecaution, $basicmoneymatter, $detailmoneymatter,
                        $basicparenting, $detailparenting, $destinynumber
                    ];

                    //primary number
                    $primarynodesc = Primaryno_type::where('number', $dayno)
                        ->first();
                    $primarynumber = array(
                        "module_name" => "Primary Number", "number" => $dayno, "description" => $primarynodesc->description, "positive" => $primarynodesc->positive, "negative" => $primarynodesc->negative,
                        "occupations" => $primarynodesc->occupations, "health" => $primarynodesc->health, "partners" => $primarynodesc->partners, "times_of_the_year" => $primarynodesc->times_of_the_year,
                        "countries" => $primarynodesc->countries, "tibbits" => $primarynodesc->tibbits, "destiny_colors" => $primarynodesc->destiny_colors, "destiny_timing" => $primarynodesc->destiny_timing
                    );

                    //compatible partner
                    $compatiblepartner = Compatible_partner::where('number', $dayno)
                        ->first();
                    $compatible_partner = array(
                        "module_name" => "Compatible Partner", "number" => $dayno,
                        "description" => strip_tags($compatiblepartner->description),
                        "more_compatible_months" => $compatiblepartner->more_compatible_months,
                        "more_compatible_dates" => $compatiblepartner->more_compatible_dates,
                        "less_compatible_months" => $compatiblepartner->less_compatible_months,
                        "less_compatible_dates" => $compatiblepartner->less_compatible_dates
                    );

                    //luckiest parameters
                    $luckyparameterdesc = Luckiest_parameter::select('lucky_colours', 'lucky_gems', 'lucky_metals')
                        ->where('number', $dayno)
                        ->first();

                    $luckyparameters = array(
                        "module_name" => "Lucky Parameters", "number" => $dayno,
                        "lucky_colours" => $luckyparameterdesc->lucky_colours,
                        "lucky_gems" => $luckyparameterdesc->lucky_gems,
                        "lucky_metals" => $luckyparameterdesc->lucky_metals
                    );

                    //planet number
                    $planet = Planet_number::select('name', 'ruling_number', 'description')
                        ->where('ruling_number', $dayno)
                        ->first();
                    $planetnumber = array(
                        "module_name" => "Planet Number", "ruling_number" => $dayno,
                        "planet_name" => $planet->name,
                        "description" => $planet->description
                    );

                    //zodiac sign
                    $formetdob = date("d-F-Y", strtotime($dob));
                    $zodiacdate = explode('-', $formetdob);
                    $dobday = $zodiacdate[0];
                    $dobmonth = $zodiacdate[1];
                    $zodiac = Zodic_sign::where('title', 'LIKE', '%' . $dobmonth . '%')
                        ->get();

                    if ($month == "March") {
                        $titledaydate = $zodiac[1]->title;
                        $explodetitle_daydate = explode(' ', $titledaydate);
                        $title_daydate = $explodetitle_daydate[2];
                        if ($dobday <= $title_daydate) {
                            $zodiacdata = $zodiac[1];
                        } else {
                            $zodiacdata = $zodiac[0];
                        }
                    } else {
                        $titledaydate = $zodiac[0]->title;
                        $explodetitle_daydate = explode(' ', $titledaydate);
                        $title_daydate = $explodetitle_daydate[2];

                        if ($dobday <= $title_daydate) {
                            $zodiacdata = $zodiac[0];
                        } else {
                            $zodiacdata = $zodiac[1];
                        }
                    }

                    $zodiacsign = array(
                        "module_name" => "Zodiac Sign", "zodiac_sign" => $zodiacdata->zodic_sign,
                        "zodiac_number" => $zodiacdata->zodic_number,
                        "zodiac_day" => $zodiacdata->zodic_day
                    );

                    //life cycle
                    $monthdescription = Life_cycle::where('number', $dayno)->value('cycle_by_month');
                    $daydescription = Life_cycle::where('number', $monthno)->value('cycle_by_date');
                    $yeardescription = Life_cycle::where('number', $yearno)->value('cycle_by_year');

                    $lifecycle = array(
                        "module_name" => "Life Cycle", "cycleone_number" => $monthno,
                        "cycleone_description" => $monthdescription,
                        "cycletwo_number" => $dayno,
                        "cycletwo_description" => $daydescription,
                        "cyclethree_number" => $yearno,
                        "cyclethree_description" => $yeardescription
                    );

                    //Name reading
                    $name = $user->name;
                    $finalname = str_replace(' ', '', $name);
                    $strname = strtoupper($finalname);
                    $splitname = str_split($strname, 1);
                    foreach ($splitname as $letter) {
                        $pytha_number[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $letter . '%')
                            ->where('systemtype_id', 1)
                            ->value('number');
                        $chald_number[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $letter . '%')
                            ->where('systemtype_id', 2)
                            ->value('number');
                    }
                    $pytha_no_sum = array_sum($pytha_number);
                    $chald_no_sum = array_sum($chald_number);
                    while (strlen($pytha_no_sum) != 1 || strlen($chald_no_sum) != 1) {
                        $pytha_no_sum = str_split($pytha_no_sum, 1);
                        $pytha_no_sum = array_sum($pytha_no_sum);
                        $chald_no_sum = str_split($chald_no_sum, 1);
                        $chald_no_sum = array_sum($chald_no_sum);
                    }
                    $pytha_description = Module_description::where('moduletype_id', 1)
                        ->where('number', $pytha_no_sum)
                        ->value('description');
                    $pytha_description = strip_tags($pytha_description);
                    $chald_description = Module_description::where('moduletype_id', 1)
                        ->where('number', $chald_no_sum)
                        ->value('description');
                    $explodenamereading_desc = explode('||', $chald_description);
                    $positive_desc = $explodenamereading_desc[0];
                    $negative_desc = $explodenamereading_desc[1];
                    $namereadingdetail = array(
                        "module_name" => "Name Reading", "Pytha_number" => $pytha_no_sum, "Pytha_description" => $pytha_description,
                        "Chald_number" => $chald_no_sum, "Chald_description" => $chald_description, "Chald_positive_title" => "Positive", "Chald_positive_desc" => $positive_desc, "Chald_negative_title" => "Negative", "Chald_negative_desc" => $negative_desc
                    );

                    /* if($chald_no_sum != 9)
					{
						$namereadingdetail = array("module_name"=>"Name Reading", "Pytha_number"=> $pytha_no_sum, "Pytha_description"=> $pytha_description,
						"Chald_number"=> $chald_no_sum, "Chald_description"=> $chald_description,);
					}else
					{
						$namereadingdetail = array("module_name"=>"Name Reading", "Pytha_number"=> $pytha_no_sum, "Pytha_description"=> $pytha_description);
					} */

                    //magic box
                    $pytha_number_count = array_count_values($pytha_number);
                    $chald_number_count = array_count_values($chald_number);
                    $magicbox = array("module_name" => "Magic Box", "pythagorean" => $pytha_number_count, "chaldean" => $chald_number_count);

                    //fav unfav parameters
                    $fav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 1)
                        ->where('month_id', $month)
                        ->where('date', $day)
                        ->first();
                    $unfav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 2)
                        ->where('month_id', $month)
                        ->where('date', $day)
                        ->first();
                    $fav_number = str_replace(' ', '', $fav->numbers);
                    $unfav_number = str_replace(' ', '', $unfav->numbers);

                    $fav_day = str_replace(',', ', ', $fav->days);
                    $fav_month = str_replace(',', ', ', $fav->months);
                    $unfav_day = str_replace(',', ', ', $unfav->days);
                    $unfav_month = str_replace(',', ', ', $unfav->months);

                    $favunfavparameters = array("favparameter" => $fav, "unfavparameters" => $unfav, 'fav_numbers_withoutSpace'=>$fav_number, 'unfav_numbers_withoutSpace'=>$unfav_number, 'fav_days_withSpace' => $fav_day,
                    'fav_months_withSpace' => $fav_month,'unfav_days_withSpace'=>$unfav_day, 'unfav_months_withSpace'=>$unfav_month);

                    //Life changes
                    $ages = Life_change::where('numbers', $dayno)->value('ages');
                    $start_year = intval($year);
                    $year_limit = $start_year + 100;
                    $years = array();
                    if ($dayno == 1) {
                        $year_sequ = array(1, 4);
                        for ($i = $start_year; $i <= $year_limit; $i++) {
                            $cal_year = str_split($i, 1);
                            $sum = array_sum($cal_year);
                            while (strlen($sum) != 1) {
                                $sum = str_split($sum);
                                $sum = array_sum($sum);
                            }
                            $sum = intval($sum);
                            if (in_array($sum, $year_sequ)) {
                                array_push($years, $i);
                            }
                        }
                    }
                    if ($dayno == 2) {
                        $year_sequ = array(2, 7);
                        for ($i = $start_year; $i <= $year_limit; $i++) {
                            $cal_year = str_split($i, 1);
                            $sum = array_sum($cal_year);
                            while (strlen($sum) != 1) {
                                $sum = str_split($sum);
                                $sum = array_sum($sum);
                            }

                            $sum = intval($sum);
                            if (in_array($sum, $year_sequ)) {
                                array_push($years, $i);
                            }
                        }
                    }
                    if ($dayno == 3) {
                        $year_sequ = array(3, 9);
                        for ($i = $start_year; $i <= $year_limit; $i++) {
                            $cal_year = str_split($i, 1);
                            $sum = array_sum($cal_year);
                            while (strlen($sum) != 1) {
                                $sum = str_split($sum);
                                $sum = array_sum($sum);
                            }
                            $sum = intval($sum);
                            if (in_array($sum, $year_sequ)) {
                                array_push($years, $i);
                            }
                        }
                    }
                    if ($dayno == 4) {
                        $year_sequ = array(4, 8, 1);
                        for ($i = $start_year; $i <= $year_limit; $i++) {
                            $cal_year = str_split($i, 1);
                            $sum = array_sum($cal_year);
                            while (strlen($sum) != 1) {
                                $sum = str_split($sum);
                                $sum = array_sum($sum);
                            }

                            $sum = intval($sum);
                            if (in_array($sum, $year_sequ)) {
                                array_push($years, $i);
                            }
                        }
                    }
                    if ($dayno == 5) {
                        $year_sequ = array(5, 6);
                        for ($i = $start_year; $i <= $year_limit; $i++) {
                            $cal_year = str_split($i, 1);
                            $sum = array_sum($cal_year);
                            while (strlen($sum) != 1) {
                                $sum = str_split($sum);
                                $sum = array_sum($sum);
                            }

                            $sum = intval($sum);
                            if (in_array($sum, $year_sequ)) {
                                array_push($years, $i);
                            }
                        }
                    }
                    if ($dayno == 6) {
                        $year_sequ = array(6, 2, 3);
                        for ($i = $start_year; $i <= $year_limit; $i++) {
                            $cal_year = str_split($i, 1);
                            $sum = array_sum($cal_year);
                            while (strlen($sum) != 1) {
                                $sum = str_split($sum);
                                $sum = array_sum($sum);
                            }

                            $sum = intval($sum);
                            if (in_array($sum, $year_sequ)) {
                                array_push($years, $i);
                            }
                        }
                    }
                    if ($dayno == 7) {
                        $year_sequ = array(2, 7);
                        for ($i = $start_year; $i <= $year_limit; $i++) {
                            $cal_year = str_split($i, 1);
                            $sum = array_sum($cal_year);
                            while (strlen($sum) != 1) {
                                $sum = str_split($sum);
                                $sum = array_sum($sum);
                            }
                            $sum = intval($sum);
                            if (in_array($sum, $year_sequ)) {
                                array_push($years, $i);
                            }
                        }
                    }
                    if ($dayno == 8) {
                        $year_sequ = array(4, 6, 8);
                        for ($i = $start_year; $i <= $year_limit; $i++) {
                            $cal_year = str_split($i, 1);
                            $sum = array_sum($cal_year);
                            while (strlen($sum) != 1) {
                                $sum = str_split($sum);
                                $sum = array_sum($sum);
                            }
                            $sum = intval($sum);
                            if (in_array($sum, $year_sequ)) {
                                array_push($years, $i);
                            }
                        }
                    }
                    if ($dayno == 9) {
                        $year_sequ = array(3, 9, 1);
                        for ($i = $start_year; $i <= $year_limit; $i++) {
                            $cal_year = str_split($i, 1);
                            $sum = array_sum($cal_year);
                            while (strlen($sum) != 1) {
                                $sum = str_split($sum);
                                $sum = array_sum($sum);
                            }
                            $sum = intval($sum);
                            if (in_array($sum, $year_sequ)) {
                                array_push($years, $i);
                            }
                        }
                    }
                    $years = implode(",", $years);

                    $lifechanges = array(
                        "module_name" => "Life Changes", "number" => $dayno, "ages" => $ages,
                        "years" => $years
                    );

                    //cosmic celander
                    $loginuserjoinyear = $user->created_at;
                    $explodejoindate = explode('-', $loginuserjoinyear);
                    $joinyear = intval($explodejoindate[0]);
                    $joinmonth = intval($explodejoindate[1]);
                    $explodedatetime = explode(' ', $explodejoindate[2]);
                    $joindateno = intval($explodedatetime[0]);
                    $currentdate = date('Y-m-d');
                    $explodecurrentdate = explode('-', $currentdate);
                    $currentdaydate = $explodecurrentdate[2];
                    $currentmonth = $explodecurrentdate[1];
                    $currentyear = $explodecurrentdate[0];
                    for ($y = $currentyear; $y <= $currentyear; $y++) {
                        $favlist = array();
                        $unfavlist = array();
                        for ($m = $currentmonth; $m <= $currentmonth; $m++) {
                            $daysinmonth = cal_days_in_month(0, $m, $y);
                            $favdata = array();
                            $unfavdata = array();

                            if ($user->subscription_status != 0 && $user->subscription_status != 2) {
                                $startday = 1;
                                $no_of_day = $daysinmonth;
                            } else {
                                if ($y == $joinyear && $m == $joinmonth) {
                                    $startday = $joindateno;
                                } else {
                                    $startday = 1;
                                }
                                if ($y == $currentyear && $m == $currentmonth) {
                                    $no_of_day = $currentdaydate;
                                } else {
                                    $no_of_day = $daysinmonth;
                                }
                            }
                            $fav_cosmic_stars = 0;
                            $unfav_cosmic_stars = 0;
                            for ($i = $startday; $i <= $no_of_day; $i++) {
                                $fav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 1)
                                    ->where('month_id', $month)
                                    ->where('date', $day)
                                    ->first();
                                $unfav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 2)
                                    ->where('month_id', $month)
                                    ->where('date', $day)
                                    ->first();
                                $fav_dates = $fav->numbers;
                                $fav_dates = str_replace(' ', '', $fav_dates);
                                $fav_dates = explode(',', $fav_dates);
                                $fav_days = $fav->days;
                                $fav_days = str_replace(' ', '', $fav_days);
                                $fav_days = explode(',', $fav_days);
                                $fav_months = $fav->months;
                                $fav_months = str_replace(' ', '', $fav_months);
                                $fav_months = explode(',', $fav_months);

                                $unfav_dates = $unfav->numbers;
                                $unfav_dates = str_replace(' ', '', $unfav_dates);
                                $unfav_dates = explode(',', $unfav_dates);
                                $unfav_days = $unfav->days;
                                $unfav_days = str_replace(' ', '', $unfav_days);
                                $unfav_days = explode(',', $unfav_days);
                                $unfav_months = $unfav->months;
                                $unfav_months = str_replace(' ', '', $unfav_months);
                                $unfav_months = explode(',', $unfav_months);

                                $current_date = explode('-', date("D-j-M-Y", mktime(0, 0, 0, $m, $i, $y)));
                                $date_sum = str_split($i, 1);
                                $date_sum = array_sum($date_sum);
                                if (strlen($date_sum) != 1) {
                                    $date_sum = str_split($date_sum);
                                    $date_sum = array_sum($date_sum);
                                }
                                $day_star = 0;
                                $month_star = 0;
                                $unfavday_star = 0;
                                $unfavmonth_star = 0;
                                if (in_array($date_sum, $fav_dates)) {
                                    $date_star = 1;
                                    if (in_array($current_date[0], $fav_days)) {
                                        $day_star = 1;
                                    }
                                    if (in_array($current_date[2], $fav_months)) {
                                        $month_star = 1;
                                    }
                                } else {
                                    $date_star = 0;
                                }
                                if (in_array($date_sum, $unfav_dates)) {
                                    $unfavdate_star = 1;
                                    if (in_array($current_date[0], $unfav_days)) {
                                        $unfavday_star = 1;
                                    }
                                    if (in_array($current_date[2], $unfav_months)) {
                                        $unfavmonth_star = 1;
                                    }
                                } else {
                                    $unfavdate_star = 0;
                                }
                                $fav_cosmic_stars = $date_star + $day_star + $month_star;
                                $unfav_cosmic_stars = $unfavdate_star + $unfavday_star + $unfavmonth_star;

                                $favdata['module_type'] = "Fav Star";
                                $favdata['year'] = $y;
                                $favdata['month'] = $m;
                                $favdata['date'] = $i;
                                $favdata['datestar'] = $fav_cosmic_stars;
                                array_push($favlist, $favdata);

                                // $favdatekey[$i] =  $fav_cosmic_stars;
                                $unfavdata['module_type'] = "Unfav Star";
                                $unfavdata['year'] = $y;
                                $unfavdata['month'] = $m;
                                $unfavdata['date'] = $i;
                                $unfavdata['datestar'] = $unfav_cosmic_stars;
                                array_push($unfavlist, $unfavdata);
                                //$unfavdatekey[$i] =  $unfav_cosmic_stars;
                            }
                            $favarraydata = $favlist;
                            $unfavarraydata = $unfavlist;
                        }
                    }

                    $cosmiccalender = array("favstars" => $favlist, "unfavstars" => $unfavlist);

                    return response()->json([
                        'status' => 1,
                        'message' => 'This OTP is verified. ',
                        'user_id' => $user_id,
                        'subscription_status' => $user->subscription_status,
                        'onboardingstatus' => 1,
                        'nextboardingstep' => 'finish',
                        'userdetail' => array('userid' => $user_id, 'fullname' => $user->name, 'dob' => $user->dob, 'user_token' => $user->user_token),
                        'Module_types' => $dobreadingdetail,
                        'primary_detail' => $primarynumber,
                        'compatible_partner' => $compatible_partner,
                        'luckyparameters' => $luckyparameters,
                        'planet_detail' => $planetnumber,
                        'zodiac_detail' => $zodiacsign,
                        'life_cycles' => $lifecycle,
                        'name_reading' => $namereadingdetail,
                        'magic_box' => $magicbox,
                        'cosmic_calender' => $cosmiccalender,
                        'favunfav_parameters' => $favunfavparameters,
                        'lifechanges' => $lifechanges,
                    ]);
                } else {
                    //Name reading
                    $finalname = str_replace(' ', '', $name);
                    $strname = strtoupper($finalname);
                    $splitname = str_split($strname, 1);
                    foreach ($splitname as $letter) {
                        $pytha_number[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $letter . '%')
                            ->where('systemtype_id', 1)
                            ->value('number');
                        $chald_number[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $letter . '%')
                            ->where('systemtype_id', 2)
                            ->value('number');
                    }
                    $pytha_no_sum = array_sum($pytha_number);
                    $chald_no_sum = array_sum($chald_number);

                    while (strlen($pytha_no_sum) != 1 || strlen($chald_no_sum) != 1) {
                        $pytha_no_sum = str_split($pytha_no_sum, 1);
                        $pytha_no_sum = array_sum($pytha_no_sum);
                        $chald_no_sum = str_split($chald_no_sum, 1);
                        $chald_no_sum = array_sum($chald_no_sum);
                    }
                    $pytha_description = Module_description::where('moduletype_id', 1)
                        ->where('number', $pytha_no_sum)
                        ->value('description');
                    $pytha_description = strip_tags($pytha_description);
                    $chalddescription = Module_description::where('moduletype_id', 1)
                        ->where('number', $chald_no_sum)
                        ->value('description');
                    $explodenamereading_desc = explode('||', $chalddescription);
                    $positive_desc = $explodenamereading_desc[0];
                    $negative_desc = $explodenamereading_desc[1];
                    $chald_description = array("positive_title" => "Positive", "positive_desc" => $positive_desc, "negative_title" => "Negative", "negative_desc" => $negative_desc);;

                    /* if($chald_no_sum != 9)
					{
						
					}else
					{
						$namereadingdesc= $pytha_description;
					} */
                    $namereadingdesc = $chald_description;
                    return response()->json([
                        'status' => 1,
                        'message' => 'This OTP is verified. ',
                        'user_id' => $user_id,
                        'onboardingstatus' => 1,
                        'nextboardingstep' => 'Step-3',
                        'Fullname' => $users->name,
                        'user_token' => $generate_user_token,
                        'namereadingdesc' => $namereadingdesc,
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'Something went wrong. Please try again.',
                    'user_id' => $user_id,
                    'onboardingstatus' => 0,
                    'nextboardingstep' => 'Step-2'
                ]);
            }
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'This OTP is not verified. Please try again.',
                'user_id' => $user_id,
                'onboardingstatus' => 0,
                'nextboardingstep' => 'Step-2'
            ]);
        }
    }

    // AutoLogin with Token
    public function loginwithtoken(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'user_id' => 'required',
                'user_token' => 'required',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $user_id = $request['user_id'];
        $user_token = $request['user_token'];
        $check_usertoken = User::where(['id' => $user_id, 'user_token' => $user_token])->first();
        if ($check_usertoken) {

            $user = User::find($user_id);

            // DOB reading 
            $dob = $user->dob;
            $date = explode('-', $dob);
            $day = $date[2];
            $month = $date[1];
            $year = $date[0];
            $dayno = str_split($day, 1);
            $dayno = array_sum($dayno);
            $dayno = intval($dayno);
            while (strlen($dayno) != 1) {
                $dayno = str_split($dayno);
                $dayno = array_sum($dayno);
            }
            $dobdesc = Module_description::where('moduletype_id', 2)
                ->where('number', $dayno)
                ->value('description');
            $dobdesc = strip_tags($dobdesc);

            $birthdayreading = array("module_type" => 2, "module_name" => "DOB Reading", "number" => $dayno, "description" => $dobdesc);

            //elemental number
            $elementaldesc = Module_description::where('moduletype_id', 4)
                ->where('number', $dayno)
                ->value('description');
            $elementaldesc = strip_tags($elementaldesc);

            $elementalno = array("module_type" => 4, "module_name" => "Elemental Number", "number" => $dayno, "description" => $elementaldesc);

            //basic health reading
            $basichealthdesc = Module_description::where('moduletype_id', 5)
                ->where('number', $dayno)
                ->value('description');
            $basichealthdesc = strip_tags($basichealthdesc);

            $basichealthreading = array("module_type" => 5, "module_name" => "Basic Health Reading", "number" => $dayno, "description" => $basichealthdesc);

            //health precaution
            $precautiondesc = Module_description::where('moduletype_id', 6)
                ->where('number', $dayno)
                ->value('description');
            $precautiondesc = strip_tags($precautiondesc);

            $healthprecaution = array("module_type" => 6, "module_name" => "Health Precaution", "number" => $dayno, "description" => $precautiondesc);

            //basic Parenting
            $basicparentingdesc = Module_description::where('moduletype_id', 12)
                ->where('number', $dayno)
                ->value('description');
            $basicparentingdesc = strip_tags($basicparentingdesc);

            $basicparenting = array(
                "module_type" => 12, "module_name" => "Basic Parent Reading",
                "number" => $dayno, "description" => $basicparentingdesc
            );

            //detail Parenting  
            $detailparentingdesc = Module_description::where('moduletype_id', 13)
                ->where('number', $dayno)
                ->value('description');
            $detailparentingdesc = strip_tags($detailparentingdesc);

            $detailparenting = array(
                "module_type" => 13, "module_name" => "Detailed Parent Reading",
                "number" => $dayno, "description" => $detailparentingdesc
            );

            //basic money 
            $basicmoneydesc = Module_description::where('moduletype_id', 14)
                ->where('number', $dayno)
                ->value('description');
            $basicmoneydesc = strip_tags($basicmoneydesc);

            $basicmoneymatter = array(
                "module_type" => 14, "module_name" => "Basic Money Matters",
                "number" => $dayno, "description" => $basicmoneydesc
            );

            //detail money 
            $detailmoneydesc = Module_description::where('moduletype_id', 15)
                ->where('number', $dayno)
                ->value('description');
            $detailmoneydesc = strip_tags($detailmoneydesc);

            $detailmoneymatter = array(
                "module_type" => 15, "module_name" => "Detailed Money Matters",
                "number" => $dayno, "description" => $detailmoneydesc
            );

            //destiny number
            $monthno = str_split($month, 1);
            $monthno = array_sum($monthno);
            while (strlen($monthno) != 1) {
                $monthno = str_split($monthno);
                $monthno = array_sum($monthno);
            }
            $yearno = str_split($year, 1);
            $yearno = array_sum($yearno);
            while (strlen($yearno) != 1) {
                $yearno = str_split($yearno);
                $yearno = array_sum($yearno);
            }
            $yearno = intval($yearno);
            $destiny_no = $dayno + $monthno + $yearno;
            while (strlen($destiny_no) != 1) {
                $destiny_no = str_split($destiny_no);
                $destiny_no = array_sum($destiny_no);
            }
            $destinynodesc = Module_description::where('moduletype_id', 16)
                ->where('number', $destiny_no)
                ->value('description');
            $destinynodesc = strip_tags($destinynodesc);
            $explode_destinynodesc = explode('||', $destinynodesc);
            $learn_desc = $explode_destinynodesc[0];
            $notlearn_desc = $explode_destinynodesc[1];
            $destinynumber = array(
                "module_type" => 16, "module_name" => "Destiny Number", "number" => $destiny_no, "description" => $destinynodesc,
                "learn_title" => "Learn To Be", "learn_desc" => $learn_desc, "notlearn_title" => "Learn Not To Be", "notlearn_desc" => $notlearn_desc
            );

            $dobreadingdetail = [
                $birthdayreading, $elementalno, $basichealthreading, $healthprecaution, $basicmoneymatter, $detailmoneymatter,
                $basicparenting, $detailparenting, $destinynumber
            ];

            //primary number
            $primarynodesc = Primaryno_type::where('number', $dayno)
                ->first();
            $primarynumber = array(
                "module_name" => "Primary Number", "number" => $dayno, "description" => $primarynodesc->description, "positive" => $primarynodesc->positive, "negative" => $primarynodesc->negative,
                "occupations" => $primarynodesc->occupations, "health" => $primarynodesc->health, "partners" => $primarynodesc->partners, "times_of_the_year" => $primarynodesc->times_of_the_year,
                "countries" => $primarynodesc->countries, "tibbits" => $primarynodesc->tibbits, "destiny_colors" => $primarynodesc->destiny_colors, "destiny_timing" => $primarynodesc->destiny_timing
            );

            //compatible partner
            $compatiblepartner = Compatible_partner::where('number', $dayno)
                ->first();

            $compatible_partner = array(
                "module_name" => "Compatible Partner", "number" => $dayno,
                "description" => strip_tags($compatiblepartner->description),
                "more_compatible_months" => $compatiblepartner->more_compatible_months,
                "more_compatible_dates" => $compatiblepartner->more_compatible_dates,
                "less_compatible_months" => $compatiblepartner->less_compatible_months,
                "less_compatible_dates" => $compatiblepartner->less_compatible_dates
            );

            //luckiest parameters
            $luckyparameterdesc = Luckiest_parameter::select('lucky_colours', 'lucky_gems', 'lucky_metals')
                ->where('number', $dayno)
                ->first();

            $luckyparameters = array(
                "module_name" => "Lucky Parameters", "number" => $dayno,
                "lucky_colours" => $luckyparameterdesc->lucky_colours,
                "lucky_gems" => $luckyparameterdesc->lucky_gems,
                "lucky_metals" => $luckyparameterdesc->lucky_metals
            );

            //planet number
            $planet = Planet_number::select('name', 'ruling_number', 'description')
                ->where('ruling_number', $dayno)
                ->first();

            $planetnumber = array(
                "module_name" => "Planet Number", "ruling_number" => $dayno,
                "planet_name" => $planet->name,
                "description" => $planet->description
            );

            //zodiac sign
            $formetdob = date("d-F-Y", strtotime($dob));
            $zodiacdate = explode('-', $formetdob);
            $dobday = $zodiacdate[0];
            $dobmonth = $zodiacdate[1];
            $zodiac = Zodic_sign::where('title', 'LIKE', '%' . $dobmonth . '%')
                ->get();

            if ($dobmonth == "March") {
                $titledaydate = $zodiac[1]->title;
                $explodetitle_daydate = explode(' ', $titledaydate);
                $title_daydate = $explodetitle_daydate[2];

                if ($dobday <= $title_daydate) {
                    $zodiacdata = $zodiac[1];
                } else {
                    $zodiacdata = $zodiac[0];
                }
            } else {
                $titledaydate = $zodiac[0]->title;
                $explodetitle_daydate = explode(' ', $titledaydate);
                $title_daydate = $explodetitle_daydate[2];

                if ($dobday <= $title_daydate) {
                    $zodiacdata = $zodiac[0];
                } else {
                    $zodiacdata = $zodiac[1];
                }
            }

            $zodiacsign = array(
                "module_name" => "Zodiac Sign", "zodiac_sign" => $zodiacdata->zodic_sign,
                "zodiac_number" => $zodiacdata->zodic_number,
                "zodiac_day" => $zodiacdata->zodic_day
            );

            //life cycle
            $monthdescription = Life_cycle::where('number', $dayno)->value('cycle_by_month');
            $daydescription = Life_cycle::where('number', $monthno)->value('cycle_by_date');
            $yeardescription = Life_cycle::where('number', $yearno)->value('cycle_by_year');

            $lifecycle = array(
                "module_name" => "Life Cycle", "cycleone_number" => $monthno,
                "cycleone_description" => $monthdescription,
                "cycletwo_number" => $dayno,
                "cycletwo_description" => $daydescription,
                "cyclethree_number" => $yearno,
                "cyclethree_description" => $yeardescription
            );

            //Name reading
            $name = $user->name;
            $finalname = str_replace(' ', '', $name);
            $strname = strtoupper($finalname);
            $splitname = str_split($strname, 1);
            foreach ($splitname as $letter) {
                $pytha_number[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $letter . '%')
                    ->where('systemtype_id', 1)
                    ->value('number');
                $chald_number[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $letter . '%')
                    ->where('systemtype_id', 2)
                    ->value('number');
            }
            $pytha_no_sum = array_sum($pytha_number);
            $chald_no_sum = array_sum($chald_number);

            while (strlen($pytha_no_sum) != 1 || strlen($chald_no_sum) != 1) {
                $pytha_no_sum = str_split($pytha_no_sum, 1);
                $pytha_no_sum = array_sum($pytha_no_sum);
                $chald_no_sum = str_split($chald_no_sum, 1);
                $chald_no_sum = array_sum($chald_no_sum);
            }
            $pytha_description = Module_description::where('moduletype_id', 1)
                ->where('number', $pytha_no_sum)
                ->value('description');
            $pytha_description = strip_tags($pytha_description);
            $chald_description = Module_description::where('moduletype_id', 1)
                ->where('number', $chald_no_sum)
                ->value('description');
            $explodenamereading_desc = explode('||', $chald_description);
            $positive_desc = $explodenamereading_desc[0];
            $negative_desc = $explodenamereading_desc[1];
            $namereadingdetail = array(
                "module_name" => "Name Reading", "Pytha_number" => $pytha_no_sum, "Pytha_description" => $pytha_description,
                "Chald_number" => $chald_no_sum, "Chald_description" => $chald_description, "Chald_positive_title" => "Positive", "Chald_positive_desc" => $positive_desc, "Chald_negative_title" => "Negative", "Chald_negative_desc" => $negative_desc
            );

            /*  if($chald_no_sum != 9)
            {
                $namereadingdetail = array("module_name"=>"Name Reading", "Pytha_number"=> $pytha_no_sum, "Pytha_description"=> $pytha_description,
                "Chald_number"=> $chald_no_sum, "Chald_description"=> $chald_description,);
            }else
            {
                $namereadingdetail = array("module_name"=>"Name Reading", "Pytha_number"=> $pytha_no_sum, "Pytha_description"=> $pytha_description);
            } */

            //magic box
            $pytha_number_count = array_count_values($pytha_number);
            $chald_number_count = array_count_values($chald_number);

            $magicbox = array("module_name" => "Magic Box", "pythagorean" => $pytha_number_count, "chaldean" => $chald_number_count);


            //fav unfav parameters
            $fav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 1)
                ->where('month_id', $month)
                ->where('date', $day)
                ->first();
            $unfav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 2)
                ->where('month_id', $month)
                ->where('date', $day)
                ->first();
            $fav_number = str_replace(' ', '', $fav->numbers);
            $unfav_number = str_replace(' ', '', $unfav->numbers);

            $fav_day = str_replace(',', ', ', $fav->days);
            $fav_month = str_replace(',', ', ', $fav->months);
            $unfav_day = str_replace(',', ', ', $unfav->days);
            $unfav_month = str_replace(',', ', ', $unfav->months);
            $favunfavparameters = array("favparameter" => $fav, "unfavparameters" => $unfav, 'fav_numbers_withoutSpace'=>$fav_number, 'unfav_numbers_withoutSpace'=>$unfav_number, 'fav_days_withSpace' => $fav_day,
            'fav_months_withSpace' => $fav_month,'unfav_days_withSpace'=>$unfav_day, 'unfav_months_withSpace'=>$unfav_month);

            //Life changes

            $ages = Life_change::where('numbers', $dayno)->value('ages');
            $start_year = intval($year);
            $year_limit = $start_year + 100;
            $years = array();
            if ($dayno == 1) {
                $year_sequ = array(1, 4);
                for ($i = $start_year; $i <= $year_limit; $i++) {
                    $cal_year = str_split($i, 1);
                    $sum = array_sum($cal_year);
                    while (strlen($sum) != 1) {
                        $sum = str_split($sum);
                        $sum = array_sum($sum);
                    }
                    $sum = intval($sum);
                    if (in_array($sum, $year_sequ)) {
                        array_push($years, $i);
                    }
                }
            }
            if ($dayno == 2) {
                $year_sequ = array(2, 7);
                for ($i = $start_year; $i <= $year_limit; $i++) {
                    $cal_year = str_split($i, 1);
                    $sum = array_sum($cal_year);
                    while (strlen($sum) != 1) {
                        $sum = str_split($sum);
                        $sum = array_sum($sum);
                    }

                    $sum = intval($sum);
                    if (in_array($sum, $year_sequ)) {
                        array_push($years, $i);
                    }
                }
            }
            if ($dayno == 3) {
                $year_sequ = array(3, 9);
                for ($i = $start_year; $i <= $year_limit; $i++) {
                    $cal_year = str_split($i, 1);
                    $sum = array_sum($cal_year);
                    while (strlen($sum) != 1) {
                        $sum = str_split($sum);
                        $sum = array_sum($sum);
                    }
                    $sum = intval($sum);
                    if (in_array($sum, $year_sequ)) {
                        array_push($years, $i);
                    }
                }
            }
            if ($dayno == 4) {
                $year_sequ = array(4, 8, 1);
                for ($i = $start_year; $i <= $year_limit; $i++) {
                    $cal_year = str_split($i, 1);
                    $sum = array_sum($cal_year);
                    while (strlen($sum) != 1) {
                        $sum = str_split($sum);
                        $sum = array_sum($sum);
                    }

                    $sum = intval($sum);
                    if (in_array($sum, $year_sequ)) {
                        array_push($years, $i);
                    }
                }
            }
            if ($dayno == 5) {
                $year_sequ = array(5, 6);
                for ($i = $start_year; $i <= $year_limit; $i++) {
                    $cal_year = str_split($i, 1);
                    $sum = array_sum($cal_year);
                    while (strlen($sum) != 1) {
                        $sum = str_split($sum);
                        $sum = array_sum($sum);
                    }

                    $sum = intval($sum);
                    if (in_array($sum, $year_sequ)) {
                        array_push($years, $i);
                    }
                }
            }
            if ($dayno == 6) {
                $year_sequ = array(6, 2, 3);
                for ($i = $start_year; $i <= $year_limit; $i++) {
                    $cal_year = str_split($i, 1);
                    $sum = array_sum($cal_year);
                    while (strlen($sum) != 1) {
                        $sum = str_split($sum);
                        $sum = array_sum($sum);
                    }

                    $sum = intval($sum);
                    if (in_array($sum, $year_sequ)) {
                        array_push($years, $i);
                    }
                }
            }
            if ($dayno == 7) {
                $year_sequ = array(2, 7);
                for ($i = $start_year; $i <= $year_limit; $i++) {
                    $cal_year = str_split($i, 1);
                    $sum = array_sum($cal_year);
                    while (strlen($sum) != 1) {
                        $sum = str_split($sum);
                        $sum = array_sum($sum);
                    }
                    $sum = intval($sum);
                    if (in_array($sum, $year_sequ)) {
                        array_push($years, $i);
                    }
                }
            }
            if ($dayno == 8) {
                $year_sequ = array(4, 6, 8);
                for ($i = $start_year; $i <= $year_limit; $i++) {
                    $cal_year = str_split($i, 1);
                    $sum = array_sum($cal_year);
                    while (strlen($sum) != 1) {
                        $sum = str_split($sum);
                        $sum = array_sum($sum);
                    }
                    $sum = intval($sum);
                    if (in_array($sum, $year_sequ)) {
                        array_push($years, $i);
                    }
                }
            }
            if ($dayno == 9) {
                $year_sequ = array(3, 9, 1);
                for ($i = $start_year; $i <= $year_limit; $i++) {
                    $cal_year = str_split($i, 1);
                    $sum = array_sum($cal_year);
                    while (strlen($sum) != 1) {
                        $sum = str_split($sum);
                        $sum = array_sum($sum);
                    }
                    $sum = intval($sum);
                    if (in_array($sum, $year_sequ)) {
                        array_push($years, $i);
                    }
                }
            }
            $years = implode(",", $years);

            $lifechanges = array(
                "module_name" => "Life Changes", "number" => $dayno, "ages" => $ages,
                "years" => $years
            );



            //cosmic celander
            $loginuserjoinyear = $user->created_at;
            $explodejoindate = explode('-', $loginuserjoinyear);
            $joinyear = intval($explodejoindate[0]);
            $joinmonth = intval($explodejoindate[1]);
            $explodedatetime = explode(' ', $explodejoindate[2]);
            $joindateno = intval($explodedatetime[0]);
            $currentdate = date('Y-m-d');
            $explodecurrentdate = explode('-', $currentdate);
            $currentdaydate = $explodecurrentdate[2];
            $currentmonth = $explodecurrentdate[1];
            $currentyear = $explodecurrentdate[0];
            for ($y = $currentyear; $y <= $currentyear; $y++) {
                $favlist = array();
                $unfavlist = array();
                for ($m = $currentmonth; $m <= $currentmonth; $m++) {
                    $daysinmonth = cal_days_in_month(0, $m, $y);
                    $favdata = array();
                    $unfavdata = array();

                    if ($user->subscription_status != 0 && $user->subscription_status != 2) {
                        $startday = 1;
                        $no_of_day = $daysinmonth;
                    } else {
                        if ($y == $joinyear && $m == $joinmonth) {
                            $startday = $joindateno;
                        } else {
                            $startday = 1;
                        }

                        if ($y == $currentyear && $m == $currentmonth) {
                            $no_of_day = $currentdaydate;
                        } else {
                            $no_of_day = $daysinmonth;
                        }
                    }
                    $fav_cosmic_stars = 0;
                    $unfav_cosmic_stars = 0;
                    for ($i = $startday; $i <= $no_of_day; $i++) {
                        $fav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 1)
                            ->where('month_id', $month)
                            ->where('date', $day)
                            ->first();
                        $unfav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 2)
                            ->where('month_id', $month)
                            ->where('date', $day)
                            ->first();
                        $fav_dates = $fav->numbers;
                        $fav_dates = str_replace(' ', '', $fav_dates);
                        $fav_dates = explode(',', $fav_dates);
                        $fav_days = $fav->days;
                        $fav_days = str_replace(' ', '', $fav_days);
                        $fav_days = explode(',', $fav_days);
                        $fav_months = $fav->months;
                        $fav_months = str_replace(' ', '', $fav_months);
                        $fav_months = explode(',', $fav_months);

                        $unfav_dates = $unfav->numbers;
                        $unfav_dates = str_replace(' ', '', $unfav_dates);
                        $unfav_dates = explode(',', $unfav_dates);
                        $unfav_days = $unfav->days;
                        $unfav_days = str_replace(' ', '', $unfav_days);
                        $unfav_days = explode(',', $unfav_days);
                        $unfav_months = $unfav->months;
                        $unfav_months = str_replace(' ', '', $unfav_months);
                        $unfav_months = explode(',', $unfav_months);

                        $current_date = explode('-', date("D-j-M-Y", mktime(0, 0, 0, $m, $i, $y)));
                        $date_sum = str_split($i, 1);
                        $date_sum = array_sum($date_sum);
                        if (strlen($date_sum) != 1) {
                            $date_sum = str_split($date_sum);
                            $date_sum = array_sum($date_sum);
                        }

                        $day_star = 0;
                        $month_star = 0;
                        $unfavday_star = 0;
                        $unfavmonth_star = 0;
                        if (in_array($date_sum, $fav_dates)) {
                            $date_star = 1;
                            if (in_array($current_date[0], $fav_days)) {
                                $day_star = 1;
                            }
                            if (in_array($current_date[2], $fav_months)) {
                                $month_star = 1;
                            }
                        } else {
                            $date_star = 0;
                        }
                        if (in_array($date_sum, $unfav_dates)) {
                            $unfavdate_star = 1;
                            if (in_array($current_date[0], $unfav_days)) {
                                $unfavday_star = 1;
                            }
                            if (in_array($current_date[2], $unfav_months)) {
                                $unfavmonth_star = 1;
                            }
                        } else {
                            $unfavdate_star = 0;
                        }

                        $fav_cosmic_stars = $date_star + $day_star + $month_star;
                        $unfav_cosmic_stars = $unfavdate_star + $unfavday_star + $unfavmonth_star;

                        $favdata['module_type'] = "Fav Star";
                        $favdata['year'] = $y;
                        $favdata['month'] = $m;
                        $favdata['date'] = $i;
                        $favdata['datestar'] = $fav_cosmic_stars;
                        array_push($favlist, $favdata);

                        // $favdatekey[$i] =  $fav_cosmic_stars;
                        $unfavdata['module_type'] = "Unfav Star";
                        $unfavdata['year'] = $y;
                        $unfavdata['month'] = $m;
                        $unfavdata['date'] = $i;
                        $unfavdata['datestar'] = $unfav_cosmic_stars;
                        array_push($unfavlist, $unfavdata);
                        //$unfavdatekey[$i] =  $unfav_cosmic_stars;
                    }
                    $favarraydata = $favlist;
                    $unfavarraydata = $unfavlist;
                }
            }

            $cosmiccalender = array("favstars" => $favarraydata, "unfavstars" => $unfavarraydata);

            return response()
                ->json([
                    'status' => 1,
                    'message' => 'Login Successfully',
                    'user_token' => $user->user_token,
                    'subscription_status' => $user->subscription_status,
                    'userdetail' => array('userid' => $user_id, 'fullname' => $user->name, 'dob' => $user->dob),
                    'Module_types' => $dobreadingdetail,
                    'primary_detail' => $primarynumber,
                    'compatible_partner' => $compatible_partner,
                    'luckyparameters' => $luckyparameters,
                    'planet_detail' => $planetnumber,
                    'zodiac_detail' => $zodiacsign,
                    'life_cycles' => $lifecycle,
                    'name_reading' => $namereadingdetail,
                    'magic_box' => $magicbox,
                    'cosmic_calender' => $cosmiccalender,
                    'favunfav_parameters' => $favunfavparameters,
                    'lifechanges' => $lifechanges,

                ]);
        } else {
            return response()->json(['status' => 0, 'message' => 'Invalid login credential!']);
        }
    }

    public function otheruserdetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'otheruserid' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $user_id = $request->user_id;
        $otheruserid = $request->otheruserid;
        $loginuser = User::find($user_id);
        $firstDateOfWeek = date("Y-m-d", strtotime('monday this week'));
        $lastdateOfWeek = date("Y-m-d", strtotime('sunday this week'));

        $otherUserReadings = User_namereading::where('user_id', $user_id)
            ->whereBetween('created_at', [$firstDateOfWeek . " 00:00:00", $lastdateOfWeek . " 23:59:59"])->get();

        if (count($otherUserReadings) == 0 && $loginuser->subscription_status == 0) {
            $message = "You have only Two compatibility check left in this week.";
        } elseif (count($otherUserReadings) == 0 && $loginuser->subscription_status == 2) {
            $message = "You have only Two compatibility check left in this week.";
        }elseif (count($otherUserReadings) == 1 && $loginuser->subscription_status == 0) {
            $message = "You have only One compatibility check left in this week.";
        } elseif (count($otherUserReadings) == 1 && $loginuser->subscription_status == 2) {
            $message = "You have only One compatibility check left in this week.";
        }elseif (count($otherUserReadings) == 2 && $loginuser->subscription_status == 0) {
            $message = "You don't have any compatibility check left in this week. Please wait for next week Or Upgrade and Subscribe to Unlock Premium Features!";
        } elseif (count($otherUserReadings) == 2 && $loginuser->subscription_status == 2) {
            $message = "You don't have any compatibility checks left this week. Please wait for next week Or Your monthly plan has been paused. Please resume and unlock premium features!";
        } else {
            $message = "Sucess";
        }
        if (count($otherUserReadings) >= 3 && $loginuser->subscription_status == 0) {
            return response()->json([
                'status' => 0,
                'message' => "You don't have any compatibility check left in this week. Please wait for next week Or Upgrade and Subscribe to Unlock Premium Features!",
                'subscription_status' => $loginuser->subscription_status,
            ]);
        }elseif (count($otherUserReadings) >= 3 && $loginuser->subscription_status == 2) {
            return response()->json([
                'status' => 0,
                'message' => "You don't have any compatibility checks left this week. Please wait for next week Or Your monthly plan has been paused. Please resume and unlock premium features!",
                'subscription_status' => $loginuser->subscription_status,
            ]);
        } else {
            $user = User_namereading::where('user_id', $user_id)->where('id', $otheruserid)->first();
            if ($user) {

                // DOB reading 
                $dob = $user->dob;
                $date = explode('-', $dob);
                $day = $date[2];
                $month = $date[1];
                $year = $date[0];
                $dayno = str_split($day, 1);
                $dayno = array_sum($dayno);
                $dayno = intval($dayno);
                while (strlen($dayno) != 1) {
                    $dayno = str_split($dayno);
                    $dayno = array_sum($dayno);
                }

                $dobdesc = Module_description::where('moduletype_id', 2)
                    ->where('number', $dayno)
                    ->value('description');
                $dobdesc = strip_tags($dobdesc);

                $birthdayreading = array("module_type" => 2, "module_name" => "DOB Reading", "number" => $dayno, "description" => $dobdesc);

                //elemental number
                $elementaldesc = Module_description::where('moduletype_id', 4)
                    ->where('number', $dayno)
                    ->value('description');
                $elementaldesc = strip_tags($elementaldesc);

                $elementalno = array("module_type" => 4, "module_name" => "Elemental Number", "number" => $dayno, "description" => $elementaldesc);

                //basic health reading
                $basichealthdesc = Module_description::where('moduletype_id', 5)
                    ->where('number', $dayno)
                    ->value('description');
                $basichealthdesc = strip_tags($basichealthdesc);

                $basichealthreading = array("module_type" => 5, "module_name" => "Basic Health Reading", "number" => $dayno, "description" => $basichealthdesc);

                //health precaution
                $precautiondesc = Module_description::where('moduletype_id', 6)
                    ->where('number', $dayno)
                    ->value('description');
                $precautiondesc = strip_tags($precautiondesc);

                $healthprecaution = array("module_type" => 6, "module_name" => "Health Precaution", "number" => $dayno, "description" => $precautiondesc);

                //basic Parenting
                $basicparentingdesc = Module_description::where('moduletype_id', 12)
                    ->where('number', $dayno)
                    ->value('description');
                $basicparentingdesc = strip_tags($basicparentingdesc);

                $basicparenting = array(
                    "module_type" => 12, "module_name" => "Basic Parent Reading",
                    "number" => $dayno, "description" => $basicparentingdesc
                );

                //detail Parenting  
                $detailparentingdesc = Module_description::where('moduletype_id', 13)
                    ->where('number', $dayno)
                    ->value('description');
                $detailparentingdesc = strip_tags($detailparentingdesc);

                $detailparenting = array(
                    "module_type" => 13, "module_name" => "Detailed Parent Reading",
                    "number" => $dayno, "description" => $detailparentingdesc
                );

                //basic money 
                $basicmoneydesc = Module_description::where('moduletype_id', 14)
                    ->where('number', $dayno)
                    ->value('description');
                $basicmoneydesc = strip_tags($basicmoneydesc);

                $basicmoneymatter = array(
                    "module_type" => 14, "module_name" => "Basic Money Matters",
                    "number" => $dayno, "description" => $basicmoneydesc
                );

                //detail money 
                $detailmoneydesc = Module_description::where('moduletype_id', 15)
                    ->where('number', $dayno)
                    ->value('description');
                $detailmoneydesc = strip_tags($detailmoneydesc);

                $detailmoneymatter = array(
                    "module_type" => 15, "module_name" => "Detailed Money Matters",
                    "number" => $dayno, "description" => $detailmoneydesc
                );

                //destiny number
                $monthno = str_split($month, 1);
                $monthno = array_sum($monthno);
                while (strlen($monthno) != 1) {
                    $monthno = str_split($monthno);
                    $monthno = array_sum($monthno);
                }
                $yearno = str_split($year, 1);
                $yearno = array_sum($yearno);
                while (strlen($yearno) != 1) {
                    $yearno = str_split($yearno);
                    $yearno = array_sum($yearno);
                }
                $yearno = intval($yearno);
                $destiny_no = $dayno + $monthno + $yearno;
                while (strlen($destiny_no) != 1) {
                    $destiny_no = str_split($destiny_no);
                    $destiny_no = array_sum($destiny_no);
                }
                $destinynodesc = Module_description::where('moduletype_id', 16)
                    ->where('number', $destiny_no)
                    ->value('description');
                $destinynodesc = strip_tags($destinynodesc);
                $explode_destinynodesc = explode('||', $destinynodesc);
                $learn_desc = $explode_destinynodesc[0];
                $notlearn_desc = $explode_destinynodesc[1];
                $destinynumber = array(
                    "module_type" => 16, "module_name" => "Destiny Number", "number" => $destiny_no, "description" => $destinynodesc,
                    "learn_title" => "Learn To Be", "learn_desc" => $learn_desc, "notlearn_title" => "Learn Not To Be", "notlearn_desc" => $notlearn_desc
                );
                $dobreadingdetail = [
                    $birthdayreading, $elementalno, $basichealthreading, $healthprecaution, $basicmoneymatter, $detailmoneymatter,
                    $basicparenting, $detailparenting, $destinynumber
                ];

                //primary number
                $primarynodesc = Primaryno_type::where('number', $dayno)
                    ->first();
                $primarynumber = array(
                    "module_name" => "Primary Number", "number" => $dayno, "description" => $primarynodesc->description, "positive" => $primarynodesc->positive, "negative" => $primarynodesc->negative,
                    "occupations" => $primarynodesc->occupations, "health" => $primarynodesc->health, "partners" => $primarynodesc->partners, "times_of_the_year" => $primarynodesc->times_of_the_year,
                    "countries" => $primarynodesc->countries, "tibbits" => $primarynodesc->tibbits, "destiny_colors" => $primarynodesc->destiny_colors, "destiny_timing" => $primarynodesc->destiny_timing
                );

                //compatible partner
                $compatiblepartner = Compatible_partner::where('number', $dayno)
                    ->first();

                $compatible_partner = array(
                    "module_name" => "Compatible Partner", "number" => $dayno,
                    "description" => strip_tags($compatiblepartner->description),
                    "more_compatible_months" => $compatiblepartner->more_compatible_months,
                    "more_compatible_dates" => $compatiblepartner->more_compatible_dates,
                    "less_compatible_months" => $compatiblepartner->less_compatible_months,
                    "less_compatible_dates" => $compatiblepartner->less_compatible_dates
                );

                //luckiest parameters
                $luckyparameterdesc = Luckiest_parameter::select('lucky_colours', 'lucky_gems', 'lucky_metals')
                    ->where('number', $dayno)
                    ->first();

                $luckyparameters = array(
                    "module_name" => "Lucky Parameters", "number" => $dayno,
                    "lucky_colours" => $luckyparameterdesc->lucky_colours,
                    "lucky_gems" => $luckyparameterdesc->lucky_gems,
                    "lucky_metals" => $luckyparameterdesc->lucky_metals
                );

                //planet number
                $planet = Planet_number::select('name', 'ruling_number', 'description')
                    ->where('ruling_number', $dayno)
                    ->first();

                $planetnumber = array(
                    "module_name" => "Planet Number", "ruling_number" => $dayno,
                    "planet_name" => $planet->name,
                    "description" => $planet->description
                );

                //zodiac sign
                $formetdob = date("d-F-Y", strtotime($dob));
                $zodiacdate = explode('-', $formetdob);
                $dobday = $zodiacdate[0];
                $dobmonth = $zodiacdate[1];
                $zodiac = Zodic_sign::where('title', 'LIKE', '%' . $dobmonth . '%')
                    ->get();

                if ($dobmonth == "March") {
                    $titledaydate = $zodiac[1]->title;
                    $explodetitle_daydate = explode(' ', $titledaydate);
                    $title_daydate = $explodetitle_daydate[2];

                    if ($dobday <= $title_daydate) {
                        $zodiacdata = $zodiac[1];
                    } else {
                        $zodiacdata = $zodiac[0];
                    }
                } else {
                    $titledaydate = $zodiac[0]->title;
                    $explodetitle_daydate = explode(' ', $titledaydate);
                    $title_daydate = $explodetitle_daydate[2];

                    if ($dobday <= $title_daydate) {
                        $zodiacdata = $zodiac[0];
                    } else {
                        $zodiacdata = $zodiac[1];
                    }
                }

                $zodiacsign = array(
                    "module_name" => "Zodiac Sign", "zodiac_sign" => $zodiacdata->zodic_sign,
                    "zodiac_number" => $zodiacdata->zodic_number,
                    "zodiac_day" => $zodiacdata->zodic_day
                );

                //life cycle
                $monthdescription = Life_cycle::where('number', $dayno)->value('cycle_by_month');
                $daydescription = Life_cycle::where('number', $monthno)->value('cycle_by_date');
                $yeardescription = Life_cycle::where('number', $yearno)->value('cycle_by_year');

                $lifecycle = array(
                    "module_name" => "Life Cycle", "cycleone_number" => $monthno,
                    "cycleone_description" => $monthdescription,
                    "cycletwo_number" => $dayno,
                    "cycletwo_description" => $daydescription,
                    "cyclethree_number" => $yearno,
                    "cyclethree_description" => $yeardescription
                );

                //Name reading
                $name = $user->name;
                $finalname = str_replace(' ', '', $name);
                $strname = strtoupper($finalname);
                $splitname = str_split($strname, 1);
                foreach ($splitname as $letter) {
                    $pytha_number[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $letter . '%')
                        ->where('systemtype_id', 1)
                        ->value('number');
                    $chald_number[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $letter . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                }
                $pytha_no_sum = array_sum($pytha_number);
                $chald_no_sum = array_sum($chald_number);

                while (strlen($pytha_no_sum) != 1 || strlen($chald_no_sum) != 1) {
                    $pytha_no_sum = str_split($pytha_no_sum, 1);
                    $pytha_no_sum = array_sum($pytha_no_sum);
                    $chald_no_sum = str_split($chald_no_sum, 1);
                    $chald_no_sum = array_sum($chald_no_sum);
                }
                $pytha_description = Module_description::where('moduletype_id', 1)
                    ->where('number', $pytha_no_sum)
                    ->value('description');
                $pytha_description = strip_tags($pytha_description);
                $chald_description = Module_description::where('moduletype_id', 1)
                    ->where('number', $chald_no_sum)
                    ->value('description');
                $explodenamereading_desc = explode('||', $chald_description);
                $positive_desc = $explodenamereading_desc[0];
                $negative_desc = $explodenamereading_desc[1];

                $namereadingdetail = array(
                    "module_name" => "Name Reading", "Pytha_number" => $pytha_no_sum, "Pytha_description" => $pytha_description, "Chald_number" => $chald_no_sum, "Chald_description" => $chald_description,
                    "Chald_positive_title" => "Positive", "Chald_positive_desc" => $positive_desc, "Chald_negative_title" => "Negative", "Chald_negative_desc" => $negative_desc
                );

                /* if($chald_no_sum != 9)
                {
                    $namereadingdetail = array("module_name"=>"Name Reading", "Pytha_number"=> $pytha_no_sum, "Pytha_description"=> $pytha_description,
                    "Chald_number"=> $chald_no_sum, "Chald_description"=> $chald_description,);
                }else
                {
                    $namereadingdetail = array("module_name"=>"Name Reading", "Pytha_number"=> $pytha_no_sum, "Pytha_description"=> $pytha_description);
                } */

                //magic box

                //magic box
                $pytha_number_count = array_count_values($pytha_number);
                $chald_number_count = array_count_values($chald_number);

                if (array_key_exists(1, $chald_number_count)) {
                    $magicboxnumber1 = $chald_number_count['1'];
                } else {
                    $magicboxnumber1 = 0;
                }
                $magicboxnumber1_decs = Module_description::where('moduletype_id', 3)
                    ->where('number', 1)
                    ->first();

                $magicboxnumber1decs = explode('||', strip_tags($magicboxnumber1_decs->description));
                $box1decs = $magicboxnumber1decs[0];
                $box1manydecs = $magicboxnumber1decs[1];
                $box1fewdecs = $magicboxnumber1decs[2];
                if ($magicboxnumber1_decs->mogicbox_average <= $magicboxnumber1) {
                    $title = 'Many 1s';
                    $description = $box1manydecs;
                } else {
                    $title = 'Few/No 1s';
                    $description = $box1fewdecs;
                }
                $magicboxnumberdecs1 = array('Box' => $box1decs, 'title' => $title, 'description' => $description, 'average_desc' => "Three 1s are average.");

                $boxcellno1 = array('number_heading' => 'number_heading', 'number' => 1, 'numbervalue' => $magicboxnumber1, 'numberdescription' => $magicboxnumberdecs1);

                if (array_key_exists(2, $chald_number_count)) {
                    $magicboxnumber2 = $chald_number_count['2'];
                } else {
                    $magicboxnumber2 = 0;
                }
                $magicboxnumber2_decs = Module_description::where('moduletype_id', 3)
                    ->where('number', 2)
                    ->first();

                $magicboxnumber2decs = explode('||', strip_tags($magicboxnumber2_decs->description));
                $box2decs = $magicboxnumber2decs[0];
                $box2manydecs = $magicboxnumber2decs[1];
                $box2fewdecs = $magicboxnumber2decs[2];
                if ($magicboxnumber2_decs->mogicbox_average <= $magicboxnumber2) {
                    $title = 'Many 2s';
                    $description = $box2manydecs;
                } else {
                    $title = 'Few/No 2s';
                    $description = $box2fewdecs;
                }
                $magicboxnumberdecs2 = array('Box' => $box2decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 2 is average.");

                $boxcellno2 = array('number_heading' => 'number_heading', 'number' => 2, 'numbervalue' => $magicboxnumber2, 'numberdescription' => $magicboxnumberdecs2);

                if (array_key_exists(3, $chald_number_count)) {
                    $magicboxnumber3 = $chald_number_count['3'];
                } else {
                    $magicboxnumber3 = 0;
                }
                $magicboxnumber3_decs = Module_description::where('moduletype_id', 3)
                    ->where('number', 3)
                    ->first();

                $magicboxnumber3decs = explode('||', strip_tags($magicboxnumber3_decs->description));
                $box3decs = $magicboxnumber3decs[0];
                $box3manydecs = $magicboxnumber3decs[1];
                $box3fewdecs = $magicboxnumber3decs[2];
                if ($magicboxnumber3_decs->mogicbox_average <= $magicboxnumber3) {
                    $title = 'Many 3s';
                    $description = $box3manydecs;
                } else {
                    $title = 'Few/No 3s';
                    $description = $box3fewdecs;
                }
                $magicboxnumberdecs3 = array('Box' => $box3decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 3 is average.");

                $boxcellno3 = array('number_heading' => 'number_heading', 'number' => 3, 'numbervalue' => $magicboxnumber3, 'numberdescription' => $magicboxnumberdecs3);

                if (array_key_exists(4, $chald_number_count)) {
                    $magicboxnumber4 = $chald_number_count['4'];
                } else {
                    $magicboxnumber4 = 0;
                }
                $magicboxnumber4_decs = Module_description::where('moduletype_id', 3)
                    ->where('number', 4)
                    ->first();

                $magicboxnumber4decs = explode('||', strip_tags($magicboxnumber4_decs->description));
                $box4decs = $magicboxnumber4decs[0];
                $box4manydecs = $magicboxnumber4decs[1];
                $box4fewdecs = $magicboxnumber4decs[2];
                if ($magicboxnumber4_decs->mogicbox_average <= $magicboxnumber4) {
                    $title = 'Many 4s';
                    $description = $box4manydecs;
                } else {
                    $title = 'Few/No 4s';
                    $description = $box4fewdecs;
                }

                $magicboxnumberdecs4 = array('Box' => $box4decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 4 is average.");

                $boxcellno4 = array('number_heading' => 'number_heading', 'number' => 4, 'numbervalue' => $magicboxnumber4, 'numberdescription' => $magicboxnumberdecs4);

                if (array_key_exists(5, $chald_number_count)) {
                    $magicboxnumber5 = $chald_number_count['5'];
                } else {
                    $magicboxnumber5 = 0;
                }
                $magicboxnumber5_decs = Module_description::where('moduletype_id', 3)
                    ->where('number', 5)
                    ->first();

                $magicboxnumber5decs = explode('||', strip_tags($magicboxnumber5_decs->description));
                $box5decs = $magicboxnumber5decs[0];
                $box5manydecs = $magicboxnumber5decs[1];
                $box5fewdecs = $magicboxnumber5decs[2];
                if ($magicboxnumber5_decs->mogicbox_average <= $magicboxnumber5) {
                    $title = 'Many 5s';
                    $description = $box5manydecs;
                } else {
                    $title = 'Few/No 5s';
                    $description = $box5fewdecs;
                }

                $magicboxnumberdecs5 = array('Box' => $box5decs, 'title' => $title, 'description' => $description, 'average_desc' => "Three 5s are average.");

                $boxcellno5 = array('number_heading' => 'number_heading', 'number' => 5, 'numbervalue' => $magicboxnumber5, 'numberdescription' => $magicboxnumberdecs5);


                if (array_key_exists(6, $chald_number_count)) {
                    $magicboxnumber6 = $chald_number_count['6'];
                } else {
                    $magicboxnumber6 = 0;
                }
                $magicboxnumber6_decs = Module_description::where('moduletype_id', 3)
                    ->where('number', 6)
                    ->first();

                $magicboxnumber6decs = explode('||', strip_tags($magicboxnumber6_decs->description));
                $box6decs = $magicboxnumber6decs[0];
                $box6manydecs = $magicboxnumber6decs[1];
                $box6fewdecs = $magicboxnumber6decs[2];
                if ($magicboxnumber6_decs->mogicbox_average <= $magicboxnumber6) {
                    $title = 'Many 6s';
                    $description = $box6manydecs;
                } else {
                    $title = 'Few/No 6s';
                    $description = $box6fewdecs;
                }

                $magicboxnumberdecs6 = array('Box' => $box6decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 6 is average.");

                $boxcellno6 = array('number_heading' => 'number_heading', 'number' => 6, 'numbervalue' => $magicboxnumber6, 'numberdescription' => $magicboxnumberdecs6);

                if (array_key_exists(7, $chald_number_count)) {
                    $magicboxnumber7 = $chald_number_count['7'];
                } else {
                    $magicboxnumber7 = 0;
                }

                $magicboxnumber7_decs = Module_description::where('moduletype_id', 3)
                    ->where('number', 7)
                    ->first();

                $magicboxnumber7decs = explode('||', strip_tags($magicboxnumber7_decs->description));
                $box7decs = $magicboxnumber7decs[0];
                $box7manydecs = $magicboxnumber7decs[1];
                $box7fewdecs = $magicboxnumber7decs[2];
                if ($magicboxnumber7_decs->mogicbox_average <= $magicboxnumber7) {
                    $title = 'Many 7s';
                    $description = $box7manydecs;
                } else {
                    $title = 'Few/No 7s';
                    $description = $box7fewdecs;
                }

                $magicboxnumberdecs7 = array('Box' => $box7decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 7 is average.");

                $boxcellno7 = array('number_heading' => 'number_heading', 'number' => 7, 'numbervalue' => $magicboxnumber7, 'numberdescription' => $magicboxnumberdecs7);

                if (array_key_exists(8, $chald_number_count)) {
                    $magicboxnumber8 = $chald_number_count['8'];
                } else {
                    $magicboxnumber8 = 0;
                }
                $magicboxnumber8_decs = Module_description::where('moduletype_id', 3)
                    ->where('number', 8)
                    ->first();

                $magicboxnumber8decs = explode('||', strip_tags($magicboxnumber8_decs->description));
                $box8decs = $magicboxnumber8decs[0];
                $box8manydecs = $magicboxnumber8decs[1];
                $box8fewdecs = $magicboxnumber8decs[2];
                if ($magicboxnumber8_decs->mogicbox_average <= $magicboxnumber8) {
                    $title = 'Many 8s';
                    $description = $box8manydecs;
                } else {
                    $title = 'Few/No 8s';
                    $description = $box8fewdecs;
                }

                $magicboxnumberdecs8 = array('Box' => $box8decs, 'title' => $title, 'description' => $description, 'average_desc' => "Three 8s are average.");

                $boxcellno8 = array('number_heading' => 'number_heading', 'number' => 8, 'numbervalue' => $magicboxnumber8, 'numberdescription' => $magicboxnumberdecs8);

                if (array_key_exists(9, $chald_number_count)) {
                    $magicboxnumber9 = $chald_number_count['9'];
                } else {
                    $magicboxnumber9 = 0;
                }
                $magicboxnumber9_decs = Module_description::where('moduletype_id', 3)
                    ->where('number', 9)
                    ->first();
                $magicboxnumber9decs = explode('||', strip_tags($magicboxnumber9_decs->description));
                $box9decs = $magicboxnumber9decs[0];
                $box9manydecs = $magicboxnumber9decs[1];
                $box9fewdecs = $magicboxnumber9decs[2];
                if ($magicboxnumber9_decs->mogicbox_average <= $magicboxnumber9) {
                    $title = 'Many 9s';
                    $description = $box9manydecs;
                } else {
                    $title = 'Few/No 9s';
                    $description = $box9fewdecs;
                }

                $magicboxnumberdecs9 = array('Box' => $box9decs, 'title' => $title, 'description' => $description, 'average_desc' => "Three 9s are average.");
                $boxcellno9 = array('number_heading' => 'number_heading', 'number' => 9, 'numbervalue' => $magicboxnumber9, 'numberdescription' => $magicboxnumberdecs9);

                $magicboxdetail = array($boxcellno1, $boxcellno2, $boxcellno3, $boxcellno4, $boxcellno5, $boxcellno6, $boxcellno7, $boxcellno8, $boxcellno9);


                //magicboxdesclist

                $magicboxdesclist = array('number1description' => $magicboxnumberdecs1, 'number2description' => $magicboxnumberdecs2, 'number3description' => $magicboxnumberdecs3, 'number4description' => $magicboxnumberdecs4, 'number5description' => $magicboxnumberdecs5, 'number6description' => $magicboxnumberdecs6, 'number7description' => $magicboxnumberdecs7, 'number8description' => $magicboxnumberdecs8, 'number9description' => $magicboxnumberdecs9);

                //fav unfav parameters
                $fav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 1)
                    ->where('month_id', $month)
                    ->where('date', $day)
                    ->first();
                $unfav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 2)
                    ->where('month_id', $month)
                    ->where('date', $day)
                    ->first();
                $fav_number = str_replace(' ', '', $fav->numbers);
                $unfav_number = str_replace(' ', '', $unfav->numbers);

                $fav_day = str_replace(',', ', ', $fav->days);
                $fav_month = str_replace(',', ', ', $fav->months);
                $unfav_day = str_replace(',', ', ', $unfav->days);
                $unfav_month = str_replace(',', ', ', $unfav->months);
                $favunfavparameters = array("favparameter" => $fav, "unfavparameters" => $unfav, 'fav_numbers_withoutSpace'=>$fav_number, 'unfav_numbers_withoutSpace'=>$unfav_number, 'fav_days_withSpace' => $fav_day,
                'fav_months_withSpace' => $fav_month,'unfav_days_withSpace'=>$unfav_day, 'unfav_months_withSpace'=>$unfav_month);

                //Life changes

                $ages = Life_change::where('numbers', $dayno)->value('ages');
                $start_year = intval($year);
                $year_limit = $start_year + 100;
                $years = array();
                if ($dayno == 1) {
                    $year_sequ = array(1, 4);
                    for ($i = $start_year; $i <= $year_limit; $i++) {
                        $cal_year = str_split($i, 1);
                        $sum = array_sum($cal_year);
                        while (strlen($sum) != 1) {
                            $sum = str_split($sum);
                            $sum = array_sum($sum);
                        }
                        $sum = intval($sum);
                        if (in_array($sum, $year_sequ)) {
                            array_push($years, $i);
                        }
                    }
                }
                if ($dayno == 2) {
                    $year_sequ = array(2, 7);
                    for ($i = $start_year; $i <= $year_limit; $i++) {
                        $cal_year = str_split($i, 1);
                        $sum = array_sum($cal_year);
                        while (strlen($sum) != 1) {
                            $sum = str_split($sum);
                            $sum = array_sum($sum);
                        }

                        $sum = intval($sum);
                        if (in_array($sum, $year_sequ)) {
                            array_push($years, $i);
                        }
                    }
                }
                if ($dayno == 3) {
                    $year_sequ = array(3, 9);
                    for ($i = $start_year; $i <= $year_limit; $i++) {
                        $cal_year = str_split($i, 1);
                        $sum = array_sum($cal_year);
                        while (strlen($sum) != 1) {
                            $sum = str_split($sum);
                            $sum = array_sum($sum);
                        }
                        $sum = intval($sum);
                        if (in_array($sum, $year_sequ)) {
                            array_push($years, $i);
                        }
                    }
                }
                if ($dayno == 4) {
                    $year_sequ = array(4, 8, 1);
                    for ($i = $start_year; $i <= $year_limit; $i++) {
                        $cal_year = str_split($i, 1);
                        $sum = array_sum($cal_year);
                        while (strlen($sum) != 1) {
                            $sum = str_split($sum);
                            $sum = array_sum($sum);
                        }

                        $sum = intval($sum);
                        if (in_array($sum, $year_sequ)) {
                            array_push($years, $i);
                        }
                    }
                }
                if ($dayno == 5) {
                    $year_sequ = array(5, 6);
                    for ($i = $start_year; $i <= $year_limit; $i++) {
                        $cal_year = str_split($i, 1);
                        $sum = array_sum($cal_year);
                        while (strlen($sum) != 1) {
                            $sum = str_split($sum);
                            $sum = array_sum($sum);
                        }

                        $sum = intval($sum);
                        if (in_array($sum, $year_sequ)) {
                            array_push($years, $i);
                        }
                    }
                }
                if ($dayno == 6) {
                    $year_sequ = array(6, 2, 3);
                    for ($i = $start_year; $i <= $year_limit; $i++) {
                        $cal_year = str_split($i, 1);
                        $sum = array_sum($cal_year);
                        while (strlen($sum) != 1) {
                            $sum = str_split($sum);
                            $sum = array_sum($sum);
                        }

                        $sum = intval($sum);
                        if (in_array($sum, $year_sequ)) {
                            array_push($years, $i);
                        }
                    }
                }
                if ($dayno == 7) {
                    $year_sequ = array(2, 7);
                    for ($i = $start_year; $i <= $year_limit; $i++) {
                        $cal_year = str_split($i, 1);
                        $sum = array_sum($cal_year);
                        while (strlen($sum) != 1) {
                            $sum = str_split($sum);
                            $sum = array_sum($sum);
                        }
                        $sum = intval($sum);
                        if (in_array($sum, $year_sequ)) {
                            array_push($years, $i);
                        }
                    }
                }
                if ($dayno == 8) {
                    $year_sequ = array(4, 6, 8);
                    for ($i = $start_year; $i <= $year_limit; $i++) {
                        $cal_year = str_split($i, 1);
                        $sum = array_sum($cal_year);
                        while (strlen($sum) != 1) {
                            $sum = str_split($sum);
                            $sum = array_sum($sum);
                        }
                        $sum = intval($sum);
                        if (in_array($sum, $year_sequ)) {
                            array_push($years, $i);
                        }
                    }
                }
                if ($dayno == 9) {
                    $year_sequ = array(3, 9, 1);
                    for ($i = $start_year; $i <= $year_limit; $i++) {
                        $cal_year = str_split($i, 1);
                        $sum = array_sum($cal_year);
                        while (strlen($sum) != 1) {
                            $sum = str_split($sum);
                            $sum = array_sum($sum);
                        }
                        $sum = intval($sum);
                        if (in_array($sum, $year_sequ)) {
                            array_push($years, $i);
                        }
                    }
                }
                $years = implode(",", $years);

                $lifechanges = array(
                    "module_name" => "Life Changes", "number" => $dayno, "ages" => $ages,
                    "years" => $years
                );



                //cosmic celander
                $userjoinyear = $user->created_at;
                $explodejoindate = explode('-', $userjoinyear);
                $joinyear = intval($explodejoindate[0]);
                $joinmonth = intval($explodejoindate[1]);
                $explodedatetime = explode(' ', $explodejoindate[2]);
                $joindateno = intval($explodedatetime[0]);
                $currentdate = date('Y-m-d');
                $explodecurrentdate = explode('-', $currentdate);
                $currentdaydate = $explodecurrentdate[2];
                $currentmonth = $explodecurrentdate[1];
                $currentyear = $explodecurrentdate[0];
                for ($y = $currentyear; $y <= $currentyear; $y++) {
                    $favlist = array();
                    $unfavlist = array();
                    for ($m = $currentmonth; $m <= $currentmonth; $m++) {
                        $daysinmonth = cal_days_in_month(0, $m, $y);
                        $favdata = array();
                        $unfavdata = array();

                        if ($user->subscription_status != 0 && $user->subscription_status != 2) {
                            $startday = 1;
                            $no_of_day = $daysinmonth;
                        } else {
                            if ($y == $joinyear && $m == $joinmonth) {
                                $startday = $joindateno;
                            } else {
                                $startday = 1;
                            }

                            if ($y == $currentyear && $m == $currentmonth) {
                                $no_of_day = $currentdaydate;
                            } else {
                                $no_of_day = $daysinmonth;
                            }
                        }
                        $fav_cosmic_stars = 0;
                        $unfav_cosmic_stars = 0;
                        for ($i = $startday; $i <= $no_of_day; $i++) {
                            $fav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 1)
                                ->where('month_id', $month)
                                ->where('date', $day)
                                ->first();
                            $unfav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 2)
                                ->where('month_id', $month)
                                ->where('date', $day)
                                ->first();
                            $fav_dates = $fav->numbers;
                            $fav_dates = str_replace(' ', '', $fav_dates);
                            $fav_dates = explode(',', $fav_dates);
                            $fav_days = $fav->days;
                            $fav_days = str_replace(' ', '', $fav_days);
                            $fav_days = explode(',', $fav_days);
                            $fav_months = $fav->months;
                            $fav_months = str_replace(' ', '', $fav_months);
                            $fav_months = explode(',', $fav_months);

                            $unfav_dates = $unfav->numbers;
                            $unfav_dates = str_replace(' ', '', $unfav_dates);
                            $unfav_dates = explode(',', $unfav_dates);
                            $unfav_days = $unfav->days;
                            $unfav_days = str_replace(' ', '', $unfav_days);
                            $unfav_days = explode(',', $unfav_days);
                            $unfav_months = $unfav->months;
                            $unfav_months = str_replace(' ', '', $unfav_months);
                            $unfav_months = explode(',', $unfav_months);

                            $current_date = explode('-', date("D-j-M-Y", mktime(0, 0, 0, $m, $i, $y)));
                            $date_sum = str_split($i, 1);
                            $date_sum = array_sum($date_sum);
                            if (strlen($date_sum) != 1) {
                                $date_sum = str_split($date_sum);
                                $date_sum = array_sum($date_sum);
                            }

                            $day_star = 0;
                            $month_star = 0;
                            $unfavday_star = 0;
                            $unfavmonth_star = 0;
                            if (in_array($date_sum, $fav_dates)) {
                                $date_star = 1;
                                if (in_array($current_date[0], $fav_days)) {
                                    $day_star = 1;
                                }
                                if (in_array($current_date[2], $fav_months)) {
                                    $month_star = 1;
                                }
                            } else {
                                $date_star = 0;
                            }
                            if (in_array($date_sum, $unfav_dates)) {
                                $unfavdate_star = 1;
                                if (in_array($current_date[0], $unfav_days)) {
                                    $unfavday_star = 1;
                                }
                                if (in_array($current_date[2], $unfav_months)) {
                                    $unfavmonth_star = 1;
                                }
                            } else {
                                $unfavdate_star = 0;
                            }

                            $fav_cosmic_stars = $date_star + $day_star + $month_star;
                            $unfav_cosmic_stars = $unfavdate_star + $unfavday_star + $unfavmonth_star;

                            $favdata['module_type'] = "Fav Star";
                            $favdata['year'] = $y;
                            $favdata['month'] = $m;
                            $favdata['date'] = $i;
                            $favdata['datestar'] = $fav_cosmic_stars;
                            array_push($favlist, $favdata);

                            // $favdatekey[$i] =  $fav_cosmic_stars;
                            $unfavdata['module_type'] = "Unfav Star";
                            $unfavdata['year'] = $y;
                            $unfavdata['month'] = $m;
                            $unfavdata['date'] = $i;
                            $unfavdata['datestar'] = $unfav_cosmic_stars;
                            array_push($unfavlist, $unfavdata);
                            //$unfavdatekey[$i] =  $unfav_cosmic_stars;
                        }
                        $favarraydata = $favlist;
                        $unfavarraydata = $unfavlist;
                    }
                }

                $cosmiccalender = array("favstars" => $favarraydata, "unfavstars" => $unfavarraydata);

                return response()
                    ->json([
                        'status' => 1,
                        'message' => $message,
                        'subscription_status' => $loginuser->subscription_status,
                        'userdetail' => array('userid' => $otheruserid, 'fullname' => $user->name, 'dob' => $user->dob),
                        'Module_types' => $dobreadingdetail,
                        'primary_detail' => $primarynumber,
                        'compatible_partner' => $compatible_partner,
                        'luckyparameters' => $luckyparameters,
                        'planet_detail' => $planetnumber,
                        'zodiac_detail' => $zodiacsign,
                        'life_cycles' => $lifecycle,
                        'name_reading' => $namereadingdetail,
                        'magic_box' => $magicboxdetail,
                        'magicboxdesclist' => $magicboxdesclist,
                        'cosmic_calender' => $cosmiccalender,
                        'favunfav_parameters' => $favunfavparameters,
                        'lifechanges' => $lifechanges,

                    ]);
            } else {
                return response()->json(['status' => 0, 'message' => 'No data found']);
            }
        }
    }

    public function otheruserreading(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'name' => 'required',
            'dob' => 'required',
            'gender' => 'required',
            'check_date' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        //return $request->all();
        $user_id = $request->user_id;
        $userdetail = User::find($user_id);
        $firstDateOfWeek = date("Y-m-d", strtotime('monday this week'));
        $lastdateOfWeek = date("Y-m-d", strtotime('sunday this week'));

        $otherUserReadings = User_namereading::where('user_id', '=', $user_id)
            ->whereBetween('created_at', [$firstDateOfWeek . " 00:00:00", $lastdateOfWeek . " 23:59:59"])->get();
        // return $otherUserReadings;

        if (count($otherUserReadings) == 0 && $userdetail->subscription_status == 0) {
            $message = "You have only Two compatibility check left in this week.";
        } elseif (count($otherUserReadings) == 0 && $userdetail->subscription_status == 2) {
            $message = "You have only Two compatibility check left in this week.";
        }elseif (count($otherUserReadings) == 1 && $userdetail->subscription_status == 0) {
            $message = "You have only One compatibility check left in this week.";
        } elseif (count($otherUserReadings) == 1 && $userdetail->subscription_status == 2) {
            $message = "You have only One compatibility check left in this week.";
        }elseif (count($otherUserReadings) == 2 && $userdetail->subscription_status == 0) {
            $message = "You don't have any compatibility check left in this week. Please wait for next week Or Upgrade and Subscribe to Unlock Premium Features!";
        } elseif (count($otherUserReadings) == 2 && $userdetail->subscription_status == 2) {
            $message = "You don't have any compatibility checks left this week. Please wait for next week Or Your monthly plan has been paused. Please resume and unlock premium features!";
        } else {
            $message = "Sucess";
        }
        if (count($otherUserReadings) >= 3 && $userdetail->subscription_status == 0) {
            return response()->json([
                'status' => 0,
                'message' => "You don't have any compatibility check left in this week. Please wait for next week Or Upgrade and Subscribe to Unlock Premium Features!",
                'subscription_status' => $userdetail->subscription_status,
            ]);
        }elseif (count($otherUserReadings) >= 3 && $userdetail->subscription_status == 2) {
            return response()->json([
                'status' => 0,
                'message' => "You don't have any compatibility checks left this week. Please wait for next week Or Your monthly plan has been paused. Please resume and unlock premium features!",
                'subscription_status' => $userdetail->subscription_status,
            ]);
        } else {
            $name = $request->name;
            $gender = $request->gender;
            $dob = $request->dob;
            $check_date = $request->check_date;
            // $name = explode(' ', $name);
            $finalname = str_replace(' ', '', $name);
            //print_r($name);
            //die();
            $strname = strtoupper($finalname);
            $splitname = str_split($strname, 1);
            foreach ($splitname as $letter) {
                $pytha_number[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $letter . '%')
                    ->where('systemtype_id', 1)
                    ->value('number');
                $chald_number[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $letter . '%')
                    ->where('systemtype_id', 2)
                    ->value('number');
            }
            $pytha_no_sum = array_sum($pytha_number);
            $chald_no_sum = array_sum($chald_number);

            while (strlen($pytha_no_sum) != 1 || strlen($chald_no_sum) != 1) {
                $pytha_no_sum = str_split($pytha_no_sum, 1);
                $pytha_no_sum = array_sum($pytha_no_sum);
                $chald_no_sum = str_split($chald_no_sum, 1);
                $chald_no_sum = array_sum($chald_no_sum);
            }
            $pytha_description = Module_description::where('moduletype_id', 1)
                ->where('number', $pytha_no_sum)
                ->value('description');
            $pytha_description = strip_tags($pytha_description);
            $chald_description = Module_description::where('moduletype_id', 1)
                ->where('number', $chald_no_sum)
                ->value('description');
            $explodenamereading_desc = explode('||', $chald_description);
            $positive_desc = $explodenamereading_desc[0];
            $negative_desc = $explodenamereading_desc[1];

            $description = array("positive_title" => "Positive", "positive_desc" => $positive_desc, "negative_title" => "Negative", "negative_desc" => $negative_desc);
            $number = $chald_no_sum;

            $date = explode('-', $dob);
            $day = str_split($date[0], 1);
            $birthno = array_sum($day);
            $birthno = intval($birthno);
            while (strlen($birthno) != 1) {
                $birthno = str_split($birthno);
                $birthno = array_sum($birthno);
            }
            $month = str_split($date[1], 1);
            $monthno = array_sum($month);
            while (strlen($monthno) != 1) {
                $monthno = str_split($monthno);
                $monthno = array_sum($monthno);
            }
            $year = str_split($date[2], 1);
            $yearno = array_sum($year);
            while (strlen($yearno) != 1) {
                $yearno = str_split($yearno);
                $yearno = array_sum($yearno);
            }
            $yearno = intval($yearno);
            $destiny_no = $birthno + $monthno + $yearno;
            while (strlen($destiny_no) != 1) {
                $destiny_no = str_split($destiny_no);
                $destiny_no = array_sum($destiny_no);
            }

            $dobdate = date("d-F-Y", strtotime($dob));
            $date = explode('-', $dobdate);
            $day = $date[0];
            $month = $date[1];
            $zodic = Zodic_sign::where('title', 'LIKE', '%' . $month . '%')->get();

            if ($month == "March") {
                $titledaydate = $zodic[1]->title;
                $explodetitle_daydate = explode(' ', $titledaydate);
                $title_daydate = $explodetitle_daydate[2];

                if ($day <= $title_daydate) {
                    $zodicdata = $zodic[1];
                } else {
                    $zodicdata = $zodic[0];
                }
            } else {
                $titledaydate = $zodic[0]->title;
                $explodetitle_daydate = explode(' ', $titledaydate);
                $title_daydate = $explodetitle_daydate[2];

                if ($day <= $title_daydate) {
                    $zodicdata = $zodic[0];
                } else {
                    $zodicdata = $zodic[1];
                }
            }


            $otheruserprofile = new User_namereading();
            $otheruserprofile->user_id = $user_id;
            $otheruserprofile->name = $request->name;
            $otheruserprofile->dob = $dob;
            $otheruserprofile->gender = $gender;
            $otheruserprofile->check_date = $check_date;
            $otheruserprofile->save();

            $detail = array('user_id' => $user_id, 'otheruserid' => $otheruserprofile->id, 'fullname' => $request->name, 'check_date' => $check_date, 'namenumber' => $number, 'namedescription' => $description, 'zodiacsign' => $zodicdata->zodic_sign, 'destinyno' => $destiny_no);

            return response()->json([
                'status' => 1,
                'message' => $message,
                'subscription_status' => $userdetail->subscription_status,
                'detail' => $detail,
            ]);
        }
    }

    // Bu Kulbir Sir Logic
    // one to other compatibility check
    public function onetoothercompatibilitycheck(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'userid' => 'required',
            'type' => 'required',
            'name' => 'required',
            'dob' => 'required',
            'gender' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }


        $userid = $request->userid;
        $otheruser_name = $request->name;
        $gender = $request->gender;
        $otheruser_dob = $request->dob;
        $email = $request->email;
        $type = $request->type;
        if ($type == 2 || $type == 6) {
            if ($type == 2) {
                $typename = "one to other";
            } else {
                $typename = "Spouse";
            }

            $loginuser = User::find($userid);
            $firstDateOfWeek = date("Y-m-d", strtotime('monday this week'));
            $lastdateOfWeek = date("Y-m-d", strtotime('sunday this week'));

            $spouseCompChecks = User_compatiblecheck::where('user_id', '=', $userid)->where('type', '=', 6)
                ->whereBetween('created_at', [$firstDateOfWeek . " 00:00:00", $lastdateOfWeek . " 23:59:59"])->get();

                if (count($spouseCompChecks) == 0 && $loginuser->subscription_status == 0) {
                    $message = "You have only Two compatibility check left in this week.";
                } elseif (count($spouseCompChecks) == 0 && $loginuser->subscription_status == 2) {
                    $message = "You have only Two compatibility check left in this week.";
                }elseif (count($spouseCompChecks) == 1 && $loginuser->subscription_status == 0) {
                    $message = "You have only One compatibility check left in this week.";
                } elseif (count($spouseCompChecks) == 1 && $loginuser->subscription_status == 2) {
                    $message = "You have only One compatibility check left in this week.";
                }elseif (count($spouseCompChecks) == 2 && $loginuser->subscription_status == 0) {
                    $message = "You don't have any compatibility check left in this week. Please wait for next week Or Upgrade and Subscribe to Unlock Premium Features!";
                } elseif (count($spouseCompChecks) == 2 && $loginuser->subscription_status == 2) {
                    $message = "You don't have any compatibility checks left this week. Please wait for next week Or Your monthly plan has been paused. Please resume and unlock premium features!";
                } else {
                    $message = "Sucess";
                }
                if (count($spouseCompChecks) >= 3 && $loginuser->subscription_status == 0) {
                    return response()->json([
                        'status' => 0,
                        'message' => "You don't have any compatibility check left in this week. Please wait for next week Or Upgrade and Subscribe to Unlock Premium Features!",
                        'subscription_status' => $loginuser->subscription_status,
                    ]);
                }elseif (count($spouseCompChecks) >= 3 && $loginuser->subscription_status == 2) {
                    return response()->json([
                        'status' => 0,
                        'message' => "You don't have any compatibility checks left this week. Please wait for next week Or Your monthly plan has been paused. Please resume and unlock premium features!",
                        'subscription_status' => $loginuser->subscription_status,
                    ]);
                } else {

                $compatibility = User_compatiblecheck::create([

                    'user_id' => $userid,
                    'type' => $type,
                    'type_name' => $typename,
                    'name' => $otheruser_name,
                    'gender' => $gender,
                    'email' => $email,
                    'type_dates' => 3,
                    'dates' => $otheruser_dob,
                ]);
                if ($compatibility) {
                    $person_id = $compatibility->id;

                    $loginuser_dob = $loginuser->dob;
                    $loginuserdob = explode("-", $loginuser_dob);

                    // DOB reading 
                    $date = explode('-', $otheruser_dob);
                    $day = $date[2];
                    $month = $date[1];
                    $year = $date[0];
                    $dayno = str_split($day, 1);
                    $dayno = array_sum($dayno);
                    $dayno = intval($dayno);
                    while (strlen($dayno) != 1) {
                        $dayno = str_split($dayno);
                        $dayno = array_sum($dayno);
                    }
                    $dobdesc = Module_description::where('moduletype_id', 2)
                        ->where('number', $dayno)
                        ->value('description');
                    $dobdesc = strip_tags($dobdesc);

                    $birthdayreading = array("module_type" => 2, "module_name" => "DOB Reading", "number" => $dayno, "description" => $dobdesc);

                    //elemental number
                    $elementaldesc = Module_description::where('moduletype_id', 4)
                        ->where('number', $dayno)
                        ->value('description');
                    $elementaldesc = strip_tags($elementaldesc);

                    $elementalno = array("module_type" => 4, "module_name" => "Elemental Number", "number" => $dayno, "description" => $elementaldesc);

                    //basic health reading
                    $basichealthdesc = Module_description::where('moduletype_id', 5)
                        ->where('number', $dayno)
                        ->value('description');
                    $basichealthdesc = strip_tags($basichealthdesc);

                    $basichealthreading = array("module_type" => 5, "module_name" => "Basic Health Reading", "number" => $dayno, "description" => $basichealthdesc);

                    //health precaution
                    $precautiondesc = Module_description::where('moduletype_id', 6)
                        ->where('number', $dayno)
                        ->value('description');
                    $precautiondesc = strip_tags($precautiondesc);

                    $healthprecaution = array("module_type" => 6, "module_name" => "Health Precaution", "number" => $dayno, "description" => $precautiondesc);

                    //basic Parenting
                    $basicparentingdesc = Module_description::where('moduletype_id', 12)
                        ->where('number', $dayno)
                        ->value('description');
                    $basicparentingdesc = strip_tags($basicparentingdesc);

                    $basicparenting = array(
                        "module_type" => 12, "module_name" => "Basic Parent Reading",
                        "number" => $dayno, "description" => $basicparentingdesc
                    );

                    //detail Parenting  
                    $detailparentingdesc = Module_description::where('moduletype_id', 13)
                        ->where('number', $dayno)
                        ->value('description');
                    $detailparentingdesc = strip_tags($detailparentingdesc);

                    $detailparenting = array(
                        "module_type" => 13, "module_name" => "Detailed Parent Reading",
                        "number" => $dayno, "description" => $detailparentingdesc
                    );

                    //basic money 
                    $basicmoneydesc = Module_description::where('moduletype_id', 14)
                        ->where('number', $dayno)
                        ->value('description');
                    $basicmoneydesc = strip_tags($basicmoneydesc);

                    $basicmoneymatter = array(
                        "module_type" => 14, "module_name" => "Basic Money Matters",
                        "number" => $dayno, "description" => $basicmoneydesc
                    );

                    //detail money 
                    $detailmoneydesc = Module_description::where('moduletype_id', 15)
                        ->where('number', $dayno)
                        ->value('description');
                    $detailmoneydesc = strip_tags($detailmoneydesc);

                    $detailmoneymatter = array(
                        "module_type" => 15, "module_name" => "Detailed Money Matters",
                        "number" => $dayno, "description" => $detailmoneydesc
                    );

                    //destiny number
                    $monthno = str_split($month, 1);
                    $monthno = array_sum($monthno);
                    while (strlen($monthno) != 1) {
                        $monthno = str_split($monthno);
                        $monthno = array_sum($monthno);
                    }
                    $yearno = str_split($year, 1);
                    $yearno = array_sum($yearno);
                    while (strlen($yearno) != 1) {
                        $yearno = str_split($yearno);
                        $yearno = array_sum($yearno);
                    }
                    $yearno = intval($yearno);
                    $destiny_no = $dayno + $monthno + $yearno;
                    while (strlen($destiny_no) != 1) {
                        $destiny_no = str_split($destiny_no);
                        $destiny_no = array_sum($destiny_no);
                    }
                    $destinynodesc = Module_description::where('moduletype_id', 16)
                        ->where('number', $destiny_no)
                        ->value('description');
                    $destinynodesc = strip_tags($destinynodesc);

                    $explode_destinynodesc = explode('||', $destinynodesc);
                    $learn_desc = $explode_destinynodesc[0];
                    $notlearn_desc = $explode_destinynodesc[1];
                    $destinynumber = array(
                        "module_type" => 16, "module_name" => "Destiny Number", "number" => $destiny_no, "description" => $destinynodesc,
                        "learn_title" => "Learn To Be", "learn_desc" => $learn_desc, "notlearn_title" => "Learn Not To Be", "notlearn_desc" => $notlearn_desc
                    );

                    $dobreadingdetail = [
                        $birthdayreading, $elementalno, $basichealthreading, $healthprecaution, $basicmoneymatter, $detailmoneymatter,
                        $basicparenting, $detailparenting, $destinynumber
                    ];

                    //primary number
                    $primarynodesc = Primaryno_type::where('number', $dayno)
                        ->first();
                    $primarynumber = array(
                        "module_name" => "Primary Number", "number" => $dayno, "description" => $primarynodesc->description, "positive" => $primarynodesc->positive, "negative" => $primarynodesc->negative,
                        "occupations" => $primarynodesc->occupations, "health" => $primarynodesc->health, "partners" => $primarynodesc->partners, "times_of_the_year" => $primarynodesc->times_of_the_year,
                        "countries" => $primarynodesc->countries, "tibbits" => $primarynodesc->tibbits, "destiny_colors" => $primarynodesc->destiny_colors, "destiny_timing" => $primarynodesc->destiny_timing
                    );

                    //compatible partner
                    $compatiblepartner_data = Compatible_partner::where('number', $dayno)
                        ->first();

                    $compatible_partner = array(
                        "module_name" => "Compatible Partner", "number" => $dayno,
                        "description" => strip_tags($compatiblepartner_data->description),
                        "more_compatible_months" => $compatiblepartner_data->more_compatible_months,
                        "more_compatible_dates" => $compatiblepartner_data->more_compatible_dates,
                        "less_compatible_months" => $compatiblepartner_data->less_compatible_months,
                        "less_compatible_dates" => $compatiblepartner_data->less_compatible_dates
                    );

                    //luckiest parameters
                    $luckyparameterdesc = Luckiest_parameter::select('lucky_colours', 'lucky_gems', 'lucky_metals')
                        ->where('number', $dayno)
                        ->first();

                    $luckyparameters = array(
                        "module_name" => "Lucky Parameters", "number" => $dayno,
                        "lucky_colours" => $luckyparameterdesc->lucky_colours,
                        "lucky_gems" => $luckyparameterdesc->lucky_gems,
                        "lucky_metals" => $luckyparameterdesc->lucky_metals
                    );

                    //planet number
                    $planet = Planet_number::select('name', 'ruling_number', 'description')
                        ->where('ruling_number', $dayno)
                        ->first();

                    $planetnumber = array(
                        "module_name" => "Planet Number", "ruling_number" => $dayno,
                        "planet_name" => $planet->name,
                        "description" => $planet->description
                    );

                    //zodiac sign
                    $formetdob = date("d-F-Y", strtotime($otheruser_dob));
                    $zodiacdate = explode('-', $formetdob);
                    $dobday = $zodiacdate[0];
                    $dobmonth = $zodiacdate[1];
                    $zodiac = Zodic_sign::where('title', 'LIKE', '%' . $dobmonth . '%')
                        ->get();

                    if ($dobmonth == "March") {
                        $titledaydate = $zodiac[1]->title;
                        $explodetitle_daydate = explode(' ', $titledaydate);
                        $title_daydate = $explodetitle_daydate[2];

                        if ($dobday <= $title_daydate) {
                            $zodiacdata = $zodiac[1];
                        } else {
                            $zodiacdata = $zodiac[0];
                        }
                    } else {
                        $titledaydate = $zodiac[0]->title;
                        $explodetitle_daydate = explode(' ', $titledaydate);
                        $title_daydate = $explodetitle_daydate[2];

                        if ($dobday <= $title_daydate) {
                            $zodiacdata = $zodiac[0];
                        } else {
                            $zodiacdata = $zodiac[1];
                        }
                    }

                    $zodiacsign = array(
                        "module_name" => "Zodiac Sign", "zodiac_sign" => $zodiacdata->zodic_sign,
                        "zodiac_number" => $zodiacdata->zodic_number,
                        "zodiac_day" => $zodiacdata->zodic_day
                    );

                    //life cycle
                    $monthdescription = Life_cycle::where('number', $dayno)->value('cycle_by_month');
                    $daydescription = Life_cycle::where('number', $monthno)->value('cycle_by_date');
                    $yeardescription = Life_cycle::where('number', $yearno)->value('cycle_by_year');

                    $lifecycle = array(
                        "module_name" => "Life Cycle", "cycleone_number" => $monthno,
                        "cycleone_description" => $monthdescription,
                        "cycletwo_number" => $dayno,
                        "cycletwo_description" => $daydescription,
                        "cyclethree_number" => $yearno,
                        "cyclethree_description" => $yeardescription
                    );

                    //Name reading
                    $name = $otheruser_name;
                    $finalname = str_replace(' ', '', $name);
                    $strname = strtoupper($finalname);
                    $splitname = str_split($strname, 1);
                    foreach ($splitname as $letter) {
                        $pytha_number[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $letter . '%')
                            ->where('systemtype_id', 1)
                            ->value('number');
                        $chald_number[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $letter . '%')
                            ->where('systemtype_id', 2)
                            ->value('number');
                    }
                    $pytha_no_sum = array_sum($pytha_number);
                    $chald_no_sum = array_sum($chald_number);

                    while (strlen($pytha_no_sum) != 1 || strlen($chald_no_sum) != 1) {
                        $pytha_no_sum = str_split($pytha_no_sum, 1);
                        $pytha_no_sum = array_sum($pytha_no_sum);
                        $chald_no_sum = str_split($chald_no_sum, 1);
                        $chald_no_sum = array_sum($chald_no_sum);
                    }
                    $pytha_description = Module_description::where('moduletype_id', 1)
                        ->where('number', $pytha_no_sum)
                        ->value('description');
                    $pytha_description = strip_tags($pytha_description);
                    $chald_description = Module_description::where('moduletype_id', 1)
                        ->where('number', $chald_no_sum)
                        ->value('description');
                    $chalddescription = strip_tags($chald_description);
                    $explodenamereading_desc = explode('||', $chalddescription);
                    $positive_desc = $explodenamereading_desc[0];
                    $negative_desc = $explodenamereading_desc[1];
                    $namereadingdetail = array(
                        "module_name" => "Name Reading", "Pytha_number" => $pytha_no_sum, "Pytha_description" => $pytha_description, "Chald_number" => $chald_no_sum, "Chald_description" => $chald_description,
                        "Chald_positive_title" => "Positive", "Chald_positive_desc" => $positive_desc, "Chald_negative_title" => "Negative", "Chald_negative_desc" => $negative_desc
                    );

                    /*  if($chald_no_sum != 9)
                    {
                        $namereadingdetail = array("module_name"=>"Name Reading", "Pytha_number"=> $pytha_no_sum, "Pytha_description"=> $pytha_description,
                        "Chald_number"=> $chald_no_sum, "Chald_description"=> $chald_description);
                    }else
                    {
                        $namereadingdetail = array("module_name"=>"Name Reading", "Pytha_number"=> $pytha_no_sum, "Pytha_description"=> $pytha_description);
                    } */

                    $othernamereadingno = $chald_no_sum;
                    //magic box
                    $pytha_number_count = array_count_values($pytha_number);
                    $chald_number_count = array_count_values($chald_number);

                    $magicbox = array("module_name" => "Magic Box", "pythagorean" => $pytha_number_count, "chaldean" => $chald_number_count);


                    //fav unfav parameters
                    $fav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 1)
                        ->where('month_id', $month)
                        ->where('date', $day)
                        ->first();
                    $unfav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 2)
                        ->where('month_id', $month)
                        ->where('date', $day)
                        ->first();
                    $fav_number = str_replace(' ', '', $fav->numbers);
                    $unfav_number = str_replace(' ', '', $unfav->numbers);

                    $fav_day = str_replace(',', ', ', $fav->days);
                    $fav_month = str_replace(',', ', ', $fav->months);
                    $unfav_day = str_replace(',', ', ', $unfav->days);
                    $unfav_month = str_replace(',', ', ', $unfav->months);
                    $favunfavparameters = array("favparameter" => $fav, "unfavparameters" => $unfav, 'fav_numbers_withoutSpace'=>$fav_number, 'unfav_numbers_withoutSpace'=>$unfav_number, 'fav_days_withSpace' => $fav_day,
                    'fav_months_withSpace' => $fav_month,'unfav_days_withSpace'=>$unfav_day, 'unfav_months_withSpace'=>$unfav_month);

                    //Life changes

                    $ages = Life_change::where('numbers', $dayno)->value('ages');
                    $start_year = intval($year);
                    $year_limit = $start_year + 100;
                    $years = array();
                    if ($dayno == 1) {
                        $year_sequ = array(1, 4);
                        for ($i = $start_year; $i <= $year_limit; $i++) {
                            $cal_year = str_split($i, 1);
                            $sum = array_sum($cal_year);
                            while (strlen($sum) != 1) {
                                $sum = str_split($sum);
                                $sum = array_sum($sum);
                            }
                            $sum = intval($sum);
                            if (in_array($sum, $year_sequ)) {
                                array_push($years, $i);
                            }
                        }
                    }
                    if ($dayno == 2) {
                        $year_sequ = array(2, 7);
                        for ($i = $start_year; $i <= $year_limit; $i++) {
                            $cal_year = str_split($i, 1);
                            $sum = array_sum($cal_year);
                            while (strlen($sum) != 1) {
                                $sum = str_split($sum);
                                $sum = array_sum($sum);
                            }

                            $sum = intval($sum);
                            if (in_array($sum, $year_sequ)) {
                                array_push($years, $i);
                            }
                        }
                    }
                    if ($dayno == 3) {
                        $year_sequ = array(3, 9);
                        for ($i = $start_year; $i <= $year_limit; $i++) {
                            $cal_year = str_split($i, 1);
                            $sum = array_sum($cal_year);
                            while (strlen($sum) != 1) {
                                $sum = str_split($sum);
                                $sum = array_sum($sum);
                            }
                            $sum = intval($sum);
                            if (in_array($sum, $year_sequ)) {
                                array_push($years, $i);
                            }
                        }
                    }
                    if ($dayno == 4) {
                        $year_sequ = array(4, 8, 1);
                        for ($i = $start_year; $i <= $year_limit; $i++) {
                            $cal_year = str_split($i, 1);
                            $sum = array_sum($cal_year);
                            while (strlen($sum) != 1) {
                                $sum = str_split($sum);
                                $sum = array_sum($sum);
                            }

                            $sum = intval($sum);
                            if (in_array($sum, $year_sequ)) {
                                array_push($years, $i);
                            }
                        }
                    }
                    if ($dayno == 5) {
                        $year_sequ = array(5, 6);
                        for ($i = $start_year; $i <= $year_limit; $i++) {
                            $cal_year = str_split($i, 1);
                            $sum = array_sum($cal_year);
                            while (strlen($sum) != 1) {
                                $sum = str_split($sum);
                                $sum = array_sum($sum);
                            }

                            $sum = intval($sum);
                            if (in_array($sum, $year_sequ)) {
                                array_push($years, $i);
                            }
                        }
                    }
                    if ($dayno == 6) {
                        $year_sequ = array(6, 2, 3);
                        for ($i = $start_year; $i <= $year_limit; $i++) {
                            $cal_year = str_split($i, 1);
                            $sum = array_sum($cal_year);
                            while (strlen($sum) != 1) {
                                $sum = str_split($sum);
                                $sum = array_sum($sum);
                            }

                            $sum = intval($sum);
                            if (in_array($sum, $year_sequ)) {
                                array_push($years, $i);
                            }
                        }
                    }
                    if ($dayno == 7) {
                        $year_sequ = array(2, 7);
                        for ($i = $start_year; $i <= $year_limit; $i++) {
                            $cal_year = str_split($i, 1);
                            $sum = array_sum($cal_year);
                            while (strlen($sum) != 1) {
                                $sum = str_split($sum);
                                $sum = array_sum($sum);
                            }
                            $sum = intval($sum);
                            if (in_array($sum, $year_sequ)) {
                                array_push($years, $i);
                            }
                        }
                    }
                    if ($dayno == 8) {
                        $year_sequ = array(4, 6, 8);
                        for ($i = $start_year; $i <= $year_limit; $i++) {
                            $cal_year = str_split($i, 1);
                            $sum = array_sum($cal_year);
                            while (strlen($sum) != 1) {
                                $sum = str_split($sum);
                                $sum = array_sum($sum);
                            }
                            $sum = intval($sum);
                            if (in_array($sum, $year_sequ)) {
                                array_push($years, $i);
                            }
                        }
                    }
                    if ($dayno == 9) {
                        $year_sequ = array(3, 9, 1);
                        for ($i = $start_year; $i <= $year_limit; $i++) {
                            $cal_year = str_split($i, 1);
                            $sum = array_sum($cal_year);
                            while (strlen($sum) != 1) {
                                $sum = str_split($sum);
                                $sum = array_sum($sum);
                            }
                            $sum = intval($sum);
                            if (in_array($sum, $year_sequ)) {
                                array_push($years, $i);
                            }
                        }
                    }
                    $years = implode(",", $years);

                    $lifechanges = array(
                        "module_name" => "Life Changes", "number" => $dayno, "ages" => $ages,
                        "years" => $years
                    );

                    //partner relationship
                    $uaerdobday = str_split($loginuserdob[2], 1);
                    $user_number = array_sum($uaerdobday);
                    while (strlen($user_number) != 1) {
                        $user_number = str_split($user_number);
                        $user_number = array_sum($user_number);
                    }
                    $persion_day = str_split($date[2], 1);
                    $persion_number = array_sum($persion_day);
                    $persion_number = intval($persion_number);
                    while (strlen($persion_number) != 1) {
                        $persion_number = str_split($persion_number);
                        $persion_number = array_sum($persion_number);
                    }

                    $relation = Partner_relationship::select('description')->where('number', $user_number)
                        ->where('mate_number', $persion_number)
                        ->first();


                    // login user detail

                    $loginuser_name = $loginuser->name;
                    $finalname = str_replace(' ', '', $loginuser_name);
                    $loginuserstrname = strtoupper($finalname);
                    $loginusersplitname = str_split($loginuserstrname, 1);
                    foreach ($loginusersplitname as $nameletter) {
                        $pytha_no[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                            ->where('systemtype_id', 1)
                            ->value('number');
                        $chald_no[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                            ->where('systemtype_id', 2)
                            ->value('number');
                    }
                    $loginuserpythano_sum = array_sum($pytha_no);
                    $loginuserchaldno_sum = array_sum($chald_no);

                    while (strlen($loginuserpythano_sum) != 1 || strlen($loginuserchaldno_sum) != 1) {
                        $loginuserpythano_sum = str_split($loginuserpythano_sum, 1);
                        $loginuserpythano_sum = array_sum($loginuserpythano_sum);
                        $loginuserchaldno_sum = str_split($loginuserchaldno_sum, 1);
                        $loginuserchaldno_sum = array_sum($loginuserchaldno_sum);
                    }
                    $loginuserpytha_description = Module_description::where('moduletype_id', 1)
                        ->where('number', $loginuserpythano_sum)
                        ->value('description');
                    $loginuserpytha_description = strip_tags($loginuserpytha_description);
                    $loginuserchald_description = Module_description::where('moduletype_id', 1)
                        ->where('number', $loginuserchaldno_sum)
                        ->value('description');
                    $loginuserchald_description = strip_tags($loginuserchald_description);
                    $explodeloginusername_desc = explode('||', $loginuserchald_description);
                    $loginuserpositive_desc = $explodeloginusername_desc[0];
                    $loginusernegative_desc = $explodeloginusername_desc[1];
                    $loginusernamereadingdetail = array("Chald_positive_title" => "Positive", "Chald_positive_desc" => $loginuserpositive_desc, "Chald_negative_title" => "Negative", "Chald_negative_desc" => $loginusernegative_desc);

                    /*  if($loginuserchaldno_sum != 9)
                    {
                        $loginusernamedesc = $loginuserchald_description;
                    }else
                    {
                        $loginusernamedesc = $loginuserpytha_description;
                    } */
                    $loginusernamedesc = $loginusernamereadingdetail;
                    $loginusernamereadingno = $loginuserchaldno_sum;


                    //primary number    
                    $loginuserday = str_split($loginuserdob[2], 1);
                    $loginuserbirthno = array_sum($loginuserday);
                    $loginuserbirthno = intval($loginuserbirthno);
                    while (strlen($loginuserbirthno) != 1) {
                        $loginuserbirthno = str_split($loginuserbirthno);
                        $loginuserbirthno = array_sum($loginuserbirthno);
                    }

                    //planet number
                    $loginuserplanet = Planet_number::where('ruling_number', $loginuserbirthno)->first();

                    //Lucky parameters
                    $loginuserluckyparameters = Luckiest_parameter::where('number', $loginuserbirthno)->first();

                    //zodiac sign
                    $loginuserdobdate = date("d-F-Y", strtotime($loginuser_dob));
                    $loginuserdobdate = explode('-', $loginuserdobdate);
                    $loginuserdobday = $loginuserdobdate[0];
                    $loginuserdobmonth = $loginuserdobdate[1];
                    $zodic = Zodic_sign::where('title', 'LIKE', '%' . $loginuserdobmonth . '%')
                        ->get();

                    if ($loginuserdobmonth == "March") {
                        $titledaydate = $zodic[1]->title;
                        $explodetitle_daydate = explode(' ', $titledaydate);
                        $title_daydate = $explodetitle_daydate[2];

                        if ($loginuserdobday <= $title_daydate) {
                            $loginuserzodicdata = $zodic[1];
                        } else {
                            $loginuserzodicdata = $zodic[0];
                        }
                    } else {
                        $titledaydate = $zodic[0]->title;
                        $explodetitle_daydate = explode(' ', $titledaydate);
                        $title_daydate = $explodetitle_daydate[2];

                        if ($loginuserdobday <= $title_daydate) {
                            $loginuserzodicdata = $zodic[0];
                        } else {
                            $loginuserzodicdata = $zodic[1];
                        }
                    }
                    //fav and unfav months
                    $loginuserfav = Fav_unfav_parameter::where('type', 1)
                        ->where('month_id', $loginuserdob[1])
                        ->where('date', $loginuserdob[2])
                        ->first();
                    $loginuserunfav = Fav_unfav_parameter::where('type', 2)
                        ->where('month_id', $loginuserdob[1])
                        ->where('date', $loginuserdob[2])
                        ->first();

                    $loginUserfav_day = str_replace(',', ', ', $fav->days);
                    $loginUserfav_month = str_replace(',', ', ', $fav->months);
                    $loginUserunfav_day = str_replace(',', ', ', $unfav->days);
                    $loginUserunfav_month = str_replace(',', ', ', $unfav->months);
                    
                    //Element name
                    $loginuserelementdescription = Module_description::where('systemtype_id', 1)
                        ->where('moduletype_id', 4)
                        ->where('number', $loginuserbirthno)
                        ->value('description');
                    $loginuserelement = explode(" ", $loginuserelementdescription);
                    $loginuserelement = strip_tags($loginuserelement[0]);

                    //relation percentage 

                    $otherperson_dob_formate = date("Y-F-d", strtotime($otheruser_dob));
                    $explode_dob_formate = explode('-', $otherperson_dob_formate);
                    $dob_month_name = $explode_dob_formate[1];
                    $loginuser_compatiblepartner_data = Compatible_partner::where('number', $loginuserbirthno)->first();
                    
                    $loginuser_compatible_dates = explode(',', $loginuser_compatiblepartner_data->more_compatible_dates);
                    $loginuser_compatible_months = explode(',', $loginuser_compatiblepartner_data->more_compatible_months);
                    $loginuser_uncompatible_dates = explode(',', $loginuser_compatiblepartner_data->less_compatible_dates);
                    $loginuser_uncompatible_months = explode(',', $loginuser_compatiblepartner_data->less_compatible_months);
                    
                    $compatible_date_array = in_array($dayno, $loginuser_compatible_dates);
                    $compatible_month_array = in_array($dob_month_name, $loginuser_compatible_months);
                    $uncompatible_date_array = in_array($dayno, $loginuser_uncompatible_dates);
                    $uncompatible_month_array = in_array($dob_month_name, $loginuser_uncompatible_months);
                    
                    if($compatible_date_array > 0 && $compatible_month_array > 0){
                        $final_percentage = 98;
                    }elseif($compatible_date_array > 0 && $compatible_month_array == 0){
                        $final_percentage = 89;
                    }elseif($compatible_date_array == 0 && $compatible_month_array > 0){
                        $final_percentage = 79;
                    }elseif($compatible_date_array > 0 && $uncompatible_month_array > 0){
                        $final_percentage = 69;
                    }elseif($compatible_month_array > 0 && $uncompatible_date_array > 0){
                        $final_percentage = 59;
                    }elseif($compatible_date_array == 0 && $compatible_month_array == 0 && $uncompatible_date_array == 0 && $uncompatible_month_array == 0){
                        $final_percentage = 50;
                    }elseif($uncompatible_month_array > 0 && $uncompatible_date_array == 0){
                        $final_percentage = 49;
                    }elseif($uncompatible_month_array == 0 && $uncompatible_date_array > 0){
                        $final_percentage = 39;
                    }elseif($uncompatible_month_array > 0 && $uncompatible_date_array > 0){
                        $final_percentage = 29;
                    }else{
                        $final_percentage = 50;
                    }
                    
                    $relation_dobpercentage = Compatibility_percentage::where('number', $user_number)->where('mate_number', $persion_number)->first();
                    $dob_percentage = $relation_dobpercentage->compatibility_percentage;
                    $remaining_percentage = 100 - $dob_percentage;

                    $relation_namepercentage = Compatibility_percentage::where('number', $loginusernamereadingno)->where('mate_number', $othernamereadingno)->first();
                    $love_scale = "On the scale of love, this one rates " . $relation_dobpercentage->strength . " " . $relation_dobpercentage->compatibility_number;
                    $name_percentage = $relation_namepercentage->compatibility_percentage;

                    $calculated_name_percentage = ($remaining_percentage / 100) * $name_percentage;
                    // $final_percentage = $dob_percentage + $calculated_name_percentage;

                    $loginuserdetail = array(
                        'loginuserid' => $userid, 'loginusername' => $loginuser_name, 'loginuserdob' => $loginuser_dob, 'namedescription' => $loginusernamedesc, 'planet' => $loginuserplanet->name, 'zodiacsign' => $loginuserzodicdata->zodic_sign, 'primaryno' => $loginuserbirthno, 'element' => $loginuserelement, 'lucky_gems' => $loginuserluckyparameters->lucky_gems,
                        'fav_numbers' => $loginuserfav->numbers, 'unfav_numbers' => $loginuserunfav->numbers, 'fav_days' => $loginuserfav->days, 'unfav_days' => $loginuserunfav->days, 'fav_months' => $loginuserfav->months, 'unfav_months' => $loginuserunfav->months, 'fav_days_withSpace' => $loginUserfav_day,
                        'fav_months_withSpace' => $loginUserfav_month,'unfav_days_withSpace'=>$loginUserunfav_day, 'unfav_months_withSpace'=>$loginUserunfav_month );

                    $otheruserdetail = array(
                        'otherpersonid' => $person_id, 'otherpersonname' => $otheruser_name, 'otherpersondob' => $otheruser_dob, 'Module_types' => $dobreadingdetail,
                        'primary_detail' => $primarynumber, 'compatible_partner' => $compatible_partner, 'luckyparameters' => $luckyparameters, 'planet_detail' => $planetnumber, 'zodiac_detail' => $zodiacsign, 'life_cycles' => $lifecycle,
                        'name_reading' => $namereadingdetail, 'magic_box' => $magicbox, 'favunfav_parameters' => $favunfavparameters, 'lifechanges' => $lifechanges, 'relation_desc' => $relation->description, 'dobtodobpercentage' => $dob_percentage, 'nametonamepercentage' => $calculated_name_percentage, 'final_usercompatiblitypercentage' => $final_percentage, 'love_scale' => $love_scale
                    );

                    return response()->json([
                        'status' => 1,
                        'message' => $message,
                        'subscription_status' => $loginuser->subscription_status,
                        'otheruser_detail' => $otheruserdetail,
                        'loginuser_detail' => $loginuserdetail,
                    ]);
                } else {

                    return response()->json([
                        'status' => 0,
                        'message' => 'Something went wrong. Please try again',
                    ]);
                }
            }
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong. Please try again and also check the type parameter',
            ]);
        }
    }

    public function checkcarcompatibility(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $type = $request->type;
        // For Car 
        if ($type == 1) {
            $validator = Validator::make($request->all(), [
                'userid' => 'required',
                'type' => 'required',
                'type_name' => 'required',
                'brand_name' => 'required',
                'modal' => 'required',
                // 'registration_no'=> 'required',

            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }
            $userid = $request->userid;
            $type_name = $request->type_name;
            $brand_name = $request->brand_name;
            $modal = $request->modal;
            $car_name = $brand_name . " " . $modal;
            $registration_no = $request->registration_no;

            $compatibility = User_compatiblecheck::create([

                'user_id' => $userid,
                'type' => $type,
                'name' => $car_name,
                'type_name' => $type_name,
            ]);

            //case1 dob and registration_no reading

            //user dob reading
            $loginuser = User::find($userid);
            $dob = $loginuser->dob;
            $date = explode('-', $dob);
            $day = str_split($date[2], 1);
            $cal_dobno = array_sum($day);
            $cal_dobno = intval($cal_dobno);
            while (strlen($cal_dobno) != 1) {
                $cal_dobno = str_split($cal_dobno);
                $cal_dobno = array_sum($cal_dobno);
            }

            //car registration_no reading

            /* $numaric = str_split(filter_var($registration_no, FILTER_SANITIZE_NUMBER_INT));    
            $carnumber_sum = array_sum($numaric);
            while(strlen($carnumber_sum)!= 1)
            {
                $carnumber_sum = str_split($carnumber_sum);
                $carnumber_sum = array_sum($carnumber_sum);
            }

            $alphabet = implode("" ,preg_split("/\d+/", $registration_no));
            $carno_alphabets = str_split($alphabet);
            $alphabet_no = array();
            foreach($carno_alphabets as $carno_alphabet)
            {
                $carno_alphabetno = Alphasystem_type::where('alphabet','LIKE','%'.$carno_alphabet.'%')
                            ->where('systemtype_id', 2)
                            ->value('number');
                array_push($alphabet_no, $carno_alphabetno);
            }
            $carno_alphabetno_sum = array_sum($alphabet_no);
            while(strlen($carno_alphabetno_sum)!= 1)
            {
                $carno_alphabetno_sum = str_split($carno_alphabetno_sum);
                $carno_alphabetno_sum = array_sum($carno_alphabetno_sum);
            }
            $final_carno = $carnumber_sum + $carno_alphabetno_sum;
             while(strlen($final_carno)!= 1)
             {
                $final_carno = str_split($final_carno);
                $final_carno = array_sum($final_carno);
             }

            //percentage Aco to dob

            $dob_carpercentage = Compatibility_percentage::where('number', $cal_dobno)->where('mate_number', $final_carno)->first();
            $cardob_percentage = $dob_carpercentage->compatibility_percentage;
            $remain_percentage = 100 - $cardob_percentage; */

            //case2 name and car name reading

            //car name reading
            $struppername = strtoupper($car_name);
            $names_array = explode(' ', $struppername);
            $cal_car_chaldno = array();
            foreach ($names_array as $carnamewords) {
                $wordletter = str_split($carnamewords);
                $wordchald_no = array();
                foreach ($wordletter as $wordletters) {
                    $word_chald_number = Alphasystem_type::where('alphabet', 'LIKE', '%' . $wordletters . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                    array_push($wordchald_no, $word_chald_number);
                }
                $car_chaldno = array_sum($wordchald_no);
                while (strlen($car_chaldno) != 1) {
                    $car_chaldno = str_split($car_chaldno, 1);
                    $car_chaldno = array_sum($car_chaldno);
                }
                array_push($cal_car_chaldno, $car_chaldno);
            }
            $car_chaldnumber = array_sum($cal_car_chaldno);
            while (strlen($car_chaldnumber) != 1) {
                $car_chaldnumber = str_split($car_chaldnumber);
                $car_chaldnumber = array_sum($car_chaldnumber);
            }

            //car brand name reading

            $strupperbrandname = strtoupper($brand_name);
            $brandname_array = explode(' ', $strupperbrandname);
            $cal_brandname_chaldno = array();
            foreach ($brandname_array as $brandnamewords) {
                $brandnameletter = str_split($brandnamewords);
                $brandnamechald_no = array();
                foreach ($brandnameletter as $brandnameletters) {
                    $brand_namechald_no = Alphasystem_type::where('alphabet', 'LIKE', '%' . $brandnameletters . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                    array_push($brandnamechald_no, $brand_namechald_no);
                }
                $brandname_chaldno = array_sum($brandnamechald_no);
                while (strlen($brandname_chaldno) != 1) {
                    $brandname_chaldno = str_split($brandname_chaldno, 1);
                    $brandname_chaldno = array_sum($brandname_chaldno);
                }
                array_push($cal_brandname_chaldno, $brandname_chaldno);
            }
            $brandname_chaldnumber = array_sum($cal_brandname_chaldno);
            while (strlen($brandname_chaldnumber) != 1) {
                $brandname_chaldnumber = str_split($brandname_chaldnumber);
                $brandname_chaldnumber = array_sum($brandname_chaldnumber);
            }

            //car model reading

            $struppermodelname = strtoupper($modal);
            $modelname_array = explode(' ', $struppermodelname);
            $cal_modelname_chaldno = array();
            foreach ($modelname_array as $modelnamewords) {
                $modelnameletter = str_split($modelnamewords);
                $modelnamechald_no = array();
                foreach ($modelnameletter as $modelnameletters) {
                    $model_namechald_no = Alphasystem_type::where('alphabet', 'LIKE', '%' . $modelnameletters . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                    array_push($modelnamechald_no, $model_namechald_no);
                }
                $modelname_chaldno = array_sum($modelnamechald_no);
                while (strlen($modelname_chaldno) != 1) {
                    $modelname_chaldno = str_split($modelname_chaldno, 1);
                    $modelname_chaldno = array_sum($modelname_chaldno);
                }
                array_push($cal_modelname_chaldno, $modelname_chaldno);
            }
            $modelname_chaldnumber = array_sum($cal_modelname_chaldno);
            while (strlen($modelname_chaldnumber) != 1) {
                $modelname_chaldnumber = str_split($modelname_chaldnumber);
                $modelname_chaldnumber = array_sum($modelname_chaldnumber);
            }

            //login user name reading

            $loginusername = $loginuser->name;
            $finalname = str_replace(' ', '', $loginusername);
            $loginuserstrname = strtoupper($finalname);
            $loginusersplitname = str_split($loginuserstrname, 1);
            foreach ($loginusersplitname as $nameletter) {
                $chald_no[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                    ->where('systemtype_id', 2)
                    ->value('number');
            }
            $loginuserchaldno_sum = array_sum($chald_no);
            while (strlen($loginuserchaldno_sum) != 1) {
                $loginuserchaldno_sum = str_split($loginuserchaldno_sum, 1);
                $loginuserchaldno_sum = array_sum($loginuserchaldno_sum);
            }

            $loginusernamereadingno = $loginuserchaldno_sum;

            //percentage login name

            $namepercentage = Compatibility_percentage::where('number', $loginusernamereadingno)->where('mate_number', $car_chaldnumber)->first();
            $name_percentage = $namepercentage->compatibility_percentage;

            /* $cal_percentage = ($remain_percentage/100) * $name_percentage;
            $final_compatibility_percentage = $cardob_percentage + $cal_percentage; */

            //brand_name and login_user name persentage
            $brandnamepercentage = Compatibility_percentage::where('number', $loginusernamereadingno)->where('mate_number', $brandname_chaldnumber)->first();
            $brandname_percentage = $brandnamepercentage->compatibility_percentage;


            //model and login_user name persentage
            $modelnamepercentage = Compatibility_percentage::where('number', $loginusernamereadingno)->where('mate_number', $modelname_chaldnumber)->first();
            $modelname_percentage = $modelnamepercentage->compatibility_percentage;


            $compatibilitycheck = array(
                'type' => $type, 'car_name' => $car_name, 'final_compatibility_percentage' => $name_percentage,
                'first_compatibility_processingbar' => $brandname_percentage, 'second_compatibility_processingbar' => $modelname_percentage
            );

            $exellent_desc = $loginuser->name . ", as per AstroNumeric calculations, your " . $type_name . " which is " . $brand_name . " " . $modal . " is perfectly compatible with your Birth Number which is " . $cal_dobno . ". A " . $type . " is completely lucky for you and can also save you from unforeseen events like an accident, vehicle theft, vehicle problems, etc. " . $type_name . " should carry high vibrations and it should keep attracting more wealth and prosperity in your life.";
            $good_desc = $loginuser->name . ", as per numerology calculations, your " . $type_name . " which is " . $brand_name . " " . $modal . " has good compatibility with your Birth Number which is " . $cal_dobno . ". On the basis of good compatibility, these vehicles rarely get stolen, but it brings good luck for someone whose profession is related to money. It is very favorable for business people.";
            $bad_desc = $loginuser->name . ", as per numerology studies, your " . $type_name . " which is " . $brand_name . " " . $modal . " has very low compatibility with your Birth Number which is " . $cal_dobno . ". It promotes uncooperative rearrangements and resourcefulness on the road. You have the best compatibility with " . $type . " which is " . $brand_name . " " . $modal . ". It accelerates quickly, is reliable, and attracts attention.  ";

            return response()->json([
                'status' => 1,
                'message' => 'success',
                'userid' => $userid,
                'subscription_status' => $loginuser->subscription_status,
                'compatibilitydetail' => $compatibilitycheck,
                'exellent_desc' => $exellent_desc,
                'good_desc' => $good_desc,
                'bad_desc' => $bad_desc
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Error',
            ]);
        }
    }

    public function checkbusinesscompatibility(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $type = $request->type;
        // For Car 
        if ($type == 3) // For Business 
        {
            $validator = Validator::make($request->all(), [
                'userid' => 'required',
                'type' => 'required',
                'type_name' => 'required',
                'business_name' => 'required',
                'incorporation_date' => 'required',
                'partners' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }
            $userid = $request->userid;
            $type_name = $request->type_name;
            $business_name = $request->business_name;
            $incorporation_date = $request->incorporation_date;
            $no_of_partner = $request->pertners;

            $compatibility = User_compatiblecheck::create([
                'user_id' => $userid,
                'type' => $type,
                'type_name' => $type_name,
                'name' => $business_name,
                'type_dates' => 1,
                'dates' => $incorporation_date,
                'no_of_partner' => $no_of_partner,
            ]);

            //case1 dob and incorporation_date reading

            //user dob reading
            $loginuser = User::find($userid);
            $dob = $loginuser->dob;
            $date = explode('-', $dob);
            $day = str_split($date[2], 1);
            $cal_dob_no = array_sum($day);
            $cal_dobno = intval($cal_dob_no);
            while (strlen($cal_dobno) != 1) {
                $cal_dobno = str_split($cal_dobno);
                $cal_dobno = array_sum($cal_dobno);
            }

            //incorporation_date reading

            $inc_date = explode('-', $incorporation_date);
            $inc_day = str_split($inc_date[2], 1);
            $cal_incdate_no = array_sum($inc_day);
            $cal_incdateno = intval($cal_incdate_no);
            while (strlen($cal_incdateno) != 1) {
                $cal_incdateno = str_split($cal_incdateno);
                $cal_incdateno = array_sum($cal_incdateno);
            }

            //percentage Aco to dob

            $primaerynopercentage = Compatibility_percentage::where('number', $cal_dobno)->where('mate_number', $cal_incdateno)->first();
            $businessdob_percentage = $primaerynopercentage->compatibility_percentage;
            $remain_percentage = 100 - $businessdob_percentage;

            $primary_nopercentage = $businessdob_percentage;

            //case2 name and business name reading

            //business name reading
            $struppername = strtoupper($business_name);
            $names_array = explode(' ', $struppername);
            $cal_business_chaldno = array();
            foreach ($names_array as $businessnamewords) {
                $wordletter = str_split($businessnamewords);
                $wordchald_no = array();
                foreach ($wordletter as $wordletters) {
                    $word_chald_number = Alphasystem_type::where('alphabet', 'LIKE', '%' . $wordletters . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                    array_push($wordchald_no, $word_chald_number);
                }
                $business_chaldno = array_sum($wordchald_no);
                while (strlen($business_chaldno) != 1) {
                    $business_chaldno = str_split($business_chaldno, 1);
                    $business_chaldno = array_sum($business_chaldno);
                }
                array_push($cal_business_chaldno, $business_chaldno);
            }
            $business_chaldnumber = array_sum($cal_business_chaldno);
            while (strlen($business_chaldnumber) != 1) {
                $business_chaldnumber = str_split($business_chaldnumber);
                $business_chaldnumber = array_sum($business_chaldnumber);
            }

            // business type name reading

            $struppertypename = strtoupper($type_name);
            $typename_array = explode(' ', $struppertypename);
            $cal_businesstype_chaldno = array();
            foreach ($typename_array as $businesstypenamewords) {
                $typenameletter = str_split($businesstypenamewords);
                $typewordchald_no = array();
                foreach ($typenameletter as $typenameletters) {
                    $letter_chald_number = Alphasystem_type::where('alphabet', 'LIKE', '%' . $typenameletters . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                    array_push($typewordchald_no, $letter_chald_number);
                }
                $businesstype_chaldno = array_sum($typewordchald_no);
                while (strlen($businesstype_chaldno) != 1) {
                    $businesstype_chaldno = str_split($businesstype_chaldno, 1);
                    $businesstype_chaldno = array_sum($businesstype_chaldno);
                }
                array_push($cal_businesstype_chaldno, $businesstype_chaldno);
            }
            $businesstype_chaldnumber = array_sum($cal_businesstype_chaldno);
            while (strlen($businesstype_chaldnumber) != 1) {
                $businesstype_chaldnumber = str_split($businesstype_chaldnumber);
                $businesstype_chaldnumber = array_sum($businesstype_chaldnumber);
            }

            //login user name reading

            $loginusername = $loginuser->name;
            $finalname = str_replace(' ', '', $loginusername);
            $loginuserstrname = strtoupper($finalname);
            $loginusersplitname = str_split($loginuserstrname, 1);
            foreach ($loginusersplitname as $nameletter) {
                $chald_no[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                    ->where('systemtype_id', 2)
                    ->value('number');
            }
            $loginuserchaldno_sum = array_sum($chald_no);
            while (strlen($loginuserchaldno_sum) != 1) {
                $loginuserchaldno_sum = str_split($loginuserchaldno_sum, 1);
                $loginuserchaldno_sum = array_sum($loginuserchaldno_sum);
            }

            $loginusernamereadingno = $loginuserchaldno_sum;

            //percentage login name

            $namepercentage = Compatibility_percentage::where('number', $loginusernamereadingno)->where('mate_number', $business_chaldnumber)->first();
            $name_percentage = $namepercentage->compatibility_percentage;

            $cal_percentage = ($remain_percentage / 100) * $name_percentage;
            $final_compatibility_percentage = $businessdob_percentage + $cal_percentage;

            // business type name percentage

            $typenamepercentage = Compatibility_percentage::where('number', $loginusernamereadingno)->where('mate_number', $businesstype_chaldnumber)->first();
            $typename_percentage = $typenamepercentage->compatibility_percentage;

            $compatibilitycheck = array(
                'type' => $type, 'business_name' => $business_name, 'nametoname_percentage' => $cal_percentage, 'dobtodate_percentage' => $businessdob_percentage, 'final_compatibility_percentage' => $final_compatibility_percentage,
                'first_compatibility_processingbar' => $primary_nopercentage, 'second_compatibility_processingbar' => $name_percentage, 'third_compatibility_processingbar' => $typename_percentage
            );

            $exellent_desc = $loginuser->name . ", according to the calculation of numbers, Since " . $incorporation_date . " there is a spiritual correlation between your Birth Number which is " . $cal_dobno . ", " . $business_name . ", and " . $type_name . ". Your Birth Number " . $cal_dobno . " is completely compatible with your " . $business_name . ", it results in rapid growth in the era of business. In no time you will get many business travels that also afford you great networking opportunities to help broaden your network and progress your career and bring bigger and better deals. Your Birth Number " . $cal_dobno . " has strong compatibility with your business name and it results in amazing surprises.";
            $good_desc = $loginuser->name . ", as per AstroNumeric law, Since " . $incorporation_date . " your Birth Number which is " . $cal_dobno . " has good compatibility with " . $business_name . " and " . $type_name . ". It seems like it takes some time for the astounding growth in the industrial environment. You can face a few ups and downs when you start competing with others, but the positive thing is that you never lose anything while competing. If your business is running on the low side then the compatible vibrations balance the frequency and again stable your business at the normal stage. ";
            $bad_desc = $loginuser->name . ", as per numerology law, Since " . $incorporation_date . " your Birth Number which is " . $cal_dobno . " does not have much good compatibility with " . $business_name . " and " . $type_name . ". It seems like it will lower your quality of work day by day.  As per predictions, you can start your own business or with a partnership after a couple of years, then you have a chance to lead your business to the next level.";

            return response()->json([
                'status' => 1,
                'message' => 'success',
                'userid' => $userid,
                'subscription_status' => $loginuser->subscription_status,
                'compatibilitydetail' => $compatibilitycheck,
                'dob' => $loginuser->dob,
                'username' => $loginuser->name,
                'prime_no' => $cal_dobno,
                'incorporation_date' => $incorporation_date,
                'exellent_desc' => $exellent_desc,
                'good_desc' => $good_desc,
                'bad_desc' => $bad_desc


            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Error',
            ]);
        }
    }

    public function checkpropertycompatibility(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $type = $request->type;
        // For Car 
        if ($type == 4) // For Property
        {
            $validator = Validator::make($request->all(), [
                'userid' => 'required',
                'type' => 'required',
                'type_name' => 'required',
                'property_number' => 'required',
                'pin' => 'required',
                'city' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }
            $userid = $request->userid;
            $type_name = $request->type_name;
            $property_number = $request->property_number;
            $postalcode = $request->pin;
            $city = $request->city;

            $compatibility = User_compatiblecheck::create([

                'user_id' => $userid,
                'type' => $type,
                'type_name' => $type_name,
                'number' => $property_number,
                'postalcode' => $postalcode,
                'city' => $city,
            ]);

            //user dob reading
            $loginuser = User::find($userid);
            $dob = $loginuser->dob;
            $date = explode('-', $dob);
            $day = str_split($date[2], 1);
            $cal_dobno = array_sum($day);
            $cal_dobno = intval($cal_dobno);
            while (strlen($cal_dobno) != 1) {
                $cal_dobno = str_split($cal_dobno);
                $cal_dobno = array_sum($cal_dobno);
            }

            // property number reading

            $numaric = str_split(filter_var($property_number, FILTER_SANITIZE_NUMBER_INT));
            $propertynumber_sum = array_sum($numaric);
            while (strlen($propertynumber_sum) != 1) {
                $propertynumber_sum = str_split($propertynumber_sum);
                $propertynumber_sum = array_sum($propertynumber_sum);
            }

            $alphabet = implode("", preg_split("/\d+/", $property_number));
            $propertyno_alphabetno_sum = 0;
            if ($alphabet != null) {
                $propertyno_alphabets = str_split($alphabet);
                $alphabet_no = array();
                foreach ($propertyno_alphabets as $propertyno_alphabet) {
                    $propertyno_alphabetno = Alphasystem_type::where('alphabet', 'LIKE', '%' . $propertyno_alphabet . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                    array_push($alphabet_no, $propertyno_alphabetno);
                }

                $propertyno_alphabetno_sum = array_sum($alphabet_no);
                while (strlen($propertyno_alphabetno_sum) != 1) {
                    $propertyno_alphabetno_sum = str_split($propertyno_alphabetno_sum);
                    $propertyno_alphabetno_sum = array_sum($propertyno_alphabetno_sum);
                }
            }
            $final_propertyno = $propertynumber_sum + $propertyno_alphabetno_sum;
            while (strlen($final_propertyno) != 1) {
                $final_propertyno = str_split($final_propertyno);
                $final_propertyno = array_sum($final_propertyno);
            }

            //percentage Aco to dob and property number

            $dob_propertypercentage = Compatibility_percentage::where('number', $cal_dobno)->where('mate_number', $final_propertyno)->first();
            $propertydob_percentage = $dob_propertypercentage->compatibility_percentage;
            $remain_percentage = 100 - $propertydob_percentage;


            // case2 dob and pin reading

            //pin reading 
            $pin = str_split($postalcode, 1);
            $cal_pinno = array_sum($pin);
            $cal_pinno = intval($cal_pinno);
            while (strlen($cal_pinno) != 1) {
                $cal_pinno = str_split($cal_pinno);
                $cal_pinno = array_sum($cal_pinno);
            }

            //percentage of dob and property or dob and PIN percentage

            $dob_pin_percentage = Compatibility_percentage::where('number', $cal_dobno)->where('mate_number', $cal_pinno)->first();
            $dobpin_percentage = $dob_pin_percentage->compatibility_percentage;

            $dob_cal_percentage = ($remain_percentage / 100) * $dobpin_percentage;
            $case2_percentage = $propertydob_percentage + $dob_cal_percentage;
            $remainingcase2_percentage = 100 - $case2_percentage;


            //case3 name and city reading 

            //city name reading 
            $struppername = strtoupper($city);
            $names_array = explode(' ', $struppername);
            $cal_city_chaldno = array();
            foreach ($names_array as $citynamewords) {
                $wordletter = str_split($citynamewords);
                $wordchald_no = array();
                foreach ($wordletter as $wordletters) {
                    $word_chald_number = Alphasystem_type::where('alphabet', 'LIKE', '%' . $wordletters . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                    array_push($wordchald_no, $word_chald_number);
                }
                $city_chaldno = array_sum($wordchald_no);
                while (strlen($city_chaldno) != 1) {
                    $city_chaldno = str_split($city_chaldno, 1);
                    $city_chaldno = array_sum($city_chaldno);
                }
                array_push($cal_city_chaldno, $city_chaldno);
            }
            $city_chaldnumber = array_sum($cal_city_chaldno);
            while (strlen($city_chaldnumber) != 1) {
                $city_chaldnumber = str_split($city_chaldnumber);
                $city_chaldnumber = array_sum($city_chaldnumber);
            }

            // login user name reading
            $loginusername = $loginuser->name;
            $finalname = str_replace(' ', '', $loginusername);
            $loginuserstrname = strtoupper($finalname);
            $loginusersplitname = str_split($loginuserstrname, 1);
            foreach ($loginusersplitname as $nameletter) {
                $chald_no[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                    ->where('systemtype_id', 2)
                    ->value('number');
            }
            $loginuserchaldno_sum = array_sum($chald_no);
            while (strlen($loginuserchaldno_sum) != 1) {
                $loginuserchaldno_sum = str_split($loginuserchaldno_sum, 1);
                $loginuserchaldno_sum = array_sum($loginuserchaldno_sum);
            }

            $loginusernamereadingno = $loginuserchaldno_sum;

            //total percentage of compatibility
            $name_city_percentage = Compatibility_percentage::where('number', $loginusernamereadingno)->where('mate_number', $city_chaldnumber)->first();
            $name_city_percentage = $name_city_percentage->compatibility_percentage;

            $namecal_percentage = ($remainingcase2_percentage / 100) * $name_city_percentage;
            $final_compatibility_percentage = $case2_percentage + $namecal_percentage;

            $compatibilitycheck = array('type' => $type, 'property_number' => $property_number, 'dobtopropertyno_percentage' => $propertydob_percentage, 'dobtopin_percentage' => $case2_percentage, 'nametocity_percentage' => $namecal_percentage, 'final_compatibility_percentage' => $final_compatibility_percentage, 'first_compatibility_processingbar' => $propertydob_percentage, 'second_compatibility_processingbar' => $dobpin_percentage, 'third_compatibility_processingbar' => $name_city_percentage);

            $exellent_desc = $loginuser->name . ", according to the calculation of numbers, every number has its own vibrations and frequencies. If your Birth Number which is " . $cal_dobno . " is completely compatible with your " . $type_name . " " . $property_number . " " . $postalcode . " and " . $city . " then, it makes a strong affinity with your property. A stronger sense of bond and togetherness is seen in this number. You will celebrate many occasions without any complications as these properties are considered a prosperous place for you";
            $good_desc = $loginuser->name . ", according to the numerology calculations, your Birth Number " . $cal_dobno . " has good compatibility with your " . $type_name . " " . $property_number . " " . $postalcode . " and " . $city . ". If you are committed or married, then " . $property_number . " can be damaging to your relationship unless you are a business partner; this number is lucky for you both. If you go randomly with property numbers, then you might have to face problems in your professional and personal life.";
            $bad_desc = $loginuser->name . ", according to the numerology calculations, your Birth Number [Birth Number/Prime Number] has a very low chance of compatibility with " . $type_name . " " . $property_number . " " . $postalcode . " and " . $city . ". This house brings big financial ups and downs. As per your numerology numbers predict that these numbers are completely suitable for you " . $property_number . " and so on.";


            return response()->json([
                'status' => 1,
                'message' => 'success',
                'userid' => $userid,
                'subscription_status' => $loginuser->subscription_status,
                'compatibilitydetail' => $compatibilitycheck,
                'propertytype_name' => $type_name,
                'username' => $loginuser->name,
                'property_no' => $property_number,
                'city' => $city,
                'prime_no' => $cal_dobno,
                'exellent_desc' => $exellent_desc,
                'good_desc' => $good_desc,
                'bad_desc' => $bad_desc


            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Error',
            ]);
        }
    }

    public function checknamereadingcompatibility(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $type = $request->type;
        // For Car 
        if ($type == 7) // For name compatibility
        {
            $validator = Validator::make($request->all(), [
                'userid' => 'required',
                'type' => 'required',
                'name' => 'required',
                'dob' => 'required',
                'gender' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }
            $userid = $request->userid;
            $otherperson_name = $request->name;
            $otherpersondob = $request->dob;
            $otherpersongender = $request->gender;

            $loginuser = User::find($userid);
            $otherpersondetail = User_compatiblecheck::create([

                'user_id' => $userid,
                'type' => $type,
                'type_name' => 'partner compatibility',
                'name' => $otherperson_name,
                'gender' => $otherpersongender,
                'type_dates' => 3,
                'dates' => $otherpersondob,
            ]);

            if ($otherpersondetail) {

                // other person name reading
                $partnerchaldno = array();
                $otherpersonname = explode(' ', $otherperson_name);
                $otherpersonstrname = strtoupper($otherpersonname[0]);
                $otherpersonsplitname = str_split($otherpersonstrname, 1);
                foreach ($otherpersonsplitname as $nameletter) {
                    $partnerchald_no = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                    array_push($partnerchaldno, $partnerchald_no);
                }

                $otherpersonchaldno_sum = array_sum($partnerchaldno);
                while (strlen($otherpersonchaldno_sum) != 1) {
                    $otherpersonchaldno_sum = str_split($otherpersonchaldno_sum, 1);
                    $otherpersonchaldno_sum = array_sum($otherpersonchaldno_sum);
                }

                $otherpersonnamereadingno = $otherpersonchaldno_sum;
                $otherpersonnamereading =  Module_description::where('moduletype_id', 1)
                    ->where('number', $otherpersonnamereadingno)
                    ->value('description');

                $explodeothernamereading_desc = explode('||', $otherpersonnamereading);
                $positive_desc = $explodeothernamereading_desc[0];
                $negative_desc = $explodeothernamereading_desc[1];
                $otherpersonnamedesc = array("positive_title" => "Positive", "positive_desc" => $positive_desc, "negative_title" => "Negative", "negative_desc" => $negative_desc);



                //otherperson magicbox

                $chald_number_count = array_count_values($partnerchaldno);

                if (array_key_exists(1, $chald_number_count)) {
                    $magicboxnumber1 = $chald_number_count['1'];
                } else {
                    $magicboxnumber1 = 0;
                }
                $boxcellno1 = array('number' => 1, 'numbervalue' => $magicboxnumber1);

                if (array_key_exists(2, $chald_number_count)) {
                    $magicboxnumber2 = $chald_number_count['2'];
                } else {
                    $magicboxnumber2 = 0;
                }
                $boxcellno2 = array('number' => 2, 'numbervalue' => $magicboxnumber2);

                if (array_key_exists(3, $chald_number_count)) {
                    $magicboxnumber3 = $chald_number_count['3'];
                } else {
                    $magicboxnumber3 = 0;
                }
                $boxcellno3 = array('number' => 3, 'numbervalue' => $magicboxnumber3);

                if (array_key_exists(4, $chald_number_count)) {
                    $magicboxnumber4 = $chald_number_count['4'];
                } else {
                    $magicboxnumber4 = 0;
                }
                $boxcellno4 = array('number' => 4, 'numbervalue' => $magicboxnumber4);

                if (array_key_exists(5, $chald_number_count)) {
                    $magicboxnumber5 = $chald_number_count['5'];
                } else {
                    $magicboxnumber5 = 0;
                }
                $boxcellno5 = array('number' => 5, 'numbervalue' => $magicboxnumber5);


                if (array_key_exists(6, $chald_number_count)) {
                    $magicboxnumber6 = $chald_number_count['6'];
                } else {
                    $magicboxnumber6 = 0;
                }
                $boxcellno6 = array('number' => 6, 'numbervalue' => $magicboxnumber6);

                if (array_key_exists(7, $chald_number_count)) {
                    $magicboxnumber7 = $chald_number_count['7'];
                } else {
                    $magicboxnumber7 = 0;
                }
                $boxcellno7 = array('number' => 7, 'numbervalue' => $magicboxnumber7);

                if (array_key_exists(8, $chald_number_count)) {
                    $magicboxnumber8 = $chald_number_count['8'];
                } else {
                    $magicboxnumber8 = 0;
                }
                $boxcellno8 = array('number' => 8, 'numbervalue' => $magicboxnumber8);

                if (array_key_exists(9, $chald_number_count)) {
                    $magicboxnumber9 = $chald_number_count['9'];
                } else {
                    $magicboxnumber9 = 0;
                }
                $boxcellno9 = array('number' => 9, 'numbervalue' => $magicboxnumber9);

                $magicboxdetail = array($boxcellno1, $boxcellno2, $boxcellno3, $boxcellno4, $boxcellno5, $boxcellno6, $boxcellno7, $boxcellno8, $boxcellno9);

                // login user name reading 
                $loginuser_name = $loginuser->name;
                $finalname = str_replace(' ', '', $loginuser_name);
                $loginuserstrname = strtoupper($finalname);
                $loginusersplitname = str_split($loginuserstrname, 1);
                foreach ($loginusersplitname as $nameletter) {
                    $letters[] = $nameletter;
                    $chald_nos[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                }
                $loginuserchaldno_sum = array_sum($chald_nos);
                while (strlen($loginuserchaldno_sum) != 1) {
                    $loginuserchaldno_sum = str_split($loginuserchaldno_sum, 1);
                    $loginuserchaldno_sum = array_sum($loginuserchaldno_sum);
                }

                $loginusernamereadingno = $loginuserchaldno_sum;
                $name_compatibilitypercentage = Compatibility_percentage::where('number', $loginusernamereadingno)->where('mate_number', $otherpersonnamereadingno)->first();
                $namecompatibilitypercentage = $name_compatibilitypercentage->compatibility_percentage;


                return response()->json([
                    'status' => 1,
                    'message' => 'success',
                    'subscription_status' => $loginuser->subscription_status,
                    'name' => $otherperson_name,
                    'dob' => $otherpersondob,
                    'namecompatibilitypercentage' => $namecompatibilitypercentage,
                    'namedec' => $otherpersonnamedesc,
                    'magicboxdetail' => $magicboxdetail,
                ]);
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'Error',
                ]);
            }
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Error',
            ]);
        }
    }

    //other module compatibility check
    public function othermodulecompatibilitycheck(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $type = $request->type;
        // For Car 
        if ($type == 1) {
            $validator = Validator::make($request->all(), [
                'userid' => 'required',
                'type' => 'required',
                'type_name' => 'required',
                'brand_name' => 'required',
                'modal' => 'required',
                'registration_no' => 'required',

            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }
            $userid = $request->userid;
            $type_name = $request->type_name;
            $brand_name = $request->brand_name;
            $modal = $request->modal;
            $car_name = $brand_name . " " . $modal;
            $registration_no = $request->registration_no;

            $compatibility = User_compatiblecheck::create([

                'user_id' => $userid,
                'type' => $type,
                'name' => $car_name,
                'type_name' => $type_name,
                'number' => $registration_no,
            ]);

            //case1 dob and registration_no reading

            //user dob reading
            $loginuser = User::find($userid);
            $dob = $loginuser->dob;
            $date = explode('-', $dob);
            $day = str_split($date[2], 1);
            $cal_dobno = array_sum($day);
            $cal_dobno = intval($cal_dobno);
            while (strlen($cal_dobno) != 1) {
                $cal_dobno = str_split($cal_dobno);
                $cal_dobno = array_sum($cal_dobno);
            }

            //car registration_no reading

            $numaric = str_split(filter_var($registration_no, FILTER_SANITIZE_NUMBER_INT));
            $carnumber_sum = array_sum($numaric);
            while (strlen($carnumber_sum) != 1) {
                $carnumber_sum = str_split($carnumber_sum);
                $carnumber_sum = array_sum($carnumber_sum);
            }

            $alphabet = implode("", preg_split("/\d+/", $registration_no));
            $carno_alphabets = str_split($alphabet);
            $alphabet_no = array();
            foreach ($carno_alphabets as $carno_alphabet) {
                $carno_alphabetno = Alphasystem_type::where('alphabet', 'LIKE', '%' . $carno_alphabet . '%')
                    ->where('systemtype_id', 2)
                    ->value('number');
                array_push($alphabet_no, $carno_alphabetno);
            }
            $carno_alphabetno_sum = array_sum($alphabet_no);
            while (strlen($carno_alphabetno_sum) != 1) {
                $carno_alphabetno_sum = str_split($carno_alphabetno_sum);
                $carno_alphabetno_sum = array_sum($carno_alphabetno_sum);
            }
            $final_carno = $carnumber_sum + $carno_alphabetno_sum;
            while (strlen($final_carno) != 1) {
                $final_carno = str_split($final_carno);
                $final_carno = array_sum($final_carno);
            }

            //percentage Aco to dob

            $dob_carpercentage = Compatibility_percentage::where('number', $cal_dobno)->where('mate_number', $final_carno)->first();
            $cardob_percentage = $dob_carpercentage->compatibility_percentage;
            $remain_percentage = 100 - $cardob_percentage;

            //case2 name and car name reading

            //car name reading
            $struppername = strtoupper($car_name);
            $names_array = explode(' ', $struppername);
            $cal_car_chaldno = array();
            foreach ($names_array as $carnamewords) {
                $wordletter = str_split($carnamewords);
                $wordchald_no = array();
                foreach ($wordletter as $wordletters) {
                    $word_chald_number = Alphasystem_type::where('alphabet', 'LIKE', '%' . $wordletters . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                    array_push($wordchald_no, $word_chald_number);
                }
                $car_chaldno = array_sum($wordchald_no);
                while (strlen($car_chaldno) != 1) {
                    $car_chaldno = str_split($car_chaldno, 1);
                    $car_chaldno = array_sum($car_chaldno);
                }
                array_push($cal_car_chaldno, $car_chaldno);
            }
            $car_chaldnumber = array_sum($cal_car_chaldno);
            while (strlen($car_chaldnumber) != 1) {
                $car_chaldnumber = str_split($car_chaldnumber);
                $car_chaldnumber = array_sum($car_chaldnumber);
            }

            //car brand name reading

            $strupperbrandname = strtoupper($brand_name);
            $brandname_array = explode(' ', $strupperbrandname);
            $cal_brandname_chaldno = array();
            foreach ($brandname_array as $brandnamewords) {
                $brandnameletter = str_split($brandnamewords);
                $brandnamechald_no = array();
                foreach ($brandnameletter as $brandnameletters) {
                    $brand_namechald_no = Alphasystem_type::where('alphabet', 'LIKE', '%' . $brandnameletters . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                    array_push($brandnamechald_no, $brand_namechald_no);
                }
                $brandname_chaldno = array_sum($brandnamechald_no);
                while (strlen($brandname_chaldno) != 1) {
                    $brandname_chaldno = str_split($brandname_chaldno, 1);
                    $brandname_chaldno = array_sum($brandname_chaldno);
                }
                array_push($cal_brandname_chaldno, $brandname_chaldno);
            }
            $brandname_chaldnumber = array_sum($cal_brandname_chaldno);
            while (strlen($brandname_chaldnumber) != 1) {
                $brandname_chaldnumber = str_split($brandname_chaldnumber);
                $brandname_chaldnumber = array_sum($brandname_chaldnumber);
            }

            //car model reading

            $struppermodelname = strtoupper($modal);
            $modelname_array = explode(' ', $struppermodelname);
            $cal_modelname_chaldno = array();
            foreach ($modelname_array as $modelnamewords) {
                $modelnameletter = str_split($modelnamewords);
                $modelnamechald_no = array();
                foreach ($modelnameletter as $modelnameletters) {
                    $model_namechald_no = Alphasystem_type::where('alphabet', 'LIKE', '%' . $modelnameletters . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                    array_push($modelnamechald_no, $model_namechald_no);
                }
                $modelname_chaldno = array_sum($modelnamechald_no);
                while (strlen($modelname_chaldno) != 1) {
                    $modelname_chaldno = str_split($modelname_chaldno, 1);
                    $modelname_chaldno = array_sum($modelname_chaldno);
                }
                array_push($cal_modelname_chaldno, $modelname_chaldno);
            }
            $modelname_chaldnumber = array_sum($cal_modelname_chaldno);
            while (strlen($modelname_chaldnumber) != 1) {
                $modelname_chaldnumber = str_split($modelname_chaldnumber);
                $modelname_chaldnumber = array_sum($modelname_chaldnumber);
            }

            //login user name reading

            $loginusername = $loginuser->name;
            $finalname = str_replace(' ', '', $loginusername);
            $loginuserstrname = strtoupper($finalname);
            $loginusersplitname = str_split($loginuserstrname, 1);
            foreach ($loginusersplitname as $nameletter) {
                $chald_no[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                    ->where('systemtype_id', 2)
                    ->value('number');
            }
            $loginuserchaldno_sum = array_sum($chald_no);
            while (strlen($loginuserchaldno_sum) != 1) {
                $loginuserchaldno_sum = str_split($loginuserchaldno_sum, 1);
                $loginuserchaldno_sum = array_sum($loginuserchaldno_sum);
            }

            $loginusernamereadingno = $loginuserchaldno_sum;

            //percentage login name

            $namepercentage = Compatibility_percentage::where('number', $loginusernamereadingno)->where('mate_number', $car_chaldnumber)->first();
            $name_percentage = $namepercentage->compatibility_percentage;

            $cal_percentage = ($remain_percentage / 100) * $name_percentage;
            $final_compatibility_percentage = $cardob_percentage + $cal_percentage;

            //brand_name and login_user name persentage
            $brandnamepercentage = Compatibility_percentage::where('number', $loginusernamereadingno)->where('mate_number', $brandname_chaldnumber)->first();
            $brandname_percentage = $brandnamepercentage->compatibility_percentage;


            //model and login_user name persentage
            $modelnamepercentage = Compatibility_percentage::where('number', $loginusernamereadingno)->where('mate_number', $modelname_chaldnumber)->first();
            $modelname_percentage = $modelnamepercentage->compatibility_percentage;


            $compatibilitycheck = array(
                'type' => $type, 'car_name' => $car_name, 'nametoname_percentage' => $cal_percentage, 'dobtodob_percentage' => $cardob_percentage, 'final_compatibility_percentage' => $final_compatibility_percentage,
                'first_compatibility_processingbar' => $brandname_percentage, 'second_compatibility_processingbar' => $modelname_percentage, 'third_compatibility_processingbar' => $cardob_percentage
            );

            return response()->json([
                'status' => 1,
                'message' => 'success',
                'userid' => $userid,
                'subscription_status' => $loginuser->subscription_status,
                'compatibilitydetail' => $compatibilitycheck,
            ]);
        } elseif ($type == 3) // For Business 
        {
            $validator = Validator::make($request->all(), [
                'userid' => 'required',
                'type' => 'required',
                'type_name' => 'required',
                'business_name' => 'required',
                'incorporation_date' => 'required',
                'partners' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }
            $userid = $request->userid;
            $type_name = $request->type_name;
            $business_name = $request->business_name;
            $incorporation_date = $request->incorporation_date;
            $no_of_partner = $request->pertners;
            $compatibility = User_compatiblecheck::create([
                'user_id' => $userid,
                'type' => $type,
                'type_name' => $type_name,
                'name' => $business_name,
                'type_dates' => 1,
                'dates' => $incorporation_date,
                'no_of_partner' => $no_of_partner,
            ]);

            //case1 dob and incorporation_date reading

            //user dob reading
            $loginuser = User::find($userid);
            $dob = $loginuser->dob;
            $date = explode('-', $dob);
            $day = str_split($date[2], 1);
            $cal_dob_no = array_sum($day);
            $cal_dobno = intval($cal_dob_no);
            while (strlen($cal_dobno) != 1) {
                $cal_dobno = str_split($cal_dobno);
                $cal_dobno = array_sum($cal_dobno);
            }

            //incorporation_date reading

            $inc_date = explode('-', $incorporation_date);
            $inc_day = str_split($inc_date[2], 1);
            $cal_incdate_no = array_sum($inc_day);
            $cal_incdateno = intval($cal_incdate_no);
            while (strlen($cal_incdateno) != 1) {
                $cal_incdateno = str_split($cal_incdateno);
                $cal_incdateno = array_sum($cal_incdateno);
            }

            //percentage Aco to dob

            $primaerynopercentage = Compatibility_percentage::where('number', $cal_dobno)->where('mate_number', $cal_incdateno)->first();
            $businessdob_percentage = $primaerynopercentage->compatibility_percentage;
            $remain_percentage = 100 - $businessdob_percentage;

            $primary_nopercentage = $businessdob_percentage;

            //case2 name and business name reading

            //business name reading
            $struppername = strtoupper($business_name);
            $names_array = explode(' ', $struppername);
            $cal_business_chaldno = array();
            foreach ($names_array as $businessnamewords) {
                $wordletter = str_split($businessnamewords);
                $wordchald_no = array();
                foreach ($wordletter as $wordletters) {
                    $word_chald_number = Alphasystem_type::where('alphabet', 'LIKE', '%' . $wordletters . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                    array_push($wordchald_no, $word_chald_number);
                }
                $business_chaldno = array_sum($wordchald_no);
                while (strlen($business_chaldno) != 1) {
                    $business_chaldno = str_split($business_chaldno, 1);
                    $business_chaldno = array_sum($business_chaldno);
                }
                array_push($cal_business_chaldno, $business_chaldno);
            }
            $business_chaldnumber = array_sum($cal_business_chaldno);
            while (strlen($business_chaldnumber) != 1) {
                $business_chaldnumber = str_split($business_chaldnumber);
                $business_chaldnumber = array_sum($business_chaldnumber);
            }

            // business type name reading

            $struppertypename = strtoupper($type_name);
            $typename_array = explode(' ', $struppertypename);
            $cal_businesstype_chaldno = array();
            foreach ($typename_array as $businesstypenamewords) {
                $typenameletter = str_split($businesstypenamewords);
                $typewordchald_no = array();
                foreach ($typenameletter as $typenameletters) {
                    $letter_chald_number = Alphasystem_type::where('alphabet', 'LIKE', '%' . $typenameletters . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                    array_push($typewordchald_no, $letter_chald_number);
                }
                $businesstype_chaldno = array_sum($typewordchald_no);
                while (strlen($businesstype_chaldno) != 1) {
                    $businesstype_chaldno = str_split($businesstype_chaldno, 1);
                    $businesstype_chaldno = array_sum($businesstype_chaldno);
                }
                array_push($cal_businesstype_chaldno, $businesstype_chaldno);
            }
            $businesstype_chaldnumber = array_sum($cal_businesstype_chaldno);
            while (strlen($businesstype_chaldnumber) != 1) {
                $businesstype_chaldnumber = str_split($businesstype_chaldnumber);
                $businesstype_chaldnumber = array_sum($businesstype_chaldnumber);
            }

            //login user name reading

            $loginusername = $loginuser->name;
            $finalname = str_replace(' ', '', $loginusername);
            $loginuserstrname = strtoupper($finalname);
            $loginusersplitname = str_split($loginuserstrname, 1);
            foreach ($loginusersplitname as $nameletter) {
                $chald_no[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                    ->where('systemtype_id', 2)
                    ->value('number');
            }
            $loginuserchaldno_sum = array_sum($chald_no);
            while (strlen($loginuserchaldno_sum) != 1) {
                $loginuserchaldno_sum = str_split($loginuserchaldno_sum, 1);
                $loginuserchaldno_sum = array_sum($loginuserchaldno_sum);
            }

            $loginusernamereadingno = $loginuserchaldno_sum;

            //percentage login name

            $namepercentage = Compatibility_percentage::where('number', $loginusernamereadingno)->where('mate_number', $business_chaldnumber)->first();
            $name_percentage = $namepercentage->compatibility_percentage;

            $cal_percentage = ($remain_percentage / 100) * $name_percentage;
            $final_compatibility_percentage = $businessdob_percentage + $cal_percentage;

            // business type name percentage

            $typenamepercentage = Compatibility_percentage::where('number', $loginusernamereadingno)->where('mate_number', $businesstype_chaldnumber)->first();
            $typename_percentage = $typenamepercentage->compatibility_percentage;


            $compatibilitycheck = array(
                'type' => $type, 'business_name' => $business_name, 'nametoname_percentage' => $cal_percentage, 'dobtodate_percentage' => $businessdob_percentage, 'final_compatibility_percentage' => $final_compatibility_percentage,
                'first_compatibility_processingbar' => $primary_nopercentage, 'second_compatibility_processingbar' => $name_percentage, 'third_compatibility_processingbar' => $typename_percentage
            );

            return response()->json([
                'status' => 1,
                'message' => 'success',
                'userid' => $userid,
                'subscription_status' => $loginuser->subscription_status,
                'compatibilitydetail' => $compatibilitycheck,
            ]);
        } elseif ($type == 4) // For Property
        {
            $validator = Validator::make($request->all(), [
                'userid' => 'required',
                'type' => 'required',
                'type_name' => 'required',
                'property_number' => 'required',
                'pin' => 'required',
                'city' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }
            $userid = $request->userid;
            $type_name = $request->type_name;
            $property_number = $request->property_number;
            $postalcode = $request->pin;
            $city = $request->city;

            $compatibility = User_compatiblecheck::create([

                'user_id' => $userid,
                'type' => $type,
                'type_name' => $type_name,
                'number' => $property_number,
                'postalcode' => $postalcode,
                'city' => $city,
            ]);

            //user dob reading
            $loginuser = User::find($userid);
            $dob = $loginuser->dob;
            $date = explode('-', $dob);
            $day = str_split($date[2], 1);
            $cal_dobno = array_sum($day);
            $cal_dobno = intval($cal_dobno);
            while (strlen($cal_dobno) != 1) {
                $cal_dobno = str_split($cal_dobno);
                $cal_dobno = array_sum($cal_dobno);
            }

            // property number reading

            $numaric = str_split(filter_var($property_number, FILTER_SANITIZE_NUMBER_INT));
            $propertynumber_sum = array_sum($numaric);
            while (strlen($propertynumber_sum) != 1) {
                $propertynumber_sum = str_split($propertynumber_sum);
                $propertynumber_sum = array_sum($propertynumber_sum);
            }

            $alphabet = implode("", preg_split("/\d+/", $property_number));
            $propertyno_alphabetno_sum = 0;
            if ($alphabet != null) {
                $propertyno_alphabets = str_split($alphabet);
                $alphabet_no = array();
                foreach ($propertyno_alphabets as $propertyno_alphabet) {
                    $propertyno_alphabetno = Alphasystem_type::where('alphabet', 'LIKE', '%' . $propertyno_alphabet . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                    array_push($alphabet_no, $propertyno_alphabetno);
                }

                $propertyno_alphabetno_sum = array_sum($alphabet_no);
                while (strlen($propertyno_alphabetno_sum) != 1) {
                    $propertyno_alphabetno_sum = str_split($propertyno_alphabetno_sum);
                    $propertyno_alphabetno_sum = array_sum($propertyno_alphabetno_sum);
                }
            }
            $final_propertyno = $propertynumber_sum + $propertyno_alphabetno_sum;
            while (strlen($final_propertyno) != 1) {
                $final_propertyno = str_split($final_propertyno);
                $final_propertyno = array_sum($final_propertyno);
            }

            //percentage Aco to dob and property number

            $dob_propertypercentage = Compatibility_percentage::where('number', $cal_dobno)->where('mate_number', $final_propertyno)->first();
            $propertydob_percentage = $dob_propertypercentage->compatibility_percentage;
            $remain_percentage = 100 - $propertydob_percentage;


            // case2 dob and pin reading

            //pin reading 
            $pin = str_split($postalcode, 1);
            $cal_pinno = array_sum($pin);
            $cal_pinno = intval($cal_pinno);
            while (strlen($cal_pinno) != 1) {
                $cal_pinno = str_split($cal_pinno);
                $cal_pinno = array_sum($cal_pinno);
            }

            //percentage of dob and property or dob and PIN percentage

            $dob_pin_percentage = Compatibility_percentage::where('number', $cal_dobno)->where('mate_number', $cal_pinno)->first();
            $dobpin_percentage = $dob_pin_percentage->compatibility_percentage;

            $dob_cal_percentage = ($remain_percentage / 100) * $dobpin_percentage;
            $case2_percentage = $propertydob_percentage + $dob_cal_percentage;
            $remainingcase2_percentage = 100 - $case2_percentage;


            //case3 name and city reading 

            //city name reading 
            $struppername = strtoupper($city);
            $names_array = explode(' ', $struppername);
            $cal_city_chaldno = array();
            foreach ($names_array as $citynamewords) {
                $wordletter = str_split($citynamewords);
                $wordchald_no = array();
                foreach ($wordletter as $wordletters) {
                    $word_chald_number = Alphasystem_type::where('alphabet', 'LIKE', '%' . $wordletters . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                    array_push($wordchald_no, $word_chald_number);
                }
                $city_chaldno = array_sum($wordchald_no);
                while (strlen($city_chaldno) != 1) {
                    $city_chaldno = str_split($city_chaldno, 1);
                    $city_chaldno = array_sum($city_chaldno);
                }
                array_push($cal_city_chaldno, $city_chaldno);
            }
            $city_chaldnumber = array_sum($cal_city_chaldno);
            while (strlen($city_chaldnumber) != 1) {
                $city_chaldnumber = str_split($city_chaldnumber);
                $city_chaldnumber = array_sum($city_chaldnumber);
            }

            // login user name reading
            $loginusername = $loginuser->name;
            $finalname = str_replace(' ', '', $loginusername);
            $loginuserstrname = strtoupper($finalname);
            $loginusersplitname = str_split($loginuserstrname, 1);
            foreach ($loginusersplitname as $nameletter) {
                $chald_no[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                    ->where('systemtype_id', 2)
                    ->value('number');
            }
            $loginuserchaldno_sum = array_sum($chald_no);
            while (strlen($loginuserchaldno_sum) != 1) {
                $loginuserchaldno_sum = str_split($loginuserchaldno_sum, 1);
                $loginuserchaldno_sum = array_sum($loginuserchaldno_sum);
            }

            $loginusernamereadingno = $loginuserchaldno_sum;

            //total percentage of compatibility
            $name_city_percentage = Compatibility_percentage::where('number', $loginusernamereadingno)->where('mate_number', $city_chaldnumber)->first();
            $name_city_percentage = $name_city_percentage->compatibility_percentage;

            $namecal_percentage = ($remainingcase2_percentage / 100) * $name_city_percentage;
            $final_compatibility_percentage = $case2_percentage + $namecal_percentage;

            $compatibilitycheck = array('type' => $type, 'property_number' => $property_number, 'dobtopropertyno_percentage' => $propertydob_percentage, 'dobtopin_percentage' => $case2_percentage, 'nametocity_percentage' => $namecal_percentage, 'final_compatibility_percentage' => $final_compatibility_percentage, 'first_compatibility_processingbar' => $propertydob_percentage, 'second_compatibility_processingbar' => $dobpin_percentage, 'third_compatibility_processingbar' => $name_city_percentage);

            return response()->json([
                'status' => 1,
                'message' => 'success',
                'userid' => $userid,
                'subscription_status' => $loginuser->subscription_status,
                'compatibilitydetail' => $compatibilitycheck,
            ]);
        } elseif ($type == 5) // For Profession
        {
            $validator = Validator::make($request->all(), [
                'userid' => 'required',
                'type' => 'required',
                'type_name' => 'required',
                'name' => 'required',
                'start_date' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }
            $userid = $request->userid;
            $type_name = $request->type_name;
            $profession_name = $request->name;
            $start_date = $request->start_date;
            $compatibility = User_compatiblecheck::create([

                'user_id' => $userid,
                'type' => $type,
                'type_name' => $type_name,
                'name' => $profession_name,
                'type_dates' => 2,
                'dates' => $start_date,
            ]);

            //case1 dob and start_date reading

            //user dob reading
            $loginuser = User::find($userid);
            $dob = $loginuser->dob;
            $date = explode('-', $dob);
            $day = str_split($date[2], 1);
            $cal_dobno = array_sum($day);
            $cal_dobno = intval($cal_dobno);
            while (strlen($cal_dobno) != 1) {
                $cal_dobno = str_split($cal_dobno);
                $cal_dobno = array_sum($cal_dobno);
            }

            //incorporation_date reading

            $start_date = explode('-', $start_date);
            $start_day = str_split($start_date[2], 1);
            $cal_startdobno = array_sum($start_day);
            $cal_startdobno = intval($cal_startdobno);
            while (strlen($cal_startdobno) != 1) {
                $cal_startdobno = str_split($cal_startdobno);
                $cal_startdobno = array_sum($cal_startdobno);
            }

            //percentage Aco to dob

            $dob_professionpercentage = Compatibility_percentage::where('number', $cal_dobno)->where('mate_number', $cal_startdobno)->first();
            $professiondob_percentage = $dob_professionpercentage->compatibility_percentage;
            $remain_percentage = 100 - $professiondob_percentage;


            //case2 name and profession name reading

            //profession name reading
            $struppername = strtoupper($profession_name);
            $names_array = explode(' ', $struppername);
            $cal_profession_chaldno = array();
            foreach ($names_array as $professionnamewords) {
                $wordletter = str_split($professionnamewords);
                $wordchald_no = array();
                foreach ($wordletter as $wordletters) {
                    $word_chald_number = Alphasystem_type::where('alphabet', 'LIKE', '%' . $wordletters . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                    array_push($wordchald_no, $word_chald_number);
                }
                $profession_chaldno = array_sum($wordchald_no);
                while (strlen($profession_chaldno) != 1) {
                    $profession_chaldno = str_split($profession_chaldno, 1);
                    $profession_chaldno = array_sum($profession_chaldno);
                }
                array_push($cal_profession_chaldno, $profession_chaldno);
            }
            $profession_chaldnumber = array_sum($cal_profession_chaldno);
            while (strlen($profession_chaldnumber) != 1) {
                $profession_chaldnumber = str_split($profession_chaldnumber);
                $profession_chaldnumber = array_sum($profession_chaldnumber);
            }

            //login user name reading

            $loginusername = $loginuser->name;
            $finalname = str_replace(' ', '', $loginusername);
            $loginuserstrname = strtoupper($finalname);
            $loginusersplitname = str_split($loginuserstrname, 1);
            foreach ($loginusersplitname as $nameletter) {
                $chald_no[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                    ->where('systemtype_id', 2)
                    ->value('number');
            }
            $loginuserchaldno_sum = array_sum($chald_no);
            while (strlen($loginuserchaldno_sum) != 1) {
                $loginuserchaldno_sum = str_split($loginuserchaldno_sum, 1);
                $loginuserchaldno_sum = array_sum($loginuserchaldno_sum);
            }

            $loginusernamereadingno = $loginuserchaldno_sum;

            //percentage login name

            $namepercentage = Compatibility_percentage::where('number', $loginusernamereadingno)->where('mate_number', $profession_chaldnumber)->first();
            $name_percentage = $namepercentage->compatibility_percentage;

            $cal_percentage = ($remain_percentage / 100) * $name_percentage;
            $cal_percentage = $professiondob_percentage + $cal_percentage;

            $compatibilitycheck = array('type' => $type, 'profession_name' => $profession_name, 'dobtostartdate' => $professiondob_percentage . '%', 'nametoprofessionname' => $professiondob_percentage . '%', 'compatibility_percentage' => $cal_percentage);

            return response()->json([
                'status' => 1,
                'message' => 'success',
                'userid' => $userid,
                'subscription_status' => $loginuser->subscription_status,
                'compatibilitydetail' => $compatibilitycheck,
            ]);
        } elseif ($type == 7) // For name compatibility
        {
            $validator = Validator::make($request->all(), [
                'userid' => 'required',
                'type' => 'required',
                'name' => 'required',
                'dob' => 'required',
                'gender' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }
            $userid = $request->userid;
            $otherperson_name = $request->name;
            $otherpersondob = $request->dob;
            $otherpersongender = $request->gender;

            $loginuser = User::find($userid);
            $otherpersondetail = User_compatiblecheck::create([

                'user_id' => $userid,
                'type' => $type,
                'type_name' => 'partner compatibility',
                'name' => $otherperson_name,
                'gender' => $otherpersongender,
                'type_dates' => 3,
                'dates' => $otherpersondob,
            ]);

            if ($otherpersondetail) {

                // other person name reading
                $partnerchaldno = array();
                $finalname = str_replace(' ', '', $otherperson_name);
                $otherpersonstrname = strtoupper($finalname);
                $otherpersonsplitname = str_split($otherpersonstrname, 1);
                foreach ($otherpersonsplitname as $nameletter) {
                    $partnerchald_no = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                    array_push($partnerchaldno, $partnerchald_no);
                }

                $otherpersonchaldno_sum = array_sum($partnerchaldno);
                while (strlen($otherpersonchaldno_sum) != 1) {
                    $otherpersonchaldno_sum = str_split($otherpersonchaldno_sum, 1);
                    $otherpersonchaldno_sum = array_sum($otherpersonchaldno_sum);
                }

                $otherpersonnamereadingno = $otherpersonchaldno_sum;
                $otherpersonnamereading =  Module_description::where('moduletype_id', 1)
                    ->where('number', $otherpersonnamereadingno)
                    ->value('description');

                $explodeothernamereading_desc = explode('||', $otherpersonnamereading);
                $positive_desc = $explodeothernamereading_desc[0];
                $negative_desc = $explodeothernamereading_desc[1];
                $otherpersonnamedesc = array("positive_title" => "Positive", "positive_desc" => $positive_desc, "negative_title" => "Negative", "negative_desc" => $negative_desc);



                //otherperson magicbox

                $chald_number_count = array_count_values($partnerchaldno);

                if (array_key_exists(1, $chald_number_count)) {
                    $magicboxnumber1 = $chald_number_count['1'];
                } else {
                    $magicboxnumber1 = 0;
                }
                $boxcellno1 = array('number' => 1, 'numbervalue' => $magicboxnumber1);

                if (array_key_exists(2, $chald_number_count)) {
                    $magicboxnumber2 = $chald_number_count['2'];
                } else {
                    $magicboxnumber2 = 0;
                }
                $boxcellno2 = array('number' => 2, 'numbervalue' => $magicboxnumber2);

                if (array_key_exists(3, $chald_number_count)) {
                    $magicboxnumber3 = $chald_number_count['3'];
                } else {
                    $magicboxnumber3 = 0;
                }
                $boxcellno3 = array('number' => 3, 'numbervalue' => $magicboxnumber3);

                if (array_key_exists(4, $chald_number_count)) {
                    $magicboxnumber4 = $chald_number_count['4'];
                } else {
                    $magicboxnumber4 = 0;
                }
                $boxcellno4 = array('number' => 4, 'numbervalue' => $magicboxnumber4);

                if (array_key_exists(5, $chald_number_count)) {
                    $magicboxnumber5 = $chald_number_count['5'];
                } else {
                    $magicboxnumber5 = 0;
                }
                $boxcellno5 = array('number' => 5, 'numbervalue' => $magicboxnumber5);


                if (array_key_exists(6, $chald_number_count)) {
                    $magicboxnumber6 = $chald_number_count['6'];
                } else {
                    $magicboxnumber6 = 0;
                }
                $boxcellno6 = array('number' => 6, 'numbervalue' => $magicboxnumber6);

                if (array_key_exists(7, $chald_number_count)) {
                    $magicboxnumber7 = $chald_number_count['7'];
                } else {
                    $magicboxnumber7 = 0;
                }
                $boxcellno7 = array('number' => 7, 'numbervalue' => $magicboxnumber7);

                if (array_key_exists(8, $chald_number_count)) {
                    $magicboxnumber8 = $chald_number_count['8'];
                } else {
                    $magicboxnumber8 = 0;
                }
                $boxcellno8 = array('number' => 8, 'numbervalue' => $magicboxnumber8);

                if (array_key_exists(9, $chald_number_count)) {
                    $magicboxnumber9 = $chald_number_count['9'];
                } else {
                    $magicboxnumber9 = 0;
                }
                $boxcellno9 = array('number' => 9, 'numbervalue' => $magicboxnumber9);

                $magicboxdetail = array($boxcellno1, $boxcellno2, $boxcellno3, $boxcellno4, $boxcellno5, $boxcellno6, $boxcellno7, $boxcellno8, $boxcellno9);

                // login user name reading 
                $loginuser_name = $loginuser->name;
                $finalname = str_replace(' ', '', $loginuser_name);
                $loginuserstrname = strtoupper($finalname);
                $loginusersplitname = str_split($loginuserstrname, 1);
                foreach ($loginusersplitname as $nameletter) {
                    $letters[] = $nameletter;
                    $chald_nos[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                }
                $loginuserchaldno_sum = array_sum($chald_nos);
                while (strlen($loginuserchaldno_sum) != 1) {
                    $loginuserchaldno_sum = str_split($loginuserchaldno_sum, 1);
                    $loginuserchaldno_sum = array_sum($loginuserchaldno_sum);
                }

                $loginusernamereadingno = $loginuserchaldno_sum;
                $name_compatibilitypercentage = Compatibility_percentage::where('number', $loginusernamereadingno)->where('mate_number', $otherpersonnamereadingno)->first();
                $namecompatibilitypercentage = $name_compatibilitypercentage->compatibility_percentage;


                return response()->json([
                    'status' => 1,
                    'message' => 'success',
                    'subscription_status' => $loginuser->subscription_status,
                    'name' => $otherperson_name,
                    'dob' => $otherpersondob,
                    'namecompatibilitypercentage' => $namecompatibilitypercentage,
                    'namedec' => $otherpersonnamedesc,
                    'magicboxdetail' => $magicboxdetail,
                ]);
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'Error',
                ]);
            }
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Error',
            ]);
        }
    }

    public function editanothernamecompatibilitycheck(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required',
            'name' => 'required',
            'check_date' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $userid = $request->userid;
        $anothername = $request->name;
        $check_date = $request->check_date;
        $loginuserdetail = User::find($userid);
        $currentDate = date('Y-m-d');
        $anothernameCompChecks = User_historyname::where('user_id', '=', $userid)->where('created_at', 'LIKE', '%' . $currentDate . '%')->get();

            $saveanothername = User_historyname::create([
                'user_id' => $userid,
                'name' => $anothername,
                'status' => 0,
                'check_date' => $check_date
            ]);


            if ($saveanothername) {
                $anothernamechaldno = array();
                $finalname = str_replace(' ', '', $anothername);
                $anothernamestrname = strtoupper($finalname);
                $anothernamesplitname = str_split($anothernamestrname, 1);
                foreach ($anothernamesplitname as $nameletter) {
                    $anothernamechald_no = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                    array_push($anothernamechaldno, $anothernamechald_no);
                }

                $anothernamechaldno_sum = array_sum($anothernamechaldno);
                while (strlen($anothernamechaldno_sum) != 1) {
                    $anothernamechaldno_sum = str_split($anothernamechaldno_sum, 1);
                    $anothernamechaldno_sum = array_sum($anothernamechaldno_sum);
                }

                $anothernamenamereadingno = $anothernamechaldno_sum;
                $anothernamenamereading =  Module_description::where('moduletype_id', 1)
                    ->where('number', $anothernamenamereadingno)
                    ->value('description');

                $anothernamedesc = strip_tags($anothernamenamereading);
                $explodeanothernamereading_desc = explode('||', $anothernamedesc);
                $anothernamepositive_desc = $explodeanothernamereading_desc[0];
                $anothernamenegative_desc = $explodeanothernamereading_desc[1];
                $anothernamenamedesc = array("positive_title" => "Positive", "positive_desc" => $anothernamepositive_desc, "negative_title" => "Negative", "negative_desc" => $anothernamenegative_desc);


                // login user name compatibility
                // name calculated number
                $anothername_name = $request->name;
                $finalname = str_replace(' ', '', $anothername_name);
                $anothernamestrname = strtoupper($finalname);
                $anothernamesplitname = str_split($anothernamestrname, 1);
                foreach ($anothernamesplitname as $nameletter) {
                    $chald_nos[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                }
                $anothernamechaldno_sum = array_sum($chald_nos);
                while (strlen($anothernamechaldno_sum) != 1) {
                    $anothernamechaldno_sum = str_split($anothernamechaldno_sum, 1);
                    $anothernamechaldno_sum = array_sum($anothernamechaldno_sum);
                }

                //dob calculated number
                $loginuser_dob = $loginuserdetail->dob;
                $explodedate = explode('-', $loginuser_dob);
                $split_day = str_split($explodedate[2], 1);
                $dobno = array_sum($split_day);
                $dobno = intval($dobno);
                while (strlen($dobno) != 1) {
                    $dobno = str_split($dobno);
                    $dobno = array_sum($dobno);
                }

                $anothernamenamereadingno = $anothernamechaldno_sum;
                $name_compatibilitypercentage = Compatibility_percentage::where('number', $anothernamenamereadingno)->where('mate_number', $dobno)->first();
                $namecompatibilitypercentage = $name_compatibilitypercentage->compatibility_percentage;


                //dob calculated number
                    $loginuser_dob = $loginuserdetail->dob;
                    $explodedate = explode('-', $loginuser_dob);
                    $dob_month = $explodedate[1];
                    $dob_day = $explodedate[2];
                    $split_day = str_split($explodedate[2], 1);
                    $dobno = array_sum($split_day);
                    $dobno = intval($dobno);
                    while (strlen($dobno) != 1) {
                        $dobno = str_split($dobno);
                        $dobno = array_sum($dobno);
                    }

                    $dobdestiny_no = array_sum($explodedate);
                    while(strlen($dobdestiny_no) != 1)
                    {
                        $split_dobdestiny_no = str_split($dobdestiny_no);
                        $sum_dobdestiny_no = array_sum($split_dobdestiny_no);
                        $dobdestiny_no = $sum_dobdestiny_no;
                    }
                    
                        $dobfav = Fav_unfav_parameter::where('type', 1)
                            ->where('month_id', $dob_month)
                            ->where('date', $dob_day)
                            ->value('numbers');
                        $dobunfav = Fav_unfav_parameter::where('type', 2)
                            ->where('month_id', $dob_month)
                            ->where('date', $dob_day)
                            ->value('numbers');

                        $fav_numbers = str_replace(' ', '', $dobfav);
                        $favnumber_array = explode(',', $fav_numbers);
                        $fav_arraycount = count($favnumber_array);
        
                        $unfav_numbers = str_replace(' ', '', $dobunfav);
                        $unfavnumber_array = explode(',', $unfav_numbers);
                        $array_reverse_unfavnumber = array_reverse($unfavnumber_array);
                        $unfav_arraycount = count($unfavnumber_array);

                        $usernamefinal_perc = 0;
                        if ($dobno == $anothernamenamereadingno) {
                            $dobpercentage = 98;
                            $usernamefinal_perc = 98;
                        } else {
                            $dobpercentage = 0;
                        }
                        if ($dobdestiny_no == $anothernamenamereadingno) {
                            $destinypercentage = 84;
                            $usernamefinal_perc = 84;
                        } else {
                            $destinypercentage = 0;
                        }
                        if ($fav_arraycount == 3) {
                            if ($favnumber_array[1] == $anothernamenamereadingno) {
                                $favpercentage = 66;
                                $usernamefinal_perc = 66;
                            } elseif ($favnumber_array[2] == $anothernamenamereadingno) {
                                $favpercentage = 55;
                                $usernamefinal_perc = 55;
                            } else {
                                $favpercentage = 0;
                            }
                        } elseif ($fav_arraycount == 4) {
                            if ($favnumber_array[1] == $anothernamenamereadingno) {
                                $favpercentage = 74;
                                $usernamefinal_perc = 74;
                            } elseif ($favnumber_array[2] == $anothernamenamereadingno) {
                                $favpercentage = 65;
                                $usernamefinal_perc = 65;
                            } elseif ($favnumber_array[3] == $anothernamenamereadingno) {
                                $favpercentage = 55;
                                $usernamefinal_perc = 55;
                            } else {
                                $favpercentage = 0;
                            }
                        } else {
                            return response()->json([
                                'status' => 0,
                                'message' => 'fav number error'
                            ]);
                        }
                        if ($unfav_arraycount == 2) {
                            if ($array_reverse_unfavnumber[0] == $anothernamenamereadingno) {
                                $unfavpercentage = 30;
                                $usernamefinal_perc = 30;
                            } elseif ($array_reverse_unfavnumber[1] == $anothernamenamereadingno) {
                                $unfavpercentage = 15;
                                $usernamefinal_perc = 15;
                            } else {
                                $unfavpercentage = 0;
                            }
                        } elseif ($unfav_arraycount == 3) {
                            if ($array_reverse_unfavnumber[0] == $anothernamenamereadingno) {
                                $unfavpercentage = 35;
                                $usernamefinal_perc = 35;
                            } elseif ($array_reverse_unfavnumber[1] == $anothernamenamereadingno) {
                                $unfavpercentage = 23;
                                $usernamefinal_perc = 23;
                            } elseif ($array_reverse_unfavnumber[2] == $anothernamenamereadingno) {
                                $unfavpercentage = 12;
                                $usernamefinal_perc = 12;
                            } else {
                                $unfavpercentage = 0;
                            }
                        } else {
                            return response()->json([
                                'status' => 0,
                                'message' => 'unfav number error'
                            ]);
                        }
                        if ($dobpercentage == 0 && $destinypercentage == 0 && $favpercentage == 0 && $unfavpercentage == 0) {
                            $usernamefinal_perc = 50;
                        }


                    $namecompatibilitypercentage = $usernamefinal_perc;

                //anothername magicbox

                $chald_number_count = array_count_values($anothernamechaldno);

                if (array_key_exists(1, $chald_number_count)) {
                    $magicboxnumber1 = $chald_number_count['1'];
                } else {
                    $magicboxnumber1 = 0;
                }
                $magicboxnumber1_decs = Module_description::where('moduletype_id', 3)
                    ->where('number', 1)
                    ->first();

                $magicboxnumber1decs = explode('||', strip_tags($magicboxnumber1_decs->description));
                $box1decs = $magicboxnumber1decs[0];
                $box1manydecs = $magicboxnumber1decs[1];
                $box1fewdecs = $magicboxnumber1decs[2];
                if ($magicboxnumber1_decs->mogicbox_average <= $magicboxnumber1) {
                    $title = 'Many 1s';
                    $description = $box1manydecs;
                } else {
                    $title = 'Few/No 1s';
                    $description = $box1fewdecs;
                }
                $magicboxnumberdecs1 = array('Box' => $box1decs, 'title' => $title, 'description' => $description, 'average_desc' => "Three 1s are average.");

                $boxcellno1 = array('number_heading' => 'number_heading', 'number' => 1, 'numbervalue' => $magicboxnumber1, 'numberdescription' => $magicboxnumberdecs1);

                if (array_key_exists(2, $chald_number_count)) {
                    $magicboxnumber2 = $chald_number_count['2'];
                } else {
                    $magicboxnumber2 = 0;
                }
                $magicboxnumber2_decs = Module_description::where('moduletype_id', 3)
                    ->where('number', 2)
                    ->first();

                $magicboxnumber2decs = explode('||', strip_tags($magicboxnumber2_decs->description));
                $box2decs = $magicboxnumber2decs[0];
                $box2manydecs = $magicboxnumber2decs[1];
                $box2fewdecs = $magicboxnumber2decs[2];
                if ($magicboxnumber2_decs->mogicbox_average <= $magicboxnumber2) {
                    $title = 'Many 2s';
                    $description = $box2manydecs;
                } else {
                    $title = 'Few/No 2s';
                    $description = $box2fewdecs;
                }
                $magicboxnumberdecs2 = array('Box' => $box2decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 2 is average.");

                $boxcellno2 = array('number_heading' => 'number_heading', 'number' => 2, 'numbervalue' => $magicboxnumber2, 'numberdescription' => $magicboxnumberdecs2);

                if (array_key_exists(3, $chald_number_count)) {
                    $magicboxnumber3 = $chald_number_count['3'];
                } else {
                    $magicboxnumber3 = 0;
                }
                $magicboxnumber3_decs = Module_description::where('moduletype_id', 3)
                    ->where('number', 3)
                    ->first();

                $magicboxnumber3decs = explode('||', strip_tags($magicboxnumber3_decs->description));
                $box3decs = $magicboxnumber3decs[0];
                $box3manydecs = $magicboxnumber3decs[1];
                $box3fewdecs = $magicboxnumber3decs[2];
                if ($magicboxnumber3_decs->mogicbox_average <= $magicboxnumber3) {
                    $title = 'Many 3s';
                    $description = $box3manydecs;
                } else {
                    $title = 'Few/No 3s';
                    $description = $box3fewdecs;
                }
                $magicboxnumberdecs3 = array('Box' => $box3decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 3 is average.");

                $boxcellno3 = array('number_heading' => 'number_heading', 'number' => 3, 'numbervalue' => $magicboxnumber3, 'numberdescription' => $magicboxnumberdecs3);

                if (array_key_exists(4, $chald_number_count)) {
                    $magicboxnumber4 = $chald_number_count['4'];
                } else {
                    $magicboxnumber4 = 0;
                }
                $magicboxnumber4_decs = Module_description::where('moduletype_id', 3)
                    ->where('number', 4)
                    ->first();

                $magicboxnumber4decs = explode('||', strip_tags($magicboxnumber4_decs->description));
                $box4decs = $magicboxnumber4decs[0];
                $box4manydecs = $magicboxnumber4decs[1];
                $box4fewdecs = $magicboxnumber4decs[2];
                if ($magicboxnumber4_decs->mogicbox_average <= $magicboxnumber4) {
                    $title = 'Many 4s';
                    $description = $box4manydecs;
                } else {
                    $title = 'Few/No 4s';
                    $description = $box4fewdecs;
                }

                $magicboxnumberdecs4 = array('Box' => $box4decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 4 is average.");

                $boxcellno4 = array('number_heading' => 'number_heading', 'number' => 4, 'numbervalue' => $magicboxnumber4, 'numberdescription' => $magicboxnumberdecs4);

                if (array_key_exists(5, $chald_number_count)) {
                    $magicboxnumber5 = $chald_number_count['5'];
                } else {
                    $magicboxnumber5 = 0;
                }
                $magicboxnumber5_decs = Module_description::where('moduletype_id', 3)
                    ->where('number', 5)
                    ->first();

                $magicboxnumber5decs = explode('||', strip_tags($magicboxnumber5_decs->description));
                $box5decs = $magicboxnumber5decs[0];
                $box5manydecs = $magicboxnumber5decs[1];
                $box5fewdecs = $magicboxnumber5decs[2];
                if ($magicboxnumber5_decs->mogicbox_average <= $magicboxnumber5) {
                    $title = 'Many 5s';
                    $description = $box5manydecs;
                } else {
                    $title = 'Few/No 5s';
                    $description = $box5fewdecs;
                }

                $magicboxnumberdecs5 = array('Box' => $box5decs, 'title' => $title, 'description' => $description, 'average_desc' => "Three 5s are average.");

                $boxcellno5 = array('number_heading' => 'number_heading', 'number' => 5, 'numbervalue' => $magicboxnumber5, 'numberdescription' => $magicboxnumberdecs5);


                if (array_key_exists(6, $chald_number_count)) {
                    $magicboxnumber6 = $chald_number_count['6'];
                } else {
                    $magicboxnumber6 = 0;
                }
                $magicboxnumber6_decs = Module_description::where('moduletype_id', 3)
                    ->where('number', 6)
                    ->first();

                $magicboxnumber6decs = explode('||', strip_tags($magicboxnumber6_decs->description));
                $box6decs = $magicboxnumber6decs[0];
                $box6manydecs = $magicboxnumber6decs[1];
                $box6fewdecs = $magicboxnumber6decs[2];
                if ($magicboxnumber6_decs->mogicbox_average <= $magicboxnumber6) {
                    $title = 'Many 6s';
                    $description = $box6manydecs;
                } else {
                    $title = 'Few/No 6s';
                    $description = $box6fewdecs;
                }

                $magicboxnumberdecs6 = array('Box' => $box6decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 6 is average.");

                $boxcellno6 = array('number_heading' => 'number_heading', 'number' => 6, 'numbervalue' => $magicboxnumber6, 'numberdescription' => $magicboxnumberdecs6);

                if (array_key_exists(7, $chald_number_count)) {
                    $magicboxnumber7 = $chald_number_count['7'];
                } else {
                    $magicboxnumber7 = 0;
                }

                $magicboxnumber7_decs = Module_description::where('moduletype_id', 3)
                    ->where('number', 7)
                    ->first();

                $magicboxnumber7decs = explode('||', strip_tags($magicboxnumber7_decs->description));
                $box7decs = $magicboxnumber7decs[0];
                $box7manydecs = $magicboxnumber7decs[1];
                $box7fewdecs = $magicboxnumber7decs[2];
                if ($magicboxnumber7_decs->mogicbox_average <= $magicboxnumber7) {
                    $title = 'Many 7s';
                    $description = $box7manydecs;
                } else {
                    $title = 'Few/No 7s';
                    $description = $box7fewdecs;
                }

                $magicboxnumberdecs7 = array('Box' => $box7decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 7 is average.");

                $boxcellno7 = array('number_heading' => 'number_heading', 'number' => 7, 'numbervalue' => $magicboxnumber7, 'numberdescription' => $magicboxnumberdecs7);

                if (array_key_exists(8, $chald_number_count)) {
                    $magicboxnumber8 = $chald_number_count['8'];
                } else {
                    $magicboxnumber8 = 0;
                }
                $magicboxnumber8_decs = Module_description::where('moduletype_id', 3)
                    ->where('number', 8)
                    ->first();

                $magicboxnumber8decs = explode('||', strip_tags($magicboxnumber8_decs->description));
                $box8decs = $magicboxnumber8decs[0];
                $box8manydecs = $magicboxnumber8decs[1];
                $box8fewdecs = $magicboxnumber8decs[2];
                if ($magicboxnumber8_decs->mogicbox_average <= $magicboxnumber8) {
                    $title = 'Many 8s';
                    $description = $box8manydecs;
                } else {
                    $title = 'Few/No 8s';
                    $description = $box8fewdecs;
                }

                $magicboxnumberdecs8 = array('Box' => $box8decs, 'title' => $title, 'description' => $description, 'average_desc' => "Three 8s are average.");

                $boxcellno8 = array('number_heading' => 'number_heading', 'number' => 8, 'numbervalue' => $magicboxnumber8, 'numberdescription' => $magicboxnumberdecs8);

                if (array_key_exists(9, $chald_number_count)) {
                    $magicboxnumber9 = $chald_number_count['9'];
                } else {
                    $magicboxnumber9 = 0;
                }
                $magicboxnumber9_decs = Module_description::where('moduletype_id', 3)
                    ->where('number', 9)
                    ->first();
                $magicboxnumber9decs = explode('||', strip_tags($magicboxnumber9_decs->description));
                $box9decs = $magicboxnumber9decs[0];
                $box9manydecs = $magicboxnumber9decs[1];
                $box9fewdecs = $magicboxnumber9decs[2];
                if ($magicboxnumber9_decs->mogicbox_average <= $magicboxnumber9) {
                    $title = 'Many 9s';
                    $description = $box9manydecs;
                } else {
                    $title = 'Few/No 9s';
                    $description = $box9fewdecs;
                }

                $magicboxnumberdecs9 = array('Box' => $box9decs, 'title' => $title, 'description' => $description, 'average_desc' => "Three 9s are average.");
                $boxcellno9 = array('number_heading' => 'number_heading', 'number' => 9, 'numbervalue' => $magicboxnumber9, 'numberdescription' => $magicboxnumberdecs9);
                $magicboxdetail = array($boxcellno1, $boxcellno2, $boxcellno3, $boxcellno4, $boxcellno5, $boxcellno6, $boxcellno7, $boxcellno8, $boxcellno9);

                return response()->json([
                    'status' => 1,
                    'message' => 'success',
                    'userid' => $userid,
                    'subscription_status' => $loginuserdetail->subscription_status,
                    'name' => $anothername,
                    'namecompatibilitypercentage' => $namecompatibilitypercentage,
                    'anothernamenamedesc' => $anothernamenamedesc,
                    'magicboxdetail' => $magicboxdetail,
                ]);
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'Record not found. Please try again !'
                ]);
            }
    }

    // Possession Data API
    public function userPossesionData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $userid = $request->userid;
        $user = User::find($userid);
        if ($user) {
            $user_dob = $user->dob;
            $date = explode('-', $user_dob);
            $day = str_split($date[2], 1);
            $number = array_sum($day);
            $number = intval($number);
            while (strlen($number) != 1) {
                $number = str_split($number);
                $number = array_sum($number);
            }
            $luckyparameters = Luckiest_parameter::select('lucky_colours', 'lucky_gems', 'lucky_metals')
                ->where('number', $number)
                ->first();

            $houseorproperty = Possesion::where('type', 1)->where('number', '=', $number)->value('description');
            $houseorpropertydesc = strip_tags($houseorproperty);
            $carorvehicle = Possesion::where('type', 2)->where('number', '=', $number)->value('description');
            $carorvehicledesc = strip_tags($carorvehicle);
            $gemsandstones = Possesion::where('type', 3)->value('description');
            $gemsandstonesdesc = strip_tags($gemsandstones);
            $metalsorcolors = Possesion::where('type', 4)->value('description');
            $metalsorcolorsdesc = strip_tags($metalsorcolors);

            $gems = array("title" => "Gems", "description" => $luckyparameters->lucky_gems . " are Lucky gems for you");
            $metals = array("title" => "Metals", "description" => "Your metal is " . $luckyparameters->lucky_metals);
            $colours = array("title" => "Colors", "description" => $luckyparameters->lucky_colours . " colours are Lucky for you");
            if ($luckyparameters) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Success',
                    'subscription_status' => $user->subscription_status,
                    'houseorpropertydesc' => $houseorpropertydesc,
                    'carorvehicledesc' => $carorvehicledesc,
                    'gemsandstonesdesc' => $gemsandstonesdesc,
                    'metalsorcolorsdesc' => $metalsorcolorsdesc,
                    'gems' => $gems,
                    'metals' => $metals,
                    'colours' => $colours,
                ]);
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'Something went wrong. Please try again.',
                ]);
            }
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'User does not exist',
            ]);
        }
    }

    public function uploadprofilepic(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required',
            'profile_pic' => 'required|mimes:png,jpg,jpeg,gif',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $userid = $request->userid;
        /* $profile_pic = $request->file('profile_pic');
        $upload = $profile_pic->store('profile_pic','public');
	    $new_profilepic_name = $profile_pic->hashName();
		 */
        if ($file = $request->file('profile_pic')) {
            //$extension = $file->extension()?: 'png';
            $destinationPath = public_path() . '/profile_pic';
            $safeName = \Str::random(12) . time() . '.' . $file->getClientOriginalExtension();
            $file->move($destinationPath, $safeName);
            $new_profilepic_name = $safeName;
        }

        $save_profilepic = User::find($userid);
        $save_profilepic->profile_pic = $new_profilepic_name;
        $save_profilepic->save();

        if ($save_profilepic) {
            return response()->json([
                'status' => 1,
                'message' => 'Profile image is uploaded successfully.',
                'subscription_status' => $save_profilepic->subscription_status,
                'profile_pic' => 'https://be.astar8.com/profile_pic/' . $new_profilepic_name,
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong. Please try again'
            ]);
        }
    }

    public function namereadingcompatibilitycheck(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $userid = $request->userid;
        $loginuser = User::find($userid);

        if ($loginuser) {
            // login user name reading
            $loginuserchaldno = array();
            $finalname = str_replace(' ', '', $loginuser->name);
            $loginuserstrname = strtoupper($finalname);
            $loginusersplitname = str_split($loginuserstrname, 1);
            foreach ($loginusersplitname as $nameletter) {
                $loginuserchald_no = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                    ->where('systemtype_id', 2)
                    ->value('number');
                array_push($loginuserchaldno, $loginuserchald_no);
            }
            $loginuserchaldno_sum = array_sum($loginuserchaldno);
            while (strlen($loginuserchaldno_sum) != 1) {
                $loginuserchaldno_sum = str_split($loginuserchaldno_sum, 1);
                $loginuserchaldno_sum = array_sum($loginuserchaldno_sum);
            }

            $loginusernamereadingno = $loginuserchaldno_sum;
            $loginusernamereading =  Module_description::where('moduletype_id', 1)
                ->where('number', $loginusernamereadingno)
                ->value('description');

            $loginuser_namedesc = strip_tags($loginusernamereading);
            $explodenamereading_desc = explode('||', $loginuser_namedesc);
            $positive_desc = $explodenamereading_desc[0];
            $negative_desc = $explodenamereading_desc[1];
            $loginusernamedesc = array("positive_title" => "Positive", "positive_desc" => $positive_desc, "negative_title" => "Negative", "negative_desc" => $negative_desc);
            
            // login user name compatibility
            $loginuser_name = $loginuser->name;
            $finalname = str_replace(' ', '', $loginuser_name);
            $loginuserstrname = strtoupper($finalname);
            $loginusersplitname = str_split($loginuserstrname, 1);
            foreach ($loginusersplitname as $nameletter) {
                $chald_nos[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                    ->where('systemtype_id', 2)
                    ->value('number');
            }
            $loginuserchaldno_sum = array_sum($chald_nos);
            while (strlen($loginuserchaldno_sum) != 1) {
                $loginuserchaldno_sum = str_split($loginuserchaldno_sum, 1);
                $loginuserchaldno_sum = array_sum($loginuserchaldno_sum);
            }
            $loginusernamereadingno = $loginuserchaldno_sum;

            //dob calculated number
            $loginuser_dob = $loginuser->dob;
            $explodedate = explode('-', $loginuser_dob);
            $dob_month = $explodedate[1];
            $dob_day = $explodedate[2];
            $split_day = str_split($explodedate[2], 1);
            $dobno = array_sum($split_day);
            $dobno = intval($dobno);
            while (strlen($dobno) != 1) {
                $dobno = str_split($dobno);
                $dobno = array_sum($dobno);
            }

            $dobdestiny_no = array_sum($explodedate);
            while(strlen($dobdestiny_no) != 1)
            {
                $split_dobdestiny_no = str_split($dobdestiny_no);
                $sum_dobdestiny_no = array_sum($split_dobdestiny_no);
                $dobdestiny_no = $sum_dobdestiny_no;
            }
            
                $dobfav = Fav_unfav_parameter::where('type', 1)
                    ->where('month_id', $dob_month)
                    ->where('date', $dob_day)
                    ->value('numbers');
                $dobunfav = Fav_unfav_parameter::where('type', 2)
                    ->where('month_id', $dob_month)
                    ->where('date', $dob_day)
                    ->value('numbers');

                $fav_numbers = str_replace(' ', '', $dobfav);
                $favnumber_array = explode(',', $fav_numbers);
                $fav_arraycount = count($favnumber_array);

                $unfav_numbers = str_replace(' ', '', $dobunfav);
                $unfavnumber_array = explode(',', $unfav_numbers);
                $array_reverse_unfavnumber = array_reverse($unfavnumber_array);
                $unfav_arraycount = count($unfavnumber_array);
                $usernamefinal_perc = 0;
                if ($dobno == $loginusernamereadingno) {
                    $dobpercentage = 98;
                    $usernamefinal_perc = 98;
                } else {
                    $dobpercentage = 0;
                }
                if ($dobdestiny_no == $loginusernamereadingno) {
                    $destinypercentage = 84;
                    $usernamefinal_perc = 84;
                } else {
                    $destinypercentage = 0;
                }
                if ($fav_arraycount == 3) {
                    if ($favnumber_array[1] == $loginusernamereadingno) {
                        $favpercentage = 66;
                        $usernamefinal_perc = 66;
                    } elseif ($favnumber_array[2] == $loginusernamereadingno) {
                        $favpercentage = 55;
                        $usernamefinal_perc = 55;
                    } else {
                        $favpercentage = 0;
                    }
                } elseif ($fav_arraycount == 4) {
                    if ($favnumber_array[1] == $loginusernamereadingno) {
                        $favpercentage = 74;
                        $usernamefinal_perc = 74;
                    } elseif ($favnumber_array[2] == $loginusernamereadingno) {
                        $favpercentage = 65;
                        $usernamefinal_perc = 65;
                    } elseif ($favnumber_array[3] == $loginusernamereadingno) {
                        $favpercentage = 55;
                        $usernamefinal_perc = 55;
                    } else {
                        $favpercentage = 0;
                    }
                } else {
                    return response()->json([
                        'status' => 0,
                        'message' => 'fav number error'
                    ]);
                }
                if ($unfav_arraycount == 2) {
                    if ($array_reverse_unfavnumber[0] == $loginusernamereadingno) {
                        $unfavpercentage = 30;
                        $usernamefinal_perc = 30;
                    } elseif ($array_reverse_unfavnumber[1] == $loginusernamereadingno) {
                        $unfavpercentage = 15;
                        $usernamefinal_perc = 15;
                    } else {
                        $unfavpercentage = 0;
                    }
                } elseif ($unfav_arraycount == 3) {
                    if ($array_reverse_unfavnumber[0] == $loginusernamereadingno) {
                        $unfavpercentage = 35;
                        $usernamefinal_perc = 35;
                    } elseif ($array_reverse_unfavnumber[1] == $loginusernamereadingno) {
                        $unfavpercentage = 23;
                        $usernamefinal_perc = 23;
                    } elseif ($array_reverse_unfavnumber[2] == $loginusernamereadingno) {
                        $unfavpercentage = 12;
                        $usernamefinal_perc = 12;
                    } else {
                        $unfavpercentage = 0;
                    }
                } else {
                    return response()->json([
                        'status' => 0,
                        'message' => 'unfav number error'
                    ]);
                }
                if ($dobpercentage == 0 && $destinypercentage == 0 && $favpercentage == 0 && $unfavpercentage == 0) {
                    $usernamefinal_perc = 50;
                }
            $namecompatibilitypercentage = $usernamefinal_perc;

            //loginuser magicbox
            $chald_number_count = array_count_values($loginuserchaldno);

            if (array_key_exists(1, $chald_number_count)) {
                $magicboxnumber1 = $chald_number_count['1'];
            } else {
                $magicboxnumber1 = 0;
            }
            $magicboxnumber1_decs = Module_description::where('moduletype_id', 3)
                ->where('number', 1)
                ->first();

            $magicboxnumber1decs = explode('||', strip_tags($magicboxnumber1_decs->description));
            $box1decs = $magicboxnumber1decs[0];
            $box1manydecs = $magicboxnumber1decs[1];
            $box1fewdecs = $magicboxnumber1decs[2];
            if ($magicboxnumber1_decs->mogicbox_average <= $magicboxnumber1) {
                $title = 'Many 1s';
                $description = $box1manydecs;
            } else {
                $title = 'Few/No 1s';
                $description = $box1fewdecs;
            }
            $magicboxnumberdecs1 = array('Box' => $box1decs, 'title' => $title, 'description' => $description, 'average_desc' => "Three 1s are average.");

            $boxcellno1 = array('number_heading' => 'number_heading', 'number' => 1, 'numbervalue' => $magicboxnumber1, 'numberdescription' => $magicboxnumberdecs1);

            if (array_key_exists(2, $chald_number_count)) {
                $magicboxnumber2 = $chald_number_count['2'];
            } else {
                $magicboxnumber2 = 0;
            }
            $magicboxnumber2_decs = Module_description::where('moduletype_id', 3)
                ->where('number', 2)
                ->first();

            $magicboxnumber2decs = explode('||', strip_tags($magicboxnumber2_decs->description));
            $box2decs = $magicboxnumber2decs[0];
            $box2manydecs = $magicboxnumber2decs[1];
            $box2fewdecs = $magicboxnumber2decs[2];
            if ($magicboxnumber2_decs->mogicbox_average <= $magicboxnumber2) {
                $title = 'Many 2s';
                $description = $box2manydecs;
            } else {
                $title = 'Few/No 2s';
                $description = $box2fewdecs;
            }
            $magicboxnumberdecs2 = array('Box' => $box2decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 2 is average.");

            $boxcellno2 = array('number_heading' => 'number_heading', 'number' => 2, 'numbervalue' => $magicboxnumber2, 'numberdescription' => $magicboxnumberdecs2);

            if (array_key_exists(3, $chald_number_count)) {
                $magicboxnumber3 = $chald_number_count['3'];
            } else {
                $magicboxnumber3 = 0;
            }
            $magicboxnumber3_decs = Module_description::where('moduletype_id', 3)
                ->where('number', 3)
                ->first();

            $magicboxnumber3decs = explode('||', strip_tags($magicboxnumber3_decs->description));
            $box3decs = $magicboxnumber3decs[0];
            $box3manydecs = $magicboxnumber3decs[1];
            $box3fewdecs = $magicboxnumber3decs[2];
            if ($magicboxnumber3_decs->mogicbox_average <= $magicboxnumber3) {
                $title = 'Many 3s';
                $description = $box3manydecs;
            } else {
                $title = 'Few/No 3s';
                $description = $box3fewdecs;
            }
            $magicboxnumberdecs3 = array('Box' => $box3decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 3 is average.");

            $boxcellno3 = array('number_heading' => 'number_heading', 'number' => 3, 'numbervalue' => $magicboxnumber3, 'numberdescription' => $magicboxnumberdecs3);

            if (array_key_exists(4, $chald_number_count)) {
                $magicboxnumber4 = $chald_number_count['4'];
            } else {
                $magicboxnumber4 = 0;
            }
            $magicboxnumber4_decs = Module_description::where('moduletype_id', 3)
                ->where('number', 4)
                ->first();

            $magicboxnumber4decs = explode('||', strip_tags($magicboxnumber4_decs->description));
            $box4decs = $magicboxnumber4decs[0];
            $box4manydecs = $magicboxnumber4decs[1];
            $box4fewdecs = $magicboxnumber4decs[2];
            if ($magicboxnumber4_decs->mogicbox_average <= $magicboxnumber4) {
                $title = 'Many 4s';
                $description = $box4manydecs;
            } else {
                $title = 'Few/No 4s';
                $description = $box4fewdecs;
            }
            $magicboxnumberdecs4 = array('Box' => $box4decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 4 is average.");
            $boxcellno4 = array('number_heading' => 'number_heading', 'number' => 4, 'numbervalue' => $magicboxnumber4, 'numberdescription' => $magicboxnumberdecs4);

            if (array_key_exists(5, $chald_number_count)) {
                $magicboxnumber5 = $chald_number_count['5'];
            } else {
                $magicboxnumber5 = 0;
            }
            $magicboxnumber5_decs = Module_description::where('moduletype_id', 3)
                ->where('number', 5)
                ->first();

            $magicboxnumber5decs = explode('||', strip_tags($magicboxnumber5_decs->description));
            $box5decs = $magicboxnumber5decs[0];
            $box5manydecs = $magicboxnumber5decs[1];
            $box5fewdecs = $magicboxnumber5decs[2];
            if ($magicboxnumber5_decs->mogicbox_average <= $magicboxnumber5) {
                $title = 'Many 5s';
                $description = $box5manydecs;
            } else {
                $title = 'Few/No 5s';
                $description = $box5fewdecs;
            }

            $magicboxnumberdecs5 = array('Box' => $box5decs, 'title' => $title, 'description' => $description, 'average_desc' => "Three 5s are average.");

            $boxcellno5 = array('number_heading' => 'number_heading', 'number' => 5, 'numbervalue' => $magicboxnumber5, 'numberdescription' => $magicboxnumberdecs5);

            if (array_key_exists(6, $chald_number_count)) {
                $magicboxnumber6 = $chald_number_count['6'];
            } else {
                $magicboxnumber6 = 0;
            }
            $magicboxnumber6_decs = Module_description::where('moduletype_id', 3)
                ->where('number', 6)
                ->first();

            $magicboxnumber6decs = explode('||', strip_tags($magicboxnumber6_decs->description));
            $box6decs = $magicboxnumber6decs[0];
            $box6manydecs = $magicboxnumber6decs[1];
            $box6fewdecs = $magicboxnumber6decs[2];
            if ($magicboxnumber6_decs->mogicbox_average <= $magicboxnumber6) {
                $title = 'Many 6s';
                $description = $box6manydecs;
            } else {
                $title = 'Few/No 6s';
                $description = $box6fewdecs;
            }
            $magicboxnumberdecs6 = array('Box' => $box6decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 6 is average.");
            $boxcellno6 = array('number_heading' => 'number_heading', 'number' => 6, 'numbervalue' => $magicboxnumber6, 'numberdescription' => $magicboxnumberdecs6);

            if (array_key_exists(7, $chald_number_count)) {
                $magicboxnumber7 = $chald_number_count['7'];
            } else {
                $magicboxnumber7 = 0;
            }

            $magicboxnumber7_decs = Module_description::where('moduletype_id', 3)
                ->where('number', 7)
                ->first();

            $magicboxnumber7decs = explode('||', strip_tags($magicboxnumber7_decs->description));
            $box7decs = $magicboxnumber7decs[0];
            $box7manydecs = $magicboxnumber7decs[1];
            $box7fewdecs = $magicboxnumber7decs[2];
            if ($magicboxnumber7_decs->mogicbox_average <= $magicboxnumber7) {
                $title = 'Many 7s';
                $description = $box7manydecs;
            } else {
                $title = 'Few/No 7s';
                $description = $box7fewdecs;
            }

            $magicboxnumberdecs7 = array('Box' => $box7decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 7 is average.");

            $boxcellno7 = array('number_heading' => 'number_heading', 'number' => 7, 'numbervalue' => $magicboxnumber7, 'numberdescription' => $magicboxnumberdecs7);

            if (array_key_exists(8, $chald_number_count)) {
                $magicboxnumber8 = $chald_number_count['8'];
            } else {
                $magicboxnumber8 = 0;
            }
            $magicboxnumber8_decs = Module_description::where('moduletype_id', 3)
                ->where('number', 8)
                ->first();

            $magicboxnumber8decs = explode('||', strip_tags($magicboxnumber8_decs->description));
            $box8decs = $magicboxnumber8decs[0];
            $box8manydecs = $magicboxnumber8decs[1];
            $box8fewdecs = $magicboxnumber8decs[2];
            if ($magicboxnumber8_decs->mogicbox_average <= $magicboxnumber8) {
                $title = 'Many 8s';
                $description = $box8manydecs;
            } else {
                $title = 'Few/No 8s';
                $description = $box8fewdecs;
            }

            $magicboxnumberdecs8 = array('Box' => $box8decs, 'title' => $title, 'description' => $description, 'average_desc' => "Three 8s are average.");
            $boxcellno8 = array('number_heading' => 'number_heading', 'number' => 8, 'numbervalue' => $magicboxnumber8, 'numberdescription' => $magicboxnumberdecs8);

            if (array_key_exists(9, $chald_number_count)) {
                $magicboxnumber9 = $chald_number_count['9'];
            } else {
                $magicboxnumber9 = 0;
            }
            $magicboxnumber9_decs = Module_description::where('moduletype_id', 3)
                ->where('number', 9)
                ->first();
            $magicboxnumber9decs = explode('||', strip_tags($magicboxnumber9_decs->description));
            $box9decs = $magicboxnumber9decs[0];
            $box9manydecs = $magicboxnumber9decs[1];
            $box9fewdecs = $magicboxnumber9decs[2];
            if ($magicboxnumber9_decs->mogicbox_average <= $magicboxnumber9) {
                $title = 'Many 9s';
                $description = $box9manydecs;
            } else {
                $title = 'Few/No 9s';
                $description = $box9fewdecs;
            }

            $magicboxnumberdecs9 = array('Box' => $box9decs, 'title' => $title, 'description' => $description, 'average_desc' => "Three 9s are average.");
            $boxcellno9 = array('number_heading' => 'number_heading', 'number' => 9, 'numbervalue' => $magicboxnumber9, 'numberdescription' => $magicboxnumberdecs9);
            $magicboxdetail = array($boxcellno1, $boxcellno2, $boxcellno3, $boxcellno4, $boxcellno5, $boxcellno6, $boxcellno7, $boxcellno8, $boxcellno9);

            return response()->json([
                'status' => 1,
                'message' => 'success',
                'loginuser_id' => $loginuser->id,
                'subscription_status' => $loginuser->subscription_status,
                'name' => $loginuser->name,
                'dob' => $loginuser->dob,
                'namecompatibilitypercentage' => $namecompatibilitypercentage,
                'namedec' => $loginusernamedesc,
                'magicboxdetail' => $magicboxdetail,
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Error',
            ]);
        }
    }

    public function updateanothernameverification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required',
            'status' => 'required',
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $userid = $request->userid;
        $status = $request->status;
        $name = $request->name;
        $loginUser = User::find($userid);
            $namedetail = User_historyname::where('user_id', $userid)->where('name', $name)->orderBy('id', 'DESC')->latest()->first();
            if ($namedetail) {
                if ($status == 1) {
                    $saveoldname = User_historyname::find($namedetail->id);
                    $saveoldname->old_name = $loginUser->name;
                    $saveoldname->status = 1;
                    $saveoldname->save();

                    $updatename = User::find($userid);
                    $updatename->name = $name;
                    $updatename->save();

                    // login user name compatibility
                    $loginuser_name = $updatename->name;
                    $finalname = str_replace(' ', '', $loginuser_name);
                    $loginuserstrname = strtoupper($finalname);
                    $loginusersplitname = str_split($loginuserstrname, 1);
                    foreach ($loginusersplitname as $nameletter) {
                        $pytha_nos[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                            ->where('systemtype_id', 1)
                            ->value('number');
                        $chald_nos[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                            ->where('systemtype_id', 2)
                            ->value('number');
                    }
                    $loginuser_Pythano_sum = array_sum($pytha_nos);
                    $loginuserchaldno_sum = array_sum($chald_nos);
                    while (strlen($loginuserchaldno_sum) != 1 && strlen($loginuser_Pythano_sum) != 1) {
                        $splitloginuserPythanosum = str_split($loginuser_Pythano_sum, 1);
                        $splitloginuserPythano_sum = array_sum($splitloginuserPythanosum);
                        $loginuser_Pythano_sum = $splitloginuserPythano_sum;
                        $split_loginuserchaldno_sum = str_split($loginuserchaldno_sum, 1);
                        $splitloginuserchaldno_sum = array_sum($split_loginuserchaldno_sum);
                        $loginuserchaldno_sum = $splitloginuserchaldno_sum;
                    }
                    
                    $loginusernamereadingno = $loginuserchaldno_sum;

                    $pytha_description = Module_description::where('moduletype_id', 1)
                        ->where('number', $loginuser_Pythano_sum)
                        ->value('description');
                    $pytha_description = strip_tags($pytha_description);
                    $chald_description = Module_description::where('moduletype_id', 1)
                        ->where('number', $loginuserchaldno_sum)
                        ->value('description');
                    $explodenamereading_desc = explode('||', $chald_description);
                    $positive_desc = $explodenamereading_desc[0];
                    $negative_desc = $explodenamereading_desc[1];

                    $namereadingdetail = array(
                        "module_name" => "Name Reading", "Pytha_number" => $loginuser_Pythano_sum, "Pytha_description" => $pytha_description, "Chald_number" => $loginuserchaldno_sum, "Chald_description" => $chald_description,
                        "Chald_positive_title" => "Positive", "Chald_positive_desc" => $positive_desc, "Chald_negative_title" => "Negative", "Chald_negative_desc" => $negative_desc
                    );

                    // magic box
                    $chald_number_count = array_count_values($chald_nos);
                    if (array_key_exists(1, $chald_number_count)) {
                        $magicboxnumber1 = $chald_number_count['1'];
                    } else {
                        $magicboxnumber1 = 0;
                    }
                    $magicboxnumber1_decs = Module_description::where('moduletype_id', 3)
                        ->where('number', 1)
                        ->first();
    
                    $magicboxnumber1decs = explode('||', strip_tags($magicboxnumber1_decs->description));
                    $box1decs = $magicboxnumber1decs[0];
                    $box1manydecs = $magicboxnumber1decs[1];
                    $box1fewdecs = $magicboxnumber1decs[2];
                    if ($magicboxnumber1_decs->mogicbox_average <= $magicboxnumber1) {
                        $title = 'Many 1s';
                        $description = $box1manydecs;
                    } else {
                        $title = 'Few/No 1s';
                        $description = $box1fewdecs;
                    }
                    $magicboxnumberdecs1 = array('Box' => $box1decs, 'title' => $title, 'description' => $description, 'average_desc' => "Three 1s are average.");
    
                    $boxcellno1 = array('number_heading' => 'number_heading', 'number' => 1, 'numbervalue' => $magicboxnumber1, 'numberdescription' => $magicboxnumberdecs1);
    
                    if (array_key_exists(2, $chald_number_count)) {
                        $magicboxnumber2 = $chald_number_count['2'];
                    } else {
                        $magicboxnumber2 = 0;
                    }
                    $magicboxnumber2_decs = Module_description::where('moduletype_id', 3)
                        ->where('number', 2)
                        ->first();
    
                    $magicboxnumber2decs = explode('||', strip_tags($magicboxnumber2_decs->description));
                    $box2decs = $magicboxnumber2decs[0];
                    $box2manydecs = $magicboxnumber2decs[1];
                    $box2fewdecs = $magicboxnumber2decs[2];
                    if ($magicboxnumber2_decs->mogicbox_average <= $magicboxnumber2) {
                        $title = 'Many 2s';
                        $description = $box2manydecs;
                    } else {
                        $title = 'Few/No 2s';
                        $description = $box2fewdecs;
                    }
                    $magicboxnumberdecs2 = array('Box' => $box2decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 2 is average.");
    
                    $boxcellno2 = array('number_heading' => 'number_heading', 'number' => 2, 'numbervalue' => $magicboxnumber2, 'numberdescription' => $magicboxnumberdecs2);
    
                    if (array_key_exists(3, $chald_number_count)) {
                        $magicboxnumber3 = $chald_number_count['3'];
                    } else {
                        $magicboxnumber3 = 0;
                    }
                    $magicboxnumber3_decs = Module_description::where('moduletype_id', 3)
                        ->where('number', 3)
                        ->first();
    
                    $magicboxnumber3decs = explode('||', strip_tags($magicboxnumber3_decs->description));
                    $box3decs = $magicboxnumber3decs[0];
                    $box3manydecs = $magicboxnumber3decs[1];
                    $box3fewdecs = $magicboxnumber3decs[2];
                    if ($magicboxnumber3_decs->mogicbox_average <= $magicboxnumber3) {
                        $title = 'Many 3s';
                        $description = $box3manydecs;
                    } else {
                        $title = 'Few/No 3s';
                        $description = $box3fewdecs;
                    }
                    $magicboxnumberdecs3 = array('Box' => $box3decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 3 is average.");
    
                    $boxcellno3 = array('number_heading' => 'number_heading', 'number' => 3, 'numbervalue' => $magicboxnumber3, 'numberdescription' => $magicboxnumberdecs3);
    
                    if (array_key_exists(4, $chald_number_count)) {
                        $magicboxnumber4 = $chald_number_count['4'];
                    } else {
                        $magicboxnumber4 = 0;
                    }
                    $magicboxnumber4_decs = Module_description::where('moduletype_id', 3)
                        ->where('number', 4)
                        ->first();
    
                    $magicboxnumber4decs = explode('||', strip_tags($magicboxnumber4_decs->description));
                    $box4decs = $magicboxnumber4decs[0];
                    $box4manydecs = $magicboxnumber4decs[1];
                    $box4fewdecs = $magicboxnumber4decs[2];
                    if ($magicboxnumber4_decs->mogicbox_average <= $magicboxnumber4) {
                        $title = 'Many 4s';
                        $description = $box4manydecs;
                    } else {
                        $title = 'Few/No 4s';
                        $description = $box4fewdecs;
                    }
    
                    $magicboxnumberdecs4 = array('Box' => $box4decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 4 is average.");
    
                    $boxcellno4 = array('number_heading' => 'number_heading', 'number' => 4, 'numbervalue' => $magicboxnumber4, 'numberdescription' => $magicboxnumberdecs4);
    
                    if (array_key_exists(5, $chald_number_count)) {
                        $magicboxnumber5 = $chald_number_count['5'];
                    } else {
                        $magicboxnumber5 = 0;
                    }
                    $magicboxnumber5_decs = Module_description::where('moduletype_id', 3)
                        ->where('number', 5)
                        ->first();
    
                    $magicboxnumber5decs = explode('||', strip_tags($magicboxnumber5_decs->description));
                    $box5decs = $magicboxnumber5decs[0];
                    $box5manydecs = $magicboxnumber5decs[1];
                    $box5fewdecs = $magicboxnumber5decs[2];
                    if ($magicboxnumber5_decs->mogicbox_average <= $magicboxnumber5) {
                        $title = 'Many 5s';
                        $description = $box5manydecs;
                    } else {
                        $title = 'Few/No 5s';
                        $description = $box5fewdecs;
                    }
    
                    $magicboxnumberdecs5 = array('Box' => $box5decs, 'title' => $title, 'description' => $description, 'average_desc' => "Three 5s are average.");
    
                    $boxcellno5 = array('number_heading' => 'number_heading', 'number' => 5, 'numbervalue' => $magicboxnumber5, 'numberdescription' => $magicboxnumberdecs5);
    
    
                    if (array_key_exists(6, $chald_number_count)) {
                        $magicboxnumber6 = $chald_number_count['6'];
                    } else {
                        $magicboxnumber6 = 0;
                    }
                    $magicboxnumber6_decs = Module_description::where('moduletype_id', 3)
                        ->where('number', 6)
                        ->first();
    
                    $magicboxnumber6decs = explode('||', strip_tags($magicboxnumber6_decs->description));
                    $box6decs = $magicboxnumber6decs[0];
                    $box6manydecs = $magicboxnumber6decs[1];
                    $box6fewdecs = $magicboxnumber6decs[2];
                    if ($magicboxnumber6_decs->mogicbox_average <= $magicboxnumber6) {
                        $title = 'Many 6s';
                        $description = $box6manydecs;
                    } else {
                        $title = 'Few/No 6s';
                        $description = $box6fewdecs;
                    }
    
                    $magicboxnumberdecs6 = array('Box' => $box6decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 6 is average.");
    
                    $boxcellno6 = array('number_heading' => 'number_heading', 'number' => 6, 'numbervalue' => $magicboxnumber6, 'numberdescription' => $magicboxnumberdecs6);
    
                    if (array_key_exists(7, $chald_number_count)) {
                        $magicboxnumber7 = $chald_number_count['7'];
                    } else {
                        $magicboxnumber7 = 0;
                    }
    
                    $magicboxnumber7_decs = Module_description::where('moduletype_id', 3)
                        ->where('number', 7)
                        ->first();
    
                    $magicboxnumber7decs = explode('||', strip_tags($magicboxnumber7_decs->description));
                    $box7decs = $magicboxnumber7decs[0];
                    $box7manydecs = $magicboxnumber7decs[1];
                    $box7fewdecs = $magicboxnumber7decs[2];
                    if ($magicboxnumber7_decs->mogicbox_average <= $magicboxnumber7) {
                        $title = 'Many 7s';
                        $description = $box7manydecs;
                    } else {
                        $title = 'Few/No 7s';
                        $description = $box7fewdecs;
                    }
    
                    $magicboxnumberdecs7 = array('Box' => $box7decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 7 is average.");
    
                    $boxcellno7 = array('number_heading' => 'number_heading', 'number' => 7, 'numbervalue' => $magicboxnumber7, 'numberdescription' => $magicboxnumberdecs7);
    
                    if (array_key_exists(8, $chald_number_count)) {
                        $magicboxnumber8 = $chald_number_count['8'];
                    } else {
                        $magicboxnumber8 = 0;
                    }
                    $magicboxnumber8_decs = Module_description::where('moduletype_id', 3)
                        ->where('number', 8)
                        ->first();
    
                    $magicboxnumber8decs = explode('||', strip_tags($magicboxnumber8_decs->description));
                    $box8decs = $magicboxnumber8decs[0];
                    $box8manydecs = $magicboxnumber8decs[1];
                    $box8fewdecs = $magicboxnumber8decs[2];
                    if ($magicboxnumber8_decs->mogicbox_average <= $magicboxnumber8) {
                        $title = 'Many 8s';
                        $description = $box8manydecs;
                    } else {
                        $title = 'Few/No 8s';
                        $description = $box8fewdecs;
                    }
    
                    $magicboxnumberdecs8 = array('Box' => $box8decs, 'title' => $title, 'description' => $description, 'average_desc' => "Three 8s are average.");
    
                    $boxcellno8 = array('number_heading' => 'number_heading', 'number' => 8, 'numbervalue' => $magicboxnumber8, 'numberdescription' => $magicboxnumberdecs8);
    
                    if (array_key_exists(9, $chald_number_count)) {
                        $magicboxnumber9 = $chald_number_count['9'];
                    } else {
                        $magicboxnumber9 = 0;
                    }
                    $magicboxnumber9_decs = Module_description::where('moduletype_id', 3)
                        ->where('number', 9)
                        ->first();
                    $magicboxnumber9decs = explode('||', strip_tags($magicboxnumber9_decs->description));
                    $box9decs = $magicboxnumber9decs[0];
                    $box9manydecs = $magicboxnumber9decs[1];
                    $box9fewdecs = $magicboxnumber9decs[2];
                    if ($magicboxnumber9_decs->mogicbox_average <= $magicboxnumber9) {
                        $title = 'Many 9s';
                        $description = $box9manydecs;
                    } else {
                        $title = 'Few/No 9s';
                        $description = $box9fewdecs;
                    }
    
                    $magicboxnumberdecs9 = array('Box' => $box9decs, 'title' => $title, 'description' => $description, 'average_desc' => "Three 9s are average.");
                    $boxcellno9 = array('number_heading' => 'number_heading', 'number' => 9, 'numbervalue' => $magicboxnumber9, 'numberdescription' => $magicboxnumberdecs9);
    
                    $magicboxdetail = array($boxcellno1, $boxcellno2, $boxcellno3, $boxcellno4, $boxcellno5, $boxcellno6, $boxcellno7, $boxcellno8, $boxcellno9);

                    //magicboxdesclist
                    $magicboxdesclist = array('number1description' => $magicboxnumberdecs1, 'number2description' => $magicboxnumberdecs2, 'number3description' => $magicboxnumberdecs3, 'number4description' => $magicboxnumberdecs4, 'number5description' => $magicboxnumberdecs5, 'number6description' => $magicboxnumberdecs6, 'number7description' => $magicboxnumberdecs7, 'number8description' => $magicboxnumberdecs8, 'number9description' => $magicboxnumberdecs9);
    
                    //dob calculated number
                    $loginuser_dob = $updatename->dob;
                    $explodedate = explode('-', $loginuser_dob);
                    $dob_month = $explodedate[1];
                    $dob_day = $explodedate[2];
                    $split_day = str_split($explodedate[2], 1);
                    $dobno = array_sum($split_day);
                    $dobno = intval($dobno);
                    while (strlen($dobno) != 1) {
                        $dobno = str_split($dobno);
                        $dobno = array_sum($dobno);
                    }

                    $dobdestiny_no = array_sum($explodedate);
                    while(strlen($dobdestiny_no) != 1)
                    {
                        $split_dobdestiny_no = str_split($dobdestiny_no);
                        $sum_dobdestiny_no = array_sum($split_dobdestiny_no);
                        $dobdestiny_no = $sum_dobdestiny_no;
                    }
                    
                        $dobfav = Fav_unfav_parameter::where('type', 1)
                            ->where('month_id', $dob_month)
                            ->where('date', $dob_day)
                            ->value('numbers');
                        $dobunfav = Fav_unfav_parameter::where('type', 2)
                            ->where('month_id', $dob_month)
                            ->where('date', $dob_day)
                            ->value('numbers');

                        $fav_numbers = str_replace(' ', '', $dobfav);
                        $favnumber_array = explode(',', $fav_numbers);
                        $fav_arraycount = count($favnumber_array);
        
                        $unfav_numbers = str_replace(' ', '', $dobunfav);
                        $unfavnumber_array = explode(',', $unfav_numbers);
                        $array_reverse_unfavnumber = array_reverse($unfavnumber_array);
                        $unfav_arraycount = count($unfavnumber_array);

                        $usernamefinal_perc = 0;
                        if ($dobno == $loginusernamereadingno) {
                            $dobpercentage = 98;
                            $usernamefinal_perc = 98;
                        } else {
                            $dobpercentage = 0;
                        }
                        if ($dobdestiny_no == $loginusernamereadingno) {
                            $destinypercentage = 84;
                            $usernamefinal_perc = 84;
                        } else {
                            $destinypercentage = 0;
                        }
                        if ($fav_arraycount == 3) {
                            if ($favnumber_array[1] == $loginusernamereadingno) {
                                $favpercentage = 66;
                                $usernamefinal_perc = 66;
                            } elseif ($favnumber_array[2] == $loginusernamereadingno) {
                                $favpercentage = 55;
                                $usernamefinal_perc = 55;
                            } else {
                                $favpercentage = 0;
                            }
                        } elseif ($fav_arraycount == 4) {
                            if ($favnumber_array[1] == $loginusernamereadingno) {
                                $favpercentage = 74;
                                $usernamefinal_perc = 74;
                            } elseif ($favnumber_array[2] == $loginusernamereadingno) {
                                $favpercentage = 65;
                                $usernamefinal_perc = 65;
                            } elseif ($favnumber_array[3] == $loginusernamereadingno) {
                                $favpercentage = 55;
                                $usernamefinal_perc = 55;
                            } else {
                                $favpercentage = 0;
                            }
                        } else {
                            return response()->json([
                                'status' => 0,
                                'message' => 'fav number error'
                            ]);
                        }
                        if ($unfav_arraycount == 2) {
                            if ($array_reverse_unfavnumber[0] == $loginusernamereadingno) {
                                $unfavpercentage = 30;
                                $usernamefinal_perc = 30;
                            } elseif ($array_reverse_unfavnumber[1] == $loginusernamereadingno) {
                                $unfavpercentage = 15;
                                $usernamefinal_perc = 15;
                            } else {
                                $unfavpercentage = 0;
                            }
                        } elseif ($unfav_arraycount == 3) {
                            if ($array_reverse_unfavnumber[0] == $loginusernamereadingno) {
                                $unfavpercentage = 35;
                                $usernamefinal_perc = 35;
                            } elseif ($array_reverse_unfavnumber[1] == $loginusernamereadingno) {
                                $unfavpercentage = 23;
                                $usernamefinal_perc = 23;
                            } elseif ($array_reverse_unfavnumber[2] == $loginusernamereadingno) {
                                $unfavpercentage = 12;
                                $usernamefinal_perc = 12;
                            } else {
                                $unfavpercentage = 0;
                            }
                        } else {
                            return response()->json([
                                'status' => 0,
                                'message' => 'unfav number error'
                            ]);
                        }
                        if ($dobpercentage == 0 && $destinypercentage == 0 && $favpercentage == 0 && $unfavpercentage == 0) {
                            $usernamefinal_perc = 50;
                        }


                    $namecompatibilitypercentage = $usernamefinal_perc;

                    return response()->json([
                        'status' => 1,
                        'message' => 'Name changed successfully',
                        'subscription_status' => $loginUser->subscription_status,
                        'update_name' => $name,
                        'namecompatibilitypercentage' => $namecompatibilitypercentage,
                        'namereadingdetail' => $namereadingdetail,
                        'magicboxdetail' => $magicboxdetail,
                    ]);
                } else {
                    return response()->json([
                        'status' => 0,
                        'message' => 'Error'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'Error'
                ]);
            }
    }

    public function compatibilitycheckhistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $userid = $request->userid;
        $loginuser = User::find($userid);
        if ($loginuser) {
            $carhistories = User_compatiblecheck::where('user_id', $userid)->where('type', 1)->get();
            $carshistory = array();
            $carhistorydata = array();
            foreach ($carhistories as $carhistory) {
                $carcheckdate = explode(' ', $carhistory->created_at);
                $cardescription = Possesion::where('type', 2)->value('description');
                $carnamedesc =  strip_tags($cardescription);


                $dob = $loginuser->dob;
                $date = explode('-', $dob);
                $day = str_split($date[2], 1);
                $cal_dobno = array_sum($day);
                $cal_dobno = intval($cal_dobno);
                while (strlen($cal_dobno) != 1) {
                    $cal_dobno = str_split($cal_dobno);
                    $cal_dobno = array_sum($cal_dobno);
                }

                //car registration_no reading

                $numaric = str_split(filter_var($carhistory->number, FILTER_SANITIZE_NUMBER_INT));
                $carnumber_sum = array_sum($numaric);
                while (strlen($carnumber_sum) != 1) {
                    $carnumber_sum = str_split($carnumber_sum);
                    $carnumber_sum = array_sum($carnumber_sum);
                }

                $alphabet = implode("", preg_split("/\d+/", $carhistory->number));
                $carno_alphabets = str_split($alphabet);
                $alphabet_no = array();
                foreach ($carno_alphabets as $carno_alphabet) {
                    $carno_alphabetno = Alphasystem_type::where('alphabet', 'LIKE', '%' . $carno_alphabet . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                    array_push($alphabet_no, $carno_alphabetno);
                }
                $carno_alphabetno_sum = array_sum($alphabet_no);
                while (strlen($carno_alphabetno_sum) != 1) {
                    $carno_alphabetno_sum = str_split($carno_alphabetno_sum);
                    $carno_alphabetno_sum = array_sum($carno_alphabetno_sum);
                }
                $final_carno = $carnumber_sum + $carno_alphabetno_sum;
                while (strlen($final_carno) != 1) {
                    $final_carno = str_split($final_carno);
                    $final_carno = array_sum($final_carno);
                }

                //percentage Aco to dob

                $dob_carpercentage = Compatibility_percentage::where('number', $cal_dobno)->where('mate_number', $final_carno)->first();
                $cardob_percentage = $dob_carpercentage->compatibility_percentage;
                $remain_percentage = 100 - $cardob_percentage;

                //case2 name and car name reading

                //car name reading
                $struppername = strtoupper($carhistory->name);
                $names_array = explode(' ', $struppername);
                $cal_car_chaldno = array();
                foreach ($names_array as $carnamewords) {
                    $wordletter = str_split($carnamewords);
                    $wordchald_no = array();
                    foreach ($wordletter as $wordletters) {
                        $word_chald_number = Alphasystem_type::where('alphabet', 'LIKE', '%' . $wordletters . '%')
                            ->where('systemtype_id', 2)
                            ->value('number');
                        array_push($wordchald_no, $word_chald_number);
                    }
                    $car_chaldno = array_sum($wordchald_no);
                    while (strlen($car_chaldno) != 1) {
                        $car_chaldno = str_split($car_chaldno, 1);
                        $car_chaldno = array_sum($car_chaldno);
                    }
                    array_push($cal_car_chaldno, $car_chaldno);
                }
                $car_chaldnumber = array_sum($cal_car_chaldno);
                while (strlen($car_chaldnumber) != 1) {
                    $car_chaldnumber = str_split($car_chaldnumber);
                    $car_chaldnumber = array_sum($car_chaldnumber);
                }

                //login user name reading

                $loginusername = $loginuser->name;
                $finalname = str_replace(' ', '', $loginusername);
                $loginuserstrname = strtoupper($finalname);
                $loginusersplitname = str_split($loginuserstrname, 1);
                foreach ($loginusersplitname as $nameletter) {
                    $chald_no[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                }
                $loginuserchaldno_sum = array_sum($chald_no);
                while (strlen($loginuserchaldno_sum) != 1) {
                    $loginuserchaldno_sum = str_split($loginuserchaldno_sum, 1);
                    $loginuserchaldno_sum = array_sum($loginuserchaldno_sum);
                }

                $loginusernamereadingno = $loginuserchaldno_sum;

                //percentage login name

                $namepercentage = Compatibility_percentage::where('number', $loginusernamereadingno)->where('mate_number', $car_chaldnumber)->first();
                $name_percentage = $namepercentage->compatibility_percentage;

                $cal_percentage = ($remain_percentage / 100) * $name_percentage;
                $car_finalcompatibility_percentage = $cardob_percentage + $cal_percentage;

                $carhistorydata['module_name'] = 'car/vehicle';
                $carhistorydata['name'] = $carhistory->name;
                $carhistorydata['description'] = $carnamedesc;
                $carhistorydata['check_date'] = $carcheckdate[0];
                $carhistorydata['percentage'] = $car_finalcompatibility_percentage;

                array_push($carshistory, $carhistorydata);
            }

            $onetootherhistories = User_compatiblecheck::where('user_id', $userid)->where('type', 2)->get();
            $historyonetoother = array();
            $onetootherhistorydata = array();
            foreach ($onetootherhistories as $onetootherhistory) {
                $onetoothercheckdate = explode(' ', $onetootherhistory->created_at);

                $loginuser_dob = $loginuser->dob;
                $loginuserdob = explode("-", $loginuser_dob);
                $otherpersondob_date = explode('-', $onetootherhistory->dates);

                //Name reading
                $otherpersionname = $onetootherhistory->name;
                $finalname = str_replace(' ', '', $otherpersionname);
                $strotherpersonname = strtoupper($finalname);
                $splitotherpersonname = str_split($strotherpersonname, 1);
                $alphabet_number = array();
                foreach ($splitotherpersonname as $otherpersonnameletter) {
                    $namechald_number = Alphasystem_type::where('alphabet', 'LIKE', '%' . $otherpersonnameletter . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                    array_push($alphabet_number, $namechald_number);
                }

                $namechald_no_sum = array_sum($alphabet_number);

                while (strlen($namechald_no_sum) != 1) {
                    $namechald_no_sum = str_split($namechald_no_sum, 1);
                    $namechald_no_sum = array_sum($namechald_no_sum);
                }

                $otherpersonnamereadingno = $namechald_no_sum;

                //partner relationship
                $loginuaerdobday = str_split($loginuserdob[2], 1);
                $loginuserdob_number = array_sum($loginuaerdobday);
                while (strlen($loginuserdob_number) != 1) {
                    $loginuserdob_number = str_split($loginuserdob_number);
                    $loginuserdob_number = array_sum($loginuserdob_number);
                }

                $otherpersion_day = str_split($otherpersondob_date[2], 1);
                $otherpersion_number = array_sum($otherpersion_day);
                $otherpersion_number = intval($otherpersion_number);
                while (strlen($otherpersion_number) != 1) {
                    $otherpersion_number = str_split($otherpersion_number);
                    $otherpersion_number = array_sum($otherpersion_number);
                }

                $relationdesc = Partner_relationship::select('description')->where('number', $loginuserdob_number)
                    ->where('mate_number', $otherpersion_number)
                    ->first();


                // login user detail

                $loginuser_name = $loginuser->name;
                $finalname = str_replace(' ', '', $loginuser_name);
                $loginuserstrname = strtoupper($finalname);
                $loginusersplitname = str_split($loginuserstrname, 1);
                foreach ($loginusersplitname as $nameletter) {
                    $chald_no[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                }

                $loginuserchaldno_sum = array_sum($chald_no);

                while (strlen($loginuserchaldno_sum) != 1) {
                    $loginuserchaldno_sum = str_split($loginuserchaldno_sum, 1);
                    $loginuserchaldno_sum = array_sum($loginuserchaldno_sum);
                }

                $loginusernamereadingno = $loginuserchaldno_sum;

                //relation percentage 

                $relation_dobpercentage = Compatibility_percentage::where('number', $loginuserdob_number)->where('mate_number', $otherpersion_number)->first();
                $ontootherdob_percentage = $relation_dobpercentage->compatibility_percentage;
                $remain_percentage = 100 - $ontootherdob_percentage;

                $relation_namepercentage = Compatibility_percentage::where('number', $loginusernamereadingno)->where('mate_number', $otherpersonnamereadingno)->first();
                $name_percentage = $relation_namepercentage->compatibility_percentage;

                $calculated_name_percentage = ($remain_percentage / 100) * $name_percentage;
                $otherpersionfinal_percentage = $ontootherdob_percentage + $calculated_name_percentage;


                $onetootherhistorydata['module_name'] = 'other person';
                $onetootherhistorydata['name'] = $onetootherhistory->name;
                $onetootherhistorydata['description'] = $relationdesc;
                $onetootherhistorydata['check_date'] = $onetoothercheckdate[0];
                $onetootherhistorydata['percentage'] = $otherpersionfinal_percentage;

                array_push($historyonetoother, $onetootherhistorydata);
            }

            $busniesshistories = User_compatiblecheck::where('user_id', $userid)->where('type', 3)->get();
            $historybusniess = array();
            $busniesshistorydata = array();

            foreach ($busniesshistories as $busniesshistory) {
                $busniesscheckdate = explode(' ', $busniesshistory->created_at);
                $businessdescription = 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available.';

                //user dob reading
                $d_o_b = $loginuser->dob;
                $explodedob = explode('-', $d_o_b);
                $strday = str_split($explodedob[2], 1);
                $caldob_no = array_sum($strday);
                $caldobno = intval($caldob_no);
                while (strlen($caldobno) != 1) {
                    $caldobno = str_split($caldobno);
                    $caldobno = array_sum($caldobno);
                }

                //incorporation_date reading

                $inc_date = explode('-', $busniesshistory->dates);
                $inc_day = str_split($inc_date[2], 1);
                $cal_incdobno = array_sum($inc_day);
                $cal_incdobno = intval($cal_incdobno);
                while (strlen($cal_incdobno) != 1) {
                    $cal_incdobno = str_split($cal_incdobno);
                    $cal_incdobno = array_sum($cal_incdobno);
                }

                //percentage Aco to dob

                $dob_businesspercentage = Compatibility_percentage::where('number', $caldobno)->where('mate_number', $cal_incdobno)->first();
                $businessdob_percentage = $dob_businesspercentage->compatibility_percentage;
                $remain_percentage = 100 - $businessdob_percentage;


                //case2 name and business name reading

                //business name reading
                $struppername = strtoupper($busniesshistory->name);
                $names_array = explode(' ', $struppername);
                $cal_business_chaldno = array();
                foreach ($names_array as $businessnamewords) {
                    $businessnamewordletter = str_split($businessnamewords);
                    $word_chald_no = array();
                    foreach ($businessnamewordletter as $businessnamewordletters) {
                        $wordchald_number = Alphasystem_type::where('alphabet', 'LIKE', '%' . $businessnamewordletters . '%')
                            ->where('systemtype_id', 2)
                            ->value('number');
                        array_push($word_chald_no, $wordchald_number);
                    }
                    $businesschald_no = array_sum($word_chald_no);
                    while (strlen($businesschald_no) != 1) {
                        $businesschald_no = str_split($businesschald_no, 1);
                        $businesschald_no = array_sum($businesschald_no);
                    }
                    array_push($cal_business_chaldno, $businesschald_no);
                }
                $business_chaldnumber = array_sum($cal_business_chaldno);
                while (strlen($business_chaldnumber) != 1) {
                    $business_chaldnumber = str_split($business_chaldnumber);
                    $business_chaldnumber = array_sum($business_chaldnumber);
                }

                //login user name reading

                $loginusername = $loginuser->name;
                $finalname = str_replace(' ', '', $loginusername);
                $loginuserstrname = strtoupper($finalname);
                $loginusersplitname = str_split($loginuserstrname, 1);
                foreach ($loginusersplitname as $nameletter) {
                    $chald_no[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                }
                $loginuserchaldno_sum = array_sum($chald_no);
                while (strlen($loginuserchaldno_sum) != 1) {
                    $loginuserchaldno_sum = str_split($loginuserchaldno_sum, 1);
                    $loginuserchaldno_sum = array_sum($loginuserchaldno_sum);
                }

                $loginusernamereadingno = $loginuserchaldno_sum;

                //percentage login name

                $namepercentage = Compatibility_percentage::where('number', $loginusernamereadingno)->where('mate_number', $business_chaldnumber)->first();
                $name_percentage = $namepercentage->compatibility_percentage;

                $cal_percentage = ($remain_percentage / 100) * $name_percentage;
                $finalbusinesscompatibility_per = $businessdob_percentage + $cal_percentage;

                $busniesshistorydata['module_name'] = 'business';
                $busniesshistorydata['name'] = $busniesshistory->name;
                $busniesshistorydata['description'] = $businessdescription;
                $busniesshistorydata['check_date'] = $busniesscheckdate[0];
                $busniesshistorydata['percentage'] = $finalbusinesscompatibility_per;
                array_push($historybusniess, $busniesshistorydata);
            }

            $propertyhistories = User_compatiblecheck::where('user_id', $userid)->where('type', 4)->get();
            $historyproperty = array();
            $propertyhistorydata = array();

            foreach ($propertyhistories as $propertyhistory) {
                $propertycheckdate = explode(' ', $propertyhistory->created_at);

                $propertydescription = Possesion::where('type', 1)->value('description');
                $propertynamedesc =  strip_tags($propertydescription);

                $dateof_birth = $loginuser->dob;
                $explode_dob = explode('-', $dateof_birth);
                $dobday = str_split($explode_dob[2], 1);
                $cal_dob_n = array_sum($dobday);
                $cal_dobn = intval($cal_dob_n);
                while (strlen($cal_dobn) != 1) {
                    $cal_dobn = str_split($cal_dobn);
                    $cal_dobn = array_sum($cal_dobn);
                }

                // property number reading

                $numaric_val = str_split(filter_var($propertyhistory->number, FILTER_SANITIZE_NUMBER_INT));
                $propertynumber_sum = array_sum($numaric_val);
                while (strlen($propertynumber_sum) != 1) {
                    $propertynumber_sum = str_split($propertynumber_sum);
                    $propertynumber_sum = array_sum($propertynumber_sum);
                }

                $propertyalphabet = implode("", preg_split("/\d+/", $propertyhistory->number));
                $propertyno_alphabetno_sum = 0;
                if ($propertyalphabet != null) {
                    $propertyno_alphabets = str_split($propertyalphabet);
                    $alphabet_no = array();
                    foreach ($propertyno_alphabets as $propertyno_alphabet) {
                        $propertyno_alphabetno = Alphasystem_type::where('alphabet', 'LIKE', '%' . $propertyno_alphabet . '%')
                            ->where('systemtype_id', 2)
                            ->value('number');
                        array_push($alphabet_no, $propertyno_alphabetno);
                    }

                    $propertyno_alphabetno_sum = array_sum($alphabet_no);
                    while (strlen($propertyno_alphabetno_sum) != 1) {
                        $propertyno_alphabetno_sum = str_split($propertyno_alphabetno_sum);
                        $propertyno_alphabetno_sum = array_sum($propertyno_alphabetno_sum);
                    }
                }
                $final_propertyno = $propertynumber_sum + $propertyno_alphabetno_sum;

                while (strlen($final_propertyno) != 1) {
                    $final_propertyno = str_split($final_propertyno);
                    $final_propertyno = array_sum($final_propertyno);
                }

                //percentage Aco to dob and property number

                $dob_propertypercentage = Compatibility_percentage::where('number', $cal_dobn)->where('mate_number', $final_propertyno)->first();
                $propertydob_percentage = $dob_propertypercentage->compatibility_percentage;
                $remain_perc = 100 - $propertydob_percentage;


                // case2 dob and pin reading

                //pin reading 
                $pin = str_split($propertyhistory->postalcode, 1);
                $cal_pin_no = array_sum($pin);
                $cal_pinno = intval($cal_pin_no);
                while (strlen($cal_pinno) != 1) {
                    $cal_pinno = str_split($cal_pinno);
                    $cal_pinno = array_sum($cal_pinno);
                }

                //percentage of dob and property or dob and PIN percentage

                $dob_pin_percentage = Compatibility_percentage::where('number', $cal_dobno)->where('mate_number', $cal_pinno)->first();
                $dobpin_percentage = $dob_pin_percentage->compatibility_percentage;

                $dob_cal_percentage = ($remain_perc / 100) * $dobpin_percentage;
                $case2_percentage = $propertydob_percentage + $dob_cal_percentage;
                $remainingcase2_percentage = 100 - $case2_percentage;


                //case3 name and city reading 

                //city name reading 
                $citystruppername = strtoupper($propertyhistory->city);
                $citynames_array = explode(' ', $citystruppername);
                $cal_city_chaldno = array();
                foreach ($citynames_array as $citynamewords) {
                    $citywordletter = str_split($citynamewords);
                    $citywordchald_no = array();
                    foreach ($citywordletter as $citywordletters) {
                        $word_chald_n = Alphasystem_type::where('alphabet', 'LIKE', '%' . $citywordletters . '%')
                            ->where('systemtype_id', 2)
                            ->value('number');
                        array_push($citywordchald_no, $word_chald_n);
                    }
                    $city_chaldno = array_sum($citywordchald_no);
                    while (strlen($city_chaldno) != 1) {
                        $city_chaldno = str_split($city_chaldno, 1);
                        $city_chaldno = array_sum($city_chaldno);
                    }
                    array_push($cal_city_chaldno, $city_chaldno);
                }
                $city_chaldnumber = array_sum($cal_city_chaldno);
                while (strlen($city_chaldnumber) != 1) {
                    $city_chaldnumber = str_split($city_chaldnumber);
                    $city_chaldnumber = array_sum($city_chaldnumber);
                }

                // login user name reading
                $loginusername = $loginuser->name;
                $finalname = str_replace(' ', '', $loginusername);
                $loginuserstrname = strtoupper($finalname);
                $loginusersplitname = str_split($loginuserstrname, 1);
                foreach ($loginusersplitname as $nameletter) {
                    $chald_no[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                }
                $loginuserchaldno_sum = array_sum($chald_no);
                while (strlen($loginuserchaldno_sum) != 1) {
                    $loginuserchaldno_sum = str_split($loginuserchaldno_sum, 1);
                    $loginuserchaldno_sum = array_sum($loginuserchaldno_sum);
                }

                $loginusernamereadingno = $loginuserchaldno_sum;

                //total percentage of compatibility
                $name_city_percentage = Compatibility_percentage::where('number', $loginusernamereadingno)->where('mate_number', $city_chaldnumber)->first();
                $name_city_percentage = $name_city_percentage->compatibility_percentage;

                $calname_percentage = ($remainingcase2_percentage / 100) * $name_city_percentage;
                $final_propertycompatibility_perc = $case2_percentage + $calname_percentage;

                $propertyhistorydata['module_name'] = 'house/property';
                $propertyhistorydata['name'] = $propertyhistory->number . ', ' . $propertyhistory->city . ', ' . $propertyhistory->postalcode;
                $propertyhistorydata['description'] = $propertynamedesc;
                $propertyhistorydata['check_date'] = $propertycheckdate[0];
                $propertyhistorydata['percentage'] = $case2_percentage;
                array_push($historyproperty, $propertyhistorydata);
            }

            $spousehistories = User_compatiblecheck::where('user_id', $userid)->where('type', 6)->get();
            $historyspouse = array();
            $spousehistorydata = array();
            foreach ($spousehistories as $spousehistory) {
                $spousecheckdate = explode(' ', $spousehistory->created_at);

                $loginuser_dob = $loginuser->dob;
                $loginuserd_o_b = explode("-", $loginuser_dob);

                $spousepersondob_date = explode('-', $spousehistory->dates);

                //Name reading
                $spousepersonname = $spousehistory->name;
                $finalname = str_replace(' ', '', $spousepersonname);
                $strpersonnname = strtoupper($finalname);
                $splitpersonname = str_split($strpersonnname, 1);
                $alphabet_numbers = array();
                foreach ($splitpersonname as $splitnameletters) {
                    $name_chald_numbers = Alphasystem_type::where('alphabet', 'LIKE', '%' . $splitnameletters . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                    array_push($alphabet_numbers, $name_chald_numbers);
                }

                $name_chald_no_sum = array_sum($alphabet_numbers);

                while (strlen($name_chald_no_sum) != 1) {
                    $name_chald_no_sum = str_split($name_chald_no_sum, 1);
                    $name_chald_no_sum = array_sum($name_chald_no_sum);
                }

                $spousenamereadingno = $name_chald_no_sum;

                //partner relationship
                $loginuaer_dob_day = str_split($loginuserd_o_b[2], 1);
                $loginuser_dob_no = array_sum($loginuaer_dob_day);
                while (strlen($loginuser_dob_no) != 1) {
                    $loginuser_dob_no = str_split($loginuser_dob_no);
                    $loginuser_dob_no = array_sum($loginuser_dob_no);
                }

                $other_persion_day = str_split($spousepersondob_date[2], 1);
                $other_persionsum_number = array_sum($other_persion_day);
                $other_persion_number = intval($other_persionsum_number);
                while (strlen($other_persion_number) != 1) {
                    $other_persion_number = str_split($other_persion_number);
                    $other_persion_number = array_sum($other_persion_number);
                }

                $relation_desc = Partner_relationship::select('description')->where('number', $loginuser_dob_no)
                    ->where('mate_number', $other_persion_number)
                    ->first();


                // login user detail

                $loginuser_name = $loginuser->name;
                $finalname = str_replace(' ', '', $loginuser_name);
                $loginuserstrname = strtoupper($finalname);
                $loginusersplitname = str_split($loginuserstrname, 1);
                foreach ($loginusersplitname as $nameletter) {
                    $chald_no[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                }

                $loginuserchaldno_sum = array_sum($chald_no);

                while (strlen($loginuserchaldno_sum) != 1) {
                    $loginuserchaldno_sum = str_split($loginuserchaldno_sum, 1);
                    $loginuserchaldno_sum = array_sum($loginuserchaldno_sum);
                }

                $loginusernamereadingno = $loginuserchaldno_sum;

                //relation percentage 

                $relation_dob_percentage = Compatibility_percentage::where('number', $loginuser_dob_no)->where('mate_number', $other_persion_number)->first();
                $spousedob_percentage = $relation_dob_percentage->compatibility_percentage;
                $remaining_perc = 100 - $spousedob_percentage;

                $relation_nameperc = Compatibility_percentage::where('number', $loginusernamereadingno)->where('mate_number', $spousenamereadingno)->first();
                $spousename_percentage = $relation_nameperc->compatibility_percentage;

                $cal_name_percentage = ($remaining_perc / 100) * $spousename_percentage;
                $spousefinal_percentage = $spousedob_percentage + $cal_name_percentage;


                $spousehistorydata['module_name'] = 'spouse';
                $spousehistorydata['name'] = $spousehistory->name;
                $spousehistorydata['description'] = $relation_desc;
                $spousehistorydata['check_date'] = $spousecheckdate[0];
                $spousehistorydata['percentage'] = $spousefinal_percentage;

                array_push($historyspouse, $spousehistorydata);
            }

            $namereainghistories = User_historyname::where('user_id', $userid)->get();
            $historynamereaing = array();
            $namereainghistorydata = array();
            foreach ($namereainghistories as $namereainghistory) {
                $namereaingcheckdate = $namereainghistory->check_date;


                $namechaldno = array();
                $finalname = str_replace(' ', '', $namereainghistory->name);
                $strname = strtoupper($finalname);
                $splitname = str_split($strname, 1);
                foreach ($splitname as $nameletters) {
                    $chald_nameno = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletters . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                    array_push($namechaldno, $chald_nameno);
                }

                $chaldname_no_sum = array_sum($namechaldno);
                while (strlen($chaldname_no_sum) != 1) {
                    $chaldname_no_sum = str_split($chaldname_no_sum, 1);
                    $chaldname_no_sum = array_sum($chaldname_no_sum);
                }

                $namereadingno = $chaldname_no_sum;

                $namereadingdesc =  Module_description::where('moduletype_id', 1)
                    ->where('number', $namereadingno)
                    ->value('description');

                $explodenamereading_desc = explode('||', $namereadingdesc);
                $positive_desc = $explodenamereading_desc[0];
                $negative_desc = $explodenamereading_desc[1];

                $namedesc = array("positive_title" => "Positive", "positive_desc" => $positive_desc, "negative_title" => "Negative", "negative_desc" => $negative_desc);;


                //dob calculated number

                $loginuser_dob = $loginuser->dob;
                $explodedate = explode('-', $loginuser_dob);
                $split_day = str_split($explodedate[2], 1);
                $dobno = array_sum($split_day);
                $dobno = intval($dobno);
                while (strlen($dobno) != 1) {
                    $dobno = str_split($dobno);
                    $dobno = array_sum($dobno);
                }

                $loginusernamereadingno = $loginuserchaldno_sum;
                $name_compatibilitypercentage = Compatibility_percentage::where('number', $namereadingno)->where('mate_number', $dobno)->first();
                $namecompatibilitypercentage = $name_compatibilitypercentage->compatibility_percentage;

                $namereainghistorydata['module_name'] = 'name reading';
                $namereainghistorydata['name'] = $namereainghistory->name;
                $namereainghistorydata['description'] = $namedesc;
                $namereainghistorydata['check_date'] = $namereaingcheckdate;
                $namereainghistorydata['percentage'] = $namecompatibilitypercentage;

                array_push($historynamereaing, $namereainghistorydata);
            }

            $personalreadinghistories = User_namereading::where('user_id', $userid)->get();
            $historypersonalreading = array();
            $personalreadinghistorydata = array();

            foreach ($personalreadinghistories as $personalreadinghistory) {
                $personalreadingcheack_date = $personalreadinghistory->check_date;

                $personalreadingchaldno = array();
                $personalname_reading = explode(' ', $personalreadinghistory->name);
                $strpersonalreading = strtoupper($personalname_reading[0]);
                $splitpersonalname = str_split($strpersonalreading, 1);
                foreach ($splitpersonalname as $personalnameletters) {
                    $personalreadingchald_no = Alphasystem_type::where('alphabet', 'LIKE', '%' . $personalnameletters . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                    array_push($personalreadingchaldno, $personalreadingchald_no);
                }

                $chaldno_sum = array_sum($personalreadingchaldno);
                while (strlen($chaldno_sum) != 1) {
                    $chaldno_sum = str_split($chaldno_sum, 1);
                    $chaldno_sum = array_sum($chaldno_sum);
                }

                $personalreadingno = $chaldno_sum;

                $personalreadingdesc =  Module_description::where('moduletype_id', 1)
                    ->where('number', $personalreadingno)
                    ->value('description');

                $explodepersonalreading_desc = explode('||', $personalreadingdesc);
                $personal_positive_desc = $explodepersonalreading_desc[0];
                $personal_negative_desc = $explodepersonalreading_desc[1];

                $personalreadingdesc = array("positive_title" => "Positive", "positive_desc" => $personal_positive_desc, "negative_title" => "Negative", "negative_desc" => $personal_negative_desc);;

                $personalreadinghistorydata['module_name'] = 'name reading';
                $personalreadinghistorydata['name'] = $personalreadinghistory->name;
                $personalreadinghistorydata['description'] = $personalreadingdesc;
                $personalreadinghistorydata['check_date'] = $personalreadingcheack_date;
                $personalreadinghistorydata['percentage'] = '';

                array_push($historypersonalreading, $personalreadinghistorydata);
            }

            $history = array('car' => $carshistory, 'onetoother' => $historyonetoother, 'business' => $historybusniess, 'property' => $historyproperty, 'spouse' => $historyspouse, 'namereading' => $historynamereaing, 'personal_reading' => $historypersonalreading);

            return response()->json([
                'status' => 1,
                'message' => 'success',
                'subscription_status' => $loginuser->subscription_status,
                'history' => $history
            ]);
        } else {
            return response()->json([
                'status' => 1,
                'message' => 'Error'
            ]);
        }
    }

    public function userelementalData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $userid = $request->userid;
        $user = User::find($userid);

        if ($user) {
            $dob = $user->dob;
            $date = explode('-', $dob);
            $day = $date[2];
            $dayno = str_split($day, 1);
            $dayno = array_sum($dayno);
            $dayno = intval($dayno);
            while (strlen($dayno) != 1) {
                $dayno = str_split($dayno);
                $dayno = array_sum($dayno);
            }

            //elemental number
            $elementaldesc = Module_description::where('moduletype_id', 4)
                ->where('number', $dayno)
                ->value('description');

            $elementaldesc = strip_tags($elementaldesc);
            $elementname = explode(' ', $elementaldesc);

            if ($elementname[0] == 'Fire') {
                $compatible_element = 'Air';
                $uncompatible_element = 'Water';
                $compatible_elementdesc = Module_description::where('moduletype_id', 4)
                    ->where('number', 4)
                    ->value('description');
                $compatible_elementdesc = strip_tags($compatible_elementdesc);
                $uncompatible_elementdesc = Module_description::where('moduletype_id', 4)
                    ->where('number', 2)
                    ->value('description');
                $uncompatible_elementdesc = strip_tags($uncompatible_elementdesc);
            } elseif ($elementname[0] == 'Water') {
                $compatible_element = 'Earth';
                $uncompatible_element = 'Air';
                $compatible_elementdesc = Module_description::where('moduletype_id', 4)
                    ->where('number', 6)
                    ->value('description');
                $compatible_elementdesc = strip_tags($compatible_elementdesc);
                $uncompatible_elementdesc = Module_description::where('moduletype_id', 4)
                    ->where('number', 4)
                    ->value('description');
                $uncompatible_elementdesc = strip_tags($uncompatible_elementdesc);
            } elseif ($elementname[0] == 'Air') {
                $compatible_element = 'Fire';
                $uncompatible_element = 'Earth';
                $compatible_elementdesc = Module_description::where('moduletype_id', 4)
                    ->where('number', 1)
                    ->value('description');
                $compatible_elementdesc = strip_tags($compatible_elementdesc);
                $uncompatible_elementdesc = Module_description::where('moduletype_id', 4)
                    ->where('number', 6)
                    ->value('description');
                $uncompatible_elementdesc = strip_tags($uncompatible_elementdesc);
            } else {
                $compatible_element = 'Water';
                $uncompatible_element = 'Fire';
                $compatible_elementdesc = Module_description::where('moduletype_id', 4)
                    ->where('number', 2)
                    ->value('description');
                $compatible_elementdesc = strip_tags($compatible_elementdesc);
                $uncompatible_elementdesc = Module_description::where('moduletype_id', 4)
                    ->where('number', 1)
                    ->value('description');
                $uncompatible_elementdesc = strip_tags($uncompatible_elementdesc);
            }


            $elemental = array("element" => $elementname[0], "description" => $elementaldesc);
            $compatible_elements = array("compatible_element" => $compatible_element, 'compatible_elementdesc' => $compatible_elementdesc);
            $uncompatible_elements = array("uncompatible_element" => $uncompatible_element, 'uncompatible_elementdesc' => $uncompatible_elementdesc);

            return response()->json([
                'status' => 1,
                'message' => 'success',
                'subscription_status' => $user->subscription_status,
                'element' => $elemental,
                'compatible_elements' => $compatible_elements,
                'uncompatible_elements' => $uncompatible_elements

            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'User not found',

            ]);
        }
    }

    public function lifecoach(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required',
            'current_date' => 'required',
            'date_current' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $currentDate = $request->current_date;
        $date_current = $request->date_current;
        $explodeDate_current = explode('-', $date_current);
        $date_currentYear = $explodeDate_current[0];

        $userid = $request->userid;
        $user = User::find($userid);

        if ($user) {
            $userdob = $user->dob;
            $explode_dob = explode("-", $userdob);
            $dob_date = $explode_dob[2];
            $dob_month = $explode_dob[1];

            $favdata = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 1)
                ->where('month_id', $dob_month)
                ->where('date', $dob_date)
                ->first();
            $unfavdata = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 2)
                ->where('month_id', $dob_month)
                ->where('date', $dob_date)
                ->first();

            $fav_dates = $favdata->numbers;
            $fav_dates = str_replace(' ', '', $fav_dates);
            $fav_dates = explode(',', $fav_dates);
            $fav_days = $favdata->days;
            $fav_days = str_replace(' ', '', $fav_days);
            $fav_days = explode(',', $fav_days);
            $fav_months = $favdata->months;
            $fav_months = str_replace(' ', '', $fav_months);
            $fav_months = explode(',', $fav_months);

            $unfav_dates = $unfavdata->numbers;
            $unfav_dates = str_replace(' ', '', $unfav_dates);
            $unfav_dates = explode(',', $unfav_dates);
            $unfav_days = $unfavdata->days;
            $unfav_days = str_replace(' ', '', $unfav_days);
            $unfav_days = explode(',', $unfav_days);
            $unfav_months = $unfavdata->months;
            $unfav_months = str_replace(' ', '', $unfav_months);
            $unfav_months = explode(',', $unfav_months);

            $currentdateformate = explode('-', $currentDate);
            $date_split = str_split($currentdateformate[1], 1);
            $date_sum = array_sum($date_split);
            while (strlen($date_sum) != 1) {
                $date_sum = str_split($date_sum);
                $date_sum = array_sum($date_sum);
            }
            $day_star = 0;
            $month_star = 0;
            if (in_array($date_sum, $fav_dates)) {
                $date_star = 1;
                if (in_array($currentdateformate[0], $fav_days)) {
                    $day_star = 1;
                }
                if (in_array($currentdateformate[2], $fav_months)) {
                    $month_star = 1;
                }
            } else {
                $date_star = 0;
            }

            $unfavday_star = 0;
            $unfavmonth_star = 0;
            if (in_array($date_sum, $unfav_dates)) {
                $unfavdate_star = 1;
                if (in_array($currentdateformate[0], $unfav_days)) {
                    $unfavday_star = 1;
                }
                if (in_array($currentdateformate[2], $unfav_months)) {
                    $unfavmonth_star = 1;
                }
            } else {
                $unfavdate_star = 0;
            }

            $currentdayfav_star = $date_star + $day_star + $month_star;
            $currentdayunfav_star = $unfavdate_star + $unfavday_star + $unfavmonth_star;

            $dobdate_array = array($dob_date, $dob_month, $date_currentYear);
            $dobdatenumber = array_sum($dobdate_array);
            while (strlen($dobdatenumber) != 1) {
                $dobdatenumber = str_split($dobdatenumber);
                $dobdatenumber = array_sum($dobdatenumber);
            }
            $current_date = $date_current;
            $current_date = explode('-', $current_date);
            $current_monthno = $current_date[1];
            while (strlen($current_monthno) != 1) {
                $current_monthno = str_split($current_monthno);
                $current_monthno = array_sum($current_monthno);
            }
            $cal_monthno = $dobdatenumber + $current_monthno;
            while (strlen($cal_monthno) != 1) {
                $cal_monthno = str_split($cal_monthno);
                $cal_monthno = array_sum($cal_monthno);
            }
            $cal_daynumber = $current_date[2];
            while (strlen($cal_daynumber) != 1) {
                $cal_daynumber = str_split($cal_daynumber);
                $cal_daynumber = array_sum($cal_daynumber);
            }
            $personal_dayno = $cal_monthno + $cal_daynumber;
            while (strlen($personal_dayno) != 1) {
                $personal_dayno = str_split($personal_dayno);
                $personal_dayno = array_sum($personal_dayno);
            }

            //zodiac Sign	
            $formetdob = date("d-F-Y", strtotime($user->dob));
            $zodiacdate = explode('-', $formetdob);
            $dobday = $zodiacdate[0];
            $dobmonth = $zodiacdate[1];
            $zodiac = Zodic_sign::where('title', 'LIKE', '%' . $dobmonth . '%')
                ->get();

            if ($dobmonth == "March") {
                $titledaydate = $zodiac[1]->title;
                $explodetitle_daydate = explode(' ', $titledaydate);
                $title_daydate = $explodetitle_daydate[2];

                if ($dobday <= $title_daydate) {
                    $zodiacdata = $zodiac[1];
                } else {
                    $zodiacdata = $zodiac[0];
                }
            } else {
                $titledaydate = $zodiac[0]->title;
                $explodetitle_daydate = explode(' ', $titledaydate);
                $title_daydate = $explodetitle_daydate[2];

                if ($dobday <= $title_daydate) {
                    $zodiacdata = $zodiac[0];
                } else {
                    $zodiacdata = $zodiac[1];
                }
            }

            $user_zodiacsign = strtolower($zodiacdata->zodic_sign);
            $user_zodiacday = 'today';
            $aztro = curl_init();
            curl_setopt_array($aztro, array(
                CURLOPT_URL => "https://aztro.sameerkumar.website/?sign=$user_zodiacsign&day=$user_zodiacday",
                CURLOPT_POST => TRUE,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                )
            ));

            $response = curl_exec($aztro);
            if ($response === FALSE) {
                die(curl_error($aztro));
            }
            $responseData = json_decode($response, TRUE);

            $user_zodiacsignliveapi = $responseData['description'];
            $usercolorliveapi = $responseData['color'];


            if ($currentdayfav_star != 0) {
                $lifecoachtype = Dailycoach_type::where('type', $personal_dayno)->first();
                $short_lifecoach_desc = 'Today is ' . $currentdayfav_star . ' star day for ' . $lifecoachtype->name . ".";
                $lifecoach = 'Today is ' . $currentdayfav_star . ' star day for ' . $lifecoachtype->name . ". " . $user_zodiacsignliveapi;
            } elseif ($currentdayunfav_star != 0) {
                $lifecoachtype = Dailycoach_type::where('type', $personal_dayno)->first();
                $short_lifecoach_desc = 'Today is not good for ' . $lifecoachtype->name . ".";
                $lifecoach = 'Today is not good for ' . $lifecoachtype->name . ". " . $user_zodiacsignliveapi;
            } else {
                $lifecoachtype = Dailycoach_type::where('type', $personal_dayno)->first();
                $short_lifecoach_desc = 'Today is good for ' . $lifecoachtype->name . ".";
                $lifecoach = 'Today is good for ' . $lifecoachtype->name . ". " . $user_zodiacsignliveapi;
            }

            return response()->json([
                'status' => 1,
                'message' => 'success',
                'userid' => $userid,
                'subscription_status' => $user->subscription_status,
                'user_zodiacsign' => $user_zodiacsign,
                'short_lifecoach_desc' => $short_lifecoach_desc,
                'life_coach' => $lifecoach,
                'personal_day' => $personal_dayno,
                'color' => $usercolorliveapi

            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'User not found',
            ]);
        }
    }

    public function usertraveldata(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required',
            'current_date' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $currentDate = $request->current_date;
        $userid = $request->userid;
        $user = User::find($userid);
        if ($user) {
            $userdob = $user->dob;
            $dobdate = explode('-', $userdob);
            $current_date = $currentDate;
            $explodecurrent_date = explode('-', $current_date);
            $dobnumber = $dobdate[1] + $dobdate[2] + $explodecurrent_date[0];
            while (strlen($dobnumber) != 1) {
                $dobnumber = str_split($dobnumber);
                $dobnumber = array_sum($dobnumber);
            }
            $current_monthno = $explodecurrent_date[1];
            while (strlen($current_monthno) != 1) {
                $current_monthno = str_split($current_monthno);
                $current_monthno = array_sum($current_monthno);
            }

            $personal_monthno = $dobnumber + $current_monthno;
            while (strlen($personal_monthno) != 1) {
                $personal_monthno = str_split($personal_monthno);
                $personal_monthno = array_sum($personal_monthno);
            }

            $triptypearray = array("1", "5", "7", "9");

            if (in_array($personal_monthno, $triptypearray)) {
                if ($personal_monthno == 1) {
                    $thismonthtraveltitle = 'New Places Trip';
                    $thismonthdescription = "As per your prime number, the numeric calculations predict that New Places Trip is well suited for you in this month. On this trip you are interested in experiencing everything and enjoy spontaneity and making good travel companions. You will discover different cultures at different places. This trip is surely lucky for you. While traveling, making new connections really impacts your personal growth.";
                } elseif ($personal_monthno == 5) {
                    $thismonthtraveltitle = 'Short/Weekend Trip';
                    $thismonthdescription = "As per your prime number, the numerology calculations predict that Short/Weekend Trip is well suited for you in this month. You can freely plan a short or weekend trip wisely with your family or friends and enjoy the standout time on this trip.";
                } elseif ($personal_monthno == 7) {
                    $thismonthtraveltitle = 'Exotic/Family Trip';
                    $thismonthdescription = "As per numeric predictions, the Exotic/Family Trip is great for your and your close ones this month. On this trip you will form strong connections with your family and enjoy traveling to experience the new cultures. While traveling you are very curious and energetic about tasting the delicious food in the new culture.";
                } else {
                    $thismonthtraveltitle = 'International/Business Trip';
                    $thismonthdescription = "As per Astro Numeric calculations, predicts that International/Business Trip is much more profitable for you in this month. On this business trip you will get many new deals and later on your business growth leads to the next level.";
                }
            } else {
                $thismonthtraveltitle = 'No Trip';
                $thismonthdescription = 'No need to plan any trip';
            }
            $thismonth = array();
            $thismonth['travel_type'] = 'This month';
            $thismonth['travel_title'] = $thismonthtraveltitle;
            $thismonth['travel_description'] = $thismonthdescription;

            $next_monthno = $explodecurrent_date[1] + 1;

            while (strlen($next_monthno) != 1) {
                $next_monthno = str_split($next_monthno);
                $next_monthno = array_sum($next_monthno);
            }


            $personal_nextmonthno = $dobnumber + $next_monthno;
            while (strlen($personal_nextmonthno) != 1) {
                $personal_nextmonthno = str_split($personal_nextmonthno);
                $personal_nextmonthno = array_sum($personal_nextmonthno);
            }

            if (in_array($personal_nextmonthno, $triptypearray)) {
                if ($personal_nextmonthno == 1) {
                    $nextmonthtraveltitle = 'New places Trip';
                    $nextmonthdescription = "As per numeric predictions, next month is great for your New places Trip. You are a born leader, and a discoverer which means you like being active on vacation and always excited to explore new cities and playing sports. ";
                } elseif ($personal_nextmonthno == 5) {
                    $nextmonthtraveltitle = 'Short/Weekend Trip';
                    $nextmonthdescription = "As per numeric calculations, it predicts that Short/Weekend Trip is well suited for you in the next month. Try to plan your trip with plenty of luxury and not too much rushing around. You value your own space, when traveling with friends be sure to book separate for you for meditating and reading. ";
                } elseif ($personal_nextmonthno == 7) {
                    $nextmonthtraveltitle = 'Exotic/Family Trip';
                    $nextmonthdescription = "As per your prime numbers, it predicts that Exotic/Family Trip is well suitable for you in the next month. On this trip you will make connections with old people and make you more accommodating to your parent’s needs and happy to travel with old parents or relatives.";
                } else {
                    $nextmonthtraveltitle = 'International/Business Trip';
                    $nextmonthdescription = "As per numeric calculations, it predicts that International/Business Trip is moneymaking for you in the next month. You oftenly enjoy a strong bond with your co-workers. On this trip you will learn new skills, boost confidence and get many good leads for your business growth.";
                }
            } else {
                $nextmonthtraveltitle = 'No Trip';
                $nextmonthdescription = 'No need to plan any trip';
            }
            $nextmonth = array();
            $nextmonth['travel_type'] = 'Next month';
            $nextmonth['travel_title'] = $nextmonthtraveltitle;
            $nextmonth['travel_description'] = $nextmonthdescription;

            $personalyear = $dobnumber;

            if (in_array($personalyear, $triptypearray)) {
                if ($personalyear == 1) {
                    $thisyeartraveltitle = 'New places Trip';
                    $thisyeardescription = "As per Numeric calculations, this year is amazingly good for your New places Trip. You love to experience the upscale culture and adventurous fun spots. You comfortably travel with the fabulous new friends who share delicious and expensive tastes with you. You value your time and do plenty of research and read reviews about the restaurants and destinations to maximize your trip.";
                } elseif ($personalyear == 5) {
                    $thisyeartraveltitle = 'Short/Weekend Trip';
                    $thisyeardescription = "As per numerical values, it predicts that Short/Weekend Trip is suitable for you this year. You will make strong connections with new friends and try adventurous activities with them. You will love trips that offer freedom, comfortable stay, and delicious food. You share a desire for an active and energetic schedule.";
                } elseif ($personalyear == 7) {
                    $thisyeartraveltitle = 'Exotic/Family Trip';
                    $thisyeardescription = "As per numerology calculations, it predicts that Exotic/Family Trip is best suitable for you this year. On trips, you will almost always prefer to stay somewhere close to water and are happy to stay in one place with your family. On this trip you will share a very beautiful bond with your family members.";
                } else {
                    $thisyeartraveltitle = 'International/Business Trip';
                    $thisyeardescription = "As per numeric calculations, it predicts that International/Business Trip is valuable for you this year. This trip will surely help you to spread your business rapidly to make new connections with high class professionals. 
                    ";
                }
            } else {
                $thisyeartraveltitle = 'No Trip';
                $thisyeardescription = 'No need to plan the trip';
            }
            $thisyear = array();
            $thisyear['travel_type'] = 'This year';
            $thisyear['travel_title'] = $thisyeartraveltitle;
            $thisyear['travel_description'] = $thisyeardescription;

            $traveldetail = array($thismonth, $nextmonth, $thisyear);

            return response()->json([
                'status' => 1,
                'message' => 'success',
                'subscription_status' => $user->subscription_status,
                'traveldetail' => $traveldetail,
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'User not found',

            ]);
        }
    }


    public function cosmiccelender(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required',
            'date_current' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $date_current = $request->date_current;
        $userid = $request->userid;
        $user = User::find($userid);

        $userdob = $user->dob;
        $explodedob = explode('-', $userdob);
        $dobday = $explodedob[2];
        $dobmonth = $explodedob[1];

        $loginuserjoindate = $user->created_at;
        $explodejoindate = explode('-', $loginuserjoindate);
        $joinyear = intval($explodejoindate[0]);
        $joinmonth = intval($explodejoindate[1]);
        $explodedatetime = explode(' ', $explodejoindate[2]);
        $joindateno = intval($explodedatetime[0]);

        $explodeDate_current = explode('-', $date_current);
        $currentdaydate = $explodeDate_current[2];
        $currentmonth = $explodeDate_current[1];
        $currentyear = $explodeDate_current[0];

        for ($y = $currentyear; $y <= $currentyear; $y++) {
            $favlist = array();
            $unfavlist = array();
            for ($m = $currentmonth; $m <= $currentmonth; $m++) {
                $daysinmonth = cal_days_in_month(0, $m, $y);
                if ($y == $joinyear && $m == $joinmonth) {
                    $startday = $joindateno;
                } else {
                    $startday = 1;
                }
                if ($y == $currentyear && $m == $currentmonth) {
                    $no_of_day = $currentdaydate;
                } else {
                    $no_of_day = $daysinmonth;
                }
                for ($i = $startday; $i <= $no_of_day; $i++) {

                    $fav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 1)
                        ->where('month_id', $dobmonth)
                        ->where('date', $dobday)
                        ->first();
                    $unfav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 2)
                        ->where('month_id', $dobmonth)
                        ->where('date', $dobday)
                        ->first();
                    $fav_dates = $fav->numbers;
                    $fav_dates = str_replace(' ', '', $fav_dates);
                    $fav_dates = explode(',', $fav_dates);
                    $fav_days = $fav->days;
                    $fav_days = str_replace(' ', '', $fav_days);
                    $fav_days = explode(',', $fav_days);
                    $fav_months = $fav->months;
                    $fav_months = str_replace(' ', '', $fav_months);
                    $fav_months = explode(',', $fav_months);

                    $unfav_dates = $unfav->numbers;
                    $unfav_dates = str_replace(' ', '', $unfav_dates);
                    $unfav_dates = explode(',', $unfav_dates);
                    $unfav_days = $unfav->days;
                    $unfav_days = str_replace(' ', '', $unfav_days);
                    $unfav_days = explode(',', $unfav_days);
                    $unfav_months = $unfav->months;
                    $unfav_months = str_replace(' ', '', $unfav_months);
                    $unfav_months = explode(',', $unfav_months);
                    $current_date = explode('-', date("D-j-M-Y", mktime(0, 0, 0, $m, $i, $y)));
                    $date_sum = str_split($i, 1);
                    $date_sum = array_sum($date_sum);
                    if (strlen($date_sum) != 1) {
                        $date_sum = str_split($date_sum);
                        $date_sum = array_sum($date_sum);
                    }
                    $fav_cosmic_stars = 0;
                    $unfav_cosmic_stars = 0;
                    $day_star = 0;
                    $month_star = 0;
                    $unfavday_star = 0;
                    $unfavmonth_star = 0;
                    if (in_array($date_sum, $fav_dates)) {
                        $date_star = 1;
                        if (in_array($current_date[0], $fav_days)) {
                            $day_star = 1;
                        }
                        if (in_array($current_date[2], $fav_months)) {
                            $month_star = 1;
                        }
                    } else {
                        $date_star = 0;
                    }
                    if (in_array($date_sum, $unfav_dates)) {
                        $unfavdate_star = 1;
                        if (in_array($current_date[0], $unfav_days)) {
                            $unfavday_star = 1;
                        }
                        if (in_array($current_date[2], $unfav_months)) {
                            $unfavmonth_star = 1;
                        }
                    } else {
                        $unfavdate_star = 0;
                    }

                    $fav_cosmic_stars = $date_star + $day_star + $month_star;
                    $favdata = array();
                    $favdata['module_type'] = "Fav Star";
                    $favdata['year'] = $y;
                    $favdata['month'] = $m;
                    $favdata['date'] = $i;
                    $favdata['datestar'] = $fav_cosmic_stars;
                    array_push($favlist, $favdata);

                    // $favdatekey[$i] =  $fav_cosmic_stars;
                    $unfav_cosmic_stars = $unfavdate_star + $unfavday_star + $unfavmonth_star;
                    $unfavdata = array();
                    $unfavdata['module_type'] = "Unfav Star";
                    $unfavdata['year'] = $y;
                    $unfavdata['month'] = $m;
                    $unfavdata['date'] = $i;
                    $unfavdata['datestar'] = $unfav_cosmic_stars;
                    array_push($unfavlist, $unfavdata);
                    //$unfavdatekey[$i] =  $unfav_cosmic_stars;

                }
            }
        }

        $cosmiccalender = array("favstars" => $favlist, "unfavstars" => $unfavlist);

        return response()
            ->json([
                'status' => 1,
                'message' => 'Success',
                'subscription_status' => $user->subscription_status,
                'cosmiccalender' => $cosmiccalender
            ]);
    }

    public function userdailyprediction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required',
            'prediction_date' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $prediction_date = $request->prediction_date;
        $userid = $request->userid;
        $user = User::find($userid);

        if ($user) {
            // current date cosmic stars
            $userdob = $user->dob;
            $explodedob = explode('-', $userdob);
            $dobday = $explodedob[2];
            $dobmonth = $explodedob[1];

            $explodePrediction_date = explode('-', $prediction_date);

            // $currentdaydate = date('d');
            $prediction_daydate = $explodePrediction_date[2];

            // $currentmonth = date('m');
            $prediction_month = $explodePrediction_date[1];

            // $currentyear = date('Y');
            $prediction_year = $explodePrediction_date[0];

            $favlist = array();
            $unfavlist = array();

            /* $fav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 1)
                ->where('month_id', $dobmonth)
                ->where('date', $dobday)
                ->first();
            $unfav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 2)
                ->where('month_id', $dobmonth)
                ->where('date', $dobday)
                ->first();
            $fav_dates = $fav->numbers;
            $fav_dates = str_replace(' ', '', $fav_dates);
            $fav_dates = explode(',', $fav_dates);
            $fav_days = $fav->days;
            $fav_days = str_replace(' ', '', $fav_days);
            $fav_days = explode(',', $fav_days);
            $fav_months = $fav->months;
            $fav_months = str_replace(' ', '', $fav_months);
            $fav_months = explode(',', $fav_months);

            $unfav_dates = $unfav->numbers;
            $unfav_dates = str_replace(' ', '', $unfav_dates);
            $unfav_dates = explode(',', $unfav_dates);
            $unfav_days = $unfav->days;
            $unfav_days = str_replace(' ', '', $unfav_days);
            $unfav_days = explode(',', $unfav_days);
            $unfav_months = $unfav->months;
            $unfav_months = str_replace(' ', '', $unfav_months);
            $unfav_months = explode(',', $unfav_months);
            $current_date = explode('-', date("D-j-M-Y", mktime(0, 0, 0, $prediction_month, $currentdaydate, $prediction_year)));
            $date_sum = str_split($prediction_daydate, 1);
            $date_sum = array_sum($date_sum);
            if (strlen($date_sum) != 1) {
                $date_sum = str_split($date_sum);
                $date_sum = array_sum($date_sum);
            }

            $day_star = 0;
            $month_star = 0;
            $unfavday_star = 0;
            $unfavmonth_star = 0;
            if (in_array($date_sum, $fav_dates)) {
                $date_star = 1;
                if (in_array($current_date[0], $fav_days)) {
                    $day_star = 1;
                }
                if (in_array($current_date[2], $fav_months)) {
                    $month_star = 1;
                }
            } else {
                $date_star = 0;
            }
            if (in_array($date_sum, $unfav_dates)) {
                $unfavdate_star = 1;
                if (in_array($current_date[0], $unfav_days)) {
                    $unfavday_star = 1;
                }
                if (in_array($current_date[2], $unfav_months)) {
                    $unfavmonth_star = 1;
                }
            } else {
                $unfavdate_star = 0;
            }

            $fav_cosmic_stars = $date_star + $day_star + $month_star;
            $favdata = array();
            $favdata['module_type'] = "Fav Star";
            $favdata['year'] = $prediction_year;
            $favdata['month'] = $prediction_month;
            $favdata['date'] = $prediction_daydate;
            $favdata['datestar'] = $fav_cosmic_stars;
            array_push($favlist, $favdata);

            // $favdatekey[$i] =  $fav_cosmic_stars;
            $unfav_cosmic_stars = $unfavdate_star + $unfavday_star + $unfavmonth_star;
            $unfavdata = array();
            $unfavdata['module_type'] = "Unfav Star";
            $unfavdata['year'] = $prediction_year;
            $unfavdata['month'] = $prediction_month;
            $unfavdata['date'] = $prediction_daydate;
            $unfavdata['datestar'] = $unfav_cosmic_stars;
            array_push($unfavlist, $unfavdata);
            //$unfavdatekey[$i] =  $unfav_cosmic_stars;
			
			echo "Fav Star -".$fav_cosmic_stars;
			echo "<br/>";
			echo "UnFav Star -".$unfav_cosmic_stars;
			echo "<br/>";

            if ($fav_cosmic_stars > 0) {
                $dfavdata = $favdata;
            } else {
                if ($unfav_cosmic_stars > 0) {
                    $dfavdata = $unfavdata;
                } else {
                    $data['module_type'] = "";
                    $data['year'] = $prediction_year;
                    $data['month'] = $prediction_month;
                    $data['date'] = $prediction_daydate;
                    $data['datestar'] = 0;
                    $dfavdata = $data;
                }
            } */

            $fav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 1)
                ->where('month_id', $dobmonth)
                ->where('date', $dobday)
                ->first();

            $unfav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 2)
                ->where('month_id', $dobmonth)
                ->where('date', $dobday)
                ->first();

            $fav_dates = $fav->numbers;
            $fav_dates = str_replace(' ', '', $fav_dates);
            $fav_dates = explode(',', $fav_dates);
            $fav_days = $fav->days;
            $fav_days = str_replace(' ', '', $fav_days);
            $fav_days = explode(',', $fav_days);
            $fav_months = $fav->months;
            $fav_months = str_replace(' ', '', $fav_months);
            $fav_months = explode(',', $fav_months);

            $unfav_dates = $unfav->numbers;
            $unfav_dates = str_replace(' ', '', $unfav_dates);
            $unfav_dates = explode(',', $unfav_dates);
            $unfav_days = $unfav->days;
            $unfav_days = str_replace(' ', '', $unfav_days);
            $unfav_days = explode(',', $unfav_days);
            $unfav_months = $unfav->months;
            $unfav_months = str_replace(' ', '', $unfav_months);
            $unfav_months = explode(',', $unfav_months);
            $current_date = explode('-', date("D-j-M-Y", mktime(0, 0, 0, $prediction_month, $prediction_daydate, $prediction_year)));
            $date_sum = str_split($prediction_daydate, 1);
            $date_sum = array_sum($date_sum);
            if (strlen($date_sum) != 1) {
                $date_sum = str_split($date_sum);
                $date_sum = array_sum($date_sum);
            }
            $fav_cosmic_stars = 0;
            $unfav_cosmic_stars = 0;
            $day_star = 0;
            $month_star = 0;
            $unfavday_star = 0;
            $unfavmonth_star = 0;
            if (in_array($date_sum, $fav_dates)) {
                $date_star = 1;
                if (in_array($current_date[0], $fav_days)) {
                    $day_star = 1;
                }
                if (in_array($current_date[2], $fav_months)) {
                    $month_star = 1;
                }
            } else {
                $date_star = 0;
            }
            if (in_array($date_sum, $unfav_dates)) {
                $unfavdate_star = 1;
                if (in_array($current_date[0], $unfav_days)) {
                    $unfavday_star = 1;
                }
                if (in_array($current_date[2], $unfav_months)) {
                    $unfavmonth_star = 1;
                }
            } else {
                $unfavdate_star = 0;
            }

            $fav_cosmic_stars = $date_star + $day_star + $month_star;
            $unfav_cosmic_stars = $unfavdate_star + $unfavday_star + $unfavmonth_star;


            $favdata = array();
            $favdata['module_type'] = "Fav Star";
            $favdata['year'] = $prediction_year;
            $favdata['month'] = $prediction_month;
            $favdata['date'] = $prediction_daydate;
            $favdata['datestar'] = $fav_cosmic_stars;
            array_push($favlist, $favdata);

            // $favdatekey[$i] =  $fav_cosmic_stars;
            $unfavdata = array();
            $unfavdata['module_type'] = "Unfav Star";
            $unfavdata['year'] = $prediction_year;
            $unfavdata['month'] = $prediction_month;
            $unfavdata['date'] = $prediction_daydate;
            $unfavdata['datestar'] = $unfav_cosmic_stars;
            array_push($unfavlist, $unfavdata);
            //$unfavdatekey[$i] =  $unfav_cosmic_stars;

            $cosmiccalender = array("favstars" => $favlist, "unfavstars" => $unfavlist);

            if ($fav_cosmic_stars > 0) {
                $dfavdata = $favdata;
            } else {
                if ($unfav_cosmic_stars > 0) {
                    $dfavdata = $unfavdata;
                } else {
                    $data['module_type'] = "";
                    $data['year'] = $prediction_year;
                    $data['month'] = $prediction_month;
                    $data['date'] = $prediction_daydate;
                    $data['datestar'] = 0;
                    $dfavdata = $data;
                }
            }

            //$dfavdata = "";


            // Current month Travel detail

            $dobnumber = $dobday + $dobmonth + $prediction_year;
            while (strlen($dobnumber) != 1) {
                $dobno_split = str_split($dobnumber);
                $dobno_sum = array_sum($dobno_split);
                $dobnumber = $dobno_sum;
            }

            $current_monthno = $prediction_month;
            while (strlen($current_monthno) != 1) {
                $current_monthno_split = str_split($current_monthno);
                $current_monthno_sum = array_sum($current_monthno_split);
                $current_monthno = $current_monthno_sum;
            }

            $personal_monthno = $dobnumber + $current_monthno;
            while (strlen($personal_monthno) != 1) {
                $personal_monthno_split = str_split($personal_monthno);
                $personal_monthno_sum = array_sum($personal_monthno_split);
                $personal_monthno = $personal_monthno_sum;
            }

            $triptypearray = array("1", "5", "7", "9");

            if (in_array($personal_monthno, $triptypearray)) {
                if ($personal_monthno == 1) {
                    $thismonthtraveltitle = 'New Places Trip';
                } elseif ($personal_monthno == 5) {
                    $thismonthtraveltitle = 'Short/Weekend Trip';
                } elseif ($personal_monthno == 7) {
                    $thismonthtraveltitle = 'Exotic/Family Trip';
                } else {
                    $thismonthtraveltitle = 'International/Business Trip';
                }

                $thismonthdescription = 'Good time for ' . $thismonthtraveltitle;
            } else {
                $thismonthtraveltitle = 'No Trip';
                $thismonthdescription = 'No need to plan any trip';
            }
            $thismonth = array();
            $thismonth['travel_type'] = 'This month';
            $thismonth['travel_description'] = $thismonthdescription;

            return response()
                ->json([
                    'status' => 1,
                    'message' => 'Success',
                    'subscription_status' => $user->subscription_status,
                    'cosmiccalender' => $cosmiccalender,
                    'dcosmiccalender' => $dfavdata,
                    'travel_detail' => $thismonth
                ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Error',
            ]);
        }
    }

    public function messagelist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $userid = $request->userid;
        $user = User::find($userid);
        $reciver = User::where('reciverIdStatus', 1)->first();
        $reciverid = $reciver->id;

        $message_lists = User_prediction::where([
            ['sender_id', '=', $userid],
            ['receiver_id', '=', $reciverid],
        ])->orWhere([
            ['sender_id', '=', $reciverid],
            ['receiver_id', '=', $userid],
        ])->get();

        $messagelist = array();
        foreach ($message_lists as $messages) {
            $message['id'] = $messages->id;
            $message['sender_id'] = $messages->sender_id;
            $message['message'] = strip_tags($messages->message);
            $message['is_seen'] = $messages->is_seen;
            $message['is_like'] = $messages->is_like;
            $message['date'] = $messages->created_at;
            array_push($messagelist, $message);
        }



        return response()->json([
            'status' => 1,
            'message' => 'success',
            'subscription_status' => $user->subscription_status,
            'message_detail' => $messagelist
        ]);
    }

    public function askquesstion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required',
            'message' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $userid = $request->userid;
        $user = User::find($userid);
        $reciver = User::where('reciverIdStatus', 1)->first();
        $reciverid = $reciver->id;
        $sendmessage = $request->message;
        if ($user->subscription_status == 0 || $user->subscription_status == 2) {

            $firstDateOfWeek = date("Y-m-d", strtotime('monday this week'));
            $lastdateOfWeek = date("Y-m-d", strtotime('sunday this week'));
            $message_list = User_prediction::where('sender_id', $user->id)->where('receiver_id', $reciverid)
                ->whereBetween('created_at', [$firstDateOfWeek . " 00:00:00", $lastdateOfWeek . " 23:59:59"])->get();
            $message = "The free chat option is available once a week only! Don't miss a week of chatting with Lloyd - subscribe to premium.";
        } else {
            $currentDate = date('Y-m-d');
            $message_list = User_prediction::where('sender_id', $user->id)->where('receiver_id', $reciverid)
                ->where('created_at', 'LIKE', '%' . $currentDate . '%')->get();
            $message = "The daily chat option for today is exhausted! Stay connected and chat tomorrow - come back soon.";
        }
        if (count($message_list) != 0) {
            return response()->json([
                'status' => 0,
                'message' => $message,
                'subscription_status' => $user->subscription_status,
            ]);
        } else {
            $userprediction = new User_prediction();
            $userprediction->sender_id = $userid;
            $userprediction->receiver_id = $reciverid;
            $userprediction->message = $sendmessage;
            $userprediction->save();
            return response()->json([
                'status' => 1,
                'message' => 'success',
                'subscription_status' => $user->subscription_status,
            ]);
        }
    }

    public function seenunseenmessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required',
            'seenstatus' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $userid = $request->userid;
        $message_seenstatus = $request->seenstatus;
        $user = User::find($userid);
        $messages = User_prediction::where('receiver_id', '=', $userid)->get();
        if ($messages) {
            foreach ($messages as $message) {
                $message->is_seen = $message_seenstatus;
                $message->save();
            }
            return response()->json([
                'status' => 1,
                'message' => 'success',
                'subscription_status' => $user->subscription_status,
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Error'
            ]);
        }
    }

    public function likedislikemessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'messageid' => 'required',
            'likestatus' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $messageid = $request->messageid;
        $message_likestatus = $request->likestatus;

        $message = User_prediction::find($messageid);
        if ($message) {

            $message->is_like = $message_likestatus;
            $message->save();

            return response()->json([
                'status' => 1,
                'message' => 'success'
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Error'
            ]);
        }
    }
    public function videolist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $data = $request->data;



        if ($data == 'all') {

            //echo $data;
            $videolist = Video::select('video_title', 'video_link', 'video_code')->get();

            return response()->json([
                'status' => 1,
                'message' => 'success',
                'video_list' => $videolist,
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Error',
                'video_list' => '',

            ]);
        }
    }

    public function usertravelpossibility(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required',
            'going_for' => 'required',
            'date_from' => 'required|date',
            'date_to' => 'required|date',
            'check_date' => 'required|date'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $userid = $request->userid;
        $going_for = $request->going_for;
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $str_date_from = strtotime($date_from);
        $str_date_to = strtotime($date_to);

        $format_date_from = date('m-d-Y', $str_date_from);
        $format_date_to = date('m-d-Y', $str_date_to);

        $check_date = $request->check_date;
        $explodeCheck_date = explode('-', $check_date);
        $check_year = $explodeCheck_date[0];
        $check_month = $explodeCheck_date[1];
        $check_day = $explodeCheck_date[2];

        $user = User::find($userid);
        if ($user) {
            $firstDateOfWeek = date("Y-m-d", strtotime('monday this week'));
            $lastdateOfWeek = date("Y-m-d", strtotime('sunday this week'));
            $traveldata = User_travel::where('user_id', $userid)->whereBetween('created_at', [$firstDateOfWeek . " 00:00:00", $lastdateOfWeek . " 23:59:59"])->get();
            // return $traveldata;
            if (count($traveldata) == 0 && $user->subscription_status == 0) {
                $message = "You have only Two compatibility check left in this week.";
            } elseif (count($traveldata) == 0 && $user->subscription_status == 2) {
                $message = "You have only Two compatibility check left in this week.";
            }elseif (count($traveldata) == 1 && $user->subscription_status == 0) {
                $message = "You have only One compatibility check left in this week.";
            } elseif (count($traveldata) == 1 && $user->subscription_status == 2) {
                $message = "You have only One compatibility check left in this week.";
            }elseif (count($traveldata) == 2 && $user->subscription_status == 0) {
                $message = "You don't have any compatibility check left in this week. Please wait for next week Or Upgrade and Subscribe to Unlock Premium Features!";
            } elseif (count($traveldata) == 2 && $user->subscription_status == 2) {
                $message = "You don't have any compatibility checks left this week. Please wait for next week Or Your monthly plan has been paused. Please resume and unlock premium features!";
            } else {
                $message = "Sucess";
            }
            if (count($traveldata) >= 3 && $user->subscription_status == 0) {
                return response()->json([
                    'status' => 0,
                    'message' => "You don't have any compatibility check left in this week. Please wait for next week Or Upgrade and Subscribe to Unlock Premium Features!",
                    'subscription_status' => $user->subscription_status,
                ]);
            }elseif (count($traveldata) >= 3 && $user->subscription_status == 2) {
                return response()->json([
                    'status' => 0,
                    'message' => "You don't have any compatibility checks left this week. Please wait for next week Or Your monthly plan has been paused. Please resume and unlock premium features!",
                    'subscription_status' => $user->subscription_status,
                ]);
            } else {

                if ($going_for == 1) {
                    $going_fortitle = 'New places Trip';
                } elseif ($going_for == 5) {
                    $going_fortitle = 'Short/Weekend Trip';
                } elseif ($going_for == 7) {
                    $going_fortitle = 'Exotic/Family Trip';
                } elseif ($going_for == 9) {
                    $going_fortitle = 'International/Business Trip';
                } else {
                    return response()->json([
                        'status' => 0,
                        'message' => 'Going for type error'
                    ]);
                }

                $savetraveldata = User_travel::create([
                    'user_id' => $userid,
                    'type' => $going_for,
                    'going_for' => $going_fortitle,
                    'date_from' => $date_from,
                    'date_to' => $date_to
                ]);

                $userdob = $user->dob;
                $dobdate = explode('-', $userdob);
                $dobmonth = $dobdate[1];
                $dobday = $dobdate[2];

                $checkfavnumbers = Fav_unfav_parameter::select('numbers')->where('type', 1)
                    ->where('month_id', $dobmonth)
                    ->where('date', $dobday)
                    ->first();
                $favnumbers = str_replace(',', ', ', $checkfavnumbers->numbers);
                if ($savetraveldata) {

                    $strdatefrom = strtotime($date_from);
                    $strdateto = strtotime($date_to);
                    if ($strdatefrom != $strdateto) {
                        $diff = $strdateto - $strdatefrom;
                        $dayscount = round($diff / 86400);
                    } else {
                        $dayscount = 1;
                    }

                    if ($dayscount <= 7) {
                        $explodedob = explode('-', $user->dob);
                        $day_monthsum = $explodedob[1] + $explodedob[2] + $check_year;

                        while (strlen($day_monthsum) != 1) {
                            $splitnumber = str_split($day_monthsum);
                            $arraysumnumber = array_sum($splitnumber);
                            $day_monthsum = $arraysumnumber;
                        }

                        $current_monthno = $check_month;

                        while (strlen($current_monthno) != 1) {
                            $splitcurrent_monthno = str_split($current_monthno);
                            $arraysumcurrent_monthno = array_sum($splitcurrent_monthno);
                            $current_monthno = $arraysumcurrent_monthno;
                        }

                        $personal_monthno = $day_monthsum + $current_monthno;

                        $current_day = $check_day;
                        $week1 = array(1, 2, 3, 4, 5, 6, 7);
                        if (in_array($current_day, $week1)) {
                            $weekno = 1;
                        }
                        $week2 = array(8, 9, 10, 11, 12, 13, 14);
                        if (in_array($current_day, $week2)) {
                            $weekno = 2;
                        }
                        $week3 = array(15, 16, 17, 18, 19, 20, 21);
                        if (in_array($current_day, $week3)) {
                            $weekno = 3;
                        }
                        $week4 = array(22, 23, 24, 25, 26, 27, 28,29, 30, 31);
                        if (in_array($current_day, $week4)) {
                            $weekno = 4;
                        }
                        $personal_weekno =  $personal_monthno + $weekno;
                        while (strlen($personal_weekno) != 1) {
                            $splitpersonal_weekno = str_split($personal_weekno);
                            $arraysumpersonal_weekno = array_sum($splitpersonal_weekno);
                            $personal_weekno = $arraysumpersonal_weekno;
                        }

                        if ($going_for == $personal_weekno) 
                        {
                            if ($personal_weekno == 1) {
                                //$travelpossibilitydesc = 'This is good time to go for New places trip.';
                                $travelpossibilitydesc = $user->name . ", as per the numerology calculations, the travel dates selected by you for " . $going_fortitle . "  from " . $format_date_from . "  to " . $format_date_to . " are perfectly suited to you. Your favourable numbers which is " . $favnumbers . " suggests, It nourishes your personal and professional life. There is a chance to experience and explore new things and do many more adventurous activities. You will boost your confidence to meet with different people at different places.";
                            } elseif ($personal_weekno == 5) {
                                //$travelpossibilitydesc = 'This is good time to go for Short/Weekend Trip.';
                                $travelpossibilitydesc = $user->name . " Numerology predicts that the choice of travel dates for " . $going_fortitle . " from " . $format_date_from . " to " . $format_date_to . " is perfectly compatible with you. Your favourable numbers which is " . $favnumbers . " is full of exciting adventures, new experiences, and relaxing in paradise for you. You can make this time more enjoyable by doing adventurous or funny activities. " . $going_fortitle . " will be the most memorable trip for you and your close ones.";
                            } elseif ($personal_weekno == 7) {
                                //$travelpossibilitydesc = 'This is good time to go for Exotic/Family Trip.';
                                $travelpossibilitydesc = $user->name . " according to the calculation of numbers, spiritual vibrations envisioned for the " . $going_fortitle . " from " . $format_date_from . " to " . $format_date_to . " are amazingly fit with favourable numbers which " . $favnumbers . ". These days you can enjoy joyful moments with your family and learn new skills.  While exploring the places, you come across many new life experiences and the bond you share with people will be stronger. On " . $going_fortitle . ", you will discover the world's best off-the-beaten-track places and experiences.";
                            } elseif ($personal_weekno == 9) {
                                //$travelpossibilitydesc = 'This is good time to go for International/Business Trip.';
                                $travelpossibilitydesc = $user->name . ", according to the numerology calculations, traveling dates for " . $going_fortitle . "  from " . $format_date_from . " to " . $format_date_to . " is a perfect fit with your favourable numbers which " . $favnumbers . ". On " . $going_fortitle . ", you will experience a different culture, learn new skills and different ways of working and boost your confidence. It can be a great perk for your trip. It also affords you great networking opportunities to help broaden your network and progress your career.";
                            } else {
                                $travelpossibilitydesc = 'personal_weekno - true Do not plan any trip at this time.';
                            }
                        } else 
                        {
                            if ($going_for == 1) {
                                //$travelpossibilitydesc = 'This is not a good time to go for '.$going_fortitle.'. But you can plan for New places trip.';
                                $travelpossibilitydesc = $user->name . ", as per numerology predictions, the above-selected travel dates for " . $going_fortitle . "  from " . $format_date_from . " to " . $format_date_to . " are not adequate as per your favourable numbers which " . $favnumbers . ". If you want to explore things smoothly without any complications, then you have to plan your New places trip. So, plan wisely if you make your travel more enjoyable and at ease.";
                            } elseif ($going_for == 5) {
                                //$travelpossibilitydesc = 'This is not a good time to go for '.$going_fortitle.'. But you can plan for Short/Weekend Trip.';
                                $travelpossibilitydesc = $user->name . ", Numerology calculations predict that the " . $going_fortitle . " is not compatible with your favourable numbers which " . $favnumbers . ". Due to some mismatching with selected dates from " . $format_date_from . " to " . $format_date_to . " could be much more expensive for you than expected. To avoid an expensive and frustrating trip, try to plan your Short/Weekend Trip. It could be more beneficial for you either with family or friends.";
                            } elseif ($going_for == 7) {
                                //$travelpossibilitydesc = 'This is not a good time to go for '.$going_fortitle.'. But you can plan for Exotic/Family Trip.';
                                $travelpossibilitydesc = $user->name . ", Numerology predictions put a stop to going on a " . $going_fortitle . " from " . $format_date_from . " to " . $format_date_to . " as per your favourable numbers which " . $favnumbers . ". If you plan a trip on these selected dates you might have to face some difficulties in your professional and personal life. Try to plan your Exotic/Family Trip, it seems to be great for you and gives you a more comfy experience.";
                            } elseif ($going_for == 9) {
                                //$travelpossibilitydesc = 'This is not a good time to go for '.$going_fortitle.'. But you can plan for International/Business Trip.';
                                $travelpossibilitydesc = $user->name . ", as per numeric calculations, when you start your travel for " . $going_fortitle . " from " . $format_date_from . " to " . $format_date_to . " you will face many difficulties as per your your favourable numbers which " . $favnumbers . " which will lead you to a very stressful situation. As per your fav number, plan your International/Business Trip. If you plan your trip wisely, you will enjoy every little moment on the trip.";
                            } else {
                                $travelpossibilitydesc = 'personal_weekno false Do not plan any trip at this time.';
                            }
                        }
                    } elseif ($dayscount <= 30) 
                    {
                        $userdob = $user->dob;
                        $dobdate = explode('-', $userdob);
                        $dobnumber = $dobdate[1] + $dobdate[2] + $check_year;
                        while (strlen($dobnumber) != 1) {
                            $splitdobnumber = str_split($dobnumber);
                            $arraysumdobnumber = array_sum($splitdobnumber);
                            $dobnumber = $arraysumdobnumber;
                        }
                        $current_monthno = $check_month;
                        while (strlen($current_monthno) != 1) {
                            $splitcurrent_monthno = str_split($current_monthno);
                            $arraysumcurrent_monthno = array_sum($splitcurrent_monthno);
                            $current_monthno = $arraysumcurrent_monthno;
                        }
                        $personal_monthno = $dobnumber + $current_monthno;
                        while (strlen($personal_monthno) != 1) {
                            $splitpersonal_monthno = str_split($personal_monthno);
                            $arraysumpersonal_monthno = array_sum($splitpersonal_monthno);
                            $personal_monthno = $arraysumpersonal_monthno;
                        }
                        if ($going_for == $personal_monthno) {
                            if ($personal_monthno == 1) {
                                $travelpossibilitydesc = $user->name . ", as per the numerology calculations, the travel dates selected by you for " . $going_fortitle . "  from " . $format_date_from . "  to " . $format_date_to . " are perfectly suited to you. Your favourable numbers which is " . $favnumbers . " suggests, It nourishes your personal and professional life. There is a chance to experience and explore new things and do many more adventurous activities. You will boost your confidence to meet with different people at different places.";
                            } elseif ($personal_monthno == 5) {
                                $travelpossibilitydesc = $user->name . " Numerology predicts that the choice of travel dates for " . $going_fortitle . " from " . $format_date_from . " to " . $format_date_to . " is perfectly compatible with you. Your favourable numbers which is " . $favnumbers . " is full of exciting adventures, new experiences, and relaxing in paradise for you. You can make this time more enjoyable by doing adventurous or funny activities. " . $going_fortitle . " will be the most memorable trip for you and your close ones.";
                            } elseif ($personal_monthno == 7) {
                                $travelpossibilitydesc = $user->name . " according to the calculation of numbers, spiritual vibrations envisioned for the " . $going_fortitle . " from " . $format_date_from . " to " . $format_date_to . " are amazingly fit with favourable numbers which " . $favnumbers . ". These days you can enjoy joyful moments with your family and learn new skills.  While exploring the places, you come across many new life experiences and the bond you share with people will be stronger. On " . $going_fortitle . ", you will discover the world's best off-the-beaten-track places and experiences.";
                            } elseif ($personal_monthno == 9) {
                                // $travelpossibilitydesc = 'This is good time to go for International/Business Trip.';
                                $travelpossibilitydesc = $user->name . ", according to the numerology calculations, traveling dates for " . $going_fortitle . "  from " . $format_date_from . " to " . $format_date_to . " is a perfect fit with your favourable numbers which " . $favnumbers . ". On " . $going_fortitle . ", you will experience a different culture, learn new skills and different ways of working and boost your confidence. It can be a great perk for your trip. It also affords you great networking opportunities to help broaden your network and progress your career.";
                            } else {
                                $travelpossibilitydesc = 'personal_monthno - true Do not plan any trip at this time.';
                            }
                        } else {

                            if ($going_for == 1) {
                                //$travelpossibilitydesc = 'This is not a good time to go for '.$going_fortitle.'. But you can plan for New places trip.';
                                $travelpossibilitydesc = $user->name . ", as per numerology predictions, the above-selected travel dates for " . $going_fortitle . "  from " . $format_date_from . " to " . $format_date_to . " are not adequate as per your favourable numbers which " . $favnumbers . ". If you want to explore things smoothly without any complications, then you have to plan your New places trip. So, plan wisely if you make your travel more enjoyable and at ease.";
                            } elseif ($going_for == 5) {
                                //$travelpossibilitydesc = 'This is not a good time to go for '.$going_fortitle.'. But you can plan for Short/Weekend Trip.';
                                $travelpossibilitydesc = $user->name . ", Numerology calculations predict that the " . $going_fortitle . " is not compatible with your favourable numbers which " . $favnumbers . ". Due to some mismatching with selected dates from " . $format_date_from . " to " . $format_date_to . " could be much more expensive for you than expected. To avoid an expensive and frustrating trip, try to plan your Short/Weekend Trip. It could be more beneficial for you either with family or friends.";
                            } elseif ($going_for == 7) {
                                //$travelpossibilitydesc = 'This is not a good time to go for '.$going_fortitle.'. But you can plan for Exotic/Family Trip.';
                                $travelpossibilitydesc = $user->name . ", Numerology predictions put a stop to going on a " . $going_fortitle . " from " . $format_date_from . " to " . $format_date_to . " as per your favourable numbers which " . $favnumbers . ". If you plan a trip on these selected dates you might have to face some difficulties in your professional and personal life. Try to plan your Exotic/Family Trip, it seems to be great for you and gives you a more comfy experience.";
                            } elseif ($going_for == 9) {
                                //$travelpossibilitydesc = 'This is not a good time to go for '.$going_fortitle.'. But you can plan for International/Business Trip.';
                                $travelpossibilitydesc = $user->name . ", as per numeric calculations, when you start your travel for " . $going_fortitle . " from " . $format_date_from . " to " . $format_date_to . " you will face many difficulties as per your your favourable numbers which " . $favnumbers . " which will lead you to a very stressful situation. As per your fav number, plan your International/Business Trip. If you plan your trip wisely, you will enjoy every little moment on the trip.";
                            } else {
                                $travelpossibilitydesc = 'personal_monthno - false Do not plan any trip at this time.';
                            }
                        }
                    } else {
                        $userdob = $user->dob;
                        $dobdate = explode('-', $userdob);
                        $dobnumber = $dobdate[1] + $dobdate[2] + $check_year;
                        while (strlen($dobnumber) != 1) {
                            $splitdobnumber = str_split($dobnumber);
                            $arraysumdobnumber = array_sum($splitdobnumber);
                            $dobnumber = $arraysumdobnumber;
                        }

                        $personalyearno = $dobnumber;

                        if ($going_for == $personalyearno) {
                            if ($personalyearno == 1) {
                                $travelpossibilitydesc = $user->name . ", as per the numerology calculations, the travel dates selected by you for " . $going_fortitle . "  from " . $format_date_from . "  to " . $format_date_to . " are perfectly suited to you. Your favourable numbers which is " . $favnumbers . " suggests, It nourishes your personal and professional life. There is a chance to experience and explore new things and do many more adventurous activities. You will boost your confidence to meet with different people at different places.";
                            } elseif ($personalyearno == 5) {
                                $travelpossibilitydesc = $user->name . " Numerology predicts that the choice of travel dates for " . $going_fortitle . " from " . $format_date_from . " to " . $format_date_to . " is perfectly compatible with you. Your favourable numbers which is " . $favnumbers . " is full of exciting adventures, new experiences, and relaxing in paradise for you. You can make this time more enjoyable by doing adventurous or funny activities. " . $going_fortitle . " will be the most memorable trip for you and your close ones.";
                            } elseif ($personalyearno == 7) {

                                $travelpossibilitydesc = $user->name . " according to the calculation of numbers, spiritual vibrations envisioned for the " . $going_fortitle . " from " . $format_date_from . " to " . $format_date_to . " are amazingly fit with favourable numbers which " . $favnumbers . ". These days you can enjoy joyful moments with your family and learn new skills.  While exploring the places, you come across many new life experiences and the bond you share with people will be stronger. On " . $going_fortitle . ", you will discover the world's best off-the-beaten-track places and experiences.";
                            } elseif ($personalyearno == 9) {
                                // $travelpossibilitydesc = 'This is good time to go for International/Business Trip.';
                                $travelpossibilitydesc = $user->name . ", according to the numerology calculations, traveling dates for " . $going_fortitle . "  from " . $format_date_from . " to " . $format_date_to . " is a perfect fit with your favourable numbers which " . $favnumbers . ". On " . $going_fortitle . ", you will experience a different culture, learn new skills and different ways of working and boost your confidence. It can be a great perk for your trip. It also affords you great networking opportunities to help broaden your network and progress your career.";
                            } else {
                                $travelpossibilitydesc = 'personalyearno - true Do not plan any trip at this time.';
                            }
                        } else {
                            if ($going_for == 1) {
                                //$travelpossibilitydesc = 'This is not a good time to go for '.$going_fortitle.'. But you can plan for New places trip.';
                                $travelpossibilitydesc = $user->name . ", as per numerology predictions, the above-selected travel dates for " . $going_fortitle . "  from " . $format_date_from . " to " . $format_date_to . " are not adequate as per your favourable numbers which " . $favnumbers . ". If you want to explore things smoothly without any complications, then you have to plan your New places trip. So, plan wisely if you make your travel more enjoyable and at ease.";
                            } elseif ($going_for == 5) {
                                //$travelpossibilitydesc = 'This is not a good time to go for '.$going_fortitle.'. But you can plan for Short/Weekend Trip.';
                                $travelpossibilitydesc = $user->name . ", Numerology calculations predict that the " . $going_fortitle . " is not compatible with your favourable numbers which " . $favnumbers . ". Due to some mismatching with selected dates from " . $format_date_from . " to " . $format_date_to . " could be much more expensive for you than expected. To avoid an expensive and frustrating trip, try to plan your Short/Weekend Trip. It could be more beneficial for you either with family or friends.";
                            } elseif ($going_for == 7) {
                                //$travelpossibilitydesc = 'This is not a good time to go for '.$going_fortitle.'. But you can plan for Exotic/Family Trip.';
                                $travelpossibilitydesc = $user->name . ", Numerology predictions put a stop to going on a " . $going_fortitle . " from " . $format_date_from . " to " . $format_date_to . " as per your favourable numbers which " . $favnumbers . ". If you plan a trip on these selected dates you might have to face some difficulties in your professional and personal life. Try to plan your Exotic/Family Trip, it seems to be great for you and gives you a more comfy experience.";
                            } elseif ($going_for == 9) {
                                //$travelpossibilitydesc = 'This is not a good time to go for '.$going_fortitle.'. But you can plan for International/Business Trip.';
                                $travelpossibilitydesc = $user->name . ", as per numeric calculations, when you start your travel for " . $going_fortitle . " from " . $format_date_from . " to " . $format_date_to . " you will face many difficulties as per your your favourable numbers which " . $favnumbers . " which will lead you to a very stressful situation. As per your fav number, plan your International/Business Trip. If you plan your trip wisely, you will enjoy every little moment on the trip.";
                            } else {
                                $travelpossibilitydesc = 'personalyearno - false Do not plan any trip at this time.';
                            }
                        }
                    }

                    return response()->json([
                        'status' => 1,
                        'message' => $message,
                        'userid' => $userid,
                        'subscription_status' => $user->subscription_status,
                        'current_month' => $check_month,
                        'current_year' => $check_year,
                        'dayscount' => $dayscount,
                        'goingdate_from' => $date_from,
                        'goingdate_to' => $date_to,
                        'triptype' => $going_fortitle,
                        'travelpossibilitydesc' => $travelpossibilitydesc,
                        'fav_no' => $favnumbers
                    ]);
                } else {
                    return response()->json([
                        'status' => 0,
                        'message' => 'Error'
                    ]);
                }
            }
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'User not found'
            ]);
        }
    }

    public function sharepersonreading(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required',
            'email' => 'required',
            'otherpersonid' => 'required',
            'module_type' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $userid = $request->userid;
        $personemail = $request->email;
        $otherpersonid = $request->otherpersonid;
        $module_type = $request->module_type;
        $user = User::find($userid);
        if ($module_type == 1) {
            $otherpersoninfo = User_namereading::find($otherpersonid);
            if ($otherpersoninfo) {
                $sharerocord = Share_data::create([
                    'module_type' => $module_type,
                    'user_id' => $userid,
                    'otheruser_id' => $otherpersonid,
                    'email' => $personemail
                ]);

                if ($sharerocord) {

                    $otherpersondob = $otherpersoninfo->dob;
                    $date = explode('-', $otherpersondob);
                    $daydate = $date[2];
                    $month = $date[1];
                    $year = $date[0];

                    $day = str_split($daydate, 1);
                    $birthno = array_sum($day);
                    $birthno = intval($birthno);
                    while (strlen($birthno) != 1) {
                        $birthno = str_split($birthno);
                        $birthno = array_sum($birthno);
                    }
                    $splitmonth = str_split($month, 1);
                    $monthno = array_sum($splitmonth);
                    while (strlen($monthno) != 1) {
                        $monthno = str_split($monthno);
                        $monthno = array_sum($monthno);
                    }

                    $splityear = str_split($year, 1);
                    $yearno = array_sum($splityear);
                    while (strlen($yearno) != 1) {
                        $yearno = str_split($yearno);
                        $yearno = array_sum($yearno);
                    }
                    $yearno = intval($yearno);

                    $destiny_no = $birthno + $monthno + $yearno;
                    while (strlen($destiny_no) != 1) {
                        $destiny_no = str_split($destiny_no);
                        $destiny_no = array_sum($destiny_no);
                    }

                    $dobdate = date("d-F-Y", strtotime($otherpersondob));
                    $date = explode('-', $dobdate);
                    $day = $date[0];
                    $month = $date[1];
                    $zodic = Zodic_sign::where('title', 'LIKE', '%' . $month . '%')->get();
                    if ($month == "March") {
                        $titledaydate = $zodic[1]->title;
                        $explodetitle_daydate = explode(' ', $titledaydate);
                        $title_daydate = $explodetitle_daydate[2];

                        if ($day <= $title_daydate) {
                            $zodicdata = $zodic[1];
                        } else {
                            $zodicdata = $zodic[0];
                        }
                    } else {
                        $titledaydate = $zodic[0]->title;
                        $explodetitle_daydate = explode(' ', $titledaydate);
                        $title_daydate = $explodetitle_daydate[2];
                        if ($day <= $title_daydate) {

                            $zodicdata = $zodic[0];
                        } else {
                            $zodicdata = $zodic[1];
                        }
                    }

                    if ($otherpersoninfo) {
                        $subject = 'ASTAR8: Share the Reading';
                        $from = "notification@designersx.us";
                        $msg = "$otherpersoninfo->name. your destiny number is .$destiny_no. and your zodiac sign is .$zodicdata->zodic_sign. <b></b>";
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                            CURLOPT_URL => "https://api.smtp2go.com/v3/email/send",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => "",
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 30000,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "POST",
                            CURLOPT_POSTFIELDS => "{\r\n    \"api_key\": \"api-22898DD4CFC911EDB2B6F23C91C88F4E\",\r\n    \"to\": [\"$personemail>\"],\r\n    \"sender\": \"$from\",\r\n    \"subject\": \"$subject\",\r\n    \"text_body\": \"$msg\",\r\n    \"html_body\": \"$msg\",\r\n    \"custom_headers\": [\r\n      {\r\n        \"header\": \"Reply-To\",\r\n        \"value\": \"$from>\"\r\n      }\r\n    ]\r\n}",
                            CURLOPT_HTTPHEADER => array(
                                // Set here Laravel Post required headers
                                "cache-control: no-cache",
                                "content-type: application/json"
                            ),
                        ));
                        $results = curl_exec($curl);
                        $resErrors = curl_error($curl);

                        curl_close($curl);
                        return response()->json([
                            'status' => 1,
                            'message' => 'success',
                            'subscription_status' => $user->subscription_status,
                            'otherpersondetail' => $otherpersoninfo->name . " your destiny number is " . $destiny_no . " and your zodiac sign is " . $zodicdata->zodic_sign,
                        ]);
                    }
                }
            } else {

                return response()->json([
                    'status' => 0,
                    'message' => 'Error',
                ]);
            }
        } elseif ($module_type == 2) {
            $otherpersoninfo = User_compatiblecheck::find($otherpersonid);
            if ($otherpersoninfo) {
                $sharerocord = Share_data::create([
                    'module_type' => $module_type,
                    'user_id' => $userid,
                    'otheruser_id' => $otherpersonid,
                    'email' => $personemail
                ]);

                if ($sharerocord) {

                    $otherpersoninfo = User_compatiblecheck::find($otherpersonid);
                    $otherpersondob = $otherpersoninfo->dates;
                    $date = explode('-', $otherpersondob);
                    $daydate = $date[2];
                    $month = $date[1];
                    $year = $date[0];

                    $day = str_split($daydate, 1);
                    $birthno = array_sum($day);
                    $birthno = intval($birthno);
                    while (strlen($birthno) != 1) {
                        $birthno = str_split($birthno);
                        $birthno = array_sum($birthno);
                    }

                    $splitmonth = str_split($month, 1);
                    $monthno = array_sum($splitmonth);
                    while (strlen($monthno) != 1) {
                        $monthno = str_split($monthno);
                        $monthno = array_sum($monthno);
                    }

                    $splityear = str_split($year, 1);
                    $yearno = array_sum($splityear);
                    while (strlen($yearno) != 1) {
                        $yearno = str_split($yearno);
                        $yearno = array_sum($yearno);
                    }
                    $yearno = intval($yearno);
                    $destiny_no = $birthno + $monthno + $yearno;
                    while (strlen($destiny_no) != 1) {
                        $destiny_no = str_split($destiny_no);
                        $destiny_no = array_sum($destiny_no);
                    }

                    $dobdate = date("d-F-Y", strtotime($otherpersondob));
                    $date = explode('-', $dobdate);
                    $day = $date[0];
                    $month = $date[1];
                    $zodic = Zodic_sign::where('title', 'LIKE', '%' . $month . '%')->get();

                    if ($month == "March") {
                        $titledaydate = $zodic[1]->title;
                        $explodetitle_daydate = explode(' ', $titledaydate);
                        $title_daydate = $explodetitle_daydate[2];

                        if ($day <= $title_daydate) {
                            $zodicdata = $zodic[1];
                        } else {
                            $zodicdata = $zodic[0];
                        }
                    } else {
                        $titledaydate = $zodic[0]->title;
                        $explodetitle_daydate = explode(' ', $titledaydate);
                        $title_daydate = $explodetitle_daydate[2];

                        if ($day <= $title_daydate) {
                            $zodicdata = $zodic[0];
                        } else {
                            $zodicdata = $zodic[1];
                        }
                    }
                    if ($otherpersoninfo) {
                        $subject = 'ASTAR8: Share the Reading';
                        $from = "notification@designersx.us";
                        $msg = "$otherpersoninfo->name. your destiny number is .$destiny_no. and your zodiac sign is .$zodicdata->zodic_sign. <b></b>";
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                            CURLOPT_URL => "https://api.smtp2go.com/v3/email/send",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => "",
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 30000,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "POST",
                            CURLOPT_POSTFIELDS => "{\r\n    \"api_key\": \"api-22898DD4CFC911EDB2B6F23C91C88F4E\",\r\n    \"to\": [\"$personemail>\"],\r\n    \"sender\": \"$from\",\r\n    \"subject\": \"$subject\",\r\n    \"text_body\": \"$msg\",\r\n    \"html_body\": \"$msg\",\r\n    \"custom_headers\": [\r\n      {\r\n        \"header\": \"Reply-To\",\r\n        \"value\": \"$from>\"\r\n      }\r\n    ]\r\n}",
                            CURLOPT_HTTPHEADER => array(
                                // Set here Laravel Post required headers
                                "cache-control: no-cache",
                                "content-type: application/json"
                            ),
                        ));
                        $results = curl_exec($curl);
                        $resErrors = curl_error($curl);

                        curl_close($curl);

                        return response()->json([
                            'status' => 1,
                            'message' => 'success',
                            'subscription_status' => $user->subscription_status,
                            'otherpersondetail' => $otherpersoninfo->name . " your destiny number is " . $destiny_no . " and your zodiac sign is " . $zodicdata->zodic_sign,
                        ]);
                    }
                }
            } else {

                return response()->json([
                    'status' => 0,
                    'message' => 'Error',
                ]);
            }
        } elseif ($module_type == 3) {
            $otherpersoninfo = User_compatiblecheck::find($otherpersonid);
            if ($otherpersoninfo) {
                $sharerocord = Share_data::create([
                    'module_type' => $module_type,
                    'user_id' => $userid,
                    'otheruser_id' => $otherpersonid,
                    'email' => $personemail
                ]);

                if ($sharerocord) {
                    $otherpersoninfo = User_compatiblecheck::find($otherpersonid);
                    $otherpersondob = $otherpersoninfo->dates;
                    $date = explode('-', $otherpersondob);
                    $daydate = $date[2];
                    $month = $date[1];
                    $year = $date[0];

                    $day = str_split($daydate, 1);
                    $birthno = array_sum($day);
                    $birthno = intval($birthno);
                    while (strlen($birthno) != 1) {
                        $birthno = str_split($birthno);
                        $birthno = array_sum($birthno);
                    }

                    $splitmonth = str_split($month, 1);
                    $monthno = array_sum($splitmonth);
                    while (strlen($monthno) != 1) {
                        $monthno = str_split($monthno);
                        $monthno = array_sum($monthno);
                    }

                    $splityear = str_split($year, 1);
                    $yearno = array_sum($splityear);
                    while (strlen($yearno) != 1) {
                        $yearno = str_split($yearno);
                        $yearno = array_sum($yearno);
                    }
                    $yearno = intval($yearno);
                    $destiny_no = $birthno + $monthno + $yearno;
                    while (strlen($destiny_no) != 1) {
                        $destiny_no = str_split($destiny_no);
                        $destiny_no = array_sum($destiny_no);
                    }

                    $dobdate = date("d-F-Y", strtotime($otherpersondob));
                    $date = explode('-', $dobdate);
                    $day = $date[0];
                    $month = $date[1];
                    $zodic = Zodic_sign::where('title', 'LIKE', '%' . $month . '%')->get();

                    if ($month == "March") {
                        $titledaydate = $zodic[1]->title;
                        $explodetitle_daydate = explode(' ', $titledaydate);
                        $title_daydate = $explodetitle_daydate[2];

                        if ($day <= $title_daydate) {
                            $zodicdata = $zodic[1];
                        } else {
                            $zodicdata = $zodic[0];
                        }
                    } else {
                        $titledaydate = $zodic[0]->title;
                        $explodetitle_daydate = explode(' ', $titledaydate);
                        $title_daydate = $explodetitle_daydate[2];

                        if ($day <= $title_daydate) {
                            $zodicdata = $zodic[0];
                        } else {
                            $zodicdata = $zodic[1];
                        }
                    }
                    if ($otherpersoninfo) {
                        $subject = 'ASTAR8: Share the Reading';
                        $from = "notification@designersx.us";
                        $msg = "$otherpersoninfo->name. your destiny number is .$destiny_no. and your zodiac sign is .$zodicdata->zodic_sign. <b></b>";
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                            CURLOPT_URL => "https://api.smtp2go.com/v3/email/send",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => "",
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 30000,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "POST",
                            CURLOPT_POSTFIELDS => "{\r\n    \"api_key\": \"api-22898DD4CFC911EDB2B6F23C91C88F4E\",\r\n    \"to\": [\"$personemail>\"],\r\n    \"sender\": \"$from\",\r\n    \"subject\": \"$subject\",\r\n    \"text_body\": \"$msg\",\r\n    \"html_body\": \"$msg\",\r\n    \"custom_headers\": [\r\n      {\r\n        \"header\": \"Reply-To\",\r\n        \"value\": \"$from>\"\r\n      }\r\n    ]\r\n}",
                            CURLOPT_HTTPHEADER => array(
                                // Set here Laravel Post required headers
                                "cache-control: no-cache",
                                "content-type: application/json"
                            ),
                        ));
                        $results = curl_exec($curl);
                        $resErrors = curl_error($curl);

                        curl_close($curl);

                        return response()->json([
                            'status' => 1,
                            'message' => 'success',
                            'subscription_status' => $user->subscription_status,
                            'otherpersondetail' => $otherpersoninfo->name . " your destiny number is " . $destiny_no . " and your zodiac sign is " . $zodicdata->zodic_sign,
                        ]);
                    }
                }
            } else {

                return response()->json([
                    'status' => 0,
                    'message' => 'Error',
                ]);
            }
        } else {

            return response()->json([
                'status' => 0,
                'message' => 'Error',
            ]);
        }
    }

    public function lifecoachcosmiccalender(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required',
            'date' => 'required',
            'current_date' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $userid = $request->userid;
        $req_date = $request->date;
        $current_date = $request->current_date;
        $user = User::find($userid);

        if ($user) {
            $joiningdate = $user->created_at->format('Y-m-d');
            $currentdate = $current_date;
            $explodeCurrentdate = explode('-', $currentdate);
            $currentDate_year = $explodeCurrentdate[0];

            if ($req_date < $joiningdate) {
                return response()->json([
                    'status' => 0,
                    'message' => 'You do not have any paid version.',
                ]);
            } elseif ($req_date > $currentdate && $user->subscription_status == 0) {
                return response()->json([
                    'status' => 0,
                    'message' => 'You do not have any paid version.',
                ]);
            } elseif ($req_date > $currentdate && $user->subscription_status == 2) {
                return response()->json([
                    'status' => 0,
                    'message' => 'You do not have any paid version.',
                ]);
            } else {

                $userdob = $user->dob;
                $explode_dob = explode("-", $userdob);
                $dob_date = $explode_dob[2];
                $dob_month = $explode_dob[1];

                $favdata = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 1)
                    ->where('month_id', $dob_month)
                    ->where('date', $dob_date)
                    ->first();
                $unfavdata = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 2)
                    ->where('month_id', $dob_month)
                    ->where('date', $dob_date)
                    ->first();

                $fav_dates = $favdata->numbers;
                $str_replace_fav_dates = str_replace(' ', '', $fav_dates);
                $explode_fav_dates = explode(',', $str_replace_fav_dates);
                $fav_dates_array = $explode_fav_dates;

                $fav_days = $favdata->days;
                $str_replace_fav_days = str_replace(' ', '', $fav_days);
                $explode_fav_days = explode(',', $str_replace_fav_days);
                $array_fav_days = $explode_fav_days;

                $fav_months = $favdata->months;
                $str_replace_fav_months = str_replace(' ', '', $fav_months);
                $explode_fav_months = explode(',', $str_replace_fav_months);
                $fav_months_array = $explode_fav_months;

                $unfav_dates = $unfavdata->numbers;
                $unfav_dates = str_replace(' ', '', $unfav_dates);
                $unfav_dates = explode(',', $unfav_dates);

                $unfav_days = $unfavdata->days;
                $unfav_days = str_replace(' ', '', $unfav_days);
                $unfav_days = explode(',', $unfav_days);

                $unfav_months = $unfavdata->months;
                $unfav_months = str_replace(' ', '', $unfav_months);
                $unfav_months = explode(',', $unfav_months);

                $dateformate = explode('-', date("D-j-M-Y", strtotime($req_date)));
                $date_split = str_split($dateformate[1], 1);
                $date_sum = array_sum($date_split);

                while (strlen($date_sum) != 1) {
                    $splitdate_sum = str_split($date_sum);
                    $date_array_sum = array_sum($splitdate_sum);
                    $date_sum = $date_array_sum;
                }
                $day_star = 0;
                $month_star = 0;
                if (in_array($date_sum, $fav_dates_array)) {
                    $date_star = 1;
                    if (in_array($dateformate[0], $array_fav_days)) {
                        $day_star = 1;
                    }
                    if (in_array($dateformate[2], $fav_months_array)) {
                        $month_star = 1;
                    }
                } else {
                    $date_star = 0;
                }
                $unfavday_star = 0;
                $unfavmonth_star = 0;
                if (in_array($date_sum, $unfav_dates)) {
                    $unfavdate_star = 1;
                    if (in_array($dateformate[0], $unfav_days)) {
                        $unfavday_star = 1;
                    }
                    if (in_array($dateformate[1], $unfav_months)) {
                        $unfavmonth_star = 1;
                    }
                } else {
                    $unfavdate_star = 0;
                }

                $currentdayfav_star = $date_star + $day_star + $month_star;
                $currentdayunfav_star = $unfavdate_star + $unfavday_star + $unfavmonth_star;

                $dobdate_array = array($dob_date, $dob_month, $currentDate_year);
                $dobdatenumber = array_sum($dobdate_array);
                while (strlen($dobdatenumber) != 1) {
                    $split_dobdatenumber = str_split($dobdatenumber);
                    $sum_dobdatenumber = array_sum($split_dobdatenumber);
                    $dobdatenumber = $sum_dobdatenumber;
                }
                $currentDate = $req_date;
                $explode_current_date = explode('-', $currentDate);
                $current_monthno = $explode_current_date[1];
                while (strlen($current_monthno) != 1) {
                    $split_current_monthno = str_split($current_monthno);
                    $sum_current_monthno = array_sum($split_current_monthno);
                    $current_monthno = $sum_current_monthno;
                }
                $cal_monthno = $dobdatenumber + $current_monthno;
                while (strlen($cal_monthno) != 1) {
                    $split_cal_monthno = str_split($cal_monthno);
                    $sum_cal_monthno = array_sum($split_cal_monthno);
                    $cal_monthno = $sum_cal_monthno;
                }
                $cal_daynumber = $explode_current_date[2];
                while (strlen($cal_daynumber) != 1) {
                    $split_cal_daynumber = str_split($cal_daynumber);
                    $sum_cal_daynumber = array_sum($split_cal_daynumber);
                    $cal_daynumber = $sum_cal_daynumber;
                }
                $personal_dayno = $cal_monthno + $cal_daynumber;
                while (strlen($personal_dayno) != 1) {
                    $split_personal_dayno = str_split($personal_dayno);
                    $sum_personal_dayno = array_sum($split_personal_dayno);
                    $personal_dayno = $sum_personal_dayno;
                }

                //zodiac Sign	
                $formetdob = date("d-F-Y", strtotime($user->dob));
                $zodiacdate = explode('-', $formetdob);
                $dobday = $zodiacdate[0];
                $dobmonth = $zodiacdate[1];
                $zodiac = Zodic_sign::where('title', 'LIKE', '%' . $dobmonth . '%')
                    ->get();

                if ($dobmonth == "March") {
                    $titledaydate = $zodiac[1]->title;
                    $explodetitle_daydate = explode(' ', $titledaydate);
                    $title_daydate = $explodetitle_daydate[2];

                    if ($dobday <= $title_daydate) {
                        $zodiacdata = $zodiac[1];
                    } else {
                        $zodiacdata = $zodiac[0];
                    }
                } else {
                    $titledaydate = $zodiac[0]->title;
                    $explodetitle_daydate = explode(' ', $titledaydate);
                    $title_daydate = $explodetitle_daydate[2];

                    if ($dobday <= $title_daydate) {
                        $zodiacdata = $zodiac[0];
                    } else {
                        $zodiacdata = $zodiac[1];
                    }
                }

                $user_zodiacsign = strtolower($zodiacdata->zodic_sign);
                $currentdate_formate = $current_date;

                if ($req_date == $currentdate_formate) {
                    $user_zodiacday = 'today';
                    $aztro = curl_init();
                    curl_setopt_array($aztro, array(
                        CURLOPT_URL => "https://aztro.sameerkumar.website/?sign=$user_zodiacsign&day=$user_zodiacday",
                        CURLOPT_POST => TRUE,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_HTTPHEADER => array(
                            'Content-Type: application/json'
                        )
                    ));
                    $response = curl_exec($aztro);
                    if ($response === FALSE) {
                        die(curl_error($aztro));
                    }
                    $responseData = json_decode($response, TRUE);
                    $user_zodiacsignliveapi = $responseData['description'];
                    $usercolorliveapi = $responseData['color'];
                    if ($currentdayfav_star != 0) {
                        $lifecoachtype = Dailycoach_type::where('type', $personal_dayno)->first();
                        $short_lifecoach_desc = 'Today is ' . $currentdayfav_star . ' star day for ' . $lifecoachtype->name . ".";
                        $lifecoach = 'Today is ' . $currentdayfav_star . ' star day for ' . $lifecoachtype->name . ". " . $user_zodiacsignliveapi;
                    } elseif ($currentdayunfav_star != 0) {
                        $lifecoachtype = Dailycoach_type::where('type', $personal_dayno)->first();
                        $short_lifecoach_desc = 'Today is not good for ' . $lifecoachtype->name . ".";
                        $lifecoach = 'Today is not good for ' . $lifecoachtype->name . ". " . $user_zodiacsignliveapi;
                    } else {
                        $lifecoachtype = Dailycoach_type::where('type', $personal_dayno)->first();
                        $short_lifecoach_desc = 'Today is good for ' . $lifecoachtype->name . ".";
                        $lifecoach = 'Today is good for ' . $lifecoachtype->name . ". " . $user_zodiacsignliveapi;
                    }
                } elseif ($req_date > $currentdate_formate) {
                    $usercolorliveapi = '';
                    if ($currentdayfav_star != 0) {
                        $lifecoachtype = Dailycoach_type::where('type', $personal_dayno)->first();
                        $short_lifecoach_desc = 'This day is ' . $currentdayfav_star . ' star day for ' . $lifecoachtype->name . '.';
                        $lifecoach = "On this day, your " . $currentdayfav_star . " will say,  you have a beneficial condition for " . $lifecoachtype->name . ". As of now, it is important to keep the routine organized.";
                    } elseif ($currentdayunfav_star != 0) {
                        $lifecoachtype = Dailycoach_type::where('type', $personal_dayno)->first();
                        $short_lifecoach_desc = 'This day is not good for ' . $lifecoachtype->name . ".";
                        $lifecoach = "This day will turn out to be a bad day for " . $lifecoachtype->name . ". Do not try to impose work on others due to laziness on this day.";
                    } else {
                        $lifecoachtype = Dailycoach_type::where('type', $personal_dayno)->first();
                        $short_lifecoach_desc = 'This day is good for ' . $lifecoachtype->name . ".";
                        $lifecoach = "On this day you will get the best results for " . $lifecoachtype->name . ". So make an outline of your tasks at the beginning of the day.";
                    }
                } else {
                    $usercolorliveapi = '';
                    if ($currentdayfav_star != 0) {
                        $lifecoachtype = Dailycoach_type::where('type', $personal_dayno)->first();
                        $short_lifecoach_desc = 'This day is ' . $currentdayfav_star . ' star day for ' . $lifecoachtype->name . ".";
                        $lifecoach = "This day was the day to make dreams come true for your " . $lifecoachtype->name . ". " . $currentdayfav_star . " say you will have the ability to accomplish any task with your determination.";
                    } elseif ($currentdayunfav_star != 0) {
                        $lifecoachtype = Dailycoach_type::where('type', $personal_dayno)->first();
                        $short_lifecoach_desc = 'This day is not good for ' . $lifecoachtype->name . ".";
                        $lifecoach = "This day strongly showed a red alert for " . $lifecoachtype->name . ". It was not a good day to start any new work. You might have faced health issues.";
                    } else {
                        $lifecoachtype = Dailycoach_type::where('type', $personal_dayno)->first();
                        $short_lifecoach_desc = 'This day is good for ' . $lifecoachtype->name . ".";
                        $lifecoach = "This day turned out to be a good day for " . $lifecoachtype->name . ".  By luck, the planetary position is not very beneficial, but there may be an improvement in it.";
                    }
                }
                return response()->json([
                    'status' => 1,
                    'message' => 'success',
                    'userid' => $userid,
                    'subscription_status' => $user->subscription_status,
                    'user_zodiacsign' => $user_zodiacsign,
                    'short_lifecoach_desc' => $short_lifecoach_desc,
                    'life_coach' => $lifecoach,
                    'personal_day' => $personal_dayno,
                    'color' => $usercolorliveapi,
                ]);
            }
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'User not found',
            ]);
        }
    }

    // Summi Ma'am Logic API's
    //update at 24-11-2022
    public function businesscompatibilitycheck(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $type = $request->type;
        // For Car
        if ($type == 3) // For Business
        {
            $validator = Validator::make($request->all(), [
                'userid' => 'required',
                'type' => 'required',
                'type_name' => 'required',
                'business_name' => 'required',
                'incorporation_date' => 'required',
                'partners' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }
            $userid = $request->userid;
            $type_name = $request->type_name;
            $business_name = $request->business_name;
            $incorporation_date = $request->incorporation_date;
            $no_of_partner = $request->pertners;

            $str_incorporation_date = strtotime($incorporation_date);
            $formate_incorporation_date = date('m-d-Y', $str_incorporation_date);

            $loginuser = User::find($userid);
            $firstDateOfWeek = date("Y-m-d", strtotime('monday this week'));
            $lastdateOfWeek = date("Y-m-d", strtotime('sunday this week'));

            $businessCompChecks = User_compatiblecheck::where('user_id', '=', $userid)->where('type', '=', 3)->whereBetween('created_at', [$firstDateOfWeek . " 00:00:00", $lastdateOfWeek . " 23:59:59"])->get();

            if (count($businessCompChecks) == 0 && $loginuser->subscription_status == 0) {
                $message = "You have only Two compatibility check left in this week.";
            } elseif (count($businessCompChecks) == 0 && $loginuser->subscription_status == 2) {
                $message = "You have only Two compatibility check left in this week.";
            }elseif (count($businessCompChecks) == 1 && $loginuser->subscription_status == 0) {
                $message = "You have only One compatibility check left in this week.";
            } elseif (count($businessCompChecks) == 1 && $loginuser->subscription_status == 2) {
                $message = "You have only One compatibility check left in this week.";
            }elseif (count($businessCompChecks) == 2 && $loginuser->subscription_status == 0) {
                $message = "You don't have any compatibility check left in this week. Please wait for next week Or Upgrade and Subscribe to Unlock Premium Features!";
            } elseif (count($businessCompChecks) == 2 && $loginuser->subscription_status == 2) {
                $message = "You don't have any compatibility checks left this week. Please wait for next week Or Your monthly plan has been paused. Please resume and unlock premium features!";
            } else {
                $message = "Sucess";
            }
            if (count($businessCompChecks) >= 3 && $loginuser->subscription_status == 0) {
                return response()->json([
                    'status' => 0,
                    'message' => "You don't have any compatibility check left in this week. Please wait for next week Or Upgrade and Subscribe to Unlock Premium Features!",
                    'subscription_status' => $loginuser->subscription_status,
                ]);
            }elseif (count($businessCompChecks) >= 3 && $loginuser->subscription_status == 2) {
                return response()->json([
                    'status' => 0,
                    'message' => "You don't have any compatibility checks left this week. Please wait for next week Or Your monthly plan has been paused. Please resume and unlock premium features!",
                    'subscription_status' => $loginuser->subscription_status,
                ]);
            } else {
                $compatibility = User_compatiblecheck::create([
                    'user_id' => $userid,
                    'type' => $type,
                    'type_name' => $type_name,
                    'name' => $business_name,
                    'type_dates' => 1,
                    'dates' => $incorporation_date,
                    'no_of_partner' => $no_of_partner,
                ]);

                //business name percentage
                $loginuserdob = $loginuser->dob;
                $dob_date = explode('-', $loginuserdob);
                $dob_day = $dob_date[2];
                $day = str_split($dob_day, 1);
                $dayno_sum = array_sum($day);
                $dayno = intval($dayno_sum);
                while (strlen($dayno) != 1) {
                    $str_dayno = str_split($dayno);
                    $sum_dayno = array_sum($str_dayno);
                    $dayno = $sum_dayno;
                }
                $dob_month = $dob_date[1];
                $month = str_split($dob_month, 1);
                $month_no = array_sum($month);
                $monthno = intval($month_no);
                if (strlen($monthno) != 1) {
                    $split_monthno = str_split($monthno);
                    $sum_monthno = array_sum($split_monthno);
                    $monthno = $sum_monthno;
                }
                $year = str_split($dob_date[0], 1);
                $yearno = array_sum($year);
                while (strlen($yearno) != 1) {
                    $splityearno = str_split($yearno);
                    $sum_yearno = array_sum($splityearno);
                    $yearno = $sum_yearno;
                }
                $yearno = intval($yearno);
                $destiny_no = $dayno + $monthno + $yearno;
                while (strlen($destiny_no) != 1) {
                    $splitdestiny_no = str_split($destiny_no);
                    $destiny_nosum = array_sum($splitdestiny_no);
                    $destiny_no = $destiny_nosum;
                }
                $strname = strtoupper($business_name);
                $names = explode(' ', $strname);
                $calculated_pythano = array();
                $calculated_chaldno = array();
                foreach ($names as $namewords) {
                    $letter = str_split($namewords);
                    $pytha_no = array();
                    $chald_no = array();
                    foreach ($letter as $letters) {
                        $pytha_number = Alphasystem_type::where('alphabet', 'LIKE', '%' . $letters . '%')
                            ->where('systemtype_id', 1)
                            ->value('number');
                        $chald_number = Alphasystem_type::where('alphabet', 'LIKE', '%' . $letters . '%')
                            ->where('systemtype_id', 2)

                            ->value('number');
                        array_push($pytha_no, $pytha_number);
                        array_push($chald_no, $chald_number);
                    }
                    $sum_pythano = array_sum($pytha_no);
                    $sum_chaldno = array_sum($chald_no);
                    while (strlen($sum_pythano) != 1 || strlen($sum_chaldno) != 1) {
                        $sum_split_pythano = str_split($sum_pythano, 1);
                        $sum_sum_pythano = array_sum($sum_split_pythano);
                        $sum_pythano = $sum_sum_pythano;
                        $sum_split_chaldno = str_split($sum_chaldno, 1);
                        $sum_sum_chaldno = array_sum($sum_split_chaldno);
                        $sum_chaldno = $sum_sum_chaldno;
                    }
                    array_push($calculated_pythano, $sum_pythano);
                    array_push($calculated_chaldno, $sum_chaldno);
                }
                $pythanumber = array_sum($calculated_pythano);
                $chaldnumber = array_sum($calculated_chaldno);
                while (strlen($chaldnumber) != 1 || strlen($pythanumber) != 1) {
                    $split_pythanumber = str_split($pythanumber);
                    $sum_pythanumber = array_sum($split_pythanumber);
                    $pythanumber = $sum_pythanumber;

                    $split_chaldnumber = str_split($chaldnumber);
                    $sum_chaldnumber = array_sum($split_chaldnumber);
                    $chaldnumber = $sum_chaldnumber;
                }
                $fav = Fav_unfav_parameter::where('type', 1)
                    ->where('month_id', $dob_month)
                    ->where('date', $dob_day)
                    ->value('numbers');
                $unfav = Fav_unfav_parameter::where('type', 2)
                    ->where('month_id', $dob_month)
                    ->where('date', $dob_day)
                    ->value('numbers');

                $fav_numbers = str_replace(' ', '', $fav);
                $favnumber_array = explode(',', $fav_numbers);
                $fav_arraycount = count($favnumber_array);

                $unfav_numbers = str_replace(' ', '', $unfav);
                $unfavnumber_array = explode(',', $unfav_numbers);
                $array_reverse_unfavnumber = array_reverse($unfavnumber_array);
                $unfav_arraycount = count($unfavnumber_array);

                $businessnamepercentage = 0;
                if ($dayno == $chaldnumber) {
                    $dobpercentage = 98;
                    $businessnamepercentage = 98;
                } else {
                    $dobpercentage = 0;
                }
                if ($destiny_no == $chaldnumber) {
                    $destinypercentage = 84;
                    $businessnamepercentage = 84;
                } else {
                    $destinypercentage = 0;
                }
                if ($fav_arraycount == 3) {
                    if ($favnumber_array[1] == $chaldnumber) {
                        $favpercentage = 66;
                        $businessnamepercentage = 66;
                    } elseif ($favnumber_array[2] == $chaldnumber) {
                        $favpercentage = 55;
                        $businessnamepercentage = 55;
                    } else {
                        $favpercentage = 0;
                    }
                } elseif ($fav_arraycount == 4) {
                    if ($favnumber_array[1] == $chaldnumber) {
                        $favpercentage = 74;
                        $businessnamepercentage = 74;
                    } elseif ($favnumber_array[2] == $chaldnumber) {
                        $favpercentage = 65;
                        $businessnamepercentage = 65;
                    } elseif ($favnumber_array[3] == $chaldnumber) {
                        $favpercentage = 55;
                        $businessnamepercentage = 55;
                    } else {
                        $favpercentage = 0;
                    }
                } else {
                    return response()->json([
                        'status' => 0,
                        'message' => 'fav number error'
                    ]);
                }
                if ($unfav_arraycount == 2) {
                    if ($array_reverse_unfavnumber[0] == $chaldnumber) {
                        $unfavpercentage = 30;
                        $businessnamepercentage = 30;
                    } elseif ($array_reverse_unfavnumber[1] == $chaldnumber) {
                        $unfavpercentage = 15;
                        $businessnamepercentage = 15;
                    } else {
                        $unfavpercentage = 0;
                    }
                } elseif ($unfav_arraycount == 3) {
                    if ($array_reverse_unfavnumber[0] == $chaldnumber) {
                        $unfavpercentage = 35;
                        $businessnamepercentage = 35;
                    } elseif ($array_reverse_unfavnumber[1] == $chaldnumber) {
                        $unfavpercentage = 23;
                        $businessnamepercentage = 23;
                    } elseif ($array_reverse_unfavnumber[2] == $chaldnumber) {
                        $unfavpercentage = 12;
                        $businessnamepercentage = 12;
                    } else {
                        $unfavpercentage = 0;
                    }
                } else {
                    return response()->json([
                        'status' => 0,
                        'message' => 'unfav number error'
                    ]);
                }
                if ($dobpercentage == 0 && $destinypercentage == 0 && $favpercentage == 0 && $unfavpercentage == 0) {
                    $businessnamepercentage = 50;
                }
                $businessnamefinalpercentage = $businessnamepercentage;

                if ($businessnamefinalpercentage == 35 || $businessnamefinalpercentage == 30 || $businessnamefinalpercentage == 23 || $businessnamefinalpercentage == 15 || $businessnamefinalpercentage == 12) {

                    $business_finaldesc = "Business name is not compatible for you. Please try with another business name";

                    $compatibilitycheck = array(
                        'type' => $type, 'business_name' => $business_name, 'final_compatibility_percentage' => $businessnamefinalpercentage, 'businessnamepercentage' => $businessnamefinalpercentage, 'first_compatibility_processingbar' => $businessnamefinalpercentage, 'second_compatibility_processingbar' => '', 'third_compatibility_processingbar' => '',
                        'businessdesc' => $business_finaldesc,
                    );

                    $exellent_desc = $loginuser->name . ", according to the calculation of numbers, Since " . $formate_incorporation_date . " there is a spiritual correlation between your Birth Number which is " . $dayno . ", " . $business_name . ", and " . $type_name . ". Your Birth Number " . $dayno . " is completely compatible with your " . $business_name . ", it results in rapid growth in the era of business. In no time you will get many business travels that also afford you great networking opportunities to help broaden your network and progress your career and bring bigger and better deals. Your Birth Number " . $dayno . " has strong compatibility with your business name and it results in amazing surprises.";
                    $good_desc = $loginuser->name . ", as per AstroNumeric law, Since " . $formate_incorporation_date . " your Birth Number which is " . $dayno . " has good compatibility with " . $business_name . " and " . $type_name . ". It seems like it takes some time for the astounding growth in the industrial environment. You can face a few ups and downs when you start competing with others, but the positive thing is that you never lose anything while competing. If your business is running on the low side then the compatible vibrations balance the frequency and again stable your business at the normal stage. ";
                    $bad_desc = $loginuser->name . ", as per numerology law, Since " . $formate_incorporation_date . " your Birth Number which is " . $dayno . " does not have much good compatibility with " . $business_name . " and " . $type_name . ". It seems like it will lower your quality of work day by day.  As per predictions, you can start your own business or with a partnership after a couple of years, then you have a chance to lead your business to the next level.";

                    return response()->json([
                        'status' => 1,
                        'message' => $message,
                        'userid' => $userid,
                        'subscription_status' => $loginuser->subscription_status,
                        'compatibilitydetail' => $compatibilitycheck,
                        'dob' => $loginuser->dob,
                        'username' => $loginuser->name,
                        'prime_no' => $dayno,
                        'incorporation_date' => $incorporation_date,
                        'exellent_desc' => $exellent_desc,
                        'good_desc' => $good_desc,
                        'bad_desc' => $bad_desc


                    ]);
                    return response()->json([
                        'status' => 1,
                        'message' => 'Success',

                    ]);
                } else {

                    //incorporation_date reading
                    $inc_date = explode('-', $incorporation_date);
                    $inc_day = str_split($inc_date[2], 1);
                    $cal_incdate_no = array_sum($inc_day);
                    $cal_incdateno = intval($cal_incdate_no);
                    while (strlen($cal_incdateno) != 1) {
                        $cal_incdateno = str_split($cal_incdateno);
                        $cal_incdateno = array_sum($cal_incdateno);
                    }

                    $fav = Fav_unfav_parameter::where('type', 1)
                        ->where('month_id', $dob_month)
                        ->where('date', $dob_day)
                        ->value('numbers');
                    $unfav = Fav_unfav_parameter::where('type', 2)
                        ->where('month_id', $dob_month)
                        ->where('date', $dob_day)
                        ->value('numbers');

                    $fav_numbers = str_replace(' ', '', $fav);
                    $favnumber_array = explode(',', $fav_numbers);
                    $fav_arraycount = count($favnumber_array);
    
                    $unfav_numbers = str_replace(' ', '', $unfav);
                    $unfavnumber_array = explode(',', $unfav_numbers);
                    $array_reverse_unfavnumber = array_reverse($unfavnumber_array);
                    $unfav_arraycount = count($unfavnumber_array);

                    $inc_date_finalperc = 0;
                    if ($dayno == $cal_incdateno) {
                        $inc_datedobpercentage = 98;
                        $inc_date_finalperc = 98;
                    } else {
                        $inc_datedobpercentage = 0;
                    }
                    if ($destiny_no == $cal_incdateno) {
                        $inc_datedestinypercentage = 84;
                        $inc_date_finalperc = 84;
                    } else {
                        $inc_datedestinypercentage = 0;
                    }
                    if ($fav_arraycount == 3) {
                        if ($favnumber_array[1] == $cal_incdateno) {
                            $inc_datefavpercentage = 66;
                            $inc_date_finalperc = 66;
                        } elseif ($favnumber_array[2] == $cal_incdateno) {
                            $inc_datefavpercentage = 55;
                            $inc_date_finalperc = 55;
                        } else {
                            $inc_datefavpercentage = 0;
                        }
                    } elseif ($fav_arraycount == 4) {
                        if ($favnumber_array[1] == $cal_incdateno) {
                            $inc_datefavpercentage = 74;
                            $inc_date_finalperc = 74;
                        } elseif ($favnumber_array[2] == $cal_incdateno) {
                            $inc_datefavpercentage = 65;
                            $inc_date_finalperc = 65;
                        } elseif ($favnumber_array[3] == $cal_incdateno) {
                            $inc_datefavpercentage = 55;
                            $inc_date_finalperc = 55;
                        } else {
                            $inc_datefavpercentage = 0;
                        }
                    } else {
                        return response()->json([
                            'status' => 0,
                            'message' => 'fav number error'
                        ]);
                    }

                    if ($unfav_arraycount == 2) {
                        if ($array_reverse_unfavnumber[0] == $cal_incdateno) {
                            $inc_dateunfavpercentage = 30;
                            $inc_date_finalperc = 30;
                        } elseif ($array_reverse_unfavnumber[1] == $cal_incdateno) {
                            $inc_dateunfavpercentage = 15;
                            $inc_date_finalperc = 15;
                        } else {
                            $inc_dateunfavpercentage = 0;
                        }
                    } elseif ($unfav_arraycount == 3) {
                        if ($array_reverse_unfavnumber[0] == $cal_incdateno) {
                            $inc_dateunfavpercentage = 35;
                            $inc_date_finalperc = 35;
                        } elseif ($array_reverse_unfavnumber[1] == $cal_incdateno) {
                            $inc_dateunfavpercentage = 23;
                            $inc_date_finalperc = 23;
                        } elseif ($array_reverse_unfavnumber[2] == $cal_incdateno) {
                            $inc_dateunfavpercentage = 12;
                            $inc_date_finalperc = 12;
                        } else {
                            $inc_dateunfavpercentage = 0;
                        }
                    } else {
                        return response()->json([
                            'status' => 0,
                            'message' => 'unfav number error'
                        ]);
                    }

                    if ($inc_datedobpercentage == 0 && $inc_datedestinypercentage == 0 && $inc_datefavpercentage == 0 && $inc_dateunfavpercentage == 0) {
                        $inc_date_finalperc = 50;
                    }
                    if ($inc_date_finalperc == 35 || $inc_date_finalperc == 30 || $inc_date_finalperc == 23 || $inc_date_finalperc == 15 || $inc_date_finalperc == 12) {
                        $numaric_businessnamefinalperc = $businessnamefinalpercentage;
                        $numaric_inc_date_finalperc = $inc_date_finalperc;

                        $final_compatibility_perc = ($numaric_businessnamefinalperc + $numaric_inc_date_finalperc) / 2;
                        $final_compatibility_percentage = round($final_compatibility_perc);
                        $business_finaldesc = "Business name is compatible for you. But incorporation_date is not compatible. Please try with another date.";
                        $compatibilitycheck = array(
                            'type' => $type, 'business_name' => $business_name, 'businessnamepercentage' => $businessnamefinalpercentage, 'incorporation_date_perc' => $inc_date_finalperc,
                            'first_compatibility_processingbar' => $businessnamefinalpercentage, 'second_compatibility_processingbar' => $inc_date_finalperc, 'third_compatibility_processingbar' => '',
                            'businessdesc' => $business_finaldesc, 'final_compatibility_percentage' => $final_compatibility_percentage
                        );
                        $exellent_desc = $loginuser->name . ", according to the calculation of numbers, Since " . $formate_incorporation_date . " there is a spiritual correlation between your Birth Number which is " . $dayno . ", " . $business_name . ", and " . $type_name . ". Your Birth Number " . $dayno . " is completely compatible with your " . $business_name . ", it results in rapid growth in the era of business. In no time you will get many business travels that also afford you great networking opportunities to help broaden your network and progress your career and bring bigger and better deals. Your Birth Number " . $dayno . " has strong compatibility with your business name and it results in amazing surprises.";
                        $good_desc = $loginuser->name . ", as per AstroNumeric law, Since " . $formate_incorporation_date . " your Birth Number which is " . $dayno . " has good compatibility with " . $business_name . " and " . $type_name . ". It seems like it takes some time for the astounding growth in the industrial environment. You can face a few ups and downs when you start competing with others, but the positive thing is that you never lose anything while competing. If your business is running on the low side then the compatible vibrations balance the frequency and again stable your business at the normal stage. ";
                        $bad_desc = $loginuser->name . ", as per numerology law, Since " . $formate_incorporation_date . " your Birth Number which is " . $dayno . " does not have much good compatibility with " . $business_name . " and " . $type_name . ". It seems like it will lower your quality of work day by day.  As per predictions, you can start your own business or with a partnership after a couple of years, then you have a chance to lead your business to the next level.";

                        return response()->json([
                            'status' => 1,
                            'message' => $message,
                            'userid' => $userid,
                            'subscription_status' => $loginuser->subscription_status,
                            'compatibilitydetail' => $compatibilitycheck,
                            'dob' => $loginuser->dob,
                            'username' => $loginuser->name,
                            'prime_no' => $dayno,
                            'incorporation_date' => $incorporation_date,
                            'exellent_desc' => $exellent_desc,
                            'good_desc' => $good_desc,
                            'bad_desc' => $bad_desc
                        ]);
                    } else {
                        $numaric_businessnamefinalperc = $businessnamefinalpercentage;
                        $numaric_inc_date_finalperc = $inc_date_finalperc;

                        $final_compatibility_perc = ($numaric_businessnamefinalperc + $numaric_inc_date_finalperc) / 2;
                        $final_compatibility_percentage = round($final_compatibility_perc);

                        $exellent_desc = $loginuser->name . ", according to the calculation of numbers, Since " . $formate_incorporation_date . " there is a spiritual correlation between your Birth Number which is " . $dayno . ", " . $business_name . ", and " . $type_name . ". Your Birth Number " . $dayno . " is completely compatible with your " . $business_name . ", it results in rapid growth in the era of business. In no time you will get many business travels that also afford you great networking opportunities to help broaden your network and progress your career and bring bigger and better deals. Your Birth Number " . $dayno . " has strong compatibility with your business name and it results in amazing surprises.";
                        $good_desc = $loginuser->name . ", as per AstroNumeric law, Since " . $formate_incorporation_date . " your Birth Number which is " . $dayno . " has good compatibility with " . $business_name . " and " . $type_name . ". It seems like it takes some time for the astounding growth in the industrial environment. You can face a few ups and downs when you start competing with others, but the positive thing is that you never lose anything while competing. If your business is running on the low side then the compatible vibrations balance the frequency and again stable your business at the normal stage. ";
                        $bad_desc = $loginuser->name . ", as per numerology law, Since " . $formate_incorporation_date . " your Birth Number which is " . $dayno . " does not have much good compatibility with " . $business_name . " and " . $type_name . ". It seems like it will lower your quality of work day by day.  As per predictions, you can start your own business or with a partnership after a couple of years, then you have a chance to lead your business to the next level.";

                        if ($final_compatibility_perc >= 70) {
                            $business_finaldesc = $exellent_desc;
                        }
                        if ($final_compatibility_perc <= 70 || $final_compatibility_perc <= 50) {
                            $business_finaldesc = $good_desc;
                        }
                        $compatibilitycheck = array(
                            'type' => $type, 'business_name' => $business_name, 'final_compatibility_percentage' => $final_compatibility_percentage,
                            'first_compatibility_processingbar' => $businessnamefinalpercentage, 'second_compatibility_processingbar' => $inc_date_finalperc, 'third_compatibility_processingbar' => '',
                            'businessdesc' => $business_finaldesc,
                        );
                        return response()->json([
                            'status' => 1,
                            'message' => $message,
                            'userid' => $userid,
                            'subscription_status' => $loginuser->subscription_status,
                            'compatibilitydetail' => $compatibilitycheck,
                            'dob' => $loginuser->dob,
                            'username' => $loginuser->name,
                            'prime_no' => $dayno,
                            'incorporation_date' => $incorporation_date,
                            'exellent_desc' => $exellent_desc,
                            'good_desc' => $good_desc,
                            'bad_desc' => $bad_desc
                        ]);
                    }
                }
            }
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Error',
            ]);
        }
    }

    //update at 24-11-2022
    public function propertycompatibilitycheck(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $type = $request->type;
        // For Car 
        if ($type == 4) // For Property
        {
            $validator = Validator::make($request->all(), [
                'userid' => 'required',
                'type' => 'required',
                'type_name' => 'required',
                'property_number' => 'required',
                'pin' => 'required',
                'city' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }
            $userid = $request->userid;
            $type_name = $request->type_name;
            $property_number = $request->property_number;
            $postalcode = $request->pin;
            $city = $request->city;
            $loginuser = User::find($userid);
            $firstDateOfWeek = date("Y-m-d", strtotime('monday this week'));
            $lastdateOfWeek = date("Y-m-d", strtotime('sunday this week'));

            $propertyCompChecks = User_compatiblecheck::where('user_id', '=', $userid)->where('type', '=', 4)
                ->whereBetween('created_at', [$firstDateOfWeek . " 00:00:00", $lastdateOfWeek . " 23:59:59"])->get();

                if (count($propertyCompChecks) == 0 && $loginuser->subscription_status == 0) {
                    $message = "You have only Two compatibility check left in this week.";
                } elseif (count($propertyCompChecks) == 0 && $loginuser->subscription_status == 2) {
                    $message = "You have only Two compatibility check left in this week.";
                }elseif (count($propertyCompChecks) == 1 && $loginuser->subscription_status == 0) {
                    $message = "You have only One compatibility check left in this week.";
                } elseif (count($propertyCompChecks) == 1 && $loginuser->subscription_status == 2) {
                    $message = "You have only One compatibility check left in this week.";
                }elseif (count($propertyCompChecks) == 2 && $loginuser->subscription_status == 0) {
                    $message = "You don't have any compatibility check left in this week. Please wait for next week Or Upgrade and Subscribe to Unlock Premium Features!";
                } elseif (count($propertyCompChecks) == 2 && $loginuser->subscription_status == 2) {
                    $message = "You don't have any compatibility checks left this week. Please wait for next week Or Your monthly plan has been paused. Please resume and unlock premium features!";
                } else {
                    $message = "Sucess";
                }
                if (count($propertyCompChecks) >= 3 && $loginuser->subscription_status == 0) {
                    return response()->json([
                        'status' => 0,
                        'message' => "You don't have any compatibility check left in this week. Please wait for next week Or Upgrade and Subscribe to Unlock Premium Features!",
                        'subscription_status' => $loginuser->subscription_status,
                    ]);
                }elseif (count($propertyCompChecks) >= 3 && $loginuser->subscription_status == 2) {
                    return response()->json([
                        'status' => 0,
                        'message' => "You don't have any compatibility checks left this week. Please wait for next week Or Your monthly plan has been paused. Please resume and unlock premium features!",
                        'subscription_status' => $loginuser->subscription_status,
                    ]);
                } else {

                $compatibility = User_compatiblecheck::create([

                    'user_id' => $userid,
                    'type' => $type,
                    'type_name' => $type_name,
                    'number' => $property_number,
                    'postalcode' => $postalcode,
                    'city' => $city,
                ]);

                //user dob reading
                $dob = $loginuser->dob;
                $date = explode('-', $dob);
                $day = str_split($date[2], 1);
                $cal_dobno = array_sum($day);
                $cal_dobno = intval($cal_dobno);
                while (strlen($cal_dobno) != 1) {
                    $cal_dobno = str_split($cal_dobno);
                    $cal_dobno = array_sum($cal_dobno);
                }

                // Property number reading
                $numaric = str_split(filter_var($property_number, FILTER_SANITIZE_NUMBER_INT));
                $propertynumber_sum = array_sum($numaric);
                while (strlen($propertynumber_sum) != 1) {
                    $propertynumber_sum = str_split($propertynumber_sum);
                    $propertynumber_sum = array_sum($propertynumber_sum);
                }

                $alphabet = implode("", preg_split("/\d+/", $property_number));
                $propertyno_alphabetno_sum = 0;
                if ($alphabet != null) {
                    $propertyno_alphabets = str_split($alphabet);
                    $alphabet_no = array();
                    foreach ($propertyno_alphabets as $propertyno_alphabet) {
                        $propertyno_alphabetno = Alphasystem_type::where('alphabet', 'LIKE', '%' . $propertyno_alphabet . '%')
                            ->where('systemtype_id', 2)
                            ->value('number');
                        array_push($alphabet_no, $propertyno_alphabetno);
                    }

                    $propertyno_alphabetno_sum = array_sum($alphabet_no);
                    while (strlen($propertyno_alphabetno_sum) != 1) {
                        $propertyno_alphabetno_sum = str_split($propertyno_alphabetno_sum);
                        $propertyno_alphabetno_sum = array_sum($propertyno_alphabetno_sum);
                    }
                }
                $final_propertyno = $propertynumber_sum + $propertyno_alphabetno_sum;
                while (strlen($final_propertyno) != 1) {
                    $split_final_propertyno = str_split($final_propertyno);
                    $sumSplit_final_propertyno = array_sum($split_final_propertyno);
                    $final_propertyno = $sumSplit_final_propertyno;
                }
                $property_compatibility_desc = Compatibility_description::where('type', 3)->where('number',$final_propertyno)->first();
                $property_description = $property_compatibility_desc->description;

                // property number reading
                $loginuser = User::find($userid);
                $dob = $loginuser->dob;
                $date = explode('-', $dob);
                $dob_day = $date[2];
                $day = str_split($dob_day, 1);
                $dayno_sum = array_sum($day);
                $dayno = intval($dayno_sum);
                while (strlen($dayno) != 1) {
                    $str_dayno = str_split($dayno);
                    $sum_dayno = array_sum($str_dayno);
                    $dayno = $sum_dayno;
                }

                $dob_month = $date[1];
                $month = str_split($dob_month, 1);
                $month_no = array_sum($month);
                $monthno = intval($month_no);
                if (strlen($monthno) != 1) {
                    $split_monthno = str_split($monthno);
                    $sum_monthno = array_sum($split_monthno);
                    $monthno = $sum_monthno;
                }
                $year = str_split($date[0], 1);
                $yearno = array_sum($year);
                while (strlen($yearno) != 1) {
                    $splityearno = str_split($yearno);
                    $sum_yearno = array_sum($splityearno);
                    $yearno = $sum_yearno;
                }
                $yearno = intval($yearno);
                $destiny_no = $dayno + $monthno + $yearno;
                while (strlen($destiny_no) != 1) {
                    $splitdestiny_no = str_split($destiny_no);
                    $destiny_nosum = array_sum($splitdestiny_no);
                    $destiny_no = $destiny_nosum;
                }

                //city name reading 
                $struppername = strtoupper($city);
                $names_array = explode(' ', $struppername);
                $cal_city_chaldno = array();
                foreach ($names_array as $citynamewords) {
                    $wordletter = str_split($citynamewords);
                    $wordchald_no = array();
                    foreach ($wordletter as $wordletters) {
                        $word_chald_number = Alphasystem_type::where('alphabet', 'LIKE', '%' . $wordletters . '%')
                            ->where('systemtype_id', 2)
                            ->value('number');
                        array_push($wordchald_no, $word_chald_number);
                    }
                    $city_chaldno = array_sum($wordchald_no);
                    while (strlen($city_chaldno) != 1) {
                        $city_chaldno = str_split($city_chaldno, 1);
                        $city_chaldno = array_sum($city_chaldno);
                    }
                    array_push($cal_city_chaldno, $city_chaldno);
                }
                $city_chaldnumber = array_sum($cal_city_chaldno);
                while (strlen($city_chaldnumber) != 1) {
                    $splitcity_chaldnumber = str_split($city_chaldnumber);
                    $sumcity_chaldnumber = array_sum($splitcity_chaldnumber);
                    $city_chaldnumber = $sumcity_chaldnumber;
                }

                $fav = Fav_unfav_parameter::where('type', 1)
                    ->where('month_id', $dob_month)
                    ->where('date', $dob_day)
                    ->value('numbers');
                $unfav = Fav_unfav_parameter::where('type', 2)
                    ->where('month_id', $dob_month)
                    ->where('date', $dob_day)
                    ->value('numbers');

                $fav_numbers = str_replace(' ', '', $fav);
                $favnumber_array = explode(',', $fav_numbers);
                $fav_arraycount = count($favnumber_array);

                $unfav_numbers = str_replace(' ', '', $unfav);
                $unfavnumber_array = explode(',', $unfav_numbers);
                $array_reverse_unfavnumber = array_reverse($unfavnumber_array);
                $unfav_arraycount = count($unfavnumber_array);
                $cityname_perc = 0;
                if ($dayno == $city_chaldnumber) {
                    $dobcitypercentage = 98;
                    $cityname_perc = 98;
                } else {

                    $dobcitypercentage = 0;
                }
                if ($destiny_no == $city_chaldnumber) {
                    $destinycitypercentage = 84;
                    $cityname_perc = 84;
                } else {
                    $destinycitypercentage = 0;
                }
                if ($fav_arraycount == 3) {
                    if ($favnumber_array[1] == $city_chaldnumber) {
                        $favcitypercentage = 66;
                        $cityname_perc = 66;
                    } elseif ($favnumber_array[2] == $city_chaldnumber) {
                        $favcitypercentage = 55;
                        $cityname_perc = 55;
                    } else {
                        $favcitypercentage = 0;
                    }
                } elseif ($fav_arraycount == 4) {
                    if ($favnumber_array[1] == $city_chaldnumber) {
                        $favcitypercentage = 74;
                        $cityname_perc = 74;
                    } elseif ($favnumber_array[2] == $city_chaldnumber) {
                        $favcitypercentage = 65;
                        $cityname_perc = 65;
                    } elseif ($favnumber_array[3] == $city_chaldnumber) {
                        $favcitypercentage = 55;
                        $cityname_perc = 55;
                    } else {
                        $favcitypercentage = 0;
                    }
                } else {
                    return response()->json([
                        'status' => 0,
                        'message' => 'fav number error'
                    ]);
                }
                if ($unfav_arraycount == 2) {
                    if ($array_reverse_unfavnumber[0] == $city_chaldnumber) {
                        $unfavcitypercentage = 30;
                        $cityname_perc = 30;
                    } elseif ($array_reverse_unfavnumber[1] == $city_chaldnumber) {
                        $unfavcitypercentage = 15;
                        $cityname_perc = 15;
                    } else {
                        $unfavcitypercentage = 0;
                    }
                } elseif ($unfav_arraycount == 3) {
                    if ($array_reverse_unfavnumber[0] == $city_chaldnumber) {
                        $unfavcitypercentage = 35;
                        $cityname_perc = 35;
                    } elseif ($array_reverse_unfavnumber[1] == $city_chaldnumber) {
                        $unfavcitypercentage = 23;
                        $cityname_perc = 23;
                    } elseif ($array_reverse_unfavnumber[2] == $city_chaldnumber) {
                        $unfavcitypercentage = 12;
                        $cityname_perc = 12;
                    } else {
                        $unfavcitypercentage = 0;
                    }
                } else {
                    return response()->json([
                        'status' => 0,
                        'message' => 'unfav number error'
                    ]);
                }

                if ($dobcitypercentage == 0 && $destinycitypercentage == 0 && $favcitypercentage == 0 && $unfavcitypercentage == 0) {
                    $cityname_perc = 50;
                }

                $property_finaldesc = '';

                if ($cityname_perc == 35 || $cityname_perc == 30 || $cityname_perc == 23 || $cityname_perc == 15 || $cityname_perc == 12) {

                    $property_finaldesc = "City is not compatible for you. Please try with another city";
                    $compatibilitycheck = array(
                        'type' => $type, 'city' => $city, 'pin' => $postalcode, 'property_number' => $property_number, 'final_compatibility_percentage' => $cityname_perc,
                        'first_compatibility_processingbar' => $cityname_perc, 'second_compatibility_processingbar' => '',
                        'third_compatibility_processingbar' => '', 'property_finaldesc' => $property_finaldesc, 'property_description'=>$property_description,
                    );

                    $exellent_desc = $loginuser->name . ", according to the calculation of numbers, every number has its own vibrations and frequencies. If your Birth Number which is " . $cal_dobno . " is completely compatible with your " . $type_name . " " . $property_number . " " . $postalcode . " and " . $city . " then, it makes a strong affinity with your property. A stronger sense of bond and togetherness is seen in this number. You will celebrate many occasions without any complications as these properties are considered a prosperous place for you";
                    $good_desc = $loginuser->name . ", according to the numerology calculations, your Birth Number " . $cal_dobno . " has good compatibility with your " . $type_name . " " . $property_number . " " . $postalcode . " and " . $city . ". If you are committed or married, then " . $property_number . " can be damaging to your relationship unless you are a business partner; this number is lucky for you both. If you go randomly with property numbers, then you might have to face problems in your professional and personal life.";
                    $bad_desc = $loginuser->name . ", according to the numerology calculations, your Birth Number " . $cal_dobno . " has a very low chance of compatibility with " . $type_name . " " . $property_number . " " . $postalcode . " and " . $city . ". This house brings big financial ups and downs. As per your numerology numbers predict that these numbers are completely suitable for you " . $property_number . " and so on.";


                    return response()->json([
                        'status' => 1,
                        'message' => $message,
                        'userid' => $userid,
                        'subscription_status' => $loginuser->subscription_status,
                        'compatibilitydetail' => $compatibilitycheck,
                        'propertytype_name' => $type_name,
                        'username' => $loginuser->name,
                        'property_no' => $property_number,
                        'city' => $city,
                        'prime_no' => $cal_dobno,
                        'exellent_desc' => $exellent_desc,
                        'good_desc' => $good_desc,
                        'bad_desc' => $bad_desc


                    ]);
                }

                //pin reading 
                $pin = str_split($postalcode, 1);
                $cal_pinno = array_sum($pin);
                $cal_pinno = intval($cal_pinno);
                while (strlen($cal_pinno) != 1) {
                    $cal_pinno = str_split($cal_pinno);
                    $cal_pinno = array_sum($cal_pinno);
                }

                $property_pin_perc = 0;
                if ($dayno == $cal_pinno) {
                    $dobpinpercentage = 98;
                    $property_pin_perc = 98;
                } else {

                    $dobpinpercentage = 0;
                }
                if ($destiny_no == $cal_pinno) {
                    $destinypinpercentage = 84;
                    $property_pin_perc = 84;
                } else {
                    $destinypinpercentage = 0;
                }
                if ($fav_arraycount == 3) {
                    if ($favnumber_array[1] == $cal_pinno) {
                        $favpinpercentage = 66;
                        $property_pin_perc = 66;
                    } elseif ($favnumber_array[2] == $cal_pinno) {
                        $favpinpercentage = 55;
                        $property_pin_perc = 55;
                    } else {
                        $favpinpercentage = 0;
                    }
                } elseif ($fav_arraycount == 4) {
                    if ($favnumber_array[1] == $cal_pinno) {
                        $favpinpercentage = 74;
                        $property_pin_perc = 74;
                    } elseif ($favnumber_array[2] == $cal_pinno) {
                        $favpinpercentage = 65;
                        $property_pin_perc = 65;
                    } elseif ($favnumber_array[3] == $cal_pinno) {
                        $favpinpercentage = 55;
                        $property_pin_perc = 55;
                    } else {
                        $favpinpercentage = 0;
                    }
                } else {
                    return response()->json([
                        'status' => 0,
                        'message' => 'fav number error'
                    ]);
                }
                if ($unfav_arraycount == 2) {
                    if ($array_reverse_unfavnumber[0] == $cal_pinno) {
                        $unfavpinpercentage = 30;
                        $property_pin_perc = 30;
                    } elseif ($array_reverse_unfavnumber[1] == $cal_pinno) {
                        $unfavpinpercentage = 15;
                        $property_pin_perc = 15;
                    } else {
                        $unfavpinpercentage = 0;
                    }
                } elseif ($unfav_arraycount == 3) {
                    if ($array_reverse_unfavnumber[0] == $cal_pinno) {
                        $unfavpinpercentage = 35;
                        $property_pin_perc = 35;
                    } elseif ($array_reverse_unfavnumber[1] == $cal_pinno) {
                        $unfavpinpercentage = 23;
                        $property_pin_perc = 23;
                    } elseif ($array_reverse_unfavnumber[2] == $cal_pinno) {
                        $unfavpinpercentage = 12;
                        $property_pin_perc = 12;
                    } else {
                        $unfavpinpercentage = 0;
                    }
                } else {
                    return response()->json([
                        'status' => 0,
                        'message' => 'unfav number error'
                    ]);
                }

                if ($dobpinpercentage == 0 && $destinypinpercentage == 0 && $favpinpercentage == 0 && $unfavpinpercentage == 0) {
                    $property_pin_perc = 50;
                }

                if ($property_pin_perc == 35 || $property_pin_perc == 30 || $property_pin_perc == 23 || $property_pin_perc == 15 || $property_pin_perc == 12) {

                    $numaric_cityname_perc = $cityname_perc;
                    $numaric_property_pin_perc = $property_pin_perc;

                    $final_compatibility_perc = ($numaric_cityname_perc + $numaric_property_pin_perc) / 2;
                    $final_compatibility_percentage = round($final_compatibility_perc);

                    $property_finaldesc = "City is compatible for you. But area is not compatible. Please try with another pin";
                    $compatibilitycheck = array(
                        'type' => $type, 'property_number' => $property_number, 'final_compatibility_percentage' => $final_compatibility_percentage,
                        'first_compatibility_processingbar' => $cityname_perc, 'second_compatibility_processingbar' => $property_pin_perc,
                        'third_compatibility_processingbar' => '', 'property_finaldesc' => $property_finaldesc, 'property_description'=>$property_description,
                    );

                    $exellent_desc = $loginuser->name . ", according to the calculation of numbers, every number has its own vibrations and frequencies. If your Birth Number which is " . $cal_dobno . " is completely compatible with your " . $type_name . " " . $property_number . " " . $postalcode . " and " . $city . " then, it makes a strong affinity with your property. A stronger sense of bond and togetherness is seen in this number. You will celebrate many occasions without any complications as these properties are considered a prosperous place for you";
                    $good_desc = $loginuser->name . ", according to the numerology calculations, your Birth Number " . $cal_dobno . " has good compatibility with your " . $type_name . " " . $property_number . " " . $postalcode . " and " . $city . ". If you are committed or married, then " . $property_number . " can be damaging to your relationship unless you are a business partner; this number is lucky for you both. If you go randomly with property numbers, then you might have to face problems in your professional and personal life.";
                    $bad_desc = $loginuser->name . ", according to the numerology calculations, your Birth Number " . $cal_dobno . " has a very low chance of compatibility with " . $type_name . " " . $property_number . " " . $postalcode . " and " . $city . ". This house brings big financial ups and downs. As per your numerology numbers predict that these numbers are completely suitable for you " . $property_number . " and so on.";

                    return response()->json([
                        'status' => 1,
                        'message' => $message,
                        'userid' => $userid,
                        'subscription_status' => $loginuser->subscription_status,
                        'compatibilitydetail' => $compatibilitycheck,
                        'propertytype_name' => $type_name,
                        'username' => $loginuser->name,
                        'property_no' => $property_number,
                        'city' => $city,
                        'prime_no' => $cal_dobno,
                        'exellent_desc' => $exellent_desc,
                        'good_desc' => $good_desc,
                        'bad_desc' => $bad_desc


                    ]);
                }

                $propertynofinal_perc = 0;
                if ($cal_dobno == $final_propertyno) {
                    $propertynodobpercentage = 98;
                    $propertynofinal_perc = 98;
                } else {
                    $propertynodobpercentage = 0;
                }
                if ($destiny_no == $final_propertyno) {
                    $propertynodestinypercentage = 84;
                    $propertynofinal_perc = 84;
                } else {
                    $propertynodestinypercentage = 0;
                }
                if ($fav_arraycount == 3) {
                    if ($favnumber_array[1] == $final_propertyno) {
                        $propertynofavpercentage = 66;
                        $propertynofinal_perc = 66;
                    } elseif ($favnumber_array[2] == $final_propertyno) {
                        $propertynofavpercentage = 55;
                        $propertynofinal_perc = 55;
                    } else {
                        $propertynofavpercentage = 0;
                    }
                } elseif ($fav_arraycount == 4) {
                    if ($favnumber_array[1] == $final_propertyno) {
                        $propertynofavpercentage = 74;
                        $propertynofinal_perc = 74;
                    } elseif ($favnumber_array[2] == $final_propertyno) {
                        $propertynofavpercentage = 65;
                        $propertynofinal_perc = 65;
                    } elseif ($favnumber_array[3] == $final_propertyno) {
                        $propertynofavpercentage = 55;
                        $propertynofinal_perc = 55;
                    } else {
                        $propertynofavpercentage = 0;
                    }
                } else {
                    return response()->json([
                        'status' => 0,
                        'message' => 'fav number error'
                    ]);
                }
                if ($unfav_arraycount == 2) {
                    if ($array_reverse_unfavnumber[0] == $final_propertyno) {
                        $propertynounfavpercentage = 30;
                        $propertynofinal_perc = 30;
                    } elseif ($array_reverse_unfavnumber[1] == $final_propertyno) {
                        $propertynounfavpercentage = 15;
                        $propertynofinal_perc = 15;
                    } else {
                        $propertynounfavpercentage = 0;
                    }
                } elseif ($unfav_arraycount == 3) {
                    if ($array_reverse_unfavnumber[0] == $final_propertyno) {
                        $propertynounfavpercentage = 35;
                        $propertynofinal_perc = 35;
                    } elseif ($array_reverse_unfavnumber[1] == $final_propertyno) {
                        $propertynounfavpercentage = 23;
                        $propertynofinal_perc = 23;
                    } elseif ($array_reverse_unfavnumber[2] == $final_propertyno) {
                        $propertynounfavpercentage = 12;
                        $propertynofinal_perc = 12;
                    } else {
                        $propertynounfavpercentage = 0;
                    }
                } else {
                    return response()->json([
                        'status' => 0,
                        'message' => 'unfav number error'
                    ]);
                }

                if ($propertynodobpercentage == 0 && $propertynodestinypercentage  == 0 && $propertynofavpercentage == 0 && $propertynounfavpercentage == 0) {
                    $propertynofinal_perc = 50;
                }


                if ($propertynofinal_perc == 35 || $propertynofinal_perc == 30 || $propertynofinal_perc == 23 || $propertynofinal_perc == 15 || $propertynofinal_perc == 12) {

                    $numaric_cityname_perc = $cityname_perc;
                    $numaric_property_pin_perc = $property_pin_perc;
                    $numaric_propertynofinal_perc = $propertynofinal_perc;

                    $final_compatibility_perc = ($numaric_cityname_perc + $numaric_property_pin_perc + $numaric_propertynofinal_perc) / 3;
                    $final_compatibility_percentage = round($final_compatibility_perc);

                    $property_finaldesc = "City and Pin is compatible for you. But property number is not compatible for you. Please try with another property number.";
                    $compatibilitycheck = array(
                        'type' => $type, 'property_number' => $property_number, 'final_compatibility_percentage' => $final_compatibility_percentage,
                        'first_compatibility_processingbar' => $cityname_perc, 'second_compatibility_processingbar' => $property_pin_perc,
                        'third_compatibility_processingbar' => $propertynofinal_perc, 'property_finaldesc' => $property_finaldesc, 'property_description'=>$property_description,
                    );

                    $exellent_desc = $loginuser->name . ", according to the calculation of numbers, every number has its own vibrations and frequencies. If your Birth Number which is " . $cal_dobno . " is completely compatible with your " . $type_name . " " . $property_number . " " . $postalcode . " and " . $city . " then, it makes a strong affinity with your property. A stronger sense of bond and togetherness is seen in this number. You will celebrate many occasions without any complications as these properties are considered a prosperous place for you";
                    $good_desc = $loginuser->name . ", according to the numerology calculations, your Birth Number " . $cal_dobno . " has good compatibility with your " . $type_name . " " . $property_number . " " . $postalcode . " and " . $city . ". If you are committed or married, then " . $property_number . " can be damaging to your relationship unless you are a business partner; this number is lucky for you both. If you go randomly with property numbers, then you might have to face problems in your professional and personal life.";
                    $bad_desc = $loginuser->name . ", according to the numerology calculations, your Birth Number " . $cal_dobno . " has a very low chance of compatibility with " . $type_name . " " . $property_number . " " . $postalcode . " and " . $city . ". This house brings big financial ups and downs. As per your numerology numbers predict that these numbers are completely suitable for you " . $property_number . " and so on.";


                    return response()->json([
                        'status' => 1,
                        'message' => $message,
                        'userid' => $userid,
                        'subscription_status' => $loginuser->subscription_status,
                        'compatibilitydetail' => $compatibilitycheck,
                        'propertytype_name' => $type_name,
                        'username' => $loginuser->name,
                        'property_no' => $property_number,
                        'city' => $city,
                        'prime_no' => $cal_dobno,
                        'exellent_desc' => $exellent_desc,
                        'good_desc' => $good_desc,
                        'bad_desc' => $bad_desc


                    ]);
                }

                $numaric_cityname_perc = $cityname_perc;
                $numaric_property_pin_perc = $property_pin_perc;
                $numaric_propertynofinal_perc = $propertynofinal_perc;

                $final_compatibility_perc = ($numaric_cityname_perc + $numaric_property_pin_perc + $numaric_propertynofinal_perc) / 3;
                $final_compatibility_percentage = round($final_compatibility_perc);


                $exellent_desc = $loginuser->name . ", according to the calculation of numbers, every number has its own vibrations and frequencies. If your Birth Number which is " . $cal_dobno . " is completely compatible with your " . $type_name . " " . $property_number . " " . $postalcode . " and " . $city . " then, it makes a strong affinity with your property. A stronger sense of bond and togetherness is seen in this number. You will celebrate many occasions without any complications as these properties are considered a prosperous place for you";
                $good_desc = $loginuser->name . ", according to the numerology calculations, your Birth Number " . $cal_dobno . " has good compatibility with your " . $type_name . " " . $property_number . " " . $postalcode . " and " . $city . ". If you are committed or married, then " . $property_number . " can be damaging to your relationship unless you are a business partner; this number is lucky for you both. If you go randomly with property numbers, then you might have to face problems in your professional and personal life.";
                $bad_desc = $loginuser->name . ", according to the numerology calculations, your Birth Number " . $cal_dobno . " has a very low chance of compatibility with " . $type_name . " " . $property_number . " " . $postalcode . " and " . $city . ". This house brings big financial ups and downs. As per your numerology numbers predict that these numbers are completely suitable for you " . $property_number . " and so on.";

                if ($final_compatibility_perc > 70) {
                    $property_finaldesc = $exellent_desc;
                }
                if ($final_compatibility_perc <= 70 && $final_compatibility_perc >= 50) {
                    $property_finaldesc = $good_desc;
                }

                $compatibilitycheck = array(
                    'type' => $type, 'property_number' => $property_number, 'final_compatibility_percentage' => $final_compatibility_percentage,
                    'first_compatibility_processingbar' => $cityname_perc, 'second_compatibility_processingbar' => $property_pin_perc,
                    'third_compatibility_processingbar' => $propertynofinal_perc, 'property_finaldesc' => $property_finaldesc, 'property_description'=>$property_description,
                );

                return response()->json([
                    'status' => 1,
                    'message' => $message,
                    'userid' => $userid,
                    'subscription_status' => $loginuser->subscription_status,
                    'compatibilitydetail' => $compatibilitycheck,
                    'propertytype_name' => $type_name,
                    'username' => $loginuser->name,
                    'property_no' => $property_number,
                    'city' => $city,
                    'prime_no' => $cal_dobno,
                    'exellent_desc' => $exellent_desc,
                    'good_desc' => $good_desc,
                    'bad_desc' => $bad_desc


                ]);
            }
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Error',
            ]);
        }
    }

    //update at 29-11-2022
    public function carcompatibilitycheck(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $type = $request->type;
        // For Car 
        if ($type == 1) {
            $validator = Validator::make($request->all(), [
                'userid' => 'required',
                'type' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }
            $userid = $request->userid;
            $type_name = $request->type_name;
            $brand_name = $request->brand_name;
            $modal = $request->modal;
            $car_name = $brand_name . " " . $modal;
            $registration_no = $request->registration_no;
            $loginuser = User::find($userid);
            $firstDateOfWeek = date("Y-m-d", strtotime('monday this week'));
            $lastdateOfWeek = date("Y-m-d", strtotime('sunday this week'));

            $carComp_Checks = User_compatiblecheck::where('user_id', '=', $userid)->where('type', '=', 1)
                ->whereBetween('created_at', [$firstDateOfWeek . " 00:00:00", $lastdateOfWeek . " 23:59:59"])->get();

                if (count($carComp_Checks) == 0 && $loginuser->subscription_status == 0) {
                    $message = "You have only Two compatibility check left in this week.";
                } elseif (count($carComp_Checks) == 0 && $loginuser->subscription_status == 2) {
                    $message = "You have only Two compatibility check left in this week.";
                }elseif (count($carComp_Checks) == 1 && $loginuser->subscription_status == 0) {
                    $message = "You have only One compatibility check left in this week.";
                } elseif (count($carComp_Checks) == 1 && $loginuser->subscription_status == 2) {
                    $message = "You have only One compatibility check left in this week.";
                }elseif (count($carComp_Checks) == 2 && $loginuser->subscription_status == 0) {
                    $message = "You don't have any compatibility check left in this week. Please wait for next week Or Upgrade and Subscribe to Unlock Premium Features!";
                } elseif (count($carComp_Checks) == 2 && $loginuser->subscription_status == 2) {
                    $message = "You don't have any compatibility checks left this week. Please wait for next week Or Your monthly plan has been paused. Please resume and unlock premium features!";
                } else {
                    $message = "Sucess";
                }
                if (count($carComp_Checks) >= 3 && $loginuser->subscription_status == 0) {
                    return response()->json([
                        'status' => 0,
                        'message' => "You don't have any compatibility check left in this week. Please wait for next week Or Upgrade and Subscribe to Unlock Premium Features!",
                        'subscription_status' => $loginuser->subscription_status,
                    ]);
                }elseif (count($carComp_Checks) >= 3 && $loginuser->subscription_status == 2) {
                    return response()->json([
                        'status' => 0,
                        'message' => "You don't have any compatibility checks left this week. Please wait for next week Or Your monthly plan has been paused. Please resume and unlock premium features!",
                        'subscription_status' => $loginuser->subscription_status,
                    ]);
                } else {

                $compatibility = User_compatiblecheck::create([

                    'user_id' => $userid,
                    'type' => $type,
                    'name' => $car_name,
                    'type_name' => $type_name,
                ]);

                $dob = $loginuser->dob;
                $date = explode('-', $dob);
                $dob_day = $date[2];
                $split_day = str_split($dob_day, 1);
                $sum_split_day = array_sum($split_day);
                $cal_dobno = intval($sum_split_day);
                while (strlen($cal_dobno) != 1) {
                    $cal_dobno_split = str_split($cal_dobno);
                    $cal_dobno_sum = array_sum($cal_dobno_split);
                    $cal_dobno = $cal_dobno_sum;
                }

                $dob_month = $date[1];
                $month = str_split($dob_month, 1);
                $month_no = array_sum($month);
                $monthno = intval($month_no);
                if (strlen($monthno) != 1) {
                    $split_monthno = str_split($monthno);
                    $sum_monthno = array_sum($split_monthno);
                    $monthno = $sum_monthno;
                }
                $dob_year = $date[0];
                $year = str_split($dob_year, 1);
                $yearno = array_sum($year);
                while (strlen($yearno) != 1) {
                    $splityearno = str_split($yearno);
                    $sum_yearno = array_sum($splityearno);
                    $yearno = $sum_yearno;
                }
                $year_no = intval($yearno);
                $destiny_no = $cal_dobno + $monthno + $year_no;
                while (strlen($destiny_no) != 1) {
                    $splitdestiny_no = str_split($destiny_no);
                    $destiny_nosum = array_sum($splitdestiny_no);
                    $destiny_no = $destiny_nosum;
                }
                if ($brand_name != null && $modal != null) {
                    $brandnamechaldno = array();
                    $brandname = strtoupper($brand_name);
                    $split_brandname = str_split($brandname, 1);
                    foreach ($split_brandname as $nameletter) {
                        $brandnamechald_no = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                            ->where('systemtype_id', 2)
                            ->value('number');
                        array_push($brandnamechaldno, $brandnamechald_no);
                    }

                    $brandnamechaldno_sum = array_sum($brandnamechaldno);
                    while (strlen($brandnamechaldno_sum) != 1) {
                        $split_brandnamechaldno_sum = str_split($brandnamechaldno_sum, 1);
                        $sum_brandnamechaldno_sum = array_sum($split_brandnamechaldno_sum);
                        $brandnamechaldno_sum = $sum_brandnamechaldno_sum;
                    }
                    $brandnamereadingno = $brandnamechaldno_sum;

                    $fav = Fav_unfav_parameter::where('type', 1)
                        ->where('month_id', $dob_month)
                        ->where('date', $dob_day)
                        ->value('numbers');
                    $unfav = Fav_unfav_parameter::where('type', 2)
                        ->where('month_id', $dob_month)
                        ->where('date', $dob_day)
                        ->value('numbers');

                    $fav_numbers = str_replace(' ', '', $fav);
                    $favnumber_array = explode(',', $fav_numbers);
                    $fav_arraycount = count($favnumber_array);

                    $unfav_numbers = str_replace(' ', '', $unfav);
                    $unfavnumber_array = explode(',', $unfav_numbers);
                    $array_reverse_unfavnumber = array_reverse($unfavnumber_array);
                    $unfav_arraycount = count($unfavnumber_array);

                    $brandnamefinal_perc = 0;
                    if ($cal_dobno == $brandnamereadingno) {
                        $dobpercentage = 98;
                        $brandnamefinal_perc = 98;
                    } else {
                        $dobpercentage = 0;
                    }
                    if ($destiny_no == $brandnamereadingno) {
                        $destinypercentage = 84;
                        $brandnamefinal_perc = 84;
                    } else {
                        $destinypercentage = 0;
                    }
                    if ($fav_arraycount == 3) {
                        if ($favnumber_array[1] == $brandnamereadingno) {
                            $favpercentage = 66;
                            $brandnamefinal_perc = 66;
                        } elseif ($favnumber_array[2] == $brandnamereadingno) {
                            $favpercentage = 55;
                            $brandnamefinal_perc = 55;
                        } else {
                            $favpercentage = 0;
                        }
                    } elseif ($fav_arraycount == 4) {
                        if ($favnumber_array[1] == $brandnamereadingno) {
                            $favpercentage = 74;
                            $brandnamefinal_perc = 74;
                        } elseif ($favnumber_array[2] == $brandnamereadingno) {
                            $favpercentage = 65;
                            $brandnamefinal_perc = 65;
                        } elseif ($favnumber_array[3] == $brandnamereadingno) {
                            $favpercentage = 55;
                            $brandnamefinal_perc = 55;
                        } else {
                            $favpercentage = 0;
                        }
                    } else {
                        return response()->json([
                            'status' => 0,
                            'message' => 'fav number error'
                        ]);
                    }
                    if ($unfav_arraycount == 2) {
                        if ($array_reverse_unfavnumber[0] == $brandnamereadingno) {
                            $unfavpercentage = 30;
                            $brandnamefinal_perc = 30;
                        } elseif ($array_reverse_unfavnumber[1] == $brandnamereadingno) {
                            $unfavpercentage = 15;
                            $brandnamefinal_perc = 15;
                        } else {
                            $unfavpercentage = 0;
                        }
                    } elseif ($unfav_arraycount == 3) {
                        if ($array_reverse_unfavnumber[0] == $brandnamereadingno) {
                            $unfavpercentage = 35;
                            $brandnamefinal_perc = 35;
                        } elseif ($array_reverse_unfavnumber[1] == $brandnamereadingno) {
                            $unfavpercentage = 23;
                            $brandnamefinal_perc = 23;
                        } elseif ($array_reverse_unfavnumber[2] == $brandnamereadingno) {
                            $unfavpercentage = 12;
                            $brandnamefinal_perc = 12;
                        } else {
                            $unfavpercentage = 0;
                        }
                    } else {
                        return response()->json([
                            'status' => 0,
                            'message' => 'unfav number error'
                        ]);
                    }
                    if ($dobpercentage == 0 && $destinypercentage == 0 && $favpercentage == 0 && $unfavpercentage == 0) {
                        $brandnamefinal_perc = 50;
                    }
                    $final_desc = "";

                    if ($brandnamefinal_perc == 35 || $brandnamefinal_perc == 30 || $brandnamefinal_perc == 23 || $brandnamefinal_perc == 15 || $brandnamefinal_perc == 12) {
                        $final_desc = "Brand is not compatible for you.";

                        $compatibilitycheck = array(
                            'type' => $type, 'car_name' => $car_name, 'final_compatibility_percentage' => $brandnamefinal_perc,
                            'first_compatibility_processingbar' => $brandnamefinal_perc, 'second_compatibility_processingbar' => '', "final_desc" => $final_desc
                        );


                        $exellent_desc = $loginuser->name . ", as per AstroNumeric calculations, your " . $type_name . " which is " . $brand_name . " " . $modal . " is perfectly compatible with your Birth Number which is " . $cal_dobno . ". A " . $type . " is completely lucky for you and can also save you from unforeseen events like an accident, vehicle theft, vehicle problems, etc. " . $type_name . " should carry high vibrations and it should keep attracting more wealth and prosperity in your life.";
                        $good_desc = $loginuser->name . ", as per numerology calculations, your " . $type_name . " which is " . $brand_name . " " . $modal . " has good compatibility with your Birth Number which is " . $cal_dobno . ". On the basis of good compatibility, these vehicles rarely get stolen, but it brings good luck for someone whose profession is related to money. It is very favorable for business people.";
                        $bad_desc = $loginuser->name . ", as per numerology studies, your " . $type_name . " which is " . $brand_name . " " . $modal . " has very low compatibility with your Birth Number which is " . $cal_dobno . ". It promotes uncooperative rearrangements and resourcefulness on the road. You have the best compatibility with " . $type . " which is " . $brand_name . " " . $modal . ". It accelerates quickly, is reliable, and attracts attention.  ";

                        return response()->json([
                            'status' => 1,
                            'message' => $message,
                            'userid' => $userid,
                            'subscription_status' => $loginuser->subscription_status,
                            'compatibilitydetail' => $compatibilitycheck,
                            'exellent_desc' => $exellent_desc,
                            'good_desc' => $good_desc,
                            'bad_desc' => $bad_desc
                        ]);
                    } else {

                        $modeldobpercentage = 0;
                        $modeldestinypercentage = 0;
                        $modelfavpercentage = 0;
                        $modelunfavpercentage = 0;
                        if ($brandnamefinal_perc != 35 || $brandnamefinal_perc != 30 || $brandnamefinal_perc != 23 || $brandnamefinal_perc != 15 || $brandnamefinal_perc != 12) {
                            $modelchaldno = array();
                            $model = strtoupper($modal);
                            $split_model = str_split($model, 1);
                            foreach ($split_model as $nameletter) {
                                $modelchald_no = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                                    ->where('systemtype_id', 2)
                                    ->value('number');
                                array_push($modelchaldno, $modelchald_no);
                            }

                            $modelchaldno_sum = array_sum($modelchaldno);
                            while (strlen($modelchaldno_sum) != 1) {
                                $split_modelchaldno_sum = str_split($modelchaldno_sum, 1);
                                $sum_modelchaldno_sum = array_sum($split_modelchaldno_sum);
                                $modelchaldno_sum = $sum_modelchaldno_sum;
                            }

                            $modelreadingno = $modelchaldno_sum;

                            $fav = Fav_unfav_parameter::where('type', 1)
                                ->where('month_id', $dob_month)
                                ->where('date', $dob_day)
                                ->value('numbers');
                            $unfav = Fav_unfav_parameter::where('type', 2)
                                ->where('month_id', $dob_month)
                                ->where('date', $dob_day)
                                ->value('numbers');

                            $fav_numbers = str_replace(' ', '', $fav);
                            $favnumber_array = explode(',', $fav_numbers);
                            $fav_arraycount = count($favnumber_array);

                            $unfav_numbers = str_replace(' ', '', $unfav);
                            $unfavnumber_array = explode(',', $unfav_numbers);
                            $array_reverse_unfavnumber = array_reverse($unfavnumber_array);
                            $unfav_arraycount = count($unfavnumber_array);

                            $modelfinal_perc = 0;
                            if ($cal_dobno == $modelreadingno) {
                                $modeldobpercentage = 98;
                                $modelfinal_perc = 98;
                            } else {
                                $modeldobpercentage = 0;
                            }
                            if ($destiny_no == $modelreadingno) {
                                $modeldestinypercentage = 84;
                                $modelfinal_perc = 84;
                            } else {
                                $modeldestinypercentage = 0;
                            }
                            if ($fav_arraycount == 3) {
                                if ($favnumber_array[1] == $modelreadingno) {
                                    $modelfavpercentage = 66;
                                    $modelfinal_perc = 66;
                                } elseif ($favnumber_array[2] == $modelreadingno) {
                                    $modelfavpercentage = 55;
                                    $modelfinal_perc = 55;
                                } else {
                                    $modelfavpercentage = 0;
                                }
                            } elseif ($fav_arraycount == 4) {
                                if ($favnumber_array[1] == $modelreadingno) {
                                    $modelfavpercentage = 74;
                                    $modelfinal_perc = 74;
                                } elseif ($favnumber_array[2] == $modelreadingno) {
                                    $modelfavpercentage = 65;
                                    $modelfinal_perc = 65;
                                } elseif ($favnumber_array[3] == $modelreadingno) {
                                    $modelfavpercentage = 55;
                                    $modelfinal_perc = 55;
                                } else {
                                    $modelfavpercentage = 0;
                                }
                            } else {
                                return response()->json([
                                    'status' => 0,
                                    'message' => 'fav number error'
                                ]);
                            }
                            if ($unfav_arraycount == 2) {
                                if ($array_reverse_unfavnumber[0] == $modelreadingno) {
                                    $modelunfavpercentage = 30;
                                    $modelfinal_perc = 30;
                                } elseif ($array_reverse_unfavnumber[1] == $modelreadingno) {
                                    $modelunfavpercentage = 15;
                                    $modelfinal_perc = 15;
                                } else {
                                    $modelunfavpercentage = 0;
                                }
                            } elseif ($unfav_arraycount == 3) {
                                if ($array_reverse_unfavnumber[0] == $modelreadingno) {
                                    $modelunfavpercentage = 35;
                                    $modelfinal_perc = 35;
                                } elseif ($array_reverse_unfavnumber[1] == $modelreadingno) {
                                    $modelunfavpercentage = 23;
                                    $modelfinal_perc = 23;
                                } elseif ($array_reverse_unfavnumber[2] == $modelreadingno) {
                                    $modelunfavpercentage = 12;
                                    $modelfinal_perc = 12;
                                } else {
                                    $modelunfavpercentage = 0;
                                }
                            } else {
                                return response()->json([
                                    'status' => 0,
                                    'message' => 'unfav number error'
                                ]);
                            }

                            if ($modeldobpercentage == 0 && $modeldestinypercentage  == 0 && $modelfavpercentage == 0 && $modelunfavpercentage == 0) {
                                $modelfinal_perc = 50;
                            }

                            $final_desc = '';

                            if ($modelfinal_perc == 35 || $modelfinal_perc == 30 || $modelfinal_perc == 23 || $modelfinal_perc == 15 || $modelfinal_perc == 12) {
                                $final_desc = "Brand is compatible but Model is not compatible for you. Please try with another model.";

                                $numaric_brandnamefinal_perc = $brandnamefinal_perc;
                                $numaric_modelfinal_perc = $modelfinal_perc;

                                $final_compatibility_perc = ($numaric_brandnamefinal_perc + $numaric_modelfinal_perc) / 2;
                                $final_compatibility_percentage = round($final_compatibility_perc);

                                $compatibilitycheck = array(
                                    'type' => $type, 'car_name' => $car_name, 'final_compatibility_percentage' => $final_compatibility_percentage,
                                    'first_compatibility_processingbar' => $brandnamefinal_perc, 'second_compatibility_processingbar' => $modelfinal_perc, 'final_desc' => $final_desc
                                );

                                $exellent_desc = $loginuser->name . ", as per AstroNumeric calculations, your " . $type_name . " which is " . $brand_name . " " . $modal . " is perfectly compatible with your Birth Number which is " . $cal_dobno . ". A " . $type . " is completely lucky for you and can also save you from unforeseen events like an accident, vehicle theft, vehicle problems, etc. " . $type_name . " should carry high vibrations and it should keep attracting more wealth and prosperity in your life.";
                                $good_desc = $loginuser->name . ", as per numerology calculations, your " . $type_name . " which is " . $brand_name . " " . $modal . " has good compatibility with your Birth Number which is " . $cal_dobno . ". On the basis of good compatibility, these vehicles rarely get stolen, but it brings good luck for someone whose profession is related to money. It is very favorable for business people.";
                                $bad_desc = $loginuser->name . ", as per numerology studies, your " . $type_name . " which is " . $brand_name . " " . $modal . " has very low compatibility with your Birth Number which is " . $cal_dobno . ". It promotes uncooperative rearrangements and resourcefulness on the road. You have the best compatibility with " . $type . " which is " . $brand_name . " " . $modal . ". It accelerates quickly, is reliable, and attracts attention.  ";

                                return response()->json([
                                    'status' => 1,
                                    'message' => $message,
                                    'userid' => $userid,
                                    'subscription_status' => $loginuser->subscription_status,
                                    'compatibilitydetail' => $compatibilitycheck,
                                    'exellent_desc' => $exellent_desc,
                                    'good_desc' => $good_desc,
                                    'bad_desc' => $bad_desc
                                ]);
                            }
                        }
                    }

                    $numaric_brandnamefinal_perc = $brandnamefinal_perc;
                    $numaric_modelfinal_perc = $modelfinal_perc;

                    $final_compatibility_perc = ($numaric_brandnamefinal_perc + $numaric_modelfinal_perc) / 2;
                    $final_compatibility_percentage = round($final_compatibility_perc);

                    $exellent_desc = $loginuser->name . ", as per AstroNumeric calculations, your " . $type_name . " which is " . $brand_name . " " . $modal . " is perfectly compatible with your Birth Number which is " . $cal_dobno . ". A " . $type . " is completely lucky for you and can also save you from unforeseen events like an accident, vehicle theft, vehicle problems, etc. " . $type_name . " should carry high vibrations and it should keep attracting more wealth and prosperity in your life.";
                    $good_desc = $loginuser->name . ", as per numerology calculations, your " . $type_name . " which is " . $brand_name . " " . $modal . " has good compatibility with your Birth Number which is " . $cal_dobno . ". On the basis of good compatibility, these vehicles rarely get stolen, but it brings good luck for someone whose profession is related to money. It is very favorable for business people.";
                    $bad_desc = $loginuser->name . ", as per numerology studies, your " . $type_name . " which is " . $brand_name . " " . $modal . " has very low compatibility with your Birth Number which is " . $cal_dobno . ". It promotes uncooperative rearrangements and resourcefulness on the road. You have the best compatibility with " . $type . " which is " . $brand_name . " " . $modal . ". It accelerates quickly, is reliable, and attracts attention.  ";

                    if ($final_compatibility_perc > 70) {
                        $final_desc = $exellent_desc;
                    }
                    if ($final_compatibility_perc <= 70 && $final_compatibility_perc >= 50) {
                        $final_desc = $good_desc;
                    }

                    $compatibilitycheck = array(
                        'type' => $type, 'car_name' => $car_name, 'final_compatibility_percentage' => $final_compatibility_percentage,
                        'first_compatibility_processingbar' => $brandnamefinal_perc, 'second_compatibility_processingbar' => $modelfinal_perc, 'final_desc' => $final_desc
                    );

                    return response()->json([
                        'status' => 1,
                        'message' => $message,
                        'userid' => $userid,
                        'subscription_status' => $loginuser->subscription_status,
                        'compatibilitydetail' => $compatibilitycheck,
                        'exellent_desc' => $exellent_desc,
                        'good_desc' => $good_desc,
                        'bad_desc' => $bad_desc
                    ]);
                } elseif ($brand_name != null && $modal == null) {
                    $brandnamechaldno = array();
                    $brandname = strtoupper($brand_name);
                    $split_brandname = str_split($brandname, 1);
                    foreach ($split_brandname as $nameletter) {
                        $brandnamechald_no = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                            ->where('systemtype_id', 2)
                            ->value('number');
                        array_push($brandnamechaldno, $brandnamechald_no);
                    }

                    $brandnamechaldno_sum = array_sum($brandnamechaldno);
                    while (strlen($brandnamechaldno_sum) != 1) {
                        $split_brandnamechaldno_sum = str_split($brandnamechaldno_sum, 1);
                        $sum_brandnamechaldno_sum = array_sum($split_brandnamechaldno_sum);
                        $brandnamechaldno_sum = $sum_brandnamechaldno_sum;
                    }
                    $brandnamereadingno = $brandnamechaldno_sum;

                    $fav = Fav_unfav_parameter::where('type', 1)
                        ->where('month_id', $dob_month)
                        ->where('date', $dob_day)
                        ->value('numbers');
                    $unfav = Fav_unfav_parameter::where('type', 2)
                        ->where('month_id', $dob_month)
                        ->where('date', $dob_day)
                        ->value('numbers');

                    $fav_numbers = str_replace(' ', '', $fav);
                    $favnumber_array = explode(',', $fav_numbers);
                    $fav_arraycount = count($favnumber_array);

                    $unfav_numbers = str_replace(' ', '', $unfav);
                    $unfavnumber_array = explode(',', $unfav_numbers);
                    $array_reverse_unfavnumber = array_reverse($unfavnumber_array);
                    $unfav_arraycount = count($unfavnumber_array);

                    $brandnamefinal_perc = 0;
                    if ($cal_dobno == $brandnamereadingno) {
                        $dobpercentage = 98;
                        $brandnamefinal_perc = 98;
                    } else {
                        $dobpercentage = 0;
                    }
                    if ($destiny_no == $brandnamereadingno) {
                        $destinypercentage = 84;
                        $brandnamefinal_perc = 84;
                    } else {
                        $destinypercentage = 0;
                    }
                    if ($fav_arraycount == 3) {
                        if ($favnumber_array[1] == $brandnamereadingno) {
                            $favpercentage = 66;
                            $brandnamefinal_perc = 66;
                        } elseif ($favnumber_array[2] == $brandnamereadingno) {
                            $favpercentage = 55;
                            $brandnamefinal_perc = 55;
                        } else {
                            $favpercentage = 0;
                        }
                    } elseif ($fav_arraycount == 4) {
                        if ($favnumber_array[1] == $brandnamereadingno) {
                            $favpercentage = 74;
                            $brandnamefinal_perc = 74;
                        } elseif ($favnumber_array[2] == $brandnamereadingno) {
                            $favpercentage = 65;
                            $brandnamefinal_perc = 65;
                        } elseif ($favnumber_array[3] == $brandnamereadingno) {
                            $favpercentage = 55;
                            $brandnamefinal_perc = 55;
                        } else {
                            $favpercentage = 0;
                        }
                    } else {
                        return response()->json([
                            'status' => 0,
                            'message' => 'fav number error'
                        ]);
                    }
                    if ($unfav_arraycount == 2) {
                        if ($array_reverse_unfavnumber[0] == $brandnamereadingno) {
                            $unfavpercentage = 30;
                            $brandnamefinal_perc = 30;
                        } elseif ($array_reverse_unfavnumber[1] == $brandnamereadingno) {
                            $unfavpercentage = 15;
                            $brandnamefinal_perc = 15;
                        } else {
                            $unfavpercentage = 0;
                        }
                    } elseif ($unfav_arraycount == 3) {
                        if ($array_reverse_unfavnumber[0] == $brandnamereadingno) {
                            $unfavpercentage = 35;
                            $brandnamefinal_perc = 35;
                        } elseif ($array_reverse_unfavnumber[1] == $brandnamereadingno) {
                            $unfavpercentage = 23;
                            $brandnamefinal_perc = 23;
                        } elseif ($array_reverse_unfavnumber[2] == $brandnamereadingno) {
                            $unfavpercentage = 12;
                            $brandnamefinal_perc = 12;
                        } else {
                            $unfavpercentage = 0;
                        }
                    } else {
                        return response()->json([
                            'status' => 0,
                            'message' => 'unfav number error'
                        ]);
                    }
                    if ($dobpercentage == 0 && $destinypercentage == 0 && $favpercentage == 0 && $unfavpercentage == 0) {
                        $brandnamefinal_perc = 50;
                    }
                    $final_desc = "";

                    if ($brandnamefinal_perc == 35 || $brandnamefinal_perc == 30 || $brandnamefinal_perc == 23 || $brandnamefinal_perc == 15 || $brandnamefinal_perc == 12) {
                        $final_desc = "Brand is not compatible for you.";
                    } else {
                        $final_desc = "According to the calculation Brand is compatible for you.";
                    }
                    $compatibilitycheck = array(
                        'type' => $type, 'car_name' => $car_name, 'final_compatibility_percentage' => $brandnamefinal_perc,
                        'first_compatibility_processingbar' => $brandnamefinal_perc, 'second_compatibility_processingbar' => '', "final_desc" => $final_desc
                    );

                    $exellent_desc = $loginuser->name . ", as per AstroNumeric calculations, your " . $type_name . " which is " . $brand_name . " " . $modal . " is perfectly compatible with your Birth Number which is " . $cal_dobno . ". A " . $type . " is completely lucky for you and can also save you from unforeseen events like an accident, vehicle theft, vehicle problems, etc. " . $type_name . " should carry high vibrations and it should keep attracting more wealth and prosperity in your life.";
                    $good_desc = $loginuser->name . ", as per numerology calculations, your " . $type_name . " which is " . $brand_name . " " . $modal . " has good compatibility with your Birth Number which is " . $cal_dobno . ". On the basis of good compatibility, these vehicles rarely get stolen, but it brings good luck for someone whose profession is related to money. It is very favorable for business people.";
                    $bad_desc = $loginuser->name . ", as per numerology studies, your " . $type_name . " which is " . $brand_name . " " . $modal . " has very low compatibility with your Birth Number which is " . $cal_dobno . ". It promotes uncooperative rearrangements and resourcefulness on the road. You have the best compatibility with " . $type . " which is " . $brand_name . " " . $modal . ". It accelerates quickly, is reliable, and attracts attention.  ";

                    return response()->json([
                        'status' => 1,
                        'message' => $message,
                        'userid' => $userid,
                        'subscription_status' => $loginuser->subscription_status,
                        'compatibilitydetail' => $compatibilitycheck,
                        'exellent_desc' => $exellent_desc,
                        'good_desc' => $good_desc,
                        'bad_desc' => $bad_desc
                    ]);
                } elseif ($modal != null && $brand_name == null) {
                    $modeldobpercentage = 0;
                    $modeldestinypercentage = 0;
                    $modelfavpercentage = 0;
                    $modelunfavpercentage = 0;

                    $modelchaldno = array();
                    $model = strtoupper($modal);
                    $split_model = str_split($model, 1);
                    foreach ($split_model as $nameletter) {
                        $modelchald_no = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                            ->where('systemtype_id', 2)
                            ->value('number');
                        array_push($modelchaldno, $modelchald_no);
                    }

                    $modelchaldno_sum = array_sum($modelchaldno);
                    while (strlen($modelchaldno_sum) != 1) {
                        $split_modelchaldno_sum = str_split($modelchaldno_sum, 1);
                        $sum_modelchaldno_sum = array_sum($split_modelchaldno_sum);
                        $modelchaldno_sum = $sum_modelchaldno_sum;
                    }

                    $modelreadingno = $modelchaldno_sum;

                    $fav = Fav_unfav_parameter::where('type', 1)
                        ->where('month_id', $dob_month)
                        ->where('date', $dob_day)
                        ->value('numbers');
                    $unfav = Fav_unfav_parameter::where('type', 2)
                        ->where('month_id', $dob_month)
                        ->where('date', $dob_day)
                        ->value('numbers');

                    $fav_numbers = str_replace(' ', '', $fav);
                    $favnumber_array = explode(',', $fav_numbers);
                    $fav_arraycount = count($favnumber_array);

                    $unfav_numbers = str_replace(' ', '', $unfav);
                    $unfavnumber_array = explode(',', $unfav_numbers);
                    $array_reverse_unfavnumber = array_reverse($unfavnumber_array);
                    $unfav_arraycount = count($unfavnumber_array);

                    $modelfinal_perc = 0;
                    if ($cal_dobno == $modelreadingno) {
                        $modeldobpercentage = 98;
                        $modelfinal_perc = 98;
                    } else {
                        $modeldobpercentage = 0;
                    }
                    if ($destiny_no == $modelreadingno) {
                        $modeldestinypercentage = 84;
                        $modelfinal_perc = 84;
                    } else {
                        $modeldestinypercentage = 0;
                    }
                    if ($fav_arraycount == 3) {
                        if ($favnumber_array[1] == $modelreadingno) {
                            $modelfavpercentage = 66;
                            $modelfinal_perc = 66;
                        } elseif ($favnumber_array[2] == $modelreadingno) {
                            $modelfavpercentage = 55;
                            $modelfinal_perc = 55;
                        } else {
                            $modelfavpercentage = 0;
                        }
                    } elseif ($fav_arraycount == 4) {
                        if ($favnumber_array[1] == $modelreadingno) {
                            $modelfavpercentage = 74;
                            $modelfinal_perc = 74;
                        } elseif ($favnumber_array[2] == $modelreadingno) {
                            $modelfavpercentage = 65;
                            $modelfinal_perc = 65;
                        } elseif ($favnumber_array[3] == $modelreadingno) {
                            $modelfavpercentage = 55;
                            $modelfinal_perc = 55;
                        } else {
                            $modelfavpercentage = 0;
                        }
                    } else {
                        return response()->json([
                            'status' => 0,
                            'message' => 'fav number error'
                        ]);
                    }
                    if ($unfav_arraycount == 2) {
                        if ($array_reverse_unfavnumber[0] == $modelreadingno) {
                            $modelunfavpercentage = 30;
                            $modelfinal_perc = 30;
                        } elseif ($array_reverse_unfavnumber[1] == $modelreadingno) {
                            $modelunfavpercentage = 15;
                            $modelfinal_perc = 15;
                        } else {
                            $modelunfavpercentage = 0;
                        }
                    } elseif ($unfav_arraycount == 3) {
                        if ($array_reverse_unfavnumber[0] == $modelreadingno) {
                            $modelunfavpercentage = 35;
                            $modelfinal_perc = 35;
                        } elseif ($array_reverse_unfavnumber[1] == $modelreadingno) {
                            $modelunfavpercentage = 23;
                            $modelfinal_perc = 23;
                        } elseif ($array_reverse_unfavnumber[2] == $modelreadingno) {
                            $modelunfavpercentage = 12;
                            $modelfinal_perc = 12;
                        } else {
                            $modelunfavpercentage = 0;
                        }
                    } else {
                        return response()->json([
                            'status' => 0,
                            'message' => 'unfav number error'
                        ]);
                    }

                    if ($modeldobpercentage == 0 && $modeldestinypercentage  == 0 && $modelfavpercentage == 0 && $modelunfavpercentage == 0) {
                        $modelfinal_perc = 50;
                    }

                    $final_desc = '';

                    if ($modelfinal_perc == 35 || $modelfinal_perc == 30 || $modelfinal_perc == 23 || $modelfinal_perc == 15 || $modelfinal_perc == 12) {
                        $final_desc = "Model is not compatible for you. Please try with another model.";
                    } else {
                        $final_desc = "According to the calculation Model is compatible for you.";
                    }

                    $final_compatibility_perc = $modelfinal_perc;
                    $final_compatibility_percentage = round($final_compatibility_perc);

                    $compatibilitycheck = array(
                        'type' => $type, 'car_name' => $car_name, 'final_compatibility_percentage' => $final_compatibility_percentage,
                        'first_compatibility_processingbar' => '', 'second_compatibility_processingbar' => $modelfinal_perc, 'final_desc' => $final_desc
                    );

                    $exellent_desc = $loginuser->name . ", as per AstroNumeric calculations, your " . $type_name . " which is " . $brand_name . " " . $modal . " is perfectly compatible with your Birth Number which is " . $cal_dobno . ". A " . $type . " is completely lucky for you and can also save you from unforeseen events like an accident, vehicle theft, vehicle problems, etc. " . $type_name . " should carry high vibrations and it should keep attracting more wealth and prosperity in your life.";
                    $good_desc = $loginuser->name . ", as per numerology calculations, your " . $type_name . " which is " . $brand_name . " " . $modal . " has good compatibility with your Birth Number which is " . $cal_dobno . ". On the basis of good compatibility, these vehicles rarely get stolen, but it brings good luck for someone whose profession is related to money. It is very favorable for business people.";
                    $bad_desc = $loginuser->name . ", as per numerology studies, your " . $type_name . " which is " . $brand_name . " " . $modal . " has very low compatibility with your Birth Number which is " . $cal_dobno . ". It promotes uncooperative rearrangements and resourcefulness on the road. You have the best compatibility with " . $type . " which is " . $brand_name . " " . $modal . ". It accelerates quickly, is reliable, and attracts attention.  ";

                    return response()->json([
                        'status' => 1,
                        'message' => $message,
                        'userid' => $userid,
                        'subscription_status' => $loginuser->subscription_status,
                        'compatibilitydetail' => $compatibilitycheck,
                        'exellent_desc' => $exellent_desc,
                        'good_desc' => $good_desc,
                        'bad_desc' => $bad_desc
                    ]);
                } else {
                    return response()->json([
                        'status' => 2,
                        'message' => 'Please fill at least one from Brand and Model.'
                    ]);
                }
            }
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Error',
            ]);
        }
    }

    //end here

    //update at 20-11-2022
    public function usernamecompatibilitycheck(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $userid = $request->userid;
        $loginuser = User::find($userid);

        if ($loginuser) {
            // login user name reading
            $loginuserchaldno = array();
            $finalname = str_replace(' ', '', $loginuser->name);
            $loginuserstrname = strtoupper($finalname);
            $loginusersplitname = str_split($loginuserstrname, 1);
            foreach ($loginusersplitname as $nameletter) {
                $loginuserchald_no = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                    ->where('systemtype_id', 2)
                    ->value('number');
                array_push($loginuserchaldno, $loginuserchald_no);
            }
            $loginuserchaldno_sum = array_sum($loginuserchaldno);
            while (strlen($loginuserchaldno_sum) != 1) {
                $loginuserchaldno_sum = str_split($loginuserchaldno_sum, 1);
                $loginuserchaldno_sum = array_sum($loginuserchaldno_sum);
            }

            $loginusernamereadingno = $loginuserchaldno_sum;
            $loginusernamereading =  Module_description::where('moduletype_id', 1)
                ->where('number', $loginusernamereadingno)
                ->value('description');

            $loginuser_namedesc = strip_tags($loginusernamereading);
            $explodenamereading_desc = explode('||', $loginuser_namedesc);
            $positive_desc = $explodenamereading_desc[0];
            $negative_desc = $explodenamereading_desc[1];
            $loginusernamedesc = array("positive_title" => "Positive", "positive_desc" => $positive_desc, "negative_title" => "Negative", "negative_desc" => $negative_desc);

            //dob calculated number
            $loginuser_dob = $loginuser->dob;
            $explodedate = explode('-', $loginuser_dob);
            $dob_day = $explodedate[2];
            $dob_month = $explodedate[1];
            $dob_year = $explodedate[0];

            $split_day = str_split($dob_day, 1);
            $dobno = array_sum($split_day);
            $dobno = intval($dobno);
            while (strlen($dobno) != 1) {
                $dobno = str_split($dobno);
                $dobno = array_sum($dobno);
            }

            $month = str_split($dob_month, 1);
            $month_no = array_sum($month);
            $monthno = intval($month_no);
            if (strlen($monthno) != 1) {
                $split_monthno = str_split($monthno);
                $sum_monthno = array_sum($split_monthno);
                $monthno = $sum_monthno;
            }

            $year = str_split($dob_year, 1);
            $yearno = array_sum($year);
            while (strlen($yearno) != 1) {
                $splityearno = str_split($yearno);
                $sum_yearno = array_sum($splityearno);
                $yearno = $sum_yearno;
            }
            $year_no = intval($yearno);
            $destiny_no = $dobno + $monthno + $year_no;
            while (strlen($destiny_no) != 1) {
                $splitdestiny_no = str_split($destiny_no);
                $destiny_nosum = array_sum($splitdestiny_no);
                $destiny_no = $destiny_nosum;
            }

            $fav = Fav_unfav_parameter::where('type', 1)
                ->where('month_id', $dob_month)
                ->where('date', $dob_day)
                ->value('numbers');
            $unfav = Fav_unfav_parameter::where('type', 2)
                ->where('month_id', $dob_month)
                ->where('date', $dob_day)
                ->value('numbers');

            $fav_numbers = str_replace(' ', '', $fav);
            $favnumber_array = explode(',', $fav_numbers);
            $fav_arraycount = count($favnumber_array);

            $unfav_numbers = str_replace(' ', '', $unfav);
            $unfavnumber_array = explode(',', $unfav_numbers);
            $array_reverse_unfavnumber = array_reverse($unfavnumber_array);
            $unfav_arraycount = count($unfavnumber_array);

            $usernamefinal_perc = 0;
            if ($dobno == $loginusernamereadingno) {
                $dobpercentage = 98;
                $usernamefinal_perc = 98;
            } else {
                $dobpercentage = 0;
            }
            if ($destiny_no == $loginusernamereadingno) {
                $destinypercentage = 84;
                $usernamefinal_perc = 84;
            } else {
                $destinypercentage = 0;
            }
            if ($fav_arraycount == 3) {
                if ($favnumber_array[1] == $loginusernamereadingno) {
                    $favpercentage = 66;
                    $usernamefinal_perc = 66;
                } elseif ($favnumber_array[2] == $loginusernamereadingno) {
                    $favpercentage = 55;
                    $usernamefinal_perc = 55;
                } else {
                    $favpercentage = 0;
                }
            } elseif ($fav_arraycount == 4) {
                if ($favnumber_array[1] == $loginusernamereadingno) {
                    $favpercentage = 74;
                    $usernamefinal_perc = 74;
                } elseif ($favnumber_array[2] == $loginusernamereadingno) {
                    $favpercentage = 65;
                    $usernamefinal_perc = 65;
                } elseif ($favnumber_array[3] == $loginusernamereadingno) {
                    $favpercentage = 55;
                    $usernamefinal_perc = 55;
                } else {
                    $favpercentage = 0;
                }
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'fav number error'
                ]);
            }
            if ($unfav_arraycount == 2) {
                if ($array_reverse_unfavnumber[0] == $loginusernamereadingno) {
                    $unfavpercentage = 30;
                    $usernamefinal_perc = 30;
                } elseif ($array_reverse_unfavnumber[1] == $loginusernamereadingno) {
                    $unfavpercentage = 15;
                    $usernamefinal_perc = 15;
                } else {
                    $unfavpercentage = 0;
                }
            } elseif ($unfav_arraycount == 3) {
                if ($array_reverse_unfavnumber[0] == $loginusernamereadingno) {
                    $unfavpercentage = 35;
                    $usernamefinal_perc = 35;
                } elseif ($array_reverse_unfavnumber[1] == $loginusernamereadingno) {
                    $unfavpercentage = 23;
                    $usernamefinal_perc = 23;
                } elseif ($array_reverse_unfavnumber[2] == $loginusernamereadingno) {
                    $unfavpercentage = 12;
                    $usernamefinal_perc = 12;
                } else {
                    $unfavpercentage = 0;
                }
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'unfav number error'
                ]);
            }
            if ($dobpercentage == 0 && $destinypercentage == 0 && $favpercentage == 0 && $unfavpercentage == 0) {
                $usernamefinal_perc = 50;
            }


            //loginuser magicbox

            $chald_number_count = array_count_values($loginuserchaldno);

            if (array_key_exists(1, $chald_number_count)) {
                $magicboxnumber1 = $chald_number_count['1'];
            } else {
                $magicboxnumber1 = 0;
            }
            $magicboxnumber1_decs = Module_description::where('moduletype_id', 3)
                ->where('number', 1)
                ->first();

            $magicboxnumber1decs = explode('||', strip_tags($magicboxnumber1_decs->description));
            $box1decs = $magicboxnumber1decs[0];
            $box1manydecs = $magicboxnumber1decs[1];
            $box1fewdecs = $magicboxnumber1decs[2];
            if ($magicboxnumber1_decs->mogicbox_average <= $magicboxnumber1) {
                $title = 'Many 1s';
                $description = $box1manydecs;
            } else {
                $title = 'Few/No 1s';
                $description = $box1fewdecs;
            }
            $magicboxnumberdecs1 = array('Box' => $box1decs, 'title' => $title, 'description' => $description, 'average_desc' => "Three 1s are average.");

            $boxcellno1 = array('number_heading' => 'number_heading', 'number' => 1, 'numbervalue' => $magicboxnumber1, 'numberdescription' => $magicboxnumberdecs1);

            if (array_key_exists(2, $chald_number_count)) {
                $magicboxnumber2 = $chald_number_count['2'];
            } else {
                $magicboxnumber2 = 0;
            }
            $magicboxnumber2_decs = Module_description::where('moduletype_id', 3)
                ->where('number', 2)
                ->first();

            $magicboxnumber2decs = explode('||', strip_tags($magicboxnumber2_decs->description));
            $box2decs = $magicboxnumber2decs[0];
            $box2manydecs = $magicboxnumber2decs[1];
            $box2fewdecs = $magicboxnumber2decs[2];
            if ($magicboxnumber2_decs->mogicbox_average <= $magicboxnumber2) {
                $title = 'Many 2s';
                $description = $box2manydecs;
            } else {
                $title = 'Few/No 2s';
                $description = $box2fewdecs;
            }
            $magicboxnumberdecs2 = array('Box' => $box2decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 2 is average.");

            $boxcellno2 = array('number_heading' => 'number_heading', 'number' => 2, 'numbervalue' => $magicboxnumber2, 'numberdescription' => $magicboxnumberdecs2);

            if (array_key_exists(3, $chald_number_count)) {
                $magicboxnumber3 = $chald_number_count['3'];
            } else {
                $magicboxnumber3 = 0;
            }
            $magicboxnumber3_decs = Module_description::where('moduletype_id', 3)
                ->where('number', 3)
                ->first();

            $magicboxnumber3decs = explode('||', strip_tags($magicboxnumber3_decs->description));
            $box3decs = $magicboxnumber3decs[0];
            $box3manydecs = $magicboxnumber3decs[1];
            $box3fewdecs = $magicboxnumber3decs[2];
            if ($magicboxnumber3_decs->mogicbox_average <= $magicboxnumber3) {
                $title = 'Many 3s';
                $description = $box3manydecs;
            } else {
                $title = 'Few/No 3s';
                $description = $box3fewdecs;
            }
            $magicboxnumberdecs3 = array('Box' => $box3decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 3 is average.");

            $boxcellno3 = array('number_heading' => 'number_heading', 'number' => 3, 'numbervalue' => $magicboxnumber3, 'numberdescription' => $magicboxnumberdecs3);

            if (array_key_exists(4, $chald_number_count)) {
                $magicboxnumber4 = $chald_number_count['4'];
            } else {
                $magicboxnumber4 = 0;
            }
            $magicboxnumber4_decs = Module_description::where('moduletype_id', 3)
                ->where('number', 4)
                ->first();

            $magicboxnumber4decs = explode('||', strip_tags($magicboxnumber4_decs->description));
            $box4decs = $magicboxnumber4decs[0];
            $box4manydecs = $magicboxnumber4decs[1];
            $box4fewdecs = $magicboxnumber4decs[2];
            if ($magicboxnumber4_decs->mogicbox_average <= $magicboxnumber4) {
                $title = 'Many 4s';
                $description = $box4manydecs;
            } else {
                $title = 'Few/No 4s';
                $description = $box4fewdecs;
            }

            $magicboxnumberdecs4 = array('Box' => $box4decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 4 is average.");

            $boxcellno4 = array('number_heading' => 'number_heading', 'number' => 4, 'numbervalue' => $magicboxnumber4, 'numberdescription' => $magicboxnumberdecs4);

            if (array_key_exists(5, $chald_number_count)) {
                $magicboxnumber5 = $chald_number_count['5'];
            } else {
                $magicboxnumber5 = 0;
            }
            $magicboxnumber5_decs = Module_description::where('moduletype_id', 3)
                ->where('number', 5)
                ->first();

            $magicboxnumber5decs = explode('||', strip_tags($magicboxnumber5_decs->description));
            $box5decs = $magicboxnumber5decs[0];
            $box5manydecs = $magicboxnumber5decs[1];
            $box5fewdecs = $magicboxnumber5decs[2];
            if ($magicboxnumber5_decs->mogicbox_average <= $magicboxnumber5) {
                $title = 'Many 5s';
                $description = $box5manydecs;
            } else {
                $title = 'Few/No 5s';
                $description = $box5fewdecs;
            }

            $magicboxnumberdecs5 = array('Box' => $box5decs, 'title' => $title, 'description' => $description, 'average_desc' => "Three 5s are average.");

            $boxcellno5 = array('number_heading' => 'number_heading', 'number' => 5, 'numbervalue' => $magicboxnumber5, 'numberdescription' => $magicboxnumberdecs5);


            if (array_key_exists(6, $chald_number_count)) {
                $magicboxnumber6 = $chald_number_count['6'];
            } else {
                $magicboxnumber6 = 0;
            }
            $magicboxnumber6_decs = Module_description::where('moduletype_id', 3)
                ->where('number', 6)
                ->first();

            $magicboxnumber6decs = explode('||', strip_tags($magicboxnumber6_decs->description));
            $box6decs = $magicboxnumber6decs[0];
            $box6manydecs = $magicboxnumber6decs[1];
            $box6fewdecs = $magicboxnumber6decs[2];
            if ($magicboxnumber6_decs->mogicbox_average <= $magicboxnumber6) {
                $title = 'Many 6s';
                $description = $box6manydecs;
            } else {
                $title = 'Few/No 6s';
                $description = $box6fewdecs;
            }

            $magicboxnumberdecs6 = array('Box' => $box6decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 6 is average.");

            $boxcellno6 = array('number_heading' => 'number_heading', 'number' => 6, 'numbervalue' => $magicboxnumber6, 'numberdescription' => $magicboxnumberdecs6);

            if (array_key_exists(7, $chald_number_count)) {
                $magicboxnumber7 = $chald_number_count['7'];
            } else {
                $magicboxnumber7 = 0;
            }

            $magicboxnumber7_decs = Module_description::where('moduletype_id', 3)
                ->where('number', 7)
                ->first();

            $magicboxnumber7decs = explode('||', strip_tags($magicboxnumber7_decs->description));
            $box7decs = $magicboxnumber7decs[0];
            $box7manydecs = $magicboxnumber7decs[1];
            $box7fewdecs = $magicboxnumber7decs[2];
            if ($magicboxnumber7_decs->mogicbox_average <= $magicboxnumber7) {
                $title = 'Many 7s';
                $description = $box7manydecs;
            } else {
                $title = 'Few/No 7s';
                $description = $box7fewdecs;
            }

            $magicboxnumberdecs7 = array('Box' => $box7decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 7 is average.");

            $boxcellno7 = array('number_heading' => 'number_heading', 'number' => 7, 'numbervalue' => $magicboxnumber7, 'numberdescription' => $magicboxnumberdecs7);

            if (array_key_exists(8, $chald_number_count)) {
                $magicboxnumber8 = $chald_number_count['8'];
            } else {
                $magicboxnumber8 = 0;
            }
            $magicboxnumber8_decs = Module_description::where('moduletype_id', 3)
                ->where('number', 8)
                ->first();

            $magicboxnumber8decs = explode('||', strip_tags($magicboxnumber8_decs->description));
            $box8decs = $magicboxnumber8decs[0];
            $box8manydecs = $magicboxnumber8decs[1];
            $box8fewdecs = $magicboxnumber8decs[2];
            if ($magicboxnumber8_decs->mogicbox_average <= $magicboxnumber8) {
                $title = 'Many 8s';
                $description = $box8manydecs;
            } else {
                $title = 'Few/No 8s';
                $description = $box8fewdecs;
            }

            $magicboxnumberdecs8 = array('Box' => $box8decs, 'title' => $title, 'description' => $description, 'average_desc' => "Three 8s are average.");

            $boxcellno8 = array('number_heading' => 'number_heading', 'number' => 8, 'numbervalue' => $magicboxnumber8, 'numberdescription' => $magicboxnumberdecs8);

            if (array_key_exists(9, $chald_number_count)) {
                $magicboxnumber9 = $chald_number_count['9'];
            } else {
                $magicboxnumber9 = 0;
            }
            $magicboxnumber9_decs = Module_description::where('moduletype_id', 3)
                ->where('number', 9)
                ->first();
            $magicboxnumber9decs = explode('||', strip_tags($magicboxnumber9_decs->description));
            $box9decs = $magicboxnumber9decs[0];
            $box9manydecs = $magicboxnumber9decs[1];
            $box9fewdecs = $magicboxnumber9decs[2];
            if ($magicboxnumber9_decs->mogicbox_average <= $magicboxnumber9) {
                $title = 'Many 9s';
                $description = $box9manydecs;
            } else {
                $title = 'Few/No 9s';
                $description = $box9fewdecs;
            }

            $magicboxnumberdecs9 = array('Box' => $box9decs, 'title' => $title, 'description' => $description, 'average_desc' => "Three 9s are average.");
            $boxcellno9 = array('number_heading' => 'number_heading', 'number' => 9, 'numbervalue' => $magicboxnumber9, 'numberdescription' => $magicboxnumberdecs9);
            $magicboxdetail = array($boxcellno1, $boxcellno2, $boxcellno3, $boxcellno4, $boxcellno5, $boxcellno6, $boxcellno7, $boxcellno8, $boxcellno9);

            return response()->json([
                'status' => 1,
                'message' => 'success',
                'loginuser_id' => $loginuser->id,
                'subscription_status' => $loginuser->subscription_status,
                'name' => $loginuser->name,
                'dob' => $loginuser->dob,
                'namecompatibilitypercentage' => $usernamefinal_perc,
                'namedec' => $loginusernamedesc,
                'magicboxdetail' => $magicboxdetail,
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Error',
            ]);
        }
    }

    public function editanotherusernamecompatibilitycheck(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required',
            'name' => 'required',
            'check_date' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $userid = $request->userid;
        $anothername = $request->name;
        $check_date = $request->check_date;
        $user = User::find($userid);
        $firstDateOfWeek = date("Y-m-d", strtotime('monday this week'));
        $lastdateOfWeek = date("Y-m-d", strtotime('sunday this week'));
        $namecompatibilityCheck = User_historyname::where('user_id', $userid)->whereBetween('created_at', [$firstDateOfWeek . " 00:00:00", $lastdateOfWeek . " 23:59:59"])->get();

        if (count($namecompatibilityCheck) == 0 && $user->subscription_status == 0) {
            $message = "You have only Two compatibility check left in this week.";
        } elseif (count($namecompatibilityCheck) == 0 && $user->subscription_status == 2) {
            $message = "You have only Two compatibility check left in this week.";
        }elseif (count($namecompatibilityCheck) == 1 && $user->subscription_status == 0) {
            $message = "You have only One compatibility check left in this week.";
        } elseif (count($namecompatibilityCheck) == 1 && $user->subscription_status == 2) {
            $message = "You have only One compatibility check left in this week.";
        }elseif (count($namecompatibilityCheck) == 2 && $user->subscription_status == 0) {
            $message = "You don't have any compatibility check left in this week. Please wait for next week Or Upgrade and Subscribe to Unlock Premium Features!";
        } elseif (count($namecompatibilityCheck) == 2 && $user->subscription_status == 2) {
            $message = "You don't have any compatibility checks left this week. Please wait for next week Or Your monthly plan has been paused. Please resume and unlock premium features!";
        } else {
            $message = "Sucess";
        }
        if (count($namecompatibilityCheck) >= 3 && $user->subscription_status == 0) {
            return response()->json([
                'status' => 0,
                'message' => "You don't have any compatibility check left in this week. Please wait for next week Or Upgrade and Subscribe to Unlock Premium Features!",
                'subscription_status' => $user->subscription_status,
            ]);
        }elseif (count($namecompatibilityCheck) >= 3 && $user->subscription_status == 2) {
            return response()->json([
                'status' => 0,
                'message' => "You don't have any compatibility checks left this week. Please wait for next week Or Your monthly plan has been paused. Please resume and unlock premium features!",
                'subscription_status' => $user->subscription_status,
            ]);
        }else 
        {
            $saveanothername = User_historyname::create([
                'user_id' => $userid,
                'name' => $anothername,
                'status' => 0,
                'check_date' => $check_date
            ]);

            if ($saveanothername) {
                $loginuserdetail = User::find($userid);

                $anothernamechaldno = array();
                $finalname = str_replace(' ', '', $anothername);
                $anothernamestrname = strtoupper($finalname);
                $anothernamesplitname = str_split($anothernamestrname, 1);
                foreach ($anothernamesplitname as $nameletter) {
                    $anothernamechald_no = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                    array_push($anothernamechaldno, $anothernamechald_no);
                }

                $anothernamechaldno_sum = array_sum($anothernamechaldno);
                while (strlen($anothernamechaldno_sum) != 1) {
                    $anothernamechaldno_sum = str_split($anothernamechaldno_sum, 1);
                    $anothernamechaldno_sum = array_sum($anothernamechaldno_sum);
                }

                $anothernamenamereadingno = $anothernamechaldno_sum;
                $anothernamenamereading =  Module_description::where('moduletype_id', 1)
                    ->where('number', $anothernamenamereadingno)
                    ->value('description');

                $anothernamedesc = strip_tags($anothernamenamereading);
                $explodeanothernamereading_desc = explode('||', $anothernamedesc);
                $anothernamepositive_desc = $explodeanothernamereading_desc[0];
                $anothernamenegative_desc = $explodeanothernamereading_desc[1];
                $anothernamenamedesc = array("positive_title" => "Positive", "positive_desc" => $anothernamepositive_desc, "negative_title" => "Negative", "negative_desc" => $anothernamenegative_desc);

                $anothernamenamereadingno = $anothernamechaldno_sum;


                // $name_compatibilitypercentage = Compatibility_percentage::where('number', $anothernamenamereadingno)->where('mate_number', $dobno)->first();
                // $namecompatibilitypercentage = $name_compatibilitypercentage->compatibility_percentage;


                //dob calculated number
                $loginuser_dob = $loginuserdetail->dob;
                $explodedate = explode('-', $loginuser_dob);
                $dob_day = $explodedate[2];
                $dob_month = $explodedate[1];
                $dob_year = $explodedate[0];

                $split_day = str_split($dob_day, 1);
                $dobno = array_sum($split_day);
                $dobno = intval($dobno);
                while (strlen($dobno) != 1) {
                    $dobno = str_split($dobno);
                    $dobno = array_sum($dobno);
                }

                $month = str_split($dob_month, 1);
                $month_no = array_sum($month);
                $monthno = intval($month_no);
                if (strlen($monthno) != 1) {
                    $split_monthno = str_split($monthno);
                    $sum_monthno = array_sum($split_monthno);
                    $monthno = $sum_monthno;
                }

                $year = str_split($dob_year, 1);
                $yearno = array_sum($year);
                while (strlen($yearno) != 1) {
                    $splityearno = str_split($yearno);
                    $sum_yearno = array_sum($splityearno);
                    $yearno = $sum_yearno;
                }
                $year_no = intval($yearno);
                $destiny_no = $dobno + $monthno + $year_no;
                while (strlen($destiny_no) != 1) {
                    $splitdestiny_no = str_split($destiny_no);
                    $destiny_nosum = array_sum($splitdestiny_no);
                    $destiny_no = $destiny_nosum;
                }

                $fav = Fav_unfav_parameter::where('type', 1)
                    ->where('month_id', $dob_month)
                    ->where('date', $dob_day)
                    ->value('numbers');
                $unfav = Fav_unfav_parameter::where('type', 2)
                    ->where('month_id', $dob_month)
                    ->where('date', $dob_day)
                    ->value('numbers');

                $fav_numbers = str_replace(' ', '', $fav);
                $favnumber_array = explode(',', $fav_numbers);
                $fav_arraycount = count($favnumber_array);

                $unfav_numbers = str_replace(' ', '', $unfav);
                $unfavnumber_array = explode(',', $unfav_numbers);
                $array_reverse_unfavnumber = array_reverse($unfavnumber_array);
                $unfav_arraycount = count($unfavnumber_array);

                $anothernamefinal_perc = 0;
                if ($dobno == $anothernamenamereadingno) {
                    $dobpercentage = 98;
                    $anothernamefinal_perc = 98;
                } else {
                    $dobpercentage = 0;
                }
                if ($destiny_no == $anothernamenamereadingno) {
                    $destinypercentage = 84;
                    $anothernamefinal_perc = 84;
                } else {
                    $destinypercentage = 0;
                }
                if ($fav_arraycount == 3) {
                    if ($favnumber_array[1] == $anothernamenamereadingno) {
                        $favpercentage = 66;
                        $anothernamefinal_perc = 66;
                    } elseif ($favnumber_array[2] == $anothernamenamereadingno) {
                        $favpercentage = 55;
                        $anothernamefinal_perc = 55;
                    } else {
                        $favpercentage = 0;
                    }
                } elseif ($fav_arraycount == 4) {
                    if ($favnumber_array[1] == $anothernamenamereadingno) {
                        $favpercentage = 74;
                        $anothernamefinal_perc = 74;
                    } elseif ($favnumber_array[2] == $anothernamenamereadingno) {
                        $favpercentage = 65;
                        $anothernamefinal_perc = 65;
                    } elseif ($favnumber_array[3] == $anothernamenamereadingno) {
                        $favpercentage = 55;
                        $anothernamefinal_perc = 55;
                    } else {
                        $favpercentage = 0;
                    }
                } else {
                    return response()->json([
                        'status' => 0,
                        'message' => 'fav number error'
                    ]);
                }
                if ($unfav_arraycount == 2) {
                    if ($array_reverse_unfavnumber[0] == $anothernamenamereadingno) {
                        $unfavpercentage = 30;
                        $anothernamefinal_perc = 30;
                    } elseif ($array_reverse_unfavnumber[1] == $anothernamenamereadingno) {
                        $unfavpercentage = 15;
                        $anothernamefinal_perc = 15;
                    } else {
                        $unfavpercentage = 0;
                    }
                } elseif ($unfav_arraycount == 3) {
                    if ($array_reverse_unfavnumber[0] == $anothernamenamereadingno) {
                        $unfavpercentage = 35;
                        $anothernamefinal_perc = 35;
                    } elseif ($array_reverse_unfavnumber[1] == $anothernamenamereadingno) {
                        $unfavpercentage = 23;
                        $anothernamefinal_perc = 23;
                    } elseif ($array_reverse_unfavnumber[2] == $anothernamenamereadingno) {
                        $unfavpercentage = 12;
                        $anothernamefinal_perc = 12;
                    } else {
                        $unfavpercentage = 0;
                    }
                } else {
                    return response()->json([
                        'status' => 0,
                        'message' => 'unfav number error'
                    ]);
                }
                if ($dobpercentage == 0 && $destinypercentage == 0 && $favpercentage == 0 && $unfavpercentage == 0) {
                    $anothernamefinal_perc = 50;
                }



                //anothername magicbox

                $chald_number_count = array_count_values($anothernamechaldno);

                if (array_key_exists(1, $chald_number_count)) {
                    $magicboxnumber1 = $chald_number_count['1'];
                } else {
                    $magicboxnumber1 = 0;
                }
                $magicboxnumber1_decs = Module_description::where('moduletype_id', 3)
                    ->where('number', 1)
                    ->first();

                $magicboxnumber1decs = explode('||', strip_tags($magicboxnumber1_decs->description));
                $box1decs = $magicboxnumber1decs[0];
                $box1manydecs = $magicboxnumber1decs[1];
                $box1fewdecs = $magicboxnumber1decs[2];
                if ($magicboxnumber1_decs->mogicbox_average <= $magicboxnumber1) {
                    $title = 'Many 1s';
                    $description = $box1manydecs;
                } else {
                    $title = 'Few/No 1s';
                    $description = $box1fewdecs;
                }
                $magicboxnumberdecs1 = array('Box' => $box1decs, 'title' => $title, 'description' => $description, 'average_desc' => "Three 1s are average.");

                $boxcellno1 = array('number_heading' => 'number_heading', 'number' => 1, 'numbervalue' => $magicboxnumber1, 'numberdescription' => $magicboxnumberdecs1);

                if (array_key_exists(2, $chald_number_count)) {
                    $magicboxnumber2 = $chald_number_count['2'];
                } else {
                    $magicboxnumber2 = 0;
                }
                $magicboxnumber2_decs = Module_description::where('moduletype_id', 3)
                    ->where('number', 2)
                    ->first();

                $magicboxnumber2decs = explode('||', strip_tags($magicboxnumber2_decs->description));
                $box2decs = $magicboxnumber2decs[0];
                $box2manydecs = $magicboxnumber2decs[1];
                $box2fewdecs = $magicboxnumber2decs[2];
                if ($magicboxnumber2_decs->mogicbox_average <= $magicboxnumber2) {
                    $title = 'Many 2s';
                    $description = $box2manydecs;
                } else {
                    $title = 'Few/No 2s';
                    $description = $box2fewdecs;
                }
                $magicboxnumberdecs2 = array('Box' => $box2decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 2 is average.");

                $boxcellno2 = array('number_heading' => 'number_heading', 'number' => 2, 'numbervalue' => $magicboxnumber2, 'numberdescription' => $magicboxnumberdecs2);

                if (array_key_exists(3, $chald_number_count)) {
                    $magicboxnumber3 = $chald_number_count['3'];
                } else {
                    $magicboxnumber3 = 0;
                }
                $magicboxnumber3_decs = Module_description::where('moduletype_id', 3)
                    ->where('number', 3)
                    ->first();

                $magicboxnumber3decs = explode('||', strip_tags($magicboxnumber3_decs->description));
                $box3decs = $magicboxnumber3decs[0];
                $box3manydecs = $magicboxnumber3decs[1];
                $box3fewdecs = $magicboxnumber3decs[2];
                if ($magicboxnumber3_decs->mogicbox_average <= $magicboxnumber3) {
                    $title = 'Many 3s';
                    $description = $box3manydecs;
                } else {
                    $title = 'Few/No 3s';
                    $description = $box3fewdecs;
                }
                $magicboxnumberdecs3 = array('Box' => $box3decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 3 is average.");

                $boxcellno3 = array('number_heading' => 'number_heading', 'number' => 3, 'numbervalue' => $magicboxnumber3, 'numberdescription' => $magicboxnumberdecs3);

                if (array_key_exists(4, $chald_number_count)) {
                    $magicboxnumber4 = $chald_number_count['4'];
                } else {
                    $magicboxnumber4 = 0;
                }
                $magicboxnumber4_decs = Module_description::where('moduletype_id', 3)
                    ->where('number', 4)
                    ->first();

                $magicboxnumber4decs = explode('||', strip_tags($magicboxnumber4_decs->description));
                $box4decs = $magicboxnumber4decs[0];
                $box4manydecs = $magicboxnumber4decs[1];
                $box4fewdecs = $magicboxnumber4decs[2];
                if ($magicboxnumber4_decs->mogicbox_average <= $magicboxnumber4) {
                    $title = 'Many 4s';
                    $description = $box4manydecs;
                } else {
                    $title = 'Few/No 4s';
                    $description = $box4fewdecs;
                }

                $magicboxnumberdecs4 = array('Box' => $box4decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 4 is average.");

                $boxcellno4 = array('number_heading' => 'number_heading', 'number' => 4, 'numbervalue' => $magicboxnumber4, 'numberdescription' => $magicboxnumberdecs4);

                if (array_key_exists(5, $chald_number_count)) {
                    $magicboxnumber5 = $chald_number_count['5'];
                } else {
                    $magicboxnumber5 = 0;
                }
                $magicboxnumber5_decs = Module_description::where('moduletype_id', 3)
                    ->where('number', 5)
                    ->first();

                $magicboxnumber5decs = explode('||', strip_tags($magicboxnumber5_decs->description));
                $box5decs = $magicboxnumber5decs[0];
                $box5manydecs = $magicboxnumber5decs[1];
                $box5fewdecs = $magicboxnumber5decs[2];
                if ($magicboxnumber5_decs->mogicbox_average <= $magicboxnumber5) {
                    $title = 'Many 5s';
                    $description = $box5manydecs;
                } else {
                    $title = 'Few/No 5s';
                    $description = $box5fewdecs;
                }

                $magicboxnumberdecs5 = array('Box' => $box5decs, 'title' => $title, 'description' => $description, 'average_desc' => "Three 5s are average.");

                $boxcellno5 = array('number_heading' => 'number_heading', 'number' => 5, 'numbervalue' => $magicboxnumber5, 'numberdescription' => $magicboxnumberdecs5);


                if (array_key_exists(6, $chald_number_count)) {
                    $magicboxnumber6 = $chald_number_count['6'];
                } else {
                    $magicboxnumber6 = 0;
                }
                $magicboxnumber6_decs = Module_description::where('moduletype_id', 3)
                    ->where('number', 6)
                    ->first();

                $magicboxnumber6decs = explode('||', strip_tags($magicboxnumber6_decs->description));
                $box6decs = $magicboxnumber6decs[0];
                $box6manydecs = $magicboxnumber6decs[1];
                $box6fewdecs = $magicboxnumber6decs[2];
                if ($magicboxnumber6_decs->mogicbox_average <= $magicboxnumber6) {
                    $title = 'Many 6s';
                    $description = $box6manydecs;
                } else {
                    $title = 'Few/No 6s';
                    $description = $box6fewdecs;
                }

                $magicboxnumberdecs6 = array('Box' => $box6decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 6 is average.");

                $boxcellno6 = array('number_heading' => 'number_heading', 'number' => 6, 'numbervalue' => $magicboxnumber6, 'numberdescription' => $magicboxnumberdecs6);

                if (array_key_exists(7, $chald_number_count)) {
                    $magicboxnumber7 = $chald_number_count['7'];
                } else {
                    $magicboxnumber7 = 0;
                }

                $magicboxnumber7_decs = Module_description::where('moduletype_id', 3)
                    ->where('number', 7)
                    ->first();

                $magicboxnumber7decs = explode('||', strip_tags($magicboxnumber7_decs->description));
                $box7decs = $magicboxnumber7decs[0];
                $box7manydecs = $magicboxnumber7decs[1];
                $box7fewdecs = $magicboxnumber7decs[2];
                if ($magicboxnumber7_decs->mogicbox_average <= $magicboxnumber7) {
                    $title = 'Many 7s';
                    $description = $box7manydecs;
                } else {
                    $title = 'Few/No 7s';
                    $description = $box7fewdecs;
                }

                $magicboxnumberdecs7 = array('Box' => $box7decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 7 is average.");

                $boxcellno7 = array('number_heading' => 'number_heading', 'number' => 7, 'numbervalue' => $magicboxnumber7, 'numberdescription' => $magicboxnumberdecs7);

                if (array_key_exists(8, $chald_number_count)) {
                    $magicboxnumber8 = $chald_number_count['8'];
                } else {
                    $magicboxnumber8 = 0;
                }
                $magicboxnumber8_decs = Module_description::where('moduletype_id', 3)
                    ->where('number', 8)
                    ->first();

                $magicboxnumber8decs = explode('||', strip_tags($magicboxnumber8_decs->description));
                $box8decs = $magicboxnumber8decs[0];
                $box8manydecs = $magicboxnumber8decs[1];
                $box8fewdecs = $magicboxnumber8decs[2];
                if ($magicboxnumber8_decs->mogicbox_average <= $magicboxnumber8) {
                    $title = 'Many 8s';
                    $description = $box8manydecs;
                } else {
                    $title = 'Few/No 8s';
                    $description = $box8fewdecs;
                }

                $magicboxnumberdecs8 = array('Box' => $box8decs, 'title' => $title, 'description' => $description, 'average_desc' => "Three 8s are average.");

                $boxcellno8 = array('number_heading' => 'number_heading', 'number' => 8, 'numbervalue' => $magicboxnumber8, 'numberdescription' => $magicboxnumberdecs8);

                if (array_key_exists(9, $chald_number_count)) {
                    $magicboxnumber9 = $chald_number_count['9'];
                } else {
                    $magicboxnumber9 = 0;
                }
                $magicboxnumber9_decs = Module_description::where('moduletype_id', 3)
                    ->where('number', 9)
                    ->first();
                $magicboxnumber9decs = explode('||', strip_tags($magicboxnumber9_decs->description));
                $box9decs = $magicboxnumber9decs[0];
                $box9manydecs = $magicboxnumber9decs[1];
                $box9fewdecs = $magicboxnumber9decs[2];
                if ($magicboxnumber9_decs->mogicbox_average <= $magicboxnumber9) {
                    $title = 'Many 9s';
                    $description = $box9manydecs;
                } else {
                    $title = 'Few/No 9s';
                    $description = $box9fewdecs;
                }

                $magicboxnumberdecs9 = array('Box' => $box9decs, 'title' => $title, 'description' => $description, 'average_desc' => "Three 9s are average.");
                $boxcellno9 = array('number_heading' => 'number_heading', 'number' => 9, 'numbervalue' => $magicboxnumber9, 'numberdescription' => $magicboxnumberdecs9);

                $magicboxdetail = array($boxcellno1, $boxcellno2, $boxcellno3, $boxcellno4, $boxcellno5, $boxcellno6, $boxcellno7, $boxcellno8, $boxcellno9);

                return response()->json([
                    'status' => 1,
                    'message' => $message,
                    'userid' => $userid,
                    'subscription_status' => $loginuserdetail->subscription_status,
                    'name' => $anothername,
                    'namecompatibilitypercentage' => $anothernamefinal_perc,
                    'anothernamenamedesc' => $anothernamenamedesc,
                    'magicboxdetail' => $magicboxdetail,
                ]);
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'Record not found. Please try again !'
                ]);
            }
        }
    }

    public function userlifecoach(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required',
            'current_date' => 'required',
            'date_current' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $userid = $request->userid;
        $current_date = $request->current_date;
        $date_current = $request->date_current;
        $explodeDate_current = explode('-', $date_current);
        $current_dateyear = $explodeDate_current[0];
        $user = User::find($userid);

        if ($user) {
            $userdob = $user->dob;
            $explode_dob = explode("-", $userdob);
            $dob_date = $explode_dob[2];
            $dob_month = $explode_dob[1];

            $favdata = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 1)
                ->where('month_id', $dob_month)
                ->where('date', $dob_date)
                ->first();
            $unfavdata = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 2)
                ->where('month_id', $dob_month)
                ->where('date', $dob_date)
                ->first();

            $fav_dates = $favdata->numbers;
            $fav_dates = str_replace(' ', '', $fav_dates);
            $fav_dates = explode(',', $fav_dates);
            $fav_days = $favdata->days;
            $fav_days = str_replace(' ', '', $fav_days);
            $fav_days = explode(',', $fav_days);
            $fav_months = $favdata->months;
            $fav_months = str_replace(' ', '', $fav_months);
            $fav_months = explode(',', $fav_months);

            $unfav_dates = $unfavdata->numbers;
            $unfav_dates = str_replace(' ', '', $unfav_dates);
            $unfav_dates = explode(',', $unfav_dates);
            $unfav_days = $unfavdata->days;
            $unfav_days = str_replace(' ', '', $unfav_days);
            $unfav_days = explode(',', $unfav_days);
            $unfav_months = $unfavdata->months;
            $unfav_months = str_replace(' ', '', $unfav_months);
            $unfav_months = explode(',', $unfav_months);

            $currentdateformate = explode('-', $current_date);
            $date_split = str_split($currentdateformate[1], 1);
            $date_sum = array_sum($date_split);
            while (strlen($date_sum) != 1) {
                $date_sum = str_split($date_sum);
                $date_sum = array_sum($date_sum);
            }
            $fav_cosmic_stars = 0;
            $unfav_cosmic_stars = 0;
            $day_star = 0;
            $month_star = 0;
            if (in_array($date_sum, $fav_dates)) {
                $date_star = 1;
                if (in_array($currentdateformate[0], $fav_days)) {
                    $day_star = 1;
                }
                if (in_array($currentdateformate[2], $fav_months)) {
                    $month_star = 1;
                }
            } else {
                $date_star = 0;
            }

            $unfavday_star = 0;
            $unfavmonth_star = 0;
            if (in_array($date_sum, $unfav_dates)) {
                $unfavdate_star = 1;
                if (in_array($currentdateformate[0], $unfav_days)) {
                    $unfavday_star = 1;
                }
                if (in_array($currentdateformate[2], $unfav_months)) {
                    $unfavmonth_star = 1;
                }
            } else {
                $unfavdate_star = 0;
            }

            $fav_cosmic_stars = $date_star + $day_star + $month_star;
            $unfav_cosmic_stars = $unfavdate_star + $unfavday_star + $unfavmonth_star;

            $currentdayfav_star = $fav_cosmic_stars;
            $currentdayunfav_star = $unfav_cosmic_stars;

            //personal year
            $dobdate_array = array($dob_date, $dob_month, $current_dateyear);
            $personalyear = array_sum($dobdate_array);
            while (strlen($personalyear) != 1) {
                $splitpersonalyear = str_split($personalyear);
                $splitpersonalyear_sum = array_sum($splitpersonalyear);
                $personalyear = $splitpersonalyear_sum;
            }

            $personalYearNo_desc = Personal_parameter::where('type', 1)->where('number', $personalyear)->first();
            $personalYear_desc = $personalYearNo_desc->description;
            

            //personal month
            $currentDate = $date_current;
            $explodecurrentDate = explode('-', $currentDate);
            $current_monthno = $explodecurrentDate[1];
            while (strlen($current_monthno) != 1) {
                $splitcurrent_monthno = str_split($current_monthno);
                $splitcurrent_monthno_sum = array_sum($splitcurrent_monthno);
                $current_monthno = $splitcurrent_monthno_sum;
            }
            $calpersonal_monthno = $personalyear + $current_monthno;
            while (strlen($calpersonal_monthno) != 1) {
                $splitpersonalMonthno = str_split($calpersonal_monthno);
                $splitpersonalMonthno_sum = array_sum($splitpersonalMonthno);
                $calpersonal_monthno = $splitpersonalMonthno_sum;
            }
            
            $personalMonthNo_desc = Personal_parameter::where('type', 2)->where('number', $calpersonal_monthno)->first();
            $personalMonth_desc = $personalMonthNo_desc->description;

            //personal week
            $current_day = $explodecurrentDate[2];
            $week1 = array(1, 2, 3, 4, 5, 6, 7);
            if (in_array($current_day, $week1)) {
                $weekno = 1;
            }
            $week2 = array(8, 9, 10, 11, 12, 13, 14);
            if (in_array($current_day, $week2)) {
                $weekno = 2;
            }
            $week3 = array(15, 16, 17, 18, 19, 20, 21);
            if (in_array($current_day, $week3)) {
                $weekno = 3;
            }
            $week4 = array(22, 23, 24, 25, 26, 27, 28,29, 30, 31);
            if (in_array($current_day, $week4)) {
                $weekno = 4;
            }

            $calpersonal_weekNo = $calpersonal_monthno + $weekno;
            while (strlen($calpersonal_weekNo) != 1) {
                $splitpersonalWeekno = str_split($calpersonal_weekNo);
                $splitpersonalWeekno_sum = array_sum($splitpersonalWeekno);
                $calpersonal_weekNo = $splitpersonalWeekno_sum;
            }
            
            if($currentdayfav_star > 0){
                $personalWeekNo_desc = Lifecoach_description::where('type', 2)->where('star_type', '=', 1)->where('star_number', '=', 4)->where('number', $calpersonal_weekNo)->first();
            }elseif($currentdayunfav_star > 0){
                $personalWeekNo_desc = Lifecoach_description::where('type', 2)->where('star_type', '=', 2)->where('star_number', '=', 4)->where('number', $calpersonal_weekNo)->first();
            }else{
                $personalWeekNo_desc = Lifecoach_description::where('type', 2)->where('star_type', '=', 3)->where('star_number', '=', 4)->where('number', $calpersonal_weekNo)->first();
            }
            $personalWeek_desc = $personalWeekNo_desc->description;

            //personal day
            $cal_daynumber = $explodecurrentDate[2];
            $personal_dayno = $calpersonal_monthno + $cal_daynumber;
            $masterno = '';
            $masterno_description = '';
            while (strlen($personal_dayno) != 1) {
                if ($personal_dayno == 11 || $personal_dayno == 22) {
                    $masterno = $personal_dayno;
                }
                $splitpersonal_dayno = str_split($personal_dayno);
                $splitpersonal_daynosum = array_sum($splitpersonal_dayno);
                $personal_dayno = $splitpersonal_daynosum;
            }
            $personalDayNo_desc = Personal_parameter::where('type', 4)->where('number', $personal_dayno)->first();
            $personalDay_desc = $personalDayNo_desc->description;

            if ($masterno != '') {
                if ($masterno == 11) {
                    $master_no_description = Personal_parameter::where('type', 4)
                        ->where('number', 11)->first();
                    $masterno_description = $master_no_description->description;
                }
                if ($masterno == 22) {
                    $master_no_description = Personal_parameter::where('type', 4)
                        ->where('number', 22)->first();
                    $masterno_description = $master_no_description->description;
                }
            }

            //zodiac Sign	
            $formetdob = date("d-F-Y", strtotime($user->dob));
            $zodiacdate = explode('-', $formetdob);
            $dobday = $zodiacdate[0];
            $dobmonth = $zodiacdate[1];
            $zodiac = Zodic_sign::where('title', 'LIKE', '%' . $dobmonth . '%')
                ->get();

            if ($dobmonth == "March") {
                $titledaydate = $zodiac[1]->title;
                $explodetitle_daydate = explode(' ', $titledaydate);
                $title_daydate = $explodetitle_daydate[2];

                if ($dobday <= $title_daydate) {
                    $zodiacdata = $zodiac[1];
                } else {
                    $zodiacdata = $zodiac[0];
                }
            } else {
                $titledaydate = $zodiac[0]->title;
                $explodetitle_daydate = explode(' ', $titledaydate);
                $title_daydate = $explodetitle_daydate[2];

                if ($dobday <= $title_daydate) {
                    $zodiacdata = $zodiac[0];
                } else {
                    $zodiacdata = $zodiac[1];
                }
            }

            $user_zodiacsign = strtolower($zodiacdata->zodic_sign);

            if ($currentdayfav_star != 0) {
                $lifecoachtype = Dailycoach_type::where('type', $personal_dayno)->first();
                $lifecoach_description = Lifecoach_description::where('type', '=', 1)->where('star_type', '=', 1)->where('star_number', '=', $currentdayfav_star)->where('number', '=', $personal_dayno)->first();
                $lifecoach = strip_tags($lifecoach_description->description);
                $short_lifecoach_desc = str_replace(' for', '',$lifecoach_description->short_description);
            } elseif ($currentdayunfav_star != 0) {
                $lifecoachtype = Dailycoach_type::where('type', $personal_dayno)->first();
                $lifecoach_description = Lifecoach_description::where('type', '=', 1)->where('star_type', '=', 2)->where('star_number', '=', $currentdayunfav_star)->where('number', '=', $personal_dayno)->first();
                $lifecoach = strip_tags($lifecoach_description->description);
                $short_lifecoach_desc = str_replace(' for', '',$lifecoach_description->short_description);
            } else {
                $lifecoachtype = Dailycoach_type::where('type', $personal_dayno)->first();
                $lifecoach_description = Lifecoach_description::where('type', '=', 1)->where('star_type', '=', 3)->where('star_number', '=', 0)->where('number', '=', $personal_dayno)->first();
                $lifecoach = strip_tags($lifecoach_description->description);
                $short_lifecoach_desc = str_replace(' for', '',$lifecoach_description->short_description);
            }

            //Relationship Number, Health numbers, Career Number and Travel number
            $relationshipno_array = array("2", "6", "8");
            $healthno_array = array("4", "6", "9");
            $careerno_array = array("1", "3", "5", "8");
            $travelno_array = array("1", "5", "7", "9");


            $relationship_percentage = '';
            $relationship_description = "";

            $health_percentage = '';
            $health_description = "";

            $career_percentage = '';
            $career_description = "";

            $travel_percentage = '';
            $travel_description = "";

            if ($currentdayfav_star == 1) {

                if (in_array($personal_dayno, $relationshipno_array)) {
                    $relationship_percentage = 64;
                    $relationship_description = "Your Personal day number is " . $personal_dayno . ". Today is your One green star day for realtionship.";
                }
                if (in_array($personal_dayno, $healthno_array)) {
                    $health_percentage = 64;
                    $health_description = "Your Personal day number is " . $personal_dayno . ". Today is your One green star day for Health.";
                }
                if (in_array($personal_dayno, $careerno_array)) {
                    $career_percentage = 64;
                    $career_description = "Your Personal day number is " . $personal_dayno . ". Today is your One green star day for Career.";
                }
                if (in_array($personal_dayno, $travelno_array)) {
                    $travel_percentage = 64;
                    $travel_description = "Your Personal day number is " . $personal_dayno . ". Today is your One green star day for Travel.";
                }
            } elseif ($currentdayfav_star == 2) {

                if (in_array($personal_dayno, $relationshipno_array)) {
                    $relationship_percentage = 81;
                    $relationship_description = "Your Personal day number is " . $personal_dayno . ". Today is your Two green star day for realtionship.";
                }
                if (in_array($personal_dayno, $healthno_array)) {
                    $health_percentage = 81;
                    $health_description = "Your Personal day number is " . $personal_dayno . ". Today is your Two green star day for Health.";
                }
                if (in_array($personal_dayno, $careerno_array)) {
                    $career_percentage = 81;
                    $career_description = "Your Personal day number is " . $personal_dayno . ". Today is your Two green star day for Career.";
                }
                if (in_array($personal_dayno, $travelno_array)) {
                    $travel_percentage = 81;
                    $travel_description = "Your Personal day number is " . $personal_dayno . ". Today is your Two green star day for Travel.";
                }
            } elseif ($currentdayfav_star == 3) {

                if (in_array($personal_dayno, $relationshipno_array)) {
                    $relationship_percentage = 98;
                    $relationship_description = "Your Personal day number is " . $personal_dayno . ". Today is your Three green star day for realtionship.";
                }
                if (in_array($personal_dayno, $healthno_array)) {
                    $health_percentage = 98;
                    $health_description = "Your Personal day number is " . $personal_dayno . ". Today is your Three green star day for Health.";
                }
                if (in_array($personal_dayno, $careerno_array)) {
                    $career_percentage = 98;
                    $career_description = "Your Personal day number is " . $personal_dayno . ". Today is your Three green star day for Career.";
                }
                if (in_array($personal_dayno, $travelno_array)) {
                    $travel_percentage = 98;
                    $travel_description = "Your Personal day number is " . $personal_dayno . ". Today is your Three green star day for Travel.";
                }
            } elseif ($currentdayunfav_star == 1) {

                if (in_array($personal_dayno, $relationshipno_array)) {
                    $relationship_percentage = 35;
                    $relationship_description = "Your Personal day number is " . $personal_dayno . ". Today is your One red star day for realtionship.";
                }
                if (in_array($personal_dayno, $healthno_array)) {
                    $health_percentage = 35;
                    $health_description = "Your Personal day number is " . $personal_dayno . ". Today is your One red star day for Health.";
                }
                if (in_array($personal_dayno, $careerno_array)) {
                    $career_percentage = 35;
                    $career_description = "Your Personal day number is " . $personal_dayno . ". Today is your One red star day for Career.";
                }
                if (in_array($personal_dayno, $travelno_array)) {
                    $travel_percentage = 35;
                    $travel_description = "Your Personal day number is " . $personal_dayno . ". Today is your One red star day for Travel.";
                }
            } elseif ($currentdayunfav_star == 2) {

                if (in_array($personal_dayno, $relationshipno_array)) {
                    $relationship_percentage = 21;
                    $relationship_description = "Your Personal day number is " . $personal_dayno . ". Today is your Two red star day for realtionship.";
                }
                if (in_array($personal_dayno, $healthno_array)) {
                    $health_percentage = 21;
                    $health_description = "Your Personal day number is " . $personal_dayno . ". Today is your Two red star day for Health.";
                }
                if (in_array($personal_dayno, $careerno_array)) {
                    $career_percentage = 21;
                    $career_description = "Your Personal day number is " . $personal_dayno . ". Today is your Two red star day for Career.";
                }
                if (in_array($personal_dayno, $travelno_array)) {
                    $travel_percentage = 21;
                    $travel_description = "Your Personal day number is " . $personal_dayno . ". Today is your Two red star day for Travel.";
                }
            } elseif ($currentdayunfav_star == 3) {

                if (in_array($personal_dayno, $relationshipno_array)) {
                    $relationship_percentage = 7;
                    $relationship_description = "Your Personal day number is " . $personal_dayno . ". Today is your Three red star day for realtionship.";
                }
                if (in_array($personal_dayno, $healthno_array)) {
                    $health_percentage = 7;
                    $health_description = "Your Personal day number is " . $personal_dayno . ". Today is your Three red star day for Health.";
                }
                if (in_array($personal_dayno, $careerno_array)) {
                    $career_percentage = 7;
                    $career_description = "Your Personal day number is " . $personal_dayno . ". Today is your Three red star day for Career.";
                }
                if (in_array($personal_dayno, $travelno_array)) {
                    $travel_percentage = 7;
                    $travel_description = "Your Personal day number is " . $personal_dayno . ". Today is your Three red star day for Travel.";
                }
            } else {

                if (in_array($personal_dayno, $relationshipno_array)) {
                    $relationship_percentage = 50;
                    $relationship_description = "Your Personal day number is " . $personal_dayno . ". Today is your neutral day for realtionship.";
                }
                if (in_array($personal_dayno, $healthno_array)) {
                    $health_percentage = 50;
                    $health_description = "Your Personal day number is " . $personal_dayno . ". Today is your neutral day for Health.";
                }
                if (in_array($personal_dayno, $careerno_array)) {
                    $career_percentage = 50;
                    $career_description = "Your Personal day number is " . $personal_dayno . ". Today is your neutral day for Career.";
                }
                if (in_array($personal_dayno, $travelno_array)) {
                    $travel_percentage = 50;
                    $travel_description = "Your Personal day number is " . $personal_dayno . ". Today is your neutral day for Travel.";
                }
            }

            // if ($masterno_description != '') {
            //     $lifecoach = $masterno_description;
            //     $personal_dayno = $masterno;
            // }


            return response()->json([
                'status' => 1,
                'message' => 'success',
                'userid' => $userid,
                'subscription_status' => $user->subscription_status,
                'user_zodiacsign' => $user_zodiacsign,
                'short_lifecoach_desc' => $short_lifecoach_desc,
                'life_coach' => $lifecoach,
                'personal_year' => $personalyear,
                'personal_month' => $calpersonal_monthno,
                'personal_week' => $calpersonal_weekNo,
                'personal_day' => $personal_dayno,
                'personal_year_desc' => $personalYear_desc,
                'personal_month_desc' => $personalMonth_desc,
                'personal_week_desc' => $personalWeek_desc,
                'personal_day_desc' => $personalDay_desc,
                'masterno' => $masterno,
                'masterno_description' => $masterno_description,
                'relationship_percentage' => $relationship_percentage,
                'relationship_description' => $relationship_description,
                'health_percentage' => $health_percentage,
                'health_description' => $health_description,
                'career_percentage' => $career_percentage,
                'career_description' => $career_description,
                'travel_percentage' => $travel_percentage,
                'travel_description' => $travel_description

            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'User not found',
            ]);
        }
    }

    //create at 29-11-2022 start here
    public function userlifecoachcosmiccalender(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required',
            'date' => 'required',
            'current_date' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $userid = $request->userid;
        $req_date = $request->date;
        $current_date = $request->current_date;

        $user = User::find($userid);

        if ($user) {
            $joiningdate = $user->created_at->format('Y-m-d');
            $currentdate = $current_date;
            $explodecCurrent_date = explode('-', $current_date);
            $current_Year = $explodecCurrent_date[0];

            if ($req_date < $joiningdate && $user->subscription_status == 0) {
                return response()->json([
                    'status' => 0,
                    'message' => 'You do not have any paid version.',
                ]);
            } elseif ($req_date < $joiningdate && $user->subscription_status == 2) {
                return response()->json([
                    'status' => 0,
                    'message' => 'You do not have any paid version.',
                ]);
            } elseif ($req_date > $currentdate && $user->subscription_status == 0) {
                return response()->json([
                    'status' => 0,
                    'message' => 'You do not have any paid version.',
                ]);
            } elseif ($req_date > $currentdate && $user->subscription_status == 2) {
                return response()->json([
                    'status' => 0,
                    'message' => 'You do not have any paid version.',
                ]);
            } else {

                $userdob = $user->dob;
                $explode_dob = explode("-", $userdob);
                $dob_date = $explode_dob[2];
                $dob_month = $explode_dob[1];

                $favdata = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 1)
                    ->where('month_id', $dob_month)
                    ->where('date', $dob_date)
                    ->first();
                $unfavdata = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 2)
                    ->where('month_id', $dob_month)
                    ->where('date', $dob_date)
                    ->first();

                $fav_dates = $favdata->numbers;
                $str_replace_fav_dates = str_replace(' ', '', $fav_dates);
                $explode_fav_dates = explode(',', $str_replace_fav_dates);
                $fav_dates_array = $explode_fav_dates;

                $fav_days = $favdata->days;
                $str_replace_fav_days = str_replace(' ', '', $fav_days);
                $explode_fav_days = explode(',', $str_replace_fav_days);
                $array_fav_days = $explode_fav_days;

                $fav_months = $favdata->months;
                $str_replace_fav_months = str_replace(' ', '', $fav_months);
                $explode_fav_months = explode(',', $str_replace_fav_months);
                $fav_months_array = $explode_fav_months;

                $unfav_dates = $unfavdata->numbers;
                $unfav_dates = str_replace(' ', '', $unfav_dates);
                $unfav_dates = explode(',', $unfav_dates);

                $unfav_days = $unfavdata->days;
                $unfav_days = str_replace(' ', '', $unfav_days);
                $unfav_days = explode(',', $unfav_days);

                $unfav_months = $unfavdata->months;
                $unfav_months = str_replace(' ', '', $unfav_months);
                $unfav_months = explode(',', $unfav_months);

                $dateformate = explode('-', date("D-j-M-Y", strtotime($req_date)));
                $date_split = str_split($dateformate[1], 1);
                $date_sum = array_sum($date_split);

                while (strlen($date_sum) != 1) {
                    $splitdate_sum = str_split($date_sum);
                    $date_array_sum = array_sum($splitdate_sum);
                    $date_sum = $date_array_sum;
                }
                $fav_cosmic_stars = 0;
                $unfav_cosmic_stars = 0;
                $day_star = 0;
                $month_star = 0;
                if (in_array($date_sum, $fav_dates_array)) {
                    $date_star = 1;
                    if (in_array($dateformate[0], $array_fav_days)) {
                        $day_star = 1;
                    }
                    if (in_array($dateformate[2], $fav_months_array)) {
                        $month_star = 1;
                    }
                } else {
                    $date_star = 0;
                }

                $unfavday_star = 0;
                $unfavmonth_star = 0;
                if (in_array($date_sum, $unfav_dates)) {
                    $unfavdate_star = 1;
                    if (in_array($dateformate[0], $unfav_days)) {
                        $unfavday_star = 1;
                    }
                    if (in_array($dateformate[2], $unfav_months)) {
                        $unfavmonth_star = 1;
                    }
                } else {
                    $unfavdate_star = 0;
                }

                $fav_cosmic_stars = $date_star + $day_star + $month_star;
                $unfav_cosmic_stars = $unfavdate_star + $unfavday_star + $unfavmonth_star;

                $currentdayfav_star = $fav_cosmic_stars;
                $currentdayunfav_star = $unfav_cosmic_stars;


                //personal year
                $dobdate_array = array($dob_date, $dob_month, $current_Year);
                $personalyear = array_sum($dobdate_array);
                while (strlen($personalyear) != 1) {
                    $splitpersonalyear = str_split($personalyear);
                    $splitpersonalyear_sum = array_sum($splitpersonalyear);
                    $personalyear = $splitpersonalyear_sum;
                }
                $personalYearNo_desc = Personal_parameter::where('type', 1)->where('number', $personalyear)->first();
                $personalYear_desc = $personalYearNo_desc->description;

                //personal month
                $currentDate = $req_date;
                $explodecurrentDate = explode('-', $currentDate);
                $current_monthno = $explodecurrentDate[1];
                while (strlen($current_monthno) != 1) {
                    $splitcurrent_monthno = str_split($current_monthno);
                    $splitcurrent_monthno_sum = array_sum($splitcurrent_monthno);
                    $current_monthno = $splitcurrent_monthno_sum;
                }
                $calpersonal_monthno = $personalyear + $current_monthno;
                while (strlen($calpersonal_monthno) != 1) {
                    $splitpersonalMonthno = str_split($calpersonal_monthno);
                    $splitpersonalMonthno_sum = array_sum($splitpersonalMonthno);
                    $calpersonal_monthno = $splitpersonalMonthno_sum;
                }
                $personalMonthNo_desc = Personal_parameter::where('type', 2)->where('number', $calpersonal_monthno)->first();
                $personalMonth_desc = $personalMonthNo_desc->description;

                //personal week
                $check_Date = $explodecurrentDate[2];
                $week1 = array(1, 2, 3, 4, 5, 6, 7);
                if (in_array($check_Date, $week1)) {
                    $weekno = 1;
                }
                $week2 = array(8, 9, 10, 11, 12, 13, 14);
                if (in_array($check_Date, $week2)) {
                    $weekno = 2;
                }
                $week3 = array(15, 16, 17, 18, 19, 20, 21);
                if (in_array($check_Date, $week3)) {
                    $weekno = 3;
                }
                $week4 = array(22, 23, 24, 25, 26, 27, 28,29, 30, 31);
                if (in_array($check_Date, $week4)) {
                    $weekno = 4;
                }
                $calpersonal_weekNo = $calpersonal_monthno + $weekno;
                while (strlen($calpersonal_weekNo) != 1) {
                    $splitpersonalWeekno = str_split($calpersonal_weekNo);
                    $splitpersonalWeekno_sum = array_sum($splitpersonalWeekno);
                    $calpersonal_weekNo = $splitpersonalWeekno_sum;
                }

                if($currentdayfav_star > 0){
                    $personalWeekNo_desc = Lifecoach_description::where('type', 2)->where('star_type', '=', 1)->where('star_number', '=', 4)->where('number', $calpersonal_weekNo)->first();
                }elseif($currentdayunfav_star > 0){
                    $personalWeekNo_desc = Lifecoach_description::where('type', 2)->where('star_type', '=', 2)->where('star_number', '=', 4)->where('number', $calpersonal_weekNo)->first();
                }else{
                    $personalWeekNo_desc = Lifecoach_description::where('type', 2)->where('star_type', '=', 3)->where('star_number', '=', 4)->where('number', $calpersonal_weekNo)->first();
                }
                
                $personalWeek_desc = $personalWeekNo_desc->description;

                //personal day
                $cal_daynumber = $explodecurrentDate[2];
                $personal_dayno = $calpersonal_monthno + $cal_daynumber;
                $masterno = '';
                $masterno_description = '';
                while (strlen($personal_dayno) != 1) {
                    if ($personal_dayno == 11 || $personal_dayno == 22) {
                        $masterno = $personal_dayno;
                    }
                    $splitpersonal_dayno = str_split($personal_dayno);
                    $splitpersonal_daynosum = array_sum($splitpersonal_dayno);
                    $personal_dayno = $splitpersonal_daynosum;
                }
                $personalDayNo_desc = Personal_parameter::where('type', 4)->where('number', $personal_dayno)->first();
                $personalDay_desc = $personalDayNo_desc->description;

                if ($masterno != '') {
                    if ($masterno == 11) {

                        $master_no_description = Personal_parameter::where('type', 4)
                            ->where('number', 11)->first();
                        $masterno_description = $master_no_description->description;
                    }
                    if ($masterno == 22) {
                        $master_no_description = Personal_parameter::where('type', 4)
                            ->where('number', 22)->first();
                        $masterno_description = $master_no_description->description;
                    }
                }

                //zodiac Sign	
                $formetdob = date("d-F-Y", strtotime($user->dob));
                $zodiacdate = explode('-', $formetdob);
                $dobday = $zodiacdate[0];
                $dobmonth = $zodiacdate[1];
                $zodiac = Zodic_sign::where('title', 'LIKE', '%' . $dobmonth . '%')
                    ->get();

                if ($dobmonth == "March") {
                    $titledaydate = $zodiac[1]->title;
                    $explodetitle_daydate = explode(' ', $titledaydate);
                    $title_daydate = $explodetitle_daydate[2];

                    if ($dobday <= $title_daydate) {
                        $zodiacdata = $zodiac[1];
                    } else {
                        $zodiacdata = $zodiac[0];
                    }
                } else {
                    $titledaydate = $zodiac[0]->title;
                    $explodetitle_daydate = explode(' ', $titledaydate);
                    $title_daydate = $explodetitle_daydate[2];

                    if ($dobday <= $title_daydate) {
                        $zodiacdata = $zodiac[0];
                    } else {
                        $zodiacdata = $zodiac[1];
                    }
                }

                $user_zodiacsign = strtolower($zodiacdata->zodic_sign);

                if ($currentdayfav_star != 0) {
                    $lifecoachtype = Dailycoach_type::where('type', $personal_dayno)->first();
                    $lifecoach_description = Lifecoach_description::where('type', '=', 1)->where('star_type', '=', 1)->where('star_number', '=', $currentdayfav_star)->where('number', '=', $personal_dayno)->first();
                    $lifecoach = strip_tags($lifecoach_description->description);
                    $short_lifecoach_desc = str_replace(' for', '',$lifecoach_description->short_description);
                } elseif ($currentdayunfav_star != 0) {
                    $lifecoachtype = Dailycoach_type::where('type', $personal_dayno)->first();
                    $lifecoach_description = Lifecoach_description::where('type', '=', 1)->where('star_type', '=', 2)->where('star_number', '=', $currentdayunfav_star)->where('number', '=', $personal_dayno)->first();
                    $lifecoach = strip_tags($lifecoach_description->description);
                    $short_lifecoach_desc = str_replace(' for', '',$lifecoach_description->short_description);
                } else {
                    $lifecoachtype = Dailycoach_type::where('type', $personal_dayno)->first();
                    $lifecoach_description = Lifecoach_description::where('type', '=', 1)->where('star_type', '=', 3)->where('star_number', '=', 0)->where('number', '=', $personal_dayno)->first();
                    $lifecoach = strip_tags($lifecoach_description->description);
                    $short_lifecoach_desc = str_replace(' for', '',$lifecoach_description->short_description);
                }

                //Relationship Number, Health numbers, Career Number and Travel number
                $relationshipno_array = array("2", "6", "8");
                $healthno_array = array("4", "6", "9");
                $careerno_array = array("1", "3", "5", "8");
                $travelno_array = array("1", "5", "7", "9");


                $relationship_percentage = '';
                $relationship_description = "";

                $health_percentage = '';
                $health_description = "";

                $career_percentage = '';
                $career_description = "";

                $travel_percentage = '';
                $travel_description = "";

                if ($currentdayfav_star == 1) {

                    if (in_array($personal_dayno, $relationshipno_array)) {
                        $relationship_percentage = 64;
                        $relationship_description = "Your Personal day number is " . $personal_dayno . ". Today is your One green star day for realtionship.";
                    }
                    if (in_array($personal_dayno, $healthno_array)) {
                        $health_percentage = 64;
                        $health_description = "Your Personal day number is " . $personal_dayno . ". Today is your One green star day for Health.";
                    }
                    if (in_array($personal_dayno, $careerno_array)) {
                        $career_percentage = 64;
                        $career_description = "Your Personal day number is " . $personal_dayno . ". Today is your One green star day for Career.";
                    }
                    if (in_array($personal_dayno, $travelno_array)) {
                        $travel_percentage = 64;
                        $travel_description = "Your Personal day number is " . $personal_dayno . ". Today is your One green star day for Travel.";
                    }
                } elseif ($currentdayfav_star == 2) {

                    if (in_array($personal_dayno, $relationshipno_array)) {
                        $relationship_percentage = 81;
                        $relationship_description = "Your Personal day number is " . $personal_dayno . ". Today is your Two green star day for realtionship.";
                    }
                    if (in_array($personal_dayno, $healthno_array)) {
                        $health_percentage = 81;
                        $health_description = "Your Personal day number is " . $personal_dayno . ". Today is your Two green star day for Health.";
                    }
                    if (in_array($personal_dayno, $careerno_array)) {
                        $career_percentage = 81;
                        $career_description = "Your Personal day number is " . $personal_dayno . ". Today is your Two green star day for Career.";
                    }
                    if (in_array($personal_dayno, $travelno_array)) {
                        $travel_percentage = 81;
                        $travel_description = "Your Personal day number is " . $personal_dayno . ". Today is your Two green star day for Travel.";
                    }
                } elseif ($currentdayfav_star == 3) {

                    if (in_array($personal_dayno, $relationshipno_array)) {
                        $relationship_percentage = 98;
                        $relationship_description = "Your Personal day number is " . $personal_dayno . ". Today is your Three green star day for realtionship.";
                    }
                    if (in_array($personal_dayno, $healthno_array)) {
                        $health_percentage = 98;
                        $health_description = "Your Personal day number is " . $personal_dayno . ". Today is your Three green star day for Health.";
                    }
                    if (in_array($personal_dayno, $careerno_array)) {
                        $career_percentage = 98;
                        $career_description = "Your Personal day number is " . $personal_dayno . ". Today is your Three green star day for Career.";
                    }
                    if (in_array($personal_dayno, $travelno_array)) {
                        $travel_percentage = 98;
                        $travel_description = "Your Personal day number is " . $personal_dayno . ". Today is your Three green star day for Travel.";
                    }
                } elseif ($currentdayunfav_star == 1) {

                    if (in_array($personal_dayno, $relationshipno_array)) {
                        $relationship_percentage = 35;
                        $relationship_description = "Your Personal day number is " . $personal_dayno . ". Today is your One red star day for realtionship.";
                    }
                    if (in_array($personal_dayno, $healthno_array)) {
                        $health_percentage = 35;
                        $health_description = "Your Personal day number is " . $personal_dayno . ". Today is your One red star day for Health.";
                    }
                    if (in_array($personal_dayno, $careerno_array)) {
                        $career_percentage = 35;
                        $career_description = "Your Personal day number is " . $personal_dayno . ". Today is your One red star day for Career.";
                    }
                    if (in_array($personal_dayno, $travelno_array)) {
                        $travel_percentage = 35;
                        $travel_description = "Your Personal day number is " . $personal_dayno . ". Today is your One red star day for Travel.";
                    }
                } elseif ($currentdayunfav_star == 2) {

                    if (in_array($personal_dayno, $relationshipno_array)) {
                        $relationship_percentage = 21;
                        $relationship_description = "Your Personal day number is " . $personal_dayno . ". Today is your Two red star day for realtionship.";
                    }
                    if (in_array($personal_dayno, $healthno_array)) {
                        $health_percentage = 21;
                        $health_description = "Your Personal day number is " . $personal_dayno . ". Today is your Two red star day for Health.";
                    }
                    if (in_array($personal_dayno, $careerno_array)) {
                        $career_percentage = 21;
                        $career_description = "Your Personal day number is " . $personal_dayno . ". Today is your Two red star day for Career.";
                    }
                    if (in_array($personal_dayno, $travelno_array)) {
                        $travel_percentage = 21;
                        $travel_description = "Your Personal day number is " . $personal_dayno . ". Today is your Two red star day for Travel.";
                    }
                } elseif ($currentdayunfav_star == 3) {

                    if (in_array($personal_dayno, $relationshipno_array)) {
                        $relationship_percentage = 7;
                        $relationship_description = "Your Personal day number is " . $personal_dayno . ". Today is your Three red star day for realtionship.";
                    }
                    if (in_array($personal_dayno, $healthno_array)) {
                        $health_percentage = 7;
                        $health_description = "Your Personal day number is " . $personal_dayno . ". Today is your Three red star day for Health.";
                    }
                    if (in_array($personal_dayno, $careerno_array)) {
                        $career_percentage = 7;
                        $career_description = "Your Personal day number is " . $personal_dayno . ". Today is your Three red star day for Career.";
                    }
                    if (in_array($personal_dayno, $travelno_array)) {
                        $travel_percentage = 7;
                        $travel_description = "Your Personal day number is " . $personal_dayno . ". Today is your Three red star day for Travel.";
                    }
                } else {

                    if (in_array($personal_dayno, $relationshipno_array)) {
                        $relationship_percentage = 50;
                        $relationship_description = "Your Personal day number is " . $personal_dayno . ". Today is your neutral day for realtionship.";
                    }
                    if (in_array($personal_dayno, $healthno_array)) {
                        $health_percentage = 50;
                        $health_description = "Your Personal day number is " . $personal_dayno . ". Today is your neutral day for Health.";
                    }
                    if (in_array($personal_dayno, $careerno_array)) {
                        $career_percentage = 50;
                        $career_description = "Your Personal day number is " . $personal_dayno . ". Today is your neutral day for Career.";
                    }
                    if (in_array($personal_dayno, $travelno_array)) {
                        $travel_percentage = 50;
                        $travel_description = "Your Personal day number is " . $personal_dayno . ". Today is your neutral day for Travel.";
                    }
                }

                // if ($masterno_description != '') {
                //     $lifecoach = $masterno_description;
                //     $personal_dayno = $masterno;
                // }


                return response()->json([
                    'status' => 1,
                    'message' => 'success',
                    'userid' => $userid,
                    'subscription_status' => $user->subscription_status,
                    'user_zodiacsign' => $user_zodiacsign,
                    'short_lifecoach_desc' => $short_lifecoach_desc,
                    'life_coach' => $lifecoach,
                    'personal_year' => $personalyear,
                    'personal_month' => $calpersonal_monthno,
                    'personal_week' => $calpersonal_weekNo,
                    'personal_day' => $personal_dayno,
                    'personal_year_desc' => $personalYear_desc,
                    'personal_month_desc' => $personalMonth_desc,
                    'personal_week_desc' => $personalWeek_desc,
                    'personal_day_desc' => $personalDay_desc,
                    'relationship_percentage' => $relationship_percentage,
                    'relationship_description' => $relationship_description,
                    'health_percentage' => $health_percentage,
                    'health_description' => $health_description,
                    'career_percentage' => $career_percentage,
                    'career_description' => $career_description,
                    'travel_percentage' => $travel_percentage,
                    'travel_description' => $travel_description
                ]);
            }
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'User not found',
            ]);
        }
    }

    //end here

    //inserted at 28-11-2022
    public function resendotpverify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required',
            'signupby' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $otp = substr(time(), -4);
        $userid = $request->userid;
        $signupby = $request->signupby;
        if ($signupby == 1) {
            $users = User::find($userid);
            $username = $users->email;
            if ($users) {

                $subject = 'ASTAR8: Verification Code';
                $from = "notification@designersx.us";
                $msg = "Verification Code is <b>" . $otp . "</b>.";
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.smtp2go.com/v3/email/send",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30000,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => "{\r\n    \"api_key\": \"api-22898DD4CFC911EDB2B6F23C91C88F4E\",\r\n    \"to\": [\"$username>\"],\r\n    \"sender\": \"$from\",\r\n    \"subject\": \"$subject\",\r\n    \"text_body\": \"$msg\",\r\n    \"html_body\": \"$msg\",\r\n    \"custom_headers\": [\r\n      {\r\n        \"header\": \"Reply-To\",\r\n        \"value\": \"$from>\"\r\n      }\r\n    ]\r\n}",
                    CURLOPT_HTTPHEADER => array(
                        // Set here Laravel Post required headers
                        "cache-control: no-cache",
                        "content-type: application/json"
                    ),
                ));

                $results = curl_exec($curl);
                $resErrors = curl_error($curl);

                curl_close($curl);

                $userstep = new Useronboarding();
                $userstep->user_id = $users->id;
                $userstep->step_1 = 1;
                $userstep->save();
                return response()->json([
                    'status' => 1, 'message' => 'Verification code has been successfully sent to your registered email.', 'user_id' => $users->id,
                    'subscription_status' => $users->subscription_status, 'Fullname' => $users->name, 'username' => $username, 'otp' => $otp, 'onboardingstatus' => 1, 'nextboardingstep' => 'Step-2'
                ]);
            } else {
                return response()->json([
                    'status' => 1,
                    'message' => 'Something went wrong. Please try with another username.',
                ]);
            }
        }
    }

    public function resentverifyloginotp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $userid = $request->userid;
        $otp = substr(time(), -4);
        $check_users = User::find($userid);
        if ($check_users) {
            if ($check_users->signupby == 1) {

                $username = $check_users->email;

                $update_otp = User::where(['id' => $check_users->id])->update(['otp' => $otp]);
                $subject = 'ASTAR8: Verification Code';
                $from = "notification@designersx.us";
                $msg = "Verification Code is <b>" . $otp . "</b>.";
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.smtp2go.com/v3/email/send",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30000,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => "{\r\n    \"api_key\": \"api-22898DD4CFC911EDB2B6F23C91C88F4E\",\r\n    \"to\": [\"$username>\"],\r\n    \"sender\": \"$from\",\r\n    \"subject\": \"$subject\",\r\n    \"text_body\": \"$msg\",\r\n    \"html_body\": \"$msg\",\r\n    \"custom_headers\": [\r\n      {\r\n        \"header\": \"Reply-To\",\r\n        \"value\": \"$from>\"\r\n      }\r\n    ]\r\n}",
                    CURLOPT_HTTPHEADER => array(
                        // Set here Laravel Post required headers
                        "cache-control: no-cache",
                        "content-type: application/json"
                    ),
                ));

                $results = curl_exec($curl);
                $resErrors = curl_error($curl);

                curl_close($curl);


                return response()->json(['status' => 1, 'message' => 'Verification code has been successfully sent to your registered email.', 'user_id' => $check_users->id, 'subscription_status' => $check_users->subscription_status, 'Fullname' => $check_users->name, 'otp' => $otp, 'onboardingstatus' => 1, 'username' => $username, 'nextboardingstep' => 'Step-2']);
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'Invalid Username.',
                ]);
            }
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'The Email id is not exist. So please login with registered email id or signup.',
            ]);
        }
    }

    public function editprofile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required',
            'date_current' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $userid = $request->userid;
        $loginuser_name = $request->name;
        $loginuserdob = $request->dob;
        $loginuser_gender = $request->gender;
        $loginuser_occupation = $request->occupation;
        $date_current = $request->date_current;
        $email = $request->email;
        $phoneno = $request->phoneno;
        $userexist = User::find($userid);
        $check_email = User::where('email', $email)->get();
        $check_phoneno = User::where('phoneno', $phoneno)->get();
        if ($userexist) {
            if ($file = $request->file('profile_pic')) {
                $destinationPath = public_path() . '/profile_pic';
                $safeName = \Str::random(12) . time() . '.' . $file->getClientOriginalExtension();
                $file->move($destinationPath, $safeName);
                $new_profilepic_name = $safeName;

                $userdetail = User::find($userid);
                $userdetail->name = $loginuser_name;
                $userdetail->dob = $loginuserdob;
                $userdetail->gender = $loginuser_gender;
                $userdetail->occupation = $loginuser_occupation;
                $userdetail->profile_pic = $new_profilepic_name;
                    if($userexist->email == null){
                if(count($check_email) == 0){
                        $userdetail->email = $email;
                    }else{
                        return response()->json([
                            'status' => 0,
                            'message' => 'The Email id is exist. So please try another Email id.',
                        ]);
                    }
                }
                if($userexist->phoneno == null){
                        if(count($check_phoneno) == 0){
                        $userdetail->phoneno = $phoneno;
                    }else{
                        return response()->json([
                            'status' => 0,
                            'message' => 'The Phone Number is exist. So please try another Phone Number.',
                        ]);
                    }
                }
                $userdetail->save();
            } else {
                $userdetail = User::find($userid);
                $userdetail->name = $loginuser_name;
                $userdetail->dob = $loginuserdob;
                $userdetail->gender = $loginuser_gender;
                $userdetail->occupation = $loginuser_occupation;
                if($userexist->email == null){
                        if(count($check_email) == 0){
                        $userdetail->email = $email;
                    }else{
                        return response()->json([
                            'status' => 0,
                            'message' => 'The Email id is exist. So please try another Email id.',
                        ]);
                    }
                }
                
                if($userexist->phoneno == null){
                        if(count($check_phoneno) == 0){
                        $userdetail->phoneno = $phoneno;
                    }else{
                        return response()->json([
                            'status' => 0,
                            'message' => 'The Phone Number is exist. So please try another Phone Number.',
                        ]);
                    }
                }
                $userdetail->save();
            }

        $user = User::find($userid);
        $gender = $user->gender;
        $occupation = $user->occupation;
        $today = $date_current;
        $useragediff = date_diff(date_create($user->dob), date_create($today));
        $userage = $useragediff->format('%y');

        // DOB reading 
        $dob = $user->dob;
        $date = explode('-', $dob);
        $day = $date[2];
        $month = $date[1];
        $year = $date[0];
        $dayno = str_split($day, 1);
        $dayno = array_sum($dayno);
        $dayno = intval($dayno);
        while (strlen($dayno) != 1) {
            $dayno = str_split($dayno);
            $dayno = array_sum($dayno);
        }
        $dobdesc = Module_description::where('moduletype_id', 2)
            ->where('number', $dayno)
            ->value('description');
        $dobdesc = strip_tags($dobdesc);

        $birthdayreading = array("module_type" => 2, "module_name" => "DOB Reading", "number" => $dayno, "description" => $dobdesc);

        //elemental number
        $elementaldesc = Module_description::where('moduletype_id', 4)
            ->where('number', $dayno)
            ->value('description');
        $elementaldesc = strip_tags($elementaldesc);

        $elementalno = array("module_type" => 4, "module_name" => "Elemental Number", "number" => $dayno, "description" => $elementaldesc);

        //basic health reading
        $basichealthdesc = Module_description::where('moduletype_id', 5)
            ->where('number', $dayno)
            ->value('description');
        $basichealthdesc = strip_tags($basichealthdesc);

        $basichealthreading = array("module_type" => 5, "module_name" => "Basic Health Reading", "number" => $dayno, "description" => $basichealthdesc);

        //health precaution
        $precautiondesc = Module_description::where('moduletype_id', 6)
            ->where('number', $dayno)
            ->value('description');
        $precautiondesc = strip_tags($precautiondesc);

        $healthprecaution = array("module_type" => 6, "module_name" => "Health Precaution", "number" => $dayno, "description" => $precautiondesc);

        //basic Parenting
        $basicparentingdesc = Module_description::where('moduletype_id', 12)
            ->where('number', $dayno)
            ->value('description');
        $basicparentingdesc = strip_tags($basicparentingdesc);

        $basicparenting = array(
            "module_type" => 12, "module_name" => "Basic Parent Reading",
            "number" => $dayno, "description" => $basicparentingdesc
        );

        //detail Parenting  
        $detailparentingdesc = Module_description::where('moduletype_id', 13)
            ->where('number', $dayno)
            ->value('description');
        $detailparentingdesc = strip_tags($detailparentingdesc);

        $detailparenting = array(
            "module_type" => 13, "module_name" => "Detailed Parent Reading",
            "number" => $dayno, "description" => $detailparentingdesc
        );

        //basic money 
        $basicmoneydesc = Module_description::where('moduletype_id', 14)
            ->where('number', $dayno)
            ->value('description');
        $basicmoneydesc = strip_tags($basicmoneydesc);

        $basicmoneymatter = array(
            "module_type" => 14, "module_name" => "Basic Money Matters",
            "number" => $dayno, "description" => $basicmoneydesc
        );

        //detail money 
        $detailmoneydesc = Module_description::where('moduletype_id', 15)
            ->where('number', $dayno)
            ->value('description');
        $detailmoneydesc = strip_tags($detailmoneydesc);

        $detailmoneymatter = array(
            "module_type" => 15, "module_name" => "Detailed Money Matters",
            "number" => $dayno, "description" => $detailmoneydesc
        );

        //destiny number
        $monthno = str_split($month, 1);
        $monthno = array_sum($monthno);
        while (strlen($monthno) != 1) {
            $monthno = str_split($monthno);
            $monthno = array_sum($monthno);
        }
        $yearno = str_split($year, 1);
        $yearno = array_sum($yearno);
        while (strlen($yearno) != 1) {
            $yearno = str_split($yearno);
            $yearno = array_sum($yearno);
        }
        $yearno = intval($yearno);
        $destiny_no = $dayno + $monthno + $yearno;
        while (strlen($destiny_no) != 1) {
            $destiny_no = str_split($destiny_no);
            $destiny_no = array_sum($destiny_no);
        }
        $destinynodesc = Module_description::where('moduletype_id', 16)
            ->where('number', $destiny_no)
            ->value('description');
        $destinynodesc = strip_tags($destinynodesc);
        $explode_destinynodesc = explode('||', $destinynodesc);
        $learn_desc = $explode_destinynodesc[0];
        $notlearn_desc = $explode_destinynodesc[1];
        $destinynumber = array(
            "module_type" => 16, "module_name" => "Destiny Number", "number" => $destiny_no, "description" => $destinynodesc,
            "learn_title" => "Learn To Be", "learn_desc" => $learn_desc, "notlearn_title" => "Learn Not To Be", "notlearn_desc" => $notlearn_desc
        );

        $dobreadingdetail = [
            $birthdayreading, $elementalno, $basichealthreading, $healthprecaution, $basicmoneymatter, $detailmoneymatter,
            $basicparenting, $detailparenting, $destinynumber
        ];

        //primary number
        $primarynodesc = Primaryno_type::where('number', $dayno)
            ->first();
        $primarynumber = array(
            "module_name" => "Primary Number", "number" => $dayno, "description" => $primarynodesc->description, "positive" => $primarynodesc->positive, "negative" => $primarynodesc->negative,
            "occupations" => $primarynodesc->occupations, "health" => $primarynodesc->health, "partners" => $primarynodesc->partners, "times_of_the_year" => $primarynodesc->times_of_the_year,
            "countries" => $primarynodesc->countries, "tibbits" => $primarynodesc->tibbits, "destiny_colors" => $primarynodesc->destiny_colors, "destiny_timing" => $primarynodesc->destiny_timing
        );

        //compatible partner
        $compatiblepartner = Compatible_partner::where('number', $dayno)
            ->first();

        $compatible_partner = array(
            "module_name" => "Compatible Partner", "number" => $dayno,
            "description" => strip_tags($compatiblepartner->description),
            "more_compatible_months" => $compatiblepartner->more_compatible_months,
            "more_compatible_dates" => $compatiblepartner->more_compatible_dates,
            "less_compatible_months" => $compatiblepartner->less_compatible_months,
            "less_compatible_dates" => $compatiblepartner->less_compatible_dates
        );

        //luckiest parameters
        $luckyparameterdesc = Luckiest_parameter::select('lucky_colours', 'lucky_gems', 'lucky_metals')
            ->where('number', $dayno)
            ->first();

        $luckyparameters = array(
            "module_name" => "Lucky Parameters", "number" => $dayno,
            "lucky_colours" => $luckyparameterdesc->lucky_colours,
            "lucky_gems" => $luckyparameterdesc->lucky_gems,
            "lucky_metals" => $luckyparameterdesc->lucky_metals
        );

        //planet number
        $planet = Planet_number::select('name', 'ruling_number', 'description')
            ->where('ruling_number', $dayno)
            ->first();

        $planetnumber = array(
            "module_name" => "Planet Number", "ruling_number" => $dayno,
            "planet_name" => $planet->name,
            "description" => $planet->description
        );

        //zodiac sign
        $formetdob = date("d-F-Y", strtotime($dob));
        $zodiacdate = explode('-', $formetdob);
        $dobday = $zodiacdate[0];
        $dobmonth = $zodiacdate[1];
        $zodiac = Zodic_sign::where('title', 'LIKE', '%' . $dobmonth . '%')
            ->get();

        if ($dobmonth == "March") {
            $titledaydate = $zodiac[1]->title;
            $explodetitle_daydate = explode(' ', $titledaydate);
            $title_daydate = $explodetitle_daydate[2];

            if ($dobday <= $title_daydate) {
                $zodiacdata = $zodiac[1];
            } else {
                $zodiacdata = $zodiac[0];
            }
        } else {
            $titledaydate = $zodiac[0]->title;
            $explodetitle_daydate = explode(' ', $titledaydate);
            $title_daydate = $explodetitle_daydate[2];

            if ($dobday <= $title_daydate) {
                $zodiacdata = $zodiac[0];
            } else {
                $zodiacdata = $zodiac[1];
            }
        }

        $zodiacsign = array(
            "module_name" => "Zodiac Sign", "zodiac_sign" => $zodiacdata->zodic_sign,
            "zodiac_number" => $zodiacdata->zodic_number,
            "zodiac_day" => $zodiacdata->zodic_day
        );

        //life cycle
        $monthdescription = Life_cycle::where('number', $dayno)->value('cycle_by_month');
        $daydescription = Life_cycle::where('number', $monthno)->value('cycle_by_date');
        $yeardescription = Life_cycle::where('number', $yearno)->value('cycle_by_year');

        $lifecycle = array(
            "module_name" => "Life Cycle", "cycleone_number" => $monthno,
            "cycleone_description" => $monthdescription,
            "cycletwo_number" => $dayno,
            "cycletwo_description" => $daydescription,
            "cyclethree_number" => $yearno,
            "cyclethree_description" => $yeardescription
        );

        //Name reading
        $name = $user->name;
        $finalname = str_replace(' ', '', $name);
        $strname = strtoupper($finalname);
        $splitname = str_split($strname, 1);
        foreach ($splitname as $letter) {
            $pytha_number[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $letter . '%')
                ->where('systemtype_id', 1)
                ->value('number');
            $chald_number[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $letter . '%')
                ->where('systemtype_id', 2)
                ->value('number');
        }
        $pytha_no_sum = array_sum($pytha_number);
        $chald_no_sum = array_sum($chald_number);

        while (strlen($pytha_no_sum) != 1 || strlen($chald_no_sum) != 1) {
            $pytha_no_sum = str_split($pytha_no_sum, 1);
            $pytha_no_sum = array_sum($pytha_no_sum);
            $chald_no_sum = str_split($chald_no_sum, 1);
            $chald_no_sum = array_sum($chald_no_sum);
        }
        $pytha_description = Module_description::where('moduletype_id', 1)
            ->where('number', $pytha_no_sum)
            ->value('description');
        $pytha_description = strip_tags($pytha_description);
        $chald_description = Module_description::where('moduletype_id', 1)
            ->where('number', $chald_no_sum)
            ->value('description');
        $chalddescription = strip_tags($chald_description);
        $explodenamereading_desc = explode('||', $chalddescription);
        $positive_desc = $explodenamereading_desc[0];
        $negative_desc = $explodenamereading_desc[1];
        $namereadingdetail = array(
            "module_name" => "Name Reading", "Pytha_number" => $pytha_no_sum, "Pytha_description" => $pytha_description, "Chald_number" => $chald_no_sum, "Chald_description" => $chald_description,
            "Chald_positive_title" => "Positive", "Chald_positive_desc" => $positive_desc, "Chald_negative_title" => "Negative", "Chald_negative_desc" => $negative_desc
        );

        /*   if($chald_no_sum != 9)
            {
                $namereadingdetail = array("module_name"=>"Name Reading", "Pytha_number"=> $pytha_no_sum, "Pytha_description"=> $pytha_description,
                "Chald_number"=> $chald_no_sum, "Chald_description"=> $chald_description,);
            }else
            {
                $namereadingdetail = array("module_name"=>"Name Reading", "Pytha_number"=> $pytha_no_sum, "Pytha_description"=> $pytha_description);
            } */

        //magic box
        $pytha_number_count = array_count_values($pytha_number);
        $chald_number_count = array_count_values($chald_number);

        if (array_key_exists(1, $chald_number_count)) {
            $magicboxnumber1 = $chald_number_count['1'];
        } else {
            $magicboxnumber1 = 0;
        }
        $magicboxnumber1_decs = Module_description::where('moduletype_id', 3)
            ->where('number', 1)
            ->first();

        $magicboxnumber1decs = explode('||', strip_tags($magicboxnumber1_decs->description));
        $box1decs = $magicboxnumber1decs[0];
        $box1manydecs = $magicboxnumber1decs[1];
        $box1fewdecs = $magicboxnumber1decs[2];
        if ($magicboxnumber1_decs->mogicbox_average <= $magicboxnumber1) {
            $title = 'Many 1s';
            $description = $box1manydecs;
        } else {
            $title = 'Few/No 1s';
            $description = $box1fewdecs;
        }
        $magicboxnumberdecs1 = array('Box' => $box1decs, 'title' => $title, 'description' => $description, 'average_desc' => "Three 1s are average.");

        $boxcellno1 = array('number_heading' => 'number_heading', 'number' => 1, 'numbervalue' => $magicboxnumber1, 'numberdescription' => $magicboxnumberdecs1);

        if (array_key_exists(2, $chald_number_count)) {
            $magicboxnumber2 = $chald_number_count['2'];
        } else {
            $magicboxnumber2 = 0;
        }
        $magicboxnumber2_decs = Module_description::where('moduletype_id', 3)
            ->where('number', 2)
            ->first();

        $magicboxnumber2decs = explode('||', strip_tags($magicboxnumber2_decs->description));
        $box2decs = $magicboxnumber2decs[0];
        $box2manydecs = $magicboxnumber2decs[1];
        $box2fewdecs = $magicboxnumber2decs[2];
        if ($magicboxnumber2_decs->mogicbox_average <= $magicboxnumber2) {
            $title = 'Many 2s';
            $description = $box2manydecs;
        } else {
            $title = 'Few/No 2s';
            $description = $box2fewdecs;
        }
        $magicboxnumberdecs2 = array('Box' => $box2decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 2 is average.");

        $boxcellno2 = array('number_heading' => 'number_heading', 'number' => 2, 'numbervalue' => $magicboxnumber2, 'numberdescription' => $magicboxnumberdecs2);

        if (array_key_exists(3, $chald_number_count)) {
            $magicboxnumber3 = $chald_number_count['3'];
        } else {
            $magicboxnumber3 = 0;
        }
        $magicboxnumber3_decs = Module_description::where('moduletype_id', 3)
            ->where('number', 3)
            ->first();

        $magicboxnumber3decs = explode('||', strip_tags($magicboxnumber3_decs->description));
        $box3decs = $magicboxnumber3decs[0];
        $box3manydecs = $magicboxnumber3decs[1];
        $box3fewdecs = $magicboxnumber3decs[2];
        if ($magicboxnumber3_decs->mogicbox_average <= $magicboxnumber3) {
            $title = 'Many 3s';
            $description = $box3manydecs;
        } else {
            $title = 'Few/No 3s';
            $description = $box3fewdecs;
        }
        $magicboxnumberdecs3 = array('Box' => $box3decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 3 is average.");

        $boxcellno3 = array('number_heading' => 'number_heading', 'number' => 3, 'numbervalue' => $magicboxnumber3, 'numberdescription' => $magicboxnumberdecs3);

        if (array_key_exists(4, $chald_number_count)) {
            $magicboxnumber4 = $chald_number_count['4'];
        } else {
            $magicboxnumber4 = 0;
        }
        $magicboxnumber4_decs = Module_description::where('moduletype_id', 3)
            ->where('number', 4)
            ->first();

        $magicboxnumber4decs = explode('||', strip_tags($magicboxnumber4_decs->description));
        $box4decs = $magicboxnumber4decs[0];
        $box4manydecs = $magicboxnumber4decs[1];
        $box4fewdecs = $magicboxnumber4decs[2];
        if ($magicboxnumber4_decs->mogicbox_average <= $magicboxnumber4) {
            $title = 'Many 4s';
            $description = $box4manydecs;
        } else {
            $title = 'Few/No 4s';
            $description = $box4fewdecs;
        }

        $magicboxnumberdecs4 = array('Box' => $box4decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 4 is average.");

        $boxcellno4 = array('number_heading' => 'number_heading', 'number' => 4, 'numbervalue' => $magicboxnumber4, 'numberdescription' => $magicboxnumberdecs4);

        if (array_key_exists(5, $chald_number_count)) {
            $magicboxnumber5 = $chald_number_count['5'];
        } else {
            $magicboxnumber5 = 0;
        }
        $magicboxnumber5_decs = Module_description::where('moduletype_id', 3)
            ->where('number', 5)
            ->first();

        $magicboxnumber5decs = explode('||', strip_tags($magicboxnumber5_decs->description));
        $box5decs = $magicboxnumber5decs[0];
        $box5manydecs = $magicboxnumber5decs[1];
        $box5fewdecs = $magicboxnumber5decs[2];
        if ($magicboxnumber5_decs->mogicbox_average <= $magicboxnumber5) {
            $title = 'Many 5s';
            $description = $box5manydecs;
        } else {
            $title = 'Few/No 5s';
            $description = $box5fewdecs;
        }

        $magicboxnumberdecs5 = array('Box' => $box5decs, 'title' => $title, 'description' => $description, 'average_desc' => "Three 5s are average.");

        $boxcellno5 = array('number_heading' => 'number_heading', 'number' => 5, 'numbervalue' => $magicboxnumber5, 'numberdescription' => $magicboxnumberdecs5);


        if (array_key_exists(6, $chald_number_count)) {
            $magicboxnumber6 = $chald_number_count['6'];
        } else {
            $magicboxnumber6 = 0;
        }
        $magicboxnumber6_decs = Module_description::where('moduletype_id', 3)
            ->where('number', 6)
            ->first();

        $magicboxnumber6decs = explode('||', strip_tags($magicboxnumber6_decs->description));
        $box6decs = $magicboxnumber6decs[0];
        $box6manydecs = $magicboxnumber6decs[1];
        $box6fewdecs = $magicboxnumber6decs[2];
        if ($magicboxnumber6_decs->mogicbox_average <= $magicboxnumber6) {
            $title = 'Many 6s';
            $description = $box6manydecs;
        } else {
            $title = 'Few/No 6s';
            $description = $box6fewdecs;
        }

        $magicboxnumberdecs6 = array('Box' => $box6decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 6 is average.");

        $boxcellno6 = array('number_heading' => 'number_heading', 'number' => 6, 'numbervalue' => $magicboxnumber6, 'numberdescription' => $magicboxnumberdecs6);

        if (array_key_exists(7, $chald_number_count)) {
            $magicboxnumber7 = $chald_number_count['7'];
        } else {
            $magicboxnumber7 = 0;
        }

        $magicboxnumber7_decs = Module_description::where('moduletype_id', 3)
            ->where('number', 7)
            ->first();

        $magicboxnumber7decs = explode('||', strip_tags($magicboxnumber7_decs->description));
        $box7decs = $magicboxnumber7decs[0];
        $box7manydecs = $magicboxnumber7decs[1];
        $box7fewdecs = $magicboxnumber7decs[2];
        if ($magicboxnumber7_decs->mogicbox_average <= $magicboxnumber7) {
            $title = 'Many 7s';
            $description = $box7manydecs;
        } else {
            $title = 'Few/No 7s';
            $description = $box7fewdecs;
        }

        $magicboxnumberdecs7 = array('Box' => $box7decs, 'title' => $title, 'description' => $description, 'average_desc' => "One 7 is average.");

        $boxcellno7 = array('number_heading' => 'number_heading', 'number' => 7, 'numbervalue' => $magicboxnumber7, 'numberdescription' => $magicboxnumberdecs7);

        if (array_key_exists(8, $chald_number_count)) {
            $magicboxnumber8 = $chald_number_count['8'];
        } else {
            $magicboxnumber8 = 0;
        }
        $magicboxnumber8_decs = Module_description::where('moduletype_id', 3)
            ->where('number', 8)
            ->first();

        $magicboxnumber8decs = explode('||', strip_tags($magicboxnumber8_decs->description));
        $box8decs = $magicboxnumber8decs[0];
        $box8manydecs = $magicboxnumber8decs[1];
        $box8fewdecs = $magicboxnumber8decs[2];
        if ($magicboxnumber8_decs->mogicbox_average <= $magicboxnumber8) {
            $title = 'Many 8s';
            $description = $box8manydecs;
        } else {
            $title = 'Few/No 8s';
            $description = $box8fewdecs;
        }

        $magicboxnumberdecs8 = array('Box' => $box8decs, 'title' => $title, 'description' => $description, 'average_desc' => "Three 8s are average.");

        $boxcellno8 = array('number_heading' => 'number_heading', 'number' => 8, 'numbervalue' => $magicboxnumber8, 'numberdescription' => $magicboxnumberdecs8);

        if (array_key_exists(9, $chald_number_count)) {
            $magicboxnumber9 = $chald_number_count['9'];
        } else {
            $magicboxnumber9 = 0;
        }
        $magicboxnumber9_decs = Module_description::where('moduletype_id', 3)
            ->where('number', 9)
            ->first();
        $magicboxnumber9decs = explode('||', strip_tags($magicboxnumber9_decs->description));
        $box9decs = $magicboxnumber9decs[0];
        $box9manydecs = $magicboxnumber9decs[1];
        $box9fewdecs = $magicboxnumber9decs[2];
        if ($magicboxnumber9_decs->mogicbox_average <= $magicboxnumber9) {
            $title = 'Many 9s';
            $description = $box9manydecs;
        } else {
            $title = 'Few/No 9s';
            $description = $box9fewdecs;
        }

        $magicboxnumberdecs9 = array('Box' => $box9decs, 'title' => $title, 'description' => $description, 'average_desc' => "Three 9s are average.");
        $boxcellno9 = array('number_heading' => 'number_heading', 'number' => 9, 'numbervalue' => $magicboxnumber9, 'numberdescription' => $magicboxnumberdecs9);
        
        $magicbox = array($boxcellno1, $boxcellno2, $boxcellno3, $boxcellno4, $boxcellno5, $boxcellno6, $boxcellno7, $boxcellno8, $boxcellno9);

        //fav unfav parameters
        $fav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 1)
            ->where('month_id', $month)
            ->where('date', $day)
            ->first();
        $unfav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 2)
            ->where('month_id', $month)
            ->where('date', $day)
            ->first();

            $fav_number = str_replace(' ', '', $fav->numbers);
            $unfav_number = str_replace(' ', '', $unfav->numbers);
            
            $fav_day = str_replace(',', ', ', $fav->days);
            $fav_month = str_replace(',', ', ', $fav->months);
            $unfav_day = str_replace(',', ', ', $unfav->days);
            $unfav_month = str_replace(',', ', ', $unfav->months);
        
        $favunfavparameters = array("favparameter" => $fav, "unfavparameters" => $unfav, 'fav_numbers_withoutSpace'=>$fav_number, 'unfav_numbers_withoutSpace'=>$unfav_number, 'fav_days_withSpace' => $fav_day,
            'fav_months_withSpace' => $fav_month,'unfav_days_withSpace'=>$unfav_day, 'unfav_months_withSpace'=>$unfav_month);

        //Life changes
        $ages = Life_change::where('numbers', $dayno)->value('ages');
        $start_year = intval($year);
        $year_limit = $start_year + 100;
        $years = array();
        if ($dayno == 1) {
            $year_sequ = array(1, 4);
            for ($i = $start_year; $i <= $year_limit; $i++) {
                $cal_year = str_split($i, 1);
                $sum = array_sum($cal_year);
                while (strlen($sum) != 1) {
                    $sum = str_split($sum);
                    $sum = array_sum($sum);
                }
                $sum = intval($sum);
                if (in_array($sum, $year_sequ)) {
                    array_push($years, $i);
                }
            }
        }
        if ($dayno == 2) {
            $year_sequ = array(2, 7);
            for ($i = $start_year; $i <= $year_limit; $i++) {
                $cal_year = str_split($i, 1);
                $sum = array_sum($cal_year);
                while (strlen($sum) != 1) {
                    $sum = str_split($sum);
                    $sum = array_sum($sum);
                }

                $sum = intval($sum);
                if (in_array($sum, $year_sequ)) {
                    array_push($years, $i);
                }
            }
        }
        if ($dayno == 3) {
            $year_sequ = array(3, 9);
            for ($i = $start_year; $i <= $year_limit; $i++) {
                $cal_year = str_split($i, 1);
                $sum = array_sum($cal_year);
                while (strlen($sum) != 1) {
                    $sum = str_split($sum);
                    $sum = array_sum($sum);
                }
                $sum = intval($sum);
                if (in_array($sum, $year_sequ)) {
                    array_push($years, $i);
                }
            }
        }
        if ($dayno == 4) {
            $year_sequ = array(4, 8, 1);
            for ($i = $start_year; $i <= $year_limit; $i++) {
                $cal_year = str_split($i, 1);
                $sum = array_sum($cal_year);
                while (strlen($sum) != 1) {
                    $sum = str_split($sum);
                    $sum = array_sum($sum);
                }

                $sum = intval($sum);
                if (in_array($sum, $year_sequ)) {
                    array_push($years, $i);
                }
            }
        }
        if ($dayno == 5) {
            $year_sequ = array(5, 6);
            for ($i = $start_year; $i <= $year_limit; $i++) {
                $cal_year = str_split($i, 1);
                $sum = array_sum($cal_year);
                while (strlen($sum) != 1) {
                    $sum = str_split($sum);
                    $sum = array_sum($sum);
                }

                $sum = intval($sum);
                if (in_array($sum, $year_sequ)) {
                    array_push($years, $i);
                }
            }
        }
        if ($dayno == 6) {
            $year_sequ = array(6, 2, 3);
            for ($i = $start_year; $i <= $year_limit; $i++) {
                $cal_year = str_split($i, 1);
                $sum = array_sum($cal_year);
                while (strlen($sum) != 1) {
                    $sum = str_split($sum);
                    $sum = array_sum($sum);
                }

                $sum = intval($sum);
                if (in_array($sum, $year_sequ)) {
                    array_push($years, $i);
                }
            }
        }
        if ($dayno == 7) {
            $year_sequ = array(2, 7);
            for ($i = $start_year; $i <= $year_limit; $i++) {
                $cal_year = str_split($i, 1);
                $sum = array_sum($cal_year);
                while (strlen($sum) != 1) {
                    $sum = str_split($sum);
                    $sum = array_sum($sum);
                }
                $sum = intval($sum);
                if (in_array($sum, $year_sequ)) {
                    array_push($years, $i);
                }
            }
        }
        if ($dayno == 8) {
            $year_sequ = array(4, 6, 8);
            for ($i = $start_year; $i <= $year_limit; $i++) {
                $cal_year = str_split($i, 1);
                $sum = array_sum($cal_year);
                while (strlen($sum) != 1) {
                    $sum = str_split($sum);
                    $sum = array_sum($sum);
                }
                $sum = intval($sum);
                if (in_array($sum, $year_sequ)) {
                    array_push($years, $i);
                }
            }
        }
        if ($dayno == 9) {
            $year_sequ = array(3, 9, 1);
            for ($i = $start_year; $i <= $year_limit; $i++) {
                $cal_year = str_split($i, 1);
                $sum = array_sum($cal_year);
                while (strlen($sum) != 1) {
                    $sum = str_split($sum);
                    $sum = array_sum($sum);
                }
                $sum = intval($sum);
                if (in_array($sum, $year_sequ)) {
                    array_push($years, $i);
                }
            }
        }
        $years = implode(",", $years);

        $lifechanges = array(
            "module_name" => "Life Changes", "number" => $dayno, "ages" => $ages,
            "years" => $years
        );

        $loginuserjoinyear = $user->created_at;
        $explodejoindate = explode('-', $loginuserjoinyear);
        $joinyear = intval($explodejoindate[0]);
        $joinmonth = intval($explodejoindate[1]);
        $explodedatetime = explode(' ', $explodejoindate[2]);
        $joindateno = intval($explodedatetime[0]);
        $currentdate = $date_current;
        $explodecurrentdate = explode('-', $currentdate);
        $currentdaydate = $explodecurrentdate[2];
        $currentmonth = $explodecurrentdate[1];
        $currentyear = $explodecurrentdate[0];
        for ($y = $currentyear; $y <= $currentyear; $y++) {
            $favlist = array();
            $unfavlist = array();
            for ($m = $currentmonth; $m <= $currentmonth; $m++) {
                $daysinmonth = cal_days_in_month(0, $m, $y);
                $favdata = array();
                $unfavdata = array();

                if ($user->subscription_status != 0 && $user->subscription_status != 2) {
                    $startday = 1;
                    $no_of_day = $daysinmonth;
                } else {
                    if ($y == $joinyear && $m == $joinmonth) {
                        $startday = $joindateno;
                    } else {
                        $startday = 1;
                    }
                    if ($y == $currentyear && $m == $currentmonth) {
                        $no_of_day = $currentdaydate;
                    } else {
                        $no_of_day = $daysinmonth;
                    }
                }
                $fav_cosmic_stars = 0;
                $unfav_cosmic_stars = 0;
                for ($i = $startday; $i <= $no_of_day; $i++) {
                    $fav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 1)
                        ->where('month_id', $month)
                        ->where('date', $day)
                        ->first();
                    $unfav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 2)
                        ->where('month_id', $month)
                        ->where('date', $day)
                        ->first();
                    $fav_dates = $fav->numbers;
                    $fav_dates = str_replace(' ', '', $fav_dates);
                    $fav_dates = explode(',', $fav_dates);
                    $fav_days = $fav->days;
                    $fav_days = str_replace(' ', '', $fav_days);
                    $fav_days = explode(',', $fav_days);
                    $fav_months = $fav->months;
                    $fav_months = str_replace(' ', '', $fav_months);
                    $fav_months = explode(',', $fav_months);

                    $unfav_dates = $unfav->numbers;
                    $unfav_dates = str_replace(' ', '', $unfav_dates);
                    $unfav_dates = explode(',', $unfav_dates);
                    $unfav_days = $unfav->days;
                    $unfav_days = str_replace(' ', '', $unfav_days);
                    $unfav_days = explode(',', $unfav_days);
                    $unfav_months = $unfav->months;
                    $unfav_months = str_replace(' ', '', $unfav_months);
                    $unfav_months = explode(',', $unfav_months);
                    
                    $current_date = explode('-', date("D-j-M-Y", mktime(0, 0, 0, $m, $i, $y)));
                    $date_sum = str_split($i, 1);
                    $date_sum = array_sum($date_sum);
                    if (strlen($date_sum) != 1) {
                        $date_sum = str_split($date_sum);
                        $date_sum = array_sum($date_sum);
                    }

                    $day_star = 0;
                    $month_star = 0;
                    $unfavday_star = 0;
                    $unfavmonth_star = 0;
                    if (in_array($date_sum, $fav_dates)) {
                        $date_star = 1;
                        if (in_array($current_date[0], $fav_days)) {
                            $day_star = 1;
                        }
                        if (in_array($current_date[2], $fav_months)) {
                            $month_star = 1;
                        }
                    } else {
                        $date_star = 0;
                    }
                    if (in_array($date_sum, $unfav_dates)) {
                        $unfavdate_star = 1;
                        if (in_array($current_date[0], $unfav_days)) {
                            $unfavday_star = 1;
                        }
                        if (in_array($current_date[2], $unfav_months)) {
                            $unfavmonth_star = 1;
                        }
                    } else {
                        $unfavdate_star = 0;
                    }

                    $fav_cosmic_stars = $date_star + $day_star + $month_star;
                    $unfav_cosmic_stars = $unfavdate_star + $unfavday_star + $unfavmonth_star;

                    $favdata['module_type'] = "Fav Star";
                    $favdata['year'] = $y;
                    $favdata['month'] = $m;
                    $favdata['date'] = $i;
                    $favdata['datestar'] = $fav_cosmic_stars;
                    array_push($favlist, $favdata);

                    // $favdatekey[$i] =  $fav_cosmic_stars;
                    $unfavdata['module_type'] = "Unfav Star";
                    $unfavdata['year'] = $y;
                    $unfavdata['month'] = $m;
                    $unfavdata['date'] = $i;
                    $unfavdata['datestar'] = $unfav_cosmic_stars;
                    array_push($unfavlist, $unfavdata);
                    //$unfavdatekey[$i] =  $unfav_cosmic_stars;
                }
                $favarraydata = $favlist;
                $unfavarraydata = $unfavlist;
            }
        }
        $cosmiccalender = array("favstars" => $favarraydata, "unfavstars" => $unfavarraydata);

        $user_profile_pic = $user->profile_pic;
        if ($user_profile_pic == NULL) {
            $profile_pic = "https://be.astar8.com/img/default-profile-img.png";
        } else {
            $profile_pic = "https://be.astar8.com/profile_pic/" . $user_profile_pic;
        }

        $loginusername = $user->name;
        $loginuserchaldno = array();
        $userfinalname = str_replace(' ', '', $loginusername);
        $loginuserstrname = strtoupper($userfinalname);
        $loginusersplitname = str_split($loginuserstrname, 1);
        foreach ($loginusersplitname as $nameletter) {
            $loginuserchald_no = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                ->where('systemtype_id', 2)
                ->value('number');
            array_push($loginuserchaldno, $loginuserchald_no);
        }
        $loginuserchaldno_sum = array_sum($loginuserchaldno);
        while (strlen($loginuserchaldno_sum) != 1) {
            $loginuserchaldno_sum = str_split($loginuserchaldno_sum, 1);
            $loginuserchaldno_sum = array_sum($loginuserchaldno_sum);
        }

        $loginusernamereadingno = $loginuserchaldno_sum;

        $loginuser_dob = $user->dob;
        $explodedate = explode('-', $loginuser_dob);
        $dob_day = $explodedate[2];
        $dob_month = $explodedate[1];
        $dob_year = $explodedate[0];

        $splitdob_day = str_split($dob_day, 1);
        $dobdyno = array_sum($splitdob_day);
        $dobdyno = intval($dobdyno);
        while (strlen($dobdyno) != 1) {
            $dobdyno = str_split($dobdyno);
            $dobdyno = array_sum($dobdyno);
        }

        $datemonth = str_split($dob_month, 1);
        $datemonth_no = array_sum($datemonth);
        $datemonthno = intval($datemonth_no);
        if (strlen($datemonthno) != 1) {
            $split_datemonthno = str_split($datemonthno);
            $sum_datemonthno = array_sum($split_datemonthno);
            $datemonthno = $sum_datemonthno;
        }

        $dobyear = str_split($dob_year, 1);
        $dobyearno = array_sum($dobyear);
        while (strlen($dobyearno) != 1) {
            $splitdobyearno = str_split($dobyearno);
            $sum_dobyearno = array_sum($splitdobyearno);
            $dobyearno = $sum_dobyearno;
        }
        $dobyear_no = intval($dobyearno);
        $dobdestiny_no = $dobdyno + $datemonthno + $dobyear_no;
        while (strlen($dobdestiny_no) != 1) {
            $splitdobdestiny_no = str_split($dobdestiny_no);
            $dobdestiny_nosum = array_sum($splitdobdestiny_no);
            $dobdestiny_no = $dobdestiny_nosum;
        }

        $dobfav = Fav_unfav_parameter::where('type', 1)
            ->where('month_id', $dob_month)
            ->where('date', $dob_day)
            ->value('numbers');
        $dobunfav = Fav_unfav_parameter::where('type', 2)
            ->where('month_id', $dob_month)
            ->where('date', $dob_day)
            ->value('numbers');

        $strFav_numbers = str_replace(' ', '', $dobfav);
        $favnumber_array = explode(',', $strFav_numbers);
        $fav_arraycount = count($favnumber_array);

        $strUnfav_numbers = str_replace(' ', '', $dobunfav);
        $unfavnumber_array = explode(',', $strUnfav_numbers);
        $array_reverse_unfavnumber = array_reverse($unfavnumber_array);
        $unfav_arraycount = count($unfavnumber_array);

        $usernamefinal_perc = 0;
        if ($dobdyno == $loginusernamereadingno) {
            $dobpercentage = 98;
            $usernamefinal_perc = 98;
        } else {
            $dobpercentage = 0;
        }
        if ($dobdestiny_no == $loginusernamereadingno) {
            $destinypercentage = 84;
            $usernamefinal_perc = 84;
        } else {
            $destinypercentage = 0;
        }
        if ($fav_arraycount == 3) {
            if ($favnumber_array[1] == $loginusernamereadingno) {
                $favpercentage = 66;
                $usernamefinal_perc = 66;
            } elseif ($favnumber_array[2] == $loginusernamereadingno) {
                $favpercentage = 55;
                $usernamefinal_perc = 55;
            } else {
                $favpercentage = 0;
            }
        } elseif ($fav_arraycount == 4) {
            if ($favnumber_array[1] == $loginusernamereadingno) {
                $favpercentage = 74;
                $usernamefinal_perc = 74;
            } elseif ($favnumber_array[2] == $loginusernamereadingno) {
                $favpercentage = 65;
                $usernamefinal_perc = 65;
            } elseif ($favnumber_array[3] == $loginusernamereadingno) {
                $favpercentage = 55;
                $usernamefinal_perc = 55;
            } else {
                $favpercentage = 0;
            }
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'fav number error'
            ]);
        }
        if ($unfav_arraycount == 2) {
            if ($array_reverse_unfavnumber[0] == $loginusernamereadingno) {
                $unfavpercentage = 30;
                $usernamefinal_perc = 30;
            } elseif ($array_reverse_unfavnumber[1] == $loginusernamereadingno) {
                $unfavpercentage = 15;
                $usernamefinal_perc = 15;
            } else {
                $unfavpercentage = 0;
            }
        } elseif ($unfav_arraycount == 3) {
            if ($array_reverse_unfavnumber[0] == $loginusernamereadingno) {
                $unfavpercentage = 35;
                $usernamefinal_perc = 35;
            } elseif ($array_reverse_unfavnumber[1] == $loginusernamereadingno) {
                $unfavpercentage = 23;
                $usernamefinal_perc = 23;
            } elseif ($array_reverse_unfavnumber[2] == $loginusernamereadingno) {
                $unfavpercentage = 12;
                $usernamefinal_perc = 12;
            } else {
                $unfavpercentage = 0;
            }
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'unfav number error'
            ]);
        }
        if ($dobpercentage == 0 && $destinypercentage == 0 && $favpercentage == 0 && $unfavpercentage == 0) {
            $usernamefinal_perc = 50;
        }

        // $name_compatibilitypercentage = Compatibility_percentage::where('number', $chald_no_sum)->where('mate_number', $dayno)->first();
        // $namecompatibilitypercentage = $name_compatibilitypercentage->compatibility_percentage;

        $namecompatibilitypercentage = $usernamefinal_perc;

        $otherpersonreadings = User_namereading::where('user_id', '=', $user->id)->orderBy('id', 'DESC')->get();
        if($user->subscription_status != 0 && $user->subscription_status != 2)
        {
            if($otherpersonreadings != null)
            {
                $otheruserHistory = array();
                foreach ($otherpersonreadings as $otherpersonreading) {
                    $otherusername = $otherpersonreading->name;
                $finalotherusername = str_replace(' ', '', $otherusername);
                //print_r($name);
                //die();
                $struppername = strtoupper($finalotherusername);
                $splitotherusername = str_split($struppername, 1);
                foreach ($splitotherusername as $otherusernameletter) {
                    $pytha_Namenumbers[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $otherusernameletter . '%')
                        ->where('systemtype_id', 1)
                        ->value('number');
                    $chald_Namenumbers[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $otherusernameletter . '%')
                        ->where('systemtype_id', 2)
                        ->value('number');
                }
                $pytha_nameno_sum = array_sum($pytha_Namenumbers);
                $chald_nameno_sum = array_sum($chald_Namenumbers);

                while (strlen($pytha_nameno_sum) != 1 || strlen($chald_nameno_sum) != 1) {
                    $pytha_nameno_sum = str_split($pytha_nameno_sum, 1);
                    $pytha_nameno_sum = array_sum($pytha_nameno_sum);
                    $chald_nameno_sum = str_split($chald_nameno_sum, 1);
                    $chald_nameno_sum = array_sum($chald_nameno_sum);
                }
                $pytha_namedescription = Module_description::where('moduletype_id', 1)
                    ->where('number', $pytha_nameno_sum)
                    ->value('description');
                $pytha_namedescription = strip_tags($pytha_namedescription);
                $explodePythanamereading_desc = explode('||', $pytha_namedescription);
                $positive_Pythadesc = $explodePythanamereading_desc[0];
                $negative_Pythadesc = $explodePythanamereading_desc[1];

                $chald_namedescription = Module_description::where('moduletype_id', 1)
                    ->where('number', $chald_nameno_sum)
                    ->value('description');
                $chald_namedescription = strip_tags($chald_namedescription);
                $explodeChaldnamereading_desc = explode('||', $chald_namedescription);
                $positive_Chalddesc = $explodeChaldnamereading_desc[0];
                $negative_Chalddesc = $explodeChaldnamereading_desc[1];
                if($user->subscription_status != 0 && $user->subscription_status != 2){
                    $positive_desc = $positive_Chalddesc;
                    $negative_desc = $negative_Chalddesc;
                    $otherperson_nameno = $chald_nameno_sum;
                }else{
                    $positive_desc = $positive_Pythadesc;
                    $negative_desc = $negative_Pythadesc;
                    $otherperson_nameno = $pytha_nameno_sum;
                }

                $otherpersonNamedesc = array("positive_title" => "Positive", "positive_desc" => $positive_desc, "negative_title" => "Negative", "negative_desc" => $negative_desc);

                $otheruserdob = explode('-', $otherpersonreading->dob);
                $otheruserdobDay = str_split($otheruserdob[2], 1);
                $otheruserBirthno = array_sum($otheruserdobDay);
                $otheruserBirthno = intval($otheruserBirthno);
                while (strlen($otheruserBirthno) != 1) {
                    $otheruserBirthno = str_split($otheruserBirthno);
                    $otheruserBirthno = array_sum($otheruserBirthno);
                }
                $otheruserMonth = str_split($otheruserdob[1], 1);
                $otheruserMonthno = array_sum($otheruserMonth);
                while (strlen($otheruserMonthno) != 1) {
                    $otheruserMonthno = str_split($otheruserMonthno);
                    $otheruserMonthno = array_sum($otheruserMonthno);
                }
                $otheruseryear = str_split($otheruserdob[0], 1);
                $otheruseryearno = array_sum($otheruseryear);
                while (strlen($otheruseryearno) != 1) {
                    $otheruseryearno = str_split($otheruseryearno);
                    $otheruseryearno = array_sum($otheruseryearno);
                }
                $otheruseryearno = intval($otheruseryearno);
                $otheruserdestiny_no = $otheruserBirthno + $otheruserMonthno + $otheruseryearno;
                while (strlen($otheruserdestiny_no) != 1) {
                    $otheruserdestiny_no = str_split($otheruserdestiny_no);
                    $otheruserdestiny_no = array_sum($otheruserdestiny_no);
                }

                $otheruserdobdate = date("d-F-Y", strtotime($otherpersonreading->dob));
                $explodeotheruserdate = explode('-', $otheruserdobdate);
                $otheruserday = $explodeotheruserdate[0];
                $otherusermonth = $explodeotheruserdate[1];
                $otheruserzodic = Zodic_sign::where('title', 'LIKE', '%' . $otherusermonth . '%')->get();

                if ($otherusermonth == "March") {
                    $title_DOBDay = $otheruserzodic[1]->title;
                    $explodetitle_DOBDay = explode(' ', $title_DOBDay);
                    $title_DOB_Day = $explodetitle_DOBDay[2];

                    if ($otheruserday <= $title_DOB_Day) {
                        $zodicdata = $otheruserzodic[1];
                    } else {
                        $zodicdata = $otheruserzodic[0];
                    }
                } else {
                    $title_DOBDay = $otheruserzodic[1]->title;
                    $explodetitle_DOBDay = explode(' ', $title_DOBDay);
                    $title_DOB_Day = $explodetitle_DOBDay[2];

                    if ($otheruserday <= $title_DOB_Day) {
                        $zodicdata = $otheruserzodic[0];
                    } else {
                        $zodicdata = $otheruserzodic[1];
                    }
                }
                $otheruserData = array();
                $otheruserData['user_id'] = $otherpersonreading->user_id;
                $otheruserData['otheruserid'] = $otherpersonreading->id;
                $otheruserData['fullname'] = $otherpersonreading->name;
                $otheruserData['check_date'] = $otherpersonreading->check_date;
                $otheruserData['namenumber'] = $otherperson_nameno;
                $otheruserData['namedescription'] = $otherpersonNamedesc;
                $otheruserData['zodiacsign'] = $zodicdata->zodic_sign;
                $otheruserData['destinyno'] = $otheruserdestiny_no;
                array_push($otheruserHistory, $otheruserData);
                }
            }else{
                $otheruserHistory = '';
            }
        }else{
            $otheruserHistory = '';
        }
        
        return response()->json([
            'status' => 1,
            'message' => 'Success',
            'user_token' => $user->user_token,
            'subscription_status' => $user->subscription_status,
            'userdetail' => array('userid' => $userid, 'fullname' => $user->name, 'dob' => $user->dob, 'email'=>$user->email,'phoneno'=>$user->phoneno, 'gender' => $gender, 'occupation' => $occupation, 'age' => $userage, 'profile_pic' => $profile_pic, 'joining_date' => date_format($loginuserjoinyear, 'Y-m-d'), 'namecompatibilitypercentage' => $namecompatibilitypercentage),
            'Module_types' => $dobreadingdetail,
            'primary_detail' => $primarynumber,
            'compatible_partner' => $compatible_partner,
            'luckyparameters' => $luckyparameters,
            'planet_detail' => $planetnumber,
            'zodiac_detail' => $zodiacsign,
            'life_cycles' => $lifecycle,
            'name_reading' => $namereadingdetail,
            'magic_box' => $magicbox,
            'cosmic_calender' => $cosmiccalender,
            'favunfav_parameters' => $favunfavparameters,
            'lifechanges' => $lifechanges,
            'otheruserHistory' => $otheruserHistory
        ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'User not found.'
            ]);
        }
    }
    //endhere

    /*
    public function usercosmiccelender(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required',
            'date_current' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $date_current = $request->date_current;
        $userid = $request->userid;
        $user = User::find($userid);
        if ($user) {
            $userdob = $user->dob;
            $explodedob = explode('-', $userdob);
            $dobday = $explodedob[2];
            $dobmonth = $explodedob[1];

         
            $loginuserjoindate = $user->created_at;
            $explodejoindate = explode('-', $loginuserjoindate);
            $joinyear = intval($explodejoindate[0]);
            $joinmonth = intval($explodejoindate[1]);
            $explodedatetime = explode(' ', $explodejoindate[2]);
            $joindateno = intval($explodedatetime[0]);

            $explodeDate_current = explode('-', $date_current);

            $currentdaydate = $explodeDate_current[2];
            $currentmonth = $explodeDate_current[1];
            $currentyear = $explodeDate_current[0];

            //  if ($user->subscription_status != 0 && $user->subscription_status != 2) {
            //     $currentyear = date('Y') + 1;
            // } else {
            //     $currentyear = date('Y');
            // } 
            $favlist = array();
            $unfavlist = array();
            for ($y = $joinyear; $y <= $currentyear; $y++) {
                
                if ($y == $joinyear) {
                    $startmonth = $joinmonth;
                } else {
                    $startmonth = 1;
                }
                if ($y == $currentyear) {
                    $no_of_month = $currentmonth;
                } else {
                    $no_of_month = 12;
                }

                for ($m = $startmonth; $m <= $no_of_month; $m++) {
                    $daysinmonth = cal_days_in_month(0, $m, $y);

                    //  if ($y == $joinyear && $m == $joinmonth) {
                    //     $startday = $joindateno;
                    // } else {
                    //     $startday = 1;
                    // }
                    // if ($y == $currentyear && $m == $currentmonth) {
                    //     $no_of_day = $currentdaydate;
                    // } else {
                    //     $no_of_day = $daysinmonth;
                    // }

                    // if ($y == $currentyear && $m == $joinmonth) {
                    //     $no_of_day = $joindateno - 1;
                    // }
					 


                    if ($y == $joinyear && $m == $joinmonth) {
                        $startday = $joindateno;
                    } else {
                        $startday = 1;
                    }
                    if ($y == $currentyear && $m == $currentmonth) {
                        $no_of_day = $currentdaydate;
                    } else {
                        $no_of_day = $daysinmonth;
                    }

                    for ($i = $startday; $i <= $no_of_day; $i++) {

                        $fav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 1)
                            ->where('month_id', $dobmonth)
                            ->where('date', $dobday)
                            ->first();
                        $unfav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 2)
                            ->where('month_id', $dobmonth)
                            ->where('date', $dobday)
                            ->first();
                        $fav_dates = $fav->numbers;
                        $fav_dates = str_replace(' ', '', $fav_dates);
                        $fav_dates = explode(',', $fav_dates);
                        $fav_days = $fav->days;
                        $fav_days = str_replace(' ', '', $fav_days);
                        $fav_days = explode(',', $fav_days);
                        $fav_months = $fav->months;
                        $fav_months = str_replace(' ', '', $fav_months);
                        $fav_months = explode(',', $fav_months);

                        $unfav_dates = $unfav->numbers;
                        $unfav_dates = str_replace(' ', '', $unfav_dates);
                        $unfav_dates = explode(',', $unfav_dates);
                        $unfav_days = $unfav->days;
                        $unfav_days = str_replace(' ', '', $unfav_days);
                        $unfav_days = explode(',', $unfav_days);
                        $unfav_months = $unfav->months;
                        $unfav_months = str_replace(' ', '', $unfav_months);
                        $unfav_months = explode(',', $unfav_months);
                        $current_date = explode('-', date("D-j-M-Y", mktime(0, 0, 0, $m, $i, $y)));
                        $date_sum = str_split($i, 1);
                        $date_sum = array_sum($date_sum);
                        if (strlen($date_sum) != 1) {
                            $date_sum = str_split($date_sum);
                            $date_sum = array_sum($date_sum);
                        }

                        $day_star = 0;
                        $month_star = 0;
                        $unfavday_star = 0;
                        $unfavmonth_star = 0;
                        if (in_array($date_sum, $fav_dates)) {
                            $date_star = 1;
                        } else {
                            $date_star = 0;
                        }
                        if (in_array($current_date[0], $fav_days)) {
                            $day_star = 1;
                        } else {
                            $day_star = 0;
                        }
                        if (in_array($current_date[2], $fav_months)) {
                            $month_star = 1;
                        } else {
                            $month_star = 0;
                        }
                        if (in_array($date_sum, $unfav_dates)) {
                            $unfavdate_star = 1;
                        } else {
                            $unfavdate_star = 0;
                        }
                        if (in_array($current_date[0], $unfav_days)) {
                            $unfavday_star = 1;
                        } else {
                            $unfavday_star = 0;
                        }
                        if (in_array($current_date[2], $unfav_months)) {
                            $unfavmonth_star = 1;
                        } else {
                            $unfavmonth_star = 0;
                        }

                        $fav_cosmic_stars = 0;
                        $unfav_cosmic_stars = 0;

                        if ($date_star == 1) {
                            $fav_cosmic_stars = $date_star + $day_star + $month_star;
                        } elseif ($unfavdate_star == 1) {
                            $unfav_cosmic_stars = $unfavdate_star + $unfavday_star + $unfavmonth_star;
                        } elseif ($date_star == 0 && $day_star == 1) {
                            $fav_cosmic_stars = 0 + $day_star + $month_star;
                        } elseif ($unfavdate_star == 0 && $unfavday_star == 1) {
                            $unfav_cosmic_stars = 0 + $unfavday_star + $unfavmonth_star;
                        } elseif ($date_star == 0 && $day_star == 0 && $month_star == 1) {
                            $fav_cosmic_stars = 0 + 0 + $month_star;
                        } elseif ($unfavdate_star == 0 && $unfavday_star == 0 && $unfavmonth_star == 1) {
                            $unfav_cosmic_stars = 0 + 0 + $unfavmonth_star;
                        } else {
                            $fav_cosmic_stars = 0;
                            $unfav_cosmic_stars = 0;
                        }

                        $favdata = array();
                        $favdata['module_type'] = "Fav Star";
                        $favdata['year'] = $y;
                        $favdata['month'] = $m;
                        $favdata['date'] = $i;
                        $favdata['datestar'] = $fav_cosmic_stars;
                        array_push($favlist, $favdata);

                        // $favdatekey[$i] =  $fav_cosmic_stars;
                        $unfavdata = array();
                        $unfavdata['module_type'] = "Unfav Star";
                        $unfavdata['year'] = $y;
                        $unfavdata['month'] = $m;
                        $unfavdata['date'] = $i;
                        $unfavdata['datestar'] = $unfav_cosmic_stars;
                        array_push($unfavlist, $unfavdata);
                        //$unfavdatekey[$i] =  $unfav_cosmic_stars;
                    }
                }
            }

            $cosmiccalender = array("favstars" => $favlist, "unfavstars" => $unfavlist);

            return response()
                ->json([
                    'status' => 1,
                    'message' => 'Success',
                    'subscription_status' => $user->subscription_status,
                    'cosmiccalender' => $cosmiccalender
                ]);
        }
    }
    */

    public function newothercompatibilitycheck(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $type = $request->type;
        if ($type == 2) {
            $validator = Validator::make($request->all(), [
                'userid' => 'required',
                'type' => 'required',
                'name' => 'required',
                'dob' => 'required',
                'gender' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }

            $userid = $request->userid;
            $otheruser_name = $request->name;
            $gender = $request->gender;
            $otheruser_dob = $request->dob;
            $email = $request->email;
            $typename = "one to other";
            $loginuser = User::find($userid);
            $firstDateOfWeek = date("Y-m-d", strtotime('monday this week'));
            $lastdateOfWeek = date("Y-m-d", strtotime('sunday this week'));
            $oneToOtherCompChecks = User_compatiblecheck::where('user_id', '=', $userid)->where('type', '=', 2)->whereBetween('created_at', [$firstDateOfWeek . " 00:00:00", $lastdateOfWeek . " 23:59:59"])->get();

            if (count($oneToOtherCompChecks) == 0 && $loginuser->subscription_status == 0) {
                $message = "You have only Two compatibility check left in this week.";
            } elseif (count($oneToOtherCompChecks) == 0 && $loginuser->subscription_status == 2) {
                $message = "You have only Two compatibility check left in this week.";
            }elseif (count($oneToOtherCompChecks) == 1 && $loginuser->subscription_status == 0) {
                $message = "You have only One compatibility check left in this week.";
            } elseif (count($oneToOtherCompChecks) == 1 && $loginuser->subscription_status == 2) {
                $message = "You have only One compatibility check left in this week.";
            }elseif (count($oneToOtherCompChecks) == 2 && $loginuser->subscription_status == 0) {
                $message = "You don't have any compatibility check left in this week. Please wait for next week Or Upgrade and Subscribe to Unlock Premium Features!";
            } elseif (count($oneToOtherCompChecks) == 2 && $loginuser->subscription_status == 2) {
                $message = "You don't have any compatibility checks left this week. Please wait for next week Or Your monthly plan has been paused. Please resume and unlock premium features!";
            } else {
                $message = "Sucess";
            }
            if (count($oneToOtherCompChecks) >= 3 && $loginuser->subscription_status == 0) {
                return response()->json([
                    'status' => 0,
                    'message' => "You don't have any compatibility check left in this week. Please wait for next week Or Upgrade and Subscribe to Unlock Premium Features!",
                    'subscription_status' => $loginuser->subscription_status,
                ]);
            }elseif (count($oneToOtherCompChecks) >= 3 && $loginuser->subscription_status == 2) {
                return response()->json([
                    'status' => 0,
                    'message' => "You don't have any compatibility checks left this week. Please wait for next week Or Your monthly plan has been paused. Please resume and unlock premium features!",
                    'subscription_status' => $loginuser->subscription_status,
                ]);
            } else {

                $compatibility = User_compatiblecheck::create([
                    'user_id' => $userid,
                    'type' => $type,
                    'type_name' => $typename,
                    'name' => $otheruser_name,
                    'gender' => $gender,
                    'email' => $email,
                    'type_dates' => 3,
                    'dates' => $otheruser_dob,
                ]);

                if ($compatibility) {
                    $person_id = $compatibility->id;

                    $loginuser_dob = $loginuser->dob;
                    $loginuserdob = explode("-", $loginuser_dob);

                    // DOB reading 
                    $explode_dobdate = explode('-', $otheruser_dob);
                    $day = $explode_dobdate[2];
                    $month = $explode_dobdate[1];
                    $year = $explode_dobdate[0];

                    $dayno = str_split($day, 1);
                    $dayno = array_sum($dayno);
                    $dayno = intval($dayno);
                    while (strlen($dayno) != 1) {
                        $split_dayno = str_split($dayno);
                        $sum_split_dayno = array_sum($split_dayno);
                        $dayno = $sum_split_dayno;
                    }

                    $otherperson_primeno = $dayno;
                    $dobdesc = Module_description::where('moduletype_id', 2)
                        ->where('number', $dayno)
                        ->value('description');
                    $dobdesc = strip_tags($dobdesc);

                    $birthdayreading = array("module_type" => 2, "module_name" => "DOB Reading", "number" => $dayno, "description" => $dobdesc);

                    //elemental number
                    $elementaldesc = Module_description::where('moduletype_id', 4)
                        ->where('number', $dayno)
                        ->value('description');
                    $elementaldesc = strip_tags($elementaldesc);

                    $elementalno = array("module_type" => 4, "module_name" => "Elemental Number", "number" => $dayno, "description" => $elementaldesc);

                    //basic health reading
                    $basichealthdesc = Module_description::where('moduletype_id', 5)
                        ->where('number', $dayno)
                        ->value('description');
                    $basichealthdesc = strip_tags($basichealthdesc);

                    $basichealthreading = array("module_type" => 5, "module_name" => "Basic Health Reading", "number" => $dayno, "description" => $basichealthdesc);

                    //health precaution
                    $precautiondesc = Module_description::where('moduletype_id', 6)
                        ->where('number', $dayno)
                        ->value('description');
                    $precautiondesc = strip_tags($precautiondesc);

                    $healthprecaution = array("module_type" => 6, "module_name" => "Health Precaution", "number" => $dayno, "description" => $precautiondesc);

                    //basic Parenting
                    $basicparentingdesc = Module_description::where('moduletype_id', 12)
                        ->where('number', $dayno)
                        ->value('description');
                    $basicparentingdesc = strip_tags($basicparentingdesc);

                    $basicparenting = array(
                        "module_type" => 12, "module_name" => "Basic Parent Reading",
                        "number" => $dayno, "description" => $basicparentingdesc
                    );

                    //detail Parenting  
                    $detailparentingdesc = Module_description::where('moduletype_id', 13)
                        ->where('number', $dayno)
                        ->value('description');
                    $detailparentingdesc = strip_tags($detailparentingdesc);

                    $detailparenting = array(
                        "module_type" => 13, "module_name" => "Detailed Parent Reading",
                        "number" => $dayno, "description" => $detailparentingdesc
                    );

                    //basic money 
                    $basicmoneydesc = Module_description::where('moduletype_id', 14)
                        ->where('number', $dayno)
                        ->value('description');
                    $basicmoneydesc = strip_tags($basicmoneydesc);

                    $basicmoneymatter = array(
                        "module_type" => 14, "module_name" => "Basic Money Matters",
                        "number" => $dayno, "description" => $basicmoneydesc
                    );

                    //detail money 
                    $detailmoneydesc = Module_description::where('moduletype_id', 15)
                        ->where('number', $dayno)
                        ->value('description');
                    $detailmoneydesc = strip_tags($detailmoneydesc);

                    $detailmoneymatter = array(
                        "module_type" => 15, "module_name" => "Detailed Money Matters",
                        "number" => $dayno, "description" => $detailmoneydesc
                    );

                    //destiny number
                    $monthno = str_split($month, 1);
                    $monthno = array_sum($monthno);
                    while (strlen($monthno) != 1) {
                        $monthno = str_split($monthno);
                        $monthno = array_sum($monthno);
                    }
                    $yearno = str_split($year, 1);
                    $yearno = array_sum($yearno);
                    while (strlen($yearno) != 1) {
                        $yearno = str_split($yearno);
                        $yearno = array_sum($yearno);
                    }
                    $yearno = intval($yearno);
                    $destiny_no = $dayno + $monthno + $yearno;
                    while (strlen($destiny_no) != 1) {
                        $destiny_no = str_split($destiny_no);
                        $destiny_no = array_sum($destiny_no);
                    }
                    $destinynodesc = Module_description::where('moduletype_id', 16)
                        ->where('number', $destiny_no)
                        ->value('description');
                    $destinynodesc = strip_tags($destinynodesc);

                    $explode_destinynodesc = explode('||', $destinynodesc);
                    $learn_desc = $explode_destinynodesc[0];
                    $notlearn_desc = $explode_destinynodesc[1];
                    $destinynumber = array(
                        "module_type" => 16, "module_name" => "Destiny Number", "number" => $destiny_no, "description" => $destinynodesc,
                        "learn_title" => "Learn To Be", "learn_desc" => $learn_desc, "notlearn_title" => "Learn Not To Be", "notlearn_desc" => $notlearn_desc
                    );

                    $dobreadingdetail = [
                        $birthdayreading, $elementalno, $basichealthreading, $healthprecaution, $basicmoneymatter, $detailmoneymatter,
                        $basicparenting, $detailparenting, $destinynumber
                    ];

                    //primary number
                    $primarynodesc = Primaryno_type::where('number', $dayno)
                        ->first();
                    $primarynumber = array(
                        "module_name" => "Primary Number", "number" => $dayno, "description" => $primarynodesc->description, "positive" => $primarynodesc->positive, "negative" => $primarynodesc->negative,
                        "occupations" => $primarynodesc->occupations, "health" => $primarynodesc->health, "partners" => $primarynodesc->partners, "times_of_the_year" => $primarynodesc->times_of_the_year,
                        "countries" => $primarynodesc->countries, "tibbits" => $primarynodesc->tibbits, "destiny_colors" => $primarynodesc->destiny_colors, "destiny_timing" => $primarynodesc->destiny_timing
                    );

                    //compatible partner
                    $compatiblepartner = Compatible_partner::where('number', $dayno)
                        ->first();

                    $compatible_partner = array(
                        "module_name" => "Compatible Partner", "number" => $dayno,
                        "description" => strip_tags($compatiblepartner->description),
                        "more_compatible_months" => $compatiblepartner->more_compatible_months,
                        "more_compatible_dates" => $compatiblepartner->more_compatible_dates,
                        "less_compatible_months" => $compatiblepartner->less_compatible_months,
                        "less_compatible_dates" => $compatiblepartner->less_compatible_dates
                    );

                    //luckiest parameters
                    $luckyparameterdesc = Luckiest_parameter::select('lucky_colours', 'lucky_gems', 'lucky_metals')
                        ->where('number', $dayno)
                        ->first();

                    $luckyparameters = array(
                        "module_name" => "Lucky Parameters", "number" => $dayno,
                        "lucky_colours" => $luckyparameterdesc->lucky_colours,
                        "lucky_gems" => $luckyparameterdesc->lucky_gems,
                        "lucky_metals" => $luckyparameterdesc->lucky_metals
                    );

                    //planet number
                    $planet = Planet_number::select('name', 'ruling_number', 'description')
                        ->where('ruling_number', $dayno)
                        ->first();

                    $planetnumber = array(
                        "module_name" => "Planet Number", "ruling_number" => $dayno,
                        "planet_name" => $planet->name,
                        "description" => $planet->description
                    );

                    //zodiac sign
                    $formetdob = date("d-F-Y", strtotime($otheruser_dob));
                    $zodiacdate = explode('-', $formetdob);
                    $dobday = $zodiacdate[0];
                    $dobmonth = $zodiacdate[1];
                    $zodiac = Zodic_sign::where('title', 'LIKE', '%' . $dobmonth . '%')
                        ->get();

                    if ($dobmonth == "March") {
                        $titledaydate = $zodiac[1]->title;
                        $explodetitle_daydate = explode(' ', $titledaydate);
                        $title_daydate = $explodetitle_daydate[2];

                        if ($dobday <= $title_daydate) {
                            $zodiacdata = $zodiac[1];
                        } else {
                            $zodiacdata = $zodiac[0];
                        }
                    } else {
                        $titledaydate = $zodiac[0]->title;
                        $explodetitle_daydate = explode(' ', $titledaydate);
                        $title_daydate = $explodetitle_daydate[2];

                        if ($dobday <= $title_daydate) {
                            $zodiacdata = $zodiac[0];
                        } else {
                            $zodiacdata = $zodiac[1];
                        }
                    }

                    $zodiacsign = array(
                        "module_name" => "Zodiac Sign", "zodiac_sign" => $zodiacdata->zodic_sign,
                        "zodiac_number" => $zodiacdata->zodic_number,
                        "zodiac_day" => $zodiacdata->zodic_day
                    );

                    //life cycle
                    $monthdescription = Life_cycle::where('number', $dayno)->value('cycle_by_month');
                    $daydescription = Life_cycle::where('number', $monthno)->value('cycle_by_date');
                    $yeardescription = Life_cycle::where('number', $yearno)->value('cycle_by_year');

                    $lifecycle = array(
                        "module_name" => "Life Cycle", "cycleone_number" => $monthno,
                        "cycleone_description" => $monthdescription,
                        "cycletwo_number" => $dayno,
                        "cycletwo_description" => $daydescription,
                        "cyclethree_number" => $yearno,
                        "cyclethree_description" => $yeardescription
                    );

                    //Name reading
                    $other_personname = $otheruser_name;
                    $finalname = str_replace(' ', '', $other_personname);
                    $strname = strtoupper($finalname);
                    $splitname = str_split($strname, 1);
                    foreach ($splitname as $letter) {
                        $pytha_number[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $letter . '%')
                            ->where('systemtype_id', 1)
                            ->value('number');
                        $chald_number[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $letter . '%')
                            ->where('systemtype_id', 2)
                            ->value('number');
                    }
                    $pytha_no_sum = array_sum($pytha_number);
                    $chald_no_sum = array_sum($chald_number);

                    while (strlen($pytha_no_sum) != 1 || strlen($chald_no_sum) != 1) {
                        $pytha_no_sum = str_split($pytha_no_sum, 1);
                        $pytha_no_sum = array_sum($pytha_no_sum);
                        $chald_no_sum = str_split($chald_no_sum, 1);
                        $chald_no_sum = array_sum($chald_no_sum);
                    }
                    $pytha_description = Module_description::where('moduletype_id', 1)
                        ->where('number', $pytha_no_sum)
                        ->value('description');
                    $pytha_description = strip_tags($pytha_description);
                    $chald_description = Module_description::where('moduletype_id', 1)
                        ->where('number', $chald_no_sum)
                        ->value('description');
                    $chalddescription = strip_tags($chald_description);
                    $explodenamereading_desc = explode('||', $chalddescription);
                    $positive_desc = $explodenamereading_desc[0];
                    $negative_desc = $explodenamereading_desc[1];
                    $namereadingdetail = array(
                        "module_name" => "Name Reading", "Pytha_number" => $pytha_no_sum, "Pytha_description" => $pytha_description, "Chald_number" => $chald_no_sum, "Chald_description" => $chald_description,
                        "Chald_positive_title" => "Positive", "Chald_positive_desc" => $positive_desc, "Chald_negative_title" => "Negative", "Chald_negative_desc" => $negative_desc
                    );

                    $othernamereadingno = $chald_no_sum;

                    //magic box
                    $pytha_number_count = array_count_values($pytha_number);
                    $chald_number_count = array_count_values($chald_number);

                    $magicbox = array("module_name" => "Magic Box", "pythagorean" => $pytha_number_count, "chaldean" => $chald_number_count);


                    //fav unfav parameters
                    $fav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 1)
                        ->where('month_id', $month)
                        ->where('date', $day)
                        ->first();
                    $unfav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 2)
                        ->where('month_id', $month)
                        ->where('date', $day)
                        ->first();

                    $unfav_number = str_replace(' ', '', $unfav->numbers);
                    $fav_number = str_replace(' ', '', $fav->numbers);

                    $fav_day = str_replace(',', ', ', $fav->days);
                    $fav_month = str_replace(',', ', ', $fav->months);
                    $unfav_day = str_replace(',', ', ', $unfav->days);
                    $unfav_month = str_replace(',', ', ', $unfav->months);
                    $favunfavparameters = array("favparameter" => $fav, "unfavparameters" => $unfav, 'fav_numbers_withoutSpace'=>$fav_number, 'unfav_numbers_withoutSpace'=>$unfav_number, 'fav_days_withSpace' => $fav_day,
                    'fav_months_withSpace' => $fav_month,'unfav_days_withSpace'=>$unfav_day, 'unfav_months_withSpace'=>$unfav_month);

                    //Life changes

                    $ages = Life_change::where('numbers', $dayno)->value('ages');
                    $start_year = intval($year);
                    $year_limit = $start_year + 100;
                    $years = array();
                    if ($dayno == 1) {
                        $year_sequ = array(1, 4);
                        for ($i = $start_year; $i <= $year_limit; $i++) {
                            $cal_year = str_split($i, 1);
                            $sum = array_sum($cal_year);
                            while (strlen($sum) != 1) {
                                $sum = str_split($sum);
                                $sum = array_sum($sum);
                            }
                            $sum = intval($sum);
                            if (in_array($sum, $year_sequ)) {
                                array_push($years, $i);
                            }
                        }
                    }
                    if ($dayno == 2) {
                        $year_sequ = array(2, 7);
                        for ($i = $start_year; $i <= $year_limit; $i++) {
                            $cal_year = str_split($i, 1);
                            $sum = array_sum($cal_year);
                            while (strlen($sum) != 1) {
                                $sum = str_split($sum);
                                $sum = array_sum($sum);
                            }

                            $sum = intval($sum);
                            if (in_array($sum, $year_sequ)) {
                                array_push($years, $i);
                            }
                        }
                    }
                    if ($dayno == 3) {
                        $year_sequ = array(3, 9);
                        for ($i = $start_year; $i <= $year_limit; $i++) {
                            $cal_year = str_split($i, 1);
                            $sum = array_sum($cal_year);
                            while (strlen($sum) != 1) {
                                $sum = str_split($sum);
                                $sum = array_sum($sum);
                            }
                            $sum = intval($sum);
                            if (in_array($sum, $year_sequ)) {
                                array_push($years, $i);
                            }
                        }
                    }
                    if ($dayno == 4) {
                        $year_sequ = array(4, 8, 1);
                        for ($i = $start_year; $i <= $year_limit; $i++) {
                            $cal_year = str_split($i, 1);
                            $sum = array_sum($cal_year);
                            while (strlen($sum) != 1) {
                                $sum = str_split($sum);
                                $sum = array_sum($sum);
                            }

                            $sum = intval($sum);
                            if (in_array($sum, $year_sequ)) {
                                array_push($years, $i);
                            }
                        }
                    }
                    if ($dayno == 5) {
                        $year_sequ = array(5, 6);
                        for ($i = $start_year; $i <= $year_limit; $i++) {
                            $cal_year = str_split($i, 1);
                            $sum = array_sum($cal_year);
                            while (strlen($sum) != 1) {
                                $sum = str_split($sum);
                                $sum = array_sum($sum);
                            }

                            $sum = intval($sum);
                            if (in_array($sum, $year_sequ)) {
                                array_push($years, $i);
                            }
                        }
                    }
                    if ($dayno == 6) {
                        $year_sequ = array(6, 2, 3);
                        for ($i = $start_year; $i <= $year_limit; $i++) {
                            $cal_year = str_split($i, 1);
                            $sum = array_sum($cal_year);
                            while (strlen($sum) != 1) {
                                $sum = str_split($sum);
                                $sum = array_sum($sum);
                            }

                            $sum = intval($sum);
                            if (in_array($sum, $year_sequ)) {
                                array_push($years, $i);
                            }
                        }
                    }
                    if ($dayno == 7) {
                        $year_sequ = array(2, 7);
                        for ($i = $start_year; $i <= $year_limit; $i++) {
                            $cal_year = str_split($i, 1);
                            $sum = array_sum($cal_year);
                            while (strlen($sum) != 1) {
                                $sum = str_split($sum);
                                $sum = array_sum($sum);
                            }
                            $sum = intval($sum);
                            if (in_array($sum, $year_sequ)) {
                                array_push($years, $i);
                            }
                        }
                    }
                    if ($dayno == 8) {
                        $year_sequ = array(4, 6, 8);
                        for ($i = $start_year; $i <= $year_limit; $i++) {
                            $cal_year = str_split($i, 1);
                            $sum = array_sum($cal_year);
                            while (strlen($sum) != 1) {
                                $sum = str_split($sum);
                                $sum = array_sum($sum);
                            }
                            $sum = intval($sum);
                            if (in_array($sum, $year_sequ)) {
                                array_push($years, $i);
                            }
                        }
                    }
                    if ($dayno == 9) {
                        $year_sequ = array(3, 9, 1);
                        for ($i = $start_year; $i <= $year_limit; $i++) {
                            $cal_year = str_split($i, 1);
                            $sum = array_sum($cal_year);
                            while (strlen($sum) != 1) {
                                $sum = str_split($sum);
                                $sum = array_sum($sum);
                            }
                            $sum = intval($sum);
                            if (in_array($sum, $year_sequ)) {
                                array_push($years, $i);
                            }
                        }
                    }
                    $years = implode(",", $years);

                    $lifechanges = array(
                        "module_name" => "Life Changes", "number" => $dayno, "ages" => $ages,
                        "years" => $years
                    );

                    //partner relationship
                    $uaerdobday = str_split($loginuserdob[2], 1);
                    $user_number = array_sum($uaerdobday);
                    while (strlen($user_number) != 1) {
                        $user_number = str_split($user_number);
                        $user_number = array_sum($user_number);
                    }
                    $persion_day = str_split($explode_dobdate[2], 1);
                    $persion_number = array_sum($persion_day);
                    $persion_number = intval($persion_number);
                    while (strlen($persion_number) != 1) {
                        $persion_number = str_split($persion_number);
                        $persion_number = array_sum($persion_number);
                    }

                    $relation = Partner_relationship::select('description')->where('number', $user_number)
                        ->where('mate_number', $persion_number)
                        ->first();


                    // login user detail

                    $loginuser_name = $loginuser->name;
                    $finalname = str_replace(' ', '', $loginuser_name);
                    $loginuserstrname = strtoupper($finalname);
                    $loginusersplitname = str_split($loginuserstrname, 1);
                    foreach ($loginusersplitname as $nameletter) {
                        $pytha_no[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                            ->where('systemtype_id', 1)
                            ->value('number');
                        $chald_no[] = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                            ->where('systemtype_id', 2)
                            ->value('number');
                    }
                    $loginuserpythano_sum = array_sum($pytha_no);
                    $loginuserchaldno_sum = array_sum($chald_no);

                    while (strlen($loginuserpythano_sum) != 1 || strlen($loginuserchaldno_sum) != 1) {
                        $loginuserpythano_sum = str_split($loginuserpythano_sum, 1);
                        $loginuserpythano_sum = array_sum($loginuserpythano_sum);
                        $loginuserchaldno_sum = str_split($loginuserchaldno_sum, 1);
                        $loginuserchaldno_sum = array_sum($loginuserchaldno_sum);
                    }
                    $loginuserpytha_description = Module_description::where('moduletype_id', 1)
                        ->where('number', $loginuserpythano_sum)
                        ->value('description');
                    $loginuserpytha_description = strip_tags($loginuserpytha_description);
                    $loginuserchald_description = Module_description::where('moduletype_id', 1)
                        ->where('number', $loginuserchaldno_sum)
                        ->value('description');
                    $loginuserchald_description = strip_tags($loginuserchald_description);
                    $explodeloginusername_desc = explode('||', $loginuserchald_description);
                    $loginuserpositive_desc = $explodeloginusername_desc[0];
                    $loginusernegative_desc = $explodeloginusername_desc[1];
                    $loginusernamereadingdetail = array("Chald_positive_title" => "Positive", "Chald_positive_desc" => $loginuserpositive_desc, "Chald_negative_title" => "Negative", "Chald_negative_desc" => $loginusernegative_desc);

                    /*  if($loginuserchaldno_sum != 9)
                    {
                        $loginusernamedesc = $loginuserchald_description;
                    }else
                    {
                        $loginusernamedesc = $loginuserpytha_description;
                    } */
                    $loginusernamedesc = $loginusernamereadingdetail;
                    $loginusernamereadingno = $loginuserchaldno_sum;


                    //primary number    
                    $loginuserday = str_split($loginuserdob[2], 1);
                    $sumloginuserbirthno = array_sum($loginuserday);
                    $loginuserbirthno = intval($sumloginuserbirthno);
                    while (strlen($loginuserbirthno) != 1) {
                        $split_loginuserbirthno = str_split($loginuserbirthno);
                        $sum_loginuserbirthno = array_sum($split_loginuserbirthno);
                        $loginuserbirthno = $sum_loginuserbirthno;
                    }

                    $loginuser_primeryno = $loginuserbirthno;

                    //planet number
                    $loginuserplanet = Planet_number::where('ruling_number', $loginuserbirthno)->first();

                    //Lucky parameters
                    $loginuserluckyparameters = Luckiest_parameter::where('number', $loginuserbirthno)->first();

                    //zodiac sign
                    $loginuserdobdate = date("d-F-Y", strtotime($loginuser_dob));
                    $loginuserdobdate = explode('-', $loginuserdobdate);
                    $loginuserdobday = $loginuserdobdate[0];
                    $loginuserdobmonth = $loginuserdobdate[1];
                    $zodic = Zodic_sign::where('title', 'LIKE', '%' . $loginuserdobmonth . '%')
                        ->get();

                    if ($loginuserdobmonth == "March") {
                        $titledaydate = $zodic[1]->title;
                        $explodetitle_daydate = explode(' ', $titledaydate);
                        $title_daydate = $explodetitle_daydate[2];

                        if ($loginuserdobday <= $title_daydate) {
                            $loginuserzodicdata = $zodic[1];
                        } else {
                            $loginuserzodicdata = $zodic[0];
                        }
                    } else {
                        $titledaydate = $zodic[0]->title;
                        $explodetitle_daydate = explode(' ', $titledaydate);
                        $title_daydate = $explodetitle_daydate[2];

                        if ($loginuserdobday <= $title_daydate) {
                            $loginuserzodicdata = $zodic[0];
                        } else {
                            $loginuserzodicdata = $zodic[1];
                        }
                    }
                    //fav and unfav months
                    $loginuserfav = Fav_unfav_parameter::where('type', 1)
                        ->where('month_id', $loginuserdob[1])
                        ->where('date', $loginuserdob[2])
                        ->first();
                    $loginuserunfav = Fav_unfav_parameter::where('type', 2)
                        ->where('month_id', $loginuserdob[1])
                        ->where('date', $loginuserdob[2])
                        ->first();

                    $loginUserfav_day = str_replace(',', ', ', $fav->days);
                    $loginUserfav_month = str_replace(',', ', ', $fav->months);
                    $loginUserunfav_day = str_replace(',', ', ', $unfav->days);
                    $loginUserunfav_month = str_replace(',', ', ', $unfav->months);

                    //Element name
                    $loginuserelementdescription = Module_description::where('systemtype_id', 1)
                        ->where('moduletype_id', 4)
                        ->where('number', $loginuserbirthno)
                        ->value('description');
                    $loginuserelement = explode(" ", $loginuserelementdescription);
                    $loginuserelement = strip_tags($loginuserelement[0]);

                    //relation percentage 

                    $relation_dobpercentage = Compatibility_percentage::where('number', $user_number)->where('mate_number', $persion_number)->first();
                    $dob_percentage = $relation_dobpercentage->compatibility_percentage;

                    $relation_namepercentage = Compatibility_percentage::where('number', $loginusernamereadingno)->where('mate_number', $othernamereadingno)->first();
                    //$love_scale = "On the scale of love, this one rates ".$relation_namepercentage->strength." ".$relation_namepercentage->compatibility_number;
                    $love_scale = "On the scale of love, this one rates " . $relation_dobpercentage->strength . " " . $relation_dobpercentage->compatibility_number;
                    $name_percentage = $relation_namepercentage->compatibility_percentage;

                    $loginusercompatiblepartner = Compatible_partner::where('number', $loginuser_primeryno)
                        ->first();

                    $compatible_dates = $loginusercompatiblepartner->more_compatible_dates;
                    $compatible_dates_array = explode(',', $compatible_dates);

                    $compatible_months = $loginusercompatiblepartner->more_compatible_months;
                    $compatible_months_array = explode(',', $compatible_months);

                    $uncompatible_dates = $loginusercompatiblepartner->less_compatible_dates;
                    $uncompatible_dates_array = explode(',', $uncompatible_dates);

                    $uncompatible_months = $loginusercompatiblepartner->less_compatible_months;
                    $uncompatible_months_array = explode(',', $uncompatible_months);

                    $formate_otheruser_dob = date("d-F-Y", strtotime($otheruser_dob));
                    $explode_formate_otheruser_dob = explode('-', $formate_otheruser_dob);
                    $otheruser_dob_month = $explode_formate_otheruser_dob[1];

                    if (in_array($otherperson_primeno, $compatible_dates_array)) {
                        $calculated_finalperc = 98;
                    } elseif (in_array($otheruser_dob_month, $compatible_months_array)) {
                        $calculated_finalperc = 74;
                    } elseif (in_array($otherperson_primeno, $uncompatible_dates_array)) {
                        $calculated_finalperc = 28;
                    } elseif (in_array($otheruser_dob_month, $uncompatible_months_array)) {
                        $calculated_finalperc = 5;
                    } else {
                        $calculated_finalperc = 50;
                    }

                    $final_percentage = $calculated_finalperc;

                    $loginuserdetail = array(
                        'loginuserid' => $userid, 'loginusername' => $loginuser_name, 'loginuserdob' => $loginuser_dob, 'namedescription' => $loginusernamedesc, 'planet' => $loginuserplanet->name, 'zodiacsign' => $loginuserzodicdata->zodic_sign, 'primaryno' => $loginuserbirthno, 'element' => $loginuserelement, 'lucky_gems' => $loginuserluckyparameters->lucky_gems,
                        'fav_numbers' => $loginuserfav->numbers, 'unfav_numbers' => $loginuserunfav->numbers, 'fav_days' => $loginuserfav->days, 'unfav_days' => $loginuserunfav->days, 'fav_months' => $loginuserfav->months, 'unfav_months' => $loginuserunfav->months, 'fav_days_withSpace' => $loginUserfav_day,
                        'fav_months_withSpace' => $loginUserfav_month,'unfav_days_withSpace'=>$loginUserunfav_day, 'unfav_months_withSpace'=>$loginUserunfav_month);

                    $otheruserdetail = array(
                        'otherpersonid' => $person_id, 'otherpersonname' => $otheruser_name, 'otherpersondob' => $otheruser_dob, 'Module_types' => $dobreadingdetail,
                        'primary_detail' => $primarynumber, 'compatible_partner' => $compatible_partner, 'luckyparameters' => $luckyparameters, 'planet_detail' => $planetnumber, 'zodiac_detail' => $zodiacsign, 'life_cycles' => $lifecycle,
                        'name_reading' => $namereadingdetail, 'magic_box' => $magicbox, 'favunfav_parameters' => $favunfavparameters, 'lifechanges' => $lifechanges, 'relation_desc' => $relation->description, 'dobtodobpercentage' => $dob_percentage, 'nametonamepercentage' => $name_percentage, 'final_usercompatiblitypercentage' => $final_percentage, 'love_scale' => $love_scale
                    );

                    return response()->json([
                        'status' => 1,
                        'message' => $message,
                        'subscription_status' => $loginuser->subscription_status,
                        'otheruser_detail' => $otheruserdetail,
                        'loginuser_detail' => $loginuserdetail,
                    ]);
                } else {

                    return response()->json([
                        'status' => 0,
                        'message' => 'Something went wrong. Please try again',
                    ]);
                }
            }
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Error',
            ]);
        }
    }

    public function usercosmiccelender(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required',
            'requested_date' => 'required',
            'date_current' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $requested_date = $request->requested_date;
        $date_current = $request->date_current;
        $userid = $request->userid;
        $user = User::find($userid);
        if ($user) {
            $userdob = $user->dob;
            $explodedob = explode('-', $userdob);
            $dobday = $explodedob[2];
            $dobmonth = $explodedob[1];
            $dobYear = $explodedob[0];

            $loginuserjoindate = $user->created_at;
            $explodejoindate = explode('-', $loginuserjoindate);
            $joinyear = intval($explodejoindate[0]);
            $joinmonth = intval($explodejoindate[1]);
            $explodedatetime = explode(' ', $explodejoindate[2]);
            $joindateno = intval($explodedatetime[0]);

            $explodeDate_requested = explode('-', $requested_date);
            $requestedMonth = $explodeDate_requested[1];
            $requested_Year = $explodeDate_requested[0];
            $requested_Day = $explodeDate_requested[2];

            $explodeDate_current = explode('-', $date_current);
            $currentdaydate = $explodeDate_current[2];
            $currentmonth = $explodeDate_current[1];
            $current_year = $explodeDate_current[0];
            $endyear = $dobYear + 100;
            if ($user->subscription_status != 0 && $user->subscription_status != 2) {
                if ($requested_Year < $dobYear) {
                    $favlist = "";
                    $unfavlist = "";
                    $cosmiccalender = array("favstars" => $favlist, "unfavstars" => $unfavlist);
                    return response()->json([
                        'status' => 1,
                        'message' => 'Success',
                        'subscription_status' => $user->subscription_status,
                        'cosmiccalender' => $cosmiccalender
                    ]);
                } elseif ($requested_Year > $endyear) {
                    $favlist = "";
                    $unfavlist = "";
                    $cosmiccalender = array("favstars" => $favlist, "unfavstars" => $unfavlist);
                    return response()->json([
                        'status' => 1,
                        'message' => 'Success',
                        'subscription_status' => $user->subscription_status,
                        'cosmiccalender' => $cosmiccalender
                    ]);
                } else {
                    $join_year = $requested_Year;
                    $currentyear = $requested_Year;
                }
            } else {
                if($user->subscription_status == 0){
                    $msg = "Upgrade and Subscribe to Unlock Premium Features!";
                }elseif($user->subscription_status == 2){
                    $msg = "Your monthly plan has been paused. Please resume and unlock premium features!";
                }
                if ($joinyear > $requested_Year || $requested_Year > $current_year) {
                    return response()
                        ->json([
                            'status' => 0,
                            'message' => $msg,
                            'subscription_status' => $user->subscription_status
                        ]);
                } else {
                    $join_year = $requested_Year;
                    $currentyear = $requested_Year;
                }   
            }
            $favlist = array();
            $unfavlist = array();
            for ($y = $join_year; $y <= $currentyear; $y++) {

                if ($user->subscription_status != 0 && $user->subscription_status != 2) {
                    if ($y == $dobYear && $requestedMonth < $dobmonth) {
                        $favlist = "";
                        $unfavlist = "";
                        $cosmiccalender = array("favstars" => $favlist, "unfavstars" => $unfavlist);
                        return response()->json([
                            'status' => 1,
                            'message' => 'Success',
                            'subscription_status' => $user->subscription_status,
                            'cosmiccalender' => $cosmiccalender
                        ]);
                    } elseif ($y == $endyear && $requestedMonth > $dobmonth) {
                        $favlist = "";
                        $unfavlist = "";
                        $cosmiccalender = array("favstars" => $favlist, "unfavstars" => $unfavlist);
                        return response()->json([
                            'status' => 1,
                            'message' => 'Success',
                            'subscription_status' => $user->subscription_status,
                            'cosmiccalender' => $cosmiccalender
                        ]);
                    } else {
                        $startmonth = $requestedMonth;
                        $no_of_month = $requestedMonth;
                    }
                } else {
                    if($user->subscription_status == 0){
                        $msg = "Upgrade and Subscribe to Unlock Premium Features!";
                    }elseif($user->subscription_status == 2){
                        $msg = "Your monthly plan has been paused. Please resume and unlock premium features!";
                    }
                    if ($y == $joinyear && $joinmonth > $requestedMonth) {
                        return response()
                            ->json([
                                'status' => 0,
                                'message' => $msg,
                                'subscription_status' => $user->subscription_status
                            ]);
                    } elseif ($y == $current_year && $requestedMonth > $currentmonth) {
                        return response()
                            ->json([
                                'status' => 0,
                                'message' => $msg,
                                'subscription_status' => $user->subscription_status
                            ]);
                    } else {
                        $startmonth = $requestedMonth;
                        $no_of_month = $requestedMonth;
                    }
                }

                for ($m = $startmonth; $m <= $no_of_month; $m++) {
                    $daysinmonth = cal_days_in_month(0, $m, $y);

                    if ($user->subscription_status != 0 && $user->subscription_status != 2) {
                        $startday = 1;
                        $no_of_day = $daysinmonth;
                    } else {

                        if ($y == $joinyear && $m == $joinmonth) {
                            $startday = $joindateno;
                        } else {
                            $startday = 1;
                        }

                        if ($y == $current_year && $m == $currentmonth) {
                            $no_of_day = $requested_Day;
                        } else {
                            $no_of_day = $daysinmonth;
                        }
                    }

                    for ($i = $startday; $i <= $no_of_day; $i++) {
                        $fav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 1)
                            ->where('month_id', $dobmonth)
                            ->where('date', $dobday)
                            ->first();
                        $unfav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 2)
                            ->where('month_id', $dobmonth)
                            ->where('date', $dobday)
                            ->first();
                        $fav_dates = $fav->numbers;
                        $fav_dates = str_replace(' ', '', $fav_dates);
                        $fav_dates = explode(',', $fav_dates);
                        $fav_days = $fav->days;
                        $fav_days = str_replace(' ', '', $fav_days);
                        $fav_days = explode(',', $fav_days);
                        $fav_months = $fav->months;
                        $fav_months = str_replace(' ', '', $fav_months);
                        $fav_months = explode(',', $fav_months);

                        $unfav_dates = $unfav->numbers;
                        $unfav_dates = str_replace(' ', '', $unfav_dates);
                        $unfav_dates = explode(',', $unfav_dates);
                        $unfav_days = $unfav->days;
                        $unfav_days = str_replace(' ', '', $unfav_days);
                        $unfav_days = explode(',', $unfav_days);
                        $unfav_months = $unfav->months;
                        $unfav_months = str_replace(' ', '', $unfav_months);
                        $unfav_months = explode(',', $unfav_months);
                        $current_date = explode('-', date("D-j-M-Y", mktime(0, 0, 0, $m, $i, $y)));
                        $date_sum = str_split($i, 1);
                        $date_sum = array_sum($date_sum);
                        if (strlen($date_sum) != 1) {
                            $date_sum = str_split($date_sum);
                            $date_sum = array_sum($date_sum);
                        }

                        $day_star = 0;
                        $month_star = 0;
                        $unfavday_star = 0;
                        $unfavmonth_star = 0;
                        if (in_array($date_sum, $fav_dates)) {
                            $date_star = 1;
                            if (in_array($current_date[0], $fav_days)) {
                                $day_star = 1;
                            }
                            if (in_array($current_date[2], $fav_months)) {
                                $month_star = 1;
                            }
                        } else {
                            $date_star = 0;
                        }
                        if (in_array($date_sum, $unfav_dates)) {
                            $unfavdate_star = 1;
                            if (in_array($current_date[0], $unfav_days)) {
                                $unfavday_star = 1;
                            }
                            if (in_array($current_date[2], $unfav_months)) {
                                $unfavmonth_star = 1;
                            }
                        } else {
                            $unfavdate_star = 0;
                        }

                        $fav_cosmic_stars = 0;
                        $unfav_cosmic_stars = 0;

                        $fav_cosmic_stars = $date_star + $day_star + $month_star;
                        $unfav_cosmic_stars = $unfavdate_star + $unfavday_star + $unfavmonth_star;

                        $favdata = array();
                        $favdata['module_type'] = "Fav Star";
                        $favdata['year'] = $y;
                        $favdata['month'] = $m;
                        $favdata['date'] = $i;
                        $favdata['datestar'] = $fav_cosmic_stars;
                        array_push($favlist, $favdata);

                        // $favdatekey[$i] =  $fav_cosmic_stars;
                        $unfavdata = array();
                        $unfavdata['module_type'] = "Unfav Star";
                        $unfavdata['year'] = $y;
                        $unfavdata['month'] = $m;
                        $unfavdata['date'] = $i;
                        $unfavdata['datestar'] = $unfav_cosmic_stars;
                        array_push($unfavlist, $unfavdata);
                        //$unfavdatekey[$i] =  $unfav_cosmic_stars;

                    }
                }
            }

            $cosmiccalender = array("favstars" => $favlist, "unfavstars" => $unfavlist);

            return response()
                ->json([
                    'status' => 1,
                    'message' => 'Success',
                    'subscription_status' => $user->subscription_status,
                    'cosmiccalender' => $cosmiccalender
                ]);
        }
    }

    public function childcompatibilitycheck(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $type = $request->type;

        if ($type == 7) {

            $validator = Validator::make($request->all(), [
                'userid' => 'required',
                'type' => 'required',
                'name' => 'required',
                'dob' => 'required',
                'gender' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }

            $userid = $request->userid;
            $typename = 'child';
            $child_name = $request->name;
            $child_gender = $request->gender;
            $child_dob = $request->dob;
            $user = User::find($userid);
            $firstDateOfWeek = date("Y-m-d", strtotime('monday this week'));
            $lastdateOfWeek = date("Y-m-d", strtotime('sunday this week'));

            $childrenCompChecks = User_compatiblecheck::where('user_id', '=', $userid)->where('type', '=', 7)->whereBetween('created_at', [$firstDateOfWeek . " 00:00:00", $lastdateOfWeek . " 23:59:59"])->get();
            if (count($childrenCompChecks) == 0 && $user->subscription_status == 0) {
                $message = "You have only Two compatibility check left in this week.";
            } elseif (count($childrenCompChecks) == 0 && $user->subscription_status == 2) {
                $message = "You have only Two compatibility check left in this week.";
            }elseif (count($childrenCompChecks) == 1 && $user->subscription_status == 0) {
                $message = "You have only One compatibility check left in this week.";
            } elseif (count($childrenCompChecks) == 1 && $user->subscription_status == 2) {
                $message = "You have only One compatibility check left in this week.";
            }elseif (count($childrenCompChecks) == 2 && $user->subscription_status == 0) {
                $message = "You don't have any compatibility check left in this week. Please wait for next week Or Upgrade and Subscribe to Unlock Premium Features!";
            } elseif (count($childrenCompChecks) == 2 && $user->subscription_status == 2) {
                $message = "You don't have any compatibility checks left this week. Please wait for next week Or Your monthly plan has been paused. Please resume and unlock premium features!";
            } else {
                $message = "Sucess";
            }
            if (count($childrenCompChecks) >= 3 && $user->subscription_status == 0) {
                return response()->json([
                    'status' => 0,
                    'message' => "You don't have any compatibility check left in this week. Please wait for next week Or Upgrade and Subscribe to Unlock Premium Features!",
                    'subscription_status' => $user->subscription_status,
                ]);
            }elseif (count($childrenCompChecks) >= 3 && $user->subscription_status == 2) {
                return response()->json([
                    'status' => 0,
                    'message' => "You don't have any compatibility checks left this week. Please wait for next week Or Your monthly plan has been paused. Please resume and unlock premium features!",
                    'subscription_status' => $user->subscription_status,
                ]);
            } else {
                if ($user) {
                    $compatibility = User_compatiblecheck::create([
                        'user_id' => $userid,
                        'type' => $type,
                        'type_name' => $typename,
                        'name' => $child_name,
                        'gender' => $child_gender,
                        'type_dates' => 3,
                        'dates' => $child_dob,
                    ]);
                    if ($compatibility) {
                        $explodechilddob = explode('-', $child_dob);
                        $child_dob_date = $explodechilddob[2];
                        $child_dob_month = $explodechilddob[1];
                        $child_dob_year = $explodechilddob[0];

                        $child_dob_dateNo = str_split($child_dob_date, 1);
                        $child_dobDateNo = array_sum($child_dob_dateNo);
                        $child_dobdayno = intval($child_dobDateNo);
                        while (strlen($child_dobdayno) != 1) {
                            $split_dayno = str_split($child_dobdayno);
                            $sum_split_dayno = array_sum($split_dayno);
                            $child_dobdayno = $sum_split_dayno;
                        }
                        $prime_no = $child_dobdayno;
                        $child_desc = Module_description::where('moduletype_id', 20)
                            ->where('number', $prime_no)
                            ->value('description');

                        $childDetail = array('name' => $child_name, 'dob' => $child_dob, 'gender' => $child_gender, 'number' => $prime_no, 'description' => $child_desc);
                        return response()->json([
                            'status' => 1,
                            'message' => $message,
                            'subscription_status' => $user->subscription_status,
                            'childdetail' => $childDetail,
                        ]);
                    } else {

                        return response()->json([
                            'status' => 0,
                            'message' => 'Something went wrong. Please try again',
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => 0,
                        'message' => 'Error',
                    ]);
                }
            }
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Error',
            ]);
        }
    }

    public function subscriptionprize(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $data = $request->data;
        if($data == 'prizelist'){
            $prize = Subscription_prize::where('subscription_time', 1)->where('is_active',1)->orderBy('id', 'DESC')->first();
            $cents = $prize->prize * 100;
            $prize_list = array('dollar' => $prize->prize, 'cent'=>$cents);
            // return $prize;
            return response()->json([
                'status' => 1,
                'message' => 'Success',
                'prize' => $prize_list,
                'created_date' => $prize->created_at->format('Y-m-d'),
            ]);
        }else{
            return response()->json([
                'status' => 0,
                'message' => 'Error',
            ]);
        }
    }

    public function user_payments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => 'required',
            "date_current" => 'required',
            "subscription_id" => 'required',
            "status" => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $user_id = $request->user_id;
        $date_current = $request->date_current;
        $subscription_id = $request->subscription_id;
        $customer_id = $request->customer_id;
        $start_date = $request->start_date;
        $starDate_formate = date('Y-m-d h:m:s', $start_date);
        $end_date = $request->end_date;
        $renewal_date = date('Y-m-d h:m:s', $end_date);
        $status = $request->status;
        $prize = $request->amount;
        $update_subscription_status = User::find($user_id);
        if($status = 'active'){
            $update_subscription_status->subscription_status = 1;
            $update_subscription_status->save();

            $user_payment = User_payment::create([
                "user_id" => $user_id,
                "subscription_id" => $subscription_id,
                "customer_id" => $customer_id,
                "amount" => $prize,
                "plan_name" => 'Standard',
                "start_date" => $starDate_formate,
                "renewal_date" => $renewal_date,
                "status" => $status,
            ]);
            if($user_payment)
            {
                $user_dob = $update_subscription_status->dob;
                $explodeDOB = explode('-', $user_dob);
                $month = $explodeDOB[1];
                $day = $explodeDOB[2];
                $loginuserjoinyear = $update_subscription_status->created_at;
                $explodejoindate = explode('-', $loginuserjoinyear);
                $joinyear = intval($explodejoindate[0]);
                $joinmonth = intval($explodejoindate[1]);
                $explodedatetime = explode(' ', $explodejoindate[2]);
                $joindateno = intval($explodedatetime[0]);
                $currentdate = $date_current;
                $explodecurrentdate = explode('-', $currentdate);
                $currentdaydate = $explodecurrentdate[2];
                $currentmonth = $explodecurrentdate[1];
                $currentyear = $explodecurrentdate[0];
                for ($y = $currentyear; $y <= $currentyear; $y++) {
                    $favlist = array();
                    $unfavlist = array();
                    for ($m = $currentmonth; $m <= $currentmonth; $m++) {
                        $daysinmonth = cal_days_in_month(0, $m, $y);
                        $favdata = array();
                        $unfavdata = array();

                        if ($update_subscription_status->subscription_status != 0 && $update_subscription_status->subscription_status != 2) {
                            $startday = 1;
                            $no_of_day = $daysinmonth;
                        } else {
                            if ($y == $joinyear && $m == $joinmonth) {
                                $startday = $joindateno;
                            } else {
                                $startday = 1;
                            }

                            if ($y == $currentyear && $m == $currentmonth) {
                                $no_of_day = $currentdaydate;
                            } else {
                                $no_of_day = $daysinmonth;
                            }
                        }
                        $fav_cosmic_stars = 0;
                        $unfav_cosmic_stars = 0;
                        for ($i = $startday; $i <= $no_of_day; $i++) {
                            $fav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 1)
                                ->where('month_id', $month)
                                ->where('date', $day)
                                ->first();
                            $unfav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 2)
                                ->where('month_id', $month)
                                ->where('date', $day)
                                ->first();
                            $fav_dates = $fav->numbers;
                            $fav_dates = str_replace(' ', '', $fav_dates);
                            $fav_dates = explode(',', $fav_dates);
                            $fav_days = $fav->days;
                            $fav_days = str_replace(' ', '', $fav_days);
                            $fav_days = explode(',', $fav_days);
                            $fav_months = $fav->months;
                            $fav_months = str_replace(' ', '', $fav_months);
                            $fav_months = explode(',', $fav_months);

                            $unfav_dates = $unfav->numbers;
                            $unfav_dates = str_replace(' ', '', $unfav_dates);
                            $unfav_dates = explode(',', $unfav_dates);
                            $unfav_days = $unfav->days;
                            $unfav_days = str_replace(' ', '', $unfav_days);
                            $unfav_days = explode(',', $unfav_days);
                            $unfav_months = $unfav->months;
                            $unfav_months = str_replace(' ', '', $unfav_months);
                            $unfav_months = explode(',', $unfav_months);
                            
                            $current_date = explode('-', date("D-j-M-Y", mktime(0, 0, 0, $m, $i, $y)));
                            $date_sum = str_split($i, 1);
                            $date_sum = array_sum($date_sum);
                            if (strlen($date_sum) != 1) {
                                $date_sum = str_split($date_sum);
                                $date_sum = array_sum($date_sum);
                            }

                            $day_star = 0;
                            $month_star = 0;
                            $unfavday_star = 0;
                            $unfavmonth_star = 0;
                            if (in_array($date_sum, $fav_dates)) {
                                $date_star = 1;
                                if (in_array($current_date[0], $fav_days)) {
                                    $day_star = 1;
                                }
                                if (in_array($current_date[2], $fav_months)) {
                                    $month_star = 1;
                                }
                            } else {
                                $date_star = 0;
                            }
                            if (in_array($date_sum, $unfav_dates)) {
                                $unfavdate_star = 1;
                                if (in_array($current_date[0], $unfav_days)) {
                                    $unfavday_star = 1;
                                }
                                if (in_array($current_date[2], $unfav_months)) {
                                    $unfavmonth_star = 1;
                                }
                            } else {
                                $unfavdate_star = 0;
                            }

                            $fav_cosmic_stars = $date_star + $day_star + $month_star;
                            $unfav_cosmic_stars = $unfavdate_star + $unfavday_star + $unfavmonth_star;

                            $favdata['module_type'] = "Fav Star";
                            $favdata['year'] = $y;
                            $favdata['month'] = $m;
                            $favdata['date'] = $i;
                            $favdata['datestar'] = $fav_cosmic_stars;
                            array_push($favlist, $favdata);

                            // $favdatekey[$i] =  $fav_cosmic_stars;
                            $unfavdata['module_type'] = "Unfav Star";
                            $unfavdata['year'] = $y;
                            $unfavdata['month'] = $m;
                            $unfavdata['date'] = $i;
                            $unfavdata['datestar'] = $unfav_cosmic_stars;
                            array_push($unfavlist, $unfavdata);
                            //$unfavdatekey[$i] =  $unfav_cosmic_stars;
                        }
                        $favarraydata = $favlist;
                        $unfavarraydata = $unfavlist;
                    }
                }
                $cosmiccalender = array("favstars" => $favarraydata, "unfavstars" => $unfavarraydata);
                        
            return response()->json([
                    'status' => 1,
                    'message' => 'success',
                    'subscription_status' => $update_subscription_status->subscription_status,
                    'cosmiccalender' => $cosmiccalender,
                ]);
            }else{
                return response()->json([
                    'status' => 0,
                    'message' => 'Something went wrong!',
                ]);
            }
        }else{
            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong!',
            ]);
        }
    }

    public function user_payment_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $user_id = $request->user_id;
        $user = User::find($user_id);
        if($user->subscription_status != 0 && $user->subscription_status != 2)
        {
            $user_payment = User_payment::where('user_id', $user_id)->where('subscription_status','!=', 9)->orWhere('subscription_status','!=', 0)->orderBy('id', 'DESC')->latest()->first();
            if($user_payment != null)
            {
                $current_date = strtotime(date('Y-m-d h:m:s'));
                $renewal_date = strtotime($user_payment->renewal_date);
                if($current_date >= $renewal_date)
                {
                    $subscription_id = $user_payment->subscription_id;
                    $payments = curl_init();
                        curl_setopt_array($payments, array(
                            CURLOPT_URL => "https://api.stripe.com/v1/subscriptions/$subscription_id",
                            CURLOPT_POST => TRUE,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_HTTPHEADER => array(
                                'Authorization: Bearer sk_test_51MTQGcBe0XanjcY6g2qgIOXcMSY9jRMjnq6boSvceAzpwlplQFxBtKpXB8Q8gL81cZepUUwRVxPLMO27lU245GYI00rByUqhdH'
                            )
                        ));
                        $response = curl_exec($payments);
                        if ($response === FALSE) {
                            die(curl_error($payments));
                        }
                        $responseData = json_decode($response, TRUE);
                        $updatRenewal_date = $responseData['current_period_end'];
                        $updatRenewal_dateFormat = date('Y-m-d h:m:s', $updatRenewal_date);
                        $updatStatus = $responseData['status'];
                        $plan = $responseData['plan'];
                        $price = $plan['amount'];
                        if($updatStatus == "active"){
                        $updateuser_payment = User_payment::find($user_payment->id);
                        $updateuser_payment->renewal_date = $updatRenewal_dateFormat;
                        $updateuser_payment->amount = $price;
                        $updateuser_payment->status = $updatStatus;
                        $updateuser_payment->save();
                        }else{
                            return response()->json([
                                'status' => 0,
                                'message' => 'Something went wrong!',
                                'subscription_status' => $user->subscription_status,
                            ]);
                        }
                }
                    $user_paymentDetail = User_payment::where('user_id', $user_id)->where('subscription_status','!=',0)->orderBy('id', 'DESC')->latest()->first();

                    $prize = $user_paymentDetail->amount/100;
                    $paymentData = array("user_id" => $user_paymentDetail->user_id,
                    "subscription_id" => $user_paymentDetail->subscription_id,
                    "customer_id" => $user_paymentDetail->customer_id,
                    "plan_name" => $user_paymentDetail->plan_name,
                    "amount" => $prize,
                    "start_date" => $user_paymentDetail->start_date,
                    "end_date" => $user_paymentDetail->renewal_date,
                    "status" => $user_paymentDetail->status );
                    return response()->json([
                            'status' => 1,
                            'message' => 'success',
                            'subscription_status' => $user->subscription_status,
                            'user_payment_lists' => $paymentData
                        ]);
            }else{
                $user_payment_lists = '';
                return response()->json([
                    'status' => 0,
                    'message' => 'No data found',
                    'subscription_status' => $user->subscription_status,
                    'user_payment_lists' => $user_payment_lists
                ]);
            }
        }else{
            $user_payment_lists = '';
            return response()->json([
                'status' => 0,
                'message' => 'No data found!',
                'subscription_status' => $user->subscription_status,
                'user_payment_lists' => $user_payment_lists
            ]);
        }
    }

    public function cancel_user_subscription(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => 'required',
            "date_current" => 'required',
            "subscription_id" => 'required',
            "status" => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $user_id = $request->user_id;
        $date_current = $request->date_current;
        $subscription_id = $request->subscription_id;
        $cancel_date = $request->cancel_date;
        $cancelDate_format = date('Y-m-d h:m:s', $cancel_date);;
        $status = $request->status;

        $update_subscription_status = User::find($user_id);
        if($status = 'canceled'){
            $update_subscription_status->subscription_status = 0;
            $update_subscription_status->save();
            $user_payment = Cancel_user_subscription::create([
                "user_id" => $user_id,
                "type" => 1,
                "subscription_id" => $subscription_id,
                "type_date" => $cancelDate_format,
            ]);

            if($user_payment)
            {
                $subscriptionDetail = User_payment::where('user_id', $user_id)->where('subscription_id', $subscription_id)->latest()->first();
                if($subscriptionDetail){
                    $deleteSubscriptionDetail = User_payment::find($subscriptionDetail->id);
                    $deleteSubscriptionDetail->delete();
                }else{
                    return response()->json([
                        'status' => 0,
                        'message' => 'Something went wrong!',
                    ]);
                }

                $user_dob = $update_subscription_status->dob;
                $explodeDOB = explode('-', $user_dob);
                $month = $explodeDOB[1];
                $day = $explodeDOB[2];
                $loginuserjoinyear = $update_subscription_status->created_at;
                $explodejoindate = explode('-', $loginuserjoinyear);
                $joinyear = intval($explodejoindate[0]);
                $joinmonth = intval($explodejoindate[1]);
                $explodedatetime = explode(' ', $explodejoindate[2]);
                $joindateno = intval($explodedatetime[0]);
                $currentdate = $date_current;
                $explodecurrentdate = explode('-', $currentdate);
                $currentdaydate = $explodecurrentdate[2];
                $currentmonth = $explodecurrentdate[1];
                $currentyear = $explodecurrentdate[0];
                for ($y = $currentyear; $y <= $currentyear; $y++) {
                    $favlist = array();
                    $unfavlist = array();
                    for ($m = $currentmonth; $m <= $currentmonth; $m++) {
                        $daysinmonth = cal_days_in_month(0, $m, $y);
                        $favdata = array();
                        $unfavdata = array();

                        if ($update_subscription_status->subscription_status != 0 && $update_subscription_status->subscription_status != 2) {
                            $startday = 1;
                            $no_of_day = $daysinmonth;
                        } else {
                            if ($y == $joinyear && $m == $joinmonth) {
                                $startday = $joindateno;
                            } else {
                                $startday = 1;
                            }

                            if ($y == $currentyear && $m == $currentmonth) {
                                $no_of_day = $currentdaydate;
                            } else {
                                $no_of_day = $daysinmonth;
                            }
                        }
                        $fav_cosmic_stars = 0;
                        $unfav_cosmic_stars = 0;
                        for ($i = $startday; $i <= $no_of_day; $i++) {
                            $fav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 1)
                                ->where('month_id', $month)
                                ->where('date', $day)
                                ->first();
                            $unfav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 2)
                                ->where('month_id', $month)
                                ->where('date', $day)
                                ->first();
                            $fav_dates = $fav->numbers;
                            $fav_dates = str_replace(' ', '', $fav_dates);
                            $fav_dates = explode(',', $fav_dates);
                            $fav_days = $fav->days;
                            $fav_days = str_replace(' ', '', $fav_days);
                            $fav_days = explode(',', $fav_days);
                            $fav_months = $fav->months;
                            $fav_months = str_replace(' ', '', $fav_months);
                            $fav_months = explode(',', $fav_months);

                            $unfav_dates = $unfav->numbers;
                            $unfav_dates = str_replace(' ', '', $unfav_dates);
                            $unfav_dates = explode(',', $unfav_dates);
                            $unfav_days = $unfav->days;
                            $unfav_days = str_replace(' ', '', $unfav_days);
                            $unfav_days = explode(',', $unfav_days);
                            $unfav_months = $unfav->months;
                            $unfav_months = str_replace(' ', '', $unfav_months);
                            $unfav_months = explode(',', $unfav_months);
                            
                            $current_date = explode('-', date("D-j-M-Y", mktime(0, 0, 0, $m, $i, $y)));
                            $date_sum = str_split($i, 1);
                            $date_sum = array_sum($date_sum);
                            if (strlen($date_sum) != 1) {
                                $date_sum = str_split($date_sum);
                                $date_sum = array_sum($date_sum);
                            }

                            $day_star = 0;
                            $month_star = 0;
                            $unfavday_star = 0;
                            $unfavmonth_star = 0;
                            if (in_array($date_sum, $fav_dates)) {
                                $date_star = 1;
                                if (in_array($current_date[0], $fav_days)) {
                                    $day_star = 1;
                                }
                                if (in_array($current_date[2], $fav_months)) {
                                    $month_star = 1;
                                }
                            } else {
                                $date_star = 0;
                            }
                            if (in_array($date_sum, $unfav_dates)) {
                                $unfavdate_star = 1;
                                if (in_array($current_date[0], $unfav_days)) {
                                    $unfavday_star = 1;
                                }
                                if (in_array($current_date[2], $unfav_months)) {
                                    $unfavmonth_star = 1;
                                }
                            } else {
                                $unfavdate_star = 0;
                            }

                            $fav_cosmic_stars = $date_star + $day_star + $month_star;
                            $unfav_cosmic_stars = $unfavdate_star + $unfavday_star + $unfavmonth_star;

                            $favdata['module_type'] = "Fav Star";
                            $favdata['year'] = $y;
                            $favdata['month'] = $m;
                            $favdata['date'] = $i;
                            $favdata['datestar'] = $fav_cosmic_stars;
                            array_push($favlist, $favdata);

                            // $favdatekey[$i] =  $fav_cosmic_stars;
                            $unfavdata['module_type'] = "Unfav Star";
                            $unfavdata['year'] = $y;
                            $unfavdata['month'] = $m;
                            $unfavdata['date'] = $i;
                            $unfavdata['datestar'] = $unfav_cosmic_stars;
                            array_push($unfavlist, $unfavdata);
                            //$unfavdatekey[$i] =  $unfav_cosmic_stars;
                        }
                        $favarraydata = $favlist;
                        $unfavarraydata = $unfavlist;
                    }
                }
                $cosmiccalender = array("favstars" => $favarraydata, "unfavstars" => $unfavarraydata);
                        
                return response()->json([
                        'status' => 1,
                        'message' => 'success',
                        'subscription_status' => $update_subscription_status->subscription_status,
                        'cosmiccalender' => $cosmiccalender,
                    ]);
            }else{
                return response()->json([
                    'status' => 0,
                    'message' => 'Something went wrong!',
                ]);
            }
        }else{
            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong!',
            ]);
        }
    }

    public function paused_user_subscription(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => 'required',
            "subscription_id" => 'required',
            "status" => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        
        $user_id = $request->user_id;
        $subscription_id = $request->subscription_id;
        $paused_date = $request->paused_date;
        $pausedDate_format = date('Y-m-d h:m:s', $paused_date);
        $status = $request->status;
        $update_subscription_status = User::find($user_id);
        if($status = 'paused'){
            $update_subscription_status->subscription_status = 2;
            $update_subscription_status->save();
        
            $subscriptionDetail = User_payment::where('user_id', $user_id)->where('subscription_id', $subscription_id)->latest()->first();
            if($subscriptionDetail )
            {
                $deleteSubscriptionDetail = User_payment::find($subscriptionDetail->id);
                $deleteSubscriptionDetail->subscription_status = 2;
                $deleteSubscriptionDetail->paused_date = $pausedDate_format;
                $deleteSubscriptionDetail->save();
                $user_payment = Cancel_user_subscription::create([
                    "user_id" => $user_id,
                    "type" => 2,
                    "subscription_id" => $subscription_id,
                    "type_date" => $pausedDate_format,
                ]);
                return response()->json([
                        'status' => 1,
                        'message' => 'success',
                        'subscription_status' => $update_subscription_status->subscription_status,
                    ]);
            }else{
                return response()->json([
                    'status' => 0,
                    'message' => 'Something went wrong!',
                ]);
            }
        }else{
            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong!',
            ]);
        }
    }

    public function resume_user_subscription(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => 'required',
            "subscription_id" => 'required',
            "status" => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $user_id = $request->user_id;
        $subscription_id = $request->subscription_id;
        $status = $request->status;
        $resume_date = $request->resume_date;
        $resumeDate_format = date('Y-m-d h:m:s', $resume_date);
        $update_subscription_status = User::find($user_id);
        if($status = 'active'){
            $update_subscription_status->subscription_status = 3;
            $update_subscription_status->save();
        
            $subscriptionDetail = User_payment::where('user_id', $user_id)->where('subscription_id', $subscription_id)->latest()->first();
            if($subscriptionDetail)
            {
                $deleteSubscriptionDetail = User_payment::find($subscriptionDetail->id);
                $deleteSubscriptionDetail->subscription_status = 3;
                $deleteSubscriptionDetail->save();
                $user_payment = Cancel_user_subscription::create([
                    "user_id" => $user_id,
                    "type" => 3,
                    "subscription_id" => $subscription_id,
                    "type_date" => $resumeDate_format,
                ]);
                return response()->json([
                        'status' => 1,
                        'message' => 'success',
                        'subscription_status' => $update_subscription_status->subscription_status,
                    ]);
            }else{
                return response()->json([
                    'status' => 0,
                    'message' => 'Something went wrong!',
                ]);
            }
        }else{
            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong!',
            ]);
        }
    }

    public function stripemode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "data" => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $data = $request->data;
        if($data == 'all')
        {
            $stripemode = Stripemode::where('current_status', 1)->latest()->first();
            if($stripemode->modes_type == 1)
            {
                $status = 'Test';
            }else{
                $status = 'Live';
            }
            $stripemodeData = array('currentmode'=>$status, 'secret_key' =>$stripemode->secret_key, 'publish_key'=> $stripemode->publish_key);
            return response()->json([
                'status' => 1,
                'message' => 'success',
                'current_stripe_status' => $status,
                'stripemode_data' => $stripemodeData,
            ]);
        }else
        {
            return response()->json([
                'status' => 0,
                'message' => 'Error',
            ]);
        }
    }

    public function cancelspecialbscription(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => 'required',
            "current_date" => 'required',
            "date_current" => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $userid = $request->user_id;
        $current_date = $request->current_date;
        $date_current = $request->date_current;
        $format_date = strtotime($date_current);
        $user = User::find($userid);
        if($user->subscription_status == 9)
        {
            $user->subscription_status = 0;
            $user->save();

            $userpayment = User_payment::where('user_id', $user->id)->where('status', 'ByAdmin')->latest()->first();
            if($userpayment)
            {
                $updatePaymentData = User_payment::find($userpayment->id);
                $updatePaymentData->delete();

                $date_current_formate = date('Y-m-d h:m:s', $format_date);
                $cancelSub = Cancel_user_subscription::create([
                    'user_id' => $user->id,
                    'type' => 9,
                    'subscription_id' => 'ByAdmin',
                    'type_date' => $date_current_formate,
                ]);
                $user_dob = $user->dob;
                $explodeDOB = explode('-', $user_dob);
                $month = $explodeDOB[1];
                $day = $explodeDOB[2];
                $loginuserjoinyear = $user->created_at;
                $explodejoindate = explode('-', $loginuserjoinyear);
                $joinyear = intval($explodejoindate[0]);
                $joinmonth = intval($explodejoindate[1]);
                $explodedatetime = explode(' ', $explodejoindate[2]);
                $joindateno = intval($explodedatetime[0]);
                $currentdate = $current_date;
                $explodecurrentdate = explode('-', $currentdate);
                $currentdaydate = $explodecurrentdate[2];
                $currentmonth = $explodecurrentdate[1];
                $currentyear = $explodecurrentdate[0];
                for ($y = $currentyear; $y <= $currentyear; $y++) {
                    $favlist = array();
                    $unfavlist = array();
                    for ($m = $currentmonth; $m <= $currentmonth; $m++) {
                        $daysinmonth = cal_days_in_month(0, $m, $y);
                        $favdata = array();
                        $unfavdata = array();

                        if ($user->subscription_status != 0 && $user->subscription_status != 2) {
                            $startday = 1;
                            $no_of_day = $daysinmonth;
                        } else {
                            if ($y == $joinyear && $m == $joinmonth) {
                                $startday = $joindateno;
                            } else {
                                $startday = 1;
                            }

                            if ($y == $currentyear && $m == $currentmonth) {
                                $no_of_day = $currentdaydate;
                            } else {
                                $no_of_day = $daysinmonth;
                            }
                        }
                        $fav_cosmic_stars = 0;
                        $unfav_cosmic_stars = 0;
                        for ($i = $startday; $i <= $no_of_day; $i++) {
                            $fav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 1)
                                ->where('month_id', $month)
                                ->where('date', $day)
                                ->first();
                            $unfav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 2)
                                ->where('month_id', $month)
                                ->where('date', $day)
                                ->first();
                            $fav_dates = $fav->numbers;
                            $fav_dates = str_replace(' ', '', $fav_dates);
                            $fav_dates = explode(',', $fav_dates);
                            $fav_days = $fav->days;
                            $fav_days = str_replace(' ', '', $fav_days);
                            $fav_days = explode(',', $fav_days);
                            $fav_months = $fav->months;
                            $fav_months = str_replace(' ', '', $fav_months);
                            $fav_months = explode(',', $fav_months);

                            $unfav_dates = $unfav->numbers;
                            $unfav_dates = str_replace(' ', '', $unfav_dates);
                            $unfav_dates = explode(',', $unfav_dates);
                            $unfav_days = $unfav->days;
                            $unfav_days = str_replace(' ', '', $unfav_days);
                            $unfav_days = explode(',', $unfav_days);
                            $unfav_months = $unfav->months;
                            $unfav_months = str_replace(' ', '', $unfav_months);
                            $unfav_months = explode(',', $unfav_months);
                            
                            $current_date = explode('-', date("D-j-M-Y", mktime(0, 0, 0, $m, $i, $y)));
                            $date_sum = str_split($i, 1);
                            $date_sum = array_sum($date_sum);
                            if (strlen($date_sum) != 1) {
                                $date_sum = str_split($date_sum);
                                $date_sum = array_sum($date_sum);
                            }

                            $day_star = 0;
                            $month_star = 0;
                            $unfavday_star = 0;
                            $unfavmonth_star = 0;
                            if (in_array($date_sum, $fav_dates)) {
                                $date_star = 1;
                                if (in_array($current_date[0], $fav_days)) {
                                    $day_star = 1;
                                }
                                if (in_array($current_date[2], $fav_months)) {
                                    $month_star = 1;
                                }
                            } else {
                                $date_star = 0;
                            }
                            if (in_array($date_sum, $unfav_dates)) {
                                $unfavdate_star = 1;
                                if (in_array($current_date[0], $unfav_days)) {
                                    $unfavday_star = 1;
                                }
                                if (in_array($current_date[2], $unfav_months)) {
                                    $unfavmonth_star = 1;
                                }
                            } else {
                                $unfavdate_star = 0;
                            }

                            $fav_cosmic_stars = $date_star + $day_star + $month_star;
                            $unfav_cosmic_stars = $unfavdate_star + $unfavday_star + $unfavmonth_star;

                            $favdata['module_type'] = "Fav Star";
                            $favdata['year'] = $y;
                            $favdata['month'] = $m;
                            $favdata['date'] = $i;
                            $favdata['datestar'] = $fav_cosmic_stars;
                            array_push($favlist, $favdata);

                            // $favdatekey[$i] =  $fav_cosmic_stars;
                            $unfavdata['module_type'] = "Unfav Star";
                            $unfavdata['year'] = $y;
                            $unfavdata['month'] = $m;
                            $unfavdata['date'] = $i;
                            $unfavdata['datestar'] = $unfav_cosmic_stars;
                            array_push($unfavlist, $unfavdata);
                            //$unfavdatekey[$i] =  $unfav_cosmic_stars;
                        }
                        $favarraydata = $favlist;
                        $unfavarraydata = $unfavlist;
                    }
                }
                $cosmiccalender = array("favstars" => $favarraydata, "unfavstars" => $unfavarraydata);
                return response()->json([
                    'status' => 1,
                    'message' => 'success',
                    'subscription_status' => $user->subscription_status,
                    'cosmiccalender' => $cosmiccalender,
                ]);   
            }else
            {
                return response()->json([
                    'status' => 0,
                    'message' => 'Something went wrong!',
                ]);
            }
        }else{
            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong!',
            ]);
        }
    }

    public function deleteuser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $userid = $request->user_id;
        $userData = User::find($userid);
        if($userData)
        {
            $userDataUpdate = User::find($userData->id);
            $userDataUpdate->email = $userData->email."-".$userData->id;
            $userDataUpdate->is_active = 2;
            $userDataUpdate->update();

            $model_has_role = model_has_role::where('model_id', $userData->id)->delete();

            return response()->json([
                'status' => 1,
                'message' => 'Success',
            ]);
        }else{
            return response()->json([
                'status' => 0,
                'message' => 'User not found',
            ]);
        }
    }

    public function newuser_payments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => 'required',
            "date_current" => 'required',
            "status" => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $user_id = $request->user_id;
        $date_current = $request->date_current;
        $subscription_id = $request->subscription_id;
        $customer_id = $request->customer_id;
        $start_date = $request->start_date;
        $starDate_formate = date('Y-m-d h:m:s', $start_date);
        $end_date = strtotime("+1 month", strtotime($starDate_formate));
        $renewal_date = date('Y-m-d h:m:s', $end_date);
        $status = $request->status;
        $prize = $request->amount;
        $update_subscription_status = User::find($user_id);
        if($status = 'succeeded'){
            $update_subscription_status->subscription_status = 1;
            $update_subscription_status->save();

            $user_payment = User_payment::create([
                "user_id" => $user_id,
                "subscription_id" => $subscription_id,
                "customer_id" => $customer_id,
                "amount" => $prize,
                "plan_name" => 'Standard',
                "start_date" => $starDate_formate,
                "renewal_date" => $renewal_date,
                "status" => $status,
            ]);
            if($user_payment)
            {
                $user_dob = $update_subscription_status->dob;
                $explodeDOB = explode('-', $user_dob);
                $month = $explodeDOB[1];
                $day = $explodeDOB[2];
                $loginuserjoinyear = $update_subscription_status->created_at;
                $explodejoindate = explode('-', $loginuserjoinyear);
                $joinyear = intval($explodejoindate[0]);
                $joinmonth = intval($explodejoindate[1]);
                $explodedatetime = explode(' ', $explodejoindate[2]);
                $joindateno = intval($explodedatetime[0]);
                $currentdate = $date_current;
                $explodecurrentdate = explode('-', $currentdate);
                $currentdaydate = $explodecurrentdate[2];
                $currentmonth = $explodecurrentdate[1];
                $currentyear = $explodecurrentdate[0];
                for ($y = $currentyear; $y <= $currentyear; $y++) {
                    $favlist = array();
                    $unfavlist = array();
                    for ($m = $currentmonth; $m <= $currentmonth; $m++) {
                        $daysinmonth = cal_days_in_month(0, $m, $y);
                        $favdata = array();
                        $unfavdata = array();

                        if ($update_subscription_status->subscription_status != 0 && $update_subscription_status->subscription_status != 2) {
                            $startday = 1;
                            $no_of_day = $daysinmonth;
                        } else {
                            if ($y == $joinyear && $m == $joinmonth) {
                                $startday = $joindateno;
                            } else {
                                $startday = 1;
                            }

                            if ($y == $currentyear && $m == $currentmonth) {
                                $no_of_day = $currentdaydate;
                            } else {
                                $no_of_day = $daysinmonth;
                            }
                        }
                        $fav_cosmic_stars = 0;
                        $unfav_cosmic_stars = 0;
                        for ($i = $startday; $i <= $no_of_day; $i++) {
                            $fav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 1)
                                ->where('month_id', $month)
                                ->where('date', $day)
                                ->first();
                            $unfav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 2)
                                ->where('month_id', $month)
                                ->where('date', $day)
                                ->first();
                            $fav_dates = $fav->numbers;
                            $fav_dates = str_replace(' ', '', $fav_dates);
                            $fav_dates = explode(',', $fav_dates);
                            $fav_days = $fav->days;
                            $fav_days = str_replace(' ', '', $fav_days);
                            $fav_days = explode(',', $fav_days);
                            $fav_months = $fav->months;
                            $fav_months = str_replace(' ', '', $fav_months);
                            $fav_months = explode(',', $fav_months);

                            $unfav_dates = $unfav->numbers;
                            $unfav_dates = str_replace(' ', '', $unfav_dates);
                            $unfav_dates = explode(',', $unfav_dates);
                            $unfav_days = $unfav->days;
                            $unfav_days = str_replace(' ', '', $unfav_days);
                            $unfav_days = explode(',', $unfav_days);
                            $unfav_months = $unfav->months;
                            $unfav_months = str_replace(' ', '', $unfav_months);
                            $unfav_months = explode(',', $unfav_months);
                            
                            $current_date = explode('-', date("D-j-M-Y", mktime(0, 0, 0, $m, $i, $y)));
                            $date_sum = str_split($i, 1);
                            $date_sum = array_sum($date_sum);
                            if (strlen($date_sum) != 1) {
                                $date_sum = str_split($date_sum);
                                $date_sum = array_sum($date_sum);
                            }

                            $day_star = 0;
                            $month_star = 0;
                            $unfavday_star = 0;
                            $unfavmonth_star = 0;
                            if (in_array($date_sum, $fav_dates)) {
                                $date_star = 1;
                                if (in_array($current_date[0], $fav_days)) {
                                    $day_star = 1;
                                }
                                if (in_array($current_date[2], $fav_months)) {
                                    $month_star = 1;
                                }
                            } else {
                                $date_star = 0;
                            }
                            if (in_array($date_sum, $unfav_dates)) {
                                $unfavdate_star = 1;
                                if (in_array($current_date[0], $unfav_days)) {
                                    $unfavday_star = 1;
                                }
                                if (in_array($current_date[2], $unfav_months)) {
                                    $unfavmonth_star = 1;
                                }
                            } else {
                                $unfavdate_star = 0;
                            }

                            $fav_cosmic_stars = $date_star + $day_star + $month_star;
                            $unfav_cosmic_stars = $unfavdate_star + $unfavday_star + $unfavmonth_star;

                            $favdata['module_type'] = "Fav Star";
                            $favdata['year'] = $y;
                            $favdata['month'] = $m;
                            $favdata['date'] = $i;
                            $favdata['datestar'] = $fav_cosmic_stars;
                            array_push($favlist, $favdata);

                            // $favdatekey[$i] =  $fav_cosmic_stars;
                            $unfavdata['module_type'] = "Unfav Star";
                            $unfavdata['year'] = $y;
                            $unfavdata['month'] = $m;
                            $unfavdata['date'] = $i;
                            $unfavdata['datestar'] = $unfav_cosmic_stars;
                            array_push($unfavlist, $unfavdata);
                            //$unfavdatekey[$i] =  $unfav_cosmic_stars;
                        }
                        $favarraydata = $favlist;
                        $unfavarraydata = $unfavlist;
                    }
                }
                $cosmiccalender = array("favstars" => $favarraydata, "unfavstars" => $unfavarraydata);
                        
            return response()->json([
                    'status' => 1,
                    'message' => 'success',
                    'subscription_status' => $update_subscription_status->subscription_status,
                    'cosmiccalender' => $cosmiccalender,
                ]);
            }else{
                return response()->json([
                    'status' => 0,
                    'message' => 'Something went wrong!',
                ]);
            }
        }else{
            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong!',
            ]);
        }
    }
}
