<?php
global $wpdb;
$post_id = $post->ID;
$projects = $wpdb->get_results("SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_value = $post_id AND meta_key = 'project_competition'");
?>
<div id="competitors">
    <table class="widefat">
        <thead>
            <tr>
                <th> <span class="dashicons dashicons-nametag"></span>
                Competitor</th>
                <th>School</th>
                <th>Grade</th>
                <th>Project</th>
                <th>Remark</th>
                <th>Points</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if($projects){
                foreach($projects as $project){
                    $project_id = $project->post_id;
                    $project_title = get_the_title( $project_id );
                    $project_link = get_edit_post_link( $project_id );
                    $post_author_id = get_post($project_id)->post_author;
                    $profileInfo = get_user_meta($post_author_id, 'comp_profile_informations', true);
                    $school = '';
                    $authorname = '';
                    $points = 0;
                    $grades = null;
                    $multiGrades = null;

                    $points = get_author_points($post_author_id);

                    if(!empty($profileInfo)){
                        $school = $profileInfo['school'];
                        $authorname = $profileInfo['name'];

                        $user = get_user_by( "ID", $post_author_id );
                        if($user->roles[0] === 'student'){
                            $grades = $profileInfo['grade'];
                            $grades = get_grade_list( $grades );
                            if(is_array($grades)){
                                $grades = $grades['grade'];
                            }
                        }
                        if($user->roles[0] === 'school' || $user->roles[0] === 'administrator'){
                            $grades = wp_list_pluck( $profileInfo['multiGrades'], 'grade' );
                        }
                    }
                    ?>
                    <tr>
                        <td><a target="_junu" href="<?php echo ((get_option('comp_profile_page')) ? get_the_permalink( get_option('comp_profile_page') ).'?user='.$post_author_id : '#') ?>"><?php echo $authorname ?></a></td>
                        <td><?php echo $school ?></td>
                        <td>
                            <?php 
                            if(is_array($grades)){
                                echo implode(", ", $grades);
                            }else{
                                echo $grades;
                            }
                            ?>
                        </td>
                        <td>
                            <a target="_blank" href="<?php echo $project_link ?>"><?php echo $project_title ?></a>
                        </td>
                        <td>
                            <select data-user="<?php echo $post_author_id ?>" id="user_remark" name="remarks[<?php echo $post_author_id ?>]">
                            <option value="">Select</option>
                            <?php
                            $selected = get_user_meta($post_author_id, "user_remark", true);
                            $remarks = get_option("comp_remarks");
                            if(!is_array($remarks)){
                                $remarks = [];
                            }
                            foreach($remarks as $remark){
                                echo '<option '.(($selected === $remark) ? 'selected': '').' value="'.$remark.'">'.$remark.'</option>';
                            }
                            ?>
                            </select>
                        </td>
                        <td>
                            <?php echo $points ?>
                        </td>
                    </tr>
                    <?php
                }
            }else{
                echo '<tr> <td>No Competitor Found!</td> </tr>';
            }
            ?>
        </tbody>
    </table>
</div>