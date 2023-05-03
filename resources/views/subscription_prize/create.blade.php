@include('includes.header')
@include('includes.navbar')
@include('includes.right_sidebar')
@include('includes.left_sidebar')


<div class="main-container">
    <div class="pd-20 card-box mb-30">
        <div class="row">
            <div class="col-md-6">
                <div class="">
                    <h2>Create New Prize</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="text-right">
                    <a class="btn btn-primary" href="{{route('subscription_prize.index')}}"> Back </a>
                </div>
            </div>
        </div>
    </div>


    @if (count($errors) > 0)
    <div class="alert alert-danger">
        <strong>Whoops!</strong>Something went wrong.<br><br>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="pd-20 card-box mb-30">
        {!! Form::open(array('route' => 'subscription_prize.store','method'=>'POST')) !!}
        <div class="form-group row">
            <label class="col-sm-12 col-md-1 col-form-label label_text"><strong>Prize:</strong></label>
            <div class="col-sm-12 col-md-9">
            {!! Form::number('prize', null, array('placeholder' => '$','min'=>'0','class' => 'form-control')) !!}
            </div>
            <label class="col-sm-12 col-md-2 col-form-label label_text"><strong>per month</strong></label>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
    {!! Form::close() !!}
</div>
@include('includes.footer')