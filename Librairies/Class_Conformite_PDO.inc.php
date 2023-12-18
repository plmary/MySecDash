<?php

include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Securite.inc.php' );


class Conformites extends HBL_Securite {
/**
* Cette classe gère les Actifs Supports.
*
* PHP version 7
* @license Loxense
* @author Pierre-Luc MARY
* @version 1.0
*/ 

	const ID_TYPE = PDO::PARAM_INT;

	const CODE_TYPE = PDO::PARAM_STR;
	const CODE_LENGTH = 6;

	const NOM_TYPE = PDO::PARAM_STR;
	const NOM_LENGTH = 100;

	const LANGUE_TYPE = PDO::PARAM_STR;
	const LANGUE_LENGTH = 2;

	const LIBELLE_TYPE = PDO::PARAM_STR;
	const LIBELLE_LENGTH = 100;
	
	const DESCRIPTION_TYPE = PDO::PARAM_LOB;

	const STATUT_CODE_TYPE = PDO::PARAM_STR;
	const STATUT_CODE_LENGTH = 20;
	
	const FREQUENCE_CODE_TYPE = PDO::PARAM_STR;
	const FREQUENCE_CODE_LENGTH = 20;
	
	const DATE_TYPE = PDO::PARAM_STR;
	const DATE_LENGTH = 10; // AAAA-MM-JJ

