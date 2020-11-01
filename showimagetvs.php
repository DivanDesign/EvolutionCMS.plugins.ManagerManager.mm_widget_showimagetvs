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

function mm_widget_showimagetvs($params){
	//For backward compatibility
	if (
		!is_array($params) &&
		!is_object($params)
	){
		//Convert ordered list of params to named
		$params = \ddTools::orderedParamsToNamed([
			'paramsList' => func_get_args(),
			'compliance' => [
				'fields',
				'maxWidth',
				'maxHeight',
				'thumbnailerUrl',
				'roles',
				'templates'
			]
		]);
	}
	
	//Defaults
	$params = \DDTools\ObjectTools::extend([
		'objects' => [
			(object) [
				'fields' => '',
				'maxWidth' => 300,
				'maxHeight' => 100,
				'thumbnailerUrl' => '',
				'roles' => '',
				'templates' => ''
			],
			$params
		]
	]);
	
	if (
		!useThisRule(
			$params->roles,
			$params->templates
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
		$output = '';
		
        //Does this page's template use any image TVs? If not, quit now!
		$params->fields = getTplMatchedFields(
			$params->fields,
			'image'
		);
		
		if ($params->fields === false){
			return;
		}
		
		$output .=
			'//---------- mm_widget_showimagetvs :: Begin -----' .
			PHP_EOL
		;
		
		// Go through each TV
		foreach (
			$params->fields as
			$field
		){
			$output .= 
'
$j("#tv' . $field['id'] . '").mm_widget_showimagetvs({
	thumbnailerUrl: "' . trim($params->thumbnailerUrl) . '",
	width: ' . intval($params->maxWidth) . ',
	height: ' . intval($params->maxHeight) . ',
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