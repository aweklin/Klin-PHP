<?php

namespace Framework\Adapters;

use Framework\Core\App;
use Framework\Utils\Str;
use Framework\Interfaces\IMailClient;
use PHPMailer\PHPMailer\{PHPMailer, SMTP, Exception};

include_once PATH_FRAMEWORK_LIBS . DS . 'vendor' . DS . 'autoload.php';

final class MailClient implements IMailClient {

    private static $instance;
    
    private function __construct() {}

    public static function getInstance() : IMailClient {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function send(array $recipients = [], string $subject, string $body, array $attachments = [], array $ccs = EMAIL_DEFAULT_CC, array $bccs = EMAIL_DEFAULT_BCC, string $senderName = EMAIL_DEFAULT_SENDER_NAME, string $senderEmail = EMAIL_DEFAULT_SENDER_EMAIL) {
		try {
            // some validations
            if (!$recipients) return 'Recipient(s) is required.';
            if (!$subject) return 'Mail subject is required.';
            if (!$body) return 'Mail body is required.';
			if (!App::hasInternetAccess()) {
				return 'No internet connection.';
			}
            
            // initialize mailer
			$mail = new PHPMailer(true);
            
            // setup smtp
			$mail->isSMTP();
			$mail->SMTPAuth = true;
			$mail->SMTPSecure = EMAIL_HOST_SECURITY_TYPE;
            
			$mail->Host = EMAIL_HOST;
			$mail->Username = EMAIL_USERNAME;
			$mail->Password = EMAIL_PASSWORD;
			$mail->Port = EMAIL_HOST_PORT;
            
            // set sender
            $mail->setFrom($senderEmail, $senderName);
            
            // set reply to
            $mail->addReplyTo($senderEmail, $senderName);
            
            // set recipients
            foreach($recipients as $recipient) {
                if (is_array($recipient)) {
                    if (count($recipient) > 1) {
                        if (Str::isValidEmail($recipient[0])) {
                            $mail->addAddress($recipient[0], $recipient[1]);
                        } else {
                            $mail->addAddress($recipient[1], $recipient[0]);
                        }
                    } else {
                        $mail->addAddress($recipient[0]);
                    }
                } else {
                    $mail->addAddress($recipient);
                }
            }

            // set cc
            if ($ccs) {
                foreach($ccs as $cc) {
                    if (is_array($cc)) {
                        if (count($cc) > 1) {
                            if (Str::isValidEmail($cc[0])) {
                                $mail->addCC($cc[0], $cc[1]);
                            } else {
                                $mail->addCC($cc[1], $cc[0]);
                            }
                        } else {
                            $mail->addCC($cc[0]);
                        }
                    } else {
                        $mail->addCC($cc);
                    }
                }
            }

            // set bcc
            if ($bccs) {
                foreach($bccs as $bcc) {
                    if (is_array($bcc)) {
                        if (count($bcc) > 1) {
                            if (Str::isValidEmail($bcc[0])) {
                                $mail->addBCC($bcc[0], $bcc[1]);
                            } else {
                                $mail->addBCC($bcc[1], $bcc[0]);
                            }
                        } else {
                            $mail->addBCC($bcc[0]);
                        }
                    } else {
                        $mail->addBCC($bcc);
                    }
                }
            }
            
            // set attachments
			if ($attachments && count($attachments) > 0) {
				foreach($attachments as $attachment) {
                    if (is_array($attachment)) {
                        if (count($attachment) > 1) {
                            $mail->addAttachment($attachment[0], $attachment[1]);
                        } else {
                            $mail->addAttachment($attachment);
                        }
                    } else {
                        $mail->addAttachment($attachment);
                    }					
				}
			}
			
			$mail->isHTML(true);
			
			$mail->Subject = $subject;
			$mail->Body = $body;
			$mail->AltBody = $body;		
			
			$result = 'Failed to send e-mail for an unknown reason.';
			
			if ($mail->send()) {
				$result = 'OK';
			} else {
				$result = 'Error sending e-mail: ' . $mail->ErrorInfo;
			}
			
			$mail = null;
		} catch (Exception $ex) {
			$result = $ex->getMessage();
		}
		
		return $result;	
	}

}