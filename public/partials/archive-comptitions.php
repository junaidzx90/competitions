<?php get_header(); ?>
<?php 
wp_enqueue_script('comp_vue'); 
wp_enqueue_script('competitions'); 

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
?>

<div :class="isDisabled ? 'isDisabled' : ''" id="competitions">
    <div class="competition_head">
        
        <div class="title_area">
            <h1 class="competition_title" style="color:#ffffff;">Competitions</h1>
        </div>

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

                <?php
        
                $applicationsOptions = get_comp_cat_list("applications");
                $gradesOptions = get_grade_list();
                $agesOptions = range(6,25);
                ?>
                
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

    <div id="competition_results">
        <div class="competition_cards">
            <div v-for="competition in competitions" :key="competition.id" class="comp_card">
                <div class="card_head">
                    <h3 class="card__title"><a :href="competition.permalink" target="_blank" rel="noopener noreferrer">{{competition.title}}</a></h3>
                    <span class="card__date"><i class="fa-solid fa-calendar-days"></i> {{competition.date}}</span>
                </div>

                <div class="card_contents">
                     <div class="competition_status">
                        <div class="project_count">
                            <span class="competition_projects">Submitted Projects</span>
                            <strong class="project_count">{{competition.projects}}</strong>
                        </div>
                    </div>

                    <div class="enter_competion_btn">
                        <a v-if="competition.running" :href="competition.permalink">ENTER COMPETITION</a>
                        <a v-if="competition.running === false" :href="competition.permalink+'?type=results&view=students'">COMPETITION RESULT</a>
                    </div>
                    
                    <table>
                        <tr>
                            <th><i class="fa-solid fa-chalkboard-user"></i> Applications</th>
                            <td v-html="getCategoryValue(competition.applications)"></th>
                        </tr>
                        <tr>
                            <th><i class="fa-solid fa-person-booth"></i> Grade</th>
                            <td v-html="getCategoryValue(competition.grade)"></th>
                        </tr>
                        <tr>
                            <th><i class="fa-solid fa-scale-unbalanced"></i> Age</th>
                            <td v-html="getCategoryValue(competition.age)"></th>
                        </tr>
                    </table>
                </div>
            </div>

            <div v-if="competitions.length === 0" class="comp_warning">No competitions were found!</div>
        </div>
        
        <div v-if="competitionsMax > currentPage" class="loadmoreWrap">
            <button @click="loadmore_projects()" :class="isDisabled ? 'isDisabled comp_loadmore' : 'comp_loadmore'">Load More</button>
        </div>
    </div>
</div>

<?php get_footer(); ?>