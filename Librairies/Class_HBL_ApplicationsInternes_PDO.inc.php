<?php

include_once( 'Constants.inc.php' );
include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Connecteur_BD_PDO.inc.php' );


if ( ! defined( 'L_AIN_LIBELLE' ) ) define( 'L_AIN_LIBELLE', 100 );
if ( ! defined( 'L_AIN_LOCALISATION' ) ) define( 'L_AIN_LOCALISATION', 255 );
if ( ! defined( 'L_AIN_PARAMETRES' ) ) define( 'L_AIN_PARAMETRES', 255 );


class HBL_ApplicationsInternes extends HBL_Connexioneur_BD {

	function __construct() {
	/**
	* Connexion à la base de données.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-01-10
	*
	* \return Renvoi un booléen sur le succès de la connexion à la base de données
	*/
		parent::__construct();
		
		return true;
	}


	public function majApplication( $ain_id, $ain_libelle, $ain_localisation, $tap_id = 1,
	 $ain_date_expiration = '', $ain_parametres = '', $ain_maintenance = '' ) {
	/**
	* Crée ou met à jour une Application Interne.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-01-10
	*
	* \param[in] $ain_id Identifiant de l'application (à préciser si modification)
	* \param[in] $ain_libelle Libellé de l'application
	* \param[in] $ain_localisation Localisation de l'application (URL ou répertoire)
	* \param[in] $tap_id Identifiant du type d'application
	* \param[in] $ain_date_expiration Période maximum d'expiration d'un compte utilisateur.
	* \param[in] $ain_parametres Paramètres particuliers à l'utilisation de l'application
	* \param[in] $ain_maintenance Statut de l'application (1 = connexion possible, 0 = connexion imposiible)
	*
	* \return Renvoi un booléen sur le succès de la création ou la modification de l'application
	*/
		if ( $ain_id == '' ) {
			$Request = 'INSERT INTO ain_applications_internes ' .
				'( ain_libelle, ain_localisation, tap_id ';

			if ( $ain_date_expiration != '' ) $Request .= ', ain_date_expiration';
			if ( $ain_parametres != '' ) $Request .= ', ain_parametres';
			if ( $ain_maintenance != '' ) $Request .= ', ain_maintenance ';

			$Request .= ' ) VALUES ( :ain_libelle, :ain_localisation, :tap_id ';

			if ( $ain_date_expiration != '' ) $Request .= ', :ain_date_expiration';
			if ( $ain_parametres != '' ) $Request .= ', :ain_parametres';
			if ( $ain_maintenance != '' ) $Request .= ', :ain_maintenance ';

			$Request .= ' )';

			$Query = $this->prepareSQL( $Request );
		} else {
			$Request = 'UPDATE ain_applications_internes SET ' .
				'ain_libelle = :ain_libelle, ' .
				'ain_localisation = :ain_localisation, ' .
				'tap_id = :tap_id ';

			if ( $ain_date_expiration != '' ) $Request .= ', ain_date_expiration = :ain_date_expiration ';
			if ( $ain_parametres != '' ) $Request .= ', ain_parametres = :ain_parametres ';
			if ( $ain_maintenance != '' ) $Request .= ', ain_maintenance = :ain_maintenance ';

			$Request .= 'WHERE ain_id = :ain_id';

			$Query = $this->prepareSQL( $Request );
			
			$this->bindSQL( $Query, ':ain_id', $ain_id, PDO::PARAM_INT );
		}


		$this->bindSQL( $Query, ':ain_libelle', $ain_libelle, PDO::PARAM_STR, L_AIN_LIBELLE );
				
		$this->bindSQL( $Query, ':ain_localisation', $ain_localisation,
			PDO::PARAM_STR, L_AIN_LOCALISATION );
				
		$this->bindSQL( $Query, ':tap_id', $tap_id, PDO::PARAM_INT );

				
		if ( $ain_date_expiration != '' ) $this->bindSQL( $Query, ':ain_date_expiration', $ain_date_expiration,
			PDO::PARAM_INT );
				
		if ( $ain_parametres != '' ) $this->bindSQL( $Query, ':ain_parametres', $ain_parametres,
			PDO::PARAM_STR, L_APP_PARAMETRES );
				
		if ( $ain_maintenance != '' ) $this->bindSQL( $Query, ':ain_maintenance', $ain_maintenance, PDO::PARAM_BOOL );


		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}


