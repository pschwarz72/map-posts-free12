<?php

class IPTMP_Map_Posts {

	function register() {
		//Adds hook adding Map Posts config to post editor
		add_action( 'add_meta_boxes', array($this,'add_register_map_meta_box' ));

		//Hooks to function that will register and add buttons upon activation
		add_action ('init', array($this,'iptmp_register_add_buttons'));

		//Hooks to save map config upon updating a post or page
		add_action( 'save_post', array($this,'save_map_post_config'), 10, 2 );

		//Hooks insert map shortcode to initialization
		add_action('init', array($this,'insert_map_shortcode_init'));

		//Hooks js and css that needs to be enqueued - 1 for admin, 1 for public
		add_action('admin_enqueue_scripts', array($this,'include_admin_map_js_file'));
		add_action('wp_enqueue_scripts', array($this,'include_public_map_js_file'));

		//Adds map config sanitization to update_meta
		add_filter('sanitize_post_meta_iptmp_post_config',array($this,'iptmp_sanitize_map_config'));

	}

	function activate() {

		//Checks user is logged in and admin
		$usr_chk_role = $this -> iptmp_check_user_role('map_posts_admin');

		if ( is_user_logged_in() && $usr_chk_role ) {

			//Add default options to database if none already present
			$this -> iptmp_add_default_options();

			//Triggers registration and addition of buttons
			$this -> iptmp_register_add_buttons();

			//Add insert map shortcode
			$this -> insert_map_shortcode_init();
			flush_rewrite_rules();
		}
	}

	//Adds default map options to database, it they don't exist already
	private function iptmp_add_default_options() {

		//Defines default values
		$default_values = array("iptmp_map_address"=>"","iptmp_map_width"=>"75","iptmp_map_height"=>"300","iptmp_map_usermsg"=>"To update location type in an address, click on map or manually enter latitude and longitude; then save the post (Save Draft, Publish or Update).","iptmp_opencage_key"=>"b14ba325907d45e5b55be4471a9f0450","iptmp_map_zoom"=>"0","iptmp_map_lat"=>"0.00","iptmp_map_long"=>"0.00");

		//Adds plugin default options to database if no existing records present
		if ( false === get_option( 'iptmp_defaults' )) {
			add_option( 'iptmp_defaults', $default_values );
		}
	}

	//Enqueues map libraries required in admin
	function include_admin_map_js_file() {
		/*
		$path_to_css = plugins_url('/css',__FILE__);
		$path_to_js = plugins_url('/js',__FILE__);
		*/

		//Defines the path to css and js files for free and pro versions
		$relative_plugin_path = plugin_basename(__file__);
		$free_or_pro_pos = stripos($relative_plugin_path,'free',10);

		if ($free_or_pro_pos != false) {
			$path_to_css = plugins_url().'/map-posts-free/css';
			$path_to_js = plugins_url().'/map-posts-free/js';
		} else {
			$path_to_css = plugins_url().'/map-posts-pro/css';
			$path_to_js = plugins_url().'/map-posts-pro/js';
		};

		wp_enqueue_script('iptmp-leaflet-js',$path_to_js.'/leaflet.js');
		wp_enqueue_style('iptmp-leaflet-css',$path_to_css.'/leaflet.css');
		wp_enqueue_script('iptmp-preview-js',$path_to_js.'/iptmp_preview.js');
	}

	//Enqueues map libraries required in front end public website
	function include_public_map_js_file() {
		/*
		$path_to_css = plugins_url('/css',__FILE__);
		$path_to_js = plugins_url('/js',__FILE__);
		*/

		//Defines the path to css and js files for free and pro versions
		$relative_plugin_path = plugin_basename(__file__);
		$free_or_pro_pos = stripos($relative_plugin_path,'free',10);

		if ($free_or_pro_pos != false) {
			$path_to_css = plugins_url().'/map-posts-free/css';
			$path_to_js = plugins_url().'/map-posts-free/js';
		} else {
			$path_to_css = plugins_url().'/map-posts-pro/css';
			$path_to_js = plugins_url().'/map-posts-pro/js';
		};

		//Enqueues Leaflet map library js and css
		wp_enqueue_script('iptmp-leaflet-js',$path_to_js.'/leaflet.js');
		wp_enqueue_style('iptmp-leaflet-css',$path_to_css.'/leaflet.css');

	}


