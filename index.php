<?php

require './init.php';

if (isset($_GET['historyId'])) {
	$historyId = intval($_GET['historyId']);
	$oldData = $History->get($historyId);
	$data = $oldData['data'];
}
if (isset($_GET['u'])) {
	$data['url'] = $_GET['u'];
	$data['name'] = $_GET['t'];
	$data['description'] = $_GET['d'];
}

$versionUrl = 'http://jeck.ru/download/version.php?product=1';
if (!file_exists('./data/version.dat') || (filemtime('./data/version.dat') + 60*60*3) < time()) {
	$lastVersion = file_get_contents($versionUrl);
	file_put_contents('./data/version.dat', $lastVersion);
} else {
	$lastVersion = file_get_contents('./data/version.dat');
}
/*
$feedUrl = 'http://ru.bmsubmitter.com/rss/?section=blog';
if (!file_exists('./data/feed.dat') || (filemtime('./data/feed.dat') + 60*60*3) < time()) {
	file_put_contents('./data/feed.dat', file_get_contents($feedUrl));
}

$doc = new DOMDocument();
$doc->load('./data/feed.dat');
$rss = array();
foreach ($doc->getElementsByTagName('item') as $node) {
	$item = array ( 
		'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
		'description' => $node->getElementsByTagName('description')->item(0)->nodeValue,
		'link' => $node->getElementsByTagName('link')->item(0)->nodeValue,
		'date' => $node->getElementsByTagName('pubDate')->item(0)->nodeValue
	);
	$rss[] = $item;
}
//*/
$title = '';
include './templates/index.php';
?>