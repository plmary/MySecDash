<?php

$L_Synthese_Manageriale_Globale = 'Synthèse managériale globale';
$L_Sommaire = 'Sommaire';

$L_T_BIAs = 'BIAs';
$L_T_Ont_Ete = 'ont été';
$L_T_Identifies = 'identifiés';
$L_T_Pour_Cette_Campagne = 'pour cette campagne';

$L_Nombre_BIAs_Identifies_Campagne = '<span id="total_bia_identifies" class="fg_couleur_2 fs-3">%s</span>&nbsp;' .
	'<span class="fg_couleur_2 fs-3">'.$L_T_BIAs.'</span> ' . $L_T_Ont_Ete .
	' <span  class="fg_couleur_2 fs-3">' . $L_T_Identifies . '</span> ' .
	$L_T_Pour_Cette_Campagne;

$L_T_BIA = 'BIA';
$L_T_A_Ete = 'a été';
$L_T_Identifie = 'identifié';

$L_Nombre_BIA_Identifie_Campagne = '<span id="total_bia_identifies" class="fg_couleur_2 fs-3">%s</span>&nbsp;' .
	'<span class="fg_couleur_2 fs-3">'.$L_T_BIA.'</span> '.$L_T_A_Ete.
	' <span  class="fg_couleur_2 fs-4">'.$L_T_Identifie.'</span>' .
	$L_T_Pour_Cette_Campagne;

$L_T_Sont = 'sont';
$L_T_Valides = 'validés';

$L_Nombre_BIAs_Valides_Campagne = '<span id="total_bia_valides" class="fg_couleur_1 fs-3">%s</span>&nbsp;' .
	$L_T_Sont . ' <span  class="fg_couleur_1 fs-4">'.$L_T_Valides.'</span>';

$L_T_Est = 'est';
$L_T_Valide = 'validé';

$L_Nombre_BIA_Valide_Campagne = '<span id="total_bia_valides" class="fg_couleur_1 fs-3">%s</span>&nbsp;' .
	$L_T_Est . ' <span  class="fg_couleur_1 fs-4">'.$L_T_Valide.'</span>';

$L_T_En_Cours = 'en cours';

$L_Nombre_BIAs_En_Cours_Campagne = '<span id="total_bia_en_cours" class="fg_couleur_2 fs-3">%s</span>&nbsp;' .
	$L_T_Sont.' <span  class="fg_couleur_2 fs-4">'.$L_T_En_Cours.'</span>';

$L_Nombre_BIA_En_Cours_Campagne = '<span id="total_bia_en_cours" class="fg_couleur_2 fs-3">%s</span>&nbsp;' .
	$L_T_Est.' <span  class="fg_couleur_2 fs-4">'.$L_T_En_Cours.'</span>';

$L_T_A_Faire = 'à faire';

$L_Nombre_BIAs_A_Faire_Campagne = '<span id="total_bia_a_faire" class="fg_couleur_3 fs-3">%s</span>&nbsp;' .
	$L_T_Sont.' <span  class="fg_couleur_3 fs-4">'.$L_T_A_Faire.'</span>';

$L_Nombre_BIA_A_Faire_Campagne = '<span id="total_bia_a_faire" class="fg_couleur_3 fs-3">%s</span>&nbsp;' .
	$L_T_Est.' <span  class="fg_couleur_3 fs-4">'.$L_T_A_Faire.'</span>';

$L_T_Activites = 'activités';
$L_T_Essentielles = 'essentielles';
$L_T_Def_Activites_Essentielles = '(impact > 2)';

$L_Nombre_Activites_Essentielles = '<span id="total_activites_essentielles" class="fg_couleur_2 fs-3">%s</span>&nbsp;' .
	$L_T_Activites . ' '.$L_T_Sont.' <span  class="fg_couleur_2 fs-4">'.$L_T_Essentielles.'</span> '.$L_T_Def_Activites_Essentielles;

$L_T_Activite = 'activité';
$L_T_Essentielle = 'essentielle';

$L_Nombre_Activite_Essentielle = '<span id="total_activites_essentielles" class="fg_couleur_2 fs-3">%s</span>&nbsp;' .
	$L_T_Activite.' '.$L_T_Est.' <span  class="fg_couleur_2 fs-4">'.$L_T_Essentielle.'</span> '.$L_T_Def_Activites_Essentielles;

$L_T_Dont = 'dont';
$L_T_Critiques = 'critiques';
$L_T_Def_Activites_Critiques = '(impact = 3)';
$L_T_Vitales = 'vitales';
$L_T_Def_Activites_Vitales = '(impact = 4)';

$L_Nombre_Activites_Vitales = $L_T_Dont.' <span id="total_activites_vitales" class="fg_couleur_3 fs-3">%s</span>&nbsp;' .
	$L_T_Activites.' '.$L_T_Sont.' <span  class="fg_couleur_3 fs-4">'.$L_T_Vitales.'</span> '.$L_T_Def_Activites_Vitales;

$L_T_Critique = 'critique';
$L_T_Vitale = 'vitale';

