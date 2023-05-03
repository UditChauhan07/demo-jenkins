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
                    <h2>Lifecoach Description List</h2>
                </div>
            </div>
            @can('lifecoach-create')
            <div class="col-md-6">
                <div class="text-right">
                <a class="btn btn-primary" href="{{ route('lifecoach_descriptions.create') }}">Add Lifecoach</a>
                </div>
            </div>
            @endcan
        </div>
    </div>
    <div class="pd-20 card-box mb-30">
        <table class="table table-striped">
            <tr>
                <th>S.No</th>
                <th>Star Type</th>
                <th>Star Count</th>
                <th>Number</th>
                <th>Description</th>
                <th width="280px">Action</th>
            </tr>
            @foreach ($lifecoach_descriptions as $lifecoach_description)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <?php 
                if($lifecoach_description->star_type == 1){
                    $typestar = "Green star";
                }elseif($lifecoach_description->star_type == 2){
                    $typestar = "Red star";
                }else{
                    $typestar = "Nutral";
                } 
                if($lifecoach_description->star_number == 1){
                    $starno = "One star";
                }elseif($lifecoach_description->star_number == 2){
                    $starno = "Two star";
                }elseif($lifecoach_description->star_number == 3){
                    $starno = "Three star";
                }else{
                    $starno = "Nutral";
                }  ?>
                <td>{{$typestar}}</td>
                <td>{{ $starno }}</td>
                <td>{{ $lifecoach_description->number }}</td>
                <td>{!! $lifecoach_description->description = Str::of($lifecoach_description->description)->limit(100); !!}</td>
                <td>
                    <a class="btn btn-info" href="{{ route('lifecoach_descriptions.show',$lifecoach_description->id) }}"><i class="icon-copy ion-eye"></i></a>
                    @can('lifecoach-create')
                    <a class="btn btn-primary" href="{{ route('lifecoach_descriptions.edit',$lifecoach_description->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                    @endcan
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>

@include('includes.footer')
