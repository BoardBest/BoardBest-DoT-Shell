<?php

class GDAddShortcode {
	
	private $debug = '';
	private $urlData;
	private $postData;
	private $initData;
    
    function __construct() {
		
    	// init
		add_action( 'wp_ajax_sGETinit_addshortcode', array($this, 'build') );
		// actions
		// no actions
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
				'tabs' => array(),
				
				/* backend data (dynamic) */
				'data' => array(
					'addShortcode' => array()
					),		
				
				/* schema data (static) */	
				'viewdata' => array(
				   'addShortcode' => 'shortcodes/add-shortcode',
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
					'ico_material',
					'layoutcard'
				),

				'initdata' => $this->initData,
				'debug' => $debug,
				'debugGlobal' => $this->debug,
			));
	
		wp_send_json( $out );
	}

}

$a = new GDAddShortcode();


