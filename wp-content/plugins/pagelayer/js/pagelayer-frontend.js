/*
PAGELAYER
http://pagelayer.com/
(c) Pagelayer Team
*/

var pagelayer_doc_width;

// Things to do on document load
jQuery(document).ready(function(){
	
	// Current width
	pagelayer_doc_width = jQuery(document).width();
	
	// Rows
	jQuery('.pagelayer-row-stretch-full').each(function(){
		pagelayer_pl_row_full(jQuery(this));
	});
	
	// Setup any sliders
	pagelayer_pl_image_slider();
	
	jQuery('.pagelayer-accordion').each(function(){
		pagelayer_pl_accordion(jQuery(this));
	});
	
	jQuery('.pagelayer-collapse').each(function(){
		pagelayer_pl_collapse(jQuery(this));
	});
	
	jQuery('.pagelayer-tabs').each(function(){
		pagelayer_pl_tabs(jQuery(this));
	});
	
	jQuery('.pagelayer-video').each(function(){
		pagelayer_pl_video(jQuery(this));
	});
	
	jQuery('.pagelayer-image').each(function(){
		pagelayer_pl_image(jQuery(this));
	});
	
	jQuery('.pagelayer-grid_gallery').each(function(){
		pagelayer_pl_grid_lightbox(jQuery(this));
	});
	
	jQuery('.pagelayer-row, .pagelayer-col>.pagelayer-col').each(function(){
		pagelayer_pl_row_video(jQuery(this));
	});
	
	jQuery('.pagelayer-parallax-window img').each(function(){
		pagelayer_pl_row_parallax(jQuery(this));
	});
	pagelayer_stars();

	// We need to call the is visible thing to show the widgets loading effect
	if(jQuery('.pagelayer-counter-content,.pagelayer-progress-container').length > 0){

		// First Call
		pagelayer_counter();
		pagelayer_progress();
		
		jQuery(window).scroll(function() {
			pagelayer_progress();
			pagelayer_counter();
		});
	}
	
	new WOW({boxClass:'pagelayer-wow'}).init();
	
	// For Pagelayer Pro
	jQuery('.pagelayer-image_hotspot').each(function(){
		pagelayer_image_hotspot(jQuery(this));
	});
	
	jQuery('.pagelayer-countdown').each(function(){
		pagelayer_countdown(jQuery(this));
	});
	
	jQuery('.pagelayer-chart').each(function(){
		pagelayer_chart(jQuery(this));
	});
	
	jQuery('.pagelayer-table').each(function(){
		pagelayer_table(jQuery(this));
	});
	
	jQuery('.pagelayer-wp_menu').each(function(){
		pagelayer_primary_menu(jQuery(this));
	});
	
	jQuery('.pagelayer-search').each(function(){
		pagelayer_search_form(jQuery(this));
	});
	
});

// For automatic row change
jQuery(window).resize(function() {
	
	var new_vw = jQuery(document).width();
	
	if(new_vw == pagelayer_doc_width){
		return false;
	}
	
	pagelayer_doc_width = new_vw;
	
	// Remove style
	jQuery('.pagelayer-row-stretch-full').removeAttr('style');
	
	// Set a timeout to prevent bubbling
	setTimeout(function(){
		
		jQuery('.pagelayer-row-stretch-full').each(function(){
			pagelayer_pl_row_full(jQuery(this));
		});
	
	}, 200);
	
});

// Check if element is visible
function pagelayer_isVisible(ele) {
	
	var offset = jQuery(window).height();
	var viewTop = window.pageYOffset;
	var viewBottom = viewTop + offset - Math.min(ele.height(), ele.innerHeight());
	var top = ele.offset().top;
	var bottom = top + ele.innerHeight();
	
	if(top <= viewBottom && bottom >= viewTop){
		return true;
	}
	
	return false;
}
	  
// Row background video and parallax
function pagelayer_pl_row_video(jEle){
	
	var vEle = jEle.find('.pagelayer-background-video');
	
	var setup = vEle.attr('pagelayer-setup');
	if(setup && setup.length > 0){
		return true;
	}

	var frame_width = vEle.width();
	var frame_height = (frame_width/100)*56.25;
	var height = vEle.height();
	
	if(frame_height < height){
		
		frame_height = height;
		
	}
	
	vEle.children().css({'width':frame_width+'px','height':frame_height+'px'});
	
	vEle.attr('pagelayer-setup', 1);
		
}

