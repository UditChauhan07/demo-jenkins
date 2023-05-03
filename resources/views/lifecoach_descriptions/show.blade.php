@include('includes.header')
@include('includes.navbar')
@include('includes.right_sidebar')
@include('includes.left_sidebar')

<div class="main-container">
    <div class="pd-20 card-box mb-30">
        <div class="row">
            <div class="col-md-6">
            <?php 
                if($lifecoach->star_type == 1){
                    $typestar = "Green star";
                }elseif($lifecoach->star_type == 2){
                    $typestar = "Red star";
                } else{
                    $typestar = "Neutral Star";
                }
                if($lifecoach->star_number == 0){
                    $starno = "";
                }elseif($lifecoach->star_number == 4){
                    $starno = "";
                }else{
                    $starno = $lifecoach->star_number;
                }  ?>
                <div class="">
                    <h2>Number: {{$lifecoach->number}} And {{$starno}} {{$typestar}}</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="text-right">
                    <a class="btn btn-primary" href="{{ route('lifecoach_descriptions.index') }}"> Back </a>
                </div>
            </div>
        </div>
    </div>
    <div class="pd-20 card-box mb-30">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Star Type:</strong>
                    {{$typestar}}
                </div>
            </div>
            @if($lifecoach->type != 2)
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <?php
                if($lifecoach->star_number == 1){
                    $starno = "One star";
                }elseif($lifecoach->star_number == 2){
                    $starno = "Two star";
                }elseif($lifecoach->star_number == 3){
                    $starno = "Three star";
                }else{
                    $starno = "Neutral";
                }  ?>
                    <strong>Star Count:</strong>
                    {{$starno}}
                </div>
            </div>
            @else
            @endif
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Number: </strong>
                    {{$lifecoach->number}}
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Description: </strong>
                    {{$lifecoach->description}}
                </div>
            </div>
        </div>
    </div>
</div>

@include('includes.footer')