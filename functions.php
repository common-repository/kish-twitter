<?php
$ktproroot = str_replace("\\", "/", dirname(__FILE__));
include_once($ktproroot.'/kish-twit-pro-config.php');
include_once($ktproroot.'/ktpro_core.php');
global $ktproauth, $ktpoauthmode, $ktproselectedaccount,$ktpgetuser, $listdata;
$ktpgetuser=$_GET['ktp'];
if(empty($ktproselectedaccount)) {
	ktpoath();
}
function kish_twit_pro_add_header() {
	echo "<script type=\"text/javascript\" src=\"" . WP_PLUGIN_URL . "/kish-twitter/kish-twit-pro-js.php\"></script>\n";
	echo "<script type=\"text/javascript\" src=\"" . WP_PLUGIN_URL ."/kish-twitter/kish-ajax.js\"></script>\n";
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . get_bloginfo('wpurl') . "/wp-content/plugins/kish-twitter/kish-twit-pro-style.php\" media=\"screen\"/>\n";
}
function kish_twit_pro_install() { 
	add_option('ktpro_status', 	"1", "Enable Kish Twit Pro");
	add_option('ktpro_relno', 	"1", "Twitter Rel No Follow");
	add_option('ktpro_debug_mode', 	"0", "Debug Mode");
	add_option('ktpro_enable_public', 	"1", "Enable for public");
	add_option('ktpro_enable_reg_users', 	"1", "Enable for Reg Users");
	add_option('ktpro_enable_save_login', 	"1", "Enable Save Login");
	add_option('ktpro_enable_ad_link', 	"1", "Enable Only Reg Users");
	add_option('ktpro_uname', 	"Enter your Twitter Username", "Twitter User Name");
	add_option('ktpro_pword', 	"Twitter Password", "Twitter Password");
	add_option('ktpro_max_status', 	"10", "Maximum Info Display");
	add_option('kish_twitter_userinfo', 	"10", "Maximum User Info Display");
	add_option('ktpro_cons_key', "", "", "Consumer Key");
	add_option('ktpro_cons_secret', "", "Consumer Secret");
	add_option('ktpro_twitter_url', 	"Enter Your Twitter Page URL", "Twitter URL");
	add_option('ktpro_cache_enabled', 	"false", "Cache Enabled");
	add_option('ktpro_pro_enabled', 	"true", "Kish Twit Pro Enabled");
	add_option('ktpro_theme', 	"classic", "Kish Twit Pro Theme");
	add_option('ktpro_twitter_login_enabled', 	"true", "Twitter Login Enabled");
	add_option('ktpro_check_rel_enabled', 	"false", "Check Rel Enabled");
	add_option('ktpro_autoscroll_enabled', 	"false", "Check Auto scroll");
	add_option('ktpro_autotweet_enabled', 	"false", "Check Auto Tweet");
	add_option('ktpro_pop_search_enabled', 	"false", "Enable Popular Search..");
	kish_twit_pro_create_db();
}
function kish_twit_pro_create_db() {
	global $wpdb;
	$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."ktpro_users` (
	`ktpro_id` INT NOT NULL AUTO_INCREMENT ,
	`ktpro_wp_id` VARCHAR( 100 ) NOT NULL ,
	`ktpro_oauth_token` VARCHAR( 100 ) NOT NULL ,
	`ktpro_oauth_token_secret` VARCHAR( 100 ) NOT NULL ,
	`ktpro_user_id` VARCHAR( 100 ) NOT NULL ,
	`ktpro_screen_name` VARCHAR( 100 ) NOT NULL ,
	`ktpro_last_login` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL ,
	PRIMARY KEY ( `ktpro_id` ) ,
	INDEX ( `ktpro_screen_name` )
	);";
	mysql_query($sql, $wpdb->dbh);
}
function kish_twit_pro_init_method() {
    wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-tabs');
}
function kish_twit_pro_add_admin() {
	$plugin_page=add_management_page('Kish Twit', 'Kish Twit', 8, 'kish-twitter', 'ktp_main_page');
	add_action( 'admin_head-'. $plugin_page, 'kish_twit_pro_add_header' );
}
function kish_twit_pro_add_to_page($content) {
	$ktp_hook='[KTP_PAGE]';
	$ktp_page=kish_twit_pro_print_twitter_page();
	$content=str_replace($ktp_hook, $ktp_page, $content);
	return $content;
}

function kish_twit_pro_settings_panel(){
	$kish_twit_pro_options=array("ktpro_status", "ktpro_relno", "ktpro_enable_public", "ktpro_enable_reg_users", "ktpro_enable_save_login", "ktpro_enable_ad_link", "ktpro_uname", "ktpro_pword",  "ktpro_max_status", "kish_twitter_userinfo", "ktpro_cons_key", "ktpro_cons_secret", "ktpro_twitter_url", "ktpro_cache_enabled", "ktpro_pro_enabled", "ktpro_theme", "ktpro_twitter_login_enabled", "ktpro_check_rel_enabled", "ktpro_autoscroll_enabled", "ktpro_autotweet_enabled");
	kish_twit_pro_print_settings_form();
}
function ktp_debug_panel() {
	$flag=true;
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	?>
	<div style="margin:5px;padding:4px;">
		<?php if(get_option('ktpro_debug_mode')=="true") : ?>
		<?php $flag=false; ?>
		<p>You are in debug mode. You will be able to go to main page only when this is disabled. Just un-check the 
		debug mode and save the option and reload the page</p>
		<?php endif; ?>
		<?php 
		$ktproroot = str_replace("\\", "/", dirname(__FILE__));
		$geoipfile=$ktproroot.'/geoip/GeoLiteCity.dat';
		$testfile1 = $ktproroot.'/cache';
		$testfile2 = $ktproroot.'/xmlcache';
		if(!file_exists($geoipfile)) {
			echo "<p>You do not have the Geo Location Data File in the /kish-multi/geo/ folder. To enable, neary tweeps, you need to download this file - <a target=\"_blank\" href=\"http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz\">Download the GeoIp File</a></p>";
			echo $flag=false;
		}
		if(!is_writable($testfile1)) {
			echo "<p>You have to chmod 775/777 to enable /cache</p>";
			$flag=false;
		}
		if(!is_writable($testfile2)) {
			echo "<p>You have to chmod 775/777 to enable /xmlcache<p>";
			$flag=false;
		}
		/*
		if(strlen(KTP_TWIT_UNAME) && strlen(KTP_TWIT_PW)){
			$ktp_rmsg=array('@kishpress Working on the debug feature','@kishpress Working on the debug feature','@kishpress Working on the debug feature - Need to check if the application is working fine','@kishpress Working on the debug feature - Its the API test..','@kishpress I think things are working fine');
			$r=rand(0,4);
			$rid=rand(1000000, 99999999);
			$data=kish_twit_pro_api()->updateStatus($ktp_rmsg[$r]." #KishPress ".$rid, '', 'json');
			//print_r($data);
			if(strlen($data->in_reply_to_screen_name)) { 
				echo "<p>Your login information is fine..Test message sent <?php echo $ktp_rmsg[$r]; ?></p>";
			}
			else { 
				echo "<p>Your login information is wrong. Either your username or password is not matching</p>";
				$flag=false;
			} 
		}
		else {
			echo "<p>Update your twitter username and password...</p>";
			$flag=false;
		}*/
		if(strlen(KTP_CONSUMER_KEY) && strlen(KTP_CONSUMER_SECRET)) {
			if(!ktp_check_acc_added()){
				echo "<p>Please add your twitter accounts..</p>";
				$flag=false;
			}
			else {
				if(ktpoath()) {
					$s=$ktproauth->get('users/show', array('screen_name' => 'labnol'));
					if(empty($s)) {
						echo "<p>Error Connecting to the application. Please check the consumer key and the secret phrase..</p>";
					}
					else {
						echo "<p>Great, you are ready to start Tweeting...</p>";
						echo "<input type=\"button\" value=\"Reload the Page..\" onclick=\"javascript:location.reload(true);\">";
					}
				}
			}
		}
		if($flag) : ?>
		<p>Things seem to be fine. I don't see any problem.. If you are still finding anything odd , just post the 
		details at the <a href="http://kishpress.com/forum" target="_blank"> support forum</a></p>
		<?php endif; ?>
		
	</div>
	<?php
}
function ktp_init() {
	if(!strlen(KTP_TWIT_UNAME) || !strlen(KTP_TWIT_PW)){
		ktp_main_page_init();
		return false;
	}
	else if(!strlen(KTP_CONSUMER_KEY) || !strlen(KTP_CONSUMER_SECRET)) {
		ktp_main_page_init();
		return false;
	}
	else if(!ktp_check_acc_added()){
		ktp_main_page_init();
		return false;
	}
	else if(get_option('ktpro_debug_mode')=="true") {
		ktp_main_page_init();
		return false;
	}
	else {
		return true;
	}
}
function ktp_main_page_init() {
	?>
	<div style="width:100%;display:block;overflow:hidden"> 
		<div class="metabox-holder">
			<div class="postbox-container" style="width:60%;">
				<div class="meta-box-sortables ui-sortable">
					<div class="postbox ">
						<h3 style="height:20px" class="hndle" style="margin-bottom:4px">Please complete the set up : Get Started<br></h3>
						<div style="clear:both"></div>
						<div style="display:inline-block;width:99%;padding:10px;font-size:12px;height:370px;">
							<?php kish_twit_pro_print_settings_form(); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="metabox-holder" style="margin-top:-40px">
			<div class="postbox-container" style="width:39%;display:inline-block;">
				<div class="postbox">
					<h3 style="height:20px" class="hndle" style="margin-bottom:4px">Getting Started Help<br></h3>
					<div style="clear:both"></div>
					<div id="ktp_debug_info_panel" style="display:inline-block;width:99%;padding:10px;font-size:12px;height:370px;">
						<p>Update your setting..</p>
					</div>
				</div>
			</div>
		</div>
		<div style="clear:both"></div>
		<div id="ktp_footer" class="ktp_footer">
			<a href="http://kishpress.com/forum" target="_blank">KishPress Forum</a> | 
			<a href="http://kishpress.com/affiliates/" target="_blank">Affiliate Program</a> | 
			Powered By <a href="http://www.twitter.com/" target="_blank">Twitter API</a>
			<div style="float:right">
				<a href="http://kishpress.com/" target="_blank"><img src="http://kishpress.com/img/kp_logo_small.png"></img></a>
			</div>
		</div>
	</div>
	<?php
}
function ktp_main_page() {
	global $ktpoauthmode, $ktproselectedaccount;
	if(ktp_init()) {
	?>
	<div style="width:100%;display:block;overflow:hidden"> 
		<div class="metabox-holder">
			<div class="postbox-container" style="width:100%;">
				<div class="meta-box-sortables ui-sortable">
					<div class="postbox ">
						<h3 class="hndle" style="margin-bottom:4px">
							<span id="kmp_myupdates_butt" onmouseover="ktp_show_msg('View Your Updates', 2000);"><input id="ktp_myupdates" class="button-secondary" type="button" name="Submit" value="My Updates &raquo;" onclick = "ktp_do('req=thisuserlatesttweets', '<?php echo KTP_CONT_2; ?>', 'My Latest Updates', 'kmp_myupdates_butt');return false; "></span>
							<span id="kmp_friendstl_butt" onmouseover="ktp_show_msg('View Your Friends timeline', 2000);"><input id="ktp_friendstl" class="button-secondary" type="button" name="Submit" value="Friends Status &raquo;" onclick = "ktp_do('req=ktpfriendstimeline', '<?php echo KTP_CONT_2; ?>', 'Friends Timeline', 'kmp_friendstl_butt');return false; "></span>
							<span id="kmp_myfollowers_butt" onmouseover="ktp_show_msg('View Tweeps following you', 2000);"><input id="ktp_myfollowers" class="button-secondary" type="button" name="Submit" value="My Followers &raquo;" onclick = "ktp_do('req=followmepage', '<?php echo KTP_CONT_1; ?>', 'My Followers', 'kmp_myfollowers_butt');return false; "></span>
							<span id="kmp_myfriends_butt" onmouseover="ktp_show_msg('View Your Tweeps that you are following', 2000);"><input id="ktp_myfriends" class="button-secondary" type="button" name="Submit" value="My Friends &raquo;" onclick = "ktp_do('req=ifollowpage', '<?php echo KTP_CONT_1; ?>', 'My Friends', 'kmp_myfriends_butt');return false; "></span>
							<span id="kmp_mentions_butt" onmouseover="ktp_show_msg('View tweets with your name mentioned', 2000);"><input id="ktp_mentions" class="button-secondary" type="button" name="Submit" value="@Mentions &raquo;" onclick = "ktp_do('req=mentions', '<?php echo KTP_CONT_1; ?>', '@Mentions', 'kmp_mentions_butt');return false; "></span>
						</h3>
						<div style="display:inline-block;width:99%">
						<div id="kish_multi_wp_prog_1" style="width:25px;height:20px;float:left;margin-left:3px"></div>
						<div id="kish_multi_wp_sidebar" style="margin:3px 0px 3px 3px;display:block;padding:5px;padding-left:0px;width:auto;height:25px;">
							<div id="ktp_acc_butts" style="display:inline">
								<div style="float:left"><?php kish_twit_pro_print_account_buttons(); ?></div>
								<div style="float:right">
								<span id="kmp_tools_butt" onmouseover="ktp_show_msg('Tools like trends, bookmarklet', 2000);"><input id="ktp_tools" class="button-secondary" type="button" name="Submit" value="Tools &raquo;" onclick = "ktp_do('req=ktptools', '<?php echo KTP_CONT_SB; ?>', 'Tools', 'kmp_mentions_butt');return false; "></span>
								</div>
							</div>
						</div>
						</div>
						<div id="kmp_global_search_results"></div>
						<div id="kish_multi_wp_resultDiv_1" style="text-align:left;">
							<div style="width:100%;display:block;overflow:hidden">
							<div style="clear:both;"></div>
							<div class="metabox-holder" style="padding:5px;">
									<div class="postbox-container" style="width:35%;display:inline-block;height:420px;">
											<div class="postbox">
												<h3 style="height:20px;" onclick = "kish_select_result_div('<?php echo KTP_CONT_1; ?>'); return false;" class="hndle"><span style="max-width:79%;float:left;overflow:hidden;" id="<?php echo KTP_CONT_1; ?>_header">Direct Messages Received [InBox]</span><span style="float:right;width:auto;max-width:19%;overflow:hidden;" id="<?php echo KTP_CONT_1; ?>_header_tools"></span><br></h3>
												<div style="clear:both"></div>
												<div class="inside" id="<?php echo KTP_CONT_1; ?>" style="margin-top:3px;display:block;height:370px;overflow:auto;padding:3px">
													<?php //kish_multi_wp_pending_comments_homepage (); ?>
												</div>
											</div>
									</div>
								</div>
								<div class="metabox-holder" style="margin-top:-40px">
									<div class="postbox-container" style="width:35%;display:inline-block;height:420px;">
											<div class="postbox">
												<h3 style="height:20px" onclick = "kish_select_result_div('<?php echo KTP_CONT_2; ?>'); return false;" class="hndle"><span style="max-width:79%;float:left;overflow:hidden;" id="<?php echo KTP_CONT_2; ?>_header">Messages for you (@mentions)</span><span style="float:right;width:auto;max-width:19%;overflow:hidden;" id="<?php echo KTP_CONT_2; ?>_header_tools"></span><br></h3>
												<div style="clear:both"></div>
												<div class="inside" id="<?php echo KTP_CONT_2; ?>" style="margin-top:3px;display:block;height:370px;overflow:auto;padding:3px">
													<?php //kish_multi_wp_pending_posts_homepage (); ?>
												</div>
											</div>
									</div>
								</div>
								<div class="metabox-holder" style="margin-top:-40px;display:inline">
									<div class="postbox-container" style="width:28%;display:inline-block;height:420px;">
										<div class="postbox ">
											<h3><div class="inside" id="ktp_update_box" style="margin-top:3px;display:block;padding:3px">
												<?php if(!strlen(KTP_CONSUMER_KEY) || !ktp_check_acc_added() ) { ?>
													<h3>Attention Needed</h3>
												<?php }
												else {
												kish_twit_pro_print_status_update_box_inline();?>
												<span id ="ktp_api_limits_link"><a style="font-size:10px;" href="javascript:kish_twit_pro_process_ajax('req=ktpaccstatus', '<?php echo KTP_CONT_SB; ?>', 'ktp_api_limits_link', '<?php echo KTP_LOADER_1; ?>' );">[API Limits]</a></span>
												<?php } ?>
											</div></h3>	
											<h3><div class="postbox" style="font-size:11px;margin:3px;display:block;padding:5px;width:95%;height:276px">
												<?php if(!ktp_check_acc_added())  {
													echo "<p class=\"ktp_error\">No accounts Added</p>";
												}
												else if(!strlen(KTP_CONSUMER_KEY)) {
													echo "<p class=\"ktp_error\">You have not updated the application information. If you
													have not registered your application, you should register one 
													now and update the settings and start using this plugin.
													</p>";
												}
												else {
												?>
												<div style="font-size:11px;overflow:auto;height:270px;" class="inside" id="<?php echo KTP_CONT_SB; ?>" class="<?php echo KTP_CONT_SB; ?>">
													
												</div>
												<?php } ?>			
											</div></h3>				
										</div>
									</div>				
								</div>
								<div style="clear:both;margin-top:0px;"></div>
								<div class="postbox" style="margin:3px 3px 3px 3px;display:block;padding:5px;width:98%;height:25px;">
									<div class="inside" id="ktp_bottom" style="">
										<div style="float:left;">
											<span  onmouseover="ktp_show_msg('Update your settings', 2000);"id="kmp_setting_butt"><input id="ktp_settings" class="button-secondary" type="button" name="Submit" value="Settings &raquo;" onclick = "ktp_do('req=ktpsettingspage', '<?php echo KTP_CONT_1; ?>', 'Update Your Settings', 'kmp_setting_butt');return false; "></span>
											<?php 
											$ktproroot = str_replace("\\", "/", dirname(__FILE__));
											$geoipfile=$ktproroot.'/geoip/GeoLiteCity.dat';
											if(file_exists($geoipfile)) {
											?>
											<span  onmouseover="ktp_show_msg('View Tweeps Near your location', 2000);"id="kmp_nearby_butt"><input id="ktp_nearby" class="button-secondary" type="button" name="Submit" value="Near By &raquo;" onclick = "ktp_do('req=nearby', '<?php echo KTP_CONT_1; ?>', 'People Near You', 'kmp_nearby_butt');return false; "></span>
											<?php } ?>
											<span  onmouseover="ktp_show_msg('View Retweets done by you', 2000);"id="kmp_rtbyme_butt"><input id="ktp_rtbyme" class="button-secondary" type="button" name="Submit" value="RT By Me &raquo;" onclick = "ktp_do('req=retweetsbyme', '<?php echo KTP_CONT_1; ?>', 'Re-Tweets By Me', 'kmp_rtbyme_butt');return false; "></span>
											<span  onmouseover="ktp_show_msg('View Your Tweets which are re-tweeted by others', 2000);"id="kmp_mytrt_butt"><input id="ktp_nearby" class="button-secondary" type="button" name="Submit" value="My Tweets RT &raquo;" onclick = "ktp_do('req=retweetsofme', '<?php echo KTP_CONT_1; ?>', 'My Tweets Re-Tweeted', 'kmp_mytrt_butt');return false; "></span>
											<span  onmouseover="ktp_show_msg('View re-tweets by people you follow', 2000);"id="kmp_rttome_butt"><input id="ktp_nearby" class="button-secondary" type="button" name="Submit" value="RT to Me &raquo;" onclick = "ktp_do('req=retweetstome', '<?php echo KTP_CONT_1; ?>', 'Re-Tweets To Me', 'kmp_rttome_butt');return false; "></span>
											<span  onmouseover="ktp_show_msg('View Direct messages received', 2000);"id="kmp_dmr_butt"><input id="ktp_dmr" class="button-secondary" type="button" name="Submit" value="DM to Me &raquo;" onclick = "ktp_do('req=dm', '<?php echo KTP_CONT_1; ?>', 'Direct Messages Recieved', 'kmp_dmr_butt');return false; "></span>
											<span  onmouseover="ktp_show_msg('View Direct Messages Sent', 2000);"id="kmp_dms_butt"><input id="ktp_dms" class="button-secondary" type="button" name="Submit" value="DM Sent &raquo;" onclick = "ktp_do('req=dmsent', '<?php echo KTP_CONT_1; ?>', 'DM Sent', 'kmp_dms_butt');return false; "></span>
											<span  onmouseover="ktp_show_msg('View Your Favourites..', 2000);"id="kmp_fav_butt"><input id="ktp_fav" class="button-secondary" type="button" name="Submit" value="Favs &raquo;" onclick = "ktp_do('req=favorites', '<?php echo KTP_CONT_1; ?>', 'My Favorites', 'kmp_fav_butt');return false; "></span>
										</div>
										<div style="float:right">
											<?php kish_twit_pro_print_status_udpate_box(); ?>
										</div>
									</div>		
								</div>	
							</div>
		
						</div>
					</div>
				</div>
				<div id="ktp_footer" class="ktp_footer">
					<a href="http://kishpress.com/forum" target="_blank">KishPress Forum</a> | 
					<a href="http://kishpress.com/affiliates/" target="_blank">Affiliate Program</a> | 
					<a href="http://kishpress.com/kish-multi-pro/" target="_blank">Kish Multi Pro</a> | 
					Powered By <a href="http://www.twitter.com/" target="_blank">Twitter API</a>
					<div style="float:right">
						<a href="http://kishpress.com/" target="_blank"><img src="http://kishpress.com/img/kp_logo_small.png"></img></a>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php 
	}
}
function kish_twit_pro_print_settings_form() {
	?>
	<div id ="ktpro_admin_settings_panel">
		<?php kish_twit_pro_settings_panel_only_settings(); ?>
	</div>
    <?php
}
function kish_twit_pro_settings_panel_headers() { ?>
	<table width="95%" style="margin:5px 2px 2px 2px; font-stretch:extra-expanded; font-weight:bold">
        <tr>
        	<td style="font-size:16px; text-align:center;">
            Kish Twit Pro Settings
            </td>
        </tr>
    </table>
<?php } 
function kish_twit_pro_settings_panel_footer() { ?>
	<table width="95%" style="margin:5px 2px 2px 2px">
        <tr>
        	<td style="font-size:14px; text-align:left">
           &copy;Kish Twit is a Wordpress plugin from <a href="http://www.asokans.com">&copy;Kishore Asokan</a>
            </td>
        </tr>
    </table>
<?php } 
function kish_twit_pro_settings_panel_only_settings() { 
	$flag=true;
	?>
	<table align="center" width="100%" class="ktp_table">
	<tr>
		<td width="60%">Enable Debug Mode</td>
		<td width="40%"><input type="checkbox" id="ktpro_debug_mode" value = "1" <?php if(get_option('ktpro_debug_mode')=="true") { echo " checked "; }?>></td>		
	</tr>
	<tr> 
		<td width="60%">Cache Enabled</td>
		<td width="40%"><input type="checkbox" id="ktpro_cache_enabled" value = "1" <?php if(get_option('ktpro_cache_enabled')=="true") { echo " checked "; }?>></td>	
	</tr>
	<tr> 
		<td width="60%">Enable Autoload on Scroll</td>
		<td width="40%"><input type="checkbox" id="ktpro_autoscroll_enabled" value = "1" <?php if(get_option('ktpro_autoscroll_enabled')=="true") { echo " checked "; }?>></td>	
	</tr>
	<tr> 
		<td width="60%">Enable Popular Search</td>
		<td width="40%"><input type="checkbox" id="ktpro_pop_search_enabled" value = "1" <?php if(get_option('ktpro_pop_search_enabled')=="true") { echo " checked "; }?>></td>	
	</tr>
	<tr>	
		<td width="60%">Enable Autotweet New Post</td>
		<td width="40%"><input type="checkbox" id="ktpro_autotweet_enabled" value = "1" <?php if(get_option('ktpro_autotweet_enabled')=="true") { echo " checked "; }?>></td>	
	</tr>
	<tr> 
	    <td width="60%">Your Application Consumer Key</font></td>
	    <td width="40%"><input id="ktpro_cons_key" type="password" value="<?php echo get_option('ktpro_cons_key'); ?>" ></td>
	</tr>
	<?php if(!get_option('ktpro_cons_key')) : ?>
	<tr class="ktp_error">
	 	<td colspan="2">
	 		<?php $flag=false; echo "Please enter your twitter application consumer key"; ?>
		</td>
	 </tr>
	<tr>
	<?php endif; ?>
	    <td width="20%">Your Application Consumer Secret</td>
	    <td width="80%"><input id="ktpro_cons_secret" type="password" value="<?php echo get_option('ktpro_cons_secret'); ?>" ></td>
  	</tr>
	<?php if(!get_option('ktpro_cons_secret')) : ?>
	<tr class="ktp_error">
	 	<td colspan="2">
	 		<?php $flag=false; echo "Please enter your twitter application consumer secret"; ?>
		</td>
	 </tr>
	 <?php endif; ?>
	<tr> 
	    <tr> 
	    <td width="20%">Maximum Info in Lines</td>
	    <td width="80%"><input id="ktpro_max_status" type="text" value="<?php echo get_option('ktpro_max_status'); ?>"></td>
	</tr>
	<tr>    
		<td width="20%">Maximum User Info</td>
	    <td width="80%"><input id="kish_twitter_userinfo" type="text" value="<?php echo get_option('kish_twitter_userinfo'); ?>"></td>
	</tr>
	 <tr class="odd"> 
		 <td><span id="result-save-settings">Save Your Settings</span></td>
		 <td>
		 	<div id="ktpro-save-settings" align="left">
		        <input class="button-secondary" type="button" value="Update Options &raquo;" onclick="kish_twit_pro_process_ajax_admin('y=ewerer&req=updatesettings&ktpro_relno=' +
				'&ktpro_cons_key=' + kish_twit_pro_getVar('ktpro_cons_key') + 
				'&ktpro_cons_secret=' + kish_twit_pro_getVar('ktpro_cons_secret') + 
                '&ktpro_max_status=' + kish_twit_pro_getVar('ktpro_max_status') + 
                '&kish_twitter_userinfo=' + kish_twit_pro_getVar('kish_twitter_userinfo') + 
				'&ktpro_cache_enabled=' + kish_twit_pro_getVarCheckBox('ktpro_cache_enabled') +
				'&ktpro_debug_mode=' + kish_twit_pro_getVarCheckBox('ktpro_debug_mode') +
				'&ktpro_autoscroll_enabled=' + kish_twit_pro_getVarCheckBox('ktpro_autoscroll_enabled') +
				'&ktpro_pop_search_enabled=' + kish_twit_pro_getVarCheckBox('ktpro_pop_search_enabled') +
				'&ktpro_autotweet_enabled=' + kish_twit_pro_getVarCheckBox('ktpro_autotweet_enabled') +
				'&raj=sidms', 'ktpro_admin_settings_panel', 'ktpro-save-settings', '<?php echo KTP_LOADER_1; ?>');" >
		    </div>
		</td>
	</tr>
	<tr>
	<?php if($flag) : ?>
			<?php if(!ktp_check_acc_added()) : ?>
			<tr>
				<td colspan="2">
					<span id="ktp_add_new_butt"><input id="ktp_add_new" class="button-secondary" type="button" name="Submit" value="No Twitter Accounts Added - Get started &raquo;" onclick = "ktp_add_new_account();">
				</td>
			</tr>	
			<?php endif; ?>
	<?php endif; ?>
	</tr>
