<?php

include_once( 'Constants.inc.php' );
include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Securite.inc.php' );

class HBL_Autorisations extends HBL_Securite {
/**
* Cette classe gère l'authentification des utilisateurs.
*
* \license Copyright Loxense
* \author Pierre-Luc MARY
* \date 2015-06-01
*
*/


	public function __construct() {
	/**
	* Connexion à la base de données via le IICA_DB_Connector.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-06-01
	*
	* \return Renvoi un booléen sur le succès de la connexion à la base de données
	*/
		parent::__construct();
		
		return TRUE;
	}


	public function permission( $Element, $Right = 'RGH_1', $Type = 'L' ) {
	/**
	* Vérifie si l'utilisateur (identifié par $_SESSION[ 'idn_id' ]) à un droit de "Lecture" sur le "script" spécifié 
	* (par son nom ou son type).
	* Sinon, vérifie si l'utilisateur à un droit "Right" sur le script spécifié.
	* En revanche, si l'utilisateur est un "Super Admin", on l'autorise immédiatement.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-06-01
	*
	* \param[in] $Element Identifiant de l'identité (de l'utilisateur)
	* \param[in] $Right Identifiant de l'identité (de l'utilisateur)
	* \param[in] $Type Si "S" alors on recherche par la localisation, sinon par le code de l'application.
	*
	* \return Retourne "TRUE" s'il est autorisé, sinon "FALSE".
	*/
		if ( isset( $_SESSION['idn_super_admin'] ) ) {
			if ( $_SESSION['idn_super_admin'] === TRUE ) return TRUE;
		}

		$Request = "SELECT " .
		 "t4.drt_code_libelle " .
		 "FROM caa_controle_acces_application_interne AS t1 " .
		 "LEFT JOIN idpr_idn_prf AS t2 ON t2.prf_id = t1.prf_id " .
		 "LEFT JOIN ain_applications_internes AS t3 ON t3.ain_id = t1.ain_id " .
		 "LEFT JOIN drt_droits AS t4 ON t4.drt_id = t1.drt_id " .
		 "WHERE t4.drt_code_libelle = :right " ;

		if ( $Type == 'L' ) {
			$Request .= "AND t3.ain_localisation = :Element ";
			$Size = 255;
		} else {
			$Request .= "AND t3.ain_libelle = :Element ";
			$Size = 50;
		}

		$Request .= "AND t2.idn_id = :idn_id ";

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':idn_id', $_SESSION[ 'idn_id' ], PDO::PARAM_INT );

		$this->bindSQL( $Query, ':right', $Right, PDO::PARAM_STR, 45 );

		if ( strpos( $Element, '/' ) !== FALSE ) {
			$Tmp = explode( '/', $Element );
			$Element = $Tmp[ 1 ];
		}

		$this->bindSQL( $Query, ':Element', $Element, PDO::PARAM_STR, $Size );
		
		$this->executeSQL( $Query );

		$Data = $Query->fetchObject();
		if (  $Data == '' ) {
			return FALSE;
		}

