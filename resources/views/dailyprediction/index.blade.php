@include('includes.header')
@include('includes.navbar')
@include('includes.right_sidebar')
@include('includes.left_sidebar')
<style>
    .mCSB_draggerRail {
        display: contents;
    }
</style>
<div class="main-container">
    <div class="pd-20 card-box mb-30">
        <div class="row">
            <div class="col-md-6">
                <div class="">
                    <h2>Daily Predictions</h2>
                </div>
            </div>
        </div>
    </div>
    @if (count($errors) > 0)
    <div class="alert alert-danger">
        <strong>Whoops!</strong> Something went wrong.<br><br>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <div class="pd-20 card-box mb-30">
        <ul class="nav nav-tabs  tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link text-blue active" data-toggle="tab" href="#predictionlist" role="tab">Prediction List</a>
            </li>
            @can('dailyprediction-create')
            <li class="nav-item">
                <a class="nav-link text-blue" data-toggle="tab" href="#predictionform" role="tab">Add Prediction</a>
            </li>
            @endcan
        </ul>
        <div class="tab-content tabs card-block">
            <div class="tab-pane active" id="predictionlist" role="tabpanel">
                <div class="card-block table-border-style">
                    <div class="">
                        <div class=" col-md-12 col-sm-12 mb-30">
                            <div class="">
                                <div class="tab">
                                    <div class="row clearfix">
                                        <div class="col-md-3 col-sm-12">
                                            <ul class="nav flex-column vtabs nav-tabs customtab" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active" data-toggle="tab" role="tab" aria-selected="true" href="#All">All</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-toggle="tab" role="tab" aria-selected="true" href="#Published">Published</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-toggle="tab" role="tab" aria-selected="true" href="#Scheduled">Scheduled</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-9 col-sm-12">
                                            <div class="tab-content tabs card-block">
                                                <div class="tab-pane fade show active" id="All" role="tabpanel">
                                                    <div class="pd-20">
                                                        <div class="">
                                                            <div class="card-block table-border-style">
                                                                <div class="chat-box">
                                                                    <div class="notification-list mx-h-350 customscroll">
                                                                        <ul>
                                                                            @foreach ($predictions as $prediction)
                                                                            <li class="clearfix profilePicli">
                                                                                <div class="chat-img col-md-2 user-icon userimg">
                                                                                    <?php
                                                                                    if ($prediction->users->profile_pic != null) {
                                                                                        $profile_pic_src = "/profile_pic/" . $prediction->users->profile_pic;
                                                                                    } else {
                                                                                        $profile_pic_src = "images/dummy.jpg";
                                                                                    }
                                                                                    ?>
                                                                                    <img src='{{asset($profile_pic_src)}}' class="profilepic" alt="">
                                                                                </div>
                                                                                <div class="chat-body col-md-10 clearfix chatBodyContainer">
                                                                                    <?php {
                                                                                        $data = strip_tags($prediction->prediction);
                                                                                        $like_prediction = App\Models\User_prediction::where('dailyprediction_id', $prediction->id)
                                                                                            ->where('is_like', '=', 1)->get();
                                                                                        $dislike_prediction = App\Models\User_prediction::where('dailyprediction_id', $prediction->id)
                                                                                            ->where('is_like', '=', 2)->get();
                                                                                        $dateformat = $prediction->prediction_date;
                                                                                        $predictiondate = date("M d, Y", strtotime($dateformat));
                                                                                    } ?>
                                                                                    <div class="containerflex">
                                                                                        <h5 class="wc20">{{$prediction->users->name}}</h5>
                                                                                        @if($prediction->prediction_date >date('Y-m-d'))
                                                                                        <span class="detaildate">Scheduled: {{ $predictiondate }}</span>
                                                                                        @else
                                                                                        <span class="detaildate">{{ $predictiondate }}</span>
                                                                                        @endif
                                                                                    </div>
                                                                                    <span>{!! $prediction->prediction !!}</span>
                                                                                    <div class="btncontainer">
                                                                                        <div class="container">
                                                                                            <div class="chat_time chatbutton"><i class="icon-copy fi-like likeicon" title="Like"></i>{{count($like_prediction)}}</div>
                                                                                            <div class="chat_time chatbutton"><i class="icon-copy fi-dislike dilikeicon" title="Dislike"></i>{{ count($dislike_prediction) }}</div>
                                                                                        </div>
                                                                                        @foreach($messagecounts as $messagecount)
                                                                                            @if($messagecount['predictionId'] == $prediction->id)
                                                                                                @if($prediction->prediction_date <= date('Y-m-d') && $prediction->publish_status == 1)
                                                                                                <div class="w-100">
                                                                                                    <a href="{{ route('chat.index',['date'=>$prediction->prediction_date])}}" style="float: right;"><span>Reply Count: {{$messagecount['count']}}</span></a>
                                                                                                </div>
                                                                                                @else
                                                                                                @endif
                                                                                            @else
                                                                                            @endif
                                                                                        @endforeach
                                                                                    </div>
                                                                                </div>
                                                                            </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade show" id="Published" role="tabpanel">
                                                    <div class="pd-20">
                                                        <div class="">
                                                            <div class="card-block table-border-style">
                                                                <div class="chat-box">
                                                                    <div class="notification-list mx-h-350 customscroll">
                                                                        <ul>
                                                                            @foreach ($predictions as $prediction)
                                                                            @if($prediction->publish_status == 1)
                                                                            <li class="clearfix profilePicli">
                                                                                <div class="chat-img col-md-2 user-icon userimg">
                                                                                    <?php
                                                                                    if ($prediction->users->profile_pic != null) {
                                                                                        $profile_pic_src = "/profile_pic/" . $prediction->users->profile_pic;
                                                                                    } else {
                                                                                        $profile_pic_src = "images/dummy.jpg";
                                                                                    }
                                                                                    ?>
                                                                                    <img src='{{asset($profile_pic_src)}}' class="profilepic" alt="">
                                                                                </div>
                                                                                <div class="chat-body col-md-10 clearfix chatBodyContainer">
                                                                                    <?php {
                                                                                        $data = strip_tags($prediction->prediction);
                                                                                        $like_prediction = App\Models\User_prediction::where('dailyprediction_id', $prediction->id)
                                                                                            ->where('is_like', '=', 1)->get();
                                                                                        $dislike_prediction = App\Models\User_prediction::where('dailyprediction_id', $prediction->id)
                                                                                            ->where('is_like', '=', 2)->get();
                                                                                        $dateformat = $prediction->prediction_date;
                                                                                        $predictiondate = date("M d, Y", strtotime($dateformat));
                                                                                    } ?>
                                                                                    <div class="containerflex">
                                                                                        <h5 class="wc20">{{$prediction->users->name}}</h5>
                                                                                        <span class="detaildate">{{ $predictiondate }}</span>
                                                                                    </div>
                                                                                    <span>{!! $prediction->prediction !!}</span>
                                                                                    <div class="btncontainer">
                                                                                        <div class="container">
                                                                                            <div class="chat_time chatbutton"><i class="icon-copy fi-like likeicon" title="Like"></i>{{count($like_prediction)}}</div>
                                                                                            <div class="chat_time chatbutton"><i class="icon-copy fi-dislike dilikeicon" title="Dislike"></i>{{ count($dislike_prediction) }}</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </li>
                                                                            @else
                                                                            @endif
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade show" id="Scheduled" role="tabpanel">
                                                    <div class="pd-20">
                                                        <div class="">
                                                            <div class="card-block table-border-style">
                                                                <div class="chat-box">
                                                                    <div class="notification-list mx-h-350 customscroll">
                                                                        <ul>
                                                                            @foreach ($predictions as $prediction)
                                                                            @if($prediction->publish_status == 2)
                                                                            <li class="clearfix profilePicli">
                                                                                <div class="chat-img col-md-2 user-icon userimg">
                                                                                    <?php
                                                                                    if ($prediction->users->profile_pic != null) {
                                                                                        $profile_pic_src = "/profile_pic/" . $prediction->users->profile_pic;
                                                                                    } else {
                                                                                        $profile_pic_src = "images/dummy.jpg";
                                                                                    }
                                                                                    ?>
                                                                                    <img src='{{asset($profile_pic_src)}}' class="profilepic" alt="">
                                                                                </div>
                                                                                <div class="chat-body col-md-10 clearfix chatBodyContainer">
                                                                                    <?php {
                                                                                        $data = strip_tags($prediction->prediction);
                                                                                        $like_prediction = App\Models\User_prediction::where('dailyprediction_id', $prediction->id)
                                                                                            ->where('is_like', '=', 1)->get();
                                                                                        $dislike_prediction = App\Models\User_prediction::where('dailyprediction_id', $prediction->id)
                                                                                            ->where('is_like', '=', 2)->get();
                                                                                        $dateformat = $prediction->prediction_date;
                                                                                        $predictiondate = date("M d, Y", strtotime($dateformat));
                                                                                    } ?>
                                                                                    <div class="containerflex">
                                                                                        <h5 class="wc20">{{$prediction->users->name}}</h5>
                                                                                        <span class="detaildate">{{ $predictiondate }}</span>
                                                                                    </div>
                                                                                    <span>{!! $prediction->prediction !!}</span>
                                                                                    <div class="btncontainer">
                                                                                        <div class="container">
                                                                                            <div class="chat_time chatbutton"><i class="icon-copy fi-like likeicon" title="Like"></i>{{count($like_prediction)}}</div>
                                                                                            <div class="chat_time chatbutton"><i class="icon-copy fi-dislike dilikeicon" title="Dislike"></i>{{ count($dislike_prediction) }}</div>
                                                                                        </div>
                                                                                        <div class="formbtn">
                                                                                            @can('dailyprediction-edit')
                                                                                                @if($prediction->publish_status != 1)
                                                                                                {!! Form::open(['method' => 'POST','route' => ['dailyprediction.publish', $prediction->id],'style'=>'display:inline']) !!}
                                                                                                <button type="submit" data-name="{{ $data }}" class="btn alert-published"><i class="fa fa-upload" title="Publish"></i></button>
                                                                                                {!! Form::close() !!}
                                                                                                {!! Form::open(['method' => 'POST','route' => ['dailyprediction.cancel', $prediction->id],'style'=>'display:inline']) !!}
                                                                                                <button type="submit" data-name="{{ $data }}" class="btn alert-cancel"><i class="icon-copy ion-android-cancel" title="Cancel"></i></button>
                                                                                                {!! Form::close() !!}
                                                                                                @else
                                                                                                @endif
                                                                                            @endcan
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </li>
                                                                            @else
                                                                            @endif
                                                                            @endforeach
                                                                        </ul>
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
            <div class="tab-pane" id="predictionform" role="tabpanel">
                <div class="card-block table-border-style">
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2"></div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            @can('dailyprediction-create')
                            {!! Form::open(array('route' => 'dailyprediction.store','id'=>'prediction_form','method'=>'POST')) !!}
                            @csrf
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <strong>Date:</strong>
                                        {!! Form::date('prediction_date', date('Y-m-d'), array('placeholder' => 'Date', 'min'=>date('Y-m-d'),'class' => 'form-control pred_Date')) !!}
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <strong>Prediction :</strong>
                                        {!! Form::textarea('prediction', null, array('placeholder' => 'Prediction','class' => 'form-control prediction')) !!}
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12 text-center submitBtn">
                                    <button type="submit" class="btn btn-primary alert-publish" onClick="return alertpublish();">Publish</button>
                                </div>
                            </div>
                            {!! Form::close() !!}
                            @endcan
                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/23.0.0/classic/ckeditor.js"></script>


