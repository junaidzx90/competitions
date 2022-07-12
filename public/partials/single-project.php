<?php

$author = get_post(  )->post_author;
$profileInfo = get_user_meta( $author, 'comp_profile_informations', true );
$avatar_url = null;
$authorname = null;
if(is_array($profileInfo)){
    $avatars = comp_avatars();
    $avatar_id = $profileInfo['avatar'];
    if(!empty($avatar_id)){
        $avtar_key = array_search($avatar_id, array_column($avatars, 'id'));
        $avatar_url = $avatars[$avtar_key]['url'];
    }

    $authorname = $profileInfo['name'];
}

get_header(  );
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

<div id="project_single" style="<?php echo ((the_post_thumbnail( 'large' ) === null)?'justify-content: center;':'') ?>">
    <div class="project_thumbnail">
        <?php echo the_post_thumbnail( 'large' ) ?>
    </div>

    <article class="project_article" style="<?php echo ((the_post_thumbnail( 'large' ) === null)?'margin-left: unset;':'') ?>">
        <div class="project_inner_top">
            <span class="publishdate"><i class="fa-solid fa-pen-to-square"></i> January 2,2022</span>
            <h3 class="competition__title"><?php echo the_title(  ) ?></h3>
            <div class="createdBy">
                <img width="50px" src="<?php echo $avatar_url ?>">
                <a target="_blank" href="<?php echo ((get_option('comp_profile_page')) ? get_the_permalink( get_option('comp_profile_page') ).'?user='.$author : '#') ?>"><?php echo $authorname ?> <i class="fa-solid fa-link"></i></a>
            </div>
        </div>

        <div class="project_contents">
            <?php echo get_post_meta(get_post()->ID, 'project_details', true) ?>
        </div>

        <?php
        // If comments are open or we have at least one comment, load up the comment template.
        if ( comments_open() || get_comments_number() ) :
            comments_template();
        endif;
        ?>
    </article>
</div>

<?php get_footer(  );