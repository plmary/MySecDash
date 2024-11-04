<?php

/**
 * Ce script gère les Civilités.
 *
 * \license Copyleft Loxense
 * \author Pierre-Luc MARY
 * \package MySecDash
 * \version 1.0
 * \date 2015-10-15
 */

// Charge les constantes du projet.
include ('Constants.inc.php');

include (DIR_LIBRAIRIES . '/Loxense-Entete-Standard.php' );


// Charge les libellés en fonction de la langue sélectionnée.
include (DIR_LIBELLES . '/' . $_SESSION['Language'] . '_' . basename($Script));

// Charge les classes utiles à cet écran.
include_once (DIR_LIBRAIRIES . '/Class_HBL_Civilites_PDO.inc.php');
include_once (DIR_LIBRAIRIES . '/Class_HBL_Societes_PDO.inc.php');

// Crée une instance de l'objet HTML.
$objCivilites = new HBL_Civilites();
$objSocietes = new HBL_Societes();

// Définit le format des colonnes du tableau central.
$Trier = 'cvl_nom';

$Format_Colonnes['Prefixe'] = 'CVL';
$Format_Colonnes['Fonction_Ouverture'] = 'ouvrirChamp';
$Format_Colonnes['Id'] = array(
	'nom' => 'cvl_id'
);
$Format_Colonnes['Colonnes'][] = array(
	'nom' => 'cvl_nom',
	'titre' => $L_Nom_Famille,
	'taille' => '4',
	'triable' => 'oui',
	'tri_actif' => 'oui',
	'sens_tri' => 'cvl_nom',
	'type' => 'input',
	'modifiable' => 'oui'
);
$Format_Colonnes['Colonnes'][] = array(
	'nom' => 'cvl_prenom',
	'titre' => $L_Prenom,
	'taille' => '3',
	'triable' => 'oui',
	'tri_actif' => 'non',
	'sens_tri' => 'cvl_prenom',
	'type' => 'input',
	'modifiable' => 'oui'
);
$Format_Colonnes['Actions'] = array(
	'taille' => '3',
	'titre' => $L_Actions,
	'boutons' => array(
		'modifier' => $Droit_Modifier,
		'supprimer' => $Droit_Supprimer
	)
);

