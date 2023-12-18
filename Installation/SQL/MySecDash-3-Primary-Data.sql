--
-- Auteur  : Pierre-Luc MARY
-- Date    : 2017-02-17
-- Package : Loxense
--
-- Commentaire :
-- Ce script insère les données de base utile au démarrage de l'application "loxense".
--


INSERT INTO prs_parametres_systeme (prs_nom, prs_type, prs_valeur, prs_commentaire, prs_groupe, prs_super_admin) VALUES
('authentification_type', 2, 'D', 'Type authentification', 'connexion', FALSE),
('expiration_time', 1, '60', 'Nombre de minutes avant expiration', 'connexion', FALSE),
('default_password', 2, 'Coucou!', 'Mot de passe par défaut', 'connexion', FALSE),
('account_lifetime', 1, '6', 'Nombre de mois avant désactivation de l identité', 'connexion', FALSE),
('max_attempt', 1, '10', 'Nombre maximum de tentative de connexion', 'connexion', FALSE),
('min_password_size', 1, '8', 'Taille minimum d''un mot de passe', 'connexion', FALSE),
('password_complexity', 1, '3', 'Complexité demandée à la création d un mot de passe', 'connexion', FALSE),
('root_alternative_boot', 0, 'TRUE', 'authentification alternative au "root"', 'connexion', TRUE),
('ldap_ip_address', 2, 'localhost', 'Adresse IP du serveur LDAP', 'connexion', TRUE),
('ldap_ip_port', 1, '10389', 'Port IP du serveur LDAP', 'connexion', TRUE),
('ldap_protocol_version', 1, '3', '', 'connexion', TRUE),
('ldap_organization', 2, 'dc=loxense,dc=fr', 'Précise l''organisation du RDN du LDAP', 'connexion', TRUE),
('ldap_rdn_prefix', 2, 'uid', 'Précise le préfixe du RDN du LDAP', 'connexion', TRUE),
('ldap_ssl', 0, 'FALSE', 'Appel LDAP en SSL', 'connexion', TRUE),

('language_alert', 2, 'fr', 'Langue dans laquelle seront affichées les alertes', 'alerte', FALSE),
('syslog_alert', 0, 'TRUE', 'Alerter par syslog', 'alerte', TRUE),
('syslog_host', 2, 'localhost', 'Serveur sur lequel on réplique le flux SYSLOG', 'alerte', TRUE),
('syslog_port', 1, '514', 'Serveur sur lequel on réplique le flux SYSLOG', 'alerte', TRUE),
('syslog_template', 2, 'template_syslog.tmpl', 'Fichier squelette d''un message SYSLOG', 'alerte', TRUE),
('mail_alert', 0, 'FALSE', 'Alerter par courriel', 'alerte', TRUE),
('mail_title', 2, 'Loxense : événement', 'Standardisation du sujet des courriels d''alert', 'alerte', TRUE),
('mail_body_type', 2, 'HTML', 'Format des courriels d''alert (HTML ou TEXT)', 'alerte', TRUE),
('mail_template', 2, 'template_mail.tmpl', 'Fichier squelette du corps du courriel d''alert', 'alerte', TRUE),
('mail_sender', 2, 'loxense@societe.com', 'Emetteur courriel', 'alerte', TRUE),
('mail_receiver', 2, 'supervision@societe.com', 'Destinataire courriel', 'alerte', TRUE),

('limitation_entites', 1, '0', 'Nombre maximum d''Entités dans Loxense', 'limitations', TRUE),
('limitation_civilites', 1, '0', 'Nombre maximum de Civilités dans Loxense', 'limitations', TRUE),
('limitation_profils', 1, '9', 'Nombre maximum de Profils dans Loxense', 'limitations', TRUE),
('limitation_utilisateurs', 1, '0', 'Nombre maximum d''Utilisateurs dans Loxense', 'limitations', TRUE),

