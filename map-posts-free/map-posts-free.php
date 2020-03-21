<?php

/**
 * Plugin Name: Map Posts Free
 * Plugin URI: https://integratedproductivitytools.com/products/map-posts-free
 * Description: Configure and insert a map without leaving the editor. Save location as part of post metadata.
 * Version: 1.2.2
 * Author: Peter Schwarz
 * Author URI: https://integratedproductivitytools.com/about-us/
 * License: GPLv2 or later
 */


/**
 * IPT Map Posts Free is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * IPT Map Posts Free is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with IPT Map Posts Free. If not go to
 * https://integratedproductivitytools.com/products/Map-Posts-Free.htm
 * or  write to the Free Software Foundation, Inc., 51 Franklin Street,
 * Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Visual editor icons by Yusuke Kamiyamane - http://p.yusukekamiyamane.com/.
 * Licensed under a Creative Commons Attribution 3.0 License
 * http://creativecommons.org/licenses/by/3.0/.
 */


// Verifies script can only run in a WordPress installation.
if ( ! defined('ABSPATH')){
  die;
}

// Includes core Map Posts code
$path_to_includes = plugin_dir_path(__FILE__).'includes/';
require_once $path_to_includes.'map-posts-common.php';

class IPTMP_Map_Free extends IPTMP_Map_Posts {

	function register_free() {

	}
	
	//Runs additional actions upon activation specific to pro version
	function activate_version_specific() {
		//Nothing in this version
	}

	//Defines the content of the [postmap] shortcode
	function insert_map_shortcode ($atts = [], $content = null) {

		$post_id = get_the_ID();

		if (get_post_meta( $post_id, 'iptmp_post_config', true ) != '') {
			//Assigns map config from post specific metadata
			$iptmp_config_options = get_post_meta( $post_id, 'iptmp_post_config', true );

			$iptmp_map_width = $iptmp_config_options['iptmp_map_width'];
			$iptmp_map_height = $iptmp_config_options['iptmp_map_height'];
			$iptmp_map_zoom = $iptmp_config_options['iptmp_map_zoom'];
			$iptmp_map_lat = $iptmp_config_options['iptmp_map_lat'];
			$iptmp_map_long = $iptmp_config_options['iptmp_map_long'];
		} else {
			//Assigns map config from _options (default values)
			$iptmp_config_options = get_option( 'iptmp_defaults' );

			$iptmp_map_width = $iptmp_config_options['iptmp_map_width'];
			$iptmp_map_height = $iptmp_config_options['iptmp_map_height'];
			$iptmp_map_zoom = $iptmp_config_options['iptmp_map_zoom'];
			$iptmp_map_lat = $iptmp_config_options['iptmp_map_lat'];
			$iptmp_map_long = $iptmp_config_options['iptmp_map_long'];
		}

		//Concatenates dimension values with units and defines the div id
		$iptmp_map_width = $iptmp_map_width.'%';
		$iptmp_map_height = $iptmp_map_height.'px';
		$iptmp_map_id = 'map'.$post_id;

		//Escapes variables that will be used as HTML attributes
		$iptmp_map_width = esc_attr($iptmp_map_width);
		$iptmp_map_height = esc_attr($iptmp_map_height);
		$iptmp_map_id = esc_attr($iptmp_map_id);

		//Defines the map code to insert in content
		$content = <<<THEMAPCODE
		</br>
		<div id ="$iptmp_map_id" style ="width: $iptmp_map_width; height: $iptmp_map_height"></div>
		<script>
		var map = L.map("$iptmp_map_id", { center: [$iptmp_map_lat,$iptmp_map_long], zoom: $iptmp_map_zoom });

		//L.tileLayer("http://{s}.tile.osm.org/{z}/{x}/{y}.png",{attribution:'Map data © OpenStreetMap contributors'}).addTo(map);

		//OpenStreetMap maps
		var standardMap = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png',{
			attribution:'Map & Data © <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
		}).addTo(map);

		var humanMap = L.tileLayer('http://a.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png',{
			attribution:'Map & Data © <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
		});

		var standardbwMap = L.tileLayer('https://tiles.wmflabs.org/bw-mapnik/{z}/{x}/{y}.png',{
			attribution:'Map & Data © <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
		});


		//Stamen maps
		var stamentonerMap = L.tileLayer('http://tile.stamen.com/toner/{z}/{x}/{y}.png',{
			attribution:'Map © <a href="http://stamen.com">Stamen Design</a>, under <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0, </a>Data © <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
		});

		var stamenterrainMap = L.tileLayer('http://tile.stamen.com/terrain/{z}/{x}/{y}.jpg',{
			attribution:'Map © <a href="http://stamen.com">Stamen Design</a>, under <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0, </a>Data © <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
		});

		var stamencolorMap = L.tileLayer('http://tile.stamen.com/terrain/{z}/{x}/{y}.jpg',{
			attribution:'Map © <a href="http://stamen.com">Stamen Design</a>,Data © <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
		});


		//Map overlays
		var trailMap = L.tileLayer('https://tile.waymarkedtrails.org/hiking/{z}/{x}/{y}.png',{
			attribution:'Data © <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
			opacity:1.0
		});

		var topoMap = L.tileLayer('http://c.tiles.wmflabs.org/hillshading/{z}/{x}/{y}.png',{
			attribution:'Data © <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
			opacity:1.0
		});

		//Defines list of basemaps to provide as layer control parameter
		var baseLayers = {
			'Standard OpenStreetMap': standardMap,
			'Humanitarian OpenStreetMap': humanMap,
			'B&W OpenStreetMap': standardbwMap,
			'B&W High Contrast': stamentonerMap,
			'Natural Terrain': stamenterrainMap,
			'Artistic Map': stamencolorMap
		};

		//Defines list of overlays to provide as layer control parameter
		var overlays = {
			'Trails Overlay': trailMap,
			'Topographic': topoMap
		};

		//Adds the layer control and populates with layers ids
		L.control.layers(baseLayers, overlays).addTo(map);

		var iptmp_marker = L.marker([$iptmp_map_lat,$iptmp_map_long]).addTo(map);
		</script>
		</br>
