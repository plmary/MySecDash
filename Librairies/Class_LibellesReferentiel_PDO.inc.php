<?php

include_once HBL_DIR_LIBRARIES . '/Class_HBL_Securite.inc.php';
include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Connecteur_BD_PDO.inc.php' );


class LibellesReferentiel extends HBL_Connexioneur_BD {
/**
* Cette classe gère les Libellés du Référentiel.
*
* \license Loxense
* \author Pierre-Luc MARY
* \date 2025-01-02
*/

	const LBR_CODE_T = PDO::PARAM_STR;
	const LBR_CODE_L = 45;

	const LNG_ID_T = PDO::PARAM_STR;
	const LNG_ID_L = 2;

	const LBR_LIBELLE_T = PDO::PARAM_LOB;


	protected $ListeLangues;
	protected $NombreLangues;


	public function __construct() {
	/**
	* Connexion à la base de données via IICA_DB_Connector.
	*
	* \license Loxense
	* \author Pierre-Luc MARY
	*
	* \return Renvoi un booléen sur le succès de la connexion à la base de données
	*/
		parent::__construct();

		$Requete = $this->prepareSQL( 'SELECT lng_id FROM lng_langages ' );

		$this->executeSQL( $Requete);

		$this->ListeLangues = $Requete->fetchAll( PDO::FETCH_CLASS );
		$this->NombreLangues = count( $this->ListeLangues );

		return true;
	}


	/* =============================================================================== */


	public function ajouterLibellesReferentiel( $Code, $Libelles ) {
	/**
	* Ajouter un nouveau code et ses libellés dans les différentes langues
	*
	* \license Loxense
	* \author Pierre-Luc MARY
	* \version 1.0
	* \date 2025-02-20
	*
	* \param[in] $Code Code libellé à ajouter
	* \param[in] $Libelles Tableau contenant des paires (array(langue => libellé) à ajouter
	*
	* \description 
	*
	* \return Renvoi le tableau des identifiants des libellés créés (attention, la langue "fr" est la seule obligatoire), sinon lève une Exception
	*/
		$Code = mb_strtoupper( trim( $Code ) );

		$_flag = false;

		foreach( $Libelles as $Libelle ) {
			$Libelle[0] = mb_strtolower( trim( $Libelle[0] ) );
			if ($Libelle[0] == 'fr') {
				$_flag = true;
			}
		}

		if ( $_flag == false ) {
			throw new Exception('__LRI_ERR_MANQUE_LANGUE_OBLIGATOIRE');
		}

		$Resultat = [];
		$_ID = '';

		foreach( $Libelles as $Libelle ) {
			$Requete = $this->prepareSQL(
				'INSERT INTO lbr_libelles_referentiel
				(lbr_code, lng_id, lbr_libelle)
				VALUES
				(:lbr_code, :lng_id, :lbr_libelle)'
				);

			$this->bindSQL($Requete, ':lbr_code', $Code, self::LBR_CODE_T, self::LBR_CODE_L);
			$this->bindSQL($Requete, ':lng_id', $Libelle[0], self::LNG_ID_T, self::LNG_ID_L);
			$this->bindSQL($Requete, ':lbr_libelle', $Libelle[1], self::LBR_LIBELLE_T);

			$this->executeSQL($Requete);

			if ( $this->RowCount > 0 ) {
				switch( $this->getAttribute(PDO::ATTR_DRIVER_NAME) ) {
					default;
					$_ID = $this->lastInsertId();
					break;
					
					case 'pgsql';
					$_ID = $this->lastInsertId( 'lbr_libelles_referentiel_lbr_id_seq' );
					break;
				}

				$Resultat[$_ID] = array( $Code, $Libelle[0], $Libelle[1] );
			}
		}

		return $Resultat;
	}



