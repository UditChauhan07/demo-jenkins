<?php

namespace App\Http\Controllers;

use App\Models\Daily_prediction;
use App\Models\Fav_unfav_parameter;
use App\Models\model_has_role;
use App\Models\Role;
use App\Models\User;
use App\Models\User_compatiblecheck;
use App\Models\User_prediction;
use App\Models\Useronboarding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $allusers = model_has_role::where('role_id', '=', 23)->with('user', 'userprofile')->get();
        // return $allusers;
        $activeusers = array();
        $inactiveusers = array();
        foreach ($allusers as $alluser) {
            if ($alluser->userprofile->overall_status == 1) {
                $activeuser = $alluser->user;
                array_push($activeusers, $activeuser);
            } else {
                $inactiveuser = $alluser->user;
                array_push($inactiveusers, $inactiveuser);
            }
        }
        $alluserCount = count($allusers);
        $activeuserCount = count($activeusers);
        $inactiveuserCount = count($inactiveusers);
        $competeuserprofiles = model_has_role::where('role_id', '=', 23)->with('user', 'userprofile')->get();
        $totalusers = count($competeuserprofiles);
        $datelist = array();
        $greenstarlist = array();
        $redstarlist = array();
        $neutrallist = array();

        for($d=0; $d<=6; $d++){
            $greenstarUser = array();
            $redstarUser = array();
            $neutraldates = array();
            $lasttenthDate = date( "D-j-M-Y", strtotime('monday this week'. '+'. $d .' days' ) );
            // return $lasttenthDate;
            $filterDate = date("M j, Y", strtotime('monday this week'. '+' . $d . ' days'));
            if($filterDate <= date("M j, Y")){
                foreach ($competeuserprofiles as $competeuserprofile) {
                    if ($competeuserprofile->userprofile->overall_status == 1) {
                        $current_date = $lasttenthDate;
                        $userdob = $competeuserprofile->user->dob;
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
                        $day_star = 0;
                        $month_star = 0;
                        if (in_array($date_sum, $fav_dates)) {
                            $date_star = 1;
                        } else {
                            $date_star = 0;
                        }
                        if (in_array($currentdateformate[0], $fav_days)) {
                            $day_star = 1;
                        } else {
                            $day_star = 0;
                        }
                        if (in_array($currentdateformate[2], $fav_months)) {
                            $month_star = 1;
                        } else {
                            $month_star = 0;
                        }

                        $unfavday_star = 0;
                        $unfavmonth_star = 0;
                        if (in_array($date_sum, $unfav_dates)) {
                            $unfavdate_star = 1;
                        } else {
                            $unfavdate_star = 0;
                        }
                        if (in_array($currentdateformate[0], $unfav_days)) {
                            $unfavday_star = 1;
                        } else {
                            $unfavday_star = 0;
                        }
                        if (in_array($currentdateformate[2], $unfav_months)) {
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

                        $currentdayfav_star = $fav_cosmic_stars;
                        $currentdayunfav_star = $unfav_cosmic_stars;
                    }
                    if ($currentdayfav_star != 0) {
                        array_push($greenstarUser, $currentdayfav_star);
                    } elseif ($currentdayunfav_star != 0) {
                        array_push($redstarUser, $currentdayunfav_star);
                    } else {
                        array_push($neutraldates, $currentdayfav_star);
                    }
                }
                $greenstarUserCount = count($greenstarUser);
                $redstarUserCount = count($redstarUser);
                $neutralUserCount = count($neutraldates);
            }else{
                $greenstarUserCount = 0;
                $redstarUserCount = 0;
                $neutralUserCount = 0;
            }
            array_push($datelist, $filterDate);
            array_push($greenstarlist, $greenstarUserCount);
            array_push($redstarlist, $redstarUserCount);
            array_push($neutrallist, $neutralUserCount);
        }

        $prediction_data = Daily_prediction::where('publish_status', '=', 1)->orderBy('id', 'DESC')->first();
        $likePrediction_data = User_prediction::where('dailyprediction_id', '=', $prediction_data->id)->where('is_like', '=', '1')->get();
        $dislikePrediction_data = User_prediction::where('dailyprediction_id', '=', $prediction_data->id)->where('is_like', '=', '2')->get();
        $like_preditionCount = count($likePrediction_data);
        $dislike_preditionCount = count($dislikePrediction_data);

        $filter_date = array();
        $carcompCheckCounts = array();
        $businesscompCheckCounts = array();
        $propertycompCheckCounts = array();
        $relationcompCheckCounts = array();
        $otherpersioncompCheckCounts = array();

        for ($check_d = 0; $check_d <= 6; $check_d++) {
            $filterDates = date( "Y-m-d", strtotime('monday this week'. '+'. $check_d .' days' ) );
            $filter_dates = date( "M d, Y", strtotime('monday this week'. '+'. $check_d .' days' ) );
            $carcompatibilitychecks = User_compatiblecheck::where('type', '=', 1)->whereDate('created_at', $filterDates)->get();
            // return $carcompatibilitychecks;
            $carcompCheckCount = count($carcompatibilitychecks);
            array_push($carcompCheckCounts, $carcompCheckCount);
            $businesscompatibilitychecks = User_compatiblecheck::where('type', '=', 3)->whereDate('created_at', $filterDates)->get();
            $businesscompCheckCount = count($businesscompatibilitychecks);
            array_push($businesscompCheckCounts, $businesscompCheckCount);
            $propertycompatibilitychecks = User_compatiblecheck::where('type', '=', 4)->whereDate('created_at', $filterDates)->get();
            $propertycompCheckCount = count($propertycompatibilitychecks);
            array_push($propertycompCheckCounts, $propertycompCheckCount);
            $relationcompatibilitychecks = User_compatiblecheck::where('type', '=', 6)->whereDate('created_at', $filterDates)->get();
            $relationcompCheckCount = count($relationcompatibilitychecks);
            array_push($relationcompCheckCounts, $relationcompCheckCount);
            $otherpersioncompatibilitychecks = User_compatiblecheck::where('type', '=', 2)->whereDate('created_at', $filterDates)->get();
            $otherpersioncompCheckCount = count($otherpersioncompatibilitychecks);
            array_push($otherpersioncompCheckCounts, $otherpersioncompCheckCount);
            array_push($filter_date, $filter_dates);
        }

        // Chat user count
        
        return view('dashboard', compact(
            'alluserCount',
            'activeuserCount',
            'inactiveuserCount',
            'datelist',
            'greenstarlist',
            'redstarlist',
            'neutrallist',
            'like_preditionCount',
            'dislike_preditionCount',
            'carcompCheckCounts',
            'businesscompCheckCounts',
            'propertycompCheckCounts',
            'relationcompCheckCounts',
            'otherpersioncompCheckCounts',
            'filter_date',
            'totalusers',
        ));
    }

    public function favunfavdatafilter(Request $request)
    {
        $favunfavdays = $request->favunfavdates;
        $competeuserprofiles = model_has_role::where('role_id', '=', 23)->with('user', 'userprofile')->get();
        $datelist = array();
        $greenstarlist = array();
        $redstarlist = array();
        $neutrallist = array();

        if($favunfavdays == 'week'){
            $filter = 6;
        }elseif($favunfavdays == 'month'){
            $currentdate = date('t');
            $filter = $currentdate - 1;
        }
        for ($d=0; $d<=$filter; $d++) {
            $greenstarUser = array();
            $redstarUser = array();
            $neutraldates = array();
            if($favunfavdays == 'week'){
            $lasttenthDate = date("D-j-M-Y", strtotime('monday this week'.'+' . $d . ' days'));
            $filterDate = date("M j, Y", strtotime('monday this week'.'+' . $d . ' days'));
            $filterd = date("d/m/Y", strtotime('monday this week'.'+' . $d . ' days'));
        }elseif($favunfavdays == 'month'){
            $firstdate = date("Y-m-1");
            $lasttenthDate = date("D-j-M-Y", strtotime($firstdate.'+' . $d . ' days'));
            $filterDate = date("M j, Y", strtotime($firstdate.'+' . $d . ' days'));
            $filterd = date("d/m/Y", strtotime($firstdate.'+' . $d . ' days'));
        }

        if($filterd <= date("d/m/Y")){
            foreach ($competeuserprofiles as $competeuserprofile) {
                if ($competeuserprofile->userprofile->overall_status == 1) {
                    $current_date = $lasttenthDate;
                    $userdob = $competeuserprofile->user->dob;
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
                    $day_star = 0;
                    $month_star = 0;
                    if (in_array($date_sum, $fav_dates)) {
                        $date_star = 1;
                    } else {
                        $date_star = 0;
                    }
                    if (in_array($currentdateformate[0], $fav_days)) {
                        $day_star = 1;
                    } else {
                        $day_star = 0;
                    }
                    if (in_array($currentdateformate[2], $fav_months)) {
                        $month_star = 1;
                    } else {
                        $month_star = 0;
                    }

                    $unfavday_star = 0;
                    $unfavmonth_star = 0;
                    if (in_array($date_sum, $unfav_dates)) {
                        $unfavdate_star = 1;
                    } else {
                        $unfavdate_star = 0;
                    }
                    if (in_array($currentdateformate[0], $unfav_days)) {
                        $unfavday_star = 1;
                    } else {
                        $unfavday_star = 0;
                    }
                    if (in_array($currentdateformate[2], $unfav_months)) {
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

                    $currentdayfav_star = $fav_cosmic_stars;
                    $currentdayunfav_star = $unfav_cosmic_stars;
                }
                if ($currentdayfav_star != 0) {
                    array_push($greenstarUser, $currentdayfav_star);
                } elseif ($currentdayunfav_star != 0) {
                    array_push($redstarUser, $currentdayunfav_star);
                } else {
                    array_push($neutraldates, $currentdayfav_star);
                }
            }
            $greenstarUserCount = count($greenstarUser);
            $redstarUserCount = count($redstarUser);
            $neutralUserCount = count($neutraldates);
        }else{
            $greenstarUserCount = 0;
            $redstarUserCount = 0;
            $neutralUserCount = 0;
        }
            array_push($datelist, $filterDate);
            array_push($greenstarlist, $greenstarUserCount);
            array_push($redstarlist, $redstarUserCount);
            array_push($neutrallist, $neutralUserCount);
        }
        return response()->json(['status'=>'success', 'datelist'=>$datelist,
        'greenstarlist'=> $greenstarlist,
        'redstarlist'=>$redstarlist,
        'neutrallist'=>$neutrallist,]);
    }

    public function compatibilityfilter(Request $request)
    {
        $compatibilityfilter = $request->compdays;
        $dates = array();
        $carcompCheckCounts = array();
        $businesscompCheckCounts = array();
        $propertycompCheckCounts = array();
        $relationcompCheckCounts = array();
        $otherpersioncompCheckCounts = array();

        if($compatibilityfilter == 'week'){
            $filter = 6;
        }elseif($compatibilityfilter == 'month'){
            $maxDays=date('t');
            $filter = $maxDays - 1;
        }else{
            return response()->json(['status'=>'success', 'message'=> 'Error']);
        }

        for ($check_d = 0; $check_d <= $filter; $check_d++) {
            if($compatibilityfilter == 'week'){
                $filterDates = date("Y-m-d", strtotime('monday this week'.'+' . $check_d . ' days'));
                $filter_dates = date("d M, Y", strtotime('monday this week'.'+' . $check_d . ' days'));
            }elseif($compatibilityfilter == 'month'){
                $firstdate = date("Y-m-1");
                $filterDates = date("Y-m-d", strtotime($firstdate. '+' . $check_d . ' days'));
                $filter_dates = date("d M, Y", strtotime($firstdate. '+' . $check_d . ' days'));
            }else{
                $filterDates = date('Y-m-d');
                $filter_dates = date("d m, Y");
            }
            $carcompatibilitychecks = User_compatiblecheck::where('type', '=', 1)->whereDate('created_at', $filterDates)->get();
            // return $carcompatibilitychecks;
            $carcompCheckCount = count($carcompatibilitychecks);
            array_push($carcompCheckCounts, $carcompCheckCount);
            $businesscompatibilitychecks = User_compatiblecheck::where('type', '=', 3)->whereDate('created_at', $filterDates)->get();
            $businesscompCheckCount = count($businesscompatibilitychecks);
            array_push($businesscompCheckCounts, $businesscompCheckCount);
            $propertycompatibilitychecks = User_compatiblecheck::where('type', '=', 4)->whereDate('created_at', $filterDates)->get();
            $propertycompCheckCount = count($propertycompatibilitychecks);
            array_push($propertycompCheckCounts, $propertycompCheckCount);
            $relationcompatibilitychecks = User_compatiblecheck::where('type', '=', 6)->whereDate('created_at', $filterDates)->get();
            $relationcompCheckCount = count($relationcompatibilitychecks);
            array_push($relationcompCheckCounts, $relationcompCheckCount);
            $otherpersioncompatibilitychecks = User_compatiblecheck::where('type', '=', 2)->whereDate('created_at', $filterDates)->get();
            $otherpersioncompCheckCount = count($otherpersioncompatibilitychecks);
            array_push($otherpersioncompCheckCounts, $otherpersioncompCheckCount);
            array_push($dates, $filter_dates);
        } 
        return response()->json(['status'=>'success', 'dates' => $dates, 'carcompCheckCounts' => $carcompCheckCounts, 'businesscompCheckCounts' => $businesscompCheckCounts,
         'propertycompCheckCounts'=>$propertycompCheckCounts, 'relationcompCheckCounts'=> $relationcompCheckCounts, 'otherpersioncompCheckCounts'=> $otherpersioncompCheckCounts]);
    }

    public function profile()
    {
        $profileData = User::find(Auth::user()->id);
        $role = model_has_role::where('model_id', Auth::user()->id)->first();
        $rolename = Role::find($role->role_id);
        return view('profile', compact('profileData','rolename'));
    }

    public function updateprofile(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
        ]);
        $name = $request->name;
        $email = $request->email;

        $user = User::find($id);
        $user->name = $name;
        $user->email = $email;
        $user->save();
        if($user)
        return redirect()->route('profile')
                        ->with('success','Profile updated successfully');
    }

    public function updateprofilepic(Request $request)
    {
        $userid = Auth::user()->id;
        $profilepic = $request->profile_pic;
        if ($profilepic) {
            $destinationPath = public_path() . '/profile_pic';
            $safeName = \Str::random(12) . time() . '.' . $profilepic->getClientOriginalExtension();
            $profilepic->move($destinationPath, $safeName);
            $new_profilepic_name = $safeName;
        }

        $save_profilepic = User::find($userid);
        $save_profilepic->profile_pic = $new_profilepic_name;
        $save_profilepic->save();

        if ($save_profilepic) {
            return redirect()->route('profile')
                        ->with('success','Profile image updated successfully');
        }
    }

    public function see()
    {
        return view('create');
    }

    public function changepassword()
    {
        return view('changepassword');
    }

    public function updatepassword(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required',
            'password' => 'required|confirmed',
        ]);
        if (!Hash::check($request->old_password, auth()->user()->password)) {
            return back()->with('error', 'old-password does not match!');
        }
        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('home')->with('success', 'Password changed successfully');
    }
}
