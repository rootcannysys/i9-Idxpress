<?php
add_action('wp_head', array('i9_IdxGuidedSearchWidget', 'LoadScripts'), 100);

class i9_IdxGuidedSearchWidget extends WP_Widget {

	public function __construct() {

		parent::__construct("cannysys-guided-search", "I-9 IDX Guided Search", array(

			"classname" => "cannysys-widget-guided-search",

			"description" => "Allow users to search from a curated list of cities, communites, tracts and/or zip codes."

		));

	}

	public static function LoadScripts(){

        i9_autocomplete::AddScripts(true);

    }

	function widget($args, $instance) {

		extract($args);

		extract($instance);

		$title = apply_filters("widget_title", $title);

		$options = get_option(i9_OPTION_NAME);



		if (!isset($options["Activated"]) || !$options["Activated"])

			return;


		wp_enqueue_script('i9_calc', plugins_url('js/widget-calc.js', __FILE__), array('jquery'), i9_PLUGIN_VERSION, true);
		
		$pluginUrl = i9_PLUGIN_URL;

		$formAction = get_home_url() . "/canny/page-1";

	//	wp_enqueue_script('i9idxpress_widget_search_view', plugins_url('js/widget-client.js', __FILE__ ), array('jquery'), i9_PLUGIN_VERSION, true);


	/*	$capabilities = CannysysAgent_ApiRequest::FetchData('MlsCapabilities');

		$capabilities = json_decode($capabilities['body'], true);

		

		$defaultSearchPanels = CannysysAgent_ApiRequest::FetchData("AccountSearchPanelsDefault", array(), false, 60 * 60 * 24);

		$defaultSearchPanels = $defaultSearchPanels["response"]["code"] == "200" ? json_decode($defaultSearchPanels["body"]) : null;

		$propertyTypes = CannysysAgent_ApiRequest::FetchData("AccountSearchSetupFilteredPropertyTypes", array(), false, 60 * 60 * 24);

		$propertyTypes = $propertyTypes["response"]["code"] == "200" ? json_decode($propertyTypes["body"]) : null;



		$account_options = CannysysAgent_ApiRequest::FetchData("AccountOptions", array(), false);

		$account_options = $account_options["response"]["code"] == "200" ? json_decode($account_options["body"]) : null;
	*/


		$values =array();

		$values['location'] = isset($_GET['location']) ? $_GET['location'] : null;

		$values['search_prop_type'] = isset($_GET['search_prop_type']) ? $_GET['search_prop_type'] : null;

		$values['canny-q-Cities'] = isset($_GET['canny-q-Cities']) ? $_GET['canny-q-Cities'] : null;

		$values['canny-q-Communities'] = isset($_GET['canny-q-Communities']) ? $_GET['canny-q-Communities'] : null;

		$values['postalcode'] = isset($_GET['postalcode']) ? $_GET['postalcode'] : null;

		$values['search_minprice'] = isset($_GET['search_minprice']) ? $_GET['search_minprice'] : null;

		$values['search_maxprice'] = isset($_GET['search_maxprice']) ? $_GET['search_maxprice'] : null;

		$values['search_bedrooms'] = isset($_GET['search_bedrooms']) ? $_GET['search_bedrooms'] : null;

		$values['search_bathrooms'] = isset($_GET['search_bathrooms']) ? $_GET['search_bathrooms'] : null;

		$values['minsqft'] = isset($_GET['minsqft']) ? $_GET['minsqft'] : null;



		$specialSlugs = array(

			'city' 		=> 'canny-q-Cities',

			'community' => 'canny-q-Communities',

			'zip' 		=> 'canny-q-ZipCodes'

		);



		$urlParts = explode('/', $_SERVER['REQUEST_URI']);

		$count = 0;

		foreach($urlParts as $p){

			if(array_key_exists($p, $specialSlugs) && isset($urlParts[$count + 1])){

				$values[$specialSlugs[$p]] = $urlParts[$count + 1];

			}

			$count++;

		}

		
		echo $before_widget;

		if ($title)

			echo $before_title . $title . $after_title;



		echo <<<HTML

			<div class="i9idx-resp-search-box i9idx-widget i9idx-resp-vertical">

			<label id="idx-search-invalid-msg" style="color:red"></label>

			<form class="i9idx-resp-search-form" action="{$formAction}" method="get" >

				<fieldset>

				<div class="i9idx-resp-area">

				<label>Property Type</label>

HTML;

          //  $selected = strtolower($city) == strtolower($values['canny-q-Cities'])?' selected="selected"':'';                   
           
		    echo <<<HTML
				<select id="search_prop_type" class="i9idx-resp-select form-control" name="search_prop_type">

                                <option value="">Any</option>
								<option value="Commercial">Commercial</option>
								<option value="Lot-Land">Lot-Land</option>
								<option value="single family">Single-Family</option>
								<option value="Multi-Family">Multi-Family</option>
								<option value="condos">Condos</option>
								<option value="Townhouse">Townhouse</option>
								<option value="MANUFACTURED HOME">Manufactured Home</option>
								<option value="farm">Farm</option>
								<option value="CL">Commercial-Lease</option>
HTML;

                                
                                echo <<<HTML

                            </select>

				</div>



				<div class="i9idx-resp-area">

HTML;

		if ($searchOptions['show_cities'] == 'yes' && !empty($searchOptions['cities'])) {

			echo <<<HTML

				<label>City</label>
				
					<!--<input placeholder="City Name" name="canny-q-Cities" type="text" class="text idx-q-Location-Filter form-control" id="canny-q-Cities" value="{$values['canny-q-Cities']}" autocomplete="off">-->
					
				<select id="canny-q-Cities" name="canny-q-Cities" class="idx-q-Location-Filter form-control">

					<option value="">Any</option>

HTML;

			foreach ($searchOptions["cities"] as $city) {

				// there's an extra trim here in case the data was corrupted before the trim was added in the update code below

				$city = htmlentities(trim($city));

				$selected = strtolower($city) == strtolower($values['canny-q-Cities'])?' selected="selected"':'';

				echo "<option value=\"{$city}\"{$selected}>{$city}</option>";

			}

			 echo '</select>';

			echo '</div>';

		}

		if($searchOptions['show_communities'] == 'yes' && !empty($searchOptions['communities'])) {

			echo <<<HTML

				<div class="i9idx-resp-area">

				<label>Community</label>

				<select id="canny-q-Communities" name="canny-q-Communities" class="idx-q-Location-Filter form-control">

					<option value="">Any</option>

HTML;

			foreach ($searchOptions['communities'] as $community) {

				// there's an extra trim here in case the data was corrupted before the trim was added in the update code below

				$community = htmlentities(trim($community));

				$selected = strtolower($community) == strtolower($values['canny-q-Communities'])?' selected="selected"':'';

				echo "<option value=\"{$community}\"{$selected}>{$community}</option>";

			}

			echo '</select>';

			echo '</div>';

		}

		if($searchOptions['show_zips'] == 'yes' && !empty($searchOptions['zips'])) {

			echo <<<HTML

				<div class="i9idx-resp-area">

				<label>Zipcode</label>

				<select id="postalcode" name="postalcode" class="idx-q-Location-Filter form-control">

					<option value="">Any</option>

HTML;

			foreach ($searchOptions["zips"] as $zip) {

				// there's an extra trim here in case the data was corrupted before the trim was added in the update code below

				$zip = htmlentities(trim($zip));

				$selected = strtolower($zip) == strtolower($values['postalcode']) ? ' selected="selected"' : '';

				echo "<option value=\"{$zip}\"{$selected}>{$zip}</option>";

			}

			echo '</select>';

			echo '</div>';

		}

		echo <<<HTML

				<div class="i9idx-resp-area i9idx-resp-area-half i9idx-resp-area-half i9idx-resp-area-left">

				<label for="search_minprice" class="i9idx-resp-price">PriceMin</label>

                            <select id="search_minprice" name="search_minprice" class="i9idx-baths form-control">

                                <option value="">Any</option>
								<option value="50000">50000</option>
								<option value="60000">60000</option>
								<option value="70000">70000</option>
								<option value="80000">80000</option>
								<option value="90000">90000</option>
HTML;

                               for($i=100000;$i<=1000000;$i=$i+25000){

                                    $selected = $i == $values['search_minprice']?' selected="selected"':'';

                                    echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
									
									if($i>=550000 && $i<=3000000)
									$i=$i+25000;
									else if($i>3000000)
									$i=$i+475000;

                                }

                            echo <<<HTML

                            </select>

</div>

				<div class="i9idx-resp-area i9idx-resp-area-half i9idx-resp-area-half i9idx-resp-area-right">

				<label for="search_maxprice" class="i9idx-resp-price">PriceMax</label>

                            <select id="search_maxprice" name="search_maxprice" class="i9idx-baths form-control">

                                <option value="">Any</option>
								<option value="50000">50000</option>
								<option value="60000">60000</option>
								<option value="70000">70000</option>
								<option value="80000">80000</option>
								<option value="90000">90000</option>
HTML;

                                for($i=100000;$i<=1000000;$i=$i+25000){

                                    $selected = $i == $values['search_maxprice']?' selected="selected"':'';

                                    echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
									
									if($i>=550000 && $i<=3000000)
									$i=$i+25000;
									else if($i>3000000)
									$i=$i+475000;

                                }

                            echo <<<HTML

                            </select>

				</div>

HTML;

		

		echo <<<HTML

				<div class="i9idx-resp-area i9idx-resp-min-baths-area i9idx-resp-area-half i9idx-resp-area-left">

				<label for="search_bedrooms">Beds</label>

				<select id="search_bedrooms" name="search_bedrooms" class="i9idx-beds form-control">

					<option value="">Any</option>

HTML;

					for($i=1; $i<=5; $i++){

						$selected = $i == $values['search_bedrooms']?' selected="selected"':'';

						echo '<option value="'.$i.'"'.$selected.'>'.$i.'+</option>';

					}

				echo <<<HTML

				</select>

				</div>



				<div class="i9idx-resp-area i9idx-resp-min-baths-area i9idx-resp-area-half i9idx-resp-area-right">

				<label for="search_bathrooms">Baths</label>

				<select id="search_bathrooms" name="search_bathrooms" class="i9idx-baths form-control">

					<option value="">Any</option>

HTML;

					for($i=1; $i<=5; $i++){

						$selected = $i == $values['search_bathrooms']?' selected="selected"':'';

						echo '<option value="'.$i.'"'.$selected.'>'.$i.'+</option>';

					}

				echo <<<HTML

				</select>

				</div>

HTML;

				/*if(isset($defaultSearchPanels)){

				foreach ($defaultSearchPanels as $key => $value) {

				if ($value->DomIdentifier == "search-input-home-size" && isset($capabilities['MinImprovedSqFt']) && $capabilities['MinImprovedSqFt'] > 0) {*/

					echo <<<HTML

						<div class="i9idx-resp-area">

						<label for="minsqft">Min Sqft</label>

						<input id="minsqft" name="minsqft" type="text" class="i9idx-improvedsqft form-control" maxlength="10" value="{$values['minsqft']}" onkeypress="return i9_calc.validateNum(event)"/>

						</div>

HTML;

				//	break;

				/*}

			}

		}*/



		echo <<<HTML

				<div class="i9idx-resp-area i9idx-resp-area-submit">
					
					<label for="submit">&nbsp;</label>

					<input type="submit" class="submit" value="Search for properties" />

				</div>

HTML;

		if($options["HasSearchAgentPro"] == "yes" && $searchOptions["show_advanced"] == "yes"){

			echo <<<HTML

					<div style="float: right;">

					try our&nbsp;<a href="{$formAction}advanced/"><img src="{$pluginUrl}assets/adv_search-16.png" /> Advanced Search</a>

					</div>

HTML;

		}

		/*if($account_options->EulaLink){

			$eula_url = $account_options->EulaLink;

			echo <<<HTML

					<p>By searching, you agree to the <a href="{$eula_url}" target="_blank">EULA</a></p>

HTML;

		}*/

		echo <<<HTML

			</fieldset>

			</form>

			</div>

HTML;

		

		echo $after_widget;

		i9_footer::ensure_disclaimer_exists("search");

	}

