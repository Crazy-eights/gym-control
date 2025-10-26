<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gym Control</title>
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
        
        .login-container {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            width: 100%;
            display: flex;
            min-height: 500px;
        }
        
        .login-left {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }
        
        .login-illustration {
            text-align: center;
            color: white;
            z-index: 2;
        }
        
        .login-illustration h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .login-illustration p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }
        
        .gym-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .login-right {
            flex: 1;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-header h3 {
            font-size: 2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
            color: #666;
            font-size: 0.95rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 15px 20px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .form-control:focus {
            border-color: #4A90E2;
            background: white;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        }
        
        .form-control.is-invalid {
            border-color: #dc3545;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #4A90E2 0%, #5FB3E4 100%);
            border: none;
            border-radius: 10px;
            padding: 15px;
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(74, 144, 226, 0.3);
            color: white;
        }
        
        .forgot-password {
            text-align: center;
            margin-top: 1rem;
        }
        
        .forgot-password a {
            color: #4A90E2;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .forgot-password a:hover {
            text-decoration: underline;
        }
        
        .social-login {
            margin-top: 2rem;
            text-align: center;
        }
        
        .social-login .divider {
            margin: 1.5rem 0;
            position: relative;
            text-align: center;
            color: #666;
        }
        
        .social-login .divider:before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e0e0e0;
        }
        
        .social-login .divider span {
            background: white;
            padding: 0 1rem;
        }
        
        .social-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }
        
        .social-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: 2px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
            background: white;
        }
        
        .social-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .social-btn.facebook { color: #3b5998; }
        .social-btn.twitter { color: #1da1f2; }
        .social-btn.google { color: #dd4b39; }
        
        .credentials-hint {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1.5rem;
            text-align: center;
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .credentials-hint strong {
            color: #495057;
        }
        
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                margin: 1rem;
            }
            
            .login-left {
                padding: 2rem;
                min-height: 200px;
            }
            
            .login-right {
                padding: 2rem;
            }
        }
        
        /* Estilos para la sección de recuperación de contraseña */
        .forgot-password-section {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e9ecef;
        }
        
        .forgot-password-section .btn-sm {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
        }
        
        .forgot-password-section .btn-outline-success:hover {
            background-color: #28a745;
            border-color: #28a745;
        }
        
        .forgot-password-section .btn-outline-primary:hover {
            background-color: #007bff;
            border-color: #007bff;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Panel Izquierdo - Ilustración -->
        <div class="login-left">
            <div class="login-illustration">
                <div class="gym-icon">
                    <i class="fas fa-dumbbell"></i>
                </div>
                <h2>Bienvenido</h2>
                <p>Gestiona tu gimnasio de manera profesional con nuestro sistema integral</p>
                <div style="margin-top: 2rem;">
                    <i class="fas fa-users" style="font-size: 2rem; margin: 0 0.5rem; opacity: 0.7;"></i>
                    <i class="fas fa-chart-line" style="font-size: 2rem; margin: 0 0.5rem; opacity: 0.7;"></i>
                    <i class="fas fa-calendar-check" style="font-size: 2rem; margin: 0 0.5rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
        
        <!-- Panel Derecho - Formulario -->
        <div class="login-right">
            <div class="login-header">
                <h3>Login</h3>
                <p>Ingresa tus credenciales para continuar</p>
            </div>
            
            <form method="POST" action="{{ request()->is('admin/login') ? route('admin.login.attempt') : route('login.attempt') }}">
                @csrf
                
                <div class="form-group">
                    <input type="text" 
                           class="form-control @error('username') is-invalid @enderror" 
                           name="username" 
                           value="{{ old('username') }}" 
                           placeholder="Usuario"
                           required 
                           autofocus>
                    
                    @error('username')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <input type="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           name="password" 
                           placeholder="Contraseña"
                           required>
                    
                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">
                        Mantenerme conectado
                    </label>
                </div>
                
                <button type="submit" class="btn btn-login">
                    Iniciar Sesión
                </button>
            </form>
            
            <div class="forgot-password">
                <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
            </div>
            
            
            
           
        </div>
    </div>
    
    <script>
        // Manejo automático del error 419 (Token CSRF expirado)
        window.addEventListener('DOMContentLoaded', function() {
            // Verificar si hay error 419 en la URL o en el título
            if (window.location.search.includes('error=419') || 
                document.title.includes('419') || 
                document.title.includes('Page Expired')) {
                
                // Mostrar mensaje temporal
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-warning';
                errorDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Sesión expirada, recargando...';
                errorDiv.style.position = 'fixed';
                errorDiv.style.top = '20px';
                errorDiv.style.left = '50%';
                errorDiv.style.transform = 'translateX(-50%)';
                errorDiv.style.zIndex = '9999';
                errorDiv.style.padding = '10px 20px';
                errorDiv.style.borderRadius = '8px';
                errorDiv.style.backgroundColor = '#fff3cd';
                errorDiv.style.border = '1px solid #ffeaa7';
                errorDiv.style.color = '#856404';
                
                document.body.appendChild(errorDiv);
                
                // Recargar después de 1 segundo
                setTimeout(function() {
                    window.location.href = '/login';
                }, 1000);
            }
        });
    </script>
</body>
</html>