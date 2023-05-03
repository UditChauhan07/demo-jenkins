@include('includes.header')
@include('includes.navbar')
@include('includes.right_sidebar')
@include('includes.left_sidebar')

<div class="main-container">
    <div class="pd-20 card-box mb-30">
        <div class="row">
            <div class="col-md-6">
                <div class="">
                    <h2>Edit Number {{$lifecoach->number}}</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="text-right">
                    <a class="btn btn-primary" href="{{ route('lifecoach_descriptions.index') }}"> Back </a>
                </div>
            </div>
        </div>
    </div>
    @if (count($errors) > 0)
    <div class="alert alert-danger">
        <strong>Whoops!</strong> something went wrong.<br><br>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <div class="pd-20 card-box mb-30">
        {!! Form::model($lifecoach, ['method' => 'PATCH','route' => ['lifecoach_descriptions.update', $lifecoach->id]]) !!}

        @csrf
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Type:</strong>
                    <select class="form-control" name="type" id="type" disabled>
                        <option>Choose Type</option>
                        <option <?php if($lifecoach->type == 1){echo "selected";}?> value="1">Day</option>
                        <option <?php if($lifecoach->type == 2){echo "selected";}?> value="2">Week</option>
                    </select>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Star Type:</strong>
                    <select class="form-control" name="star_type" disabled>
                        <option>Choose Star type</option>
                        <option <?php if($lifecoach->star_type == 1){echo "selected";}?> value="1">Green Star</option>
                        <option <?php if($lifecoach->star_type == 2){echo "selected";}?> value="2">Red Star</option>
                        <option <?php if($lifecoach->star_type == 3){echo "selected";}?> value="3">Neutral</option>
                    </select>
                </div>
            </div>
            @if($lifecoach->type != 2)
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Star Count:</strong>
                    <select class="form-control" name="star_number" disabled>
                        <option>Choose Star Count</option>
                        <option <?php if($lifecoach->star_number == 1){echo "selected";}?> value="1">One Star</option>
                        <option <?php if($lifecoach->star_number == 2){echo "selected";}?> value="2">Two Star</option>
                        <option <?php if($lifecoach->star_number == 3){echo "selected";}?> value="3">Three Star</option>
                        <option <?php if($lifecoach->star_number == 0){echo "selected";}?> value="0">Neutral</option>
                    </select>
                </div>
            </div>
            @else
            @endif
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Number</strong>
                    {!! Form::text('number', null, array('placeholder' => 'Number','class' => 'form-control', 'disabled')) !!}
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Description:</strong>
                    {!! Form::textarea('description', null, array('placeholder' => 'Description','class' => 'form-control description')) !!}
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>
@include('includes.footer')