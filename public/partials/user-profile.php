<?php get_header(); ?>
<?php
$author_id = null;
if(!is_user_logged_in(  ) && !isset($_GET['user'])){
    wp_safe_redirect( home_url() );exit;
}else{
    if(isset($_GET['user'])){
        $author_id = intval($_GET['user']);
    }
}

wp_enqueue_script('comp_vue'); 
wp_enqueue_script( 'multiselect' ); 
wp_enqueue_script( 'profile' ); 
wp_localize_script('profile', "ajax_data", array(
    'ajaxurl' 		=> admin_url('admin-ajax.php'),
    'nonce' 		=> wp_create_nonce('ajax-nonce'),
    'userId'        => (($author_id) ? $author_id : get_current_user_id(  ))
));

$typeofuser = null;
if(is_user_logged_in(  ) && current_user_can( 'student' ) && $author_id === null){
    $typeofuser = 'student';
}elseif( is_user_logged_in(  ) && $author_id === null && (current_user_can( 'school' ) || current_user_can( 'administrator' ))){
    $typeofuser = 'school';
}else{
    if($author_id){
        $user = get_user_by( "ID", $author_id );
        if($user && $user->roles[0] === 'student'){
            $typeofuser = 'student';
        }
        if($user && ($user->roles[0] === 'school' || $user->roles[0] === 'administrator')){
            $typeofuser = 'school';
        }
    }else{
        wp_safe_redirect( home_url() );exit;
    }
}

$applicationsOptions = get_comp_cat_list("applications");
$gradesOptions = get_grade_list();
$agesOptions = range(6,25);

