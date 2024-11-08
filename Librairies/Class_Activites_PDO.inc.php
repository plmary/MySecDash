<?php

include_once( 'Constants.inc.php' );
include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Connecteur_BD_PDO.inc.php' );


if ( ! defined( 'L_NOM' ) ) define( 'L_NOM', 100 );
if ( ! defined( 'L_NOM_CODE' ) ) define( 'L_NOM_CODE', 60 );
if ( ! defined( 'L_DATE' ) ) define( 'L_DATE', 10 );
if ( ! defined( 'L_CONSEQUENCE_INDISPONIBILITE' ) ) define( 'L_CONSEQUENCE_INDISPONIBILITE', 100 );


class Activites extends HBL_Connexioneur_BD {

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


	public function majActivite( $act_id, $cmp_id, $ent_id, $ppr_id_responsable, $ppr_id_suppleant,
		$act_nom, $act_description, $act_effectifs_en_nominal = '', $act_effectifs_a_distance = '',
		$act_teletravail = 1, $act_dependances_internes_amont = "",
		$act_dependances_internes_aval = "", $act_justification_dmia = "") {
	/**
	* Crée ou met à jour une Activité.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-03-05
	*
	* \param[in] $act_id Identifiant de l'Activité (à préciser si modification)
	* \param[in] $cmp_id Identifiant de la Campagne de rattachement
	* \param[in] $ent_id Identifiant de l'Entité de rattachement
	* \param[in] $ppr_id_responsable Identifiant du Responsable de l'Activité
	* \param[in] $ppr_id_suppleant Identifiant du Suppléant au Responsable d'Activité
	* \param[in] $act_nom Nom de l'Activité
	* \param[in] $act_description Description de l'Activité
	* \param[in] $act_effectifs_en_nominal Nom de personne travaillant sur site
	* \param[in] $act_effectifs_a_distance Nom de personne pouvant faire du télétravail
	* \param[in] $act_teletravail Flag pour identifier si l'activité peut être faite en télétravail
	* \param[in] $act_dependances_internes_amont Décrit les dépendances internes en amont de l'Activité
	* \param[in] $act_dependances_internes_aval Décrit les dépendances internes en aval de l'Activité
	* \param[in] $act_justification_dmia Justification du DMIA mis en oeuvre
	*
	* \return Renvoi un booléen sur le succès de la création ou la modification de l'application
	*/
		if ( $act_id == '' ) {
			$Request = 'INSERT INTO act_activites
				( cmp_id, ent_id, ppr_id_responsable, act_nom, ';

			if ( $ppr_id_suppleant != '') $Request .= 'ppr_id_suppleant, ' ;
			if ( $act_description != '' ) $Request .= 'act_description, ';

			$Request .= 'act_teletravail, act_dependances_internes_amont, act_dependances_internes_aval,
				act_justification_dmia ';
			
			if ( $act_effectifs_en_nominal != '' ) {
				$Request .= ', act_effectifs_en_nominal ';
			}
			
			if ( $act_effectifs_a_distance != '' ) {
				$Request .= ', act_effectifs_a_distance ';
			}
			
			$Request .= ') VALUES ( :cmp_id, :ent_id, :ppr_id_responsable, :act_nom, ';
			
			if ( $ppr_id_suppleant != '') $Request .= ':ppr_id_suppleant, ';
			if ( $act_description != '' ) $Request .= ':act_description, ';
			
			$Request .= ':act_teletravail, :act_dependances_internes_amont, :act_dependances_internes_aval,
				:act_justification_dmia ';

			if ( $act_effectifs_en_nominal != '' ) {
				$Request .= ', :act_effectifs_en_nominal ';
			}

			if ( $act_effectifs_a_distance != '' ) {
				$Request .= ', :act_effectifs_a_distance ';
			}

			$Request .= ') ';
			
			$Query = $this->prepareSQL( $Request );
		} else {
			$Request = 'UPDATE act_activites SET
				cmp_id = :cmp_id,
				ent_id = :ent_id,
				ppr_id_responsable = :ppr_id_responsable,
				act_nom = :act_nom,
				act_teletravail = :act_teletravail,
				act_dependances_internes_amont = :act_dependances_internes_amont,
				act_dependances_internes_aval = :act_dependances_internes_aval,
				act_justification_dmia = :act_justification_dmia ';

			if ( $act_effectifs_en_nominal != '' ) {
				$Request .= ', act_effectifs_en_nominal = :act_effectifs_en_nominal ';
			}

			if ( $act_effectifs_a_distance != '' ) {
				$Request .= ', act_effectifs_a_distance = :act_effectifs_a_distance ';
			}

			if ( $ppr_id_suppleant != '' ) {
				$Request .= ', ppr_id_suppleant = :ppr_id_suppleant ';
			}

			if ( $act_description != '' ) {
				$Request .= ', act_description = :act_description ';
			}

			$Request .= 'WHERE act_id = :act_id';

			$Query = $this->prepareSQL( $Request );
			
			$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT );
		}

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ent_id', $ent_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ppr_id_responsable', $ppr_id_responsable, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':act_nom', $act_nom, PDO::PARAM_STR, L_NOM );

		if ( $act_effectifs_en_nominal != '' ) {
			$this->bindSQL( $Query, ':act_effectifs_en_nominal', $act_effectifs_en_nominal, PDO::PARAM_INT );
		}

		if ( $act_effectifs_a_distance != '' ) {
			$this->bindSQL( $Query, ':act_effectifs_a_distance', $act_effectifs_a_distance, PDO::PARAM_INT );
		}

		if ( $ppr_id_suppleant != '') $this->bindSQL( $Query, ':ppr_id_suppleant', $ppr_id_suppleant, PDO::PARAM_INT );

		if ( $act_description != '' ) $this->bindSQL( $Query, ':act_description', $act_description, PDO::PARAM_LOB );

		if ($act_teletravail == 0) $act_teletravail = FALSE;
		else $act_teletravail = TRUE;

		$this->bindSQL( $Query, ':act_teletravail', $act_teletravail, PDO::PARAM_BOOL );
		$this->bindSQL( $Query, ':act_dependances_internes_amont', $act_dependances_internes_amont, PDO::PARAM_LOB );
		$this->bindSQL( $Query, ':act_dependances_internes_aval', $act_dependances_internes_aval, PDO::PARAM_LOB );
		$this->bindSQL( $Query, ':act_justification_dmia', $act_justification_dmia, PDO::PARAM_LOB );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		if ( $act_id == '' ) {
			switch( $this->getAttribute(PDO::ATTR_DRIVER_NAME) ) {
			 default;
				$this->LastInsertId = $this->lastInsertId();
				break;

			 case 'pgsql';
				$this->LastInsertId = $this->lastInsertId( 'act_activites_act_id_seq' );
				break;
			}
		}

