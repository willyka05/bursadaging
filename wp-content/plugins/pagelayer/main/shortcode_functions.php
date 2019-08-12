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

// Is there a tag ?
function pagelayer_render_shortcode($atts, $content = '', $tag = ''){

	global $pagelayer;
	
	$_tag = $class = $tag;
	$final_tag = $tag;
	
	// Check if the tag is inner row and col then change it to row and col tag
	if($tag == 'pl_inner_row'){
		$tag = 'pl_row';
	}elseif($tag == 'pl_inner_col'){
		$tag = 'pl_col';
		$final_tag = $tag;
	}
	
	// Clear the pagelayer tags
	if(substr($tag, 0, 3) == 'pl_'){
		$_tag = str_replace('pl_', '', $final_tag);
		$class = 'pagelayer-'.$_tag;
	}
	
	// Is there any function ?
	$func = @$pagelayer->shortcodes[$tag]['func'];
	
	// Create the element array. NOTE : This is similar to the JS el and is temporary
	$el = [];
	$el['atts'] = $atts;
	$el['oAtts'] = $atts;
	$el['id'] = pagelayer_RandomString(16);
	$el['tmp'] = [];
	$el['tag'] = $final_tag;
	$el['content'] = $content;
	$el['selector'] = '[pagelayer-id="'.$el['id'].'"]';
	
	$innerHTML = @$pagelayer->shortcodes[$tag]['innerHTML'];
	if(!empty($innerHTML) && !empty($content)){
		$el['oAtts'][$innerHTML] = $content;
		$el['atts'][$innerHTML] = $content;
	}
	
	// The default class
	$el['classes'][] = $class;
	
	//pagelayer_print($el);
	
	// Lets create the CSS, Classes, Attr. Also clean the dependent atts
	foreach($pagelayer->tabs as $tab){
		
		if(empty($pagelayer->shortcodes[$tag][$tab])){
			continue;
		}
		
		foreach($pagelayer->shortcodes[$tag][$tab] as $section => $Lsection){
			
			$props = empty($pagelayer->shortcodes[$tag][$section]) ? @$pagelayer->styles[$section] : @$pagelayer->shortcodes[$tag][$section];
			
			//echo $tab.' - '.$section.' - <br>';
			
			if(empty($props)){
				continue;
			}
			
			foreach($props as $prop => $param){
			
				//echo $tab.' - '.$section.' - '.$prop.'<br>';
				
				// No value set
				if(empty($el['atts'][$prop]) && empty($el['atts'][$prop.'_tablet']) && empty($el['atts'][$prop.'_mobile'])){
					continue;
				}
				
				// Clean the not required atts
				if(!empty($param['req'])){
					
					$set = true;
					
					foreach($param['req'] as $rk => $reqval){
						$except = $rk{0} == '!' ? true : false;
						$rk = $except ? substr($rk, 1) : $rk;
						$val = @$el['atts'][$rk];
						
						//echo $prop.' - '.$rk.' : '.$reqval.' == '.$val.'<br>';
						
						// The value should not be there
						if($except){
							
							if(!is_array($reqval) && $reqval == $val){
								$set = false;
								break;
							}
							
							// Its an array and a value is found, then dont show
							if(is_array($reqval) && in_array($val, $reqval)){
								$set = false;
								break;
							}
							
						// The value must be equal
						}else{
							
							 if(!is_array($reqval) && $reqval != $val){
								$set = false;
								break;
							 }
							
							// Its an array and no value is found, then dont show
							if(is_array($reqval) && !in_array($val, $reqval)){
								$set = false;
								break;
							}
						}
						
					}
					
					// Unset as we dont need
					if(empty($set)){
						unset($el['atts'][$prop]);
						unset($el['atts'][$prop.'_tablet']);
						unset($el['atts'][$prop.'_mobile']);
					}
					
				}
				
				// We could have unset the value above, so we need to check again if the value is there
				if(empty($el['atts'][$prop]) && empty($el['atts'][$prop.'_tablet']) && empty($el['atts'][$prop.'_mobile'])){
					continue;
				}
				
				// Handle the edit fields
				if(!empty($param['edit'])){
					$el['edit'][$prop] = $param['edit'];
				}
				
				// Load any attachment values
				if(in_array($param['type'], ['image', 'video', 'audio', 'media'])){
					
					$attachment = ($param['type'] == 'image') ? pagelayer_image($el['atts'][$prop]) : pagelayer_attachment($el['atts'][$prop]);
	
					if(!empty($attachment)){
						foreach($attachment as $k => $v){
							$el['tmp'][$prop.'-'.$k] = $v;
						}
					}
					
				}
				
				// Handle the AddClasses
				if(!empty($param['addClass']) && !empty($el['atts'][$prop])){
					
					// Convert to an array
					if(!is_array($param['addClass'])){
						$param['addClass'] = array($param['addClass']);
					}
					
					// Loop through
					foreach($param['addClass'] as $k => $v){
						$k = str_replace('{{element}}', '', $k);
						$el['classes'][] = [trim($k) => str_replace('{{val}}', $el['atts'][$prop], $v)];
					}
					
				}
				
				// Handle the AddClasses
				if(!empty($param['addAttr']) && !empty($el['atts'][$prop])){
					
					// Convert to an array
					if(!is_array($param['addAttr'])){
						$param['addAttr'] = array($param['addAttr']);
					}
					
					// Loop through
					foreach($param['addAttr'] as $k => $v){
						$k = str_replace('{{element}}', '', $k);
						$el['attr'][] = [trim($k) => $v];
					}
					
				}
				
				// Handle the CSS
				if(!empty($param['css'])){
					//echo $prop.'<br>';
					// Convert to an array
					if(!is_array($param['css'])){
						$param['css'] = array($param['css']);
					}
					
					$modes = [
						'desktop' => '', 
						'tablet' => '_tablet', 
						'mobile' => '_mobile'
					];
					
					// Loop the modes and check for values
					foreach($modes as $mk => $mv){
						
						$M_prop = $prop.$mv;
						
						// Any value ?
						if(empty($el['atts'][$M_prop])){
							continue;
						}
						
						// Loop through
						foreach($param['css'] as $k => $v){
							
							// Make the selector
							$selector = (!is_numeric($k) ? str_replace('{{element}}', $el['selector'], $k) : $el['selector']);
							
							$ender = '';
							
							if($mk == 'tablet'){
								$selector = '@media (max-width: 768px) and (min-width: 361px){'.$selector;
								$ender = '}';
							}
							
							if($mk == 'mobile'){
								$selector = '@media (max-width: 360px){'.$selector;
								$ender = '}';
							}
							
							// Make the CSS
							$el['css'][] = $selector.'{'.pagelayer_css_render($v, $el['atts'][$M_prop], @$param['sep']).'}'.$ender;
						}
					
					}
					
				}
				
				if($param['type'] == 'typography'){
					$val = explode(',', $el['atts'][$prop]);
					
					if(!empty($val[0]) && !in_array($val[0], $pagelayer->runtime_fonts)){
						$pagelayer->runtime_fonts[] = $val[0];
						//pagelayer_print($pagelayer->runtime_fonts);
					}
				}
				
			}
			
		}
		
	}
	
	//@pagelayer_print($el['css']);
	
	// Is there a function of the tag ?
	if(function_exists($func)){
		call_user_func_array($func, array(&$el));
	}
	
	// Create the default atts and tmp atts
	if(pagelayer_is_live()){
		pagelayer_create_sc($el);
	}
	
	$div = '<div pagelayer-id="'.$el['id'].'">
			<style pagelayer-style-id="'.$el['id'].'"></style>';
	
	$is_group = !empty($pagelayer->shortcodes[$tag]['params']['elements']) ? true : false;
	
	// If there is an HTML AND you are not a GROUP, then make use of it, or append the real content
	if(!empty($pagelayer->shortcodes[$tag]['html'])){
		
		// Create the HTML object
		$node = pQuery::parseStr($pagelayer->shortcodes[$tag]['html']);
		
		// Remove the if-ext
		foreach($node('[if-ext]') as $v){
			$reqvar = pagelayer_var($v->attr('if-ext'));
			$v->removeAttr('if-ext');
			
			// Is the element there ?
			if(empty($el['atts'][$reqvar])){
				$v->after($v->html());
				$v->remove();
			}
		}
		
		// Remove the if
		foreach($node('[if]') as $v){
			$reqvar = pagelayer_var($v->attr('if'));
			$v->removeAttr('if');
			
			// Is the element there ?
			if(empty($el['atts'][$reqvar])){
				$v->remove();
			}
		}
		
		//die($node->html());
		
		// Do we have a holder ? Mainly for groups
		if(!empty($pagelayer->shortcodes[$tag]['holder'])){
			$node->query($pagelayer->shortcodes[$tag]['holder'])->html('{{pagelayer_do_shortcode}}');
			$do_shortcode = 1;
		}
		
		$html = pagelayer_parse_vars($node->html(), $el);
		
		// Append to the DIV
		$div .= $html;
		
	// Is it a widget ?
	}elseif(!empty($pagelayer->shortcodes[$tag]['widget'])){
		
		$class = $pagelayer->shortcodes[$tag]['widget'];
		$instance = [];
		
		// Is there any existing data ?
		if(!empty($el['atts']['widget_data'])){		
			$json = trim($el['atts']['widget_data']);
			$json = json_decode($json, true);
			//pagelayer_print($json);die();
			if(!empty($json)){
				$instance = $json;
			}
		}
		
		ob_start();
		the_widget($class, $instance, array('widget_id'=>'arbitrary-instance-'.$el['id'],
			'before_widget' => '',
			'after_widget' => '',
			'before_title' => '',
			'after_title' => ''
		));
		
		$div .= ob_get_contents();
		ob_end_clean();
		
	}else{
		$div .= '{{pagelayer_do_shortcode}}';
		$do_shortcode = 1;
	}
	
	// End the tag
	$div .= '</div>';
	
	// Add classes and attributes
	if(!empty($el['classes']) || !empty($el['attr'])){
	
		// Create the HTML object
		$node = pQuery::parseStr($div);
		
		// Add the editable values
		if(!empty($el['edit'])){
			
			foreach($el['edit'] as $k => $v){
				$node->query($v)->attr('pagelayer-editable', $k);
			}
			
		}
		
		// Add the classes
		if(!empty($el['classes'])){
			
			//pagelayer_print($el['classes']);
			
			foreach($el['classes'] as $k => $v){
				
				if(!is_array($v)){
					$v = [$v];
				}
				
				foreach($v as $kk => $vv){
					//echo $kk.' - '.$vv."\n";
					if(is_numeric($kk)){
						$node->query($el['selector'])->addClass($vv);
					}else{
						$node->query($kk)->addClass($vv);
					}
					
				}
				
			}
			
			//echo $node->html();
			//die();
			
		}
	
		// Add the attributes		
		if(!empty($el['attr'])){
			
			//pagelayer_print($el['attr']);
			
			foreach($el['attr'] as $k => $v){
				
				if(!is_array($v)){
					$v = [$v];
				}
				
				foreach($v as $kk => $vv){
					
					$att = explode('=', $vv, 2);
					$att[1] = pagelayer_parse_vars($att[1], $el);
					$att[1] = trim($att[1], '"');
					
					if(is_numeric($kk)){
						$node->query($el['selector'])->attr($att[0], $att[1]);
					}else{
						$node->query($kk)->attr($att[0], $att[1]);
					}
					
				}
				
			}
			
		}
		
		$div = $node->html();
		//die($div);
	
	}
		
	// Add the CSS if any or remove it
	$style = '';
	if(!empty($el['css'])){
		
		$style = '<style pagelayer-style-id="'.$el['id'].'">
'.implode("\n", pagelayer_parse_vars($el['css'], $el)).'
</style>';
		
	}
	
	$div = str_replace('<style pagelayer-style-id="'.$el['id'].'"></style>', $style, $div);
	
	// Is there an inner content which requires a SHORTCODE ?
	if(!empty($do_shortcode)){
		$div = str_replace('{{pagelayer_do_shortcode}}', do_shortcode($el['content']), $div);
	}
	
	return $div;
	
}

