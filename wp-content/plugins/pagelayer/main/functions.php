<?php

//////////////////////////////////////////////////////////////
//===========================================================
// class.php
//===========================================================
// PAGELAYER
// Inspired by the DESIRE to be the BEST OF ALL
// ----------------------------------------------------------
// Started by: Pulkit Gupta
// Date:	   23rd Jan 2017
// Time:	   23:00 hrs
// Site:	   http://pagelayer.com/wordpress (PAGELAYER)
// ----------------------------------------------------------
// Please Read the Terms of use at http://pagelayer.com/tos
// ----------------------------------------------------------
//===========================================================
// (c)Pagelayer Team
//===========================================================
//////////////////////////////////////////////////////////////

// Are we being accessed directly ?
if(!defined('PAGELAYER_VERSION')) {
	exit('Hacking Attempt !');
}

// Get the client IP
function _pagelayer_getip(){
	if(isset($_SERVER["REMOTE_ADDR"])){
		return $_SERVER["REMOTE_ADDR"];
	}elseif(isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
		return $_SERVER["HTTP_X_FORWARDED_FOR"];
	}elseif(isset($_SERVER["HTTP_CLIENT_IP"])){
		return $_SERVER["HTTP_CLIENT_IP"];
	}
}

// Get the client IP
function pagelayer_getip(){

	global $pagelayer;

	// Just so that we have something
	$ip = _pagelayer_getip();

	$pagelayer['ip_method'] = (int) @$pagelayer['ip_method'];

	if(isset($_SERVER["REMOTE_ADDR"])){
		$ip = $_SERVER["REMOTE_ADDR"];
	}

	if(isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && @$pagelayer['ip_method'] == 1){
		$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	}

	if(isset($_SERVER["HTTP_CLIENT_IP"]) && @$pagelayer['ip_method'] == 2){
		$ip = $_SERVER["HTTP_CLIENT_IP"];
	}

	// Hacking fix for X-Forwarded-For
	if(!pagelayer_valid_ip($ip)){
		return '';
	}

	return $ip;

}

// Execute a select query and return an array
function pagelayer_selectquery($query, $array = 0){
	global $wpdb;

	$result = $wpdb->get_results($query, 'ARRAY_A');

	if(empty($array)){
		return current($result);
	}else{
		return $result;
	}
}

// Check if an IP is valid
function pagelayer_valid_ip($ip){

	// IPv6
	if(pagelayer_valid_ipv6($ip)){
		return true;
	}

	// IPv4
	if(!ip2long($ip)){
		return false;
	}

	return true;
}

function pagelayer_valid_ipv6($ip){

	$pattern = '/^((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))$/';

	if(!preg_match($pattern, $ip)){
		return false;
	}

	return true;

}

// Check if a field is posted via POST else return default value
function pagelayer_optpost($name, $default = ''){

	if(!empty($_POST[$name])){
		return pagelayer_inputsec(pagelayer_htmlizer(trim($_POST[$name])));
	}

	return $default;
}

// Check if a field is posted via GET else return default value
function pagelayer_optget($name, $default = ''){

	if(!empty($_GET[$name])){
		return pagelayer_inputsec(pagelayer_htmlizer(trim($_GET[$name])));
	}

	return $default;
}

// Check if a field is posted via GET or POST else return default value
function pagelayer_optreq($name, $default = ''){

	if(!empty($_REQUEST[$name])){
		return pagelayer_inputsec(pagelayer_htmlizer(trim($_REQUEST[$name])));
	}

	return $default;
}

// For filling in posted values
function pagelayer_POSTval($name, $default = ''){

	return (!empty($_POST) ? (!isset($_POST[$name]) ? '' : $_POST[$name]) : $default);

}

function pagelayer_POSTchecked($name, $default = false){

	return (!empty($_POST) ? (isset($_POST[$name]) ? 'checked="checked"' : '') : (!empty($default) ? 'checked="checked"' : ''));

}

function pagelayer_POSTselect($name, $value, $default = false){

	if(empty($_POST)){
		if(!empty($default)){
			return 'selected="selected"';
		}
	}else{
		if(isset($_POST[$name])){
			if(trim($_POST[$name]) == $value){
				return 'selected="selected"';
			}
		}
	}

}

