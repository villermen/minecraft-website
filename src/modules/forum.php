<?php
require_once("minecraft/modules/mysql.php");
require_once("minecraft/modules/account.php");

class Thread
{
	public $id;
	public $title;
	public $creatorUUID;
	public $creatorName;
	public $created;
	public $lastPostTime;
	public $lastPostCreatorUUID;
	public $lastPostCreatorName;
	public $sticky;
	public $active;
	public $canView;
	public $canViewString;
	public $canPost;
	public $canPostString;
	
	public function __construct($id, $title, $creatorUUID, $created, $lastPostTime, $lastPostCreatorUUID, $sticky, $active, $canView, $canPost)
	{
		$this->id = $id;
		$this->title = $title;
		$this->creatorUUID = $creatorUUID;
		$this->creatorName = Account::ResolveName($creatorUUID);
		$this->created = $created;
		$this->lastPostTime = $lastPostTime;
		$this->lastPostCreatorUUID = $lastPostCreatorUUID;
		$this->lastPostCreatorName = Account::ResolveName($lastPostCreatorUUID);
		$this->sticky = $sticky;
		$this->active = $active;
		$this->canView = $canView;
		$this->canViewString = ucfirst($canView);
		$this->canPost = $canPost;
		$this->canPostString = ucfirst($canPost);
		
		if ($canView == "member" || $canView == "donator" || $canView == "admin")
			$this->canViewString = $this->canViewString . "s";
			
		if ($canPost == "member" || $canPost == "donator" || $canPost == "admin")
			$this->canPostString = $this->canPostString . "s";
	}
}

class Post
{
	public $id;
	public $creatorUUID;
	public $creatorName;
	public $threadID;
	public $created;
	public $body;
	
	public function __construct($id, $creatorUUID, $threadID, $created, $body)
	{
		$this->id = $id;
		$this->creatorUUID = $creatorUUID;
		$this->creatorName = Account::ResolveName($creatorUUID);
		$this->threadID = $threadID;
		$this->created = $created;
		$this->body = Forum::ParseBody($body);
	}
}

class Forum
{
	private static $bbContext = false;

	public static function ParseBody($body)
	{
		//create bbcontext once
		if (!self::$bbContext)
		{
			self::$bbContext = bbcode_create([
				"b" => [
					"type" => BBCODE_TYPE_NOARG,
					"open_tag" => "<b>",
					"close_tag" => "</b>"
				],
				
				"url" => [
					"type" => BBCODE_TYPE_OPTARG,
					"open_tag" => "<a href='{PARAM}'>",
					"close_tag" => "</a>",
					"default_arg" => "{CONTENT}"
				],
				
				"code" => [
					"type" => BBCODE_TYPE_NOARG,
					"open_tag" => "<code>",
					"close_tag" => "</code>"
				],
				
				"u" => [
					"type" => BBCODE_TYPE_NOARG,
					"open_tag" => "<u>",
					"close_tag" => "</u>"
				],
				
				"ul" => [
					"type" => BBCODE_TYPE_NOARG,
					"open_tag" => "<ul>",
					"close_tag" => "</ul>"
				],
				
				"ol" => [
					"type" => BBCODE_TYPE_NOARG,
					"open_tag" => "<ol>",
					"close_tag" => "</ol>"
				],
				
				"li" => [
					"type" => BBCODE_TYPE_NOARG,
					"open_tag" => "<li>",
					"close_tag" => "</li>",
					"parent" => "ul,ol"
				],
				
				"img" => [
					"type" => BBCODE_TYPE_OPTARG,
					"open_tag" => "<img src='",
					"close_tag" => "'/ alt='{PARAM}' title='{PARAM}' >",
					"default_arg" => ""
				],
				
				"table" => [
					"type" => BBCODE_TYPE_NOARG,
					"open_tag" => "<table>",
					"close_tag" => "</table>"
				],
				
				"tr" => [
					"type" => BBCODE_TYPE_NOARG,
					"open_tag" => "<tr>",
					"close_tag" => "</tr>",
					"parent" => "table"
				],
				
				"th" => [
					"type" => BBCODE_TYPE_NOARG,
					"open_tag" => "<th>",
					"close_tag" => "</th>",
					"parent" => "tr"
				],
				
				"td" => [
					"type" => BBCODE_TYPE_NOARG,
					"open_tag" => "<td>",
					"close_tag" => "</td>",
					"parent" => "tr"
				],
				
				"color" => [
					"type" => BBCODE_TYPE_ARG,
					"open_tag" => "<span style='color: {PARAM};'>",
					"close_tag" => "</span>"
				]
			]);
		}
		
		//parse
		$body = bbcode_parse(self::$bbContext, $body);
		$body = str_replace("\r\n", "<br />", $body);
		return $body;
	}