// Creates the shortcode and returns a base64 encoded files
function pagelayer_create_sc(&$el){
	
	$a = $tmp = array();
	
	if(!empty($el['oAtts'])){
		
		foreach($el['oAtts'] as $k => $v){
			$el['attr'][] = 'pagelayer-a-'.$k.'="'.$v.'"';
		}
		
	}
	
	// Tmp atts
	if(!empty($el['tmp'])){
		
		foreach($el['tmp'] as $k => $v){
			$el['attr'][] = 'pagelayer-tmp-'.$k.'="'.$v.'"';
		}
		
	}
	
	// Add the tag
	$el['attr'][] = 'pagelayer-tag="'.$el['tag'].'"';
	
	// Make it a PageLayer element for editing
	$el['classes'][] = 'pagelayer-ele';
	
}

// Converts {{val}} to val
function pagelayer_var($var){
	return substr($var, 2, -2);
}

// Parse the variables
function pagelayer_parse_vars($str, &$el){
	
	//pagelayer_print($el);
	if(is_array($el['tmp'])){
		foreach($el['tmp'] as $k => $v){
			$str = str_replace('{{{'.$k.'}}}', $el['tmp'][$k], $str);
		}
	}
	
	if(is_array($el['atts'])){
		foreach($el['atts'] as $k => $v){
			$str = str_replace('{{'.$k.'}}', $el['atts'][$k], $str);
		}
	}
	
	return $str;
}

