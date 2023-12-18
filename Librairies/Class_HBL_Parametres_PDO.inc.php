<?php

include_once( 'Constants.inc.php' );
include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Connecteur_BD_PDO.inc.php' );


/**
* Cette classe gère les paramètres internes de l'application.
*
* PHP version 5
* @license Copyright Loxense
* @author Pierre-Luc MARY
* @date 2015-05-31
*
*/

define( 'L_SPR_NAME', 30 );
define( 'L_SPR_VALUE', 60 );


class HBL_Parametres extends HBL_Connecteur_BD {

	public function __construct() {
	/**
	* Connexion à la base de données.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-31
	*
	* @return Renvoi un booléen sur le succès de la connexion à la base de données
	*/
		parent::__construct();
		
		return TRUE;
	}


	/* ===============================================================================
	** Gestion des Paramètres
	*/
	
	public function recupererParametre( $Name ) {
	/**
	* Récupère la valeur d'un paramètre.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-31
	*
	* @param[in] $Name Nom du paramètre recherché
	*
	* @return Renvoi une chaîne contenant la valeur du paramètre ou une chaîne vide si le paramètre n'existe pas.
	*/
		// -----------------------------------
		// Récupère la valeur d'un paramètre.
		$Request = 'SELECT ' .
		 'prs_valeur ' .
		 'FROM prs_parametres_systeme ' .
 		 'WHERE prs_nom = :Name ';
		 
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':Name', $Name, PDO::PARAM_STR, L_SPR_NAME );

		$Result = $this->executeSQL( $Query );
      
		if ( $this->RowCount == 0 ) {
			return '';
		}

		$Occurrence = $Result->fetchObject();
		
		if ( $Occurrence == '' ) $Value = '';
		else $Value = $Occurrence->prs_valeur;

