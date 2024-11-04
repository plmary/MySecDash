$(function() {
	// Charge les données du tableau.
	//trier( $( 'div#entete_tableau div.row div:first' ), true );

	// Charge la matrice en fonction de la campagne par défaut
	//cmp_id = $('#s_cmp_id').val();
	charger();

	// Active l'écoute du "select" sur le changement de Société.
	$('#s_sct_id').change(function() {
		//var sens_recherche = $( 'div#entete_tableau div.row div:first' ).attr( 'data-sens-tri' );
		var sct_id = $('#s_sct_id').val();

		$.ajax({
			url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Selectioner_Societe',
			type: 'POST',
			data: $.param({'sct_id': sct_id }),
			dataType: 'json', // le résultat est transmit dans un objet JSON
			success: function(reponse) {
				var statut = reponse['statut'];
				var texteMsg = reponse['texteMsg'];

				if (statut == 'success') {
					$('#s_cmp_id option').remove();

					if ( reponse['Liste_Campagnes'] != [] && reponse['Liste_Campagnes'] != '' ) {
						if (reponse['Liste_Campagnes'].length > 1) {
							// Mise à jour de la liste déroulante des Campagnes associées à la Société
							for (let element of reponse['Liste_Campagnes']) {
								$('#s_cmp_id').append('<option value="' + element.cmp_id + '">' + element.cmp_date + '</option>');
							}
						} else {
							$('#s_cmp_id').val(reponse['Liste_Campagnes'][0].cmp_date);
						}
					} else {
						$('#s_cmp_id').val('---');
					}

					charger();
				}

				afficherMessage(texteMsg, statut);
			}
		});
	});

	// Active l'écoute du "select" sur le changement de Campagne.
	$('#s_cmp_id').change(function() {
		//var sens_recherche = $( 'div#entete_tableau div.row div:first' ).attr( 'data-sens-tri' );
		var cmp_id = $('#s_cmp_id').val();

		$.ajax({
			url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Selectioner_Campagne',
			type: 'POST',
			data: $.param({/*'trier': sens_recherche,*/ 'cmp_id': cmp_id }),
			dataType: 'json', // le résultat est transmit dans un objet JSON
			success: function(reponse) {
				var statut = reponse['statut'];

				if (statut == 'success') {
					var texteMsg = reponse['texteMsg'];

					afficherMessage(texteMsg, statut);

					charger(reponse['cmp_id']);
				} else {
					var texteMsg = reponse['texteMsg'];

					afficherMessage(texteMsg, statut);
				}
			}
		});
	});
});


