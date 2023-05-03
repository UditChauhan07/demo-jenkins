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
                        <h2>Insert Data</h2>
                    </div>
                </div>
            </div>
        </div>
            <!-- Nav tabs -->
        <div class="pd-20 card-box mb-30">
            <ul class="nav nav-tabs  tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#addnumber" role="tab">Add Number</a>
                </li>
            </ul>
                <!-- Tab panes -->
            <div class="tab-content tabs card-block">
                <div class="tab-pane active" id="addnumber" role="tabpanel">
                    <div class="card-block table-border-style">
                    <form method="POST" action="{{ route('communs.store')}}">
                        @csrf
                            <div class="row">
                                
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <strong>Number:</strong>
                                        <input type="text" name="numbers" id="numbers" placeholder="Numbers" class="form-control">
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <strong>Description:</strong>
                                        <textarea name="description" id="description" placeholder="Description" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
</div>
	
<script src="https://cdn.ckeditor.com/ckeditor5/23.0.0/classic/ckeditor.js"></script>
<script>
ClassicEditor
.create( document.querySelector( '#description' ) )
.catch( error => {
console.error( error );
} );
</script>
@include('includes.footer')