('limitation_cartographies', 1, '0', 'Nombre maximum de Cartographies dans Loxense', 'limitations', TRUE),
('limitation_actifs_primordiaux', 1, '0', 'Nombre maximum d''Actifs Primodiaux dans Loxense', 'limitations', TRUE),
('limitation_actifs_supports', 1, '0', 'Nombre maximum d''Actifs Supports dans Loxense', 'limitations', TRUE),
('limitation_evenements_redoutes', 1, '0', 'Nombre maximum d''Evénements Redoutés dans Loxense', 'limitations', TRUE);


--
-- Contenu de la Civilité de base.
--

INSERT INTO cvl_civilites (cvl_id, cvl_nom, cvl_prenom) VALUES
(1, 'Loxense', 'Administrateur');


SELECT pg_catalog.setval('cvl_civilites_cvl_id_seq', 1, true);


--
-- Contenu de l'Entité de Base.
--

INSERT INTO ent_entites (ent_id, ent_nom) VALUES
(1, 'PLM');


SELECT pg_catalog.setval('ent_entites_ent_id_seq', 1, true);


--
-- Contenu de l'Identité de base. Mot de passe par défaut : "Welcome !"
--

INSERT INTO idn_identites (
idn_id, ent_id, cvl_id, idn_login, idn_authentifiant, idn_grain_sel, idn_changer_authentifiant, idn_super_admin, idn_tentative, idn_desactiver, idn_derniere_connexion, idn_date_expiration, idn_date_modification_authentifiant) VALUES(
1, 1, 1, 'root', 'f6f3b63150785a187a6f425db0b07d34d46642b34a892d493b758bdefd2ceafa', 'Azdser23ddAAXx', false, true, 0, false, CURRENT_TIMESTAMP, '2030-01-01 00:00:00', CURRENT_TIMESTAMP);


SELECT pg_catalog.setval('idn_identites_idn_id_seq', 1, true);


--
-- Création du Profil de base.
--

INSERT INTO prf_profils (prf_id, prf_libelle) VALUES
(1, 'Administrateur Système' ),
(2, 'Admin. Risque'),
(3, 'Gest. Risque'),
(4, 'Gest. Technique');


SELECT pg_catalog.setval('prf_profils_prf_id_seq', 4, true);


--
-- Création du lien entre le Profil de base et l'Identité de base.
--

INSERT INTO idpr_idn_prf (idn_id, prf_id) VALUES
(1, 1);


--
-- Création des Droits.
--

INSERT INTO drt_droits (drt_id, drt_code_libelle) VALUES
(1, 'RGH_1'), --'Lecture'
(2, 'RGH_2'), --'Ecriture'
(3, 'RGH_3'), --'Modification'
(4, 'RGH_4'); --'Suppression'


SELECT pg_catalog.setval('drt_droits_drt_id_seq', 4, true);


--
-- Création des Types d'applications
--

INSERT INTO tap_types_application (tap_id, tap_code_libelle) VALUES
(1, 'TAP_1'), -- Interactive
(2, 'TAP_2'); -- Batch


SELECT pg_catalog.setval('tap_types_application_tap_id_seq', 2, true);


--
-- Création des applications.
--

INSERT INTO ain_applications_internes (ain_id, tap_id, ain_libelle, ain_localisation) VALUES
(1, 1, 'Gestion des Entités', 'Loxense-Entites.php'),
(2, 1, 'Gestion des Civilités', 'Loxense-Civilites.php'),
(3, 1, 'Gestion des Utilisateurs', 'Loxense-Utilisateurs.php'),
(4, 1, 'Gestion des Profils', 'Loxense-Profils.php'),
(5, 1, 'Gestion des Applications', 'Loxense-Applications.php'),
(6, 1, 'Gestion des Privilèges', 'Loxense-Privileges.php'),
(7, 1, 'Paramétrage interne de Loxense', 'Loxense-Parametres.php'),

