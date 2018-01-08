<?php
$GLOBALS['app_object'] = array(
	/* excerption tab model */
	'tabs' => array(
		array(
			'name'=>'Posts list',
			'viewDataTarget'=>'postsList',
			'template'=>'mainFormTemplate',
			"action"=>"sGETinit_app"
		),				
		array(
			'name'=>'Media list',
			'viewDataTarget'=>'mediaList',
			'template'=>'mainFormCards',
			"action"=>"sGETinit_app"
		),
		array(
			'name'=>'Example form',
			'viewDataTarget'=>'set1',
			'template'=>'mainFormTemplate',
			"action"=>"sGETinit_app"
		)
	),
	
	/* backend data (dynamic) */
	'data' => array(
		'addPost' => array(),
		'editPost' =>  array(),
		'postsList' => array(),
		'mediaList' => array(),
		'set1' => array(
			"useremail" => array(
				"active" => false,
				"value" => "dadmor@gmail.com"
			)				
		),
		'okPost' => array(),
	),		
	
	/* schema data (static) */	
	'viewdata' => array(
		'postsList' => 'posts-list-view',
		'mediaList' => 'media-list-view',
		'addPost' => 'add-post-view',
		'set1' => 'set1',
		'editPost' => 'edit-post-view',
		'okPost' => 'ok-post-view'
	),
	
	/* templates (static) */
	'templates' => array(
		'mainFormTemplate' => 'main-form-template',
		'mainTabsTemplate' => 'main-tabs-template',
		'mainFormCards' => 'main-form-cards'
	),
	
	/* template parts (static) */
	'defs' => array(
		'action',
		'checkbox',
		'datepicker',
		'fieldset',
		'select',
		'switch',
		'text',
		'textarea',
		'timepicker',
		'label',
		'editor',
		'layoutcard',
		'image'
	)
);

$GLOBALS['app_example_shortcode'] = array(
	'tabs' => null,
	'data' => array(
		'example_shortcode' => array()
		),
	'viewdata' => array(
		'example_shortcode' => 'shortcodes/example-shortcode'
		),
	'templates' => array(
		'mainFormTemplate' => 'main-form-template',
	),
	/* template parts (static) */
	'defs' => array(
		'action',
		'checkbox',
		'datepicker',
		'fieldset',
		'select',
		'switch',
		'text',
		'textarea',
		'timepicker',
		'label',
		'editor',
		'layoutcard',
		'image',
		'action_insert_shortcode'
	)
);
/* ----------------------------------------------------- */
/* ##### APP METHOD RENDER FORMS ###### */

add_action( 'wp_ajax_sGETinit_app', 'sGETinit_app' );
/* run in nonadmin rules */
//add_action( 'wp_ajax_nopriv_sGETinit_app', 'sGETinit_app' );

function sGETinit_app(){

	$data = dot_prepare_data($_POST['data']);

	$GLOBALS['app_object']['data']['editPost'] = dot_editPost($data['urldata']);
	$GLOBALS['app_object']['data']['postsList'] = dot_createPostList();
	$GLOBALS['app_object']['data']['mediaList'] = dot_createMediaList();
	
	wp_send_json( dot_send_data($data,'app_object') );

}

/* ----------------------------------------------------- */
/* ##### APP METHOD SAVE POST ###### */

add_action( 'wp_ajax_sGET_app_savepost', 'sGET_app_savepost' );
//add_action( 'wp_ajax_nopriv_sGET_app_savepost', 'sGET_app_savepost' );
function sGET_app_savepost(){
	
	$data = dot_prepare_data($_POST['data']);

	$my_post = array(
		'post_title'    => wp_strip_all_tags( $data['postdata']['insertpost']['post_title'] ),
		'post_content'  => $data['postdata']['insertpost']['post_content'],
		'post_status'   => 'publish',
		'post_author'   => 1	
	); 
	// Insert the post into the database
	$new_post = wp_insert_post( $my_post );
	$data['postdata']['insert_post_id'] = $new_post;

	wp_send_json( dot_send_data($data,'app_object') );

}

/* ----------------------------------------------------- */
/* ##### APP METHOD EDIT POST ###### */

add_action( 'wp_ajax_sGET_app_editpost', 'sGET_app_editpost' );
//add_action( 'wp_ajax_nopriv_sGET_app_savepost', 'sGET_app_savepost' );
function sGET_app_editpost(){

	$data = dot_prepare_data($_POST['data']);

	$my_post = array(
		'ID' => $data['postdata']['editpost']['ID'],
		'post_title'    => wp_strip_all_tags( $data['postdata']['editpost']['post_title'] ),
		'post_content'  => $data['postdata']['editpost']['post_content']
	); 
	// Insert the post into the database
	$new_post = wp_update_post( $my_post );
	$data['postdata']['edit_post_id'] = $new_post;

	wp_send_json( dot_send_data($data,'app_object') );

}

/* ----------------------------------------------------- */
/* ##### APP METHOD DELETE POST ###### */

add_action( 'wp_ajax_sGET_app_deletepost', 'sGET_app_deletepost' );
//add_action( 'wp_ajax_nopriv_sGET_app_savepost', 'sGET_app_savepost' );
function sGET_app_deletepost(){

	$data = dot_prepare_data($_POST['data']);
	
	wp_delete_post( $data['postdata']['editpost']['ID'] , true );
	$data['postdata']['deleted_post_id'] = $data['postdata']['editpost']['ID'];
	
	wp_send_json( dot_send_data($data,'app_object') );
}

/* ----------------------------------------------------- */
/* ##### APP METHOD DELETE POST ###### */

add_action( 'wp_ajax_sGET_loadShortcodeAction', 'sGET_loadShortcodeAction' );
//add_action( 'wp_ajax_nopriv_sGET_loadShortcodeAction', 'sGET_loadShortcodeAction' );
function sGET_loadShortcodeAction(){

	$data = dot_prepare_data($_POST['data']);

	foreach ( $data['postdata'] as $key => $value) {
		$GLOBALS['app_example_shortcode']['data']['example_shortcode'][$key]=$value;
	}
	
	//wp_delete_post( $data['postdata']['editpost']['ID'] , true );
	//$data['postdata']['deleted_post_id'] = $data['postdata']['editpost']['ID'];
	
	wp_send_json( dot_send_data($data,'app_example_shortcode') );
}



/* ----------------------------------------------------- */
/* ##### all methods ###### */

function dot_createPostList(){
	$postList = array();
	$args = array(
		'posts_per_page' => -1
	);
	$results = get_posts( $args );
	foreach ( $results as $post ) {
	   $postList[$post->ID] = array(
			'id' => $post->ID,
			'post_name' => $post->post_title,
			'thumbnail' => $post->post_date
		);
	}
	return $postList;
};

function dot_createMediaList(){

	$mediaList = array();
	$args = array(
		'posts_per_page' => -1,
		'post_type' => 'attachment',
		'post_status' => null,
    	'post_parent' => null, // any parent
	);
	$results = get_posts( $args );
	foreach ( $results as $post ) {
	   $mediaList[$post->ID] = array(
			'id' => $post->ID,
			'post_name' => $post->post_title,
			'thumbnail' => $post->guid
		);
	}
	return $mediaList;
};

function dot_editPost($attr){
	/* show post data bedore edit */
	global $post;
	$my_post = array('editpost' => get_post( $attr['post_id'] ) );
	return $my_post;
}
