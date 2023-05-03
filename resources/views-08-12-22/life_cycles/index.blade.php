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
                    <h2>Life Cycle</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="pd-20 card-box mb-30">
        <table class="table table-striped">
            <tr>
                <th>Number</th>
                <th>Cycle By Month</th>
                <th>Cycle By Date</th>
                <th>Cycle By Year</th>
                <th width="280px">Action</th>
            </tr>
            @foreach ($life_cycle as $key => $life_cycles)
            <tr>
                <td>{{ $life_cycles->number }}</td>
                <td>{{ $life_cycles->cycle_by_month = Str::of($life_cycles->cycle_by_month)->limit(60); }}</td>
                <td>{{ $life_cycles->cycle_by_date = Str::of($life_cycles->cycle_by_date)->limit(60); }}</td>
                <td>{{ $life_cycles->cycle_by_year = Str::of($life_cycles->cycle_by_year)->limit(60); }}</td>
                <td>
                    <a class="btn btn-info" href="{{ route('life_cycles.show',$life_cycles->id) }}"><i class="icon-copy ion-eye"></i></a>
                    @can('life_cycle-edit')
                    <a class="btn btn-primary" href="{{ route('life_cycles.edit',$life_cycles->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                    @endcan
                    @if($life_cycles->is_active == 1)
                    @can('life_cycle-delete')   
                    {!! Form::open(['method' => 'DELETE','route' => ['life_cycles.destroy', $life_cycles->id ],'style'=>'display:inline', 'onclick' => '']) !!}
                            <button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>
                    {!! Form::close() !!}
                    @endcan
                    @endif
                    @if($life_cycles->is_active == 0)
                    @can('life_cycle-delete')   
                    {!! Form::open(['method' => 'post','route' => ['lifecycle.unblock', $life_cycles->id ],'style'=>'display:inline', 'onclick' => '']) !!}
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
