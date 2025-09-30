@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width:460px">
  <h1 class="h4 mb-3">Verificación en dos pasos</h1>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger">
      {{ $errors->first() }}
    </div>
  @endif

  <p class="text-muted">Enviamos un código por SMS al teléfono terminado en
    <strong>{{ substr(session('otp_phone'), -4) }}</strong>.
  </p>

  <form method="POST" action="{{ route('otp.verify') }}" class="mb-3">
    @csrf
    <label class="form-label">Código</label>
    <input name="code" class="form-control" placeholder="123456" autofocus>
    <button class="btn btn-success mt-3 w-100" type="submit">Verificar</button>
  </form>

  <form method="POST" action="{{ route('otp.resend') }}">
    @csrf
    <button class="btn btn-outline-secondary w-100" type="submit">Reenviar código</button>
  </form>
</div>
@endsection
