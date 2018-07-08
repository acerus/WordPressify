<?php
if ( ! defined( 'ABSPATH' ) ) exit;


	/***
	 ***    @adding sort directories by friends
	 ***/
	add_filter( 'um_modify_sortby_parameter', 'um_friends_sortby_friends', 100, 2 );
	function um_friends_sortby_friends( $query_args, $sortby ) {
		if ($sortby != 'most_friends' && $sortby != 'least_friends') return $query_args;

		$query_args['orderby'] = 'friends';
		$query_args['order'] = $sortby == 'most_friends' ? 'DESC' : 'ASC';

		return $query_args;
	}

	/***
	 ***    @adding sort directories by friends
	 ***/
	add_filter( 'pre_user_query', 'um_wp_user_filter_by_friends', 100 );
	function um_wp_user_filter_by_friends( $query ) {
		global $wpdb;

		$users_table = $wpdb->users;
		$friends_table = UM()->Friends_API()->api()->table_name;

		if (isset( $query->query_vars['orderby'] ) && 'friends' == $query->query_vars['orderby']) {
			$order = $query->query_vars['order'];
			$query->query_orderby = sprintf(
				'ORDER BY (SELECT COUNT(*) FROM `%s` WHERE ( `%1$s`.`user_id1`= `%2$s`.ID OR `%1$s`.`user_id2` = `%2$s`.ID ) AND  `%1$s`.`status` = 1 ) %3$s',
				$friends_table, $users_table, $order );
		}

		return $query;
	}
