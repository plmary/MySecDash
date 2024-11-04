<?php

include_once( 'Constants.inc.php' );
include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Connecteur_BD_PDO.inc.php' );


define( 'L_PRF_LABEL', 40 );
define( 'L_APP_LABEL', 50 );
define( 'L_RGH_LABEL', 30 );


class HBL_Profils_Controles_Acces extends HBL_Connexioneur_BD {

	function __construct() {
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
		$this->prepareSQL( 'INSERT ' .
		 'INTO prac_profiles_access_control ' .
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


	public function supprimerControleAcces( $Id_Profil, $Id_Application ) {
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
		$Query = $this->prepareSQL( 'DELETE ' .
		 'FROM prac_profiles_access_control ' .
		 'WHERE prf_id = :Prf_id AND app_id = :App_id ' );
		
		$this->bindSQL( $Query, ':Prf_id', $Id_Profil, PDO::PARAM_INT ) ;
		$this->bindSQL( $Query, ':App_id', $Id_Application, PDO::PARAM_INT ) ;
		
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
		 'FROM prac_profiles_access_control ' .
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
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-31
	*
	* \param[in] $Id_Profil Identifiant du Profil de référence
	*
	* \return Renvoi un tableau d'occurrences d'Applications associées au Profil,
	*  sinon retourne un tableau vide
	*/
		$Query = $this->prepareSQL( 'SELECT ' .
		 't1.app_id, t1.drt_id, t2.app_code, t2.app_libelle, t3.drt_code_libelle ' .
		 'FROM prac_profiles_access_control AS t1 ' .
		 'LEFT JOIN app_applications AS t2 ON t1.app_id = t2.app_id ' .
		 'LEFT JOIN drt_droits AS t3 ON t1.drt_id = t3.drt_id ' .
		 'WHERE prf_id = :Prf_id ' );
		
		$this->bindSQL( $Query, ':Prf_id', $Id_Profil, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
 
 		return $Query->fetchAll( FETCH_CLASS );
	}


	public function rechercherControleAcces( $prf_id = '', $app_id = '', $drt_id = '', $order = 'profil' ) {
	/**
	* Lister les Contrôles d'Accès.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-31
	*
	* \param[in] $prf_id Recherche les contrôles d'accès liés à un Profil.
	* \param[in] $app_id Recherche les contrôles d'accès liés à une Application.
	* \param[in] $drt_id Recherche les contrôles d'accès liés à un Droit.
	* \param[in] $order Permet de trier le résultat selon un nom de colonne.
	*
	* \return Renvoi un tableau des Contrôles d'Accès trouvés, sinon un tableau vide
	*/
		$Request = 'SELECT ' .
		 't1.prf_id, t2.prf_libelle, t1.app_id, t3.app_libelle, t1.drt_id, t4.drt_code_libelle ' .
		 'FROM prac_profiles_access_control AS t1 ' .
		 'LEFT JOIN prf_profils AS t2 ON t1.prf_id = t2.prf_id ' .
		 'LEFT JOIN app_applications AS t3 ON t1.app_id = t3.app_id ' .
		 'LEFT JOIN drt_droits AS t4 ON t1.drt_id = t4.drt_id ';
		
		if ( $prf_id != '' ) {
			$Request .= 'WHERE t1.prf_id = :prf_id ';
		}

		if ( $app_id != '' ) {
			$MyCriteria = 't1.app_id = :app_id ';

			if ( preg_match("/WHERE/i", $Request ) ) {
				$Request .= 'AND (' . $MyCriteria . ') ';
			} else {
				$Request .= 'WHERE ' . $MyCriteria . ' ';
			}
		}

		if ( $drt_id != '' ) {
			$MyCriteria = 't1.drt_id = :drt_id ';

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

		$this->bindSQL( $Query, ':prf_id', $prf_id, PDO::PARAM_INT );

		$this->bindSQL( $Query, ':app_id', $app_id, PDO::PARAM_INT );

		$this->bindSQL( $Query, ':drt_id', $drt_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );

 		return $Query->fetchAll( FETCH_CLASS );
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
		 'FROM prac_profiles_access_control AS t1 ' .
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
		
 		return $Query->fetchAll( FETCH_CLASS );
	}

}

?>