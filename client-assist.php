<?php
ini_set('display_errors', 0); 
error_reporting(~E_ALL);

define('ZP_NO_REDIRECT', true);

//bootstrap wordpress
$bootstrapSearchDir = dirname($_SERVER["SCRIPT_FILENAME"]);
$docRoot = dirname(isset($_SERVER["APPL_PHYSICAL_PATH"]) ? $_SERVER["APPL_PHYSICAL_PATH"] : $_SERVER["DOCUMENT_ROOT"]);

while (!file_exists($bootstrapSearchDir . "/wp-load.php")) {
	$bootstrapSearchDir = dirname($bootstrapSearchDir);
	if (strpos($bootstrapSearchDir, $docRoot) === false){
		$bootstrapSearchDir = "../../.."; // critical failure in our directory finding, so fall back to relative
		break;
	}
}
require_once($bootstrapSearchDir . "/wp-load.php");

if(defined('ZPRESS_API') && ZPRESS_API != '') {
	
	require_once(WPMU_PLUGIN_DIR . '/akismet/loadAkismet.php');

}

class i9_ClientAssist {
	
    static public function call($method) 
    { 
    	if(method_exists('i9_ClientAssist', $method)) { 
			call_user_func(array('i9_ClientAssist', $method));
        }else{ 
        //	die();
        } 
    } 
	
	static function AutoComplete() {
		
		$apiParams = array();
		
		$apiParams['query.partialLocationTerm'] = $_GET['term'];
		
		$apiHttpResponse = i9_ApiRequest::FetchData('Autocomplete', $apiParams, false, 0);
		
		header('Content-Type: application/json');
		echo $apiHttpResponse['body'];
		die();
	}
	
	static function Register() {
		
		$apiParams = array();
		
		$apiParams['query.register'] = $_REQUEST['action'];
		$apiParams['query.account'] = $_REQUEST['account'];
		$apiParams['query.fname'] = $_REQUEST['fname'];
		$apiParams['query.lname'] = $_REQUEST['lname'];
		$apiParams['query.emailid'] = $_REQUEST['emailid'];
		$apiParams['query.mno'] = $_REQUEST['mno'];
		$apiParams['query.gender'] = $_REQUEST['gender'];
		
		$apiHttpResponse = i9_ApiRequest::FetchData('Register', $apiParams, false, 0);
		
		if($apiHttpResponse['body']>0){
			setcookie('i9idx-lead-id',$apiHttpResponse['body'] ,0 , '/');
		}

		echo json_encode($apiHttpResponse['body']);
		die();
	}
	
	static function Login() {
		
		$apiParams = array();
		
		$apiParams['query.login'] = $_REQUEST['action'];
		$apiParams['query.uname'] = $_REQUEST['uname'];
		$apiParams['query.pwd'] = $_REQUEST['pwd'];
		
		$apiHttpResponse = i9_ApiRequest::FetchData('Register', $apiParams, false, 0);
		
		if($apiHttpResponse['body']>0){
			setcookie('i9idx-lead-id',$apiHttpResponse['body'] ,0 , '/');
		}
		
		echo json_encode($apiHttpResponse['body']);
		die();
	}

	static function ForgotPwd() {
		
		$apiParams = array();
		
		$apiParams['query.forgot'] = $_REQUEST['action'];
		$apiParams['query.umail'] = $_REQUEST['umail'];
		$apiParams['query.uphone'] = $_REQUEST['uphone'];
		
		$apiHttpResponse = i9_ApiRequest::FetchData('ForgotPwd', $apiParams, false, 0);
		
		echo json_encode($apiHttpResponse['body']);
		die();
	}
	
	static function Logout() {
		
		$apiParams = array();
		
		if(isset($_COOKIE['i9idx-lead-id']))
		setcookie('i9idx-lead-id',"" ,time()-3600 , '/');
		
		echo json_encode(1);
		die();
	}
	
