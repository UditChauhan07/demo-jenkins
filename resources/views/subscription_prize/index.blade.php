@include('includes.header')
@include('includes.navbar')
@include('includes.right_sidebar')
@include('includes.left_sidebar')

<div class="main-container">
    <div class="pd-20 card-box mb-30">
        <div class="row">
            <div class="col-md-6">
                <div class="">
                    <h2>Subscription Prize List</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="text-right">
                    <a class="btn btn-success" href="{{route('subscription_prize.create')}}"> Create New Prediction </a>
                </div>
                @can('dailyprediction-create')
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
                            <th>No</th>
                            <th>Prize</th>
                            <th>Created Date</th>
                        </tr>
                        @foreach ($prize_list as $prize)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>$ {{ $prize->prize }}</td>
                            <td>{{ $prize->created_at->format('d-F-Y') }}</td>
                        </tr>
                        @endforeach
                    </table>
                    <div class="custom-pagination">
                        {!! $prize_list->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@include('includes.footer')