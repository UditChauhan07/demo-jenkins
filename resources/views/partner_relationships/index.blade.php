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
                    <h2>Partner Relationship</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="pd-20 card-box mb-30">
        <table class="table table-striped">
            <tr>
                <th>Number</th>
                <th>Mate Number</th>
                <th>Description</th>
                <th width="280px">Action</th>
            </tr>
            @foreach ($partner_relationship as $partner_relationships)
            <tr>
                <td>{{ $partner_relationships->number }}</td>
                <td>{{ $partner_relationships->mate_number }}</td>
                <td>{!! $partner_relationships->description = Str::of($partner_relationships->description)->limit(90); !!}</td>
                <td>
                    <a class="btn btn-info" href="{{ route('partner_relationships.show',$partner_relationships->id) }}"><i class="icon-copy ion-eye"></i></a>
                    @can('partner_relation-edit')
                    <a class="btn btn-primary" href="{{ route('partner_relationships.edit',$partner_relationships->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                    @endcan
                    @if($partner_relationships->is_active == 1)
                    @can('partner_relation-delete')   
                    {!! Form::open(['method' => 'DELETE','route' => ['partner_relationships.destroy', $partner_relationships->id ],'style'=>'display:inline', 'onclick' => '']) !!}
                            <button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>
                    {!! Form::close() !!}
                    @endcan
                    @endif
                    @if($partner_relationships->is_active == 0)
                    @can('partner_relation-delete')   
                    {!! Form::open(['method' => 'post','route' => ['relation.unblock', $partner_relationships->id ],'style'=>'display:inline', 'onclick' => '']) !!}
                            <button type="submit" name="Delete" class="btn btn-danger alert-unblock"><i class="icon-copy fi-lock"></i></button>
                    {!! Form::close() !!}
                    @endcan
                    @endif
                </td>
            </tr>
            @endforeach
        </table>
        <div class="custom-pagination">
            {{$partner_relationship->links()}}
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