THEMAPCODE;

		return $content;

	}

	//Defines the content of the [allpostmap] shortcode
	function insert_all_posts_map_shortcode ($atts = [], $content = null) {

		//Gets the postid, post type, and full postmeta and post table names
		global $wpdb;
		global $post;
		$post_id = get_the_ID();
		$this_post_type = $post -> post_type;
		$post_meta_table_name = $wpdb-> prefix.'postmeta';
		$post_table_name = $wpdb-> prefix.'posts';

		//Gets the current Map Post configuration - either default or a custom config
		if (get_post_meta( $post_id, 'iptmp_post_config',true ) == '') {
			$iptmp_config_options = get_option( 'iptmp_defaults' );
		} else {
			$iptmp_config_options = get_post_meta( $post_id, 'iptmp_post_config',true);
		}

		//Gets the default center point, initial zoom, height and width
		$iptmp_map_lat = $iptmp_config_options['iptmp_map_lat'];
		$iptmp_map_long = $iptmp_config_options['iptmp_map_long'];
		$iptmp_map_zoom = $iptmp_config_options['iptmp_map_zoom'];
		$iptmp_map_width = $iptmp_config_options['iptmp_map_width'];
		$iptmp_map_height = $iptmp_config_options['iptmp_map_height'];

		//Gets the highest meta_id value
		$query_max_meta_id = "SELECT MAX(meta_id) FROM $post_meta_table_name";
		$max_meta_id = $wpdb->get_var($query_max_meta_id);

		//Creates an array compatible with GeoJSON specification
		$iptmp_geojson_array = array(
			"type" => "FeatureCollection",
			"crs" => array(
				"type" => "name",
				"properties" => array(
					"name" => "urn:ogc:def:crs:OGC:1.3:CRS84"
				)
			),
			"features" => array()
		);

		//Initilializes counter to use in features array (element in $iptmp_geojson_array)
		$features_index = 0;

		//Adds each post location, if any, to string
		for ($current_meta_id = 1; $current_meta_id <= $max_meta_id; $current_meta_id++) {

			$meta_query = $wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE meta_id = %d",$current_meta_id);
			$meta_query_row = $wpdb->get_row($meta_query,ARRAY_A);

			if ( $meta_query_row != null) {

				//Gets the query row's meta key and value
				$the_meta_key = $meta_query_row['meta_key'];
				$the_meta_value = $meta_query_row['meta_value'];

				//Gets the query row's post type and status
				$the_post_id = $meta_query_row['post_id'];
				$the_post_info = get_post( $the_post_id,ARRAY_A );
				$the_post_status = $the_post_info['post_status'];
				$the_post_type = $the_post_info['post_type'];

				//Adds post location (if any) to location array, only for published posts of the same post type (other then type = 'page')
				if ($the_meta_key == 'iptmp_post_config' && $the_post_status == 'publish' && $the_post_type == $this_post_type && 'page' != $this_post_type ) {
					$the_map_config = unserialize($the_meta_value);
					$the_lat = $the_map_config['iptmp_map_lat'];
					$the_long = $the_map_config['iptmp_map_long'];

					//Excludes posts with default configuration
					if ($the_lat != '0.00' || $the_long != '0.00') {

						//Gets the post title associated with metadata record
						$current_title_query = $wpdb -> prepare(
							"
							SELECT post_title FROM $post_table_name WHERE ID = %d
							",
							$the_post_id
						);
						$current_post_title = $wpdb -> get_var($current_title_query);

						//Gets all categories associated with current post and adds unique names to an array
						$categories_index=0;
						$categories_array=array();
						foreach((get_the_category($the_post_id)) as $category){
							$categories_array[$categories_index] = $category->name;
							++$categories_index;
						}

						//Adds location to list of points (features)
						$iptmp_geojson_array['features'][$features_index] = array("type" => "Feature","properties" => array("Label" => "Point ".$features_index,"PostTitle" => $current_post_title,"Categories" => $categories_array),"geometry" => array("type" => "Point","coordinates" => array($the_long, $the_lat)));

						//Increments features index by 1
						++$features_index;
					}

				//Adds post location (if any) to location array, only for published posts of type = 'page'
				} else if ($the_meta_key == 'iptmp_post_config' && $the_post_status == 'publish' && 'page' == $this_post_type) {
					$the_map_config = unserialize($the_meta_value);
					$the_lat = $the_map_config['iptmp_map_lat'];
					$the_long = $the_map_config['iptmp_map_long'];

					//Excludes posts with default configuration (any post with a lat of 0.00 or long of 0.00
					if ($the_lat != '0.00' || $the_long != '0.00') {

						//Gets post id related to current postmeta record
						$current_post_id_query = $wpdb -> prepare(
							"
							SELECT post_id FROM $post_meta_table_name WHERE meta_id = %d
							",
							$current_meta_id
						);
						$current_post_id = $wpdb -> get_var($current_post_id_query);

						//Gets the post title
						$current_title_query = $wpdb -> prepare(
							"
							SELECT post_title FROM $post_table_name WHERE ID = %d
							",
							$current_post_id
						);
						$current_post_title = $wpdb -> get_var($current_title_query);

						//Adds location to list of points (as geojson features)
						$iptmp_geojson_array['features'][$features_index] = array("type" => "Feature","properties" => array("Label" => "Point ".$features_index,"PostTitle" => $current_post_title),"geometry" => array("type" => "Point","coordinates" => array($the_long, $the_lat)));

						//Increments features index by 1
						++$features_index;

					}
				}
			}
		}

		//Creates a geojson from $iptmp_geojson_array
		$iptmp_geojson = json_encode($iptmp_geojson_array);

		//Concatenates dimension values with units and defines the div id
		$iptmp_map_width = $iptmp_map_width.'%';
		$iptmp_map_height = $iptmp_map_height.'px';
		$iptmp_map_id = 'allpostsmap'.$post_id;

		//Escapes variables that will be used as HTML attributes
		$iptmp_map_width = esc_attr($iptmp_map_width);
		$iptmp_map_height = esc_attr($iptmp_map_height);
		$iptmp_map_id = esc_attr($iptmp_map_id);

		//Defines the map code to insert in content
		$all_post_content = <<<ALLPOSTSMAP

		<div id ="$iptmp_map_id" style ="width: $iptmp_map_width; height: $iptmp_map_height">

		</div>
		<script>

		var all_posts_map = L.map("$iptmp_map_id", { center: [$iptmp_map_lat,$iptmp_map_long], zoom: $iptmp_map_zoom });

		//Adding a geojson layer
		var locations_as_geojson = $iptmp_geojson;
		var locations_geojson = L.geoJSON(locations_as_geojson).addTo(all_posts_map);

		//OpenStreetMap maps
		var standardMap = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png',{
			attribution:'Map & Data © <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
		}).addTo(all_posts_map);

		var humanMap = L.tileLayer('http://a.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png',{
			attribution:'Map & Data © <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
		});

		var standardbwMap = L.tileLayer('https://tiles.wmflabs.org/bw-mapnik/{z}/{x}/{y}.png',{
			attribution:'Map & Data © <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
		});


		//Stamen maps
		var stamentonerMap = L.tileLayer('http://tile.stamen.com/toner/{z}/{x}/{y}.png',{
			attribution:'Map © <a href="http://stamen.com">Stamen Design</a>, under <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0, </a>Data © <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
		});

		var stamenterrainMap = L.tileLayer('http://tile.stamen.com/terrain/{z}/{x}/{y}.jpg',{
			attribution:'Map © <a href="http://stamen.com">Stamen Design</a>, under <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0, </a>Data © <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
		});

		var stamencolorMap = L.tileLayer('http://tile.stamen.com/terrain/{z}/{x}/{y}.jpg',{
			attribution:'Map © <a href="http://stamen.com">Stamen Design</a>,Data © <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
		});


		//Map overlays
		var trailMap = L.tileLayer('https://tile.waymarkedtrails.org/hiking/{z}/{x}/{y}.png',{
			attribution:'Data © <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
			opacity:1.0
		});

		var topoMap = L.tileLayer('http://c.tiles.wmflabs.org/hillshading/{z}/{x}/{y}.png',{
			attribution:'Data © <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
			opacity:1.0
		});

		//Defines list of basemaps to provide as layer control parameter
		var baseLayers = {
			'Standard OpenStreetMap': standardMap,
			'Humanitarian OpenStreetMap': humanMap,
			'B&W OpenStreetMap': standardbwMap,
			'B&W High Contrast': stamentonerMap,
			'Natural Terrain': stamenterrainMap,
			'Artistic Map': stamencolorMap
		};

		//Defines list of overlays to provide as layer control parameter
		var overlays = {
			'Trails Overlay': trailMap,
			'Topographic': topoMap,
			'Post Locations': locations_geojson
		};

		//Adds the layer control and populates with layers ids
		L.control.layers(baseLayers, overlays).addTo(all_posts_map);

		</script>
		</br>

ALLPOSTSMAP;

		return $all_post_content;
	}
}

if (class_exists('IPTMP_Map_Posts')){
  $iptmp_free_class_instance = new IPTMP_Map_Free();
  $iptmp_free_class_instance -> register_free();
  $iptmp_free_class_instance -> register();
}


// Registers activation hook

register_activation_hook(__FILE__, array( $iptmp_free_class_instance,'activate'));

// Registers deactivation hook

register_deactivation_hook(__FILE__, array( $iptmp_free_class_instance,'deactivate'));