var KEY_RETURN = 13;
var KEY_ESCAPE = 27;

var KEY_Escape = 'Escape';
var KEY_Enter = 'Enter';

var ListeExtentions = ['pdf', 'odt', 'ods', 'odp', 'docx', 'xlsx', 'pptx', 'doc', 'xls', 'ppt', 'rtf', 'txt', 'wps', 'sxw', 'log', 
	'jpg', 'jpeg', 'png', 'gif', 'tiff', 'tif', 'bmp'];

var Pile_Messages = [];


// Attend le chargement complet du DOM avant de mettre en place les écoutes sur les événéments.
$(document).ready(function(){
	// Gestion du "click" sur le bouton de "Déconnexion".
	$("#dcnx").click( function() {
		window.location = Parameters['URL_BASE'] + '/MySecDash-Connexion.php?action=DCNX';
	});


	// Gestion du "click" sur le bouton de "changement de mot de passe".
	$('#chgMdP').click( function() {
		changerMdP();
	});


	// Gestion du "redimensionnement" de la fenêtre du navigateur.
	$(window).resize( function() {
		redimensionnerWindow();
	});


	// Lance un recalcul autormatique des objets dans la fenêtre du navigateur.
	redimensionnerWindow();


	// Si le script courant n'est pas le script de connexion, alors on déclenche la supervision de l'expiration de la session de l'utilisateur.
	if ( document.URL.search(/MySecDash-Connexion/) == -1 ) {
		function controlerTempsSession() {
			$.ajax({
				url: Parameters['URL_BASE'] + '/MySecDash-Connexion.php?action=AJAX_CTRL_TEMPS_SESSION',
				type: 'POST',
				//data: $.param({'libelle': $('#temps_session').val()}),
				dataType: 'json',
				success: function(reponse) {
					if ( reponse['statut'] == 'OK' ) { // La session n'a pas expiré.
						$('#temps_session').text( reponse['expire'] );
						
						return;
					} else { // La session a expiré.
						window.location = Parameters['URL_BASE'] + '/MySecDash-Connexion.php?action=DCNX&expired';
						return -1;
					}
				},
				error: function(erreur) {
					afficherErreurSysteme( erreur );

					window.location = Parameters['URL_BASE'] + '/MySecDash-Connexion.php?action=DCNX&expired';

					return -1
				}
			});
		}

		var myVar=setInterval(controlerTempsSession, 1000 * 60); // Déclenche la fonction toutes les 60 secondes.
	}

	function sauverTempsSession() {
		$.ajax({
			url: Parameters['URL_BASE'] + '/MySecDash-Connexion.php?action=AJAX_SAUVER_TEMPS_SESSION',
			type: 'POST',
			dataType: 'json',
			success: function(response) {
				$('#temps_session').text( response['expire'] );

				return;
			}
		});
	}

	// Gestion du "click" sur le bouton de réinitialisation du temps de session.
	$('#temps_session').on('click', function() {
		sauverTempsSession();
	});


/*
	$('#titre_ecran').on('click', function() {
		sauverTempsSession();
	});

	$('#entete_tableau').on('click', function() {
		sauverTempsSession();
	});

	$('#corps_tableau').on('click', function() {
		sauverTempsSession();
	});

	$('#pied_tableau').on('click', function() {
		sauverTempsSession();
	});
*/

	// Gère l'affichage d'une fenêtre d'attente durant un traitement Ajax.
	$(document).ajaxStart(function(){
	$( "<div class=\"modal\" id=\"fenetre_attente\" tabindex=\"-1\" style=\"z-index: 3000;\">\n" +
		" <div class=\"modal-dialog\" role=\"document\">\n" +
		"  <div class=\"modal-content\">\n" +
		"   <div class=\"modal-body\">\n" +
		"	<img src=\"" + Parameters["URL_PICTURES"] + "/ajax-loader-2.gif\" /><span style=\"margin-left:20px;font-weight:bold;font-size:20px;\">" + Parameters['TravailEnCours'] + "</span>\n" +
		"   </div>\n" +
		"  </div>\n" +
		" </div>\n" +
		"</div>\n" ).prependTo('body').show();
	}).ajaxStop(function(){
		fermerFenetreAttente();
	});


	// Standardisation du traitement des erreurs AJAX.
	$(document).ajaxError( function(evenement, requete, options, erreur){
/*		$.each(evenement, function(attribut, valeur) {
			document.write('evenement : "'+ attribut + '"" = "' + valeur + "\"<br>\n");
		});

		document.write("<hr>\n");

		$.each(requete, function(attribut, valeur) {
			document.write('requete : "'+ attribut + '" = "' + valeur + "\"<br>\n");
		});

		document.write("<hr>\n");

		$.each(options, function(attribut, valeur) {
			document.write('options: "'+ attribut + '" = "' + valeur + "\"<br>\n");
		});

		document.write("<hr>\n");
*/
//		if ( requete[ 'responseText'] != '' ) {
			document.write('Erreur AJAX : "'+erreur+'"<br>'+"\n"+'Erreur à l\'exécution de la requête : "'+requete[ 'responseText']+'"<br>'+"\n");
//		}

	});


	$('#titre-menu').on('shown.bs.dropdown', function() {
		$('#c_rechercher').focus();
	});


	// ===================
	// Gestion du bouton de recherche dans les écrans à beaucoup d'occurrence
	
	if ( $('.btn-rechercher').length > 0 ) {
		$('.btn-rechercher').on('click', function() {
			var Activer;
			if ($('.row.criteres_recherche').hasClass('d-none')) {
				$('.row.criteres_recherche').removeClass('d-none');
				Activer = 1;
			} else {
				$('.row.criteres_recherche').addClass('d-none');
				Activer = 0;
			}

			$('div.row.criteres_recherche input').each(function(index, input){
				if (Activer == 1) {
					$(input).on('keyup', function(event){
						var p_event = event;
		
						if ( p_event.key == KEY_Enter ) {
							lancerRecherche();
						}
					});
				} else {
					$(input).off('keyup');
				}
			});

			redimensionnerWindow();
			
			$('.row.criteres_recherche input:first').focus();
		});
	}


	if ( $('.lancer-recherche').length > 0 ) {
		$('.lancer-recherche').on('click', function() {
			lancerRecherche();
		});
	}

	$('#corps_tableau_univers .mysecdash').on('click', function() {
		window.location = Parameters['URL_BASE'] + '/MySecDash-Principal.php';
	});

	$('#corps_tableau_univers .mycontinuity').on('click', function() {
		window.location = Parameters['URL_BASE'] + '/MyContinuity-Principal.php';
	});

	$('#corps_tableau_univers .myrisk').on('click', function() {
		alert('myrisk');
	});
});


