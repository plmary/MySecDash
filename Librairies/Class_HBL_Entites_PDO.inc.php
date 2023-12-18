<?php

include_once( 'Constants.inc.php' );

include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Connecteur_BD_PDO.inc.php' );


// Centralisation de la taille des objets en base.
define( 'L_ENT_LABEL', 35);


class HBL_Entites extends HBL_Connecteur_BD {
/**
* Cette classe gère les entités.
*
* PHP version 5
* @license Copyright Loxense
* @author Pierre-Luc MARY
* @date 2015-05-13
*/
	public function __construct() {
	/**
	* Connexion à la base de données en appelant le constructeur du Parent.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2016-11-07
	*
	* @return Renvoi un booléen sur le succès de la connexion à la base de données
	*/
		parent::__construct();
		
		return true;
	}


	/* ===============================================================================
	** Gestion des Entités
	*/
	
	public function majEntite( $ent_id, $Label ) {
	/**
	* Créé ou actualise une Entité.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-14
	*
	* @param[in] $ent_id Identifiant de l'entité à modifier (si précisé)
	* @param[in] $Label Libeller de l'entité
	*
	* @return Renvoi TRUE si l'entité a été créée ou modifiée, FALSE si l'entité n'existe pas ou lève une exception en cas d'erreur.
	*/
		if ( $ent_id == '' ) {
			$Query = $this->prepareSQL(
				'INSERT INTO ent_entites ' .
				'( ent_libelle ) ' .
				'VALUES ( :Label )'
				);
		} else {
			$Query = $this->prepareSQL(
				'UPDATE ent_entites SET ' .
				'ent_libelle = :Label ' .
				'WHERE ent_id = :ent_id'
				);

			
			$this->bindSQL( $Query, ':ent_id', $ent_id, PDO::PARAM_INT );
		}

		$this->bindSQL( $Query, ':Label', $Label, PDO::PARAM_STR, L_ENT_LABEL );

		$this->executeSQL( $Query );

		if ( $ent_id == '' ) {
			switch( $this->getAttribute(PDO::ATTR_DRIVER_NAME) ) {
			 case 'pgsql';
				$this->LastInsertId = $this->lastInsertId( 'ent_entites_ent_id_seq' );
				break;
			}
		}
	
		
		return TRUE;
	}


	public function rechercherEntites( $orderBy = 'label', $search = '', $specificColumns = '*' ) {
	/**
	* Lister les Entités.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-15
	*
	* @param[in] $orderBy Permet de gérer l'ordre d'affichage.
	* @param[in] $search Permet de recherchrer des Entités contenant une partie de cette chaîne.
	* @param[in] $specificColumns Permet de récupérer des colonnes spécifiques et non pas toutes les colonnes.
	*
	* @return Renvoi un tableau d'objet ou un tableau vide si pas de données trouvées. Lève une exception en cas d'erreur.
	*/
		$Request = 'SELECT ' .
			$specificColumns . ' ' .
			'FROM ent_entites AS "ent" ';

		
		if ( $search != '' ) {
			$Request .= 'WHERE ent_libelle like :Search ';
		}


		switch( $orderBy ) {
		 default:
		 case 'label':
		 	$Request .= 'ORDER BY ent_libelle ';
			break;

		 case 'label-desc':
		 	$Request .= 'ORDER BY ent_libelle DESC ';
			break;
		}

		 
		$Query = $this->prepareSQL( $Request );

		if ( $search != '' ) {
			$this->bindSQL( $Query, ':Search', '%' . $search . '%', PDO::PARAM_STR, 35 );
		}

		
		$this->executeSQL( $Query );
		
 		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function detaillerEntite( $ent_id ) {
	/**
	* Récupère les informations d'une Entité.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-15
	*
	* @param[in] $ent_id Identifiant de l'entité à récupérer
	*
	* @return Renvoi l'occurrence d'une Entité ou FALSE si pas d'entité. Lève une Exception en cas d'erreur.
	*/
		$Request = 'SELECT ' .
		 '* ' .
		 'FROM ent_entites ' .
		 'WHERE ent_id = :ent_id ' ;
		 
		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':ent_id', $ent_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return $Query->fetchObject();
	}


	public function EntiteEstAssociee( $ent_id ) {
	/**
	* Récupère les nombres d'association d'une Entité.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-15
	*
	* @param[in] $ent_id Identifiant de l'entité à récupérer
	*
	* @return Renvoi l'occurrence listant les associations de l'Entité ou FALSE si pas d'entité. Lève une Exception en cas d'erreur.
	*/
		$Request = 'SELECT
COUNT(DISTINCT crs_id) AS total_crs,
COUNT(DISTINCT idn_id) AS total_idn
FROM ent_entites AS "ent"
LEFT JOIN crs_cartographies_risques AS "crs" ON crs.ent_id = ent.ent_id
LEFT JOIN idn_identites AS "idn" ON idn.ent_id = ent.ent_id
WHERE ent.ent_id  = :ent_id ';

		 
		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':ent_id', $ent_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return $Query->fetchObject();
	}


	public function supprimerEntite( $ent_id ) {
	/**
	* Supprimer une Entité.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-15
	*
	* @param[in] $ent_id Identifiant de l'entité à supprimer
	*
	* @return Renvoi TRUE si l'Entité a été supprimée ou FALSE si l'entité n'existe pas. Lève une Exception en cas d'erreur.
	*/
      
        $Query = $this->prepareSQL( 'DELETE ' .
         'FROM ent_entites ' .
         'WHERE ent_id = :ent_id' );
		
		$this->bindSQL( $Query, ':ent_id', $ent_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

 		return TRUE;
	}


	public function totalEntites() {
	/**
	* Calcul le nombre total d'Entités.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2016-12-24
	*
	* @return Renvoi le nombre total d'Entités stockées en base. Lève une Exception en cas d'erreur.
	*/

		$Request = 'SELECT ' .
		 'count(*) AS total ' .
		 'FROM ent_entites ' ;

		$Query = $this->prepareSQL( $Request );

		$this->executeSQL( $Query );
		
		$Occurrence = $Query->fetchObject() ;
		
		return $Occurrence->total;
	}


	public function construireMessagePourHistorique( $ent_id, $objEntity = '' ) {
	/**
	* Construit le message détaillé à remonter dans l'Historique.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2014-06-23
	*
	* @param[in] $ent_id Identitifiant de l'Id à identifier
	* @param[in] $objEntity Fournit des informations spécifiques sur une Entité qui vient d'être créée
	*
	* @return Renvoi le nombre total d'Entités de stocker en base
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
    
    
    public function recupererENT_IDparCRS_ID( $crs_id ) {
    	/**
    	 * Récupère l'ID de l'Entité associé à une Cartographie.
    	 *
    	 * @license Loxense
    	 * @author Pierre-Luc MARY
    	 * @date 2018-03-30
    	 *
    	 * @param[in] $crs_id ID de la Cartographie
    	 *
    	 * @return Renvoi l'ID de l'Entité associée.
    	 */
    	
    	$requete = $this->prepareSQL(
    		'SELECT ent_id FROM crs_cartographies_risques AS "crs" WHERE crs_id = :crs_id '
    		);
    	
    	return $this->bindSQL($requete, ':crs_id', $crs_id, PDO::PARAM_INT)
    	->executeSQL($requete)
    	->fetchObject()->ent_id;
    }

} // Fin class IICA_Entities

?>