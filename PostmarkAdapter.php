<?php

/**
 * This is a PostmarkAdapter that overrides the normal method of sending mail and routes ALL email through
 * Postmarkapp. It is loosely coupled through an adapter and can be uncoupled by removing this plugin or commenting
 * out the code below.
 */
PVMail::addAdapter('PVMail','sendEmail', function($args){
	
	$postmark = new Postmarkapp();
	
	if(isset($args['receiver'])) {
		$postmark -> setReceiver($args['receiver']);
	}
	
	if(isset($args['subject'])) {
		$postmark -> setSubject($args['subject']);
	}
	
	if(isset($args['sender'])) {
		$postmark -> setSender($args['sender']);
	}
	
	if(isset($args['reply_to'])) {
		$postmark -> setReplyTo($args['reply_to']);
	}
	
	if(isset($args['carboncopy'])) {
		$postmark -> setCarbonCopy($args['carboncopy']);
	}
		
	if(isset($args['blindcopy'])) {
		$postmark -> setBlindCopy($args['blindcopy']);
	}
	
	if(isset($args['html_message'])) {
		$postmark -> setHtmlMessage($args['html_message']);
	}
	
	if(isset($args['text_message'])) {
		$postmark -> setTextMessage($args['text_message']);
	}
	
	if(isset($args['headers']) && is_array($args['headers'])) {
		foreach($args['headers'] as $key => $value) {
			$postmark -> addHeader($key, $value);
		}
	}
	
	$postmark -> send();
},array('type' => 'closure'));