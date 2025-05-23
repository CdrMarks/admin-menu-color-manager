<?php
/**
 * Plugin Name: Admin Bar & Menu Color Manager
 * Plugin URI: https://github.com/CdrMarks/admin-menu-color-manager.git
 * Description: Customize the colors of the WordPress admin bar and menu.
 * Version: 1.0.4
 * Author: Ryan Marks
 * Author URL: https://profile.wordpress.org/rmarks
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Class Admin_Bar_Menu_Color_Manager
 * Manages the settings and application of custom admin menu colors.
 */
class Admin_Bar_Menu_Color_Manager {

    // Store default colors to pass to JS
    private $default_colors = array();

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_color_picker' ) );
        add_action( 'admin_head', array( $this, 'apply_custom_admin_styles' ) );
        add_action( 'wp_head', array( $this, 'apply_custom_admin_styles' ) ); // For logged-in users on frontend with admin bar
    }

    /**
     * Adds the admin menu page under the "Settings" menu.
     */
    public function add_admin_menu_page() {
        add_options_page(
            __( 'Admin Bar & Menu Color Settings', 'admin-bar-menu-color-manager' ),
            __( 'Admin Colors', 'admin-bar-menu-color-manager' ),
            'manage_options',
            'admin-bar-menu-color',
            array( $this, 'render_settings_page' )
        );
    }

    /**
     * Registers the plugin settings and stores defaults.
     */
    public function register_settings() {
        // Admin Bar (Top Horizontal Menu) Colors
        $this->default_colors['abmcm_admin_bar_background_color'] = '#23282d';
        register_setting(
            'admin_bar_menu_color_group', // Option group
            'abmcm_admin_bar_background_color',
            array(
                'type'              => 'string',
                'sanitize_callback' => array( $this, 'sanitize_hex_color' ),
                'default'           => $this->default_colors['abmcm_admin_bar_background_color'],
            )
        );

        $this->default_colors['abmcm_admin_bar_text_color'] = '#eeeeee';
        register_setting(
            'admin_bar_menu_color_group', // Option group
            'abmcm_admin_bar_text_color',
            array(
                'type'              => 'string',
                'sanitize_callback' => array( $this, 'sanitize_hex_color' ),
                'default'           => $this->default_colors['abmcm_admin_bar_text_color'],
            )
        );

        $this->default_colors['abmcm_admin_bar_hover_color'] = '#0073aa';
        register_setting(
            'admin_bar_menu_color_group', // Option group
            'abmcm_admin_bar_hover_color',
            array(
                'type'              => 'string',
                'sanitize_callback' => array( $this, 'sanitize_hex_color' ),
                'default'           => $this->default_colors['abmcm_admin_bar_hover_color'],
            )
        );

        // Admin Menu Colors
        $this->default_colors['abmcm_background_color'] = '#23282d';
        register_setting(
            'admin_bar_menu_color_group', // Option group
            'abmcm_background_color',
            array(
                'type'              => 'string',
                'sanitize_callback' => array( $this, 'sanitize_hex_color' ),
                'default'           => $this->default_colors['abmcm_background_color'],
            )
        );

        $this->default_colors['abmcm_text_color'] = '#a7aaad';
        register_setting(
            'admin_bar_menu_color_group', // Option group
            'abmcm_text_color',
            array(
                'type'              => 'string',
                'sanitize_callback' => array( $this, 'sanitize_hex_color' ),
                'default'           => $this->default_colors['abmcm_text_color'],
            )
        );

        $this->default_colors['abmcm_hover_background_color'] = '#0073aa';
        register_setting(
            'admin_bar_menu_color_group', // Option group
            'abmcm_hover_background_color',
            array(
                'type'              => 'string',
                'sanitize_callback' => array( $this, 'sanitize_hex_color' ),
                'default'           => $this->default_colors['abmcm_hover_background_color'],
            )
        );

        $this->default_colors['abmcm_hover_text_color'] = '#ffffff';
        register_setting(
            'admin_bar_menu_color_group', // Option group
            'abmcm_hover_text_color',
            array(
                'type'              => 'string',
                'sanitize_callback' => array( $this, 'sanitize_hex_color' ),
                'default'           => $this->default_colors['abmcm_hover_text_color'],
            )
        );

        $this->default_colors['abmcm_current_item_background_color'] = '#191e23';
        register_setting(
            'admin_bar_menu_color_group', // Option group
            'abmcm_current_item_background_color',
            array(
                'type'              => 'string',
                'sanitize_callback' => array( $this, 'sanitize_hex_color' ),
                'default'           => $this->default_colors['abmcm_current_item_background_color'],
            )
        );

        $this->default_colors['abmcm_current_item_text_color'] = '#ffffff';
        register_setting(
            'admin_bar_menu_color_group', // Option group
            'abmcm_current_item_text_color',
            array(
                'type'              => 'string',
                'sanitize_callback' => array( $this, 'sanitize_hex_color' ),
                'default'           => $this->default_colors['abmcm_current_item_text_color'],
            )
        );


        add_settings_section(
            'abmcm_color_settings_section',    // ID of the section
            __( 'Customize Admin Bar & Menu Colors', 'admin-bar-menu-color-manager' ), // Title of the section
            array( $this, 'settings_section_callback' ), // Callback for section content
            'admin-bar-menu-color'                // Page slug where this section appears
        );

        // Admin Bar Settings Fields
        add_settings_field(
            'abmcm_admin_bar_background_color_field',     // ID of the field
            __( 'Admin Bar Background Color', 'admin-bar-menu-color-manager' ),
            array( $this, 'render_color_field' ),
            'admin-bar-menu-color',
            'abmcm_color_settings_section',
            array(
                'option_name' => 'abmcm_admin_bar_background_color',
                'description' => __( 'Choose the background color for the top horizontal Admin Bar.', 'admin-bar-menu-color-manager' ),
            )
        );

        add_settings_field(
            'abmcm_admin_bar_text_color_field',     // ID of the field
            __( 'Admin Bar Text Color', 'admin-bar-menu-color-manager' ),
            array( $this, 'render_color_field' ),
            'admin-bar-menu-color',
            'abmcm_color_settings_section',
            array(
                'option_name' => 'abmcm_admin_bar_text_color',
                'description' => __( 'Choose the default text and icon color for the top horizontal Admin Bar.', 'admin-bar-menu-color-manager' ),
            )
        );

        add_settings_field(
            'abmcm_admin_bar_hover_color_field',     // ID of the field
            __( 'Admin Bar Item Hover/Active Color', 'admin-bar-menu-color-manager' ),
            array( $this, 'render_color_field' ),
            'admin-bar-menu-color',
            'abmcm_color_settings_section',
            array(
                'option_name' => 'abmcm_admin_bar_hover_color',
                'description' => __( 'Choose the background color for Admin Bar items on hover, and for active/current items.', 'admin-bar-menu-color-manager' ),
            )
        );

        // Admin Menu Settings Fields
        add_settings_field(
            'abmcm_background_color_field',     // ID of the field
            __( 'Menu Background Color', 'admin-bar-menu-color-manager' ),          // Title of the field
            array( $this, 'render_color_field' ), // Callback to render the field
            'admin-bar-menu-color',                // Page slug
            'abmcm_color_settings_section',     // Section ID
            array(
                'option_name' => 'abmcm_background_color',
                'description' => __( 'Choose the main background color for the left-hand admin menu and submenu containers.', 'admin-bar-menu-color-manager' ),
            )
        );

        add_settings_field(
            'abmcm_text_color_field',     // ID of the field
            __( 'Menu Text Color', 'admin-bar-menu-color-manager' ),          // Title of the field
            array( $this, 'render_color_field' ), // Callback to render the field
            'admin-bar-menu-color',                // Page slug
            'abmcm_color_settings_section',     // Section ID
            array(
                'option_name' => 'abmcm_text_color',
                'description' => __( 'Choose the default color for left-hand menu text and icons.', 'admin-bar-menu-color-manager' ),
            )
        );

        add_settings_field(
            'abmcm_hover_background_color_field',     // ID of the field
            __( 'Menu Item Hover Background Color', 'admin-bar-menu-color-manager' ),          // Title of the field
            array( $this, 'render_color_field' ), // Callback to render the field
            'admin-bar-menu-color',                // Page slug
            'abmcm_color_settings_section',     // Section ID
            array(
                'option_name' => 'abmcm_hover_background_color',
                'description' => __( 'Choose the background color when hovering over left-hand menu items.', 'admin-bar-menu-color-manager' ),
            )
        );

        add_settings_field(
            'abmcm_hover_text_color_field',     // ID of the field
            __( 'Menu Item Hover Text Color', 'admin-bar-menu-color-manager' ),          // Title of the field
            array( $this, 'render_color_field' ), // Callback to render the field
            'admin-bar-menu-color',                // Page slug
            'abmcm_color_settings_section',     // Section ID
            array(
                'option_name' => 'abmcm_hover_text_color',
                'description' => __( 'Choose the text and icon color when hovering over left-hand menu items.', 'admin-bar-menu-color-manager' ),
            )
        );

        add_settings_field(
            'abmcm_current_item_background_color_field',     // ID of the field
            __( 'Current Menu Item Background Color', 'admin-bar-menu-color-manager' ),          // Title of the field
            array( $this, 'render_color_field' ), // Callback to render the field
            'admin-bar-menu-color',                // Page slug
            'abmcm_color_settings_section',     // Section ID
            array(
                'option_name' => 'abmcm_current_item_background_color',
                'description' => __( 'Choose the background color for the currently active left-hand menu item.', 'admin-bar-menu-color-manager' ),
            )
        );

        add_settings_field(
            'abmcm_current_item_text_color_field',     // ID of the field
            __( 'Current Menu Item Text Color', 'admin-bar-menu-color-manager' ),          // Title of the field
            array( $this, 'render_color_field' ), // Callback to render the field
            'admin-bar-menu-color',                // Page slug
            'abmcm_color_settings_section',     // Section ID
            array(
                'option_name' => 'abmcm_current_item_text_color',
                'description' => __( 'Choose the text and icon color for the currently active left-hand menu item.', 'admin-bar-menu-color-manager' ),
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
        echo '<p>' . esc_html__( 'Use the color pickers below to customize the appearance of your WordPress admin bar and menu.', 'admin-bar-menu-color-manager' ) . '</p>';
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
        <input type="text" name="<?php echo $option_name; ?>" value="<?php echo esc_attr( $value ); ?>" class="admin-menu-color-field" />
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
                settings_fields( 'admin_bar_menu_color_group' ); // Output security fields for the registered setting.
                do_settings_sections( 'admin-bar-menu-color' ); // Output setting sections and their fields.
                submit_button( __( 'Save Changes', 'admin-bar-menu-color-manager' ) );
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Enqueues the WordPress color picker script and styles.
     * Also localizes script with default colors.
     *
     * @param string $hook The current admin page hook.
     */
    public function enqueue_color_picker( $hook ) {
        if ( 'settings_page_admin-bar-menu-color' != $hook ) { // Updated page slug
            return;
        }

        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_script(
            'admin-bar-menu-color-picker-init', // Updated handle
            plugins_url( 'js/color-picker-init.js', __FILE__ ), // __FILE__ will now point to the new name
            array( 'jquery', 'wp-color-picker' ),
            '1.0.6', // Current version for cache busting
            true
        );

        // Pass default colors from PHP to JavaScript
        wp_localize_script(
            'admin-bar-menu-color-picker-init', // Handle of the script to localize
            'abmcm_data',                    // Name of the JS object (e.g., abmcm_data.defaultColors)
            array(
                'defaultColors' => $this->default_colors,
            )
        );
    }

    /**
     * Applies custom CSS to the admin menu and admin bar based on saved settings.
     */
    public function apply_custom_admin_styles() {
        // Admin Menu Colors
        $background_color            = get_option( 'abmcm_background_color' );
        $text_color                  = get_option( 'abmcm_text_color' );
        $hover_background_color      = get_option( 'abmcm_hover_background_color' );
        $hover_text_color            = get_option( 'abmcm_hover_text_color' );
        $current_item_background_color = get_option( 'abmcm_current_item_background_color' );
        $current_item_text_color     = get_option( 'abmcm_current_item_text_color' );

        // Admin Bar Colors
        $admin_bar_background_color = get_option( 'abmcm_admin_bar_background_color' );
        $admin_bar_text_color       = get_option( 'abmcm_admin_bar_text_color' );
        $admin_bar_hover_color      = get_option( 'abmcm_admin_bar_hover_color' );


        // Only output styles if at least one color is set.
        if ( ! empty( $background_color ) || ! empty( $text_color ) || ! empty( $hover_background_color ) || ! empty( $hover_text_color ) || ! empty( $current_item_background_color ) || ! empty( $current_item_text_color ) || ! empty( $admin_bar_background_color ) || ! empty( $admin_bar_text_color ) || ! empty( $admin_bar_hover_color ) ) {
            ?>
            <style type="text/css">
                /* --- Admin Bar (Top Horizontal Menu) Styles --- */

                #wpadminbar {
                <?php if ( ! empty( $admin_bar_background_color ) ) : ?>
                    background-color: <?php echo esc_attr( $admin_bar_background_color ); ?> !important;
                <?php endif; ?>
                }

                #wpadminbar .ab-item,
                #wpadminbar a.ab-item,
                #wpadminbar #wp-admin-bar-wp-logo.hover .ab-icon:before,
                #wpadminbar #wp-admin-bar-site-name.hover .ab-icon:before,
                #wpadminbar #wp-admin-bar-site-name.hover .ab-label,
                #wpadminbar #wp-admin-bar-my-account > .ab-item,
                #wpadminbar #wp-admin-bar-my-account.hover > .ab-item,
                #wpadminbar #wp-admin-bar-user-info .display-name,
                #wpadminbar #wp-admin-bar-user-info a,
                #wpadminbar .quicklinks .ab-top-menu > li.current > .ab-item,
                #wpadminbar .quicklinks .ab-top-menu > li.current-menu-parent > .ab-item,
                #wpadminbar .quicklinks .ab-top-menu > li.current.menu-top-item > .ab-item {
                <?php if ( ! empty( $admin_bar_text_color ) ) : ?>
                    color: <?php echo esc_attr( $admin_bar_text_color ); ?> !important;
                <?php endif; ?>
                }

                /* Admin Bar Hover and Active States */
                #wpadminbar .ab-top-menu > li.hover > .ab-item,
                #wpadminbar .ab-top-menu > li:hover > .ab-item,
                #wpadminbar .ab-top-menu > li.current-menu-item > .ab-item,
                #wpadminbar .ab-top-menu > li.current-menu-parent > .ab-item,
                #wpadminbar .ab-top-menu > li.current-menu-ancestor > .ab-item,
                #wpadminbar .ab-item:focus,
                #wpadminbar .ab-item:hover,
                #wpadminbar #wp-admin-bar-wp-logo > .ab-item:hover:before,
                #wpadminbar #wp-admin-bar-wp-logo.hover > .ab-item:before,
                #wpadminbar.nojq .quicklinks .ab-top-menu > li > .ab-item:focus,
                #wpadminbar .quicklinks .ab-top-menu > li > .ab-item:focus,
                #wpadminbar .quicklinks .ab-top-menu > li:hover > .ab-item,
                #wpadminbar .quicklinks .ab-top-menu > li.hover > .ab-item,
                #wpadminbar .menupop .ab-sub-wrapper {
                <?php if ( ! empty( $admin_bar_hover_color ) ) : ?>
                    background-color: <?php echo esc_attr( $admin_bar_hover_color ); ?> !important;
                <?php endif; ?>
                }

                #wpadminbar .menupop .ab-sub-wrapper .ab-item:hover,
                #wpadminbar .menupop .ab-sub-wrapper .ab-item:focus,
                #wpadminbar .menupop .ab-sub-wrapper a:hover,
                #wpadminbar .menupop .ab-sub-wrapper a:focus {
                <?php if ( ! empty( $admin_bar_hover_color ) ) : ?>
                    background-color: <?php echo esc_attr( $admin_bar_hover_color ); ?> !important;
                <?php endif; ?>
                <?php if ( ! empty( $admin_bar_text_color ) ) : ?>
                    color: <?php echo esc_attr( $admin_bar_text_color ); ?> !important; /* Keep text readable on hover */
                <?php endif; ?>
                }

                /* Admin Bar icons on hover/active */
                #wpadminbar .ab-icon:before {
                <?php if ( ! empty( $admin_bar_text_color ) ) : ?>
                    color: <?php echo esc_attr( $admin_bar_text_color ); ?> !important;
                <?php endif; ?>
                }

                #wpadminbar .ab-item:hover .ab-icon:before,
                #wpadminbar .ab-item:focus .ab-icon:before,
                #wpadminbar li:hover #adminbar-logo.hover > a .ab-icon:before,
                #wpadminbar li.hover .ab-icon:before,
                #wpadminbar .ab-menu-link:hover .ab-icon:before,
                #wpadminbar .ab-top-menu > li.current > .ab-item .ab-icon:before,
                #wpadminbar .ab-top-menu > li.current-menu-parent > .ab-item .ab-icon:before,
                #wpadminbar .ab-top-menu > li.current-menu-ancestor > .ab-item .ab-icon:before {
                <?php if ( ! empty( $admin_bar_text_color ) ) : ?>
                    color: <?php echo esc_attr( $admin_bar_text_color ); ?> !important;
                <?php endif; ?>
                }

                /* Admin Bar Submenu Background */
                #wpadminbar .menupop .ab-sub-wrapper,
                #wpadminbar .quicklinks .menupop ul.ab-sub-secondary,
                #wpadminbar .quicklinks .menupop ul.ab-sub-secondary .ab-submenu {
                <?php if ( ! empty( $admin_bar_background_color ) ) : ?>
                    background-color: <?php echo esc_attr( $admin_bar_background_color ); ?> !important;
                <?php endif; ?>
                }

                /* Admin Bar Submenu Item Text */
                #wpadminbar .quicklinks .menupop ul li a,
                #wpadminbar .quicklinks .menupop ul li a .ab-icon:before,
                #wpadminbar .quicklinks .menupop ul li a .ab-label {
                <?php if ( ! empty( $admin_bar_text_color ) ) : ?>
                    color: <?php echo esc_attr( $admin_bar_text_color ); ?> !important;
                <?php endif; ?>
                }

                /* Admin Menu Background & Text */
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
new Admin_Bar_Menu_Color_Manager();