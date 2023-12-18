<?php

include_once( 'Constants.inc.php' );
include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Parametres_PDO.inc.php' );

/**
* Cette classe gère les problématiques de sécurité. Tel que le contrôle des variables en
* entrées, en sortie (notamment pour l'affichage à l'écran) ou calcule des grains de sel,
* etc.
*
* PHP version 5
* @license Copyright Loxense
* @author Pierre-Luc MARY
* @date 2015-06-01
*
*/

class HBL_Securite extends HBL_Parametres {
	public $Extensions_Fichier;


	public function __construct() {
		parent::__construct();

		$this->Extensions_Fichier = array('pdf', 'odt', 'ods', 'odp', 'docx', 'xlsx', 'pptx', 'doc', 'xls', 'ppt', 'rtf', 'txt',
			'wps', 'sxw', 'log', 'jpg', 'jpeg', 'png', 'gif', 'tiff', 'tif', 'bmp' );

		return;
	}


	public function protection_XSS( $value, $type='ASCII' ) {
	/**
	* Anti-injection XSS (à utiliser avant l'affichage d'une variable).
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-06-01
	*
	* @param[in] $value Chaine de caractère à contrôler
	* @param[in] $type Type de la valeur à protéger
	*
	* @return Retourne le résultat protégé (prêt à l'affichage) ou faux
	*/

		switch( strtoupper( $type ) ) {
		 case 'NUMERIC' :
			if ( $numeric = ctype_digit( $value ) ) {
				return $value;
			} else return FALSE;
			break;

		 case 'ALPHA' :
			if ( $alpha = ctype_alpha( $value ) ) {
				$value = stripslashes( $value );
				$value = htmlspecialchars( $value, ENT_QUOTES, 'ISO-8859-15' );
				return $value;
			} else return FALSE;
			break;
	  	
		 case 'ALPHA-NUMERIC' :
			if ( $alnum = ctype_alnum( $value ) ) {
				$value = stripslashes( $value );
				$value = htmlspecialchars( $value );
				return $value;
			} else return FALSE;
			break;

		 case 'PRINTABLE' :
			if ( $alnum = ctype_print( $value ) ) {
				$value = stripslashes( $value );
				$value = htmlspecialchars( $value, ENT_QUOTES, 'ISO-8859-15' );
				return $value;
			} else return FALSE;
			break;
		  
		 default:
		 case 'ASCII':
			$value = stripslashes( $value );
			$value = htmlspecialchars( $value, ENT_QUOTES, 'ISO-8859-15' );
			return $value;
			break;
		}
	}


	public function controlerTypeValeur( $value, $type='ASCII' ) {
	/**
	* Contrôle et prépare les variables avant un stockage.
	* A utiliser avant l'affichage d'une variable.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-06-01
	*
	* @param[in] $value Chaine de caractère à contrôler
	*
	* @return Retourne le résultat protégé ou faux
	*/
		switch( strtoupper( $type ) ) {
		 case 'NUMERIC' :
			if ( $numeric = ctype_digit( $value ) ) {
				return $value;
			} else return -1 ;
			break;

		 case 'ALPHA' :
			if ( $alpha = ctype_alpha( $value ) ) {
				if ( mb_detect_encoding( $value ) != 'UTF-8' ) $value = utf8_encode( $value );
				$value = addslashes( $value );
				return $value;
			} else return -1 ;
			break;
	  	
		 case 'ALPHA-NUMERIC' :
			if ( $alnum = ctype_alnum( $value ) ) {
				if ( mb_detect_encoding( $value ) != 'UTF-8' ) $value = utf8_encode( $value );
				$value = addslashes( $value );
				return $value;
			} else return -1 ;
			break;

		 case 'PRINTABLE' :
			if ( $alnum = ctype_print( $value ) ) {
				if ( mb_detect_encoding( $value ) != 'UTF-8' ) $value = utf8_encode( $value );
				$value = addslashes( $value );
				return $value;
			} else return -1 ;
			break;

		 case 'BOOLEAN' :
			if ( strtoupper( $value ) == 'FALSE' or strtoupper( $value ) == 'TRUE'
			 or $value === FALSE or $value === TRUE or $value === false or $value === true
			 or $value === 0 or $value === 1 ) {
				if ( strtoupper( $value ) == 'FALSE' or $value === FALSE or $value === false or $value === 0 ) return FALSE;
				if ( strtoupper( $value ) == 'TRUE' or $value === TRUE or $value === true or $value === 1 ) return TRUE;
			} else return -1 ;
			break;
		  
		 default:
		 case 'ASCII':
			//$value = addslashes( $value );
			if ( mb_detect_encoding( $value ) != 'UTF-8' ) $value = utf8_encode( $value );
			return $value;
			break;
		}
	}


	public function protection_MySQL( $value ) {
	/**
	* Anti-injection SQL dans MySQL.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-06-01
	*
	* @param[in] $value Chaine de caractère à protéger
	*
	* @return Retourne le résultat protégé.
	*/
		return @mysql_real_escape_string( $value );
	}
	
	
 	
