<?php

include_once( 'Constants.inc.php' );
include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Connecteur_BD_PDO.inc.php' );


if ( ! defined( 'L_ETE_NOM' ) ) define( 'L_ETE_NOM', 100 );


class EchellesTemps extends HBL_Connexioneur_BD {

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


	public function majEchelleTemps( $ete_id, $ete_poids, $ete_nom_code ) {
	/**
	* Créé ou actualise une Echelle de Temps.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-02-21
	*
	* \param[in] $ete_id Identifiant de l'échelle de temps (à préciser si modification)
	* \param[in] $ete_poids Poids de l'élément dans l'échelle de temps.
	* \param[in] $ete_nom Nom de l'élément dans l'échelle de temps.
	*
	* \return Renvoi un booléen sur le succès de la création ou la modification de l'application
	*/
		if ( $ete_id == '' ) {
			$Request = 'INSERT INTO ete_echelle_temps
				( cmp_id, ete_poids, ete_nom_code ) VALUES
				( :cmp_id, :ete_poids, :ete_nom_code )';

			$Query = $this->prepareSQL( $Request );
		} else {
			$Request = 'UPDATE ete_echelle_temps SET 
				cmp_id = :cmp_id,
				ete_poids = :ete_poids,
				ete_nom_code = :ete_nom_code
				WHERE ete_id = :ete_id ';

			$Query = $this->prepareSQL( $Request );
			
			$this->bindSQL( $Query, ':ete_id', $ete_id, PDO::PARAM_INT );
		}

		$this->bindSQL( $Query, ':cmp_id', $_SESSION['s_cmp_id'], PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ete_poids', $ete_poids, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ete_nom_code', $ete_nom_code, PDO::PARAM_STR, L_ETE_NOM );
		

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}


		if ( $ete_id == '' ) {
			switch( $this->getAttribute(PDO::ATTR_DRIVER_NAME) ) {
			 default;
				$this->LastInsertId = $this->lastInsertId();
				break;

			 case 'pgsql';
				$this->LastInsertId = $this->lastInsertId( 'ete_echelle_temps_ete_id_seq' );
				break;
			}
		}
		
