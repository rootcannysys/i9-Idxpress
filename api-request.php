<?php

class i9_ApiRequest{



    public static $ApiEndPoint = "";



	// do NOT change this value or you will be automatically banned from the API. since the data is only updated every two hours, and



	// since these API calls are computationally intensive on our servers, we need to set a reasonable cache duration.



	private static $CacheSeconds = 7200;







	static function FetchData($action, $params = array(), $echoAssetsIfNotEnqueued = true, $cacheSecondsOverride = null, $options = null, $headers = array()) {

		

		global $wp_query, $wp_version;



		require_once(ABSPATH . "wp-admin/includes/plugin.php");



		$pluginData = get_plugin_data(__FILE__);



		$pluginVersion = $pluginData["Version"];



		//$options = $options ? $options : get_option("i9idxwidgets-options");



		$requestUri = self::$ApiEndPoint . $action;



		$compressCache = function_exists('gzdeflate') && function_exists('gzinflate');







		$idxpress_options = get_option(i9_OPTION_NAME);







		if(!empty($idxpress_options['AccountID']) && !empty($idxpress_options['SearchSetupID'])){



			$params["query.SearchSetupID"] = $idxpress_options["SearchSetupID"];



			$params["requester.AccountID"] = $idxpress_options["AccountID"];



		}



		else{



			$params["query.SearchSetupID"] = $options["SearchSetupID"];



			$params["requester.AccountID"] = $options["AccountID"];



		}



		
		if(!isset($params["requester.ApplicationProfile"]))



			$params["requester.ApplicationProfile"] = "WordPressIdxModule";



		$params["requester.ApplicationVersion"] = $wp_version;



		$params["requester.PluginVersion"] = $pluginVersion;

		
		$params["requester.RequesterUri"] = get_home_url();



		



		if(isset($_COOKIE['i9idx-visitor-public-id']))



			$params["requester.VisitorPublicID"] = $_COOKIE['i9idx-visitor-public-id'];



		if(isset($_COOKIE['i9idx-visitor-auth']))



			$params["requester.VisitorAuth"] = $_COOKIE['i9idx-visitor-auth'];



		



		if(isset($_COOKIE['i9idx-visitor-details-views']))



			$params["requester.VisitorDetailViews"] = $_COOKIE['i9idx-visitor-details-views'];



		if(isset($_COOKIE['i9idx-visitor-results-views']))



			$params["requester.VisitorResultsViews"] = $_COOKIE['i9idx-visitor-results-views'];







		ksort($params);



		$transientKey = "canny_" . sha1($action . "_" . http_build_query($params));



		



		if ($cacheSecondsOverride !== 0) {



			$cachedRequestData = get_transient($transientKey);



			if ($cachedRequestData) {



				$cachedRequestData = $compressCache ? unserialize(gzinflate(base64_decode($cachedRequestData))) : $cachedRequestData;



				return $cachedRequestData;



			}



		}



		

		if(isset($_COOKIE['i9idx-lead-id']))

		

			$params["requester.leadAuth"] = $_COOKIE['i9idx-lead-id'];





		// these params need to be beneath the caching stuff since otherwise the cache will be useless



		 $params["requester.ClientIpAddress"] = $_SERVER["REMOTE_ADDR"];



		$params["requester.ClientUserAgent"] = $_SERVER["HTTP_USER_AGENT"];



		if(isset($_SERVER["HTTP_REFERER"]))



			$params["requester.UrlReferrer"] = $_SERVER["HTTP_REFERER"];



		$params["requester.UtcRequestDate"] = gmdate("c");



		



		ksort($params);



		$stringToSign = "";



		foreach ($params as $key => $value) {



			$stringToSign .= "$key:$value\n";



			if (!isset($params[$key]))



				$params[$key] = "";



		}

		

		$stringToSign = rtrim($stringToSign, "\n"); 

		

		$linkz = "http://localhost/i9IdxAPI/";

		//$linkz = "http://preprod.idx.i9techus.com/i9IdxAPI/";

		
		

		if($action == 'BindToRequester'){

				$requestUri = $linkz."activation.php";

		}

		else if($action == 'MlsCapabilities'){

				$requestUri = $linkz."MlsCapabilities.php";

		}

		else if($action == 'Diagnostics'){

			//print_r($params);die;

			//$requestUri = $linkz."diagnostics.php?apiKey=".$params["apiKey"]."&requester_AccountID=".$params["requester.AccountID"]."&requester_RequesterUri=".$params["requester.RequesterUri"];

			//header('Location:'.$requestUri);

			//	echo $requestUri;die;

			$requestUri = $linkz."diagnostics.php";

		}

		else if($action == 'city'){

			$requestUri = $linkz."communitydata.php?city=".get_query_var('canny-q-Cities')."&page=".get_query_var('canny-d-ResultPage')."&".$_SERVER['QUERY_STRING'];

		}

		else if($action == 'community'){

			$requestUri = $linkz."communitydata.php?community=".get_query_var('canny-q-Communities')."&page=".get_query_var('canny-d-ResultPage')."&".$_SERVER['QUERY_STRING'];

		}

		else if($action == 'area'){

			$requestUri = $linkz."communitydata.php?area=".get_query_var('canny-q-Areas')."&page=".get_query_var('canny-d-ResultPage')."&".$_SERVER['QUERY_STRING'];

		}

		else if($action == 'zip'){

			$requestUri = $linkz."communitydata.php?zip=".get_query_var('canny-q-ZipCodes')."&page=".get_query_var('canny-d-ResultPage')."&".$_SERVER['QUERY_STRING'];

		}

		else if($action == 'listingkey'){

			$requestUri = $linkz."keydata.php?listingkey=".get_query_var('canny-q-MlsNumber');
		
		}

		else if($action == 'Autocomplete'){

			$requestUri = $linkz."ourdata.php?".$_SERVER['QUERY_STRING']."&function=".$action;

		}

		else if($action == 'listings'){

			$requestUri = $linkz."listingdata.php";

		}

		else if($action == 'Register'){

			$requestUri = $linkz."logindata.php";

		}

		else if($action == 'ForgotPwd'){

			$requestUri = $linkz."logindata.php";

		}

		else if($action == 'favorite'){

			$requestUri = $linkz."logindata.php";

		}

		else if($action == 'Savesearch'){

			$requestUri = $linkz."logindata.php";

		}

		else if($action == 'gofav'){

			$requestUri = $linkz."communitydata.php?favorite=1";

		}

		else if($action == 'Managesearch'){

			$requestUri = $linkz."logindata.php";

		}

		else if($action == 'opnehouses'){

			$requestUri = $linkz."listOpenhouse.php";

		}
		
		else if($action == 'Contactdata'){
			
			$requestUri = $linkz."contactdata.php";
		
		}

		else{

			$requestUri = $linkz."getdata.php?".$_SERVER['QUERY_STRING']."&function=".$action."&page=".get_query_var('canny-d-ResultPage');

		}

		
		//echo $requestUri;die;
		

		$response = (array)wp_remote_post($requestUri, array(



			"body"			=> $params,



			"redirection"	=> "0",



			"headers"       => $headers,



			"timeout"		=> 15, // we look into anything that takes longer than 2 seconds to return



			"reject_unsafe_urls" => false



		));

		

		//if($action == 'Register')

		//print_r($response);die;

		

		if (empty($response["errors"]) && substr($response["response"]["code"], 0, 1) != "5") {



			if ($cacheSecondsOverride !== 0 && $response["body"]){



			//	set_transient($transientKey, $compressCache ? base64_encode(gzdeflate(serialize($response))) : $response, $cacheSecondsOverride === null ? self::$CacheSeconds : $cacheSecondsOverride);



			}



		}



		return $response;



	}

	

