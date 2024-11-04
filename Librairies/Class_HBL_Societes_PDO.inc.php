<?php

include_once( 'Constants.inc.php' );

include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Connecteur_BD_PDO.inc.php' );


// Centralisation de la taille des objets en base.
define( 'L_SCT_NOM', 100);


class HBL_Societes extends HBL_Connexioneur_BD {
/**
* Cette classe gère les Sociétés.
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
	* \date 2016-11-07
	*
	* \return Renvoi un booléen sur le succès de la connexion à la base de données
	*/
		parent::__construct();
		
		return true;
	}


	/* 
	 * ===============================================================================
	*/
	
	public function majSociete( $sct_id, $sct_nom, $sct_description = '' ) {
	/**
	* Créé ou actualise une Societé.
	*
	* \license Copyleft Loxense
	* \author Pierre-Luc MARY
	* \date 2023-12-22
	*
	* \param[in] $sct_id Identifiant de la Société à modifier (si précisé)
	* \param[in] $sct_nom Nom de la Société
	* \param[in] $sct_description Description de la Société
	*
	* \return Renvoi TRUE si l'entité a été créée ou modifiée, FALSE si l'entité n'existe pas ou lève une exception en cas d'erreur.
	*/
		
		if ( $sct_id == '' ) {
			$Request = 'INSERT INTO sct_societes (sct_nom';
			
			if ( $sct_description != '' ) {
				$Request .= ', sct_description';
			}
			
			$Request .= ') VALUES (:nom';

			if ( $sct_description != '' ) {
				$Request .= ', :description ';
			}
			
			$Request .= ') ';
			
			$Query = $this->prepareSQL( $Request );
		} else {
			$Request = 'UPDATE sct_societes SET sct_nom = :nom ';
			
			if ( $sct_description != '' ) {
				$Request .= ', sct_description = :description ';
			}
			
			$Request .= 'WHERE sct_id = :id ';
			
			$Query = $this->prepareSQL( $Request );
			
			$this->bindSQL( $Query, ':id', $sct_id, PDO::PARAM_INT );
		}

		$this->bindSQL( $Query, ':nom', $sct_nom, PDO::PARAM_STR, L_SCT_NOM );

		if ( $sct_description != '' ) {
			$this->bindSQL( $Query, ':description', $sct_description, PDO::PARAM_LOB );
		}

		$this->executeSQL( $Query );

		if ( $sct_id == '' ) {
			switch( $this->getAttribute(PDO::ATTR_DRIVER_NAME) ) {
			 case 'pgsql';
				$this->LastInsertId = $this->lastInsertId( 'sct_societes_sct_id_seq' );
				break;
			}

			// L'utilisateur venant de créer la Société, on lui joute dans sa liste des Sociétés autorisées.
			$Request = 'INSERT INTO idsc_idn_sct ( idn_id, sct_id ) VALUES ( :idn_id, :sct_id ) ';
			$Query = $this->prepareSQL( $Request );
			$this->bindSQL( $Query, ':sct_id', $this->LastInsertId, PDO::PARAM_INT );
			$this->bindSQL( $Query, ':idn_id', $_SESSION['idn_id'], PDO::PARAM_INT );
			$this->executeSQL( $Query );
		}

		return TRUE;
	}



	public function rechercherSocietes( $orderBy = 'nom', $search = '' ) {
	/**
	* Lister les Sociétés.
	*
	* \license Copyleft Loxense
	* \author Pierre-Luc MARY
	* \date 2023-12-22
	*
	* \param[in] $orderBy Permet de gérer l'ordre d'affichage.
	* \param[in] $search Permet de recherchrer des Entités contenant une partie de cette chaîne.
	*
	* \return Renvoi un tableau d'objet ou un tableau vide si pas de données trouvées. Lève une exception en cas d'erreur.
	*/
		$Where = '';

		if ( $_SESSION['idn_super_admin'] == FALSE ) {
			$Request = 'SELECT sct.*
				FROM idsc_idn_sct AS "idsc"
				LEFT JOIN sct_societes AS "sct" ON sct.sct_id = idsc.sct_id ';
			$Where .= 'WHERE idsc.idn_id = ' . $_SESSION['idn_id'] . ' ';
		} else {
			$Request = 'SELECT sct.* FROM sct_societes AS "sct" ';
		}

		if ( $search != '' ) {
			if ( $Where == '' ) {
				$Where .= 'WHERE sct_nom like :Search OR sct_description like :Search ';
			} else {
				$Where .= 'AND sct_nom like :Search OR sct_description like :Search ';
			}
		}

		$Request .= $Where;

		switch( $orderBy ) {
		 default:
		 case 'nom':
			$Request .= 'ORDER BY sct_nom ';
			break;

		 case 'nom-desc':
			$Request .= 'ORDER BY sct_nom DESC ';
			break;
		}

		$Query = $this->prepareSQL( $Request );

		if ( $search != '' ) {
			$this->bindSQL( $Query, ':Search', '%' . $search . '%', PDO::PARAM_STR, L_SCT_NOM );
		}

		$this->executeSQL( $Query );

		return $Query->fetchAll( PDO::FETCH_CLASS );
	}



	public function detaillerSociete( $sct_id ) {
	/**
	* Récupère les informations d'une Société.
	*
	* \license Copyleft Loxense
	* \author Pierre-Luc MARY
	* \date 2023-12-22
	*
	* \param[in] $sct_id Identifiant de la Société à récupérer
	*
	* \return Renvoi l'occurrence de la Société ou FALSE si pas de Société. Lève une Exception en cas d'erreur.
	*/
		$Request = 'SELECT * FROM sct_societes WHERE sct_id = :sct_id ' ;
		 
		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':sct_id', $sct_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return $Query->fetchObject();
	}



	public function SocieteEstAssociee( $sct_id ) {
	/**
	* Récupère les nombres d'association d'une Société.
	*
	* \license Copyleft Loxense
	* \author Pierre-Luc MARY
	* \date 2023-12-22
	*
	* \param[in] $sct_id Id de la Société à contrôler
	*
	* \return Renvoi l'occurrence listant les associations de l'Entité ou FALSE si pas d'entité. Lève une Exception en cas d'erreur.
	*/
		$Request = 'SELECT sct_nom, sct_description,
COUNT(DISTINCT cmp.cmp_id) AS total_cmp,
COUNT(DISTINCT idsc.idn_id) AS total_idn,
COUNT(DISTINCT ppr.ppr_id) AS total_ppr
FROM sct_societes AS "sct"
LEFT JOIN cmp_campagnes AS "cmp" ON cmp.sct_id = sct.sct_id
LEFT JOIN ppr_parties_prenantes AS "ppr" ON ppr.sct_id = sct.sct_id
LEFT JOIN idsc_idn_sct AS "idsc" ON idsc.sct_id = sct.sct_id
WHERE sct.sct_id  = :sct_id 
GROUP BY sct_nom, sct_description ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':sct_id', $sct_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return $Query->fetchObject();
	}



	public function supprimerSociete( $sct_id ) {
	/**
	* Supprimer une Société.
	*
	* \license Copyleft Loxense
	* \author Pierre-Luc MARY
	* \date 2023-12-22
	*
	* \param[in] $sct_id Identifiant de la Société à supprimer
	*
	* \return Renvoi TRUE si la Société a été supprimée ou FALSE si la Société n'existe pas. Lève une Exception en cas d'erreur.
	*/

		$Query = $this->prepareSQL( 'DELETE ' .
			'FROM sct_societes ' .
			'WHERE sct_id = :sct_id' );
		
		$this->bindSQL( $Query, ':sct_id', $sct_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

 		return TRUE;
	}



	public function totalSocietes() {
	/**
	* Calcul le nombre total de Sociétés.
	*
	* \license Copyleft Loxense
	* \author Pierre-Luc MARY
	* \date 2023-12-22
	*
	* \return Renvoi le nombre total de Sociétés stockées en base. Lève une Exception en cas d'erreur.
	*/

		$Request = 'SELECT ' .
		 'count(*) AS total ' .
		 'FROM sct_societes ' ;

		$Query = $this->prepareSQL( $Request );

		$this->executeSQL( $Query );
		
		$Occurrence = $Query->fetchObject() ;
		
		return $Occurrence->total;
	}



	public function construireMessagePourHistorique( $ent_id, $objEntity = '' ) {
	/**
	* Construit le message détaillé à remonter dans l'Historique.
	*
	* \license Copyleft Loxense
	* \author Pierre-Luc MARY
	* \date 2014-06-23
	*
	* \param[in] $ent_id Identitifiant de l'Id à identifier
	* \param[in] $objEntity Fournit des informations spécifiques sur une Entité qui vient d'être créée
	*
	* \return Renvoi le nombre total d'Entités de stocker en base
	*/
		if ( $ent_id == '' and $objEntity == '' ) return '*** Internal error ***';

		include_once( HBL_DIR_LIBRARIES . '/Class_HTML.inc.php');

		$pHTML = new HTML();

		// Affiche les infrmations transmises ou les récupère en base.
		if ( $objEntity == '' ) $objEntity = $this->detaillerEntite( $ent_id );

		// Récupère les libellés pour le message
		$Labels = $pHTML->getTextCode( array( 'L_Label' ) );

		return ' (' . $Labels[ 'L_Label' ] . ': "' . $objEntity->ent_libelle . '")';
	}

} // Fin class IICA_Societes

?>