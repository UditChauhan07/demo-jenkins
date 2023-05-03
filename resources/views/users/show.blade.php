@include('includes.header')
@include('includes.navbar')
@include('includes.right_sidebar')
@include('includes.left_sidebar')

<!-- <div class="main-container">
    <div class="pd-20 card-box mb-30">
        <div class="row">
            <div class="col-md-6">
                <div class="">
                    <h2>Name: {{ $data->name }}</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="text-right">
                    <a class="btn btn-primary" href="{{ route('users.index') }}"> Back </a>
                </div>
            </div>
        </div>
    </div>
    <div class="pd-20 card-box mb-30">
        <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6">
                <div class="form-group">
                    <strong>Name:</strong>
                    {{ $data->name }}
                </div>
                <div class="form-group">
                    <strong>D.O.B :</strong>
                    {{ $data->dob }}
                </div>
                <div class="form-group">
                    <strong>Gender :</strong>
                    {{ $data->gender }}
                </div>
                <div class="form-group">
                    <strong>Email :</strong>
                    {{ $data->email }}
                </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6">
                @if($data->profile_pic == null)
                <img src="{{asset('img/default-profile-img.png')}}" height="40%" width="40%" alt="">
                @else
                <img src="{{url('/profile_pic/'.$data->profile_pic)}}" height="40%" width="40%" alt="">
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Destiny Number:</strong>
                    {{ $destiny_no }}
                </div>
            </div>
            @php
            $explode_namereading = explode('||', $namereading);
            @endphp
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Name Reading:</strong>
                    <p style="margin-left: 50px;"><b>Positive:</b> {{$explode_namereading[0]}}</p>
                    <p style="margin-left: 50px;"><b>Negative:</b> {{$explode_namereading[1]}}</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6">
                <div class="form-group">
                    <strong>Fav Numbers:</strong>
                    {{ $favdata->numbers }}
                </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6">
                <div class="form-group">
                    <strong>Unfav Numbers:</strong>
                    {{ $unfavdata->numbers }}
                </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6">
                <div class="form-group">
                    <strong>Fav Months:</strong>
                    {{ $favdata->months }}
                </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6">
                <div class="form-group">
                    <strong>Unfav Months:</strong>
                    {{ $unfavdata->months }}
                </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6">
                <div class="form-group">
                    <strong>Fav Days:</strong>
                    {{ $favdata->days }}
                </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6">
                <div class="form-group">
                    <strong>Unfav Days:</strong>
                    {{ $unfavdata->days }}
                </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6">
                <div class="form-group">
                    <strong>Lucky Colors:</strong>
                    {{ $luckyparameters->lucky_colours }}
                </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6">
                <div class="form-group">
                    <strong>Lucky Metals:</strong>
                    {{ $luckyparameters->lucky_metals }}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6">
                <div class="form-group">
                    <strong>Zodiac sign:</strong>
                    {{ $zodiacdata->zodic_sign }}
                </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6">
                <div class="form-group">
                    <strong>Planet Name:</strong>
                    {{ $planet->name }}
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Planet Description:</strong>
                    {{ $planet->description }}
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Parenting:</strong>
                    {{ $parenting }}
                </div>
            </div>
        </div>
    </div>
