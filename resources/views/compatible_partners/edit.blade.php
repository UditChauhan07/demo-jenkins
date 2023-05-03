@include('includes.header')
@include('includes.navbar')
@include('includes.right_sidebar')
@include('includes.left_sidebar')

<div class="main-container">
    <div class="pd-20 card-box mb-30">
        <div class="row">
            <div class="col-md-6">
                <div class="">
                    <h2>Edit Number {{ $compatible->number }}</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="text-right">
                <a class="btn btn-primary" href="{{ route('compatible_partners.index') }}"> Back </a>
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
        {!! Form::model($compatible, ['method' => 'PATCH','route' => ['compatible_partners.update', $compatible->id]]) !!}
                 @csrf
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Numbers:</strong>
                        {!! Form::number('number', null, array('placeholder' => 'Numbers','class' => 'form-control', 'disabled')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Description :</strong>
                        {!! Form::textarea('description', null, array('placeholder' => 'Description','class' => 'form-control description')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>More Compatible Months :</strong>
                        {!! Form::text('more_compatible_months', null, array('placeholder' => 'More Compatible Months','class' => 'form-control')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>More Compatible Dates :</strong>
                        {!! Form::text('more_compatible_dates', null, array('placeholder' => 'More Compatible Dates','class' => 'form-control')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Less Compatible Months :</strong>
                        {!! Form::text('less_compatible_months', null, array('placeholder' => 'Less Compatible Months','class' => 'form-control')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Less Compatible Dates :</strong>
                        {!! Form::text('less_compatible_dates', null, array('placeholder' => 'Less Compatible Dates','class' => 'form-control')) !!}
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
