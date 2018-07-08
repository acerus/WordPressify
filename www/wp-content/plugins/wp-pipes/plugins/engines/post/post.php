<?php
/**
 * @package          WP Pipes plugin
 * @version          $Id: post.php 170 2014-01-26 06:34:40Z thongta $
 * @author           thimpress.com
 * @copyright    2014 thimpress.com. All rights reserved.
 * @license          http://www.gnu.org/licenses/gpl-2.0.html
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

class WPPipesEngine_post {
	public static function getData( $params ) {
		if ( isset( $_GET['e'] ) ) {
			_e("\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n");
			ogb_pr( $params, 'Params: ' );
		}
		$data = self::getItemsPost( $params );
		$datas = array();
		foreach ( $data as $key=>$value ) {
			if (has_post_thumbnail( $value->ID ) ) {
				$image = wp_get_attachment_image_src( get_post_thumbnail_id( $value->ID ), 'single-post-thumbnail' );
			}
			$data[$key]->featured_image = @$image[0];
			$data[$key]->author_name	= get_the_author_meta( 'display_name' , $value->post_author );
			$data[$key]->link			= $value->guid;
			$data[$key]->src_name		= isset($value->post_title)?$value->post_title:'';
			$data[$key]->src_url		= isset($value->ID)?get_permalink($value->ID):'';
			
			$datas[]					= $data[$key];
		}
		if ( isset( $_GET['e1'] ) ) {
			_e("\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n");
			_e('Total: ' . count( $datas ));
			ogb_pr( $datas, 'Data: ' );
		}

		return $datas;
	}

	public static function getDataFields() {
		$data         = new stdClass();
		$data->output = array( 'post_title', 'post_name', 'post_content', 'link', 'author_name', 'post_date', 'featured_image' );

		return $data;
	}


	public static function getItemsPost( $params ) {
		$tags_list = array();
		if(count($params->tags)){
			foreach($params->tags as $tag){
				$tag_obj = get_term_by('slug', $tag, 'post_tag');
				$tags_list[] = $tag_obj->term_id;
			}
		}
		$just_seven = new WP_Query(
			array(
				'tag__in' => $tags_list,
				'category__in' => $params->categories,
				'author__in' => $params->author,
				'posts_per_page' => $params->limit_items
			)
		);

		return $just_seven->posts;
	}
}