<?php
//session_start();
global $ktprequser;
$root = dirname(dirname(dirname(dirname(__FILE__))));
file_exists($root.'/wp-load.php') ? require_once($root.'/wp-load.php') : require_once($root.'/wp-config.php');
require_once('oauth/config.php');
define('KTP_WP_URL', $url, true); 
//define('KTP_WP_URL', 'http://kish.in',true);
define('KTP_LOADER_1', WP_PLUGIN_URL."/kish-twitter/img/ajax-loader.gif",true);
define('KTP_XML_CACHE', WP_PLUGIN_DIR. "/kish-twitter/xmlcache",true);
define('KTP_CLS_IMG',WP_PLUGIN_URL."/kish-twitter/img/cls.png",true);
define('KTP_LOADER_2', WP_PLUGIN_URL."/kish-twitter/img/smallloader.gif",true);
define('KTP_LOADER_BLANK', WP_PLUGIN_URL."/kish-twitter/img/blank.png",true);
define('KTP_LOADER_SMALL_BLANK',WP_PLUGIN_URL."/kish-twitter/img/small_blank.png",true);
define('KTP_WP_BlOG_URL', get_bloginfo('wpurl'),true);
define('KTP_ME', 'kishtweet',true);
define('KTP_TWIT_MAX', get_option('ktpro_max_status'),true);
define('KTP_TWIT_UMAX', get_option('kish_twitter_userinfo'),true);
define('KTP_TWIT_UNAME', get_option('ktpro_uname'),true);
define('KTP_TWIT_PW', get_option('ktpro_pword'),true);
define('KTP_TWIT_API_URL', WP_PLUGIN_DIR.'/kish-twitter/twitter_api.php',true);
define('KTP_TWIT_API_URL_2', WP_PLUGIN_DIR.'/kish-twitter/twitter.lib.php',true);
define('KTP_TWIT_AJAX_URL', WP_PLUGIN_URL."/kish-twitter/ktp_ajax.php",true);
define('KTP_OAUTH_DIR', WP_PLUGIN_URL."/kish-twitter/oauth",true);
define('KTP_TWIT_COOK_ID', $_COOKIE["kish_twit_user_id"],true);
define('KTP_TWIT_COOK_SCREEN_NAME', $_COOKIE["kish_twit_screen_name"],true);
define('KTP_TWIT_COOK_TOKEN', $_COOKIE["kish_twit_oauth_token"],true);
define('KTP_TWIT_COOK_SECRET', $_COOKIE["kish_twit_oauth_token_secret"],true);
define('KTP_CONSUMER_KEY', get_option('ktpro_cons_key') ? get_option('ktpro_cons_key') : CONSUMER_KEY ,true);
define('KTP_CONSUMER_SECRET', strlen(get_option('ktpro_cons_secret')) ? get_option('ktpro_cons_secret') : CONSUMER_SECRET ,true);
define('KTP_TWIT_URL', get_option('ktpro_twitter_url'),true);
define('KTP_TWIT_ENABLED', get_option('ktpro_status')=="true" ? true : false,true);
define('KTP_TWIT_NOFOLLOW_ENABLED', get_option('ktpro_relno')=="true" ? true : false, true);
define('KTP_TWIT_CACHE_ENABLED', get_option('ktpro_cache_enabled')=="true" ? true : false,true);
define('KTP_TWIT_PRO_ENABLED', get_option('ktpro_pro_enabled')=="true" ? true : false,true);
define('KTP_TWIT_PRO_CHECK_REL', get_option('ktpro_check_rel_enabled')=="true" ? true : false,true);
define('KTP_PUB_ENABLED', get_option('ktpro_enable_public')=="true" ? true : false,true);
define('KTP_REG_ENABLED', get_option('ktpro_enable_reg_users')=="true" ? true : false,true);
define('KTP_SAVE_LOGIN', true,true);
define('KTP_AD_LINK', get_option('ktpro_enable_ad_link')=="true" ? true : false,true);
define('KTP_CONT_1','ktp_container_1',true); // To add to settings 
define('KTP_CONT_2','ktp_container_2',true); // To add to settings
define('KTP_CONT_SB','ktp_sidebar',true); // To add to settings
define('KTP_THEME',get_usermeta(1, 'admin_color', true),true); // To add to settings
define('KTP_AUTH_LOGIN',get_option('ktpro_twitter_login_enabled')=="true" ? true : false,true);
define('KTP_AUTO_SCROLL',get_option('ktpro_autoscroll_enabled')=="true" ? true : false,true);
define('KTP_AUTO_TWEET',get_option('ktpro_autotweet_enabled')=="true" ? true : false,true);
define('KTP_POP_SEARCH', get_option('ktpro_pop_search_enabled')=="true" ? true : false,true);
define('KTP_AUTO_WP_POST',true ,true); // ADD TO SETTINGS
define('KTP_AUTO_REGISTER',true ,true); // ADD TO SETTINGS
define('KTP_AUTO_LOGIN',true ,true); // ADD TO SETTINGS
//define('KTP_TWITTER_BLOG_ID',2 ,true); // ADD TO SETTINGS
define('KTP_TWITTER_SHOW_PROF_IMG',true ,true); // ADD TO SETTINGS
define('KTP_TWITTER_GET_USER',$ktprequser[0] ,true); // ADD TO SETTINGS
function ktp_getDomain( $inputURL ) {
	$parsed = parse_url($inputURL); 
	$hostname = $parsed['host']; 
	return $hostname; 
}
?>