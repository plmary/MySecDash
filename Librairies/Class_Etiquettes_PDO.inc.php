<?php

include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Securite.inc.php' );


class Etiquettes extends HBL_Securite {
/**
* Cette classe gère les Etiquettes apposables sur les Actifs Primordiaux.
*
* PHP version 5
* \license Loxense
* \author Pierre-Luc MARY
* \version 1.0
* \date 2017-09-14
*/ 

	const ID_TYPE = PDO::PARAM_INT;

	const CODE_TYPE = PDO::PARAM_STR;
	const CODE_LENGTH = 10;

	const LIBELLE_TYPE = PDO::PARAM_STR;
	const LIBELLE_LENGTH = 60;

	const LANGUE_TYPE = PDO::PARAM_STR;
	const LANGUE_LENGTH = 2;

	const TEXTE_TYPE = PDO::PARAM_LOB;

	public $LastInsertId;
//	public $Cartographie;


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


	public function listerEtiquettes( $Trier = 'code', $tgs_id = '', $Recherche = '' ) {
	/**
	* Lister toutes les Etiquettes.
	*
	* \license Loxense
	* \author Pierre-Luc MARY
	* \date 2017-09-12
	*
	* \return Renvoi la liste des Etiquettes ou lève une exception en cas d'erreur.
	*/
		$Ordre = '';

/*		$SQL = 'SELECT tgs.tgs_id, tgs.tgs_code, tgs.tgs_libelle, tgs.tgs_description,
	COUNT( DISTINCT idtg.idn_id ) AS "total_idn",
	COUNT( DISTINCT aptg.apr_id ) AS "total_apr"
	FROM tgs_tags AS "tgs" 
	LEFT JOIN idtg_idn_tgs AS "idtg" ON idtg.tgs_id = tgs.tgs_id 
	LEFT JOIN aptg_apr_tgs AS "aptg" ON aptg.tgs_id = tgs.tgs_id '; */

		$SQL = 'SELECT tgs.tgs_id, tgs.tgs_code, tgs.tgs_libelle, tgs.tgs_description,
	COUNT( DISTINCT idtg.idn_id ) AS "total_idn"
	FROM tgs_tags AS "tgs"
	LEFT JOIN idtg_idn_tgs AS "idtg" ON idtg.tgs_id = tgs.tgs_id ';
		

		if ( $tgs_id != '' ) {
			$WHERE = 'WHERE tgs.tgs_id = :tgs_id ';
		} else {
			$WHERE = '';
		}


		if ( $Recherche != '' ) {
			if ( $WHERE == '' ) {
				$WHERE = 'WHERE LOWER(tgs.tgs_code) like :recherche OR LOWER(tgs.tgs_libelle) like :recherche ';
			} else {
				$WHERE = 'OR LOWER(tgs.tgs_code) like :recherche OR LOWER(tgs.tgs_libelle) like :recherche ';
			}
		}


		$SQL .= $WHERE;


		switch( $Trier ) {
		 default:
		 case 'code':
			$Ordre = 'ORDER BY tgs_code ';
			break;

		 case 'code-desc':
			$Ordre = 'ORDER BY tgs_code DESC ';
			break;

		 case 'libelle':
			$Ordre = 'ORDER BY tgs_libelle ';
			break;

		 case 'libelle-desc':
			$Ordre = 'ORDER BY tgs_libelle DESC ';
			break;

		 case 'total_idn':
			$Ordre = 'ORDER BY total_idn ';
			break;

		 case 'total_idn-desc':
			$Ordre = 'ORDER BY total_idn DESC ';
			break;

/*		 case 'total_apr':
			$Ordre = 'ORDER BY total_apr ';
			break;

		 case 'total_apr-desc':
			$Ordre = 'ORDER BY total_apr DESC ';
			break; */
		}

		$SQL .= 'GROUP BY tgs.tgs_id, tgs.tgs_code, tgs.tgs_libelle, tgs.tgs_description ' . $Ordre;

		$Requete = $this->prepareSQL( $SQL );

		if ( $tgs_id != '' ) $this->bindSQL( $Requete, ':tgs_id', $tgs_id, self::ID_TYPE );

		if ( $Recherche != '' ) $this->bindSQL( $Requete, ':recherche', '%'.strtolower($Recherche).'%', self::LIBELLE_TYPE, self::LIBELLE_LENGTH );

		return $this->executeSQL( $Requete )
			->fetchAll( PDO::FETCH_CLASS );
	}