(8,  1, 'Consultation de l''historique', 'Loxense-Historiques.php'),
(9,  1, 'Consultation des Matrices des Risques', 'Loxense-MatricesRisques.php'),
(10, 1, 'Gestion de l''Appréciation des Risques', 'Loxense-AppreciationRisques.php'),
(11, 1, 'Gestion des Actifs Primordiaux', 'Loxense-ActifsPrimordiaux.php'),
(12, 1, 'Gestion des Actifs Supports', 'Loxense-ActifsSupports.php'),
(13, 1, 'Gestion des Cartographies', 'Loxense-CartographiesRisques.php'),
(14, 1, 'Gestion des Critères de Valorisation des Actifs', 'Loxense-CriteresValorisationActifs.php'),
(15, 1, 'Gestion des Critères d''Appréciation et d''Acceptation des Risques', 'Loxense-CriteresAppreciationAcceptationRisques.php'),
(16, 1, 'Gestion des Evénements Redoutés', 'Loxense-EvenementsRedoutes.php'),
(17, 1, 'Gestion des Impacts Generiques', 'Loxense-ImpactsGeneriques.php'),
(18, 1, 'Gestion des Menaces Génériques', 'Loxense-MenacesGeneriques.php'),
(19, 1, 'Gestion des Mesures de Loxense', 'Loxense-MesuresGeneriques.php'),
(20, 1, 'Gestion des Risques Generiques', 'Loxense-RisquesGeneriques.php'),
(21, 1, 'Gestion des Référentiels de Conformité', 'Loxense-ReferentielsConformite.php'),
(22, 1, 'Gestion des Sources de Menaces', 'Loxense-SourcesMenaces.php'),
(23, 1, 'Gestion des Type d''Actif Support', 'Loxense-TypesActifSupport.php'),
(24, 1, 'Gestion des Types de Menace Générique', 'Loxense-TypesMenaceGenerique.php'),
(25, 1, 'Gestion des Types de Traitement des Risques', 'Loxense-TypesTraitementRisques.php'),
(26, 1, 'Gestion des éditions des Risques', 'Loxense-EditionsRisques.php'),
(27, 1, 'Gestion du Traitement des Risques', 'Loxense-TraitementRisques.php'),
(28, 1, 'Gestion des Equipes de Gestionnaires', 'Loxense-Gestionnaires.php'),
(29, 1, 'Gestion des Vulnérabilités Génériques', 'Loxense-VulnerabilitesGeneriques.php'),
(30, 1, 'Gestion des Actions', 'Loxense-Actions.php'),
(31, 1, 'Gestion des Editions des Actions', 'Loxense-EditionsActions.php'),

(32, 1, 'Gestion des Actifs Primordiaux par Tags', 'Loxense-ActifsPrimordiauxTags.php'),
(33, 1, 'Gestion des Actifs Supports par Tags', 'Loxense-ActifsSupportsTags.php'),
(34, 1, 'Gestion de l''Appréciation des Risques par Tags', 'Loxense-AppreciationRisquesTags.php'),
(35, 1, 'Gestion du Traitement des Risques par Tags', 'Loxense-TraitementRisquesTags.php'),
(36, 1, 'Gestion des Etiquettes', 'Loxense-Etiquettes.php'),
(37, 1, 'Gestion des Imports et Exports de Base', 'Loxense-ExportBase.php'),
(38, 1, 'Gestion des Types de Critère de Valorisation des Risques', 'Loxense-TypesCritereValorisationRisques.php'),

(39, 1, 'Gestion des Grilles d''Impact', 'Loxense-GrillesImpacts.php'),
(40, 1, 'Gestion des Grilles de Vraisemblances', 'Loxense-GrillesVraisemblances.php'),
(41, 1, 'Gestion des Parties Prenantes', 'Loxense-PartiesPrenantes.php'),
(42, 1, 'Gestion de la Conformité', 'Loxense-Conformite.php'),
(43, 1, 'Gestion des éditions de Conformité', 'Loxense-EditionConformite.php'),
(44, 1, 'Gestion des Objectifs Visés', 'Loxense-ObjectifsVises.php'),
(45, 1, 'Gestion des Sources de Risque', 'Loxense-SourcesRisques.php'),

(46, 1, 'Gestion du Référentiel des Actifs Supports', 'Loxense-ReferentielActifsSupports.php'),
(47, 1, 'Gestion du Référentiel des Actifs Primordiaux', 'Loxense-ReferentielActifsPrimordiaux.php');


