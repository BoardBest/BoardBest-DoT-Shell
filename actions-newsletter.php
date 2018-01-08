<?php

class GDNewsletter {
	
	 private $debug = '';
	 private $db;
	 private $urlData;
	 private $postData;
	 private $initData;
    
    function __construct() {
    	 global $wpdb;
       $this->db = $wpdb;
       
    	 //init
       add_action( 'wp_ajax_sGETinit_newsletter', array($this, 'build') );
       //actions
       add_action( 'wp_ajax_sGET_newsletter_addShortcode', array($this, 'addShortcode') );
       add_action( 'wp_ajax_sGET_newsletter_addMessage', array($this, 'addMessage') );
       add_action( 'wp_ajax_sGET_newsletter_updateMessage', array($this, 'updateMessage') );
       add_action( 'wp_ajax_sGET_newsletter_removeMessage', array($this, 'removeMessage') );
       add_action( 'wp_ajax_sGET_newsletter_removeMailingUser', array($this, 'removeMailingUser') );
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
						'name'=>'Newsletter form',
						'viewDataTarget'=>'addShortcode',
						'template'=>'mainFormTemplate',
						"action"=>"sGETinit_newsletter"
					),
					array(
						'name'=>'Newsletters List',
						'viewDataTarget'=>'messagesList',
						'template'=>'mainFormTemplate',
						"action"=>"sGETinit_newsletter"
					),
					array(
						'name'=>'Mailling List',
						'viewDataTarget'=>'mailingList',
						'template'=>'mainFormTemplate',
						"action"=>"sGETinit_newsletter"
					),			
				),
				
				/* backend data (dynamic) */
				'data' => array(
					'addMessage' => array(),
					'editMessage' => $this->getMessage(),
					'messagesList' => $this->getMessagesList(),
					'mailingList' => $this->getMailingList(),
					'addShortcode' => $this->addShortcode()
				),		
				
				/* schema data (static) */	
				'viewdata' => array(
				   'addMessage' => 'newsletter/addMessage',
					'editMessage' => 'newsletter/editMessage',
					'messagesList' => 'newsletter/messagesList',
					'mailingList' => 'newsletter/mailingList',
					'addShortcode' => 'newsletter/addShortcode',
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
			$results = $this->db->get_results("SELECT * FROM ".BFTPRO_NEWSLETTERS." WHERE id = ".$this->urlData->id);
			$my_post = array('editmessage' => $results[0] );
	   }
	   
		return $my_post;
	}
	
	function getMessagesList(){
		
		$postList = array();
		
		$results = $this->db->get_results("SELECT * FROM ".BFTPRO_NEWSLETTERS);
		
		foreach ( $results as $message ) {
		
		   array_push($postList, array(
					'id' => $message->id,
					'datelastsent' => $message->date_last_sent,
					'status' => $message->status,
					'subject' => $message->subject,
			));
				
		}
		
		return $postList;
	
	}
	
	function getMailingList(){
		
		$mailingList = array();
	
		$results = $this->db->get_results("SELECT * FROM ".BFTPRO_USERS." WHERE `list_id` = 2");
		
		foreach ( $results as $message ) {
		
		   array_push($mailingList, array(
					'id' => $message->id,
					'email' => $message->email,
			));
				
		}
	
		return $mailingList;
	
   }
	
	function addMessage(){
		
		$this->getURLData();
		
		$data = (object) $this->postData->addmessage;
		
		if($data->scheduled_for != '') $is_scheduled = 1;
		else $is_scheduled = 0;
		
		$message = array(
			'subject' => $data->subject,
	      'message' => $data->message,
	      'is_scheduled' => $data->scheduled_for,
			'list_id' => 2,
	      'status' => 'not_sent',
	      'mailtype' => 'text/html',
	      'is_global' => 0,
	      'is_scheduled' => $is_scheduled,
      );
      
      $result = $this->db->insert(BFTPRO_NEWSLETTERS, $message);
      $lastid = $wpdb->insert_id;
      
      require_once(BFTPRO_PATH."/models/newsletter.php");
      $_nl = new BFTProNLModel();
      $_nl->select($lastid);
      $_nl->send($lastid);
		
		$this->build();
	}
	
	function updateMessage(){
		
		$this->getURLData();
		
		$data = (object) $this->postData->editmessage;
		
		if($data->scheduled_for != '') $is_scheduled = 1;
		else $is_scheduled = 0;
		
		$message = array(
			'subject' => $data->subject,
	      'message' => $data->message,
	      'is_scheduled' => $data->scheduled_for,
			'list_id' => 2,
	      'status' => 'not_sent',
	      'mailtype' => 'text/html',
	      'is_global' => 0,
	      'is_scheduled' => $is_scheduled,
      );
		
		$result = $this->db->update(BFTPRO_NEWSLETTERS, $message, array('id' => $data->id));
		
		require_once(BFTPRO_PATH."/models/newsletter.php");
      $_nl = new BFTProNLModel();
      $_nl->select($data->id);
      $_nl->send($data->id);
		
		$this->build();
	}
	
	function removeMessage(){
		
		$this->getURLData();
		
		$data = (object) $this->postData->editmessage;
		
		$this->db->delete(BFTPRO_NEWSLETTERS, array('id' => $data->id));
		
		$this->build();
	}
	
	function removeMailingUser() {
		
		$this->getURLData();
		
		$this->db->delete(BFTPRO_USERS, array('id' => $this->urlData->id));
		
		$this->build();
	}

}

$a = new GDNewsletter();


