<?php

include_once( 'Constants.inc.php' );
include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Connecteur_BD_PDO.inc.php' );


if ( ! defined( 'L_NOM' ) ) define( 'L_NOM', 35 );
if ( ! defined( 'L_PRENOM' ) ) define( 'L_PRENOM', 25 );
if ( ! defined( 'L_TRIGRAMME' ) ) define( 'L_TRIGRAMME', 3 );
if ( ! defined( 'L_NOM_CODE' ) ) define( 'L_NOM_CODE', 60 );


class PartiesPrenantes extends HBL_Connexioneur_BD {

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


	public function majPartiePrenante( $ppr_id, $sct_id, $ppr_nom, $ppr_prenom, $ppr_interne, $ppr_description='' ) {
	/**
	* Crée ou met à jour une Partie Prenante.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-02-26
	*
	* \param[in] $ppr_id Identifiant de la Partie Prenante (à préciser si modification)
	* \param[in] $sct_id Identifiant de la Société de rattachement
	* \param[in] $ppr_nom Nom de la Partie Prenante
	* \param[in] $ppr_prenom Prénom de la Partie Prenante
	* \param[in] $ppr_interne Flag pour déterminer s'il s'agit d'une Partie Prenante interne
	* \param[in] $ppr_description Description de la Partie Prenante
	*
	* \return Renvoi un booléen sur le succès de la création ou la modification de l'application
	*/
		if ( $ppr_id == '' ) {
			$Request = 'INSERT INTO ppr_parties_prenantes
				( sct_id, ppr_nom, ppr_prenom, ppr_interne, ppr_description )
				VALUES ( :sct_id, :ppr_nom, :ppr_prenom, :ppr_interne, :ppr_description ) ';

			$Query = $this->prepareSQL( $Request );
		} else {
			$Request = 'UPDATE ppr_parties_prenantes SET
				sct_id = :sct_id,
				ppr_nom = :ppr_nom,
				ppr_prenom = :ppr_prenom,
				ppr_interne = :ppr_interne,
				ppr_description = :ppr_description
				WHERE ppr_id = :ppr_id';

			$Query = $this->prepareSQL( $Request );
			
			$this->bindSQL( $Query, ':ppr_id', $ppr_id, PDO::PARAM_INT );
		}


		$this->bindSQL( $Query, ':sct_id', $sct_id, PDO::PARAM_INT );

		$this->bindSQL( $Query, ':ppr_nom', $ppr_nom, PDO::PARAM_STR, L_NOM );

		$this->bindSQL( $Query, ':ppr_prenom', $ppr_prenom, PDO::PARAM_STR, L_PRENOM );
		
		$this->bindSQL( $Query, ':ppr_interne', $ppr_interne, PDO::PARAM_BOOL );
		
		$this->bindSQL( $Query, ':ppr_description', $ppr_description, PDO::PARAM_LOB );
		

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}


		if ( $ppr_id == '' ) {
			switch( $this->getAttribute(PDO::ATTR_DRIVER_NAME) ) {
			 default;
				$this->LastInsertId = $this->lastInsertId();
				break;

			 case 'pgsql';
				$this->LastInsertId = $this->lastInsertId( 'ppr_parties_prenantes_ppr_id_seq' );
				break;
			}
		}
		