<script type="text/javascript">
    $('.pred_Date').on('change', function(event) {
        var prediction_Date = $('.pred_Date').val();
        var d = new Date();
        var date = d.getDate();
        var month = d.getMonth() + 1;
        var year = d.getFullYear();
        if (month < 10) {
            var currentdate = year + '-0' + month + '-' + date;
        } else {
            var currentdate = year + '-' + month + '-' + date;
        }
        var html = '';
        if (prediction_Date == currentdate) {
            html += '<button type="submit" class="btn btn-primary alert-publish" onClick="return alertpublish();">Publish</button>';
        } else {
            html += '<button type="submit" class="btn btn-primary alert-schedule" onClick="return alertschedule();">Schedule</button>';
        }
        $('.submitBtn').empty();
        $('.submitBtn').html(html);
    });
</script>
<script type="text/javascript">
    function alertpublish() {
        var prediction_Date = $('.pred_Date').val();
        event.preventDefault();
        $.ajax({
            type: 'POST',
            url: "{{url('predictiondate')}}",
            data: {
                "_token": "{{ csrf_token() }}",
                "prediction_Date": prediction_Date,
            },
            success: function(data) {
                if (data.status == 1) {
                var form = $(this).closest("form");
                    swal({
                        title: data.message,
                        icon: "warning",
                        type: "warning",
                        buttons: ["Cancel", "Yes!"],
                        confirmButtonColor: '#17A2B8',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Publish it!'
                    }).then((isConfirm) => {
                        if (isConfirm) {
                            $('#prediction_form').submit();
                        }
                    });
                } else {
                    swal({
                        title: data.message,
                        icon: "warning",
                        type: "warning",
                    });
                }
            }
        });
    };