	//Registers meata boxes - to accomodate unknown number of custom post types, do as a loop
	function add_register_map_meta_box() {

		//Alternate code to add map configuration to only non-custom post types and pages
		/*
		add_meta_box( 'define_map_meta_box', 'Define Map', array($this,'add_define_map_meta_box'), 'post', 'normal');
		add_meta_box( 'define_map_meta_box', 'Define Map', array($this,'add_define_map_meta_box'), 'page', 'normal');
		*/

		//Adds map configuration meta box to every post type
		$post_types = get_post_types( array(), 'objects' );
		foreach ( $post_types as $post_type ) {
        add_meta_box( 'define_map_meta_box',
                      'Define Map',
                      array($this,'add_define_map_meta_box'),
                      $post_type->name, 'normal' );
		}
	}

	//Adds buttons to visual editor
	function iptmp_register_add_buttons () {
		add_filter('mce_external_plugins', array($this,'iptmp_register_tinymce_plugin'));
		add_filter('mce_buttons', array($this,'iptmp_add_tinymce_button'));

	}

	//Registers path and id of tinymce plugin js file
	function iptmp_register_tinymce_plugin($plugin_array) {
		//$iptmp_plugin_path = plugins_url('/js/iptmp_plugin.js',__FILE__);
		$iptmp_plugin_path = plugins_url().'/map-posts-pro/js/iptmp_plugin.js';
		$plugin_array['iptmp_button'] = $iptmp_plugin_path;
		return $plugin_array;
	}

	//Adds the buttons to the button array
	function iptmp_add_tinymce_button($buttons) {
		$buttons[] = "iptmp_button";
		$buttons[] = "iptmp_all_posts_button";
		return $buttons;
	}

