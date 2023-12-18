<?php

include_once( 'Constants.inc.php' );
include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Connecteur_BD_PDO.inc.php' );

define( 'L_CVL_LAST_NAME',  35);
define( 'L_CVL_FIRST_NAME', 25);


class HBL_Civilites extends HBL_Connecteur_BD {
/**
* Cette classe gère les civilités.
*
* PHP version 5
* @license Copyright Loxense
* @author Pierre-Luc MARY
* @date 2015-05-20
*/

	public function __construct() {
	/**
	* Connexion à la base de données.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-20
	*
	* @return Renvoi un booléen sur le succès de la connexion à la base de données
	*/
		parent::__construct();
		
		return TRUE;
	}


	/* ===============================================================================
	** Gestion des Civilités
	*/
	
	public function majCivilite( $cvl_id, $LastName, $FirstName ) {
	/**
	* Créé ou actualise une Civilité.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-20
	*
	* @param[in] $cvl_id Identifiant de la civilité (à préciser si modification)
	* @param[in] $LastName Nom de famille de l'utilisateur
	* @param[in] $FirstName Prénom de l'utilisateur
	*
	* @return Renvoi TRUE si la civilité a été créée ou mise à jour, FALSE si l'entité n'existe pas. Lève une Exception en cas d'erreur.
	*/

		if ( $cvl_id == '' ) {
			$Request = 'INSERT INTO cvl_civilites ' .
				'( cvl_nom, cvl_prenom ) VALUES ( :LastName, :FirstName ) ';

			$Query = $this->prepareSQL( $Request );
		} else {
			$Request = 'UPDATE cvl_civilites SET ' .
				'cvl_nom = :LastName, ' .
				'cvl_prenom = :FirstName ' .
				'WHERE cvl_id = :cvl_id ';

			$Query = $this->prepareSQL( $Request );

			$this->bindSQL( $Query, ':cvl_id', $cvl_id, PDO::PARAM_INT );
		}
	
		$this->bindSQL( $Query, ':LastName', $LastName, PDO::PARAM_STR, L_CVL_LAST_NAME );

		$this->bindSQL( $Query, ':FirstName', $FirstName, PDO::PARAM_STR, L_CVL_FIRST_NAME );
		
		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		if ( $cvl_id == '' ) {
			switch( $this->getAttribute(PDO::ATTR_DRIVER_NAME) ) {
			 default;
				$this->LastInsertId = $this->lastInsertId();
				break;

			 case 'pgsql';
				$this->LastInsertId = $this->lastInsertId( 'cvl_civilites_cvl_id_seq' );
				break;
			}
		}

		return TRUE;
	}


	public function majCiviliteParChamp( $cvl_id, $Source, $Valeur ) {
	/**
	* Créé ou actualise une Civilité.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-20
	*
	* @param[in] $ID Identifiant de la civilité (à préciser si modification)
	* @param[in] $Source Nom du champ à modifier
	* @param[in] $Valeur Valeur à affecter au champ.
	*
	* @return Renvoi TRUE si la civilité a été créée ou mise à jour, FALSE si l'entité n'existe pas. Lève une Exception en cas d'erreur.
	*/
		if ( $cvl_id == '' ) return FALSE;

		$Old = $this->detaillerCivilite( $cvl_id );
		switch ( $Source ) {
		 	case 'cvl_nom':
				$Old->cvl_nom = $Valeur;
		 		break;
		 	
		 	case 'cvl_prenom':
				$Old->cvl_prenom = $Valeur;
		 		break;
		}

		$Request = 'UPDATE cvl_civilites SET ';
			
		switch ( $Source ) {
		 	case 'cvl_nom':
				$Request .= 'cvl_nom = :LastName ';
		 		break;
		 	
		 	case 'cvl_prenom':
				$Request .= 'cvl_prenom = :FirstName ';
		 		break;
		}

		$Request .= 'WHERE cvl_id = :cvl_id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cvl_id', $cvl_id, PDO::PARAM_INT );


