<?php

/**
 * Generated by the WordPress Option Page generator
 * at http://jeremyhixon.com/wp-tools/option-page/
 */

class Disguises {
	private $disguises_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'disguises_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'disguises_page_init' ) );
	}

	public function disguises_add_plugin_page() {
		add_menu_page(
			'Disguises', // page_title
			'Disguises', // menu_title
			'manage_options', // capability
			'disguises', // menu_slug
			array( $this, 'disguises_create_admin_page' ), // function
			'dashicons-businessman', // icon_url
			80 // position
		);
	}

	public function disguises_create_admin_page() {
		$this->disguises_options = get_option( 'disguises_option_name' ); ?>

		<div class="wrap">
			<h2>Disguises</h2>
			<p></p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
				settings_fields( 'disguises_option_group' );
				do_settings_sections( 'disguises-admin' );
				submit_button();
				?>
			</form>
		</div>
	<?php }

	public function disguises_page_init() {
		register_setting(
			'disguises_option_group', // option_group
			'disguises_option_name', // option_name
			array( $this, 'disguises_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'disguises_setting_section', // id
			'Settings', // title
			array( $this, 'disguises_section_info' ), // callback
			'disguises-admin' // page
		);

		add_settings_field(
			'production_domain_0', // id
			'Production domain', // title
			array( $this, 'production_domain_0_callback' ), // callback
			'disguises-admin', // page
			'disguises_setting_section' // section
		);

		add_settings_field(
			'urls_to_match', // id
			'Urls to match (one per line)', // title
			array( $this, 'urls_to_match_callback' ), // callback
			'disguises-admin', // page
			'disguises_setting_section' // section
		);
	}

	public function disguises_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['production_domain_0'] ) ) {
			$sanitary_values['production_domain_0'] = wp_http_validate_url(sanitize_text_field( $input['production_domain_0'] ));
		}

		// @TODO better would be to parse URLs now and validate them.
		if ( isset( $input['urls_to_match'] ) ) {
			$sanitary_values['urls_to_match'] = esc_textarea( sanitize_textarea_field( $input['urls_to_match'] ) );
		}

		return $sanitary_values;
	}

	public function disguises_section_info() {

	}

	public function production_domain_0_callback() {
		printf(
			'<input class="regular-text" type="text" name="disguises_option_name[production_domain_0]" id="production_domain_0" value="%s">',
			isset( $this->disguises_options['production_domain_0'] ) ? esc_attr( $this->disguises_options['production_domain_0']) : ''
		);
	}

	public function urls_to_match_callback() {
		printf(
			'<textarea class="large-text" rows="5" name="disguises_option_name[urls_to_match]" id="urls_to_match">%s</textarea>',
			isset( $this->disguises_options['urls_to_match'] ) ? esc_textarea( $this->disguises_options['urls_to_match']) : ''
		);
	}

}
//if ( is_admin() )
	$disguises = new Disguises();

/*
 * Retrieve this value with:
 * $disguises_options = get_option( 'disguises_option_name' ); // Array of All Options
 * $production_domain_0 = $disguises_options['production_domain_0']; // Production domain
 * $urls_to_match = $disguises_options['urls_to_match']; // Urls to match (one per line)
 */
