<?php

include_once( 'Constants.inc.php' );
include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Connecteur_BD_PDO.inc.php' );


class HBL_Identites_Societes extends HBL_Connexioneur_BD {
/**
* Cette classe gère les associations entre les Identités et les Sociétés.
*
* \license Copyright Loxense
* \author Pierre-Luc MARY
* \date 2024-01-04
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


	public function rechercherSocietesUtilisateur( $idn_id ) {
		/**
		 * Lister les Sociétés autorisées à un Utilisateur.
		 *
		 * \license Copyleft Loxense
		 * \author Pierre-Luc MARY
		 * \date 2023-12-22
		 *
		 * \param[in] $idn_id Id de l'Utilisateur pour lequel on cherche à vérifier l'accès.
		 *
		 * \return Renvoi un tableau d'objet ou un tableau vide si pas de données trouvées. Lève une exception en cas d'erreur.
		 */
		$Request = 'SELECT sct.*, idsc.idn_id AS "autorise", idsc_admin
			FROM sct_societes AS "sct"
			LEFT JOIN (SELECT sct_id, idn_id, idsc_admin FROM idsc_idn_sct WHERE idn_id = :idn_id) AS "idsc" ON idsc.sct_id = sct.sct_id 
			ORDER BY sct_nom ';
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':idn_id', $idn_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function ajouterSocieteIdentite( $idn_id, $sct_id ) {
	/**
	* Ajouter une association entre une Société et une Identité.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-01-04
	*
	* \param[in] $idn_id Identifiant de l'Identité
	* \param[in] $ent_id Identifiant de la Société
	*
	* \return Renvoi TRUE si l'Identité a été associée à la Société, sinon FALSE. Lève une Exception en cas d'erreur.
	*/

		$Query = $this->prepareSQL( 'INSERT ' .
		 'INTO idsc_idn_sct ' .
		 '( idn_id, sct_id ) ' .
		 'VALUES ( :idn_id, :sct_id ) ' );
		
		$this->bindSQL( $Query, ':idn_id', $idn_id, PDO::PARAM_INT ) ;

		$this->bindSQL( $Query, ':sct_id', $sct_id, PDO::PARAM_INT ) ;

		$this->executeSQL( $Query );

 		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

 		return TRUE;
	}


	public function supprimerSocieteIdentite( $idn_id, $sct_id ) {
	/**
	* Détruire l'association entre une Société et une Identité.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-01-04
	*
	* \param[in] $idn_id Identifiant de l'Identité
	* \param[in] $sct_id Identifiant de l'Entité
	*
	* \return Renvoi TRUE si l'association entre l'Identité et la Société a été supprimée, sinon FALSE. Lève une Exception en cas d'erreur.
	*/

		$Query = $this->prepareSQL( 'DELETE ' .
		 'FROM idsc_idn_sct ' .
		 'WHERE idn_id = :idn_id AND sct_id = :sct_id ' );
		
		$this->bindSQL( $Query, ':idn_id', $idn_id, PDO::PARAM_INT ) ;

		$this->bindSQL( $Query, ':sct_id', $sct_id, PDO::PARAM_INT ) ;
		
		$this->executeSQL( $Query );

 		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
 
 		return TRUE;
	}

} // Fin HBL_Identites_Entites

?>