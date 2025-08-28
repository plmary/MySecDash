<?php

include_once( 'Constants.inc.php' );

include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Connecteur_BD_PDO.inc.php' );


// Centralisation de la taille des objets en base.
define( 'L_CMP_DATE', 10); // AAAA-MM-DD
define( 'L_CMP_DATE_VALIDATION', 19); // AAAA-MM-DD 17:44:21

class Campagnes extends HBL_Connexioneur_BD {
/**
* Cette classe gère les Campagnes de BIA.
*
* \license Copyleft Loxense
* \author Pierre-Luc MARY
* \date 2015-05-13
*/
	public function __construct() {
	/**
	* Connexion à la base de données en appelant le constructeur du Parent.
	*
	* \license Copyleft Loxense
	* \author Pierre-Luc MARY
	* \date 2024-01-14
	*
	* \return Renvoi un booléen sur le succès de la connexion à la base de données
	*/
		parent::__construct();
		
		return true;
	}


	/* ===============================================================================
	** Gestion des Campagnes
	*/
	
	public function majCampagne( $sct_id, $cmp_id='', $cmp_date='', $flag_validation=FALSE ) {
	/**
	* Créé ou actualise une Campagne.
	*
	* \license Copyleft Loxense
	* \author Pierre-Luc MARY
	* \date 2024-01-14
	*
	* \param[in] $sct_id Identifiant de la Société de rattachement de l'Entité
	* \param[in] $cmp_id Identifiant de la Campagne à modifier (si précisé)
	* \param[in] $cmp_date Date de réalisation de la Campagne
	*
	* \return Renvoi TRUE si la Campagne a été créée ou modifiée, FALSE si l'entité n'existe pas ou lève une exception en cas d'erreur.
	*/
		if ( $cmp_id == '' ) {
			$Request = 'INSERT INTO cmp_campagnes
				( sct_id, cmp_date, idn_id, cmp_flag_validation, cmp_date_validation )
				VALUES ( :sct_id, :cmp_date, :idn_id, :cmp_flag_validation, :cmp_date_validation ) ';

			$Query = $this->prepareSQL( $Request );

			if ( $flag_validation == TRUE ) {
				$this->bindSQL( $Query, ':cmp_flag_validation', TRUE, PDO::PARAM_BOOL );
				$this->bindSQL( $Query, ':cmp_date_validation', date('Y-m-d H:i:s'), PDO::PARAM_STR, L_CMP_DATE_VALIDATION );
			} else {
				$this->bindSQL( $Query, ':cmp_flag_validation', FALSE, PDO::PARAM_BOOL );
				$this->bindSQL( $Query, ':cmp_date_validation', NULL, PDO::PARAM_NULL );
			}
		
			$this->bindSQL( $Query, ':idn_id', $_SESSION['idn_id'], PDO::PARAM_INT );
		} else {
			$Query = $this->prepareSQL(
				'UPDATE cmp_campagnes SET
				sct_id  = :sct_id,
				cmp_date = :cmp_date,
				cmp_flag_validation = :cmp_flag_validation,
				cmp_date_validation = :cmp_date_validation
				WHERE cmp_id = :cmp_id'
				);

			if ( $flag_validation == TRUE ) {
				$this->bindSQL( $Query, ':cmp_flag_validation', TRUE, PDO::PARAM_BOOL );
				$this->bindSQL( $Query, ':cmp_date_validation', date('Y-m-d H:i:s'), PDO::PARAM_STR, L_CMP_DATE_VALIDATION );
			} else {
				$this->bindSQL( $Query, ':cmp_flag_validation', FALSE, PDO::PARAM_BOOL );
				$this->bindSQL( $Query, ':cmp_date_validation', NULL, PDO::PARAM_NULL );
			}

			$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		}

		$this->bindSQL( $Query, ':sct_id', $sct_id, PDO::PARAM_INT );

		if ( $cmp_date == '' ) {
			$cmp_date = date('Y-m-d');
		}
		$this->bindSQL( $Query, ':cmp_date', $cmp_date, PDO::PARAM_STR, L_CMP_DATE );
		
		$this->executeSQL( $Query );

		if ( $cmp_id == '' ) {
			switch( $this->getAttribute(PDO::ATTR_DRIVER_NAME) ) {
			 case 'pgsql';
				$this->LastInsertId = $this->lastInsertId( 'cmp_campagnes_cmp_id_seq' );
				break;
			}
		}
	
		
		return TRUE;
	}


	public function majCampagneParChamp( $sct_id, $Id, $Champ, $Valeur ) {
		/**
		 * Actualise le Champ d'une Campagne.
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-03-19
		 *
		 * \param[in] $sct_id Identifiant de la Société de rattachement de la Campagne
		 * \param[in] $Id Identifiant de la Campagne à modifier
		 * \param[in] $Champ Nom du Champ de la table Campagne à modifier
		 * \param[in] $Valeur Valeur du Champ à stocker 
		 *
		 * \return Renvoi TRUE si la Campagne a été créée ou modifiée, FALSE si l'entité n'existe pas ou lève une exception en cas d'erreur.
		 */
		if ( $Id == '' or $Champ == '' or $Valeur == '' ) return FALSE;

		$Request = 'UPDATE cmp_campagnes SET ';

		switch ( $Champ ) {
			case 'cmp_date':
				$Request .= $Champ . ' = :Valeur ';
				break;

			case 'cmp_flag_validation':
				$Request .= 'cmp_flag_validation = :cmp_flag_validation, cmp_date_validation = :cmp_date_validation ';
				break;

			default:
				return FALSE;
		}

		$Request .= 'WHERE cmp_id = :cmp_id';


		$Query = $this->prepareSQL( $Request );


		$this->bindSQL( $Query, ':cmp_id', $Id, PDO::PARAM_INT );


		switch ( $Champ ) {
			case 'cmp_date':
				if ( $Valeur == '' ) {
					$Valeur = date('Y-m-d');
				}
				$this->bindSQL( $Query, ':Valeur', $Valeur, PDO::PARAM_STR, L_CMP_DATE );

				break;

			case 'cmp_flag_validation':
				if ( $Valeur == TRUE ) {
					$this->bindSQL( $Query, ':cmp_flag_validation', TRUE, PDO::PARAM_BOOL );
					$this->bindSQL( $Query, ':cmp_date_validation', date('Y-m-d H:i:s'), PDO::PARAM_STR, L_CMP_DATE_VALIDATION );
				} else {
					$this->bindSQL( $Query, ':cmp_flag_validation', FALSE, PDO::PARAM_BOOL );
					$this->bindSQL( $Query, ':cmp_date_validation', NULL, PDO::PARAM_NULL );
				}

				break;

			default:
				return FALSE;
		}

		$this->executeSQL( $Query );

		return TRUE;
	}


