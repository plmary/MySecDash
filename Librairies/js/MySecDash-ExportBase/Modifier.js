function choixRestaurerBase( Id ) {
	var _Version, _Type, _Date;

	$.ajax({
		url: '../../../Loxense-ExportBase.php?Action=AJAX_Libeller',
		type: 'POST',
		dataType: 'json',
		//data: $.param({'libelle': ent_libelle, 'statut_libelle': crs_statut, 'lister_informations': 'oui', 'crs_id': Id}),

		success: function( reponse ) {
			var Alerte;

			if ( Id.split('-')[0] == 'data' ) {
				Alerte = reponse['L_Alerte_Restauration_Donnees'];
			} else {
				Alerte = reponse['L_Alerte_Restauration_Structure'];
			}


			_Version = $('#SAV_'+Id+' div[data-src="sav_version"] span').text();
			_Type = $('#SAV_'+Id+' div[data-src="sav_type"] span').text();
			_Date = $('#SAV_'+Id+' div[data-src="sav_date"] span').text();

			var Corps =
				'<div class="alert alert-danger" role="alert" style="margin: 0 12px;">' +
				'<span class="bi-exclamation-triangle" aria-hidden="true" style="margin-right: 12px;"></span>' +
				Alerte +
				'</div>' +
				'<div style="margin-top: 18px;">' +
				reponse['L_Detail_Base'].replace('%sav_version', _Version).replace('%sav_type', _Type).replace('%sav_date', _Date) +
				'</div>';

			construireModal( 'idModal',
				reponse[ 'L_Restaurer_Base' ],
				Corps,
				'idBoutonRestaurer', reponse[ 'L_Restaurer' ],
				true, reponse[ 'L_Fermer' ],
				'formRestaurer', 'modal-xxlg' );

			$('#idModal').modal('show'); // Affiche la modale qui vient d'être créée


			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModal').on('shown.bs.modal', function() {
				$('input:first').focus();
			});


			// Détruit la modale quand cette dernière est désactivée.
			$('#idModal').on('hidden.bs.modal', function() {
				$('#idModal').remove();
			});


			$('#formRestaurer').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault();

				_Version = _Version.replace('.','_').replace('-','_');
				_Type = Id.split('-')[0];
				_Date = _Date.replace(/-/g,'_').replace(/:/g,'_').replace(' ','-');

				restaurerBase( _Version, _Type, _Date );

				return false;
			} );
		}
	});
}


function restaurerBase( _Version, _Type, _Date ) {
	$.ajax({
		url: '../../../Loxense-ExportBase.php?Action=AJAX_Restaurer_Base',
		type: 'POST',
		dataType: 'json',
		data: $.param({'version': _Version, 'type': _Type, 'date': _Date}),

		success: function( reponse ) {
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			//if( statut != 'success' ){
					//alert(reponse['code']+' - '+reponse['affichage']);
			//}

			afficherMessage( texteMsg, statut );


			$('#idModal').modal('hide'); // Cache la modale qui a été créée
		}
	});
}