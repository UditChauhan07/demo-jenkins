<!DOCTYPE html>
<html>
<head>
<title>ASTAR8XX Cosmic Calendar</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>
<style>
.main-container{
	text-align:center;
	width:100%;
	margin: 100px auto;
}
</style>
<div class="main-container">
    <div class="xs-pd-20-10 pd-ltr-20">
        
            <div class="row">
			<div class="col-md-4"></div>
                <div class="col-md-4">
				<h2>Enter DOB:</h2>
				<form method="post" action="{{route('cosmic.public')}}">
                @csrf
                <input type="date" class="form-control" name="dob" id="dob"/>
                <button type="submit" value="submit" class="btn btn-primary" style="margin-top:10px;">Submit</button>
				</form>
            </div>
			<div class="col-md-4"></div>
        </div>
		 </div>
		  </div>
		</body>
</html>