@include('includes.header')
@include('includes.navbar')
@include('includes.right_sidebar')
@include('includes.left_sidebar')

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
    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
    @endif

    <div class="pd-20 card-box mb-30">
        {!! Form::open(array('route' => 'dailyprediction.store','method'=>'POST')) !!}
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Date:</strong>
                    {!! Form::date('prediction_date', null, array('placeholder' => 'Date','class' => 'form-control')) !!}
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Prediction :</strong>
                    {!! Form::textarea('prediction', null, array('placeholder' => 'Prediction','class' => 'form-control prediction')) !!}
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>

    <div class="pd-20 card-box mb-30">
        <!-- Tab panes -->
        <div class="card-block table-border-style">
            <div class="chat-box">
                <div class="chat-desc customscroll">
                    <ul>
                        @foreach ($predictions as $prediction)
                        <li class="clearfix profilePicli">
                            <div class="chat-img col-md-1 user-icon">
                                <?php
                                if ($prediction->users->profile_pic != null) {
                                    $profile_pic_src = "public/profile_pic/" . $prediction->users->profile_pic;
                                } else {
                                    $profile_pic_src = "images/dummy.jpg";
                                }
                                ?>
                                <img src='{{asset($profile_pic_src)}}' class="profilepic" alt="">
                            </div>
                            <div class="chat-body clearfix chatBodyContainer">
                                <div class="containerflex">
                                    <h5 class="wc20">{{$prediction->users->name}}</h5>
                                    <span class="detaildate">{{ $prediction->prediction_date }}</span>
                                </div>
                                <span>{!! $prediction->prediction !!}</span>
                                <?php {
                                    $data = strip_tags($prediction->prediction);
                                    $like_prediction = App\Models\User_prediction::where('dailyprediction_id', $prediction->id)
                                        ->where('is_like', '=', 1)->get();
                                    $dislike_prediction = App\Models\User_prediction::where('dailyprediction_id', $prediction->id)
                                        ->where('is_like', '=', 2)->get();
                                } ?>
                                <div class="btncontainer">
                                    <div class="container">
                                        <div class="chat_time chatbutton"><i class="icon-copy fi-like likeicon" title="Like"></i>{{count($like_prediction)}}</div>
                                        <div class="chat_time chatbutton"><i class="icon-copy fi-dislike dilikeicon" title="Dislike"></i>{{ count($dislike_prediction) }}</div>
                                    </div>
                                    <div class="formbtn">
                                        @if($prediction->publish_status != 1)
                                        {!! Form::open(['method' => 'POST','route' => ['dailyprediction.publish', $prediction->id],'style'=>'display:inline']) !!}
                                        <button type="submit" data-name="{{ $data }}" class="btn alert-publish"><i class="icon-copy fa fa-upload uploadicon" title="Publish"></i></button>
                                        {!! Form::close() !!}
                                        @else
                                        @endif
                                    </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/23.0.0/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('.prediction'))
        .catch(error => {
            console.error(error);
        });
</script>

<script type="text/javascript">
    $('.alert-publish').click(function(event) {
        var form = $(this).closest("form");
        var name = $(this).data("name");
        event.preventDefault();
        swal({
            title: "Are you sure you want to publish this prediction?",
            text: `${name}`,
            icon: "warning",
            type: "warning",
            buttons: ["Cancel", "Yes!"],
            confirmButtonColor: '#17A2B8',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((isConfirm) => {
            if (isConfirm) {
                form.submit();
            }
        });
    });
</script>

<script type="text/javascript">
    $('.delete').click(function(event) {
        var form = $(this).closest("form");
        var id = $(this).data("name");
        alert(id);
        event.preventDefault();
        swal({
            title: "Are you sure you want to delete this record?",
            text: "If you delete this, it will be gone forever.",
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