SELECT pg_catalog.setval('ain_applications_internes_ain_id_seq', 47, true);


--
-- Création du lien entre le Profil de base (Administrateur système), les Applications et les Droits que le profil a sur les Applications.
--

INSERT INTO caa_controle_acces_application_interne (prf_id, ain_id, drt_id) VALUES
(1, 1, 1),
(1, 1, 2),
(1, 1, 3),
(1, 1, 4),
(1, 2, 1),
(1, 2, 2),
(1, 2, 3),
(1, 2, 4),
(1, 3, 1),
(1, 3, 2),
(1, 3, 3),
(1, 3, 4),
(1, 4, 1),
(1, 4, 2),
(1, 4, 3),
(1, 4, 4),
(1, 6, 1),
(1, 6, 2),
(1, 6, 3),
(1, 6, 4),
(1, 7, 1),
(1, 7, 2),
(1, 7, 3),
(1, 7, 4),
(1, 8, 1),
(2, 9, 1),
(2, 10, 1),
(2, 10, 2),
(2, 10, 4),
(2, 10, 3),
(2, 11, 1),
(2, 11, 2),
(2, 11, 4),
(2, 11, 3),
(2, 12, 1),
(2, 12, 2),
(2, 12, 4),
(2, 12, 3),
(2, 13, 2),
(2, 13, 1),
(2, 13, 3),
(2, 13, 4),
(2, 14, 2),
(2, 14, 1),
(2, 14, 3),
(2, 14, 4),
(2, 15, 2),
(2, 15, 1),
(2, 15, 3),
(2, 15, 4),
(2, 16, 1),
(2, 16, 2),
(2, 16, 3),
(2, 16, 4),
(2, 17, 1),
(2, 17, 2),
(2, 17, 4),
(2, 17, 3),
(2, 18, 1),
(2, 18, 2),
(2, 18, 4),
(2, 18, 3),
(2, 19, 1),
(2, 19, 2),
(2, 19, 4),
(2, 19, 3),
(2, 20, 1),
(2, 20, 2),
(2, 20, 4),
(2, 20, 3),
(2, 22, 1),
(2, 22, 2),
(2, 22, 4),
(2, 22, 3),
(2, 23, 1),
(2, 23, 2),
(2, 23, 4),
(2, 23, 3),
(2, 24, 1),
(2, 24, 2),
(2, 24, 4),
(2, 24, 3),
(2, 25, 1),
(2, 25, 2),
(2, 25, 4),
(2, 25, 3),
(2, 26, 1),
(2, 26, 2),
(2, 26, 4),
(2, 26, 3),
(2, 27, 1),
(2, 27, 2),
(2, 27, 3),
(2, 27, 4),
(2, 28, 1),
(2, 28, 2),
(2, 28, 3),
(2, 28, 4),
(2, 33, 1),
(2, 33, 2),
(2, 33, 3),
(2, 33, 4),
(2, 8, 1),
(3, 9, 1),
(3, 10, 1),
(3, 10, 2),
(3, 10, 4),
(3, 10, 3),
(3, 11, 1),
(3, 11, 2),
(3, 11, 4),
(3, 11, 3),
(3, 12, 1),
(3, 12, 2),
(3, 12, 4),
(3, 12, 3),
(3, 13, 1),
(3, 13, 2),
(3, 13, 4),
(3, 13, 3),
(3, 14, 1),
(3, 14, 2),
(3, 14, 4),
(3, 14, 3),
(3, 15, 1),
(3, 15, 2),
(3, 15, 4),
(3, 15, 3),
(3, 16, 2),
(3, 16, 1),
(3, 16, 3),
(3, 16, 4),
(3, 26, 2),
(3, 26, 1),
(3, 26, 3),
(3, 26, 4),
(3, 27, 2),
(3, 27, 1),
(3, 27, 3),
(3, 27, 4),
(3, 8, 1),
(3, 39, 1),
(3, 39, 2),
(3, 39, 3),
(3, 39, 4),
(3, 40, 1),
(3, 40, 2),
(3, 40, 3),
(3, 40, 4),
(4, 11, 1),
(4, 12, 1),
(4, 30, 1),
(4, 30, 2),
(4, 30, 3),
(4, 30, 4),
(4, 31, 1),
(4, 31, 2),
(4, 31, 3),
(4, 31, 4),

