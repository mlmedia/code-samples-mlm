<?php

/* get the settings objects */
$g_opt = get_option('lec_general_settings');
$h_opt = get_option('lec_housing_settings');
$u_opt = get_option('lec_upkeep_settings');
$l_opt = get_option('lec_lifestyle_settings');
$f_opt = get_option('lec_form_settings');

/* calculate the "Our Cost" totals for the report sections */
$housing_total_cost = cost_sum(array(
	$h_opt['lec_mortgage_rent_our_cost'],
	$h_opt['lec_home_insurance_our_cost'],
	$h_opt['lec_hoa_our_cost'],
	$h_opt['lec_tax_our_cost'],
	$h_opt['lec_security_our_cost'],
	$h_opt['lec_emergency_our_cost']
));

$upkeep_total_cost = cost_sum(array(
	$u_opt['lec_home_maintenance_our_cost'],
	$u_opt['lec_appliances_our_cost'],
	$u_opt['lec_housekeeping_our_cost'],
	$u_opt['lec_water_sewage_our_cost'],
	$u_opt['lec_gas_electric_our_cost'],
	$u_opt['lec_groundskeeping_our_cost']
));

$lifestyle_total_cost = cost_sum(array(
	$l_opt['lec_transportation_our_cost'],
	$l_opt['lec_health_club_our_cost'],
	$l_opt['lec_activities_our_cost'],
	$l_opt['lec_restaurants_our_cost'],
	$l_opt['lec_groceries_our_cost']
));

$cost_grand_total = cost_sum(array(
	$housing_total_cost,
	$upkeep_total_cost,
	$lifestyle_total_cost
));

/* validate and set defaults for text variables */
$your_cost_title = validate_var($g_opt['lec_your_cost_title'], 'Your Cost');
$our_cost_title = validate_var($g_opt['lec_our_cost_title'], 'Our Cost');
$housing_opening = validate_var($h_opt['lec_housing_opening_textarea']);
$housing_closing = validate_var($h_opt['lec_housing_closing_textarea']);
$upkeep_opening = validate_var($u_opt['lec_upkeep_opening_textarea']);
$upkeep_closing = validate_var($u_opt['lec_upkeep_closing_textarea']);
$lifestyle_opening = validate_var($l_opt['lec_lifestyle_opening_textarea']);
$lifestyle_closing = validate_var($l_opt['lec_lifestyle_closing_textarea']);
$form_opening_text = validate_var($f_opt['lec_communication_pref_opening_text']);
$form_checkbox_text = validate_var($f_opt['lec_communication_pref_checkbox_text']);
$form_closing_text = validate_var($f_opt['lec_communication_pref_closing_text']);
$lead_capture_intro = validate_var($f_opt['lec_lead_capture_intro'], '<h2>Almost Done</h2><p>Please fill in the form below to view your report.</p>');
$lead_capture_thank_you = validate_var($f_opt['lec_lead_capture_thank_you'], '<h2>Thank You</h2><p>View your report below.</p>');

