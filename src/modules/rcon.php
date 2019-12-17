<?php
class Rcon
{
	public static $connected = false;
	private static $stream;
	private static $requestID;

	public static function Connect($host, $port, $password)
	{
		//connect
		if ($stream = @fsockopen($host, $port, $errno, $errstr, 1))
		{
			//authenticate
			$data = pack("VV", 0, 3) . $password . "\x00\x00\x00";
			$data = pack("V", strlen($data)) . $data;
			if (fwrite($stream, $data, strlen($data)) === strlen($data))
			{
				$size = fread($stream, 4);
				$size = unpack("V1size", $size);
				$size = $size["size"];
				$packet = fread($stream, $size);
				$packet = unpack("V1requestId/V1response/a*string/a*string2", $packet);
				if ($packet["requestId"] > -1 && $packet["response"] == 2)
				{
					self::$stream = $stream;
					self::$requestID = 1;
					self::$connected = true;

					return true;
				}
			}
		}

		return false;
	}

	public static function RunCommand($command)
	{
		//late-connect, so it will only connect when needed
		if (self::$connected || Rcon::Connect('[REDACTED]'))
		{
			//request
			$data = pack("VV", self::$requestID++, 2) . $command . "\x00\x00\x00";
			$data = pack("V", strlen($data)) . $data;
			if (@fwrite(self::$stream, $data, strlen($data)) === strlen($data))
			{
				//read response
				$size = fread(self::$stream, 4);

				if ($size)
				{
					$size = unpack("V1size", $size);
					$size = $size["size"];
					$packet = fread(self::$stream, $size);
					$packet = unpack("V1requestId/V1response/a*string", $packet);
					if ($packet["requestId"] > -1 && $packet["response"] == 0)
						return $packet["string"];
				}
				else
					echo "Read failed for $command response";
			}
			else
				echo "Write failed for $command (data: $data)";
		}

		return false;
	}
}
?>
