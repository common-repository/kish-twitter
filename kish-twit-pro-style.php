<?php header("Content-type: text/css"); ?>
<?php
     //kish-twitter-style
	$kroot = str_replace("\\", "/", dirname(__FILE__));
	include_once($kroot.'/functions.php');
	global $ktp_theme_dark, $ktp_theme_light;
	$ktptheme=kish_twit_pro_theme();
	$ktp_theme_dark = $ktptheme['dark'];
	$ktp_theme_light = $ktptheme['light'];
?>
<style type="text/css">

.ktp_featured_1 {
	border:1px solid <?php echo $ktp_theme_dark; ?>; 
	padding:2px;
	margin:5px 2px 5px 2px;
	font-size:11px;
}
.ktp_featured_1 p a{
	font-size:11px;
}
.ktp_container {
	font:Georgia, "Times New Roman", Times, serif;
	width:100%;
	border:1px solid <?php echo $ktp_theme_light; ?>;
	float:left;
	padding:4px;
	display:inline-block;
	height:auto;
}
.ktp_container img{
	border:none;
}
.ktp_container a{
	color:<?php echo $ktp_theme_dark; ?>;
}
.ktp_container_top {
	width:100%;
	border:none;
	display:block;
	height:100px;
}
.ktp_login_control {
	width:49%;
	float:left;
	display:inline;
	height:95px;
	border:1px solid <?php echo $ktp_theme_light; ?>;
	font-size:11px;
}
.ktp_info_box {
	width:49%;
	float:right;
	display:inline;
	height:95px;
	border:1px solid <?php echo $ktp_theme_light; ?>;
	font-size:9px;
}
.ktp_info_box p{
	margin:2px 0px 2px 0px;
	font-size:11px;
	padding:2px;
}
.ktp_buttons {
	height:30px;
	display:block;
	margin:2px 0px 2px 0px;
	padding:2px;
	border:1px solid <?php echo $ktp_theme_light; ?>;
}
.ktp_inptut_buttons{
	height:25px;
	padding:2px;
	margin:1px 3px 1px 0px;
	font-size:inherit;
	display:inline-block;
	background: <?php echo $ktp_theme_dark; ?>;
	color:#FFFFFF;
}
input.ktp_inptut_buttons { 
	background: <?php echo $ktp_theme_dark; ?>;
	color:<?php echo $ktp_theme_light; ?>;
} 
.ktp_text_box {
	border: 1px solid <?php echo $ktp_theme_light; ?>;
	width: 95%;
	height:15px;
	margin:2px;
	padding:2px;
}
.ktp_buttons_accounts {
	height:25px;
	width:49%;
	display:inline-block;
	float:left;
}
.ktp_buttons_menu {
	height:25px;
	width:49%;
	display:inline-block;
	float:right;
	font-size:10px;
}
.ktp_buttons_menu_search_box{
	float:left;
	display:inline-block;
	width:70%;
	height:23px;
}
.ktp_search_box{
	width:75%;
	float:left;
	margin:1px 1px 1px 0px;
	border:1px solid <?php echo $ktp_theme_light; ?>;
}
.ktp_search_box_button{
	float:left;
	color:#FFFFFF;
	padding:2px;
	margin:1px;
	display:inline;
	height:25px;
	background: <?php echo $ktp_theme_dark; ?>;
}
.ktp_menu_pro{
	float:right;
	width:25%;
	height:25px;
	display:inline-block;
	margin-right:5px;
}
.ktp_buttons_menu div{
	display:inline-block;
}
.ktp_buttons_menu div img{
	display:inline-block;
	border:none;
}
.ktp_info_box img {

}
.ktp_container a {
	font-weight:500;
}
.kish_twit_page_container {
	height:460x;
	display:block;
	width:66%; 
	border:1px solid <?php echo $ktp_theme_light; ?>;
	display:inline-block;
	float:left;
	margin-top:2px;
}
.ktp_container_1_wrap {
	height:460px;
	display:block;
	width:49%;
	float:left;
	padding:1px;
	margin:2px 2px 2px 2px;
	padding:1px;
	margin:2px 2px 2px 2px;
}
.ktp_container_1_top {
	float:left;
	height:30px;
	display:block;
	padding:2px;
	margin:3px;
	width:99%;
	color:color:<?php echo $ktp_theme_dark; ?>;
	border-top:2px solid <?php echo $ktp_theme_dark; ?>;
	border-bottom:2px solid <?php echo $ktp_theme_dark; ?>;
	font-stretch:extra-expanded;
	font-weight:bold;
}
.ktp_container_1_top_data {
	float:left;
	display:block;
	margin:2px;
	background:<?php echo $ktp_theme_dark; ?>;
	padding:3px;
	font-weight:bold;
	color:#FFFFFF;
	width:95%;
	overflow:hidden; 
}
.ktp_container_1_top_data_home {
	float:left;
	display:block;
	margin:2px;
	background:<?php echo $ktp_theme_dark; ?>;
	padding:3px;
	font-weight:bold;
	color:#FFFFFF;
	width:97%;
	overflow:hidden; 
}
#ktp_container_home {
	width:100%;
	display:inline-block;
	height:445px;
	padding:1px;
	margin:5px 0px 5px 0px;
	margin-bottom:10px;
	border-top:2px solid <?php echo $ktp_theme_dark; ?>;
	border-bottom:1px solid <?php echo $ktp_theme_light; ?>;
	font-size:13px;
}
#ktp_container_home .ktp_action_panel_links {
	font-size:11px;
	height:15px;
	font-weight:bold;
}
#ktp_container_home img{
	margin:4px 4px 4px 0px;
}
.ktp_container_1 {
	height:410px;
	display:block;
	width:98%;
	float:left;
	padding:1px;
	margin:0px 0px 2px 0px;
	padding:1px;
	overflow:auto;
}
.ktp_container_2_wrap {
	height:460px;
	display:block;
	width:49%;
	float:right;
	padding:1px;
	margin:2px 2px 2px 2px;
	padding:1px;
}
.kpt_container_2_top {
	float:left;
	height:30px;
	display:block;
	padding:2px;
	margin:3px;
	width:98%;
	color:<?php echo $ktp_theme_dark; ?>;
	border-top:2px solid <?php echo $ktp_theme_dark; ?>;
	border-bottom:2px solid <?php echo $ktp_theme_dark; ?>;
	font-stretch:extra-expanded;
	font-weight:bold;
}
.kpt_container_2_top_data {
	float:left;
	display:block;
	margin:2px;
	background:<?php echo $ktp_theme_dark; ?>;
	padding:3px;
	font-weight:bold;
	color:#FFFFFF;
	width:95%;
	overflow:hidden;
}
.kpt_container_2{
	height:410px;
	display:block;
	overflow:auto;
	width:98%;
	float:left;
	padding:1px;
	margin:0px 0px 2px 2px;
	overflow:auto;
}
.ktp_sidebar {
	font-size:11px;
}
.kish-twit-pro-sidebar{
	height:450px;
	display:block;
	width:99%;
	float:left;
	padding:2px;
	margin:0px;
	overflow:visible;
}
.ktp_menu_tabs {
	height:30px;
	display:inline-block;
	padding:2px;
	margin:0px 2px 2px 2px;
	width:95%;
	color:#FFFFFF;
	border-top:2px solid <?php echo $ktp_theme_dark; ?>;
	border-bottom:2px solid <?php echo $ktp_theme_dark; ?>;
}
.ktp_menu_tabs_items {
	float:left;
	display:inline-block;
	margin:2px;
	background:<?php echo $ktp_theme_dark; ?>;
	padding:3px;
	font-weight:bold;
	color:#FFFFFF;
}
.ktp_menu_tabs_items a{
	color:#FFFFFF;
	font-weight:bold;
}
.ktp_profile_items {
	width:100%; 
	display:inline-block;
}
.ktp_profile_items_left {
	width:49%;
	float:left;
	font-size:10px;
	margin:0px 0px 3px 0px;
}
.ktp_profile_items_right {
	width:49%;
	float:right;
	margin:0px 0px 3px 0px;
	font-size:10px;
}
.ktp_profile_items a{
	color:<?php echo $ktp_theme_dark; ?>;
	font-weight:bold;
}
#ktp_tab_menu_prog {
	width:25px;
	height:25px;
	float:right;
	display:inline-block;
}
#ktp_tabs_display {
	padding:2px 2px 2px 5px;
	font-size:11px;
	display:block;
	height:auto;
	margin:2px 2px 5px 2px;
	width:96%;
	border:1px solid <?php echo $ktp_theme_light; ?>;
}
#ktp_tabs_display ul li{
	font-size:11px;
	margin-top:1px;
}
#ktp_tabs_display ul li{
	font-size:11px;
	margin-top:1px;
	margin-bottom:1px;
}
#ktp_tabs_display ul a{

}
.kish_menu {
	width:28%;float:right;margin:2px;
}
.ktp_update_box_top {
	margin:2px;
	padding:2px;
}
.ktp_timeline_box {
	border:1px solid <?php echo $ktp_theme_light; ?>;
	padding:2px;
	margin:2px;
	font-size:11px;
	padding:3px;
}
.ktp_thumb {
	width:45px;
	height:45px;
	border:2px solid <?php echo $ktp_theme_light; ?>;
	margin:2px 4px 2px 0px;
	float:left;
}
.ktp_timeline_box , ktp_timeline_box_fav, ktp_status_tl p{
	padding:2px;
	text-align:left;
	margin-top:2px;
	font-size:11px;
	overflow:hidden;
}
.ktp_p {
	padding:2px;
	text-align:left;
	margin-top:2px;
	font-size:11px;
}
.ktp_timeline_box_notfol {
	border:1px solid <?php echo $ktp_theme_light; ?>;
	padding:2px;
	margin:2px;
	background:#CACACA;
}
.ktp_timeline_box_fav {
	border:1px solid <?php echo $ktp_theme_light; ?>;
	padding:2px;
	margin:2px;
	background:url(img/fav.png) no-repeat top right;
}
.ktp_rights {
	font-size:10px;
	height:80px;
	display:inline-block;
	margin:2px 2px 2px 2px;
	border:1px solid <?php echo $ktp_theme_light; ?>;
	padding:2px;
	width:96%;
}
.ktp_status_tl {
	display:compact;
	float:left;
	width:100%;
}
.ktp_action_panel {
	width:98%;
	height:18px;
	overflow:hidden;
	display:block;
	margin:2px 0px 2px 0px;
	font-size:10px;
	padding:0px;
}
.ktp_action_panel img{
	margin:3px 4px 3px 0px;
}
.ktp_action_panel_links {
	width:20%;
	float:left;
	height:20px;
	display:inline-block;
	text-align:center;
}
.ktp_action_panel_links a{
	color:<?php echo $ktp_theme_dark; ?>;
}
.ktp_action_panel_links a hover{
	text-transform:uppercase;
}
.ktp_error {
	background:#CA0000;
	display:inline;
	color:#FFFFFF;
	font-weight:bolder;
	font-stretch:extra-expanded;
	padding:2px 2px 2px 5px;
	margin-left:5px;
}
.ktp_footer {
	width:98%;
	height:25px;
	padding:3px;
	margin:auto;
	border:1px solid #CACACA;
	margin-top:-10px;
	display:block;
	font-size:11px;
	background:<?php echo $ktp_theme_light; ?>;
}
.ktp_footer_left {
	width:48%;
	padding:2px;
	margin:2px;
	display:inline-block;
	font-size:11px;
	color:<?php echo $ktp_theme_dark; ?>;
}
.ktp_footer_right {
	width:48%;
	padding:2px;
	margin:2px;
	display:inline-block;
	text-align:right;
	font-size:11px;
	color:<?php echo $ktp_theme_dark; ?>;
}
.ktp_pop_container {
	border:2px solid <?php echo $ktp_theme_light; ?>;
	padding:5px;
	margin:5px;
	width:450px;
	height:300px;
	display:block;
}
.kish_twit_post_footer {
	font-size:12px;
	border:1px solid <?php echo $ktp_theme_light; ?>;
	padding:3px;
	text-align:left;
}
.kish_twit_post_footer img{
	margin:10px 5px 5px 0px;
	max-width:40px;
	max-height:40px;
}
.ktp_table {
	font-size:11px;
}
.ktp_table tr {
	font-size:11px;
}
.ktp_table td {
	font-size:11px;
}
#hor-zebra
{
	font-family:Georgia, "Times New Roman", Times, serif;
	font-size: 10px;
	margin: 2px;
	width: 95%;
	text-align: left;
	border-collapse: collapse;
}
#hor-zebra th
{
	font-size: 12px;
	font-weight: normal;
	padding: 3px 3px;
	color: #FFFFFF;
	background:#3b5998;
}
#hor-zebra td
{
	padding: 3px;
	color: #669;
	width:auto;
	border:1px solid <?php echo $ktp_theme_light; ?>;
	height:10px;
}
#hor-zebra .odd
{
	background: #e8edff; 
}
</style>