// Row background parallax
function pagelayer_pl_row_parallax(jEle){
	
	//Parallax background
	var setup = jEle.attr('pagelayer-setup');
	if(setup && setup.length > 0){
		return true;
	}
	
	new simpleParallax(jEle);
	jEle.attr('pagelayer-setup', 1);
}

// Adjust rows
function pagelayer_pl_row_full(jEle){
	
	// Get current width
	var vw = jQuery('html').width();
	
	// Now give the row the width
	jEle.css({'width': vw, 'max-width': '100vw'});
	
	jEle.offset({left: 0});
	
};

// Modal open
function pagelayer_render_pl_modal(param){
	jQuery(param).parent().parent().find('.pagelayer-modal-content').show();
};

// Modal close
function pagelayer_pl_modal_close(param){
	jQuery(param).parent().hide();
}

// Setup the image slider
function pagelayer_pl_image_slider(){
	
	jQuery('.pagelayer-image_slider').each(function(){
		
		var jEle = jQuery(this);
		var ul = jEle.find('.pagelayer-image-slider-ul');
		
		// No UL is impossible !
		if(ul.length < 1){
			return false;
		}
		
		var setup = ul.attr('pagelayer-setup');
		
		// Already setup ?
		if(setup && setup.length > 0){
			return true;
		}
		
		// Build the options
		var options = {};
		
		// Add required options
		options.adaptiveheight = false;
		options.autohover = false;
		options.loop = false;
		options.autodirection = 'next';
	
		jQuery.each(ul[0].attributes, function(index, att){
			if(att.name.match(/data\-/i)){
				options[att.name.substr(5)] = att.value;
			}
		});
		
		// Make the values correct
		for(var x in options){
			var val = options[x];
			if(val == 'true') val = true;
			if(val == 'false') val = false;
			if(jQuery.isNumeric(val)) val = parseInt(val);
			
			options[x] = val;
		}
		
		// Handle case sensitive issues
		options.autohover = options.autoHover;
		options.adaptiveHeight = options.adaptiveheight;
		options.autoDirection = options.autodirection;
		
		//console.log(options);
		
		// Enable Slippry
		ul.slippry(options);
		
		// Set that we have setup everything
		ul.attr('pagelayer-setup', 1);
		
	});

}

function pagelayer_tab_show(el, pl_id) {

	jQuery('[pagelayer-id='+pl_id+']').closest('.pagelayer-tabcontainer').find('[pagelayer-id]').hide();
	jQuery('[pagelayer-id='+pl_id+']').show();
	
	jQuery(el).parent().find('.pagelayer-tablinks').each(function(){
		jQuery(this).removeClass('active');
	});
	
	jQuery(el).addClass("active");
}

var pagelayer_tab_timers = {};

function pagelayer_pl_tabs(jEle) {
	
	var default_active = '';
	var children = jEle.find('.pagelayer-tabcontainer').find('[pagelayer-id]');
	
	// Loop thru
	children.each(function(){
		var tEle = jQuery(this);
		var pl_id = tEle.attr('pagelayer-id');
		var title = tEle.attr('pagelayer-tab-title') || 'Tab';
		var func = "pagelayer_tab_show(this, '"+pl_id+"')";
		
		var icon = '';
		if(tEle.attr('pagelayer-tab-icon')){
			icon = "fa fa-"+tEle.attr('pagelayer-tab-icon');
		}
		
		// Set the default tab
		if(tEle.attr('pagelayer-default_active')){
			default_active = pl_id;
		}
		
		jEle.find('.pagelayer-tabs-holder').append('<span tab-id="'+pl_id+'" class="pagelayer-tablinks" onclick="'+func+'"> <i class="'+icon+'"></i> <span>'+title+'</span></span>');
	});

	// Set the default tab
	if(default_active.length > 0){
		pagelayer_tab_show(jEle.find('[tab-id='+default_active+']'), default_active);
	// Set the first tab as active
	}else{
		var first_tab = jEle.find('[tab-id]').first();
		pagelayer_tab_show(first_tab, first_tab.attr('tab-id'));
	}

	try{
		clearInterval(pagelayer_tab_timers[jEle.attr('pagelayer-id')])
	}catch(e){};
	
	var rotate = parseInt(jEle.attr('pagelayer-tabs-rotate'));
	
	// Are we to rotate
	if(rotate > 0){
		
		var i= 0;
		pagelayer_tab_timers[jEle.attr('pagelayer-id')] = setInterval(function () {
			
			if(i >= children.length){
				i = 0;
			}
			
			var tmp_pl_ele = jEle.find('.pagelayer-tabcontainer').find('[pagelayer-id]')[i];
			var tmp_btn_ele = jEle.find('.pagelayer-tablinks')[i]
			var tmp_pl_id = jQuery(tmp_pl_ele).attr('pagelayer-id');
			
			jEle.find('.pagelayer-tablinks').each(function(){
				jQuery(this).removeClass('active');
			});
			
			jQuery(tmp_btn_ele).addClass("active");
			pagelayer_tab_show('', tmp_pl_id);
			
			i++;
	   
		}, rotate);
	}
	
}

