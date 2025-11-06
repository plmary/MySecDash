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
		$app_description='', $sct_id = NULL, $app_nom_alias='' ) {
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
				( app_nom, app_nom_alias, frn_id, app_hebergement, app_niveau_service, app_description, sct_id ) VALUES
				( :app_nom, :app_nom_alias, :frn_id, :app_hebergement, :app_niveau_service, :app_description, :sct_id )';

			$Query = $this->prepareSQL( $Request );
		} else {
			$Request = 'UPDATE app_applications SET 
				app_nom = :app_nom,
				app_nom_alias = :app_nom_alias,
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
		$this->bindSQL( $Query, ':app_nom_alias', $app_nom_alias, PDO::PARAM_STR, L_APP_NOM );
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


	public function majApplicationSI( $app_id, $sct_id, $ete_id_dima_dsi, $scap_description_dima, $ete_id_pdma_dsi, $scap_description_pdma ) {
		/**
		 * Actualise les informations SI d'une Application.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-10-29
		 *
		 * \param[in] $app_id ID de l'Application
		 * \param[in] $sct_id ID de la Société
		 * \param[in] $ete_id_dima_dsi Id de l'Echelle de temps pour la DIMA
		 * \param[in] $scap_description_dima Description de la DIMA
		 * \param[in] $ete_id_pdma_dsi Id de l'Echelle de temps pour la PDMA
		 * \param[in] $scap_description_pdma Description de la PDMA
		 *
		 * \return Renvoi un booléen sur le succès de la création ou la modification de l'application
		 */

		$Request = 'SELECT count(*) AS "total" FROM scap_sct_app WHERE app_id = :app_id AND sct_id = :sct_id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':app_id', $app_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':sct_id', $sct_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		$Resultat = $Query->fetchObject();

		//print('Resultat : "'.$Resultat->total.'"');

		if ( $Resultat->total == 0 ) {
			$Request = 'INSERT INTO scap_sct_app
			( app_id, sct_id, ete_id_dima_dsi, scap_description_dima, ete_id_pdma_dsi, scap_description_pdma ) VALUES
			( :app_id, :sct_id, :ete_id_dima_dsi, :scap_description_dima, :ete_id_pdma_dsi, :scap_description_pdma )';
		} else {
			$Request = 'UPDATE scap_sct_app SET
				ete_id_dima_dsi = :ete_id_dima_dsi,
				scap_description_dima = :scap_description_dima,
				ete_id_pdma_dsi = :ete_id_pdma_dsi,
				scap_description_pdma = :scap_description_pdma
				WHERE app_id = :app_id AND sct_id = :sct_id ';
		}

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':app_id', $app_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':sct_id', $sct_id, PDO::PARAM_INT );
		if ($ete_id_dima_dsi == '') {
			$this->bindSQL( $Query, ':ete_id_dima_dsi', NULL, PDO::PARAM_NULL );
		} else {
			$this->bindSQL( $Query, ':ete_id_dima_dsi', $ete_id_dima_dsi, PDO::PARAM_INT );
		}
		$this->bindSQL( $Query, ':scap_description_dima', $scap_description_dima, PDO::PARAM_LOB );
		if ($ete_id_pdma_dsi == '') {
			$this->bindSQL( $Query, ':ete_id_pdma_dsi', NULL, PDO::PARAM_NULL );
		} else {
			$this->bindSQL( $Query, ':ete_id_pdma_dsi', $ete_id_pdma_dsi, PDO::PARAM_INT );
		}
		$this->bindSQL( $Query, ':scap_description_pdma', $scap_description_pdma, PDO::PARAM_LOB );

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


	public function majApplicationSIParChamp( $app_id, $Field, $Value ) {
		/**
		 * Créé ou actualise une Application vu par la DSI.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2025-10-21
		 *
		 * \param[in] $app_id Identifiant de l'application
		 * \param[in] $Field Nom du champ de l'application à modifier
		 * \param[in] $Value Valeur du champ de l'application à prendre en compte
		 *
		 * \return Renvoi un booléen sur le succès de la création ou la modification de l'application
		 */
		//if ( $app_id == '' or ($Field == '' and $Field != 'frn_id') or $Value == '' ) return FALSE;


		switch ( $Field ) {
			case 'app_nom':
			case 'app_nom_alias':
				$Request = 'UPDATE app_applications SET ';

				break;

			case 'ete_nom_dima_dsi':
				$Request = 'SELECT count(*) AS "total" FROM scap_sct_app WHERE app_id = :app_id AND sct_id = ' . $_SESSION['s_sct_id'];

				$Query = $this->prepareSQL( $Request );

				$this->bindSQL( $Query, ':app_id', $app_id, PDO::PARAM_INT );

				$this->executeSQL( $Query );

				$Resultat = $Query->fetchObject();

				if ( $Resultat->total == 0 ) {
					$Mode = 'insert';
					$Request = 'INSERT INTO scap_sct_app ';
				} else {
					$Mode = 'update';
					$Request = 'UPDATE scap_sct_app SET ';
				}

				break;

			case 'ete_nom_pdma_dsi':
				$Request = 'SELECT count(*) AS "total" FROM scap_sct_app WHERE app_id = :app_id AND sct_id = ' . $_SESSION['s_sct_id'];
				
				$Query = $this->prepareSQL( $Request );
				
				$this->bindSQL( $Query, ':app_id', $app_id, PDO::PARAM_INT );
				
				$this->executeSQL( $Query );
				
				$Resultat = $Query->fetchObject();
				
				if ( $Resultat->total == 0 ) {
					$Mode = 'insert';
					$Request = 'INSERT INTO scap_sct_app ';
				} else {
					$Mode = 'update';
					$Request = 'UPDATE scap_sct_app SET ';
				}

				break;

			default:
				return FALSE;
		}


		switch ( $Field ) {
			case 'app_nom':
				$Request .= 'app_nom = :Value WHERE app_id = :app_id ';

				break;

			case 'app_nom_alias':
				$Request .= 'app_nom_alias = :Value WHERE app_id = :app_id ';

				break;

			case 'ete_nom_dima_dsi':
				if ( $Mode == 'insert' ) {
					$Request .= '(app_id, sct_id, ete_id_dima_dsi) VALUES (:app_id, ' . $_SESSION['s_sct_id'] . ', :Value) ';
				} else {
					$Request .= 'ete_id_dima_dsi = :Value WHERE app_id = :app_id AND sct_id = ' . $_SESSION['s_sct_id'];
				}

				break;

			case 'ete_nom_pdma_dsi':
				if ( $Mode == 'insert' ) {
					$Request .= '(app_id, sct_id, ete_id_pdma_dsi) VALUES (:app_id, ' . $_SESSION['s_sct_id'] . ',:Value) ';
				} else {
					$Request .= 'ete_id_pdma_dsi = :Value WHERE app_id = :app_id AND sct_id = ' . $_SESSION['s_sct_id'];
				}

				break;

			default:
				return FALSE;
		}


		$Query = $this->prepareSQL( $Request );


		$this->bindSQL( $Query, ':app_id', $app_id, PDO::PARAM_INT );

		switch ( $Field ) {
			case 'app_nom':
			case 'app_nom_alias':
				$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_STR, L_APP_NOM );
				break;

			case 'ete_nom_dima_dsi':
				if ($Value != '') {
					$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_INT );
				} else {
					$this->bindSQL( $Query, ':Value', NULL, PDO::PARAM_NULL );
				}
				break;

			case 'ete_nom_pdma_dsi':
				if ($Value != NULL) {
					$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_INT );
				} else {
					$this->bindSQL( $Query, ':Value', NULL, PDO::PARAM_NULL );
				}
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

		if ($sct_id == '*') {
			$Request = 'SELECT
				*
				FROM app_applications AS "app" 
				LEFT JOIN frn_fournisseurs AS "frn" ON frn.frn_id = app.frn_id 
				LEFT JOIN sct_societes AS "sct" ON sct.sct_id = app.sct_id ';

			if (! $_SESSION['idn_super_admin']) {
				$Where = 'WHERE app.sct_id IS NULL ';
			}
/*
			if ($sct_id != '') {
				$Where .= 'OR app.sct_id = :sct_id ';
			}
*/
		} else {
			$Request = 'SELECT DISTINCT app.*, frn.*, sct.*
				FROM ent_entites AS "ent"
				LEFT JOIN act_activites AS "act" ON act.ent_id = ent.ent_id
				LEFT JOIN acap_act_app AS "acap" ON acap.act_id = act.act_id
				LEFT JOIN app_applications AS "app" ON app.app_id = acap.app_id
				LEFT JOIN frn_fournisseurs AS "frn" ON frn.frn_id = app.frn_id
				LEFT JOIN sct_societes AS "sct" ON sct.sct_id = app.sct_id ';

			$Where = 'WHERE ent.sct_id = :sct_id and app.app_nom != \'\' ';
		}

		if ($app_id != '') {
			if ($Where == '') {
				$Where .= 'WHERE ';
			} else {
				$Where .= 'AND ';
			}
			$Where .= 'app.app_id = :app_id ';
		}

		$Request = $Request . $Where;
