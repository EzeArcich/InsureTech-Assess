<h1>dejanos un mensaje</h1>

<form action="{{route('contactanos.store')}}" method="POST">
@csrf
<label for="">
    Nombre:
    <br>
    <input type="text" name="localidad">
</label>
<br>

@error('name')
    <p><strong>{{$message}}</strong></p>
@enderror

<label for="">
    Correo:
    <br>
    <input type="text" name="patente">
</label>
<br>

@error('correo')
    <p><strong>{{$message}}</strong></p>
@enderror

<label for="">
    Mensaje:
    <br>
    <textarea name="observaciones"  rows="4"></textarea>
</label>
<br>

@error('mensaje')
    <p><strong>{{$message}}</strong></p>
@enderror

<button type="submit">Enviar Mensaje</button>
</form>

@if (session('info'))
    <script>
        alert('{{session('info')}}');
    </script>

@endif