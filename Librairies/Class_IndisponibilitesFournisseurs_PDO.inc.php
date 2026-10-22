<?php

include_once( 'Constants.inc.php' );
include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Connecteur_BD_PDO.inc.php' );


class IndisponibilitesFournisseurs extends HBL_Connexioneur_BD {

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


	public function ajouterFournisseurActivite( $t_fournisseur ) {
	/**
	* Ajouter un Fournisseur à une Activité pour pallier à l'Indisponibilité d'un autre Fournisseur
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2026-01-02
	*
	* \param[in] $t_fournisseur Tableau contenant les informations du Fournisseur à ajouter
	*
	* \return Renvoi un booléen sur le succès de la création du Fournisseur
	*/
		$Request = 'INSERT INTO pap_prestataires_autres_prestataires
			( frn_id, act_id, ete_id, frn_id_nouveau, pap_contact, pap_commentaires ) VALUES 
			( :frn_id, :act_id, :ete_id, :frn_id_nouveau, :pap_contact, :pap_commentaires ) ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':act_id', $t_fournisseur[0], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':frn_id', $t_fournisseur[1], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':frn_id_nouveau', $t_fournisseur[2], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':pap_contact', $t_fournisseur[3], PDO::PARAM_STR, 100 );
		$this->bindSQL( $Query, ':ete_id', $t_fournisseur[4], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':pap_commentaires', $t_fournisseur[5], PDO::PARAM_LOB );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		switch( $this->getAttribute(PDO::ATTR_DRIVER_NAME) ) {
		 default;
			$this->LastInsertId = $this->lastInsertId();
			break;

		 case 'pgsql';
			$this->LastInsertId = $this->lastInsertId( 'pap_prestataires_autres_prestataires_pap_id_seq' );
			break;
		}

