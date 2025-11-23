<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\InvitationToken;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $recipientName;
    public $inviteUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(InvitationToken $token, ?string $recipientName = null)
    {
        $this->token = $token;
        $this->recipientName = $recipientName;
        $this->inviteUrl = url('/invite/' . $token->token);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $roleNames = [
            'league_manager' => 'Encargado de Liga',
            'coach' => 'Entrenador',
            'player' => 'Jugador',
            'referee' => 'Árbitro',
        ];

        $roleName = $roleNames[$this->token->token_type] ?? 'Usuario';

        return new Envelope(
            subject: "Invitación a FlowFast - {$roleName}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invitation',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
