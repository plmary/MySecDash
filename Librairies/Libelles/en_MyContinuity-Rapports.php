<?php

$L_Synthese_Manageriale_Globale = 'Overall management summary';
$L_Sommaire = 'Summary';

$L_T_BIAs = 'BIAs';
$L_T_Ont_Ete = 'have been';
$L_T_Identifies = 'identified';
$L_T_Pour_Cette_Campagne = 'for this campaign';

$L_Nombre_BIAs_Identifies_Campagne = '<span id="total_bia_identifies" class="fg_couleur_2 fs-3">%s</span>&nbsp;' .
	'<span class="fg_couleur_2 fs-3">'.$L_T_BIAs.'</span> ' . $L_T_Ont_Ete .
	' <span  class="fg_couleur_2 fs-3">' . $L_T_Identifies . '</span> ' .
	$L_T_Pour_Cette_Campagne;
	
$L_T_BIA = 'BIA';
$L_T_A_Ete = 'has been';
$L_T_Identifie = 'identified';

$L_Nombre_BIA_Identifie_Campagne = '<span id="total_bia_identifies" class="fg_couleur_2 fs-3">%s</span>&nbsp;' .
	'<span class="fg_couleur_2 fs-3">'.$L_T_BIA.'</span> '.$L_T_A_Ete.
	' <span  class="fg_couleur_2 fs-4">'.$L_T_Identifie.'</span>' .
	$L_T_Pour_Cette_Campagne;

$L_T_Sont = 'are';
$L_T_Valides = 'validated';

$L_Nombre_BIAs_Valides_Campagne = '<span id="total_bia_valides" class="fg_couleur_1 fs-3">%s</span>&nbsp;' .
	$L_T_Sont . ' <span  class="fg_couleur_1 fs-4">'.$L_T_Valides.'</span>';

$L_T_Est = 'is';
$L_T_Valide = 'validated';

$L_Nombre_BIA_Valide_Campagne = '<span id="total_bia_valides" class="fg_couleur_1 fs-3">%s</span>&nbsp;' .
	$L_T_Est . ' <span  class="fg_couleur_1 fs-4">'.$L_T_Valide.'</span>';

$L_T_En_Cours = 'ongoing';

$L_Nombre_BIAs_En_Cours_Campagne = '<span id="total_bia_en_cours" class="fg_couleur_2 fs-3">%s</span>&nbsp;' .
$L_T_Sont.' <span  class="fg_couleur_2 fs-4">'.$L_T_En_Cours.'</span>';

$L_Nombre_BIA_En_Cours_Campagne = '<span id="total_bia_en_cours" class="fg_couleur_2 fs-3">%s</span>&nbsp;' .
	$L_T_Est.' <span  class="fg_couleur_2 fs-4">'.$L_T_En_Cours.'</span>';

$L_T_A_Faire = 'to do';

$L_Nombre_BIAs_A_Faire_Campagne = '<span id="total_bia_a_faire" class="fg_couleur_3 fs-3">%s</span>&nbsp;' .
	$L_T_Sont.' <span  class="fg_couleur_3 fs-4">'.$L_T_A_Faire.'</span>';
	
$L_Nombre_BIA_A_Faire_Campagne = '<span id="total_bia_a_faire" class="fg_couleur_3 fs-3">%s</span>&nbsp;' .
	$L_T_Est.' <span  class="fg_couleur_3 fs-4">'.$L_T_A_Faire.'</span>';

$L_T_Activites = 'activities';
$L_T_Essentielles = 'essentials';
$L_T_Def_Activites_Essentielles = '(impact > 2)';

$L_Nombre_Activites_Essentielles = '<span id="total_activites_essentielles" class="fg_couleur_2 fs-3">%s</span>&nbsp;' .
	$L_T_Activites . ' '.$L_T_Sont.' <span  class="fg_couleur_2 fs-4">'.$L_T_Essentielles.'</span> '.$L_T_Def_Activites_Essentielles;

$L_T_Activite = 'activity';
$L_T_Essentielle = 'essential';

$L_Nombre_Activite_Essentielle = '<span id="total_activites_essentielles" class="fg_couleur_2 fs-3">%s</span>&nbsp;' .
	$L_T_Activite.' '.$L_T_Est.' <span  class="fg_couleur_2 fs-4">'.$L_T_Essentielle.'</span> '.$L_T_Def_Activites_Essentielles;

$L_T_Dont = 'dont';
$L_T_Critiques = 'criticals';
$L_T_Def_Activites_Critiques = '(impact = 4)';

$L_Nombre_Activites_Critiques = $L_T_Dont.' <span id="total_activites_critiques" class="fg_couleur_3 fs-3">%s</span>&nbsp;' .
	$L_T_Activites.' '.$L_T_Sont.' <span  class="fg_couleur_3 fs-4">'.$L_T_Critiques.'</span> '.$L_T_Def_Activites_Critiques;

