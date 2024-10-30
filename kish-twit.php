<?php
/*
Plugin Name: Kish Twitter
Plugin URI: http://kish.in/ajax-wordpress-twitter-plugin/
Description: This plugin is using the Twitter API. You can update your status, View your followers, Read what your followers Say, Read Public Updates and more. This is using AJAX, so there is no page reloading. You can customize the widget, color, font, font size, disable and enable features all you need is to add one line to your template <?php if (function_exists('printKishTwitter')) printKishTwitter(); ?> 
Version:1.3
Author: Kishore Asokan
Author URI: http://www.kisaso.com 
*/

/*  Copyright 2008  Kishore Asokan  (email : kishore@asokans.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
$kroot = str_replace("\\", "/", dirname(__FILE__));
include_once($kroot.'/functions.php');
if($_POST['req']=='getUpdateBox') {
	printtwitterUpdateStatusBox(); 
}
else if($_POST['req']=='twitupdatestatus') {
	if($_POST['newmsg']) updateTwitterStatus($_POST['newmsg']); 
	printTwitterUpdates(); 
	//print "mESSAGE IS ".$_POST['newmsg'];
}
else if($_POST['req']=='showTwitStatus') {
	printTwitterUpdates(); 
}
else if($_POST['req']=='whoifollow') {
	printFollowingUsers(); 
}
else if($_POST['req']=='followme') {
	printFollowMe(); 
}
else if($_POST['req']=='public') {
	printTwitterPublicTimeLine(); 
}
else if($_POST['req']=='followsay') {
	printTwitterFollowingTimeLine(); 
}
if( function_exists('register_activation_hook') ) {
	register_activation_hook(__FILE__,"kish_twitter_install");
}
if( function_exists('add_action') ) {
		add_action('wp_head', 'addHeaderCodeKishTwitter'); 
		add_action('admin_menu', 'kish_twitter_add_admin');		
		add_action("plugins_loaded", "kishTwitterWidget_init");
		add_action('wp_head', 'kish_twitter_style');
		//add_filter('the_content', 'kish_twit_search_the_content');
}
define('KTWITTER_RIGHTS', "<p style=\"font-size:10px; margin-top:-5px;\"><span style=\"float:left;font-size:12px;\"><a href = \"http://www.twitter.com/".get_option('kish_twitter_username')."\">Follow me on Twitter</span><span style=\"float:right\"><a target = \"_blank\" href = \"http://www.twitter.com/Asokans\">Powered By Twitter</a> | <a target = \"_blank\" href = \"http://www.kisaso.com/ajax-wordpress-twitter-plugin/\">Twitter Plugin For Wordpress</a></span></p>");

?>