<?php

include_once( DIR_LIBRAIRIES . '/Class_HBL_Parametres_PDO.inc.php');

class Gestionnaires  extends HBL_Parametres {
/**
* Cette classe gère les équipes de Gestionnaires.
*
* PHP version 5
* @license Loxense
* @author Pierre-Luc MARY
*/

	const PREFIXE = "GST_";

	const ID_TYPE = PDO::PARAM_INT;

	const LIBELLE_TYPE = PDO::PARAM_STR;
	const LIBELLE_LENGTH = 100;

	const LANGUE_TYPE = PDO::PARAM_STR;
	const LANGUE_LENGTH = 2;

	public $LastInsertId;


	public function __construct() {
	/**
	* Connexion à la base de données via IICA_DB_Connector.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @version 1.0
	* @date 2016-10-24
	*
	* @return Renvoi un booléen sur le succès de la connexion à la base de données
	*/
		parent::__construct();
		
		return true;
	}


	public function listerGestionnaires( $Trier = 'libelle', $Recherche = '' ) {
	/**
	* Lister les gestionnaires.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-01-18
	*
	* @param[in] $Trier Permet de définir un ordre d'affichage.
	* @param[in] $Recherche Permet de réaliser une recherche particulière.
	*
	* @return Renvoi une liste de mances génériques ou une liste vide. Lève une Exception en cas d'erreur.
	*/
		$Sql = 'SELECT gst.gst_id, gst.gst_libelle, ' .
			'COUNT(DISTINCT gsts.tsp_id) AS total_tsp, ' .
			'COUNT(DISTINCT idgs.idn_id) AS total_idn '. 
			'FROM gst_gestionnaires AS "gst" ' .
			'LEFT JOIN gsts_gst_tsp AS "gsts" ON gsts.gst_id = gst.gst_id ' .
			'LEFT JOIN idgs_idn_gst AS "idgs" ON idgs.gst_id = gst.gst_id ' ;

		if ( $Recherche != '' ) {
			$Where = 'WHERE LOWER(gst_libelle) LIKE :Libelle ';
		} else {
			$Where = '';
		}

		switch ( $Trier ) {
		 default:
		 case 'libelle':
			$Order = 'ORDER BY gst_libelle ';
			break;

		 case 'libelle-desc':
			$Order = 'ORDER BY gst_libelle DESC ';
			break;

		 case 'total_idn':
			$Order = 'ORDER BY total_idn ';
			break;

		 case 'total_idn-desc':
			$Order = 'ORDER BY total_idn DESC ';
			break;

		 case 'total_tsp':
			$Order = 'ORDER BY total_tsp ';
			break;

		 case 'total_tsp-desc':
			$Order = 'ORDER BY total_tsp DESC ';
			break;
		}

		$Sql .= $Where . 'GROUP BY gst.gst_id, gst_libelle ' . $Order;

		$Requete = $this->prepareSQL( $Sql );

		if ( $Recherche != '') {
			$this->bindSQL( $Requete, ':Libelle', '%' . strtolower($Recherche) . '%', self::LIBELLE_TYPE, self::LIBELLE_LENGTH );
		}

		$this->executeSQL( $Requete );

		return $Requete->fetchAll( PDO::FETCH_CLASS );
	}


	public function ajouterGestionnaire( $Libelle ) {
	/**
	* Ajouter une nouvelle équipe de Gestionnaire
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-01-18
	*
	* @param[in] $Libelle le libellé de la nouvelle équipe de Gestionnaires
	*
	* @return Renvoi vrai si la nouvelle équipe de Gestionnaires a été créée. Sinon, lève une exception.
	*			
	*/
		$Requete = 'INSERT INTO gst_gestionnaires ( gst_libelle ) VALUES ( :libelle )';

		$requete = $this->prepareSQL( $Requete );

		$this->bindSQL( $requete, ':libelle', $Libelle, self::LIBELLE_TYPE, self::LIBELLE_LENGTH )
			->executeSQL( $requete );


		switch( $this->getAttribute(PDO::ATTR_DRIVER_NAME) ) {
		 	default;
				$ID = $this->lastInsertId();
				break;

			case 'pgsql';
				$ID = $this->lastInsertId( 'gst_gestionnaires_gst_id_seq' );
				break;
		}


		return $ID;
	}


