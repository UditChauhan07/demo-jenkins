@include('includes.header')
@include('includes.navbar')
@include('includes.right_sidebar')
@include('includes.left_sidebar')

<div class="main-container">
    <div class="pd-20 card-box mb-30">
        <div class="row">
            <div class="col-md-6">
                <div class="">
                    <h2>Edit Number {{ $module->number }}</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="text-right">
                <a class="btn btn-primary" href="{{ route('personalyear.index') }}"> Back </a>
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
        {!! Form::model($module, ['method' => 'PATCH','route' => ['personalyear.update', $module->id]]) !!}
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Number:</strong>
                        {!! Form::number('number', null, array('placeholder' => 'Number','class' => 'form-control', 'disabled')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Description:</strong>
                        {!! Form::textarea('description', null, array('placeholder' => 'Description','class' => 'form-control description')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Love Relationship:</strong>
                        {!! Form::textarea('love_relationship', null, array('placeholder' => 'Love Relationship','class' => 'form-control love')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Health:</strong>
                        {!! Form::textarea('health', null, array('placeholder' => 'Health','class' => 'form-control health')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Career:</strong>
                        {!! Form::textarea('career', null, array('placeholder' => 'Career','class' => 'form-control career')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Travel:</strong>
                        {!! Form::textarea('travel', null, array('placeholder' => 'Travel','class' => 'form-control travel')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        {!! Form::close() !!}
    </div>
</div>
<script src="https://cdn.ckeditor.com/ckeditor5/23.0.0/classic/ckeditor.js"></script>
<script>
ClassicEditor
    .create( document.querySelector( '.description' ) )
    .catch( error => {
    console.error( error );
    } );
ClassicEditor
    .create( document.querySelector( '.love' ) )
    .catch( error => {
    console.error( error );
    } );
ClassicEditor
    .create( document.querySelector( '.health' ) )
    .catch( error => {
    console.error( error );
    } );
ClassicEditor
    .create( document.querySelector( '.career' ) )
    .catch( error => {
    console.error( error );
    } );
ClassicEditor
    .create( document.querySelector( '.travel' ) )
    .catch( error => {
    console.error( error );
    } );
</script>
@include('includes.footer')
