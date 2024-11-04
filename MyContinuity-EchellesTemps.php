<?php

/**
* Ce script gère les Echelles de Temps.
*
* \license Copyleft Loxense
* \author Pierre-Luc MARY
* \package MyContinuity
* \version 1.0
* \date 2024-02-21
*/

// Charge les constantes du projet.
include( 'Constants.inc.php' );

include( DIR_LIBRAIRIES . '/Loxense-Entete-Standard.php' );


// Charge les libellés en fonction de la langue sélectionnée.
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );
include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_HBL_Generiques.inc.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_' . basename( $Script ) );

// Charge les classes utiles à cet écran.
include( DIR_LIBRAIRIES . '/Class_Campagnes_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_HBL_Societes_PDO.inc.php' );
include_once( DIR_LIBRAIRIES . '/Class_EchellesTemps_PDO.inc.php' );

// Crée une instance de l'objet HTML.
$objCampagnes = new Campagnes();
$objSocietes = new HBL_Societes();
$objEchellesTemps = new EchellesTemps();

// Définit le format des colonnes du tableau central.
$Format_Colonnes[ 'Prefixe' ] = 'ETE';
$Format_Colonnes[ 'Fonction_Ouverture' ] = 'ouvrirChamp';
$Format_Colonnes[ 'Id' ] = array( 'nom' => 'ete_id' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'ete_poids', 'titre' => $L_Poids, 'taille' => '2',
	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'ete_poids', 'type' => 'input', 'modifiable' => 'non' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'ete_nom_code', 'titre' => $L_Nom, 'taille' => '8',
	'triable' => 'oui', 'tri_actif' => 'oui', 'sens_tri' => 'ete_nom_code', 'type' => 'input', 'modifiable' => 'oui', );
$Format_Colonnes[ 'Actions' ] = array( 'taille' => '2', 'titre' => $L_Actions,
	'boutons' => array( 'modifier' => $Droit_Modifier, 'supprimer' => $Droit_Supprimer ) );


