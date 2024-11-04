$(function() {
	// Charge les données du tableau.
	trier( $( 'div#entete_tableau div.row div:first'), false );


	// Active l'écoute du "click" sur les libellés de l'entête du tableau.
	$('.triable').click( function() {
		trier( this, true );
	});


	if ( $('.btn-chercher').length > 0 ) {
		$('.btn-chercher').on('click', function() {
			trier( $('div.active'), false, $('#c_rechercher').val() );
		});
	}
});


function trier( myElement, changerTri, tags, chercher ) {
	// AJAX changeant la valeur du filtre
	var sens_recherche = $( myElement ).attr( 'data-sens-tri' );
	var changer_tri = changerTri || false;

	$.ajax({
		url: '../../../Loxense-ExportBase.php?Action=AJAX_Trier',
		type: 'POST',
		data: $.param({'trier': sens_recherche, 'chercher': chercher}),
		dataType: 'json', // le résultat est transmit dans un objet JSON

		success: function( reponse ){
			var statut = reponse['statut'];

			if( statut == 'success' ){
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


				$('.btn-exporter').on('click', function() {
					var Id = $(this).attr('data-id');

					choixExporterSauvegarde( Id );
				});


				$('.btn-restaurer').on('click', function() {
					var Id = $(this).attr('data-id');

					choixRestaurerBase( Id );
				});


				// Assigne l'événement "click" sur tous les boutons de Suppression
				$('.btn-supprimer').click(function(){
					var sav_id = $(this).attr('data-id');

					ModalConfirmerSuppression( sav_id );
				});


				redimensionnerWindow();
			} else {
				var texteMsg = reponse['texteMsg'];

				afficherMessage( texteMsg, statut );
			}
		}
	});
}


