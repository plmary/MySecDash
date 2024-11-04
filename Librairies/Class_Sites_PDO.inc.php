<?php

include_once( 'Constants.inc.php' );

include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Connecteur_BD_PDO.inc.php' );


// Centralisation de la taille des objets en base.
define( 'L_SITE_NOM', 50);


class Sites extends HBL_Connexioneur_BD {
/**
* Cette classe gère les Sites liés aux BIA.
*
* \license Copyleft Loxense
* \author Pierre-Luc MARY
* \date 2024-02-19
*/
	public function __construct() {
	/**
	* Connexion à la base de données en appelant le constructeur du Parent.
	*
	* \license Copyleft Loxense
	* \author Pierre-Luc MARY
	* \date 2024-01-19
	*
	* \return Renvoi un booléen sur le succès de la connexion à la base de données
	*/
		parent::__construct();
		
		return true;
	}


	/* ===============================================================================
	** Gestion des Entités
	*/
	
	public function majSite( $sct_id, $sts_nom, $sts_description='', $sts_id='' ) {
	/**
	* Créé ou actualise un Site.
	*
	* \license Copyleft Loxense
	* \author Pierre-Luc MARY
	* \date 2024-01-14
	*
	* \param[in] $sct_id Identifiant de la Société de rattachement du Site
	* \param[in] $sts_id Identifiant du Site à modifier (si précisé)
	* \param[in] $sts_nom Nom du Site
	* \param[in] $sts_description Descirption du Site
	*
	* \return Renvoi TRUE si le Site a Campagne a été créée ou modifiée, FALSE si l'entité n'existe pas ou lève une exception en cas d'erreur.
	*/
		if ( $sts_id == '' ) {
			$Query = $this->prepareSQL(
				'INSERT INTO sts_sites ' .
				'( sct_id, sts_nom, sts_description ) ' .
				'VALUES ( :sct_id, :sts_nom, :sts_description )'
				);
		} else {
			$Query = $this->prepareSQL(
				'UPDATE sts_sites SET ' .
				'sct_id  = :sct_id, ' .
				'sts_nom = :sts_nom, ' .
				'sts_description = :sts_description ' .
				'WHERE sts_id = :sts_id'
				);

			
			$this->bindSQL( $Query, ':sts_id', $sts_id, PDO::PARAM_INT );
		}

		$this->bindSQL( $Query, ':sct_id', $sct_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':sts_nom', $sts_nom, PDO::PARAM_STR, L_SITE_NOM );
		$this->bindSQL( $Query, ':sts_description', $sts_description, PDO::PARAM_LOB );

		$this->executeSQL( $Query );

		if ( $sts_id == '' ) {
			switch( $this->getAttribute(PDO::ATTR_DRIVER_NAME) ) {
			 case 'pgsql';
				$this->LastInsertId = $this->lastInsertId( 'sts_sites_sts_id_seq' );
				break;
			}
		}
	
		
		return TRUE;
	}