// Make the rule
function pagelayer_css_render($rule, $val, $sep = ','){
	
	// Seperator
	$sep = empty($sep) ? ',' : $sep;
	
	// Replace the val
	$rule = str_replace('{{val}}', $val, $rule);
	
	// If there is an array
	if(preg_match('/\{val\[\d/is', $rule)){
		$val = explode($sep, $val);
		foreach($val as $k => $v){
			$rule = str_replace('{{val['.$k.']}}', $v, $rule);
		}
	}
	
	return $rule;
	
}

// ROW Handler
function pagelayer_sc_row(&$el){
	pagelayer_bg_video($el);
	
	if(!empty($el['atts']['row_shape_type_top'])){
		$path_top = PAGELAYER_DIR.'/images/shapes/'.$el['atts']['row_shape_type_top'].'-top.svg';
		$el['atts']['svg_top'] = file_get_contents($path_top);
	}
	
	if(!empty($el['atts']['row_shape_type_bottom'])){
		$path_bottom = PAGELAYER_DIR.'/images/shapes/'.$el['atts']['row_shape_type_bottom'].'-bottom.svg';
		$el['atts']['svg_bottom'] = file_get_contents($path_bottom);
	}
}

// Column Handler
function pagelayer_sc_col(&$el){
	
	// Add the default col class
	$el['classes'][] = 'pagelayer-col';
	
	//return do_shortcode($el['content']);
	
	pagelayer_bg_video($el);
	
}

