<?php
/**
 * Create the rt-person post type, career taxonomy and rt-person meta boxes to save basic and social meta data about person
 *
 * @package movie-library
 */

namespace Includes\Class_Settings_page;

/**
 * Create the settings page option for our movie library plugin.
 */
class Settings_Page {
	/**
	 * Initialize the class and connect the call back functions to their respective hooks
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'admin_init', [ $this, 'settings_page_options' ] );
		add_action( 'admin_menu', [ $this, 'show_settings' ] );
	}

	/**
	 * Set up the settings fields and options and registers them
	 *
	 * @return void
	 */
	public function settings_page_options() {

		add_settings_section(
			'deletion-checkbox',
			__( 'Delete all site data and content', 'movie-library' ),
			[ $this, 'delete_header' ],
			'movie-library-settings'
		);

		add_settings_field(
			'delete-checkbox',
			__( 'Delete content and data', 'movie-library' ),
			[ $this, 'display_checkbox' ],
			'movie-library-settings',
			'deletion-checkbox'
		);

		register_setting( 'deletion-checkbox', 'delete-checkbox' );

		add_settings_section(
			'ml-api-key-section',
			__( 'Your API Key', 'movie-library' ),
			[ $this, 'api_key_page_header' ],
			'ml-api-key-setting'
		);

		add_settings_field(
			'ml-api-key-field',
			__( 'Your API Key', 'movie-library' ),
			[ $this, 'display_api_field' ],
			'ml-api-key-setting',
			'ml-api-key-section'
		);

		register_setting(
			'ml-api-key',
			'ml_api_key_field',
			array(
				'sanitize_callback' => array( $this, 'sanitize_api_key_callback' ),
			)
		);
	}

	/**
	 * Display the delete checkbox using its HTML
	 *
	 * @return void
	 */
	public function display_checkbox() {
		?>
			<input type="checkbox" name="delete-checkbox" value="1" <?php checked( 1, get_option( 'delete-checkbox' ), true ); ?> />
		<?php
	}

	/**
	 * Add the submenu page to the settings
	 *
	 * @return void
	 */
	public function show_settings() {
		add_submenu_page(
			'options-general.php',
			__( 'Movie Library', 'movie-library' ),
			__( 'Movie Library', 'movie-library' ),
			'manage_options',
			'checkbox-to-delete',
			[ $this, 'custom_settings_page' ]
		);
	}

	/**
	 * Display the heading for the movie library settings section
	 *
	 * @return void
	 */
	public function delete_header() {
		?>
		<p>
			<strong>
				<?php esc_html_e( 'Warning', 'movie-library' ); ?>
			</strong>
			<?php esc_html_e( 'Checking this checkbox and saving these changes will erase all content and data generated and stored by the plugin.', 'movie-library' ); ?>
		</p>
		<?php
	}

	/**
	 * Create the HTML to be displayed in the settings page along with settings fields.
	 *
	 * @return void
	 */
	public function custom_settings_page() {
		?>
		<div class="main">
			<h1><?php esc_html_e( 'Movie Library Plugin Settings', 'movie-library' ); ?></h1>
			<form method="post" action="options.php">
				<br>
				<?php
				settings_fields( 'deletion-checkbox' );
				do_settings_sections( 'movie-library-settings' );
				settings_fields( 'ml-api-key' );
				do_settings_sections( 'ml-api-key-setting' );
				?>
				<?php
				submit_button( 'Save Settings' );
				?>
			</form>
		</div>
		<?php
	}
	/**
	 * Display the heading text for the API key settings page.
	 *
	 * @return void
	 */
	public function api_key_page_header() {
		?>
		<p>
			<?php esc_html_e( 'The following API key will be used for calling the iMDb API in the dashboard widget.', 'movie-library' ); ?>
		</p>
		<?php
	}
	/**
	 * Render the HTML of the API Key input field to take in the API Key input.
	 *
	 * @return void
	 */
	public function display_api_field() {
		?>
		<input
			type='text'
			name='ml_api_key_field'
			id='ml_api_key_field'
			<?php
			if ( get_option( 'ml_api_key_field' ) ) {
				?>
				placeholder='xxxxxxxxx'
				<?php
			} else {
				?>
				placeholder= '<?php echo esc_attr_e( 'Enter API key here', 'movie-library' ); ?>'
				<?php
			}
			?>
		/>
		<?php
	}

	/**
	 * Sanitize and check for empty key field.
	 *
	 * @param string $option_value The value of the key being passed.
	 * @return string
	 */
	public function sanitize_api_key_callback( $option_value ) {
		$api_key = get_option( 'ml_api_key_field' );
		if ( empty( $option_value ) ) {
			if ( $api_key ) {
				return $api_key;
			}
		}
		return sanitize_text_field( $option_value );

	}
}
