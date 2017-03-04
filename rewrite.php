<?php

add_filter("rewrite_rules_array", array("CannysysAgent_Rewrite", "InsertRules"));

add_filter("query_vars", array("CannysysAgent_Rewrite", "SaveQueryVars"));

class CannysysAgent_Rewrite {

	static function GetUrlSlug() {
		
		$options = get_option(i9_OPTION_NAME);
		
		return !empty($options["UseAlternateUrlStructure"]) ? "" : "canny/";

	}

	static function InsertRules($incomingRules) {
		
		$options = get_option(i9_OPTION_NAME);
		
		$cannyRules = array(

			"canny/city/([^/]+)(?:/page\-(\\d+))?"       => 'index.php?i9-action=city&canny-q-Cities=$matches[1]&canny-d-ResultPage=$matches[2]',

			"canny/community/([^/]+)(?:/page\-(\\d+))?"  => 'index.php?i9-action=community&canny-q-Communities=$matches[1]&canny-d-ResultPage=$matches[2]',

			"canny/tract/([^/]+)(?:/page\-(\\d+))?"      => 'index.php?i9-action=results&canny-q-TractIdentifiers=$matches[1]&canny-d-ResultPage=$matches[2]',

			"canny/area/([^/]+)(?:/page\-(\\d+))?"       => 'index.php?i9-action=area&canny-q-Areas=$matches[1]&canny-d-ResultPage=$matches[2]',

			"canny/zip/(\\d+)(?:/page\-(\\d+))?"         => 'index.php?i9-action=zip&canny-q-ZipCodes=$matches[1]&canny-d-ResultPage=$matches[2]',

			"canny/mls-(.+)-.*"                          => 'index.php?i9-action=details&canny-q-MlsNumber=$matches[1]',

			"canny/(\\d+)-mls-(.+)-.*"                   => 'index.php?i9-action=details&canny-q-PropertyID=$matches[1]&canny-q-MlsNumber=$matches[2]',

			"canny/(\\d+)[^/]*(?:/page\-(\\d+))?"        => 'index.php?i9-action=results&canny-q-LinkID=$matches[1]&canny-d-ResultPage=$matches[2]',

			"canny/advanced.*"                           => 'index.php?i9-action=framed',

			"canny/search/?$"                            => 'index.php?i9-action=search',

			"canny(?:/page\-(\\d+))?$"                   => 'index.php?i9-action=results&canny-d-ResultPage=$matches[1]',
			
			"canny/lte-(\\d+)"                           => 'index.php?i9-action=listingkey&canny-q-MlsNumber=$matches[1]',
			
			"canny/favorite.*"                           => 'index.php?i9-action=gofav',
		
		);



		if (!empty($options["UseAlternateUrlStructure"])) {

			$cannyRules["\w{2}/[^/]+/(\\d+)-mls-(.+)-.*"] =

				'index.php?i9-action=details&canny-q-PropertyID=$matches[1]&canny-q-MlsNumber=$matches[2]';

			$cannyRules["(\w{2})/([^/]+)(?:/page\-(\\d+))?"] =

				'index.php?i9-action=results&canny-q-States=$matches[1]&canny-q-Cities=$matches[2]&canny-d-ResultPage=$matches[3]';

			$cannyRules["(\w{2})/([^/]+)/community/([^/]+)(?:/page\-(\\d+))?"] =

				'index.php?i9-action=results&canny-q-States=$matches[1]&canny-q-Communities=$matches[2]&canny-d-ResultPage=$matches[3]';

			$cannyRules["(\w{2})/([^/]+)/tract/([^/]+)(?:/page\-(\\d+))?"] =

				'index.php?i9-action=results&canny-q-States=$matches[1]&canny-q-TractIdentifiers=$matches[2]&canny-d-ResultPage=$matches[3]';

			$cannyRules["(\w{2})/(\\d+)(?:/page\-(\\d+))?"] =

				'index.php?i9-action=results&canny-q-States=$matches[1]&canny-q-ZipCodes=$matches[2]&canny-d-ResultPage=$matches[3]';

		}



		return $cannyRules + $incomingRules;

	}

	static function SaveQueryVars($queryVars) {

		$queryVars[] = "i9-action";

		$queryVars[] = "canny-q-Cities";

		$queryVars[] = "canny-q-Communities";

		$queryVars[] = "canny-q-TractIdentifiers";

		$queryVars[] = "canny-q-Areas";

		$queryVars[] = "canny-q-ZipCodes";

		$queryVars[] = "canny-q-States";

		$queryVars[] = "canny-q-LinkID";

		$queryVars[] = "canny-q-MlsNumber";

		$queryVars[] = "canny-q-PropertyID";

		$queryVars[] = "canny-d-ResultPage";


		// there will be a bunch of other parameters that will be used in the final API call, but we only need to

		// be concerned with the ones in the pseudo- URL rewrite thing. the rest of the parameters will be passed

		// as HTTP GET or POST vars, so we can just use the superglobal $_REQUEST to access those

		return $queryVars;

	}

}

?>