// Just for BG handling
function pagelayer_bg_video(&$el){
	
	if(empty($el['tmp']['bg_video_src-url'])){
		return false;
	}
	
	// Get the video URL for the iframe
	$iframe_src = pagelayer_video_url($el['tmp']['bg_video_src-url']);
	
	$source = filter_var($el['tmp']['bg_video_src-url'], FILTER_SANITIZE_URL);
	$source = str_replace('&amp;', '&', $source);
	$url = parse_url($source);

	$youtubeRegExp = '/youtube\.com|youtu\.be/is';
	$vimeoRegExp = '/vimeo\.com/is';
	
	if (!empty($source)) {
		
		if (preg_match($youtubeRegExp, $source)) {
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
			
			$el['atts']['vid_src'] = '<iframe src="'.$iframe_src.'?autoplay=1&controls=0&showinfo=0&rel=0&loop=1&autohide=1&playlist='.$videoId.'" allowfullscreen="1" webkitallowfullscreen="1" mozallowfullscreen="1" frameborder="0"></iframe>';
			
		} else if (preg_match($vimeoRegExp, $source)) {
			
			$el['atts']['vid_src'] = '<iframe src="'.$iframe_src.'?background=1&autoplay=1&loop=1&byline=0&title=0" allowfullscreen="1" webkitallowfullscreen="1" mozallowfullscreen="1" frameborder="0"></iframe>';
			
		}else{
			
			$el['atts']['vid_src'] = '<video autoplay loop>'.
					'<source src="'.$iframe_src.'" type="video/mp4">'.
				'</video>';
			
		}
	}
}

// Heading
function pagelayer_sc_heading($atts, $content = '', $tag = ''){
	//return '<div '.pagelayer_create_sc($tag, $atts, 'pagelayer-text').'>'.$content.'</div>';
}

// Text
function pagelayer_sc_text($atts, $content = '', $tag = ''){
	//return '<div '.pagelayer_create_sc($tag, $atts, 'pagelayer-text').'>'.$content.'</div>';
}

// Rich Text Handler
function pagelayer_sc_code($atts, $content = '', $tag = ''){
	//return '<div '.pagelayer_create_sc($tag, $atts, 'pagelayer-text').'>'.$content.'</div>';
}

// List Handler
function pagelayer_sc_list($atts, $content = '', $tag = ''){
	return;
	$items = [];
	$list = $list_type = $icon_type = $icon_color = $text_color = $ul = $ol = $type = '';
	$i_item = '';
	if($atts['items']){
		$items = preg_split('/\r\n|\r|\n/', ($atts['items']));
	}
	
	$list_type = $atts['list_type'];
	$ul = array('circle', 'disc', 'square', 'armenian', 'georgian');
	$ol = array('decimal', 'decimal-leading-zero', 'lower-latin', 'lower-roman', 'lower-greek', 'upper-latin', 'upper-roman');
	
	$icon_type = $atts['icon'];
	$icon_color = $atts['icon_color'];
	$text_color = $atts['text_color'];
	
	if(in_array($list_type, $ul)){
		
		$type = 'ul';
	}
	
	if(in_array($list_type, $ol)){
		$type = 'ol';
	}
	
	if($list_type == 'icon'){
		$type = 'ul';
		$i_item = '<i class="'.$icon_type .'" style="color:'. $icon_color .'"></i>';
	}
	if($list_type == 'none'){
		$type = 'undefined';
	}
	
	$list = '<'. $type .' class="pagelayer-list-item">';
	
	foreach($items as $x){
		$list .= '<li class="pagelayer-list-'.$list_type.'" >'. $i_item .'<span style="color:'. $text_color .'">'.$x.'</span></li>';
		
	}
	
	$list .= '</'. $type .'>';
	
	
	return '<div '.pagelayer_create_sc($tag, $atts, 'pagelayer-list').'>'.$list.'</div>';
}

// Image Handler
function pagelayer_sc_image(&$el){
	
	// Decide the image URL
	$el['atts']['func_id'] = @$el['tmp']['id-'.$el['atts']['id-size'].'-url'];
	$el['atts']['func_id'] = empty($el['atts']['func_id']) ? @$el['tmp']['id-full-url'] : $el['atts']['func_id'];
	
	// What is the link ?
	if(!empty($el['atts']['link_type'])){
		
		// Custom url
		if($el['atts']['link_type'] == 'custom_url'){
			$el['atts']['func_link'] = $el['atts']['link'];
		}
		
		// Link to the media file itself
		if($el['atts']['link_type'] == 'media_file'){
			$el['atts']['func_link'] = $el['atts']['func_id'];
		}
		
		// Lightbox
		if($el['atts']['link_type'] == 'lightbox'){
			$el['atts']['func_link'] = $el['atts']['func_id'];
		}
		
	}
	
	//pagelayer_print($el);
	
}

// Image Slider Handler
function pagelayer_sc_image_slider(&$el){
	
	$ids = explode(',', $el['atts']['ids']);
	$urls = [];
	$all_urls = [];
	$final_urls = [];
	$ul = [];
	$size = $el['atts']['size'];
	
	// Make the image URL
	foreach($ids as $k => $v){
		
		$image = pagelayer_image($v);
		
		$final_urls[$v] = empty($image[$size.'-url']) ? @$image['full-url'] : $image[$size.'-url'];
		
		$urls['i'.$v] = @$image['full-url'];
		
		foreach($image as $kk => $vv){
			$si = strstr($kk, '-url', true);
			if(!empty($si)){
				$all_urls['i'.$v][$si] = $vv;
			}
		}
		
		$li = '<li class="pagelayer-slider-item">';
		
		// Any Link ?
		if(!empty($el['atts']['link_type'])){
			$link = ($el['atts']['link_type'] == 'media_file' ? $final_urls[$v] : @$el['atts']['link']);
			$li .= '<a href="'.$link.'">';
		}
		
		// The Image
		$li .= '<img src="'.$final_urls[$v].'">';
		
		if(!empty($el['atts']['link_type'])){
			$li .= '</a>';
		}
		
		$li .= '</li>';
		
		$ul[] = $li;
		
	}
	
	//pagelayer_print($urls);
	//pagelayer_print($final_urls);
	//pagelayer_print($all_urls);
	
	// Make the TMP vars
	if(!empty($urls)){
		$el['tmp']['ids-urls'] = json_encode($urls);
		$el['tmp']['ids-all-urls'] = json_encode($all_urls);
		$el['atts']['ul'] = implode('', $ul);
	
		// Which arrows to show
		if(in_array(@$el['atts']['controls'], ['arrows', 'none'])){
			$el['attr'][] = ['.pagelayer-image-slider-ul' => 'data-pager="false"'];
		}
		
		if(in_array(@$el['atts']['controls'], ['pager', 'none'])){
			$el['attr'][] = ['.pagelayer-image-slider-ul' => 'data-controls="false"'];
		}
	}
	
};

