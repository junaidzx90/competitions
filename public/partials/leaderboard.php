<?php
function get_leaderboard_contents($role){
    global $wpdb, $post;
    $post_id = $post->ID;
    $projects = $wpdb->get_results("SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_value = $post_id AND meta_key = 'project_competition'");

    $project_info = [];
    if($projects){
        foreach($projects as $project){
            $project_id = $project->post_id;
            if(get_post_status( $project_id ) === 'publish'){
                $post_author_id = get_post($project_id)->post_author;
                $user = get_user_by( "ID", $post_author_id );
                if($user && $user->roles[0] === $role){
                    $profileInfo = get_user_meta($post_author_id, 'comp_profile_informations', true);
 
                    if(!array_key_exists($post_author_id, $project_info)){
                        $project_info[$post_author_id] = [
                            'project_id' => $project_id,
                            'name' => ((array_key_exists('name', $profileInfo)) ? $profileInfo['name']: '...'),
                            'avatar' => ((array_key_exists('avatar', $profileInfo)) ? base64_decode($profileInfo['avatar']): '...'),
                            'country' => ((array_key_exists('country', $profileInfo)) ? $profileInfo['country']: '...'),
                            'projectIds' => [$project_id],
                            'project_link' => get_the_permalink( $project_id ),
                            'remark' => get_user_meta($post_author_id, "user_remark", true),
                            'points' => get_author_points($post_author_id)
                        ];
                    }elseif(array_key_exists($post_author_id, $project_info)){
                        $project_info[$post_author_id]['projectIds'][] = $project_id;
                    }
                }
            }
        }
    }

    return $project_info;
}

function thousandsShortform($num) {

    if($num>1000) {
  
          $x = round($num);
          $x_number_format = number_format($x);
          $x_array = explode(',', $x_number_format);
          $x_parts = array('k', 'm', 'b', 't');
          $x_count_parts = count($x_array) - 1;
          $x_display = $x;
          $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
          $x_display .= $x_parts[$x_count_parts - 1];
  
          return $x_display;
  
    }
  
    return $num;
}

$currentView = 'students';
$role = '';
if(isset($_GET['view'])){
    $currentView = $_GET['view'];
    if($currentView === 'students'){
        $role = 'student';
    }
    if($currentView === 'schools'){
        $role = 'school';
    }
}
?>

<div id="leaderboard">
    <div class="leaderboard_filter">
        <div class="tabs_container">
            <a href="<?php echo get_the_permalink(  )."?type=results&view=students" ?>" class="<?php echo (($currentView === 'students') ? 'active': '') ?>" id="students_tab">Students</a>
            <a href="<?php echo get_the_permalink(  )."?type=results&view=schools" ?>" class="<?php echo (($currentView === 'schools') ? 'active': '') ?>" id="schools_tab">Schools</a>
        </div>
    </div>

    <table class="leaderboard_table">
        <thead>
            <tr>
                <th class="leftalign">Competitor</th>
                <th>Country</th>
                <th>Application used</th>
                <th>Project Link</th>
                <th>Remark</th>
                <th>Points</th>
            </tr>
        </thead>
        <tbody id="leaderboardBody">
            <?php
            $results = get_leaderboard_contents($role);
            if(sizeof($results)){
                foreach ($results as $key => $row) {
                    $points[$key] = $row['points'];
                }
                array_multisort($points, SORT_DESC, $results);
            }

            if(is_array($results) && sizeof($results) > 0){
                $index = 1;
                foreach($results as $result){
                    ?>
                    <tr>
                        <td class="leftalign">
                            <p class="mblhead">Competitor: </p>
                            <div class="competitior_name">
                                <div class="competitor_avatar">
                                    <img width="30px"
                                        src="<?php echo $result['avatar'] ?>"
                                        alt="">
                                </div>
                                <span class="competitior_name"><?php echo $result['name'] ?></span>
                            </div>
                        </td>
                        
                        <td>
                            <p class="mblhead">Country: </p>
                            <span><?php echo $result['country'] ?></span>
                        </td>
                        
                        <td>
                            <p class="mblhead">Application used: </p>
                            <?php
                            if(is_array($result['projectIds'])){
                                $names = [];
                                foreach($result['projectIds'] as $project_id){
                                    foreach(wp_list_pluck( get_the_terms( $project_id, 'application_cat' ), 'name' ) as $name){
                                        $names[] = $name;
                                    }
                                }
                            }
                            ?>
                            <span><?php echo implode(", ", $names) ?></span>
                        </td>

                        <td>
                            <p class="mblhead">Project Link: </p>
                            <?php
                            if($index <= 3){
                                ?>
                                <a target="_blank" href="<?php echo $result['project_link'] ?>"><?php echo $result['project_link'] ?></a>
                                <?php
                            }
                            ?>
                        </td>
                        
                        <td>
                            <p class="mblhead">Remark: </p>
                            <span><?php echo $result['remark'] ?></span>
                        </td>

                        <td>
                            <p class="mblhead">Points: </p>
                            <div class="points_box">
                                <div title="<?php echo intval($result['points']) ?> points" class="stars">
                                    <?php echo thousandsShortform(intval($result['points'])/10) ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php
                    $index++;
                }
            }else{
                echo "<tr><td colspan='6'>No results found!</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>