	public function rechercherCampagnes( $sct_id, $orderBy = 'cmp_date', $cmp_id = '' ) {
	/**
	* Lister les Campagnes.
	*
	* \license Copyleft Loxense
	* \author Pierre-Luc MARY
	* \date 2024-01-14
	*
	* \param[in] $sct_id ID de la Société pour lesquelles on recherche les Entités.
	* \param[in] $orderBy Permet de gérer l'ordre d'affichage.
	* \param[in] $cmp_id Permet de rechercher une Campagne Spécifique
	*
	* \return Renvoi un tableau d'objet ou un tableau vide si pas de données trouvées. Lève une exception en cas d'erreur.
	*/

		$Where = '';

		if ( $_SESSION['idn_super_admin'] === TRUE ) {
			$Request = 'SELECT 
				cmp.cmp_id, cmp.idn_id, cmp_date, cmp_flag_validation, cmp_date_validation,
				count(DISTINCT cmen.ent_id) AS "total_ent",
				count(DISTINCT cmst.sts_id) AS "total_sts",
				count(DISTINCT mim.mim_id) AS "total_mim",
				count(DISTINCT ete_id) AS "total_ete",
				count(DISTINCT tim.tim_id) AS "total_tim",
				count(DISTINCT nim.nim_id) AS "total_nim"
				FROM cmp_campagnes AS "cmp"
				LEFT JOIN tim_types_impact AS "tim" ON tim.cmp_id = cmp.cmp_id
				LEFT JOIN nim_niveaux_impact AS "nim" ON nim.cmp_id = cmp.cmp_id
				LEFT JOIN mim_matrice_impacts AS "mim" ON mim.cmp_id = cmp.cmp_id
				LEFT JOIN ete_echelle_temps AS "ete" ON ete.cmp_id = cmp.cmp_id
				LEFT JOIN cmen_cmp_ent AS "cmen" ON cmen.cmp_id = cmp.cmp_id
				LEFT JOIN cmst_cmp_sts AS "cmst" ON cmst.cmp_id = cmp.cmp_id ';
		} else {
			$Request = 'SELECT
				cmp.cmp_id, cmp.idn_id, cmp_date, cmp_flag_validation, cmp_date_validation,
				count(DISTINCT cmen.ent_id) AS "total_ent",
				count(DISTINCT cmst.sts_id) AS "total_sts",
				count(DISTINCT mim.mim_id) AS "total_mim",
				count(DISTINCT ete_id) AS "total_ete",
				count(DISTINCT tim.tim_id) AS "total_tim",
				count(DISTINCT nim.nim_id) AS "total_nim"
				FROM idsc_idn_sct AS "idsc"
				LEFT JOIN cmp_campagnes AS "cmp" ON cmp.sct_id = idsc.sct_id
				LEFT JOIN tim_types_impact AS "tim" ON tim.cmp_id = cmp.cmp_id
				LEFT JOIN nim_niveaux_impact AS "nim" ON nim.cmp_id = cmp.cmp_id
				LEFT JOIN mim_matrice_impacts AS "mim" ON mim.cmp_id = cmp.cmp_id
				LEFT JOIN ete_echelle_temps AS "ete" ON ete.cmp_id = cmp.cmp_id
				LEFT JOIN cmen_cmp_ent AS "cmen" ON cmen.cmp_id = cmp.cmp_id
				LEFT JOIN cmst_cmp_sts AS "cmst" ON cmst.cmp_id = cmp.cmp_id ';

			$Where .= 'WHERE idsc.idn_id = ' . $_SESSION['idn_id'] . ' ';
		}

		if ($sct_id != '*') {
			if ($Where == '') {
				$Where .= 'WHERE ';
			} else {
				$Where .= 'AND ';
			}
			
			$Where .= 'cmp.sct_id = :sct_id ';
		}

		if ($cmp_id != '') {
			if ($Where == '') {
				$Where .= 'WHERE ';
			} else {
				$Where .= 'AND ';
			}

			$Where .= 'cmp.cmp_id = :cmp_id ';
		}

		$Request .= $Where . 'GROUP BY cmp.cmp_id, cmp_date ';

		switch( $orderBy ) {
		 default:
		 case 'cmp_date':
		 	$Request .= 'ORDER BY cmp_date ';
			break;

		 case 'cmp_date-desc':
		 	$Request .= 'ORDER BY cmp_date DESC ';
			break;
		}

		//print('<hr>'.$Request.'<hr>sct_id='.$sct_id.'<hr>');
		$Query = $this->prepareSQL( $Request );

		if ($sct_id != '*') {
			$this->bindSQL( $Query, ':sct_id', $sct_id, PDO::PARAM_INT );
		}

		if ($cmp_id != '' ) {
			$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		}

		$this->executeSQL( $Query );

		$Occurrences = $Query->fetchAll( PDO::FETCH_CLASS );
		$Nouvelles_Occurrences = [];

		foreach( $Occurrences as $Occurrence) {
			$Occurrence->total_act = $this->compterActivitesCampagne( $Occurrence->cmp_id );

			$Nouvelles_Occurrences[] = $Occurrence;
		}

		return $Nouvelles_Occurrences;
	}


	public function compterActivitesCampagne( $cmp_id ) {
		/**
		 * Récupère le nombre total d'Activités créés dans cette Campagne.
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-10-23
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne à récupérer
		 *
		 * \return Renvoi le nombre total d'Activité sur une Campagne. Lève une Exception en cas d'erreur.
		 */
		$Request = 'SELECT count(DISTINCT act_id) AS "total_act"
FROM act_activites AS "act"
WHERE act.cmp_id = :cmp_id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		return $Query->fetchObject()->total_act;
	}


	public function detaillerCampagne( $cmp_id ) {
	/**
	* Récupère les informations d'une Campagne.
	*
	* \license Copyleft Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-15
	*
	* \param[in] $cmp_id Identifiant de la Campagne à récupérer
	*
	* \return Renvoi l'occurrence d'une Campagne ou FALSE si pas d'occurrence. Lève une Exception en cas d'erreur.
	*/
		$Request = 'SELECT ' .
		'* ' .
		'FROM cmp_campagnes AS "cmp" ' .
		'LEFT JOIN sct_societes AS "sct" ON sct.sct_id = cmp.sct_id ' .
		'WHERE cmp_id = :cmp_id ' ;
		
		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return $Query->fetchObject();
	}


