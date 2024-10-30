<?php
$ktproroot = str_replace("\\", "/", dirname(__FILE__));
include_once($ktproroot.'/functions.php');
global $ktproauth, $ktpoauthmode, $ktproselectedaccount,$ktpgetuser,$kishpostid, $ktprequser, $ktpreqsearch, $tuser, $ktptags;
if(strlen($_POST['accountid'])) {
	global $ktproselectedaccount;
	$ktproselectedaccount = kish_twit_pro_get_saved_acc_details($_POST['accountid']);
	ktpoath($_POST['accountid']);
}
//ktpoath($_POST['account']);
if($_POST['req']=='updatesettings') {
	if(current_user_can('edit_users')) {
		$kish_twit_pro_options=array("ktpro_relno",  "ktpro_max_status", "ktpro_cons_key", "ktpro_cons_secret", "kish_twitter_userinfo", "ktpro_cache_enabled", "ktpro_theme", "ktpro_autoscroll_enabled", "ktpro_autotweet_enabled", "ktpro_pop_search_enabled", "ktpro_debug_mode");
			if($_POST['y'] == 'ewerer') {			
				foreach($kish_twit_pro_options as $o) {	
					if(isset( $_POST[$o]) ) {
						$val = $_POST[$o];
						update_option($o, $val);
					}	
				}
			}
		echo "<p><strong>Please Reload the page as some settings will be active only on page reload..</strong></p>";
		kish_twit_pro_settings_panel_only_settings();
	}
}
if($_POST['req']=='updateprofile') {
	global $user_ID, $tuser;
	get_currentuserinfo();
	$kish_twit_pro_profile=array("user_email");
		if($_POST['y'] == 'ewerer') {			
			foreach($kish_twit_pro_profile as $o) {	
				if(isset( $_POST[$o]) ) {
					$val = $_POST[$o];
					update_usermeta($tuser->ID, $o, $val);
				}	
			}
		}
	if(strlen(KTP_TWIT_COOK_SCREEN_NAME)){
		update_usermeta($user_ID, 'ktp_sn', KTP_TWIT_COOK_SCREEN_NAME);
	}
	kish_twit_pro_user_profile_page_2();
}
if(ktpoath($_POST['accountid'])) {
	if($_POST['req']=='sidebarmenu') {
		kish_twit_pro_print_sidebar_menu_links();
	}
	else if($_POST['req']=='delaccount') {
		kish_twit_pro_delete_account($_POST['accountid']);
		kish_twit_pro_print_account_buttons();
	}
	else if($_POST['req']=='retweet') {
		if(strlen($_POST['postid'])) {
			kish_post_tweet_as_comment($_POST['postid'], $_POST['tweetid']);
		}
		else {
			kish_twit_pro_retwit_twit($_POST['tweetid'], $_POST['mytags']);
		}
	}
	else if($_POST['req']=='ktptwitupdatestatus') {
		kish_twit_pro_update_status($_POST['newmsg'], $_POST['inreplyto'], $_POST['wppost']); 
	}
	else if($_POST['req']=='replyform') {
		kish_twit_pro_print_reply_update_box_pop($_POST['twitid'], $_POST['touser']);
	}
	else if($_POST['req']=='twitupdatestatuspop') {
		if($_POST['newmsg']) {
			$status=kish_twit_pro_update_status($_POST['newmsg']); 
			kish_twit_pro_print_single_status($status);
			//kish_twit_pro_post_wp_2($status);
		}
	}
	else if($_POST['req']=='twitupdatestatuspost') {
		if($_POST['newmsg']) kish_twit_pro_auto_tweet($_POST['pid']); 
	}
	else if($_POST['req']=='twitupdatestatusnopost') {
		kish_twit_pro_tweet_the_post($_POST['pid']);  
	}
	else if($_POST['req']=='twitupdatestatuspage') {
		if($_POST['newmsg']) kish_twit_pro_update_status($_POST['newmsg']); 
		//printuserTimeLinePage(); 
		//kish_twit_pro_print_user_tl();
	}
	else if($_POST['req']=='ktpsenddm') {
		if($_POST['newmsg']) kish_twit_pro_send_new_dm($_POST['user'], $_POST['newmsg']); 
		//printuserTimeLinePage(); 
	}
	else if($_POST['req']=='ktpdeltweet') {
		kish_twit_pro_del_twit($_POST['tweetid']); 
	}
	else if($_POST['req']=='checkrel') {
		//echo "I am in";
		echo kish_twit_pro_check_rel($_POST['targetuser'], $_POST['sourceuser'], true);
	}
	else if($_POST['req']=='markfav') {
		kish_twit_pro_create_fav($_POST['twitid']); 
	}
	else if($_POST['req']=='removefav') {
		kish_twit_pro_destroy_fav($_POST['twitid']); 
	}
	else if($_POST['req']=='retweet') {
		kish_twit_pro_retwit_twit($_POST['tweetid']); 
		//kish_twit_pro_print_user_tl(); 
	}
	else if($_POST['req']=='follownewuser') {
		kish_twit_pro_follow_new_user($_POST['user']); 
		//kish_twit_pro_print_i_follow();
	}
	else if($_POST['req']=='unfollowuser') {
		kish_twit_pro_unfollow_new_user($_POST['user']); 
		//kish_twit_pro_print_i_follow();
	}
	else if($_POST['req']=='subscribelist') {
		kish_twit_pro_subscribe_new_list($_POST['user'],$_POST['listid']); 
		//kish_twit_pro_print_i_follow();
	}
	else if($_POST['req']=='unsubscribelist') {
		kish_twit_pro_unsubscribe_list($_POST['user'], $_POST['listid']); 
		//kish_twit_pro_print_i_follow();
	}
	else if($_POST['req']=='showktppageadmin') {
		kish_twit_pro_print_twitter_page();
	}
}
// End Actions
if($_POST['req']=='sidebartrends') {
	kish_twit_pro_print_daily_trends();
}
if($_POST['req']=='ktpsettingspage') {
	kish_twit_pro_print_settings_form();
}
if($_POST['req']=='ktprefreshdebug') {
	ktp_debug_panel();
}
if($_POST['req']=='delsavedsearch') {
	ktp_save_del_saved_search($_POST['id']);
}
if($_POST['req']=='savedsearches') {
	kish_twit_pro_print_saved_searches($_POST['user']);
}
if($_POST['req']=='savednewsearch') {
	ktp_save_new_search($_POST['query'], $_POST['user']);
}
else if($_POST['req']=='ktptools') {
	kish_twit_pro_print_bookmarklet();
}
else if($_POST['req']=='profmultimedia') {
	printUserProfileOtherOauth_multimedia($_POST['user']);
}
else if($_POST['req']=='ktpgetUpdateBox') {
	echo "check the first function";
	//kish_twit_pro_print_update_status_box(); 
}
else if($_POST['req']=='shortenurl') {
	kish_twitter_shorturl($_POST['url']); 
	//echo "Twitted";
}
else if($_POST['req']=='ktpshowTwitStatus') {
	//printTwitterUpdates(); 
}
else if($_POST['req']=='ktpaccstatus') {
	kish_twit_pro_print_acc_status(); 
}
else if($_POST['req']=='ktppublictimeline') {
	kish_twit_pro_print_public_tl(); 
}
else if($_POST['req']=='mentions') {
	kish_twit_pro_print_user_mentions($_POST['user'], $_POST['max_id'], $_POST['since_id'], $_POST['count']); 
}
else if($_POST['req']=='dm') {
	kish_twit_pro_print_dm_page(); 
}
else if($_POST['req']=='dmsent') {
	kish_twit_pro_print_dm_sent($_POST['user'], $_POST['max_id'], $_POST['since_id'], $_POST['count']); 
}
else if($_POST['req']=='favorites') {
	kish_twit_pro_print_user_favs($_POST['user'], $_POST['page']); 
}

