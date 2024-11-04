<?php

include_once( 'Constants.inc.php' );
include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Connecteur_BD_PDO.inc.php' );


class HBL_Historiques extends HBL_Connexioneur_BD {
/**
* Cette classe gère les accès en consultation à l'historique des événements réalisées dans Loxense.
*
* \license Copyright Loxense
* \author Pierre-Luc MARY
* \date 2015-05-20
*/

	public function __construct() {
	/**
	* Connexion à la base de données.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-20
	*
	* \return Renvoi un booléen sur le succès de la connexion à la base de données
	*/
		parent::__construct();
		
		return TRUE;
	}


	public function rechercherEvenements( $Order = 'hac_date',
		$date_debut = '', $date_fin = '', $tpa_id = '', $tpo_id = '', $user = '', $ip_user = '', $detail = '', $crs_id = '' ) {
	/**
	* Lister les événements contenu dans l'historique.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-20
	*
	* \param[in] $Order Permet de gérer l'ordre d'affichage
	* \param[in] $Search Chaîne à rechercher dans les colonnes constituants une Civilité
	*
	* \return Renvoi une liste de civilité ou une liste vide. Lève une exception en cas d'erreur.
	*/
		$Request = 'SELECT ' .
			'hac.*, tpa.tpa_code_libelle, rlb_tpa.lbr_libelle AS libelle_tpa, tpo.tpo_code_libelle, rlb_tpo.lbr_libelle AS libelle_tpo ' .
			'FROM hac_historiques_activites AS hac ' .
			'LEFT JOIN tpa_types_action AS tpa ON tpa.tpa_id = hac.tpa_id ' .
			'LEFT JOIN tpo_types_objet AS tpo ON tpo.tpo_id = hac.tpo_id ' .
			'LEFT JOIN lbr_libelles_referentiel AS rlb_tpa ON tpa.tpa_code_libelle = rlb_tpa.lbr_code ' .
			'LEFT JOIN lbr_libelles_referentiel AS rlb_tpo ON tpo.tpo_code_libelle = rlb_tpo.lbr_code ' .
			'WHERE rlb_tpa.lng_id = \'' . $_SESSION['Language'] . '\' AND rlb_tpo.lng_id = \'' . $_SESSION['Language'] . '\' ';

		if ( $crs_id != '' ) {
			$Request .= 'AND crs_id = :crs_id ';
		}

		if ( $date_debut != '' ) {
			$Request .= 'AND hac_date >= :date_debut ';
		}

		if ( $date_fin != '' ) {
			$Request .= 'AND hac_date <= :date_fin ';
		}

		if ( $tpa_id != '' ) {
			$Request .= 'AND hac.tpa_id = :tpa_id ';
		}

		if ( $tpo_id != '' ) {
			$Request .= 'AND hac.tpo_id = :tpo_id ';
		}

		if ( $user != '' ) {
			$Request .= 'AND hac.hac_utilisateur like :user ';
		}

		if ( $ip_user != '' ) {
			$Request .= 'AND hac.hac_ip_utilisateur like :ip_user ';
		}

		if ( $detail != '' ) {
			$Request .= 'AND hac.hac_detail like :detail ';
		}
		
		switch( $Order ) {
		 case 'libelle_tpa':
		 default:
			$Request .= 'ORDER BY libelle_tpa, hac_date DESC ';
			break;

		 case 'libelle_tpa-desc':
			$Request .= 'ORDER BY libelle_tpa DESC, hac_date DESC ';
			break;

		 case 'libelle_tpo':
			$Request .= 'ORDER BY libelle_tpo, hac_date DESC ';
			break;

		 case 'libelle_tpo-desc':
			$Request .= 'ORDER BY libelle_tpo DESC, hac_date DESC ';
			break;

		 case 'hac_date':
			$Request .= 'ORDER BY hac_date ';
			break;

		 case 'hac_date-desc':
			$Request .= 'ORDER BY hac_date DESC ';
			break;

		 case 'hac_utilisateur':
			$Request .= 'ORDER BY hac_utilisateur, hac_date DESC ';
			break;

		 case 'hac_utilisateur-desc':
			$Request .= 'ORDER BY hac_utilisateur DESC, hac_date DESC ';
			break;

		 case 'hac_ip_utilisateur':
			$Request .= 'ORDER BY hac_ip_utilisateur, hac_date DESC ';
			break;

		 case 'hac_ip_utilisateur-desc':
			$Request .= 'ORDER BY hac_ip_utilisateur DESC, hac_date DESC ';
			break;

		 case 'hac_detail':
			$Request .= 'ORDER BY hac_detail, hac_date DESC ';
			break;

		 case 'hac_detail-desc':
			$Request .= 'ORDER BY hac_detail DESC, hac_date DESC ';
			break;
		}
		 
		$Query = $this->prepareSQL( $Request );


		if ( $crs_id != '' ) {
			$this->bindSQL( $Query, ':crs_id', $crs_id, PDO::PARAM_INT );
		}

		if ( $date_debut != '' ) {
			$this->bindSQL( $Query, ':date_debut', $date_debut, PDO::PARAM_STR, 20 );
		}

		if ( $date_fin != '' ) {
			$this->bindSQL( $Query, ':date_fin', $date_fin, PDO::PARAM_STR, 20 );
		}

		if ( $tpa_id != '' ) {
			$this->bindSQL( $Query, ':tpa_id', $tpa_id, PDO::PARAM_INT );
		}

		if ( $tpo_id != '' ) {
			$this->bindSQL( $Query, ':tpo_id', $tpo_id, PDO::PARAM_INT );
		}

		if ( $user != '' ) {
			$user = '%' . $user . '%';
			$this->bindSQL( $Query, ':user', $user, PDO::PARAM_STR, 60 );
		}

		if ( $ip_user != '' ) {
			$ip_user = '%' . $ip_user . '%';
			$this->bindSQL( $Query, ':ip_user', $ip_user, PDO::PARAM_STR, 40 );
		}

		if ( $detail != '' ) {
			$detail = '%' . $detail . '%';
			$this->bindSQL( $Query, ':detail', $detail, PDO::PARAM_STR, 20 );
		}


		$this->executeSQL( $Query );
 
 		return $Query->fetchAll( PDO::FETCH_CLASS );
	}

/*
	public function detaillerCivilite( $cvl_id ) {
	/**
	* Récupère les informations d'une Civilité.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-20
	*
	* \param[in] $cvl_id Identifiant de la civilité à afficher
	*
	* \return Renvoi l'occurrence de la civilité ou renvoi FALSE si la civilité n'existe pas. Lève une Exception en cas d'erreur.
	*/
/*		$Request = 'SELECT ' .
		 '* ' .
		 'FROM cvl_civilites ' .
		 'WHERE cvl_id = :cvl_id ' ;
		 
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':cvl_id', $cvl_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
		
		return $Query->fetchObject() ;
	}


	public function supprimerCivilite( $cvl_id ) {
	/**
	* Supprime une Civilité.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-21
	*
	* \param[in] $cvl_id Identifiant de la civilité à supprimer
	*
	* \return Renvoi TRUE si l'occurrence a été supprimée, FALSE si la civilité n'existe pas. Lève une Exception en cas d'erreur.
	*/
/*		$Query = $this->prepareSQL( 'DELETE ' .
			 'FROM cvl_civilites ' .
			 'WHERE cvl_id = :cvl_id' );

		$this->bindSQL( $Query, ':cvl_id', $cvl_id, PDO::PARAM_INT ) ;
		
		$this->executeSQL( $Query );

 		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

 		return TRUE;
	}
*/

