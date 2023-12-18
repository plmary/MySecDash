$(document).ready(function() {
	// Charge les données du tableau.
	trier( $( 'div#entete_tableau div.row div:first'), true );

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


function trier( myElement, changerTri, chercher ) {
	// AJAX changeant la valeur du filtre
	var sens_recherche = $( myElement ).attr( 'data-sens-tri' );
	var changer_tri = changerTri || false;

	$.ajax({
		url: '../../../Loxense-EditionConformite.php?Action=AJAX_Trier',
		type: 'POST',
		data: $.param({'trier': sens_recherche, 'chercher': chercher}),
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

				// Vérifie s'il y a une limitation à la création des Cartographies.
				//gererBoutonAjouter( reponse['total'], reponse['limitation'], reponse['libelle_limitation'] );


				$('.btn-imprimer').click(function(){
					var crs_id = $(this).attr('data-id');

					ModalChoixImpression( crs_id );
				});

				redimensionnerWindow();
			} else {
				var texteMsg = reponse['texteMsg'];

				afficherMessage( texteMsg, statut );
			}
		}
	});
}




// ===============================================
// Fonctions communes pour gérer les impressions.

function chargerImpressionsCartoExcel( crs_id ) {
	$.ajax({
		url: '../../../Loxense-EditionConformite.php?Action=AJAX_Verifier_Impression_Excel',
		type: 'POST',
		data: $.param({'crs_id': crs_id}),
		dataType: 'json', // le résultat est transmit dans un objet JSON

		success: function(reponse) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			if ( reponse['statut'] == 'success' ) {
				window.location.href = '../../../Loxense-EditionConformite.php?Action=AJAX_Charger_Impression_Excel&crs_id='+crs_id;
			} else {
				afficherMessage( reponse['texteMsg'], reponse['statut'] );
			}
		}
	});
}


function chargerImpressionsCartoWord( crs_id ) {
	$.ajax({
		url: '../../../Loxense-EditionConformite.php?Action=AJAX_Verifier_Impression_Word',
		type: 'POST',
		data: $.param({'crs_id': crs_id}),
		dataType: 'json', // le résultat est transmit dans un objet JSON

		success: function(reponse) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			if ( reponse['statut'] == 'success' ) {
				window.location.href = '../../../Loxense-EditionConformite.php?Action=AJAX_Charger_Impression_Word&crs_id='+crs_id;
			} else {
				afficherMessage( reponse['texteMsg'], reponse['statut'] );
			}
		}
	});
}


function ModalChoixImpression( Id ) {
	var ent_libelle = $('#CRS_'+Id).find('div[data-src="ent_libelle"]').find('span').text();
	var crs_libelle = $('#CRS_'+Id).find('div[data-src="crs_libelle"]').find('span').text();
	var crs_periode = $('#CRS_'+Id).find('div[data-src="crs_periode"]').find('span').text();
	var crs_version = $('#CRS_'+Id).find('div[data-src="crs_version"]').find('span').text();

	$.ajax({
		url: '../../../Loxense-EditionConformite.php?Action=AJAX_Libeller',
		type: 'POST',
		dataType: 'json',
		success: function( reponse ) {
			var Corps =
				'<div class="well">' +
				 '<div class="row">' +
				  '<div class="col-lg-4"><strong>' + reponse['L_Entite'] + '</strong></div>' +
				  '<div class="col-lg-5"><strong>' + reponse['L_Libelle'] + '</strong></div>' +
				  '<div class="col-lg-1"><strong>' + reponse['L_Periode'] + '</strong></div>' +
				  '<div class="col-lg-2"><strong>' + reponse['L_Version'] + '</strong></div>' +
				 '</div>' +

				 '<div class="row">' +
				  '<div class="col-lg-4" style="background-color: #dcafdd;">' + ent_libelle + '</div>' +
				  '<div class="col-lg-5" style="background-color: #dcafdd;">' + crs_libelle + '</div>' +
				  '<div class="col-lg-1" style="background-color: #dcafdd;">' + crs_periode + '</div>' +
				  '<div class="col-lg-2" style="background-color: #dcafdd;">' + crs_version + '</div>' +
				 '</div>' +
				'</div>' +

				'<div class="well corps_onglet">' +
				 '<div class="row">' +
				  '<label class="col-lg-5 col-form-label text-end" for="format_edition">' + reponse[ 'L_Format_Edition' ] + '</label>' +
				  '<div class="col-lg-2">' +
				   '<select id="format_edition" class="form-select" required>' +
				    '<option>excel</option>' +
//				    '<option>word</option>' +
//				    '<option disabled>html</option>' +
				   '</select>' +
				  '</div>' +
				 '</div>' +

				'</div>';

			construireModal( 'idModal',
				reponse[ 'L_Editions_Actions' ],
				Corps,
				'idBoutonPrincipal', reponse[ 'L_Imprimer' ],
				true, reponse[ 'L_Fermer' ],
				'idForm', 'modal-xl' );

			$('#idModal').modal('show'); // Affiche la modale qui vient d'être créée

			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModal').on('shown.bs.modal', function() {
				$('#idModal select:first').focus();
			});

			// Supprime la modale après l'avoir caché.
			$('#idModal').on('hidden.bs.modal', function() {
				$('#idModal').remove();
			});

			$('#idForm').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				imprimerCartographie( Id );
			} );
		}
	});
}


