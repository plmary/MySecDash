<?php

include_once( 'Constants.inc.php' );
include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Connecteur_BD_PDO.inc.php' );


class HBL_Identites_Applications extends HBL_Connexioneur_BD {
/**
* Cette classe gère la relation entre les Identités et les Applications.
*
* \license Copyright Loxense
* \author Pierre-Luc MARY
* \date 2015-05-29
*/

	public function rechercherApplicationsIdentite( $Id_Identity ) {
	/**
	* Lister les Applications d'une Identité.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-29
	*
	* \param[in] $Id_Identity Identifiant de l'Identité
	*
	* \return Renvoi la liste des Applications associé à l'Identité, sinon renvoi une liste vide. Lève une Exception en cas d'erreur.
	*/
		$Query = $this->prepareSQL( 'SELECT ' .
		 't2.app_id, app_code, app_libelle, app_localisation, t4.drt_code_libelle ' .
		 'FROM idpr_identities_profiles AS t1 ' .
		 'LEFT JOIN prac_profiles_access_control AS t2 ON t1.prf_id = t2.prf_id ' .
		 'LEFT JOIN app_applications AS t3 ON t2.app_id = t3.app_id ' .
		 'LEFT JOIN drt_droits AS t4 ON t4.drt_id = t2.drt_id ' .
		 'WHERE t1.idn_id = :Idn_id ' );
		
		$this->bindSQL( $Query, ':Idn_id', $Id_Identity, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
 
 		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function ajouterApplicationIdentite( $Id_Identity, $Id_Application ) {
	/**
	* Ajouter une Application à une Identité.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-29
	*
	* \param[in] $Id_Identity Identifiant de l'Identité
	* \param[in] $Id_Application Identifiant de l'Application
	*
	* \return Renvoi TRUE si l'association entre l'Identité et une Application a été créée, sinon FALSE. Lève une exception en cas d'erreur.
	*/
		$Query = $this->prepareSQL( 'INSERT ' .
		 'INTO idap_identities_applications ' .
		 '( idn_id, app_id ) ' .
		 'VALUES ( :Idn_id, :App_id ) ' );

		$this->bindSQL( $Query, ':Idn_id', $Id_Identity, PDO::PARAM_INT );

		$this->bindSQL( $Query, ':App_id', $Id_Application, PDO::PARAM_INT );
				
		$this->executeSQL( $Query );
 
 		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

 		return TRUE;
	}


	public function supprimerApplicationIdentite( $Id_Identity, $Id_Application ) {
	/**
	* Détruire une Application rattachée à une Identité.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-05-29
	*
	* \param[in] $Id_Identity Identifiant de l'Identité
	* \param[in] $Id_Application Identifiant de l'Application
	*
	* \return Renvoi TRUE si l'association entre l'Identité et une Application a été supprimée, sinon FALSE. Lève une Exception en cas d'erreur.
	*/

		$Query = $this->prepareSQL( 'DELETE ' .
		 'FROM idap_identities_applications ' .
		 'WHERE idn_id = :Idn_id AND app_id = :App_id ' );
		
		$this->bindSQL( $Query, ':Idn_id', $Id_Identity, PDO::PARAM_INT ) ;

		$this->bindSQL( $Query, ':App_id', $Id_Application, PDO::PARAM_INT ) ;
		
		$this->executeSQL( $Query );
 
 		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
 
 		return TRUE;
	}

} // Fin HBL_Identites_Applications

?>