	public function modifierChamp(	$Id, $Source, $Valeur ) {
	/**
	* Modifier une valeur à une Etiquette.
	*
	* \license Loxense
	* \author Pierre-Luc MARY
	* \date 2017-09-14
	*
	* \param[in] $Id Id. de l'Actif Primordial
	* \param[in] $Source Colonne de la table que l'on va modifier
	* \param[in] $Valeur Valeur que l'on associe à la Colonne de la table
	*
	* \return Renvoi "vrai" si l'occurrence a été modifiée, sinon, lève une exception. 
	*			
	*/
		$sql = 'UPDATE tgs_tags SET ' . $Source . '= :valeur ' .
			'WHERE tgs_id = :tgs_id ';

		$requete = $this->prepareSQL($sql);


		if ( $Source == 'tgs_code' ) $Valeur = mb_strtoupper( $Valeur );


		switch( $Source ) {
		 case 'tgs_code':
			$this->bindSQL($requete, ':valeur', $Valeur, self::CODE_TYPE, self::CODE_LENGTH);
			break;

		 case 'tgs_libelle':
			$this->bindSQL($requete, ':valeur', $Valeur, self::LIBELLE_TYPE, self::LIBELLE_LENGTH);
			break;

		 case 'tgs_description':
			$this->bindSQL($requete, ':valeur', $Valeur, self::TEXTE_TYPE);
			break;
		}


		$this->bindSQL($requete, ':tgs_id', $Id, self::ID_TYPE)
			->executeSQL($requete);

		return TRUE;
	}


	public function ajouterEtiquette( $Code, $Libelle, $Description = '' ) {
	/**
	* Ajoute une nouvelle Etiquette.
	*
	* \license Loxense
	* \author Pierre-Luc MARY
	* \date 2017-09-14
	*
	* \return Renvoi "vrai" si l'Etiquette a été créée ou lève une exception en cas d'erreur.
	*/
		$Requete = $this->prepareSQL(
			'INSERT INTO tgs_tags ( tgs_code, tgs_libelle, tgs_description )
			VALUES ( :code, :libelle, :description ) '
		);
			
		$this->bindSQL( $Requete, ':code', $Code, self::CODE_TYPE, self::CODE_LENGTH )
			->bindSQL( $Requete, ':libelle', $Libelle, self::LIBELLE_TYPE, self::LIBELLE_LENGTH )
			->bindSQL( $Requete, ':description', $Description, self::TEXTE_TYPE )
			->executeSQL( $Requete );

		switch( $this->getAttribute(PDO::ATTR_DRIVER_NAME) ) {
			default;
			$this->LastInsertId = $this->lastInsertId();
			break;
			
			case 'pgsql';
			$this->LastInsertId = $this->lastInsertId( 'tgs_tags_tgs_id_seq' );
			break;
		}
			
		return TRUE;
	}


	public function modifierEtiquette( $Id, $Code, $Libelle, $Description='' ) {
	/**
	* Modifie les valeurs d'une Etiquette.
	*
	* \license Loxense
	* \author Pierre-Luc MARY
	* \date 2017-09-14
	*
	* \return Renvoi "vrai" la liste des Etiquettes ou lève une exception en cas d'erreur.
	*/
		$Requete = $this->prepareSQL(
			'UPDATE tgs_tags SET tgs_code = :code, tgs_libelle = :libelle, tgs_description = :description 
			WHERE tgs_id = :id '
		);
			
		$this->bindSQL( $Requete, ':id', $Id, self::ID_TYPE )
			->bindSQL( $Requete, ':code', $Code, self::CODE_TYPE, self::CODE_LENGTH )
			->bindSQL( $Requete, ':libelle', $Libelle, self::LIBELLE_TYPE, self::LIBELLE_LENGTH )
			->bindSQL( $Requete, ':description', $Description, self::TEXTE_TYPE )
			->executeSQL( $Requete );

		return TRUE;
	}