	public static function GetThread($threadID)
	{
		global $mysqli;
		
		if ($threadID == $mysqli->escape_string($threadID))
		{
			if ($queryResult = $mysqli->query("SELECT forum_threads.id,title,forum_threads.creator,forum_threads.created,forum_posts.created AS lastposttime,forum_posts.creator AS lastpostcreator,sticky,active,canview,canpost FROM forum_threads JOIN forum_posts ON forum_posts.thread=forum_threads.id WHERE forum_threads.id=$threadID AND forum_posts.created=(SELECT MAX(created) FROM forum_posts WHERE thread=$threadID)"))
			{
				$row = $queryResult->fetch_assoc();
				return new Thread(
					$row["id"],
					$row["title"],
					$row["creator"],
					$row["created"],
					$row["lastposttime"],
					$row["lastpostcreator"],
					$row["sticky"] == true,
					$row["active"] == true,
					$row["canview"],
					$row["canpost"]
				);
			}
		}
		
		return false;
	}

	public static function GetThreads($viewerNameOrUUID = false, $viewPrivileges = ["everyone"])
	{
		global $mysqli;
		
		$where = " active=1 AND";
		if ($viewerNameOrUUID && $viewerUUID = Account::ResolveUUID($viewerNameOrUUID))
			$where .= " (forum_threads.creator='{$viewerUUID}' OR";
		else
			$where .= " (FALSE OR";
		
		//check for all view privileges
		$where .= " (FALSE";
		foreach($viewPrivileges as $viewPrivilege)
		{
			$where .= " OR canview='{$viewPrivilege}'";
		}
		$where .= ")) AND forum_posts.created=(SELECT MAX(created) FROM forum_posts WHERE thread=forum_threads.id)";	
		
		if ($queryResult = $mysqli->query("SELECT forum_threads.id,title,forum_threads.creator,forum_threads.created,forum_posts.created AS lastposttime,forum_posts.creator AS lastpostcreator,sticky,canview,canpost FROM forum_threads JOIN forum_posts ON forum_posts.thread=forum_threads.id WHERE{$where} ORDER BY sticky DESC,lastposttime DESC"))
		{
			$result = [];
		
			while ($row = $queryResult->fetch_assoc())
			{
				$result[] = new Thread(
					$row["id"],
					$row["title"],
					$row["creator"],
					$row["created"],
					$row["lastposttime"],
					$row["lastpostcreator"],
					$row["sticky"] == true,
					true,
					$row["canview"],
					$row["canpost"]
				);
			}
			
			return $result;
		}		
		return false;
	}
	
	public static function GetPosts($threadID = 0, $startIndex = 0, $limit = 0, $minTime = 0, &$totalPosts = 0, $inverse = false)
	{
		global $mysqli;
		
		if ($threadID == $mysqli->escape_string($threadID) &&
			$startIndex == $mysqli->escape_string($startIndex) &&
			$limit == $mysqli->escape_string($limit) &&
			$minTime == $mysqli->escape_string($minTime) &&
			ctype_digit((string)$threadID))
		{
			$limitString = "";
			if ($limit)
				$limitString = " LIMIT {$startIndex},{$limit}";

			if ($threadID != 0)
				$threadWhere = "thread={$threadID} AND ";
			else
				$threadWhere = "";

			if ($inverse)
				$order = "DESC";
			else
				$order = "ASC";

			if ($queryResult = $mysqli->query("SELECT id,creator,thread,created,body FROM forum_posts WHERE {$threadWhere}created>={$minTime} ORDER BY created {$order}{$limitString}"))
			{			
				$result = [];
		
				while ($row = $queryResult->fetch_assoc())
				{
					$result[] = new Post(
						$row["id"],
						$row["creator"],
						$row["thread"],
						$row["created"],
						$row["body"]
					);
				}
				
				//get previously calculated total posts in thread
				if ($queryResult = $mysqli->query("SELECT COUNT(*) FROM forum_posts WHERE thread={$threadID} AND created>={$minTime}"))
				{
					$row = $queryResult->fetch_row();
					$totalPosts = $row[0];
				}
					
				return $result;
			}
		}
		
		return false;
	}
	
