<?php

include_once( 'Constants.inc.php' );
//include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Connecteur_BD_PDO.inc.php' );
include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Parametres_PDO.inc.php' );

if ( ! defined( 'L_IDN_ATTEMPT' ) ) define( 'L_IDN_ATTEMPT', 35 );
if ( ! defined( 'L_IDN_AUTHENTICATOR' ) ) define( 'L_IDN_AUTHENTICATOR', 64 );
if ( ! defined( 'L_IDN_SALT' ) ) define( 'L_IDN_SALT', 32 );
if ( ! defined( 'L_IDN_LOGIN' ) ) define( 'L_IDN_LOGIN', 20 );
if ( ! defined( 'L_IDN_EMAIL' ) ) define( 'L_IDN_EMAIL', 100 );
if ( ! defined( 'L_IDN_EXPIRATION_DATE' ) ) define( 'L_IDN_EXPIRATION_DATE', 22 );
if ( ! defined( 'L_IDN_UPDATED_AUTHENTICATION' ) ) define( 'L_IDN_UPDATED_AUTHENTICATION', 22 );

if ( ! defined( 'L_CVL_LAST_NAME' ) ) define( 'L_CVL_LAST_NAME', 35 );
if ( ! defined( 'L_CVL_FIRST_NAME' ) ) define( 'L_CVL_FIRST_NAME', 25 );
if ( ! defined( 'L_CVL_BORN_TOWN' ) ) define( 'L_CVL_BORN_TOWN', 40 );
if ( ! defined( 'L_ENT_LABEL' ) ) define( 'L_ENT_LABEL', 35 );


class HBL_Identites extends HBL_Parametres {
/**
* Cette classe gère les Identités.
* Les "Identités" représentent les utilisateurs du système.
*
* PHP version 5
* @license Copyright Loxense
* @author Pierre-Luc MARY
* @date 2015-05-27
*/

	public function __construct() {
	/**
	* Connexion à la base de données.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-27
	*
	* @return Renvoi un booléen sur le succès de la connexion à la base de données
	*/
		parent::__construct();
		
		return true;
	}


	/* ===============================================================================
	** Gestion des Identités
	*/
	