	public function rechercherNiveauxImpactCampagne( $cmp_id ) {
		/**
		 * Lister les Niveaux d'Appréciation déclarés sur une Campagne
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-01-15
		 *
		 * \param[in] $cmp_id ID de la Campagne qui pourrait être associé
		 *
		 * \return Renvoi un tableau d'objet ou un tableau vide si pas de données trouvées. Lève une exception en cas d'erreur.
		 */
		$Request = 'SELECT nim.*, lbr_code AS "nim_nom"
		FROM nim_niveaux_impact AS "nim"
		LEFT JOIN cmp_campagnes AS "cmp" ON cmp.cmp_id = nim.cmp_id 
		LEFT JOIN lbr_libelles_referentiel AS "lbr" ON lbr_code = nim_nom_code AND lng_id = \'' . $_SESSION['Language'] . '\' 
		WHERE nim.cmp_id = :cmp_id ';
		
		$Request .= 'ORDER BY nim_poids, nim_nom ';
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		return $Query->fetchAll( PDO::FETCH_CLASS );
	}
	
	
	public function rechercherTypesImpactCampagne( $cmp_id ) {
		/**
		 * Lister les Types d'Impact déclarés sur une Campagne
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-01-15
		 *
		 * \param[in] $cmp_id ID de la Campagne qui pourrait être associé
		 *
		 * \return Renvoi un tableau d'objet ou un tableau vide si pas de données trouvées. Lève une exception en cas d'erreur.
		 */
		$Request = 'SELECT tim.*, lbr_code AS "tim_nom"
		FROM tim_types_impact AS "tim"
		LEFT JOIN cmp_campagnes AS "cmp" ON cmp.cmp_id = tim.cmp_id
		LEFT JOIN lbr_libelles_referentiel AS "lbr" ON lbr_code = tim_nom_code AND lng_id = \'' . $_SESSION['Language'] . '\'
		WHERE tim.cmp_id = :cmp_id ';
		
		$Request .= 'ORDER BY tim_poids, tim_nom ';
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		return $Query->fetchAll( PDO::FETCH_CLASS );
	}
	
	
	public function rechercherMatriceImpactsCampagne( $cmp_id ) {
		/**
		 * Lister la Matrice des Impacts déclarée sur une Campagne
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-01-15
		 *
		 * \param[in] $cmp_id ID de la Campagne qui pourrait être associé
		 *
		 * \return Renvoi un tableau d'objet ou un tableau vide si pas de données trouvées. Lève une exception en cas d'erreur.
		 */
		$Request = 'SELECT mim.*, micm.cmp_id AS "associe"
		FROM mim_matrice_impacts AS "mim"
		LEFT JOIN (SELECT mim_id, cmp_id FROM micm_mim_cmp WHERE cmp_id = :cmp_id) AS "micm" ON micm.mim_id = mim.mim_id
		LEFT JOIN cmp_campagnes AS "cmp" ON cmp.cmp_id = micm.cmp_id ';
		
		$Request .= 'ORDER BY mim_description ';
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function rechercherEntitesAssocieesCampagne( $sct_id, $cmp_id, $ent_id = '*' ) {
		/**
		 * Lister les Entites déclarées sur une Campagne
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-01-15
		 *
		 * \param[in] $sct_id ID de la Société de rattachement
		 * \param[in] $cmp_id ID de la Campagne qui pourrait être associé
		 *
		 * \return Renvoi un tableau d'objet ou un tableau vide si pas de données trouvées. Lève une exception en cas d'erreur.
		 */

		if ( $_SESSION['idn_super_admin'] == TRUE ) {
			$Request = 'SELECT ent.*, cmen.cmp_id AS "associe", ppr_id_cpca, cmen_date_entretien_cpca, cmen_effectif_total, ppr.*
				FROM ent_entites AS "ent"
				LEFT JOIN (SELECT ent_id, cmp_id, ppr_id_cpca, cmen_date_entretien_cpca, cmen_effectif_total FROM cmen_cmp_ent WHERE cmp_id = :cmp_id) AS "cmen" ON cmen.ent_id = ent.ent_id
				LEFT JOIN cmp_campagnes AS "cmp" ON cmp.cmp_id = cmen.cmp_id
				LEFT JOIN ppr_parties_prenantes AS "ppr" ON ppr.ppr_id = cmen.ppr_id_cpca ';
			if ( $ent_id == '*' ) {
				$Request .= 'WHERE ent.sct_id = :sct_id ';
			} else {
				$Request .= 'WHERE ent.sct_id = :sct_id AND ent.ent_id = :ent_id ';
			}
			$Request .= 'ORDER BY cmen.cmp_id, ent_nom ';
		} else {
			$Request = 'SELECT ent.*, cmen.cmp_id AS "associe", ppr_id_cpca, cmen_date_entretien_cpca, cmen_effectif_total, ppr.*
				FROM iden_idn_ent AS "iden"
				LEFT JOIN ent_entites AS "ent" ON ent.ent_id = iden.ent_id
				LEFT JOIN (SELECT ent_id, cmp_id, ppr_id_cpca, cmen_date_entretien_cpca, cmen_effectif_total FROM cmen_cmp_ent WHERE cmp_id = :cmp_id) AS "cmen" ON cmen.ent_id = ent.ent_id
				LEFT JOIN cmp_campagnes AS "cmp" ON cmp.cmp_id = cmen.cmp_id
				LEFT JOIN ppr_parties_prenantes AS "ppr" ON ppr.ppr_id = cmen.ppr_id_cpca ';
			if ( $ent_id == '*' ) {
				$Request .= 'WHERE ent.sct_id = :sct_id AND iden.idn_id = :idn_id ';
			} else {
				$Request .= 'WHERE ent.sct_id = :sct_id AND iden.idn_id = :idn_id AND iden.ent_id = :ent_id ';
			}
			$Request .= 'ORDER BY cmen.cmp_id, ent_nom ';
		}

		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':sct_id', $sct_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );

		if ( $ent_id != '*' ) {
			$this->bindSQL( $Query, ':ent_id', $ent_id, PDO::PARAM_INT );
		}

		if ( $_SESSION['idn_super_admin'] == FALSE ) {
			$this->bindSQL( $Query, ':idn_id', $_SESSION['idn_id'], PDO::PARAM_INT );
		}

		$this->executeSQL( $Query );
		
		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function rechercherEntitesCampagne( $cmp_id, $ent_id = '*', $trier = 'ent_nom' ) {
		/**
		 * Lister les Entites déclarées sur une Campagne
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-01-15
		 *
		 * \param[in] $cmp_id ID de la Campagne qui pourrait être associé
		 * \param[in] $ent_id ID de l'Entité spécifique à rechercher (par défaut recherche toutes les Entités rattachées à une Campagne
		 *
		 * \return Renvoi un tableau d'objet ou un tableau vide si pas de données trouvées. Lève une exception en cas d'erreur.
		 */
		
		$Where = '';
		
		if ( $ent_id != '' && $ent_id != '*' ) $Where = 'AND cmen.ent_id = :ent_id ';
		
		$Request = 'SELECT *
			FROM cmen_cmp_ent AS "cmen"
			LEFT JOIN ent_entites AS "ent" ON ent.ent_id = cmen.ent_id
			LEFT JOIN cmp_campagnes AS "cmp" ON cmp.cmp_id = cmen.cmp_id
			LEFT JOIN ppr_parties_prenantes AS "ppr" ON ppr.ppr_id = cmen.ppr_id_validation
			WHERE cmen.cmp_id = :cmp_id ' . $Where;


		switch( $trier ) {
			default:
			case 'ent_nom':
				$Order_By = 'ent_nom';
				break;
			case 'ent_nom-desc':
				$Order_By = 'ent_nom DESC';
				break;

			case 'ent_description':
				$Order_By = 'ent_description';
				break;
			case 'ent_description-desc':
				$Order_By = 'ent_description DESC';
				break;

			case 'cmen_date_validation':
				$Order_By = 'cmen_date_validation, ent_nom';
				break;
			case 'cmen_date_validation-desc':
				$Order_By = 'cmen_date_validation DESC, ent_nom';
				break;

			case 'ppr_id_validation':
				$Order_By = 'cvl_nom, cvl_prenom';
				break;
			case 'ppr_id_validation-desc':
				$Order_By = 'cvl_nom DESC, cvl_prenom';
				break;
		}

		$Request = $Request . ' ORDER BY ' . $Order_By . ' ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );

		if ( $ent_id != '' && $ent_id != '*' ) $this->bindSQL( $Query, ':ent_id', $ent_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function rechercherSitesCampagne( $sct_id, $cmp_id ) {
		/**
		 * Lister les Sites déclarées sur une Campagne
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-08-07
		 *
		 * \param[in] $sct_id ID de la Société de rattachement
		 * \param[in] $cmp_id ID de la Campagne qui pourrait être associé
		 *
		 * \return Renvoi un tableau d'objet ou un tableau vide si pas de données trouvées. Lève une exception en cas d'erreur.
		 */
		$Request = 'SELECT sts.*, cmst.cmp_id AS "associe"
			FROM sts_sites AS "sts"
			LEFT JOIN (SELECT sts_id, cmp_id FROM cmst_cmp_sts WHERE cmp_id = :cmp_id) AS "cmst" ON cmst.sts_id = sts.sts_id
			--LEFT JOIN cmp_campagnes AS "cmp" ON cmp.cmp_id = cmen.cmp_id
			WHERE sts.sct_id = :sct_id
			ORDER BY cmst.cmp_id, sts_nom ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':sct_id', $sct_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function rechercherEchelleTempsCampagne( $cmp_id = '' ) {
		/**
		 * Lister l'Echelle de Temps déclarée sur une Campagne
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-01-15
		 *
		 * \param[in] $cmp_id ID de la Campagne qui pourrait être associé
		 *
		 * \return Renvoi un tableau d'objet ou un tableau vide si pas de données trouvées. Lève une exception en cas d'erreur.
		 */
		
		if ( $cmp_id == '' ) return '';
		
		$Request = 'SELECT ete.*, lbr_libelle AS "ete_nom"
		FROM ete_echelle_temps AS "ete"
		LEFT JOIN lbr_libelles_referentiel AS "lbr" ON lbr.lbr_code = ete.ete_nom_code AND lng_id = \'' . $_SESSION['Language'] . '\' 
		WHERE ete.cmp_id = :cmp_id 
		ORDER BY ete_poids, ete_nom_code ';
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function CampagneEstAssociee( $cmp_id ) {
	/**
	* Récupère les nombres d'association d'une Entité.
	*
	* \license Copyleft Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-15
	*
	* \param[in] $ent_id Identifiant de l'entité à récupérer
	*
	* \return Renvoi l'occurrence listant les associations de l'Entité ou FALSE si pas d'entité. Lève une Exception en cas d'erreur.
	*/
		$Compteurs = new stdClass();

		$Request = 'SELECT COUNT(dma_id) AS "total" FROM dma_dmia_activite WHERE cmp_id = :cmp_id ';
		$Query = $this->prepareSQL( $Request );
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		$this->executeSQL( $Query );
		$Compteurs->total_dma = $Query->fetchObject()->total;

		$Request = 'SELECT COUNT(mim_id) AS "total" FROM mim_matrice_impacts WHERE cmp_id = :cmp_id ';
		$Query = $this->prepareSQL( $Request );
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		$this->executeSQL( $Query );
		$Compteurs->total_mim = $Query->fetchObject()->total;
		
		$Request = 'SELECT COUNT(ent_id) AS "total" FROM cmen_cmp_ent WHERE cmp_id = :cmp_id ';
		$Query = $this->prepareSQL( $Request );
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		$this->executeSQL( $Query );
		$Compteurs->total_ent = $Query->fetchObject()->total;
		
		$Request = 'SELECT COUNT(ete_id) AS "total" FROM ete_echelle_temps WHERE cmp_id = :cmp_id ';
		$Query = $this->prepareSQL( $Request );
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		$this->executeSQL( $Query );
		$Compteurs->total_ete = $Query->fetchObject()->total;
		
		$Request = 'SELECT COUNT(act_id) AS "total" FROM act_activites WHERE cmp_id = :cmp_id ';
		$Query = $this->prepareSQL( $Request );
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		$this->executeSQL( $Query );
		$Compteurs->total_act = $Query->fetchObject()->total;
		
		$Request = 'SELECT COUNT(ppr_id) AS "total" FROM ppac_ppr_act WHERE cmp_id = :cmp_id ';
		$Query = $this->prepareSQL( $Request );
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		$this->executeSQL( $Query );
		$Compteurs->total_ppr = $Query->fetchObject()->total;
		
		$Request = 'SELECT COUNT(sts_id) AS "total" FROM cmst_cmp_sts WHERE cmp_id = :cmp_id ';
		$Query = $this->prepareSQL( $Request );
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		$this->executeSQL( $Query );
		$Compteurs->total_sts = $Query->fetchObject()->total;

		return $Compteurs;
	}


	public function supprimerCampagne( $cmp_id ) {
	/**
	* Supprimer une Campagne.
	*
	* \license Copyleft Loxense
	* \author Pierre-Luc MARY
	* \date 2024-01-14
	*
	* \param[in] $cmp_id Identifiant de la Campagne à supprimer
	*
	* \return Renvoi TRUE si la Campagne a été supprimée ou FALSE si la Campagne n'existe pas. Lève une Exception en cas d'erreur.
	*/

		$Query = $this->prepareSQL( 'DELETE ' .
			'FROM cmp_campagnes ' .
			'WHERE cmp_id = :cmp_id' );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return TRUE;
	}


	public function totalCampagnes( $sct_id = '' ) {
	/**
	* Calcul le nombre total de Campagnes.
	*
	* \license Copyleft Loxense
	* \author Pierre-Luc MARY
	* \date 2024-01-14
	*
	* \return Renvoi le nombre total de Campagnes stockées en base. Lève une Exception en cas d'erreur.
	*/

		$Request = 'SELECT ' .
		 'count(*) AS total ' .
		 'FROM cmp_campagnes ' ;

		if ( $sct_id != '' ) {
			$Request .= 'WHERE sct_id = :sct_id ';
		}

		$Query = $this->prepareSQL( $Request );

		if ( $sct_id != '' ) {
			$this->bindSQL( $Query, ':sct_id', $sct_id, PDO::PARAM_INT );
		}

		
		$this->executeSQL( $Query );
		
		$Occurrence = $Query->fetchObject() ;
		
		return $Occurrence->total;
	}


	public function validerDevaliderDateValidationCampagne( $cmp_id, $flag_validation=FALSE ) {
		$Request = 'UPDATE cmp_campagnes
			SET ';
		
		if ( $flag_validation == TRUE ) {
			$Request .= 'idn_id = :idn_id, ';
		}

		$Request .= 'cmp_flag_validation = :flag_validation, cmp_date_validation = :date_validation
			WHERE cmp_id = :cmp_id ';

		$Query = $this->prepareSQL( $Request );

		if ( $flag_validation == TRUE ) {
			$this->bindSQL( $Query, ':idn_id', $_SESSION['idn_id'], PDO::PARAM_INT );
			$this->bindSQL( $Query, ':cmp_flag_validation', TRUE, PDO::PARAM_BOOL );
			$this->bindSQL( $Query, ':cmp_date_validation', date('Y-m-d H:i:s'), PDO::PARAM_STR, L_CMP_DATE_VALIDATION );
		} else {
			$this->bindSQL( $Query, ':cmp_flag_validation', FALSE, PDO::PARAM_BOOL );
			$this->bindSQL( $Query, ':cmp_date_validation', NULL, PDO::PARAM_STR, L_CMP_DATE_VALIDATION );
		}
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
	
		$this->executeSQL( $Query );
	
		return TRUE;
	}


	public function modifierEntiteCampagne( $cmp_id, $ent_id, $ppr_id_cpca, $cmen_date_entretien_cpca = null, $cmen_effectif_total = null ) {
		/**
		 * Modifiée l'association entre une Campagne et une Entité.
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2025-04-01
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne à utiliser pour associer
		 * \param[in] $ent_id Identifiant de l'Entité à utiliser pour associer
		 * \param[in] $ppr_id_cpca Identifiant de la Partie Prenante qui a le rôle de CPCA
		 * \param[in] $cmen_date_entretien_cpca Date de l'entretien prévue avec le CPCA
		 * \param[in] $cmen_effectif_total Effectif total de l'Entité
		 *
		 * \return Renvoi TRUE si l'association à créer. Lève une Exception en cas d'erreur.
		 */

		$Request = 'UPDATE cmen_cmp_ent SET ';

		$Champs = '';

		if ($ppr_id_cpca != '' && $ppr_id_cpca != null) {
			$Champs = 'ppr_id_cpca = :ppr_id_cpca ';
		}

		if ($cmen_date_entretien_cpca != '' && $cmen_date_entretien_cpca != null) {
			if ($Champs != '') {
				$Champs .= ', ';
			}
			$Champs .= 'cmen_date_entretien_cpca = :cmen_date_entretien_cpca ';
		}

		if ($cmen_effectif_total != '' && $cmen_effectif_total != null) {
			if ($Champs != '') {
				$Champs .= ', ';
			}
			$Champs .= 'cmen_effectif_total = :cmen_effectif_total ';
		}

		$Request .= $Champs . 'WHERE cmp_id = :cmp_id AND ent_id = :ent_id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ent_id', $ent_id, PDO::PARAM_INT );

		if ($ppr_id_cpca != '' && $ppr_id_cpca != null) {
			$this->bindSQL( $Query, ':ppr_id_cpca', $ppr_id_cpca, PDO::PARAM_INT );
		}

		if ($cmen_date_entretien_cpca != '' && $cmen_date_entretien_cpca != null) {
			$this->bindSQL( $Query, ':cmen_date_entretien_cpca', $cmen_date_entretien_cpca, PDO::PARAM_STR, L_CMP_DATE );
		}

		if ($cmen_effectif_total != '' && $cmen_effectif_total != null) {
			$this->bindSQL( $Query, ':cmen_effectif_total', $cmen_effectif_total, PDO::PARAM_INT );
		}

		$this->executeSQL( $Query );

		return true;
	}


	public function modifierEffectifEntiteCampagne( $cmp_id, $ent_id, $cmen_effectif_total ) {
		/**
		 * Modifiée l'effectif total d'une Entité au moment de la Campagne.
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2025-05-05
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne à utiliser pour associer
		 * \param[in] $ent_id Identifiant de l'Entité à utiliser pour associer
		 * \param[in] $cmen_effectif_total Effectif total de l'Entité
		 *
		 * \return Renvoi TRUE si l'association à créer. Lève une Exception en cas d'erreur.
		 */

		$Request = 'UPDATE cmen_cmp_ent SET
			cmen_effectif_total = :cmen_effectif_total
			WHERE cmp_id = :cmp_id AND ent_id = :ent_id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ent_id', $ent_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':cmen_effectif_total', $cmen_effectif_total, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		return true;
	}


	public function associerEntiteCampagne( $cmp_id, $ent_id, $ppr_id_cpca = null, $cmen_date_entretien_cpca = null, $cmen_effectif_total = null ) {
		/**
		 * Crée l'association entre une Campagne et une Entité.
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-09-03
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne à utiliser pour associer
		 * \param[in] $ent_id Identifiant de l'Entité à utiliser pour associer
		 * \param[in] $ppr_id_cpca Identifiant de la Partie Prenante qui a le rôle de CPCA
		 * \param[in] $cmen_date_entretien_cpca Date de l'entretien prévue avec le CPCA
		 * \param[in] $cmen_effectif_total Effectif total de l'Entité
		 *
		 * \return Renvoi TRUE si l'association à créer. Lève une Exception en cas d'erreur.
		 */
		$Request = 'INSERT INTO cmen_cmp_ent (cmp_id, ent_id ';

		if ($ppr_id_cpca != null) {
			$Request .= ', ppr_id_cpca';
		}

		if ($cmen_date_entretien_cpca != null) {
			$Request .= ', cmen_date_entretien_cpca';
		}

		if ($cmen_effectif_total != null) {
			$Request .= ', cmen_effectif_total';
		}

		$Request .= ') VALUES (:cmp_id, :ent_id ';

		if ($ppr_id_cpca != null) {
			$Request .= ', :ppr_id_cpca';
		}

		if ($cmen_date_entretien_cpca != null) {
			$Request .= ', :cmen_date_entretien_cpca';
		}

		if ($cmen_effectif_total != null) {
			$Request .= ', :cmen_effectif_total';
		}

		$Request .= ') ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ent_id', $ent_id, PDO::PARAM_INT );

		if ($ppr_id_cpca != null) {
			$this->bindSQL( $Query, ':ppr_id_cpca', $ppr_id_cpca, PDO::PARAM_INT );
		}

		if ($cmen_date_entretien_cpca != null) {
			$this->bindSQL( $Query, ':cmen_date_entretien_cpca', $cmen_date_entretien_cpca, PDO::PARAM_STR, L_CMP_DATE );
		}

		if ($cmen_effectif_total != null) {
			$this->bindSQL( $Query, ':cmen_effectif_total', $cmen_effectif_total, PDO::PARAM_INT );
		}

		$this->executeSQL( $Query );

		return true;
	}


	public function dissocierEntiteCampagne( $cmp_id, $ent_id ) {
		/**
		 * Supprime l'association entre une Campagne et une Entité.
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-09-03
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne à utiliser pour dissocier
		 * \param[in] $ent_id Identifiant de l'Entité à utiliser pour dissocier
		 *
		 * \return Renvoi TRUE si l'association à supprimer. Lève une Exception en cas d'erreur.
		 */
		$Request = 'DELETE FROM cmen_cmp_ent
			WHERE (cmp_id = :cmp_id AND ent_id = :ent_id) ';
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ent_id', $ent_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		return TRUE;
	}


	public function associerFournisseurCampagne( $cmp_id, $frn_id ) {
		/**
		 * Crée l'association entre une Campagne et un Fournisseur.
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-09-03
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne à utiliser pour associer
		 * \param[in] $frn_id Identifiant du Fournisseur à utiliser pour associer
		 *
		 * \return Renvoi TRUE si l'association à créer. Lève une Exception en cas d'erreur.
		 */
		$Request = 'INSERT INTO cmfr_cmp_frn (cmp_id, frn_id)
			VALUES (:cmp_id, :frn_id) ';
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':frn_id', $frn_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		return TRUE;
	}


	public function dissocierFournisseurCampagne( $cmp_id, $frn_id ) {
		/**
		 * Supprime l'association entre une Campagne et un Fournisseur.
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-09-03
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne à utiliser pour dissocier
		 * \param[in] $frn_id Identifiant du Fournisseur à utiliser pour dissocier
		 *
		 * \return Renvoi TRUE si l'association à supprimer. Lève une Exception en cas d'erreur.
		 */
		$Request = 'DELETE FROM cmfr_cmp_frn
			WHERE (cmp_id = :cmp_id AND frn_id = :frn_id) ';
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':frn_id', $frn_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		return TRUE;
	}


	public function associerSiteCampagne( $cmp_id, $sts_id ) {
		/**
		 * Crée l'association entre une Campagne et une Société.
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-09-03
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne à utiliser pour associer
		 * \param[in] $sts_id Identifiant de la Société à utiliser pour associer
		 *
		 * \return Renvoi TRUE si l'association à créer. Lève une Exception en cas d'erreur.
		 */
		$Request = 'INSERT INTO cmst_cmp_sts (cmp_id, sts_id)
			VALUES (:cmp_id, :sts_id) ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':sts_id', $sts_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		return TRUE;
	}


	public function dissocierSiteCampagne( $cmp_id, $sts_id ) {
		/**
		 * Supprime l'association entre une Campagne et une Société.
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-09-03
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne à utiliser pour dissocier
		 * \param[in] $sts_id Identifiant de la Société à utiliser pour dissocier
		 *
		 * \return Renvoi TRUE si l'association à supprimer. Lève une Exception en cas d'erreur.
		 */
		$Request = 'DELETE FROM cmst_cmp_sts
			WHERE (cmp_id = :cmp_id AND sts_id = :sts_id) ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':sts_id', $sts_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		return TRUE;
	}


	public function listerEntiteCampagne( $cmp_id ) {
		/**
		 * Récupère la liste des Entités qui sont rattachées à une Campagne.
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-09-03
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne où lancer la recherche
		 *
		 * \return Renvoi un tableau d'occurrences d'Entité. Lève une Exception en cas d'erreur.
		 */
		$Request = 'SELECT * FROM cmen_cmp_ent
			WHERE cmp_id = :cmp_id  ';
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function verifierCoherenceSocieteCampagne( $sct_id='', $cmp_id='', $reset_session=0 ) {
		/**
		 * Vérifie la cohérence entre une Société et une Campagne. En d'autres termes, on ne peut pas accéder
		 * à une Campagne qui n'est pas associée à la Société déclarée.
		 * Dans un tel cas, si "$reset_session" est à 1, on réinitialise les variables de session.
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-09-03
		 *
		 * \param[in] $sct_id Identifiant de la Campagne où lancer la recherche
		 * \param[in] $cmp_id Identifiant de la Campagne où lancer la recherche
		 * \param[in] $reset_session Si le Flag est à 1, on reset les variables de session
		 *
		 * \return Renvoi TRUE si la cohérence est trouvée, sinon renvoie FALSE. Lève une Exception en cas d'erreur.
		 */

		if ( $sct_id == '' ) $sct_id = $_SESSION['s_sct_id'];
		if ( $cmp_id == '' ) $cmp_id = $_SESSION['s_cmp_id'];

		if ( $sct_id == '' ) return FALSE;
		if ( $cmp_id == '' ) return FALSE;

		if ( $_SESSION['idn_super_admin'] ) {
			$Request = 'SELECT idn_id FROM cmp_campagnes AS "cmp"
				WHERE cmp.cmp_id = :cmp_id AND cmp.sct_id = :sct_id ';
		} else {
			
		}

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':sct_id', $sct_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		$Resultat = $Query->fetch();

		if ( $Resultat == FALSE ) {
			return FALSE;
		} else {
			return TRUE;
		}
	}


	public function dupliquerCampagne( $cmp_id, $cmp_date = '' ) {
		/**
		 * Duplique la Campagne et toutes ses associations
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-09-03
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne à dupliquer
		 * \param[in] $cmp_date Date de la nouvelle Campagne (suite à la duplication)
		 *
		 * \return Renvoi l'ID de la nouvelle Campagne (issue de la duplication), sinon renvoie FALSE. Lève une Exception en cas d'erreur.
		 */

		include( DIR_LIBRAIRIES . '/Class_Activites_PDO.inc.php' );
		$objActivites = new Activites();

		// ==========================================================================
		// Duplication de la Campagne d'Origine en la renommant avec la date du jour.
		$Campagne_O = $this->detaillerCampagne( $cmp_id );

		$Request = 'INSERT INTO cmp_campagnes
			(sct_id, idn_id, cmp_date, cmp_flag_validation, cmp_date_validation) VALUES
			(:sct_id, :idn_id, :cmp_date, :cmp_flag_validation, :cmp_date_validation) ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':sct_id', $Campagne_O->sct_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':idn_id', $Campagne_O->idn_id, PDO::PARAM_INT );

		if ( $cmp_date == '' ) {
			$this->bindSQL( $Query, ':cmp_date', date('Y-m-d'), PDO::PARAM_STR, L_CMP_DATE );
		} else {
			$tDate = explode('-', $cmp_date);
			if ( checkdate($tDate[1], $tDate[2], $tDate[0]) ) {
				$this->bindSQL( $Query, ':cmp_date', $cmp_date, PDO::PARAM_STR, L_CMP_DATE );
			} else {
				throw new Exception('Date invalide', -1);
			}
		}

		if ( $Campagne_O->cmp_flag_validation == NULL ) {
			$this->bindSQL( $Query, ':cmp_flag_validation', FALSE, PDO::PARAM_BOOL );
			$this->bindSQL( $Query, ':cmp_date_validation', NULL, PDO::PARAM_NULL );
		} else {
			$this->bindSQL( $Query, ':cmp_flag_validation', TRUE, PDO::PARAM_BOOL );
			$this->bindSQL( $Query, ':cmp_date_validation', $Campagne_O->cmp_date_validation,
				PDO::PARAM_STR, L_CMP_DATE_VALIDATION );
		}

		$this->executeSQL( $Query );

		$cmp_id_new = $this->lastInsertId( 'cmp_campagnes_cmp_id_seq' );


		// ================================
		// Duplication des Types d'Impact.
		$Request = 'SELECT * FROM tim_types_impact WHERE cmp_id = :cmp_id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		foreach( $Query->fetchAll( PDO::FETCH_CLASS ) as $Occurrence ) {
			$Request = 'INSERT INTO tim_types_impact 
				(cmp_id, tim_poids, tim_nom_code) VALUES
				(:cmp_id, :tim_poids, :tim_nom_code) ';

			$Query = $this->prepareSQL( $Request );

			$this->bindSQL( $Query, ':cmp_id', $cmp_id_new, PDO::PARAM_INT );
			$this->bindSQL( $Query, ':tim_poids', $Occurrence->tim_poids, PDO::PARAM_INT );
			$this->bindSQL( $Query, ':tim_nom_code', $Occurrence->tim_nom_code, PDO::PARAM_STR, 60 );

			$this->executeSQL( $Query );

			$tim_id_new[$Occurrence->tim_id] = $this->lastInsertId( 'tim_types_impact_tim_id_seq' );
		}


		// ==================================
		// Duplication des Niveaux d'Impact.
		$Request = 'SELECT * FROM nim_niveaux_impact WHERE cmp_id = :cmp_id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		foreach( $Query->fetchAll( PDO::FETCH_CLASS ) as $Occurrence ) {
			$Request = 'INSERT INTO nim_niveaux_impact
				(cmp_id, nim_numero, nim_poids, nim_nom_code, nim_couleur) VALUES
				(:cmp_id, :nim_numero, :nim_poids, :nim_nom_code, :nim_couleur) ';

			$Query = $this->prepareSQL( $Request );

			$this->bindSQL( $Query, ':cmp_id', $cmp_id_new, PDO::PARAM_INT );
			$this->bindSQL( $Query, ':nim_numero', $Occurrence->nim_numero, PDO::PARAM_INT );
			$this->bindSQL( $Query, ':nim_poids', $Occurrence->nim_poids, PDO::PARAM_INT );
			$this->bindSQL( $Query, ':nim_nom_code', $Occurrence->nim_nom_code, PDO::PARAM_STR, 60 );
			$this->bindSQL( $Query, ':nim_couleur', $Occurrence->nim_couleur, PDO::PARAM_STR, 6 );

			$this->executeSQL( $Query );

			$nim_id_new[$Occurrence->nim_id] = $this->lastInsertId( 'nim_niveaux_impact_nim_id_seq' );
		}


		// =======================================
		// Duplication de la Matrice des Impacts.
		$Request = 'SELECT * FROM mim_matrice_impacts WHERE cmp_id = :cmp_id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		foreach( $Query->fetchAll( PDO::FETCH_CLASS ) as $Occurrence ) {
			$Request = 'INSERT INTO mim_matrice_impacts
				(cmp_id, nim_id, tim_id, mim_description) VALUES
				(:cmp_id, :nim_id, :tim_id, :mim_description) ';

			$Query = $this->prepareSQL( $Request );

			$this->bindSQL( $Query, ':cmp_id', $cmp_id_new, PDO::PARAM_INT );
			$this->bindSQL( $Query, ':nim_id', $nim_id_new[$Occurrence->nim_id], PDO::PARAM_INT );
			$this->bindSQL( $Query, ':tim_id', $tim_id_new[$Occurrence->tim_id], PDO::PARAM_INT );
			$this->bindSQL( $Query, ':mim_description', $Occurrence->mim_description, PDO::PARAM_LOB );
			
			$this->executeSQL( $Query );
		}


		// ==============================================
		// Duplication du lien avec les Entités à gérer.
		$Request = 'SELECT * FROM cmen_cmp_ent WHERE cmp_id = :cmp_id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		foreach( $Query->fetchAll( PDO::FETCH_CLASS ) as $Occurrence ) {
			$Request = 'INSERT INTO cmen_cmp_ent
				(cmp_id, ent_id) VALUES
				(:cmp_id, :ent_id) ';

			$Query = $this->prepareSQL( $Request );

			$this->bindSQL( $Query, ':cmp_id', $cmp_id_new, PDO::PARAM_INT );
			$this->bindSQL( $Query, ':ent_id', $Occurrence->ent_id, PDO::PARAM_INT );

			$this->executeSQL( $Query );
		}


