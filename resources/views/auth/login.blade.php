{{-- resources/views/auth/login.blade.php --}}
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Iniciar sesión</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;background:#f6f7fb;margin:0}
    .wrap{max-width:420px;margin:6vh auto;background:#fff;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.06);padding:28px}
    h1{font-size:20px;margin:0 0 14px}
    .mb{margin-bottom:12px}
    label{display:block;font-size:13px;margin-bottom:6px;color:#222}
    input[type=text],input[type=password]{width:100%;padding:10px 12px;border:1px solid #d7dbe3;border-radius:8px;font-size:14px}
    .btn{display:inline-block;width:100%;padding:12px 14px;border:0;border-radius:10px;background:#2563eb;color:#fff;font-weight:600;cursor:pointer}
    .muted{color:#6b7280;font-size:12px}
    .row{display:flex;justify-content:space-between;align-items:center}
    .err{background:#fee2e2;border:1px solid #fecaca;color:#7f1d1d;padding:10px 12px;border-radius:8px;margin-bottom:12px;font-size:13px}
    .chk{display:flex;gap:8px;align-items:center}
    a{color:#2563eb;text-decoration:none}
  </style>
</head>
<body>
  <div class="wrap">
    <h1>Iniciar sesión</h1>

    {{-- errores de validación --}}
    @if ($errors->any())
      <div class="err">
        @foreach ($errors->all() as $error)
          <div>• {{ $error }}</div>
        @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}">
      @csrf

      {{-- Campo único: teléfono, email o usuario --}}
      <div class="mb">
        <label for="username">Teléfono, Email o Usuario</label>
        <input id="username" name="username" type="text" value="{{ old('username') }}" autocomplete="username" required>
      </div>

      <div class="mb">
        <label for="password">Contraseña</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>
      </div>

      <div class="mb row">
        <label class="chk muted">
          <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
          Recuérdame
        </label>
        <a href="{{ route('auth.forgot.password.showForm') }}">¿Olvidaste tu contraseña?</a>
      </div>

      <button class="btn" type="submit">Entrar</button>

      <p class="muted" style="margin-top:14px;">
        ¿No tienes cuenta? <a href="{{ route('auth.register.showForm') }}">Crear cuenta</a>
      </p>
    </form>
  </div>
</body>
</html>
