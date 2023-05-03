@include('includes.header')
@include('includes.navbar')
@include('includes.right_sidebar')
@include('includes.left_sidebar')

	<div class="main-container">
		<div class="invoice-box">
			<div class="row">
				<div class="col-xl-12 text-right">
				</div>
			</div>
			<table class="table table-bordered mt-5">
				<thead>
					<tr>
						<th>Id</th>
						<th>Name</th>
						<th>Email</th>
						<th>Created</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@forelse ($users as $user)
						<tr>
							<td>{{ $user->id }}</td>
							<td>{{ $user->name }}</td>
							<td>{{ $user->email }}</td>
							<td>{{ \Carbon\Carbon::parse($user->created_at)->diffForHumans() }}</td>
							<td><a class="btn btn-info" href="{{ url('export-pdf/'. $user->id) }}">Bill PDF</a></td>
						</tr>
					@empty
					@endforelse
				</tbody>
            </table>
		</div>
	</div>
<script>
	function print(){
		window.print()
	
	}
	print();
</script>
@include('includes.footer')