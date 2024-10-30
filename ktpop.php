<?php
$kroot = str_replace("\\", "/", dirname(__FILE__));
include_once($kroot.'/functions.php');
$url=KTP_OAUTH_DIR.'/redirect.php';
//if(!strlen(KTP_TWIT_COOK_SCREEN_NAME)) {
	 //header('Location: ' . $url);
//}
?>
<html>
	<head>
		<title>
			Kish Twitter - Pop
		</title>
		<script type='text/javascript' src='<?php echo get_bloginfo('wpurl'); ?>/wp-includes/js/jquery/jquery.js?ver=1.3.2'></script>
		<?php kish_twit_pro_add_header(); 
		global $ktproauth, $ktpoauthmode;
		if($ktpoauthmode) { ?>
			<script language="JavaScript">
				ktpResizeWindow(500, 370);
			</script>
		<?php } ?>
	</head>
	<body>
		<?php
		global $ktproauth, $ktpoauthmode;
		if($ktpoauthmode) {
			$value .= $_GET['t']." - ";
			if(strlen($_GET['s'])) { $value .= $_GET['s']." - "; }
			$value .= kish_twit_pro_shorturl($_GET['u']);
			strlen($value) ? $value=$value : $value="Update your Status...";
			?>
			<div class="ktp_pop_container">
				<div id="ktp_acc_butts" style="float:left; margin-left:2px; width:98%; display:block">
		             <?php kish_twit_pro_print_account_buttons(false, false); ?>
		             <?php //kish_twit_login_logout();?>
		        </div> 
		        <div style="border:2px;float:left; margin-left:2px; width:98%; display:block">
		        <?php
				kish_twit_pro_print_status_update_box_pop(stripslashes($value));
				?>
		        </div>
		        <div style="clear:both"></div>
				<div id="success"></div>
				
			</div>
			<div style="width:98%;display:compact">
				<?php kish_twit_pro_print_footer(); ?> 
			</div>
		<?php }
		else { ?>
			<div class="ktp_pop_container" style="font-family:Georgia;font-size:12px">
			<h2>Sign in with your Twitter account</h2>
			<p><a href="http://kishpress.com/kish-twit-pro/" target="_blank" title="Kish Twit Pro by Kishore"><img alt="Ktprologo" class="app-icon" src="https://s3.amazonaws.com/twitter_production/client_application_images/21030/ktprologo.png" align="left"></a>    
			<h4 style="font-weight: normal;">The application <strong>Kish Twit</strong> by <strong>Kishore</strong> would like to sign you in using your Twitter account.</h4>
			
			Welcome to Kish-Twit Pro. You can submit pages to twitter using this tool. If you would like to know
			more about this service, please visit <a href="http://eref.in/twitter/" target="_blank" title="Kish Twit by Kishore">Kish Twit</a>. You do not have to provide your login information
			at this site. You will be taken to Twitter to authenticate and re-directed back.
			</p>
			<p><a onClick="ktpResizeWindow(800, 470);" href="<?php echo WP_PLUGIN_URL;?>/kish-twitter/oauth/redirect.php"><img src="<?php echo WP_PLUGIN_URL;?>/kish-twitter/oauth/images/lighter.png" alt="Sign in with Twitter"/></a></p>
			</div>
		<?php }?>
	</body>
</html>
