<?php

/**
* Ce script gère l'historique des actions réalisées par les utilisateurs.
*
* \license Copyleft Loxense
* \author Pierre-Luc MARY
* \package MySecDash
* \date 2015-10-15
*/

// Charge les constantes du projet.
include( 'Constants.inc.php' );

include( DIR_LIBRAIRIES . '/Loxense-Entete-Standard.php' );

// Charge les libellés en fonction de la langue sélectionnée.
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_' . basename( $Script ) );

include( DIR_LIBRAIRIES . '/Class_HBL_Historiques_PDO.inc.php' );
$Historicals = new HBL_Historiques();


// Définit le format des colonnes du tableau central.
$Format_Colonnes[ 'Prefixe' ] = 'HAC';
$Format_Colonnes[ 'Fonction_Ouverture' ] = 'ouvrirChamp';
$Format_Colonnes[ 'Id' ] = array( 'nom' => 'hac_id' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'hac_date', 'taille' => '2', 'titre' => $L_Date,
	'triable' => 'oui', 'tri_actif' => 'oui', 'sens_tri' => 'hac_date-desc',
	'type' => 'input', 'modifiable' => 'non',
	'recherche' => array(
		array( 'nom' => 'date_1', 'arriere_plan' => $L_De_Date, 'info_bulle' => $L_Format_Date_Heure ),
		array( 'nom' => 'date_2', 'arriere_plan' => $L_A_Date, 'info_bulle' => $L_Format_Date_Heure ) ) );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'libelle_tpa', 'taille' => '2', 'titre' => $L_Type_Action,
	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'libelle_tpa',
	'type' => 'input', 'modifiable' => 'non',
	'recherche' => array( 'nom' => 'libelle_tpa', 'type' => 'select', 'fonction' => 'listerTypesAction' ) );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'libelle_tpo', 'taille' => '2', 'titre' => $L_Type_Objet,
	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'libelle_tpo',
	'type' => 'input', 'modifiable' => 'non',
	'recherche' => array( 'nom' => 'libelle_tpo', 'type' => 'select', 'fonction' => 'listerTypesObjet' ) );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'hac_utilisateur', 'taille' => '1', 'titre' => $L_User,
	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'hac_utilisateur',
	'type' => 'input', 'modifiable' => 'non',
	'recherche' => array( 'nom' => 'user' ) );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'hac_ip_utilisateur', 'taille' => '1', 'titre' => $L_Adresse_IP,
	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'hac_ip_utilisateur',
	'type' => 'input', 'modifiable' => 'non',
	'recherche' => array( 'nom' => 'ip_user' ) );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'hac_detail', 'taille' => '4', 'titre' => $L_Detail,
	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'hac_detail',
	'type' => 'input', 'modifiable' => 'non',
	'recherche' => array( 'nom' => 'detail' ) );


// Exécute l'action identifie
switch( $Action ) {
 default:
	$Boutons_Alternatifs = [
	//['class'=>'btn-rechercher', 'libelle'=>$L_Rechercher, 'glyph'=>'search']
	];

	print( $PageHTML->construireEnteteHTML( $L_Consultation_Historique, $Fichiers_JavaScript ) .
		$PageHTML->construireNavbarJson('Logo-MySecDash.svg', 'nav-items.json') .
		$PageHTML->construireTitreEcran( $L_Consultation_Historique, '', $Boutons_Alternatifs )
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
	print( json_encode( array(
		'Statut' => 'success',
		'L_Fermer' => $L_Fermer,
		'Titre' => $L_Ajouter_Civilite,
		'Titre1' => $L_Modifier_Civilite,
		'L_Ajouter' => $L_Ajouter,
		'L_Modifier' => $L_Modify,
		'L_Nom' => $L_Nom,
		'L_Prenom' => $L_Prenom,
		'L_Sex' => $L_Sex,
		'L_Date_Naissance' => $L_Date_Naissance,
		'L_Ville_Naissance' => $L_Ville_Naissance
		) ) );
	
	exit();


 case 'AJAX_Trier':
	if ( $Droit_Lecture === TRUE ) {
		$Trier = $_POST[ 'trier' ];

		if ( isset( $_POST['date_debut'] ) ) $date_debut = $_POST['date_debut'];
		else $date_debut = '';

		if ( isset( $_POST['date_fin'] ) ) $date_fin = $_POST['date_fin'];
		else $date_fin = '';

		if ( $date_debut == '' and $date_fin == '' ) {
			$date = new DateTime(); // Récupère la date courante
			$date->sub(new DateInterval('P1M')); // Retire un mois
			$date_debut = $date->format('Y-m-d 00:00:00'); // Reformate la date
		}

		if ( isset( $_POST['libelle_tpa'] ) ) $libelle_tpa = $_POST['libelle_tpa'];
		else $libelle_tpa = '';
 
		if ( isset( $_POST['libelle_tpo'] ) ) $libelle_tpo = $_POST['libelle_tpo'];
		else $libelle_tpo = '';

		if ( isset( $_POST['user'] ) ) $user = $_POST['user'];
		else $user = '';

		if ( isset( $_POST['ip_user'] ) ) $ip_user = $_POST['ip_user'];
		else $ip_user = '';

		if ( isset( $_POST['detail'] ) ) $detail = $_POST['detail'];
		else $detail = '';
		
		try {
			$Historiques = $Historicals->rechercherEvenements( $Trier, $date_debut, $date_fin, $libelle_tpa, $libelle_tpo, $user, $ip_user, $detail );

			$Total = $Historicals->RowCount;

			$Texte_HTML = '';
			
			foreach ($Historiques as $Occurrence) {
				$Texte_HTML .= $PageHTML->creerOccurrenceCorpsTableau( $Occurrence->hac_id, $Occurrence, $Format_Colonnes );
			}

			echo json_encode( array(
				'statut' => 'success',
				'texteHTML' => $Texte_HTML,
				'total' => $Total,
				'droit_modifier' => $Droit_Modifier,
				'droit_supprimer' => $Droit_Supprimer,
				'date_debut'=> $date_debut
				) );
		} catch( Exception $e ) {
			echo json_encode( array(
				'statut' => 'error',
				'texteMsg' => $e->getMessage()
				) );
		}
	}	
	break;
}


function listerTypesAction() {
	$Code_HTML = '';

	$Historicals = new HBL_Historiques();

	$Types = $Historicals->listActionTypes();

	$Code_HTML .= '<option value="">' . $GLOBALS[ 'L_Tous' ] . '</option>';

	foreach ($Types as $Occurrence) {
		$Code_HTML .= '<option value="' . $Occurrence->tpa_id . '">' . $Occurrence->libelle_tpa . '</option>';
	}

	return $Code_HTML;
}


function listerTypesObjet() {
	$Code_HTML = '';

	$Historicals = new HBL_Historiques();

	$Types = $Historicals->listObjectTypes();

	$Code_HTML .= '<option value="">' . $GLOBALS[ 'L_Tous' ] . '</option>';

	foreach ($Types as $Occurrence) {
		$Code_HTML .= '<option value="' . $Occurrence->tpo_id . '">' . $Occurrence->libelle_tpo . '</option>';
	}

	return $Code_HTML;
}

?>