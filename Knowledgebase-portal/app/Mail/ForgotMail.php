<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Resend\Laravel\Facades\Resend;
class ForgotMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
        // public $otp;
    public function __construct()
    {

    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Wachtwoord wijziging plaatsgevonden',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {

        // $otp = $this->otp;


        return new Content(
            htmlString:"
<!DOCTYPE html>
<html lang='nl'>
  <head>
    <meta charset='UTF-8' />
    <meta name='viewport' content='width=device-width, initial-scale=1.0' />
    <style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;900&family=DM+Mono:wght@500&display=swap');

      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }

      body {
        background-color: #f0ede8;
        padding: 48px 16px;
        font-family: 'DM Sans', sans-serif;
        color: #1a1a1a;
      }

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
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 4px;
      }

      .header-title {
        font-size: 20px;
        font-weight: 700;
        color: #fff;
      }

      .header-icon {
        width: 52px;
        height: 52px;
        background: rgba(0, 22, 61, 0.18);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
      }

      .wrapper {
        max-width: 550px;
        margin: 0 auto;
      }

      .email-card {
        background-color: #ffffff;
        border-radius: 0 0 16px 16px;
        border-top: none;
        padding: 25px 36px 15px;
      }

      .eyebrow {
        font-size: 20px;
        font-weight: 900;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: #000;
        margin-bottom: 4px;
      }

      .hero-mail {
        margin-bottom: 6px;
        text-align: center;
      }

      .small-subtext {
        text-align: center;
        font-size: 12px;
        text-transform: uppercase;
        color: #999;
        font-weight: 600;
        margin: 8px;
      }

      .notice {
        display: grid;
        grid-template-columns: 1fr 5fr;
        background-color: #e1ffe1;
        border-left: 3px solid #85e847;
        border-radius: 0 8px 8px 0;
        text-align: start;
        padding: 12px 16px;
        margin-bottom: 20px;
        margin-top: 20px;
        justify-content: center;
        align-items: center;
        font-size: 13px;
        color: #327a2e;
        line-height: 1.5;
      }
      .notice-icon {
        display: flex;
      }
      .faq-icon {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 50px;
        border-radius: 4px;
        height: 50px;
      }

      .faq-box {
        display: grid;
        border-left: 3px solid rgba(0, 22, 61, 0.18);
        padding: 12px 5px;
        margin-top: 20px;
        grid-template-columns: 0fr 5fr;
        background-color: rgb(65, 184, 220);
        border-radius: 5px;
      }

      .text {
          color: #fff;
          display: flex;
          font-size: 15px;
          margin-right: 35px;
          justify-content: center;
          font-weight: 600;
          align-items: center;
          text-align: center;
      }

      .divider {
        height: 1px;
        background: #f0ede8;
        margin: 15.8px;
      }

      .hero-icon-wrap {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 64px;
        height: 64px;
        background: rgba(65, 184, 220, 0.12);
        border-radius: 50%;
        margin-bottom: 16px;
      }

      .body-text {
     text-align: center;
     font-size: 14px;
     color: #666;
     line-height: 3.5px;
     }

     .action {
       line-height: 3.9;
     }

     .eyebrow-footer {
       font-size: 12px;
       font-weight: 600;
       letter-spacing: 0.14em;
       text-align: center;
       text-transform: uppercase;
       color: #999;
     }

      .header-title {
        font-size: 20px;
        font-weight: 700;
      }

      .footer {
        text-align: center;
        margin-top: 24px;
        font-size: 12px;
        color: #aaaa9f;
        line-height: 1.7;
        }

        .footer a {
          color: rgb(65,184,220);
          text-decoration: none;
      }

    </style>
  </head>
  <body>
    <div class='wrapper'>
      <div class='header'>
        <div>
          <div class='header-label'>Kyano Digital</div>
          <div class='header-title'>Wachtwoord wijziging</div>
        </div>
        <div class='header-icon'>
          <svg width='37' height='37' viewBox='0 0 24 24' style='color: rgb(255, 255, 255)'>
            <path
              fill='currentColor'
              fill-rule='evenodd'
              d='M19 7h-2a4 4 0 0 0-8 0v3h9a3 3 0 0 1 3 3v6a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3v-6a3 3 0 0 1 3-3h1V7a6 6 0 1 1 12 0m-1 5H6a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-6a1 1 0 0 0-1-1'
              clip-rule='evenodd'
            ></path>
          </svg>
        </div>
      </div>
      <div class='email-card'>
        <div class='hero-mail'>
          <div class='hero-icon-wrap'>
            <svg width='40' height='40' viewBox='0 0 24 24' style='color: rgb(65, 184, 220)'>
              <path
                fill='currentColor'
                fill-rule='evenodd'
                d='M19 7h-2a4 4 0 0 0-8 0v3h9a3 3 0 0 1 3 3v6a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3v-6a3 3 0 0 1 3-3h1V7a6 6 0 1 1 12 0m-1 5H6a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-6a1 1 0 0 0-1-1'
                clip-rule='evenodd'
              ></path>
            </svg>
          </div>

          <p class='eyebrow'>Beveiligingsemail</p>
        </div>
        <div class='small-subtext'>
          <p>Wachtwoord Wijziging plaats gevonden</p>
        </div>
        <div class='divider'></div>
        <p class='body-text'>Er is een wachtwoord wijziging plaats gevonden op jouw account.</p>

        <div class='notice'>
          <div class='notice-icon'>
            <svg width='45' height='45' viewBox='0 0 80 80' style='color: rgb(74, 85, 101)'>
              <g fill='none'>
                <path
                  fill='#f2f2f2'
                  d='m40 12l3.135.176l3.096.526l3.017.87l2.9 1.2l2.749 1.52l2.56 1.817L59.8 20.2l2.092 2.341l1.817 2.561l1.52 2.748l1.2 2.901l.87 3.017l.526 3.096L68 40l-.176 3.135l-.526 3.096l-.87 3.017l-1.2 2.9l-1.52 2.749l-1.817 2.56L59.8 59.8l-2.341 2.092l-2.561 1.817l-2.748 1.52l-2.901 1.2l-3.017.87l-3.096.526L40 68l-3.135-.176l-3.096-.526l-3.017-.87l-2.9-1.2l-2.749-1.52l-2.56-1.817L20.2 59.8l-2.092-2.341l-1.817-2.561l-1.52-2.748l-1.2-2.901l-.87-3.017l-.526-3.096L12 40l.176-3.135l.526-3.096l.87-3.017l1.2-2.9l1.52-2.749l1.817-2.56L20.2 20.2l2.341-2.092l2.561-1.817l2.748-1.52l2.901-1.2l3.017-.87l3.096-.526z'
                ></path>
                <path
                  fill='#f2f2f2'
                  d='m40 12l.112-1.997a2 2 0 0 0-.224 0zm3.135.176l.335-1.972a2 2 0 0 0-.223-.025zm3.096.526l.553-1.922a2 2 0 0 0-.218-.05zm3.017.87l.765-1.848a2 2 0 0 0-.212-.075zm2.9 1.2l.968-1.75a2 2 0 0 0-.202-.097zm2.749 1.52l1.157-1.631a2 2 0 0 0-.19-.12zm2.56 1.817l1.333-1.492a2 2 0 0 0-.175-.14zM59.8 20.2l1.491-1.333a2 2 0 0 0-.158-.158zm2.092 2.341l1.631-1.157a2 2 0 0 0-.14-.175zm1.817 2.561l1.75-.967a2 2 0 0 0-.119-.19zm1.52 2.748l1.847-.765a2 2 0 0 0-.097-.202zm1.2 2.901l1.923-.553a2 2 0 0 0-.075-.212zm.87 3.017l1.972-.335a2 2 0 0 0-.05-.218zm.526 3.096l1.997-.112a2 2 0 0 0-.025-.223zM68 40l1.997.112q.006-.113 0-.224zm-.176 3.135l1.972.335q.018-.11.025-.223zm-.526 3.096l1.922.553q.03-.108.05-.218zm-.87 3.017l1.849.765q.042-.104.074-.212zm-1.2 2.9l1.75.968q.054-.098.097-.202zm-1.52 2.749l1.631 1.157q.066-.09.12-.19zm-1.817 2.56l1.492 1.333q.075-.083.14-.175zM59.8 59.8l1.333 1.491q.083-.074.158-.158zm-2.341 2.092l1.157 1.631q.092-.065.175-.14zm-2.561 1.817l.967 1.75q.1-.053.19-.119zm-2.748 1.52l.765 1.847q.105-.043.202-.097zm-2.901 1.2l.553 1.923q.108-.032.212-.075zm-3.017.87l.335 1.972q.11-.02.218-.05zm-3.096.526l.112 1.997q.113-.007.223-.025zM40 68l-.112 1.997q.113.006.224 0zm-3.135-.176l-.335 1.972q.11.018.223.025zm-3.096-.526l-.553 1.922q.108.03.218.05zm-3.017-.87l-.765 1.849q.104.042.212.074zm-2.9-1.2l-.968 1.75q.098.054.202.097zm-2.749-1.52l-1.157 1.631q.09.066.19.12zm-2.56-1.817l-1.333 1.492q.083.075.175.14zM20.2 59.8l-1.491 1.333q.075.083.158.158zm-2.092-2.341l-1.631 1.157q.065.092.14.175zm-1.817-2.561l-1.75.967q.053.1.119.19zm-1.52-2.748l-1.847.765q.043.105.097.202zm-1.2-2.901l-1.923.553q.032.108.075.212zm-.87-3.017l-1.972.335q.02.11.05.218zm-.526-3.096l-1.997.112q.006.113.025.223zM12 40l-1.997-.112a2 2 0 0 0 0 .224zm.176-3.135l-1.972-.335q-.018.11-.025.223zm.526-3.096l-1.922-.553q-.03.108-.05.218zm.87-3.017l-1.848-.765q-.044.104-.075.212zm1.2-2.9l-1.75-.968a2 2 0 0 0-.097.202zm1.52-2.749l-1.631-1.157a2 2 0 0 0-.12.19zm1.817-2.56l-1.492-1.333q-.075.083-.14.175zM20.2 20.2l-1.333-1.491q-.083.075-.158.158zm2.341-2.092l-1.157-1.631q-.092.065-.175.14zm2.561-1.817l-.967-1.75a2 2 0 0 0-.19.119zm2.748-1.52l-.765-1.847q-.105.043-.202.097zm2.901-1.2l-.553-1.923q-.108.032-.212.075zm3.017-.87l-.335-1.972q-.11.02-.218.05zm3.096-.526l-.112-1.997q-.113.006-.223.025zm3.023 1.82l3.135.177l.224-3.994l-3.135-.176zm2.912.152l3.096.526l.67-3.944l-3.096-.526zm2.877.476l3.017.87l1.107-3.845l-3.017-.869zm2.805.795l2.901 1.202l1.531-3.696l-2.9-1.202zm2.7 1.104l2.747 1.52l1.935-3.502l-2.748-1.519zm2.558 1.4l2.56 1.817l2.315-3.262l-2.56-1.817zm2.385 1.677l2.341 2.092l2.666-2.982l-2.342-2.093zm2.183 1.934l2.092 2.341l2.983-2.665l-2.093-2.342zM60.26 23.7l1.817 2.56l3.262-2.314l-1.817-2.561zm1.698 2.37l1.519 2.749l3.5-1.935l-1.518-2.748zm1.421 2.547l1.202 2.9l3.695-1.53l-1.201-2.901zm1.128 2.689l.87 3.017l3.843-1.107l-.87-3.018zm.82 2.798l.525 3.096l3.944-.67l-.526-3.096zm.5 2.873l.176 3.135l3.994-.224l-.176-3.135zm.176 2.91l-.176 3.136l3.994.224l.176-3.135zm-.15 2.913l-.527 3.096l3.944.67l.526-3.096zm-.477 2.877l-.87 3.017l3.845 1.107l.869-3.017zm-.795 2.805l-1.202 2.901l3.696 1.531l1.201-2.9zm-1.104 2.7l-1.52 2.747l3.502 1.935l1.519-2.748zm-1.4 2.558L60.26 56.3l3.262 2.315l1.817-2.56zM60.4 56.125l-2.092 2.341l2.982 2.666l2.093-2.342zm-1.934 2.183L56.125 60.4l2.665 2.983l2.342-2.093zM56.3 60.26l-2.56 1.817l2.314 3.262l2.561-1.817zm-2.37 1.698l-2.749 1.519l1.935 3.5l2.748-1.518zm-2.547 1.421l-2.9 1.202l1.53 3.695l2.901-1.201zm-2.689 1.128l-3.017.87l1.107 3.843l3.017-.87zm-2.798.82l-3.096.525l.67 3.944l3.096-.526zm-2.873.5l-3.135.176l.224 3.994l3.135-.176zm-2.91.176l-3.136-.176l-.224 3.994l3.135.176zm-2.913-.15l-3.096-.527l-.67 3.944l3.096.526zm-2.877-.477l-3.017-.87l-1.108 3.845l3.018.869zm-2.805-.795l-2.901-1.202l-1.531 3.696l2.9 1.201zm-2.7-1.104l-2.748-1.52l-1.934 3.502l2.748 1.519zm-2.558-1.4L23.7 60.26l-2.315 3.262l2.56 1.817zM23.875 60.4l-2.341-2.092l-2.666 2.982l2.342 2.093zm-2.183-1.934L19.6 56.125l-2.983 2.665l2.093 2.342zM19.74 56.3l-1.817-2.56l-3.262 2.314l1.817 2.561zm-1.698-2.37l-1.519-2.749l-3.5 1.935l1.518 2.748zm-1.421-2.547l-1.202-2.9l-3.696 1.53l1.202 2.901zm-1.128-2.689l-.87-3.017l-3.843 1.107l.87 3.017zm-.82-2.798l-.525-3.096l-3.944.67l.526 3.096zm-.5-2.873l-.176-3.135l-3.994.224l.176 3.135zm-.176-2.91l.176-3.136l-3.994-.224l-.176 3.135zm.15-2.913l.527-3.096l-3.944-.67l-.526 3.096zm.477-2.877l.87-3.017l-3.845-1.108l-.869 3.018zm.795-2.805l1.202-2.901l-3.696-1.531l-1.202 2.9zm1.104-2.7l1.52-2.748l-3.502-1.934l-1.519 2.748zm1.4-2.558l1.817-2.56l-3.262-2.315l-1.817 2.56zm1.677-2.385l2.092-2.341l-2.982-2.666l-2.093 2.342zm1.934-2.183l2.341-2.092l-2.665-2.983l-2.342 2.093zM23.7 19.74l2.56-1.817l-2.314-3.262l-2.561 1.817zm2.37-1.698l2.749-1.519l-1.935-3.5l-2.748 1.518zm2.547-1.421l2.9-1.202l-1.53-3.696l-2.901 1.202zm2.689-1.128l3.017-.87l-1.107-3.843l-3.018.87zm2.798-.82l3.096-.525l-.67-3.944l-3.096.526zm2.873-.5l3.135-.176l-.224-3.994l-3.135.176z'
                ></path>
                <path
                  stroke='#219653'
                  stroke-linecap='round'
                  stroke-linejoin='round'
                  stroke-width='4'
                  d='m25.23 40.182l8.44 8.519a1.977 1.977 0 0 0 2.814 0L54.77 30.243'
                ></path>
              </g>
            </svg>
          </div>
          Wachtwoord wijziging geslaagd!<br />
          Uw wachtwoord is veranderd.
        </div>
        <div class='faq-box'>
          <div class='faq-icon'>
            <svg
              width='35'
              height='35'
              viewBox='0 0 24 24'
              style='color: rgb(255, 255, 255)'
            >
              <path
                fill='currentColor'
                d='M12 1.75A10.25 10.25 0 1 0 22.25 12A10.26 10.26 0 0 0 12 1.75m-.11 16.8a1 1 0 1 1 0-2a1 1 0 0 1 0 2m3.82-7.32a3.73 3.73 0 0 1-2.21 2.05a1 1 0 0 0-.41.33a.9.9 0 0 0-.18.54v.71a1 1 0 1 1-2 0v-.74a2.9 2.9 0 0 1 .55-1.67a2.9 2.9 0 0 1 1.37-1a1.6 1.6 0 0 0 .63-.38A1.63 1.63 0 0 0 14 9.81a1.7 1.7 0 0 0-.16-.69a2 2 0 0 0-.62-.69a2 2 0 0 0-.89-.36a2.27 2.27 0 0 0-1.44.2a2.2 2.2 0 0 0-1 1a1 1 0 0 1-1.82-.83a4.17 4.17 0 0 1 4.56-2.37a4 4 0 0 1 1.72.7A4.1 4.1 0 0 1 15.6 8.2a3.7 3.7 0 0 1 .08 3.05z'
              ></path>
            </svg>
          </div>
          <div class='text'>Heeft u dit niet aangevraagd?</div>
        </div>
        <div class='action'>
          <p class='eyebrow-footer'>Neem actie zo snel mogelijk!</p>
        </div>
      </div>
      <div class='footer'>
        <p>Je ontvangt deze e-mail omdat er een wachtwoord verandering is plaats gevonden voor jouw
          account.
        </p>
          <p>© Kyano Digital B.V. · <a href='https://kyano.digital'>kyano.digital</a></p>

      </div>
    </div>
  </body>
</html>"        );
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