		// ===================================
		// Duplication des Echelles de Temps.
		$Request = 'SELECT * FROM ete_echelle_temps WHERE cmp_id = :cmp_id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		foreach( $Query->fetchAll( PDO::FETCH_CLASS ) as $Occurrence ) {
			$Request = 'INSERT INTO ete_echelle_temps
				(cmp_id, ete_poids, ete_nom_code) VALUES
				(:cmp_id, :ete_poids, :ete_nom_code) ';

			$Query = $this->prepareSQL( $Request );

			$this->bindSQL( $Query, ':cmp_id', $cmp_id_new, PDO::PARAM_INT );
			$this->bindSQL( $Query, ':ete_poids', $Occurrence->ete_poids, PDO::PARAM_INT );
			$this->bindSQL( $Query, ':ete_nom_code', $Occurrence->ete_nom_code, PDO::PARAM_STR, 60 );
			
			$this->executeSQL( $Query );
		}


		// ========================================
		// Duplication de l'association aux Sites.
		$Request = 'SELECT * FROM cmst_cmp_sts WHERE cmp_id = :cmp_id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		foreach( $Query->fetchAll( PDO::FETCH_CLASS ) as $Occurrence ) {
			$Request = 'INSERT INTO cmst_cmp_sts
				(cmp_id, sts_id) VALUES
				(:cmp_id, :sts_id) ';

			$Query = $this->prepareSQL( $Request );

			$this->bindSQL( $Query, ':cmp_id', $cmp_id_new, PDO::PARAM_INT );
			$this->bindSQL( $Query, ':sts_id', $Occurrence->sts_id, PDO::PARAM_INT );

			$this->executeSQL( $Query );
		}


		// ===========================
		// Duplication des Activités.
		$Request = 'SELECT act_id FROM act_activites WHERE cmp_id = :cmp_id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		foreach( $Query->fetchAll( PDO::FETCH_CLASS ) as $Occurrence ) {
			$objActivites->dupliquerActivite($Occurrence->act_id, NULL, TRUE, TRUE, TRUE, TRUE, TRUE, $cmp_id_new);
		}


		return $cmp_id_new;
	}


	public function syntheseCampagne( $cmp_id, $sct_id = '' ) {
		/**
		 * Récupère toutes les données de la Campagne
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-09-05
		 *
		 * \param[in] $cmp_id Identifiant la Campagne pour laquelle on récupère les données.
		 *
		 * \return Renvoi TRUE si la Campagne est dupliquée, sinon renvoie FALSE. Lève une Exception en cas d'erreur.
		 */

		$Donnees = [];

		// ==========================================
		// Récupère les informations de la Campagne.
		if ( $sct_id == '') $sct_id = $_SESSION['s_sct_id'];
		$_Campagne = $this->rechercherCampagnes($sct_id, 'cmp_date', $cmp_id);
		$Donnees['campagne'] = $_Campagne[0];


		// ==========================================================================
		// Récupère le nombre de BIA.
		$Request = 'SELECT COUNT(*) AS "total" FROM cmen_cmp_ent AS "cmen" WHERE cmen.cmp_id = :cmp_id ';
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );

		$Resultat = $Query->fetchObject();

		$Donnees['total_bia'] = $Resultat->total;


		// ==========================================================================
		// Récupère le nombre de BIA validés.
		$Request = 'SELECT COUNT(ppr_id_validation) AS "total" FROM cmen_cmp_ent AS "cmen" WHERE cmen.cmp_id = :cmp_id
			AND cmen.ppr_id_validation IS NOT NULL ';
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		$Resultat = $Query->fetchObject();
		
		$Donnees['total_bia_valides'] = $Resultat->total;
		

		// ==========================================================================
		// Récupère le nombre de BIA en cours.
		$Request = 'SELECT ent.ent_nom, COUNT(DISTINCT act.act_id) AS "total"
			FROM cmen_cmp_ent AS "cmen"
			LEFT JOIN ent_entites AS "ent" ON ent.ent_id = cmen.ent_id
			LEFT JOIN act_activites AS "act" ON act.ent_id = ent.ent_id
			WHERE cmen.cmp_id = :cmp_id
			AND cmen.ppr_id_validation IS NULL
			GROUP BY ent.ent_nom ';
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
//		$Resultat = $Query->fetchObject();
		
