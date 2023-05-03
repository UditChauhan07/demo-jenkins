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
                    <h2>General Setting</h2>
                </div>
            </div>
        </div>
    </div>

    @if (count($errors) > 0)
    <div class="alert alert-danger">
        <strong>Whoops!</strong>Something went wrong.<br><br>
        <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
        </ul>
    </div>
    @endif

    <div class="pd-20 card-box mb-30">
    {!! Form::open(array('route' => 'general.store','method'=>'POST', 'id'=> 'form1')) !!}
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Number of New users :</strong>
                    <select name="number_of_user" id="number_of_user" class="form-control" placeholder="Select...">
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="150">150</option>
                        <option value="200">200</option>
                        <option value="250">250</option>
                        <option value="300">300</option>
                        <option value="350">350</option>
                        <option value="400">400</option>
                        <option value="450">450</option>
                        <option value="500">500</option>
                    </select>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button type="submit" class="btn btn-primary free-subscribe">Submit</button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<script type="text/javascript">
    $('.free-subscribe').click(function(event) {
        var number = document.getElementById("number_of_user").value;
        event.preventDefault();
        $.ajax({
            type: 'POST',
            url: "{{ url('free_subcheck') }}",
            data: {
                "_token": "{{ csrf_token() }}",
                "number": number,
            },
            success: function(data) {
                console.log(data);
            if(data.status == 1){
                swal({
                    title: data.message,
                    icon: "warning",
                    type: "warning",
                    buttons:"Ok",
                });
            }else if(data.status == 2){
                swal({
                    title: data.message,
                    icon: "warning",
                    type: "warning",
                    buttons: ["Cancel", "Yes!"],
                    confirmButtonColor: '#17A2B8',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((willDelete) => {
                    if (willDelete) {
                        $('#form1').submit();
                    }
                });
            }else{
                swal({
                    title: "Are you sure you want to provide 3 months free subscription to the "+number+" users?",
                    icon: "warning",
                    type: "warning",
                    buttons: ["Cancel", "Yes!"],
                    confirmButtonColor: '#17A2B8',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((willDelete) => {
                    if (willDelete) {
                        $('#form1').submit();
                    }
                });
            }
            }
        });
    });
</script>
@include('includes.footer')