<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

add_action('qa_user_profile','qa_user_profile_navs', 10, 1);
add_action('qa_user_profile','qa_user_profile_section', 10, 1);




function qa_user_profile_section($author_id){

    ?>
    <div class="profile-sidebar">
	    <?php do_action('qa_user_profile_sidebar', $author_id); ?>
    </div>
    <div class="profile-main">
	    <?php do_action('qa_user_profile_main', $author_id); ?>
    </div>

    <?php

}





add_action('qa_user_profile_sidebar','qa_user_profile_card', 10, 1);
//add_action('qa_user_profile_sidebar','qa_user_profile_badges', 10, 1);

add_action('qa_user_profile_main','qa_user_profile_main', 10, 1);



function qa_user_profile_navs($author_id){

	$class_qa_user_profile = new class_qa_user_profile();
	$profile_navs = $class_qa_user_profile->profile_navs();


	$qa_page_user_profile = get_option('qa_page_user_profile');
	$qa_page_user_profile_url = get_permalink($qa_page_user_profile);
	//var_dump($profile_navs);


	$tab = isset($_GET['tab']) ? $_GET['tab'] : '';


    ?>
    <div class=" section">
        <div class="inner">
            <div class="profile-tabs">
                <ul class="profile-navs">
					<?php
					$i = 1;
					foreach ($profile_navs as $nav_index=>$nav){
						$nav_name = $nav['name'];

						?><li class="nav-item <?php if($nav_index==$tab) echo 'active'; ?>"><a href="<?php echo $qa_page_user_profile_url; ?>?id=<?php echo $author_id; ?>&tab=<?php echo $nav_index; ?>"><?php echo $nav_name?></a></li><?php

						$i++;
					}
					?>
                </ul>

            </div>
        </div>
    </div>
    <?php

}






function qa_user_profile_card($author_id){

	$author 	= get_userdata($author_id);
	$cover_photo = get_user_meta($author_id, 'cover_photo', true);
	$profile_photo = get_user_meta($author_id, 'profile_photo', true);

	if(empty($profile_photo)){
		$profile_photo = get_avatar_url( $author_id, array('size'=>'75') );
	}

	if(empty($cover_photo)){
		$cover_photo = QA_PLUGIN_URL."assets/front/images/card-cover.jpg";
	}

	global $wpdb;
	$table = $wpdb->prefix . "qa_follow";
	$logged_user_id = get_current_user_id();

	$follow_result = $wpdb->get_results("SELECT * FROM $table WHERE author_id = '$author_id' AND follower_id = '$logged_user_id'", ARRAY_A);

	$already_insert = $wpdb->num_rows;
	if($already_insert > 0 ){
		$follow_text = __('Following', 'question-answer');
		$follow_class = 'following';
	}
	else{
		$follow_text = __('Follow', 'question-answer');
		$follow_class = '';
	}

	?>

    <div class="section">
        <div class="inner">
            <div class="user-card">
                <div class="card-cover">
                    <img src="<?php echo $cover_photo; ?>" />
                </div>
                <div class="card-avatar">
                    <img src="<?php echo $profile_photo; ?>" />
                </div>

                <div class="author-follow qa-follow <?php echo $follow_class; ?>" author_id="<?php echo $author_id; ?>"><?php echo $follow_text;  ?></div>
                <div class="author-name"><?php echo $author->display_name; ?></div>
            </div>

        </div>

    </div>
	<?php

}



function qa_user_profile_badges($author_id){

	$author 	= get_userdata($author_id);
	$cover_photo = get_user_meta($author_id, 'cover_photo', true);

	$profile_photo = get_user_meta($author_id, 'profile_photo', true);

	if(empty($profile_photo)){
		$profile_photo = get_avatar_url( $author_id, array('size'=>'75') );
	}


	if(empty($cover_photo)){
		$cover_photo = QA_PLUGIN_URL."assets/front/images/card-cover.jpg";
	}

	?>

    <div class="section">
        <div class="inner">

            <div class="section-title">Badges</div>

        </div>

    </div>




	<?php

}




add_filter('qa_user_profile_nav_questions','qa_user_profile_nav_questions');

function qa_user_profile_nav_questions(){

	$author_id = isset($_GET['id']) ? $_GET['id'] : '';


	ob_start();
	$author 	= get_userdata($author_id);
	$wp_query = new WP_Query( array (
		'post_type' => 'question',
		'author' => $author_id,
		'posts_per_page' => 5,
	) );


    ?>
    <div class="section">
        <div class="inner">
            <div class="section-title">Recent Question</div>





            <div class="recent-questions">

				<?php

				if ( $wp_query->have_posts() ) :
					while ( $wp_query->have_posts() ) : $wp_query->the_post();

						?>
                        <div class="item">
                            <a href=""><?php echo get_the_title(); ?></a>
                        </div>
						<?php


					endwhile;
				endif;

				?>
            </div>
        </div>
    </div>
    <?php



    return ob_get_clean();


}




add_filter('qa_user_profile_nav_answers','qa_user_profile_nav_answers');

function qa_user_profile_nav_answers(){

	$author_id = isset($_GET['id']) ? $_GET['id'] : '';


	ob_start();
	$author 	= get_userdata($author_id);
	$wp_query = new WP_Query( array (
		'post_type' => 'answer',
		'author' => $author_id,
		'posts_per_page' => 5,
	) );


	?>
    <div class="section">
        <div class="inner">
            <div class="section-title">Recent Answer</div>
            <div class="recent-questions">
            <?php

            if ( $wp_query->have_posts() ) :
                while ( $wp_query->have_posts() ) : $wp_query->the_post();

                    ?>
                    <div class="item">
                        <a href=""><?php echo get_the_title(); ?></a>
                    </div>
                    <?php


                endwhile;
            endif;

            ?>
            </div>
        </div>
    </div>
	<?php

	return ob_get_clean();

}




add_filter('qa_user_profile_nav_comments','qa_user_profile_nav_comments');

function qa_user_profile_nav_comments(){

	$author_id = isset($_GET['id']) ? $_GET['id'] : '';


	ob_start();
	$author 	= get_userdata($author_id);
	$wp_query = new WP_Query( array (
		'post_type' => 'answer',
		'author' => $author_id,
		'posts_per_page' => 5,
	) );


	?>
    <div class="section">
        <div class="inner">
            <div class="section-title">Recent Answer</div>
            <div class="recent-questions">
				<?php

				if ( $wp_query->have_posts() ) :
					while ( $wp_query->have_posts() ) : $wp_query->the_post();

						?>
                        <div class="item">
                            <a href=""><?php echo get_the_title(); ?></a>
                        </div>
						<?php


					endwhile;
				endif;

				?>
            </div>
        </div>
    </div>
	<?php

	return ob_get_clean();

}





function qa_user_profile_main($author_id){

	$author 	= get_userdata($author_id);

	$class_qa_user_profile = new class_qa_user_profile();
	$profile_navs = $class_qa_user_profile->profile_navs();


	$tab = isset($_GET['tab']) ? $_GET['tab'] : '';

	?>

    <div class="tab-content">
		<?php
		$profile_navs_content = isset($profile_navs[$tab]['content']) ? $profile_navs[$tab]['content'] : '';
		echo $profile_navs_content;
		?>
    </div>


	<?php

}