	public function supprimerGestionnaire( $Id ) {
	/**
	* Supprimer une équipe de Gestionnaires.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-01-18
	*
	* @param[in] $Id ID de l'équipe de Gestionnaires à supprimer
	*
	* @return Renvoi vrai si l'équipe de Gestionnaires a été supprimée. Sinon, lève une exception.
	*			
	*/
		$Requete = 'DELETE FROM gst_gestionnaires WHERE gst_id = :id ';

		$requete = $this->prepareSQL( $Requete );

		$this->bindSQL( $requete, ':id', $Id, self::ID_TYPE )
			->executeSQL( $requete );

		return TRUE;
	}


	public function isAssociatedGestionnaire( $Id ) {
	/**
	* Vérifie si l'équipe de Gestionnaires est associée à des Types de Support et des Identités.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-01-18
	*
	* @param[in] $Id l'Id de la Mesure Générique à vérifier
	*
	* @return Renvoi une occurrence contenant le compteurs. Sinon, lève une exception.
	*			
	*/
		$Request = 'SELECT ' .
			'COUNT(gsts.tsp_id) AS total_tsp ' .
			'FROM gst_gestionnaires AS "gst" ' .
			'LEFT JOIN gsts_gst_tsp AS "gsts" ON gsts.gst_id = gst.gst_id ' .
			'WHERE gst.gst_id = :id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':id', $Id, self::ID_TYPE );

		$this->executeSQL( $Query );

		if ( $this->RowCount == 0 ) {
			return FALSE;
		}

		$Result = $Query->fetchObject();

		$Request = 'SELECT ' .
			'COUNT(idgs.idn_id) AS total_idn '.
			'FROM gst_gestionnaires AS "gst" ' .
			'LEFT JOIN idgs_idn_gst AS "idgs" ON idgs.gst_id = gst.gst_id ' .
			'WHERE gst.gst_id = :id ';
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':id', $Id, self::ID_TYPE );
		
		$this->executeSQL( $Query );
		
		if ( $this->RowCount == 0 ) {
			return FALSE;
		}
		
		$_Result = $Query->fetchObject();
		$Result->total_idn = $_Result->total_idn;
		
