<?php
/**
 * Plugin Name: DSG Integration - Core
 * Version: 1.0.0
 * Author: Eric A Mohlenhoff <eamohl@leadsanddata.net>
 */

class DSG_Integration_Core
{
	public static $SESSION_TOKEN_NAME = "dsg_core_session_token";

	public static $SESSION_TOKEN_EXPIATION = 60 * 60 * 24 * 30;

	protected static $current_instance = NULL;

	private $session_token;

	private $active_session;

	public static function is_session_token_set()
	{
		if ( isset( $_COOKIE[self::$SESSION_TOKEN_NAME] )) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public static function is_session_token_valid( $session_token )
	{
		return TRUE;
	}

	public static function can_session_token_be_restored()
	{
		if (( isset( $_GET[self::$SESSION_TOKEN_NAME] )) &&
			( is_session_token_valid( $_GET[self::$SESSION_TOKEN_NAME] ))) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public static function can_session_token_be_created()
	{
		if (( isset( $_GET['affiliate_id'] )) &&
			( isset( $_GET['offer_id'] )) &&
			( isset( $_GET['transaction_id'] ))) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public static function restore_session_token()
	{
		$session_token = $_GET[self::$SESSION_TOKEN_NAME];

		$expiration = time() + self::$SESSION_TOKEN_EXPIRATION;

		setcookie( self::$SESSION_TOKEN_NAME, $session_token, $expiration );
	}

	public static function create_session_token()
	{
		$session_token = $_GET['transaction_id'];

		$expiration = time() + self::$SESSION_TOKEN_EXPIRATION;

		setcookie( self::$SESSION_TOKEN_NAME, $session_token, $expiration );
	}

	public static function get_session_token()
	{
		return $_COOKIE[self::$SESSION_TOKEN_NAME];
	}

	public static function get_active_session( $session_token )
	{
		$active_session = array();

		return $active_session;
	}

	public static function create_active_session( $session_token )
	{
		$active_session = array();

		$active_session['session_token'] = $session_token;
		$active_session['affiliate_id'] = $_GET['affiliate_id'];
		$active_session['offer_id'] = $_GET['offer_id'];
		$active_session['transaction_id'] = $_GET['transaction_id'];

		return $active_session;
	}

	public static function get_instance()
	{
		if ( self::$current_instance == NULL ) {

		}

		return self::$current_instance;
	}

	protected function __construct( $session_token )
	{
		$this->session_token = $session_token;

	}

	public function create()
	{

	}

	public function read()
	{

	}

	public function update()
	{

	}
}
?>
