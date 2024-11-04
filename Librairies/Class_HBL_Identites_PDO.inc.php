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
* \license Copyright Loxense
* \author Pierre-Luc MARY
* \date 2015-05-27
*/

	public function __construct() {
	/**
	* Connexion à la base de données.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-27
	*
	* \return Renvoi un booléen sur le succès de la connexion à la base de données
	*/
		parent::__construct();
		
		return true;
	}


	/* ===============================================================================
	** Gestion des Identités
	*/
	
	public function majIdentite( $idn_id, $Login, $Authenticator, $SuperAdmin, $Id_Societe, $Id_Civilite, $Email = '' ) {
	/**
	* Créé ou actualise une Identité.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \version 1.0
	* \date 2016-11-13
	*
	* \param[in] $idn_id Identifiant de l'identité à modifier (si précisé)
	* \param[in] $Login Nom de connexion de l'utilisateur
	* \param[in] $Authenticator Mot de passe de l'utilisateur
	* \param[in] $ChangeAuthenticator Booléen pour indiquer s'il faut changer le mot de passe
	* \param[in] $Attempt Nombre de tentative de connexion
	* \param[in] $SuperAdmin Booléen pour indiquer si l'utilisateur est un Administrateur
	* \param[in] $Id_Societe Identifiant de l'Entité de rattachement de l'utilisateur
	* \param[in] $Id_Civilite Identifiant de la Civilité de rattachement de l'utilisateur
	* \param[in] $Email Adresse courriel de l'utilisateur
	*
	* \return Renvoi vrai si l'Identité a été créée ou modifiée, sinon lève une exception
	*/
		if ( $idn_id == '' ) {
			$Request = 'INSERT INTO idn_identites (' .
			 'sct_id, ' .
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
			 ':sct_id, ' .
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

			$NextDate  = date( "Y-m-d", time() );

			$this->bindSQL( $Query, ':idn_date_modification_authentifiant', $NextDate, PDO::PARAM_STR, L_IDN_UPDATED_AUTHENTICATION );

			$this->bindSQL( $Query, ':idn_grain_sel', $Salt, PDO::PARAM_STR, L_IDN_SALT );

			if ( $SuperAdmin == '' or $_SESSION['idn_super_admin'] === FALSE ) $SuperAdmin = FALSE;

			$this->bindSQL( $Query, ':idn_super_admin', $SuperAdmin, PDO::PARAM_BOOL );

/*
$tmp = $Request;
str_replace(':idn_authentifiant', $Authenticator, $tmp);
str_replace(':idn_date_modification_authentifiant', $NextDate, $tmp);
str_replace(':idn_grain_sel', $Salt, $tmp);
str_replace(':idn_super_admin', $SuperAdmin, $tmp);
*/

		} else {
			$Request = 'UPDATE idn_identites SET ' .
			 'sct_id = :sct_id, ' .
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

		$this->bindSQL( $Query, ':sct_id', $Id_Societe, PDO::PARAM_INT );
		
		$this->bindSQL( $Query, ':cvl_id', $Id_Civilite, PDO::PARAM_INT );

		$this->bindSQL( $Query, ':idn_login', $Login, PDO::PARAM_STR, L_IDN_LOGIN );

		$this->bindSQL( $Query, ':idn_courriel', $Email, PDO::PARAM_STR, L_IDN_EMAIL );

		$NextDate  = date( "Y-m-d",
			mktime( 0, 0, 0, date("m") + $this->recupererParametre('account_lifetime'), date("d"), date("Y") ) );
		
		$this->bindSQL( $Query, ':idn_date_expiration', $NextDate, PDO::PARAM_STR, L_IDN_EXPIRATION_DATE );

/*		str_replace(':sct_id', $Id_Societe, $tmp);
		str_replace(':cvl_id', $Id_Civilite, $tmp);
		str_replace(':idn_login', $Login, $tmp);
		str_replace(':idn_courriel', $Email, $tmp);
		str_replace(':idn_date_expiration', $NextDate, $tmp);
		print('<hr>'.$tmp.'<hr>');
*/

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
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-11-02
	*
	* \param[in] $ID Identifiant de l'Identité à modifier
	* \param[in] $Source Nom du champ à modifier
	* \param[in] $Valeur Valeur à affecter au champ.
	*
	* \return Renvoi TRUE si l'Identité a été mise à jour, FALSE si l'Identité n'existe pas. Lève une Exception en cas d'erreur.
	*/
		if ( $idn_id == '' ) return FALSE;

		$Request = 'UPDATE idn_identites SET ';

		switch ( $Source ) {
		 	case 'cvl_label':
				$Request .= 'cvl_id = :Valeur ';
		 		break;
		 	
		 	case 'sct_libelle':
				$Request .= 'sct_id = :Valeur ';
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
		 	case 'sct_libelle':
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
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-28
	*
	* \param[in] $idn_id Identifiant de l'identité à modifier
	* \param[in] $Authenticator Mot de passe à modifier
	*
	* \return Renvoi TRUE si le mot de passe a été modifiée, FALSE sinon. Lève une Exception en cas d'erreur.
	*/
		$NextDate  = date( "Y-m-d",
			mktime( 0, 0, 0, date("m") + 3, date("d"), date("Y") ) );

		$Query = $this->prepareSQL(
			 'UPDATE idn_identites SET ' .
			 'idn_authentifiant = :idn_authentifiant, ' .
			 'idn_changer_authentifiant = FALSE, ' .
			 'idn_date_expiration = :idn_date_expiration, ' .
			 'idn_date_modification_authentifiant = :idn_date_modification_authentifiant ' .
			 'WHERE idn_id = :idn_id' );

		$this->bindSQL( $Query, ':idn_authentifiant', $Authenticator, PDO::PARAM_STR, L_IDN_AUTHENTICATOR );


		$NextDate  = date( "Y-m-d",
		 mktime( 0, 0, 0, date("m") + $_Default_User_Lifetime, date("d"), date("Y") ) );
		print('<hr>'.$NextDate);
		
		$this->bindSQL( $Query, ':idn_date_expiration', $NextDate,
		 PDO::PARAM_STR, L_IDN_EXPIRATION_DATE );


		$Current_Date = date( 'Y-m-d H:n:s' );

		$this->bindSQL( $Query, ':idn_date_modification_authentifiant', $Current_Date,
		 PDO::PARAM_STR, L_IDN_UPDATED_AUTHENTICATION );


		$this->bindSQL( $Query, ':idn_id', $idn_id, PDO::PARAM_INT );
		print('<hr>'.$Query);
		
		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
		
		return TRUE;
	}


	/* -------------------
	** Lister les Identités de façon détaillées.
	*/
	public function rechercherIdentites( $sct_id, $orderBy = '', $Search = '', $Detailed = TRUE ) {
	/**
	* Lister les Identités avec ses relations.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-28
	*
	* \param[in] $sct_id Id. de la Société d'appartenance des Identités
	* \param[in] $orderBy Permet de changer l'ordre d'affichage des Identités
	* \param[in] $Search Permet de rechercher des Identités spécifiques
	* \param[in] $Detailed Permet d'afficher le détail de l'Identité
	*
	* \return Renvoi une liste détaillée d'identités (avec toutes les relations) ou une liste vide
	*/
		$Where = '';

		$Request = 'SELECT ' .
		 'idn.* ';

		if ( $Detailed == TRUE ) {
			$Request .= ', cvl.*, sct.* ';
		}

		$Request .= 'FROM idn_identites AS "idn" ';

		if ( $Detailed == TRUE ) {
			$Request .= 'LEFT JOIN cvl_civilites AS "cvl" ON idn.cvl_id = cvl.cvl_id ' .
				'LEFT JOIN sct_societes AS "sct" ON idn.sct_id = sct.sct_id ';
		}

		if ( $_SESSION['idn_super_admin'] === FALSE ) {
				$Where .= 'WHERE idn.idn_id != ' . $_SESSION['idn_id'] . ' ';
		}

		if ($Where != '') {
			$Where .= 'AND  idn.sct_id = :sct_id ';
		} else {
			$Where = 'WHERE idn.sct_id = :sct_id ';
		}

		$Request .= $Where;

		if ( $Search != '' ) {
			$Request .= 'AND (cvl_nom like :cvl_nom ' .
				'OR cvl_prenom like :cvl_prenom ' .
				'OR idn_login like :idn_login) ' ;
		}
		
		switch( $orderBy ) {
		 default:
		 case 'societe':
			$Request .= 'ORDER BY sct.sct_nom ';
			break;

		 case 'societe-desc':
			$Request .= 'ORDER BY sct.sct_nom DESC ';
			break;

		 case 'civilite':
			$Request .= 'ORDER BY cvl.cvl_prenom, cvl.cvl_nom, idn.idn_login ';
			break;

		 case 'civilite-desc':
			$Request .= 'ORDER BY cvl.cvl_prenom DESC, cvl.cvl_nom DESC, idn.idn_login DESC ';
			break;

		 case 'prenom':
			$Request .= 'ORDER BY cvl.cvl_prenom ';
			break;

		 case 'prenom-desc':
			$Request .= 'ORDER BY cvl.cvl_prenom DESC ';
			break;

		 case 'nom':
			$Request .= 'ORDER BY cvl.cvl_nom ';
			break;

		 case 'nom-desc':
			$Request .= 'ORDER BY cvl.cvl_nom DESC ';
			break;

		 case 'username':
			$Request .= 'ORDER BY idn.idn_login ';
			break;

		 case 'username-desc':
			$Request .= 'ORDER BY idn.idn_login DESC ';
			break;

		 case 'derniere_connexion':
			$Request .= 'ORDER BY idn.idn_derniere_connexion ';
			break;

		 case 'derniere_connexion-desc':
			$Request .= 'ORDER BY idn.idn_derniere_connexion DESC ';
			break;

		 case 'administrateur':
			$Request .= 'ORDER BY idn.idn_super_admin ';
			break;

		 case 'administrateur-desc':
			$Request .= 'ORDER BY idn.idn_super_admin DESC ';
			break;

		 case 'desactive':
			$Request .= 'ORDER BY idn.idn_desactiver ';
			break;

		 case 'desactive-desc':
			$Request .= 'ORDER BY idn.idn_desactiver DESC ';
			break;
		}

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':sct_id', $sct_id, PDO::PARAM_INT );

		if ( $Search != '' ) {
			$Search = '%' . $Search . '%';

			$this->bindSQL( $Query, ':cvl_nom', $Search, PDO::PARAM_STR, L_CVL_LAST_NAME );

			$this->bindSQL( $Query, ':cvl_prenom', $Search, PDO::PARAM_STR, L_CVL_FIRST_NAME );

			$this->bindSQL( $Query, ':sct_nom', $Search, PDO::PARAM_STR, L_ENT_LABEL );

			$this->bindSQL( $Query, ':idn_login', $Search, PDO::PARAM_STR, L_IDN_LOGIN );
		}

		$this->executeSQL( $Query );

		return $Query->fetchAll( PDO::FETCH_CLASS );
	}



	public function detaillerIdentite( $idn_id, $Detailed = TRUE ) {
	/**
	* Afficher une Identité en détail.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-28
	*
	* \param[in] $idn_id Identifiant de l'Identité à récupérer
	* \param[in] $Detailed Permet d'afficher le détail de l'Identité
	*
	* \return Renvoi l'occurrence détaillée d'une Identité
	*/
		$Request = 'SELECT ' .
		 'T1.* ';

		if ( $Detailed == TRUE ) {
			$Request .= ', T2.*, T3.* ';
		}

		$Request .= 'FROM idn_identites as T1 ';

		if ( $Detailed == TRUE ) {
		$Request .= 'LEFT JOIN cvl_civilites as T2 ON T1.cvl_id = T2.cvl_id ' .
			'LEFT JOIN sct_societes as T3 ON T1.sct_id = T3.sct_id ';
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
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-28
	*
	* \param[in] $idn_id Identifiant de l'Identité à supprimer
	*
	* \return Renvoi TRUE si l'Identité a été supprimée, FALSE sinon. Lève une exception en cas d'erreur.
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
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-29
	*
	* \return Renvoi le nombre total d'Identités
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
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-29
	*
	* \return Renvoi le nombre total d'Identités désactivées
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
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-29
	*
	* \return Renvoi le nombre total d'Identités expirées
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
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-29
	*
	* \return Renvoi le nombre total d'Identités Super Administrateur
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
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-29
	*
	* \return Renvoi le nombre total d'Identités ayant atteint le maximum de tentative de connexion.
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