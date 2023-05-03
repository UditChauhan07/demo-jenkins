@include('includes.header')
@include('includes.navbar')
@include('includes.right_sidebar')
@include('includes.left_sidebar')
<div class="main-container">
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-6">
                        <div class="">
                            <h2>Lifecoach Description List</h2>
                        </div>
                    </div>
                    @can('lifecoach-create')
                    <!-- <div class="col-md-6">
                        <div class="text-right">
                            <a class="btn btn-primary" href="{{ route('lifecoach_descriptions.create') }}">Add Lifecoach</a>
                        </div>
                    </div> -->
                    @endcan
                </div>
            </div>
            @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
            @endif
            <div class="row clearfix">
                <!-- Nav tabs -->
                <div class="col-lg-12 col-md-12 col-sm-12 mb-30">
                    <div class="pd-20 card-box">
                        <div class="tab">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link text-blue @if (Request::query('page') === null || Request::query('page') == 'day') active @endif" data-toggle="tab" href="#day" role="tab" aria-selected="true">Day</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-blue @if (Request::query('page') == 'week') active @endif" data-toggle="tab" href="#week" role="tab" aria-selected="true">Week</a>
                                </li>
                            </ul>
                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div class="tab-pane fade show @if (Request::query('page') === null || Request::query('page') == 'day') active @endif" id="day" role="tabpanel">
                                    <div class="card-block table-border-style">
                                        <table class="table table-striped">
                                            <tr>
                                                <th>S.No</th>
                                                <th>Star Type</th>
                                                <th>Star Count</th>
                                                <th>Number</th>
                                                <th>Description</th>
                                                <th width="280px">Action</th>
                                            </tr>
                                            @foreach ($lifecoach_description_day as $lifecoach_description)
                                            <tr>
                                                <td>{{ (($lifecoach_description_day->currentPage() * 15) - 15) + $loop->iteration }}</td>
                                                <?php
                                                if ($lifecoach_description->star_type == 1) {
                                                    $typestar = "Green star";
                                                } elseif ($lifecoach_description->star_type == 2) {
                                                    $typestar = "Red star";
                                                } else {
                                                    $typestar = "Neutral";
                                                }
                                                if ($lifecoach_description->star_number == 1) {
                                                    $starno = "One star";
                                                } elseif ($lifecoach_description->star_number == 2) {
                                                    $starno = "Two star";
                                                } elseif ($lifecoach_description->star_number == 3) {
                                                    $starno = "Three star";
                                                } else {
                                                    $starno = "Neutral";
                                                }  ?>
                                                <td>{{$typestar}}</td>
                                                <td>{{ $starno }}</td>
                                                <td>{{ $lifecoach_description->number }}</td>
                                                <td>{!! $lifecoach_description->description = Str::of($lifecoach_description->description)->limit(100); !!}</td>
                                                <td>
                                                    <a class="btn btn-info" href="{{ route('lifecoach_descriptions.show',$lifecoach_description->id) }}"><i class="icon-copy ion-eye"></i></a>
                                                    @can('lifecoach-create')
                                                    <a class="btn btn-primary" href="{{ route('lifecoach_descriptions.edit',$lifecoach_description->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                                    @endcan
                                                </td>
                                            </tr>
                                            @endforeach
                                        </table>
                                        <div class="custom-pagination">
                                            {!! $lifecoach_description_day->appends(['page' => 'day', 'day' => $lifecoach_description_day->currentPage(), 'week' => $lifecoach_description_week->currentPage()])->links() !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade show @if (Request::query('page') == 'week') active @endif" id="week" role="tabpanel">
                                    <div class="card-block table-border-style">
                                        <table class="table table-striped">
                                            <tr>
                                                <th>S.No</th>
                                                <th>Star Type</th>
                                                <th>Number</th>
                                                <th>Description</th>
                                                <th width="280px">Action</th>
                                            </tr>
                                            @foreach ($lifecoach_description_week as $lifecoach_description)
                                            <tr>
                                                <td>{{ (($lifecoach_description_week->currentPage() * 15) - 15) + $loop->iteration }}</td>
                                                <?php
                                                if ($lifecoach_description->star_type == 1) {
                                                    $typestar = "Green star";
                                                } elseif ($lifecoach_description->star_type == 2) {
                                                    $typestar = "Red star";
                                                } else {
                                                    $typestar = "Neutral";
                                                }  ?>
                                                <td>{{$typestar}}</td>
                                                <td>{{ $lifecoach_description->number }}</td>
                                                <td>{!! $lifecoach_description->description = Str::of($lifecoach_description->description)->limit(100); !!}</td>
                                                <td>
                                                    <a class="btn btn-info" href="{{ route('lifecoach_descriptions.show',$lifecoach_description->id) }}"><i class="icon-copy ion-eye"></i></a>
                                                    @can('lifecoach-create')
                                                    <a class="btn btn-primary" href="{{ route('lifecoach_descriptions.edit',$lifecoach_description->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                                    @endcan
                                                </td>
                                            </tr>
                                            @endforeach
                                        </table>
                                        <div class="custom-pagination">
                                            {!! $lifecoach_description_week->appends(['page' => 'week', 'day' => $lifecoach_description_week->currentPage(), 'week' => $lifecoach_description_week->currentPage()])->links() !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('includes.footer')