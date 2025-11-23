<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitación a FlowFast</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(to right, #2563eb, #4f46e5); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: white; margin: 0; font-size: 28px;">FlowFast</h1>
        <p style="color: #e0e7ff; margin: 10px 0 0 0;">Sistema de Gestión Deportiva</p>
    </div>

    <div style="background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 10px 10px;">
        @if($recipientName)
            <p style="font-size: 16px; margin-bottom: 20px;">Hola <strong>{{ $recipientName }}</strong>,</p>
        @else
            <p style="font-size: 16px; margin-bottom: 20px;">Hola,</p>
        @endif

        <p style="font-size: 15px; line-height: 1.8; margin-bottom: 20px;">
            Has sido invitado a unirte a <strong>{{ $token->targetLeague->name ?? 'una liga' }}</strong> en FlowFast como 
            <strong>
                @if($token->token_type === 'league_manager')
                    Encargado de Liga
                @elseif($token->token_type === 'coach')
                    Entrenador
                    @if($token->targetTeam)
                        del equipo {{ $token->targetTeam->name }}
                    @endif
                @elseif($token->token_type === 'player')
                    Jugador
                    @if($token->targetTeam)
                        del equipo {{ $token->targetTeam->name }}
                    @endif
                @elseif($token->token_type === 'referee')
                    Árbitro
                @endif
            </strong>.
        </p>

        <p style="font-size: 15px; line-height: 1.8; margin-bottom: 25px;">
            Para aceptar esta invitación y crear tu cuenta, haz clic en el siguiente botón:
        </p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $inviteUrl }}" style="display: inline-block; background: linear-gradient(to right, #2563eb, #4f46e5); color: white; text-decoration: none; padding: 14px 30px; border-radius: 8px; font-weight: bold; font-size: 16px;">
                Aceptar Invitación
            </a>
        </div>

        <p style="font-size: 13px; color: #6b7280; line-height: 1.6; margin-top: 25px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
            <strong>Nota:</strong> Si no solicitaste esta invitación, puedes ignorar este correo.
            Esta invitación expira el {{ $token->expires_at->format('d/m/Y') }}.
        </p>

        <p style="font-size: 12px; color: #9ca3af; margin-top: 15px;">
            Si el botón no funciona, copia y pega este enlace en tu navegador:<br>
            <a href="{{ $inviteUrl }}" style="color: #2563eb; word-break: break-all;">{{ $inviteUrl }}</a>
        </p>
    </div>

    <div style="text-align: center; margin-top: 20px; color: #6b7280; font-size: 12px;">
        <p>&copy; {{ date('Y') }} FlowFast. Todos los derechos reservados.</p>
    </div>
</body>
</html>
