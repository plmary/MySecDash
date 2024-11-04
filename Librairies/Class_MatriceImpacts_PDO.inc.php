<?php

include_once( 'Constants.inc.php' );

include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Connecteur_BD_PDO.inc.php' );


// Centralisation de la taille des objets en base.
if ( ! defined( 'L_NOM_CODE' ) ) define( 'L_NOM_CODE', 60);
if ( ! defined( 'L_CODE_COULEUR' ) ) define( 'L_CODE_COULEUR', 6);


class MatriceImpacts extends HBL_Connexioneur_BD {
/**
* Cette classe gère les Matrices d'Impacts de BIA.
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
	*/
	
	public function rechercherNiveauxImpact( $cmp_id, $nim_id='' ) {
	/**
	* Récupère les Niveaux d'Impact associé à une Campagne
	*
	* \license Copyleft Loxense
	* \author Pierre-Luc MARY
	* \date 2024-01-20
	*
	* \param[in] $cmp_id Identifiant de la Campagne à utiliser pour la recherche
	* \param[in] $nim_id Identifiant du Niveau d'Impact à rechercher
	*
	* \return Renvoi la liste des Niveaux d'Appréciation ou lève une exception en cas d'erreur.
	*/

		$SQL = 'SELECT * FROM nim_niveaux_impact AS "nim"
			WHERE nim.cmp_id = :cmp_id ';

		if ( $nim_id != '' ) {
			$SQL .= 'AND nim.nim_id = :nim_id ';
		}

		$SQL .= 'ORDER BY nim.nim_poids ';

		$Query = $this->prepareSQL( $SQL );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );

		if ( $nim_id != '' ) {
			$this->bindSQL( $Query, ':nim_id', $nim_id, PDO::PARAM_INT );
		}

		$this->executeSQL( $Query );

		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function rechercherTypesImpact( $cmp_id='', $tim_id='' ) {
	/**
	 * Récupère les Types d'Impact associé à une Campagne
	 *
	 * \license Copyleft Loxense
	 * \author Pierre-Luc MARY
	 * \date 2024-01-20
	 *
	 * \param[in] $cmp_id Identifiant de la Campagne à utiliser
	 * \param[in] $tim_id Identifiant du Type d'Impact à utiliser
	 *
	 * \return Renvoi la liste des Types d'Impact ou lève une exception en cas d'erreur.
	 */

		$Request = 'SELECT * FROM tim_types_impact AS "tim"
			WHERE tim.cmp_id = :cmp_id ';

		if ( $tim_id != '' ) {
			$Request .= 'AND tim.tim_id = :tim_id ';
		}

		$Request .= 'ORDER BY tim.tim_poids ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );

		if ( $tim_id != '' ) {
			$this->bindSQL( $Query, ':tim_id', $tim_id, PDO::PARAM_INT );
		}
		
		$this->executeSQL( $Query );
		
		return $Query->fetchAll( PDO::FETCH_CLASS );
	}
	
	
	public function rechercherMatriceImpacts( $cmp_id='', $nim_id='', $tim_id='' ) {
	/**
	 * Récupère les éléments de la Matrice d'Impacts
	 *
	 * \license Copyleft Loxense
	 * \author Pierre-Luc MARY
	 * \date 2024-01-20
	 *
	 * \param[in] $cmp_id Identifiant de la Campagne à utiliser
	 * \param[in] $nim_id Identifiant du Niveau d'Impact
	 * \param[in] $tim_id Identifiant du Type d'Impact
	 *
	 * \return Renvoi la liste des éléments de la Matrice d'Impacts ou lève une exception en cas d'erreur.
	 */

		$Request = 'SELECT * FROM mim_matrice_impacts AS "mim" 
			LEFT JOIN tim_types_impact AS "tim" ON tim.tim_id = mim.tim_id 
			LEFT JOIN nim_niveaux_impact AS "nim" ON nim.nim_id = mim.nim_id ';

		$Where = 'WHERE mim.cmp_id = :cmp_id ';

		if ( $nim_id != '' ) {
			$Where .= 'AND mim.nim_id = :nim_id ';
		}

		if ( $tim_id != '' ) {
			$Where .= 'AND mim.tim_id = :tim_id ';
		}

		$Request = $Request . $Where . 'ORDER BY nim_poids, tim_poids ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );

		if ( $nim_id != '' ) {
			$this->bindSQL( $Query, ':nim_id', $nim_id, PDO::PARAM_INT );
		}

		if ( $tim_id != '' ) {
			$this->bindSQL( $Query, ':tim_id', $tim_id, PDO::PARAM_INT );
		}

		$this->executeSQL( $Query );
		
		return $Query->fetchAll( PDO::FETCH_CLASS );
	}
	
	
	public function rechercherMatriceImpactsParID( $cmp_id='' ) {
		/**
		 * Récupère les éléments de la Matrice d'Impacts et les classes par ID
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-01-20
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne à utiliser
		 *
		 * \return Renvoi la liste des éléments de la Matrice d'Impacts ou lève une exception en cas d'erreur.
		 */
		
		$Request = 'SELECT mim.*, nim.nim_numero, nim.nim_couleur
			FROM mim_matrice_impacts AS "mim"
			LEFT JOIN tim_types_impact AS "tim" ON tim.tim_id = mim.tim_id
			LEFT JOIN nim_niveaux_impact AS "nim" ON nim.nim_id = mim.nim_id
			WHERE mim.cmp_id = :cmp_id
			ORDER BY nim_poids, tim_poids ';
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		$Occurrences = $Query->fetchAll( PDO::FETCH_CLASS );

		$Occurrences_Triees = [];
		foreach($Occurrences as $Occurrence) {
			$Occurrences_Triees[$Occurrence->nim_id.'-'.$Occurrence->tim_id] = $Occurrence;
		}

		return $Occurrences_Triees;
	}


