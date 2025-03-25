@extends('layouts.auth_app')
@section('title')
    Login
@endsection
@section('content')

<head>
    <link rel="shortcut icon" href="#" />
        <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insure TechAsses</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Dongle:wght@300&family=Roboto&display=swap');

body {
    
    
    margin: 0;
    background: linear-gradient(to left,hsla(234, 84%, 42%, 0.767), #0f63eb );
    
}

#login-row #login-column #login-box {
    margin-top: 20px;
    max-width: 550px;
    height: 360px;
    box-shadow: 2px 2px 2px 2px white;
    border-radius: 3px;
}



#btnEnter {
   bottom: 75px;
   margin-right: 200px;
   position: relative;

}

.logo {
  position: relative;
  left: 390px;  
  bottom: 110px;
  color: white;
}

#login-box {
    position: fixed;
    left: 105px;
    top: 150px;
    background-color: white;

}

#login {
    padding-top: 40px;
    font-family: 'Bebas Neue', cursive;
    padding-bottom: 0px;
    margin-bottom: 0px; 
}

.form-group {
    width: 300px;
    padding-left: 15px;
    color: white;
    font-size:x-large;
}



footer {
    text-align:center;
    position: absolute;
    margin-top: 400px;
    margin-left: 700px;
    font-size: x-large;
    font-weight: 700;
    color: white;
}

.container {
    min-height: 700px;
}

h2 {
    padding-left: 130px;
    letter-spacing: 2px;
    color:  white;
    font-weight: 300;
    font-size: 3cm;
    text-shadow: #ffffff69 10px 5px;
}


.form {
    color: white;
    background: linear-gradient(35deg, hwb(222 6% 33%), #0358c7);
    padding: 10px;
}

#btnEnter {
    color: 0358c7;
}

.text-dark {
    color: white;
}

h3 {
    color: #0358c7 ;
}

.form-group > label {
    color: #0358c7 ;
}
    </style>
                
</head>

    <div class="card card-primary">
        <div class="card-header"><h4>Ingresar</h4></div>

        <div class="card-body">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                @if ($errors->any())
                    <div class="alert alert-danger p-0">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="form-group">
                    <label for="email">Email</label>
                    <input aria-describedby="emailHelpBlock" id="email" type="email"
                           class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email"
                           placeholder="Enter Email" tabindex="1"
                           value="{{ (Cookie::get('email') !== null) ? Cookie::get('email') : old('email') }}" autofocus
                           required>
                    <div class="invalid-feedback">
                        {{ $errors->first('email') }}
                    </div>
                </div>

                <div class="form-group">
                    <div class="d-block">
                        <label for="password" class="control-label">Password</label>
                        <div class="float-right">
                            <a href="{{ route('password.request') }}" class="text-small">
                                Olvidaste la contraseña?
                            </a>
                        </div>
                    </div>
                    <input aria-describedby="passwordHelpBlock" id="password" type="password"
                           value="{{ (Cookie::get('password') !== null) ? Cookie::get('password') : null }}"
                           placeholder="Enter Password"
                           class="form-control{{ $errors->has('password') ? ' is-invalid': '' }}" name="password"
                           tabindex="2" required>
                    <div class="invalid-feedback">
                        {{ $errors->first('password') }}
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="remember" class="custom-control-input" tabindex="3"
                               id="remember"{{ (Cookie::get('remember') !== null) ? 'checked' : '' }}>
                        <label class="custom-control-label" style="color:black" for="remember">Recuerdame</label>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                        Ingresar
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script type = "text/javascript">  

      </script> 
@endsection