//		$Donnees['total_bia_en_cours'] = $Resultat->total;

		$Donnees['total_bia_en_cours'] = 0;
		
		foreach( $Query->fetchAll( PDO::FETCH_CLASS ) as $Occurrence ) {
			if ( $Occurrence->total > 1 ) {
				$Donnees['total_bia_en_cours'] += 1;
			}
		}
		

		// ==========================================================================
		// Récupère le nombre total d'Activité.
		$Request = 'SELECT COUNT(*) AS "total" FROM act_activites AS "act" WHERE act.cmp_id = :cmp_id ';
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		$Resultat = $Query->fetchObject();
		
		$Donnees['total_act'] = $Resultat->total;
		
		
		// ==========================================================================
		// Récupère le nombre total d'Activité Graves (niveau d'impact > 2).
		$Request = 'SELECT COUNT(DISTINCT act_id) AS "total"
FROM dma_dmia_activite AS "dma"
LEFT JOIN mim_matrice_impacts AS "mim" ON mim.mim_id = dma.mim_id
LEFT JOIN nim_niveaux_impact AS "nim" ON nim.nim_id = mim.nim_id
WHERE dma.cmp_id = :cmp_id AND nim.nim_poids > 2 ';
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		$Resultat = $Query->fetchObject();
		
		$Donnees['total_act_3_4'] = $Resultat->total;
		
		
		// ==========================================================================
		// Récupère le nombre total d'Activité Critiques (niveau d'impact = 4).
		$Request = 'SELECT COUNT(DISTINCT act_id) AS "total"
