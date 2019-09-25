
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Aanime_Laravel</title>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <META HTTP-EQUIV="Expires" CONTENT="-1">
    <meta name="keywords" content="watch movies online, free movies online, free hd movies, full hd movies, best site for movies, watch free movies online, streaming free movies, full hd movies, free movies, cinema movies, movies in theaters now, free tv series, free anime series, putlocker, megashare9, megashare, hdmovie14, project free tv, 123movies, primewire, letmewatchthis, sockshare">

	<meta name="csrf-token"  content="{{ csrf_token() }}">
	<meta name="api_default" content= <?= session()->get('API')['default'] ?> >
	<meta name="root_url_f"  content= <?= url()->full() ?> >


	<!-- bootstrap -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous"> -->
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" >

	<!-- Three JS -->
		<!-- <script src="js/three.min.js"></script> -->
		<script src="{{ asset('js/three.min.js') }}"></script>
	<!-- Custom -->
		<!-- <link rel="stylesheet" type="text/css" href="css/app.css"> -->
		<link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">

		<!-- <script src="js/app.js"></script> -->
		<script src="{{ asset('js/app.js') }}"></script>
</head>
<body>
	<nav id="nav" class= 'container-fluid row '>

		<input type="checkbox" class="toggle" id="mMenu" style="display:none">
		<label for="mMenu" class="box col-xs-1 col-sm-1 col-md-1">
			<i class="glyphicon glyphicon-align-justify"></i>
		</label>

		<!-- <p class="logo col-xs-8 col-sm-9 col-md-1"> Logo </p> -->
		<!-- CANVAS -->
		<canvas class="logo  col-xs-4 col-sm-2 col-md-2 col-lg-2 " ></canvas>

		<ul class="nPanel col-xs-12 col-sm-12 col-md-7 col-lg-7 ">
			<li><a href="<?=  url('/') 					 ?>">Home  </a></li>
			<li><a href="<?=  url('/?Order=dataUpdated') ?>">Latest</a></li>
			<li><a href="<?=  url('/?Type=TV')           ?>">TV    </a></li>
			<li><a href="<?=  url('/?Type=Movie')        ?>">Movie </a></li>

			@if( Auth::check() )
				<li class="head favor">
					<a href="<?=  url('/?f_status=favourites') ?>" title="FAVOURITES"><span class="fas fa-heart"></span></a>
					<!-- <a href="<?=  url('/favourites') ?>" title="FAVOURITES"><span class="fas fa-heart"></span></a> -->
				</li>
			@endif

		</ul>

		<div id='search_wrapper'>
			<search_bar></search_bar>
		</div>

	<!-- LOGIN SISTEM -->

		<div class="loginContainer col-xs-2 col-sm-2 col-md-1 col-lg-1">

			<?php if ( ! Auth::check() ):?>

				<div class="loginButtons  ">
				<button class="enter ">LOGIN</button>
				</div>

				<form  class="loginForm" method="POST" action = "login">
					@csrf



					<a class="closeMe">&times;</a>
					<div class="imgContainer">
						<i class="fa fa-user" style="font-size:8vmin;"></i>
					</div>
						<label for="uname">Email</label>
						<input id="uname" type="text" placeholder="Email" name="email" >


						<br>

						<label for="pass">Password</label>
						<input id="pass" type="password" placeholder="Enter Password" name="password" >

						<br>
						@error('email')
							<label class="error alert alert-danger">{{ $message }}</label>
							<br>
						@enderror

						<button class="singin" type="submit" name="singin">Login</button>

						<button class="singup"  name="singup" ><a href="<?= url('/registration') ?>">Sing-Up</a> </button>
						<br>
						<a href="<?= url('password/reset') ?> ">Forgot password?</a>
				</form>

			<?php else:?>

				<?php if ( auth()->user()['avatar'] ):?>
					<img src  = <?= auth()->user()['avatar'] ?>
						alt   = "avatar " class="avatarlogged "
						title = <?= auth()->user()["username"]."&#13;".'('.auth()->user()["email"].')';?> >

				<?php else:?>
					<i class="fa fa-user avatarlogged" style="font-size:4vmin;color:rgb(39, 217, 187);"></i>
				<?php endif;?>

				<form class="logged " action='logout' method="POST" >
					@csrf
					<div class="fold">
						<h4><?= auth()->user()["username"]?></h4><p><?= auth()->user()["email"]?></p>
						<a href="<?= url('/profile') ?>" >Profile</a>
						<button class="logout" type="submit" name="logout">Log-Out</button>
					</div>
				</form>
			<?php endif;?>
		</div>

	</nav>



	@yield('content')

	<footer>
		<v_footer ></v_footer>
	</footer>

  	<!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>