	public function supprimerAccentuation( $Value ) {
	/**
	* Supprime les caractères accentués d'une chaîne.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-06-01
	*
	* @param[in] $value Chaine de caractère à protéger
	*
	* @return Retourne le résultat protégé.
	*/
		return strtr( utf8_decode( $Value ),
		 utf8_decode( 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ' ),
		 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy' );
	}
 	
 	
	public function genererMotPasse( $size = 10, $complexity = 3 ) {
	/**
	* Générateur de mot de passe ou de grain de sel.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-06-01
	*
	* @param[in] $size Longeur du mot de passe à générer (par défaut 10 caractères)
	* @param[in] $complexity Complexité du mot de passe (constitution du mot de passe) (par défaut complexité à 4, soit le mot de passe doit être constitué de "minuscule", "majuscule", "numérique", "accentué" et caractères "spéciaux").
	*
	* @return Retourne la chaîne générée
	*/
		$accentuations = 'àçèéêëîïôöùûüÿ';
		$lowercase_letters = 'abcdefghijklmnopqrstuvwxyz';
		$uppercase_letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$numbers = '0123456789';
		$specials = '#@&(§!)-_*$£%+=/:.;,?><\\{}[]|';
 		
 		switch( $complexity ) {
 		 case 1:
	 		$caracters = $lowercase_letters . $uppercase_letters;
	 		break;

 		 case 2:
	 		$caracters = $lowercase_letters . $uppercase_letters . $numbers;
	 		break;

 		 case 3:
	 		$caracters = $lowercase_letters . $uppercase_letters . $numbers . $specials;
	 		break;

 		 default:
 		 case 4:
	 		$caracters = $lowercase_letters . $uppercase_letters . $numbers . $specials ;
	 		 $accentuations ;
	 		break;
	 	}

		$Password = '';
		 		 
 		for( $i = 0; $i < $size; $i++ )
 			$Password .= $caracters[ mt_rand( 0, (strlen( $caracters )-1) ) ];
 		
 		return $Password;
 	}
 	
 	
 	public function controlerComplexiteMotPasse( $Password, $complexity = 3 ) {
	/**
	* Vérifie si le mot de passe ou le grain de sel respecte la complexité spécifiée.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-06-02
	*
	* @param[in] $Password Mot de passe à contrôler
	* @param[in] $complexity Complexité du mot de passe (constitution du mot de passe) (par défaut complexité à 3, soit le mot de passe doit être constitué de "minuscules", "majuscules", "numériques" et caractères "spéciaux").
	*
	* @return Retourne vrai si la complexité est respectée et faux dans le cas contraire
	*/
		$Accentuation = 0;
		$Lowercase = 0;
		$Uppercase = 0;
		$Numbers = 0;
		$Specials = 0;
 		
 		$Size = strlen( $Password );
 		
 		for( $Position=0; $Position < $Size; $Position++ ) {
 			$Char = ord( $Password[ $Position ] );
 			
			if ( $Char >= 192 and $Char <= 255 ) $Accentuation = 1;

			if ( $Char >= 97 and $Char <= 122 ) $Lowercase = 1;

			if ( $Char >= 65 and $Char <= 90 ) $Uppercase = 1;

			if ( $Char >= 48 and $Char <= 57 ) $Numbers = 1;

			if ( ($Char >= 33 and $Char <= 46)
			 or ($Char >= 58 and $Char <= 64)
			 or ($Char >= 91 and $Char <= 96)
			 or ($Char >= 123 and $Char <= 191)
			 ) $Specials = 1;
		}

		$Status = false;

 		switch( $complexity ) {
 		 case 1:
	 		if ( $Lowercase == 1 and $Uppercase == 1) $Status = true;
	 		break;

 		 case 2:
	 		if ( $Lowercase == 1 and $Uppercase == 1 and $Numbers == 1 ) $Status = true;
	 		break;

 		 default:
 		 case 3:
	 		if ( $Lowercase == 1 and $Uppercase == 1 and $Numbers == 1
	 		 and $Specials == 1 ) $Status = true;
	 		break;

 		 case 4:
	 		if ( $Lowercase == 1 and $Uppercase == 1 and $Numbers == 1
	 		 and $Specials == 1 and $Accentuation ) $Status = true;
	 		break;
	 	}
 		
 		return $Status;
 	}
 	
 	
 	public function contientAccent( $String ) {
	/**
	* Vérifie si la chaîne contient un accent.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-06-02
	*
	* @param[in] $String Chaine à contrôler
	*
	* @return Retourne vrai si un accent est trouvé, sinon faux
	*/
		$Status = false;
		
		for( $Position = 0; $Position < strlen( $String ); $Position++ ) {
			if ( $String[ $Position ] >= 192 and $String[ $Position ] <= 255 ) {
				$Status = true;
				break;
			}
		}
 		
 		return $Status;
 	}
 	
 	
 	public function contientMinuscule( $String ) {
	/**
	* Vérifie si la chaîne contient une minuscule.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-06-02
	*
	* @param[in] $String Chaine à contrôler
	*
	* @return Retourne vrai si une minuscule est trouvée, sinon faux
	*/
		$Status = false;
		
		for( $Position = 0; $Position < strlen( $String ); $Position++ ) {
			if ( $String[ $Position ] >= 97 and $String[ $Position ] <= 122 ) {
				$Status = true;
				break;
			}
		}
 		
 		return $Status;
 	}
 	
 	
 	public function contientMajuscule( $String ) {
	/**
	* Vérifie si la chaîne contient une minuscule.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-06-02
	*
	* @param[in] $String Chaine à contrôler
	*
	* @return Retourne vrai si une majuscule est trouvée, sinon faux
	*/
		$Status = false;
		
		for( $Position = 0; $Position < strlen( $String ); $Position++ ) {
			if ( $String[ $Position ] >= 65 and $String[ $Position ] <= 90 ) {
				$Status = true;
				break;
			}
		}
 		
 		return $Status;
 	}
 	
 	
 	public function contientChiffre( $String ) {
	/**
	* Vérifie si la chaîne contient un nombre.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-06-02
	*
	* @param[in] $String Chaine à contrôler
	*
	* @return Retourne vrai si un nombre est trouvé, sinon faux
	*/
		$Status = false;
		
		for( $Position = 0; $Position < strlen( $String ); $Position++ ) {
			if ( $String[ $Position ] >= 48 and $String[ $Position ] <= 57 ) {
				$Status = true;
				break;
			}
		}
 		
 		return $Status;
 	}
 	
 	
 	public function contientSpecial( $String ) {
	/**
	* Vérifie si la chaîne contient un caractère spécial.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-06-02
	*
	* @param[in] $String Chaine à contrôler
	*
	* @return Retourne vrai si un caractère spécial est trouvé, sinon faux
	*/
		$Status = false;
		
		for( $Position = 0; $Position < strlen( $String ); $Position++ ) {
			if ( ($String[ $Position ] >= 33 and $String[ $Position ] <= 46)
			 or ($String[ $Position ] >= 58 and $String[ $Position ] <= 64)
			 or ($String[ $Position ] >= 91 and $String[ $Position ] <= 96)
			 or ($String[ $Position ] >= 123 and $String[ $Position ] <= 191) ) {
				$Status = true;
				break;
			}
		}
 		
		return $Status;
	}


	/* ===============================================================================
	** Gestion du Chiffrement
	*/
	
	public function mc_encrypt( $encrypt, $mc_key = '', $salt = '' ) {
	/**
	* Chiffrement d'une donnée.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-06-02
	*
	* @param[in] $encrypt Données à chiffrer.
	* @param[in] $mc_key Clé de chiffrement.
	* @param[in] $salt Diversifiant pour qu'une clé fasse toujours 32 caractères
	*
	* @return string Retourne la chaine de données chiffrée.
	*/

		if ( $salt == '' ) {
			include( HBL_DIR_LIBRARIES . '/Config_Hash.inc.php' );
			$salt = $_default_salt;
		}

		if ( $mc_key == '' ) {			
			$mc_key = $salt;
		} else {
			$mc_key = $mc_key . mb_substr( $salt, strlen( $mc_key ) );
		}
		
		$iv = mcrypt_create_iv(
		 mcrypt_get_iv_size( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND );

		$passcrypt = trim( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, $mc_key, trim($encrypt),
		 MCRYPT_MODE_ECB, $iv ) );

		$encode = base64_encode($passcrypt);
		
		return $encode;
	}


	public function mc_decrypt( $decrypt, $mc_key = '', $salt = '' ) {
	/**
	* Déchiffrement d'une donnée.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-06-02
	*
	* @param[in] $decrypt Données à déchiffrer.
	* @param[in] $mc_key Clé de déchiffrement.
	*
	* @return string Retourne la chaine de données déchiffrée.
	*/

		if ( $salt == '' ) {
			include( HBL_DIR_LIBRARIES . '/Config_Hash.inc.php' );
			$salt = $_default_salt;
		}

		if ( $mc_key == '' ) {			
			$mc_key = $salt;
		} else {
			$mc_key = $mc_key . mb_substr( $salt, strlen( $mc_key ) );
		}
		
		$decoded = base64_decode( $decrypt );
		
		$iv = mcrypt_create_iv(
		 mcrypt_get_iv_size( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB ), MCRYPT_RAND );

		$decrypted = trim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, $mc_key, trim( $decoded ),
		 MCRYPT_MODE_ECB, $iv ) );
	
		return $decrypted;
	}


