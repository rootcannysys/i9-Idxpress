<?php
class i9_IdxBudgetWidget extends WP_Widget {

	public function __construct() {

		parent::__construct("i9-budget-widget", "I-9 IDX Budget", array(

			"classname" => "cannysys-widget-budget",

			"description" => "Allow users to search from a curated list of prices."

		));

	}

	function widget($args, $instance) {

		extract($args);

		extract($instance);

		$title = apply_filters("widget_title", $title);

		$options = get_option(i9_OPTION_NAME);



		if (!isset($options["Activated"]) || !$options["Activated"])

			return;
		
		if (!isset($options["HasSearchAgentPro"]) || !$options["HasSearchAgentPro"])

			return;


		$formAction = get_home_url() . "/canny/page-1";

	
		echo $before_widget;

		if ($title)

			echo $before_title . $title . $after_title;
		
		
		echo <<<HTML
		
		<div class="i9idx-resp-budget">

                <form class="i9idx-resp-search-form" action="{$formAction}" method="GET">

                    <fieldset>
						
						<div class="i9idx-resp-area">

						<label for="annualincome">Budget Type</label>
						
						</div>
						
						<div class="form-inline">
						
						<input id="byannual" name="budgettype" type="radio" class="i9idx-improvedsqft" value="1" checked/>
						
						<label for="annualincome">by annual income</label>
						
						</div>
						
						<div class="form-inline">
						
						<input id="bymonthly" name="budgettype" type="radio" class="i9idx-improvedsqft" value="2" />
						
						<label for="annualincome">by monthly payment</label>

						</div>
						

						<div class="i9idx-resp-area">

						<label for="annualincome">ANNUAL INCOME</label>

						<input id="annualincome" name="annualincome" type="text" class="i9idx-improvedsqft form-control" value="{$values['canny-q-annualincome']}" />

						</div>

HTML;

		echo <<<HTML

						<div class="i9idx-resp-area">

						<label for="downpayment">DOWN PAYMENT</label>

						<input id="downpayment" name="downpayment" type="text" class="i9idx-improvedsqft form-control" value="{$values['canny-q-downpayment']}" />

						</div>

HTML;

				echo <<<HTML

						<div class="i9idx-resp-area">

						<label for="monthlydebts">MONTHLY DEBTS</label>

						<input id="monthlydebts" name="monthlydebts" type="text" class="i9idx-improvedsqft form-control" value="{$values['canny-q-monthlydebts']}" />

						</div>

HTML;

		echo <<<HTML

				<div class="i9idx-resp-area i9idx-resp-area-submit">
					
					<label for="canny-q-submit">&nbsp;</label>

					<input type="button" class="submit" value="Show Listings" />

				</div>

HTML;

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

		//$new_instance = $new_instance["quicksearchOptions"];
		
		return $new_instance;

	}

	function form($instance) {

	//	wp_enqueue_script('i9idxpress_widget_search', i9_PLUGIN_URL . 'js/widget-search.js', array('jquery'), i9_PLUGIN_VERSION, true);

		
		$options = get_option(i9_OPTION_NAME);


		$instance = wp_parse_args($instance, array(

			"title" => "Real Estate Budget Widget",

			"budget_type" => 1,
			
		));


		$title = htmlspecialchars($instance["title"]);

		$budget_type = htmlspecialchars($instance["budget_type"]);

		$titleFieldId = $this->get_field_id("title");

		$titleFieldName = $this->get_field_name("title");

		$searchOptionsFieldId = $this->get_field_id("budget_type");

		$searchOptionsFieldName = $this->get_field_name("budget_type");


		$budget_type1 = ($instance["budget_type"] == 1) ? "checked=\"checked\" " : "";
		
		$budget_type2 = ($instance["budget_type"] == 2) ? "checked=\"checked\" " : "";

		
		echo <<<HTML

			<p>

				<label for="{$titleFieldId}">Widget title</label>

				<input id="{$titleFieldId}" name="{$titleFieldName}" value="{$title}" class="widefat" type="text" />

			</p>



			<!--<p>

				<h4>Fields to Display</h4>

				<div id="{$searchOptionsFieldId}-show_checkboxes" class="search-widget-searchOptions">

					<input type="radio" id="{$searchOptionsFieldId}-by_annual" name="{$searchOptionsFieldName}" {$budget_type1}/>

					<label for="{$searchOptionsFieldId}-by_annual">by Annual Income</label><br />

					<input type="radio" id="{$searchOptionsFieldId}-by_monthly" name="{$searchOptionsFieldName}" {$budget_type2}/>
					
					<label for="{$searchOptionsFieldId}-by_monthly">by Monthly Payment</label><br />
				
HTML;

		echo <<<HTML

				</div>

			</p>-->


HTML;

	}

}

?>