// Recalcul la position des objets dans la fenêtre suite au redimensionnement de celle-ci.
function redimensionnerWindow() {
	var hauteur_window = $(window).height();
	
	// =======================================
	// Ajuste la position du titre de l'écran et de l'entête du tableau principal en fonction de la hauteur de la barre de navigation.
	var hauteur_navbar = $("nav").outerHeight(true);
	var hauteur_titre_ecran = $("#titre_ecran").outerHeight(true);
	var hauteur_entete_tableau = $("#entete_tableau").outerHeight(true);
	
	$("#titre_ecran").css("top", hauteur_navbar + "px");
	$("#entete_tableau").css("top",(hauteur_navbar + hauteur_titre_ecran) + "px");

	var Total = hauteur_navbar + hauteur_titre_ecran + hauteur_entete_tableau;
	if ( Total == 0 ) Total = 70;

	$("body").css("padding-top",Total + "px");
	
	
	// =======================================
	// Définit la position du "footer".
	// Si le contenu de la page est plus grand que la fenêtre alors le footer est "static",
	// sinon il est "fixed".
	var hauteur_body = $("body").outerHeight(true);
	var hauteur_footer = $("footer").outerHeight(true);
	var hauteur_body_total;

	if ($("footer").css("position") == "static") {
		hauteur_body_total = hauteur_body;
	} else {
		hauteur_body_total = hauteur_body + hauteur_footer;
	}

	//console.log(hauteur_body_total+" >= "+window_height+", "+$("footer").css("position"));
	
	if (hauteur_body_total >= hauteur_window) {
		$("footer").css("position","static");
	} else {
		$("footer").css("position","fixed");
	}
}



// Ferme la fenêtre d'attente (.ajaxstop).
function fermerFenetreAttente() {
	$('#fenetre_attente').remove();
}


// Transforme une date JJ-MM-AAAA en AAAA-MM-JJ
function convertirDate(date) {
	if (date==null||date=='') return '';

	var jour = date.substr(0, 2);
	var mois = date.substr(3, 2);
	var annee = date.substr(6, 10);

	return date = annee + '-' + mois + '-'+jour;
}


// Transforme une date AAAA-MM-JJ en JJ-MM-AAAA
function convertirDateInverse(date) {
	if (date==null||date=='') return '';

	var annee = date.substr(0, 4);
	var mois = date.substr(5, 2);
	var jour = date.substr(8, 2);

	return date = jour + '-' + mois + '-'+annee;
}


// Valider une date
function validerDate(s) {
	var Lexemes;
	var Annee, Mois, Jour;

	if ( s.indexOf('-') == -1 ) {
		if ( s.indexOf('/') == -1 ) {
			if ( s.indexOf('.') == -1 ) {
				return false;
			} else {
				Lexemes = s.split('.');
			}
		} else {
			Lexemes = s.split('/');
		}
	} else {
		Lexemes = s.split('-');
	}

	// Identifier la localisation de l'année.
	if ( Lexemes[0].length == 4 ) {
		Annee = Lexemes[0];
		Mois = Lexemes[1];
		Jour = Lexemes[2];
	} else {
		if ( Lexemes[2].length == 4 ) {
			Annee = Lexemes[2];
			Mois = Lexemes[1];
			Jour = Lexemes[0];
		} else {
			return false;
		}
	}

	// Date(year, month, day)
 	var ControleDate = new Date(Annee+'/'+Mois+'/'+Jour);

 	return !!(ControleDate && (ControleDate.getMonth() + 1) == Mois && ControleDate.getDate() == Number(Jour));
}

