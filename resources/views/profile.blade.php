@include('includes.header')
@include('includes.navbar')
@include('includes.right_sidebar')
@include('includes.left_sidebar')
<style>

</style>
<div class="main-container">
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="pd-20 card-box mb-30">
                <div class="row">
                    <div class="col-md-6">
                        <div class="">
                            <h2>Profile</h2>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-right">
                            <a class="btn btn-primary" href="{{ url('/home') }}"> Back </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-30">
                    <div class="pd-20 card-box height-100-p">
                        <div class="row">
                            <div class="profile-photo profile_photo" style="width: 170px !important; margin-top: 25px;">
                                    <h5 class="mb-20 h5 text-blue text-center">Profile Image</h5>
                                    {!! Form::open(array('route' => 'update.profilepic','method'=>'POST','enctype'=>'multipart/form-data')) !!}
                                    <label for="profile_pic" class="edit-avatar edit-profile"><i class="fa fa-pencil"></i></label>
                                    <input type="file" onChange="this.form.submit()" name="profile_pic" id="profile_pic" aria-describedby="profile_pic" style="display: none;">
                                    @if($profileData->profile_pic == null)
                                    <img src="{{asset('img/default-profile-img.png')}}" alt="" height="100%" width="100%">
                                    @else
                                    <img src="{{url('/profile_pic/'.$profileData->profile_pic)}}" alt="" height="100%" width="100%">
                                    @endif
                                    {!! Form::close() !!}
                            </div>
                        </div>
                        <h5 class="text-center h5 mb-0">{{ $profileData->name }}</h5>
                    </div>
                </div>
                <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 mb-30">
                    <div class="card-box height-100-p overflow-hidden">
                        <div class="profile-tab height-100-p">
                            <div class="tab height-100-p">
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="timeline" role="tabpanel">
                                        <div class="pd-20">
                                            <div class="profile-timeline">
                                                <h5 class="mb-20 h5 text-blue">Personal Information</h5>
                                                <div class="profile-timeline-list">
                                                    <div class="row">
                                                        <div class="col-xs-12 col-sm-12 col-md-12">
                                                            <div class="form-group">
                                                                <strong>Name :</strong>
                                                                {{$profileData->name}}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-xs-8 col-sm-8 col-md-8">
                                                            <div class="form-group">
                                                                @if($profileData->email != null)
                                                                <strong>Email Address:</strong>
                                                                    {{ $profileData->email }}
                                                                @endif
                                                                @if($profileData->phoneno != null)
                                                                <strong>Phone Number:</strong>
                                                                    {{ $profileData->phoneno }}
                                                                @endif
                                                            </div>
                                                            <div class="form-group">
                                                                <strong>Role:</strong>
                                                                {{$rolename->name}}
                                                            </div>
                                                            <div class="form-group">
                                                            @php
                                                                $joining_date = date_format($profileData->created_at, 'd-M-Y');
                                                            @endphp
                                                            <strong>Joining Date:</strong>
                                                            {{$joining_date}}
                                                            </div>
                                                            
                                                        </div>
                                                        <div class="col-xs-8 col-sm-8 col-md-8">
                                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                                                                Update Profile
                                                            </button>
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
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel">Edit Profile</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      {!! Form::model($profileData, ['method' => 'POST','route' => ['profile.update', $profileData->id]]) !!}
      <div class="modal-body">
        <div class="pd-20 card-box mb-30">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Name: </strong>
                        {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Email: </strong>
                        {!! Form::text('email', null, array('placeholder' => 'Email','class' => 'form-control', 'disabled')) !!}
                    </div>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
      </div>
      {!! Form::close() !!}
    </div>
  </div>
</div>
@include('includes.footer')