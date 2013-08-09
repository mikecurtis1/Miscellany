<pre>
<?php 

echo "SMTP\n";

if ( function_exists(fsockopen) ) {
  echo "fsockopen EXISTS\n";
}

function smtpSocket($smtpServer='',$port='',$timeout='',$username='',$password='',$localhost='',$to='',$nameto='',$subject='',$message=''){

    $from='nobody@idsproject.org';
    $namefrom='nobody';
    $newLine="\r\n";

    //Connect to the host on the specified port
    $smtpConnect  = fsockopen($smtpServer, $port, $errno, $errstr, $timeout);
    $smtpResponse = fgets($smtpConnect, 515);
    if ( empty($smtpConnect) ) {
		$errorLog['connection'] = "Failed to connect: {$smtpResponse}";
		return $errorLog;
    } else {
		$errorLog['connection'] = "Connected: {$smtpResponse}";
    }

    //Request Auth Login
    fputs($smtpConnect,"AUTH LOGIN" . $newLine);
    $smtpResponse = fgets($smtpConnect, 515);
    $errorLog['authrequest'] = "{$smtpResponse}";

    //Send username
    fputs($smtpConnect, base64_encode($username) . $newLine);
    $smtpResponse = fgets($smtpConnect, 515);
    $errorLog['authusername'] = "{$smtpResponse}";

    //Send password
    fputs($smtpConnect, base64_encode($password) . $newLine);
    $smtpResponse = fgets($smtpConnect, 515);
    $errorLog['authpassword'] = "{$smtpResponse}";

    //Say Hello to SMTP
    fputs($smtpConnect, "HELO {$localhost}" . $newLine);
    $smtpResponse = fgets($smtpConnect, 515);
    $errorLog['heloresponse'] = "{$smtpResponse}";

    //Email From
    fputs($smtpConnect, "MAIL FROM: {$from}" . $newLine);
    $smtpResponse = fgets($smtpConnect, 515);
    $errorLog['mailfromresponse'] = "{$smtpResponse}";

    //Email To
    fputs($smtpConnect, "RCPT TO: {$to}" . $newLine);
    $smtpResponse = fgets($smtpConnect, 515);
    $errorLog['mailtoresponse'] = "{$smtpResponse}";

    //The Email
    fputs($smtpConnect, "DATA" . $newLine);
    $smtpResponse = fgets($smtpConnect, 515);
    $errorLog['data1response'] = "{$smtpResponse}";

    //Construct Headers
    $headers  = "MIME-Version: 1.0" . $newLine;
    $headers .= "Content-type: text/html; charset=utf-8" . $newLine;
    $headers .= "To: {$nameto} <{$to}>" . $newLine;
    $headers .= "From: {$namefrom} <{$from}>" . $newLine;

    fputs($smtpConnect, "To: {$to}\nFrom: {$from}\nSubject: {$subject}\n{$headers}\n\n{$message}\n.\n");
    $smtpResponse = fgets($smtpConnect, 515);
    $errorLog['data2response'] = "{$smtpResponse}";

    // SMTP Loggin Out
    fputs($smtpConnect,"QUIT" . $newLine);
    $smtpResponse = fgets($smtpConnect, 515);
    $errorLog['quitresponse'] = "{$smtpResponse}";

    return $errorLog;
}

/*
$smtpServer = 'smtp.example.com';
$domain = 'example.com';
$user = 'username';
$port 	= '25';
$timeout 	= '30';
$username 	= $user.'@'.$domain;
$password 	= 'password';
$localhost 	= 'smtp.example.com';
$to = 'recipient@somewhere.com';
$nameto = 'John Doe';
$subject = 'Test '.time();
$message = 'Relay test '.time();

$response = smtpSocket($smtpServer,$port,$timeout,$username,$password,$localhost,$to,$nameto,$subject,$message);
*/

$response = smtpSocket();

print_r($response);

echo "\n\nEND\n";

?>
</pre>