	public function majIdentite( $idn_id, $Login, $Authenticator, $SuperAdmin, $Id_Entity, $Id_Civility, $Email = '' ) {
	/**
	* Créé ou actualise une Identité.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @version 1.0
	* @date 2016-11-13
	*
	* @param[in] $idn_id Identifiant de l'identité à modifier (si précisé)
	* @param[in] $Login Nom de connexion de l'utilisateur
	* @param[in] $Authenticator Mot de passe de l'utilisateur
	* @param[in] $ChangeAuthenticator Booléen pour indiquer s'il faut changer le mot de passe
	* @param[in] $Attempt Nombre de tentative de connexion
	* @param[in] $SuperAdmin Booléen pour indiquer si l'utilisateur est un Administrateur
	* @param[in] $Id_Entity Identifiant de l'Entité de rattachement de l'utilisateur
	* @param[in] $Id_Civility Identifiant de la Civilité de rattachement de l'utilisateur
	* @param[in] $Email Adresse courriel de l'utilisateur
	*
	* @return Renvoi vrai si l'Identité a été créée ou modifiée, sinon lève une exception
	*/
		if ( $idn_id == '' ) {
			$Command = 'INSERT : ' ;

			$Request = 'INSERT INTO idn_identites (' .
			 'ent_id, ' .
			 'cvl_id, ' .
			 'idn_login, ' .
			 'idn_courriel, ' .
			 'idn_authentifiant, ' .
			 'idn_changer_authentifiant, ' .
			 'idn_tentative, ' .
			 'idn_date_expiration, ' .
			 'idn_date_modification_authentifiant, ' .
			 'idn_super_admin, ' .
			 'idn_grain_sel ' .
			 ') VALUES ( ' .
			 ':ent_id, ' .
			 ':cvl_id, ' .
			 ':idn_login, ' .
			 ':idn_courriel, ' .
			 ':idn_authentifiant, ' .
			 'TRUE, ' .
			 '0, ' .
			 ':idn_date_expiration, ' .
			 ':idn_date_modification_authentifiant, ' .
			 ':idn_super_admin, ' .
			 ':idn_grain_sel )' ;
			 
			$Query = $this->prepareSQL( $Request );
         

			// Génère un "sel" de chiffrement et chiffre le mot de passe reçu.
			$size = $this->recupererParametre( 'min_password_size' );
			$complexity = $this->recupererParametre( 'password_complexity' );
			
			require_once( HBL_DIR_LIBRARIES . '/Class_HBL_Securite.inc.php' );
			$Security = new HBL_Securite();

			$Salt = $Security->genererMotPasse( $size, 2 );

			$Authenticator = hash( 'sha256', $this->recupererParametre('default_password') . $Salt, FALSE );

			$this->bindSQL( $Query, ':idn_authentifiant', $Authenticator, PDO::PARAM_STR, L_IDN_AUTHENTICATOR );

			$NextDate  = strftime( "%Y-%m-%d", time() );

			$this->bindSQL( $Query, ':idn_date_modification_authentifiant', $NextDate, PDO::PARAM_STR, L_IDN_UPDATED_AUTHENTICATION );

			$this->bindSQL( $Query, ':idn_grain_sel', $Salt, PDO::PARAM_STR, L_IDN_SALT );

			if ( $SuperAdmin == '' or $_SESSION['idn_super_admin'] === FALSE ) $SuperAdmin = FALSE;

			$this->bindSQL( $Query, ':idn_super_admin', $SuperAdmin, PDO::PARAM_BOOL );

		} else {
			$Command = 'UPDATE : ' ;

			$Request = 'UPDATE idn_identites SET ' .
			 'ent_id = :ent_id, ' .
			 'cvl_id = :cvl_id, ' .
			 'idn_login = :idn_login, ' .
			 'idn_courriel = :idn_courriel, ' .
			 'idn_date_expiration = :idn_date_expiration ';

			if ( $SuperAdmin !== '' and  $_SESSION['idn_super_admin'] === TRUE ) $Request .= ', idn_super_admin = :idn_super_admin ';

			$Request .= ' WHERE idn_id = :idn_id';

			$Query = $this->prepareSQL( $Request );

			$this->bindSQL( $Query, ':idn_id', $idn_id, PDO::PARAM_INT );

			if ( $SuperAdmin !== '' and  $_SESSION['idn_super_admin'] === TRUE ) $this->bindSQL( $Query, ':idn_super_admin', $SuperAdmin, PDO::PARAM_BOOL );
		}

		$this->bindSQL( $Query, ':ent_id', $Id_Entity, PDO::PARAM_INT );
		
		$this->bindSQL( $Query, ':cvl_id', $Id_Civility, PDO::PARAM_INT );

		$this->bindSQL( $Query, ':idn_login', $Login, PDO::PARAM_STR, L_IDN_LOGIN );

		$this->bindSQL( $Query, ':idn_courriel', $Email, PDO::PARAM_STR, L_IDN_EMAIL );

		$NextDate  = 
		strftime( "%Y-%m-%d",
		 mktime( 0, 0, 0, date("m") + $this->recupererParametre('account_lifetime'), date("d"), date("Y") ) );

		$this->bindSQL( $Query, ':idn_date_expiration', $NextDate, PDO::PARAM_STR, L_IDN_EXPIRATION_DATE );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		if ( $idn_id == '' ) {
			switch( $this->getAttribute(PDO::ATTR_DRIVER_NAME) ) {
			 default;
				$this->LastInsertId = $this->lastInsertId();
				break;

			 case 'pgsql';
				$this->LastInsertId = $this->lastInsertId( 'idn_identites_idn_id_seq' );
				break;
			}
		}
		
		return TRUE;
	}