	// ===========================
	// Chiffrement à double clés.

	public function initialisationDoubleCles() {
	/**
	* Mise en place du fichier de confiuguration de l'environnement de chiffrement.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2017-12-17
	*
	* @return chaine|TRUE Retourne TRUE pour une fin normale sinon remonte une erreur.
	*/
		if ( ! file_exists( CHF_CLES ) ) $Nom_Fichier = CHF_CLES;
		else $Nom_Fichier = CHF_CLES . '.new';

		$pFichier = fopen( $Nom_Fichier, 'w' );

		fwrite( $pFichier, '<?php' . "\n\n" );
		fwrite( $pFichier, '/** ' . "\n" );
		fwrite( $pFichier, '* @author Pierre-Luc MARY' . "\n" );
		fwrite( $pFichier, '* @date ' . date('Y-m-d') . "\n" );
		fwrite( $pFichier, '*/ ' . "\n\n" );
		fwrite( $pFichier, '	define( \'CLE_1\', \'' . base64_encode( openssl_random_pseudo_bytes( 32 ) ) . '\');' . "\n" );
		fwrite( $pFichier, '	define( \'CLE_2\', \'' . base64_encode( openssl_random_pseudo_bytes( 64 ) ) . '\');' . "\n\n" );
		fwrite( $pFichier, '	define( \'METHODE_CHIFFREMENT\', \'aes-256-cbc\' );' . "\n" );
		fwrite( $pFichier, '	define( \'METHODE_HACHAGE\', \'sha512\' );' . "\n" );
		fwrite( $pFichier, '	define( \'TAILLE_BLOC_CHIFFREMENT_FICHIER\', 65536 );' . "\n\n" );
		fwrite( $pFichier, '?>' . "\n" );

		fclose( $pFichier );

		return TRUE;
	}


	public function chiffrementDoubleCles( $Donnee ) {
	/**
	* Chiffrement d'une donnée par deux clés (avec les clés initialisées par le système).
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2017-12-17
	*
	* @param[in] $Donnee Chaine à chiffrer.
	*
	* @return chaine Retourne la donnée chiffrée.
	*/
		include_once( CHF_CLES );

		$Cle_1 = base64_decode( CLE_1 );
		$Cle_2 = base64_decode( CLE_2 );

		$IV_Taille = openssl_cipher_iv_length( METHODE_CHIFFREMENT );
		$IV = openssl_random_pseudo_bytes( $IV_Taille );
				
		$Chiffra_1 = openssl_encrypt( $Donnee, METHODE_CHIFFREMENT, $Cle_1, OPENSSL_RAW_DATA ,$IV );
		$Chiffra_2 = hash_hmac( METHODE_HACHAGE, $Chiffra_1, $Cle_2, TRUE );

		$Resultat = base64_encode( $IV . $Chiffra_2 . $Chiffra_1 );

		return $Resultat;
	}


	public function dechiffrementDoubleCles( $Donnee_Chiffree ) {
	/**
	* Déchiffrement d'une donnée par deux clés (avec les clés initialisées par le système).
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2017-12-17
	*
	* @param[in] $Donnee_Chiffree Chaine à déchiffrer.
	*
	* @return chaine Retourne la donnée déchiffrée.
	*/
		include_once( CHF_CLES );

		$Cle_1 = base64_decode( CLE_1 );
		$Cle_2 = base64_decode( CLE_2 );
		$Mixe = base64_decode( $Donnee_Chiffree );

		$IV_Taille = openssl_cipher_iv_length( METHODE_CHIFFREMENT );

		$IV = substr( $Mixe, 0, $IV_Taille );
		$Chiffra_2 = substr( $Mixe, $IV_Taille, 64 );
		$Chiffra_1 = substr( $Mixe, $IV_Taille + 64 );
			
		$Resultat = openssl_decrypt( $Chiffra_1, METHODE_CHIFFREMENT, $Cle_1, OPENSSL_RAW_DATA, $IV);
		$Nouveau_Chiffra_2 = hash_hmac( METHODE_HACHAGE, $Chiffra_1, $Cle_2, TRUE );

		if ( hash_equals( $Chiffra_2, $Nouveau_Chiffra_2 ) ) return $Resultat;

		return FALSE;
	}


