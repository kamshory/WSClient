<?php
require dirname(dirname(__FILE__))."/websocket-client.php";
$server = '127.0.0.1';
$port = 8888;
$username = 'qa';
$password = '4lt0@1234';
$headers = array(
'Authorization: Basic '.base64_encode($username.':'.$password)
);

$request = array(
	'command'=>'ping',
	'channel'=>'sms',
	'data'=>array(
		array(
			'time' => time(0)
		)
			
	)
);
$message = json_encode($request, JSON_PRETTY_PRINT);

if( $sp = websocket_open($server, $port, $headers, $errstr, 10, false) ) 
{
  websocket_write($sp, $message);
  $response = websocket_read($sp, $errorcode, $errstr); 
  echo $response."\r\n";
}
else 
{
  echo "Failed to connect to server\n";
  echo "Server responed with: $errstr\n";
}

?>