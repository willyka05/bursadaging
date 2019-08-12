<?php

//////////////////////////////////////////////////////////////
//===========================================================
// ajax.php
//===========================================================
// PAGELAYER
// Inspired by the DESIRE to be the BEST OF ALL
// ----------------------------------------------------------
// Started by: Pulkit Gupta
// Date:       23rd Jan 2017
// Time:       23:00 hrs
// Site:       http://pagelayer.com/wordpress (PAGELAYER)
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


// The ajax handler
add_action('wp_ajax_pagelayer_wp_widget', 'pagelayer_wp_widget_ajax');
function pagelayer_wp_widget_ajax(){

	global $pagelayer;

	// Some AJAX security
	check_ajax_referer('pagelayer_ajax', 'nonce');
	
	pagelayer_load_shortcodes();
	
	header('Content-Type: application/json');
	
	$ret = [];
	$tag = @$_POST['tag'];
	//pagelayer_print($pagelayer->shortcodes[$tag]);
	
	// No tag ?
	if(empty($pagelayer->shortcodes[$tag])){
		$ret['error'][] =  __pl('no_tag');
		echo json_encode($ret);
		wp_die();
	}
	
	// Include the widgets
	include_once(ABSPATH . 'wp-admin/includes/widgets.php');
	
	$class = $pagelayer->shortcodes[$tag]['widget'];
	
	// Check the widget class exists ?
	if(empty($class) || !class_exists($class)){
		$ret['error'][] =  __pl('no_widget_class');
		echo json_encode($ret);
		wp_die();
	}
	
	$instance = [];
	$widget = new $class();
	$widget->_set('pagelayer-widget-1234567890');
	
	// Is there any existing data ?
	if(!empty($_POST['widget_data'])){
		$json = json_decode(stripslashes($_POST['widget_data']), true);
		//pagelayer_print($json);die();
		if(!empty($json)){
			$instance = $json;
		}
	}

	// Are there any form values ?
	if(!empty($_POST['values'])){		
		parse_str(stripslashes($_POST['values']), $data);
		//pagelayer_print($data);die();
		
		// Any data ?
		if(!empty($data)){
			
			// First key is useless
			$data = current($data);
			
			// Do we still have valid data ?
			if(!empty($data)){
				
				// 2nd key is useless and just over-ride instance
				$instance = current($data);
				
			}
		}
	}
	
	// Get the form
	ob_start();
	$widget->form($instance);
	$ret['form'] = ob_get_contents();
	ob_end_clean();
	
	// Get the html
	ob_start();
	$widget->widget([], $instance);
	$ret['html'] = ob_get_contents();
	ob_end_clean();
	
	// Widget data to set
	if(!empty($instance)){
		$ret['widget_data'] = $instance;
	}
	
	echo json_encode($ret);
	wp_die();
	
}

// Update Post content
add_action('wp_ajax_pagelayer_save_content', 'pagelayer_save_content');
function pagelayer_save_content(){

	// Some AJAX security
	check_ajax_referer('pagelayer_ajax', 'nonce');

	$content = $_POST['pagelayer_update_content'];

	$postID = (int) $_GET['postID'];

	if(empty($postID)){
		$msg['error'] =  __pl('invalid_post_id');
	}
	
	// Check if the post exists
	
	if(!empty($postID) && !empty($content)){
		
		$post = array(
					'ID' => $postID,
					'post_content' => $content,
				);

		// Update the post into the database
		wp_update_post($post);

		if (is_wp_error($postID)) {
			$msg['error'] =  __pl('post_update_err');
		}else{
			$msg['success'] =  __pl('post_update_success');
		}
		
	}else{
		$msg['error'] =  __pl('post_update_err');
	}

	echo json_encode($msg);
	wp_die();
	
}

// Shortcodes Widget Handler
add_action('wp_ajax_pagelayer_do_shortcodes', 'pagelayer_do_shortcodes');
function pagelayer_do_shortcodes(){

	// Some AJAX security
	check_ajax_referer('pagelayer_ajax', 'nonce');
	
	$data = '';
	if(isset($_REQUEST['shortcode_data'])){
		$data = stripslashes($_REQUEST['shortcode_data']);
	}
		
	echo do_shortcode($data);
	wp_die();
	
}

// Get the Site Title
add_action('wp_ajax_pagelayer_fetch_site_title', 'pagelayer_fetch_site_title');
function pagelayer_fetch_site_title(){

	// Some AJAX security
	check_ajax_referer('pagelayer_ajax', 'nonce');
	
	echo get_bloginfo('name');
	wp_die();
}

