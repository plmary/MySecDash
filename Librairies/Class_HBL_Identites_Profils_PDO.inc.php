<?php

include_once( 'Constants.inc.php' );
include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Connecteur_BD_PDO.inc.php' );


class HBL_Identites_Profiles extends HBL_Connecteur_BD {
/**
* Cette classe gère la relation entre les Identités et les Entités.
*
* PHP version 5
* @license Copyright Loxense
* @author Pierre-Luc MARY
* @date 2015-05-28
*/

	public function __construct() {
	/**
	* Connexion à la base de données.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-28
	*
	* @return Renvoi un booléen sur le succès de la connexion à la base de données
	*/
		parent::__construct();
		
		return true;
	}


	public function rechercherProfilsIdentite( $Id_Identity, $In_Array = FALSE, $Detailed_Obj = FALSE ) {
	/**
	* Lister les Profils d'une Identité.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-28
	*
	* @param[in] $Id_Identity Identifiant de l'Identité
	*
	* @return Renvoi la liste des Profiles rattachés à l'Identité, sinon une liste vide
	*/
		$Query = $this->prepareSQL( 'SELECT ' .
		 't1.prf_id, t2.prf_libelle ' .
		 'FROM idpr_idn_prf AS t1 ' .
		 'LEFT JOIN prf_profils AS t2 ON t1.prf_id = t2.prf_id ' .
		 'WHERE t1.idn_id = :Idn_id ' );
		
		$this->bindSQL( $Query, ':Idn_id', $Id_Identity, PDO::PARAM_INT ) ;
		
		$this->executeSQL( $Query );
		

		$MyArray = array();
 
 		if ( $In_Array === TRUE ) {
			while( $Data = $Query->fetchObject() ) {
				if ( $Detailed_Obj === TRUE ) $MyArray[ $Data->prf_id ] = $Data;
				else $MyArray[] = $Data->prf_id;
			}

			return $MyArray;
 		} else {
	 		return $Query->fetchAll( PDO::FETCH_CLASS );
 		}
	}


	public function ajouterProfilIdentite( $Id_Identity, $Id_Profile ) {
	/**
	* Ajouter un Profil à une Identité.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-28
	*
	* @param[in] $Id_Identity Identifiant de l'Identité
	* @param[in] $Id_Profile Identifiant du Profil
	*
	* @return Renvoi TRUE si l'association entre l'Identité et le Profile a été créée, sinon FALSE. Lève une Exception en cas d'erreur.
	*/
		$Query = $this->prepareSQL( 'INSERT ' .
		 'INTO idpr_idn_prf ' .
		 '( idn_id, prf_id ) ' .
		 'VALUES ( :Idn_id, :Prf_id ) ' );
		
		$this->bindSQL( $Query, ':Idn_id', $Id_Identity, PDO::PARAM_INT ) ;

		$this->bindSQL( $Query, ':Prf_id', $Id_Profile, PDO::PARAM_INT ) ;
		
		$this->executeSQL( $Query );
 
 		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

 		return TRUE;
	}


	public function supprimerProfilIdentite( $Id_Identity, $Id_Profile ) {
	/**
	* Détruire un Profil rattaché à une Identité.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-28
	*
	* @param[in] $Id_Identity Identifiant de l'Identité
	* @param[in] $Id_Profile Identifiant du Profil
	*
	* @return Renvoi TRUE si l'association entre l'Identité et le Profile a été supprimée, sinon FALSE. Lève une Exception en cas d'erreur.
	*/
		$Query = $this->prepareSQL( 'DELETE ' .
		 'FROM idpr_idn_prf ' .
		 'WHERE idn_id = :Idn_id AND prf_id = :Prf_id ' );
		
		$this->bindSQL( $Query, ':Idn_id', $Id_Identity, PDO::PARAM_INT ) ;

		$this->bindSQL( $Query, ':Prf_id', $Id_Profile, PDO::PARAM_INT ) ;
		
		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
 
 		return TRUE;
	}


	public function supprimerProfilsIdentite( $Id_Identity ) {
	/**
	* Détruire les Profiles rattaché à une Identité.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-28
	*
	* @param[in] $Id_Identity Identifiant de l'Identité
	*
	* @return Renvoi vrai si l'association entre l'Identité et tous ses Profiles ont été supprimées, sinon lève une exception
	*/
		$Query = $this->prepareSQL( 'DELETE ' .
		 'FROM idpr_idn_prf ' .
		 'WHERE idn_id = :Idn_id ' );
		
		$this->bindSQL( $Query, ':Idn_id', $Id_Identity, PDO::PARAM_INT ) ;
		
		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
 
 		return TRUE;
	}

} // Fin HBL_Identites_Profiles

?> 