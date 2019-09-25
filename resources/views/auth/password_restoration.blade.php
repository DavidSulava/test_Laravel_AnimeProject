@extends('layout')

@section('content')

    <div class="wrapper">

        <form  class="restoration" method="POST" action="{{ route( '_password/update/email' ) }}">
            @csrf

            @if( session('status') )
                <div class="alert alert-success" role="alert">
                    {{ session('status')  }}
                </div>

            @else
                <h4 style="color:black;">We will send a new password to your email. Please enter your email down below.</h4>

                <input type="hidden" name='sender'  value= "<? echo basename(__FILE__) ?>" ><br>

                <input type="text"   name='email'   class="email" placeholder="E-mail"  value= "<?= old('email')     ?>"  ><br>

                @error('email')
                    <label class="error alert alert-danger">{{ $message }}</label>
                    <br>
                @enderror

                @if( session('erCaptcha') )
                    <label class="error alert alert-danger">{{ session('erCaptcha') }}</label>
                    <br>
                @endif

                <div class="g-recaptcha" data-sitekey=<?= config('custom_glob.capcha.siteKay') ;?> style ='margin:auto auto;width:250px;text-align: center;'></div>

                <button type="submit" name="submitRestoration" class="submit">SUBMIT</button>
            @endif
        </form>

        <script src='https://www.google.com/recaptcha/api.js' defer></script>

    </div>




@endsection