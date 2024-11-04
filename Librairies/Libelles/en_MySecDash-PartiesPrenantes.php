<?php
/**
* Libellés spécifiques à la gestion des Sources de Menaces.
*
* @warning Ce fichier doit impérativement être au format UTF-8 pour une gestion cohérente des caractères accentués.
*
* @license Copyleft
* @author Pierre-Luc MARY
* @date 2014-11-04
*/
	include_once( 'Constants.inc.php');

	$L_PartiePrenante = 'stakeholder';
	$L_PartiesPrenantes = 'stakeholders';
	
	$L_Categorie = 'Category';
	$L_Dependance = 'Dependency';
	$L_Penetration = 'Penetration';
	$L_Maturite = 'Maturity';
	$L_Confiance = 'Trust';
	$L_Niveau_Menace = 'Threat level';
	$L_Type_Support = 'Support asset type';
	
	$L_PartiePrenante_Confirm_Suppression = 'Do you really want to remove ' . $L_PartiePrenante . ' "<span class="fg_couleur_1">%s</span>"?';
	$L_PartiePrenante_Confirm_Suppression_Associe = 'Warning, the ' . $L_PartiePrenante . ' "<span class="fg_couleur_1">%s</span>" is associated with:';

	$L_List_PartiePrenantes = ucfirst($L_PartiesPrenantes) . ' list';

	$L_PartiePrenante_Creer     = 'Create a '. $L_PartiePrenante;
	$L_PartiePrenante_Ajouter   = "Add a ". $L_PartiePrenante;
	$L_PartiePrenante_Modifier  = 'Modify '. $L_PartiePrenante;
	$L_PartiePrenante_Supprimer = 'Remove '. $L_PartiePrenante;

	$L_PartiePrenante_Cree    = ucfirst($L_PartiePrenante).' created' ;
	$L_PartiePrenante_Ajoute  = ucfirst($L_PartiePrenante). " added";
	$L_PartiePrenante_Modifie = ucfirst($L_PartiePrenante).' modified' ;
	$L_PartiePrenante_Supprime  = ucfirst($L_PartiePrenante).' deleted' ;

	$L_ERR_CREA_PartiePrenante = 'An error occurred during the creation of the '. $L_PartiePrenante;
	$L_ERR_MODI_PartiePrenante = 'An error occurred during the modification of the '. $L_PartiePrenante;
	$L_ERR_DELE_PartiePrenante = 'An error occurred during the removal of the '. $L_PartiePrenante;
	$L_ERR_DUPL_PartiePrenante = 'This ' . $L_PartiePrenante . ' already exists';

	$L_PartiePrenante = ucfirst($L_PartiePrenante);
	$L_PartiesPrenantes = ucfirst($L_PartiesPrenantes);

?>