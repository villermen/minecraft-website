<?php 
class Misc
{
	//returns a string with an optional length and allowed chars
	public static function GenerateRandomString($length = 10, $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789")
	{
		$string = "";
		for ($i = 0; $i < $length; $i++)
		{
			$string .= $chars[rand(0, strlen($chars) - 1)];
		}
		return $string;
	}
	
	public static function TimeAgo($time)
	{
		$daysAgo = floor((time() - $time) / 86400);
		if ($daysAgo > 1)
			return "{$daysAgo} days ago";
		if ($daysAgo == 1 )
			return "1 day ago";
			
		$hoursAgo = floor((time() - $time) / 3600);
		if ($hoursAgo > 1)
			return "{$hoursAgo} hours ago";
		if ($hoursAgo == 1)
			return "1 hour ago";
			
		$minutesAgo = floor((time() - $time) / 60);
		if ($minutesAgo > 1)
			return "{$minutesAgo} minutes ago";
		if ($minutesAgo == 1)
			return "1 minute ago";
			
		$secondsAgo = time() - $time;
		if ($secondsAgo > 1 )
			return "$secondsAgo seconds ago";
		if ($secondsAgo == 1)
			return "1 second ago";
		
		return "just now";
	}
/*
//returns the rankname of a rankvalue, and optionally a color
function rankValueToString($rankValue,&$color="gray")
	{
	switch ($rankValue)
		{
		case 1:
			$rankName="member";
			$color="blue";
			break;
		case 2:
			$rankName="member+";
			$color="blue";
			break;
		case 3:
			$rankName="donator-";
			$color="green";
			break;
		case 4:
			$rankName="donator";
			$color="green";
			break;
        case 5:
			$rankName="moneybagd";
			$color="gold";
			break;
		case 6:
			$rankName="admin";
			$color="red";
			break;
        case 7:
			$rankName="moneybaga";
			$color="gold";
			break;
		default:
			$rankName="guest";
			$color="gray";
			break;
		}
		
	return $rankName;
	}	

function rankNameToValue($rankName, &$colorCode = "&7")
	{
	$rankName=strtolower($rankName);
	switch ($rankName)
		{
		case "guest":
			$value=0;
			break;
		case "member":
			$value=1;
            $colorCode = "&9";
			break;
		case "member+":
			$value=2;
            $colorCode = "&9";
			break;
		case "donator-":
			$value=3;
            $colorCode = "&2";
			break;
		case "donator":
			$value=4;
            $colorCode = "&2";
			break;
        case "moneybagd":
			$value=5;
            $colorCode = "&6";
			break;
		case "admin":
			$value=6;
            $colorCode = "&4";
			break;
        case "moneybaga":
			$value=7;
            $colorCode = "&6";
			break;
		default:
			$value=0;
			break;
		}
	return $value;
	}
	
//returns a string with a representation of how long the time was relative to the current time
function timePassed($time)
	{
	$daysAgo=floor((time()-$time)/86400);
	if ($daysAgo>1)
		return "$daysAgo days ago";
	if ($daysAgo==1)
		return "1 day ago";
		
	$hoursAgo=floor((time()-$time)/3600);
	if ($hoursAgo>1)
		return "$hoursAgo hours ago";
	if ($hoursAgo==1)
		return "1 hour ago";
		
	$minutesAgo=floor((time()-$time)/60);
	if ($minutesAgo>1)
		return "$minutesAgo minutes ago";
	if ($minutesAgo==1)
		return "1 minute ago";
		
	$secondsAgo=time()-$time;
	if ($secondsAgo>1)
		return "$secondsAgo seconds ago";
	if ($secondsAgo==1)
		return "1 second ago";
	return "0 seconds ago";
	}*/

//returns a string with the amount of days/hours/minutes till a given unix time	
	public static function timeTillDate($date)
	{
		if (is_string($date))
			$date=strtotime($date);
		if (!is_numeric($date))
			return "";
		
		$secondsDifference = $date - time();
		$days = floor($secondsDifference / 86400);
		$hours = floor($secondsDifference % 86400 / 3600);
		$minutes = floor($secondsDifference % 86400 % 3600 / 60);
		
		$differenceString = "";
		if ($days == 1)
			$differenceString .= "1 day, ";
		elseif ($days > 1)
			$differenceString .= "$days days, ";
		
		if ($hours == 1)
			$differenceString .= "1 hour and ";
		elseif ($hours > 1 || ($days > 0 && $hours == 0))
			$differenceString .= "$hours hours and ";
		
		if ($minutes == 1)
			$differenceString .= "1 minute";
		else
			$differenceString .= "$minutes minutes";
		
		return $differenceString;
		}
/*
//removes all minecraft console color codes from a string
function removeColorCodes($string)
	{
	return preg_replace("~§([0-9]|[A-F])~i","",$string);
	}

//tags all console color codes into the right html colors
function convertRawColorCodes($string)
{
	$codes = array(
		"§0",
		"§1",
		"§2",
		"§3",
		"§4",
		"§5",
		"§6",
		"§7",
		"§8",
		"§9",
		"§A",
		"§B",
		"§C",
		"§D",
		"§E",
		"§F",
		"§R",
		"\n"
	);
	$replacements = array(
		"</span><span style='color:#000000'>",
		"</span><span style='color:#0000BE'>",
		"</span><span style='color:#00BE00'>",
		"</span><span style='color:#00BEBE'>",
		"</span><span style='color:#BE0000'>",
		"</span><span style='color:#BE00BE'>",
		"</span><span style='color:#D9A334'>",
		"</span><span style='color:#BEBEBE'>",
		"</span><span style='color:#3F3F3F'>",
		"</span><span style='color:#3F3FFE'>",
		"</span><span style='color:#3FFE3F'>",
		"</span><span style='color:#3FFEFE'>",
		"</span><span style='color:#FE3F3F'>",
		"</span><span style='color:#FE3FFE'>",
		"</span><span style='color:#FEFE3F'>",
		"</span><span style='color:#FFFFFF'>",
		"</span><span style='color:#FFFFFF'>",
		"<br />"
	);
	return "<span style='color:#FFFFFF'>".str_ireplace($codes,$replacements,$string)."</span>";
}

//converts &-color codes to native color codes (\xA7)
function convertChatCodes($string)
	{
	$colorCodes=array(
		"&0",
		"&1",
		"&2",
		"&3",
		"&4",
		"&5",
		"&6",
		"&7",
		"&8",
		"&9",
		"&A",
		"&B",
		"&C",
		"&D",
		"&E",
		"&F",
		"&K",
		"&L",
		"&M",
		"&N",
		"&O",
		"&R");
	$nativeCodes=array(
		"\xA70",
		"\xA71",
		"\xA72",
		"\xA73",
		"\xA74",
		"\xA75",
		"\xA76",
		"\xA77",
		"\xA78",
		"\xA79",
		"\xA7A",
		"\xA7B",
		"\xA7C",
		"\xA7D",
		"\xA7E",
		"\xA7F",
		"\xA7K",
		"\xA7L",
		"\xA7M",
		"\xA7N",
		"\xA7O",
		"\xA7R");
	return str_ireplace($colorCodes,$nativeCodes,$string);
	}

//validates a minecraft-standard name
function validateName($name,&$errors=array())
	{
	if (strlen($name)<2)
		$errors[]="The username is too short, it must have 2 or more characters.";
	if (strlen($name)>16)
		$errors[]="The username is too long, it can't have more than 16 characters.";
	if (!preg_match("~^[A-z0-9_]+$~",$name) or $name!=mysql_real_escape_string($name))
		$errors[]="The username contains invalid characters, only alphanumeric characters (A-z,0-9) and the underscore (_) are permitted.";
	if (preg_match("~^_+$~",$name))
		$errors[]="You can't only have underscores in your name =S";
	if (!$errors)
		return true;
	else
		return false;
	}
    
//returns username corresponding to the UUID or false if the UUID does not exist
function UUIDToName($uuid)
{
    //deformat uuid if it's formatted
    $uuid = str_replace("-", "", $uuid);
    
    $result = @file_get_contents("https://sessionserver.mojang.com/session/minecraft/profile/$uuid", false, stream_context_create(array(
        "http" => array(
            "timeout" => 5
        )
    )));
    
    if ($result)
    {
        $result = json_decode($result, true);
        return $result["name"];
    }
    else
        return false;    
}

//returns uuid corresponding to the username, or false if user does not exist or when the mojang api is down
//option to return capital-corrected name
function NameToUUID($name, &$capitalizedName = "")
{
    $result = @file_get_contents("https://api.mojang.com/profiles/page/1", false, stream_context_create(array(
        "http" => array(
            "header"  => "Content-type: application/json\r\n",
            "method"  => "POST",
            "content" => "{\"name\":\"$name\", \"agent\":\"minecraft\"}",
            "timeout" => "5"
        )
    )));
        
    if ($result)
    {
        $decoded = json_decode($result, true);
        if ($decoded["size"] != 0)
        {
            $capitalizedName = $decoded["profiles"][0]["name"];
            $uuid = $decoded["profiles"][0]["id"];
            
            //format the uuid like minecraft does (will not intefere with UUIDToName
            $uid = "";
            $uid .= substr($uuid, 0, 8) . "-";
            $uid .= substr($uuid, 8, 4) . "-";
            $uid .= substr($uuid, 12, 4) . "-";
            $uid .= substr($uuid, 16, 4) . "-";
            $uid .= substr($uuid, 20);
            return $uid;
        }
        else
            return false;
    }
    else
        return false;
}*/
}
?>