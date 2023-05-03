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
                    <h2>Unfav Parameters</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="pd-20 card-box mb-30">
        <div class=" col-md-12 col-sm-12 mb-30">
            <div class="pd-20 card-box">
                <div class="tab">
                    <div class="row clearfix">
                        <div class="col-md-3 col-sm-12">
                            <ul class="nav flex-column vtabs nav-tabs customtab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" role="tab" aria-selected="true" href="#jandata1">Jan</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" role="tab" aria-selected="true" href="#febdata1">Feb</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" role="tab" aria-selected="true" href="#mardata1">Mar</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" role="tab" aria-selected="true" href="#aprdata1">Apr</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" role="tab" aria-selected="true" href="#maydata1">May</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" role="tab" aria-selected="true" href="#jundata1">Jun</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" role="tab" aria-selected="true" href="#juldata1">Jul</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" role="tab" aria-selected="true" href="#augdata1">Aug</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" role="tab" aria-selected="true" href="#sepdata1">Sep</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" role="tab" aria-selected="true" href="#octdata1">Oct</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" role="tab" aria-selected="true" href="#nevdata1">Nov</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" role="tab" aria-selected="true" href="#decdata1">Dec</a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-9 col-sm-12">
                            <div class="tab-content tabs card-block">
                                <div class="tab-pane fade show active" id="jandata1" role="tabpanel">
                                    <div class="pd-20">
                                        <table class="table table-striped">
											<tr><h3>Month Of January</h3></tr>
                                            <tr>
                                                <th scope="col">Date</th>
                                                <th scope="col">Numbers</th>
												<th scope="col">Days</th>
												<th scope="col">Months</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                            @foreach($unfavjan as $unfavjans)
                                            <tr>
                                                <td scope="row">{{$unfavjans->date}}</td>
                                                <td scope="row">{{$unfavjans->numbers}}</td>
												<td scope="row">{{$unfavjans->days}}</td>
												<td scope="row">{{$unfavjans->months}}</td>
                                                <td>
                                                   <!--  <a class="btn btn-info" href="{{ route('unfav_parameters.show',$unfavjans->id) }}"><i class="icon-copy ion-eye"></i></a> -->
                                                    @can('fav_unfav-edit')
                                                    <a class="btn btn-primary" href="{{ route('unfav_parameters.edit',$unfavjans->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                                    @endcan
                                                     @if($unfavjans->is_active == 1)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'DELETE','route' => ['unfav_parameters.destroy', $unfavjans->id ],'style'=>'display:inline', 'onclick' => '']) !!}
                                                            <button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>
                                                    {!! Form::close() !!}
                                                    @endcan
                                                    @endif
                                                    @if($unfavjans->is_active == 0)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'post','route' => ['unfav.unblock', $unfavjans->id ],'style'=>'display:inline', 'onclick' => '']) !!}
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
                                <div class="tab-pane fade" id="febdata1" role="tabpanel">
                                    <div class="pd-20">
                                        <table class="table table-striped">
											<tr><h3>Month Of February</h3></tr>
                                            <tr>
                                                <th scope="col">Date</th>
                                                <th scope="col">Numbers</th>
                                                <th scope="col">Days</th>
												<th scope="col">Months</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                            @foreach($unfavfeb as $unfavfebs)
                                            <tr>
                                                <td scope="row">{{$unfavfebs->date}}</td>
                                                <td scope="row">{{$unfavfebs->numbers}}</td>
                                                <td scope="row">{{$unfavfebs->days}}</td>
												<td scope="row">{{$unfavfebs->months}}</td>
                                                <td>
                                                    @can('fav_unfav-edit')
                                                    <a class="btn btn-primary" href="{{ route('unfav_parameters.edit',$unfavfebs->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                                    @endcan
                                                     @if($unfavfebs->is_active == 1)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'DELETE','route' => ['unfav_parameters.destroy', $unfavfebs->id ],'style'=>'display:inline', 'onclick' => '']) !!}
                                                            <button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>
                                                    {!! Form::close() !!}
                                                    @endcan
                                                    @endif
                                                    @if($unfavfebs->is_active == 0)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'post','route' => ['unfav.unblock', $unfavfebs->id ],'style'=>'display:inline', 'onclick' => '']) !!}
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
                                <div class="tab-pane fade" id="mardata1" role="tabpanel">
                                    <div class="pd-20">
                                        <table class="table table-striped">
											<tr><h3>Month Of March</h3></tr>
                                            <tr>
                                                <th scope="col">Date</th>
                                                <th scope="col">Numbers</th>
                                                <th scope="col">Days</th>
												<th scope="col">Months</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                            @foreach($unfavmar as $unfav)
                                            <tr>
                                                <td scope="row">{{$unfav->date}}</td>
                                                <td scope="row">{{$unfav->numbers}}</td>
                                                <td scope="row">{{$unfav->days}}</td>
												<td scope="row">{{$unfav->months}}</td>
                                                <td>
                                                    @can('fav_unfav-edit')
                                                    <a class="btn btn-primary" href="{{ route('unfav_parameters.edit',$unfav->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                                    @endcan
                                                     @if($unfav->is_active == 1)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'DELETE','route' => ['unfav_parameters.destroy', $unfav->id ],'style'=>'display:inline', 'onclick' => '']) !!}
                                                            <button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>
                                                    {!! Form::close() !!}
                                                    @endcan
                                                    @endif
                                                    @if($unfav->is_active == 0)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'post','route' => ['unfav.unblock', $unfav->id ],'style'=>'display:inline', 'onclick' => '']) !!}
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
                                <div class="tab-pane fade" id="aprdata1" role="tabpanel">
                                    <div class="pd-20">
                                        <table class="table table-striped">
											<tr><h3>Month Of April</h3></tr>
                                            <tr>
                                                <th scope="col">Date</th>
                                                <th scope="col">Numbers</th>
                                                <th scope="col">Days</th>
												<th scope="col">Months</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                            @foreach($unfavapr as $unfav)
                                            <tr>
                                                <td scope="row">{{$unfav->date}}</td>
                                                <td scope="row">{{$unfav->numbers}}</td>
                                                <td scope="row">{{$unfav->days}}</td>
												<td scope="row">{{$unfav->months}}</td>
                                                <td>
                                                    @can('fav_unfav-edit')
                                                    <a class="btn btn-primary" href="{{ route('unfav_parameters.edit',$unfav->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                                    @endcan
                                                     @if($unfav->is_active == 1)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'DELETE','route' => ['unfav_parameters.destroy', $unfav->id ],'style'=>'display:inline', 'onclick' => '']) !!}
                                                            <button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>
                                                    {!! Form::close() !!}
                                                    @endcan
                                                    @endif
                                                    @if($unfav->is_active == 0)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'post','route' => ['unfav.unblock', $unfav->id ],'style'=>'display:inline', 'onclick' => '']) !!}
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
                                <div class="tab-pane fade" id="maydata1" role="tabpanel">
                                    <div class="pd-20">
                                        <table class="table table-striped">
											<tr><h3>Month Of May</h3></tr>
                                            <tr>
                                                <th scope="col">Date</th>
                                                <th scope="col">Numbers</th>
                                                <th scope="col">Days</th>
												<th scope="col">Months</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                            @foreach($unfavmay as $unfav)
                                            <tr>
                                                <td scope="row">{{$unfav->date}}</td>
                                                <td scope="row">{{$unfav->numbers}}</td>
                                                <td scope="row">{{$unfav->days}}</td>
												<td scope="row">{{$unfav->months}}</td>
                                                <td>
                                                    @can('fav_unfav-edit')
                                                    <a class="btn btn-primary" href="{{ route('unfav_parameters.edit',$unfav->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                                    @endcan
                                                     @if($unfav->is_active == 1)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'DELETE','route' => ['unfav_parameters.destroy', $unfav->id ],'style'=>'display:inline', 'onclick' => '']) !!}
                                                            <button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>
                                                    {!! Form::close() !!}
                                                    @endcan
                                                    @endif
                                                    @if($unfav->is_active == 0)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'post','route' => ['unfav.unblock', $unfav->id ],'style'=>'display:inline', 'onclick' => '']) !!}
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
                                <div class="tab-pane fade" id="jundata1" role="tabpanel">
                                    <div class="pd-20">
                                        <table class="table table-striped">
											<tr><h3>Month Of June</h3></tr>
                                            <tr>
                                                <th scope="col">Date</th>
                                                <th scope="col">Numbers</th>
                                                <th scope="col">Days</th>
												<th scope="col">Months</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                            @foreach($unfavjun as $unfav)
                                            <tr>
                                                <td scope="row">{{$unfav->date}}</td>
                                                <td scope="row">{{$unfav->numbers}}</td>
                                                <td scope="row">{{$unfav->days}}</td>
												<td scope="row">{{$unfav->months}}</td>
                                                <td>
                                                    @can('fav_unfav-edit')
                                                    <a class="btn btn-primary" href="{{ route('unfav_parameters.edit',$unfav->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                                    @endcan
                                                     @if($unfav->is_active == 1)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'DELETE','route' => ['unfav_parameters.destroy', $unfav->id ],'style'=>'display:inline', 'onclick' => '']) !!}
                                                            <button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>
                                                    {!! Form::close() !!}
                                                    @endcan
                                                    @endif
                                                    @if($unfav->is_active == 0)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'post','route' => ['unfav.unblock', $unfav->id ],'style'=>'display:inline', 'onclick' => '']) !!}
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
                                <div class="tab-pane fade" id="juldata1" role="tabpanel">
                                    <div class="pd-20">
                                        <table class="table table-striped">
											<tr><h3>Month Of July</h3></tr>
                                            <tr>
                                                <th scope="col">Date</th>
                                                <th scope="col">Numbers</th>
                                                <th scope="col">Days</th>
												<th scope="col">Months</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                            @foreach($unfavjul as $unfav)
                                            <tr>
                                                <td scope="row">{{$unfav->date}}</td>
                                                <td scope="row">{{$unfav->numbers}}</td>
                                                <td scope="row">{{$unfav->days}}</td>
												<td scope="row">{{$unfav->months}}</td>
                                                <td>
                                                    @can('fav_unfav-edit')
                                                    <a class="btn btn-primary" href="{{ route('unfav_parameters.edit',$unfav->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                                    @endcan
                                                     @if($unfav->is_active == 1)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'DELETE','route' => ['unfav_parameters.destroy', $unfav->id ],'style'=>'display:inline', 'onclick' => '']) !!}
                                                            <button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>
                                                    {!! Form::close() !!}
                                                    @endcan
                                                    @endif
                                                    @if($unfav->is_active == 0)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'post','route' => ['unfav.unblock', $unfav->id ],'style'=>'display:inline', 'onclick' => '']) !!}
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
                                <div class="tab-pane fade" id="augdata1" role="tabpanel">
                                    <div class="pd-20">
                                        <table class="table table-striped">
											<tr><h3>Month Of August</h3></tr>
                                            <tr>
                                                <th scope="col">Date</th>
                                                <th scope="col">Numbers</th>
                                                <th scope="col">Days</th>
												<th scope="col">Months</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                            @foreach($unfavaug as $unfav)
                                            <tr>
                                                <td scope="row">{{$unfav->date}}</td>
                                                <td scope="row">{{$unfav->numbers}}</td>
                                                <td scope="row">{{$unfav->days}}</td>
												<td scope="row">{{$unfav->months}}</td>
                                                <td>
                                                    @can('fav_unfav-edit')
                                                    <a class="btn btn-primary" href="{{ route('unfav_parameters.edit',$unfav->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                                    @endcan
                                                     @if($unfav->is_active == 1)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'DELETE','route' => ['unfav_parameters.destroy', $unfav->id ],'style'=>'display:inline', 'onclick' => '']) !!}
                                                            <button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>
                                                    {!! Form::close() !!}
                                                    @endcan
                                                    @endif
                                                    @if($unfav->is_active == 0)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'post','route' => ['unfav.unblock', $unfav->id ],'style'=>'display:inline', 'onclick' => '']) !!}
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
                                <div class="tab-pane fade" id="sepdata1" role="tabpanel">
                                    <div class="pd-20">
                                        <table class="table table-striped">
											<tr><h3>Month Of September</h3></tr>
                                            <tr>
                                                <th scope="col">Date</th>
                                                <th scope="col">Numbers</th>
                                                <th scope="col">Days</th>
												<th scope="col">Months</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                            @foreach($unfavsep as $unfav)
                                            <tr>
                                                <td scope="row">{{$unfav->date}}</td>
                                                <td scope="row">{{$unfav->numbers}}</td>
                                                <td scope="row">{{$unfav->days}}</td>
												<td scope="row">{{$unfav->months}}</td>
                                                <td>
                                                    @can('fav_unfav-edit')
                                                    <a class="btn btn-primary" href="{{ route('unfav_parameters.edit',$unfav->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                                    @endcan
                                                     @if($unfav->is_active == 1)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'DELETE','route' => ['unfav_parameters.destroy', $unfav->id ],'style'=>'display:inline', 'onclick' => '']) !!}
                                                            <button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>
                                                    {!! Form::close() !!}
                                                    @endcan
                                                    @endif
                                                    @if($unfav->is_active == 0)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'post','route' => ['unfav.unblock', $unfav->id ],'style'=>'display:inline', 'onclick' => '']) !!}
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
                                <div class="tab-pane fade" id="octdata1" role="tabpanel">
                                    <div class="pd-20">
                                        <table class="table table-striped">
											<tr><h3>Month Of October</h3></tr>
                                            <tr>
                                                <th scope="col">Date</th>
                                                <th scope="col">Numbers</th>
                                                <th scope="col">Days</th>
												<th scope="col">Months</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                            @foreach($unfavoct as $unfav)
                                            <tr>
                                                <td scope="row">{{$unfav->date}}</td>
                                                <td scope="row">{{$unfav->numbers}}</td>
                                                <td scope="row">{{$unfav->days}}</td>
												<td scope="row">{{$unfav->months}}</td>
                                                <td>
                                                    @can('fav_unfav-edit')
                                                    <a class="btn btn-primary" href="{{ route('unfav_parameters.edit',$unfav->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                                    @endcan
                                                     @if($unfav->is_active == 1)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'DELETE','route' => ['unfav_parameters.destroy', $unfav->id ],'style'=>'display:inline', 'onclick' => '']) !!}
                                                            <button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>
                                                    {!! Form::close() !!}
                                                    @endcan
                                                    @endif
                                                    @if($unfav->is_active == 0)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'post','route' => ['unfav.unblock', $unfav->id ],'style'=>'display:inline', 'onclick' => '']) !!}
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
                                <div class="tab-pane fade" id="nevdata1" role="tabpanel">
                                    <div class="pd-20">
                                        <table class="table table-striped">
											<tr><h3>Month Of November</h3></tr>
                                            <tr>
                                                <th scope="col">Date</th>
                                                <th scope="col">Numbers</th>
                                                <th scope="col">Days</th>
												<th scope="col">Months</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                            @foreach($unfavnev as $unfav)
                                            <tr>
                                                <td scope="row">{{$unfav->date}}</td>
                                                <td scope="row">{{$unfav->numbers}}</td>
                                                <td scope="row">{{$unfav->days}}</td>
												<td scope="row">{{$unfav->months}}</td>
                                                <td>
                                                    @can('fav_unfav-edit')
                                                    <a class="btn btn-primary" href="{{ route('unfav_parameters.edit',$unfav->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                                    @endcan
                                                     @if($unfav->is_active == 1)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'DELETE','route' => ['unfav_parameters.destroy', $unfav->id ],'style'=>'display:inline', 'onclick' => '']) !!}
                                                            <button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>
                                                    {!! Form::close() !!}
                                                    @endcan
                                                    @endif
                                                    @if($unfav->is_active == 0)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'post','route' => ['unfav.unblock', $unfav->id ],'style'=>'display:inline', 'onclick' => '']) !!}
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
                                <div class="tab-pane fade" id="decdata1" role="tabpanel">
                                    <div class="pd-20">
                                        <table class="table table-striped">
											<tr><h3>Month Of December</h3></tr>
                                            <tr>
                                                <th scope="col">Date</th>
                                                <th scope="col">Numbers</th>
                                                <th scope="col">Days</th>
												<th scope="col">Months</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                            @foreach($unfavdec as $unfav)
                                            <tr>
                                                <td scope="row">{{$unfav->date}}</td>
                                                <td scope="row">{{$unfav->numbers}}</td>
                                                <td scope="row">{{$unfav->days}}</td>
												<td scope="row">{{$unfav->months}}</td>
                                                <td>
                                                    @can('fav_unfav-edit')
                                                    <a class="btn btn-primary" href="{{ route('unfav_parameters.edit',$unfav->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                                    @endcan
                                                     @if($unfav->is_active == 1)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'DELETE','route' => ['unfav_parameters.destroy', $unfav->id ],'style'=>'display:inline', 'onclick' => '']) !!}
                                                            <button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>
                                                    {!! Form::close() !!}
                                                    @endcan
                                                    @endif
                                                    @if($unfav->is_active == 0)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'post','route' => ['unfav.unblock', $unfav->id ],'style'=>'display:inline', 'onclick' => '']) !!}
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
@include('includes.footer')