</table> <?php
}
function kish_twit_pro_print_settings_report() {
	$ktproroot = str_replace("\\", "/", dirname(__FILE__));
	$geoipfile=$ktproroot.'/geoip/GeoLiteCity.dat';
	$testfile1 = $ktproroot.'/cache';
	$testfile2 = $ktproroot.'/xmlcache';
	if(!file_exists($geoipfile)) {
		echo "<a target=\"_blank\" href=\"http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz\">Download the GeoIp File</a>";
	}
	if(!is_writable($testfile1)) {
		echo "You have to chmod 775/777 to enable /cache";
	}
	if(!is_writable($testfile2)) {
		echo "You have to chmod 775/777 to enable /xmlcache";
	}
	if(get_option('ktpro_status')=="true") {

	}
	else {
		echo "<strong>Your Twitter Plugin is not working.. Please enable Twitter Enabled</strong>";
	}
	if(!strlen(KTP_CONSUMER_KEY) || !strlen(KTP_CONSUMER_SECRET)) {
		kish_twit_pro_print_application_registration();
	}
}
function kish_twit_pro_print_application_registration() {
	?>
		<strong>Registering Your Own Application</strong><br>
		Your callback URL is <?php echo KTP_WP_URL.'/wp-content/plugins/kish-twitter/oauth/callback.php';?>. Check this to <a href="http://www.kisaso.com/technology/how-to-register-your-own-twitter-application/" target="_blank">Register your own Twitter Application</a>. 
	<?php
}
function kish_twit_pro_theme() {
	$ktp_theme=array();
	if(KTP_THEME=='classic') {
		$ktp_theme=array('dark'=>'#1D507D', 'light'=>'#d5e8f9');
	}
	else {
		$ktp_theme=array('dark'=>'#464646', 'light'=>'#E6E6E6');
	}
	return $ktp_theme;
}
function kish_twit_pro_format_date($date){
	//return $date ." | ". date();
	$d = ktp_get_time_difference( $date, $now );
	if($d['days']>0) {
		if($d['days']==1) {
			$dat = "Yesterday";
		}
		else {
			$dat = $d['days']." days back";
		}
	}
	else if($d['hours']>0) {
		$dat = $d['hours']." hours back";
	}
	else  if($d['minutes']>0){
		$dat = $d['minutes']." minutes back";
	}
	else {
		$dat = "Few Seconds ago..";
	}
	//$dat = $d['days']." days ".$d['hours']." hours,  ".$d['minutes'].", minutes";
	return $dat;
	//return substr($date, 0, 10).substr($date,-5);
}
function ktp_get_time_difference( $start, $end ) {
    $uts['start']      =    strtotime( $start );
    $uts['end']        =    strtotime( 'now' );
    if( $uts['start']!==-1 && $uts['end']!==-1 )
    {
        if( $uts['end'] >= $uts['start'] )
        {
            $diff    =    $uts['end'] - $uts['start'];
            if( $days=intval((floor($diff/86400))) )
                $diff = $diff % 86400;
            if( $hours=intval((floor($diff/3600))) )
                $diff = $diff % 3600;
            if( $minutes=intval((floor($diff/60))) )
                $diff = $diff % 60;
            $diff    =    intval( $diff );            
            return( array('days'=>$days, 'hours'=>$hours, 'minutes'=>$minutes, 'seconds'=>$diff) );
        }
        else
        {
            trigger_error( "Ending date/time is earlier than the start date/time", E_USER_WARNING );
        }
    }
    else
    {
        trigger_error( "Invalid date/time data detected", E_USER_WARNING );
    }
    return( false );
}
function kish_twit_pro_search_location (){
	$ktproroot = str_replace("\\", "/", dirname(__FILE__));
	$geoipfile=$ktproroot.'/geoip/GeoLiteCity.dat';
	if(file_exists($geoipfile)) {
		include_once("geoip/geoip.inc");
		include_once("geoip/geoipcity.inc");
		include_once("geoip/geoipregionvars.php");
		$gi = geoip_open("./geoip/GeoLiteCity.dat", GEOIP_STANDARD);
		return geoip_record_by_addr($gi, $_SERVER['REMOTE_ADDR']);
	}
	else {return false; }
}
function kish_twit_pro_search_api($options) {
	$popoptions=array();
	$popoptions=$options;
	global $ktproauth, $ktpoauthmode;
	if(KTP_POP_SEARCH) {
		$popoptions['result_type']="popular";
		if(strpos($options['q'], ":")===false && $options['page']=='') {
			echo "<h3>Popular Search Results</h3>";
			kish_twit_pro_print_adv_search_results($ktproauth->get('search', $options));
			if($options['page']=='') {
				echo "<h3>Latest Search Results</h3>";
			}
		}
	}
	kish_twit_pro_print_adv_search_results($ktproauth->get('search', $options));
}
function kish_twit_pro_search_nearby() {
	global $ktproauth, $ktpoauthmode;
	$geoloc=kish_twit_pro_search_location();
	if($geoloc!==false) {
		$options=array('geocode'=>$geoloc->latitude.",".$geoloc->longitude.",25km");
		echo "<strong>Tweets From ".$geoloc->city.", ".$geoloc->country_name."</strong>\n";
		kish_twit_pro_print_adv_search_results($ktproauth->get('search', $options));
	}
	else {
		echo "<a target=\"_blank\" href=\"http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz\">Download the GeoIp File</a>";
	}
}
function kish_twit_pro_print_adv_search_results($info) {
	//print_r($info);
	$c=0;
	if($info) {
		if(!strlen($info->results[0]->from_user)) {
			echo "<span class=\"ktp_error\">Sorry no results fetched..</span>"; 
			return;
		}
		foreach($info->results as $i): ?>
			<?php $desc = str_replace("href", "rel=\"nofollow\" target = \"_blank\" href", $i->text); ?>
			<?php $desc = kish_twit_pro_add_profile_link(kish_twit_pro_changeToUrl($desc), "sr-", $i->id);  ?>
			 <div class="ktp_timeline_box">
			 	 <p class="ktp_p"><img class="ktp_thumb" src="<?php echo $i->profile_image_url; ?>" />
			 	<?php echo "<strong>".$i->from_user."</strong> - ".$desc; ?> | <?php echo substr($i->created_at,0,16); ?>| <?php //echo $i['source']; ?></p>
	          <?php if(ktpoath()) { ?>
					<div class="ktp_action_panel">
	                 	<div class="ktp_action_panel">
	                    	<div class="ktp_action_panel_links">
	                         	<?php kish_twit_pro_print_retwit_link($i->from_user,strip_tags($desc), $i->id,'myfollow-'); ?>
	                       	</div>	
	                        <div class="ktp_action_panel_links">
	                        	<?php kish_twit_pro_mark_fav_link($i->id); ?>
	                        </div>	
	                        <div class="ktp_action_panel_links">
	                          	<?php kish_twit_pro_print_user_profile_retrive($i->from_user, $i->id, "myfollow-"); ?>
	                       	</div>
                            <div class="ktp_action_panel_links">
								<?php kish_twit_pro_reply_link($i->id, $i->from_user); ?>
							</div>
							<div class="ktp_action_panel_links">
								<?php kish_twit_pro_follow_user_link($i->from_user); ?>
							</div>         
	                    </div>
	                </div>
					<div id="profinfo-<?php echo $i->id; ?>"></div>
					<?php } ?>
	                <div style="clear:both"></div>
	        </div>
	        <div style="clear:both"></div>
			<?php $c++; ?>	
		<?php endforeach; 
		if(strlen($info->next_page)) {
			$querystring=str_replace("?", "", $info->next_page);
			$r=rand(100000, 999999);
			echo "<script type=\"text/javascript\">\n";
			echo "ktp_cont2_req='kishsearch&".$querystring."';\n";
			echo "ktp_cont2_resultdiv='kprsearch-".$r."';\n";
			echo "ktp_cont2_progdiv='kprsearch-".$r."';\n";
			echo "scrollnew2=true;\n";
			echo "ktp_cont1_req='kishsearch&".$querystring."';\n";
			echo "ktp_cont1_resultdiv='kprsearch-".$r."';\n";
			echo "ktp_cont1_progdiv='kprsearch-".$r."';\n";
			echo "scrollnew=true;\n";
			echo "</script>";
			?>
			<div id ="kprsearch-<?php echo $r; ?>"><input class="button-secondary" type="button" value ="Load More &raquo;" onclick="kish_twit_pro_process_ajax_load_new('req=kishsearch&<?php echo $querystring; ?>', 'kprsearch-<?php echo $r; ?>', 'kprsearch-<?php echo $r; ?>', '<?php echo KTP_LOADER_1; ?>'); return false;">
			<?php
		}
	}
	else {echo "<span class=\"ktp_error\">Sorry no results fetched..</span>"; }
}
function kish_twit_pro_remove_element($arr, $val){
	foreach ($arr as $key => $value){
		if ($arr[$key] == $val){
			unset($arr[$key]);
		}
	}
	return $arr = array_values($arr);
}
function kish_twit_pro_print_acc_status() {	
	global $ktproauth, $ktpoauthmode;
	$info = $ktproauth->get('account/rate_limit_status');
	?>
    <p><strong>API Limits</strong></p>
    <ul>
    	<li>Hourly Limit : <?php echo $info->hourly_limit; ?></li>
        <?php $info->remaining_hits <25 ? $style="#CA0000" : $style= "#006600"; ?>
        <li style="color:<?php echo $style; ?>">Remaining Hits : <?php echo $info->remaining_hits; ?></li>
    </ul><?php
	kish_twit_pro_print_api_status($info->hourly_limit, $info->remaining_hits);
}
function kish_twit_pro_print_api_status($hourlylimit, $balance) {
	$used=$hourlylimit-$balance;
	$used=($used/$hourlylimit)*100;
	$balance=($balance/$hourlylimit)*100;
	echo "<img src=\"http://chart.apis.google.com/chart?chs=250x100&chco=0E8E04,CA0000&chd=t:".$balance.",".$used."&cht=p3&chl=Balance|Used\">";
}
function kish_twit_pro_print_daily_trends() {
	global $ktproauth, $ktpoauthmode;
	$info = $ktproauth->get('trends/current');
	//print_r($info);
	foreach($info->trends as $hours){
		echo "<h3>Current Trends </h3><br>";
		echo "<ul style=\"font-size:11px;padding:3px;margin:3px 0px 0px 0px\" >";
		for($c=0; $c<10; $c++) {
			echo kish_twit_pro_print_search_link(str_replace("#", "", $hours[$c]->name))."\n";
		}
		echo "</ul>";
	}
}
function printUserProfileOtherOauth($username='', $divid='') {
	$list = kish_twit_pro_print_list_listbox();
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	if(!$ktpoauthmode) {return;}
	if(!strlen($username)) {
		if(!empty($ktproselectedaccount)) {
			$ktpoauthmode ? $username= $ktproselectedaccount[0]->ktpro_screen_name : $username=KTP_TWIT_UNAME;
		}
		else {
			$ktpoauthmode ? $username= KTP_TWIT_COOK_SCREEN_NAME : $username=KTP_TWIT_UNAME;
		}
	}
	$s=$ktproauth->get('users/show', array('screen_name' => $username));
	if(empty($s)) {echo "Empty";}
	//print_r($s);
	kish_twit_pro_print_profile_hidden_tl($s, $list);
}