// Setup the Accordion
function pagelayer_pl_accordion(jEle){
	
	var holder = jEle.find('.pagelayer-accordion-holder');
	var tabs = jEle.find('.pagelayer-accordion-tabs');
	
	if(tabs.length < 1){
		return false;
	}
	
	var setup = tabs.attr('pagelayer-setup');
	
	var icon = 'fa fa-'+holder.attr('data-icon');
	var active_icon = 'fa fa-'+holder.attr('data-active_icon');
	
	tabs.find('span i').attr('class', icon);
	var currentTab = jEle.find('.pagelayer-accordion-tabs.active');
	
	if(currentTab.length < 1){
		jQuery(tabs[0]).addClass('active').next().show('slow');
		jQuery(tabs[0]).find('span i').attr('class', icon);
	}
	
	jQuery(currentTab).addClass('active').next().show('slow');
	jQuery(currentTab).find('span i').attr('class', active_icon);
	
	// Already setup ?
	if(setup && setup.length > 0){
		tabs.unbind('click');
	}

	tabs.click(function(){
		
		var currentTab = jQuery(this);
		
		if(currentTab.hasClass('active')){
			currentTab.removeClass('active').next().hide('slow');;
			currentTab.find('span i').attr('class', icon);
			return true;
		} 
		
		tabs.find('span i').attr('class', icon);
		tabs.removeClass('active').next().hide('slow');
			
		currentTab.addClass('active').next().show('slow');
		currentTab.find('span i').attr('class', active_icon);
		
	});
	
	// Set that we have setup everything
	tabs.attr('pagelayer-setup', 1);
		
}

// Setup the Collapse
function pagelayer_pl_collapse(jEle){
	
	var holder = jEle.find('.pagelayer-collapse-holder');
	var tabs = jEle.find('.pagelayer-accordion-tabs');
		
	if(tabs.length < 1){
		return false;
	}
		
	var setup = tabs.attr('pagelayer-setup');
	var icon = 'fa fa-'+holder.attr('data-icon');
	var active_icon = 'fa fa-'+holder.attr('data-active_icon');
	var activeTabs = jEle.find('.pagelayer-accordion-tabs.active');

	tabs.find('span i').attr('class', icon);
	jQuery(activeTabs).addClass('active').next().show('slow');
	jQuery(activeTabs).find('span i').attr('class', active_icon);
		
	// Already setup ?
	if(setup && setup.length > 0){
		tabs.unbind('click');
	}

	tabs.click(function(){
		
		var currentTab = jQuery(this);
		
		if(currentTab.hasClass('active')){
			currentTab.removeClass('active').next().hide('slow');;
			currentTab.find('span i').attr('class', icon);
			return true;
		}
			
		currentTab.addClass('active').next().show('slow');
		currentTab.find('span i').attr('class', active_icon);
		
	});
	
	// Set that we have setup everything
	tabs.attr('pagelayer-setup', 1);
	
}

// Counter
function pagelayer_counter(){
	
	jQuery('.pagelayer-counter-content').each(function(){
		
		var jEle = jQuery(this);
		
		if(pagelayer_isVisible(jEle)){
			
			var setup = jEle.attr('pagelayer-setup');
			
			// Already setup ?
			if(setup && setup.length > 0){
				return true;
			}
			
			var options = {};
			options['duration'] = jEle.children('.pagelayer-counter-display').attr('pagelayer-counter-animation-duration');
			options['delimiter'] = jEle.children('.pagelayer-counter-display').attr('pagelayer-counter-seperator-type');
			options['toValue'] = jEle.children('.pagelayer-counter-display').attr('pagelayer-counter-last-value');					
			jEle.children('.pagelayer-counter-display').numerator( options );
		
			// Set that we have setup everything
			jEle.attr('pagelayer-setup', 1);
			
		}
	});
}

