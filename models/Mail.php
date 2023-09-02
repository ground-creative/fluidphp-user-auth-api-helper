<?php

	namespace helpers\UserAuthApi\models;
	
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	use \helpers\Logger\Manager as Logger;

	class Mail
	{
		public static function sendUserVerification($from, $fromName, $data)
		{
			Logger::msg('sending verification email to ' . $data['email'])
				->data([$from, $fromName, $data])
				->script(__METHOD__, __LINE__)
				->save( );
			$tpl = "/defaults/user-notifications";
			$manager = new \helpers\EmailManager\Core($tpl, [$data['lang'], 'en_GB']);
			$data['_client_name'] = $data['firstname'];
			$data['_user_lang'] = $data['lang'];
			$data['_user_email'] = $data['email'];
			$data['_verification_code'] = $data['verification_code'];
			$email_data = array_merge($data, \App::options("user-auth-api.mail_tpls_data"));
			$manager->data( $email_data )->compile('view.user-verification')->subject("xml:user_registration_subject", true );
			$mailer = static::buildMailer($data['email'], $manager->getTemplate(), $manager->getSubject(), $from, $fromName);
			if ($err = static::_sendMail($mailer)) 
			{
				Logger::error("could not send user verification email, some mailer error occured")
				->data($err)
				->script(__METHOD__, __LINE__)
				->save();
			} 
			else 
			{
				Logger::msg("user verification email sent successfully")
					->data([$data['email'], $manager->getTemplate(), 'no subject', $from, $fromName])
					->script(__METHOD__, __LINE__)
					->save();
			}
		}
		
		public static function sendUserResetPass($from, $fromName, $data)
		{
			Logger::msg('sending reset password email to ' . $data['email'])
				->data([$from, $fromName, $data])
				->script(__METHOD__, __LINE__)
				->save( );
			$tpl = "/defaults/user-notifications";
			$manager = new \helpers\EmailManager\Core($tpl, [$data['lang'], 'en_GB']);
			$data['_user_lang'] = $data['lang'];
			$data['_user_email'] = $data['email'];
			$email_data = array_merge($data, \App::options("user-auth-api.mail_tpls_data"));
			$manager->data( $email_data )->compile('view.reset-password')->subject("xml:user_reset_password_subject", true );
			$mailer = static::buildMailer($data['email'], $manager->getTemplate(), $manager->getSubject(), $from, $fromName);			
			if ($err = static::_sendMail($mailer)) 
			{
				Logger::error("could not send user reset password email, some mailer error occured")
				->data($err)
				->script(__METHOD__, __LINE__)
				->save();
			} 
			else 
			{
				Logger::msg("user reset password email sent successfully")
					->data([$data['email'], $manager->getTemplate(), 'no subject', $from, $fromName])
					->script(__METHOD__, __LINE__)
					->save();
			}
		}
			
		public static function buildMailer($address, $body, $subject = 'no subject', $sender = null, $senderName = null)
		{
			$options = \App::options('user-auth-api.mail_config');
			Logger::debug('building phpmailer object')->script(__METHOD__, __LINE__)->save( );
			$mailer = new PHPMailer();
			$mailer->CharSet = mb_detect_encoding($body); 
			$mailer->IsSMTP();
			$mailer->Mailer = "smtp";
			if ($options['log'])
			{
				$mailer->SMTPDebug = $options['debug_level'];
				$mailer->Debugoutput = function($str, $level) 
				{
					file_put_contents(\App::options('user-auth-api.mail_config.log'), gmdate('Y-m-d H:i:s') . "\t$level\t$str\n", FILE_APPEND | LOCK_EX);
				};
			}
			$mailer->SMTPAuth = TRUE;
			$mailer->SMTPSecure = $options['secure'];
			$mailer->Port = $options['port'];
			$mailer->Host = $options['address'];
			$mailer->Username = $options['username'];
			$mailer->Password = $options['password'];
			$mailer->IsHTML(true);
			$mailer->SetFrom($sender, $senderName);
			$mailer->AddReplyTo($sender, $senderName);
			$mailer->Subject = $subject;
			$mailer->MsgHTML($body); 
			$address = (is_string($address) ? explode(',', $address) : $address);
			foreach($address as $receiver)
			{
				$mailer->addAddress($receiver); 
			}
			//$mailer->Body = $body;
			//$mailer->AltBody = strip_tags($body);
			return $mailer;
		}

		protected static function _sendMail($mailer)
		{
			return ($mailer->send()) ? null :  $mailer->ErrorInfo;
		}
	}