FROM dma_dmia_activite AS "dma"
LEFT JOIN mim_matrice_impacts AS "mim" ON mim.mim_id = dma.mim_id
LEFT JOIN nim_niveaux_impact AS "nim" ON nim.nim_id = mim.nim_id
LEFT JOIN ete_echelle_temps AS "ete" ON ete.ete_id = dma.ete_id
WHERE dma.cmp_id = :cmp_id  AND nim.nim_poids = 4 ';
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		$Resultat = $Query->fetchObject();
		
		$Donnees['total_act_4'] = $Resultat->total;
		
		
		// ==========================================================================
		// Récupère le nombre total d'Activité Graves (niveau d'impact = 3).
		$Request = 'SELECT COUNT(DISTINCT act_id) AS "total"
FROM dma_dmia_activite AS "dma"
LEFT JOIN mim_matrice_impacts AS "mim" ON mim.mim_id = dma.mim_id
LEFT JOIN nim_niveaux_impact AS "nim" ON nim.nim_id = mim.nim_id
LEFT JOIN ete_echelle_temps AS "ete" ON ete.ete_id = dma.ete_id
WHERE dma.cmp_id = :cmp_id  AND nim.nim_poids = 3 ';
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		$Resultat = $Query->fetchObject();
		
		$Donnees['total_act_3'] = $Resultat->total;
		
		
		// ==========================================================================
		// Récupère le nombre total de Sites.
		$Request = 'SELECT COUNT(DISTINCT sts_id) AS "total"
