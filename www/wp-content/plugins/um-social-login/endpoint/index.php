<?php 
if( isset( $_REQUEST['um.auth.provider'] ) ) 
{
	$_SERVER["QUERY_STRING"] = 'um.oauth.done=' . $_REQUEST['um.auth.provider'] . '&' . str_ireplace( '?', '&', $_SERVER["QUERY_STRING"] );
	parse_str( $_SERVER["QUERY_STRING"], $_REQUEST );
}