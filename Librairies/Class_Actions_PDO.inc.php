<?php

include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Parametres_PDO.inc.php' );


class Actions extends HBL_Parametres {
/**
* Cette classe gère les Actions.
*
* PHP version 5
* @license Loxense
* @author Pierre-Luc MARY
* @version 1.0
*/ 

	const ID_TYPE = PDO::PARAM_INT;

	const CODE_TYPE = PDO::PARAM_STR;
	const CODE_LENGTH = 20;

	const LIBELLE_TYPE = PDO::PARAM_STR;
	const LIBELLE_LENGTH = 100;

	const DESCRIPTION_TYPE = PDO::PARAM_STR;
	const DESCRIPTION_LENGTH = 100;

	const TEXTE_TYPE = PDO::PARAM_LOB;

	const DATE_TYPE = PDO::PARAM_STR;
	const DATE_LENGTH = 10;

	const LANGUE_TYPE = PDO::PARAM_STR;
	const LANGUE_LENGTH = 2;

	const LOCALISATION_TYPE = PDO::PARAM_STR;
	const LOCALISATION_LENGTH = 255;


	public $LastInsertId;


	public function __construct() {
	/**
	* Connexion à la base de données via HBL_Connecteur_BD.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @version 1.0
	* @date 2016-10-24
	*
	* @return Renvoi un booléen sur le succès de la connexion à la base de données
	*/
		parent::__construct();

		return true;
	}


	/* ===============================================================================
	** Gestion des Actions
	*/


