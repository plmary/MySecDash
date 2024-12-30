<?php

include_once( 'Constants.inc.php' );
include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Connecteur_BD_PDO.inc.php' );


if ( ! defined( 'L_APP_NOM' ) ) define( 'L_APP_NOM', 100 );
if ( ! defined( 'L_APP_HEBERGEMENT' ) ) define( 'L_APP_HEBERGEMENT', 100 );
if ( ! defined( 'L_APP_NIVEAU_SERVICE' ) ) define( 'L_APP_NIVEAU_SERVICE', 100 );


class Applications extends HBL_Connexioneur_BD {

	function __construct() {
	/**
	* Connexion à la base de données.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-29
	*
	* \return Renvoi un booléen sur le succès de la connexion à la base de données
	*/
		parent::__construct();
		
		return true;
	}


	public function majApplication( $app_id, $app_nom, $frn_id='', $app_hebergement='', $app_niveau_service='',
		$app_description='', $sct_id = NULL ) {
	/**
	* Créé ou actualise une Application.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-02-20
	*
	* \param[in] $app_id Identifiant de l'application (à préciser si modification)
	* \param[in] $app_nom Nom de l'application
	* \param[in] $frn_id ID du Fournisseur
	* \param[in] $app_hebergement Définit l'hébergement de l'application
	* \param[in] $app_niveau_service Niveau de service de l'application
	* \param[in] $app_description Donne une description de l'application
	* \param[in] $sct_id Permet de déclarer une Société quand l'application est spécifique à celle-ci
	*
	* \return Renvoi un booléen sur le succès de la création ou la modification de l'application
	*/
		if ( $app_id == '' ) {
			$Request = 'INSERT INTO app_applications 
				( app_nom, frn_id, app_hebergement, app_niveau_service, app_description, sct_id ) VALUES
				( :app_nom, :frn_id, :app_hebergement, :app_niveau_service, :app_description, :sct_id )';

			$Query = $this->prepareSQL( $Request );
		} else {
			$Request = 'UPDATE app_applications SET 
				app_nom = :app_nom,
				frn_id = :frn_id,
				app_hebergement = :app_hebergement,
				app_niveau_service = :app_niveau_service,
				app_description = :app_description,
				sct_id = :sct_id
				WHERE app_id = :app_id ';

			$Query = $this->prepareSQL( $Request );
			
			$this->bindSQL( $Query, ':app_id', $app_id, PDO::PARAM_INT );
		}

		$this->bindSQL( $Query, ':app_nom', $app_nom, PDO::PARAM_STR, L_APP_NOM );
		$this->bindSQL( $Query, ':app_hebergement', $app_hebergement, PDO::PARAM_STR, L_APP_HEBERGEMENT );
		$this->bindSQL( $Query, ':app_niveau_service', $app_niveau_service, PDO::PARAM_STR, L_APP_NIVEAU_SERVICE );
		$this->bindSQL( $Query, ':app_description', $app_description, PDO::PARAM_LOB );

		if ( $sct_id == NULL ) {
			$this->bindSQL( $Query, ':sct_id', NULL, PDO::PARAM_NULL );
		} else {
			$this->bindSQL( $Query, ':sct_id', $sct_id, PDO::PARAM_INT );
		}

		if ($frn_id != '') {
			$this->bindSQL( $Query, ':frn_id', $frn_id, PDO::PARAM_INT );
		} else {
			$this->bindSQL( $Query, ':frn_id', NULL, PDO::PARAM_NULL );
		}
		
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
	* Créé ou actualise une Application.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-29
	*
	* \param[in] $app_id Identifiant de l'application
	* \param[in] $Field Nom du champ de l'application à modifier
	* \param[in] $Value Valeur du champ de l'application à prendre en compte
	*
	* \return Renvoi un booléen sur le succès de la création ou la modification de l'application
	*/
		//if ( $app_id == '' or ($Field == '' and $Field != 'frn_id') or $Value == '' ) return FALSE;


		$Request = 'UPDATE app_applications SET ';

		switch ( $Field ) {
			case 'app_nom':
				$Request .= 'app_nom = :Value ';
				break;

			case 'frn_id':
				$Request .= 'frn_id = :Value ';
				break;
				
			case 'sct_id':
				$Request .= 'sct_id = :Value ';
				if ( $Value == 1 ) {
					$Value = $_SESSION['s_sct_id'];
				} else {
					$Value = NULL;
				}
				break;
				
			case 'app_hebergement':
				$Request .= 'app_hebergement = :Value ';
				break;

			case 'app_niveau_service':
				$Request .= 'app_niveau_service = :Value ';
				break;

			case 'app_description':
				$Request .= 'app_description = :Value ';
				break;

			default:
				return FALSE;
		}

		$Request .= 'WHERE app_id = :app_id';

		$Query = $this->prepareSQL( $Request );


		$this->bindSQL( $Query, ':app_id', $app_id, PDO::PARAM_INT );
		
		switch ( $Field ) {
			case 'app_nom':
				$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_STR, L_APP_NOM );
				break;

			case 'frn_id':
				if ($Value != '') {
					$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_INT );
				} else {
					$this->bindSQL( $Query, ':Value', NULL, PDO::PARAM_NULL );
				}
				break;

