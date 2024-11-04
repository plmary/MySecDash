<?php

/**
* Ce script gère les Actions de Sécurité de Loxense.
*
* \license Copyleft
* \author Pierre-Luc MARY
* \package MySecDash
* \date 2016-08-20
*/

// Charge les constantes du projet.
include( 'Constants.inc.php' );

// Démarre le gestionnaire de session du serveur.
session_save_path( DIR_SESSION );
session_start();

// Initialise la langue Française par défaut.
if ( ! isset( $_SESSION[ 'Language' ] ) ) $_SESSION[ 'Language' ] = 'fr';

// Récupère le code langue, quand celui-ci est précisé.
if ( array_key_exists( 'Lang', $_GET ) ) {
   $_SESSION[ 'Language' ] = $_GET[ 'Lang' ];
}   

$Script = $_SERVER[ 'SCRIPT_NAME' ];
$URI = $_SERVER[ 'REQUEST_URI' ];
$IP_Source = $_SERVER[ 'REMOTE_ADDR' ];

$Separateur_Preuve = '___';

// Force la connexion en HTTPS.
if ( ! array_key_exists( 'HTTPS', $_SERVER ) )
	header( 'Location: ' . URL_BASE . $URI );

// Charge les libellés en fonction de la langue sélectionnée.
include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_HBL_Generiques.inc.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_' . basename( $Script ) );

// Charge les classes utiles à cet écran.
include( DIR_LIBRAIRIES . '/Class_HTML.inc.php' );
include( DIR_LIBRAIRIES . '/Class_Actions_PDO.inc.php' );


// Crée une instance de l'objet HTML.
$PageHTML = new HTML();
$objActions = new Actions();


// Vérifie si la session de l'utilisateur n'a pas expiré.
if ( $PageHTML->validerTempsSession() ) {
	$PageHTML->sauverTempsSession();
} else {
	print( $PageHTML->construirePageAlerte( $L_Session_Expired, '/Loxense-Connexion.php' ) );
	exit();
}


// Récupère les droits de l'utilisateur sur ce script.
$Permissions = $PageHTML->permissionsGroupees( $Script );


// Vérifie que l'utilisateur est habilité en lecture sur ce script.
if ( $PageHTML->permission( $Script ) === false ) {
	include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_HBL_Autorisations.inc.php' );
	print( $PageHTML->construirePageAlerte( $L_No_Authorize_Script, '/Loxense-Principal.php' ) );
	exit();
}


// Identifie l'action à réaliser.
if ( array_key_exists( 'Action', $_GET ) ) {
	$Action = $_GET[ 'Action' ];
} else {
	$Action = '';
}


// Charge les javascripts relatifs aux droits de l'utilisateur.
$Droit_Lecture = FALSE;
$Droit_Ajouter = FALSE;
$Droit_Modifier = FALSE;
$Droit_Supprimer = FALSE;

$Path_Parts = pathinfo( $Script );
$Fichiers_JavaScript = [];

if ( $Permissions !== FALSE ) {
	foreach ($Permissions[ basename( $Script ) ]["rights"] as $Droit) {
		switch ($Droit) {
			case 'RGH_1':
				$Droit_Lecture = TRUE;
				$Fichiers_JavaScript[] = $Path_Parts[ 'filename' ] . '/Lecture.js';
				break;
			case 'RGH_2':
				$Droit_Ajouter = TRUE;
				$Fichiers_JavaScript[] = $Path_Parts[ 'filename' ] . '/Ajouter.js';

				break;
			case 'RGH_3':
				$Droit_Modifier = TRUE;
				$Fichiers_JavaScript[] = $Path_Parts[ 'filename' ] . '/Modifier.js';
				break;
			case 'RGH_4':
				$Droit_Supprimer = TRUE;
				$Fichiers_JavaScript[] = $Path_Parts[ 'filename' ] . '/Supprimer.js';
				break;
		}
	}
}


// Récupère les droits de l'utilisateur sur tous les scripts.
$Permissions = $PageHTML->permissionsGroupees();


// Définit le format des colonnes du tableau central.
$Format_Colonnes[ 'Prefixe' ] = 'ACT';

$Format_Colonnes[ 'Fonction_Ouverture' ] = 'ouvrirChamp';

$Format_Colonnes[ 'Id' ] = array( 'nom' => 'act_id' );

$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'mcr_libelle', 'titre' => $L_Mesure, 'taille' => '3',
	'triable' => 'oui', 'tri_actif' => 'oui', 'sens_tri' => 'mcr_libelle', 'modifiable' => 'non', 'affichage' => 'img' );

$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'spp_nom', 'titre' => $L_Actif_Support, 'taille' => '2',
	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'spp_nom', 'modifiable' => 'non' );

//$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'pea_cotation', 'titre' => $L_Sensibilite, 'taille' => '1',
//	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'pea_cotation', 'modifiable' => 'non' );

$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'act_libelle', 'titre' => $L_Action, 'taille' => '2',
	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'act_libelle', 'modifiable' => 'oui', 'type' => 'input', 'affichage' => 'img' );

$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'acteur', 'titre' => $L_Acteur, 'taille' => '1',
	'triable' => 'oui', 'sens_tri' => 'acteur', 'modifiable' => 'oui', 'type' => 'select', 'fonction' => 'listerUtilisateursMesure' );

