<?php 
include_once('functions.php'); 
global $ktp_theme_dark, $ktp_theme_light,$ktprequser;
$ktptheme=kish_twit_pro_theme();
$ktp_theme_dark = $ktptheme['dark'];
$ktp_theme_light = $ktptheme['light'];
$arrsavedsiteinfo=ktp_get_saved_accounts_as_string();
?>
var ktp_arr_siteid = new Array();
var ktp_arr_sitename = new Array();
var ktp_str_siteid='<?php echo $arrsavedsiteinfo['siteid'];?>';
ktp_arr_siteid=ktp_str_siteid.split(',');
var ktp_str_sitename='<?php echo $arrsavedsiteinfo['sitename'];?>';
ktp_arr_sitename = ktp_str_sitename.split(',');
var ktp_ref_url ='<?php echo ktp_getDomain(getenv("HTTP_REFERER"));?>';
var ktp_licence='<?php echo $_GET['t']?>';
var ktp_ajax_url='<?php echo KTP_TWIT_AJAX_URL; ?>';
var ktp_ajax_loader='<?php echo KTP_LOADER_1; ?>';
var ktp_autoscroll ='<?php echo KTP_AUTO_SCROLL; ?>';
var ktp_sidebar_menu = '';
var ktp_current_account='';
var ktp_latest_rtbymesinceid='';
var ktp_latest_rttomesinceid='';
var ktp_latest_ktpfriendstimelinesinceid='';
var ktp_latest_ktpshowtwitstatuspagesinceid='';
var ktp_latest_thisuserlatest='';
var ktp_otheruserid='';
var ktp_selected_tweep='';
var ktp_cont1_req = '';
var ktp_cont1_resultdiv = '';
var ktp_cont1_progdiv = '';
var scrollnew = true;
var ktp_cont2_req = '';
var ktp_cont2_resultdiv = '';
var ktp_cont2_progdiv = '';
var scrollnew2 = true;
var homeresultdiv = '<?php echo KTP_CONT_1; ?>';
var homeresultdiv2 = '<?php echo KTP_CONT_2; ?>';
var ktpSidebar = '<?php echo KTP_CONT_SB; ?>';
var globalProg='kish_multi_wp_prog_1';
var dodebug=false;
var ktp_getuser='';
var ktp_get_request='';
var ktp_tags='';
var ktp_api_balance='';
var cont_data_1='';
var cont_data_2='';
var cont_data_top_1='';
var cont_data_top_2='';
var ktp_history = '';
var ktp_rdiv='';
var ktp_rdiv_top='';
var ktp_theme='<?php echo KTP_THEME; ?>';
var ktp_pop=false;
var ktp_history_menu = new Array();
var ktp_history_data = new Array();
var ktp_max_history_items = 12;
jQuery(document).ready(function(){
	ktp_init();
});
function ktp_init() {
	if(!ktp_pop) {
		//jQuery('html, body').animate({scrollTop:120}, 'slow');
	}
	if(jQuery("#ktp_sidebar").length != 0) {
		kish_twit_pro_process_ajax_page_load('req=moreinfo', 'ktp_sidebar', ktp_ajax_loader);
	}  
	if(jQuery("#"+homeresultdiv).length != 0) {
		kish_twit_pro_process_ajax_page_load('req=dm', homeresultdiv, ktp_ajax_loader);
	}
	if(jQuery("#"+homeresultdiv2).length != 0) {
		kish_twit_pro_process_ajax_page_load('req=mentions', homeresultdiv2, ktp_ajax_loader); 
	}
	var ktp_cookie_acc=ktpGetCookie_named('ktpacc');
	if(ktp_cookie_acc.length>0) {
		ktp_current_account=ktp_cookie_acc;
	}
	else {
		ktp_current_account=ktp_arr_siteid[0];
	}
}
//jQuery(document).ready(function(){var sys=setInterval("kish_twit_pro_reload_1();",120000);});
//jQuery(document).ready(function(){var sys=setInterval("kish_twit_pro_reload_2();",65000);});
//jQuery(document).ready(function(){var sys=setInterval("kish_twit_pro_reload_3();",10000);});
//jQuery(document).ready(function(){var sys=setInterval("kish_twit_pro_reload_4();",15000);});
//jQuery(document).ready(function(){var sys=setInterval("kish_twit_pro_reload_5();",30000);});
/*
if(ktp_autoscroll) {
	jQuery(document).ready(function(){
		var elem2 = jQuery("#"+homeresultdiv);
		jQuery("#"+homeresultdiv).scroll(function(){
		//ktpdebug('Mode is ' + scrollnew2);
		//ktpdebug(ktp_cont2_req);
			if(scrollnew2==true) {
				var pos = (elem2.scrollTop()/elem2[0].scrollHeight) * 100;
				ktpdebug(pos + " / ");
				if (pos >=70) {
					//ktpdebug(ktp_cont2_req + ' ' + ktp_cont2_resultdiv);
					if(ktp_cont2_req.length==0) {return false; }
				  	kish_twit_pro_process_ajax_load_new_auto('req=' + ktp_cont2_req, ktp_cont2_resultdiv, ktp_cont2_progdiv, ktp_ajax_loader, 2);
				}
			}
		});
	});
	jQuery(document).ready(function(){
		var elem1 = jQuery('#ktp_container_1');
		jQuery("#ktp_container_1").scroll(function(){
			//ktpdebug('Mode is ' + scrollnew);
			if(scrollnew==true) {
				var pos = (elem1.scrollTop()/elem1[0].scrollHeight) * 100;
				ktpdebug(pos + " / ");
				if (pos >=70) {
				if(ktp_cont1_req.length==0) {return false; }
					//ktpdebug(ktp_cont1_req + ' -  ' + ktp_cont1_resultdiv);
				  kish_twit_pro_process_ajax_load_new_auto('req=' + ktp_cont1_req, ktp_cont1_resultdiv, ktp_cont1_progdiv, ktp_ajax_loader, 1);
				}
			}
		});
	});
}
*/
function ktp_get_tweethandle_name_from_id(id) {
	var i = -1;
	x=0;
	for(x in ktp_arr_siteid) {
		if(ktp_arr_siteid[x]==id) {
			i = x;
		}
	}
	if(i>=0) {
		return ktp_arr_sitename[i];
	}
	else {
		return false;
	}
}
function ktp_update_history(divid) {
	var historylinkanchotext = jQuery("#" + divid + '_header').html();
	var nextHistId = ktp_history_menu.length;
	var historylink='<a href="#" onclick="ktp_on_menu_click(' + nextHistId + ', \''+ historylinkanchotext +'\');return false;">' + historylinkanchotext + '</a> [' +ktp_get_tweethandle_name_from_id(ktp_current_account)+ ']';
	if(ktp_history_menu.length > ktp_max_history_items) {
		ktp_history_menu.pop();
		ktp_history_data.pop();
	}
	ktp_history_menu.push(historylink);
	ktp_history_data.push(jQuery("#" + divid).html());
}
function ktp_on_menu_click(id, headertext) {
	if(ktp_rdiv.length==0) {ktp_rdiv=homeresultdiv;};
	jQuery("#"+ktp_rdiv).fadeOut("slow", function() {
		jQuery("#" + ktp_rdiv).html(ktp_history_data[id]);
		jQuery("#" + ktp_rdiv + '_header').html(headertext);
		jQuery("#"+ktp_rdiv).fadeIn("slow");
	});
}
function ktp_print_history_menu() {
	if(ktp_history_menu.length==0) {
		alert('Sorry No History Saved');
		return false;
	}
	var ktp_history_menu_print='<ul>';
	for(x in ktp_history_menu) {
		ktp_history_menu_print+='<li>' + ktp_history_menu[x]+ '</li>';
	}
	ktp_history_menu_print+='</ul>';
	jQuery("#" + ktpSidebar).html(ktp_history_menu_print);
}
function kish_twit_pro_reload_1() {
	if(jQuery("#"+homeresultdiv).html()=='My Updates') {
		kish_twit_pro_process_ajax_load_new('req=ktpshowTwitStatusPage&since_id=' + ktp_latest_ktpshowtwitstatuspagesinceid + '&count=20', 'usertl-new-utl' + ktp_latest_ktpshowtwitstatuspagesinceid, 'usertl-new-utl' + ktp_latest_ktpshowtwitstatuspagesinceid, ktp_ajax_loader);
	}
}
function kish_twit_pro_reload_2() {
	if(jQuery("#"+homeresultdiv).html()=='My Friends Updates') {
		kish_twit_pro_process_ajax_load_new('req=ktpfriendstimeline&since_id=' + ktp_latest_ktpfriendstimelinesinceid + '&count=20', 'usertl-new-ftl' + ktp_latest_ktpfriendstimelinesinceid, 'usertl-new-ftl' + ktp_latest_ktpfriendstimelinesinceid, ktp_ajax_loader);
	}
}
function kish_twit_pro_reload_4() {
	if(jQuery("#kish_twit_post_footer").length != 0) {
		kish_twit_pro_process_ajax_page_load('req=relatedtweets', 'kish_twit_post_footer', ktp_ajax_loader);
	}
}
function kish_twit_pro_reload_3() {
	var headtext = jQuery("#ktp_cont_top_data").html();
	//alert(ktp_otheruserid);
	var pageurl = window.location;
    if(ktp_otheruserid.length>0) {
    	//alert('Im in');
        if(pageurl.search(/users/i)) {
            var data = 'req=thisuserlatesttweets&user=' + ktp_otheruserid +'&since_id=' + ktp_latest_thisuserlatest + '&count=20';
            alert(data);
            kish_twit_pro_process_ajax_load_new(data, 'usertl-new-page-progress' + ktp_latest_thisuserlatest, 'usertl-new-page-progress' + ktp_latest_thisuserlatest, ktp_ajax_loader);
        }
    }
}
function kish_twit_pro_reload_5() {
	if(jQuery("#kish_twit_api_limits").length != 0) {
		kish_twit_pro_process_ajax_page_load('req=ktpaccstatus', 'kish_twit_api_limits', ktp_ajax_loader); 
	}
}
function ktp_show_settings() {
	var dataToSend='req=settingspage';
	jQuery.post(ktp_ajax_url,dataToSend,function(data) {
	jQuery("#"+homeresultdiv).fadeOut("slow", function() {
		jQuery("#"+homeresultdiv).html(data);
		jQuery("#"+homeresultdiv).fadeIn("slow");
	});
	},"html");
}
function ktp_do(dataToSend, resultDiv, title, linkDiv, img, msg) {
	ktp_show_msg('Processing Request...', 3000);
	img = img == undefined ? ktp_ajax_loader : img;
	if(ktp_rdiv_top.length>0 && resultDiv !=ktpSidebar) {
		resultDiv=ktp_rdiv;
	}
	jQuery("#"+resultDiv).animate({scrollTop:0}, 'slow');
	if(title!=undefined || title !='') {
		jQuery("#"+resultDiv + '_header').fadeOut("slow", function() {
			jQuery("#" + resultDiv + '_header').html(title);
			jQuery("#"+resultDiv + '_header').fadeIn("slow");
		});
	}
	var linkDivHTML='';
	linkDivHTML = linkDiv == undefined ? '' : jQuery("#"+linkDiv).html();
	if(linkDivHTML.length==0) {
		//ktp_show_msg('Processing Request...');
		jQuery("#kish_multi_wp_prog_1").html('<img width="15px" height="9px" src="'+ img +'">');
	}
	else {
		jQuery("#" + linkDiv).html('<img width="15px" height="9px" src="'+ img +'">');
		
	}
	if(ktp_current_account>0) {
		dataToSend = dataToSend + '&accountid=' + ktp_current_account;
    }
	jQuery.post(ktp_ajax_url,dataToSend,function(data) {
		jQuery("#"+resultDiv).fadeOut("slow", function() {
			jQuery("#"+resultDiv).html('');
			jQuery("#"+resultDiv).html(data);
			jQuery("#"+resultDiv).fadeIn("slow", function() {
				if(linkDivHTML.length==0) {
					jQuery("#kish_multi_wp_prog_1").html('');
					ktp_show_msg('Request Processed...', 2000);
				}
				else {
					jQuery("#" + linkDiv).html(linkDivHTML);
				}
				ktp_update_history(resultDiv);
			});
		});
	},"html");
}
function kmp_save_search_link(q, resultDiv) {
	if(ktp_rdiv_top.length>0) {
		resultDiv=ktp_rdiv;
	}
	var lDiv = resultDiv + '_header_tools';
	var saveSearchLink = '<a href="#" onclick="ktp_save_search(\'req=savednewsearch&query=' + q + '\', \'' + lDiv + '\');return false;">Save</a>';
	jQuery("#" + lDiv).html(saveSearchLink);
}
function ktp_save_search(dataToSend, resultDiv){
	if(ktp_current_account>0) {
		dataToSend = dataToSend + '&accountid=' + ktp_current_account;
    }
	jQuery("#"+resultDiv).html('<img width="15px" height="9px" src="'+ ktp_ajax_loader +'">');
	jQuery.post(ktp_ajax_url,dataToSend,function(data) {
		jQuery("#"+resultDiv).fadeOut("slow", function() {
			jQuery("#"+resultDiv).html(data);
			jQuery("#"+resultDiv).fadeIn("slow", function() {
				dataToSend='req=savedsearches';
				if(ktp_current_account>0) {
					dataToSend = dataToSend + '&accountid=' + ktp_current_account;
			    }
				jQuery.post(ktp_ajax_url,dataToSend,function(data) {
					jQuery("#"+ktpSidebar).html(data);
				});
			});
		});
	},"html");
}
function ktp_del_saved_search(divid, id){
	var dataToSend = 'req=delsavedsearch&id=' + id;
	if(ktp_current_account>0) {
		dataToSend = dataToSend + '&accountid=' + ktp_current_account;
    }
	jQuery("#"+divid).html('<img width="15px" height="9px" src="'+ ktp_ajax_loader +'">');
	jQuery.post(ktp_ajax_url,dataToSend,function(data) {
		dataToSend='req=savedsearches';
		if(ktp_current_account>0) {
			dataToSend = dataToSend + '&accountid=' + ktp_current_account;
    	}
		jQuery.post(ktp_ajax_url,dataToSend,function(data) {
			jQuery("#"+ktpSidebar).html(data);
		});
	},"html");
}
function kish_twit_pro_process_ajax_update_pop(dataToSend, resultDiv, linkDiv, img) {
	if(jQuery("#"+resultDiv).length>0) { resultDiv=resultDiv; }
	else {resultDiv=homeresultdiv; }
	if(ktp_current_account>0) {
		dataToSend = dataToSend + '&accountid=' + ktp_current_account;
    }
	var linkDivHTML = jQuery("#"+linkDiv).html();
	jQuery("#"+linkDiv).html('<img width="15px" height="9px" src="'+ img +'">');
	jQuery.post(ktp_ajax_url,dataToSend,function(data) {
	jQuery("#"+resultDiv).fadeOut("slow", function() {
		jQuery("#"+resultDiv).html(data);
	});
	jQuery("#"+resultDiv).show("slow");
	jQuery("#"+linkDiv).html(linkDivHTML);
	var howLong = 500;
	t = null;
	t = setTimeout("self.close()",howLong);
	},"html");
}
function kish_twit_pro_process_ajax_admin(dataToSend, resultDiv, linkDiv, img) {
	if(jQuery("#"+resultDiv).length>0) { resultDiv=resultDiv; }
	else {resultDiv=homeresultdiv; }
	if(ktp_current_account>0) {
		dataToSend = dataToSend + '&accountid=' + ktp_current_account;
    }
	var linkDivHTML = jQuery("#"+linkDiv).html();
	jQuery("#"+linkDiv).html('<img width="15px" height="9px" src="'+ img +'">');
	jQuery.post(ktp_ajax_url,dataToSend,function(data) {
		jQuery("#"+resultDiv).fadeOut("slideUp", function() {
			jQuery("#"+resultDiv).html(data);
		});
		jQuery("#"+resultDiv).show("slideDown");
		jQuery("#"+linkDiv).html(linkDivHTML);
		if(jQuery("#ktp_debug_info_panel").length>0) {
			jQuery("#ktp_debug_info_panel").html('<img width="15px" height="9px" src="'+ img +'">Checking for current settings..');
			ktp_do('req=ktprefreshdebug', 'ktp_debug_info_panel');
		}
	},"html");
}
function kish_twit_pro_process_ajax_reloading(dataToSend, resultDiv, linkDiv, img) {
	if(jQuery("#"+resultDiv).length>0) { resultDiv=resultDiv; }
	else {resultDiv=homeresultdiv; }
	if(ktp_current_account>0) {
		dataToSend = dataToSend + '&accountid=' + ktp_current_account;
    }
	var linkDivHTML = jQuery("#"+linkDiv).html();
	jQuery("#"+linkDiv).html('<img width="15px" height="9px" src="'+ img +'">');
	jQuery.post(ktp_ajax_url,dataToSend,function(data) {
	jQuery("#"+resultDiv).slideUp("slow", function() {
		jQuery("#"+resultDiv).html(data);
	});
	jQuery("#"+resultDiv).slideDown("slow");
	jQuery("#"+linkDiv).html(linkDivHTML);
	},"html");
}
function kish_twit_pro_process_ajax_load_new(dataToSend, resultDiv, linkDiv, img) {
	if(jQuery("#"+resultDiv).length>0) { resultDiv=resultDiv; }
	else {resultDiv=homeresultdiv; }
	if(ktp_current_account>0) {
		dataToSend = dataToSend + '&accountid=' + ktp_current_account;
    }
	var linkDivHTML = jQuery("#"+linkDiv).html();
	jQuery("#"+linkDiv).html('<img width="15px" height="9px" src="'+ img +'">');
	jQuery.post(ktp_ajax_url,dataToSend,function(data) {
	jQuery("#"+resultDiv).slideUp("slow", function() {
    	if(data.length<=1) {
        	jQuery("#"+resultDiv).html(linkDivHTML);
        }
        else {
			jQuery("#"+resultDiv).html(data);
        }
	});
	jQuery("#"+resultDiv).slideDown("slow");
    jQuery("#"+resultDiv).fadeTo("slow", 0.33, function() {
    	jQuery("#"+resultDiv).fadeTo("slow", 1.0);
    });
	//jQuery("#"+linkDiv).html(linkDivHTML);
	},"html");
}
function kish_twit_pro_process_ajax_load_new_auto(dataToSend, resultDiv, linkDiv, img, contnum) {
	if(jQuery("#"+resultDiv).length>0) { resultDiv=resultDiv; }
	else {resultDiv=homeresultdiv; }
	if(contnum==1) {scrollnew=false;}
	if(contnum==2) {scrollnew2=false;}
	if(ktp_current_account>0) {
		dataToSend = dataToSend + '&accountid=' + ktp_current_account;
    }
    //alert(dataToSend);
	var linkDivHTML = jQuery("#"+linkDiv).html();
	jQuery("#"+linkDiv).html('<img width="15px" height="9px" src="'+ img +'">');
	jQuery.post(ktp_ajax_url,dataToSend,function(data) {
	jQuery("#"+resultDiv).slideUp("slow", function() {
	if(contnum==1) {scrollnew=true;}
	if(contnum==2) {scrollnew2=true;}
    	if(data.length<=1) {
        	jQuery("#"+resultDiv).html(linkDivHTML);
        }
        else {
			jQuery("#"+resultDiv).html(data);
        }
	});
	jQuery("#"+resultDiv).slideDown("slow");
    jQuery("#"+resultDiv).fadeTo("slow", 0.33, function() {
    	jQuery("#"+resultDiv).fadeTo("slow", 1.0);
    });
	//jQuery("#"+linkDiv).html(linkDivHTML);
	},"html");
}
function kish_twit_pro_process_ajax_msg(dataToSend, resultDiv, linkDiv, img, msg) {
	//ktpgetCookie();
	dataToSend = dataToSend + '&mytags=' + ktp_tags;
	if(jQuery("#"+resultDiv).length>0) { resultDiv=resultDiv; }
	else {resultDiv=homeresultdiv; }
	if(ktp_current_account>0) {
		dataToSend = dataToSend + '&accountid=' + ktp_current_account;
    }
	var linkDivHTML;
	if(msg.length>0) {
		linkDivHTML = msg;
	}
	else {
		linkDivHTML = jQuery("#"+linkDiv).html();
	}
	jQuery("#"+linkDiv).html('<img width="15px" height="9px" src="'+ img +'">');
	jQuery.post(ktp_ajax_url,dataToSend,function(data) {
		jQuery("#"+resultDiv).fadeOut("slow", function() {
			if(resultDiv!=linkDiv) {
				jQuery("#"+resultDiv).html(data);
			}
			else {
				jQuery("#"+linkDiv).html(linkDivHTML);
			}
		});
		jQuery("#"+resultDiv).show("slow");
		if(msg=='Re-Tweeted..') {
			if(jQuery("#ktp_container_1_top_data").html()=='Retweets By Me') {
				kish_twit_pro_process_ajax_load_new('req=retweetsbyme&amp;since_id=' + ktp_latest_rtbymesinceid + '&amp;count=20', 'usertl-new-ftl' + ktp_latest_rtbymesinceid , 'usertl-new-ftl' + ktp_latest_rtbymesinceid, img);
			}
			//else {
				//kish_twit_pro_process_ajax('req=retweetsbyme', 'ktp_container_1', 'ktp_tab_menu_progretweetsbyme',img); kish_twit_pro_update_header_title('ktp_container_1_top_data', 'Retweets By Me', img);
			//}
		}
	},"html");
}
function ktp_update_status(dataToSend, resultDiv, linkDiv, img) {
	if(ktp_current_account>0) {
		dataToSend = dataToSend + '&accountid=' + ktp_current_account;
    }
	var linkDivHTML = jQuery("#"+linkDiv).html();
	jQuery("#"+linkDiv).html('<img width="15px" height="9px" src="'+ img +'">');
	jQuery.post(ktp_ajax_url,dataToSend,function(data) {
	jQuery("#"+resultDiv).fadeOut("slow", function() {
		if(resultDiv=='sucess') {
			jQuery("#"+resultDiv).html(data);
		}
		else {
			jQuery("#"+resultDiv).html(data);
		}
	});
	jQuery("#"+resultDiv).show("slow");
	jQuery("#"+linkDiv).html(linkDivHTML);
	},"html");	
}
function kish_twit_pro_process_ajax(dataToSend, resultDiv, linkDiv, img, anchortext) {
	if(ktp_rdiv.length>0) {
		if(resultDiv==homeresultdiv || resultDiv==homeresultdiv2) {
			resultDiv=ktp_rdiv;
		}
	}
	if(anchortext===undefined) {
		anchortext='';
	}
	else {
		var rand_no = Math.floor((9999-999)*Math.random()) + 1000;
		lDiv = 'history_' + rand_no;
		var link2="kish_twit_pro_update_header_title('" + resultDiv + "_top_data', '" + anchortext + "', '" + img + "'); return false;";
		var link = "kish_twit_pro_process_ajax('" + dataToSend + "', '" + resultDiv + "', '" + lDiv + "', '" + img + "');";
		var link3="kish_twit_pro_clear('"+lDiv+"');return false;";
		var history = '<a href="#" onclick="'+ link + link2 + '">' + anchortext + '</a><a href="##" onclick="'+link3+'"> X </a>';
		if(anchortext.length>0) {
			var kdt= jQuery("#ktp_t_widget").html();
			if(kdt!=null) {
				if(kdt.search(anchortext)== -1) {
					kish_twit_save_history('<span id="'+lDiv+'">'+history+'</span>');
				}
			}
		}
	}
	if(resultDiv==homeresultdiv) {
		cont_data_1=jQuery("#"+resultDiv).html();
		cont_data_top_1=jQuery("#"+resultDiv+"_top_data").html();
	}
	if(resultDiv==homeresultdiv2){
		cont_data_2=jQuery("#"+resultDiv).html();
		cont_data_top_2=jQuery("#"+resultDiv+"_top_data").html();
	}
	var resultDivData;
	if(jQuery("#"+resultDiv).length>0) { resultDiv=resultDiv; resultDivData=jQuery("#"+resultDiv).html(); }
	else {resultDiv=homeresultdiv; }
	if(ktp_current_account>0) {
		dataToSend = dataToSend + '&accountid=' + ktp_current_account;
    }
	var linkDivHTML = jQuery("#"+linkDiv).html();
	jQuery("#"+linkDiv).html('<img width="15px" height="9px" src="'+ img +'">');
	jQuery.post(ktp_ajax_url,dataToSend,function(data) {
	jQuery("#"+resultDiv).fadeOut("slow", function() {
		if(resultDiv=='sucess') {
			jQuery("#"+resultDiv).html(data);
		}
		else {
			jQuery("#"+resultDiv).html(data);
		}
	});
	jQuery("#"+resultDiv).show("slow");
	jQuery("#"+linkDiv).html(linkDivHTML);
	},"html");
}
function kish_twit_pro_update_header_title(divid, data, img) {
	if(ktp_rdiv_top.length>0) {
		divid=ktp_rdiv_top;
	}
    jQuery("#"+divid).html('<img width="15px" height="9px" src="'+ img +'">');
    jQuery("#"+divid).fadeOut("slow", function() {
    	jQuery("#"+divid).html('<strong>' + data + '</strong>');
        jQuery("#"+divid).show("slow");       
	});
}
function kish_select_result_div(seldiv) {
	kish_unselect_all_result_divs();
	ktp_rdiv=seldiv;
	if(seldiv==homeresultdiv){
		jQuery("#" + homeresultdiv + '_header').css("color","<?php echo $ktp_theme_dark; ?>");
		ktp_rdiv_top=homeresultdiv + '_header';
	}
	else {
		jQuery("#" + homeresultdiv2 + '_header').css("color","<?php echo $ktp_theme_dark; ?>");
		ktp_rdiv_top=homeresultdiv2 + '_header';
	}
}
function kish_unselect_all_result_divs(){
	jQuery("#" + homeresultdiv + '_header').css("color","#FFFFFF");
	jQuery("#" + homeresultdiv2 + '_header').css("color","#FFFFFF");
}
function kish_twit_back() {
	if(cont_data_1.length>0) {
		jQuery("#"+homeresultdiv).html(cont_data_1);
		jQuery("#"+homeresultdiv+"_top_data").html(cont_data_top_1);
	}
	if(cont_data_2.length>0) {
		jQuery("#"+homeresultdiv2).html(cont_data_2);
		jQuery("#"+homeresultdiv2+"_top_data").html(cont_data_top_2);
	}
}
function kish_twit_save_history(d) {
	jQuery("#ktp_t_widget").append(d);
}
function kish_twit_pro_process_ajax_progress(dataToSend, resultDiv, progressDiv, img) {
	if(jQuery("#"+resultDiv).length>0) { resultDiv=resultDiv; }
	else {resultDiv=homeresultdiv; }
	if(ktp_current_account>0) {
		dataToSend = dataToSend + '&accountid=' + ktp_current_account;
    }
	jQuery("#"+progressDiv).html('<img src="'+ img +'">');
	jQuery.post(ktp_ajax_url,dataToSend,function(data) {
	jQuery("#"+resultDiv).fadeOut("slow", function() {
		jQuery("#"+resultDiv).html(data);
	});
	jQuery("#"+resultDiv).show("slow");
	if(resultDiv!=progressDiv) {
		jQuery("#"+progressDiv).html('');
	}
	},"html");
}
function kish_twit_pro_process_ajax_page_load(dataToSend, resultDiv, img) {
	ktp_show_msg('Processing Request...');
	var tempdts = dataToSend;
	if(jQuery("#"+resultDiv).length>0) { resultDiv=resultDiv; }
	else {resultDiv=homeresultdiv; }
	if(ktp_current_account>0) {
		dataToSend = dataToSend + '&accountid=' + ktp_current_account;
    }
	//var linkDivHTML = jQuery("#"+linkDiv).html();
	if(tempdts=='req=ktpaccstatus') {
	
	}
	else {
		jQuery("#"+resultDiv).html('<img src="'+ img +'">');
	}
	jQuery.post(ktp_ajax_url,dataToSend,function(data) {
		jQuery("#"+resultDiv).slideToggle("slow", function(){
			jQuery("#"+resultDiv).html(data);
			jQuery("#"+resultDiv).slideToggle("slow");
			ktp_show_msg('Request Processed...', 3000);
			if(resultDiv!=ktpSidebar) {
				ktp_update_history(resultDiv);
			}
		});
	},"html");
}
function kish_twit_pro_del_account(resultDiv, img) {
	if(jQuery("#"+resultDiv).length>0) { resultDiv=resultDiv; }
	else {resultDiv=homeresultdiv; }
	if(confirm("Are you sure to delete this account from Kish Twit Pro, Any way you can add it any time.. Shall I remove it? ")) {
		dataToSend = 'req=delaccount&accountid=' + ktp_current_account;
        jQuery("#"+resultDiv).html('<img src="'+ img +'">');
        jQuery.post(ktp_ajax_url,dataToSend,function(data) {
        jQuery("#"+resultDiv).slideToggle("slow", function(){
            jQuery("#"+resultDiv).html(data);
            jQuery("#"+resultDiv).slideToggle("slow");
        });
        },"html");
    }
}
function kish_twit_pro_select_tweep(tweepid) {
	ktp_selected_tweep=tweepid;
}
function kish_twit_pro_add_member_to_list(id, listid, resultDiv, linkDiv) {
	if(jQuery("#"+resultDiv).length>0) { resultDiv=resultDiv; }
	else {resultDiv=homeresultdiv; }
	var list = kish_twit_pro_getVar(listid);
    if(list=='Select Your List') {
    	alert('Please Select a List and Click the Add Button..');
        return false;
    }
	var dataToSend = 'req=addnewmembertolist&member=' + id + '&listid=' + list;
	if(ktp_current_account>0) {
		dataToSend = dataToSend + '&accountid=' + ktp_current_account;
    }
    kish_twit_pro_process_ajax(dataToSend, resultDiv, linkDiv, ktp_ajax_loader)
	//kish_twit_pro_process_ajax_progress(dataToSend, resultdiv, 'ktp_tab_menu_prog', ktp_ajax_loader);
	//kish_twit_pro_update_header_title('kpt_container_2_top_data', 'Added to list', ktp_ajax_loader);
}
function kish_twit_pro_remove_member_from_list(id, listid, divid, progressdiv) {
	jQuery("#"+progressdiv).html('<img src="'+ ktp_ajax_loader +'">');
	var dataToSend = 'req=removememberfromlist&member=' + id + '&listid=' + listid;
	if(ktp_current_account>0) {
		dataToSend = dataToSend + '&accountid=' + ktp_current_account;
    }
    jQuery.post(ktp_ajax_url,dataToSend,function() {
	jQuery("#"+divid).slideUp("slow");
	},"html");
	//kish_twit_pro_process_ajax_progress(dataToSend, 'ktp_container_1', 'ktp_tab_menu_prog', ktp_ajax_loader);
}
function kish_twit_pro_unsubscribe_list(user, listid, divid, progressdiv) {
	jQuery("#"+progressdiv).html('<img src="'+ ktp_ajax_loader +'">');
	var dataToSend = 'req=unsubscribelist&user=' + user + '&listid=' + listid;
	if(ktp_current_account>0) {
		dataToSend = dataToSend + '&accountid=' + ktp_current_account;
    }
    jQuery.post(ktp_ajax_url,dataToSend,function() {
	jQuery("#"+divid).slideUp("slow");
	},"html");
	//kish_twit_pro_process_ajax_progress(dataToSend, 'ktp_container_1', 'ktp_tab_menu_prog', ktp_ajax_loader);
}
function kish_twit_pro_save_new_list() {
	var dataToSend = 'req=savenewlist&name=' + kish_twit_pro_getVar('ktp_new_list_name') + '&mode=' + kish_twit_pro_getVar('select_mode') + '&description=' + kish_twit_pro_getVar('ktp_new_list_desc');
	if(ktp_current_account>0) {
		dataToSend = dataToSend + '&accountid=' + ktp_current_account;
    }
	kish_twit_pro_process_ajax_progress(dataToSend, 'kpt_container_2', 'ktp_tab_menu_prog', ktp_ajax_loader);
}
function kish_twit_pro_edit_old_list(listid) {
	var dataToSend = 'req=editsavedlist&name=' + kish_twit_pro_getVar('ktp_new_list_name_' + listid) + '&mode=' + kish_twit_pro_getVar('select_mode_' + listid) + '&description=' + kish_twit_pro_getVar('ktp_new_list_desc_' + listid) + '&listid=' + listid;
	if(ktp_current_account>0) {
		dataToSend = dataToSend + '&accountid=' + ktp_current_account;
    }
	kish_twit_pro_process_ajax_progress(dataToSend, 'kpt_container_2', 'edit_save_list_form_' + listid, ktp_ajax_loader);
}
function kish_twit_pro_edit_list_populate(name, mode, description, hiddenlistid) {
	kish_twit_pro_setVar('ktp_new_list_name', name);
	kish_twit_pro_setVar('select_mode', mode);
	kish_twit_pro_setVar('ktp_new_list_desc', description);
	kish_twit_pro_setVar('ktp_hidden_listid', hiddenlistid);
}
function kish_twitter_clear_div(div) {
	jQuery("#"+div).slideUp("slow");
}
function kish_twit_pro_getVar(v) {
	var retval;
	retval = document.getElementById(v).value; 
	return retval;
}
function kish_twit_pro_setVar(divid, msg) {
	document.getElementById(divid).value=msg; 
}
function kish_twit_pro_resetTbox(msg, control) {
	document.getElementById(control).value=msg; 
}
function kish_twit_pro_clear(clearDiv) {
	if(document.getElementById(clearDiv)) {
		document.getElementById(clearDiv).innerHTML = '';
	}
}
function kish_twit_pro_kishShowDiv(div) {
	if(document.getElementById(div)) {
		document.getElementById(div).style.display='block';
	}
}
function kish_twit_pro_toggle(div) {
	jQuery("#"+div).toggle("slow");
} 
function kish_twit_pro_kishHideDiv(div) {
	jQuery("#"+div).slideUp("slow");
}
function kish_twit_pro_getVarCheckBox(v) {
	var retval;
	retval = document.getElementById(v).checked; 
	return retval;
}
function kish_twit_pro_set_accountid(id) {
	if(id!=ktp_current_account) {
		jQuery("#ac-"+id).css("background","<?php echo $ktp_theme_dark; ?>");
		jQuery("#ac-"+id).css("color","<?php echo $ktp_theme_light; ?>");
        jQuery("#ac-"+id).css("font-weight","bold");
		jQuery("#ac-"+ktp_current_account).css("font-weight","normal");
		jQuery("#ac-"+ktp_current_account).css("background","<?php echo $ktp_theme_light; ?>");
		jQuery("#ac-"+ktp_current_account).css("color","<?php echo $ktp_theme_dark; ?>"); 
		ktp_current_account=id;
		Set_Cookie( 'ktpacc', id, 1, '/', ktp_ref_url, '' );
		if(jQuery("#ktp_should_refresh:checked").val()) {
			jQuery("#"+homeresultdiv + '_header').fadeOut("slow", function() {
				jQuery("#" + homeresultdiv + '_header').html('Direct Messages Received [InBox]');
				jQuery("#"+homeresultdiv + '_header').fadeIn("slow");
			});
	        kish_twit_pro_process_ajax_page_load('req=dm', homeresultdiv, ktp_ajax_loader); 
	        jQuery("#"+homeresultdiv2 + '_header').fadeOut("slow", function() {
				jQuery("#" + homeresultdiv2 + '_header').html('Direct Messages Received [InBox]');
				jQuery("#"+homeresultdiv2 + '_header').fadeIn("slow");
			});
	        kish_twit_pro_process_ajax_page_load('req=mentions', homeresultdiv2, ktp_ajax_loader);
			kish_twit_pro_process_ajax_page_load('req=moreinfo', ktpSidebar, ktp_ajax_loader);
	    }
	}
}
function Set_Cookie( name, value, expires, path, domain, secure )
{
// set time, it's in milliseconds
var today = new Date();
today.setTime( today.getTime() );

/*
if the expires variable is set, make the correct
expires time, the current script below will set
it for x number of days, to make it for hours,
delete * 24, for minutes, delete * 60 * 24
*/
if ( expires )
{
expires = expires * 1000 * 60 * 60 * 24;
}
var expires_date = new Date( today.getTime() + (expires) );

document.cookie = name + "=" +escape( value ) +
( ( expires ) ? ";expires=" + expires_date.toGMTString() : "" ) +
( ( path ) ? ";path=" + path : "" ) +
( ( domain ) ? ";domain=" + domain : "" ) +
( ( secure ) ? ";secure" : "" );
}
function kish_twit_pro_set_account_from_button(id) {
	ktp_current_account=id;
	jQuery("#ac-"+id).css("background","<?php echo $ktp_theme_dark; ?>");
	jQuery("#ac-"+id).css("color","<?php echo $ktp_theme_light; ?>");
    jQuery("#ac-"+id).css("font-weight","bold");
}
function kish_twit_pro_set_account_from_button_unselected(id) {
	jQuery("#ac-"+id).css("background","<?php echo $ktp_theme_light; ?>");
	jQuery("#ac-"+id).css("color","<?php echo $ktp_theme_dark; ?>");
    jQuery("#ac-"+id).css("font-weight","normal");
}
function kish_twit_pro_load_menu_links(img){
	jQuery("#ktp_tab_menu_prog").html('<img src="'+ img +'">');
	jQuery("#ktp_tabs_display").fadeOut("slow", function() {
		jQuery("#ktp_tabs_display").html(ktp_sidebar_menu);
        jQuery("#ktp_tabs_display").show("slow");
        jQuery("#ktp_tab_menu_prog").html('');
	});	
}
function kish_twit_pro_load_menu_profile(linkDiv, dataDiv,img){
	var profiledata=jQuery("#"+dataDiv).html();
    profiledata=profiledata.replace(/check-rel/g, 'ktpcheck');
	profiledata=profiledata.replace(/dmbox/g, 'dmboxprof');
    profiledata=profiledata.replace(/profile_list_/g, 'p_l');
	profiledata=profiledata.replace(/list-/g, 'l_');
	profiledata=profiledata.replace(/totup_/g, 'tup_');
	profiledata=profiledata.replace(/kfolling_/g, 'tfol_');
	profiledata=profiledata.replace(/kfollowers_/g, 'tfolow_');
	//profiledata=profiledata.replace(/follow/g, 'prof-follow');
	var linkDivHTML = jQuery("#"+linkDiv).html();
	jQuery("#"+linkDiv).html('<img width="15px" height="9px" src="'+ img +'">');
	jQuery("#" + ktpSidebar).fadeOut("slow", function() {
		jQuery("#" + ktpSidebar).html(profiledata);
        jQuery("#" + ktpSidebar).show("slow");
        jQuery("#"+linkDiv).html(linkDivHTML);
	});	
}
function kish_twit_pro_save_menu_data() {
	if(ktp_sidebar_menu=='') {
		ktp_sidebar_menu = jQuery("#ktp_tabs_display").html();
    }
}
function kish_twit_pro_get_data_to_new_location(divfrom, divto, img) {
	var data = jQuery("#"+divfrom).html();
    jQuery("#"+divto).html('<img width="15px" height="9px" src="'+ img +'">');
    jQuery("#"+divto).html(data);
    jQuery("#"+divto).fadeOut("slow", function() {
    	jQuery("#"+divto).html(data);
        jQuery("#"+divto).show("slow"); 
	});
}
function kish_twit_pro_change_class(divtochange, nameofclass, newclass) {
	jQuery("#"+divtochange).removeClass(nameofclass);
	jQuery("#"+divtochange).addClass(newclass);
}
function ktpdebug(data) {
	if(dodebug) {
		jQuery("#ktp_debug").append("\n" + data);
	}
	else {
		jQuery("#ktp_debug").slideUp("fast");
	}
}
function ktp_add_new_account() {
	jQuery("#"+globalProg).html('<img width="15px" height="9px" src="'+ ktp_ajax_loader +'">');
	window.location = '<?php echo KTP_OAUTH_DIR; ?>/redirect.php';
	return false;
}
function ktpgetCookie() {
	var name ='kish_twit_oauth_token';
	var cookies = document.cookie;
	if (cookies.indexOf(name) == -1) {
		window.location = '<?php echo KTP_OAUTH_DIR; ?>/redirect.php';
		return false;
	}
	else {
	return true;
	}
}
function ktpGetCookie_named(c_name) {
	if (document.cookie.length>0) {
	  	c_start=document.cookie.indexOf(c_name + "=");
	  	if (c_start!=-1) {
		    c_start=c_start + c_name.length+1;
		    c_end=document.cookie.indexOf(";",c_start);
		    if (c_end==-1) c_end=document.cookie.length;
		    return unescape(document.cookie.substring(c_start,c_end));
	    }
  	}
	return "";
}
function ktp_show_msg(msg, timeout) {
	var overlay=false;
	var ktpbg='#000';
	if(ktp_theme=='classic') {ktpbg='#065baa'}
	if(timeout==undefined) {
		timeout=1000000;
		overlay=true;
		center=true;
		msg='<img src="'+ ktp_ajax_loader +'">' + '  ' + msg;
	}
	jQuery.blockUI({ 
		message: msg, 
		fadeIn: 700, 
		fadeOut: 700, 
		timeout: timeout, 
		showOverlay: overlay, 
		centerY: false, 
		css: { 
		   	width: '350px', 
		   	top: '10px', 
		   	left: '', 
		    right: '10px', 
		    border: 'none', 
		    padding: '5px', 
		    backgroundColor: ktpbg, 
		    '-webkit-border-radius': '10px', 
		    '-moz-border-radius': '10px', 
		    opacity: .6, 
		    color: '#fff' 
		 } 
	});
}
function ktpResizeWindow(width, height) {
	self.resizeTo(width, height);
}