			case 'sct_id':
				if ($Value != NULL) {
					$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_INT );
				} else {
					$this->bindSQL( $Query, ':Value', NULL, PDO::PARAM_NULL );
				}
				break;

			case 'app_hebergement':
				$this->bindSQL( $Query, ':Value', $Value,
					PDO::PARAM_STR, L_APP_HEBERGEMENT );
				break;

			case 'app_niveau_service':
				$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_STR, L_APP_NIVEAU_SERVICE );
				break;

			case 'app_description':
				$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_LOB );
				break;
		}


		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		
		return TRUE;
	}


	public function rechercherApplications( $Order = 'app_nom', $app_id = '', $sct_id = '' ) {
	/**
	* Lister les Applications.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-02-20
	*
	* \param[in] $Order Permet de gérer l'ordre d'affichage
	* \param[in] $app_id Permet de limiter la recherche à une application spécifique
	* \param[in] $sct_id Permet d'ajouter les applications spécifique à une société
	*
	* \return Renvoi une liste d'Applications ou une liste vide
	*/
		$Where = '';
		
		$Request = 'SELECT
			*
			FROM app_applications AS "app" 
			LEFT JOIN frn_fournisseurs AS "frn" ON frn.frn_id = app.frn_id 
			WHERE app.sct_id IS NULL ';


		if ($app_id != '') {
			$Where .= 'AND app.app_id = :app_id ';
		}

		if ($sct_id != '') {
			$Where .= 'OR app.sct_id = :sct_id ';
		}


		$Request = $Request . $Where;


		switch ( $Order ) {
		 default:
		 case 'app_nom':
			$Request .= 'ORDER BY app_nom ';
			break;

		 case 'app_nom-desc':
			$Request .= 'ORDER BY app_nom DESC ';
			break;

		 case 'app_hebergement':
			$Request .= 'ORDER BY app_hebergement ';
			break;

		 case 'app_hebergement-desc':
			$Request .= 'ORDER BY app_hebergement DESC ';
			break;

		 case 'app_niveau_service':
			$Request .= 'ORDER BY app_niveau_service ';
			break;

		 case 'app_niveau_service-desc':
			$Request .= 'ORDER BY app_niveau_service DESC ';
			break;

		 case 'app_description':
			$Request .= 'ORDER BY app_description ';
			break;

		 case 'app_description-desc':
			$Request .= 'ORDER BY app_description DESC ';
			break;
		}


		$Query = $this->prepareSQL( $Request );


		if ($app_id != '') {
			$this->bindSQL( $Query, ':app_id', $app_id, PDO::PARAM_INT ) ;
		}

		if ($sct_id != '') {
			$this->bindSQL( $Query, ':sct_id', $sct_id, PDO::PARAM_INT ) ;
		}


		$this->executeSQL( $Query );

		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function supprimerApplication( $app_id = '' ) {
		/**
		 * Supprime une Application.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-02-20
		 *
		 * \param[in] $app_id Identifiant de l'application à supprimer
		 *
		 * \return Renvoi TRUE si l'occurrence a été supprimée, sinon FALSE. Lève une Exception en cas d'erreur.
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


	public function controlerAssociationApplication( $app_id ) {
	/**
	* Vérifie si cette Application est associée à un autre objet.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-15
	*
	* \param[in] $app_id Identifiant de l'Application à contrôler
	*
	* \return Renvoi l'occurrence listant les association de l'Application ou FALSE si pas d'entité. Lève une Exception en cas d'erreur.
	*/
		$Request = 'SELECT
			COUNT(DISTINCT acap.act_id) AS total_act
			FROM app_applications AS "app"
			LEFT JOIN acap_act_app AS "acap" ON acap.app_id = app.app_id
			WHERE app.app_id = :app_id ';

		 
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
	* Calcul le nombre total d'Applications.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2020-02-20
	*
	* \return Renvoi le nombre total d'Applications stockées en base. Lève une Exception en cas d'erreur.
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