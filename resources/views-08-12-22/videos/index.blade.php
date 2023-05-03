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
                    <h2>Video List</h2>
                </div>
            </div>
            @can('video-create')
            <div class="col-md-6">
                <div class="text-right">
                <a class="btn btn-primary" href="{{ route('videos.create') }}">Add Video</a>
                </div>
            </div>
            @endcan
        </div>
    </div>
    <div class="pd-20 card-box mb-30">
        <table class="table table-striped">
            <tr>
                <th>S.No</th>
                <th>Title</th>
                <th>Thumbnail</th>
                <th>Link</th>
                <th width="280px">Action</th>
            </tr>
            @foreach ($videos as $video)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $video->video_title = Str::of($video->video_title)->limit(50); }}</td>
                @if($video->video_thumbnail != null)
                <td><img src="/thumbnail/{{$video->video_thumbnail}}"alt="Video Thumbnail"></td>
                @else
                <td><img src="/thumbnail/default_thumbnail.png"alt="Video Thumbnail"></td>
                @endif
                <td>{{ $video->video_link }}</td>
                <td>
                    <a class="btn btn-info" href="{{ route('videos.show',$video->id) }}"><i class="icon-copy ion-eye"></i></a>
                    @can('video-edit')
                    <a class="btn btn-primary" href="{{ route('videos.edit',$video->id) }}"><i class="icon-copy ti-pencil-alt"></i></a>
                    @endcan
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>

@include('includes.footer')