	function update($new_instance, $old_instance) {

		$new_instance["title"] = strip_tags($new_instance["title"]);

		$new_instance["searchOptions"]["cities"] = explode("\n", $new_instance["searchOptions"]["cities"]);

		$new_instance["searchOptions"]["zips"] = explode("\n", $new_instance["searchOptions"]["zips"]);

		$new_instance["searchOptions"]["communities"] = explode("\n", $new_instance["searchOptions"]["communities"]);



		if ($new_instance["searchOptions"]["sortZips"])

			sort($new_instance["searchOptions"]["zips"]);



		if ($new_instance["searchOptions"]["sortCities"])

			sort($new_instance["searchOptions"]["cities"]);


		if ($new_instance["searchOptions"]["sortCommunities"])

			sort($new_instance["searchOptions"]["communities"]);

		// we don't need to store this option

		unset($new_instance["searchOptions"]["sortCities"]);

		unset($new_instance["searchOptions"]["sortCommunities"]);

		unset($new_instance["searchOptions"]["sortZips"]);



		foreach ($new_instance["searchOptions"]["cities"] as &$area)

			$area = trim($area);

		foreach ($new_instance["searchOptions"]["communities"] as &$area)

			$area = trim($area);

		foreach ($new_instance["searchOptions"]["zips"] as &$area)

			$area = trim($area);



		/* we're doing this conversion from on/null to yes/no so that we can detect if the show_cities has never been

		 * set, because if it's never been set we want it to show */

		if($new_instance["searchOptions"]["show_cities"] == "on") $new_instance["searchOptions"]["show_cities"] = "yes";

		else $new_instance["searchOptions"]["show_cities"] = "no";



		if($new_instance["searchOptions"]["show_communities"] == "on") $new_instance["searchOptions"]["show_communities"] = "yes";

		else $new_instance["searchOptions"]["show_communities"] = "no";


		if($new_instance["searchOptions"]["show_zips"] == "on") $new_instance["searchOptions"]["show_zips"] = "yes";

		else $new_instance["searchOptions"]["show_zips"] = "no";


		//if($new_instance["searchOptions"]["show_advanced"] == "on") $new_instance["searchOptions"]["show_advanced"] = "yes";

		//else $new_instance["searchOptions"]["show_advanced"] = "no";



		return $new_instance;

	}