	public function chiffrementCleUtilisateur( $Cle, $Donnee ) {
	/**
	* Chiffrement d'une donnée avec la clé de l'utilisateur.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2017-12-17
	*
	* @param[in] $Cle Clé à utiliser pour chiffrer.
	* @param[in] $Donnee Chaine à chiffrer.
	*
	* @return chaine Retourne la donnée chiffrée.
	*/
		include_once( CHF_CLES );

		$Cle_1 = base64_encode( $Cle );

		$IV_Taille = openssl_cipher_iv_length( METHODE_CHIFFREMENT );
		$IV = openssl_random_pseudo_bytes( $IV_Taille );
				
		$Chiffra_1 = openssl_encrypt( $Donnee, METHODE_CHIFFREMENT, $Cle_1, OPENSSL_RAW_DATA ,$IV );

		$Resultat = base64_encode( $IV . $Chiffra_1 );

		return $Resultat;
	}


	public function dechiffrementCleUtilisateur( $Cle, $Donnee_Chiffree ) {
	/**
	* Déchiffrement d'une donnée avec la clé de l'utilisateur.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2017-12-17
	*
	* @param[in] $Cle Clé à utiliser pour chiffrer.
	* @param[in] $Donnee_Chiffree Chaine à déchiffrer.
	*
	* @return chaine Retourne la donnée déchiffrée.
	*/
		include_once( CHF_CLES );

		$Cle_1 = base64_encode( $Cle );
		$Mixe = base64_decode( $Donnee_Chiffree );

		$IV_Taille = openssl_cipher_iv_length( METHODE_CHIFFREMENT );

		$IV = substr( $Mixe, 0, $IV_Taille );
		$Chiffra_1 = substr( $Mixe, $IV_Taille );
			
		$Resultat = openssl_decrypt( $Chiffra_1, METHODE_CHIFFREMENT, $Cle_1, OPENSSL_RAW_DATA, $IV);

		return $Resultat;
	}


	public function chiffrementCleInterne( $Donnee ) {
	/**
	* Chiffrement d'une donnée avec la clé de l'utilisateur.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2017-12-17
	*
	* @param[in] $Cle Clé à utiliser pour chiffrer.
	* @param[in] $Donnee Chaine à chiffrer.
	*
	* @return chaine Retourne la donnée chiffrée.
	*/
		include_once( CHF_CLES );

		$Cle_1 = base64_encode( CLE_2 );

		$IV_Taille = openssl_cipher_iv_length( METHODE_CHIFFREMENT );
		$IV = openssl_random_pseudo_bytes( $IV_Taille );
				
		$Chiffra_1 = openssl_encrypt( $Donnee, METHODE_CHIFFREMENT, $Cle_1, OPENSSL_RAW_DATA ,$IV );

		$Resultat = base64_encode( $IV . $Chiffra_1 );

		return $Resultat;
	}


	public function dechiffrementCleInterne( $Donnee_Chiffree ) {
	/**
	* Déchiffrement d'une donnée avec la clé de l'utilisateur.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2017-12-17
	*
	* @param[in] $Cle Clé à utiliser pour chiffrer.
	* @param[in] $Donnee_Chiffree Chaine à déchiffrer.
	*
	* @return chaine Retourne la donnée déchiffrée.
	*/
		include_once( CHF_CLES );

		$Cle_1 = base64_encode( CLE_2 );
		$Mixe = base64_decode( $Donnee_Chiffree );

		$IV_Taille = openssl_cipher_iv_length( METHODE_CHIFFREMENT );

		$IV = substr( $Mixe, 0, $IV_Taille );
		$Chiffra_1 = substr( $Mixe, $IV_Taille );
			
		$Resultat = openssl_decrypt( $Chiffra_1, METHODE_CHIFFREMENT, $Cle_1, OPENSSL_RAW_DATA, $IV);

		return $Resultat;
	}


	public function chiffrerFichierInterne( $Fichier_Source, $Fichier_Destination = '', $Supprimer_Source = 0 ) {
	/**
	* Chiffre le fichier (avec les clés initialisées par le système)
	* 
	* @param[in] $Fichier_Source Nom et localisation du fichier qui va être chiffré
	* @param[in] $Fichier_Destination Nom et localisation du fichier qui contient le résultat du chiffrement
	*			(par défaut le nom du de départ est utilisé avec le suffixe ".enc")
	* @param[in] $Supprimer_Source Si "1", alors le fichier source sera supprimé en fin normale de traitement.
	* 			Si "0", alors il n'y aura pas de suppression.
	*
	* @return string|false  Retourne le nom du fichier de destination qui a été créé ou FALSE si une erreur a été rencontrée
	*/
		include_once( CHF_CLES );

		$Erreur = FALSE;
		$Masque_Cryptographique = '';

		if ( $Fichier_Destination == '' ) $Fichier_Destination = $Fichier_Source . '.enc';

		if ( $pfDestination = fopen( $Fichier_Destination, 'wb' ) ) {
			for ($i = 0; $i <= floor(TAILLE_BLOC_CHIFFREMENT_FICHIER / strlen(CLE_2)); $i++) $Masque_Cryptographique .= CLE_2;
			
			if ( $pfSource = fopen( $Fichier_Source, 'rb' ) ) {
				while ( ! feof( $pfSource ) ) {
					$Texte = fread( $pfSource, TAILLE_BLOC_CHIFFREMENT_FICHIER );

					$Chiffra = $Texte ^ $Masque_Cryptographique;

					fwrite( $pfDestination, $Chiffra );
				}

				fclose( $pfSource );
			} else {
				$Erreur = TRUE;
			}

			fclose( $pfDestination );
		} else {
			$Erreur = TRUE;
		}

		if ( $Erreur == FALSE && $Supprimer_Source == 1 ) unlink( $Fichier_Source );

		return $Erreur ? FALSE : $Fichier_Destination;
	}


