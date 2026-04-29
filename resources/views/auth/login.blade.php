<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión - Sistema Médico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="d-flex align-items-center justify-content-center min-vh-100">
        <div class="w-100" style="max-width: 420px;">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <h2 class="text-center h4 mb-1">Sistema Médico</h2>
                    <p class="text-center text-muted mb-4">Iniciar sesión en el sistema</p>


                    @if ($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first('email') }}
                        </div>
                    @endif


                    <form method="POST" action="{{ route('auth.login') }}">
                        @csrf


                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email"
                                   value="{{ old('email') }}" required autofocus>
                        </div>


                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password" required>
                        </div>


                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
                            <label class="form-check-label" for="remember">Recuérdame</label>
                        </div>


                        <button type="submit" class="btn btn-primary w-100 py-2">Iniciar Sesión</button>
                    </form>


                    <div class="alert alert-info mt-4 mb-0">
                        <strong>Demo:</strong>
                        Email: <code>admin@example.com</code> / Contraseña: <code>password</code>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


