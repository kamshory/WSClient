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
$path = "/ws?sessid=1";

$headers = array(
	'Authorization: Basic '.base64_encode($username.':'.$password),
	'Content-type: application/json'
);


function process_response($response)
{
	echo "$response\r\n";
	$json = json_decode($response, true);
	if($json != null && !empty($json))
	{
		$command = $json['command'];
		$data = $json['data'];
		if($command == 'send-message')
		{
			/**
			 * Write your code here
			 */
		}
		else if($command == 'connect')
		{
			echo "Connected\r\n";
		}
	}
}

echo "Server: $server:$port\n";

$request = array(
	'command'=>'receive-message',
	'channel'=>$channel,
	'data'=>array(
	)
);
$message = json_encode($request, JSON_PRETTY_PRINT);

while(true)
{
	echo "Connecting to $server:$port ...\r\n";
	$sp = websocket_open($server, $port, $headers, $errstr, 10, false, false, $path);
	if($sp) 
	{
		websocket_write($sp, $message);
		$loop2 = true;
		while($loop2)
		{
			try
			{
				$response = websocket_read($sp, $errorcode, $errstr);
				if(strlen($response) > 7)
				{
					process_response($response);
				}
				else
				{
					usleep(200000);
				}
			}
			catch(Exception $e)
			{
				$message = $e->getMessage();
				if(stripos($message, 'not') !== false 
					&& stripos($message, 'valid') !== false 
					&& stripos($message, 'stream') !== false 
					&& stripos($message, 'resource') !== false)
					{
						$loop2 = false;
						break 1;
					}
				sleep(1);
			}
			$loop2 = !@feof($sp);
		}

	}
	else 
	{
		echo "Failed to connect to server\n";
		echo "Server responed with: $errstr\n";
	}
	sleep(5);
}

?>