// Force l'affichage d'une erreur interne à l'écran.
function afficherErreurSysteme( reponse ){
	var resultat = new Array();

	$.each(reponse, function(attribut, valeur) {
		resultat[attribut]=valeur;
		//document.write(attribut + ' = ' + valeur + "\n");
	});

	document.write("Internal error: status = " + resultat['status'] + ", statusText = " + resultat['statusText'] + ", responseText = " + resultat['responseText']);
	return -1;
}


// Efface la modale active, ainsi que de toutes autres fenêtres d'information ou d'attente.
// Si la modale contient un formulaire tous les champs de saisie sont réinitialisés.
function effaceChampsModal() {
	$('.modal').modal('hide');
	$('.modal').attr('tabindex','-1');

	// Remet à vide tous les champs de la fenêtre, sauf les boutons submit
	$('.modal :input').not(':submit').not(".btn").not("[readonly]").val('');

	// On cherche toutes les balises HTML "select" de la fenêtre et on sélectionne la première option.
	$(".modal").find("select").each(function(i) {
		$(this).get(0).selectedIndex = 0;
	});

	// On décoche les "chekbox" et les boutons "radio".
	$('input[type=checkbox]').attr('checked',false);
	$('input[type=radio]').attr('checked',false);

	// On retire la fenêtre d'attente si elle est présente.
	$('#wait_message').remove();

	// On retire le backdrop (s'il est présent)
	$('div.modal-backdrop').remove();
}


// Récupère un numéro dans une chaîne de caractères.
function extractionId(chaine){
	return chaine.match(/(\d+)/)[0];
}


// Efface les messages d'alerte.
function effacerMessage( Id_Message ) {
	var Id_Message = Id_Message || '';
	var Element_Trouve = false;

	if ( Id_Message == '' ) {
		// Récupère le premier élément supprimable du tableau.
		for( Index = 0; Index < Pile_Messages.length; Index++ ) {
			if ( $('div#'+Pile_Messages[Index]).hasClass('alert-success') ) {
				Id_Message = Pile_Messages[Index];
				break;
			}
		}
	} else {
		$('div#'+Id_Message).remove();
		return;
	}

	for( Index = 0; Index < Pile_Messages.length; Index++ ) {
		if ( Pile_Messages[Index] == Id_Message ) {
			Element_Trouve = true;

			// Récupère la taille de l'élément (avant sa suppression).
			var Hauteur = $('div#'+Id_Message).outerHeight(true);
			
			// Détruit l'élément à l'écran.
			$('div#'+Id_Message).remove();

			// Détruit l'élément dans le tableau.
			Pile_Messages.splice(Index,1);
		}
		
		if ( Element_Trouve == true ) {
			// Déplace tous les éléments (vers le haut) de la hauteur de l'élément
			// qui vient d'être supprimé.
			$('div#'+Pile_Messages[Index]).animate({'top':'-='+Hauteur+'px'},'slow');
		}
	}

}


// Affiche un message d'alerte.
function afficherMessage( texteMsg, statut, elementSpecifique, delai, message_independant ) {
	var statut = statut || '';
	var delai = delai || 5;
	var elementSpecifique = elementSpecifique || 'body';
	var message_independant = message_independant || 'o'; // "o" = message indépendant de son objet parent, "n" = message dépendant de son objet parent

	var Date_Jour = new Date();
	var Id_Message = Date_Jour.getTime();
	
	var Top_Depart = 65;
	var Hauteur_Message;
	var Position_Message;
	var maClass, monImage;
	
	Pile_Messages.push( Id_Message );

	var Total = $('div.alert-dismissible').length;

	if (statut == 'success') {
		// Déclenche la fonction après l'attente "delai".
		Parameters['internal_timer_message'] = setTimeout('effacerMessage()', 1000 * delai);
		maClass = 'alert-success text-success';
		monImage = 'check2-circle';
	} else {
		maClass = 'alert-danger text-danger';
		monImage = 'x-circle';
	}

	if ( texteMsg != '' ) {
		var code_HTML = '<div id="' + Id_Message + '" class="alert alert-dismissible ' + maClass + ' fade show';

		if ( message_independant == 'o' ) code_HTML += ' loxense-alert';

		code_HTML += '" role="alert" onClick="effacerMessage( ' + Id_Message + ' );">' +
		 ' <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
		 ' <i class="bi-' + monImage + ' ' + maClass + '"></i>&nbsp;&nbsp;&nbsp;' + texteMsg +
		 '</div>\n';

		$( elementSpecifique ).append( code_HTML );

		Hauteur_Message = $('div.alert-dismissible:last-child').outerHeight( true );
		Position_Message = Top_Depart + ( Total * Hauteur_Message );

		$('div.alert-dismissible:last-child').css('top',Position_Message+'px');
	}
}