$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'act_priorite', 'titre' => $L_Priorite, 'taille' => '1',
	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'act_priorite', 'modifiable' => 'oui', 'type' => 'input-number' );

$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'date_debut', 'titre' => $L_Date_Debut, 'taille' => '1',
	'triable' => 'oui', 'sens_tri' => 'date_debut', 'modifiable' => 'non' );

$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'date_fin', 'titre' => $L_Date_Fin, 'taille' => '1',
	'triable' => 'oui', 'sens_tri' => 'date_fin', 'modifiable' => 'non' );

$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'act_statut_libelle', 'titre' => $L_Status, 'taille' => '1',
	'triable' => 'oui', 'sens_tri' => 'act_statut_libelle', 'modifiable' => 'oui', 'affichage' => 'img', 'type' => 'select',
	'fonction' => 'listerStatutsAction' );

$Format_Colonnes[ 'Actions' ] = array( 'taille' => '12', 'affichage' => 'invisible-droit', //'titre' => $L_Actions,
	'boutons' => array( 'modifier' => $Droit_Modifier, 'supprimer' => $Droit_Supprimer ) );


// Exécute l'action identifie
switch( $Action ) {
 default:
	// Définit le titre de navigation.
	$Titres_Nav = $PageHTML->prepareTitreMenuAction( $Permissions, $Script );

//	$Bouton_Alternatif['class'] = 'btn-regenerer';
//	$Bouton_Alternatif['glyph'] = 'refresh';
//	$Bouton_Alternatif['libelle'] = $L_Regenerer_Risques;

	print( $PageHTML->construireEnteteHTML( $L_Gestion_Actions, $Fichiers_JavaScript ) .
	 $PageHTML->construireNavbar() .
	 $PageHTML->construireTitreEcran( $Titres_Nav, $Droit_Ajouter, 'cartographie' /*, $Bouton_Alternatif */ )
	);


	if ( $Droit_Lecture === TRUE ) {
//		$Bouton_Alternatif['class'] = '';
//		$Bouton_Alternatif['libelle'] = 'test';

		// Construit un tableau central vide.
		print( $PageHTML->contruireTableauVide( $Format_Colonnes ) );
	}

	print( $PageHTML->construireFooter( TRUE ) .
	   $PageHTML->construirePiedHTML() );

	break;


 /* ========================================================================
 ** Réponses aux appels AJAX
 */

 case 'AJAX_Trier':
	if ( $Droit_Lecture === TRUE ) {
		//include( DIR_LIBRAIRIES . '/Class_Actions_PDO.inc.php' );

		//$objActions = new Actions();

		$Trier = $_POST[ 'trier' ];

		if ( isset( $_POST['chercher']) ) {
			$Chercher = $_POST['chercher'];
		} else {
			$Chercher = '';
		}
		
		try {
			if ( ! isset( $_SESSION['CARTOGRAPHIE_SEL'] ) ) {
				echo json_encode( array(
					'statut' => 'warning',
					'texteMsg' => $L_Aucune_Cartographie,
					'avertissement' => $L_Warning
					) );

				exit();
			}

			$Listes = $objActions->listerActions( $_SESSION['CARTOGRAPHIE_SEL'], $Trier, $_SESSION['Language'], '', $Chercher );

			$Total = $objActions->totalActions();

			$Texte_HTML = '';

			$Total_Colonnes = count( $Format_Colonnes['Colonnes'] );

			$Action_X = 1;

			
			foreach ($Listes as $Occurrence) {
				if ( $Occurrence->mcr_libelle == '' ) $Occurrence->mcr_libelle = $Occurrence->mgr_libelle;
				$Occurrence->mcr_libelle .= ' [<strong class="purple_3">' . $Occurrence->pea_cotation . '</strong>]';

				$Occurrence->spp_nom = $Occurrence->spp_nom . ' [' . $Occurrence->tsp_libelle . ']';

				if ( $Occurrence->idn_id == NULL ) $Occurrence->acteur = '';
				else $Occurrence->acteur = $Occurrence->idn_login . ' - ' . $Occurrence->cvl_prenom . ' ' . $Occurrence->cvl_nom;

				if ( $Occurrence->act_date_debut_r != '' ) $Occurrence->date_debut = $Occurrence->act_date_debut_r;
				else $Occurrence->date_debut = $Occurrence->act_date_debut_p;

				if ( $Occurrence->act_date_fin_r != '' ) $Occurrence->date_fin = $Occurrence->act_date_fin_r;
				else $Occurrence->date_fin = $Occurrence->act_date_fin_p;

				if ( $Occurrence->act_libelle == '' or $Occurrence->act_libelle == NULL ) {
					$Format_Colonnes['Colonnes'][2]['modifiable'] = 'non';
					$Format_Colonnes['Colonnes'][3]['modifiable'] = 'non';
					$Format_Colonnes['Colonnes'][4]['modifiable'] = 'non';
					$Format_Colonnes['Colonnes'][7]['modifiable'] = 'non';
				} else {
					$Format_Colonnes['Colonnes'][2]['modifiable'] = 'oui';
					$Format_Colonnes['Colonnes'][3]['modifiable'] = 'oui';
					$Format_Colonnes['Colonnes'][4]['modifiable'] = 'oui';
					$Format_Colonnes['Colonnes'][7]['modifiable'] = 'oui';
				}

				if ( $Occurrence->act_id == NULL or  $Occurrence->act_id == '' ) {
					 $Occurrence->act_id = 'X_' . $Action_X;
					 $Action_X += 1;
				}

				$Texte_HTML .= $PageHTML->creerOccurrenceCorpsTableau( $Occurrence->act_id.'-MCR_'.$Occurrence->mcr_id, $Occurrence, $Format_Colonnes );
			}

			echo json_encode( array(
				'statut' => 'success',
				'texteHTML' => $Texte_HTML,
				'total' => $Total,
				'JourCourant' => date( 'Y-m-d' ),
				'droit_ajouter' => $Droit_Ajouter,
				'droit_modifier' => $Droit_Modifier,
				'droit_supprimer' => $Droit_Supprimer
				) );
		} catch( Exception $e ) {
			echo json_encode( array(
				'statut' => 'error',
				'texteMsg' => $e->getMessage()
				) );
		}
	}	
	break;


 case 'AJAX_Libeller':
	$Libelles = array(
		'statut' => 'success',

		'Titre_Ajouter' => $L_Action_Ajouter,
		'Titre_Modifier' => $L_Action_Modifier,
		'Titre_Supprimer' => $L_Action_Supprimer,

		'L_Fermer' => $L_Fermer,
		'L_Creer' => $L_Creer,
		'L_Ajouter' => $L_Ajouter,
		'L_Modifier' => $L_Modifier,
		'L_Supprimer' => $L_Supprimer,

		'L_Libelle' => $L_Libelle,

		'L_Mesure' => $L_Mesure,
		'L_Mesures' => $L_Mesures,
		'L_Actif_Support' => $L_Actif_Support,
		'L_Actions' => $L_Actions,
		'L_Acteur' => $L_Acteur,
		'L_Utilisateur' => $L_Utilisateur,
		'L_Gestionnaire' => $L_Gestionnaire,		
		'L_Priorite' => $L_Priorite,
		'L_Dates' => $L_Dates,
		'L_Date_Debut_p' => $L_Date_Debut_p,
		'L_Date_Debut_r' => $L_Date_Debut_r,
		'L_Date_Fin_p' => $L_Date_Fin_p,
		'L_Date_Fin_r' => $L_Date_Fin_r,
		'L_Statut' => $L_Status,
		'L_Description' => $L_Description,
		'L_Frequence' => $L_Frequence,
		'L_Civilite' => $L_Civilite,

		'L_Preuves' => $L_Preuves,
		'L_Charger' => $L_Charger,
		'L_Transferer_Fichier' => $L_Transferer_Fichier,
		'L_Preuve_Visualiser' => $L_Preuve_Visualiser,
		'L_Preuve_Telecharger' => $L_Preuve_Telecharger,
		'L_Preuve_Supprimer' => $L_Preuve_Supprimer,
		'L_Preuve_Transferer' => $L_Preuve_Transferer,

		'L_Action_Confirm_Suppression' => $L_Action_Confirm_Suppression,

		'L_ERR_Champs_Obligatoires' => $L_ERR_Champs_Obligatoires,
		'AnneeCourante' => date( 'Y' ),
		'JourCourant' => date( 'Y-m-d' ),
		'L_Format_Date' => $L_Format_Date,
		'Utilisateur' => $_SESSION['cvl_prenom'] . ' ' . $_SESSION['cvl_nom'],
		'is_super_admin' => $_SESSION['idn_super_admin'],
		'L_Fichier_Non_Autorise' => $L_Fichier_Non_Autorise,

		'L_Confirmer_Supprimer_Preuve' => $L_Confirmer_Supprimer_Preuve,
		'L_Oui' => $L_Yes,
		'L_Non' => $L_No
		);


	if ( isset( $_POST['Creer_Action'] ) ) {
		//include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_Loxense-ActifsPrimordiaux.php' );
		try {
			if ( $_POST['Creer_Action'] == 'N' ) {
				$Action = $objActions->listerActions( '*', 'mcr_libelle', $_SESSION[ 'Language' ], $_POST[ 'ActionID' ] );
				$Libelles['action'] = $Action[0];

				$Libelle = $Action[0]->idn_login . ' - ' . $Action[0]->cvl_prenom . ' ' . $Action[0]->cvl_nom;

				$ListeUtilisateurs = listerUtilisateursMesure( '_0-' . $_POST['MesureID'], $Libelle, $_SESSION[ 'Language' ] );

				$Libelles['liste_utilisateurs'] = $ListeUtilisateurs;
			}


			$Libelles['Liste_Mesures'] = '';

			foreach( $objActions->listerMesures( $_SESSION['CARTOGRAPHIE_SEL'], $_SESSION['Language']) as $Occurrence ) {
				if ( $Occurrence->mcr_libelle == '' ) $Occurrence->mcr_libelle = $Occurrence->mgr_libelle;

				$Occurrence->mcr_libelle .= ' [<strong class="purple_3">' . $Occurrence->pea_cotation . '</strong>] => <strong class="bg-purple_3" style="padding: 0 6px;">' . $Occurrence->spp_nom . '</strong>';

				$Mesure_Active = '';

				if ( $_POST['Creer_Action'] == 'N' ) {
					if ( $Occurrence->mcr_id == $_POST['MesureID'] ) {
						$Mesure_Active = 'checked';
					}
				}

				$Libelles['Liste_Mesures'] .= '<div class="form-check liste">' .
					' <input class="form-check-input" type="radio" name="mcr_id" id="mcr_id_' . $Occurrence->mcr_id . '" value="' . $Occurrence->mcr_id . '" ' . $Mesure_Active .
					'  data-spp_nom="' . $Occurrence->spp_nom . ' [' . $Occurrence->tsp_libelle . ']"  data-spp_id="' . $Occurrence->spp_id . '">' .
					' <label class="form-check-label" for="mcr_id_' . $Occurrence->mcr_id . '">' .
					$Occurrence->mcr_libelle .
					' </label>' .
					'</div>';
			}


			$Libelles['Liste_Statuts'] = '';

			foreach( $objActions->listerStatutsAction( $_SESSION['Language'] ) as $Occurrence ) {
				$Statut_Actif = '';

				if ( $_POST['Creer_Action'] == 'N' ) {
					if ( $Occurrence->lbr_code == $Libelles['action']->act_statut_code ) {
						$Statut_Actif = 'selected';
					}
				}

				$Libelles['Liste_Statuts'] .= '<option value="' . $Occurrence->lbr_code . '" ' . $Statut_Actif . '>' .
					$Occurrence->lbr_libelle . '</option>';
			}


			$Libelles['Liste_Frequences'] = '';

			foreach( $objActions->listerFrequencesAction( $_SESSION['Language'] ) as $Occurrence ) {
				$Frequence_Active = '';

				if ( $_POST['Creer_Action'] == 'N' ) {
					if ( $Occurrence->lbr_code == $Libelles['action']->act_frequence_code ) {
						$Frequence_Active = 'selected';
					}
				}

				$Libelles['Liste_Frequences'] .= '<option value="' . $Occurrence->lbr_code . '" ' . $Frequence_Active . '>' .
					$Occurrence->lbr_libelle . '</option>';
			}


			if ( $_POST['Creer_Action'] == 'N' ) {
				$Libelles['Liste_Preuves'] = '';
				$act_id = $_POST['ActionID'];
				$crs_id = $_SESSION['CARTOGRAPHIE_SEL'];

				foreach( $objActions->listerPreuves( $act_id ) as $Occurrence ) {
					$prv_localisation = explode($Separateur_Preuve, $Occurrence->prv_localisation)[1];
					$prv_id = $Occurrence->prv_id;

					$Libelles['Liste_Preuves'] .= '<div class="row liste" id="PRV_' . $prv_id . '">' .
						'<div class="col-lg-5 prv_libelle">' .
						$Occurrence->prv_libelle .
						'</div>' .
						'<div class="col-lg-5 prv_localisation" data-rep="' . URL_PREUVES . '">' .
						$prv_localisation .
						'</div>' .
						'<div class="col-lg-2">' .
						'<a class="btn btn-outline-secondary btn-sm btn-v-preuve" title="'.$L_Preuve_Visualiser.'" data-act_id="'.$act_id.'" data-crs_id="'.$crs_id.'" data-prv_id="'.$prv_id.'">' .
							'<i class="bi-eye-fill"></i></a>&nbsp;' .
						'<a class="btn btn-outline-secondary btn-sm btn-t-preuve" title="'.$L_Preuve_Telecharger.'" data-act_id="'.$act_id.'" data-crs_id="'.$crs_id.'" data-prv_id="'.$prv_id.'">' .
							'<i class="bi-download"></i></a>&nbsp;' .
						'<a class="btn btn-outline-secondary btn-sm btn-s-preuve" title="'.$L_Preuve_Supprimer.'" data-act_id="'.$act_id.'" data-crs_id="'.$crs_id.'" data-prv_id="'.$prv_id.'">' .
							'<i class="bi-x-circle"></i></a>' .
						'</div>' .
						'</div>';
				}
			}

		} catch (Exception $e) {
			$Message = $e->getMessage();

			if ( $e->getCode() == 23505 ) { // Gestion d'un doublon
				$Message = $L_ERR_DUPL_Action;
			}

			$Resultat = array(
				'statut' => 'error',
				'texteMsg' => $Message
				);

			print( json_encode( $Resultat ) );
			exit();
		}
	}


	if ( isset( $_POST['ActionID'] ) ) {
		//include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_Loxense-ActifsPrimordiaux.php' );
		try {
		} catch (Exception $e) {
			$Message = $e->getMessage();

			if ( $e->getCode() == 23505 ) { // Gestion d'un doublon
				$Message = $L_ERR_DUPL_Action;
			}

			$Resultat = array(
				'statut' => 'error',
				'texteMsg' => $Message
				);

			print( json_encode( $Resultat ) );
			exit();
		}
	}

	print( json_encode( $Libelles ) );
	
	exit();


 case 'AJAX_Ajouter':
	if ( $Droit_Ajouter === TRUE ) {
		if ( isset($_POST['mcr_id']) && isset($_POST['act_libelle'])
			&& isset($_POST['act_date_debut_p']) && isset($_POST['act_date_fin_p'])
			&& isset($_POST['act_frequence']) && isset($_POST['act_priorite']) && isset($_POST['act_statut']) ) {
			try {
				$ID_Nouvelle_Action = $objActions->insererAction( $_POST['mcr_id'], $_POST['act_libelle'], $_POST['act_statut'],
					$_POST['act_frequence'], $_POST['act_date_debut_p'], $_POST['act_date_fin_p'], $_POST['act_priorite'],
					$_SESSION['idn_id'], NULL, NULL, $_POST['act_description'], $_POST['act_date_debut_r'], $_POST['act_date_fin_r'] );

				$PageHTML->ecrireEvenement( 'ATP_GENERATION', 'OTP_ACTION',
					'mcr_id="'.$_POST['mcr_id'].'", act_libelle="'.$_POST['act_libelle'].'", act_statut="'.$_POST['act_statut'].'"' .
					', act_frequence="'.$_POST['act_frequence'].'", act_date_debut_p="'.$_POST['act_date_debut_p'].'", act_date_fin_p="'.
					$_POST['act_date_fin_p'], $_POST['act_priorite'], $_SESSION['idn_id'],
					NULL, NULL, $_POST['act_description'], $_POST['act_date_debut_r'], $_POST['act_date_fin_r'].'"', LOG_WARNING,
					$_SESSION['CARTOGRAPHIE_SEL'] );


				$Occurrence = $objActions->recupererLibellesMesure( $_POST['mcr_id'], $_SESSION['Language'] );

				$monAction = new stdClass();

				if ( $Occurrence->mcr_libelle == '' ) $Occurrence->mcr_libelle = $Occurrence->mgr_libelle;

				$monAction->mcr_libelle = $Occurrence->mcr_libelle.' ['.$Occurrence->pea_cotation.'] ['.$Occurrence->mcr_etat_libelle.']';

				$monAction->acteur = $_SESSION['cvl_nom'] . ' ' . $_SESSION['cvl_prenom'];

				$monAction->spp_nom = $Occurrence->spp_nom;

				$monAction->act_libelle = $_POST['act_libelle'];

				$monAction->act_priorite = $_POST['act_priorite'];

				$monAction->act_statut_libelle = $_POST['act_statut_libelle'];

				if ( $_POST['act_date_debut_r'] != '' ) $monAction->date_debut = $_POST['act_date_debut_r'];
				else $monAction->date_debut = $_POST['act_date_debut_p'];

				if ( $_POST['act_date_fin_r'] != '' ) $monAction->date_fin = $_POST['act_date_fin_r'];
				else $monAction->date_fin = $_POST['act_date_fin_p'];

				$Texte_HTML = $PageHTML->creerOccurrenceCorpsTableau( $ID_Nouvelle_Action.'-MCR_'.$_POST['mcr_id'], $monAction, $Format_Colonnes );


				$Resultat = array(
					'statut' => 'success',
					'texteMsg' => $L_Action_Creee,
					'texteHTML' => $Texte_HTML,
					'droit_ajouter' => $Droit_Ajouter,
					'droit_modifier' => $Droit_Modifier,
					'droit_supprimer' => $Droit_Supprimer
					);
			} catch (Exception $e) {
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Gestion d'un doublon
					$Message = $L_ERR_DUPL_Action;
				}

				$Resultat = array(
					'statut' => 'error',
					'texteMsg' => $Message
					);
			}
		} else {
			$Resultat = array(
				'statut' => 'error',
				'texteMsg' => $L_ERR_Champs_Obligatoires
				);
		}
	} else {
		$Resultat = array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
			);

		echo json_encode( $Resultat );

		exit();
	}

	print( json_encode( $Resultat ) );
	
	exit();


 case 'AJAX_Modifier_Champ':
	if ( $Droit_Ajouter === TRUE ) {
		if ( isset($_POST['id']) && isset($_POST['source'])	&& isset($_POST['valeur']) ) {
			try {
				if ($_POST['source'] == 'act_statut_libelle') $_POST['source'] = 'act_statut_code';

				if ($_POST['source'] == 'acteur') $_POST['source'] = 'idn_id';

				$objActions->modifierChamp(explode('-',$_POST['id'])[0], $_POST['source'], $_POST['valeur']);

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_APPRECIATION_RISQUE', $_POST[ 'source' ] . '="' . $_POST['valeur'] . '"' );

				$Resultat = array(
					'statut' => 'success',
					'texteMsg' => $L_Action_Modifiee
					);
			} catch (Exception $e) {
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Cas d'un doublon
					$Message = $L_ERR_DUPL_Action;
				}

				$Resultat = array(
					'statut' => 'error',
					'texteMsg' => $Message
					);
			}
			
			echo json_encode( $Resultat );
		}
	}
	break;


 case 'AJAX_Supprimer':
	if ( isset($_POST['act_id']) ) {
		try  {
			$objActions->supprimerAction( $_POST['act_id'] );

			$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_ACTION', 'act_id="' . $_POST['act_id'] . '", ' .
				'mcr_libelle="' . $_POST[ 'mcr_libelle' ] . '", ' .
				'spp_nom="' . $_POST[ 'spp_nom' ] . '" ' .
				'act_libelle="' . $_POST[ 'act_libelle' ] . '", ' .
				'acteur="' . $_POST[ 'acteur' ] . '", ' .
				'act_priorite="' . $_POST[ 'act_priorite' ] . '", ' .
				'date_debut="' . $_POST[ 'date_debut' ] . '", ' .
				'date_fin="' . $_POST[ 'date_fin' ] . '", ' .
				'act_statut_libelle="' . $_POST[ 'act_statut_libelle' ] . '"'
				);

			$Resultat = array( 'statut' => 'success',
				'titreMsg' => $L_Success,
				'texteMsg' => $L_Action_Supprimee
				);
		} catch (Exception $e) {
			$Resultat = array( 'statut' => 'error',
				'titreMsg' => $L_Error,
				'texteMsg' => $e->getMessage() );
		}

		echo json_encode( $Resultat );
	}
	break;


 case 'AJAX_Modifier':
	if ( $Droit_Modifier === TRUE ) {
		if ( isset($_POST['act_statut_code']) and isset($_POST['act_id']) and isset($_POST['act_priorite'])
			and isset($_POST['act_frequence_code']) and isset($_POST['mcr_id'])
			and isset($_POST['act_date_debut_p']) and isset($_POST['act_date_fin_p']) ) {
			try {
				// Contrôle les paramètres reçus.
				$_POST['idn_id'] = $PageHTML->controlerTypeValeur( $_POST['idn_id'], 'NUMERIC' );
				if ( $_POST['idn_id'] == -1 ) {
					echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (idn_id)'
					) );

					exit();
				}

				$_POST['act_id'] = $PageHTML->controlerTypeValeur( $_POST['act_id'], 'NUMERIC' );
				if ( $_POST['act_id'] == -1 ) {
					echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (act_id)'
					) );

					exit();
				}

				$_POST['mcr_id'] = $PageHTML->controlerTypeValeur( $_POST['mcr_id'], 'NUMERIC' );
				if ( $_POST['mcr_id'] == -1 ) {
					echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (mcr_id)'
					) );

					exit();
				}

				$_POST['act_priorite'] = $PageHTML->controlerTypeValeur( $_POST['act_priorite'], 'ASCII' );
				if ( $_POST['act_priorite'] == -1 ) {
					echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (act_priorite)'
					) );

					exit();
				}

				$_POST['act_frequence_code'] = $PageHTML->controlerTypeValeur( $_POST['act_frequence_code'], 'ASCII' );
				if ( $_POST['act_frequence_code'] == -1 ) {
					echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (act_frequence_code)'
					) );

					exit();
				}

				$_POST['act_statut_code'] = $PageHTML->controlerTypeValeur( $_POST['act_statut_code'], 'ASCII' );
				if ( $_POST['act_statut_code'] == -1 ) {
					echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (act_statut_code)'
					) );

					exit();
				}

				$_POST['act_date_debut_p'] = $PageHTML->controlerTypeValeur( $_POST['act_date_debut_p'], 'ASCII' );
				if ( $_POST['act_date_debut_p'] == -1 ) {
					echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (act_date_debut_p)'
					) );

					exit();
				}

				$_POST['act_date_fin_p'] = $PageHTML->controlerTypeValeur( $_POST['act_date_fin_p'], 'ASCII' );
				if ( $_POST['act_date_fin_p'] == -1 ) {
					echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (act_date_fin_p)'
					) );

					exit();
				}

				$_POST['act_date_debut_r'] = $PageHTML->controlerTypeValeur( $_POST['act_date_debut_r'], 'ASCII' );
				if ( $_POST['act_date_debut_r'] == -1 ) {
					echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (act_date_debut_r)'
					) );

					exit();
				}

				$_POST['act_date_fin_r'] = $PageHTML->controlerTypeValeur( $_POST['act_date_fin_r'], 'ASCII' );
				if ( $_POST['act_date_fin_r'] == -1 ) {
					echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (act_date_fin_r)'
					) );

					exit();
				}

				$_POST['act_description'] = $PageHTML->controlerTypeValeur( $_POST['act_description'], 'ASCII' );
				if ( $_POST['act_description'] == -1 ) {
					echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (act_description)'
					) );

					exit();
				}


				// Modifie le Risque.
				$objActions->modifierAction( $_POST['act_id'], $_POST['mcr_id'], $_POST['act_libelle'], $_POST['act_statut_code'],
					$_POST['act_frequence_code'],
					$_POST['act_date_debut_p'], $_POST['act_date_fin_p'], $_POST['act_priorite'], $_POST['idn_id'], 
					$_POST['act_description'], $_POST['act_date_debut_r'], $_POST['act_date_fin_r'] /*, $_POST['acg_id'] */ );

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_ACTION',
					'mcr_id = "' . $_POST['mcr_id'] . '", ' .
					'act_libelle="' . $_POST[ 'act_libelle' ] . '", ' .
					'act_statut_code="' . $_POST['act_statut_code'] . '", ' .
					'act_frequence_code="' . $_POST[ 'act_frequence_code' ] . '", ' .
					'act_date_debut_p="' . $_POST[ 'act_date_debut_p' ] . '", ' .
					'act_date_fin_p="' . $_POST['act_date_fin_p'] . '", ' .
					'act_date_debut_r="' . $_POST['act_date_debut_r'] . '", ' .
					'act_date_fin_r="' . $_POST['act_date_fin_r'] . '", ' .
					'act_priorite="' . $_POST['act_priorite'] . '", ' .
					'idn_id="' . $_POST['idn_id'] . '", ' .
					//'acg_id="' . $_POST['acg_id'] . '", ' .
					'act_description="' . $_POST['act_description'] . '"'
					);


				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Action_Modifiee
					);

			} catch (Exception $e) {
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Gestion d'un doublon
					$Message = $L_ERR_DUPL_Risque;
				}

				$Resultat = array(
					'statut' => 'error',
					'texteMsg' => $Message
					);
			}

			echo json_encode( $Resultat );
		} else {
			$Resultat = array(
				'statut' => 'error',
				'texteMsg' => $L_ERR_Champs_Obligatoires
				);

			echo json_encode( $Resultat );
			exit();
		}
	} else {
		$Resultat = array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
			);

		echo json_encode( $Resultat );
		exit();
	}
	break;


 case 'AJAX_Modifier_Cartographie':
	if ( $Droit_Lecture === TRUE ) {
		if ( isset($_POST['s_crs_id']) and isset($_POST['s_crs_id']) ) {
			$_POST['s_crs_id'] = $PageHTML->controlerTypeValeur( $_POST['s_crs_id'], 'NUMERIC' );
			if ( $_POST['s_crs_id'] === -1 ) {
				echo json_encode( array(
				'statut' => 'error',
				'texteMsg' => $L_Invalid_Value . ' (s_crs_id)'
				) );

				exit();
			}

			$_POST['s_ent_id'] = $PageHTML->controlerTypeValeur( $_POST['s_ent_id'], 'NUMERIC' );
			if ( $_POST['s_ent_id'] === -1 ) {
				echo json_encode( array(
				'statut' => 'error',
				'texteMsg' => $L_Invalid_Value . ' (s_ent_id)'
				) );

				exit();
			}


			$_SESSION['ENTITE_SEL'] = $_POST['s_ent_id'];
			$_SESSION['CARTOGRAPHIE_SEL'] = $_POST['s_crs_id'];


			$Resultat = array(
				'statut' => 'success'
				);

			echo json_encode( $Resultat );

			exit();
		} else {
			$Resultat = array(
				'statut' => 'error',
				'texteMsg' => $L_ERR_Champs_Obligatoires
				);

			echo json_encode( $Resultat );

			exit();
		}
	} else {
		$Resultat = array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
			);

		echo json_encode( $Resultat );

		exit();
	}

	break;


 case 'AJAX_Associer_Actifs_Primordiaux_EVR':
	if ( $Droit_Modifier === TRUE ) {
		if ( isset($_POST['evr_id']) ) {
			try {
				if ( isset( $_POST['liste_ajouter'] ) ) {
					foreach ( $_POST['liste_ajouter'] as $Id ) {
						$objActions->ajouterAssociationActifPrimordial( $_POST['evr_id'], $Id );
					}
				}

				if ( isset( $_POST['liste_supprimer'] ) ) {
					foreach ( $_POST['liste_supprimer'] as $Id ) {
						$objActions->supprimerAssociationActifPrimordial( $_POST['evr_id'], $Id );
					}
				}

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_EVENEMENT_REDOUTE', 'evr_id="' . $_POST[ 'evr_id' ] . '" <=> apr_id="' . $Id . '"' );

				$Resultat = array(
					'statut' => 'success',
					'texteMsg' => $L_Risque_Modifie
					);
			} catch (Exception $e) {
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Cas d'un doublon
					$Message = $L_ERR_DUPL_Risque;
				}

				$Resultat = array(
					'statut' => 'error',
					'texteMsg' => $Message
					);
			}
			
			echo json_encode( $Resultat );
		} else {
			$Resultat = array(
				'statut' => 'error',
				'texteMsg' => $L_ERR_Champs_Obligatoires
				);

			echo json_encode( $Resultat );
			exit();
		}
	} else {
		$Resultat = array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
			);

		echo json_encode( $Resultat );
		exit();
	}
	break;


 case 'AJAX_listerStatutsAction':
	$Resultat = array(
		'statut' => 'success',
		'texteMsg' => listerStatutsAction( $_POST['libelle'], $_SESSION['Language'] )
		);

	echo json_encode( $Resultat );

	break;


 case 'AJAX_listerUtilisateursMesure':
	$Resultat = array(
		'statut' => 'success',
		'texteMsg' => listerUtilisateursMesure( $_POST['id'], $_POST['libelle'], $_SESSION['Language'] )
		);

	echo json_encode( $Resultat );

	break;


 case 'AJAX_Charger_Preuve':
	if( $_FILES['fichier']['tmp_name'] != '' ) { // Vérifie si un nom de fichier est présent
		$tmp_file = $_FILES['fichier']['tmp_name'];


		if( !is_uploaded_file($tmp_file) ) {
			echo json_encode( array(
				'statut' => 'error',
				'texteMsg' => $L_Fichier_Temporaire_Introuvable
				) );

			exit();
		}

		if ( $_FILES['fichier']['size'] > return_bytes(ini_get('post_max_size')) ) {
			echo json_encode( array(
				'statut' => 'error',
				'texteMsg' => $L_Fichier_Trop_Gros . ' ('.return_bytes(ini_get('post_max_size')).' bytes)'
				) );

			exit();
		}

		// on vérifie maintenant l'extension
/*		$type_file = $_FILES['fichier']['type'];

		if( !strstr($type_file, 'jpg') && !strstr($type_file, 'jpeg') && !strstr($type_file, 'bmp') && !strstr($type_file, 'gif') ) {
			exit("Le fichier n'est pas une image");
		}
*/

		// on copie le fichier dans le dossier de destination
		$name_file = $_FILES['fichier']['name'];

		$prv_localisation = $_SESSION['CARTOGRAPHIE_SEL'] . $Separateur_Preuve . $name_file;
		$prv_libelle = $_GET['Libelle'];
		$act_id = $_GET['act_id'];


		if( preg_match('#[\x00-\x1F\x7F-\x9F/\\\\]#', $name_file) ) {
			$Resultat = array(
				'statut' => 'error',
				'texteMsg' => $L_Nom_Fichier_Non_Valide
				);
		}
		else if( ! move_uploaded_file( $tmp_file, DIR_PREUVES . DIRECTORY_SEPARATOR . $prv_localisation ) ) {
			$Resultat = array(
				'statut' => 'error',
				'texteMsg' => $L_Copy_Loxense_Impossible
				);
		}
		else {
			$Derniere_Preuve = $objActions->insererPreuve( $act_id, $prv_libelle, $prv_localisation );

			$Resultat = array(
				'statut' => 'success',
				'texteMsg' => $L_Fichier_Transfere,
				'prv_id' => $Derniere_Preuve,
				'prv_localisation' => $prv_localisation,
				'derniere_preuve' => $Derniere_Preuve,
				'crs_id' => $_SESSION['CARTOGRAPHIE_SEL'],
				'URL_PREUVES' => URL_PREUVES,
				'L_Preuve_Visualiser' => $L_Preuve_Visualiser,
				'L_Preuve_Telecharger' => $L_Preuve_Telecharger,
				'L_Preuve_Supprimer' => $L_Preuve_Supprimer
				);
		}
	}
	else {
		$Resultat = array(
			'statut' => 'error',
			'texteMsg' => $L_Pas_Fichier_A_Transferer
			);
	}

	echo json_encode( $Resultat );

	break;


 case 'AJAX_Supprimer_Preuve':
	if ( isset($_POST['prv_id']) ) {
		try  {
			$objActions->supprimerPreuve( $_POST['prv_id'] );

			$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_ACTION', 'prv_id="' . $_POST['prv_id'] . '", ' .
				'prv_localisation="' . $_POST[ 'prv_localisation' ] . '"'
				);

			@unlink( DIR_PREUVES . DIRECTORY_SEPARATOR . $_POST['crs_id'] . $Separateur_Preuve . $_POST['prv_localisation']);

			$Resultat = array( 'statut' => 'success',
				'titreMsg' => $L_Success,
				'texteMsg' => $L_Preuve_Supprimee
				);
		} catch (Exception $e) {
			$Resultat = array( 'statut' => 'error',
				'titreMsg' => $L_Error,
				'texteMsg' => $e->getMessage() );
		}

		echo json_encode( $Resultat );
	}
	break;


 case 'AJAX_Telecharger_Fichier':
	try {
		//$Statut = @$objActions->telechargerFichier( $_POST['nom_fichier'] );
		$Statut = @$objActions->telechargerFichier( $_GET['Nom_Fichier'] );

		if ( $Statut == TRUE ) {
			$Statut = 'success';
			$Message = $L_Preuve_Telechargee;
		} else {
			$Statut = 'error';
			$Message = $L_Preuve_Pb_Telechargement;
		}
	} catch ( Exception $e ) {
		$Statut = 'error';
		$Message = $L_Preuve_Pb_Telechargement;
	}

	$Resultat = array(
		'statut' => $Statut,
		'texteMsg' => $Message
		);
	echo json_encode( $Resultat );

	exit();

	break;
}


