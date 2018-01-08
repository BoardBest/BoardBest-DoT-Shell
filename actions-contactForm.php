<?php

class GDContactForm {
	
	 private $debug = '';
	 private $db;
	 private $urlData;
	 private $postData;
	 private $initData;
    
    function __construct() {
    	 global $wpdb;
       $this->db = $wpdb;
       
    	 //init
       add_action( 'wp_ajax_sGETinit_contactform', array($this, 'build') );
       //actions
        add_action( 'wp_ajax_sGET_contactform_addShortcode', array($this, 'addShortcode') );
       add_action( 'wp_ajax_sGET_contactform_updateOptions', array($this, 'updateOptions') );
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
						'name'=>'Component form',
						'viewDataTarget'=>'addShortcode',
						'template'=>'mainFormTemplate',
						"action"=>"sGETinit_contactform"
					),
					array(
						'name'=>'Options',
						'viewDataTarget'=>'options',
						'template'=>'mainFormTemplate',
						"action"=>"sGETinit_contactform"
					),
				),
				
				/* backend data (dynamic) */
				'data' => array(
				   'options' => $this->getOptions(),
				   'addShortcode' => $this->addShortcode()
				),		
				
				/* schema data (static) */	
				'viewdata' => array(
					'options' => 'contactForm/options',
					'addShortcode' => 'contactForm/addShortcode',
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
			'contactemail' => array(
				'contactemail_active' => 'on',
				'contactemail' => 'Insert email: example@email.com' 
				),
			'contactlastname' => array(
				'contactlastname_active' => 'on',
				'contactlastname' => 'Insert first name' 
				),
			
			'contactphone' => array(
				'contactphone_active' => 'on',
				'contactphone' => 'Insert phone number' 
				),
			'contactextra1' => array(
				'contactextra1_active' => 'on',
				'contactextra1' => 'Insert more specyfic information' 
				),
			'contactextra2' => array(
				'contactextra2_active' => 'on',
				'contactextra2' => 'Insert more specyfic information' 
				),
			'contactextra3' => array(
				'contactextra3_active' => 'on',
				'contactextra3' => 'Insert more specyfic information' 
				),
			'contactmessage' => array(
				'contactmessage_active' => 'on',
				'contactmessage' => 'Insert message' 
				),

			'contactagreement' => array(
				'contactagreement_active' => 'on',
				'contactagreement' => 'I agree for procesing my data' 
				)

			);

			$my_shortcode = array_merge ( $my_shortcode, $data);
			return $my_shortcode;

		}
	
	function getOptions(){
		
		$response = array();
	   
	   $listId = 1;
		$options = $this->db->get_row("SELECT * FROM ".BFTPRO_LISTS." WHERE `id` = ".$listId);
		
		$response = array(
			'options' => $options,
		);

		return $response;
	
   }
	
	function updateOptions() {
		
		$this->getURLData();
		
      $data = $this->postData->options;
		
		$listId = 1;
		
		$this->db->update(BFTPRO_LISTS, $data, array('id' => $listId));
		
		/*
		$query = "SELECT * FROM ".BFTPRO_LISTS;
		if (isset($listId)) {
			$query .= " WHERE `id` = {$listId};";
		}
		$res = $wpdb->get_results($query);
		if (count($res) < 2) {
			$res = $res[0];
		}
		if (isset($listId) && $listId == 1) {
			$res->notify_email = (empty($res->notify_email))? get_option('admin_email', $res->notify_email) : $res->notify_email;
		}
		
		
		$listId = 2;
		
		$update = array(
			'do_notify' => $data->do_notify,
	      'optin' => $data->optin,
	      'confirm_email_subject' => $data->confirm_email_subject,
	      'confirm_email_content' => $data->confirm_email_content,
			'redirect_to' => $data->redirect_to,
			'redirect_confirm' => $data->redirect_confirm,
			'unsubscribe_text' => $data->unsubscribe_text,
			'unsubscribe_redirect' => $data->unsubscribe_redirect,
      );
		
		$this->db->update(BFTPRO_LISTS, $update, array('id' => $listId));
		*/
		$this->build();
	}

}

$a = new GDContactForm();


