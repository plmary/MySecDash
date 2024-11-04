<?php

include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Securite.inc.php' );


class LibellesReferentiel extends HBL_Securite {
/**
* Cette classe gère les Libellés du Référentiel.
*
* \license Loxense
* \author Pierre-Luc MARY
* \version 1.0
*/

	const CODE_TYPE = PDO::PARAM_STR;
	const CODE_LENGTH = 45;

	const LANGUE_TYPE = PDO::PARAM_STR;
	const LANGUE_LENGTH = 2;

	const LIBELLE_TYPE = PDO::PARAM_STR;
	const LIBELLE_LENGTH = 300;


	public function __construct() {
	/**
	* Connexion à la base de données via IICA_DB_Connector.
	*
	* \license Loxense
	* \author Pierre-Luc MARY
	* \version 1.0
	* \date 2016-10-24
	*
	* \return Renvoi un booléen sur le succès de la connexion à la base de données
	*/
		parent::__construct();

		return true;
	}


	/* =============================================================================== */


	public function ajouterLibelleReferentiel( $Code, $Langue, $Libelle ) {
	/**
	* Met à jour les libellés du Référentiel
	*
	* \license Loxense
	* \author Pierre-Luc MARY
	* \version 1.0
	* \date 2015-11-20
	*
	* \param[in] $Code le code du libellé du référenciel
	* \param[in] $Langue la langue du libellé à ajouter
	* \param[in] $Libelle le libellé à ajouter
	*
	* \return Renvoi vrai si le libellé a été créé, sinon lève une Exception 
	*/
		$Code   = mb_strtoupper( trim( $Code ) );
		$Langue = mb_strtolower( trim( $Langue ) );

		if ( $Langue == 'fr' ) $Autre_Langue = 'en';
		else $Autre_Langue = 'fr';
		
		$Requete = $this->prepareSQL(
			'INSERT INTO lbr_libelles_referentiel '.
			'(lbr_code, lng_id, lbr_libelle) ' .
			'VALUES ' .
			'(:Code, :Langue, :Libelle)'
		);

		$this->bindSQL($Requete, ':Code', $Code, self::CODE_TYPE, self::CODE_LENGTH);
		$this->bindSQL($Requete, ':Langue', $Langue, self::LANGUE_TYPE, self::LANGUE_LENGTH);
		$this->bindSQL($Requete, ':Libelle', $Libelle, self::LIBELLE_TYPE, self::LIBELLE_LENGTH);

		$this->executeSQL($Requete);

		
		$Requete = $this->prepareSQL(
			'SELECT COUNT( lbr_code ) AS "total" FROM lbr_libelles_referentiel '.
			'WHERE lbr_code = :Code AND lng_id = :Langue AND lbr_libelle = :Libelle '
			);
		
		$this->bindSQL($Requete, ':Code', $Code, self::CODE_TYPE, self::CODE_LENGTH);
		$this->bindSQL($Requete, ':Langue', $Autre_Langue, self::LANGUE_TYPE, self::LANGUE_LENGTH);
		$this->bindSQL($Requete, ':Libelle', $Libelle, self::LIBELLE_TYPE, self::LIBELLE_LENGTH);
		
		$this->executeSQL($Requete);
		$Resultat = $Requete->fetch( PDO::FETCH_OBJ )->total;

		if ( $Resultat == 1 ) {
			$Requete = $this->prepareSQL(
				'INSERT INTO lbr_libelles_referentiel '.
				'(lbr_code, lng_id, lbr_libelle) ' .
				'VALUES ' .
				'(:Code, :Langue, :Libelle)'
				);
			
			$this->bindSQL($Requete, ':Code', $Code, self::CODE_TYPE, self::CODE_LENGTH);
			$this->bindSQL($Requete, ':Langue', $Autre_Langue, self::LANGUE_TYPE, self::LANGUE_LENGTH);
			$this->bindSQL($Requete, ':Libelle', $Libelle, self::LIBELLE_TYPE, self::LIBELLE_LENGTH);
			
			$this->executeSQL($Requete);
		}

		return TRUE;
	}
	
	
	public function modifierLibelleReferentiel( $Code, $Langue, $Libelle ) {
		/**
		 * Met à jour le Libellé associé à un code du Référentiel
		 *
		 * \license Loxense
		 * \author Pierre-Luc MARY
		 * \date 2020-03-23
		 *
		 * \param[in] $Code Code à rechercher dans la base des libellés
		 * \param[in] $Langue Langue associée au code
		 * \param[in] $Libelle Libellé à associer au Code et à la Langue
		 *
		 * \return Renvoi vrai si le libellé a été modifié, sinon lève une Exception
		 */
		$Sql = 'UPDATE lbr_libelles_referentiel '.
			'SET lbr_libelle = :Libelle ' .
			'WHERE lbr_code = :Code AND lng_id = :Langue ';

		$Requete = $this->prepareSQL( $Sql );
		
		$Code   = mb_strtoupper( trim( $Code ) );
		$Langue = mb_strtolower( trim( $Langue ) );
		$Libelle = trim( $Libelle );
		
		$this->bindSQL($Requete, ':Code', $Code, self::CODE_TYPE, self::CODE_LENGTH);
		$this->bindSQL($Requete, ':Langue', $Langue, self::LANGUE_TYPE, self::LANGUE_LENGTH);
		$this->bindSQL($Requete, ':Libelle', $Libelle, self::LIBELLE_TYPE, self::LIBELLE_LENGTH);
		
		$this->executeSQL($Requete);
		
		return TRUE;
	}
	