function pagelayer_progress(){
	jQuery('.pagelayer-progress-container').each(function(){
		var jEle = jQuery(this);
		
		if(pagelayer_isVisible(jEle)){
			
			var setup = jEle.attr('pagelayer-setup');
			if(setup && setup.length > 0){
				return true;
			}
			
			var progress_width = jEle.children('.pagelayer-progress-bar').attr('pagelayer-progress-width');
			if(progress_width == undefined){
				progress_width = "1";
			}
			
			var width = 0;
			var interval;
			
			var progress = function(){
				if (width >= progress_width) {
					clearInterval(interval);
				} else {
					width++;
					jEle.children('.pagelayer-progress-bar').css('width', width + '%'); 
					jEle.find('.pagelayer-progress-percent').text(width * 1  + '%');
				}
			}
			interval = setInterval(progress, 30);
			jEle.attr('pagelayer-setup', 1);
			
		}
	});
}

// Dismiss Alert Function
function pagelayer_dismiss_alert(x){
	jQuery(x).parent().parent().fadeOut();
}

// Video light box handler
function pagelayer_pl_video(jEle){
	
	// A tag will be there ONLY if the lightbox is on
	var overlayval = jEle.find('.pagelayer-video-overlay');	
	var a = jEle.find(".pagelayer-video-holder a");
	
	// No lightbox
	if(a.length < 1 && pagelayer_empty(overlayval)){
		return;
	}

	a.nivoLightbox({
		effect: "fadeScale",
	});
	
	jEle.find(".pagelayer-video-holder .pagelayer-video-overlay").on("click", function(ev) {

		var target = jQuery(ev.target);

		if (!target.parent("a").length) {
			jQuery(this).hide();
			jQuery(this).parent().find("#embed_video")[0].src += "?rel=0&autoplay=1";
		}
	});
	
}

// Image light box handler
function pagelayer_pl_image(jEle){
	// A tag will be there ONLY if the lightbox is on
	var a = jEle.find("[pagelayer-image-link-type=lightbox]");
	
	// No lightbox
	if(a.length < 1){
		return;
	}
	
	a.nivoLightbox({
		effect: "fadeScale",
	});
}

function pagelayer_stars(){
	jQuery('.pagelayer-stars-container').each(function(){
		var jEle = jQuery(this);
		var setup = jEle.attr('pagelayer-setup');
		if(setup && setup.length > 0){
			return true;
		}
		var count = jEle.attr('pagelayer-stars-count');
		i = 0;
		var stars = "";
		while(i < count){			
			stars +='<div class="pagelayer-stars-icon pagelayer-stars-empty"><i class="fa fa-star" aria-hidden="true"></i></div>';
			i++;
		}

		jEle.empty();
		jEle.append(stars);
		var starsval = jEle.attr('pagelayer-stars-value');
		starsval = starsval.split('.');		
		var fullstars = starsval[0];
		var value =  starsval[1];
		var halfstar = parseInt(fullstars) + 1;
		var emptystars = parseInt(fullstars) + 2;                     
		jEle.children('.pagelayer-stars-icon').attr("class","pagelayer-stars-icon");
		jEle.children('.pagelayer-stars-icon:nth-child(-n+'+ fullstars +')').addClass('pagelayer-stars-full'); 
		if(value != undefined){
			jEle.children('.pagelayer-stars-icon:nth-child('+ halfstar +')').addClass('pagelayer-stars-'+value);		
		}else{
			jEle.children('.pagelayer-stars-icon:nth-child('+ halfstar +')').addClass('pagelayer-stars-empty');
		}
		jEle.children('.pagelayer-stars-icon:nth-child(n+'+ emptystars +')').addClass('pagelayer-stars-empty'); 		
		jEle.attr('pagelayer-setup', 1);
	});
}

//Grid Gallery Lightbox
function pagelayer_pl_grid_lightbox(jEle){

	// A tag will be there ONLY if the lightbox is on
	var a = jEle.find("[pagelayer-grid-gallery-type=lightbox]");
	
	// No lightbox
	if(a.length < 1){
		return;
	}
	
	a.nivoLightbox({
		effect: "fadeScale",
		keyboardNav: true,
		clickImgToClose: false,
		clickOverlayToClose: true,
	});
}

// PHP equivalent empty()
function pagelayer_empty(mixed_var) {

  var undef, key, i, len;
  var emptyValues = [undef, null, false, 0, '', '0'];

  for (i = 0, len = emptyValues.length; i < len; i++) {
    if (mixed_var === emptyValues[i]) {
      return true;
    }
  }

  if (typeof mixed_var === 'object') {
    for (key in mixed_var) {
      // TODO: should we check for own properties only?
      //if (mixed_var.hasOwnProperty(key)) {
      return false;
      //}
    }
    return true;
  }

  return false;
};

