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
            <div class="col-md-12">
                <div class="">
                    <h2>Health Reading</h2>
                </div>
            </div>
        </div>
    </div>
    <!-- Nav tabs -->
    <div class="pd-20 card-box mb-30">
        <div class="card-block table-border-style">
            <table class="table table-striped">
                <tr>
                    <th>Number</th>
                    <th>Description</th>
                    <th width="280px">Action</th>
                </tr>
                @foreach ($phythahealth as $key => $module)
                <tr>
                    <td>{{ $module->number }}</td>
                    <td>{!! $module->description = Str::of($module->description)->limit(80);!!}</td>
                    <td>
                        <a class="btn btn-info" href="{{ route('healthreading.show',$module->id) }}"><i class="icon-copy ion-eye"></i></a>
                        @can('healthreading-edit')
                        <a class="btn btn-primary" href="{{ route('healthreading.edit',$module->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                        @endcan
                        @if($module->is_active == 1)
                        @can('healthreading-delete')
                        {!! Form::open(['method' => 'DELETE','route' => ['healthreading.destroy', $module->id ],'style'=>'display:inline', 'onclick' => '']) !!}
                        <button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>
                        {!! Form::close() !!}
                        @endcan
                        @endif
                        @if($module->is_active == 0)
                        @can('healthreading-delete')
                        {!! Form::open(['method' => 'post','route' => ['healthreading.unblock', $module->id ],'style'=>'display:inline', 'onclick' => '']) !!}
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
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script type="text/javascript">
    $('.alert-block').click(function(event) {
        var form = $(this).closest("form");
        var name = $(this).data("name");
        event.preventDefault();
        swal({
            title: "Are you sure you want to block this record?",
            text: "If you block this, it will be unblockable.",
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

    $('.alert-unblock').click(function(event) {
        var form = $(this).closest("form");
        var name = $(this).data("name");
        event.preventDefault();
        swal({
            title: "Are you sure you want to unblock this record?",
            text: "If you unblock this, it will be blockable.",
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
@include('includes.footer')