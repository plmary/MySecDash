<?php

/**
* Ce script gère les Gestionnaires.
*
* \license Copyleft Loxense
* \author Pierre-Luc MARY
* \package MySecDash
* \version 1.0
* \date 2015-11-20
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

// Force la connexion en HTTPS.
if ( ! array_key_exists( 'HTTPS', $_SERVER ) )
	header( 'Location: ' . URL_BASE . $URI );

// Charge les libellés en fonction de la langue sélectionnée.
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_Loxense-ActifsSupports.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_' . basename( $Script ) );

// Charge les classes utiles à cet écran.
include( DIR_LIBRAIRIES . '/Class_HTML.inc.php' );
include_once( DIR_LIBRAIRIES . '/Class_Gestionnaires_PDO.inc.php' );


// Instanciation des différents objets.
$PageHTML = new HTML();
$objGestionnaires = new Gestionnaires();

$Prefixe = 'GST';


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


// Récupère les droits de l'utilisateur sur tous les scripts.
$Permissions = $PageHTML->permissionsGroupees();


// Définit le format des colonnes du tableau central.
$Format_Colonnes[ 'Prefixe' ] = $Prefixe;
$Format_Colonnes[ 'Fonction_Ouverture' ] = 'ouvrirChamp';
$Format_Colonnes[ 'Id' ] = array( 'nom' => 'gst_id' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'gst_libelle', 'titre' => $L_Libelle, 'taille' => '6',
	'triable' => 'oui', 'tri_actif' => 'oui', 'sens_tri' => 'libelle',
	'type' => 'input', 'modifiable' => 'oui', 'maximum' => '100' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'total_tsp', 'titre' => couperLibelle( $L_Types_Actif_Support, 25 ), 'taille' => '2',
	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'total_tsp',
	'type' => 'input', 'modifiable' => 'non', 'affichage' => 'img' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'total_idn', 'titre' => couperLibelle( $L_Utilisateurs, 25 ), 'taille' => '2',
	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'total_idn',
	'type' => 'input', 'modifiable' => 'non', 'affichage' => 'img' );
$Format_Colonnes[ 'Actions' ] = array( 'taille' => '1', 'titre' => $L_Actions,
	'boutons' => array( 'modifier' => $Droit_Modifier, 'supprimer' => $Droit_Supprimer ) );


// Exécute l'action identifie
switch( $Action ) {
 default:
	// Définit le titre de navigation.
	$Titres_Nav = $PageHTML->prepareTitreMenuControleAcces( $Permissions, $Script );

	$Bouton_Alternatif['class'] = 'btn-rechercher';
	$Bouton_Alternatif['glyph'] = 'search';
	$Bouton_Alternatif['libelle'] = $L_Rechercher;
	
	print( $PageHTML->construireEnteteHTML( $L_Gestion_Gestionnaires, $Fichiers_JavaScript ) .
	 $PageHTML->construireNavbar() .
	 $PageHTML->construireTitreEcran( $Titres_Nav, $Droit_Ajouter, '', $Bouton_Alternatif ) //, 'langue' )
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

 case 'AJAX_Trier':
	if ( $Droit_Lecture === TRUE ) {
		$Trier = $_POST[ 'trier' ];

		try {
			if ( isset( $_POST['chercher']) ) {
				$Chercher = $_POST['chercher'];
			} else {
				$Chercher = '';
			}


			$Liste_Gestionnaires = $objGestionnaires->listerGestionnaires( $Trier, $Chercher );
			$Total = $objGestionnaires->RowCount;

			$Texte_HTML = '';

			if ( $Liste_Gestionnaires != '' ) {
				foreach ($Liste_Gestionnaires as $Occurrence) {
					$ID_Occurrence = $Occurrence->gst_id;

					$Occurrence->total_tsp = $PageHTML->construireCompteurListe( $Occurrence->total_tsp );
					$Occurrence->total_idn = $PageHTML->construireCompteurListe( $Occurrence->total_idn );

					$Texte_HTML .= $PageHTML->creerOccurrenceCorpsTableau( $ID_Occurrence, $Occurrence, $Format_Colonnes );
				}
			}

			echo json_encode( array(
				'statut' => 'success',
				'texteHTML' => $Texte_HTML,
				'total' => $Total,
				'droit_modifier' => $Droit_Modifier,
				'droit_supprimer' => $Droit_Supprimer,
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
	print( json_encode( array(
		'Statut' => 'success',
		'L_Fermer' => $L_Fermer,
		'Titre_Ajouter' => $L_Gestionnaire_Ajouter,
		'Titre_Supprimer' => $L_Gestionnaire_Supprimer,
		'Titre_Modifier' => $L_Gestionnaire_Modifier,
		'L_Ajouter' => $L_Ajouter,
		'L_Modifier' => $L_Modifier,
		'L_Supprimer' => $L_Supprimer,
		'L_Code' => $L_Code,
		'L_Langue' => $L_Langue,
		'L_Libelle' => $L_Libelle,
		'L_Langue_fr' => $L_Langue_fr,
		'L_Langue_en' => $L_Langue_en,
		'L_Types_Actif_Support' => $L_Types_Actif_Support,
		'L_Utilisateurs' => $L_Utilisateurs
		) ) );
	
	exit();


 case 'AJAX_Ajouter':
	if ( $Droit_Ajouter === TRUE ) {
		if ( isset($_POST['libelle']) ) {
			if ( $_POST['libelle'] == '' ) {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $L_Field_Mandatory );

				echo json_encode( $Resultat );
				exit();
			}

			try {
				$Id = $objGestionnaires->ajouterGestionnaire( $_POST['libelle'] );

				$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_GESTIONNAIRE', 'gst_id="' . $Id . '", ' .
					'gst_libelle="' . $_POST[ 'libelle' ] . '"' );


				if ( isset( $_POST['liste_TSP_a_ajouter'] ) ) {
					foreach( $_POST['liste_TSP_a_ajouter'] as $Type_Actif_Support ) {
						$objGestionnaires->ajouterTypeActifSupportAGestionnaire( $Type_Actif_Support, $Id );

						$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_GESTIONNAIRE', 'gst_id="' . $Id . '", ' .
							'tsp_id="' . $Type_Actif_Support . '"' );
					}
				}


				if ( isset( $_POST['liste_IDN_a_ajouter'] ) ) {
					foreach( $_POST['liste_IDN_a_ajouter'] as $Utilisateur ) {
						$objGestionnaires->ajouterUtilisateurAGestionnaire( $Utilisateur, $Id );

						$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_GESTIONNAIRE', 'gst_id="' . $Id . '", ' .
							'idn_id="' . $Utilisateur . '"' );
					}
				}

				$Donnees = $objGestionnaires->recupererGestionnaire( $Id );

				$Donnees->total_tsp = $PageHTML->construireCompteurListe( $Donnees->total_tsp );
				$Donnees->total_idn = $PageHTML->construireCompteurListe( $Donnees->total_idn );

				$Occurrence = $PageHTML->creerOccurrenceCorpsTableau( $Id, $Donnees, $Format_Colonnes );

				$Total = $objGestionnaires->totalGestionnaires();

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Gestionnaire_Ajoute,
					'texte' => $Occurrence,
					'id' => $Id,
					'total' => $Total,
					'libelle_limitation' => $L_Limitation_Licence,
					'droit_modifier' => $Droit_Modifier,
					'droit_supprimer' => $Droit_Supprimer
					);

			} catch (Exception $e) {
				$Statut = 'error';
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Gestion des doublons
					$Message = $L_ERR_DUPL_Gestionnaire;
				}

				if ( $e->getCode() == 10 ) { // Gestion des doublons
					$Message = $L_Code . ' => ' . $L_Only_Numeric_Characters;
				}

				$Resultat = array(
					'statut' => $Statut,
					'texteMsg' => $Message
					);
			}

			echo json_encode( $Resultat );
		}
	}
	break;


 case 'AJAX_Modifier':
	if ( $Droit_Modifier === TRUE ) {
		if ( isset($_POST['id']) && isset($_POST['libelle']) ){
			try {
				$objGestionnaires->modifierGestionnaire($_POST['id'], $_POST['libelle']);


				// Mise à jour des associations avec les Types de Supports.
				if ( array_key_exists( 'liste_TSP_a_ajouter', $_POST ) ) {
					foreach ( $_POST['liste_TSP_a_ajouter'] as $Occurrence ) {
						$objGestionnaires->ajouterTypeActifSupportAGestionnaire( $Occurrence, $_POST['id'] );

						$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_GESTIONNAIRE', 'gst_id="' . $_POST[ 'id' ] . '", ' .
							'tsp_id="' . $Occurrence . '"' );
					}
				}

				if ( array_key_exists( 'liste_TSP_a_supprimer', $_POST ) ) {
					foreach( $_POST['liste_TSP_a_supprimer'] as $Occurrence ) {
						$objGestionnaires->supprimerTypeActifSupportAGestionnaire( $Occurrence, $_POST['id'] );

						$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_GESTIONNAIRE', 'gst_id="' . $_POST[ 'id' ] . '", ' .
							'tsp_id="' . $Occurrence . '"' );
					}
				}


				// Mise à jour des associations avec les Utilisateurs.
				if ( array_key_exists( 'liste_IDN_a_ajouter', $_POST ) ) {
					foreach( $_POST['liste_IDN_a_ajouter'] as $Occurrence ) {
						$objGestionnaires->ajouterUtilisateurAGestionnaire( $Occurrence, $_POST['id'] );

						$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_GESTIONNAIRE', 'gst_id="' . $_POST[ 'id' ] . '", ' .
							'idn_id="' . $Occurrence . '"' );
					}
				}

				if ( array_key_exists( 'liste_IDN_a_supprimer', $_POST ) ) {
					foreach( $_POST['liste_IDN_a_supprimer'] as $Occurrence ) {
						$objGestionnaires->supprimerUtilisateurAGestionnaire( $Occurrence, $_POST['id'] );

						$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_GESTIONNAIRE', 'gst_id="' . $_POST[ 'id' ] . '", ' .
							'idn_id="' . $Occurrence . '"' );
					}
				}

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_GESTIONNAIRE', 'gst_id="' . $_POST[ 'id' ] . '", ' .
					'gst_libelle="' . $_POST[ 'libelle' ] . '"' );

				$Donnees = $objGestionnaires->recupererGestionnaire( $_POST['id'] );

				$Donnees->total_tsp = $PageHTML->construireCompteurListe( $Donnees->total_tsp );
				$Donnees->total_idn = $PageHTML->construireCompteurListe( $Donnees->total_idn );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Gestionnaire_Modifie,
					'total_tsp' => $Donnees->total_tsp,
					'total_idn' => $Donnees->total_idn
					);
			} catch (Exception $e) {
				$Message = $e->getMessage();
				$Statut = 'error';

				if ( $e->getCode() == 23505 ) { // Gestion des doublons
					$Message = $L_ERR_DUPL_Gestionnaire;
				}

				$Resultat = array(
					'statut' => $Statut,
					'texteMsg' => $Message
					);
			}
			
			echo json_encode( $Resultat );
		}
	}
	break;


 case 'AJAX_Supprimer':
	if ( isset($_POST['id']) ) {
		try  {
			$objGestionnaires->supprimerGestionnaire( $_POST['id'] );

			$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_GESTIONNAIRE', 'gst_id="' . $_POST['id'] . '"' );

			$Resultat = array( 'statut' => 'success',
				'titreMsg' => $L_Success,
				'texteMsg' => $L_Gestionnaire_Supprime,
				'libelle_limitation' => $L_Limitation_Licence
				);
		} catch (Exception $e) {
			$Resultat = array( 'statut' => 'error',
				'titreMsg' => $L_Error,
				'texteMsg' => $e->getMessage() );
		}

		echo json_encode( $Resultat );
	}
	break;


 case 'AJAX_Verifier_Associer':
	if ( isset($_POST['id']) ) {
		try { 
			$Compteurs = $objGestionnaires->isAssociatedGestionnaire( $_POST['id'] );

			$CodeHTML = '';

			if ( $Compteurs->total_tsp != 0
			 or $Compteurs->total_idn != 0 ) {
				$CodeHTML .= sprintf( $L_Gestionnaire_Confirm_Suppression_Associe, $_POST['libelle'] ) .
					'<ul style="margin-top: 10px;">';

				if ( $Compteurs->total_tsp != 0 ) {
					if ( $Compteurs->total_tsp > 1 ) $Libelle = $L_Types_Actif_Support;
					else $Libelle = $L_Type_Actif_Support;

					$CodeHTML .= '<li><span class="orange_moyen">' . $Compteurs->total_tsp . '</span> ' . $Libelle . '</li>';
				}

				if ( $Compteurs->total_idn != 0 ) {
					if ( $Compteurs->total_idn > 1 ) $Libelle = $L_Utilisateurs;
					else $Libelle = $L_Utilisateur;

					$CodeHTML .= '<li><span class="orange_moyen">' . $Compteurs->total_idn . '</span> ' . $Libelle . '</li>';
				}


				$CodeHTML .= '</ul>' . $L_Cascading_Delete;
			} else {
				$CodeHTML .= sprintf( $L_Gestionnaire_Confirm_Suppression, $_POST['libelle'] );
			}

			$Resultat = array( 'statut' => 'success',
				'texteMsg' => $CodeHTML );
		} catch( Exception $e ) {
			$Resultat = array( 'statut' => 'error',
				'texteMsg' => $e->getMessage() );
		}
	} else {
		$Resultat = array( 'statut' => 'error',
			'texteMsg' => $L_ERR_Champs_Obligatoires );
	}

	echo json_encode( $Resultat );

	break;


 case 'AJAX_Charger':
	if ( isset($_POST['tsp_id']) ) {
		try { 
			$Occurrence = $objGestionnaires->recupererGestionnaire( $_POST['tsp_id'] );

			$Resultat = array( 'statut' => 'success',
				'Code' => $Occurrence->tsp_code,
				'Langue' => $Occurrence->lng_id,
				'Libelle' => $Occurrence->lbr_libelle );
		} catch( Exception $e ) {
			$Resultat = array( 'statut' => 'error',
				'texteMsg' => $e->getMessage() );
		}
	} else {
		$Resultat = array( 'statut' => 'error',
			'texteMsg' => $L_ERR_Champs_Obligatoires );
	}

	echo json_encode( $Resultat );

	break;


 case 'AJAX_Modifier_Champ':
	if ( $Droit_Modifier === TRUE ) {
		if (isset($_POST['id']) && isset($_POST['source']) && isset($_POST['valeur'])) {
			try {
				$objGestionnaires->modifierChampGestionnaire($_POST['id'], $_POST['source'], $_POST['valeur']);

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_GESTIONNAIRE', 'gst_id="' . $_POST[ 'id'] . '", ' .
					$_POST[ 'source' ] . '="' . $_POST[ 'valeur' ] . '"' );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Gestionnaire_Modifie,
					);

				if ( $_POST['source'] == 'lng_id' ) $Resultat['langue'] = ${'L_Langue_'.$_POST['valeur']};
			} catch (Exception $e) {
				$Message = $e->getMessage();
				$Statut = 'error';

				if ( $e->getCode() == 23505 ) { // Gestion des doublons
					$Message = $L_ERR_DUPL_Gestionnaire;
				}

				if ( $e->getCode() == 10 ) { // Ce champ n'accepte que des numériques
					$Message = $L_Only_Numeric_Characters;
				}

				$Resultat = array(
					'statut' => $Statut,
					'texteMsg' => $Message
					);
			}
		} else {
			$Resultat = array( 'statut' => 'error',
				'texteMsg' => $L_ERR_Champs_Obligatoires );
		}

		echo json_encode( $Resultat );
	}

	break;


 case 'AJAX_Changer_Langue':
	$_SESSION['langue_libelle'] = $_POST['langue'];

	break;


 case 'AJAX_Lister_Types_Supports':
	try {
		include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_Loxense-TypesActifSupport.php' );

		$Liste = $objGestionnaires->listerTypesSupportsParGestionnaire( $_POST['id'], TRUE, $_SESSION['Language'] );

		$CodeHTML_1 = '';

		foreach ($Liste as $Occurrence) {
			if ( $_POST['id'] != '' ) {
				if ( $Occurrence->gst_id != '' ) {
					$Ancienne_Valeur = 1;
					$Valeur = ' checked';
				} else {
					$Ancienne_Valeur = 0;
					$Valeur = '';
				}
			} else {
				$Ancienne_Valeur = 0;
				$Valeur = '';
			}

			$CodeHTML_1 .= '<div class="form-check liste">' .
				'<input class="form-check-input" id="TSP_' . $Occurrence->tsp_id . '" type="checkbox" data-old="' . $Ancienne_Valeur . '"' . $Valeur . '>' .
				'<label class="form-check-label" for="TSP_' . $Occurrence->tsp_id . '">' .
				$Occurrence->tsp_libelle .
				'</label>' .
				'</div>';
		}

		$CodeHTML_2 = '';
/*			'<div class="row">' .
				'<div class="col-lg-1 bg-gris-normal">&nbsp;</div>' .
				'<div class="col-lg-2 bg-gris-normal"><strong>' . $L_Libelle . '</strong></div>' .
				'</div>';
		}*/

		$Resultat = array(
			'statut' => 'success',
			'texteHTML' => $CodeHTML_1,
			'titreHTML' => $CodeHTML_2
			);
	} catch ( Exception $e ) {
		$Resultat = array(
			'statut' => 'error',
			'texteMsg' => $e->getMessage()
			);
	}

	echo json_encode( $Resultat );

	break;


 case 'AJAX_Lister_Utilisateurs':
	try {
		$Liste = $objGestionnaires->listerUtilisateursParGestionnaire( $_POST['id'], TRUE );

		$CodeHTML = '';

		foreach ($Liste as $Occurrence) {
			if ( $_POST['id'] != '' ) {
				if ( $Occurrence->gst_id != '' ) {
					$Ancienne_Valeur = 1;
					$Valeur = ' checked';
				} else {
					$Ancienne_Valeur = 0;
					$Valeur = '';
				}
			} else {
				$Ancienne_Valeur = 0;
				$Valeur = '';
			}

			$CodeHTML .= '<div class="form-check liste">' .
				'<input class="form-check-input" id="IDN_' . $Occurrence->idn_id . '" type="checkbox" data-old="' . $Ancienne_Valeur . '"' . $Valeur . '>' .
				'<label class="form-check-label" for="IDN_' . $Occurrence->idn_id . '">' .
				$Occurrence->idn_login . ' (' . $Occurrence->cvl_prenom . ' ' . $Occurrence->cvl_nom . ')' .
				'</label>' .
				'</div>';
		}

		$Resultat = array(
			'statut' => 'success',
			'texteHTML' => $CodeHTML
			);
	} catch ( Exception $e ) {
		$Resultat = array(
			'statut' => 'error',
			'texteMsg' => $e->getMessage()
			);
	}

	echo json_encode( $Resultat );

	break;
}


function couperLibelle( $Libelle, $Limite = 33 ) {
	$Taille = mb_strlen( $Libelle );

	if ( $Taille > $Limite ) {
		$Texte = mb_substr( $Libelle, 0, $Limite );
		$Texte = '<span title="' . $Libelle . '">' . $Texte . '&hellip;</span>';
	} else {
		$Texte = $Libelle;
	}

	return $Texte;
}

?>