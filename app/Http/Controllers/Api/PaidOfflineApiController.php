<?php

namespace App\Http\Controllers\Api;

use App\Models\Alphasystem_type;
use App\Models\Compatible_partner;
use App\Models\Fav_unfav_parameter;
use App\Models\Life_change;
use App\Models\Life_cycle;
use App\Models\Luckiest_parameter;
use App\Models\Module_description;
use App\Models\Planet_number;
use App\Models\Primaryno_type;
use App\Models\User;
use App\Models\Zodic_sign;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaidOfflineApiController extends Controller
{
    //
    public function offlineapi(Request $request)
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

        //health suggestions
        $healthsuggestion_desc = Module_description::where('moduletype_id', 7)
                            ->where('number', $dayno)
                            ->value('description');
        $healthsuggestionDesc = strip_tags($healthsuggestion_desc);
        $healthsuggestion = array("module_type" => 7, "module_name" => "Health Suggestion months", "number" => $dayno, "description" => $healthsuggestionDesc);


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
            $birthdayreading, $elementalno, $basichealthreading, $healthprecaution, $healthsuggestion, $basicmoneymatter, $detailmoneymatter,
            $basicparenting, $detailparenting, $destinynumber
        ];

        //prime number
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
        $explodePythaNameReading_desc = explode('||', $pytha_description);
        $pythaPositive_desc = $explodePythaNameReading_desc[0];
        $pythaNegative_desc = $explodePythaNameReading_desc[1];

        $chald_description = Module_description::where('moduletype_id', 1)
            ->where('number', $chald_no_sum)
            ->value('description');
        $chalddescription = strip_tags($chald_description);
        $explodenamereading_desc = explode('||', $chalddescription);
        $chaldPositive_desc = $explodenamereading_desc[0];
        $chaldNegative_desc = $explodenamereading_desc[1];
        if($user->subscription_status == 1){
            $positive_desc = $chaldPositive_desc;
            $negative_desc = $chaldNegative_desc;
        }else{
            $positive_desc = $pythaPositive_desc;
            $negative_desc = $pythaNegative_desc;
        }
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

        $favunfavparameters = array("favparameter" => $fav, "unfavparameters" => $unfav);

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

            $favlist = array();
            $unfavlist = array();
            for ($m = $startmonth; $m <= $no_of_month; $m++) {
                $daysinmonth = cal_days_in_month(0, $m, $y);
                $favdata = array();
                $unfavdata = array();

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
            $profile_pic = "";
        } else {
            $profile_pic = "https://astar.7kstartup.com/public/profile_pic/" . $user_profile_pic;
        }

        $loginusername = $user->name;
        $loginuserPythano = array();
        $loginuserchaldno = array();
        $userfinalname = str_replace(' ', '', $loginusername);
        $loginuserstrname = strtoupper($userfinalname);
        $loginusersplitname = str_split($loginuserstrname, 1);
        foreach ($loginusersplitname as $nameletter) {
            $loginuserPytha_no = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                ->where('systemtype_id', 1)
                ->value('number');
            array_push($loginuserPythano, $loginuserPytha_no);
            $loginuserchald_no = Alphasystem_type::where('alphabet', 'LIKE', '%' . $nameletter . '%')
                ->where('systemtype_id', 2)
                ->value('number');
            array_push($loginuserchaldno, $loginuserchald_no);
        }
        $loginuserPyhtano_sum = array_sum($loginuserPythano);
        while (strlen($loginuserPyhtano_sum) != 1) {
            $loginuserPyhtano_sumSplit = str_split($loginuserPyhtano_sum, 1);
            $loginuserPyhtano_arraysum = array_sum($loginuserPyhtano_sumSplit);
            $loginuserPyhtano_sum = $loginuserPyhtano_arraysum;
        }
        
        $loginuserchaldno_sum = array_sum($loginuserchaldno);
        while (strlen($loginuserchaldno_sum) != 1) {
            $loginuserchaldno_sumSplit = str_split($loginuserchaldno_sum, 1);
            $loginuserchaldno_arraysum = array_sum($loginuserchaldno_sumSplit);
            $loginuserchaldno_sum = $loginuserchaldno_arraysum;
        }

        if($user->subscription_status == 1){
            $loginusernamereadingno = $loginuserchaldno_sum;
        }else{
            $loginusernamereadingno = $loginuserPyhtano_sum;
        }

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

        $favnumber_array = explode(',', $dobfav);
        $fav_arraycount = count($favnumber_array);
        $unfavnumber_array = explode(',', $dobunfav);
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

        return response()
            ->json([
                'status' => 1,
                'message' => 'Success',
                'user_token' => $user->user_token,
                'subscription_status' => $user->subscription_status,
                'userdetail' => array('userid' => $userid, 'fullname' => $user->name, 'dob' => $user->dob, 'gender' => $gender, 'occupation' => $occupation, 'age' => $userage, 'profile_pic' => $profile_pic, 'joining_date' => date_format($loginuserjoinyear, 'Y-m-d'), 'namecompatibilitypercentage' => $namecompatibilitypercentage),
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
    }
}
