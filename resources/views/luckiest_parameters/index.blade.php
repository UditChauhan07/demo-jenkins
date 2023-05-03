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
                    <h2>Luckiest Parameters</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="pd-20 card-box mb-30">
        <table class="table table-striped">
            <tr>
                <th>Number</th>
                <th>Lucky Colors</th>
                <th>Lucky Gems</th>
                <th width="280px">Action</th>
            </tr>
            @foreach ($parameter as $key => $parameters)
            <tr>
                <td>{{ $parameters->number }}</td>
                <td>{{ $parameters->lucky_colours }}</td>
                <td>{{ $parameters->lucky_gems }}</td>
                <td>
                    <a class="btn btn-info" href="{{ route('luckiest_parameters.show',$parameters->id) }}"><i class="icon-copy ion-eye"></i></a>
                    @can('lucky_parameter-edit')
                    <a class="btn btn-primary" href="{{ route('luckiest_parameters.edit',$parameters->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                    @endcan
                    @if($parameters->is_active == 1)
                    @can('lucky_parameter-delete')   
                    {!! Form::open(['method' => 'DELETE','route' => ['luckiest_parameters.destroy', $parameters->id ],'style'=>'display:inline', 'onclick' => '']) !!}
                            <button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>
                    {!! Form::close() !!}
                    @endcan
                    @endif
                    @if($parameters->is_active == 0)
                    @can('lucky_parameter-delete')   
                    {!! Form::open(['method' => 'post','route' => ['luckyno.unblock', $parameters->id ],'style'=>'display:inline', 'onclick' => '']) !!}
                            <button type="submit" name="Delete" class="btn btn-danger alert-unblock"><i class="icon-copy fi-lock"></i></button>
                    {!! Form::close() !!}
                    @endcan
                    @endif
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script type="text/javascript">
    $('.alert-block').click(function(event){
        var form =  $(this).closest("form");
        var name = $(this).data("name");
        event.preventDefault();
        swal({
            title: "Are you sure you want to block this record?",
            text: "If you block this, it will be unblockable.",
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

    $('.alert-unblock').click(function(event){
        var form =  $(this).closest("form");
        var name = $(this).data("name");
        event.preventDefault();
        swal({
            title: "Are you sure you want to unblock this record?",
            text: "If you unblock this, it will be blockable.",
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