// For Pagelayer Pro
// Show tooltip in image hotspot
function pagelayer_image_hotspot(jEle){
	
	var tooltip_click = jEle.find('.pagelayer-icon-holder').hasClass('pagelayer-hotspots-click');
	
		if(tooltip_click){
			jEle.find('.pagelayer-hotspots-icon-holder').each(function(){
				
				jQuery(this).toggle(function(){
					jQuery(this).find('.pagelayer-tooltip-text').css({'visibility': 'visible'});
				}, function(){
					jQuery(this).find('.pagelayer-tooltip-text').css({'visibility': 'hidden'});
				});

			});
		}
}

var count_int ={};
// Show countdown render
function pagelayer_countdown(jEle){
	
	var expiry_date = jEle.find('.pagelayer-countdown-container').attr('pagelayer-expiry-date');
	var jEle_id = jEle.attr('pagelayer-id');
	
	if(!pagelayer_empty(expiry_date)){
			
		clearInterval(count_int[jEle_id]);
			
		count_int[jEle_id] = setInterval(function() {

			var countDownDate = new Date(expiry_date).getTime();
			var now = new Date().getTime();
			var distance = countDownDate - now;
			
			// Time calculations for days, hours, minutes and seconds
			var days = Math.floor(distance / (1000 * 60 * 60 * 24));
			var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
			var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
			var seconds = Math.floor((distance % (1000 * 60)) / 1000);
			
			jEle.find('.pagelayer-days-count').html(days);
			jEle.find('.pagelayer-hours-count').html(hours);
			jEle.find('.pagelayer-minutes-count').html(minutes);
			jEle.find('.pagelayer-seconds-count').html(seconds);
			
			// If the count down is over, write some text 
			if (distance < 0) {
				clearInterval(count_int[jEle_id]);
				jEle.find('.pagelayer-countdown-container').html("EXPIRED");
			}
		}, 1000);
	}
	
}

// Show Chart render
function pagelayer_chart(jEle){
	
	var pl_id = jEle.attr('pagelayer-id');
	var chart_holder = jEle.find('.pagelayer-chart-holder');
	var chart_type = chart_holder.attr('chart-type');
	var chart_colors = chart_holder.attr('chart-colors') || '';
	var chart_labels = jEle.find('.pagelayer-chart-holder').attr('chart-labels') || '';
	var chart_series = [];
	var tmp_series  = '';
	
	chart_colors = chart_colors.split(',');
	var alphabets = 'abcdefghijklmnopqrstuvwxyz'; 
	
	for(var i =0; chart_colors.length > i; i++){
		var char_at = alphabets.charAt(i);
		
		if(i == 26){i=0;}
			 
		var styles = '[pagelayer-id="'+pl_id+'"] .ct-series-'+char_at+' .ct-bar, .ct-series-'+char_at+' .ct-line, .ct-series-'+char_at+' .ct-point, .ct-series-'+char_at+' .ct-slice-donut {stroke : '+chart_colors[i]+'}'+
		'[pagelayer-id="'+pl_id+'"] .ct-series-'+char_at+' .ct-area, .ct-series-'+char_at+' .ct-slice-donut-solid, .ct-series-'+char_at+' .ct-slice-pie{fill : '+chart_colors[i]+'}';
		
		jEle.find('style')[0].append(styles);
	}
	
	chart_labels = chart_labels.split(',');
	
	jEle.find('.pagelayer-chart-child-holder').find('[chart-series]').each(function(){
		tmp_series = jQuery(this).attr('chart-series');
		tmp_series = tmp_series.split(',');
		chart_series.push(tmp_series);
	});
	
	var data = {
			labels: chart_labels,
			series: chart_series
		};
		
	var options = {};
		
	jQuery.each(chart_holder[0].attributes, function(index, att){
			if(att.name.match(/data\-/i)){
				
				var name = att.name.substr(5);
				
				for(var i = 0; name.length > i; i++){
					var index = name.search("_");
					if(index < 0)break;
					var s_char = name.charAt(index+1);
					var C_char = s_char.toUpperCase();
					name = name.replace("_"+s_char, C_char);
				}
				
				var value = att.value;
				
				if(value == "true"){
					value= true;
				}else if(value == "false"){
					value = false;
				}else if(jQuery.isNumeric(value)){
					value = parseInt(value);
				}
				
				options[name] = value;
			}
	});
		
	//console.log(options);
	
	var responsiveOptions = [];
	
	if(chart_type == "Pie"){
		data.series = tmp_series;
	} 
	
	new Chartist[chart_type]('[pagelayer-id="'+pl_id+'"] .pagelayer-chart-holder', data, options, responsiveOptions);
}

