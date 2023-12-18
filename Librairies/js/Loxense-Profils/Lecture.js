var Liste_ID_Applications, Liste_ID_Profils;
var User_Droit_Lecture, User_Droit_Ecriture, User_Droit_Modification, User_Droit_Suppression;

$(function() {
	// Charge les données du tableau.
	construireCorps();
});


function construireCorps() {
	var totalApplications, totalProfils, Acces;

	// Récupère et affiche toutes les Applications trouvées dans Loxense
	afficherApplications();

	// Récupère et affiche tous les Profils trouvés dans Loxense
	afficherProfils();

	// Parcours les profils et crée les colonnes associées.
	listerControlAcces();
}


function afficherApplications() {
	$.ajax({
		url: '../../../Loxense-Profils.php?Action=AJAX_Lister_Applications',
		type: 'POST',
		async: false,
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ){
			var statut = reponse['statut'];

			if( statut == 'success' ){
				$('div#corps_tableau').html( reponse[ 'liste_applications'] );
				$('div#entete_tableau div.row.profils div:first').attr( 'data-total_app', reponse[ 'total_applications'] );

				Liste_ID_Applications = reponse['liste_id_applications'];

				redimensionnerWindow();
			} else {
				var texteMsg = reponse['texteMsg'];

				afficherMessage( texteMsg, statut );
			}
		}
	});
}


function afficherProfils() {
	$.ajax({
		url: '../../../Loxense-Profils.php?Action=AJAX_Lister_Profils',
		type: 'POST',
		async: false,
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ){
			var statut = reponse['statut'];

			if( statut == 'success' ){
				$('div#entete_tableau div.row.profils div').after( reponse[ 'liste_profils'] );
				$('div#entete_tableau div.row:first div:nth-child(2)').attr( 'data-total_prf', reponse[ 'total_profils'] );

				Liste_ID_Profils = reponse['liste_id_profils'];

				if ( reponse['droit_suppression'] == 1 ) activerBoutonsSuppression();
				if ( reponse['droit_modification'] == 1 ) activerBoutonsModification();

				if ( reponse['total_profils'] >= reponse['limitation'] ) {
					$('button.btn-ajouter').attr('disabled', 'disabled');
				} else {
					$('button.btn-ajouter').removeAttr('disabled');
				}

				redimensionnerWindow();
			} else {
				var texteMsg = reponse['texteMsg'];

				afficherMessage( texteMsg, statut );
			}
		}
	});
}
   

function listerControlAcces() {
	$.ajax({
		url: '../../../Loxense-Profils.php?Action=AJAX_Lister_Control_Acces',
		type: 'POST',
		async: false,
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ){
			var statut = reponse['statut'];

			if( statut == 'success' ){
				var totalApplications = $('div#entete_tableau div.row.profils div:first').attr( 'data-total_app' );
				var totalProfils = $('div#entete_tableau div.row:first div:nth-child(2)').attr( 'data-total_prf' );

				var Acces = reponse['liste_acces'];

				Libelles = reponse['libelles_droits'];

				var Id_Profil, Id_Application;

				// Parcours toutes les applications.
				for( var cpt_app = 0; cpt_app < Liste_ID_Applications.length; cpt_app++ ) {
					// Parcours tous les profils.
					for( var cpt_prf = 0; cpt_prf < Liste_ID_Profils.length; cpt_prf++ ) {
						Id_Profil = Liste_ID_Profils[cpt_prf];
						Id_Application = Liste_ID_Applications[cpt_app];

						$('div#corps_tableau div.row[data-id="'+Id_Application+'"]').append(
							creerCellule( Id_Profil, Id_Application, Acces, Libelles ) );

						$('div#corps_tableau div.row[data-id="'+Id_Application+'"] div.cellule[data-prf="'+Id_Profil+'"]').on('mouseover',function() {
							var Profil_Courant = $(this).attr('data-prf');
							var Application_Courante = $(this).attr('data-app');

							$('div#corps_tableau div.row div.cellule[data-prf="'+Profil_Courant+'"]').css('background-color', '#dcafdd');
							$(this).css('background-color', '#ffcc00'); //'#dcafdd');
							$('div#entete_tableau div.profils div[data-id="'+Profil_Courant+'"]').css('color', '#ffcc00'); //'#dcafdd');
							//$('div#corps_tableau div.row[data-id="'+Application_Courante+'"]').css('color', '#ffcc00'); // 'white');
						});

						$('div#corps_tableau div.row[data-id="'+Id_Application+'"] div.cellule[data-prf="'+Id_Profil+'"]').on('mouseout',function() {
							var Profil_Courant = $(this).attr('data-prf');
							var Application_Courante = $(this).attr('data-app');
							
							$('div#corps_tableau div.row div.cellule[data-prf="'+Profil_Courant+'"]').css('background-color','');
							$('div#entete_tableau div.profils div[data-id="'+Profil_Courant+'"]').css('color', 'white'); //'#dcafdd');
							//$('div#corps_tableau div.row[data-id="'+Application_Courante+'"]').css('color', 'black'); //'#dcafdd');
						});
					}
				}

				activerBoutons();

				redimensionnerWindow();
			} else {
				var texteMsg = reponse['texteMsg'];

				afficherMessage( texteMsg, statut );
			}
		}
	});	
}


