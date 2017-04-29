<?php
require __DIR__.'/../../vendor/autoload.php';
use Services\TweetsFeedService;

class TwitterFetchScript {

  private $tweetsFeedService;

  public function __construct() {
      $this->tweetsFeedService = new TweetsFeedService;
  }

  public function DBCleanUpScript() {
    $this->tweetsFeedService->cleanUpOldTweets();
  }
}

$obj = new TwitterFetchScript();
$obj->DBCleanUpScript();