		return TRUE;
	}
	
	
	public function controlerPermission( $Script, $Right = 'RGH_1' ) {
		/**
		 * Contrôler si l'utilisateur (identifié par $_SESSION[ 'idn_id' ]) à un droit sur un "script".
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-10-16
		 *
		 * \param[in] $Script Nom du script
		 * \param[in] $Right Code du droit à contrôler
		 *
		 * \return Retourne "TRUE" s'il est autorisé, sinon "FALSE".
		 */
		if ( isset( $_SESSION['idn_super_admin'] ) ) {
			if ( $_SESSION['idn_super_admin'] === TRUE ) return TRUE;
		}
		
		$Request = 'SELECT drt.drt_code_libelle
			FROM caa_controle_acces_application_interne AS "caa"
			LEFT JOIN idpr_idn_prf AS "idpr" ON idpr.prf_id = caa.prf_id
			LEFT JOIN ain_applications_internes AS "ain" ON ain.ain_id = caa.ain_id
			LEFT JOIN drt_droits AS drt ON drt.drt_id = caa.drt_id
			WHERE drt.drt_code_libelle = :right
			AND ain.ain_localisation = :script
			AND idpr.idn_id = :idn_id ';
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':idn_id', $_SESSION[ 'idn_id' ], PDO::PARAM_INT );
		
		$this->bindSQL( $Query, ':right', $Right, PDO::PARAM_STR, 45 );
		
		$this->bindSQL( $Query, ':script', $Script, PDO::PARAM_STR, 255 );
		
		$this->executeSQL( $Query );
		
		$Data = $Query->fetchObject();
		if ( $Data == '' ) {
			return FALSE;
		}
		
		return TRUE;
	}


	public function permissions( $Script = '' ) {
	/**
	* Récupère les permissions d'un utilisateur (identifié par $_SESSION[ 'idn_id' ]) sur le "script" spécifié.
	* Si pas de script spécifié, alors la recherche sera faite pour toutes les applications associées à cet utilisateur.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-06-01
	*
	* \param[in] $Script Identifiant de l'identité (de l'utilisateur)
	*
	* \return Retourne un tableau de permission où chaque occurrence est un droit spécifique sur une application (script).
	*/

		$Where = '';

		$Request = "SELECT " .
		 "t3.ain_libelle, t3.ain_localisation, t4.drt_code_libelle " .
		 "FROM caa_controle_acces_application_interne AS t1 " .
		 "LEFT JOIN idpr_idn_prf AS t2 ON t2.prf_id = t1.prf_id " .
		 "LEFT JOIN ain_applications_internes AS t3 ON t3.ain_id = t1.ain_id " .
		 "LEFT JOIN drt_droits AS t4 ON t4.drt_id = t1.drt_id " ;

		if ( isset( $_SESSION['idn_super_admin'] ) ) {
			if ( $_SESSION['idn_super_admin'] === FALSE ) {
				$Where .= "WHERE t2.idn_id = :idn_id ";
			}
		}

		if ( $Script != '' ) {
			if ( $Where == '' ) $Where .= 'WHERE ';
			else $Where .= 'AND ';

			$Where .= "t3.ain_localisation = :script ";
		}

		$Request .= $Where;

		$Request .= "GROUP BY t3.ain_libelle, t3.ain_localisation, t4.drt_code_libelle ";

		$Request .= "ORDER BY t3.ain_localisation, t4.drt_code_libelle ";


		$Query = $this->prepareSQL( $Request );


		if ( isset( $_SESSION['idn_super_admin'] ) ) {
			if ( $_SESSION['idn_super_admin'] === FALSE ) {
				$this->bindSQL( $Query, ':idn_id', $_SESSION[ 'idn_id' ], PDO::PARAM_INT );
			}
		}

		if ( $Script != '' ) {
			if ( strpos( $Script, '/' ) !== FALSE ) {
				$Tmp = explode( '/', $Script );
				$Script = $Tmp[ 1 ];
			}

			$this->bindSQL( $Query, ':script', $Script, PDO::PARAM_STR, 255 );
		}

		$this->executeSQL( $Query );

		return $Query->fetchAll();
	}


	public function permissionsGroupees( $Script = '' ) {
	/**
	* Récupère les permissions d'un utilisateur (identifié par $_SESSION[ 'idn_id' ]) sur le "script" spécifié.
	* Si pas de script spécifié, alors la recherche sera faite pour toutes les applications associées à cet utilisateur.
	* A la différence de la méthode "permissions", cette méthode regroupe son résultat par application dans le tableau qu'elle retourne.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-06-01
	*
	* \param[in] $Script Identifiant de l'identité (de l'utilisateur)
	*
	* \return Retourne un tableau de permission où chaque occurrence est un ensemble de droits par application (script).
	*/


		if ( isset( $_SESSION['idn_super_admin'] ) ) {
			if ( $_SESSION['idn_super_admin'] === FALSE ) {
				$Request = "SELECT DISTINCT " .
				 "t3.ain_libelle, t3.ain_localisation, t4.drt_code_libelle " .
				 "FROM caa_controle_acces_application_interne AS t1 " .
				 "LEFT JOIN idpr_idn_prf AS t2 ON t2.prf_id = t1.prf_id " .
				 "LEFT JOIN ain_applications_internes AS t3 ON t3.ain_id = t1.ain_id " .
				 "LEFT JOIN drt_droits AS t4 ON t4.drt_id = t1.drt_id " .
				 "WHERE t2.idn_id = :idn_id ";

				if ( $Script != '' ) $Request .= "AND t3.ain_localisation = :script ";

			} else {
				$Request = "SELECT DISTINCT " .
				 "t3.ain_libelle, t3.ain_localisation, t4.drt_code_libelle " .
				 "FROM ain_applications_internes AS t3 " .
				 "CROSS JOIN drt_droits AS t4 ";

				 if ( $Script != '' ) $Request .= "WHERE t3.ain_localisation = :script ";
			}
		}

		$Request .= "ORDER BY t3.ain_localisation, t4.drt_code_libelle ";

		$Query = $this->prepareSQL( $Request );

		if ( isset( $_SESSION['idn_super_admin'] ) ) {
			if ( $_SESSION['idn_super_admin'] === FALSE ) {
				$this->bindSQL( $Query, ':idn_id', $_SESSION[ 'idn_id' ], PDO::PARAM_INT );
			}
		}

		if ( $Script != '' ) {
			if ( strpos( $Script, '/' ) !== FALSE ) {
				$Tmp = explode( '/', $Script );
				$Script = $Tmp[ 1 ];
			}

			$this->bindSQL( $Query, ':script', $Script, PDO::PARAM_STR, 255 );
		}
		 
		$this->executeSQL( $Query );

		$Data = array();
		
		$Localization = '';
		$Label = '';
		$Rights = array();
		$Tmp = '';
		
		if ( $Query->rowCount() == 0 ) return FALSE;

		while ( $Occurrence = $Query->fetchObject() ) {
			if ( $Tmp == '' ) {
				$Tmp = $Occurrence->ain_localisation;
				$Localization = $Occurrence->ain_localisation;
				$Label = $Occurrence->ain_libelle;
			}


			if ( $Tmp != $Occurrence->ain_localisation ) {
				$Tmp = $Occurrence->ain_localisation;

				$Data[ $Localization ] = array( 'label' => $Label, 'rights' => $Rights );

				$Localization = $Occurrence->ain_localisation;
				$Label = $Occurrence->ain_libelle;
				$Rights = array( $Occurrence->drt_code_libelle );
			} else {
				$Rights[] = $Occurrence->drt_code_libelle;
			}

			$Data[ $Localization ] = array( 'label' => $Label, 'rights' => $Rights );
		}
 
 		return $Data;
	}
   	

	public function voirTempsSession() {
	/**
	* Affiche le nombre de minutes avant expiration de la session.
	*
	* \license Copyright Orasys
	* \author Pierre-Luc MARY
	* \date 2014-03-10
	*
	* \return Retourne vrai si la session n'a pas expirée, sinon retourne faux.
	*/
		$expired_date = new DateTime( date( 'Y-m-d H:i:s', $_SESSION[ 'Expired' ] ) );
		$since_date = new DateTime( date( 'Y-m-d H:i:s' ) );
		$session_date = $since_date->diff( $expired_date );

		$minutes = (($session_date->d * (24 * 60)) + ($session_date->h*(60)) + $session_date->i) + 1 ;

		return $minutes;
	}
   	

	public function validerTempsSession() {
	/**
	* Contrôle si la session n'a pas expirée.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-06-01
	*
	* \return Retourne vrai si la session n'a pas expirée, sinon retourne faux.
	*/
		if ( ! isset($_SESSION[ 'Expired' ]) ) return FALSE;

		if ( $_SESSION[ 'Expired' ] < time() ) {
			return FALSE;
		} else {
			return TRUE;
		}
	}


	public function sauverTempsSession() {
	/**
	* Calcul la nouvelle date d'expiration et la stocke.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2015-06-01
	*
	* \return Retourne vrai en cas de succès, sinon retourne faux
	*/
		$_SESSION[ 'Expired' ] = time() + ( $this->recupererParametre( 'expiration_time' ) * 60 );
		
		return TRUE;
	}


	public function listerEntitesAutorisees() {
	/**
	* Récupère la liste de toutes les Entités auxquelles a accès l'Utilisateur Courant
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2017-07-17
	*
	* \return Retourne la liste des Entités sur lesquelles l'utilsateur est autorisé.
	*/
		if ( $_SESSION['idn_super_admin'] == TRUE ) return $this->__listerToutesEntites();

		$Request = 'SELECT ent.ent_id, ent.ent_libelle 
FROM iden_idn_ent AS "iden"
LEFT JOIN ent_entities AS "ent" ON ent.ent_id = iden.ent_id 
WHERE iden.idn_id = ' . $_SESSION['idn_id'] . ' ';

		$Query = $this->prepareSQL( $Request );

		$this->executeSQL( $Query );

		return $Query->fetchAll();
	}


	public function listerSocietesAutorisees() {
		/**
		 * Récupère la liste de toutes les Sociétés auxquelles a accès l'Utilisateur Courant
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-10-10
		 *
		 * \return Retourne la liste des Entités sur lesquelles l'utilsateur est autorisé.
		 */
		if ( $_SESSION['idn_super_admin'] == TRUE ) return $this->__listerToutesSocietes();
		
		$Request = 'SELECT sct.sct_id, sct.sct_nom
FROM idsc_idn_sct AS "idsc"
LEFT JOIN sct_societes AS "sct" ON sct.sct_id = idsc.sct_id
WHERE idsc.idn_id = ' . $_SESSION['idn_id'] . ' ';
		
		$Query = $this->prepareSQL( $Request );
		
		
		$this->executeSQL( $Query );
		
		return $Query->fetchAll();
	}


	public function verifierSocieteAutorisee( $sct_id ) {
		/**
		 * Teste si l'Utilisateur courant à les droits sur cette Société
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-01-05
		 *
		 * \param[in] $sct_id ID de la Société à vérifier
		 *
		 * \return Retourne "0" si pas autorisé, sinon supérieur "0" si autorisé.
		 */
		if ( $_SESSION['idn_super_admin'] == TRUE ) return 1;
		
		$Request = 'SELECT count(idsc.sct_id) AS "autorise"
FROM idsc_idn_sct AS "idsc"
WHERE idsc.idn_id = ' . $_SESSION['idn_id'] . ' AND idsc.sct_id = :sct_id ';
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':sct_id', $sct_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		return $Query->fetchObject()->autorise;
	}


	public function verifierEntiteAutorisee( $ent_id ) {
	/**
	* Teste si l'Utilisateur courant à les droits sur une Entité
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2017-07-17
	*
	* \param[in] $ent_id ID de l'Entité à vérifier
	*
	* \return Retourne "0" si pas autorisé, sinon supérieur "0" si autorisé.
	*/
		if ( $_SESSION['idn_super_admin'] == TRUE ) return 1;
		
		$Request = 'SELECT count(iden.ent_id) AS "autorise" 
FROM iden_idn_ent AS "iden"
WHERE iden.idn_id = ' . $_SESSION['idn_id'] . ' AND iden.ent_id = :ent_id ';

		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':ent_id', $ent_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );

		return $Query->fetchObject()->autorise;
	}


	public function verifierActiviteAutorisee( $act_id ) {
		/**
		 * Teste si l'Utilisateur courant à les droits sur une Activité
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-10-19
		 *
		 * \param[in] $act_id ID de l'Activité à vérifier
		 *
		 * \return Retourne "0" si pas autorisé, sinon supérieur "0" si autorisé.
		 */
		if ( $_SESSION['idn_super_admin'] == TRUE ) return 1;

		$Request = 'SELECT count(iden.ent_id) AS "autorise"
FROM act_activites AS "act"
LEFT JOIN iden_idn_ent AS "iden" ON iden.ent_id = act.ent_id
WHERE iden.idn_id = ' . $_SESSION['idn_id'] . ' AND act.act_id = :act_id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':act_id', $act_id, PDO::PARAM_INT );

		$this->executeSQL( $Query );

		return $Query->fetchObject()->autorise;
	}


	public function verifierCampagneAutorisee( $cmp_id ) {
		/**
		 * Teste si l'Utilisateur courant à les droits sur une Campagne
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2017-07-17
		 *
		 * \param[in] $cmp_id ID de la Campagne à vérifier
		 *
		 * \return Retourne "0" si pas autorisé, sinon supérieur "0" si autorisé.
		 */

		if ( $_SESSION['idn_super_admin'] == TRUE ) return 1;
		
		$Request = 'SELECT count(idsc.sct_id) AS "autorise"
FROM cmp_campagnes AS "cmp"
RIGHT JOIN idsc_idn_sct AS "idsc" ON idsc.sct_id = cmp.sct_id
WHERE idsc.idn_id = ' . $_SESSION['idn_id'] . ' AND cmp.cmp_id = :cmp_id ';
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':cmp_id', $cmp_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );
		
		return $Query->fetchObject()->autorise;
	}


	public function listerEntitesAdministrables() {
	/**
	* Récupère la liste de toutes les Entités auxquelles l'Utilisateur a un droit d'administration.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2017-07-17
	*
	* \return Retourne la liste des Entités sur lesquelles l'utilsateur est autorisé.
	*/
		if ( $_SESSION['idn_super_admin'] == TRUE ) return $this->__listerToutesEntites();

		$Request = 'SELECT ent.ent_id, ent.ent_libelle 
FROM iden_idn_ent AS "iden"
LEFT JOIN ent_entities AS "ent" ON ent.ent_id = iden.ent_id 
WHERE iden.iden_admin = TRUE AND iden.idn_id = ' . $_SESSION['idn_id'] . ' ';

		$Query = $this->prepareSQL( $Request );

		$this->executeSQL( $Query );

		return $Query->fetchAll();
	}


	public function verifierEntiteAdministrable( $ent_id ) {
	/**
	* Vérifie si l'Entité est administrable par l'Utilisateur.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2017-07-17
	*
	* \param[in] $ent_id ID de l'Entité à vérifier
	*
	* \return Retourne "0" si pas autorisé, sinon supérieur "0" si autorisé.
	*/
		if ( $_SESSION['idn_super_admin'] == TRUE ) return 1;

		$Request = 'SELECT iden.iden_admin
FROM iden_idn_ent AS "iden"
WHERE iden.idn_id = ' . $_SESSION['idn_id'] . ' AND iden.ent_id = : ent_id ';

		$Query = $this->prepareSQL( $Request );

		$this->bindSQL( $Query, ':ent_id', $ent_id, PDO::PARAM_INT );
		
		$Droit = $this->executeSQL( $Query )->fetchObject()->iden_admin;
		
		if ( $Droit === TRUE ) {
			$Droit = 1;
		} else {
			$Droit = 0;
		}

		return $Droit;
	}


	public function verifierSocieteAdministrable( $sct_id ) {
		/**
		 * Vérifie si l'Entité est administrable par l'Utilisateur.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-10-10
		 *
		 * \param[in] $ent_id ID de l'Entité à vérifier
		 *
		 * \return Retourne "0" si pas autorisé, sinon supérieur "0" si autorisé.
		 */
		if ( $_SESSION['idn_super_admin'] == TRUE ) return 1;
		
		$Request = 'SELECT idsc.idsc_admin
FROM idsc_idn_sct AS "idsc"
WHERE idsc.idn_id = ' . $_SESSION['idn_id'] . ' AND idsc.sct_id = :sct_id ';
		
		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':sct_id', $sct_id, PDO::PARAM_INT );
		
		$Droit = $this->executeSQL( $Query )->fetchObject()->iden_admin;
		
		if ( $Droit === TRUE ) {
			$Droit = 1;
		} else {
			$Droit = 0;
		}
		
		return $Droit;
	}


	private function __listerToutesEntites() {
	/**
	* Récupère toutes les Entités.
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2017-07-17
	*
	* \return Retourne la liste de toutes les Entités.
	*/
		$Request = 'SELECT ent_id, ent_libelle FROM ent_entities AS "ent" ';

		$Query = $this->prepareSQL( $Request );

		$this->executeSQL( $Query );

		return $Query->fetchAll();
	}
	
	
	private function __listerToutesSocietes() {
		/**
		 * Récupère toutes les Sociétés.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-10-10
		 *
		 * \return Retourne la liste de toutes les Entités.
		 */
		$Request = 'SELECT sct_id, sct_nom FROM sct_societes AS "sct" ';
		
		$Query = $this->prepareSQL( $Request );
		
		$this->executeSQL( $Query );
		
		return $Query->fetchAll();
	}
	

	public function verifierCartographieAutorisee( $crs_id ) {
	/**
	* Teste si l'Utilisateur courant à les droits sur l'Entité de la Cartographie
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2017-12-01
	*
	* \param[in] $crs_id ID de la Cartographie à vérifier
	*
	* \return Retourne "0" si pas autorisé, sinon supérieur "0" si autorisé.
	*/
		if ( $_SESSION['idn_super_admin'] == TRUE ) return 1;
		
		$Request = 'SELECT count(iden.ent_id) AS "autorise" 
FROM crs_cartographies_risques AS "crs"
LEFT JOIN iden_idn_ent AS "iden" ON iden.ent_id = crs.ent_id
WHERE iden.idn_id = ' . $_SESSION['idn_id'] . ' AND crs.crs_id = :crs_id ';

		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':crs_id', $crs_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );

		return $Query->fetchObject()->autorise;
	}


	public function verifierUtilisateurAutorise( $idn_id ) {
	/**
	* Teste si l'Utilisateur courant à les droits sur l'Entité de l'Utilisateur
	*
	* \license Copyright Loxense
	* \author Pierre-Luc MARY
	* \date 2017-12-01
	*
	* \param[in] $idn_id ID de l'Utilisateur à vérifier
	*
	* \return Retourne "0" si pas autorisé, sinon supérieur "0" si autorisé.
	*/
		if ( $_SESSION['idn_super_admin'] == TRUE ) return 1;
		
		$Request = 'SELECT count(iden.ent_id) AS "autorise" 
FROM idn_identites AS "idn"
LEFT JOIN iden_idn_ent AS "iden" ON iden.ent_id = idn.ent_id
WHERE iden.idn_id = ' . $_SESSION['idn_id'] . ' AND idn.idn_id = :idn_id ';

		$Query = $this->prepareSQL( $Request );
		
		$this->bindSQL( $Query, ':idn_id', $idn_id, PDO::PARAM_INT );
		
		$this->executeSQL( $Query );

		return $Query->fetchObject()->autorise;
	}


	public function selectionnerSociete() {
		/**
		 * Sélectionne le nouveau choix de Société par l'Utilisateur, tout en vérifiant, s'il a bien les droits.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-10-31
		 *
		 * \param[in] $_POST variable système initié par le formulaire
		 * 
		 * \param[out] $_SESSION['s_sct_id'] variable système dans lequel on stocke la Société qui vient d'être sélectionnée
		 *
		 * \return Retourne "TRUE" si autorisé et changement effectué, sinon lève une exception.
		 */
		if ( isset($_POST['sct_id']) ) {
			include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );

			if ( ! $this->verifierSocieteAutorisee($_POST['sct_id']) ) {
				print( json_encode( array( 'Statut' => 'error',
					'texteMsg' => $L_Pas_Droit_Ressource . ' (sct_id="' . $_POST['sct_id'] . '")'.' [' . __LINE__ . ']' ) ) );
				
				$this->ecrireEvenement( 'ATP_ALERTE', 'OTP_SECURITE', $L_Pas_Droit_Ressource . ' (sct_id="' . $_POST['sct_id'] . '")'.' [' . __LINE__ . ']' );
				
				exit();
			}

			$_SESSION['s_sct_id'] = $_POST['sct_id'];

			$Resultat = array( 'statut' => 'success',
				'texteMsg' => $L_Societe_Change );
		} else {
			$Resultat = array( 'statut' => 'error',
				'texteMsg' => $L_ERR_Champs_Obligatoires . ' (sct_id)' );
		}

		echo json_encode( $Resultat );

		return TRUE;
	}


	public function calculerJetonDeConnexion() {
		/**
		 * Calcule un jeton de connexion d'après les caractéristiques du client initiant la connexion.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-10-31
		 *
		 * \param[in] $_SERVER Variable système dans laquelle on récupère les informations du Client
		 * 
		 * \return Retourne "TRUE" si autorisé et changement effectué, sinon lève une exception.
		 */

		$Texte = $_SERVER['HTTP_USER_AGENT'] . '##' . $_SERVER['HTTP_ACCEPT_LANGUAGE'] . '##' . $_SERVER['HTTP_ACCEPT_ENCODING'] .
			'##' . $_SERVER['HTTP_COOKIE'] . '##' . $_SERVER['REMOTE_ADDR'];

		return hash('sha256', $Texte );
	}


	public function stockerJetonDeConnexion() {
		/**
		 * Calcule et stocke le jeton de connexion d'après les caractéristiques du client initiant la connexion.
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-10-31
		 *
		 * \param[out] $_SESSION['jeton_session'] Variable système dans laquelle on stocke le hash des informations du Client
		 *
		 * \return Retourne "TRUE" après avoir calculé et stocké le "Hash", sinon lève une exception.
		 */

		$_SESSION['jeton_session'] = $this->calculerJetonDeConnexion();

		return TRUE;
	}


	public function comparerJetonDeConnexion() {
		/**
		 * Compare le jeton de connexion avec les dernières informations du client et celui qui a initié la Session
		 *
		 * \license Copyright Loxense
		 * \author Pierre-Luc MARY
		 * \date 2024-10-31
		 *
		 * \param[in] $_SERVER Variable système dans laquelle on récupère les informations du Client
		 *
		 * \return Retourne "TRUE" si le jeton qui vient d'être calculé est égal à celui d'origine, sinon "FALSE". Lève une exception en cas d'erreur.
		 */

		if ( ! array_key_exists('jeton_session', $_SESSION) ) {
			return FALSE;
		}

		$New_Jeton = $this->calculerJetonDeConnexion();
		if ( $_SESSION['jeton_session'] == $New_Jeton ) {
			return TRUE;
		} else {
			print($_SESSION['jeton_session'].' == '.$New_Jeton);
			return FALSE;
		}
	}
}

?>
