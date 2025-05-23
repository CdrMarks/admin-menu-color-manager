<?php
/**
 * Plugin Name: Admin Menu Color Manager
 * Plugin URI: https://github.com/CdrMarks/admin-menu-color-manager.git
 * Description: Customize the colors of the WordPress admin menu.
 * Version: 1.0.1
 * Author: Ryan Marks
 * Author URL: https://profile.wordpress.org/rmarks
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Class Admin_Menu_Color_Manager
 * Manages the settings and application of custom admin menu colors.
 */
class Admin_Menu_Color_Manager {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_color_picker' ) );
        add_action( 'admin_head', array( $this, 'apply_custom_admin_styles' ) );
    }

    /**
     * Adds the admin menu page under the "Settings" menu.
     */
    public function add_admin_menu_page() {
        add_options_page(
            __( 'Admin Menu Color Settings', 'admin-menu-color-manager' ),
            __( 'Menu Color', 'admin-menu-color-manager' ),
            'manage_options',
            'admin-menu-color',
            array( $this, 'render_settings_page' )
        );
    }

    /**
     * Registers the plugin settings.
     */
    public function register_settings() {
        register_setting(
            'admin_menu_color_group', // Option group
            'amcm_background_color',  // Option name for background color
            array(
                'type'              => 'string',
                'sanitize_callback' => array( $this, 'sanitize_hex_color' ),
                'default'           => '#23282d', // Default WordPress admin background
            )
        );

        register_setting(
            'admin_menu_color_group', // Option group
            'amcm_text_color',        // Option name for text color
            array(
                'type'              => 'string',
                'sanitize_callback' => array( $this, 'sanitize_hex_color' ),
                'default'           => '#a7aaad', // Default WordPress admin text
            )
        );

        register_setting(
            'admin_menu_color_group', // Option group
            'amcm_hover_background_color', // Option name for hover background color
            array(
                'type'              => 'string',
                'sanitize_callback' => array( $this, 'sanitize_hex_color' ),
                'default'           => '#0073aa', // Default WordPress admin hover background
            )
        );

        register_setting(
            'admin_menu_color_group', // Option group
            'amcm_hover_text_color',  // Option name for hover text color
            array(
                'type'              => 'string',
                'sanitize_callback' => array( $this, 'sanitize_hex_color' ),
                'default'           => '#ffffff', // Default WordPress admin hover text
            )
        );

        register_setting(
            'admin_menu_color_group', // Option group
            'amcm_current_item_background_color',  // Option name for current item background color
            array(
                'type'              => 'string',
                'sanitize_callback' => array( $this, 'sanitize_hex_color' ),
                'default'           => '#191e23', // Default WordPress admin current item background
            )
        );

        register_setting(
            'admin_menu_color_group', // Option group
            'amcm_current_item_text_color',  // Option name for current item text color
            array(
                'type'              => 'string',
                'sanitize_callback' => array( $this, 'sanitize_hex_color' ),
                'default'           => '#ffffff', // Default WordPress admin current item text
            )
        );


        add_settings_section(
            'amcm_color_settings_section',    // ID of the section
            __( 'Customize Admin Menu Colors', 'admin-menu-color-manager' ), // Title of the section
            array( $this, 'settings_section_callback' ), // Callback for section content
            'admin-menu-color'                // Page slug where this section appears
        );

        add_settings_field(
            'amcm_background_color_field',     // ID of the field
            __( 'Menu Background Color', 'admin-menu-color-manager' ),          // Title of the field
            array( $this, 'render_color_field' ), // Callback to render the field
            'admin-menu-color',                // Page slug
            'amcm_color_settings_section',     // Section ID
            array(
                'option_name' => 'amcm_background_color',
                'description' => __( 'Choose the main background color for the admin menu and submenu containers.', 'admin-menu-color-manager' ),
            )
        );

        add_settings_field(
            'amcm_text_color_field',     // ID of the field
            __( 'Menu Text Color', 'admin-menu-color-manager' ),          // Title of the field
            array( $this, 'render_color_field' ), // Callback to render the field
            'admin-menu-color',                // Page slug
            'amcm_color_settings_section',     // Section ID
            array(
                'option_name' => 'amcm_text_color',
                'description' => __( 'Choose the default color for menu text and icons.', 'admin-menu-color-manager' ),
            )
        );

        add_settings_field(
            'amcm_hover_background_color_field',     // ID of the field
            __( 'Menu Item Hover Background Color', 'admin-menu-color-manager' ),          // Title of the field
            array( $this, 'render_color_field' ), // Callback to render the field
            'admin-menu-color',                // Page slug
            'amcm_color_settings_section',     // Section ID
            array(
                'option_name' => 'amcm_hover_background_color',
                'description' => __( 'Choose the background color when hovering over menu items.', 'admin-menu-color-manager' ),
            )
        );

        add_settings_field(
            'amcm_hover_text_color_field',     // ID of the field
            __( 'Menu Item Hover Text Color', 'admin-menu-color-manager' ),          // Title of the field
            array( $this, 'render_color_field' ), // Callback to render the field
            'admin-menu-color',                // Page slug
            'amcm_color_settings_section',     // Section ID
            array(
                'option_name' => 'amcm_hover_text_color',
                'description' => __( 'Choose the text and icon color when hovering over menu items.', 'admin-menu-color-manager' ),
            )
        );

        add_settings_field(
            'amcm_current_item_background_color_field',     // ID of the field
            __( 'Current Menu Item Background Color', 'admin-menu-color-manager' ),          // Title of the field
            array( $this, 'render_color_field' ), // Callback to render the field
            'admin-menu-color',                // Page slug
            'amcm_color_settings_section',     // Section ID
            array(
                'option_name' => 'amcm_current_item_background_color',
                'description' => __( 'Choose the background color for the currently active menu item.', 'admin-menu-color-manager' ),
            )
        );

        add_settings_field(
            'amcm_current_item_text_color_field',     // ID of the field
            __( 'Current Menu Item Text Color', 'admin-menu-color-manager' ),          // Title of the field
            array( $this, 'render_color_field' ), // Callback to render the field
            'admin-menu-color',                // Page slug
            'amcm_color_settings_section',     // Section ID
            array(
                'option_name' => 'amcm_current_item_text_color',
                'description' => __( 'Choose the text and icon color for the currently active menu item.', 'admin-menu-color-manager' ),
            )
        );
    }

    /**
     * Sanitizes a HEX color value.
     *
     * @param string $color The color value to sanitize.
     * @return string The sanitized HEX color or an empty string if invalid.
     */
    public function sanitize_hex_color( $color ) {
        if ( '' === $color ) {
            return '';
        }

        // 3 or 6 hex digits, or the empty string.
        if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
            return $color;
        }

        return ''; // Return empty string for invalid colors.
    }

    /**
     * Renders the settings section description.
     */
    public function settings_section_callback() {
        echo '<p>' . esc_html__( 'Use the color pickers below to customize the appearance of your WordPress admin menu.', 'admin-menu-color-manager' ) . '</p>';
    }

    /**
     * Renders a color input field using the WordPress color picker.
     *
     * @param array $args Arguments containing 'option_name' and 'description'.
     */
    public function render_color_field( $args ) {
        $option_name = esc_attr( $args['option_name'] );
        $description = isset( $args['description'] ) ? esc_html( $args['description'] ) : '';
        $value       = get_option( $option_name );
        ?>
        <input type="text" name="<?php echo $option_name; ?>" value="<?php echo esc_attr( $value ); ?>" class="admin-menu-color-field" data-default-color="" />
        <p class="description"><?php echo $description; ?></p>
        <?php
    }

    /**
     * Renders the main settings page.
     */
    public function render_settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'admin_menu_color_group' ); // Output security fields for the registered setting.
                do_settings_sections( 'admin-menu-color' ); // Output setting sections and their fields.
                submit_button( __( 'Save Changes', 'admin-menu-color-manager' ) );
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Enqueues the WordPress color picker script and styles.
     *
     * @param string $hook The current admin page hook.
     */
    public function enqueue_color_picker( $hook ) {
        if ( 'settings_page_admin-menu-color' != $hook ) {
            return;
        }

        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_script(
            'admin-menu-color-picker-init',
            plugins_url( 'js/color-picker-init.js', __FILE__ ),
            array( 'jquery', 'wp-color-picker' ),
            null,
            true
        );
    }

    /**
     * Applies custom CSS to the admin menu based on saved settings.
     */
    public function apply_custom_admin_styles() {
        $background_color            = get_option( 'amcm_background_color' );
        $text_color                  = get_option( 'amcm_text_color' );
        $hover_background_color      = get_option( 'amcm_hover_background_color' );
        $hover_text_color            = get_option( 'amcm_hover_text_color' );
        $current_item_background_color = get_option( 'amcm_current_item_background_color' );
        $current_item_text_color     = get_option( 'amcm_current_item_text_color' );


        // Only output styles if at least one color is set.
        if ( ! empty( $background_color ) || ! empty( $text_color ) || ! empty( $hover_background_color ) || ! empty( $hover_text_color ) || ! empty( $current_item_background_color ) || ! empty( $current_item_text_color ) ) {
            ?>
            <style type="text/css">
                /* General Admin Menu Background & Text */
                #adminmenuback, #adminmenuwrap, #adminmenu, #adminmenu .wp-submenu,
                #adminmenu .wp-submenu-wrap {
                <?php if ( ! empty( $background_color ) ) : ?>
                    background-color: <?php echo esc_attr( $background_color ); ?> !important;
                <?php endif; ?>
                }

                /* Default Text and Icon Color for all menu items */
                #adminmenu li.menu-top a,
                #adminmenu li.menu-top .wp-menu-image::before,
                #adminmenu .wp-submenu li a {
                <?php if ( ! empty( $text_color ) ) : ?>
                    color: <?php echo esc_attr( $text_color ); ?> !important;
                <?php endif; ?>
                }

                /* Hover State Background for Top-Level Menu Items */
                #adminmenu li.menu-top:hover,
                #adminmenu li.opensub > a:hover,
                #adminmenu li > a.menu-top:focus,
                #adminmenu li.menu-top.menu-top-last.opensub > a:hover {
                <?php if ( ! empty( $hover_background_color ) ) : ?>
                    background-color: <?php echo esc_attr( $hover_background_color ); ?> !important;
                <?php endif; ?>
                }

                /* Hover State Text and Icons for Top-Level Menu Items */
                #adminmenu li.menu-top:hover .wp-menu-name,
                #adminmenu li.opensub > a:hover .wp-menu-name,
                #adminmenu li > a.menu-top:focus .wp-menu-name,
                #adminmenu li.menu-top:hover .wp-menu-image::before,
                #adminmenu li.opensub > a:hover .wp-menu-image::before,
                #adminmenu li > a.menu-top:focus .wp-menu-image::before {
                <?php if ( ! empty( $hover_text_color ) ) : ?>
                    color: <?php echo esc_attr( $hover_text_color ); ?> !important;
                <?php endif; ?>
                }

                /* Submenu Item Hover */
                #adminmenu .wp-submenu li a:hover {
                <?php if ( ! empty( $hover_background_color ) ) : ?>
                    background-color: <?php echo esc_attr( $hover_background_color ); ?> !important;
                <?php endif; ?>
                <?php if ( ! empty( $hover_text_color ) ) : ?>
                    color: <?php echo esc_attr( $hover_text_color ); ?> !important;
                <?php endif; ?>
                }


                /* Current/Active Top-Level Menu Item Background */
                #adminmenu li.current > a.menu-top,
                #adminmenu li.current.menu-top,
                #adminmenu li.current.menu-top a,
                #adminmenu li.current.menu-top.opensub > a {
                <?php if ( ! empty( $current_item_background_color ) ) : ?>
                    background-color: <?php echo esc_attr( $current_item_background_color ); ?> !important;
                <?php endif; ?>
                }

                /* Current/Active Top-Level Menu Item Text and Icon */
                #adminmenu li.current a.menu-top .wp-menu-name,
                #adminmenu li.current .wp-menu-image::before,
                #adminmenu li.current a.menu-top.wp-has-current-submenu .wp-menu-image::before {
                <?php if ( ! empty( $current_item_text_color ) ) : ?>
                    color: <?php echo esc_attr( $current_item_text_color ); ?> !important;
                <?php endif; ?>
                }

                /* Active Submenu Item Background and Text */
                #adminmenu .wp-has-current-submenu .wp-submenu .wp-submenu-head,
                #adminmenu .wp-menu-open.menu-top .wp-submenu li.current > a,
                #adminmenu .wp-menu-open.menu-top .wp-submenu li.current > a:hover {
                <?php if ( ! empty( $current_item_background_color ) ) : ?>
                    background-color: <?php echo esc_attr( $current_item_background_color ); ?> !important;
                <?php endif; ?>
                <?php if ( ! empty( $current_item_text_color ) ) : ?>
                    color: <?php echo esc_attr( $current_item_text_color ); ?> !important;
                <?php endif; ?>
                }

                /* Submenu item for active top-level menu */
                #adminmenu .wp-has-current-submenu .wp-submenu .wp-submenu-head {
                <?php if ( ! empty( $current_item_text_color ) ) : ?>
                    color: <?php echo esc_attr( $current_item_text_color ); ?> !important;
                <?php endif; ?>
                }

                /* Admin menu arrow for active items */
                #adminmenu .wp-has-current-submenu .wp-menu-arrow,
                #adminmenu .wp-has-current-submenu .wp-menu-arrow div {
                <?php if ( ! empty( $current_item_background_color ) ) : ?>
                    background: <?php echo esc_attr( $current_item_background_color ); ?> !important; /* Use current item background for arrow */
                <?php endif; ?>
                }

                /* Adjusting text for currently active submenu item */
                #adminmenu .wp-submenu li.current a,
                #adminmenu .wp-submenu li.current a:hover {
                <?php if ( ! empty( $current_item_text_color ) ) : ?>
                    color: <?php echo esc_attr( $current_item_text_color ); ?> !important;
                <?php endif; ?>
                }
            </style>
            <?php
        }
    }
}

// Initialize the plugin.
new Admin_Menu_Color_Manager();