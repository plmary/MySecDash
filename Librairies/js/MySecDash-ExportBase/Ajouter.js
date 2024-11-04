// ======================================================================
// Mise en place des écoutes sur les événements produits par les objets.
$(function() {
	$('.btn-sauver').on('click', function() {
		choixSauvegarde();
	});


	$('.btn-importer').on('click', function() {
		importerBase();
	});
});


function choixSauvegarde() {
	$.ajax({
		url: '../../../Loxense-ExportBase.php?Action=AJAX_Libeller',
		type: 'POST',
		dataType: 'json',
		//data: $.param({'version': _Version, 'type': _Type, 'date': _Date}),

		success: function( reponse ) {
			if ( reponse[ 'statut'] != 'success' ) {
				afficherMessage( reponse['texteMsg'], reponse['statut'], 'body' );
				return -1;
			}

			var Corps = 
				'<div id="SAV-SAVE">' +
				reponse['L_Confirmer_Sauvegarde_Base'] +
				'</div>';


			construireModal( 'idModal',
				reponse[ 'L_Sauvegarder_Base' ],
				Corps,
				'idBoutonSauver', reponse[ 'L_Sauvegarder' ],
				true, reponse[ 'L_Fermer' ],
				'idForm' );


			$('#idModal').modal('show'); // Affiche la modale qui vient d'être créée

			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModal').on('shown.bs.modal', function() {
				$('#idBoutonSauver').focus();
			});

			// Supprime la modale après l'avoir caché.
			$('#idModal').on('hidden.bs.modal', function() {
				$('#idModal').remove();
			});

			$('#idForm').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				sauvegarde();
			} );
		}
	});
}


function sauvegarde() {
	$.ajax({
		url: '../../../Loxense-ExportBase.php?Action=AJAX_Sauvegarder_Base',
		type: 'POST',
		//data: $.param({'type_d': Type_D, 'type_s': Type_S}),
		dataType: 'json', // le résultat est transmit dans un objet JSON

		success: function( reponse ){
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			if( statut == 'success' ){
				trier( $( 'div#entete_tableau div.row div:first'), false );
			}

			afficherMessage( texteMsg, statut );
		}
	});

	$('.modal').modal('hide');
}


function choixExporterSauvegarde( Id ) {
	$.ajax({
		url: '../../../Loxense-ExportBase.php?Action=AJAX_Libeller',
		type: 'POST',
		dataType: 'json',
		//data: $.param({'version': _Version, 'type': _Type, 'date': _Date}),

		success: function( reponse ) {
			if ( reponse[ 'statut'] != 'success' ) {
				afficherMessage( reponse['texteMsg'], reponse['statut'], 'body' );
				return -1;
			}

			var _Version = $('#SAV_'+Id+' div[data-src="sav_version"] span').text();
			var _Type = $('#SAV_'+Id+' div[data-src="sav_type"] span').text();
			var _Date = $('#SAV_'+Id+' div[data-src="sav_date"] span').text();


			var Corps = 
				'<div id="SAV-EXPORT">' +
				reponse['L_Choisir_Exporter_Base'].replace('%sav_version', _Version).replace('%sav_type', _Type)
					.replace('%sav_date', _Date) +
				'</div>';


			construireModal( 'idModal',
				reponse[ 'L_Exporter_Base' ],
				Corps,
				'idBoutonExporter', reponse[ 'L_Exporter' ],
				true, reponse[ 'L_Fermer' ],
				'idForm', 'modal-lg' );


			$('#idModal').modal('show'); // Affiche la modale qui vient d'être créée

			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModal').on('shown.bs.modal', function() {
				$('#mdp_export').focus();
			});

			// Supprime la modale après l'avoir caché.
			$('#idModal').on('hidden.bs.modal', function() {
				$('#idModal').remove();
			});

			$('#idForm').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				if ( exporterSauvegarde( Id ) == -1 ) {
					$('#mdp_export').parent().addClass('has-error');
					$('#mdp_export').focus();
				}
			} );
		}
	});
}


