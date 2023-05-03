@include('includes.header')
@include('includes.navbar')
@include('includes.right_sidebar')
@include('includes.left_sidebar')

<div class="main-container">
    <div class="pd-20 card-box mb-30">
        <div class="row">
            <div class="col-md-6">
                <div class="">
                    <h2>Edit </h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="text-right">
                    <a class="btn btn-primary" href="{{ route('destinyno.index') }}"> Back </a>
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
        {!! Form::model($module, ['method' => 'PATCH', 'route' => ['destinyno.update', $module->id]]) !!}
        @csrf
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Numbers:</strong>
                    {!! Form::number('number', null, ['placeholder' => 'Numbers', 'class' => 'form-control', 'disabled']) !!}
                </div>
            </div>
            {{-- <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Description :</strong>
                    {!! Form::textarea('description', null, ['placeholder' => 'Description', 'class' => 'form-control description']) !!}
                </div>
            </div> --}}
            @php
                $description = explode('||', $module->description);
            @endphp
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Learn To be Description:</strong>
                    <textarea name="learn_to_be" id="" cols="30" rows="10" class="form-control description">{{$description[0]}}</textarea>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Learn Not To be Description:</strong>
                    <textarea name="learn_not_to_be" id="" cols="30" rows="10" class="form-control description">{{$description[1]}}</textarea>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>
{{-- <script src="https://cdn.ckeditor.com/ckeditor5/23.0.0/classic/ckeditor.js"></script>
<script>
ClassicEditor
    .create( document.querySelector( '.description' ) )
    .catch( error => {
    console.error( error );
    } );
</script> --}}
@include('includes.footer')
