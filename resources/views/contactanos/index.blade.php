<h1>dejanos un mensaje</h1>

<form action="{{route('coordination.contact.store')}}" method="POST">
@csrf
<label for="">
    Nombre:
    <br>
    <input type="text" name="city">
</label>
<br>

@error('name')
    <p><strong>{{$message}}</strong></p>
@enderror

<label for="">
    Correo:
    <br>
    <input type="text" name="license_plate">
</label>
<br>

@error('email')
    <p><strong>{{$message}}</strong></p>
@enderror

<label for="">
    Mensaje:
    <br>
    <textarea name="notes"  rows="4"></textarea>
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