		return TRUE;
	}


	public function dupliquerActivite( $act_id, $n_act_nom = NULL, $flag_dmia = TRUE, $flag_fournisseurs = FALSE,
		$flag_applications = FALSE, $flag_parties_prenantes = FALSE, $flag_sites = FALSE, $n_cmp_id = NULL ) {
			/**
			 * Duplique une Activité.
			 *
			 * \license Copyright Loxense
			 * \author Pierre-Luc MARY
			 * \date 2024-10-21
			 *
			 * \param[in] $act_id Identifiant de l'Activité à dupliquer
			 *
			 * \return Renvoi l'ID de la nouvelle Activité ou FALSE en cas d'erreur. Lève une exception si un problème est rencontré.
			 */
			if ( $act_id == '' ) return FALSE;

			// ===================================================
			// Récupère les informations de l'activité d'origine.
			$Request = 'SELECT * FROM act_activites WHERE act_id = :act_id ';

			$Query = $this->prepareSQL( $Request );

			$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT );

			$this->executeSQL( $Query );

			$O_Activite = $Query->fetchObject();

			if ( $this->RowCount == 0 ) {
				return FALSE;
			}


			// ===============================================================================
			// Création de la nouvelle Activité à partir des informations de celle d'origine.
			$Request = 'INSERT INTO act_activites 
				(ent_id, cmp_id, ppr_id_responsable, ppr_id_suppleant, act_nom, act_teletravail, act_description, ';

			if ( $flag_dmia == TRUE ) {
				$Request .= 'act_justification_dmia, ';
			}

			$Request .= 'act_dependances_internes_amont, act_dependances_internes_aval, act_effectifs_en_nominal, act_effectifs_a_distance) VALUES
				(:ent_id, :cmp_id, :ppr_id_responsable, :ppr_id_suppleant, :act_nom, :act_teletravail, :act_description, ';

			if ( $flag_dmia == TRUE ) {
				$Request .= ':act_justification_dmia, ';
			}

			$Request .= ':act_dependances_internes_amont, :act_dependances_internes_aval,
				 :act_effectifs_en_nominal, :act_effectifs_a_distance) ';

			$Query = $this->prepareSQL( $Request );

			$this->bindSQL( $Query, ':ent_id', $O_Activite->ent_id, PDO::PARAM_INT );
			if ( $n_cmp_id == NULL ) {
				$this->bindSQL( $Query, ':cmp_id', $O_Activite->cmp_id, PDO::PARAM_INT );
			} else {
				$this->bindSQL( $Query, ':cmp_id', $n_cmp_id, PDO::PARAM_INT );
			}
			$this->bindSQL( $Query, ':ppr_id_responsable', $O_Activite->ppr_id_responsable, PDO::PARAM_INT );
			$this->bindSQL( $Query, ':ppr_id_suppleant', $O_Activite->ppr_id_suppleant, PDO::PARAM_INT );
			if ( $n_act_nom == NULL ) {
				$this->bindSQL( $Query, ':act_nom', $O_Activite->act_nom, PDO::PARAM_STR, L_NOM );
			} else {
				$this->bindSQL( $Query, ':act_nom', $n_act_nom, PDO::PARAM_STR, L_NOM );
			}
			$this->bindSQL( $Query, ':act_teletravail', $O_Activite->act_teletravail, PDO::PARAM_BOOL );
			$this->bindSQL( $Query, ':act_description', $O_Activite->act_description, PDO::PARAM_LOB );
			if ( $flag_dmia == TRUE ) {
				$this->bindSQL( $Query, ':act_justification_dmia', $O_Activite->act_justification_dmia, PDO::PARAM_LOB );
			}
			$this->bindSQL( $Query, ':act_dependances_internes_amont', $O_Activite->act_dependances_internes_amont, PDO::PARAM_LOB );
			$this->bindSQL( $Query, ':act_dependances_internes_aval', $O_Activite->act_dependances_internes_aval, PDO::PARAM_LOB );
			$this->bindSQL( $Query, ':act_effectifs_en_nominal', $O_Activite->act_effectifs_en_nominal, PDO::PARAM_INT );
			$this->bindSQL( $Query, ':act_effectifs_a_distance', $O_Activite->act_effectifs_a_distance, PDO::PARAM_INT );

			$this->executeSQL( $Query );

			switch( $this->getAttribute(PDO::ATTR_DRIVER_NAME) ) {
				default;
				$n_act_id = $this->lastInsertId();
				break;
				
				case 'pgsql';
				$n_act_id = $this->lastInsertId( 'act_activites_act_id_seq' );
				break;
			}


			if ( $flag_dmia == TRUE ) {
				// ============================
				// Duplique le détail du DMIA.
				$Request = 'SELECT * FROM dma_dmia_activite WHERE act_id = :act_id ';

				$Query = $this->prepareSQL( $Request );

				$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT );

				$this->executeSQL( $Query );

				foreach( $Query->fetchAll( PDO::FETCH_CLASS ) as $DMIA ) {
					$Request = 'INSERT INTO dma_dmia_activite 
						(act_id, ete_id, mim_id, cmp_id) VALUES 
						(:act_id, :ete_id, :mim_id, :cmp_id) ';
	
					$Query = $this->prepareSQL( $Request );
	
					$this->bindSQL( $Query, ':act_id', $n_act_id, PDO::PARAM_INT );
					$this->bindSQL( $Query, ':ete_id', $DMIA->ete_id, PDO::PARAM_INT );
					$this->bindSQL( $Query, ':mim_id', $DMIA->mim_id, PDO::PARAM_INT );
					if ( $n_cmp_id == NULL ) {
						$this->bindSQL( $Query, ':cmp_id', $DMIA->cmp_id, PDO::PARAM_INT );
					} else {
						$this->bindSQL( $Query, ':cmp_id', $n_cmp_id, PDO::PARAM_INT );
					}
					
					$this->executeSQL( $Query );
				}
			}


			if ( $flag_fournisseurs == TRUE ) {
				// ============================
				// Duplique les Fournisseurs.
				$Request = 'SELECT * FROM acfr_act_frn WHERE act_id = :act_id ';

				$Query = $this->prepareSQL( $Request );

				$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT );

				$this->executeSQL( $Query );

				foreach( $Query->fetchAll( PDO::FETCH_CLASS ) as $Element ) {
					$Request = 'INSERT INTO acfr_act_frn
						(act_id, frn_id, ete_id, acfr_consequence_indisponibilite, acfr_palliatif_tiers) VALUES
						(:act_id, :frn_id, :ete_id, :acfr_consequence_indisponibilite, :acfr_palliatif_tiers) ';

					$Query = $this->prepareSQL( $Request );

					$this->bindSQL( $Query, ':act_id', $n_act_id, PDO::PARAM_INT );
					$this->bindSQL( $Query, ':frn_id', $Element->frn_id, PDO::PARAM_INT );
					$this->bindSQL( $Query, ':ete_id', $Element->ete_id, PDO::PARAM_INT );
					$this->bindSQL( $Query, ':acfr_consequence_indisponibilite', $Element->acfr_consequence_indisponibilite, PDO::PARAM_LOB );
					$this->bindSQL( $Query, ':acfr_palliatif_tiers', $Element->acfr_palliatif_tiers, PDO::PARAM_LOB );

					$this->executeSQL( $Query );
				}
			}


			if ( $flag_applications == TRUE ) {
				// ============================
				// Duplique les Applications.
				$Request = 'SELECT * FROM acap_act_app WHERE act_id = :act_id ';

				$Query = $this->prepareSQL( $Request );

				$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT );

				$this->executeSQL( $Query );

				foreach( $Query->fetchAll( PDO::FETCH_CLASS ) as $Element ) {
					$Request = 'INSERT INTO acap_act_app
						(act_id, app_id, ete_id_dima, ete_id_pdma, acap_palliatif, acap_donnees, acap_hebergement, acap_niveau_service) VALUES
						(:act_id, :app_id, :ete_id_dima, :ete_id_pdma, :acap_palliatif, :acap_donnees, :acap_hebergement, :acap_niveau_service) ';

					$Query = $this->prepareSQL( $Request );

					$this->bindSQL( $Query, ':act_id', $n_act_id, PDO::PARAM_INT );
					$this->bindSQL( $Query, ':app_id', $Element->app_id, PDO::PARAM_INT );
					$this->bindSQL( $Query, ':ete_id_dima', $Element->ete_id_dima, PDO::PARAM_INT );
					$this->bindSQL( $Query, ':ete_id_pdma', $Element->ete_id_pdma, PDO::PARAM_INT );
					$this->bindSQL( $Query, ':acap_palliatif', $Element->acap_palliatif, PDO::PARAM_LOB );
					$this->bindSQL( $Query, ':acap_donnees', $Element->acap_donnees, PDO::PARAM_LOB );
					$this->bindSQL( $Query, ':acap_hebergement', $Element->acap_hebergement, PDO::PARAM_LOB );
					$this->bindSQL( $Query, ':acap_niveau_service', $Element->acap_niveau_service, PDO::PARAM_LOB );

					$this->executeSQL( $Query );
				}
			}


			if ( $flag_parties_prenantes == TRUE ) {
				// ================================
				// Duplique les Parties Prenantes.
				$Request = 'SELECT * FROM ppac_ppr_act WHERE act_id = :act_id ';

				$Query = $this->prepareSQL( $Request );

				$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT );

				$this->executeSQL( $Query );

				foreach( $Query->fetchAll( PDO::FETCH_CLASS ) as $Element ) {
					$Request = 'INSERT INTO ppac_ppr_act
						(act_id, ppr_id, cmp_id, ppac_description) VALUES
						(:act_id, :ppr_id, :cmp_id, :ppac_description) ';

					$Query = $this->prepareSQL( $Request );

					$this->bindSQL( $Query, ':act_id', $n_act_id, PDO::PARAM_INT );
					$this->bindSQL( $Query, ':ppr_id', $Element->ppr_id, PDO::PARAM_INT );
					if ( $n_cmp_id == NULL ) {
						$this->bindSQL( $Query, ':cmp_id', $Element->cmp_id, PDO::PARAM_INT );
					} else {
						$this->bindSQL( $Query, ':cmp_id', $n_cmp_id, PDO::PARAM_INT );
					}
					$this->bindSQL( $Query, ':ppac_description', $Element->ppac_description, PDO::PARAM_LOB );

					$this->executeSQL( $Query );
				}
			}


			if ( $flag_sites == TRUE ) {
				// ================================
				// Duplique les Sites.
				$Request = 'SELECT * FROM acst_act_sts WHERE act_id = :act_id ';

				$Query = $this->prepareSQL( $Request );

				$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT );

				$this->executeSQL( $Query );

				foreach( $Query->fetchAll( PDO::FETCH_CLASS ) as $Element ) {
					$Request = 'INSERT INTO acst_act_sts
						(act_id, sts_id, cmp_id, acst_type_site, acst_strategie_montee_charge, acst_description_entraides) VALUES
						(:act_id, :sts_id, :cmp_id, :acst_type_site, :acst_strategie_montee_charge, :acst_description_entraides) ';

					$Query = $this->prepareSQL( $Request );

					$this->bindSQL( $Query, ':act_id', $n_act_id, PDO::PARAM_INT );
					$this->bindSQL( $Query, ':sts_id', $Element->sts_id, PDO::PARAM_INT );
					if ( $n_cmp_id == NULL ) {
						$this->bindSQL( $Query, ':cmp_id', $Element->cmp_id, PDO::PARAM_INT );
					} else {
						$this->bindSQL( $Query, ':cmp_id', $n_cmp_id, PDO::PARAM_INT );
					}
					$this->bindSQL( $Query, ':acst_type_site', $Element->acst_type_site, PDO::PARAM_INT );
					$this->bindSQL( $Query, ':acst_strategie_montee_charge', $Element->acst_strategie_montee_charge, PDO::PARAM_LOB );
					$this->bindSQL( $Query, ':acst_description_entraides', $Element->acst_description_entraides, PDO::PARAM_LOB );

					$this->executeSQL( $Query );
				}
			}


			return $n_act_id;
	}


	public function majActiviteParChamp( $act_id, $Field, $Value ) {
	/**
	* Actualise les champs d'une Activité.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-03-05
	*
	* \param[in] $act_id Identifiant de l'Activité
	* \param[in] $Field Nom du champ de la Partie Prenante à modifier
	* \param[in] $Value Valeur du champ de la Partie Prenante à prendre en compte
	*
	* \return Renvoi un booléen sur le succès de la création ou la modification de l'application
	*/
		if ( $act_id == '' or $Field == '' or $Value == '' ) return FALSE;


		$Request = 'UPDATE act_activites SET ';

		switch ( $Field ) {
			case 'ent_id':
			case 'ppr_id_responsable':
			case 'ppr_id_suppleant':
			case 'act_nom':
			case 'act_description':
			case 'act_teletravail':
				$Request .= $Field . ' = :Value ';
				break;

			default:
				return FALSE;
		}

		$Request .= 'WHERE act_id = :act_id';

		$Query = $this->prepareSQL( $Request );


		$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT );
		
		switch ( $Field ) {
			case 'ent_id':
			case 'ppr_id_responsable':
			case 'ppr_id_suppleant':
				$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_INT );
				break;

			case 'act_nom':
				$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_STR, L_NOM );
				break;

			case 'act_description':
				$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_LOB );
				break;

			case 'act_teletravail':
				$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_BOOLEAN );
				break;
		}


		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		
		return TRUE;
	}


	public function rechercherActivites( $cmp_id, $ent_id, $Order = 'act_nom', $act_id = '' ) {
	/**
	* Lister les Activités.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-03-05
	*
	* \param[in] $cmp_id Identifiant de la Campagne de rattachement
	* \param[in] $ent_id Identifiant de l'Entité de rattachement
	* \param[in] $Order Permet de gérer l'ordre d'affichage
	* \param[in] $act_id Identifiant de l'Activité spécifique à récupérer
	*
	* \return Renvoi une liste des Activités ou une liste vide
	*/
		$Request = 'SELECT
act.act_id, act.ppr_id_responsable, act.ppr_id_suppleant,
ppr_resp.ppr_nom AS "ppr_nom_resp", ppr_resp.ppr_prenom AS "ppr_prenom_resp",
ppr_supp.ppr_nom AS "ppr_nom_supp", ppr_supp.ppr_prenom AS "ppr_prenom_supp",
act_nom, act_description, act_effectifs_en_nominal, act_effectifs_a_distance, act_teletravail, act_dependances_internes_amont,
act_dependances_internes_aval, act_justification_dmia, acst0.sts_id AS "sts_id_nominal",
acst1.sts_id AS "sts_id_secours", min_max.nim_poids, max_dmia.ete_poids,
COUNT(DISTINCT ppac.ppr_id) AS "total_ppr",
COUNT(DISTINCT acst.sts_id) AS "total_sts",
COUNT(DISTINCT dma.ete_id) AS "total_dma",
COUNT(DISTINCT frn_id) AS "total_frn",
COUNT(DISTINCT app_id) AS "total_app"

FROM act_activites AS "act"

LEFT JOIN dma_dmia_activite AS "dma" ON dma.act_id = act.act_id
LEFT JOIN ete_echelle_temps AS "ete" ON ete.ete_id = dma.ete_id
LEFT JOIN mim_matrice_impacts AS "mim" ON mim.mim_id = dma.mim_id
LEFT JOIN acfr_act_frn AS "acfr" ON acfr.act_id = act.act_id
LEFT JOIN acap_act_app AS "acap" ON acap.act_id = act.act_id
LEFT JOIN ppr_parties_prenantes AS "ppr_resp" ON ppr_resp.ppr_id = act.ppr_id_responsable
LEFT JOIN ppr_parties_prenantes AS "ppr_supp" ON ppr_supp.ppr_id = act.ppr_id_suppleant
LEFT JOIN ppac_ppr_act AS "ppac" ON ppac.act_id = act.act_id
LEFT JOIN acst_act_sts AS "acst" ON acst.act_id = act.act_id
LEFT JOIN acst_act_sts AS "acst0" ON acst0.act_id = act.act_id and acst0.acst_type_site = 0
LEFT JOIN acst_act_sts AS "acst1" ON acst1.act_id = act.act_id and acst1.acst_type_site = 1
LEFT JOIN
	(SELECT act_id, max(nim.nim_poids) AS "nim_poids" 
	FROM dma_dmia_activite AS "dma" 
	LEFT JOIN mim_matrice_impacts AS "mim" ON mim.mim_id = dma.mim_id 
	LEFT JOIN nim_niveaux_impact AS "nim" ON nim.nim_id = mim.nim_id 
	GROUP BY act_id) AS "min_max" ON act.act_id = min_max.act_id
LEFT JOIN
	(SELECT act_id, min(ete.ete_poids) AS "ete_poids"
	FROM dma_dmia_activite AS "dma2"
	LEFT JOIN mim_matrice_impacts AS "mim" ON mim.mim_id = dma2.mim_id
	RIGHT JOIN nim_niveaux_impact AS "nim" ON nim.nim_id = mim.nim_id AND nim.nim_poids > 2
	LEFT JOIN ete_echelle_temps AS "ete" ON ete.ete_id = dma2.ete_id
	GROUP BY act_id) AS "max_dmia" ON max_dmia.act_id = act.act_id

WHERE act.cmp_id = :cmp_id AND act.ent_id = :ent_id ';

		if ( $act_id != '' ) $Request .= 'AND act.act_id = :act_id ';

		$Request .= 'GROUP BY act.act_id, act.ppr_id_responsable, act.ppr_id_suppleant,
ppr_nom_resp, ppr_prenom_resp, ppr_nom_supp, ppr_prenom_supp, act_effectifs_en_nominal, act_effectifs_a_distance, 
act_nom, act_description, act_teletravail, act_dependances_internes_amont,
act_dependances_internes_aval, act_justification_dmia, acst0.sts_id, acst1.sts_id,
min_max.nim_poids, max_dmia.ete_poids ';

		switch ( $Order ) {
		 default:
		 case 'ppr_id_responsable':
			$Request .= 'ORDER BY ppr_nom_resp, ppr_prenom_resp ';
			break;

		 case 'ppr_id_responsable-desc':
			$Request .= 'ORDER BY ppr_nom_resp DESC, ppr_prenom_resp DESC ';
			break;

		 case 'ppr_id_suppleant':
		 	$Request .= 'ORDER BY ppr_nom_supp, ppr_prenom_supp ';
		 	break;
		 	
		 case 'ppr_id_suppleant-desc':
		 	$Request .= 'ORDER BY ppr_nom_supp DESC, ppr_prenom_supp DESC ';
		 	break;
		 	
		 case 'act_nom':
			$Request .= 'ORDER BY act_nom ';
			break;

		 case 'act_nom-desc':
			$Request .= 'ORDER BY act_nom DESC ';
			break;

		 case 'act_description':
			$Request .= 'ORDER BY act_description ';
			break;

		 case 'act_description-desc':
			$Request .= 'ORDER BY act_description DESC ';
			break;

		 case 'act_teletravail':
			$Request .= 'ORDER BY act_teletravail ';
			break;

		 case 'act_teletravail-desc':
			$Request .= 'ORDER BY act_teletravail DESC ';
			break;

		 case 'nim_poids':
			$Request .= 'ORDER BY nim_poids, ete_poids, act_nom ';
			break;

		 case 'nim_poids-desc':
			$Request .= 'ORDER BY nim_poids DESC, ete_poids, act_nom ';
			break;

		 case 'ete_poids':
			$Request .= 'ORDER BY ete_poids, nim_poids ';
			break;

		 case 'ete_poids-desc':
			$Request .= 'ORDER BY ete_poids DESC, nim_poids ';
			break;
		}

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT ) ;
		$this->bindSQL( $Query, ':ent_id', $ent_id, PDO::PARAM_INT ) ;
		
		if ( $act_id != '' ) $this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function listerActivitesUtilisateur( $Order = 'act_nom' ) {
		/**
		 * Lister les Activités Autorisées à l'Utilisateur.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-11-06
		 *
		 * \param[in] $Order Permet de gérer l'ordre d'affichage
		 *
		 * \return Renvoi une liste des Activités ou une liste vide
		 */
		$Request = 'SELECT
sct.sct_nom,
ent.ent_nom, ent.ent_description,
act.act_id, act.act_nom,
ete.ete_poids, ete.ete_nom_code,
nim.nim_numero, nim.nim_poids, nim.nim_nom_code, nim.nim_couleur,
tim.tim_poids, tim.tim_nom_code

FROM idsc_idn_sct AS "idsc"
LEFT JOIN sct_societes AS "sct" ON sct.sct_id = idsc.sct_id
LEFT JOIN ent_entites AS "ent" ON ent.sct_id = idsc.sct_id
LEFT JOIN act_activites AS "act" ON act.ent_id = ent.ent_id
LEFT JOIN dma_dmia_activite AS "dma" ON dma.act_id = act.act_id
LEFT JOIN ete_echelle_temps AS "ete" ON ete.ete_id = dma.ete_id
LEFT JOIN mim_matrice_impacts AS "mim" ON mim.mim_id = dma.mim_id
LEFT JOIN nim_niveaux_impact AS "nim" ON nim.nim_id = mim.nim_id
LEFT JOIN tim_types_impact AS "tim" ON tim.tim_id = mim.tim_id ';

		if ( $_SESSION['idn_super_admin'] === FALSE ) {
			$Request .= 'WHERE idsc.idn_id = :idn_id ';
		}

		switch ( $Order ) {
			default:
			case 'act_nom':
				$Request .= 'ORDER BY sct_nom, ent_nom, act_nom, ete_poids, nim_poids ';
				break;
				
			case 'act_nom-desc':
				$Request .= 'ORDER BY sct_nom, ent_nom, act_nom DESC, ete_poids, nim_poids ';
				break;
				
			case 'nim_poids':
				$Request .= 'ORDER BY sct_nom, ent_nom, act_nom, ete_poids, nim_poids ';
				break;
				
			case 'nim_poids-desc':
				$Request .= 'ORDER BY sct_nom, ent_nom, act_nom, ete_poids, nim_poids DESC';
				break;
				
			case 'tim_poids':
				$Request .= 'ORDER BY tim_poids, ete_poids, act_nom ';
				break;
				
			case 'tim_poids-desc':
				$Request .= 'ORDER BY tim_poids DESC, ete_poids, act_nom ';
				break;
				
			case 'ete_poids':
				$Request .= 'ORDER BY ete_poids, nim_poids ';
				break;
				
			case 'ete_poids-desc':
				$Request .= 'ORDER BY ete_poids DESC, nim_poids ';
				break;
		}

		$Query = $this->prepareSQL( $Request );

		if ( $_SESSION['idn_super_admin'] === FALSE ) $this->bindSQL( $Query, ':idn_id', $_SESSION['idn_id'], PDO::PARAM_INT );

		$this->executeSQL( $Query );

		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function supprimerActivite( $act_id ) {
	/**
	* Supprime une Activité.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-03-05
	*
	* \param[in] $act_id Identifiant de l'Activité à supprimer
	*
	* \return Renvoi TRUE si l'occurrence a été supprimée, sinon FALSE. Lève une Exception en cas d'erreur.
	*/
		if ( $act_id == '' ) return FALSE;
	
		$Query = $this->prepareSQL( 'DELETE
		 FROM act_activites
		 WHERE act_id = :act_id ' );
		
		$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT ) ;
		
		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return TRUE;
	}


	public function controlerAssociationActivite( $act_id ) {
	/**
	* Vérifie si cette Activité est associée à un autre objet.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-03-05
	*
	* \param[in] $act_id Identifiant de la Partie Prenante à contrôler
	*
	* \return Renvoi l'occurrence listant les associations ou FALSE si pas d'association. Lève une Exception en cas d'erreur.
	*/
		$Request = 'SELECT
			COUNT(DISTINCT ppr_id) AS "total_ppr",
			COUNT(DISTINCT sts_id) AS "total_sts",
			COUNT(DISTINCT dma.ete_id) AS "total_dma",
			COUNT(DISTINCT frn_id) AS "total_frn",
			COUNT(DISTINCT app_id) AS "total_app"
			FROM act_activites AS "act"
			LEFT JOIN ppac_ppr_act AS "ppac" ON ppac.act_id = act.act_id
			LEFT JOIN acst_act_sts AS "acst" ON acst.act_id = act.act_id
			LEFT JOIN dma_dima_activite AS "dma" ON dma.act_id = act.act_id
			LEFT JOIN acfr_act_frn AS "acfr" ON acfr.act_id = act.act_id
			LEFT JOIN acap_act_app AS "acap" ON acap.act_id = act.act_id
			WHERE act.act_id = :act_id ';

		 
		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return $Query->fetchObject();
	}


	public function totalActivites() {
	/**
	* Calcul le nombre total des Activités.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-03-05
	*
	* \return Renvoi le nombre total des Activités stockées en base. Lève une Exception en cas d'erreur.
	*/

		$Request = 'SELECT ' .
		 'count(*) AS total ' .
		 'FROM act_activites ' ;

		$Query = $this->prepareSQL( $Request );

		$this->executeSQL( $Query );
		
		$Occurrence = $Query->fetchObject() ;
		
		return $Occurrence->total;
	}


	public function rechercherRolesPartiePrenante( $colonne_trie = 'rpp_nom_code', $rpp_id = '' ) {
		/**
		 * Lister les Types de Partie Prenante.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-02-27
		 *
		 * \return Renvoi la liste des Types de Partie Prenante ou une liste vide. Lève une Exception en cas d'erreur.
		 */
		$Request = 'SELECT * FROM rpp_roles_parties_prenantes AS "rpp" ';
		
		if ( $rpp_id != '' ) $Request .= 'WHERE rpp_id = :rpp_id ';
		
		switch( $colonne_trie ) {
			case 'rpp_nom_code':
				$Request .= 'ORDER BY rpp_nom_code ';
				break;
				
			case 'rpp_nom_code-desc':
				$Request .= 'ORDER BY rpp_nom_code DESC ';
				break;
		}
		
		$Query = $this->prepareSQL( $Request );
		
		if ( $rpp_id!= '' ) $this->bindSQL( $Query, ':rpp_id', $rpp_id, PDO::PARAM_INT ) ;
		
		$this->executeSQL( $Query );
		
		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function majRolePartiePrenante( $rpp_id, $rpp_nom_code ) {
		/**
		 * Crée ou met à jour le Rôle d'une Partie Prenante.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-02-27
		 *
		 * \param[in] $rpp_id Identifiant du Rôle de la Partie Prenante
		 * \param[in] $rpp_nom_code Nom de code du Rôle de la Partie Prenante
		 *
		 * \return Renvoi un booléen sur le succès de la création ou la modification du Rôle de la Partie Prenante
		 */
		if ( $rpp_id == '' ) {
			$Request = 'INSERT INTO rpp_roles_parties_prenantes
				( rpp_nom_code )
				VALUES ( :rpp_nom_code ) ';
			
			$Query = $this->prepareSQL( $Request );
		} else {
			$Request = 'UPDATE rpp_roles_parties_prenantes SET
				rpp_nom_code = :rpp_nom_code
				WHERE rpp_id = :rpp_id';
			
			$Query = $this->prepareSQL( $Request );
			
			$this->bindSQL( $Query, ':rpp_id', $rpp_id, PDO::PARAM_INT );
		}
		
		
		$this->bindSQL( $Query, ':rpp_nom_code', $rpp_nom_code, PDO::PARAM_STR, L_NOM_CODE );
		
		
		$this->executeSQL( $Query );
		
		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
		
		
		if ( $rpp_id == '' ) {
			switch( $this->getAttribute(PDO::ATTR_DRIVER_NAME) ) {
				default;
				$this->LastInsertId = $this->lastInsertId();
				break;
				
				case 'pgsql';
				$this->LastInsertId = $this->lastInsertId( 'rpp_roles_parties_prenantes_rpp_id_seq' );
				break;
			}
		}
		
		return TRUE;
	}


	public function majRolePartiePrenanteParChamp( $rpp_id, $Field, $Value ) {
		/**
		 * Actualise les champs d'un Role des Parties Prenantes.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-02-29
		 *
		 * \param[in] $rpp_id Identifiant de la Partie Prenante
		 * \param[in] $Field Nom du champ de la Partie Prenante à modifier
		 * \param[in] $Value Valeur du champ de la Partie Prenante à prendre en compte
		 *
		 * \return Renvoi un booléen sur le succès de la création ou la modification de l'application
		 */
		if ( $rpp_id == '' or $Field == '' or $Value == '' ) return FALSE;
		
		
		$Request = 'UPDATE rpp_roles_parties_prenantes SET ';
		
		switch ( $Field ) {
			case 'rpp_nom_code':
				$Request .= $Field . ' = :Value ';
				break;
				
			default:
				return FALSE;
		}
		
		$Request .= 'WHERE rpp_id = :rpp_id';
		
		$Query = $this->prepareSQL( $Request );
		
		
		$this->bindSQL( $Query, ':rpp_id', $rpp_id, PDO::PARAM_INT );
		
		switch ( $Field ) {
			case 'rpp_nom_code':
				$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_STR, L_NOM_CODE );
				break;
		}
		
		
		$this->executeSQL( $Query );
		
		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
		
		
		return TRUE;
	}


	public function supprimerRolePartiePrenante( $rpp_id ) {
		/**
		 * Supprime le Rôle d'une Partie Prenante.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-02-29
		 *
		 * \param[in] $rpp_id Identifiant du Rôle de la Partie Prenante
		 *
		 * \return Renvoi un TRUE en cas de succès, sinon FALSE
		 */
		if ( $rpp_id == '' ) return FALSE;
		
		$Query = $this->prepareSQL( 'DELETE ' .
			'FROM rpp_roles_parties_prenantes ' .
			'WHERE rpp_id = :rpp_id' );
		
		$this->bindSQL( $Query, ':rpp_id', $rpp_id, PDO::PARAM_INT ) ;
		
		$this->executeSQL( $Query );
		
		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
		
		return TRUE;
	}


	public function totalRolesPartiePrenante() {
		/**
		 * Calcul le nombre total des Types de Partie Prenante.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-02-22
		 *
		 * \return Renvoi le nombre total des Types de Partie Prenante stockées en base. Lève une Exception en cas d'erreur.
		 */
		
		$Request = 'SELECT ' .
			'count(*) AS total ' .
			'FROM rpp_roles_parties_prenantes ' ;
		
		$Query = $this->prepareSQL( $Request );
		
		$this->executeSQL( $Query );
		
		$Occurrence = $Query->fetchObject() ;
		
		return $Occurrence->total;
	}


	public function ajouterSiteRattachementActivite( $cmp_id, $act_id, $sts_id, $acst_type_site=0, $acst_strategie_montee_charge='', $acst_description_entraides='' ) {
		/**
		 * Ajoute un Site à l'Activité (par défaut, ajout du site nominal).
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-05-06
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne de rattachement
		 * \param[in] $act_id Identifiant de l'Activité de rattachement
		 * \param[in] $sts_id Identifiant de la Société de rattachement
		 * \param[in] $acst_type_site Flag pour spécifier le Type de Site (0=Nominal, 1=Secours)
		 * \param[in] $acst_strategie_montee_charge Texte libre pour décrire la Stratégie de Montée en Charge
		 * \param[in] $acst_description_entraides Texte libre pour décrire l'Entraides attendu pour la Montée en charge
		 *
		 * \return Renvoi TRUE si l'occurrence a été créée. Lève une Exception en cas d'erreur.
		 */
//print('top 1<br>');
		$Request = 'SELECT COUNT(*) AS "total" FROM cmst_cmp_sts WHERE cmp_id = :cmp_id AND sts_id = :sts_id ';

		$Query = $this->prepareSQL( $Request );
//print('top 2<br>');
		
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT ) ;
		$this->bindSQL( $Query, ':sts_id', $sts_id, PDO::PARAM_INT ) ;
//print('top 3<br>');
		
		$this->executeSQL( $Query );

		$cmst = $Query->fetchObject();
//print_r($cmst);print('<hr>');print_r($cmst->total);print('<hr>');
		if ( $cmst->total != 1 ) {
			$Request = 'INSERT INTO cmst_cmp_sts (cmp_id, sts_id) VALUES (:cmp_id, :sts_id) ';
			$Query = $this->prepareSQL( $Request );
			$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT ) ;
			$this->bindSQL( $Query, ':sts_id', $sts_id, PDO::PARAM_INT ) ;
			$this->executeSQL( $Query );
		}


		$Request = 'INSERT INTO acst_act_sts ( ' .
			'cmp_id, act_id, sts_id, acst_type_site';
		if ( $acst_strategie_montee_charge != '' ) $Request .= ', acst_strategie_montee_charge';
		if ( $acst_description_entraides != '' ) $Request .= ', acst_description_entraides';
		$Request .= ' ) VALUES ( ' .
			':cmp_id, :act_id, :sts_id, :acst_type_site';
		if ( $acst_strategie_montee_charge != '' ) $Request .= ', :acst_strategie_montee_charge';
		if ( $acst_description_entraides != '' ) $Request .= ', :acst_description_entraides';
		$Request .= ' ) ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT ) ;
		$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT ) ;
		$this->bindSQL( $Query, ':sts_id', $sts_id, PDO::PARAM_INT ) ;
		$this->bindSQL( $Query, ':acst_type_site', $acst_type_site, PDO::PARAM_INT) ;
		if ( $acst_strategie_montee_charge != '' ) $this->bindSQL( $Query, ':acst_strategie_montee_charge', $acst_strategie_montee_charge, PDO::PARAM_LOB ) ;
		if ( $acst_description_entraides != '' ) $this->bindSQL( $Query, ':acst_description_entraides', $acst_description_entraides, PDO::PARAM_LOB ) ;
		
		$this->executeSQL( $Query );
				
		return TRUE;
	}


	public function modifierSiteRattachementActivite( $cmp_id, $act_id, $sts_id, $acst_type_site, $acst_strategie_montee_charge='', $acst_description_entraides='' ) {
		/**
		 * Modifie un rattachement de Site à l'Activité.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-05-06
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne de rattachement
		 * \param[in] $act_id Identifiant de l'Activité de rattachement
		 * \param[in] $sts_id Identifiant de la Société de rattachement
		 * \param[in] $acst_type_site Flag pour spécifier le Type de Site (0=Nominal, 1=Secours)
		 * \param[in] $acst_strategie_montee_charge Texte libre pour décrire la Stratégie de Montée en Charge
		 * \param[in] $acst_description_entraides Texte libre pour décrire l'Entraides attendu pour la Montée en charge
		 *
		 * \return Renvoi TRUE si l'occurrence a été modifiée. Lève une Exception en cas d'erreur.
		 */

		$Request = 'UPDATE acst_act_sts SET
			acst_type_site = :acst_type_site ';
		if ( $acst_strategie_montee_charge != '' ) {
			$Request .= ', acst_strategie_montee_charge = :acst_strategie_montee_charge ';
		}
		if ( $acst_description_entraides != '' ) {
			$Request .= ', acst_description_entraides = :acst_description_entraides ';
		}

		$Request .= 'WHERE act_id = :act_id AND sts_id = :sts_id AND cmp_id = :cmp_id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT ) ;
		$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT ) ;
		$this->bindSQL( $Query, ':sts_id', $sts_id, PDO::PARAM_INT ) ;
		$this->bindSQL( $Query, ':acst_type_site', $acst_type_site, PDO::PARAM_INT) ;
		if ( $acst_strategie_montee_charge != '' ) $this->bindSQL( $Query, ':acst_strategie_montee_charge', $acst_strategie_montee_charge, PDO::PARAM_LOB ) ;
		if ( $acst_description_entraides != '' ) $this->bindSQL( $Query, ':acst_description_entraides', $acst_description_entraides, PDO::PARAM_LOB ) ;

		$this->executeSQL( $Query );
		
		return TRUE;
	}


	public function supprimerSiteRattachementActivite( $cmp_id, $act_id, $sts_id ) {
		/**
		 * Supprime la relation d'un Site à une Activité.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-05-06
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne de rattachement
		 * \param[in] $act_id Identifiant de l'Activité de rattachement
		 * \param[in] $sts_id Identifiant de la Société de rattachement
		 * 
		 * \return Renvoi TRUE si l'occurrence a été supprimée. Lève une Exception en cas d'erreur.
		 */
		
		$Request = 'DELETE FROM acst_act_sts
			WHERE act_id = :act_id AND sts_id = :sts_id AND cmp_id = :cmp_id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT ) ;
		$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT ) ;
		$this->bindSQL( $Query, ':sts_id', $sts_id, PDO::PARAM_INT ) ;

		$this->executeSQL( $Query );

		return TRUE;
	}


	public function rechercherSitesCampagne( $cmp_id, $colonne_trie = 'sts_nom' ) {
		/**
		 * Rechercher les Sites associés à une Campagne.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-08-13
		 *
		 * \return Renvoi la liste des Sites ou une liste vide. Lève une Exception en cas d'erreur.
		 */
		$Request = 'SELECT * FROM cmst_cmp_sts AS "cmst" 
			LEFT JOIN sts_sites AS "sts" ON sts.sts_id = cmst.sts_id
			WHERE cmst.cmp_id = :cmp_id ';

		switch( $colonne_trie ) {
			default:
			case 'sts_nom':
				$Request .= 'ORDER BY sts_nom ';
				break;

			case 'sts_nom-desc':
				$Request .= 'ORDER BY sts_nom DESC ';
				break;

			case 'sts_description':
				$Request .= 'ORDER BY sts_description ';
				break;

			case 'sts_description-desc':
				$Request .= 'ORDER BY sts_description DESC ';
				break;
		}

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT ) ;

		$this->executeSQL( $Query );

		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function ajouterDMIA($cmp_id, $act_id, $ete_id, $mim_id) {
		/**
		 * Ajoute le DMIA à l'Activité.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-08-18
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne à associer
		 * \param[in] $act_id Identifiant de l'Activité à associer
		 * \param[in] $ete_id Identifiant de l'Echelle à associer
		 * \param[in] $mim_id Identifiant de la Matrice d'Impact à associer
		 *
		 * \return Renvoi TRUE si l'occurrence est ajoutée. Lève une Exception en cas d'erreur.
		 */
		$Request = 'INSERT INTO dma_dmia_activite (cmp_id, act_id, ete_id, mim_id)
			VALUES (:cmp_id, :act_id, :ete_id, :mim_id) ';
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT ) ;
		$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT ) ;
		$this->bindSQL( $Query, ':ete_id', $ete_id, PDO::PARAM_INT ) ;
		$this->bindSQL( $Query, ':mim_id', $mim_id, PDO::PARAM_INT ) ;
		
		$this->executeSQL( $Query );
		
		return TRUE;
	}


	public function modifierDMIA($cmp_id, $act_id, $ete_id, $mim_id_old, $mim_id) {
		/**
		 * Modifie le DMIA à l'Activité.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-08-18
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne à associer
		 * \param[in] $act_id Identifiant de l'Activité à associer
		 * \param[in] $ete_id Identifiant de l'Echelle à associer
		 * \param[in] $mim_id_old Identifiant de la Matrice d'Impact à associer
		 * \param[in] $mim_id Identifiant de la Matrice d'Impact à associer
		 *
		 * \return Renvoi TRUE si l'occurrence est supprimée et ajoutée. Lève une Exception en cas d'erreur.
		 */
		if ($mim_id_old != 'undefined') {
			$Request = 'DELETE FROM dma_dmia_activite
				WHERE cmp_id = :cmp_id AND act_id = :act_id AND ete_id = :ete_id AND mim_id = :mim_id ';

			$Query = $this->prepareSQL( $Request );

			$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT ) ;
			$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT ) ;
			$this->bindSQL( $Query, ':ete_id', $ete_id, PDO::PARAM_INT ) ;
			$this->bindSQL( $Query, ':mim_id', $mim_id_old, PDO::PARAM_INT ) ;

			$this->executeSQL( $Query );
		}


		$Request = 'INSERT INTO dma_dmia_activite (cmp_id, act_id, ete_id, mim_id)
			VALUES (:cmp_id, :act_id, :ete_id, :mim_id) ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT ) ;
		$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT ) ;
		$this->bindSQL( $Query, ':ete_id', $ete_id, PDO::PARAM_INT ) ;
		$this->bindSQL( $Query, ':mim_id', $mim_id, PDO::PARAM_INT ) ;

		$this->executeSQL( $Query );

		return TRUE;
	}


	public function recupererDMIA($cmp_id, $act_id) {
		/**
		 * Récupère le DMIA des Activités associées à une Campagne.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-08-18
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne à associer
		 *
		 * \return Renvoi les occurrences qui sont associées à une Campagne. Lève une Exception en cas d'erreur.
		 */
		$Request = 'SELECT * FROM dma_dmia_activite AS "dma"
			LEFT JOIN mim_matrice_impacts AS "mim" ON mim.mim_id = dma.mim_id
			LEFT JOIN nim_niveaux_impact AS "nim" ON nim.nim_id = mim.nim_id
			LEFT JOIN tim_types_impact AS "tim" ON tim.tim_id = mim.tim_id
			LEFT JOIN ete_echelle_temps AS "ete" ON ete.ete_id = dma.ete_id
			WHERE dma.cmp_id = :cmp_id AND dma.act_id = :act_id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT ) ;
		$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT ) ;

		$this->executeSQL( $Query );

		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function rechercherPersonnesClesActivites( $sct_id, $act_id = 0 ) {
		/**
		 * Récupère les Personnes Clés.
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-02-19
		 *
		 * \param[in] $sct_id Identifiant de la Société à récupérer
		 * \param[in] $act_id Identifiant de l'Activité à récupérer
		 *
		 * \return Renvoi l'occurrence listant les associations du Site ou FALSE si pas d'entité. Lève une Exception en cas d'erreur.
		 */
		$Request = 'SELECT ppr.*, ppac.act_id AS "associe", ppac.ppac_description
			FROM ppr_parties_prenantes AS "ppr"
			LEFT JOIN (SELECT ppr_id, act_id, ppac_description FROM ppac_ppr_act WHERE act_id = :act_id) AS "ppac" ON ppac.ppr_id = ppr.ppr_id
			WHERE ppr.sct_id = :sct_id
			ORDER BY ppac.act_id, ppr.ppr_nom ';
		
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':sct_id', $sct_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
		
		return $Query->fetchAll( PDO::FETCH_CLASS );
	}
	
	
	public function rechercherPersonnesClesAssociesActivite( $act_id ) {
		/**
		 * Récupère les Personnes Clés associées à une Activité.
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-02-19
		 *
		 * \param[in] $act_id Identifiant de l'Activité à récupérer
		 *
		 * \return Renvoi les occurrences de Personnes Clés ou FALSE si aucune occurrence. Lève une Exception en cas d'erreur.
		 */
		$Request = 'SELECT *
			FROM ppac_ppr_act AS "ppac"
			LEFT JOIN ppr_parties_prenantes AS "ppr" ON ppr.ppr_id = ppac.ppr_id
			WHERE ppac.act_id = :act_id
			ORDER BY ppr.ppr_nom ';
		
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
		
		return $Query->fetchAll( PDO::FETCH_CLASS );
	}
	

	public function ajouterPersonneCleActivite( $cmp_id, $act_id, $ppr_id, $ppac_description ) {
		/**
		 * Associe une Partie Prenante à une Activité (notion de Personnes Clés).
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-02-19
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne à associer
		 * \param[in] $act_id Identifiant de l'Activité à associer
		 * \param[in] $ppr_id Identifiant de la Partie Prenante à associer
		 * \param[in] $ppac_description Description de la Partie Prenante quand elle est associée à cette activité
		 *
		 * \return Renvoi TRUE si l'occurrence est créée. Lève une Exception en cas d'erreur.
		 */
		$Request = 'INSERT INTO ppac_ppr_act AS "ppac" (cmp_id, act_id, ppr_id, ppac_description)
			VALUES (:cmp_id, :act_id, :ppr_id, :ppac_description) ';
		
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ppr_id', $ppr_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ppac_description', $ppac_description, PDO::PARAM_LOB );
		
		$this->executeSQL( $Query );
		
		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
		
		return TRUE;
	}


	public function modifierPersonneCleActivite( $cmp_id, $act_id, $ppr_id, $ppac_description ) {
		/**
		 * Modifie une Partie Prenante à une Activité (notion de Personnes Clés).
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-02-19
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne à associer
		 * \param[in] $act_id Identifiant de l'Activité à associer
		 * \param[in] $ppr_id Identifiant de la Partie Prenante à associer
		 * \param[in] $ppac_description Description de la Partie Prenante quand elle est associée à cette activité
		 *
		 * \return Renvoi TRUE si l'occurrence est créée. Lève une Exception en cas d'erreur.
		 */
		$Request = 'UPDATE ppac_ppr_act SET ppac_description = :ppac_description
			WHERE cmp_id = :cmp_id AND act_id = :act_id AND ppr_id = :ppr_id';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ppr_id', $ppr_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ppac_description', $ppac_description, PDO::PARAM_LOB );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return TRUE;
	}


	public function supprimerPersonneCleActivite( $cmp_id, $act_id, $ppr_id ) {
		/**
		 * Supprime l'association entre une Partie Prenante et une Activité (notion de Personnes Clés).
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-02-19
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne à associer
		 * \param[in] $act_id Identifiant de l'Activité à associer
		 * \param[in] $ppr_id Identifiant de la Partie Prenante à associer
		 *
		 * \return Renvoi TRUE si l'occurrence est créée. Lève une Exception en cas d'erreur.
		 */
		$Request = 'DELETE FROM ppac_ppr_act AS "ppac"
			WHERE cmp_id = :cmp_id AND act_id = :act_id AND ppr_id = :ppr_id ';
		
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ppr_id', $ppr_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
		
		return TRUE;
	}



	public function rechercherApplicationsActivites( $act_id = 0 ) {
		/**
		 * Récupère les Applications.
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-08-21
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne à récupérer
		 * \param[in] $act_id Identifiant de l'Activité à récupérer
		 *
		 * \return Renvoi l'occurrence listant les associations du Site ou FALSE si pas d'entité. Lève une Exception en cas d'erreur.
		 */
		$Request = 'SELECT app.*, acap.act_id AS "associe", acap.ete_id_dima, acap.ete_id_pdma, 
			acap.acap_donnees, acap.acap_palliatif
			FROM app_applications AS "app"
			LEFT JOIN (SELECT app_id, act_id, ete_id_dima, ete_id_pdma, acap_donnees, acap_palliatif FROM acap_act_app WHERE act_id = :act_id) AS "acap" ON acap.app_id = app.app_id
			ORDER BY acap.act_id, app.app_nom ';
		
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
		
		return $Query->fetchAll( PDO::FETCH_CLASS );
	}
	
	
	public function rechercherApplicationsAssocieesActivite( $act_id = 0 ) {
		/**
		 * Récupère les Applications associées à l'Activité.
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-08-21
		 *
		 * \param[in] $act_id Identifiant de l'Activité à récupérer
		 *
		 * \return Renvoi l'occurrence listant les associations du Site ou FALSE si pas d'entité. Lève une Exception en cas d'erreur.
		 */
		$Request = 'SELECT *, ete1.ete_nom_code AS "ete_id_dima", ete2.ete_nom_code AS "ete_id_pdma"
			FROM acap_act_app AS "acap"
			LEFT JOIN app_applications AS "app" ON app.app_id = acap.app_id
			LEFT JOIN ete_echelle_temps AS "ete1" ON ete1.ete_id = acap.ete_id_dima
			LEFT JOIN ete_echelle_temps AS "ete2" ON ete2.ete_id = acap.ete_id_pdma
			WHERE acap.act_id = :act_id
			ORDER BY app.app_nom ';
		
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
		
		return $Query->fetchAll( PDO::FETCH_CLASS );
	}
	


	public function ajouterApplicationActivite( $act_id, $app_id, $ete_id_dima, $ete_id_pdma,
		$acap_donnees="", $acap_palliatif="" ) {
		/**
		 * Associe une Application à une Activité.
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-08-21
		 *
		 * \param[in] $act_id Identifiant de l'Activité à associer
		 * \param[in] $app_id Identifiant de l'Application à associer
		 * \param[in] $ete_id_dima Identifiant de l'échelle de temps pour le DMIA de l'Application
		 * \param[in] $ete_id_pdma Identifiant de l'échelle de temps pour le PDMA de l'Application
		 * \param[in] $acap_donnees Données associées à cette Application
		 * \param[in] $acap_palliatif Commentaire sur le palliatif
		 *
		 * \return Renvoi TRUE si l'occurrence est créée. Lève une Exception en cas d'erreur.
		 */
		$Request = 'INSERT INTO acap_act_app AS "acap"
			(act_id, app_id, ete_id_dima, ete_id_pdma, acap_donnees, acap_palliatif)
			VALUES (:act_id, :app_id, :ete_id_dima, :ete_id_pdma, :acap_donnees, :acap_palliatif) ';


		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':app_id', $app_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ete_id_dima', $ete_id_dima, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ete_id_pdma', $ete_id_pdma, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':acap_donnees', $acap_donnees, PDO::PARAM_LOB );
		$this->bindSQL( $Query, ':acap_palliatif', $acap_palliatif, PDO::PARAM_LOB );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return TRUE;
	}



	public function modifierApplicationActivite( $act_id, $app_id, $ete_id_dima, $ete_id_pdma,
		$acap_donnees="", $acap_palliatif="" ) {
		/**
		 * Modifie une Application à une Activité.
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-08-21
		 *
		 * \param[in] $act_id Identifiant de l'Activité à associer
		 * \param[in] $app_id Identifiant de l'Application à associer
		 * \param[in] $ete_id_dima Identifiant de l'échelle de temps pour le DMIA de l'Application
		 * \param[in] $ete_id_pdma Identifiant de l'échelle de temps pour le PDMA de l'Application
		 * \param[in] $acap_donnees Description des Données associées à l'Application
		 * \param[in] $acap_palliatif Commentaire sur le palliatif
		 *
		 * \return Renvoi TRUE si l'occurrence est créée. Lève une Exception en cas d'erreur.
		 */
		$Request = 'UPDATE acap_act_app SET 
			ete_id_dima = :ete_id_dima,
			ete_id_pdma = :ete_id_pdma,
			acap_donnees = :acap_donnees,
			acap_palliatif = :acap_palliatif
			WHERE act_id = :act_id AND app_id = :app_id ';


		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':app_id', $app_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ete_id_dima', $ete_id_dima, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ete_id_pdma', $ete_id_pdma, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':acap_donnees', $acap_donnees, PDO::PARAM_LOB );
		$this->bindSQL( $Query, ':acap_palliatif', $acap_palliatif, PDO::PARAM_LOB );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return TRUE;
	}


	public function supprimerApplicationActivite( $act_id, $app_id ) {
		/**
		 * Supprime l'association entre une Application et une Activité.
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-08-21
		 *
		 * \param[in] $act_id Identifiant de l'Activité à dissocier
		 * \param[in] $app_id Identifiant de l'Application à dissocier
		 *
		 * \return Renvoi TRUE si l'occurrence est supprimée. Lève une Exception en cas d'erreur.
		 */
		$Request = 'DELETE FROM acap_act_app AS "acap"
			WHERE act_id = :act_id AND app_id = :app_id ';
		
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':app_id', $app_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
		
		return TRUE;
	}
	
	
	public function rechercherEntitesCampagne( $cmp_id ) {
		/**
		 * Rechercher les Entités associées à une Campagne.
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-08-30
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne à utiliser pour la recherche
		 *
		 * \return Renvoi TRUE si l'occurrence est supprimée. Lève une Exception en cas d'erreur.
		 */

		if ( $_SESSION['idn_super_admin'] === TRUE ) {
			$Request = 'SELECT ent.*
				FROM cmen_cmp_ent AS "cmen"
				LEFT JOIN ent_entites AS "ent" ON ent.ent_id = cmen.ent_id
				WHERE cmen.cmp_id = :cmp_id
				ORDER BY ent_nom ';
		} else {
			$Request = 'SELECT ent.*
				FROM iden_idn_ent AS "iden"
				LEFT JOIN cmen_cmp_ent AS "cmen" ON cmen.ent_id = iden.ent_id
				LEFT JOIN ent_entites AS "ent" ON ent.ent_id = cmen.ent_id
				WHERE iden.idn_id = :idn_id AND cmen.cmp_id = :cmp_id
				ORDER BY ent_nom ';
		}

		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );

		if ( $_SESSION['idn_super_admin'] === FALSE ) {
			$this->bindSQL( $Query, ':idn_id', $_SESSION['idn_id'], PDO::PARAM_INT );
		}

		$this->executeSQL( $Query );
		
		return $Query->fetchAll( PDO::FETCH_CLASS );
	}



	public function rechercherFournisseursActivite( $act_id = 0 ) {
		/**
		 * Récupère les Fournisseurs associés à une Activité.
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-09-02
		 *
		 * \param[in] $act_id Identifiant de l'Activité à récupérer
		 *
		 * \return Renvoi l'occurrence listant les associations du Site ou FALSE si pas d'entité. Lève une Exception en cas d'erreur.
		 */

		$Request = 'SELECT frn.*, acfr.act_id AS "associe", acfr.ete_id, acfr.acfr_consequence_indisponibilite,
			acfr.acfr_palliatif_tiers
			FROM frn_fournisseurs AS "frn"
			LEFT JOIN (SELECT act_id, frn_id, ete_id, acfr_consequence_indisponibilite, acfr_palliatif_tiers FROM acfr_act_frn WHERE act_id = :act_id) AS "acfr" ON acfr.frn_id = frn.frn_id
			ORDER BY acfr.act_id, frn.frn_nom ';
		
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
		
		return $Query->fetchAll( PDO::FETCH_CLASS );
	}
	
	
	public function rechercherFournisseursAssociesActivite( $act_id = 0 ) {
		/**
		 * Récupère les Fournisseurs associés à une Activité.
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-09-02
		 *
		 * \param[in] $act_id Identifiant de l'Activité à récupérer
		 *
		 * \return Renvoi l'occurrence listant les associations du Site ou FALSE si pas d'entité. Lève une Exception en cas d'erreur.
		 */
		
		$Request = 'SELECT *
			FROM acfr_act_frn AS "acfr"
			LEFT JOIN frn_fournisseurs AS "frn" ON frn.frn_id = acfr.frn_id
			LEFT JOIN ete_echelle_temps AS "ete" ON ete.ete_id = acfr.ete_id
			LEFT JOIN tfr_types_fournisseur AS "tfr" ON tfr.tfr_id = frn.tfr_id
			WHERE acfr.act_id = :act_id
			ORDER BY frn.frn_nom ';
		
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
		
		return $Query->fetchAll( PDO::FETCH_CLASS );
	}
	
	
	
	public function ajouterFournisseurActivite( $act_id, $frn_id, $ete_id, $acfr_consequence_indisponibilite="",
		$acfr_palliatif_tiers="" ) {
			/**
			 * Associe un Fournisseur à une Activité.
			 *
			 * \license Copyleft Loxense
			 * \author Pierre-Luc MARY
			 * \date 2024-09-02
			 *
			 * \param[in] $act_id Identifiant de l'Activité à associer
			 * \param[in] $frn_id Identifiant du Fournisseur à associer
			 * \param[in] $ete_id Identifiant de l'échelle de temps pour la DMIA du Fournisseur
			 * \param[in] $acfr_consequence_indisponibilite Description de la conséquence de l'indisponibilité
			 * \param[in] $acfr_palliatif_tiers Description du palliatif possible pour ce tiers
			 *
			 * \return Renvoi TRUE si l'occurrence est créée. Lève une Exception en cas d'erreur.
			 */
			$Request = 'INSERT INTO acfr_act_frn
				(act_id, frn_id, ete_id, acfr_consequence_indisponibilite, acfr_palliatif_tiers)
				VALUES (:act_id, :frn_id, :ete_id, :acfr_consequence_indisponibilite, :acfr_palliatif_tiers) ';
			
			
			$Query = $this->prepareSQL( $Request );
			
			$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT );
			$this->bindSQL( $Query, ':frn_id', $frn_id, PDO::PARAM_INT );
			$this->bindSQL( $Query, ':ete_id', $ete_id, PDO::PARAM_INT );
			$this->bindSQL( $Query, ':acfr_consequence_indisponibilite', $acfr_consequence_indisponibilite, PDO::PARAM_LOB );
			$this->bindSQL( $Query, ':acfr_palliatif_tiers', $acfr_palliatif_tiers, PDO::PARAM_LOB );
			
			$this->executeSQL( $Query );
			
			if ( $this->RowCount == 0 ) {
				return FALSE;
			}
			
			return TRUE;
	}
	
	
	
	public function modifierFournisseurActivite( $act_id, $frn_id, $ete_id, $acfr_consequence_indisponibilite="",
		$acfr_palliatif_tiers="" ) {
			/**
			 * Modifie l'association entre un Fournisseur et une Activité.
			 *
			 * \license Copyleft Loxense
			 * \author Pierre-Luc MARY
			 * \date 2024-09-02
			 *
			 * \param[in] $act_id Identifiant de l'Activité à modifier
			 * \param[in] $frn_id Identifiant du Fournisseur à modifier
			 * \param[in] $ete_id Identifiant de l'échelle de temps pour la DMIA du Fournisseur
			 * \param[in] $acfr_consequence_indisponibilite Description de la conséquence de l'indisponibilité
			 * \param[in] $acfr_palliatif_tiers Description du palliatif possible pour ce tiers
			 *
			 * \return Renvoi TRUE si l'occurrence est créée. Lève une Exception en cas d'erreur.
			 */
			$Request = 'UPDATE acfr_act_frn SET
				ete_id = :ete_id,
				acfr_consequence_indisponibilite = :acfr_consequence_indisponibilite,
				acfr_palliatif_tiers = :acfr_palliatif_tiers
				WHERE act_id = :act_id AND frn_id = :frn_id ';
			
			
			$Query = $this->prepareSQL( $Request );
			
			$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT );
			$this->bindSQL( $Query, ':frn_id', $frn_id, PDO::PARAM_INT );
			$this->bindSQL( $Query, ':ete_id', $ete_id, PDO::PARAM_INT );
			$this->bindSQL( $Query, ':acfr_consequence_indisponibilite', $acfr_consequence_indisponibilite, PDO::PARAM_LOB );
			$this->bindSQL( $Query, ':acfr_palliatif_tiers', $acfr_palliatif_tiers, PDO::PARAM_LOB );
			
			$this->executeSQL( $Query );
			
			if ( $this->RowCount == 0 ) {
				return FALSE;
			}
			
			return TRUE;
	}
	
	
	public function supprimerFournisseurActivite( $act_id, $frn_id ) {
		/**
		 * Supprime l'association entre un Fournisseur et une Activité.
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-08-21
		 *
		 * \param[in] $act_id Identifiant de l'Activité à dissocier
		 * \param[in] $app_id Identifiant du Fournisseur à dissocier
		 *
		 * \return Renvoi TRUE si l'occurrence est supprimée. Lève une Exception en cas d'erreur.
		 */
		$Request = 'DELETE FROM acfr_act_frn
				WHERE act_id = :act_id AND frn_id = :frn_id ';
		
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':frn_id', $frn_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
		
		return TRUE;
	}


	public function rechercherSyntheseActivites( $cmp_id, $ent_id, $nim_poids = 3, $Regroupement='nim_poids' ) {
		/**
		 * Lister les Activités en fonction de leur poids ( par défaut poids impact = 3).
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-03-05
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne de rattachement
		 * \param[in] $ent_id Identifiant de l'Entité de rattachement (si "*", alors afficher toutes les entités)
		 *
		 * \return Renvoi une liste des Activités ou une liste vide. Lève une erreur en cas d'incident
		 */

		$Request = 'SELECT
act.act_id, act_nom, act_teletravail, min_max.nim_poids, max_dmia.ete_poids, ent_nom, ent_description
FROM act_activites AS "act"
LEFT JOIN ent_entites AS "ent" ON ent.ent_id = act.ent_id
LEFT JOIN dma_dmia_activite AS "dma" ON dma.act_id = act.act_id
LEFT JOIN ete_echelle_temps AS "ete" ON ete.ete_id = dma.ete_id
LEFT JOIN mim_matrice_impacts AS "mim" ON mim.mim_id = dma.mim_id
LEFT JOIN
	(SELECT act_id, max(nim.nim_poids) AS "nim_poids" 
	FROM dma_dmia_activite AS "dma" 
	LEFT JOIN mim_matrice_impacts AS "mim" ON mim.mim_id = dma.mim_id 
	LEFT JOIN nim_niveaux_impact AS "nim" ON nim.nim_id = mim.nim_id 
	GROUP BY act_id) AS "min_max" ON act.act_id = min_max.act_id
LEFT JOIN
	(SELECT act_id, min(ete.ete_poids) AS "ete_poids"
	FROM dma_dmia_activite AS "dma2"
	LEFT JOIN mim_matrice_impacts AS "mim" ON mim.mim_id = dma2.mim_id
	RIGHT JOIN nim_niveaux_impact AS "nim" ON nim.nim_id = mim.nim_id AND nim.nim_poids > 2
	LEFT JOIN ete_echelle_temps AS "ete" ON ete.ete_id = dma2.ete_id
	GROUP BY act_id) AS "max_dmia" ON max_dmia.act_id = act.act_id 
	WHERE act.cmp_id = :cmp_id ';
		
		if ($Regroupement == 'nim_poids' ) {
			$Request .= 'AND min_max.nim_poids = :nim_poids ';
		}

		if ( $ent_id != '*') {
			$Request .= 'AND act.ent_id = :ent_id ';
		}

		$Request .= 'GROUP BY act.act_id, act_nom, act_description, act_teletravail, min_max.nim_poids,
max_dmia.ete_poids, ent_nom, ent_description ';

		if ($Regroupement == 'nim_poids' ) {
			$Request .= 'ORDER BY min_max.nim_poids DESC, max_dmia.ete_poids, act_nom, ent_nom ';
		} else {
			$Request .= 'ORDER BY max_dmia.ete_poids, min_max.nim_poids DESC, act_nom, ent_nom ';
		}

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );

		if ($Regroupement == 'nim_poids' ) {
			$this->bindSQL( $Query, ':nim_poids', $nim_poids, PDO::PARAM_INT );
		}

		if ( $ent_id != '*') {
			$this->bindSQL( $Query, ':ent_id', $ent_id, PDO::PARAM_INT );
		}

		$this->executeSQL( $Query );

		return $Query->fetchAll( PDO::FETCH_CLASS );
	}
}

?>