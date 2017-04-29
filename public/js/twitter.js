$(function() {
	var $feed = $('#feed'),
		$search = $("#srch-term"),
		lastUid = 0;	

	function addTweet( res ) {
		var htm='';
			
			if(res.length) {
				lastUid = res[res.length - 1]['tweetId'];
				for(var i=0; i<res.length; i++){

					htm=htm +'<div class="tweets"><p><span class="name"><a href='+res[i]["userurl"]+'>'+res[i]["name"]+ " "+'</a></span><span class="uid">'+res[i]["name"]+" "+'</span><span class="time">'+res[i]["time"]+'</span></p><p>'+res[i]["text"]+'</p></div>';
				}
				$feed.html(htm);
			}else {
                            $('.initial').hide();
                            htm=htm +'<div>No New Tweets Found.</div>';
                            $feed.html(htm);
                        }
		
	};
	function makeAJx (url,sendData,lastUid) {
		$.ajax({
			url: url,
			data: {
				hashTag : sendData,
                                lastId : lastUid 
			},
			beforeSend: function( xhr ) {
				//xhr.overrideMimeType( "text/plain; charset=x-user-defined" );
		  	}
		})
		.done(function( res) {
	        addTweet (res);
		})
		.fail(function(err) {
		   $feed.html(err.responceText);
		   console.log(err);
		})
		.always(function() {
		});	
	};
	$('.searchTweets').on('click', function (e) {
		e.preventDefault();
		var searchVal = $search.val();
		console.log("searching");
		if(searchVal) {
			$('.error').addClass('hide').removeClass('show');
			makeAJx(searchUrl,searchVal,lastUid);
		}else {
			$('.error').removeClass('hide').addClass('show');
			$('.initial').hide();
		}
		
	});

	if($feed.length) {
		var counter = 0,
                    uid = lastUid,
                    searchVal = $search.val();
                var i = setInterval(function(){
                var value = $('#srch-term').val();
                    makeAJx(apiUrl,value,lastUid);    
		    if(counter === 10) {
		        clearInterval(i);
		    }
		},6000);
	}	
});