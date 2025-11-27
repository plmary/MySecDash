<?php

/**
* Ce script gère la validation des BIA d'une Entité.
*
* \license Copyleft Loxense
* \author Pierre-Luc MARY
* \package MyContinuity
* \version 1.0
* \date 2024-10-24
* \note check ok 2024-10-24
*/

// Charge les constantes du projet.
include( 'Constants.inc.php' );


// Charge les informations et droits de base d'un écran.
include( DIR_LIBRAIRIES . '/Loxense-Entete-Standard.php' );

// Charge les libellés en fonction de la langue sélectionnée.
include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );
include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_HBL_Entites.inc.php' );
include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-Activites.php' );
include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-Rapports.php' );
//include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_' . basename( $Script ) );

include( DIR_LIBRAIRIES . '/Class_HBL_Societes_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_Campagnes_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_HBL_Entites_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_PartiesPrenantes_PDO.inc.php' );


// Crée l'instance de l'objet Entites.
$objEntites = new HBL_Entites();
$objSocietes = new HBL_Societes();
$objCampagnes = new Campagnes();
$objPartiesPrenantes = new PartiesPrenantes();


// Définit le format des colonnes du tableau central.
$Trier = 'ent_libelle';

$Format_Colonnes[ 'Prefixe' ] = 'ENT';
$Format_Colonnes[ 'Fonction_Ouverture' ] = 'ouvrirChamp';
$Format_Colonnes[ 'Id' ] = array( 'nom' => 'ent_id' );
$Format_Colonnes[ 'Colonnes' ][] = array(
	'nom' => 'ent_nom', 'titre' => $L_Entite, 'taille' => '2',
	'triable' => 'oui', 'tri_actif' => 'oui', 'sens_tri' => 'ent_nom', 'type' => 'input', 'modifiable' => 'non' );
$Format_Colonnes[ 'Colonnes' ][] = array(
	'nom' => 'ent_description', 'titre' => $L_Description, 'taille' => '4',
	'triable' => 'oui', 'tri_actif' => 'oui', 'sens_tri' => 'ent_description', 'type' => 'textarea', 'modifiable' => 'non' );
$Format_Colonnes[ 'Colonnes' ][] = array(
	'nom' => 'cmen_date_validation', 'titre' => $L_Date_Validation, 'taille' => '2',
	'triable' => 'oui', 'tri_actif' => 'oui', 'sens_tri' => 'cmen_date_validation', 'type' => 'input', 'modifiable' => 'non' );
$Format_Colonnes[ 'Colonnes' ][] = array(
	'nom' => 'ppr_id_validation', 'titre' => $L_Valideur, 'taille' => '2',
	'triable' => 'oui', 'tri_actif' => 'oui', 'sens_tri' => 'ppr_id_validation', 'type' => 'input', 'modifiable' => 'non' );
$Format_Colonnes[ 'Actions' ] = array( 'taille' => '2', 'titre' => $L_Actions,
	'boutons' => array( 'valider' => $Droit_Modifier ) );


