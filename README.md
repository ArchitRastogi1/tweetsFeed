About the application -

1 - Initially if no tag is searched. Interface will show message "Enter a #tag".
2 - Once user search a hashtag (data for this tag will be searched using twitter dev apis).
    (It may take some time for the first time).
3 - After that every one minute data will be refreshed.
4 - If no data found for a perticular tag it will show no twwets found.

5 - There is a cron job for inserting data in db for a perticular hashtag.
6 - There is a clean up job which will clean data .


Technologies used - PHP,Slim, JQuery, html, css, ajax, json 

Project Architecture - 

1 - Project is developed in slim microframework of php.
2 - There are 2 main sections (app and public)

3 - App holds important files like services/controllers etc
    Public holds css/js and other files.

4 - Flow is like - 
    -> Front Controller -> Controller -> Validator -> Service -> Dao ->
    other folders - views, public/js, public/css

DB - 

1 - TweetsFeed table with following columns - id,tweetId,text,hashtag,username,name etc
2 - Indexing on hashtag column.

Features - 
1 - Using PDO for db interactions to protect application from sql attacks.
2 - Custom Exception handler (DBException and TwitterFeedException) classes to handle exceptions.
3 - Proper error handling.
4 - Cron job for clean up of database.



