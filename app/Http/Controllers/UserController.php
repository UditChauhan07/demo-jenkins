<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Alphasystem_type;
use App\Models\Cancel_user_subscription;
use App\Models\Fav_unfav_parameter;
use App\Models\Luckiest_parameter;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\model_has_role;
use App\Models\Module_description;
use App\Models\Planet_number;
use App\Models\Subscription_prize;
use App\Models\User_payment;
use App\Models\Zodic_sign;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;

class UserController extends Controller

{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = model_has_role::where('role_id', 23)->with('user','userprofile')->orderBy('model_id', 'DESC')->whereHas("userprofile", function($subQuery){ 
            $subQuery->where("overall_status", "=", 1); })->paginate(15, ['*'], 'all');
        $active_users = model_has_role::where('role_id', '!=', 17)->with('user', 'userprofile')->orderBy('model_id', 'DESC')->whereHas("userprofile", function($subQuery){ 
            $subQuery->where("overall_status", "=", 1); })->whereHas("user", function($subQuery){ $subQuery->where("is_active", "=", 1); })->paginate(15, ['*'], 'activeUser');
        $inactive_users = model_has_role::where('role_id', '!=', 17)->with('user', 'userprofile')->orderBy('model_id', 'DESC')->whereHas("userprofile", function($subQuery){ 
            $subQuery->where("overall_status", "=", 1); })->whereHas("user", function($subQuery){ $subQuery->where("is_active", "=", 0); })->paginate(15, ['*'], 'inactiveUser');
        $allpayments = User_payment::where('is_active', 1)->get();
        // return $users;
         return view('users.index', compact('users','active_users','inactive_users','allpayments'));
    }

    // Export users list
    public function userexport(Request $request)
    {
       $list = $request->listtype;
       if($list == 1)
       {
        $users = model_has_role::where('role_id', 23)->with('user', 'userprofile')->orderBy('model_id', 'DESC')->whereHas("userprofile", function($subQuery){ 
            $subQuery->where("overall_status", "=", 1); })->get();
        foreach($users as $alluser)
        {
            $allusers[] = $alluser->user;

        }
         return (new FastExcel($allusers))->download('allusers.xlsx');

        }elseif($list == 2)
       {

        $users = model_has_role::where('role_id', 23)->with('user')->get();
        foreach($users as $user)
        {
            if($user->user->is_active == 1)
            {
                $active_user[] = $user->user;

            }
        }
        return (new FastExcel($active_user))->download('activeusers.csv');

       }else
       {

        $users = model_has_role::where('role_id', 23)->with('user')->get();
            foreach($users as $user)
            {
                if($user->user->is_active == 0)
                {
                    $inactive_user[] = $user->user;

                }
            }
     return (new FastExcel($inactive_user))->download('inactiveusers.xlsx');
       }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $roles = Role::where('id', '!=', 17)->get();
        return view('users.create',compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
        ]);

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);

        $user = User::create($input);

        return redirect()->route('users.index')
                        ->with('success','User created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = User::find($id);
        $userdob = $data->dob;
        $explode_userdob = explode('-', $userdob);
        $userDobdate = $explode_userdob[2];
        $userDobmonth = $explode_userdob[1];
        $userDobyear = $explode_userdob[0];

        //destiny no
        $dayno = str_split($userDobdate, 1);
        $dayno = array_sum($dayno);
        $dayno = intval($dayno);
        while (strlen($dayno) != 1) {
            $dayno = str_split($dayno);
            $dayno = array_sum($dayno);
        }
        $monthno = str_split($userDobmonth, 1);
        $monthno = array_sum($monthno);
        while (strlen($monthno) != 1) {
            $monthno = str_split($monthno);
            $monthno = array_sum($monthno);
        }
        $yearno = str_split($userDobyear, 1);
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

        //name reading
            $username = $data->name;
            $finalname = str_replace(' ', '', $username);
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
            $namereading = strip_tags($chald_description);

        //fav unfav parameters
        $favdata = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 1)
        ->where('month_id', $userDobmonth)
        ->where('date', $userDobdate)
        ->first();
        
        $unfavdata = Fav_unfav_parameter::select('numbers', 'days', 'months')->where('type', 2)
            ->where('month_id', $userDobmonth)
            ->where('date', $userDobdate)
            ->first();

        //Zodic sign
            $formetdob = date("d-F-Y", strtotime($userdob));
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

        //Planet number
        $planet = Planet_number::select('name', 'ruling_number', 'description')
                ->where('ruling_number', $dayno)
                ->first();
        
        //Lucky parameters
        $luckyparameters = Luckiest_parameter::select('lucky_colours', 'lucky_gems', 'lucky_metals')
                ->where('number', $dayno)
                ->first();

        //Parenting
        if($data->subscription_status == 1){
            $parenting = Module_description::where('moduletype_id', 13)
                        ->where('number', $dayno)
                        ->value('description');
        }else{
            $parenting = Module_description::where('moduletype_id', 12)
                    ->where('number', $dayno)
                    ->value('description');
        }
        if($data->subscription_status == 1){
            $subscription_detail = User_payment::where('user_id', $data->id)->orderBy('id', 'DESC')->latest()->first();
        }else{
            $subscription_detail = '';
        }
        // dd($subscription_detail);
        return view('users.show',compact('data','namereading','destiny_no','destinynodesc','favdata','unfavdata','zodiacdata','luckyparameters','planet','parenting','subscription_detail'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::pluck('name','name')->all();
        $userRole = $user->roles->pluck('name','name')->all();
        return view('users.edit',compact('user','roles','userRole'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);

        $input = $request->all();
        if(!empty($input['password'])){
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));
        }

        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id',$id)->delete();

        $user->assignRole($request->input('roles'));

        return redirect()->route('users.index')
                        ->with('success','User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = User::find($id);
        $data->is_active = 0;
        $data->save();
        return redirect()->route('users.index')
                        ->with('success','User Blocked successfully');
    }

    public function unblock($id)
    {
        $data = User::find($id);
        $data->is_active = 1;
        $data->save();
        return redirect()->route('users.index')
                        ->with('success','User Unblock successfully');
    }

    public function threemonthsubscribes($id)
    {
        $user = User::find($id);
        $user->subscription_status = 9;
        $user->save();

        $amount = 299;
        
        $start_date = strtotime($user->updated_at);
        $end_date = strtotime("+3 month", $start_date);
        $endDate_format = date('Y-m-d h:m:s', $end_date); 
        $userpaymentData = User_payment::where('user_id', $user->id)->where('status', 'ByAdmin')->get();
        if(count($userpaymentData) > 0){
            $userpayment = User_payment::where('user_id', $user->id)->where('status', 'ByAdmin')->latest()->first();
            $userpayment->plan_name = 'Standard';
            $userpayment->amount = $amount;
            $userpayment->renewal_date = $endDate_format;
            $userpayment->update();
        }else{
            $userpayment = User_payment::create([
                'user_id' => $user->id,
                'subscription_status' => 9,
                'plan_name' => 'Standard',
                'amount' => $amount,
                'start_date' => $user->updated_at,
                'renewal_date' => $endDate_format,
                'status' => 'ByAdmin',
            ]);
        }
        
        return redirect()->route('users.index')
                        ->with('success','User Subscription status updated successfully');
    }

    public function subscribe($id)
    {
        $user = User::find($id);
        $user->subscription_status = 9;
        $user->save();

        $amount = 299;
        
        $start_date = strtotime($user->updated_at);
        $end_date = strtotime("+1 month", $start_date);
        $endDate_format = date('Y-m-d h:m:s', $end_date); 
        $userpaymentData = User_payment::where('user_id', $user->id)->where('subscription_status', 9)->get();
        if(count($userpaymentData) > 0){
            $userpayment = User_payment::where('user_id', $user->id)->where('subscription_status', 9)->latest()->first();
            $userpayment->plan_name = 'Standard';
            $userpayment->amount = $amount;
            $userpayment->renewal_date = $endDate_format;
            $userpayment->save();
        }else{
            $userpayment = User_payment::create([
                'user_id' => $user->id,
                'subscription_status' => 9,
                'plan_name' => 'Standard',
                'amount' => $amount,
                'start_date' => $user->updated_at,
                'renewal_date' => $endDate_format,
                'status' => 'ByAdmin',
            ]);
        }
        return redirect()->route('users.index')
                        ->with('success','User Subscription status updated successfully');
    }

    public function unsubscribe($id)
    {
        $user = User::find($id);
        $userpayment = User_payment::where('user_id', $user->id)->where('subscription_status', 9)->latest()->first();
        if($userpayment)
        {
            $user->subscription_status = 0;
            $user->save();

            $updatePaymentData = User_payment::find($userpayment->id);
            $updatePaymentData->delete();
            $cancelSub = Cancel_user_subscription::create([
                'user_id' => $user->id,
                'type' => 9,
                'subscription_id' => 'ByAdmin',
                'type_date' => $user->updated_at,
            ]);
            return redirect()->route('users.index')
                            ->with('success','User Subscription status updated successfully');
        }else{
            return redirect()->route('users.index')
                            ->with('Error','Something went Wrong please try again later!');
        }
    }

    public function userfilter(Request $request)
    {
        $username = $request->username;
        $useremail = $request->useremail;
        $usersubscription = $request->usersubscription;
        $pageNumber = $request->pageNumber;
        $pageNumber1 = $request->pageNumber1;
        $pageNumber2 = $request->pageNumber2;
        // return $username." ".$useremail." ".$usersubscription;
        // return $pageNumber." ".$pageNumber1." ".$pageNumber2;
        
        if($username != '' && $useremail == '' && $usersubscription == '')
        {
            $filterusers = model_has_role::where('role_id', 23)->with('user','userprofile')->orderBy('model_id', 'DESC')->whereHas("userprofile", function($subQuery) { 
                $subQuery->where("overall_status", "=", 1); })->whereHas("user", function($subQuery) use ($username){ 
                $subQuery->where('name', 'LIKE', '%' . $username . '%'); })->paginate(15, ['*'], 'page', $pageNumber);
                
            $filterActiveusers = model_has_role::where('role_id', 23)->with('user','userprofile')->orderBy('model_id', 'DESC')->whereHas("userprofile", function($subQuery) { 
                $subQuery->where("overall_status", "=", 1); })->whereHas("user", function($subQuery) use ($username){ 
                $subQuery->where('name', 'LIKE', '%' . $username . '%')->where('is_active', 1); })->paginate(15, ['*'], 'page', $pageNumber1);
                
            $filterInactiveusers = model_has_role::where('role_id', 23)->with('user','userprofile')->orderBy('model_id', 'DESC')->whereHas("userprofile", function($subQuery) { 
                $subQuery->where("overall_status", "=", 1); })->whereHas("user", function($subQuery) use ($username){ 
                $subQuery->where('name', 'LIKE', '%' . $username . '%')->where('is_active', 0); })->paginate(15, ['*'], 'page', $pageNumber2);
                
            $pagination = "<div class='customPagination'>".$filterusers->links()."</div>";
            $pagination1 = "<div class='customPagination1'>".$filterActiveusers->links()."</div>";
            $pagination2 = "<div class='customPagination2'>".$filterInactiveusers->links()."</div>";
                return response()->json(['status'=>1, 'message'=>'success','filterusers'=>$filterusers,'pagination'=>$pagination,'filterActiveusers'=>$filterActiveusers, 'pagination1'=>$pagination1, 'filterInactiveusers'=>$filterInactiveusers, 'pagination2'=>$pagination2]);
        }elseif($username == '' && $useremail != '' && $usersubscription == '')
        {
            $filterusers = model_has_role::where('role_id', 23)->with('user','userprofile')->orderBy('model_id', 'DESC')->whereHas("userprofile", function($subQuery) { 
                $subQuery->where("overall_status", "=", 1); })->whereHas("user", function($subQuery) use ($useremail){ 
                $subQuery->where('email', 'LIKE', '%' . $useremail . '%'); })->paginate(15, ['*'], 'page', $pageNumber);

            $filterActiveusers = model_has_role::where('role_id', 23)->with('user','userprofile')->orderBy('model_id', 'DESC')->whereHas("userprofile", function($subQuery) { 
                $subQuery->where("overall_status", "=", 1); })->whereHas("user", function($subQuery) use ($useremail){ 
                $subQuery->where('email', 'LIKE', '%' . $useremail . '%')->where('is_active', 1); })->paginate(15, ['*'], 'page', $pageNumber1);
            $filterInactiveusers = model_has_role::where('role_id', 23)->with('user','userprofile')->orderBy('model_id', 'DESC')->whereHas("userprofile", function($subQuery) { 
                $subQuery->where("overall_status", "=", 1); })->whereHas("user", function($subQuery) use ($useremail){ 
                $subQuery->where('email', 'LIKE', '%' . $useremail . '%')->where('is_active', 0); })->paginate(15, ['*'], 'page', $pageNumber2);
            
            $pagination = "<div class='customPagination'>".$filterusers->links()."</div>";
            $pagination1 = "<div class='customPagination1'>".$filterActiveusers->links()."</div>";
            $pagination2 = "<div class='customPagination2'>".$filterInactiveusers->links()."</div>";
                return response()->json(['status'=>1, 'message'=>'success', 'filterusers'=>$filterusers, 'pagination'=>$pagination,'filterActiveusers'=>$filterActiveusers, 'pagination1'=>$pagination1, 'filterInactiveusers'=>$filterInactiveusers, 'pagination2'=>$pagination2]);
        }elseif($username == '' && $useremail == '' && $usersubscription != '')
        {
            $filterusers = model_has_role::where('role_id', 23)->with('user','userprofile')->orderBy('model_id', 'DESC')->whereHas("userprofile", function($subQuery) { 
                $subQuery->where("overall_status", "=", 1); })->whereHas("user", function($subQuery) use ($usersubscription){ 
                $subQuery->where('subscription_status', $usersubscription); })->paginate(15, ['*'], 'page', $pageNumber);

            $filterActiveusers = model_has_role::where('role_id', 23)->with('user','userprofile')->orderBy('model_id', 'DESC')->whereHas("userprofile", function($subQuery) { 
                $subQuery->where("overall_status", "=", 1); })->whereHas("user", function($subQuery) use ($usersubscription){ 
                $subQuery->where('subscription_status', $usersubscription)->where('is_active', 1); })->paginate(15, ['*'], 'page', $pageNumber1);

            $filterInactiveusers = model_has_role::where('role_id', 23)->with('user','userprofile')->orderBy('model_id', 'DESC')->whereHas("userprofile", function($subQuery) { 
                $subQuery->where("overall_status", "=", 1); })->whereHas("user", function($subQuery) use ($usersubscription){ 
                $subQuery->where('subscription_status', $usersubscription)->where('is_active', 0); })->paginate(15, ['*'], 'page', $pageNumber2);

            $pagination = "<div class='customPagination'>".$filterusers->links()."</div>";
            $pagination1 = "<div class='customPagination1'>".$filterActiveusers->links()."</div>";
            $pagination2 = "<div class='customPagination2'>".$filterInactiveusers->links()."</div>";
                return response()->json(['status'=>1, 'message'=>'success', 'filterusers'=>$filterusers, 'pagination'=>$pagination,'filterActiveusers'=>$filterActiveusers, 'pagination1'=>$pagination1, 'filterInactiveusers'=>$filterInactiveusers, 'pagination2'=>$pagination2]);
        }elseif($username != '' && $useremail != '' && $usersubscription == '')
        {
            $filterusers = model_has_role::where('role_id', 23)->with('user','userprofile')->orderBy('model_id', 'DESC')->whereHas("userprofile", function($subQuery) { 
                $subQuery->where("overall_status", "=", 1); })->whereHas("user", function($subQuery) use ($username,$useremail){ 
                $subQuery->where('name', 'LIKE', '%' . $username . '%')->where('email', 'LIKE', '%' . $useremail . '%'); })->paginate(15, ['*'], 'page', $pageNumber);

            $filterActiveusers = model_has_role::where('role_id', 23)->with('user','userprofile')->orderBy('model_id', 'DESC')->whereHas("userprofile", function($subQuery) { 
                $subQuery->where("overall_status", "=", 1); })->whereHas("user", function($subQuery) use ($username,$useremail){ 
                $subQuery->where('name', 'LIKE', '%' . $username . '%')->where('email', 'LIKE', '%' . $useremail . '%')->where('is_active', 1); })->paginate(15, ['*'], 'page', $pageNumber1);

            $filterInactiveusers = model_has_role::where('role_id', 23)->with('user','userprofile')->orderBy('model_id', 'DESC')->whereHas("userprofile", function($subQuery) { 
                $subQuery->where("overall_status", "=", 1); })->whereHas("user", function($subQuery) use ($username,$useremail){ 
                $subQuery->where('name', 'LIKE', '%' . $username . '%')->where('email', 'LIKE', '%' . $useremail . '%')->where('is_active', 0); })->paginate(15, ['*'], 'page', $pageNumber2);

            $pagination = "<div class='customPagination'>".$filterusers->links()."</div>";
            $pagination1 = "<div class='customPagination1'>".$filterActiveusers->links()."</div>";
            $pagination2 = "<div class='customPagination2'>".$filterInactiveusers->links()."</div>";
                return response()->json(['status'=>1, 'message'=>'success', 'filterusers'=>$filterusers, 'pagination'=>$pagination,'filterActiveusers'=>$filterActiveusers, 'pagination1'=>$pagination1, 'filterInactiveusers'=>$filterInactiveusers, 'pagination2'=>$pagination2]);
        }elseif($username != '' && $useremail == '' && $usersubscription != '')
        {
            $filterusers = model_has_role::where('role_id', 23)->with('user','userprofile')->orderBy('model_id', 'DESC')->whereHas("userprofile", function($subQuery) { 
                $subQuery->where("overall_status", "=", 1); })->whereHas("user", function($subQuery) use ($username,$usersubscription){ 
                $subQuery->where('name', 'LIKE', '%' . $username . '%')->where('subscription_status', $usersubscription); })->paginate(15, ['*'], 'page', $pageNumber);

            $filterActiveusers = model_has_role::where('role_id', 23)->with('user','userprofile')->orderBy('model_id', 'DESC')->whereHas("userprofile", function($subQuery) { 
                $subQuery->where("overall_status", "=", 1); })->whereHas("user", function($subQuery) use ($username,$usersubscription){ 
                $subQuery->where('name', 'LIKE', '%' . $username . '%')->where('subscription_status', $usersubscription)->where('is_active', 1); })->paginate(15, ['*'], 'page', $pageNumber1);

            $filterInactiveusers = model_has_role::where('role_id', 23)->with('user','userprofile')->orderBy('model_id', 'DESC')->whereHas("userprofile", function($subQuery) { 
                $subQuery->where("overall_status", "=", 1); })->whereHas("user", function($subQuery) use ($username,$usersubscription){ 
                $subQuery->where('name', 'LIKE', '%' . $username . '%')->where('subscription_status', $usersubscription)->where('is_active', 0); })->paginate(15, ['*'], 'page', $pageNumber2);

            $pagination = "<div class='customPagination'>".$filterusers->links()."</div>";
            $pagination1 = "<div class='customPagination1'>".$filterActiveusers->links()."</div>";
            $pagination2 = "<div class='customPagination2'>".$filterInactiveusers->links()."</div>";
                return response()->json(['status'=>1, 'message'=>'success', 'filterusers'=>$filterusers, 'pagination'=>$pagination,'filterActiveusers'=>$filterActiveusers, 'pagination1'=>$pagination1, 'filterInactiveusers'=>$filterInactiveusers, 'pagination2'=>$pagination2]);
        }elseif($username == '' && $useremail != '' && $usersubscription != '')
        {
            $filterusers = model_has_role::where('role_id', 23)->with('user','userprofile')->orderBy('model_id', 'DESC')->whereHas("userprofile", function($subQuery) { 
                $subQuery->where("overall_status", "=", 1); })->whereHas("user", function($subQuery) use ($useremail,$usersubscription){ 
                $subQuery->where('email', 'LIKE', '%' . $useremail . '%')->where('subscription_status', $usersubscription); })->paginate(15, ['*'], 'page', $pageNumber);

            $filterActiveusers = model_has_role::where('role_id', 23)->with('user','userprofile')->orderBy('model_id', 'DESC')->whereHas("userprofile", function($subQuery) { 
                $subQuery->where("overall_status", "=", 1); })->whereHas("user", function($subQuery) use ($useremail,$usersubscription){ 
                $subQuery->where('email', 'LIKE', '%' . $useremail . '%')->where('subscription_status', $usersubscription)->where('is_active', 1); })->paginate(15, ['*'], 'page', $pageNumber1);

            $filterInactiveusers = model_has_role::where('role_id', 23)->with('user','userprofile')->orderBy('model_id', 'DESC')->whereHas("userprofile", function($subQuery) { 
                $subQuery->where("overall_status", "=", 1); })->whereHas("user", function($subQuery) use ($useremail,$usersubscription){ 
                $subQuery->where('email', 'LIKE', '%' . $useremail . '%')->where('subscription_status', $usersubscription)->where('is_active', 0); })->paginate(15, ['*'], 'page', $pageNumber2);

            $pagination = "<div class='customPagination'>".$filterusers->links()."</div>";
            $pagination1 = "<div class='customPagination1'>".$filterActiveusers->links()."</div>";
            $pagination2 = "<div class='customPagination2'>".$filterInactiveusers->links()."</div>";
                return response()->json(['status'=>1, 'message'=>'success', 'filterusers'=>$filterusers, 'pagination'=>$pagination,'filterActiveusers'=>$filterActiveusers, 'pagination1'=>$pagination1, 'filterInactiveusers'=>$filterInactiveusers, 'pagination2'=>$pagination2]);
        }elseif($username != '' && $useremail != '' && $usersubscription != '')
        {
            $filterusers = model_has_role::where('role_id', 23)->with('user','userprofile')->orderBy('model_id', 'DESC')->whereHas("userprofile", function($subQuery) { 
                $subQuery->where("overall_status", "=", 1); })->whereHas("user", function($subQuery) use ($username,$useremail,$usersubscription){ 
                $subQuery->where('name', 'LIKE', '%' . $username . '%')->where('email', 'LIKE', '%' . $useremail . '%')->where('subscription_status', $usersubscription); })->paginate(15, ['*'], 'page', $pageNumber);

            $filterActiveusers = model_has_role::where('role_id', 23)->with('user','userprofile')->orderBy('model_id', 'DESC')->whereHas("userprofile", function($subQuery) { 
                $subQuery->where("overall_status", "=", 1); })->whereHas("user", function($subQuery) use ($username,$useremail,$usersubscription){ 
                $subQuery->where('name', 'LIKE', '%' . $username . '%')->where('email', 'LIKE', '%' . $useremail . '%')->where('subscription_status', $usersubscription)->where('is_active', 1); })->paginate(15, ['*'], 'page', $pageNumber1);

            $filterInactiveusers = model_has_role::where('role_id', 23)->with('user','userprofile')->orderBy('model_id', 'DESC')->whereHas("userprofile", function($subQuery) { 
                $subQuery->where("overall_status", "=", 1); })->whereHas("user", function($subQuery) use ($username,$useremail,$usersubscription){ 
                $subQuery->where('name', 'LIKE', '%' . $username . '%')->where('email', 'LIKE', '%' . $useremail . '%')->where('subscription_status', $usersubscription)->where('is_active', 0); })->paginate(15, ['*'], 'page', $pageNumber2);

            $pagination = "<div class='customPagination'>".$filterusers->links()."</div>";
            $pagination1 = "<div class='customPagination1'>".$filterActiveusers->links()."</div>";
            $pagination2 = "<div class='customPagination2'>".$filterInactiveusers->links()."</div>";
                return response()->json(['status'=>1, 'message'=>'success', 'filterusers'=>$filterusers, 'pagination'=>$pagination,'filterActiveusers'=>$filterActiveusers, 'pagination1'=>$pagination1, 'filterInactiveusers'=>$filterInactiveusers, 'pagination2'=>$pagination2]);
        }else
        {
            return response()->json(['status'=>2, 'message'=>'success']);
        }
        
    }
}