	public function dechiffrerFichierInterne( $Fichier_Source, $Fichier_Destination = '', $Supprimer_Source = 0 ) {
	/**
	* Déchiffre le fichier (avec les clés initialisées par le système)
	* 
	* @param[in] $Fichier_Source Nom et localisation du fichier qui va être déchiffré
	* @param[in] $Fichier_Destination Nom et localisation du fichier qui contient le résultat du déchiffrement
	*			(par défaut le nom du de départ est utilisé et on supprime le suffixe ".enc")
	* @param[in] $Supprimer_Source Si "1", alors le fichier source sera supprimé en fin normale de traitement.
	* 			Si "0", alors il n'y aura pas de suppression.
	*
	* @return string|false  Retourne le nom du fichier de destination qui a été créé ou FALSE si une erreur a été rencontrée
	*/
		include_once( CHF_CLES );

		$Erreur = FALSE;
		$Masque_Cryptographique = '';
		
		if ( $Fichier_Destination == '' ) {
			$_Tmp = pathinfo( $Fichier_Source );

			if ( $_Tmp['extension'] == 'enc' ) {
				$Fichier_Destination = $_Tmp['dirname'] . DIRECTORY_SEPARATOR . $_Tmp['filename'];
			} else {
				$Fichier_Destination = $Fichier_Source . '.dec';
			}
		}

		if ( $pfDestination = fopen( $Fichier_Destination, 'wb' ) ) {
			for ($i = 0; $i <= floor(TAILLE_BLOC_CHIFFREMENT_FICHIER / strlen(CLE_2)); $i++) $Masque_Cryptographique .= CLE_2;
			
			if ( $pfSource = fopen( $Fichier_Source, 'rb' ) ) {
				while ( ! feof( $pfSource ) ) {
					$Texte = fread( $pfSource, TAILLE_BLOC_CHIFFREMENT_FICHIER );

					$Clair = $Texte ^ $Masque_Cryptographique;

					fwrite( $pfDestination, $Clair );
				}

				fclose( $pfSource );
			} else {
				$Erreur = TRUE;
			}

			fclose( $pfDestination );
		} else {
			$Erreur = TRUE;
		}

		if ( $Erreur == FALSE && $Supprimer_Source == 1 ) unlink( $Fichier_Source );
		
		return $Erreur ? FALSE : $Fichier_Destination;
	}


	public function chiffrerFichierParCleUtilisateur( $Cle, $Fichier_Source, $Fichier_Destination = '', $Supprimer_Source = 0 ) {
	/**
	* Chiffre le fichier avec la clé précisé par l'utilisateur.
	* 
	* @param[in] $Cle Clé de l'utilisateur à utiliser pour chiffrer le fichier.
	* @param[in] $Fichier_Source Nom et localisation du fichier qui va être déchiffré.
	* @param[in] $Fichier_Destination Nom et localisation du fichier qui contient le résultat du déchiffrement
	*			(par défaut le nom du de départ est utilisé et on supprime le suffixe ".enc").
	* @param[in] $Supprimer_Source Si "1", alors le fichier source sera supprimé en fin normale de traitement.
	* 			Si "0", alors il n'y aura pas de suppression.
	*
	* @return string|false  Retourne le nom du fichier de destination qui a été créé ou FALSE si une erreur a été rencontrée.
	*/
		include_once( CHF_CLES );

		$Erreur = FALSE;
		$Masque_Cryptographique = '';

		if ( $Fichier_Destination == '' ) $Fichier_Destination = $Fichier_Source . '.enc';

		if ($pfDestination = fopen( $Fichier_Destination, 'wb' ) ) {
			for ($i = 0; $i <= floor(TAILLE_BLOC_CHIFFREMENT_FICHIER / strlen($Cle)); $i++) $Masque_Cryptographique .= $Cle;
			
			if ( $pfSource = fopen( $Fichier_Source, 'rb' ) ) {
				while ( ! feof( $pfSource ) ) {
					$Texte = fread( $pfSource, TAILLE_BLOC_CHIFFREMENT_FICHIER );

					$Chiffra = $Texte ^ $Masque_Cryptographique;

					fwrite( $pfDestination, $Chiffra );
				}

				fclose( $pfSource );
			} else {
				$Erreur = TRUE;
			}

			fclose( $pfDestination );
		} else {
			$Erreur = TRUE;
		}
		
		if ( $Erreur == FALSE && $Supprimer_Source == 1 ) unlink( $Fichier_Source );

		return $Erreur ? FALSE : $Fichier_Destination;
	}


	function dechiffrerFichierParCleUtilisateur( $Cle, $Fichier_Source, $Fichier_Destination = '', $Supprimer_Source = 0 ) {
	/**
	* Déchiffre le fichier avec la clé précisé par l'utilisateur.
	* 
	* @param[in] $Cle Clé de l'utilisateur à utiliser pour déchiffrer le fichier.
	* @param[in] $Fichier_Source Nom et localisation du fichier qui va être déchiffré
	* @param[in] $Fichier_Destination Nom et localisation du fichier qui contient le résultat du déchiffrement
	*			(par défaut le nom du de départ est utilisé et on supprime le suffixe ".enc")
	* @param[in] $Supprimer_Source Si "1", alors le fichier source sera supprimé en fin normale de traitement.
	* 			Si "0", alors il n'y aura pas de suppression.
	*
	* @return string|false  Retourne le nom du fichier de destination qui a été créé ou FALSE si une erreur a été rencontrée
	*/
		include_once( CHF_CLES );

		$Erreur = FALSE;
		$Masque_Cryptographique = '';

		if ( $Fichier_Destination == '' ) {
			$_Tmp = pathinfo( $Fichier_Source );

			if ( $_Tmp['extension'] == 'enc' ) {
				$Fichier_Destination = $_Tmp['dirname'] . DIRECTORY_SEPARATOR . $_Tmp['filename'];
			} else {
				$Fichier_Destination = $Fichier_Source . '.dec';
			}
		}

		if ( $pfDestination = fopen( $Fichier_Destination, 'wb' ) ) {
			for ($i = 0; $i <= floor(TAILLE_BLOC_CHIFFREMENT_FICHIER / strlen($Cle)); $i++) $Masque_Cryptographique .= $Cle;

			if ( $pfSource = fopen( $Fichier_Source, 'rb' ) ) {
				while ( ! feof( $pfSource ) ) {
					$Chiffra = fread( $pfSource, TAILLE_BLOC_CHIFFREMENT_FICHIER ); 

					$Clair = $Chiffra ^ $Masque_Cryptographique;

					fwrite( $pfDestination, $Clair );
				}

				fclose( $pfSource );
			} else {
				$Erreur = TRUE;
			}

			fclose( $pfDestination );
		} else {
			$Erreur = TRUE;
		}

		if ( $Erreur == FALSE && $Supprimer_Source == 1 ) unlink( $Fichier_Source );
		
		return $Erreur ? FALSE : $Fichier_Destination;
	}


