<?php

include_once( 'Constants.inc.php' );
include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Connecteur_BD_PDO.inc.php' );


if ( ! defined( 'L_APP_CODE' ) ) define( 'L_APP_CODE', 10 );
if ( ! defined( 'L_APP_LABEL' ) ) define( 'L_APP_LABEL', 50 );
if ( ! defined( 'L_APP_LOCALIZATION' ) ) define( 'L_APP_LOCALIZATION', 255 );
if ( ! defined( 'L_APP_PARAMETERS' ) ) define( 'L_APP_PARAMETERS', 255 );


class HBL_Applications extends HBL_Connecteur_BD {

	function __construct() {
	/**
	* Connexion � la base de donn�es.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-29
	*
	* @return Renvoi un bool�en sur le succ�s de la connexion � la base de donn�es
	*/
		parent::__construct();
		
		return true;
	}


	public function majApplication( $app_id, $app_libelle, $app_localisation, $tap_id = 1,
	 $app_date_expiration = '', $app_parametres = '', $app_status = '' ) {
	/**
	* Cr�� ou actualise une Application.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-29
	*
	* @param[in] $app_id Identifiant de l'application (� pr�ciser si modification)
	* @param[in] $app_libelle Libell� de l'application
	* @param[in] $app_localiszation Localisation de l'application (URL ou r�pertoire)
	* @param[in] $tap_id Identifiant du type d'application
	* @param[in] $app_date_expiration P�riode maximum d'expiration d'un compte utilisateur.
	* @param[in] $app_parametres Param�tres particuliers � l'utilisation de l'application
	* @param[in] $app_status Statut de l'application (1 = connexion possible,
	*             0 = connexion imposiible)
	*
	* @return Renvoi un bool�en sur le succ�s de la cr�ation ou la modification de l'application
	*/
		if ( $app_id == '' ) {
			$Request = 'INSERT INTO app_applications ' .
				'( app_libelle, app_localisation, tap_id ';

			if ( $app_date_expiration != '' ) $Request .= ', app_date_expiration';
			if ( $app_parametres != '' ) $Request .= ', app_parametres';
			if ( $app_status != '' ) $Request .= ', app_status ';

			$Request .= ' ) VALUES ( :app_libelle, :app_localisation, :tap_id ';

			if ( $app_date_expiration != '' ) $Request .= ', :app_date_expiration';
			if ( $app_parametres != '' ) $Request .= ', :app_parametres';
			if ( $app_status != '' ) $Request .= ', :app_status ';

			$Request .= ' )';

			$Query = $this->prepareSQL( $Request );
		} else {
			$Request = 'UPDATE app_applications SET ' .
				'app_libelle = :app_libelle, ' .
				'app_localisation = :app_localisation, ' .
				'tap_id = :tap_id ';

			if ( $app_date_expiration != '' ) $Request .= ', app_date_expiration = :app_date_expiration ';
			if ( $app_parametres != '' ) $Request .= ', app_parametres = :app_parametres ';
			if ( $app_status != '' ) $Request .= ', app_status = :app_status ';

			$Request .= 'WHERE app_id = :app_id';

			$Query = $this->prepareSQL( $Request );
			
			$this->bindSQL( $Query, ':app_id', $app_id, PDO::PARAM_INT );
		}


		$this->bindSQL( $Query, ':app_libelle', $app_libelle, PDO::PARAM_STR, L_APP_LABEL );
				
		$this->bindSQL( $Query, ':app_localisation', $app_localisation,
			PDO::PARAM_STR, L_APP_LOCALIZATION );
				
		$this->bindSQL( $Query, ':tap_id', $tap_id, PDO::PARAM_INT );

				
		if ( $app_date_expiration != '' ) $this->bindSQL( $Query, ':app_date_expiration', $app_date_expiration,
			PDO::PARAM_INT );
				
		if ( $app_parametres != '' ) $this->bindSQL( $Query, ':app_parametres', $app_parametres,
			PDO::PARAM_STR, L_APP_PARAMETERS );
				
		if ( $app_status != '' ) $this->bindSQL( $Query, ':app_status', $app_status, PDO::PARAM_INT );


		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}


		if ( $app_id == '' ) {
			switch( $this->getAttribute(PDO::ATTR_DRIVER_NAME) ) {
			 default;
				$this->LastInsertId = $this->lastInsertId();
				break;

			 case 'pgsql';
				$this->LastInsertId = $this->lastInsertId( 'app_applications_app_id_seq' );
				break;
			}
		}
		
