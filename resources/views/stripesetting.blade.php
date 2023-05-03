@include('includes.header')
@include('includes.navbar')
@include('includes.right_sidebar')
@include('includes.left_sidebar')

<div class="main-container">
    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
    @endif
    <div class="pd-20 card-box mb-30">
        <div class="row">
            <div class="col-md-6">
                <div class="">
                    <h2>Payment Setting</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="pd-20 card-box mb-30">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Stripe payment mode :</strong>
                    @foreach($stripemodeData as $stripemode)
                        @if($stripemode->current_status == 1)
                            @if($stripemode->modes_type == 1)
                                <button type="submit" name="Delete" class="btn btn-success alert-block">Test</button>
                            @else
                                <button type="submit" name="Delete" class="btn btn-success alert-block">Live</button>
                            @endif
                        @else
                        @endif
                        @if($stripemode->current_status == 0)
                            @if($stripemode->modes_type == 1)
                            {!! Form::open(['method' => 'post','route' => ['stripe.status', $stripemode->id ],'style'=>'display:inline']) !!}
                                    <button type="submit" name="Delete" class="btn btn-secondary alert-unblock">Test</button>
                            {!! Form::close() !!}
                            @else
                            {!! Form::open(['method' => 'post','route' => ['stripe.status', $stripemode->id ],'style'=>'display:inline']) !!}
                                    <button type="submit" name="Delete" class="btn btn-secondary alert-unblock">Live</button>
                            {!! Form::close() !!}
                            @endif
                        @else
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@include('includes.footer')