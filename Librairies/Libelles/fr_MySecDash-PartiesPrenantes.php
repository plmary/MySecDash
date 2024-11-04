<?php
/**
* Libellés spécifiques à la gestion des Parties Prenantes.
*
* @warning Ce fichier doit impérativement être au format UTF-8 pour une gestion cohérente des caractères accentués.
*
* @license Copyleft
* @author Pierre-Luc MARY
* @date 2019-08-27
*/
	include_once( 'Constants.inc.php');

	$L_PartiePrenante = 'partie prenante';
	$L_PartiesPrenantes = 'parties prenantes';
	
	$L_Categorie = 'Catégorie';
	$L_Dependance = 'Dépendance';
	$L_Penetration = 'Pénétration';
	$L_Maturite = 'Maturité';
	$L_Confiance = 'Confiance';
	$L_Niveau_Menace = 'Niveau de menace';
	$L_Type_Support = 'Type de support';
	
	$L_PartiePrenante_Confirm_Suppression = 'Voulez-vous vraiment supprimer la ' . $L_PartiePrenante . ' "<span class="fg_couleur_1">%s</span>" ?';
	$L_PartiePrenante_Confirm_Suppression_Associe = 'Attention, la ' . $L_PartiePrenante . ' "<span class="fg_couleur_1">%s</span>" est associée avec :';

	$L_List_PartiesPrenantes = 'Liste des '. $L_PartiesPrenantes;

	$L_PartiePrenante_Creer     = 'Créer une '. $L_PartiePrenante;
	$L_PartiePrenante_Ajouter   = "Ajouter une ". $L_PartiePrenante;
	$L_PartiePrenante_Modifier  = 'Modifier une '. $L_PartiePrenante;
	$L_PartiePrenante_Supprimer = 'Supprimer une '. $L_PartiePrenante;

	$L_PartiePrenante_Cree    = ucfirst($L_PartiePrenante).' créée' ;
	$L_PartiePrenante_Ajoute  = ucfirst($L_PartiePrenante). " ajoutée";
	$L_PartiePrenante_Modifie = ucfirst($L_PartiePrenante).' modifiée' ;
	$L_PartiePrenante_Supprime  = ucfirst($L_PartiePrenante).' supprimée' ;

	$L_ERR_CREA_PartiePrenante = 'Erreur durant la création de la '. $L_PartiePrenante;
	$L_ERR_MODI_PartiePrenante = 'Erreur durant la modification de la '. $L_PartiePrenante;
	$L_ERR_DELE_PartiePrenante = 'Erreur durant la suppression de la '. $L_PartiePrenante;
	$L_ERR_DUPL_PartiePrenante = 'Cette ' . $L_PartiePrenante . ' existe déjà';

	$L_PartiePrenante = ucfirst($L_PartiePrenante);
	$L_PartiesPrenantes = ucfirst($L_PartiesPrenantes);

?>