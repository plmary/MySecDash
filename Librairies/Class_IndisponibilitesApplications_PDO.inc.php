<?php

include_once( 'Constants.inc.php' );
include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Connecteur_BD_PDO.inc.php' );


class IndisponibilitesApplications extends HBL_Connexioneur_BD {

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


	public function ajouterApplicationSecours( $t_application ) {
	/**
	* Ajouter une Application de Secours pour pallier à l'Indisponibilité d'une Application
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2026-08-26
	*
	* \param[in] $t_application Tableau contenant les informations d'une Application de secours à ajouter
	*
	* \return Renvoi un booléen sur le succès de la création de l'Application de Secours
	*/
		$Request = 'INSERT INTO appa_applications_palliatives
			( app_id, act_id, app_id_secours, appa_mode_operatoire ) VALUES 
			( :app_id, :act_id, :app_id_secours, :appa_mode_operatoire ) ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':app_id', $t_application[0], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':act_id', $t_application[1], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':app_id_secours', $t_application[2], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':appa_mode_operatoire', $t_application[3], PDO::PARAM_LOB );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		switch( $this->getAttribute(PDO::ATTR_DRIVER_NAME) ) {
		 default;
			$this->LastInsertId = $this->lastInsertId();
			break;

		 case 'pgsql';
			$this->LastInsertId = $this->lastInsertId( 'appa_applications_palliatives_appa_id_seq' );
			break;
		}

		return TRUE;
	}


	public function modifierApplicationSecours( $t_application ) {
		/**
		 * Modifier une Application de Secours pour pallier à l'Indisponibilité d'une Application
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2026-08-26
		 *
		 * \param[in] $t_application Tableau contenant les informations de l'Application de Secours à modifier
		 *
		 * \return Renvoi un booléen sur le succès de la modification de l'Application de Secours
		 */

		$Request = 'UPDATE appa_applications_palliatives SET
			app_id = :app_id,
			act_id = :act_id,
			app_id_secours = :app_id_secours,
			appa_mode_operatoire = :appa_mode_operatoire
			WHERE appa_id = :appa_id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':app_id', $t_application[0], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':act_id', $t_application[1], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':app_id_secours', $t_application[2], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':appa_mode_operatoire', $t_application[3], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':appa_id', $t_application[4], PDO::PARAM_STR, 100 );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return TRUE;
	}


	public function supprimerApplicationSecours( $t_application ) {
		/**
		 * Supprimer une Application de Secours pour pallier à l'Indisponibilité d'une Application
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2026-01-02
		 *
		 * \param[in] $t_application Id de l'Application de Secours à supprimer
		 *
		 * \return Renvoi un booléen sur le succès de la suppression de l'Application  de Secours
		 */

		$Request = 'DELETE FROM appa_applications_palliatives
			WHERE appa_id = :appa_id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':appa_id', $t_fournisseur[0], PDO::PARAM_INT );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return TRUE;
	}


	public function rechercherApplicationsSecours( $cmp_id, $ent_id, $act_id, $Order = 'app_nom', $app_id = '' ) {
	/**
	* Lister les Applications de Secours de rattachées à une Application et une Activité.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2026-08-26
	*
	* \param[in] $cmp_id Identifiant de la Campagne de rattachement
	* \param[in] $ent_id Identifiant de l'Entité de rattachement
	* \param[in] $act_id Identifiant de l'Activité de rattachement
	* \param[in] $Order Permet de gérer l'ordre d'affichage
	*
	* \return Renvoi une liste des Activités ou une liste vide
	*  || \'^^^\' || acap_palliatif || \'^^^\' || acap_hebergement || \'^^^\' || acap_niveau_service
	*/
		$Request = 'SELECT acap.app_id, app.app_nom, ete_dima.ete_nom_code AS "ete_dmia_nom", ete_pdma.ete_nom_code AS "ete_pdma_nom",
	acap.acap_palliatif, acap.acap_hebergement, acap.acap_niveau_service,
	STRING_AGG(DISTINCT acap.act_id || \'^^^\' || act.act_nom, \'¨¨¨\') AS "activites",
	STRING_AGG(DISTINCT appa.appa_id || \'^^^\' || appa.app_id_secours || \'^^^\' || app_secours.app_nom || \'^^^\' || appa.appa_mode_operatoire,\'¨¨¨\') AS "applications_palliatives"
FROM acap_act_app AS "acap"
LEFT JOIN act_activites AS "act" ON act.act_id = acap.act_id
LEFT JOIN app_applications AS "app" ON app.app_id = acap.app_id
LEFT JOIN ete_echelle_temps AS "ete_dima" ON ete_dima.ete_id = acap.ete_id_dima
LEFT JOIN ete_echelle_temps AS "ete_pdma" ON ete_pdma.ete_id = acap.ete_id_pdma
LEFT JOIN appa_applications_palliatives AS "appa" ON appa.act_id = acap.act_id AND appa.app_id = acap.app_id
LEFT JOIN app_applications AS "app_secours" ON app_secours.app_id = appa.app_id_secours
WHERE act.cmp_id = :cmp_id AND act.ent_id = :ent_id AND acap.act_id = :act_id ';

		if ($app_id != '') {
			$Request .= 'AND acap.app_id = :app_id ';
		}

		$Request .= 'GROUP BY acap.app_id, app.app_nom, ete_dima.ete_nom_code, ete_pdma.ete_nom_code, acap.acap_palliatif, acap.acap_hebergement, acap.acap_niveau_service ';

		switch ( $Order ) {
		 default:
		 case 'app_nom':
			$Request .= 'ORDER BY app_nom ';
			break;

		 case 'app_nom-desc':
			$Request .= 'ORDER BY app_nom DESC ';
			break;
		 case 'act_nom':
			$Request .= 'ORDER BY act_nom ';
			break;

		 case 'act_nom-desc':
			$Request .= 'ORDER BY act_nom DESC ';
			break;
		}

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT ) ;
		$this->bindSQL( $Query, ':ent_id', $ent_id, PDO::PARAM_INT ) ;
		$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT ) ;

		if ($app_id != '') {
			$this->bindSQL( $Query, ':app_id', $app_id, PDO::PARAM_INT ) ;
		}

		$this->executeSQL( $Query );

		return $Query->fetchAll( PDO::FETCH_CLASS );
	}
}

?>