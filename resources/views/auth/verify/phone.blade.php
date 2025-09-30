@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Verifica tu teléfono</h4>
    <form method="POST" action="{{ route('phone.verify.otp') }}">
        @csrf
        <div class="form-group">
            <label>Código recibido por SMS:</label>
            <input type="text" name="otp" class="form-control" required autofocus>
            @error('otp')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary mt-2">Verificar</button>
    </form>
</div>
@endsection