<?php
  require_once("minecraft/modules/forum.php");
  require_once("minecraft/modules/misc.php");

  $section = "index";

  if (isset($_GET["s"]) &&
    ($_GET["s"] == "thread" ||
    $_GET["s"] == "newthread" ||
    $_GET["s"] == "feed"))
    $section = $_GET["s"];

  //determine view privilege
  $privileges = ["everyone"];
  if (Account::$loggedInAccount)
  {
    $privileges[] = "member";

    if (Account::$loggedInAccount->donator)
      $privileges[] = "donator";

    if (Account::$loggedInAccount->admin)
      $privileges[] = "admin";

    if (Account::$loggedInAccount->villermen)
      $privileges[] = "villermen";
  }

  $content .= "
    <h1>Forum</h1>

    <div class='forumaccountbar'>
      <a href='forum'>Threads</a>
  ";

  if (Account::$loggedInAccount)
    $content .= "
      - <a href='forum&s=newthread'>New thread</a>
    ";

  $content .= "
      <a href='forum&s=feed'><img style='float:right; width:1em' src='img/rss_icon.png' alt='ATOM feed' title='ATOM feed' /></a>
    </div>

    <div class='spacer'></div>
  ";

  //delete post
  if (isset($_GET["delpost"]))
  {
    if (Account::$loggedInAccount)
    {
      if ($post = Forum::GetPost($_GET["delpost"]))
      {
        if ($post->creatorUUID == Account::$loggedInAccount->uuid || Account::$loggedInAccount->villermen)
          Forum::DeletePost($post->id);
      }
    }
  }

  if ($section == "index")
  {
    $uuid = false;
    if (Account::$loggedInAccount)
      $uuid = Account::$loggedInAccount->uuid;

    $threads = Forum::GetThreads($uuid, $privileges);

    if ($threads)
    {
      foreach($threads as $thread)
      {
        //format thread
        $content .= Forum::FormatThreadSummary($thread);
      }
    }
    else
      $content .= "<div class='notice green'>There are currently no threads to view for you.</div>";
  }
  elseif ($section == "thread")
  {
    $threadID = $_GET["id"];
    if (isset($threadID) && is_numeric($threadID) && $thread = Forum::GetThread($threadID))
    {
      if (in_array($thread->canView, $privileges) || (Account::$loggedInAccount && $thread->creatorUUID == Account::$loggedInAccount->uuid))
      {
        $content .= Forum::FormatThreadSummary($thread);

        //determine current page
        $gotoLast = false;
        if (!isset($_GET["tp"]) || !($currentPage = intval($_GET["tp"])) || $currentPage < 1)
        {
          $currentPage = 1; //redirect to last page if unset or not valid
          $gotoLast = true;
        }

        if ($posts = Forum::GetPosts($threadID, ($currentPage - 1) * 10, 10, 0, $totalPosts))
        {
          $lastPage = ceil($totalPosts / 10);

          //redirect to last page
          if ($gotoLast)
            header("Location: forum&s=thread&id={$threadID}&tp={$lastPage}");

          //show pages
          if ($lastPage > 1)
          {
            $customPage1 = $currentPage - 1;
            $customPage2 = $currentPage;
            $customPage3 = $currentPage + 1;

            if ($currentPage == 1)
              $customPage2 = false;

            if ($currentPage <= 2)
              $customPage1 = false;

            if ($currentPage >= $lastPage - 1)
              $customPage3 = false;

            if ($currentPage == $lastPage)
              $customPage2 = false;

            $content .= "<div class='threadpagenavigation center'>";

            $content .= Forum::FormatPageLink($_GET["id"], 1, $currentPage);
            $content .= " .. ";
            $content .= Forum::FormatPageLink($_GET["id"], $customPage1, $currentPage);
            $content .= " ";
            $content .= Forum::FormatPageLink($_GET["id"], $customPage2, $currentPage);
            $content .= " ";
            $content .= Forum::FormatPageLink($_GET["id"], $customPage3, $currentPage);
            $content .= " .. ";
            $content .= Forum::FormatPageLink($_GET["id"], $lastPage, $currentPage);

            $content .= "
              </div>
              <div class='spacer'></div>
            ";
          }

          foreach($posts as $post)
            $content .= Forum::FormatPost($post, "forum&s=thread&id={$thread->id}");
        }
        else
          $content .= "<div class='notice green'>There are no posts to show here.</div>";

        //new post
        if (Account::$loggedInAccount && (in_array($thread->canPost, $privileges) || $thread->creatorUUID == Account::$loggedInAccount->uuid))
        {
          if (isset($_POST["newpost"]))
          {
            if (Forum::CreatePost($thread->id, Account::$loggedInAccount->uuid, $_POST["body"]))
              header("Refresh: 0");
            else
              $content .= "<div class='notice red'>Could not create a new post.</div>";
          }

          $content .= "
            <div class='notice blue'>
              <h3>Create new post</h3>
              BB-tags you can use:<br />
              [b] [i] [u] [code] [table] [tr] [th] [td] [ul] [ol] [li] [color=red] [url=http://awesomesite.com/]my&nbsp;site[/url] [img=Awesome&nbsp;hover&nbsp;text]http://puu.sh/bXo1b/02597b601d.jpg[/img]<br />
              <br />
              <form method='post' action=''>
                <textarea name='body' class='newpostbody blue'></textarea>
                <input type='submit' name='newpost' value='Create post' />
              </form>
            </div>
          ";
        }
      }
      else
        $content .= "<div class='notice red'>You do not have permission to view this thread.</div>";
    }
    else
      $content .= "<div class='notice red'>The given thread ID is not valid or does not exist.</div>";
  }
  elseif ($section == "newthread")
  {
    if (Account::$loggedInAccount)
    {
      if (isset($_POST["newthread"]))
      {
        $sticky = false;
        if (Account::$loggedInAccount->admin && isset($_POST["sticky"]))
          $sticky = true;

        if ($threadID = Forum::CreateThread(Account::$loggedInAccount->uuid, $_POST["title"], $_POST["canview"], $_POST["canpost"], $sticky))
        {
          Forum::CreatePost($threadID, Account::$loggedInAccount->uuid, $_POST["body"]);

          header("Location: forum?s=thread&id={$threadID}");
        }
      }

      $content .= "
        <h2>Create new thread</h2>

        <form method='post' action=''>
          <input class='center' type='text' name='title' maxlength='50' placeholder='Title' /><br />
          <br />

          Viewable by
          <select name='canview'>
            <option value='everyone'>Everyone</option>
            <option value='member' selected='selected'>Members</option>
            <option value='donator'>Donators</option>
            <option value='admin'>Admins</option>
            <option value='villermen'>Villermens</option>
          </select> and higher.<br />
          <br />

          <select name='canpost'>
            <option value='member'>Members</option>
            <option value='donator'>Donators</option>
            <option value='admin'>Admins</option>
            <option value='villermen'>Villermens</option>
          </select> and higher can post.<br />
          <br />
      ";

      if (Account::$loggedInAccount->admin)
        $content .= "
          <label for='stickycheckbox' title='Will not be removed after 30 days of inactivity and appears before non-sticky threads.'>
            <input type='checkbox' id='stickycheckbox' name='sticky' />
            Sticky
          </label>
        ";

      $content .= "
          <h3>First post</h3>
          BB-tags you can use:<br />
          [b] [i] [u] [code] [table] [tr] [th] [td] [ul] [ol] [li] [color=red] [url=http://awesomesite.com/]my&nbsp;site[/url] [img=Awesome&nbsp;hover&nbsp;text]http://puu.sh/bXo1b/02597b601d.jpg[/img]<br />
          <br />

          <textarea name='body' class='newpostbody blue'></textarea>
          <input type='submit' name='newthread' value='Create thread' />
        </form>
      ";
    }
    else
    {
      $content .= "
        <h2>New thread?</h2>
        <div class='center'>
          Nope, not yet.


        </div>
      ";
    }
  }
  elseif ($section == "feed")
  {
    error_reporting(E_ALL);

    require_once("lib/FeedWriter/Item.php");
    require_once("lib/FeedWriter/Feed.php");
    require_once("lib/FeedWriter/ATOM.php");

    $posts = Forum::GetPosts(0, 0, 50, 0, $totalPosts, true);

    $uuid = false;
    if (Account::$loggedInAccount)
      $uuid = Account::$loggedInAccount->uuid;

    $threads = Forum::GetThreads($uuid, $privileges);

    $threadIds = [];
    foreach ($threads as $thread)
    {
      $threadIds[$thread->id] = $thread->title;
    }

    $posts = array_filter($posts, function($post) use ($threadIds) {
      return isset($threadIds[$post->threadID]);
    });

    $feed = new FeedWriter\ATOM();
    $feed->setTitle("villermen.com minecraft forum feed");
    $feed->setLink("https://viller.men/minecraft/forum");
    $feed->setDate(time());
    $feed->setSelfLink("https://viller.men/minecraft/forum?s=feed");

    foreach($posts as $post)
    {
      if (strlen($post->body) > 80)
        $description = substr($post->body, 0, 77) . "...";
      else
        $description = $post->body;

      $threadTitle = $threadIds[$post->threadID];

      $feedItem = $feed->createNewItem();
      $feedItem->setTitle("{$post->creatorName} posted a new message in {$threadTitle}");
      $feedItem->setLink("https://viller.men/minecraft/forum&s=thread&id={$post->threadID}");
      $feedItem->setDate($post->created);
      $feedItem->setAuthor($post->creatorName);
      $feedItem->setDescription($description);
      $feedItem->setContent($post->body);

      $feed->addItem($feedItem);
    }

    $feed->printFeed();
    exit();
  }
?>