// Exécute l'action identifie
switch( $Action ) {
 default:
	$Liste_Societes = '';
	$Liste_Campagnes = '';

	// Initialise les listes déroulantes : Sociétés, Campagnes et Entités
	try {
		list($Liste_Societes, $Liste_Campagnes) =
			actualiseSocieteCampagne($objSocietes, $objCampagnes);
	} catch( Exception $e ) {
		print('<h1 class="text-urgent">' . $e->getMessage() . '</h1>');
		break;
	}

	$Boutons_Alternatifs[] = ['class'=>'btn-rechercher', 'libelle'=>$L_Rechercher, 'glyph'=>'search'];


	$Choix_Campagnes['id'] = 's_cmp_id';
	$Choix_Campagnes['libelle'] = $L_Campagnes;

	if ( $Liste_Campagnes != '' ) {
		foreach( $Liste_Campagnes AS $Campagne ) {
			$Choix_Campagnes['options'][] = array('id' => $Campagne->cmp_id, 'nom' => $Campagne->cmp_date );
		}
	}

	print( $PageHTML->construireEnteteHTML( $L_Validation_BIA_Entite, $Fichiers_JavaScript, 3 ) .
		$PageHTML->construireNavbarJson('Logo-MyContinuity.svg', 'nav-items.json') .
		$PageHTML->construireTitreEcran( $L_Validation_BIA_Entite, $Liste_Societes, $Boutons_Alternatifs, $Choix_Campagnes )
		);

	if ( $Droit_Lecture === TRUE ) {
		// Construit un tableau central vide.
		print( $PageHTML->contruireTableauVide( $Format_Colonnes ) );
	}

	print( $PageHTML->construireFooter( TRUE ) .
		$PageHTML->construirePiedHTML() );

	break;


 /* ========================================================================
 ** Réponses aux appels AJAX
 */

 case 'AJAX_Libeller':
	$Libelles = array(
		'Statut' => 'success',
		'L_Fermer' => $L_Fermer,
		'L_Titre_Ajouter' => $L_Ajouter_Entite,
		'L_Titre_Modifier' => $L_Modifier_Entite,
		'L_Titre_Supprimer' => $L_Supprimer_Entite,
		'L_Ajouter' => $L_Ajouter,
		'L_Modifier' => $L_Modifier,
		'L_Supprimer' => $L_Supprimer,
		'L_Libelle' => $L_Label,
		'L_Nom' => $L_Nom,
		'L_Description' => $L_Description,
		'L_Administrateur' => $L_Administrateur,
		'is_super_admin' => $_SESSION['idn_super_admin'],
		'L_Valider_BIA_Entite' => $L_Valider_BIA_Entite,
		'L_Valider' => $L_Valider,
		'L_Valideur' => $L_Valideur,
		'L_Date_Validation' => $L_Date_Validation,
		'L_Aucune' => $L_Neither_f,
		'L_Informations_Validation' => $L_Informations_Validation
		);

	if ( isset( $_POST['ent_id'] ) ) {
		if ( $_POST['ent_id'] != '' ) {
			if ( ! $PageHTML->verifierEntiteAutorisee($_POST['ent_id']) ) {
				print( json_encode( array( 'Statut' => 'error',
					'texteMsg' => $L_Pas_Droit_Ressource . ' (ent_id="' . $_POST['ent_id'] . '")'.' [' . __LINE__ . ']' ) ) );
				
				$PageHTML->ecrireEvenement( 'ATP_ALERTE', 'OTP_SECURITE', $L_Pas_Droit_Ressource . ' (ent_id="' . $_POST['ent_id'] . '")'.' [' . __LINE__ . ']' );
				
				exit();
			}

			$Libelles['infos_validation'] = $objCampagnes->informationsValidationEntite($_SESSION['s_cmp_id'], $_POST['ent_id']);

			$_SESSION['s_ent_id'] = $_POST['ent_id'];

			$Libelles['liste_parties_prenantes'] = $objPartiesPrenantes->rechercherPartiesPrenantes($_SESSION['s_sct_id']);
		}
	}
	
	print( json_encode( $Libelles ) );

	exit();


 case 'AJAX_Valider':
	if ( $Droit_Modifier === TRUE ) {
		if ( isset($_POST['ent_id']) ) {
			if ( ! $PageHTML->verifierEntiteAutorisee($_POST['ent_id']) ) {
				print( json_encode( array( 'Statut' => 'error',
					'texteMsg' => $L_Pas_Droit_Ressource . ' (ent_id="' . $_POST['ent_id'] . '")'.' [' . __LINE__ . ']' ) ) );

				$PageHTML->ecrireEvenement( 'ATP_ALERTE', 'OTP_SECURITE', $L_Pas_Droit_Ressource . ' (ent_id="' . $_POST['ent_id'] . '")'.' [' . __LINE__ . ']' );

				exit();
			}

			$_POST['ent_id'] = $PageHTML->controlerTypeValeur( $_POST['ent_id'], 'NUMBER' );
			if ( $_POST['ent_id'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (ent_id)'
				) );

				exit();
			}

			$_POST['ppr_id_validation'] = $PageHTML->controlerTypeValeur( $_POST['ppr_id_validation'], 'NUMBER' );
			if ( $_POST['ppr_id_validation'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (ppr_id_validation)'
				) );

				exit();
			}

			$_POST['cmen_date_validation'] = $PageHTML->controlerTypeValeur( $_POST['cmen_date_validation'], 'ASCII' );
			if ( $_POST['cmen_date_validation'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (cmen_date_validation)'
				) );

				exit();
			}

			try {
				$objCampagnes->modifierValidationEntite( $_SESSION['s_cmp_id'], $_POST['ent_id'], $_POST['ppr_id_validation'], $_POST['cmen_date_validation'] );

				$PageHTML->ecrireEvenement( 'ATP_VALIDATION', 'OTP_ENTITE', 'ent_id="' . $_POST['ent_id'] . '", ' . $L_Entite_Validee );

				$Infos_Validation = $objCampagnes->informationsValidationEntite($_SESSION['s_cmp_id'], $_POST['ent_id']);
				
				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Entite_Validee,
					'infos_validation' => $Infos_Validation
				);

			} catch (Exception $e) {
				$Statut = 'error';
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Gestion des doublons
					$Message = $L_ERR_DUPL_Entite;
				}

				$Resultat = array(
					'statut' => $Statut,
					'texteMsg' => $Message
				);
			}

			echo json_encode( $Resultat );
		} else {
			echo jsen_encode( array(
				'statut' => 'error',
				'texteMsg' => $L_ERR_Champs_Obligatoires
			) );
		}
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		) );
	}
	break;


 case 'AJAX_Trier':
	if ( $Droit_Lecture === TRUE ) {
		$Trier = $_POST[ 'trier' ];

		try {
			if ( isset( $_SESSION['s_cmp_id'] ) && $_SESSION['s_cmp_id'] != '' ) {
				$Entites = $objCampagnes->rechercherEntitesCampagne( $_SESSION['s_cmp_id'], '', $Trier );
				$Total = $objCampagnes->RowCount;

				$Texte_HTML = '';

				foreach ($Entites as $Occurrence) {
					if ($Occurrence->ent_description == NULL) $Occurrence->ent_description = '';
					if ($Occurrence->ppr_id_validation != NULL) {
						$Occurrence->ppr_id_validation = $Occurrence->ppr_nom . ' ' . $Occurrence->ppr_prenom;
					} else {
						$Occurrence->ppr_id_validation = '';
					}

					$Texte_HTML .= $PageHTML->creerOccurrenceCorpsTableau( $Occurrence->ent_id, $Occurrence, $Format_Colonnes );
				}
			} else {
				$Texte_HTML = '<div class="row justify-content-md-center mt-2"><div class="col col-lg-8"><h2 class="text-center">' . $L_Campagne_Sans_Entite . '</h2></div></div>' .
					'<div class="row justify-content-md-center mb-2"><div class="col col-lg-4 text-center"><a href="' . URL_BASE . '/MySecDash-Entites.php" class="btn btn-primary btn-gerer-campagnes">' . $L_Gestion_Entites . '</a></div></div>';
				$Total = 0;
			}

			echo json_encode( array(
				'statut' => 'success',
				'texteHTML' => $Texte_HTML,
				'total' => $Total,
				'droit_modifier' => $Droit_Modifier,
				'droit_supprimer' => $Droit_Supprimer,
				'sct_id' => $_SESSION['s_sct_id'],
				'cmp_id' => $_SESSION['s_cmp_id']
			) );
		} catch( Exception $e ) {
			echo json_encode( array(
				'statut' => 'error',
				'texteMsg' => $e->getMessage()
				) );
		}
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
			) );
	}
	break;


 case 'AJAX_Selectioner_Societe':
	if ( isset($_POST['sct_id']) ) {
		if ( ! $PageHTML->verifierSocieteAutorisee($_POST['sct_id']) ) {
			print( json_encode( array( 'Statut' => 'error',
				'texteMsg' => $L_Pas_Droit_Ressource . ' (sct_id="' . $_POST['sct_id'] . '")'.' [' . __LINE__ . ']' ) ) );
			
			$PageHTML->ecrireEvenement( 'ATP_ALERTE', 'OTP_SECURITE', $L_Pas_Droit_Ressource . ' (sct_id="' . $_POST['sct_id'] . '")'.' [' . __LINE__ . ']' );
			
			exit();
		}
		
		$_SESSION['s_sct_id'] = $_POST['sct_id'];
		
		try {
			list($Liste_Societes, $Liste_Campagnes) =
				actualiseSocieteCampagne($objSocietes, $objCampagnes, 2);
		} catch ( Exception $e ) {
			$Resultat = array( 'statut' => 'error',
				'texteMsg' => $e->getMessage() );
			echo json_encode( $Resultat );
			break;
		}
		
		$Resultat = array( 'statut' => 'success',
			'texteMsg' => $L_Societe_Change,
			'sct_id' => $_SESSION['s_sct_id'],
			'cmp_id' => $_SESSION['s_cmp_id'],
			'L_Societe_Sans_Campagne' => $L_Societe_Sans_Campagne,
			'L_Gestion_Campagnes' => $L_Gestion_Campagnes,
			'L_Campagne_Sans_Entite' => $L_Campagne_Sans_Entite,
			'L_Gestion_Entites' => $L_Gestion_Entites,
			'Liste_Campagnes' => $Liste_Campagnes
		);
	} else {
		$Resultat = array( 'statut' => 'error',
			'texteMsg' => $L_ERR_Champs_Obligatoires . ' (sct_id)' );
	}
	
	echo json_encode( $Resultat );
	
	break;
	
	
 case 'AJAX_Selectioner_Campagne':
	if ( isset($_POST['cmp_id']) ) {
		if ( ! $PageHTML->verifierCampagneAutorisee($_POST['cmp_id']) ) {
			print( json_encode( array( 'Statut' => 'error',
				'texteMsg' => $L_Pas_Droit_Ressource . ' (cmp_id="' . $_POST['cmp_id'] . '")'.' [' . __LINE__ . ']' ) ) );
			
			$PageHTML->ecrireEvenement( 'ATP_ALERTE', 'OTP_SECURITE', $L_Pas_Droit_Ressource . ' (ent_id="' . $_POST['ent_id'] . '")'.' [' . __LINE__ . ']' );
			
			exit();
		}
		
		$_SESSION['s_cmp_id'] = $_POST['cmp_id'];
		
		try {
			list($Liste_Societes, $Liste_Campagnes) =
				actualiseSocieteCampagne($objSocietes, $objCampagnes, 3);
		} catch ( Exception $e ) {
			$Resultat = array( 'statut' => 'error',
				'texteMsg' => $e->getMessage() );
			echo json_encode( $Resultat );
			break;
		}
		
		$Resultat = array( 'statut' => 'success',
			'texteMsg' => $L_Campagne_Change,
			'sct_id' => $_SESSION['s_sct_id'],
			'cmp_id' => $_SESSION['s_cmp_id']
		);
	} else {
		$Resultat = array( 'statut' => 'error',
			'texteMsg' => $L_ERR_Champs_Obligatoires . ' (cmp_id)' );
	}
	
	echo json_encode( $Resultat );
	
	break;
}



