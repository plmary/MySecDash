<?php

include_once( 'Constants.inc.php' );

include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Connecteur_BD_PDO.inc.php' );


// Centralisation de la taille des objets en base.
define( 'L_ENT_NOM', 100);


class HBL_Entites extends HBL_Connexioneur_BD {
/**
* Cette classe gère les entités.
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


	/* ===============================================================================
	** Gestion des Entités
	*/
	
	public function majEntite( $sct_id, $ent_id, $ent_nom, $ent_description='' ) {
	/**
	* Créé ou actualise une Entité.
	*
	* \license Copyleft Loxense
	* \author Pierre-Luc MARY
	* \date 2023-12-28
	*
	* \param[in] $sct_id Identifiant de la Société de rattachement de l'Entité
	* \param[in] $ent_id Identifiant de l'entité à modifier (si précisé)
	* \param[in] $ent_nom Nom de l'entité
	* \param[in] $ent_description Description de l'entité
	*
	* \return Renvoi TRUE si l'entité a été créée ou modifiée, FALSE si l'entité n'existe pas ou lève une exception en cas d'erreur.
	*/
		if ( $ent_id == '' ) {
			$Query = $this->prepareSQL(
				'INSERT INTO ent_entites ' .
				'( sct_id, ent_nom, ent_description ) ' .
				'VALUES ( :sct_id, :ent_nom, :ent_description )'
				);
		} else {
			$Query = $this->prepareSQL(
				'UPDATE ent_entites SET ' .
				'sct_id  = :sct_id, ' .
				'ent_nom = :ent_nom, ' .
				'ent_description = :ent_description ' .
				'WHERE ent_id = :ent_id'
				);

			
			$this->bindSQL( $Query, ':ent_id', $ent_id, PDO::PARAM_INT );
		}

		$this->bindSQL( $Query, ':sct_id', $sct_id, PDO::PARAM_INT );
		$this->bindSQL( $Query, ':ent_nom', $ent_nom, PDO::PARAM_STR, L_ENT_NOM );
		$this->bindSQL( $Query, ':ent_description', $ent_description, PDO::PARAM_LOB );
		
		$this->executeSQL( $Query );

		if ( $ent_id == '' ) {
			switch( $this->getAttribute(PDO::ATTR_DRIVER_NAME) ) {
			 case 'pgsql';
				$this->LastInsertId = $this->lastInsertId( 'ent_entites_ent_id_seq' );
				break;
			}

			// Comme l'utilisateur vient de créer son Entité, on la lui rattache automatiquement.
			$Request = 'INSERT INTO iden_idn_ent (idn_id, ent_id)
				VALUES (' . $_SESSION['idn_id'] . ', ' . $this->LastInsertId . ') ';

			$Query = $this->prepareSQL( $Request );

			$this->executeSQL( $Query );
		}
	
		
		return TRUE;
	}


	public function majEntiteParChamp( $Id, $Field, $Value ) {
		/**
		 * Crée ou actualise les champs courants d'une Entite.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-03-14
		 *
		 * \param[in] $Id Identifiant d'une Entite
		 * \param[in] $Field Nom du champ d'une Entite à modifier
		 * \param[in] $Value Valeur du champ d'une Entite à prendre en compte
		 *
		 * \return Renvoi un booléen sur le succès de la création ou la modification de l'application
		 */
		if ( $Id == '' or $Field == '' or $Value == '' ) return FALSE;
		
		
		$Request = 'UPDATE ent_entites SET ';
		
		switch ( $Field ) {
			case 'ent_id':
			case 'ent_nom':
			case 'ent_description':
				$Request .= $Field . ' = :Value ';
				break;
				
			default:
				return FALSE;
		}
		
		$Request .= 'WHERE ent_id = :ent_id';
		
		$Query = $this->prepareSQL( $Request );
		
		
		$this->bindSQL( $Query, ':ent_id', $Id, PDO::PARAM_INT );
		
		switch ( $Field ) {
			case 'ent_id':
				$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_INT );
				break;
				
			case 'ent_nom':
				$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_STR, L_ENT_NOM );
				break;
				
