@include('includes.header')
@include('includes.navbar')
@include('includes.right_sidebar')
@include('includes.left_sidebar')

<div class="main-container">
    <div class="pd-20 card-box mb-30">
        <div class="row">
            <div class="col-md-6">
                <div class="">
                    <h2>Edit Number {{ $primaryno_type->number }}</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="text-right">
                <a class="btn btn-primary" href="{{ route('primaryno_types.index') }}"> Back </a>
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
        {!! Form::model($primaryno_type, ['method' => 'PATCH','route' => ['primaryno_types.update', $primaryno_type->id]]) !!}
                 @csrf
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Number :</strong>
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
                        <strong>Positive :</strong>
                        {!! Form::textarea('positive', null, array('placeholder' => 'Positive','class' => 'form-control positive')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Negative :</strong>
                        {!! Form::textarea('negative', null, array('placeholder' => 'Negative','class' => 'form-control nagetive')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Occupation :</strong>
                        {!! Form::textarea('occupations', null, array('placeholder' => 'Occupation','class' => 'form-control occupation')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Health :</strong>
                        {!! Form::textarea('health', null, array('placeholder' => 'Health','class' => 'form-control health')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Partners :</strong>
                        {!! Form::textarea('partners', null, array('placeholder' => 'Partners','class' => 'form-control partner')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Time of The Year :</strong>
                        {!! Form::textarea('times_of_the_year', null, array('placeholder' => 'Time of The Year','class' => 'form-control year')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Countries :</strong>
                        {!! Form::textarea('countries', null, array('placeholder' => 'Countries','class' => 'form-control country')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Tibbits :</strong>
                        {!! Form::textarea('tibbits', null, array('placeholder' => 'Tibbits','class' => 'form-control tibbit')) !!}
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
    .create( document.querySelector( '.positive' ) )
    .catch( error => {
    console.error( error );
    } );
    ClassicEditor
    .create( document.querySelector( '.nagetive' ) )
    .catch( error => {
    console.error( error );
    } );
    ClassicEditor
    .create( document.querySelector( '.occupation' ) )
    .catch( error => {
    console.error( error );
    } );
    ClassicEditor
    .create( document.querySelector( '.health' ) )
    .catch( error => {
    console.error( error );
    } );
    ClassicEditor
    .create( document.querySelector( '.partner' ) )
    .catch( error => {
    console.error( error );
    } );
    ClassicEditor
    .create( document.querySelector( '.year' ) )
    .catch( error => {
    console.error( error );
    } );
    ClassicEditor
    .create( document.querySelector( '.country' ) )
    .catch( error => {
    console.error( error );
    } );
    ClassicEditor
    .create( document.querySelector( '.tibbit' ) )
    .catch( error => {
    console.error( error );
    } );
</script>
@include('includes.footer')