// Affiche une fenêtre modale pouvant inclure un formulaire.
function construireModal( Id_Modal, Titre, Corps, Id_Bouton, Libelle_Bouton, Bouton_Fermer, 
	Libelle_Bouton_Fermer, Nom_Formulaire, Taille_Modal, Id_Bouton_Alternatif, Libelle_Bouton_Alternatif ) {
/**
* Standardisation des écrans de type "modal".
* La standardisation définit une fenêtre complète.
*
* @license Copyleft
* @author Pierre-Luc MARY
* @date 2015-07-23
*
* @param[in] Id_Modal ID de la fenêtre "modal".
* @param[in] Titre Indique le titre à afficher en haut de la fenêtre "modal".
* @param[in] Corps Indique le message à afficher dans le corps de la fenêtre "modal".
* @param[in] Id_Bouton Indique l'ID associé au bouton primaire de la fenêtre "modal".
* @param[in] Libelle_Bouton Indique le libellé à associer au bouton primaire de la fenêtre "modal".
* @param[in] Bouton_Fermer Indique s'il faut afficher un bouton de fermeture de la fenêtre "modal".
* @param[in] Libelle_Bouton_Fermer Indique le libellé à associer au bouton de fermeture de la fenêtre "modal".
* @param[in] Nom_Formulaire Donne un nom au formulaire
* @param[in] Taille_Modal Taille de la de la fenêtre "modal" (modal-lg ou modal-sm).
* @param[in] Id_Bouton_Alternatif Permet de pouvoir gérer l'ID du du bouton alternatif qui apparaîtra entre le bouton "Fermer" et le bouton "Principal"
* @param[in] Libelle_Bouton_Alternatif Permet de pouvoir gérer le Libelle du bouton alternatif qui apparaîtra entre le bouton "Fermer" et le bouton "Principal"
*
* @return Retourne la chaîne à afficher.
*/
	var Id_Modal = Id_Modal || 'plmModal';
	var Bouton_Fermer = Bouton_Fermer || 'true';
	var Nom_Formulaire = Nom_Formulaire || '';
	var Type_Button = 'button';
	var Taille_Modal = Taille_Modal || '';
	var Id_Bouton_Alternatif = Id_Bouton_Alternatif || '';
	var Libelle_Bouton_Alternatif = Libelle_Bouton_Alternatif || '';

	var Texte = "<!-- Modal -->\n" +
		"<div class=\"modal fade\" id=\"" + Id_Modal + "\" tabindex=\"-1\" data-bs-backdrop=\"static\" aria-hidden=\"true\">\n";

	if ( Nom_Formulaire != '' ) {
		Texte += "<form id=\"" + Nom_Formulaire + "\" class=\"form-horizontal\" method=\"post\"  autocomplete=\"off\">\n";
		Type_Button = 'submit';
	}
	
	Texte += " <div class=\"modal-dialog " + Taille_Modal + "\" role=\"document\">\n" +
		"  <div class=\"modal-content\">\n" +
		"   <div class=\"modal-header\">\n" +
		"    <h4 class=\"modal-title\" id=\"" + Id_Modal + "Label\">" + Titre + "</h4>\n" +
		"    <button type=\"button\" class=\"btn-close btn-fermer\" data-bs-dismiss=\"modal\" aria-label=\"" + Libelle_Bouton_Fermer + "\"></button>\n" +
		"   </div>\n" +
		"   <div class=\"modal-body\">\n" +
		Corps + "\n" +
		"   </div>\n" +
		"   <div class=\"modal-footer\">\n";

	if ( Bouton_Fermer == 'true' || Bouton_Fermer == true ) Texte += "	<button type=\"button\" class=\"btn btn-outline-secondary btn-fermer\" data-bs-dismiss=\"modal\">" + Libelle_Bouton_Fermer + "</button>\n";

	if ( Id_Bouton_Alternatif != '' ) Texte += "	<button id=\"" + Id_Bouton_Alternatif + "\" type=\"button\" class=\"btn btn-outline-secondary btn-manuel\" data-bs-dismiss=\"modal\">" + Libelle_Bouton_Alternatif + "</button>\n";

	if ( Id_Bouton != null && Id_Bouton != '' ) Texte += "	<button id=\"" + Id_Bouton + "\" type=\"" + Type_Button + "\" class=\"btn btn-primary btn-automatique\">" + Libelle_Bouton + "</button>\n";

	Texte += "   </div>\n" +
		"  </div>\n" +
		" </div>\n";

	if ( Nom_Formulaire != '' ) {
		Texte += "</form>";
	}

	Texte += "</div>\n";

	$( Texte ).prependTo('body');

	return;
}


