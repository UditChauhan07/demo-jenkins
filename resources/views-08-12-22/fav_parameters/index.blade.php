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
                    <h2>Fav Parameters</h2>
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
                                    <a class="nav-link active" data-toggle="tab" role="tab" aria-selected="true" href="#jandata">Jan</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" role="tab" aria-selected="true" href="#febdata">Feb</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" role="tab" aria-selected="true" href="#mardata">Mar</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" role="tab" aria-selected="true" href="#aprdata">Apr</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" role="tab" aria-selected="true" href="#maydata">May</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" role="tab" aria-selected="true" href="#jundata">Jun</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" role="tab" aria-selected="true" href="#juldata">Jul</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" role="tab" aria-selected="true" href="#augdata">Aug</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" role="tab" aria-selected="true" href="#sepdata">Sep</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" role="tab" aria-selected="true" href="#octdata">Oct</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" role="tab" aria-selected="true" href="#nevdata">Nov</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" role="tab" aria-selected="true" href="#decdata">Dec</a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-9 col-sm-12">
                            <div class="tab-content tabs card-block">
                                <div class="tab-pane fade show active" id="jandata" role="tabpanel">
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
                                            @foreach($favjan as $favs)
                                            <tr>
                                                <td scope="row">{{$favs->date}}</td>
                                                <td scope="row">{{$favs->numbers}}</td>
												<td scope="row">{{$favs->days}}</td>
												<td scope="row">{{$favs->months}}</td>
                                                <td>
                                                    <!-- <a class="btn btn-info" href="{{ route('fav_parameters.show',$favs->id) }}"><i class="icon-copy ion-eye"></i></a> -->
                                                    @can('fav_unfav-edit')
                                                    <a class="btn btn-primary" href="{{ route('fav_parameters.edit',$favs->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                                    @endcan
                                                    @if($favs->is_active == 1)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'DELETE','route' => ['fav_parameters.destroy', $favs->id ],'style'=>'display:inline', 'onclick' => '']) !!}
                                                            <button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>
                                                    {!! Form::close() !!}
                                                    @endcan
                                                    @endif
                                                    @if($favs->is_active == 0)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'post','route' => ['fav.unblock', $favs->id ],'style'=>'display:inline', 'onclick' => '']) !!}
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
                                <div class="tab-pane fade" id="febdata" role="tabpanel">
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
                                            @foreach($favfeb as $favs)
                                            <tr>
                                                <td scope="row">{{$favs->date}}</td>
                                                <td scope="row">{{$favs->numbers}}</td>
												<td scope="row">{{$favs->days}}</td>
												<td scope="row">{{$favs->months}}</td>
                                                <td>
                                                    @can('fav_unfav-edit')
                                                    <a class="btn btn-primary" href="{{ route('fav_parameters.edit',$favs->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                                    @endcan
                                                    @if($favs->is_active == 1)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'DELETE','route' => ['fav_parameters.destroy', $favs->id ],'style'=>'display:inline', 'onclick' => '']) !!}
                                                            <button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>
                                                    {!! Form::close() !!}
                                                    @endcan
                                                    @endif
                                                    @if($favs->is_active == 0)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'post','route' => ['fav.unblock', $favs->id ],'style'=>'display:inline', 'onclick' => '']) !!}
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
                                <div class="tab-pane fade" id="mardata" role="tabpanel">
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
                                            @foreach($favmar as $favs)
                                            <tr>
                                                <td scope="row">{{$favs->date}}</td>
                                                <td scope="row">{{$favs->numbers}}</td>
												<td scope="row">{{$favs->days}}</td>
												<td scope="row">{{$favs->months}}</td>
                                                <td>
                                                    @can('fav_unfav-edit')
                                                    <a class="btn btn-primary" href="{{ route('fav_parameters.edit',$favs->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                                    @endcan
                                                    @if($favs->is_active == 1)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'DELETE','route' => ['fav_parameters.destroy', $favs->id ],'style'=>'display:inline', 'onclick' => '']) !!}
                                                            <button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>
                                                    {!! Form::close() !!}
                                                    @endcan
                                                    @endif
                                                    @if($favs->is_active == 0)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'post','route' => ['fav.unblock', $favs->id ],'style'=>'display:inline', 'onclick' => '']) !!}
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
                                <div class="tab-pane fade" id="aprdata" role="tabpanel">
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
                                            @foreach($favapr as $favs)
                                            <tr>
                                                <td scope="row">{{$favs->date}}</td>
                                                <td scope="row">{{$favs->numbers}}</td>
												<td scope="row">{{$favs->days}}</td>
												<td scope="row">{{$favs->months}}</td>
                                                <td>
                                                    @can('fav_unfav-edit')
                                                    <a class="btn btn-primary" href="{{ route('fav_parameters.edit',$favs->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                                    @endcan
                                                    @if($favs->is_active == 1)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'DELETE','route' => ['fav_parameters.destroy', $favs->id ],'style'=>'display:inline', 'onclick' => '']) !!}
                                                            <button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>
                                                    {!! Form::close() !!}
                                                    @endcan
                                                    @endif
                                                    @if($favs->is_active == 0)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'post','route' => ['fav.unblock', $favs->id ],'style'=>'display:inline', 'onclick' => '']) !!}
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
                                <div class="tab-pane fade" id="maydata" role="tabpanel">
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
                                            @foreach($favmay as $favs)
                                            <tr>
                                                <td scope="row">{{$favs->date}}</td>
                                                <td scope="row">{{$favs->numbers}}</td>
												<td scope="row">{{$favs->days}}</td>
												<td scope="row">{{$favs->months}}</td>
                                                <td>
                                                    @can('fav_unfav-edit')
                                                    <a class="btn btn-primary" href="{{ route('fav_parameters.edit',$favs->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                                    @endcan
                                                    @if($favs->is_active == 1)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'DELETE','route' => ['fav_parameters.destroy', $favs->id ],'style'=>'display:inline', 'onclick' => '']) !!}
                                                            <button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>
                                                    {!! Form::close() !!}
                                                    @endcan
                                                    @endif
                                                    @if($favs->is_active == 0)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'post','route' => ['fav.unblock', $favs->id ],'style'=>'display:inline', 'onclick' => '']) !!}
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
                                <div class="tab-pane fade" id="jundata" role="tabpanel">
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
                                            @foreach($favjun as $favs)
                                            <tr>
                                                <td scope="row">{{$favs->date}}</td>
                                                <td scope="row">{{$favs->numbers}}</td>
												<td scope="row">{{$favs->days}}</td>
												<td scope="row">{{$favs->months}}</td>
                                                <td>
                                                    @can('fav_unfav-edit')
                                                    <a class="btn btn-primary" href="{{ route('fav_parameters.edit',$favs->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                                    @endcan
                                                    @if($favs->is_active == 1)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'DELETE','route' => ['fav_parameters.destroy', $favs->id ],'style'=>'display:inline', 'onclick' => '']) !!}
                                                            <button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>
                                                    {!! Form::close() !!}
                                                    @endcan
                                                    @endif
                                                    @if($favs->is_active == 0)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'post','route' => ['fav.unblock', $favs->id ],'style'=>'display:inline', 'onclick' => '']) !!}
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
                                <div class="tab-pane fade" id="juldata" role="tabpanel">
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
                                            @foreach($favjul as $favs)
                                            <tr>
                                                <td scope="row">{{$favs->date}}</td>
                                                <td scope="row">{{$favs->numbers}}</td>
												<td scope="row">{{$favs->days}}</td>
												<td scope="row">{{$favs->months}}</td>
                                                <td>
                                                    @can('fav_unfav-edit')
                                                    <a class="btn btn-primary" href="{{ route('fav_parameters.edit',$favs->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                                    @endcan
                                                    @if($favs->is_active == 1)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'DELETE','route' => ['fav_parameters.destroy', $favs->id ],'style'=>'display:inline', 'onclick' => '']) !!}
                                                            <button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>
                                                    {!! Form::close() !!}
                                                    @endcan
                                                    @endif
                                                    @if($favs->is_active == 0)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'post','route' => ['fav.unblock', $favs->id ],'style'=>'display:inline', 'onclick' => '']) !!}
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
                                <div class="tab-pane fade" id="augdata" role="tabpanel">
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
                                            @foreach($favaug as $favs)
                                            <tr>
                                                <td scope="row">{{$favs->date}}</td>
                                                <td scope="row">{{$favs->numbers}}</td>
												<td scope="row">{{$favs->days}}</td>
												<td scope="row">{{$favs->months}}</td>
                                                <td>
                                                    @can('fav_unfav-edit')
                                                    <a class="btn btn-primary" href="{{ route('fav_parameters.edit',$favs->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                                    @endcan
                                                    @if($favs->is_active == 1)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'DELETE','route' => ['fav_parameters.destroy', $favs->id ],'style'=>'display:inline', 'onclick' => '']) !!}
                                                            <button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>
                                                    {!! Form::close() !!}
                                                    @endcan
                                                    @endif
                                                    @if($favs->is_active == 0)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'post','route' => ['fav.unblock', $favs->id ],'style'=>'display:inline', 'onclick' => '']) !!}
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
                                <div class="tab-pane fade" id="sepdata" role="tabpanel">
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
                                            @foreach($favsep as $favs)
                                            <tr>
                                                <td scope="row">{{$favs->date}}</td>
                                                <td scope="row">{{$favs->numbers}}</td>
												<td scope="row">{{$favs->days}}</td>
												<td scope="row">{{$favs->months}}</td>
                                                <td>
                                                    @can('fav_unfav-edit')
                                                    <a class="btn btn-primary" href="{{ route('fav_parameters.edit',$favs->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                                    @endcan
                                                    @if($favs->is_active == 1)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'DELETE','route' => ['fav_parameters.destroy', $favs->id ],'style'=>'display:inline', 'onclick' => '']) !!}
                                                            <button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>
                                                    {!! Form::close() !!}
                                                    @endcan
                                                    @endif
                                                    @if($favs->is_active == 0)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'post','route' => ['fav.unblock', $favs->id ],'style'=>'display:inline', 'onclick' => '']) !!}
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
                                <div class="tab-pane fade" id="octdata" role="tabpanel">
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
                                            @foreach($favoct as $favs)
                                            <tr>
                                                <td scope="row">{{$favs->date}}</td>
                                                <td scope="row">{{$favs->numbers}}</td>
												<td scope="row">{{$favs->days}}</td>
												<td scope="row">{{$favs->months}}</td>
                                                <td>
                                                    @can('fav_unfav-edit')
                                                    <a class="btn btn-primary" href="{{ route('fav_parameters.edit',$favs->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                                    @endcan
                                                    @if($favs->is_active == 1)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'DELETE','route' => ['fav_parameters.destroy', $favs->id ],'style'=>'display:inline', 'onclick' => '']) !!}
                                                            <button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>
                                                    {!! Form::close() !!}
                                                    @endcan
                                                    @endif
                                                    @if($favs->is_active == 0)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'post','route' => ['fav.unblock', $favs->id ],'style'=>'display:inline', 'onclick' => '']) !!}
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
                                <div class="tab-pane fade" id="nevdata" role="tabpanel">
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
                                            @foreach($favnev as $favs)
                                            <tr>
                                                <td scope="row">{{$favs->date}}</td>
                                                <td scope="row">{{$favs->numbers}}</td>
												<td scope="row">{{$favs->days}}</td>
												<td scope="row">{{$favs->months}}</td>
                                                <td>
                                                    @can('fav_unfav-edit')
                                                    <a class="btn btn-primary" href="{{ route('fav_parameters.edit',$favs->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                                    @endcan
                                                    @if($favs->is_active == 1)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'DELETE','route' => ['fav_parameters.destroy', $favs->id ],'style'=>'display:inline', 'onclick' => '']) !!}
                                                            <button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>
                                                    {!! Form::close() !!}
                                                    @endcan
                                                    @endif
                                                    @if($favs->is_active == 0)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'post','route' => ['fav.unblock', $favs->id ],'style'=>'display:inline', 'onclick' => '']) !!}
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
                                <div class="tab-pane fade" id="decdata" role="tabpanel">
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
                                            @foreach($favdec as $favs)
                                            <tr>
                                                <td scope="row">{{$favs->date}}</td>
                                                <td scope="row">{{$favs->numbers}}</td>
												<td scope="row">{{$favs->days}}</td>
												<td scope="row">{{$favs->months}}</td>
                                                <td>
                                                    @can('fav_unfav-edit')
                                                    <a class="btn btn-primary" href="{{ route('fav_parameters.edit',$favs->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                                                    @endcan
                                                    @if($favs->is_active == 1)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'DELETE','route' => ['fav_parameters.destroy', $favs->id ],'style'=>'display:inline', 'onclick' => '']) !!}
                                                            <button type="submit" name="Delete" class="btn btn-success alert-block"><i class="icon-copy fi-unlock"></i></button>
                                                    {!! Form::close() !!}
                                                    @endcan
                                                    @endif
                                                    @if($favs->is_active == 0)
                                                    @can('fav_unfav-delete')   
                                                    {!! Form::open(['method' => 'post','route' => ['fav.unblock', $favs->id ],'style'=>'display:inline', 'onclick' => '']) !!}
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
