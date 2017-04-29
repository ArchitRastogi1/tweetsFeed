<?php

use Services\TweetsFeedService;
use Exceptions\TwitterFeedException;
use Validators\GetTweetsValidator;

$app->get('/gets/tweets', function() use ($app) {
    $response = $app->response();
    $hashTag = $app->request->get('hashTag');
    $lastId = $app->request->get('lastId');    
    
    $validator = new GetTweetsValidator();
    $validatorResp = $validator->validateTweetGetData($hashTag);
    if($validatorResp) {
        $response->status(500);
        $response->end();
    }
    
    try {
        $tweetsFeedService = new TweetsFeedService;        
        $tweetsData = $tweetsFeedService->fetchNewTweetsFromDB($hashTag,$lastId);
    } catch(TwitterFeedException $ex) {
        $app->render('error.html.twig',array('error' => $ex->getMessage()));
        $response->status(500);
        $response->end();
    }
    
    $response['Content-Type'] = 'application/json';
    $response->status(200);
    $response->body(json_encode($tweetsData));
    
});

$app->get('/gets/searchTags', function() use ($app) {
    $response = $app->response();
    $hashTag = $app->request->get('hashTag');
    
    $validator = new GetTweetsValidator();
    $validatorResp = $validator->validateTweetGetData($hashTag);
    if($validatorResp) {
        $response->status(500);
        $response->end();
    }
    
    try {
        $tweetsFeedService = new TweetsFeedService;        
        $tweetsData = $tweetsFeedService->fetchNewTweetsFromTwitter($hashTag);
    } catch(TwitterFeedException $ex) {
        $app->render('error.html.twig',array('error' => $ex->getMessage()));
        $response->status(500);
        $response->end();
    }
    
    $response['Content-Type'] = 'application/json';
    $response->status(200);
    $response->body(json_encode($tweetsData));
    
});