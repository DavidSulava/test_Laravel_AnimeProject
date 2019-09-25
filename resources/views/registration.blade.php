@extends('layout')

@section('content')

<? if(!isset( session('user')->id )):?>

	<form  method = "POST" class="regForm" action='_register'>
		@csrf


		<div class="Registration">

			<input type="hidden" name='sender'   value=<? echo basename(__FILE__) ?> ><br>

			<input type="text"   name='username' class="username" placeholder="Username" value= "<?= old('username')  ?>"  ><br>
			@error('username')
				<div class="alert alert-danger">{{ $message }}</div>
			@enderror

			<input type="text"   name='email'    class="email"    placeholder="E-mail"   value= "<?= old('email') ?>" ><br>
			@error('email')
				<div class="alert alert-danger">{{ $message }}</div>
			@enderror

			<br>

			<input type="text" name='firstName' class="firstName" placeholder="First Name"   value= "<?= old('firstName') ?>" ><br>
			@error('firstName')
				<div class="alert alert-danger">{{ $message }}</div>
			@enderror

			<input type="text" name='lastName'  class="lastName"  placeholder="Last Name"    value= "<?= old('lastName')  ?>" ><br>
			@error('lastName')
				<div class="alert alert-danger">{{ $message }}</div>
			@enderror

			<input type="text" name='phone'     class="phone"     placeholder="Phone Number" value= "<?= old('phone')     ?>" ><br><br>
			@error('phone')
				<div class="alert alert-danger">{{ $message }}</div>
			@enderror

			<input type="password" placeholder="Password" name='password' class="password">
			@error('password')
				<div class="alert alert-danger">{{ $message }}</div>
			@enderror

			<input type="password" placeholder="Confirm Password" name='password_confirmation' class="pasConfirm">
			<br>
			@error('password_confirmation')
				<div class="alert alert-danger">{{ $message }}</div>
			@enderror

			@if(session('erCaptcha'))
				<br>
				<div class="alert alert-danger">{{ session('erCaptcha') }}</div>
			@endif

			<?php
				!isset( $_GET['erCaptcha'] ) ? :
					print_r('<label  class="error">'.htmlspecialchars($_GET['erCaptcha']).'</label><br>');
			?>
			<br>
			<div class="g-recaptcha" data-sitekey=<?= config('custom_glob.capcha.siteKay') ;?> style ='margin:auto auto;width:250px;text-align: center;'></div>
			<br>
			<button type="submit" name="submitReg" class="submitRreg" >SUBMIT</button>

		</div>
	</form>

	<script src='https://www.google.com/recaptcha/api.js' defer></script>

	<? if (isset( $success )):?>
		<script>
			alert('Registration succesfully complited!');
			location.replace('index.php');
		</script>"
	<?endif;?>
<? else:?>
	<?= url('/') ?>
<?endif;?>








@endsection