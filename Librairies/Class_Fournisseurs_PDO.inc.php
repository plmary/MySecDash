<?php

include_once( 'Constants.inc.php' );
include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Connecteur_BD_PDO.inc.php' );


if ( ! defined( 'L_NOM' ) ) define( 'L_NOM', 100 );
if ( ! defined( 'L_NOM_CODE' ) ) define( 'L_NOM_CODE', 60 );


class Fournisseurs extends HBL_Connexioneur_BD {

	function __construct() {
	/**
	* Connexion à la base de données.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-01-10
	*
	* \return Renvoi un booléen sur le succès de la connexion à la base de données
	*/
		parent::__construct();
		
		return true;
	}


	public function majFournisseur( $frn_id, $tfr_id, $frn_nom, $frn_description ) {
	/**
	* Crée ou met à jour un Fournisseur.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-02-22
	*
	* \param[in] $frn_id Identifiant du Fournisseur (à préciser si modification)
	* \param[in] $tfr_id Identifiant du Type de Fournisseur
	* \param[in] $frn_nom Nom du Fournisseur
	* \param[in] $frn_description Description du Fournisseur.
	*
	* \return Renvoi un booléen sur le succès de la création ou la modification de l'application
	*/
		if ($tfr_id == '') $tfr_id = NULL;

		if ( $frn_id == '' ) {
			$Request = 'INSERT INTO frn_fournisseurs
				( tfr_id, frn_nom, frn_description )
				VALUES ( :tfr_id, :frn_nom, :frn_description ) ';

			$Query = $this->prepareSQL( $Request );
		} else {
			$Request = 'UPDATE frn_fournisseurs SET
				tfr_id = :tfr_id,
				frn_nom = :frn_nom,
				frn_description = :frn_description
				WHERE frn_id = :frn_id';

			$Query = $this->prepareSQL( $Request );
			
			$this->bindSQL( $Query, ':frn_id', $frn_id, PDO::PARAM_INT );
		}


		$this->bindSQL( $Query, ':tfr_id', $tfr_id, PDO::PARAM_INT );

		$this->bindSQL( $Query, ':frn_nom', $frn_nom, PDO::PARAM_STR, L_NOM );

		$this->bindSQL( $Query, ':frn_description', $frn_description, PDO::PARAM_LOB );


		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}


		if ( $frn_id == '' ) {
			switch( $this->getAttribute(PDO::ATTR_DRIVER_NAME) ) {
			 default;
				$this->LastInsertId = $this->lastInsertId();
				break;

			 case 'pgsql';
				$this->LastInsertId = $this->lastInsertId( 'frn_fournisseurs_frn_id_seq' );
				break;
			}
		}
		
