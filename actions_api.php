<?php
/* ------------------------------ */
/* init scripts and styles */

function doT_shell_enqueue_script()
{   
	//wp_enqueue_script( 'lodash', plugin_dir_url( __FILE__ ) . 'js/lodash.min.js');
	
	$ajax_url = admin_url( 'admin-ajax.php' );
	wp_register_script( 'shell-ajax-script', plugin_dir_url(__FILE__) . 'js/shell-scripts.js', array (), false, true );
	$wp_vars = array(
	  'ajax_url' => admin_url( 'admin-ajax.php' ) ,
	);
	wp_localize_script( 'shell-ajax-script', 'wp_vars', $wp_vars );
	wp_enqueue_script( 'shell-ajax-script', array('lodash') );

	wp_enqueue_script( 'dotjs', plugin_dir_url( __FILE__ ) . 'js/dotjs.min.js' );
 	
 	wp_enqueue_script( 'dragulajs', plugin_dir_url( __FILE__ ) . 'js/dragula.min.js' );
 	wp_enqueue_script( 'flatpickr', plugin_dir_url( __FILE__ ) . 'js/flatpickr.min.js');
 	wp_enqueue_style( 'dragulacss', plugin_dir_url( __FILE__ ) . 'css/dragula.min.css' );
 	//wp_enqueue_style( 'shell-css', plugin_dir_url( __FILE__ ) . 'css/shell-style.css' );
 	wp_enqueue_style( 'modal-css', plugin_dir_url( __FILE__ ) . 'css/modal.css' );
 	wp_enqueue_style( 'spectre-css', plugin_dir_url( __FILE__ ) . 'css/spectre.min.css' );
 	wp_enqueue_style( 'flatpickr-css', plugin_dir_url( __FILE__ ) . 'css/flatpickr.min.css' );
 	

}
add_action('wp_enqueue_scripts', 'doT_shell_enqueue_script');

/* ------------------------------ */
/* doT Application */

function doT_init($attr){

	if(array_key_exists('defs',$attr)){
		$out = createDefinitions($attr['defs']);
		$attr['defs'] = $out;
	}

	if(array_key_exists('templates',$attr)){
		foreach ($attr['templates'] as $key => $value) {
			$out = file_get_contents(ABSPATH.'wp-content/plugins/dot-shell/tpl/'.$value.'.html', FILE_USE_INCLUDE_PATH);
			$attr['templates'][$key] = $out;
		}
	}

	if(array_key_exists('viewdata',$attr)){
		foreach ($attr['viewdata'] as $key => $value) {
			$out = file_get_contents(ABSPATH.'wp-content/plugins/dot-shell/tpl-data/'.$value.'.json', FILE_USE_INCLUDE_PATH);
			
			/* is collection */
			if (strpos($out, '#_collection_#') !== false) {
		    	
		    	$out = json_decode($out,true);
		    	$collection_object = $out['#_collection_#'][0];
		    	unset($out['#_collection_#']);
		    	
		    	$storage_options = $out['options'];		    		
		    	$out['options'] = array();

		    	$__counter = 0;
		    	
		    	foreach ($attr['data'][$key] as $collection_key => $collection_value) {

		    		$collection_object['id'] = $collection_key;
		    		
		    		$tech = json_encode($collection_object);
		    		$tech = str_replace("#_index_#", $__counter, $tech);

		    		$el_counter = 0;
		    		foreach ($attr['data'][$key][$collection_key] as $collection_el_key => $collection_el_value) {

		    			if (strpos($tech, '#_value.'.$collection_el_key.'_#') !== false) {
							$find = '#_value.'.$collection_el_key.'_#';
							$replace = $collection_el_value;
							$tech = str_replace($find, $replace, $tech);
						}

						if (strpos($tech, '%_key.'.$el_counter.'_%') !== false) {
							$find = '%_key.'.$el_counter.'_%';
							$replace = $collection_el_key;
							$tech = str_replace($find, $replace, $tech);
						}

						if (strpos($tech, '%_value.'.$el_counter.'_%') !== false) {
							$find = '%_value.'.$el_counter.'_%';
						 	$replace = $collection_el_value;
							$tech = str_replace($find, $replace, $tech);
						}

						$el_counter++;
		    		}
					
		    		$tech = json_decode($tech,true);
					array_push($out['options'],$tech);
					$__counter++;

		    	}

		   		$out['options'] = array_merge (  $out['options'] , $storage_options);
		    	
		    	
				
			}else{

				/* is not  collection */
				$out = json_decode($out,true);
			}
			
			$attr['viewdata'][$key] = $out;
		}
	}


	return $attr;
}



/* ------------------------------ */
/* Create doT definitions */

function createDefinitions($defs_list){
	$out = array();
	foreach ($defs_list as $value) {
		$out[$value] = file_get_contents(ABSPATH.'wp-content/plugins/dot-shell/tpl-defs/'.$value.'.html', FILE_USE_INCLUDE_PATH);
	}
	return $out;
}

/* ------------------------------ */
/* render Modal */

function modal(){
	$edit = '<div id="showmodal">';
		$edit .= '<div id="modal">';
			$edit .= '<div id="modal-menu"></div>';
			$edit .= '<div id="modal-body"></div>';
			//$edit .= '<div id="closemodal" onclick="close_modal()">x</div>';
			$edit .= '<a href="#closemodal"><div id="closemodal">x</div></a>';
		$edit .= '</div>';
	$edit .= '</div>';
    echo $edit;
}
add_action( 'wp_footer', 'modal' );

/* ------------------------------ */
/* parse UrlData */
function queryToArray($qry){
if($qry){

	$result = array();
	//string must contain at least one = and cannot be in first position
	if(strpos($qry,'=')) {

		if(strpos($qry,'?')!==false) {

			$q = parse_url($qry);
			$qry = $q['query'];
			
		}

	}else {

		return false;

	}

	foreach (explode('&', $qry) as $couple) {
		
		list ($key, $val) = explode('=', $couple);
		$result[$key] = $val;

	}

		return empty($result) ? false : $result;

	}else{

		return null;
	}

}
function dot_prepare_data($post_data){
	$data = json_decode(stripslashes($post_data), true);
	$data['urldata'] = queryToArray($data['urldata']);
	return $data;
}
function dot_send_data($data,$global){
	$GLOBALS[$global]['initdata'] = $data;
	$out = doT_init($GLOBALS[$global]);
	return $out;
}
