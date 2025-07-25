<?php

include_once( 'Constants.inc.php' );
include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Connecteur_BD_PDO.inc.php' );

if ( ! defined( 'L_PRF_LABEL' ) ) define( 'L_PRF_LABEL', 40 );
if ( ! defined( 'L_APP_LABEL' ) ) define( 'L_APP_LABEL', 50 );
if ( ! defined( 'L_RGH_LABEL' ) ) define( 'L_RGH_LABEL', 30 );


class HBL_Profils extends HBL_Connexioneur_BD {
/**
* Cette classe gère les profils.
*
* \license Copyleft Loxense
* \author Pierre-Luc MARY
* \date 2015-05-31
*/

	public function __construct() {
	/**
	* Connexion à la base de données.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-31
	*
	* \return Renvoi un booléen sur le succès de la connexion à la base de données
	*/
		parent::__construct();
		
		return TRUE;
	}


	/* ===============================================================================
	** Gestion des Profils
	*/
	
	public function majProfil( $prf_id, $prf_libelle, $prf_description ) {
	/**
	* Créé ou actualise un Profil.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-31
	*
	* \param[in] $prf_id Identifiant du Profil à mettre à jour s'il est précisé
	* \param[in] $prf_libelle Libellé à donner au Profil (obligatoire)
	* \param[in] $prf_description Description du Profil
	*
	* \return Renvoi TRUE si le Profil a été créé ou mis à jour, sinon FALSE. Lève une exception en cas d'erreur.
	*/

		if ( $prf_id == '' ) {
			$Command = 'INSERT : ' ;

			$Query = $this->prepareSQL( 'INSERT INTO prf_profils
				( prf_libelle, prf_description )
				VALUES ( :prf_libelle, :prf_description )' );
		} else {
			$Command = 'UPDATE : ' ;

			$Query = $this->prepareSQL( 'UPDATE prf_profils SET
				prf_libelle = :prf_libelle,
				prf_description = :prf_description
				WHERE prf_id = :prf_id' );

			$this->bindSQL( $Query, ':prf_id', $prf_id, PDO::PARAM_INT );
		}

		$this->bindSQL( $Query, ':prf_libelle', $prf_libelle, PDO::PARAM_STR, L_PRF_LABEL );
		$this->bindSQL( $Query, ':prf_description', $prf_description, PDO::PARAM_LOB );

		$this->executeSQL( $Query );


		if ( $prf_id == '' ) {
			switch( $this->getAttribute(PDO::ATTR_DRIVER_NAME) ) {
			 case 'pgsql';
				$this->LastInsertId = $this->lastInsertId( 'prf_profils_prf_id_seq' );
				break;
			}
		}

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
		
		return TRUE;
	}
	
	
	public function majProfilParChamp( $ID, $Source, $Valeur ) {
		/**
		 * Modifier juste un champ du Profil.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2025-07-11
		 *
		 * \param[in] $ID Identifiant du Profil à modifier
		 * \param[in] $Source Nom du champ à modifier
		 * \param[in] $Valeur Valeur à affecter au champ.
		 *
		 * \return Renvoi TRUE si le Profil a été mis à jour, FALSE si le Profil n'existe pas. Lève une Exception en cas d'erreur.
		 */
		if ( $ID == '' ) return FALSE;
		
		$Request = 'UPDATE prf_profils SET ';
		
		switch ( $Source ) {
			case 'prf_libelle':
				$Request .= 'prf_libelle = :Valeur ';
				break;
				
			case 'prf_description':
				$Request .= 'prf_description = :Valeur ';
				break;
		}
		
		$Request .= 'WHERE prf_id = :ID ';
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':ID', $ID, PDO::PARAM_INT );
		
		
		switch ( $Source ) {
			case 'prf_libelle':
				$this->bindSQL( $Query, ':Valeur', $Valeur, PDO::PARAM_STR, L_PRF_LABEL );
				break;
				
			case 'prf_description':
				$this->bindSQL( $Query, ':Valeur', $Valeur, PDO::PARAM_LOB );
				break;
		}
		
		$this->executeSQL( $Query );
		
		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
		
		
		return TRUE;
	}


	public function rechercherProfils( $order = '', $Search = '' ) {
	/**
	* Lister les Profils.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-31
	*
	* \param[in] $order Ordre d'affichage des éléments dans la liste
	* \param[in] $Search Critère de recherche pour trouver des profils
	*
	* \return Renvoi un tableau des Profils trouvés, sinon un tableau vide. Lève une Exception en cas d'erreur.
	*/
		$Request = 'SELECT ' .
		 '* ' .
		 'FROM prf_profils ' ;
		
		switch ( $order ) {
		 default:
		 case 'prf_libelle':
			$Request .= 'ORDER BY prf_libelle ';
			break;
			
		 case 'prf_libelle-desc':
			$Request .= 'ORDER BY prf_libelle DESC ';
			break;

		 case 'prf_description':
			$Request .= 'ORDER BY prf_description ';
			break;
			
		 case 'prf_description-desc':
			$Request .= 'ORDER BY prf_description DESC ';
			break;
		}

		if ( $Search != '' ) {
			$Request .= 'WHERE prf_libelle like :search ';
		}

		$Query = $this->prepareSQL( $Request );

		if ( $Search != '' ) {
			$this->bindSQL( $Query, ':search', '%' . $Search . '%', PDO::PARAM_STR, L_PRF_LABEL );
		}
		
		$this->executeSQL( $Query );
 
 		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function detaillerProfil( $prf_id ) {
	/**
	* Récupère les informations d'un Profil.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-31
	*
	* \param[in] $prf_id Identifiant du Profil à récupérer
	*
	* \return Renvoi l'instance du Profil trouvé, sinon une instance vide. Lève une Exception en cas d'erreur.
	*/
		$Request = 'SELECT ' .
		 'prf_libelle, prf_description ' .
		 'FROM prf_profils ' .
		 'WHERE prf_id = :prf_id ';
		 
		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':prf_id', $prf_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
		
 		return $Query->fetchObject();
	}


	public function supprimerProfil( $prf_id ) {
	/**
	* Supprime le Profil spécifié.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-31
	*
	* \param[in] $prf_id Identifiant du Profil à supprimer
	*
	* \return Renvoi TRUE si le Profil a été supprimé, sinon FALSE. Lève une Exception en cas d'erreur.
	*/
	
		/*
		** Détruit le Profil.
		*/
		$Query = $this->prepareSQL( 'DELETE ' .
		 'FROM prf_profils ' .
		 'WHERE prf_id = :prf_id' );
		
		$this->bindSQL( $Query, ':prf_id', $prf_id, PDO::PARAM_INT ) ;
		
		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return TRUE;
	}


	public function totalProfils() {
	/**
	* Calcul le nombre total de Profils.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-31
	*
	* \return Renvoi un entier représentant le total de Profils trouvé en base
	*/
		$Request = 'SELECT ' .
		 'count(*) AS total ' .
		 'FROM prf_profils ';

		$Query = $this->prepareSQL( $Request );

		$this->executeSQL( $Query );
		
		$Occurrence = $Query->fetchObject() ;
		
		return $Occurrence->total;
	}


	public function testerAssociationProfil( $prf_id ) {
	/**
	* Récupère les informations d'un Profil.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-15
	*
	* \param[in] $prf_id Identifiant du Profil à récupérer
	*
	* \return Renvoi l'occurrence listant les association du Profil ou FALSE si pas de Profil. Lève une Exception en cas d'erreur.
	*/
		$Request = 'SELECT ' .
		 'COUNT(DISTINCT idn_id) AS "total_idn" ' .
		 'FROM prf_profils AS "prf" ' .
		 'LEFT JOIN idpr_idn_prf AS "idpr" ON idpr.prf_id = prf.prf_id ' .
		 'WHERE prf.prf_id = :prf_id ';

		 
		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':prf_id', $prf_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return $Query->fetchObject();
	}

} // Fin class IICA_Profiles


// ************ ================ ****************


class HBL_Profils_Controles_Acces extends HBL_Connexioneur_BD {

	function __construct() {
	/**
	* Connexion à la base de données.
	*
	* \license Copyleft Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-31
	*
	* \return Renvoi un booléen sur le succès de la connexion à la base de données
	*/
		parent::__construct();
		
		return true;
	}

	public function ajouterControleAcces( $Id_Profil, $Id_Application, $Id_Right ) {
	/**
	* Ajoute une Application à un Profil.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-31
	*
	* \param[in] $Id_Profil Identifiant du Profil à associer
	* \param[in] $Id_Application Identifiant de l'Application à associer
	* \param[in] $Id_Right Identifiant du Droit sur cette association
	*
	* \return Renvoi TRUE si l'association entre le Profil et l'Application a été créée, FALSE sinon. Lève une Exception en cas d'erreur.
	*/
		$Query = $this->prepareSQL( 'INSERT ' .
		 'INTO caa_controle_acces_application_interne ' .
		 '( prf_id, ain_id, drt_id ) ' .
		 'VALUES ( :prf_id, :ain_id, :rgh_id )' );
		
		$this->bindSQL( $Query, ':prf_id', $Id_Profil, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ain_id', $Id_Application, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':rgh_id', $Id_Right, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
 
 		return TRUE;
	}


	public function supprimerControleAcces( $Id_Profil, $Id_Application, $Id_Right = '' ) {
	/**
	* Supprime une Application à un Profil.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-31
	*
	* \param[in] $Id_Profil Identifiant du Profil à dissocier
	* \param[in] $Id_Application Identifiant de l'Application à dissocier
	*
	* \return Renvoi TRUE si l'association entre le Profil et l'Application a été supprimée, FALSE sinon. Lève une Exception en cas d'erreur.
	*/
		$Request = 'DELETE ' .
		 'FROM caa_controle_acces_application_interne ' .
		 'WHERE prf_id = :prf_id AND ain_id = :ain_id ';

		if ( $Id_Right != '' ) $Request .= 'AND drt_id = :rgh_id ';

		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':prf_id', $Id_Profil, PDO::PARAM_INT ) ;
		$this->bindSQL( $Query, ':ain_id', $Id_Application, PDO::PARAM_INT ) ;

		if ( $Id_Right != '' ) $this->bindSQL( $Query, ':rgh_id', $Id_Right, PDO::PARAM_INT ) ;
		
		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
 
 		return TRUE;
	}


	public function supprimerControlesAcces( $Id_Profil ) {
	/**
	* Supprime les Applications associées à un Profil.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-31
	*
	* \param[in] $Id_Profil Identifiant du Profil à dissocier
	*
	* \return Renvoi TRUE si les associations entre le Profil et les Applications ont été supprimées, FALSE sinon. Lève une Exception en cas d'erreur.
	*/
		$Query = $this->prepareSQL( 'DELETE ' .
		 'FROM caa_controle_acces_application_interne ' .
		 'WHERE prf_id = :prf_id ' );
		
		$this->bindSQL( $Query, ':prf_id', $Id_Profil, PDO::PARAM_INT ) ;
		
		$this->executeSQL( $Query );

		if ( $this->RowCount > 0 ) {
			return FALSE;
		}

 		return TRUE;
	}


	public function rechercherApplicationsParProfil( $prf_id ) {
	/**
	* Liste les Applications d'un Profil.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-31
	*
	* \param[in] $Id_Profil Identifiant du Profil de référence
	*
	* \return Renvoi un tableau d'occurrences d'Applications associées au Profil,
	*  sinon retourne un tableau vide
	*/
		$Query = $this->prepareSQL( 'SELECT ain.ain_id, ain_libelle, ain_localisation, caa.prf_id, caa.drt_id, drt.drt_code_libelle
FROM ain_applications_internes AS "ain"
LEFT JOIN (SELECT prf_id, drt_id, ain_id FROM caa_controle_acces_application_interne WHERE prf_id = :prf_id) AS "caa" ON caa.ain_id = ain.ain_id
LEFT JOIN drt_droits AS "drt" ON drt.drt_id = caa.drt_id
ORDER BY ain.ain_libelle, drt.drt_code_libelle ' );
		
		$this->bindSQL( $Query, ':prf_id', $prf_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );

		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function rechercherControleAccesApplications( $prf_id = '', $order = 'prv_libelle' ) {
		/**
		 * Lister les Contrôles d'Accès aux Applications.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2015-05-31
		 *
		 * \param[in] $prf_id Recherche les contrôles d'accès liés à un Profil.
		 * \param[in] $order Permet de trier le résultat selon un nom de colonne.
		 *
		 * \return Renvoi un tableau des Contrôles d'Accès trouvés, sinon un tableau vide
		 */


		$Request = 'SELECT
 caa.prf_id, prf.prf_libelle, caa.ain_id, ain.ain_libelle, ain.ain_localisation, caa.drt_id, drt.drt_code_libelle, lbr.lbr_libelle AS "drt_libelle"
 FROM caa_controle_acces_application_interne AS caa
 LEFT JOIN prf_profils AS prf ON caa.prf_id = prf.prf_id
 LEFT JOIN ain_applications_internes AS ain ON caa.ain_id = ain.ain_id
 LEFT JOIN drt_droits AS drt ON caa.drt_id = drt.drt_id
 LEFT JOIN lbr_libelles_referentiel AS lbr ON lbr_code = drt.drt_code_libelle
 WHERE lbr.lng_id = \'' . $_SESSION['Language'] . '\' ';
		
		if ( $prf_id != '' ) {
			$Request .= 'AND caa.prf_id = :prf_id ';
		}
		
		if ( $ain_id != '' ) {
			$Request .= 'AND caa.ain_id = :ain_id ';
		}
		
		if ( $drt_id != '' ) {
			$Request .= 'AND caa.drt_id = :drt_id ';
		}
		
		
		switch ( $order ) {
			default:
			case 'prf_libelle':
				$Request .= 'ORDER BY prf_libelle, caa.drt_id ';
				break;
				
			case 'prf_libelle-desc':
				$Request .= 'ORDER BY prf_libelle DESC, caa.drt_id ';
				break;
				
			case 'ain_libelle':
				$Request .= 'ORDER BY ain_libelle, caa.drt_id ';
				break;
				
			case 'ain_libelle-desc':
				$Request .= 'ORDER BY ain_libelle DESC, caa.drt_id ';
				break;
				
			case 'drt_code_libelle':
				$Request .= 'ORDER BY drt_code_libelle ';
				break;
				
			case 'drt_code_libelle-desc':
				$Request .= 'ORDER BY drt_code_libelle DESC ';
				break;
		}
		
		$Query = $this->prepareSQL( $Request );
		
		if ( $prf_id != '' ) $this->bindSQL( $Query, ':prf_id', $prf_id, PDO::PARAM_INT );
		
		if ( $app_id != '' ) $this->bindSQL( $Query, ':app_id', $app_id, PDO::PARAM_INT );
		
		if ( $drt_id != '' ) $this->bindSQL( $Query, ':drt_id', $drt_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function rechercherControleAcces( $prf_id = '', $ain_id = '', $drt_id = '', $order = 'prf_libelle' ) {
	/**
	* Lister les Contrôles d'Accès.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-31
	*
	* \param[in] $prf_id Recherche les contrôles d'accès liés à un Profil.
	* \param[in] $ain_id Recherche les contrôles d'accès liés à une Application.
	* \param[in] $drt_id Recherche les contrôles d'accès liés à un Droit.
	* \param[in] $order Permet de trier le résultat selon un nom de colonne.
	*
	* \return Renvoi un tableau des Contrôles d'Accès trouvés, sinon un tableau vide
	*/
		$Request = 'SELECT
 caa.prf_id, prf.prf_libelle, caa.ain_id, ain.ain_libelle, ain.ain_localisation, caa.drt_id, drt.drt_code_libelle, lbr.lbr_libelle AS "drt_libelle"
 FROM caa_controle_acces_application_interne AS caa
 LEFT JOIN prf_profils AS prf ON caa.prf_id = prf.prf_id
 LEFT JOIN ain_applications_internes AS ain ON caa.ain_id = ain.ain_id
 LEFT JOIN drt_droits AS drt ON caa.drt_id = drt.drt_id
 LEFT JOIN lbr_libelles_referentiel AS lbr ON lbr_code = drt.drt_code_libelle
 WHERE lbr.lng_id = \'' . $_SESSION['Language'] . '\' ';
		
		if ( $prf_id != '' ) {
			$Request .= 'AND caa.prf_id = :prf_id ';
		}

		if ( $ain_id != '' ) {
			$Request .= 'AND caa.ain_id = :ain_id ';
		}

		if ( $drt_id != '' ) {
			$Request .= 'AND caa.drt_id = :drt_id ';
		}


		switch ( $order ) {
		 default:
		 case 'prf_libelle':
			$Request .= 'ORDER BY prf_libelle, caa.drt_id ';
			break;
			
		 case 'prf_libelle-desc':
			$Request .= 'ORDER BY prf_libelle DESC, caa.drt_id ';
			break;

		 case 'ain_libelle':
			$Request .= 'ORDER BY ain_libelle, caa.drt_id ';
			break;
			
		 case 'ain_libelle-desc':
			$Request .= 'ORDER BY ain_libelle DESC, caa.drt_id ';
			break;

		 case 'drt_code_libelle':
			$Request .= 'ORDER BY drt_code_libelle ';
			break;
			
		 case 'drt_code_libelle-desc':
			$Request .= 'ORDER BY drt_code_libelle DESC ';
			break;
		}

		$Query = $this->prepareSQL( $Request );

		if ( $prf_id != '' ) $this->bindSQL( $Query, ':prf_id', $prf_id, PDO::PARAM_INT );

		if ( $app_id != '' ) $this->bindSQL( $Query, ':app_id', $app_id, PDO::PARAM_INT );

		if ( $drt_id != '' ) $this->bindSQL( $Query, ':drt_id', $drt_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );

		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function rechercherControleAccesParLibelle( $prf_libelle = '', $app_libelle = '', $drt_code_libelle = '', $order = 'profil' ) {
	/**
	* Lister les Contrôles d'Accès par les libellés.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-31
	*
	* \param[in] $prf_libelle Recherche les contrôles d'accès liés à un Profil.
	* \param[in] $app_libelle Recherche les contrôles d'accès liés à une Application.
	* \param[in] $drt_code_libelle Recherche les contrôles d'accès liés à un Droit.
	* \param[in] $order Permet de trier le résultat selon un nom de colonne.
	*
	* \return Renvoi un tableau des Contrôles d'Accès trouvés, sinon un tableau vide
	*/
		$Request = 'SELECT ' .
		 't1.prf_id, t2.prf_libelle, t1.app_id, t3.app_libelle, t1.drt_id, t4.drt_code_libelle ' .
		 'FROM cta_controle_acces AS t1 ' .
		 'LEFT JOIN prf_profils AS t2 ON t1.prf_id = t2.prf_id ' .
		 'LEFT JOIN app_applications AS t3 ON t1.app_id = t3.app_id ' .
		 'LEFT JOIN drt_droits AS t4 ON t1.drt_id = t4.drt_id ';
		
		if ( $prf_libelle != '' ) {
			$Request .= 'WHERE t2.prf_libelle like :prf_libelle ';
		}

		if ( $app_libelle != '' ) {
			$MyCriteria = 't3.app_libelle like :app_libelle ';

			if ( preg_match("/WHERE/i", $Request ) ) {
				$Request .= 'AND (' . $MyCriteria . ') ';
			} else {
				$Request .= 'WHERE ' . $MyCriteria . ' ';
			}
		}

		if ( $drt_code_libelle != '' ) {
			$MyCriteria = 't4.drt_code_libelle like :drt_code_libelle ';

			if ( preg_match("/WHERE/i", $Request ) ) {
				$Request .= 'AND (' . $MyCriteria . ') ';
			} else {
				$Request .= 'WHERE ' . $MyCriteria . ' ';
			}
		}

		switch ( $order ) {
		 default:
		 case 'profil':
			$Request .= 'ORDER BY prf_libelle ';
			break;
			
		 case 'profil-desc':
			$Request .= 'ORDER BY prf_libelle DESC ';
			break;

		 case 'application':
			$Request .= 'ORDER BY app_libelle ';
			break;
			
		 case 'application-desc':
			$Request .= 'ORDER BY app_libelle DESC ';
			break;

		 case 'right':
			$Request .= 'ORDER BY drt_code_libelle ';
			break;
			
		 case 'right-desc':
			$Request .= 'ORDER BY drt_code_libelle DESC ';
			break;
		}


		$Query = $this->prepareSQL( $Request );

		if ( $prf_libelle != '' ) {
			$prf_libelle = '%' . $prf_libelle . '%';
			$this->bindSQL( $Query, ':prf_libelle', $prf_libelle, PDO::PARAM_STR, L_PRF_LABEL );
		}

		if ( $app_libelle != '' ) {
			$app_libelle = '%' . $app_libelle . '%';
			$this->bindSQL( $Query, ':app_libelle', $app_libelle, PDO::PARAM_STR, L_APP_LABEL );
		}

		if ( $drt_code_libelle != '' ) {
			$drt_code_libelle = '%' . $drt_code_libelle . '%';
			$this->bindSQL( $Query, ':drt_code_libelle', $drt_code_libelle, PDO::PARAM_STR, L_RGH_LABEL );
		}

		
		$this->executeSQL( $Query );
		
 		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function rechercherDroits( $order = '', $Search = '' ) {
	/**
	* Lister les Profils.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-31
	*
	* \param[in] $order Ordre d'affichage des éléments dans la liste
	* \param[in] $Search Critère de recherche pour trouver des profils
	*
	* \return Renvoi un tableau des Profils trouvés, sinon un tableau vide. Lève une Exception en cas d'erreur.
	*/
		$Request = 'SELECT ' .
		 'drt_id, drt_code_libelle, lbr_libelle ' .
		 'FROM drt_droits AS "rgh" ' .
		 'LEFT JOIN lbr_libelles_referentiel AS "lbr" ON lbr.lbr_code = rgh.drt_code_libelle AND lbr.lng_id = \'' . $_SESSION['Language'] . '\' ';


		if ( $Search != '' ) {
			$Request .= 'AND drt_code_libelle LIKE :search OR lbr_libelle LIKE :search ';
		}
		
		switch ( $order ) {
		 default:
		 case 'label':
			$Request .= 'ORDER BY drt_code_libelle ';
			break;
			
		 case 'label-desc':
			$Request .= 'ORDER BY drt_code_libelle DESC ';
			break;
		}

		$Query = $this->prepareSQL( $Request );

		if ( $Search != '' ) {
			$this->bindSQL( $Query, ':search', '%' . $Search . '%', PDO::PARAM_STR, L_RGH_LABEL );
		}
		
		$this->executeSQL( $Query );
 
 		//return $Query->fetchAll( PDO::FETCH_CLASS );
 		while( $Occurrence = $Query->fetchObject() ) {
 			$Result[ $Occurrence->drt_code_libelle ] = array( 'id' => $Occurrence->drt_id, 'label' => $Occurrence->lbr_libelle );
 		}

 		return $Result;
	}


	public function rechercherDroitsVersMatrice() {
	/**
	* Lister les Droits sous forme de Matrice.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-10-22
	*
	* \param[in] $order Ordre d'affichage des éléments dans la liste
	* \param[in] $Search Critère de recherche pour trouver des profils
	*
	* \return Renvoi un tableau des Profils trouvés, sinon un tableau vide. Lève une Exception en cas d'erreur.
	*/
		$Request = 'SELECT caa.* ' .
			'FROM ain_applications_internes AS "ain" ' .
			'LEFT JOIN caa_controle_acces_application_interne AS "caa" ON caa.ain_id = ain.ain_id ' .
			'LEFT JOIN drt_droits AS "rgh" ON rgh.drt_id = caa.drt_id ' .
			'LEFT JOIN lbr_libelles_referentiel AS "lbr1" ON lbr1.lbr_code = rgh.drt_code_libelle AND lbr1.lng_id = \'' . $_SESSION['Language'] . '\' ' .
			'LEFT JOIN prf_profils AS "prf" ON prf.prf_id = caa.prf_id ' .
			'ORDER BY ain.ain_libelle, prf.prf_libelle, caa.drt_id ';

		$Query = $this->prepareSQL( $Request );
		
		$this->executeSQL( $Query );
 
 		//return $Query->fetchAll( PDO::FETCH_CLASS );
 		while( $Occurrence = $Query->fetchObject() ) {
 			$Result[ $Occurrence->prf_id.'-'.$Occurrence->ain_id.'-'.$Occurrence->drt_id] = 'O' ;
 		}

 		return $Result;
	}


	public function rechercherLibellesDroits() {
	/**
	* Lister les Libellés des Droits
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-10-22
	*
	* \param[in] $order Ordre d'affichage des éléments dans la liste
	* \param[in] $Search Critère de recherche pour trouver des profils
	*
	* \return Renvoi un tableau des Profils trouvés, sinon un tableau vide. Lève une Exception en cas d'erreur.
	*/
		$Request = 'SELECT drt_code_libelle, lbr_libelle FROM drt_droits AS rgh ' .
			'LEFT JOIN lbr_libelles_referentiel AS lbr1 ON lbr1.lbr_code = rgh.drt_code_libelle ' .
			'AND lbr1.lng_id = \'' . $_SESSION['Language'] . '\' ' .
			'ORDER BY drt_code_libelle ';

		$Query = $this->prepareSQL( $Request );
		
		$this->executeSQL( $Query );
 
 		//return $Query->fetchAll( PDO::FETCH_CLASS );
 		while( $Occurrence = $Query->fetchObject() ) {
 			$Result[ $Occurrence->drt_code_libelle ] = $Occurrence->lbr_libelle;
 		}

 		return $Result;
	}

} // Fin class = HBL_Profils_Controles_Acces

?>