	public function SSL_actif() {
		if ( isset($_SERVER['HTTPS']) ) {
			if ( 'on' == strtolower($_SERVER['HTTPS']) )
				return TRUE;
			if ( '1' == $_SERVER['HTTPS'] )
				return TRUE;
		} elseif ( isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
			return TRUE;
		}
		return FALSE;
	}

	
	public function ajouterEvenementDansHistorique( $Action_Date, $Action_Type, $Object_Type, $Message, $crs_id = NULL ) {
	/**
	* Met à jour l'historique interne des actions des utiilsateurs dans l'outil.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-08-19
	*
	* @param[in] $Action_Date (date) Code de l'action qui vient d'être réalisée
	* @param[in] $Action_Type (string) Code de l'action qui vient d'être réalisée
	* @param[in] $Object_Type (string) Code du type d'objet qui vient d'être accédé
	* @param[in] $Message (string) Code de l'action qui vient d'être réalisée
	* @param[in] $crs_id (int) Id de la Cartographie auquel l'événement est rattaché
	*
	* @return Renvoi vrai sur le succès de la mise à jour du Groupe, sinon lève une Exception
	*/

		// Récupère l'ID associé au code action.
		$Query = $this->prepareSQL( 'SELECT tpa_id FROM tpa_types_action ' .
			'WHERE tpa_code_libelle = :tpa_code_libelle ;' );
				
		$this->bindSQL( $Query, ':tpa_code_libelle', $Action_Type, PDO::PARAM_STR, 30 );

		$this->executeSQL( $Query );
		
		$Tmp = $Query->fetchObject();

		if ( $this->RowCount > 0 ) {
			$tpa_id = $Tmp->tpa_id;
		} else {
			throw new Exception('"' . $Action_Type . '" inexistant "action type" (atp_action_type)', 1000);
		}


		// Récupère l'ID associé au code objet.
		$Query = $this->prepareSQL( 'SELECT tpo_id FROM tpo_types_objet ' .
			'WHERE tpo_code_libelle = :tpo_code_libelle ;' );
				
		$this->bindSQL( $Query, ':tpo_code_libelle', $Object_Type, PDO::PARAM_STR, 30 );

		$this->executeSQL( $Query );
		
		$Tmp = $Query->fetchObject();

		if ( $this->RowCount > 0 ) {
			$tpo_id = $Tmp->tpo_id;
		} else {
			throw new Exception('"' . $Object_Type . '" inexistant "object type" (otp_object_type)', 1000);
		}


		if ( array_key_exists( 'idn_login', $_SESSION ) ) {
			$Login = $_SESSION[ 'idn_login' ];
		} else {
			$Login = '';
		}

		if ( array_key_exists( 'user_ip', $_SESSION ) ) {
			$user_ip = $_SESSION[ 'user_ip' ];
		} else {
			$user_ip = '';
		}

		$SQL = 'INSERT INTO hac_historiques_activites ' .
			'( ' .
			'tpa_id, ' .
			'tpo_id, ' .
			'hac_date, ' .
			'hac_utilisateur, ' .
			'hac_ip_utilisateur, ' .
			'hac_detail ';

		if ( $crs_id != NULL ) $SQL .= ', crs_id ';

		$SQL .= ') VALUES ( ' .
			':tpa_id, ' .
			':tpo_id, ' .
			':hac_date, ' . 
			':hac_utilisateur, ' .
			':hac_ip_utilisateur, ' .
			':hac_detail ';

		if ( $crs_id != NULL ) $SQL .= ', :crs_id ';
		
		$SQL .= '); ';

		$Request = $this->prepareSQL( $SQL );

		$this->bindSQL( $Request, ':tpa_id', $tpa_id, PDO::PARAM_INT );

		$this->bindSQL( $Request, ':tpo_id', $tpo_id, PDO::PARAM_INT );
		
		$this->bindSQL( $Request, ':hac_date', $Action_Date, PDO::PARAM_STR, 20 );

		$this->bindSQL( $Request, ':hac_utilisateur', $Login, PDO::PARAM_STR, 60 );

		$this->bindSQL( $Request, ':hac_ip_utilisateur', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR, 40 );
		
		$this->bindSQL( $Request, ':hac_detail', $Message, PDO::PARAM_STR, 300 );

		if ( $crs_id != NULL ) $this->bindSQL( $Request, ':crs_id', $crs_id, PDO::PARAM_INT );

		$this->executeSQL( $Request );
		
		return TRUE;
	}

	
	public function formaterMessagePourSyslog( $Action_Date, $Action_Type, $Object_Type, $Message ) {
	/**
	* Formate le message à remonter dans l'historique.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2015-08-17
	*
	* @param[in] $action Action à tracer.
	* @param[in] $pObject Pointeur sur le Secret manipulé
	*
	* @return Retourne la chaîne formatée ou lève une exception en cas d'erreur.
	*/
		include( HBL_DIR_LABELS . '/' . $this->recupererParametre( 'language_alert' ) . '_libelles_referentiels.php' );

		if ( isset( $_SESSION[ 'idn_login' ] ) ) {
			$idn_login = $_SESSION[ 'idn_login' ];
		} else {
			$idn_login = '';
		}

		if ( isset( $_SERVER[ 'REMOTE_ADDR' ] ) ) $Server = $_SERVER[ 'REMOTE_ADDR' ];
		else $Server = '';


		// Reformate le corps du Syslog
		$Template = @file_get_contents( DIR_RESTREINT . '/' . $this->recupererParametre( 'syslog_template' ) );

		$Template = str_ireplace( '%User', $idn_login, $Template );
		$Template = str_ireplace( '%UserIP', $Server, $Template );
		$Template = str_ireplace( '%ActionDate', $Action_Date, $Template );
		$Template = str_ireplace( '%ActionType', $Action_Type, $Template );
		$Template = str_ireplace( '%ObjectType', $Object_Type, $Template );
		$Template = str_ireplace( '%Message', $Message, $Template );

		return $Template;
	}	
 	
 	
	public function ecrireEvenementDansSyslog( $Action_Date, $Action_Type, $Object_Type, $Message, $priority = LOG_WARNING ) {
	/**
	* Envoi le message dans le flux "Syslog"
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2014-06-08
	*
	* @param[in] $action Action à tracer.
	* @param[in] $priority Type de priorité dans le "Syslog" (par défaut LOG_WARNING)
	*
	* Les autres valeurs de "priority" sont :
	*   LOG_EMERG	système inutilisable
	*   LOG_ALERT	une décision doit être prise immédiatement
	*   LOG_CRIT	condition critique
	*   LOG_ERR 	condition d'erreur
	*   LOG_WARNING	condition d'alerte
	*   LOG_NOTICE	condition normale, mais significative
	*   LOG_INFO	message d'information
	*   LOG_DEBUG	message de déboguage
	*
	* @return Retourne vrai si le message a été envoyé dans Syslog, sinon retrouve faux
	*/
		$message = $this->formaterMessagePourSyslog( $Action_Date, $Action_Type, $Object_Type, $Message );


		// Ouverture du syslog local, ajout du PID.
		if ( ! openlog( "Loxense", LOG_PID, LOG_USER ) ) {
			return false;
		}

		if ( ! syslog( $priority, $message ) ) {
			return false;
		}

		if ( ! closelog() ) {
			return false;
		}


		// Duplique éventuellement le syslog vers un autre serveur.
		if ( $this->recupererParametre( 'syslog_host' ) != '' ) {
			$Sock = socket_create( AF_INET, SOCK_DGRAM, SOL_UDP );

			socket_sendto( $Sock, $message, strlen( $message ), 0, $this->recupererParametre( 'syslog_host' ), $this->recupererParametre( 'syslog_port' ) );

			socket_close( $Sock );
		}
		
		return true;
	}
	
	
	public function envoyerEvenementParCourriel( $Action_Date, $Action_Type, $Object_Type, $Message ) {
	/**
	* Envoi le message par courriel
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2014-06-08
	*
	* @param[in] $Action Type d'action qui l'on vient de réaliser sur un Secret et que l'on souhaite notifier.
	* @param[in] $pSecret Objet de type Secret qui vient d'être accédé
	*
	* @return Retourne vrai si le message a été envoyé au serveur de messagerie, sinon retrouve faux (attention, envoyé au serveur de messagerie, ne signifie pas bien arrivé auprès des destinataires)
	*/

		$Sender = $this->recupererParametre('mail_sender');
		$Receiver = $this->recupererParametre('mail_receiver');
		$Subject = $this->recupererParametre('mail_title');
		$Output = $this->recupererParametre('mail_body_type');

		// Reformate le corps du Courriel
		$Template = file_get_contents( DIR_RESTREINT . '/' . $this->recupererParametre( 'mail_template' ) );

		if ( ! array_key_exists( 'idn_login', $_SESSION ) ) {
			$User = '';
		} else {
			$User = $_SESSION['idn_login'];
		}

		$Template = str_ireplace( '%User', $User, $Template );
		$Template = str_ireplace( '%ActionDate', $Action_Date, $Template );
		$Template = str_ireplace( '%ActionType', $Action_Type, $Template );
		$Template = str_ireplace( '%UserIP', $_SERVER['REMOTE_ADDR'], $Template );
		$Template = str_ireplace( '%ObjectType', $Object_Type, $Template );
		$Template = str_ireplace( '%Message', $Message, $Template );

		if ( $Output == 'HTML') {
			$Body = '
		<html>
		 <head>
		  <title>' . $Subject . '</title>
		 </head>
		 <body>
		  <p>' . $Template . '</p>
		 </body>
		</html>
			';
		} else {
			$Body = $Template;
		}

		// Pour envoyer un mail HTML, l'en-tête Content-type doit être défini
		$Headers = 'From: ' .$Sender . "\r\n";
		$Headers .= 'MIME-Version: 1.0' . "\r\n";

		if ( $Output == 'HTML' ) $Headers .= 'Content-type: text/html; charset="UTF-8"' . "\r\n";

		// Envoi
		return mail($Receiver, $Subject, $Body, $Headers);
	}


