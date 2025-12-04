<?php

namespace Framework\Adapters;

include_once PATH_FRAMEWORK_LIBS . DS . 'vendor' . DS . 'autoload.php';

use CURLFile;
use Framework\Core\App;
use Framework\Utils\Str;
use Framework\Interfaces\IMailClient;
use Framework\Enums\EmailProvider;
use Framework\Utils\File;
use PHPMailer\PHPMailer\{PHPMailer, SMTP, Exception};
use \SendGrid\Mail\Mail;

final class MailClient implements IMailClient {

    private static $instance;

    private string $_apiKey = '';
    private EmailProvider $_provider = EmailProvider::PHPMailer;
    
    private function __construct() {}

    public static function getInstance() : IMailClient {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function setApiKey(string $apiKey) : IMailClient {
        $this->_apiKey = $apiKey;
        return $this;
    }

    public function setProvider(EmailProvider $provider) : IMailClient {
        $this->_provider = $provider;
        return $this;
    }

    public function send(array $recipients = [], string $subject = '', string $body = '', array $attachments = [], array $ccs = EMAIL_DEFAULT_CC, array $bccs = EMAIL_DEFAULT_BCC, string $senderName = EMAIL_DEFAULT_SENDER_NAME, string $senderEmail = EMAIL_DEFAULT_SENDER_EMAIL) {
        switch ($this->_provider) {
            case EmailProvider::Elastic:
                return $this->_sendWithElastic($recipients, $subject, $body, $attachments, $ccs, $bccs, $senderName, $senderEmail);

            case EmailProvider::SendGrid:
                return $this->_sendWithSendGrid($recipients, $subject, $body, $attachments, $ccs, $bccs, $senderName, $senderEmail);

            default:
                return $this->_sendWithPHPMailer($recipients, $subject, $body, $attachments, $ccs, $bccs, $senderName, $senderEmail);
        }
	}

    private function _sendWithSendGrid(array $recipients = [], string $subject = '', string $body = '', array $attachments = [], array $ccs = EMAIL_DEFAULT_CC, array $bccs = EMAIL_DEFAULT_BCC, string $senderName = EMAIL_DEFAULT_SENDER_NAME, string $senderEmail = EMAIL_DEFAULT_SENDER_EMAIL) {
        if (!$this->_apiKey) return 'API key is required.';

        $email = new Mail();
        $email->setFrom(
            $senderEmail,
            $senderName
        );
        /*$email->setSubject($subject);
        // Replace the email address and name with your recipient
        $email->addTo(
            'email',
            'Name'
        );
        $email->addContent(
            'text/html',
            '<strong>This is an email delivery test from PayPen using a premium mailing service.</strong><p>It is not free but we will get our own account.</p>'
        );//echo 'APIKey: ' . $this->_apiKey.'<br>';
        $sendgrid = new \SendGrid($this->_apiKey);
        try {
            $response = $sendgrid->send($email);
            printf("Response status: %d\n\n", $response->statusCode());

            $headers = array_filter($response->headers());
            echo "Response Headers\n\n";
            foreach ($headers as $header) {
                echo '- ' . $header . "\n";
            }
        } catch (Exception $e) {
            echo 'Caught exception: '. $e->getMessage() ."\n";
        }*/
    }

    private function _sendWithPHPMailer(array $recipients = [], string $subject = '', string $body = '', array $attachments = [], array $ccs = EMAIL_DEFAULT_CC, array $bccs = EMAIL_DEFAULT_BCC, string $senderName = EMAIL_DEFAULT_SENDER_NAME, string $senderEmail = EMAIL_DEFAULT_SENDER_EMAIL) {
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
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
			$mail->Host = EMAIL_HOST;
			$mail->Username = EMAIL_USERNAME;
			$mail->Password = EMAIL_PASSWORD;
			$mail->Port = EMAIL_HOST_PORT;

            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];
            
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

    private function _sendWithElastic(array $recipients = [], string $subject = '', string $body = '', array $attachments = [], array $ccs = EMAIL_DEFAULT_CC, array $bccs = EMAIL_DEFAULT_BCC, string $senderName = EMAIL_DEFAULT_SENDER_NAME, string $senderEmail = EMAIL_DEFAULT_SENDER_EMAIL) {
        try {
            $url = 'https://api.elasticemail.com/v2/email/send';

            // set recipients
            $to = '';
            foreach($recipients as $recipient) {
                if (is_array($recipient)) {
                    if (count($recipient) > 1) {
                        if (Str::isValidEmail($recipient[0])) {
                            $to .= "$recipient[1]<$recipient[0]>;";
                        } else {
                            $to .= "$recipient[0]<$recipient[1]>;";
                        }
                    } else {
                        $to .= "$recipient[0];";
                    }
                } else {
                    $to .= "$recipient;";
                }
            }

            // set cc
            $cc = '';
            foreach($ccs as $recipient) {
                if (is_array($recipient)) {
                    if (count($recipient) > 1) {
                        if (Str::isValidEmail($recipient[0])) {
                            $cc .= "$recipient[1]<$recipient[0]>;";
                        } else {
                            $cc .= "$recipient[0]<$recipient[1]>;";
                        }
                    } else {
                            $cc .= "$recipient[0];";
                    }
                } else {
                    $cc .= "$recipient;";
                }
            }

            // set bcc
            $bcc = '';
            foreach($bccs as $recipient) {
                if (is_array($recipient)) {
                    if (count($recipient) > 1) {
                        if (Str::isValidEmail($recipient[0])) {
                            $bcc .= "$recipient[1]<$recipient[0]>;";
                        } else {
                            $bcc .= "$recipient[0]<$recipient[1]>;";
                        }
                    } else {
                            $bcc .= "$recipient[0];";
                    }
                } else {
                    $bcc .= "$recipient;";
                }
            }

            $post = [
                'from' => $senderEmail,
                'fromName' => $senderName,
                'apikey' => $this->_apiKey,
                'subject' => $subject,
                'msgTo' => $to,
                'bodyHtml' => $body,
                'bodyText' => '',
                'isTransactional' => false
            ];
            if ($cc) {
                $post['msgCC'] = $cc;
            }
            if ($bcc) {
                $post['msgBcc'] = $bcc;
            }

            // set attachments
			if ($attachments && count($attachments) > 0) {
                $i = 1;
				foreach($attachments as $attachment) {
                    $fileName = '';
                    $filePath = '';
                    if (is_array($attachment)) {
                        if (count($attachment) > 1) {
                            $filePath = $attachment[0];
                            $fileName = $attachment[1];
                        } else {
                            $fileInfo = File::getFileInfo($attachment[0]);
                            $filePath = $attachment[0];
                            $fileName = $fileInfo['filename'];
                        }
                    } else {
                        $fileInfo = File::getFileInfo($attachment);
                        $filePath = $attachment;
                        $fileName = $fileInfo['filename'];
                    }
                    $fileType = mime_content_type($filePath);

                    $post["file_$i"] = new CURLFile($filePath, $fileType, $fileName);
                    $i++;
				}
			}
           //var_dump($post); return; 
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $post,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false,
                CURLOPT_SSL_VERIFYPEER => false
            ));
            
            $result=curl_exec ($ch);
            curl_close ($ch);

            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}