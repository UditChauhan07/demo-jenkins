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
                    <h2>Possesions</h2>
                </div>
            </div>
            @can('possesion-create')
            <div class="col-md-6">
                <div class="text-right">
                <a class="btn btn-primary" href="{{ route('possesions.create') }}">Create</a>
                </div>
            </div>
            @endcan
        </div>
    </div>
    <div class="pd-20 card-box mb-30">
        <table class="table table-striped">
            <tr>
                <th>S.No</th>
                <th>Name</th>
                <th>Description</th>
                <th width="280px">Action</th>
            </tr>
            @foreach ($possesion as $possesions)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $possesions->name }}</td>
                <td>{!! $possesions->description = Str::of($possesions->description)->limit(80); !!}</td>
                <td>
                    <a class="btn btn-info" href="{{ route('possesions.show',$possesions->id) }}"><i class="icon-copy ion-eye"></i></a>
                    @can('possesion-edit')
                    <a class="btn btn-primary" href="{{ route('possesions.edit',$possesions->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                    @endcan
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