		if ( $ain_id == '' ) {
			switch( $this->getAttribute(PDO::ATTR_DRIVER_NAME) ) {
			 default;
				$this->LastInsertId = $this->lastInsertId();
				break;

			 case 'pgsql';
				$this->LastInsertId = $this->lastInsertId( 'ain_applications_internes_ain_id_seq' );
				break;
			}
		}
		
		return TRUE;
	}


	public function majApplicationParChamp( $ain_id, $Field, $Value ) {
	/**
	* Crée ou actualise les champs courants d'une Application Interne.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-01-10
	*
	* \param[in] $ain_id Identifiant de l'application
	* \param[in] $Field Nom du champ de l'application à modifier
	* \param[in] $Value Valeur du champ de l'application à prendre en compte
	*
	* \return Renvoi un booléen sur le succès de la création ou la modification de l'application
	*/
		if ( $ain_id == '' or $Field == '' or $Value == '' ) return FALSE;


		$Request = 'UPDATE ain_applications_internes SET ';

		switch ( $Field ) {
			case 'ain_libelle':
				$Request .= 'ain_libelle = :Value ';
				break;
			
			case 'ain_localisation':
				$Request .= 'ain_localisation = :Value ';
				break;
			
			case 'tap_code': // tap_id
				$Request .= 'tap_id = :Value ';
				break;
			
			case 'ain_date_expiration':
				$Request .= 'ain_date_expiration = :Value ';
				break;
			
			case 'ain_parametres':
				$Request .= 'ain_parametres = :Value ';
				break;
			
			case 'ain_maintenance':
				$Request .= 'ain_maintenance = :Value ';
				break;

			default:
				return FALSE;
		}

		$Request .= 'WHERE ain_id = :ain_id';

		$Query = $this->prepareSQL( $Request );


		$this->bindSQL( $Query, ':ain_id', $ain_id, PDO::PARAM_INT );
		
		switch ( $Field ) {
			case 'ain_libelle':
				$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_STR, L_AIN_LIBELLE );
				break;
			
			case 'ain_localisation':
				$this->bindSQL( $Query, ':Value', $Value,
					PDO::PARAM_STR, L_AIN_LOCALISATION );
				break;
			
			case 'tap_code': // tap_id
				$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_INT );
				break;
			
			case 'ain_date_expiration':
				$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_INT );
				break;
			
			case 'app_parametres':
				$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_STR, L_AIN_PARAMETRES );
				break;
			
			case 'ain_maintenance':
				$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_INT );
				break;
		}


		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		
		return TRUE;
	}


	public function rechercherApplications( $Order = 'ain_code', $Search = '' ) {
	/**
	* Lister les Applications.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-01-10
	*
	* \param[in] $Order Permet de gérer l'ordre d'affichage
	* \param[in] $Search Permet de préciser un critère de recherche
	* \param[in] $Detailed Permet de préciser le détail des informations à remonter
	*
	* \return Renvoi une liste d'Applications ou une liste vide
	*/
		$Request = 'SELECT ' .
			'ain.*, tap_code_libelle, lbr_libelle AS tap_code ' .
			'FROM ain_applications_internes AS "ain" ' .
			'LEFT JOIN tap_types_application AS "tap" ON tap.tap_id = ain.tap_id ' .
			'LEFT JOIN lbr_libelles_referentiel AS "lbr" ON tap_code_libelle = lbr.lbr_code AND lbr.lng_id = \'' . $_SESSION['Language'] . '\' ';
		 
		switch ( $Order ) {
		 default:
		 case 'tap_code':
			$Request .= 'ORDER BY tap_code ';
			break;
			
		 case 'tap_code-desc':
			$Request .= 'ORDER BY tap_code DESC ';
			break;

		 case 'ain_libelle':
			$Request .= 'ORDER BY ain_libelle ';
			break;
			
		 case 'ain_libelle-desc':
			$Request .= 'ORDER BY ain_libelle DESC ';
			break;

		 case 'ain_localisation':
			$Request .= 'ORDER BY ain_localisation ';
			break;
			
		 case 'ain_localisation-desc':
			$Request .= 'ORDER BY ain_localisation DESC ';
			break;

		 case 'tap_label':
			$Request .= 'ORDER BY tap_label ';
			break;
			
		 case 'tap_label-desc':
			$Request .= 'ORDER BY tap_label DESC ';
			break;

		 case 'ain_date_expiration':
			$Request .= 'ORDER BY ain_date_expiration ';
			break;
			
		 case 'ain_date_expiration-desc':
			$Request .= 'ORDER BY ain_date_expiration DESC ';
			break;

		 case 'ain_parametres':
			$Request .= 'ORDER BY ain_parametres ';
			break;
			
		 case 'ain_parametres-desc':
			$Request .= 'ORDER BY ain_parametres DESC ';
			break;

		 case 'ain_maintenance':
			$Request .= 'ORDER BY ain_maintenance ';
			break;
			
		 case 'ain_maintenance-desc':
			$Request .= 'ORDER BY ain_maintenance DESC ';
			break;
		}
		 
		$Query = $this->prepareSQL( $Request );
		
		$this->executeSQL( $Query );
 
 		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function detaillerApplication( $ain_id = '' ) {
	/**
	* Récupère les informations d'une Application Interne.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-01-10
	*
	* \param[in] $ain_id Identifiant de l'Application Interne à afficher
	*
	* \return Renvoi l'occurrence d'une civilité ou FALSE si aucune. Lève une Exception en cas d'erreur.
	*/
		if ( $ain_id == '' ) return false;
		
		$Request = 'SELECT ' .
			'ain.*, tap_code_libelle, lbr_libelle AS tap_code ' .
			'FROM ain_applications_internes AS "ain" ' .
			'LEFT JOIN tap_types_application AS "tap" ON tap.tap_id = ain.tap_id ' .
			'LEFT JOIN lbr_libelles_referentiel AS "lbr" ON tap_code_libelle = lbr_code AND lbr.lng_id = \'' . $_SESSION['Language'] . '\' ' .
			'WHERE ain_id = :ain_id ';
		 
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':ain_id', $ain_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
		
		return $Query->fetchObject();
	}


	public function supprimerApplication( $ain_id = '' ) {
	/**
	* Supprime une Application.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-01-10
	*
	* \param[in] $app_id Identifiant de la civilité à supprimer
	*
	* \return Renvoi TRUE si l'occurrence a été supprimée, sinon FALSE. Lève une Exception en cas d'erreur.
	*/
		if ( $ain_id == '' ) return FALSE;
	
		$Query = $this->prepareSQL( 'DELETE ' .
		 'FROM ain_applications_internes ' .
		 'WHERE ain_id = :ain_id' );
		
		$this->bindSQL( $Query, ':ain_id', $ain_id, PDO::PARAM_INT ) ;
		
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
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-01-10
	*
	* \return Renvoi une liste de types d'application ou une liste vide. Lève une Exception en cas d'erreur.
	*/
		$Request = 'SELECT ' .
		 'tap_id, lbr_libelle AS tap_code ' .
		 'FROM tap_types_application ' .
		 'LEFT JOIN lbr_libelles_referentiel AS "lbr" ON tap_code_libelle = lbr_code ' .
		 'WHERE lbr.lng_id = \'' . $_SESSION['Language'] . '\' ';

		if ( $tap_id != '' ) $Request .= 'AND tap_id = ' . $tap_id;
		 
		$Query = $this->prepareSQL( $Request );
		
		$this->executeSQL( $Query );
		 
 		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function controlerAssociationApplication( $ain_id ) {
	/**
	* Vérifie si cette Civilité est associé à un autre objet.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-01-10
	*
	* \param[in] $cvl_id Identifiant de la Civilité à contrôler
	*
	* \return Renvoi l'occurrence listant les association de l'Entité ou FALSE si pas d'entité. Lève une Exception en cas d'erreur.
	*/
		$Request = 'SELECT ' .
		 'COUNT(DISTINCT prf_id) AS total_prf ' .
		 'FROM ain_applications_internes AS "ain" ' .
		 'LEFT JOIN caa_controle_acces_application_interne AS "caa" ON caa.ain_id = ain.ain_id ' .
		 'WHERE ain.ain_id = :ain_id ';

		 
		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':ain_id', $ain_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return $Query->fetchObject();
	}


	public function totalApplications() {
	/**
	* Calcul le nombre total d'Entités.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-01-10
	*
	* \return Renvoi le nombre total d'Entités stockées en base. Lève une Exception en cas d'erreur.
	*/

		$Request = 'SELECT ' .
		 'count(*) AS total ' .
		 'FROM ain_applications_internes ' ;

		$Query = $this->prepareSQL( $Request );

		$this->executeSQL( $Query );
		
		$Occurrence = $Query->fetchObject() ;
		
		return $Occurrence->total;
	}
	
}

?>