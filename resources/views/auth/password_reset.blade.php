@extends('layout')

@section('content')

    <div class="wrapper">

        @if( session('status') )
                    <div class="alert alert-success" role="alert">
                        {{ session('status')  }}
                    </div>
        @endif


        <form class='pReset' method="POST" action="{{ route( 'password.update' ) }}">
            @csrf


            <input type="hidden" name="email"    value  = "<?= !isset($email) ? '' : htmlspecialchars( $email ) ?>" >
            <input type="hidden" name="token"    value  = "<?= !isset($token) ? '' : htmlspecialchars( $token ) ?>" >

            <br>
            <input type="text"   name="password" placeholder="Enter new password"         >

            <br>



            <input type="text" name="password_confirmation" class="submit" placeholder="Enter new password again">

            @error('password')
                <label class="error alert alert-danger">{{ $message }}</label>
                <br>
            @enderror

            @error('email')
                <label class="error alert alert-danger">{{ $message }}</label>
                <br>
            @enderror
            <br>

            <button type="submit" name="submitChangePas" class="submit">submit</button>
        </form>
    </div>

@endsection