	public function modifierCodeLibelleReferentiel( $Ancien_Code, $Nouveau_Code ) {
	/**
	* Met à jour le Code d'un Libellé du Référentiel
	*
	* \license Loxense
	* \author Pierre-Luc MARY
	* \date 2022-11-30
	*
	* \param[in] $Ancien_Code Code à rechercher
	* \param[in] $Nouveau_Code Code à remplacer
	*
	* \return Renvoi vrai si le libellé a été modifié, sinon lève une Exception 
	*/
		$SQL = 'UPDATE lbr_libelles_referentiel '.
			'SET lbr_code = :Nouveau_Code ' .
			'WHERE lbr_code = :Ancien_Code ';
		
		$Requete = $this->prepareSQL( $SQL );

		$Ancien_Code  = mb_strtoupper( trim( $Ancien_Code ) );
		$Nouveau_Code = mb_strtoupper( trim( $Nouveau_Code ) );
			
		$this->bindSQL($Requete, ':Ancien_Code', $Ancien_Code, self::CODE_TYPE, self::CODE_LENGTH);
		$this->bindSQL($Requete, ':Nouveau_Code', $Nouveau_Code, self::CODE_TYPE, self::CODE_LENGTH);

		$this->executeSQL($Requete);

		return TRUE;
	}


	public function listerLibelleReferentiel( $Code, $Langue = 'fr',  $TypeRecherche = 'S' ) {
	/**
	* Lister les libellés du référentiel
	*
	* \license Loxense
	* \author Pierre-Luc MARY
	* \version 1.0
	* \date 2015-11-20
	*
	* \param[in] $Code le code du libellé du référenciel à rechercher
	* \param[in] $Langue la langue du libellé qui est recherhée (si $Langue = *, alors toutes les langues sont recherchées)
	* \param[in] $TypeRecherche le type de recherche ('D':débute par, 'T':termine par, 'C':contient, 'S':strict)
	*
	* \return Renvoi les libellés trouvés ou une liste vide si aucune correspondance. Lève une Exception en cas d'erreur.
	*/
		if ( $Code == '' ) return array();

		$Code = mb_strtoupper( trim( $Code ) );
		$Langue = mb_strtolower( trim( $Langue ) );
		$TypeRecherche = mb_strtoupper( trim( $TypeRecherche ) );

		if ( $TypeRecherche == 'S' ) $OperateurRecherche = '=';
		else $OperateurRecherche = 'LIKE';

		$Recherche = "SELECT lbr_code, lng_id, lbr_libelle " .
			"FROM lbr_libelles_referentiel AS rlb " .
			"WHERE lbr_code " . $OperateurRecherche . " :Code ";

		if ( $Langue != '*' ) {
			$Recherche .= 'AND lng_id = :Langue ';
		}

		$Recherche .= 'ORDER BY lbr_code, lng_id ';

		$Requete = $this->prepareSQL( $Recherche );

		switch( $TypeRecherche ) {
		 case 'D':
			$Code = $Code . '%';
			break;
		 case 'T':
			$Code = '%' . $Code;
			break;
		 case 'C':
			$Code = '%' . $Code . '%';
			break;
		}

		$this->bindSQL( $Requete, ':Code', $Code, self::CODE_TYPE, self::CODE_LENGTH );

		if ( $Langue != '*' ) $this->bindSQL( $Requete, ':Langue', $Langue, self::LANGUE_TYPE, self::LANGUE_LENGTH );

		$this->executeSQL( $Requete);

		return	$Requete->fetchAll( PDO::FETCH_CLASS );
	}