//Grid Gallery Handler
function pagelayer_sc_grid_gallery(&$el){
	
	$ids = explode(',', $el['atts']['ids']);
	$urls = [];
	$all_urls = [];
	$final_urls = [];
	$ul = [];
	$size = $el['atts']['size'];
	$i = 0;
	$col = $el['atts']['columns'];
	$gallery_rand = 'gallery-id-'.floor((rand() * 100) + 1);
	
	$ul[] = '<ul class="pagelayer-grid-gallery-ul">';
	// Make the image URL
	foreach($ids as $k => $v){
		
		$image = pagelayer_image($v);
		
		$final_urls[$v] = empty($image[$size.'-url']) ? @$image['full-url'] : $image[$size.'-url'];
		
		$urls['i'.$v] = @$image['full-url'];
		$links['i'.$v] = @$image['link'];
		$titles['i'.$v] = @$image['title'];
		$captions['i'.$v] = @$image['caption'];
		
		foreach($image as $kk => $vv){
			$si = strstr($kk, '-url', true);
			if(!empty($si)){
				$all_urls['i'.$v][$si] = $vv;
			}
		}
		
		if(($i % $col) == 0 && $i != 0 ){
			$ul[] = '</ul><ul class="pagelayer-grid-gallery-ul">';
		}
		
		$li = '<li class="pagelayer-gallery-item" >';
		
		if(empty($el['atts']['link_to'])){
			$li .= '<div>';
		}
		
		// Any Link ?
		if(!empty($el['atts']['link_to']) &&  $el['atts']['link_to'] == 'media_file'){
			$link = ($el['atts']['link_to'] == 'media_file' ? $final_urls[$v] : @$el['atts']['link']);
			$li .= '<a href="'.$link.'" class="pagelayer-ele-link">';
		}
		
		// Any Link ?
		if(!empty($el['atts']['link_to']) &&  $el['atts']['link_to'] == 'attachment' ){
			$link = $image['link'];
			$li .= '<a href="'.$link.'" class="pagelayer-ele-link">';
		}
		
		if(!empty($el['atts']['link_to']) && $el['atts']['link_to'] == 'lightbox'){			
			$li .= '<a href="'.$image['full-url'].'" data-lightbox-gallery="'.$gallery_rand.'" alt="'.$image['alt'].'" class="pagelayer-ele-link" pagelayer-grid-gallery-type="'.$el['atts']['link_to'].'">';
		}
		// The Image
		$li .= '<img src="'.$final_urls[$v].'" title="'.$image['title'].'" alt="'.$image['alt'].'">';
		
		if(!empty($el['atts']['caption'])){
			$li .= '<span class="pagelayer-grid-gallery-caption">'.$image['caption'].'</span>';
		}
		
		if(!empty($el['atts']['link_to'])){
			$li .= '</a>';
		}
		
		if(empty($el['atts']['link_to'])){
			$li .= '</div>';
		}
		
		$li .= '</li>';
		
		$ul[] = $li;
		$i++;
	}
	
	$ul[] = '</ul>';
	
	//pagelayer_print($urls);
	//pagelayer_print($final_urls);
	//pagelayer_print($all_urls);
	
	// Make the TMP vars
	if(!empty($urls)){
		$el['tmp']['ids-urls'] = json_encode($urls);
		$el['tmp']['ids-all-urls'] = json_encode($all_urls);
		$el['tmp']['ids-all-links'] = json_encode($links);
		$el['tmp']['ids-all-titles'] = json_encode($titles);
		$el['tmp']['ids-all-captions'] = json_encode($captions);
		$el['atts']['ul'] = implode('', $ul);
		$el['tmp']['gallery-random-id'] = $gallery_rand;
	
	}
}

