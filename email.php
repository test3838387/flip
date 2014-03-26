<?php

require("bin/mail/lib/class.phpmailer.php");

$mail             = new PHPMailer();

$mail->IsSMTP(); // telling the class to use SMTP
$mail->Host       = "cse.msstate.edu"; // SMTP server
$mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
                                           // 0 = no debug output
                                           // 1 = errors and messages
                                           // 2 = messages only
                                           
// change from address to your own email address
$mail->SetFrom('dcsp04@cse.msstate.edu', 'DCSP Group 0004');

//$mail->AddReplyTo("name@yourdomain.com","First Last");

$mail->Subject    = "PHPMailer Test";

$mail->Body    = "This is a test message. Can you hear me now?"; 

// change address to your own email address
$address = "hunter.lovvorn@me.com";
$mail->AddAddress($address, "Hunter Lovvorn");


if(!$mail->Send())
{
  echo "Mailer Error: " . $mail->ErrorInfo;
}
else
{
  echo "Message sent!";
}

?>