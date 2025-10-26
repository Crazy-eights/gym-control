<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - Gym Control</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .reset-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .reset-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
        }
        
        .reset-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .reset-header h2 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
        }
        
        .reset-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 0.95rem;
        }
        
        .reset-body {
            padding: 40px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            display: block;
        }
        
        .form-control {
            border: 2px solid #e1e5e9;
            border-radius: 12px;
            padding: 12px 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
            background: white;
        }
        
        .btn-reset {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            padding: 12px 30px;
            color: white;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            transition: all 0.3s ease;
        }
        
        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
            color: white;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ffa8a8 100%);
            color: white;
        }
        
        .back-to-login {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-to-login a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .back-to-login a:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        
        .password-requirements {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
            font-size: 0.9rem;
        }
        
        .password-requirements h6 {
            color: #333;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .password-requirements ul {
            margin: 0;
            padding-left: 20px;
        }
        
        .password-requirements li {
            color: #666;
            margin-bottom: 4px;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-card">
            <div class="reset-header">
                <i class="fas fa-key fa-2x mb-3"></i>
                <h2>Restablecer Contraseña</h2>
                <p>Ingresa tu nueva contraseña</p>
            </div>
            
            <div class="reset-body">
                @if (session('status'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        @foreach ($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ request('email') }}">

                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-2"></i>Email
                        </label>
                        <input 
                            type="email" 
                            class="form-control" 
                            id="email" 
                            name="email" 
                            value="{{ request('email') }}" 
                            readonly
                        >
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-2"></i>Nueva Contraseña
                        </label>
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password" 
                            name="password" 
                            required 
                            autocomplete="new-password"
                            minlength="8"
                        >
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">
                            <i class="fas fa-lock me-2"></i>Confirmar Nueva Contraseña
                        </label>
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            required 
                            autocomplete="new-password"
                            minlength="8"
                        >
                    </div>

                    <div class="password-requirements">
                        <h6><i class="fas fa-info-circle me-1"></i>Requisitos de contraseña:</h6>
                        <ul>
                            <li>Mínimo 8 caracteres</li>
                            <li>Al menos una letra mayúscula</li>
                            <li>Al menos una letra minúscula</li>
                            <li>Al menos un número</li>
                            <li>Al menos un carácter especial (!@#$%^&*)</li>
                        </ul>
                    </div>

                    <button type="submit" class="btn btn-reset">
                        <i class="fas fa-save me-2"></i>
                        Restablecer Contraseña
                    </button>
                </form>

                <div class="back-to-login">
                    <a href="{{ route('login') }}">
                        <i class="fas fa-arrow-left me-1"></i>
                        Volver al Login
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación en tiempo real de contraseñas
        document.getElementById('password_confirmation').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmation = this.value;
            
            if (password !== confirmation && confirmation.length > 0) {
                this.style.borderColor = '#ff6b6b';
            } else {
                this.style.borderColor = '#e1e5e9';
            }
        });

        // Mostrar/ocultar contraseña
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling;
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>