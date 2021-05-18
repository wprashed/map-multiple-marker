<?php

/**
 * WordPress settings API
 *
 * @author InternetCSS
 */
if ( !class_exists('GOOGLE_Map_Settings' ) ):
class GOOGLE_Map_Settings {

    private $settings_api;

    function __construct() {
        $this->settings_api = new EB_Map_Settings_API;

        add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'admin_menu', array($this, 'admin_menu'), 502 );
    }

    function admin_init() {

        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();

    }

    function admin_menu() {
        add_submenu_page( Elementor\Settings::PAGE_ID, __( 'Google Map', 'google-map-for-elementor' ), __( 'Google Map', 'google-map-for-elementor' ), 'delete_posts', 'GOOGLE_map_setting', 
            array($this, 'plugin_page' ) );
    }

    function get_settings_sections() {
        $sections = array(
            array(
                'id'    => 'eb_map_general_settings',
                'title' => __( 'Google Map Settings', 'google-map-for-elementor' ),
            ),
        );
        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {
        $settings_fields = array(
            'eb_map_general_settings' => array(
                array(
                    'name'              => 'GOOGLE_map_api_key',
                    'label'             => __( 'Google Map API', 'google-map-for-elementor' ),
                    'desc'              => __( 'You will need Google Map API in order to use Elementor Map Extended Widget. If not you can <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">click here</a> to generate one.', 'google-map-for-elementor' ),
                    'placeholder'       => __( 'Enter your Google Map API here', 'google-map-for-elementor' ),
                    'type'              => 'text',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
				array(
                    'name'    => 'GOOGLE_map_lang',
                    'label'   => __( 'Google Map Language', 'google-map-for-elementor' ),
                    'type'    => 'select',
                    'default' => 'en',
                    'options' => array(
                        'en' => 'English',
                        'en-Au'  => 'English (Australian)',
						'en-GB'  => 'English (Great Britain)',
						'es'  => 'Español',
						'de'  => 'Deutsch',
						'fr'  => 'Français',
                        'pt'  => 'Português',
                        'sv'  => 'Svenska',
						'ar'  => 'العربية',
						'ja'  => '日本語',
						'ko'  => '한국어',
                        'zh-CN'  => '简体中文',
                        'zh-HK'  => '香港中文版',
                        'zh-TW'  => '繁體中文',
                        'vi'  => 'Tiếng Việt',
                        'th'  => 'ไทย',
                        'iw'  => 'עִבְרִית',
                    )
                ),
                array(
                    'name'              => 'eb_dequeue_scripts',
                    'label'             => __( 'Dequeue Scripts', 'google-map-for-elementor' ),
                    'desc'              => __( 'If you know the handle script of the other plugin, you can dequeue the script that also enqueue Google Map Script to improve compatiblity. (e.g. essential_addons_elementor-google-map-api, ep-google-maps) separate with a comma.', 'google-map-for-elementor' ),
                    'placeholder'       => __( 'essential_addons_elementor-google-map-api, ep-google-maps', 'google-map-for-elementor' ),
                    'type'              => 'text',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
            ),
        );

        return $settings_fields;
    }

    function plugin_page() {
        echo '<div class="wrap">';
        echo __( '<h1>Multiple Map Marker For Elementor Page Builder</h1>', 'google-map-for-elementor' );

        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();

        echo '</div>';
        
    }

    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    function get_pages() {
        $pages = get_pages();
        $pages_options = array();
        if ( $pages ) {
            foreach ($pages as $page) {
                $pages_options[$page->ID] = $page->post_title;
            }
        }

        return $pages_options;
    }

}
endif;

new GOOGLE_Map_Settings();