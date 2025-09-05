<?php
/**
 * Plugin Name: Floating Circle Button
 * Plugin URI: https://idevelop.vip/plugins/plugin/idevelop-floating-circle-button/
 * Description: Adds a sticky Floating Circle button to your WordPress site with customizable options.
 * Version: 1.0.3
 * Author: iDevelop
 * Author URI: https://idevelop.vip/plugins
 * License: GPL2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class iDevelop_Floating_Button
 */
class iDevelop_Floating_Button {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'settings_init' ) );
        add_action( 'wp_footer', array( $this, 'display_floating_button' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
    }

    /**
     * Add admin menu page
     */
    public function add_admin_menu() {
        add_menu_page(
            __( 'Floating Circle Button Settings', 'idevelop-floating-circle-button' ), // Page title
            __( 'Floating Circle Button', 'idevelop-floating-circle-button' ),         // Menu title
            'manage_options',                                       // Capability
            'idevelop-floating-circle-button',                                   // Menu slug
            array( $this, 'settings_page_html' ),                   // Callback function
            'dashicons-whatsapp',                                   // Icon (requires dashicons)
            6                                                       // Position
        );
    }

    /**
     * Register settings
     */
    public function settings_init() {
        register_setting( 'idevelop_floating_button_group', 'idevelop_floating_button_options', array( $this, 'sanitize_floating_button_options' ) );

        add_settings_section(
            'idevelop_floating_button_section',
            __( 'Floating Circle Button Settings', 'idevelop-floating-circle-button' ),
            array( $this, 'settings_section_callback' ),
            'idevelop-floating-circle-button'
        );

        add_settings_field(
            'idevelop_floating_button_enable',
            __( 'Enable Floating Circle Button', 'idevelop-floating-circle-button' ),
            array( $this, 'enable_callback' ),
            'idevelop-floating-circle-button',
            'idevelop_floating_button_section'
        );

        add_settings_field(
            'idevelop_floating_button_phone',
            __( 'Phone Number (e.g., +972521234567)', 'idevelop-floating-circle-button' ),
            array( $this, 'phone_callback' ),
            'idevelop-floating-circle-button',
            'idevelop_floating_button_section'
        );

        add_settings_field(
            'idevelop_floating_button_pre_filled_message',
            __( 'Pre-filled Message', 'idevelop-floating-circle-button' ),
            array( $this, 'pre_filled_message_callback' ),
            'idevelop-floating-circle-button',
            'idevelop_floating_button_section'
        );
 
        add_settings_field(
            'idevelop_floating_button_icon',
            __( 'Choose Icon', 'idevelop-floating-circle-button' ),
            array( $this, 'icon_callback' ),
            'idevelop-floating-circle-button',
            'idevelop_floating_button_section'
        );

        add_settings_field(
            'idevelop_floating_button_vertical_position',
            __( 'Vertical Position', 'idevelop-floating-circle-button' ),
            array( $this, 'vertical_position_callback' ),
            'idevelop-floating-circle-button',
            'idevelop_floating_button_section'
        );

        add_settings_field(
            'idevelop_floating_button_horizontal_position',
            __( 'Horizontal Position', 'idevelop-floating-circle-button' ),
            array( $this, 'horizontal_position_callback' ),
            'idevelop-floating-circle-button',
            'idevelop_floating_button_section'
        );

        add_settings_field(
            'idevelop_floating_button_display_devices',
            __( 'Display on Devices', 'idevelop-floating-circle-button' ),
            array( $this, 'display_devices_callback' ),
            'idevelop-floating-circle-button',
            'idevelop_floating_button_section'
        );

        add_settings_field(
            'idevelop_floating_button_delay',
            __( 'Delay Before Appearance (seconds)', 'idevelop-floating-circle-button' ),
            array( $this, 'delay_callback' ),
            'idevelop-floating-circle-button',
            'idevelop_floating_button_section'
        );

        add_settings_field(
            'idevelop_floating_button_scroll_percentage',
            __( 'Appear After Scrolling (%)', 'idevelop-floating-circle-button' ),
            array( $this, 'scroll_percentage_callback' ),
            'idevelop-floating-circle-button',
            'idevelop_floating_button_section'
        );

        add_settings_field(
            'idevelop_floating_button_open_new_tab',
            __( 'Open chat in new tab', 'idevelop-floating-circle-button' ),
            array( $this, 'open_new_tab_callback' ),
            'idevelop-floating-circle-button',
            'idevelop_floating_button_section'
        );
    }

    /**
     * Settings section callback
     */
    public function settings_section_callback() {
        echo '<p>' . esc_html__( 'Configure your Floating Circle Button settings below.', 'idevelop-floating-circle-button' ) . '</p>';
    }

    /**
     * Enable checkbox callback
     */
    public function enable_callback() {
        $options = get_option( 'idevelop_floating_button_options' );
        echo '<input type="checkbox" name="idevelop_floating_button_options[enable]" value="1"' . checked( 1, isset( $options['enable'] ) ? $options['enable'] : 0, false ) . ' />';
    }

    /**
     * Phone number field callback
     */
    public function phone_callback() {
        $options = get_option( 'idevelop_floating_button_options' );
        $phone = isset( $options['phone'] ) ? sanitize_text_field( $options['phone'] ) : '';
        echo '<input type="text" name="idevelop_floating_button_options[phone]" value="' . esc_attr( $phone ) . '" placeholder="+972521234567" />';
        echo '<p class="description">' . esc_html__( 'Enter your phone number including country code (e.g., +972521234567).', 'idevelop-floating-circle-button' ) . '</p>';
    }

    /**
     * Pre-filled message field callback
     */
    public function pre_filled_message_callback() {
        $options = get_option( 'idevelop_floating_button_options' );
        $message = isset( $options['pre_filled_message'] ) ? sanitize_textarea_field( $options['pre_filled_message'] ) : '';
        echo '<textarea name="idevelop_floating_button_options[pre_filled_message]" rows="5" cols="50">' . esc_textarea( $message ) . '</textarea>';
        echo '<p class="description">' . esc_html__( 'Enter a pre-filled message for the chat. Use placeholders like {{url}} for current page URL, {{title}} for current page title, and {{field_name}} for custom field values.', 'idevelop-floating-circle-button' ) . '</p>';
    }
 
    /**
     * Icon selection callback
     */
    public function icon_callback() {
        $options = get_option( 'idevelop_floating_button_options' );
        $selected_icon = isset( $options['icon'] ) ? $options['icon'] : 'icon1'; // Default icon

        $icon1_url = plugins_url( 'assets/floating-circle-button-wa-black.svg', __FILE__ );
        $icon2_url = plugins_url( 'assets/floating-circle-button-wa-green.svg', __FILE__ );

        echo '<label>';
        echo '<input type="radio" name="idevelop_floating_button_options[icon]" value="icon1"' . checked( 'icon1', $selected_icon, false ) . ' />';
        echo '<img src="' . esc_url( $icon1_url ) . '" style="width: 30px; height: 30px; vertical-align: middle; margin-right: 5px;" alt="Floating Circle Button Black Plain" />';
        echo esc_html__( 'Floating Circle Button Black Plain', 'idevelop-floating-circle-button' );
        echo '</label><br>';

        echo '<label>';
        echo '<input type="radio" name="idevelop_floating_button_options[icon]" value="icon2"' . checked( 'icon2', $selected_icon, false ) . ' />';
        echo '<img src="' . esc_url( $icon2_url ) . '" style="width: 30px; height: 30px; vertical-align: middle; margin-right: 5px;" alt="Floating Circle Button Standard Square" />';
        echo esc_html__( 'Floating Circle Button Standard Square', 'idevelop-floating-circle-button' );
        echo '</label>';
    }

    /**
     * Vertical position callback
     */
    public function vertical_position_callback() {
        $options = get_option( 'idevelop_floating_button_options' );
        $selected_position = isset( $options['vertical_position'] ) ? $options['vertical_position'] : 'bottom'; // Default

        echo '<select name="idevelop_floating_button_options[vertical_position]">';
        echo '<option value="bottom"' . selected( 'bottom', $selected_position, false ) . '>' . esc_html__( 'Bottom of screen', 'idevelop-floating-circle-button' ) . '</option>';
        echo '<option value="middle"' . selected( 'middle', $selected_position, false ) . '>' . esc_html__( 'Middle of screen', 'idevelop-floating-circle-button' ) . '</option>';
        echo '</select>';
    }

    /**
     * Horizontal position callback
     */
    public function horizontal_position_callback() {
        $options = get_option( 'idevelop_floating_button_options' );
        $selected_position = isset( $options['horizontal_position'] ) ? $options['horizontal_position'] : 'right'; // Default

        echo '<select name="idevelop_floating_button_options[horizontal_position]">';
        echo '<option value="right"' . selected( 'right', $selected_position, false ) . '>' . esc_html__( 'Right', 'idevelop-floating-circle-button' ) . '</option>';
        echo '<option value="left"' . selected( 'left', $selected_position, false ) . '>' . esc_html__( 'Left', 'idevelop-floating-circle-button' ) . '</option>';
        echo '<option value="center"' . selected( 'center', $selected_position, false ) . '>' . esc_html__( 'Center (Horizontal)', 'idevelop-floating-circle-button' ) . '</option>';
        echo '</select>';
    }

    /**
     * Display on Devices callback
     */
    public function display_devices_callback() {
        $options = get_option( 'idevelop_floating_button_options' );
        $selected_devices = isset( $options['display_devices'] ) ? (array) $options['display_devices'] : array('desktop', 'mobile', 'tablet');

        echo '<label>';
        echo '<input type="checkbox" name="idevelop_floating_button_options[display_devices][]" value="desktop"' . checked( in_array( 'desktop', $selected_devices ), true, false ) . ' /> ';
        echo esc_html__( 'Desktop', 'idevelop-floating-circle-button' );
        echo '</label><br>';

        echo '<label>';
        echo '<input type="checkbox" name="idevelop_floating_button_options[display_devices][]" value="mobile"' . checked( in_array( 'mobile', $selected_devices ), true, false ) . ' /> ';
        echo esc_html__( 'Mobile', 'idevelop-floating-circle-button' );
        echo '</label><br>';

        echo '<label>';
        echo '<input type="checkbox" name="idevelop_floating_button_options[display_devices][]" value="tablet"' . checked( in_array( 'tablet', $selected_devices ), true, false ) . ' /> ';
        echo esc_html__( 'Tablet', 'idevelop-floating-circle-button' );
        echo '</label>';
    }

    /**
     * Delay Before Appearance callback
     */
    public function delay_callback() {
        $options = get_option( 'idevelop_floating_button_options' );
        $delay = isset( $options['delay'] ) ? intval( $options['delay'] ) : 0;
        echo '<input type="number" name="idevelop_floating_button_options[delay]" value="' . esc_attr( $delay ) . '" min="0" />';
        echo '<p class="description">' . esc_html__( 'Enter the number of seconds before the button appears. Set to 0 for no delay.', 'idevelop-floating-circle-button' ) . '</p>';
    }

    /**
     * Appear After Scrolling (%) callback
     */
    public function scroll_percentage_callback() {
        $options = get_option( 'idevelop_floating_button_options' );
        $scroll_percentage = isset( $options['scroll_percentage'] ) ? intval( $options['scroll_percentage'] ) : 0;
        echo '<input type="number" name="idevelop_floating_button_options[scroll_percentage]" value="' . esc_attr( $scroll_percentage ) . '" min="0" max="100" />';
        echo '<p class="description">' . esc_html__( 'Enter the percentage of the page scrolled before the button appears. Set to 0 to appear immediately.', 'idevelop-floating-circle-button' ) . '</p>';
    }

    /**
     * Open in new tab checkbox callback
     */
    public function open_new_tab_callback() {
        $options = get_option( 'idevelop_floating_button_options' );
        echo '<input type="checkbox" name="idevelop_floating_button_options[open_new_tab]" value="1"' . checked( 1, isset( $options['open_new_tab'] ) ? $options['open_new_tab'] : 0, false ) . ' />';
        echo '<p class="description">' . esc_html__( 'Check this box to open the chat in a new browser tab.', 'idevelop-floating-circle-button' ) . '</p>';
    }

    /**
     * Sanitize plugin options
     *
     * @param array $input The input options.
     * @return array The sanitized options.
     */
    public function sanitize_floating_button_options( $input ) {
        $output = array();

        // Sanitize 'enable'
        $output['enable'] = isset( $input['enable'] ) ? 1 : 0;

        // Sanitize 'phone'
        $output['phone'] = isset( $input['phone'] ) ? sanitize_text_field( $input['phone'] ) : '';

        // Sanitize 'pre_filled_message'
        $output['pre_filled_message'] = isset( $input['pre_filled_message'] ) ? sanitize_textarea_field( $input['pre_filled_message'] ) : '';

        // Sanitize 'icon'
        $output['icon'] = isset( $input['icon'] ) && in_array( $input['icon'], array( 'icon1', 'icon2' ) ) ? sanitize_key( $input['icon'] ) : 'icon1';

        // Sanitize 'vertical_position'
        $output['vertical_position'] = isset( $input['vertical_position'] ) && in_array( $input['vertical_position'], array( 'bottom', 'middle' ) ) ? sanitize_key( $input['vertical_position'] ) : 'bottom';

        // Sanitize 'horizontal_position'
        $output['horizontal_position'] = isset( $input['horizontal_position'] ) && in_array( $input['horizontal_position'], array( 'right', 'left', 'center' ) ) ? sanitize_key( $input['horizontal_position'] ) : 'right';

        // Sanitize 'display_devices'
        if ( isset( $input['display_devices'] ) && is_array( $input['display_devices'] ) ) {
            $output['display_devices'] = array_map( 'sanitize_key', $input['display_devices'] );
        } else {
            $output['display_devices'] = array('desktop', 'mobile', 'tablet'); // Default
        }

        // Sanitize 'delay'
        $output['delay'] = isset( $input['delay'] ) ? intval( $input['delay'] ) : 0;
        if ( $output['delay'] < 0 ) {
            $output['delay'] = 0;
        }

        // Sanitize 'scroll_percentage'
        $output['scroll_percentage'] = isset( $input['scroll_percentage'] ) ? intval( $input['scroll_percentage'] ) : 0;
        if ( $output['scroll_percentage'] < 0 || $output['scroll_percentage'] > 100 ) {
            $output['scroll_percentage'] = 0;
        }

        // Sanitize 'open_new_tab'
        $output['open_new_tab'] = isset( $input['open_new_tab'] ) ? 1 : 0;

        return $output;
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
                settings_fields( 'idevelop_floating_button_group' );
                do_settings_sections( 'idevelop-floating-circle-button' );
                submit_button( __( 'Save Settings', 'idevelop-floating-circle-button' ) );
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Enqueue styles for the front-end button
     */
    public function enqueue_styles() {
        $options = get_option( 'idevelop_floating_button_options' );

        // Only enqueue if the button is enabled and phone number is set
        if ( isset( $options['enable'] ) && $options['enable'] && ! empty( $options['phone'] ) ) {
            wp_enqueue_style( 'idevelop-floating-circle-button-style', plugins_url( 'idevelop-floating-circle-button.css', __FILE__ ), array(), time());//filemtime( plugin_dir_path( __FILE__ ) . 'idevelop-floating-circle-button.css' ) );

            // Enqueue JavaScript for display conditions
            wp_enqueue_script( 'idevelop-floating-circle-button-script', plugins_url( 'idevelop-floating-circle-button.js', __FILE__ ), array(), filemtime( plugin_dir_path( __FILE__ ) . 'idevelop-floating-circle-button.js' ), true );

            // Pass options to JavaScript
            wp_localize_script( 'idevelop-floating-circle-button-script', 'idevelopFloatingButton', array(
                'delay' => isset( $options['delay'] ) ? intval( $options['delay'] ) : 0,
                'scroll_percentage' => isset( $options['scroll_percentage'] ) ? intval( $options['scroll_percentage'] ) : 0,
                'display_devices' => isset( $options['display_devices'] ) ? (array) $options['display_devices'] : array('desktop', 'mobile', 'tablet'),
                'open_new_tab' => isset( $options['open_new_tab'] ) ? (bool) $options['open_new_tab'] : true, // Default to true for backward compatibility
                'pre_filled_message' => isset( $options['pre_filled_message'] ) ? sanitize_textarea_field( $options['pre_filled_message'] ) : '',
                'phone' => isset( $options['phone'] ) ? sanitize_text_field( $options['phone'] ) : '',
            ) );

            // Generate dynamic CSS based on settings
            $vertical_pos = isset( $options['vertical_position'] ) ? $options['vertical_position'] : 'bottom';
            $horizontal_pos = isset( $options['horizontal_position'] ) ? $options['horizontal_position'] : 'right';

            $dynamic_css = "
            .idevelop-floating-circle-button {
                position: fixed;
                z-index: 9999;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                border-radius: 50%;
                overflow: hidden;
                justify-content: center;
                align-items: center;
                width: 50px; /* Default size */
                height: 50px; /* Default size */
                display: none; /* Hidden by default, controlled by JS */
            }
            .idevelop-floating-circle-button img {
                width: 100%;
                height: 100%;
                display: block;
            }
            ";

            // Apply vertical position
            if ( $vertical_pos === 'bottom' ) {
                $dynamic_css .= ".idevelop-floating-circle-button { bottom: 20px; top: auto; transform: none; }";
            } elseif ( $vertical_pos === 'middle' ) {
                $dynamic_css .= ".idevelop-floating-circle-button { top: 50%; bottom: auto; transform: translateY(-50%); }";
            }

            // Apply horizontal position
            if ( $horizontal_pos === 'right' ) {
                $dynamic_css .= ".idevelop-floating-circle-button { right: 20px; left: auto; }";
            } elseif ( $horizontal_pos === 'left' ) {
                $dynamic_css .= ".idevelop-floating-circle-button { left: 20px; right: auto; }";
            } elseif ( $horizontal_pos === 'center' ) {
                 // For center, we need to adjust for mobile responsiveness
                 $dynamic_css .= ".idevelop-floating-circle-button { left: 50%; right: auto; transform: translateX(-50%); }";
                 if ( $vertical_pos === 'middle' ) {
                     $dynamic_css .= ".idevelop-floating-circle-button { transform: translate(-50%, -50%); }";
                 }
                 // Add media query for center to prevent it from being hidden on small screens
                 $dynamic_css .= "
                 @media (max-width: 600px) {
                     .idevelop-floating-circle-button {
                         left: 50%; /* Center again for small screens */
                         transform: translateX(-50%);
                     }
                 }
                 ";
            }
            wp_add_inline_style( 'idevelop-floating-circle-button-style', $dynamic_css );
        }
    }

    /**
     * Display Floating Circle Button on the front-end
     */
    public function display_floating_button() {
        $options = get_option( 'idevelop_floating_button_options' );

        // Check if enabled and phone number is set
        if ( ! isset( $options['enable'] ) || ! $options['enable'] || empty( $options['phone'] ) ) {
            return;
        }

        $phone = sanitize_text_field( $options['phone'] );
        $pre_filled_message = isset( $options['pre_filled_message'] ) ? sanitize_textarea_field( $options['pre_filled_message'] ) : '';
        $icon_choice = isset( $options['icon'] ) ? $options['icon'] : 'icon1';
        // $vertical_pos and $horizontal_pos are no longer needed here as CSS is handled by wp_add_inline_style

        $icon_url = plugins_url( 'assets/floating-circle-button-wa-black.svg', __FILE__ );
        if ( $icon_choice === 'icon2' ) {
            $icon_url = plugins_url( 'assets/floating-circle-button-wa-green.svg', __FILE__ );
        }

        // Output the button HTML
        $chat_url = 'https://wa.me/' . esc_attr( $phone );
        if ( ! empty( $pre_filled_message ) ) {
            $chat_url .= '?text=' . urlencode( $pre_filled_message );
        }
        echo '<a href="' . esc_url( $chat_url ) . '" class="idevelop-floating-circle-button"';
        if ( isset( $options['open_new_tab'] ) && $options['open_new_tab'] ) {
            echo ' target="_blank" rel="noopener noreferrer"';
        }
        echo '>';
        echo '<img src="' . esc_url( $icon_url ) . '" alt="Floating Circle Button" />';
        echo '</a>';
    }
}

// Initialize the plugin
new iDevelop_Floating_Button();