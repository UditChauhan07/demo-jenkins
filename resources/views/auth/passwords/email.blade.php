<!DOCTYPE html>
<html>

<head>
	<!-- Basic Page Info -->
	<meta charset="utf-8">
	<title>Astar8</title>

	<!-- Site favicon -->
	<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/apple-touch-icon.png')}} ">
	<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon-32x32.png')}} ">
	<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon-16x16.png')}} ">
	<!-- Mobile Specific Metas -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<!-- Google Font -->
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="{{ asset('css/core.css') }}">
	<link rel="stylesheet" href="path/iconfont/css/iconfont.min.css">
	{{-- <link rel="stylesheet" type="text/css" href="{{ asset('css/icon-font.min.css') }}"> --}}
	<link rel="stylesheet" type="text/css" href="{{ asset('css/jquery-jvectormap-2.0.3.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.steps.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}">


	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-119386393-1"></script>
	<script>
		window.dataLayer = window.dataLayer || [];

		function gtag() {
			dataLayer.push(arguments);
		}
		gtag('js', new Date());

		gtag('config', 'UA-119386393-1');
	</script>
</head>

<body class="login-page" style="background-color: #082d52;background-repeat: no-repeat;background-size: cover;background-image:url('{{url('img/Desktop-Bg.png')}}')">
	<div class="login-wrap d-flex align-items-center flex-wrap justify-content-center">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-md-6 col-lg-7">
					<img src="{{asset('img/Logo.png')}}" alt="">
				</div>
				<div class="col-md-6 col-lg-5">
					<div class="login-box bg-white box-shadow border-radius-10">
						<div class="login-title">
							<h2 class="text-center text-primary">Login To DeskApp</h2>
						</div>
						<form method="POST" action="{{ url('forget-password') }}">
								@if ($message = Session::get('success'))
								<div class="alert alert-success">
									<p>{{ $message }}</p>
								</div>
								@endif
							@csrf
							<div class="input-group custom">
								<input id="email" type="email" placeholder="Username" class="form-control form-control-lg" @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

								@error('email')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
								@enderror
								
								<div class="input-group-append custom">
									<span class="input-group-text"><i class="icon-copy dw dw-user1"></i></span>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="input-group mb-0">
										<button type="submit" class="btn btn-primary">
											{{ __('Send Password Reset Link') }}
										</button>
									</div>
								</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- js -->
	<script src="{{ asset('js/core.js') }}"></script>
	<script src="{{ asset('js/script.min.js') }}"></script>
	<script src="{{ asset('js/process.js') }}"></script>
	<script src="{{ asset('js/layout-settings.js') }}"></script>
</body>

</html>