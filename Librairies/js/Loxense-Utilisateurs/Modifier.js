// ===========================


function ModifierCivilite( Id ) {
	var Last_Name = $('#cvl_nom').val();
	var First_Name = $('#cvl_prenom').val();
	var Sex = $('#cvl_sexe').val();
	var Birth_Date = $('#cvl_date_naissance').val();
	var Birth_Town = $('#cvl_lieu_naissance').val();

	var Champ = $('div[data-src="cvl_sexe"]');
	var Liste = Champ.attr('data-list').split(';');

	$.ajax({
		url: '../../../Loxense-Civilites.php?Action=AJAX_Modifier',
		type: 'POST',
		data: $.param({'cvl_id': Id,'last_name': Last_Name,'first_name': First_Name,'sex': Sex,'birth_date': Birth_Date,'born_town': Birth_Town}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			if ( statut == 'success' ) {
				$('#idModalCivilite').modal('hide'); // Cache la modale d'ajout.

				var sex_label;

				// Récupère le libellé du sexe.
				for(var i = 0; i < Liste.length; i++) {
					Elements = Liste[i].split('=');
					if ( Elements[0] == Sex ) sex_label = Elements[1];
				}

				// Met à jour les différents champs de l'occurrence modifiée.
				$('#CVL_' + Id).find('div[data-src="cvl_nom"]').find('span').text( Last_Name );
				$('#CVL_' + Id).find('div[data-src="cvl_prenom"]').find('span').text( First_Name );
				$('#CVL_' + Id).find('div[data-src="cvl_sexe"]').find('span').text( sex_label );
			}

			afficherMessage( texteMsg, statut, 'body' );

		}
	});
}