function kish_twit_pro_send_new_dm($touser, $message) {
	global $ktproauth, $ktpoauthmode;
	$message=stripslashes($message);
	if(strlen($message)>140) {
		$pattern="@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@";
		preg_match($pattern, $message, $matches);
		if(strlen($matches[1])) {
			$surl=kish_twit_pro_shorturl($matches[1]);
		}
		$shorturlen=strlen($surl);
		$balpos=135-$shorturlen;
		$msgwithouturl=str_replace($matches[1], "", $message);
		$message=substr($msgwithouturl, 0, $balpos);
		$message=$message." - ". $surl;
		$message=str_replace($matches[1], $surl, $message);
	}
	$ktproauth->post('direct_messages/new', array('user' =>$touser, 'text' =>$message));
}
/*
function kish_twit_pro() {
	require_once(KTP_TWIT_API_URL); 
	return new MyTwitter(KTP_TWIT_UNAME, KTP_TWIT_PW);
}
function kish_twit_pro_api() {
	require_once(KTP_TWIT_API_URL_2); 
	return new Twitter(KTP_TWIT_UNAME, KTP_TWIT_PW);
}*/

function kish_twit_pro_shorturl($url) {
	return ktp_is_gd_shorturl($url);
}
// PRINTING LINKS FUCTIONS
function kish_twit_pro_print_search_link($searchterm) {
	$randid=rand(1000,9999);
	echo  "<span id =\"search-".$randid."\" <a href=\"#\" onclick = \"ktp_do('req=kishsearch&q=".$searchterm."', 'kpt_container_2', 'Search Results for ".$searchterm."','search-".$randid."', '".KTP_LOADER_1."');kmp_save_search_link('".$searchterm."', 'kpt_container_2'); return false;\">".$searchterm."</a></span>&nbsp;";
}
function kish_twit_pro_print_user_latest_twits($username, $resultdiv, $progressdiv) {
	echo  "<div id=\"user-latest-tweets-".$username."\"><a href=\"#\" onclick = \"kish_twit_pro_process_ajax('req=thisuserlatesttweets&user=".$username."','".$resultdiv."', 'user-latest-tweets-".$username."','".KTP_LOADER_1."'); return false;\">Latest Tweets</a></div>";
}
function kish_twit_pro_api_status_update_func() {
	return "kish_twit_pro_process_ajax('req=ktpaccstatus' , 'accstatus', 'accstatus', '".KTP_LOADER_1."')";
}

function kish_twit_pro_print_retweet_link($username, $msg, $statusid, $divpre) {
	$msg=str_replace(array("\r\n", "\r", "\n", "\t"),' ',$msg);
	$msg=str_replace(array("'", "\""),"", $msg); 
	$msg=addslashes($msg);
	echo "<span id=\"".$divpre.$statusid."\"><a href=\"#\" onclick = \"kish_twit_pro_process_ajax_msg('req=twitupdatestatuspop&newmsg=RT @".$username." $msg' + '&ok=yes', '".$divpre.$statusid."', '".$divpre.$statusid."','".KTP_LOADER_1."', 'Re-Tweeted..'); return false;\">Re-Tweet</a></span>";
}
function kish_twit_pro_print_retwit_link($username, $msg, $statusid, $divpre) {
	echo "<span id=\"".$divpre.$statusid."\"><a href=\"#\" onclick = \"kish_twit_pro_process_ajax_msg('req=retweet&tweetid=".$statusid."', '".$divpre.$statusid."', '".$divpre.$statusid."','".KTP_LOADER_1."', 'Re-Tweeted..'); return false;\">Re-Tweet</a></span>";
}
// Marking the tweet as favorite
function kish_twit_pro_mark_fav_link($statusid) {
	echo "<span class=\"kish_twitter_links\" id=\"markfav-".$statusid."\"><a href=\"#\" onclick = \"kish_twit_pro_process_ajax_progress('req=markfav&twitid=".$statusid."' + '&divid=".$statusid."' + '&ok=yes', 'markfav-".$statusid."', 'markfav-".$statusid."','".KTP_LOADER_1."'); kish_twit_pro_change_class('kish-owntl-".$statusid."', 'ktp_timeline_box', 'ktp_timeline_box_fav');return false;\">Mark Fav</a></span>";
}
function kish_twit_pro_follow_user_link($user, $statusid='', $divpre='') {
	$r=rand(1000000, 9999999);
	$user=str_replace('@', '', $user);
	echo "<span id=\"follow-".$user."-".$r."\"><a href=\"#\" onclick = \"kish_twit_pro_process_ajax('req=follownewuser&user=".$user."' + '&ok=yes', 'follow-".$user."-".$r."', 'follow-".$user."-".$r."', '".KTP_LOADER_1."'); return false;\">Follow</a></span>";
}
function kish_twit_pro_subscribe_list_link($user, $listid) {
	$r=rand(1000000, 9999999);
	echo "<span id=\"follow-".$user."-".$r."\"><a href=\"#\" onclick = \"kish_twit_pro_process_ajax('req=subscribelist&user=".$user."&listid=".$listid."' + '&ok=yes', 'follow-".$user."-".$r."', 'follow-".$user."-".$r."', '".KTP_LOADER_1."'); return false;\">Follow</a></span>";
}
function kish_twit_pro_edit_list_link($id) {
	$r=rand(1000000, 9999999);
	echo "<span id=\"edit-".$user."-".$r."\"><a href=\"#\" onclick = \"kish_twit_pro_toggle('".$id."'); return false;\">Edit</a></span>";
}
function kish_twit_pro_unfollow_link($user) {
	$r=rand(1000000, 9999999);
	$user=str_replace('@', '', $user);
	echo "<span id=\"unfollow-".$user."-".$r."\"><a href=\"#\" onclick = \"kish_twit_pro_process_ajax('req=unfollowuser&user=".$user."' + '&ok=yes', 'unfollow-".$user."-".$r."', 'unfollow-".$user."-".$r."', '".KTP_LOADER_1."'); return false;\">Un-Follow</span></a></span>";
}
// Link to delete a tweet (only own)
function kish_twit_pro_twit_del_link($tweetid, $statusid, $divpre, $hidecontainer) {
	echo "<span class=\"kish_twitter_links\" id=\"".$divpre.$statusid."\"><a href=\"#\" onclick = \"kish_twit_pro_process_ajax('req=ktpdeltweet&tweetid=".$tweetid."' + '&ok=yes', '".$divpre.$statusid."', '".$divpre.$statusid."','".KTP_LOADER_1."'); kish_twit_pro_kishHideDiv('".$hidecontainer."'); return false;\">Delete</a></span>";
}
function kish_twit_pro_reply_link($tweetid, $touser) {
	echo "<span id=\"kish-reply-".$tweetid."\"><a href=\"#\" onclick = \"kish_twit_pro_process_ajax('req=replyform&twitid=".$tweetid."' +  '&touser=".$touser."' + '&ok=yes', 'profinfo-".$tweetid."', 'kish-reply-".$tweetid."','".KTP_LOADER_1."'); return false;\">Reply</a></span>";
}
// Link to view User Profile kish_twit_pro_process_ajax(dataToSend, resultDiv, linkDiv, img)
function kish_twit_pro_print_user_profile($user, $statusid, $divpre) {
	$user=str_replace('@', '', $user);
	echo "<span id=\"kish-prof-".$statusid."\"><a href=\"#\" onclick = \"kish_twit_pro_select_tweep('".$user."');kish_twit_pro_load_menu_profile('kish-prof-".$statusid."', 'profinfo-".$statusid."', '".KTP_LOADER_1."'); return false;\">Info</a></span>";
}
function kish_twit_pro_print_user_profile_retrive($user, $statusid, $divpre) {
	$user=str_replace('@', '', $user);
	echo "<span id=\"kish-prof-".$statusid."\"><a href=\"#\" onclick = \"kish_twit_pro_select_tweep('".$user."');kish_twit_pro_process_ajax('req=moreinfo&user=".$user."' + '&divid=".$statusid."' + '&ok=yes', 'ktp_sidebar', 'kish-prof-".$statusid."','".KTP_LOADER_1."'); return false;\">Info</a></span>";
}
// Link Marking the tweet as remove favorite
function kish_twit_pro_remove_fav_link($statusid) {
	echo "<span class=\"kish_twitter_links\" id=\"removefav-".$statusid."\"><a href=\"#\" onclick = \"kish_twit_pro_process_ajax_progress('req=removefav&twitid=".$statusid."' + '&divid=".$statusid."' + '&ok=yes', 'removefav-".$statusid."', 'removefav-".$statusid."','".KTP_LOADER_1."'); kish_twit_pro_change_class('kish-owntl-".$statusid."', 'ktp_timeline_box_fav', 'ktp_timeline_box');return false;\">Un Fav</a></span>";
}
// Links for menu and header
function kish_twit_pro_print_refresh_link($req, $menutext, $resultdiv, $divpre, $img) {
	$idname=str_replace(" ", "-", $menutext);
	echo "<div id=\"".$divpre.$req."\"><input class=\"button-secondary\" type=\"button\" value=\"{$menutext}\" onclick = \"kish_twit_pro_process_ajax('req=".$req."', '".$resultdiv."', '".$divpre.$req."','".$img."'); kish_twit_pro_update_header_title('".$resultdiv."_top_data', '".$menutext."', '".$img."'); return false;\"></div>";
}
function kish_twit_pro_retweet_info($tweetid, $resultdiv, $img) {
	echo "<span id = \"rtinfo-".$tweetid."\"><a href=\"#\" onclick = \"ktp_do('req=retweetinfo&tweetid=".$tweetid."', '".$resultdiv."', 'Re-Tweet Information', 'rtinfo-".$tweetid."','".$img."'); return false;\">RT ?</a></span>";
}
// Links for profile
function kish_twit_pro_print_links_in_profile($reqs, $menutext, $resultdiv, $progressdiv, $img) {
	$tempreq='req='.$reqs;
	parse_str($tempreq);
	if($req=='followmepage') {$postext = " \'s Followers";}
	elseif($req=='favorites') {$postext = " \'s Favs";}
	elseif($req=='ifollowpage') {$postext = " \'s Friends";}
	else {$postext = " \'s Updates";}
	$idname=str_replace(" ", "-", $menutext);
	$r=rand(100000, 9999999);
	$div = "kmp-{$user}-{$r}";
	echo "<span id=\"{$div}\"><a href=\"#\" onclick = \"ktp_do('req=".$reqs."', '".$resultdiv."', '".ucwords($user).$postext."','".$div."');return false;\">".$menutext."</a></span>";
}
function kish_twit_pro_print_menu_link($req, $menutext, $resultdiv, $divpre, $img) {
	$idname=str_replace(" ", "-", $menutext);
	echo "<div id=\"".$divpre.$req."\"><a href=\"#\" onclick = \"kish_twit_pro_process_ajax_progress('req=".$req."', '".$resultdiv."', 'ktp_menu_pro','".$img."'); return false;\">".$menutext."</a></div>";
}
function kish_twit_pro_print_refresh_link_paged($page, $req, $menutext, $resultdiv, $divpre, $img) {
	$idname=str_replace(" ", "-", $menutext);
	echo "<span class=\"kish_twitter_links\" id=\"".$divpre."\"><input style=\"width:99%\" class=\"button-secondary\" type=\"button\" value=\"{$menutext}\" onclick = \"kish_twit_pro_process_ajax('req=".$req."&page=".$page."', '".$resultdiv."', '".$divpre."','".$img."'); return false;\"></span>";
}

function kish_twit_pro_follow_kishtweet() {
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	if(!empty($ktproselectedaccount)) {
		$ktpoauthmode ? $user= $ktproselectedaccount[0]->ktpro_screen_name : $user=KTP_TWIT_UNAME;
	}
	else {
		$ktpoauthmode ? $user= KTP_TWIT_COOK_SCREEN_NAME : $user=KTP_TWIT_UNAME;
	}
	if(!strlen($user) || $user== KTP_ME) {return true;}
	$options=array('source_screen_name'=>$user, 'target_screen_name'=>KTP_ME);
	$status = $ktproauth->get('friendships/show', $options);
	if($status['relationship']['source']['following']) { return true; }
	else {return false;}
}
//kish_twit_pro_process_ajax(dataToSend, resultDiv, linkDiv, img)
function kish_twit_pro_print_status_udpate_box() {
	echo "<input onkeypress=\"if(event.keyCode==13) document.getElementById('searchbtn').click()\" type =\"text\" id=\"tttwiter\" style=\"margin-left:0px;font-size:11px;width:180px\" value=\"Enter keywords...\" onfocus=\"if (this.value == 'Enter keywords...') {this.value = '';}\" onblur=\"if (this.value == '') {this.value = 'Enter keywords...'; }\">";
	echo "<input id=\"searchbtn\" class=\"button-secondary\" style=\"margin-left:3px\" type=\"button\" value=\"Ok\" onclick = \"if(kish_twit_pro_getVar('tttwiter')=='' || kish_twit_pro_getVar('tttwiter')=='Enter keywords...') {alert('Please enter your message'); return false; };kmp_save_search_link(kish_twit_pro_getVar('tttwiter') , '".KTP_CONT_1."');ktp_do('req=kishsearch&q=' + kish_twit_pro_getVar('tttwiter') + '&ok=yes', '".KTP_CONT_1."', 'Search Results - ' + kish_twit_pro_getVar('tttwiter')); kish_twit_pro_setVar('tttwiter','Enter keywords...');  return false;\">";
}
function kish_twit_pro_print_status_update_box_pop($value='') { 
	if(!ktpoath()) {echo "Please login.."; return; }
	if($value=='') {$value='Update your Status...';}
		echo "<textarea onkeypress=\"if(event.keyCode==13) document.getElementById('twtbutton').click()\" id=\"topudatebox\" style=\"width:97%;float:left;padding:2px;margin:2px 2px 2px 5px;\"  onfocus=\"if (this.value == 'Update your Status...') {this.value = '';}\" onblur=\"if (this.value == '') {this.value = 'Update your Status...'; }\">".$value."</textarea>";
		echo "<span id=\"kish_twitter_update\"><input id=\"twtbutton\" class=\"button-secondary\" type=\"button\" value=\"Press me or hit the enter key\" onclick = \"if(kish_twit_pro_getVar('topudatebox')=='' || kish_twit_pro_getVar('topudatebox')=='Update your Status...') {alert('Please enter keyword..'); return false; };kish_twit_pro_process_ajax_update_pop('req=twitupdatestatuspop&newmsg=' + kish_twit_pro_getVar('topudatebox') + '&ok=yes', 'success', 'kish_twitter_update','".KTP_LOADER_1."');kish_twit_pro_setVar('topudatebox','Update your Status...'); return false;\"></span>";
}
function kish_twit_pro_print_status_update_box_inline($value='') {
	if(!ktpoath()) {echo "Please login.."; return; }
	if($value=='') {$value='Update your Status...';}
		echo "<textarea onkeypress=\"if(event.keyCode==13) document.getElementById('twtbutton').click()\" id=\"topudatebox\" style=\"width:98%;height:53px;float:left;padding:2px;margin:2px 2px 2px 3px;font-size:11px\"  onfocus=\"if (this.value == 'Update your Status...') {this.value = '';}\" onblur=\"if (this.value == '') {this.value = 'Update your Status...'; }\">".$value."</textarea>";
		echo "<span id=\"kish_twitter_update\"><input id=\"twtbutton\" class=\"button-secondary\" type=\"button\" value=\"Press me or hit the enter key\" onclick = \"if(kish_twit_pro_getVar('topudatebox')=='' || kish_twit_pro_getVar('topudatebox')=='Update your Status...') {alert('Please enter your message..'); return false; };ktp_update_status('req=twitupdatestatuspop&newmsg=' + kish_twit_pro_getVar('topudatebox') + '&ok=yes', '".KTP_CONT_SB."', 'kish_twitter_update','".KTP_LOADER_1."');kish_twit_pro_setVar('topudatebox','Update your Status...'); return false;\"></span>";
}
function kish_twit_pro_print_single_status($status) {
	$image=$status->user->profile_image_url;
	$kish_text = kish_twit_pro_add_profile_link(kish_twit_pro_changeToUrl($status->text), "retweetinfo-", $status->screen_name); ?>
	<div class="ktp_status_tl">
		<p class="ktp_p"><img class="ktp_thumb"  src="<?php echo $image; ?>" />
		<?php echo "<strong>".$status->user->screen_name."</strong> - ".$kish_text; ?> | 
		<?php echo  kish_twit_pro_format_date($status->created_at); ?> | 
		<?php echo $status->source; ?></p>
	</div>
	<?php
}
function kish_twit_pro_print_reply_update_box_pop($inreplyto, $touser) {
	if(!ktpoath()) {echo "Please login.."; return; }
		echo "<textarea style=\"font-size:11px;width:99%\" onkeypress=\"if(event.keyCode==13) document.getElementById('replybutton').click()\" id=\"replybox\" style=\"width:97%;float:left;padding:2px;margin:2px 2px 2px 5px;\"  onfocus=\"if (this.value == 'Enter your reply...') {this.value = '';}\" onblur=\"if (this.value == '') {this.value = 'Enter your reply...'; }\">Enter your reply...</textarea>";
		echo "<span id=\"ktp_reply_".$inreplyto."\"><input id=\"replybutton\" class=\"button-secondary\" type=\"button\" value=\"Reply\" onclick = \"if(kish_twit_pro_getVar('replybox')=='' || kish_twit_pro_getVar('replybox')=='Enter your reply...') {alert('Enter your reply...'); return false; };kish_twit_pro_process_ajax('req=ktptwitupdatestatus&newmsg=@".$touser." - ' + kish_twit_pro_getVar('replybox') + '&inreplyto=".$inreplyto."' + '&ok=yes', 'profinfo-".$inreplyto."', 'ktp_reply_".$inreplyto."','".KTP_LOADER_1."');kish_twit_pro_setVar('replybox','Enter your reply...'); return false;\"></span><span><input class=\"button-secondary\" type=\"button\" value=\"Cancel\" onclick=\"kish_twitter_clear_div('profinfo-".$inreplyto."')\"></span>";
}
// ALL THE API COMMUNICATION FUNCTIONS
// Following a new user