	static function Changefav() {
		//print_r($_REQUEST);die;
		$apiParams = array();
		
		$apiParams['query.favorite'] = $_REQUEST['action'];
		$apiParams['query.key'] = $_REQUEST['key'];
		
		$apiHttpResponse = i9_ApiRequest::FetchData('favorite', $apiParams, false, 0);
		//print_r($apiHttpResponse);die;
		echo json_encode($apiHttpResponse['body']);
		die();
	}
	
	static function Checkfav() {
		//print_r($_REQUEST);die;
		$apiParams['query.checkfav'] = $_REQUEST['action'];
		$apiParams['query.key'] = $_REQUEST['key'];
		$apiHttpResponse = i9_ApiRequest::FetchData('favorite', $apiParams, false, 0);
		//print_r($apiHttpResponse['body']);die;
		echo $apiHttpResponse['body'];
		die();
	}
	
	static function Savesearch() {
		
		$apiParams['query.Savesearch'] = $_REQUEST['action'];
		$apiParams['query.sname'] = $_REQUEST['sname'];
		$apiParams['query.semail'] = $_REQUEST['semail'];
		$apiParams['query.sfrequency'] = $_REQUEST['sfrequency'];
		$apiHttpResponse = i9_ApiRequest::FetchData('Savesearch', $apiParams, false, 0);
		//print_r($apiHttpResponse['body']);die;
		echo $apiHttpResponse['body'];
		die();
	}
	
	static function Managesearch() {
		//print_r($_REQUEST);die;
		$apiParams['query.Managesearch'] = $_REQUEST['action'];
		$apiHttpResponse = i9_ApiRequest::FetchData('Managesearch', $apiParams, false, 0);
		//print_r($apiHttpResponse['body']);die;
		echo $apiHttpResponse['body'];
		die();
	}
	
	static function Delsearch() {
		//print_r($_REQUEST);die;
		$apiParams['query.Delsearch'] = $_REQUEST['action'];
		$apiParams['query.searchId'] = $_REQUEST['searchId'];
		$apiHttpResponse = i9_ApiRequest::FetchData('Managesearch', $apiParams, false, 0);
		//print_r($apiHttpResponse['body']);die;
		echo $apiHttpResponse['body'];
		die();
	}
	
	static function Contact() {
		
		$apiParams['query.contact'] = $_REQUEST['action'];
		$apiParams['query.ListKey'] = $_REQUEST['cont_key'];
		$apiParams['query.fname'] = $_REQUEST['cont_firstName'];
		$apiParams['query.lname'] = $_REQUEST['cont_lastName'];
		$apiParams['query.phone'] = $_REQUEST['cont_phone'];
		$apiParams['query.email'] = $_REQUEST['cont_email'];
		$apiParams['query.schedule'] = $_REQUEST['cont_schedule'];
		$apiParams['query.smonth'] = $_REQUEST['cont_month'];
		$apiParams['query.sday'] = $_REQUEST['cont_day'];
		$apiParams['query.comment'] = $_REQUEST['cont_comments'];
		$apiHttpResponse = i9_ApiRequest::FetchData('Contactdata', $apiParams, false, 0);
		//print_r($apiHttpResponse['body']);die;
		echo $apiHttpResponse['body'];
		die();
	}
	
	static function EmailListing() {
		//print_r($_REQUEST);die;
		$apiParams['query.emaillisting'] = $_REQUEST['action'];
		$apiParams['query.frommail'] = $_REQUEST['email_from'];
		$apiParams['query.tomail'] = $_REQUEST['email_to'];
		$apiParams['query.note'] = $_REQUEST['email_note'];
		$apiParams['query.ListKey'] = $_REQUEST['email_key'];
		
		$apiHttpResponse = i9_ApiRequest::FetchData('Contactdata', $apiParams, false, 0);
		//print_r($apiHttpResponse['body']);die;
		echo $apiHttpResponse['body'];
		die();
	}
	
}
if(!empty($_REQUEST['action']))
{
	i9_ClientAssist::call($_REQUEST['action']);
}
else
{
//	die;
}