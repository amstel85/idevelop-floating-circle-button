<?php
/**
 * Plugin Name: My WhatsApp Button
 * Plugin URI: https://idevelop.vip/plugins/my-whatsapp-button
 * Description: Adds a sticky WhatsApp button to your WordPress site with customizable options.
 * Version: 1.0.0
 * Author: iDevelop
 * Author URI: https://idevelop.vip/
 * License: GPL2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class iDevelop_WhatsApp_Button
 */
class iDevelop_WhatsApp_Button {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'settings_init' ) );
        add_action( 'wp_footer', array( $this, 'display_whatsapp_button' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
    }

    /**
     * Add admin menu page
     */
    public function add_admin_menu() {
        add_menu_page(
            __( 'WhatsApp Button Settings', 'my-whatsapp-button' ), // Page title
            __( 'WhatsApp Button', 'my-whatsapp-button' ),         // Menu title
            'manage_options',                                       // Capability
            'my-whatsapp-button',                                   // Menu slug
            array( $this, 'settings_page_html' ),                   // Callback function
            'dashicons-whatsapp',                                   // Icon (requires dashicons)
            6                                                       // Position
        );
    }

    /**
     * Register settings
     */
    public function settings_init() {
        register_setting( 'my_whatsapp_button_group', 'my_whatsapp_button_options' );

        add_settings_section(
            'my_whatsapp_button_section',
            __( 'WhatsApp Button Settings', 'my-whatsapp-button' ),
            array( $this, 'settings_section_callback' ),
            'my-whatsapp-button'
        );

        add_settings_field(
            'my_whatsapp_button_enable',
            __( 'Enable WhatsApp Button', 'my-whatsapp-button' ),
            array( $this, 'enable_callback' ),
            'my-whatsapp-button',
            'my_whatsapp_button_section'
        );

        add_settings_field(
            'my_whatsapp_button_phone',
            __( 'WhatsApp Phone Number (e.g., +972521234567)', 'my-whatsapp-button' ),
            array( $this, 'phone_callback' ),
            'my-whatsapp-button',
            'my_whatsapp_button_section'
        );

        add_settings_field(
            'my_whatsapp_button_icon',
            __( 'Choose WhatsApp Icon', 'my-whatsapp-button' ),
            array( $this, 'icon_callback' ),
            'my-whatsapp-button',
            'my_whatsapp_button_section'
        );

        add_settings_field(
            'my_whatsapp_button_vertical_position',
            __( 'Vertical Position', 'my-whatsapp-button' ),
            array( $this, 'vertical_position_callback' ),
            'my-whatsapp-button',
            'my_whatsapp_button_section'
        );

        add_settings_field(
            'my_whatsapp_button_horizontal_position',
            __( 'Horizontal Position', 'my-whatsapp-button' ),
            array( $this, 'horizontal_position_callback' ),
            'my-whatsapp-button',
            'my_whatsapp_button_section'
        );
    }

    /**
     * Settings section callback
     */
    public function settings_section_callback() {
        echo '<p>' . __( 'Configure your WhatsApp button settings below.', 'my-whatsapp-button' ) . '</p>';
    }

    /**
     * Enable checkbox callback
     */
    public function enable_callback() {
        $options = get_option( 'my_whatsapp_button_options' );
        $checked = isset( $options['enable'] ) ? checked( 1, $options['enable'], false ) : '';
        echo '<input type="checkbox" name="my_whatsapp_button_options[enable]" value="1"' . $checked . ' />';
    }

    /**
     * Phone number field callback
     */
    public function phone_callback() {
        $options = get_option( 'my_whatsapp_button_options' );
        $phone = isset( $options['phone'] ) ? sanitize_text_field( $options['phone'] ) : '';
        echo '<input type="text" name="my_whatsapp_button_options[phone]" value="' . esc_attr( $phone ) . '" placeholder="+972521234567" />';
        echo '<p class="description">' . __( 'Enter your WhatsApp phone number including country code (e.g., +972521234567).', 'my-whatsapp-button' ) . '</p>';
    }

    /**
     * Icon selection callback
     */
    public function icon_callback() {
        $options = get_option( 'my_whatsapp_button_options' );
        $selected_icon = isset( $options['icon'] ) ? $options['icon'] : 'icon1'; // Default icon

        $icon1_url = plugins_url( 'assets/whatsapp-logo-variant-svgrepo-com.svg', __FILE__ );
        $icon2_url = plugins_url( 'assets/whatsapp-1623579.svg', __FILE__ );

        echo '<label>';
        echo '<input type="radio" name="my_whatsapp_button_options[icon]" value="icon1"' . checked( 'icon1', $selected_icon, false ) . ' />';
        echo '<img src="' . esc_url( $icon1_url ) . '" style="width: 30px; height: 30px; vertical-align: middle; margin-right: 5px;" alt="WhatsApp Black Plain" />';
        echo 'WhatsApp Black Plain';
        echo '</label><br>';

        echo '<label>';
        echo '<input type="radio" name="my_whatsapp_button_options[icon]" value="icon2"' . checked( 'icon2', $selected_icon, false ) . ' />';
        echo '<img src="' . esc_url( $icon2_url ) . '" style="width: 30px; height: 30px; vertical-align: middle; margin-right: 5px;" alt="WhatsApp Standard Square" />';
        echo 'WhatsApp Standard Square';
        echo '</label>';
    }

    /**
     * Vertical position callback
     */
    public function vertical_position_callback() {
        $options = get_option( 'my_whatsapp_button_options' );
        $selected_position = isset( $options['vertical_position'] ) ? $options['vertical_position'] : 'bottom'; // Default

        echo '<select name="my_whatsapp_button_options[vertical_position]">';
        echo '<option value="bottom"' . selected( 'bottom', $selected_position, false ) . '>' . __( 'Bottom of screen', 'my-whatsapp-button' ) . '</option>';
        echo '<option value="middle"' . selected( 'middle', $selected_position, false ) . '>' . __( 'Middle of screen', 'my-whatsapp-button' ) . '</option>';
        echo '</select>';
    }

    /**
     * Horizontal position callback
     */
    public function horizontal_position_callback() {
        $options = get_option( 'my_whatsapp_button_options' );
        $selected_position = isset( $options['horizontal_position'] ) ? $options['horizontal_position'] : 'right'; // Default

        echo '<select name="my_whatsapp_button_options[horizontal_position]">';
        echo '<option value="right"' . selected( 'right', $selected_position, false ) . '>' . __( 'Right', 'my-whatsapp-button' ) . '</option>';
        echo '<option value="left"' . selected( 'left', $selected_position, false ) . '>' . __( 'Left', 'my-whatsapp-button' ) . '</option>';
        echo '<option value="center"' . selected( 'center', $selected_position, false ) . '>' . __( 'Center (Horizontal)', 'my-whatsapp-button' ) . '</option>';
        echo '</select>';
    }

    /**
     * Settings page HTML
     */
    public function settings_page_html() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields( 'my_whatsapp_button_group' );
                do_settings_sections( 'my-whatsapp-button' );
                submit_button( __( 'Save Settings', 'my-whatsapp-button' ) );
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Enqueue styles for the front-end button
     */
    public function enqueue_styles() {
        $options = get_option( 'my_whatsapp_button_options' );

        // Only enqueue if the button is enabled and phone number is set
        if ( isset( $options['enable'] ) && $options['enable'] && ! empty( $options['phone'] ) ) {
            wp_enqueue_style( 'my-whatsapp-button-style', plugins_url( 'my-whatsapp-button.css', __FILE__ ) );
        }
    }

    /**
     * Display WhatsApp button on the front-end
     */
    public function display_whatsapp_button() {
        $options = get_option( 'my_whatsapp_button_options' );

        // Check if enabled and phone number is set
        if ( ! isset( $options['enable'] ) || ! $options['enable'] || empty( $options['phone'] ) ) {
            return;
        }

        $phone = sanitize_text_field( $options['phone'] );
        $icon_choice = isset( $options['icon'] ) ? $options['icon'] : 'icon1';
        $vertical_pos = isset( $options['vertical_position'] ) ? $options['vertical_position'] : 'bottom';
        $horizontal_pos = isset( $options['horizontal_position'] ) ? $options['horizontal_position'] : 'right';

        $icon_url = plugins_url( 'assets/whatsapp-logo-variant-svgrepo-com.svg', __FILE__ );
        if ( $icon_choice === 'icon2' ) {
            $icon_url = plugins_url( 'assets/whatsapp-1623579.svg', __FILE__ );
        }

        // Generate dynamic CSS based on settings
        $dynamic_css = "
        .my-whatsapp-button {
            position: fixed;
            z-index: 9999;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 50%;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 50px; /* Default size */
            height: 50px; /* Default size */
        }
        .my-whatsapp-button img {
            width: 100%;
            height: 100%;
            display: block;
        }
        ";

        // Apply vertical position
        if ( $vertical_pos === 'bottom' ) {
            $dynamic_css .= ".my-whatsapp-button { bottom: 20px; top: auto; transform: none; }";
        } elseif ( $vertical_pos === 'middle' ) {
            $dynamic_css .= ".my-whatsapp-button { top: 50%; bottom: auto; transform: translateY(-50%); }";
        }

        // Apply horizontal position
        if ( $horizontal_pos === 'right' ) {
            $dynamic_css .= ".my-whatsapp-button { right: 20px; left: auto; }";
        } elseif ( $horizontal_pos === 'left' ) {
            $dynamic_css .= ".my-whatsapp-button { left: 20px; right: auto; }";
        } elseif ( $horizontal_pos === 'center' ) {
             // For center, we need to adjust for mobile responsiveness
             $dynamic_css .= ".my-whatsapp-button { left: 50%; right: auto; transform: translateX(-50%); }";
             if ( $vertical_pos === 'middle' ) {
                 $dynamic_css .= ".my-whatsapp-button { transform: translate(-50%, -50%); }";
             }
             // Add media query for center to prevent it from being hidden on small screens
             $dynamic_css .= "
             @media (max-width: 600px) {
                 .my-whatsapp-button {
                     left: 50%; /* Center again for small screens */
                     transform: translateX(-50%);
                 }
             }
             ";
        }


        // Output dynamic CSS
        echo '<style>' . $dynamic_css . '</style>';

        // Output the button HTML
        echo '<a href="https://wa.me/' . esc_attr( $phone ) . '" class="my-whatsapp-button" target="_blank" rel="noopener noreferrer">';
        echo '<img src="' . esc_url( $icon_url ) . '" alt="WhatsApp" />';
        echo '</a>';
    }
}

// Initialize the plugin
new iDevelop_WhatsApp_Button();