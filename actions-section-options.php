<?php

class GDSectionOptions {
	
	 private $debug = 'no data';
	 //private $db;
	 private $urlData;
	 private $postData;
	 private $initData;
    
    function __construct() {
		//global $wpdb;
		//$this->db = $wpdb;
       
		//init
		add_action( 'wp_ajax_sGETinit_section_options_bg', array($this, 'build') );
		//actions
		add_action( 'wp_ajax_sGET_section_add_background_to_cell', array($this, 'addBackgroundToCell') );
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
					array(
						'name'=>'Backgrounds list',
						'viewDataTarget'=>'backgrounds',
						'template'=>'mainFormCards',
						"action"=>"sGETinit_section_options_bg"
					),
					array(
						'name'=>'Uploaded images',
						'viewDataTarget'=>'listbackgrounds',
						'template'=>'mainFormCards',
						"action"=>"sGETinit_section_options_bg"
					),	
				),
				
				/* backend data (dynamic) */
				'data' => array(
					'backgrounds' => $this-> listBackgroundsCells(),
					'listbackgrounds' => $this-> listBackgrouns(),
				),		
				
				/* schema data (static) */	
				'viewdata' => array(
					'backgrounds' => 'sectionOptions/backgrounds',
					'listbackgrounds' => 'sectionOptions/list-backgrounds',
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

	function listBackgroundsCells(){

		$my_backgrounds_schema = array(
			'background1' => array(				
				'post_name' => 'BG name is not defined',
				'cellID' => 'background1'

				),
			'background2' => array(				
				'post_name' => 'BG name is not defined',
				'cellID' => 'background2'

				),
			'background3' => array(				
				'post_name' => 'BG name is not defined',
				'cellID' => 'background3'
				)
		);		
		$my_backgrounds = get_option( 'section_backgrounds' );
		if(!$my_backgrounds){
			add_option( 'section_backgrounds', $my_backgrounds_schema, '', 'no' );
			$my_backgrounds = $my_backgrounds_schema;
		}
		return $my_backgrounds;
		
	}

	function listBackgrouns(){

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
		
		return (array)$mediaList;

	}

	function addBackgroundToCell(){

		$this->getURLData();
		$data = (array) $this->postData;

		$cell_id = $this->urlData->cell_id;
		$media_id = $this->urlData->post_id;

		$my_backgrounds = get_option( 'section_backgrounds' );

		foreach ($my_backgrounds as $key => $value) {
			
			if($key == $cell_id){
				
				$attachment = get_post($media_id);
				
				$my_backgrounds[$key] =	array(				
					'post_name' => $attachment->post_title,
					'cellID' => $key,
					'thumbnail' => $attachment->guid
				);
			}

		}

		update_option( 'section_backgrounds', $my_backgrounds );

		$this->build();
	}

	function file_upload(){
		/*https://www.ibenic.com/wordpress-file-upload-with-ajax/*/
	}

}

$a = new GDSectionOptions();

/* routing */
/* #showmodal/a=sGETinit_section_options_bg/active=backgrounds/tpl=mainFormCards */
/* #showmodal/a=sGETinit_section_options_bg/active=listbackgrounds/tpl=mainFormCards */

