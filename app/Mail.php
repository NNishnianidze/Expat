<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

class Mail
{
    public function send(string $email, string $token)
    {
        $html = <<<HTMLBody
            <h1 >
                We recived a request to reset your password.
            </h1>
            <p>
            Use the link below to set up a new password for your account. 
            If you did not request to reset password, ignore this email 
            and the link will expire on it own.
            </p>
            <a class="btn btn-primary p-3 fw-700" href='http://localhost:8000/new-password?email=$email&token=$token'>Choose a new Password</a>
        HTMLBody;;

        $email = (new Email())
            ->from('support@expat.com')
            ->to($email)
            ->subject('Reset Password')
            ->html($html);

        $transport = Transport::fromDsn($_ENV['MAILER_DSN']);

        $mailer = new Mailer($transport);

        $mailer->send($email);
    }
}
