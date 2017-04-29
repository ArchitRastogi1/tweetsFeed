<?php

namespace Services;
use Constants\TwitterTokens;
use Dao\TweetsFeedDao;
use Exceptions\DBException;
use Exceptions\TwitterFeedException;
use DateTime;
use DateTimeZone;
use TwitterAPIExchange;

class TweetsFeedService {

  private $tweetsFeedDao;

    public function __construct() {
        if(empty($this->tweetsFeedDao)) {
          try{
            $this->tweetsFeedDao = new TweetsFeedDao;
          } catch(DBException $ex) {
              throw new TwitterFeedException($ex);
          }
        }
        return $this->tweetsFeedDao;
    }

    public function processNewTwitterFeed($hashTag) {
      $settings = $this->createTweetAPISettingsArr();
      $lastTweetId = $this->findLastTweetId($hashTag);

      $newTweetsFeed = $this->getNewestTwitterStream($settings,$lastTweetId,$hashTag);
      try {
          foreach($newTweetsFeed as $tweet) {
              $this->storeNewTweetsData($tweet,$hashTag);
          }
      } catch(DBException $ex) {
          throw new TwitterFeedException($ex);
      }
    }

    protected function getNewestTwitterStream($settings,$lastTweetId,$hashTag) {
      $url = TwitterTokens::twitterFeedUrl;
      $requestMethod = 'GET';
      $lastIdQuery = '';
      if(!empty($lastTweetId)) {
          $lastIdQuery = '&maxId='.$lastTweetId;
      }
      $getfield = '?q='.$hashTag.'&result_type=recent'.$lastIdQuery;

      // Request for fetching tweets
      $twitter = new TwitterAPIExchange($settings);
      $tweetsData = $twitter->setGetfield($getfield)
                   ->buildOauth($url, $requestMethod)
                   ->performRequest();

      $tweetsRecordArr = json_decode($tweetsData,true);
      if(isset($tweetsRecordArr['errors'])) {
        return array();  
      } 
      return $tweetsRecordArr['statuses'];
    }

    protected function findLastTweetId($hashTag) {
      try {
        $lastTweetId =  $this->tweetsFeedDao->findLastTweetId($hashTag);
        if(empty($lastTweetId)) {
          return 0;
        }
        return $lastTweetId;
      } catch (DBException $ex) {
          throw new TwitterFeedException($ex,$ex->getMessage(),$ex->getCode());
      }
    }

    protected function createTweetAPISettingsArr() {
      $settings = array(
          'oauth_access_token' => TwitterTokens::oauth_access_token ,
          'oauth_access_token_secret' => TwitterTokens::oauth_access_token_secret,
          'consumer_key' => TwitterTokens::consumer_key,
          'consumer_secret' => TwitterTokens::consumer_secret
      );
      return $settings;
    }
  
    protected function storeNewTweetsData($tweet,$hashTag) {
        $tweet['time'] = $this->formatDate($tweet['created_at']);
        return $this->tweetsFeedDao->storeNewTweetsData($tweet,$hashTag);
    }
    
    protected function formatDate($createdDate) {
        $date = new DateTime($createdDate);
        $date->setTimezone(new DateTimeZone('Asia/Kolkata'));
        return $date->format('Y:m:d:h:i:s');
    }
    
    public function fetchNewTweetsFromDB($hashTag,$lastId) {
        if($hashTag[0] != '#') {
            $hashTag = '#'.$hashTag;
        }
        try {
            $tweetsData = $this->tweetsFeedDao->fetchNewTweetsFromDB($lastId,$hashTag);
        } catch(DBException $ex) {
            throw new TwitterFeedException($ex);
        }
        if(count($tweetsData) > 0) {
            return $tweetsData;
        } else {
            return array();
        }
    }
    
    public function fetchNewTweetsFromTwitter($hashTag) {
        if($hashTag[0] != '#') {
            $hashTag = '#'.$hashTag;
        }
        try {
            $tweetsData = $this->processNewTwitterFeed($hashTag);
        } catch(DBException $ex) {
            throw new TwitterFeedException($ex);
        }
        if(count($tweetsData) > 0) {
            return $tweetsData;
        } else {
            return array();
        }
    }
    
    public function cleanUpOldTweets() {
        try {
            $date = date('Y-m-d H:i:s', strtotime('-1 hour'));
            return $this->tweetsFeedDao->cleanUpOldTweets($date);
        } catch(DBException $ex) {
            throw new TwitterFeedException($ex);
        }
    }
}