function listerStatutsAction( $Libelle, $Langue ) {
	include_once( DIR_LIBRAIRIES . '/Class_Actions_PDO.inc.php');

	$objActions = new Actions();

	$Liste_Statuts = '';

	foreach ( $objActions->listerStatutsAction( $Langue ) as $Occ ) {
		if ( $Occ->lbr_libelle == $Libelle ) $Selected = ' selected';
		else $Selected = '';

		$Liste_Statuts .= '<option value="' . $Occ->lbr_code . '"' . $Selected . '>' . $Occ->lbr_libelle . '</option>';
	}

	return $Liste_Statuts;
}


function listerUtilisateursMesure( $mcr_id, $Libelle, $Langue ) {
	include_once( DIR_LIBRAIRIES . '/Class_Actions_PDO.inc.php');

	$objActions = new Actions();

	$Liste_Statuts = '';

	if ( mb_strpos( explode('-',$mcr_id )[1], 'MCR_' ) === FALSE ) {
		$_ID = explode('-',$mcr_id )[1];
	} else {
		$_ID = explode('_',explode('-',$mcr_id )[1])[1];
	}

	$Liste = $objActions->listerUtilisateursMesure( $_ID );
	
	foreach ( $Liste as $Occ ) {
		if ( $Occ->idn_login == '' ) continue;

		if ( $Occ->idn_login . ' - ' . $Occ->cvl_prenom . ' ' . $Occ->cvl_nom == $Libelle ) $Selected = ' selected';
		else $Selected = '';

		$Liste_Statuts .= '<option value="' . $Occ->idn_id . '"' . $Selected . '>' . $Occ->idn_login . ' - ' . $Occ->cvl_prenom . ' ' . $Occ->cvl_nom . '</option>';
	}

	return $Liste_Statuts;
}

?>
