<?php
/* Start session and load lib */
	session_start();
	require_once('twitteroauth/twitteroauth.php');
	require_once('config.php');
	$_REQUEST['kgoto'] ? $ref = $_REQUEST['kgoto'] : $ref = $_SERVER['HTTP_REFERER'];
	//setcookie("kish_twit_oauth_refurl", $ref, time()+3600 ,"/", $ktp_domain );
	$ktproot = dirname(dirname(dirname(dirname(__FILE__))));
	$ktproot=str_replace('/wp-content', "", $ktproot);
	
	//Geting the Wordpress Settings
	file_exists($ktproot.'/wp-load.php') ? require_once($ktproot.'/wp-load.php') : require_once($ktproot.'/wp-config.php');
	/* Create TwitterOAuth object and get request token */
	$connection = new TwitterOAuth(KTP_CONSUMER_KEY, KTP_CONSUMER_SECRET);
	$connection->format="xml";
	/* Get request token */
	//$callbackurl = WPMU_PLUGIN_DIR.'/kish-twit-pro/oauth/callback.php?kgoto='.$ref;
	$callbackurl = WP_PLUGIN_URL.'/kish-twitter/oauth/callback.php?kgoto='.$ref;
	$request_token = $connection->getRequestToken($callbackurl);	
	/* Save request token to session */
	$_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
	$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
	/* If last connection fails don't display authorization link */
	switch ($connection->http_code) {
	  case 200:
	    /* Build authorize URL */
	    $url = $connection->getAuthorizeURL($token)."&force_login=true";
	    header('Location: ' . $url); 
	    break;
	  default:
	    echo 'Could not connect to Twitter. Refresh the page or try again later.';
	    break;
	}
?>