<?php
class i9_IdxCalcWidget extends WP_Widget {

	public function __construct() {

		parent::__construct("i9-calc-widget", "I-9 IDX Calculator", array(

			"classname" => "cannysys-widget-calc",

			"description" => "Allow users to calculate payments monthly wise."

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

		
		wp_enqueue_script('i9_calc', plugins_url('js/widget-calc.js', __FILE__), array('jquery'), i9_PLUGIN_VERSION, true);
		
		echo $before_widget;

		if ($title)

			echo $before_title . $title . $after_title;
		
		
		echo <<<HTML
		
		<div class="i9idx-resp-calculate">

                   <fieldset>
						
						<div class="i9idx-resp-area" style="text-align:center;font-weight:bold;">
					
							<span id="hidecont" style="display:none;"></span>

						</div>
				
						<div class="i9idx-resp-area">

						<label for="price">Price</label>

						<input id="i9idx_price" name="price" type="text" class="i9idx-price form-control" value="" maxlength="10" onkeypress="return i9calc.validateNum(event)"/>

						</div>

HTML;

		echo <<<HTML

						<div class="i9idx-resp-area">

						<label for="interestrate">Interest Rate (%)</label>

						<input id="i9idx_interestrate" name="interestrate" type="text" class="i9idx-interestrate form-control" maxlength="3" value="{$interest_rate}" onkeypress="return i9calc.validateNum(event)"/>

						</div>

HTML;

		echo <<<HTML

						<div class="i9idx-resp-area">

						<label for="downpayment">Down Payment (%)</label>

						<input id="i9idx_downpayment" name="downpayment" type="text" class="i9idx-downpayment form-control" maxlength="3" value="{$down_pay}" onkeypress="return i9calc.validateNum(event)"/>

						</div>

HTML;
				
		echo <<<HTML
					<div class="i9idx-resp-area">
	
					<label>Loan Type</label>

		    
					<select id="i9idx_loan_type" class="i9idx-resp-select form-control" name="search_prop_type">

                                <option value="30">30 Years Fixed</option>
								<option value="15">15 Years Fixed</option>
								<option value="5">5/1 ARM</option>
								

                            </select>

				</div>
HTML;

				echo <<<HTML

						<div class="i9idx-resp-area">

						<label for="monthlydebts">Est. Tax & Insurance (%)</label>

						<input id="i9idx_monthlydebts" name="monthlydebts" type="text" class="i9idx-tax form-control" maxlength="3" value="{$esttax_rate}" onkeypress="return i9calc.validateNum(event)"/>

						</div>

HTML;

		echo <<<HTML

				<div class="i9idx-resp-area i9idx-resp-area-submit">
					
					<label for="canny-q-submit">&nbsp;</label>

					<input type="submit" class="submit" value="Get Estimate" onclick="i9calc.getcalc();"/>

				</div>

HTML;

		echo <<<HTML
				
			</fieldset>

			</div>

HTML;

		echo $after_widget;

		i9_footer::ensure_disclaimer_exists("search");

	}

	function update($new_instance, $old_instance) {

		$new_instance["title"] = strip_tags($new_instance["title"]);
		
		$new_instance["interest_rate"] = strip_tags($new_instance["interest_rate"]);
		
		$new_instance["esttax_rate"] = strip_tags($new_instance["esttax_rate"]);
		
		$new_instance["down_pay"] = strip_tags($new_instance["down_pay"]);
		
		return $new_instance;

	}

	function form($instance) {

	//	wp_enqueue_script('i9idxpress_widget_search', i9_PLUGIN_URL . 'js/widget-search.js', array('jquery'), i9_PLUGIN_VERSION, true);

		
		$options = get_option(i9_OPTION_NAME);


		$instance = wp_parse_args($instance, array(

			"title" => "Mortgage Calculator Widget",

			"interest_rate" => 5,
			
			"esttax_rate" => 1,
			
			"down_pay" => 10,
			
	
		));


		$title = htmlspecialchars($instance["title"]);

		$interest_rate = htmlspecialchars($instance["interest_rate"]);
		
		$esttax_rate = htmlspecialchars($instance["esttax_rate"]);
		
		$down_pay = htmlspecialchars($instance["down_pay"]);
		
		$loan_type = htmlspecialchars($instance["loan_type"]);

		$titleFieldId = $this->get_field_id("title");

		$titleFieldName = $this->get_field_name("title");

		$irateFieldId = $this->get_field_id("interest_rate");

		$irateFieldName = $this->get_field_name("interest_rate");

		$taxFieldId = $this->get_field_id("esttax_rate");

		$taxFieldName = $this->get_field_name("esttax_rate");
		
		$dwnFieldId = $this->get_field_id("down_pay");

		$dwnFieldName = $this->get_field_name("down_pay");
		
				
		echo <<<HTML

			<p>

				<label for="{$titleFieldId}">Widget title</label>

				<input id="{$titleFieldId}" name="{$titleFieldName}" value="{$title}" class="widefat" type="text" />

			</p>

			<p>
				<label for="{$irateFieldId}">Interest Rate(%)</label>

				<input id="{$irateFieldId}" name="{$irateFieldName}" value="{$interest_rate}" class="widefat" type="text" />
				
			</p>
			
			<p>
				<label for="{$dwnFieldId}">Down Payment (%)</label>

				<input id="{$dwnFieldId}" name="{$dwnFieldName}" value="{$down_pay}" class="widefat" type="text" />
				
			</p>
			
			<p>
				<label for="{$taxFieldId}">Est. Tax & Insurance(%)</label>

				<input id="{$taxFieldId}" name="{$taxFieldName}" value="{$esttax_rate}" class="widefat" type="text" />
				
			</p>
			
			

HTML;

	}

}

?>