function kish_twit_pro_follow_new_user($userid) {
	global $ktproauth, $ktpoauthmode;
	if($ktpoauthmode) {
		 $ktproauth->post('friendships/create', array('screen_name' =>$userid));
		 kish_twit_pro_print_refresh_link('ifollowpage', 'Followed..', 'kpt_container_2','menu-', KTP_LOADER_1);
	}/*
	else {
		if(current_user_can('edit_users') || $ktpoauthmode) {
			kish_twit_pro_api()->createFriendship(array('screen_name'=>$userid));
			kish_twit_pro_print_refresh_link('ifollowpage', 'Followed..', 'kpt_container_2','menu-', KTP_LOADER_1);
		}
	}*/
}
// Unfollowing an user
function kish_twit_pro_unfollow_new_user($userid) {
	global $ktproauth, $ktpoauthmode;
	if($ktpoauthmode) {
		 $ktproauth->post('friendships/destroy', array('screen_name' =>$userid));
		 kish_twit_pro_print_refresh_link('ifollowpage', 'Un-Followed..', 'kpt_container_2','menu-', KTP_LOADER_1);
	}/*
	else {
		if(current_user_can('edit_users') || $ktpoauthmode) {
			kish_twit_pro_api()->destroyFriendship(array('screen_name'=>$userid));
			kish_twit_pro_print_refresh_link('ifollowpage', 'Un-Followed..', 'kpt_container_2','menu-', KTP_LOADER_1);
		}
	}*/
}
// Deleting a tweet
function kish_twit_pro_del_twit($tweetid) {
	global $ktproauth, $ktpoauthmode;
	if($ktpoauthmode) {
		 $ktproauth->post('statuses/destroy/'.$tweetid);
	}
}
// Retweet a tweet
function kish_twit_pro_retwit_twit($tweetid) {
		global $ktproauth, $ktpoauthmode; 
		$options=array('id'=>$tweetid);
		$ktproauth->post("statuses/retweet/{$tweetid}", $options);
}
function kish_twit_pro_user_timeline($info, $timelineheading, $refreshrequest, $refreshresultdiv, $refreshprogressdiv, $page=1) {
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	$list = kish_twit_pro_print_list_listbox();
	$seluser="";
	empty($ktproselectedaccount) ? $seluser=KTP_TWIT_COOK_SCREEN_NAME : $seluser = $ktproselectedaccount[0]->ktpro_screen_name;
	$counter=1;
	//print_r($info);
	if($info) {
		foreach ($info as $information) { 
			if($refreshrequest=='favorites') {
				
			}
			else {
				if($counter==1 && ($page==1 || $page==0)) {
					//echo "I am in";
					$statusidfirst=$information->id;
					$tempreq='request='.$refreshrequest;
					parse_str($tempreq);
					if($request=='retweetsbyme') {
						echo "<script type=\"text/javascript\">\n";
						echo "ktp_latest_rtbymesinceid=".$statusidfirst.";\n";
						echo "</script>";
					}
					elseif($request=='ktpshowTwitStatusPage') {
						echo "<script type=\"text/javascript\">\n";
						echo "ktp_latest_ktpshowtwitstatuspagesinceid=".$statusidfirst.";\n";
						echo "</script>";
					}
					elseif($request=='ktpfriendstimeline') {
						echo "<script type=\"text/javascript\">\n";
						echo "ktp_latest_ktpfriendstimelinesinceid=".$statusidfirst.";\n";
						echo "</script>";
					}
					elseif($request=='retweetstome') {
						echo "<script type=\"text/javascript\">\n";
						echo "ktp_latest_rttomesinceid=".$statusidfirst.";\n";
						echo "</script>";
					}
					if($request =='thisuserlatesttweets') {
						if(strlen($user) && $user!=$seluser) {
							echo "<script type=\"text/javascript\">\n";
							echo "ktp_latest_thisuserlatest=".$statusidfirst.";\n";
							echo "ktp_otheruserid='".$user."';\n";
							//echo "alert('yes I am in');\n";
							echo "</script>";
						}
					}
					$reqnew .= $refreshrequest."&since_id=".$statusidfirst."&count=".KTP_TWIT_MAX; 
						$resultdivnew= "usertl-new-".$refreshprogressdiv.$statusidfirst;
						$progdivnew = "usertl-new-".$refreshprogressdiv.$statusidfirst;
					?>
					<?php if($request!='dm') :?>
					<div id="<?php echo $resultdivnew; ?>">
						<?php kish_twit_pro_print_reloadmore_link($reqnew, "Check Anything new", $resultdivnew, KTP_LOADER_1); ?>
					</div>
					<?php endif; ?>
				<?php }
			}
			$information->favorited=='' ? $holderclass="ktp_timeline_box" : $holderclass="ktp_timeline_box_fav";
        	$kish_text = kish_twit_pro_add_profile_link(kish_twit_pro_changeToUrl($information->text), "myreplies-", $information->id); ?>
        	<div class="<?php echo $holderclass; ?>" id="kish-owntl-<?php echo $information->id; ?>">
            	<?php
				if($refreshrequest=='dm') {
					$image=$information->sender->profile_image_url;
				}
				else if($refreshrequest=='dmsent') {
					$image=$information->recipient->profile_image_url;
				}
				else if($refreshrequest=='retweetsbyme') {
					$image=$information->retweeted_status->user->profile_image_url;
				}
				else {
					$image=$information->user->profile_image_url;
				}
				?>
				<div class="ktp_status_tl">
					<p class="ktp_p"><img class="ktp_thumb"  src="<?php echo $image; ?>" />
                <?php if($refreshrequest=='dm') {echo "<strong>".$information->sender->screen_name."</strong> -  "; }?>
                <?php if($refreshrequest=='dmsent') {echo "<strong>".$information->recipient->screen_name."</strong> -  "; }?>
				<?php echo "<strong>".$information->user->screen_name."</strong> - ".$kish_text; ?> | <?php echo  kish_twit_pro_format_date($information->created_at); ?> | <?php echo $information->source; ?></p></div>
				<div style="clear:both"></div>
				<?php if(ktpoath()) { ?>
				<?php $tempreq='request='.$refreshrequest;
					$ktpclass='ktp_action_panel';
					parse_str($tempreq);
					//echo $request;
				?>
                <div class="ktp_action_panel">
                	 <div style="width:98%;float:left;height:22px;display:inline-block">
					 	<?php if($refreshrequest=='ktpshowTwitStatusPage') { ?>
						<div class="ktp_action_panel_links">
								<?php kish_twit_pro_twit_del_link($information->id , $information->id, "myreplies-", 'kish-owntl-'.$information->id); ?>
                        </div>
						<?php }
						if($request=='dm' || $request=='dmsent') {	?>
							<div class="ktp_action_panel_links">
								<?php kish_twit_pro_reply_link($information->id, $information->user->screen_name); ?>
							</div>
						<?php }
						else {
							if($request!='ktpshowTwitStatusPage') { ?>
								<div class="ktp_action_panel_links">
									<?php //kish_twit_pro_print_user_profile($information->user->screen_name, $information->id, "myreplies-"); ?>
									<?php kish_twit_pro_print_user_profile_retrive($information->user->screen_name, $information->id, "myfollow-"); ?>
								</div>
						<?php }
						}
						if($request=='ktpfriendstimeline' || $request=='ktppublictimeline' || $request=='thisuserlatesttweets' || $request=='mentions' || $request=='liststl') { ?>
							<div class="ktp_action_panel_links">
								<?php kish_twit_pro_print_retwit_link($information->screen_name, $information->text, $information->id, 'myreplies-'); ?>
							</div>	
                            <div class="ktp_action_panel_links">
								<?php kish_twit_pro_retweet_info($information->id, KTP_CONT_1, KTP_LOADER_1); ?>
							</div>	
							<?php if($information->favorited=='') { ?>
								<div class="ktp_action_panel_links">
									<?php kish_twit_pro_mark_fav_link($information->id); ?>
								</div><?php
							}
							else {
							?>
							<div class="ktp_action_panel_links">
								<?php kish_twit_pro_remove_fav_link($information->id);	?>
							</div>
							<?php } ?>
							<div class="ktp_action_panel_links">
								<?php kish_twit_pro_reply_link($information->id, $information->user->screen_name); ?>
							</div>
							
							<?php }
							if($request=='favorites' || $refreshrequest=='request') { ?>
							<div class="ktp_action_panel_links">
								<?php kish_twit_pro_remove_fav_link($information->id);	?>
							</div>	
							<div class="ktp_action_panel_links">
								<?php kish_twit_pro_reply_link($information->id, $information->user->screen_name); ?>
							</div>
							<?php }
                        ?>
                    </div>
				</div> 
                <?php $statusid = $information->id -1 ; ?>
                <div style="display:none" id="profinfo-<?php echo $information->id; ?>">
                	<?php //kish_twit_pro_print_profile_hidden_tl($information->user, $list); ?>
                </div>
                <?php } ?> 
			</div>
			<div style="clear:both"></div>
            <?php $counter++; ?>
	<?php } ?>
		<?php 
			if($refreshrequest=='favorites') {
				$page++;
				$req .= $refreshrequest."&page=".$page; 
					$resultdiv= "usertl-after-".$refreshprogressdiv.$statusid;
					$progdiv = "usertl-after-".$refreshprogressdiv.$statusid;
					?>
		            <div id="<?php echo $resultdiv; ?>">
						<?php kish_twit_pro_print_reloadmore_link($req, "Load Next ".KTP_TWIT_MAX."", $resultdiv, KTP_LOADER_1); ?>
					</div> <?php 	
			}
			else {
				if($page==2 || $page==0) { 
					$req .= $refreshrequest."&max_id=".$statusid."&count=".KTP_TWIT_MAX; 
					$resultdiv= "usertl-after-".$refreshprogressdiv.$statusid;
					$progdiv = "usertl-after-".$refreshprogressdiv.$statusid;
					if($request=="ktpfriendstimeline") {
						echo "<script type=\"text/javascript\">\n";
						echo "ktp_cont2_req='".$req."';\n";
						echo "ktp_cont2_resultdiv='".$resultdiv."';\n";
						echo "ktp_cont2_progdiv='".$progdiv."';\n";
						echo "scrollnew2=true;\n";
						echo "</script>";
					}
					if($request=="ktpshowTwitStatusPage" || $request=="retweetsbyme" || $request=="retweetstome" || $request=="liststl" || $request=="thisuserlatesttweets") {
						echo "<script type=\"text/javascript\">\n";
						echo "ktp_cont1_req='".$req."';\n";
						echo "ktp_cont1_resultdiv='".$resultdiv."';\n";
						echo "ktp_cont1_progdiv='".$progdiv."';\n";
						echo "scrollnew=true;\n";
						echo "</script>";
					}
					?>
		            <div id="<?php echo $resultdiv; ?>">
						<?php kish_twit_pro_print_reloadmore_link($req, "Load Next ".KTP_TWIT_MAX."", $resultdiv, KTP_LOADER_1); ?>
					</div> <?php 
				}
			}
	}
}
function kish_twit_pro_print_profile_hidden_tl($status, $list) {
	global $ktp_theme_dark, $ktp_theme_light;
	$jdate =  kish_twit_pro_format_date($status->created_at);
	$profimage=$status->profile_image_url; 
    if($status->verified==1) {$accountverified=true;} 
	strlen($status->verified) ? $verfiedstyle="margin-left:10px;color:#009900" : $verfiedstyle="margin-left:10px;color:#CA0000";?>
    <div>
    	<div style="width:100%; display:inline-block;">
    		<div style="width:100%;float:left;display:inline-block;">
    			<p class="ktp_p"><img style="border:2px solid #CACACA;float:right;width:60px;height:60px" src="<?php echo $profimage; ?>">
				<strong style="<?php echo $verifiedstyle; ?>"><?php echo $status->name; ?></strong> : <?php echo $status->description; ?></p>
			</div>
    	</div>
		<div style="font-size:11px;width:98%;padding:3px;margin;3px; border 1px solid <?php echo $ktp_theme_dark; ?>">
			<div class="ktp_profile_items">
				<div class="ktp_profile_items_left">
					Joined :
				</div>
				<div class="ktp_profile_items_right">
					<?php echo $jdate; ?>
				</div>
				
			</div>
			<div class="ktp_profile_items">
				<div class="ktp_profile_items_left">
					Location : 
				</div>
				<div class="ktp_profile_items_right">
					<?php kish_twit_pro_print_search_link($status->location); ?>
				</div>
			</div>
			<div class="ktp_profile_items">
				<div class="ktp_profile_items_left">
					<strong>Account Status :</strong>
				</div>
				<div class="ktp_profile_items_right">
					<?php if($accountverified) {echo "Verified";} else {echo "Not verified";} ?>
				</div>
			</div>
			<div class="ktp_profile_items">
				<div class="ktp_profile_items_left">
					<strong>Total Updates :</strong>
				</div>
				<div class="ktp_profile_items_right">
					<?php kish_twit_pro_print_links_in_profile('thisuserlatesttweets&user='. $status->screen_name, $status->statuses_count, KTP_CONT_1, 'kish_multi_wp_prog_1', KTP_LOADER_1); ?>
				</div>
			</div>
			<div class="ktp_profile_items">
				<div class="ktp_profile_items_left">
					<strong>Following :</strong>
				</div>
				<div class="ktp_profile_items_right">
					<?php kish_twit_pro_print_links_in_profile('ifollowpage&user='. $status->screen_name, $status->friends_count, KTP_CONT_1, 'kish_multi_wp_prog_1', KTP_LOADER_1); ?>
				</div>
			</div>
			<div class="ktp_profile_items">
				<div class="ktp_profile_items_left">
					<strong>Followers :</strong>
				</div>
				<div class="ktp_profile_items_right">
					<?php kish_twit_pro_print_links_in_profile('followmepage&user='. $status->screen_name, $status->followers_count, KTP_CONT_2, 'kish_multi_wp_prog_1', KTP_LOADER_1); ?>
				</div>
			</div>
			<div class="ktp_profile_items">
				<div class="ktp_profile_items_left">
					<strong><?php kish_twit_pro_profile_conversation_link($status->screen_name, KTP_CONT_1, 'kish_multi_wp_prog_1' ); ?></strong>
				</div>
				<div class="ktp_profile_items_right">
					<strong><?php kish_twit_pro_profile_replies_link($status->screen_name, KTP_CONT_1, 'kish_multi_wp_prog_1' ); ?></strong>
				</div>
			</div>
			<div class="ktp_profile_items">
				<div class="ktp_profile_items_left">
					<strong><?php kish_twit_pro_profile_list_link($status->screen_name, KTP_CONT_2); ?></strong>
				</div>
				<div class="ktp_profile_items_right">
					<strong><a href="<?php echo $status->url; ?>">Visit Website</a></strong>
				</div>
			</div>
            <div class="ktp_profile_items">
				<div class="ktp_profile_items_left">
					<strong><?php kish_twit_pro_profile_subscribed_list_link($status->screen_name, KTP_CONT_2); ?></strong>
				</div>
				<div class="ktp_profile_items_right">
					<strong><a href="<?php echo $status->url; ?>">Visit Website</a></strong>
				</div>
			</div>
			<div class="ktp_profile_items">
				<div style="width:99%;float:left">
					<strong><?php echo str_replace("##user##",$status->id , $list); ?></strong>
				</div>
			</div>
			<div class="ktp_profile_items">
				<div style="width:99%;float:left">
					<strong><?php echo kish_twit_pro_check_rel($status->screen_name); ?></strong>
				</div>
			</div>
			<div class="ktp_profile_items">
				<div style="width:99%;float:left">
					<strong><?php kish_twit_pro_profile_your_conversation_link($status->screen_name, KTP_CONT_1, 'kish_multi_wp_prog_1' ); ?></strong>
				</div>
			</div>
		</div>
   <?php //endforeach; ?> 
   </div> <?php
}
function kish_twit_pro_print_bookmarklet() {?>
	<div class="ktp_featured_1">
	<h3>Bookmarklet </h3><br>
	<p style="font-size:11px;padding:3px;margin:3px 0px 0px 0px">Drag-and-drop the following link to your bookmarks bar or right click it and add it to your favorites for a posting shortcut.</p>
	<p style="font-size:11px;padding:3px;margin:3px 0px 0px 0px"><a href="javascript:var d=document,w=window,e=w.getSelection,k=d.getSelection,x=d.selection,s=(e?e():(k)?k():(x?x.createRange().text:0)),f='<?php echo WP_PLUGIN_URL; ?>/kish-twitter/ktpop.php',l=d.location,e=encodeURIComponent,g=f+'?u='+e(l.href)+'&t='+e(d.title)+'&s='+e(s)+'&v=2';function a(){if(!w.open(g,'t','toolbar=0,resizable=0,scrollbars=1,status=1,width=500,height=300')){l.href=g;}}a();void(0);" title="Kish Twit This">Kish Twit This</a></p>
    </div>
    <div class="ktp_featured_1">
    <?php kish_twit_pro_print_daily_trends(); ?>
	</div>
<?php }
function kish_twit_pro_print_reloadmore_link($req, $menutext, $resultdiv, $img) {
	//function kish_twit_pro_process_ajax(dataToSend, resultDiv, linkDiv, img)
	echo "<input class=\"button-secondary\" type=\"button\" value=\"{$menutext}\" onclick = \"kish_twit_pro_process_ajax_load_new('req=".$req."', '".$resultdiv."', '".$resultdiv."', '".$img."'); return false;\">";
}
// function to add profile link
function kish_twit_pro_add_profile_link($text, $divpre, $statusid) {
	$r=rand(100000, 9999999);
	$patterns = "/@(\w+)/is";	
	$tt="<span id=\"searchprof-".$r."\"><a href=\"#\" onclick = \"kish_twit_pro_process_ajax('req=moreinfo&user=$1' + '&divid=".$statusid."' + '&ok=yes', 'ktp_sidebar', 'searchprof-".$r."','".KTP_LOADER_1."'); return false;\">@$1</a></span>";
	$t= preg_replace($patterns , $tt, $text);	
	return kish_twit_pro_add_search_link($t);
}
// Function for adding search link
function kish_twit_pro_add_search_link($text) {
	$r=rand(100000, 9999999);
	$patterns = "/#(\w+)/is";	
	$tt= "<span id=\"searchterm-".$r."\"><a href=\"#\" onclick = \"kish_twit_pro_process_ajax('req=kishsearch&q=$1','".KTP_CONT_1."', 'searchterm-".$r."','".KTP_LOADER_1."'); return false;\">#$1</a></span>";
	return preg_replace($patterns , $tt, $text);	
}
function kish_twit_pro_profile_conversation_link($user, $resultdiv, $progdiv) {
	$q="to:".$user." OR from:".$user."";
	$r=rand(100000, 9999999);
	$div = "ktp-{$user}-{$r}";
	echo "<span id=\"{$div}\"><a href=\"#\" onclick = \"ktp_do('req=kishsearch&q=".$q."','".$resultdiv."', 'Conversations of ".$user."','".$div."'); return false;\">View Conversations</a></span>";
}
function kish_twit_pro_save_query_link($query) {
	echo "<a href=\"#\" onclick=\"ktp_do('req=savednewsearch&query={$query}', resultDiv);return false;\">Save</a>";
}
function kish_twit_pro_profile_list_link($user, $resultdiv) {
	$r=rand(100000, 9999999);
	$div = "list-{$user}-{$r}";
	echo "<span id=\"{$div}\"><a href=\"#\" onclick = \"kish_twit_pro_process_ajax('req=lists&user=".$user."','".$resultdiv."', '".$div."','".KTP_LOADER_1."'); kish_twit_pro_update_header_title('".$resultdiv."_header', 'Lists of ".$user."', '".KTP_LOADER_1."'); return false;\">Lists of {$user}</a></span>";
}
function kish_twit_pro_profile_subscribed_list_link($user, $resultdiv) {
	$r=rand(100000, 9999999);
	$div = "list-{$user}-{$r}";
	echo "<span id=\"{$div}\"><a href=\"#\" onclick = \"kish_twit_pro_process_ajax('req=subscribedlists&user=".$user."','".$resultdiv."', '".$div."','".KTP_LOADER_1."'); kish_twit_pro_update_header_title('".$resultdiv."_header', 'Subscribed By ".$user."', '".KTP_LOADER_1."'); return false;\">Subscribed Lists</a></span>";
}
function kish_twit_pro_profile_list_members_link($user, $listid, $memcount, $resultdiv) {
	$r=rand(100000, 9999999);
	$div = "list-{$user}-{$r}";
	echo "<span id=\"{$div}\"><a href=\"#\" onclick = \"kish_twit_pro_process_ajax('req=listmembers&user=".$user."&listid=".$listid."','".$resultdiv."', '".$div."','".KTP_LOADER_1."'); kish_twit_pro_update_header_title('".$resultdiv."_header', 'Members of ".$listid."', '".KTP_LOADER_1."'); return false;\">{$memcount}</a></span>";
}
function kish_twit_pro_profile_list_subscribers_link($user, $listid, $subscount, $resultdiv) {
	$r=rand(100000, 9999999);
	$div = "list-{$user}-{$r}";
	echo "<span id=\"{$div}\"><a href=\"#\" onclick = \"kish_twit_pro_process_ajax('req=listsubscribers&user=".$user."&listid=".$listid."','".$resultdiv."', '".$div."','".KTP_LOADER_1."'); kish_twit_pro_update_header_title('".$resultdiv."_header', 'Subscribers of ".$listid."', '".KTP_LOADER_1."'); return false;\">{$subscount}</a></span>";
}
function kish_twit_pro_profile_list_tl_link($user, $listid, $text, $resultdiv, $maxid='', $sinceid='') {
	$r=rand(100000, 9999999);
	$div = "list-{$user}-{$r}";
	echo "<span id=\"{$div}\"><a href=\"#\" onclick = \"kish_twit_pro_process_ajax('req=liststl&user=".$user."&listid=".$listid."','".$resultdiv."', '".$div."','".KTP_LOADER_1."'); kish_twit_pro_update_header_title('".$resultdiv."_header', 'Updates of ".$listid."', '".KTP_LOADER_1."'); return false;\">{$text}</a></span>";
}
function kish_twit_pro_profile_your_conversation_link($user, $resultdiv, $progdiv) {
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	if(!empty($ktproselectedaccount)) {
		$ktpoauthmode ? $username= $ktproselectedaccount[0]->ktpro_screen_name : $username=KTP_TWIT_UNAME;
	}
	else {
		$ktpoauthmode ? $username= KTP_TWIT_COOK_SCREEN_NAME : $username=KTP_TWIT_UNAME;
	}
	$q="from:".$username." to:".$user." OR from:".$user." to:".$username;
	//$q="to:".$username." AND from:".$user." ";
	echo "<a href=\"#\" onclick = \"ktp_do('req=kishsearch&q=".$q."','".$resultdiv."', 'Your Conversations with ".$user."', '".$progdiv."','".KTP_LOADER_1."'); kmp_save_search_link('".$q."', '".$resultdiv."');return false;\">Your Conversations with {$user}</a>";
}
function ktp_conversations() {
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	if(!empty($ktproselectedaccount)) {
		$ktpoauthmode ? $username= $ktproselectedaccount[0]->ktpro_screen_name : $username=KTP_TWIT_UNAME;
	}
	else {
		$ktpoauthmode ? $username= KTP_TWIT_COOK_SCREEN_NAME : $username=KTP_TWIT_UNAME;
	}
	$q="from:".$username." OR to:".$username;
	kish_twit_pro_search_api(array('q'=>$q));
}
function kish_twit_pro_profile_replies_link($user, $resultdiv, $progdiv) {
	$r=rand(100000, 9999999);
	$div = "kmp-{$user}-{$r}";
	$q="to:".$user."";
	echo "<span id=\"{$div}\"><a href=\"#\" onclick = \"ktp_do('req=kishsearch&q=".$q."','".$resultdiv."', 'Replies of ".$user."','".$div."'); kish_twit_pro_update_header_title('".$resultdiv."_header', 'Replies of ".$user."', '".KTP_LOADER_1."'); return false;\">View Replies</a></span>";
}
// Direct Messages Recieved
function kish_twit_pro_print_dm_page($user='', $maxid='', $sinceid='', $count='') {
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	if(!$ktpoauthmode && empty($ktproselectedaccount)) { echo "<span class=\"ktp_error\">Sorry, I cannot show you my Direct Messages !!</span>"; return; }
	if(!empty($ktproselectedaccount)) {
		$ktpoauthmode ? $username= $ktproselectedaccount[0]->ktpro_screen_name : $username=KTP_TWIT_UNAME;
	}
	else {
		$ktpoauthmode ? $username= KTP_TWIT_COOK_SCREEN_NAME : $username=KTP_TWIT_UNAME;
	}
	$page=0;
	!strlen($count)?$count=KTP_TWIT_MAX:$count=$count;
	if(strlen($maxid)) {
		$options = array('screen_name' => $user, 'count' => $count, 'max_id' => $maxid);
		$page=2;
	}
	else {
		if(strlen($sinceid)) {
			$options=array('screen_name' => $user, 'count' => $count, 'since_id' => $sinceid);
			$page=1;
		}
		else {
			$options= array('screen_name' => $user, 'count' => $count);
		}
	}
	kish_twit_pro_user_timeline($ktproauth->get('direct_messages', $options), 'Direct Messages Recieved', 'dm', 'kpt_container_2', 'page-dm', $page);
}
// User Timeline
function kish_twit_pro_print_user_tl($user='', $maxid='', $sinceid='', $count='') {
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount, $ktpgetuser;
	if(!strlen($user)) {
		if(!empty($ktproselectedaccount)) {
			$ktpoauthmode ? $user= $ktproselectedaccount[0]->ktpro_screen_name : $user=KTP_TWIT_UNAME;
		}
		else {
			$ktpoauthmode ? $user= KTP_TWIT_COOK_SCREEN_NAME : $user=KTP_TWIT_UNAME;
		}
	}
	$page=0;
	!strlen($count)?$count=KTP_TWIT_MAX:$count=$count;
	if(strlen($maxid)) {
		$options = array('screen_name' => $user, 'count' => $count, 'max_id' => $maxid);
		$page=2;
	}
	else {
		if(strlen($sinceid)) {
			$options=array('screen_name' => $user, 'count' => $count, 'since_id' => $sinceid);
			$page=1;
		}
		else {
			$options= array('screen_name' => $user, 'count' => $count);
		}
	}
	//print_r($options);
	kish_twit_pro_user_timeline($ktproauth->get('statuses/user_timeline', $options), 'My latest Tweets', 'ktpshowTwitStatusPage', KTP_CONT_1, 'utl', $page);
}
// Tweets of people I am following
function kish_twit_pro_print_following_tl($user='', $maxid='', $sinceid='', $count='') {
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	if(!strlen($user)) {
		if(!empty($ktproselectedaccount)) {
			$ktpoauthmode ? $user= $ktproselectedaccount[0]->ktpro_screen_name : $user=KTP_TWIT_UNAME;
		}
		else {
			$ktpoauthmode ? $user= KTP_TWIT_COOK_SCREEN_NAME : $user=KTP_TWIT_UNAME;
		}
	}
	$page=0;
	if(!strlen($user)) {
		$ktpoauthmode ? $user= KTP_TWIT_COOK_SCREEN_NAME : $user=KTP_TWIT_UNAME;
	}
	!strlen($count)?$count=KTP_TWIT_MAX:$count=$count;
	if(strlen($maxid)) {
		$options = array('screen_name' => $user, 'count' => $count, 'max_id' => $maxid);
		$page=2;
	}
	else {
		if(strlen($sinceid)) {
			$options=array('screen_name' => $user, 'count' => $count, 'since_id' => $sinceid);
			$page=1;
		}
		else {
			$options= array('screen_name' => $user, 'count' => $count);
		}
	}
	//print_r($options);
	kish_twit_pro_user_timeline($ktproauth->get('statuses/friends_timeline', $options), 'My Friends Updates', 'ktpfriendstimeline', 'ktp_container_2', 'ftl', $page); 
}
// Retweets done by me / user
function kish_twit_pro_print_retweets_by_me_tl($user='', $maxid='', $sinceid='', $count='') {
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	if(!strlen($user)) {
		if(!empty($ktproselectedaccount)) {
			$ktpoauthmode ? $user= $ktproselectedaccount[0]->ktpro_screen_name : $user=KTP_TWIT_UNAME;
		}
		else {
			$ktpoauthmode ? $user= KTP_TWIT_COOK_SCREEN_NAME : $user=KTP_TWIT_UNAME;
		}
	}
	$page=0;

	!strlen($count)?$count=KTP_TWIT_MAX:$count=$count;
	if(strlen($maxid)) {
		$options = array( 'count' => $count, 'max_id' => $maxid);
		$page=2;
	}
	else {
		if(strlen($sinceid)) {
			$options=array('count' => $count, 'since_id' => $sinceid);
			$page=1;
		}
		else {
			$options= array('count' => $count);
		}
	}
	kish_twit_pro_user_timeline($ktproauth->get('statuses/retweeted_by_me', $options), 'Retweets By Me', 'retweetsbyme', KTP_CONT_1, 'ftl', $page); 
}
// Retweets of my tweets by others
function kish_twit_pro_print_retweets_to_me_tl($user='', $maxid='', $sinceid='', $count='') {
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	if(!strlen($user)) {
		if(!empty($ktproselectedaccount)) {
			$ktpoauthmode ? $user= $ktproselectedaccount[0]->ktpro_screen_name : $user=KTP_TWIT_UNAME;
		}
		else {
			$ktpoauthmode ? $user= KTP_TWIT_COOK_SCREEN_NAME : $user=KTP_TWIT_UNAME;
		}
	}
	$page=0;
	!strlen($count)?$count=KTP_TWIT_MAX:$count=$count;
	if(strlen($maxid)) {
		$options = array('screen_name' => $user, 'count' => $count, 'max_id' => $maxid);
		$page=2;
	}
	else {
		if(strlen($sinceid)) {
			$options=array('screen_name' => $user, 'count' => $count, 'since_id' => $sinceid);
			$page=1;
		}
		else {
			$options= array('screen_name' => $user, 'count' => $count);
		}
	}
	kish_twit_pro_user_timeline($ktproauth->get('statuses/retweeted_to_me', $options), 'Retweets By Me', 'retweetstome', KTP_CONT_1, 'ftl', $page); 
}
// Retweets of my tweets by others
function kish_twit_pro_print_retweets_of_me_tl($user='', $maxid='', $sinceid='', $count='') {
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	if(!strlen($user)) {
		if(!empty($ktproselectedaccount)) {
			$ktpoauthmode ? $user= $ktproselectedaccount[0]->ktpro_screen_name : $user=KTP_TWIT_UNAME;
		}
		else {
			$ktpoauthmode ? $user= KTP_TWIT_COOK_SCREEN_NAME : $user=KTP_TWIT_UNAME;
		}
	}
	$page=0;
	!strlen($count)?$count=KTP_TWIT_MAX:$count=$count;
	if(strlen($maxid)) {
		$options = array('screen_name' => $user, 'count' => $count, 'max_id' => $maxid);
		$page=2;
	}
	else {
		if(strlen($sinceid)) {
			$options=array('screen_name' => $user, 'count' => $count, 'since_id' => $sinceid);
			$page=1;
		}
		else {
			$options= array('screen_name' => $user, 'count' => $count);
		}
	}
	kish_twit_pro_user_timeline($ktproauth->get('statuses/retweets_of_me', $options), 'Retweets By Me', 'retweetstome', KTP_CONT_1, 'ftl', $page); 
}
// Lists
function kish_twit_pro_print_saved_lists($user='', $nextcursor='') {
	strlen($user)?$mylist=false : $mylist=true;
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	if(!strlen($user)) {
		if(!empty($ktproselectedaccount)) {
			$ktpoauthmode ? $user= $ktproselectedaccount[0]->ktpro_screen_name : $user=KTP_TWIT_UNAME;
			$userid= $ktproselectedaccount[0]->ktpro_user_id;
		}
		else {
			$ktpoauthmode ? $user= KTP_TWIT_COOK_SCREEN_NAME : $user=KTP_TWIT_UNAME;
		}
	}
	if(strlen($nextcursor)) {
			$options=array('cursor' => $nextcursor);
			$page=2;
	}
	else {
		$options=array();
	}
	$data = $ktproauth->get($user.'/lists', $options);
	//foreach($data['lists'] as $s) {
		//kish_twit_pro_is_subscribed($user, $s['name'], $userid);
	//}
	kish_twit_pro_print_lists($data, ' My Lists', 'lists', KTP_CONT_1, $nextcursor, $mylist); 
}
function kish_twit_pro_print_saved_searches($user='') {
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	$data = $ktproauth->get('saved_searches');
	//print_r($data);
	if($data) {
		echo "<h4>Saved Searches</h4>";
		echo "<ul>";
		foreach($data as $d) : 
			$r=rand(100000, 9999999);
			$q=$d->query;
			$divid="savedsearch-".$r;
			 echo "<li style=\"font-size:11px;margin-left:5px;\" id=\"{$divid}\"><a href=\"#\" onclick = \"ktp_do('req=kishsearch&q={$q}','".KTP_CONT_1."', 'Search Results for {$q}',  'savedsearch-".$r."'); return false;\">{$q}</a> | <a href=\"#\" onclick=\"ktp_del_saved_search('".$divid."', '".$q=$d->id."'); return false;\">x</a></li>";
		endforeach;
		echo "</ul>";
	}
	else {
		echo "No Saved Searches..";
	}
	//kish_twit_pro_print_lists($data, ' My Lists', 'lists', KTP_CONT_1, $nextcursor, $mylist); 
}
function ktp_save_new_search($query, $user="") {
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	if(strlen($query)) {
			$options=array('query' => $query);
	}
	else {
		echo "No Query found to be saved";
	}
	$data = $ktproauth->post('saved_searches/create', $options);
	if(strlen($data->query)) {
		echo "<span style=\"font-size:11px\">Saved..</span>";
	}
	else {
		echo "<span class=\"ktp_error\">Error Saving..</span>";
	}
}
function ktp_save_del_saved_search($id, $user="") {
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	$options=array();
	$data = $ktproauth->post("saved_searches/destroy/{$id}", $options);
}
function kish_twit_pro_print_lists($status, $timelineheading, $refreshrequest, $refreshresultdiv, $nextcursor='', $mylist=true) {
	//print_r($status);
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	$page=0;
	if(!empty($ktproselectedaccount)) {
		$ktpoauthmode ? $username= $ktproselectedaccount[0]->ktpro_screen_name : $username=KTP_TWIT_UNAME;
		$userid= $ktproselectedaccount[0]->ktpro_user_id;
	}
	else {
		$ktpoauthmode ? $username= KTP_TWIT_COOK_SCREEN_NAME : $username=KTP_TWIT_UNAME;
		$userid=KTP_TWIT_COOK_ID;
	}
	$counter=1;
	
	foreach($status->lists as $s): ?>
		<?php if(!strlen($s->user->screen_name)) {
			echo "<span class=\"ktp_error\">Sorry no lists found..</span>"; 
			return;
		}?>
    	<?php $profimage=$s->user->profile_image_url; ?>
    	<div class="ktp_timeline_box" id="sublist_<?php echo $s->user->screen_name."_".$s->slug; ?>">
    		<p class="ktp_p"><img class="ktp_thumb" src="<?php echo $profimage; ?>">
			<strong><?php echo "{$counter} . "; ?><?php kish_twit_pro_profile_list_tl_link($s->user->screen_name, $s->slug, ucwords($s->name), KTP_CONT_1); ?></strong> - <?php echo " {$s->name} - {$s->uri}";?> : <?php echo $s->description; ?></p>
            <div class="ktp_profile_items">
                    <div class="ktp_profile_items_left">
                        <strong>Subscribers :</strong>
                    </div>
                    <div class="ktp_profile_items_right">
                        <strong><?php kish_twit_pro_profile_list_subscribers_link($s->user->screen_name, $s->slug, $s->subscriber_count, KTP_CONT_1);; ?></strong>
                    </div>
            </div>
            <div class="ktp_profile_items">
            	<div class="ktp_profile_items_left">
                	<strong>Members :</strong>
                </div>
                <div class="ktp_profile_items_right">
                   	<strong><?php kish_twit_pro_profile_list_members_link($s->user->screen_name, $s->slug, $s->member_count, KTP_CONT_1); ?></strong><?php $user=$s->user->screen_name; ?>
                </div>
            </div>
			<?php //kish_twit_pro_is_subscribed($user, $s['name'], $userid); ?>
             <div class="ktp_action_panel">
                <div style="width:98%;float:left;height:22px;display:inline-block">
				<?php //echo "Username is ".$username." and user is ".$user." userid is ".$userid." and name is "; ?>
					<?php if($mylist && $refreshrequest=="subscribedlists") :?>
					<div class="ktp_action_panel_links">
                        <span id="remlist_<?php echo $s->user->screen_name."_".$s->slug; ?>"><a href="#" onclick="kish_twit_pro_unsubscribe_list('<?php echo $s->user->screen_name;?>','<?php echo $s->slug;?>', 'sublist_<?php echo $s->user->screen_name."_".$s->slug; ?>', 'remlist_<?php echo $s->user->screen_name."_".$s->slug; ?>');return false;">Leave</a></span>
                    </div>
                    <?php endif; ?>
                	<?php if(!$mylist) : ?>
                    <div class="ktp_action_panel_links">
                        <?php kish_twit_pro_subscribe_list_link($s->user->screen_name, $s->slug); ?>
                    </div>
                    <?php endif;
					if($mylist && $refreshrequest=="lists"): ?>
                    <div class="ktp_action_panel_links">
                        <?php kish_twit_pro_edit_list_link("edit_save_list_form_".$s->id); ?>
                    </div>
                    <?php endif; ?>
                    <div class="ktp_action_panel_links">
                    	<?php kish_twit_pro_profile_list_tl_link($s->user->screen_name, $s->slug, "Updates", KTP_CONT_1); ?>
                    </div>
                </div>
             </div>
        <?php $counter++; ?>
		<div id="edit_save_list_form_<?php echo $s->id; ?>" style="display:none">
			<?php kish_twit_pro_edit_list_form($s->id, $s->name, $s->mode, $s->description); ?>
        </div>
    	<div style="clear:both"></div>
    </div>
	<?php endforeach; ?>
	<?php if($mylist && $refreshrequest=="lists"): ?>
	<div class="ktp_timeline_box">
	<?php kish_twit_pro_new_list_form(); ?>
    </div>
    <?php endif; ?>
	<?php if(strlen($nextcursor)>5) { ?>
    <?php $req="{$refreshrequest}&user={$user}&cursor={$nextcursor}"; 
    		echo "<script type=\"text/javascript\">\n";
			echo "ktp_cont2_req='".$req."';\n";
			echo "ktp_cont2_resultdiv='cont-".$nextcursor."';\n";
			echo "ktp_cont2_progdiv='cont-".$nextcursor."';\n";
			echo "scrollnew2=true;\n";
			echo "</script>";
	?>
	<div id ="cont-<?php echo $nextcursor; ?>"><input class="button-secondary" type="button" value ="Load More &raquo;" onclick="kish_twit_pro_process_ajax_load_new('req=<?php echo $req; ?>', 'cont-<?php echo $nextcursor ?>', 'cont-<?php echo $nextcursor; ?>', '<?php echo KTP_LOADER_1; ?>'); return false;">
	<?php 
	}
	else {
		echo "<script type=\"text/javascript\">\n";
		echo "ktp_cont2_req='';\n";
		echo "</script>";
	}
}
// Lists Subscribed by a tweep
function kish_twit_pro_print_lists_subscribed_by_user($user='', $nextcursor='') {
	strlen($user)?$mylist=false : $mylist=true;
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	if(!strlen($user)) {
		if(!empty($ktproselectedaccount)) {
			$ktpoauthmode ? $user= $ktproselectedaccount[0]->ktpro_screen_name : $user=KTP_TWIT_UNAME;
		}
		else {
			$ktpoauthmode ? $user= KTP_TWIT_COOK_SCREEN_NAME : $user=KTP_TWIT_UNAME;
		}
	}
	if(strlen($nextcursor)) {
		$options=array('cursor' => $nextcursor);
		$page=2;
	}
	else {
		$options=array();
	}
	$data = $ktproauth->get("{$user}/lists/subscriptions", $options);
	//print_r($data);
	kish_twit_pro_print_lists($data,  "Lists followed by {$user}", "subscribedlists", KTP_CONT_1, $nextcursor, $mylist); 
	//ktp_print_list_subscribers($data, $user, $listid, 'listsubscribers');
}
// Replies
function kish_twit_pro_print_user_relies($user='', $maxid='', $sinceid='', $count='') {
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	$page=0;
	if(!strlen($user)) {
		if(!empty($ktproselectedaccount)) {
			$ktpoauthmode ? $user= $ktproselectedaccount[0]->ktpro_screen_name : $user=KTP_TWIT_UNAME;
		}
		else {
			$ktpoauthmode ? $user= KTP_TWIT_COOK_SCREEN_NAME : $user=KTP_TWIT_UNAME;
		}
	}
	!strlen($count)?$count=KTP_TWIT_MAX:$count=$count;
	if(strlen($maxid)) {
		$options = array('screen_name' => $user, 'count' => $count, 'max_id' => $maxid);
		$page=2;
	}
	else {
		if(strlen($sinceid)) {
			$options=array('screen_name' => $user, 'count' => $count, 'since_id' => $sinceid);
			$page=1;
		}
		else {
			$options= array('screen_name' => $user, 'count' => $count);
		}
	}
	kish_twit_pro_user_timeline($ktproauth->get('statuses/replies', $options), 'Replies I Got', 'myreplies', 'kpt_container_2', 'page-replies', $page);
}
// Favorites
function kish_twit_pro_print_user_favs($user='', $page=1) {
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	if(!strlen($user)) {
		if(!empty($ktproselectedaccount)) {
			$ktpoauthmode ? $user= $ktproselectedaccount[0]->ktpro_screen_name : $user=KTP_TWIT_UNAME;
		}
		else {
			$ktpoauthmode ? $user= KTP_TWIT_COOK_SCREEN_NAME : $user=KTP_TWIT_UNAME;
		}
	}
	$options=array('id'=>$user, 'page'=>$page);
	kish_twit_pro_user_timeline($ktproauth->get('favorites', $options), 'My Favorite Tweets', 'favorites&user='.$user, 'kpt_container_2', 'fav-page', $page);
}
// Make Favorites
function kish_twit_pro_create_fav($id) {
	global $ktproauth, $ktpoauthmode;
	if($ktpoauthmode) {
		$ktproauth->post('favorites/create', array('id' => $id));
		kish_twit_pro_print_refresh_link('favorites', 'Check Favs', 'kpt_container_2','menu-', KTP_LOADER_1);
	}
}
// Destroy Favorites
function kish_twit_pro_destroy_fav($id) {
	global $ktproauth, $ktpoauthmode;
	if($ktpoauthmode) {
		$ktproauth->post('favorites/destroy', array('id' => $id));
		kish_twit_pro_print_refresh_link('favorites', 'Check Favs', 'kpt_container_2','menu-', KTP_LOADER_1);
	}
}
// list members
function kish_twit_pro_print_list_members($user, $listid, $nextcursor='') {
	$page=0;
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	if(strlen($nextcursor)) {
			$options=array('cursor' => $nextcursor);
			$page=2;
	}
	else {
		$options=array();
	}
	$data = $ktproauth->get("{$user}/{$listid}/members", $options);
	//print_r($data);
	ktp_print_list_subscribers($data, $user, $listid, 'listmembers');
	//kish_twit_pro_user_timeline($data, "Members of {$listid}", 'listmembers', 'kpt_container_1', 'page-list-mem', $page);
	//kish_twit_pro_user_timeline(kish_twit_pro()->repliesLine(), 'Replies I Got', 'mentions', 'kpt_container_2', 'page-mentions');
}
// Subscribe to New List
function kish_twit_pro_subscribe_new_list($user, $listid) {
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	if(!strlen($user)) {
		if(!empty($ktproselectedaccount)) {
			$ktpoauthmode ? $user= $ktproselectedaccount[0]->ktpro_screen_name : $user=KTP_TWIT_UNAME;
		}
		else {
			$ktpoauthmode ? $user= KTP_TWIT_COOK_SCREEN_NAME : $user=KTP_TWIT_UNAME;
		}
	}
	$ktproauth->post("{$user}/{$listid}/subscribers", array());
	echo "Done..";
}
// unsubscribe a list
function kish_twit_pro_unsubscribe_list($user, $listid) {
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	$ktproauth->delete("{$user}/{$listid}/subscribers", array());
	echo "Done..";
}
// check if the user is subscribed to that particular list
function kish_twit_pro_is_subscribed($user, $listid, $ismember) {
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	if($ktpoauthmode) {
		echo "User is {$user}, listid is {$listid} checking for {$ismember}";
		$data=$ktproauth->get("{$user}/{$listid}/subscribers/{$ismember}", array());
		print_r($data);
	}	
	else {echo "Error Login";}
}
// list subscribers
function kish_twit_pro_print_list_subscribers($user, $listid, $nextcursor='') {
	$page=0;
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	if(strlen($nextcursor)) {
		$options=array('cursor' => $nextcursor);
		$page=2;
	}
	else {
		$options=array();
	}
	$data = $ktproauth->get("{$user}/{$listid}/subscribers", $options);
	//print_r($data);
	ktp_print_list_subscribers($data, $user, $listid, 'listsubscribers');
}