	public function listerActions( $crs_id, $trier='mcr_libelle', $langue='fr', $act_id = '', $chercher = '' ) {
	/**
	* Lister les Actions
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @version 1.0
	* @date 2017-04-24
	*
	* @param[in] $crs_id ID de la cartographie
	* @param[in] $trier Nom de la colonne sur laquelle on réalise le tri
	* @param[in] $langue ID de la langue d'affichage
	*
	* @return Renvoi une liste d'action ou lève une erreur en cas d'erreur. 
	*			
	*/
		if ( $chercher != '' ) {
			$SQL_Chercher = 'AND (LOWER(mcr.mcr_libelle) like :chercher OR LOWER(lbr1.lbr_libelle) like :chercher ' .
				'OR LOWER(spp.spp_nom) like :chercher OR LOWER(lbr5.lbr_libelle) like :chercher ' .
				'OR LOWER(act.act_libelle) like :chercher OR LOWER(idn_login) like :chercher ' .
				'OR LOWER(cvl_nom) like :chercher OR LOWER(cvl_prenom) like :chercher ' .
				'OR LOWER(lbr3.lbr_libelle) like :chercher) ';
		} else {
			$SQL_Chercher = '';
		}

		$sql = 'SELECT ' .
			'DISTINCT mcr.mcr_id, mcr.rcs_id, mcr.mgr_id, mcr.mcr_libelle, ' .
			'spp.spp_nom, ' .
			'lbr1.lbr_libelle AS "mgr_libelle", ' .
			'lbr2.lbr_libelle AS "mcr_etat_libelle", ' .
			'lbr3.lbr_libelle AS "act_statut_libelle", ' .
			'lbr4.lbr_libelle AS "act_frequence_libelle", ' .
			'lbr5.lbr_libelle AS "tsp_libelle", ' .
			'act.act_id, act.idn_id, act.acg_id, act.act_libelle, act.act_description, act.act_statut_code, act.act_frequence_code, ' .
			'act.act_date_debut_p, act.act_date_fin_p, act.act_date_debut_r, act.act_date_fin_r, act.act_priorite, ' . //act.act_type_code, ' .
			'idn_login, cvl_nom, cvl_prenom, ' .
			'pea_cotation ' .
			'FROM spp_supports AS "spp" ' .
			'LEFT JOIN rcs_risques_cartographies AS "rcs" ON rcs.spp_id = spp.spp_id ' .
			'LEFT JOIN pea_poids_evaluation_actifs as "pea" ON pea.pea_id = rcs.rcs_cotation_actif ' .
			'LEFT JOIN mcr_mesures_cartographies AS "mcr" ON mcr.rcs_id = rcs.rcs_id ' .
			'LEFT JOIN mgr_mesures_generiques AS "mgr" ON mgr.mgr_id = mcr.mgr_id ' .
			'LEFT JOIN lbr_libelles_referentiel AS "lbr1" ON lbr1.lbr_code = mgr.mgr_code AND lbr1.lng_id = :langue ' .
			'LEFT JOIN lbr_libelles_referentiel AS "lbr2" ON lbr2.lbr_code = \'MCR_ETAT_\'||mcr.mcr_etat_code AND lbr2.lng_id = :langue ' .
			'LEFT JOIN act_actions AS "act" ON act.mcr_id = mcr.mcr_id ' .
			'LEFT JOIN lbr_libelles_referentiel AS "lbr3" ON lbr3.lbr_code = act.act_statut_code AND lbr3.lng_id = :langue ' .
			'LEFT JOIN lbr_libelles_referentiel AS "lbr4" ON lbr4.lbr_code = act.act_frequence_code AND lbr4.lng_id = :langue ' .
			'LEFT JOIN tsp_types_support AS "tsp" ON tsp.tsp_id = spp.tsp_id ' .
			'LEFT JOIN lbr_libelles_referentiel AS "lbr5" ON lbr5.lbr_code = tsp.tsp_code AND lbr5.lng_id = :langue ' .
			'LEFT JOIN idn_identites AS "idn" ON idn.idn_id = act.idn_id ' .
			'LEFT JOIN cvl_civilites AS "cvl" ON cvl.cvl_id = idn.cvl_id ' .
			'LEFT JOIN gsts_gst_tsp AS "gsts" ON gsts.tsp_id = tsp.tsp_id ' .
			'LEFT JOIN idgs_idn_gst AS "idgs" ON idgs.gst_id = gsts.gst_id ' .
			'LEFT JOIN spcr_spp_crs AS "spcr" ON spcr.spp_id = spp.spp_id ' .
			'WHERE mcr.mcr_id IS NOT NULL AND mcr.mcr_etat_code > 0 ' . $SQL_Chercher;

		if ( $crs_id != '*' ) {
			$sql .= 'AND spcr.crs_id = :crs_id ';
		}

		if ( ! $_SESSION['idn_super_admin'] ) {
			$sql .= 'AND idgs.idn_id = :idn_id ';
		}

		if ( $act_id != '' ) {
			$sql .= 'AND act.act_id = :act_id ';
		}

		switch( $trier ) {
		 default:
		 case 'mcr_libelle':
			$Order = 'ORDER BY mcr_libelle, mgr_libelle, act_priorite '; //pea_cotation ';
			break;
		 case 'mcr_libelle-desc':
			$Order = 'ORDER BY mcr_libelle DESC, mgr_libelle DESC, act_priorite DESC '; // pea_cotation DESC ';
			break;

		 case 'spp_nom':
			$Order = 'ORDER BY spp_nom ';
			break;
		 case 'spp_nom-desc':
			$Order = 'ORDER BY spp_nom DESC ';
			break;

		 case 'mcr_etat':
			$Order = 'ORDER BY mcr_etat_libelle ';
			break;
		 case 'mcr_etat-desc':
			$Order = 'ORDER BY mcr_etat_libelle DESC ';
			break;

		 case 'mcr_statut':
			$Order = 'ORDER BY mcr_statut_libelle ';
			break;
		 case 'mcr_statut-desc':
			$Order = 'ORDER BY mcr_statut_libelle DESC ';
			break;

		 case 'mcr_frequence':
			$Order = 'ORDER BY mcr_frequence_libelle ';
			break;
		 case 'mcr_frequence-desc':
			$Order = 'ORDER BY mcr_frequence_libelle DESC ';
			break;

		 case 'act_libelle':
			$Order = 'ORDER BY act_libelle ';
			break;
		 case 'act_libelle-desc':
			$Order = 'ORDER BY act_libelle DESC ';
			break;

		 case 'acteur':
			$Order = 'ORDER BY cvl_nom, cvl_prenom ';
			break;
		 case 'acteur-desc':
			$Order = 'ORDER BY cvl_nom DESC, cvl_prenom DESC ';
			break;

		 case 'act_description':
			$Order = 'ORDER BY act_description ';
			break;
		 case 'act_description-desc':
			$Order = 'ORDER BY act_description DESC ';
			break;

		 case 'act_date_debut_p':
			$Order = 'ORDER BY act_date_debut_p ';
			break;
		 case 'act_date_debut_p-desc':
			$Order = 'ORDER BY act_date_debut_p DESC ';
			break;

		 case 'act_date_fin_p':
			$Order = 'ORDER BY act_date_fin_p ';
			break;
		 case 'act_date_fin_p-desc':
			$Order = 'ORDER BY act_date_fin_p DESC ';
			break;

		 case 'act_date_debut_r':
			$Order = 'ORDER BY act_date_debut_r ';
			break;
		 case 'act_date_debut_r-desc':
			$Order = 'ORDER BY act_date_debut_r DESC ';
			break;

		 case 'act_date_fin_r':
			$Order = 'ORDER BY act_date_fin_r ';
			break;
		 case 'act_date_fin_r-desc':
			$Order = 'ORDER BY act_date_fin_r DESC ';
			break;

		 case 'act_priorite':
			$Order = 'ORDER BY act_priorite ';
			break;
		 case 'act_priorite-desc':
			$Order = 'ORDER BY act_priorite DESC ';
			break;
		}

		$sql .= $Order;
//print(str_replace(':langue', $langue, str_replace(':crs_id', $crs_id, $sql)));

		$requete = $this->prepareSQL($sql);

		if ( $crs_id != '*' ) {
			$this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE);
		}