function pagelayer_inputsec($string){

	if(!get_magic_quotes_gpc()){

		$string = addslashes($string);

	}else{

		$string = stripslashes($string);
		$string = addslashes($string);

	}

	// This is to replace ` which can cause the command to be executed in exec()
	$string = str_replace('`', '\`', $string);

	return $string;

}

function pagelayer_htmlizer($string){

	$string = htmlentities($string, ENT_QUOTES, 'UTF-8');

	preg_match_all('/(&amp;#(\d{1,7}|x[0-9a-fA-F]{1,6});)/', $string, $matches);//r_print($matches);

	foreach($matches[1] as $mk => $mv){
		$tmp_m = pagelayer_entity_check($matches[2][$mk]);
		$string = str_replace($matches[1][$mk], $tmp_m, $string);
	}

	return $string;

}

function pagelayer_entity_check($string){

	//Convert Hexadecimal to Decimal
	$num = ((substr($string, 0, 1) === 'x') ? hexdec(substr($string, 1)) : (int) $string);

	//Squares and Spaces - return nothing 
	$string = (($num > 0x10FFFF || ($num >= 0xD800 && $num <= 0xDFFF) || $num < 0x20) ? '' : '&#'.$num.';');

	return $string;

}

// Check if a checkbox is selected
function pagelayer_is_checked($post){

	if(!empty($_POST[$post])){
		return true;
	}
	return false;
}

// Reoort an error
function pagelayer_report_error($error = array()){

	if(empty($error)){
		return true;
	}

	$error_string = '<b>Please fix the below error(s) :</b> <br />';

	foreach($error as $ek => $ev){
		$error_string .= '* '.$ev.'<br />';
	}

	echo '<div id="message" class="error"><p>'
					. __pl($error_string)
					. '</p></div>';
}

// Report a notice
function pagelayer_report_notice($notice = array()){

	global $wp_version;

	if(empty($notice)){
		return true;
	}

	// Which class do we have to use ?
	if(version_compare($wp_version, '3.8', '<')){
		$notice_class = 'updated';
	}else{
		$notice_class = 'updated';
	}

	$notice_string = '<b>Please check the below notice(s) :</b> <br />';

	foreach($notice as $ek => $ev){
		$notice_string .= '* '.$ev.'<br />';
	}

	echo '<div id="message" class="'.$notice_class.'"><p>'
					. __pl($notice_string)
					. '</p></div>';
}

// Convert an objext to array
function pagelayer_objectToArray($d){

	if(is_object($d)){
		$d = get_object_vars($d);
	}

	if(is_array($d)){
		return array_map(__FUNCTION__, $d); // recursive
	}elseif(is_object($d)){
		return pagelayer_objectToArray($d);
	}else{
		return $d;
	}
}

// Sanitize variables
function pagelayer_sanitize_variables($variables = array()){

	if(is_array($variables)){
		foreach($variables as $k => $v){
			$variables[$k] = trim($v);
			$variables[$k] = escapeshellcmd($v);
		}
	}else{
		$variables = escapeshellcmd(trim($variables));
	}

	return $variables;
}

// Is multisite ?
function pagelayer_is_multisite() {

	if(function_exists('get_site_option') && function_exists('is_multisite') && is_multisite()){
		return true;
	}

	return false;
}

