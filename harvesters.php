<?php

// Make sure we don't expose any info of colled directly
if( !function_exists( 'add_action' ) ) {
	exit;
}

if( !class_exists( 'SPAMTRAP_HARVESTERS' ) ) {
	class SPAMTRAP_HARVESTERS
	{
		public static $domains = array( 'mailxu.com' );
		
		public static $sep = array( ".", ".", "-", "_", "_", "", "" );
		
		public static $firstNames = array(
			  "rubi", "eko", "shay", "christian", "lucinda", "eileen"
			, "ervin", "ernestine", "waneta", "hoa", "yolando", "ahmad"
			, "cherlyn", "erica", "hilario", "wes", "eustolia", "francie"
			, "kurt", "shaunte", "brandon", "kent", "coralee", "mona"
			, "maddie", "scarlet", "augustine", "dean", "salley", "viva"
			, "bernie", "lovella", "jamel", "quiana", "ute", "josphine"
			, "stephania", "ilse", "pamula", "ed", "sebastian", "vaughn"
			, "golda", "harriette", "robin", "carissa", "misti", "thanh"
			, "jeni", "ellen", "sharen", "danette", "daisey", "emery"
			, "sofia", "cindi", "ina", "dayna", "brandee", "julieann"
			, "mariella", "torrie", "belva", "latonia", "man", "treva"
			, "mckenzie", "selina", "valentin", "mariah", "esteban", "darcie"
			, "vernell", "tandra", "ciera", "raven", "melita", "siobhan"
			, "bernetta", "francina", "bernice", "tatiana", "magdalene", "brunilda"
			, "trang", "mertie", "cyril", "rhiannon", "fatimah", "fe"
			, "kamilah", "chadwick", "rolf", "annis", "grace", "violet"
			, "maryln", "belinda", "misha", "viviana", "elfreda", "dalila"
			, "carli", "rayford", "garnet", "chase", "maris", "verdell"
			, "napoleon", "leilani", "lizabeth", "georgie", "lenna", "stacie"
			, "alisha", "winnifred", "mariko", "anja", "krystin", "jonas"
			, "janelle", "melania", "jeremiah", "carol", "sung", "elvina"
			, "erin", "lelia", "kiley", "noemi", "siu", "amal"
			, "blossom", "rosalee", "soledad", "trista", "rafael", "ching"
			, "jamila", "norbert", "krysta", "fidel", "dierdre", "sadie"
			, "karlene", "tasia", "mindy", "florencia", "francine", "ernestina"
		);
		
		public static $lastNames = array(
			  "connington", "rindal", "hovenga", "desorbo", "muschett", "githens"
			, "miao", "baldinger", "meireles", "pishner", "rydolph", "gilchrest"
			, "blicker", "ivanoff", "heaslip", "cousey", "romberger", "febles"
			, "hermanns", "harriston", "krolick", "hickock", "rodina", "wela"
			, "maise", "masucci", "stifter", "schoeneman", "jannell", "naro"
			, "silmon", "garriott", "mccaine", "justino", "sumida", "wegge"
			, "boblak", "buchmann", "schabot", "vogelsang", "champine", "cookingham"
			, "saras", "bacurin", "loats", "gilding", "odenheimer", "michaux"
			, "favero", "loverich", "stafford", "reutzel", "demarce", "apalategui"
			, "garlits", "mikkelsen", "stepaniak", "toevs", "powroznik", "delore"
			, "merdian", "dauphinais", "pesta", "dapas", "jervis", "winterbottom"
			, "kriegel", "tinnes", "butrick", "kammer", "deidrick", "marsolek"
			, "galkin", "schweiker", "strysko", "bua", "wildman", "jakubov"
			, "carnero", "murriel", "agpaoa", "westfield", "womble", "keely"
			, "ruesch", "truman", "lawbaugh", "sniffen", "raymos", "cornely"
			, "ahalt", "walman", "stuber", "budreau", "chinchilla", "nakai"
			, "sianez", "sealander", "moon", "cabrera", "balter", "ragans"
			, "sau", "bon", "vierra", "hemanes", "ruoho", "crook"
			, "vanetta", "prevost", "storniolo", "munster", "fritter", "coriell"
			, "holthaus", "hevessy", "ekis", "brassard", "dehghani", "darroch"
			, "brightly", "royals", "nati", "gelino", "berdar", "montejano"
			, "longton", "lechuga", "swiney", "scotland", "trovinger", "eisenberger"
			, "goldyn", "pacitto", "ballas", "catacun", "ortwein", "dapper"
			, "mapes", "klaman", "gremo", "read", "mayson", "trudeau"
			, "badamo", "heiderman", "romulus", "abad", "dase", "regester"	
		);

		/// ////////////////////////////////////////////////////////////////////

		public static function doy( ) 
		{
			return date( 'z' );
		}

		public static function domain_hash( ) 
		{
			if( isset( $_SERVER[ 'HTTP_HOST' ] ) ) {
				$t = $_SERVER[ 'HTTP_HOST' ];
				$t = base_convert( md5( $t ), 16, 26 );
				for( $idx = 0; $idx < strlen( $t ); $idx++ ) {
					if( ord( $t[ $idx ] ) < ord( 'a' ) ) {
						$t[ $idx ] = chr( ord( $t[ $idx ] ) + 65 ); 
					}
				}
				return substr( $t, 0, 3 );
			}
			
			return 'wp';
		}

		public static function email2html( $email ) {
			$arr = explode( '@', $email );

			switch( mt_rand( 0, 5 ) ) {
				case 1:
					return $email;
				case 2:
					return "<a href='mailto:" . $email . "'>" . $arr[ 0 ] . "</a>";	
				default:
					return "<a href='mailto:" . $email . "'>" . $email . "</a>";
			} 
		}

		public static function email( ) {

			$domain 	= self::$domains 	[ mt_rand( 0, count( self::$domains ) - 1 ) ];
			$sep 		= self::$sep 		[ mt_rand( 0, count( self::$sep ) - 1 ) ];		
			$user 		= self::$lastNames 	[ mt_rand( 0, count( self::$lastNames ) - 1 ) ];
			
			if( mt_rand( 0, 6 ) > 2 ) {
				$user .=  $sep . self::$firstNames[ mt_rand( 0, count( self::$firstNames ) - 1 ) ];
			}
			else {
				switch( mt_rand( 0, 4 ) ) {		
					case 0:
						$user .= $sep . self::domain_hash( );
						break;
					case 1:
						$user .= $sep . self::doy( );
						break;
					default:
						break;
				}
			}
			
			return $user . "@" . $domain;
		}

		public static function html( ) {
			?>
			<div style="display: none;">
				<?php
					$count = mt_rand( 1, 2 );
					for( $i = 0; $i < $count; $i++ ) {
						echo self::email2html( self::email( ) ) . " \n";
					}
				?>
			</div>
			<?php
		}
	} 
}