		switch ( $Source ) {
		 	case 'cvl_nom':
				$this->bindSQL( $Query, ':LastName', $Valeur, PDO::PARAM_STR, L_CVL_LAST_NAME );
		 		break;
		 	
		 	case 'cvl_prenom':
				$this->bindSQL( $Query, ':FirstName', $Valeur, PDO::PARAM_STR, L_CVL_FIRST_NAME );
		 		break;
		}


		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}


		return TRUE;
	}


	public function rechercherCivilites( $Order = 'last_name', $Search = '' ) {
	/**
	* Lister les Civilités.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-20
	*
	* @param[in] $Order Permet de gérer l'ordre d'affichage
	* @param[in] $Search Chaîne à rechercher dans les colonnes constituants une Civilité
	*
	* @return Renvoi une liste de civilité ou une liste vide. Lève une exception en cas d'erreur.
	*/
		$Request = 'SELECT ' .
		 '* ' .
		 'FROM cvl_civilites ' ;

		if ( $Search != '' ) {
			$Request .= 'WHERE cvl_nom like :LastName ' .
				'OR cvl_prenom like :FirstName ' ;
		}
		
		switch( $Order ) {
		 case 'cvl_nom':
		 default:
			$Request .= 'ORDER BY cvl_nom ';
			break;

		 case 'cvl_nom-desc':
			$Request .= 'ORDER BY cvl_nom DESC ';
			break;

		 case 'cvl_prenom':
			$Request .= 'ORDER BY cvl_prenom ';
			break;

		 case 'cvl_prenom-desc':
			$Request .= 'ORDER BY cvl_prenom DESC ';
			break;
		}
		 
		$Query = $this->prepareSQL( $Request );

		if ( $Search != '' ) {
			$Search = '%' . $Search . '%';

			$this->bindSQL( $Query, ':LastName', $Search, PDO::PARAM_STR, L_CVL_LAST_NAME );

			$this->bindSQL( $Query, ':FirstName', $Search, PDO::PARAM_STR, L_CVL_FIRST_NAME );
		}

		$this->executeSQL( $Query );
 
		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function detaillerCivilite( $cvl_id ) {
	/**
	* Récupère les informations d'une Civilité.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-20
	*
	* @param[in] $cvl_id Identifiant de la civilité à afficher
	*
	* @return Renvoi l'occurrence de la civilité ou renvoi FALSE si la civilité n'existe pas. Lève une Exception en cas d'erreur.
	*/
		$Request = 'SELECT ' .
		 '* ' .
		 'FROM cvl_civilites ' .
		 'WHERE cvl_id = :cvl_id ' ;
		 
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':cvl_id', $cvl_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
		
		return $Query->fetchObject();
	}


	public function supprimerCivilite( $cvl_id ) {
	/**
	* Supprime une Civilité.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-21
	*
	* @param[in] $cvl_id Identifiant de la civilité à supprimer
	*
	* @return Renvoi TRUE si l'occurrence a été supprimée, FALSE si la civilité n'existe pas. Lève une Exception en cas d'erreur.
	*/
		$Query = $this->prepareSQL( 'DELETE ' .
			 'FROM cvl_civilites ' .
			 'WHERE cvl_id = :cvl_id' );

		$this->bindSQL( $Query, ':cvl_id', $cvl_id, PDO::PARAM_INT ) ;
		
		$this->executeSQL( $Query );

 		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

 		return TRUE;
	}


	public function totalCivilites() {
	/**
	* Calcul le nombre total de Civilités.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-21
	*
	* @return Renvoi le total d es occurrences trouvé. Lève une Exception en cas d'erreur.
	*/
		$Request = 'SELECT ' .
		 'count(*) AS total ' .
		 'FROM cvl_civilites ';

		$Query = $this->prepare( $Request );

		$this->executeSQL( $Query );

		$Occurrence = $Query->fetchObject() ;
		
		return $Occurrence->total;
	}


	public function CiviliteEstAssociee( $cvl_id ) {
	/**
	* Vérifie si cette Civilité est associé à un autre objet.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-15
	*
	* @param[in] $cvl_id Identifiant de la Civilité à contrôler
	*
	* @return Renvoi l'occurrence listant les association de l'Entité ou FALSE si pas d'entité. Lève une Exception en cas d'erreur.
	*/
		$Request = 'SELECT ' .
		 'COUNT(DISTINCT idn_id) AS total_idn ' .
		 'FROM cvl_civilites AS cvl ' .
		 'LEFT JOIN idn_identites AS idn ON idn.cvl_id = cvl.cvl_id ' .
		 'WHERE cvl.cvl_id = :cvl_id ';

		 
		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':cvl_id', $cvl_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return $Query->fetchObject();
	}

} // Fin class IICA_Civilities

?>