		return TRUE;
	}


	public function majApplicationParChamp( $app_id, $Field, $Value ) {
	/**
	* Cr�� ou actualise une Application.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-29
	*
	* @param[in] $app_id Identifiant de l'application
	* @param[in] $Field Nom du champ de l'application � modifier
	* @param[in] $Value Valeur du champ de l'application � prendre en compte
	*
	* @return Renvoi un bool�en sur le succ�s de la cr�ation ou la modification de l'application
	*/
		if ( $app_id == '' or $Field == '' or $Value == '' ) return FALSE;


		$Request = 'UPDATE app_applications SET ';

		switch ( $Field ) {
			case 'app_libelle':
				$Request .= 'app_libelle = :Value ';
				break;
			
			case 'app_localisation':
				$Request .= 'app_localisation = :Value ';
				break;
			
			case 'app_code': // tap_id
				$Request .= 'tap_id = :Value ';
				break;
			
			case 'app_date_expiration':
				$Request .= 'app_date_expiration = :Value ';
				break;
			
			case 'app_parametres':
				$Request .= 'app_parametres = :Value ';
				break;
			
			case 'app_status':
				$Request .= 'app_status = :Value ';
				break;

			default:
				return FALSE;
		}

		$Request .= 'WHERE app_id = :app_id';

		$Query = $this->prepareSQL( $Request );


		$this->bindSQL( $Query, ':app_id', $app_id, PDO::PARAM_INT );
		
		switch ( $Field ) {
			case 'app_libelle':
				$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_STR, L_APP_LABEL );
				break;
			
			case 'app_localisation':
				$this->bindSQL( $Query, ':Value', $Value,
					PDO::PARAM_STR, L_APP_LOCALIZATION );
				break;
			
			case 'app_code': // tap_id
				$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_INT );
				break;
			
			case 'app_date_expiration':
				$this->bindSQL( $Query, ':Value', $Value,
					PDO::PARAM_INT );
				break;
			
			case 'app_parametres':
				$this->bindSQL( $Query, ':Value', $Value,
					PDO::PARAM_STR, L_APP_PARAMETERS );
				break;
			
			case 'app_status':
				$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_INT );
				break;
		}


		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		
		return TRUE;
	}


	public function rechercherApplications( $Order = 'app_code', $Search = '' ) {
	/**
	* Lister les Applications.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-29
	*
	* @param[in] $Order Permet de g�rer l'ordre d'affichage
	* @param[in] $Search Permet de pr�ciser un crit�re de recherche
	* @param[in] $Detailed Permet de pr�ciser le d�tail des informations � remonter
	*
	* @return Renvoi une liste d'Applications ou une liste vide
	*/
		$Request = 'SELECT ' .
			'app.*, tap_code_libelle, lbr_libelle AS app_code ' .
			'FROM app_applications AS app ' .
			'LEFT JOIN tap_types_application AS tap ON tap.tap_id = app.tap_id ' .
			'LEFT JOIN lbr_libelles_referentiel AS rlb ON tap_code_libelle = lbr_code ' .
			'WHERE rlb.lng_id = \'' . $_SESSION['Language'] . '\' ';
		 
		switch ( $Order ) {
		 default:
		 case 'app_code':
			$Request .= 'ORDER BY app_code ';
			break;
			
		 case 'app_code-desc':
			$Request .= 'ORDER BY app_code DESC ';
			break;

		 case 'app_libelle':
			$Request .= 'ORDER BY app_libelle ';
			break;
			
		 case 'app_libelle-desc':
			$Request .= 'ORDER BY app_libelle DESC ';
			break;

		 case 'app_localisation':
			$Request .= 'ORDER BY app_localisation ';
			break;
			
		 case 'app_localisation-desc':
			$Request .= 'ORDER BY app_localisation DESC ';
			break;

		 case 'tap_label':
			$Request .= 'ORDER BY tap_label ';
			break;
			
		 case 'tap_label-desc':
			$Request .= 'ORDER BY tap_label DESC ';
			break;

		 case 'app_date_expiration':
			$Request .= 'ORDER BY app_date_expiration ';
			break;
			
		 case 'app_date_expiration-desc':
			$Request .= 'ORDER BY app_date_expiration DESC ';
			break;

		 case 'app_parametres':
			$Request .= 'ORDER BY app_parametres ';
			break;
			
		 case 'app_parametres-desc':
			$Request .= 'ORDER BY app_parametres DESC ';
			break;

		 case 'app_status':
			$Request .= 'ORDER BY app_status ';
			break;
			
		 case 'app_status-desc':
			$Request .= 'ORDER BY app_status DESC ';
			break;
		}
		 
		$Query = $this->prepareSQL( $Request );
		
		$this->executeSQL( $Query );
 
 		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function detaillerApplication( $app_id = '' ) {
	/**
	* R�cup�re les informations d'une Application.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-31
	*
	* @param[in] $app_id Identifiant de l'Application � afficher
	*
	* @return Renvoi l'occurrence d'une civilit� ou FALSE si aucune. L�ve une Exception en cas d'erreur.
	*/
		if ( $app_id == '' ) return false;
		
		$Request = 'SELECT ' .
			'app.*, tap_code_libelle, lbr_libelle AS app_code ' .
			'FROM app_applications AS app ' .
			'LEFT JOIN tap_types_application AS tap ON tap.tap_id = app.tap_id ' .
			'LEFT JOIN lbr_libelles_referentiel AS rlb ON tap_code_libelle = lbr_code ' .
			'WHERE rlb.lng_id = \'' . $_SESSION['Language'] . '\' AND app_id = :app_id ';
		 
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':app_id', $app_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
		
		return $Query->fetchObject();
	}


	public function supprimerApplication( $app_id = '' ) {
	/**
	* Supprime une Application.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-31
	*
	* @param[in] $app_id Identifiant de la civilit� � supprimer
	*
	* @return Renvoi TRUE si l'occurrence a �t� supprim�e, sinon FALSE. L�ve une Exception en cas d'erreur.
	*/
		if ( $app_id == '' ) return FALSE;
	
		$Query = $this->prepareSQL( 'DELETE ' .
		 'FROM app_applications ' .
		 'WHERE app_id = :app_id' );
		
		$this->bindSQL( $Query, ':app_id', $app_id, PDO::PARAM_INT ) ;
		
		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
 
 		return TRUE;
	}


	public function rechercherTypesApplication( $tap_id = '' ) {
	/**
	* Lister les Types d'Application.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-31
	*
	* @return Renvoi une liste de types d'application ou une liste vide. L�ve une Exception en cas d'erreur.
	*/
		$Request = 'SELECT ' .
		 'tap_id, lbr_libelle AS app_code ' .
		 'FROM tap_types_application ' .
		 'LEFT JOIN lbr_libelles_referentiel AS rlb ON tap_code_libelle = lbr_code ' .
		 'WHERE rlb.lng_id = \'' . $_SESSION['Language'] . '\' ';

		if ( $tap_id != '' ) $Request .= 'AND tap_id = ' . $tap_id;
		 
		$Query = $this->prepareSQL( $Request );
		
		$this->executeSQL( $Query );
		 
 		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function controlerAssociationApplication( $app_id ) {
	/**
	* V�rifie si cette Civilit� est associ� � un autre objet.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-15
	*
	* @param[in] $cvl_id Identifiant de la Civilit� � contr�ler
	*
	* @return Renvoi l'occurrence listant les association de l'Entit� ou FALSE si pas d'entit�. L�ve une Exception en cas d'erreur.
	*/
		$Request = 'SELECT ' .
		 'COUNT(DISTINCT prf_id) AS total_prf ' .
		 'FROM app_applications AS app ' .
		 'LEFT JOIN cta_controle_acces AS accn ON accn.app_id = app.app_id ' .
		 'WHERE app.app_id = :app_id ';

		 
		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':app_id', $app_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return $Query->fetchObject();
	}


	public function totalApplications() {
	/**
	* Calcul le nombre total d'Entit�s.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2016-12-24
	*
	* @return Renvoi le nombre total d'Entit�s stock�es en base. L�ve une Exception en cas d'erreur.
	*/

		$Request = 'SELECT ' .
		 'count(*) AS total ' .
		 'FROM app_applications ' ;

		$Query = $this->prepareSQL( $Request );

		$this->executeSQL( $Query );
		
		$Occurrence = $Query->fetchObject() ;
		
		return $Occurrence->total;
	}
	
}

?>