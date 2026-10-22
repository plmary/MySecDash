<?php

include_once( 'Constants.inc.php' );
include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Connecteur_BD_PDO.inc.php' );


if ( ! defined( 'L_NOM' ) ) define( 'L_NOM', 100 );
if ( ! defined( 'L_NOM_CODE' ) ) define( 'L_NOM_CODE', 60 );
if ( ! defined( 'L_DATE' ) ) define( 'L_DATE', 10 );
if ( ! defined( 'L_CONSEQUENCE_INDISPONIBILITE' ) ) define( 'L_CONSEQUENCE_INDISPONIBILITE', 100 );


class IndisponibilitesPersonnels extends HBL_Connexioneur_BD {

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


	public function ajouterEquipeInterneActivite( $t_autre_equipe_interne ) {
	/**
	* Ajouter une Equipe Interne à une Activité pour pallier à l'Indisponibilité des Personnels
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2025-12-23
	*
	* \param[in] $t_autre_equipe_interne Tableau contenant les informations de l'équipe interne à ajouter
	*
	* \return Renvoi un booléen sur le succès de la création de l'équipe interne
	*/
		$Request = 'INSERT INTO ipi_indisponibilite_personnel_interne
			( act_id, ein_id, ete_id, ppr_id, ipi_commentaires ) VALUES 
			( :act_id, :ein_id, :ete_id, :ppr_id, :ipi_commentaires ) ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':act_id', $t_autre_equipe_interne[0], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ein_id', $t_autre_equipe_interne[1], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ete_id', $t_autre_equipe_interne[2], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ppr_id', $t_autre_equipe_interne[3], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ipi_commentaires', $t_autre_equipe_interne[4], PDO::PARAM_LOB );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		switch( $this->getAttribute(PDO::ATTR_DRIVER_NAME) ) {
		 default;
			$this->LastInsertId = $this->lastInsertId();
			break;

		 case 'pgsql';
			$this->LastInsertId = $this->lastInsertId( 'ipi_indisponibilite_personnel_interne_ipi_id_seq' );
			break;
		}

