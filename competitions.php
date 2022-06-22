<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.upwork.com/freelancers/~013cb8700c27145b4e
 * @since             1.0.0
 * @package           Competitions
 *
 * @wordpress-plugin
 * Plugin Name:       Competitions
 * Plugin URI:        https://www.upwork.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.4
 * Author:            Junayed
 * Author URI:        https://www.upwork.com/freelancers/~013cb8700c27145b4e
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       competitions
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define("COMPETITIONS_VERSION", "1.0.4");
$comp_alerts = null;
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-competitions-activator.php
 */
function activate_competitions() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-competitions-activator.php';
	Competitions_Activator::activate();
}

add_action( 'init', function(){
    if ( ! current_user_can( 'manage_options' ) ) {
        add_filter('show_admin_bar', '__return_false');
    }
} );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-competitions-deactivator.php
 */
function deactivate_competitions() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-competitions-deactivator.php';
	Competitions_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_competitions' );
register_deactivation_hook( __FILE__, 'deactivate_competitions' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-competitions.php';

function comp_avatars(){
	$avatars = array();
	$images = get_option( 'comp_avatars', [] );
	if(sizeof($images) > 0){
		foreach($images as $image){
			$imagearr = array(
				"id" => base64_encode($image),
				"url" => $image
			);
			$avatars[] = $imagearr;
		}
	}
	return $avatars;
}

function get_comp_cat_list($type){
    switch ($type) {
        case 'applications':
            $applicationsOptions = [];

            $applications = get_terms( array(
                'taxonomy' => 'application_cat',
                'hide_empty' => false,
            ) );
            if($applications){
                foreach($applications as $application){
                    $term = array(
                        'term_id' => $application->term_id,
                        'term_name' => ucfirst($application->name)
                    );
                    $applicationsOptions[$application->term_id] = $term;
                }
            }

            return $applicationsOptions;
            break;
        case 'grades':
            return get_grade_list();
            break;
    }
}

function get_author_points($author_id){
	global $wpdb;
	$author_posts = $wpdb->get_results("SELECT ID FROM {$wpdb->prefix}posts WHERE post_author = '$author_id' AND post_status = 'publish'");

	$points = 0;
	if($author_posts){
		foreach($author_posts as $post){
			$points += ((get_post_meta($post->ID, 'user_project_points', true)) ? intval(get_post_meta($post->ID, 'user_project_points', true)) : 0);
		}
	}

	return $points;
}

function get_grade_list($gid = null){
    $grades = array(
        array(
            'id' => 1,
            'grade' => 'Lower Elementary (Grade 1-3)'
        ),
        array(
            'id' => 2,
            'grade' => 'Upper Elementary (Grade 4-6)'
        ),
        array(
            'id' => 3,
            'grade' => 'College (Grade 7-12)'
        )
    );

    if($gid !== null){
        $ind = array_search($gid, array_column($grades, 'id'));
        return $grades[$ind];
    }else{
        return $grades;
    }
}

function comp_terms($post_id, $taxonomy){
    $term_list = get_the_terms( $post_id, $taxonomy );
    if($term_list){
        $arr = wp_list_pluck( $term_list, 'name' );
        return implode(", ", $arr);
    }else{
        return false;
    }
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_competitions() {

	$plugin = new Competitions();
	$plugin->run();

}
run_competitions();