function creerCellule( Id_Profil, Id_Application, Droits, Libelles ) {
	var Droit_Lecture = ' desactive';
	var Droit_Ecriture = ' desactive';
	var Droit_Modification = ' desactive';
	var Droit_Suppression = ' desactive';
	var Droit_Max = 4;

	// Parcours tous les droits possibles et n'active que les droits de l'utilisateur en base.
	if ( Droits != '' ) {
		if ( Droits[Id_Profil+'-'+Id_Application+'-1'] !== undefined ) {
			Droit_Lecture = '';
		}

		if ( Droits[Id_Profil+'-'+Id_Application+'-2'] !== undefined ) {
			Droit_Ecriture = '';
		}

		if ( Droits[Id_Profil+'-'+Id_Application+'-3'] !== undefined ) {
			Droit_Modification = '';
		}

		if ( Droits[Id_Profil+'-'+Id_Application+'-4'] !== undefined ) {
			Droit_Suppression = '';
		}
	}

	Id_Bouton = Id_Profil+'-'+Id_Application;


	Cellule = '<div class="col-lg-1 cellule" id="'+Id_Bouton+'" data-prf="'+Id_Profil+'" data-app="'+Id_Application+'">' +
		'<button id="'+Id_Bouton+'-1" title="'+Libelles['RGH_1']+'" class="bi-eye-fill btn btn-primary btn-sm'+Droit_Lecture+' droit"></button>' +
		'<button id="'+Id_Bouton+'-2" title="'+Libelles['RGH_2']+'" class="bi-plus-circle btn btn-primary btn-sm'+Droit_Ecriture+' droit"></button>' + // plus-sign
		'<button id="'+Id_Bouton+'-3" title="'+Libelles['RGH_3']+'" class="bi-pencil-fill btn btn-primary btn-sm'+Droit_Modification+' droit"></button>' + // edit
		'<button id="'+Id_Bouton+'-4" title="'+Libelles['RGH_4']+'" class="bi-x-circle btn btn-primary btn-sm'+Droit_Suppression+' droit"></button>' + // minus-sign
		'</div>';

	return Cellule;
}


function activerBoutons() {
	$('button.droit').off( 'click' );

	$('button.droit').on( 'click', function() {
		var Id_Profil, Id_Application, Id_Droit, Droit, Activer, Id_Objet, Objet_Courant;

		Objet_Courant = $(this);
		Id_Objet = $(this).attr('id');

		if ( Objet_Courant.hasClass('desactive') ) {
			Activer = 1;
		} else {
			Activer = 0;
		}

		var Elements = Id_Objet.split('-');
		Id_Profil = Elements[0];
		Id_Application = Elements[1];
		Id_Droit = Elements[2];

		$.ajax({
			url: '../../../Loxense-Profils.php?Action=AJAX_Activer_Droit',
			type: 'POST',
			async: false,
			dataType: 'json', // le résultat est transmit dans un objet JSON
			data: $.param({'id_profil': Id_Profil, 'id_application': Id_Application, 'id_droit': Id_Droit, 'activer': Activer}),
			success: function( reponse ){
				var statut = reponse['statut'];
				var texteMsg = reponse['texteMsg'];

				afficherMessage( texteMsg, statut );

				if( statut == 'success' ){
					Objet_Courant.toggleClass('desactive');
				}
			}
		});	

	});
}