function ModalVisualiser( rcs_id ){
	$.ajax({
		url: '../../../Loxense-AppreciationRisquesTags.php?Action=AJAX_Libeller',
		type: 'POST',
		//async: false,
		dataType: 'json',
		data: $.param({'Charger_Risque': rcs_id}),

		success: function( reponse ) {
			if ( reponse[ 'statut'] != 'success' ) {
				afficherMessage( reponse['texteMsg'], reponse['statut'], 'body' );
				return -1;
			}

			var evr_actif = '';

			if ( reponse[ 'Risque' ][0].evr_libelle == null ) reponse[ 'Risque' ][0].evr_libelle = '';
			else evr_actif = ' disabled';

			if ( reponse[ 'rcs_scenario' ] == null ) reponse[ 'rcs_scenario' ] = '';

			var mgn_libelle;
			if ( reponse[ 'rcs_libelle_menace' ] != null && reponse[ 'rcs_libelle_menace' ] != '' ) {
				mgn_libelle = reponse[ 'rcs_libelle_menace' ];
			} else {
				mgn_libelle = reponse[ 'Risque' ][0].mgn_libelle
			}

			var Corps = 
				'<div id="zone_modification">' +
				 '<div class="table-responsive">' +
				  '<table class="table">' +
				   '<tr>' +
					'<td class="col-lg-2">' +
					 '<div class="col-lg-12 libelle">' + reponse[ 'L_Code' ] + '</div>' +
					 '<div class="col-lg-12 bordure_arrondie desactive">' + reponse[ 'Risque' ][0].rcs_code + '</div>' +
					'</td>' +
					'<td class="col-lg-5">' +
					 '<label class="col-lg-5 col-form-label" style="text-align: left;" for="mgn_libelle">' + reponse[ 'L_Menace' ] + '</label>' +
					 '<textarea id="rcs_libelle_menace" class="form-control" disabled>' + mgn_libelle + '</textarea>' +
					 '<textarea id="mgn_libelle" class="hide">' + reponse[ 'Risque' ][0].mgn_libelle + '</textarea>' +
					 '<div class="col-lg-12 libelle">' + reponse[ 'L_Evenement_Redoute' ] + '</div>' +
					 '<div class="col-lg-12 bordure_arrondie desactive">' + reponse[ 'Risque' ][0].evr_libelle + '</div>' +
					'</td>' +
					'<td class="col-lg-5">' +
					 '<div class="col-lg-12 libelle">' + reponse[ 'L_Actif_Support' ] + '</div>' +
					 '<div class="col-lg-12 bordure_arrondie desactive">' + reponse[ 'Risque' ][0].spp_nom + '</div>' +
					 //'<div class="col-lg-12 libelle">' + reponse[ 'L_Actifs_Primordiaux' ] + '</div>' +
					 //'<div class="col-lg-12 bordure_arrondie desactive" style="height: 70px; overflow-y: auto;">' + reponse[ 'Risque' ][0].apr_noms + '</div>' +
					'</td>' +
				   '</tr>' +
				  '</table>' +
				 '</div>' +
				 '<ul class="nav nav-tabs">' +
				  '<li role="presentation"><a id="lister_cartographie" href="#">' + reponse[ 'L_Cartographie'] + '</a></li>' +
				  '<li role="presentation"><a id="lister_actifs_primordiaux" href="#">' + reponse[ 'L_Actifs_Primordiaux'] + '</a></li>' +
				  '<li role="presentation"><a id="lister_vulnerabilites" href="#">' + reponse[ 'L_Vulnerabilites'] + '</a></li>' +
				  '<li role="presentation"><a id="lister_impacts" href="#">' + reponse[ 'L_Impacts'] + '</a></li>' +
				  '<li role="presentation"><a id="lister_sources_menaces" href="#">' + reponse[ 'L_Sources_Menaces'] + '</a></li>' +
				  '<li role="presentation"><a id="afficher_scenario" href="#">' + reponse[ 'L_Scenario'] + '</a></li>' +
				  '<li role="presentation"><a id="lister_criteres_evaluation" href="#">' + reponse[ 'L_Evaluation_Risque'] + '</a></li>' +
				 '</ul>' +
				
				 // Onglet : critères d'évaluation
				 '<div id="corps_onglet">' +

				  '<div id="onglet-cartographie" style="display: none;">' +
					reponse[ 'Cartographie' ] +
				  '</div>' +

				  '<div id="onglet-actifs_primordiaux" style="display: none;">' +
					reponse[ 'Actifs_Primordiaux' ] +
				  '</div>' +

				  '<div id="onglet-criteres_evaluation" style="display: none;">' +
				   '<div class="row form-group">' +
					'<label class="col-lg-4 col-form-label" style="text-align: left;" for="vrs_libelle">' + reponse[ 'L_Niveau_Vraisemblance' ] + '</label>' +
					'<div class="col-lg-4">' +
					 '<select id="vrs_libelle" class="form-control">' + reponse[ 'vrs_libelles' ] + '</select>' +
					'</div>' +
				   '</div>' +

				   '<div class="row form-group">' +
					'<label class="col-lg-4 col-form-label" style="text-align: left;" for="gri_libelle">' + reponse[ 'L_Niveau_Impact' ] + '</label>' +
					'<div class="col-lg-4">' +
					 '<select id="gri_libelle" class="form-control"' + evr_actif + '>' + reponse[ 'gri_libelles' ] + '</select>' +
					'</div>' +
				   '</div>' +

				   '<div class="row form-group">' +
					'<label class="col-lg-4 col-form-label" style="text-align: left;" for="rcs_cotation_actif">' + reponse[ 'L_Sensibilite_Actif_affecte' ] + '</label>' +
					'<div class="col-lg-7">' +
					 '<select id="rcs_cotation_actif" class="form-control">' + reponse['criteres_sensibilites_max'] + '</select>' +
					'</div>' +
				   '</div>' +
				  '</div>' +

				  // Onglet : sources des menaces
				  '<div id="onglet-sources_menaces" style="display: none;">' +
				   '<div class="row form-group">' +
					'<div class="col-lg-12 sources_menaces">' +
					 reponse['srm_libelles'] +
					'</div>' +
				   '</div>' +
				  '</div>' +

				  // Onglet : scenario
				  '<div id="onglet-scenario" style="display: none;">' +
				   '<div class="row form-group">' +
					'<div class="col-lg-12">' +
					 '<textarea id="rcs_scenario" class="form-control scenario">' + reponse[ 'rcs_scenario' ] + '</textarea>' +
					'</div>' +
				   '</div>' +
				  '</div>' +

				  // Onglet : vulnérabilités
				  '<div id="onglet-vulnerabilites" style="display: none;">' +
				   '<div class="row form-group">' +
					'<div class="col-lg-12 vulnerabilites">' +
					 reponse['vln_libelles'] +
					'</div>' +
				   '</div>' +
				  '</div>' +

				  // Onglet : impacts génériques
				  '<div id="onglet-impacts" style="display: none;">' +
				   '<div class="row form-group">' +
					'<div class="col-lg-12 impacts">' +
					 reponse['ign_libelles'] +
					'</div>' +
				   '</div>' +
				  '</div>' +

				 '</div>' + // corps_onglet
				'</div>'; // zone_modification


			var ID_Btn_Modifier = '';
			if ( reponse['crs_modifiable'] == true ) ID_Btn_Modifier = 'idBoutonPrincipal';


			construireModal( 'idModal',
				reponse[ 'Titre_Visualiser' ],
				Corps,
				null, null,
				true, reponse[ 'L_Fermer' ],
				'formModal', 'modal-xxlg' );

			$('#idModal').modal('show').drags({handle:".modal-header"}); // Affiche la modale qui vient d'être créée

			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModal').on('shown.bs.modal', function() {
				$('.nav-tabs a:first').trigger('click');

				$('.modal-body #onglet-scenario textarea').attr( 'disabled', 'disabled' );
				$('.modal-body #onglet-criteres_evaluation select').attr( 'disabled', 'disabled' );

				$('.btn-fermer').focus();				   
			});


			// Supprime la modale après l'avoir caché.
			$('#idModal').on('hidden.bs.modal', function() {
				$('#idModal').remove();
			});


			// Affiche les informations de la Cartographie.
			$('#lister_cartographie').on('click', function() {
				$('.nav-tabs li').removeClass('active');
				$('.nav-tabs li a#lister_cartographie').parent().addClass('active');
				
				$('div[id^=onglet-]').hide();

				$('#onglet-cartographie').show();
				$('div#onglet-cartographie input:first').focus();
			});


			// Affiche les Actifs Primordiaux.
			$('#lister_actifs_primordiaux').on('click', function() {
				$('.nav-tabs li').removeClass('active');
				$('.nav-tabs li a#lister_actifs_primordiaux').parent().addClass('active');
				
				$('div[id^=onglet-]').hide();

				$('#onglet-actifs_primordiaux').show();
				$('div#onglet-actifs_primordiaux input:first').focus();
			});


			// Affiche la liste des critères d'évaluation du risque.
			$('#lister_criteres_evaluation').on('click', function() {
				$('.nav-tabs li').removeClass('active');
				$('.nav-tabs li a#lister_criteres_evaluation').parent().addClass('active');
				
				$('div[id^=onglet-]').hide();

				$('#onglet-criteres_evaluation').show();
				$('div#onglet-criteres_evaluation select:first').focus();
			});


			// Affiche la liste des sources de menaces.
			$('#lister_sources_menaces').on('click', function() {
				$('.nav-tabs li').removeClass('active');
				$('.nav-tabs li a#lister_sources_menaces').parent().addClass('active');
				
				$('div[id^=onglet-]').hide();

				$('#onglet-sources_menaces').show();
				$('div#onglet-sources_menaces input:first').focus();
			});


			// Affiche la liste des sources de menaces et le scénario du risque.
			$('#afficher_scenario').on('click', function() {
				$('.nav-tabs li').removeClass('active');
				$('.nav-tabs li a#afficher_scenario').parent().addClass('active');
				
				$('div[id^=onglet-]').hide();

				$('#onglet-scenario').show();
				$('div#onglet-scenario textarea:first').focus();
				document.getElementById('rcs_scenario').selectionStart = reponse[ 'rcs_scenario' ].length;
			});

			// Affiche la liste des entités pouvant être associées.
			$('#lister_vulnerabilites').on('click', function() {
				$('.nav-tabs li').removeClass('active');
				$('.nav-tabs li a#lister_vulnerabilites').parent().addClass('active');
				
				$('div[id^=onglet-]').hide();

				$('#onglet-vulnerabilites').show();
				$('div#onglet-vulnerabilites input:enabled:first').focus();
			});

			// Affiche la liste des profils pouvant être associés.
			$('#lister_impacts').on('click', function() {
				$('.nav-tabs li').removeClass('active');
				$('.nav-tabs li a#lister_impacts').parent().addClass('active');

				$('div[id^=onglet-]').hide();

				$('#onglet-impacts').show();
				$('div#onglet-impacts input:first').focus();
			});


			// Gère la soumission du formulaire.
			$('#formModal').submit( function( event ) {
				event.preventDefault(); // Laisse le contrôle au Javascript.

				if ( $('#rcs_scenario').val() == '' ) {
					$('#afficher_scenario').trigger('click');
					return false;
				}

				modifierDescriptionRisque( Id );

				//trier( $( 'div#entete_tableau div.row div:first'), true );
			} );


			// Gère le changement d'Entités.
			$('#ent_id').on('change', function() {
				var Id = $(this).val();

				actualiserStatutEntiteSelectionnee( Id );
			});
		}
	});
}
