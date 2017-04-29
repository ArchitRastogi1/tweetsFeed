<?php
namespace Dao;
use PDO;
use PDOException;
use Exceptions\DBException;
use Constants\DBConn;

class TweetsFeedDao {

    private $db;

    public function __construct() {
        if(empty($this->db)) {
            try{
                $this->db = new PDO(DBConn::dsn,DBConn::user,DBConn::password);
            } catch(PDOException $ex) {
                throw new DBException($ex);
            }
        }
    }

    public function findLastTweetId($hashTag) {
        try {
          $selectQuery = "select max(tweetId) from TweetsFeed where hashTag = :hashTag";
          $selectPrep = $this->db->prepare($selectQuery);
          $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);                     
          $selectPrep->bindValue(":hashTag",$hashTag,PDO::PARAM_STR);
          $selectPrep->execute();
          $tweetId = $selectPrep->fetch(PDO::FETCH_COLUMN);
          $selectPrep->closeCursor();
          return $tweetId;
        } catch (PDOException $ex){
            throw new DBException($ex);
        }
    }
    
    public function storeNewTweetsData($tweet,$hashTag) {
        try {
            $insertQuery = "insert into TweetsFeed(tweetId,text,time,name,username,userurl,hashTag) values(:tweetId,:text,:time,"
                    . ":name,:username,:userurl,:hashTag)";
            $insertPrep = $this->db->prepare($insertQuery);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);            
            $insertPrep->bindValue(":tweetId",$tweet['id'],PDO::PARAM_STR);
            $insertPrep->bindValue(":text",$tweet['text'],PDO::PARAM_STR);
            $insertPrep->bindValue(":time",$tweet['time'],PDO::PARAM_STR);
            $insertPrep->bindValue(":name",$tweet['user']['name'],PDO::PARAM_STR);
            $insertPrep->bindValue(":username",$tweet['user']['screen_name'],PDO::PARAM_STR);
            $insertPrep->bindValue(":userurl", $tweet['user']['url'],PDO::PARAM_STR);           
            $insertPrep->bindValue(":hashTag",$hashTag,PDO::PARAM_STR);
            $insertPrep->execute();
            $insertPrep->closeCursor();
            return true;
        } catch (PDOException $ex) {
            throw new DBException($ex);
        }
    }
    
    public function fetchNewTweetsFromDB($lastId,$hashTag) {
        try {
            $selectQuery = "select tweetId,text,time,name,username,userurl from TweetsFeed where tweetId > :tweetId and hashTag = :hashTag order by tweetId ";
            $selectPrep = $this->db->prepare($selectQuery);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);                        
            $selectPrep->bindValue(":tweetId", $lastId,PDO::PARAM_STR);
            $selectPrep->bindValue(":hashTag",$hashTag,PDO::PARAM_STR);            
            $selectPrep->execute();
            $tweetsData = $selectPrep->fetchAll(PDO::FETCH_ASSOC);
            $selectPrep->closeCursor();
            return $tweetsData;
        } catch (PDOException $ex) {
            throw new DBException($ex);
        }
    }
    
    public function cleanUpOldTweets($date) {
        try {
           $deleteQuery = "delete from TweetsFeed where time <= :time" ;
           $deletePrep = $this->db->prepare($deleteQuery);
           $deletePrep->bindValue(":time", $date,PDO::PARAM_STR);
           $deletePrep->execute();
           $deletePrep->closeCursor();
           return true;
        } catch (PDOException $ex) {
            throw new DBException($ex);
        }
    }
}