	function add_define_map_meta_box( $post ) {

		$post_id = get_the_ID();

		//Gets map post config from post meta if exists otherwise use defaults
		if (get_post_meta( $post_id, 'iptmp_post_config', true ) != '') {

			//Assigns map config from _postmeta
			$iptmp_config_options = get_post_meta( $post_id, 'iptmp_post_config', true );

			$iptmp_map_address = $iptmp_config_options['iptmp_map_address'];
			$iptmp_map_width = $iptmp_config_options['iptmp_map_width'];
			$iptmp_map_height = $iptmp_config_options['iptmp_map_height'];
			$iptmp_map_zoom = $iptmp_config_options['iptmp_map_zoom'];
			$iptmp_map_lat = $iptmp_config_options['iptmp_map_lat'];
			$iptmp_map_long = $iptmp_config_options['iptmp_map_long'];
			$iptmp_map_usermsg = $iptmp_config_options['iptmp_map_usermsg'];
		} else {
			//Assigns map config from _options (default values)
			$iptmp_config_options = get_option( 'iptmp_defaults' );

			$iptmp_map_address = $iptmp_config_options['iptmp_map_address'];
			$iptmp_map_width = $iptmp_config_options['iptmp_map_width'];
			$iptmp_map_height = $iptmp_config_options['iptmp_map_height'];
			$iptmp_map_zoom = $iptmp_config_options['iptmp_map_zoom'];
			$iptmp_map_lat = $iptmp_config_options['iptmp_map_lat'];
			$iptmp_map_long = $iptmp_config_options['iptmp_map_long'];
			$iptmp_map_usermsg = $iptmp_config_options['iptmp_map_usermsg'];
		}

		//Writes the meta post map config HTML
		?>
		<table style="background-color:lightgray; width:60%">
			<tr>
				<td colspan="2" style="font-family:tahoma; font-size:1.4em; padding-bottom:10px; font-weight:600">Post Location</td>
			</tr>
			<tr>
				<td style="font-family:tahoma; min-width:50px;text-align:left;font-size:1.2em;font-weight:400">Address</td>
				<td style="min-width:100px;text-align:left"><input size="100" type="text" id="iptmp_address" name="iptmp_address"  maxlength="100" value="<?php echo esc_attr($iptmp_map_address); ?>"/></td>
			</tr>
			<tr>
				<td></td>
				<td style="min-width:100px; text-align:left; font-style:italic; padding-bottom:20px">123 Fake Street East, Springfield, Illinois, 10234, United States</td>
			</tr>
			<tr>
				<td style="font-family:tahoma; min-width:50px;text-align:left;font-size:1.2em;font-weight:400">Latitude</td>
				<td style="min-width:100px;text-align:left"><input type="text" id="iptmp_map_lat" name="iptmp_map_lat"  maxlength="15" value="<?php echo esc_attr($iptmp_map_lat); ?>"/></td>
			</tr>
			<tr>
				<td></td>
				<td style="min-width:100px; text-align:left; font-style:italic; padding-bottom:20px">-90.0000000000 to 90.0000000000</td>
			</tr>
			<tr>
				<td style="font-family:tahoma; min-width:50px;text-align:left;font-size:1.2em;font-weight:400">Longitude</td>
				<td style="min-width:100px;text-align:left"><input type="text" id="iptmp_map_long" name="iptmp_map_long"  maxlength="15" value="<?php echo esc_attr($iptmp_map_long); ?>"/></td>
			</tr>
			<tr>
				<td></td>
				<td style="min-width:100px; text-align:left; font-style:italic; padding-bottom:20px">-180.0000000000 to 180.0000000000</td>
			</tr>
			<tr>
				<td colspan="2" style="font-family:tahoma; font-size:1.4em; padding-bottom:10px; font-weight:600">Map Config</td>
			</tr>
			<tr>
				<td style="font-family:tahoma; min-width:50px;text-align:left;font-size:1.2em;font-weight:400">Map Width</td>
				<td style="min-width:100px;text-align:left"><input type="text" id="iptmp_map_width" name="iptmp_map_width"  maxlength="100" value="<?php echo esc_attr($iptmp_map_width); ?>"/> %</td>
			</tr>
			<tr>
				<td style="font-family:tahoma; min-width:50px;text-align:left;font-size:1.2em;font-weight:400">Map Height</td>
				<td style="min-width:100px;text-align:left"><input type="text" id="iptmp_map_height" name="iptmp_map_height"  maxlength="100" value="<?php echo esc_attr($iptmp_map_height); ?>"/> px</td>
			</tr>
			<tr>
				<td style="font-family:tahoma; min-width:50px;text-align:left;font-size:1.2em;font-weight:400;padding-right:20px">Initial Zoom</td>
				<td>
					<select id="iptmp_map_zoom" name="iptmp_map_zoom">
						<option <?php if (0 == esc_html($iptmp_map_zoom)) { echo 'selected="true"'; } ?>>0</option>
						<option <?php if (1 == esc_html($iptmp_map_zoom)) { echo 'selected="true"'; } ?>>1</option>
						<option <?php if (2 == esc_html($iptmp_map_zoom)) { echo 'selected="true"'; } ?>>2</option>
						<option <?php if (3 == esc_html($iptmp_map_zoom)) { echo 'selected="true"'; } ?>>3</option>
						<option <?php if (4 == esc_html($iptmp_map_zoom)) { echo 'selected="true"'; } ?>>4</option>
						<option <?php if (5 == esc_html($iptmp_map_zoom)) { echo 'selected="true"'; } ?>>5</option>
						<option <?php if (6 == esc_html($iptmp_map_zoom)) { echo 'selected="true"'; } ?>>6</option>
						<option <?php if (7 == esc_html($iptmp_map_zoom)) { echo 'selected="true"'; } ?>>7</option>
						<option <?php if (8 == esc_html($iptmp_map_zoom)) { echo 'selected="true"'; } ?>>8</option>
						<option <?php if (9 == esc_html($iptmp_map_zoom)) { echo 'selected="true"'; } ?>>9</option>
						<option <?php if (10 == esc_html($iptmp_map_zoom)) { echo 'selected="true"'; } ?>>10</option>
						<option <?php if (11 == esc_html($iptmp_map_zoom)) { echo 'selected="true"'; } ?>>11</option>
						<option <?php if (12 == esc_html($iptmp_map_zoom)) { echo 'selected="true"'; } ?>>12</option>
						<option <?php if (13 == esc_html($iptmp_map_zoom)) { echo 'selected="true"'; } ?>>13</option>
						<option <?php if (14 == esc_html($iptmp_map_zoom)) { echo 'selected="true"'; } ?>>14</option>
						<option <?php if (15 == esc_html($iptmp_map_zoom)) { echo 'selected="true"'; } ?>>15</option>
						<option <?php if (16 == esc_html($iptmp_map_zoom)) { echo 'selected="true"'; } ?>>16</option>
						<option <?php if (17 == esc_html($iptmp_map_zoom)) { echo 'selected="true"'; } ?>>17</option>
						<option <?php if (18 == esc_html($iptmp_map_zoom)) { echo 'selected="true"'; } ?>>18</option>
					</select>
				</td>
			</tr>
			<tr>
				<td></td>
				<td style="min-width:100px; text-align:left; font-style:italic; padding-bottom:20px">0 (World) to 18 (Max Zoom)</td>
			</tr>
			<tr>
				<!-- border: 1px solid  -->
				<td colspan="2" style="background-color: white"><span type="text" id="iptmp_map_usermsg" name="iptmp_map_usermsg"><?php echo esc_html($iptmp_map_usermsg); ?></span></td>

			</tr>
		</table>

		<!-- Nonce security field -->
		<?php wp_nonce_field( 'iptmp_nonce_action', 'iptmp_nonce_field' ); ?>

		<h2 style="font-family:tahoma; font-size=1.4em">Preview Map</h2>
		<!-- width: 600px -->
		<div id ="previewmap" style ="width: 60%; height: 300px"></div>

		<?php

	}