$L_Nombre_Activite_Vitale = $L_T_Dont.' <span id="total_activites_vitales" class="fg_couleur_3 fs-3">%s</span>&nbsp;' .
	$L_T_Activite.' '.$L_T_Est.' <span  class="fg_couleur_3 fs-4">'.$L_T_Vitale.'</span> '.$L_T_Def_Activites_Vitales;

$L_T_Def_Activites_Graves = '(impact = 3)';

$L_Nombre_Activites_Graves = $L_T_Dont.' <span id="total_activites_graves" class="fg_couleur_3 fs-3">%s</span>&nbsp;' .
	$L_T_Activites.' '.$L_T_Sont.' <span  class="fg_couleur_3 fs-4">graves</span> '.$L_T_Def_Activites_Graves;

$L_Nombre_Activite_Grave = $L_T_Dont.' <span id="total_activites_graves" class="fg_couleur_3 fs-3">%s</span>&nbsp;' .
	$L_T_Activite.' '.$L_T_Est.' <span  class="fg_couleur_3 fs-4">grave</span> '.$L_T_Def_Activites_Graves;

$L_T_Ensemble_Activites_Analysees = 'pour l\'ensemble des activités analysées';

$L_T_Site = 'site';
$L_T_Sites = 'sites';

$L_Nombre_Sites = '<span id="total_sites" class="fg_couleur_1 fs-3">%s</span>&nbsp;' .
	$L_T_Sites.' ont été identifiés '.$L_T_Ensemble_Activites_Analysees;

$L_Nombre_Site = '<span id="total_sites" class="fg_couleur_1 fs-3">%s</span>&nbsp;' .
	$L_T_Site.' a été identifié '.$L_T_Ensemble_Activites_Analysees;

$L_T_Application = 'application';
$L_T_Applications = 'applications';
$L_T_Supportant_Activites = 'supportant des activités essentielles';

$L_Nombre_Applications = '<span id="total_applications" class="fg_couleur_1 fs-3">%s</span>&nbsp;' .
	$L_T_Applications.' '.$L_T_Supportant_Activites;

$L_Nombre_Application = '<span id="total_application" class="fg_couleur_1 fs-3">%s</span>&nbsp;' .
	$L_T_Application.' '.$L_T_Supportant_Activites;

$L_T_Personne_Cle = 'personne clé';
$L_T_Personnes_Cles = 'personnes clés';

$L_Nombre_Personnes_Cles = '<span id="total_personnes_cles" class="fg_couleur_1 fs-3">%s</span>&nbsp;' . $L_T_Personnes_Cles;

$L_Nombre_Personne_Cle = '<span id="total_personnes_cles" class="fg_couleur_1 fs-3">%s</span>&nbsp;' . $L_T_Personne_Cle;

$L_T_Fournisseurs = 'fournisseurs';
$L_T_Fournisseur = 'fournisseur';

$L_Nombre_Fournisseurs = '<span id="total_fournisseurs" class="fg_couleur_1 fs-3">%s</span>&nbsp;' .
	$L_T_Fournisseurs.' '.$L_T_Supportant_Activites;

$L_Nombre_Fournisseur = '<span id="total_fournisseurs" class="fg_couleur_1 fs-3">%s</span>&nbsp;' .
	$L_T_Fournisseur.' '.$L_T_Supportant_Activites;
	
$L_Activites_Non_Critiques_Non_Abordees = 'Les activités non critiques n\'ont pas été abordées';

$L_Et = 'et';

$L_Liste_Activites = 'Liste des activités';
$L_Liste_Activites_Critiques = 'Liste des activités critiques';
$L_Liste_Activites_Graves = 'Liste des activités graves';
$L_Detail_Activites = 'Détail des activités';

$L_Activites_A_Redemarrer = 'Activités à redémarrer en';

$L_Liste_Applications = 'Liste des applications';
$L_Applications_A_Redemarrer = 'Applications à redémarrer en';
$L_Applications_PDMA = 'Applications avec un perte de données maximale admissible de';

$L_Liste_Personnes_Cles = 'Liste des personnes clés';

$L_Liste_Fournisseurs = 'Liste des fournisseurs';
$L_DMIA_Fournisseurs = 'Fournisseurs indispensables à partir de';

$L_Conclusion_BIAs = 'Conclusion des Bilans d\'Impact sur les Activités';

$L_Nom_G = 'Nom';

$L_Liste_Activites_Redemarrer_Par_Periode = 'Liste des activités à redémarrer par période';
$L_Liste_Applications_Redemarrer_Par_Periode = 'Liste des applications à redémarrer par période';
$L_Liste_Fournisseurs_Utiles_Par_Periode = 'Liste des fournisseurs utiles par période';
$L_Liste_Applications_Par_PDMA = 'Liste des applications par période de perte de données maximale admissible';

$L_BIA_Entite = 'BIA de l\'entité';
$L_BIAs_Societe = 'BIAs de la société';

$L_Comparateur_DMIA_Activites = 'Comparateur de DMIA sur Activités';

?>