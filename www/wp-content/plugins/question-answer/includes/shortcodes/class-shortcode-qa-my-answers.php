<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

class class_qa_shortcode_qa_my_answers{
	
    public function __construct(){
		add_shortcode( 'qa_my_answers', array( $this, 'my_answers' ) );
   	}	
		
	public function my_answers($atts, $content = null ) {
			
		$atts = shortcode_atts( array(
					
		), $atts);

		$question_per_page 	= get_option( 'qa_myaccount_question_per_page', 10 );

		if ( get_query_var('paged') ) { $paged = get_query_var('paged');}
		elseif ( get_query_var('page') ) { $paged = get_query_var('page'); }
		else { $paged = 1; }

		global $current_user;

		$wp_query = new WP_Query( array (

			'post_type' 	=> 'answer',
			'post_status' 	=> array('publish', 'pending'),
			'author' 		=> $current_user->ID,
			'orderby' 		=> 'date',
			'order' 		=> 'DESC',
			'posts_per_page'=> $question_per_page,
			'paged'			=> $paged,

		) );






		ob_start();

		?>
		<div class="qa-my-answers">


			<?php
			if ( $wp_query->have_posts() ) :
				while ( $wp_query->have_posts() ) : $wp_query->the_post();

					$qa_answer_question_id = get_post_meta( get_the_id(),'qa_answer_question_id', true );
					$post_status = get_post_status( get_the_id() );
					$post_date = get_the_date();
					?>

					<div class="item">
						<div class="q-title"><i class="fa fa-question-circle-o" aria-hidden="true"></i> <a href="<?php echo get_permalink($qa_answer_question_id); ?>#single-answer-<?php echo get_the_id(); ?>"><?php echo get_the_title($qa_answer_question_id); ?></a></div>
						<div class="meta">
							<span class="status"><?php echo $post_status; ?></span>
							<span class="date"><?php echo $post_date; ?></span>
						</div>

					</div>
					<?php
				endwhile;
				$html = '<div class="paginate">';
				$big = 999999999;
				$html .= paginate_links( array(
					'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
					'format' => '?paged=%#%',
					'current' => max( 1, $paged ),
					'total' => $wp_query->max_num_pages
				) );
				$html .= '</div >';
				echo $html;

				wp_reset_query();
			endif;
			?>



		</div>
		<?php

		return ob_get_clean();
	}
}


new class_qa_shortcode_qa_my_answers();