?>
<div :class="isDisabled ? 'disabled': ''" id="user_dashboard">
    <div class="__dash_contents">
        <?php 
        if($typeofuser === 'school'){
            echo '<h1 class="__profile_type">School team</h1>';
        }
        ?>

        <div class="__profile__top">
            <div class="profile__card">
                <div class="__card_top">
                    <div class="__avatar">
                        <img width="100px" :src="getProfileImage(profileInfoStore.avatar)" alt="avatar">
                    </div>
                    <div class="user__info">
                        <div class="__user__name">
                            <h3 class="__name">{{profileInfoStore.name}}</h3>
                        </div>
                        <div class="__school_name">
                            <p>{{profileInfoStore.school}}</p>
                        </div>
                        <div class="__avg_points">
                            <i class="fa-solid fa-trophy"></i>
                            <strong>{{get_points_value(points)}}</strong>
                        </div>
                    </div>
                </div>
                <div class="__description">
                    <label>Description</label>
                    <p>{{profileInfoStore.userBio}}</p>
                </div>
            </div>

            <div class="profile__info">

                <div class="profile__info_top">
                    <h4 class="profile_setup">Profile informations</h4>

                    <?php 
                    if(is_user_logged_in(  ) && $author_id === null){
                        ?>
                        <button title="Edit profile" @click="needToEditInfo()" v-if="isEditInfo === false" c-lass="edit_profile__btn"><i class="fa-solid fa-pen-to-square"></i></button>
                        <button @click="saveProfileChanges()" title="Save changes" v-if="isEditInfo" class="save_profile__btn"><i class="fa-solid fa-floppy-disk"></i></button>
                        <?php
                    }
                    ?>
                </div>
                

                <div v-if="isEditInfo === false" class="info__view">
                    <dl>
                        <dt><i class="fas fa-globe-africa"></i> Country</dt>
                        <dd v-html="profileInfoStore.country"></dd>
                        <?php
                        if($typeofuser === 'student'){
                            ?>
                            <dt><i class="fa-solid fa-chalkboard-user"></i> Applications</dt>
                            <dd v-html="getMultiCategories('applications', profileInfoStore.multiapplications)"></dd>
                            <dt><i class="fa-solid fa-person-booth"></i> Grade</dt>
                            <dd v-html="getGrade(profileInfoStore.grade)"></dd>
                            <dt><i class="fa-solid fa-scale-unbalanced"></i> Age</dt>
                            <dd v-html="profileInfoStore.age"></dd>
                            <?php
                        }
                        if($typeofuser === 'school'){
                            ?>
                            <dt><i class="fa-solid fa-chalkboard-user"></i> Applications</dt>
                            <dd v-html="getMultiCategories('applications', profileInfoStore.multiapplications)"></dd>
                            <dt><i class="fa-solid fa-person-booth"></i> Grades</dt>
                            <dd v-html="getMultiCategories('grades', profileInfoStore.multiGrades)"></dd>
                            <dt><i class="fa-solid fa-scale-unbalanced"></i> Ages</dt>
                            <dd v-html="getMultiCategories('ages', profileInfoStore.multiAges)"></dd>
                            <?php
                        }
                        ?>
                    </dl>
                </div>
                <?php 
                if(is_user_logged_in(  ) && $author_id === null){
                    ?>
                    <div v-if="isEditInfo" class="formData">
                        <div class="comp__p_inp">
                            <label>Avatar</label>
                            <div class="__avatar_box">
                                <div v-for="avatar in avatars" :key="avatar.id" :class="avatar.id === profileInfoStore.avatar ? 'avatar_image selected' : 'avatar_image'">
                                    <img @click="avatarSelect(avatar.id)" width="50px" :src="avatar.url" alt="avatar">
                                </div>
                            </div>
                        </div>

                        <div class="comp__p_inp">
                            <label for="__user_name">Your name</label>
                            <input v-model="profileInfoStore.name" type="text" id="__user_name" placeholder="Full Name">
                        </div>

                        <div class="comp__p_inp">
                            <label for="__about_user">About you</label>
                            <textarea v-model="profileInfoStore.userBio" id="__about_user"></textarea>
                        </div>

                        <div class="comp__p_inp">
                            <label for="__school">School</label>
                            <input type="text" v-model="profileInfoStore.school" placeholder="School" id="__school">
                        </div>

                        <div class="comp__p_inp">
                            <label for="__country">Country</label>
                            <input type="text" v-model="profileInfoStore.country" placeholder="Country" id="__country">
                        </div>

                        <div class="comp__p_inp">
                            <label for="__applications">Applications</label>
                            <multiselect v-model="profileInfoStore.multiapplications" :options="applications"
                                :multiple="true" 
                                :taggable="true"
                                :close-on-select="true" 
                                :clear-on-select="false" 
                                :preserve-search="true" 
                                label="term_name" 
                                track-by="term_name" 
                                :preselect-first="false" 
                                id="__applications">
                            </multiselect>
                        </div>
                        <?php

                        // Grades
                        if($typeofuser === 'student'){
                            ?>
                            <div class="comp__p_inp">
                                <label for="__grades">Grade</label>
                                <select v-model="profileInfoStore.grade" id="__grades">
                                    <option v-for="grade in grades" v-if="grade.id >= profileInfoStore.grade" :key="grade.id" :value="grade.id">{{grade.grade}}</option>
                                </select>
                            </div>
                            <?php
                        }

                        if($typeofuser === 'school'){
                            ?>
                            <div class="comp__p_inp">
                                <label for="__grades">Grades</label>
                                <multiselect v-model="profileInfoStore.multiGrades" :options="grades"
                                    :multiple="true" 
                                    :taggable="true"
                                    :close-on-select="true" 
                                    :clear-on-select="false" 
                                    :preserve-search="true" 
                                    label="grade" 
                                    track-by="grade" 
                                    :preselect-first="false" 
                                    id="__grades">
                                </multiselect>
                            </div>
                            <?php
                        }

                        // Ages
                        if($typeofuser === 'student'){
                            ?>
                            <div class="comp__p_inp">
                                <label for="__ages">Age</label>
                                <select v-model="profileInfoStore.age" id="__ages">
                                    <option v-for="age in ages" :key="age" v-if="age >= profileInfoStore.age" :value="age">{{age}}</option>
                                </select>
                            </div>
                            <?php
                        }

                        if($typeofuser === 'school'){
                            ?>
                            <div class="comp__p_inp">
                                <label for="__ages">Ages</label>
                                <multiselect v-model="profileInfoStore.multiAges" :options="ages"
                                    :multiple="true" 
                                    :taggable="true"
                                    :close-on-select="true" 
                                    :clear-on-select="false" 
                                    :preserve-search="true"
                                    :preselect-first="false" 
                                    id="__ages">
                                </multiselect>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>

        <?php
        if(is_user_logged_in(  ) && $author_id === null){
        ?>
        <div id="submitted__projects">
            <h3 class="projects__title">My Projects</h3>

            <div class="filters">
                <div class="competiton_filters">
                <div class="date_filters">
                    <span @click="prevDateFilter(event)" class="leftArrow"><i class="fa-solid fa-angle-left"></i></span>
                    <ul>
                        <li @click="dateFilter(event, item.value)" v-for="(item, index) in dateFilters" :key="item.id" :class="index <= 4 ? 'fitem_visible' : ''">
                            <span class="comp_year">{{item.year}}</span>
                            <strong class="comp_month">{{item.month}}</strong>
                        </li>
                    </ul>
                    <span @click="nextDateFilter(event)" class="rightArrow"><i class="fa-solid fa-angle-right"></i></span>
                </div>

                <div class="filters_by_cats">
                    <div class="_filter_select invisibleFilter">
                        <select @change="categoryFilter()" v-model="applicationFilter" class="_filter_select">
                            <option value="">All applications</option>
                            <?php
                            if(sizeof($applicationsOptions) > 0){
                                foreach($applicationsOptions as $coursArr){
                                    echo '<option value="'.$coursArr['term_id'].'">'.$coursArr['term_name'].'</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="_filter_select invisibleFilter">
                        <select @change="categoryFilter()" v-model="gradeFilter" class="_filter_select">
                            <option value="">All grades</option>
                            <?php
                            if(sizeof($gradesOptions) > 0){
                                foreach($gradesOptions as $classArr){
                                    echo '<option value="'.$classArr['id'].'">'.$classArr['grade'].'</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            </div>

            <div class="project_cards">
                <div v-for="project in projectsList" :key="project.id" class="project_card">
                    <div class="pro__thumbnail">
                        <img :src="project.thumbnail" alt="thumbnail">

                        <?php
                            if(1===1){
                                ?>
                                    <div class="delete_project">
                                        <i @click="deleteProject(project.id)" class="fa-solid fa-trash-can"></i>
                                    </div>
                                <?php
                            }
                        ?>

                    </div>

                    <div class="card_bottom">
                        <h3 class="card__title"><a :href="project.permalink" target="_blank" rel="noopener noreferrer">{{project.title}}</a></h3>
                        <span class="card__date"><i class="fa-solid fa-calendar-days"></i> {{project.date}}</span>

                        <div class="project__for">
                            <h4>Competition</h4>
                            <a :href="project.competition_link" target="_blank" rel="noopener noreferrer"><i class="fa-solid fa-link"></i> {{project.competition_title}}</a>
                        </div>
                    </div>
                </div>
                <div v-if="projectsList.length === 0" class="comp_warning">No projects were found!</div>
            </div>
            
            <div v-if="maxProjects > currentPage" class="loadmoreWrap">
                <button @click="loadmore_projects()" :class="isDisabled ? 'isDisabled comp_loadmore' : 'comp_loadmore'">Load More</button>
            </div>
        </div>
        <?php } ?>

    </div>
</div>
<?php get_footer(); ?>