	public function modifierLibellesReferentiel( $Code, $Libelles ) {
	/**
	 * Met à jour (ajoute ou modifie) les Libellés associés à un code du Référentiel
	 *
	 * \license Loxense
	 * \author Pierre-Luc MARY
	 * \date 2025-02-20
	 *
	 * \param[in] $Code Code à rechercher dans la base des libellés
	 * \param[in] $Libelles Les libellés dans les différentes langues à modifier
	 *
	 * \return Renvoi vrai si le libellé a été modifié, sinon lève une Exception
	 */
		$Code = mb_strtoupper( trim( $Code ) );

		foreach ( $Libelles as $Langue => $Libelle ) {
			$Requete = $this->prepareSQL( 'SELECT lbr_id FROM lbr_libelles_referentiel
				WHERE lbr_code = :Code AND lng_id = :Langue ');
			
			$Langue = mb_strtolower( trim( $Langue ) );
			$Libelle = trim( $Libelle );
			
			$this->bindSQL($Requete, ':Code', $Code, self::LBR_CODE_T, self::LBR_CODE_L);
			$this->bindSQL($Requete, ':Langue', $Langue, self::LNG_ID_T, self::LNG_ID_L);
			
			$this->executeSQL($Requete);
			$Resultat = $Requete->fetchObject();

			if ( $Resultat == false ) {
				$Requete = $this->prepareSQL( 'INSERT INTO lbr_libelles_referentiel
					(lbr_code, lng_id, lbr_libelle) VALUES
					(:Code, :Langue, :Libelle ');
			} else {
				$Requete = $this->prepareSQL( 'UPDATE lbr_libelles_referentiel
					SET lbr_libelle = :Libelle
					WHERE lbr_code = :Code AND lng_id = :Langue ');
			}

			$Langue = mb_strtolower( trim( $Langue ) );
			$Libelle = trim( $Libelle );

			$this->bindSQL($Requete, ':Code', $Code, self::LBR_CODE_T, self::LBR_CODE_L);
			$this->bindSQL($Requete, ':Langue', $Langue, self::LNG_ID_T, self::LNG_ID_L);
			$this->bindSQL($Requete, ':Libelle', $Libelle, self::LBR_LIBELLE_T);

			$this->executeSQL($Requete);
		}

		return true;
	}



	public function getLibelle( $Code, $Langue = 'X', $TypeRecherche = 'E' ) {
		/**
		 * Récupérer le libellé du référentiel à partir du code fourni
		 *
		 * \license Loxense
		 * \author Pierre-Luc MARY
		 *
		 * \param[in] $Code le ou les codes du libellé du référenciel à rechercher
		 * \param[in] $Langue la langue du libellé qui est recherhée (si $Langue = *, alors toutes les langues sont recherchées)
		 * \param[in] $TypeRecherche Précise le type de recherche à réaliser ('E' = Egal à, 'D' = Débute par, 'T' = Termine par, 'C' = Contient)
		 *
		 * \return Renvoi les libellés trouvés ou une liste vide si aucune correspondance. Lève une Exception en cas d'erreur.
		 */
		if ( $Code == '' ) {
			return '';
		}

		if ( $Langue == 'X' ) {
			$Langue = $_SESSION['Language'];
		}

		$Langue = mb_strtolower( trim( $Langue ) );
		$TypeRecherche = mb_strtoupper( trim( $TypeRecherche ) );

		$Code = mb_strtoupper( trim( $Code ) );


		if ( $TypeRecherche == 'E' ) {
			$OperateurRecherche = '=';
		} else {
			$OperateurRecherche = 'LIKE';
		}


		$Recherche = 'SELECT lbr_libelle FROM lbr_libelles_referentiel AS "lbr"
			WHERE lbr_code ' . $OperateurRecherche . ' :Code  AND lng_id = :Langue ';


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

		$this->bindSQL( $Requete, ':Code', $Code, self::LBR_CODE_T, self::LBR_CODE_L );
		$this->bindSQL( $Requete, ':Langue', $Langue, self::LNG_ID_T, self::LNG_ID_L );

		$this->executeSQL( $Requete);

		$Resultat = $Requete->fetch(PDO::FETCH_NUM);

		if ( $Resultat != false ) {
			return $Resultat[0];
		} else {
			return '---*---';
		}
	}



	public function recupererLibellesReferentiel( $Codes, $Langue = '*', $TypeRecherche = 'E' ) {
	/**
	* Récupérer les libellés du référentiel à partir d'un ou des codes fournis
	*
	* \license Loxense
	* \author Pierre-Luc MARY
	*
	* \param[in] $Codes le ou les codes du libellé du référenciel à rechercher
	* \param[in] $Langue la langue du libellé qui est recherhée (si $Langue = *, alors toutes les langues sont recherchées)
	* \param[in] $TypeRecherche Précise le type de recherche à réaliser ('E' = Egal à, 'D' = Débute par, 'T' = Termine par, 'C' = Contient)
	*
	* \return Renvoi les libellés trouvés ou une liste vide si aucune correspondance. Lève une Exception en cas d'erreur.
	*/
		if ( $Codes == '' ) {
			return '';
		}

		if ( is_array( $Codes ) ) {
			sort( $Codes );
		} else {
			$Codes = array( $Codes );
		}

		$Resultat = [];

		$Langue = mb_strtolower( trim( $Langue ) );
		$TypeRecherche = mb_strtoupper( trim( $TypeRecherche ) );


		foreach ( $Codes as $Code ) {
			$Code = mb_strtoupper( trim( $Code ) );

			if ( $TypeRecherche == 'E' ) {
				$OperateurRecherche = '=';
			} else {
				$OperateurRecherche = 'LIKE';
			}

			$Recherche = 'SELECT lbr_id, lbr_code, lng_id, lbr_libelle
				FROM lbr_libelles_referentiel AS "lbr" ';

			if ( $Code != '*' ) {
				$Where = 'WHERE lbr_code ' . $OperateurRecherche . ' :Code ';
			} else {
				$Where = '';
			}

			if ( $Langue != '*' ) {
				if ( $Where == '' ) {
					$Where .= 'WHERE ';
				} else {
					$Where .= 'AND ';
				}

				$Where .= 'lng_id = :Langue ';
			}

			$Recherche .= $Where . 'ORDER BY lbr_code, lng_id ';

			$Requete = $this->prepareSQL( $Recherche );


			if ( $Code != '*' ) {
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

				$this->bindSQL( $Requete, ':Code', $Code, self::LBR_CODE_T, self::LBR_CODE_L );
			}

			if ( $Langue != '*' ) {
				$this->bindSQL( $Requete, ':Langue', $Langue, self::LNG_ID_T, self::LNG_ID_L );
			}

			$this->executeSQL( $Requete);

			$Resultat = $Requete->fetchAll( PDO::FETCH_CLASS ); /* as $Occurrence ) {
				$Resultat[] = $Occurrence; //->lbr_code ][ $Occurrence->lng_id ]= $Occurrence->lbr_libelle;
			}*/
		}

		return $Resultat;
	}



	public function majLibelleReferentielParChamp( $lbr_id, $Champ, $Valeur ) {
		/**
		 * Actualise un champ d'un libellé du référentiel.
		 *
		 * \license Loxense
		 * \author Pierre-Luc MARY
		 *
		 * \param[in] $app_id Identifiant de l'application
		 * \param[in] $Champ Nom du champ de l'application à modifier
		 * \param[in] $Valeur Valeur du champ de l'application à prendre en compte
		 *
		 * \return Renvoi un booléen sur le succès de la création ou la modification de l'application
		 */

		$Request = 'UPDATE lbr_libelles_referentiel SET ';

		switch ( $Champ ) {
			case 'lbr_code':
				$Request .= 'lbr_code = :Valeur ';
				break;

			case 'lbr_libelle':
				$Request .= 'lbr_libelle = :Valeur ';
				break;

			case 'lng_id':
				$Request .= 'lng_id = :Valeur ';
				break;

			default:
				return false;
		}

		$Request .= 'WHERE lbr_id = :lbr_id';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':lbr_id', $lbr_id, PDO::PARAM_INT );


		switch ( $Champ ) {
			case 'lbr_code':
				$this->bindSQL( $Query, ':Valeur', $Valeur, self::LBR_CODE_T, self::LBR_CODE_L );
				break;

			case 'lbr_libelle':
				$this->bindSQL( $Query, ':Valeur', $Valeur, self::LBR_LIBELLE_T );
				break;

			case 'lng_id':
				$this->bindSQL( $Query, ':Valeur', $Valeur, self::LNG_ID_T, self::LNG_ID_L );
				break;

			default:
				return false;
		}
		
		
		$this->executeSQL( $Query );
		
		if ( $this->RowCount == 0 ) {
			return false;
		}
		
		
		return true;
	}



	public function supprimerLibelleReferentielParCode( $Code, $Langue = '*', $TypeRecherche = 'E' ) {
	/**
	* Supprimer le ou les libellés rattaché à un code du référentiel
	*
	* \license Loxense
	* \author Pierre-Luc MARY
	*
	* \param[in] $Code le code du libellé du référenciel à supprimer
	* \param[in] $Langue la langue du libellé qui est supprimée (si $Langue = '*', alors toutes les langues sont supprimées)
	* \param[in] $TypeRecherche Précise le type de recherche à réaliser ('E' = Egal à, 'D' = Débute par, 'T' = Termine par, 'C' = Contient)
	*
	* \return Renvoi vrai si les libellés ont été supprimés, sinon lève une Exception
	*/
		if ( $TypeRecherche == 'E' ) {
			$OperateurRecherche = '=';
		} else {
			$OperateurRecherche = 'LIKE';
		}
		
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

		$this->bindSQL( $Requete, ':code', $Code, self::LBR_CODE_T, self::LBR_CODE_L );

		if ( $Langue != '*' ) {
			$this->bindSQL( $Requete, ':langue', $Langue, self::LNG_ID_T, self::LNG_ID_L );
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
			
			$this->bindSQL( $Requete, ':code', $Code, self::LBR_CODE_T, self::LBR_CODE_L );
			
			if ( $Langue != '*' ) {
				$this->bindSQL( $Requete, ':langue', $Langue, self::LNG_ID_T, self::LNG_ID_L );
			}
			
			$this->executeSQL($Requete);
		}
		
		return true;
	}



	public function supprimerLibelleReferentielParId( $lbr_id ) {
		/**
		 * Supprimer le libellé du référentiel par son Id
		 *
		 * \license Loxense
		 * \author Pierre-Luc MARY
		 *
		 * \param[in] $lbr_id ID du libellé du référentiel à supprimer
		 *
		 * \return Renvoi vrai si le libellé a été supprimé, sinon lève une Exception
		 */

		$SQL = 'DELETE FROM lbr_libelles_referentiel ' .
			'WHERE lbr_id = :lbr_id ';

		$Requete = $this->prepareSQL( $SQL );

		$this->bindSQL( $Requete, ':lbr_id', $lbr_id, PDO::PARAM_INT );

		$this->executeSQL($Requete);

		return true;
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
		if ( $Code == '' ) {
			return 0;
		}

		$Code = mb_strtoupper( trim( $Code ) );
		$Langue = mb_strtolower( trim( $Langue ) );
		$TypeRecherche = mb_strtoupper( trim( $TypeRecherche ) );

		if ( $TypeRecherche == 'S' ) {
			$OperateurRecherche = '=';
		} else {
			$OperateurRecherche = 'LIKE';
		}

		$Recherche = "SELECT count(*) AS total " .
			"FROM lbr_libelles_referentiel AS rlb ";

		if ( $Code != '*' ) {
			$Recherche .= "WHERE lbr_code " . $OperateurRecherche . " :Code ";
		}

		if ( $Langue != '*' ) {
			$Recherche .= 'AND lng_id = :Langue ';
		}

		$Requete = $this->prepareSQL( $Recherche );

		if ( $Code != '*' ) {
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

			$this->bindSQL( $Requete, ':Code', $Code, self::LBR_CODE_T, self::LBR_CODE_L );
		}

		if ( $Langue != '*' ) $this->bindSQL( $Requete, ':Langue', $Langue, self::LNG_ID_T, self::LNG_ID_L );

		$this->executeSQL( $Requete );

		return	$Requete->fetch( PDO::FETCH_OBJ )->total;
	}



	public function recupererLangues() {
		/**
		 * Récupérer toutes les langues à traduire
		 *
		 * \license Loxense
		 * \author Pierre-Luc MARY
		 *
		 *
		 * \return Renvoi la liste des langues ou une liste vide si aucune correspondance. Lève une Exception en cas d'erreur.
		 */

		$Recherche = 'SELECT *
			FROM lng_langages AS "lng"
			WHERE lng_langue_geree = TRUE
			ORDER BY lbr_code, lng_id ';

		$Requete = $this->prepareSQL( $Recherche );

		$this->executeSQL( $Requete);

		return $Requete->fetchAll( PDO::FETCH_CLASS );
	}

}
?>