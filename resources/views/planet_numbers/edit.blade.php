@include('includes.header')
@include('includes.navbar')
@include('includes.right_sidebar')
@include('includes.left_sidebar')

<div class="main-container">
    <div class="pd-20 card-box mb-30">
        <div class="row">
            <div class="col-md-6">
                <div class="">
                    <h2>Edit Planet {{ $planet_number->name }}</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="text-right">
                <a class="btn btn-primary" href="{{ route('planet_numbers.index') }}"> Back </a>
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
        {!! Form::model($planet_number, ['method' => 'PATCH','route' => ['planet_numbers.update', $planet_number->id]]) !!}
                 @csrf
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Name :</strong>
                        {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control', 'disabled')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Ruling Number :</strong>
                        {!! Form::number('ruling_number', null, array('placeholder' => 'Ruling Number','class' => 'form-control', 'disabled')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Description :</strong>
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