function actualiseSocieteCampagne($objSocietes, $objCampagnes, $forcer=0) {
	/**
	 * Actualise les listes Sociétés, Campagnes et Entités à l'entrée dans l'écran et en cas de changement.
	 *
	 * \license Copyleft Loxense
	 * \author Pierre-Luc MARY
	 * \date 2024-10-01
	 *
	 * \param[in] $objSocietes Objet permettant d'accéder aux fonctions de gestion des Sociétés
	 * \param[in] $objCampagnes Objet permettant d'accéder aux fonctions de gestion des Campagnes
	 * \param[in] $forcer Flag permettant de forcer le résultat (0=Tout charger, 1=Changer Société, 2=Changer Campagne)
	 *
	 * \return Renvoi un tableau d'objet ou un tableau vide si pas de données trouvées. Lève une exception en cas d'erreur.
	 */
	include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );
	
	$Liste_Societes = [];
	$Liste_Campagnes = [];
	
	
	switch ( $forcer ) {
		case 1:
			// Comme on vient de change de Société, on efface les variables de Session qui pointaient :
			//   sur une Campagne
			//   et sur une Entité.
			// Ainsi, on forcera le repositionnement sur la première occurrence de Campagne et d'Entité.
			unset($_SESSION['s_cmp_id']);
			unset($_SESSION['s_ent_id']);
			break;
			
		case 2:
			// Comme on vient de change de Campagne, on efface la variable de Session qui pointait sur une Entité.
			// Ainsi, on forcera le repositionnement sur la première occurrence d'Entité.
			unset($_SESSION['s_ent_id']);
			break;
	}
	
	
	// Récupère les Sociétés accessibles pour l'Utilisateur
	if ( $_SESSION['idn_super_admin'] === TRUE ) {
		$Liste_Societes = $objSocietes->rechercherSocietes();
		if ( $Liste_Societes == [] ) {
			// Si pas de société, alors on efface tout et on lève une exception.
			$_SESSION['s_sct_id'] = '';
			$_SESSION['s_cmp_id'] = '';
			
			throw new Exception($L_Plus_De_Societe, 0);
		} else {
			// Si la variable de Session n'est pas initialisé, alors on l'initialise sur la première occurrence de notre résultat.
			if ( ! isset($_SESSION['s_sct_id']) or $_SESSION['s_sct_id'] == '' ) {
				$_SESSION['s_sct_id'] = $Liste_Societes[0]->sct_id;
			}
		}
	} else {
		$Liste_Societes = $objSocietes->rechercherSocietes('', '', $_SESSION['idn_id']);
		if ( $Liste_Societes == [] ) {
			// Si pas de société, alors on efface tout et on lève une exception.
			$_SESSION['s_sct_id'] = '';
			$_SESSION['s_cmp_id'] = '';
			
			throw new Exception($L_Pas_Societe_Autorisee_Pour_Utilisateur, 0);
		} else {
			if ( ! isset($_SESSION['s_sct_id']) or $_SESSION['s_sct_id'] == '' ) {
				$_SESSION['s_sct_id'] = $Liste_Societes[0]->sct_id;
			} else {
				// On contrôle que l'utilisateur a encore accès à cette Société, sinon on lève une exception.
				$_Autorise = 0;
				foreach ($Liste_Societes as $_Tmp) {
					if ( $_Tmp->sct_id == $_SESSION['s_sct_id'] ) {
						$_Autorise = 1;
						break;
					}
				}
				
				if ( $_Autorise == 0 ) {
					throw new Exception($L_Societe_Plus_Autorisee_Pour_Utilisateur, 0);
				}
			}
		}
	}
	
	// Récupère les Campagnes associées à la Société Sélectionnée.
	$Liste_Campagnes = $objCampagnes->rechercherCampagnes($_SESSION['s_sct_id'], 'cmp_date-desc');
	if ( $Liste_Campagnes == [] ) {
		$tmpObj1 = new stdClass();
		$tmpObj1->cmp_id = '';
		$tmpObj1->cmp_date = '---';
		$Liste_Campagnes[0] = $tmpObj1;

		$tmpObj2 = new stdClass();
		$tmpObj2->ent_id = '';
		$tmpObj2->ent_nom = '---';
		$tmpObj2->total_activites = 0;
		$Liste_Entites[0] = $tmpObj2;

		$_SESSION['s_cmp_id'] = '';
		$_SESSION['s_ent_id'] = '';

		return [$Liste_Societes, $Liste_Campagnes];
	} else {
		if ( ! isset($_SESSION['s_cmp_id']) or $_SESSION['s_cmp_id'] == '' ) {
			$_SESSION['s_cmp_id'] = $Liste_Campagnes[0]->cmp_id;
		} else {
			// On contrôle que l'utilisateur a encore accès à cette Société.
			$_Autorise = 0;
			
			foreach ($Liste_Campagnes as $_Tmp) {
				if ( $_Tmp->cmp_id == $_SESSION['s_cmp_id'] ) {
					$_Autorise = 1;
					break;
				}
			}
			
			if ( $_Autorise == 0 ) {
				$_SESSION['s_cmp_id'] = $Liste_Campagnes[0]->cmp_id;
			}
		}
	}
	
	//print($_SESSION['s_sct_id'].' - '.$_SESSION['s_cmp_id'].' - '.$_SESSION['s_ent_id'].'<hr>');
	
	return [$Liste_Societes, $Liste_Campagnes];
}
?>