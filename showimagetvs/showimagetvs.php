<?php
/**
 * mm_widget_showimagetvs
 * @version 1.2.1 (2014-05-07)
 * 
 * @see README.md
 * 
 * @link https://code.divandesign.biz/modx/mm_widget_showimagetvs
 * 
 * @copyright 2014 DD Group {@link https://DivanDesign.biz }
 */

function mm_widget_showimagetvs(
	$tvs = '',
	$maxWidth = 300,
	$maxHeight = 100,
	$thumbnailerUrl = '',
	$roles = '',
	$templates = ''
){
	if (
		!useThisRule(
			$roles,
			$templates
		)
	){
		return;
	}
	
	global $modx;
	$e = &$modx->Event;
	
	if ($e->name == 'OnDocFormPrerender'){
		//The main js file including
		$output = includeJsCss(
			(
				$modx->config['site_url'] .
				'assets/plugins/managermanager/widgets/showimagetvs/jquery.ddMM.mm_widget_showimagetvs.js'
			),
			'html',
			'jquery.ddMM.mm_widget_showimagetvs',
			'1.0.2'
		);
		
		$e->output($output);
	}else if ($e->name == 'OnDocFormRender'){
		global $mm_current_page;
		
		$output = '';
		
        // Does this page's template use any image TVs? If not, quit now!
		$tvs = tplUseTvs(
			$mm_current_page['template'],
			$tvs,
			'image'
		);
		if ($tvs == false){
			return;
		}
		
		$output .=
			'//---------- mm_widget_showimagetvs :: Begin -----' .
			PHP_EOL
		;
		
		// Go through each TV
		foreach (
			$tvs as
			$tv
		){
			$output .= 
'
$j("#tv' . $tv['id'] . '").mm_widget_showimagetvs({
	thumbnailerUrl: "' . trim($thumbnailerUrl) . '",
	width: ' . intval($maxWidth) . ',
	height: ' . intval($maxHeight) . ',
});
'
			;
		}
		
		$output .=
			'//---------- mm_widget_showimagetvs :: End -----' .
			PHP_EOL
		;
		
		$e->output($output);
	}
}
?>