// Show table render
function pagelayer_table(jEle){
	
	var tHolder = jEle.find('.pagelayer-table-holder');
	var dHolder = jEle.find('.pagelayer-data-holder');
	var trEle = dHolder.find(".pagelayer-table_row");
	var tdlength = 0;
	tHolder.empty();
	
	/* trEle.each(function(){
		var tdEle = jQuery(this).find(".pagelayer-table_col").length;
		if(tdlength < tdEle){tdlength = tdEle}
	}) */;
	
	// Add rows
	trEle.each(function(){
		var this_trEle = jQuery(this);
		var tdEle = this_trEle.find(".pagelayer-table_col");
		var style_row = 'color:'+this_trEle.find('.pagelayer-table-row-holder').attr('data-trcolor')+';background-color:'+this_trEle.find('.pagelayer-table-row-holder').attr('data-trbg-color')+'';

		var html = '';
		
		// Add columns
		tdEle.each(function(){
			var td_data_Holder = jQuery(this).find('.pagelayer-col-data');
			var tdata = td_data_Holder.attr('data-td') || '';
			var t_tag = td_data_Holder.attr('data-tag') || '';
			var style_col = 'color:'+td_data_Holder.attr('data-color')+';background-color:'+td_data_Holder.attr('data-bg-color')+'';
			var col_attr = 'style="'+style_col+'" colspan="'+td_data_Holder.attr('data-colspan')+'" rowspan="'+td_data_Holder.attr('data-rowspan')+'"';
			
			html = html+'<'+t_tag +' '+col_attr+'>'+tdata+'</'+t_tag+'>';
		});
		
		/* if(tdlength > tdEle.length){
			
			var extra_td = tdlength - tdEle.length;
			for(var i=0; extra_td >i; i++){
				html = html+'<td></td>';
			}
		} */
		
		tHolder.append('<tr style="'+style_row+'">'+html+'</tr>');
	});
	
}

// Primary Menu Handler - Premium
function pagelayer_primary_menu(jEle){
	
	var container = jEle.find('.pagelayer-wp-menu-container');
	var menu_bar = jEle.find('.pagelayer-primary-menu-bar').find('i.fa');
	var layout = jEle.find('.pagelayer-wp-menu-holder').data('layout');
	var submenu_ind = jEle.find('.pagelayer-wp-menu-holder').data('submenu_ind');
		
	if(layout == 'vertical'){
		menu_bar.hide();
	}
	
	// Menu toggle
	jQuery(menu_bar).unbind('click');
	jQuery(menu_bar).click(function(){
		jQuery(container).toggleClass('pagelayer-togglt-on');
		if(jQuery(container).hasClass('pagelayer-togglt-on')){
			jQuery(this).removeClass('fa-bars');
			jQuery(this).addClass('fa-times');
		}else{
			jQuery(this).addClass('fa-bars');
			jQuery(this).removeClass('fa-times');
		}
	});
	
	// If has sub-menu the as icon
	var after_icons = '<span class="after-icon fa fa-'+submenu_ind+'"></span>';
	jQuery(container).find('ul.menu li ul.sub-menu').parent().children('a').append(after_icons);
	
	// Toggle Sub nav
	var after_icon = jQuery(container).find('ul.menu li.menu-item-has-children .after-icon');
	
	after_icon.unbind('click');
	after_icon.click(function(e){
		e.preventDefault();
		if(window.matchMedia("(max-width: 782px)").matches || layout != 'horizontal'){
			jQuery(this).closest('li').toggleClass('active-sub-menu');
		}else{
			jQuery(this).closest('li').removeClass('active-sub-menu');
		}
	});
	
}

// Search Form handler - Premium
function pagelayer_search_form(jEle){
	
	// In full screen mode set auto complete offscreenBuffering
	jEle.find('.pagelayer-search-full-screen form').attr('autocomplete', 'off');
	
	jEle.find('.pagelayer-search-toggle').click(function(){
		jEle.find('.pagelayer-search-fields').toggleClass('show');
	});
	
	jEle.find('.pagelayer-search-fields').click(function(e){
		 e = window.event || e; 
		if(this === e.target) {
			jQuery(this).removeClass('show');
		}
	});
}
