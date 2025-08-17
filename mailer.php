<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . "/vendor/autoload.php";


$mail = new PHPMailer(true);

// $mail->SMTPDebug = SMTP::DEBUG_SERVER;

$mail->isSMTP();
$mail->SMTPAuth = true;

$mail->Host       = 'smtp.gmail.com';
$mail->Username   = 'thelmyatthankyaw@gmail.com';      // your Gmail
$mail->Password   = 'pksqcxknnpzcuaur';         // Gmail app password


$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;


$mail->isHtml(true);

return $mail;