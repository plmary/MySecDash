<?php

include_once( 'Constants.inc.php' );
include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Connecteur_BD_PDO.inc.php' );


class HBL_Identites_Entites extends HBL_Connexioneur_BD {
/**
* Cette classe gère la relation entre les Identités et les Entités.
*
* \license Copyright Loxense
* \author Pierre-Luc MARY
* \date 2015-05-28
*/

	public function __construct() {
	/**
	* Connexion à la base de données.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-28
	*
	* \return Renvoi un booléen sur le succès de la connexion à la base de données
	*/
		parent::__construct();
		
		return true;
	}


	public function rechercherEntitesUtilisateur( $sct_id, $idn_id ) {
		/**
		 * Lister les Entités authorisées à un Utilisateur
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2023-12-28
		 *
		 * \param[in] $sct_id ID de la Société pour lesquelles on recherche les Entités.
		 * \param[in] $idn_id ID de l'Utilisateur qui pourrait être associé
		 *
		 * \return Renvoi un tableau d'objet ou un tableau vide si pas de données trouvées. Lève une exception en cas d'erreur.
		 */
		$Request = 'SELECT ent.*, sct.sct_nom, iden.idn_id AS "autorise", iden_admin
			FROM ent_entites AS "ent"
			LEFT JOIN (SELECT ent_id, idn_id, iden_admin FROM iden_idn_ent WHERE idn_id = :idn_id) AS "iden" ON iden.ent_id = ent.ent_id 
			LEFT JOIN sct_societes AS "sct" ON sct.sct_id = ent.sct_id ';
		
		if ( $sct_id != '*' ) {
			$Request .= 'WHERE ent.sct_id = :sct_id ';
		}
		
		$Request .= 'ORDER BY ent_nom ';
		
		$Query = $this->prepareSQL( $Request );
		
		if ( $sct_id != '*' ) {
			$this->bindSQL( $Query, ':sct_id', $sct_id, PDO::PARAM_INT );
		}
		
		$this->bindSQL( $Query, ':idn_id', $idn_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function rechercherEntitesIdentite( $Id_Identity, $In_Array = FALSE, $Detailed_Obj = FALSE, $For_Admin = FALSE ) {
	/**
	* Lister les Entités d'une Identité.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-28
	*
	* \param[in] $Id_Identity Identifiant de l'Identité de référence
	*
	* \return Renvoi la liste des Entités associées à l'Identité, sinon retourne une liste vide
	*/
		if ( $Id_Identity != '' ) {
			$Request = 'SELECT ' .
			 't2.ent_id, t2.ent_libelle, t1.iden_admin ' .
			 'FROM iden_idn_ent AS t1 ' .
			 'LEFT JOIN ent_entites AS t2 ON t1.ent_id = t2.ent_id ' .
			 'WHERE t1.idn_id = :Idn_id ';

			if ( $For_Admin === TRUE ) {
				$Request .= 'AND t1.iden_admin = TRUE ';
			}

			$Request .= 'ORDER BY t2.ent_libelle ';
		} else {
			return array();
		}

		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':Idn_id', $Id_Identity, PDO::PARAM_INT ) ;
		
		$this->executeSQL( $Query );


		$MyArray = array();
 
 		if ( $In_Array === TRUE ) {
			while( $Data = $Query->fetchObject() ) {
				if ( $Detailed_Obj === TRUE ) $MyArray[ $Data->ent_id ] = $Data;
				else $MyArray[] = $Data->ent_id;
			}

			return $MyArray;
 		} else {
	 		return $Query->fetchAll( PDO::FETCH_CLASS );
 		}
	}


	public function ajouterEntiteIdentite( $Id_Identity, $Id_Entity, $Flag_Admin = false ) {
	/**
	* Ajouter une Entité à une Identité.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-28
	*
	* \param[in] $Id_Identity Identifiant de l'Identité
	* \param[in] $Id_Entity Identifiant de l'Entité
	* \param[in] $Flag_Admin Permet d'indiquer si l'Identifiant est administrateur de l'Entité
	*
	* \return Renvoi TRUE si l'Identité a été associée à l'Entité, sinon FALSE. Lève une Exception en cas d'erreur.
	*/

		$Query = $this->prepareSQL( 'INSERT ' .
		 'INTO iden_idn_ent ' .
		 '( idn_id, ent_id, iden_admin ) ' .
		 'VALUES ( :Idn_id, :Ent_id, :Admin ) ' );
		
		$this->bindSQL( $Query, ':Idn_id', $Id_Identity, PDO::PARAM_INT ) ;

		$this->bindSQL( $Query, ':Ent_id', $Id_Entity, PDO::PARAM_INT ) ;

		$this->bindSQL( $Query, ':Admin', $Flag_Admin, PDO::PARAM_BOOL ) ;
		
		$this->executeSQL( $Query );

 		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

 		return TRUE;
	}


	public function majEntiteIdentite( $Id_Identity, $Id_Entity, $Flag_Admin = false ) {
	/**
	* Modifier le flag d'Administration d'une Entité rattachée à une Identité.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-11-10
	*
	* \param[in] $Id_Identity Identifiant de l'Identité
	* \param[in] $Id_Entity Identifiant de l'Entité
	* \param[in] $Flag_Admin Permet d'indiquer si l'Identifiant est administrateur de l'Entité
	*
	* \return Renvoi TRUE si l'Identité a été associée à l'Entité, sinon FALSE. Lève une Exception en cas d'erreur.
	*/

		$Query = $this->prepareSQL( 'UPDATE iden_idn_ent ' .
		 'SET iden_admin = :Admin ' .
		 'WHERE idn_id = :Idn_id and ent_id = :Ent_id ' );
		
		$this->bindSQL( $Query, ':Idn_id', $Id_Identity, PDO::PARAM_INT ) ;

		$this->bindSQL( $Query, ':Ent_id', $Id_Entity, PDO::PARAM_INT ) ;

		$this->bindSQL( $Query, ':Admin', $Flag_Admin, PDO::PARAM_BOOL ) ;
		
		$this->executeSQL( $Query );

 		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

 		return TRUE;
	}


	public function supprimerEntiteIdentite( $Id_Identity, $Id_Entity ) {
	/**
	* Détruire une Entité rattachée à une Identité.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-28
	*
	* \param[in] $Id_Identity Identifiant de l'Identité
	* \param[in] $Id_Entity Identifiant de l'Entité
	*
	* \return Renvoi TRUE si l'association entre l'Identité et l'Entité a été supprimée, sinon FALSE. Lève une Exception en cas d'erreur.
	*/

		$Query = $this->prepareSQL( 'DELETE ' .
		 'FROM iden_idn_ent ' .
		 'WHERE idn_id = :Idn_id AND ent_id = :Ent_id ' );
		
		$this->bindSQL( $Query, ':Idn_id', $Id_Identity, PDO::PARAM_INT ) ;

		$this->bindSQL( $Query, ':Ent_id', $Id_Entity, PDO::PARAM_INT ) ;
		
		$this->executeSQL( $Query );

 		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
 
 		return TRUE;
	}

} // Fin HBL_Identites_Entites

?>