// Image Handler
function pagelayer_sc_audio(&$el){
	
	return;
	
	$el['atts']['a_url'] = '';

	if ($el['atts']['source'] == 'external'){
		$el['atts']['a_url'] = $el['atts']['url'];
	}
	
	if ($el['atts']['source'] == 'library'){
		
		$el['atts']['a_url'] = wp_get_attachment_url($el['atts']['id']); 
	}
	if(!empty($el['atts']['a_url'])){
		
		$filename=$el['atts']['a_url'];
		
		//Get the file extension 
		
		$extension = pathinfo($filename, PATHINFO_EXTENSION);
	

		//Create source tag according to audio file
		switch($extension){
			
			default:
			case 'mp3':
				$el['atts']['a_type'] = 'audio/mpeg';
				break;
			
			case 'ogg':
				$el['atts']['a_type']= 'audio/ogg';
				break;
			
			case 'wav':
				$el['atts']['a_type'] = 'audio/wav';
				break;
		}
	}

	 if(!empty($el['atts']['a_url']) && !empty($el['atts']['a_type'])){
		$el['attr'][]= ['source' => 'src="{{a_url}}'];
		$el['attr'][]= ['source' => 'type="{{a_type}}'];
	} 

}

// Social Share Handler
function pagelayer_sc_share(&$el){
	
	$labelList = array(
		'Facebook' => array(
			'icons' => array('facebook', 'facebook-official', 'facebook-square'),
			'url' => 'https://www.facebook.com/sharer/sharer.php?u='
		),
		'Twitter' => array(
			'icons' => array('twitter', 'twitter-square'),
			'url' => 'https://twitter.com/share?url='
		),
		'Google+' => array(
			'icons' => array('google-plus', 'google-plus-square'),
			'url' => 'https://plus.google.com/share?url='
		),
		'Instagram' => array(
			'icons' => array('instagram'),
			'url' => ''
		),
		'Linkedin' => array(
			'icons' => array('linkedin', 'linkedin-square'),
			'url' => 'https://www.linkedin.com/shareArticle?url='
		),
		'pinterest' => array(
			'icons' => array('pinterest', 'pinterest-p', 'pinterest-square'),
			'url' => '//www.pinterest.com/pin/create/button/?url='
		),
		'Reddit' => array(
			'icons' => array('reddit-alien', 'reddit-square', 'reddit'),
			'url' => 'https://reddit.com/submit?url='
		),
		'Skype' => array(
			'icons' => array('skype'),
			'url' => ''
		),
		'Stumbleupon' => array(
			'icons' => array('stumbleupon'),
			'url' => 'https://www.stumbleupon.com/submit?url='
		),
		'Telegram' => array(
			'icons' => array('telegram'),
			'url' => 'https://t.me/share/url?url='
		),
		'Tumblr' => array(
			'icons' => array('tumblr', 'tumblr-square'),
			'url' => 'https://www.tumblr.com/share/link?url='
		),
		'VK' => array(
			'icons' => array('vk'),
			'url' => 'http://vk.com/share.php?url='
		),
		'Weibo' => array(
			'icons' => array('weibo'),
			'url' => 'http://service.weibo.com/share/share.php?url='
		),
		'WhatsApp' => array(
			'icons' => array('whatsapp'),
			'url' => 'whatsapp://send?text='
		),
		'WordPress' => array(
			'icons' => array('wordpress'),
			'url' => 'https://wordpress.com/press-this.php?u='
		),
		'Xing' => array(
			'icons' => array('xing', 'xing-square'),
			'url' => 'https://www.xing.com/spi/shares/new?url='
		),
		'Delicious' => array(
			'icons' => array('delicious'),
			'url' => 'https://delicious.com/save?v=5&noui&jump=close&url='
		),
		'Dribbble' => array(
			'icons' => array('dribbble'),
			'url' => ''
		)
	);
		
	if(!empty($el['atts']['text'])){
		$el['atts']['icon_label'] = $el['atts']['text'];
	}else{
		foreach($labelList as $key => $val){
			if(in_array($el['atts']['icon'], $val['icons'])){
				$el['atts']['icon_label'] = $key;
				break;
			}
		}
	}
	
	foreach($labelList as $key => $val){
		if(in_array($el['atts']['icon'], $val['icons'])){
			$el['atts']['social_url'] = $val['url'].$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];;
			break;
		}
	}
}

// Posts Grid
function pagelayer_sc_wp_posts_grid($atts, $content = '', $tag = ''){
	
	$args = array(
			'numberposts' => -1,
			'post_type' => 'post',
			'post_status' => array('publish', 'pending', 'draft', 'future', 'private', 'inherit', 'trash')
	);     
	$all_posts = get_posts($args);
	
	$html = '<div '.pagelayer_create_sc($tag, $atts, 'pagelayer-posts-grid').'>';
	
	//pagelayer_print($all_posts);
	foreach($all_posts as $pk => $pv){
		$post_link = get_permalink($pv->ID);
		$html .= '<div>
			<h2><a href="'.$post_link.'">'.$pv->post_title.'</a></h2>
			<p>'.date('F jS, Y', strtotime($pv->post_date)).' | Published by <a href="'.site_url('author/'.get_the_author_meta('user_login', $pv->post_author)).'">'.get_the_author_meta('display_name', $pv->post_author).'</a></p>
			<p>'.do_shortcode($pv->post_content).'</p>
			<p><a href="'.$post_link.'">Read More</a></p>
		</div>';
	}
	
	$html .= '</div>';
	
	return $html;
}

