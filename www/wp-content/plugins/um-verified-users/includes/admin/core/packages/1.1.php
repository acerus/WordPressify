<?php
if ( ! defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly

$email_verified_account = UM()->options()->get( 'email-verified-account' );
$verified_notify_admin = UM()->options()->get( 'verified_notify_admin' );
$email_verification_request = UM()->options()->get( 'email-verification-request' );

UM()->options()->update( 'verified_account_on', true );
UM()->options()->update( 'verified_account_sub', 'Your account is verified on {site_name}!' );
UM()->options()->update( 'verified_account', $email_verified_account );

UM()->options()->update( 'verification_request_on', $verified_notify_admin );
UM()->options()->update( 'verification_request_sub', '{display_name} ({username}) verification request on {site_name}' );
UM()->options()->update( 'verification_request', $email_verification_request );

UM()->options()->remove( 'email-verified-account' );
UM()->options()->remove( 'verified_notify_admin' );
UM()->options()->remove( 'email-verification-request' );