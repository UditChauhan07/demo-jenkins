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
            <div class="col-md-6">
                <div class="text-right">
                <a class="btn btn-primary" href="{{ route('compatibility_description.create') }}">Create</a>
                </div>
            </div>
        </div>
    </div>
    <div class="pd-20 card-box mb-30">
        <form method="POST" action="">
            <div class="row">
                @csrf
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Type:</strong>
                        <select id="type" class="form-control">
                            <option>Select ....</option>
                            <option value="1">Car/Vehicle</option>
                            <option value="2">Business</option>
                            <option value="3">Property</option>
                            <option value="4">Other Person</option>
                            <option value="5">Spouse/Partner</option>
                            <option value="6">Name Reading</option>
                        </select>
                    </div>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group desc">
                    
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$('#type').change(function() {
   var option = $('#type').val();
   $.ajax({
      url: "{{ url('type_description' )}}",
      method: 'post',
      data:{"_token": "{{ csrf_token() }}",
        'type':option},
      success: function(data) {
        $i = 1;
        html = '';
        html +=("<table class='table table-striped'><tr><th>S.no</th>");
        html +=("<th>Descriptions</th>");
        html +=("<th>Action</th></tr><tr>");
        $.each(data, function(k, v){
            html += "<td>"+$i+"</td>";
            html += "<td>"+v.description+"</td>";
            html += "<td><a class='btn btn-info' href='{{ route('compatibility_description.show',"+v.id+") }}'><i class='icon-copy ion-eye'></i></a></td>";
            $i++;
        })
        $('.desc').html(html);
      }
   });
});
</script>
@include('includes.footer')