FROM acst_act_sts AS "acst"
WHERE acst.cmp_id = :cmp_id ';
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		$Resultat = $Query->fetchObject();
		
		$Donnees['total_sts'] = $Resultat->total;
		
		
		// ==========================================================================
		// Récupère le nombre total d'Applications.
		$Request = 'SELECT COUNT(DISTINCT app_id) AS "total"
FROM act_activites AS "act"
LEFT JOIN acap_act_app AS "acap" ON acap.act_id = act.act_id
WHERE act.cmp_id = :cmp_id ';
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		$Resultat = $Query->fetchObject();
		
		$Donnees['total_app'] = $Resultat->total;
		
		
		// ==========================================================================
		// Récupère le nombre total de Personnes Clées.
		$Request = 'SELECT COUNT(DISTINCT ppr_id) AS "total"
FROM act_activites AS "act"
LEFT JOIN ppac_ppr_act AS "ppac" ON ppac.act_id = act.act_id
WHERE act.cmp_id = :cmp_id ';
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		$Resultat = $Query->fetchObject();
		
		$Donnees['total_ppr'] = $Resultat->total;
		
		
		// ==========================================================================
		// Récupère le nombre total de Fournisseurs.
		$Request = 'SELECT COUNT(DISTINCT frn_id) AS "total"