else if($_POST['req']=='myreplies') {
	kish_twit_pro_print_user_relies($_POST['user'], $_POST['max_id'], $_POST['since_id'], $_POST['count']); 
}
else if($_POST['req']=='refreshprofile') {
	kish_twit_pro_print_profile_infobox(); 
}

else if($_POST['req']=='ktpfriendstimeline') {
	kish_twit_pro_print_following_tl($_POST['user'], $_POST['max_id'], $_POST['since_id'], $_POST['count']); 
}
else if($_POST['req']=='retweetsbyme') {
	kish_twit_pro_print_retweets_by_me_tl($_POST['user'], $_POST['max_id'], $_POST['since_id'], $_POST['count']); 
}
else if($_POST['req']=='retweetsofme') {
	kish_twit_pro_print_retweets_of_me_tl($_POST['user'], $_POST['max_id'], $_POST['since_id'], $_POST['count']); 
}
else if($_POST['req']=='retweetstome') {
	kish_twit_pro_print_retweets_to_me_tl($_POST['user'], $_POST['max_id'], $_POST['since_id'], $_POST['count']); 
}
else if($_POST['req']=='lists') {
	kish_twit_pro_print_saved_lists($_POST['user'],$_POST['cursor']); 
}
else if($_POST['req']=='subscribedlists') {
	kish_twit_pro_print_lists_subscribed_by_user($_POST['user'],$_POST['cursor']); 
}
else if($_POST['req']=='listmembers') {
	kish_twit_pro_print_list_members($_POST['user'],$_POST['listid'],$_POST['cursor']);
}
else if($_POST['req']=='listsubscribers') {
	kish_twit_pro_print_list_subscribers($_POST['user'],$_POST['listid'],$_POST['cursor']);
}
else if($_POST['req']=='liststl') {
	kish_twit_pro_print_list_tl($_POST['user'],$_POST['listid'], $_POST['max_id'], $_POST['since_id'], $_POST['count']);
}
else if($_POST['req']=='addnewmembertolist') {
	//kish_twit_pro_update_status('Tesing the list update API feature'); 
	kish_twit_pro_add_member_to_list($_POST['member'],$_POST['listid']);
}
else if($_POST['req']=='removememberfromlist') {
	//kish_twit_pro_update_status('Tesing the list update API feature'); 
	kish_twit_pro_remove_member_from_list($_POST['member'],$_POST['listid']);
}
else if($_POST['req']=='savenewlist') {
	//kish_twit_pro_update_status('Tesing the list update API feature'); 
	kish_twit_pro_create_new_list($_POST['name'],$_POST['mode'], $_POST['description']);
}
else if($_POST['req']=='editsavedlist') {
	//kish_twit_pro_update_status('Tesing the list update API feature'); 
	kish_twit_pro_edit_saved_list($_POST['listid'],$_POST['name'],$_POST['mode'], $_POST['description']);
}
else if($_POST['req']=='whoifollow') {
	printFollowingUsers(); 
}
else if($_POST['req']=='retweetinfo') {
	kish_twit_pro_retweeted_by($_POST['tweetid']);
}
else if($_POST['req']=='ifollowpage') {
	//ktpoath($_POST['account']);
	kish_twit_pro_print_i_follow($_POST['user'], $_POST['cursor'], $_POST['page']); 
}
else if($_POST['req']=='followmepage') {
	//ktpoath($_POST['account']);
	kish_twit_pro_print_follow_me($_POST['user'], $_POST['cursor'], $_POST['page']); 
}
else if($_POST['req']=='thisuserlatesttweets') {
	kish_twit_pro_print_this_user_latest_twits($_POST['user'], $_POST['max_id'], $_POST['since_id'], $_POST['count']); 
}
else if($_POST['req']=='nearby') {
	kish_twit_pro_search_nearby(); 
}
else if($_POST['req']=='kishsearch') {
	if(substr($_POST['q'], 0,1)=='@') {
		kish_twit_pro_print_this_user_latest_twits(substr($_POST['q'], 1), "", "", "", $_POST['postid']); 
	}
	else {
		kish_twit_pro_search_api($_POST); 
		//kish_twit_pro_search_api(array('q'=>$_POST['q']));
	}
}
else if($_POST['req']=='myconversations') {
	ktp_conversations();
}
else if($_POST['req']=='kusearch') {
	if(substr($_POST['q'], 0,1)=='@') {
		kish_twit_pro_print_this_user_latest_twits(substr($_POST['q'], 1), "", "", "", $_POST['postid']); 
	}
	else {
		kish_twit_pro_print_this_user_latest_twits($_POST['q'], "", "", "", $_POST['postid']); 
	}
}
else if($_POST['req']=='relatedtweets') {
	kish_twit_pro_related_tweets($_POST['pid']); 
}

else if($_POST['req']=='moreinfo') {
	//kish_twit_pro_print_user_profile_inline($_POST['user'], $_POST['divid']); 
	printUserProfileOtherOauth($_POST['user'],$_POST['divid'] );
}

else if($_POST['req']=='ktpshowTwitStatusPage') {
	kish_twit_pro_print_user_tl($_POST['user'], $_POST['max_id'], $_POST['since_id'], $_POST['count']); 
}
else if($_POST['req']=='public') {
	//printTwitterPublicTimeLine(); 
}
else if($_POST['req']=='followsay') {
	//printTwitterFollowingTimeLine(); 
}
else if($_POST['req']=='changeaccount') {
	//kish_twit_pro_print_following_tl();  
}
?>