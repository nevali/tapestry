<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
<?php

	 array_unshift($links, array('rel' => 'stylesheet', 'href' => $skin_iri . 'screen.css', 'media' => 'screen,projection'));

if(!isset($theme))
{
	$theme = 'default';
}
$links[] = array('rel' => 'stylesheet', 'href' => $skin_iri . $theme . '.css', 'media' => 'screen,projection');

$this->links();
$this->scripts();
$this->title();

$name = trim($owner['foaf:name']);
if(!strlen($name))
{
	$name = trim($owner['foaf:firstName'] . ' ' . $owner['foaf:lastName']);
}
if(!strlen($name))
{
	$name = trim($owner['foaf:nick']);
}

$birthday = explode('-', $owner['foaf:birthday'], 3);
if(count($birthday) == 2)
{
	array_unshift($birthday, '');
}
else if(count($birthday) == 1 && strlen($birthday[0]))
{
	$birthday = array($birthday[0], '', '');
}
else if(count($birthday) == 1)
{
	$birthday = null;
}

?>
  </head>
  <body>
	<header>
      <h1><?php e($name); ?></h1>
	  <ul>
		<li class="works">Works at <a href="http://www.bbc.co.uk/">BBC</a></li>
		<li class="lives">Lives in <a href="http://en.wikipedia.org/wiki/Glasgow">Glasgow</a>, <a href="http://en.wikipedia.org/wiki/United_Kingdom">United Kingdom</a></li>
		<li class="relationships">Married to <a href="http://felinedream.craftyblogs.net/">Kirsty McRoberts</a></li>
		<li class="birthday">Born on <a href="http://en.wikipedia.org/wiki/June_29">June 29th</a></li>   
	  </ul>
	  <nav class="global">
	    <p><?php e($name); ?></p>
      </nav>
	</header>
	<div class="outer">
	<aside class="info">
	  <p class="photo">
		<img src="/avatar.jpg" width="200" alt="">
	  </p>
	  <ul class="nav">
	    <li class="newsfeed"><a href="/newsfeed">News feed</a></li>
	    <li class="profile"><a href="/profile">Profile</a></li>
	    <li class="activity active"><a href="/activity">Activity</a></li>
	  </ul>
	  <h2>Apps</h2>
	  <ul class="apps">
	  <?php
foreach($accounts as $acct)
{
	echo '<li>';
	echo '<a href="' . _e($acct['account_uri']) . '"><img src="' . _e($skin_iri . 'services/' . $acct['icon']) . '" alt="" width="16">' . _e($acct['title']) . '</a></li>';
	echo '</li>';

}
?>
      </ul>
	</aside>
