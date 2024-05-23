<?php
/**
 * Plugin Name: Living Expenses Calculator
 * Description: Adds a shortcode to create a living expenses comparison calculator.
 * Version: 1.0
 */

add_action( 'admin_menu', 'lec_add_admin_menu' );
add_action( 'admin_init', 'lec_settings_init' );

/* register plugin styles */
function lec_register_styles()
{
	$dir_path = plugin_dir_url(__FILE__);
	$plugin_css_path = wp_normalize_path($dir_path.'css/lec-styles.css');

	wp_register_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css');

	wp_register_style('lec-plugin-css', $plugin_css_path, array(), null, false);
}
add_action('wp_enqueue_scripts', 'lec_register_styles');

/* register plugin scripts */
function lec_register_scripts()
{
	wp_register_script('currency', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js', array(), null, false);

	wp_register_script('bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', array(), null, false);

	$dir_path = plugin_dir_url(__FILE__);
	$plugin_js_path = wp_normalize_path($dir_path.'js/lec-scripts.js');

	wp_register_script('lec-plugin-js', $plugin_js_path, array(), null, false );
}
add_action('wp_enqueue_scripts', 'lec_register_scripts');

/* adding a custom plugin menu */
function lec_add_admin_menu()
{
	add_menu_page( 'Living Expense Calculator', 'Living Expense Calculator', 'manage_options', 'living_expense_calculator', 'lec_options_page' );
}

/* settings for the plugin using the WP Settings API */
function lec_settings_init()
{
	/**
	 * general settings tab / section
	 */
	register_setting( 'lec_general', 'lec_general_settings' );
	add_settings_section(
		'lec_general_settings',
		__( 'General Settings', 'General Settings' ),
		null,
		'lec_general'
	);

	/* add fields */
	add_settings_field(
		'lec_intro_textarea',
		__( 'Intro Textarea', 'General Settings' ),
		'lec_textarea_render',
		'lec_general',
		'lec_general_settings',
		array(
			'key' => 'lec_intro_textarea',
			'group' => 'lec_general_settings'
		)
	);

	add_settings_field(
		'lec_our_cost_title',
		__( 'Our Cost Title', 'General Settings' ),
		'lec_text_field_render',
		'lec_general',
		'lec_general_settings',
		array(
			'helper' => '"Our" cost title displayed on the report.  Leave blank to use "Our Cost"',
			'key' => 'lec_our_cost_title',
			'group' => 'lec_general_settings'
		)
	);

	add_settings_field(
		'lec_your_cost_title',
		__( 'Your Cost Title', 'General Settings' ),
		'lec_text_field_render',
		'lec_general',
		'lec_general_settings',
		array(
			'helper' => 'Your cost title displayed on the report.  Leave blank to use "Your Cost"',
			'key' => 'lec_your_cost_title',
			'group' => 'lec_general_settings'
		)
	);


	/**
	 * housing settings tab / section
	 */
	register_setting( 'lec_housing', 'lec_housing_settings' );
	add_settings_section(
		'lec_housing_settings',
		__( 'Housing Settings', 'Housing Settings' ),
		null,
		'lec_housing'
	);

	/* add fields */
	add_settings_field(
		'lec_housing_opening_textarea',
		__( 'Housing Opening Textarea', 'Housing Settings' ),
		'lec_textarea_render',
		'lec_housing',
		'lec_housing_settings',
		array(
			'key' => 'lec_housing_opening_textarea',
			'group' => 'lec_housing_settings'
		)
	);

	add_settings_field(
		'lec_mortgage_rent_title',
		__( 'Mortgage/Rent Title', 'Housing Settings' ),
		'lec_text_field_render',
		'lec_housing',
		'lec_housing_settings',
		array(
			'key' => 'lec_mortgage_rent_title',
			'group' => 'lec_housing_settings'
		)
	);

	add_settings_field(
		'lec_mortgage_rent_average',
		__( 'Mortgage/Rent Average', 'Housing Settings' ),
		'lec_text_field_render',
		'lec_housing',
		'lec_housing_settings',
		array(
			'key' => 'lec_mortgage_rent_average',
			'group' => 'lec_housing_settings'
		)
	);

	add_settings_field(
		'lec_mortgage_rent_our_cost',
		__( 'Mortgage/Rent Our Cost', 'Housing Settings' ),
		'lec_text_field_render',
		'lec_housing',
		'lec_housing_settings',
		array(
			'helper' => 'Enter 0 to display as "Included"',
			'key' => 'lec_mortgage_rent_our_cost',
			'group' => 'lec_housing_settings'
		)
	);

	add_settings_field(
		'lec_home_insurance_title',
		__( 'Homeowner Insurance Title', 'Housing Settings' ),
		'lec_text_field_render',
		'lec_housing',
		'lec_housing_settings',
		array(
			'key' => 'lec_home_insurance_title',
			'group' => 'lec_housing_settings'
		)
	);

	add_settings_field(
		'lec_home_insurance_average',
		__( 'Homeowner Insurance Average', 'Housing Settings' ),
		'lec_text_field_render',
		'lec_housing',
		'lec_housing_settings',
		array(
			'key' => 'lec_home_insurance_average',
			'group' => 'lec_housing_settings'
		)
	);

	add_settings_field(
		'lec_home_insurance_our_cost',
		__( 'Homeowner Insurance Our Cost', 'Housing Settings' ),
		'lec_text_field_render',
		'lec_housing',
		'lec_housing_settings',
		array(
			'helper' => 'Enter 0 to display as "Included"',
			'key' => 'lec_home_insurance_our_cost',
			'group' => 'lec_housing_settings'
		)
	);

	add_settings_field(
		'lec_hoa_title',
		__( 'HOA Title', 'Housing Settings' ),
		'lec_text_field_render',
		'lec_housing',
		'lec_housing_settings',
		array(
			'key' => 'lec_hoa_title',
			'group' => 'lec_housing_settings'
		)
	);

	add_settings_field(
		'lec_hoa_average',
		__( 'HOA Average', 'Housing Settings' ),
		'lec_text_field_render',
		'lec_housing',
		'lec_housing_settings',
		array(
			'key' => 'lec_hoa_average',
			'group' => 'lec_housing_settings'
		)
	);

	add_settings_field(
		'lec_hoa_our_cost',
		__( 'HOA Our Cost', 'Housing Settings' ),
		'lec_text_field_render',
		'lec_housing',
		'lec_housing_settings',
		array(
			'helper' => 'Enter 0 to display as "Included"',
			'key' => 'lec_hoa_our_cost',
			'group' => 'lec_housing_settings'
		)
	);

	add_settings_field(
		'lec_tax_title',
		__( 'Property Tax Title', 'Housing Settings' ),
		'lec_text_field_render',
		'lec_housing',
		'lec_housing_settings',
		array(
			'key' => 'lec_tax_title',
			'group' => 'lec_housing_settings'
		)
	);

	add_settings_field(
		'lec_tax_average',
		__( 'Property Tax Average', 'Housing Settings' ),
		'lec_text_field_render',
		'lec_housing',
		'lec_housing_settings',
		array(
			'key' => 'lec_tax_average',
			'group' => 'lec_housing_settings'
		)
	);

	add_settings_field(
		'lec_tax_our_cost',
		__( 'Property Tax Our Cost', 'Housing Settings' ),
		'lec_text_field_render',
		'lec_housing',
		'lec_housing_settings',
		array(
			'helper' => 'Enter 0 to display as "Included"',
			'key' => 'lec_tax_our_cost',
			'group' => 'lec_housing_settings'
		)
	);

	add_settings_field(
		'lec_security_title',
		__( 'Home Security Title', 'Housing Settings' ),
		'lec_text_field_render',
		'lec_housing',
		'lec_housing_settings',
		array(
			'key' => 'lec_security_title',
			'group' => 'lec_housing_settings'
		)
	);

	add_settings_field(
		'lec_security_average',
		__( 'Home Security Average', 'Housing Settings' ),
		'lec_text_field_render',
		'lec_housing',
		'lec_housing_settings',
		array(
			'key' => 'lec_security_average',
			'group' => 'lec_housing_settings'
		)
	);

	add_settings_field(
		'lec_security_our_cost',
		__( 'Home Security Our Cost', 'Housing Settings' ),
		'lec_text_field_render',
		'lec_housing',
		'lec_housing_settings',
		array(
			'helper' => 'Enter 0 to display as "Included"',
			'key' => 'lec_security_our_cost',
			'group' => 'lec_housing_settings'
		)
	);

	add_settings_field(
		'lec_emergency_title',
		__( 'Emergency Response Title', 'Housing Settings' ),
		'lec_text_field_render',
		'lec_housing',
		'lec_housing_settings',
		array(
			'key' => 'lec_emergency_title',
			'group' => 'lec_housing_settings'
		)
	);

	add_settings_field(
		'lec_emergency_average',
		__( 'Emergency Response Average', 'Housing Settings' ),
		'lec_text_field_render',
		'lec_housing',
		'lec_housing_settings',
		array(
			'key' => 'lec_emergency_average',
			'group' => 'lec_housing_settings'
		)
	);

	add_settings_field(
		'lec_emergency_our_cost',
		__( 'Emergency Response Our Cost', 'Housing Settings' ),
		'lec_text_field_render',
		'lec_housing',
		'lec_housing_settings',
		array(
			'helper' => 'Enter 0 to display as "Included"',
			'key' => 'lec_emergency_our_cost',
			'group' => 'lec_housing_settings'
		)
	);

	add_settings_field(
		'lec_housing_closing_textarea',
		__( 'Housing Closing', 'Housing Settings' ),
		'lec_textarea_render',
		'lec_housing',
		'lec_housing_settings',
		array(
			'key' => 'lec_housing_closing_textarea',
			'group' => 'lec_housing_settings'
		)
	);

	/**
	 * upkeep settings tab / section
	 */
	register_setting( 'lec_upkeep', 'lec_upkeep_settings' );
	add_settings_section(
		'lec_upkeep_settings',
		__( 'Upkeep Settings', 'Upkeep Settings' ),
		null,
		'lec_upkeep'
	);

	add_settings_field(
		'lec_upkeep_opening_textarea',
		__( 'Upkeep Opening Textarea', 'Upkeep Settings' ),
		'lec_textarea_render',
		'lec_upkeep',
		'lec_upkeep_settings',
		array(
			'key' => 'lec_upkeep_opening_textarea',
			'group' => 'lec_upkeep_settings'
		)
	);

	add_settings_field(
		'lec_home_maintenance_title',
		__( 'Home Maintenance Title', 'Upkeep Settings' ),
		'lec_text_field_render',
		'lec_upkeep',
		'lec_upkeep_settings',
		array(
			'key' => 'lec_home_maintenance_title',
			'group' => 'lec_upkeep_settings'
		)
	);

	add_settings_field(
		'lec_home_maintenance_average',
		__( 'Home Maintenance Average', 'Upkeep Settings' ),
		'lec_text_field_render',
		'lec_upkeep',
		'lec_upkeep_settings',
		array(
			'key' => 'lec_home_maintenance_average',
			'group' => 'lec_upkeep_settings'
		)
	);

	add_settings_field(
		'lec_home_maintenance_our_cost',
		__( 'Home Maintenance Our Cost', 'Upkeep Settings' ),
		'lec_text_field_render',
		'lec_upkeep',
		'lec_upkeep_settings',
		array(
			'helper' => 'Enter 0 to display as "Included"',
			'key' => 'lec_home_maintenance_our_cost',
			'group' => 'lec_upkeep_settings'
		)
	);

	add_settings_field(
		'lec_appliances_title',
		__( 'Appliances Title', 'Upkeep Settings' ),
		'lec_text_field_render',
		'lec_upkeep',
		'lec_upkeep_settings',
		array(
			'key' => 'lec_appliances_title',
			'group' => 'lec_upkeep_settings'
		)
	);

	add_settings_field(
		'lec_appliances_average',
		__( 'Appliances Average', 'Upkeep Settings' ),
		'lec_text_field_render',
		'lec_upkeep',
		'lec_upkeep_settings',
		array(
			'key' => 'lec_appliances_average',
			'group' => 'lec_upkeep_settings'
		)
	);

	add_settings_field(
		'lec_appliances_our_cost',
		__( 'Appliances Our Cost', 'Upkeep Settings' ),
		'lec_text_field_render',
		'lec_upkeep',
		'lec_upkeep_settings',
		array(
			'helper' => 'Enter 0 to display as "Included"',
			'key' => 'lec_appliances_our_cost',
			'group' => 'lec_upkeep_settings'
		)
	);

	add_settings_field(
		'lec_housekeeping_title',
		__( 'Housekeeping Title', 'Upkeep Settings' ),
		'lec_text_field_render',
		'lec_upkeep',
		'lec_upkeep_settings',
		array(
			'key' => 'lec_housekeeping_title',
			'group' => 'lec_upkeep_settings'
		)
	);

	add_settings_field(
		'lec_housekeeping_average',
		__( 'Housekeeping Average', 'Upkeep Settings' ),
		'lec_text_field_render',
		'lec_upkeep',
		'lec_upkeep_settings',
		array(
			'key' => 'lec_housekeeping_average',
			'group' => 'lec_upkeep_settings'
		)
	);

	add_settings_field(
		'lec_housekeeping_our_cost',
		__( 'Housekeeping Our Cost', 'Upkeep Settings' ),
		'lec_text_field_render',
		'lec_upkeep',
		'lec_upkeep_settings',
		array(
			'helper' => 'Enter 0 to display as "Included"',
			'key' => 'lec_housekeeping_our_cost',
			'group' => 'lec_upkeep_settings'
		)
	);

	add_settings_field(
		'lec_water_sewage_title',
		__( 'Water/Sewage Title', 'Upkeep Settings' ),
		'lec_text_field_render',
		'lec_upkeep',
		'lec_upkeep_settings',
		array(
			'key' => 'lec_water_sewage_title',
			'group' => 'lec_upkeep_settings'
		)
	);

	add_settings_field(
		'lec_water_sewage_average',
		__( 'Water/Sewage Average', 'Upkeep Settings' ),
		'lec_text_field_render',
		'lec_upkeep',
		'lec_upkeep_settings',
		array(
			'key' => 'lec_water_sewage_average',
			'group' => 'lec_upkeep_settings'
		)
	);

	add_settings_field(
		'lec_water_sewage_our_cost',
		__( 'Water/Sewage Our Cost', 'Upkeep Settings' ),
		'lec_text_field_render',
		'lec_upkeep',
		'lec_upkeep_settings',
		array(
			'helper' => 'Enter 0 to display as "Included"',
			'key' => 'lec_water_sewage_our_cost',
			'group' => 'lec_upkeep_settings'
		)
	);

	add_settings_field(
		'lec_gas_electric_title',
		__( 'Gas/Electric Title', 'Upkeep Settings' ),
		'lec_text_field_render',
		'lec_upkeep',
		'lec_upkeep_settings',
		array(
			'key' => 'lec_gas_electric_title',
			'group' => 'lec_upkeep_settings'
		)
	);

	add_settings_field(
		'lec_gas_electric_average',
		__( 'Gas/Electric Average', 'Upkeep Settings' ),
		'lec_text_field_render',
		'lec_upkeep',
		'lec_upkeep_settings',
		array(
			'key' => 'lec_gas_electric_average',
			'group' => 'lec_upkeep_settings'
		)
	);

	add_settings_field(
		'lec_gas_electric_our_cost',
		__( 'Gas/Electric Our Cost', 'Upkeep Settings' ),
		'lec_text_field_render',
		'lec_upkeep',
		'lec_upkeep_settings',
		array(
			'helper' => 'Enter 0 to display as "Included"',
			'key' => 'lec_gas_electric_our_cost',
			'group' => 'lec_upkeep_settings'
		)
	);

	add_settings_field(
		'lec_groundskeeping_title',
		__( 'Groundskeeping Title', 'Upkeep Settings' ),
		'lec_text_field_render',
		'lec_upkeep',
		'lec_upkeep_settings',
		array(
			'key' => 'lec_groundskeeping_title',
			'group' => 'lec_upkeep_settings'
		)
	);

	add_settings_field(
		'lec_groundskeeping_average',
		__( 'Groundskeeping Average', 'Upkeep Settings' ),
		'lec_text_field_render',
		'lec_upkeep',
		'lec_upkeep_settings',
		array(
			'key' => 'lec_groundskeeping_average',
			'group' => 'lec_upkeep_settings'
		)
	);

	add_settings_field(
		'lec_groundskeeping_our_cost',
		__( 'Groundskeeping Our Cost', 'Upkeep Settings' ),
		'lec_text_field_render',
		'lec_upkeep',
		'lec_upkeep_settings',
		array(
			'helper' => 'Enter 0 to display as "Included"',
			'key' => 'lec_groundskeeping_our_cost',
			'group' => 'lec_upkeep_settings'
		)
	);

	add_settings_field(
		'lec_upkeep_closing_textarea',
		__( 'Upkeep Closing', 'Upkeep Settings' ),
		'lec_textarea_render',
		'lec_upkeep',
		'lec_upkeep_settings',
		array(
			'key' => 'lec_upkeep_closing_textarea',
			'group' => 'lec_upkeep_settings'
		)
	);

	/**
	 * lifestyle settings tab / section
	 */
	register_setting( 'lec_lifestyle', 'lec_lifestyle_settings' );
	add_settings_section(
		'lec_lifestyle_settings',
		__( 'Lifestyle Settings', 'Lifestyle Settings' ),
		null,
		'lec_lifestyle'
	);

	add_settings_field(
		'lec_lifestyle_opening_textarea',
		__( 'Lifestyle Opening Textarea', 'Lifestyle Settings' ),
		'lec_textarea_render',
		'lec_lifestyle',
		'lec_lifestyle_settings',
		array(
			'key' => 'lec_lifestyle_opening_textarea',
			'group' => 'lec_lifestyle_settings'
		)
	);

	add_settings_field(
		'lec_transportation_title',
		__( 'Transportation Title', 'Lifestyle Settings' ),
		'lec_text_field_render',
		'lec_lifestyle',
		'lec_lifestyle_settings',
		array(
			'key' => 'lec_transportation_title',
			'group' => 'lec_lifestyle_settings'
		)
	);

	add_settings_field(
		'lec_transportation_average',
		__( 'Transportation Average', 'Lifestyle Settings' ),
		'lec_text_field_render',
		'lec_lifestyle',
		'lec_lifestyle_settings',
		array(
			'key' => 'lec_transportation_average',
			'group' => 'lec_lifestyle_settings'
		)
	);

	add_settings_field(
		'lec_transportation_our_cost',
		__( 'Transportation Our Cost', 'Lifestyle Settings' ),
		'lec_text_field_render',
		'lec_lifestyle',
		'lec_lifestyle_settings',
		array(
			'helper' => 'Enter 0 to display as "Included"',
			'key' => 'lec_transportation_our_cost',
			'group' => 'lec_lifestyle_settings'
		)
	);

	add_settings_field(
		'lec_health_club_title',
		__( 'Health Club Title', 'Lifestyle Settings' ),
		'lec_text_field_render',
		'lec_lifestyle',
		'lec_lifestyle_settings',
		array(
			'key' => 'lec_health_club_title',
			'group' => 'lec_lifestyle_settings'
		)
	);

	add_settings_field(
		'lec_health_club_average',
		__( 'Health Club Average', 'Lifestyle Settings' ),
		'lec_text_field_render',
		'lec_lifestyle',
		'lec_lifestyle_settings',
		array(
			'key' => 'lec_health_club_average',
			'group' => 'lec_lifestyle_settings'
		)
	);

	add_settings_field(
		'lec_health_club_our_cost',
		__( 'Health Club Our Cost', 'Lifestyle Settings' ),
		'lec_text_field_render',
		'lec_lifestyle',
		'lec_lifestyle_settings',
		array(
			'helper' => 'Enter 0 to display as "Included"',
			'key' => 'lec_health_club_our_cost',
			'group' => 'lec_lifestyle_settings'
		)
	);

	add_settings_field(
		'lec_activities_title',
		__( 'Activities Title', 'Lifestyle Settings' ),
		'lec_text_field_render',
		'lec_lifestyle',
		'lec_lifestyle_settings',
		array(
			'key' => 'lec_activities_title',
			'group' => 'lec_lifestyle_settings'
		)
	);

	add_settings_field(
		'lec_activities_average',
		__( 'Activities Average', 'Lifestyle Settings' ),
		'lec_text_field_render',
		'lec_lifestyle',
		'lec_lifestyle_settings',
		array(
			'key' => 'lec_activities_average',
			'group' => 'lec_lifestyle_settings'
		)
	);

	add_settings_field(
		'lec_activities_our_cost',
		__( 'Activities Our Cost', 'Lifestyle Settings' ),
		'lec_text_field_render',
		'lec_lifestyle',
		'lec_lifestyle_settings',
		array(
			'helper' => 'Enter 0 to display as "Included"',
			'key' => 'lec_activities_our_cost',
			'group' => 'lec_lifestyle_settings'
		)
	);

	add_settings_field(
		'lec_restaurants_title',
		__( 'Restaurants Title', 'Lifestyle Settings' ),
		'lec_text_field_render',
		'lec_lifestyle',
		'lec_lifestyle_settings',
		array(
			'key' => 'lec_restaurants_title',
			'group' => 'lec_lifestyle_settings'
		)
	);

	add_settings_field(
		'lec_restaurants_average',
		__( 'Restaurants Average', 'Lifestyle Settings' ),
		'lec_text_field_render',
		'lec_lifestyle',
		'lec_lifestyle_settings',
		array(
			'key' => 'lec_restaurants_average',
			'group' => 'lec_lifestyle_settings'
		)
	);

	add_settings_field(
		'lec_restaurants_our_cost',
		__( 'Restaurants Our Cost', 'Lifestyle Settings' ),
		'lec_text_field_render',
		'lec_lifestyle',
		'lec_lifestyle_settings',
		array(
			'helper' => 'Enter 0 to display as "Included"',
			'key' => 'lec_restaurants_our_cost',
			'group' => 'lec_lifestyle_settings'
		)
	);

	add_settings_field(
		'lec_groceries_title',
		__( 'Groceries Title', 'Lifestyle Settings' ),
		'lec_text_field_render',
		'lec_lifestyle',
		'lec_lifestyle_settings',
		array(
			'key' => 'lec_groceries_title',
			'group' => 'lec_lifestyle_settings'
		)
	);

	add_settings_field(
		'lec_groceries_average',
		__( 'Groceries Average', 'Lifestyle Settings' ),
		'lec_text_field_render',
		'lec_lifestyle',
		'lec_lifestyle_settings',
		array(
			'key' => 'lec_groceries_average',
			'group' => 'lec_lifestyle_settings'
		)
	);

	add_settings_field(
		'lec_groceries_our_cost',
		__( 'Groceries Our Cost', 'Lifestyle Settings' ),
		'lec_text_field_render',
		'lec_lifestyle',
		'lec_lifestyle_settings',
		array(
			'helper' => 'Enter 0 to display as "Included"',
			'key' => 'lec_groceries_our_cost',
			'group' => 'lec_lifestyle_settings'
		)
	);

	add_settings_field(
		'lec_lifestyle_closing_textarea',
		__( 'Lifestyle Closing', 'Lifestyle Settings' ),
		'lec_textarea_render',
		'lec_lifestyle',
		'lec_lifestyle_settings',
		array(
			'key' => 'lec_lifestyle_closing_textarea',
			'group' => 'lec_lifestyle_settings'
		)
	);

	/**
	 * form settings (HubSpot)
	 * TODO: add more options for lead capture? - MailChimp, Constant Contact, etc.
	 */
	register_setting( 'lec_form', 'lec_form_settings' );
 	add_settings_section(
 		'lec_form_settings',
 		__( 'Form Settings', 'Form Settings' ),
 		null,
 		'lec_form'
 	);

	add_settings_field(
		'lec_hubspot_portal_id',
		__( 'HubSpot Portal ID', 'Form Settings' ),
		'lec_text_field_render',
		'lec_form',
		'lec_form_settings',
		array(
			'helper' => 'Portal ID for the HubSpot form.  Can be found in the lead capture form embed code.',
			'key' => 'lec_hubspot_portal_id',
			'group' => 'lec_form_settings'
		)
	);

	add_settings_field(
		'lec_hubspot_form_id',
		__( 'HubSpot Form ID', 'Form Settings' ),
		'lec_text_field_render',
		'lec_form',
		'lec_form_settings',
		array(
			'helper' => 'Form ID for the HubSpot form.  Can be found in the lead capture form embed code.',
			'key' => 'lec_hubspot_form_id',
			'group' => 'lec_form_settings'
		)
	);

	add_settings_field(
		'lec_communication_pref_opening_text',
		__( 'Communication preference opening text', 'Form Settings' ),
		'lec_textarea_render',
		'lec_form',
		'lec_form_settings',
		array(
			'helper' => 'OPTIONAL: Text displayed before the communication preference checkbox.',
			'key' => 'lec_communication_pref_opening_text',
			'group' => 'lec_form_settings'
		)
	);

	add_settings_field(
		'lec_communication_pref_checkbox_text',
		__( 'Communication checkbox text', 'Form Settings' ),
		'lec_text_field_render',
		'lec_form',
		'lec_form_settings',
		array(
			'helper' => 'OPTIONAL: Text for communication preference checkbox.  Checkbox will not display if blank.',
			'key' => 'lec_communication_pref_checkbox_text',
			'group' => 'lec_form_settings'
		)
	);

	add_settings_field(
		'lec_communication_pref_closing_text',
		__( 'Communication preference closing text', 'Form Settings' ),
		'lec_textarea_render',
		'lec_form',
		'lec_form_settings',
		array(
			'helper' => 'OPTIONAL: Text displayed after the communication preference checkbox.',
			'key' => 'lec_communication_pref_closing_text',
			'group' => 'lec_form_settings'
		)
	);

	add_settings_field(
		'lec_lead_capture_intro',
		__( 'Lead capture intro content', 'Form Settings' ),
		'lec_textarea_render',
		'lec_form',
		'lec_form_settings',
		array(
			'helper' => 'Introduction content displayed above the lead capture form.',
			'key' => 'lec_lead_capture_intro',
			'group' => 'lec_form_settings'
		)
	);

	add_settings_field(
		'lec_lead_capture_thank_you',
		__( 'Lead capture thank you content', 'Form Settings' ),
		'lec_textarea_render',
		'lec_form',
		'lec_form_settings',
		array(
			'helper' => 'Thank you content displayed after the lead capture form submission and above the report.',
			'key' => 'lec_lead_capture_thank_you',
			'group' => 'lec_form_settings'
		)
	);
}

function lec_textarea_render($args)
{
	if (!isset($args['key']) || !isset($args['group'])) {
		return false;
	}
	$key = $args['key'];
	$group = $args['group'];
	$helper = isset($args['helper']) ? $args['helper'] : '';
	$options = get_option($group);
	$name = $group . '[' . $key . ']';
	$value = isset($options[$key]) ? $options[$key] : '';
	$settings = array(
		'textarea_name' => $name,
		'textarea_rows' => 8
	);
	wp_editor( $value, $key, $settings );
	if ($helper) {
		echo '<p class="description">' . $helper . '</p>';
	}
}

function lec_text_field_render($args)
{
	if (!isset($args['key']) || !isset($args['group'])) {
		return false;
	}
	$key = $args['key'];
	$group = $args['group'];
	$helper = isset($args['helper']) ? $args['helper'] : '';
	$name = $group . '[' . $key . ']';
	$options = get_option( $group );
	$value = isset($options[$key]) ? $options[$key] : '';
	echo '<input type="text" name="'. $name . '" value="' . $value . '">';
	if ($helper) {
		echo '<p class="description">' . $helper . '</p>';
	}
}

function lec_options_page()
{
	include plugin_dir_path( __FILE__ ) . 'templates' . DIRECTORY_SEPARATOR . 'options-page.php';
}


/**
 * calculator output
 */
function expenses_calculator()
{
	/* enqueue styles */
	wp_enqueue_style('lec-plugin-css');

	/* enqueue scripts */
	wp_enqueue_script('lec-plugin-js');

	ob_start();
	include plugin_dir_path( __FILE__ ) . 'templates' . DIRECTORY_SEPARATOR . 'calculator-template.php';
	return ob_get_clean();
}
add_shortcode('expenses_calculator', 'expenses_calculator');

/* format money (float) */
function format_money($money, $sign = true) {
	$output = '';
	if (
		!is_numeric($money) &&
		!is_float($money)
	) {
		$output = '0.00';
	} else {
		if ($sign == true) {
			$output .= '$';
		}
		$output .= number_format($money, 2, '.', ',');
	}
	return $output;
}

/* validate string vars to avoid unexpected index errors */
function validate_var($var, $default = '')
{
	if (
		isset($var) &&
		strlen($var) > 0
	) {
		$output = $var;
	} else {
		$output = strlen($default) > 0 ? $default : false;
	}
	return $output;
}

/* add the cost variables for the Our Cost totals */
function cost_sum($array) {
	$sum = 0;
	if (!empty($array)) {
		foreach ($array as $val) {
			if (is_numeric($val)) {
				$sum += $val;
			}
		}
	}
	return $sum;
}

/* format float for calculations */
function format_float_value($num) {
	$output = '';
	if (
		!is_numeric($num) &&
		!is_float($num)
	) {
		$output = '0.00';
	} else {
		$output .= number_format($num, 2, '.', '');
	}
	return $output;
}

/* our cost value to be passed to data-value attribute for calculation  */
function our_cost_display($cost) {
	$output = 'Included';
	if ($cost > 0) {
		$output = format_money($cost, 1);
	}
	return $output;
}
