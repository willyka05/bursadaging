<?php

//////////////////////////////////////////////////////////////
//===========================================================
// settings.php
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

	$option_name = 'pl_gen_setting' ;
	$new_value = '';

	if(isset($_REQUEST['pl_gen_setting'])){
		$new_value = $_REQUEST['pl_gen_setting'];
		
		if ( get_option( $option_name ) !== false ) {
	
			// The option already exists, so we just update it.
			update_option( $option_name, $new_value );

		} else {

			// The option hasn't been added yet. We'll add it with $autoload set to 'no'.
			$deprecated = null;
			$autoload = 'no';
			add_option( $option_name, $new_value, $deprecated, $autoload );
		}

	}
	
	if(isset($_REQUEST['pl_support_ept'])){

		$pl_support_ept = $_REQUEST['pl_support_ept'];
		
		if ( get_option( 'pl_support_ept' ) !== false ) {
	
			// The option already exists, so we just update it.
			update_option( 'pl_support_ept', $pl_support_ept );

		} else {

			// The option hasn't been added yet. We'll add it with $autoload set to 'no'.
			$deprecated = null;
			$autoload = 'no';
			$pl_support_ept = ['post', 'page'];
			add_option( 'pl_support_ept', $pl_support_ept, $deprecated, $autoload );
		}
	}
	
	if(isset($_REQUEST['pagelayer_content_width'])){

		$content_width = $_REQUEST['pagelayer_content_width'];
		
		if ( get_option( 'pagelayer_content_width' ) !== false ) {
	
			// The option already exists, so we just update it.
			update_option( 'pagelayer_content_width', $content_width );

		} else {

			// The option hasn't been added yet. We'll add it with $autoload set to 'no'.
			$deprecated = null;
			$autoload = 'no';
			add_option( 'pagelayer_content_width', $content_width, $deprecated, $autoload );
		}
	}
	
	if(isset($_REQUEST['pagelayer_between_widgets'])){

		$space_widgets = $_REQUEST['pagelayer_between_widgets'];
		
		if ( get_option( 'pagelayer_between_widgets' ) !== false ) {
	
			// The option already exists, so we just update it.
			update_option( 'pagelayer_between_widgets', $space_widgets );

		} else {

			// The option hasn't been added yet. We'll add it with $autoload set to 'no'.
			$deprecated = null;
			$autoload = 'no';
			add_option( 'pagelayer_between_widgets', $space_widgets, $deprecated, $autoload );
		}
	}
	
	$post_type = array('post', 'page', 'product');
	
	$support_ept = get_option( 'pl_support_ept', ['post', 'page']);
	foreach ( $support_ept as $support_pl ) {
		add_post_type_support( $support_pl, 'pagelayer' );
	}
?>
	<form class="pagelayer-setting-form" method="post" action="">
		<h1>PageLayer Editor</h1>
		<p>Welcome To PageLayer</p>
		<div class="tabs-wrapper">
			<h2 class="nav-tab-wrapper pagelayer-wrapper">
				<a href="#general" class="nav-tab">General</a>
				<a href="#settings" class="nav-tab ">Settings</a>
				<a href="#support" class="nav-tab ">Support</a>
				<a href="#faq" class="nav-tab ">FAQ</a>
			</h2>
		
			<div class="pagelayer-tab-panel" id="general">
				 <table>
					<tr>
						<th scope="row">Editor Enables On </th>
						<td>
						<label>
					<?php
						foreach($post_type as $type){
							
							echo '<input type="checkbox" name="pl_support_ept[]" value="'.$type.'" '. (in_array($type, $support_ept) ? "checked" : "") .'/>'.$type.'</br>';
						}
					?>
					</label></td>
					</tr>
				 </table>
			</div>
			<div class="pagelayer-tab-panel" id="settings">
				<table>
					<tr>
						<th>Content Width</th>
						<td>
							<input name="pagelayer_content_width" type="number" step="1" min="320" max="5000" placeholder="1170" <?php if(get_option('pagelayer_content_width')){
								echo 'value="'.get_option('pagelayer_content_width').'"';
							}?>>
							<p>Set the custom width of the content area. The default width set is 1170px</p>
						</td>
					<tr>
					<tr>
						<th>Space Between Widgets</th>
						<td>
							<input name="pagelayer_between_widgets" type="number" step="1" min="0" max="500" placeholder="15" <?php if(get_option('pagelayer_between_widgets')){
								echo 'value="'.get_option('pagelayer_between_widgets').'"';
							}?>>
							<p>Set the Space Between Widgets. The default Space set is 15px</p>
						</td>
					<tr>
				</table>
			</div>
			<div class="pagelayer-tab-panel" id="support">
				<h2>Support</h2>
				<h3>You can contact the PageLayer Group via email. Our email address is <a href="mailto:support@pagelayer.com">support@pagelayer.com</a>. We will get back to you as soon as possible!</h3>


			</div>
			<div class="pagelayer-tab-panel" id="faq">
				<h2>FAQ</h2>
				<div class="pagelayer-acc-wrapper">
					<span class="nav-tab pagelayer-acc-tab">1: Why choose us</span>
					<div class="pagelayer-acc-panel">
						<p>PageLayer is best live editor and easy to use and we will keep improving it !</P>
					</div>
					
					<span class="nav-tab pagelayer-acc-tab">2: Support</span>
					<div class="pagelayer-acc-panel">
						<p>You can contact the PageLayer Group via email. Our email address is <a href="mailto:support@pagelayer.com">support@pagelayer.com</a>. We will get back to you as soon as possible!</p>
					</div>
				</div>
			</div>
		</div>
		<tr>
		<p>
			<input type="submit" name="submit" class="button button-primary" value="Save Changes">
		</p>
	</form>