// Adding a new member to a list
function kish_twit_pro_add_member_to_list($member, $listid) {
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	if(!empty($ktproselectedaccount)) {
			$ktpoauthmode ? $user= $ktproselectedaccount[0]->ktpro_screen_name : $user=KTP_TWIT_UNAME;
	}
	else {
		$ktpoauthmode ? $user= KTP_TWIT_COOK_SCREEN_NAME : $user=KTP_TWIT_UNAME;
	}
	$options=array();	
	$options=array('id'=>$member);
	$k=$ktproauth->post("{$user}/{$listid}/members", $options);
	//print_r($k);
	kish_twit_pro_profile_list_members_link($user, $listid, "Added..", KTP_CONT_1);
	//echo "Added..";
	//kish_twit_pro_print_list_members($user,$listid);
}
function kish_twit_pro_remove_member_from_list($member, $listid) {
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	if(!empty($ktproselectedaccount)) {
			$ktpoauthmode ? $user= $ktproselectedaccount[0]->ktpro_screen_name : $user=KTP_TWIT_UNAME;
	}
	else {
		$ktpoauthmode ? $user= KTP_TWIT_COOK_SCREEN_NAME : $user=KTP_TWIT_UNAME;
	}
	$options=array();	
	$options=array('id'=>$member);
	$k=$ktproauth->delete("{$user}/{$listid}/members", $options);
	//print_r($k);
	kish_twit_pro_print_list_members($user,$listid);
}
// Prints New List creating form
function kish_twit_pro_new_list_form() {?>
	<div><strong>Create a New List</strong></div>
	<input type="hidden" id = "ktp_hidden_listid">
	<div style="font-size:11px;width:99%; margin:5px 2px 3px 2px;">
    	<div style="display:block;width:99%;height:25px;">
            <div style="width:25%;float:left;height:25px;display:inline-block;">Name</div>
            <div style="width:72%;float:right;height:25px;display:inline-block;"><input class="ktp_text_box" type="text" id="ktp_new_list_name"></div>
        </div>
        <div style="clear:both">
        <div style="display:block;width:99%;height:25px;">
            <div style="width:25%;float:left;height:25px;display:inline-block;">Mode</div>
            <div style="width:72%;float:right;height:25px;display:inline-block;"><select style="margin:2px;height:23px;width:95%" id="select_mode"><option value="public">Public</option><option value="private">Private</option></select></div>
        </div>
        <div style="clear:both">
        <div style="display:block;width:99%;height:25px;">
            <div style="width:25%;float:left;height:25px;display:inline-block;">Description</div>
            <div style="width:72%;float:right;height:25px;display:inline-block;"><input class="ktp_text_box" type="text" id="ktp_new_list_desc"></div>
        </div>
        <div style="clear:both">
        <div style="display:block;width:99%;height:25px;">
            <div style="width:25%;float:right;height:25px;"><input class="button-secondary" style="float:right" type="button" value="Save" onclick="kish_twit_pro_save_new_list();"></div>
        </div>
        <div style="clear:both">
    </div>
<?php
}
// Prints Edit List creating form
function kish_twit_pro_edit_list_form($listid, $name, $mode, $description) {?>
	<input type="hidden" id = "ktp_hidden_listid_<?php echo $listid; ?>" value="<?php echo $listid; ?>">
	<div style="font-size:11px;width:99%; margin:5px 2px 3px 2px;">
    	<div style="display:block;width:99%;height:25px;">
            <div style="width:25%;float:left;height:25px;display:inline-block;">Name</div>
            <div style="width:72%;float:right;height:25px;display:inline-block;"><input class="ktp_text_box" type="text" id="ktp_new_list_name_<?php echo $listid; ?>" value="<?php echo $name; ?>"></div>
        </div>
        <div style="display:block;width:99%;height:25px;">
            <div style="width:25%;float:left;height:25px;display:inline-block;">Mode</div>
            <div style="width:72%;float:right;height:25px;display:inline-block;"><select style="margin:2px;height:23px;width:95%" id="select_mode_<?php echo $listid; ?>"><option value="public" <?php if($mode=='public') {echo "selected=selected";} ?>>Public</option><option value="private" <?php if($mode=='private') {echo "selected=selected";} ?>>Private</option></select></div>
        </div>
        <div style="display:block;width:99%;height:25px;">
            <div style="width:25%;float:left;height:25px;display:inline-block;">Description</div>
            <div style="width:72%;float:right;height:25px;display:inline-block;"><input class="ktp_text_box" type="text" id="ktp_new_list_desc_<?php echo $listid; ?>" value="<?php echo $description; ?>"></div>
        </div>
        <div style="display:block;width:99%;height:25px;">
            <div style="width:50%;float:right;height:25px;display:inline-block;"><input  class="button-secondary" type="button" value="Cancel" onclick="kish_twitter_clear_div('edit_save_list_form_<?php echo $listid; ?>');"><input class="button-secondary"  type="button" value="Save" onclick="kish_twit_pro_edit_old_list('<?php echo $listid; ?>');"></div>
        </div>
    </div>
<?php
}
// Create New List
function kish_twit_pro_create_new_list($name, $mode='', $desc='') {
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	if(!empty($ktproselectedaccount)) {
			$ktpoauthmode ? $user= $ktproselectedaccount[0]->ktpro_screen_name : $user=KTP_TWIT_UNAME;
	}
	else {
		$ktpoauthmode ? $user= KTP_TWIT_COOK_SCREEN_NAME : $user=KTP_TWIT_UNAME;
	}
	$options=array();	
	$options=array('name'=>$name, 'mode'=>$mode, 'description'=>$desc);
	$ktproauth->post("{$user}/lists", $options);
	 kish_twit_pro_print_saved_lists();
}
// Edit Saved List
function kish_twit_pro_edit_saved_list($listid, $name, $mode='', $desc='') {
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	if(!empty($ktproselectedaccount)) {
			$ktpoauthmode ? $user= $ktproselectedaccount[0]->ktpro_screen_name : $user=KTP_TWIT_UNAME;
	}
	else {
		$ktpoauthmode ? $user= KTP_TWIT_COOK_SCREEN_NAME : $user=KTP_TWIT_UNAME;
	}
	$options=array();	
	$options=array('name'=>$name, 'mode'=>$mode, 'description'=>$desc);
	echo $ktproauth->post("{$user}/lists/{$listid}", $options);
	 kish_twit_pro_print_saved_lists();
}
//print User List in List Box for adding new members
function kish_twit_pro_print_list_listbox() {
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	if(!strlen($user)) {
		if(!empty($ktproselectedaccount)) {
			$ktpoauthmode ? $user= $ktproselectedaccount[0]->ktpro_screen_name : $user=KTP_TWIT_UNAME;
		}
		else {
			$ktpoauthmode ? $user= KTP_TWIT_COOK_SCREEN_NAME : $user=KTP_TWIT_UNAME;
		}
	}
	if(strlen($nextcursor)) {
			$options=array('cursor' => $nextcursor);
			$page=2;
	}
	else {
		$options=array();
	}
	$data = $ktproauth->get($user.'/lists', $options); 
	$r=rand(500, 9999999);
	//print_r($data);
	//echo $data['error'];
	$divid="profile_list_".$r;
    $lb="<div style=\"float:left\"><select id=\"profile_list_".$r."\" name =\"profile_list_".$r."\" style=\"width:95%\">"; 
	$counter=1;
	$lb.="<option value=\"Select Your List\" selected=\"selected\">Select Your List</option>";
	foreach($data->lists as $d): 
		$lb= $lb."<option value=\"".$d->slug."\">".$d->name." </option>";
		$counter++;
	endforeach;
	$lb = $lb."</select></div>";
	$lb = $lb."<div class=\"ktp_profile_items_right\" style=\"width:27%\" id=\"profile_list_butt_".$r."\">
					<input type=\"button\" value=\"Add &raquo;\" class=\"button-secondary\" onclick=\"kish_twit_pro_add_member_to_list('##user##', '".$divid."', 'profile_list_result_".$r."', 'profile_list_butt_".$r."');\">
					
				</div><span style=\"font-size:10px\" id=\"profile_list_result_".$r."\"></span>";
	return $lb;
}
function kish_twit_pro_print_list_divbox($userid) {
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	if(!strlen($user)) {
		if(!empty($ktproselectedaccount)) {
			$ktpoauthmode ? $user= $ktproselectedaccount[0]->ktpro_screen_name : $user=KTP_TWIT_UNAME;
		}
		else {
			$ktpoauthmode ? $user= KTP_TWIT_COOK_SCREEN_NAME : $user=KTP_TWIT_UNAME;
		}
	}
	if(strlen($nextcursor)) {
			$options=array('cursor' => $nextcursor);
			$page=2;
	}
	else {
		$options=array();
	}
	$data = $ktproauth->get($user.'/lists', $options); 
	$r=rand(100000, 9999999);
	$divid="profile_list_".$r;
    $lb="<div style=\"float:left:width:95%\">"; 
	$counter=1;
	foreach($data['lists'] as $d): 
		$divid2="list_".$userid."_".$d['slug']."_".$r;
		$lb= $lb."<div id=\"".$divid2."\>".$d['name']."</div>";
		$counter++;
	endforeach;
	$lb = $lb."</div>";
	$lb = $lb."<div class=\"ktp_profile_items_right\" style=\"width:27%\">
					<input type=\"button\" value=\"Add\" class=\"button-secondary\" onclick=\"kish_twit_pro_add_member_to_list('".$userid."', '".$divid."'); kish_twit_pro_update_header_title('".KTP_CONT_1."_top_data', 'Added to list', '".KTP_LOADER_1."');\">
				</div>";
	return $lb;
}
function ktp_print_list_subscribers($s, $user, $listid, $request) {
	if(!empty($ktproselectedaccount)) {
		$ktpoauthmode ? $loggeduser= $ktproselectedaccount[0]->ktpro_screen_name : $loggeduser=KTP_TWIT_UNAME;
	}
	else {
		$ktpoauthmode ? $loggeduser= KTP_TWIT_COOK_SCREEN_NAME : $loggeduser=KTP_TWIT_UNAME;
	}
	$list = kish_twit_pro_print_list_listbox();
	$nextcursor=$s->next_cursor;
	foreach($s->users as $status): ?>
        <?php if(!strlen($status->screen_name)) {return;}?>
        <?php $kish_text = kish_twit_pro_add_profile_link(kish_twit_pro_changeToUrl($status->status->text), "retweetinfo-", $status->screen_name); ?>
		<div class="ktp_timeline_box" id="ktp_list_mem_<?php echo $status->screen_name; ?>">
		<p class="ktp_p"><img class="ktp_thumb" src="<?php echo $status->profile_image_url; ?>" />
		<?php echo "<a rel=\"nofollow\" href =\"".$status->url."\">".$status->screen_name."</a>-".$kish_text; ?> | <?php echo kish_twit_pro_format_date($status->created_at); ?></p>
		<div style="clear:both"></div>
				<?php if(ktpoath()) { ?>
                <div class="ktp_action_panel">
                	 <div style="width:98%;float:left;height:22px;display:inline-block">
                        <div class="ktp_action_panel_links">
                            <?php //kish_twit_pro_print_user_profile($status->screen_name, $status->id, "retweetinfo-"); ?>
                            <?php kish_twit_pro_print_user_profile_retrive($status->screen_name, $status->id, "myfollow-"); ?>
                        </div>
                        <div class="ktp_action_panel_links">
                        	<?php $status->following!=1 ? kish_twit_pro_follow_user_link($status->screen_name, $status->id, 'retweetinfo-') : kish_twit_pro_unfollow_link($status->screen_name); ?>   
                            <?php //kish_twit_pro_follow_user_link($status['screen_name'], $status['id'], 'retweetinfo-'); ?>
                        </div>
                        <?php if($loggeduser==$user && $request=="listmembers"):?>
                        <div class="ktp_action_panel_links">
                            <span id ="removelink_<?php echo $status->screen_name;?>"><a href="#" onclick="kish_twit_pro_remove_member_from_list('<?php echo $status->id;?>', '<?php echo $listid; ?>', 'ktp_list_mem_<?php echo $status->screen_name; ?>', 'removelink_<?php echo $status->screen_name;?>'); return false;">Remove</a></span>
                        </div>
                        <?php endif;?>
                    </div>
				</div> 
                <div style="display:none" id="profinfo-<?php echo $status->id; ?>">
                	<?php //$st->user=$status; ?>
                	<?php //kish_twit_pro_print_profile_hidden_tl($status, $list); ?>
                </div>  
				<?php } ?> 
			</div>
			<div style="clear:both"></div>
         	<?php $counter++; ?>
         	<?php //endif; ?>
	<?php endforeach;  ?>
    <?php if(strlen($nextcursor)>5) { ?>
    <?php $req="{$request}&user={$user}&listid={$listid}&cursor={$nextcursor}"; ?>
    <?php //echo $request; ?>
    <?php if($request=="listmembers" || $request=="listsubscribers") {
			echo "<script type=\"text/javascript\">\n";
			echo "ktp_cont1_req='".$req."';\n";
			echo "ktp_cont1_resultdiv='cont-".$nextcursor."';\n";
			echo "ktp_cont1_progdiv='cont-".$nextcursor."';\n";
			echo "scrollnew=true;\n";
			echo "</script>";
		}
	?>
	<div id ="cont-<?php echo $nextcursor; ?>"><input class="button-secondary" type="button" value ="Load More &raquo;" onclick="kish_twit_pro_process_ajax_load_new('req=<?php echo $req; ?>', 'cont-<?php echo $nextcursor ?>', 'cont-<?php echo $nextcursor; ?>', '<?php echo KTP_LOADER_1; ?>'); return false;"></div>
	<?php 
    }
    else {
    	echo "<script type=\"text/javascript\">\n";
		echo "ktp_cont1_req='';\n";
		echo "</script>";
    }
}
// list Status Updates
function kish_twit_pro_print_list_tl($user, $listid, $maxid='', $sinceid='', $count='') {
	$page=0;
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	!strlen($count)?$count=KTP_TWIT_MAX:$count=$count;
	if(strlen($maxid)) {
		$options = array('screen_name' => $user, 'per_page' => $count, 'max_id' => $maxid);
		$page=2;
	}
	else {
		if(strlen($sinceid)) {
			$options=array('screen_name' => $user, 'per_page' => $count, 'since_id' => $sinceid);
			$page=1;
		}
		else {
			$options= array('screen_name' => $user, 'per_page' => $count);
		}
	}
	$data = $ktproauth->get("{$user}/lists/{$listid}/statuses", $options);
	//print_r($data);
	
	kish_twit_pro_user_timeline($data, "Status of {$listid}", "liststl&user={$user}&listid={$listid}", 'kpt_container_1', 'page-list-tl', $page);
	//kish_twit_pro_user_timeline(kish_twit_pro()->repliesLine(), 'Replies I Got', 'mentions', 'kpt_container_2', 'page-mentions');
}
// Mentions
function kish_twit_pro_print_user_mentions($user='', $maxid='', $sinceid='', $count='') {
	$page=0;
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	if(!strlen($user)) {
		if(!empty($ktproselectedaccount)) {
			$ktpoauthmode ? $user= $ktproselectedaccount[0]->ktpro_screen_name : $user=KTP_TWIT_UNAME;
		}
		else {
			$ktpoauthmode ? $user= KTP_TWIT_COOK_SCREEN_NAME : $user=KTP_TWIT_UNAME;
		}
	}
	!strlen($count)?$count=KTP_TWIT_MAX:$count=$count;
	if(strlen($maxid)) {
		$options = array('screen_name' => $user, 'count' => $count, 'max_id' => $maxid);
		$page=2;
	}
	else {
		if(strlen($sinceid)) {
			$options=array('screen_name' => $user, 'count' => $count, 'since_id' => $sinceid);
			$page=1;
		}
		else {
			$options= array('screen_name' => $user, 'count' => $count);
		}
	}
	kish_twit_pro_user_timeline($ktproauth->get('statuses/mentions', $options), 'Replies I Got', 'mentions', 'kpt_container_2', 'page-mentions', $page);
	//kish_twit_pro_user_timeline(kish_twit_pro()->repliesLine(), 'Replies I Got', 'mentions', 'kpt_container_2', 'page-mentions');
}
// Direct Messages Sent
function kish_twit_pro_print_dm_sent($user='', $maxid='', $sinceid='', $count='') {
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	if(!$ktpoauthmode && empty($ktproselectedaccount)) { echo "<span class=\"ktp_error\">Sorry, I cannot show you my Direct Messages !!</span>"; return; }
	if(!strlen($user)) {
		if(!empty($ktproselectedaccount)) {
			$ktpoauthmode ? $user= $ktproselectedaccount[0]->ktpro_screen_name : $user=KTP_TWIT_UNAME;
		}
		else {
			$ktpoauthmode ? $user= KTP_TWIT_COOK_SCREEN_NAME : $user=KTP_TWIT_UNAME;
		}
	}
	$page=0;
	!strlen($count)?$count=KTP_TWIT_MAX:$count=$count;
	if(strlen($maxid)) {
		$options = array('screen_name' => $user, 'count' => $count, 'max_id' => $maxid);
		$page=2;
	}
	else {
		if(strlen($sinceid)) {
			$options=array('screen_name' => $user, 'count' => $count, 'since_id' => $sinceid);
			$page=1;
		}
		else {
			$options= array('screen_name' => $user, 'count' => $count);
		}
	}
	kish_twit_pro_user_timeline($ktproauth->get('direct_messages/sent', $options), 'Direct Messages Sent', 'dmsent', 'kpt_container_2', 'page-dmsent', $page);
}
// Tweets Public Timeline
function kish_twit_pro_print_public_tl() {
	global $ktproauth, $ktpoauthmode;
	kish_twit_pro_user_timeline($ktproauth->get('statuses/public_timeline'), 'Latest Public Updates', 'ktppublictimeline', 'kpt_container_2', 'page-public');
}
// latest tweets of this user
function kish_twit_pro_print_this_user_latest_twits($user='', $maxid='', $sinceid='', $count='') {
	//echo "user inside the function ". $user;
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	if(!strlen($user)) {
		if(!empty($ktproselectedaccount)) {
			$ktpoauthmode ? $user= $ktproselectedaccount[0]->ktpro_screen_name : $user=KTP_TWIT_UNAME;
		}
		else {
			$ktpoauthmode ? $user= KTP_TWIT_COOK_SCREEN_NAME : $user=KTP_TWIT_UNAME;
		}
	}
	$page=0;
	!strlen($count)?$count=KTP_TWIT_MAX:$count=$count;
	if(strlen($maxid)) {
		$options = array('screen_name' => $user, 'count' => $count, 'max_id' => $maxid);
		$page=2;
	}
	else {
		if(strlen($sinceid)) {
			$options=array('screen_name' => $user, 'count' => $count, 'since_id' => $sinceid);
			$page=1;
		}
		else {
			$options= array('screen_name' => $user, 'count' => $count);
		}
	}
	kish_twit_pro_user_timeline($ktproauth->get('statuses/user_timeline', $options), $user." Latest Updates", "thisuserlatesttweets&user=".$user, KTP_CONT_1, "page-progress", $page);
}
function kish_twit_pro_statusus_tl($status, $timelineheading, $refreshrequest, $refreshresultdiv, $refreshprogressdiv, $page=1) {
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	$list = kish_twit_pro_print_list_listbox();
	empty($ktproselectedaccount) ? $seluser=KTP_TWIT_COOK_SCREEN_NAME : $seluser = $ktproselectedaccount[0]->ktpro_screen_name;
	if ($status){
		if(current_user_can('edit_users')) {
		//print_r($status);
			//echo $refreshrequest;
		}
		if($page==1) {
			$start = 0;
			$end = KTP_TWIT_UMAX-1;
		}else {
			$start= (($page-1)*KTP_TWIT_UMAX)-1;
			$end = $start + KTP_TWIT_UMAX;
		}
		$nextpage=$page+1;
		$counter=0;
		if(current_user_can('edit_users')) {
			//print_r($status);
		}
		//print_r($status);
		echo "<p style=\"margin-left:3px\"><strong>";
		if($refreshrequest=='ktpfriendstimeline') {
			kish_twit_pro_print_refresh_link_paged($page+1,$refreshrequest, $timelineheading." - [Next Page]", $refreshresultdiv, 'header-',KTP_LOADER_1);
		} else {
			kish_twit_pro_print_refresh_link($refreshrequest, $timelineheading." - [Refresh]", $refreshresultdiv, 'header-',KTP_LOADER_1);
		}
		
		echo "</strong>";
		echo "</p>";
		foreach($status->users as $s): ?>
		<?php //for($c=$start; $c<$end; $c++) : ?>
        	<?php if(!strlen($s->screen_name)) {return;}?>
        	<?php //if($counter<=KTP_TWIT_UMAX) : ?>
            <?php $kish_text = kish_twit_pro_add_profile_link(kish_twit_pro_changeToUrl($s->status->text), "myreplies-", $s->screen_name); ?>
			<div class="ktp_timeline_box">
				<p class="ktp_p"><img class="ktp_thumb" src="<?php echo $s->profile_image_url; ?>" />
				<?php echo "<a target=\"_blank\" rel=\"nofollow\" href =\"".$s->url."\">".$s->screen_name."</a>-".$kish_text; ?> | Joined <?php echo kish_twit_pro_format_date($s->created_at); ?> | <?php echo $s->status->source; ?></p>
				<div style="clear:both"></div>
				<?php if($ktpoauthmode) { ?>
                <?php $tempreq='req='.$refreshrequest;
					$ktpclass='ktp_action_panel';
					parse_str($tempreq);
				?>
                <div class="<?php echo $ktpclass; ?>">
                	 <div style="width:98%;float:left;height:22px;display:inline-block">
                        <div class="ktp_action_panel_links">
                            <?php //kish_twit_pro_print_user_profile($s->screen_name, $s->id, "myreplies-"); ?>
                            <?php kish_twit_pro_print_user_profile_retrive($s->screen_name, $s->id, "myfollow-"); ?>
                        </div>
                       
						<?php
                        if(($req =='followmepage') || ($req =='featuredusers') || ($req=='ifollowpage' && $user==$seluser) || ($req =='featuredusers' || $user!=$ktproselectedaccount[0]->ktpro_screen_name)) { ?>
                 			 <div class="ktp_action_panel_links">
		                        <?php $s->following!=1 ? kish_twit_pro_follow_user_link($s->screen_name, $s->id, 'favfollow-') : kish_twit_pro_unfollow_link($s->screen_name); ?>                     
		                 	</div>
                        <?php } ?>
                    </div>
				</div> 
                <div style="display:none" id="profinfo-<?php echo $s->id; ?>">
                	<?php //kish_twit_pro_print_profile_hidden_tl($s, $list); ?>
                </div>  
				<?php } ?> 
			</div>
			<div style="clear:both"></div>
         	<?php $counter++; ?>
         	<?php //endif; ?>
		<?php //endfor; ?>
		<?php endforeach; ?>
		<?php //echo $req;?>
		<?php if($status->next_cursor!=0) {?>
		<?php $refreshrequest.="&cursor=".$status->next_cursor;?>
		<?php if($req=="followmepage" || $req=="ifollowpage") {
			echo "<script type=\"text/javascript\">\n";
			echo "ktp_cont2_req='".$refreshrequest."';\n";
			echo "ktp_cont2_resultdiv='cont-".$counter."';\n";
			echo "ktp_cont2_progdiv='cont-".$counter."';\n";
			echo "scrollnew2=true;\n";
			echo "</script>";
		}
		?>
			<div id ="cont-<?php echo $counter; ?>"><input class="button-secondary" type="button" value ="Load More &raquo;" onclick="kish_twit_pro_process_ajax_load_new('<?php echo $refreshrequest; ?>&page=<?php echo $nextpage; ?>', 'cont-<?php echo $counter; ?>', 'cont-<?php echo $counter; ?>', '<?php echo KTP_LOADER_1; ?>'); return false;">
		<?php 
		}
	}	
	
}
function kish_twit_pro_print_profile_hidden_rt_users($status, $counter) {
	$profimage=$status[$counter]['user']['profile_image_url']; 
	$joindate=$status[$counter]['user']['created_at'];
	$jdate = substr($joindate, 0, 10).substr($joindate,-5);
    if($status[$counter]['user']['verified']==1) {$accountverified=true;} 
	strlen($status['verified']) ? $verfiedstyle="margin-left:10px;color:#009900" : $verfiedstyle="margin-left:10px;color:#CA0000";?>
    <div>
	<img style="float:right;width:50px;height:50px" src="<?php echo $profimage; ?>">
    <p><strong style="<?php echo $verifiedstyle; ?>"><?php echo $status[$counter]['user']['name']; ?></strong></p>
    <ul>
        <li>Joined On : <?php echo $jdate; ?>
		<li>Location : <?php kish_twit_pro_print_search_link($status[$counter]['user']['location']); ?>
        <li>Account Status : <?php if($accountverified) {echo "Verified";} else {echo "Not verified";} ?>
        <li>Description : <?php echo $status[$counter]['user']['description']; ?></li>
        <li>Following : <?php kish_twit_pro_print_links_in_profile('ifollowpage&user='. $status[$counter]['user']['screen_name'], $status[$counter]['user']['friends_count'], 'kpt_container_2', 'kish_multi_wp_prog_1', KTP_LOADER_1); ?></li>
        <li>Followers : <?php kish_twit_pro_print_links_in_profile('followmepage&user='. $status[$counter]['user']['screen_name'], $status[$counter]['user']['followers_count'], 'kpt_container_2', 'kish_multi_wp_prog_1', KTP_LOADER_1); ?></li>
        <li>Favourites : <?php kish_twit_pro_print_links_in_profile('favorites&user='. $status[$counter]['user']['screen_name'], $status[$counter]['user']['favourites_count'], 'kpt_container_2', 'kish_multi_wp_prog_1', KTP_LOADER_1); ?></li>
        <li>Total Updates : <?php kish_twit_pro_print_links_in_profile('thisuserlatesttweets&user='. $status[$counter]['user']['screen_name'], $status[$counter]['user']['statuses_count'], KTP_CONT_1, 'kish_multi_wp_prog_1', KTP_LOADER_1); ?></li>
        <li><?php kish_twit_pro_profile_conversation_link($status['user'][$counter]['screen_name'], KTP_CONT_1, 'kish_multi_wp_prog_1' ); ?></li>
		<li><?php kish_twit_pro_profile_your_conversation_link($status['user'][$counter]['screen_name'], KTP_CONT_1, 'kish_multi_wp_prog_1' ); ?></li>
		<li><a href="<?php echo $status[$counter]['user']['url']; ?>">Visit Website</a></li> 
		<li><?php $status[$counter]['user']['following']!=1 ? kish_twit_pro_follow_user_link($status[$counter]['user']['screen_name'], $status[$counter]['user']['id'], 'favfollow-') : kish_twit_pro_unfollow_link($status[$counter]['user']['screen_name']); ?></li>
		<?php echo kish_twit_pro_check_rel($status[$counter]['user']['screen_name']); ?>       
   </ul>  
   <?php //printUserTweetsLink($username); ?> 
   </div> <?php
}
// CACHE FUNCTIONS
function kish_twit_write_json($jsondata, $filename) {
	if(function_exists('json_decode')){
		$jsondata = json_encode($jsondata);
	}
	else {
		require_once 'oauth/twitteroauth/kish_json.php';
		$json = new Services_JSON(SERVICES_JSON_IN_OBJ);
		$jsondata = $json->encode($jsondata);
	}
	kish_twit_pro_write_cache($jsondata, $filename); 
}
function kish_twit_read_json($filename) {
	$data=array();
	if(file_exists($filename)) {
		$fh = fopen($filename,'r');
		$jsondata=file_get_contents($filename);
		fclose($fh);
		if(function_exists('json_decode')){
			return json_decode($jsondata);
		} else {
			require_once 'oauth/twitteroauth/kish_json.php';
			$json = new Services_JSON(SERVICES_JSON_IN_OBJ);
			return $json->decode($jsondata);
		}
	}
	else {
		return $data;
	}
}
function kish_twit_pro_write_cache($file, $filename) {
	$fh = fopen($filename,'w') or die("can't write to file - please make sure that xmlcache folder is chmod 777");
	if(fwrite($fh,$file)) {echo ""; }
	fclose($fh);
}
function kish_twit_pro_retweeted_by($tweetid) {
	global $ktproauth, $ktpoauthmode;
	//$options=array('id'=>$tweetid);
	$status = $ktproauth->get("statuses/retweets/{$tweetid}", $options);
	//print_r($status);
	if(strlen($status->error)) {
		echo "<span class=\"ktp_error\">Error Communication With Twitter..</span>"; return;
	}
	if(!strlen($status[0]->user->screen_name)) {echo "<span class=\"ktp_error\">Sorry no retweets yet..</span>"; return;}
	$thistext = kish_twit_pro_add_profile_link(kish_twit_pro_changeToUrl($status[0]->retweeted_status->text), "myreplies-", $status[0]->user->screen_name);
	?>
    <div class="ktp_featured_1" style="background:<?php echo $ktp_theme_light; ?>;min-height:80px;border:1px solid <?php echo $ktp_theme_dark; ?>">
    <p style="font-size:11px;padding:4px;margin-bottom:5px;"><img style="float:right;width:60px;height:60px" src="<?php echo $status[0]->retweeted_status->user->profile_image_url; ?>" />
		<?php echo "<a rel=\"nofollow\" href =\"".$status->retweeted_status->user->url."\">".$status[0]->retweeted_status->user->screen_name."</a>-".$thistext; ?> | <?php echo kish_twit_pro_format_date($status[0]->retweeted_status->created_at); ?> | <?php echo $status[0]->retweeted_status->user->source; ?></p>
    </div>
    <div style="clear:both;"></div>
    <?php
	//echo "<strong>Status text - ".$status[0]['retweeted_status']['text']."by ".$status[0]['retweeted_status']['user']['screen_name']."</strong>\n";
	ktp_print_retweet_info($status);
}

