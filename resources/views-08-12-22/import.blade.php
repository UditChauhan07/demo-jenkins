@include('includes.header')
@include('includes.navbar')
@include('includes.right_sidebar')
@include('includes.left_sidebar')

<div class="main-container">
    <div class="pd-20 card-box mb-30">
        <div class="row">
            <div class="col-md-6">
                <div class="">
                    <h2>Import Data</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="text-right">
                    <a class="btn btn-primary" href="{{ route('roles.index') }}"> Back </a>
                </div>
            </div>
        </div>
    </div>
    <div class="pd-20 card-box mb-30">
        <div class="row">
            <form action="{{ route('file-import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <!-- <div class="form-group">
                        <strong>Import DB Table:</strong>
                        <select name="table">
                            <option>Select Table</option>
                            @foreach ($tables as $table)
                            <option value="{{ $loop->iteration }}">{{$table}}</option>
                            @endforeach
                        </select>
                    </div> -->
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Select File:</strong>
                        <input type="file" name="file" class="" id="customFile">
                        <button class="btn btn-primary" type="submit" name="submit" id="submit">Import data</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@include('includes.footer')