// Generate a random string
function pagelayer_RandomString($length = 10){
	$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
	$charactersLength = strlen($characters);
	$randomString = '';
	for($i = 0; $i < $length; $i++){
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}

function pagelayer_print($array){

	echo '<pre>';
	print_r($array);
	echo '</pre>';

}

function pagelayer_cleanpath($path){
	$path = str_replace('\\\\', '/', $path);
	$path = str_replace('\\', '/', $path);
	$path = str_replace('//', '/', $path);
	return rtrim($path, '/');
}

// Returns the Numeric Value of results Per Page
function pagelayer_get_page($get = 'page', $resperpage = 50){

	$resperpage = (!empty($_REQUEST['reslen']) && is_numeric($_REQUEST['reslen']) ? (int) pagelayer_optreq('reslen') : $resperpage);

	if(pagelayer_optget($get)){
		$pg = (int) pagelayer_optget($get);
		$pg = $pg - 1;
		$page = ($pg * $resperpage);
		$page = ($page <= 0 ? 0 : $page);
	}else{
		$page = 0;
	}
	return $page;
}

// Are we editing from the Admin panel ?
function pagelayer_is_editing($force = false){

	global $post, $pagelayer;

	if(!empty($force)){
		return true;
	}

	if(!is_admin()){
		return false;
	}

	$current_file = basename($_SERVER['PHP_SELF']);

	$type = get_post_type();
	//echo $type;return false;
	//$page = pagelayer_optreq('page');

	// Are we in the live editor mode OR is this a post which is supported
	if((pagelayer_supported_type($type) && in_array($current_file, array('post.php', 'post-new.php'))) || pagelayer_is_live()){
		return true;
	}else{
		return false;
	}

}

// Is the given post type editable by us ?
function pagelayer_supported_type($type){

	global $pagelayer;

	$type = trim($type);

	if(in_array($type, $pagelayer->settings['post_types'])){
		return true;
	}

	return false;

}

function pagelayer_shortlink($id){
	$link = wp_get_shortlink($id);
	$link .= substr_count($link, '?') > 0 ? '' : '?';
	return $link;
}

// Are we in live mode ?
function pagelayer_is_live(){

	global $post;

	// Are we seeing the post ?
	if(!isset($post) || !isset($post->ID) || empty($post->ID)){
		return false;
	}

	// Is it the live mode ?
	if(pagelayer_optreq('pagelayer-live')){
		return true;
	}

	return false;

}

// Are we in live IFRAME mode ?
function pagelayer_is_live_iframe(){

	// Are we seeing the post ?
	if(!pagelayer_is_live()){
		return false;
	}

	// Is it the live mode ?
	if(pagelayer_optreq('pagelayer-iframe')){
		return true;
	}

	return false;

}

// Can the current user edit the post ?
function pagelayer_user_can_edit($post = NULL){

	global $wp_the_query, $current_user;

	if(!isset($post) || empty($post) || $post === NULL){
		global $post;
	}

	wp_get_current_user();

	if(isset($post) && is_object($post) && isset($post->ID) && isset($post->post_author) && isset($current_user) && is_object($current_user) && isset($current_user->ID) && (current_user_can('edit_others_posts', $post->ID) || ($post->post_author == $current_user->ID))){
		return true;
	}

	return false;

}

// Language sting function
function __pl($key){

	global $pagelayer;

	if(!empty($pagelayer->l[$key])){
		return $pagelayer->l[$key];
	}

	return $key;

}

// Give the list of icon sources
function pagelayer_icon_sources(){
	return array();
}

// Loads the shortcodes
function pagelayer_load_shortcodes(){

	include_once(PAGELAYER_DIR.'/main/shortcode_functions.php');
	include_once(PAGELAYER_DIR.'/main/shortcodes.php');

	// pQuery
	include_once(PAGELAYER_DIR.'/lib/pquery/IQuery.php');
	include_once(PAGELAYER_DIR.'/lib/pquery/gan_formatter.php');
	include_once(PAGELAYER_DIR.'/lib/pquery/gan_node_html.php');
	include_once(PAGELAYER_DIR.'/lib/pquery/gan_tokenizer.php');
	include_once(PAGELAYER_DIR.'/lib/pquery/gan_parser_html.php');
	include_once(PAGELAYER_DIR.'/lib/pquery/gan_selector_html.php');
	include_once(PAGELAYER_DIR.'/lib/pquery/gan_xml2array.php');
	include_once(PAGELAYER_DIR.'/lib/pquery/pQuery.php');
	
	// Apply filter to load custom widgets
	do_action('pagelayer_load_custom_widgets');

}

// Add the shortcodes to the pagelayer list
function pagelayer_add_shortcode($tag, $params = array()){

	global $pagelayer;

	// Is there a handler function ?
	if(!empty($params['func'])){
		
		if($tag == 'pl_row'){
			$inner_tag = 'pl_inner_row';
			add_shortcode($inner_tag, 'pagelayer_render_shortcode');
		}
		
		if($tag == 'pl_col'){
			$inner_tag = 'pl_inner_col';
			add_shortcode($inner_tag, 'pagelayer_render_shortcode');
		}
		
		add_shortcode($tag, 'pagelayer_render_shortcode');//$params['func']);
		//unset($params['func']);
	}

	// Is there a group ?
	if(empty($params['group'])){
		$params['group'] = 'misc';
	}

	// Add the advanced styling group
	$params['options'] = [
		'ele_bg_styles' => __pl('ele_bg_styles'),
		'ele_styles' => __pl('ele_styles'),
		'border_styles' => __pl('border_styles'),
		'animation_styles' => __pl('animation_styles'),
		'responsive_styles' => __pl('responsive_styles'),
		'custom_styles' => __pl('custom_styles'),
	];

	// Are the settings there which hold the params ?
	if(empty($params['settings'])){
		$params['settings'] = [
			'params' => $params['name'],
		];
	}

	// Disable the style options
	if(!empty($params['styles'])){
		$params['settings'] = array_merge($params['settings'], $params['styles']);
		unset($params['styles']);
	}

	// Insert the shortcode
	$pagelayer->shortcodes[$tag] = $params;
	$pagelayer->groups[$params['group']][] = $tag;

}

// Returns the Image values
function pagelayer_image($id){

	$ret = [];

	// External image ?
	if(pagelayer_is_external_img($id)){

		$ret['full-url'] = $id;

	// Attachment
	}elseif(!empty($id)){

		$id = (int) @$id;

		$image = get_post($id);

		// Is there an attachment which is an image ?
		if(!empty($image) && $image->post_type == 'attachment' && wp_attachment_is_image($id)){

			$sizes = get_intermediate_image_sizes();
			array_unshift($sizes, 'full');

			foreach($sizes as $size){
				$src = wp_get_attachment_image_src($id, $size);
				$ret[$size.'-url'] = $src[0];
			}

			// Title and Alt
			$title = esc_attr($image->post_title);
			$alt = get_post_meta($id, '_wp_attachment_image_alt', true);
			$alt = empty($alt) ? $image->post_excerpt : $alt;
			$alt = empty($alt) ? $image->post_title : $alt;
			$alt = empty($alt) ? '' : trim(strip_tags($alt));
			$link = get_attachment_link($id);
			$caption = wp_get_attachment_caption($id);
			$caption = !empty($caption) ? $caption : '';

			$ret['alt'] = $alt;
			$ret['title'] = $title;
			$ret['link'] = $link;
			$ret['caption'] = $caption;

		}

	}

	// No image
	if(empty($ret['full-url'])){
		$ret['full-url'] = PAGELAYER_URL.'/images/default-image.png';
	}

	$ret['url'] = $ret['full-url'];

	return $ret;

}

// Checks if the given parameter is an external link or a wp attachment id
function pagelayer_is_external_img($img = ''){

	if(empty($img)){
		return false;
	}

	if(preg_match('#http://#is', $img) || preg_match('#https://#is', $img) || preg_match('#^{{#is', $img)){
		return true;
	}

	return false;

}

// Returns the attachment url
function pagelayer_attachment($id){

	$ret = [];

	// External url ?
	if(pagelayer_is_external_img($id)){

		$ret['url'] = $id;

	// Attachment
	}elseif(!empty($id)){

		$ret['url'] = wp_get_attachment_url($id);

	}

	return $ret;

}

// Convert the regular URL of a Video to a Embed URL
// Todo : Check
function pagelayer_video_url($source){

	if (!empty($source)) {
		$source = filter_var($source, FILTER_SANITIZE_URL);
		$source = str_replace('&amp;', '&', $source);
		$url = parse_url($source);

		$youtubeRegExp = '/youtube\.com|youtu\.be/is';
		$vimeoRegExp = '/vimeo\.com/is';

		if (preg_match($youtubeRegExp, $source)) {
			$videoSite = 'youtube';
		} else if (preg_match($vimeoRegExp, $source)) {
			$videoSite = 'vimeo';
		}

		switch ($videoSite) {
			case 'youtube':

				if (preg_match('/youtube\.com/is', $source)) {

					if (preg_match('/watch/is', $source)) {
						parse_str($url['query'], $parameters);

						if (isset($parameters['v']) && !empty($parameters['v'])) {
						   $videoId = $parameters['v'];
						}

					} else if (preg_match('/embed/is', $url['path'])) {
						$path = explode('/', $url['path']);
						if (isset($path[2]) && !empty($path[2])) {
							$videoId = $path[2];
						}
					}

				} else if (preg_match('/youtu\.be/is', $url['host'])) {
					$path = explode('/', $url['path']);

					if (isset($path[1]) && !empty($path[1])) {
						$videoId = $path[1];
					}

				}

				return '//youtube.com/embed/'.$videoId;

				break;
			case 'vimeo':

				if (preg_match('/player\.vimeo\.com/is', $url['host']) && preg_match('/video/is', $url['path'])) {
					$path = explode('/', $url['path']);

					if (isset($path[2]) && !empty($path[2])) {
						$videoId = $path[2];
					}

				} else if (preg_match('/vimeo\.com/is', $url['host'])) {
					$path = explode('/', $url['path']);

					if (isset($path[1]) && !empty($path[1])) {
						$videoId = $path[1];
					}

				}

				return '//player.vimeo.com/video/'.$videoId;

				break;
			default:

				return $source;

		}

	}
}


// As per the JS specification
function pagelayer_escapeHTML($str){
	
	$replace = [
		']' => '&#93;',
		'[' => '&#91;',
		//'=' => '&#61;',
		'<' => '&lt;',
		'>' => '&gt;',
		'"' => '&quot;',
		//'&' => '&amp;',
		'\'' => '&#39;'
	];
	
	$str = str_replace(array_keys($replace), array_values($replace), $str);
	
	return $str;
}

// As per the JS specification
function pagelayer_unescapeHTML($str){
	$replace = [
		'#93' => ']',
		'#91' => '[',
		//'#61' => '=',
		'lt' => '<',
		'gt' => '>',
		'quot' => '"',
		//'amp' => '&',
		'#39' => '\''
	];
	
	foreach($replace as $k => $v){
		$str = str_replace('&'.$k.';', $v, $str);
	}
	return $str;
}

// Show promo notice on dashboard
function pagelayer_show_promo(){
	
	global $pagelayer_promo_opts;
	$opts = $pagelayer_promo_opts;
	
	echo '<style>
.pagelayer_promo_button {
background-color: #4CAF50; /* Green */
border: none;
color: white;
padding: 6px 10px;
text-align: center;
text-decoration: none;
display: inline-block;
font-size: 13px;
margin: 4px 2px;
-webkit-transition-duration: 0.4s; /* Safari */
transition-duration: 0.4s;
cursor: pointer;
}
.pagelayer_promo_button:focus{
border: none;
color: white;
}
.pagelayer_promo_button1 {
color: white;
background-color: #4CAF50;
border:3px solid #4CAF50;
}
.pagelayer_promo_button1:hover {
box-shadow: 0 6px 8px 0 rgba(0,0,0,0.24), 0 9px 25px 0 rgba(0,0,0,0.19);
color: white;
border:3px solid #4CAF50;
}
.pagelayer_promo_button2 {
color: white;
background-color: #0085ba;
}
.pagelayer_promo_button2:hover {
box-shadow: 0 6px 8px 0 rgba(0,0,0,0.24), 0 9px 25px 0 rgba(0,0,0,0.19);
color: white;
}
.pagelayer_promo_button3 {
color: white;
background-color: #365899;
}
.pagelayer_promo_button3:hover {
box-shadow: 0 6px 8px 0 rgba(0,0,0,0.24), 0 9px 25px 0 rgba(0,0,0,0.19);
color: white;
}
.pagelayer_promo_button4 {
color: white;
background-color: rgb(66, 184, 221);
}
.pagelayer_promo_button4:hover {
box-shadow: 0 6px 8px 0 rgba(0,0,0,0.24), 0 9px 25px 0 rgba(0,0,0,0.19);
color: white;
}
.pagelayer_promo-close{
float:right;
text-decoration:none;
margin: 5px 10px 0px 0px;
}
.pagelayer_promo-close:hover{
color: red;
}
</style>
<script type="application/javascript">
	jQuery(document).ready(function(){
		jQuery("#pagelayer_promo .pagelayer_promo-close").click(function(){
			var data;
			jQuery("#pagelayer_promo").hide();
			// Save this preference
			jQuery.post("'.admin_url('?pagelayer_promo=0').'", data, function(response) {
				//alert(response);
			});
		});
	});
</script>

	<div class="notice notice-success" id="pagelayer_promo" style="min-height:90px">
	<a class="pagelayer_promo-close" href="javascript:" aria-label="Dismiss this Notice">
		<span class="dashicons dashicons-dismiss"></span> Dismiss
	</a>';
	
	if(!empty($opts['image'])){
		echo '<a href="'.$opts['website'].'"><img src="'.$opts['image'].'" style="float:left; margin:10px 20px 10px 10px" width="67" /></a>';
	}
	
	echo '
	<p style="font-size:13px">We are glad you like <a href="'.$opts['website'].'"><b>PageLayer</b></a> and have been using it since the past few days. It is time to take the next step !</p>
	<p>
		'.(empty($opts['rating']) ? '' : '<a class="pagelayer_promo_button pagelayer_promo_button2" target="_blank" href="'.$opts['rating'].'">Rate it 5★\'s</a>').'
		'.(empty($opts['facebook']) ? '' : '<a class="pagelayer_promo_button pagelayer_promo_button3" target="_blank" href="'.$opts['facebook'].'"><span class="dashicons dashicons-thumbs-up"></span> Facebook</a>').'
		'.(empty($opts['twitter']) ? '' : '<a class="pagelayer_promo_button pagelayer_promo_button4" target="_blank" href="'.$opts['twitter'].'"><span class="dashicons dashicons-twitter"></span> Tweet</a>').'
		'.(empty($opts['website']) ? '' : '<a class="pagelayer_promo_button pagelayer_promo_button4" target="_blank" href="'.$opts['website'].'">Visit our website</a>').'
	</p>
</div>';

}

