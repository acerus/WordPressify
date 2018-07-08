<?php
/*
* @Author 		pickplugins
* Copyright: 	pickplugins.com
*/

class class_qa_user_profile{

	public function __construct(){

	}

	public function profile_navs(){

		$navs = array(
//			'profile'=>array(
//				'name'=>'Profile',
//				'content'=>apply_filters('qa_user_profile_nav_profile',''),
//
//			),
			'questions'=>array(
				'name'=>'Questions',
				'content'=>apply_filters('qa_user_profile_nav_questions',''),

			),
			'answers'=>array(
				'name'=>'Answers',
				'content'=>apply_filters('qa_user_profile_nav_answers',''),

			),
			'comments'=>array(
				'name'=>'Comments',
				'content'=>apply_filters('qa_user_profile_nav_comments',''),

			),
			'vote'=>array(
				'name'=>'Vote',
				'content'=>apply_filters('qa_user_profile_nav_vote',''),

			),


		);

		$navs = apply_filters('qa_user_profile_navs',$navs);

		return $navs;



	}

}