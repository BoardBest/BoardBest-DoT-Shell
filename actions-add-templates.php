<?php

class GDAddTemplates {
	
	 private $debug = 'no data';
	 //private $db;
	 private $urlData;
	 private $postData;
	 private $initData;
    
    function __construct() {
		//global $wpdb;
		//$this->db = $wpdb;
       
		//init
		add_action( 'wp_ajax_sGETinit_sections_templates', array($this, 'build') );
		//actions
		
    }
    
    function getURLData() {
    	$this->initData = dot_prepare_data($_POST['data']);
    	$this->urlData = (object) $this->initData['urldata'];
		$this->postData = (object) $this->initData['postdata'];
    }
    
    function build($debug = ''){
    	
    	$this->getURLData();
		
		$out = doT_init(array(
	
				/* excerption tab model */
				'tabs' => array(					
					
				),
				
				/* backend data (dynamic) */
				'data' => array(
					'templates' => $this-> get_templates(),
				),		
				
				/* schema data (static) */	
				'viewdata' => array(
					'templates' => 'sectionTemplates/templates',					
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
					'image',
					'function'
				),
				'initdata' => $this->initData,
				'debug' => $debug,
				'debugGlobal' => $this->debug,
			));
	
		wp_send_json( $out );
	}

	
	function get_templates(){

		$this->getURLData();
		$data = (array) $this->postData;


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
				'thumbnail' => $post->guid,
				'cell_id' => $this->urlData->cell_id
			);
		}


		$postList = array();
		$args = array(
			'posts_per_page' => -1,
			'post_type' => 'mcetemplates'
		);
		$results = get_posts( $args );
		foreach ( $results as $post ) {
		   $postList[$post->ID] = array(
				'id' => $post->ID,
				'post_name' => $post->post_title,
				'thumbnail' => get_stylesheet_directory_uri().'/inc/kirki-icons/fn_edit_golden-2-1.png',
				'post_content' => urlencode($post->post_content)
			);
		}
	
		return (array)$postList;

	}
}

$a = new GDAddTemplates();

/* routing */
/* #showmodal/a=sGETinit_section_options_bg/active=backgrounds/tpl=mainFormCards */
/* #showmodal/a=sGETinit_section_options_bg/active=listbackgrounds/tpl=mainFormCards */

