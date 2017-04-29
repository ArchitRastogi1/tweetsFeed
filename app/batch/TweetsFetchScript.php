<?php
require __DIR__.'/../../vendor/autoload.php';
use Services\TweetsFeedService;

class TwitterFetchScript {

  private $tweetsFeedService;

  public function __construct() {
      $this->tweetsFeedService = new TweetsFeedService;
  }

  public function processNewTwitterFeed($options) {
    $this->tweetsFeedService->processNewTwitterFeed($options['hashTag']);
  }
}
$shortopts = "h::";
$longopts  = array(
     "hashTag::"    // Optional value
);
$options = getopt($shortopts, $longopts);
$obj = new TwitterFetchScript();
$obj->processNewTwitterFeed($options);