	public function rechercherMatriceImpactsParChamp( $cmp_id, $NomChamp = 'mim_id' ) {
		/**
		 * Récupère les éléments de la Matrice d'Impacts et les classes par le $NomChamp.
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-01-20
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne à utiliser
		 * \param[in] $NomChamp Nom du champ à utiliser pour regrouper le résultat
		 *
		 * \return Renvoi la liste des éléments de la Matrice d'Impacts ou lève une exception en cas d'erreur.
		 */

		$Request = 'SELECT *
			FROM mim_matrice_impacts AS "mim"
			LEFT JOIN tim_types_impact AS "tim" ON tim.tim_id = mim.tim_id
			LEFT JOIN nim_niveaux_impact AS "nim" ON nim.nim_id = mim.nim_id
			WHERE mim.cmp_id = :cmp_id
			ORDER BY nim_poids, tim_poids ';
		
		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		$Occurrences = $Query->fetchAll( PDO::FETCH_CLASS );

		$Occurrences_Triees = [];
		foreach($Occurrences as $Occurrence) {
			$Occurrences_Triees[$Occurrence->$NomChamp] = $Occurrence;
		}

		return $Occurrences_Triees;
	}
	

	public function MaJNiveauImpact( $cmp_id, $nim_id, $nim_poids, $nim_numero, $nim_nom_code, $nim_couleur ) {
	/**
	 * Ajoute un Niveau d'Impacts
	 *
	 * \license Copyleft Loxense
	 * \author Pierre-Luc MARY
	 * \date 2024-01-29
	 *
	 * \param[in] $cmp_id Identifiant de la Campagne de Rattachement
	 * \param[in] $nim_id Identifiant du Niveau d'Impact
	 * \param[in] $nim_poids Poids du Niveau d'Impact
	 * \param[in] $nim_numero Numéro du Niveau d'Impact
	 * \param[in] $nim_nom_code Nom du Niveau d'Impact
	 * \param[in] $nim_couleur Couleur du Niveau d'Impact
	 *
	 * \return Renvoi TRUE si la mise à jour a réussi ou lève une exception en cas d'erreur.
	 */
		
		if ($nim_id == '') {
			$Query = $this->prepareSQL(
				'INSERT INTO nim_niveaux_impact
				(cmp_id, nim_poids, nim_numero, nim_nom_code, nim_couleur)
				VALUES (:cmp_id, :nim_poids, :nim_numero, :nim_nom_code, :nim_couleur)'
			);
		} else {
			$Query = $this->prepareSQL(
				'UPDATE nim_niveaux_impact SET
				cmp_id = :cmp_id,
				nim_poids = :nim_poids,
				nim_numero = :nim_numero,
				nim_nom_code = :nim_nom_code,
				nim_couleur = :nim_couleur
				WHERE nim_id = :nim_id'
				);

			$this->bindSQL( $Query, ':nim_id', $nim_id, PDO::PARAM_INT );
		}

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':nim_poids', $nim_poids, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':nim_numero', $nim_numero, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':nim_nom_code', $nim_nom_code, PDO::PARAM_STR, L_NOM_CODE );
		$this->bindSQL( $Query, ':nim_couleur', $nim_couleur, PDO::PARAM_STR, L_CODE_COULEUR );