	private static function FilterData($data) {

		global $wp_version;



		$blog_url = get_home_url();



		$blogUrlWithoutProtocol = str_replace("http://", "", $blog_url);

		$blogUrlDirIndex = strpos($blogUrlWithoutProtocol, "/");

		$blogUrlDir = "";

		if ($blogUrlDirIndex) // don't need to check for !== false here since WP prevents trailing /'s

			$blogUrlDir = substr($blogUrlWithoutProtocol, strpos($blogUrlWithoutProtocol, "/"));



		$idxActivationPath = $blogUrlDir . "/" . CannysysAgent_Rewrite::GetUrlSlug();



		$i9idxpress_options = get_option(i9_OPTION_NAME);

		$i9idxpress_option_keys_to_output = array("ResultsDefaultState", "ResultsMapDefaultState");

		$i9idxpress_options_to_output = array();



		if(!empty($i9idxpress_options)){

			foreach($i9idxpress_options as $key => $value)

			{

				if(in_array($key, $i9idxpress_option_keys_to_output))

					$i9idxpress_options_to_output[$key] = $value;

			}

		}



		//$data = str_replace('{$pluginUrlPath}', self::MakePluginsUrlRelative(plugin_dir_url(__FILE__)), $data);

		$data = str_replace('{$pluginVersion}', i9_PLUGIN_VERSION, $data);

		$data = str_replace('{$wordpressVersion}', $wp_version, $data);

		$data = str_replace('{$wordpressBlogUrl}', $blog_url, $data);

		$data = str_replace('{$wordpressBlogUrlEncoded}', urlencode($blog_url), $data);

		$data = str_replace('{$wpOptions}', json_encode($i9idxpress_options_to_output), $data);



		$data = str_replace('{$idxActivationPath}', $idxActivationPath, $data);

		$data = str_replace('{$idxActivationPathEncoded}', urlencode($idxActivationPath), $data);



		return $data;

	

	}



	private static function ExtractAndEnqueueStyles($data, $echoAssetsIfNotEnqueued) {

		// since we 100% control the data coming from the API, we can set up a regex to look for what we need. regex

		// is never ever ideal to parse html, but since neither wordpress nor php have a HTML parser built in at the

		// time of this writing, we don't really have another choice. in other words, this is super delicate!



		preg_match_all('/<link\s*rel="stylesheet"\s*type="text\/css"\s*href="(?P<href>[^"]+)"\s*data-handle="(?P<handle>[^"]+)"\s*\/>/', $data, $styles, PREG_SET_ORDER);

		foreach ($styles as $style) {

			if (!$echoAssetsIfNotEnqueued || ($echoAssetsIfNotEnqueued && wp_style_is($style["handle"], 'registered')))

				$data = str_replace($style[0], "", $data);



			if ($echoAssetsIfNotEnqueued)

				wp_register_style($style["handle"], $style["href"], false, null);

			else

				wp_enqueue_style($style["handle"], $style["href"], false, null);

		}



		return $data;

	}

	private static function ExtractAndEnqueueScripts($data) {

		// see comment in ExtractAndEnqueueStyles



		global $wp_scripts;

		preg_match_all('/<script\s*src="(?P<src>[^"]+)"\s*data-handle="(?P<handle>[^"]+)"><\/script>/', $data, $scripts, PREG_SET_ORDER);

		foreach ($scripts as $script) {

			$alreadyIncluded = wp_script_is($script['handle']);

			if (!$alreadyIncluded) {

				wp_register_script($script["handle"], $script["src"], array('jquery'), i9_PLUGIN_VERSION);

				wp_enqueue_script($script["handle"]);				

			}

			$data = str_replace($script[0], "", $data);

		}

		return $data;



	}



}



?>