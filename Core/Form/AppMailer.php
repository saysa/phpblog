<?php
namespace Core\Form;
use Core\Config\AppConfig;

// Composer autoloader
if (!class_exists('Composer\\Autoload\\ClassLoader'))
{
	require_once __DIR__ . '/../../Libs/vendor/autoload.php';
}

// Import PHPMailer component
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Class to use mailers in form
 */
class AppMailer
{
	/**
	 * @var object: mailer to use
	 */
	private $mailer;
	/**
	 * @var string: parameter to select a method
	 */
	private $sendingMethod;
	/**
	 * @var string: parameter to select a method
	 */
	private $use;
	/**
	 * @var object: config to use
	 */
	private $config;
	/**
	 * Constructor
	 * @param object $mailer: an instance of one type of mailer object 
	 * @return void
	 */
	
	public function __construct($mailer, $sendingMethod, $use)
    {
        $this->mailer = $mailer;
        $this->sendingMethod = $sendingMethod;
        $this->use = $use;
        $this->config = AppConfig::getInstance();
    }

    /**
     * Call the right method and depends on called object
     * @param array $arguments: an array of $arguments to feed a method
     * @return mixed: return depends on called method
     */
    public function call($arguments)
    {
    	switch((new \ReflectionClass($this->mailer))->getShortName()) {
    		case 'PHPMailer':
    			if($this->sendingMethod == 'smtp' && $this->use == 'contactForm') {
    				return call_user_func_array([$this, 'sendContactFormMessageWithSMTP'], $arguments);
    			}
    			// Other types: do stuff here
    		break;
    		// Other types: do stuff here
    	}
    }

    /**
     * Description
     * @param array $datas: contact form datas to send 
     * @param string $insertionInfos: a string to call which represents a notice message
     * to know if Contact entity is saved or not in database
     * @param string $sendingInfos: an empty string property to feed in controller
     * @return boolean: sending mail state
     */
    public function sendContactFormMessageWithSMTP($datas, $insertionInfos, $sendingInfos)
    {
    	$this->mailer->isSMTP(); // use SMTP
		$this->mailer->SMTPDebug = $this->config::$_params['contactPHPMailer']['SMTPDebug']; // enable SMTP debugging or not
		$this->mailer->SMTPAuth  = $this->config::$_params['contactPHPMailer']['SMTPAuth'];
		$this->mailer->Username = $this->config::$_params['contactPHPMailer']['SMTPUserName']; // username to use for SMTP authentication
		$this->mailer->Password = $this->config::$_params['contactPHPMailer']['SMTPPwd']; // password to use for SMTP authentication
		$this->mailer->Port = $this->config::$_params['contactPHPMailer']['Port'];
		$this->mailer->Host = $this->config::$_params['contactPHPMailer']['Host']; // set the hostname of the mail server
		$this->mailer->SMTPSecure = $this->config::$_params['contactPHPMailer']['SMTPSecure']; //set the encryption system to use
		
		// Recipients
		$this->mailer->setFrom($this->config::$_params['contactForm']['contactEmail'], 'phpBlog - Contact form'); // sent from (for instance, if Google mail SMTP is used, a restriction exists: it is better to use receiver address and not sender address.)
		$this->mailer->addAddress($this->config::$_params['contactForm']['contactEmail'], 'phpBlog'); // sent to	
		$this->mailer->ClearReplyTos();
		$this->mailer->addReplyTo($datas['email'], 'Reply to ' . $datas['firstName'] . ' ' . $datas['familyName']); //set an alternative reply-to address

		// Content
	    $this->mailer->isHTML(true); // set email format to HTML
	    $this->mailer->Subject = 'phpBlog - Contact form: someone sent a message!'; // Email subject
	    $this->mailer->Body = '<p style="text-align:center;"><img src="' . $this->config::$_params['contactPHPMailer']['HostedImagesAbsoluteURL'] . 'dotprogs-logo-2016.png" alt="phpBlog contact form"></p>'; // Add custom header image
	    $this->mailer->Body .= '<p style="text-align:center;"><strong>phpBlog - Contact form: someone sent a message!</strong></p>'; // html format
	    $this->mailer->Body .= '<p style="width:50%;margin:auto;text-align:center;padding:10px;background-color:#bdbdbc;color:#ffffff;">' . $insertionInfos . '</p>'; // html format
	    $this->mailer->Body .= '<p style="width:50%;margin:auto;text-align:center;padding:10px;background-color:#7b7c7c;color:#ffffff;">From: ' . $datas['firstName'] . ' ' . $datas['familyName'] . ' | <a href="#" style="color:#ffffff; text-decoration:none"><font color="#ffffff">' . $datas['email'] . '</font></a><br>- Message -<br>' . nl2br($datas['message']) . '</p>'; // html format
	    $this->mailer->Body .= '<p style="width:50%;margin:auto;text-align:center;padding:10px;">&copy; ' . date('Y') . ' phpBlog</p>'; // html format
	    $this->mailer->AltBody = $insertionInfos . "\n\r"; // text format
	    $this->mailer->AltBody .= 'From:' . $datas['firstName'] . ' ' . $datas['familyName'] . ' | ' . $datas['email'] . "\n\r" . '- Message -' . "\n\r" . $datas['message']. "\n\r"; // text format
	    $this->mailer->AltBody .= '&copy; ' . date('Y') . ' phpBlog'; // text format

		try {
		    if(!$this->mailer->send()) {
		    	$sendingInfos = $this->mailer->ErrorInfo;
		    	return false;
		    }
		    else {
		    	return true;
		    }
		} catch (Exception $e) {
			$sendingInfos = $e->errorMessage();
			return false;
		}
    }

}