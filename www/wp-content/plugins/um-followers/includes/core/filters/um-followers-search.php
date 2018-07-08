<?php

	/***
	***	@adding sort directories by followers
	***/
	add_filter('um_modify_sortby_parameter', 'um_followers_sortby_followed', 100, 2);
	function um_followers_sortby_followed( $query_args, $sortby ) {
		if ( $sortby != 'most_followed' && $sortby != 'least_followed' ) return $query_args;

		$query_args['orderby'] = 'followers';
		$query_args['order']   = $sortby == 'most_followed' ? 'DESC' : 'ASC';

		return $query_args;
	}

	/***
	***	@adding sort directories by followers
	***/
	add_filter('pre_user_query', 'um_wp_user_filter_by_followers', 100);
	function um_wp_user_filter_by_followers($query) {
		global $wpdb;

		$users_table     = $wpdb->users;
		$followers_table = UM()->Followers_API()->api()->table_name;

		if(  isset( $query->query_vars['orderby'] ) && 'followers' == $query->query_vars['orderby'] ) {
			$order = isset( $query->query_vars['order'] ) ? $query->query_vars['order'] : 'DESC';
			$query->query_orderby = sprintf(
				'ORDER BY (SELECT COUNT(*) FROM `%s` WHERE `%s`.ID = `%s`.`user_id1`) %s',
				$followers_table, $users_table, $followers_table, $order);
		}

		return $query;
	}