function ktp_print_retweet_info($status) {
	for($c=0; $c<10; $c++) : ?>
        <?php if(!strlen($status[$c]->user->screen_name)) {return;}?>
        <?php //if($counter<=KTP_TWIT_UMAX) : ?>
        <?php $kish_text = kish_twit_pro_add_profile_link(kish_twit_pro_changeToUrl($status[$c]->retweeted_status->text), "retweetinfo-", $status[$c]->user->screen_name); ?>
		<div class="ktp_timeline_box">
		<p class="ktp_p"><img class="ktp_thumb" src="<?php echo $status[$c]->user->profile_image_url; ?>" />
		<?php echo "<a rel=\"nofollow\" href =\"".$status[$c]->user->url."\">".$status[$c]->user->screen_name."</a>-".$kish_text; ?> | <?php echo kish_twit_pro_format_date($status[$c]->retweeted_status->created_at); ?> | <?php echo $status[$c]->source; ?></p>
		<div style="clear:both"></div>
				<?php if(ktpoath()) { ?>
                <div class="ktp_action_panel">
                	 <div style="width:98%;float:left;height:22px;display:inline-block">
                        <div class="ktp_action_panel_links">
                            <?php //kish_twit_pro_print_user_profile($status[$c]->user->screen_name, $status[$c]->user->id, "retweetinfo-"); ?>
                            <?php kish_twit_pro_print_user_profile_retrive($status[$c]->user->screen_name, $status[$c]->user->id, "myfollow-"); ?>
                        </div>
                        <div class="ktp_action_panel_links">
                            <?php kish_twit_pro_follow_user_link($status[$c]->user->screen_name, $status[$c]->user->id, 'retweetinfo-'); ?>
                        </div>
                    </div>
				</div> 
                <div style="display:none" id="profinfo-<?php echo $status[$c]->user->id; ?>">
                	<?php //kish_twit_pro_print_profile_hidden_rt_users($status, $c); ?>
                </div>  
				<?php } ?> 
			</div>
			<div style="clear:both"></div>
         	<?php $counter++; ?>
         	<?php //endif; ?>
		<?php endfor; 
}
// My Followers
function kish_twit_pro_print_follow_me($user='', $cursor='', $page=1) {
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	if(!strlen($user)) {
		if(!empty($ktproselectedaccount)) {
			$ktpoauthmode ? $user= $ktproselectedaccount[0]->ktpro_screen_name : $user=KTP_TWIT_UNAME;
		}
		else {
			$ktpoauthmode ? $user= KTP_TWIT_COOK_SCREEN_NAME : $user=KTP_TWIT_UNAME;
		}
	}
	$data=array();
	$filename = KTP_XML_CACHE.'/'.$user.'-followers.json';
	if(KTP_TWIT_CACHE_ENABLED && $page>1) {
		//print "File name is ".$filename."<br>";
		$data = kish_twit_read_json($filename);	
		//echo "Data from Cache<br>";
		//print_r($data);
	}
	if(!empty($data)) {
		//echo "Data is not empty from cache so getting it..<br>";
		kish_twit_pro_statusus_tl($data, 'My Followers', 'followmepage&user='.$user, 'kpt_container_2', 'page-progress', $page);
	}
	else {
		//echo "Data was empty so getting fresh ones..<br>";
		!strlen($page) ? $page=1 : $page=$page;
		if(!strlen($user)) {
			$ktpoauthmode ? $user= $ktproselectedaccount[0]->ktpro_screen_name : $user=KTP_TWIT_UNAME;
		}
		!strlen($cursor)?$cursor=-1:$cursor=$cursor;
		$options = array('screen_name' => $user, 'cursor' => $cursor);
		$data = $ktproauth->get('statuses/followers', $options);
		if(KTP_TWIT_CACHE_ENABLED && $page==1) {
			kish_twit_write_json($data, $filename);
			//echo "Cache enabled, so saving new data to cache..<br>";
		}
		kish_twit_pro_statusus_tl($data, ucwords($user).'s Followers', 'followmepage&user='.$user, 'kpt_container_2', 'page-progress', $page);
	}
}
// Who am I Following
function kish_twit_pro_print_i_follow($user='', $cursor='', $page=1) {
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	if(!strlen($user)) {
		if(!empty($ktproselectedaccount)) {
			$ktpoauthmode ? $user= $ktproselectedaccount[0]->ktpro_screen_name : $user=KTP_TWIT_UNAME;
		}
		else {
			$ktpoauthmode ? $user= KTP_TWIT_COOK_SCREEN_NAME : $user=KTP_TWIT_UNAME;
		}
	}
	$data=array();
	$filename = KTP_XML_CACHE.'/'.$user.'-following.json';
	if(KTP_TWIT_CACHE_ENABLED && $page>1) {
		//print "File name is ".$filename."<br>";
		$data = kish_twit_read_json($filename);	
		//echo "Data from Cache<br>";
		//print_r($data);
	}
	if(!empty($data)) {
		echo "Data is not empty from cache so getting it..<br>";
		kish_twit_pro_statusus_tl($data, 'My Followers', 'followmepage&user='.$user, 'kpt_container_2', 'page-progress', $page);
	}
	else {
		//echo "Data was empty so getting fresh ones..<br>";
		!strlen($page) ? $page=1 : $page=$page;
		if(!strlen($user)) {
			$ktpoauthmode ? $user= $ktproselectedaccount[0]->ktpro_screen_name : $user=KTP_TWIT_UNAME;
		}
		!strlen($cursor)?$cursor=-1:$cursor=$cursor;
		$options = array('screen_name' => $user, 'cursor' => $cursor);
		$data = $ktproauth->get('statuses/friends', $options);
		if(KTP_TWIT_CACHE_ENABLED && $page==1) {
			kish_twit_write_json($data, $filename);
			//echo "Cache enabled, so saving new data to cache..<br>";
		}
		kish_twit_pro_statusus_tl($data, 'I am Following', 'ifollowpage&user='.$user, 'kpt_container_2', 'page-progress', $page);
	}
}
// Featured Users
/*
function kish_twit_pro_print_featured_users() {
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	kish_twit_pro_statusus_tl(kish_twit_pro_api()->getFeaturedUsers('json'), 'Currently Featured Users', 'featuredusers', 'kpt_container_2', 'page-progress');
}*/
function kish_twit_pro_check_rel_link($targetuser, $sourceuser='') { ?>
	<?php $r=rand(1000,99999); ?>
	<div id="check-rel-<?php echo $r."_".$targetuser; ?>"><a href="#" onclick="kish_twit_pro_process_ajax_progress('req=checkrel&targetuser=<?php echo $targetuser; ?>&sourceuser=<?php echo $sourceuser; ?>', 'check-rel-<?php echo $r."_".$targetuser; ?>', 'check-rel-<?php echo $r."_".$targetuser; ?>', '<?php echo KTP_LOADER_1; ?>'); return false;">Check Relation</a></div>
<?php
}
function kish_twit_pro_check_rel($targetuser, $sourceuser='', $profrequest=false) {
	if(!strlen($targetuser)) {echo "Target user not specified.."; return;}
		global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
		if(!strlen($sourceuser)) {
			if(!empty($ktproselectedaccount)) {
				$ktpoauthmode ? $user= $ktproselectedaccount[0]->ktpro_screen_name : $user=KTP_TWIT_UNAME;
			}
			else {
				$ktpoauthmode ? $user= KTP_TWIT_COOK_SCREEN_NAME : $user=KTP_TWIT_UNAME;
			}
		}
		if(KTP_TWIT_PRO_CHECK_REL || $profrequest) {
		$options=array('source_screen_name'=>$user, 'target_screen_name'=>$targetuser);
		$followme=false;
		$follow=false;
		$status = $ktproauth ? $ktproauth->get('friendships/show', $options):kish_twit_pro_api()->friendshipExists($options, 'json');
		//print_r($status);
		//echo "Is target following - ".$status['relationship']['target']['following']; 
		if($status->relationship->source->following && $status->relationship->source->followed_by) {
			$relation = '<li style="color:#066E1C"><strong>Relation : You both are following each other</strong></li>';
			$follow=true;
			$followme=true;
		}
		else if($status->relationship->source->following && !$status->relationship->source->followed_by) {
			$relation ='<li style="color:#CA0000"><strong>Relation : You are following, not @'.$targetuser.'!!</strong></li>';
			$follow=true;
		}
		else if(!$status->relationship->source->following && !$status->relationship->source->followed_by) {
			$relation ='<li style="color:#CA0000"><strong>Relation : You Guys are new to each other</strong></li>';
		}
		else if(!$status->relationship->source->following && $status->relationship->source->followed_by) {
			$relation='<li style="color:#0948CC"><strong>Relation : You are not following @' .$targetuser.'</strong></li>';
			$followme=true;
		}
		$follow ? kish_twit_pro_unfollow_link($targetuser, $targetuser, 'dmresponse-') : kish_twit_pro_follow_user_link($targetuser, $targetuser, 'dmresponse-');
		if($followme) {
			echo "<div><input onkeypress=\"if(event.keyCode==13) document.getElementById('dmbutton').click()\" type =\"text\" id=\"dmbox\" style=\"width:75%;float:left;padding:2px;margin:2px 2px 2px 5px;\" value=\"Send a Direct Message...\" onfocus=\"if (this.value == 'Send a Direct Message...') {this.value = '';}\" onblur=\"if (this.value == '') {this.value = 'Send a Direct Message...'; }\">"; 
	   		echo "<input class=\"button-secondary\" type=\"button\" id=\"dmbutton\" value=\"Ok\" onclick = \"if(kish_twit_pro_getVar('dmbox')=='' || kish_twit_pro_getVar('dmbox')=='Send a Direct Message...') {alert('Please enter your message'); return false; }; kish_twit_pro_process_ajax_msg('req=ktpsenddm&newmsg=' + kish_twit_pro_getVar('dmbox') + '&ok=yes&user=".$targetuser."', 'dmresponse-".$targetuser."', 'dmresponse-".$targetuser."', '".KTP_LOADER_1."', 'DM Sent...');kish_twit_pro_resetTbox('Send a Direct Message...', 'dmbox')\">";
		}
	   else {
			echo "<div><input onkeypress=\"if(event.keyCode==13) document.getElementById('dmbutton').click()\" type =\"text\" id=\"dmbox\" style=\"width:75%;float:left;padding:2px;margin:2px 2px 2px 5px;\" value=\"Send a Message...\" onfocus=\"if (this.value == 'Send a Message...') {this.value = '';}\" onblur=\"if (this.value == '') {this.value = 'Send a Message...'; }\">"; 
	   		echo "<input class=\"button-secondary\" type=\"button\" id=\"dmbutton\" value=\"Ok\" onclick = \"if(kish_twit_pro_getVar('dmbox')=='' || kish_twit_pro_getVar('dmbox')=='Send a Message...') {alert('Please enter your message'); return false; }; kish_twit_pro_process_ajax_msg('req=twitupdatestatuspage&newmsg=@".$targetuser." ' + kish_twit_pro_getVar('dmbox') + '&ok=yes&user=".$targetuser."',  'dmresponse-".$targetuser."', 'dmresponse-".$targetuser."','".KTP_LOADER_1."', 'Msg Sent..');kish_twit_pro_resetTbox('Send a Message...', 'dmbox')\">"; 
	   }
	   echo "<div id =\"dmresponse-".$targetuser."\"></div>"; 
	   kish_twit_pro_print_user_latest_twits($targetuser, 'kpt_container_2', "dmresponse-".$targetuser);
		return $relation;
	}
	else {
		kish_twit_pro_check_rel_link($targetuser, $sourceuser);
	}
}
function kish_twit_pro_changeToUrl($str) {
	$flag=true;
	// storing the original text
	$tempStr = $str;
	for($i=0 ; $flag==true; $i++) {
		//Getting the first http position, I checking if any text begins with you can modify this according to your need
		$httpos = strpos($tempStr, 'http');
		//checking if any more http available in the text
		if( $httpos === false) { $flag=false; return $str; }
		if($flag) {
			// Removing the text before the http position
			$afterurl = substr($tempStr, $httpos);
			// Getting the space postion after the url which means the end of the URL
			$gapos = strpos($afterurl, " ");
			($gapos===false) ? $finalurl =trim($afterurl):$finalurl = substr(trim($afterurl), 0, $gapos);
			// Getting the URL text for which we need to replace with the <a> tag
			// Getting the length of the URL
			$lengthOfUrl = strlen($finalurl);
			//Getting the text removing the URL that is to scan for the next URL	
			$tempStr = substr($afterurl,$lengthOfUrl);
			// This will replace the URL adding the <a> tag
			$replace = '<a target = _blank href ='.$finalurl.'>'.$finalurl.'</a>';
			// replacing the URL with <a> tag
			$includelink = str_replace($finalurl, $replace, $str);
			// Saving the new string after replacing the URL with <a> tag and goes to the next URL
			$str=$includelink;
		}
	}
	return $includelink;
}

