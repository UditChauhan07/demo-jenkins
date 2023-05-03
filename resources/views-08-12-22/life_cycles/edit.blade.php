@include('includes.header')
@include('includes.navbar')
@include('includes.right_sidebar')
@include('includes.left_sidebar')

<div class="main-container">
    <div class="pd-20 card-box mb-30">
        <div class="row">
            <div class="col-md-6">
                <div class="">
                    <h2>Edit Number {{ $life_cycle->number }}</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="text-right">
                <a class="btn btn-primary" href="{{ route('life_cycles.index') }}"> Back </a>
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
        {!! Form::model($life_cycle, ['method' => 'PATCH','route' => ['life_cycles.update', $life_cycle->id]]) !!}
                 @csrf
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Number:</strong>
                        {!! Form::number('number', null, array('placeholder' => 'Number','class' => 'form-control', 'disabled')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Cycle By Month :</strong>
                        {!! Form::textarea('cycle_by_month', null, array('placeholder' => 'Cycle By Month','class' => 'form-control month')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Cycle By Date :</strong>
                        {!! Form::textarea('cycle_by_date', null, array('placeholder' => 'Cycle By Date','class' => 'form-control date')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Cycle By Year :</strong>
                        {!! Form::textarea('cycle_by_year', null, array('placeholder' => 'Cycle By Year','class' => 'form-control year')) !!}
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
    .create( document.querySelector( '.month' ) )
    .catch( error => {
    console.error( error );
    } );
    ClassicEditor
    .create( document.querySelector( '.day' ) )
    .catch( error => {
    console.error( error );
    } );
    ClassicEditor
    .create( document.querySelector( '.year' ) )
    .catch( error => {
    console.error( error );
    } );
</script>
@include('includes.footer')