		return TRUE;
	}


	public function majFournisseurParChamp( $frn_id, $Field, $Value ) {
	/**
	* Crée ou actualise les champs courants d'un Fournisseur.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-01-10
	*
	* \param[in] $frn_id Identifiant du Fournisseur
	* \param[in] $Field Nom du champ du Fournisseur à modifier
	* \param[in] $Value Valeur du champ du Fournisseur à prendre en compte
	*
	* \return Renvoi un booléen sur le succès de la création ou la modification de l'application
	*/
		if ( $frn_id == '' or $Field == '' or $Value == '' ) return FALSE;


		$Request = 'UPDATE frn_fournisseurs SET ';

		switch ( $Field ) {
			case 'tfr_id':
			case 'frn_nom':
			case 'frn_description':
				$Request .= $Field . ' = :Value ';
				break;

			default:
				return FALSE;
		}

		$Request .= 'WHERE frn_id = :frn_id';

		$Query = $this->prepareSQL( $Request );


		$this->bindSQL( $Query, ':frn_id', $frn_id, PDO::PARAM_INT );
		
		switch ( $Field ) {
			case 'tfr_id':
				$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_INT );
				break;

			case 'frn_nom':
				$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_STR, L_NOM );
				break;
			
			case 'frn_description':
				$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_LOB );
				break;
		}


		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		
		return TRUE;
	}


	public function rechercherFournisseurs( $cmp_id = '', $Order = 'frn_nom', $frn_id = '' ) {
	/**
	* Lister les Fournisseurs.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-01-10
	*
	* \param[in] $cmp_id Identifiant de la Campagne de rattachement
	* \param[in] $Order Permet de gérer l'ordre d'affichage
	* \param[in] $frn_id Identifiant du Fournisseur spécifique
	*
	* \return Renvoi une liste de Fournisseurs ou une liste vide
	*/
		$Where = '';

		$Request = 'SELECT
			*
			FROM frn_fournisseurs AS "frn"
			LEFT JOIN tfr_types_fournisseur AS "tfr" ON tfr.tfr_id = frn.tfr_id ';

		if ( $cmp_id != '' ) {
			$Request .= 'LEFT JOIN cmfr_cmp_frn AS "cmfr" ON cmfr.frn_id = frn.frn_id ';
		}
			
		if ( $frn_id != '' ) $Where .= 'WHERE frn_id = :frn_id ';

		if ( $cmp_id != '' ) {
			if ( $Where != '' ) {
				$Where .= 'AND ';
			} else {
				$Where .= 'WHERE ';
			}

			$Where .= 'cmp_id = :cmp_id ';
		}
		
		$Request = $Request . $Where;
		
		switch ( $Order ) {
		 default:
		 case 'frn_nom':
			$Request .= 'ORDER BY frn_nom ';
			break;

		 case 'frn_nom-desc':
			$Request .= 'ORDER BY frn_nom DESC ';
			break;

		 case 'frn_description':
			$Request .= 'ORDER BY frn_description ';
			break;

		 case 'frn_description-desc':
			$Request .= 'ORDER BY frn_description DESC ';
			break;

		 case 'tfr_nom_code':
			$Request .= 'ORDER BY tfr_nom_code ';
			break;

		 case 'tfr_nom_code-desc':
			$Request .= 'ORDER BY tfr_nom_code DESC ';
			break;
		}

		$Query = $this->prepareSQL( $Request );

		if ( $frn_id != '' ) $this->bindSQL( $Query, ':frn_id', $frn_id, PDO::PARAM_INT ) ;

		if ( $cmp_id != '' ) $this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT ) ;
		
		$this->executeSQL( $Query );

		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function supprimerFournisseur( $frn_id = '' ) {
	/**
	* Supprime un Fournisseur.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-02-22
	*
	* \param[in] $frn_id Identifiant du Fournisseur à supprimer
	*
	* \return Renvoi TRUE si l'occurrence a été supprimée, sinon FALSE. Lève une Exception en cas d'erreur.
	*/
		if ( $frn_id == '' ) return FALSE;
	
		$Query = $this->prepareSQL( 'DELETE ' .
		 'FROM frn_fournisseurs ' .
		 'WHERE frn_id = :frn_id' );
		
		$this->bindSQL( $Query, ':frn_id', $frn_id, PDO::PARAM_INT ) ;
		
		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return TRUE;
	}


	public function rechercherTypesFournisseur( $tfr_id = '', $colonne_trie = 'tfr_nom_code' ) {
	/**
	* Lister les Types de Fournisseurs.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-02-22
	*
	* \return Renvoi la liste des types de Fournisseur ou une liste vide. Lève une Exception en cas d'erreur.
	*/
		$Request = 'SELECT * FROM tfr_types_fournisseur AS "tfr" ';
		
		if ( $tfr_id != '' ) $Request .= 'WHERE tfr_id = :tfr_id ';

		switch ( $colonne_trie ) {
			case 'tfr_nom_code':
				$Request .= 'ORDER BY tfr_nom_code ';
				break;

			case 'tfr_nom_code-desc':
				$Request .= 'ORDER BY tfr_nom_code DESC ';
				break;
		}

		$Query = $this->prepareSQL( $Request );

		if ( $tfr_id != '' ) $this->bindSQL( $Query, ':tfr_id', $tfr_id, PDO::PARAM_INT ) ;

		$this->executeSQL( $Query );

		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function controlerAssociationFournisseur( $frn_id ) {
	/**
	* Vérifie si ce Fournisseur est associé à un autre objet.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-02-22
	*
	* \param[in] $frn_id Identifiant du Fournisseur à contrôler
	*
	* \return Renvoi l'occurrence listant les associations ou FALSE si pas d'entité. Lève une Exception en cas d'erreur.
	*/
		$Request = 'SELECT
		COUNT(DISTINCT act_id) AS total_act
		FROM frn_fournisseurs AS "frn"
		LEFT JOIN acfr_act_frn AS "acfr" ON acfr.frn_id = frn.frn_id
		WHERE frn.frn_id = :frn_id ';

		 
		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':frn_id', $frn_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return $Query->fetchObject();
	}


	public function totalFournisseurs() {
	/**
	* Calcul le nombre total de Fournisseurs.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-02-22
	*
	* \return Renvoi le nombre total de Fournisseurs stockées en base. Lève une Exception en cas d'erreur.
	*/

		$Request = 'SELECT ' .
		 'count(*) AS total ' .
		 'FROM frn_fournisseurs ' ;

		$Query = $this->prepareSQL( $Request );

		$this->executeSQL( $Query );
		
		$Occurrence = $Query->fetchObject() ;
		
		return $Occurrence->total;
	}
	
	
	
	public function majTypeFournisseur( $tfr_id, $tfr_nom_code ) {
		/**
		 * Crée ou met à jour un Type de Fournisseur.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-02-23
		 *
		 * \param[in] $tfr_id Identifiant du Type de Fournisseur
		 * \param[in] $tfr_nom_code Nom de code du Type de Fournisseur
		 *
		 * \return Renvoi un booléen sur le succès de la création ou la modification de l'application
		 */
		if ( $tfr_id == '' ) {
			$Request = 'INSERT INTO tfr_types_fournisseur
				( tfr_nom_code )
				VALUES ( :tfr_nom_code ) ';
			
			$Query = $this->prepareSQL( $Request );
		} else {
			$Request = 'UPDATE tfr_types_fournisseur SET
				tfr_nom_code = :tfr_nom_code
				WHERE tfr_id = :tfr_id';
			
			$Query = $this->prepareSQL( $Request );
			
			$this->bindSQL( $Query, ':tfr_id', $tfr_id, PDO::PARAM_INT );
		}
		
		
		$this->bindSQL( $Query, ':tfr_nom_code', $tfr_nom_code, PDO::PARAM_STR, L_NOM_CODE );
		
		
		$this->executeSQL( $Query );
		
		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
		
		
		if ( $tfr_id == '' ) {
			switch( $this->getAttribute(PDO::ATTR_DRIVER_NAME) ) {
				default;
				$this->LastInsertId = $this->lastInsertId();
				break;
				
				case 'pgsql';
				$this->LastInsertId = $this->lastInsertId( 'tfr_types_fournisseur_tfr_id_seq' );
				break;
			}
		}
		
		return TRUE;
	}


	public function majTypeFournisseurParChamp( $tfr_id, $Field, $Value ) {
		/**
		 * Crée ou actualise les champs courants d'un Type de Fournisseur.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-02-29
		 *
		 * \param[in] $tfr_id Identifiant du Type de Fournisseur
		 * \param[in] $Field Nom du champ du Fournisseur à modifier
		 * \param[in] $Value Valeur du champ du Fournisseur à prendre en compte
		 *
		 * \return Renvoi un booléen sur le succès de la création ou la modification de l'application
		 */
		if ( $tfr_id == '' or $Field == '' or $Value == '' ) return FALSE;
		
		
		$Request = 'UPDATE tfr_types_fournisseur SET ';
		
		switch ( $Field ) {
			case 'tfr_nom_code':
				$Request .= $Field . ' = :Value ';
				break;
				
			default:
				return FALSE;
		}
		
		$Request .= 'WHERE tfr_id = :tfr_id';
		
		$Query = $this->prepareSQL( $Request );
		
		
		$this->bindSQL( $Query, ':tfr_id', $tfr_id, PDO::PARAM_INT );
		
		switch ( $Field ) {
			case 'tfr_nom_code':
				$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_STR, L_NOM_CODE );
				break;
		}
		
		
		$this->executeSQL( $Query );
		
		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
		
		
		return TRUE;
	}
	
	
	public function controlerAssociationTypeFournisseur( $tfr_id ) {
		/**
		 * Vérifie si ce Type de Fournisseur est associé à un autre objet.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-03-01
		 *
		 * \param[in] $tfr_id Identifiant du Type de Fournisseur à contrôler
		 *
		 * \return Renvoi l'occurrence listant les associations ou FALSE si pas d'entité. Lève une Exception en cas d'erreur.
		 */
		$Request = 'SELECT ' .
			'COUNT(DISTINCT frn_id) AS total_frn ' .
			'FROM tfr_types_fournisseur AS "tfr" ' .
			'LEFT JOIN frn_fournisseurs AS "frn" ON frn.frn_id = tfr.tfr_id ' .
			'WHERE tfr.tfr_id = :tfr_id ';
		
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':tfr_id', $tfr_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
		
		return $Query->fetchObject();
	}
	

	public function supprimerTypeFournisseur( $tfr_id = '' ) {
		/**
		 * Supprime un Type de Fournisseur.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-02-29
		 *
		 * \param[in] $tfr_id Identifiant du Type de Fournisseur à supprimer
		 *
		 * \return Renvoi TRUE si l'occurrence a été supprimée, sinon FALSE. Lève une Exception en cas d'erreur.
		 */
		if ( $tfr_id == '' ) return FALSE;

		$Query = $this->prepareSQL( 'DELETE ' .
			'FROM tfr_types_fournisseur ' .
			'WHERE tfr_id = :tfr_id' );

		$this->bindSQL( $Query, ':tfr_id', $tfr_id, PDO::PARAM_INT ) ;

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return TRUE;
	}
}

?>