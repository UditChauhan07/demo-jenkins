@include('includes.header')
@include('includes.navbar')
@include('includes.right_sidebar')
@include('includes.left_sidebar')

<div class="main-container">
    <div class="pd-20 card-box mb-30">
        <div class="row">
            <div class="col-md-6">
                <div class="">
                    <h2>Daily Predictions</h2>
                </div>
            </div>
            <div class="col-md-6">
                @can('dailyprediction-create')
                <div class="text-right">
                    <a class="btn btn-primary" href="{{ route('dailyprediction.index') }}"> Back </a>
                </div>
                @endcan
            </div>
        </div>
    </div>
        @if ($message = Session::get('success'))
        <div class="alert alert-success">
        <p>{{ $message }}</p>
        </div>
        @endif

    <div class="pd-20 card-box mb-30">
            <!-- Tab panes -->
            <div class="tab-content tabs card-block">
                <div class="tab-pane active" id="all" role="tabpanel">
                    <div class="card-block table-border-style">
                        <table class="table table-bordered">
                            <tr>
                                <th>Date</th>
                                <th>Prediction</th>
                                <th>View count</th>
                                <th>Like count</th>
                                <th>Dislike count</th>
                             </tr>
                                
                            <tr>
                                <td>{{ $prediction->prediction_date }}</td>
                                <td>{!! $prediction->prediction !!}</td>
                                <td>{{ count($seen_prediction) }}</td>
                                <td>{{ count($like_prediction) }}</td>
                                <td>{{ count($dislike_prediction) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('includes.footer')
