<?php

class GDAutoresponder {
	
	 private $debug = 'no data';
	 private $db;
	 private $urlData;
	 private $postData;
	 private $initData;
    
    function __construct() {
		global $wpdb;
		$this->db = $wpdb;
       
		//init
		add_action( 'wp_ajax_sGETinit_autoresponder', array($this, 'build') );
		//actions
		add_action( 'wp_ajax_sGET_autoresponder_addShortcode', array($this, 'addShortcode') );
		add_action( 'wp_ajax_sGET_autoresponder_addMessage', array($this, 'addMessage') );
		add_action( 'wp_ajax_sGET_autoresponder_updateMessage', array($this, 'updateMessage') );
		add_action( 'wp_ajax_sGET_autoresponder_removeMessage', array($this, 'removeMessage') );
		add_action( 'wp_ajax_sGET_autoresponder_updateOptions', array($this, 'updateOptions') );
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
						'name'=>'Autoresponder form',
						'viewDataTarget'=>'addShortcode',
						'template'=>'mainFormTemplate',
						"action"=>"sGETinit_autoresponder"
					),
					array(
						'name'=>'Autoresponders',
						'viewDataTarget'=>'messagesList',
						'template'=>'mainFormTemplate',
						"action"=>"sGETinit_autoresponder"
					),
					array(
						'name'=>'Options',
						'viewDataTarget'=>'options',
						'template'=>'mainFormTemplate',
						"action"=>"sGETinit_autoresponder"
					),
							
				),
				
				/* backend data (dynamic) */
				'data' => array(
					'messagesList' => $this->getMessagesList(),
					'options' => $this->getOptions(),
					'addMessage' => array(),
					'editMessage' => $this->getMessage(),
					'addShortcode' => $this->addShortcode()
				),		
				
				/* schema data (static) */	
				'viewdata' => array(
					'messagesList' => 'autoresponder/messagesList',
					'options' => 'autoresponder/options',
					'addMessage' => 'autoresponder/addMessage',
					'editMessage' => 'autoresponder/editMessage',
					'addShortcode' => 'autoresponder/addShortcode',
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
			'useremail' => array(
				'useremail_active' => 'on',
				'useremail' => 'Insert email: example@email.com' 
				),
			'userfirstname' => array(
				'userfirstname_active' => 'on',
				'userfirstname' => 'Insert first name' 
				),
			'userlastname' => array(
				'userlastname_active' => 'on',
				'userlastname' => 'Insert last name' 
				),
			'userphone' => array(
				'userphone_active' => 'on',
				'userphone' => 'Insert phone number' 
				),
			'userextra1' => array(
				'userextra1_active' => 'on',
				'userextra1' => 'Insert more specyfic information' 
				),
			'userextra2' => array(
				'userextra2_active' => 'on',
				'userextra2' => 'Insert more specyfic information' 
				),
			'userextra3' => array(
				'userextra3_active' => 'on',
				'userextra3' => 'Insert more specyfic information' 
				),
			'useragreement' => array(
				'useragreement_active' => 'on',
				'useragreement' => 'I agree for procesing my data' 
				)

			);

		$my_shortcode = array_merge ( $my_shortcode, $data);
		return $my_shortcode;

	}
	
	function getMessage(){
		
		$my_post = array();
		
		if($this->urlData->id) {
			$results = $this->db->get_results("SELECT * FROM ".BFTPRO_MAILS." WHERE id = ".$this->urlData->id);
			$my_post = array('editmessage' => $results[0] );
	   }
	   
		return $my_post;
	}
	
	function getMessagesList(){
		
		$messagesList = array();
		
		$listId = 2;
		$arId = $this->db->get_var("SELECT `id` FROM ".BFTPRO_ARS." WHERE `list_ids` LIKE '%|{$listId}|%';");
		$results = $this->db->get_results("SELECT * FROM ".BFTPRO_MAILS." WHERE `ar_id` = {$arId}");
		
		foreach ( $results as $message ) {
		
		   array_push($messagesList, array(
					'id' => $message->id,
					'subject' => $message->subject,
			));
				
		}
		
		return $messagesList;
	
	}
	
	function getOptions(){
	   
	   $listId = 2;
		$options = $this->db->get_row("SELECT * FROM ".BFTPRO_LISTS." WHERE `id` = ".$listId);
		
		$response = array(
			'options' => $options,
		);

		return $response;
	
   }
	
	function addMessage(){
		
		$this->getURLData();
		
		$data = (object) $this->postData->addmessage;
		
		$listId = 2;
		$arId = $this->db->get_var("SELECT `id` FROM ".BFTPRO_ARS." WHERE `list_ids` LIKE '%|{$listId}|%';");
		
		if($data->days != '') $artype = 'days';
		else if($data->daytime != '') $artype = 'date';
		
		$message = array(
			'subject' => $data->subject,
			'message' => $data->message,
			'artype' => $artype,
			'days' => $data->days,
			'daytime' => $data->daytime,
			'ar_id' => $arId,
			'mailtype' => 'text/html',
		);
      
		$result = $this->db->insert(BFTPRO_MAILS, $message);
      
		
		$this->build();
	}
	
	function updateMessage(){
		
		$this->getURLData();
		
		$data = (object) $this->postData->editmessage;
		
		$listId = 2;
		$arId = $this->db->get_var("SELECT `id` FROM ".BFTPRO_ARS." WHERE `list_ids` LIKE '%|{$listId}|%';");
		
		if($data->days != '') $artype = 'days';
		else if($data->daytime != '') $artype = 'date';
		
		$message = array(
			'subject' => $data->subject,
			'message' => $data->message,
			'artype' => $artype,
			'days' => $data->days,
			'daytime' => $data->daytime,
			'ar_id' => $arId,
			'mailtype' => 'text/html',
		);
		
		$result = $this->db->update(BFTPRO_MAILS, $message, array('id' => $data->id));
		
		
		$this->build();
	}
	
	function removeMessage(){
		
		$this->getURLData();
		
		$data = (object) $this->postData->editmessage;
		
		$this->db->delete(BFTPRO_MAILS, array('id' => $data->id));
		
		$this->build();
	}
	
	function updateOptions() {
		
		$this->getURLData();
		
		$data = (object) $this->postData->options;
		
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
		
		$this->build();
	}

}

$a = new GDAutoresponder();


