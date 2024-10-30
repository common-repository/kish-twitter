<?php
/**
 * @file
 * Take the user when they return from Twitter. Get access tokens.
 * Verify credentials and redirect to based on response from Twitter.
 */

/* Start session and load lib */
	session_start();
	require_once('twitteroauth/twitteroauth.php');
	require_once('config.php');
	$ktproot = dirname(dirname(dirname(dirname(__FILE__))));
	$ktproot=str_replace('/wp-content', "", $ktproot);
	//exit;
/* If the oauth_token is old redirect to the connect page. */
	if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
		file_exists($ktproot.'/wp-load.php') ? require_once($ktproot.'/wp-load.php') : require_once($ktproot.'/wp-config.php');
		header('Location:' .WP_PLUGIN_URL.'/kish-twitter/oauth/clearsessions.php');
	}
	// Saving the session as sessions won't be available after calling the wp-config file
	$ktpauthtoken = $_SESSION['oauth_token'];
	$ktpauthsecret = $_SESSION['oauth_token_secret'];
	$ktp_config_file=$ktproot.'/wp-content/plugins/kish-twitter/functions.php';
	//file_exists($ktproot.'/wp-load.php') ? require_once($ktproot.'/wp-load.php') : require_once($ktproot.'/wp-config.php');
	require_once($ktp_config_file);
/* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
	$connection = new TwitterOAuth(KTP_CONSUMER_KEY, KTP_CONSUMER_SECRET, $ktpauthtoken, $ktpauthsecret);
/* Request access tokens from twitter */
	$connection->format="xml";
	$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
	$ktp_domain=str_replace('http://', "",KTP_WP_URL);
	$ktp_domain=str_replace('www', "",$ktp_domain);
	if(substr($ktp_domain,0,1)!=".") {
		$ktp_domain=".".$ktp_domain;
	}
	// saving the login information as cookies
	
	kish_twit_pro_save_cookies($access_token['oauth_token'], $access_token['oauth_token_secret'], $access_token['user_id'], $access_token['screen_name']);
	//$ktp = new TwitterOAuth(KTP_CONSUMER_KEY, KTP_CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
	$options = array('screen_name' => $access_token['screen_name']);
	
	$con = new TwitterOAuth(KTP_CONSUMER_KEY, KTP_CONSUMER_SECRET, $ktpauthtoken, $ktpauthsecret);
	/* If HTTP response is 200 continue otherwise send to connect page to retry */
	if (200 == $connection->http_code) {
		if(KTP_AUTO_LOGIN) {
			if (!is_user_logged_in() ) {
				$twitudetails = kish_twit_pro_get_wp_details_from_twitterid($access_token['screen_name']);
				if(strlen($twitudetails[0]->ktpro_id)) {
					$userdetails=kish_twit_pro_get_wp_userdetail($twitudetails[0]->ktpro_wp_id);
				}
				else {
					$userdetails=kish_twit_pro_get_wp_userdetail($access_token['screen_name']);
				}
				if(strlen($userdetails[0]->ID)) {
					global $using_cookie;
					$using_cookie = true;
					wp_setcookie($userdetails[0]->user_login, $userdetails[0]->user_pass, $using_cookie);
					kish_twit_pro_update_user_meta($userdetails[0]->ID, $access_token['oauth_token'], $access_token['oauth_token_secret'], $access_token['screen_name'], $profimage);
				}
				else {
					if(KTP_AUTO_REGISTER) { 
						kish_twit_pro_create_new_user($access_token['screen_name']);
						$userinfo = kish_twit_pro_get_wp_userdetail($access_token['screen_name']);
						global $using_cookie;
						$using_cookie = true;
						wp_setcookie($access_token['screen_name'], $userinfo[0]->user_pass, $using_cookie);
						kish_twit_pro_update_user_meta($userinfo[0]->ID, $access_token['oauth_token'], $access_token['oauth_token_secret'], $profimage);
						if(KTP_SAVE_LOGIN){
							kish_twit_pro_save_new_twitter_login($access_token['oauth_token'], $access_token['oauth_token_secret'], $access_token['user_id'], $access_token['screen_name'], $profimage);
						}
					}
				}
			}
			else {
				if(KTP_SAVE_LOGIN){
					kish_twit_pro_save_new_twitter_login($access_token['oauth_token'], $access_token['oauth_token_secret'], $access_token['user_id'], $access_token['screen_name']);
				}
			}
		}
		$goto = $_REQUEST['kgoto'];
		header('Location: '.$goto);
	  	
	} else {
	  /* Save HTTP status for error dialog on connnect page.*/
		$goto = $_REQUEST['kgoto'];
	  header('Location: ' .WP_PLUGIN_URL.'/kish-twitter/oauth/clearsessions.php?kgoto='.$goto);
	}
?>