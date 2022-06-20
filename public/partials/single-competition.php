<?php
get_header(  );
global $post, $wpdb;

if(current_user_can( 'administrator' )){
    ?>
    <style>
        html.touch, html.no-touch {
            margin-top: 32px !important;
        }
    </style>
    <?php
}else{
    /* Disable WordPress Admin Bar for all users */
    add_filter( 'show_admin_bar', '__return_false' );
}

$typeofuser = null;
if(current_user_can( 'student' )){
    $typeofuser = 'student';
}
if(current_user_can( 'school' ) || current_user_can( 'administrator' )){
    $typeofuser = 'school';
}

if(isset($_GET['type']) && $_GET['type'] === 'results'){
    $active_date = get_post_meta(get_post()->ID, 'competition_active_date', true );
    $competition_activate = strtotime(date("Y-m")) <= strtotime($active_date);

    if($competition_activate){
        wp_safe_redirect( get_the_permalink(  ) );
        exit;
    }

    ?>
    <div class="competition_results">
        <div class="comp_inner_top">
            <?php 
            $active_date = get_post_meta(get_post()->ID, 'competition_active_date', true );
            ?>
            <p class="compActiveDate"><?php echo date("F, Y", strtotime($active_date)) ?></p>
            <h3 class="competition__title"><?php echo the_title(  ) ?></h3>
        </div>

        <?php
        require_once 'leaderboard.php';
        ?>
    </div>
    <?php
}else{
    if(isset($_GET['action']) && $_GET['action'] === "submission"){
        require_once plugin_dir_path( __FILE__ )."/submit-project.php";
    }else{
        ?> 
        <div id="competition_single">
            <article class="competition_article">
                <div class="list_of_requirements">
                    <dl>
                        <dt><i class="fa-solid fa-chalkboard-user"></i> Applications</dt>
                        <dd>
                            <?php 
                            $apps = comp_terms( get_post(), 'application_cat' );
                            echo (($apps) ? $apps : ''); 
                            ?>
                        </dd>
                        <dt><i class="fa-solid fa-person-booth"></i> Grade</dt>
                        <dd>
                            <?php 
                                $gradeId = get_post_meta(get_post()->ID, 'comp__grade', true);
                                if($gradeId){
                                    echo get_grade_list($gradeId)['grade'];
                                }
                            ?>
                        </dd>
                        <dt><i class="fa-solid fa-scale-unbalanced"></i> Age</dt>
                        <dd>
                            <?php 
                            echo get_post_meta(get_post()->ID, 'comp__age', true);
                            ?>
                        </dd>
                    </dl>
                </div>
                <div class="comp_inner_top">
                    <?php 
                    $active_date = get_post_meta(get_post()->ID, 'competition_active_date', true );
                    ?>
                    <p class="compActiveDate"><?php echo date("F, Y", strtotime($active_date)) ?></p>
                    <h3 class="competition__title"><?php echo the_title(  ) ?></h3>
                </div>
        
                <div class="comp_contents">
                    <?php echo the_content(  ) ?>
                </div>
                
                <?php
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
                $profileInfo = get_user_meta( get_current_user_id(  ), 'comp_profile_informations', true );
                $competition_type = get_post_meta( get_post()->ID, 'type_of_competition', true );
                $max_projects = get_post_meta( get_post()->ID, 'maximum_project_allowed', true );
                $active_date = get_post_meta( get_post()->ID, 'competition_active_date', true );
                
                $competition_activate = strtotime(date("Y-m")) <= strtotime($active_date);

                if(is_user_logged_in(  )){
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

                        if(intval($max_projects) > 0 && intval($max_projects) > sizeof($mysubmissions)){ // Maximum allowed check
                            if($typeofuser === 'student' && ($competition_type === "students" || $competition_type === "both")){
                                if(is_array($profileInfo)){
                                    $multiapplications = [];
                                    if(is_array($profileInfo['multiapplications'])){
                                        foreach($profileInfo['multiapplications'] as $mapplication){
                                            $multiapplications[] = $mapplication['term_id'];
                                        }
                                    }
                                    $userGrade = $profileInfo['grade'];
                                    $userAge = $profileInfo['age'];
                                    
                                    if(array_intersect($multiapplications, $applicationIds) && $userGrade == $comp__grade){
                                        if($userAge >= $comp__age[0] && $userAge <= $comp__age[1]){
                                            $notallowed = false;
                                            ?>
                                            <div class="submitcompbtn">
                                                <a href="?action=submission">Submit Project</a>
                                            </div>
                                            <?php
                                        }else{
                                            $notallowed = false;
                                            ?>
                                            <div class="notallowed_user">
                                                <div class="warning__msg">
                                                    <p>You are not eligible to participate in this competition This competition is not for your age.</p>
                                                    <span>Please check other competitions for your age.</small>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    }
                                }
                            }
                            
                            if($typeofuser === 'school' && ($competition_type === "schools" || $competition_type === "both")){
                                if(is_array($profileInfo)){
                                    $multiapplications = [];
                                    if(is_array($profileInfo['multiapplications'])){
                                        foreach($profileInfo['multiapplications'] as $mapplication){
                                            $multiapplications[] = $mapplication['term_id'];
                                        }
                                    }
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
                                        $ageIs = false;
                                        foreach($multiAges as $age){
                                            if(intval($age) >= $comp__age[0] && intval($age) <= $comp__age[1]){
                                                $ageIs = true;
                                            }
                                        }
                                        if($ageIs){
                                            $notallowed = false;
                                            ?>
                                            <div class="submitcompbtn">
                                                <a href="?action=submission">Submit Project</a>
                                            </div>
                                            <?php
                                        }else{
                                            $notallowed = false;
                                            ?>
                                            <div class="notallowed_user">
                                                <div class="warning__msg">
                                                    <p>You are not eligible to participate in this competition This competition is not for your age.</p>
                                                    <span>Please check other competitions for your age.</small>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    }
                                }
                            }

                            if($notallowed){
                                ?>
                                <div class="notallowed_user">
                                    <div class="warning__msg">
                                        <p>You are not eligible to submit a project on this competition.</p>
                                        <span>Your profile does not meet with the requirements.</small>
                                    </div>
                                </div>
                                <?php
                            }
                        }else{
                            ?>
                            <div class="notallowed_user">
                                <div class="warning__msg">
                                    <p>You are not eligible to submit a project on this competition.</p>
                                    <span>You reached the maximum project submission.</small>
                                </div>
                            </div>
                            <?php
                        }
                    }else{
                        ?>
                        <div class="notallowed_user">
                            <div class="warning__msg">
                                <p>The competition has expired.</p>
                                <span>You can't submit your project to this competition, the running date is exceeded.</small>
                                <p><a href="<?php echo the_permalink(  ) ?>?type=results">Please see the competiton result here</a></p>
                            </div>
                        </div>
                        <?php
                    }
                }else{
                    ?>
                    <div class="notallowed_user">
                        <div class="warning__msg">
                            <p>You are not eligible to participate in this competition.</p>
                            <span>Please login to view competition and submit your project.</small>
                        </div>
                    </div>
                    <?php
                }
                ?>

            </article>
        </div>
        <?php
    }
}
get_footer(  );