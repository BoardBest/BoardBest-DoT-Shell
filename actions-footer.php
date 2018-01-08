<?php

class GDFooter {
	
	 private $debug = '';
	 private $db;
	 private $urlData;
	 private $postData;
	 private $initData;
    
    function __construct() {
    	 global $wpdb;
       $this->db = $wpdb;
       
    	 //init
       add_action( 'wp_ajax_sGETinit_footer', array($this, 'build') );
       //actions
       add_action( 'wp_ajax_sGET_footer_addMessage', array($this, 'addMessage') );
       add_action( 'wp_ajax_sGET_footer_updateMessage', array($this, 'updateMessage') );
       add_action( 'wp_ajax_sGET_footer_removeMessage', array($this, 'removeMessage') );
       add_action( 'wp_ajax_sGET_footer_removeMailingUser', array($this, 'removeMailingUser') );
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
						'name'=>'Posts List',
						'viewDataTarget'=>'messagesList',
						'template'=>'mainFormTemplate',
						"action"=>"sGETinit_footer"
					),
					/*
					array(
						'name'=>'Mailling List',
						'viewDataTarget'=>'mailingList',
						'template'=>'mainFormTemplate',
						"action"=>"sGETinit_footer"
					),
					*/		
				),
				
				/* backend data (dynamic) */
				'data' => array(
					//'addMessage' => array(),
					//'editMessage' => $this->getMessage(),
					'messagesList' => $this->getMessagesList(),
				),		
				
				/* schema data (static) */	
				'viewdata' => array(
				   //'addMessage' => 'footer/addMessage',
					//'editMessage' => 'footer/editMessage',
					'messagesList' => 'footer/messagesList',
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
					'editor'
				),
				'initdata' => $this->initData,
				'debug' => $debug,
				'debugGlobal' => $this->debug,
			));
	
		wp_send_json( $out );
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
		
		$args = array(
			'posts_per_page' => -1,
			'post_status' => 'publish',
		);
		$results = get_posts($args);
		
		$this->debug = $results;
		
		foreach ( $results as $message ) {
		
		   array_push($postList, array(
					'id' => $message->ID,
					'post_title' => $message->post_title,
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
		
		$message = array(
			'subject' => $data->subject,
	      'message' => $data->message,
	      'is_scheduled' => $data->is_scheduled,
			'list_id' => 2,
	      'status' => 'not_sent',
	      'mailtype' => 'text/html',
	      'is_global' => 0,
	      'is_scheduled' => 0,
      );
      
      $result = $this->db->insert(BFTPRO_NEWSLETTERS, $message);
      
		
		$this->build();
	}
	
	function updateMessage(){
		
		$this->getURLData();
		
		$data = (object) $this->postData->editmessage;
		
		$message = array(
			'subject' => $data->subject,
	      'message' => $data->message,
	      'is_scheduled' => $data->is_scheduled,
			'list_id' => 2,
	      'status' => 'not_sent',
	      'mailtype' => 'text/html',
	      'is_global' => 0,
	      'is_scheduled' => 0,
      );
		
		$result = $this->db->update(BFTPRO_NEWSLETTERS, $message, array('id' => $data->id));
		
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

$a = new GDFooter();