		return TRUE;
	}


	public function modifierFournisseurActivite( $t_fournisseur ) {
		/**
		 * Modifier un Fournisseur à une Activité pour pallier à l'Indisponibilité d'un Fournisseur'
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2026-01-02
		 *
		 * \param[in] $t_fournisseur Tableau contenant les informations du Fournisseur à modifier
		 *
		 * \return Renvoi un booléen sur le succès de la modification d'un Fournisseur'
		 */

		$Request = 'UPDATE pap_prestataires_autres_prestataires SET
			frn_id = :frn_id,
			act_id = :act_id,
			ete_id = :ete_id,
			frn_id_nouveau = :frn_id_nouveau,
			pap_contact = :pap_contact,
			pap_commentaires = :pap_commentaires
			WHERE pap_id = :pap_id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':pap_id', $t_fournisseur[0], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':act_id', $t_fournisseur[1], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':frn_id', $t_fournisseur[2], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':frn_id_nouveau', $t_fournisseur[3], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':pap_contact', $t_fournisseur[4], PDO::PARAM_STR, 100 );
		$this->bindSQL( $Query, ':ete_id', $t_fournisseur[5], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':pap_commentaires', $t_fournisseur[6], PDO::PARAM_LOB );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return TRUE;
	}


	public function supprimerFournisseurActivite( $t_fournisseur ) {
		/**
		 * Supprimer un Fournisseur à une Activité pour pallier à l'Indisponibilité d'un Fournisseur
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2026-01-02
		 *
		 * \param[in] $t_fournisseur Id de l'équipe interne à supprimer
		 *
		 * \return Renvoi un booléen sur le succès de la suppression d'un Fournisseur
		 */

		$Request = 'DELETE FROM pap_prestataires_autres_prestataires
			WHERE pap_id = :pap_id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':pap_id', $t_fournisseur[0], PDO::PARAM_INT );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return TRUE;
	}


	public function ajouterEquipeActivite( $t_equipe ) {
		/**
		 * Ajouter une Equipe à une Activité pour pallier à l'Indisponibilité d'un Fournisseur
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2026-01-02
		 *
		 * \param[in] $t_equipe Tableau contenant les informations de l'équipe à ajouter
		 * 
		 * \return Renvoi un booléen sur le succès de la création d'une Equipe
		 */

		$Request = 'INSERT INTO pei_prestataires_vers_equipes_internes
			( frn_id, act_id, ete_id, ppr_id, ein_id, pei_commentaires ) VALUES
			( :frn_id, :act_id, :ete_id, :ppr_id, :ein_id, :pei_commentaires ) ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':frn_id', $t_equipe[0], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':act_id', $t_equipe[1], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ein_id', $t_equipe[2], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ete_id', $t_equipe[3], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ppr_id', $t_equipe[4], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':pei_commentaires', $t_equipe[5], PDO::PARAM_LOB );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		switch( $this->getAttribute(PDO::ATTR_DRIVER_NAME) ) {
			default;
			$this->LastInsertId = $this->lastInsertId();
			break;

			case 'pgsql';
			$this->LastInsertId = $this->lastInsertId( 'pei_prestataires_vers_equipes_internes_pei_id_seq' );
			break;
		}

		return TRUE;
	}


	public function modifierEquipeActivite( $t_equipe ) {
		/**
		 * Modifier l'Equipe à une Activité pour pallier à l'Indisponibilité d'un Fournisseur
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2026-01-02
		 *
		 * \param[in] $t_autre_prestataire Tableau contenant les informations du Prestataire à modifier
		 *
		 * \return Renvoi un booléen sur le succès de la modification d'une Equipe
		 */

		$Request = 'UPDATE pei_prestataires_vers_equipes_internes SET
			ete_id = :ete_id,
			ppr_id = :ppr_id,
			ein_id = :ein_id,
			pei_commentaires = :pei_commentaires
			WHERE pei_id = :pei_id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':ein_id', $t_equipe[2], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ete_id', $t_equipe[3], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ppr_id', $t_equipe[4], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':pei_commentaires', $t_equipe[5], PDO::PARAM_LOB );
		$this->bindSQL( $Query, ':pei_id', $t_equipe[0], PDO::PARAM_INT );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return TRUE;
	}


	public function supprimerEquipeActivite( $t_equipe ) {
		/**
		 * Supprimer un Prestataire à une Activité pour pallier à l'Indisponibilité des Personnels
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2025-12-26
		 *
		 * \param[in] $t_autre_prestataire ID du Prestataire à supprimer
		 *
		 * \return Renvoi un booléen sur le succès de la suppression du Prestataire
		 */

		$Request = 'DELETE FROM pei_prestataires_vers_equipes_internes
			WHERE pei_id = :pei_id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':pei_id', $t_equipe[0], PDO::PARAM_INT );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return TRUE;
	}


	public function rechercherActivites( $cmp_id, $ent_id = '*', $Order = 'frn_nom', $frn_id = '' ) {
	/**
	* Lister les Activités.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2026-01-02
	*
	* \param[in] $cmp_id Identifiant de la Campagne de rattachement
	* \param[in] $ent_id Identifiant de l'Entité de rattachement
	* \param[in] $Order Permet de gérer l'ordre d'affichage
	* \param[in] $act_id Identifiant de l'Activité spécifique à récupérer
	*
	* \return Renvoi une liste des Activités ou une liste vide
	*/
		$Request = 'SELECT
acfr.frn_id, frn1.frn_nom, frn1.frn_description, acfr.ete_id, ete.ete_nom_code, acfr.acfr_consequence_indisponibilite, acfr.acfr_palliatif_tiers,
act.act_id, act.act_nom,
string_agg(DISTINCT pap.pap_id || \'---\' || pap.ete_id || \'---\' || ete_frn.ete_nom_code || \'---\'
	|| pap.frn_id_nouveau || \'---\' || frn2.frn_nom || \'---\' || pap.pap_contact || \'---\' || pap.pap_commentaires, \'===\') AS "pap_composite",
string_agg(DISTINCT pei.pei_id || \'---\' || pei.ete_id || \'---\' || ete_ein.ete_nom_code || \'---\'
	|| pei.ein_id || \'---\' || ein.ein_nom || \'---\' || pei.ppr_id || \'---\' || ppr_prenom || \'---\' || ppr_nom || \'---\' || ppr_interne
	|| \'---\' || pei_commentaires, \'===\') AS "pei_composite"

FROM acfr_act_frn AS "acfr"

LEFT JOIN act_activites AS "act" ON act.act_id = acfr.act_id
LEFT JOIN ete_echelle_temps AS "ete" ON ete.ete_id = acfr.ete_id

LEFT JOIN pap_prestataires_autres_prestataires AS "pap" ON pap.act_id = acfr.act_id AND pap.frn_id = acfr.frn_id
LEFT JOIN frn_fournisseurs AS "frn1" ON frn1.frn_id = acfr.frn_id
LEFT JOIN frn_fournisseurs AS "frn2" ON frn2.frn_id = pap.frn_id_nouveau
LEFT JOIN ete_echelle_temps AS "ete_frn" ON ete_frn.ete_id = pap.ete_id

LEFT JOIN pei_prestataires_vers_equipes_internes AS "pei" ON pei.act_id = act.act_id AND pei.frn_id = acfr.frn_id
LEFT JOIN frn_fournisseurs AS "frn3" ON frn3.frn_id = acfr.frn_id
LEFT JOIN ein_equipes_internes AS "ein" ON ein.ein_id = pei.ein_id
LEFT JOIN ppr_parties_prenantes AS "ppr" ON ppr.ppr_id = pei.ppr_id
LEFT JOIN ete_echelle_temps AS "ete_ein" ON ete_ein.ete_id = pei.ete_id

WHERE act.cmp_id = :cmp_id ';

		if ( $ent_id != '*' ) $Request .= 'AND act.ent_id = :ent_id ';

		if ( $frn_id != '' ) $Request .= 'AND acfr.frn_id = :frn_id ';

		$Request .= 'GROUP BY acfr.frn_id, frn1.frn_nom, frn1.frn_description, acfr.ete_id, ete.ete_nom_code, acfr.acfr_consequence_indisponibilite, ' .
			'acfr.acfr_palliatif_tiers, act.act_id, act.act_nom ';

		switch ( $Order ) {
		 default:
		 case 'act_nom':
			$Request .= 'ORDER BY act_nom ';
			break;

		 case 'act_nom-desc':
			$Request .= 'ORDER BY act_nom DESC ';
			break;

		 case 'frn_nom':
			$Request .= 'ORDER BY frn_nom ';
			break;

		 case 'frn_nom-desc':
			$Request .= 'ORDER BY frn_nom DESC ';
			break;
		}

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT ) ;

		if ( $frn_id != '' ) $this->bindSQL( $Query, ':frn_id', $frn_id, PDO::PARAM_INT );

		if ( $ent_id != '*' ) $this->bindSQL( $Query, ':ent_id', $ent_id, PDO::PARAM_INT ) ;

		$this->executeSQL( $Query );

		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function rechercherFournisseurs( $cmp_id, $ent_id = '*' ) {
		$Request = 'SELECT frn.* 
			FROM acfr_act_frn AS "acfr"
			LEFT JOIN act_activites AS "act" ON act.act_id = acfr.act_id
			LEFT JOIN frn_fournisseurs AS "frn" ON frn.frn_id = acfr.frn_id
			WHERE act.cmp_id = :cmp_id AND act.ent_id = :ent_id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT ) ;

		if ( $ent_id != '*' ) $this->bindSQL( $Query, ':ent_id', $ent_id, PDO::PARAM_INT ) ;

		$this->executeSQL( $Query );

		return $Query->fetchAll( PDO::FETCH_CLASS );
	}
}

?>