	public function supprimerEtiquette( $Id ) {
	/**
	* Supprimer une Etiquette.
	*
	* \license Loxense
	* \author Pierre-Luc MARY
	* \date 2017-09-14
	*
	* \return Renvoi la liste des Etiquettes ou lève une exception en cas d'erreur.
	*/
		$Requete = $this->prepareSQL(
			'DELETE FROM tgs_tags WHERE tgs_id = :id '
		);
			
		$this->bindSQL( $Requete, ':id', $Id, self::ID_TYPE )
			->executeSQL( $Requete );

		return TRUE;
	}


	public function totalEtiquettes() {
	/**
	* Compte le nombre total d'Etiquettes.
	*
	* \license Loxense
	* \author Pierre-Luc MARY
	* \date 2017-09-19
	*
	* \return Renvoi le nombre total d'étiquettes ou lève une exception en cas d'erreur.
	*/
		$Requete = $this->prepareSQL(
			'SELECT COUNT(tgs_id) AS "total" FROM tgs_tags '
		);
			
		return $this->executeSQL( $Requete )
			->fetchObject()->total;
	}


	public function totalAssociationUtilisateurs( $tgs_id ) {
	/**
	* Compte le nombre total d'association entre cette Etiquette et des Utilisateurs.
	*
	* \license Loxense
	* \author Pierre-Luc MARY
	* \date 2017-09-15
	*
	* \return Renvoi le nombre total d'utisateurs associés ou lève une exception en cas d'erreur.
	*/
		$Requete = $this->prepareSQL(
			'SELECT COUNT(idn_id) AS "total" FROM idtg_idn_tgs WHERE tgs_id = :tgs_id '
		);
			
		return $this->bindSQL( $Requete, ':tgs_id', $tgs_id, self::ID_TYPE )
			->executeSQL( $Requete )
			->fetchObject()->total;
	}


	public function listerEtiquettesAssocieesIdentite( $idn_id ) {
	/**
	* Lister les Etiquettes rattachées à l'Identité.
	*
	* \license Loxense
	* \author Pierre-Luc MARY
	* \date 2017-09-12
	*
	* \param[in] $idn_id ID de l'Identité pour laquelle on cherche les étiquettes.
	*
	* \return Renvoi la liste des Etiquettes ou lève une exception en cas d'erreur.
	*/
		$Requete = $this->prepareSQL(
			'SELECT tgs.tgs_id, tgs.tgs_code, tgs.tgs_libelle, idtg.idn_id
FROM tgs_tags AS "tgs"
LEFT JOIN (SELECT idn_id, tgs_id FROM idtg_idn_tgs AS "idtg" WHERE idn_id = :idn_id) AS "idtg" ON idtg.tgs_id = tgs.tgs_id '
		);

		return $this->bindSQL( $Requete, ':idn_id', $idn_id, self::ID_TYPE )
			->executeSQL( $Requete )
			->fetchAll( PDO::FETCH_CLASS );
	}


	public function listerIdentitesAssocieesEtiquettes( $tgs_id = 0 ) {
	/**
	* Lister les Identités rattachées à une Etiquette
	*
	* \license Loxense
	* \author Pierre-Luc MARY
	* \date 2017-09-19
	*
	* \param[in] $tgs_id ID de l'Etiquette pour laquelle on cherche les identités.
	*
	* \return Renvoi la liste des Identités ou lève une exception en cas d'erreur.
	*/
		if ( $tgs_id == '' ) $tgs_id = 0;

		$Requete = $this->prepareSQL(
			'SELECT idn.idn_id, idn.idn_login, cvl.cvl_prenom, cvl.cvl_nom, idtg.tgs_id
FROM idn_identites AS "idn"
LEFT JOIN cvl_civilites AS "cvl" ON cvl.cvl_id = idn.cvl_id
LEFT JOIN (SELECT idn_id, tgs_id FROM idtg_idn_tgs AS "idtg" WHERE tgs_id = :tgs_id) AS "idtg" ON idtg.idn_id = idn.idn_id '
		);

		return $this->bindSQL( $Requete, ':tgs_id', $tgs_id, self::ID_TYPE )
			->executeSQL( $Requete )
			->fetchAll( PDO::FETCH_CLASS );
	}