function kish_twit_pro_update_status($message, $replytoid='') {
echo $message;
	global $ktproauth, $ktpoauthmode;
	$message=stripslashes($message);
	if(strlen($message)>139) {
		$pattern="@(http(s)?://([\w-]+\.)+[\w-]+(/[\w- ./?%&=]*)?)@is"; 
		preg_match($pattern, $message, $matches);
		$url = $matches[1];
		if(strlen($url)) {
			if(strpos($url, 'http://is.gd')!==false) {
				$surl = $url;
			}
			else {
				$surl=kish_twit_pro_shorturl($url);
			}
		}
		$shorturlen=strlen($surl);
		$balpos=135-$shorturlen;
		$msgwithouturl=str_replace($matches[1], "", $message);
		$message=substr($msgwithouturl, 0, $balpos);
		$message=$message." - ". $surl;
		//$message=str_replace($matches[1], $surl, $message);
	}
	$options=array();
	strlen($replytoid) ? $options=array('status' => $message, 'in_reply_to_status_id'=>$replytoid) : $options=array('status' => $message);
	$data=  $ktproauth->post('statuses/update', $options);
	return $data;
}
function kish_twit_pro_print_footer() { ?>
<div style="clear:both;"></div>
    <div class="ktp_footer">
        <div class="ktp_footer_left">&copy;<a href="http://www.asokans.com">Kishore Asokan</a> | Powered By <a href="http://www.twitter.com">Twitter</a></div>
        <div class="ktp_footer_right">You can follow me @kishtweet</div>
    </div>
    <div style="height:100px; overflow:auto;width:100%" id="ktp_debug" ondblclick="kish_twit_pro_clear('ktp_debug')"></div>
<?php }
// LOGIN ETC
function kish_twitter_oauth() {
	if (strlen(KTP_TWIT_COOK_TOKEN) && strlen(KTP_TWIT_COOK_SECRET)) {
		require_once('oauth/twitteroauth/twitteroauth.php');
		require_once('oauth/config.php');
		return new TwitterOAuth(KTP_CONSUMER_KEY, KTP_CONSUMER_SECRET, KTP_TWIT_COOK_TOKEN, KTP_TWIT_COOK_SECRET);
	}
	else {return false;}
}
function ktp_check_acc_added() {
	global $current_user;
	$loggedin_user = $current_user->user_login;
	if(strlen($loggedin_user)) {
		$accounts = kish_twit_pro_get_saved_twit_accounts($loggedin_user);
		if($accounts) {
			return true;
		}
		else {
			return false;
		}
	}
}
function ktp_get_saved_accounts_as_string() {
	global $current_user;
	$loggedin_user = $current_user->user_login;
	if(strlen($loggedin_user)) {
		$accounts = kish_twit_pro_get_saved_twit_accounts($loggedin_user);
		if($accounts) {
			$counter=0;
			foreach($accounts as $account) :
				if($counter==0) {
					$accnamestring=$account->ktpro_screen_name;
					$accidstring=$account->ktpro_id;
				}
				else {
					$accnamestring.=",".$account->ktpro_screen_name;
					$accidstring.=",".$account->ktpro_id;
				}
			$counter++;
			endforeach;
		}
		return array('siteid'=>$accidstring, 'sitename'=>$accnamestring);
	}
	else {
		return $accstring;
	}
}
function kish_twit_pro_print_account_buttons() {
	?>
	<input type="button" value="Upgrade to Pro Version and add multiple twitter accounts &raquo;" class="button-secondary" onclick="window.location = 'http://kishpress.com/kish-multi-pro/'">
	<?php
}
function ktpoath($accountid='') {
	global $ktproauth, $ktpoauthmode, $current_user, $wpdb, $ktproselectedaccount, $listdata;
	$loggedin_user = $current_user->user_login;
	if(strlen($loggedin_user) && KTP_SAVE_LOGIN) {
		strlen($accountid) ? $accounts = kish_twit_pro_get_saved_acc_details($accountid) : $accounts = kish_twit_pro_get_saved_twit_accounts($loggedin_user);
		if($accounts) {		
			if(strlen($accounts[0]->ktpro_oauth_token) && strlen($accounts[0]->ktpro_oauth_token_secret)) {
				require_once('oauth/twitteroauth/twitteroauth.php');
				//require_once('oauth/config.php');
				if(strlen(KTP_CONSUMER_KEY) && strlen(KTP_CONSUMER_SECRET)) {
					$ktproauth = new TwitterOAuth(KTP_CONSUMER_KEY, KTP_CONSUMER_SECRET, $accounts[0]->ktpro_oauth_token, $accounts[0]->ktpro_oauth_token_secret);
					$ktpoauthmode = true;
					$ktproselectedaccount=$accounts;
					return true;
				}
				else {
					return false;
				}
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}
	else {
		if (strlen(KTP_TWIT_COOK_TOKEN) && strlen(KTP_TWIT_COOK_SECRET)) {
			require_once('oauth/twitteroauth/twitteroauth.php');
			//require_once('oauth/config.php');
			$ktproauth = new TwitterOAuth(KTP_CONSUMER_KEY, KTP_CONSUMER_SECRET, KTP_TWIT_COOK_TOKEN, KTP_TWIT_COOK_SECRET);
			$ktpoauthmode = true;
			//$listdata = $ktpoauthmode ? $ktproauth->get(KTP_TWIT_COOK_SCREEN_NAME.'/lists', $options):kish_twit_pro_api()->getLists(KTP_TWIT_COOK_SCREEN_NAME, 'json');
			$ktproselectedaccount=array();
			return true;
		}
		else {

			$ktproselectedaccount=array();
			$ktpoauthmode=false;
			return false;
		}
	}
}
function kish_twit_pro_save_new_twitter_login($oauth_token, $oauth_token_secret, $user_id, $screen_name) {
	// if the user if logged in save
	global $current_user, $wpdb;
	require_once (ABSPATH . WPINC . '/pluggable.php');
	kish_twit_pro_delete_account();
	get_currentuserinfo();
	$loggedin_user = $current_user->user_login;
	
	if(strlen($loggedin_user)) {
		$sql = "SELECT ktpro_wp_id FROM ".$wpdb->prefix."ktpro_users WHERE ktpro_screen_name = '".$screen_name."' LIMIT 1";
		//echo $sql;
		$results=$wpdb->get_results($sql, OBJECT);
		if($results) {
			//echo "Login already saved";
			if($results[0]->ktpro_wp_id!=$loggedin_user) {
				//echo "This Twitter Login is already added to a different a different login - ".$results[0]->ktpro_wp_id ;
			}
		}
		else {
			if(strlen($oauth_token)) {
				$sql = "INSERT INTO ".$wpdb->prefix."ktpro_users (ktpro_wp_id, ktpro_oauth_token, ktpro_oauth_token_secret, ktpro_user_id, ktpro_screen_name) VALUES('".$loggedin_user."', '".$oauth_token."', '".$oauth_token_secret."', '".$user_id."', '".$screen_name."')";
				$wpdb->get_results($sql, OBJECT);
			}
			else {
				echo "KTP_TWIT_COOK_TOKEN not saved";
				exit;
			}
		}
		//kish_twit_pro_clear_cookies();
	}
}
function kish_twit_pro_clear_cookies() {
	global $ktproauth, $ktpoauthmode, $ktproselectedaccount;
	$ktproselectedaccount=array();
	$ktpoauthmode=false;
	$ktproauth='';
	$ktp_domain=str_replace('http://', "",KTP_WP_URL);
	$ktp_domain=str_replace('www', "",$ktp_domain);
	if(isset($_COOKIE["kish_twit_oauth_token"])) { setcookie("kish_twit_oauth_token", "", time()-3600,"/", $ktp_domain); }
	if(isset($_COOKIE["kish_twit_oauth_token_secret"])) { setcookie("kish_twit_oauth_token_secret",  "", time()-3600,"/", $ktp_domain); }
	if(isset($_COOKIE["kish_twit_user_id"])) { setcookie("kish_twit_user_id", "", time()-3600,"/", $ktp_domain); }
	if(isset($_COOKIE["kish_twit_screen_name"])) { setcookie("kish_twit_screen_name",  "", time()-3600,"/", $ktp_domain); }
}
function ktp_get_domain() {
	$ktp_domain=str_replace('http://', "",KTP_WP_URL);
	return str_replace('www', "",$ktp_domain);
}
function kish_twit_pro_save_cookies($at, $as, $uid, $sn) {
	$ktp_domain=str_replace('http://', "",KTP_WP_URL);
	$ktp_domain=str_replace('www', "",$ktp_domain);
	setcookie("kish_twit_oauth_token", $at, time()+33600 ,"/", $ktp_domain );
	setcookie("kish_twit_oauth_token_secret", $as, time()+33600,"/", $ktp_domain);
	setcookie("kish_twit_user_id", $uid, time()+33600,"/", $ktp_domain);
	setcookie("kish_twit_screen_name", $sn, time()+33600,"/", $ktp_domain);
}
function kish_twit_pro_delete_account() {
	global $wpdb;
	$sql = "DELETE FROM ".$wpdb->prefix."ktpro_users"; 
	mysql_query($sql, $wpdb->dbh);
}
function kish_twit_pro_get_saved_twit_accounts($wpusername) {
	global $wpdb;
	$sql = "SELECT * FROM ".$wpdb->prefix."ktpro_users WHERE ktpro_wp_id = '".$wpusername."' ORDER BY ktpro_id"; 
	return $wpdb->get_results($sql, OBJECT);
}
function kish_twit_pro_get_saved_acc_details($accountid) {
	global $wpdb;
	$sql = "SELECT * FROM ".$wpdb->prefix."ktpro_users WHERE ktpro_id = ".$accountid." LIMIT 1"; 
	return $wpdb->get_results($sql, OBJECT);
}
function ktp_is_gd_shorturl( $url ){
		$url = 'http://is.gd/api.php?longurl=' . $url;
		if(function_exists('curl_init')) {	
			$ch = curl_init();	
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_GET, true);
			$shorturl=curl_exec($ch);
		
			$Headers = curl_getinfo($ch);
			curl_close($ch);
								
			if($Headers['http_code'] == 200) {
				return $shorturl;
			} 
			else{
				echo "error";
			}//Check Response
		} 
		else {
			echo "Curl Lib not installed";
		}//CURL Library installed	
	}
?>