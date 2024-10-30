<?php
$kroot = str_replace("\\", "/", dirname(__FILE__));
include_once($kroot.'/functions.php');
$url=KTP_OAUTH_DIR.'/redirect.php';
get_currentuserinfo() ;
global $user_level;
if ($user_level < 8) {
	$redirect=kmp_get_current_page_url ();
	header( "Location:".get_bloginfo('wpurl')."/wp-login.php?redirect_to=".$redirect);
} 
?>
<html>
	<head>
		<title>
			Kish Twitter - Pop
		</title>
		<script type='text/javascript' src='<?php echo get_bloginfo('wpurl'); ?>/wp-includes/js/jquery/jquery.js?ver=1.3.2'></script>
		<?php kish_twit_pro_add_header(); ?>
		<script language="JavaScript">
				ktpResizeWindow(500, 370);
			</script>
	</head>
	<body>
		<?php
		$value .= $_GET['t']." - ";
		if(strlen($_GET['s'])) { $value .= $_GET['s']." - "; }
		$value .= $_GET['u'];
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
			<div id="success"></div>
			
		</div>
		<div style="width:98%;display:compact">
			<?php kish_twit_pro_print_footer(); ?>
		</div>
	</body>
</html>