// Update the Site Title
add_action('wp_ajax_pagelayer_update_site_title', 'pagelayer_update_site_title');
function pagelayer_update_site_title(){
	global $wpdb;

	// Some AJAX security
	check_ajax_referer('pagelayer_ajax', 'nonce');

	$site_title = $_POST['site_title'];

	update_option('blogname', $site_title);

	$wpdb->query("UPDATE `sm_sitemeta` 
				SET meta_value = '".$site_title."'
				WHERE meta_key = 'site_name'");
	wp_die();
}

// Show the SideBars
add_action('wp_ajax_pagelayer_fetch_sidebar', 'pagelayer_fetch_sidebar');
function pagelayer_fetch_sidebar(){
	
	global $wp_registered_sidebars;

	// Some AJAX security
	check_ajax_referer('pagelayer_ajax', 'nonce');
	
	// Create a list
	$pagelayer_wp_widgets = array();
	
	foreach($wp_registered_sidebars as $v){
		$pagelayer_wp_widgets[$v['id']] = $v['name'];
	}
	
	$id = @$_REQUEST['sidebar'];
		
	if(function_exists('dynamic_sidebar') && !empty($pagelayer_wp_widgets[$id])) {
		ob_start();
		dynamic_sidebar($id);
		$result = ob_get_clean();
	}else{
		$result =  __pl('no_widget_area');
	}
	
	echo $result;
	wp_die();
	
}

// Show the primary menu !
add_action('wp_ajax_pagelayer_fetch_primary_menu', 'pagelayer_fetch_primary_menu');
function pagelayer_fetch_primary_menu(){

	// Some AJAX security
	check_ajax_referer('pagelayer_ajax', 'nonce');
	
	if(isset($_POST['nav_list'])){
		echo wp_nav_menu([
			'menu'   => wp_get_nav_menu_object($_POST['nav_list']),
			'menu_id' => $_POST["nav_list"],
			//'theme_location' => 'primary',
			//'menu_class'	 => 'primary-menu',
		]);
	}
	
	wp_die();
}

// Get post revision 
add_action('wp_ajax_pagelayer_get_revision', 'pagelayer_get_revision');
function pagelayer_get_revision(){

	// Some AJAX security
	check_ajax_referer('pagelayer_ajax', 'nonce');

	$postID = (int) $_GET['postID'];
	$post_revisions = array();
	
	if(empty($postID)){
		$post_revisions['error'] =  __pl('invalid_post_id');
	}else{
		$post_revisions = pagelayer_get_post_revision_by_id($postID);
	}
	
	echo json_encode($post_revisions);
	wp_die();
}

// Get post revision 
add_action('wp_ajax_pagelayer_apply_revision', 'pagelayer_apply_revision');
function pagelayer_apply_revision(){

	// Some AJAX security
	check_ajax_referer('pagelayer_ajax', 'nonce');

	$revisionID = (int) $_REQUEST['revisionID'];
	$post_data = array();
	
	if(empty($revisionID)){
		$post_data['error'] =  __pl('invalid_post_id');
	}else{
		
		$post = get_post( $revisionID );
		
		if ( empty( $post ) ) {
			$post_data['error'] =  __pl('invalid_revision');
			echo json_encode($post_data);
			return false;
		}
		
		// Need to make the reviews post global 
		$GLOBALS['post'] = $post;
		
		// Need to reload the shortcodes
		pagelayer_load_shortcodes();
		
		$post_data['content'] = do_shortcode($post->post_content);
		
		if (is_wp_error($postID)) {
			$post_data['error'] =  __pl('rev_load_error');
		}else{
			$post_data['success'] = __pl('rev_load_success');
		}
		
		wp_reset_postdata();
	}
	
	echo json_encode($post_data);
	wp_die();
}

// Get post revision 
add_action('wp_ajax_pagelayer_delete_revision', 'pagelayer_delete_revision');
function pagelayer_delete_revision() {
	
	// Some AJAX security
	check_ajax_referer('pagelayer_ajax', 'nonce');

	$revisionID = (int) $_REQUEST['revisionID'];
	
	if(empty($revisionID)){
		$post_data['error'] =  __pl('invalid_post_id');
	}else{

		$revision = get_post( $revisionID );

		if ( empty( $revision ) ) {
			$post_data['error'] =  __pl('invalid_revision');
		}else{

			if ( ! current_user_can( 'delete_post', $revision->ID ) ) {
					$post_data['error'] =  __pl('access_denied');
					echo json_encode($post_data);
					return false;
			}

			$deleted = wp_delete_post_revision( $revision->ID );

			if ( ! $deleted || is_wp_error( $deleted ) ) {
				$post_data['error'] =  __pl('delete_rev_error');
			}else{
				$post_data['success'] =  __pl('delete_rev_success');
			}
		}
	}
	
	echo json_encode($post_data);
	wp_die();
}

// Get post revision 
add_action('wp_ajax_pagelayer_post_nav', 'pagelayer_post_nav');
function pagelayer_post_nav() {
	
	// Some AJAX security
	check_ajax_referer('pagelayer_ajax', 'nonce');
	
	if(!isset($_REQUEST['data']) || !isset($_REQUEST['postID'])){
		return;
	}
	
	$el['atts'] = $_REQUEST['data'];
	
	$post = get_post($_REQUEST['postID']);
	
	// Need to make this post global
	$GLOBALS['post'] = $post;
	
	$in_same_term = false;
	$taxonomies = 'category';
	$title = '';
	$arrows_list = $el['atts']['arrows_list'];
	
	if($el['atts']['in_same_term']){
		$in_same_term = true;
		$taxonomies = $el['atts']['taxonomies'];
	}
	
	if($el['atts']['post_title']){
		$title = '<span class="pagelayer-post-nav-title">%title</span>';
	}
	
	$next_label = '<span class="pagelayer-next-holder">
		<span class="pagelayer-post-nav-link"> '.$el["atts"]["next_label"].'</span>'.$title.'
	</span>
	<span class="pagelayer-post-nav-icon fa fa-'.$arrows_list.'-right"></span>';
		
	$prev_label = '<span class="pagelayer-post-nav-icon fa fa-'.$arrows_list.'-left"></span>
	<span class="pagelayer-next-holder">
		<span class="pagelayer-post-nav-link"> '.$el["atts"]["prev_label"].'</span>'.$title.'
	</span>';

	$el['atts']['next_link'] = get_next_post_link('%link', $next_label, $in_same_term, '', $taxonomies); 

	$el['atts']['prev_link'] = get_previous_post_link('%link', $prev_label, $in_same_term, '', $taxonomies ); 
	
	echo json_encode($el);
	wp_die();
	
}

// Get post comment template 
add_action('wp_ajax_pagelayer_post_comment', 'pagelayer_post_comment');
function pagelayer_post_comment() {
	global $post;
	
	// Some AJAX security
	check_ajax_referer('pagelayer_ajax', 'nonce');
	
	if(!isset($_REQUEST['postID'])){
		return true;
	}
	
	$GLOBALS['post'] = get_post($_REQUEST['postID']);
	$GLOBALS['withcomments'] = true;
	
	if ( comments_open() || get_comments_number() ) {
		echo '<div class="pagelayer-comments-template">'.comments_template().'</div>';
	}else{
		echo '<div class="pagelayer-comments-close">
			<h2>Comments are closed!</h2>
		</div>';
	}
	wp_die();
		
}

// Get post comment template 
add_action('wp_ajax_pagelayer_post_info', 'pagelayer_post_info');
function pagelayer_post_info() {
	global $post;
	
	// Some AJAX security
	check_ajax_referer('pagelayer_ajax', 'nonce');

	if(!isset($_REQUEST['postID']) || !isset($_REQUEST['el'])){
		return true;
	}
	
	$el['atts'] = $_REQUEST['el'];
	
	$GLOBALS['post'] = get_post($_REQUEST['postID']);
	
	$post_info_content ='';
	$link ='';
	$info_content ='';
	$avatar_url ='';
	
	switch($el['atts']['type']){
		case 'author':
		
			$link = get_author_posts_url( get_the_author_meta( 'ID' ) );
			$avatar_url = get_avatar_url( get_the_author_meta( 'ID' ), 96 );
			$post_info_content = get_the_author_meta( 'display_name', $post->post_author );
			break;

		case 'date':
		
			$format = [
				'default' => 'F j, Y',
				'0' => 'F j, Y',
				'1' => 'Y-m-d',
				'2' => 'm/d/Y',
				'3' => 'd/m/Y',
				'custom' => empty( $el['atts']['date_format_custom'] ) ? 'F j, Y' : $el['atts']['date_format_custom'],
			];

			$post_info_content = get_the_time( $format[ $el['atts']['date_format'] ] );
			$link = get_day_link( get_post_time( 'Y' ), get_post_time( 'm' ), get_post_time( 'j' ) );
				
			break;

		case 'time':
		
			$format = [
				'default' => 'g:i a',
				'0' => 'g:i a',
				'1' => 'g:i A',
				'2' => 'H:i',
				'custom' =>  empty( $el['atts']['time_format_custom'] ) ? 'F j, Y' : $el['atts']['time_format_custom'],
			];
			$post_info_content = get_the_time( $format[ $el['atts']['time_format'] ] );
			
			break;

		case 'comments':
		
			if (comments_open()) {
				$post_info_content = (int) get_comments_number();
				$link = get_comments_link();
			}
			
			break;

		case 'terms':
		
			$taxonomy = $el['atts']['taxonomy'];
			$terms = wp_get_post_terms( get_the_ID(), $taxonomy );
			foreach ( $terms as $term ) {
				$post_info_content .= ' <a if-ext="{{info_link}}" href="'. get_term_link( $term ) .'" class="pagelayer-post-info-list-link"> '. $term->name .' </a>';
			}
			
			break;

		case 'custom':
		
			$post_info_content = $el['atts']['type_custom'];
			$link = $el['atts']['info_custom_link'];

			break;
	}
				
	$el['atts']['post_info_content'] = $post_info_content;
	$el['atts']['avatar_url'] = $avatar_url;
	$el['atts']['link'] = $link;
	
	echo json_encode($el['atts']);
	wp_die();
		
}