/* set up the action for the lead capture form */
$action = 'https://api.hsforms.com/submissions/v3/integration/submit/';
if (
	isset($f_opt['lec_hubspot_portal_id']) &&
	isset($f_opt['lec_hubspot_form_id'])
) {
	$action .= $f_opt['lec_hubspot_portal_id'] . '/';
	$action .= $f_opt['lec_hubspot_form_id'] . '/';
};
?>
<div class="lec-calculator">
	<nav class="nav nav-tabs" role="tablist">
		<!-- NOTE: using spans due to Enfold built-in smooth scrolling issue -->
		<span id="intro-nav" class="nav-item nav-link active" data-toggle="tab" href="#intro-tab" role="tab" aria-controls="nav-home" aria-selected="true">
			<span class="nav-item-number">1</span>
			<span class="nav-item-title"><?php echo __('Introduction');?></span>
		</span>
		<span id="housing-nav" class="nav-item nav-link" data-toggle="tab" href="#housing-tab" role="tab" aria-controls="housing" aria-selected="false">
			<span class="nav-item-number">2</span>
			<span class="nav-item-title"><?php echo __('Housing');?></span>
		</span>
		<span id="upkeep-nav" class="nav-item nav-link" data-toggle="tab" href="#upkeep-tab" role="tab" aria-controls="upkeep" aria-selected="false">
			<span class="nav-item-number">3</span>
			<span class="nav-item-title"><?php echo __('Upkeep');?></span>
		</span>
		<span id="lifestyle-nav" class="nav-item nav-link" data-toggle="tab" href="#lifestyle-tab" role="tab" aria-controls="lifestyle" aria-selected="false">
			<span class="nav-item-number">4</span>
			<span class="nav-item-title"><?php echo __('Lifestyle');?></span>
		</span>
		<span id="report-nav" class="nav-item nav-link" data-toggle="tab" href="#report-tab" role="tab" aria-controls="report" aria-selected="false">
			<span class="nav-item-number">5</span>
			<span class="nav-item-title"><?php echo __('Report');?></span>
		</span>
	</nav>
	<div class="tab-content">
		<div class="tab-pane active" id="intro-tab" role="tabpanel" aria-labelledby="nav-home-tab">
			<div class="tab-content--top">
				<?php echo wpautop($g_opt['lec_intro_textarea']); ?>
			</div>
			<div class="lec-nav-flow">
				<div class="lec-nav-flow--next">
					<a class="tab-nav-item btn" data-nav="housing-nav">
						<?php echo __('Start Now');?>
					</a>
				</div>
			</div>
		</div>
		<div class="tab-pane" id="housing-tab" role="tabpanel" aria-labelledby="nav-profile-tab">
			<?php if ($housing_opening): ?>
				<div class="tab-content--top">
					<?php echo wpautop($housing_opening); ?>
				</div>
			<?php endif; ?>
			<div class="tab-content--button-bar">
				<a href="#" class="show-average-control btn">
					<?php echo __('Show Area Average');?>
				</a>
			</div>
			<div class="tab-content--main">
				<div class="item">
					<div class="item--title">
						<?php echo $h_opt['lec_mortgage_rent_title']; ?>
					</div>
					<div class="item--input">
						<input type="text" name="lec-mortgage-rent" class="form-control currency-input" id="lec-mortgage-rent" placeholder="Enter <?php echo $h_opt['lec_mortgage_rent_title']; ?>" />
					</div>
					<div class="item--average">
						<a href="#" class="average-copy-control" data-value="<?php echo format_money($h_opt['lec_mortgage_rent_average'], 0);?>">
							<?php echo format_money($h_opt['lec_mortgage_rent_average']); ?>
						</a>
					</div>
				</div>
				<div class="item">
					<div class="item--title">
						<?php echo $h_opt['lec_home_insurance_title']; ?>
					</div>
					<div class="item--input">
						<input type="text" name="lec-home-insurance" class="form-control currency-input" id="lec-home-insurance" placeholder="Enter <?php echo $h_opt['lec_home_insurance_title']; ?>" />
					</div>
					<div class="item--average">
						<a href="#" class="average-copy-control" data-value="<?php echo format_money($h_opt['lec_home_insurance_average'], 0);?>">
							<?php echo format_money($h_opt['lec_home_insurance_average']); ?>
						</a>
					</div>
				</div>
				<div class="item">
					<div class="item--title">
						<?php echo $h_opt['lec_hoa_title']; ?>
					</div>
					<div class="item--input">
						<input type="text" name="lec-hoa" class="form-control currency-input" id="lec-hoa" placeholder="Enter <?php echo $h_opt['lec_hoa_title']; ?>"/>
					</div>
					<div class="item--average">
						<a href="#" class="average-copy-control" data-value="<?php echo format_money($h_opt['lec_hoa_average'], 0);?>">
							<?php echo format_money($h_opt['lec_hoa_average']); ?>
						</a>
					</div>
				</div>
				<div class="item">
					<div class="item--title">
						<?php echo $h_opt['lec_tax_title']; ?>
					</div>
					<div class="item--input">
						<input type="text" name="lec-property-tax" class="form-control currency-input" id="lec-property-tax" placeholder="Enter <?php echo $h_opt['lec_tax_title']; ?>"/>
					</div>
					<div class="item--average">
						<a href="#" class="average-copy-control" data-value="<?php echo format_money($h_opt['lec_tax_average'], 0);?>">
							<?php echo format_money($h_opt['lec_tax_average']); ?>
						</a>
					</div>
				</div>
				<div class="item">
					<div class="item--title">
						<?php echo $h_opt['lec_security_title']; ?>
					</div>
					<div class="item--input">
						<input type="text" name="lec-home-security" class="form-control currency-input" id="lec-home-security" placeholder="Enter <?php echo $h_opt['lec_security_title']; ?>"/>
					</div>
					<div class="item--average">
						<a href="#" class="average-copy-control" data-value="<?php echo format_money($h_opt['lec_security_average'], 0);?>">
							<?php echo format_money($h_opt['lec_security_average']); ?>
						</a>
					</div>
				</div>
				<div class="item">
					<div class="item--title">
						<?php echo $h_opt['lec_emergency_title']; ?>
					</div>
					<div class="item--input">
						<input type="text" name="lec-emergency-response" class="form-control currency-input" id="lec-emergency-response" placeholder="Enter <?php echo $h_opt['lec_emergency_title']; ?>" />
					</div>
					<div class="item--average">
						<a href="#" class="average-copy-control" data-value="<?php echo format_money($h_opt['lec_emergency_average'], 0);?>">
							<?php echo format_money($h_opt['lec_emergency_average']); ?>
						</a>
					</div>
				</div>
				<div class="item">
					<div class="item--title">
						<?php echo __('Total'); ?>
					</div>
					<div id="lec-housing-total" class="item--section-total">0.00</div>
					<div class="item--average">&nbsp;</div>
				</div>
			</div>
			<?php if ($housing_closing): ?>
				<div class="tab-content--bottom">
					<?php echo wpautop($housing_closing); ?>
				</div>
			<?php endif; ?>
			<div class="lec-nav-flow">
				<div class="lec-nav-flow--next">
					<a class="tab-nav-item btn" data-nav="upkeep-nav">
						<?php echo __('Next');?>
					</a>
				</div>
			</div>
		</div>
		<div class="tab-pane" id="upkeep-tab" role="tabpanel" aria-labelledby="nav-contact-tab">
			<?php if ($upkeep_opening): ?>
				<div class="tab-content--top">
					<?php echo wpautop($upkeep_opening); ?>
				</div>
			<?php endif; ?>
			<div class="tab-content--button-bar">
				<a href="#" class="show-average-control btn">
					<?php echo __('Show Area Average');?>
				</a>
			</div>
			<div class="tab-content--main">
				<div class="item">
					<div class="item--title">
						<?php echo $u_opt['lec_home_maintenance_title']; ?>
					</div>
					<div class="item--input">
						<input type="text" name="lec-home-maintenance" class="form-control currency-input" id="lec-home-maintenance" placeholder="Enter <?php echo $u_opt['lec_home_maintenance_title']; ?>"/>
					</div>
					<div class="item--average">
						<a href="#" class="average-copy-control" data-value="<?php echo format_money($u_opt['lec_home_maintenance_average'], 0);?>">
							<?php echo format_money($u_opt['lec_home_maintenance_average']); ?>
						</a>
					</div>
				</div>
				<div class="item">
					<div class="item--title">
						<?php echo $u_opt['lec_appliances_title']; ?>
					</div>
					<div class="item--input">
						<input type="text" name="lec-appliances" class="form-control currency-input" id="lec-appliances" placeholder="Enter <?php echo $u_opt['lec_appliances_title']; ?>"/>
					</div>
					<div class="item--average">
						<a href="#" class="average-copy-control" data-value="<?php echo format_money($u_opt['lec_appliances_average'], 0);?>">
							<?php echo format_money($u_opt['lec_appliances_average']); ?>
						</a>
					</div>
				</div>
				<div class="item">
					<div class="item--title">
						<?php echo $u_opt['lec_housekeeping_title']; ?>
					</div>
					<div class="item--input">
						<input type="text" name="lec-housekeeping" class="form-control currency-input" id="lec-housekeeping" placeholder="Enter <?php echo $u_opt['lec_housekeeping_title']; ?>"/>
					</div>
					<div class="item--average">
						<a href="#" class="average-copy-control" data-value="<?php echo format_money($u_opt['lec_housekeeping_average'], 0);?>">
							<?php echo format_money($u_opt['lec_housekeeping_average']); ?>
						</a>
					</div>
				</div>
				<div class="item">
					<div class="item--title">
						<?php echo $u_opt['lec_water_sewage_title']; ?>
					</div>
					<div class="item--input">
						<input type="text" name="lec-water-sewage" class="form-control currency-input" id="lec-water-sewage" placeholder="Enter <?php echo $u_opt['lec_water_sewage_title']; ?>"/>
					</div>
					<div class="item--average">
						<a href="#" class="average-copy-control" data-value="<?php echo format_money($u_opt['lec_water_sewage_average'], 0);?>">
							<?php echo format_money($u_opt['lec_water_sewage_average']); ?>
						</a>
					</div>
				</div>
				<div class="item">
					<div class="item--title">
						<?php echo $u_opt['lec_gas_electric_title']; ?>
					</div>
					<div class="item--input">
						<input type="text" name="lec-gas-electric" class="form-control currency-input" id="lec-gas-electric" placeholder="Enter <?php echo $u_opt['lec_gas_electric_title']; ?>"/>
					</div>
					<div class="item--average">
						<a href="#" class="average-copy-control" data-value="<?php echo format_money($u_opt['lec_gas_electric_average'], 0);?>">
							<?php echo format_money($u_opt['lec_gas_electric_average']); ?>
						</a>
					</div>
				</div>
				<div class="item">
					<div class="item--title">
						<?php echo $u_opt['lec_groundskeeping_title']; ?>
					</div>
					<div class="item--input">
						<input type="text" name="lec-groundskeeping" class="form-control currency-input" id="lec-groundskeeping" placeholder="Enter <?php echo $u_opt['lec_groundskeeping_title']; ?>"/>
					</div>
					<div class="item--average">
						<a href="#" class="average-copy-control" data-value="<?php echo format_money($u_opt['lec_groundskeeping_average'], 0);?>">
							<?php echo format_money($u_opt['lec_groundskeeping_average']); ?>
						</a>
					</div>
				</div>
				<div class="item">
					<div class="item--title">
						<?php echo __('Total'); ?>
					</div>
					<div id="lec-upkeep-total" class="item--section-total">0.00</div>
					<div class="item--average"></div>
				</div>
			</div>
			<?php if ($upkeep_closing): ?>
				<div class="tab-content--bottom">
					<?php echo wpautop($upkeep_closing); ?>
				</div>
			<?php endif; ?>
			<div class="lec-nav-flow">
				<div class="lec-nav-flow--next">
					<a class="tab-nav-item btn" data-nav="lifestyle-nav">
						<?php echo __('Next');?>
					</a>
				</div>
			</div>
		</div>
		<div class="tab-pane" id="lifestyle-tab" role="tabpanel" aria-labelledby="nav-contact-tab">
			<?php if ($lifestyle_opening): ?>
				<div class="tab-content--top">
					<?php echo wpautop($lifestyle_opening); ?>
				</div>
			<?php endif; ?>
			<div class="tab-content--button-bar">
				<a href="#" class="show-average-control btn">
					<?php echo __('Show Area Average');?>
				</a>
			</div>
			<div class="tab-content--main">
				<div class="item">
					<div class="item--title">
						<?php echo $l_opt['lec_transportation_title']; ?>
					</div>
					<div class="item--input">
						<input type="text" name="lec-transportation" class="form-control currency-input" id="lec-transportation" placeholder="Enter <?php echo $l_opt['lec_transportation_title']; ?>"/>
					</div>
					<div class="item--average">
						<a href="#" class="average-copy-control" data-value="<?php echo format_money($l_opt['lec_transportation_average'], 0);?>">
							<?php echo format_money($l_opt['lec_transportation_average']); ?>
						</a>
					</div>
				</div>
				<div class="item">
					<div class="item--title">
						<?php echo $l_opt['lec_health_club_title']; ?>
					</div>
					<div class="item--input">
						<input type="text" name="lec-health-club" class="form-control currency-input" id="lec-health-club" placeholder="Enter <?php echo $l_opt['lec_health_club_title']; ?>"/>
					</div>
					<div class="item--average">
						<a href="#" class="average-copy-control" data-value="<?php echo format_money($l_opt['lec_health_club_average'], 0);?>">
							<?php echo format_money($l_opt['lec_health_club_average']); ?>
						</a>
					</div>
				</div>
				<div class="item">
					<div class="item--title">
						<?php echo $l_opt['lec_activities_title']; ?>
					</div>
					<div class="item--input">
						<input type="text" name="lec-activities" class="form-control currency-input" id="lec-activities" placeholder="Enter <?php echo $l_opt['lec_activities_title']; ?>" />
					</div>
					<div class="item--average">
						<a href="#" class="average-copy-control" data-value="<?php echo format_money($l_opt['lec_activities_average'], 0);?>">
							<?php echo format_money($l_opt['lec_activities_average']); ?>
						</a>
					</div>
				</div>
				<div class="item">
					<div class="item--title">
						<?php echo $l_opt['lec_restaurants_title']; ?>
					</div>
					<div class="item--input">
						<input type="text" name="lec-restaurants" class="form-control currency-input" id="lec-restaurants" placeholder="Enter <?php echo $l_opt['lec_restaurants_title']; ?>" />
					</div>
					<div class="item--average">
						<a href="#" class="average-copy-control" data-value="<?php echo format_money($l_opt['lec_restaurants_average'], 0);?>">
							<?php echo format_money($l_opt['lec_restaurants_average']); ?>
						</a>
					</div>
				</div>
				<div class="item">
					<div class="item--title">
						<?php echo $l_opt['lec_groceries_title']; ?>
					</div>
					<div class="item--input">
						<input type="text" name="lec-groceries" class="form-control currency-input" id="lec-groceries" placeholder="Enter <?php echo $l_opt['lec_groceries_title']; ?>"/>
					</div>
					<div class="item--average">
						<a href="#" class="average-copy-control" data-value="<?php echo format_money($l_opt['lec_transportation_average'], 0);?>">
							<?php echo format_money($l_opt['lec_transportation_average']); ?>
						</a>
					</div>
				</div>
				<div class="item">
					<div class="item--title">
						<?php echo __('Total'); ?>
					</div>
					<div id="lec-lifestyle-total" class="item--section-total">0.00</div>
					<div class="item--average"></div>
				</div>
			</div>
			<?php if($lifestyle_closing): ?>
				<div class="tab-content--bottom">
					<?php echo wpautop($lifestyle_closing); ?>
				</div>
			<?php endif; ?>
			<div class="lec-nav-flow">
				<div class="lec-nav-flow--next">
					<a class="tab-nav-item btn" data-nav="report-nav">
						<?php echo __('Next');?>
					</a>
				</div>
			</div>
		</div>
		<div class="tab-pane" id="report-tab" role="tabpanel" aria-labelledby="nav-contact-tab">
			<div class="lec-lead-capture">
				<div class="tab-content--top">
					<?php if ($lead_capture_intro): ?>
						<?php echo wpautop($lead_capture_intro); ?>
					<?php endif; ?>
				</div>
				<div class="tab-content--main">
					<form class="lec-lead-capture-form" method="post" action="<?php echo $action; ?>" enctype="multipart/form-data">
						<fieldset>
							<div class="form-group field--firstname">
								<label for="firstname"><?php echo __('First Name');?></label>
								<input name="firstname" type="text" class="form-control" placeholder="First Name" required="required" />
							</div>
							<div class="form-group field--lastname">
								<label for="lastname"><?php echo __('Last Name');?></label>
								<input name="lastname" type="text" class="form-control" placeholder="Last Name" required="required" />
							</div>
							<div class="form-group field--email">
								<label for="email"><?php echo __('Email');?></label>
								<input name="email" type="email" class="form-control" placeholder="Email" required="required" />
							</div>
							<?php if ($form_opening_text): ?>
								<div class="form-group field--text">
									<?php echo wpautop($form_opening_text); ?>
								</div>
							<?php endif; ?>
							<?php if ($form_checkbox_text): ?>
								<div class="form-group field--checkbox">
									<label for="checkbox">
										<input type="checkbox" name="checkbox" value="<?php echo $form_checkbox_text; ?>"><?php echo $form_checkbox_text; ?>
									</label>
								</div>
							<?php endif; ?>
							<?php if ($form_closing_text): ?>
								<div class="form-group field--text">
									<?php echo wpautop($form_closing_text); ?>
								</div>
							<?php endif; ?>
							<button type="submit" class="btn"><?php echo __('Submit');?></button>
						</fieldset>
					</form>
				</div>
			</div>
			<div class="lec-report">
				<div class="tab-content--top">
					<?php if ($lead_capture_thank_you): ?>
						<?php echo wpautop($lead_capture_thank_you); ?>
					<?php endif; ?>
					<div class="print-button">
						<a class="btn" onClick="window.print()">
							<?php echo __('Print this page');?>
						</a>
					</div>
				</div>
				<div class="tab-content--main">
					<table class="table">
						<thead>
							<th class="report--expense-name"><?php echo __('Montly Housing Expense');?></th>
							<th class="report--your-cost"><?php echo $your_cost_title; ?></th>
							<th class="report--our-cost"><?php echo $our_cost_title; ?></th>
						</thead>
						<tr>
							<td>
								<?php echo $h_opt['lec_mortgage_rent_title']; ?>
							</td>
							<td>
								<span class="report--section-item" data-source="lec-mortgage-rent">0.00</span>
							</td>
							<td>
								<span class="cost-housing" data-value="<?php echo format_float_value($h_opt['lec_mortgage_rent_our_cost'], 0);?>">
									<?php echo our_cost_display($h_opt['lec_mortgage_rent_our_cost']);?>
								</span>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo $h_opt['lec_home_insurance_title']; ?>
							</td>
							<td>
								<span class="report--section-item" data-source="lec-home-insurance">0.00</span>
							</td>
							<td>
								<span class="cost-housing" data-value="<?php echo format_float_value($h_opt['lec_home_insurance_our_cost'], 0);?>">
									<?php echo our_cost_display($h_opt['lec_home_insurance_our_cost']); ?>
								</span>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo $h_opt['lec_hoa_title']; ?>
							</td>
							<td>
								<span class="report--section-item" data-source="lec-hoa">0.00</span>
							</td>
							<td>
								<span class="cost-housing" data-value="<?php echo format_float_value($h_opt['lec_hoa_our_cost'], 0);?>">
									<?php echo our_cost_display($h_opt['lec_hoa_our_cost']);?>
								</span>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo $h_opt['lec_tax_title']; ?>
							</td>
							<td>
								<span class="report--section-item" data-source="lec-property-tax">0.00</span>
							</td>
							<td>
								<span class="cost-housing" data-value="<?php echo format_float_value($h_opt['lec_tax_our_cost'], 0);?>">
									<?php echo our_cost_display($h_opt['lec_tax_our_cost']); ?>
								</span>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo $h_opt['lec_security_title']; ?>
							</td>
							<td>
								<span class="report--section-item" data-source="lec-home-security">0.00</span>
							</td>
							<td>
								<span class="cost-housing" data-value="<?php echo format_float_value($h_opt['lec_security_our_cost'], 0);?>">
									<?php echo our_cost_display($h_opt['lec_security_our_cost']);?>
								</span>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo $h_opt['lec_emergency_title']; ?>
							</td>
							<td>
								<span class="report--section-item" data-source="lec-emergency-response">0.00</span>
							</td>
							<td>
								<span class="cost-housing" data-value="<?php echo format_float_value($h_opt['lec_emergency_our_cost'], 0);?>"><?php echo our_cost_display($h_opt['lec_emergency_our_cost']);?></span>
							</td>
						</tr>
						<tr>
							<td><?php echo __('Total Housing Expense');?></td>
							<td>
								<span class="report--section-your-total" data-source="lec-housing-total">0.00</span>
							</td>
							<td>
								<span class="report--section-our-total">
									<?php echo our_cost_display($housing_total_cost); ?>
								</span>
							</td>
						</tr>
					</table>
					<table>
						<thead>
							<th class="report--expense-name"><?php echo __('Monthly Home Upkeep & Maintenance Expense');?></th>
							<th class="report--your-cost"><?php echo $your_cost_title; ?></th>
							<th class="report--our-cost"><?php echo $our_cost_title; ?></th>
						</thead>
						<tr>
							<td>
								<?php echo $u_opt['lec_home_maintenance_title']; ?>
							</td>
							<td>
								<span class="report--section-item" data-source="lec-home-maintenance">0.00</span>
							</td>
							<td>
								<span class="cost-upkeep" data-value="<?php echo format_float_value($u_opt['lec_home_maintenance_our_cost'], 0);?>"><?php echo our_cost_display($u_opt['lec_home_maintenance_our_cost']);?></span>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo $u_opt['lec_appliances_title']; ?>
							</td>
							<td>
								<span class="report--section-item" data-source="lec-appliances">0.00</span>
							</td>
							<td>
								<span class="cost-upkeep" data-value="<?php echo format_float_value($u_opt['lec_appliances_our_cost'], 0);?>"><?php echo our_cost_display($u_opt['lec_appliances_our_cost']);?></span>
							</td>
						</tr>
						<tr>
							<td><?php echo $u_opt['lec_housekeeping_title']; ?></td>
							<td>
								<span class="report--section-item" data-source="lec-housekeeping">0.00</span>
							</td>
							<td>
								<span class="cost-upkeep" data-value="<?php echo format_float_value($u_opt['lec_housekeeping_our_cost'], 0);?>"><?php echo our_cost_display($u_opt['lec_housekeeping_our_cost']);?></span>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo $u_opt['lec_water_sewage_title']; ?>
							</td>
							<td>
								<span class="report--section-item" data-source="lec-water-sewage">0.00</span>
							</td>
							<td>
								<span class="cost-upkeep" data-value="<?php echo format_float_value($u_opt['lec_water_sewage_our_cost'], 0);?>">
									<?php echo our_cost_display($u_opt['lec_water_sewage_our_cost']);?>
								</span>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo $u_opt['lec_gas_electric_title']; ?>
							</td>
							<td>
								<span class="report--section-item" data-source="lec-gas-electric">0.00</span>
							</td>
							<td>
								<span class="cost-upkeep" data-value="<?php echo format_float_value($u_opt['lec_gas_electric_our_cost'], 0);?>"><?php echo our_cost_display($u_opt['lec_gas_electric_our_cost']);?></span>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo $u_opt['lec_groundskeeping_title']; ?>
							</td>
							<td>
								<span class="report--section-item" data-source="lec-groundskeeping">0.00</span>
							</td>
							<td>
								<span class="cost-upkeep" data-value="<?php echo format_float_value($u_opt['lec_groundskeeping_our_cost'], 0);?>"><?php echo our_cost_display($u_opt['lec_groundskeeping_our_cost']);?></span>
							</td>
						</tr>
						<tr>
							<td><?php echo __('Total Upkeep');?></td>
							<td>
								<span class="report--section-your-total" data-source="lec-upkeep-total">0.00</span>
							</td>
							<td>
								<span class="report--section-our-total">
									<?php echo our_cost_display($upkeep_total_cost); ?>
								</span>
							</td>
						</tr>
					</table>
					<table>
						<thead>
							<th class="report--expense-name"><?php echo __('Monthly Lifestyle Expense');?></th>
							<th class="report--your-cost"><?php echo $your_cost_title; ?></th>
							<th class="report--our-cost"><?php echo $our_cost_title; ?></th>
						</thead>
						<tr>
							<td>
								<?php echo $l_opt['lec_transportation_title']; ?>
							</td>
							<td>
								<span class="report--section-item" data-source="lec-transportation">0.00</span>
							</td>
							<td>
								<span class="cost-lifestyle report--section-item" data-value="<?php echo format_float_value($l_opt['lec_transportation_our_cost'], 0);?>"><?php echo our_cost_display($l_opt['lec_transportation_our_cost']);?></span>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo $l_opt['lec_health_club_title']; ?>
							</td>
							<td>
								<span class="report--section-item" data-source="lec-health-club">0.00</span>
							</td>
							<td>
								<span class="cost-lifestyle report--section-item" data-value="<?php echo format_float_value($l_opt['lec_health_club_our_cost'], 0);?>"><?php echo our_cost_display($l_opt['lec_health_club_our_cost']);?></span>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo $l_opt['lec_activities_title']; ?>
							</td>
							<td>
								<span class="report--section-item" data-source="lec-activities">0.00</span>
							</td>
							<td>
								<span class="cost-lifestyle report--section-item" data-value="<?php echo format_float_value($l_opt['lec_activities_our_cost'], 0);?>"><?php echo our_cost_display($l_opt['lec_activities_our_cost']);?></span>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo $l_opt['lec_restaurants_title']; ?>
							</td>
							<td>
								<span class="report--section-item" data-source="lec-restaurants">0.00</span>
							</td>
							<td>
								<span class="cost-lifestyle report--section-item" data-value="<?php echo format_float_value($l_opt['lec_restaurants_our_cost'], 0);?>">
									<?php echo our_cost_display($l_opt['lec_restaurants_our_cost']);?>
								</span>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo $l_opt['lec_groceries_title']; ?>
							</td>
							<td>
								<span class="report--section-item" data-source="lec-groceries">0.00</span>
							</td>
							<td>
								<span class="cost-lifestyle report--section-item" data-value="<?php echo format_float_value($l_opt['lec_groceries_our_cost'], 0);?>">
									<?php echo our_cost_display($l_opt['lec_groceries_our_cost']);?>
								</span>
							</td>
						</tr>
						<tr>
							<td><?php echo __('Total Lifestyle');?></td>
							<td>
								<span class="report--section-your-total" data-source="lec-lifestyle-total">0.00</span>
							</td>
							<td>
								<span class="report--section-our-total">
									<?php echo our_cost_display($lifestyle_total_cost);?>
								</span>
							</td>
						</tr>
					</table>
					<table>
						<tr>
							<td class="report--expense-name"><?php echo __('TOTAL');?></td>
							<td class="report--your-cost">
								<span class="report--your-grand-total">0.00</span>
							</td>
							<td class="report--our-cost">
								<span class="report--our-grand-total">
									<?php echo our_cost_display($cost_grand_total); ?>
								</span>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
