<?php
require dirname(dirname(__FILE__))."/websocket-client.php";

$server = '127.0.0.1';
$port = 8888;
$username = 'qa';
$password = '4lt0@1234';
$channel = 'sms';

putenv('MQ_SERVER_HOST='.$server);
putenv('MQ_SERVER_PORT='.$port);
putenv('MQ_CLIENT_USERNAME='.$username);
putenv('MQ_CLIENT_PASSWORD='.$password);
putenv('MQ_CANNEL_NAME='.$channel);

$server = getenv('MQ_SERVER_HOST');
$port = getenv('MQ_SERVER_PORT');
$username = getenv('MQ_CLIENT_USERNAME');
$password = getenv('MQ_CLIENT_PASSWORD');
$channel = getenv('MQ_CANNEL_NAME');
$path = "/ws?sessid=2";

$headers = array(
	'Authorization: Basic '.base64_encode($username.':'.$password),
	'Content-type: application/json'
);

$request = array(
	'command'=>'send-message',
	'channel'=>$channel,
	'data'=>array(
		array(
			'id'=>uniqid(),
			'time' => time(0),
			'receiver'=>'08126666666',
			'message'=>'Haloo. Pesan ini dikirim '.date('j F Y H:i:s')
		)	
	)
);
$message = json_encode($request, JSON_PRETTY_PRINT);

if($sp = websocket_open($server, $port, $headers, $errstr, 10, false, false, $path)) 
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