(1, 46, 1),
(1, 46, 2),
(1, 46, 3),
(1, 46, 4);



--
-- Création des Types d'Action traçables dans l'outil.
--

INSERT INTO tpa_types_action (tpa_code_libelle) VALUES
('ATP_CNX'), --'connexion'
('ATP_DCNX'), --'Déconnexion'
('ATP_CHG_MDP'), --'Changer de mot de passe'
('ATP_LECTURE'), --'Lecture'
('ATP_ECRITURE'), --'Ecriture'
('ATP_MODIFICATION'), --'Modification'
('ATP_SUPPRESSION'), --'Suppression'
('ATP_DUPLICATION'), --'Duplication'
('ATP_GENERATION'); -- Génération


--
-- Création des Types d'Objet traçables dans l'outil.
--

INSERT INTO tpo_types_objet (tpo_code_libelle) VALUES
('OTP_PARAMETRE'), --'Parametre'
('OTP_LIBELLE_INTERNE'), -- 'Libellé interne'
('OTP_REF_INTERNE'), -- 'Référentiel interne'
('OTP_ENTITE'), -- 'Entité'
('OTP_CIVILITE'), -- 'Civilité'
('OTP_IDENTITE'), -- 'Identité'
('OTP_PROFIL'), -- 'Profil'
('OTP_CTRL_ACCES'), -- 'Contrôle d'accès'
('OTP_APPLICATION'), -- 'Application'
('OTP_DROIT'), -- Droit interne (Profil sur Application)
('OTP_TYPE_ACTIF_SUPPORT'), -- Type d'actif support
('OTP_TYPE_MENACE_GENERIQUE'), -- Type de Menace Générique
('OTP_MENACE_GENERIQUE'), -- Menace Générique
('OTP_IMPACT_GENERIQUE'), -- Impact Générique
('OTP_VULNERABILITE_GENERIQUE'), -- Vulnérabilité Générique
('OTP_SOURCE_MENACE'), -- Source de Menace
('OTP_RISQUE_GENERIQUE'), -- Risque Générique
('OTP_MESURE_GENERIQUE'), -- Mesure de Loxense
('OTP_CARTOGRAPHIE_RISQUES'), -- Cartographie des risques de Loxense
('OTP_CRITERE_VALORISATION'), -- Critere de valorisation des actifs de Loxense
('OTP_CRITERE_APPRECIATION'), -- Critere d'appréciation et d'acceptaion des risques de Loxense
('OTP_ACTIF_PRIMORDIAL'), -- Actif primordial de Loxense
('OTP_ACTIF_SUPPORT'), -- Actif support de Loxense
('OTP_EVENEMENT_REDOUTE'), -- Evénement redouté de Loxense
('OTP_APPRECIATION_RISQUE'), -- Appréciation des risques de Loxense
('OTP_TYPE_TRAITEMENT_RISQUE'), -- Type de traitement des risques de Loxense
('OTP_TRAITEMENT_RISQUE'), -- Traitement des risques de Loxense
('OTP_GESTIONNAIRE'), -- Equipe des Gestionnaires
('OTP_ACTION'), -- Action de sécurité de Loxense
('OTP_TAG'), -- Etiquette ou Tag
('OTP_INFORMATION_COMPLEMENTAIRE'),
('OTP_REFERENTIEL'),
('OTP_OBJECTIF_REFERENTIEL'),
('OTP_THEME_REFERENTIEL'),
('OTP_MESURE_REFERENTIEL'),
('OTP_MESURE_CONFORMITE'),
('OTP_SOURCE_RISQUE'),
('OTP_OBJECTIF_VISE'),
('OTP_PARTIE_PRENANTE');