// Are we to show a promo ?
function pagelayer_maybe_promo($opts){
	
	global $pagelayer_promo_opts;
	
	// There must be an interval
	if(empty($opts['interval'])){
		return false;
	}
	
	// Are we to show a promo	
	$opt_name = 'pagelayer_promo_time';
	$promo_time = get_option($opt_name);
	
	// First time access
	if(empty($promo_time)){
		update_option($opt_name, time() + (!empty($opts['after']) ? $opts['after'] * 86400 : 0));
		$promo_time = get_option($opt_name);
	}
	
	// Is there interval elapsed
	if(time() > $promo_time){
		$pagelayer_promo_opts = $opts;
		add_action('admin_notices', 'pagelayer_show_promo');
	}
	
	// Are we to disable the promo
	if(isset($_GET['pagelayer_promo']) && (int)$_GET['pagelayer_promo'] == 0){
		update_option($opt_name, time() + ($opts['interval'] * 86400));
		die('DONE');
	}
	
}

// Get Post Revision
function pagelayer_get_post_revision_by_id($postID){
	
	// Insert the post revision into the database
	$post_revisions = array();
	$reviews =  wp_get_post_revisions($postID);		
	
	foreach($reviews as $values){
		
		$date_format = date_i18n('j-M @ H:i', strtotime( $values->post_modified ) );
		$user_meta = get_userdata($values->post_author);
		
		if ( false !== strpos( $values->post_name, 'autosave' ) ) {
			$type = 'autosave';
		} else {
			$type = 'revision';
		}
		
		$post_tmp_data = array(
			'ID' => $values->ID,
			'post_author_name' => $user_meta->data->display_name,
			'post_author_url' => get_avatar_url($values->post_author),
			'post_date' => $date_format,
			'post_date_ago' => human_time_diff(strtotime($values->post_modified), current_time( 'timestamp' )) . ' ago ',
			'post_type' => $type,
		);
		
		$post_revisions[] = $post_tmp_data;
	}
	
	return $post_revisions;
}