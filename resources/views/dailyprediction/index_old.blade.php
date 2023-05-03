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
                    <a class="btn btn-success" href="{{route('dailyprediction.create')}}"> Create New Prediction </a>
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
                                <th>No</th>
                                <th>Date</th>
                                <th>Prediction</th>
                                <th width="280px">Action</th>
                             </tr>
                                @foreach ($predictions as $prediction)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $prediction->prediction_date }}</td>
                                <td>{!! $prediction->prediction !!}</td>
                                <td>
                                <?php {
                                    $data = strip_tags($prediction->prediction);
                                } ?>
                                <a class="btn btn-info" href="{{route('dailyprediction.show',$prediction->id)}}"><i class="icon-copy ion-eye"></i></a>
                                @if($prediction->publish_status == 0)
                                {!! Form::open(['method' => 'POST','route' => ['dailyprediction.publish', $prediction->id],'style'=>'display:inline']) !!}
                                        <button type="submit" data-name="{{ $data }}" class="btn btn-success alert-publish">Publish</button>
                                    {!! Form::close() !!}                                
                                @else
                                <a class="btn btn-primary" href="{{route('prediction.report',$prediction->id)}}">Report</a>
                                @endif
                                {!! Form::open(['method' => 'DELETE','route' => ['dailyprediction.destroy', $prediction->id],'style'=>'display:inline']) !!}
                                        {!! Form::submit('Delete', ['class' => 'btn btn-danger delete']) !!}
                                        <button type="submit" data-name="{{ $prediction->id }}" class="btn btn-danger delete">Publish</button>
                                {!! Form::close() !!}
                                </td>
                            </tr>
                                 @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<script type="text/javascript">
    $('.alert-publish').click(function(event){
        var form =  $(this).closest("form");
        var name = $(this).data("name");
        event.preventDefault();
        swal({
            title: "Are you sure you want to publish this prediction?",
            text: `${name}`,
            icon: "warning",
            type: "warning",
            buttons: ["Cancel","Yes!"],
            confirmButtonColor: '#17A2B8',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((isConfirm) => {
            if(isConfirm)
            {
                form.submit();
            }
        });
    });
</script>

<script type="text/javascript">
    $('.delete').click(function(event){
        var form =  $(this).closest("form");
        var id = $(this).data("name");
        alert(id)
        event.preventDefault();
        swal({
            title: "Are you sure you want to delete this record?",
            text: "If you delete this, it will be gone forever.",
            icon: "warning",
            type: "warning",
            buttons: ["Cancel","Yes!"],
            confirmButtonColor: '#17A2B8',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((willDelete) => {
            if (willDelete) {
                form.submit();
            }
        });
    });
</script>
@include('includes.footer')