$L_T_Critique = 'critical';

$L_Nombre_Activite_Critique = $L_T_Dont.' <span id="total_activites_critiques" class="fg_couleur_3 fs-3">%s</span>&nbsp;' .
	$L_T_Activite.' '.$L_T_Est.' <span  class="fg_couleur_3 fs-4">'.$L_T_Critique.'</span> '.$L_T_Def_Activites_Critiques;

$L_T_Def_Activites_Graves = '(impact = 3)';

$L_Nombre_Activites_Graves = $L_T_Dont.' <span id="total_activites_graves" class="fg_couleur_3 fs-3">%s</span>&nbsp;' .
	$L_T_Activites.' '.$L_T_Sont.' <span  class="fg_couleur_3 fs-4">graves</span> '.$L_T_Def_Activites_Graves;

$L_Nombre_Activite_Grave = $L_T_Dont.' <span id="total_activites_graves" class="fg_couleur_3 fs-3">%s</span>&nbsp;' .
	$L_T_Activite.' '.$L_T_Est.' <span  class="fg_couleur_3 fs-4">grave</span> '.$L_T_Def_Activites_Graves;

$L_T_Ensemble_Activites_Analysees = 'for all the activities analysed';

$L_T_Site = 'site';
$L_T_Sites = 'sites';

$L_Nombre_Sites = '<span id="total_sites" class="fg_couleur_1 fs-3">%s</span>&nbsp;' .
	$L_T_Sites.' have been identified '.$L_T_Ensemble_Activites_Analysees;

$L_Nombre_Site = '<span id="total_sites" class="fg_couleur_1 fs-3">%s</span>&nbsp;' .
	$L_T_Sites.' has been identified '.$L_T_Ensemble_Activites_Analysees;

$L_T_Application = 'application';
$L_T_Applications = 'applications';
$L_T_Supportant_Activites = 'supporting essential activities';

$L_Nombre_Applications = '<span id="total_applications" class="fg_couleur_1 fs-3">%s</span>&nbsp;' .
	$L_T_Applications.' '.$L_T_Supportant_Activites;

$L_Nombre_Application = '<span id="total_application" class="fg_couleur_1 fs-3">%s</span>&nbsp;' .
	$L_T_Application.' '.$L_T_Supportant_Activites;

$L_T_Personne_Cle = 'key person';
$L_T_Personnes_Cles = 'key people';

$L_Nombre_Personnes_Cles = '<span id="total_personnes_cles" class="fg_couleur_1 fs-3">%s</span>&nbsp;' . $L_T_Personnes_Cles;

$L_Nombre_Personne_Cle = '<span id="total_personnes_cles" class="fg_couleur_1 fs-3">%s</span>&nbsp;' . $L_T_Personne_Cle;

$L_T_Fournisseurs = 'suppliers';
$L_T_Fournisseur = 'supplier';

$L_Nombre_Fournisseurs = '<span id="total_fournisseurs" class="fg_couleur_1 fs-3">%s</span>&nbsp;' .
	$L_T_Fournisseurs.' '.$L_T_Supportant_Activites;

$L_Nombre_Fournisseur = '<span id="total_fournisseurs" class="fg_couleur_1 fs-3">%s</span>&nbsp;' .
	$L_T_Fournisseur.' '.$L_T_Supportant_Activites;

$L_Activites_Non_Critiques_Non_Abordees = 'Non-critical activities have not been addressed';

$L_Et = 'and';

$L_Liste_Activites = 'Activities list';
$L_Liste_Activites_Critiques = 'Critical activities list';
$L_Liste_Activites_Graves = 'List of serious activities';
$L_Detail_Activites = 'Détail des activités';

$L_Activites_A_Redemarrer = 'Activities to restart in';

$L_Liste_Applications = 'Applications list';
$L_Applications_A_Redemarrer = 'Applications to restart in';

$L_Liste_Personnes_Cles = 'Key persons list';

$L_Liste_Fournisseurs = 'Suppliers list';
$L_DMIA_Fournisseurs = 'Essential suppliers from';

$L_Conclusion_BIAs = 'Concluding Business Impact Analysis';

$L_Nom_G = 'Name';

$L_Liste_Activites_Redemarrer_Par_Periode = 'List of activities to be restarted by period';
$L_Liste_Applications_Redemarrer_Par_Periode = 'List of applications to be restarted by period';
$L_Liste_Fournisseurs_Utiles_Par_Periode = 'List of useful suppliers by period';

$L_BIA_Entite = 'Entity BIA';
$L_BIAs_Societe = 'Company\'s BIAs';

?>