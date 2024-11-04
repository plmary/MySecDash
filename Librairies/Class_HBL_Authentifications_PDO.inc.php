<?php

include_once( 'Constants.inc.php' );
include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Autorisations_PDO.inc.php' );


define( 'L_IDN_LOGIN', 20 );
define( 'L_IDN_AUTHENTICATOR', 64 );
define( 'L_IDN_SALT', 32 );
define( 'L_DATE_TIME', 19 );


class HBL_Authentifications extends HBL_Autorisations {
/**
* Cette classe gère l'authentification des utilisateurs.
*
* \license Copyright Loxense
* \author Pierre-Luc MARY
* \date 2015-05-31
*
*/

	public function __construct() {
	/**
	* Connexion à la base de données.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-31
	*
	* \return Renvoi un booléen sur le succès de la connexion à la base de données
	*/
		parent::__construct();
		
		return true;
	}


	public function authentification( $Login, $Authenticator, $Type = 'D', $Salt = '' ) {
	/**
	* Contrôle les éléments d'authentification
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-31
	*
	* \param[in] $Login Nom de connexion de l'utilisateur
	* \param[in] $Authenticator Authentifiant de l'utilisageur.
	* \param[in] $Type Type d'authentification (par défaut : 'D = database', 'R = Radius')
	* \param[in] $Salt Grain de sel à utiliser pour calculer le hash du mot de passe
	*
	* \param[out] $_SESSION['idn_id'] Identifiant de l'utilisateur connecté
	* \param[out] $_SESSION['sct_id'] Identifiant de la société d'appartenance de l'utilisateur
	* \param[out] $_SESSION['ent_id'] Identifiant de l'entité d'appartenance de l'utilisateur
	* \param[out] $_SESSION['cvl_id'] Identifiant de la civilité de l'utilisateur
	* \param[out] $_SESSION['idn_login'] Nom de connexion de l'utilisateur
	* \param[out] $_SESSION['idn_changer_authentifiant'] Flag sur la nécessité de changer de mot de passe
	* \param[out] $_SESSION['idn_tentative'] Nombre de tentative de connexion
	* \param[out] $_SESSION['idn_date_modification_authentifiant'] Date de mise à jour du mot de passe
	* \param[out] $_SESSION['idn_derniere_connexion'] Date de dernière connexion
	* \param[out] $_SESSION['idn_super_admin'] Flag sur le droit Super Administrateur
	* \param[out] $_SESSION['cvl_nom'] Nom usuel de l'utilisateur
	* \param[out] $_SESSION['cvl_prenom'] Prénom de l'utilisateur
	* \param[out] $_SESSION['ent_nom'] Libellé de l'entité d'appartenance de l'utilisateur
	* \param[out] $_SESSION['Expired'] Temps d'expiration
	*
	* \exception Exception Exception standard. Le message retourné étant applicatif dans la majorité des cas
	*
	* \return Renvoi vrai en cas de succès ou génère une exception en cas d'erreur.
	*/
		include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_HBL_Autorisations.inc.php' );


		// -----------------------------------
		// Récupère les données de l'identité.
		$Request = 'SELECT ' .
		 'T1.idn_id, ' .
		 'T1.sct_id, ' .
		 'T1.idn_login, ' .
		 'T1.idn_grain_sel, ' .
		 'T1.idn_authentifiant, ' .
		 'T1.idn_changer_authentifiant, ' .
		 'T1.idn_tentative, ' .
		 'T1.idn_date_modification_authentifiant, ' .
		 'T1.idn_derniere_connexion, ' .
		 'T1.idn_super_admin, ' .
		 'T1.idn_desactiver, ' .
		 'T1.idn_date_expiration, ' .
		 'T2.cvl_id, ' .
		 'T2.cvl_nom, ' .
		 'T2.cvl_prenom,' .
		 'T3.sct_nom ' .
		 'FROM idn_identites AS T1 ' .
		 'LEFT JOIN cvl_civilites AS T2 ON T1.cvl_id = T2.cvl_id ' .
		 'LEFT JOIN sct_societes AS T3 ON T1.sct_id = T3.sct_id ' .
		 'WHERE LOWER(T1.idn_login) = :Login ' ;
		
		$Query = $this->prepareSQL( $Request );		

		$this->bindSQL( $Query, ':Login', strtolower($Login), PDO::PARAM_STR, L_IDN_LOGIN );

		$Type = strtoupper( $Type );		 
		 
		$this->executeSQL( $Query );

		$Occurrence = $Query->fetchObject();


		/* ---------------------------------------------------------------------
		** Si pas d'occurrence, alors nom d'utilisateur inconnu.
		*/
		if ( $Occurrence == '' ) {
			return FALSE;
		}


		/* ========================================
		** Test l'authentification reçu.
		*/
		$Cascading_Connection = $this->recupererParametre( 'root_alternative_boot' );

		do {
			switch( $Type ) {
			 // Gestion d'une connexion LDAP
			 case 'L':
				include( DIR_RESTREINT . '/Config_LDAP.inc.php' );
				
				//$LDAP_RDN = $_LDAP_RDN_Prefix . '=' . $Login . ',' . $_LDAP_Organization;
				$LDAP_RDN = $_LDAP_RDN_Prefix . $Login . $_LDAP_Organization;

				// Connexion au serveur LDAP
				try {
					if ( $_LDAP_SSL === TRUE ) {
						$_LDAP_Server = 'ldaps://' . $_LDAP_Server . '/';
						$_LDAP_Port = $_LDAP_SSL_Port;
					}

					$ldap_c = ldap_connect( $_LDAP_Server, $_LDAP_Port );

					if ( $ldap_c ) {
/* Non compatible avec un AD (à suivre).
 						if ( ldap_set_option( $ldap_c, LDAP_OPT_PROTOCOL_VERSION, $_LDAP_Protocol_Version ) === FALSE ) {
							throw new Exception( ldap_error( $ldap_c ), ldap_errno( $ldap_c ) );
						} */

						// Authentification au serveur LDAP
						$ldap_b = @ldap_bind( $ldap_c, $LDAP_RDN, $Authenticator );

						// Vérification de l'authentification
						if ( ldap_errno( $ldap_c ) != 0 ) {
							if ( $Cascading_Connection == 'TRUE' and $Occurrence->idn_super_admin == TRUE ) {
								$Type = 'D'; // Redirige pour faire un test dans la base interne.
							} else {

								if ( ldap_errno( $ldap_c ) == 49 ) {
									$this->incrementeTentative( $Occurrence->idn_login );
									
									return FALSE;
								} else {
									throw new Exception( ldap_error( $ldap_c ).' (RDN: '.$LDAP_RDN.')', ldap_errno( $ldap_c ) );
								}
							}
						}
					} else {
						throw new Exception( ldap_error( $ldap_c ), ldap_errno( $ldap_c ) );
					}
				} catch ( Exception $e ) {
					if ( $Cascading_Connection == 'TRUE' and $Occurrence->idn_super_admin == TRUE ) {
						$Type = 'D'; // Redirige pour faire un test dans la base interne.
					} else {
						throw new Exception( ldap_error( $ldap_c ), ldap_errno( $ldap_c ) );
					}
				}

				break;
	
			 case 'D':
				/* --------------------------------------------------------------------------------------
				** Vérifie si le mot de passe de l'utilisateur est cohérent avec le grain de sel en plus.
				*/
				$Authenticator = hash( 'sha256', $Authenticator . $Occurrence->idn_grain_sel, FALSE );

				if ( $Authenticator != $Occurrence->idn_authentifiant ) {
					$this->incrementeTentative( $Occurrence->idn_login );
					
					return FALSE;
				}

				$Cascading_Connection = 'FALSE'; // Arrête la boucle de connexion en cascade.

				break;
			}
		} while( $Cascading_Connection == 'TRUE' and $Occurrence->idn_super_admin == TRUE );


		/* ---------------------------------------------
		** Vérifie si l'utilisateur n'est pas désactivé.
		*/
		if ( $Occurrence->idn_desactiver == 1 ) {
			throw new Exception( $L_Utilisateur_Desactive, -1 );
		}


		/* ----------------------------------------
		** Vérifie si l'utilisateur n'a pas expiré.
		** Date d'expiration dépassée.
		*/
		if ( $Occurrence->idn_date_expiration != '0000-00-00 00:00:00' && $Type != 'L' ) {
			if ( $Occurrence->idn_date_expiration < date( 'Y-m-d' ) ) {
				throw new Exception( $L_Utilisateur_Expire . '<br/>' .
				 $L_Date_Expiration_Atteinte, -1 );
			}
		}
		
		
		/* -----------------------------------------------------------------
		** Vérifie si le nombre de tentative de connexion n'est pas dépassé.
		*/
		$Nombre_Tentative = $this->recupererParametre( 'nombre_tentative' );
		if ( $Nombre_Tentative == '' ) $Nombre_Tentative = 3;

		if ( $Occurrence->idn_tentative > $Nombre_Tentative ) {
			throw new Exception( $L_Nombre_Connexion_Atteinte, -1 );
		}


		// -----------------------------------
		// Met à jour la date de connexion.
		$Request = 'UPDATE idn_identites SET ' .
		 'idn_tentative = 0, ' .
		 'idn_derniere_connexion = \'' . date( 'Y-m-d H:i:s' ) . '\' ' .
		 'WHERE idn_id = ' . $Occurrence->idn_id ;

		$Query = $this->prepareSQL( $Request );		 
		 
		$this->executeSQL( $Query );


		$_SESSION[ 'idn_id' ] = $Occurrence->idn_id ;
		$_SESSION[ 'sct_id' ] = $Occurrence->sct_id ;
//		$_SESSION[ 'ent_id' ] = $Occurrence->ent_id ;
		$_SESSION[ 'cvl_id' ] = $Occurrence->cvl_id ;
		$_SESSION[ 'idn_login' ] = $Occurrence->idn_login ;
		$_SESSION[ 'idn_changer_authentifiant' ] = $Occurrence->idn_changer_authentifiant ;
		$_SESSION[ 'idn_tentative' ] = $Occurrence->idn_tentative ;
		$_SESSION[ 'idn_date_modification_authentifiant' ] =
			$Occurrence->idn_date_modification_authentifiant ;
		$_SESSION[ 'idn_derniere_connexion' ] = $Occurrence->idn_derniere_connexion ;
		$_SESSION[ 'idn_super_admin' ] = $Occurrence->idn_super_admin ;

		$_SESSION[ 'cvl_nom' ] = $this->protection_XSS(
			$Occurrence->cvl_nom );
		$_SESSION[ 'cvl_prenom' ] = $this->protection_XSS( 
			$Occurrence->cvl_prenom );

		$_SESSION[ 'sct_nom' ] = $this->protection_XSS( $Occurrence->sct_nom );

		include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Autorisations_PDO.inc.php' );
		$Authorizations = new HBL_Autorisations();
		$Authorizations->sauverTempsSession();

 		return TRUE;
	}