	public function majIdentiteParChamp( $idn_id, $Source, $Valeur ) {
	/**
	* Actualise une Identité.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-11-02
	*
	* @param[in] $ID Identifiant de l'Identité à modifier
	* @param[in] $Source Nom du champ à modifier
	* @param[in] $Valeur Valeur à affecter au champ.
	*
	* @return Renvoi TRUE si l'Identité a été mise à jour, FALSE si l'Identité n'existe pas. Lève une Exception en cas d'erreur.
	*/
		if ( $idn_id == '' ) return FALSE;

		$Request = 'UPDATE idn_identites SET ';

		switch ( $Source ) {
		 	case 'cvl_label':
				$Request .= 'cvl_id = :Valeur ';
		 		break;
		 	
		 	case 'ent_libelle':
				$Request .= 'ent_id = :Valeur ';
		 		break;
		 	
		 	case 'idn_login':
				$Request .= 'idn_login = :Valeur ';
		 		break;
		 	
		 	case 'idn_super_admin':
				$Request .= 'idn_super_admin = :Valeur ';
		 		break;
		 	
		 	case 'idn_desactiver':
				$Request .= 'idn_desactiver = :Valeur ';
		 		break;
		}

		$Request .= 'WHERE idn_id = :idn_id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':idn_id', $idn_id, PDO::PARAM_INT );


		switch ( $Source ) {
		 	case 'cvl_label':
		 	case 'ent_libelle':
				$this->bindSQL( $Query, ':Valeur', $Valeur, PDO::PARAM_INT );
		 		break;
		 	
		 	case 'idn_login':
				$this->bindSQL( $Query, ':Valeur', $Valeur, PDO::PARAM_STR, L_IDN_LOGIN );
		 		break;
		 	
		 	case 'idn_super_admin':
		 	case 'idn_desactiver':
				$this->bindSQL( $Query, ':Valeur', $Valeur, PDO::PARAM_BOOL );
		 		break;
		}

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}