	public function ajouterAssociationUtilisateur( $tgs_id, $idn_id ) {
	/**
	* Ajoute une association entre l'Etiquette et l'Utilisateur.
	*
	* \license Loxense
	* \author Pierre-Luc MARY
	* \date 2017-09-15
	*
	* \return Renvoi "vrai" si l'association a été créée ou lève une exception en cas d'erreur.
	*/
		$Requete = $this->prepareSQL(
			'INSERT INTO idtg_idn_tgs ( tgs_id, idn_id )
			VALUES ( :tgs_id, :idn_id ) '
		);
			
		$this->bindSQL( $Requete, ':tgs_id', $tgs_id, self::ID_TYPE )
			->bindSQL( $Requete, ':idn_id', $idn_id, self::ID_TYPE )
			->executeSQL( $Requete );

		return TRUE;
	}


	public function supprimerAssociationUtilisateur( $tgs_id, $idn_id ) {
	/**
	* Supprime l'association entre l'Etiquette et l'Utilisateur.
	*
	* \license Loxense
	* \author Pierre-Luc MARY
	* \date 2017-09-15
	*
	* \return Renvoi "vrai" si l'association a été supprimée ou lève une exception en cas d'erreur.
	*/
		$Requete = $this->prepareSQL(
			'DELETE FROM idtg_idn_tgs WHERE tgs_id = :tgs_id AND idn_id = :idn_id '
		);
			
		$this->bindSQL( $Requete, ':tgs_id', $tgs_id, self::ID_TYPE )
			->bindSQL( $Requete, ':idn_id', $idn_id, self::ID_TYPE )
			->executeSQL( $Requete );

		return TRUE;
	}


	public function etiquetteEstAssociee( $tgs_id ) {
	/**
	* Contrôle les associations entre l'Etiquette et l'Utilisateur.
	*
	* \license Loxense
	* \author Pierre-Luc MARY
	* \date 2017-09-15
	*
	* \return Renvoi un objet de compteur ou lève une exception en cas d'erreur.
	*/
		$Compteurs = new stdClass();

		$Requete = $this->prepareSQL(
			'SELECT COUNT(idn_id) AS "total_idn" FROM idtg_idn_tgs WHERE tgs_id = :tgs_id '
		);
			
		$Compteurs->total_idn = $this->bindSQL( $Requete, ':tgs_id', $tgs_id, self::ID_TYPE )
			->executeSQL( $Requete )
			->fetchObject()->total_idn;


/*		$Requete = $this->prepareSQL(
			'SELECT COUNT(apr_id) AS "total_apr" FROM aptg_apr_tgs WHERE tgs_id = :tgs_id '
		);
			
		$Compteurs->total_apr = $this->bindSQL( $Requete, ':tgs_id', $tgs_id, self::ID_TYPE )
			->executeSQL( $Requete )
			->fetchObject()->total_apr; */

		return $Compteurs;
	}