	public function estConnecte() {
	/** -----------------------------
	* Contrôle si l'utilisateur est déjà connecté.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-31
	*
	* \return Retourne vrai si l'utilisateur est connecté. Sinon, retourne faux.
	*/
		if ( isset( $_SESSION[ 'idn_id' ] ) ) {
			return TRUE;
		}

		return FALSE;
	}


	public function deconnecter() {
	/** -----------------------------
	* Détruit les variables de la session.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-31
	*
	* \return Retourne toujours vrai
	*/
		$_SESSION = array();

		return TRUE;
	}


	public function estAdministrateur() {
	/** -----------------------------
	* Contrôle si l'utilisateur connecté est un administrateur.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-31
	*
	* \return Retourne vrai si l'utilisateur est un administrateur, sinon retourne faux
	*/
		if ( $_SESSION[ 'idn_super_admin' ] == TRUE ) {
			return TRUE;
		}

		return FALSE;
	}


	public function reinitialiserMotPasse( $idn_id ) {
	/** -----------------------------
	* Ecrase le mot de passe de l'utilisateur par celui par défaut.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-31
	*
	* \param[in] $idn_id Identifiant de l'utilisateur
	*
	* \return Retourne vrai en cas de succès, sinon lève une exception en cas d'erreur
	*/
		include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_MySecDash-Utilisateurs.php' );
		
		// ===========================================================
		// Calcule un nouveau grain de sel spécifique à l'utilisateur.
		$size = $this->recupererParametre( 'taille_min_password' );
		$complexity = $this->recupererParametre( 'complexite_password' );
		
		$Salt = $this->genererMotPasse( $size, $complexity );


		// ===================================
		// Récupère les données de l'identité.
		$Request = 'UPDATE idn_identites SET ' .
		 'idn_changer_authentifiant = TRUE, ' .
		 'idn_authentifiant = :Authenticator, ' .
		 'idn_grain_sel = :Salt, ' .
		 'idn_date_modification_authentifiant = \'' . date( 'Y-m-d H:i:s' ) . '\' ' .
		 'WHERE idn_id = :idn_id ' ;
		 
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':idn_id', $idn_id, PDO::PARAM_INT );
		
