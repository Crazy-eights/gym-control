<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $testData['system_name'] }} - Email de Prueba</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header .subtitle {
            margin: 5px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            background: #f8f9fa;
            padding: 30px 20px;
            border-left: 3px solid #007bff;
        }
        .message-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin: 20px 0;
        }
        .test-info {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #007bff;
            margin: 20px 0;
        }
        .test-info h3 {
            margin: 0 0 10px 0;
            color: #0056b3;
            font-size: 16px;
        }
        .test-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .test-info td {
            padding: 5px 0;
            border-bottom: 1px solid #ddd;
        }
        .test-info td:first-child {
            font-weight: bold;
            width: 120px;
            color: #666;
        }
        .footer {
            background: #343a40;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 0 0 10px 10px;
            font-size: 12px;
        }
        .success-badge {
            background: #28a745;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
        }
        .logo {
            font-size: 32px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="logo">🏋️</div>
            <h1>{{ $testData['system_name'] }}</h1>
            <div class="subtitle">Email de Prueba de Configuración</div>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="success-badge">✓ PRUEBA EXITOSA</div>
            
            <div class="message-box">
                <h2>¡Configuración de Email Funcionando Correctamente!</h2>
                <p>{{ $content }}</p>
            </div>

            <div class="test-info">
                <h3>📊 Información de la Prueba</h3>
                <table>
                    <tr>
                        <td>ID de Prueba:</td>
                        <td><code>{{ $testData['test_id'] }}</code></td>
                    </tr>
                    <tr>
                        <td>Fecha y Hora:</td>
                        <td>{{ $testData['timestamp'] }}</td>
                    </tr>
                    <tr>
                        <td>Sistema:</td>
                        <td>{{ $testData['system_name'] }}</td>
                    </tr>
                    <tr>
                        <td>Estado:</td>
                        <td><span style="color: #28a745; font-weight: bold;">✓ Entregado</span></td>
                    </tr>
                </table>
            </div>

            <div style="background: #fff3cd; padding: 15px; border-radius: 6px; border-left: 4px solid #ffc107; margin: 20px 0;">
                <strong>💡 Nota:</strong> Si recibiste este correo, significa que la configuración SMTP de tu sistema de gimnasio está funcionando perfectamente. Ya puedes enviar notificaciones automáticas a tus miembros y empleados.
            </div>

            <div style="background: #d1ecf1; padding: 15px; border-radius: 6px; border-left: 4px solid #17a2b8; margin: 20px 0;">
                <strong>🔧 Características Configuradas:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Envío de correos SMTP</li>
                    <li>Autenticación de servidor</li>
                    <li>Encriptación de conexión</li>
                    <li>Configuración de remitente</li>
                    <li>Validación de entrega</li>
                </ul>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>{{ $testData['system_name'] }}</strong></p>
            <p>Sistema de Gestión Integral para Gimnasios</p>
            <p style="margin: 10px 0 0 0; opacity: 0.7;">
                Este es un mensaje automático generado por el sistema. No responder a este correo.
            </p>
        </div>
    </div>
</body>
</html>