function charger() {
	// AJAX permettant de charger la matrice d'impacts associée à cette Campagne

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller',
		type: 'POST',
		//data: $.param({ 'cmp_id': cmp_id }),
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function(reponse) {
			var statut = reponse['statut'];
			var cmp_id = reponse['cmp_id'];

			if (statut == 'success') {
				if (cmp_id == '' || reponse['Liste_Campagnes'] == undefined) {
					$('#corps_ecran').html(
						'<div id="corps_alert" class="container-fluid text-center" style="top: 174px;">' +
						'<div class="col-12">' +
						'<h2 style="margin: 50px auto;">' + reponse['L_Societe_Sans_Campagne'] + '</h2>' +
						'</div>' +
						'<div class="col-12">' +
						'<a class="btn btn-primary" role="button" href="' + Parameters['URL_BASE'] + '/MyContinuity-Campagnes.php">' + reponse['L_Gestion_Campagnes'] + '</a>' +
						'</div>' +
						'</div>'
					);
				} else {
					// Mise à jour du corps du tableau.
					Occurrence = '';

					if (reponse['Liste_Niveaux_Impact'] != undefined) {
						for (let Niveau_Impact of reponse['Liste_Niveaux_Impact']) {
							Occurrence += '<div class="row niveau-impact" data-poids="'+Niveau_Impact.nim_poids+'" data-nim_id="'+Niveau_Impact.nim_id+'">' +
								'<div class="d-grid gap-2 col-plm-2" style="background-color: #'+Niveau_Impact.nim_couleur+';">' +
								'<button class="btn btn-light border btn-modifier-niveau" id="nim_id-'+Niveau_Impact.nim_id+'">'+Niveau_Impact.nim_poids+' - '+Niveau_Impact.nim_nom_code+'</button>' +
								'</div> <!-- .col-plm-2 -->';

							if (reponse['Liste_Types_Impact'] != undefined) {
								for (let Type_Impact of reponse['Liste_Types_Impact']) {
									if (reponse['Liste_Matrice_Impacts'][Niveau_Impact.nim_id+'-'+Type_Impact.tim_id] != undefined) {
										Description = reponse['Liste_Matrice_Impacts'][Niveau_Impact.nim_id+'-'+Type_Impact.tim_id].mim_description;
										mim_id = reponse['Liste_Matrice_Impacts'][Niveau_Impact.nim_id+'-'+Type_Impact.tim_id].mim_id;
									} else {
										Description = '';
										mim_id = '';
									}
									Occurrence += '<div class="col-plm-2 cellule-impact" style="background-color: #'+Niveau_Impact.nim_couleur+';" data-tim_id="'+Type_Impact.tim_id+'">' +
										'<span id="description-'+Niveau_Impact.nim_id+'-'+Type_Impact.tim_id+'">'+Description+'</span>';

									if ( reponse['Droit_Modifier'] == true ) {
										Occurrence += '<button type="button" class="btn btn-sm btn-outline-secondary btn-maj btn-modifier-cell" data-nim_id="'+Niveau_Impact.nim_id+'" data-tim_id="'+Type_Impact.tim_id+'" data-mim_id="'+mim_id+'" title="'+reponse['L_Modifier']+'"><i class="bi-pencil" width="32"></i></button>';
									}

									Occurrence += '</div> <!-- .col-plm-2 .cellule_impact -->';
								}
							}
							Occurrence += '</div> <!-- .row -->';
						}
					}

					if (reponse['Liste_Types_Impact'].length > 0) {
						Desactiver_S_Type = '';
					} else {
						Desactiver_S_Type = 'disabled';
					}

					if (reponse['Liste_Types_Impact'].length >= 5) {
						Desactiver_A_Type = 'disabled';
					} else {
						Desactiver_A_Type = '';
					}

					if (reponse['Liste_Niveaux_Impact'].length > 0) {
						Desactiver_S_Niveau = '';
					} else {
						Desactiver_S_Niveau = 'disabled';
					}

					Tableau_Complet = '<div id="entete_tableau" class="container-fluid" style="top: 133.65px;">' +
						'<div class="row">' +
						'<div class="col-plm-2 rotation-10 representation">' +
						reponse['L_Type'] + '<hr>' + reponse['L_Niveau'] +
						'</div> <!-- .representation -->';

					if (reponse['Liste_Types_Impact'].length > 0) {
						for (let Type_Impact of reponse['Liste_Types_Impact']) {
							Tableau_Complet += '<div class="d-grid gap-2 col-plm-2 type-impact" data-tim_poids="'+Type_Impact.tim_poids+'" data-tim_id="'+Type_Impact.tim_id+'">' +
								'<button class="btn btn-light btn-modifier-type" type="role" id="tim_id-'+Type_Impact.tim_id+'">'+Type_Impact.tim_nom_code+'</button>' +
								'</div> <!-- .col-plm-2 -->';
						}
					}

					Tableau_Complet += '<div class="col-plm-1">' +
						'<div class="btn-group" role="group">';

					if ( reponse['Droit_Ajouter'] == true ) {
						Tableau_Complet += '<button class="btn btn-light btn-ajouter-type" ' + Desactiver_A_Type + ' type="role" title="' + reponse['L_Ajouter_Type_Impact'] + '"><span class="bi-plus"></span></button>';
					}

					if ( reponse['Droit_Supprimer'] == true ) {
						Tableau_Complet += '<button class="btn btn-light btn-supprimer-type" ' + Desactiver_S_Type + ' type="role" title="' + reponse['L_Supprimer_Type_Impact'] + '"><span class="bi-dash"></span></button>';
					}

					Tableau_Complet += '</div> <!-- .btn-group -->' +
						'</div> <!-- .col-plm-1 -->' +
						'</div> <!-- .row -->' +
						'</div> <!-- #entete_tableau -->' +

						'<div id="corps_tableau" class="container-fluid">' + Occurrence + '</div>' +

						'<div id="pied_tableau" class="container-fluid">' +
						'<div class="row">' +
						'<div class="col-plm-1">' +
						'<div class="btn-group" role="group">';

					if ( reponse['Droit_Ajouter'] == true ) {
						Tableau_Complet += '<button class="btn btn-light btn-ajouter-niveau" type="role" title="' + reponse['L_Ajouter_Niveau_Impact'] + '"><span class="bi-plus"></span></button>';
					}

					if ( reponse['Droit_Supprimer'] == true ) {
						Tableau_Complet += '<button class="btn btn-light btn-supprimer-niveau" ' + Desactiver_S_Niveau + ' type="role" title="' + reponse['L_Supprimer_Niveau_Impact'] + '"><span class="bi-dash"></span></button>';
					}

					Tableau_Complet += '</div> <!-- .btn-group -->' +
						'</div> <!-- .col-plm-1 -->' +
						'</div> <!-- .row -->' +
						'</div> <!-- #pied_tableau -->';

					$('#corps_ecran').html( Tableau_Complet );
				}

				$('.btn-ajouter-niveau').on('click', function(event) {
					ModalMaJNiveau();
				});

				$('.btn-supprimer-niveau').on('click', function(event) {
					ModalSuppNiveau();
				});

				$('.btn-modifier-niveau').on('click', function(event) {
					nim_id = $(this).attr('id').split('-')[1];

					ModalMaJNiveau(nim_id);
				});


				$('.btn-ajouter-type').on('click', function(event) {
					ModalMaJType();
				});

				$('.btn-supprimer-type').on('click', function(event) {
					ModalSuppType();
				});

				$('.btn-modifier-type').on('click', function(event) {
					tim_id = $(this).attr('id').split('-')[1];

					ModalMaJType(tim_id);
				});

				$('.btn-modifier-cell').on('click', function(event) {
					ModalMaJCell($(this).attr('data-nim_id'), $(this).attr('data-tim_id'), $(this).attr('data-mim_id'));
				});

				redimensionnerWindow();
			} else {
				var texteMsg = reponse['texteMsg'];

				afficherMessage(texteMsg, statut);
			}
		}
	});
}