	public function majSiteParChamp( $ID, $Source, $Valeur ) {
		/**
		 * Créé ou actualise une Civilité.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2015-05-20
		 *
		 * \param[in] $ID Identifiant du Site à modifier
		 * \param[in] $Source Nom du champ à modifier
		 * \param[in] $Valeur Valeur à affecter au champ.
		 *
		 * \return Renvoi TRUE si le Site a été créée ou mise à jour, FALSE si l'entité n'existe pas. Lève une Exception en cas d'erreur.
		 */
		if ( $ID == '' ) return FALSE;

		$Request = 'UPDATE sts_sites SET ';
		
		switch ( $Source ) {
			case 'sts_nom':
				$Request .= 'sts_nom = :valeur ';
				break;
				
			case 'sts_description':
				$Request .= 'sts_description = :valeur ';
				break;
		}
		
		$Request .= 'WHERE sts_id = :sts_id ';
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':sts_id', $ID, PDO::PARAM_INT );
		
		
		switch ( $Source ) {
			case 'sts_nom':
				$this->bindSQL( $Query, ':valeur', $Valeur, PDO::PARAM_STR, L_SITE_NOM );
				break;
				
			case 'sts_description':
				$this->bindSQL( $Query, ':valeur', $Valeur, PDO::PARAM_LOB );
				break;
		}
		
		
		$this->executeSQL( $Query );
		
		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
		
		
		return TRUE;
	}


	public function rechercherSites( $sct_id, $orderBy = 'sts_nom', $sts_id = '' ) {
	/**
	* Lister les Sites.
	*
	* \license Copyleft Loxense
	* \author Pierre-Luc MARY
	* \date 2024-02-19
	*
	* \param[in] $sct_id ID de la Société pour lesquelles on recherche les Entités.
	* \param[in] $orderBy Permet de gérer l'ordre d'affichage.
	* \param[in] $sts_id Permet de rechercher un Site Spécifique
	*
	* \return Renvoi un tableau d'objet ou un tableau vide si pas de données trouvées. Lève une exception en cas d'erreur.
	*/

		$Request = 'SELECT 
sts.*
FROM sts_sites AS "sts" ';

		if ($sct_id != '*' ) {
			$Where = 'WHERE sts.sct_id = :sct_id ';
		} else {
			$Where = '';
		}

		if ($sts_id != '') {
			if ($Where == '') {
				$Where .= 'WHERE ';
			} else {
				$Where .= 'AND ';
			}

			$Where .= 'sts.sts_id = :sts_id ';
		}

		$Request .= $Where;

		switch( $orderBy ) {
		 default:
		 case 'sts_nom':
			$Request .= 'ORDER BY sts_nom ';
			break;

		 case 'sts_nom-desc':
			$Request .= 'ORDER BY sts_nom DESC ';
			break;

		 case 'sts_description':
			$Request .= 'ORDER BY sts_description ';
			break;
			
		 case 'sts_description-desc':
			$Request .= 'ORDER BY sts_description DESC ';
			break;
		}

		$Query = $this->prepareSQL( $Request );

		if ($sct_id != '*' ) {
			$this->bindSQL( $Query, ':sct_id', $sct_id, PDO::PARAM_INT );
		}

		if ($sts_id != '' ) {
			$this->bindSQL( $Query, ':sts_id', $sts_id, PDO::PARAM_INT );
		}

		$this->executeSQL( $Query );

		return $Query->fetchAll( PDO::FETCH_CLASS );
	}
	
	
	public function rechercherSitesParCampagne( $cmp_id ) {
		/**
		 * Lister les Sites par Campagne.
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-09-09
		 *
		 * \param[in] $cmp_id ID de la Campagne pour laquelle on recherche les Sites.
		 *
		 * \return Renvoi un tableau d'objet ou un tableau vide si pas de données trouvées. Lève une exception en cas d'erreur.
		 */
		
		$Request = 'SELECT
sts.*
FROM cmst_cmp_sts AS "cmst"
LEFT JOIN sts_sites AS "sts" ON sts.sts_id = cmst.sts_id
WHERE cmst.cmp_id = :cmp_id
ORDER BY sts_nom ';
		
		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		return $Query->fetchAll( PDO::FETCH_CLASS );
	}
	

	public function SiteEstAssociee( $sts_id ) {
	/**
	* Récupère les nombres d'association d'un Site.
	*
	* \license Copyleft Loxense
	* \author Pierre-Luc MARY
	* \date 2024-02-19
	*
	* \param[in] $sts_id Identifiant du Site à récupérer
	*
	* \return Renvoi l'occurrence listant les associations du Site ou FALSE si pas d'entité. Lève une Exception en cas d'erreur.
	*/
		$Request = 'SELECT ' .
			'count(DISTINCT cmst.cmp_id) AS "total_cmp" ' .
			'FROM sts_sites AS "sts" ' .
			'LEFT JOIN cmst_cmp_sts AS "cmst" ON cmst.sts_id = sts.sts_id ' .
			'WHERE sts.sts_id = :sts_id ';
		
		 
		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':sts_id', $sts_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return $Query->fetchObject();
	}


	public function supprimerSite( $sts_id ) {
	/**
	* Supprimer un Site.
	*
	* \license Copyleft Loxense
	* \author Pierre-Luc MARY
	* \date 2024-02-19
	*
	* \param[in] $sts_id Identifiant du Site à supprimer
	*
	* \return Renvoi TRUE si le Site a été supprimé ou FALSE si la Campagne n'existe pas. Lève une Exception en cas d'erreur.
	*/

		$Query = $this->prepareSQL( 'DELETE ' .
			'FROM sts_sites ' .
			'WHERE sts_id = :sts_id' );

		$this->bindSQL( $Query, ':sts_id', $sts_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return TRUE;
	}


	public function totalSites( $sct_id = '' ) {
	/**
	* Calcul le nombre total de Campagnes.
	*
	* \license Copyleft Loxense
	* \author Pierre-Luc MARY
	* \date 2024-01-14
	*
	* \return Renvoi le nombre total de Campagnes stockées en base. Lève une Exception en cas d'erreur.
	*/

		$Request = 'SELECT ' .
		 'count(*) AS total ' .
		 'FROM sts_sites ' ;

		if ( $sct_id != '' ) {
			$Request .= 'WHERE sct_id = :sct_id ';
		}

		$Query = $this->prepareSQL( $Request );

		if ( $sct_id != '' ) {
			$this->bindSQL( $Query, ':sct_id', $sct_id, PDO::PARAM_INT );
		}

		
		$this->executeSQL( $Query );
		
		$Occurrence = $Query->fetchObject() ;
		
		return $Occurrence->total;
	}

} // Fin class Sites

?>