	public function listerEtiquettesParUtilisateur( $ID = '' ) {
	/**
	* Lister les associations entre un Utilisateur et les Etiquettes.
	*
	* \license Loxense
	* \author Pierre-Luc MARY
	* \date 2017-10-06
	*
	* \param[in] $ID Id de l'Utilisateur à contrôler
	*
	* \return Renvoi une liste d'Etiquettes. Lève une Exception en cas d'erreur.
	*
	*/
		if ( $ID == '' or $ID == NULL ) $InternalID = 'NULL';
		else $InternalID = ':id'; 

		$SQL = 'SELECT idtg.idn_id, tgs.tgs_id, tgs.tgs_code, tgs.tgs_libelle ' .
			'FROM tgs_tags AS tgs ' .
			'LEFT JOIN (SELECT idn_id, tgs_id FROM idtg_idn_tgs WHERE idn_id = ' . $InternalID . ') AS idtg ON tgs.tgs_id = idtg.tgs_id ' .
		     'ORDER BY idtg.idn_id, tgs.tgs_code ';

		$requete = $this->prepareSQL( $SQL );

		if ( $ID != NULL ) $this->bindSQL( $requete, ':id', $ID, self::ID_TYPE );

		$this->executeSQL( $requete );

		return $requete->fetchAll( PDO::FETCH_CLASS );
	}


	public function supprimerEtiquetteAUtilisateur( $idn_id, $tgs_id ) {
	/**
	* Supprime l'association entre un Utilisateur et une Etiquette.
	*
	* \license Loxense
	* \author Pierre-Luc MARY
	* \date 2017-10-06
	*
	* \param[in] $idn_id ID de l'Identité à supprimer
	* \param[in] $tgs_id ID de l'Etiquette à supprimer
	*
	* \return Renvoi vrai si la suppression à réussi ou lève une exception en cas d'erreur
	*/
		$Query = $this->prepareSQL( 'DELETE FROM idtg_idn_tgs ' .
			'WHERE idn_id = :idn_id AND tgs_id = :tgs_id ' );
		
		$this->bindSQL( $Query, ':idn_id', $idn_id, self::ID_TYPE )
			->bindSQL( $Query, ':tgs_id', $tgs_id, self::ID_TYPE )
			->executeSQL( $Query );

		return TRUE;
	}


	public function ajouterEtiquetteAUtilisateur( $idn_id, $tgs_id ) {
	/**
	* Ajoute l'association entre un Utilisateur et une Etiquette.
	*
	* \license Loxense
	* \author Pierre-Luc MARY
	* \date 2017-10-06
	*
	* \param[in] $idn_id ID de l'Identité à ajouter
	* \param[in] $tgs_id ID de l'Etiquette à ajouter
	*
	* \return Renvoi vrai si l'ajout à réussi ou lève une exception en cas d'erreur
	*/
		$Query = $this->prepareSQL( 'INSERT INTO idtg_idn_tgs (idn_id, tgs_id) ' .
			'VALUES (:idn_id, :tgs_id) ' );
		
		$this->bindSQL( $Query, ':idn_id', $idn_id, self::ID_TYPE )
			->bindSQL( $Query, ':tgs_id', $tgs_id, self::ID_TYPE )
			->executeSQL( $Query );

		return TRUE;
	}


	public function listerEtiquettesParUtilisateurSimple( $idn_id = '', $super_admin = '' ) {
	/**
	* Lister les Etiquettes associées à un Utilisateur.
	*
	* \license Loxense
	* \author Pierre-Luc MARY
	* \date 2017-10-06
	*
	* \param[in] $ID Id de l'Utilisateur à contrôler
	*
	* \return Renvoi une liste d'Etiquettes. Lève une Exception en cas d'erreur.
	*
	*/
		$SQL = 'SELECT DISTINCT ON (tgs.tgs_code) tgs.tgs_code, idtg.idn_id, tgs.tgs_id, tgs.tgs_libelle ' .
			'FROM tgs_tags AS tgs ' .
			'LEFT JOIN idtg_idn_tgs AS "idtg" ON idtg.tgs_id = tgs.tgs_id ';

		if ( ! $super_admin ) $SQL .= 'WHERE idtg.idn_id = :idn_id ';

		$SQL .= 'ORDER BY tgs.tgs_code ';

		$requete = $this->prepareSQL( $SQL );

		if ( ! $super_admin ) $this->bindSQL( $requete, ':idn_id', $idn_id, self::ID_TYPE );

		$this->executeSQL( $requete );

		return $requete->fetchAll( PDO::FETCH_CLASS );
	}

}

?>