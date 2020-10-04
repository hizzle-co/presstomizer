<?php
/**
 * Contains the class for creating custom customizer screens.
 */

/**
 * Provides an environment for creating custom customizer screens.
 *
 * @since 1.0.0
 */
class presstomizer {

	/**
	 * Contains the unique id of this instance.
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * Contains a cache of all our panels.
	 *
	 * @var array
	 */
	protected $panels = array();

	/**
	 * Contains a cache of all our sections.
	 *
	 * @var array
	 */
	protected $sections = array();

	/**
	 * Class constructor.
	 *
	 * @param string $id An alphanumeric unique id for your specific instance.
	 */
	public function __construct( $id ) {

		$this->id = sanitize_key( $id );

		// Setup the customizer in case this is a customizer request.
		if ( isset( $_REQUEST['wp_customize'] ) && isset( $_GET[ $this->id ] ) ) {
			$this->set_up_custom_customizer();
		}

	}

	/**
	 * Sets up our custom customizer.
	 *
	 */
	protected function set_up_custom_customizer() {

		// Remove sections/panels/controls that are not ours.
		add_filter( 'customize_section_active', array( $this, 'remove_third_party_sections' ), 999999, 2 );
		add_filter( 'customize_panel_active', array( $this, 'remove_third_party_panels' ), 999999, 2 );

		// Do not load core components.
		add_filter( 'customize_loaded_components', '__return_empty_array', 999999 );

		// Cleanup the customizer and the frontend.
		add_action( 'customize_register', array( $this, 'clean_up_customizer' ), 999999 );
		add_action( 'init', array( $this, 'clean_up_frontend' ), 999999 );

		// Load our own template.
		add_action( 'template_redirect', array( $this, 'maybe_display_frontend' ) );
	}

	/**
	 * Remover other sections
	 *
	 * @param bool                 $is_active  Whether the Customizer section is active.
	 * @param WP_Customize_Section $section WP_Customize_Section instance.
	 *
	 * @return bool
	 */
	public function remove_other_sections( $is_active, $section ) {
		return in_array( $section->id, $this->sections, true );
	}

	/**
	 * Remover other panels
	 *
	 * @param bool               $active Whether the Customizer panel is active.
	 * @param WP_Customize_Panel $panel  WP_Customize_Panel instance.
	 *
	 * @return bool
	 */
	public function remove_third_party_panels( $is_active, $panel ) {
		return in_array( $panel->id, $this->panels, true );
	}

	/**
	 * Remove third-party customizer elements.
	 *
	 * @param WP_Customize_Manager $wp_customize An instance of WP_Customize_Manager.
	 */
	public function clean_up_customizer( $wp_customize ) {

		$wp_customize->remove_panel( 'themes' );
		$wp_customize->remove_section( 'title_tagline' );
		remove_all_actions( 'admin_print_footer_scripts' );

	}

	/**
	 * Add a new customizer panel.
	 *
	 * Use this method to register a panel instead of directly calling WP_Customize_Manager::add_panel
	 * so that we can link the panel to your customizer instance.
	 *
	 * @param WP_Customize_Manager      $customizer An instance of the customize manager class.
	 * @param WP_Customize_Panel|string $id         Customize Panel object, or ID.
	 * @param array                     $args       Optional. Array of properties for the new Panel object.
	 * @see WP_Customize_Manager::add_panel
	 *
	 * @return WP_Customize_Panel
	 */
	public function add_panel( $customizer, $id, $args = array() ) {
		$this->panels[] = is_string( $id ) ? $id : $id->id;
		return $customizer->add_panel( $id, $args );
	}

	/**
	 * Add a new customizer section.
	 *
	 * Use this method to register a section instead of directly calling WP_Customize_Manager::add_section
	 * so that we can link the section to your customizer instance.
	 *
	 * @param WP_Customize_Manager        $customizer An instance of the customize manager class.
	 * @param WP_Customize_Section|string $id         Customize Section object, or ID.
	 * @param array                       $args       Optional. Array of properties for the new Section object.
	 * @see WP_Customize_Manager::add_section
	 *
	 * @return WP_Customize_Section
	 */
	public function add_section( $customizer, $id, $args = array() ) {
		$this->panels[] = is_string( $id ) ? $id : $id->id;
		return $customizer->add_section( $id, $args );
	}

	/**
	 * Tries to display our frontend page.
	 *
	 * @return string
	 */
	public function maybe_display_frontend() {

		if ( is_customize_preview() ) {
			$this->display_frontend();
			exit;
		}

	}

	/**
	 * Displays our page on the frontend.
	 *
	 */
	public function display_frontend() {
		do_action( "prestomizer_frontend_display_{$this->id}" );
	}

}

// TODO: Rename the "customize_controls_print_footer_scripts" && "customize_controls_enqueue_scripts" actions && "customize_controls_print_styles" and attach the code wp hooks.
