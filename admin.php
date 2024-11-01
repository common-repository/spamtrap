<?php

if( !class_exists( 'SPAMTRAP_ADMIN' ) ) {
	class SPAMTRAP_ADMIN {
		private static $initiated = false;

		public static function init( ) {
			if( self::$initiated ) {
				return; 
			}

			self::$initiated = true;

			add_action( 'admin_menu', 			array( __CLASS__, 'admin_menu' ) );
			add_filter( 'plugin_action_links', 	array( __CLASS__, 'plugin_action_links' ), 10, 2 );
		}

		public static function plugin_action_links( $links, $file ) {
			if( $file == plugin_basename( SPAMTRAP_PLUGIN_URL . '/spamtrap.php' ) ) {
				$links[] = '<a href="' . admin_url( 'admin.php?page=spamtrap' ) . '">' . __( 'Settings' ) . '</a>';
			}
			return $links;		
		}

		public static function admin_menu( ) {
			add_menu_page( "Spamtrap", "Spamtrap", "manage_options", "spamtrap", "" );

			add_submenu_page( 
				"spamtrap", 
				__( "Settings" ), 
				__( "Settings" ), 	
				"manage_options", 
				"spamtrap", 			
				array( __CLASS__, "admin_page" )
			);
		}

		public static function admin_page( ) {
			$cfg = SPAMTRAP::cfgLoad( );

			if( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' ) {
				if( function_exists( 'current_user_can' ) && 
					!current_user_can( 'manage_options' ) )
				{
					die( __( 'Cheatin&#8217; uh?' ) );
				}

				if( isset( $_POST[ 'check_wplogin' ] ) && $_POST[ 'check_wplogin' ] ) 
					$cfg[ 'onWPLogin' ] = 1;
				else 
					$cfg[ 'onWPLogin' ] = 0;

				if( isset( $_POST[ 'check_wpregister' ] ) && $_POST[ 'check_wpregister' ] ) 
					$cfg[ 'onWPRegister' ] = 1;
				else
					$cfg[ 'onWPRegister' ] = 0;

				if( isset( $_POST[ 'check_wpcomments' ] ) && $_POST[ 'check_wpcomments' ] )
					$cfg[ 'onWPComments' ] = 1;
				else
					$cfg[ 'onWPComments' ] = 0;

				SPAMTRAP::cfgSave( $cfg );
			}
					
			?><div class="wrap">
				<h2>SpamTrap</h2>

				<hr/>
				<div class="wrap">
					<h3>Components</h3>
					<form method="POST">
						<table class="form-table">
							<tr valign="top">
								<th scope="row">WP Login</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span>WP Login Forms</span>
										</legend>
										<label for="check_wplogin">
											<input name="check_wplogin" type="checkbox" id="check_wplogin" value="1" 
												<?php if( $cfg[ 'onWPLogin' ] ) echo "checked='1'"; ?> >
											Enable for wordpress login form
										</label>
									</fieldset>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">WP Register</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span>WP Register Forms</span>
										</legend>
										<label for="check_wpregister">
											<input name="check_wpregister" type="checkbox" id="check_wpregister" value="1" 
												<?php if( $cfg[ 'onWPRegister' ] ) echo "checked='1'"; ?> >
											Enable for wordpress register form
										</label>
									</fieldset>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">WP Comments Forms</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text">
											<span>WP Comments Forms</span>
										</legend>
										<label for="check_wpcomments">
											<input name="check_wpcomments" type="checkbox" id="check_wpcomments" value="1" 
												<?php if( $cfg[ 'onWPComments' ] ) echo "checked='1'"; ?> >
											Enable for wordpress comments
										</label>
									</fieldset>
								</td>
							</tr>
						</table>
						<p class="submit">
							<input type="submit" name="submit" id="submit" class="button button-primary" value="Save">
						</p>
					</form>
				</div>
				
				<hr/>

				<div class="wrap">
					<h3>Harvesters</h3>
					<p>
						Spamtraps are usually e-mail addresses that are created not for communication, 
						but rather to lure spam. In order to prevent legitimate email from being invited, 
						the e-mail address will typically only be published in a location hidden from view 
						such that an automated e-mail address harvester (used by spammers) can find the 
						email address, but no sender would be encouraged to send messages to the email 
						address for any legitimate purpose. Since no e-mail is solicited by the owner of 
						this spamtrap e-mail address, any e-mail messages sent to this address are 
						immediately considered unsolicited.
						<span class="description">
							( for more information read <a href="http://en.wikipedia.org/wiki/Spamtrap" target="_blank">here</a> )
						</span>
					</p>
				</div>
			</div><?php		
		}
	}
}
