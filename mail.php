<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class Mail
{
  function __construct()
  {
  }

  function __destruct()
  {
  }

  public function SendResetPassword($user, $token, $lang)
  {
    require("../composer/vendor/autoload.php");
    try {
      if ($lang == "eng") {
        $subject = "Password Recovery - icebein.ch";
        $body = $this->GetResetPasswordBodyENG($user, $token);
      } else {
        $subject = "Passwort-Wiederherstellung - icebein.ch";
        $body = $this->GetResetPasswordBodyDE($user, $token);
      }

      $send_mail = "noreplay@icebein.ch";
      $mail = new PHPMailer();
      $mail->IsHTML(true);
      $mail->From = $send_mail;
      $mail->FromName = $send_mail;
      $mail->Sender = $send_mail;
      $mail->Subject = $subject;
      $mail->Body = $body;
      $mail->AddAddress($user['email']);
      if (!$mail->Send()) {
        echo "Mailer Error: " . $user['email']->ErrorInfo;
      }
    } catch (Exception $e) {
      echo $e->errorMessage();
    } catch (\Exception $e) {
      echo $e->getMessage();
    }
  }

  public function SendWelcome($user, $lang)
  {
    require("../composer/vendor/autoload.php");
    try {
      if ($lang == "eng") {
        $subject = "Welcome - icebein.ch";
        $body = $this->GetAccountActivationBodyENG($user);
      } else {
        $subject = "Willkommen - icebein.ch";
        $body = $this->GetAccountActivationBodyDE($user);
      }

      $send_mail = "noreplay@icebein.ch";
      $mail = new PHPMailer();
      $mail->IsHTML(true);
      $mail->From = $send_mail;
      $mail->FromName = $send_mail;
      $mail->Sender = $send_mail;
      $mail->Subject = $subject;
      $mail->Body = $body;
      $mail->AddAddress($user['email']);
      if (!$mail->Send()) {
        echo "Mailer Error: " . $user['email']->ErrorInfo;
      }
    } catch (Exception $e) {
      echo $e->errorMessage();
    } catch (\Exception $e) {
      echo $e->getMessage();
    }
  }

  function GetAccountActivationBodyENG($user)
  {
    $body = $this->GetMeta();
    $body .= $this->GetCSS();
    $body .= '
          <body>
            <span class="preheader"
              >Thank you for singing up with icebein.ch. We’ve pulled together some
              information and resources to help you get started.</span
            >
            <table
              class="email-wrapper"
              width="100%"
              cellpadding="0"
              cellspacing="0"
              role="presentation"
            >
              <tr>
                <td align="center">
                  <table
                  bgcolor="#00A49C"
                    class="email-content"
                    width="100%"
                    cellpadding="0"
                    cellspacing="0"
                    role="presentation"
                  >
                    <tr bgcolor="#00A49C">
                      <td class="email-masthead">
                        <a
                          href="https://ssiag.com/icebein/en/"
                          class="f-fallback email-masthead_name"
                        >
                          Icebein
                        </a>
                      </td>
                    </tr>
                    <!-- Email Body -->
                    <tr>
                      <td
                        class="email-body"
                        width="100%"
                        cellpadding="0"
                        cellspacing="0"
                      >
                        <table
                          class="email-body_inner"
                          align="center"
                          width="570"
                          cellpadding="0"
                          cellspacing="0"
                          role="presentation"
                        >
                          <!-- Body content -->
                          <tr>
                            <td class="content-cell">
                              <div class="f-fallback">
                                <h1>Welcome, ' . $user["name"] . '!</h1>
                                <p>
                                  Thank you for singing up with <strong>icebein.ch</strong>. We are thrilled to
                                  have you on board.
                                </p>
                                <!-- Action -->
                                <p>For reference, here is your login information:</p>
                                <table
                                  class="attributes"
                                  width="100%"
                                  cellpadding="0"
                                  cellspacing="0"
                                  role="presentation"
                                >
                                  <tr>
                                    <td class="attributes_content">
                                      <table
                                        width="100%"
                                        cellpadding="0"
                                        cellspacing="0"
                                        role="presentation"
                                      >
                                        <tr>
                                          <td class="attributes_item">
                                            <span class="f-fallback">
                                              <strong>Login Page: </strong>https://ssiag.com/icebein/en/login.html
                                            </span>
                                          </td>
                                        </tr>
                                        <tr>
                                          <td class="attributes_item">
                                            <span class="f-fallback">
                                              <strong>E-mail: </strong>' . $user["email"] . '
                                            </span>
                                          </td>
                                        </tr>
                                      </table>
                                    </td>
                                  </tr>
                                </table>
                                <p>
                                  Thanks, <br>The Icebein Team</br>
                                </p>
                              </div>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <table
                          bgcolor="#00A49C"
                          class="email-footer"
                          align="center"
                          width="570"
                          cellpadding="0"
                          cellspacing="0"
                          role="presentation"
                        >
                          <tr>
                            <td class="content-cell" align="center">
                              <p class="f-fallback sub align-center">
                                &copy; 2020 SSIAG. All rights reserved.
                              </p>
                              <p class="f-fallback sub align-center">
                                Swiss Sport Investment AG
                                <br />Poststrasse 9
                                <br />6300 Zug
                                <br />Schweiz
                              </p>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </body>
        </html>';
    return $body;
  }

  function GetAccountActivationBodyDE($user)
  {
    $body = $this->GetMeta();
    $body .= $this->GetCSS();
    $body .= '
          <body>
            <span class="preheader"
              >Thank you for singing up with icebein.ch. We’ve pulled together some
              information and resources to help you get started.</span
            >
            <table
              class="email-wrapper"
              width="100%"
              cellpadding="0"
              cellspacing="0"
              role="presentation"
            >
              <tr>
                <td align="center">
                  <table
                  bgcolor="#00A49C"
                    class="email-content"
                    width="100%"
                    cellpadding="0"
                    cellspacing="0"
                    role="presentation"
                  >
                    <tr bgcolor="#00A49C">
                      <td class="email-masthead">
                        <a
                          href="https://ssiag.com/icebein"
                          class="f-fallback email-masthead_name"
                        >
                          Icebein
                        </a>
                      </td>
                    </tr>
                    <!-- Email Body -->
                    <tr>
                      <td
                        class="email-body"
                        width="100%"
                        cellpadding="0"
                        cellspacing="0"
                      >
                        <table
                          class="email-body_inner"
                          align="center"
                          width="570"
                          cellpadding="0"
                          cellspacing="0"
                          role="presentation"
                        >
                          <!-- Body content -->
                          <tr>
                            <td class="content-cell">
                              <div class="f-fallback">
                                <h1>Willkommen, ' . $user["name"] . '!</h1>
                                <p>
                                  Vielen Dank für Ihre Anmeldung und herzlich willkommen bei Icebein.
                                </p>
                                <!-- Action -->
                                <p>Mit folgenden Daten können Sie sich jederzeit einloggen:</p>
                                <table
                                  class="attributes"
                                  width="100%"
                                  cellpadding="0"
                                  cellspacing="0"
                                  role="presentation"
                                >
                                  <tr>
                                    <td class="attributes_content">
                                      <table
                                        width="100%"
                                        cellpadding="0"
                                        cellspacing="0"
                                        role="presentation"
                                      >
                                        <tr>
                                          <td class="attributes_item">
                                            <span class="f-fallback">
                                              <strong>Login: </strong>https://ssiag.com/icebein/login.html
                                            </span>
                                          </td>
                                        </tr>
                                        <tr>
                                          <td class="attributes_item">
                                            <span class="f-fallback">
                                              <strong>Benutzername: </strong>' . $user["email"] . '
                                            </span>
                                          </td>
                                        </tr>
                                      </table>
                                    </td>
                                  </tr>
                                </table>
                                <p>
                                  Freundliche Grüsse, <br>The Icebein Team</br>
                                </p>
                              </div>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <table
                          bgcolor="#00A49C"
                          class="email-footer"
                          align="center"
                          width="570"
                          cellpadding="0"
                          cellspacing="0"
                          role="presentation"
                        >
                          <tr>
                            <td class="content-cell" align="center">
                              <p class="f-fallback sub align-center">
                                &copy; 2020 SSIAG. All rights reserved.
                              </p>
                              <p class="f-fallback sub align-center">
                                Swiss Sport Investment AG
                                <br />Poststrasse 9
                                <br />6300 Zug
                                <br />Schweiz
                              </p>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </body>
        </html>';
    return $body;
  }

  function GetResetPasswordBodyENG($user, $token)
  {
    $body = $this->GetMeta();
    $body .= $this->GetCSS();
    $body .= '
              <body>
                <span class="preheader">Use this link to reset your password. The link is only valid for 3 hours.</span>
                <table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                  <tr>
                    <td align="center">
                      <table class="email-content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                        <tr>
                          <td class="email-masthead">
                            <a href="https://www.ssiag.com/icebein/en/" class="f-fallback email-masthead_name">
                            Icebein
                          </a>
                          </td>
                        </tr>
                        <!-- Email Body -->
                        <tr>
                          <td class="email-body" width="100%" cellpadding="0" cellspacing="0">
                            <table class="email-body_inner" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                              <!-- Body content -->
                              <tr>
                                <td class="content-cell">
                                  <div class="f-fallback">
                                    <h1>Dear ' . $user['name'] . ',</h1>
                                    <p>You recently requested to reset the password for your <strong>icebein.ch</strong> account. Use the button below to reset it. <strong>This password reset is only valid for the next 3 hours.</strong></p>
                                    <!-- Action -->
                                    <table class="body-action" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                      <tr>
                                        <td align="center">
                                          <!-- Border based button
                       https://litmus.com/blog/a-guide-to-bulletproof-buttons-in-email-design -->
                                          <table width="100%" border="0" cellspacing="0" cellpadding="0" role="presentation">
                                            <tr>
                                              <td align="center">
                                                <a style="color:#FFF" href="https://www.ssiag.com/icebein/en/password-reset?key=' . $token . '" class="f-fallback button button--green" target="_blank">Reset your password</a>
                                              </td>
                                            </tr>
                                          </table>
                                        </td>
                                      </tr>
                                    </table>
                                    <p>In case that you did not request a password reset, please ignore this email and your password will not be reset.</p>
                                    <p>Thanks,
                                      <br>The Icebein Team</br>
                                    </p>
                                    <!-- Sub copy -->
                                    <table class="body-sub" role="presentation">
                                      <tr>
                                        <td>
                                          <p class="f-fallback sub">If you’re having trouble with the button above, copy and paste the URL below into your web browser.</p>
                                          <p class="f-fallback sub">https://www.ssiag.com/icebein/en/password-reset?key=' . $token . '</p>
                                        </td>
                                      </tr>
                                    </table>
                                  </div>
                                </td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                        <tr style="background-color: #00A49C;">
                          <td>
                            <table class="email-footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                              <tr>
                                <td class="content-cell" align="center">
                                <p class="f-fallback sub align-center">
                                &copy; 2020 SSIAG. All rights reserved.
                              </p>
                              <p class="f-fallback sub align-center">
                                Swiss Sport Investment AG
                                <br />Poststrasse 9
                                <br />6300 Zug
                                <br />Schweiz
                              </p>
                                </td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
              </body>
            </html>';
    return $body;
  }

  function GetResetPasswordBodyDE($user, $token)
  {
    $body = $this->GetMeta();
    $body .= $this->GetCSS();
    $body .= '
              <body>
                <span class="preheader">Use this link to reset your password. The link is only valid for 3 hours.</span>
                <table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                  <tr>
                    <td align="center">
                      <table class="email-content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                        <tr>
                          <td class="email-masthead">
                            <a href="https://www.ssiag.com/icebein" class="f-fallback email-masthead_name">
                            Icebein
                          </a>
                          </td>
                        </tr>
                        <!-- Email Body -->
                        <tr>
                          <td class="email-body" width="100%" cellpadding="0" cellspacing="0">
                            <table class="email-body_inner" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                              <!-- Body content -->
                              <tr>
                                <td class="content-cell">
                                  <div class="f-fallback">
                                    <h1>Geehrter ' . $user['name'] . ',</h1>
                                    <p>Sie haben vor Kurzem die Zurücksetzung des Passworts von Ihrem icebein.ch Konto angefordert. <strong>Die Möglichkeit Ihren Passwort zurückzusetzen endet drei Stunden nach Erhalt dieser Mail.</strong></p>
                                    <!-- Action -->
                                    <table class="body-action" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                      <tr>
                                        <td align="center">
                                          <!-- Border based button
                       https://litmus.com/blog/a-guide-to-bulletproof-buttons-in-email-design -->
                                          <table width="100%" border="0" cellspacing="0" cellpadding="0" role="presentation">
                                            <tr>
                                              <td align="center">
                                                <a style="color:#FFF" href="https://www.ssiag.com/icebein/password-reset?key=' . $token . '" class="f-fallback button button--green" target="_blank">Reset your password</a>
                                              </td>
                                            </tr>
                                          </table>
                                        </td>
                                      </tr>
                                    </table>
                                    <p>Passwort zurücksetzen
Sollten Sie Passwortzurücksetzung nicht angefordert haben, können Sie diese Mitteilung ignorieren.</p>
                                    <p>Freundliche Grüsse,
                                      <br>The Icebein Team</br>
                                    </p>
                                    <!-- Sub copy -->
                                    <table class="body-sub" role="presentation">
                                      <tr>
                                        <td>
                                          <p class="f-fallback sub">Sollten irgendwelche Schwierigkeiten auftreten, verwenden Sie bitte den untenstehenden Link.</p>
                                          <p class="f-fallback sub">https://www.ssiag.com/icebein/password-reset?key=' . $token . '</p>
                                        </td>
                                      </tr>
                                    </table>
                                  </div>
                                </td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                        <tr style="background-color: #00A49C;">
                          <td>
                            <table class="email-footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                              <tr>
                                <td class="content-cell" align="center">
                                <p class="f-fallback sub align-center">
                                &copy; 2020 SSIAG. All rights reserved.
                              </p>
                              <p class="f-fallback sub align-center">
                                Swiss Sport Investment AG
                                <br />Poststrasse 9
                                <br />6300 Zug
                                <br />Schweiz
                              </p>
                                </td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
              </body>
            </html>';
    return $body;
  }

  function GetMeta()
  {
    return '
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
          <head>
            <meta name="viewport" content="width=device-width, initial-scale=1.0" />
            <meta name="x-apple-disable-message-reformatting" />
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <meta name="color-scheme" content="light dark" />
            <meta name="supported-color-schemes" content="light dark" />
            <title></title>';
  }

  function GetCSS()
  {
    return '
            <style type="text/css" rel="stylesheet" media="all">
            /* Base ------------------------------ */
            
            @import url("https://fonts.googleapis.com/css?family=Nunito+Sans:400,700&display=swap");
            body {
              width: 100% !important;
              height: 100%;
              margin: 0;
              -webkit-text-size-adjust: none;
            }
            
            a {
              color: #3869D4;
            }
            
            a img {
              border: none;
            }
            
            td {
              word-break: break-word;
            }
            
            .preheader {
              display: none !important;
              visibility: hidden;
              mso-hide: all;
              font-size: 1px;
              line-height: 1px;
              max-height: 0;
              max-width: 0;
              opacity: 0;
              overflow: hidden;
            }
            /* Type ------------------------------ */
            
            body,
            td,
            th {
              font-family: "Nunito Sans", Helvetica, Arial, sans-serif;
            }
            
            h1 {
              margin-top: 0;
              color: #333333;
              font-size: 22px;
              font-weight: bold;
              text-align: left;
            }
            
            h2 {
              margin-top: 0;
              color: #333333;
              font-size: 16px;
              font-weight: bold;
              text-align: left;
            }
            
            h3 {
              margin-top: 0;
              color: #333333;
              font-size: 14px;
              font-weight: bold;
              text-align: left;
            }
            
            td,
            th {
              font-size: 16px;
            }
            
            p,
            ul,
            ol,
            blockquote {
              margin: .4em 0 1.1875em;
              font-size: 16px;
              line-height: 1.625;
            }
            
            p {
                text-align:justify;
            }

            p.sub {
              font-size: 13px;
            }
            /* Utilities ------------------------------ */
            
            .align-right {
              text-align: right;
            }
            
            .align-left {
              text-align: left;
            }
            
            .align-center {
              text-align: center;
            }
            /* Buttons ------------------------------ */
            
            .button {
              background-color: #00A49C;
              border-top: 10px solid #00A49C;
              border-right: 18px solid #00A49C;
              border-bottom: 10px solid #00A49C;
              border-left: 18px solid #00A49C;
              display: inline-block;
              color: #FFF;
              text-decoration: none;
              border-radius: 3px;
              box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16);
              -webkit-text-size-adjust: none;
              box-sizing: border-box;
            }
            
            @media only screen and (max-width: 500px) {
              .button {
                width: 100% !important;
                text-align: center !important;
              }
            }
            /* Attribute list ------------------------------ */
            
            .attributes {
              margin: 0 0 21px;
            }
            
            .attributes_content {
              background-color: #F4F4F7;
              padding: 16px;
            }
            
            .attributes_item {
              padding: 0;
            }
            
            body {
              background-color: #F4F4F7;
              color: #51545E;
            }
            
            p {
              color: #51545E;
            }
            
            p.sub {
              color: #6B6E76;
            }
            
            .email-wrapper {
              width: 100%;
              margin: 0;
              padding: 0;
              -premailer-width: 100%;
              -premailer-cellpadding: 0;
              -premailer-cellspacing: 0;
              background-color: #F4F4F7;
            }
            
            .email-content {
              width: 100%;
              margin: 0;
              padding: 0;
              -premailer-width: 100%;
              -premailer-cellpadding: 0;
              -premailer-cellspacing: 0;
            }
            /* Masthead ----------------------- */
            
            .email-masthead {
              padding: 25px 0;
              text-align: center;
              background-color: #00A49C!important;
            }
            
            .email-masthead_logo {
              width: 94px;
            }
            
            .email-masthead_name {
              font-size: 25px;
              font-weight: bold;
              color: #FFF!important;
              text-decoration: none;
              text-shadow: 0 1px 0 white;
            }
            /* Body ------------------------------ */
            
            .email-body {
              width: 100%;
              margin: 0;
              padding: 0;
              -premailer-width: 100%;
              -premailer-cellpadding: 0;
              -premailer-cellspacing: 0;
              background-color: #FFFFFF;
            }
            
            .email-body_inner {
              width: 570px;
              margin: 0 auto;
              padding: 0;
              -premailer-width: 570px;
              -premailer-cellpadding: 0;
              -premailer-cellspacing: 0;
              background-color: #FFFFFF;
            }
            
            .email-footer {
              background-color: #00A49C!important;
              width: 570px;
              margin: 0 auto;
              padding: 0;
              -premailer-width: 570px;
              -premailer-cellpadding: 0;
              -premailer-cellspacing: 0;
              text-align: center;
            }
            
            .email-footer p {
              color: #FFF;
            }
            
            .body-action {
              width: 100%;
              margin: 30px auto;
              padding: 0;
              -premailer-width: 100%;
              -premailer-cellpadding: 0;
              -premailer-cellspacing: 0;
              text-align: center;
            }
            
            .body-sub {
              margin-top: 25px;
              padding-top: 25px;
              border-top: 1px solid #EAEAEC;
            }
            
            .content-cell {
              padding: 35px;
            }
            /*Media Queries ------------------------------ */
            
            @media only screen and (max-width: 600px) {
              .email-body_inner,
              .email-footer {
                width: 100% !important;
              }
            }
            
            @media (prefers-color-scheme: dark) {
              body,
              .email-body,
              .email-body_inner,
              .email-content,
              .email-wrapper,
              .email-masthead,
              .email-footer {
                background-color: #00A49C !important;
                color: #FFF !important;
              }
              p,
              ul,
              ol,
              blockquote,
              h1,
              h2,
              h3 {
                color: #FFF !important;
              }
              .attributes_content,
              .discount {
                background-color: #222 !important;
              }
              .email-masthead_name {
                text-shadow: none !important;
              }
            }
            
            :root {
              color-scheme: light dark;
              supported-color-schemes: light dark;
            }
            </style>
            <!--[if mso]>
            <style type="text/css">
              .f-fallback  {
                font-family: Arial, sans-serif;
              }
            </style>
          <![endif]-->
          </head>';
  }
}