function listerActions( Id_Identite, Attempt, Disable, Last_Connection, Expiration_Date, Updated_Authentication ) {
	var Id_Identite = Id_Identite || '';

	$.ajax({
		url: '../../../Loxense-Utilisateurs.php?Action=AJAX_Libeller',
		type: 'POST',
		dataType: 'json',
		success: function( reponse ) {
			if ( reponse['Statut'] == 'success' ) {
				var Tentative_Max = reponse['max_attempt'] || 3;
				var Temps_Inactivite = reponse['account_lifetime'] || 6;
				var btn_nom, btn_texte;


				// Vérifie si l'utilisateur est désactivé
				var Boutons_Activation = '<a class="btn btn-sm btn-outline-secondary" id="btn-activer">' + reponse['L_To_Activate_User'] + '</a>' +
					'<a class="btn btn-sm btn-outline-secondary" id="btn-desactiver">' + reponse['L_To_Deactivate_User'] + '</a>';

				var Bouton_Password = '<a class="btn btn-sm btn-outline-secondary" id="btn-password">' + reponse['L_Authenticator_Reset'] + '</a>';


				// Vérifie si le nombre de tentative de connexion n'est pas dépassé.
				if ( Attempt >= Tentative_Max ) {
					badge_couleur = ' bg-danger';
				} else if ( Attempt > 0 && Attempt < Tentative_Max ) {
					badge_couleur = ' bg-warning';
				} else {
					badge_couleur = ' bg-success';
				}
				var Badge_Tentative = '<span id="badge-tentative" class="badge' + badge_couleur + '">' + Attempt + '</span><span class="badge text-secondary"> / </span>' +
					'<span class="badge bg-secondary">' + Tentative_Max + '</span>';


				// Vérifie la date de dernière connexion et contrôle si cela ne fait pas trop longtemps.
				if ( Last_Connection === null ) {
					var Couleur = 'bg-danger';
					var Texte = reponse['L_Never_Connected'];
				} else {
					var Date1 = moment( Last_Connection );
					var Date2 = moment();

					var NbJours = Date2.diff( Date1, 'days' );;

					if ( NbJours <= (Temps_Inactivite * Number('-30')) ) {
						var Couleur = 'bg-danger';
						var Texte = reponse['L_Last_Connection_Old'];
					} else {
						var Couleur = 'bg-success';
						var Texte = Last_Connection;
					}
				}
				Last_Connection = '<span class="badge ' + Couleur + '">' + Texte + '</span>';

				// Vérifie si la date d'expiration de l'utilisateur n'est pas atteinte
				if ( Expiration_Date != '0000-00-00 00:00:00' ) {
					var Date2 = moment( Expiration_Date );
					var Date1 = moment();

					var NbJours = Date2.diff( Date1, 'days' );;

					var Couleur = 'bg-success';
					var Texte = Expiration_Date;

					if ( NbJours > 7 && NbJours < 14 ) {
						Couleur = 'bg-info';
					}

					if ( NbJours > 2 && NbJours <= 7 ) {
						Couleur = 'bg-warning';
					}

					if ( NbJours <= 2 ) {
						Couleur = 'bg-danger';
					}
				}
				Expiration_Date = '<span id="badge-expiration" class="badge ' + Couleur + '">' + Texte + '</span>';

				var Code_HTML = '<div id="liste-actions">' +
					'<div class="row">' +
					'<div class="col-lg-4 col-form-label">' + reponse['L_Attempt'] + '</div>' +
					'<div class="col-lg-4 col-form-label">' + Badge_Tentative + '</div>' +
					'<div class="col-lg-4 col-form-label"><a class="btn btn-sm btn-outline-secondary" id="btn-tentative">' + reponse['L_Reset'] + '</a></div>' +
					'</div>' +
					'<div class="row">' +
					'<div class="col-lg-4 col-form-label">' + reponse['L_Expiration_Date'] + '</div>' +
					'<div class="col-lg-4 col-form-label">' + Expiration_Date + '</div>' +
					'<div class="col-lg-4 col-form-label"><a class="btn btn-sm btn-outline-secondary" id="btn-expiration">' + reponse['L_Reset'] + '</a></div>' +
					'</div>' +
					'<div class="row">' +
					'<div class="col-lg-4 col-form-label">' + reponse['L_Last_Connection'] + '</div>' +
					'<div class="col-lg-4 col-form-label">' + Last_Connection + '</div>' +
					'</div>' +
					'<div class="row">' +
					'<div class="col-lg-6 text-center">' + Boutons_Activation + '</div>' +
					'<div class="col-lg-6 text-center">' + Bouton_Password + '</div>' +
					'</div>' +
					'</div>';

				$( Code_HTML ).appendTo( '#onglets_utilisateur' );


				if ( Disable === 1 || Disable == true ) {
					$('#btn-activer').show();
					$('#btn-desactiver').hide();
				} else {
					$('#btn-activer').hide();
					$('#btn-desactiver').show();
				}


				$('#btn-tentative').on( 'click', function( event ) {
					$.ajax({
						url: '../../../Loxense-Utilisateurs.php?Action=AJAX_Reset_Tentative',
						type: 'POST',
						dataType: 'json',
						//async: false,
						data: $.param({'id': Id_Identite}),
						success: function( reponse ) {
							if ( reponse['statut'] == 'success' ) {
								$('#badge-tentative').attr('data-toggle', 'tooltip').attr('data-placement', 'top').attr('title', reponse['texteMsg']);
								$('#badge-tentative').tooltip('show').removeClass('bg-danger bg-warning').addClass('bg-success').text('0');
								$('#btn-tentative').on( 'mouseout', function( event ) {
									$('#badge-tentative').tooltip('destroy');
								});
							}
						}
					});
				});


				$('#btn-expiration').on( 'click', function() {
					$.ajax({
						url: '../../../Loxense-Utilisateurs.php?Action=AJAX_Reset_Expiration',
						type: 'POST',
						dataType: 'json',
						//async: false,
						data: $.param({'id': Id_Identite}),
						success: function( reponse ) {
							if ( reponse['statut'] == 'success' ) {
								$('#badge-expiration').attr('data-toggle', 'tooltip').attr('data-placement', 'top').attr('title', reponse['texteMsg']);
								$('#badge-expiration').tooltip('show').removeClass('bg-danger bg-warning').addClass('bg-success').text(reponse['next_date']+' 00:00:00');
								$('#btn-expiration').on( 'mouseout', function( event ) {
									$('#badge-expiration').tooltip('destroy');
								});
							}
						}
					});
				});


				$('#btn-activer').on( 'click', function() {
					var tmp1 = $('#IDN_'+Id_Identite+' div[data-src="idn_super_admin"]').data('liste');
					var tmp1 = tmp1.split(';');
					var tmp2 = tmp1[0].split('=');
					var tmp3 = tmp1[1].split('=');
					var L_oui_non = new Array();
					L_oui_non[tmp2[0]] = tmp2[1];
					L_oui_non[tmp3[0]] = tmp3[1];

					$.ajax({
						url: '../../../Loxense-Utilisateurs.php?Action=AJAX_Activer',
						type: 'POST',
						dataType: 'json',
						//async: false,
						data: $.param({'id': Id_Identite}),
						success: function( reponse ) {
							if ( reponse['statut'] == 'success' ) {
								$('#btn-activer').hide();
								$('#btn-desactiver').show();

								$('#btn-desactiver').attr('data-toggle', 'tooltip').attr('data-placement', 'top').attr('title', reponse['texteMsg']);
								$('#btn-desactiver').tooltip('show');
								$('#btn-desactiver').on( 'mouseout', function( event ) {
									$('#btn-desactiver').tooltip('destroy');
								});

								$('#IDN_'+Id_Identite+' div[data-src="idn_desactiver"] span').text(L_oui_non[0]);
							}
						}
					});
				});


				$('#btn-desactiver').on( 'click', function() {
					var tmp1 = $('#IDN_'+Id_Identite+' div[data-src="idn_super_admin"]').data('liste');
					var tmp1 = tmp1.split(';');
					var tmp2 = tmp1[0].split('=');
					var tmp3 = tmp1[1].split('=');
					var L_oui_non = new Array();
					L_oui_non[tmp2[0]] = tmp2[1];
					L_oui_non[tmp3[0]] = tmp3[1];

					$.ajax({
						url: '../../../Loxense-Utilisateurs.php?Action=AJAX_Desactiver',
						type: 'POST',
						dataType: 'json',
						//async: false,
						data: $.param({'id': Id_Identite}),
						success: function( reponse ) {
							if ( reponse['statut'] == 'success' ) {
								$('#btn-desactiver').hide();
								$('#btn-activer').show();

								$('#btn-activer').attr('data-toggle', 'tooltip').attr('data-placement', 'top').attr('title', reponse['texteMsg']);
								$('#btn-activer').tooltip('show').text(reponse['libelle']);
								$('#btn-activer').on( 'mouseout', function( event ) {
									$('#btn-activer').tooltip('destroy');
								});

								$('#IDN_'+Id_Identite+' div[data-src="idn_desactiver"] span').text(L_oui_non[1]);
							}
						}
					});
				});


				$('#btn-password').on( 'click', function() {
					$.ajax({
						url: '../../../Loxense-Utilisateurs.php?Action=AJAX_Reset_Password',
						type: 'POST',
						dataType: 'json',
						data: $.param({'id': Id_Identite}),
						success: function( reponse ) {
							if ( reponse['statut'] == 'success' ) {
								$('#btn-password').attr('data-toggle', 'tooltip').attr('data-placement', 'top').attr('title', reponse['texteMsg']);
								$('#btn-password').tooltip('show').text(reponse['libelle']);
								$('#btn-password').on( 'mouseout', function( event ) {
									$('#btn-password').tooltip('destroy');
								});
							} else {
								afficherMessage( reponse['texteMsg'], reponse['statut'], '#idModalUtilisateur', 5, 'n' );
							}
						}
					});
				});

				$('#liste-actions').hide();
			}
		}
	});
}