// Video Handler
function pagelayer_sc_video(&$el){
	
	$el['atts']['video_overlay_image-url'] = empty($el['tmp']['video_overlay_image-'.$el['atts']['custom_size'].'-url']) ? $el['tmp']['video_overlay_image-url'] : $el['tmp']['video_overlay_image-'.$el['atts']['custom_size'].'-url'];
	$el['atts']['video_overlay_image-url'] = empty($el['atts']['video_overlay_image-url']) ? $el['atts']['video_overlay_image'] : $el['atts']['video_overlay_image-url'];
	
	// Get the video URL for the iframe
	$el['atts']['vid_src'] = pagelayer_video_url($el['tmp']['src-url']);
	
	if($el['atts']['autoplay'] == "true"){
		$el['atts']['vid_src'] .="?&autoplay=1";
	}else{
		$el['atts']['vid_src'] .="?&autoplay=0";
	}

	if($el['atts']['mute'] == "true"){
		$el['atts']['vid_src'] .="&mute=1";
	}else{
		$el['atts']['vid_src'] .="&mute=0";
	}

	if($el['atts']['loop'] == "true"){
		$el['atts']['vid_src'] .="&loop=1";	
	}else{
		$el['atts']['vid_src'] .="&loop=0";
	}

	$el['tmp']['ele_id'] = $el['id'];
	
}

// Video slider items Handler
function pagelayer_sc_video_slider(&$el){

	$pager = (!empty($el['atts']['slider_pager']))? 'true': 'false';
	$loop = (!empty($el['atts']['slider_loop']))? 'true': 'false';
	$autoplay = (!empty($el['atts']['autoplay']) || !($el['atts']['autoplay']))? 'true' : 'false';
	$slideshow_speed = intval($el['atts']['slideshow_speed']);
	$slideshow_start = intval($el['atts']['slideshow_start']);
	 
	echo "<script type='text/javascript'>
		jQuery(document).ready(function(){
			jQuery('".$el['selector']." .pagelayer-video-slider-holder').slippry({
				elements: 'div.pagelayer-video',
				auto: ".$autoplay.",
				speed: ".$slideshow_speed.",
				transition: '".$el['atts']['slider_transition']."',
				preload: '".$el['atts']['slider_preload']."',
				pager: ".$pager.",
				start : ".$slideshow_start.",
				loop : ".$loop.",
				
			});
		});
	</script>";
}

// Splash Handler
function pagelayer_sc_splash(&$el){
	
	$delay = intval($el['atts']['delay']);
	echo '<script type="text/javascript">
jQuery(document).ready(function(){
	if("'.$el['atts']['display'].'" == "once"){
	
		if (!sessionStorage.isVisited) {
			sessionStorage.isVisited = "true";
			jQuery("[pagelayer-id='.$el['id'].'] .pagelayer-splash-container").delay('.$delay.').fadeIn();
		}
	}else{
		jQuery("[pagelayer-id='.$el['id'].'] .pagelayer-splash-container").delay('.$delay.').fadeIn();
	}
			
	jQuery("[pagelayer-id='.$el['id'].']  .pagelayer-splash-close").on("click", function(){
		jQuery("[pagelayer-id='.$el['id'].'] .pagelayer-splash-container").fadeOut();
	});	
});
</script>';

}

// Shortcodes Handler
function pagelayer_sc_shortcodes(&$el){
	$el['tmp']['shortcode'] = do_shortcode($el['atts']['data']);
}

// Shortcodes Handler
function pagelayer_sc_wp_widgets(&$el){
	
	global $wp_registered_sidebars;
	
	$data = '';	
	foreach($wp_registered_sidebars as $v){
		if($el['atts']['sidebar'] == $v['id']){
			ob_start();
			dynamic_sidebar($v['id']);
			$data = ob_get_clean();
		}
	}
	
	$el['tmp']['data'] = $data;
}

// Testimonial Handler
function pagelayer_sc_testimonial(&$el){
	
	$el['atts']['func_image'] = @$el['tmp']['avatar-'.$el['atts']['custom_size'].'-url'];
	$el['atts']['func_image'] = empty($el['atts']['func_image']) ? @$el['tmp']['avatar-full-url'] : $el['atts']['func_image'];
	
	if(!empty($image)){
		foreach($image as $k => $v){
			$el['tmp']['avatar-'.$k] = $v;
		}
	}
	
}

// Service Handler
function pagelayer_sc_service(&$el){
	
	if(!empty($el['atts']['service_image'])){		
		$el['atts']['func_image'] = @$el['tmp']['service_image-'.$el['atts']['service_image_size'].'-url'];
		$el['atts']['func_image'] = empty($el['atts']['func_image']) ? @$el['tmp']['service_image-full-url'] : $el['atts']['func_image'];
	}
}

// Primary menu Handler 
function pagelayer_sc_wp_menu(&$el){

	$el['atts']['nav_menu'] = wp_nav_menu( array(
		'menu'   => wp_get_nav_menu_object($el['atts']['nav_list']),
		'menu_id' => $el['atts']['nav_list'],
		//'theme_location' => 'primary',
		//'menu_class'	 => 'primary-menu',
		'echo'	 => false,
	) );
}

// Post Navigation Handler
function pagelayer_sc_post_nav(&$el){
	
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
}

