@include('includes.header')
@include('includes.navbar')
@include('includes.right_sidebar')
@include('includes.left_sidebar')

<div class="main-container">
    <div class="pd-20 card-box mb-30">
        <div class="row">
            <div class="col-md-6">
                <div class="">
                    <h2>Number: {{ $module->number }} </h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="text-right">
                    <a class="btn btn-primary" href="{{ route('childrens.index') }}"> Back </a>
                </div>
            </div>
        </div>
    </div>
    <div class="pd-20 card-box mb-30">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Number:</strong>
                    {{ $module->number }}
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Description:</strong>
                    {!! $module->description !!}
                </div>
            </div>
        </div>
    </div>
</div>

@include('includes.footer')