function modifierUtilisateur( Id ) {
	var idn_login = $('#idn_login').val();
	var idn_courriel = $('#idn_courriel').val();
	var idn_super_admin = $('#idn_super_admin').is(':checked');
	var cvl_id = $('#cvl_id').val();
	var cvl_libelle = $('#cvl_id option:selected').text();
	var ent_id = $('#ent_id').val();
	var ent_libelle = $('#ent_id option:selected').text();

	var tmp1 = $('#IDN_'+Id+' div[data-src="idn_super_admin"]').data('liste');
	var tmp1 = tmp1.split(';');
	var tmp2 = tmp1[0].split('=');
	var tmp3 = tmp1[1].split('=');
	var L_oui_non = new Array();
	L_oui_non[tmp2[0]] = tmp2[1];
	L_oui_non[tmp3[0]] = tmp3[1];

	if ( idn_super_admin == true ) var Super_Admin = L_oui_non[1];
	else var Super_Admin = L_oui_non[0];


	var e_ID, e_OLD;

	var liste_GST_a_ajouter = [], liste_GST_a_supprimer = [];

	$('input[id^="chk-GST-"]').each( function( index, element ) {
		e_ID = $(element).attr('id');
		e_OLD = $(element).attr('data-old');

		if ( $(element).is(':checked') == true && e_OLD == '0' ) {
			liste_GST_a_ajouter.push( e_ID.split('-')[2] );
		}

		if ( $(element).is(':checked') == false && e_OLD == '1' ) {
			liste_GST_a_supprimer.push( e_ID.split('-')[2] );
		}
	});


	var liste_TGS_a_ajouter = [], liste_TGS_a_supprimer = [];

	$('input[id^="chk-TGS-"]').each( function( index, element ) {
		e_ID = $(element).attr('id');
		e_OLD = $(element).attr('data-old');

		if ( $(element).is(':checked') == true && e_OLD == '0' ) {
			liste_TGS_a_ajouter.push( e_ID.split('-')[2] );
		}

		if ( $(element).is(':checked') == false && e_OLD == '1' ) {
			liste_TGS_a_supprimer.push( e_ID.split('-')[2] );
		}
	});


	$.ajax({
		url: '../../../Loxense-Utilisateurs.php?Action=AJAX_Modifier',
		type: 'POST',
		dataType: 'json',
		data: $.param({'id': Id, 'idn_login': idn_login, 'idn_super_admin': idn_super_admin, 'cvl_id': cvl_id, 'ent_id': ent_id,
			'liste_GST_a_ajouter': liste_GST_a_ajouter, 'liste_GST_a_supprimer': liste_GST_a_supprimer, 'idn_courriel': idn_courriel,
			'liste_TGS_a_ajouter': liste_TGS_a_ajouter, 'liste_TGS_a_supprimer': liste_TGS_a_supprimer}),
		success: function( reponse ) {
			if ( reponse['statut'] == 'success' ) {
				afficherMessage( reponse['texteMsg'], reponse['statut'] );

				$('#IDN_'+Id+' div[data-src="idn_login"] span').text(idn_login);
				$('#IDN_'+Id+' div[data-src="ent_libelle"] span').text(ent_libelle);
				$('#IDN_'+Id+' div[data-src="cvl_label"] span').text(cvl_libelle);
				$('#IDN_'+Id+' div[data-src="idn_super_admin"] span').text(Super_Admin);


				// Sauvegarde les Entités ajoutées ou supprimées à l'Utilisateur
				var ListeAjouter = [];
				var ListeSupprimer = [];
				var ListeModifier = [];
				var Droit;
				var ent_id, ent_id_adm;

				$('div#liste-entites input[id^="chk-ENT-"]').each( function( Index ) {
					ent_id = $(this).attr('id').split('-')[2];


					// Cette entité est à ajouter.
					if ( $(this).attr('data-old') == 0 && $(this).is(':checked') === true ) {
						// Vérifie si le droit Administrateur a été coché.
						if ( $('input#chk-ENT_ADM-'+ent_id).is(':checked') === true ) {
							Droit = true;
						} else {
							Droit = false;
						}

						ListeAjouter.push( [ ent_id, Droit ] );
					}


					// Cette entité est à supprimer.
					if ( $(this).attr('data-old') == 1 && $(this).is(':checked') === false ) {
						ListeSupprimer.push( ent_id );
					}


					// Cette entité est peut-être à modifier.
					if ( $(this).attr('data-old') == 1 && $(this).is(':checked') === true ) {
						ent_id_adm = 'input#chk-ENT_ADM-'+ent_id;

						if ( ($(ent_id_adm).is(':checked') === true && $(ent_id_adm).attr('data-old') == 0)
						 || ($(ent_id_adm).is(':checked') === false && $(ent_id_adm).attr('data-old') == 1) ) {
							if ( $(ent_id_adm).is(':checked') === true ) {
								Droit = true;
							} else {
								Droit = false;
							}
							ListeModifier.push( [ ent_id, Droit ] );
						}
					}
				});


				$.ajax({
					url: '../../../Loxense-Utilisateurs.php?Action=AJAX_Associer_Entites',
					type: 'POST',
					async: false,
					dataType: 'json',
					data: $.param({'idn_id': Id, 'supprimerEntites': ListeSupprimer, 'ajouterEntites': ListeAjouter,
						'modifierEntites':ListeModifier}),
						//'listeClesEntitesAdm': ListeClesEntitesAdm, 'listeValeursEntitesAdm': ListeValeursEntitesAdm}),
					success: function( reponse ) {
						if ( reponse['statut'] != 'success' ) {
							afficherMessage( reponse['texteMsg'], reponse['statut'], '#idModalUtilisateur', 0, 'n' );
						}
					}
				});


				// Sauvegarde les Profils ajoutés ou supprimés à l'Utilisateur
				ListeAjouter = [];
				ListeSupprimer = [];

				$("div#liste-profils input").each( function( Index ) {
					if ( $(this).attr('data-old') == 0 && $(this).is(':checked') === true ) {
						ListeAjouter.push( $(this).attr('id').split('-')[2] );
					}

					if ( $(this).attr('data-old') == 1 && $(this).is(':checked') === false ) {
						ListeSupprimer.push( $(this).attr('id').split('-')[2] );
					}
				});

				$.ajax({
					url: '../../../Loxense-Utilisateurs.php?Action=AJAX_Associer_Profils',
					type: 'POST',
					async: false,
					dataType: 'json',
					data: $.param({'idn_id': Id, 'supprimerProfils': ListeSupprimer, 'ajouterProfils': ListeAjouter}),
					success: function( reponse ) {
						if ( reponse['statut'] != 'success' ) {
							afficherMessage( reponse['texteMsg'], reponse['statut'], '#idModalUtilisateur', 0, 'n' );
						}
					}
				});

				$('#idModalUtilisateur').modal('hide');
			} else {
				afficherMessage( reponse['texteMsg'], reponse['statut'], '#formModifierUtilisateur', 0, 'n' );
			}
		}
	});
}