// Comments Handler
function pagelayer_sc_post_comment(&$el){
	global $post;
	
	// Is it custom ?
	if($el['atts']['post_type'] == 'custom' && !empty($el['atts']['post_id'])){
		$orig_post = $post;
		$post = get_post($el['atts']['post_id']);
	}
	
	$post_id = $post->ID;
	//echo $post_id.' - '.$el['atts']['post_id'];
	
	if ( comments_open($post_id) || get_comments_number($post_id) ) {
		
		// Handel comments template echo  
		ob_start();
		comments_template();
		
		$el['atts']['post_comment'] =  '<div class="pagelayer-comments-template">'.ob_get_clean().'</div>';		
	}else{
		$el['atts']['post_comment'] = '<div class="pagelayer-comments-close">
			<h2>Comments are closed!</h2>
		</div>';
	}
	
	if(!empty($orig_post)){
		$post = $orig_post;
	}
	
}

// post navigation Handler
function pagelayer_sc_post_info_list(&$el){
	
	$el['atts']['post_info_content'] ='';

	switch($el['atts']['type']){
		case 'author':
		
			$el['atts']['link'] = get_author_posts_url( get_the_author_meta( 'ID' ) );
			$el['atts']['avatar_url'] = get_avatar_url( get_the_author_meta( 'ID' ), 96 );
			$el['atts']['post_info_content'] = get_the_author_meta( 'display_name' );
			
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

			$el['atts']['post_info_content'] = get_the_time( $format[ $el['atts']['date_format'] ] );
			$el['atts']['link'] = get_day_link( get_post_time( 'Y' ), get_post_time( 'm' ), get_post_time( 'j' ) );
				
			break;

		case 'time':
		
			$format = [
				'default' => 'g:i a',
				'0' => 'g:i a',
				'1' => 'g:i A',
				'2' => 'H:i',
				'custom' =>  empty( $el['atts']['time_format_custom'] ) ? 'F j, Y' : $el['atts']['time_format_custom'],
			];
			$el['atts']['post_info_content'] = get_the_time( $format[ $el['atts']['time_format'] ] );
			
			break;

		case 'comments':
		
			if (comments_open()) {
				$el['atts']['post_info_content'] = (int) get_comments_number();
				$el['atts']['link'] = get_comments_link();
			}
			
			break;

		case 'terms':
		
			$taxonomy = $el['atts']['taxonomy'];
			$terms = wp_get_post_terms( get_the_ID(), $taxonomy );
			foreach ( $terms as $term ) {
					$el['atts']['post_info_content'] .= ' <a href="'. get_term_link( $term ) .'"> '. $term->name .' </a>';
			}
			
			$el['atts']['info_link'] = '';
			break;

		case 'custom':
		
			$el['atts']['post_info_content'] = $el['atts']['type_custom'];
			$el['atts']['link'] = $el['atts']['info_custom_link'];

			break;
	}
	

}

/*pagelayer_print($atts);
pagelayer_print($content);
die();*/

/////////////////////////////////////
// Miscellaneous Shortcode Functions
/////////////////////////////////////

// The font family list
function pagelayer_font_family(){
	return array(
		'arial' => 'Arial',				
		'terminal' => 'Terminal'
	);
}

// Supported Icons
function pagelayer_icon_class_list(){
	return array();
}

// The types of Posts
function pagelayer_post_types($page = false){
	
	// Get the types
	$args = array('public' => TRUE);	
	$types = get_post_types($args, 'objects');
	
	// Unset Page if not required
	if($page == false){
		unset($types['page']);
	}
	
	// Remove Attachment types !
	unset($types['attachment']);
	
	foreach($types as $name => $type){
		$return[$name] = $type->labels->singular_name;
	}
	
	return $return;
}

// Get Taxonomies
function pagelayer_tax_list($item, $page = false){
	
	// Get types
	$types = pagelayer_post_types($page);
	
	// Loop thru
	foreach($types as $slug => $label){
		
		// Get the items
		$items = get_object_taxonomies($slug, 'objects');
		
		foreach($items as $name => $v) {
			if(!isset($taxonomies[$name])){
				$taxonomies[$name] = array('label' => $v->labels->singular_name, 'posttypes' => array($label));
			}else{
				$taxonomies[$name]['posttypes'][] = $label;
			}
		}			
	}
	
	// Make it simple
	foreach($taxonomies as $k => $v){
		$taxonomies[$k] = $v['label'].' ('.implode(', ', $v['posttypes']).')';
	}
	
	$pos = array_search($item, array_keys($taxonomies));
	if(!empty($pos)) {
		$cut = array_splice($taxonomies, $pos, 1);
		$taxonomies = $cut + $taxonomies;
	}

	return $taxonomies;
}

// Get all posts and pages list
function pagelayer_get_posts($args){
	
	if(empty($args)){
		$args = array_keys(pagelayer_post_types(true));
	}
	
	$posts_list = array();
	
	// Get type
	foreach($args as $p){
		
		// Create post list
		foreach(get_posts(['post_type' => $p]) as $post){
			$posts_list[$post->ID] = $post->post_title;
		}
	}
	
	return $posts_list;
}

// Get Menu List()
function pagelayer_get_menu_list($return_def = false){

	$menus = wp_get_nav_menus();
	$nav_menu = array();

	$default = $menus[0]->term_id;

	foreach ( $menus as $menu ) {
	$nav_menu[$menu->term_id] = $menu->name;

		if($default > $menu->term_id){
			$default = $menu->term_id;
		}
	}
	
	if($return_def){
		return $default;
	}
	
	return $nav_menu;
	
}