<?php
global $comp_alerts;
wp_enqueue_script('jbox');
wp_enqueue_script('jquery-form');
wp_enqueue_script('submit.pro');


$applicationsCat = get_the_terms( get_post()->ID, 'application_cat' );
$applicationIds = [];
if($applicationsCat){
    foreach($applicationsCat as $application){
        $applicationIds[] = $application->term_id;
    }
}
$comp__grade = get_post_meta(get_post()->ID, 'comp__grade', true);
$comp__age = get_post_meta(get_post()->ID, 'comp__age', true);
$comp__age = explode("-", $comp__age);

$notallowed = true;
$formSubmission = false;

$profileInfo = get_user_meta( get_current_user_id(  ), 'comp_profile_informations', true );
$competition_type = get_post_meta( get_post()->ID, 'type_of_competition', true );
$max_projects = get_post_meta( get_post()->ID, 'maximum_project_allowed', true );
$active_date = get_post_meta( get_post()->ID, 'competition_active_date', true );

$competition_activate = strtotime(date("Y-m")) <= strtotime($active_date);

if($competition_activate){ // Expired date check
    $competition_id = get_post()->ID;
    $projects = $wpdb->get_results("SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_value = $competition_id AND meta_key = 'project_competition'");

    $mysubmissions = array();
    if($projects){
        foreach($projects as $project){
            $post_author_id = get_post($project->post_id)->post_author;

            if(get_current_user_id(  ) === intval($post_author_id)){
                $mysubmissions[] = intval($post_author_id);
            }

        }
    }

    $multiapplications = [];
    if(is_array($profileInfo['multiapplications'])){
        foreach($profileInfo['multiapplications'] as $mapplication){
            $multiapplications[] = $mapplication['term_id'];
        }
    }

    if(intval($max_projects) > 0 && intval($max_projects) > sizeof($mysubmissions)){ // Maximum allowed check
        if($typeofuser === 'student' && ($competition_type === "students" || $competition_type === "both")){
            if(is_array($profileInfo)){
                $userGrade = $profileInfo['grade'];
                $userAge = $profileInfo['age'];
                
                if(array_intersect($multiapplications, $applicationIds) && $userGrade == $comp__grade && ($userAge >= $comp__age[0] && $userAge <= $comp__age[1]) ){
                    $notallowed = false;
                    $formSubmission = true;
                }
            }
        }
        
        if($typeofuser === 'school' && ($competition_type === "schools" || $competition_type === "both")){
            if(is_array($profileInfo)){
               
                $multiGrades = [];
                if(is_array($profileInfo['multiGrades'])){
                    foreach($profileInfo['multiGrades'] as $mgrade){
                        $multiGrades[] = intval($mgrade['id']);
                    }
                }
                $multiAges = [];
                if(is_array($profileInfo['multiAges'])){
                    foreach($profileInfo['multiAges'] as $mgage){
                        $multiAges[] = intval($mgage);
                    }
                }
                
                if(array_intersect($multiapplications, $applicationIds) && in_array($comp__grade, $multiGrades)){
                    foreach($multiAges as $age){
                        if(intval($age) >= $comp__age[0] && intval($age) <= $comp__age[1]){
                            $notallowed = false;
                            $formSubmission = true;
                        }
                    }
                }
            }
        }

        if($notallowed){
            wp_safe_redirect(get_the_permalink( get_post()->ID ));
            exit;
        }
    }else{
        wp_safe_redirect(get_the_permalink( get_post()->ID ));
        exit;
    }
}else{
    wp_safe_redirect(get_the_permalink( get_post()->ID ));
    exit;
}

if($formSubmission){
?>
    <div id="submit_projects">
        <form method="post" class="project-upload-form" id="project-upload-form" enctype="multipart/form-data">
            <div class="comp_form_elements">
                <h3 class="comp_page_title"><?php echo __('Submit your project and get rewards.', 'competition') ?></h3>
                <?php 
                if(is_array($comp_alerts) && array_key_exists("error", $comp_alerts) && !empty($comp_alerts['error'])){
                    echo "<span class='comp-error-alerts'>".$comp_alerts['error']."</span>";
                }
                if(isset($_COOKIE['success_project'])){
                    echo "<span class='comp-success-alerts'>".$_COOKIE['success_project']."</span>";
                }
                ?>
                <div class="comp_input name_of_the_project">
                    <label for="name_of_the_project">Name of the project</label>
                    <input autocomplete="off" type="text" name="name_of_the_project" placeholder="22 characters" id="name_of_the_project">
                </div>

                <div class="comp_input project_url">
                    <label for="project_url">Project URL</label>
                    <input autocomplete="off" type="url" name="project_url" id="project_url">
                </div>

                <?php
                $applications = get_comp_cat_list("applications");
                $grades = get_comp_cat_list("grades");
                ?>
                <div class="comp_input comp_input_box">
                    <div class="comp_input project_application">
                        <label for="application">Application</label>
                        <select name="project_application" id="project_application">
                            <option value="">Select</option>
                            <?php 
                            foreach($applications as $application){
                                echo '<option value="'.$application['term_id'].'">'.$application['term_name'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="comp_input project_grade">
                        <label for="grade">Grade</label>
                        <select name="project_grade" id="project_grade">
                            <option value="">Select</option>
                            <?php 
                            foreach($grades as $grade){
                                echo '<option value="'.$grade['id'].'">'.$grade['grade'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                
                <div class="comp_input short_description">
                    <label for="short_description">Project Description</label>
                    <textarea name="short_description" rows="10" id="short_description"></textarea>
                </div>

                <div class="preview_image">
                    <img id="preview_image" src="" alt="preview-thumbnail">
                </div>

                <div class="comp_input project_image">
                    <label for="project_image">Upload Image
                        <input type="file" name="project_image" id="project_image">
                    </label>
                </div>

                <?php wp_nonce_field( 'form_nonce', 'project_form_nonce', '', true ); ?>
                <input type="hidden" name="competition_id" value="<?php echo get_post()->ID ?>">

                <input type="submit" value="Submit" name="submit_project" class="comp_btn" id="submit_project">
            </div>
        </form>
    </div>
    <?php
}