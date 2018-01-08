<?php

class GDAddIcons {
	
	 private $debug = 'no data';
	 private $db;
	 private $urlData;
	 private $postData;
	 private $initData;
    
    function __construct() {
		global $wpdb;
		$this->db = $wpdb;
       
		//init
		add_action( 'wp_ajax_sGETinit_ico_material', array($this, 'build') );
		//actions
		add_action( 'wp_ajax_sGET_addicons_addShortcode', array($this, 'addShortcode') );
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
					'addShortcode' => $this->addShortcode()
				),		
				
				/* schema data (static) */	
				'viewdata' => array(
					'addShortcode' => 'addicons/addShortcode',
				),
				
				/* templates (static) */
				'templates' => array(
					'mainFormTemplate' => 'main-form-template',
					'mainTabsTemplate' => 'main-tabs-template'
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
					'action_insert_shortcode',
				),
				'initdata' => $this->initData,
				'debug' => $debug,
				'debugGlobal' => $this->debug,
			));
	
		wp_send_json( $out );
	}

	function addShortcode(){

		$this->getURLData();
		$data = (array) $this->postData;

		$my_shortcode = array(
			'addicon' => array(
				'addicon_name' => 'option2',
				'addicon_active' => false,
				'addicon_label' => 'My avesome icon' 
				));
		$my_shortcode = array_merge ( $my_shortcode, $data);
		return $my_shortcode;

	}
}

$a = new GDAddIcons();


