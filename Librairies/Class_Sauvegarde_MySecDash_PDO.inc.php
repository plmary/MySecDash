<?php
/**
 * Classe pour sauvegarder et restaurer des entités de la base MySecDash en JSON
 * Gère les sociétés et campagnes avec toutes leurs dépendances
 *
 * @author Généré pour MySecDash
 * @version 1.0
 */

class MySecDashBackupRestore {
	private $pdo;
	private $errors = [];
	
	/**
	 * Constructeur
	 * @param PDO $pdo Instance PDO connectée à la base de données
	 */
	public function __construct(PDO $pdo) {
		$this->pdo = $pdo;
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	
	/**
	 * Sauvegarde une société avec toutes ses dépendances dans un fichier JSON
	 * @param int $sct_id ID de la société
	 * @param string $filename Nom du fichier de sortie
	 * @return bool Succès de l'opération
	 */
	public function saveSociete($sct_id, $filename) {
		try {
			$this->errors = [];
			$data = [
				'type' => 'societe',
				'version' => '1.0',
				'export_date' => date('Y-m-d H:i:s'),
				'data' => []
			];
			
			// 1. Société principale
			$data['data']['societe'] = $this->fetchOne("SELECT * FROM sct_societes WHERE sct_id = ?", [$sct_id]);
			if (!$data['data']['societe']) {
				throw new Exception("Société avec l'ID $sct_id non trouvée");
			}
			
			// 2. Tags de la société
			$data['data']['tags'] = $this->fetchAll(
				"SELECT t.* FROM tgs_tags t
                 INNER JOIN sctg_sct_tgs st ON t.tgs_id = st.tgs_id
                 WHERE st.sct_id = ?",
				[$sct_id]
				);
			
			// 3. Échelles de temps
			$data['data']['echelles_temps'] = $this->fetchAll(
				"SELECT * FROM ete_echelle_temps WHERE sct_id = ?",
				[$sct_id]
				);
			
			// 4. Entités
			$data['data']['entites'] = $this->fetchAll(
				"SELECT * FROM ent_entites WHERE sct_id = ?",
				[$sct_id]
				);
			
			// Tags des entités
			$entite_ids = array_column($data['data']['entites'], 'ent_id');
			if (!empty($entite_ids)) {
				$data['data']['entites_tags'] = $this->fetchAll(
					"SELECT et.*, t.tgs_code FROM entg_ent_tgs et
                     INNER JOIN tgs_tags t ON et.tgs_id = t.tgs_id
                     WHERE et.ent_id IN (" . implode(',', $entite_ids) . ")"
					);
			}
			
			// 5. Sites
			$data['data']['sites'] = $this->fetchAll(
				"SELECT * FROM sts_sites WHERE sct_id = ?",
				[$sct_id]
				);
			
			// Tags des sites
			$site_ids = array_column($data['data']['sites'], 'sts_id');
			if (!empty($site_ids)) {
				$data['data']['sites_tags'] = $this->fetchAll(
					"SELECT st.*, t.tgs_code FROM sttg_sts_tgs st
                     INNER JOIN tgs_tags t ON st.tgs_id = t.tgs_id
                     WHERE st.sts_id IN (" . implode(',', $site_ids) . ")"
					);
			}
			
			// 6. Parties prenantes
			$data['data']['parties_prenantes'] = $this->fetchAll(
				"SELECT * FROM ppr_parties_prenantes WHERE sct_id = ?",
				[$sct_id]
				);
			
			// Tags des parties prenantes
			$ppr_ids = array_column($data['data']['parties_prenantes'], 'ppr_id');
			if (!empty($ppr_ids)) {
				$data['data']['parties_prenantes_tags'] = $this->fetchAll(
					"SELECT pt.*, t.tgs_code FROM pptg_ppr_tgs pt
                     INNER JOIN tgs_tags t ON pt.tgs_id = t.tgs_id
                     WHERE pt.ppr_id IN (" . implode(',', $ppr_ids) . ")"
					);
			}
			
			// 7. Identités
			$data['data']['identites'] = $this->fetchAll(
				"SELECT i.*, c.cvl_nom, c.cvl_prenom
                 FROM idn_identites i
                 INNER JOIN cvl_civilites c ON i.cvl_id = c.cvl_id
                 WHERE i.sct_id = ?",
				[$sct_id]
				);
			
			// Relations identités-sociétés
			$idn_ids = array_column($data['data']['identites'], 'idn_id');
			if (!empty($idn_ids)) {
				$data['data']['identites_societes'] = $this->fetchAll(
					"SELECT * FROM idsc_idn_sct WHERE idn_id IN (" . implode(',', $idn_ids) . ")"
					);
				
				$data['data']['identites_entites'] = $this->fetchAll(
					"SELECT * FROM iden_idn_ent WHERE idn_id IN (" . implode(',', $idn_ids) . ")"
					);
				
				$data['data']['identites_profils'] = $this->fetchAll(
					"SELECT ip.*, p.prf_libelle FROM idpr_idn_prf ip
                     INNER JOIN prf_profils p ON ip.prf_id = p.prf_id
                     WHERE ip.idn_id IN (" . implode(',', $idn_ids) . ")"
					);
				
				$data['data']['identites_tags'] = $this->fetchAll(
					"SELECT it.*, t.tgs_code FROM idtg_idn_tgs it
                     INNER JOIN tgs_tags t ON it.tgs_id = t.tgs_id
                     WHERE it.idn_id IN (" . implode(',', $idn_ids) . ")"
					);
			}
			
			// 8. Applications
			$data['data']['applications'] = $this->fetchAll(
				"SELECT a.*, f.frn_nom FROM app_applications a
                 LEFT JOIN frn_fournisseurs f ON a.frn_id = f.frn_id
                 WHERE a.sct_id = ?",
				[$sct_id]
				);
			
			// Relations société-applications
			$app_ids = array_column($data['data']['applications'], 'app_id');
			if (!empty($app_ids)) {
				$data['data']['societes_applications'] = $this->fetchAll(
					"SELECT * FROM scap_sct_app WHERE sct_id = ?",
					[$sct_id]
					);
				
				$data['data']['applications_tags'] = $this->fetchAll(
					"SELECT at.*, t.tgs_code FROM aptg_app_tgs at
                     INNER JOIN tgs_tags t ON at.tgs_id = t.tgs_id
                     WHERE at.app_id IN (" . implode(',', $app_ids) . ")"
					);
			}
			
			// 9. Fournisseurs utilisés
			$data['data']['fournisseurs'] = $this->fetchAll(
				"SELECT DISTINCT f.*, tf.tfr_nom_code
                 FROM frn_fournisseurs f
                 LEFT JOIN tfr_types_fournisseur tf ON f.tfr_id = tf.tfr_id
                 INNER JOIN app_applications a ON f.frn_id = a.frn_id
                 WHERE a.sct_id = ?",
				[$sct_id]
				);
			
			// Tags des fournisseurs
			$frn_ids = array_column($data['data']['fournisseurs'], 'frn_id');
			if (!empty($frn_ids)) {
				$data['data']['fournisseurs_tags'] = $this->fetchAll(
					"SELECT ft.*, t.tgs_code FROM frtg_frn_tgs ft
                     INNER JOIN tgs_tags t ON ft.tgs_id = t.tgs_id
                     WHERE ft.frn_id IN (" . implode(',', $frn_ids) . ")"
					);
			}
			
			// 10. Campagnes utilisées
			$data['data']['campagnes'] = $this->fetchAll(
				"SELECT DISTINCT c.*
                 FROM cmp_campagnes c
                 WHERE c.sct_id = ?",
				[$sct_id]
				);
			$cmp_ids = array_column($data['data']['campagnes'], 'cmp_id');
			
			// 11. Association Campagnes et Entités utilisés
			$data['data']['campagnes_entites'] = $this->fetchAll(
				"SELECT DISTINCT cmen.*
                 FROM cmen_cmp_ent cmen
                 WHERE cmen.cmp_id IN (" . implode(',', $cmp_ids) . ")"
				);
			
			// 12. Association Sociétés et Applications utilisées
			$data['data']['societes_applications'] = $this->fetchAll(
				"SELECT DISTINCT scap.*
                 FROM scap_sct_app scap
                 WHERE scap.sct_id = ?",
				[$sct_id]
				);
			
			// 13. Activités utilisées
			$data['data']['activites'] = $this->fetchAll(
				"SELECT DISTINCT a.*
                 FROM act_activites a
                 WHERE a.cmp_id IN (" . implode(',', $cmp_ids) . ")"
				);
			$act_ids = array_column($data['data']['activites'], 'act_id');
			
			// 14. Association Parties Prenantes et Activités
			$data['data']['partiesprenantes_activites'] = $this->fetchAll(
				"SELECT DISTINCT p.*
                 FROM ppac_ppr_act p
                 WHERE p.act_id IN (" . implode(',', $act_ids) . ")"
				);
			
			// Écriture du fichier JSON
			$json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
			if ($json === false) {
				throw new Exception("Erreur lors de l'encodage JSON: " . json_last_error_msg());
			}
			
			if (file_put_contents($filename, $json) === false) {
				throw new Exception("Impossible d'écrire le fichier $filename");
			}
			
			return true;
			
		} catch (Exception $e) {
			$this->errors[] = $e->getMessage();
			return false;
		}
	}
	
	/**
	 * Sauvegarde une campagne avec toutes ses dépendances dans un fichier JSON
	 * @param int $cmp_id ID de la campagne
	 * @param string $filename Nom du fichier de sortie
	 * @return bool Succès de l'opération
	 */
	public function saveCampagne($cmp_id, $filename) {
		try {
			$this->errors = [];
			$data = [
				'type' => 'campagne',
				'version' => '1.0',
				'export_date' => date('Y-m-d H:i:s'),
				'data' => []
			];
			
			// 1. Campagne principale
			$data['data']['campagne'] = $this->fetchOne(
				"SELECT c.*, s.sct_nom, i.idn_login
                 FROM cmp_campagnes c
                 INNER JOIN sct_societes s ON c.sct_id = s.sct_id
                 LEFT JOIN idn_identites i ON c.idn_id = i.idn_id
                 WHERE c.cmp_id = ?",
				[$cmp_id]
				);
			
			if (!$data['data']['campagne']) {
				throw new Exception("Campagne avec l'ID $cmp_id non trouvée");
			}
			
			// 2. Niveaux d'impact
			$data['data']['niveaux_impact'] = $this->fetchAll(
				"SELECT * FROM nim_niveaux_impact WHERE cmp_id = ? ORDER BY nim_numero",
				[$cmp_id]
				);
			
			// 3. Types d'impact
			$data['data']['types_impact'] = $this->fetchAll(
				"SELECT * FROM tim_types_impact WHERE cmp_id = ? ORDER BY tim_poids",
				[$cmp_id]
				);
			
			// 4. Matrice des impacts
			$data['data']['matrice_impacts'] = $this->fetchAll(
				"SELECT m.*, n.nim_nom_code, t.tim_nom_code
                 FROM mim_matrice_impacts m
                 INNER JOIN nim_niveaux_impact n ON m.nim_id = n.nim_id
                 INNER JOIN tim_types_impact t ON m.tim_id = t.tim_id
                 WHERE m.cmp_id = ?",
				[$cmp_id]
				);
			
			// 5. Sites de la campagne
			$data['data']['campagne_sites'] = $this->fetchAll(
				"SELECT cs.*, s.sts_nom FROM cmst_cmp_sts cs
                 INNER JOIN sts_sites s ON cs.sts_id = s.sts_id
                 WHERE cs.cmp_id = ?",
				[$cmp_id]
				);
			
			// 6. Entités de la campagne
			$data['data']['campagne_entites'] = $this->fetchAll(
				"SELECT ce.*, e.ent_nom,
                 p1.ppr_nom as ppr_cpca_nom, p1.ppr_prenom as ppr_cpca_prenom,
                 p2.ppr_nom as ppr_validation_nom, p2.ppr_prenom as ppr_validation_prenom
                 FROM cmen_cmp_ent ce
                 INNER JOIN ent_entites e ON ce.ent_id = e.ent_id
                 LEFT JOIN ppr_parties_prenantes p1 ON ce.ppr_id_cpca = p1.ppr_id
                 LEFT JOIN ppr_parties_prenantes p2 ON ce.ppr_id_validation = p2.ppr_id
                 WHERE ce.cmp_id = ?",
				[$cmp_id]
				);
			
			// 7. Activités
			$data['data']['activites'] = $this->fetchAll(
				"SELECT a.*, e.ent_nom,
                 pr.ppr_nom as ppr_resp_nom, pr.ppr_prenom as ppr_resp_prenom,
                 ps.ppr_nom as ppr_supp_nom, ps.ppr_prenom as ppr_supp_prenom
                 FROM act_activites a
                 INNER JOIN ent_entites e ON a.ent_id = e.ent_id
                 LEFT JOIN ppr_parties_prenantes pr ON a.ppr_id_responsable = pr.ppr_id
                 LEFT JOIN ppr_parties_prenantes ps ON a.ppr_id_suppleant = ps.ppr_id
                 WHERE a.cmp_id = ?",
				[$cmp_id]
				);
			
			$act_ids = array_column($data['data']['activites'], 'act_id');
			
			if (!empty($act_ids)) {
				$act_ids_str = implode(',', $act_ids);
				
				// Tags des activités
				$data['data']['activites_tags'] = $this->fetchAll(
					"SELECT at.*, t.tgs_code FROM actg_act_tgs at
                     INNER JOIN tgs_tags t ON at.tgs_id = t.tgs_id
                     WHERE at.act_id IN ($act_ids_str)"
					);
				
				// Sites des activités
				$data['data']['activites_sites'] = $this->fetchAll(
					"SELECT acs.*, s.sts_nom FROM acst_act_sts acs
                     INNER JOIN sts_sites s ON acs.sts_id = s.sts_id
                     WHERE acs.act_id IN ($act_ids_str)"
					);
				
				// Applications des activités
				$data['data']['activites_applications'] = $this->fetchAll(
					"SELECT aa.*, ap.app_nom,
                     ed.ete_nom_code as ete_dima_nom, ep.ete_nom_code as ete_pdma_nom
                     FROM acap_act_app aa
                     INNER JOIN app_applications ap ON aa.app_id = ap.app_id
                     LEFT JOIN ete_echelle_temps ed ON aa.ete_id_dima = ed.ete_id
                     LEFT JOIN ete_echelle_temps ep ON aa.ete_id_pdma = ep.ete_id
                     WHERE aa.act_id IN ($act_ids_str)"
					);
				
				// Fournisseurs des activités
				$data['data']['activites_fournisseurs'] = $this->fetchAll(
					"SELECT af.*, f.frn_nom, e.ete_nom_code
                     FROM acfr_act_frn af
                     INNER JOIN frn_fournisseurs f ON af.frn_id = f.frn_id
                     LEFT JOIN ete_echelle_temps e ON af.ete_id = e.ete_id
                     WHERE af.act_id IN ($act_ids_str)"
					);
				
				// Redémarrage utilisateurs
				$data['data']['redemarrage_utilisateurs'] = $this->fetchAll(
					"SELECT ru.*, e.ete_nom_code FROM rut_redemarrage_utilisateurs ru
                     INNER JOIN ete_echelle_temps e ON ru.ete_id = e.ete_id
                     WHERE ru.act_id IN ($act_ids_str)"
					);
				
				// DMIA des activités
				$data['data']['dmia_activites'] = $this->fetchAll(
					"SELECT d.*, e.ete_nom_code,
                     n.nim_nom_code, t.tim_nom_code
                     FROM dma_dmia_activite d
                     INNER JOIN ete_echelle_temps e ON d.ete_id = e.ete_id
                     INNER JOIN mim_matrice_impacts m ON d.mim_id = m.mim_id
                     INNER JOIN nim_niveaux_impact n ON m.nim_id = n.nim_id
                     INNER JOIN tim_types_impact t ON m.tim_id = t.tim_id
                     WHERE d.act_id IN ($act_ids_str)"
					);
				
				// Parties prenantes des activités
				$data['data']['activites_parties_prenantes'] = $this->fetchAll(
					"SELECT pa.*, p.ppr_nom, p.ppr_prenom
                     FROM ppac_ppr_act pa
                     INNER JOIN ppr_parties_prenantes p ON pa.ppr_id = p.ppr_id
                     WHERE pa.act_id IN ($act_ids_str)"
					);
			}
			
			// 8. Plan d'actions
			$data['data']['plan_actions'] = $this->fetchAll(
				"SELECT * FROM pac_plan_actions WHERE cmp_id = ?",
				[$cmp_id]
				);
			
			// Écriture du fichier JSON
			$json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
			if ($json === false) {
				throw new Exception("Erreur lors de l'encodage JSON: " . json_last_error_msg());
			}
			
			if (file_put_contents($filename, $json) === false) {
				throw new Exception("Impossible d'écrire le fichier $filename");
			}
			
			return true;
			
		} catch (Exception $e) {
			$this->errors[] = $e->getMessage();
			return false;
		}
	}
	
	/**
	 * Restaure une société depuis un fichier JSON
	 * @param string $filename Nom du fichier JSON
	 * @param int|null $new_sct_id ID de la société cible (null pour créer une nouvelle)
	 * @return int|false ID de la société restaurée ou false en cas d'erreur
	 */
	public function restoreSociete($filename, $new_sct_id = null) {
		try {
			$this->errors = [];
			
			if (!file_exists($filename)) {
				throw new Exception("Fichier $filename non trouvé");
			}
			
			$json = file_get_contents($filename);
			$data = json_decode($json, true);
			
			if ($data === null) {
				throw new Exception("Erreur lors du décodage JSON: " . json_last_error_msg());
			}
			
			if ($data['type'] !== 'societe') {
				throw new Exception("Ce fichier ne contient pas une sauvegarde de société");
			}
			
			$this->pdo->beginTransaction();
			
			$mapping = ['tags' => [], 'entites' => [], 'sites' => [], 'parties_prenantes' => [],
				'identites' => [], 'applications' => [], 'fournisseurs' => [],
				'echelles_temps' => [], 'profils' => [], 'civilites' => []];
			
			// 1. Restaurer les tags
			foreach ($data['data']['tags'] as $tag) {
				$existing = $this->fetchOne("SELECT tgs_id FROM tgs_tags WHERE tgs_code = ?", [$tag['tgs_code']]);
				if ($existing) {
					$mapping['tags'][$tag['tgs_id']] = $existing['tgs_id'];
				} else {
					$stmt = $this->pdo->prepare(
						"INSERT INTO tgs_tags (tgs_code, tgs_libelle, tgs_description)
                         VALUES (?, ?, ?) RETURNING tgs_id"
						);
					$stmt->execute([$tag['tgs_code'], $tag['tgs_libelle'], $tag['tgs_description']]);
					$mapping['tags'][$tag['tgs_id']] = $stmt->fetchColumn();
				}
			}
			
			// 2. Restaurer ou utiliser la société existante
			if ($new_sct_id === null) {
				$stmt = $this->pdo->prepare(
					"INSERT INTO sct_societes (sct_nom, sct_description)
                     VALUES (?, ?) RETURNING sct_id"
					);
				$stmt->execute([
					$data['data']['societe']['sct_nom'],
					$data['data']['societe']['sct_description']
				]);
				$new_sct_id = $stmt->fetchColumn();
			}
			
			// Tags de la société
			if (isset($data['data']['tags'])) {
				foreach ($data['data']['tags'] as $tag) {
					$this->pdo->prepare("INSERT INTO sctg_sct_tgs (tgs_id, sct_id) VALUES (?, ?)")
					->execute([$mapping['tags'][$tag['tgs_id']], $new_sct_id]);
				}
			}
			
			// 3. Restaurer les échelles de temps
			foreach ($data['data']['echelles_temps'] as $echelle) {
				$stmt = $this->pdo->prepare(
					"INSERT INTO ete_echelle_temps (sct_id, ete_poids, ete_nom_code)
                     VALUES (?, ?, ?) RETURNING ete_id"
					);
				$stmt->execute([$new_sct_id, $echelle['ete_poids'], $echelle['ete_nom_code']]);
				$mapping['echelles_temps'][$echelle['ete_id']] = $stmt->fetchColumn();
			}
			
			// 4. Restaurer les entités
			foreach ($data['data']['entites'] as $entite) {
				$stmt = $this->pdo->prepare(
					"INSERT INTO ent_entites (sct_id, ent_nom, ent_description)
                     VALUES (?, ?, ?) RETURNING ent_id"
					);
				$stmt->execute([$new_sct_id, $entite['ent_nom'], $entite['ent_description']]);
				$new_ent_id = $stmt->fetchColumn();
				$mapping['entites'][$entite['ent_id']] = $new_ent_id;
				
				// Tags des entités
				if (isset($data['data']['entites_tags'])) {
					foreach ($data['data']['entites_tags'] as $et) {
						if ($et['ent_id'] == $entite['ent_id']) {
							$tag_id = $this->fetchOne("SELECT tgs_id FROM tgs_tags WHERE tgs_code = ?", [$et['tgs_code']]);
							if ($tag_id) {
								$this->pdo->prepare("INSERT INTO entg_ent_tgs (tgs_id, ent_id) VALUES (?, ?)")
								->execute([$tag_id['tgs_id'], $new_ent_id]);
							}
						}
					}
				}
			}
			
			// 5. Restaurer les sites
			foreach ($data['data']['sites'] as $site) {
				$stmt = $this->pdo->prepare(
					"INSERT INTO sts_sites (sct_id, sts_nom, sts_description)
                     VALUES (?, ?, ?) RETURNING sts_id"
					);
				$stmt->execute([$new_sct_id, $site['sts_nom'], $site['sts_description']]);
				$new_sts_id = $stmt->fetchColumn();
				$mapping['sites'][$site['sts_id']] = $new_sts_id;
				
				// Tags des sites
				if (isset($data['data']['sites_tags'])) {
					foreach ($data['data']['sites_tags'] as $st) {
						if ($st['sts_id'] == $site['sts_id']) {
							$tag_id = $this->fetchOne("SELECT tgs_id FROM tgs_tags WHERE tgs_code = ?", [$st['tgs_code']]);
							if ($tag_id) {
								$this->pdo->prepare("INSERT INTO sttg_sts_tgs (tgs_id, sts_id) VALUES (?, ?)")
								->execute([$tag_id['tgs_id'], $new_sts_id]);
							}
						}
					}
				}
			}
			
			// 6. Restaurer les parties prenantes
			foreach ($data['data']['parties_prenantes'] as $ppr) {
				$stmt = $this->pdo->prepare(
					"INSERT INTO ppr_parties_prenantes (sct_id, ppr_nom, ppr_prenom, ppr_interne, ppr_description)
                     VALUES (?, ?, ?, ?, ?) RETURNING ppr_id"
					);
				$stmt->execute([
					$new_sct_id, $ppr['ppr_nom'], $ppr['ppr_prenom'],
					$ppr['ppr_interne'], $ppr['ppr_description']
				]);
				$new_ppr_id = $stmt->fetchColumn();
				$mapping['parties_prenantes'][$ppr['ppr_id']] = $new_ppr_id;
				
				// Tags des parties prenantes
				if (isset($data['data']['parties_prenantes_tags'])) {
					foreach ($data['data']['parties_prenantes_tags'] as $pt) {
						if ($pt['ppr_id'] == $ppr['ppr_id']) {
							$tag_id = $this->fetchOne("SELECT tgs_id FROM tgs_tags WHERE tgs_code = ?", [$pt['tgs_code']]);
							if ($tag_id) {
								$this->pdo->prepare("INSERT INTO pptg_ppr_tgs (ppr_id, tgs_id) VALUES (?, ?)")
								->execute([$new_ppr_id, $tag_id['tgs_id']]);
							}
						}
					}
				}
			}
			
			// 7. Restaurer les fournisseurs
			if (isset($data['data']['fournisseurs'])) {
				foreach ($data['data']['fournisseurs'] as $frn) {
					// Vérifier si le fournisseur existe déjà
					$existing = $this->fetchOne("SELECT frn_id FROM frn_fournisseurs WHERE frn_nom = ?", [$frn['frn_nom']]);
					if ($existing) {
						$mapping['fournisseurs'][$frn['frn_id']] = $existing['frn_id'];
					} else {
						$tfr_id = null;
						if ($frn['tfr_nom_code']) {
							$tfr = $this->fetchOne("SELECT tfr_id FROM tfr_types_fournisseur WHERE tfr_nom_code = ?", [$frn['tfr_nom_code']]);
							$tfr_id = $tfr ? $tfr['tfr_id'] : null;
						}
						
						$stmt = $this->pdo->prepare(
							"INSERT INTO frn_fournisseurs (tfr_id, frn_nom, frn_description)
                             VALUES (?, ?, ?) RETURNING frn_id"
							);
						$stmt->execute([$tfr_id, $frn['frn_nom'], $frn['frn_description']]);
						$new_frn_id = $stmt->fetchColumn();
						$mapping['fournisseurs'][$frn['frn_id']] = $new_frn_id;
						
						// Tags des fournisseurs
						if (isset($data['data']['fournisseurs_tags'])) {
							foreach ($data['data']['fournisseurs_tags'] as $ft) {
								if ($ft['frn_id'] == $frn['frn_id']) {
									$tag_id = $this->fetchOne("SELECT tgs_id FROM tgs_tags WHERE tgs_code = ?", [$ft['tgs_code']]);
									if ($tag_id) {
										$this->pdo->prepare("INSERT INTO frtg_frn_tgs (frn_id, tgs_id) VALUES (?, ?)")
										->execute([$new_frn_id, $tag_id['tgs_id']]);
									}
								}
							}
						}
					}
				}
			}
			
			// 8. Restaurer les applications
			if (isset($data['data']['applications'])) {
				foreach ($data['data']['applications'] as $app) {
					$frn_id = null;
					if ($app['frn_nom']) {
						$frn = $this->fetchOne("SELECT frn_id FROM frn_fournisseurs WHERE frn_nom = ?", [$app['frn_nom']]);
						$frn_id = $frn ? $frn['frn_id'] : ($mapping['fournisseurs'][$app['frn_id']] ?? null);
					}
					
					$stmt = $this->pdo->prepare(
						"INSERT INTO app_applications (frn_id, sct_id, app_nom, app_nom_alias, app_hebergement, app_niveau_service, app_description)
                         VALUES (?, ?, ?, ?, ?, ?, ?) RETURNING app_id"
						);
					$stmt->execute([
						$frn_id, $new_sct_id, $app['app_nom'], $app['app_nom_alias'],
						$app['app_hebergement'], $app['app_niveau_service'], $app['app_description']
					]);
					$new_app_id = $stmt->fetchColumn();
					$mapping['applications'][$app['app_id']] = $new_app_id;
					
					// Tags des applications
					if (isset($data['data']['applications_tags'])) {
						foreach ($data['data']['applications_tags'] as $at) {
							if ($at['app_id'] == $app['app_id']) {
								$tag_id = $this->fetchOne("SELECT tgs_id FROM tgs_tags WHERE tgs_code = ?", [$at['tgs_code']]);
								if ($tag_id) {
									$this->pdo->prepare("INSERT INTO aptg_app_tgs (app_id, tgs_id) VALUES (?, ?)")
									->execute([$new_app_id, $tag_id['tgs_id']]);
								}
							}
						}
					}
				}
				
				// Relations société-applications
				if (isset($data['data']['societes_applications'])) {
					foreach ($data['data']['societes_applications'] as $scap) {
						if (isset($mapping['applications'][$scap['app_id']])) {
							$ete_dima = isset($mapping['echelles_temps'][$scap['ete_id_dima_dsi']])
							? $mapping['echelles_temps'][$scap['ete_id_dima_dsi']] : null;
							$ete_pdma = isset($mapping['echelles_temps'][$scap['ete_id_pdma_dsi']])
							? $mapping['echelles_temps'][$scap['ete_id_pdma_dsi']] : null;
							
							$this->pdo->prepare(
								"INSERT INTO scap_sct_app (app_id, sct_id, ete_id_dima_dsi, ete_id_pdma_dsi, scap_description_dima, scap_description_pdma)
                                 VALUES (?, ?, ?, ?, ?, ?)"
								)->execute([
									$mapping['applications'][$scap['app_id']], $new_sct_id,
									$ete_dima, $ete_pdma,
									$scap['scap_description_dima'], $scap['scap_description_pdma']
								]);
						}
					}
				}
			}
			
			// 9. Restaurer les identités (sans les mots de passe pour des raisons de sécurité)
			if (isset($data['data']['identites'])) {
				foreach ($data['data']['identites'] as $idn) {
					// Vérifier/créer la civilité
					$cvl = $this->fetchOne(
						"SELECT cvl_id FROM cvl_civilites WHERE cvl_nom = ? AND cvl_prenom = ?",
						[$idn['cvl_nom'], $idn['cvl_prenom']]
						);
					if (!$cvl) {
						$stmt = $this->pdo->prepare(
							"INSERT INTO cvl_civilites (cvl_nom, cvl_prenom) VALUES (?, ?) RETURNING cvl_id"
							);
						$stmt->execute([$idn['cvl_nom'], $idn['cvl_prenom']]);
						$mapping['civilites'][$idn['cvl_id']] = $stmt->fetchColumn();
					} else {
						$mapping['civilites'][$idn['cvl_id']] = $cvl['cvl_id'];
					}
					
					// Créer l'identité avec un mot de passe temporaire
					$stmt = $this->pdo->prepare(
						"INSERT INTO idn_identites (sct_id, cvl_id, idn_login, idn_courriel, idn_authentifiant,
                         idn_grain_sel, idn_changer_authentifiant, idn_tentative, idn_desactiver,
                         idn_super_admin, idn_auditeur, idn_date_expiration, idn_date_modification_authentifiant)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) RETURNING idn_id"
						);
					
					// Générer un mot de passe temporaire
					$temp_salt = bin2hex(random_bytes(16));
					$temp_hash = hash('sha256', 'ChangeMe123!' . $temp_salt);
					
					$stmt->execute([
						$new_sct_id, $mapping['civilites'][$idn['cvl_id']],
						$idn['idn_login'] . '_restored', // Modifier le login pour éviter les doublons
						$idn['idn_courriel'],
						$temp_hash, $temp_salt,
						true, // Forcer le changement de mot de passe
						0, false, false, false,
						date('Y-m-d H:i:s', strtotime('+1 year')),
						date('Y-m-d H:i:s')
					]);
					$new_idn_id = $stmt->fetchColumn();
					$mapping['identites'][$idn['idn_id']] = $new_idn_id;
					
					// Relations identité-société
					$this->pdo->prepare("INSERT INTO idsc_idn_sct (idn_id, sct_id, idsc_admin) VALUES (?, ?, ?)")
					->execute([$new_idn_id, $new_sct_id, false]);
				}
				
				// Relations identités-entités
				if (isset($data['data']['identites_entites'])) {
					foreach ($data['data']['identites_entites'] as $iden) {
						if (isset($mapping['identites'][$iden['idn_id']]) && isset($mapping['entites'][$iden['ent_id']])) {
							$this->pdo->prepare("INSERT INTO iden_idn_ent (idn_id, ent_id, iden_admin) VALUES (?, ?, ?)")
							->execute([
								$mapping['identites'][$iden['idn_id']],
								$mapping['entites'][$iden['ent_id']],
								$iden['iden_admin']
							]);
						}
					}
				}
				
				// Relations identités-profils
				if (isset($data['data']['identites_profils'])) {
					foreach ($data['data']['identites_profils'] as $idpr) {
						if (isset($mapping['identites'][$idpr['idn_id']])) {
							$prf = $this->fetchOne("SELECT prf_id FROM prf_profils WHERE prf_libelle = ?", [$idpr['prf_libelle']]);
							if ($prf) {
								$this->pdo->prepare("INSERT INTO idpr_idn_prf (idn_id, prf_id) VALUES (?, ?)")
								->execute([$mapping['identites'][$idpr['idn_id']], $prf['prf_id']]);
							}
						}
					}
				}
				
				// Tags des identités
				if (isset($data['data']['identites_tags'])) {
					foreach ($data['data']['identites_tags'] as $it) {
						if (isset($mapping['identites'][$it['idn_id']])) {
							$tag_id = $this->fetchOne("SELECT tgs_id FROM tgs_tags WHERE tgs_code = ?", [$it['tgs_code']]);
							if ($tag_id) {
								$this->pdo->prepare("INSERT INTO idtg_idn_tgs (tgs_id, idn_id) VALUES (?, ?)")
								->execute([$tag_id['tgs_id'], $mapping['identites'][$it['idn_id']]]);
							}
						}
					}
				}
			}
			
			$this->pdo->commit();
			return $new_sct_id;
			
		} catch (Exception $e) {
			$this->pdo->rollBack();
			$this->errors[] = $e->getMessage();
			return false;
		}
	}
	
	/**
	 * Restaure une campagne depuis un fichier JSON
	 * @param string $filename Nom du fichier JSON
	 * @param int $target_sct_id ID de la société cible
	 * @param int|null $new_cmp_id ID de la campagne cible (null pour créer une nouvelle)
	 * @return int|false ID de la campagne restaurée ou false en cas d'erreur
	 */
	public function restoreCampagne($filename, $target_sct_id, $new_cmp_id = null) {
		try {
			$this->errors = [];
			
			if (!file_exists($filename)) {
				throw new Exception("Fichier $filename non trouvé");
			}
			
			$json = file_get_contents($filename);
			$data = json_decode($json, true);
			
			if ($data === null) {
				throw new Exception("Erreur lors du décodage JSON: " . json_last_error_msg());
			}
			
			if ($data['type'] !== 'campagne') {
				throw new Exception("Ce fichier ne contient pas une sauvegarde de campagne");
			}
			
			// Vérifier que la société cible existe
			$sct = $this->fetchOne("SELECT sct_id FROM sct_societes WHERE sct_id = ?", [$target_sct_id]);
			if (!$sct) {
				throw new Exception("La société cible avec l'ID $target_sct_id n'existe pas");
			}
			
			$this->pdo->beginTransaction();
			
			$mapping = [
				'niveaux_impact' => [], 'types_impact' => [], 'matrice_impacts' => [],
				'sites' => [], 'entites' => [], 'parties_prenantes' => [],
				'activites' => [], 'applications' => [], 'fournisseurs' => [],
				'echelles_temps' => []
			];
			
			// Récupérer les mappings existants de la société cible
			$existing_sites = $this->fetchAll("SELECT sts_id, sts_nom FROM sts_sites WHERE sct_id = ?", [$target_sct_id]);
			foreach ($existing_sites as $site) {
				$mapping['sites'][$site['sts_nom']] = $site['sts_id'];
			}
			
			$existing_entites = $this->fetchAll("SELECT ent_id, ent_nom FROM ent_entites WHERE sct_id = ?", [$target_sct_id]);
			foreach ($existing_entites as $entite) {
				$mapping['entites'][$entite['ent_nom']] = $entite['ent_id'];
			}
			
			$existing_ppr = $this->fetchAll(
				"SELECT ppr_id, ppr_nom, ppr_prenom FROM ppr_parties_prenantes WHERE sct_id = ?",
				[$target_sct_id]
				);
			foreach ($existing_ppr as $ppr) {
				$mapping['parties_prenantes'][$ppr['ppr_nom'] . '|' . $ppr['ppr_prenom']] = $ppr['ppr_id'];
			}
			
			$existing_echelles = $this->fetchAll(
				"SELECT ete_id, ete_nom_code FROM ete_echelle_temps WHERE sct_id = ?",
				[$target_sct_id]
				);
			foreach ($existing_echelles as $echelle) {
				$mapping['echelles_temps'][$echelle['ete_nom_code']] = $echelle['ete_id'];
			}
			
			$existing_apps = $this->fetchAll("SELECT app_id, app_nom FROM app_applications WHERE sct_id = ?", [$target_sct_id]);
			foreach ($existing_apps as $app) {
				$mapping['applications'][$app['app_nom']] = $app['app_id'];
			}
			
			$existing_frn = $this->fetchAll("SELECT frn_id, frn_nom FROM frn_fournisseurs");
			foreach ($existing_frn as $frn) {
				$mapping['fournisseurs'][$frn['frn_nom']] = $frn['frn_id'];
			}
			
			// 1. Créer ou utiliser la campagne
			if ($new_cmp_id === null) {
				$idn_id = null;
				if ($data['data']['campagne']['idn_login']) {
					$idn = $this->fetchOne(
						"SELECT idn_id FROM idn_identites WHERE idn_login = ? AND sct_id = ?",
						[$data['data']['campagne']['idn_login'], $target_sct_id]
						);
					$idn_id = $idn ? $idn['idn_id'] : null;
				}
				
				$stmt = $this->pdo->prepare(
					"INSERT INTO cmp_campagnes (sct_id, idn_id, cmp_date, cmp_flag_validation,
                     cmp_date_validation, cmp_niveau_impact_accepte)
                     VALUES (?, ?, ?, ?, ?, ?) RETURNING cmp_id"
					);
				$stmt->execute([
					$target_sct_id, $idn_id,
					$data['data']['campagne']['cmp_date'],
					$data['data']['campagne']['cmp_flag_validation'],
					$data['data']['campagne']['cmp_date_validation'],
					$data['data']['campagne']['cmp_niveau_impact_accepte']
				]);
				$new_cmp_id = $stmt->fetchColumn();
			}
			
			// 2. Restaurer les niveaux d'impact
			foreach ($data['data']['niveaux_impact'] as $niveau) {
				$stmt = $this->pdo->prepare(
					"INSERT INTO nim_niveaux_impact (cmp_id, nim_numero, nim_poids, nim_nom_code, nim_couleur)
                     VALUES (?, ?, ?, ?, ?) RETURNING nim_id"
					);
				$stmt->execute([
					$new_cmp_id, $niveau['nim_numero'], $niveau['nim_poids'],
					$niveau['nim_nom_code'], $niveau['nim_couleur']
				]);
				$mapping['niveaux_impact'][$niveau['nim_nom_code']] = $stmt->fetchColumn();
			}
			
			// 3. Restaurer les types d'impact
			foreach ($data['data']['types_impact'] as $type) {
				$stmt = $this->pdo->prepare(
					"INSERT INTO tim_types_impact (cmp_id, tim_poids, tim_nom_code)
                     VALUES (?, ?, ?) RETURNING tim_id"
					);
				$stmt->execute([$new_cmp_id, $type['tim_poids'], $type['tim_nom_code']]);
				$mapping['types_impact'][$type['tim_nom_code']] = $stmt->fetchColumn();
			}
			
			// 4. Restaurer la matrice des impacts
			foreach ($data['data']['matrice_impacts'] as $matrice) {
				$nim_id = $mapping['niveaux_impact'][$matrice['nim_nom_code']];
				$tim_id = $mapping['types_impact'][$matrice['tim_nom_code']];
				
				$stmt = $this->pdo->prepare(
					"INSERT INTO mim_matrice_impacts (cmp_id, nim_id, tim_id, mim_description)
                     VALUES (?, ?, ?, ?) RETURNING mim_id"
					);
				$stmt->execute([$new_cmp_id, $nim_id, $tim_id, $matrice['mim_description']]);
				$mapping['matrice_impacts'][$matrice['nim_nom_code'] . '|' . $matrice['tim_nom_code']] = $stmt->fetchColumn();
			}
			
			// 5. Restaurer les sites de la campagne
			if (isset($data['data']['campagne_sites'])) {
				foreach ($data['data']['campagne_sites'] as $cs) {
					$sts_id = $mapping['sites'][$cs['sts_nom']] ?? null;
					if ($sts_id) {
						$this->pdo->prepare("INSERT INTO cmst_cmp_sts (cmp_id, sts_id) VALUES (?, ?)")
						->execute([$new_cmp_id, $sts_id]);
					}
				}
			}
			
			// 6. Restaurer les entités de la campagne
			if (isset($data['data']['campagne_entites'])) {
				foreach ($data['data']['campagne_entites'] as $ce) {
					$ent_id = $mapping['entites'][$ce['ent_nom']] ?? null;
					if (!$ent_id) continue;
					
					$ppr_cpca_id = null;
					if ($ce['ppr_cpca_nom']) {
						$ppr_cpca_id = $mapping['parties_prenantes'][$ce['ppr_cpca_nom'] . '|' . $ce['ppr_cpca_prenom']] ?? null;
					}
					
					$ppr_validation_id = null;
					if ($ce['ppr_validation_nom']) {
						$ppr_validation_id = $mapping['parties_prenantes'][$ce['ppr_validation_nom'] . '|' . $ce['ppr_validation_prenom']] ?? null;
					}
					
					$this->pdo->prepare(
						"INSERT INTO cmen_cmp_ent (ent_id, cmp_id, ppr_id_cpca, cmen_date_entretien_cpca,
                         ppr_id_validation, cmen_date_validation, cmen_effectif_total)
                         VALUES (?, ?, ?, ?, ?, ?, ?)"
						)->execute([
							$ent_id, $new_cmp_id, $ppr_cpca_id, $ce['cmen_date_entretien_cpca'],
							$ppr_validation_id, $ce['cmen_date_validation'], $ce['cmen_effectif_total']
						]);
				}
			}
			
			// 7. Restaurer les activités
			if (isset($data['data']['activites'])) {
				foreach ($data['data']['activites'] as $act) {
					$ent_id = $mapping['entites'][$act['ent_nom']] ?? null;
					if (!$ent_id) continue;
					
					$ppr_resp_id = null;
					if ($act['ppr_resp_nom']) {
						$ppr_resp_id = $mapping['parties_prenantes'][$act['ppr_resp_nom'] . '|' . $act['ppr_resp_prenom']] ?? null;
					}
					
					$ppr_supp_id = null;
					if ($act['ppr_supp_nom']) {
						$ppr_supp_id = $mapping['parties_prenantes'][$act['ppr_supp_nom'] . '|' . $act['ppr_supp_prenom']] ?? null;
					}
					
					$stmt = $this->pdo->prepare(
						"INSERT INTO act_activites (ent_id, cmp_id, ppr_id_responsable, ppr_id_suppleant,
                         act_nom, act_teletravail, act_effectifs_en_nominal, act_taux_occupation,
                         act_effectifs_a_distance, act_justification_dmia, act_description,
                         act_dependances_internes_amont, act_dependances_internes_aval,
                         act_description_entraides, act_strategie_montee_en_charge)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) RETURNING act_id"
						);
					$stmt->execute([
						$ent_id, $new_cmp_id, $ppr_resp_id, $ppr_supp_id,
						$act['act_nom'], $act['act_teletravail'], $act['act_effectifs_en_nominal'],
						$act['act_taux_occupation'], $act['act_effectifs_a_distance'],
						$act['act_justification_dmia'], $act['act_description'],
						$act['act_dependances_internes_amont'], $act['act_dependances_internes_aval'],
						$act['act_description_entraides'], $act['act_strategie_montee_en_charge']
					]);
					$new_act_id = $stmt->fetchColumn();
					$mapping['activites'][$act['act_id']] = $new_act_id;
				}
			}
			
			// 8. Restaurer les sites des activités
			if (isset($data['data']['activites_sites'])) {
				foreach ($data['data']['activites_sites'] as $acst) {
					$act_id = $mapping['activites'][$acst['act_id']] ?? null;
					$sts_id = $mapping['sites'][$acst['sts_nom']] ?? null;
					
					if ($act_id && $sts_id) {
						$this->pdo->prepare(
							"INSERT INTO acst_act_sts (act_id, cmp_id, sts_id, acst_type_site)
                             VALUES (?, ?, ?, ?)"
							)->execute([$act_id, $new_cmp_id, $sts_id, $acst['acst_type_site']]);
					}
				}
			}
			
			// 9. Restaurer les applications des activités
			if (isset($data['data']['activites_applications'])) {
				foreach ($data['data']['activites_applications'] as $acap) {
					$act_id = $mapping['activites'][$acap['act_id']] ?? null;
					$app_id = $mapping['applications'][$acap['app_nom']] ?? null;
					
					if ($act_id && $app_id) {
						$ete_dima = $mapping['echelles_temps'][$acap['ete_dima_nom']] ?? null;
						$ete_pdma = $mapping['echelles_temps'][$acap['ete_pdma_nom']] ?? null;
						
						$this->pdo->prepare(
							"INSERT INTO acap_act_app (act_id, app_id, ete_id_dima, ete_id_pdma,
                             acap_palliatif, acap_donnees, acap_hebergement, acap_niveau_service)
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
							)->execute([
								$act_id, $app_id, $ete_dima, $ete_pdma,
								$acap['acap_palliatif'], $acap['acap_donnees'],
								$acap['acap_hebergement'], $acap['acap_niveau_service']
							]);
					}
				}
			}
			
			// 10. Restaurer les fournisseurs des activités
			if (isset($data['data']['activites_fournisseurs'])) {
				foreach ($data['data']['activites_fournisseurs'] as $acfr) {
					$act_id = $mapping['activites'][$acfr['act_id']] ?? null;
					$frn_id = $mapping['fournisseurs'][$acfr['frn_nom']] ?? null;
					
					if ($act_id && $frn_id) {
						$ete_id = $mapping['echelles_temps'][$acfr['ete_nom_code']] ?? null;
						
						$this->pdo->prepare(
							"INSERT INTO acfr_act_frn (act_id, frn_id, ete_id,
                             acfr_consequence_indisponibilite, acfr_palliatif_tiers)
                             VALUES (?, ?, ?, ?, ?)"
							)->execute([
								$act_id, $frn_id, $ete_id,
								$acfr['acfr_consequence_indisponibilite'], $acfr['acfr_palliatif_tiers']
							]);
					}
				}
			}
			
			// 11. Restaurer le redémarrage utilisateurs
			if (isset($data['data']['redemarrage_utilisateurs'])) {
				foreach ($data['data']['redemarrage_utilisateurs'] as $rut) {
					$act_id = $mapping['activites'][$rut['act_id']] ?? null;
					$ete_id = $mapping['echelles_temps'][$rut['ete_nom_code']] ?? null;
					
					if ($act_id && $ete_id) {
						$this->pdo->prepare(
							"INSERT INTO rut_redemarrage_utilisateurs (ete_id, act_id, rut_nbr_utilisateurs_a_redemarrer)
                             VALUES (?, ?, ?)"
							)->execute([$ete_id, $act_id, $rut['rut_nbr_utilisateurs_a_redemarrer']]);
					}
				}
			}
			
			// 12. Restaurer les DMIA des activités
			if (isset($data['data']['dmia_activites'])) {
				foreach ($data['data']['dmia_activites'] as $dma) {
					$act_id = $mapping['activites'][$dma['act_id']] ?? null;
					$ete_id = $mapping['echelles_temps'][$dma['ete_nom_code']] ?? null;
					$mim_id = $mapping['matrice_impacts'][$dma['nim_nom_code'] . '|' . $dma['tim_nom_code']] ?? null;
					
					if ($act_id && $ete_id && $mim_id) {
						$this->pdo->prepare(
							"INSERT INTO dma_dmia_activite (act_id, ete_id, mim_id, cmp_id)
                             VALUES (?, ?, ?, ?)"
							)->execute([$act_id, $ete_id, $mim_id, $new_cmp_id]);
					}
				}
			}
			
			// 13. Restaurer les parties prenantes des activités
			if (isset($data['data']['activites_parties_prenantes'])) {
				foreach ($data['data']['activites_parties_prenantes'] as $ppac) {
					$act_id = $mapping['activites'][$ppac['act_id']] ?? null;
					$ppr_id = $mapping['parties_prenantes'][$ppac['ppr_nom'] . '|' . $ppac['ppr_prenom']] ?? null;
					
					if ($act_id && $ppr_id) {
						$this->pdo->prepare(
							"INSERT INTO ppac_ppr_act (ppr_id, act_id, cmp_id, ppac_description)
                             VALUES (?, ?, ?, ?)"
							)->execute([$ppr_id, $act_id, $new_cmp_id, $ppac['ppac_description']]);
					}
				}
			}
			
			// 14. Restaurer le plan d'actions
			if (isset($data['data']['plan_actions'])) {
				foreach ($data['data']['plan_actions'] as $pac) {
					$this->pdo->prepare(
						"INSERT INTO pac_plan_actions (cmp_id, pac_action_defaut,
                         pac_debut_date_previsionnelle, pac_fin_date_previsionnelle,
                         pac_debut_date_reelle, pac_fin_date_reelle, pac_nom, pac_description)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
						)->execute([
							$new_cmp_id, $pac['pac_action_defaut'],
							$pac['pac_debut_date_previsionnelle'], $pac['pac_fin_date_previsionnelle'],
							$pac['pac_debut_date_reelle'], $pac['pac_fin_date_reelle'],
							$pac['pac_nom'], $pac['pac_description']
						]);
				}
			}
			
			$this->pdo->commit();
			return $new_cmp_id;
			
		} catch (Exception $e) {
			$this->pdo->rollBack();
			$this->errors[] = $e->getMessage();
			return false;
		}
	}
	
	/**
	 * Retourne les erreurs rencontrées
	 * @return array Liste des erreurs
	 */
	public function getErrors() {
		return $this->errors;
	}
	
	/**
	 * Récupère une seule ligne de résultat
	 * @param string $sql Requête SQL
	 * @param array $params Paramètres
	 * @return array|false
	 */
	private function fetchOne($sql, $params = []) {
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($params);
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}
	
	/**
	 * Récupère toutes les lignes de résultat
	 * @param string $sql Requête SQL
	 * @param array $params Paramètres
	 * @return array
	 */
	private function fetchAll($sql, $params = []) {
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($params);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}

