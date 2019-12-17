<?php
require_once("minecraft/modules/account.php");

class Skin
{
	private static $cacheDirectory = __DIR__ . "/../skincache/";
	private static $defaultSkinFile = __DIR__ . "/../steve.png";
	private static $timeToLive = 60 * 60 * 24;
	
	public static function GetSkin($nameOrUUID)
	{	
		if ($uuid = Account::ResolveUUID($nameOrUUID))
		{
			//clear skincachefiles if they are over 1 hour old
			$cachedSkins = glob(self::$cacheDirectory . "*.png");
			foreach ($cachedSkins as $cachedSkin)
			{
				if (filemtime($cachedSkin) < time() - self::$timeToLive)
					unlink($cachedSkin);
			}
			
			$skinCacheFile = self::$cacheDirectory . "{$uuid}.png";
		
			//obtain skin location from mojang profile api if the skinfile doesn't exist yet/anymore
			if (!file_exists($skinCacheFile))
			{
				$profile = json_decode(@file_get_contents("https://sessionserver.mojang.com/session/minecraft/profile/{$uuid}"));
				
				if ($profile && !isset($profile->error))
				{
					$profile = json_decode(base64_decode($profile->properties[0]->value));
					
					if ($profile)
					{
						$skinURL = $profile->textures->SKIN->url;
						
						//copy to cache
						if ($skinURL)
							copy($skinURL, $skinCacheFile);
					}
				}
			}
			
			//check again, and return it
			if (file_exists($skinCacheFile))
				return @file_get_contents($skinCacheFile);				
		}
		
		//return default skin on failure
		return @file_get_contents(self::$defaultSkinFile);
	}
	
	//18, 36, 54, 72, 90, 108, 126, 144, 162, 180, 198
	public static function CreatePlayerHead($nameOrUUID, $size)
	{
		if ($skin = self::GetSkin($nameOrUUID))
		{
			$orig = @imagecreatefromstring($skin);		
			$img = imagecreatetruecolor($size, $size);
			$col = imagecolorallocate($img, 246, 168, 96);
			imagecolortransparent($img, $col);
			imagefilledrectangle($img, 0, 0, $size, $size, $col);
			imagecolortransparent($img);
			imagecopyresized($img, $orig, $size / 18, $size / 18, 8, 8, 8 / 9 * $size, 8 / 9 * $size, 8, 8);
			imagecopyresized($img, $orig, 0, 0, 40, 8, $size, $size, 8, 8);
			imagedestroy($orig);
			
			ob_start();
			imagepng($img);
			$imageString = ob_get_clean();
			imagedestroy($img);
			
			return $imageString;
		}
	}
}
?>