		$Authenticator = hash( 'sha256', $this->recupererParametre('default_password') . $Salt, FALSE );
		
		$this->bindSQL( $Query, ':Authenticator', $Authenticator,
		 PDO::PARAM_STR, L_IDN_AUTHENTICATOR );
		 
		$this->bindSQL( $Query, ':Salt', $Salt, PDO::PARAM_STR, L_IDN_SALT );
		 
		 
		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

 		return TRUE;
	}
	


	public function changerMotPasse( $Idn_Id, $O_Password, $N_Password ) {
	/** -----------------------------
	* Change le mot de passe de l'utilisateur
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-06-01
	*
	* \param[in] $idn_id Identifiant de l'utilisateur
	* \param[in] $O_Password Ancien mot de passe
	* \param[in] $N_Password Nouveau mot de passe
	*
	* \return Retourne vrai en cas de succès, sinon lève une exception en cas d'erreur
	*/
		// ===========================================================
		// Calcule un nouveau grain de sel spécifique à l'utilisateur.
		$size = 16;
		$complexity = 2; // Majuscules, Minuscules et Chiffres
		
		$Salt = $this->genererMotPasse( $size, $complexity );

		// ===================================
		// Récupère les données de l'identité.
		$Request = 'UPDATE idn_identites SET ' .
		 'idn_authentifiant = :N_Password, ' .
		 'idn_grain_sel = :Salt, ' .
		 'idn_date_modification_authentifiant = \'' . date( 'Y-m-d H:i:s' ) . '\', ' .
		 'idn_changer_authentifiant = FALSE ' .
		 'WHERE idn_id = :Idn_Id AND idn_authentifiant = :O_Password ' ;
		 
		$Query = $this->prepareSQL( $Request );
		
		
		$this->bindSQL( $Query, ':Idn_Id', $Idn_Id, PDO::PARAM_INT );
		
		$this->bindSQL( $Query, ':Salt', $Salt, PDO::PARAM_STR, L_IDN_SALT );
		

		$N_Password = hash( 'sha256', $N_Password . $Salt, FALSE );
		
		$this->bindSQL( $Query, ':N_Password', $N_Password,
		 PDO::PARAM_STR, L_IDN_AUTHENTICATOR );
		 

		$Old_Salt = $this->recupereGrainSel( '', $Idn_Id );

		$O_Password = hash( 'sha256', $O_Password . $Old_Salt, FALSE );
		
		$this->bindSQL( $Query, ':O_Password', $O_Password,
		 PDO::PARAM_STR, L_IDN_AUTHENTICATOR );

		
		$Result = $this->executeSQL( $Query );

		if ( $Result->rowCount() == 0 ) return FALSE;

 		return TRUE;
	}



	public function reinitialiserTentative( $idn_id ) {
	/** -----------------------------
	* Remet à zéro le nombre de tentative de connexion.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-06-01
	*
	* \param[in] $idn_id Identifiant de l'utilisateur
	*
	* \return Retourne vrai en cas de succès, sinon lève une exception en cas d'erreur
	*/

		// ===================================
		// Récupère les données de l'identité.
		$Request = 'UPDATE idn_identites SET ' .
		 'idn_tentative = 0 ' .
		 'WHERE idn_id = :idn_id ' ;
		 
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':idn_id', $idn_id, PDO::PARAM_INT );
		 
		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) return FALSE;

 		return TRUE;
	}



	public function reinitialiserDateExpiration( $idn_id, $Flag_Date = FALSE ) {
	/** -----------------------------
	* Réactualise la date d'expiration de l'utilisateur.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-06-01
	*
	* \param[in] $idn_id Identifiant de l'utilisateur
	*
	* \return Retourne vrai en cas de succès, sinon lève une exception en cas d'erreur
	*/
		// ===================================
		// Récupère les données de l'identité.
		$Request = 'UPDATE idn_identites SET ' .
		 'idn_date_expiration = :idn_date_expiration ' .
		 'WHERE idn_id = :idn_id ' ;
		 
		$Query = $this->prepareSQL( $Request );

		$Account_Lifetime = $this->recupererParametre('account_lifetime');
		if ( $Account_Lifetime == '' ) $Account_Lifetime = 6;
		
		$NextDate  = date( "Y-m-d",
			mktime( 0, 0, 0, date("m") + $Account_Lifetime, date("d"), date("Y") ) );

		$this->bindSQL( $Query, ':idn_date_expiration', $NextDate,
		 PDO::PARAM_STR, L_DATE_TIME );


		$this->bindSQL( $Query, ':idn_id', $idn_id, PDO::PARAM_INT );		
		
		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) return FALSE;

		if ( $Flag_Date == TRUE ) return $NextDate;
		else return TRUE;
	}



	public function activerDesactiver( $idn_id, $Status ) {
	/** -----------------------------
	* Active ou désactive un utilisateur
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-06-01
	*
	* \param[in] $idn_id Identifiant de l'utilisateur
	* \param[in] $Status Statut d'activation de l'utilisateur (0 = active, 1 = désactive)
	*
	* \return Retourne vrai en cas de succès, sinon lève une exception en cas d'erreur
	*/
		// ===================================
		// Récupère les données de l'identité.
		$Request = 'UPDATE idn_identites SET ' .
		 'idn_desactiver = :Status ' .
		 'WHERE idn_id = :idn_id ' ;
		 
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':idn_id', $idn_id, PDO::PARAM_INT );
		
		$this->bindSQL( $Query, ':Status', $Status, PDO::PARAM_BOOL );		
		 
		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) return FALSE;

 		return TRUE;
	}


	public function incrementeTentative( $Login ) {
	/** -----------------------------
	* Incrémente le compteur de tentative de connexion (suite à une erreur de connexion).
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-06-01
	*
	* \param[in] $Login Nom de l'utilisateur à traiter
	*
	* \return Retourne la nouvelle valeur du nombre de tentative ou lève une exception en cas d'erreur.
	*/
		// ===================================
		// Récupère la dernière valeur de tentative de connexion.
		$Request = 'SELECT idn_tentative FROM idn_identites ' .
		 'WHERE idn_login = :Login ' ;
		 
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':Login', $Login, PDO::PARAM_STR, L_IDN_LOGIN );
		
		$this->executeSQL( $Query );

		if ( $Query->rowCount() == 0 ) return 0;

		$Occurrence = $Query->fetchObject();

		$Attempt = $Occurrence->idn_tentative + 1;

		$Request = 'UPDATE idn_identites SET ' .
		 'idn_tentative = :Attempt ' .
		 'WHERE idn_login = :Login ' ;
		 
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':Login', $Login, PDO::PARAM_STR, L_IDN_LOGIN );

		$this->bindSQL( $Query, ':Attempt', $Attempt, PDO::PARAM_INT );
		 
		$this->executeSQL( $Query );

		if ( $Query->rowCount() == 0 ) return FALSE;

 		return $Attempt;
	}


	public function recupereGrainSel( $idn_login, $idn_id = '' ) {
	/** -----------------------------
	** Récupère le grain de sel de l'utilisateur
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-06-01
	*
	* \param[in] $idn_login Nom de l'utilisateur à traiter
	*
	* \return Retourne le grain de sel ou lève une exception en cas d'erreur.
	*/

		// ===================================
		// Récupère les données de l'identité.
		$Request = 'SELECT idn_grain_sel ' .
		 'FROM idn_identites ';
		 
		if ( $idn_id != '' ) {
			$Request .= 'WHERE idn_id = :idn_id ';
		} else {
			$Request .= 'WHERE idn_login = :idn_login ';
		}
		 
		$Query = $this->prepareSQL( $Request );
		
		if ( $idn_id != '' ) {
			$this->bindSQL( $Query, ':idn_id', $idn_id, PDO::PARAM_INT );
		} else {
			$this->bindSQL( $Query, ':idn_login', $idn_login, PDO::PARAM_STR, L_IDN_LOGIN );
		}
		 
		$Result = $this->executeSQL( $Query );

		if ( $Result->rowCount() == 0 ) return FALSE;
		
		$Data = $Query->fetchObject();
		if ( $Data == '' ) {
			return false;
		}
	
		return $Data->idn_grain_sel;
	}

}

?>