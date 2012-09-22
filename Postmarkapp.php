<?php
/**
 *  Postmarkapp is an application designed to work with http://www.postmarkapp.com for sending out emails to clients. The application can be used
 * as a standalone and also comes with an adapter.
 * 
 */
class Postmarkapp extends PVStaticInstance {
	
	protected $_mail;
	
	protected $_options;
	
	protected $_configuration;
	
	/**
	 * The constructor for the Postmarkapp configureds how the postmarkapp will operate
	 * 
	 * @param array $data The initial data that can be used to set the values without using  a mutator method. The values can be as values:
	 * 			-'HtmlBody' _string_: The html body of the message
	 * 			-'TextBody' T_string_ The text body of the message
	 * 			-'To' _string_: The person that is receiving the message
	 * 			-'Cc ' _string_: Who the carbon copy will be sent too.
	 * 			-'Bcc' _string_: Who will receive a blind copy
	 * 			-'Subject' _string_ The subject of the email going out
	 * @param array $configuration Configuration options that can be used to determ how postmark will execute
	 * 			-'host' _string_: Determines to use regular connection or an ssl connection. Default is normal, change to ssl for ssl connections
	 * 			-'protocol' _string_: The protocol used to send the mail. Default is curl for sending through curl. Other is SMTP but that is not recommended
	 * @param array $options Options that are used to find the sending
	 * 			-'host' _string_: The host that the message will be sent too
	 * 			-'token' _string: The security token provided by postmarkapp. Set as a define before application is called.
	 * 			-'auth_header' The authorization header sent too postmark. Default is X-Postmark-Server-Token: 
	 * 			-'application_type' : The application type used to set the format. Default is 'application/json'
	 * 
	 * @return void
	 * @access public
	 */
	public function __construct(array $data = array(), array $configuration = array(), array $options = array()) {
		
		$this -> _mail = $data;
		
		$configuration_defaults = array(
			'host' => 'normal',
			'protocol' => 'curl',
		);
		
		$this -> _configuration = $configuration += $configuration_defaults;
		
		$option_defaults = array(
			'host' => array('normal' => 'http://api.postmarkapp.com/email', 'ssl' => 'https://api.postmarkapp.com/email'),
			'token' => POSTMARK_API_KEY,
			'auth_header' => 'X-Postmark-Server-Token: ',
			'application_type' => 'application/json'
		);
		
		$this -> _options = $options += $option_defaults;
	}
	
	/**
	 * Sets the HTML body of the email to be sent for clients that are able to receieve html emails
	 * 
	 * @param string $message
	 * 
	 * @return void
	 * @access public
	 */
	public function setHtmlMessage($message) {
		$this -> _mail['HtmlBody'] = $message;
	}
	
	/**
	 * Sets the text message for clients that are not able to receieve html emails.
	 * 
	 * @param string $message
	 * 
	 * @return void
	 * @access public
	 */
	public function setTextMessage($message) {
		$this -> _mail['TextBody'] = $message;
	}
	
	/**
	 * Set the email address that will be receive the email. In others words, who the email is going too.
	 * 
	 * @param string $email
	 * 
	 * @return void
	 * @access public
	 */
	public function setReceiver($message) {
		$this -> _mail['To'] = $message;
	}
	
	/**
	 * Set email address that will receive carbon copy of the emails.
	 * 
	 * @param string $email
	 * 
	 * @return void
	 * @access public
	 */
	public function setCarbonCopy($message) {
		$this -> _mail['Cc'] = $message;
	}
	
	/**
	 * Set email address that will receive blind copies of the email.
	 * 
	 * @param string $email
	 * 
	 * @return void
	 * @access public
	 */
	public function setBlindCopy($message) {
		$this -> _mail['Bcc'] = $message;
	}
	
	/**
	 * Set the sender email who the email will be coming from.
	 * 
	 * @param string $email
	 * 
	 * @return void
	 * @access public
	 */
	public function setSender($message) {
		$this -> _mail['From'] = $message;
	}
	
	/**
	 * Set the email address to go in the ReplyTo option.
	 * 
	 * @param string $email
	 * 
	 * @access public
	 * @return void
	 */
	public function setReplyTo($message) {
		$this -> _mail['ReplyTo'] = $message;
	}
	
	/**
	 * Sets the subject line in the email
	 * 
	 * @param string $subject
	 * 
	 * @return void
	 * @access public
	 */
	public function setSubject($message) {
		$this -> _mail['Subject'] = $message;
	}
	
	/**
	 * Sets a tag that is used by Postmarkapp, maybe for something else
	 * 
	 * @param string $tag
	 * 
	 * @return void
	 * @access public
	 * @todo find out more about tags
	 */
	public function setTag($message) {
		$this -> _mail['Tag'] = $message;
	}
	
	/**
	 * Adds an attachment to the email to be sent
	 * 
	 * @param string $name The name of the attachement
	 * @param string $file The location of the file to be sent. Make sure file is accessible
	 * @param string $content_type The content type of the file
	 * 
	 * @return void
	 * @access public
	 */
	public function addAttachment($name, $file, $content_type) {
		if(!isset($this -> _mail['Attachments'])  && !is_array($this -> _mail['Attachments']))
			$this -> _mail['Attachments'] = array();
		
		$this -> _mail['Attachments'][]= array(
			'Name' => $name,
			'Content' => $file,
			'ContentType' => $content_type
		);
	}
	
	/**
	 * Adds a custom header to the email that will be sent.
	 * 
	 * @param string $name The name of header (ex: 'Content-Type' )
	 * @param string $value The value associated with the header (ex: 'text/html')
	 * 
	 * @return void
	 * @access public
	 */
	public function addHeader($name, $value) {
		if(!isset($this -> _mail['Headers'])  && !is_array($this -> _mail['Headers']))
			$this -> _mail['Headers'] = array();
		
		$this -> _mail['Headers'][]= array(
			'Name' => $name,
			'Value' => $value,
		);
	}
	
	/**
	 * Only use send after the required parameters have been set. The sender, receiver, html message and text message should all be sent. Uses a notifer
	 * for finding out results or returns the result
	 * 
	 * @return void
	 * @access
	 */
	public function send() {
		$this -> _convertFormat();
		
		$data = $this -> _sendCurl();
		
		$this->_notify('Postmarkapp::send', $this -> _mail, $data);
		
		return $data;
	}
	
	/**
	 * Sends the email through using curl. Curl is the default option set in the protocol when the class is instantiated.
	 * 
	 * @return void
	 * @access public
	 */
	protected function _sendCurl() {
		
		$headers = array(
			'Accept: '.$this -> _options['application_type'],
			'Content-Type: '. $this -> _options['application_type'],
			 $this -> _options['auth_header'].' '.$this -> _options['token'],
		);
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $this -> _options['host'][$this-> _configuration['host']]); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this -> _mail));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
		
	}
	
	/**
	 * Ensures that all string data entered into Postmark is UTF-8 format. Postmarkapp only allows utf-8
	 * data to be sent.
	 * 
	 * @return void
	 * @access protected
	 */
	protected function _convertFormat($format = 'utf-8') {
		
		if(is_array($this -> _mail)){
			foreach($this -> _mail as $key => $value) {
				if(is_string($value)) {
					$this -> _mail[$key] = utf8_encode($value);
				}
			}//end foreach
		}
	}
	
	/**
	 * Sends the email using SMTP. According to the Postmarkapp documention, this method is not robust as curl.
	 * 
	 * @todo implement method
	 */
	protected function _sendSmtp() {
		
	}
}
