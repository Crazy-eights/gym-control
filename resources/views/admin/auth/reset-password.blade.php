<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña - Gym Control</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #4A90E2 0%, #5FB3E4 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .reset-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            max-width: 450px;
            width: 100%;
            padding: 3rem;
            text-align: center;
        }
        
        .reset-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            color: white;
            font-size: 2rem;
        }
        
        .reset-title {
            color: #333;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .reset-subtitle {
            color: #666;
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }
        
        .form-label {
            color: #333;
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .form-control {
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            padding: 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }
        
        .btn-reset {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            padding: 1rem 2rem;
            width: 100%;
            font-size: 1rem;
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
        }
        
        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        .back-to-login {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-to-login:hover {
            color: #764ba2;
            text-decoration: none;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .password-requirements {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            text-align: left;
        }
        
        .password-requirements h6 {
            color: #333;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .password-requirements ul {
            margin: 0;
            padding-left: 1.2rem;
            font-size: 0.85rem;
            color: #666;
        }
        
        .password-requirements li {
            margin-bottom: 0.2rem;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-icon">
            <i class="fas fa-lock"></i>
        </div>
        
        <h1 class="reset-title">Nueva Contraseña</h1>
        <p class="reset-subtitle">
            Ingresa tu nueva contraseña para restablecer el acceso a tu cuenta de administrador.
        </p>

        @if ($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.password.update') }}">
            @csrf
            
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">
            
            <div class="password-requirements">
                <h6><i class="fas fa-info-circle me-2"></i>Requisitos de contraseña:</h6>
                <ul>
                    <li>Mínimo 8 caracteres</li>
                    <li>Recomendado: incluir mayúsculas, minúsculas y números</li>
                    <li>Evita usar información personal</li>
                </ul>
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">Nueva Contraseña</label>
                <input 
                    type="password" 
                    class="form-control @error('password') is-invalid @enderror" 
                    id="password" 
                    name="password" 
                    required 
                    autocomplete="new-password"
                    placeholder="Ingresa tu nueva contraseña"
                    autofocus
                    minlength="8"
                >
            </div>
            
            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>
                <input 
                    type="password" 
                    class="form-control" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    required 
                    autocomplete="new-password"
                    placeholder="Confirma tu nueva contraseña"
                    minlength="8"
                >
            </div>
            
            <button type="submit" class="btn btn-reset">
                <i class="fas fa-key me-2"></i>
                Restablecer Contraseña
            </button>
        </form>
        
        <a href="{{ route('admin.login.form') }}" class="back-to-login">
            <i class="fas fa-arrow-left me-2"></i>
            Volver al inicio de sesión
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Validación en tiempo real de confirmación de contraseña
        document.getElementById('password_confirmation').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmation = this.value;
            
            if (confirmation && password !== confirmation) {
                this.style.borderColor = '#dc3545';
            } else {
                this.style.borderColor = '#e1e5e9';
            }
        });
    </script>
</body>
</html>