// ============================================

function ModalMaJNiveau(nim_id='') {
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller',
		type: 'POST',
		data: $.param({ 'nim_id': nim_id }),
		dataType: 'json',
		success: function(reponse) {
			var statut = reponse['statut'],
				selected_1 = '', selected_2 = '', selected_3 = '', selected_4 = '', selected_5 = '',
				selected_6 = '', selected_7 = '', selected_8 = '', selected_9 = '', selected_10 = '',
				selected_11 = '', selected_12 = '', selected_13 = '', selected_14 = '', selected_15 = '';

			if (statut == 'success') {
				if (reponse['nim'] == undefined) {
					tmp_poids = $('.niveau-impact').length + 1;
					nim_numero = tmp_poids;
					nim_nom_code = '';
				} else {
					tmp_poids = reponse['nim'][0].nim_poids;
					nim_numero = reponse['nim'][0].nim_numero;
					nim_nom_code = reponse['nim'][0].nim_nom_code;
				}

				Corps = '<div class="row">' +
					'<div class="mb-3 col-4">' +
					'<label class="col-6 col-form-label" for="nim_poids">' + reponse['L_Poids'] + '</label>' +
					'<div class="col-6">' +
					'<input id="nim_poids" class="form-control" disabled type="text" value="' + tmp_poids + '">' +
					'</div> <!-- .col-lg-2 -->' +
					'</div> <!-- .mb-3 -->' +
					'<div class="mb-3 col-6">' +
					'<label class="col-5 col-form-label" for="nim_numero">' + reponse['L_Niveau'] + '</label>' +
					'<div class="col-5">' +
					'<input id="nim_numero" class="form-control" type="number" value="' + nim_numero + '" required>' +
					'</div> <!-- .col-lg-2 -->' +
					'</div> <!-- .mb-3 -->' +
					'</div> <!-- .row -->' +
	
					'<div class="mb-3">' +
					'<label class="col-lg-2 col-form-label" for="nim_nom_code">' + reponse['L_Nom'] + '</label>' +
					'<div class="col-lg-8">' +
					'<input id="nim_nom_code" class="form-control" type="text" value="' + nim_nom_code + '" required>' +
					'</div> <!-- .col-lg-8 -->' +
	
					'</div> <!-- .mb-3 -->' +
					'<div class="mb-3">' +
					'<label class="col-lg-2 col-form-label" for="nim_couleur">' + reponse['L_Couleur'] + '</label>' +
					'<div class="col-lg-1">' +
					'<select id="nim_couleur" class="rnr_code_couleur" required>';

				if (reponse['nim'] != undefined) {
					switch (reponse['nim'][0].nim_couleur.toLowerCase()) {
					 case 'c0392b':
						selected_1 = ' selected';
						break;

					 case 'e74c3c':
						selected_2 = ' selected';
						break;

					 case '9b59b6':
						selected_3 = ' selected';
						break;

					 case '8e44ad':
						selected_4 = ' selected';
						break;

					 case '2980b9':
						selected_5 = ' selected';
						break;

					 case '3498db':
						selected_6 = ' selected';
						break;

					 case '1abc9c':
						selected_7 = ' selected';
						break;

					 case '16a085':
						selected_8 = ' selected';
						break;

					 case '27ae60':
						selected_9 = ' selected';
						break;

					 case '2ecc71':
						selected_10 = ' selected';
						break;

					 case 'f1c40f':
						selected_11 = ' selected';
						break;

					 case 'f39c12':
						selected_12 = ' selected';
						break;

					 case 'e67e22':
						selected_13 = ' selected';
						break;

					 case 'd35400':
						selected_14 = ' selected';
						break;

					 case '000000':
						selected_15 = ' selected';
						break;
					}
					
				}

				Corps += '<option data-color="#c0392b" value="1"'+selected_1+'>1</option>' +
					'<option data-color="#e74c3c" value="2"'+selected_2+'>2</option>' +
					'<option data-color="#9b59b6" value="3"'+selected_3+'>3</option>' +
					'<option data-color="#8e44ad" value="4"'+selected_4+'>4</option>' +
					'<option data-color="#2980b9" value="5"'+selected_5+'>5</option>' +
					'<option data-color="#3498db" value="6"'+selected_6+'>6</option>' +
					'<option data-color="#1abc9c" value="7"'+selected_7+'>7</option>' +
					'<option data-color="#16a085" value="8"'+selected_8+'>8</option>' +
					'<option data-color="#27ae60" value="9"'+selected_9+'>9</option>' +
					'<option data-color="#2ecc71" value="10"'+selected_10+'>10</option>' +
					'<option data-color="#f1c40f" value="11"'+selected_11+'>11</option>' +
					'<option data-color="#f39c12" value="12"'+selected_12+'>12</option>' +
					'<option data-color="#e67e22" value="13"'+selected_13+'>13</option>' +
					'<option data-color="#d35400" value="14"'+selected_14+'>14</option>' +
					'<option data-color="#000000" value="15"'+selected_15+'>15</option>';

				Corps += '</select>' +
					'</div> <!-- .col-lg-1 -->' +
					'</div> <!-- .mb-3 -->';

				if (nim_id == '') {
					Titre = reponse['L_Ajouter_Niveau_Impact'];
					Bouton = reponse['L_Ajouter'];
				} else {
					Titre = reponse['L_Modifier_Niveau_Impact']
					Bouton = reponse['L_Modifier'];
				}

				construireModal('idModalMaJNiveau',
					Titre,
					Corps,
					'idBoutonAjouter', Bouton,
					true, reponse['L_Fermer'],
					'formCreerNiveau');
	
				// Affiche la modale qui vient d'être créée
				$('#idModalMaJNiveau').modal('show');
	
				// Attend que la modale soit affichée avant de donner le focus au champ.
				$('#idModalMaJNiveau').on('shown.bs.modal', function() {
					// Attend que la modale soit affichée avant de donner le focus au champ.
					$('#nim_nom_code').focus();

					// On place le curseur après le dernier caractère.
					document.getElementById('nim_nom_code').selectionStart = nim_nom_code.length;

					$('#nim_couleur').colorselector();

					$('.dropdown-toggle').on('click', function() {
						$('.dropdown-caret').toggleClass('d-block').css("top", "32px").css("left", "2px");
					});

					$('.color-btn').on('click', function() {
						$('.dropdown-caret').toggleClass('d-block');
					});
				});

				$('#idModalMaJNiveau').on('hidden.bs.modal', function() {
					$('#idModalMaJNiveau').remove(); // Supprime la modale d'ajout.
					$('.dropdown-toggle').off('click');
				});
	
				$('#formCreerNiveau').submit(function(event) { // Gère la soumission du formulaire.
					event.preventDefault(); // Laisse le contrôle au Javascript.
	
					if (nim_id == '') {
						AjouterNiveau();
					} else {
						ModifierNiveau(nim_id);
					}
				});
			} else {
				var texteMsg = reponse['texteMsg'];

				afficherMessage(texteMsg, statut);
			}
		}
	});
}


