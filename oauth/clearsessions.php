<?php
/**
 * @file
 * Clears PHP sessions and redirects to the connect page.
 */
 
/* Load and clear sessions */
session_start();
session_destroy();
session_unset();
$_REQUEST['kgoto'] ? $ref = $_REQUEST['kgoto'] : $ref = $_SERVER['HTTP_REFERER'];
$ktproot = dirname(dirname(dirname(dirname(__FILE__))));
$ktproot=str_replace('/wp-content', "", $ktproot);
file_exists($ktproot.'/wp-load.php') ? require_once($ktproot.'/wp-load.php') : require_once($ktproot.'/wp-config.php');
if($_GET['mode']=='addnew') {
	kish_twit_pro_clear_cookies();
	header('Location: '.KTP_OAUTH_DIR.'/redirect.php?kgoto='.$ref);
}
else {
	if ( is_user_logged_in() ) {
		if(KTP_SAVE_LOGIN) {wp_logout();}		
	}
	kish_twit_pro_clear_cookies();
	/* Redirect to page with the connect to Twitter option. */
	header('Location: '.$ref);
}

