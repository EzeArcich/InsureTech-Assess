@extends('layouts.auth_app')
@section('title')
    Register
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
        <div class="card-header"><h4>Register</h4></div>

        <div class="card-body pt-1">
            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="first_name">Full Name:</label><span
                                    class="text-danger">*</span>
                            <input id="firstName" type="text"
                                   class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                   name="name"
                                   tabindex="1" placeholder="Enter Full Name" value="{{ old('name') }}"
                                   autofocus required>
                            <div class="invalid-feedback">
                                {{ $errors->first('name') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email:</label><span
                                    class="text-danger">*</span>
                            <input id="email" type="email"
                                   class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                   placeholder="Enter Email address" name="email" tabindex="1"
                                   value="{{ old('email') }}"
                                   required autofocus>
                            <div class="invalid-feedback">
                                {{ $errors->first('email') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password" class="control-label">Password
                                :</label><span
                                    class="text-danger">*</span>
                            <input id="password" type="password"
                                   class="form-control{{ $errors->has('password') ? ' is-invalid': '' }}"
                                   placeholder="Set account password" name="password" tabindex="2" required>
                            <div class="invalid-feedback">
                                {{ $errors->first('password') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password_confirmation"
                                   class="control-label">Confirm Password:</label><span
                                    class="text-danger">*</span>
                            <input id="password_confirmation" type="password" placeholder="Confirm account password"
                                   class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid': '' }}"
                                   name="password_confirmation" tabindex="2">
                            <div class="invalid-feedback">
                                {{ $errors->first('password_confirmation') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-4">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                                Register
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="mt-5 text-muted text-center">
        Already have an account ? <a
                href="{{ route('login') }}">SignIn</a>
    </div>
@endsection
