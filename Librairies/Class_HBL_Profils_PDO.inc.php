<?php

include_once( 'Constants.inc.php' );
include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Connecteur_BD_PDO.inc.php' );

if ( ! defined( 'L_PRF_LABEL' ) ) define( 'L_PRF_LABEL', 40 );
if ( ! defined( 'L_APP_LABEL' ) ) define( 'L_APP_LABEL', 50 );
if ( ! defined( 'L_RGH_LABEL' ) ) define( 'L_RGH_LABEL', 30 );


class HBL_Profils extends HBL_Connecteur_BD {
/**
* Cette classe gère les profils.
*
* PHP version 5
* @license Copyright Loxense
* @author Pierre-Luc MARY
* @date 2015-05-31
*/

	public function __construct() {
	/**
	* Connexion à la base de données.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-31
	*
	* @return Renvoi un booléen sur le succès de la connexion à la base de données
	*/
		parent::__construct();
		
		return TRUE;
	}


	/* ===============================================================================
	** Gestion des Profils
	*/
	
	public function majProfil( $prf_id = '', $Label ) {
	/**
	* Créé ou actualise un Profil.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-31
	*
	* @param[in] $prf_id Identifiant du Profil à mettre à jour s'il est précisé
	* @param[in] $Label Libellé à donner au Profil
	*
	* @return Renvoi TRUE si le Profil a été créé ou mis à jour, sinon FALSE. Lève une exception en cas d'erreur.
	*/

		if ( $prf_id == '' ) {
			$Command = 'INSERT : ' ;

			$Query = $this->prepareSQL( 'INSERT INTO prf_profils ' .
				'( prf_libelle ) ' .
				'VALUES ( :Label )' );
		} else {
			$Command = 'UPDATE : ' ;

			$Query = $this->prepareSQL( 'UPDATE prf_profils SET ' .
				'prf_libelle = :Label ' .
				'WHERE prf_id = :prf_id' );

			$this->bindSQL( $Query, ':prf_id', $prf_id, PDO::PARAM_INT );
		}
		
		$this->bindSQL( $Query, ':Label', $Label, PDO::PARAM_STR, L_PRF_LABEL );
		
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


	public function rechercherProfils( $order = '', $Search = '' ) {
	/**
	* Lister les Profils.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-31
	*
	* @param[in] $order Ordre d'affichage des éléments dans la liste
	* @param[in] $Search Critère de recherche pour trouver des profils
	*
	* @return Renvoi un tableau des Profils trouvés, sinon un tableau vide. Lève une Exception en cas d'erreur.
	*/
		$Request = 'SELECT ' .
		 'prf_id, prf_libelle ' .
		 'FROM prf_profils ' ;
		
		switch ( $order ) {
		 default:
		 case 'label':
			$Request .= 'ORDER BY prf_libelle ';
			break;
			
		 case 'label-desc':
			$Request .= 'ORDER BY prf_libelle DESC ';
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
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-31
	*
	* @param[in] $prf_id Identifiant du Profil à récupérer
	*
	* @return Renvoi l'instance du Profil trouvé, sinon une instance vide. Lève une Exception en cas d'erreur.
	*/
		$Request = 'SELECT ' .
		 'prf_libelle ' .
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
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-31
	*
	* @param[in] $prf_id Identifiant du Profil à supprimer
	*
	* @return Renvoi TRUE si le Profil a été supprimé, sinon FALSE. Lève une Exception en cas d'erreur.
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
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-31
	*
	* @return Renvoi un entier représentant le total de Profils trouvé en base
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
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-15
	*
	* @param[in] $prf_id Identifiant du Profil à récupérer
	*
	* @return Renvoi l'occurrence listant les association du Profil ou FALSE si pas de Profil. Lève une Exception en cas d'erreur.
	*/
		$Request = 'SELECT ' .
		 'COUNT(DISTINCT idn_id) AS total_idn ' .
		 'FROM prf_profils AS prf ' .
		 'LEFT JOIN idpr_idn_prf AS idpr ON idpr.prf_id = prf.prf_id ' .
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


class HBL_Profils_Controles_Acces extends HBL_Connecteur_BD {

	function __construct() {
	/**
	* Connexion à la base de données.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-31
	*
	* @return Renvoi un booléen sur le succès de la connexion à la base de données
	*/
		parent::__construct();
		
		return true;
	}

	public function ajouterControleAcces( $Id_Profil, $Id_Application, $Id_Right ) {
	/**
	* Ajoute une Application à un Profil.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-31
	*
	* @param[in] $Id_Profil Identifiant du Profil à associer
	* @param[in] $Id_Application Identifiant de l'Application à associer
	* @param[in] $Id_Right Identifiant du Droit sur cette association
	*
	* @return Renvoi TRUE si l'association entre le Profil et l'Application a été créée, FALSE sinon. Lève une Exception en cas d'erreur.
	*/
		$Query = $this->prepareSQL( 'INSERT ' .
		 'INTO cta_controle_acces ' .
		 '( prf_id, app_id, drt_id ) ' .
		 'VALUES ( :Prf_id, :App_id, :Rgh_id )' );
		
		$this->bindSQL( $Query, ':Prf_id', $Id_Profil, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':App_id', $Id_Application, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':Rgh_id', $Id_Right, PDO::PARAM_INT );
		
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
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-31
	*
	* @param[in] $Id_Profil Identifiant du Profil à dissocier
	* @param[in] $Id_Application Identifiant de l'Application à dissocier
	*
	* @return Renvoi TRUE si l'association entre le Profil et l'Application a été supprimée, FALSE sinon. Lève une Exception en cas d'erreur.
	*/
		$Request = 'DELETE ' .
		 'FROM cta_controle_acces ' .
		 'WHERE prf_id = :Prf_id AND app_id = :App_id ';

		if ( $Id_Right != '' ) $Request .= 'AND drt_id = :Rgh_id ';

		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':Prf_id', $Id_Profil, PDO::PARAM_INT ) ;
		$this->bindSQL( $Query, ':App_id', $Id_Application, PDO::PARAM_INT ) ;

		if ( $Id_Right != '' ) $this->bindSQL( $Query, ':Rgh_id', $Id_Right, PDO::PARAM_INT ) ;
		
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
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-31
	*
	* @param[in] $Id_Profil Identifiant du Profil à dissocier
	*
	* @return Renvoi TRUE si les associations entre le Profil et les Applications ont été supprimées, FALSE sinon. Lève une Exception en cas d'erreur.
	*/
		$Query = $this->prepareSQL( 'DELETE ' .
		 'FROM cta_controle_acces ' .
		 'WHERE prf_id = :Prf_id ' );
		
		$this->bindSQL( $Query, ':Prf_id', $Id_Profil, PDO::PARAM_INT ) ;
		
		$this->executeSQL( $Query );

		if ( $this->RowCount > 0 ) {
			return FALSE;
		}

 		return TRUE;
	}


	public function rechercherApplicationsParProfil( $Id_Profil ) {
	/**
	* Liste les Applications d'un Profil.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-31
	*
	* @param[in] $Id_Profil Identifiant du Profil de référence
	*
	* @return Renvoi un tableau d'occurrences d'Applications associées au Profil,
	*  sinon retourne un tableau vide
	*/
		$Query = $this->prepareSQL( 'SELECT ' .
		 't1.app_id, t1.drt_id, t2.app_code, t2.app_libelle, t3.drt_code_libelle ' .
		 'FROM cta_controle_acces AS t1 ' .
		 'LEFT JOIN app_applications AS t2 ON t1.app_id = t2.app_id ' .
		 'LEFT JOIN drt_droits AS t3 ON t1.drt_id = t3.drt_id ' .
		 'WHERE prf_id = :Prf_id ' );
		
		$this->bindSQL( $Query, ':Prf_id', $Id_Profil, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
 
 		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function rechercherControleAcces( $prf_id = '', $app_id = '', $drt_id = '', $order = 'profil' ) {
	/**
	* Lister les Contrôles d'Accès.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-31
	*
	* @param[in] $prf_id Recherche les contrôles d'accès liés à un Profil.
	* @param[in] $app_id Recherche les contrôles d'accès liés à une Application.
	* @param[in] $drt_id Recherche les contrôles d'accès liés à un Droit.
	* @param[in] $order Permet de trier le résultat selon un nom de colonne.
	*
	* @return Renvoi un tableau des Contrôles d'Accès trouvés, sinon un tableau vide
	*/
		$Request = 'SELECT ' .
		 't1.prf_id, t2.prf_libelle, t1.app_id, t3.app_libelle, t1.drt_id, t4.drt_code_libelle, rlb.lbr_libelle ' .
		 'FROM cta_controle_acces AS t1 ' .
		 'LEFT JOIN prf_profils AS t2 ON t1.prf_id = t2.prf_id ' .
		 'LEFT JOIN app_applications AS t3 ON t1.app_id = t3.app_id ' .
		 'LEFT JOIN drt_droits AS t4 ON t1.drt_id = t4.drt_id ' .
		 'LEFT JOIN lbr_libelles_referentiel AS rlb ON lbr_code = t4.drt_code_libelle ' .
		 'WHERE rlb.lng_id = \'' . $_SESSION['Language'] . '\' ';
		
		if ( $prf_id != '' ) {
			$Request .= 'AND t1.prf_id = :prf_id ';
		}

		if ( $app_id != '' ) {
			$Request .= 'AND t1.app_id = :app_id ';
		}

		if ( $drt_id != '' ) {
			$Request .= 'AND t1.drt_id = :drt_id ';
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
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-31
	*
	* @param[in] $prf_libelle Recherche les contrôles d'accès liés à un Profil.
	* @param[in] $app_libelle Recherche les contrôles d'accès liés à une Application.
	* @param[in] $drt_code_libelle Recherche les contrôles d'accès liés à un Droit.
	* @param[in] $order Permet de trier le résultat selon un nom de colonne.
	*
	* @return Renvoi un tableau des Contrôles d'Accès trouvés, sinon un tableau vide
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
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-31
	*
	* @param[in] $order Ordre d'affichage des éléments dans la liste
	* @param[in] $Search Critère de recherche pour trouver des profils
	*
	* @return Renvoi un tableau des Profils trouvés, sinon un tableau vide. Lève une Exception en cas d'erreur.
	*/
		$Request = 'SELECT ' .
		 'drt_id, drt_code_libelle, lbr_libelle ' .
		 'FROM drt_droits AS rgh ' .
		 'LEFT JOIN lbr_libelles_referentiel AS rlb ON rlb.lbr_code = rgh.drt_code_libelle ' .
		 'WHERE rlb.lng_id = \'' . $_SESSION['Language'] . '\' ';

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
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-10-22
	*
	* @param[in] $order Ordre d'affichage des éléments dans la liste
	* @param[in] $Search Critère de recherche pour trouver des profils
	*
	* @return Renvoi un tableau des Profils trouvés, sinon un tableau vide. Lève une Exception en cas d'erreur.
	*/
		$Request = 'SELECT accn.* ' . //app.app_id, app.app_libelle, accn.prf_id, prf.prf_libelle, accn.drt_id, rlb1.lbr_libelle ' .
			'FROM app_applications AS app ' .
			'LEFT JOIN cta_controle_acces AS accn ON accn.app_id = app.app_id ' .
			'LEFT JOIN drt_droits AS rgh ON rgh.drt_id = accn.drt_id ' .
			'LEFT JOIN lbr_libelles_referentiel AS rlb1 ON rlb1.lbr_code = rgh.drt_code_libelle ' .
			'LEFT JOIN prf_profils AS prf ON prf.prf_id = accn.prf_id ' .
			'WHERE rlb1.lng_id = \'' . $_SESSION['Language'] . '\' ' .
			'ORDER BY app.app_libelle, prf.prf_libelle, accn.drt_id ';

		$Query = $this->prepareSQL( $Request );
		
		$this->executeSQL( $Query );
 
 		//return $Query->fetchAll( PDO::FETCH_CLASS );
 		while( $Occurrence = $Query->fetchObject() ) {
 			$Result[ $Occurrence->prf_id.'-'.$Occurrence->app_id.'-'.$Occurrence->drt_id] = 'O' ;
 		}

 		return $Result;
	}


	public function rechercherLibellesDroits() {
	/**
	* Lister les Libellés des Droits
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-10-22
	*
	* @param[in] $order Ordre d'affichage des éléments dans la liste
	* @param[in] $Search Critère de recherche pour trouver des profils
	*
	* @return Renvoi un tableau des Profils trouvés, sinon un tableau vide. Lève une Exception en cas d'erreur.
	*/
		$Request = 'SELECT drt_code_libelle, lbr_libelle FROM drt_droits AS rgh ' .
			'LEFT JOIN lbr_libelles_referentiel AS rlb1 ON rlb1.lbr_code = rgh.drt_code_libelle ' .
			'WHERE rlb1.lng_id = \'' . $_SESSION['Language'] . '\' ' .
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