<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Gym Control</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #4A90E2 0%, #28a745 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .forgot-container {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            margin: 20px;
        }
        
        .forgot-header {
            background: linear-gradient(135deg, #4A90E2 0%, #28a745 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .forgot-header h3 {
            margin: 0;
            font-weight: 600;
            font-size: 1.8rem;
        }
        
        .forgot-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 0.95rem;
        }
        
        .forgot-body {
            padding: 40px 30px;
        }
        
        .form-control {
            height: 50px;
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding-left: 20px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #4A90E2;
            box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #4A90E2 0%, #28a745 100%);
            border: none;
            height: 50px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #357abd 0%, #218838 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(74, 144, 226, 0.3);
        }
        
        .back-link {
            color: #6c757d;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }
        
        .back-link:hover {
            color: #4A90E2;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .icon-circle {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
        }
        
        .user-type-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="forgot-container">
        <div class="forgot-header">
            <div class="icon-circle">
                <i class="fas fa-key"></i>
            </div>
            <h3>Recuperar Contraseña</h3>
        </div>
        
        <div class="forgot-body">
            @if (session('status'))
                <div class="alert alert-success mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    @foreach ($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </div>
            @endif
            
            <div class="user-type-info">
                <i class="fas fa-info-circle me-2"></i>
                 Ingresa tu email para enviar un correo de recuperación de contraseña.
            </div>
            
            <form method="POST" action="{{ route('password.email') }}" id="forgotForm">
                @csrf
                
                <div class="mb-4">
                    <input type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           name="email" 
                           value="{{ old('email') }}" 
                           placeholder="Tu email (socio@ejemplo.com o admin@ejemplo.com)"
                           required 
                           autofocus>
                    
                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                
                <button type="submit" class="btn btn-primary w-100 mb-3" id="submitBtn">
                    <i class="fas fa-search me-2"></i>Buscar Cuenta y Enviar Enlace
                </button>
            </form>
            
            <div class="text-center">
                <a href="{{ route('login') }}" class="back-link">
                    <i class="fas fa-arrow-left me-2"></i>Volver al Login
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Manejar envío del formulario con protección CSRF
        document.getElementById('forgotForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            
            // Deshabilitar botón para prevenir doble envío
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enviando...';
            
            // Reactivar botón después de 5 segundos en caso de error
            setTimeout(function() {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-search me-2"></i>Buscar Cuenta y Enviar Enlace';
            }, 5000);
        });
        
        // Refrescar página si hay error 419 (token expirado)
        if (window.location.search.includes('error=419') || document.title.includes('419')) {
            setTimeout(function() {
                window.location.href = '{{ route('password.request') }}';
            }, 2000);
        }
    </script>
</body>
</html>