	public static function GetPost($postID)
	{
		global $mysqli;
		
		if (!($postID = intval($postID)))
			return false;
		
		if (($queryResult = $mysqli->query("SELECT id,creator,thread,created,body FROM forum_posts WHERE id='{$postID}'")) && $mysqli->affected_rows)
		{
			$row = $queryResult->fetch_assoc();
			return new Post(
				$row["id"],
				$row["creator"],
				$row["thread"],
				$row["created"],
				$row["body"]
			);
		}
		
		return false;
	}
	
	public static function DeletePost($postID)
	{
		global $mysqli;
		
		$mysqli->query("DELETE FROM forum_posts WHERE id='{$postID}'");
	}
	
	public static function CreateThread($creatorUUID, $title, $canView, $canPost, $sticky)
	{
		global $mysqli;
		
		$time = time();
		$title = $mysqli->escape_string(htmlspecialchars($title));
		if ($canView != "everyone" && $canView != "member" && $canView != "donator" && $canView != "admin" && $canView != "villermen")
			return false;
		if ($canPost != "member" && $canPost != "donator" && $canPost != "admin" && $canPost != "villermen")
			return false;
		if ($sticky)
			$sticky = 1;
		else
			$sticky = 0;
		
		if ($mysqli->query("INSERT INTO forum_threads (title,creator,created,sticky,active,canview,canpost) VALUES ('{$title}','{$creatorUUID}',{$time},{$sticky},1,'{$canView}','{$canPost}')"))
		{
			//obtain thread id to return
			$queryResult = $mysqli->query("SELECT LAST_INSERT_ID()");
			$row = $queryResult->fetch_row();
			$threadID = $row[0];
			return $threadID;
		}
		else
			return false;		
	}
	
	public static function CreatePost($threadID, $creatorUUID, $body)
	{
		global $mysqli;
		
		$time = time();
		$body = $mysqli->escape_string(htmlspecialchars($body));
		
		if ($mysqli->query("INSERT INTO forum_posts (creator,thread,created,body) VALUES ('{$creatorUUID}',{$threadID},{$time},'{$body}')") && $mysqli->affected_rows)
		{
			return true;
		}
		else
			return false;
	}
	
	//format functions
	public static function FormatThreadSummary($thread)
	{
		return "
			<a class='threadsummary' href='forum&s=thread&id={$thread->id}'>
				<h2>{$thread->title}</h2>
				<div class='threadsummarycreator'>
					By {$thread->creatorName}, " . Misc::TimeAgo($thread->created) . "<br />
					Last post by {$thread->lastPostCreatorName}, " . Misc::TimeAgo($thread->lastPostTime) . "		
				</div>
				<div class='threadsummarylastpost'>
					{$thread->canViewString} can view<br />
					{$thread->canPostString} can post
				</div>
			</a>
		";
	}
	
	public static function FormatPost($post, $link = "forum&s=index")
	{	
		$result = "<div class='post center'>";
		
		//add delete button
		if (Account::$loggedInAccount && (Account::$loggedInAccount->uuid == $post->creatorUUID || Account::$loggedInAccount->villermen))
			$result .= "
				<a class='postdeletebutton' href='{$link}&delpost={$post->id}'>
					delete
				</a>
			";
		
		$result .= 
				$post->creatorName . ", " . Misc::TimeAgo($post->created) . "<br />
			{$post->body}
			</div>
			<div class='spacer'></div>
		";
		
		return $result;
	}
	
	public static function FormatPageLink($threadID, $pageNumber, $currentPage)
	{
		if ($pageNumber)
		{
			$currentClass = "";
			if ($pageNumber == $currentPage)
				$currentClass = " currentpage";
				
			return "<a class='threadpagelink{$currentClass}' href='forum&s=thread&id={$threadID}&tp={$pageNumber}'>{$pageNumber}</a>";
		}
		
		return "";
	}
}
?>