// Exécute l'action identifie
switch ($Action) {
	default:
		if ( $Droit_Ajouter === TRUE ) {
			$Boutons_Alternatifs[] = [ 'class' => 'btn-ajouter', 'libelle' => $L_Ajouter, 'glyph' => 'plus' ];
		}

		$Boutons_Alternatifs[] = [ 'class' => 'btn-rechercher', 'libelle' => $L_Rechercher, 'glyph' => 'search' ];

		print($PageHTML->construireEnteteHTML($L_Gestion_Civilites, $Fichiers_JavaScript) .
			$PageHTML->construireNavbarJson('Logo-MySecDash.svg', 'nav-items.json') .
			$PageHTML->construireTitreEcran($L_Gestion_Civilites, '', $Boutons_Alternatifs));

		if ($Droit_Lecture === TRUE) {
			// Construit un tableau central vide.
			print($PageHTML->contruireTableauVide($Format_Colonnes));
		}

		print($PageHTML->construireFooter(TRUE) . $PageHTML->construirePiedHTML());

		break;

	/*
	 * ========================================================================
	 * * Réponses aux appels AJAX
	 */

	case 'AJAX_Libeller':
		$Libelles = array(
			'Statut' => 'success',
			'L_Fermer' => $L_Fermer,
			'L_Titre_Ajouter' => $L_Ajouter_Civilite,
			'L_Titre_Modifier' => $L_Modifier_Civilite,
			'L_Titre_Supprimer' => $L_Supprimer_Civilite,
			'L_Ajouter' => $L_Ajouter,
			'L_Modifier' => $L_Modify,
			'L_Supprimer' => $L_Delete,
			'L_Nom' => $L_Nom_Famille,
			'L_Prenom' => $L_Prenom,
			'L_Homme' => $L_Homme,
			'L_Femme' => $L_Femme,
			'L_Field_Mandatory' => $L_Field_Mandatory,
			'L_Date' => $L_Format_Date
		);

		if ($Droit_Lecture === TRUE) {
			if ( isset( $_POST['cvl_id'] ) ) {
				if ( $_POST['cvl_id'] != '' ) {
					$Libelles['Civilite'] = $objCivilites->detaillerCivilite( $_POST['cvl_id'] );
					
				}
			}
		} else {
			$Resultat = array(
				'statut' => 'error',
				'texteMsg' => $L_No_Authorize
			);
			
			echo json_encode($Resultat);
			exit();
		}

		print(json_encode($Libelles));

		exit();

	case 'AJAX_Ajouter':
		if ($Droit_Ajouter === TRUE) {
			if (isset($_POST['last_name']) and isset($_POST['first_name'])) {
				try {
					$objCivilites->majCivilite('', $_POST['last_name'], $_POST['first_name']);

					$Id_Civility = $objCivilites->LastInsertId;

					$PageHTML->ecrireEvenement('ATP_ECRITURE', 'OTP_CIVILITE', 'cvl_id = "' . $Id_Civility . '", cvl_nom="' . $_POST['last_name'] . '", cvl_prenom="' . $_POST['first_name'] . '"');

					$Valeurs = new stdClass();
					$Valeurs->cvl_nom = $_POST['last_name'];
					$Valeurs->cvl_prenom = $_POST['first_name'];

					$Occurrence = $PageHTML->creerOccurrenceCorpsTableau($Id_Civility, $Valeurs, $Format_Colonnes);

					$Limitation = $PageHTML->recupererParametre('limitation_civilites');

					$Resultat = array(
						'statut' => 'success',
						'texteMsg' => $L_Civilite_Cree,
						'texte' => $Occurrence,
						'id' => $Id_Civility,
						'droit_modifier' => $Droit_Modifier,
						'droit_supprimer' => $Droit_Supprimer,
						'limitation' => $Limitation,
						'libelle_limitation' => $L_Limitation_Licence
					);
				} catch (Exception $e) {
					$Message = $e->getMessage();

					if ($e->getCode() == 23505) { // Gestion d'un doublon
						$Message = $L_ERR_DUPL_Civilite;
					}

					$Resultat = array(
						'statut' => 'error',
						'texteMsg' => $Message
					);
				}

				echo json_encode($Resultat);
			} else {
				$Resultat = array(
					'statut' => 'error',
					'texteMsg' => $L_ERR_Champs_Obligatoires
				);

				echo json_encode($Resultat);
				exit();
			}
		} else {
			$Resultat = array(
				'statut' => 'error',
				'texteMsg' => $L_No_Authorize
			);

			echo json_encode($Resultat);
			exit();
		}
		break;

	case 'AJAX_Modifier_Champ':
		if ($Droit_Modifier === TRUE) {
			if (isset($_POST['id']) && isset($_POST['source']) && isset($_POST['valeur'])) {
				try {
					$objCivilites->majCiviliteParChamp($_POST['id'], $_POST['source'], $_POST['valeur']);

					$PageHTML->ecrireEvenement('ATP_MODIFICATION', 'OTP_CIVILITE', $_POST['source'] . '="' . $_POST['valeur'] . '"');

					$Resultat = array(
						'statut' => 'success',
						'texteMsg' => $L_Civilite_Modifiee
					);
				} catch (Exception $e) {
					$Message = $e->getMessage();

					if ($e->getCode() == 23505) { // Cas d'un doublon
						$Message = $L_ERR_DUPL_Civilite;
					}

					$Resultat = array(
						'statut' => 'error',
						'texteMsg' => $Message
					);
				}

				echo json_encode($Resultat);
			}
		} else {
			$Resultat = array(
				'statut' => 'error',
				'texteMsg' => $L_No_Authorize
			);
			
			echo json_encode($Resultat);
			exit();
		}
		break;

	case 'AJAX_Supprimer':
		if ( $Droit_Supprimer === TRUE ) {
			if (isset($_POST['id'])) {
				try {
					$objCivilites->supprimerCivilite($_POST['id']);
	
					$PageHTML->ecrireEvenement('ATP_SUPPRESSION', 'OTP_CIVILITE', 'cvl_id="' . $_POST['id'] . '", ' . 'cvl_nom="' . $_POST['last_name'] . '", ' . 'cvl_prenom="' . $_POST['first_name'] . '" ');
	
					$Limitation = $PageHTML->recupererParametre('limitation_civilites');
	
					$Resultat = array(
						'statut' => 'success',
						'titreMsg' => $L_Success,
						'texteMsg' => $L_Civilite_Supprimee,
						'limitation' => $Limitation,
						'libelle_limitation' => $L_Limitation_Licence
					);
				} catch (Exception $e) {
					$Resultat = array(
						'statut' => 'error',
						'titreMsg' => $L_Error,
						'texteMsg' => $e->getMessage()
					);
				}
	
				echo json_encode($Resultat);
			}
		} else {
			$Resultat = array(
				'statut' => 'error',
				'texteMsg' => $L_No_Authorize
			);
			
			echo json_encode($Resultat);
			exit();
		}
		break;

	case 'AJAX_Trier':
		if ($Droit_Lecture === TRUE) {
			$Trier = $_POST['trier'];

			try {
				$Civilites = $objCivilites->rechercherCivilites($Trier);
				$Total = $objCivilites->RowCount;

				$Texte_HTML = '';

				foreach ($Civilites as $Occurrence) {
					$Texte_HTML .= $PageHTML->creerOccurrenceCorpsTableau($Occurrence->cvl_id, $Occurrence, $Format_Colonnes);
				}

				$Limitation = $PageHTML->recupererParametre('limitation_civilites');

				echo json_encode(array(
					'statut' => 'success',
					'texteHTML' => $Texte_HTML,
					'total' => $Total,
					'droit_modifier' => $Droit_Modifier,
					'droit_supprimer' => $Droit_Supprimer,
					'limitation' => $Limitation,
					'libelle_limitation' => $L_Limitation_Licence
				));
			} catch (Exception $e) {
				echo json_encode(array(
					'statut' => 'error',
					'texteMsg' => $e->getMessage()
				));
			}
		} else {
			$Resultat = array(
				'statut' => 'error',
				'texteMsg' => $L_No_Authorize
			);
			
			echo json_encode($Resultat);
			exit();
		}
		break;

	case 'AJAX_Charger':
		if ( $Droit_Lecture === TRUE ) {
			try {
				$Civilite = $objCivilites->detaillerCivilite($_POST['cvl_id']);
	
				echo json_encode(array(
					'statut' => 'success',
					'last_name' => $Civilite->cvl_nom,
					'first_name' => $Civilite->cvl_prenom
				));
			} catch (Exception $e) {
				echo json_encode(array(
					'statut' => 'error',
					'texteMsg' => $e->getMessage()
				));
			}
		} else {
			$Resultat = array(
				'statut' => 'error',
				'texteMsg' => $L_No_Authorize
			);
			
			echo json_encode($Resultat);
			exit();
		}
		break;

	case 'AJAX_Modifier':
		if ($Droit_Modifier === TRUE) {
			if (isset($_POST['cvl_id']) and isset($_POST['last_name']) and isset($_POST['first_name'])) {
				try {
					$objCivilites->majCivilite($_POST['cvl_id'], $_POST['last_name'], $_POST['first_name']);

					$Resultat = array(
						'statut' => 'success',
						'texteMsg' => $L_Civilite_Modifiee
					);

					$PageHTML->ecrireEvenement('ATP_MODIFICATION', 'OTP_CIVILITE', 'cvl_id="' . $_POST['cvl_id'] . '", cvl_nom="' . $_POST['last_name'] . '", cvl_prenom="' . $_POST['first_name'] . '"');
				} catch (Exception $e) {
					$Message = $e->getMessage();

					if ($e->getCode() == 23505) {
						$Statut = 'exists';
						$Message = $L_ERR_DUPL_Civilite;
					} else { // Si erreur infrastructure
						$Statut = 'error';
					}

					$Resultat = array(
						'statut' => $Statut,
						'texteMsg' => $Message
					);
				}

				echo json_encode($Resultat);
			} else {
				$Resultat = array(
					'statut' => 'error',
					'texteMsg' => $L_ERR_Champs_Obligatoires
				);

				echo json_encode($Resultat);
				exit();
			}
		} else {
			$Resultat = array(
				'statut' => 'error',
				'texteMsg' => $L_No_Authorize
			);

			echo json_encode($Resultat);
			exit();
		}
		break;

	case 'AJAX_Verifier_Associer':
		if (isset($_POST['id'])) {
			try {
				$Compteurs = $objCivilites->CiviliteEstAssociee($_POST['id']);

				$CodeHTML = '';

				if ($Compteurs->total_idn) {
					$CodeHTML .= sprintf($L_Confirmer_Suppression_Civilite_Associee, $_POST['first_name'] . ' ' . $_POST['last_name']) . '<ul style="margin-top: 10px;">';

					include (HBL_DIR_LABELS . '/' . $_SESSION['Language'] . '_HBL_Identites.inc.php');

					if ($Compteurs->total_idn > 1)
						$Libelle = $L_Identities;
					else
						$Libelle = $L_Identity;

					$CodeHTML .= '<li><span class="orange_moyen">' . $Compteurs->total_idn . '</span> ' . $Libelle . '</li>' . '</ul>' . $L_Cascading_Delete;
				} else {
					$CodeHTML .= sprintf($L_Confirmer_Suppression_Civilite, $_POST['first_name'] . ' ' . $_POST['last_name']);
				}

				$Resultat = array(
					'statut' => 'success',
					'texteMsg' => $CodeHTML
				);
			} catch (Exception $e) {
				$Resultat = array(
					'statut' => 'error',
					'texteMsg' => $e->getMessage()
				);
			}
		} else {
			$Resultat = array(
				'statut' => 'error',
				'texteMsg' => $L_ERR_Champs_Obligatoires
			);
		}

		echo json_encode($Resultat);

		break;
}

?>