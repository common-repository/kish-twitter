<?php
/*
Plugin Name: Kish Twit 
Plugin URI: http://kishpress.com/kish-twit-pro/
Description: This plugin is using the Twitter oauth API. You need to register your own application to use this plugin 
Version:3.0
Author: Kishore Asokan
Author URI: http://www.asokans.com 
*/

/*  Copyright 2008  Kishore Asokan  (email : kishore@asokans.com) All rights reserved
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 3 of the License, or
	(at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

$ktproroot = str_replace("\\", "/", dirname(__FILE__));
include_once($ktproroot.'/functions.php');
global $ktproauth, $ktpoauthmode, $ktproselectedaccount,$ktpgetuser,$kishpostid, $ktprequser, $ktpreqsearch, $tuser, $ktptags;

if( function_exists('register_activation_hook') ) {
	register_activation_hook(__FILE__,"kish_twit_pro_install");
}
if( function_exists('add_action') ) {
	add_action('wp_head', 'kish_twit_pro_add_header'); 
	add_action('init', 'kish_twit_pro_init_method');
	add_action('admin_menu', 'kish_twit_pro_add_admin');
	if(KTP_AUTO_TWEET) {
		add_action('publish_post', 'kish_twit_pro_auto_tweet');
	}
}
?>