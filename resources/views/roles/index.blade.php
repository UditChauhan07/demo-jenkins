@include('includes.header')
@include('includes.navbar')
@include('includes.right_sidebar')
@include('includes.left_sidebar')

<div class="main-container">
    <div class="pd-20 card-box mb-30">
        <div class="row">
            <div class="col-md-6">
                <div class="">
                    <h2>Role Management</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="text-right">
                    @can('role-create')
                    <a class="btn btn-success" href="{{ route('roles.create') }}"> Create New Role </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>


        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
    <div class="pd-20 card-box mb-30">
        <table class="table table-striped">
            <tr>
                <th>No</th>
                <th>Name</th>
                <!-- <th width="280px">Action</th> -->
            </tr>
            @foreach ($roles as $key => $role)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $role->name }}</td>
                <!-- <td>
                    <a class="btn btn-info" href="{{ route('roles.show',$role->id) }}"><i class="icon-copy ion-eye"></i></a>
                    @can('role-edit')
                        <a class="btn btn-primary" href="{{ route('roles.edit',$role->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                    @endcan
                    @if($role->is_active == 1)
                    <?php $status = 0; ?>
                    @can('role-delete')   
                    {!! Form::open(['method' => 'DELETE','route' => ['roles.destroy', $role->id ],'style'=>'display:inline', 'onclick' => '']) !!}
                            <button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>
                    {!! Form::close() !!}
                    @endcan
                    @endif
                    @if($role->is_active == 0)
                    <?php $status = 1; ?>
                    @can('role-delete')   
                    {!! Form::open(['method' => 'post','route' => ['role.unblock', $role->id ],'style'=>'display:inline', 'onclick' => '']) !!}
                            <button type="submit" name="Delete" class="btn btn-danger alert-unblock"><i class="icon-copy fi-lock"></i></button>
                    {!! Form::close() !!}
                    @endcan
                    @endif
                </td> -->
            </tr>
            @endforeach
        </table>
    </div>
</div>
{!! $roles->render() !!}

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
