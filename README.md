PV Postmarkapp
==============

PV Postmarkapp is an application than intergrates Postmarkapp with ProdigyView. If you are unfamiliar with Postmarkapp, http://postmarkapp.com/, it is a service for delivering emails.

##What is ProdigyView
ProdigyView is an open-source framework for building applications. Unlike many frameworks, it is formless and was not built around the MVC design pattern but utilizes adapters, filters and obversers to take an aspect oriented approach to programming.

##Installation
1. Copy or clone the files in pv_postmarkapp into your designated librares folder.
2. At the beginning of execution of your script set the api key obtained from postmark in a define ex: define('POSTMARK_API_KEY', 'xxx-xxx-xx-xxxx')
3. At the beginning of execution, add the library to ProdigyView execution(ex: PVLibraries::addLibrary('pv_postmarkapp');

###Example
<pre><code>
define('POSTMARK_API_KEY', 'xxx-xxx-xx-xxxx');
PVLibraries::addLibrary('pv_postmarkapp');

/**
Use explicit loading to ensure that the design patterns are included
PVLibraries::addLibrary('pv_postmarkapp', array('explicit_load' => true));
*/

$postmark = new Postmarkapp();
</code></pre>

##Overriding PVMail::sendMail
Postmarkapp for ProdigyView comes with an adapter that allows overriding of the regular mail function in ProdigyView. This mean you will never have to instantiate an Postmarkapp object, but just you the regular PVMail::sendEmail(). The adapter will always use Postmarkapp for sending an email. This is designed to be loosely coupled.

#Observer
The method 'send' has a observer that will be used to attach functionality after email has attempted to be sent. Using the obsever, you will get a dump of the data that attempted to be sent and the response data return from curl.

###Example

<pre><code>
Postmarkapp::addObserver('Postmarkapp::send', 'read_closure', function($args, $feedback) {
//Execution of code here
}, array('type' => 'closure'));