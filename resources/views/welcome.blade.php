<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="shortcut icon" href="#" />
        <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estudio DAG</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Dongle:wght@300&family=Roboto&display=swap');

        body {
    margin: 0;
    position: relative;
    overflow: hidden;
}

body::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100vh;
    background-image: url('/1903-CEO.jpg');
    background-size: cover;
    background-position: top left;
    clip-path: polygon(100% 0, 100% 100%, 0 100%);
    z-index: -1;
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
    padding-top: 300px;
    margin-left: -200px;
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
    margin-top: 650px;
    margin-left: 700px;
    font-size: x-large;
    font-weight: 700;
    color: white;
}

.container {
    min-height: 700px;
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

h2 {
    padding-left: 30px;
    letter-spacing: 2px;
    color:  white;
    font-weight: 300;
    font-size: 8vh;
    text-shadow: #ffffff69 10px 5px;
    background: linear-gradient(to right, red, yellow);
    -webkit-background-clip: text; /* Para navegadores Webkit (Chrome, Safari, etc.) */
    background-clip: text;
    color: transparent; /* Oculta el color del texto */
}

.btn-outline-danger {
    background-color: transparent; /* Color de fondo inicial */
    transition: background-color 0.5s; /* Duración de la transición */
}

/* Establece el color de fondo al hacer hover */
.btn-outline-danger:hover {
    background-color: orange; /* Color de fondo al hacer hover */
    color: red;
}

.login-container {
    position: relative;
}

.register-container {
    position: relative
}

.login-button {
  font-size: 18px;
  padding: 10px 20px;
  cursor: pointer;
  transition: all 1.5s ease;
}

.register-button {
  font-size: 18px;
  padding: 10px 20px;
  cursor: pointer;
  transition: all 1.5s ease;
}

/* Estilo para el formulario de inicio de sesión */
.login-form {
    opacity: 0;
    visibility: hidden;
    position: absolute;
    top: 0;
    left: 0;
    background: white;
    transition: opacity 2s;
    border: 2px solid #FFA500; /* Borde azul para el formulario de inicio de sesión */
}

/* Estilo para el formulario de registro */
.register-form {
    opacity: 0;
    visibility: hidden;
    position: absolute;
    top: 0;
    left: 0;
    background: white;
    transition: opacity 2s;
    border: 2px solid #FFA500; /* Borde naranja para el formulario de registro */
}


.login-form.active {
    display: block;
    opacity: 1;
    transform: translateY(0);
    transform: translateX(0);
}



.register-form.active {
    display: block;
    opacity: 1;
    transform: translateY(0);
    transform: translateX(0);
}

/* Estilo de los campos de inicio de sesión y el botón */
.login-form input[type="text"],
.login-form input[type="password"],
.login-form button {
  display: block;
  padding: 10px;
  margin-bottom: 10px;
  background: linear-gradient(to right, red, yellow);
}

.register-form input[type="text"],
.register-form input[type="password"],
.register-form button {
  display: block;
  padding: 10px;
  margin-bottom: 10px;
  background: linear-gradient(to right, red, yellow);
}

#login-button.clicked {
    display: none;
}

#register-button.clicked {
    display: none;
}
    </style>
                
</head>

<body>
    <div class="container">
        <div class="row" id="login">
            <h2 class="text-center">InsureTech Assess</h2>
        </div>
        <div class="row" style="margin: 20px 0px 0px 20px; font-size: 25px;">
            <div class="col-md-12">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/home') }}" class="btn btn-outline-danger btn-lg mx-3" style="text-decoration: none">Home</a>
                    @else
                        <div class="login-container">
                            <div class="col-md-6">
                                <a id="login-button" onclick="fadeIn()" class="btn btn-outline-danger btn-lg mx-3 login-button" style="text-decoration: none">Login</a>
                                <a id="register-button" onclick="fadeIn2()" class="btn btn-outline-danger btn-lg mx-3 register-button" style="text-decoration: none">Register</a>
                            </div>
                            <form id="login-form" class="login-form" method="POST" action="{{ route('login') }}">
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
                                <h5 class="modal-title bg-primary text-center text-white" style="background: linear-gradient(to right, red, yellow);">Login</h5>
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

                <div class="form-group d-flex justify-content-center">
                    <button type="submit" class="btn btn-danger btn-lg" tabindex="4">
                        Ingresar
                    </button>
                </div>
                            </form>
                            <form id="register-form" class="register-form">
                            <h5 class="modal-title bg-danger text-center text-white" style="background: linear-gradient(to right, red, yellow);">Register</h5>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Email address</label>
                                    <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
                                    <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputPassword1">Password</label>
                                    <input type="password" class="form-control" id="exampleInputPassword1">
                                </div>
                                <div class="d-flex justify-content-center">
                                    <button type="submit" class="btn btn-danger">Submit</button>
                                </div>
                            </form>
                        </div>
                    @endauth
                @endif
            </div>
        </div>
        <footer class="">
            <div class="row">
                <div class="col-md-12">
                    <div class="copyright" id="footer">
                        <span>Copyright &copy; Arcich Silvio 2022</span>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script>
    function fadeIn() {
    const form = document.getElementById('login-form');
    const form2 = document.getElementById('register-form');
    const button = document.getElementById('register-button');

    form2.style.opacity = '0';
    button.style.transform = 'translateX(160px)';
    form.style.opacity = '1';
    form.style.visibility = 'visible';
}

function fadeIn2() {
    const form = document.getElementById('login-form');
    const form2 = document.getElementById('register-form');
    const button2 = document.getElementById('login-button');

    form.style.opacity = '0';
    button2.style.transform = 'translateX(-160px)';
    form2.style.opacity = '1';
    form2.style.visibility = 'visible';
}
document.addEventListener("DOMContentLoaded", function() {
    // var loginButton = document.getElementById("login-button");
    // var loginForm = document.getElementById("login-form");

    // if (loginButton && loginForm) {
    //     loginButton.addEventListener("click", function() {
    //         loginButton.classList.add("clicked");
    //         setTimeout(function() {
    //             loginForm.classList.add("active");
    //         }, 100); // Agregar un retraso de 100 milisegundos
    //     });
    // }

    var loginButton = document.getElementById("register-button");
    var loginForm = document.getElementById("register-form");

    if (loginButton && loginForm) {
        loginButton.addEventListener("click", function() {
            loginButton.classList.add("clicked");
            setTimeout(function() {
                loginForm.classList.add("active");
            }, 100); // Agregar un retraso de 100 milisegundos
        });
    }
});



</script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
</html>