		if ( ! $_SESSION['idn_super_admin'] ) {
			$this->bindSQL($requete, ':idn_id', $_SESSION['idn_id'], self::ID_TYPE);
		}

		if ( $act_id != '' ) {
			$this->bindSQL($requete, ':act_id', $act_id, self::ID_TYPE);
		}

		if ( $chercher != '' ) {
			$this->bindSQL($requete, ':chercher', '%'.$chercher.'%', PDO::PARAM_STR, 100);
		}

		return $this->bindSQL($requete, ':langue', $langue, self::LANGUE_TYPE, self::LANGUE_LENGTH)
			->executeSQL($requete)->fetchAll(PDO::FETCH_CLASS);
	}

	
	public function insererAction( $mcr_id, $act_libelle, $act_statut_code, $act_frequence_code, $act_date_debut_p, $act_date_fin_p,
		$act_priorite, $idn_id = NULL, $act_type_code = NULL, $acg_id = NULL, $act_description = NULL, $gst_id = NULL,
		$act_date_debut_r = NULL, $act_date_fin_r = NULL ) {
	/**
	* Insérer une Action
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-04-27
	*
	* @param[in] $mcr_id ID de la Mesure de rattachement
	* @param[in] $act_libelle Libellé de l'Action
	* @param[in] $act_statut_code Code du Statut de l'Action (Non fait | Fait | En cours | Supprimé)
	* @param[in] $act_frequence_code Code de la Fréquence de l'Action (Aucune | Quotidien | Hebdomadaire | Mensuel | Semestriel | Annuel)
	* @param[in] $act_date_debut_p Date de début prévisionnelle de l'Action
	* @param[in] $act_date_fin_p Date de fin prévisionnelle de l'Action
	* @param[in] $act_priorite Priorité de l'Action (pour gérer les priorités)
	* @param[in] $idn_id ID de l'Utilisateur qui a pris en charge l'Action
	* @param[in] $act_type_code [pas encore utilisé] Code du Type d'Action (Formation | Organisation | Investissement | ...)
	* @param[in] $acg_id ID de l'Action Générique de rattachement (quand elle existe)
	* @param[in] $act_description 
	* @param[in] $gst_id 
	* @param[in] $act_date_debut_r Date de début réelle de l'Action
	* @param[in] $act_date_fin_r Date de début réelle de l'Action
	*
	* @return Renvoi l'ID de l'Action créée ou lève une exception en cas d'erreur.
	*			
	*/

		$Sql = 'INSERT INTO act_actions (' .
			'mcr_id, act_libelle, act_statut_code, act_frequence_code, act_date_debut_p, act_date_fin_p, act_priorite';

		if ( $idn_id != NULL ) {
			$Sql .= ', idn_id';
		}

		if ( $act_type_code != NULL ) {
			$Sql .= ', act_type_code';
		}

		if ( $acg_id != NULL ) {
			$Sql .= ', acg_id';
		}

		if ( $act_description != NULL ) {
			$Sql .= ', act_description';
		}
		
		if ( $gst_id != NULL ) {
			$Sql .= ', gst_id';
		}

		if ( $act_date_debut_r != NULL ) {
			$Sql .= ', act_date_debut_r';
		}

		if ( $act_date_fin_r != NULL ) {
			$Sql .= ', act_date_fin_r';
		}

		$Sql .= ') VALUES (' .
			':mcr_id, :act_libelle, :act_statut_code, :act_frequence_code, :act_date_debut_p, :act_date_fin_p, :act_priorite';
		
		if ( $idn_id != NULL ) {
			$Sql .= ', :idn_id';
		}

		if ( $act_type_code != NULL ) {
			$Sql .= ', :act_type_code';
		}

		if ( $acg_id != NULL ) {
			$Sql .= ', :acg_id';
		}

		if ( $act_description != NULL ) {
			$Sql .= ', :act_description';
		}
		
		if ( $gst_id != NULL ) {
			$Sql .= ', :gst_id';
		}

		if ( $act_date_debut_r != NULL ) {
			$Sql .= ', :act_date_debut_r';
		}

		if ( $act_date_fin_r != NULL ) {
			$Sql .= ', :act_date_fin_r';
		}

		$Sql .= ')';

		$requete = $this->prepareSQL( $Sql );

		$this->bindSQL($requete, ':mcr_id', $mcr_id, self::ID_TYPE)
			->bindSQL($requete, ':act_libelle', $act_libelle, self::LIBELLE_TYPE, self::LIBELLE_LENGTH)
			->bindSQL($requete, ':act_statut_code', $act_statut_code, self::CODE_TYPE, self::CODE_LENGTH)
			->bindSQL($requete, ':act_frequence_code', $act_frequence_code, self::CODE_TYPE, self::CODE_LENGTH)
			->bindSQL($requete, ':act_date_debut_p', $act_date_debut_p, self::DATE_TYPE, self::DATE_LENGTH)
			->bindSQL($requete, ':act_date_fin_p', $act_date_fin_p, self::DATE_TYPE, self::DATE_LENGTH)
			->bindSQL($requete, ':act_priorite', $act_priorite, self::ID_TYPE);


		if ( $idn_id != NULL ) {
			$this->bindSQL($requete, ':idn_id', $idn_id, self::ID_TYPE);
		}

		if ( $act_type_code != NULL ) {
			$this->bindSQL($requete, ':act_type_code', $act_type_code, self::CODE_TYPE, self::CODE_LENGTH);
		}

		if ( $acg_id != NULL ) {
			$this->bindSQL($requete, ':acg_id', $acg_id, self::ID_TYPE);
		}

		if ( $act_description != NULL ) {
			$this->bindSQL($requete, ':act_description', $act_description, self::TEXTE_TYPE);
		}
		
		if ( $gst_id != NULL ) {
			$this->bindSQL($requete, ':gst_id', $gst_id, self::ID_TYPE);
		}

		if ( $act_date_debut_r != NULL ) {
			$this->bindSQL($requete, ':act_date_debut_r', $act_date_debut_r, self::DATE_TYPE, self::DATE_LENGTH);
		}

		if ( $act_date_fin_r != NULL ) {
			$this->bindSQL($requete, ':act_date_fin_r', $act_date_fin_r, self::DATE_TYPE, self::DATE_LENGTH);
		}


		$this->executeSQL($requete);

		switch( $this->getAttribute(PDO::ATTR_DRIVER_NAME) ) {
		 	default;
				$this->LastInsertId = $this->lastInsertId();
				break;

			case 'pgsql';
				$this->LastInsertId = $this->lastInsertId( 'act_actions_act_id_seq' );
				break;
		}	

		return $this->LastInsertId;
	}


	public function supprimerAction( $Id ) {
	/**
	* Supprimer une Action.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-04-27
	*
	* @param[in] $Id ID de l'Action à supprimer
	*
	* @return Renvoi "vrai" si l'occurrence a été supprimée, sinon, lève une exception. 
	*			
	*/
		$sql = 'DELETE FROM act_actions WHERE act_id = :act_id ';

		$requete = $this->prepareSQL($sql);

		$this->bindSQL($requete, ':act_id', $Id, self::ID_TYPE)
			->executeSQL($requete);

		return TRUE;
	}

	
	public function modifierAction( $act_id, $mcr_id, $act_libelle, $act_statut_code, $act_frequence_code, $act_date_debut_p, $act_date_fin_p,
		$act_priorite, $idn_id = NULL, $act_description = NULL,	$act_date_debut_r = NULL, $act_date_fin_r = NULL, $acg_id = NULL ) {
	/**
	* Modifier une Action
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-04-27
	*
	* @param[in] $act_id ID de l'Action à modifier
	* @param[in] $mcr_id ID de la Mesure de rattachement
	* @param[in] $act_libelle Libellé de l'Action
	* @param[in] $act_statut_code Code du Statut de l'Action (Non fait | Fait | En cours | Supprimé)
	* @param[in] $act_frequence_code Code de la Fréquence de l'Action (Aucune | Quotidien | Hebdomadaire | Mensuel | Semestriel | Annuel)
	* @param[in] $act_date_debut_p Date de début prévisionnelle de l'Action
	* @param[in] $act_date_fin_p Date de fin prévisionnelle de l'Action
	* @param[in] $act_priorite Poids de l'Action (pour gérer les priorités)
	* @param[in] $idn_id ID de l'Utilisateur qui a pris en charge l'Action
	* @param[in] $act_description Description ou commentaire sur l'action
	* @param[in] $act_date_debut_r Date de début réelle de l'Action
	* @param[in] $act_date_fin_r Date de début réelle de l'Action
	* @param[in] $acg_id ID de l'Action Générique de rattachement (quand elle existe)
	*
	* @return Renvoi l'ID de l'Actif Support créé ou lève une exception en cas d'erreur.
	*			
	*/

		$Sql = 'UPDATE act_actions SET ' .
			'mcr_id = :mcr_id, act_libelle = :act_libelle, act_statut_code = :act_statut_code, act_frequence_code = :act_frequence_code, ' .
			'act_date_debut_p = :act_date_debut_p, act_date_fin_p = :act_date_fin_p, act_priorite = :act_priorite';

		if ( $idn_id != NULL ) {
			$Sql .= ', idn_id = :idn_id';
		}

		if ( $acg_id != NULL ) {
			$Sql .= ', acg_id = :acg_id';
		}

		if ( $act_description != NULL ) {
			$Sql .= ', act_description = :act_description';
		}
		
		if ( $act_date_debut_r != NULL ) {
			$Sql .= ', act_date_debut_r = :act_date_debut_r';
		}

		if ( $act_date_fin_r != NULL ) {
			$Sql .= ', act_date_fin_r = :act_date_fin_r';
		}

		$Sql .= ' WHERE act_id = :act_id;';

		$requete = $this->prepareSQL( $Sql );

		$this->bindSQL($requete, ':mcr_id', $mcr_id, self::ID_TYPE)
			->bindSQL($requete, ':act_id', $act_id, self::ID_TYPE)
			->bindSQL($requete, ':act_libelle', $act_libelle, self::LIBELLE_TYPE, self::LIBELLE_LENGTH)
			->bindSQL($requete, ':act_statut_code', $act_statut_code, self::CODE_TYPE, self::CODE_LENGTH)
			->bindSQL($requete, ':act_frequence_code', $act_frequence_code, self::CODE_TYPE, self::CODE_LENGTH)
			->bindSQL($requete, ':act_date_debut_p', $act_date_debut_p, self::DATE_TYPE, self::DATE_LENGTH)
			->bindSQL($requete, ':act_date_fin_p', $act_date_fin_p, self::DATE_TYPE, self::DATE_LENGTH)
			->bindSQL($requete, ':act_priorite', $act_priorite, self::ID_TYPE);


		if ( $idn_id != NULL ) {
			$this->bindSQL($requete, ':idn_id', $idn_id, self::ID_TYPE);
		}

		if ( $acg_id != NULL ) {
			$this->bindSQL($requete, ':acg_id', $acg_id, self::ID_TYPE);
		}

		if ( $act_description != NULL ) {
			$this->bindSQL($requete, ':act_description', $act_description, self::TEXTE_TYPE);
		}

		if ( $act_date_debut_r != NULL ) {
			$this->bindSQL($requete, ':act_date_debut_r', $act_date_debut_r, self::DATE_TYPE, self::DATE_LENGTH);
		}

		if ( $act_date_fin_r != NULL ) {
			$this->bindSQL($requete, ':act_date_fin_r', $act_date_fin_r, self::DATE_TYPE, self::DATE_LENGTH);
		}


		$this->executeSQL($requete);

		return TRUE;
	}


	public function modifierChamp(	$Id, $Source, $Valeur ) {
	/**
	* Modifier une valeur d'une Action.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-04-27
	*
	* @param[in] $Id ID de l'Action
	* @param[in] $Source Colonne de la table que l'on va modifier
	* @param[in] $Valeur Valeur que l'on associe à la Colonne de la table
	*
	* @return Renvoi "vrai" si l'occurrence a été modifiée, sinon, lève une exception. 
	*			
	*/
		$sql = 'UPDATE act_actions SET ' . $Source . ' = :valeur ' .
			'WHERE act_id = :act_id ';

		$requete = $this->prepareSQL($sql);


		switch( $Source ) {
		 case 'mcr_id':
		 case 'idn_id':
		 case 'acg_id':
		 case 'gst_id':
		 case 'act_priorite':
			$this->bindSQL($requete, ':valeur', $Valeur, self::ID_TYPE);
			break;

		 case 'act_statut_code':
		 case 'act_frequence_code':
		 case 'act_type_code':
			$this->bindSQL($requete, ':valeur', $Valeur, self::CODE_TYPE, self::CODE_LENGTH);
			break;

		 case 'act_date_debut_p':
		 case 'act_date_fin_p':
		 case 'act_date_debut_r':
		 case 'act_date_fin_r':
			$this->bindSQL($requete, ':valeur', $Valeur, self::DATE_TYPE, self::DATE_LENGTH);
			break;

		 case 'act_libelle':
			$this->bindSQL($requete, ':valeur', $Valeur, self::LIBELLE_TYPE, self::LIBELLE_LENGTH);
			break;

		 case 'act_description':
			$this->bindSQL($requete, ':valeur', $Valeur, self::TEXTE_TYPE);
			break;
		}


		$this->bindSQL($requete, ':act_id', $Id, self::ID_TYPE)
			->executeSQL($requete);

		return TRUE;
	}


	public function totalActions() {
	/**
	* Total des Actions.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-04-27
	*
	* @return Renvoi les occurrences qui ont été trouvées, sinon, lève une exception. 
	*
	*/
		$sql = 'SELECT COUNT(act_id) as "total" ' .
			'FROM act_actions ';

		$requete = $this->prepareSQL($sql);

		return $this->executeSQL($requete)->fetchObject()->total;
	}


	public function listerMesures( $crs_id = '*', $langue = 'fr' ) {
	/**
	* Lister les Mesures qui sont associées à cette cartographie (si elle est précisée) et à l'Utilisateur en fonction de son appartenance.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-05-01
	*
	* @param[in] $crs_id ID. de la Cartographie qui a été sélectionnée
	* @param[in] $Langue Langue pour afficher les libellés
	*
	* @return Renvoi les occurrences qui ont été trouvées, sinon, lève une exception. 
	*
	*/
		$sql = 'SELECT ' .
			'DISTINCT mcr.mcr_id, mcr.mcr_libelle, ' .
			'spp.spp_nom, spp.spp_id, ' .
			'lbr1.lbr_libelle AS "mgr_libelle", ' .
			'lbr2.lbr_libelle AS "mcr_etat_libelle", ' .
			'lbr5.lbr_libelle AS "tsp_libelle", ' .
			'pea_cotation ' .
			'FROM apr_actifs_primordiaux AS "apr" ' .
			'LEFT JOIN apsp_apr_spp AS "apsp" ON apsp.apr_id = apr.apr_id AND apsp.spp_id IS NOT NULL ' .
			'LEFT JOIN spp_supports AS "spp" ON spp.spp_id = apsp.spp_id ' .
			'LEFT JOIN rcs_risques_cartographies AS "rcs" ON rcs.spp_id = spp.spp_id ' .
			'LEFT JOIN pea_poids_evaluation_actifs as "pea" ON pea.pea_id = rcs.rcs_cotation_actif ' .
			'LEFT JOIN mcr_mesures_cartographies AS "mcr" ON mcr.rcs_id = rcs.rcs_id ' .
			'LEFT JOIN mgr_mesures_generiques AS "mgr" ON mgr.mgr_id = mcr.mgr_id ' .
			'LEFT JOIN lbr_libelles_referentiel AS "lbr1" ON lbr1.lbr_code = mgr.mgr_code AND lbr1.lng_id = :langue ' .
			'LEFT JOIN lbr_libelles_referentiel AS "lbr2" ON lbr2.lbr_code = \'MCR_ETAT_\'||mcr.mcr_etat_code AND lbr2.lng_id = :langue ' .
//			'LEFT JOIN act_actions AS "act" ON act.mcr_id = mcr.mcr_id ' .
//			'LEFT JOIN lbr_libelles_referentiel AS "lbr3" ON lbr3.lbr_code = act.act_statut_code AND lbr3.lng_id = :langue ' .
//			'LEFT JOIN lbr_libelles_referentiel AS "lbr4" ON lbr4.lbr_code = act.act_frequence_code AND lbr4.lng_id = :langue ' .
//			'LEFT JOIN gst_gestionnaires AS "gst" ON gst.gst_id = act.gst_id ' .
			'LEFT JOIN tsp_types_support AS "tsp" ON tsp.tsp_id = spp.tsp_id ' .
			'LEFT JOIN lbr_libelles_referentiel AS "lbr5" ON lbr5.lbr_code = tsp.tsp_code AND lbr5.lng_id = :langue ' .
//			'LEFT JOIN idn_identites AS "idn" ON idn.idn_id = act.idn_id ' .
//			'LEFT JOIN cvl_civilites AS "cvl" ON cvl.cvl_id = idn.cvl_id ' .
			'LEFT JOIN gsts_gst_tsp AS "gsts" ON gsts.tsp_id = tsp.tsp_id ' .
			'LEFT JOIN idgs_idn_gst AS "idgs" ON idgs.gst_id = gsts.gst_id ' .
			'WHERE mcr.mcr_id IS NOT NULL AND mcr.mcr_etat_code > 0 ';

		if ( $crs_id != '*' ) {
			$sql .= 'AND apr.crs_id = :crs_id ';
		}

		if ( ! $_SESSION['idn_super_admin'] ) {
			$sql .= 'AND idgs.idn_id = :idn_id ';
		}

		$requete = $this->prepareSQL($sql);

		if ( $crs_id != '*' ) {
			$this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE);
		}

		if ( ! $_SESSION['idn_super_admin'] ) {
			$this->bindSQL($requete, ':idn_id', $_SESSION['idn_id'], self::ID_TYPE);
		}

		return $this->bindSQL($requete, ':langue', $langue, self::LANGUE_TYPE, self::LANGUE_LENGTH)
			->executeSQL($requete)->fetchAll(PDO::FETCH_CLASS);
	}


	public function recupererLibellesMesure( $mcr_id, $langue = 'fr' ) {
	/**
	* Lister les Libellés d'une Mesure.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-05-04
	*
	* @param[in] $mcr_id ID. de la Mesure recherchée
	* @param[in] $Langue Langue pour afficher les libellés
	*
	* @return Renvoi l'occurrence qui a été trouvée, sinon, lève une exception. 
	*
	*/
		$sql = 'SELECT ' .
			'DISTINCT mcr.mcr_id, mcr.mcr_libelle, ' .
			'spp.spp_nom, mcr.mgr_id, ' .
			'lbr1.lbr_libelle AS "mgr_libelle", ' .
			'lbr2.lbr_libelle AS "mcr_etat_libelle", ' .
			'lbr5.lbr_libelle AS "tsp_libelle", ' .
			'pea_cotation ' .
			'FROM mcr_mesures_cartographies AS "mcr" ' .
			'LEFT JOIN rcs_risques_cartographies AS "rcs" ON rcs.rcs_id = mcr.rcs_id ' .
			'LEFT JOIN spp_supports AS "spp" ON spp.spp_id = rcs.spp_id ' .
			'LEFT JOIN pea_poids_evaluation_actifs as "pea" ON pea.pea_id = rcs.rcs_cotation_actif ' .
			'LEFT JOIN mgr_mesures_generiques AS "mgr" ON mgr.mgr_id = mcr.mgr_id ' .
			'LEFT JOIN lbr_libelles_referentiel AS "lbr1" ON lbr1.lbr_code = mgr.mgr_code AND lbr1.lng_id = :langue ' .
			'LEFT JOIN lbr_libelles_referentiel AS "lbr2" ON lbr2.lbr_code = \'MCR_ETAT_\'||mcr.mcr_etat_code AND lbr2.lng_id = :langue ' .
			'LEFT JOIN tsp_types_support AS "tsp" ON tsp.tsp_id = spp.tsp_id ' .
			'LEFT JOIN lbr_libelles_referentiel AS "lbr5" ON lbr5.lbr_code = tsp.tsp_code AND lbr5.lng_id = :langue ' .
			'LEFT JOIN gsts_gst_tsp AS "gsts" ON gsts.tsp_id = tsp.tsp_id ' .
			'LEFT JOIN idgs_idn_gst AS "idgs" ON idgs.gst_id = gsts.gst_id ' .
			'WHERE mcr.mcr_id = :mcr_id AND mcr.mcr_etat_code > 0 ';

		if ( ! $_SESSION['idn_super_admin'] ) {
			$sql .= 'AND idgs.idn_id = :idn_id ';
		}

		$requete = $this->prepareSQL($sql);

		$this->bindSQL($requete, ':mcr_id', $mcr_id, self::ID_TYPE);

		if ( ! $_SESSION['idn_super_admin'] ) {
			$this->bindSQL($requete, ':idn_id', $_SESSION['idn_id'], self::ID_TYPE);
		}

		return $this->bindSQL($requete, ':langue', $langue, self::LANGUE_TYPE, self::LANGUE_LENGTH)
			->executeSQL($requete)->fetchObject();
	}


	public function listerStatutsAction( $langue = 'fr' ) {
	/**
	* Lister les Statuts qui peuvent être associés à une Action.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-05-01
	*
	* @param[in] $Langue Langue pour afficher les libellés
	*
	* @return Renvoi les occurrences qui ont été trouvées, sinon, lève une exception. 
	*
	*/
		$sql = 'SELECT ' .
			'lbr_code, lbr_libelle ' .
			'FROM lbr_libelles_referentiel ' .
			'WHERE lbr_code like \'ACT_STATUT_%\' AND lng_id = :langue ' .
			'ORDER BY lbr_code ';

		$requete = $this->prepareSQL($sql);

		return $this->bindSQL($requete, ':langue', $langue, self::LANGUE_TYPE, self::LANGUE_LENGTH)
			->executeSQL($requete)->fetchAll(PDO::FETCH_CLASS);
	}


	public function listerFrequencesAction( $langue = 'fr' ) {
	/**
	* Lister les Fréquences qui peuvent être associées à une Action.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-05-01
	*
	* @param[in] $Langue Langue pour afficher les libellés
	*
	* @return Renvoi les occurrences qui ont été trouvées, sinon, lève une exception. 
	*
	*/
		$sql = 'SELECT ' .
			'lbr_code, lbr_libelle ' .
			'FROM lbr_libelles_referentiel ' .
			'WHERE lbr_code like \'ACT_FREQUENCE_%\' AND lng_id = :langue ' .
			'ORDER BY lbr_code ';

		$requete = $this->prepareSQL($sql);

		return $this->bindSQL($requete, ':langue', $langue, self::LANGUE_TYPE, self::LANGUE_LENGTH)
			->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);
	}


	public function listerUtilisateursMesure( $mcr_id ) {
	/**
	* Lister les utilisateurs gestionnaires de ce type d'actif support et autorisé à travailler sur cette mesure.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @version 1.0
	* @date 2017-05-08
	*
	* @param[in] $mcr_id ID. de la Mesure
	*
	* @return Renvoi les occurrences correspondantes ou lève une exception en cas d'erreur.
	*			
	*/
		$sql = 'SELECT mcr.mcr_id, idn.idn_id, idn.idn_login, cvl.cvl_prenom, cvl.cvl_nom 
FROM mcr_mesures_cartographies AS "mcr" 
LEFT JOIN rcs_risques_cartographies AS "rcs" ON rcs.rcs_id = mcr.rcs_id 
LEFT JOIN spp_supports AS "spp" ON spp.spp_id = rcs.spp_id 
LEFT JOIN spcr_spp_crs AS "spcr" ON spcr.spp_id = spp.spp_id 
LEFT JOIN crs_cartographies_risques "crs" ON crs.crs_id = spcr.crs_id 
LEFT JOIN gsts_gst_tsp AS "gsts" ON gsts.tsp_id = spp.tsp_id 
LEFT JOIN idgs_idn_gst AS "idgs" ON idgs.gst_id = gsts.gst_id 
LEFT JOIN idn_identites AS "idn" ON idn.idn_id = idgs.idn_id 
LEFT JOIN cvl_civilites AS "cvl" ON cvl.cvl_id = idn.cvl_id 
LEFT JOIN iden_idn_ent AS "iden" ON iden.idn_id = idgs.idn_id AND iden.ent_id = crs.ent_id 
WHERE mcr.mcr_id = :mcr_id ';

		$requete = $this->prepareSQL($sql);

		return $this->bindSQL($requete, ':mcr_id', $mcr_id, self::ID_TYPE)
			->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);
	}


	public function listerPreuves( $act_id ) {
	/**
	* Lister les Preuves associées à une Action.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-05-13
	*
	* @param[in] $act_id ID. de l'Action de référence
	*
	* @return Renvoi les occurrences correspondantes ou lève une exception en cas d'erreur.
	*			
	*/
		$sql = 'SELECT prv_id, prv_libelle, prv_localisation, prv_date_creation
FROM prv_preuves AS "prv" 
WHERE prv.act_id = :act_id
ORDER BY prv_libelle ';

		$requete = $this->prepareSQL($sql);

		return $this->bindSQL($requete, ':act_id', $act_id, self::ID_TYPE)
			->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);
	}


	public function insererPreuve( $act_id, $prv_libelle, $prv_localisation ) {
	/**
	* Insère une nouvelle Preuve pour une Action.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-05-13
	*
	* @param[in] $act_id ID. de l'Action de rattachement
	* @param[in] $prv_libelle Libellé de la Preuve
	* @param[in] $prv_localisation Localisation de la Preuve
	*
	* @return Renvoi l'ID de la nouvelle occurrence qui a été crée ou lève une exception en cas d'erreur.
	*			
	*/
		$sql = 'INSERT INTO prv_preuves 
(act_id, prv_libelle, prv_localisation, prv_date_creation) VALUES
(:act_id, :prv_libelle, :prv_localisation, :prv_date_creation) ';

		$requete = $this->prepareSQL($sql);

		$this->bindSQL($requete, ':act_id', $act_id, self::ID_TYPE)
			->bindSQL($requete, ':prv_libelle', $prv_libelle, self::LIBELLE_TYPE, self::LIBELLE_LENGTH)
			->bindSQL($requete, ':prv_localisation', $prv_localisation, self::LOCALISATION_TYPE, self::LOCALISATION_LENGTH)
			->bindSQL($requete, ':prv_date_creation', date('Y-m-d'), self::DATE_TYPE, self::DATE_LENGTH)
			->executeSQL($requete);

		switch( $this->getAttribute(PDO::ATTR_DRIVER_NAME) ) {
		 	default;
				$this->LastInsertId = $this->lastInsertId();
				break;

			case 'pgsql';
				$this->LastInsertId = $this->lastInsertId( 'prv_preuves_prv_id_seq' );
				break;
		}	

		return $this->LastInsertId;
	}


	public function supprimerPreuve( $prv_id ) {
	/**
	* Lister les Preuves associées à une Action.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-05-13
	*
	* @param[in] $prv_id ID. de la Preuve à supprimer
	*
	* @return Renvoi "vrai" si l'occurrence a été supprimée ou lève une exception en cas d'erreur.
	*			
	*/
		$sql = 'DELETE FROM prv_preuves WHERE prv_id = :prv_id ';

		$requete = $this->prepareSQL($sql);

		$this->bindSQL($requete, ':prv_id', $prv_id, self::ID_TYPE)
			->executeSQL($requete);

		return TRUE;
	}


	public function telechargerFichier( $NomFichier ) {
	/**
	* Télécharge la preuve.
	*
	* @author Pierre-Luc MARY
	* @date 2017-05-14
	*
	* @return Renvoi vrai si le fichier a bien été créé.
	*			
	*/
		$NomCompletFichier = DIR_PREUVES . DIRECTORY_SEPARATOR . $NomFichier;

		if ( file_exists( $NomCompletFichier ) ) {
			header( 'Content-Description: File Transfer' );
			header( 'Content-Type: application/octet-stream' );
			header( 'Content-Disposition: attachment; filename="' . $NomFichier . '"' );
			header( 'Content-Transfer-Encoding: binary' );
			header( 'Expires: 0' );
			header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
			header( 'Pragma: public' );
			header( 'Content-Length: ' . filesize( $NomCompletFichier ) );
			ob_clean();
			flush();
			readfile( $NomCompletFichier );
		} else {
//			header( 'Location: Loxense-Actions.php?PasDeFichier&crs_id='.$crs_id.'&NomFichier='.urlencode($NomFichier) );
			exit( FALSE );
		}

		exit( TRUE );
	}

} // Fin class Actions


?>