	function save_map_post_config ( $post_id = false,  $post = false ) {

		//Aborts if no nonce field set or nonce check fails
		if ( isset($_POST['iptmp_nonce_field']) && !wp_verify_nonce($_POST['iptmp_nonce_field'],'iptmp_nonce_action') ) {
			return;
		}

		//Checks user role and post type
		$usr_chk_role = $this -> iptmp_check_user_role('map_posts_config');
		$iptmp_post_type = $post->post_type;

		//Aborts if WP is only autosaving
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;

		// Checks if user is at least contributor
		} else if ( $usr_chk_role === true ) {

			//Check if a value is provided - which should always be the case
			if ( isset( $_POST['iptmp_map_width'] )) {

				//Gets the $_POST values from Posts Map metabox
				$iptmp_post_config = array("iptmp_map_address"=>sanitize_text_field($_POST['iptmp_address']),"iptmp_map_width"=>sanitize_text_field($_POST['iptmp_map_width']),"iptmp_map_height"=>sanitize_text_field($_POST['iptmp_map_height']),"iptmp_map_zoom"=>sanitize_text_field($_POST['iptmp_map_zoom']),"iptmp_map_lat"=>sanitize_text_field($_POST['iptmp_map_lat']),"iptmp_map_long"=>sanitize_text_field($_POST['iptmp_map_long']));

				//Gets the current Map Post configuration - either default or a custom config
				if (get_post_meta( $post_id, 'iptmp_post_config',true ) == '') {
					$iptmp_db_options = get_option( 'iptmp_defaults' );

					$iptmp_db_config['iptmp_map_address'] = $iptmp_db_options['iptmp_map_address'];
					$iptmp_db_config['iptmp_map_width']   = $iptmp_db_options['iptmp_map_width'];
					$iptmp_db_config['iptmp_map_height']  = $iptmp_db_options['iptmp_map_height'];
					$iptmp_db_config['iptmp_map_zoom']    = $iptmp_db_options['iptmp_map_zoom'];
					$iptmp_db_config['iptmp_map_lat']     = $iptmp_db_options['iptmp_map_lat'];
					$iptmp_db_config['iptmp_map_long']    = $iptmp_db_options['iptmp_map_long'];

				} else {
					$iptmp_db_postmeta = get_post_meta( $post_id, 'iptmp_post_config',true);

					$iptmp_db_config['iptmp_map_address'] = $iptmp_db_postmeta['iptmp_map_address'];
					$iptmp_db_config['iptmp_map_width']   = $iptmp_db_postmeta['iptmp_map_width'];
					$iptmp_db_config['iptmp_map_height']  = $iptmp_db_postmeta['iptmp_map_height'];
					$iptmp_db_config['iptmp_map_zoom']    = $iptmp_db_postmeta['iptmp_map_zoom'];
					$iptmp_db_config['iptmp_map_lat']     = $iptmp_db_postmeta['iptmp_map_lat'];
					$iptmp_db_config['iptmp_map_long']    = $iptmp_db_postmeta['iptmp_map_long'];
				}

				//Compares imploded arrays to detect config changes
				$iptmp_db_config_str = implode(',',$iptmp_db_config);
				$iptmp_post_config_str = implode(',',$iptmp_post_config);

				//Aborts rest of code if no changes detected
				if ($iptmp_db_config_str == $iptmp_post_config_str) {
					return;
				} else {
					update_metadata ('post',$post_id,'iptmp_post_config',$iptmp_post_config);
				}
			}
		}
	}

	//Sanitizes user map config before saving
	function iptmp_sanitize_map_config($iptmp_config_to_sanitize) {

		$post_id = get_the_ID();

		//Gets the original map config values to substitute if any input fails validation
		if (get_post_meta( $post_id, 'iptmp_post_config', true ) != '') {
			//Defaults to last saved map configuration - if any
			$iptmp_config_options = get_post_meta( $post_id, 'iptmp_post_config', true );

			$iptmp_map_address = $iptmp_config_options['iptmp_map_address'];
			$iptmp_map_width = $iptmp_config_options['iptmp_map_width'];
			$iptmp_map_height = $iptmp_config_options['iptmp_map_height'];
			$iptmp_map_zoom = $iptmp_config_options['iptmp_map_zoom'];
			$iptmp_map_lat = $iptmp_config_options['iptmp_map_lat'];
			$iptmp_map_long = $iptmp_config_options['iptmp_map_long'];
		} else {
			//Defaults to plugin defaults
			$iptmp_config_options = get_option( 'iptmp_defaults' );

			$iptmp_map_address = $iptmp_config_options['iptmp_map_address'];
			$iptmp_map_width = $iptmp_config_options['iptmp_map_width'];
			$iptmp_map_height = $iptmp_config_options['iptmp_map_height'];
			$iptmp_map_zoom = $iptmp_config_options['iptmp_map_zoom'];
			$iptmp_map_lat = $iptmp_config_options['iptmp_map_lat'];
			$iptmp_map_long = $iptmp_config_options['iptmp_map_long'];
		}

		//Defines a stem error message
		$user_msg = 'Custom configuration only partially applied - please resolve the following issues: ';
		$original_user_msg = $user_msg;

		//Gets the last saved address and address in latest post update
		$iptmp_db_address = $iptmp_config_options['iptmp_map_address'];
		$iptmp_post_address = $iptmp_config_to_sanitize['iptmp_map_address'];

		//Geocodes only if new address
		if ( $iptmp_post_address != $iptmp_db_address) {

			//Gets the OpenCage geocoding API key
			$iptmp_options = get_option( 'iptmp_defaults' );
			$iptmp_key = $iptmp_options['iptmp_opencage_key'];

			//Defines geocode request parameters
			$params = array(
			'key' => $iptmp_key,
			'q' => $iptmp_post_address,
			'language' => 'en',
			'limit' => 1,
			'no_annotations' => 1);

			//Creates parameters portion of the request URL
			$iptmp_formattedurl = http_build_query($params);

			//Saves the response from executed http request
			$iptmp_theresponse = wp_remote_get('https://api.opencagedata.com/geocode/v1/json?'.$iptmp_formattedurl);

			//Gets the response body
			$iptmp_georesponse = $iptmp_theresponse['body'];

			//Gets the status code - note that you can have 0 results and still have code 200
			$find_status_code_string = '"status":{"code":';
			$status_code_location = stripos($iptmp_georesponse, $find_status_code_string);
			$status_code_start = $status_code_location + 17;
			$status_code = substr($iptmp_georesponse,$status_code_start,3);

			if ($status_code == '200') {

				//Parses through string to obtain latitude and longitude
				$the_geometry_pos = stripos($iptmp_georesponse,'geometry');

				//Gets the latitude and longitude if geocoder returned a result
				if ($the_geometry_pos != false) {

					$lat_start_pos = $the_geometry_pos + 17;
					$lat_end_pos = stripos($iptmp_georesponse,',',$lat_start_pos);
					$lat_value_length = $lat_end_pos - $lat_start_pos;
					$geocodelat = substr($iptmp_georesponse,$lat_start_pos,$lat_value_length);

					$lng_start_pos = $lat_end_pos + 7;
					$lng_end_pos = stripos( $iptmp_georesponse,'}',$lng_start_pos);
					$lng_value_length = $lng_end_pos - $lng_start_pos;
					$geocodelng = substr($iptmp_georesponse,$lng_start_pos,$lng_value_length);

					//Updates map config to geocode results
					$iptmp_config_to_sanitize['iptmp_map_lat'] = $geocodelat;
					$iptmp_config_to_sanitize['iptmp_map_long'] = $geocodelng;

				//Handles valid response status code but no co-ordinates returned
				} else {

					//Reverts to last successfully saved address
					$iptmp_config_to_sanitize['iptmp_map_address'] = $iptmp_db_address;
					$user_msg .= " | Map Address -- $iptmp_post_address -- did not return any results. Reverting to previously saved address $iptmp_db_address".".";

				}

			//Handles geocoder response with an error status code
			} else if (substr($status_code,1,1) == '4' || substr($status_code,1,1) == '5') {

				//Reverts to last successfully saved address
				$iptmp_config_to_sanitize['iptmp_map_address'] = $iptmp_db_address;
				$user_msg .= " | An error occurred during geocoding. No results obtained for Address -- $iptmp_post_address -- . Reverting to previously saved address $iptmp_db_address".".";

			//Handles no geocoder response and any other unknown error condition
			} else {

				//Reverts to last successfully saved address
				$iptmp_config_to_sanitize['iptmp_map_address'] = $iptmp_db_address;
				$user_msg .= " | Geocoder not responding. No results obtained for Address -- $iptmp_post_address -- . Reverting to previously saved address $iptmp_db_address".".";

			}
		}

		//Validates width is numeric between 10 and 100
		$unsanitized_width = $iptmp_config_to_sanitize['iptmp_map_width'];
		if (!is_numeric($unsanitized_width)) {
			$user_msg .= " | Map Width -- $unsanitized_width -- is not a number. Reverting to previously saved value $iptmp_map_width".".";
			$iptmp_config_to_sanitize['iptmp_map_width'] = $iptmp_map_width;
		} else if ($unsanitized_width < 10 || $unsanitized_width > 100) {
			$user_msg .= " | Map Width -- $unsanitized_width -- must be a percentage value between 10 and 100. Reverting to previously saved value $iptmp_map_width".".";
			$iptmp_config_to_sanitize['iptmp_map_width'] = $iptmp_map_width;
		}

		//Validates height is numeric between 10 and 10000
		$unsanitized_height = $iptmp_config_to_sanitize['iptmp_map_height'];
		if (!is_numeric($unsanitized_height)) {
			$user_msg .= " | Map Height -- $unsanitized_height -- is not a number. Reverting to previously saved value $iptmp_map_height".".";
			$iptmp_config_to_sanitize['iptmp_map_height'] = $iptmp_map_height;
		} else if ($unsanitized_height < 10 || $unsanitized_height > 9999) {
			$user_msg .= " | Map Height -- $unsanitized_height -- must be an integer value between 10 and 10000 pixels. Reverting to previously saved value $iptmp_map_height".".";
			$iptmp_config_to_sanitize['iptmp_map_height'] = $iptmp_map_height;
		}

		//Validates latitude is numeric between -90.0 and 90.0
		$unsanitized_lat = $iptmp_config_to_sanitize['iptmp_map_lat'];
		if (!is_numeric($unsanitized_lat)) {
			$user_msg .= " | Latitude -- $unsanitized_lat -- is not a number. Reverting to previously saved value $iptmp_map_lat".".";
			$iptmp_config_to_sanitize['iptmp_map_lat'] = $iptmp_map_lat;
		} else if ($unsanitized_lat < -90.0 || $unsanitized_lat > 90.0) {
			$user_msg .= " | Latitude -- $unsanitized_lat -- must be a decimal value between -90.0 and 90.0. Reverting to previously saved value $iptmp_map_lat".".";
			$iptmp_config_to_sanitize['iptmp_map_lat'] = $iptmp_map_lat;
		}

		//Validates longitude is numeric between -180.0 and 180.0
		$unsanitized_long = $iptmp_config_to_sanitize['iptmp_map_long'];
		if (!is_numeric($unsanitized_long)) {
			$user_msg .= " | Longitude -- $unsanitized_long -- is not a number. Reverting to previously saved value $iptmp_map_long".".";
			$iptmp_config_to_sanitize['iptmp_map_long'] = $iptmp_map_long;
		} else if ($unsanitized_long < -180.0 || $unsanitized_long > 180.0) {
			$user_msg .= " | Longitude -- $unsanitized_long -- must be a decimal value between -180.0 and 180.0. Reverting to previously saved value $iptmp_map_long".".";
			$iptmp_config_to_sanitize['iptmp_map_long'] = $iptmp_map_long;
		}

		//Updates user message if no validation errors
		if ($user_msg == $original_user_msg) {
			$user_msg = 'Custom map configuration successfully applied!';
		}

		//Updates sanitized array with final user message
		$iptmp_config_to_sanitize['iptmp_map_usermsg'] = $user_msg;

		return $iptmp_config_to_sanitize;
	}

	function insert_map_shortcode_init () {
		add_shortcode('postmap', array($this,'insert_map_shortcode'));
		add_shortcode('allpostmap', array($this,'insert_all_posts_map_shortcode'));
	}

	public function iptmp_check_user_role($can_do_what) {
		/**
		 * Valide values for $can_do_what
		 *
		 * map_posts_admin
		 * map_posts_config
		 *
		 * Returns true or false depending on whether user has been assigned a role or not.
		 * $can_do_what is used to identify whether user is admin type (and can activate, deactivate
		 * and uninstall) or only post writer (and can configure or insert map)
		 *
		 */

		//Gets all user roles
		$the_user = wp_get_current_user();
		$the_user_roles = $the_user -> roles;

		//Identifies if user has role that allows action
		$the_user_has_role = false;
		$start_role = $the_user_has_role;

		if ( $can_do_what == 'map_posts_admin') {
			while (list($key,$value) = each($the_user_roles)) {
				if ($value == 'super admin' || $value == 'administrator' ) {
					$the_user_has_role = true;
				}
			}
		} else if ( $can_do_what == 'map_posts_config') {
			while (list($key,$value) = each($the_user_roles)) {
				if ($value == 'super admin' || $value == 'administrator' || $value == 'editor' || $value == 'author' || $value == 'contributor') {
					$the_user_has_role = true;
				}
			}
		}

		//Returns whether user has requisite role or not
		$end_role = $the_user_has_role;
		return $the_user_has_role;
	}

	function deactivate() {

		//Checks user is logged in and admin
		$usr_chk_role = $this -> iptmp_check_user_role('map_posts_admin');
		if ( is_user_logged_in() && $usr_chk_role ) {
			flush_rewrite_rules();
		}
	}
}