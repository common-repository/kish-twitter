<?php
$ktproroot = str_replace("\\", "/", dirname(__FILE__));
include_once($ktproroot.'/kish-twit-pro-config.php');
function kish_twit_pro_print_welcome() { 
	if(KTP_REG_ENABLED) { // Only Registered Users
		$msg = "You can use this tool to manage your twitter account. If you are a registered user, you can login here and use this tool. ";
		if(KTP_SAVE_LOGIN) {
			$msg.="You can also save multiple accounts and access your accounts with a click on a button. ";
		}
		$msg.="<a href=\"".KTP_WP_URL."//wp-login.php?redirect_to=".KTP_TWIT_URL."\"> Login..</a>";
	}
	if(!KTP_PUB_ENABLED && !KTP_REG_ENABLED) {
		$msg = "Hi Welcome to my Twitter page. Here you can find information on my Twitter account. You can follow me..";
	}
	if(KTP_PUB_ENABLED && KTP_REG_ENABLED) {
		$msg .= "You can also use this tool without an account. You will be taken to the twitter login page and redirected back to this site..";
	}
	if(!KTP_PUB_ENABLED && KTP_REG_ENABLED) {
		$msg = "You can use this tool to manage your twitter account. Currently this page is available only to registered users. So please <a href=\"".KTP_WP_URL."//wp-login.php?redirect_to=".KTP_TWIT_URL."\"> login </a> to use this account";
	}
	if(KTP_PUB_ENABLED && !KTP_REG_ENABLED) {
		$msg .= "You can login using your twitter login. You do not have to submit your login credentials here. You will be redirected to Twitter and authenticated and then redirected to this page again. Kish Twit is currently a beta version so just bear with me for some time if you get any errors.";
	}
	
	echo $msg;
}
function kish_twit_pro_print_msg_accountbox() {
	echo "Get This Twitter Plugin For Wordpress";
}

function kish_twit_pro_getDomain($url)
{
    if(filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED) === FALSE)
    {
        return false;
    }
    /*** get the url parts ***/
    $parts = parse_url($url);
    /*** return the host domain ***/
    return $parts['scheme'].'://'.$parts['host'];
}
?>
