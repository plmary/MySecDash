<?php

/**
* Ce script gère les Etiquettes (tags).
*
* \license Copyleft Loxense
* \author Pierre-Luc MARY
* \package MySecDash
* \version 1.0
* \date 2024-01-09
*/

// Charge les constantes du projet.
include( 'Constants.inc.php' );

include( DIR_LIBRAIRIES . '/Loxense-Entete-Standard.php' );

// Charge les libellés en fonction de la langue sélectionnée.
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_' . basename( $Script ) );

// Charge les classes utiles à cet écran.
include_once( DIR_LIBRAIRIES . '/Class_Etiquettes_PDO.inc.php' );

$objEtiquettes = new Etiquettes();

$Prefixe = 'TGS';


// Définit le format des colonnes du tableau central.
$Format_Colonnes[ 'Prefixe' ] = $Prefixe;
$Format_Colonnes[ 'Fonction_Ouverture' ] = 'ouvrirChamp';
$Format_Colonnes[ 'Id' ] = array( 'nom' => 'tgs_id' );

$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'tgs_code', 'titre' => $L_Code, 'taille' => '2',
	'triable' => 'oui', 'tri_actif' => 'oui', 'sens_tri' => 'code', 'maximum' => 10,
	'type' => 'input', 'modifiable' => 'oui', 'maximum' => '10', 'casse' => 'majuscule' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'tgs_libelle', 'titre' => $L_Libelle, 'taille' => '4',
	'triable' => 'oui', 'tri_actif' => 'oui', 'sens_tri' => 'libelle',
	'type' => 'input', 'modifiable' => 'oui', 'maximum' => '60' );
/*$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'total_apr', 'titre' => couperLibelle( $L_Actifs_Primordiaux, 25 ), 'taille' => '2',
	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'total_apr',
	'type' => 'input', 'modifiable' => 'non', 'affichage' => 'img' );*/
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'total_idn', 'titre' => couperLibelle( $L_Utilisateurs, 25 ), 'taille' => '2',
	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'total_idn',
	'type' => 'input', 'modifiable' => 'non', 'affichage' => 'img' );
$Format_Colonnes[ 'Actions' ] = array( 'taille' => '2', 'titre' => $L_Actions,
	'boutons' => array( 'modifier' => $Droit_Modifier, 'supprimer' => $Droit_Supprimer ) );