function imprimerCartographie( Id ) {
	var format_edition = $('#format_edition').val();

	$('#idModal').modal('hide');

	genererImpressionCarto( Id, format_edition );
}


// ============================================
// Fonctions communes pour gérer les Lectures.

function genererImpressionCarto( crs_id, type_edition ) {
	$.ajax({
		url: '../../../Loxense-EditionConformite.php?Action=AJAX_Generer_Impression',
		type: 'POST',
		data: $.param({'crs_id': crs_id, 'type_edition': type_edition}),
		dataType: 'json', // le résultat est transmit dans un objet JSON

		success: function(reponse) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			afficherMessage( reponse['texteMsg'], reponse['statut'] );

			if ( type_edition == 'excel' ) chargerImpressionsCartoExcel( crs_id );
			else if ( type_edition == 'word' ) chargerImpressionsCartoWord( crs_id );
			else if ( type_edition == 'html' ) alert('Yop');
		}
	});
}


function chargerImpressionsCarto( crs_id ) {
	$.ajax({
		url: '../../../Loxense-EditionConformite.php?Action=AJAX_Verifier_Impression',
		type: 'POST',
		data: $.param({'crs_id': crs_id}),
		dataType: 'json', // le résultat est transmit dans un objet JSON

		success: function(reponse) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			if ( reponse['statut'] == 'success' ) {
				window.location.href = '../../../Loxense-EditionConformite.php?Action=AJAX_Charger_Impression&crs_id='+crs_id;
//				window.location.href = '../../../Loxense-EditionsRisques.php?Action=AJAX_Charger_Impression_Excel&crs_id='+crs_id;
			} else {
				afficherMessage( reponse['texteMsg'], reponse['statut'] );
			}
		}
	});
}


function chargerImpressionsCartoExcel( crs_id ) {
	$.ajax({
		url: '../../../Loxense-EditionConformite.php?Action=AJAX_Verifier_Impression_Excel',
		type: 'POST',
		data: $.param({'crs_id': crs_id}),
		dataType: 'json', // le résultat est transmit dans un objet JSON

		success: function(reponse) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			if ( reponse['statut'] == 'success' ) {
				window.location.href = '../../../Loxense-EditionConformite.php?Action=AJAX_Charger_Impression_Excel&crs_id='+crs_id;
			} else {
				afficherMessage( reponse['texteMsg'], reponse['statut'] );
			}
		}
	});
}


function chargerImpressionsCartoWord( crs_id ) {
	$.ajax({
		url: '../../../Loxense-EditionConformite.php?Action=AJAX_Verifier_Impression_Word',
		type: 'POST',
		data: $.param({'crs_id': crs_id}),
		dataType: 'json', // le résultat est transmit dans un objet JSON

		success: function(reponse) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			if ( reponse['statut'] == 'success' ) {
				window.location.href = '../../../Loxense-EditionConformite.php?Action=AJAX_Charger_Impression_Word&crs_id='+crs_id;
			} else {
				afficherMessage( reponse['texteMsg'], reponse['statut'] );
			}
		}
	});
}