</div> -->
<div class="main-container">
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="pd-20 card-box mb-30">
                <div class="row">
                    <div class="col-md-6">
                        <div class="">
                            <h2>Name: {{ $data->name }}</h2>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-right">
                            <a class="btn btn-primary" href="{{ route('users.index') }}"> Back </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-30">
                    <div class="pd-20 card-box height-100-p">
                        <div class="profile-photo profile_photo">
                            @if($data->profile_pic == null)
                            <img src="{{ asset('img/default-profile-img.png')}}" alt="" height="100%" width="100%">
                            @else
                            <img src="{{url('/profile_pic/'.$data->profile_pic)}}" alt="" height="100%" width="100%">
                            @endif
                        </div>
                        <h5 class="text-center h5 mb-0">{{ $data->name }}</h5>
                        <p class="text-center text-muted font-14">DOB: {{ $data->dob }}</p>
                        <div class="profile-info">
                            <h5 class="mb-20 h5 text-blue">Personal Information</h5>
                            <ul>
                                <li>
                                    <span>Gender:</span>
                                    {{ $data->gender }}
                                </li>
                                @if($data->email != null)
                                <li>
                                    <span>Email Address:</span>
                                    {{ $data->email }}
                                </li>
                                @endif
                                @if($data->phoneno != null)
                                <li>
                                    <span>Phone Number:</span>
                                    {{ $data->phoneno }}
                                </li>
                                @endif
                                <li>
                                    <span>Relationship:</span>
                                    @if($data->relationship == 1)
                                    Marriage
                                    @elseif($data->relationship == 2)
                                    Love
                                    @elseif($data->relationship == 3)
                                    Family
                                    @elseif($data->relationship == 4)
                                    Single
                                    @endif

                                </li>
                                <li>
                                    <span>Occupation:</span>
                                    @if($data->occupation == 1)
                                    Business
                                    @elseif($data->occupation == 2)
                                    Job
                                    @elseif($data->occupation == 3)
                                    Farmer
                                    @elseif($data->occupation == 4)
                                    Other
                                    @endif
                                </li>
                                <li>
                                    @php
                                    $joining_date = date_format($data->created_at, 'd-M-Y');
                                    if($data->subscription_status == 1){
                                        $status = 'Paid'; 
                                    }else{
                                        $status = 'Free'; 
                                    }
                                    @endphp
                                    <span>Joining Date:</span>
                                    {{$joining_date}}
                                </li>
                                <li>
                                    <span>Subscription Status</span>
                                    {{$status}}
                                </li>
                                @if($data->subscription_status == 1)
                                    @if($subscription_detail != null)
                                    @php
                                    $start_date = date('d-M-Y', strtotime($subscription_detail->start_date));
                                    $renewal_date = date('d-M-Y', strtotime($subscription_detail->renewal_date));
                                    @endphp
                                    <li>
                                        <span>Subscription Start Date</span>
                                        {{$start_date}}
                                    </li>
                                    <li>
                                        <span>Subscription Renewal Date</span>
                                        {{$renewal_date}}
                                    </li>
                                    @else
                                    @endif
                                @else
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 mb-30">
                    <div class="card-box height-100-p overflow-hidden">
                        <div class="profile-tab height-100-p">
                            <div class="tab height-100-p">
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="timeline" role="tabpanel">
                                        <div class="pd-20">
                                            <div class="profile-timeline">
                                                <h5 class="mb-20 h5 text-blue">Other Information</h5>
                                                <div class="profile-timeline-list">
                                                    <div class="row">
                                                        <div class="col-xs-12 col-sm-12 col-md-12">
                                                            <div class="form-group">
                                                                <strong>Name Reading:</strong>
                                                                <p><b>Positive:</b> {{$explode_namereading[0]}}</p>
                                                                <p><b>Negative:</b> {{$explode_namereading[1]}}</p>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-12 col-md-12">
                                                            <div class="form-group">
                                                                <strong>Destiny Number:</strong>
                                                                {{ $destiny_no }}
                                                            </div>
                                                        </div>
                                                        @php
                                                        $explode_destinynodesc = explode('||', $destinynodesc);
                                                        $learn_desc = $explode_destinynodesc[0];
                                                        $notlearn_desc = $explode_destinynodesc[1];
                                                        $explode_namereading = explode('||', $namereading);
                                                        @endphp
                                                        <div class="col-xs-12 col-sm-12 col-md-12">
                                                            <div class="form-group">
                                                                <strong>Destiny Description:</strong>
                                                                <p><b>Learn To Be:</b> {{$learn_desc}}</p>
                                                                <p><b>Learn Not To Be:</b> {{$notlearn_desc}}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-xs-6 col-sm-6 col-md-6">
                                                            <div class="form-group">
                                                                <strong>Fav Numbers:</strong>
                                                                {{ $favdata->numbers }}
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-6 col-sm-6 col-md-6">
                                                            <div class="form-group">
                                                                <strong>Unfav Numbers:</strong>
                                                                {{ $unfavdata->numbers }}
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-6 col-sm-6 col-md-6">
                                                            <div class="form-group">
                                                                <strong>Fav Months:</strong>
                                                                {{ $favdata->months }}
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-6 col-sm-6 col-md-6">
                                                            <div class="form-group">
                                                                <strong>Unfav Months:</strong>
                                                                {{ $unfavdata->months }}
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-6 col-sm-6 col-md-6">
                                                            <div class="form-group">
                                                                <strong>Fav Days:</strong>
                                                                {{ $favdata->days }}
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-6 col-sm-6 col-md-6">
                                                            <div class="form-group">
                                                                <strong>Unfav Days:</strong>
                                                                {{ $unfavdata->days }}
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-6 col-sm-6 col-md-6">
                                                            <div class="form-group">
                                                                <strong>Lucky Colors:</strong>
                                                                {{ $luckyparameters->lucky_colours }}
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-6 col-sm-6 col-md-6">
                                                            <div class="form-group">
                                                                <strong>Lucky Metals:</strong>
                                                                {{ $luckyparameters->lucky_metals }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-xs-6 col-sm-6 col-md-6">
                                                            <div class="form-group">
                                                                <strong>Zodiac sign:</strong>
                                                                {{ $zodiacdata->zodic_sign }}
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-6 col-sm-6 col-md-6">
                                                            <div class="form-group">
                                                                <strong>Planet Name:</strong>
                                                                {{ $planet->name }}
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-12 col-md-12">
                                                            <div class="form-group">
                                                                <strong>Planet Description:</strong>
                                                                {{ $planet->description }}
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-12 col-md-12">
                                                            <div class="form-group">
                                                                <strong>Parenting:</strong>
                                                                {{ $parenting }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('includes.footer')