// Affiche la modale de changement de mot de passe.
function changerMdP( script_suivant ) {
	script_suivant = script_suivant || '';

	$.ajax({
		url: Parameters['URL_BASE'] + '/MySecDash-Connexion.php?action=AJAX_LBL_CHG_MDP',
		async: false,
		type: 'POST',
		dataType: 'json',
		success: function(reponse) {
			var Corps = //'<form method="post" id="formChgMdP" class="form-horizontal" autocomplete="off">' +
				'<div class="form-group">' +
				'<label for="O_Password" class="col-lg-4 col-form-label">' + reponse[ 'MdP' ] + '</label>' +
				'<div class="col-lg-8"><input type="password" class="form-control" id="O_Password" autofocus required></div>' +
				'</div>' +
				'<div class="form-group">' +
				'<label for="N_Password" class="col-lg-4 col-form-label">' + reponse[ 'Nouveau_MdP' ] + '</label>' +
				'<div class="col-lg-8"><input type="password" class="form-control" id="N_Password" required></div>' +
				'</div>' +
				'<div class="form-group">' +
				'<label for="C_Password" class="col-lg-4 col-form-label">' + reponse[ 'Conf_MdP' ] + '</label>' +
				'<div class="col-lg-8"><input type="password" class="form-control" id="C_Password" required></div>' +
				'</div>'; //+
				//'</form>';
			var Bouton_Fermer = true;

			if ( $("#modalChgMdP").length == 0 ) {
				construireModal( 'modalChgMdP', reponse[ 'Titre' ], Corps,
					'bouton_chg_MdP', reponse[ 'Modifier' ],
					Bouton_Fermer, reponse[ 'Fermer' ], "formChgMdP" );
			}
			
			$('#modalChgMdP').modal('show'); // Affiche la modale qui vient d'être créée

			$('#formChgMdP').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				var O_Password = $('#O_Password').val();
				var N_Password = $('#N_Password').val();
				var C_Password = $('#C_Password').val();

				$.ajax({
					url: Parameters['URL_BASE'] + '/MySecDash-Connexion.php?action=AJAX_CHG_MDP',
					type: 'POST',
					data: $.param({'O_Password': O_Password, 'N_Password': N_Password, 'C_Password': C_Password}),
					dataType: 'json',
					success: function(reponse) {
						var statut = reponse['statut'];
						var titreMsg = reponse['titreMsg'];
						var texteMsg = reponse['texteMsg'];

						if ( statut == 'success') {
							window.location = script_suivant;
						} else {
							afficherMessage( texteMsg, statut, '.modal-body' );
							return -1;
						}
					}
				});
			});

			$('.btn-fermer').click( function() {
				$('#modalChgMdP').one('hidden.bs.modal', function (e) {
					$('#modalChgMdP').remove();
				})

				$('#modalChgMdP').modal('hide');
				$('#bouton_chg_MdP').off('click');
			});
		},
		error: function(reponse) {
			var undef;
			if ( reponse === undef ) window.location = Parameters['URL_BASE'] + '/MySecDash-Connexion.php?action=DCNX&bizarre';

			afficherErreurSysteme(reponse); // Fonction dans main.js
		}
	});
}


// Ajoute des zéros au début d'un chiffre.
function ajouterZero( chiffre, taille ) {
	var taille = taille || 3;
	chiffre = chiffre.toString();
	var taille_reelle = chiffre.length;
	var ajout = '';

	if ( chiffre == 'NaN' ) chiffre = '0';

	for( var taille2 = taille - taille_reelle; taille2 > 0; taille2-- ) {
		ajout = '0' + ajout;
	}

	return ajout + chiffre;
}


