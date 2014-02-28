<?php
/**
 * mm_widget_showimagetvs
 * @version 1.1 (2012-11-13)
 * 
 * @desc A widget for ManagerManager plugin that allows the preview of images chosen in image TVs to be shown on the document editing page.
 * Emulates showimagestv plugin, which is not compatible with ManagerManager.
 * 
 * @uses ManagerManager plugin 0.4.
 * 
 * @param $tvs {comma separated string} - The name(s) of the template variables this should apply to. Default: ''.
 * @param $w {integer} - Preferred maximum width of the preview. Default: 300.
 * @param $h {integer} - Preferred maximum height of the preview. Default: 100.
 * @param $roles {comma separated string} - The roles that the widget is applied to (when this parameter is empty then widget is applied to the all roles). Default: ''.
 * @param $templates {comma separated string} - Id of the templates to which this widget is applied (when this parameter is empty then widget is applied to the all templates). Default: ''.
 * 
 * @link http://code.divandesign.biz/modx/mm_widget_showimagetvs/1.1
 * 
 * @copyright 2012
 */

function mm_widget_showimagetvs($tvs = '', $w = 300, $h = 100, $thumbnailerUrl = '', $roles = '', $templates = ''){
	global $modx, $mm_current_page;
	$e = &$modx->Event;
	
	if ($e->name == 'OnDocFormRender' && useThisRule($roles, $templates)){
		$output = '';
		
		$site = $modx->config['site_url'];
		
		if (isset($w) || isset($h)){
			$w = isset($w) ? $w : 300;
			$h = isset($h) ? $h : 100;
			$style = "'max-width:{$w}px; max-height:{$h}px; margin: 4px 0; cursor: pointer;'";
		}else{
 			$style = '';
		}
		
        // Does this page's template use any image TVs? If not, quit now!
		$tvs = tplUseTvs($mm_current_page['template'], $tvs, 'image');
		if ($tvs == false){
			return;
		}
		
		$output .= "//  -------------- mm_widget_showimagetvs :: Begin ------------- \n";
		
		// Go through each TV
		foreach ($tvs as $tv){
			$new_html = '';
			
			$output .= '
// Adding preview for tv'.$tv['id'].'
$j("#tv'.$tv['id'].'").addClass("imageField").bind("change load", function(){
	var $this = $j(this),
		// Get the new URL
		url = $this.val();
	
	$this.data("lastvalue", url);
	
	url = (url != "" && url.search(/http:\/\//i) == -1) ? ("'.$site.'" + url) : url;
	
			';
			
			// If we have a PHPThumb URL
			if (!empty($thumbnailerUrl)){
				$output .= 'url = "'.$thumbnailerUrl.'?src="+escape(url)+"&w='.$w.'&h='.$h.'"; ' . "\n";
			}
			
			$output .= '
	// Remove the old preview tv'.$tv['id'].'
	$j("#tv'.$tv['id'].'PreviewContainer").remove();
	
	if (url != ""){
		// Create a new preview
		$j("#tv'.$tv['id'].'").parents("td").append("<div class=\"tvimage\" id=\"tv'.$tv['id'].'PreviewContainer\"><img src=\""+url+"\" style=\""+'.$style.'+"\" id=\"tv'.$tv['id'].'Preview\"/></div>");
		
		// Attach a browse event to the picture, so it can trigger too
		$j("#tv'.$tv['id'].'Preview").click(function(){
			BrowseServer("tv'.$tv['id'].'");
		 });
	}
}).trigger("load"); // Trigger a change event on load

			';
		}
		
		$output .= '
		
// Monitor the image TVs for changes
checkImageTVupdates = function(){
	$j(".imageField").each(function(){
		var $this = $j(this);
		if ($this.val() != $this.data("lastvalue")){
			$this.trigger("change");
		}
	});
}

setInterval ("checkImageTVupdates();", 250);

		';
		
		$output .= "//  -------------- mm_widget_showimagetvs :: End ------------- \n";
		
		$e->output($output . "\n");
	}
}
?>