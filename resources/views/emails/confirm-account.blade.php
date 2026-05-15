<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Confirma tu cuenta de ContaPro</title>
</head>
<body style="font-family: Arial, sans-serif; color: #10233f; line-height: 1.6;">
    <h1 style="color: #006341;">Confirma tu cuenta de ContaPro</h1>
    <p>Hola {{ $name }},</p>
    <p>Para activar tu cuenta y continuar con la configuracion de tu perfil fiscal, confirma tu correo electronico.</p>
    <p>
        <a href="{{ $confirmationUrl }}" style="background: #006341; color: #ffffff; padding: 12px 18px; border-radius: 8px; text-decoration: none; font-weight: bold;">
            Confirmar cuenta
        </a>
    </p>
    <p>Este enlace vence en 24 horas.</p>
    <p>Si no solicitaste esta cuenta, ignora este correo.</p>
</body>
</html>
