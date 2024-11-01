<?php

if( !class_exists( 'SPAMTRAP' ) ) {
	class SPAMTRAP {
		const API_HOST = 'api.spamtrap.ro';
		const API_VERSION = '/1';

		private static $init = false;
		private static $php_version = array( );
		private static $ip_headers = array( 
			'HTTP_CF_CONNECTING_IP',
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_X_CLUSTER_CLIENT_IP',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED',
			'REMOTE_ADDR' 
		);

		private static $default_cfg = array( 
			'onWPLogin'    => 1,
			'onWPRegister' => 1,
			'onWPComments' => 1,
		);

		public static function cfgLoad( ) {
			$cfg = get_option( SPAMTRAP_CFG );

			if( !is_array( $cfg ) )
				$cfg = array( );

			return array_merge( self::$default_cfg, ( array )$cfg );
		}

		public static function cfgSave( $cfg ) {
			return update_option( SPAMTRAP_CFG, $cfg );
		}

		public static function init( ) {
			if( !self::$init ) 
				self::init_hooks( );
		}

		public static function init_hooks( ) {
			self::$init = true;

			$cfg = self::cfgLoad( );
			
			if( $cfg[ 'onWPLogin' ] ) {
				add_action( 'login_enqueue_scripts' , array( __CLASS__, 'noncer_scripts' ) );
				add_action( 'login_form'            , array( __CLASS__, 'noncer_echo' ) );
				if( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' ) {
					add_filter( 'authenticate'      , array( __CLASS__, 'login_filter_authenticate_1' ), 10, 3 );
				}
			}

			if( $cfg[ 'onWPRegister' ] ) {
				add_action( 'login_enqueue_scripts' , array( __CLASS__, 'noncer_scripts' ) );
				add_action( 'register_form'         , array( __CLASS__, 'noncer_echo' ) );
				if( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' ) {
					add_filter( 'registration_errors', array( __CLASS__, 'register_filter_register_1' ), 10, 3 );
				}
			}

			if( $cfg[ 'onWPComments' ] ) {
				add_action( 'wp_enqueue_scripts'    , array( __CLASS__, 'noncer_scripts' ) );
				add_action( 'comment_form'          , array( __CLASS__, 'noncer_echo' ) );
				if( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' ) {
					add_filter( 'preprocess_comment', 	array( __CLASS__,  'comment_preprocess' ) ); 
				}
			}
		}

		public static function is_test( ) {
			if( defined( 'SPAMTRAP_TEST_MODE' ) && constant( 'SPAMTRAP_TEST_MODE' ) )
				return true;
			return false;
		}

		public static function microtime( ) {
			$mtime = explode( ' ', microtime( ) );
			return $mtime[ 1 ] + $mtime[ 0 ];
		}

		public static function is_php( $version = '5.0.0' ) {
			$version = ( string )$version;
			
			if( !isset( self::$php_version[ $version ] ) ) {
				self::$php_version[ $version ] = ( version_compare( PHP_VERSION, $version ) < 0 ) ? false : true;
			}
			
			return self::$php_version[ $version ];
		} 

		
		private static function get_user_agent( ) {
			return isset( $_SERVER[ 'HTTP_USER_AGENT' ] ) ? $_SERVER[ 'HTTP_USER_AGENT' ] : null;
		}

		private static function get_referer( ) {
			return isset( $_SERVER[ 'HTTP_REFERER' ] ) ? $_SERVER[ 'HTTP_REFERER' ] : null;
		}

		private static function get_user_ip( )
		{
			return isset( $_SERVER[ 'REMOTE_ADDR' ] ) ? $_SERVER[ 'REMOTE_ADDR' ] : null;
			
			/*
			$ip = array( );
			
			foreach( self::$ip_headers as $key ) {
				if( array_key_exists( $key, $_SERVER ) === true ) {
					foreach( explode( ',', $_SERVER[ $key ] ) as $visitors_ip ) {
						$ip[ ] = str_replace( ' ', '', $visitors_ip );
					}
				}
			}
			
			// If for some strange reason we don't get an IP we return imemdiately with 0.0.0.0
			if( empty( $ip ) ) {
				return '0.0.0.0';
			}
			
			$ip = array_values( array_unique( $ip ) );
			$return = null;

			// In PHP 5.3 and up the function filter_var can be used, much quicker as the regular expression check
			if( self::is_php( '5.3' ) ) {
				foreach( $ip as $i ) {
					if( filter_var( $i, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
						$return = $i;
						break;
					}
				}
			} 
			else {
				$dec_octet = '(?:\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])';
				$ip4_address = $dec_octet . '.' . $dec_octet . '.' . $dec_octet . '.' . $dec_octet;
				$match = array();
				foreach( $ip as $i ) {
					if( preg_match( '/^' . $ip4_address . '$/', $i, $match ) ) {
						if( preg_match('/^(127\.|10\.|192\.168\.|172\.((1[6-9])|(2[0-9])|(3[0-1]))\.)/', $i ) ) {
							continue;
						} else {
							$return = $i;
							break;
						}
					}
				}
			}
			
			if( $return === null ) {
				$return = '0.0.0.0';
			}
			
			return $return;
			*/
		} 

		public static function noncer_scripts( )
		{
			wp_enqueue_script( "jquery" );
		}

		public static function noncer_echo( $id = null )
		{
			$_id = current_filter( );
			if( $_id == 'comment_form' ) {
				$_id = 'comment_form_' . $id;
			}

			echo '<p style="display: none;">';
				wp_nonce_field( 'spamtrap_nonce_get_' . $_id, 'spamtrap_get', FALSE );

				echo "<input type='hidden' id='spamtrap_js' name='spamtrap_js' value='' />";
				$nonce = wp_create_nonce( 'spamtrap_nonce_js_' . $_id );
				echo "<script type='text/javascript'>"
						. "jQuery( '#spamtrap_js' ).val( '" 
						. $nonce
						. "' );"
						. "</script>";
			echo "</p>";
		}

		public static function noncer_check( $id = NULL )
		{
			if( $id == NULL )
				$id = self::$id;

			$ok = true;

			$rett = array( 
				'nonce_get' => 1,
				'nonce_js'  => 1,
				'nonce'     => 1
			);

			/// check GET
			if( !isset( $_POST[ 'spamtrap_get' ] ) 
				|| !wp_verify_nonce( $_POST[ 'spamtrap_get' ], 'spamtrap_nonce_get_' . $id ) )
			{
				$ok = false;
				$rett[ 'nonce_get' ] = 0;    			
			}

			/// check JS 
			if( !isset( $_POST[ 'spamtrap_js' ] ) 
				|| !wp_verify_nonce( $_POST[ 'spamtrap_js' ], 'spamtrap_nonce_js_' . $id ) )
			{
				$ok = false;
				$rett[ 'nonce_js' ] = 0;
			}
			
			$rett[ 'nonce' ] = $ok ? 1 : 0;

			return $rett;
		}

		///

		public static function register_filter_register_1( $errors, $user_login = null, $user_email = null ) {
			$ev = array(
				'type'  		=> 'register',
				'user_name'		=> $user_login,
				'user_email'	=> $user_email
			);

			$nonce = self::noncer_check( 'register_form' );
			$ev = array_merge( $ev, $nonce );

			if( !$nonce[ 'nonce' ] ) {
				self::request( '/event/register', $ev );	
				$errors->add( 'spamtrap_nonce', 'Spamtrap: Nonce Fail!' );
			}
			else {
				self::request( '/check/register', $ev );
			}

			return $errors;
		}

		///

		public static function login_filter_authenticate_1( $user, $username, $password ) {
			/// If we have already an error, just exit :)
			if( is_wp_error( $user ) ) 
				return $user;

			$ev = array(
				'type'  		=> 'login',
				'user_name'		=> $username,
				//'user_password' => md5( $password ),
			);
			
			$nonce = self::noncer_check( 'login_form' );
			$ev = array_merge( $ev, $nonce );

			if( !$nonce[ 'nonce' ] ) {
				self::request( '/event/login', $ev );

				/// remove official authentication filtre, what ignore my errors :|
				remove_filter( 'authenticate', 'wp_authenticate_username_password', 20, 3 );

				/// return the error
				$error = new WP_Error( );
				$error->add( 'spamtrap_nonce', 'Spamtrap: Nonce Fail!' );
				return $error;
			}
			else {
				self::request( '/check/login', $ev );				
			}

			return $user;
		}

		///

		static public function set_comment_as_spam( ) {
			return 'spam';
		}

		public static function comment_preprocess( $comment ) {
			if( $comment[ 'comment_type' ] != '' ) {
				return $comment;
			}

			$ev = array(
				'type'  		=> 'comment',
			);

			$nonce = self::noncer_check( 'comment_form_' . $comment[ 'comment_post_ID' ] );
			$ev = array_merge( $ev, $nonce, $comment );

			foreach( $_POST as $key => $value ) {
				if( is_string( $value ) )
					$ev[ "POST_{$key}" ] = $value;
			}

			if( !$nonce[ 'nonce' ] ) {
				add_filter( 'pre_comment_approved', array( __CLASS__, 'set_comment_as_spam' ) );
				self::request( '/event/comment', $ev );
				return $comment;
			}
			else {
				self::request( '/check/comment', $ev );
			}

			return $comment;
		}

		/// 

		public static function request( $path, $post ) 
		{
			global $wp_version;

			$ua = "Wordpress/{$wp_version} | Spamtrap/" . constant( "SPAMTRAP_VERSION" );
			
			$raw_post = '';
			
			if( is_array( $post ) ) {
				if( self::is_test( ) )
					$post[ 'test' ] = 1;
				
				///

				$post[ 'blog' ]	        = get_option( 'home' );
				$post[ 'blog_lang' ]    = get_locale( );
				$post[ 'blog_charset' ] = get_option( 'blog_charset' );
				$post[ 'referrer' ]     = self::get_referer( );
				$post[ 'user_ip' ]		= self::get_user_ip( );
				$post[ 'user_agent' ]   = self::get_user_agent( );

				$ignore = array( 'HTTP_COOKIE', 'HTTP_COOKIE2', 'PHP_AUTH_PW' );
				foreach ( $_SERVER as $key => $value ) {
					if ( !in_array( $key, $ignore ) && is_string( $value ) )
						$post[ "SRV_{$key}" ] = $value;
					else
						$post[ "SRV_{$key}" ] = '';
				}

				/// 

				foreach( $post as $key => $data )
					$raw_post .= $key . '=' . urlencode( stripslashes( $data ) ) . '&';
			}
			else {
				$raw_post = $post;
			}
			
			$args = array(
				'body' 			=> $raw_post,
				'headers' 		=> array(
					'Content-Type'	=> 	'application/x-www-form-urlencoded; charset=' . get_option( 'blog_charset' ),
					'Host'			=> self::API_HOST,
					'User-Agent'	=> $ua
				),
				'httpversion'	=> '1.0',
				'timeout'		=> 15,
			);
			
			$resp = wp_remote_post( "http://" . self::API_HOST . self::API_VERSION . $path, $args );
			
			if( is_wp_error( $resp ) ) {
				return '';
			}
			
			return array( $resp[ 'headers' ], $resp[ 'body' ] );
		}
	}
}
