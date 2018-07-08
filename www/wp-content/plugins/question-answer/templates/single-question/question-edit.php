<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

$question_edit = sanitize_text_field($_GET['question_edit']);

$question_id = get_the_id();

$current_user_id = get_current_user_id();
$question_post = get_post($question_id);
$question_author = $question_post->post_author;

//echo '<pre>'.var_export($question_post, true).'</pre>';

if($question_author != $current_user_id){

    return;
}

$question_content = get_the_content();
$question_title = get_the_title();
$all_categories = get_terms( array(
    'taxonomy' => 'question_cat',
    'hide_empty' => false,
) );

$question_cat = wp_get_post_terms( $question_id, 'question_cat' );
$question_tags = wp_get_post_terms( $question_id, 'question_tags' );
$update_text = '';




$editor_id 		= 'question_content';


if(!empty($_POST['qa_question_edit_hidden'])) {


    $class_pickform = new class_pickform();





    $nonce = sanitize_text_field($_POST['_wpnonce']);

    if(wp_verify_nonce( $nonce, 'nonce_qa_question_edit' ) && $_POST['qa_question_edit_hidden'] == 'Y'){

        $question_content = ($_POST['question_content']);
        $question_title = sanitize_text_field($_POST['question_title']);
        $question_tags = sanitize_text_field($_POST['question_tags']);
        $question_cat = sanitize_text_field($_POST['question_cat']);

        $question_content = $class_pickform->kses($question_content);

        $question_content = stripslashes($question_content);



        //echo '<pre>'.var_export($question_cat, true).'</pre>';


        $question_post = array(
            'ID'           => $question_id,
            'post_title'   => $question_title,
            'post_content' => $question_content,
        );

// Update the post into the database
        $return = wp_update_post( $question_post );

        wp_set_post_terms( $question_id, array($question_cat), 'question_cat' );
        wp_set_post_terms( $question_id, $question_tags, 'question_tags' );

        if($return){

            $update_text = 'Question successfully update';

            wp_safe_redirect(get_permalink($question_id));
        }
        //var_dump($qa_question_content);
    }
}


?>

<div class="question-edit pickform">
    <h1 class="entry-title"><?php echo __('Edit:', 'question-answer'); ?> <?php the_title(); ?></h1>

    <?php

    if(!empty($update_text)){

        ?>
    <div class="update"><i class="fa fa-check" aria-hidden="true"></i> <?php echo $update_text; ?></div>
    <?php

    }

    ?>

    <form action="#" method="post">
        <input type="hidden" name="qa_question_edit_hidden" value="Y">

        <div class="option">
            <div class="title">Question title</div>
            <input type="text" value="<?php echo $question_title; ?>" name="question_title">
        </div>

        <div class="option">
            <div class="title">Question descriptions</div>

            <?php

            $editor_settings['editor_height'] = 150;
            $editor_settings['tinymce'] = true;
            $editor_settings['quicktags'] = true;
            $editor_settings['media_buttons'] = true;
            $editor_settings['drag_drop_upload'] = true;
            $editor_settings['media_buttons'] = false;
            $editor_settings['teeny'] = true;

            wp_editor($question_content, $editor_id, $editor_settings);

            ?>

        </div>


        <div class="option">
            <div class="title">Question category</div>
            <select name="question_cat" >
                <?php

                foreach($all_categories as $categories){
                    $term_id = $categories->term_id;
                    $name = $categories->name;
                    ?>
                        <option value="<?php echo $term_id; ?>"><?php echo $name; ?></option>

                    <?php
                }
                ?>
            </select>

        </div>

        <div class="option">
            <div class="title">Question tags</div>

            <?php
            $tags_html = '';

            if(is_array($question_tags))
            foreach($question_tags as $tags){

                $name = $tags->name;
                $tags_html.= $name.', ';
            }

            ?>


            <input type="text" value="<?php echo $tags_html; ?>" name="question_tags">
        </div>


        <?php wp_nonce_field( 'nonce_qa_question_edit' ); ?>
        <input type="submit" value="Update">


    </form>
</div>