 		return $Value;
	}

	
	public function recupererParametreParID( $Id ) {
	/**
	* Récupère les valeurs d'un paramètre par son Id.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2018-04-16
	*
	* @param[in] $ID Id. du paramètre à rechercher
	*
	* @return Renvoi un objet contenant les valeurs du paramètre ou "FALSE" si le paramètre n'existe pas.
	*/
		// -----------------------------------
		// Récupère la valeur d'un paramètre.
		$Request = 'SELECT ' .
		 'prs_nom, prs_groupe, prs_valeur ' .
		 'FROM prs_parametres_systeme ' .
 		 'WHERE prs_id = :Id ';
		 
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':Id', $Id, PDO::PARAM_INT );

		$Result = $this->executeSQL( $Query );
      
		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		$Occurrence = $Result->fetchObject();
		
		if ( $Occurrence == '' ) $Value = FALSE;
		else $Value = $Occurrence;

 		return $Value;
	}


	public function majParametre( $Name, $Value ) {
	/**
	* Crée ou met à jour la valeur d'un paramètre.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-31
	*
	* @param[in] $Name Nom du paramètre à créer ou mettre à jour
	* @param[in] $Value Valeur du paramètre à créer ou mettre à jour
	*
	* @return Renvoi vrai si le paramêtre a été mis à jour, sinon renvoi une exception
	*/

		if ( $this->recupererParametre( $Name ) == '' ) {
			$Query = $this->prepareSQL( 'INSERT INTO prs_parametres_systeme ' .
				'( prs_valeur, prs_nom ) ' .
				'VALUES ( :Value, :Name ) ' );
		} else {
			$Query = $this->prepareSQL( 'UPDATE prs_parametres_systeme SET ' .
				'prs_valeur = :Value ' .
				'WHERE prs_nom = :Name ' );
		}
				
		$this->bindSQL( $Query, ':Name', $Name, PDO::PARAM_STR, L_SPR_NAME );
				
		$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_STR, L_SPR_VALUE );

		$this->executeSQL( $Query );
		
		return TRUE;
	}


	public function majParametreParID( $ID, $Value ) {
	/**
	* Met à jour la valeur d'un paramètre pointé par son ID.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-10-13
	*
	* @param[in] $ID Id. du paramètre à mettre à jour
	* @param[in] $Value Valeur du paramètre à créer ou mettre à jour
	*
	* @return Renvoi vrai si le paramêtre a été mis à jour, sinon renvoi une exception
	*/

		$Query = $this->prepareSQL( 'UPDATE prs_parametres_systeme SET ' .
			'prs_valeur = :Value ' .
			'WHERE prs_id = :Id ' );
				
		$this->bindSQL( $Query, ':Id', $ID, PDO::PARAM_INT );
				
		$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_STR, L_SPR_VALUE );

		$this->executeSQL( $Query );
		
		return TRUE;
	}


	public function rechercherParametres( $orderBy = 'comment', $search = '', $specificColumns = '*', $regroupement = '' ) {
	/**
	* Lister les différents paramètres internes de Loxense.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-15
	*
	* @param[in] $orderBy Permet de gérer l'ordre d'affichage.
	* @param[in] $search Permet de recherchrer des Entités contenant une partie de cette chaîne.
	* @param[in] $specificColumns Permet de récupérer des colonnes spécifiques et non pas toutes les colonnes.
	* @param[in] $regroupement Permet de limiter la recherche à un groupe de Paramètres.
	*
	* @return Renvoi un tableau d'objet ou un tableau vide si pas de données trouvées. Lève une exception en cas d'erreur.
	*/
		$Request = 'SELECT ' .
			$specificColumns . ' ' .
			'FROM prs_parametres_systeme ';

		$Where = '';

		if ( $search != '' ) {
			$Where .= 'WHERE prs_commentaire like :Search ';
		}

		if ( $regroupement != '' ){
			if ( $Where == '' ) $Where .= 'WHERE ';
			else $Where .= 'AND ';

			$Where .= 'prs_groupe = :regroupement ';
		}

		if ( $_SESSION['idn_super_admin'] !== TRUE ) {
			if ( $Where != '' ) $Where .= 'AND prs_super_admin = FALSE ';
			else $Where = 'WHERE prs_super_admin = FALSE ';
//		} else {
//			$Where = '';
		}

		$Request .= $Where . 'ORDER BY prs_groupe ';


		switch( $orderBy ) {
		 default:
		 case 'comment':
		 	$Request .= ', prs_commentaire ';
			break;

		 case 'comment-desc':
		 	$Request .= ', prs_commentaire DESC ';
			break;
		}

		 
		$Query = $this->prepareSQL( $Request );

		if ( $search != '' ) {
			$this->bindSQL( $Query, ':Search', '%' . $search . '%', PDO::PARAM_STR, 35 );
		}

		if ( $regroupement != '' && $Where != '' ) {
			$this->bindSQL( $Query, ':regroupement', $regroupement, PDO::PARAM_STR, 30 );
		}

		
		$this->executeSQL( $Query );

		$Parameters = array();
		$Liste = $Query->fetchAll( PDO::FETCH_CLASS );

 		foreach( $Liste as $Occurrence ) {
 			$Parameters[ $Occurrence->prs_nom ] = $Occurrence;
 		}

 		return $Parameters;
	}


	public function rechercherGroupesParametres() {
	/**
	* Lister les différents Groupes de Paramètres.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-05-15
	*
	* @return Renvoi un tableau d'objet ou un tableau vide si pas de données trouvées. Lève une exception en cas d'erreur.
	*/

		if ( $_SESSION['idn_super_admin'] !== TRUE ) {
			$Where = 'WHERE prs_super_admin = FALSE ';
		} else {
			$Where = '';
		}

		$Request = 'SELECT ' .
			'DISTINCT prs_groupe ' .
			'FROM prs_parametres_systeme ' .
			$Where .
			'ORDER BY prs_groupe ';

		$Query = $this->prepareSQL( $Request );

		$this->executeSQL( $Query );
		
 		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function totalParametres() {
	/**
	* Récupère le nombre total de Paramètres.
	*
	* @license Copyright Loxense
	* @author Pierre-Luc MARY
	* @date 2015-11-24
	*
	* @return Renvoi le nombre total d'Identités
	*/
		$Query = $this->prepareSQL( 'SELECT ' .
		 'count(*) as total ' .
		 'FROM prs_parametres_systeme ' );
		
		$this->executeSQL( $Query );
		
		$Occurrence = $Query->fetchObject();

		return $Occurrence->total;
	}
	
	
	public function recupererLangues() {
		/**
		 * Récupère toutes les langues gérées par l'outil.
		 *
		 * @license Copyright Loxense
		 * @author Pierre-Luc MARY
		 *
		 * @return Renvoi un tableau d'objet ou un tableau vide si pas de données trouvées. Lève une exception en cas d'erreur.
		 */
		
		$Request = 'SELECT ' .
			'lng_id, lng_libelle ' .
			'FROM lng_langages ' .
			'ORDER BY lng_id ';
			
			$Query = $this->prepareSQL( $Request );
			
			$this->executeSQL( $Query );
			
			return $Query->fetchAll( PDO::FETCH_CLASS );
	}
	
}

?>