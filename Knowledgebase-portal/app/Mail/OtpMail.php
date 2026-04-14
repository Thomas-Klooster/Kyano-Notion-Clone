<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Wachtwoord wijzigen"
        );
    }

    public function content(): Content
    {
        $otp = $this->otp;

        return new Content(
            htmlString: "
<!DOCTYPE html>
<html lang='nl'>
<head>
    <meta charset='UTF-8' />
    <meta name='viewport' content='width=device-width, initial-scale=1.0' />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;900&family=DM+Mono:wght@500&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background-color: #f0ede8;
            font-family: 'DM Sans', sans-serif;
            padding: 48px 16px;
            color: #1a1a1a;
        }
        .wrapper { max-width: 600px; margin: 0 auto; }

        .header {
            background-color: rgb(65, 184, 220);
            border-radius: 16px 16px 0 0;
            padding: 28px 36px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header-label {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.7);
            margin-bottom: 4px;
        }
        .header-title {
            font-size: 20px;
            font-weight: 700;
            color: #ffffff;
        }
        .header-icon {
            width: 52px;
            height: 52px;
            background: rgba(0,22,61,0.18);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            background-color: #ffffff;
            border-radius: 0 0 16px 16px;
            border: 1px solid #e4e0d8;
            border-top: none;
            padding: 44px 36px 40px;
        }

        .hero { text-align: center; margin-bottom: 6px; }
        .hero-icon-wrap {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 64px;
            height: 64px;
            background: rgba(65,184,220,0.12);
            border-radius: 50%;
            margin-bottom: 16px;
        }
        .eyebrow {
            font-size: 25px;
            font-weight: 900;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #000;
            margin-bottom: 4px;
        }
        .hero-sub { font-size: 14px; color: #777; }

        .divider { height: 1px; background: #f0ede8; margin: 28px 0; }

        .otp-label {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #999;
            margin-bottom: 14px;
        }

        .otp-box {
            background: rgba(65,184,220,0.05);
            border: 1.5px dashed #d4cfc6;
            border-radius: 12px;
            padding: 20px 12px;
            text-align: center;
            margin-bottom: 12px;
        }
        .otp-timer {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #bbb;
            margin-bottom: 8px;
        }
        .otp-code {
            font-family: 'DM Mono', monospace;
            font-size: 46px;
            font-weight: 500;
            letter-spacing: 0.2em;
            color: rgb(0, 22, 61);
            line-height: 1;
        }

        .expiry-note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 28px;
            font-size: 12px;
            color: #bbb;
        }

        .notice {
            background: rgba(65,184,220,0.13);
            border-left: 3px solid rgb(0,22,61);
            border-radius: 0 8px 8px 0;
            padding: 14px 18px;
            margin-bottom: 28px;
            font-size: 13px;
            color: rgba(0,22,61,0.7);
            line-height: 1.6;
        }

        .tiles { display: flex; gap: 12px; }
        .tile {
            flex: 1;
            padding: 14px;
            background: #fafaf8;
            border-radius: 10px;
            border: 1px solid #eee;
            text-align: center;
        }
        .tile-icon { margin-bottom: 6px; }
        .tile p { font-size: 12px; color: #999; line-height: 1.5; }

        .footer {
            text-align: center;
            margin-top: 24px;
            font-size: 12px;
            color: #aaaa9f;
            line-height: 1.7;
        }
        .footer a { color: rgb(65,184,220); text-decoration: none; }
    </style>
</head>
<body>
<div class='wrapper'>

    <div class='header'>
        <div>
            <div class='header-label'>Kyano Digital</div>
            <div class='header-title'>Wachtwoord wijzigen</div>
        </div>
        <div class='header-icon'>
            <svg width='26' height='26' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='1.8' stroke-linecap='round' stroke-linejoin='round'>
                <rect x='3' y='11' width='18' height='11' rx='2' ry='2'/>
                <path d='M7 11V7a5 5 0 0 1 10 0v4'/>
            </svg>
        </div>
    </div>

    <div class='card'>

        <div class='hero'>
            <div class='hero-icon-wrap'>
                <svg width='30' height='30' viewBox='0 0 24 24' fill='none' stroke='rgb(65,184,220)' stroke-width='1.8' stroke-linecap='round' stroke-linejoin='round'>
                    <path d='M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4'/>
                </svg>
            </div>
            <p class='eyebrow'>Beveiligingscode</p>
            <p class='hero-sub'>Gebruik de code hieronder om jouw wachtwoord te wijzigen.</p>
        </div>

        <div class='divider'></div>

        <p class='otp-label'>Jouw eenmalige code</p>

        <div class='otp-box'>
            <div class='otp-timer'>Geldig voor 10 minuten</div>
            <div class='otp-code'>{$otp}</div>
        </div>

        <div class='expiry-note'>
            <svg width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='#bbb' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>
                <circle cx='12' cy='12' r='10'/><polyline points='12 6 12 12 16 14'/>
            </svg>
            <span>Deze code vervalt automatisch na gebruik</span>
        </div>

        <div class='divider'></div>

        <div class='notice'>
            <strong>Heb jij dit niet aangevraagd?</strong> Dan kun je deze email veilig negeren. Jouw wachtwoord blijft ongewijzigd.
        </div>

        <div class='tiles'>
            <div class='tile'>
                <div class='tile-icon'>
                    <svg width='18' height='18' viewBox='0 0 24 24' fill='none' stroke='#bbb' stroke-width='1.8' stroke-linecap='round' stroke-linejoin='round'>
                        <path d='M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z'/>
                    </svg>
                </div>
                <p>Deel deze code nooit met anderen</p>
            </div>
            <div class='tile'>
                <div class='tile-icon'>
                    <svg width='18' height='18' viewBox='0 0 24 24' fill='none' stroke='#bbb' stroke-width='1.8' stroke-linecap='round' stroke-linejoin='round'>
                        <path d='M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z'/><circle cx='12' cy='12' r='3'/>
                    </svg>
                </div>
                <p>Wordt maar één keer gevraagd</p>
            </div>
        </div>

    </div>

    <div class='footer'>
        <p>Je ontvangt deze e-mail omdat er een wachtwoord reset is aangevraagd voor jouw account.</p>
        <p>© Kyano Digital B.V. · <a href='https://kyano.digital'>kyano.digital</a></p>
    </div>

</div>
</body>
</html>
            "
        );
    }
}