	public function totalEvenements() {
	/**
	* Calcul le nombre total d'événements dans l'Historique.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-21
	*
	* \return Renvoi le total d es occurrences trouvé. Lève une Exception en cas d'erreur.
	*/
		$Request = 'SELECT ' .
		 'count(*) AS total ' .
		 'FROM hac_historiques_activites ';

		$Query = $this->prepare( $Request );

		$this->executeSQL( $Query );

		$Occurrence = $Query->fetchObject() ;
		
		return $Occurrence->total;
	}


	public function listActionTypes() {
		$Request = 'SELECT ' .
		 'tpa.*, rlb_tpa.lbr_libelle AS libelle_tpa ' .
		 'FROM tpa_types_action AS tpa ' .
		 'LEFT JOIN lbr_libelles_referentiel AS rlb_tpa ON tpa.tpa_code_libelle = rlb_tpa.lbr_code ' .
		 'WHERE rlb_tpa.lng_id = \'' . $_SESSION['Language'] . '\' ' .
		 'ORDER BY libelle_tpa ';
		 
		$Query = $this->prepareSQL( $Request );
		
		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

 		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function listObjectTypes() {
		$Request = 'SELECT ' .
		 'tpo.*, rlb_tpo.lbr_libelle AS libelle_tpo ' .
		 'FROM tpo_types_objet AS tpo ' .
		 'LEFT JOIN lbr_libelles_referentiel AS rlb_tpo ON tpo.tpo_code_libelle = rlb_tpo.lbr_code ' .
		 'WHERE rlb_tpo.lng_id = \'' . $_SESSION['Language'] . '\' ' .
		 'ORDER BY libelle_tpo ';
		 
		$Query = $this->prepareSQL( $Request );
		
		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

 		return $Query->fetchAll( PDO::FETCH_CLASS );
	}

} // Fin class HBL_Historiques

?>