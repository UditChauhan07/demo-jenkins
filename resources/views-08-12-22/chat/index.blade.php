@include('includes.header')
@include('includes.navbar')
@include('includes.right_sidebar')
@include('includes.left_sidebar')

<div class="main-container">
    <div class="pd-20 card-box mb-30">
        <div class="row">
            <div class="col-md-6">
                <div class="">
                    <h2>Chat</h2>
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
            <!-- Tab panes -->
            <div class="tab-content tabs card-block">
                <div class="tab-pane active" id="all" role="tabpanel">
                    <div class="card-block table-border-style">
                        <table class="table table-bordered">
                            <tr>
                                <th>No</th>
                                <th>User</th>
                                <th>Email</th>
                                <th width="280px">Action</th>
                             </tr>
                                @foreach ($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $user->user->name }}</td>
                                <td>{{ $user->user->email }}</td>
                                <td><a href="{{route('chat.chat',$user->user->id)}}">
                                    <span class="micon dw dw-chat3" style="font-size: x-large;"></span>
                                    </a>
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

@include('includes.footer')