function ModalMaJType(tim_id='') {
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller',
		type: 'POST',
		data: $.param({ 'tim_id': tim_id }),
		dataType: 'json',
		success: function(reponse) {
			var statut = reponse['statut'];

			if (statut == 'success') {
				if (reponse['tim'] == undefined) {
					tim_nom_code = '';
					tim_poids = $('.type-impact').length + 1;
				} else {
					tim_nom_code = reponse['tim'][0].tim_nom_code;
					tim_poids = reponse['tim'][0].tim_poids;
				}

				Corps = '<div class="row mb-3">' +
					'<label class="col-lg-2 col-form-label" for="tim_poids">' + reponse['L_Poids'] + '</label>' +
					'<div class="col-lg-2">' +
					'<input id="tim_poids" class="form-control" type="text" value="' + tim_poids + '" disabled>' +
					'</div> <!-- .col-lg-8 -->' +
					'</div> <!-- .row .mb-3 -->' +
					'<div class="row mb-3">' +
					'<label class="col-lg-2 col-form-label" for="tim_nom_code">' + reponse['L_Nom'] + '</label>' +
					'<div class="col-lg-8">' +
					'<input id="tim_nom_code" class="form-control" type="text" value="' + tim_nom_code + '" required>' +
					'</div> <!-- .col-lg-8 -->' +
					'</div> <!-- .row .mb-3 -->';

				if (tim_id == '') {
					Titre = reponse['L_Ajouter_Type_Impact'];
					Bouton = reponse['L_Ajouter'];
				} else {
					Titre = reponse['L_Modifier_Type_Impact']
					Bouton = reponse['L_Modifier'];
				}

				construireModal('idModalMaJType',
					Titre,
					Corps,
					'idBoutonAjouter', Bouton,
					true, reponse['L_Fermer'],
					'formCreerType');
	
				// Affiche la modale qui vient d'être créée
				$('#idModalMaJType').modal('show');
	
				// Attend que la modale soit affichée avant de donner le focus au champ.
				$('#idModalMaJType').on('shown.bs.modal', function() {
					// Attend que la modale soit affichée avant de donner le focus au champ.
					$('#tim_nom_code').focus();

					// On place le curseur après le dernier caractère.
					document.getElementById('tim_nom_code').selectionStart = tim_nom_code.length;
				});

				$('#idModalMaJType').on('hidden.bs.modal', function() {
					$('#idModalMaJType').remove(); // Supprime la modale d'ajout.
					$('.dropdown-toggle').off('click');
				});
	
				$('#formCreerType').submit(function(event) { // Gère la soumission du formulaire.
					event.preventDefault(); // Laisse le contrôle au Javascript.
	
					if (tim_id == '') {
						AjouterType();
					} else {
						ModifierType(tim_id);
					}
				});
			} else {
				var texteMsg = reponse['texteMsg'];

				afficherMessage(texteMsg, statut);
			}
		}
	});
}


