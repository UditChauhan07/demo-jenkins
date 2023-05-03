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
            <div class="row clearfix">

                <!-- <div class="col-mb-30">
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
                </div> -->
                
                <!-- Nav tabs -->
                <div class="col-lg-12 col-md-12 col-sm-12 mb-30">
                    <div class="pd-20 card-box">
                        <div style="float:right; display: inline-flex;" class="form-group">
                            <input type="text" class="form-control" style="width:140px;" name="username" id="username" placeholder="Enter Name" onkeyup="return userFilter();">
                            <input type="text" class="form-control" style="width:140px;" name="useremail" id="useremail" placeholder="Enter Email" onkeyup="return userFilter();">
                            <select name="usersubscription" class="form-control" style="width:140px; height:45px;" id="usersubscription" onchange="return userFilter();">
                                <option value="">Select...</option>
                                <option value="0">Free</option>
                                <option value="1">Paid</option>
                                <option value="9">Special offezr</option>
                            </select>
                        </div>
                        <div class="tab">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link text-blue @if (Request::query('page') === null || Request::query('page') == 'all') active @endif" data-toggle="tab" href="#all" role="tab" aria-selected="true">All</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-blue @if (Request::query('page') == 'activeUser') active @endif" data-toggle="tab" href="#activeUser" role="tab" aria-selected="true">Active</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-blue @if (Request::query('page') == 'inactiveUser') active @endif" data-toggle="tab" href="#inactiveUser" role="tab" aria-selected="true">Inactive</a>
                                </li>
                            </ul>
                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div class="tab-pane fade show @if (Request::query('page') === null || Request::query('page') == 'all') active @endif" id="all" role="tabpanel">
                                    <div class="card-block table-border-style alluserlist">
                                        <table class="table table-bordered" id="allDataTable">
                                            <tr>
                                                <th>No</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Subscription Status</th>
                                                <th>Subscription Action</th>
                                                <th>3 Month Subscription Action</th>
                                                <th width="280px">Action</th>
                                            </tr>
                                            @foreach ($users as $key=>$user)
                                            <tr>
                                                <td>{{ (($users->currentPage() * 15) - 15) + $loop->iteration }}</td>
                                                <td>{{ $user->user->name }}</td>
                                                <td>{{ $user->user->email }}</td>
                                                <td style="text-align: center;">
                                                    @if($user->user->subscription_status == 0)
                                                    Free
                                                    @else
                                                    Paid
                                                    @endif
                                                </td>
                                                <td style="text-align: center;">
                                                @if($user->user->is_active == 1)
                                                        @if($user->user->subscription_status == 0)
                                                        {!! Form::open(['method' => 'get' ,'route' => ['users.subscribe', $user->user->id], 'style'=>'display:inline']) !!}
                                                        <button type="submit" name="subscribe" class="btn btn-danger alert-subscribe">Active</button>
                                                        {!! Form::close() !!}
                                                        @elseif($user->user->subscription_status == 9)
                                                        {!! Form::open(['method' => 'get' ,'route' => ['users.unsubscribe', $user->user->id], 'style'=>'display:inline']) !!}
                                                            <button type="submit" name="subscribe" class="btn alert-unsubscribe btnBgcolor">Inactive</button>
                                                        {!! Form::close() !!}
                                                        @elseif($user->user->subscription_status == 1)
                                                        <button type="submit" name="subscribe" class="btn btn-secondary alert-unsubscribe1 ">Inactive</button>
                                                        @endif
                                                @else
                                                @endif
                                                </td>
                                                <td style="text-align: center;">
                                                @if($user->user->is_active == 1)
                                                    @if($user->user->subscription_status == 0)
                                                    {!! Form::open(['method' => 'get' ,'route' => ['users.threemonthsubscribes', $user->user->id], 'style'=>'display:inline']) !!}
                                                        <button type="submit" name="subscribe" class="btn alert-subscribe2">Active</button>
                                                    {!! Form::close() !!}
                                                    @elseif($user->user->subscription_status == 1)
                                                        <button type="submit" name="subscribe" class="btn btn-secondary alert-unsubscribe1 ">Inactive</button>
                                                    @else
                                                    {!! Form::open(['method' => 'get' ,'route' => ['users.unsubscribe', $user->user->id], 'style'=>'display:inline']) !!}
                                                        <button type="submit" name="subscribe" class="btn alert-unsubscribe2">Inactive</button>
                                                    {!! Form::close() !!}
                                                    @endif
                                                @else
                                                @endif
                                                </td>
                                                <td>
                                                    <a class="btn btn-info" href="{{ route('users.show',$user->user->id) }}"><i class="icon-copy ion-eye"></i></a>
                                                    @can('user-edit')
                                                    <a class="btn btn-primary" href="{{ route('users.edit',$user->user->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                                    @endcan
                                                    @if($user->user->is_active == 1)
                                                    @can('user-delete')   
                                                    {!! Form::open(['method' => 'DELETE','route' => ['users.destroy', $user->user->id ],'style'=>'display:inline']) !!}
                                                            <button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>
                                                    {!! Form::close() !!}
                                                    @endcan
                                                    @endif
                                                    @if($user->user->is_active == 0)
                                                    @can('user-delete')
                                                    {!! Form::open(['method' => 'post','route' => ['user.unblock', $user->user->id ],'style'=>'display:inline']) !!}
                                                            <button type="submit" name="Delete" class="btn btn-danger alert-unblock"><i class="icon-copy fi-lock"></i></button>
                                                    {!! Form::close() !!}
                                                    @endcan
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </table>
                                        <div class="custom-pagination">
                                            {!! $users->appends(['page' => 'all', 'all' => $users->currentPage(), 'activeUser' => $active_users->currentPage(), 'inactiveUser' => $inactive_users->currentPage()])->links() !!}
                                        </div>
                                    </div>
                                    <div class="card-block table-border-style userfilterlist"></div>
                                </div>
                                <div class="tab-pane @if (Request::query('page') == 'activeUser')active @endif" id="activeUser" role="tabpanel">
                                    <div class="card-block table-border-style activeuserlist">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>No</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Subscription Status</th>
                                                <th>Subscription Action</th>
                                                <th>3 Month Subscription Action</th>
                                                <th width="280px">Action</th>
                                            </tr>
                                            @foreach ($active_users as $active_user)
                                            <tr>
                                                <td>{{ (($active_users->currentPage() * 15) - 15) + $loop->iteration }}</td>
                                                <td>{{ $active_user->user->name }}</td>
                                                <td>{{ $active_user->user->email }}</td>
                                                <td style="text-align: center;">
                                                    @if($active_user->user->subscription_status == 0)
                                                    Free
                                                    @else
                                                    Paid
                                                    @endif
                                                </td>
                                                <td style="text-align: center;">
                                                    @if($active_user->user->subscription_status == 0)
                                                    {!! Form::open(['method' => 'get' ,'route' => ['users.subscribe', $active_user->user->id], 'style'=>'display:inline']) !!}
                                                    <button type="submit" name="subscribe" class="btn btn-danger alert-subscribe">Active</button>
                                                    {!! Form::close() !!}
                                                    @elseif($active_user->user->subscription_status == 9)
                                                    {!! Form::open(['method' => 'get' ,'route' => ['users.unsubscribe', $active_user->user->id], 'style'=>'display:inline']) !!}
                                                        <button type="submit" name="subscribe" class="btn alert-unsubscribe btnBgcolor">Inactive</button>
                                                    {!! Form::close() !!}
                                                    @elseif($active_user->user->subscription_status == 1)
                                                    <button type="submit" name="subscribe" class="btn btn-secondary alert-unsubscribe1 ">Inactive</button>
                                                    @else
                                                    @endif
                                                </td>
                                                <td style="text-align: center;">
                                                    @if($active_user->user->subscription_status == 0)
                                                    {!! Form::open(['method' => 'get' ,'route' => ['users.threemonthsubscribes', $active_user->user->id], 'style'=>'display:inline']) !!}
                                                        <button type="submit" name="subscribe" class="btn alert-subscribe2">Active</button>
                                                    {!! Form::close() !!}
                                                    @elseif($active_user->user->subscription_status == 1)
                                                        <button type="submit" name="subscribe" class="btn btn-secondary alert-unsubscribe1 ">Inactive</button>
                                                    @elseif($active_user->user->subscription_status == 9)
                                                    {!! Form::open(['method' => 'get' ,'route' => ['users.unsubscribe', $active_user->user->id], 'style'=>'display:inline']) !!}
                                                        <button type="submit" name="subscribe" class="btn alert-unsubscribe2">Inactive</button>
                                                    {!! Form::close() !!}
                                                    @else
                                                    @endif
                                                </td>
                                                <td>
                                                    <a class="btn btn-info" href="{{ route('users.show',$active_user->user->id) }}"><i class="icon-copy ion-eye"></i></a>
                                                    @can('user-edit')
                                                    <a class="btn btn-primary" href="{{ route('users.edit',$active_user->user->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                                    @endcan
                                                    @can('user-delete')
                                                    {!! Form::open(['method' => 'DELETE','route' => ['users.destroy', $active_user->user->id ],'style'=>'display:inline']) !!}
                                                            <button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>
                                                    {!! Form::close() !!}
                                                    @endcan
                                                </td>
                                            </tr>
                                            @endforeach
                                        </table>
                                        <div class="custom-pagination">
                                            {!! $active_users->appends(['page' => 'activeUser', 'activeUser' => $active_users->currentPage(), 'all' => $users->currentPage(), 'inactiveUser' => $inactive_users->currentPage()])->links() !!}
                                        </div>
                                    </div>
                                    <div class="card-block table-border-style activeuserfilterlist"></div>
                                </div>
                                <div class="tab-pane @if (Request::query('page') == 'inactiveUser')active @endif" id="inactiveUser" role="tabpanel">
                                    <div class="card-block table-border-style inactiveuserlist">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>No</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Subscription Status</th>
                                                <th width="280px">Action</th>
                                            </tr>
                                            @foreach ($inactive_users as $inactive_user)
                                            <tr>
                                                <td>{{ (($inactive_users->currentPage() * 15) - 15) + $loop->iteration }}</td>
                                                <td>{{ $inactive_user->user->name }}</td>
                                                <td>{{ $inactive_user->user->email }}</td>
                                                <td style="text-align: center;">
                                                    @if($inactive_user->user->subscription_status == 0)
                                                    Free
                                                    @else
                                                    Paid
                                                    @endif
                                                </td>
                                                <td>
                                                    <a class="btn btn-info" href="{{ route('users.show',$inactive_user->user->id) }}"><i class="icon-copy ion-eye"></i></a>
                                                    @can('user-edit')
                                                    <a class="btn btn-primary" href="{{ route('users.edit',$inactive_user->user->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                                    @endcan
                                                    @can('user-delete')
                                                    {!! Form::open(['method' => 'post','route' => ['user.unblock', $inactive_user->user->id ],'style'=>'display:inline']) !!}
                                                            <button type="submit" name="Delete" class="btn btn-danger alert-unblock"><i class="icon-copy fi-lock"></i></button>
                                                    {!! Form::close() !!}
                                                    @endcan
                                                </td>
                                            </tr>
                                            @endforeach
                                        </table>
                                        <div class="custom-pagination">
                                            {!! $inactive_users->appends(['page' => 'inactiveUser', 'all' => $users->currentPage(), 'activeUser' => $active_users->currentPage(), 'inactiveUser' => $inactive_users->currentPage()])->links() !!}
                                        </div>
                                    </div>
                                    <div class="card-block table-border-style inactiveuserfilterlist"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
            title: "Are you sure you want to block this user?",
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
            title: "Are you sure you want to unblock this user?",
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