		return TRUE;
	}


	public function modifierEquipeInterneActivite( $t_autre_equipe_interne ) {
		/**
		 * Modifier une Equipe Interne à une Activité pour pallier à l'Indisponibilité des Personnels
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2025-12-23
		 *
		 * \param[in] $t_autre_equipe_interne Tableau contenant les informations de l'équipe interne à modifier
		 *
		 * \return Renvoi un booléen sur le succès de la modification de l'équipe interne
		 */

		$Request = 'UPDATE ipi_indisponibilite_personnel_interne SET
			act_id = :act_id,
			ein_id = :ein_id,
			ete_id = :ete_id,
			ppr_id = :ppr_id,
			ipi_commentaires = :ipi_commentaires
			WHERE ipi_id = :ipi_id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':ipi_id', $t_autre_equipe_interne[0], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':act_id', $t_autre_equipe_interne[1], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ein_id', $t_autre_equipe_interne[2], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ete_id', $t_autre_equipe_interne[3], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ppr_id', $t_autre_equipe_interne[4], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ipi_commentaires', $t_autre_equipe_interne[5], PDO::PARAM_LOB );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return TRUE;
	}


	public function supprimerEquipeInterneActivite( $t_autre_equipe_interne ) {
		/**
		 * Supprimer une Equipe Interne à une Activité pour pallier à l'Indisponibilité des Personnels
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2025-12-23
		 *
		 * \param[in] $t_autre_equipe_interne Id de l'équipe interne à supprimer
		 *
		 * \return Renvoi un booléen sur le succès de la suppression de l'Equipe Interne
		 */

		$Request = 'DELETE FROM ipi_indisponibilite_personnel_interne
			WHERE ipi_id = :ipi_id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':ipi_id', $t_autre_equipe_interne[0], PDO::PARAM_INT );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return TRUE;
	}


	public function ajouterPrestataireActivite( $t_autre_prestataire ) {
		/**
		 * Ajouter un Prestataire à une Activité pour pallier à l'Indisponibilité des Personnels
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2025-12-26
		 *
		 * \param[in] $t_autre_prestataire Informations sur le Prestataire à ajouter [act_id, frn_id, ipp_contact, ete_id, ipp_commentaires]
		 * 
		 * \return Renvoi un booléen sur le succès de la création du Prestataire
		 */

		$Request = 'INSERT INTO ipp_indisponibilite_personnel_prestataire
			( act_id, frn_id, ete_id, ipp_contact, ipp_commentaires ) VALUES
			( :act_id, :frn_id, :ete_id, :ipp_contact, :ipp_commentaires ) ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':act_id', $t_autre_prestataire[0], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':frn_id', $t_autre_prestataire[1], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ipp_contact', $t_autre_prestataire[2], PDO::PARAM_STR, 100 );
		$this->bindSQL( $Query, ':ete_id', $t_autre_prestataire[3], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ipp_commentaires', $t_autre_prestataire[4], PDO::PARAM_LOB );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		switch( $this->getAttribute(PDO::ATTR_DRIVER_NAME) ) {
			default;
			$this->LastInsertId = $this->lastInsertId();
			break;

			case 'pgsql';
			$this->LastInsertId = $this->lastInsertId( 'ipp_indisponibilite_personnel_prestataire_ipp_id_seq' );
			break;
		}

		return TRUE;
	}


	public function modifierPrestataireActivite( $t_autre_prestataire ) {
		/**
		 * Modifier le Prestataire à une Activité pour pallier à l'Indisponibilité des Personnels
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2025-12-26
		 *
		 * \param[in] $t_autre_prestataire Tableau contenant les informations du Prestataire à modifier [ipp_id, act_id, frn_id, ipp_contact, ete_id, ipp_commentaires]
		 *
		 * \return Renvoi un booléen sur le succès de la modification du Prestataire
		 */

		$Request = 'UPDATE ipp_indisponibilite_personnel_prestataire SET
			act_id = :act_id,
			frn_id = :frn_id,
			ete_id = :ete_id,
			ipp_contact = :ipp_contact,
			ipp_commentaires = :ipp_commentaires
			WHERE ipp_id = :ipp_id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':ipp_id', $t_autre_prestataire[0], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':act_id', $t_autre_prestataire[1], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':frn_id', $t_autre_prestataire[2], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ipp_contact', $t_autre_prestataire[3], PDO::PARAM_STR, 100 );
		$this->bindSQL( $Query, ':ete_id', $t_autre_prestataire[4], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ipp_commentaires', $t_autre_prestataire[5], PDO::PARAM_LOB );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return TRUE;
	}


	public function supprimerPrestataireActivite( $t_autre_prestataire ) {
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

		$Request = 'DELETE FROM ipp_indisponibilite_personnel_prestataire
			WHERE ipp_id = :ipp_id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':ipp_id', $t_autre_prestataire[0], PDO::PARAM_INT );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return TRUE;
	}


	public function rechercherActivites( $cmp_id, $ent_id = '*', $Order = 'act_nom', $act_id = '' ) {
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
act.act_id, act.act_nom,
string_agg(DISTINCT ipp.ipp_id || \'---\' || ipp.frn_id || \'---\' || frn.frn_nom || \'---\' || COALESCE(ipp.ipp_contact, \'\') || \'---\' || COALESCE(ipp.ipp_commentaires, \'\') || \'---\' || ipp.ete_id || \'---\' || ete_frn.ete_nom_code, \'===\') AS "ipp_composite",
string_agg(DISTINCT ipi.ipi_id || \'---\' || ipi.ein_id || \'---\' || ein.ein_nom || \'---\' || ipi.ppr_id || \'---\' || ppr_prenom || \'---\' || ppr_nom || \'---\' || ppr_interne || \'---\' || ipi_commentaires || \'---\' || ipi.ete_id || \'---\' || ete_ein.ete_nom_code, \'===\') AS "ipi_composite"

FROM act_activites AS "act"

LEFT JOIN ipp_indisponibilite_personnel_prestataire AS "ipp" ON ipp.act_id = act.act_id
LEFT JOIN frn_fournisseurs AS "frn" ON frn.frn_id = ipp.frn_id
LEFT JOIN ete_echelle_temps AS "ete_frn" ON ete_frn.ete_id = ipp.ete_id

LEFT JOIN ipi_indisponibilite_personnel_interne AS "ipi" ON ipi.act_id = act.act_id
LEFT JOIN ein_equipes_internes AS "ein" ON ein.ein_id = ipi.ein_id
LEFT JOIN ppr_parties_prenantes AS "ppr" ON ppr.ppr_id = ipi.ppr_id
LEFT JOIN ete_echelle_temps AS "ete_ein" ON ete_ein.ete_id = ipi.ete_id

WHERE act.cmp_id = :cmp_id ';

		if ( $ent_id != '*' ) $Request .= 'AND act.ent_id = :ent_id ';

		if ( $act_id != '' ) $Request .= 'AND act.act_id = :act_id ';

		$Request .= 'GROUP BY act.act_id, act.act_nom ';

		switch ( $Order ) {
		 default:
		 case 'act_nom':
			$Request .= 'ORDER BY act_nom ';
			break;

		 case 'act_nom-desc':
			$Request .= 'ORDER BY act_nom DESC ';
			break;
		}

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT ) ;

		if ( $act_id != '' ) $this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT );

		if ( $ent_id != '*' ) $this->bindSQL( $Query, ':ent_id', $ent_id, PDO::PARAM_INT ) ;

		$this->executeSQL( $Query );

		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function rechercherEquipes( $sct_id, $ordre_tri = 'ein_nom', $ein_id = '' ) {
		/**
		 * Lister les Equipes identifiées chez une Société.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-03-05
		 *
		 * \param[in] $sct_id Identifiant de la Campagne de rattachement
		 * \param[in] $ordre_tri Indique le nom de la colonne sur lequel le tri doit être réalisé
		 * \param[in] $ein_id Identifiant de l'équipe interne à récupérer
		 *
		 * \return Renvoi une liste d'équipes ou une liste vide
		 */

		$Request = 'SELECT *
FROM ein_equipes_internes AS "ein"
WHERE ein.sct_id = :sct_id ';

		if ( $ein_id != '' ) {
			$Request .= ' AND ein.ein_id = :ein_id ';
		}

		switch ( $ordre_tri ) {
			default:
			case 'ein_nom':
				$Request .= 'ORDER BY ein_nom ';
				break;
				
			case 'ein_nom-desc':
				$Request .= 'ORDER BY ein_nom DESC ';
				break;
		}

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':sct_id', $sct_id, PDO::PARAM_INT ) ;

		if ( $ein_id != '' ) {
			$this->bindSQL( $Query, ':ein_id', $ein_id, PDO::PARAM_INT ) ;
		}

		$this->executeSQL( $Query );

		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function ajouterModifierEquipeInterne( $ein_id, $sct_id, $ein_nom, $ein_description = '' ) {
		/**
		 * Ajouter ou modifier une Equipe Interne à une Société.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2025-12-25
		 *
		 * \param[in] $ein_id Identifiant de l'Equipe'
		 * \param[in] $sct_id Identifiant de la Société de rattachement
		 * \param[in] $ein_nom Nom de l'Equipe
		 * \param[in] $ein_description Description de l'Equipe
		 *
		 * \return Renvoi TRUE si l'équipe a été créée ou modifiée. Sinon, retourne une erreur
		 */

		if ( $ein_id == '' ) {
			$Request = 'INSERT INTO ein_equipes_internes ( sct_id, ein_nom ';

			if ($ein_description != '' ) {
				$Request .= ', ein_description ';
			}

			$Request .= ') VALUES ( :sct_id, :ein_nom';

			if ($ein_description != '' ) {
				$Request .= ', :ein_description ';
			}

			$Request .= ') ';
		} else {
			$Request = 'UPDATE ein_equipes_internes SET
				sct_id = :sct_id,
				ein_nom = :ein_nom ';

			if ($ein_description != '' ) {
				$Request .= ', ein_description = :ein_description ';
			}

			$Request .= 'WHERE ein_id = :ein_id ';
		}

		$Query = $this->prepareSQL( $Request );


		$this->bindSQL( $Query, ':sct_id', $sct_id, PDO::PARAM_INT ) ;

		$this->bindSQL( $Query, ':ein_nom', $ein_nom, PDO::PARAM_STR, 100 ) ;

		if ($ein_description != '' ) {
			$this->bindSQL( $Query, ':ein_description', $ein_description, PDO::PARAM_LOB );
		}

		if ( $ein_id != '' ) {
			$this->bindSQL( $Query, ':ein_id', $ein_id, PDO::PARAM_INT ) ;
		}

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		if ( $ein_id == '' ) {
			switch( $this->getAttribute(PDO::ATTR_DRIVER_NAME) ) {
				default;
				$this->LastInsertId = $this->lastInsertId();
				break;
	
				case 'pgsql';
				$this->LastInsertId = $this->lastInsertId( 'ein_equipes_internes_ein_id_seq' );
				break;
			}
		}

		return TRUE;
	}


	public function supprimerEquipeInterne( $ein_id ) {
		/**
		 * Supprimer une Equipe Interne à une Société.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2025-12-25
		 *
		 * \param[in] $ein_id Identifiant de l'Equipe'
		 *
		 * \return Renvoi TRUE si l'équipe a été supprimée. Sinon, retourne une erreur
		 */

		$Request = 'DELETE FROM ein_equipes_internes
			WHERE ein_id = :ein_id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':ein_id', $ein_id, PDO::PARAM_INT ) ;

		$this->executeSQL( $Query );

		return TRUE;
	}


	public function majEquipeInterneParChamp( $_Id, $_Source, $_Valeur ) {
		/**
		 * Modifier le Nom d'une Equipe Interne.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2025-12-27
		 *
		 * \param[in] $_Id Identifiant de l'Equipe à modifier
		 * \param[in] $_Source Nom du champ à modifier
		 * \param[in] $_Valeur Valeur du champ à modifier
		 *
		 * \return Renvoi TRUE si l'équipe a été modifiée. Sinon, retourne une erreur
		 */

		$Request = 'UPDATE ein_equipes_internes SET ' .
			$_Source . ' = :valeur ' .
			'WHERE ein_id = :ein_id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':ein_id', $_Id, PDO::PARAM_INT ) ;
		
		switch( $_Source ) {
		 case 'ein_nom':
			$this->bindSQL( $Query, ':valeur', $_Valeur, PDO::PARAM_STR, 100 ) ;
			break;

		 case 'ein_description':
			$this->bindSQL( $Query, ':valeur', $_Valeur, PDO::PARAM_LOB ) ;
			break;
		}

		$this->executeSQL( $Query );

		return TRUE;
	}
}

?>