function ModalMaJCell( nim_id, tim_id, mim_id ) {
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller&Charger_Cellule',
		type: 'POST',
		data: $.param({ 'nim_id': nim_id, 'tim_id': tim_id }),
		dataType: 'json',
		success: function(reponse) {
			var statut = reponse['statut'];

			if (statut == 'success') {
				if (reponse['mim'].length == 0) {
					Titre = reponse['L_Ajouter_Description_Impact'];
					Bouton = reponse['L_Ajouter'];
					mim_description = '';
					//mim_id = '';
				} else {
					Titre = reponse['L_Modifier_Description_Impact']
					Bouton = reponse['L_Modifier'];
					mim_description = reponse['mim'][0].mim_description;
					//mim_id = reponse['mim'][0].mim_id;
				}

				Corps = '<div class="row mb-3">' +
					'<label class="col-lg-2 col-form-label" for="libelle_type">' + reponse['L_Type'] + '</label>' +
					'<div class="col-lg-10">' +
					'<input id="libelle_type" class="form-control" type="text" value="' + reponse['tim'][0].tim_nom_code + '" disabled>' +
					'</div> <!-- .col-lg-10 -->' +
					'</div> <!-- .row .mb-3 -->' +
					'<div class="row mb-3">' +
					'<label class="col-lg-2 col-form-label" for="tim_nom_code">' + reponse['L_Niveau'] + '</label>' +
					'<div class="col-lg-10">' +
					'<input id="tim_nom_code" class="form-control" type="text" value="' + reponse['nim'][0].nim_poids + ' - ' + reponse['nim'][0].nim_nom_code + '" disabled>' +
					'</div> <!-- .col-lg-10 -->' +
					'</div> <!-- .row .mb-3 -->' +
					'<div class="mb-3">' +
					'<label class="col-lg-2 col-form-label" for="mim_description">' + reponse['L_Description'] + '</label>' +
					'<div class="col-lg-12">' +
					'<div id="mim_description" class="summernote" rows="3">' + mim_description + '</div>' +
					'</div> <!-- .col-lg-12 -->' +
					'</div> <!-- .mb-3 -->';

				construireModal('idModalMaJDescription',
					Titre,
					Corps,
					'idBoutonAjouter', Bouton,
					true, reponse['L_Fermer'],
					'formCreerDescription', 'modal-lg');
	
				// Affiche la modale qui vient d'être créée
				$('#idModalMaJDescription').modal('show');
	
				// Attend que la modale soit affichée avant de donner le focus au champ.
				$('#idModalMaJDescription').on('shown.bs.modal', function() {

					$('.summernote').on('transform-summernote', function(){
						$('.note-toolbar button.dropdown-toggle').each( function( index ) {
							$(this).removeAttr('data-toggle').attr('data-bs-toggle', 'dropdown');
						});
					});

					$('.summernote').summernote({
						toolbar: [
						 ['style', ['style']],
						 ['font', ['bold', 'underline', 'clear']],
						 ['fontname', ['fontname']],
						 ['color', ['color']],
						 ['para', ['ul', 'ol', 'paragraph']],
						 ['table', ['table']],
						// ['insert', ['link', 'picture', 'video']],
						 ['view', ['fullscreen', 'codeview']], //, 'help']],
						]
					}).trigger('transform-summernote');

					// Attend que la modale soit affichée avant de donner le focus au champ.
					$('#mim_description').focus();

					// On place le curseur après le dernier caractère.
					document.getElementById('mim_description').selectionStart = mim_description.length;
				});

				$('#idModalMaJDescription').on('hidden.bs.modal', function() {
					$('#idModalMaJDescription').remove(); // Supprime la modale d'ajout.
					$('.dropdown-toggle').off('click');
				});
	
				$('#formCreerDescription').submit(function(event) { // Gère la soumission du formulaire.
					event.preventDefault(); // Laisse le contrôle au Javascript.
	
					if (mim_id == '') {
						AjouterDescription(nim_id, tim_id);
					} else {
						ModifierDescription(mim_id, nim_id, tim_id);
					}
				});
			} else {
				var texteMsg = reponse['texteMsg'];

				afficherMessage(texteMsg, statut);
			}
		}
	});
}
