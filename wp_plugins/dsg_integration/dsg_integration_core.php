<?php
/**
 * Plugin Name: DSG Integration - Core
 * Version: 1.0.0
 * Author: Eric A Mohlenhoff <eamohl@leadsanddata.net>
 */

class DSG_Integration_Core
{
	/**
	 * Some defined class constants
	 */
	
	const SESSION_TOKEN_NAME = "dsg_core_session_token";

	const SESSION_TOKEN_EXPIATION = 2592000;
	
	const SQL_TABLE_NAME = "dsg_core_active_sessions";
	
	/**
	 * Class Singleton Instance
	 */
	protected static $current_instance = NULL;
	
	/**
	 * Class Instance Members
	 */
	
	protected $session_token;

	protected $active_session;
	
	/**
	 * Static Class Utility and Helper Functions
	 */
	
	public static function create_sql_table()
	{
		global $wpdb;
		
		$table = $wpdb->prefix . self::SQL_TABLE_NAME;
		
		$sql = <<<SQL
		CREATE TABLE IF NOT EXISTS $table (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			session_token varchar(32) NOT NULL,
			active_session text NOT NULL,
			created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			expires timestamp NOT NULL,
			PRIMARY KEY (id),
			KEY session_token (session_token)
		)
SQL;
		
		$wpdb->query( $sql );
	}
	
	public static function plugin_activation()
	{
		self::create_sql_table();
	}
	
	public static function is_session_token_set()
	{
		if ( isset( $_COOKIE[self::SESSION_TOKEN_NAME])) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public static function is_session_token_valid( $session_token )
	{
		/**
		 * TODO
		 */
		return TRUE;
	}
	
	public static function is_session_token_expired( $session_token )
	{
		/**
		 * TODO
		 */
		return FALSE;
	}

	public static function can_session_token_be_restored()
	{
		if ( isset( $_GET[self::SESSION_TOKEN_NAME])) {
			$restored_token = $_GET[self::SESSION_TOKEN_NAME];
			
			if ( self::is_session_token_valid( $restored_token ) && ( ! self::is_session_token_expired( $restored_token ))) {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	public static function can_session_token_be_created()
	{
		if ( isset( $_GET['transaction_id'])) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Returns the singleton instance
	 */
	public static function get_instance()
	{
		$instance = NULL;
		
		if ( self::$current_instance == NULL ) {
			$instance = new DSG_Integration_Core();
			
			if ( self::is_session_token_set() ) {
				$instance->continue_session_token();
				
				$session_token = $instance->get_session_token();
				
				if ( self::is_session_token_valid() && ( ! self::is_session_token_expired())) {
					$instance->restore_active_session();
				} else {
					$instance = NULL;
				}
			} else if ( self::can_session_token_be_restored() ) {
				$instance->restore_session_token();
				
				$session_token = $instance->get_session_token();
				
				if ( self::is_session_token_valid() && ( ! self::is_session_token_expired())) {
					$instance->restore_active_session();
				} else {
					$instance = NULL;
				}
			} else if ( self::can_session_token_be_created() ) {
				$instance->create_session_token();
				$instance->create_active_session();
			} else {
				$instance = NULL;
			}
		}
		
		self::$current_instance = $instance;
		
		return self::$current_instance;
	}
	
	/**
	 * Class instance constructor (non-public)
	 */
	protected function __construct()
	{
	}
	
	/**
	 * Class instance member functions
	 */
	
	protected function continue_session_token()
	{
		$session_token = $_COOKIE[self::SESSION_TOKEN_NAME];
		
		$this->session_token = $session_token;
	}

	protected function restore_session_token()
	{
		$session_token = $_GET[self::SESSION_TOKEN_NAME];

		$expiration = time() + self::SESSION_TOKEN_EXPIRATION;

		setcookie( self::SESSION_TOKEN_NAME, $session_token, $expiration );
		
		$this->session_token = $session_token;
	}

	protected function create_session_token()
	{
		$session_token = $_GET['transaction_id'];

		$expiration = time() + self::SESSION_TOKEN_EXPIRATION;

		setcookie( self::SESSION_TOKEN_NAME, $session_token, $expiration );
		
		$this->session_token = $session_token;
	}
	
	public function get_session_token()
	{
		return $this->session_token;
	}
}

/**
 * hook onto some actions, filters, etc., etc., etc.....
 */

 register_activation_hook( __FILE__, array( 'DSG_Integration_Core', 'plugin_activation' ));

?>