			case 'ent_description':
				$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_LOB );
				break;
		}
		
		
		$this->executeSQL( $Query );
		
		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
		
		
		return TRUE;
	}
	

	public function rechercherEntites( $sct_id = '*', $orderBy = 'ent_nom', $search = '', $specificColumns = '*' ) {
	/**
	* Lister les Entités.
	*
	* \license Copyleft Loxense
	* \author Pierre-Luc MARY
	* \date 2023-12-28
	*
	* \param[in] $sct_id ID de la Société pour lesquelles on recherche les Entités.
	* \param[in] $orderBy Permet de gérer l'ordre d'affichage.
	* \param[in] $search Permet de recherchrer des Entités contenant une partie de cette chaîne.
	* \param[in] $specificColumns Permet de récupérer des colonnes spécifiques et non pas toutes les colonnes.
	*
	* \return Renvoi un tableau d'objet ou un tableau vide si pas de données trouvées. Lève une exception en cas d'erreur.
	*/
		$Where = '';

		if ( $_SESSION['idn_super_admin'] == FALSE ) {
			$Request = 'SELECT ent.*
				FROM iden_idn_ent AS "iden"
				LEFT JOIN ent_entites AS "ent" ON ent.ent_id = iden.ent_id ';
			$Where .= 'WHERE iden.idn_id = ' . $_SESSION['idn_id'] . ' ';
		} else {
			$Request = 'SELECT ' .
			$specificColumns . ' ' .
			'FROM ent_entites AS "ent" ' .
			'LEFT JOIN sct_societes AS "sct" ON sct.sct_id = ent.sct_id ';
		}

		if ( $sct_id != '*' && $sct_id != '' ) {
			if ( $Where == '' ) $Request .= 'WHERE ';
			else $Request .= 'AND ';

			$Request .= 'ent.sct_id = :sct_id ';
		}

		
		if ( $search != '' ) {
			if ( $Where == '' ) $Request .= 'WHERE ';
			else $Request .= 'AND ';
			
			$Request .= 'ent_libelle like :Search ';
		}


		switch( $orderBy ) {
		 default:
		 case 'ent_nom':
		 	$Request .= 'ORDER BY ent_nom ';
			break;

		 case 'ent_nom-desc':
		 	$Request .= 'ORDER BY ent_nom DESC ';
			break;
		}

		//print('<hr>'.$Request.' : sct_id='.$sct_id.'<hr>');
		$Query = $this->prepareSQL( $Request );

		if ( $search != '' ) {
			$this->bindSQL( $Query, ':Search', '%' . $search . '%', PDO::PARAM_STR, 35 );
		}

		if ($sct_id != '*' && $sct_id != '' ) {
			$this->bindSQL( $Query, ':sct_id', $sct_id, PDO::PARAM_INT );
		}

		$this->executeSQL( $Query );

		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function detaillerEntite( $ent_id ) {
	/**
	* Récupère les informations d'une Entité.
	*
	* \license Copyleft Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-15
	*
	* \param[in] $ent_id Identifiant de l'entité à récupérer
	*
	* \return Renvoi l'occurrence d'une Entité ou FALSE si pas d'entité. Lève une Exception en cas d'erreur.
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
	* \license Copyleft Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-15
	*
	* \param[in] $ent_id Identifiant de l'entité à récupérer
	*
	* \return Renvoi l'occurrence listant les associations de l'Entité ou FALSE si pas d'entité. Lève une Exception en cas d'erreur.
	*/
		$Request = 'SELECT
COUNT(DISTINCT act.act_id) AS total_act,
COUNT(DISTINCT iden.idn_id) AS total_iden,
COUNT(DISTINCT cmen.cmp_id) AS total_cmen
FROM ent_entites AS "ent"
LEFT JOIN iden_idn_ent AS "iden" ON iden.ent_id = ent.ent_id
LEFT JOIN cmen_cmp_ent AS "cmen" ON cmen.ent_id = ent.ent_id
LEFT JOIN act_activites AS "act" ON act.ent_id = ent.ent_id
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
	* \license Copyleft Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-15
	*
	* \param[in] $ent_id Identifiant de l'entité à supprimer
	*
	* \return Renvoi TRUE si l'Entité a été supprimée ou FALSE si l'entité n'existe pas. Lève une Exception en cas d'erreur.
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


	public function totalEntites( $sct_id = '' ) {
	/**
	* Calcul le nombre total d'Entités.
	*
	* \license Copyleft Loxense
	* \author Pierre-Luc MARY
	* \date 2016-12-24
	*
	* \return Renvoi le nombre total d'Entités stockées en base. Lève une Exception en cas d'erreur.
	*/

		$Request = 'SELECT ' .
		 'count(*) AS total ' .
		 'FROM ent_entites ' ;

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
    
    
    public function recupererENT_IDparCRS_ID( $crs_id ) {
    	/**
    	 * Récupère l'ID de l'Entité associé à une Cartographie.
    	 *
    	 * \license Loxense
    	 * \author Pierre-Luc MARY
    	 * \date 2018-03-30
    	 *
    	 * \param[in] $crs_id ID de la Cartographie
    	 *
    	 * \return Renvoi l'ID de l'Entité associée.
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