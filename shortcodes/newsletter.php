<?php

add_shortcode('newsletter', 'vue_component_newsletter_shortcode');
function vue_component_newsletter_shortcode($atts, $content, $shortcode) {
	global $wpdb;
	$listId = 2;

	$query = 'SELECT * FROM '.BFTPRO_FIELDS.' WHERE `list_id` = '.$listId;
	$dbIDs = $wpdb->get_results($query);
	$parsed = array();
	foreach ($dbIDs as $row) {
		$parsed[$row->name] = array(
			'id' => $row->id,
			'label' => $row->label
		);
	}
	$dbIDs = $parsed;
	unset($parsed);
	$update = 'UPDATE '.BFTPRO_FIELDS.' SET `is_required` = %d WHERE `list_id` = '.$listId.' AND `name` IN (%s);';

	$disable = sprintf($update, 0, _parseIn(array_keys($dbIDs)));
	$wpdb->query($disable);

	$content = do_shortcode("[bftpro-form-start {$listId}]");
	$parsed = array();
	
	foreach ($atts as $field => $value) {
		
		$required = true;
		
		if (!isset($atts[$field.'_active'])) continue;
		$format = '<p><strong>%s:</strong> <br>%s</p>';
		$parsed[] = $field;
		switch ($field) {
			case 'useremail':
			case 'contactemail':
				$fieldId = '-static email';
				$label = $value;
				break;
			case 'userfirstname':
			   $fieldId = '-static name';
				$label = $value;
				$required = false;
				break;
			case 'contactfirstname':
				$fieldId = '-static name';
				$label = $value;
				break;
			case 'userlastname':
			case 'userphone':
			   $fieldId = ' '.$dbIDs[$field]['id'];
				$label = $value;
				break;
			case 'userextra1':
			case 'userextra2':
			case 'userextra3':
			case 'contactlastname':
			case 'contactphone':
			case 'contactextra1':
			case 'contactextra2':
			case 'contactextra3':
			case 'contactmessage':
				$fieldId = ' '.$dbIDs[$field]['id'];
				$label = $value;
				$required = false;
				break;
			case 'useragreement':
			case 'contactagreement':
				$format = '<p><label for="agreement">%2$s %1$s</label></p>';
				$fieldId = ' '.$dbIDs[$field]['id'];
				$label = $value;
				break;
			default:
				continue;
		}
		// $content .= '<pre>'.$field."\n".$fieldId.'</pre>';
		foreach ($atts as $field2 => $value2) {
			if($field.'_active' == $field2 && $value2 == 'on') {
				$contentTmp = sprintf(
					$format,
					$label,
					do_shortcode('[bftpro-field'.$fieldId.']')
				);
				if($required) {
					$content .= $newphrase = str_replace(array('<input'), array('<input required=required'), $contentTmp);
				} else {
					$content .= $contentTmp;
				}
			}
		}
	}
	
	$enable = sprintf($update, 1, _parseIn($parsed));
	$wpdb->query($enable);

	$instance = '';
	$submit = (($instance)? $instance['submit'] : __('Subscribe'));
	$content .= do_shortcode("[bftpro-submit-button {$listId} \"{$submit}\"]");
	$content .= do_shortcode("[bftpro-form-end {$listId}]");


	return $content;
}