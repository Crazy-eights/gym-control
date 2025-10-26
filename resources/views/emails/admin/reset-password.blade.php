<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - Gym Control</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        
        .email-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
        }
        
        .header .icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .content {
            padding: 2rem;
        }
        
        .greeting {
            font-size: 1.1rem;
            margin-bottom: 1rem;
            color: #333;
        }
        
        .message {
            margin-bottom: 2rem;
            color: #666;
            line-height: 1.7;
        }
        
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 1rem 0 2rem;
            transition: all 0.3s ease;
        }
        
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            color: white;
            text-decoration: none;
        }
        
        .security-info {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 1rem;
            margin: 1.5rem 0;
        }
        
        .security-info h4 {
            color: #856404;
            margin: 0 0 0.5rem;
            font-size: 1rem;
        }
        
        .security-info p {
            color: #856404;
            margin: 0;
            font-size: 0.9rem;
        }
        
        .footer {
            background-color: #f8f9fa;
            padding: 1.5rem;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        
        .footer p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }
        
        .manual-link {
            background-color: #f8f9fa;
            border-radius: 6px;
            padding: 1rem;
            margin: 1rem 0;
            border: 1px dashed #dee2e6;
        }
        
        .manual-link p {
            margin: 0 0 0.5rem;
            font-size: 0.9rem;
            color: #666;
        }
        
        .manual-link code {
            background-color: #e9ecef;
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 0.85rem;
            color: #333;
            word-break: break-all;
        }
        
        .expiry-notice {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
        }
        
        .expiry-notice p {
            margin: 0;
            color: #721c24;
            font-size: 0.9rem;
        }
        
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            
            .content {
                padding: 1.5rem 1rem;
            }
            
            .header {
                padding: 1.5rem 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="icon">🔐</div>
            <h1>Gym Control Admin</h1>
        </div>
        
        <div class="content">
            <div class="greeting">
                Hola {{ $admin->name ?? $admin->username }},
            </div>
            
            <div class="message">
                <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta de administrador en <strong>Gym Control</strong>.</p>
                
                <p>Si fuiste tú quien solicitó este cambio, haz clic en el siguiente botón para crear una nueva contraseña:</p>
            </div>
            
            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="cta-button">
                    🔑 Restablecer mi contraseña
                </a>
            </div>
            
            <div class="expiry-notice">
                <p><strong>⏰ Importante:</strong> Este enlace expirará en 60 minutos por seguridad.</p>
            </div>
            
            <div class="manual-link">
                <p>Si el botón no funciona, copia y pega el siguiente enlace en tu navegador:</p>
                <code>{{ $resetUrl }}</code>
            </div>
            
            <div class="security-info">
                <h4>🛡️ Información de Seguridad</h4>
                <p>Si no solicitaste este cambio de contraseña, puedes ignorar este email de forma segura. Tu contraseña actual seguirá siendo válida.</p>
            </div>
            
            <div class="message">
                <p><strong>Datos de la solicitud:</strong></p>
                <ul>
                    <li><strong>Email:</strong> {{ $admin->email }}</li>
                    <li><strong>Fecha:</strong> {{ now()->format('d/m/Y H:i:s') }}</li>
                    <li><strong>Token:</strong> {{ substr($token, 0, 8) }}...</li>
                </ul>
            </div>
        </div>
        
        <div class="footer">
            <p>Este es un email automático del sistema Gym Control.</p>
            <p>Por favor, no respondas a este mensaje.</p>
        </div>
    </div>
</body>
</html>