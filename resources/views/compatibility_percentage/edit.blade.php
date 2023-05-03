@include('includes.header')
@include('includes.navbar')
@include('includes.right_sidebar')
@include('includes.left_sidebar')

<div class="main-container">
    <div class="pd-20 card-box mb-30">
        <div class="row">
            <div class="col-md-6">
                <div class="">
                    <h2>Edit Compatibility Scale</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="text-right">
                <a class="btn btn-primary" href="{{ route('compatibility_percentage.index') }}"> Back </a>
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
        {!! Form::model($compatiblepercentage, ['method' => 'PATCH','route' => ['compatibility_percentage.update', $compatiblepercentage->id]]) !!}
                 @csrf
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Numbers:</strong>
                        {!! Form::number('number', null, array('placeholder' => 'Number','class' => 'form-control', 'disabled')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Mate Numbers:</strong>
                        {!! Form::number('mate_number', null, array('placeholder' => 'Mate Number','class' => 'form-control', 'disabled')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Compatibility Number:</strong>
                        {!! Form::text('compatibility_number', null, array('placeholder' => 'Compatibility Number','class' => 'form-control')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Compatibility Percentage:</strong>
                        {!! Form::number('compatibility_percentage', null, array('placeholder' => 'Compatibility Percentage','class' => 'form-control')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Strength :</strong>
                        {!! Form::text('strength', null, array('placeholder' => 'Strength','class' => 'form-control description')) !!}
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
