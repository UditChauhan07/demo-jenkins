<?php

namespace App\Http\Controllers;

use App\Models\Fav_unfav_parameter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CosmiccellenderController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:cosmiccalender-list|cosmiccalender-create|cosmiccalender-edit|cosmiccalender-delete', ['only' => ['index','store']]);
         $this->middleware('permission:cosmiccalender-create', ['only' => ['create','store']]);
         $this->middleware('permission:cosmiccalender-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:cosmiccalender-delete', ['only' => ['destroy']]);
    }

    public function cosmiccalender()
    {
        return view('cosmiccalender');
    }

    public function cosmicPublic(Request $request)   
    {
        // $user = User::find(Auth::user()->id);
        // $date = explode('-', $user->dob);
        $dob = $request->dob;
        if($dob){
        $date = explode('-', $dob);
        $birth_date = $date[2];
        $birth_month = $date[1];
        $month = 10;
        $year = 2022;
        $date_fav = cal_days_in_month(0, $month, $year);
        $favdatekey = array();
        $offsets = null;
        for ($i = 1; $i <= $date_fav; $i++) {
            $fav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 1)
                ->where('month_id', $birth_month)
                ->where('date', $birth_date)
                ->first();
            $unfav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 2)
                ->where('month_id', $birth_month)
                ->where('date', $birth_date)
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
            $start_date =  mktime(0, 0, 0, $month, $i, $year);
            $current_date = explode('-', date("D-j-M-Y", mktime(0, 0, 0, $month, $i, $year)));
            if ($i == 1) {

                $offsets = date("w", $start_date);
            }
            $date_sum = str_split($current_date[1], 1);
            $date_sum = array_sum($date_sum);
            if (strlen($date_sum) != 1) {
                $date_sum = str_split($date_sum);
                $date_sum = array_sum($date_sum);
            }
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

            $fav_cosmic_stars = [$date_star, $day_star, $month_star];
            $favdatekey[$i] =  $fav_cosmic_stars;

            $unfav_cosmic_stars = [$unfavdate_star, $unfavday_star, $unfavmonth_star];
            $unfavdatekey[$i] =  $unfav_cosmic_stars;
        }
        return view('publicCelender', compact('offsets', 'favdatekey', 'unfavdatekey', 'date_fav'));
        }else{
            return redirect()->back();
        }
    }

    public function cosmicstars()
    {
        $user = User::find(Auth::user()->id);
        $date = explode('-', $user->dob);

        // $dobdate = "1976-06-03";
        // $date = explode('-', $dobdate);
        $birth_date = $date[2];
        $birth_month = $date[1];
        $month = 10;
        $year = 2022;
        $date_fav = cal_days_in_month(0, $month, $year);
        $favdatekey = array();
        $offsets = null;
        for ($i = 1; $i <= $date_fav; $i++) {
            $fav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 1)
                ->where('month_id', $birth_month)
                ->where('date', $birth_date)
                ->first();
            $unfav = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 2)
                ->where('month_id', $birth_month)
                ->where('date', $birth_date)
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
            $start_date =  mktime(0, 0, 0, $month, $i, $year);
            $current_date = explode('-', date("D-j-M-Y", mktime(0, 0, 0, $month, $i, $year)));
            if ($i == 1) {

                $offsets = date("w", $start_date);
            }
            $date_sum = str_split($current_date[1], 1);
            $date_sum = array_sum($date_sum);
            if (strlen($date_sum) != 1) {
                $date_sum = str_split($date_sum);
                $date_sum = array_sum($date_sum);
            }
            $day_star = 0;
            $month_star = 0;
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

            $unfavday_star = 0;
            $unfavmonth_star = 0;
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

            $fav_cosmic_stars = [$date_star, $day_star, $month_star];
            $favdatekey[$i] =  $fav_cosmic_stars;

            $unfav_cosmic_stars = [$unfavdate_star, $unfavday_star, $unfavmonth_star];
            $unfavdatekey[$i] =  $unfav_cosmic_stars;
        }
        return view('cosmiccellender', compact('offsets', 'favdatekey', 'unfavdatekey', 'date_fav'));
    }
}