	function form($instance) {

		wp_enqueue_script('i9idxpress_widget_search', plugins_url('js/widget-search.js', __FILE__ ), array('jquery'), i9_PLUGIN_VERSION, true);


		$pluginUrl = i9_PLUGIN_URL;


		$options = get_option(i9_OPTION_NAME);


		$instance = wp_parse_args($instance, array(

			"title" => "Real Estate Guided Search",

			"searchOptions" => array(

				"cities" => array(),

				"communities" => array(),

				"zips" => array(),

				"show_cities" => 'yes',

				"show_communities" => 'no',

				"show_zips" => 'no',

				"show_advanced" => 'no'

			)

		));


		$title = htmlspecialchars($instance["title"]);

		$cities = htmlspecialchars(implode("\n", (array)$instance["searchOptions"]["cities"]));

		$communities = htmlspecialchars(implode("\n", (array)$instance["searchOptions"]["communities"]));

		$zips = htmlspecialchars(implode("\n", (array)$instance["searchOptions"]["zips"]));


		$titleFieldId = $this->get_field_id("title");

		$titleFieldName = $this->get_field_name("title");

		$searchOptionsFieldId = $this->get_field_id("searchOptions");

		$searchOptionsFieldName = $this->get_field_name("searchOptions");



		$show_cities = $instance["searchOptions"]["show_cities"] == "yes" || !isset($instance["searchOptions"]["show_cities"]) ? "checked=\"checked\" " : "";

		$show_communities = $instance["searchOptions"]["show_communities"] == "yes" ? "checked=\"checked\" " : "";

		$show_zips = $instance["searchOptions"]["show_zips"] == "yes" ? "checked=\"checked\" " : "";

		$show_advanced = $instance["searchOptions"]["show_advanced"] == "yes" ? "checked=\"checked\" " : "";


		echo <<<HTML

			<p>

				<label for="{$titleFieldId}">Widget title</label>

				<input id="{$titleFieldId}" name="{$titleFieldName}" value="{$title}" class="widefat" type="text" />

			</p>



			<p>

				<h4>Fields to Display</h4>

				<div id="{$searchOptionsFieldId}-show_checkboxes" class="search-widget-searchOptions">

					<input type="checkbox" id="{$searchOptionsFieldId}-show_cities" name="{$searchOptionsFieldName}[show_cities]" {$show_cities} onclick="dsWidgetSearch.ShowBlock(this);"/>

					<label for="{$searchOptionsFieldId}-show_cities">Cities</label><br />

					<input type="checkbox" id="{$searchOptionsFieldId}-show_communities" name="{$searchOptionsFieldName}[show_communities]" {$show_communities} onclick="dsWidgetSearch.ShowBlock(this);"/>

					<label for="{$searchOptionsFieldId}-show_communities">Communities</label><br />

					<input type="checkbox" id="{$searchOptionsFieldId}-show_zips" name="{$searchOptionsFieldName}[show_zips]" {$show_zips} onclick="dsWidgetSearch.ShowBlock(this);"/>

					<label for="{$searchOptionsFieldId}-show_zips">Zipcodes</label><br />


HTML;

		if($options["HasSearchAgentPro"] == "yes") {

			echo <<<HTML

					<!--<input id="{$searchOptionsFieldId}-show-advanced" name="{$searchOptionsFieldName}[show_advanced]" class="checkbox" type="checkbox" {$show_advanced} onclick="dsWidgetSearch.ShowBlock(this);"/>

					<label for="{$searchOptionsFieldId}-show-advanced">Show Advanced Option</label>-->

HTML;

		}

			echo <<<HTML

				</div>

			</p>



			<div id="{$searchOptionsFieldId}-cities_block">

				<h4>Cities (one per line)</h4>

				<p>

					<textarea id="{$searchOptionsFieldId}[cities]" name="{$searchOptionsFieldName}[cities]" class="widefat" rows="10">{$cities}</textarea>

				</p>

				<p>

					<label for="{$searchOptionsFieldId}[sortCities]">Sort Cities</label>

					<input id="{$searchOptionsFieldId}[sortCities]" name="{$searchOptionsFieldName}[sortCities]" class="checkbox" type="checkbox" />

				</p>

				<p>

					<!--<span class="description">See all City Names <a href="javascript:void(0);" onclick="dsWidgetSearch.LaunchLookupList('{$pluginUrl}locations.php?type=city')">here</a></span>-->

				</p>

				<hr noshade="noshade" />

			</div>

			<div id="{$searchOptionsFieldId}-communities_block">

				<h3>Communities (one per line)</h3>

				<p>

					<textarea id="{$searchOptionsFieldId}[communities]" name="{$searchOptionsFieldName}[communities]" class="widefat" rows="10">{$communities}</textarea>

				</p>

				<p>

					<label for="{$searchOptionsFieldId}[sortCommunities]">Sort Communities</label>

					<input id="{$searchOptionsFieldId}[sortCommunities]" name="{$searchOptionsFieldName}[sortCommunities]" class="checkbox" type="checkbox" />

				</p>

				<p>

					<!--<span class="description">See all Community Names <a href="javascript:void(0);" onclick="dsWidgetSearch.LaunchLookupList('{$pluginUrl}locations.php?type=community')">here</a></span>-->

				</p>

				<hr noshade="noshade" />

			</div>



			<div id="{$searchOptionsFieldId}-zips_block">

				<h3>Zipcodes (one per line)</h3>

				<p>

					<textarea id="{$searchOptionsFieldId}[zips]" name="{$searchOptionsFieldName}[zips]" class="widefat" rows="10">{$zips}</textarea>

				</p>

				<p>

					<label for="{$searchOptionsFieldId}[sortZips]">Sort Zipcodes</label>

					<input id="{$searchOptionsFieldId}[sortZips]" name="{$searchOptionsFieldName}[sortZips]" class="checkbox" type="checkbox" />

				</p>

				<p>

					<!--<span class="description">See all Zips <a href="javascript:void(0);" onclick="dsWidgetSearch.LaunchLookupList('{$pluginUrl}locations.php?type=zip')">here</a></span>-->

				</p>

			</div>

			<script> jQuery(document).ready(function() { if(typeof dsWidgetSearch != "undefined") { dsWidgetSearch.InitFields(); } }); </script>

HTML;

	}

}

?>