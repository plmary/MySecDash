$(function() {
	// Charge les données du tableau.
	trier( $( 'div#entete_tableau div.row div:first'), true );

	// Active l'écoute du "click" sur les libellés de l'entête du tableau.
	$('.triable').click( function() {
		trier( this, true );
	});
});


function trier( myElement, changerTri ) {
	// AJAX changeant la valeur du filtre
	var sens_recherche = $( myElement ).attr( 'data-sens-tri' );
	var changer_tri = changerTri || false;

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Trier',
		type: 'POST',
		data: $.param({'trier': sens_recherche}),
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ){
			var statut = reponse['statut'];

			if( statut == 'success' ){
				var texteMsg = reponse['texteHTML'];

				$('div#corps_tableau').html( reponse[ 'texteHTML'] );

				if ( changer_tri == true ) {
					var Element = sens_recherche.split('-');
					if ( Element[ Element.length - 1 ] == 'desc' ) {
						sens_recherche = Element[ 0 ];
					} else {
						sens_recherche = Element[ 0 ] + '-desc';
					}
				}

				// Postionne la couleur sur la colonne active sur le tri.
				$('div#entete_tableau div.row div.triable').removeClass('active');
				$(myElement).addClass('active');

				$(myElement).attr( 'data-sens-tri', sens_recherche );

				$('#totalOccurrences').text( ajouterZero(reponse[ 'total' ] ) );

				if ( reponse[ 'droit_modifier' ] == 1 ) {
					// Assigne l'événement "click" sur tous les boutons de Modification
					$('.btn-modifier').click( function( event ){
						var Id = $(this).attr('data-id');

						ModalAjouterModifier( Id );
					});
				}

				if ( reponse[ 'droit_supprimer' ] == 1 ) {
					// Assigne l'événement "click" sur tous les boutons de Suppression
					$('.btn-supprimer').click(function(){
						var Id = $(this).attr('data-id');
						var prf_libelle = $(this).parent().parent().find('div[data-src="prf_libelle"]').find('span').text();

						ModalSupprimer( Id, prf_libelle );
					});
				}

				$('[data-toggle="tooltip"]').tooltip();

				redimensionnerWindow();
			} else {
				var texteMsg = reponse['texteMsg'];

				afficherMessage( texteMsg, statut );
			}
		}
	});
}