function ajouterSlashes( chaine, flag ) {
	var flag = flag || 0;

	if ( flag == 0 || flag == 3 ) chaine = chaine.replace(/\\/g, "\\");
	if ( flag == 0 || flag == 1 ) chaine = chaine.replace(/'/g, "\\\'");
	if ( flag == 0 || flag == 2 ) chaine = chaine.replace(/"/g, "\\\"");

	return chaine;
}


function supprimerSlashes( chaine, flag ) {
	var flag = flag || 0;

	if ( flag == 0 || flag == 3 ) chaine = chaine.replace(/\\\\/g, "\\");
	if ( flag == 0 || flag == 1 ) chaine = chaine.replace(/\\'/g, "\'");
	if ( flag == 0 || flag == 2 ) chaine = chaine.replace(/\\\"/g, "\"");

	return chaine;
}


function protegerQuotes( chaine ) {
	chaine = chaine.replace(/'/g, "&apos;");  // (/\\'/g, "&apos;");
	chaine = chaine.replace(/"/g, "&quot;");  // (/\\\"/g, "&quot;");

	return chaine;
}


function gererBoutonAjouter( total, limitation, libelle_limitation ) {
	if ( limitation != 0 && limitation !== undefined ) {
		if ( total >= limitation ) {
			var old_title = $('div#titre_ecran button.btn-ajouter').attr('title');
			$('div#titre_ecran button.btn-ajouter').attr('data-old_title', old_title );

			$('div#titre_ecran button.btn-ajouter').attr('disabled', 'disabled').attr('title', libelle_limitation);


			$('button.btn-dupliquer').attr('data-old_title', old_title );

			$('button.btn-dupliquer').attr('disabled', 'disabled').attr('title', libelle_limitation);
		} else {
			var old_title = $('div#titre_ecran button.btn-ajouter').attr('data-old_title');

			$('div#titre_ecran button.btn-ajouter').removeAttr('disabled').attr('title', old_title);


			var old_title = $('button.btn-dupliquer').attr('data-old_title');

			$('button.btn-dupliquer').removeAttr('disabled').attr('title', old_title);
		}
	}
}


function traiterCarateresSpeciaux( Chaine ) {
	var resultat='';

	for(var i=0; Chaine[i]; i++){
		resultat+= '&#'+Chaine.charCodeAt(i)+';';
	}

	return resultat;
}


function ouvrirChamp( event, Source, Id ) {
	var p_event = event;

	// On remplace le champ modifiable par un champ de saisie.
	var Champ = $('#'+Id).find('div[data-src="' + Source + '"]');
	var Type_Champ = Champ.attr( 'data-type' );
	var Enfant = Champ.find('span[class="modifiable"]');
	var Libelle = '';
	var Attr_Champ;

	if ( Enfant.length  ) {
		// Ferme tous les précédents champs de type "input" et "number".
		$('#corps_tableau input[type="number"]').each( function( index, element ) {
			var Ancien_Source = $(this).attr('id').split('-')[0];
			var Ancien_Id = $(this).attr('id').substr(Ancien_Source.length + 1);

			fermerChamp( Ancien_Source, Ancien_Id );
		});

		Libelle = protegerQuotes( Enfant.text() );

		switch( Type_Champ ) {
		 case 'input':
		 case 'input-text':
			Attr_Champ = '<input class="form-control" type="text" ';
			break;

		 case 'input-number':
			Attr_Champ = '<input class="form-control" type="number" ';
			break;


		 case 'input-date':
			Attr_Champ = '<input class="form-control" type="date" ';
			break;
		}

		switch( Type_Champ ) {
		 case 'input':
		 case 'input-text':
		 case 'input-date':
			Attr_Champ += 'id="' + Source + '-' + Id + '" ' +
				'data-old="' + Libelle + '" ' + 
				'value="' + Libelle + '" ' +
				'onKeyDown="ouvrirChamp(event,\'' + Source + '\',\''+ Id +'\');" ' +
				//'onBlur="fermerChamp(\'' + Source + '\', \'' + Id + '\', \'\', \'blur\');" ' +
				' ';

			if ( Champ.attr('data-maximum') != undefined ) Attr_Champ += 'maxlength="' + Champ.attr('data-maximum') + '" ';

			Attr_Champ += '>';

			$( Champ.find('span[class*="modifiable"]') ).replaceWith( Attr_Champ );

			$('#' + Source + '-' + Id).focus();

			// On place le curseur après le dernier caractère.
			document.getElementById(Source + '-' + Id).selectionStart = Libelle.length;

			break;

		 case 'input-number':
			Attr_Champ += 'id="' + Source + '-' + Id + '" ' +
				'data-old="' + Libelle + '" ' + 
				'value="' + Libelle + '" ' +
				'onKeyDown="ouvrirChamp(event,\'' + Source + '\',\''+ Id +'\');" ';

			Attr_Champ += '>';

			$( Champ.find('span[class*="modifiable"]') ).replaceWith( Attr_Champ );

			$('#' + Source + '-' + Id).focus();

			// On place le curseur après le dernier caractère.
//			document.getElementById(Source + '-' + Id).selectionStart = Libelle.length;

			break;

		 case 'textarea':
			var Colonnes = Champ.attr( 'data-colonnes' ) || '';
			if ( Colonnes != '' ) Colonnes = 'cols="' + Colonnes + '" ';

			var Lignes = Champ.attr( 'data-lignes' ) || '';
			if ( Lignes != '' ) Lignes = 'rows="' + Lignes + '" ';

			$( Champ.find('span[class*="modifiable"]') ).replaceWith(
				'<textarea class="form-control" type="text" ' +
				'id="' + Source + '-' + Id + '" ' +
				'data-old="' + Libelle + '" ' + Colonnes + Lignes +
				'onKeyDown="ouvrirChamp(event,\'' + Source + '\',\''+ Id +'\');" ' +
//				'onBlur="fermerChamp(\'' + Source + '\', \'' + Id +'\');"' +
				'>' +
				Libelle + '</textarea>'
			);

			$('#' + Source + '-' + Id).focus();

			// On place le curseur après le dernier caractère.
			document.getElementById(Source + '-' + Id).selectionStart = Libelle.length;

			break;

		 case 'select':
			var Texte = '<select id="' + Source + '-' + Id + '" class="form-select" ' + 
				'onKeyUp="ouvrirChamp(event,\'' + Source + '\',\''+ Id +'\');" ' +
//				'onBlur="fermerChamp(\'' + Source + '\', \'' + Id + '\',\'' + Libelle + '\');" ' +
				'data-old="' + Libelle + '" ' +
				'>';

			var Selected = '';


			if ( Champ.attr('data-list') != undefined || Champ.attr('data-liste') !== undefined ) {
				var Liste;

				if ( Champ.attr('data-list') != undefined ) {
					Liste = Champ.attr('data-list').split(';');
				} else {
					Liste = Champ.attr('data-liste').split(';');
				}

				for(var i = 0; i < Liste.length; i++) {
					Elements = Liste[i].split('=');

					if ( Elements[1] == Libelle ) Selected = ' selected';
					else Selected = '';

					Texte += '<option value="' + Elements[0] + '"' + Selected + '>' + Elements[1] + '</option>';
				}
			} else {
				CurrentLocation = new String(window.location);
		
				if (CurrentLocation[CurrentLocation.length-1] == '#') {
					CurrentLocation = CurrentLocation.substring(0, CurrentLocation.length-1);
				}

				$.ajax({
					url: CurrentLocation + '?Action=AJAX_' + Champ.attr('data-fonction'),
					type: 'POST',
					async: false,
					dataType: 'json',
					data: $.param({'id': Id, 'libelle': Libelle}), // les paramètres sont protégés avant envoi
					success: function(reponse){
						var statut = reponse['statut'];
						var texteMsg = reponse['texteMsg'];

						if (statut == 'success') {
							Texte += texteMsg;
						} else {
							afficherMessage( texteMsg, statut, 'body' );
						}
					}
				});
			}

			Texte += '</select>';

			$( Champ.find('span[class*="modifiable"]') ).replaceWith( Texte );

			$('#' + Source + '-' + Id).focus();

			break;
		}
	}
	
	if ( p_event.key == KEY_Enter ) {
		var IdChoix = $('#' + Source + '-' + Id).val();
		var ValeurChoix = $( '#' + Source + '-' + Id + ' option:selected' ).text();

		$( 'body' ).trigger('champModifie', [Source, Id, IdChoix, ValeurChoix]);
		
		sauverChamp( Source, Id, IdChoix, ValeurChoix );
	}

	if ( p_event.key == KEY_Escape ) {
		fermerChamp( Source, Id, Libelle );
	}
}


function fermerChamp( Source, Id, Valeur ) {
	var Valeur = Valeur || '';
	var Libelle;

	var Type_Champ = document.getElementById(Source+'-'+Id).tagName.toLowerCase();

	if ( $('#'+Source+'-'+Id).length == 0 ) return;

	if ( Valeur == '' ) {
		Libelle = $('#'+Source+'-'+Id).attr('data-old');
	} else {
		Libelle = Valeur;
	}

//	$( Champ ).replaceWith('<span class="modifiable" onClick="ouvrirChamp(event,\'' + Source + '\',\''+Id+'\');">' + Libelle + '</span>');
	$( '#'+Source+'-'+Id ).replaceWith('<span class="modifiable" onClick="ouvrirChamp(event,\'' + Source + '\',\''+Id+'\');">' + Libelle + '</span>');
}


function sauverChamp( Source, Id, Valeur, Libelle ) {
	var Type_Champ = document.getElementById(Source+'-'+Id).tagName.toLowerCase();
	var Valeur_Modifiee = false;
	var Langue = '';
//	alert( 'Source: '+Source+', Id: '+Id+', Old: '+$('#'+Source+'-'+Id).attr('data-old')+', Valeur: '+Valeur+', Libelle: '+Libelle+', Type: '+Type_Champ );

	if ( Type_Champ == 'select' ) {
		if ( $('#'+Source+'-'+Id).attr('data-old') != Libelle ) Valeur_Modifiee = true;
	} else {
		if ( $('#'+Source+'-'+Id).attr('data-old') != Valeur ) Valeur_Modifiee = true;
	}

	if ( Valeur_Modifiee == true ) {
//alert( Source+', '+Id+', '+Valeur+', '+Libelle );
		if ( Id.split('-').length > 1 ) {
			var I_Id = Id.split('-');
			Langue = I_Id[1];
			I_Id = I_Id[0].split('_');
			I_Id = I_Id[I_Id.length - 1];

		} else {
			var I_Id = Id.split('_');
			I_Id = I_Id[I_Id.length - 1];
		}


		if ( $('#'+Source+'-'+Id).parent().attr('data-casse') == 'maj' ) Valeur = Valeur.toUpperCase();
		if ( $('#'+Source+'-'+Id).parent().attr('data-casse') == 'majuscule' ) Valeur = Valeur.toUpperCase();

		if ( $('#'+Source+'-'+Id).parent().attr('data-casse') == 'min' ) Valeur = Valeur.toLowerCase();
		if ( $('#'+Source+'-'+Id).parent().attr('data-casse') == 'minuscule' ) Valeur = Valeur.toLowerCase();

		CurrentLocation = new String(window.location);

		if (CurrentLocation[CurrentLocation.length-1] == '#') {
			CurrentLocation = CurrentLocation.substring(0, CurrentLocation.length-1);
		}

		$.ajax({
			url: CurrentLocation + '?Action=AJAX_Modifier_Champ',
			type: 'POST',
			data: $.param({'id': I_Id, 'source': Source, 'valeur': Valeur, 'langue': Langue}),
			dataType: 'json',
			success: function(reponse){
				var statut = reponse['statut'];
				var texteMsg = reponse['texteMsg'];
				var MAJ_Code = reponse['MAJ_Code'] || false;

				afficherMessage( texteMsg, statut, 'body' );

				if (statut == 'success') {
					if ( Type_Champ == 'select' ) Valeur = Libelle;

					fermerChamp( Source, Id, Valeur );


					if ( MAJ_Code == true ) {
						$('div[id^="'+Id.split('-')[0]+'"]').find('div[data-src="'+Source+'"]').find('span').text(Valeur);
					}
				}
			}
		});
	} else {
		if ( Type_Champ == 'select' ) Valeur = Libelle;

		fermerChamp( Source, Id, Valeur );
	}
}


function defilerPage( page, speed ) {
	var speed = speed || 'fast'; //750; // Durée de l'animation (en ms)

	//159px 190

	var hauteur_navbar = $("nav.navbar").outerHeight(true);
	var hauteur_titre_ecran = $("div#titre_ecran").outerHeight(true);
	var hauteur_entete_tableau = $("div#entete_tableau").outerHeight(true);
	
	var Total_Header = hauteur_navbar + hauteur_titre_ecran + hauteur_entete_tableau;

	$('html, body').animate( { scrollTop: ($(page).offset().top - Total_Header) }, speed ); // Go

	return false;
}


function controlerSiExtentionFichierBureautique( extention ) {
	if ( ListeExtentions.indexOf( extention.toLowerCase() ) == -1 ) return false;
	else return true;
}


function afficherExtentionsFichierBureautique() {
	var Liste = '';

	for( var i = 0; i < ListeExtentions.length; i++ ) {
		if ( Liste != '' ) Liste += ', ';

		Liste += ListeExtentions[ i ];
	}

	return Liste;
}


// Permet de déplacer les modales.
(function ($) {
	$.fn.drags = function (opt) {

		opt = $.extend({
			handle: "",
			cursor: "move"
		}, opt);

		var $selected = this;
		var $elements = (opt.handle === "") ? this : this.find(opt.handle);

		$elements.css('cursor', opt.cursor).on("mousedown", function (e) {
			var pos_y = $selected.offset().top - e.pageY,
				pos_x = $selected.offset().left - e.pageX;

			$(document).on("mousemove", function (e) {
				$selected.offset({
					top: e.pageY + pos_y,
					left: e.pageX + pos_x
				});
			}).on("mouseup", function () {
				$(this).off("mousemove"); // Unbind events from document                
			});

			e.preventDefault(); // disable selection
		});

		return this;
	};
})(jQuery);


function controlerChamp( event ) {
    var p_event = event;
    
    if ( p_event.key == KEY_Enter ) {
        $('.btn-chercher').trigger('click');
    }

    if ( p_event.key == KEY_Escape ) {
        $('#screen-menu').trigger('click');
    }

}


function filtrerOccurrences( event ) {
	var p_event = event;
	var valeur = $('#rechercher_items').val();


	if ( p_event.key == 'Backspace' ) {
		valeur = valeur.substring( 0, valeur.length - 1 );
	}

	if ( p_event.key.length == 1 ) valeur += p_event.key;

	//if ( valeur.length == 1 || valeur.length == 2 ) return;

	$('label.form-check-label').each( function( index, element ) {
		var MaChaine = $(element).html();
		MaChaine = MaChaine.replace('<span style="background-color: yellow; font-weight: bold;">','');
		MaChaine = MaChaine.replace('</span>','');
		
		var MonInput = MaChaine.indexOf('>') + 1;
		MonInput = MaChaine.substr(0, MonInput);

		if ( (Pos0 = MaChaine.toLowerCase().search(valeur.toLowerCase())) == -1 && valeur != '' ) {
			$(element).parent().hide();
		} else {
			_Tmp = MaChaine.substr(Pos0, valeur.length);
			$(element).html( MonInput + $(element).text().replace(valeur,'<span style="background-color: yellow; font-weight: bold;">'+valeur+"</span>") );
			$(element).html( MonInput + $(element).text().replace(_Tmp,'<span style="background-color: yellow; font-weight: bold;">'+_Tmp+"</span>") );
			$(element).parent().show();
		}
	});


	if ( p_event.key == KEY_Enter ) {
		event.preventDefault();
		$('#rechercher_items').focus();
		return -1;
	}

	if ( p_event.key == KEY_Escape ) {
		return -1;
	}
}


function lancerRecherche() {
	// Réaffiche toutes les occurrences avant d'appliquer les filtres de recherche.
	$('div.row.liste').each(function(index, row){
		$(row).removeClass('d-none');
	});

	// Applique le filtre de recherche.
	$('div.row.liste').each(function(index, row){
		$(row).children('div').each(function(index, div) {
			_Nom = $(div).attr('data-src');

			if (_Nom !== undefined) {
				_Rech = $('#rech_'+_Nom).val();
				if (!$(row).hasClass('d-none')) {
					if (_Rech != "") {
						_Trouve = $(div).text();
						_Rech = new RegExp(_Rech, 'i');

						if (_Rech.test(_Trouve) == true) {
							$(row).removeClass('d-none');
						} else {
							$(row).addClass('d-none');
						}
					}
				}
			}
		});
	});
}


function chercherXObjet(element) {
	var rec = document.getElementById(element).getBoundingClientRect();
	return rec.left + window.scrollX;
}

function chercherYObjet(element) {
	var rec = document.getElementById(element).getBoundingClientRect();
	return rec.top + window.scrollY;
}

function transformePrenom( prenom ) {
	prenom = prenom
		.toLowerCase()
		.split(' ')
		.map((word) => word[0].toUpperCase() + word.slice(1))
		.join(' ');

	prenom = prenom
		.toLowerCase()
		.split('-')
		.map((word) => word[0].toUpperCase() + word.slice(1))
		.join('-');

	return prenom
}