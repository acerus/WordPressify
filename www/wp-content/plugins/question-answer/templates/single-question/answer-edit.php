<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


$answer_id = isset($_GET['answer_edit']) ? sanitize_text_field($_GET['answer_edit']) : '';

$answer_data = get_post($answer_id);
$answer_author_id = $answer_data->post_author;
$answer_content = $answer_data->post_content;
$logged_user_id = get_current_user_id();


?>
<form class="form-answer-post pickform" style="display: block;"  enctype="multipart/form-data"  action="">

    <input id="answer_id" type="hidden" class="" value="<?php echo $answer_id; ?>" name="answer_id" />
    <input id="question_id" type="hidden" class="" value="<?php echo get_the_id(); ?>" name="question_id" />
	<div class="answer-post  clearfix">
        <?php

        if( is_user_logged_in()):

            if($answer_author_id == $logged_user_id || current_user_can('manage_options')):

            $logged_userdata = get_userdata($logged_user_id);
            $qa_account_required_post_answer = get_option( 'qa_account_required_post_answer', 'yes' );
            $qa_submitted_answer_status = get_option( 'qa_submitted_answer_status', 'pending' );
            $qa_options_quick_notes = get_option( 'qa_options_quick_notes' );
            $qa_who_can_comment_answer = get_option( 'qa_who_can_comment_answer' );
            $qa_who_can_see_quick_notes = get_option( 'qa_who_can_see_quick_notes', array('administrator') );
            $qa_answer_editor_media_buttons = get_option( 'qa_answer_editor_media_buttons', 'no' );

            $current_user_role = array_shift( $logged_userdata->roles );

            if( ! empty( $qa_who_can_answer ) && ! in_array( $current_user_role, $qa_who_can_answer ) ) return;
            ?>
			<div class="answer-post-header" _status="0">
				<span class="fs_18"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> <?php echo __('Edit Answer', 'question-answer');?></span>
					<i class="fa fa-compress fs_28 float_right apost_header_status"></i>
			</div>

				<?php if(!empty($qa_options_quick_notes)&& !empty($current_user_role) && in_array( $current_user_role, $qa_who_can_see_quick_notes ) ) : ?>

				<div class="quick-notes">

					<strong><?php echo __('Quick notes', 'question-answer'); ?></strong>
					<?php foreach( $qa_options_quick_notes as $note ) : if( empty( $note ) ) continue; ?>
						<input onclick="this.select();" type="text" value="<?php echo $note; ?>" />
					<?php endforeach; ?>

				</div>

				<?php endif; ?>


				<?php
				$editor_settings['editor_height'] = 150;
				$editor_settings['tinymce'] = true;
				$editor_settings['quicktags'] = true;
				$editor_settings['media_buttons'] = false;
				$editor_settings['drag_drop_upload'] = true;

				if( $qa_answer_editor_media_buttons == 'yes' ) $editor_settings['media_buttons'] = true;

				wp_editor( $answer_content, 'qa_answer_editor', $editor_settings );
				?>

				<p>
					<label for="is_private">
						<input id="is_private" type="checkbox" class="" value="1" name="is_private" />
						<?php echo __('Make your answer private.', 'question-answer'); ?>
					</label>
				</p>

                <p>
                    <label for="is_private">
	                    <?php echo __('Edit reason.', 'question-answer'); ?><br>
                        <input id="edit_reason" type="text" class="" value="" name="edit_reason" />

                    </label>
                </p>


				<div class="answer_posting_notice"></div>

				<?php wp_nonce_field( 'nonce_answer_edit' ); ?>
				<input type="hidden" name="question_id" value="<?php echo get_the_ID(); ?>" />
				<div class="qa_button update_answer_button hint--top" aria-label="<?php echo __('Answer will review', 'question-answer'); ?>">
					<?php echo __('Update Answer','question-answer'); ?>
				</div>




			<?php //do_action( 'qa_action_after_answer_post_form' ); ?>




    <?php
        else:
            ?>
            <div><i class="fa fa-exclamation-circle" aria-hidden="true"></i>
                <?php
                echo __('You are not authorized to edit this answer.', 'question-answer') ;
                ?>
            </div>
            <?php

            endif;

	else:
		?>
		<div>
			<?php
			echo sprintf( __('Please <a href="%s">login</a> to edit answer.', 'question-answer'), wp_login_url($_SERVER['REQUEST_URI'])  ) ;
			?>
		</div>
		<?php

	endif;

?>

	</div> <!-- .answer-post -->
</form>