function ModalAjouterModifier( Id ) {

	function rechercherObjetsDansOnglet(Id_Zone = '') {
		chp_rechercher_objet = new RegExp($('#chp-rechercher-objet').val(), 'i');

		$(Id_Zone+'.liste').each( function( index ){
			Valeur = $( this ).text();

			if (chp_rechercher_objet == '') {
				$( this ).show();
			} else {
				if (Valeur.search(chp_rechercher_objet) >= 0) {
					$( this ).show();
				} else {
					$( this ).hide();
				}
			}
		});
	}


	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller',
		type: 'POST',
		dataType: 'json',
		data: $.param({'prf_id': Id}),
		success: function( reponse ) {
			if ( Id != '' && Id != 0) { // Modification
				var Titre = reponse['Titre1'];
				var Bouton_Principal = reponse['L_Modifier'];
				var prf_libelle = reponse['prf_libelle'];
				var prf_description = reponse['prf_description'];
				if (prf_description == null ) {
					prf_description = '';
				}
			} else { // Création
				var Titre = reponse['Titre'];
				var Bouton_Principal = reponse['L_Ajouter'];
				var prf_libelle = '';
				var prf_description = '';
			}

			var Corps =
				'<div class="row">' +
				 '<div class="col-5">' +
				  '<div class="form-floating">' +
				   '<input type="text" class="form-control" id="prf_libelle" placeholder="' + reponse[ 'L_Libelle' ] + '" value="' + prf_libelle + '" required>' +
				   '<label for="prf_libelle">' + reponse[ 'L_Libelle' ] + '</label>' +
				  '</div> <!-- .form-floating -->' +
				 '</div> <!-- .col-5 -->' +
				 '<div class="col-7">' +
				  '<div class="form-floating">' +
				   '<textarea class="form-control" id="prf_description" placeholder="' + reponse[ 'L_Description' ] + '">' + prf_description + '</textarea>' +
				   '<label for="prf_description">' + reponse[ 'L_Description' ] + '</label>' +
				  '</div> <!-- .form-floating -->' +
				 '</div> <!-- .col-7 -->' +
				'</div> <!-- .row -->' +
				'<ul class="nav nav-tabs">' +
				 '<li><a id="liste_applications" class="nav-link active" href="#">' + reponse['L_Applications'] + '</a></li>' +
				'</ul>' +
				'<div id="zone-applications">' +
				 '<div class="row">' +
				  '<div class="col-6">' +
				   '<div class="input-group mt-1 mb-3">' +
				    '<input id="chp-rechercher-objet" class="form-control" type="text" placeholder="Rechercher" aria-label="Chercher" aria-describedby="button-addon2">' +
				    '<label id="btn-rechercher-objets" class="btn btn-outline-secondary" title="Rechercher"><i class="bi-search"></i></label>' +
				   '</div> <!-- .input-group mb-3 -->' +
				  '</div> <!-- .col-6 -->' +
				 '</div> <!-- .row -->' +
				 '<div class="row titre">' +
				  '<div class="col-4">' + reponse['L_Nom'] + '</div>' +
				  '<div class="col-4">' + reponse['L_Localisation'] + '</div>' +
				  '<div class="col-4">' + reponse['L_Droits'] + '</div>' +
				 '</div> <!-- .row .titre -->' +
				 '<div id="liste_applications" class="overflow-y-scroll" style="height: 500px;">';

				var ancien_ain_id = reponse['Liste_Applications'][0].ain_id;
				var ancien_prf_id = reponse['Liste_Applications'][0].prf_id;
				var ancien_ain_libelle = reponse['Liste_Applications'][0].ain_libelle;
				var ancien_ain_localisation = reponse['Liste_Applications'][0].ain_localisation;

				var Total_Applications = 0;

				var Old_Value_Lecture = 0;
				var Old_Value_Ecriture = 0;
				var Old_Value_Modifier = 0;
				var Old_Value_Supprimer = 0;

				var Statut_Lecture = 'desactive';
				var Statut_Ecriture = 'desactive';
				var Statut_Modifier = 'desactive';
				var Statut_Supprimer = 'desactive';

				for (i=0; reponse['Liste_Applications'][i] != null; i++) {
					Application = reponse['Liste_Applications'][i];

					if (Application.prf_id == null) {
						Application.prf_id='*';
					}

					if ( ancien_ain_libelle != Application.ain_libelle ) {

						Corps += '<div class="row liste">' +
							 '<div class="col-4">' + ancien_ain_libelle + '</div>' +
							 '<div class="col-4">' + ancien_ain_localisation + '</div>' +
							 '<div class="col-4 cellule">' +
							  '<a id="'+ancien_ain_id+'-1-'+ancien_prf_id+'" class="bi-eye-fill btn btn-primary btn-sm droit ' + Statut_Lecture + '" data-old_value="' + Old_Value_Lecture + '" title="' + reponse['L_Lecture'] + '" href="#"></a>' +
							  '<a id="'+ancien_ain_id+'-2-'+ancien_prf_id+'" class="bi-plus-circle btn btn-primary btn-sm droit ' + Statut_Ecriture + '" data-old_value="' + Old_Value_Ecriture + '" title="' + reponse['L_Ecriture'] + '" href="#"></a>' +
							  '<a id="'+ancien_ain_id+'-3-'+ancien_prf_id+'" class="bi-pencil-fill btn btn-primary btn-sm droit ' + Statut_Modifier + '" data-old_value="' + Old_Value_Modifier + '" title="' + reponse['L_Modifier'] + '" href="#"></a>' +
							  '<a id="'+ancien_ain_id+'-4-'+ancien_prf_id+'" class="bi-x-circle btn btn-primary btn-sm droit ' + Statut_Supprimer + '" data-old_value="' + Old_Value_Supprimer + '" title="' + reponse['L_Supprimer'] + '" href="#"></a>' +
							 '</div> <!-- .col-4 .cellule -->' +
							'</div> <!-- .row .liste -->';

						Old_Value_Lecture = 0;
						Old_Value_Ecriture = 0;
						Old_Value_Modifier = 0;
						Old_Value_Supprimer = 0;

						Statut_Lecture = 'desactive';
						Statut_Ecriture = 'desactive';
						Statut_Modifier = 'desactive';
						Statut_Supprimer = 'desactive';

						ancien_ain_id = Application.ain_id;
						ancien_prf_id = reponse['Liste_Applications'][0].prf_id;
						ancien_ain_libelle = Application.ain_libelle;
						ancien_ain_localisation = Application.ain_localisation;
					}

					switch ( Application.drt_id ) {
					 case 1:
						Old_Value_Lecture = 1;
						Statut_Lecture = '';
						break;

					 case 2:
						Old_Value_Ecriture = 1;
						Statut_Ecriture = '';
						break;

					 case 3:
						Old_Value_Modifier = 1;
						Statut_Modifier = '';
						break;

					 case 4:
						Old_Value_Supprimer = 1;
						Statut_Supprimer = '';
						break;
					}

					Total_Applications += 1;
				}
			Corps += '</div> <!-- #liste_applications -->' +
				'</div> <!-- #zone-applications -->';


			construireModal( 'idModalProfil',
				Titre,
				Corps,
				'idBoutonAjouter', Bouton_Principal,
				true, reponse[ 'L_Fermer' ],
				'formAjouterProfil', 'modal-xxl' );

			$('#idModalProfil').modal('show'); // Affiche la modale qui vient d'être créée

			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModalProfil').on('shown.bs.modal', function() {
				$('#prf_libelle').focus();
			});

			$('#chp-rechercher-objet').off('keyup').on('keyup', function( eventKey){
				rechercherObjetsDansOnglet('div#liste_applications ');
			});

			$('a.droit').on('click', function() {
				//alert($(this).attr('id'));
				$(this).toggleClass('desactive');
			});

			// Supprime la modale après l'avoir caché.
			$('#idModalProfil').on('hidden.bs.modal', function() {
				$('#idModalProfil').remove();
			});

			$('#formAjouterProfil').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				if ( Id != null && Id != '') {
					ModifierProfil( Id );
				} else {
					AjouterProfil();
				}
			} );
		}
	});
}


function afficherApplications() {
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Lister_Applications',
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
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Lister_Profils',
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
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Lister_Control_Acces',
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
			url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Activer_Droit',
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
