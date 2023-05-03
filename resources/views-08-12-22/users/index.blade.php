@include('includes.header')
@include('includes.navbar')
@include('includes.right_sidebar')
@include('includes.left_sidebar')

<div class="main-container">
    <div class="pd-20 card-box mb-30">
        <div class="row">
            <div class="col-md-6">
                <div class="">
                    <h2>Users Management</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="text-right">
                    @can('user-create')
                    <a class="btn btn-success" href="{{ route('users.create') }}"> Create New User </a>
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
        <div class="col-mb-30" >
            <form method="POST" action="filetype" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <div class=" form-group">
                            <label>Export User List:- </label>
                            <select name="listtype" class="form-control">
                                <option value="1">All</option>
                                <option value="2">Active</option>
                                <option value="3">Inactive</option>
                            </select>
                        </div>
                        
                    </div>
                    <div class="col-md-4 m-auto p-0">
                            <button type="submit" name="submit" class="btn btn-success">Export</button>
                        </div>
                </div>
            </form>
        </div>
            <!-- Nav tabs -->
            <ul class="nav nav-tabs  tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#all" role="tab">All</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#active" role="tab">Active</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#inactive" role="tab">Inactive</a>
                </li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content tabs card-block">
                <div class="tab-pane active" id="all" role="tabpanel">
                    <div class="card-block table-border-style">
                        <table class="table table-bordered">
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th width="280px">Action</th>
                             </tr>
                                @foreach ($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $user->user->name }}</td>
                                <td>{{ $user->user->email }}</td>
                                <td>
                                <a class="btn btn-info" href="{{ route('users.show',$user->user->id) }}"><i class="icon-copy ion-eye"></i></a>
                                @can('user-edit')
                                <a class="btn btn-primary" href="{{ route('users.edit',$user->user->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                @endcan
                                @can('user-delete')
                                    {!! Form::open(['method' => 'DELETE','route' => ['users.destroy', $user->user->id],'style'=>'display:inline']) !!}
                                        <button type="submit" name="Delete" class="btn btn-danger alert-delete"><i class="icon-copy ion-ios-trash-outline"></i></button>
                                    {!! Form::close() !!}
                                @endcan
                                </td>
                            </tr>
                                 @endforeach
                        </table>
                    </div>
                </div>
                <div class="tab-pane" id="active" role="tabpanel">
                    <div class="card-block table-border-style">
                        <table class="table table-bordered">
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th width="280px">Action</th>
                             </tr>
                                @foreach ($users as $user)
                                @if($user->user->is_active == 1)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $user->user->name }}</td>
                                <td>{{ $user->user->email }}</td>
                                <td>
                                <a class="btn btn-info" href="{{ route('users.show',$user->user->id) }}"><i class="icon-copy ion-eye"></i></a>
                                @can('user-edit')
                                <a class="btn btn-primary" href="{{ route('users.edit',$user->user->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                @endcan
                                @can('user-delete')
                                    {!! Form::open(['method' => 'DELETE','route' => ['users.destroy', $user->user->id],'style'=>'display:inline']) !!}
                                        {!! Form::submit('Delete', ['class' => 'btn btn-danger alert-delete']) !!}
                                    {!! Form::close() !!}
                                @endcan
                                </td>
                            </tr>
                                @endif
                                 @endforeach
                        </table>
                    </div>
                </div>
                <div class="tab-pane" id="inactive" role="tabpanel">
                    <div class="card-block table-border-style">
                        <table class="table table-bordered">
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th width="280px">Action</th>
                             </tr>
                                @foreach ($users as $user)
                                @if($user->user->is_active == 0)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $user->user->name }}</td>
                                <td>{{ $user->user->email }}</td>
                                <td>
                                <a class="btn btn-info" href="{{ route('users.show',$user->user->id) }}"><i class="icon-copy ion-eye"></i></a>
                                @can('user-edit')
                                <a class="btn btn-primary" href="{{ route('users.edit',$user->user->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                @endcan
                                @can('user-delete')
                                    {!! Form::open(['method' => 'DELETE','route' => ['users.destroy', $user->user->id],'style'=>'display:inline']) !!}
                                        {!! Form::submit('Delete', ['class' => 'btn btn-danger alert-delete']) !!}
                                    {!! Form::close() !!}
                                @endcan
                                </td>
                            </tr>
                            @endif
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
    $('.alert-delete').click(function(event){
        var form =  $(this).closest("form");
        var name = $(this).data("name");
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
