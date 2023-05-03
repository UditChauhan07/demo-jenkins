@include('includes.header')
@include('includes.navbar')
@include('includes.right_sidebar')
@include('includes.left_sidebar')

<div class="main-container">
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
        <div class="pd-20 card-box mb-30">
            <div class="row">
                <div class="col-md-6">
                    <div class="">
                        <h2>System Types</h2>
                    </div>
                </div>
            </div>
        </div>
            <!-- Nav tabs -->
        <div class="pd-20 card-box mb-30">
            <ul class="nav nav-tabs  tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#pythagorean" role="tab">Pythagorean System</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#chaldean" role="tab">Chaldean System</a>
                </li>
            </ul>
                <!-- Tab panes -->
            <div class="tab-content tabs card-block">
                <div class="tab-pane active" id="pythagorean" role="tabpanel">
                    <div class="card-block table-border-style">
                        <table class="table table-bordered">
                            <tr>
                                @foreach ($palpha as $alpha)
                                <th>{{ $alpha->number }}</th>
                                @endforeach
                                </tr>
                                <tr>  
                                @foreach ($palpha as $alpha)
                                <?php
                                    $alphanumbers = explode(",", $alpha->alphabet);
                                ?>

                                <td>
                                    <ul class="custom-ullist">
                                    @foreach($alphanumbers as $adata)
                                    <li>{{ $adata }}</li>
                                    @endforeach
                                    </ul>
                                </td>

                                    @endforeach
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="tab-pane" id="chaldean" role="tabpanel">
                    <div class="card-block table-border-style">
                        <table class="table table-bordered">
                            <tr>
                               
                                @foreach ($calpha as $alphas)
                                <th>{{ $alphas->number }}</th>
                                @endforeach
                            </tr>
                            <tr>
                               
                                @foreach ($calpha as $calphas)
                                <?php
                                    $calphanumbers = explode(",",$calphas->alphabet);
                                ?>
                                <td>
                                  <ul class="custom-ullist">
                                @foreach($calphanumbers as $calphabet)
                                <li>{{ $calphabet }}</li>
                                @endforeach
                                    </ul>
                                </td>
                                    @endforeach
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
@include('includes.footer')