<script type="text/javascript">
    $('.alert-subscribe').click(function(event) {
        var form = $(this).closest("form");
        var name = $(this).data("name");
        event.preventDefault();
        swal({
            title: "Are you sure you want to Activate subscription Of this user?",
            icon: "warning",
            type: "warning",
            buttons: ["Cancel", "Yes!"],
            confirmButtonColor: '#17A2B8',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((willDelete) => {
            if (willDelete) {
                form.submit();
            }
        });
    });
    $('.alert-subscribe2').click(function(event) {
        var form = $(this).closest("form");
        var name = $(this).data("name");
        event.preventDefault();
        swal({
            title: "Are you sure you want to Activate subscription Of this user?",
            icon: "warning",
            type: "warning",
            buttons: ["Cancel", "Yes!"],
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

<script type="text/javascript">
    $('.alert-unsubscribe').click(function(event) {
        var form = $(this).closest("form");
        var name = $(this).data("name");
        event.preventDefault();
        swal({
            title: "Are you sure you want to Inactivate subscription of this user?",
            icon: "warning",
            type: "warning",
            buttons: ["Cancel", "Yes!"],
            confirmButtonColor: '#17A2B8',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((willDelete) => {
            if (willDelete) {
                $('#subscribe').attr('checked', false);
                form.submit();
            }
        });
    });
    $('.alert-unsubscribe2').click(function(event) {
        var form = $(this).closest("form");
        var name = $(this).data("name");
        event.preventDefault();
        swal({
            title: "Are you sure you want to Inactivate subscription of this user?",
            icon: "warning",
            type: "warning",
            buttons: ["Cancel", "Yes!"],
            confirmButtonColor: '#17A2B8',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((willDelete) => {
            if (willDelete) {
                $('#subscribe').attr('checked', false);
                form.submit();
            }
        });
    });

    $('.alert-unsubscribe1').click(function(event) {
        swal({
            title: "The user has an active paid subscription, which unfortunately cannot be canceled at this time.",
            icon: "warning",
            type: "warning",
            buttons:"Ok",
        });
    });
</script>
<script type="text/javascript">
    function userFilter(pageNumber,pageNumber1,pageNumber2)
    {
        let username = $('#username').val();
        let useremail = $('#useremail').val();
        let usersubscription = document.getElementById("usersubscription").value;
        let page;
        let page1;
        let page2;
        if(pageNumber){
            page = pageNumber;
        }else{
            page = 1;
        }
        if(pageNumber1){
            page1 = pageNumber1;
        }else{
            page1 = 1;
        }
        if(pageNumber2){
            page2 = pageNumber2;
        }else{
            page2 = 1;
        }
        $.ajax({
            type: 'POST',
            url: "{{url('userfilter')}}",
            data: {
                "_token": "{{ csrf_token() }}",
                "username" : username,
                "useremail": useremail,
                "usersubscription": usersubscription,
                "pageNumber" : page,
                "pageNumber1" : page1,
                "pageNumber2" : page2,
            },
            success: function(data)
            {
                console.log(data);
                if(data.status == 1){
                // all users
                var $x = 1;
                var html = '';
                html += '<table class="table table-bordered" id="filterDataTable"><tr><th>No</th><th>Name</th><th>Email</th>';
                html += '<th>Subscription Status</th><th>Subscription Action</th><th>3 Month Subscription Action</th><th width="280px">Action</th></tr>';
                if(data.filterusers.data.length > 0){
                $.each(data.filterusers.data, function(k, v) {
                    var iteration = ((page * 15)- 15) + $x;
                html += '<tr><td>'+iteration+'</td><td>'+v.user.name+'</td>';
                html += '<td>'+v.user.email+'</td><td style="text-align: center;">';
                if(v.user.subscription_status == 0){
                    html += 'Free';
                }else{
                    html += 'Paid';
                }
                html += '</td><td style="text-align: center;">'
                if(v.user.is_active == 1){
                    if(v.user.subscription_status == 0)
                    {
                    html += '<form method="get" action="{{url('subscribe')}}/'+v.user.id+'" style="display:inline">'
                    html += '<button type="submit" name="subscribe" class="btn btn-danger alert-subscribe">Active</button>';
                    html += '{!! Form::close() !!}';
                    }else{
                        if(v.user.subscription_status == 9)
                        {
                            html += '<form method="get" action="{{url('unsubscribe')}}/'+v.user.id+'" style="display:inline">';
                            html += '<button type="submit" name="subscribe" class="btn alert-unsubscribe btnBgcolor">Inactive</button>{!! Form::close() !!}';
                        }else {
                            if(v.user.subscription_status == 1)
                            {
                                html += '<button type="submit" name="subscribe" class="btn btn-secondary alert-unsubscribe1 ">Inactive</button>';
                            }
                        }
                    }
                }
                    html += '</td><td style="text-align: center;">';
                    if(v.user.is_active == 1){
                        if(v.user.subscription_status == 0)
                        {
                            html += '<form method="get" action="{{url('threemonthsubscribes')}}/'+v.user.id+'" style="display:inline">';
                            html += '<button type="submit" name="subscribe" value="'+v.user.id+'" class="btn alert-subscribe2">Active</button>{!! Form::close() !!}';
                        }else{
                            if(v.user.subscription_status == 1){
                            html += '<button type="submit" name="subscribe" class="btn btn-secondary alert-unsubscribe1 ">Inactive</button>';
                            }else{
                            html += '<form method="get" action="{{url('unsubscribe')}}/'+v.user.id+'" style="display:inline">'
                            html += '<button type="submit" name="subscribe" class="btn alert-unsubscribe2">Inactive</button></form>';
                            }
                        }
                    }
                    html += '</td><td><a class="btn btn-info" href="{{url('user_show')}}/'+v.user.id+'"><i class="icon-copy ion-eye"></i></a>';
                    html += '@can("user-edit")<a class="btn btn-primary" href="{{ url('user_edit')}}/'+v.user.id+'"><i class="icon-copy ti-pencil-alt"></i></a>';
                    html += '@endcan';
                    if(v.user.is_active == 1)
                    {
                        html += '@can("user-delete")<form method="get" action="{{url('users_destroy')}}/'+v.user.id+'" style="display:inline">';
                        html += '<button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>{!! Form::close() !!}';
                        html += '@endcan';
                    }
                    if(v.user.is_active == 0)
                    {
                        html += '@can("user-delete")<form method="get" action="{{url('user_unblock')}}/'+v.user.id+'" style="display:inline">';
                        html += '<button type="submit" name="Delete" class="btn btn-danger alert-unblock"><i class="icon-copy fi-lock"></i></button>{!! Form::close() !!}@endcan';
                    }
                    html += '</td></tr>';
                    $x++;
                });
            }else{
                html += '<tr><td colspan="7" style="text-align: center;"><h5>No data found</h5></td></tr>'
            }
                    html += '</table>';
                    html += data.pagination;

                // active users
                var $y = 1;
                var html1 = '';
                html1 += '<table class="table table-bordered" id="filterDataTable"><tr><th>No</th><th>Name</th><th>Email</th>';
                html1 += '<th>Subscription Status</th><th>Subscription Action</th><th>3 Month Subscription Action</th><th width="280px">Action</th></tr>';
                if(data.filterActiveusers.data.length > 0){
                $.each(data.filterActiveusers.data, function(k1, v1) {
                var iteration1 = ((page1 * 15)- 15) + $y;
                html1 += '<tr><td>'+iteration1+'</td><td>'+v1.user.name+'</td>';
                html1 += '<td>'+v1.user.email+'</td><td style="text-align: center;">';
                if(v1.user.subscription_status == 0){
                    html1 += 'Free';
                }else{
                    html1 += 'Paid';
                }
                html1 += '</td><td style="text-align: center;">'
                if(v1.user.is_active == 1){
                    if(v1.user.subscription_status == 0)
                    {
                    html1 += '<form method="get" action="{{url('subscribe')}}/'+v1.user.id+'" style="display:inline">'
                    html1 += '<button type="submit" name="subscribe" class="btn btn-danger alert-subscribe">Active</button>';
                    html1 += '{!! Form::close() !!}';
                    }else{
                        if(v1.user.subscription_status == 9)
                        {
                            html1 += '<form method="get" action="{{url('unsubscribe')}}/'+v1.user.id+'" style="display:inline">';
                            html1 += '<button type="submit" name="subscribe" class="btn alert-unsubscribe btnBgcolor">Inactive</button>{!! Form::close() !!}';
                        }else {
                            if(v1.user.subscription_status == 1)
                            {
                                html1 += '<button type="submit" name="subscribe" class="btn btn-secondary alert-unsubscribe1 ">Inactive</button>';
                            }
                        }
                    }
                }
                    html1 += '</td><td style="text-align: center;">';
                    if(v1.user.is_active == 1){
                        if(v1.user.subscription_status == 0)
                        {
                            html1 += '<form method="get" action="{{url('threemonthsubscribes')}}/'+v1.user.id+'" style="display:inline">';
                            html1 += '<button type="submit" name="subscribe" value="'+v1.user.id+'" class="btn alert-subscribe2">Active</button>{!! Form::close() !!}';
                        }else if(v1.user.subscription_status == 1){
                            html1 += '<button type="submit" name="subscribe" class="btn btn-secondary alert-unsubscribe1 ">Inactive</button>';
                            
                        }else{
                            html1 += '<form method="get" action="{{url('unsubscribe')}}/'+v1.user.id+'" style="display:inline">'
                            html1 += '<button type="submit" name="subscribe" class="btn alert-unsubscribe2">Inactive</button></form>';
                        }
                        
                    }
                    html1 += '</td><td><a class="btn btn-info" href="{{url('user_show')}}/'+v1.user.id+'"><i class="icon-copy ion-eye"></i></a>';
                    html1 += '@can("user-edit")<a class="btn btn-primary" href="{{ url('user_edit')}}/'+v1.user.id+'"><i class="icon-copy ti-pencil-alt"></i></a>';
                    html1 += '@endcan';
                    if(v1.user.is_active == 1)
                    {
                        html1 += '@can("user-delete")<form method="get" action="{{url('users_destroy')}}/'+v1.user.id+'" style="display:inline">';
                        html1 += '<button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>{!! Form::close() !!}';
                        html1 += '@endcan';
                    }
                    if(v1.user.is_active == 0)
                    {
                        html1 += '@can("user-delete")<form method="get" action="{{url('user_unblock')}}/'+v1.user.id+'" style="display:inline">';
                        html1 += '<button type="submit" name="Delete" class="btn btn-danger alert-unblock"><i class="icon-copy fi-lock"></i></button>{!! Form::close() !!}@endcan';
                    }
                    html1 += '</td></tr>';
                    $y++;
                });
            }else{
                html1 += '<tr><td colspan="7" style="text-align: center;"><h5>No data found</h5></td></tr>'
            }
                    html1 += '</table>';
                    html1 += data.pagination1;

                // inactive users
                var $z = 1;
                var html2 = '';
                html2 += '<table class="table table-bordered" id="filterDataTable"><tr><th>No</th><th>Name</th><th>Email</th>';
                html2 += '<th>Subscription Status</th><th>Subscription Action</th><th>3 Month Subscription Action</th><th width="280px">Action</th></tr>';
                if(data.filterInactiveusers.data.length > 0){
                $.each(data.filterInactiveusers.data, function(k2, v2) {
                var iteration2 = ((page2 * 15)- 15) + $z;
                html2 += '<tr><td>'+iteration2+'</td><td>'+v2.user.name+'</td>';
                html2 += '<td>'+v2.user.email+'</td><td style="text-align: center;">';
                if(v2.user.subscription_status == 0){
                    html2 += 'Free';
                }else{
                    html2 += 'Paid';
                }
                html2 += '</td><td style="text-align: center;">'
                if(v2.user.is_active == 1){
                    if(v2.user.subscription_status == 0)
                    {
                    html2 += '<form method="get" action="{{url('subscribe')}}/'+v2.user.id+'" style="display:inline">'
                    html2 += '<button type="submit" name="subscribe" class="btn btn-danger alert-subscribe">Active</button>';
                    html2 += '{!! Form::close() !!}';
                    }else{
                        if(v2.user.subscription_status == 9)
                        {
                            html2 += '<form method="get" action="{{url('unsubscribe')}}/'+v2.user.id+'" style="display:inline">';
                            html2 += '<button type="submit" name="subscribe" class="btn alert-unsubscribe btnBgcolor">Inactive</button>{!! Form::close() !!}';
                        }else {
                            if(v2.user.subscription_status == 1)
                            {
                                html2 += '<button type="submit" name="subscribe" class="btn btn-secondary alert-unsubscribe1 ">Inactive</button>';
                            }
                        }
                    }
                }
                    html2 += '</td><td style="text-align: center;">';
                    if(v2.user.is_active == 1){
                        if(v2.user.subscription_status == 0)
                        {
                            html2 += '<form method="get" action="{{url('threemonthsubscribes')}}/'+v2.user.id+'" style="display:inline">';
                            html2 += '<button type="submit" name="subscribe" value="'+v2.user.id+'" class="btn alert-subscribe2">Active</button>{!! Form::close() !!}';
                        }else{
                            if(v2.user.subscription_status == 1){
                            html2 += '<button type="submit" name="subscribe" class="btn btn-secondary alert-unsubscribe1 ">Inactive</button>';
                            }else{
                            html2 += '<form method="get" action="{{url('unsubscribe')}}/'+v2.user.id+'" style="display:inline">'
                            html2 += '<button type="submit" name="subscribe" class="btn alert-unsubscribe2">Inactive</button></form>';
                            }
                        }
                    }
                    html2 += '</td><td><a class="btn btn-info" href="{{url('user_show')}}/'+v2.user.id+'"><i class="icon-copy ion-eye"></i></a>';
                    html2 += '@can("user-edit")<a class="btn btn-primary" href="{{ url('user_edit')}}/'+v2.user.id+'"><i class="icon-copy ti-pencil-alt"></i></a>';
                    html2 += '@endcan';
                    if(v2.user.is_active == 1)
                    {
                        html2 += '@can("user-delete")<form method="get" action="{{url('users_destroy')}}/'+v2.user.id+'" style="display:inline">';
                        html2 += '<button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>{!! Form::close() !!}';
                        html2 += '@endcan';
                    }
                    if(v2.user.is_active == 0)
                    {
                        html2 += '@can("user-delete")<form method="get" action="{{url('user_unblock')}}/'+v2.user.id+'" style="display:inline">';
                        html2 += '<button type="submit" name="Delete" class="btn btn-danger alert-unblock"><i class="icon-copy fi-lock"></i></button>{!! Form::close() !!}@endcan';
                    }
                    html2 += '</td></tr>';
                    $z++;
                });
            }else{
                html2 += '<tr><td colspan="7" style="text-align: center;"><h5>No data found</h5></td></tr>'
            }
                    html2 += '</table>';
                    html2 += data.pagination2;

                $(".alluserlist").hide();
                $(".userfilterlist").html(html);
                $(".userfilterlist").show();

                $(".activeuserlist").hide();
                $(".activeuserfilterlist").html(html1);
                $(".activeuserfilterlist").show();

                $(".inactiveuserlist").hide();
                $(".inactiveuserfilterlist").html(html2);
                $(".inactiveuserfilterlist").show();
            }else{
                $(".alluserlist").show();
                $(".userfilterlist").hide();

                $(".activeuserlist").show();
                $(".activeuserfilterlist").hide();

                $(".inactiveuserlist").show();
                $(".inactiveuserfilterlist").hide();
            }
            }
        });
    }
    
    $('body .userfilterlist, .customPagination').on('click','.page-link', function(event){
        event.preventDefault();
        var pageNumber = $(this).attr('href').split('page=')[1];
        var pageNumber1 = 1;
        var pageNumber2 = 1;

        userFilter(pageNumber,pageNumber1,pageNumber2);
    });
    $('body .activeuserfilterlist, .customPagination1').on('click','.page-link', function(event){
        event.preventDefault();
        var pageNumber = 1;
        var pageNumber1 = $(this).attr('href').split('page=')[1];
        var pageNumber2 = 1;
        userFilter(pageNumber,pageNumber1,pageNumber2);
    });
    $('body .inactiveuserfilterlist, .customPagination2').on('click','.page-link', function(event){
        event.preventDefault();
        var pageNumber2 = $(this).attr('href').split('page=')[1];
        var pageNumber = 1;
        var pageNumber1 = 1;
        userFilter(pageNumber,pageNumber1,pageNumber2);
    });
</script>
@include('includes.footer')