//print($Request);

		switch ( $Order ) {
		 default:
		 case 'app_nom':
			$Request .= 'ORDER BY app_nom ';
			break;

		 case 'app_nom-desc':
			$Request .= 'ORDER BY app_nom DESC ';
			break;

		 case 'frn_nom':
			$Request .= 'ORDER BY frn_nom ';
			break;

		 case 'frn_nom-desc':
			$Request .= 'ORDER BY frn_nom DESC ';
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

		 case 'sct_id':
			$Request .= 'ORDER BY sct_id ';
			break;

		 case 'sct_id-desc':
			$Request .= 'ORDER BY sct_id DESC ';
			break;
		}

		$Query = $this->prepareSQL( $Request );

		if ($app_id != '') {
			$this->bindSQL( $Query, ':app_id', $app_id, PDO::PARAM_INT ) ;
		}

		if ($sct_id != '*') {
			$this->bindSQL( $Query, ':sct_id', $sct_id, PDO::PARAM_INT ) ;
		}


		$this->executeSQL( $Query );

		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function rechercherApplicationsSI( $Order = 'app_nom', $sct_id = '', $app_id = '' ) {
		/**
		 * Lister les Applications pour la DSI.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2025-10-15
		 *
		 * \param[in] $Order Permet de gérer l'ordre d'affichage
		 * \param[in] $cmp_id Permet de préciser la Campagne sur laquelle on recherche les applications
		 * \param[in] $app_id Permet de préciser l'Application spécifique sur laquelle on recherche
		 *
		 * \return Renvoi une liste d'Applications ou une liste vide
		 */

/*		$Request = 'SELECT app.app_id, app.app_nom, app.app_nom_alias, app.app_hebergement, app.app_niveau_service, app.app_description,
			ete4.ete_poids AS "ete_poids_dima_dsi", ete5.ete_poids AS "ete_poids_pdma_dsi", scap_description_dima, scap_description_pdma,
			app.frn_id, app.sct_id, ete4.ete_id AS "ete_id_dima_dsi", ete5.ete_id AS "ete_id_pdma_dsi",
			MIN(ete1.ete_poids) AS "ete_poids_dima", MIN(ete2.ete_poids) AS "ete_poids_pdma",
			STRING_AGG(act.act_nom || \'===\' || nim.nim_numero || \'---\' || nim.nim_nom_code || \'---\' || nim.nim_couleur || \'===\'
				|| ete3.ete_nom_code, \'###\') AS "act_noms"
			FROM act_activites AS "act"
			LEFT JOIN acap_act_app AS "acap" ON acap.act_id = act.act_id
			LEFT JOIN ete_echelle_temps AS "ete1" ON ete1.ete_id = acap.ete_id_dima
			LEFT JOIN ete_echelle_temps AS "ete2" ON ete2.ete_id = acap.ete_id_pdma
			LEFT JOIN app_applications AS "app" ON app.app_id = acap.app_id
			LEFT JOIN dma_dmia_activite AS "dma" ON dma.act_id = act.act_id
			LEFT JOIN ete_echelle_temps AS "ete3" ON ete3.ete_id = dma.ete_id
			LEFT JOIN mim_matrice_impacts AS "mim" ON mim.mim_id = dma.mim_id
			LEFT JOIN nim_niveaux_impact AS "nim" ON nim.nim_id = mim.nim_id
			LEFT JOIN scap_cmp_app AS "scap" ON scap.app_id = app.app_id AND scap.cmp_id = :cmp_id
			LEFT JOIN ete_echelle_temps AS "ete4" ON ete4.ete_id = scap.ete_id_dima_dsi
			LEFT JOIN ete_echelle_temps AS "ete5" ON ete5.ete_id = scap.ete_id_pdma_dsi
			LEFT JOIN frn_fournisseurs AS "frn" ON frn.frn_id = app.frn_id
			LEFT JOIN sct_societes AS "sct" ON sct.sct_id = app.sct_id
			WHERE act.cmp_id = :cmp_id AND app.app_id IS NOT NULL ';
*/
		$Request = 'SELECT app.app_id, app.app_nom, app.app_nom_alias, app.app_hebergement, app.app_niveau_service, app.app_description,
			ete4.ete_poids AS "ete_poids_dima_dsi", ete5.ete_poids AS "ete_poids_pdma_dsi", scap_description_dima, scap_description_pdma,
			app.frn_id, app.sct_id, ete4.ete_id AS "ete_id_dima_dsi", ete5.ete_id AS "ete_id_pdma_dsi",
			MIN(ete1.ete_poids) AS "ete_poids_dima", MIN(ete2.ete_poids) AS "ete_poids_pdma",
			STRING_AGG(act.act_nom || \'===\' || nim.nim_numero || \'---\' || nim.nim_nom_code || \'---\' || nim.nim_couleur || \'===\'
				|| ete3.ete_nom_code, \'###\') AS "act_noms"
			FROM app_applications AS "app"
			LEFT JOIN acap_act_app AS "acap" ON acap.app_id = app.app_id
			LEFT JOIN act_activites AS "act" ON acap.act_id = act.act_id
			LEFT JOIN ete_echelle_temps AS "ete1" ON ete1.ete_id = acap.ete_id_dima
			LEFT JOIN ete_echelle_temps AS "ete2" ON ete2.ete_id = acap.ete_id_pdma
			LEFT JOIN dma_dmia_activite AS "dma" ON dma.act_id = act.act_id
			LEFT JOIN ete_echelle_temps AS "ete3" ON ete3.ete_id = dma.ete_id
			LEFT JOIN mim_matrice_impacts AS "mim" ON mim.mim_id = dma.mim_id
			LEFT JOIN nim_niveaux_impact AS "nim" ON nim.nim_id = mim.nim_id
			LEFT JOIN scap_sct_app AS "scap" ON scap.app_id = app.app_id AND scap.sct_id = :sct_id
			LEFT JOIN ete_echelle_temps AS "ete4" ON ete4.ete_id = scap.ete_id_dima_dsi
			LEFT JOIN ete_echelle_temps AS "ete5" ON ete5.ete_id = scap.ete_id_pdma_dsi
			LEFT JOIN frn_fournisseurs AS "frn" ON frn.frn_id = app.frn_id
			LEFT JOIN sct_societes AS "sct" ON sct.sct_id = app.sct_id ';
//			WHERE act.cmp_id = :cmp_id ';
		
		if ($app_id != '') {
			$Request .= 'WHERE app.app_id = :app_id ';
		}

		$Request .= 'GROUP BY app.app_id, app.app_nom, app.app_nom_alias, app.app_hebergement, app.app_niveau_service, app.app_description,
			ete4.ete_poids, ete5.ete_poids, scap_description_dima, scap_description_pdma, ete4.ete_id, ete5.ete_id ';


		$Request = $Request;
		//print($Request);
		
		switch ( $Order ) {
			default:
			case 'app_nom':
				$Request .= 'ORDER BY app_nom ';
				break;
				
			case 'app_nom-desc':
				$Request .= 'ORDER BY app_nom DESC ';
				break;
				
			case 'frn_nom':
				$Request .= 'ORDER BY frn_nom ';
				break;
				
			case 'frn_nom-desc':
				$Request .= 'ORDER BY frn_nom DESC ';
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
				
			case 'sct_id':
				$Request .= 'ORDER BY sct_id ';
				break;
				
			case 'sct_id-desc':
				$Request .= 'ORDER BY sct_id DESC ';
				break;
		}


		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':sct_id', $sct_id, PDO::PARAM_INT ) ;

		if ($app_id != '') {
			$this->bindSQL( $Query, ':app_id', $app_id, PDO::PARAM_INT ) ;
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