FROM act_activites AS "act"
LEFT JOIN acfr_act_frn AS "acfr" ON acfr.act_id = act.act_id
WHERE act.cmp_id = :cmp_id ';
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		$Resultat = $Query->fetchObject();
		
		$Donnees['total_frn'] = $Resultat->total;
		
		return $Donnees;
	}


	public function rechercherApplicationsCampagne( $cmp_id, $dmia = '*', $ent_id = '*' ) {
		/**
		 * Recherche les Applications liés à une Campagne (et les classes par DMIA)
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-09-10
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne de rattachement
		 * \param[in] $dmia ID de l'échelle de temps qui matérialise le DMIA de l'application
		 * \param[in] $ent_id ID de l'Entité à récupérer plus particulièrement
		 *
		 * \return Renvoi TRUE si la cohérence est trouvée, sinon renvoie FALSE. Lève une Exception en cas d'erreur.
		 */

		$Request = 'SELECT app.app_id, app.app_nom, app.app_niveau_service, app.app_hebergement, MIN(ete.ete_poids) AS "dmia", MIN(ete1.ete_poids) AS "pdma",
STRING_AGG( ent.ent_nom || \'+++\' || ent.ent_description || \'+++ - \' || act.act_nom || \'###\'||act.act_id, \',<br>\' ORDER BY ent_nom, act_nom ) AS "act_nom",
STRING_AGG( DISTINCT acap.acap_palliatif, \'##\' ) AS "acap_palliatif",
STRING_AGG( DISTINCT acap.acap_donnees, \'##\' ) AS "acap_donnees",
STRING_AGG( DISTINCT acap.acap_hebergement, \'##\' ) AS "acap_hebergement",
STRING_AGG( DISTINCT acap.acap_niveau_service, \'##\' ) AS "acap_niveau_service"
FROM act_activites AS "act"
RIGHT JOIN acap_act_app AS "acap" ON acap.act_id = act.act_id
LEFT JOIN ent_entites AS "ent" ON ent.ent_id = act.ent_id
LEFT JOIN app_applications AS "app" ON app.app_id = acap.app_id
LEFT JOIN ete_echelle_temps AS "ete" ON ete.ete_id = acap.ete_id_dima
LEFT JOIN ete_echelle_temps AS "ete1" ON ete1.ete_id = acap.ete_id_pdma
WHERE act.cmp_id = :cmp_id ';

		if ( $dmia != '*' ) {
			$Request .= 'AND acap.ete_id_dima = :dmia ';
		}

		if ( $ent_id != '*' ) {
			$Request .= 'AND act.ent_id = :ent_id ';
		}

		$Request .= 'GROUP BY app.app_id, app.app_nom
ORDER BY dmia, app.app_nom ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );

		if ( $dmia != '*' ) {
			$this->bindSQL( $Query, ':dmia', $dmia, PDO::PARAM_INT );
		}

		if ( $ent_id != '*' ) {
			$this->bindSQL( $Query, ':ent_id', $ent_id, PDO::PARAM_INT );
		}

		$this->executeSQL( $Query );

		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function rechercherPDMAApplicationsCampagne( $cmp_id, $pdma = '*', $ent_id = '*' ) {
		/**
		 * Recherche les PDMA des Applications liés à une Campagne (et les classes par DMIA)
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-09-10
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne de rattachement
		 * \param[in] $pdma ID de l'échelle de temps qui matérialise le PDMA de l'application
		 * \param[in] $ent_id ID de l'Entité à récupérer plus particulièrement
		 *
		 * \return Renvoi TRUE si la cohérence est trouvée, sinon renvoie FALSE. Lève une Exception en cas d'erreur.
		 */

		$Request = 'SELECT app.app_nom, app.app_description, app.app_hebergement, app.app_niveau_service, acap.acap_hebergement, acap.acap_niveau_service,
MIN(ete.ete_poids) AS "pdma",
STRING_AGG(ent_nom || \' (\' || ent_description || \') - \' || act_nom, \',//\' ORDER BY ent_nom, act_nom) AS "act_nom",
STRING_AGG( DISTINCT acap.acap_donnees, \'##\' ) AS "acap_donnees"
FROM app_applications AS "app"
LEFT JOIN acap_act_app AS "acap" ON acap.app_id = app.app_id
LEFT JOIN ete_echelle_temps AS "ete" ON ete.ete_id = acap.ete_id_pdma
LEFT JOIN act_activites AS "act" ON act.act_id = acap.act_id
LEFT JOIN ent_entites AS "ent" ON ent.ent_id = act.ent_id
WHERE ete_poids IS NOT NULL AND act.cmp_id = :cmp_id ';

		if ( $pdma != '*' ) {
			$Request .= 'AND acap.ete_id_pdma = :pdma ';
		}

		if ( $ent_id != '*' ) {
			$Request .= 'AND act.ent_id = :ent_id ';
		}

		$Request .= 'GROUP BY app.app_nom, app.app_description, app.app_hebergement, app.app_niveau_service, acap.acap_hebergement, acap.acap_niveau_service
ORDER BY pdma, app.app_nom ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );

		if ( $pdma != '*' ) {
			$this->bindSQL( $Query, ':pdma', $pdma, PDO::PARAM_INT );
		}

		if ( $ent_id != '*' ) {
			$this->bindSQL( $Query, ':ent_id', $ent_id, PDO::PARAM_INT );
		}

		$this->executeSQL( $Query );

		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function rechercherPersonnesClesCampagne( $cmp_id, $ent_id = '*' ) {
		/**
		 * Recherche les Personnes Clées liées à une Campagne (et les classes par Entités)
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-09-10
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne de rattachement
		 * \param[in] $ent_id ID de l'Entité à travailler en particulier
		 *
		 * \return Renvoi TRUE si la cohérence est trouvée, sinon renvoie FALSE. Lève une Exception en cas d'erreur.
		 */
		
		$Request = 'SELECT ppr.ppr_prenom, ppr.ppr_nom, ent.ent_nom, ent.ent_description,
STRING_AGG( act.act_nom, \',<br>\' ) AS "act_nom",
STRING_AGG( DISTINCT ppac_description, \',<br>\' ) AS "ppac_description"
FROM act_activites AS "act" 
RIGHT JOIN ppac_ppr_act AS "ppac" ON ppac.act_id = act.act_id
LEFT JOIN ppr_parties_prenantes AS "ppr" ON ppr.ppr_id = ppac.ppr_id
LEFT JOIN ent_entites AS "ent" ON ent.ent_id = act.ent_id
WHERE act.cmp_id = :cmp_id ';

		
		if ( $ent_id != '*' ) {
			$Request .= 'AND act.ent_id = :ent_id ';
		}
		
		$Request .= 'GROUP BY ent.ent_nom, ent.ent_description, ppr.ppr_prenom, ppr.ppr_nom
ORDER BY ent.ent_nom, ppr.ppr_prenom, ppr.ppr_nom ';
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		
		if ( $ent_id != '*' ) {
			$this->bindSQL( $Query, ':ent_id', $ent_id, PDO::PARAM_INT );
		}
		
		$this->executeSQL( $Query );
		
		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function rechercherFournisseursCampagne( $cmp_id, $dmia = '*', $ent_id = '*' ) {
		/**
		 * Recherche les Fournisseurs liées à une Campagne (et les classes par Entités)
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-09-10
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne de rattachement
		 * \param[in] $dmia ID de l'échelle de temps qui matérialise le DMIA de l'application
		 * \param[in] $ent_id ID de l'Entité à travailler en particulier
		 *
		 * \return Renvoi TRUE si la cohérence est trouvée, sinon renvoie FALSE. Lève une Exception en cas d'erreur.
		 */

		$Request = 'SELECT frn.frn_nom, frn.frn_description, tfr.tfr_nom_code,
	ent.ent_nom, ent.ent_description,
	ete.ete_poids, ete.ete_nom_code,
	acfr.acfr_consequence_indisponibilite, acfr.acfr_palliatif_tiers,
	STRING_AGG( act.act_nom, \',<br>\' ) AS "act_nom"
FROM acfr_act_frn AS "acfr"
LEFT JOIN act_activites AS "act"  ON acfr.act_id = act.act_id
LEFT JOIN frn_fournisseurs AS "frn" ON frn.frn_id = acfr.frn_id
LEFT JOIN tfr_types_fournisseur AS "tfr" ON tfr.tfr_id = frn.tfr_id
LEFT JOIN ent_entites AS "ent" ON ent.ent_id = act.ent_id
LEFT JOIN ete_echelle_temps AS "ete" ON ete.ete_id = acfr.ete_id
WHERE act.cmp_id = :cmp_id ';

		if ( $dmia != '*' ) {
			$Request .= 'AND acfr.ete_id = :ete_id ';
		}


		if ( $ent_id != '*' ) {
			$Request .= 'AND act.ent_id = :ent_id ';
		}

		$Request .= 'GROUP BY frn.frn_nom, frn.frn_description, tfr.tfr_nom_code, ent.ent_nom, ent.ent_description, ete.ete_poids, ete.ete_nom_code, acfr.acfr_consequence_indisponibilite, acfr.acfr_palliatif_tiers
ORDER BY ete.ete_poids, ent.ent_nom, tfr.tfr_nom_code, frn.frn_nom ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		
		if ( $dmia != '*' ) {
			$this->bindSQL( $Query, ':ete_id', $dmia, PDO::PARAM_INT );
		}
		
		if ( $ent_id != '*' ) {
			$this->bindSQL( $Query, ':ent_id', $ent_id, PDO::PARAM_INT );
		}

		$this->executeSQL( $Query );

		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function informationsValidationEntite( $cmp_id, $ent_id ) {
		/**
		 * Récupère les informations de validation d'une Entité dans une Campagne.
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-10-25
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne
		 * \param[in] $ent_id ID de l'Entité à récupérer
		 *
		 * \return Renvoi un tableau qui contient la date de validation et le nom du valideur. Lève une Exception en cas d'erreur.
		 */

		$Request = 'SELECT cmen.*, ppr.* FROM cmen_cmp_ent AS "cmen"
			LEFT JOIN ppr_parties_prenantes AS "ppr" ON ppr.ppr_id = cmen.ppr_id_validation
			WHERE ent_id = :ent_id AND cmp_id = :cmp_id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':ent_id', $ent_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		return $Query->fetchObject();
	}


	public function modifierValidationEntite( $cmp_id, $ent_id, $ppr_id_validation, $cmen_date_validation ) {
		/**
		 * Modifie les informations de validation d'une Entité dans une Campagne.
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-10-25
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne
		 * \param[in] $ent_id ID de l'Entité à récupérer
		 *
		 * \return Renvoi TRUE si la mise à jour a été faite ou FALSE si la mise à jour n'a pu être faite. Lève une Exception en cas d'erreur.
		 */

		$Request = 'UPDATE cmen_cmp_ent SET
			ppr_id_validation = :ppr_id_validation,
			cmen_date_validation = :cmen_date_validation
			WHERE ent_id = :ent_id AND cmp_id = :cmp_id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':ent_id', $ent_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ppr_id_validation', $ppr_id_validation, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':cmen_date_validation', $cmen_date_validation, PDO::PARAM_STR, L_CMP_DATE );

		$this->executeSQL( $Query );

		return $Query->fetchObject();
	}
} // Fin class Campagnes

?>