		return TRUE;
	}


	public function majEchelleTempsParChamp( $Id, $Field, $Value ) {
	/**
	* Créé ou actualise une Application.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-29
	*
	* \param[in] $ete_id Identifiant de l'application
	* \param[in] $Field Nom du champ de l'application à modifier
	* \param[in] $Value Valeur du champ de l'application à prendre en compte
	*
	* \return Renvoi un booléen sur le succès de la création ou la modification de l'application
	*/
		if ( $Id == '' or $Field == '' or $Value == '' ) return FALSE;


		$Request = 'UPDATE ete_echelle_temps SET ';

		switch ( $Field ) {
			case 'ete_poids':
				$Request .= 'ete_poids = :Value ';
				break;
				
			case 'ete_nom_code':
				$Request .= 'ete_nom_code = :Value ';
				break;

			default:
				return FALSE;
		}

		$Request .= 'WHERE ete_id = :ete_id';

		$Query = $this->prepareSQL( $Request );


		$this->bindSQL( $Query, ':ete_id', $Id, PDO::PARAM_INT );
		
		switch ( $Field ) {
			case 'ete_nom_code':
				$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_STR, L_ETE_NOM );
				break;
			
			case 'ete_poids':
				$this->bindSQL( $Query, ':Value', $Value,
					PDO::PARAM_INT );
				break;
		}


		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		
		return TRUE;
	}


	public function rechercherEchellesTemps( $cmp_id, $Order = 'ete_poids', $ete_id = '' ) {
	/**
	* Lister les Echelles Temps.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-02-21
	*
	* \param[in] $cmp_id Identification de la Campagne de rattachement
	* \param[in] $Order Permet de gérer l'ordre d'affichage
	* \param[in] $ete_id Permet de limiter la recherche à un élément de l'échelle de temps
	*
	* \return Renvoi une liste d'éléments de l'échelle de temps ou une liste vide
	*/
		$Request = 'SELECT
			ete.*
			FROM ete_echelle_temps AS "ete"
			WHERE ete.cmp_id = :cmp_id ';

		if ($ete_id != '') {
			$Request .= 'AND ete_id = :ete_id ';
		}

		switch ( $Order ) {
		 default:
		 case 'ete_nom':
			$Request .= 'ORDER BY ete_nom_code ';
			break;

		 case 'ete_nom-desc':
			$Request .= 'ORDER BY ete_nom_code DESC ';
			break;

		 case 'ete_poids':
			$Request .= 'ORDER BY ete_poids ';
			break;

		 case 'ete_poids-desc':
			$Request .= 'ORDER BY ete_poids DESC ';
			break;
		}

		$Query = $this->prepareSQL( $Request );
		if ($ete_id != '') {
			$this->bindSQL( $Query, ':ete_id', $ete_id, PDO::PARAM_INT ) ;
		}

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT ) ;

		$this->executeSQL( $Query );

		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function rechercherEchellesTempsParChamp( $cmp_id, $NomChamp = 'ete_id' ) {
		/**
		 * Lister les "Echelles de Temps" regrouper par un "Nom de Champ".
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-08-22
		 *
		 * \param[in] $cmp_id Identification de la Campagne de rattachement
		 * \param[in] $NomChamp Nom du champ à utiliser pour regrouper le résultat
		 *
		 * \return Renvoi une liste d'éléments de l'échelle de temps ou une liste vide
		 */
		$Request = 'SELECT
			*
			FROM ete_echelle_temps AS "ete"
			WHERE ete.cmp_id = :cmp_id
			ORDER BY ete_poids ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT ) ;

		$this->executeSQL( $Query );

		$Occurrences = $Query->fetchAll( PDO::FETCH_CLASS );

		$Occurrences_Triees = [];
		foreach($Occurrences as $Occurrence) {
			$Occurrences_Triees[$Occurrence->$NomChamp] = $Occurrence;
		}

		return $Occurrences_Triees;
	}


	public function supprimerEchelleTemps( $ete_id = '' ) {
	/**
	* Supprime un élément de l'échelle de temps.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-02-21
	*
	* \param[in] $ete_id Identifiant de l'élément de l'échelle de temps à supprimer
	*
	* \return Renvoi TRUE si l'occurrence a été supprimée, sinon FALSE. Lève une Exception en cas d'erreur.
	*/
		if ( $ete_id == '' ) return FALSE;

		$Query = $this->prepareSQL( 'DELETE ' .
		 'FROM ete_echelle_temps ' .
		 'WHERE ete_id = :ete_id' );
		
		$this->bindSQL( $Query, ':ete_id', $ete_id, PDO::PARAM_INT ) ;
		
		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return TRUE;
	}


	public function controlerAssociationEchelleTemps( $ete_id ) {
	/**
	* Vérifie si cette Echelle de Temps est associée à un autre objet.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-02-21
	*
	* \param[in] $ete_id Identifiant de l'Echelle de Temps à contrôler
	*
	* \return Renvoi l'occurrence listant les association de l'Echelle de Temps ou FALSE si pas d'entité. Lève une Exception en cas d'erreur.
	*/
		$Request = 'SELECT 
			COUNT(DISTINCT acap.app_id) AS "total_app",
			COUNT(DISTINCT acfr.frn_id) AS "total_frn",
			COUNT(DISTINCT dma.act_id) AS "total_act"
			FROM ete_echelle_temps AS "ete"
			LEFT JOIN acfr_act_frn AS "acfr" ON acfr.ete_id = ete.ete_id
			LEFT JOIN acap_act_app AS "acap" ON acap.ete_id_dima = ete.ete_id or acap.ete_id_pdma = ete.ete_id
			LEFT JOIN dma_dmia_activite AS "dma" ON dma.ete_id = ete.ete_id
			WHERE ete.ete_id = :ete_id ';

		 
		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':ete_id', $ete_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return $Query->fetchObject();
	}


	public function controlerSiCampagneAEchelleTemps( $cmp_id ) {
		/**
		 * Vérifie si une Campagne à une Echelle de Temps associée.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-08-29
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne à contrôler
		 *
		 * \return Renvoi un tableau (1er élément = flag association, 2ème élément = Date de la Campagne). Lève une Exception en cas d'erreur.
		 */
		$Request = 'SELECT cmp_date, COUNT(ete_id) as "total"
			FROM ete_echelle_temps AS "ete"
			LEFT JOIN cmp_campagnes AS "cmp" ON cmp.cmp_id = ete.cmp_id
			WHERE ete.cmp_id = :cmp_id
			GROUP BY cmp_date ';
		
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		$Resultat = $Query->fetchObject();

		if ( $Resultat == '' ) {
			$Request = 'SELECT cmp_date
			FROM cmp_campagnes AS "cmp"
			WHERE cmp.cmp_id = :cmp_id ';
			
			$Query = $this->prepareSQL( $Request );
			
			$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
			
			$this->executeSQL( $Query );
			
			$Resultat = $Query->fetchObject();

			return [FALSE, $Resultat->cmp_date];
		}
		
		if ( $Resultat->total > 0 ) {
			return [TRUE, $Resultat->cmp_date];
		} elseif ( $Resultat->total == 0 ) {
			return [FALSE, $Resultat->cmp_date];
		} else {
			return [-1, 'internal error'];
		}
	}
	
	
	public function initialiserEchelleTempsDefautACampagne( $cmp_id ) {
		/**
		 * Initialise une Echelle de Temps par défaut à une Campagne.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-08-30
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne à contrôler
		 *
		 * \return Renvoi TRUE si l'échelle de temps par défaut à bien était créés. Lève une Exception en cas d'erreur.
		 */
		$Request = 'INSERT INTO ete_echelle_temps (cmp_id, ete_poids, ete_nom_code) VALUES
			(:cmp_id, 1, \'4 heures\'),
			(:cmp_id, 2, \'1 jour\'),
			(:cmp_id, 3, \'2 jours\'),
			(:cmp_id, 4, \'3 jours\'),
			(:cmp_id, 5, \'1 semaine\'),
			(:cmp_id, 6, \'2 semaines\'),
			(:cmp_id, 7, \'1 mois\') ';
		
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		return TRUE;
	}
	

	public function totalEchellesTemps() {
	/**
	* Calcul le nombre total d'Echelles de Temps.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-02-21
	*
	* \return Renvoi le nombre total d'Echelles de Temps stockées en base. Lève une Exception en cas d'erreur.
	*/

		$Request = 'SELECT ' .
		 'count(*) AS total ' .
		 'FROM ete_echelle_temps ' ;

		$Query = $this->prepareSQL( $Request );

		$this->executeSQL( $Query );
		
		$Occurrence = $Query->fetchObject() ;
		
		return $Occurrence->total;
	}
	
}

?>