		$this->executeSQL( $Query );

		if ($nim_id == '') {
			switch( $this->getAttribute(PDO::ATTR_DRIVER_NAME) ) {
				default;
				$this->LastInsertId = $this->lastInsertId();
				break;
				
				case 'pgsql';
				$this->LastInsertId = $this->lastInsertId( 'nim_niveaux_impact_nim_id_seq' );
				break;
			}
		}
		
		return TRUE;
	}


	public function supprimerNiveauImpact( $nim_id ) {
	/**
	 * Supprime un Niveau d'Impact
	 *
	 * \license Copyleft Loxense
	 * \author Pierre-Luc MARY
	 * \date 2024-01-29
	 *
	 * \param[in] $nim_id Identifiant du Niveau d'Impact
	 *
	 * \return Renvoi TRUE si la suppression a réussi ou lève une exception en cas d'erreur.
	 */

		$Query = $this->prepareSQL(
			'DELETE FROM nim_niveaux_impact
			WHERE nim_id = :nim_id'
			);
			
		$this->bindSQL( $Query, ':nim_id', $nim_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );

		return TRUE;
	}


	public function MaJTypeImpact( $cmp_id, $tim_id, $tim_poids, $tim_nom_code ) {
		/**
		 * Ajoute un Type d'Impacts
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-02-10
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne de rattachement
		 * \param[in] $tim_id Identifiant du Type d'Impact
		 * \param[in] $tim_poids Poids du Type d'Impact
		 * \param[in] $tim_nom_code Nom du Type d'Impact
		 *
		 * \return Renvoi TRUE si la mise à jour a réussi ou lève une exception en cas d'erreur.
		 */

		if ($tim_id == '') {
			$Query = $this->prepareSQL(
				'INSERT INTO tim_types_impact
				(cmp_id, tim_poids, tim_nom_code)
				VALUES (:cmp_id, :tim_poids, :tim_nom_code)'
				);
		} else {
			$Query = $this->prepareSQL(
				'UPDATE tim_types_impact SET
				cmp_id = :cmp_id,
				tim_poids = :tim_poids,
				tim_nom_code = :tim_nom_code
				WHERE tim_id = :tim_id'
				);
			
			$this->bindSQL( $Query, ':tim_id', $tim_id, PDO::PARAM_INT );
		}

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':tim_poids', $tim_poids, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':tim_nom_code', $tim_nom_code, PDO::PARAM_STR, L_NOM_CODE );

		$this->executeSQL( $Query );

		if ($tim_id == '') {
			switch( $this->getAttribute(PDO::ATTR_DRIVER_NAME) ) {
				default;
				$this->LastInsertId = $this->lastInsertId();
				break;
				
				case 'pgsql';
				$this->LastInsertId = $this->lastInsertId( 'tim_types_impact_tim_id_seq' );
				break;
			}
		}

		return TRUE;
	}


	public function supprimerTypeImpact( $tim_id ) {
		/**
		 * Supprime un Type d'Impact
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-02-10
		 *
		 * \param[in] $tim_id Identifiant du Type d'Impact
		 *
		 * \return Renvoi TRUE si la suppression a réussi ou lève une exception en cas d'erreur.
		 */

		$Query = $this->prepareSQL(
			'DELETE FROM tim_types_impact
			WHERE tim_id = :tim_id'
			);

		$this->bindSQL( $Query, ':tim_id', $tim_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		return TRUE;
	}


	public function MaJDescriptionImpact( $cmp_id, $mim_id, $nim_id, $tim_id, $mim_description ) {
		/**
		 * Ajoute une Description à un Impact
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-02-13
		 *
		 * \param[in] $cmp_id Identifiant de la Description de l'Impact
		 * \param[in] $mim_id Identifiant de la Description de l'Impact
		 * \param[in] $nim_id Identifiant du Type d'Impact
		 * \param[in] $tim_id Identifiant du Type d'Impact
		 * \param[in] $mim_description Texte de Description de l'Impact
		 *
		 * \return Renvoi TRUE si la mise à jour a réussi ou lève une exception en cas d'erreur.
		 */

		if ($mim_id == '') {
			$Query = $this->prepareSQL(
				'INSERT INTO mim_matrice_impacts
					(cmp_id, tim_id, nim_id, mim_description)
					VALUES (:cmp_id, :tim_id, :nim_id, :mim_description) '
				);

			$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
			$this->bindSQL( $Query, ':tim_id', $tim_id, PDO::PARAM_INT );
			$this->bindSQL( $Query, ':nim_id', $nim_id, PDO::PARAM_INT );
		} else {
			$Query = $this->prepareSQL(
				'UPDATE mim_matrice_impacts SET
					mim_description = :mim_description
					WHERE mim_id = :mim_id '
				);
			
			$this->bindSQL( $Query, ':mim_id', $mim_id, PDO::PARAM_INT );
		}

		$this->bindSQL( $Query, ':mim_description', $mim_description, PDO::PARAM_LOB );

		$this->executeSQL( $Query );

		if ($mim_id == '') {
			switch( $this->getAttribute(PDO::ATTR_DRIVER_NAME) ) {
				default;
				$this->LastInsertId = $this->lastInsertId();
				break;

				case 'pgsql';
				$this->LastInsertId = $this->lastInsertId( 'mim_matrice_impacts_mim_id_seq' );
				break;
			}
		}

		return TRUE;
	}


	public function supprimerDescriptionImpact( $mim_id ) {
		/**
		 * Supprime la Description d'un Impact
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-02-13
		 *
		 * \param[in] $mim_id Identifiant de la Description d'Impact à supprimer
		 *
		 * \return Renvoi TRUE si la suppression a réussi ou lève une exception en cas d'erreur.
		 */

		$Query = $this->prepareSQL(
			'DELETE FROM mim_matrice_impacts
			WHERE mim_id = :mim_id'
			);

		$this->bindSQL( $Query, ':mim_id', $mim_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );

		return TRUE;
	}
	
	
	public function controlerSiCampagneAMatriceImpacts( $cmp_id ) {
		/**
		 * Vérifie si une Campagne à une Matrice d'Impact associée.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-08-30
		 *
		 * \param[in] $cmp_id Identifiant de la Campagne à contrôler
		 *
		 * \return Renvoi un tableau (1er élément = flag association, 2ème élément = Date de la Campagne). Lève une Exception en cas d'erreur.
		 */
		$Request = 'SELECT cmp_date, COUNT(mim_id) as "total"
			FROM mim_matrice_impacts AS "mim"
			LEFT JOIN cmp_campagnes AS "cmp" ON cmp.cmp_id = mim.cmp_id
			WHERE mim.cmp_id = :cmp_id
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
	
} // Fin class MatriceImpacts

?>