	public function recupererLibelle( $Label_Code, $Language_Code = 'fr' ) {
		/**
		* Récupère un libellé dans la table des libellés de l'outil.
		*
		* @license Loxense, 2015
		* @author Pierre-Luc MARY
		* @date 2015-08-21
		*
		* @param[in] $Label_Code Code du libellé à récupérer dans la base
		* @param[in] $Language_Code Code de la langue dans lequel on souhaite le libellé (par défaut "fr - France")
		*
		* @return Renvoi le libellé ou chaine vide quand le libellé n'existe pas.
		*/

			// ===================================
			// Récupère les données de l'identité.
			$Request = 'SELECT lbr_libelle FROM lbr_libelles_referentiel ' .
			 'WHERE lbr_code = :Label_Code AND lng_id = :Language_Code ' ;
			 
			$Query = $this->prepareSQL( $Request );


			$this->bindSQL( $Query, ':Label_Code', $Label_Code, PDO::PARAM_STR, 45 );
			
			$this->bindSQL( $Query, ':Language_Code', $Language_Code, PDO::PARAM_STR, 2 );
			

			$Result = $this->executeSQL( $Query );

			if ( $Result->rowCount() == 0 ) return '';

			$Data = $Query->fetchObject();

	 		return $Data->lbr_libelle;
	}


	public function ajouterLibelle( $Code, $Libelle, $Langue = 'fr' ) {
		/**
		* Ajoute un nouveau libellé.
		*
		* @license Loxense
		* @author Pierre-Luc MARY
		* @date 2018-04-05
		*
		* @param[in] $Code Code du libellé à ajouter dans la base
		* @param[in] $Libelle Libellé à associer au code et à ajouter dans la base
		* @param[in] $Language Code de la langue dans lequel on souhaite le libellé (par défaut "fr - France")
		*
		* @return Renvoi "TRUE" en cas de succes, sinon lève une exception.
		*/

			// ===================================
			// Récupère les données de l'identité.
			$Request = 'INSERT INTO lbr_libelles_referentiel ( lbr_code, lbr_libelle, lng_id ) 
VALUES ( :Code, :Libelle, :Langue ) ' ;
			 
			$Query = $this->prepareSQL( $Request );

			$this->bindSQL( $Query, ':Code', $Code, PDO::PARAM_STR, 45 )
				->bindSQL( $Query, ':Libelle', $Libelle, PDO::PARAM_LOB )
				->bindSQL( $Query, ':Langue', $Langue, PDO::PARAM_STR, 2 )
				->executeSQL( $Query );

	 		return TRUE;
	}
 

