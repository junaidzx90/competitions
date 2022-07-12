<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.upwork.com/freelancers/~013cb8700c27145b4e
 * @since      1.0.0
 *
 * @package    Competitions
 * @subpackage Competitions/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Competitions
 * @subpackage Competitions/public
 * @author     Junayed <admin@easeare.com>
 */
class Competitions_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Competitions_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Competitions_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		global $post;
		if(is_a( $post, 'WP_Post' )){
			if(get_post_type( $post ) === 'project'){
				wp_enqueue_style( 'fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css', array(), $this->version, 'all' );
				wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/projects.css', array(), $this->version, 'all' );
			}
			
			wp_enqueue_style( 'leaderboard', plugin_dir_url( __FILE__ ) . 'css/leaderboard.css', array(), $this->version, 'all' );

			if(get_post_type( $post ) === 'competitions'){
				wp_enqueue_style( 'filters', plugin_dir_url( __FILE__ ) . 'css/filters.css', array(), $this->version, 'all' );
				wp_enqueue_style( 'jbox', plugin_dir_url( __FILE__ ) . 'css/jbox.min.css', array(), $this->version, 'all' );
				wp_enqueue_style( 'fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css', array(), $this->version, 'all' );
				wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/competitions.css', array(), $this->version, 'all' );
			}

			if ( get_page_template_slug() === 'udashboard' ) {
				wp_enqueue_style( 'fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css', array(), $this->version, 'all' );
				wp_enqueue_style( 'filters', plugin_dir_url( __FILE__ ) . 'css/filters.css', array(), $this->version, 'all' );
				wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/profile.css', array(), $this->version, 'all' );
				wp_enqueue_style( 'multiselect', plugin_dir_url( __FILE__ ) . 'css/multiselect.min.css', array(), $this->version, 'all' );
			}
		}
		
	}

	function competition_wp_head(){
		global $post;
		if(is_a( $post, 'WP_Post' )){
			if(get_post_type( $post ) === 'project' || get_post_type( $post ) === 'competitions' || get_page_template_slug() === 'udashboard'){
				?>
				<style>
					body {
						background-image: url("<?php echo get_template_directory_uri() ?>/assets/img/bg-texture-01.jpg") !important;
					}
				</style>
				<?php
			}
		}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Competitions_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Competitions_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		
		wp_enqueue_script('jquery');
		wp_register_script( 'jbox', plugin_dir_url( __FILE__ ) . 'js/jbox.min.js', array( 'jquery' ), $this->version, false );
		wp_register_script( 'jquery-form', plugin_dir_url( __FILE__ ) . 'js/jquery.form.min.js', array(  ), $this->version, false );
		wp_register_script( 'submit.pro', plugin_dir_url( __FILE__ ) . 'js/submit.pro.js', array( 'jquery' ), $this->version, false );

		wp_localize_script('submit.pro', "ajax_data", array(
			'ajaxurl' 		=> admin_url('admin-ajax.php'),
			'nonce' 		=> wp_create_nonce('ajax-nonce'),
			'max_upload'	=> wp_max_upload_size()
		));

		wp_register_script( 'comp_vue', plugin_dir_url( __FILE__ ) . 'js/vue.min.js', array(  ), $this->version, false );
		wp_register_script( 'competitions', plugin_dir_url( __FILE__ ) . 'js/competitions-public.js', array( 'jquery', 'comp_vue' ), $this->version, true );
		wp_localize_script('competitions', "ajax_data", array(
			'ajaxurl' 		=> admin_url('admin-ajax.php'),
			'nonce' 		=> wp_create_nonce('ajax-nonce'),
		));

		wp_register_script( 'profile', plugin_dir_url( __FILE__ ) . 'js/profile.js', array( 'jquery' ), $this->version, false );
		wp_register_script( 'multiselect', plugin_dir_url( __FILE__ ) . 'js/multiselect.min.js', array( 'comp_vue' ), $this->version, false );

	}

	public function competition_pages($templates){ 
        $templates['udashboard'] = 'Competition profile';
        return $templates;
    }

	// Add page states
	function competition_profile_page_states( $post_states, $post ) {

		if(get_page_template_slug() === 'udashboard'){
			$post_states[] = 'Competition';
		}
	
		return $post_states;
	}

	// Include competitions archive page
	function competitions_templates( $template ) {
		// For profile page
		if(get_page_template_slug() === 'udashboard'){
			$theme_files = array('user-profile.php', plugin_dir_path( __FILE__ ).'partials/user-profile.php');
			$exists_in_theme = locate_template($theme_files, false);
			if ( $exists_in_theme != '' ) {
				$template = $exists_in_theme;
			} else {
				$template = plugin_dir_path( __FILE__ ). 'partials/user-profile.php';
			}
		}

		if ( is_post_type_archive( 'competitions' ) ) {
			$theme_files = array('archive-comptitions.php', plugin_dir_path( __FILE__ ).'partials/archive-comptitions.php');
			$exists_in_theme = locate_template($theme_files, false);
			if ( $exists_in_theme != '' ) {
				$template = $exists_in_theme;
			} else {
				$template = plugin_dir_path( __FILE__ ). 'partials/archive-comptitions.php';
			}
		}

		if ( is_singular( 'competitions' )) {
			$theme_files = array('single-competition.php', plugin_dir_path( __FILE__ ).'partials/single-competition.php');
			$exists_in_theme = locate_template($theme_files, false);
			if ( $exists_in_theme != '' ) {
				$template = $exists_in_theme;
			} else {
				$template = plugin_dir_path( __FILE__ ). 'partials/single-competition.php';
			}
		}

		if ( is_singular( 'project' )) {
			$theme_files = array('single-project.php', plugin_dir_path( __FILE__ ).'partials/single-project.php');
			$exists_in_theme = locate_template($theme_files, false);
			if ( $exists_in_theme != '' ) {
				$template = $exists_in_theme;
			} else {
				$template = plugin_dir_path( __FILE__ ). 'partials/single-project.php';
			}
		}

		if ($template == '') {
			throw new \Exception('No template found');
		}

		return $template;
	}

	function get_competition_results(){
		global $wpdb;
		if(!wp_verify_nonce( $_GET['nonce'], 'ajax-nonce' )){
			die("Invalid Request!");
		}
		
		$page = 1;
		if(isset($_GET['page'])){
			$page = intval($_GET['page']);
		}

		$perpage = ((get_option('comp_perpage')) ? intval(get_option('comp_perpage')) : 12);

		$args = array(
			'post_type' => 'competitions',
			'post_status' => 'publish',
			'posts_per_page' => $perpage,
			'paged' => $page,
    		'orderby' => 'id',
			'order' => 'DESC',
		);

		if(isset($_GET['application_filter']) && $_GET['application_filter'] !== ''){
			$application_id = intval($_GET['application_filter']);
			if(!empty($application_id)){
				$args['tax_query'] = array(
					array(
						'taxonomy' => 'application_cat',
						'terms' => $application_id,
						'include_children' => false
					)
				);
			}
		}

		if(isset($_GET['grade_filter']) && $_GET['grade_filter'] !== ''){
			$grade = intval($_GET['grade_filter']);
			$args['meta_query'][] = array(
				'key' => 'comp__grade',
				'value' => $grade,
				'compare' => '=='
			);
		}

		if(isset($_GET['date_filter']) && !empty($_GET['date_filter'])){
			$args['meta_query'][] = array(
				'key' => 'competition_active_date',
				'value' => $_GET['date_filter'],
				'compare' => 'LIKE'
			);
		}

		$defaultZone = wp_timezone_string();
		if($defaultZone){
			date_default_timezone_set($defaultZone);
		}

		$competitions = array();
		$competitionsObj = new WP_Query( $args );
		if ( $competitionsObj->have_posts() ){
			while ( $competitionsObj->have_posts() ){
				$competitionsObj->the_post();
				$active_date = get_post_meta(get_post()->ID, 'competition_active_date', true );
				$competition_activate = strtotime(date("Y-m")) <= strtotime($active_date);
				$pid = get_post()->ID;
				$projects = $wpdb->query("SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_value = $pid AND meta_key = 'project_competition'");
				$gradeId = get_post_meta(get_post()->ID, 'comp__grade', true);
				$grade = false;
				if($gradeId){
					$grade = get_grade_list($gradeId)['grade'];
				}

				$competition = array(
					'id' => get_post()->ID,
					'title' => get_the_title( get_post() ),
					'date' => date("F, Y", strtotime($active_date)),
					'running' => $competition_activate,
					'projects' => $projects,
					'applications' => comp_terms( get_post(), 'application_cat' ),
					'grade' => $grade,
					'age' => get_post_meta(get_post()->ID, 'comp__age', true),
					'permalink' => get_the_permalink( get_post() )
				);

				$competitions[] = $competition;
			}
		}

		// Dates
		$args = array(
			'post_type' => 'competitions',
			'post_status' => 'publish',
			'numberposts' => -1,
			'fields' => 'ids',
		);

		$dates = [];
		$competitionsIds = get_posts($args);
		if($competitionsIds){
			foreach($competitionsIds as $compId){
				$date = get_post_meta($compId, 'competition_active_date', true);
				$date1 = date("F, Y", strtotime($date));
				$date1 = explode(", ", $date1);
				$dateArr = array(
					'id' => $compId,
					'year' => $date1[1],
					'month' => $date1[0],
					'value' => $date
				);

				$dates[$date] = $dateArr;
			}
		}

		$dates = array_values($dates);

		function date_compare($a, $b){
			$t1 = strtotime($a['value']);
			$t2 = strtotime($b['value']);
			return $t1 - $t2;
		}
		usort($dates, 'date_compare');

		echo json_encode(array(
			"competitions" => $competitions, 
			"dates" => $dates, 
			'maxpages' => $competitionsObj->max_num_pages
		));
		die;
	}

	function get_projects_results(){
		if(!wp_verify_nonce( $_GET['nonce'], 'ajax-nonce' )){
			die("Invalid Request!");
		}
		
		if(!isset($_GET['author'])){
			return;
		}

		$author = intval($_GET['author']);

		$page = 1;
		if(isset($_GET['page'])){
			$page = intval($_GET['page']);
		}

		$perpage = ((get_option('projects_perpage')) ? intval(get_option('projects_perpage')) : 12);

		$args = array(
			'post_type' => 'project',
			'post_status' => 'publish',
			'author__in' => [$author],
			'posts_per_page' => $perpage,
			'paged' => $page,
    		'orderby' => 'id',
			'order' => 'DESC',
		);

		if(isset($_GET['application_filter']) && $_GET['application_filter'] !== ''){
			$application_id = intval($_GET['application_filter']);
			if(!empty($application_id)){
				$args['tax_query'] = array(
					array(
						'taxonomy' => 'application_cat',
						'terms' => $application_id,
						'include_children' => false
					)
				);
			}
		}

		if(isset($_GET['grade_filter']) && $_GET['grade_filter'] !== ''){
			$grade_id = intval($_GET['grade_filter']);
			
			$args['meta_query'][] = array(
				'key' => 'project__grade',
				'value' => $grade_id,
				'compare' => '=='
			);
		}
		
		if(isset($_GET['date_filter']) && !empty($_GET['date_filter'])){
			$date = $_GET['date_filter'];
			$date = date("m, Y", strtotime($date));
			$date1 = explode(", ", $date);

			$args['date_query'][] = array(
				'year'  => $date1[1],
				'month' => $date1[0]
			);
		}

		$projects = array();
		$projectsObj = new WP_Query( $args );
		if ( $projectsObj->have_posts() ){
			while ( $projectsObj->have_posts() ){
				$projectsObj->the_post();
				
				$compId = intval(get_post_meta(get_post()->ID, "project_competition", true));
				$competition = array(
					'id' => get_post()->ID,
					'title' => get_the_title( get_post() ),
					'thumbnail' => get_the_post_thumbnail_url( get_post(), '250' ),
					'competition_title' => (($compId) ? get_the_title( $compId ) : "Invalid project"),
					'competition_link' => (($compId) ? get_the_permalink($compId) : "#"),
					'date' => get_the_date("F j, Y", get_post()),
					'permalink' => get_the_permalink( get_post() )
				);

				$projects[] = $competition;
			}
		}

		// Dates
		$args = array(
			'post_type' => 'project',
			'post_status' => 'publish',
			'numberposts' => -1,
			'fields' => 'ids',
		);

		$dates = [];
		$projectsIds = get_posts($args);
		if($projectsIds){
			foreach($projectsIds as $proId){
				$date = get_the_date("F, Y", $proId);
				$date1 = explode(", ", $date);
				$dateArr = array(
					'id' => $proId,
					'year' => $date1[1],
					'month' => $date1[0],
					'value' => $date
				);

				$dates[$date] = $dateArr;
			}
		}

		echo json_encode(array(
			"projects" => $projects, 
			"dates" => array_values($dates), 
			'maxpages' => $projectsObj->max_num_pages
		));
		die;
	}

	function delete_project(){
		if(!wp_verify_nonce( $_POST['nonce'], 'ajax-nonce' )){
			die("Invalid Request!");
		}

		if(isset($_POST['project_id'])){
			$project_id = intval($_POST['project_id']);
			wp_delete_post($project_id, true);
			delete_post_meta( $project_id, 'project_details' );
			delete_post_meta( $project_id, 'project_preview_url' );
			delete_post_meta( $project_id, 'project_competition' );

			echo json_encode(array("success" => "Seuccess"));
			die;
		}
	}

	function get_profile_informations(){
		if(!wp_verify_nonce( $_GET['nonce'], 'ajax-nonce' )){
			die("Invalid Request!");
		}

		if(!isset($_GET['author'])){
			return;
		}

		$author = intval($_GET['author']);

		$applicationsOptions = get_comp_cat_list("applications");
		$gradesOptions = get_grade_list();
		$agesOptions = range(6,24);

		$points = get_author_points($author);;

		$profileInfo = get_user_meta( $author, 'comp_profile_informations', true );
		echo json_encode(array(
			"points" => $points,
			"profileData" => $profileInfo,
			'applications' => array_values($applicationsOptions),
			'grades' => array_values($gradesOptions),
			'ages' => array_values($agesOptions),
			'avatars' => comp_avatars()
		));
		die; 
	}

	// Save profile info
	function save_profile_information(){
		if(!wp_verify_nonce( $_POST['nonce'], 'ajax-nonce' )){
			die("Invalid Request!");
		}

		if(isset($_POST['data'])){
			$data = $_POST['data'];
			update_user_meta( get_current_user_id(  ), 'comp_profile_informations', $data );
			echo json_encode(array("success" => "Success"));
			die;
		}

		die;
	}

	function comp_set_post_thumbnail($file, $post_id){
		$filename = basename($file);

		$upload_file = wp_upload_bits( $filename, null, @file_get_contents( $file ) );
		if ( ! $upload_file['error'] ) {
			// if succesfull insert the new file into the media library (create a new attachment post type).
			$wp_filetype = wp_check_filetype($filename, null );
			
			$attachment = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_parent'    => $post_id,
				'post_title'     => preg_replace( '/\.[^.]+$/', '', $filename ),
				'post_content'   => '',
				'post_status'    => 'inherit'
			);
			
			$attachment_id = wp_insert_attachment( $attachment, $upload_file['file'], $post_id );
			
			if ( ! is_wp_error( $attachment_id ) ) {
				// if attachment post was successfully created, insert it as a thumbnail to the post $post_id.
				require_once(ABSPATH . "wp-admin" . '/includes/image.php');
			
				$attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_file['file'] );
			
				wp_update_attachment_metadata( $attachment_id,  $attachment_data );
				set_post_thumbnail( $post_id, $attachment_id );
			}
		}
	}

	function upload_documents($file){
		global $comp_alerts;
		$comp_alerts = [];

		$wpdir = wp_upload_dir(  );
		$max_upload_size = wp_max_upload_size();
		$fileSize = $file['size'];
		$imageFileType = strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));

		$filename = rand(10,100);

		$folderPath = $wpdir['basedir'];
		$uploadPath = $folderPath."/".$filename.".".$imageFileType;
		$uploadedUrl = $wpdir['baseurl']."/".$filename.".".$imageFileType;

		// Allow certain file formats
		$allowedExt = array("jpg", "jpeg", "png", "PNG", "JPG", "gif");

		if(!in_array($imageFileType, $allowedExt)) {
			$comp_alerts['error'] = "Unsupported file format!";
		}

		if ($fileSize > $max_upload_size) {
			$comp_alerts['error'] = "Maximum upload size $max_upload_size";
		}

		if(empty($comp_alerts)){
			if (move_uploaded_file($file["tmp_name"], $uploadPath)) {
				return $uploadedUrl;
			}
		}
	}

	// Submit project
	function submit_project(){
		if(isset($_POST['submit_project'])){
			if(wp_verify_nonce( $_POST['project_form_nonce'], 'form_nonce') && isset($_POST['competition_id'])){
				global $comp_alerts;

				$competition_id = intval($_POST['competition_id']);

				$name_of_the_project = null;
				$project_url = null;
				$project_application = null;
				$project_grade = null;
				$short_description = null;
				$imageUrl = null;

				if(isset($_POST['name_of_the_project'])){
					$name_of_the_project = sanitize_text_field( $_POST['name_of_the_project'] );
					$name_of_the_project = stripcslashes($name_of_the_project);
				}
				if(isset($_POST['project_url'])){
					$project_url = sanitize_text_field( $_POST['project_url'] );
				}
				if(isset($_POST['project_application'])){
					$project_application = intval($_POST['project_application']);
				}
				if(isset($_POST['project_grade'])){
					$project_grade = intval($_POST['project_grade']);
				}
				if(isset($_POST['short_description'])){
					$short_description = sanitize_text_field( $_POST['short_description'] );
					$short_description = stripcslashes($short_description);
				}
				if(isset($_FILES['project_image'])){
					$imageUrl = $this->upload_documents($_FILES['project_image']);
				}

				if($name_of_the_project !== null && $project_url !== null && $short_description !== null && $project_application !== null && $project_grade !== null){
					$args = array(
						'post_title'    => wp_strip_all_tags( $name_of_the_project ),
						'post_type'		=> 'project',
						'post_content'  => '',
						'post_status'   => 'publish',
						'post_author'   => get_current_user_id(  )
					);
					$project_id = wp_insert_post( $args );

					if(is_int($project_id)){

						if(!empty($imageUrl)){
							$this->comp_set_post_thumbnail($imageUrl, $project_id);
						}

						wp_set_object_terms( $project_id, intval( $project_application ), 'application_cat' );

						update_post_meta( $project_id, 'project_details', $short_description );
						update_post_meta( $project_id, 'project_preview_url', $project_url );
						update_post_meta( $project_id, 'project_competition', $competition_id );
						update_post_meta( $project_id, 'project__grade', $project_grade );

						setcookie('success_project', "Successfully create your project.", time()+30, "/");
						wp_safe_redirect( get_the_permalink( $competition_id ).'?action=submission' );
						exit;
					}
				}
			}
		}
	}
}
