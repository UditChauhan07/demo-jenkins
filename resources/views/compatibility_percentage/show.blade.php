@include('includes.header')
@include('includes.navbar')
@include('includes.right_sidebar')
@include('includes.left_sidebar')

<div class="main-container">
    <div class="pd-20 card-box mb-30">
        <div class="row">
            <div class="col-md-6">
                <div class="">
                    <h2>Number:{{ $compatiblepercentage->number }}   Mate Number:{{ $compatiblepercentage->number }} </h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="text-right">
                    <a class="btn btn-primary" href="{{ route('compatibility_percentage.index') }}"> Back </a>
                </div>
            </div>
        </div>
    </div>
    <div class="pd-20 card-box mb-30">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Number:</strong>
                    {{ $compatiblepercentage->number }}
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Mate Number :</strong>
                    {!! $compatiblepercentage->mate_number !!}
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Compatibility Scale Number :</strong>
                    {{ $compatiblepercentage->compatibility_number }}
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Compatibility Percentage :</strong>
                    {{ $compatiblepercentage->compatibility_percentage }}
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Strength :</strong>
                    {{ $compatiblepercentage->strength }}
                </div>
            </div>
        </div>
    </div>
</div>

@include('includes.footer')