	const PRIORITE_TYPE = PDO::PARAM_INT;
	
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
	** Gestion de la Conformité
	*/

	
	public function copierMesuresConformiteCartographie( $crs_id, $rfc_id ) {
		/**
		 * Copier les Mesures de Conformité pour les associer à cette Cartographie
		 *
		 * @license Loxense
		 * @author Pierre-Luc MARY
		 * @version 1.0
		 * @date 2019-08-11
		 *
		 * @param[in] $crs_id ID de la Cartographie
		 * @param[in] $rfc_id ID du Référentiele
		 *
		 * @return Renvoi TRUE si les Mesures de conformité ont été copiées ou lève une exception en cas d'erreur
		 *
		 */
		
		$Sql = 'SELECT msr_code, msr_type FROM msr_mesures_referentiel WHERE rfc_id = :rfc_id ';
		
		$requete = $this->prepareSQL($Sql);
		
		$Mesures_Conformite = $this->bindSQL($requete, ':rfc_id', $rfc_id, self::ID_TYPE)
			->executeSQL($requete)->fetchAll(PDO::FETCH_CLASS);
		

		$this->begin_Transaction();
			
		foreach( $Mesures_Conformite as $Mesure_Conformite ) {
			$Sql = 'INSERT INTO cnf_conformite ' .
				'(crs_id, rfc_id, cnf_code, cnf_type, cnf_etat_code, cnf_description) ' .
				'VALUES (:crs_id, :rfc_id, :cnf_code, :cnf_type, 1, \'\') ';
			
			$requete = $this->prepareSQL($Sql);
			
			$this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
				->bindSQL($requete, ':rfc_id', $rfc_id, self::ID_TYPE)
				->bindSQL($requete, ':cnf_code', $Mesure_Conformite->msr_code, self::CODE_TYPE, self::CODE_LENGTH)
				->bindSQL($requete, ':cnf_type', $Mesure_Conformite->msr_type, self::ID_TYPE)
				->executeSQL($requete);			
		}

		$this->commit_Transaction();
		
		return TRUE;
	}
	
	
	public function supprimerMesuresConformiteCartographie( $crs_id, $rfc_id ) {
		/**
		 * Supprimer les Mesures de Conformité pour les dissocier de cette Cartographie
		 *
		 * @license Loxense
		 * @author Pierre-Luc MARY
		 * @version 1.0
		 * @date 2019-08-11
		 *
		 * @param[in] $crs_id ID de la Cartographie
		 * @param[in] $rfc_id ID du Référentiele
		 *
		 * @return Renvoi TRUE si les Mesures de conformité ont été supprimées ou lève une exception en cas d'erreur
		 *
		 */

		$Sql = 'DELETE FROM crrf_crs_rfc WHERE crs_id = :crs_id AND rfc_id = :rfc_id '; 

		$requete = $this->prepareSQL($Sql);
				
		$this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->bindSQL($requete, ':rfc_id', $rfc_id, self::ID_TYPE)
			->executeSQL($requete);
		
		return TRUE;
	}
	
	
	public function listerConformiteCartographie( $crs_id, $trier='code', $langue='fr', $chercher = '' ) {
	/**
	* Lister les Mesures de Conformité liées à une Cartographie
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @version 1.0
	* @date 2019-08-10
	*
	* @param[in] $crs_id ID de la cartographie
	* @param[in] $trier Indique la colonne sur laquelle on trie le résultat
	* @param[in] $langue ID de la langue pour afficher les libellés
	* @param[in] $chercher Chaine de recherche dans le résultat
	*
	* @return Renvoi la liste des mesures de conformité ou lève une exception en cas d'erreur 
	*			
	*/
		if ( $chercher != '' ) {
			$SQL_Chercher = 'AND (LOWER(rfc_code) like :chercher OR LOWER(lbr1.lbr_libelle) like :chercher OR LOWER(lbr3.lbr_libelle) like :chercher) ';
		} else {
			$SQL_Chercher = '';
		}

		$sql = 'SELECT cnf.cnf_id, cnf.cnf_code, cnf.cnf_type, cnf.rfc_id,
lbr1.lbr_libelle AS "libelle_code", lbr2.lbr_libelle AS "libelle_etat",
rfc.rfc_version, rfc.rfc_code, lbr3.lbr_libelle AS "libelle_referentiel"
FROM crrf_crs_rfc AS "crrf"
LEFT JOIN cnf_conformite AS "cnf" ON cnf.rfc_id = crrf.rfc_id AND cnf.crs_id = crrf.crs_id 
LEFT JOIN rfc_referentiels_conformite AS "rfc" ON rfc.rfc_id = crrf.rfc_id
LEFT JOIN lbr_libelles_referentiel AS "lbr1" ON lbr1.lbr_code = CONCAT(\'RFC-\',cnf.rfc_id,\'-\',cnf.cnf_code) AND lbr1.lng_id = :langue
LEFT JOIN lbr_libelles_referentiel AS "lbr2" ON lbr2.lbr_code = CONCAT(\'MCR_ETAT_\',cnf.cnf_etat_code) AND lbr2.lng_id = :langue
LEFT JOIN lbr_libelles_referentiel AS "lbr3" ON lbr3.lbr_code = CONCAT(\'RFC-\',cnf.rfc_id) AND lbr3.lng_id = :langue
WHERE crrf.crs_id = :crs_id AND cnf_type = 1 ' . $SQL_Chercher . ' ';

		switch( $trier ) {
		 default:
		 case 'referentiel':
		 	$Order = 'ORDER BY rfc_code, rfc_version, lbr3.lbr_libelle, lbr1.lbr_libelle ';
		 	break;
		 case 'referentiel-desc':
		 	$Order = 'ORDER BY rfc_code DESC, rfc_version DESC, lbr3.lbr_libelle DESC, lbr1.lbr_libelle DESC ';
		 	break;
		 	
		 case 'code':
			$Order = 'ORDER BY length(substring(cnf_code FROM \'[0-9]+\')), cnf_code ';
			break;
		 case 'code-desc':
			$Order = 'ORDER BY length(substring(cnf_code FROM \'[0-9]+\')), cnf_code DESC ';
			break;

		 case 'libelle':
			$Order = 'ORDER BY lbr1.lbr_libelle ';
			break;
		 case 'libelle-desc':
			$Order = 'ORDER BY lbr1.lbr_libelle DESC ';
			break;

		 case 'etat':
			$Order = 'ORDER BY lbr2.lbr_libelle ';
			break;
		 case 'etat-desc':
			$Order = 'ORDER BY lbr2.lbr_libelle DESC ';
			break;
		}

		$sql .= $Order;

		$requete = $this->prepareSQL($sql);

		if ( $chercher != '' ) {
			$this->bindSQL($requete, ':chercher', '%'.strtolower($chercher).'%', PDO::PARAM_STR, 100);
		}

		return $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->bindSQL($requete, ':langue', $langue, self::LANGUE_TYPE, self::LANGUE_LENGTH)
			->executeSQL($requete)->fetchAll(PDO::FETCH_CLASS);
	}
	
	
	public function listerRegroupementConformiteCartographie( $crs_id, $lng_id ) {
		/**
		 * Lister les Regroupement de Mesures de Conformité liées à une Cartographie
		 *
		 * @license Loxense
		 * @author Pierre-Luc MARY
		 * @version 1.0
		 * @date 2019-08-10
		 *
		 * @param[in] $crs_id ID de la cartographie
		 * @param[in] $lng_id ID de la langue pour afficher les libellés
		 *
		 * @return Renvoi la liste des regroupements de mesures de conformité ou lève une exception en cas d'erreur
		 *
		 */
		
		$sql = 'SELECT cnf.cnf_code, cnf.rfc_id,
lbr1.lbr_libelle AS "libelle_code", rfc.rfc_version, rfc.rfc_code, lbr2.lbr_libelle AS "libelle_referentiel"
FROM crrf_crs_rfc AS "crrf"
LEFT JOIN cnf_conformite AS "cnf" ON cnf.rfc_id = crrf.rfc_id AND cnf.crs_id = crrf.crs_id
LEFT JOIN rfc_referentiels_conformite AS "rfc" ON rfc.rfc_id = crrf.rfc_id
LEFT JOIN lbr_libelles_referentiel AS "lbr1" ON lbr1.lbr_code = CONCAT(\'RFC-\',cnf.rfc_id,\'-\',cnf.cnf_code) AND lbr1.lng_id = :lng_id
LEFT JOIN lbr_libelles_referentiel AS "lbr2" ON lbr2.lbr_code = CONCAT(\'RFC-\',cnf.rfc_id) AND lbr2.lng_id = :lng_id
WHERE crrf.crs_id = :crs_id AND cnf_type = 2 
ORDER BY lbr2.lbr_libelle, rfc_version, length(substring(cnf_code FROM \'[0-9]+\')), cnf_code ';
		
		$requete = $this->prepareSQL($sql);
		
		$Liste_Libelles = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->bindSQL($requete, ':lng_id', $lng_id, self::LANGUE_TYPE, self::LANGUE_LENGTH)
			->executeSQL($requete)->fetchAll(PDO::FETCH_CLASS);

		$Libelles = [];

		foreach( $Liste_Libelles as $Libelle ) {
			$Libelles[$Libelle->rfc_id.'-'.$Libelle->cnf_code] = $Libelle->libelle_code;
		}
		
		return $Libelles;
	}

	
	public function rechargerMesuresConformiteCartographie( $crs_id ) {
		/**
		 * Recharger les Mesures de Conformité de cette Cartographie
		 *
		 * @license Loxense
		 * @author Pierre-Luc MARY
		 * @version 1.0
		 * @date 2019-08-11
		 *
		 * @param[in] $crs_id ID de la Cartographie
		 * @param[in] $rfc_id ID du Référentiele
		 *
		 * @return Renvoi TRUE si les Mesures de conformité ont été rechargées ou lève une exception en cas d'erreur
		 *
		 */

		// Récupère tous les référentiels associés à la cartographie.
		$Sql = 'SELECT rfc_id FROM crrf_crs_rfc WHERE crs_id = :crs_id ';
		
		$Requete = $this->prepareSQL($Sql);
		
		$Referentiels = $this->bindSQL($Requete, ':crs_id', $crs_id, self::ID_TYPE)
		->executeSQL($Requete)->fetchAll(PDO::FETCH_CLASS);
		
		foreach( $Referentiels as $Referentiel ) {
			// Supprime les enregistrements dans "Conformité" qui ne sont plus dans le référentiel.
			$Sql = 'SELECT cnf_code,cnf_type FROM cnf_conformite WHERE rfc_id = :rfc_id AND crs_id = :crs_id EXCEPT
SELECT msr_code,msr_type FROM msr_mesures_referentiel WHERE rfc_id = :rfc_id ';
			
			$Requete = $this->prepareSQL($Sql);
			
			$Mesures = $this->bindSQL($Requete, ':rfc_id', $Referentiel->rfc_id, self::ID_TYPE)
			->bindSQL($Requete, ':crs_id', $crs_id, self::ID_TYPE)
			->executeSQL($Requete)->fetchAll(PDO::FETCH_CLASS);
			
			foreach( $Mesures as $Mesure ) {
				$Sql = 'DELETE FROM cnf_conformite WHERE rfc_id = :rfc_id AND crs_id = :crs_id AND cnf_code = :cnf_code AND cnf_type = :cnf_type ';
				
				$Requete = $this->prepareSQL( $Sql );
				
				$this->bindSQL($Requete, ':rfc_id', $Referentiel->rfc_id, self::ID_TYPE)
				->bindSQL($Requete, ':crs_id', $crs_id, self::ID_TYPE)
				->bindSQL($Requete, ':cnf_code', $Mesure->cnf_code, self::STATUT_CODE_TYPE, self::STATUT_CODE_LENGTH)
				->bindSQL($Requete, ':cnf_type', $Mesure->cnf_type, self::ID_TYPE)
				->executeSQL($Requete);
			}
			
			
			// Ajoute les enregistrements dans "Conformité" qui sont dans le référentiel.
			$Sql = 'SELECT msr_code,msr_type FROM msr_mesures_referentiel WHERE rfc_id = :rfc_id EXCEPT
SELECT cnf_code,cnf_type FROM cnf_conformite WHERE rfc_id = :rfc_id AND crs_id = :crs_id ';
			
			$Requete = $this->prepareSQL($Sql);
			
			$Mesures = $this->bindSQL($Requete, ':rfc_id', $Referentiel->rfc_id, self::ID_TYPE)
			->bindSQL($Requete, ':crs_id', $crs_id, self::ID_TYPE)
			->executeSQL($Requete)->fetchAll(PDO::FETCH_CLASS);
			
			foreach( $Mesures as $Mesure ) {
				$Sql = 'INSERT INTO cnf_conformite (rfc_id, crs_id, cnf_code, cnf_type, cnf_etat_code)
VALUES (:rfc_id, :crs_id, :cnf_code, :cnf_type, 1) ';
				
				$Requete = $this->prepareSQL( $Sql );
				
				$this->bindSQL($Requete, ':rfc_id', $Referentiel->rfc_id, self::ID_TYPE)
				->bindSQL($Requete, ':crs_id', $crs_id, self::ID_TYPE)
				->bindSQL($Requete, ':cnf_code', $Mesure->msr_code, self::STATUT_CODE_TYPE, self::STATUT_CODE_LENGTH)
				->bindSQL($Requete, ':cnf_type', $Mesure->msr_type, self::ID_TYPE)
				->executeSQL($Requete);
			}
		}
		
		return TRUE;
	}

	
	public function listerActionsMesureConformite( $cnf_id ) {
		/**
		 * Lister les Actions rattachées à une Mesure de Conformité
		 *
		 * @license Loxense
		 * @author Pierre-Luc MARY
		 * @version 1.0
		 * @date 2019-08-11
		 *
		 * @param[in] $cnf_id ID de la Mesure de Conformité
		 *
		 * @return Renvoi la liste des actions ou lève une exception en cas d'erreur
		 *
		 */
		
		$Sql = 'SELECT cnf_etat_code, cnf_description
FROM cnf_conformite AS "cnf"
WHERE cnf.cnf_id = :cnf_id ';
		
		$requete = $this->prepareSQL($Sql);
		
		$Infos_Mesure = $this->bindSQL($requete, ':cnf_id', $cnf_id, self::ID_TYPE)
		->executeSQL($requete)->fetchObject();


		$Sql = 'SELECT amc.amc_id, amc.idn_id, cvl_prenom, cvl_nom, amc_libelle, amc_description, lbr_libelle AS "amc_statut_code", amc_frequence_code,
amc_date_debut_p, amc_date_fin_p, amc_date_debut_r, amc_date_fin_r, amc_priorite
FROM amc_actions_mesure_conformite AS "amc"
LEFT JOIN idn_identites AS "idn" ON idn.idn_id = amc.idn_id
LEFT JOIN cvl_civilites AS "cvl" ON cvl.cvl_id = idn.cvl_id
LEFT JOIN lbr_libelles_referentiel AS "lbr" ON lbr.lbr_code = amc_statut_code AND lng_id = :lng_id
WHERE amc.cnf_id = :cnf_id 
ORDER BY amc_date_debut_r, amc_date_debut_p, amc_libelle ';
		
		$requete = $this->prepareSQL($Sql);
		
		$Liste_Actions = $this->bindSQL($requete, ':cnf_id', $cnf_id, self::ID_TYPE)
			->bindSQL($requete, ':lng_id', $_SESSION['Language'], self::LANGUE_TYPE, self::LANGUE_LENGTH)
			->executeSQL($requete)->fetchAll(PDO::FETCH_CLASS);
		
		return [$Infos_Mesure, $Liste_Actions];
	}


	public function listerStatutsMesure( $lng_id ) {
		/**
		 * Lister les Statuts possibles d'une Mesure
		 *
		 * @license Loxense
		 * @author Pierre-Luc MARY
		 * @version 1.0
		 * @date 2019-08-11
		 *
		 * @param[in] $lng_id ID de la Langue du libellé de la Mesure de Conformité
		 *
		 * @return Renvoi la liste des statuts d'une mesure ou lève une exception en cas d'erreur
		 *
		 */
		
		$Sql = 'SELECT lbr_code, lbr_libelle FROM lbr_libelles_referentiel WHERE lbr_code like \'MCR_ETAT_%\'
AND lng_id = :lng_id ';
		
		$requete = $this->prepareSQL($Sql);
		
		return $this->bindSQL($requete, ':lng_id', $lng_id, self::LANGUE_TYPE, self::LANGUE_LENGTH)
			->executeSQL($requete)->fetchAll(PDO::FETCH_CLASS);
	}
	
	
	public function listerActeursAction() {
		/**
		 * Lister les Acteurs (Identités et Civilités) possibles à associer à une Action
		 *
		 * @license Loxense
		 * @author Pierre-Luc MARY
		 * @version 1.0
		 * @date 2019-08-11
		 *
		 * @return Renvoi la liste des utilisateurs ou lève une exception en cas d'erreur
		 *
		 */
		
		$Sql = 'SELECT idn_id, cvl_prenom, cvl_nom 
FROM idn_identites AS "idn"
LEFT JOIN cvl_civilites AS "cvl" ON cvl.cvl_id = idn.cvl_id ';
		
		$requete = $this->prepareSQL($Sql);
		
		return $this->executeSQL($requete)->fetchAll(PDO::FETCH_CLASS);
	}
	
	
	public function listerStatutsAction( $lng_id ) {
		/**
		 * Lister les Statuts possibles d'une Action
		 *
		 * @license Loxense
		 * @author Pierre-Luc MARY
		 * @version 1.0
		 * @date 2019-08-11
		 *
		 * @param[in] $lng_id ID de la Langue du libellé de l'Action
		 *
		 * @return Renvoi la liste des statuts d'une action ou lève une exception en cas d'erreur
		 *
		 */
		
		$Sql = 'SELECT lbr_code, lbr_libelle FROM lbr_libelles_referentiel WHERE lbr_code like \'ACT_STATUT_%\'
AND lng_id = :lng_id ';
		
		$requete = $this->prepareSQL($Sql);
		
		return $this->bindSQL($requete, ':lng_id', $lng_id, self::LANGUE_TYPE, self::LANGUE_LENGTH)
		->executeSQL($requete)->fetchAll(PDO::FETCH_CLASS);
	}
	
	
	public function listerFrequencesAction( $lng_id ) {
		/**
		 * Lister les Fréquences possibles d'une Action
		 *
		 * @license Loxense
		 * @author Pierre-Luc MARY
		 * @version 1.0
		 * @date 2019-08-11
		 *
		 * @param[in] $lng_id ID de la Langue du libellé de l'Action
		 *
		 * @return Renvoi la liste des fréquences d'une action ou lève une exception en cas d'erreur
		 *
		 */
		
		$Sql = 'SELECT lbr_code, lbr_libelle FROM lbr_libelles_referentiel WHERE lbr_code like \'ACT_FREQUENCE_%\'
AND lng_id = :lng_id ';
		
		$requete = $this->prepareSQL($Sql);
		
		return $this->bindSQL($requete, ':lng_id', $lng_id, self::LANGUE_TYPE, self::LANGUE_LENGTH)
		->executeSQL($requete)->fetchAll(PDO::FETCH_CLASS);
	}

	
	public function creerActionMesureConformite( $idn_id, $cnf_id, $amc_libelle, $amc_description,
		$amc_statut_code, $amc_frequence_code, $amc_date_debut_p, $amc_date_fin_p,
		$amc_date_debut_r, $amc_date_fin_r, $amc_priorite ) {
		/**
		 * Créer une Action à la Mesure de Conformité
		 *
		 * @license Loxense
		 * @author Pierre-Luc MARY
		 * @version 1.0
		 * @date 2019-08-15
		 *
		 * @param[in] $idn_id Id de l'Acteur qui est affecté à cette Action
		 * @param[in] $cnf_id Id de la Mesure de Conformité à laquelle est rattachée l'Action
		 * @param[in] $amc_libelle Libellé de l'Action
		 * @param[in] $amc_description Description de l'Action
		 * @param[in] $amc_statut_code Code du Statut de l'Action
		 * @param[in] $amc_frequence_code Code de la Fréquence de l'Action
		 * @param[in] $amc_date_debut_p Date de début prévisionnelle de l'Action
		 * @param[in] $amc_date_fin_p Date de fin prévisionnelle de l'Action
		 * @param[in] $amc_date_debut_r Date de début réelle de l'Action
		 * @param[in] $amc_date_fin_r Date de fin réelle de l'Action
		 * @param[in] $amc_priorite Priorité de l'Action
		 *
		 * @return Renvoi TRUE si l'Action a été créée ou lève une exception en cas d'erreur
		 *
		 */
			
		$Sql = 'INSERT INTO amc_actions_mesure_conformite ( ';

		if ( $idn_id != '' ) $Sql .= 'idn_id, '; 

		$Sql .= 'cnf_id, amc_libelle, amc_description, amc_statut_code, amc_frequence_code,
amc_date_debut_p, amc_date_fin_p';

		if ( $amc_date_debut_r != '' ) $Sql .= ', amc_date_debut_r';
		if ( $amc_date_fin_r != '' ) $Sql .= ', amc_date_fin_r';

		$Sql .= ', amc_priorite )
VALUES 
('; 
		if ( $idn_id != '' ) $Sql .= ':idn_id, ';

		$Sql .= ':cnf_id, :amc_libelle, :amc_description, :amc_statut_code, :amc_frequence_code,
:amc_date_debut_p, :amc_date_fin_p';

		if ( $amc_date_debut_r != '' ) $Sql .= ', :amc_date_debut_r';
		if ( $amc_date_fin_r != '' ) $Sql .= ', :amc_date_fin_r';

		$Sql .= ', :amc_priorite ) ';

		$requete = $this->prepareSQL($Sql);
		
		if ( $idn_id != '' ) $this->bindSQL($requete, ':idn_id', $idn_id, self::ID_TYPE);

		$this->bindSQL($requete, ':cnf_id', $cnf_id, self::ID_TYPE)
			->bindSQL($requete, ':amc_libelle', $amc_libelle, self::LIBELLE_TYPE, self::LIBELLE_LENGTH)
			->bindSQL($requete, ':amc_description', $amc_description, self::DESCRIPTION_TYPE)
			->bindSQL($requete, ':amc_statut_code', $amc_statut_code, self::STATUT_CODE_TYPE, self::STATUT_CODE_LENGTH)
			->bindSQL($requete, ':amc_frequence_code', $amc_frequence_code, self::FREQUENCE_CODE_TYPE, self::FREQUENCE_CODE_LENGTH)
			->bindSQL($requete, ':amc_date_debut_p', $amc_date_debut_p, self::DATE_TYPE, self::DATE_LENGTH)
			->bindSQL($requete, ':amc_date_fin_p', $amc_date_fin_p, self::DATE_TYPE, self::DATE_LENGTH);

		if ( $amc_date_debut_r != '' ) $this->bindSQL($requete, ':amc_date_debut_r', $amc_date_debut_r, self::DATE_TYPE, self::DATE_LENGTH);
		if ( $amc_date_fin_r != '' ) $this->bindSQL($requete, ':amc_date_fin_r', $amc_date_fin_r, self::DATE_TYPE, self::DATE_LENGTH);
			
		$this->bindSQL($requete, ':amc_priorite', $amc_priorite, self::PRIORITE_TYPE)
			->executeSQL($requete);
			
			
		switch( $this->getAttribute(PDO::ATTR_DRIVER_NAME) ) {
			default;
				$this->LastInsertId = $this->lastInsertId();
				break;
			
			case 'pgsql';
				$this->LastInsertId = $this->lastInsertId( 'amc_actions_mesure_conformite_amc_id_seq' );
				break;
		}
			
			
		return TRUE;
	}
	
	
	public function supprimerActionMesureConformite( $amc_id ) {
			/**
			 * Supprimer une Action à la Mesure de Conformité
			 *
			 * @license Loxense
			 * @author Pierre-Luc MARY
			 * @version 1.0
			 * @date 2019-08-15
			 *
			 * @param[in] $amc_id Id de l'Action à supprimer
			 *
			 * @return Renvoi TRUE si l'Action a été supprimée ou lève une exception en cas d'erreur
			 *
			 */
			
			$Sql = 'DELETE FROM amc_actions_mesure_conformite WHERE amc_id = :amc_id ';
			
			$requete = $this->prepareSQL($Sql);
			
			$this->bindSQL($requete, ':amc_id', $amc_id, self::ID_TYPE)
			->executeSQL($requete);

			
			return TRUE;
	}

	
	public function modifierActionMesureConformite( $amc_id, $idn_id, $cnf_id, $amc_libelle, $amc_description,
		$amc_statut_code, $amc_frequence_code, $amc_date_debut_p, $amc_date_fin_p,
		$amc_date_debut_r, $amc_date_fin_r, $amc_priorite ) {
			/**
			 * Modifier une Action à la Mesure de Conformité
			 *
			 * @license Loxense
			 * @author Pierre-Luc MARY
			 * @version 1.0
			 * @date 2019-08-15
			 *
			 * @param[in] $amc_id Id de l'Action à modifier
			 * @param[in] $idn_id Id de l'Acteur qui est affecté à cette Action
			 * @param[in] $cnf_id Id de la Mesure de Conformité à laquelle est rattachée l'Action
			 * @param[in] $amc_libelle Libellé de l'Action
			 * @param[in] $amc_description Description de l'Action
			 * @param[in] $amc_statut_code Code du Statut de l'Action
			 * @param[in] $amc_frequence_code Code de la Fréquence de l'Action
			 * @param[in] $amc_date_debut_p Date de début prévisionnelle de l'Action
			 * @param[in] $amc_date_fin_p Date de fin prévisionnelle de l'Action
			 * @param[in] $amc_date_debut_r Date de début réelle de l'Action
			 * @param[in] $amc_date_fin_r Date de fin réelle de l'Action
			 * @param[in] $amc_priorite Priorité de l'Action
			 *
			 * @return Renvoi TRUE si l'Action a été créée ou lève une exception en cas d'erreur
			 *
			 */
			
			$Sql = 'UPDATE amc_actions_mesure_conformite SET ';

			if ( $idn_id != '' ) $Sql .= 'idn_id = :idn_id, '; 
			
			$Sql .= 'cnf_id = :cnf_id, amc_libelle = :amc_libelle, ';
			
			if ( $amc_description != '' ) $Sql .= 'amc_description = :amc_description, ';

			$Sql .= 'amc_statut_code = :amc_statut_code, amc_frequence_code = :amc_frequence_code,
amc_date_debut_p = :amc_date_debut_p, amc_date_fin_p = :amc_date_fin_p, ';

			if ( $amc_date_debut_r != '' ) $Sql .= 'amc_date_debut_r = :amc_date_debut_r, '; 

			if ( $amc_date_fin_r != '' ) $Sql .= 'amc_date_fin_r = :amc_date_fin_r, ';

			$Sql .= 'amc_priorite = :amc_priorite
WHERE amc_id = :amc_id ';
			
			$requete = $this->prepareSQL($Sql);
			
			if ( $idn_id != '' ) $this->bindSQL($requete, ':idn_id', $idn_id, self::ID_TYPE);
			if ( $amc_description != '' ) $this->bindSQL($requete, ':amc_description', $amc_description, self::DESCRIPTION_TYPE);
			if ( $amc_date_debut_r != '' ) $this->bindSQL($requete, ':amc_date_debut_r', $amc_date_debut_r, self::DATE_TYPE, self::DATE_LENGTH);
			if ( $amc_date_fin_r != '' ) $this->bindSQL($requete, ':amc_date_fin_r', $amc_date_fin_r, self::DATE_TYPE, self::DATE_LENGTH);
			
			$this->bindSQL($requete, ':cnf_id', $cnf_id, self::ID_TYPE)
				->bindSQL($requete, ':amc_libelle', $amc_libelle, self::LIBELLE_TYPE, self::LIBELLE_LENGTH)
				->bindSQL($requete, ':amc_statut_code', $amc_statut_code, self::STATUT_CODE_TYPE, self::STATUT_CODE_LENGTH)
				->bindSQL($requete, ':amc_frequence_code', $amc_frequence_code, self::FREQUENCE_CODE_TYPE, self::FREQUENCE_CODE_LENGTH)
				->bindSQL($requete, ':amc_date_debut_p', $amc_date_debut_p, self::DATE_TYPE, self::DATE_LENGTH)
				->bindSQL($requete, ':amc_date_fin_p', $amc_date_fin_p, self::DATE_TYPE, self::DATE_LENGTH)
				->bindSQL($requete, ':amc_priorite', $amc_priorite, self::PRIORITE_TYPE)
				->bindSQL($requete, ':amc_id', $amc_id, self::ID_TYPE)
				->executeSQL($requete);
						
			return TRUE;
	}
	
	
	public function recupererInformationsAction( $amc_id ) {
		/**
		 * Récupérer les informations d'une Action de la Mesure de Conformité
		 *
		 * @license Loxense
		 * @author Pierre-Luc MARY
		 * @version 1.0
		 * @date 2019-08-15
		 *
		 * @param[in] $amc_id Id de l'Action pour laquelle on récupère les informations
		 *
		 * @return Renvoi les informations de l'Action ou lève une exception en cas d'erreur
		 *
		 */
		
		$Sql = 'SELECT idn_id, cnf_id, amc_libelle, amc_description, amc_statut_code, amc_frequence_code,
amc_date_debut_p, amc_date_fin_p, amc_date_debut_r, amc_date_fin_r, amc_priorite
FROM amc_actions_mesure_conformite
WHERE amc_id = :amc_id ';
		
		$requete = $this->prepareSQL($Sql);
		
		return $this->bindSQL($requete, ':amc_id', $amc_id, self::ID_TYPE)
			->executeSQL($requete)->fetchObject();
	}
	
	
	public function modifierMesureConformite( $cnf_id, $cnf_description, $cnf_etat_code ) {
		/**
		 * Modifier la mise en oeuvre d'une Mesure de Conformité
		 *
		 * @license Loxense
		 * @author Pierre-Luc MARY
		 * @version 1.0
		 * @date 2019-08-18
		 *
		 * @param[in] $cnf_id Id de la Mesure de Conformité à modifier
		 * @param[in] $cnf_description Descpription de la Mesure de Conformité
		 * @param[in] $cnf_etat_code Code de l'Etat de la Mesure de Conformité
		 *
		 * @return Renvoi TRUE si la Mesure de Conformites à été modifiée ou lève une exception en cas d'erreur
		 *
		 */
		
		$Sql = 'UPDATE cnf_conformite SET cnf_description = :cnf_description, cnf_etat_code = :cnf_etat_code
WHERE cnf_id = :cnf_id ';
		
		$requete = $this->prepareSQL($Sql);
		
		return $this->bindSQL($requete, ':cnf_id', $cnf_id, self::ID_TYPE)
			->bindSQL($requete, ':cnf_description', $cnf_description, self::DESCRIPTION_TYPE)
			->bindSQL($requete, ':cnf_etat_code', $cnf_etat_code, self::STATUT_CODE_TYPE, self::STATUT_CODE_LENGTH)
			->executeSQL($requete);
	}
	
} // Fin class Conformites

?>