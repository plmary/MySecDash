<?php

/**
* Ce script gère les Libellés du Référentiel Interne.
*
* \license Copyleft Loxense
* \author Pierre-Luc MARY
* \package MySecDash
* \version 1.0
* \date 2025-07-16
*/

// Charge les constantes du projet.
include 'Constants.inc.php';

include DIR_LIBRAIRIES . '/Loxense-Entete-Standard.php';


// Charge les libellés en fonction de la langue sélectionnée.
include DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php';


if ( ! isset( $_SESSION['s_lng_id'] ) ) {
	$_SESSION['s_lng_id'] = $_SESSION['Language'];
}

// Crée une instance de l'objet HTML.
$objLibelles = new LibellesReferentiel();


// Définit le format des colonnes du tableau central.
$Format_Colonnes[ 'Prefixe' ] = 'LBR';
$Format_Colonnes[ 'Fonction_Ouverture' ] = 'ouvrirChamp';
$Format_Colonnes[ 'Id' ] = array( 'nom' => 'lbr_id' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'lbr_code', 'titre' => $L_Code, 'taille' => '4',
	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'lbr_code', 'type' => 'input', 'modifiable' => 'non' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'lng_id', 'titre' => $L_Langue, 'taille' => '1',
	'triable' => 'oui', 'sens_tri' => 'lng_id', 'type' => 'select', 'fonction' => 'listerLangues', 'modifiable' => 'non' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'lbr_libelle', 'titre' => $L_Libelle, 'taille' => '5',
	'triable' => 'oui', 'sens_tri' => 'lbr_libelle', 'type' => 'input', 'modifiable' => 'oui' );
$Format_Colonnes[ 'Actions' ] = array( 'taille' => '2', 'titre' => $L_Actions,
	'boutons' => array( 'modifier' => $Droit_Modifier, 'supprimer' => $Droit_Supprimer ) );