		return TRUE;
	}


	public function majPartiePrenanteParChamp( $ppr_id, $Field, $Value ) {
	/**
	* Actualise les champs d'une Partie Prenante.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-02-26
	*
	* \param[in] $ppr_id Identifiant de la Partie Prenante
	* \param[in] $Field Nom du champ de la Partie Prenante à modifier
	* \param[in] $Value Valeur du champ de la Partie Prenante à prendre en compte
	*
	* \return Renvoi un booléen sur le succès de la création ou la modification de l'application
	*/
		if ( $ppr_id == '' or $Field == '' or $Value == '' ) return FALSE;


		$Request = 'UPDATE ppr_parties_prenantes SET ';

		switch ( $Field ) {
			case 'sct_id':
			case 'ppr_nom':
			case 'ppr_prenom':
			case 'ppr_interne':
			case 'ppr_description':
				$Request .= $Field . ' = :Value ';
				break;

			default:
				return FALSE;
		}

		$Request .= 'WHERE ppr_id = :ppr_id';

		$Query = $this->prepareSQL( $Request );


		$this->bindSQL( $Query, ':ppr_id', $ppr_id, PDO::PARAM_INT );
		
		switch ( $Field ) {
			case 'sct_id':
				$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_INT );
				break;

			case 'ppr_nom':
				$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_STR, L_NOM );
				break;

			case 'ppr_prenom':
				$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_STR, L_PRENOM );
				break;
				
			case 'ppr_interne':
				$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_BOOL );
				break;
				
			case 'ppr_description':
				$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_LOB );
				break;
		}


		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		
		return TRUE;
	}


	public function rechercherPartiesPrenantes( $sct_id, $Order = 'ppr_nom', $ppr_id = '' ) {
	/**
	* Lister les Parties Prenantes.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-02-27
	*
	* \param[in] $sct_id Identifiant de la Société de rattachement
	* \param[in] $Order Permet de gérer l'ordre d'affichage
	* \param[in] $ppr_id Identifiant de la Partie Prenante spécifique à récupérer
	*
	* \return Renvoi une liste de Parties Prenantes ou une liste vide
	*/
		$Request = 'SELECT
			*
			FROM ppr_parties_prenantes AS "ppr" 
			WHERE sct_id = :sct_id ';
		
		if ( $ppr_id != '' ) $Request .= 'AND ppr_id = :ppr_id ';
		
		switch ( $Order ) {
		 default:
		 case 'ppr_nom':
			$Request .= 'ORDER BY ppr_nom ';
			break;

		 case 'ppr_nom-desc':
			$Request .= 'ORDER BY ppr_nom DESC ';
			break;

		 case 'ppr_prenom':
			$Request .= 'ORDER BY ppr_prenom ';
			break;

		 case 'ppr_prenom-desc':
			$Request .= 'ORDER BY ppr_prenom DESC ';
			break;

		 case 'ppr_interne':
			$Request .= 'ORDER BY ppr_interne ';
			break;

		 case 'ppr_interne-desc':
			$Request .= 'ORDER BY ppr_interne DESC ';
			break;

		 case 'ppr_description':
			$Request .= 'ORDER BY ppr_description ';
			break;

		 case 'ppr_description-desc':
			$Request .= 'ORDER BY ppr_description DESC ';
			break;
		}

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':sct_id', $sct_id, PDO::PARAM_INT ) ;

		if ( $ppr_id != '' ) $this->bindSQL( $Query, ':ppr_id', $ppr_id, PDO::PARAM_INT ) ;

		$this->executeSQL( $Query );

		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function supprimerPartiePrenante( $ppr_id ) {
	/**
	* Supprime une Partie Prenante.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-02-27
	*
	* \param[in] $ppr_id Identifiant de la Partie Prenante à supprimer
	*
	* \return Renvoi TRUE si l'occurrence a été supprimée, sinon FALSE. Lève une Exception en cas d'erreur.
	*/
		if ( $ppr_id == '' ) return FALSE;
	
		$Query = $this->prepareSQL( 'DELETE ' .
		 'FROM ppr_parties_prenantes ' .
		 'WHERE ppr_id = :ppr_id' );
		
		$this->bindSQL( $Query, ':ppr_id', $ppr_id, PDO::PARAM_INT ) ;
		
		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return TRUE;
	}


	public function rechercherRolesPartiePrenante( $colonne_trie = 'rpp_nom_code', $rpp_id = '' ) {
	/**
	* Lister les Types de Partie Prenante.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-02-27
	*
	* \return Renvoi la liste des Types de Partie Prenante ou une liste vide. Lève une Exception en cas d'erreur.
	*/
		$Request = 'SELECT * FROM rpp_roles_parties_prenantes AS "rpp" ';
		
		if ( $rpp_id != '' ) $Request .= 'WHERE rpp_id = :rpp_id ';
		
		switch( $colonne_trie ) {
			case 'rpp_nom_code':
				$Request .= 'ORDER BY rpp_nom_code ';
				break;

			case 'rpp_nom_code-desc':
				$Request .= 'ORDER BY rpp_nom_code DESC ';
				break;
		}
		 
		$Query = $this->prepareSQL( $Request );
		
		if ( $rpp_id!= '' ) $this->bindSQL( $Query, ':rpp_id', $rpp_id, PDO::PARAM_INT ) ;

		$this->executeSQL( $Query );
		 
 		return $Query->fetchAll( PDO::FETCH_CLASS );
	}


	public function controlerAssociationPartiePrenante( $ppr_id ) {
	/**
	* Vérifie si cette Partie Prenante est associée à un autre objet.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-02-27
	*
	* \param[in] $ppr_id Identifiant de la Partie Prenante à contrôler
	*
	* \return Renvoi l'occurrence listant les associations ou FALSE si pas d'association. Lève une Exception en cas d'erreur.
	*/
		$Request = 'SELECT ' .
		 'COUNT(DISTINCT act_id) AS total_act ' .
		 'FROM ppr_parties_prenantes AS "ppr" ' .
		 'LEFT JOIN ppac_ppr_act AS "ppac" ON ppac.ppr_id = ppr.ppr_id ' .
		 'WHERE ppr.ppr_id = :ppr_id ';

		 
		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':ppr_id', $ppr_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		return $Query->fetchObject();
	}


	public function totalPartiesPrenantes() {
	/**
	* Calcul le nombre total de Partie Prenante.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2024-02-22
	*
	* \return Renvoi le nombre total de Partie Prenante stockées en base. Lève une Exception en cas d'erreur.
	*/

		$Request = 'SELECT ' .
		 'count(*) AS total ' .
		 'FROM ppr_parties_prenantes ' ;

		$Query = $this->prepareSQL( $Request );

		$this->executeSQL( $Query );
		
		$Occurrence = $Query->fetchObject() ;
		
		return $Occurrence->total;
	}
	
	
	
	public function majRolePartiePrenante( $rpp_id, $rpp_nom_code ) {
		/**
		 * Crée ou met à jour le Rôle d'une Partie Prenante.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-02-27
		 *
		 * \param[in] $rpp_id Identifiant du Rôle de la Partie Prenante
		 * \param[in] $rpp_nom_code Nom de code du Rôle de la Partie Prenante
		 *
		 * \return Renvoi un booléen sur le succès de la création ou la modification du Rôle de la Partie Prenante
		 */
		if ( $rpp_id == '' ) {
			$Request = 'INSERT INTO rpp_roles_parties_prenantes
				( rpp_nom_code )
				VALUES ( :rpp_nom_code ) ';
			
			$Query = $this->prepareSQL( $Request );
		} else {
			$Request = 'UPDATE rpp_roles_parties_prenantes SET
				rpp_nom_code = :rpp_nom_code
				WHERE rpp_id = :rpp_id';
			
			$Query = $this->prepareSQL( $Request );
			
			$this->bindSQL( $Query, ':rpp_id', $rpp_id, PDO::PARAM_INT );
		}
		
		
		$this->bindSQL( $Query, ':rpp_nom_code', $rpp_nom_code, PDO::PARAM_STR, L_NOM_CODE );
		
		
		$this->executeSQL( $Query );
		
		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
		
		
		if ( $rpp_id == '' ) {
			switch( $this->getAttribute(PDO::ATTR_DRIVER_NAME) ) {
				default;
				$this->LastInsertId = $this->lastInsertId();
				break;
				
				case 'pgsql';
				$this->LastInsertId = $this->lastInsertId( 'rpp_roles_parties_prenantes_rpp_id_seq' );
				break;
			}
		}
		
		return TRUE;
	}


	public function majRolePartiePrenanteParChamp( $rpp_id, $Field, $Value ) {
		/**
		 * Actualise les champs d'un Role des Parties Prenantes.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-02-29
		 *
		 * \param[in] $rpp_id Identifiant de la Partie Prenante
		 * \param[in] $Field Nom du champ de la Partie Prenante à modifier
		 * \param[in] $Value Valeur du champ de la Partie Prenante à prendre en compte
		 *
		 * \return Renvoi un booléen sur le succès de la création ou la modification de l'application
		 */
		if ( $rpp_id == '' or $Field == '' or $Value == '' ) return FALSE;
		
		
		$Request = 'UPDATE rpp_roles_parties_prenantes SET ';
		
		switch ( $Field ) {
			case 'rpp_nom_code':
				$Request .= $Field . ' = :Value ';
				break;
				
			default:
				return FALSE;
		}
		
		$Request .= 'WHERE rpp_id = :rpp_id';
		
		$Query = $this->prepareSQL( $Request );
		
		
		$this->bindSQL( $Query, ':rpp_id', $rpp_id, PDO::PARAM_INT );
		
		switch ( $Field ) {
			case 'rpp_nom_code':
				$this->bindSQL( $Query, ':Value', $Value, PDO::PARAM_STR, L_NOM_CODE );
				break;
		}
		
		
		$this->executeSQL( $Query );
		
		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
		
		
		return TRUE;
	}


	public function supprimerRolePartiePrenante( $rpp_id ) {
		/**
		 * Supprime le Rôle d'une Partie Prenante.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-02-29
		 *
		 * \param[in] $rpp_id Identifiant du Rôle de la Partie Prenante
		 *
		 * \return Renvoi un TRUE en cas de succès, sinon FALSE
		 */
		if ( $rpp_id == '' ) return FALSE;
		
		$Query = $this->prepareSQL( 'DELETE ' .
			'FROM rpp_roles_parties_prenantes ' .
			'WHERE rpp_id = :rpp_id' );
		
		$this->bindSQL( $Query, ':rpp_id', $rpp_id, PDO::PARAM_INT ) ;
		
		$this->executeSQL( $Query );
		
		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
		
		return TRUE;
	}


	public function totalRolesPartiePrenante() {
		/**
		 * Calcul le nombre total des Types de Partie Prenante.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-02-22
		 *
		 * \return Renvoi le nombre total des Types de Partie Prenante stockées en base. Lève une Exception en cas d'erreur.
		 */
		
		$Request = 'SELECT ' .
			'count(*) AS total ' .
			'FROM rpp_roles_parties_prenantes ' ;
		
		$Query = $this->prepareSQL( $Request );
		
		$this->executeSQL( $Query );
		
		$Occurrence = $Query->fetchObject() ;
		
		return $Occurrence->total;
	}
}

?>