		return $Result;
	}


	public function recupererGestionnaire( $Id ) {
	/**
	* Récupérer une équipe de Gestionnaire.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-01-18
	*
	* @param[in] $Id Id de l'équipe de Gestionnaire à récupérer
	*
	* @return Renvoi une Mesure Générique ou une liste vide. Lève une Exception en cas d'erreur.
	*/
		$Sql = 'SELECT gst.gst_id, gst.gst_libelle, ' .
			'COUNT(DISTINCT gsts.tsp_id) AS total_tsp, ' .
			'COUNT(DISTINCT idgs.idn_id) AS total_idn '. 
			'FROM gst_gestionnaires AS "gst" ' .
			'LEFT JOIN gsts_gst_tsp AS "gsts" ON gsts.gst_id = gst.gst_id ' .
			'LEFT JOIN idgs_idn_gst AS "idgs" ON idgs.gst_id = gst.gst_id ' .
			'WHERE gst.gst_id = :id ' .
			'GROUP BY gst.gst_id, gst_libelle ';

		$Requete = $this->prepareSQL( $Sql );

		$this->bindSQL( $Requete, ':id', $Id, self::ID_TYPE );

		$this->executeSQL( $Requete );

		return $Requete->fetchObject();
	}


	public function modifierGestionnaire( $Id, $Libelle ) {
	/**
	* Modifier une équipe de Gestionnaire
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-01-18
	*
	* @param[in] $Id l'ID de l'équipe de Gestionnaire à modifier
	* @param[in] $Libelle le libelle à prendre en compte
	*
	* @return Renvoi vrai si l'équipe de Gestionnaire a été modifiée. Lève une Exception en cas d'erreur.
	*			
	*/
		$SQL = 'UPDATE gst_gestionnaires '.
			'SET gst_libelle = :libelle '.
			'WHERE gst_id = :id ';

		$requete = $this->prepareSQL( $SQL );
			
		$this->bindSQL( $requete, ':libelle', $Libelle, self::LIBELLE_TYPE, self::LIBELLE_LENGTH )
			->bindSQL( $requete, ':id', $Id, self::ID_TYPE )
			->executeSQL( $requete );

		return TRUE;
	}


	public function modifierChampGestionnaire( $Id, $Source, $Valeur ) {
	/**
	* Modifier le champ d'une équipe de Gestionnaire
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-01-21
	*
	* @param[in] $Id l'ID de l'équipe de Gestionnaire à modifier
	* @param[in] $Source Nom du champ à modifier
	* @param[in] $Valeur Valeur à affecter au champ à modifier
	*
	* @return Renvoi vrai si l'équipe de Gestionnaire a été modifiée. Lève une Exception en cas d'erreur.
	*			
	*/
		$SQL = 'UPDATE gst_gestionnaires '.
			'SET ' . $Source . ' = :valeur '.
			'WHERE gst_id = :id ';
//print('<hr>'.$SQL.'<hr>');
		$requete = $this->prepareSQL( $SQL );

		switch( $Source ) {
		 default:
		 case 'gst_libelle':
			$this->bindSQL( $requete, ':valeur', $Valeur, self::LIBELLE_TYPE, self::LIBELLE_LENGTH );

			break;
		}

		$this->bindSQL( $requete, ':id', $Id, self::ID_TYPE )
			->executeSQL( $requete );

		return TRUE;
	}


	public function listerTypesSupportsParGestionnaire( $ID, $Toutes = TRUE, $Langue = 'fr' ) {
	/**
	* Lister les Types de Support qui sont associées à cette Equipe de Gestionnaires.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-01-19
	*
	* @param[in] $ID Id de l'équipe de Gestionnaires à contrôler
	* @param[in] $Toutes Si ce flag est à TRUE, on liste toutes les actions, sinon seulement celles associées
	* @param[in] $Langue Langue dans laquelle on remonte les libellés
	*
	* @return Renvoi une liste Types de Type de Support. Lève une Exception en cas d'erreur.
	*
	*/

		if ( $ID != '' ) {
			$SQL = 'SELECT gsts.gst_id, tsp.tsp_id, ' .
				'tsp.tsp_code, rlb1.lbr_libelle AS "tsp_libelle" ' .
				'FROM tsp_types_support AS tsp ' .
				'LEFT JOIN lbr_libelles_referentiel AS rlb1 ON tsp.tsp_code = rlb1.lbr_code AND rlb1.lng_id = :langue ' .
				'LEFT JOIN (SELECT tsp_id, gst_id FROM gsts_gst_tsp WHERE gst_id = :id) AS gsts ON tsp.tsp_id = gsts.tsp_id ';
		} else {
			$SQL = 'SELECT tsp.tsp_id, ' .
				'tsp.tsp_code, rlb1.lbr_libelle AS "tsp_libelle" ' .
				'FROM tsp_types_support AS tsp ' .
				'LEFT JOIN lbr_libelles_referentiel AS rlb1 ON tsp.tsp_code = rlb1.lbr_code AND rlb1.lng_id = :langue ';
		}


		if ( $Toutes != TRUE ) {
			$SQL .= 'WHERE gsts.gts_id = :id ';
		}

		$SQL .= 'ORDER BY rlb1.lbr_libelle ';

		$requete = $this->prepareSQL( $SQL );

		if ( $ID != '' ) {
			$this->bindSQL( $requete, ':id', $ID, self::ID_TYPE );
		}

		$this->bindSQL( $requete, ':langue', $Langue, self::LANGUE_TYPE, self::LANGUE_LENGTH )
			->executeSQL( $requete );

		return $requete->fetchAll( PDO::FETCH_CLASS );
	}


	public function listerUtilisateursParGestionnaire( $ID, $Toutes = TRUE ) {
	/**
	* Lister les Utilisateurs qui sont associés à cette Equipe de Gestionnaires.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-01-20
	*
	* @param[in] $ID Id de l'Equipe de Gestionnaires
	* @param[in] $Toutes Si ce flag est à TRUE, on liste tous les Utilisateurs, sinon seulement ceux associées (par défaut Toutes)
	*
	* @return Renvoi une liste des Utilisateurs. Lève une Exception en cas d'erreur.
	*
	*/

		if ( $ID != '' ) {
			$SQL = 'SELECT idn.idn_id, idgs.gst_id, idn_login, cvl_prenom, cvl_nom
				FROM idn_identites AS idn
				LEFT JOIN cvl_civilites AS cvl ON cvl.cvl_id = idn.cvl_id
				LEFT JOIN (SELECT gst_id, idn_id FROM idgs_idn_gst WHERE gst_id = :id) AS idgs ON idn.idn_id = idgs.idn_id ';
		} else {
			$SQL = 'SELECT idn.idn_id, idn_login, cvl_prenom, cvl_nom
				FROM idn_identites AS idn
				LEFT JOIN cvl_civilites AS cvl ON cvl.cvl_id = idn.cvl_id ';

			$Toutes = TRUE;
		}

		if ( $Toutes != TRUE ) {
			$SQL .= 'WHERE idgs.gst_id = :id ';
		}

		if ( $ID != '' ) $SQL .= 'ORDER BY idgs.idn_id, idn_login, cvl_prenom, cvl_nom ';
		else $SQL .= 'ORDER BY idn_login, cvl_prenom, cvl_nom ';


		$requete = $this->prepareSQL( $SQL );

		if ( $ID != '' ) $this->bindSQL( $requete, ':id', $ID, self::ID_TYPE );

		$this->executeSQL( $requete );

		return $requete->fetchAll( PDO::FETCH_CLASS );
	}


	public function totalGestionnaires() {
	/**
	* Récupère le nombre total d'équipes de Gestionnaires.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-01-18
	*
	* @return Renvoi le nombre total d'équipes de Gestionnaires
	*/
		$Query = $this->prepareSQL( 'SELECT ' .
		 'count(*) as total ' .
		 'FROM gst_gestionnaires ' );
		
		$this->executeSQL( $Query );
		
		$Occurrence = $Query->fetchObject();

		return $Occurrence->total;
	}


	public function ajouterTypeActifSupportAGestionnaire( $tsp_id, $gst_id ) {
	/**
	* Ajoute une association entre un Type d'Actif Support et une Equipe de Gestionnaires.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-01-20
	*
	* @param[in] $tsp_id ID du Type Actif de Support à associer
	* @param[in] $gst_id ID du Gestionnaire à associer
	*
	* @return Renvoi vrai si l'ajout à réussi ou lève une exception en cas d'erreur
	*/
		$Query = $this->prepareSQL( 'INSERT INTO gsts_gst_tsp ' .
		 '(tsp_id, gst_id) ' .
		 'VALUES ' .
		 '(:tsp_id, :gst_id) ');
		
		$this->bindSQL( $Query, ':tsp_id', $tsp_id, self::ID_TYPE )
			->bindSQL( $Query, ':gst_id', $gst_id, self::ID_TYPE )
			->executeSQL( $Query );

		return TRUE;
	}


	public function ajouterUtilisateurAGestionnaire( $idn_id, $gst_id ) {
	/**
	* Ajoute une association entre un Utilisateur et une Equipe de Gestionnaires.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-01-20
	*
	* @param[in] $idn_id ID de l'Identité à associer
	* @param[in] $gst_id ID du Gestionnaire à associer
	*
	* @return Renvoi vrai si l'ajout à réussi ou lève une exception en cas d'erreur
	*/
		$Query = $this->prepareSQL( 'INSERT INTO idgs_idn_gst ' .
		 '(idn_id, gst_id) ' .
		 'VALUES ' .
		 '(:idn_id, :gst_id) ');
		
		$this->bindSQL( $Query, ':idn_id', $idn_id, self::ID_TYPE )
			->bindSQL( $Query, ':gst_id', $gst_id, self::ID_TYPE )
			->executeSQL( $Query );

		return TRUE;
	}


	public function supprimerTypeActifSupportAGestionnaire( $tsp_id, $gst_id ) {
	/**
	* Supprime l'association entre un Type d'Actif Support et une Equipe de Gestionnaires.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-01-20
	*
	* @param[in] $tsp_id ID du Type Actif de Support à associer
	* @param[in] $gst_id ID du Gestionnaire à associer
	*
	* @return Renvoi vrai si l'ajout à réussi ou lève une exception en cas d'erreur
	*/
		$Query = $this->prepareSQL( 'DELETE FROM gsts_gst_tsp ' .
			'WHERE tsp_id = :tsp_id AND gst_id = :gst_id ' );
		
		$this->bindSQL( $Query, ':tsp_id', $tsp_id, self::ID_TYPE )
			->bindSQL( $Query, ':gst_id', $gst_id, self::ID_TYPE )
			->executeSQL( $Query );

		return TRUE;
	}


	public function supprimerUtilisateurAGestionnaire( $idn_id, $gst_id ) {
	/**
	* Supprime l'association entre un Utilisateur et une Equipe de Gestionnaires.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-01-20
	*
	* @param[in] $idn_id ID de l'Identité à associer
	* @param[in] $gst_id ID du Gestionnaire à associer
	*
	* @return Renvoi vrai si l'ajout à réussi ou lève une exception en cas d'erreur
	*/
		$Query = $this->prepareSQL( 'DELETE FROM idgs_idn_gst ' .
			'WHERE idn_id = :idn_id AND gst_id = :gst_id ' );
		
		$this->bindSQL( $Query, ':idn_id', $idn_id, self::ID_TYPE )
			->bindSQL( $Query, ':gst_id', $gst_id, self::ID_TYPE )
			->executeSQL( $Query );

		return TRUE;
	}


	public function listerGestionnairesParTypeActifSupport( $ID ) {
	/**
	* Lister les associations entre un Type de Support et les Equipes de Gestionnaires.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-01-21
	*
	* @param[in] $ID Id de l'équipe de Gestionnaires à contrôler
	*
	* @return Renvoi une liste de Gestionnaires. Lève une Exception en cas d'erreur.
	*
	*/

		if ( $ID != '' ) {
			$SQL = 'SELECT gsts.tsp_id, gst.gst_id, gst.gst_libelle ' .
				'FROM gst_gestionnaires AS gst ' .
				'LEFT JOIN (SELECT tsp_id, gst_id FROM gsts_gst_tsp WHERE tsp_id = :id) AS gsts ON gst.gst_id = gsts.gst_id ';
		} else {
			$SQL = 'SELECT gst.gst_id, gst.gst_libelle ' .
				'FROM gst_gestionnaires AS gst ';
		}

		$requete = $this->prepareSQL( $SQL );

		if ( $ID != '' ) {
			$this->bindSQL( $requete, ':id', $ID, self::ID_TYPE );
		}

		$this->executeSQL( $requete );

		return $requete->fetchAll( PDO::FETCH_CLASS );
	}


	public function ajouterGestionnaireATypeActifSupport( $Id, $Gestionnaire ) {
	/**
	* Ajoute une association entre un Type de Support et une Equipe de Gestionnaires.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-01-21
	*
	* @param[in] $ID Id du Type d'Actif Support à associer
	* @param[in] $Gestionnaire Id de l'Equipe de Gestionnaires à associer
	*
	* @return Renvoi une liste de Gestionnaires. Lève une Exception en cas d'erreur.
	*
	*/

		$SQL = 'INSERT INTO gsts_gst_tsp ' .
			'( gst_id, tsp_id ) ' .
			'VALUES ' .
			'( :gst_id, :tsp_id ) ';

		$requete = $this->prepareSQL( $SQL );

		$this->bindSQL( $requete, ':gst_id', $Gestionnaire, self::ID_TYPE )
			->bindSQL( $requete, ':tsp_id', $Id, self::ID_TYPE )
			->executeSQL( $requete );
	}


	public function supprimerGestionnaireATypeActifSupport( $Id, $Gestionnaire ) {
	/**
	* Supprime une association entre un Type de Support et une Equipe de Gestionnaires.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-01-21
	*
	* @param[in] $ID Id du Type d'Actif Support à associer
	* @param[in] $Gestionnaire Id de l'Equipe de Gestionnaires à associer
	*
	* @return Renvoi une liste de Gestionnaires. Lève une Exception en cas d'erreur.
	*
	*/

		$SQL = 'DELETE FROM gsts_gst_tsp ' .
			'WHERE gst_id = :gst_id AND tsp_id = :tsp_id ';

		$requete = $this->prepareSQL( $SQL );

		$this->bindSQL( $requete, ':gst_id', $Gestionnaire, self::ID_TYPE )
			->bindSQL( $requete, ':tsp_id', $Id, self::ID_TYPE )
			->executeSQL( $requete );
	}


	public function compterGestionnairesParTypeActifSupport( $Id ) {
	/**
	* Compte le nombre d'association entre un Type de Support et ses Equipes de Gestionnaires.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-01-21
	*
	* @param[in] $ID Id du Type d'Actif Support à associer
	*
	* @return Renvoi le nombre d'association. Lève une Exception en cas d'erreur.
	*
	*/

		$SQL = 'SELECT COUNT(*) AS total_gst ' .
			'FROM gsts_gst_tsp ' .
			'WHERE tsp_id = :id ';

		$requete = $this->prepareSQL( $SQL );

		$this->bindSQL( $requete, ':id', $Id, self::ID_TYPE )
			->executeSQL( $requete );

		return $requete->fetchObject()->total_gst;
	}


	public function listerGestionnairesParUtilisateur( $ID = '' ) {
	/**
	* Lister les associations entre un Utilisateur et les Equipes de Gestionnaires.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-01-22
	*
	* @param[in] $ID Id de l'équipe de Gestionnaires à contrôler
	*
	* @return Renvoi une liste de Gestionnaires. Lève une Exception en cas d'erreur.
	*
	*/
		if ( $ID == '' or $ID == NULL ) $InternalID = 'NULL';
		else $InternalID = ':id'; 

		$SQL = 'SELECT idgs.idn_id, gst.gst_id, gst.gst_libelle ' .
			'FROM gst_gestionnaires AS gst ' .
			'LEFT JOIN (SELECT idn_id, gst_id FROM idgs_idn_gst WHERE idn_id = ' . $InternalID . ') AS idgs ON gst.gst_id = idgs.gst_id ';

		$requete = $this->prepareSQL( $SQL );

		if ( $ID != NULL ) $this->bindSQL( $requete, ':id', $ID, self::ID_TYPE );

		$this->executeSQL( $requete );

		return $requete->fetchAll( PDO::FETCH_CLASS );
	}


	public function ajouterGestionnaireAUtilisateur( $Id, $Gestionnaire ) {
	/**
	* Ajoute une association entre un Utilisateur et une Equipe de Gestionnaires.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-01-22
	*
	* @param[in] $ID Id de l'Utilisateur à associer
	* @param[in] $Gestionnaire Id de l'Equipe de Gestionnaires à associer
	*
	* @return Renvoi une liste de Gestionnaires. Lève une Exception en cas d'erreur.
	*
	*/

		$SQL = 'INSERT INTO idgs_idn_gst ' .
			'( gst_id, idn_id ) ' .
			'VALUES ' .
			'( :gst_id, :idn_id ) ';

		$requete = $this->prepareSQL( $SQL );

		$this->bindSQL( $requete, ':gst_id', $Gestionnaire, self::ID_TYPE )
			->bindSQL( $requete, ':idn_id', $Id, self::ID_TYPE )
			->executeSQL( $requete );
	}


	public function supprimerGestionnaireAUtilisateur( $Id, $Gestionnaire ) {
	/**
	* Supprime une association entre un Utilisateur et une Equipe de Gestionnaires.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-01-22
	*
	* @param[in] $ID Id de l'Utilisateur à associer
	* @param[in] $Gestionnaire Id de l'Equipe de Gestionnaires à associer
	*
	* @return Renvoi une liste de Gestionnaires. Lève une Exception en cas d'erreur.
	*
	*/

		$SQL = 'DELETE FROM idgs_idn_gst ' .
			'WHERE gst_id = :gst_id AND idn_id = :idn_id ';

		$requete = $this->prepareSQL( $SQL );

		$this->bindSQL( $requete, ':gst_id', $Gestionnaire, self::ID_TYPE )
			->bindSQL( $requete, ':idn_id', $Id, self::ID_TYPE )
			->executeSQL( $requete );
	}


	public function compterGestionnairesParUtilisateur( $Id ) {
	/**
	* Compte le nombre d'association entre un Type de Support et ses Equipes de Gestionnaires.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-01-21
	*
	* @param[in] $ID Id de l'Utilisateur à utiliser
	*
	* @return Renvoi le nombre d'association. Lève une Exception en cas d'erreur.
	*
	*/

		$SQL = 'SELECT COUNT(*) AS total_gst ' .
			'FROM idgs_idn_gst ' .
			'WHERE idn_id = :id ';

		$requete = $this->prepareSQL( $SQL );

		$this->bindSQL( $requete, ':id', $Id, self::ID_TYPE )
			->executeSQL( $requete );

		return $requete->fetchObject()->total_gst;
	}

} // Fin class MesuresGeneriques

?>