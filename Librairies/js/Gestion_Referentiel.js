function ModalReferentielAjouterModifier( Prefixe, Nom_Champ_Code, Id, Code, Type_Champ='input' ){
	var Id = Id || '';
	var Code = Code || '';
/*	var Langue = Langue || 'fr';*/
	var Libelle = Libelle || '';
	var Action, Appel_Liste;

	if ( Code != '' ) Code = protegerQuotes( Code );
	if ( Libelle != '' ) Libelle = protegerQuotes( Libelle );


	if ( Id != '' ) {
		Action = "M";
		Appel_Liste = '&lister_gst=' + Id;
	} else {
		Action = "A";
		Appel_Liste = '&lister_gst';
	}

	$.ajax({
		url: Parameters['URL_BASE']+Parameters['SCRIPT']+'?Action=AJAX_Libeller' + Appel_Liste,
		type: 'POST',
		dataType: 'json',
		success: function( reponse ) {
			var Taille = $('#'+Prefixe+'_'+Id).find('[data-src="'+Nom_Champ_Code+'"]').attr('data-maximum');
			if ( Taille != null ) {
				Taille = 'maxlength=' + Taille;
			} else {
				Taille = '';
			}

			if ( Code != '' ) Code = ' value="' + Code + '"';

			var code_HTML = '<div class="col-lg-4 mb-2 form-floating">' +
				'<input id="ref_code" class="form-control text-uppercase" type="text" plaholder="' + reponse[ 'L_Code' ] + '" required ' + Taille + Code + '>' +
				'<label for="ref_code">' + reponse[ 'L_Code' ] + '</label>' +
				'</div>';

			for (i=0; i < reponse['lister_lng'].length; i++) {
				if (reponse['Liste_Libelles'][reponse['lister_lng'][i].lng_id] == undefined ) {
					n_libelle = '';
				} else {
					n_libelle = reponse['Liste_Libelles'][reponse['lister_lng'][i].lng_id]
				}
			
				code_HTML += '<div class="row g-2">' +
					'<div class="col-lg-3 mb-2 form-floating">' +
					'<input id="ref_langue_'+reponse['lister_lng'][i].lng_id+'" class="form-control" type="text" disabled value="' + reponse['lister_lng'][i].lng_libelle + '">' +
					'<label for="ref_libelle_'+reponse['lister_lng'][i].lng_id+'">' + reponse[ 'L_Libelle' ] + '</label>' +
					'</div>';

				if (Type_Champ == 'input') {
					code_HTML += '<div class="col-lg-9 mb-2 form-floating">' +
						'<input id="ref_libelle_'+reponse['lister_lng'][i].lng_id+'" class="form-control ref_libelle" type="text" required value="' + n_libelle + '">' +
						'<label for="ref_libelle_'+reponse['lister_lng'][i].lng_id+'">' + reponse[ 'L_Libelle' ] + '</label>';
				} else {
					code_HTML += '<div class="col-lg-9 mb-2">' +
						'<textarea id="ref_libelle_'+reponse['lister_lng'][i].lng_id+'" class="form-control ref_libelle" type="text" rows="3" placeholder="' + reponse[ 'L_Libelle' ] + '" required>' +
						n_libelle +
						'</textarea>';
				}
					
				code_HTML += '</div>' +
					'</div>';
			}

			if (reponse['lister_gst'] != undefined) {
				code_HTML += '<input type="hidden" name="action_form" value="' + Action + '">' +
					'<ul class="nav nav-tabs">' +
					'<li role="presentation"><a id="onglet-gst" class="nav-link active" href="#">' + reponse['L_Gestionnaires'] + '</a></li>' +
					'</ul>' +
					'<div id="onglet-association">' +
					reponse['lister_gst'] +
					'</div>';
			}


			if ( Id != '' ) {
				var Bouton = reponse[ 'L_Modifier' ];
				var Titre = reponse[ 'Titre2'];
			} else {
				var Bouton = reponse[ 'L_Ajouter' ];
				var Titre = reponse[ 'Titre'];
			}


			construireModal( 'idModalAjouterModifier',
				Titre,
				code_HTML,
				'idBouton', Bouton,
				true, reponse[ 'L_Fermer' ],
				'formModalAjouterModifier', 'modal-xl' );


			// Affiche la modale qui vient d'être créée
			$('#idModalAjouterModifier').modal('show');


			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModalAjouterModifier').on('shown.bs.modal', function() {
				$('#ref_code').focus();

				var P_Champ = $('#ref_code');
				var V_Champ = P_Champ.val();

				if ( V_Champ != '' ) P_Champ[0].selectionStart = V_Champ.length;
			});


			// Après avoir disparu à l'écran la fenêtre est supprimée.
			$('#idModalAjouterModifier').on('hidden.bs.modal', function() {
				$('#idModalAjouterModifier').remove(); // Supprime la modale d'ajout.
			});


			// Sauvegarde les modifications réalisées dans la fenêtre.
			$('#formModalAjouterModifier').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				if ( Action == 'M' ) {
					ModifierReferentiel( Id );
				} else {
					AjouterReferentiel();
				}
			} );

		}
	});

}