// Exécute l'action identifie
switch( $Action ) {
 default:
	if ( $_SESSION['idn_super_admin'] === TRUE ) {
		$Liste_Societes = $objSocietes->rechercherSocietes();
		if ( isset($_SESSION['s_sct_id']) ) {
			$sct_id = $_SESSION['s_sct_id'];
		} else {
			$_SESSION['s_sct_id'] = $sct_id = $Liste_Societes[0]->sct_id;
		}

		$Liste_Campagnes = $objCampagnes->rechercherCampagnes($sct_id, 'cmp_date-desc');
		if ($Liste_Campagnes != '' && $Liste_Campagnes != []) {
			$_SESSION['s_cmp_id'] = $Liste_Campagnes[0]->cmp_id;
		}
	} else {
		$Liste_Societes = $objSocietes->rechercherSocietes('', '', $_SESSION['idn_id']);
		if ( isset($_SESSION['s_sct_id']) ) {
			$sct_id = $_SESSION['s_sct_id'];
		} else {
			$sct_id = $Liste_Societes[0]->sct_id;
		}

		$Liste_Campagnes = $objCampagnes->rechercherCampagnes($sct_id, 'cmp_date-desc');
		if ( ! isset($_SESSION['s_cmp_id']) ) {
			$_SESSION['s_cmp_id'] = $Liste_Campagnes[0]->cmp_id;
		}
	}

	$Choix_Campagnes['id'] = 's_cmp_id';
	$Choix_Campagnes['libelle'] = $L_Campagnes;

	foreach( $Liste_Campagnes AS $Campagne ) {
		$Choix_Campagnes['options'][] = array('id' => $Campagne->cmp_id, 'nom' => $Campagne->cmp_date );
	}

	if ( $Droit_Ajouter === TRUE ) {
		$Boutons_Alternatifs[] = ['class'=>'btn-ajouter', 'libelle'=>$L_Ajouter, 'glyph'=>'plus'];
		if ( $Droit_Supprimer === TRUE ) {
			$Boutons_Alternatifs[] = ['class'=>'btn-initialiser', 'libelle'=>$L_Initialiser, 'glyph'=>'magic'];
		}
	}
	$Boutons_Alternatifs[] = ['class'=>'btn-rechercher', 'libelle'=>$L_Rechercher, 'glyph'=>'search'];

	print( $PageHTML->construireEnteteHTML( $L_Gestion_Echelles_Temps, $Fichiers_JavaScript ) .
		$PageHTML->construireNavbarJson('Logo-MyContinuity.svg', 'nav-items.json') .
		$PageHTML->construireTitreEcran( $L_Gestion_Echelles_Temps, $Liste_Societes, $Boutons_Alternatifs, $Choix_Campagnes )
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
	$Libelles = array( 'Statut' => 'success',
		'L_Fermer' => $L_Fermer,
		'L_Titre_Ajouter' => $L_Ajouter_Echelle_Temps,
		'L_Titre_Modifier' => $L_Modifier_Echelle_Temps,
		'L_Titre_Supprimer' => $L_Supprimer_Echelle_Temps,
		'L_Ajouter' => $L_Ajouter,
		'L_Modifier' => $L_Modify,
		'L_Supprimer' => $L_Delete,
		'L_Nom_Echelle_Temps' => $L_Nom_Echelle_Temps,
		'L_Poids' => $L_Poids,
		'L_Libelle' => $L_Label,
		'L_Description' => $L_Description
		);

	if ( $Droit_Lecture === TRUE ) {
		if ( isset($_POST['ete_id']) and $_POST['ete_id'] != '') {
			$EchelleTemps = $objEchellesTemps->rechercherEchellesTemps( $_SESSION['s_cmp_id'], '', $_POST['ete_id'] );
			$Libelles['EchelleTemps'] = $EchelleTemps[0];
		}
	}

	print( json_encode( $Libelles ) );
		
	exit();


 case 'AJAX_Ajouter':
	if ( $Droit_Ajouter === TRUE ) {
		if ( isset($_POST['ete_poids']) && isset($_POST['ete_nom_code']) ) {

			$_POST['ete_poids'] = $PageHTML->controlerTypeValeur( $_POST['ete_poids'], 'NUMBER' );
			if ( $_POST['ete_poids'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (ete_poids)'
				) );
				
				exit();
			}

			$_POST['ete_nom_code'] = $PageHTML->controlerTypeValeur( $_POST['ete_nom_code'], 'ASCII' );
			if ( $_POST['ete_nom_code'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (ete_nom_code)'
				) );
				
				exit();
			}

			try {
				$objEchellesTemps->majEchelleTemps( '', $_POST['ete_poids'], $_POST['ete_nom_code'] );

				$Id_EchelleTemps = $objEchellesTemps->LastInsertId;

				$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_ECHELLE_TEMPS', 'ete_id="' . $Id_EchelleTemps .
					'", ete_poids="' . $_POST[ 'ete_poids' ] .
					'", ete_nom_code="' . $_POST['ete_nom_code'] . '"' );

				$Valeurs = new stdClass();
				$Valeurs->ete_poids = $_POST[ 'ete_poids' ];
				$Valeurs->ete_nom_code = $_POST['ete_nom_code'];

				$Occurrence = $PageHTML->creerOccurrenceCorpsTableau( $Id_EchelleTemps, $Valeurs, $Format_Colonnes );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Echelle_Temps_Cree,
					'texte' => $Occurrence,
					'id' => $Id_EchelleTemps,
					'droit_modifier' => $Droit_Modifier,
					'droit_supprimer' => $Droit_Supprimer
					);

			} catch (Exception $e) {
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Gestion d'un doublon
					$Message = $L_ERR_DUPL_Echelle_Temps;
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


 case 'AJAX_Modifier_Champ':
	if ( $Droit_Modifier === TRUE ) {
		if ( isset($_POST['id']) && isset($_POST['source']) && isset($_POST['valeur']) ) {
			$_POST['id'] = $PageHTML->controlerTypeValeur( $_POST['id'], 'NUMBER' );
			if ( $_POST['id'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (' . $_POST['id'] . ')'
				) );
				
				exit();
			}

			$_POST['source'] = $PageHTML->controlerTypeValeur( $_POST['source'], 'ASCII' );
			if ( $_POST['source'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (' . $_POST['source'] . ')'
				) );
				
				exit();
			}

			$_POST['valeur'] = $PageHTML->controlerTypeValeur( $_POST['valeur'], 'ASCII' );
			if ( $_POST['valeur'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (' . $_POST['valeur'] . ')'
				) );
				
				exit();
			}

			try {
				$objEchellesTemps->majEchelleTempsParChamp($_POST['id'], $_POST['source'], $_POST['valeur']);

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_ECHELLE_TEMPS', $_POST[ 'source' ] . ' = "' . $_POST['valeur'] . '"' );

				$Resultat = array(
					'statut' => 'success',
					'texteMsg' => $L_Echelle_Temps_Modifiee
					);
			} catch (Exception $e) {
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Cas d'un doublon
					$Message = $L_ERR_DUPL_Echelle_Temps;
				}

				$Resultat = array(
					'statut' => 'error',
					'texteMsg' => $Message
					);
			}
			
			echo json_encode( $Resultat );
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


 case 'AJAX_Supprimer':
 	if ( $Droit_Supprimer === TRUE ) {
		if ( isset($_POST['id']) ) {
			try  {
				$objEchellesTemps->supprimerEchelleTemps( $_POST['id'] );
	
				$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_ECHELLE_TEMPS', 'ete_id="' . $_POST['id'] . '", ' .
					'libelle="' . $_POST[ 'libelle' ] . '"' );
	
				$Resultat = array( 'statut' => 'success',
					'titreMsg' => $L_Success,
					'texteMsg' => $L_Echelle_Temps_Supprimee
					);
			} catch (Exception $e) {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $e->getMessage() );
			}
	
			echo json_encode( $Resultat );
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


 case 'AJAX_Trier':
	if ( $Droit_Lecture === TRUE ) {
		$Trier = $_POST[ 'trier' ];
		
		try {
			$Texte_HTML = '';
			if ( isset( $_SESSION['s_cmp_id'] ) && $_SESSION['s_cmp_id'] != '' ) {
				$ListeEchellesTemps = $objEchellesTemps->rechercherEchellesTemps( $_SESSION['s_cmp_id'], $Trier );
				$Total = $objEchellesTemps->RowCount;

				foreach ($ListeEchellesTemps as $Occurrence) {
					$Texte_HTML .= $PageHTML->creerOccurrenceCorpsTableau( $Occurrence->ete_id, $Occurrence, $Format_Colonnes );
				}
			} else {
				$Texte_HTML .= '<div class="row justify-content-md-center mt-2"><div class="col col-lg-8"><h2 class="text-center">' . $L_Societe_Sans_Campagne . '</h2></div></div>' .
					'<div class="row justify-content-md-center mb-2"><div class="col col-lg-4 text-center"><a href="' . URL_BASE . '/MyContinuity-Campagnes.php" class="btn btn-primary btn-gerer-campagnes">' . $L_Gestion_Campagnes . '</a></div></div>';
				$Total = 0;
			}

			echo json_encode( array(
				'statut' => 'success',
				'texteHTML' => $Texte_HTML,
				'total' => $Total,
				'droit_modifier' => $Droit_Modifier,
				'droit_supprimer' => $Droit_Supprimer
				) );
		} catch( Exception $e ) {
			echo json_encode( array(
				'statut' => 'error',
				'texteMsg' => $e->getMessage()
				) );
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


 case 'AJAX_Charger':
 	if ( $Droit_Lecture === TRUE ) {
		try {
			$EchelleTemps = $objEchellesTemps->rechercherEchellesTemps( $_SESSION['s_cmp_id'], '', $_POST['ete_id'] );
	
			echo json_encode( array(
				'statut' => 'success',
				'ete_poids' => $EchelleTemps->ete_poids,
				'ete_nom_code' => $EchelleTemps->ete_nom_code
				) );
		} catch( Exception $e ) {
			echo json_encode( array(
				'statut' => 'error',
				'texteMsg' => $e->getMessage()
				) );		
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


 case 'AJAX_Modifier':
	if ( $Droit_Modifier === TRUE ) {
		if ( isset($_POST['ete_id']) and isset($_POST['ete_nom_code']) and isset($_POST['ete_poids']) ) {
			try {
				$objEchellesTemps->majEchelleTemps( $_POST['ete_id'], $_POST['ete_poids'], $_POST['ete_nom_code'] );

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_ECHELLE_TEMPS', 'ete_id="' . $_POST['ete_id'] . '", ' .
					'ete_poids="' . $_POST[ 'ete_poids' ] . '", ete_nom_code="' . $_POST[ 'ete_nom_code' ] . '"' );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Echelle_Temps_Modifiee
					);

			} catch (Exception $e) {
				$Statut = 'error';
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) {
					$Message = $L_ERR_DUPL_Echelle_Temps;
				}

				$Resultat = array(
					'statut' => $Statut,
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


 case 'AJAX_Verifier_Associer':
 	if ( $Droit_Lecture === TRUE ) {
		if ( isset($_POST['id']) ) {
			try { 
				$Compteurs = $objEchellesTemps->controlerAssociationEchelleTemps( $_POST['id'] );
	
				$CodeHTML = '';
	
				if ( $Compteurs->total_app > 0 || $Compteurs->total_frn > 0 || $Compteurs->total_act > 0 ) {
					$CodeHTML .= sprintf( $L_Confirmer_Suppression_Echelle_Temps_Associee, $_POST['libelle'] ) .
						'<ul style="margin-top: 10px;">';
				}
	
				if ( $Compteurs->total_app ) {
					if ( $Compteurs->total_app > 1 ) $Libelle = $L_Applications;
					else $Libelle = $L_Application;
	
					$CodeHTML .= '<li><span class="orange_moyen">' . $Compteurs->total_app . '</span> ' . $Libelle . '</li>';
				} elseif ( $Compteurs->total_frn ) {
					if ( $Compteurs->total_frn > 1 ) $Libelle = $L_Fournisseurs;
					else $Libelle = $L_Fournisseur;
					
					$CodeHTML .= '<li><span class="orange_moyen">' . $Compteurs->total_frn . '</span> ' . $Libelle . '</li>';
				} elseif ( $Compteurs->total_act ) {
					if ( $Compteurs->total_act > 1 ) $Libelle = $L_Activites;
					else $Libelle = $L_Activite;
					
					$CodeHTML .= '<li><span class="orange_moyen">' . $Compteurs->total_act . '</span> ' . $Libelle . '</li>';
				} else {
					$CodeHTML .= sprintf( $L_Confirmer_Suppression_Echelle_Temps, $_POST['libelle'] );
				}
	
				if ( $Compteurs->total_app > 0 || $Compteurs->total_frn > 0 || $Compteurs->total_act > 0 ) {
					$CodeHTML .= '</ul>';
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
	} else {
		$Resultat = array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		);
		
		echo json_encode( $Resultat );
		exit();
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


 case 'AJAX_Verifier_Avant_Initialisation':
	if ( $Droit_Lecture === TRUE && isset($_SESSION['s_cmp_id']) && $_SESSION['s_cmp_id'] != '' ) {
		$Resultat = $objEchellesTemps->controlerSiCampagneAEchelleTemps( $_SESSION['s_cmp_id'] );
		
		if ( $Resultat[0] === TRUE ) {
			$Message = sprintf( $L_Confirmer_Reinitialiser_Echelle_Temps, $Resultat[1] );
			$Titre = $L_Reinitialiser_Echelle_Temps_Campagne;
			$Bouton = $L_Reinitialiser;
		} elseif ( $Resultat[0] === FALSE ) {
			$Message = sprintf( $L_Confirmer_Initialiser_Echelle_Temps_Defaut, $Resultat[1] );
			$Titre = $L_Initialiser_Echelle_Temps_Campagne;
			$Bouton = $L_Initialiser;
		} else {
			$Message = $Resultat[1];
		}

		$Resultat = array( 'statut' => 'success',
			'texteMsg' => $Message,
			'L_Titre' => $Titre,
			'L_Bouton' => $Bouton,
			'L_Fermer' => $L_Fermer
		);
		
		echo json_encode( $Resultat );
	} else {
		$Resultat = array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize,
			'L_Titre' => $L_Error,
			'L_Bouton' => '',
			'L_Fermer' => $L_Fermer
		);
		
		echo json_encode( $Resultat );
		exit();
	}
	
	break;
	
	
 case 'AJAX_Initialiser_Echelle_Temps':
	if ( $Droit_Ajouter === TRUE && $Droit_Supprimer === TRUE ) {
		try {
			$objEchellesTemps->initialiserEchelleTempsDefautACampagne( $_SESSION['s_cmp_id'] );

			$Resultat = array(
				'statut' => 'success',
				'texteMsg' => $L_Echelle_Temps_Cree
			);
		} catch (Exception $e) {
			$Resultat = array(
				'statut' => 'error',
				'texteMsg' => $e->getMessage()
				);
		}
		
		echo json_encode( $Resultat );
	} else {
		$Resultat = array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		);
		
		echo json_encode( $Resultat );
		exit();
	}
	
	break;
}



function actualiseSocieteCampagne($objSocietes, $objCampagnes, $forcer=0) {
	/**
	 * Actualise les listes Sociétés et Campagnes à l'entrée dans l'écran et en cas de changement.
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
			$_SESSION['s_ent_id'] = '';
			
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
			$_SESSION['s_ent_id'] = '';
			
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
		$_SESSION['s_cmp_id'] = '';
		$_SESSION['s_ent_id'] = '';
		
		throw new Exception($L_Pas_Campagne_Pour_Societe, 0);
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

	return [$Liste_Societes, $Liste_Campagnes];
}

?>