function exporterSauvegarde( Id ) {
	var Mot_Passe = $('#mdp_export').val();

	if ( Mot_Passe == '' ) return -1;

	var _Version = $('#SAV_'+Id+' div[data-src="sav_version"] span').text();
	var _Type = Id.split('-')[0]; //$('#SAV_'+Id+' div[data-src="sav_type"] span').text();
	var _Date = $('#SAV_'+Id+' div[data-src="sav_date"] span').text();

	_Version = _Version.replace('.','_').replace('-','_');
	_Date = _Date.replace(/-/g,'_').replace(/:/g,'_').replace(' ','-');

	$.ajax({
		url: '../../../Loxense-ExportBase.php?Action=AJAX_Exporter_Base',
		type: 'POST',
		data: $.param({'version': _Version, 'type': _Type, 'date': _Date, 'mot_passe': Mot_Passe}),
		dataType: 'json', // le résultat est transmit dans un objet JSON

		success: function( reponse ){
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			if( statut == 'success' ){
				window.location.href = '../../../Loxense-ExportBase.php?Action=AJAX_Charge_Fichier&Nom_Fichier='+reponse['nom_fichier'];

				afficherMessage( texteMsg, statut );
			}
		}
	});

	$('.modal').modal('hide');
}


function importerBase() {
	$.ajax({
		url: '../../../Loxense-ExportBase.php?Action=AJAX_Libeller',
		type: 'POST',
		dataType: 'json',
		//data: $.param({'libelle': ent_libelle, 'statut_libelle': crs_statut, 'lister_informations': 'oui', 'crs_id': Id}),

		success: function( reponse ) {
			var Corps =
				'<form id="formCharger" method="post" enctype="multipart/form-data" class="form-horizontal">' + // Bloc horizontal
				'<div class="form-group">' +
				 '<div class="col-lg-3">' +
				   '<label for="fichier_interne">' + reponse[ 'L_Nom_Base_Chiffree' ] + '</label>' +
				 '</div>' +
				 '<div class="col-lg-9">' +
				   '<input class="form-control" type="file" id="fichier_interne" name="fichier" accept=".exp" style="height: inherit;" required>' +
				 '</div>' +
				'</div>' +
				'<div class="form-group">' +
				 '<div class="col-lg-3">' +
				   '<label for="mot_passe">' + reponse[ 'L_Mot_Passe_Dechiffrer_Base' ] + '</label>' +
				 '</div>' +
				 '<div class="col-lg-4">' +
				  '<input class="form-control" type="password" id="mot_passe" placeholder="' + reponse[ 'L_Mot_Passe' ] + '" required autocomplete="off">' +
				 '</div>' +
				'</div>' +
				'</form>';

			construireModal( 'idModal',
				reponse[ 'L_Importer_Base' ],
				Corps,
				'idBoutonTelecharger', reponse[ 'L_Importer' ],
				true, reponse[ 'L_Fermer' ],
				'formTelecharger', 'modal-xxlg' );

			$('#idModal').modal('show'); // Affiche la modale qui vient d'être créée


			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModal').on('shown.bs.modal', function() {
				$('input:first').focus();
			});


			// Détruit la modale quand cette dernière est désactivée.
			$('#idModal').on('hidden.bs.modal', function() {
				$('#idModal').remove();
			});

			$('#formTelecharger').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.
				
				DataBloc = new FormData( $('form')[0] );
				DataBloc.append( 'mot_passe', $('#mot_passe').val() );

				$.ajax({
					url: '../../../Loxense-ExportBase.php?Action=AJAX_Importer_Base',
					type: 'POST',
					dataType: 'json',
					data: DataBloc,
					processData: false,
				    contentType: false,

				    success: function( reponse ) {
				    	texteMsg = reponse[ 'texteMsg' ];
				    	statut = reponse[ 'statut' ];

				    	$('#idModal').modal('hide');

				    	afficherMessage( texteMsg, statut );

		                $( reponse[ 'texteHTML' ] ).prependTo( '#corps_tableau' );
				    	$('#totalOccurrences').text( ajouterZero(reponse[ 'total' ] ) );
					}
				});
			} );
		}
	});
}
