<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.upwork.com/freelancers/~013cb8700c27145b4e
 * @since      1.0.0
 *
 * @package    Competitions
 * @subpackage Competitions/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Competitions
 * @subpackage Competitions/admin
 * @author     Junayed <admin@easeare.com>
 */
class Competitions_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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
		wp_enqueue_style( 'select2', plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/competitions-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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
		if(isset($_GET['page']) && $_GET['page'] === 'avatars'){
			wp_enqueue_media();
		}
		
		wp_enqueue_script( 'select2', plugin_dir_url( __FILE__ ) . 'js/select2.min.js', array( ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/competitions-admin.js', array( 'jquery', 'select2' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'compajax', [
			'ajaxurl' => admin_url("admin-ajax.php")
		] );

	}

	function update_user_remark(){
		if(isset($_POST['user']) && isset($_POST['remark'])){
			$userid = intval($_POST['user']);
			$remark = $_POST['remark'];

			update_user_meta($userid, "user_remark", $remark);
			echo json_encode(array("success" => "Success"));
			die;
		}
	}

	function competitions_post_types(){
		register_post_type('competitions',array(
			'labels' => array(
				'name' => _x('Competitions', 'Competitions'),
				'singular_name' => _x('competitions', 'Competition'),
				'menu_name'             => _x( 'Competitions', 'Admin Menu text', 'competition' ),
				'name_admin_bar'        => _x( 'Add Competition', 'Add New on Toolbar', 'competition' ),
				'add_new'               => __( 'New compitition', 'competition' ),
				'add_new_item'          => __( 'Add New Competition', 'competition' ),
				'new_item'              => __( 'New competition', 'competition' ),
				'edit_item'             => __( 'Edit competition', 'competition' ),
				'view_item'             => __( 'View competition', 'competition' ),
			),

			'public' 			  => true,
			'exclude_from_search' => false,
			'has_archive'         => true,
			'hierarchical'		  => false,
			'show_ui'		  	  => true,
			'publicly_queryable'  => true,
        	'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => false,
			'publicly_queryable'  => true,
			'register_meta_box_cb' => [$this,'competitions_meta_boxes'],
			'supports'            => array( 'title', 'editor' ),
			'menu_icon'           => 'dashicons-superhero',
			'query_var'           => true
		));

		register_post_type('project',array(
			'labels' => array(
				'name' => _x('Projects', 'project'),
				'singular_name' => _x('Project', 'Project'),
				'menu_name'             => _x( 'Projects', 'Admin Menu text', 'Project' ),
				'name_admin_bar'        => _x( 'Add Project', 'Add New on Toolbar', 'Project' ),
				'add_new'               => __( 'Add New', 'Project' ),
				'add_new_item'          => __( 'Add New Project', 'Project' ),
				'new_item'              => __( 'New Project', 'Project' ),
				'edit_item'             => __( 'Edit Project', 'Project' ),
				'view_item'             => __( 'View Project', 'Project' ),
			),

			'public' 			  => true,
			'exclude_from_search' => false,
			'has_archive'         => false,
			'hierarchical'		  => false,
			'show_ui'		  	  => true,
			'publicly_queryable'  => true,
        	'show_in_menu'        => 'edit.php?post_type=competitions',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => false,
			'publicly_queryable'  => true,
			'register_meta_box_cb' => [$this,'projects_meta_boxes'],
			'supports'            => array( 'title', 'thumbnail' ),
			'query_var'           => true
		));
	}

	function project_taxonomies(){
		$application_lab = array(
			'name' => _x( 'Applications', 'Application general name' ),
			'singular_name' => _x( 'Application', 'taxonomy singular name' ),
			'search_items' =>  __( 'Search Applications' ),
			'all_items' => __( 'All Applications' ),
			'parent_item' => __( 'Parent application' ),
			'parent_item_colon' => __( 'Parent application:' ),
			'edit_item' => __( 'Edit application' ), 
			'update_item' => __( 'Update application' ),
			'add_new_item' => __( 'Add New application' ),
			'new_item_name' => __( 'New application Name' ),
			'menu_name' => __( 'Applications' ),
		);
		
		// Now register the taxonomy
		register_taxonomy('application_cat',array('project', 'competitions'), array(
			'hierarchical' => true,
			'labels' => $application_lab,
			'show_ui' => true,
			'show_in_rest' => true,
			'show_admin_column' => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var' => true,
			'rewrite' => array( 'with_front'=>true, 'slug' => 'application_cat' ),
		));
	}
	
	// Custom roles
	function competition_roles(){
		if(get_option( 'competition_roles_version' ) < 1){
			add_role( 'student', 'Student' );
			add_role( 'school', 'School' );

			update_option( 'competition_roles_version', 1 );
		}
	}

	// Manage table columns
	function manage_competitions_columns($columns) {
		unset(
			$columns['subscribe-reloaded'],
			$columns['title'],
			$columns['taxonomy-application_cat'],
			$columns['date']
		);
	
		$new_columns = array(
			'title' => __('Title', 'competitions'),
			'competition_active_date' => __('Running month', 'competitions'),
			'competition_type' => __('Type', 'competitions'),
			'projects' => __('Projects', 'competitions'),
			'taxonomy-application_cat' => __('Applications', 'competitions'),
			'date' => __('Create Date', 'competitions'),
		);
	
		return array_merge($columns, $new_columns);
	}

	// View custom column data
	function manage_competitions_columns_views($column_id, $post_id){
		switch ($column_id) {
			case 'competition_active_date':
				$active_date = get_post_meta( $post_id, 'competition_active_date', true );
				echo date("F, Y", strtotime($active_date));
				break;
			case 'competition_type':
				$competition_type = get_post_meta( $post_id, 'type_of_competition', true );
				echo ucfirst($competition_type);
				break;
			case 'projects':
				global $wpdb;
				$projects = $wpdb->query("SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_value = $post_id AND meta_key = 'project_competition'");
				echo $projects;
				break;
		}
	}

	// Replace avatar
	function comp_get_avatar( $avatar = '', $id_or_email = null, $size = 96, $default = '', $alt = '' ) {
		$user = false;
 
		if ( is_numeric( $id_or_email ) ) {
	
			$id = (int) $id_or_email;
			$user = get_user_by( 'id' , $id );
	
		} elseif ( is_object( $id_or_email ) ) {
	
			if ( ! empty( $id_or_email->user_id ) ) {
				$id = (int) $id_or_email->user_id;
				$user = get_user_by( 'id' , $id );
			}
	
		} else {
			$user = get_user_by( 'email', $id_or_email );   
		}
	
		if ( $user && is_object( $user ) ) {
			$profileInfo = get_user_meta( $user->data->ID, 'comp_profile_informations', true );
			$avatar_url = null;
			if(is_array($profileInfo)){
				$avatars = comp_avatars();
				$avatar_id = $profileInfo['avatar'];
				if(!empty($avatar_id)){
					$avtar_key = array_search($avatar_id, array_column($avatars, 'id'));
					$avatar_url = $avatars[$avtar_key]['url'];
				}
			}

			if ( !empty($avatar_url) ) {
				$avatar = $avatar_url;
				$avatar = "<img alt='{$alt}' src='{$avatar}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
			}
	
		}
		return $avatar;
	}

	function competitions_meta_boxes(){
		// Competitions
		add_meta_box( 'competition_active_date', 'Running month', [$this, 'competion_date_callback'], 'competitions', 'side' );
		add_meta_box( 'type_of_competition', 'Type of competition', [$this, 'type_of_competitions_callback'], 'competitions', 'side' );
		add_meta_box( 'grade_selection', 'Grade', [$this, 'grade_selections_callback'], 'competitions', 'side' );
		add_meta_box( 'age_selection', 'Age range', [$this, 'age_selections_callback'], 'competitions', 'side' );
		add_meta_box( 'max_projects_allowed', 'Maximum number of projects allowed', [$this, 'max_projects_allowed_callback'], 'competitions', 'side' );
		add_meta_box( 'competitors_list', '<span><span class="dashicons dashicons-awards"></span> Competitors</span>', [$this, 'competitors_callback'], 'competitions', 'advanced' );
	}

	// Admin menupage
	function competition_admin_menupage(){
		add_submenu_page( 'edit.php?post_type=competitions', 'Avatars', 'Avatars', 'manage_options', 'avatars', [$this, 'competitions_avatars'], null );
		add_submenu_page( 'edit.php?post_type=competitions', 'Settings', 'Settings', 'manage_options', 'settings', [$this, 'competitions_settings'], null );
		add_settings_section( 'comp_settings_section', '', '', 'comp_settings_page' );
		// Profile page
		add_settings_field( 'comp_profile_page', 'Profile page', [$this,'comp_profile_page_cb'], 'comp_settings_page', 'comp_settings_section');
		register_setting( 'comp_settings_section', 'comp_profile_page');
		// Remarks
		add_settings_field( 'comp_remarks', 'Remarks', [$this,'comp_remarks_cb'], 'comp_settings_page', 'comp_settings_section');
		register_setting( 'comp_settings_section', 'comp_remarks');
	}

	// Settings page
	function competitions_settings(){
		?>
		<div id="comp">
			<h3 class="comp-title">Settings</h3>
			<hr>
			<div class="comp-content">
				<form style="width: 50%" method="post" action="options.php">
					<table class="widefat">
					<?php
					settings_fields( 'comp_settings_section' );
					do_settings_fields( 'comp_settings_page', 'comp_settings_section' );
					?>
					</table>
					<?php submit_button(); ?>
				</form>
			</div>
		</div>
		<?php
	}

	function competitions_avatars(){
		if(isset($_POST['avatars_upbtn'])){
			if(isset($_POST['image_urls'])){
				$images = $_POST['image_urls'];
				update_option( 'comp_avatars', $images );
			}
		}
		?>
		<h3>Avatars</h3>
		<hr>

		<div id="avatars_wrap">
			<form action="" method="post">
				<div class="avatars">
					<?php
					$images = get_option( 'comp_avatars', [] );
					if(sizeof($images) > 0){
						foreach($images as $image){
							?>
							<div class="avatar">
								<span class="removeAvatar">+</span>
								<img src="<?php echo $image ?>" alt="avatar">
								<input type="hidden" name="image_urls[]" value="<?php echo $image ?>">
							</div>
							<?php
						}
					}
					?>
				</div>

				<button id="addAvatar" class="button-secondary">Add avatars</button>
				<br>
				<br>
				<br>
				<input class="button-primary" type="submit" value="Save changes" name="avatars_upbtn">
			</form>
		</div>
		<?php
	}

	function comp_remarks_cb(){
		$remarks = get_option("comp_remarks");
		if(!is_array($remarks)){
			$remarks = [];
		}
		?>
		<div id="remarks">
			<div class="remarks_list">
				<?php 
				if(sizeof($remarks) > 0){
					foreach($remarks as $remark){
						echo '<div class="remark"><span>'.$remark.'</span><input type="hidden" name="comp_remarks[]" value="'.$remark.'"><span class="remarkremove">+</span></div>';
					}
				}
				?>
			</div>
				
			<input id="remark_input" type="text">
			<button class="button-secondary" id="add_remark">Add remark</button>
		</div>
		<?php
	}

	// Submit project page
	function comp_profile_page_cb(){
		$dropdown_args = array(
			'post_type'        => 'page',
			'selected'         => get_option('comp_profile_page'),
			'name'             => 'comp_profile_page',
			'show_option_none' => __('Select'),
			'sort_column'      => 'menu_order, post_title',
			'echo'             => 0,
		);
		
		echo wp_dropdown_pages( $dropdown_args );
	}

	// Date callback
	function competion_date_callback($post){
		$date = get_post_meta($post->ID, 'competition_active_date', true );
		echo '<input class="widefat" required type="month" value="'.$date.'" name="comp_date" id="comp_date">';
	}

	// Type of competition callbaack
	function type_of_competitions_callback($post){
		$output = '<select class="widefat" name="typeof_comp" id="typeof_comp">';
		$selected = get_post_meta($post->ID, 'type_of_competition', true );
		$output .= '<option '.($selected == 'both' ? 'selected' : '').' value="both">Both</option>';
		$output .= '<option '.($selected == 'students' ? 'selected' : '').' value="students">Students</option>';
		$output .= '<option '.($selected == 'schools' ? 'selected' : '').' value="schools">Schools</option>';
		$output .= '</select>';
		echo $output;
	}

	function grade_selections_callback($post){
		$grades = get_grade_list();
		$selected = get_post_meta($post->ID, 'comp__grade', true);
		if($selected){
			$selected = intval($selected);
		}
		echo '<select class="widefat" name="grade_selections" id="grade_selections">';
		echo '<option value="">Select</option>';
		
		if(is_array($grades)){
			foreach($grades as $grade){
				echo '<option '.(($selected === intval($grade['id'])) ? 'selected' : '').' value="'.$grade['id'].'">'.$grade['grade'].'</option>';
			}
		}

		echo '</select>';
	}

	function age_selections_callback($post){
		$selected = get_post_meta($post->ID, 'comp__age', true);
		echo '<input class="widefat" placeholder="6-16" type="text" name="comp__age" value="'.$selected.'">';
	}

	function max_projects_allowed_callback($post){
		$allowed = get_post_meta($post->ID, 'maximum_project_allowed', true );
		echo '<input type="number" class="widefat" name="maximum_project_allowed" value="'.$allowed.'">';
	}

	// competitors_callback
	function competitors_callback($post){
		require_once plugin_dir_path( __FILE__ )."partials/manage_competitors_meta.php";
	}

	function save_competition_data($post_id){
		if(isset($_POST['comp_date'])){
			update_post_meta( $post_id, 'competition_active_date', $_POST['comp_date'] );
			}
		if(isset($_POST['typeof_comp'])){
			update_post_meta( $post_id, 'type_of_competition', $_POST['typeof_comp'] );
		}
		if(isset($_POST['maximum_project_allowed'])){
			update_post_meta( $post_id, 'maximum_project_allowed', $_POST['maximum_project_allowed'] );
		}
		if(isset($_POST['grade_selections'])){
			update_post_meta( $post_id, 'comp__grade', $_POST['grade_selections'] );
		}
		if(isset($_POST['comp__age'])){
			update_post_meta( $post_id, 'comp__age', sanitize_text_field($_POST['comp__age']) );
		}
	}

	function projects_meta_boxes(){
		// Projects
		add_meta_box( 'project_details', 'Project details', [$this, 'project_details_callback'], 'project', 'advanced' );
		add_meta_box( 'project_preview_url', 'Project details', [$this, 'project_preview_url_callback'], 'project', 'advanced' );
		add_meta_box( 'submitted_by', 'Ownered', [$this, 'project_submitted_by_callback'], 'project', 'advanced' );
		add_meta_box( 'project_competition', 'Competition', [$this, 'project_competition_callback'], 'project', 'side' );
		add_meta_box( 'project_grade', 'Grade', [$this, 'project_grade_callback'], 'project', 'side' );
	}

	function project_details_callback($post){
		$project_details = get_post_meta($post->ID, 'project_details', true );
		echo '<textarea name="project_details" class="widefat" id="project_details" rows="10">'.$project_details.'</textarea>';
	}

	function project_preview_url_callback($post){
		$project_preview_url = get_post_meta($post->ID, 'project_preview_url', true );
		echo '<input class="widefat" type="url" name="project_preview_url" id="project_preview_url" value="'.$project_preview_url.'">';
	}

	function project_submitted_by_callback($post){
		$school = true;

		$post_author_id = $post->post_author;
		$profileInfo = get_user_meta($post_author_id, 'comp_profile_informations', true);
		$authorname = '';
		$points = 0;
		$schoolname = '';
		
		$points = ((get_post_meta($post->ID, 'user_project_points', true)) ? get_post_meta($post->ID, 'user_project_points', true) : 0);
		
		if(!empty($profileInfo)){
			$schoolname = $profileInfo['school'];
			$authorname = $profileInfo['name'];
		}

		$output = '<table class="widefat">';
		$output .= '<thead>';
		$output .= '<tr>';
		$output .= '<th>Submitted</th>';
		$output .= '<th>School</th>';
		$output .= '<th>Points / Marks</th>';
		$output .= '</tr>';
		$output .= '</thead>';

		$output .= '<tbody>';
		$output .= '<tr>';
		$output .= '<td><a href="'.((get_option('comp_profile_page')) ? get_the_permalink( get_option('comp_profile_page') ).'?user='.$post_author_id : '#').'" target="_b">'.$authorname.'</a></td>';
		$output .= '<td>'.$schoolname.'</td>';
		$output .= '<td>
		<input min="0" oninput="this.value = Math.abs(this.value)" type="number" name="competitor_points" value="'.$points.'">
		</td>';
		$output .= '</tr>';
		$output .= '</tbody>';
		$output .= '</table>';

		echo $output;
	}

	function project_competition_callback($post){
		$args = array(
			'post_type' => 'competitions',
			'post_status' => 'publish',
			'numberposts' => -1,
			'fields' => 'ids',
		);

		$competitions_IDS = get_posts($args);
		?>
		<select required id="project_competition" name="project_competition" class="widefat">
			<?php
			$selected = get_post_meta($post->ID, 'project_competition', true);
			
			if(sizeof($competitions_IDS) > 0){
				foreach($competitions_IDS as $cid){
					echo '<option '.((intval($selected) === intval($cid)) ? 'selected' : '').' value="'.$cid.'">'.get_the_title( $cid ).'</option>';
				}
			}
			?>
		</select>
		<?php
	}

	function project_grade_callback($post){
		$sgrade = get_post_meta($post->ID, 'project__grade', true);
		
		$grades = get_grade_list();
		echo '<select name="project_grade" class="widefat">';
		foreach($grades as $grade){
			echo '<option '.(($sgrade == $grade['id']) ? 'selected' : '').' value="'.$grade['id'].'">'.$grade['grade'].'</option>';
		}
		echo '</select>';
	}

	function save_project_data($post_id){
		if(isset($_POST['project_details'])){
			$details = stripcslashes( $_POST['project_details'] );
			$details = sanitize_text_field( $details );
			update_post_meta( $post_id, 'project_details', $details );
		}
		if(isset($_POST['project_competition'])){
			$compId = intval($_POST['project_competition']);
			update_post_meta( $post_id, 'project_competition', $compId );
		}
		if(isset($_POST['competitor_points'])){
			$competitor_points = intval($_POST['competitor_points']);
			update_post_meta($post_id, 'user_project_points', $competitor_points);
		}
	}
	
}