	public function modifierLibelle( $Code, $Libelle, $Langue = 'fr' ) {
		/**
		* Modifie un nouveau libellé.
		*
		* @license Loxense
		* @author Pierre-Luc MARY
		* @date 2018-04-05
		*
		* @param[in] $Code Code du libellé à modifier dans la base
		* @param[in] $Libelle Libellé à associer au code et à modifier dans la base
		* @param[in] $Language Code de la langue dans lequel on souhaite le libellé (par défaut "fr - France")
		*
		* @return Renvoi "TRUE" en cas de succes, sinon lève une exception.
		*/

			// ===================================
			// Récupère les données de l'identité.
			$Request = 'UPDATE lbr_libelles_referentiel 
SET lbr_libelle = :Libelle
WHERE lbr_code = :Code AND lng_id = :Langue ' ;
			 
			$Query = $this->prepareSQL( $Request );

			$this->bindSQL( $Query, ':Code', $Code, PDO::PARAM_STR, 45 )
				->bindSQL( $Query, ':Libelle', $Libelle, PDO::PARAM_LOB )
				->bindSQL( $Query, ':Langue', $Langue, PDO::PARAM_STR, 2 )
				->executeSQL( $Query );

	 		return TRUE;
	}
 	
 	
	public function ecrireEvenement( $Action_Type, $Object_Type, $Message, $Priority = LOG_WARNING, $crs_id = NULL ) {
	/**
	* Ecrit un événement (message) dans les LOG de l'outil.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-08-19
	*
	* @param[in] $Action_Type Type d'action qui vient d'être réalisé.
	* @param[in] $Object_Type Type de l'objet qui vient d'être accédé.
	* @param[in] $Message Alerte de sécurité à envoyer dans le courriel.
	* @param[in] $Priority Priorité de notification du message dans les SYSLOG.
	* Les valeurs possibles de "Priority" sont :
	*   LOG_EMERG	système inutilisable
	*   LOG_ALERT	une décision doit être prise immédiatement
	*   LOG_CRIT	condition critique
	*   LOG_ERR 	condition d'erreur
	*   LOG_WARNING	condition d'alerte
	*   LOG_NOTICE	condition normale, mais significative
	*   LOG_INFO	message d'information
	*   LOG_DEBUG	message de déboguage
	* @param[in] $crs_id Id de la Cartographie de rattachement.
	*
	* @return Retourne vrai si le message a été envoyé au serveur de messagerie, sinon retrouve faux (attention, envoyé au serveur de messagerie, ne signifie pas bien arrivé auprès des destinataires)
	*/
		$Action_Date = date( "Y-m-d H:i:s" );

		// Mise à jour de l'historique interne des actions de l'outil.
		$this->ajouterEvenementDansHistorique(
			$Action_Date,
			$Action_Type,
			$Object_Type,
			$Message,
			$crs_id
		);

//		$Message = $Action_Date . ' : ' . $Message;

		$Action_Type = $this->recupererLibelle( $Action_Type, $this->recupererParametre( 'language_alert' ) );
		$Object_Type = $this->recupererLibelle( $Object_Type, $this->recupererParametre( 'language_alert' ) );

		// Ecriture dans les SYSLOG (local et éventuellement distant).
		if ( $this->recupererParametre('syslog_alert') == 1 or $this->recupererParametre('syslog_alert') == 'true' ) $this->ecrireEvenementDansSyslog( $Action_Date, $Action_Type, $Object_Type, $Message );

		// Envoi d'un courriel.
		if ( $this->recupererParametre('mail_alert') == 1 or $this->recupererParametre('mail_alert') == 'true' ) $this->envoyerEvenementParCourriel( $Action_Date, $Action_Type, $Object_Type, $Message );

		return TRUE;
	}


	public function verifierSiCartographieModifiable( $crs_id ) {
	/**
	* Vérifie si une Cartographie est modifiable (statut de la cartographie supérieur à 2).
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-08-19
	*
	* @param[in] $crs_id ID de la Cartographie à contrôler
	*
	* @return Retourne "vrai" si la Cartographie est modifiable, "faux" si non
	*/
		if ( $crs_id == '' ) {
			unset($_SESSION['CARTOGRAPHIE_SEL']);
			return FALSE;
		}

		$sql = 'SELECT crs_statut FROM crs_cartographies_risques AS "crs" WHERE crs.crs_id = :crs_id ';

		$requete = $this->prepareSQL( $sql );

		$statut = $this->bindSQL($requete, ':crs_id', $crs_id, PDO::PARAM_INT)
			->executeSQL($requete)
			->fetchObject();

		if ($statut == '') {
			unset($_SESSION['CARTOGRAPHIE_SEL']);
			return FALSE;
		} else {
			$statut = $statut->crs_statut;
		}

		if ( $statut > 2 ) return FALSE;
		else return TRUE;
	}


	public function entiteCartographie( $crs_id ) {
		$sql = 'SELECT ent_id FROM crs_cartographies_risques WHERE crs_id = :crs_id ';

		$requete = $this->prepareSQL( $sql );

		return $this->bindSQL($requete, ':crs_id', $crs_id, PDO::PARAM_INT)
			->executeSQL($requete)
			->fetchObject()->ent_id;
	}
}  // Fin class "HBL_Securite"


function return_bytes($val) {
	if(empty($val))return 0;

	$val = trim($val);
	preg_match('#([0-9]+)[\s]*([a-z]+)#i', $val, $matches);

	$last = '';

	if(isset($matches[2])){
		$last = strtolower($matches[2]);
	}

	if(isset($matches[1])){
		$val = (int) $matches[1];
	}

	switch($last) {
		case 'g':
		case 'gb':
			$val *= 1024 * 1024 * 1024;
		case 'm':
		case 'mb':
			$val *= 1024 * 1024;
		case 'k':
		case 'kb':
			$val *= 1024;
	}
	
	return $val;
}
?>