// Exécute l'action identifie
switch( $Action ) {
 default:
	if ( $Droit_Ajouter === true ) {
		$Boutons_Alternatifs[] = ['class'=>'btn-ajouter', 'libelle'=>$L_Ajouter, 'glyph'=>'plus'];
	}
	$Boutons_Alternatifs[] = ['class'=>'btn-rechercher', 'libelle'=>$L_Rechercher, 'glyph'=>'search'];


	$Choix_Langues['id'] = 's_lng_id';
	$Choix_Langues['libelle'] = $L_Langue;

	$Liste_Langues = $PageHTML->recupererLangues();

	if ( $Liste_Langues != '' ) {
		$Choix_Langues['options'][] = array('id' => '*', 'nom' => $L_Toutes);

		foreach( $Liste_Langues as $Langue ) {
			$Choix_Langues['options'][] = array('id' => $Langue->lng_id, 'nom' => $Langue->lng_libelle);
		}
	}

	$Titre_Ecran = $PageHTML->getLibelle('__LRI_GESTION_LIBELLES_REFERENTIEL', $_SESSION['Language']);

	print $PageHTML->construireEnteteHTML( $Titre_Ecran, $Fichiers_JavaScript ) .
		$PageHTML->construireNavbarJson('Logo-MySecDash.svg', 'nav-items.json') .
		$PageHTML->construireTitreEcran( $Titre_Ecran, '', $Boutons_Alternatifs, '', '', $Choix_Langues );


	if ( $Droit_Lecture === true ) {
		// Construit un tableau central vide.
		print $PageHTML->contruireTableauVide( $Format_Colonnes );
	}

	print $PageHTML->construireFooter( true ) .
		$PageHTML->construirePiedHTML();

	break;


 /* ========================================================================
 ** Réponses aux appels AJAX
 */

 case 'AJAX_Libeller':
	$Libelles = array( 'Statut' => 'success',
		'L_Titre_Ajouter' => $PageHTML->getLibelle('__LRI_CREATION_LIBELLES_REFERENTIEL'),
		'L_Titre_Modifier' => $PageHTML->getLibelle('__LRI_MODIFICATION_LIBELLES_REFERENTIEL'),
		'L_Titre_Supprimer' => $PageHTML->getLibelle('__LRI_SUPPRESSION_LIBELLES_REFERENTIEL'),
		'L_Ajouter' => $PageHTML->getLibelle('__LRI_SYS_AJOUTER'),
		'L_Modifier' => $PageHTML->getLibelle('__LRI_SYS_MODIFIER'),
		'L_Supprimer' => $PageHTML->getLibelle('__LRI_SYS_SUPPRIMER'),
		'L_Fermer' => $PageHTML->getLibelle('__LRI_SYS_FERMER'),
		'L_Libelles_Referentiel' => $PageHTML->getLibelle('__LRI_LIBELLES_REFERENTIEL'),
		'L_Code' => $PageHTML->getLibelle('__LRI_SYS_CODE'),
		'L_Langue' => $PageHTML->getLibelle('__LRI_SYS_LANGUE'),
		'L_Libelle' => $PageHTML->getLibelle('__LRI_SYS_LIBELLE'),
		'L_Supprime_Tous_Libelles_Code' => $PageHTML->getLibelle('__LRI_SUPPRIME_TOUS_LIBELLES_CODE'),
		'Liste_Langues' => $PageHTML->recupererLangues()
		);

	if ( $Droit_Lecture === true ) {
		if ( isset($_POST['ain_id']) && $_POST['ain_id'] != '') {
			$Libelles['Application'] = $Applications->detaillerApplication( $_POST['ain_id'] );
		}
	}

	if ( $Droit_Modifier === true ) {
		if ( isset($_POST['lbr_code']) && $_POST['lbr_code'] != '') {
			$Libelles['Libelles'] = $PageHTML->recupererLibellesReferentiel( $_POST['lbr_code'] );
		}
	}

	print json_encode( $Libelles );

	exit();


 case 'AJAX_Trier':
	if ( $Droit_Lecture === true ) {
		$Trier = $_POST[ 'trier' ];

		try {
			$ListeLibelles = $objLibelles->recupererSimpleLibellesReferentiel( '*', $_SESSION['s_lng_id'], 'E', $Trier );
			$Total = $objLibelles->RowCount;

			$Texte_HTML = '';

			foreach ($ListeLibelles as $Occurrence) {
				$Texte_HTML .= $PageHTML->creerOccurrenceCorpsTableau( $Occurrence->lbr_id, $Occurrence, $Format_Colonnes );
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
			'texteMsg' => $PageHTML->getLibelle( '__LRI_ERR_SYS_PAS_LES_DROITS' )
		);

		echo json_encode( $Resultat );
		exit();
	}
	break;


 case 'AJAX_Ajouter':
	if ( $Droit_Ajouter === true ) {
		if ( isset($_POST['code']) && isset($_POST['libelles']) ) {
			try {
				$Resultat = $objLibelles->ajouterLibellesReferentiel( $_POST['code'], $_POST['libelles'] );
				$Occurrences = '';

				foreach( $Resultat as $Cle => $Occurrence ) {
					$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_LIBELLE_INTERNE', 'lbr_id="' . $Cle . '", lbr_code="' . $Occurrence[0] . '", ' .
						'lng_id="' . $Occurrence[1] . '", lbr_libelle="' . $Occurrence[2] );
	
					$Valeurs = new stdClass();
					$Valeurs->lbr_code = $_POST['code'];
					$Valeurs->lng_id = $Occurrence[1];
					$Valeurs->lbr_libelle = $Occurrence[2];
	
					$Occurrences .= $PageHTML->creerOccurrenceCorpsTableau( $Cle, $Valeurs, $Format_Colonnes );
				}

				$Resultat = array(
					'statut' => 'success',
					'texteMsg' => $PageHTML->getLibelle('__LRI_LIBELLE_REFERENTIEL_CREE', $_SESSION['Language']),
					'codeHTML' => $Occurrences,
					'droit_modifier' => $Droit_Modifier,
					'droit_supprimer' => $Droit_Supprimer
					);

			} catch (Exception $e) {
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Gestion d'un doublon
					$Message = $L_ERR_DUPL_Application;
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
			'texteMsg' => $PageHTML->getLibelle( '__LRI_ERR_SYS_PAS_LES_DROITS' )
			);

		echo json_encode( $Resultat );
		exit();
	}
	break;


 case 'AJAX_Modifier_Champ':
	if ( $Droit_Modifier === true ) {
		if ( isset($_POST['id']) && isset($_POST['source']) && isset($_POST['valeur']) ) {
			try {
				$objLibelles->majLibelleReferentielParChamp($_POST['id'], $_POST['source'], $_POST['valeur']);

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_LIBELLE_INTERNE', $_POST[ 'source' ] . ' = "' . $_POST['valeur'] . '"' );

				$Resultat = array(
					'statut' => 'success',
					'texteMsg' => $PageHTML->getLibelle('__LRI_LIBELLE_REFERENTIEL_MODIFIE')
					);
			} catch (Exception $e) {
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Cas d'un doublon
					$Message = $PageHTML->getLibelle('__LRI_ERR_SYS_DEJA_EXISTANT');
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
				'texteMsg' => $PageHTML->getLibelle( '__LRI_ERR_SYS_CHAMPS_OBLIGATOIRES' )
			);

			echo json_encode( $Resultat );
			exit();
		}
	} else {
		$Resultat = array(
			'statut' => 'error',
			'texteMsg' => $PageHTML->getLibelle( '__LRI_ERR_SYS_PAS_LES_DROITS' )
		);
		
		echo json_encode( $Resultat );
		exit();
	}
	break;


 case 'AJAX_Supprimer':
	if ( $Droit_Supprimer === true ) {
		if ( isset($_POST['lbr_code']) ) {
			try  {
				$objLibelles->supprimerLibelleReferentielParCode( $_POST['lbr_code'] );
	
				$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_LIBELLE_INTERNE',
					'lbr_code="' . $_POST[ 'lbr_code' ] . '", lng_id="*" ' );
	
				$Resultat = array( 'statut' => 'success',
					'titreMsg' => $PageHTML->getLibelle( '__LRI_ERR_SYS_SUCCES' ),
					'texteMsg' => $PageHTML->getLibelle( '__LRI_LIBELLE_REFERENTIEL_SUPPRIME' )
					);
			} catch (Exception $e) {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $PageHTML->getLibelle( '__LRI_ERR_SYS_ERREUR' ),
					'texteMsg' => $e->getMessage() );
			}
	
			echo json_encode( $Resultat );
		}
	} else {
		$Resultat = array(
			'statut' => 'error',
			'texteMsg' => $PageHTML->getLibelle( '__LRI_ERR_SYS_PAS_LES_DROITS' )
		);
		
		echo json_encode( $Resultat );
		exit();
	}
	break;


 case 'AJAX_Modifier':
	if ( $Droit_Modifier === true ) {
		if ( isset($_POST['ain_id']) && isset($_POST['ain_libelle']) && isset($_POST['tap_id']) && isset($_POST['ain_localisation']) ) {
			try {
				$Applications->majApplication( $_POST['ain_id'], $_POST['ain_libelle'], $_POST['ain_localisation'], $_POST['tap_id'] );

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_LIBELLE_INTERNE', 'ain_id="' . $_POST['ain_id'] . '", ' .
					'ain_libelle="' . $_POST[ 'ain_libelle' ] . '", tap_id="' . $_POST[ 'tap_id' ] . '", ' .
					'ain_localisation="' . $_POST[ 'ain_localisation' ] . '" ' );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Application_Modifiee
					);

			} catch (Exception $e) {
				$Statut = 'error';
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) {
					$Message = $L_ERR_DUPL_Application;
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
				'texteMsg' => $PageHTML->getLibelle( '__LRI_ERR_SYS_CHAMPS_OBLIGATOIRES' )
				);

			echo json_encode( $Resultat );
			exit();
		}
	} else {
		$Resultat = array(
			'statut' => 'error',
			'texteMsg' => $PageHTML->getLibelle( '__LRI_ERR_SYS_PAS_LES_DROITS' )
			);

		echo json_encode( $Resultat );
		exit();
	}
	break;


 case 'AJAX_Selectioner_Langue':
	if ( isset($_POST['lng_id']) ) {
		$_SESSION['s_lng_id'] = $_POST['lng_id'];

		$Resultat = array( 'statut' => 'success',
			'texteMsg' => $L_Entite_Change,
			'lng_id' => $_SESSION['s_lng_id']
		);
	} else {
		$Resultat = array( 'statut' => 'error',
			'texteMsg' => $PageHTML->getLibelle( '__LRI_ERR_SYS_CHAMPS_OBLIGATOIRES' ) . ' (lng_id)' );
	}

	echo json_encode( $Resultat );

	break;


 case 'AJAX_listerLangues':
	if ( $Droit_Lecture === TRUE ) {
		$Resultat = array(
			'statut' => 'success',
			'texteMsg' => listerLangues( $_POST['id'], $_POST['libelle'] )
		);

		echo json_encode( $Resultat );
		exit();
	} else {
		$Resultat = array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		);

		echo json_encode( $Resultat );
		exit();
	}

}



function listerLangues( $Init_Id = '', $Init_Libelle = '' ) {
	include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );

	$PageHTML = new HTML();
	$Liste = $PageHTML->recupererLangues();

	$Code_HTML = '';

	foreach ($Liste as $Occurrence) {
		if ( $Init_Id != '' ) {
			if ( $Init_Id == $Occurrence->lng_id || $Init_Libelle == $Occurrence->lng_id ) {
				$Selected = ' selected';
			} else {
				$Selected = '';
			}
		} else {
			$Selected = '';
		}

		$Code_HTML .= '<option value="' . $Occurrence->lng_id . '"' . $Selected . '>' . $Occurrence->lng_id . '</option>' ;
	}

	return $Code_HTML;
}

?>