	public function supprimerLibelleReferentiel( $Code, $Langue = '*', $TypeRecherche = 'S' ) {
	/**
	* Supprime des libellés du référentiel
	*
	* \license Loxense
	* \author Pierre-Luc MARY
	* \version 1.0
	* \date 2015-11-20
	*
	* \param[in] $Code le code du libellé du référenciel à supprimer
	* \param[in] $Langue la langue du libellé qui est supprimée (si $Langue = '*', alors toutes les langues sont supprimées)
	* \param[in] $TypeRecherche le type de recherche ('D':débute par, 'T':termine par, 'C':contient, 'S':strict)
	*
	* \return Renvoi vrai si le libellé a été créée, sinon lève une Exception 
	*/
		if ( $TypeRecherche == 'S' ) $OperateurRecherche = '=';
		else $OperateurRecherche = 'LIKE';
		
		$Code = mb_strtoupper( trim( $Code ) );
		$Langue = mb_strtolower( trim( $Langue ) );
		
		switch( $TypeRecherche ) {
			case 'D':
				$Code = $Code . '%';
				break;
			case 'T':
				$Code = '%' . $Code;
				break;
			case 'C':
				$Code = '%' . $Code . '%';
				break;
		}
		

		$SQL = 'SELECT COUNT(lbr_code) AS "total" FROM lbr_libelles_referentiel ' .
			'WHERE lbr_code ' . $OperateurRecherche . ' :code ';

		if ( $Langue != '*' ) {
			$SQL .= 'AND lng_id = :langue ';
		}

		$Requete = $this->prepareSQL( $SQL );

		$this->bindSQL( $Requete, ':code', $Code, self::CODE_TYPE, self::CODE_LENGTH );

		if ( $Langue != '*' ) {
			$this->bindSQL( $Requete, ':langue', $Langue, self::LANGUE_TYPE, self::LANGUE_LENGTH );
		}

		$this->executeSQL($Requete);
		$Resultat = $Requete->fetch( PDO::FETCH_OBJ )->total;

		if ( $Resultat > 0 ) {
			$SQL = 'DELETE FROM lbr_libelles_referentiel ' .
				'WHERE lbr_code ' . $OperateurRecherche . ' :code ';
			
			if ( $Langue != '*' ) {
				$SQL .= 'AND lng_id = :langue ';
			}
			
			$Requete = $this->prepareSQL( $SQL );
			
			$this->bindSQL( $Requete, ':code', $Code, self::CODE_TYPE, self::CODE_LENGTH );
			
			if ( $Langue != '*' ) {
				$this->bindSQL( $Requete, ':langue', $Langue, self::LANGUE_TYPE, self::LANGUE_LENGTH );
			}
			
			$this->executeSQL($Requete);
		}
		
		return TRUE;
	}


	public function compterLibelleReferentiel( $Code, $Langue = 'fr',  $TypeRecherche = 'S' ) {
	/**
	* Compte le nombre de libellés
	*
	* \license Loxense
	* \author Pierre-Luc MARY
	* \version 1.0
	* \date 2015-11-20
	*
	* \param[in] $Code le code du libellé du référenciel à rechercher
	* \param[in] $Langue la langue du libellé qui est supprimée (si $Langue = *, alors toutes les langues sont recherchées)
	* \param[in] $TypeRecherche le type de recherche ('D':débute par, 'T':termine par, 'C':contient, 'S':strict)
	*
	* \return Renvoi le nombre de libellés trouvés, sinon lève une Exception 
	*/
		if ( $Code == '' ) return 0;

		$Code = mb_strtoupper( trim( $Code ) );
		$Langue = mb_strtolower( trim( $Langue ) );
		$TypeRecherche = mb_strtoupper( trim( $TypeRecherche ) );

		if ( $TypeRecherche == 'S' ) $OperateurRecherche = '=';
		else $OperateurRecherche = 'LIKE';

		$Recherche = "SELECT count(*) AS total " .
			"FROM lbr_libelles_referentiel AS rlb " .
			"WHERE lbr_code " . $OperateurRecherche . " :Code ";

		if ( $Langue != '*' ) {
			$Recherche .= 'AND lng_id = :Langue ';
		}

		$Requete = $this->prepareSQL( $Recherche );
		
		switch( $TypeRecherche ) {
		 case 'D':
			$Code = $Code . '%';
			break;
		 case 'T':
			$Code = '%' . $Code;
			break;
		 case 'C':
			$Code = '%' . $Code . '%';
			break;
		}

		$this->bindSQL( $Requete, ':Code', $Code, self::CODE_TYPE, self::CODE_LENGTH );
		if ( $Langue != '*' ) $this->bindSQL( $Requete, ':Langue', $Langue, self::LANGUE_TYPE, self::LANGUE_LENGTH );

		$this->executeSQL( $Requete );

		return	$Requete->fetch( PDO::FETCH_OBJ )->total;
	}

}
?>