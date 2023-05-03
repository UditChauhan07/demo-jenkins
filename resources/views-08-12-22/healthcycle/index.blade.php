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
                    <h2>Destiny Numbers</h2>
                </div>
            </div>
        </div>
    </div>
            <!-- Nav tabs -->
        <div class="pd-20 card-box mb-30">
            <ul class="nav nav-tabs  tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#pythagorean" role="tab">Pythagorean system</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#chaldean" role="tab">Chaldean system</a>
                </li>
            </ul>
                <!-- Tab panes -->
            <div class="tab-content tabs card-block">
                <div class="tab-pane active" id="pythagorean" role="tabpanel">
                    <div class="card-block table-border-style">
                        <table class="table table-striped">
                            <tr>
                                <th>Number</th>
                                <th>Description</th>
                                <th width="280px">Action</th>
                            </tr>
                            @foreach ($phythahealthcyc as $key => $module)
                            <tr>
                                <td>{{ $module->number }}</td>
                                <td>{!! $module->description = Str::of($module->description)->limit(90); !!}</td>
                                <td>
                                    <a class="btn btn-info" href="{{ route('healthcycle.show',$module->id) }}"><i class="icon-copy ion-eye"></i></a>
                                    
                                    <a class="btn btn-primary" href="{{ route('healthcycle.edit',$module->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                    
                                    {!! Form::open(['method' => 'DELETE','route' => ['healthcycle.destroy', $module->id],'style'=>'display:inline', 'onclick' => '']) !!}
                                            <button type="submit" value="Delete" class="btn btn-danger alert-delete"><i class="icon-copy ion-ios-trash-outline"></i></button>
                                    {!! Form::close() !!}
                                    
                                </td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
                <div class="tab-pane" id="chaldean" role="tabpanel">
                    <div class="card-block table-border-style">
                        <table class="table table-striped">
                            <tr>
                                <th>Number</th>
                                <th>Description</th>
                                <th width="280px">Action</th>
                            </tr>
                            @foreach ($chaldhealthcyc as $key => $modules)
                            <tr>
                                <td>{{ $modules->number }}</td>
                                <td>{!! $modules->description !!}</td>
                                <td>
                                    <a class="btn btn-info" href="{{ route('healthcycle.show',$modules->id) }}"><i class="icon-copy ion-eye"></i></a>
                                       
                                    <a class="btn btn-primary" href="{{ route('healthcycle.edit',$modules->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                    
                                    {!! Form::open(['method' => 'DELETE','route' => ['healthcycle.destroy', $modules->id],'style'=>'display:inline', 'onclick' => '']) !!}
                                            <button type="Submin" name="Delete" class="btn btn-danger alert-delete"><i class="icon-copy ion-ios-trash-outline"></i></button> 
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
