<?php
require 'phpmailer/class.phpmailer.php';
require 'phpmailer/class.smtp.php';

/*

全局配置示例：

	$g_smtp_host = 'ssl://smtp.qiye.aliyun.com'; // 'smtp.oliveche.com'
	$g_smtp_port = 465; // 25
	$g_smtp_username = 'liangjian@oliveche.com';
	$g_smtp_password = 'XXXX';
	$g_from_email = $g_smtp_username;
	$g_from_name = 'PDI System';

交互接口：

	sendMail(type, id)  -- 须根据业务需求修改。在api_functions.php中定义。
	Mailer.test() -- 仅用于测试

内部接口：

	AC_Mailer::sendMail($to, $subject, $content)

*/

class JDMailer
{
	// - to: email地址，可以一个或多个。多个以','分隔
	// - content: html格式内容
	static function sendMail($to, $subject, $content) {
		foreach (["g_smtp_host", "g_smtp_port", "g_smtp_username", "g_smtp_password", "g_from_email", "g_from_name"] as $e) {
			if (! isset($GLOBALS[$e]))
				throw new MyException(E_SERVER, "missing email config: $e", "缺少邮件配置");
		}
		$mail = new PHPMailer;

		$mail->CharSet = 'UTF-8';
		$mail->isSMTP();
		$mail->SMTPSecure = 'tls';
		$mail->SMTPAuth = true;
		$mail->Host = $GLOBALS["g_smtp_host"]; // 'ssl://smtp.qiye.aliyun.com'; // 'smtp.oliveche.com'
		$mail->Port = $GLOBALS["g_smtp_port"]; // 465; // 25
		$mail->Username = $GLOBALS["g_smtp_username"]; // 'liangjian@oliveche.com';
		$mail->Password = $GLOBALS["g_smtp_password"];

		$mail->setFrom($GLOBALS["g_from_email"], $GLOBALS["g_from_name"]);
		foreach (explode(',', $to) as $e) {
			$mail->addAddress($e);
		}
		// $mail->addCC('cc@example.com');

		// $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
		// $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
		$mail->isHTML(true);                                  // Set email format to HTML

		$mail->Subject = $subject;
		$mail->Body    = $content;
		// $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

		logit("sendmail to " . $mail->Username . ": " . $mail->Subject);
		if(!$mail->send()) {
			logit('Mailer Error: ' . $mail->ErrorInfo);
			throw new MyException(E_PARAM, "mailer error");
		}
	}
}