</script>
<script type="text/javascript">
    function alertschedule() {
        var form = $(this).closest("form");
        var prediction_Date = $('.pred_Date').val();
        event.preventDefault();
        $.ajax({
            type: 'POST',
            url: "{{url('predictiondate')}}",
            data: {
                "_token": "{{ csrf_token() }}",
                "prediction_Date": prediction_Date,
            },
            success: function(data) {
                if (data.status == 1) {
                    swal({
                        title: data.message,
                        icon: "warning",
                        type: "warning",
                        buttons: ["Cancel", "Yes!"],
                        confirmButtonColor: '#17A2B8',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((willDelete) => {
                        if (willDelete) {
                            $('#prediction_form').submit();

                        }
                    });
                }else{
                    swal({
                        title: data.message,
                        icon: "warning",
                        type: "warning",
                    });
                }
            }
        });
    };
</script>
<script type="text/javascript">
    $('.alert-published').click(function(event) {
        var form = $(this).closest("form");
        event.preventDefault();
        swal({
            title: "Are you sure you want to Publish this Prediction?",
            icon: "warning",
            type: "warning",
            buttons: ["Cancel", "Yes!"],
            confirmButtonColor: '#17A2B8',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((willDelete) => {
            if (willDelete) {
                form.submit();
            }
        });
    });
</script>
<script type="text/javascript">
    $('.alert-cancel').click(function(event) {
        var form = $(this).closest("form");
        event.preventDefault();
        swal({
            title: "Are you sure you want to Cancel this Prediction?",
            text: "If you cancel this, it will be gone forever.",
            icon: "warning",
            type: "warning",
            buttons: ["Cancel", "Yes!"],
            confirmButtonColor: '#17A2B8',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((willDelete) => {
            if (willDelete) {
                form.submit();
            }
        });      
    });
</script>
@include('includes.footer')