// Exemple d'utilisation
try {
	// Connexion à la base de données
	$pdo = new PDO('pgsql:host=localhost;port=5432;dbname=mysecdash', 'mysecdash_u', 'hNyxR4RNzo');

	// Instanciation de la classe
	$backup = new MySecDashBackupRestore($pdo);

	// SAUVEGARDE
	// Sauvegarder une société
	if ($backup->saveSociete(2, '/Applications/XAMPP/xamppfiles/htdocs/MySecDash/Rapports/societe_export.json')) {
		echo "Société sauvegardée avec succès\n";
	} else {
		echo "Erreurs: " . implode(", ", $backup->getErrors()) . "\n";
	}

/*
	// Sauvegarder une campagne
	if ($backup->saveCampagne(5, 'campagne_export.json')) {
		echo "Campagne sauvegardée avec succès\n";
	} else {
		echo "Erreurs: " . implode(", ", $backup->getErrors()) . "\n";
	}


	// RESTAURATION
	// Restaurer une société (créer une nouvelle)
	$new_sct_id = $backup->restoreSociete('societe_export.json');
	if ($new_sct_id) {
		echo "Société restaurée avec l'ID: $new_sct_id\n";
	} else {
		echo "Erreurs: " . implode(", ", $backup->getErrors()) . "\n";
	}

	// Restaurer une campagne dans une société existante
	$new_cmp_id = $backup->restoreCampagne('campagne_export.json', $target_sct_id = 2);
	if ($new_cmp_id) {
		echo "Campagne restaurée avec l'ID: $new_cmp_id\n";
	} else {
		echo "Erreurs: " . implode(", ", $backup->getErrors()) . "\n";
	}
*/

} catch (PDOException $e) {
	echo "Erreur de connexion: " . $e->getMessage() . "\n";
}

?>