		return TRUE;
	}


	public function majAuthentifiant( $idn_id, $Authenticator ) {
	/**
	* Met à jour l'authentifiant d'une Identité.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-28
	*
	* @param[in] $idn_id Identifiant de l'identité à modifier
	* @param[in] $Authenticator Mot de passe à modifier
	*
	* @return Renvoi TRUE si le mot de passe a été modifiée, FALSE sinon. Lève une Exception en cas d'erreur.
	*/
		$NextDate  = strftime( "%Y-%m-%d",
			mktime( 0, 0, 0, date("m") + 3, date("d"), date("Y") ) );

		$Query = $this->prepareSQL(
			 'UPDATE idn_identites SET ' .
			 'idn_authentifiant = :idn_authentifiant, ' .
			 'idn_changer_authentifiant = FALSE, ' .
			 'idn_date_expiration = :idn_date_expiration, ' .
			 'idn_date_modification_authentifiant = :idn_date_modification_authentifiant ' .
			 'WHERE idn_id = :idn_id' );

		$this->bindSQL( $Query, ':idn_authentifiant', $Authenticator, PDO::PARAM_STR, L_IDN_AUTHENTICATOR );


		$NextDate  = strftime( "%Y-%m-%d",
		 mktime( 0, 0, 0, date("m") + $_Default_User_Lifetime, date("d"), date("Y") ) );

		$this->bindSQL( $Query, ':idn_date_expiration', $NextDate,
		 PDO::PARAM_STR, L_IDN_EXPIRATION_DATE );


		$Current_Date = date( 'Y-m-d H:n:s' );

		$this->bindSQL( $Query, ':idn_date_modification_authentifiant', $Current_Date,
		 PDO::PARAM_STR, L_IDN_UPDATED_AUTHENTICATION );


		$this->bindSQL( $Query, ':idn_id', $idn_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
		
		return TRUE;
	}


	/* -------------------
	** Lister les Identités de façon détaillées.
	*/
	public function rechercherIdentites( $orderBy = '', $Search = '', $Detailed = TRUE ) {
	/**
	* Lister les Identités avec ses relations.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-28
	*
	* @param[in] $orderBy Permet de changer l'ordre d'affichage des Identités
	* @param[in] $Search Permet de rechercher des Identités spécifiques
	* @param[in] $Detailed Permet d'afficher le détail de l'Identité
	*
	* @return Renvoi une liste détaillée d'identités (avec toutes les relations) ou une liste vide
	*/
		if ( $_SESSION['idn_super_admin'] == TRUE ) {
			$Request = 'SELECT ' .
			 't0.* ';

			if ( $Detailed == TRUE ) {
				$Request .= ', t2.*, t3.* ';
			}

			$Request .= 'FROM idn_identites AS "t0" ';

			if ( $Detailed == TRUE ) {
	    		$Request .= 'LEFT JOIN cvl_civilites AS "t2" ON t0.cvl_id = t2.cvl_id ' .
	    	 	 'LEFT JOIN ent_entites AS "t3" ON t0.ent_id = t3.ent_id ';
	    	}
		} else {
			$Request = 'SELECT ' .
			 't0.* ';

			if ( $Detailed == TRUE ) {
				$Request .= ', t2.*, t3.* ';
			}

			$Request .= 'FROM idn_identites AS "t0" ' .
			 'LEFT JOIN iden_idn_ent AS "t1" ON t1.ent_id = t0.ent_id ';

			if ( $Detailed == TRUE ) {
	    		$Request .= 'LEFT JOIN cvl_civilites AS "t2" ON t0.cvl_id = t2.cvl_id ' .
	    	 	 'LEFT JOIN ent_entites AS "t3" ON t0.ent_id = t3.ent_id ';
	    	}

	    	$Request .= 'WHERE t0.idn_id != ' . $_SESSION['idn_id'] . ' ' .
	    	 'AND t1.iden_admin = TRUE ';
		}


		if ( $Search != '' ) {
			$Request .= 'AND (cvl_nom like :LastName ' .
				'OR cvl_prenom like :FirstName ' .
				'OR cvl_lieu_naissance like :BornTown ' .
				'OR ent_libelle like :EntityName ' .
				'OR idn_login like :LoginName) ' ;
		}
		
		switch( $orderBy ) {
		 default:
		 case 'entity':
			$Request .= 'ORDER BY t3.ent_libelle ';
			break;

		 case 'entity-desc':
			$Request .= 'ORDER BY t3.ent_libelle DESC ';
			break;

		 case 'civility':
			$Request .= 'ORDER BY t2.cvl_prenom, T2.cvl_nom ';
			break;

		 case 'civility-desc':
			$Request .= 'ORDER BY t2.cvl_prenom DESC, T2.cvl_nom DESC ';
			break;

		 case 'first_name':
			$Request .= 'ORDER BY t2.cvl_prenom ';
			break;

		 case 'first_name-desc':
			$Request .= 'ORDER BY t2.cvl_prenom DESC ';
			break;

		 case 'last_name':
			$Request .= 'ORDER BY t2.cvl_nom ';
			break;

		 case 'last_name-desc':
			$Request .= 'ORDER BY t2.cvl_nom DESC ';
			break;

		 case 'username':
			$Request .= 'ORDER BY t0.idn_login ';
			break;

		 case 'username-desc':
			$Request .= 'ORDER BY t0.idn_login DESC ';
			break;

		 case 'last_connection':
			$Request .= 'ORDER BY t0.idn_derniere_connexion ';
			break;

		 case 'last_connection-desc':
			$Request .= 'ORDER BY t0.idn_derniere_connexion DESC ';
			break;

		 case 'administrator':
			$Request .= 'ORDER BY t0.idn_super_admin ';
			break;

		 case 'administrator-desc':
			$Request .= 'ORDER BY t0.idn_super_admin DESC ';
			break;

		 case 'disable':
			$Request .= 'ORDER BY t0.idn_desactiver ';
			break;

		 case 'disable-desc':
			$Request .= 'ORDER BY t0.idn_desactiver DESC ';
			break;
		}

		$Query = $this->prepareSQL( $Request );


		if ( $Search != '' ) {
			$Search = '%' . $Search . '%';

			$this->bindSQL( $Query, ':LastName', $Search, PDO::PARAM_STR, L_CVL_LAST_NAME );

			$this->bindSQL( $Query, ':FirstName', $Search, PDO::PARAM_STR, L_CVL_FIRST_NAME );

			$this->bindSQL( $Query, ':BornTown', $Search, PDO::PARAM_STR, L_CVL_BORN_TOWN );

			$this->bindSQL( $Query, ':EntityName', $Search, PDO::PARAM_STR, L_ENT_LABEL );

			$this->bindSQL( $Query, ':LoginName', $Search, PDO::PARAM_STR, L_IDN_LOGIN );
		}

		
		$this->executeSQL( $Query );
		 
 		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function detaillerIdentite( $idn_id, $Detailed = TRUE ) {
	/**
	* Afficher une Identité en détail.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-28
	*
	* @param[in] $idn_id Identifiant de l'Identité à récupérer
	* @param[in] $Detailed Permet d'afficher le détail de l'Identité
	*
	* @return Renvoi l'occurrence détaillée d'une Identité
	*/
		$Request = 'SELECT ' .
		 'T1.* ';

		if ( $Detailed == TRUE ) {
			$Request .= ', T2.*, T3.* ';
		}

		$Request .= 'FROM idn_identites as T1 ';

		if ( $Detailed == TRUE ) {
    		$Request .= 'LEFT JOIN cvl_civilites as T2 ON T1.cvl_id = T2.cvl_id ' .
    	 	 'LEFT JOIN ent_entites as T3 ON T1.ent_id = T3.ent_id ';
    	}

    	$Request .= 'WHERE idn_id = :idn_id ';

		$Query = $this->prepareSQL( $Request );
		 
		$this->bindSQL( $Query, ':idn_id', $idn_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
 		return $Query->fetchObject();
	}


	public function supprimerIdentite( $idn_id ) {
	/**
	* Supprimer une Identité.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-28
	*
	* @param[in] $idn_id Identifiant de l'Identité à supprimer
	*
	* @return Renvoi TRUE si l'Identité a été supprimée, FALSE sinon. Lève une exception en cas d'erreur.
	*/
		
		$Query = $this->prepareSQL( 'DELETE ' .
			 'FROM idn_identites ' .
			 'WHERE idn_id = :idn_id' );
		
		$this->bindSQL( $Query, ':idn_id', $idn_id, PDO::PARAM_INT ) ;
		
		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
 
 		return true;
	}


	public function totalIdentites() {
	/**
	* Récupère le nombre total d'Identités.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-29
	*
	* @return Renvoi le nombre total d'Identités
	*/
		$Query = $this->prepareSQL( 'SELECT ' .
		 'count(*) as total ' .
		 'FROM idn_identites ' );
		
		$this->executeSQL( $Query );
		
		$Occurrence = $Query->fetchObject();
 
 		return $Occurrence->total;
	}


	public function totalIdentitesDesactivees() {
	/**
	* Récupère le nombre total d'Identités désactivées.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-29
	*
	* @return Renvoi le nombre total d'Identités désactivées
	*/
		$Query = $this->prepareSQL( 'SELECT ' .
		 'count(*) as total ' .
		 'FROM idn_identites ' .
		 'WHERE idn_desactiver = 1 ' );
		
		$this->executeSQL( $Query );
		
		$Occurrence = $Query->fetchObject();
 
 		return $Occurrence->total;
	}


	public function totalIdentitesExpirees() {
	/**
	* Récupère le nombre total d'Identités expirées.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-29
	*
	* @return Renvoi le nombre total d'Identités expirées
	*/
		$Query = $this->prepareSQL( 'SELECT ' .
		 'count(*) as total ' .
		 'FROM idn_identites ' .
		 'WHERE idn_date_expiration < "' .  date( 'Y-m-d' ) . '" ' .
		 'AND idn_date_expiration <> "0000-00-00 00:00:00"' );
		
		$this->executeSQL( $Query );
		
		$Occurrence = $Query->fetchObject();
 
 		return $Occurrence->total;
	}


	public function totalIdentitesSuperAdmin() {
	/**
	* Récupère le nombre total d'Identités Super Administrateur.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-29
	*
	* @return Renvoi le nombre total d'Identités Super Administrateur
	*/
		$Query = $this->prepareSQL( 'SELECT ' .
		 'count(*) as total ' .
		 'FROM idn_identites ' .
		 'WHERE idn_super_admin = 1 ' );

		$this->executeSQL( $Query );
		
		$Occurrence = $Query->fetchObject();
 
 		return $Occurrence->total;
	}


	public function totalIdentitesAtteintMaxTentative() {
	/**
	* Récupère le nombre total d'Identités ayant atteint le maximum de tentative de
	* connexion.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-29
	*
	* @return Renvoi le nombre total d'Identités ayant atteint le maximum de tentative de connexion.
	*/
		include( HBL_DIR_LIBRARIES .'/Config_Authentication.inc.php' );

		$Request = 'SELECT ' .
		 'count(*) as total ' .
		 'FROM idn_identites ' .
		 'WHERE idn_tentative > :Max_Attempt ';
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':Max_Attempt', $_Max_Attempt, PDO::PARAM_INT );

		$this->executeSQL( $Query );
		
		$Occurrence = $Query->fetchObject();
 
 		return $Occurrence->total;
	}

} // Fin class IICA_Identities

?>