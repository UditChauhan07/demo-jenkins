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
                    <h2>Personal Month</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="pd-20 card-box mb-30">
        <table class="table table-striped">
            <tr>
                <th>Numbers</th>
                <th>Description</th>
                <th width="280px">Action</th>
            </tr>
            @foreach ($personalmonth as $personalmonths)
            <tr>
                <td>{{ $personalmonths->number }}</td>
                <td>{!! $personalmonths->description = Str::of($personalmonths->description)->limit(80); !!}</td>
                <td>
                <a class="btn btn-info" href="{{ route('personalmonth.show',$personalmonths->id) }}"><i class="icon-copy ion-eye"></i></a>
                    @can('persmonth-edit')
                    <a class="btn btn-primary" href="{{ route('personalmonth.edit',$personalmonths->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                    @endcan
                    @if($personalmonths->is_active == 1)
                    @can('persmonth-delete')   
                    {!! Form::open(['method' => 'DELETE','route' => ['personalmonth.destroy', $personalmonths->id ],'style'=>'display:inline', 'onclick' => '']) !!}
                            <button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>
                    {!! Form::close() !!}
                    @endcan
                    @endif
                    @if($personalmonths->is_active == 0)
                    @can('persmonth-delete')   
                    {!! Form::open(['method' => 'post','route' => ['personalmonth.unblock', $personalmonths->id ],'style'=>'display:inline', 'onclick' => '']) !!}
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