// Exécute l'action identifie
switch( $Action ) {
 default:
	if ( $Droit_Ajouter === TRUE ) {
		$Boutons_Alternatifs[] = ['class'=>'btn-ajouter', 'libelle'=>$L_Ajouter, 'glyph'=>'plus'];
	}
	$Boutons_Alternatifs[] = ['class'=>'btn-rechercher', 'libelle'=>$L_Rechercher, 'glyph'=>'search'];

	print( $PageHTML->construireEnteteHTML( $L_Gestion_Etiquettes, $Fichiers_JavaScript ) .
		$PageHTML->construireNavbarJson('Logo-MySecDash.svg', 'nav-items.json') .
		$PageHTML->construireTitreEcran( $L_Gestion_Etiquettes, '', $Boutons_Alternatifs )
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


			$Liste_Etiquettes = $objEtiquettes->listerEtiquettes( $Trier, '', $Chercher );
			$Total = $objEtiquettes->RowCount;

			$Texte_HTML = '';

			if ( $Liste_Etiquettes != '' ) {
				foreach ($Liste_Etiquettes as $Occurrence) {
					$ID_Occurrence = $Occurrence->tgs_id;

					$Occurrence->total_idn = $PageHTML->construireCompteurListe( $Occurrence->total_idn ); //$objEtiquettes->totalAssociationUtilisateurs( $Occurrence->tgs_id ) );
//					$Occurrence->total_apr = $PageHTML->construireCompteurListe( $Occurrence->total_apr ); //$objEtiquettes->totalAssociationUtilisateurs( $Occurrence->tgs_id ) );

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
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		) );
	}	
	break;


 case 'AJAX_Libeller':
	$Libelles = array(
		'Statut' => 'success',
		'L_Fermer' => $L_Fermer,
		'Titre_Ajouter' => $L_Etiquette_Ajouter,
		'Titre_Supprimer' => $L_Etiquette_Supprimer,
		'Titre_Modifier' => $L_Etiquette_Modifier,
		'L_Ajouter' => $L_Ajouter,
		'L_Modifier' => $L_Modifier,
		'L_Supprimer' => $L_Supprimer,
		'L_Code' => $L_Code,
		'L_Langue' => $L_Langue,
		'L_Libelle' => $L_Libelle,
		'L_Langue_fr' => $L_Langue_fr,
		'L_Langue_en' => $L_Langue_en,
		'L_Types_Actif_Support' => $L_Types_Actif_Support,
		'L_Utilisateurs' => $L_Utilisateurs,
		'L_Description' => $L_Description
		);

	if ( isset( $_POST['action'] ) ) {
		if ( $_POST['action'] == 'M' ) {
			$Info = $objEtiquettes->listerEtiquettes( '', $_POST[ 'tgs_id' ] );

			$Libelles['Info'] = $Info[0];
		}
	}

	print( json_encode( $Libelles ) );

	exit();


 case 'AJAX_Ajouter':
	if ( $Droit_Ajouter === TRUE ) {
		if ( isset($_POST['code']) AND isset($_POST['libelle']) ) {
			if ( $_POST['code'] == '' ) {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $L_Field_Mandatory + ' (code)' );

				echo json_encode( $Resultat );
				exit();
			}

			if ( $_POST['libelle'] == '' ) {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $L_Field_Mandatory + ' (libelle)' );

				echo json_encode( $Resultat );
				exit();
			}

			$_POST['code'] = mb_strtoupper( $_POST['code'] );

			if ( isset( $_POST['description'] ) ) {
				$Description = $_POST['description'];
			} else {
				$Description = '';
			}

			try {
				$objEtiquettes->ajouterEtiquette( $_POST['code'], $_POST['libelle'], $Description );
				$Id = $objEtiquettes->LastInsertId;

				$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_TAG', 'tgs_id="' . $Id . '", ' .
					'tgs_code="' . $_POST['code'] . '", tgs_libelle="' . $_POST[ 'libelle' ] . '"' );


				if ( isset( $_POST['liste_IDN_a_ajouter'] ) ) {
					foreach( $_POST['liste_IDN_a_ajouter'] as $idn_id ) {
						$objEtiquettes->ajouterAssociationUtilisateur( $Id, $idn_id );

						$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_TAG', 'tgs_id="' . $Id . '", ' .
							'idn_id="' . $idn_id . '"' );
					}
				}

				$Donnees = new stdClass();
				$Donnees->tgs_code = $_POST['code'];
				$Donnees->tgs_libelle = $_POST['libelle'];

				$Total_IDN = $objEtiquettes->totalAssociationUtilisateurs( $Id );
				$Donnees->total_idn = $PageHTML->construireCompteurListe( $Total_IDN );
				$Donnees->total_apr = $PageHTML->construireCompteurListe( 0 );
				
				$Occurrence = $PageHTML->creerOccurrenceCorpsTableau( $Id, $Donnees, $Format_Colonnes );

				$Total = $objEtiquettes->totalEtiquettes();

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Etiquette_Ajoute,
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
					$Message = $L_ERR_DUPL_Etiquette;
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
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		) );
	}
	break;


 case 'AJAX_Modifier':
	if ( $Droit_Modifier === TRUE ) {
		if ( isset($_POST['id']) && isset($_POST['libelle']) ){
			try {
				$_POST['code'] = mb_strtoupper( $_POST['code'] );

				$objEtiquettes->modifierEtiquette( $_POST['id'], $_POST['code'], $_POST['libelle'] );

				// Mise à jour des associations avec les Utilisateurs.
				if ( array_key_exists( 'liste_IDN_a_ajouter', $_POST ) ) {
					foreach( $_POST['liste_IDN_a_ajouter'] as $Occurrence ) {
						$objEtiquettes->ajouterAssociationUtilisateur( $_POST['id'], $Occurrence );

						$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_TAG', 'tgs_id="' . $_POST[ 'id' ] . '", ' .
							'idn_id="' . $Occurrence . '"' );
					}
				}

				if ( array_key_exists( 'liste_IDN_a_supprimer', $_POST ) ) {
					foreach( $_POST['liste_IDN_a_supprimer'] as $Occurrence ) {
						$objEtiquettes->supprimerAssociationUtilisateur( $_POST['id'], $Occurrence );

						$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_TAG', 'tgs_id="' . $_POST[ 'id' ] . '", ' .
							'idn_id="' . $Occurrence . '"' );
					}
				}

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_TAG', 'tgs_id="' . $_POST[ 'id' ] . '", ' .
					'tgs_code="' . $_POST[ 'code' ] . '", tgs_libelle="' . $_POST[ 'libelle' ] . '"' );

				$Donnees = new stdClass();
				$Donnees->tgs_code = $_POST['code'];
				$Donnees->tgs_libelle = $_POST['libelle'];

				$Total_IDN = $objEtiquettes->totalAssociationUtilisateurs( $_POST['id'] );
				$Donnees->total_idn = $PageHTML->construireCompteurListe( $Total_IDN );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Etiquette_Modifie,
					'total_idn' => $Donnees->total_idn
					);
			} catch (Exception $e) {
				$Message = $e->getMessage();
				$Statut = 'error';

				if ( $e->getCode() == 23505 ) { // Gestion des doublons
					$Message = $L_ERR_DUPL_Etiquette;
				}

				$Resultat = array(
					'statut' => $Statut,
					'texteMsg' => $Message
					);
			}
			
			echo json_encode( $Resultat );
		}
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		) );
	}
	break;


 case 'AJAX_Supprimer':
	if ( $Droit_Supprimer === TRUE ) {
		if ( isset($_POST['id']) ) {
			try  {
				$objEtiquettes->supprimerEtiquette( $_POST['id'] );
	
				$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_TAG', 'gst_id="' . $_POST['id'] . '"' );
	
				$Resultat = array( 'statut' => 'success',
					'titreMsg' => $L_Success,
					'texteMsg' => $L_Etiquette_Supprime,
					'libelle_limitation' => $L_Limitation_Licence
					);
			} catch (Exception $e) {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $e->getMessage() );
			}
	
			echo json_encode( $Resultat );
		}
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		) );
	}
	break;


 case 'AJAX_Verifier_Associer':
	if ( isset($_POST['id']) ) {
		try { 
			$Compteurs = $objEtiquettes->etiquetteEstAssociee( $_POST['id'] );

			$CodeHTML = '';

			if ( /*$Compteurs->total_apr != 0
			 or*/ $Compteurs->total_idn != 0 ) {
				$CodeHTML .= sprintf( $L_Etiquette_Confirm_Suppression_Associe, $_POST['code'] . ' - ' . $_POST['libelle'] ) .
					'<ul style="margin-top: 10px;">';

/*				if ( $Compteurs->total_apr != 0 ) {
					if ( $Compteurs->total_apr > 1 ) $Libelle = $L_Actifs_Primordiaux;
					else $Libelle = $L_Actif_Primordial;

					$CodeHTML .= '<li><span class="orange_moyen">' . $Compteurs->total_apr . '</span> ' . $Libelle . '</li>';
				}*/

				if ( $Compteurs->total_idn != 0 ) {
					if ( $Compteurs->total_idn > 1 ) $Libelle = $L_Utilisateurs;
					else $Libelle = $L_Utilisateur;

					$CodeHTML .= '<li><span class="orange_moyen">' . $Compteurs->total_idn . '</span> ' . $Libelle . '</li>';
				}


				$CodeHTML .= '</ul>' . $L_Cascading_Delete;
			} else {
				$CodeHTML .= sprintf( $L_Etiquette_Confirm_Suppression, $_POST['code'] . ' - ' . $_POST['libelle'] );
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
	if ( $Droit_Lecture === TRUE ) {
		if ( isset($_POST['tsp_id']) ) {
			try { 
				$Occurrence = $objEtiquettes->recupererEtiquette( $_POST['tsp_id'] );
	
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
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		) );
	}
	break;


 case 'AJAX_Modifier_Champ':
	if ( $Droit_Modifier === TRUE ) {
		if (isset($_POST['id']) && isset($_POST['source']) && isset($_POST['valeur'])) {
			try {
				$objEtiquettes->modifierChamp($_POST['id'], $_POST['source'], $_POST['valeur']);

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_TAG', 'gst_id="' . $_POST[ 'id'] . '", ' .
					$_POST[ 'source' ] . '="' . $_POST[ 'valeur' ] . '"' );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Etiquette_Modifie,
					);

				if ( $_POST['source'] == 'lng_id' ) $Resultat['langue'] = ${'L_Langue_'.$_POST['valeur']};
			} catch (Exception $e) {
				$Message = $e->getMessage();
				$Statut = 'error';

				if ( $e->getCode() == 23505 ) { // Gestion des doublons
					$Message = $L_ERR_DUPL_Etiquette;
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
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		) );
	}

	break;


 case 'AJAX_Changer_Langue':
	$_SESSION['langue_libelle'] = $_POST['langue'];

	break;


 case 'AJAX_Lister_Utilisateurs':
	if ( $Droit_Lecture === TRUE ) {
		try {
			$Liste = $objEtiquettes->listerIdentitesAssocieesEtiquettes( $_POST['id'], TRUE );
	
			$CodeHTML = '';
	
			foreach ($Liste as $Occurrence) {
				if ( $Occurrence->tgs_id != '' ) {
					$Ancienne_Valeur = 1;
					$Valeur = ' checked';
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
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		) );
	}
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