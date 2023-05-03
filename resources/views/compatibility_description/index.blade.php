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
                    <h2>Compatibility Descriptions</h2>
                </div>
            </div>
            @can('compatibility_desc-create')
            <div class="col-md-6">
                <div class="text-right">
                    <a class="btn btn-primary" href="{{ route('compatibility_description.create') }}">Create</a>
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
                <th>Number</th>
                <th>Description</th>
                <th width="280px">Action</th>
            </tr>
            @foreach ($compatibility_description as $compatibility_descriptions)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    @if($compatibility_descriptions->type == 1)
                    {{ $type = 'Car/Vehicle'}}
                    @elseif($compatibility_descriptions->type == 2)
                    {{ $type = 'Business'}}
                    @elseif($compatibility_descriptions->type == 3)
                    {{ $type = 'Property'}}
                    @elseif($compatibility_descriptions->type == 4)
                    {{ $type = 'Other Person'}}
                    @elseif($compatibility_descriptions->type == 5)
                    {{ $type = 'Spouse/Partner'}}
                    @elseif($compatibility_descriptions->type == 6)
                    {{ $type = 'Name Reading'}}
                    @endif
                </td>
                <td>{{$compatibility_descriptions->number}}</td>
                <td>{!! $compatibility_descriptions->description = Str::of($compatibility_descriptions->description)->limit(80); !!}</td>
                <td>
                    <a class="btn btn-info" href="{{ route('compatibility_description.show',$compatibility_descriptions->id) }}"><i class="icon-copy ion-eye"></i></a>
                    @can('compatibility_desc-edit')
                    <a class="btn btn-primary" href="{{ route('compatibility_description.edit',$compatibility_descriptions->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                    @endcan
                </td>
            </tr>
            @endforeach
        </table>
        <div class="custom-pagination">
            {{ $compatibility_description->links() }}
        </div>
    </div>
</div>

@include('includes.footer')