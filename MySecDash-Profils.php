<?php

/**
* Ce script gère les Profils de MySecDash.
*
* \license Copyleft Loxense
* \author Pierre-Luc MARY
* \package MySecDash
* \version 1.0
* \date 2015-10-15
*/

// Charge les constantes du projet.
include( 'Constants.inc.php' );

// Charge les informations et droits de base d'un écran
include( DIR_LIBRAIRIES . '/Loxense-Entete-Standard.php' );

// Charge les libellés en fonction de la langue sélectionnée.
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_' . basename( $Script ) );

// Charge les classes utiles à cet écran.
include_once( DIR_LIBRAIRIES . '/Class_HBL_ApplicationsInternes_PDO.inc.php' );
include_once( DIR_LIBRAIRIES . '/Class_HBL_Profils_PDO.inc.php' );
include_once( DIR_LIBRAIRIES . '/Class_HBL_Societes_PDO.inc.php' );


$objApplications = new HBL_ApplicationsInternes();
$objProfils = new HBL_Profils();
$objProfiles_Access_Control = new HBL_Profils_Controles_Acces();
$objSocietes = new HBL_Societes();



// Exécute l'action identifie
switch( $Action ) {
 default:
	if ( $Droit_Ajouter === TRUE ) {
		$Boutons_Alternatifs = [
			['class'=>'btn-ajouter', 'libelle'=>$L_Ajouter_Profil, 'glyph'=>'plus']
		];
	} else {
		$Boutons_Alternatifs = '';
	}
	
	print( $PageHTML->construireEnteteHTML( $L_Gestion_Profils, $Fichiers_JavaScript ) .
	$PageHTML->construireNavbarJson('Logo-MySecDash.svg', 'nav-items.json') .
		$PageHTML->construireTitreEcran( $L_Gestion_Profils, '', $Boutons_Alternatifs )
	);


	if ( $Droit_Lecture === TRUE ) {
		// Construit le tableau central.
		print(
			"<div id=\"entete_tableau\" class=\"container-fluid\">\n" .
			" <div class=\"row\">\n" .
			"  <div class=\"col-lg-3\">&nbsp;</div>\n" .
			"  <div class=\"col-lg-9 titre\">" . $L_Profils . "</div>\n" .
			" </div> <!-- Fin : row -->\n" .
			" <div class=\"row profils\">\n" .
			"  <div class=\"col-lg-3 titre\">" . $L_Applications . "</div>\n" .
			" </div> <!-- Fin : row -->\n" .
			"</div> <!-- Fin : entete_tableau -->\n" .
			"<div id=\"corps_tableau\" class=\"container-fluid\">\n" .
			"</div> <!-- Fin : corps_tableau -->\n"
			);
	}

	print( $PageHTML->construireFooter( TRUE ) .
		$PageHTML->construirePiedHTML() );

	break;


 /* ========================================================================
 ** Réponses aux appels AJAX
 */

 case 'AJAX_Libeller':
	print( json_encode( array(
		'Statut' => 'success',
		'L_Fermer' => $L_Fermer,
		'Titre' => $L_Ajouter_Profil,
		'Titre1' => $L_Modifier_Profil,
		'Titre2' => $L_Supprimer_Profil,
		'L_Ajouter' => $L_Ajouter,
		'L_Modifier' => $L_Modify,
		'L_Supprimer' => $L_Delete,
		'L_Libelle' => $L_Label
		) ) );
	
	exit();


 case 'AJAX_Lister_Applications':
	if ( $Droit_Lecture === TRUE ) {
		$Liste_Applications = $objApplications->rechercherApplications('ain_libelle');
		$Total_Applications = $objApplications->RowCount;
	
		$Liste_Applications_HTML = '';
		$Lignes = 0;
	
		foreach( $Liste_Applications as $Occurrence ) {
			$Lignes += 1;
			$Liste_Applications_HTML .= "<div class=\"row liste\" data-ligne=\"" . $Lignes . "\" data-id=\"" . $Occurrence->ain_id . "\"><div class=\"col-lg-3\">" . $Occurrence->ain_libelle . "</div></div>";
			$Liste_ID_Applications[] = $Occurrence->ain_id;
		}
	
		echo json_encode( array(
			'statut' => 'success',
			'liste_applications' => $Liste_Applications_HTML,
			'liste_id_applications' => $Liste_ID_Applications,
			'total_applications' => $Total_Applications
			) );
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		) );
	}
	break;


 case 'AJAX_Lister_Profils':
	if ( $Droit_Lecture === TRUE ) {
		$Liste_Profils = $objProfils->rechercherProfils();
		$Total_Profils = $objProfils->RowCount;
	
		$Liste_Profils_HTML = '';
		$Colonnes = 0;
	
		foreach( $Liste_Profils as $Occurrence ) {
			$Colonnes += 1;
			$Liste_Profils_HTML .= '<div class="col-lg-1 text-break" data-id="' . $Occurrence->prf_id . '"><a href="#" class="modifiable">' . $Occurrence->prf_libelle . '</a>';
	
			if ( $Droit_Supprimer == TRUE ) $Liste_Profils_HTML .= '<button class="bi-x-circle btn btn-outline-secondary btn-sm" title="'.$L_Supprimer_Profil.'"></button>';
	
			$Liste_Profils_HTML .= '</div>';
			
			$Liste_ID_Profils[] = $Occurrence->prf_id;
		}
	
		$Limitation = $PageHTML->recupererParametre('limitation_profils');
	
		echo json_encode( array(
			'statut' => 'success',
			'liste_profils' => $Liste_Profils_HTML,
			'liste_id_profils' => $Liste_ID_Profils,
			'total_profils' => $Total_Profils,
			'droit_suppression' => $Droit_Supprimer,
			'droit_modification' => $Droit_Modifier,
			'libelle_limitation' => $L_Limitation_Licence,
			'limitation' => $Limitation
			) );
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		) );
	}
	break;


 case 'AJAX_Lister_Control_Acces':
 	if ( $Droit_Lecture === TRUE ) {
		$Liste_Acces = $objProfiles_Access_Control->rechercherDroitsVersMatrice();
		$Total_Acces = $objProfiles_Access_Control->RowCount;
		$Libelles = $objProfiles_Access_Control->rechercherLibellesDroits();
	
		echo json_encode( array(
			'statut' => 'success',
			'liste_acces' => $Liste_Acces,
			'total_acces' => $Total_Acces,
			'libelles_droits' => $Libelles
			) );
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		) );
	}
	break;


 case 'AJAX_Activer_Droit':
	if ( $Droit_Modifier === TRUE ) {
		try {
			if ( $_POST['activer'] == 1 ) {
				$Action = $L_Droit_Ajoute;
				$Code_Action = 'ATP_ECRITURE';
				$objProfiles_Access_Control->ajouterControleAcces( $_POST['id_profil'], $_POST['id_application'], $_POST['id_droit'] );
			} else {
				$Action = $L_Droit_Supprime;
				$Code_Action = 'ATP_SUPPRESSION';
				$objProfiles_Access_Control->supprimerControleAcces( $_POST['id_profil'], $_POST['id_application'], $_POST['id_droit'] );
			}

			$PageHTML->ecrireEvenement( $Code_Action, 'OTP_DROIT', 'prf_id="' . $_POST['id_profil'] . '", app_id="' . $_POST[ 'id_application' ] . '", ' .
				'drt_id="' . $_POST['id_droit'] . '"' );

			echo json_encode( array(
				'statut' => 'success',
				'texteMsg' => $Action
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


 case 'AJAX_Ajouter':
	if ( $Droit_Ajouter === TRUE ) {
		if (isset($_POST['libelle'])) {
			if ($_POST['libelle'] == '') {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $L_Field_Mandatory );

				echo json_encode( $Resultat );
				exit();
			}

			try {
				$objProfils->majProfil( '', $_POST[ 'libelle' ] );
				$Id_Profil = $objProfils->LastInsertId;

				$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_PROFIL', 'prf_id="' . $Id_Profil . '", prf_libelle="' . $_POST[ 'libelle' ] . '"' );

				$Limitation = $PageHTML->recupererParametre('limitation_profils');
				$Libelles = $objProfiles_Access_Control->rechercherLibellesDroits();

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Profil_Cree,
					'id' => $Id_Profil,
					'limitation' => $Limitation,
					'libelle_limitation' => $L_Limitation_Licence,
					'libelles_droits' => $Libelles,
					'libelle_delete_profil' => $L_Supprimer_Profil
					);

			} catch (Exception $e) {
				$Statut = 'error';
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Gestion des doublons
					$Message = $L_ERR_DUPL_Profil;
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


 case 'AJAX_Supprimer_Profil':
	if ( $Droit_Supprimer === TRUE ) {
		try {
			$objProfils->supprimerProfil( $_POST[ 'id_profil' ] );

			$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_PROFIL', 'prf_id="' . $_POST['id_profil'] . '", prf_libelle="' . $_POST[ 'libelle' ] . '"' );

			$Limitation = $PageHTML->recupererParametre('limitation_profils');

			$Resultat = array( 'statut' => 'success',
				'texteMsg' => $L_Profil_Supprime,
				'limitation' => $Limitation,
				'libelle_limitation' => $L_Limitation_Licence
				);

		} catch (Exception $e) {
			$Statut = 'error';
			$Message = $e->getMessage();

			$Resultat = array(
				'statut' => $Statut,
				'texteMsg' => $Message
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


 case 'AJAX_Modifier_Profil':
	if ( $Droit_Supprimer === TRUE ) {
		try {
			$objProfils->majProfil( $_POST[ 'id_profil' ], $_POST[ 'libelle' ] );

			$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_PROFIL', 'prf_id="' . $_POST['id_profil'] . '", prf_libelle="' . $_POST[ 'libelle' ] . '"' );

			$Resultat = array( 'statut' => 'success',
				'texteMsg' => $L_Profil_Modifie
				);

		} catch (Exception $e) {
			$Statut = 'error';
			$Message = $e->getMessage();

			if ( $e->getCode() == 23505 ) { // Gestion des doublons
				$Message = $L_ERR_DUPL_Profil;
			}

			$Resultat = array(
				'statut' => $Statut,
				'texteMsg' => $Message
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


 case 'AJAX_Verifier_Associer':
	if ( isset($_POST['id']) ) {
		try { 
			$Compteurs = $objProfils->testerAssociationProfil( $_POST['id'] );

			$CodeHTML = '';

			if ( $Compteurs->total_idn ) {
				$CodeHTML .= sprintf( $L_Confirmation_Suppression_Profil_Associe, $_POST['libelle'] ) .
					'<ul style="margin-top: 10px;">';

				if ( $Compteurs->total_idn > 1 ) $Libelle = $L_Identities;
				else $Libelle = $L_Identity;

				$CodeHTML .= '<li><span class="orange_moyen">' . $Compteurs->total_idn . '</span> ' . $Libelle . '</li>' .
					'</ul>' . $L_Cascading_Delete;
			} else {
				$CodeHTML .= sprintf( $L_Confirmation_Suppression_Profil, $_POST['libelle'] );
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
}

?>