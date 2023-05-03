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
                    <h2>Compatibility Scale</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="pd-20 card-box mb-30">
        <table class="table table-striped">
            <tr>
                <th>Numbers</th>
                <th>Mate Number</th>
                <th width="280px">Action</th>
            </tr>
            @foreach ($compatiblepercentage as $compatiblepercentages)
            <tr>
                <td>{{ $compatiblepercentages->number }}</td>
                <td>{{ $compatiblepercentages->mate_number}}</td>
                <td>
                    <a class="btn btn-info" href="{{ route('compatibility_percentage.show',$compatiblepercentages->id) }}"><i class="icon-copy ion-eye"></i></a>
                @can('compatibility_percentage-edit')
                    <a class="btn btn-primary" href="{{ route('compatibility_percentage.edit',$compatiblepercentages->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                @endcan
                </td>
            </tr>
            @endforeach
        </table>
        <div class="custom-pagination">
            {{$compatiblepercentage->links()}}
        </div>
    </div>
</div>
@include('includes.footer')
