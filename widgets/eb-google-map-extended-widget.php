<?php
namespace ElementorGoogleMapExtended\Widgets;
//namespace Elementor;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Text_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class GOOGLE_Map_Extended extends Widget_Base {

	/**
	 * Retrieve heading widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'eb-google-map-extended';
	}

	/**
	 * Retrieve heading widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Elementor Multiple Map Marker', 'google-map-multiple-marker' );
	}

	/**
	 * Retrieve heading widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-google-maps';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'eb-elementor-extended' ];
	}

	public function get_script_depends() {
		return [ 'eb-google-maps-api', 'eb-google-map' ];
	}


	/**
	 * Register google maps widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_map',
			[
				'label' => __( 'Map', 'google-map-multiple-marker' ),
			]
		);

		/**
		 * InternetCSS is the first who came up with this idea for Seaching Latitude & Longitude right inside Elementor Widget. If you wish to use, please credit and link us back.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return string Copyright.
		 */
		$this->add_control(
		    'map_notice',
		    [
			    'label' => __( 'Find Latitude & Longitude', 'google-map-multiple-marker' ),
			    'type'  => Controls_Manager::RAW_HTML,
			    'raw'   => '<form onsubmit="ebMapFindAddress(this);" action="javascript:void(0);"><input type="text" id="eb-map-find-address" class="eb-map-find-address" style="margin-top:10px; margin-bottom:10px;"><input type="submit" value="Search" class="elementor-button elementor-button-default" onclick="ebMapFindAddress(this)"></form><div id="eb-output-result" class="eb-output-result" style="margin-top:10px; line-height: 1.3; font-size: 12px;"></div>',
			    'label_block' => true,
		    ]
	    );

		$this->add_control(
			'map_lat',
			[
				'label' => __( 'Latitude', 'google-map-multiple-marker' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => '1.2833754',
				'default' => '1.2833754',
				'dynamic' => [ 'active' => true ]
			]
		);

		$this->add_control(
			'map_lng',
			[
				'label' => __( 'Longitude', 'google-map-multiple-marker' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => '103.86072639999998',
				'default' => '103.86072639999998',
				'separator' => true,
				'dynamic' => [ 'active' => true ]
			]
		);

		$this->add_control(
			'zoom',
			[
				'label' => __( 'Zoom Level', 'google-map-multiple-marker' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 15,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 25,
					],
				],
			]
		);

		$this->add_responsive_control(
			'height',
			[
				'label' => __( 'Height', 'google-map-multiple-marker' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'default'   => [
					'size' => 300,
				],
				'range' => [
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
					'px' => [
						'min' => 40,
						'max' => 1440,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eb-map' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'map_type',
			[
				'label' => __( 'Map Type', 'google-map-multiple-marker' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'roadmap' => __( 'Road Map', 'google-map-multiple-marker' ),
					'satellite' => __( 'Satellite', 'google-map-multiple-marker' ),
					'hybrid' => __( 'Hybrid', 'google-map-multiple-marker' ),
					'terrain' => __( 'Terrain', 'google-map-multiple-marker' ),
				],
				'default' => 'roadmap',
			]
		);

		$this->add_control(
			'gesture_handling',
			[
				'label' => __( 'Gesture Handling', 'google-map-multiple-marker' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'auto' => __( 'Auto (Default)', 'google-map-multiple-marker' ),
					'cooperative' => __( 'Cooperative', 'google-map-multiple-marker' ),
					'greedy' => __( 'Greedy', 'google-map-multiple-marker' ),
					'none' => __( 'None', 'google-map-multiple-marker' ),
				],
				'default' => 'auto',
				'description' => __('Understand more about Gesture Handling by reading it <a href="https://developers.google.com/maps/documentation/javascript/reference/3/#MapOptions" target="_blank">here.</a> Basically it control how it handles gestures on the map. Used to be draggable and scroll wheel function which is deprecated.'),
			]
		);


		/*$this->add_control(
			'scroll_wheel',
			[
				'label' => __( 'Scroll Wheel', 'google-map-multiple-marker' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no'
			]
		);*/

		$this->add_control(
			'zoom_control',
			[
				'label' => __( 'Show Zoom Control', 'google-map-multiple-marker' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes'
			]
		);

		$this->add_control(
			'zoom_control_position',
			[
				'label' => __( 'Control Position', 'google-map-multiple-marker' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'RIGHT_BOTTOM' => __( 'Bottom Right (Default)', 'google-map-multiple-marker' ),
					'TOP_LEFT' => __( 'Top Left', 'google-map-multiple-marker' ),
					'TOP_CENTER' => __( 'Top Center', 'google-map-multiple-marker' ),
					'TOP_RIGHT' => __( 'Top Right', 'google-map-multiple-marker' ),
					'LEFT_CENTER' => __( 'Left Center', 'google-map-multiple-marker' ),
					'RIGHT_CENTER' => __( 'Right Center', 'google-map-multiple-marker' ),
					'BOTTOM_LEFT' => __( 'Bottom Left', 'google-map-multiple-marker' ),
					'BOTTOM_CENTER' => __( 'Bottom Center', 'google-map-multiple-marker' ),
					'BOTTOM_RIGHT' => __( 'Bottom Right', 'google-map-multiple-marker' ),
				],
				'default' => 'RIGHT_BOTTOM',
				'condition' => [
					'zoom_control' => 'yes',
				],
				'separator' => false,
			]
		);

		$this->add_control(
			'default_ui',
			[
				'label' => __( 'Show Default UI', 'google-map-multiple-marker' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes'
			]
		);

		$this->add_control(
			'map_type_control',
			[
				'label' => __( 'Map Type Control', 'google-map-multiple-marker' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes'
			]
		);

		$this->add_control(
			'map_type_control_style',
			[
				'label' => __( 'Control Styles', 'google-map-multiple-marker' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'DEFAULT' => __( 'Default', 'google-map-multiple-marker' ),
					'HORIZONTAL_BAR' => __( 'Horizontal Bar', 'google-map-multiple-marker' ),
					'DROPDOWN_MENU' => __( 'Dropdown Menu', 'google-map-multiple-marker' ),
				],
				'default' => 'DEFAULT',
				'condition' => [
					'map_type_control' => 'yes',
				],
				'separator' => false,
			]
		);

		$this->add_control(
			'map_type_control_position',
			[
				'label' => __( 'Control Position', 'google-map-multiple-marker' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'TOP_LEFT' => __( 'Top Left (Default)', 'google-map-multiple-marker' ),
					'TOP_CENTER' => __( 'Top Center', 'google-map-multiple-marker' ),
					'TOP_RIGHT' => __( 'Top Right', 'google-map-multiple-marker' ),
					'LEFT_CENTER' => __( 'Left Center', 'google-map-multiple-marker' ),
					'RIGHT_CENTER' => __( 'Right Center', 'google-map-multiple-marker' ),
					'BOTTOM_LEFT' => __( 'Bottom Left', 'google-map-multiple-marker' ),
					'BOTTOM_CENTER' => __( 'Bottom Center', 'google-map-multiple-marker' ),
					'BOTTOM_RIGHT' => __( 'Bottom Right', 'google-map-multiple-marker' ),
				],
				'default' => 'TOP_LEFT',
				'condition' => [
					'map_type_control' => 'yes',
				],
				'separator' => false,
			]
		);

		$this->add_control(
			'streetview_control',
			[
				'label' => __( 'Show Streetview Control', 'google-map-multiple-marker' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no'
			]
		);

		$this->add_control(
			'streetview_control_position',
			[
				'label' => __( 'Streetview Position', 'google-map-multiple-marker' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'RIGHT_BOTTOM' => __( 'Bottom Right (Default)', 'google-map-multiple-marker' ),
					'TOP_LEFT' => __( 'Top Left', 'google-map-multiple-marker' ),
					'TOP_CENTER' => __( 'Top Center', 'google-map-multiple-marker' ),
					'TOP_RIGHT' => __( 'Top Right', 'google-map-multiple-marker' ),
					'LEFT_CENTER' => __( 'Left Center', 'google-map-multiple-marker' ),
					'RIGHT_CENTER' => __( 'Right Center', 'google-map-multiple-marker' ),
					'BOTTOM_LEFT' => __( 'Bottom Left', 'google-map-multiple-marker' ),
					'BOTTOM_CENTER' => __( 'Bottom Center', 'google-map-multiple-marker' ),
					'BOTTOM_RIGHT' => __( 'Bottom Right', 'google-map-multiple-marker' ),
				],
				'default' => 'RIGHT_BOTTOM',
				'condition' => [
					'streetview_control' => 'yes',
				],
				'separator' => false,
			]
		);

		$this->add_control(
			'custom_map_style',
			[
				'label' => __( 'Custom Map Style', 'google-map-multiple-marker' ),
				'type' => Controls_Manager::TEXTAREA,
				'description' => __('Add style from <a href="https://mapstyle.withgoogle.com/" target="_blank">Google Map Styling Wizard</a> or <a href="https://snazzymaps.com/explore" target="_blank">Snazzy Maps</a>. Copy and Paste the style in the textarea.'),
				'condition' => [
					'map_type' => 'roadmap',
				],
			]
		);

		$this->add_control(
			'view',
			[
				'label' => __( 'View', 'google-map-multiple-marker' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'traditional',
			]
		);

		$this->end_controls_section();

		/*Pins Option*/
		$this->start_controls_section(
			'map_marker_pin',
			[
				'label' => __( 'Marker Pins', 'google-map-multiple-marker' ),
			]
		);

		$this->add_control(
			'infowindow_max_width',
			[
				'label' => __( 'InfoWindow Max Width', 'google-map-multiple-marker' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => '250',
				'default' => '250',
			]
		);

		$this->add_control(
			'tabs',
			[
				'label' => __( 'Pin Item', 'google-map-multiple-marker' ),
				'type' => Controls_Manager::REPEATER,
				'default' => [
					[
						'pin_title' => __( 'Pin #1', 'google-map-multiple-marker' ),
						'pin_notice' => __( 'Find Latitude & Longitude', 'google-map-multiple-marker' ),
						'pin_lat' => __( '1.2833754', 'google-map-multiple-marker' ),
						'pin_lng' => __( '103.86072639999998', 'google-map-multiple-marker' ),
						'pin_content' => __( 'I am item content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'google-map-multiple-marker' ),
					],
				], 
				'fields' => [
				    [
				    	'name' => 'pin_notice',
				    	'label' => __( 'Find Latitude & Longitude', 'google-map-multiple-marker' ),
					    'type'  => Controls_Manager::RAW_HTML,
					    'raw'   => '<form onsubmit="ebMapFindPinAddress(this);" action="javascript:void(0);"><input type="text" id="eb-map-find-address" class="eb-map-find-address" style="margin-top:10px; margin-bottom:10px;"><input type="submit" value="Search" class="elementor-button elementor-button-default" onclick="ebMapFindPinAddress(this)"></form><div id="eb-output-result" class="eb-output-result" style="margin-top:10px; line-height: 1.3; font-size: 12px;"></div>',
					    'label_block' => true,
				    ],
					[
						'name' => 'pin_lat',
						'label' => __( 'Latitude', 'google-map-multiple-marker' ),
						'type' => Controls_Manager::TEXT,
						'dynamic' => [ 'active' => true ]
					],
					[
						'name' => 'pin_lng',
						'label' => __( 'Longitude', 'google-map-multiple-marker' ),
						'type' => Controls_Manager::TEXT,
						'dynamic' => [ 'active' => true ]
					],
					[
						'name' => 'pin_icon',
						'label' => __( 'Marker Icon', 'google-map-multiple-marker' ),
						'type' => Controls_Manager::SELECT,
						'options' => [
							'' => __( 'Default (Google)', 'google-map-multiple-marker' ),
							'red' => __( 'Red', 'google-map-multiple-marker' ),
							'blue' => __( 'Blue', 'google-map-multiple-marker' ),
							'yellow' => __( 'Yellow', 'google-map-multiple-marker' ),
							'purple' => __( 'Purple', 'google-map-multiple-marker' ),
							'green' => __( 'Green', 'google-map-multiple-marker' ),
							'orange' => __( 'Orange', 'google-map-multiple-marker' ),
							'grey' => __( 'Grey', 'google-map-multiple-marker' ),
							'white' => __( 'White', 'google-map-multiple-marker' ),
							'black' => __( 'Black', 'google-map-multiple-marker' ),
						],
						'default' => '',
					],
					[
						'name' => 'pin_title',
						'label' => __( 'Title', 'google-map-multiple-marker' ),
						'type' => Controls_Manager::TEXT,
						'default' => __( 'Pin Title' , 'google-map-multiple-marker' ),
						'label_block' => true,
						'dynamic' => [ 'active' => true ]
					],
					[
						'name' => 'pin_content',
						'label' => __( 'Content', 'google-map-multiple-marker' ),
						'type' => Controls_Manager::WYSIWYG,
						'default' => __( 'Pin Content', 'google-map-multiple-marker' ),
						'dynamic' => [ 'active' => true ]
					],
				],
				'title_field' => '{{{ pin_title }}}',
			]
		);

		$this->end_controls_section();

		/*Main Style*/
		$this->start_controls_section(
			'section_main_style',
			[
				'label' => __( 'Pin Global Styles', 'google-map-multiple-marker' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'pin_header_color',
			[
				'label' => __( 'Title Color', 'google-map-multiple-marker' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eb-map-container h6' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Title Typography', 'google-map-multiple-marker' ),
				'name' => 'pin_header_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .eb-map-container h6',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'label' => __( 'Title Text Shadow', 'google-map-multiple-marker' ),
				'name' => 'pin_header_text_shadow',
				'selector' => '{{WRAPPER}} .eb-map-container h6',
				'separator' => true,
			]
		);


		$this->add_control(
			'pin_content_color',
			[
				'label' => __( 'Content Color', 'google-map-multiple-marker' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eb-map-container .eb-map-content' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Content Typography', 'google-map-multiple-marker' ),
				'name' => 'pin_content_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .eb-map-container .eb-map-content',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'label' => __( 'Content Text Shadow', 'google-map-multiple-marker' ),
				'name' => 'pin_content_text_shadow',
				'selector' => '{{WRAPPER}} .eb-map-container .eb-map-content',
				'separator' => true,
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => __( 'Alignment', 'google-map-multiple-marker' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'google-map-multiple-marker' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'google-map-multiple-marker' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'google-map-multiple-marker' ),
						'icon' => 'fa fa-align-right',
					],
					'justify' => [
						'title' => __( 'Justified', 'google-map-multiple-marker' ),
						'icon' => 'fa fa-align-justify',
					],
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .eb-map-container' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render google maps widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _content_template() {
	}

	/**
	 * Render google maps widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$eb_map_styles = $settings['custom_map_style'];
		$eb_replace_code_content = strip_tags($eb_map_styles);
        $eb_new_replace_code_content = preg_replace('/\s/', '', $eb_replace_code_content);

		if ( 0 === absint( $settings['zoom']['size'] ) ) {
			$settings['zoom']['size'] = 10;
		}

		$this->add_render_attribute('google-map-multiple-marker', 'data-eb-map-style', $eb_new_replace_code_content);

		$mapmarkers = array();

		foreach ( $settings['tabs'] as $index => $item ) :
			$mapmarkers[] = array(
				'lat' => $item['pin_lat'],
				'lng' => $item['pin_lng'],
				'title' => htmlspecialchars($item['pin_title'], ENT_QUOTES & ~ENT_COMPAT ),
				'content' => htmlspecialchars($item['pin_content'], ENT_QUOTES & ~ENT_COMPAT ),
				'pin_icon' => $item['pin_icon']
			);
		endforeach; 

		?>
		<div id="eb-map-<?php echo esc_attr($this->get_id()); ?>" class="eb-map" data-eb-map-gesture-handling="<?php echo $settings['gesture_handling'] ?>" <?php if ( 'yes' == $settings['zoom_control'] ) { ?> data-eb-map-zoom-control="true" data-eb-map-zoom-control-position="<?php echo $settings['zoom_control_position']; ?>" <?php } else { ?> data-eb-map-zoom-control="false"<?php } ?> data-eb-map-defaultui="<?php if ( 'yes' == $settings['default_ui'] ) { ?>false<?php } else { ?>true<?php } ?>" <?php echo $this->get_render_attribute_string('google-map-multiple-marker'); ?> data-eb-map-type="<?php echo $settings['map_type']; ?>" <?php if ( 'yes' == $settings['map_type_control'] ) { ?> data-eb-map-type-control="true" data-eb-map-type-control-style="<?php echo $settings['map_type_control_style']; ?>" data-eb-map-type-control-position="<?php echo $settings['map_type_control_position']; ?>"<?php } else { ?> data-eb-map-type-control="false"<?php } ?> <?php if ( 'yes' == $settings['streetview_control'] ) { ?> data-eb-map-streetview-control="true" data-eb-map-streetview-position="<?php echo $settings['streetview_control_position']; ?>"<?php } else {?> data-eb-map-streetview-control="false"<?php } ?> data-eb-map-lat="<?php echo $settings['map_lat']; ?>" data-eb-map-lng="<?php echo $settings['map_lng']; ?>" data-eb-map-zoom="<?php echo $settings['zoom']['size']; ?>" data-eb-map-infowindow-width="<?php echo $settings['infowindow_max_width']; ?>" data-eb-locations='<?php echo json_encode($mapmarkers);?>'></div>

	<?php }
}