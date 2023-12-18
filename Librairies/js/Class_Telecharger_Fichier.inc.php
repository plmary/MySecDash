<?php
	$Nom_Fichier_Complet = $_POST['nom_fichier_complet'];
	$Nom_Fichier = basename($Nom_Fichier_Complet);

	$Taille_Fichier = filesize($Nom_Fichier_Complet);

	header("Content-disposition: attachment; filename=" . $Nom_Fichier);
	header("Content-Type: application/force-download");
	header("Content-Transfer-Encoding: application/octet-stream");
	header("Content-Length: " . $Taille_Fichier);
	header("Pragma: no-cache");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
	header("Expires: 0");

	ob_clean();
	flush();

	readfile($fichier);

	if ( isset( $_POST['detruire_fichier'] ) ) {
		unlink($Nom_Fichier_Complet);
	}
?>