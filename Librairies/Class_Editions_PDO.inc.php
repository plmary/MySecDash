<?php
include_once( 'Constants.inc.php' );

include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Parametres_PDO.inc.php' );

include_once( DIR_PHPEXCEL . '/PHPExcel.php' );
include_once( DIR_PHPEXCEL . '/PHPExcel/IOFactory.php' );

include_once( DIR_PHPWORD . '/PHPWord.php' );

include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-GestionEdition.php' );
include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_libelles_generiques.php' );


function mesureSort($a, $b){
	return strnatcmp($a->mgr_code, $b->mgr_code);
}


function codeRisqueSort($a, $b){
	return strnatcmp($a->rcs_code, $b->rcs_code);
}


class Editions extends HBL_Parametres {
/**
* Cette classe gère les éditions des Cartographies.
*
* PHP version 5
* @license Loxense
* @author Pierre-Luc MARY
* @package 1.6
* @date 2014-11-26
*/ 

	const ID_TYPE = PDO::PARAM_INT;

	const POIDS_TYPE = PDO::PARAM_INT;

	const CODE_TYPE = PDO::PARAM_STR;
	const CODE_LENGTH = 10;
	const CODE_C_LENGTH = 6;

	const LIBELLE_TYPE = PDO::PARAM_STR;
	const LIBELLE_LENGTH = 100;
	const LIBELLE_M_LENGTH = 300;
	const LIBELLE_L_LENGTH = 400;

	const PERIODE_TYPE = PDO::PARAM_INT;

	const LANGUE_TYPE = PDO::PARAM_STR;
	const LANGUE_LENGTH = 2;

	const NOM_TYPE = PDO::PARAM_STR;
	const NOM_LENGTH = 50;
	const NOM_L_LENGTH = 100;

	const VALEUR_TYPE = PDO::PARAM_STR;
	const VALEUR_LENGTH = 50;

	const ETAT_IMPORT = "RCS_IMPORT";
	const ETAT_NORMAL = "RCS_NORMAL";
	const ETAT_IGNORE = "RCS_IGNORE";


	public $LastInsertId;

	public $objPHPExcel;
	public $objPHPWord;
	public $editionHTML;
	public $Langue;
	public $FormatEdition;
	public $NomCartographie;
	public $PeriodeCartographie;
	public $VersionCartographie;
	public $EntiteCartographie;
	public $NomFichierExcel;
	public $NomCompletFichierExcel;
	public $NomFichierWord;
	public $NomCompletFichierWord;

	public $TitreDocument;
	public $SujetDocument;
	public $VersionLoxense;

	public $_Colonne_Titre_Actifs_Primordiaux;
	public $_Colonne_Type_Actifs_Primordiaux;

	public $_Ligne_Titre_Actifs_Supports;
	public $_Ligne_Type_Actifs_Supports;
	public $_Ligne_Nom_Actifs_Supports;

	public $styleTitreHorizontal;
	public $styleTitreNomVertical;
	public $styleTitrePrincipalHorizontal;
	public $styleTitrePrincipalHorizontalSpecial;
	public $styleTitreVertical;
	public $styleTitreNomHorizontal;
	public $styleTitrePrincipalVertical;
	public $styleBordureNoirAutourGrisInterieur;
	public $styleTextToutCentre;
	public $styleSurligne;
	public $styleBordureNoirPartout;

	public $Couleur_Defaut_1;
	public $Couleur_Defaut_2;
	public $Couleur_Defaut_3;
	public $Couleur_Defaut_4;
	public $Couleur_Defaut_5;
	public $Couleur_Defaut_6;


	public function __construct( $langue = '', $format = 'html' ) {
	/**
	* Connexion à la base de données via IICA_DB_Connector et instanciation d'un Objet Excel
	*
	* @author Pierre-Luc MARY
	* @date 2014-11-26
	*
	* @param[in] $langue Langue dans laquelle il faudra produire les éditions (par défaut Français).
	*
	* @return Renvoi vrai sur le succès de la connexion à la base de données et l'initialisation de l'objet Excel
	*/
		include_once( HBL_DIR_LIBRARIES . '/Class_HTML.inc.php' );


		$this->Couleur_Defaut_1 = 'A0A0A0'; // Gris
		$this->Couleur_Defaut_2 = 'FFFFFF'; // Blanc
		$this->Couleur_Defaut_3 = '000000'; // Noir
		$this->Couleur_Defaut_4 = 'DCAFDD'; // Mauve moyen-clair
		$this->Couleur_Defaut_5 = '8F58A2'; // Mauve moyen-fort (purple_1) //6F8C9B' // Bleu ardoise
		$this->Couleur_Defaut_6 = 'DAA520'; // Goldenrod // EF8027' //Orange foncé


		parent::__construct();

		$objHTML = new HTML();


		$this->VersionLoxense = $objHTML->Nom_Outil_TXT . ' v' . $objHTML->Version_Outil;

		$this->FormatEdition = $format;
		
		if ( $langue == '' ) {
			$langue = $_SESSION['Language'];
		}

		$this->Langue = $langue;


		switch ( $this->FormatEdition ) {
		 case 'excel':
			$this->objPHPExcel = new PHPExcel();

			// Met en place les Propriétés du Document Excel
			$this->objPHPExcel->getProperties()
				->setCreator( $this->VersionLoxense )
				->setLastModifiedBy( $_SESSION['cvl_prenom'] . ' ' . $_SESSION['cvl_nom'] . ' (' . $_SESSION['ent_libelle'] . ')' )
				->setDescription( $GLOBALS['L_Titre_Principal'] )
				->setKeywords('Loxense')
				->setCompany('Loxense');

			$this->NomFichierExcel = '';

			$this->_Colonne_Titre_Actifs_Primordiaux = 'A';
			$this->_Colonne_Type_Actifs_Primordiaux = $this->xlColumnValue( $this->xlColumnValue( $this->_Colonne_Titre_Actifs_Primordiaux ) + 1 );

			$this->_Ligne_Titre_Actifs_Supports = 1;
			$this->_Ligne_Type_Actifs_Supports = $this->_Ligne_Titre_Actifs_Supports + 1;
			$this->_Ligne_Nom_Actifs_Supports = $this->_Ligne_Type_Actifs_Supports + 1;


			// Définition des styles pour le document Excel
			$this->styleTitreHorizontal = array(
				'font' => array(
					'bold' => true
					),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
					),
				'borders' => array(
					'top' => array(
	 				'style' => PHPExcel_Style_Border::BORDER_THIN
	 				)
				),
				'fill' => array(
		 			'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
		  			'rotation' => 90,
		 			'startcolor' => array(
		 				'argb' => 'FF' . $this->Couleur_Defaut_1
		 				),
		 			'endcolor' => array(
		 				'argb' => 'FF' . $this->Couleur_Defaut_2
		 				)
		 		)
			);

			$this->styleTitreNomVertical = array(
				'font' => array(
					'bold' => true
					),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_BOTTOM,
					'rotation' => 90
					),
				'borders' => array(
					'top' => array(
	 				'style' => PHPExcel_Style_Border::BORDER_THIN
	 				)
				)
			);

			$this->styleTitrePrincipalHorizontal = array(
				'font' => array(
					'bold' => true,
					'color' => array(
		 				'argb' => 'FFFFFFFF'
		 				)
					),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
					),
				'borders' => array(
					'outline' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array('argb' => 'FF' . $this->Couleur_Defaut_3
						),
					)
				),
				'fill' => array(
		 			'type' => PHPExcel_Style_Fill::FILL_SOLID,
		 			'startcolor' => array(
		 				'argb' => 'FF' . $this->Couleur_Defaut_4
		 				)
		 		)
			);

			$this->styleTitrePrincipalHorizontalSpecial = array(
				'font' => array(
					'bold' => true,
					'color' => array(
		 				'argb' => 'FF' . $this->Couleur_Defaut_2
		 				)
					),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
					),
				'borders' => array(
					'outline' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array('argb' => 'FF' . $this->Couleur_Defaut_3
						),
					)
				),
				'fill' => array(
		 			'type' => PHPExcel_Style_Fill::FILL_SOLID,
		 			'startcolor' => array(
		 				'argb' => 'FF' . $this->Couleur_Defaut_5
		 				)
		 		)
			);

			$this->styleTitreVertical = array(
				'font' => array(
					'bold' => true
					),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
					),
				'borders' => array(
					'top' => array(
	 				'style' => PHPExcel_Style_Border::BORDER_THIN
	 				)
				),
				'fill' => array(
		 			'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
		  			'rotation' => 90,
		 			'startcolor' => array(
		 				'argb' => 'FF' . $this->Couleur_Defaut_1
		 				),
		 			'endcolor' => array(
		 				'argb' => 'FF' . $this->Couleur_Defaut_2
		 				)
		 		)
			);

			$this->styleTitreNomHorizontal = array(
				'font' => array(
					'bold' => true
					),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
					) /*,
				'borders' => array(
					'top' => array(
	 				'style' => PHPExcel_Style_Border::BORDER_THIN
	 				)
				) */
			);

			$this->styleTitrePrincipalVertical = array(
				'font' => array(
					'bold' => true,
					'color' => array(
		 				'argb' => 'FF' . $this->Couleur_Defaut_2
		 				)
					),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
					'rotation' => 90
					),
				'borders' => array(
					'outline' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array('argb' => 'FF' . $this->Couleur_Defaut_3),
					)
				),
				'fill' => array(
		 			'type' => PHPExcel_Style_Fill::FILL_SOLID,
		 			'startcolor' => array(
		 				'argb' => 'FF' . $this->Couleur_Defaut_6
		 				)
		 		)
			);

			$this->styleTitrePrincipalVerticalSpecial = array(
				'font' => array(
					'bold' => true,
					'color' => array(
		 				'argb' => 'FF' . $this->Couleur_Defaut_2
		 				)
					),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
					'rotation' => 90
					),
				'borders' => array(
					'outline' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array('argb' => 'FF' . $this->Couleur_Defaut_3),
					)
				),
				'fill' => array(
		 			'type' => PHPExcel_Style_Fill::FILL_SOLID,
		 			'startcolor' => array(
		 				'argb' => 'FF' . $this->Couleur_Defaut_5
		 				)
		 		)
			);

			$this->styleBordureNoirAutourGrisInterieur = array(
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array('argb' => 'FFACACAC'),
					),
					'outline' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array('argb' => 'FF' . $this->Couleur_Defaut_3),
					)
				)
			);

			$this->styleBordureNoirAutour = array(
				'borders' => array(
					'outline' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array('argb' => 'FF' . $this->Couleur_Defaut_3),
					)
				)
			);

			$this->styleBordureNoirPartout = array(
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array('argb' => 'FF' . $this->Couleur_Defaut_3),
					)
				)
			);

			$this->styleTextToutCentre = array(
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
					)
			);

			$this->styleTextCentre = array(
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
					)
			);

			$this->styleTextCentreBas = array(
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_BOTTOM,
					)
			);

			$this->styleTextAGauche = array(
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
					)
			);

			$this->styleTextAGaucheHaut = array(
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
					)
			);

			$this->styleTextADroite = array(
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
					)
			);

			$this->styleSurligne = array(
				'fill' => array(
		 			'type' => PHPExcel_Style_Fill::FILL_SOLID,
		 			'startcolor' => array(
		 				'argb' => 'FFECECEC'
		 				)
		 		)
			);


			break;

		 case 'word':
			$this->objPHPWord = new PHPWord();

			// Met en place les Propriétés du Document Word
			$this->objPHPWord->setDefaultFontName('Calibri');
			$this->objPHPWord->setDefaultFontSize(10);


			$this->objPHPWord->getProperties()
				->setCreator( $this->VersionLoxense )
				->setLastModifiedBy( $_SESSION['cvl_prenom'] . ' ' . $_SESSION['cvl_nom'] . ' (' . $_SESSION['ent_libelle'] . ')' )
				->setDescription( $GLOBALS['L_Titre_Principal'] )
				->setKeywords('Loxense')
				->setCompany('Loxense');

			$this->NomFichierWord = '';


			// =============================================
			// Définition des styles pour le document Word.
			$this->WordParagraphNormal = array(
				'align' => 'left',
				'spaceAfter' => 300
			);

			$this->objPHPWord->addFontStyle('TitreDocument', array('bold'=>false, 'size'=>22));
			$this->objPHPWord->addFontStyle('SujetDocument', array('bold'=>false, 'size'=>18));
			$this->objPHPWord->addFontStyle('VersionLoxense', array('bold'=>false, 'size'=>16));


			// Define the TOC font style
			$this->TOCFontStyle = array('spaceAfter'=>60, 'size'=>12);


			// Définit les Titres de niveau 1, 2 et 3
			$this->objPHPWord->addTitleStyle(1, array('size'=>16, 'color'=>'333333', 'bold'=>true, 'spaceBefore'=>$this->PixelToTwip(18)));
			$this->objPHPWord->addTitleStyle(2, array('size'=>14, 'color'=>'666666', 'bold'=>true, 'spaceBefore'=>$this->PixelToTwip(12)));
			$this->objPHPWord->addTitleStyle(3, array('size'=>12, 'color'=>'666666', 'bold'=>true, 'spaceBefore'=>$this->PixelToTwip(6)));


			// Bordure des tableaux
			$this->styleCellBorder = array('borderSize'=>6);
			$this->styleCellTopBorder = array('borderLeftSize'=>6, 'borderTopSize'=>6, 'borderRightSize'=>6);
			$this->styleCellMiddleBorder = array('borderLeftSize'=>6, 'borderRightSize'=>6);
			$this->styleCellBottomBorder = array('borderLeftSize'=>6, 'borderBottomSize'=>6, 'borderRightSize'=>6);

			// Titre sur fond mauve
			$this->styleCellTitleBorder = array('borderSize'=>6, 'borderBottomSize'=>18, 'bgColor'=>'DCAFDD', 'color'=>'FFFFFF');
			$this->styleCellTitleBorderLeft = array('valign'=>'center', 'borderLeftSize'=>6, 'borderTopSize'=>6, 'borderBottomSize'=>18, 'bgColor'=>'DCAFDD');
			$this->styleCellTitleBorderMiddle = array('valign'=>'center', 'borderTopSize'=>6, 'borderBottomSize'=>18, 'bgColor'=>'DCAFDD');
			$this->styleCellTitleBorderRight = array('valign'=>'center', 'borderTopSize'=>6, 'borderRightSize'=>6, 'borderBottomSize'=>18, 'bgColor'=>'DCAFDD');

			// Titre sur fond gris moyen
			$this->styleCellTitle2Border = array('valign'=>'center', 'borderSize'=>6, 'borderBottomSize'=>18, 'bgColor'=>'A0A0A0', 'color'=>'FFFFFF');
			$this->styleCellTitle2BorderLeft = array('valign'=>'center', 'borderLeftSize'=>6, 'borderTopSize'=>6, 'borderBottomSize'=>18, 'bgColor'=>'A0A0A0');
			$this->styleCellTitle2BorderMiddle = array('valign'=>'center', 'borderTopSize'=>6, 'borderBottomSize'=>18, 'bgColor'=>'A0A0A0');
			$this->styleCellTitle2BorderRight = array('valign'=>'center', 'borderTopSize'=>6, 'borderRightSize'=>6, 'borderBottomSize'=>18, 'bgColor'=>'A0A0A0');

			// Titre sur fond mauve clair
			$this->styleCellTitle3Border = array('borderSize'=>6, 'borderBottomSize'=>18, 'bgColor'=>'8F58A2');
			$this->styleCellTitle3BorderLeft = array('borderLeftSize'=>6, 'borderTopSize'=>6, 'borderBottomSize'=>18, 'bgColor'=>'8F58A2');
			$this->styleCellTitle3BorderMiddle = array('borderTopSize'=>6, 'borderBottomSize'=>18, 'bgColor'=>'8F58A2');
			$this->styleCellTitle3BorderRight = array('borderTopSize'=>6, 'borderRightSize'=>6, 'borderBottomSize'=>18, 'bgColor'=>'8F58A2');

			// Titre sur fond gris clair
			$this->styleCellTitle4Border = array('borderSize'=>6, 'bgColor'=>'E6E6E6', 'color'=>'FFFFFF');

			// Sous-titre sur fond gris moyen
			$this->styleCellSubtitleBorder = array('valign'=>'center', 'borderSize'=>6, 'bgColor'=>'A0A0A0', 'color'=>'FFFFFF');
			$this->styleCellSubtitleBorderLeft = array('valign'=>'center', 'borderLeftSize'=>6, 'borderBottomSize'=>6, 'borderTopSize'=>6, 'bgColor'=>'A0A0A0', 'color'=>'FFFFFF');
			$this->styleCellSubtitleBorderMiddle = array('valign'=>'center', 'borderBottomSize'=>6, 'borderTopSize'=>6, 'bgColor'=>'A0A0A0', 'color'=>'FFFFFF');
			$this->styleCellSubtitleBorderRight = array('valign'=>'center', 'borderRightSize'=>6, 'borderBottomSize'=>6, 'borderTopSize'=>6, 'bgColor'=>'A0A0A0', 'color'=>'FFFFFF');

			$this->styleTable = array('cellMargin'=>80);

			$this->styleTitleFont = array('bold'=>true, 'color'=>'FFFFFF');
			$this->styleTitle2Font = array('bold'=>true);

			break;

		 default:
		 case 'html':
			break;
		}

		return TRUE;
	}



	public function PixelToTwip( $Value ) {
		return ( $Value * 15 );
	}


	public function PixelToCentimeter( $Value ) {
		return ( $Value * 38 );
	}


	public function TwipToPixel( $Value ) {
		return ( $Value / 15 );
	}


	public function TwipToCentimeter( $Value ) {
		return ( $Value / 566 );
	}


	public function CentimeterToPixel( $Value ) {
		return ( $Value / 38 );
	}


	public function CentimeterToTwip( $Value ) {
		return ( $Value * 566 );
	}


	public function wlog( $record ) {
		// Fonction de DEBUG (uniquement)
		$PF_Log = fopen('traceur.log','a+');

		fwrite( $PF_Log, utf8_decode($record) . "\n" );

		fclose($PF_Log);
	}


	public function xlColumnValue( $strColumnIndex ) {
	/**
	* Change les numéro de Colonne en lettre et inversement.
	*
	* @author Pierre-Luc MARY
	* @date 2014-11-26
	*
	* @param[in] $strColumnIndex Valeur à convertir
	*
	* @return Renvoi le résultat de la conversion chiffre en lettre ou lettre en chiffre, sinon FAUX en cas d'erreur.
	*			
	*/
	    $strColumnIndex = strtoupper($strColumnIndex);

	    // Suppression des caractères "$" si présent
	    if (strpos($strColumnIndex,"\$") >= 0){
	    	$strColumnIndex = mb_ereg_replace("\\$", "", $strColumnIndex);
		}
	    
	    switch ( ord($strColumnIndex) ) {
	        case 48: // Le premier caractère ne doit pas être un 0
	            return FALSE;
	            break;

	        case ( ord($strColumnIndex) >= 49 and ord($strColumnIndex) <= 57 ): // Converti un chiffre en caractères
	            if ($strColumnIndex < 27){
	                $xlColumnValue = chr($strColumnIndex + 65 - 1);
				} else {
	                if ($strColumnIndex % 26 <> 0) {
	                    $xlColumnValue = chr($strColumnIndex / 26 + 65 - 1) . chr($strColumnIndex % 26 + 65 - 1);
					} else {
	                    $xlColumnValue = chr($strColumnIndex / 26 + 65 - 2) . chr(90);
					}
				}

				return $xlColumnValue;
	            break;

	        case ( ord($strColumnIndex) >= 65 and ord($strColumnIndex) <= 90): // Converti des caractères en chiffre
	            $xlColumnValue = ord($strColumnIndex) - 65 + 1;

	            if (strlen($strColumnIndex) > 1){
	                $xlColumnValue = ($xlColumnValue * 26) + (ord(substr($strColumnIndex, -1)) - 65 + 1);
				}

	            return $xlColumnValue;
	            break;

	        default:
	            return FALSE;
	            break;
		}
	}


	public function initialiseCartographie( $crs_id, $ActionsSeules = FALSE, $TypeEdition = '' ) {
	/**
	* Récupère les informations relatives à la Cartographie à imprimer.
	*
	* @author Pierre-Luc MARY
	* @date 2014-11-26
	*
	* @param[in] $crs_id ID de la Cartographie à imprimer
	*
	* @return Renvoi vrai si l'initialisation a réussi.
	*			
	*/
		include_once( DIR_LIBRAIRIES . '/Class_HBL_Securite.inc.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-Actions.php' );

		$objSecurite = new HBL_Securite();

		$sql = 'SELECT crs_libelle AS "cartographie", crs_periode AS "periode", ent_libelle AS "entite", crs_version AS "version" ' .
			'FROM crs_cartographies_risques AS "crs" ' .
			'LEFT JOIN ent_entites AS "ent" ON ent.ent_id = crs.ent_id ' .
			'WHERE crs.crs_id = :crs_id ';

		$requete = $this->prepareSQL( $sql );

		$resultat = $this->bindSQL($requete, ':crs_id',  $crs_id, self::ID_TYPE)
			->executeSQL($requete)
			->fetchObject();

		// Sauvegarde au niveau de l'objet les valeurs de la Cartographie.
		$this->NomCartographie = $resultat->cartographie;
		$this->PeriodeCartographie = $resultat->periode;
		$this->EntiteCartographie = $resultat->entite;
		$this->VersionCartographie = $resultat->version;

		$this->TitreDocument = $GLOBALS['L_Edition_Entite'] . $resultat->entite;
		$this->SujetDocument = $resultat->cartographie . " - " . $resultat->periode . " - v" . $resultat->version;


		// Sauvegarde les noms des fichiers Excel et Word relatifs à cette Cartographie.
		if ( $ActionsSeules == TRUE ) $Libelle = $L_Actions . ' - ';
		else $Libelle = '';

		$_NomFichier = $objSecurite->supprimerAccentuation( $this->EntiteCartographie ) . ' - ' . 
			$objSecurite->supprimerAccentuation( $this->NomCartographie ) . ' - ' . $Libelle . $objSecurite->supprimerAccentuation( $this->PeriodeCartographie )
			 . ' - ' . $objSecurite->supprimerAccentuation( $this->VersionCartographie );

		if ( $TypeEdition != '' ) $_NomFichier .= ' - ' . $TypeEdition;


		switch ( $this->FormatEdition ) {
		 case 'excel':
			// Complète les Propriétés du Document Excel
			$this->objPHPExcel->getProperties()
				->setTitle( $this->TitreDocument )
				->setSubject( $this->SujetDocument )
				->setCategory($resultat->periode);

			$this->NomFichierExcel = $_NomFichier . '.xlsx';
			$this->NomCompletFichierExcel = DIR_EDITIONS . '/' . $this->NomFichierExcel;

			break;

		 case 'word':
			// Complète les Propriétés du Document Word
			$this->objPHPWord->getProperties()
				->setTitle( $this->TitreDocument )
				->setSubject( $this->SujetDocument )
				->setCategory( $resultat->periode );

			$this->NomFichierWord = $_NomFichier . '.docx';
			$this->NomCompletFichierWord = DIR_EDITIONS . '/' . $this->NomFichierWord;

			break;

		 default:
		 case 'html':
			break;
		}

		return TRUE;
	}


	public function sauverFichierExcel() {
	/**
	* Sauvegarde l'objet Excel généré par l'objet dans un fichier.
	*
	* @author Pierre-Luc MARY
	* @date 2014-11-26
	*
	* @return Renvoi vrai si le fichier a bien été créé.
	*			
	*/
		$objWriterExcel = PHPExcel_IOFactory::createWriter( $this->objPHPExcel, 'Excel2007' );
		$objWriterExcel->save( $this->NomCompletFichierExcel );

		return TRUE;
	}


	public function genererFichierWord( $crs_id, $Flag_Chapitres ) {
	/**
	* Génère les différents chapitres du fichier Word.
	*
	* @author Pierre-Luc MARY
	* @date 2017-08-30
	*
	* @param[in] $crs_id ID. de la Cartographie à traiter.
	* @param[in] $Flag_Chapitres Indicateurs sur les chapitres à imprimer.
	*
	* @return Renvoi vrai si les chapitres ont été créés.
	*
	*/
		ini_set('memory_limit', '1024M');

		if ( file_exists( DIR_LIBRAIRIES . '/formatRapportLoxense.xml' ) ) {
		    $XML = simplexml_load_file( DIR_LIBRAIRIES . '/formatRapportLoxense.xml' );
		    $Niveau = 0;

			$this->rechercherChapitre( $crs_id, $XML, $Niveau, $Flag_Chapitres );

		} else {
		    exit('Internal error: "formatRapportLoxense.xml" file not found.');
		}

	}


	protected function rechercherChapitre( $crs_id, $Flux, $Niveau, $Flag_Chapitres ) {
		$Niveau += 1;

		foreach ( $Flux as $Noeud ) {
			$Nom = $Noeud['nom'];
			$Orientation = $Noeud['orientation'];
			$Limitation = $Noeud['limitation'];
			$Organisation = $Noeud['organisation'];

			if ( $Orientation == '' ) $Orientation = 'portrait';

			$FonctionWord = 'Word_'.$Nom;
			$this->$FonctionWord( $crs_id, $Niveau, $Orientation, $Limitation, $Organisation, $Flag_Chapitres );

			if ( array_key_exists('Chapitre', $Noeud) ) { // Premier niveau de chapitre
				$this->rechercherChapitre( $crs_id, $Noeud, $Niveau, $Flag_Chapitres );
			}
		}

	}


	public function sauverFichierWord() {
	/**
	* Sauvegarde l'objet Word généré par l'objet dans un fichier.
	*
	* @author Pierre-Luc MARY
	* @date 2014-11-26
	*
	* @return Renvoi vrai si le fichier a bien été créé.
	*			
	*/
		$objWriterWord = PHPWord_IOFactory::createWriter($this->objPHPWord, 'Word2007');
		$objWriterWord->save( $this->NomCompletFichierWord );


		return TRUE;
	}


	public function imprimerFichierExcel( $crs_id, $ActionsSeules = FALSE, $SupprimeEdition = FALSE ) {
	/**
	* Imprime le fichier Excel qui a été généré.
	*
	* @author Pierre-Luc MARY
	* @date 2014-11-26
	*
	* @return Renvoi vrai si le fichier a bien été créé.
	*			
	*/
		// Récupère les informations utiles pour récupérer le nom du fichier
		$this->initialiseCartographie( $crs_id, $ActionsSeules );

		if ( file_exists( $this->NomCompletFichierExcel ) ) {
			header( 'Content-Description: File Transfer' );
			header( 'Content-Type: application/octet-stream' );
			header( 'Content-Disposition: attachment; filename="' . $this->NomFichierExcel . '"' );
			header( 'Content-Transfer-Encoding: binary' );
			header( 'Expires: 0' );
			header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0, public' );
			header( 'Pragma: no-cache' );
			header( 'Content-Length: ' . filesize( $this->NomCompletFichierExcel ) );
			ob_clean();
			flush();
			readfile( $this->NomCompletFichierExcel );

			if ( $SupprimeEdition == TRUE ) {
				unlink( $this->NomCompletFichierExcel );
			}
		} else {
			//header( 'Location: Loxense-EditionsRisques.php?Action=AJAX_Pas_De_Fichier&crs_id='.$crs_id.'&Type=excel' );
			throw new Exception("no_file", 404);
		}

		exit( 1 );
	}


	public function imprimerFichierWord( $crs_id, $ActionsSeules = FALSE, $SupprimeEdition = FALSE ) {
	/**
	* Imprime le fichier Word qui a été généré.
	*
	* @author Pierre-Luc MARY
	* @date 2014-11-26
	*
	* @return Renvoi vrai si le fichier a bien été créé.
	*			
	*/
		// Récupère les informations utiles pour récupérer le nom du fichier
		$this->initialiseCartographie( $crs_id, $ActionsSeules );

		if ( file_exists( $this->NomCompletFichierWord ) ) {
			header( 'Content-Description: File Transfer' );
			header( 'Content-Type: application/octet-stream' );
			header( 'Content-Disposition: attachment; filename="' . $this->NomFichierWord . '"' );
			header( 'Content-Transfer-Encoding: binary' );
			header( 'Expires: 0' );
			header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
			header( 'Pragma: public' );
			header( 'Content-Length: ' . filesize( $this->NomCompletFichierWord ) );
			ob_clean();
			flush();
			readfile( $this->NomCompletFichierWord );

			if ( $SupprimeEdition == TRUE ) {
				unlink( $this->NomCompletFichierWord );
			}
		} else {
			header( 'Location: Loxense-GestionEdition.php?PasDeFichier&crs_id='.$crs_id.'&Type=word' );
		}

		exit( 1 );
	}


	public function initialiseHautBasOnglet( $Titre_Onglet ){
	/**
	* Initialise les informations à placer en haut et bas de l'onglet courant.
	*
	* @author Pierre-Luc MARY
	* @date 2014-11-26
	*
	* @param[in] $Titre_Onglet Titre à appliquer à l'onglet courant.
	*
	* @return Renvoi vrai si les informations ont bien été mises à jour.
	*			
	*/
		// Met en place les informations en haut de l'onglet courant.
		$this->objPHPExcel->getActiveSheet()->getHeaderFooter()
			->setOddHeader( '&L&B' . $Titre_Onglet . '&R' . $GLOBALS['L_Imprime_Le'] . ' &D');

		// Met en place les informations en bas de l'onglet courant.
		$this->objPHPExcel->getActiveSheet()->getHeaderFooter()
			->setOddFooter('&L&B' . $this->objPHPExcel->getProperties()->getSubject() . '&RPage &P ' . $GLOBALS['L_Sur'] . ' &N');

		// Centre le résultat de l'édition dans la page à imprimer
		$this->objPHPExcel->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
		$this->objPHPExcel->getActiveSheet()->getPageSetup()->setVerticalCentered(true);

		return TRUE;
	}


	/* ================================================================================== */
	
	public function editionPrimordiauxSupports( $crs_id ) {
	/**
	* Edite les Actifs Primordiaux ainsi que leur répartition sur les Actifs Supports.
	*
	* @author Pierre-Luc MARY
	* @date 2014-11-26
	*
	* @param[in] $crs_id ID de la Cartographie à utiliser
	*
	* @return Renvoi vrai si les informations ont bien été éditées.
	*			
	*/
		$Titre_Onglet = $GLOBALS['L_Primordiaux_Supports'];

		// Active le 1er onglet et lui donne un nom.
		$this->objPHPExcel->setActiveSheetIndex(0)->setTitle( $Titre_Onglet );

		// Initialise le haut et le bas de page de l'onglet courant.
		$this->initialiseHautBasOnglet( $Titre_Onglet );

		// Mise en page de l'onglet.
		$this->objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$this->objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

		// Répète sur chaque page les lignes 1 à 3.
		$this->objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 3);


		// ==============================================
		// Construit la ligne des Actifs Supports.
		$requete = $this->prepareSQL(
			'SELECT ' .
			'rlb.lbr_libelle AS "spp_type", ' .
			'spp_code, ' .
			'spp_nom ' .
			'FROM spp_supports AS "spp" ' .
		    'LEFT JOIN spcr_spp_crs AS "spcr" ON spcr.spp_id = spp.spp_id ' .
			'LEFT JOIN tsp_types_support AS "tsp" ON tsp.tsp_id = spp.tsp_id ' .
			'LEFT JOIN lbr_libelles_referentiel AS "rlb" ON rlb.lbr_code = tsp_code AND rlb.lng_id = :langue ' .
		    'WHERE spcr.crs_id = :crs_id ' .
			'ORDER BY spp_type, spp_nom '
		);
			
		$Liste_Actifs_Supports = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->bindSQL($requete,  ':langue', $this->Langue, self::LANGUE_TYPE, self::LANGUE_LENGTH)
			->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);

		if ( $Liste_Actifs_Supports == [] ) {
			$this->objPHPExcel->getActiveSheet()->setCellValue('A1', $GLOBALS['L_Actifs_Supports']);
			$this->objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray( $this->styleTitrePrincipalHorizontal );
			$this->objPHPExcel->getActiveSheet()->setCellValue('A2', $GLOBALS['L_Neither']);

			return TRUE;
		}


		$Colonne = $this->xlColumnValue( $this->_Colonne_Type_Actifs_Primordiaux ) + 1; // Numéro de la colonne courante
		$Derniere_Colonne = 0; // Dernière colonne de la Matrice
		$Type_Support = ''; // Type de l'Actif Primordial courant
		$_Type_Support = ''; // Ancien type de l'Actif Primordial
		$Debut_Colonne_Type = 0; // Numéro de colonne matérialisant le début d'un type
		$Fin_Colonne_Type = 0; // Numéro de colonne matérialisant la fin d'un type

		foreach ($Liste_Actifs_Supports as $Occurrence) {
			$Type_Support = $Occurrence->spp_type;

			if ( $_Type_Support != $Type_Support ) {
				if ( $Debut_Colonne_Type == 0 and $Fin_Colonne_Type == 0 ) { // Première initialisation
					$Debut_Colonne_Type = $Colonne;
				} else {
					if ( $Debut_Colonne_Type < $Fin_Colonne_Type ) {
						$DebutCellule = $this->xlColumnValue( $Debut_Colonne_Type ) . $this->_Ligne_Type_Actifs_Supports;
						$FinCellule = $this->xlColumnValue( $Fin_Colonne_Type ) . $this->_Ligne_Type_Actifs_Supports;

						$this->objPHPExcel->getActiveSheet()->mergeCells($DebutCellule . ':' . $FinCellule);
					}

					$Debut_Colonne_Type = $Colonne;
				}

				$ColonneCourante = $this->xlColumnValue( $Colonne );
				$CelluleCourante = $ColonneCourante . $this->_Ligne_Type_Actifs_Supports;

				$this->objPHPExcel->getActiveSheet()->setCellValue($CelluleCourante, $Type_Support);

				$this->objPHPExcel->getActiveSheet()->getStyle($CelluleCourante)->applyFromArray( $this->styleTitreHorizontal );

				$_Type_Support = $Type_Support;

				$CelluleCourante = $ColonneCourante . $this->_Ligne_Nom_Actifs_Supports;

				$this->objPHPExcel->getActiveSheet()->setCellValue($CelluleCourante, $Occurrence->spp_nom);

				$this->objPHPExcel->getActiveSheet()->getStyle($CelluleCourante)->applyFromArray( $this->styleTitreNomVertical );
			} else {
				$ColonneCourante = $this->xlColumnValue( $Colonne );
				$CelluleCourante = $ColonneCourante . $this->_Ligne_Nom_Actifs_Supports;

				$this->objPHPExcel->getActiveSheet()->setCellValue($CelluleCourante, $Occurrence->spp_nom);

				$this->objPHPExcel->getActiveSheet()->getStyle($CelluleCourante)->applyFromArray( $this->styleTitreNomVertical );
			}

			$Indice_Actifs_Supports[$Occurrence->spp_code] = $ColonneCourante;

			$Fin_Colonne_Type = $Colonne;

			$Colonne += 1;
			$Derniere_Colonne = $Colonne;
		}

		if ( $Debut_Colonne_Type < $Fin_Colonne_Type ) {
			$DebutCellule = $this->xlColumnValue( $Debut_Colonne_Type ) . $this->_Ligne_Type_Actifs_Supports;
			$FinCellule = $this->xlColumnValue( $Fin_Colonne_Type ) . $this->_Ligne_Type_Actifs_Supports;

			$this->objPHPExcel->getActiveSheet()->mergeCells($DebutCellule . ':' . $FinCellule);
		}

		$Derniere_Colonne -= 1; // Rattrappe le décalage.

		// Ajoute un titre principal pour annoncer les Actifs Primordiaux.
		$ColonneSuivante = $this->xlColumnValue( $this->xlColumnValue( $this->_Colonne_Type_Actifs_Primordiaux ) + 1 );
		$CelluleCourante = $ColonneSuivante . $this->_Ligne_Titre_Actifs_Supports;
		$FinCellule = $this->xlColumnValue( $Derniere_Colonne ) . $this->_Ligne_Titre_Actifs_Supports;

		$this->objPHPExcel->getActiveSheet()->setCellValue($CelluleCourante, $GLOBALS['L_Actifs_Supports']);

		if ( $Derniere_Colonne > 0 ) {
			$this->objPHPExcel->getActiveSheet()->mergeCells( $CelluleCourante . ':' . $FinCellule );
			$this->objPHPExcel->getActiveSheet()->getStyle( $CelluleCourante . ':' . $FinCellule )->applyFromArray( $this->styleTitrePrincipalHorizontal );
		} else {
			$this->objPHPExcel->getActiveSheet()->getStyle( $CelluleCourante )->applyFromArray( $this->styleTitrePrincipalHorizontal );
		}



		// ==============================================
		// Construit la colonne des Actifs Primordiaux.
		$requete = $this->prepareSQL(
			'SELECT ' .
			'rlb.lbr_libelle AS "apr_type", ' .
			'apr_code, ' .
			'apr_nom ' .
			'FROM apr_actifs_primordiaux AS "apr" ' .
			'LEFT JOIN lbr_libelles_referentiel AS "rlb" ON rlb.lbr_code = \'APR_TYPE_\'||apr_type_code AND rlb.lng_id = :langue ' .
			'WHERE apr.crs_id = :crs_id ' .
			'ORDER BY apr_type DESC, apr_nom '
		);
			
		$Liste_Actifs_Primordiaux = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->bindSQL($requete, ':langue', $this->Langue, self::LANGUE_TYPE, self::LANGUE_LENGTH)
			->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);


		$Ligne = $this->_Ligne_Nom_Actifs_Supports + 1; // Numéro de la ligne courante
		$Derniere_Ligne = 0; // Dernière ligne de la Matrice
		$Type_Primordial = ''; // Type de l'Actif Primordial courant
		$_Type_Primordial = ''; // Ancien type de l'Actif Primordial

		foreach ($Liste_Actifs_Primordiaux as $Occurrence) {
			$Type_Primordial = $Occurrence->apr_type;

			if ( $_Type_Primordial != $Type_Primordial ) {
				$this->objPHPExcel->getActiveSheet()->setCellValue($this->_Colonne_Type_Actifs_Primordiaux.$Ligne, $Type_Primordial);

				$this->objPHPExcel->getActiveSheet()->getStyle($this->_Colonne_Type_Actifs_Primordiaux.$Ligne)
					->applyFromArray( $this->styleTitreVertical );

				// Etant le titre pour marquer la séparation
				$DebutCellule = $this->_Colonne_Type_Actifs_Primordiaux . $Ligne;
				$FinCellule = $this->xlColumnValue( $Derniere_Colonne ) . $Ligne;

				$this->objPHPExcel->getActiveSheet()->mergeCells($DebutCellule . ':' . $FinCellule);


				$_Type_Primordial = $Type_Primordial;

				$Ligne += 1;

				$this->objPHPExcel->getActiveSheet()->setCellValue($this->_Colonne_Type_Actifs_Primordiaux.$Ligne, $Occurrence->apr_nom);

				$this->objPHPExcel->getActiveSheet()->getStyle($this->_Colonne_Type_Actifs_Primordiaux.$Ligne)
					->applyFromArray( $this->styleTitreNomHorizontal );

				$Indice_Actifs_Primordiaux[$Occurrence->apr_code] = $Ligne;
			} else {
				$this->objPHPExcel->getActiveSheet()->setCellValue($this->_Colonne_Type_Actifs_Primordiaux.$Ligne, $Occurrence->apr_nom);

				$this->objPHPExcel->getActiveSheet()->getStyle($this->_Colonne_Type_Actifs_Primordiaux.$Ligne)
					->applyFromArray( $this->styleTitreNomHorizontal );

				$Indice_Actifs_Primordiaux[$Occurrence->apr_code] = $Ligne;
			}

			$Ligne += 1;

			$Derniere_Ligne = $Ligne;
		}

		$Derniere_Ligne -= 1; // Rattrape le décalage.

		// Ajuste la taille de la colonne des Actifs Primordiaux.
		$this->objPHPExcel->getActiveSheet()->getColumnDimension($this->_Colonne_Type_Actifs_Primordiaux)->setAutoSize( TRUE );

		// Ajoute un titre principal pour annoncer les Actifs Primordiaux.
		// Met en place un titre principal pour annoncer les Actifs Primordiaux.
		$CelluleCourante = $this->_Colonne_Titre_Actifs_Primordiaux . ($this->_Ligne_Nom_Actifs_Supports + 1);
		$FinCellule = $this->_Colonne_Titre_Actifs_Primordiaux . $Derniere_Ligne;
		
		$this->objPHPExcel->getActiveSheet()->setCellValue($CelluleCourante, $GLOBALS['L_Actifs_Primordiaux']);
		if ( $Derniere_Ligne > 0 ) {
			$this->objPHPExcel->getActiveSheet()->mergeCells($CelluleCourante . ':' . $FinCellule);
			$this->objPHPExcel->getActiveSheet()->getStyle($CelluleCourante . ':' . $FinCellule)
				->applyFromArray( $this->styleTitrePrincipalVertical )
				->getAlignment()->setWrapText(TRUE);
		} else {
			$this->objPHPExcel->getActiveSheet()->getStyle($CelluleCourante)
				->applyFromArray( $this->styleTitrePrincipalVertical )
				->getAlignment()->setWrapText(TRUE);
		}

		// Ajuste la taille de la colonne des Actifs Primordiaux.
		$this->objPHPExcel->getActiveSheet()->getColumnDimension($this->_Colonne_Titre_Actifs_Primordiaux)->setAutoSize( TRUE );


		// ===============================================================
		// Met une bordure autour des libelles et du corps de la Matrice.
		// Bordure autour des libellés des Actifs Supports.
		if ( $Derniere_Colonne > 0 ) {
			$DerniereCellule = $this->xlColumnValue( $Derniere_Colonne );
			$DebutCellule = $this->xlColumnValue( $this->xlColumnValue( $this->_Colonne_Type_Actifs_Primordiaux ) + 1 );

			$this->objPHPExcel->getActiveSheet()
				->getStyle($DebutCellule . $this->_Ligne_Type_Actifs_Supports . ':' . $DerniereCellule . $this->_Ligne_Nom_Actifs_Supports)
				->applyFromArray($this->styleBordureNoirAutourGrisInterieur);
		}

		// Bordure autour des libellés des Actifs Primordiaux.
		if ( $Derniere_Ligne > 0 ) {
			$this->objPHPExcel->getActiveSheet()
				->getStyle($this->_Colonne_Type_Actifs_Primordiaux . ($this->_Ligne_Nom_Actifs_Supports + 1) . ':' .
					$this->_Colonne_Type_Actifs_Primordiaux . $Derniere_Ligne)
				->applyFromArray($this->styleBordureNoirAutourGrisInterieur);
		}

		// Bordure autour du cors de la Matrice.
		if ( $Derniere_Colonne > 0 and $Derniere_Ligne > 0 ) {
			$this->objPHPExcel->getActiveSheet()
				->getStyle($DebutCellule . ($this->_Ligne_Nom_Actifs_Supports + 1) . ':' .
					$DerniereCellule.$Derniere_Ligne)
				->applyFromArray($this->styleBordureNoirAutourGrisInterieur);
		}
/*print('Colonne: '.$Derniere_Colonne.' - '.'Ligne: '.$Derniere_Ligne.'<hr>');
print_r($Indice_Actifs_Supports);
print_r($Indice_Actifs_Primordiaux);
print('<hr>');*/


		// =============================================================================
		// Recherche les relations entre les Actifs Primordiaux et les Actifs Supports.
		$requete = $this->prepareSQL(
			'SELECT ' .
			'apr_code, ' .
			'spp_code ' .
			'FROM apsp_apr_spp AS "apsp" ' .
			'LEFT JOIN apr_actifs_primordiaux AS "apr" ON apr.apr_id = apsp.apr_id ' .
			'LEFT JOIN spp_supports AS "spp" ON spp.spp_id = apsp.spp_id ' .
			'LEFT JOIN spcr_spp_crs AS "spcr" ON spcr.spp_id = spp.spp_id ' .
			'WHERE apr.crs_id = :crs_id AND spcr.crs_id = :crs_id '
		);
			
		$Resultat_Jointures = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);


		// Parcours les Actifs et met à jour l'Edition
		foreach( $Resultat_Jointures as $Occurrence ) {
			$CelluleCourante = $Indice_Actifs_Supports[$Occurrence->spp_code] . $Indice_Actifs_Primordiaux[$Occurrence->apr_code];

			$this->objPHPExcel->getActiveSheet()->setCellValue($CelluleCourante, 'X');
			$this->objPHPExcel->getActiveSheet()->getStyle($CelluleCourante)->applyFromArray($this->styleTextToutCentre);
		}

		return TRUE;
	}


	/* ================================================================================== */

	public function editionClassificationActifsPrimordiaux( $crs_id ) {
	/**
	* Edite la classification des Actifs Primordiaux.
	*
	* @author Pierre-Luc MARY
	* @date 2014-11-28
	*
	* @param[in] $crs_id ID de la Cartographie à utiliser
	*
	* @return Renvoi vrai si les informations ont bien été éditées.
	*			
	*/
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-ActifsPrimordiaux.php' );

		$Titre_Onglet = $L_Valorisation;

		// Création d'un nouvel onglet et lui donne un nom.
		$this->objPHPExcel->createSheet()->setTitle( $Titre_Onglet );

		// Mise en page de l'onglet.
		$this->objPHPExcel->setActiveSheetIndexByName( $Titre_Onglet )->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
		$this->objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

		// Répète sur chaque page les lignes 1 à 2.
		$this->objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 2);

		// Initialise le haut et le bas de page de l'onglet courant.
		$this->initialiseHautBasOnglet( $Titre_Onglet );


		// ==============================================
		// Construit les colonnes des Classifications.
		$requete = $this->prepareSQL(
			'SELECT ' .
			'cva.cva_code, cva.cva_nom ' .
			'FROM cva_criteres_valorisation_actifs AS "cva"  '
		);
			
		$Liste_Criteres_Valorisation = $this->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);


		$Colonne_Titre = 3;
		$Ligne_Titre = 1;

		$Ligne_Courante = $Ligne_Titre;
		$Derniere_Colonne = $Colonne_Titre; // Dernière colonne de la Matrice (mais également colonne de départ au colonne)

		// Ajoute un titre pour annoncer les Critères de Valorisation.
		$CelluleTitre = $this->xlColumnValue( $Colonne_Titre ) . $Ligne_Titre;

		$this->objPHPExcel->getActiveSheet()->setCellValue($CelluleTitre, $L_Criteres_Valorisation);

		$Ligne_Courante += 1; // Passe à la ligne suivante.


		foreach ($Liste_Criteres_Valorisation as $Occurrence) {
			$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );
			$CelluleCourante = $ColonneCourante . $Ligne_Courante;

			$this->objPHPExcel->getActiveSheet()->setCellValue( $CelluleCourante, $Occurrence->cva_nom );

			$this->objPHPExcel->getActiveSheet()->getStyle( $CelluleCourante )->applyFromArray( $this->styleTitreNomVertical );

			$Indice_Criteres_Valorisation[ $Occurrence->cva_code ] = $ColonneCourante;

			$Derniere_Colonne += 1;
		}

		$Derniere_Colonne -= 1; // Rattrappe le décalage.


		// Ajuste le titre principal des Critères de Valorisation par rapport au nombre de Critères remontés.
		if ( $Derniere_Colonne > 2 ) {
			$FinCellule = $this->xlColumnValue( $Derniere_Colonne ) . $Ligne_Titre;

			$this->objPHPExcel->getActiveSheet()->mergeCells( $CelluleTitre . ':' . $FinCellule );
			$this->objPHPExcel->getActiveSheet()->getStyle( $CelluleTitre . ':' . $FinCellule )->applyFromArray( $this->styleTitrePrincipalHorizontal );
		} else {
			$this->objPHPExcel->getActiveSheet()->getStyle( $CelluleTitre )->applyFromArray( $this->styleTitrePrincipalHorizontal );
		}


		// ==============================================
		// Construit la colonne des Actifs Primordiaux.
		$requete = $this->prepareSQL(
			'SELECT ' .
			'rlb.lbr_libelle AS "apr_type", ' .
			'apr_code, ' .
			'apr_nom ' .
			'FROM apr_actifs_primordiaux AS "apr" ' .
			'LEFT JOIN lbr_libelles_referentiel AS "rlb" ON rlb.lbr_code = \'APR_TYPE_\'||apr_type_code AND rlb.lng_id = :langue ' .
			'WHERE apr.crs_id = :crs_id ' .
			'ORDER BY apr_type DESC, apr_nom '
		);
			
		$Liste_Actifs_Primordiaux = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->bindSQL($requete, ':langue', $this->Langue, self::LANGUE_TYPE, self::LANGUE_LENGTH)
			->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);


		$Ligne = $this->_Ligne_Nom_Actifs_Supports; // Numéro de la ligne courante
		$Derniere_Ligne = 0; // Dernière ligne de la Matrice
		$Type_Primordial = ''; // Type de l'Actif Primordial courant
		$_Type_Primordial = ''; // Ancien type de l'Actif Primordial

		foreach ($Liste_Actifs_Primordiaux as $Occurrence) {
			$Type_Primordial = $Occurrence->apr_type;

			if ( $_Type_Primordial != $Type_Primordial ) {
				$this->objPHPExcel->getActiveSheet()->setCellValue($this->_Colonne_Type_Actifs_Primordiaux.$Ligne, $Type_Primordial);

				$this->objPHPExcel->getActiveSheet()->getStyle($this->_Colonne_Type_Actifs_Primordiaux.$Ligne)
					->applyFromArray( $this->styleTitreVertical );

				// Etant le titre pour marquer la séparation
				$DebutCellule = $this->_Colonne_Type_Actifs_Primordiaux . $Ligne;
				$FinCellule = $this->xlColumnValue( $Derniere_Colonne ) . $Ligne;

				$this->objPHPExcel->getActiveSheet()->mergeCells($DebutCellule . ':' . $FinCellule);


				$_Type_Primordial = $Type_Primordial;

				$Ligne += 1;

				$this->objPHPExcel->getActiveSheet()->setCellValue($this->_Colonne_Type_Actifs_Primordiaux.$Ligne, $Occurrence->apr_nom);

				$this->objPHPExcel->getActiveSheet()->getStyle($this->_Colonne_Type_Actifs_Primordiaux.$Ligne)
					->applyFromArray( $this->styleTitreNomHorizontal );

				$Indice_Actifs_Primordiaux[$Occurrence->apr_code] = $Ligne;
			} else {
				$this->objPHPExcel->getActiveSheet()->setCellValue($this->_Colonne_Type_Actifs_Primordiaux.$Ligne, $Occurrence->apr_nom);

				$this->objPHPExcel->getActiveSheet()->getStyle($this->_Colonne_Type_Actifs_Primordiaux.$Ligne)
					->applyFromArray( $this->styleTitreNomHorizontal );

				$Indice_Actifs_Primordiaux[$Occurrence->apr_code] = $Ligne;
			}

			$Ligne += 1;

			$Derniere_Ligne = $Ligne;
		}

		$Derniere_Ligne -= 1; // Rattrape le décalage.

		// Ajuste la taille de la colonne des Actifs Primordiaux.
		$this->objPHPExcel->getActiveSheet()->getColumnDimension($this->_Colonne_Type_Actifs_Primordiaux)->setAutoSize( TRUE );

		// Met en place un titre principal pour annoncer les Actifs Primordiaux.
		$CelluleCourante = $this->_Colonne_Titre_Actifs_Primordiaux . ($this->_Ligne_Nom_Actifs_Supports);
		$FinCellule = $this->_Colonne_Titre_Actifs_Primordiaux . $Derniere_Ligne;
		
		$this->objPHPExcel->getActiveSheet()->setCellValue($CelluleCourante, $GLOBALS['L_Actifs_Primordiaux']);
		if ( $Derniere_Ligne > 0 ) {
			$this->objPHPExcel->getActiveSheet()->mergeCells($CelluleCourante . ':' . $FinCellule);
			$this->objPHPExcel->getActiveSheet()->getStyle($CelluleCourante . ':' . $FinCellule)->applyFromArray( $this->styleTitrePrincipalVertical );
		} else {
			$this->objPHPExcel->getActiveSheet()->getStyle($CelluleCourante)->applyFromArray( $this->styleTitrePrincipalVertical );
		}

		// Ajuste la taille de la colonne des Actifs Primordiaux.
		$this->objPHPExcel->getActiveSheet()->getColumnDimension($this->_Colonne_Titre_Actifs_Primordiaux)->setAutoSize( TRUE );


		// ===============================================================
		// Met une bordure autour des libelles et du corps de la Matrice.
		// Bordure autour des libellés des Actifs Supports.
		if ( $Derniere_Colonne > 0 ) {
			$DerniereCellule = $this->xlColumnValue( $Derniere_Colonne );
			$DebutCellule = $this->xlColumnValue( $this->xlColumnValue( $this->_Colonne_Type_Actifs_Primordiaux ) + 1 );

			$this->objPHPExcel->getActiveSheet()
				->getStyle($DebutCellule . $this->_Ligne_Type_Actifs_Supports . ':' . $DerniereCellule . $this->_Ligne_Nom_Actifs_Supports)
				->applyFromArray($this->styleBordureNoirAutourGrisInterieur);
		}

		// Bordure autour des libellés des Actifs Primordiaux.
		if ( $Derniere_Ligne > 0 ) {
			$this->objPHPExcel->getActiveSheet()
				->getStyle($this->_Colonne_Type_Actifs_Primordiaux . ($this->_Ligne_Nom_Actifs_Supports) . ':' .
					$this->_Colonne_Type_Actifs_Primordiaux . $Derniere_Ligne)
				->applyFromArray($this->styleBordureNoirAutourGrisInterieur);
		}

		// Bordure autour du cors de la Matrice.
		if ( $Derniere_Colonne > 0 and $Derniere_Ligne > 0 ) {
			$this->objPHPExcel->getActiveSheet()
				->getStyle($DebutCellule . ($this->_Ligne_Nom_Actifs_Supports) . ':' .
					$DerniereCellule.$Derniere_Ligne)
				->applyFromArray($this->styleBordureNoirAutourGrisInterieur);
		}

		// =============================================================================
		// Recherche les relations entre les Actifs Primordiaux et les Actifs Supports.
		$requete = $this->prepareSQL(
			'SELECT ' .
			'cva.cva_code, apr.apr_code, pea.pea_cotation ' .
			'FROM apr_actifs_primordiaux AS "apr" ' .
			'RIGHT JOIN vac_valorisation_actifs AS "vac" ON vac.apr_id = apr.apr_id ' .
			'LEFT JOIN pea_poids_evaluation_actifs AS "pea" ON pea.pea_id = vac.pea_id ' .
			'LEFT JOIN cva_criteres_valorisation_actifs AS "cva" ON cva.cva_id = pea.cva_id ' .
			'WHERE apr.crs_id = :crs_id '
		);
			
		$Resultat_Jointures = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);


		// Parcours les Actifs et met à jour l'Edition
		foreach( $Resultat_Jointures as $Occurrence ) {
			$CelluleCourante = $Indice_Criteres_Valorisation[$Occurrence->cva_code] . $Indice_Actifs_Primordiaux[$Occurrence->apr_code];

			$this->objPHPExcel->getActiveSheet()->setCellValue($CelluleCourante, $Occurrence->pea_cotation);
			$this->objPHPExcel->getActiveSheet()->getStyle($CelluleCourante)->applyFromArray($this->styleTextToutCentre);
		}

		return TRUE;
	}



	public function editionActifsSupports( $crs_id ) {
	/**
	* Edite les Actifs Supports.
	*
	* @author Pierre-Luc MARY
	* @date 2017-11-17
	*
	* @param[in] $crs_id ID de la Cartographie à utiliser
	*
	* @return Renvoi vrai si les informations ont bien été éditées.
	*			
	*/
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_libelles_generiques.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-ActifsSupports.php' );

		$Titre_Onglet = $L_Actifs_Supports;

		// Création d'un nouvel onglet et lui donne un nom.
		$this->objPHPExcel->createSheet()->setTitle( $Titre_Onglet );

		// Mise en page de l'onglet.
		$this->objPHPExcel->setActiveSheetIndexByName( $Titre_Onglet )->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
		$this->objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

		// Répète sur chaque page les lignes 1 à 2.
		$this->objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 1);

		// Initialise le haut et le bas de page de l'onglet courant.
		$this->initialiseHautBasOnglet( $Titre_Onglet );


		// ==============================================
		// Construit les colonnes des Classifications.
		$requete = $this->prepareSQL(
			'SELECT lbr.lbr_libelle AS "spp_type", spp_code, spp_nom,
COUNT(DISTINCT apsp.apr_id) AS "nbr_apr", ROUND(AVG(pea.pea_poids)::numeric,2) AS "moyenne_poids_apr"
FROM spp_supports AS "spp"
LEFT JOIN tsp_types_support AS "tsp" ON tsp.tsp_id = spp.tsp_id
LEFT JOIN lbr_libelles_referentiel AS "lbr" ON lbr.lbr_code = tsp_code AND lbr.lng_id = :langue
LEFT JOIN apsp_apr_spp AS "apsp" ON apsp.spp_id = spp.spp_id
LEFT JOIN vac_valorisation_actifs AS "vac" ON vac.apr_id = apsp.apr_id
LEFT JOIN pea_poids_evaluation_actifs AS "pea" ON pea.pea_id = vac.pea_id
LEFT JOIN spcr_spp_crs AS "spcr" ON spcr.spp_id = spp.spp_id 
WHERE spcr.crs_id = :crs_id
GROUP BY lbr.lbr_libelle, spp_code, spp_nom
ORDER BY spp_type, spp_nom '
		);
			
		$Liste_Actifs_Supports = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->bindSQL($requete, ':langue', $_SESSION['Language'], self::LANGUE_TYPE, self::LANGUE_LENGTH)
			->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);
//var_dump($Liste_Actifs_Supports);print('<hr>');

		$Ligne = 1; // Numéro de la ligne courante

		$Type_Support = ''; // Type de l'Actif Support courant
		$_Type_Support = ''; // Ancien type de l'Actif Support

		foreach ($Liste_Actifs_Supports as $Occurrence) {
			$Type_Support = $Occurrence->spp_type;

			if ( $_Type_Support != $Type_Support ) {
				$__statut = $this->objPHPExcel->getActiveSheet()->setCellValue('A'.$Ligne, $Type_Support);

				$__statut = $this->objPHPExcel->getActiveSheet()->getStyle('A'.$Ligne)->applyFromArray($this->styleTitrePrincipalHorizontal);
				$__statut = $this->objPHPExcel->getActiveSheet()->mergeCells('A'.$Ligne.':C'.$Ligne);
				
				$_Type_Support = $Type_Support;

				$Ligne += 1;

				$__statut = $this->objPHPExcel->getActiveSheet()->setCellValue('A'.$Ligne, $L_Libelle);
				$__statut = $this->objPHPExcel->getActiveSheet()->getStyle('A'.$Ligne)->applyFromArray($this->styleTitreVertical);
				$__statut = $this->objPHPExcel->getActiveSheet()->getStyle('A'.$Ligne)->getAlignment()->setWrapText( TRUE );
				
				$__statut = $this->objPHPExcel->getActiveSheet()->setCellValue('B'.$Ligne, $L_Nombre_APR_associes);
				$__statut = $this->objPHPExcel->getActiveSheet()->getStyle('B'.$Ligne)->applyFromArray($this->styleTitreVertical);
				$__statut = $this->objPHPExcel->getActiveSheet()->getStyle('B'.$Ligne)->getAlignment()->setWrapText( TRUE );
				
				$__statut = $this->objPHPExcel->getActiveSheet()->setCellValue('C'.$Ligne, $L_Moyenne_Poids_APR);
				$__statut = $this->objPHPExcel->getActiveSheet()->getStyle('C'.$Ligne)->applyFromArray($this->styleTitreVertical);
				$__statut = $this->objPHPExcel->getActiveSheet()->getStyle('C'.$Ligne)->getAlignment()->setWrapText( TRUE );
				
				$Ligne += 1;
			}

			if ($Occurrence->moyenne_poids_apr == NULL or $Occurrence->moyenne_poids_apr == '') $Occurrence->moyenne_poids_apr = 0;
//print('Occurrence->moyenne_poids_apr = ' . $Occurrence->moyenne_poids_apr.'<br>');
			$this->objPHPExcel->getActiveSheet()->setCellValue('A'.$Ligne, $Occurrence->spp_code.' - '.$Occurrence->spp_nom);

			$this->objPHPExcel->getActiveSheet()->setCellValue('B'.$Ligne, (string)$Occurrence->nbr_apr);

			$this->objPHPExcel->getActiveSheet()->setCellValue('C'.$Ligne, (string)$Occurrence->moyenne_poids_apr);

			$Ligne += 1;
		}

		if ($Ligne > 1) $Ligne -= 1;

		$this->objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize( TRUE );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth( 20 );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth( 20 );

		// ===============================================================
		// Met une bordure autour des libelles et du corps de la Matrice.
		// Bordure autour des libellés des Actifs Supports.
		$this->objPHPExcel->getActiveSheet()
			->getStyle('A1:C' . $Ligne)
			->applyFromArray($this->styleBordureNoirAutourGrisInterieur);


		return TRUE;
	}


	/* ================================================================================== */

	public function editionAppreciationRisques( $crs_id, $uniquement_risques_evalues, $limitation = 0 ) {
	/**
	* Edite l'Appréciation des Risques.
	*
	* @author Pierre-Luc MARY
	* @date 2014-11-28
	*
	* @param[in] $crs_id ID de la Cartographie à utiliser
	*
	* @return Renvoi vrai si les informations ont bien été éditées.
	*			
	*/
		include_once( DIR_LIBRAIRIES . '/Class_HTML.inc.php' );
		include_once( DIR_LIBRAIRIES . '/Class_CartographiesRisques_PDO.inc.php' );

		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_libelles_generiques.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-AppreciationRisques.php' );


		$objCartographie = new CartographiesRisques();


		$Titre_Onglet = $L_Appreciation_Risques;

		// Création d'un nouvel onglet et lui donne un nom.
		$this->objPHPExcel->createSheet()->setTitle( $Titre_Onglet );

		// Mise en page de l'onglet.
		$this->objPHPExcel->setActiveSheetIndexByName( $Titre_Onglet )->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$this->objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

		// Répète sur chaque page les lignes 1 à 4.
		$this->objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 4);

		// Initialise le haut et le bas de page de l'onglet courant.
		$this->initialiseHautBasOnglet( $Titre_Onglet );


		// ==========================================================
		// Vérifie si la Cartographie dispose d'Evénements Redoutés.
		$requete = $this->prepareSQL( 'SELECT count(evr_id) AS total FROM evr_evenements_redoutes AS "evr" WHERE crs_id = :crs_id ' );
			
		$Total_EVR = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->executeSQL($requete)
			->fetchObject()->total;


		// =====================================
		// Construit la partie fixe du tableau.

		// Titre : Menaces / Vulnérabilités.
		$this->objPHPExcel->getActiveSheet()->getStyle( 'A1:A4' )->applyFromArray(
			array_merge( $this->styleTitrePrincipalHorizontalSpecial, $this->styleBordureNoirAutour ) );

		$this->objPHPExcel->getActiveSheet()->setCellValue( 'A2', $L_Menaces );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'A2' )->applyFromArray(
			array_merge( $this->styleTitreNomHorizontal, $this->styleTextAGauche ) );

		$this->objPHPExcel->getActiveSheet()->setCellValue( 'A4', $L_Vulnerabilites );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'A4' )->applyFromArray(
			array_merge( $this->styleTitreNomHorizontal, $this->styleTextADroite ) );

		$this->objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth( 60 );


		// Titre : Risques.
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'B1', ucfirst( $L_Risques ) );
		$this->objPHPExcel->getActiveSheet()->mergeCells( 'B1:G1' );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'B1:F1' )->applyFromArray(
			array_merge( $this->styleTextToutCentre, $this->styleBordureNoirAutour, $this->styleTitrePrincipalHorizontal ) );

		// Titre : Code.
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'B3', $L_Code );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'B2:B4' )->applyFromArray(
			array_merge( $this->styleTitreNomHorizontal, $this->styleTextToutCentre, $this->styleBordureNoirAutour ) );

		// Titre : Scénario.
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'C4', $L_Scenario );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth( 80 );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'C2:C4' )->applyFromArray(
			array_merge( $this->styleTitreNomHorizontal, $this->styleTextToutCentre, $this->styleBordureNoirAutour ) );

		// Titre : Niveau de risque.
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'D3', $L_Niveau_Risque );
		$this->objPHPExcel->getActiveSheet()->getStyle('D3')->getAlignment()->setWrapText( TRUE );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'D2:D4' )->applyFromArray(
			array_merge( $this->styleTitreNomHorizontal, $this->styleTextToutCentre, $this->styleBordureNoirAutour ) );

		// Titre : Vraisemblance.
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'E2', $L_Vraisemblance );
		$this->objPHPExcel->getActiveSheet()->mergeCells( 'E2:E4' );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'E2:E4' )->applyFromArray( array_merge( $this->styleTitreNomVertical, $this->styleBordureNoirAutour ) );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension( 'E' )->setWidth( 4 );

		$this->objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight( 25 );
		$this->objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight( 35 );
		$this->objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight( 35 );
		$this->objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight( 25 );

		// Titre : Niveau impact.
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'F2', $L_Niveau_Impact );
		$this->objPHPExcel->getActiveSheet()->mergeCells( 'F2:F4' );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'F2:F4' )->applyFromArray( array_merge( $this->styleTitreNomVertical, $this->styleBordureNoirAutour ) );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension( 'F' )->setWidth( 4 );

		// Titre : Sensibilité de l'actif affecté
		$this->objPHPExcel->getActiveSheet()->mergeCells( 'G2:G4' );
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'G2', $L_Sensibilite_Actif_affecte );
		$this->objPHPExcel->getActiveSheet()->getStyle('G2')->getAlignment()->setWrapText( TRUE );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'G2' )->applyFromArray(
			array_merge( $this->styleTitreNomHorizontal, $this->styleTextToutCentre, $this->styleBordureNoirAutour ) );


		// --------------------------------------------------------------
		// Construit la colonne des Evénements Redoutés (si nécessaire).
		$Derniere_Colonne = 8;

		if ( $Total_EVR > 0 ) {
			$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );

			// Ajoute un titre pour annoncer les Types d'Actifs Supports.
			$this->objPHPExcel->getActiveSheet()->setCellValue( $ColonneCourante . '1', $L_Evenement_Redoute );

			$this->objPHPExcel->getActiveSheet()->mergeCells( $ColonneCourante . '1:' . $ColonneCourante . '4' );

			$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante . '1:' . $ColonneCourante . '4' )->applyFromArray(
				array_merge( $this->styleTitrePrincipalHorizontalSpecial, $this->styleBordureNoirAutour, $this->styleTextToutCentre ) );

			$this->objPHPExcel->getActiveSheet()->getColumnDimension( $ColonneCourante )->setWidth( 20 );
			$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante . '1' )->getAlignment()->setWrapText( TRUE );

			$Derniere_Colonne += 1; // Décale la colonne, car elle vient d'être utilisée.
		}


		// Titre : Actifs Primordiaux.
		$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );

		$this->objPHPExcel->getActiveSheet()->setCellValue( $ColonneCourante . '1', $L_Actifs_Primordiaux );

		$this->objPHPExcel->getActiveSheet()->mergeCells( $ColonneCourante . '1:' . $ColonneCourante . '4' );

		$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante . '1:' . $ColonneCourante . '4' )->applyFromArray(
			array_merge( $this->styleTitrePrincipalHorizontal, $this->styleBordureNoirAutour, $this->styleTextToutCentre ) );

		$this->objPHPExcel->getActiveSheet()->getColumnDimension( $ColonneCourante )->setWidth( 40 );
		$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante . '1' )->getAlignment()->setWrapText( TRUE );

		$Derniere_Colonne += 1;


		// Titre : Actif Support.
		$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );

		$this->objPHPExcel->getActiveSheet()->setCellValue( $ColonneCourante . '1', $L_Actif_Support );

		$this->objPHPExcel->getActiveSheet()->mergeCells( $ColonneCourante . '1:' . $ColonneCourante . '4' );

		$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante . '1:' . $ColonneCourante . '4' )->applyFromArray(
			array_merge( $this->styleTitrePrincipalHorizontalSpecial, $this->styleBordureNoirAutour, $this->styleTextToutCentre ) );

		$this->objPHPExcel->getActiveSheet()->getColumnDimension( $ColonneCourante )->setWidth( 30 );
		$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante . '1' )->getAlignment()->setWrapText( TRUE );


		// ==============================================
		// Construit la liste des Risques.
		if ( $Total_EVR > 0 ) {
			$sql = 'SELECT ' .
				'rcs.rcs_id, rcs.rcs_code, rcs.rcs_scenario, rcs.mgn_id, rcs.spp_id, rcs.vrs_id, ' .
				'spp.spp_nom, spp.spp_code, ' .
				'evr.gri_id, evr_libelle, ' .
				'vrs.vrs_poids, pea.pea_cotation, ' .
				'CAST(substr(mgn.mgn_code, 5) AS numeric) AS "mgn_code", ' .
				'gri.gri_poids, ' .
				'car.car_poids, ' .
				'coalesce((CASE WHEN coalesce(car.car_poids,0) = 0 THEN vrs.vrs_poids + gri.gri_poids ELSE car.car_poids END)::int,0) AS "poids_brut" ' .
				'FROM rcs_risques_cartographies AS "rcs" ' .
				'LEFT JOIN rcev_rcs_evr AS "rcev" ON rcev.rcs_id = rcs.rcs_id ' .
				'LEFT JOIN evr_evenements_redoutes AS "evr" ON evr.evr_id = rcev.evr_id ' .
				'LEFT JOIN vrs_vraisemblances_risques AS "vrs" ON vrs.vrs_id = rcs.vrs_id ' .
				'LEFT JOIN mgn_menaces_generiques AS "mgn" ON mgn.mgn_id = rcs.mgn_id ' .
				'LEFT JOIN gri_grilles_impact AS "gri" ON gri.gri_id = evr.gri_id ' .
				'LEFT JOIN pea_poids_evaluation_actifs AS "pea" ON pea.pea_id = rcs.rcs_cotation_actif ' .
				'LEFT JOIN spp_supports AS "spp" ON spp.spp_id = rcs.spp_id ' .
				'LEFT JOIN car_criteres_appreciation_risques AS "car" ON car.gri_id = evr.gri_id AND car.vrs_id = rcs.vrs_id ' .
				'WHERE evr.crs_id = :crs_id ';

			if ( $uniquement_risques_evalues == 'o' )
				$sql .= 'AND ( vrs.vrs_poids IS NOT NULL OR car.car_poids IS NOT NULL ) ';

			$sql .= 'ORDER BY poids_brut DESC, mgn_code '; //mgn.mgn_code ';
		} else {
			$sql = 'SELECT 
rcs.rcs_id, rcs.rcs_code, rcs.rcs_scenario, rcs.mgn_id, rcs.gri_id, rcs.spp_id, rcs.vrs_id,
spp.spp_nom, spp.spp_code,
vrs.vrs_poids, pea.pea_cotation,
CAST(substr(mgn.mgn_code, 5) AS numeric) AS "mgn_code",
gri.gri_poids,
car.car_poids,
coalesce((CASE WHEN coalesce(car.car_poids,0) = 0 THEN vrs.vrs_poids + gri.gri_poids ELSE car.car_poids END)::int,0) AS "poids_brut"
FROM rcs_risques_cartographies AS "rcs"
LEFT JOIN spp_supports AS "spp" ON spp.spp_id = rcs.spp_id
LEFT JOIN vrs_vraisemblances_risques AS "vrs" ON vrs.vrs_id = rcs.vrs_id 
LEFT JOIN mgn_menaces_generiques AS "mgn" ON mgn.mgn_id = rcs.mgn_id 
LEFT JOIN gri_grilles_impact AS "gri" ON gri.gri_id = rcs.gri_id 
LEFT JOIN pea_poids_evaluation_actifs AS "pea" ON pea.pea_id = rcs.rcs_cotation_actif 
LEFT JOIN car_criteres_appreciation_risques AS "car" ON car.gri_id = rcs.gri_id AND car.vrs_id = rcs.vrs_id 
LEFT JOIN spcr_spp_crs AS "spcr" ON spcr.spp_id = spp.spp_id 
WHERE spcr.crs_id = :crs_id ';

			if ( $uniquement_risques_evalues == 'o' )
				$sql .= 'AND ( vrs.vrs_poids IS NOT NULL OR car.car_poids IS NOT NULL ) ';

			$sql .= 'ORDER BY poids_brut DESC, mgn.mgn_code ';
		}

		if ( $limitation > 0 ) $sql .= ' LIMIT ' . $limitation . ' ';

		$requete = $this->prepareSQL( $sql );
			
		$Liste_Risques = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);

		$DebutLigne = 5; // Numéro de la ligne courante
		$Derniere_Ligne = $DebutLigne; // Dernière ligne de la Matrice
		$Couleur_Fond = FALSE;


		// Récupère la représentation des niveaux de risque.
		$requete = $this->prepareSQL(
			'SELECT rnr_debut_poids, rnr_fin_poids, rnr_code_couleur FROM rnr_representation_niveaux_risque AS "rnr" '
		);
		
		$NiveauxRisque = $this->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);

		foreach ($Liste_Risques as $Occurrence) {
			$mgn_libelle = $objCartographie->recupererLibelleMenace( $crs_id, $Occurrence->mgn_id, $this->Langue );
			$this->objPHPExcel->getActiveSheet()->setCellValue( 'A'.$Derniere_Ligne, $Occurrence->mgn_code . ' - ' . $mgn_libelle );
			$this->objPHPExcel->getActiveSheet()->getStyle( 'A'.$Derniere_Ligne )->applyFromArray( $this->styleTextAGauche );
			$this->objPHPExcel->getActiveSheet()->getStyle( 'A'.$Derniere_Ligne . ':' . 'A'.($Derniere_Ligne+1) )
				->applyFromArray( $this->styleBordureNoirAutourGrisInterieur );

			$this->objPHPExcel->getActiveSheet()->setCellValue( 'B'.$Derniere_Ligne, 'R' . $Occurrence->rcs_code );
			$this->objPHPExcel->getActiveSheet()->mergeCells( 'B'.$Derniere_Ligne . ':' . 'B'.($Derniere_Ligne+1) );
			$this->objPHPExcel->getActiveSheet()->getStyle( 'B'.$Derniere_Ligne . ':' . 'B'.($Derniere_Ligne+1) )
				->applyFromArray( $this->styleTextAGaucheHaut );

			$this->objPHPExcel->getActiveSheet()->setCellValue( 'C'.$Derniere_Ligne, strip_tags($Occurrence->rcs_scenario) );
			$this->objPHPExcel->getActiveSheet()->mergeCells( 'C'.$Derniere_Ligne . ':' . 'C'.($Derniere_Ligne+1) );
			$this->objPHPExcel->getActiveSheet()->getStyle( 'C'.$Derniere_Ligne . ':' . 'C'.($Derniere_Ligne+1) )
				->applyFromArray( $this->styleTextAGaucheHaut );


			// Mettre en place la bonne couleur.
			if ( $Occurrence->car_poids != NULL ) $PoidsRisque = $Occurrence->car_poids;
			else $PoidsRisque = $Occurrence->vrs_poids + $Occurrence->gri_poids;

			$Couleur_Risque = '';

			foreach ($NiveauxRisque as $NiveauRisque) {
				if ( $PoidsRisque >= $NiveauRisque->rnr_debut_poids and $PoidsRisque <= $NiveauRisque->rnr_fin_poids ) {
					$Couleur_Risque = $NiveauRisque->rnr_code_couleur;
				}
			}

			$this->objPHPExcel->getActiveSheet()->setCellValue( 'E'.$Derniere_Ligne, (string)$Occurrence->vrs_poids );
			$this->objPHPExcel->getActiveSheet()->mergeCells( 'E'.$Derniere_Ligne . ':' . 'E'.($Derniere_Ligne+1) );
			$this->objPHPExcel->getActiveSheet()->getStyle( 'E'.$Derniere_Ligne . ':' . 'E'.($Derniere_Ligne+1) )
				->applyFromArray( $this->styleTextToutCentre );

			$this->objPHPExcel->getActiveSheet()->setCellValue( 'F'.$Derniere_Ligne, (string)$Occurrence->gri_poids );
			$this->objPHPExcel->getActiveSheet()->mergeCells( 'F'.$Derniere_Ligne . ':' . 'F'.($Derniere_Ligne+1) );
			$this->objPHPExcel->getActiveSheet()->getStyle( 'F'.$Derniere_Ligne . ':' . 'F'.($Derniere_Ligne+1) )
				->applyFromArray( $this->styleTextToutCentre );

			$this->objPHPExcel->getActiveSheet()->setCellValue( 'G'.$Derniere_Ligne, (string)$Occurrence->pea_cotation );
			$this->objPHPExcel->getActiveSheet()->mergeCells( 'G'.$Derniere_Ligne . ':' . 'G'.($Derniere_Ligne+1) );
			$this->objPHPExcel->getActiveSheet()->getStyle( 'G'.$Derniere_Ligne . ':' . 'G'.($Derniere_Ligne+1) )
				->applyFromArray( $this->styleTextToutCentre );

			$Derniere_Colonne = 8;

			if ( $Total_EVR > 0 ) {
				$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );

				$this->objPHPExcel->getActiveSheet()->setCellValue( $ColonneCourante.$Derniere_Ligne, $Occurrence->evr_libelle );
				$this->objPHPExcel->getActiveSheet()->mergeCells( $ColonneCourante.$Derniere_Ligne . ':' . $ColonneCourante.($Derniere_Ligne+1) );
				$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante.$Derniere_Ligne . ':' . $ColonneCourante.($Derniere_Ligne+1) )
					->applyFromArray( $this->styleTextAGaucheHaut );

				$Derniere_Colonne += 1;
			}


			// Actifs Primordiaux.
			$requete = $this->prepareSQL(
				'SELECT apr_code||\' - \'||apr_nom AS "actif" ' .
				'FROM spp_supports AS "spp" ' .
				'LEFT JOIN apsp_apr_spp AS "apsp" ON spp.spp_id = apsp.spp_id ' .
				'LEFT JOIN apr_actifs_primordiaux AS "apr" ON apsp.apr_id = apr.apr_id ' .
				'WHERE spp.spp_id = ' . $Occurrence->spp_id
			);

			$Liste_Actifs_Primordiaux = $this->executeSQL($requete)
				->fetchAll(PDO::FETCH_CLASS);

			$Actifs = '';
			foreach ( $Liste_Actifs_Primordiaux as $_Tmp ) {
				if ( $Actifs != '' ) $Actifs .= "\n";

				$Actifs .= '- ' . $_Tmp->actif;
			}


			$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );

			$this->objPHPExcel->getActiveSheet()->setCellValue( $ColonneCourante.$Derniere_Ligne, $Actifs );

			$this->objPHPExcel->getActiveSheet()->mergeCells(
				$ColonneCourante.$Derniere_Ligne . ':' . $ColonneCourante.($Derniere_Ligne+1) );

			$this->objPHPExcel->getActiveSheet()->getStyle(
				$ColonneCourante.$Derniere_Ligne . ':' . $ColonneCourante.($Derniere_Ligne+1) )
				->applyFromArray( $this->styleTextAGauche );

			$Derniere_Colonne += 1; // Change de ligne.


			// Actif Support.
			$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );

			$this->objPHPExcel->getActiveSheet()->setCellValue( $ColonneCourante.$Derniere_Ligne, $Occurrence->spp_code.' - '.$Occurrence->spp_nom );

			$this->objPHPExcel->getActiveSheet()->mergeCells(
				$ColonneCourante.$Derniere_Ligne . ':' . $ColonneCourante.($Derniere_Ligne+1) );

			$this->objPHPExcel->getActiveSheet()->getStyle(
				$ColonneCourante.$Derniere_Ligne . ':' . $ColonneCourante.($Derniere_Ligne+1) )
				->applyFromArray( $this->styleTextAGauche );

			$Derniere_Ligne += 1; // Change de ligne.


			// Récupère toutes les Vulnérabilités de ce Risque.
			$requete = $this->prepareSQL(
				'SELECT rcv_libelle, lbr_libelle FROM rcvl_rcs_vln AS "rcv" ' .
				'LEFT JOIN vln_vulnerabilites AS "vln" ON vln.vln_id = rcv.vln_id ' .
				'LEFT JOIN lbr_libelles_referentiel AS "rlb" ON rlb.lbr_code = vln_code AND rlb.lng_id = :langue ' .
				'WHERE rcv.rcs_id = :rcs_id '
			);
			
			$Liste_Vulnerabilites = $this->bindSQL($requete, ':rcs_id', $Occurrence->rcs_id, self::ID_TYPE)
				->bindSQL($requete, ':langue', $this->Langue, self::LANGUE_TYPE, self::LANGUE_LENGTH)
				->executeSQL($requete)
				->fetchAll(PDO::FETCH_CLASS);

			$Vulnerabilites = '';
			foreach ( $Liste_Vulnerabilites as $Occurrence ) {
				if ( $Vulnerabilites != '' ) $Vulnerabilites .= "\n";

				if ( $Occurrence->rcv_libelle != '' ) {
					$Vulnerabilites .= $Occurrence->rcv_libelle;
				} else {
					$Vulnerabilites .= $Occurrence->lbr_libelle;
				}
			}

			$this->objPHPExcel->getActiveSheet()->setCellValue( 'A'.$Derniere_Ligne, $Vulnerabilites );
			$this->objPHPExcel->getActiveSheet()->getStyle( 'A'.$Derniere_Ligne )->applyFromArray( $this->styleTextADroite );

			// Applique des propriétés à l'ensemble des cellules du corps de la Matrice.
			$this->objPHPExcel->getActiveSheet()->getStyle( 'A'.($Derniere_Ligne-1).':'.$this->xlColumnValue( $Derniere_Colonne ) . $Derniere_Ligne )
				->getAlignment()->setWrapText( TRUE );

			if ( $Couleur_Fond == FALSE ) {
				$styleSpecial = array_merge( $this->styleBordureNoirAutour, $this->styleSurligne );
				$Couleur_Fond = TRUE;
			} else {
				$styleSpecial = $this->styleBordureNoirAutour;
				$Couleur_Fond = FALSE;
			}

			$this->objPHPExcel->getActiveSheet()->getStyle( 'A'.($Derniere_Ligne-1).':'.$this->xlColumnValue( $Derniere_Colonne ) . $Derniere_Ligne )
				->applyFromArray( $styleSpecial );


			$this->objPHPExcel->getActiveSheet()->mergeCells( 'D'.($Derniere_Ligne-1) . ':' . 'D'.$Derniere_Ligne );

			if ( $NiveauRisque != '' and $PoidsRisque != 0 ) {
				$this->objPHPExcel->getActiveSheet()->setCellValue( 'D'.($Derniere_Ligne-1), (string)$PoidsRisque );
				$this->objPHPExcel->getActiveSheet()->getStyle( 'D'.($Derniere_Ligne-1) . ':' . 'D'.$Derniere_Ligne )
					->applyFromArray( array_merge( $this->styleTextToutCentre,
						array(
							'font' => array(
								'color' => array(
					 				'argb' => 'FF' . HTML::calculCouleurCelluleHexa( $Couleur_Risque )
					 				)
								),
							'fill' => array(
		 						'type' => PHPExcel_Style_Fill::FILL_SOLID,
		 						'startcolor' => array(
		 							'argb' => 'FF' . $Couleur_Risque
		 							)
		 						)
	 						)
						)
					);				
			}

			$Derniere_Ligne += 1; // Change de ligne.
		}

		$Derniere_Ligne -= 1; // Rattrape le décalage.


		// Met une bordure pour séparer les éléments de la matrice.
		for ($i = 1; $i < $Derniere_Colonne; $i++ ) {
			$ColonneCourante = $this->xlColumnValue( $i );

			$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante . '5:' . $ColonneCourante . $Derniere_Ligne )
				->applyFromArray( $this->styleBordureNoirAutour );		
		}

		return TRUE;
	}


	/* ================================================================================== */

	public function editionTraitementRisques( $crs_id, $limitation = 0 ) {
	/**
	* Edite le Traitement des Risques.
	*
	* @author Pierre-Luc MARY
	* @date 2014-11-28
	*
	* @param[in] $crs_id ID de la Cartographie à utiliser
	*
	* @return Renvoi vrai si les informations ont bien été éditées.
	*			
	*/
		include_once( DIR_LIBRAIRIES . '/Class_HTML.inc.php' );
		include_once( DIR_LIBRAIRIES . '/Class_CartographiesRisques_PDO.inc.php' );

		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_libelles_generiques.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_HBL_Generiques.inc.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-AppreciationRisques.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-TraitementRisques.php' );


		$objCartographie = new CartographiesRisques();


		$Titre_Onglet = $L_Traitement_Risques;

		// Création d'un nouvel onglet et lui donne un nom.
		$this->objPHPExcel->createSheet()->setTitle( $Titre_Onglet );

		// Mise en page de l'onglet.
		$this->objPHPExcel->setActiveSheetIndexByName( $Titre_Onglet )->getPageSetup()
			->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$this->objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

		// Répète sur chaque page les lignes 1 à 4.
		$this->objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 4);

		// Initialise le haut et le bas de page de l'onglet courant.
		$this->initialiseHautBasOnglet( $Titre_Onglet );


		// ==========================================================
		// Vérifie si la Cartographie dispose d'Evénements Redoutés.
		$requete = $this->prepareSQL(
			'SELECT count(evr_id) AS total FROM evr_evenements_redoutes AS "evr" WHERE crs_id = :crs_id '
			);
			
		$Total_EVR = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->executeSQL($requete)
			->fetchObject()->total;


		// =====================================
		// Construit la partie fixe du tableau.

		// Titre : Menaces / Vulnérabilités.
		$this->objPHPExcel->getActiveSheet()->getStyle( 'A1:A2' )->applyFromArray(
			array_merge( $this->styleTitrePrincipalHorizontalSpecial, $this->styleBordureNoirAutour ) );

		$this->objPHPExcel->getActiveSheet()->setCellValue( 'A1', $L_Menaces );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'A1' )->applyFromArray(
			array_merge( $this->styleTitreNomHorizontal, $this->styleTextAGauche ) );

		$this->objPHPExcel->getActiveSheet()->setCellValue( 'A2', $L_Vulnerabilites );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'A2' )->applyFromArray(
			array_merge( $this->styleTitreNomHorizontal, $this->styleTextADroite ) );

		$this->objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth( 60 );


		// Titre : Risques.
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'B1', ucfirst( $L_Risques ) );
		$this->objPHPExcel->getActiveSheet()->mergeCells( 'B1:G1' );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'B1:G1' )->applyFromArray(
			array_merge( $this->styleTextToutCentre, $this->styleBordureNoirAutour, $this->styleTitrePrincipalHorizontal ) );

		// Titre : Code.
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'B2', $L_Code );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'B2' )->applyFromArray(
			array_merge( $this->styleTitreNomHorizontal, $this->styleTextToutCentre, $this->styleBordureNoirAutour ) );

		// Titre : Scénario.
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'C2', $L_Scenario );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth( 80 );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'C2' )->applyFromArray(
			array_merge( $this->styleTitreNomHorizontal, $this->styleTextToutCentre, $this->styleBordureNoirAutour ) );

		// Titre : Niveau de risque (Brut).
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'D2', $L_Niveau_Risque_Brut );
		$this->objPHPExcel->getActiveSheet()->getStyle('D2')->getAlignment()->setWrapText( TRUE );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'D2' )->applyFromArray(
			array_merge( $this->styleTitreNomHorizontal, $this->styleTextToutCentre, $this->styleBordureNoirAutour ) );

		// Titre : Niveau de risque (Net).
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'E2', $L_Niveau_Risque_Net );
		$this->objPHPExcel->getActiveSheet()->getStyle('E2')->getAlignment()->setWrapText( TRUE );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'E2' )->applyFromArray(
			array_merge( $this->styleTitreNomHorizontal, $this->styleTextToutCentre, $this->styleBordureNoirAutour ) );

		// Titre : Type de traitement
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'F2', $L_Type_Traitement );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth( 15 );
		$this->objPHPExcel->getActiveSheet()->getStyle('F2')->getAlignment()->setWrapText( TRUE );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'F2' )->applyFromArray(
			array_merge( $this->styleTitreNomHorizontal, $this->styleTextToutCentre, $this->styleBordureNoirAutour ) );

		// Titre : Couverture
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'G2', $L_Couverture );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth( 15 );
		$this->objPHPExcel->getActiveSheet()->getStyle('G2')->getAlignment()->setWrapText( TRUE );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'G2' )->applyFromArray(
			array_merge( $this->styleTitreNomHorizontal, $this->styleTextToutCentre, $this->styleBordureNoirAutour ) );


		// --------------------------------------------------------------
		// Construit la colonne des Evénements Redoutés (si nécessaire).
		$Derniere_Colonne = 8;

		if ( $Total_EVR > 0 ) {
			$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );

			// Ajoute un titre pour annoncer les Types d'Actifs Supports.
			$this->objPHPExcel->getActiveSheet()->setCellValue( $ColonneCourante . '1', $L_Evenement_Redoute );

			$this->objPHPExcel->getActiveSheet()->mergeCells( $ColonneCourante . '1:' . $ColonneCourante . '2' );

			$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante . '1:' . $ColonneCourante . '2' )->applyFromArray(
				array_merge( $this->styleTitrePrincipalHorizontalSpecial, $this->styleBordureNoirAutour, $this->styleTextToutCentre ) );

			$this->objPHPExcel->getActiveSheet()->getColumnDimension( $ColonneCourante )->setWidth( 20 );
			$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante . '1' )->getAlignment()->setWrapText( TRUE );

			$Derniere_Colonne += 1; // Décale la colonne, car elle vient d'être utilisée.
		}


		// Titre : Mesures
		$Colonne_1 = $this->xlColumnValue( $Derniere_Colonne );

		$Derniere_Colonne += 1;
		$Colonne_2 = $this->xlColumnValue( $Derniere_Colonne );


		$this->objPHPExcel->getActiveSheet()->setCellValue( $Colonne_1 . '1', $L_Mesures );

		$this->objPHPExcel->getActiveSheet()->mergeCells( $Colonne_1 . '1:' . $Colonne_2 . '1' );

		$this->objPHPExcel->getActiveSheet()->getStyle( $Colonne_1 . '1:' . $Colonne_2 . '1' )->applyFromArray(
			array_merge( $this->styleTitrePrincipalHorizontal, $this->styleBordureNoirAutour, $this->styleTextToutCentre ) );

		$this->objPHPExcel->getActiveSheet()->getColumnDimension( $Colonne_1 )->setWidth( 50 );
		$this->objPHPExcel->getActiveSheet()->getStyle( $Colonne_1 . '1' )->getAlignment()->setWrapText( TRUE );

		// Titre : Libelle
		$this->objPHPExcel->getActiveSheet()->setCellValue( $Colonne_1 . '2', $L_Libelle );
		$this->objPHPExcel->getActiveSheet()->getStyle( $Colonne_1 . '2' )->getAlignment()->setWrapText( TRUE );
		$this->objPHPExcel->getActiveSheet()->getStyle( $Colonne_1 . '2' )->applyFromArray(
			array_merge( $this->styleTitreNomHorizontal, $this->styleTextToutCentre, $this->styleBordureNoirAutour ) );

		// Titre : Statut
		$this->objPHPExcel->getActiveSheet()->setCellValue( $Colonne_2 . '2', $L_Status );
		$this->objPHPExcel->getActiveSheet()->getStyle( $Colonne_2 . '2' )->getAlignment()->setWrapText( TRUE );
		$this->objPHPExcel->getActiveSheet()->getStyle( $Colonne_2 . '2' )->applyFromArray(
			array_merge( $this->styleTitreNomHorizontal, $this->styleTextToutCentre, $this->styleBordureNoirAutour ) );

		$Derniere_Colonne += 1;


		// Titre : Risque résiduel.
		$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );

		$this->objPHPExcel->getActiveSheet()->setCellValue( $ColonneCourante . '1', $L_Risque_Residuel );

		$this->objPHPExcel->getActiveSheet()->mergeCells( $ColonneCourante . '1:' . $ColonneCourante . '2' );

		$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante . '1:' . $ColonneCourante . '2' )->applyFromArray(
			array_merge( $this->styleTitrePrincipalHorizontalSpecial, $this->styleBordureNoirAutour, $this->styleTextToutCentre ) );

		$this->objPHPExcel->getActiveSheet()->getColumnDimension( $ColonneCourante )->setWidth( 30 );
		$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante . '1' )->getAlignment()->setWrapText( TRUE );


		// ==============================================
		// Construit la liste des Risques.
		if ( $Total_EVR > 0 ) {
			$sql = 'SELECT ' .
				'rcs.*, ' .
				'spp.spp_nom, spp.spp_code, ' .
				'evr.gri_id, evr_libelle, ' .
				'vrs.vrs_poids, pea.pea_cotation, ' .
				'vrs_trt.vrs_poids AS "vrs_poids_trt", ' .
				'CAST(substr(mgn.mgn_code, 5) AS numeric) AS "mgn_code", ' .
				'gri.gri_poids, ' .
				'gri_trt.gri_poids AS "gri_poids_trt", ' .
				'lbr1.lbr_libelle AS "rcs_type_traitement_libelle", ' .
				'lbr2.lbr_libelle AS "rcs_couverture_libelle", ' .
				'car.car_poids, car_trt.car_poids AS "car_poids_trt", ' .
				'coalesce((CASE WHEN coalesce(car.car_poids,0) = 0 THEN vrs.vrs_poids + gri.gri_poids ELSE car.car_poids END)::int,0) AS "poids_brut", ' .
				'coalesce((CASE WHEN coalesce(car_trt.car_poids,0) = 0 THEN vrs_trt.vrs_poids + gri_trt.gri_poids ELSE car_trt.car_poids END)::int,0) AS "poids_net" ' .
				'FROM rcs_risques_cartographies AS "rcs" ' .
				'LEFT JOIN rcev_rcs_evr AS "rcev" ON rcev.rcs_id = rcs.rcs_id ' .
				'LEFT JOIN evr_evenements_redoutes AS "evr" ON evr.evr_id = rcev.evr_id ' .
				'LEFT JOIN vrs_vraisemblances_risques AS "vrs" ON vrs.vrs_id = rcs.vrs_id ' .
				'LEFT JOIN vrs_vraisemblances_risques AS "vrs_trt" ON vrs_trt.vrs_id = rcs.vrs_id_trt ' .
				'LEFT JOIN mgn_menaces_generiques AS "mgn" ON mgn.mgn_id = rcs.mgn_id ' .
				'LEFT JOIN gri_grilles_impact AS "gri" ON gri.gri_id = evr.gri_id ' .
				'LEFT JOIN gri_grilles_impact AS "gri_trt" ON gri_trt.gri_id = rcs.gri_id_trt ' .
				'LEFT JOIN pea_poids_evaluation_actifs AS "pea" ON pea.pea_id = rcs.rcs_cotation_actif ' .
				'LEFT JOIN spcr_spp_crs AS "spcr" ON spcr.spp_id = rcs.spp_id AND spcr.crs_id = rcs.crs_id ' .
				'LEFT JOIN spp_supports AS "spp" ON spp.spp_id = spcr.spp_id ' .
				'LEFT JOIN lbr_libelles_referentiel AS "lbr1" ON lbr1.lbr_code = \'RCS_TT_\' || rcs.rcs_type_traitement_code AND lbr1.lng_id = :langue ' .
				'LEFT JOIN lbr_libelles_referentiel AS "lbr2" ON lbr2.lbr_code = \'RCS_ETAT_\' || rcs.rcs_couverture_code AND lbr2.lng_id = :langue ' .
				'LEFT JOIN car_criteres_appreciation_risques AS "car" ON car.gri_id = evr.gri_id AND car.vrs_id = rcs.vrs_id ' .
				'LEFT JOIN car_criteres_appreciation_risques AS "car_trt" ON car_trt.gri_id = rcs.gri_id_trt AND car_trt.vrs_id = rcs.vrs_id_trt ' .
				'LEFT JOIN crs_cartographies_risques AS "crs" ON crs.crs_id = spcr.crs_id ' .
				'LEFT JOIN ent_entites AS "ent" ON ent.ent_id = crs.ent_id ' .
				'WHERE rcs.crs_id = :crs_id ' .
				'AND rcs_etat != :etat_ignore ' .
				'AND ( vrs.vrs_poids IS NOT NULL OR car.car_poids IS NOT NULL ) ' .
				//'AND (vrs.vrs_poids + gri.gri_poids) >= ent_niveau_criteres_valorisation_actifs ' .
				'ORDER BY poids_net DESC, poids_brut DESC, mgn_code '; //mgn.mgn_code ';
		} else {
			$sql = 'SELECT ' .
				'rcs.*, ' .
				'spp.spp_nom, spp.spp_code, ' .
				'vrs.vrs_poids, pea.pea_cotation, ' .
				'vrs_trt.vrs_poids AS "vrs_poids_trt", ' .
				'CAST(substr(mgn.mgn_code, 5) AS numeric) AS "mgn_code", ' .
				'gri.gri_poids, ' .
				'gri_trt.gri_poids AS "gri_poids_trt", ' .
				'lbr1.lbr_libelle AS "rcs_type_traitement_libelle", ' .
				'lbr2.lbr_libelle AS "rcs_couverture_libelle", ' .
				'car.car_poids, car_trt.car_poids AS "car_poids_trt", ' .
				'coalesce((CASE WHEN coalesce(car.car_poids,0) = 0 THEN vrs.vrs_poids + gri.gri_poids ELSE car.car_poids END)::int,0) AS "poids_brut", ' .
				'coalesce((CASE WHEN coalesce(car_trt.car_poids,0) = 0 THEN vrs_trt.vrs_poids + gri_trt.gri_poids ELSE car_trt.car_poids END)::int,0) AS "poids_net" ' .
				'FROM rcs_risques_cartographies AS "rcs" ' .
				'LEFT JOIN spcr_spp_crs AS "spcr" ON spcr.spp_id = rcs.spp_id AND spcr.crs_id = rcs.crs_id ' .
				'LEFT JOIN spp_supports AS "spp" ON spp.spp_id = spcr.spp_id ' .
				'LEFT JOIN vrs_vraisemblances_risques AS "vrs" ON vrs.vrs_id = rcs.vrs_id ' .
				'LEFT JOIN vrs_vraisemblances_risques AS "vrs_trt" ON vrs_trt.vrs_id = rcs.vrs_id_trt ' .
				'LEFT JOIN mgn_menaces_generiques AS "mgn" ON mgn.mgn_id = rcs.mgn_id ' .
				'LEFT JOIN gri_grilles_impact AS "gri" ON gri.gri_id = rcs.gri_id ' .
				'LEFT JOIN gri_grilles_impact AS "gri_trt" ON gri_trt.gri_id = rcs.gri_id_trt ' .
				'LEFT JOIN pea_poids_evaluation_actifs AS "pea" ON pea.pea_id = rcs.rcs_cotation_actif ' .
				'LEFT JOIN lbr_libelles_referentiel AS "lbr1" ON lbr1.lbr_code = \'RCS_TT_\' || rcs.rcs_type_traitement_code AND lbr1.lng_id = :langue ' .
				'LEFT JOIN lbr_libelles_referentiel AS "lbr2" ON lbr2.lbr_code = \'RCS_ETAT_\' || rcs.rcs_couverture_code AND lbr2.lng_id = :langue ' .
				'LEFT JOIN car_criteres_appreciation_risques AS "car" ON car.gri_id = rcs.gri_id AND car.vrs_id = rcs.vrs_id ' .
				'LEFT JOIN car_criteres_appreciation_risques AS "car_trt" ON car_trt.gri_id = rcs.gri_id_trt AND car_trt.vrs_id = rcs.vrs_id_trt ' .
				'LEFT JOIN crs_cartographies_risques AS "crs" ON crs.crs_id = spcr.crs_id ' .
				'LEFT JOIN ent_entites AS "ent" ON ent.ent_id = crs.ent_id ' .
				'WHERE rcs.crs_id = :crs_id ' .
				'AND rcs_etat != :etat_ignore ' .
				//'AND (vrs.vrs_poids + gri.gri_poids) >= ent_niveau_criteres_valorisation_actifs ' .
				'AND ( vrs.vrs_poids IS NOT NULL OR car.car_poids IS NOT NULL ) ' .
				'ORDER BY poids_net DESC, poids_brut DESC, mgn.mgn_code ';
		}

		if ( $limitation > 0 ) $sql .= ' LIMIT ' . $limitation . ' ';

		$requete = $this->prepareSQL( $sql );
			
		$Liste_Risques = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->bindSQL($requete, ':langue', $this->Langue, self::LANGUE_TYPE, self::LANGUE_LENGTH)
			->bindSQL($requete, ':etat_ignore', self::ETAT_IGNORE, self::LANGUE_TYPE, self::LANGUE_LENGTH)
			->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);
			
		// Récupère la représentation des niveaux de risque.
		$requete = $this->prepareSQL(
			'SELECT rnr_debut_poids, rnr_fin_poids, rnr_code_couleur FROM rnr_representation_niveaux_risque AS "rnr" '
		);
		
		$NiveauxRisque = $this->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);


		// ===========================================
		// Edite le détail de l'ensemble des Risques.

		$Ligne_Courante = 3; // Numéro de la ligne courante
		$Derniere_Ligne = $Ligne_Courante;
		$Couleur_Fond = FALSE;

		foreach ($Liste_Risques as $Occurrence) {
			$Ligne_Suivante = $Ligne_Courante + 1;

			$mgn_libelle = $objCartographie->recupererLibelleMenace( $crs_id, $Occurrence->mgn_id, $this->Langue );

			// Valeur : Code et libellé de la menace.
			$this->objPHPExcel->getActiveSheet()->setCellValue( 'A'.$Ligne_Courante, $Occurrence->mgn_code . ' - ' . $mgn_libelle );


			// Récupère toutes les Vulnérabilités de ce Risque.
			$requete = $this->prepareSQL(
				'SELECT rcv_libelle, lbr_libelle FROM rcvl_rcs_vln AS "rcv" ' .
				'LEFT JOIN vln_vulnerabilites AS "vln" ON vln.vln_id = rcv.vln_id ' .
				'LEFT JOIN lbr_libelles_referentiel AS "rlb" ON rlb.lbr_code = vln_code AND rlb.lng_id = :langue ' .
				'WHERE rcv.rcs_id = :rcs_id '
			);
			
			$Liste_Vulnerabilites = $this->bindSQL($requete, ':rcs_id', $Occurrence->rcs_id, self::ID_TYPE)
				->bindSQL($requete, ':langue', $this->Langue, self::LANGUE_TYPE, self::LANGUE_LENGTH)
				->executeSQL($requete)
				->fetchAll(PDO::FETCH_CLASS);


			$Vulnerabilites = '';

			foreach ( $Liste_Vulnerabilites as $Vulnerabilite ) {
				if ( $Vulnerabilites != '' ) $Vulnerabilites .= "\n";

				if ( $Vulnerabilite->rcv_libelle != '' ) {
					$Vulnerabilites .= $Vulnerabilite->rcv_libelle;
				} else {
					$Vulnerabilites .= $Vulnerabilite->lbr_libelle;
				}
			}

			$this->objPHPExcel->getActiveSheet()->setCellValue( 'A'.$Ligne_Suivante, $Vulnerabilites );


			// Valeur : Code du risque.
			$this->objPHPExcel->getActiveSheet()->setCellValue( 'B'.$Ligne_Courante, 'R' . $Occurrence->rcs_code );

			// Valeur : Scénario du risque.
			$this->objPHPExcel->getActiveSheet()->setCellValue( 'C'.$Ligne_Courante, strip_tags($Occurrence->rcs_scenario) );


			// Mettre en place la valeur des niveaux de risque et des bonnes couleurs.
			if ( $Occurrence->car_poids != NULL ) $PoidsRisque = $Occurrence->car_poids;
			else $PoidsRisque = $Occurrence->vrs_poids + $Occurrence->gri_poids;

			$Couleur_Risque = '';

			if ( $Occurrence->car_poids != NULL ) $PoidsRisque_trt = $Occurrence->car_poids_trt;
			else $PoidsRisque_trt = $Occurrence->vrs_poids_trt + $Occurrence->gri_poids_trt;

			$Couleur_Risque_trt = '';

			foreach ($NiveauxRisque as $NiveauRisque) {
				if ( $PoidsRisque >= $NiveauRisque->rnr_debut_poids and $PoidsRisque <= $NiveauRisque->rnr_fin_poids ) {
					$Couleur_Risque = $NiveauRisque->rnr_code_couleur;
				}

				if ( $PoidsRisque_trt >= $NiveauRisque->rnr_debut_poids and $PoidsRisque_trt <= $NiveauRisque->rnr_fin_poids ) {
					$Couleur_Risque_trt = $NiveauRisque->rnr_code_couleur;
				}
			}

			if ( $Couleur_Risque != '' ) {
				$this->objPHPExcel->getActiveSheet()->setCellValue( 'D'.$Ligne_Courante, (string)$PoidsRisque );
			}

			if ( $Couleur_Risque_trt != '' ) {
				$this->objPHPExcel->getActiveSheet()->setCellValue( 'E'.$Ligne_Courante, (string)$PoidsRisque_trt );
			}


			// Valeur : Type de traitement
			$this->objPHPExcel->getActiveSheet()->setCellValue( 'F'.$Ligne_Courante, $Occurrence->rcs_type_traitement_libelle );

			
			// Valeur : Couverture
			$this->objPHPExcel->getActiveSheet()->setCellValue( 'G'.$Ligne_Courante, $Occurrence->rcs_couverture_libelle );


			$Derniere_Colonne = 8;

			if ( $Total_EVR > 0 ) {
				$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );

				$this->objPHPExcel->getActiveSheet()->setCellValue( $ColonneCourante.$Ligne_Courante, $Occurrence->evr_libelle );

				$Derniere_Colonne += 1;
			}


			// Valeur : Liste des Mesures attachées à ce Risque.
			$requete = $this->prepareSQL(
				'SELECT ' .
				'lbr.lbr_libelle AS "mgr_libelle", ' .
				'lbr1.lbr_libelle AS "mgr_etat_libelle" ' .
				'FROM mcr_mesures_cartographies AS "mcr" ' .
				'LEFT JOIN mgr_mesures_generiques AS "mgr" ON mgr.mgr_id = mcr.mgr_id ' .
				'LEFT JOIN lbr_libelles_referentiel AS "lbr" ON lbr.lbr_code = mgr_code AND lbr.lng_id = :langue ' .
				'LEFT JOIN lbr_libelles_referentiel AS "lbr1" ON lbr1.lbr_code = \'MCR_ETAT_\'||mcr_etat_code AND lbr1.lng_id = :langue ' .
				'WHERE mcr.rcs_id = :rcs_id '
			);
			
			$Liste_Mesures = $this->bindSQL($requete, ':rcs_id', $Occurrence->rcs_id, self::ID_TYPE)
				->bindSQL($requete, ':langue', $this->Langue, self::LANGUE_TYPE, self::LANGUE_LENGTH)
				->executeSQL($requete)
				->fetchAll(PDO::FETCH_CLASS);

			$Total_Mesures = count( $Liste_Mesures );

			$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );

			$Derniere_Colonne += 1;

			$ColonneSuivante = $this->xlColumnValue( $Derniere_Colonne );

			$Derniere_Ligne = $Ligne_Courante; // Dernière ligne de la Matrice


			foreach ( $Liste_Mesures as $Mesure ) {
				$this->objPHPExcel->getActiveSheet()->setCellValue( $ColonneCourante.$Derniere_Ligne, $Mesure->mgr_libelle );

				$this->objPHPExcel->getActiveSheet()->setCellValue( $ColonneSuivante.$Derniere_Ligne, $Mesure->mgr_etat_libelle );

				$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante.$Derniere_Ligne . ':' . $ColonneSuivante.$Derniere_Ligne )
					->applyFromArray( $this->styleTextAGauche );

				$Derniere_Ligne += 1;
			}

			$Derniere_Colonne += 1;


			// Valeur : Risque résiduel.
			$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );

			$this->objPHPExcel->getActiveSheet()->setCellValue( $ColonneCourante.$Ligne_Courante, strip_tags( $Occurrence->rcs_justif_risque_residuel ) );


			// --------------------------------------------
			// Gestion de la fusion de certaines cellules.
			if ( $Total_Mesures < 2 ) {
				$Derniere_Ligne = $Ligne_Courante + 1;
			} else {
				$Derniere_Ligne = $Ligne_Courante + ( $Total_Mesures - 1 );
				$this->objPHPExcel->getActiveSheet()->mergeCells( 'A'.$Ligne_Suivante . ':' . 'A'.$Derniere_Ligne );
			}

			// Fusion des éléments de la colonne : Code du Risque.
			$this->objPHPExcel->getActiveSheet()->mergeCells( 'B'.$Ligne_Courante . ':' . 'B'.$Derniere_Ligne );

			// Fusion des éléments de la colonne : Scénario du Risque.
			$this->objPHPExcel->getActiveSheet()->mergeCells( 'C'.$Ligne_Courante . ':' . 'C'.$Derniere_Ligne );

			// Fusion des éléments de la colonne : Niveau de Risque Brut.
			$this->objPHPExcel->getActiveSheet()->mergeCells( 'D'.$Ligne_Courante . ':' . 'D'.$Derniere_Ligne );

			// Fusion des éléments de la colonne : Niveau de Risque Net.
			$this->objPHPExcel->getActiveSheet()->mergeCells( 'E'.$Ligne_Courante . ':' . 'E'.$Derniere_Ligne );

			// Fusion des éléments de la colonne : Type de Traitement du Risque.
			$this->objPHPExcel->getActiveSheet()->mergeCells( 'F'.$Ligne_Courante . ':' . 'F'.$Derniere_Ligne );

			// Fusion des éléments de la colonne : Couverture du Risque.
			$this->objPHPExcel->getActiveSheet()->mergeCells( 'G'.$Ligne_Courante . ':' . 'G'.$Derniere_Ligne );

			// Fusion des éléments de la colonne : Evénement Redouté du Risque.
			$Derniere_Colonne = 8;

			if ( $Total_EVR > 0 ) {
				$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );

				$this->objPHPExcel->getActiveSheet()->mergeCells( $ColonneCourante.$Ligne_Courante . ':' . $ColonneCourante.$Derniere_Ligne );

				$Derniere_Colonne += 1;
			}


			if ( $Total_Mesures < 2 ) {
				// Fusion des éléments de la colonne : Du Libellé des Mesures associées au Risque.
				$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );

				$this->objPHPExcel->getActiveSheet()->mergeCells(
					$ColonneCourante.$Ligne_Courante . ':' . $ColonneCourante.$Derniere_Ligne );

				$Derniere_Colonne += 1;

				// Fusion des éléments de la colonne : Du Statut des Mesures associées au Risque.
				$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );

				$this->objPHPExcel->getActiveSheet()->mergeCells(
					$ColonneCourante.$Ligne_Courante . ':' . $ColonneCourante.$Derniere_Ligne );

				$Derniere_Colonne += 1;

			} else {
				$Derniere_Colonne += 2;
			}


			// Fusion des éléments de la colonne : Du Risque Résiduel.
			$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );

			$this->objPHPExcel->getActiveSheet()->mergeCells(
				$ColonneCourante.$Ligne_Courante . ':' . $ColonneCourante.$Derniere_Ligne );

			$Derniere_Colonne += 1;


			// ----------------------------------------------------
			// Gestion de l'alignement des textes et des bordures.

			// Alignement de la Menace.
			$this->objPHPExcel->getActiveSheet()->getStyle( 'A'.$Ligne_Courante )->applyFromArray( $this->styleTextAGauche );

			// Alignement des Vulnérabilités.
			$this->objPHPExcel->getActiveSheet()->getStyle( 'A'.$Ligne_Suivante )->applyFromArray( $this->styleTextADroite );

			// Affichage de la bordure autour de la Menace et des Vulnérabilités.
			$this->objPHPExcel->getActiveSheet()->getStyle( 'A'.$Ligne_Courante . ':' . 'A'.$Ligne_Suivante )
				->applyFromArray( $this->styleBordureNoirAutourGrisInterieur );


			// Alignement du Code du Risque.
			$this->objPHPExcel->getActiveSheet()->getStyle( 'B'.$Ligne_Courante . ':' . 'B'.$Derniere_Ligne )
				->applyFromArray( $this->styleTextAGauche );

			// Affichage de la bordure autour du Code du Risque.
			$this->objPHPExcel->getActiveSheet()->getStyle( 'B'.$Ligne_Courante . ':' . 'B'.$Derniere_Ligne )
				->applyFromArray( $this->styleBordureNoirAutour );


			// Alignement du Scénario du Risque.
			$this->objPHPExcel->getActiveSheet()->getStyle( 'C'.$Ligne_Courante . ':' . 'C'.$Derniere_Ligne )
				->applyFromArray( $this->styleTextAGauche );

			// Affichage de la bordure autour du Scénario du Risque.
			$this->objPHPExcel->getActiveSheet()->getStyle( 'C'.$Ligne_Courante . ':' . 'C'.$Derniere_Ligne )
				->applyFromArray( $this->styleBordureNoirAutour );


			// Affichage de la bordure autour du Niveau de Risque Brut.
			$this->objPHPExcel->getActiveSheet()->getStyle( 'D'.$Ligne_Courante . ':' . 'D'.$Derniere_Ligne )
				->applyFromArray( $this->styleBordureNoirAutour );


			// Affichage de la bordure autour du Niveau de Risque Net.
			$this->objPHPExcel->getActiveSheet()->getStyle( 'E'.$Ligne_Courante . ':' . 'E'.$Derniere_Ligne )
				->applyFromArray( $this->styleBordureNoirAutour );


			// Alignement du Type de Traitement du Risque.
			$this->objPHPExcel->getActiveSheet()->getStyle( 'F'.$Ligne_Courante . ':' . 'F'.$Derniere_Ligne )
				->applyFromArray( $this->styleTextToutCentre );

			// Affichage de la bordure autour du Scénario du Risque.
			$this->objPHPExcel->getActiveSheet()->getStyle( 'F'.$Ligne_Courante . ':' . 'F'.$Derniere_Ligne )
				->applyFromArray( $this->styleBordureNoirAutour );


			// Alignement de la Couverture du Risque.
			$this->objPHPExcel->getActiveSheet()->getStyle( 'G'.$Ligne_Courante . ':' . 'G'.$Derniere_Ligne )
				->applyFromArray( $this->styleTextToutCentre );

			// Affichage de la bordure autour de la Couverture du Risque.
			$this->objPHPExcel->getActiveSheet()->getStyle( 'G'.$Ligne_Courante . ':' . 'G'.$Derniere_Ligne )
				->applyFromArray( $this->styleBordureNoirAutour );


			// Alignement de l'Evénement Redouté associé au Risque (s'il existe).
			$Derniere_Colonne = 8;

			if ( $Total_EVR > 0 ) {
				$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );
				$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante.$Ligne_Courante . ':' . $ColonneCourante.$Derniere_Ligne )
					->applyFromArray( $this->styleTextAGauche );

				// Affichage de la bordure autour de l'événement redouté.
				$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante.$Ligne_Courante . ':' . $ColonneCourante.$Derniere_Ligne )
					->applyFromArray( $this->styleBordureNoirAutour );

				$Derniere_Colonne += 1;
			}


			// Affichage de la bordure autour des Mesures.
			$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );

			$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante.$Ligne_Courante . ':' . $ColonneCourante.$Derniere_Ligne )
				->applyFromArray( $this->styleBordureNoirAutourGrisInterieur );

			$Derniere_Colonne += 1;

			$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );

			$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante.$Ligne_Courante . ':' . $ColonneCourante.$Derniere_Ligne )
				->applyFromArray( $this->styleBordureNoirAutourGrisInterieur );

			$Derniere_Colonne += 1;


			// Alignement du Risque Résiduel
			$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );

			$this->objPHPExcel->getActiveSheet()->getStyle(
				$ColonneCourante.$Ligne_Courante . ':' . $ColonneCourante.$Derniere_Ligne )
				->applyFromArray( $this->styleTextAGauche );

			// Affichage de la bordure autour du Scénario du Risque.
			$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante.$Ligne_Courante . ':' . $ColonneCourante.$Derniere_Ligne )
				->applyFromArray( $this->styleBordureNoirAutour );



			// --------------------------------------------------------------------------
			// Applique des propriétés à l'ensemble des cellules du corps de la Matrice.
			$this->objPHPExcel->getActiveSheet()->getStyle( 'A'.$Ligne_Courante.':'.$this->xlColumnValue( $Derniere_Colonne ) . $Derniere_Ligne )
				->getAlignment()->setWrapText( TRUE );

			if ( $Couleur_Fond == FALSE ) {
				$styleSpecial = array_merge( $this->styleBordureNoirAutour, $this->styleSurligne );
				$Couleur_Fond = TRUE;
			} else {
				$styleSpecial = $this->styleBordureNoirAutour;
				$Couleur_Fond = FALSE;
			}

			$this->objPHPExcel->getActiveSheet()->getStyle( 'A'.$Ligne_Courante.':'.$this->xlColumnValue( $Derniere_Colonne ) . $Derniere_Ligne )
				->applyFromArray( $styleSpecial );


			// Alignement du Niveau de Risque Brut et de sa couleur.
			if ( $Couleur_Risque != '' ) {
				$this->objPHPExcel->getActiveSheet()->getStyle( 'D'.$Ligne_Courante . ':' . 'D'.$Derniere_Ligne )
					->applyFromArray( array_merge( $this->styleTextToutCentre,
						array(
							'font' => array(
								'color' => array(
									'argb' => 'FF' . HTML::calculCouleurCelluleHexa( $Couleur_Risque )
									)
								),
							'fill' => array(
								'type' => PHPExcel_Style_Fill::FILL_SOLID,
								'startcolor' => array(
									'argb' => 'FF' . $Couleur_Risque
									)
								)
							)
						)
					);
			}

			// Alignement du Niveau de Risque Net et de sa couleur.
			if ( $Couleur_Risque_trt != '' ) {
				$this->objPHPExcel->getActiveSheet()->getStyle( 'E'.$Ligne_Courante . ':' . 'E'.$Derniere_Ligne )
					->applyFromArray( array_merge( $this->styleTextToutCentre,
						array(
							'font' => array(
								'color' => array(
									'argb' => 'FF' . HTML::calculCouleurCelluleHexa( $Couleur_Risque_trt )
									)
								),
							'fill' => array(
								'type' => PHPExcel_Style_Fill::FILL_SOLID,
								'startcolor' => array(
									'argb' => 'FF' . $Couleur_Risque_trt
									)
								)
							)
						)
					);
			}


			$Ligne_Courante = $Derniere_Ligne + 1; // Change de ligne.
		}

		$Ligne_Courante -= 1; // Rattrape le décalage.


		// Met une bordure pour séparer les éléments de la matrice.
		for ($i = 1; $i < $Derniere_Colonne; $i++ ) {
			$ColonneCourante = $this->xlColumnValue( $i );

			$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante . '5:' . $ColonneCourante . $Derniere_Ligne )
				->applyFromArray( $this->styleBordureNoirAutour );		
		}

		return TRUE;
	}


	/* ================================================================================== */

	public function editionActions( $crs_id ) {
	/**
	* Edite les Actions associées à une Cartographie.
	*
	* @author Pierre-Luc MARY
	* @date 2017-05-15
	*
	* @param[in] $crs_id ID de la Cartographie à utiliser
	*
	* @return Renvoi vrai si les informations ont bien été éditées.
	*			
	*/
		include_once( DIR_LIBRAIRIES . '/Class_HTML.inc.php' );
		include_once( DIR_LIBRAIRIES . '/Class_CartographiesRisques_PDO.inc.php' );

		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_libelles_generiques.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_HBL_Generiques.inc.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-AppreciationRisques.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-TraitementRisques.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-Actions.php' );


		$objCartographie = new CartographiesRisques();


		$Titre_Onglet = ucfirst($L_Actions);

		// Création d'un nouvel onglet et lui donne un nom.
		$this->objPHPExcel->createSheet()->setTitle( $Titre_Onglet );

		// Mise en page de l'onglet.
		$this->objPHPExcel->setActiveSheetIndexByName( $Titre_Onglet )->getPageSetup()
			->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$this->objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

		// Répète sur chaque page les lignes 1 à 4.
		$this->objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 4);

		// Initialise le haut et le bas de page de l'onglet courant.
		$this->initialiseHautBasOnglet( $Titre_Onglet );


		// ==========================================================
		// Vérifie si la Cartographie dispose d'Evénements Redoutés.
		$requete = $this->prepareSQL(
			'SELECT count(evr_id) AS total FROM evr_evenements_redoutes AS "evr" WHERE crs_id = :crs_id '
			);
			
		$Total_EVR = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->executeSQL($requete)
			->fetchObject()->total;


		// ================================================
		// Construit la partie fixe du tableau (l'entête).

		// ------------------------------------
		// Colonne => Menaces / Vulnérabilités

		// Titre : Menaces / Vulnérabilités.
		$this->objPHPExcel->getActiveSheet()->getStyle( 'A1:A2' )->applyFromArray(
			array_merge( $this->styleTitrePrincipalHorizontalSpecial, $this->styleBordureNoirAutour ) );

		$this->objPHPExcel->getActiveSheet()->setCellValue( 'A1', $L_Menaces );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'A1' )->applyFromArray(
			array_merge( $this->styleTitreNomHorizontal, $this->styleTextAGauche ) );

		$this->objPHPExcel->getActiveSheet()->setCellValue( 'A2', $L_Vulnerabilites );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'A2' )->applyFromArray(
			array_merge( $this->styleTitreNomHorizontal, $this->styleTextADroite ) );

		$this->objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth( 60 );


		// -------------------
		// Colonne => Risques

		// Titre : Risques.
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'B1', ucfirst( $L_Risques ) );
		$this->objPHPExcel->getActiveSheet()->mergeCells( 'B1:E1' );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'B1:E1' )->applyFromArray(
			array_merge( $this->styleTextToutCentre, $this->styleBordureNoirAutour, $this->styleTitrePrincipalHorizontal ) );

		// Sous-Titre : Code.
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'B2', $L_Code );

		// Sous-Titre : Scénario.
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'C2', $L_Scenario );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth( 60 );

		// Sous-Titre : Actif Support.
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'D2', $L_Actif_Support );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth( 25 );
		$this->objPHPExcel->getActiveSheet()->getStyle('D2')->getAlignment()->setWrapText( TRUE );

		// Sous-Titre : Actifs Primordiaux.
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'E2', $L_Actifs_Primordiaux );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth( 25 );
		$this->objPHPExcel->getActiveSheet()->getStyle('E2')->getAlignment()->setWrapText( TRUE );

		// Bordure autour de la zone "Risque"
		$this->objPHPExcel->getActiveSheet()->getStyle( 'B2:E2' )->applyFromArray(
			array_merge( $this->styleTitreNomHorizontal, $this->styleTextToutCentre, $this->styleBordureNoirAutourGrisInterieur ) );


		// -----------------------------------------------------
		// Colonne => Evénements Redoutés (créée si nécessaire)
		$Derniere_Colonne = 6;

		if ( $Total_EVR > 0 ) {
			$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );

			// Ajoute un titre pour annoncer les Types d'Actifs Supports.
			$this->objPHPExcel->getActiveSheet()->setCellValue( $ColonneCourante . '1', $L_Evenement_Redoute );

			$this->objPHPExcel->getActiveSheet()->mergeCells( $ColonneCourante . '1:' . $ColonneCourante . '2' );

			$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante . '1:' . $ColonneCourante . '2' )->applyFromArray(
				array_merge( $this->styleTitrePrincipalHorizontalSpecial, $this->styleBordureNoirAutour, $this->styleTextToutCentre ) );

			$this->objPHPExcel->getActiveSheet()->getColumnDimension( $ColonneCourante )->setWidth( 25 );
			$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante . '1' )->getAlignment()->setWrapText( TRUE );

			$Derniere_Colonne += 1; // Décale la colonne, car elle vient d'être utilisée.
		}

		// -------------------
		// Colonne => Mesures

		// Titre : Mesures
		$Colonne_1 = $this->xlColumnValue( $Derniere_Colonne );

		$Derniere_Colonne += 1;
		$Colonne_2 = $this->xlColumnValue( $Derniere_Colonne );


		$this->objPHPExcel->getActiveSheet()->setCellValue( $Colonne_1 . '1', $L_Mesures );

		$this->objPHPExcel->getActiveSheet()->mergeCells( $Colonne_1 . '1:' . $Colonne_2 . '1' );

		if ( $Total_EVR > 0 ) {
			$this->objPHPExcel->getActiveSheet()->getStyle( $Colonne_1 . '1:' . $Colonne_2 . '1' )->applyFromArray(
				array_merge( $this->styleTitrePrincipalHorizontal, $this->styleBordureNoirAutour, $this->styleTextToutCentre ) );
		} else {
			$this->objPHPExcel->getActiveSheet()->getStyle( $Colonne_1 . '1:' . $Colonne_2 . '1' )->applyFromArray(
				array_merge( $this->styleTitrePrincipalHorizontalSpecial, $this->styleBordureNoirAutour, $this->styleTextToutCentre ) );
		}

		$this->objPHPExcel->getActiveSheet()->getColumnDimension( $Colonne_1 )->setWidth( 50 );
		$this->objPHPExcel->getActiveSheet()->getStyle( $Colonne_1 . '1' )->getAlignment()->setWrapText( TRUE );

		// Titre : Libelle
		$this->objPHPExcel->getActiveSheet()->setCellValue( $Colonne_1 . '2', $L_Libelle );
		$this->objPHPExcel->getActiveSheet()->getStyle( $Colonne_1 . '2' )->getAlignment()->setWrapText( TRUE );

		// Titre : Statut
		$this->objPHPExcel->getActiveSheet()->setCellValue( $Colonne_2 . '2', $L_Status );
		$this->objPHPExcel->getActiveSheet()->getStyle( $Colonne_2 . '2' )->getAlignment()->setWrapText( TRUE );

		// Bordure autour de la zone "Mesure"
		$this->objPHPExcel->getActiveSheet()->getStyle( $Colonne_1 . '2:' . $Colonne_2 . '2' )->applyFromArray(
			array_merge( $this->styleTitreNomHorizontal, $this->styleTextToutCentre, $this->styleBordureNoirAutourGrisInterieur ) );


		// -------------------
		// Colonne => Actions

		$Derniere_Colonne += 1;

		// Titre : Actions.
		$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );
		$ColonneDebutAction = $ColonneCourante;
		$ColonneFinAction = $this->xlColumnValue( $Derniere_Colonne + 5 );

		$this->objPHPExcel->getActiveSheet()->setCellValue( $ColonneCourante . '1', ucfirst( $L_Actions ) );

		$this->objPHPExcel->getActiveSheet()->mergeCells( $ColonneCourante . '1:' . $ColonneFinAction . '1' );

		if ( $Total_EVR > 0 ) {
			$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante . '1:' . $ColonneFinAction . '1' )->applyFromArray(
				array_merge( $this->styleTitrePrincipalHorizontalSpecial, $this->styleBordureNoirAutour, $this->styleTextToutCentre ) );
		} else {
			$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante . '1:' . $ColonneFinAction . '1' )->applyFromArray(
				array_merge( $this->styleTitrePrincipalHorizontal, $this->styleBordureNoirAutour, $this->styleTextToutCentre ) );
		}

		// Titre : Libellé
		$this->objPHPExcel->getActiveSheet()->setCellValue( $ColonneCourante . '2', $L_Libelle );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension( $ColonneCourante )->setWidth( 50 );
		$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante . '2' )->getAlignment()->setWrapText( TRUE );

		// Titre : Acteur
		$Derniere_Colonne += 1;
		$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );

		$this->objPHPExcel->getActiveSheet()->setCellValue( $ColonneCourante . '2', $L_Acteur );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension( $ColonneCourante )->setWidth( 15 );
		$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante . '2' )->getAlignment()->setWrapText( TRUE );

		// Titre : Date début
		$Derniere_Colonne += 1;
		$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );

		$this->objPHPExcel->getActiveSheet()->setCellValue( $ColonneCourante . '2', $L_Date_Debut );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension( $ColonneCourante )->setWidth( 10 );
		$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante . '2' )->getAlignment()->setWrapText( TRUE );

		// Titre : Date fin
		$Derniere_Colonne += 1;
		$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );

		$this->objPHPExcel->getActiveSheet()->setCellValue( $ColonneCourante . '2', $L_Date_Fin );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension( $ColonneCourante )->setWidth( 10 );
		$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante . '2' )->getAlignment()->setWrapText( TRUE );

		// Titre : Fréquence
		$Derniere_Colonne += 1;
		$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );

		$this->objPHPExcel->getActiveSheet()->setCellValue( $ColonneCourante . '2', $L_Frequence );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension( $ColonneCourante )->setWidth( 15 );
		$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante . '2' )->getAlignment()->setWrapText( TRUE );

		// Titre : Statut
		$Derniere_Colonne += 1;
		$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );

		$this->objPHPExcel->getActiveSheet()->setCellValue( $ColonneCourante . '2', $L_Status );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension( $ColonneCourante )->setWidth( 15 );
		$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante . '2' )->getAlignment()->setWrapText( TRUE );

		// Bordure autour de la zone "Action"
		$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneDebutAction . '2:' . $ColonneFinAction . '2' )->applyFromArray(
			array_merge( $this->styleTitreNomHorizontal, $this->styleTextToutCentre, $this->styleBordureNoirAutour ) );


		// *********************************************************************

		// =====================================================================
		// Construit la liste des Risques, des Mesures et des Actions associés.
		if ( $Total_EVR > 0 ) {
			$sql = 'SELECT
rcs.rcs_id,
mgn.mgn_code, lbr_mgn.lbr_libelle AS "mgn_libelle",
rcs.rcs_code, rcs.rcs_scenario,
spp.spp_code, spp.spp_nom,
string_agg( \' - \'||apr_nom, \'\n\' )  AS "apr_noms",
string_agg( DISTINCT evr_libelle, \'\n\' ) AS evr_libelles
FROM rcs_risques_cartographies AS "rcs"
LEFT JOIN rcev_rcs_evr AS "rcev" ON rcev.rcs_id = rcs.rcs_id
LEFT JOIN evr_evenements_redoutes AS "evr" ON evr.evr_id = rcev.evr_id
LEFT JOIN evap_evr_apr AS "evap" ON evap.evr_id = evr.evr_id
LEFT JOIN apr_actifs_primordiaux AS "apr" ON apr.apr_id = evap.apr_id
LEFT JOIN spp_supports AS "spp" ON spp.spp_id = rcs.spp_id
LEFT JOIN mgn_menaces_generiques AS "mgn" ON mgn.mgn_id = rcs.mgn_id
LEFT JOIN lbr_libelles_referentiel AS "lbr_mgn" ON lbr_mgn.lbr_code = mgn.mgn_code AND lbr_mgn.lng_id = :langue
LEFT JOIN mcr_mesures_cartographies AS "mcr" ON mcr.rcs_id = rcs.rcs_id
WHERE evr.crs_id = :crs_id
AND rcs_scenario != \'\'
AND rcs_etat != :etat_ignore
AND mcr.mcr_id IS NOT NULL
GROUP BY rcs.rcs_id, mgn.mgn_code, mgn_libelle, rcs.rcs_code, rcs.rcs_scenario, spp.spp_code, spp.spp_nom
ORDER BY rcs.rcs_id, mgn_libelle, rcs.rcs_scenario, spp.spp_nom ';
		} else {

			$sql = 'SELECT
rcs.rcs_id,
mgn.mgn_code, lbr_mgn.lbr_libelle AS "mgn_libelle",
rcs.rcs_code, rcs.rcs_scenario,
spp.spp_code, spp.spp_nom,
string_agg( \' - \'||apr_nom, \'\n\' )  AS "apr_noms"
FROM rcs_risques_cartographies AS "rcs"
LEFT JOIN spp_supports AS "spp" ON spp.spp_id = rcs.spp_id
LEFT JOIN apsp_apr_spp AS "apsp" ON apsp.spp_id = spp.spp_id
LEFT JOIN apr_actifs_primordiaux AS "apr" ON apr.apr_id = apsp.apr_id
LEFT JOIN mgn_menaces_generiques AS "mgn" ON mgn.mgn_id = rcs.mgn_id
LEFT JOIN lbr_libelles_referentiel AS "lbr_mgn" ON lbr_mgn.lbr_code = mgn.mgn_code AND lbr_mgn.lng_id = :langue
LEFT JOIN mcr_mesures_cartographies AS "mcr" ON mcr.rcs_id = rcs.rcs_id
LEFT JOIN spcr_spp_crs AS "spcr" ON spcr.spp_id = spp.spp_id 
WHERE spcr.crs_id = :crs_id
AND rcs_scenario != \'\'
AND rcs_etat != :etat_ignore
AND mcr.mcr_id IS NOT NULL
GROUP BY rcs.rcs_id, mgn.mgn_code, mgn_libelle, rcs.rcs_code, rcs.rcs_scenario, spp.spp_code, spp.spp_nom
ORDER BY rcs.rcs_id, mgn_libelle, rcs.rcs_scenario, spp.spp_nom ';
		}

		$requete = $this->prepareSQL( $sql );
			
		$Liste_Risques = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->bindSQL($requete, ':langue', $this->Langue, self::LANGUE_TYPE, self::LANGUE_LENGTH)
			->bindSQL($requete, ':etat_ignore', self::ETAT_IGNORE, self::LANGUE_TYPE, self::LANGUE_LENGTH)
			->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);


		// =========================================================================================
		// Edite le détail de l'ensemble des Risques, des Mesures et des Actions (corps du tableau.

		$Ligne_Courante = 3; // Ammorce la ligne courante
		$Couleur_Fond = FALSE;
		$Traceur = '';

		$Ligne_Debut_Mesure = $Ligne_Courante;
		$Ligne_Fin_Mesure = $Ligne_Courante + 1;

		foreach ($Liste_Risques as $Occurrence) {
			$Ligne_Suivante = $Ligne_Courante + 1;
			$Derniere_Ligne = $Ligne_Suivante;


			// Valeur : Code et libellé de la menace.
			$this->objPHPExcel->getActiveSheet()->setCellValue( 'A'.$Ligne_Courante,
				explode('_', $Occurrence->mgn_code)[1] .' - '. $Occurrence->mgn_libelle );


			// Récupère toutes les Vulnérabilités de ce Risque.
			$requete = $this->prepareSQL(
				'SELECT rcv_libelle, lbr_libelle FROM rcvl_rcs_vln AS "rcvl" ' .
				'LEFT JOIN vln_vulnerabilites AS "vln" ON vln.vln_id = rcvl.vln_id ' .
				'LEFT JOIN lbr_libelles_referentiel AS "lbr" ON lbr.lbr_code = vln_code AND lbr.lng_id = :langue ' .
				'WHERE rcvl.rcs_id = :rcs_id '
			);
			
			$Liste_Vulnerabilites = $this->bindSQL($requete, ':rcs_id', $Occurrence->rcs_id, self::ID_TYPE)
				->bindSQL($requete, ':langue', $this->Langue, self::LANGUE_TYPE, self::LANGUE_LENGTH)
				->executeSQL($requete)
				->fetchAll(PDO::FETCH_CLASS);


			$Vulnerabilites = '';

			foreach ( $Liste_Vulnerabilites as $Vulnerabilite ) {
				if ( $Vulnerabilites != '' ) $Vulnerabilites .= "\n";

				if ( $Vulnerabilite->rcv_libelle != '' ) {
					$Vulnerabilites .= $Vulnerabilite->rcv_libelle;
				} else {
					$Vulnerabilites .= $Vulnerabilite->lbr_libelle;
				}
			}

			// Valeur : Vulnérabilités du risque.
			$this->objPHPExcel->getActiveSheet()->setCellValue( 'A'.$Ligne_Suivante, $Vulnerabilites );


			// Valeur : Code du risque.
			$this->objPHPExcel->getActiveSheet()->setCellValue( 'B'.$Ligne_Courante, 'R'.$Occurrence->rcs_code );


			// Valeur : Scénario du risque.
			$this->objPHPExcel->getActiveSheet()->setCellValue( 'C'.$Ligne_Courante, strip_tags($Occurrence->rcs_scenario) );


			// Valeur : Actif support.
			$this->objPHPExcel->getActiveSheet()->setCellValue( 'D'.$Ligne_Courante, $Occurrence->spp_nom );


			// Valeur : Actifs primordiaux.
			$this->objPHPExcel->getActiveSheet()->setCellValue( 'E'.$Ligne_Courante, str_replace('\n', "\n", $Occurrence->apr_noms ) );


			$Derniere_Colonne = 6;

			if ( $Total_EVR > 0 ) {
				$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );

				$this->objPHPExcel->getActiveSheet()->setCellValue( $ColonneCourante.$Ligne_Courante, $Occurrence->evr_libelles );

				$Derniere_Colonne += 1;
			}


			// --------------------------------------------------
			// Valeur : Liste des Mesures attachées à ce Risque.
			$requete = $this->prepareSQL(
				'SELECT ' .
				'mcr.mcr_id, ' .
				'lbr.lbr_libelle AS "mgr_libelle", ' .
				'lbr1.lbr_libelle AS "mgr_etat_libelle" ' .
				'FROM mcr_mesures_cartographies AS "mcr" ' .
				'LEFT JOIN mgr_mesures_generiques AS "mgr" ON mgr.mgr_id = mcr.mgr_id ' .
				'LEFT JOIN lbr_libelles_referentiel AS "lbr" ON lbr.lbr_code = mgr_code AND lbr.lng_id = :langue ' .
				'LEFT JOIN lbr_libelles_referentiel AS "lbr1" ON lbr1.lbr_code = \'MCR_ETAT_\'||mcr_etat_code AND lbr1.lng_id = :langue ' .
				'WHERE mcr.rcs_id = :rcs_id '
			);
			
			$Liste_Mesures = $this->bindSQL($requete, ':rcs_id', $Occurrence->rcs_id, self::ID_TYPE)
				->bindSQL($requete, ':langue', $this->Langue, self::LANGUE_TYPE, self::LANGUE_LENGTH)
				->executeSQL($requete)
				->fetchAll(PDO::FETCH_CLASS);


			$Total_Mesures = count( $Liste_Mesures );


			foreach ( $Liste_Mesures as $Mesure ) {
				if ( $Total_EVR > 0 ) {
					$Colonne_Mesure_1 = $this->xlColumnValue( 7 );
					$Colonne_Mesure_2 = $this->xlColumnValue( 8 );
				} else {
					$Colonne_Mesure_1 = $this->xlColumnValue( 6 );
					$Colonne_Mesure_2 = $this->xlColumnValue( 7 );
				}

				// Affecte et cadre le Libellé de la Mesure.
				$this->objPHPExcel->getActiveSheet()->setCellValue( $Colonne_Mesure_1.$Ligne_Debut_Mesure, $Mesure->mgr_libelle );
				$this->objPHPExcel->getActiveSheet()->getStyle( $Colonne_Mesure_1.$Ligne_Debut_Mesure )->getAlignment()->setWrapText( TRUE );
				$this->objPHPExcel->getActiveSheet()->getStyle( $Colonne_Mesure_1.$Ligne_Debut_Mesure )
					->applyFromArray( $this->styleTextAGauche );

				// Affecte et cadre le Statut de la Mesure.
				$this->objPHPExcel->getActiveSheet()->setCellValue( $Colonne_Mesure_2.$Ligne_Debut_Mesure, $Mesure->mgr_etat_libelle );
				$this->objPHPExcel->getActiveSheet()->getStyle( $Colonne_Mesure_2.$Ligne_Debut_Mesure )->getAlignment()->setWrapText( TRUE );
				$this->objPHPExcel->getActiveSheet()->getStyle( $Colonne_Mesure_2.$Ligne_Debut_Mesure )
					->applyFromArray( $this->styleTextAGauche );


				// --------------------------------------------------
				// Valeur : Liste des Actions attachées à cette Mesure.
				$sql = 'SELECT
				act_libelle,
				idn.idn_login||\' - \'||cvl_prenom||\' \'||cvl_nom AS "acteur",
				act_date_debut_p,
				act_date_debut_r,
				act_date_fin_p,
				act_date_fin_r,
				lbr_act_frequence.lbr_libelle AS "act_frequence_libelle",
				lbr_act_statut.lbr_libelle AS "act_statut_libelle"

				FROM act_actions AS "act"
				LEFT JOIN idn_identites AS "idn" ON idn.idn_id = act.idn_id
				LEFT JOIN cvl_civilites AS "cvl" ON cvl.cvl_id = idn.cvl_id
				LEFT JOIN lbr_libelles_referentiel AS "lbr_act_frequence" ON lbr_act_frequence.lbr_code = act.act_frequence_code AND lbr_act_frequence.lng_id = :langue
				LEFT JOIN lbr_libelles_referentiel AS "lbr_act_statut" ON lbr_act_statut.lbr_code = act.act_statut_code AND lbr_act_statut.lng_id = :langue

				WHERE act.mcr_id = :mcr_id

				ORDER BY act_libelle ';

				$requete = $this->prepareSQL( $sql );
					
				$Liste_Actions = $this->bindSQL($requete, ':mcr_id', $Mesure->mcr_id, self::ID_TYPE)
					->bindSQL($requete, ':langue', $this->Langue, self::LANGUE_TYPE, self::LANGUE_LENGTH)
					->executeSQL($requete)
					->fetchAll(PDO::FETCH_CLASS);

				$Ligne_Action = $Ligne_Debut_Mesure;

				foreach ( $Liste_Actions as $Action ) {
					if ( $Total_EVR > 0 ) $ColonneAction = 9;
					else $ColonneAction = 8;

					// Décale la ligne de fin de Mesure au fur et à mesure des Actions à afficher.
					$Ligne_Fin_Mesure = $Ligne_Action;

					// Affecte et cadre le Libellé de l'Action.
					$ColonneActionCourante = $this->xlColumnValue( $ColonneAction );

					$this->objPHPExcel->getActiveSheet()->setCellValue( $ColonneActionCourante.$Ligne_Action, $Action->act_libelle );
					$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneActionCourante.$Ligne_Action )->getAlignment()->setWrapText( TRUE );
					$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneActionCourante.$Ligne_Action )
						->applyFromArray( $this->styleTextAGauche );

					// Affecte et cadre l'Acteur de l'Action.
					$ColonneAction += 1;
					$ColonneActionCourante = $this->xlColumnValue( $ColonneAction );

					$this->objPHPExcel->getActiveSheet()->setCellValue( $ColonneActionCourante.$Ligne_Action, $Action->acteur );
					$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneActionCourante.$Ligne_Action )->getAlignment()->setWrapText( TRUE );
					$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneActionCourante.$Ligne_Action )
						->applyFromArray( $this->styleTextAGauche );

					// Affecte et cadre la Date de Début de l'Action.
					$ColonneAction += 1;
					$ColonneActionCourante = $this->xlColumnValue( $ColonneAction );

					if ( $Action->act_date_debut_r != '' ) $Date_Debut = $Action->act_date_debut_r;
					else $Date_Debut = $Action->act_date_debut_p;

					$this->objPHPExcel->getActiveSheet()->setCellValue( $ColonneActionCourante.$Ligne_Action, $Date_Debut );
					$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneActionCourante.$Ligne_Action )->getAlignment()->setWrapText( TRUE );
					$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneActionCourante.$Ligne_Action )
						->applyFromArray( $this->styleTextAGauche );

					// Affecte et cadre la Date de Fin de l'Action.
					$ColonneAction += 1;
					$ColonneActionCourante = $this->xlColumnValue( $ColonneAction );

					if ( $Action->act_date_fin_r != '' ) $Date_Fin = $Action->act_date_fin_r;
					else $Date_Fin = $Action->act_date_fin_p;

					$this->objPHPExcel->getActiveSheet()->setCellValue( $ColonneActionCourante.$Ligne_Action, $Date_Fin );
					$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneActionCourante.$Ligne_Action )->getAlignment()->setWrapText( TRUE );
					$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneActionCourante.$Ligne_Action )
						->applyFromArray( $this->styleTextAGauche );

					// Affecte et cadre la Fréquence de l'Action.
					$ColonneAction += 1;
					$ColonneActionCourante = $this->xlColumnValue( $ColonneAction );

					$this->objPHPExcel->getActiveSheet()->setCellValue( $ColonneActionCourante.$Ligne_Action, $Action->act_frequence_libelle );
					$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneActionCourante.$Ligne_Action )->getAlignment()->setWrapText( TRUE );
					$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneActionCourante.$Ligne_Action )
						->applyFromArray( $this->styleTextAGauche );

					// Affecte et cadre le Statut de l'Action.
					$ColonneAction += 1;
					$ColonneActionCourante = $this->xlColumnValue( $ColonneAction );

					$this->objPHPExcel->getActiveSheet()->setCellValue( $ColonneActionCourante.$Ligne_Action, $Action->act_statut_libelle );
					$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneActionCourante.$Ligne_Action )->getAlignment()->setWrapText( TRUE );
					$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneActionCourante.$Ligne_Action )
						->applyFromArray( $this->styleTextAGauche );


					// Dessine un cadre autour de la zone : Action
					if ( $Total_EVR > 0 ) $ColonneAction = 9;
					else $ColonneAction = 8;

					$ColonneActionDebut = $this->xlColumnValue( $ColonneAction );

					$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneActionDebut.$Ligne_Action.':'.$ColonneActionCourante.$Ligne_Action )
						->applyFromArray( $this->styleBordureNoirAutourGrisInterieur );

					// Passe à la ligne d'action suivante.
					$Ligne_Action += 1;

				} // Fin boucle : Actions


				$Total_Actions = count( $Liste_Actions );


				// Cas d'une occurrence de Risque ou il n'y a entre 0 et 1 Mesure et 0 et 1 Action.
				if ( $Total_Actions < 2 && $Total_Mesures < 2 ) {
					// ------------------------------------------------
					// Fusion des éléments des sous-colonnes : Mesure.
					if ( $Total_EVR > 0 ) {
						$Derniere_Colonne = 7;
					} else {
						$Derniere_Colonne = 6;
					}

					// Fusionne et aligne la cellule : Libellé.
					$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );

					$this->objPHPExcel->getActiveSheet()->mergeCells( $ColonneCourante.$Ligne_Courante . ':' . $ColonneCourante.$Derniere_Ligne );

					$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante.$Ligne_Courante . ':' . $ColonneCourante.$Derniere_Ligne )
						->applyFromArray( $this->styleTextAGauche );

					// Fusionne et aligne la cellule : Statut.
					$Derniere_Colonne += 1;
					$ColonneSuivante = $this->xlColumnValue( $Derniere_Colonne );

					$this->objPHPExcel->getActiveSheet()->mergeCells( $ColonneSuivante.$Ligne_Courante . ':' . $ColonneSuivante.$Derniere_Ligne );

					$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneSuivante.$Ligne_Courante . ':' . $ColonneSuivante.$Derniere_Ligne )
						->applyFromArray( $this->styleTextAGauche );

					// Met une bordure autour des cellules : Libellé/Statut.
					$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante.$Ligne_Courante . ':' . $ColonneSuivante.$Derniere_Ligne )
						->applyFromArray( $this->styleBordureNoirAutourGrisInterieur );

					$Derniere_Colonne += 1;


					// ------------------------------------------------
					// Fusion des éléments des sous-colonnes : Action.

					// Fusion des éléments de la colonne : Mesure = Libellé.
					$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );
					$ColonneActionDebut = $ColonneCourante;

					$this->objPHPExcel->getActiveSheet()->mergeCells( $ColonneCourante.$Ligne_Courante . ':' . $ColonneCourante.$Derniere_Ligne );

					$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante.$Ligne_Courante . ':' . $ColonneCourante.$Derniere_Ligne )
						->applyFromArray( $this->styleTextAGauche );

					$Derniere_Colonne += 1;

					// Fusion des éléments de la colonne : Mesure = Acteur.
					$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );

					$this->objPHPExcel->getActiveSheet()->mergeCells( $ColonneCourante.$Ligne_Courante . ':' . $ColonneCourante.$Derniere_Ligne );

					$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante.$Ligne_Courante . ':' . $ColonneCourante.$Derniere_Ligne )
						->applyFromArray( $this->styleTextAGauche );

					$Derniere_Colonne += 1;

					// Fusion des éléments de la colonne : Mesure = Date Début.
					$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );

					$this->objPHPExcel->getActiveSheet()->mergeCells( $ColonneCourante.$Ligne_Courante . ':' . $ColonneCourante.$Derniere_Ligne );

					$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante.$Ligne_Courante . ':' . $ColonneCourante.$Derniere_Ligne )
						->applyFromArray( $this->styleTextAGauche );

					$Derniere_Colonne += 1;

					// Fusion des éléments de la colonne : Mesure = Date Fin.
					$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );

					$this->objPHPExcel->getActiveSheet()->mergeCells( $ColonneCourante.$Ligne_Courante . ':' . $ColonneCourante.$Derniere_Ligne );

					$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante.$Ligne_Courante . ':' . $ColonneCourante.$Derniere_Ligne )
						->applyFromArray( $this->styleTextAGauche );

					$Derniere_Colonne += 1;

					// Fusion des éléments de la colonne : Mesure = Fréquence.
					$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );

					$this->objPHPExcel->getActiveSheet()->mergeCells( $ColonneCourante.$Ligne_Courante . ':' . $ColonneCourante.$Derniere_Ligne );

					$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante.$Ligne_Courante . ':' . $ColonneCourante.$Derniere_Ligne )
						->applyFromArray( $this->styleTextAGauche );

					$Derniere_Colonne += 1;

					// Fusion des éléments de la colonne : Mesure = Statut.
					$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );

					$this->objPHPExcel->getActiveSheet()->mergeCells( $ColonneCourante.$Ligne_Courante . ':' . $ColonneCourante.$Derniere_Ligne );

					$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante.$Ligne_Courante . ':' . $ColonneCourante.$Derniere_Ligne )
						->applyFromArray( $this->styleTextAGauche );


					$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneActionDebut.$Ligne_Courante . ':' . $ColonneCourante.$Derniere_Ligne )
						->applyFromArray( $this->styleBordureNoirAutourGrisInterieur );

					$Ligne_Fin_Mesure = $Derniere_Ligne;
				} else {
					if ( $Total_Actions < 2 ) {
						if ( $Total_EVR > 0 ) {
							$ColonneMesureDebut = $this->xlColumnValue( 7 );
							$ColonneMesureFin = $this->xlColumnValue( 8 );

							$ColonneActionDebut = $this->xlColumnValue( 9 );
							$ColonneActionFin = $this->xlColumnValue( 14 );
						} else {
							$ColonneMesureDebut = $this->xlColumnValue( 6 );
							$ColonneMesureFin = $this->xlColumnValue( 7 );

							$ColonneActionDebut = $this->xlColumnValue( 8 );
							$ColonneActionFin = $this->xlColumnValue( 13 );
						}

						// Bordure autour de la zone : Mesure
						$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneMesureDebut . $Ligne_Debut_Mesure . ':' .
							$ColonneMesureFin . $Ligne_Debut_Mesure )->applyFromArray( $this->styleBordureNoirAutourGrisInterieur );

						// Bordure autour de la zone : Action
						$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneActionDebut . $Ligne_Debut_Mesure . ':' .
							$ColonneActionFin . $Ligne_Debut_Mesure )->applyFromArray( $this->styleBordureNoirAutourGrisInterieur );

					} else { // Cas où il a plusieurs Actions.
						if ( $Total_EVR > 0 ) {
							$Colonne = 7;
						} else {
							$Colonne = 6;
						}

						// ------------------------------------------------
						// Fusion des éléments des sous-colonnes : Mesure.

						// Fusionne et aligne la cellule : Libellé.
						$ColonneCourante = $this->xlColumnValue( $Colonne );

						if ( $Total_Actions > 1 ) {
							$this->objPHPExcel->getActiveSheet()->mergeCells( $ColonneCourante . $Ligne_Debut_Mesure . ':' .
								$ColonneCourante . $Ligne_Fin_Mesure );
						}

						$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante . $Ligne_Debut_Mesure . ':' .
							$ColonneCourante . $Ligne_Fin_Mesure )->applyFromArray( $this->styleTextAGauche );

						// Fusionne et aligne la cellule : Statut.
						$Colonne += 1;
						$ColonneSuivante = $this->xlColumnValue( $Colonne );

						if ( $Total_Actions > 1 ) {
							$this->objPHPExcel->getActiveSheet()->mergeCells( $ColonneSuivante . $Ligne_Debut_Mesure . ':' .
								$ColonneSuivante . $Ligne_Fin_Mesure );
						}

						$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneSuivante.$Ligne_Debut_Mesure . ':' . $ColonneSuivante.$Ligne_Fin_Mesure )
							->applyFromArray( $this->styleTextAGauche );

						// Met une bordure autour des cellules : Libellé/Statut.
						$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante.$Ligne_Debut_Mesure . ':' . $ColonneSuivante.$Ligne_Fin_Mesure )
							->applyFromArray( $this->styleBordureNoirAutourGrisInterieur );

					}

				}

				$Ligne_Debut_Mesure += 1;


			} // Fin boucle : Mesures


			// =============================================================
			// Met en forme toutes les informations rattachées à un risque.


			if ( $Ligne_Fin_Mesure > $Ligne_Debut_Mesure ) $Ligne_Debut_Mesure = $Ligne_Fin_Mesure;

			if ( $Derniere_Ligne < $Ligne_Debut_Mesure ) $Derniere_Ligne = $Ligne_Debut_Mesure;

			if ( $Total_Mesures > 1 ) $Derniere_Ligne -=1; // Réajustement

			// Affichage de la bordure autour de la Menace et des Vulnérabilités.
			if ( ($Derniere_Ligne - $Ligne_Courante) > 1 ) {
				// Fusionne la cellule : Vulnérabilités.
				$this->objPHPExcel->getActiveSheet()->mergeCells( 'A'.$Ligne_Suivante . ':' . 'A'.$Derniere_Ligne );
			}

			$this->objPHPExcel->getActiveSheet()->getStyle( 'A'.$Ligne_Courante . ':' . 'A'.$Derniere_Ligne )
				->applyFromArray( $this->styleBordureNoirAutourGrisInterieur );

			$this->objPHPExcel->getActiveSheet()->getStyle( 'A'.$Ligne_Courante )
				->applyFromArray( $this->styleTextAGauche );

			$this->objPHPExcel->getActiveSheet()->getStyle( 'A' . $Ligne_Courante )->getAlignment()->setWrapText( TRUE );

			$this->objPHPExcel->getActiveSheet()->getStyle( 'A' . $Ligne_Suivante )->getAlignment()->setWrapText( TRUE );

			$this->objPHPExcel->getActiveSheet()->getStyle( 'A'.$Ligne_Suivante )
				->applyFromArray( $this->styleTextADroite );


			// Fusionne, aligne et met une bordure autour de la cellule : Code du Risque.
			$this->objPHPExcel->getActiveSheet()->mergeCells( 'B'.$Ligne_Courante . ':' . 'B'.$Derniere_Ligne );

			$this->objPHPExcel->getActiveSheet()->getStyle( 'B'.$Ligne_Courante . ':' . 'B'.$Derniere_Ligne )
				->applyFromArray( $this->styleTextAGauche );

			$this->objPHPExcel->getActiveSheet()->getStyle( 'B' . $Ligne_Courante )->getAlignment()->setWrapText( TRUE );

			$this->objPHPExcel->getActiveSheet()->getStyle( 'B'.$Ligne_Courante . ':' . 'B'.$Derniere_Ligne )
				->applyFromArray( $this->styleBordureNoirAutour );


			// Fusionne, aligne et met une bordure autour de la cellule : Scénario du Risque.
			$this->objPHPExcel->getActiveSheet()->mergeCells( 'C'.$Ligne_Courante . ':' . 'C'.$Derniere_Ligne );

			$this->objPHPExcel->getActiveSheet()->getStyle( 'C'.$Ligne_Courante . ':' . 'C'.$Derniere_Ligne )
				->applyFromArray( $this->styleTextAGauche );

			$this->objPHPExcel->getActiveSheet()->getStyle( 'C' . $Ligne_Courante )->getAlignment()->setWrapText( TRUE );

			$this->objPHPExcel->getActiveSheet()->getStyle( 'C'.$Ligne_Courante . ':' . 'C'.$Derniere_Ligne )
				->applyFromArray( $this->styleBordureNoirAutour );


			// Fusionne, aligne et met une bordure autour de la cellule : Actif support.
			$this->objPHPExcel->getActiveSheet()->mergeCells( 'D'.$Ligne_Courante . ':' . 'D'.$Derniere_Ligne );

			$this->objPHPExcel->getActiveSheet()->getStyle( 'D'.$Ligne_Courante . ':' . 'D'.$Derniere_Ligne )
				->applyFromArray( $this->styleTextAGauche );

			$this->objPHPExcel->getActiveSheet()->getStyle( 'D' . $Ligne_Courante )->getAlignment()->setWrapText( TRUE );

			$this->objPHPExcel->getActiveSheet()->getStyle( 'D'.$Ligne_Courante . ':' . 'D'.$Derniere_Ligne )
				->applyFromArray( $this->styleBordureNoirAutour );


			// Fusionne, aligne et met une bordure autour de la cellule : Actifs primordiaux.
			$this->objPHPExcel->getActiveSheet()->mergeCells( 'E'.$Ligne_Courante . ':' . 'E'.$Derniere_Ligne );

			$this->objPHPExcel->getActiveSheet()->getStyle( 'E'.$Ligne_Courante . ':' . 'E'.$Derniere_Ligne )
				->applyFromArray( $this->styleTextAGauche );

			$this->objPHPExcel->getActiveSheet()->getStyle( 'E' . $Ligne_Courante )->getAlignment()->setWrapText( TRUE );

			$this->objPHPExcel->getActiveSheet()->getStyle( 'E'.$Ligne_Courante . ':' . 'E'.$Derniere_Ligne )
				->applyFromArray( $this->styleBordureNoirAutour );


			// Fusionne, aligne et met une bordure autour de la cellule : Evénement Redouté du Risque.
			$Derniere_Colonne = 6;

			if ( $Total_EVR > 0 ) {
				$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );
				$this->objPHPExcel->getActiveSheet()->mergeCells( $ColonneCourante.$Ligne_Courante . ':' . $ColonneCourante.$Derniere_Ligne );

				$ColonneCourante = $this->xlColumnValue( $Derniere_Colonne );
				$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante.$Ligne_Courante . ':' . $ColonneCourante.$Derniere_Ligne )
					->applyFromArray( $this->styleTextAGauche );

				$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante.$Ligne_Courante )->getAlignment()->setWrapText( TRUE );

				$this->objPHPExcel->getActiveSheet()->getStyle( $ColonneCourante.$Ligne_Courante . ':' . $ColonneCourante.$Derniere_Ligne )
					->applyFromArray( $this->styleBordureNoirAutour );

				$Derniere_Colonne += 1;

				$Derniere_Colonne_Occ = 'N';
			} else {
				$Derniere_Colonne_Occ = 'M';
			}


			// Gère la surbrillance de l'occurrence de Risque
			if ( $Traceur != $Ligne_Courante.'-'.$Derniere_Ligne ) {
				if ( $Couleur_Fond == FALSE ) {
					$styleSpecial = array_merge( $this->styleBordureNoirAutour, $this->styleSurligne );
					$Couleur_Fond = TRUE;
				} else {
					$styleSpecial = $this->styleBordureNoirAutour;
					$Couleur_Fond = FALSE;
				}

				$this->objPHPExcel->getActiveSheet()
					->getStyle( 'A' . $Ligne_Courante . ':' . $Derniere_Colonne_Occ . $Derniere_Ligne )
					->applyFromArray( $styleSpecial );

				$Traceur = $Ligne_Courante.'-'.$Derniere_Ligne;
			}

			$Derniere_Ligne += 1;
			$Ligne_Courante = $Derniere_Ligne; // Change de ligne.
			$Ligne_Debut_Mesure = $Ligne_Courante;

		} // Fin boucle : Risques

		return TRUE;
	}


	/* ================================================================================== */

	public function editionActionsLimites( $crs_id ) {
	/**
	* Edite les Actions associées à une Cartographie (en revanche pas d'inforation sur les Risques).
	*
	* @author Pierre-Luc MARY
	* @date 2017-05-15
	*
	* @param[in] $crs_id ID de la Cartographie à utiliser
	*
	* @return Renvoi vrai si les informations ont bien été éditées.
	*			
	*/
		include_once( DIR_LIBRAIRIES . '/Class_HTML.inc.php' );
		include_once( DIR_LIBRAIRIES . '/Class_CartographiesRisques_PDO.inc.php' );

		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_libelles_generiques.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_HBL_Generiques.inc.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-AppreciationRisques.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-TraitementRisques.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-Actions.php' );


		$objCartographie = new CartographiesRisques();


		$Titre_Onglet = ucfirst($L_Actions);

		// Création d'un nouvel onglet et lui donne un nom.
		$this->objPHPExcel->createSheet()->setTitle( $Titre_Onglet );

		// Mise en page de l'onglet.
		$this->objPHPExcel->setActiveSheetIndexByName( $Titre_Onglet )->getPageSetup()
			->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$this->objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

		// Répète sur chaque page les lignes 1 à 4.
		$this->objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 4);

		// Initialise le haut et le bas de page de l'onglet courant.
		$this->initialiseHautBasOnglet( $Titre_Onglet );


		// ==========================================================
		// Vérifie si la Cartographie dispose d'Evénements Redoutés.
		$requete = $this->prepareSQL(
			'SELECT count(evr_id) AS total FROM evr_evenements_redoutes AS "evr" WHERE crs_id = :crs_id '
			);
			
		$Total_EVR = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->executeSQL($requete)
			->fetchObject()->total;


		// ================================================
		// Construit la partie fixe du tableau (l'entête).

		// -------------------
		// Colonne => Risques

		// Titre : Risques.
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'A1', ucfirst( $L_Risques ) );
		$this->objPHPExcel->getActiveSheet()->mergeCells( 'A1:C1' );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'A1:C1' )->applyFromArray(
			array_merge( $this->styleTextToutCentre, $this->styleBordureNoirAutour, $this->styleTitrePrincipalHorizontal ) );

		// Sous-Titre : Code.
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'A2', $L_Code );

		// Sous-Titre : Actif Support.
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'B2', $L_Actif_Support );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth( 25 );
		$this->objPHPExcel->getActiveSheet()->getStyle('B2')->getAlignment()->setWrapText( TRUE );

		// Sous-Titre : Actifs Primordiaux.
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'C2', $L_Actifs_Primordiaux );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth( 25 );
		$this->objPHPExcel->getActiveSheet()->getStyle('C2')->getAlignment()->setWrapText( TRUE );

		// Bordure autour de la zone "Risque"
		$this->objPHPExcel->getActiveSheet()->getStyle( 'A2:C2' )->applyFromArray(
			array_merge( $this->styleTitreNomHorizontal, $this->styleTextToutCentre, $this->styleBordureNoirAutourGrisInterieur ) );


		// -------------------
		// Colonne => Mesures

		// Titre : Mesures
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'D1', $L_Mesures );

		$this->objPHPExcel->getActiveSheet()->mergeCells( 'D1:E1' );

		if ( $Total_EVR > 0 ) {
			$this->objPHPExcel->getActiveSheet()->getStyle( 'D1:E1' )->applyFromArray(
				array_merge( $this->styleTitrePrincipalHorizontal, $this->styleBordureNoirAutour, $this->styleTextToutCentre ) );
		} else {
			$this->objPHPExcel->getActiveSheet()->getStyle( 'D1:E1' )->applyFromArray(
				array_merge( $this->styleTitrePrincipalHorizontalSpecial, $this->styleBordureNoirAutour, $this->styleTextToutCentre ) );
		}

		$this->objPHPExcel->getActiveSheet()->getStyle( 'D1:E1' )->getAlignment()->setWrapText( TRUE );

		// Titre : Libelle
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'D2', $L_Libelle );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension( 'D' )->setWidth( 50 );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'D2' )->getAlignment()->setWrapText( TRUE );

		// Titre : Statut
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'E2', $L_Status );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension( 'E' )->setWidth( 15 );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'E2' )->getAlignment()->setWrapText( TRUE );

		// Bordure autour de la zone "Mesure"
		$this->objPHPExcel->getActiveSheet()->getStyle( 'D2:E2' )->applyFromArray(
			array_merge( $this->styleTitreNomHorizontal, $this->styleTextToutCentre, $this->styleBordureNoirAutourGrisInterieur ) );


		// -------------------
		// Colonne => Actions

		// Titre : Actions.
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'F1', ucfirst( $L_Actions ) );

		$this->objPHPExcel->getActiveSheet()->mergeCells( 'F1:K1' );

		if ( $Total_EVR > 0 ) {
			$this->objPHPExcel->getActiveSheet()->getStyle( 'F1:K1' )->applyFromArray(
				array_merge( $this->styleTitrePrincipalHorizontalSpecial, $this->styleBordureNoirAutour, $this->styleTextToutCentre ) );
		} else {
			$this->objPHPExcel->getActiveSheet()->getStyle( 'F1:K1' )->applyFromArray(
				array_merge( $this->styleTitrePrincipalHorizontal, $this->styleBordureNoirAutour, $this->styleTextToutCentre ) );
		}

		// Titre : Libellé
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'F2', $L_Libelle );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension( 'F' )->setWidth( 50 );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'F2' )->getAlignment()->setWrapText( TRUE );

		// Titre : Acteur
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'G2', $L_Acteur );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension( 'G' )->setWidth( 15 );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'G2' )->getAlignment()->setWrapText( TRUE );

		// Titre : Date début
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'H2', $L_Date_Debut );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension( 'H' )->setWidth( 10 );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'H2' )->getAlignment()->setWrapText( TRUE );

		// Titre : Date fin
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'I2', $L_Date_Fin );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension( 'I' )->setWidth( 10 );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'I2' )->getAlignment()->setWrapText( TRUE );

		// Titre : Fréquence
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'J2', $L_Frequence );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension( 'J' )->setWidth( 15 );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'J2' )->getAlignment()->setWrapText( TRUE );

		// Titre : Statut
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'K2', $L_Status );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension( 'K' )->setWidth( 15 );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'K2' )->getAlignment()->setWrapText( TRUE );

		// Bordure autour de la zone "Action"
		$this->objPHPExcel->getActiveSheet()->getStyle( 'F2:K2' )->applyFromArray(
			array_merge( $this->styleTitreNomHorizontal, $this->styleTextToutCentre, $this->styleBordureNoirAutour ) );


		// *********************************************************************

		// =====================================================================
		// Construit la liste des Risques, des Mesures et des Actions associés.
		if ( $Total_EVR > 0 ) {
			$sql = 'SELECT
rcs.rcs_id,
mgn.mgn_code, lbr_mgn.lbr_libelle AS "mgn_libelle",
rcs.rcs_code, rcs.rcs_scenario,
spp.spp_code, spp.spp_nom,
string_agg( DISTINCT \' - \'||apr_nom, \'\n\' )  AS "apr_noms",
string_agg( DISTINCT evr_libelle, \'\n\' ) AS evr_libelles
FROM rcs_risques_cartographies AS "rcs"
LEFT JOIN rcev_rcs_evr AS "rcev" ON rcev.rcs_id = rcs.rcs_id
LEFT JOIN evr_evenements_redoutes AS "evr" ON evr.evr_id = rcev.evr_id
LEFT JOIN evap_evr_apr AS "evap" ON evap.evr_id = evr.evr_id
LEFT JOIN apr_actifs_primordiaux AS "apr" ON apr.apr_id = evap.apr_id
LEFT JOIN spp_supports AS "spp" ON spp.spp_id = rcs.spp_id
LEFT JOIN mgn_menaces_generiques AS "mgn" ON mgn.mgn_id = rcs.mgn_id
LEFT JOIN lbr_libelles_referentiel AS "lbr_mgn" ON lbr_mgn.lbr_code = mgn.mgn_code AND lbr_mgn.lng_id = :langue
LEFT JOIN mcr_mesures_cartographies AS "mcr" ON mcr.rcs_id = rcs.rcs_id
WHERE evr.crs_id = :crs_id
AND rcs_scenario != \'\'
AND rcs_etat != :etat_ignore
AND mcr.mcr_id IS NOT NULL
GROUP BY rcs.rcs_id, mgn.mgn_code, mgn_libelle, rcs.rcs_code, rcs.rcs_scenario, spp.spp_code, spp.spp_nom
ORDER BY rcs.rcs_id, mgn_libelle, rcs.rcs_scenario, spp.spp_nom ';
		} else {

			$sql = 'SELECT
rcs.rcs_id,
mgn.mgn_code, lbr_mgn.lbr_libelle AS "mgn_libelle",
rcs.rcs_code, rcs.rcs_scenario,
spp.spp_code, spp.spp_nom,
string_agg( DISTINCT \' - \'||apr_nom, \'\n\' )  AS "apr_noms"
FROM rcs_risques_cartographies AS "rcs"
LEFT JOIN spp_supports AS "spp" ON spp.spp_id = rcs.spp_id
LEFT JOIN apsp_apr_spp AS "apsp" ON apsp.spp_id = spp.spp_id
LEFT JOIN apr_actifs_primordiaux AS "apr" ON apr.apr_id = apsp.apr_id
LEFT JOIN mgn_menaces_generiques AS "mgn" ON mgn.mgn_id = rcs.mgn_id
LEFT JOIN lbr_libelles_referentiel AS "lbr_mgn" ON lbr_mgn.lbr_code = mgn.mgn_code AND lbr_mgn.lng_id = :langue
LEFT JOIN mcr_mesures_cartographies AS "mcr" ON mcr.rcs_id = rcs.rcs_id
LEFT JOIN spcr_spp_crs AS "spcr" ON spcr.spp_id = spp.spp_id 
WHERE spcr.crs_id = :crs_id
AND rcs_scenario != \'\'
AND rcs_etat != :etat_ignore
AND mcr.mcr_id IS NOT NULL
GROUP BY rcs.rcs_id, mgn.mgn_code, mgn_libelle, rcs.rcs_code, rcs.rcs_scenario, spp.spp_code, spp.spp_nom
ORDER BY rcs.rcs_id, mgn_libelle, rcs.rcs_scenario, spp.spp_nom ';
		}

		$requete = $this->prepareSQL( $sql );
			
		$Liste_Risques = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->bindSQL($requete, ':langue', $this->Langue, self::LANGUE_TYPE, self::LANGUE_LENGTH)
			->bindSQL($requete, ':etat_ignore', self::ETAT_IGNORE, self::LANGUE_TYPE, self::LANGUE_LENGTH)
			->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);


		// =========================================================================================
		// Edite le détail de l'ensemble des Risques, des Mesures et des Actions (corps du tableau.

		$Ligne_Courante = 3; // Ammorce la ligne courante
		$Couleur_Fond = FALSE;
		$Traceur = '';

		$Ligne_Debut_Mesure = $Ligne_Courante;
		$Ligne_Fin_Mesure = $Ligne_Courante + 1;

		foreach ($Liste_Risques as $Occurrence) {
			//$Ligne_Suivante = $Ligne_Courante + 1;
			$Derniere_Ligne = $Ligne_Courante; //$Ligne_Suivante;


			// Valeur : Code du risque.
			$this->objPHPExcel->getActiveSheet()->setCellValue( 'A'.$Ligne_Courante, 'R'.$Occurrence->rcs_code );


			// Valeur : Actif support.
			$this->objPHPExcel->getActiveSheet()->setCellValue( 'B'.$Ligne_Courante, $Occurrence->spp_nom );


			// Valeur : Actifs primordiaux.
			$this->objPHPExcel->getActiveSheet()->setCellValue( 'C'.$Ligne_Courante, str_replace('\n', "\n", $Occurrence->apr_noms ) );


			// --------------------------------------------------
			// Valeur : Liste des Mesures attachées à ce Risque.
			$requete = $this->prepareSQL(
				'SELECT ' .
				'mcr.mcr_id, ' .
				'lbr.lbr_libelle AS "mgr_libelle", ' .
				'lbr1.lbr_libelle AS "mgr_etat_libelle" ' .
				'FROM mcr_mesures_cartographies AS "mcr" ' .
				'LEFT JOIN mgr_mesures_generiques AS "mgr" ON mgr.mgr_id = mcr.mgr_id ' .
				'LEFT JOIN lbr_libelles_referentiel AS "lbr" ON lbr.lbr_code = mgr_code AND lbr.lng_id = :langue ' .
				'LEFT JOIN lbr_libelles_referentiel AS "lbr1" ON lbr1.lbr_code = \'MCR_ETAT_\'||mcr_etat_code AND lbr1.lng_id = :langue ' .
				'WHERE mcr.rcs_id = :rcs_id '
			);
			
			$Liste_Mesures = $this->bindSQL($requete, ':rcs_id', $Occurrence->rcs_id, self::ID_TYPE)
				->bindSQL($requete, ':langue', $this->Langue, self::LANGUE_TYPE, self::LANGUE_LENGTH)
				->executeSQL($requete)
				->fetchAll(PDO::FETCH_CLASS);


			$Total_Mesures = count( $Liste_Mesures );


			foreach ( $Liste_Mesures as $Mesure ) {
				// Affecte et cadre le Libellé de la Mesure.
				$this->objPHPExcel->getActiveSheet()->setCellValue( 'D'.$Ligne_Debut_Mesure, $Mesure->mgr_libelle );
				$this->objPHPExcel->getActiveSheet()->getStyle( 'D'.$Ligne_Debut_Mesure )->getAlignment()->setWrapText( TRUE );
				$this->objPHPExcel->getActiveSheet()->getStyle( 'D'.$Ligne_Debut_Mesure )
					->applyFromArray( $this->styleTextAGauche );

				// Affecte et cadre le Statut de la Mesure.
				$this->objPHPExcel->getActiveSheet()->setCellValue( 'E'.$Ligne_Debut_Mesure, $Mesure->mgr_etat_libelle );
				$this->objPHPExcel->getActiveSheet()->getStyle( 'E'.$Ligne_Debut_Mesure )->getAlignment()->setWrapText( TRUE );
				$this->objPHPExcel->getActiveSheet()->getStyle( 'E'.$Ligne_Debut_Mesure )
					->applyFromArray( $this->styleTextAGauche );


				// --------------------------------------------------
				// Valeur : Liste des Actions attachées à cette Mesure.
				$sql = 'SELECT
				act_libelle,
				idn.idn_login||\' - \'||cvl_prenom||\' \'||cvl_nom AS "acteur",
				act_date_debut_p,
				act_date_debut_r,
				act_date_fin_p,
				act_date_fin_r,
				lbr_act_frequence.lbr_libelle AS "act_frequence_libelle",
				lbr_act_statut.lbr_libelle AS "act_statut_libelle"

				FROM act_actions AS "act"
				LEFT JOIN idn_identites AS "idn" ON idn.idn_id = act.idn_id
				LEFT JOIN cvl_civilites AS "cvl" ON cvl.cvl_id = idn.cvl_id
				LEFT JOIN lbr_libelles_referentiel AS "lbr_act_frequence" ON lbr_act_frequence.lbr_code = act.act_frequence_code AND lbr_act_frequence.lng_id = :langue
				LEFT JOIN lbr_libelles_referentiel AS "lbr_act_statut" ON lbr_act_statut.lbr_code = act.act_statut_code AND lbr_act_statut.lng_id = :langue

				WHERE act.mcr_id = :mcr_id

				ORDER BY act_libelle ';

				$requete = $this->prepareSQL( $sql );
					
				$Liste_Actions = $this->bindSQL($requete, ':mcr_id', $Mesure->mcr_id, self::ID_TYPE)
					->bindSQL($requete, ':langue', $this->Langue, self::LANGUE_TYPE, self::LANGUE_LENGTH)
					->executeSQL($requete)
					->fetchAll(PDO::FETCH_CLASS);

				$Ligne_Action = $Ligne_Debut_Mesure;

				foreach ( $Liste_Actions as $Action ) {
					// Décale la ligne de fin de Mesure au fur et à mesure des Actions à afficher.
					$Ligne_Fin_Mesure = $Ligne_Action;

					// Affecte et cadre le Libellé de l'Action.
					$this->objPHPExcel->getActiveSheet()->setCellValue( 'F'.$Ligne_Action, $Action->act_libelle );
					$this->objPHPExcel->getActiveSheet()->getStyle( 'F'.$Ligne_Action )->getAlignment()->setWrapText( TRUE );
					$this->objPHPExcel->getActiveSheet()->getStyle( 'F'.$Ligne_Action )
						->applyFromArray( $this->styleTextAGauche );

					// Affecte et cadre l'Acteur de l'Action.
					$this->objPHPExcel->getActiveSheet()->setCellValue( 'G'.$Ligne_Action, $Action->acteur );
					$this->objPHPExcel->getActiveSheet()->getStyle( 'G'.$Ligne_Action )->getAlignment()->setWrapText( TRUE );
					$this->objPHPExcel->getActiveSheet()->getStyle( 'G'.$Ligne_Action )
						->applyFromArray( $this->styleTextAGauche );

					// Affecte et cadre la Date de Début de l'Action.
					if ( $Action->act_date_debut_r != '' ) $Date_Debut = $Action->act_date_debut_r;
					else $Date_Debut = $Action->act_date_debut_p;

					$this->objPHPExcel->getActiveSheet()->setCellValue( 'H'.$Ligne_Action, $Date_Debut );
					$this->objPHPExcel->getActiveSheet()->getStyle( 'H'.$Ligne_Action )->getAlignment()->setWrapText( TRUE );
					$this->objPHPExcel->getActiveSheet()->getStyle( 'H'.$Ligne_Action )
						->applyFromArray( $this->styleTextAGauche );

					// Affecte et cadre la Date de Fin de l'Action.
					if ( $Action->act_date_fin_r != '' ) $Date_Fin = $Action->act_date_fin_r;
					else $Date_Fin = $Action->act_date_fin_p;

					$this->objPHPExcel->getActiveSheet()->setCellValue( 'I'.$Ligne_Action, $Date_Fin );
					$this->objPHPExcel->getActiveSheet()->getStyle( 'I'.$Ligne_Action )->getAlignment()->setWrapText( TRUE );
					$this->objPHPExcel->getActiveSheet()->getStyle( 'I'.$Ligne_Action )
						->applyFromArray( $this->styleTextAGauche );

					// Affecte et cadre la Fréquence de l'Action.
					$this->objPHPExcel->getActiveSheet()->setCellValue( 'J'.$Ligne_Action, $Action->act_frequence_libelle );
					$this->objPHPExcel->getActiveSheet()->getStyle( 'J'.$Ligne_Action )->getAlignment()->setWrapText( TRUE );
					$this->objPHPExcel->getActiveSheet()->getStyle( 'J'.$Ligne_Action )
						->applyFromArray( $this->styleTextAGauche );

					// Affecte et cadre le Statut de l'Action.
					$this->objPHPExcel->getActiveSheet()->setCellValue( 'K'.$Ligne_Action, $Action->act_statut_libelle );
					$this->objPHPExcel->getActiveSheet()->getStyle( 'K'.$Ligne_Action )->getAlignment()->setWrapText( TRUE );
					$this->objPHPExcel->getActiveSheet()->getStyle( 'K'.$Ligne_Action )
						->applyFromArray( $this->styleTextAGauche );


					// Dessine un cadre autour de la zone : Action
					$this->objPHPExcel->getActiveSheet()->getStyle( 'F'.$Ligne_Action.':K'.$Ligne_Action )
						->applyFromArray( $this->styleBordureNoirAutourGrisInterieur );

					// Passe à la ligne d'action suivante.
					$Ligne_Action += 1;

				} // Fin boucle : Actions


				$Total_Actions = count( $Liste_Actions );


				// Cas d'une occurrence de Risque ou il n'y a entre 0 et 1 Mesure et 0 et 1 Action.
				if ( $Total_Actions < 2 && $Total_Mesures < 2 ) {
					// ------------------------------------------------
					// Aligne les éléments des sous-colonnes : Mesure.

					// Aligne la cellule : Libellé.
					$this->objPHPExcel->getActiveSheet()->getStyle( 'D'.$Ligne_Courante )
						->applyFromArray( $this->styleTextAGauche );

					// Fusionne et aligne la cellule : Statut.
					$this->objPHPExcel->getActiveSheet()->getStyle( 'E'.$Ligne_Courante )
						->applyFromArray( $this->styleTextAGauche );

					// Met une bordure autour des cellules : Libellé/Statut.
					$this->objPHPExcel->getActiveSheet()->getStyle( 'D'.$Ligne_Courante . ':' . 'E'.$Ligne_Courante )
						->applyFromArray( $this->styleBordureNoirAutourGrisInterieur );


					// ------------------------------------------------
					// Fusion des éléments des sous-colonnes : Action.

					// Fusion des éléments de la colonne : Libellé.
					$this->objPHPExcel->getActiveSheet()->mergeCells( 'F'.$Ligne_Courante . ':' . 'F'.$Derniere_Ligne );

					$this->objPHPExcel->getActiveSheet()->getStyle( 'F'.$Ligne_Courante . ':' . 'F'.$Derniere_Ligne )
						->applyFromArray( $this->styleTextAGauche );

					// Fusion des éléments de la colonne : Acteur.
					$this->objPHPExcel->getActiveSheet()->mergeCells( 'G'.$Ligne_Courante . ':' . 'G'.$Derniere_Ligne );

					$this->objPHPExcel->getActiveSheet()->getStyle( 'G'.$Ligne_Courante . ':' . 'G'.$Derniere_Ligne )
						->applyFromArray( $this->styleTextAGauche );

					// Fusion des éléments de la colonne : Date Début.
					$this->objPHPExcel->getActiveSheet()->mergeCells( 'H'.$Ligne_Courante . ':' . 'H'.$Derniere_Ligne );

					$this->objPHPExcel->getActiveSheet()->getStyle( 'H'.$Ligne_Courante . ':' . 'H'.$Derniere_Ligne )
						->applyFromArray( $this->styleTextAGauche );

					// Fusion des éléments de la colonne : Date Fin.
					$this->objPHPExcel->getActiveSheet()->mergeCells( 'I'.$Ligne_Courante . ':' . 'I'.$Derniere_Ligne );

					$this->objPHPExcel->getActiveSheet()->getStyle( 'I'.$Ligne_Courante . ':' . 'I'.$Derniere_Ligne )
						->applyFromArray( $this->styleTextAGauche );

					// Fusion des éléments de la colonne : Fréquence.
					$this->objPHPExcel->getActiveSheet()->mergeCells( 'J'.$Ligne_Courante . ':' . 'J'.$Derniere_Ligne );

					$this->objPHPExcel->getActiveSheet()->getStyle( 'J'.$Ligne_Courante . ':' . 'J'.$Derniere_Ligne )
						->applyFromArray( $this->styleTextAGauche );

					// Fusion des éléments de la colonne : Statut.
					$this->objPHPExcel->getActiveSheet()->mergeCells( 'K'.$Ligne_Courante . ':' . 'K'.$Derniere_Ligne );

					$this->objPHPExcel->getActiveSheet()->getStyle( 'K'.$Ligne_Courante . ':' . 'K'.$Derniere_Ligne )
						->applyFromArray( $this->styleTextAGauche );


					$this->objPHPExcel->getActiveSheet()->getStyle( 'F'.$Ligne_Courante . ':' . 'K'.$Derniere_Ligne )
						->applyFromArray( $this->styleBordureNoirAutourGrisInterieur );


					$Ligne_Fin_Mesure = $Derniere_Ligne;
				} else {
					if ( $Total_Actions < 2 ) {
						// Bordure autour de la zone : Mesure
						$this->objPHPExcel->getActiveSheet()
							->getStyle( 'D'.$Ligne_Debut_Mesure . ':' .	'E'.$Ligne_Debut_Mesure )
							->applyFromArray( $this->styleBordureNoirAutourGrisInterieur );

						// Bordure autour de la zone : Action
						$this->objPHPExcel->getActiveSheet()
							->getStyle( 'F'.$Ligne_Debut_Mesure . ':' . 'K'.$Ligne_Debut_Mesure )
							->applyFromArray( $this->styleBordureNoirAutourGrisInterieur );

					} else { // Cas où il a plusieurs Actions.
						// ------------------------------------------------
						// Fusion des éléments des sous-colonnes : Mesure.

						// Fusionne et aligne la cellule : Libellé.
						if ( $Total_Actions > 1 ) {
							$this->objPHPExcel->getActiveSheet()->mergeCells( 'D'.$Ligne_Debut_Mesure . ':' . 'D'.$Ligne_Fin_Mesure );
						}

						$this->objPHPExcel->getActiveSheet()->getStyle( 'D'.$Ligne_Debut_Mesure . ':' . 'D'.$Ligne_Fin_Mesure )
							->applyFromArray( $this->styleTextAGauche );

						// Fusionne et aligne la cellule : Statut.
						if ( $Total_Actions > 1 ) {
							$this->objPHPExcel->getActiveSheet()->mergeCells( 'E'.$Ligne_Debut_Mesure . ':' . 'E'.$Ligne_Fin_Mesure );
						}

						$this->objPHPExcel->getActiveSheet()->getStyle( 'E'.$Ligne_Debut_Mesure . ':' . 'E'.$Ligne_Fin_Mesure )
							->applyFromArray( $this->styleTextAGauche );

						// Met une bordure autour des cellules : Libellé/Statut.
						$this->objPHPExcel->getActiveSheet()->getStyle( 'D'.$Ligne_Debut_Mesure . ':' . 'E'.$Ligne_Fin_Mesure )
							->applyFromArray( $this->styleBordureNoirAutourGrisInterieur );

					}

				}

				$Ligne_Debut_Mesure += 1;


			} // Fin boucle : Mesures


			// =============================================================
			// Met en forme toutes les informations rattachées à un risque.
			if ( $Ligne_Fin_Mesure > $Ligne_Debut_Mesure ) $Ligne_Debut_Mesure = $Ligne_Fin_Mesure;

			if ( $Derniere_Ligne < $Ligne_Debut_Mesure ) $Derniere_Ligne = $Ligne_Debut_Mesure;

			if ( $Total_Mesures > 1 or $Total_Actions < 2 ) $Derniere_Ligne -=1; // Réajustement


			// Fusionne, aligne et met une bordure autour de la cellule : Code du Risque.
			$this->objPHPExcel->getActiveSheet()->mergeCells( 'A'.$Ligne_Courante . ':' . 'A'.$Derniere_Ligne );

			$this->objPHPExcel->getActiveSheet()->getStyle( 'A'.$Ligne_Courante . ':' . 'A'.$Derniere_Ligne )
				->applyFromArray( $this->styleTextAGauche );

			$this->objPHPExcel->getActiveSheet()->getStyle( 'A' . $Ligne_Courante )->getAlignment()->setWrapText( TRUE );

			$this->objPHPExcel->getActiveSheet()->getStyle( 'A'.$Ligne_Courante . ':' . 'A'.$Derniere_Ligne )
				->applyFromArray( $this->styleBordureNoirAutour );


			// Fusionne, aligne et met une bordure autour de la cellule : Actif support.
			$this->objPHPExcel->getActiveSheet()->mergeCells( 'B'.$Ligne_Courante . ':' . 'B'.$Derniere_Ligne );

			$this->objPHPExcel->getActiveSheet()->getStyle( 'B'.$Ligne_Courante . ':' . 'B'.$Derniere_Ligne )
				->applyFromArray( $this->styleTextAGauche );

			$this->objPHPExcel->getActiveSheet()->getStyle( 'B' . $Ligne_Courante )->getAlignment()->setWrapText( TRUE );

			$this->objPHPExcel->getActiveSheet()->getStyle( 'B'.$Ligne_Courante . ':' . 'B'.$Derniere_Ligne )
				->applyFromArray( $this->styleBordureNoirAutour );


			// Fusionne, aligne et met une bordure autour de la cellule : Actifs primordiaux.
			$this->objPHPExcel->getActiveSheet()->mergeCells( 'C'.$Ligne_Courante . ':' . 'C'.$Derniere_Ligne );

			$this->objPHPExcel->getActiveSheet()->getStyle( 'C'.$Ligne_Courante . ':' . 'C'.$Derniere_Ligne )
				->applyFromArray( $this->styleTextAGauche );

			$this->objPHPExcel->getActiveSheet()->getStyle( 'C' . $Ligne_Courante )->getAlignment()->setWrapText( TRUE );

			$this->objPHPExcel->getActiveSheet()->getStyle( 'C'.$Ligne_Courante . ':' . 'C'.$Derniere_Ligne )
				->applyFromArray( $this->styleBordureNoirAutour );


			// Gère la surbrillance de l'occurrence de Risque
			if ( $Traceur != $Ligne_Courante.'-'.$Derniere_Ligne ) {
				if ( $Couleur_Fond == FALSE ) {
					$styleSpecial = array_merge( $this->styleBordureNoirAutour, $this->styleSurligne );
					$Couleur_Fond = TRUE;
				} else {
					$styleSpecial = $this->styleBordureNoirAutour;
					$Couleur_Fond = FALSE;
				}

				$this->objPHPExcel->getActiveSheet()
					->getStyle( 'A' . $Ligne_Courante . ':' . 'K' . $Derniere_Ligne )
					->applyFromArray( $styleSpecial );

				$Traceur = $Ligne_Courante.'-'.$Derniere_Ligne;
			}

			$Derniere_Ligne += 1;
			$Ligne_Courante = $Derniere_Ligne; // Change de ligne.
			$Ligne_Debut_Mesure = $Ligne_Courante;

		} // Fin boucle : Risques

		return TRUE;
	}
	
	
	/* ================================================================================== */
	
	public function editionConformite( $crs_id ) {
		/**
		 * Edite les Mesures de Conformité associées à une Cartographie (en revanche pas d'inforation sur les Risques).
		 *
		 * @author Pierre-Luc MARY
		 * @date 2019-11-26
		 *
		 * @param[in] $crs_id ID de la Cartographie à utiliser
		 *
		 * @return Renvoi vrai si les informations ont bien été éditées.
		 *
		 */
		include_once( DIR_LIBRAIRIES . '/Class_HTML.inc.php' );
		include_once( DIR_LIBRAIRIES . '/Class_CartographiesRisques_PDO.inc.php' );
		
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_libelles_generiques.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_HBL_Generiques.inc.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-AppreciationRisques.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-TraitementRisques.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-Actions.php' );
		
		
		$objCartographie = new CartographiesRisques();
		
		
		$Titre_Onglet = ucfirst($L_Conformite);
		
		// Création d'un nouvel onglet et lui donne un nom.
		$this->objPHPExcel->createSheet()->setTitle( $Titre_Onglet );
		
		// Mise en page de l'onglet.
		$this->objPHPExcel->setActiveSheetIndexByName( $Titre_Onglet )->getPageSetup()
		->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$this->objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		
		// Répète sur chaque page les lignes 1 à 4.
		$this->objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 4);
		
		// Initialise le haut et le bas de page de l'onglet courant.
		$this->initialiseHautBasOnglet( $Titre_Onglet );
		
		
		// ================================================
		// Construit la partie fixe du tableau (l'entête).
		
		// -------------------
		// Colonne => Cartographie des Risques
		
		// Titre : Cartographie des Risques.
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'A1', ucfirst( $L_Cartographies_Risques ) );
		$this->objPHPExcel->getActiveSheet()->mergeCells( 'A1:C1' );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'A1:C1' )->applyFromArray(
			array_merge( $this->styleTextToutCentre, $this->styleBordureNoirAutour, $this->styleTitrePrincipalHorizontal ) );
		
		// Sous-Titre : Entité.
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'A2', $L_Entite );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth( 15 );
		
		// Sous-Titre : Libellé.
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'B2', $L_Libelle );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth( 40 );
		$this->objPHPExcel->getActiveSheet()->getStyle('B2')->getAlignment()->setWrapText( TRUE );
		
		// Sous-Titre : Actifs Primordiaux.
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'C2', $L_Actifs_Primordiaux );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth( 9.7 );
		$this->objPHPExcel->getActiveSheet()->getStyle('C2')->getAlignment()->setWrapText( TRUE );
		
		// Bordure autour de la zone "Risque"
		$this->objPHPExcel->getActiveSheet()->getStyle( 'A2:C2' )->applyFromArray(
			array_merge( $this->styleTitreNomHorizontal, $this->styleTextToutCentre, $this->styleBordureNoirAutourGrisInterieur ) );
		
		
		// -------------------
		// Colonne => Mesures
		
		// Titre : Mesures des Référentiels
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'D1', $L_Mesures_Referentiels );
		
		$this->objPHPExcel->getActiveSheet()->mergeCells( 'D1:F1' );
		
		$this->objPHPExcel->getActiveSheet()->getStyle( 'D1:F1' )->applyFromArray(
			array_merge( $this->styleTitrePrincipalHorizontal, $this->styleBordureNoirAutour, $this->styleTextToutCentre ) );
		
		$this->objPHPExcel->getActiveSheet()->getStyle( 'D1:F1' )->getAlignment()->setWrapText( TRUE );
		
		// Titre : Référentiel
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'D2', $L_Referentiel );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension( 'D' )->setWidth( 25 );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'D2' )->getAlignment()->setWrapText( TRUE );
		
		// Titre : Libelle
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'E2', $L_Libelle );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension( 'E' )->setWidth( 40 );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'E2' )->getAlignment()->setWrapText( TRUE );
		
		// Titre : Statut
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'F2', $L_Status );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension( 'F' )->setWidth( 15 );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'F2' )->getAlignment()->setWrapText( TRUE );
		
		// Bordure autour de la zone "Mesure"
		$this->objPHPExcel->getActiveSheet()->getStyle( 'D2:E2' )->applyFromArray(
			array_merge( $this->styleTitreNomHorizontal, $this->styleTextToutCentre, $this->styleBordureNoirAutourGrisInterieur ) );
		
		
		// -------------------
		// Colonne => Actions
		
		// Titre : Actions.
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'G1', ucfirst( $L_Actions ) );
		
		$this->objPHPExcel->getActiveSheet()->mergeCells( 'G1:L1' );
		
		$this->objPHPExcel->getActiveSheet()->getStyle( 'G1:L1' )->applyFromArray(
			array_merge( $this->styleTitrePrincipalHorizontal, $this->styleBordureNoirAutour, $this->styleTextToutCentre ) );
		
		// Titre : Libellé
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'G2', $L_Libelle );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension( 'G' )->setWidth( 50 );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'G2' )->getAlignment()->setWrapText( TRUE );
		
		// Titre : Acteur
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'H2', $L_Acteur );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension( 'H' )->setWidth( 15 );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'H2' )->getAlignment()->setWrapText( TRUE );
		
		// Titre : Date début
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'I2', $L_Date_Debut );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension( 'I' )->setWidth( 10 );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'I2' )->getAlignment()->setWrapText( TRUE );
		
		// Titre : Date fin
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'J2', $L_Date_Fin );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension( 'J' )->setWidth( 10 );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'J2' )->getAlignment()->setWrapText( TRUE );
		
		// Titre : Fréquence
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'K2', $L_Frequence );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension( 'K' )->setWidth( 15 );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'K2' )->getAlignment()->setWrapText( TRUE );
		
		// Titre : Statut
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'L2', $L_Status );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension( 'L' )->setWidth( 15 );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'L2' )->getAlignment()->setWrapText( TRUE );
		
		// Bordure autour de la zone "Action"
		$this->objPHPExcel->getActiveSheet()->getStyle( 'G2:L2' )->applyFromArray(
			array_merge( $this->styleTitreNomHorizontal, $this->styleTextToutCentre, $this->styleBordureNoirAutour ) );
		
		
		// *********************************************************************
		
		// =====================================================================
		// Construit la liste des Référentiels, des Mesures et des Actions associés.
			$sql = 'SELECT
ent.ent_libelle, crs.crs_libelle, crs.crs_version,
rfc.rfc_code, rfc.rfc_version, lbr_rfc.lbr_libelle,
cnf.cnf_id, cnf.cnf_code, cnf.cnf_type, cnf.rfc_id,
lbr1.lbr_libelle AS "libelle_code", lbr2.lbr_libelle AS "libelle_etat"
FROM iden_idn_ent AS "iden"
LEFT JOIN ent_entites AS "ent" ON ent.ent_id = iden.ent_id
LEFT JOIN crs_cartographies_risques AS "crs" ON crs.ent_id = ent.ent_id
LEFT JOIN crrf_crs_rfc AS "crrf" ON crrf.crs_id = crs.crs_id
LEFT JOIN rfc_referentiels_conformite AS "rfc" ON rfc.rfc_id = crrf.rfc_id
LEFT JOIN lbr_libelles_referentiel AS "lbr_rfc" ON lbr_rfc.lbr_code = \'RFC-\'||crrf.rfc_id AND lbr_rfc.lng_id = :langue
LEFT JOIN lbr_libelles_referentiel AS "lbr1" ON lbr1.lbr_code = CONCAT(\'RFC-\',cnf.rfc_id,\'-\',cnf.cnf_code) AND lbr1.lng_id = :langue
LEFT JOIN lbr_libelles_referentiel AS "lbr2" ON lbr2.lbr_code = CONCAT(\'MCR_ETAT_\',cnf.cnf_etat_code) AND lbr2.lng_id = :langue
WHERE iden.idn_id = :idn_id
ORDER BY rfc_code, rfc_version, lbr3.lbr_libelle, lbr1.lbr_libelle ';
		
		$requete = $this->prepareSQL( $sql );
		
		$Liste_Risques = $this->bindSQL($requete, ':idn_id', $_SESSION['idn_id'], self::ID_TYPE)
		->bindSQL($requete, ':langue', $this->Langue, self::LANGUE_TYPE, self::LANGUE_LENGTH)
		->executeSQL($requete)
		->fetchAll(PDO::FETCH_CLASS);
		
		
		// =========================================================================================
		// Edite le détail de l'ensemble des Risques, des Mesures et des Actions (corps du tableau.
		
		$Ligne_Courante = 3; // Ammorce la ligne courante
		$Couleur_Fond = FALSE;
		$Traceur = '';
		
		$Ligne_Debut_Mesure = $Ligne_Courante;
		$Ligne_Fin_Mesure = $Ligne_Courante + 1;
		
		foreach ($Liste_Risques as $Occurrence) {
			//$Ligne_Suivante = $Ligne_Courante + 1;
			$Derniere_Ligne = $Ligne_Courante; //$Ligne_Suivante;
			
			
			// Valeur : Code du risque.
			$this->objPHPExcel->getActiveSheet()->setCellValue( 'A'.$Ligne_Courante, 'R'.$Occurrence->rcs_code );
			
			
			// Valeur : Actif support.
			$this->objPHPExcel->getActiveSheet()->setCellValue( 'B'.$Ligne_Courante, $Occurrence->spp_nom );
			
			
			// Valeur : Actifs primordiaux.
			$this->objPHPExcel->getActiveSheet()->setCellValue( 'C'.$Ligne_Courante, str_replace('\n', "\n", $Occurrence->apr_noms ) );
			
			
			// --------------------------------------------------
			// Valeur : Liste des Mesures attachées à ce Risque.
			$requete = $this->prepareSQL(
				'SELECT ' .
				'mcr.mcr_id, ' .
				'lbr.lbr_libelle AS "mgr_libelle", ' .
				'lbr1.lbr_libelle AS "mgr_etat_libelle" ' .
				'FROM mcr_mesures_cartographies AS "mcr" ' .
				'LEFT JOIN mgr_mesures_generiques AS "mgr" ON mgr.mgr_id = mcr.mgr_id ' .
				'LEFT JOIN lbr_libelles_referentiel AS "lbr" ON lbr.lbr_code = mgr_code AND lbr.lng_id = :langue ' .
				'LEFT JOIN lbr_libelles_referentiel AS "lbr1" ON lbr1.lbr_code = \'MCR_ETAT_\'||mcr_etat_code AND lbr1.lng_id = :langue ' .
				'WHERE mcr.rcs_id = :rcs_id '
				);
			
			$Liste_Mesures = $this->bindSQL($requete, ':rcs_id', $Occurrence->rcs_id, self::ID_TYPE)
			->bindSQL($requete, ':langue', $this->Langue, self::LANGUE_TYPE, self::LANGUE_LENGTH)
			->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);
			
			
			$Total_Mesures = count( $Liste_Mesures );
			
			
			foreach ( $Liste_Mesures as $Mesure ) {
				// Affecte et cadre le Libellé de la Mesure.
				$this->objPHPExcel->getActiveSheet()->setCellValue( 'D'.$Ligne_Debut_Mesure, $Mesure->mgr_libelle );
				$this->objPHPExcel->getActiveSheet()->getStyle( 'D'.$Ligne_Debut_Mesure )->getAlignment()->setWrapText( TRUE );
				$this->objPHPExcel->getActiveSheet()->getStyle( 'D'.$Ligne_Debut_Mesure )
				->applyFromArray( $this->styleTextAGauche );
				
				// Affecte et cadre le Statut de la Mesure.
				$this->objPHPExcel->getActiveSheet()->setCellValue( 'E'.$Ligne_Debut_Mesure, $Mesure->mgr_etat_libelle );
				$this->objPHPExcel->getActiveSheet()->getStyle( 'E'.$Ligne_Debut_Mesure )->getAlignment()->setWrapText( TRUE );
				$this->objPHPExcel->getActiveSheet()->getStyle( 'E'.$Ligne_Debut_Mesure )
				->applyFromArray( $this->styleTextAGauche );
				
				
				// --------------------------------------------------
				// Valeur : Liste des Actions attachées à cette Mesure.
				$sql = 'SELECT
				act_libelle,
				idn.idn_login||\' - \'||cvl_prenom||\' \'||cvl_nom AS "acteur",
				act_date_debut_p,
				act_date_debut_r,
				act_date_fin_p,
				act_date_fin_r,
				lbr_act_frequence.lbr_libelle AS "act_frequence_libelle",
				lbr_act_statut.lbr_libelle AS "act_statut_libelle"
					
				FROM act_actions AS "act"
				LEFT JOIN idn_identites AS "idn" ON idn.idn_id = act.idn_id
				LEFT JOIN cvl_civilites AS "cvl" ON cvl.cvl_id = idn.cvl_id
				LEFT JOIN lbr_libelles_referentiel AS "lbr_act_frequence" ON lbr_act_frequence.lbr_code = act.act_frequence_code AND lbr_act_frequence.lng_id = :langue
				LEFT JOIN lbr_libelles_referentiel AS "lbr_act_statut" ON lbr_act_statut.lbr_code = act.act_statut_code AND lbr_act_statut.lng_id = :langue
					
				WHERE act.mcr_id = :mcr_id
					
				ORDER BY act_libelle ';
				
				$requete = $this->prepareSQL( $sql );
				
				$Liste_Actions = $this->bindSQL($requete, ':mcr_id', $Mesure->mcr_id, self::ID_TYPE)
				->bindSQL($requete, ':langue', $this->Langue, self::LANGUE_TYPE, self::LANGUE_LENGTH)
				->executeSQL($requete)
				->fetchAll(PDO::FETCH_CLASS);
				
				$Ligne_Action = $Ligne_Debut_Mesure;
				
				foreach ( $Liste_Actions as $Action ) {
					// Décale la ligne de fin de Mesure au fur et à mesure des Actions à afficher.
					$Ligne_Fin_Mesure = $Ligne_Action;
					
					// Affecte et cadre le Libellé de l'Action.
					$this->objPHPExcel->getActiveSheet()->setCellValue( 'F'.$Ligne_Action, $Action->act_libelle );
					$this->objPHPExcel->getActiveSheet()->getStyle( 'F'.$Ligne_Action )->getAlignment()->setWrapText( TRUE );
					$this->objPHPExcel->getActiveSheet()->getStyle( 'F'.$Ligne_Action )
					->applyFromArray( $this->styleTextAGauche );
					
					// Affecte et cadre l'Acteur de l'Action.
					$this->objPHPExcel->getActiveSheet()->setCellValue( 'G'.$Ligne_Action, $Action->acteur );
					$this->objPHPExcel->getActiveSheet()->getStyle( 'G'.$Ligne_Action )->getAlignment()->setWrapText( TRUE );
					$this->objPHPExcel->getActiveSheet()->getStyle( 'G'.$Ligne_Action )
					->applyFromArray( $this->styleTextAGauche );
					
					// Affecte et cadre la Date de Début de l'Action.
					if ( $Action->act_date_debut_r != '' ) $Date_Debut = $Action->act_date_debut_r;
					else $Date_Debut = $Action->act_date_debut_p;
					
					$this->objPHPExcel->getActiveSheet()->setCellValue( 'H'.$Ligne_Action, $Date_Debut );
					$this->objPHPExcel->getActiveSheet()->getStyle( 'H'.$Ligne_Action )->getAlignment()->setWrapText( TRUE );
					$this->objPHPExcel->getActiveSheet()->getStyle( 'H'.$Ligne_Action )
					->applyFromArray( $this->styleTextAGauche );
					
					// Affecte et cadre la Date de Fin de l'Action.
					if ( $Action->act_date_fin_r != '' ) $Date_Fin = $Action->act_date_fin_r;
					else $Date_Fin = $Action->act_date_fin_p;
					
					$this->objPHPExcel->getActiveSheet()->setCellValue( 'I'.$Ligne_Action, $Date_Fin );
					$this->objPHPExcel->getActiveSheet()->getStyle( 'I'.$Ligne_Action )->getAlignment()->setWrapText( TRUE );
					$this->objPHPExcel->getActiveSheet()->getStyle( 'I'.$Ligne_Action )
					->applyFromArray( $this->styleTextAGauche );
					
					// Affecte et cadre la Fréquence de l'Action.
					$this->objPHPExcel->getActiveSheet()->setCellValue( 'J'.$Ligne_Action, $Action->act_frequence_libelle );
					$this->objPHPExcel->getActiveSheet()->getStyle( 'J'.$Ligne_Action )->getAlignment()->setWrapText( TRUE );
					$this->objPHPExcel->getActiveSheet()->getStyle( 'J'.$Ligne_Action )
					->applyFromArray( $this->styleTextAGauche );
					
					// Affecte et cadre le Statut de l'Action.
					$this->objPHPExcel->getActiveSheet()->setCellValue( 'K'.$Ligne_Action, $Action->act_statut_libelle );
					$this->objPHPExcel->getActiveSheet()->getStyle( 'K'.$Ligne_Action )->getAlignment()->setWrapText( TRUE );
					$this->objPHPExcel->getActiveSheet()->getStyle( 'K'.$Ligne_Action )
					->applyFromArray( $this->styleTextAGauche );
					
					
					// Dessine un cadre autour de la zone : Action
					$this->objPHPExcel->getActiveSheet()->getStyle( 'F'.$Ligne_Action.':K'.$Ligne_Action )
					->applyFromArray( $this->styleBordureNoirAutourGrisInterieur );
					
					// Passe à la ligne d'action suivante.
					$Ligne_Action += 1;
					
				} // Fin boucle : Actions
				
				
				$Total_Actions = count( $Liste_Actions );
				
				
				// Cas d'une occurrence de Risque ou il n'y a entre 0 et 1 Mesure et 0 et 1 Action.
				if ( $Total_Actions < 2 && $Total_Mesures < 2 ) {
					// ------------------------------------------------
					// Aligne les éléments des sous-colonnes : Mesure.
					
					// Aligne la cellule : Libellé.
					$this->objPHPExcel->getActiveSheet()->getStyle( 'D'.$Ligne_Courante )
					->applyFromArray( $this->styleTextAGauche );
					
					// Fusionne et aligne la cellule : Statut.
					$this->objPHPExcel->getActiveSheet()->getStyle( 'E'.$Ligne_Courante )
					->applyFromArray( $this->styleTextAGauche );
					
					// Met une bordure autour des cellules : Libellé/Statut.
					$this->objPHPExcel->getActiveSheet()->getStyle( 'D'.$Ligne_Courante . ':' . 'E'.$Ligne_Courante )
					->applyFromArray( $this->styleBordureNoirAutourGrisInterieur );
					
					
					// ------------------------------------------------
					// Fusion des éléments des sous-colonnes : Action.
					
					// Fusion des éléments de la colonne : Libellé.
					$this->objPHPExcel->getActiveSheet()->mergeCells( 'F'.$Ligne_Courante . ':' . 'F'.$Derniere_Ligne );
					
					$this->objPHPExcel->getActiveSheet()->getStyle( 'F'.$Ligne_Courante . ':' . 'F'.$Derniere_Ligne )
					->applyFromArray( $this->styleTextAGauche );
					
					// Fusion des éléments de la colonne : Acteur.
					$this->objPHPExcel->getActiveSheet()->mergeCells( 'G'.$Ligne_Courante . ':' . 'G'.$Derniere_Ligne );
					
					$this->objPHPExcel->getActiveSheet()->getStyle( 'G'.$Ligne_Courante . ':' . 'G'.$Derniere_Ligne )
					->applyFromArray( $this->styleTextAGauche );
					
					// Fusion des éléments de la colonne : Date Début.
					$this->objPHPExcel->getActiveSheet()->mergeCells( 'H'.$Ligne_Courante . ':' . 'H'.$Derniere_Ligne );
					
					$this->objPHPExcel->getActiveSheet()->getStyle( 'H'.$Ligne_Courante . ':' . 'H'.$Derniere_Ligne )
					->applyFromArray( $this->styleTextAGauche );
					
					// Fusion des éléments de la colonne : Date Fin.
					$this->objPHPExcel->getActiveSheet()->mergeCells( 'I'.$Ligne_Courante . ':' . 'I'.$Derniere_Ligne );
					
					$this->objPHPExcel->getActiveSheet()->getStyle( 'I'.$Ligne_Courante . ':' . 'I'.$Derniere_Ligne )
					->applyFromArray( $this->styleTextAGauche );
					
					// Fusion des éléments de la colonne : Fréquence.
					$this->objPHPExcel->getActiveSheet()->mergeCells( 'J'.$Ligne_Courante . ':' . 'J'.$Derniere_Ligne );
					
					$this->objPHPExcel->getActiveSheet()->getStyle( 'J'.$Ligne_Courante . ':' . 'J'.$Derniere_Ligne )
					->applyFromArray( $this->styleTextAGauche );
					
					// Fusion des éléments de la colonne : Statut.
					$this->objPHPExcel->getActiveSheet()->mergeCells( 'K'.$Ligne_Courante . ':' . 'K'.$Derniere_Ligne );
					
					$this->objPHPExcel->getActiveSheet()->getStyle( 'K'.$Ligne_Courante . ':' . 'K'.$Derniere_Ligne )
					->applyFromArray( $this->styleTextAGauche );
					
					
					$this->objPHPExcel->getActiveSheet()->getStyle( 'F'.$Ligne_Courante . ':' . 'K'.$Derniere_Ligne )
					->applyFromArray( $this->styleBordureNoirAutourGrisInterieur );
					
					
					$Ligne_Fin_Mesure = $Derniere_Ligne;
				} else {
					if ( $Total_Actions < 2 ) {
						// Bordure autour de la zone : Mesure
						$this->objPHPExcel->getActiveSheet()
						->getStyle( 'D'.$Ligne_Debut_Mesure . ':' .	'E'.$Ligne_Debut_Mesure )
						->applyFromArray( $this->styleBordureNoirAutourGrisInterieur );
						
						// Bordure autour de la zone : Action
						$this->objPHPExcel->getActiveSheet()
						->getStyle( 'F'.$Ligne_Debut_Mesure . ':' . 'K'.$Ligne_Debut_Mesure )
						->applyFromArray( $this->styleBordureNoirAutourGrisInterieur );
						
					} else { // Cas où il a plusieurs Actions.
						// ------------------------------------------------
						// Fusion des éléments des sous-colonnes : Mesure.
						
						// Fusionne et aligne la cellule : Libellé.
						if ( $Total_Actions > 1 ) {
							$this->objPHPExcel->getActiveSheet()->mergeCells( 'D'.$Ligne_Debut_Mesure . ':' . 'D'.$Ligne_Fin_Mesure );
						}
						
						$this->objPHPExcel->getActiveSheet()->getStyle( 'D'.$Ligne_Debut_Mesure . ':' . 'D'.$Ligne_Fin_Mesure )
						->applyFromArray( $this->styleTextAGauche );
						
						// Fusionne et aligne la cellule : Statut.
						if ( $Total_Actions > 1 ) {
							$this->objPHPExcel->getActiveSheet()->mergeCells( 'E'.$Ligne_Debut_Mesure . ':' . 'E'.$Ligne_Fin_Mesure );
						}
						
						$this->objPHPExcel->getActiveSheet()->getStyle( 'E'.$Ligne_Debut_Mesure . ':' . 'E'.$Ligne_Fin_Mesure )
						->applyFromArray( $this->styleTextAGauche );
						
						// Met une bordure autour des cellules : Libellé/Statut.
						$this->objPHPExcel->getActiveSheet()->getStyle( 'D'.$Ligne_Debut_Mesure . ':' . 'E'.$Ligne_Fin_Mesure )
						->applyFromArray( $this->styleBordureNoirAutourGrisInterieur );
						
					}
					
				}
				
				$Ligne_Debut_Mesure += 1;
				
				
			} // Fin boucle : Mesures
			
			
			// =============================================================
			// Met en forme toutes les informations rattachées à un risque.
			if ( $Ligne_Fin_Mesure > $Ligne_Debut_Mesure ) $Ligne_Debut_Mesure = $Ligne_Fin_Mesure;
			
			if ( $Derniere_Ligne < $Ligne_Debut_Mesure ) $Derniere_Ligne = $Ligne_Debut_Mesure;
			
			if ( $Total_Mesures > 1 or $Total_Actions < 2 ) $Derniere_Ligne -=1; // Réajustement
			
			
			// Fusionne, aligne et met une bordure autour de la cellule : Code du Risque.
			$this->objPHPExcel->getActiveSheet()->mergeCells( 'A'.$Ligne_Courante . ':' . 'A'.$Derniere_Ligne );
			
			$this->objPHPExcel->getActiveSheet()->getStyle( 'A'.$Ligne_Courante . ':' . 'A'.$Derniere_Ligne )
			->applyFromArray( $this->styleTextAGauche );
			
			$this->objPHPExcel->getActiveSheet()->getStyle( 'A' . $Ligne_Courante )->getAlignment()->setWrapText( TRUE );
			
			$this->objPHPExcel->getActiveSheet()->getStyle( 'A'.$Ligne_Courante . ':' . 'A'.$Derniere_Ligne )
			->applyFromArray( $this->styleBordureNoirAutour );
			
			
			// Fusionne, aligne et met une bordure autour de la cellule : Actif support.
			$this->objPHPExcel->getActiveSheet()->mergeCells( 'B'.$Ligne_Courante . ':' . 'B'.$Derniere_Ligne );
			
			$this->objPHPExcel->getActiveSheet()->getStyle( 'B'.$Ligne_Courante . ':' . 'B'.$Derniere_Ligne )
			->applyFromArray( $this->styleTextAGauche );
			
			$this->objPHPExcel->getActiveSheet()->getStyle( 'B' . $Ligne_Courante )->getAlignment()->setWrapText( TRUE );
			
			$this->objPHPExcel->getActiveSheet()->getStyle( 'B'.$Ligne_Courante . ':' . 'B'.$Derniere_Ligne )
			->applyFromArray( $this->styleBordureNoirAutour );
			
			
			// Fusionne, aligne et met une bordure autour de la cellule : Actifs primordiaux.
			$this->objPHPExcel->getActiveSheet()->mergeCells( 'C'.$Ligne_Courante . ':' . 'C'.$Derniere_Ligne );
			
			$this->objPHPExcel->getActiveSheet()->getStyle( 'C'.$Ligne_Courante . ':' . 'C'.$Derniere_Ligne )
			->applyFromArray( $this->styleTextAGauche );
			
			$this->objPHPExcel->getActiveSheet()->getStyle( 'C' . $Ligne_Courante )->getAlignment()->setWrapText( TRUE );
			
			$this->objPHPExcel->getActiveSheet()->getStyle( 'C'.$Ligne_Courante . ':' . 'C'.$Derniere_Ligne )
			->applyFromArray( $this->styleBordureNoirAutour );
			
			
			// Gère la surbrillance de l'occurrence de Risque
			if ( $Traceur != $Ligne_Courante.'-'.$Derniere_Ligne ) {
				if ( $Couleur_Fond == FALSE ) {
					$styleSpecial = array_merge( $this->styleBordureNoirAutour, $this->styleSurligne );
					$Couleur_Fond = TRUE;
				} else {
					$styleSpecial = $this->styleBordureNoirAutour;
					$Couleur_Fond = FALSE;
				}
				
				$this->objPHPExcel->getActiveSheet()
				->getStyle( 'A' . $Ligne_Courante . ':' . 'K' . $Derniere_Ligne )
				->applyFromArray( $styleSpecial );
				
				$Traceur = $Ligne_Courante.'-'.$Derniere_Ligne;
			}
			
			$Derniere_Ligne += 1;
			$Ligne_Courante = $Derniere_Ligne; // Change de ligne.
			$Ligne_Debut_Mesure = $Ligne_Courante;
			
		} // Fin boucle : Risques
		
		return TRUE;
	}
	

	/* ================================================================================== */
	
	public function editionEvenementsRedoutes( $crs_id ) {
	/**
	* Edite les Evénements Redoutés.
	*
	* @author Pierre-Luc MARY
	* @date 2014-11-26
	*
	* @param[in] $crs_id ID de la Cartographie à utiliser
	*
	* @return Renvoi vrai si les informations ont bien été éditées.
	*			
	*/
		// ==========================================================
		// Vérifie si la Cartographie dispose d'Evénements Redoutés.
		$requete = $this->prepareSQL( 'SELECT count(evr_id) AS total FROM evr_evenements_redoutes AS "evr" WHERE crs_id = :crs_id ' );
			
		$Total_EVR = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->executeSQL($requete)
			->fetchObject()->total;

		if ( $Total_EVR == 0 ) return TRUE; // Ne créé rien, si pas d'Evénement Redouté.


		$Titre_Onglet = 'Evénements redoutés';


		// Création d'un nouvel onglet et lui donne un nom.
		$this->objPHPExcel->createSheet()->setTitle( $Titre_Onglet );

		// Mise en page de l'onglet.
		$this->objPHPExcel->setActiveSheetIndexByName( $Titre_Onglet )->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

		// Mise en page de l'onglet.
		$this->objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$this->objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

		// Initialise le haut et le bas de page de l'onglet courant.
		$this->initialiseHautBasOnglet( $Titre_Onglet );

		// Répète sur chaque page les lignes 1 à 3.
		$this->objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 3);


		// Définit et place les titres.
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'A2', $Titre_Onglet );
		$this->objPHPExcel->getActiveSheet()->mergeCells( 'A2:B2' );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'A2:B2' )->applyFromArray(
			array_merge( $this->styleBordureNoirAutour, $this->styleTitrePrincipalHorizontalSpecial, $this->styleTextCentreBas )
		);

		$this->objPHPExcel->getActiveSheet()->setCellValue( 'A3', 'Libellé' );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'A3' )->getAlignment()->setWrapText( TRUE );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension( 'A' )->setWidth( 50 );

		$this->objPHPExcel->getActiveSheet()->setCellValue( 'B3', 'Niveau impact' );
		$this->objPHPExcel->getActiveSheet()->getStyle( 'B3' )->getAlignment()->setWrapText( TRUE );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension( 'B' )->setWidth( 10 );

		$this->objPHPExcel->getActiveSheet()->getStyle( 'A3:B3' )->applyFromArray(
			array_merge( $this->styleBordureNoirAutourGrisInterieur, $this->styleTitreNomHorizontal, $this->styleTextAGauche ) );


		// Donne de la hauteur aux lignes pour un meilleur affichage.
		$this->objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight( 20 );
		$this->objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight( 120 );


		// ==============================================
		// Construit la ligne des Impacts.
		$DebutColonneImpact = 3;

		$this->objPHPExcel->getActiveSheet()->setCellValue( $this->xlColumnValue( $DebutColonneImpact ) . '1', 'Impacts' );
		$this->objPHPExcel->getActiveSheet()->getStyle( $this->xlColumnValue( $DebutColonneImpact ) . '1' )->getAlignment()->setWrapText( TRUE );


		// Récupère le libellé de tous les impacts.
		$requete = $this->prepareSQL(
			'SELECT ' .
			'rlb.lbr_libelle, ign.ign_id ' .
			'FROM ign_impacts_generiques AS "ign" ' .
			'LEFT JOIN lbr_libelles_referentiel AS "rlb" ON rlb.lbr_code = ign_code AND rlb.lng_id = :langue '
		);
			
		$Liste_Impacts = $this //->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->bindSQL($requete,  ':langue', $this->Langue, self::LANGUE_TYPE, self::LANGUE_LENGTH)
			->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);


		$FinColonneImpact = $DebutColonneImpact;
		$Indice_Impacts = '';

		foreach ( $Liste_Impacts as $Occurrence ) {
			$Derniere_Colonne = $this->xlColumnValue( $FinColonneImpact );

			$this->objPHPExcel->getActiveSheet()->setCellValue( $Derniere_Colonne . '2', $Occurrence->lbr_libelle );
			$this->objPHPExcel->getActiveSheet()->mergeCells( $Derniere_Colonne .'2:' . $Derniere_Colonne .'3' );
			$this->objPHPExcel->getActiveSheet()->getStyle( $Derniere_Colonne .'2:' . $Derniere_Colonne .'3' )
				->getAlignment()->setWrapText( TRUE );
			$this->objPHPExcel->getActiveSheet()->getStyle( $Derniere_Colonne .'2:' . $Derniere_Colonne .'3' )
				->applyFromArray( $this->styleBordureNoirAutourGrisInterieur );
			$this->objPHPExcel->getActiveSheet()->getStyle( $Derniere_Colonne .'2' )->applyFromArray( $this->styleTitreNomVertical );

			$Indice_Impacts[ $Occurrence->ign_id ] = $Derniere_Colonne;

			$FinColonneImpact += 1;
		}


		// Ajuste le titre.
		$FinColonneImpact -= 1; // Réajustement.
		$Plage = $this->xlColumnValue( $DebutColonneImpact ) .'1:' . $this->xlColumnValue( $FinColonneImpact ) .'1';
		$this->objPHPExcel->getActiveSheet()->mergeCells( $Plage );
		$this->objPHPExcel->getActiveSheet()->getStyle( $Plage )->applyFromArray(
				array_merge( $this->styleBordureNoirAutourGrisInterieur, $this->styleTitrePrincipalHorizontal ) );


		// ===========================================
		// Construit la ligne des Actifs Primordiaux.
		$DebutColonneActif = $FinColonneImpact + 1;

		$this->objPHPExcel->getActiveSheet()->setCellValue( $this->xlColumnValue( $DebutColonneActif ) . '1', 'Actifs Primordiaux' );
		$this->objPHPExcel->getActiveSheet()->getStyle( $this->xlColumnValue( $DebutColonneActif ) . '1' )->getAlignment()->setWrapText( TRUE );

		$requete = $this->prepareSQL(
			'SELECT ' .
			'apr.apr_nom, apr.apr_id ' .
			'FROM apr_actifs_primordiaux AS "apr" ' .
			'WHERE apr.crs_id = :crs_id '
		);
			
		$Liste = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);

		$FinColonneActif = $DebutColonneActif;
		$Indice_Actifs_Primordiaux = '';

		foreach ( $Liste as $Occurrence ) {
			$Derniere_Colonne = $this->xlColumnValue( $FinColonneActif );

			$this->objPHPExcel->getActiveSheet()->setCellValue( $Derniere_Colonne . '2', $Occurrence->apr_nom );
			$this->objPHPExcel->getActiveSheet()->mergeCells( $Derniere_Colonne .'2:' . $Derniere_Colonne .'3' );
			$this->objPHPExcel->getActiveSheet()->getStyle( $Derniere_Colonne .'2:' . $Derniere_Colonne .'3' )->getAlignment()->setWrapText( TRUE );
			$this->objPHPExcel->getActiveSheet()->getStyle( $Derniere_Colonne .'2:' . $Derniere_Colonne .'3' )
				->applyFromArray( $this->styleBordureNoirAutourGrisInterieur );
			$this->objPHPExcel->getActiveSheet()->getStyle( $Derniere_Colonne .'2' )->applyFromArray( $this->styleTitreNomVertical );

			$Indice_Actifs_Primordiaux[ $Occurrence->apr_id ] = $Derniere_Colonne;

			$FinColonneActif += 1;
		}

		// Ajuste le titre.
		$FinColonneActif -= 1; // Réajustement.
		$Plage = $this->xlColumnValue( $DebutColonneActif ) .'1:' . $this->xlColumnValue( $FinColonneActif ) .'1';
		$this->objPHPExcel->getActiveSheet()->mergeCells( $Plage );
		$this->objPHPExcel->getActiveSheet()->getStyle( $Plage )->applyFromArray(
				array_merge( $this->styleBordureNoirAutourGrisInterieur, $this->styleTitrePrincipalHorizontal ) );


		// ===========================================
		// Construit la ligne des Sources de Menaces.
		$DebutColonneSource = $FinColonneActif + 1;

		$this->objPHPExcel->getActiveSheet()->setCellValue( $this->xlColumnValue( $DebutColonneSource ) . '3', 'Sources de Menaces' );
		$this->objPHPExcel->getActiveSheet()->getStyle( $this->xlColumnValue( $DebutColonneSource ) . '3' )->getAlignment()->setWrapText( TRUE );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension( $this->xlColumnValue( $DebutColonneSource ) )->setWidth( 40 );
		$this->objPHPExcel->getActiveSheet()->getStyle( $this->xlColumnValue( $DebutColonneSource ) . '3' )->applyFromArray(
				array_merge( $this->styleBordureNoirAutourGrisInterieur, $this->styleTitrePrincipalHorizontal ) );


		// Récupère tous les Evénements Redoutés de cette Cartographie
		$requete = $this->prepareSQL(
			'SELECT ' .
			'evr.evr_id, evr.evr_libelle, gri_poids, gri_libelle ' .
			'FROM evr_evenements_redoutes AS "evr" ' .
			'LEFT JOIN gri_grilles_impact AS "gri" ON gri.gri_id = evr.gri_id ' .
			'WHERE evr.crs_id = :crs_id '
		);
			
		$Liste = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);

		$Ligne = 4; // Numéro de la ligne courante
		$Derniere_Ligne = count( $Liste ); // Dernière ligne de la Matrice

		foreach ($Liste as $Occurrence) {
			$this->objPHPExcel->getActiveSheet()->setCellValue( 'A'.$Ligne, $Occurrence->evr_libelle );
			$this->objPHPExcel->getActiveSheet()->setCellValue( 'B'.$Ligne, $Occurrence->gri_libelle . ' ('.$Occurrence->gri_poids.')' );


			// Mise en place des bordures.
			$this->objPHPExcel->getActiveSheet()->getStyle( 'A'.$Ligne . ':B'.$Ligne )
				->applyFromArray( array_merge( $this->styleBordureNoirAutourGrisInterieur, $this->styleTextAGauche ) );

			$this->objPHPExcel->getActiveSheet()->getStyle( $this->xlColumnValue($DebutColonneImpact).$Ligne . ':'.$this->xlColumnValue($FinColonneImpact).$Ligne )
				->applyFromArray( array_merge( $this->styleBordureNoirAutourGrisInterieur, $this->styleTextToutCentre ) );

			$this->objPHPExcel->getActiveSheet()->getStyle( $this->xlColumnValue($DebutColonneActif).$Ligne . ':'.$this->xlColumnValue($FinColonneActif).$Ligne )
				->applyFromArray( array_merge( $this->styleBordureNoirAutourGrisInterieur, $this->styleTextToutCentre ) );

			$this->objPHPExcel->getActiveSheet()->getStyle( $this->xlColumnValue($DebutColonneSource).$Ligne )
				->applyFromArray( array_merge( $this->styleBordureNoirAutourGrisInterieur, $this->styleTextAGaucheHaut ) )
				->getAlignment()->setWrapText( TRUE );


			// Récupère les impacts spécifiques à cet Evénement redouté.
			$requete = $this->prepareSQL(
				'SELECT ' .
				'evig.ign_id ' .
				'FROM evr_evenements_redoutes AS "evr" ' .
				'LEFT JOIN evig_evr_ign AS "evig" ON evig.evr_id = evr.evr_id ' .
				'WHERE evr.evr_id = :evr_id AND evig.ign_id IS NOT NULL '
			);
				
			$Liste_Impacts = $this->bindSQL($requete, ':evr_id', $Occurrence->evr_id, self::ID_TYPE)
				->executeSQL($requete)
				->fetchAll(PDO::FETCH_CLASS);

			foreach ($Liste_Impacts as $Impact ) {
				$this->objPHPExcel->getActiveSheet()->setCellValue( $Indice_Impacts[$Impact->ign_id].$Ligne, 'X' );
			}


			// Récupère les Actifs Primordiaux spécifiques à cet Evénement redouté.
			$requete = $this->prepareSQL(
				'SELECT ' .
				'apr.apr_id ' .
				'FROM evr_evenements_redoutes AS "evr" ' .
				'LEFT JOIN evap_evr_apr AS "evap" ON evap.evr_id = evr.evr_id ' .
				'LEFT JOIN apr_actifs_primordiaux AS "apr" ON apr.apr_id = evap.apr_id ' .
				'WHERE evr.evr_id = :evr_id AND apr.apr_id IS NOT NULL '
			);
				
			$Liste_Actifs = $this->bindSQL($requete, ':evr_id', $Occurrence->evr_id, self::ID_TYPE)
				->executeSQL($requete)
				->fetchAll(PDO::FETCH_CLASS);

			foreach ($Liste_Actifs as $Actif ) {
				$this->objPHPExcel->getActiveSheet()->setCellValue( $Indice_Actifs_Primordiaux[$Actif->apr_id].$Ligne, 'X' );
			}

			$requete = $this->prepareSQL(
				'SELECT ' .
				'lbr1.lbr_libelle AS "srm_libelle" ' .
				'FROM evr_evenements_redoutes AS "evr" ' .
				'LEFT JOIN evsr_evr_srm AS "evsr" ON evsr.evr_id = evr.evr_id ' .
				'LEFT JOIN srm_sources_menaces AS "srm" ON srm.srm_id = evsr.srm_id ' .
				'LEFT JOIN lbr_libelles_referentiel AS "lbr1" ON lbr1.lbr_code = srm.srm_code AND lbr1.lng_id = :langue ' .
				'WHERE evr.evr_id = :evr_id '
			);
				
			$Liste_Sources = $this->bindSQL($requete, ':evr_id', $Occurrence->evr_id, self::ID_TYPE)
				->bindSQL($requete,  ':langue', $this->Langue, self::LANGUE_TYPE, self::LANGUE_LENGTH)
				->executeSQL($requete)
				->fetchAll(PDO::FETCH_CLASS);

			$Sources = '';

			foreach ( $Liste_Sources as $Source ) {
				if ( $Sources != '' ) $Sources .= "\n";

				$Sources .= '- ' . $Source->srm_libelle;
			}

			$this->objPHPExcel->getActiveSheet()->setCellValue( $this->xlColumnValue($DebutColonneSource).$Ligne, $Sources );

			$Ligne += 1;
		}

		return TRUE;
	}


	/* ================================================================================== */
	
	public function editionActifsRisques( $crs_id ) {
	/**
	* Edite les Actifs Primordiaux ainsi que leur répartition sur les Risques.
	*
	* @author Pierre-Luc MARY
	* @date 2014-11-26
	*
	* @param[in] $crs_id ID de la Cartographie à utiliser
	*
	* @return Renvoi vrai si les informations ont bien été éditées.
	*			
	*/
		$Titre_Onglet = 'Actifs-Risques';

		// Création d'un nouvel onglet et lui donne un nom.
		$this->objPHPExcel->createSheet()->setTitle( $Titre_Onglet );

		// Mise en page de l'onglet.
		$this->objPHPExcel->setActiveSheetIndexByName( $Titre_Onglet )->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$this->objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

		// Initialise le haut et le bas de page de l'onglet courant.
		$this->initialiseHautBasOnglet( $Titre_Onglet );

		// Répète sur chaque page les lignes 1 à 3.
		//$this->objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 3);


		// ==============================================
		// Construit la ligne des Risques.
		$requete = $this->prepareSQL(
			'SELECT ' .
			'rcs.rcs_id, ' .
			'rcs.rcs_code ' .
			'FROM rcs_risques_cartographies AS "rcs" ' .
			'LEFT JOIN spp_supports AS "spp" ON spp.spp_id = rcs.spp_id ' .
		    'LEFT JOIN spcr_spp_crs AS "spcr" ON spcr.spp_id = spp.spp_id ' .
		    'WHERE spcr.crs_id = :crs_id AND rcs_a_verifier = FALSE '
		);
			
		$Liste_Risques = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);

		$Derniere_Colonne = count( $Liste_Risques ) + 2 ; // Dernière colonne de la Matrice

		
		// Ajoute et ajuste un titre pour annoncer les Risques.
		$_Ligne = 1;
		$CelluleCourante = 'C' . $_Ligne;

		$this->objPHPExcel->getActiveSheet()->setCellValue($CelluleCourante, 'Risques');

		if ( $Derniere_Colonne > 2 ) {
			$FinCellule = $this->xlColumnValue( $Derniere_Colonne ) . $_Ligne;

			$this->objPHPExcel->getActiveSheet()->mergeCells( $CelluleCourante . ':' . $FinCellule );
			$this->objPHPExcel->getActiveSheet()->getStyle( $CelluleCourante . ':' . $FinCellule )->applyFromArray( $this->styleTitrePrincipalHorizontal );

			$_Ligne += 1;
			$CelluleCourante = 'C' . $_Ligne;
			$FinCellule = $this->xlColumnValue( $Derniere_Colonne ) . $_Ligne;
			$this->objPHPExcel->getActiveSheet()->getStyle( $CelluleCourante . ':' . $FinCellule )->applyFromArray( $this->styleBordureNoirAutourGrisInterieur );
		} else {
			$this->objPHPExcel->getActiveSheet()->getStyle( $CelluleCourante )->applyFromArray(
				array_merge( $this->styleBordureNoirAutour, $this->styleTitrePrincipalHorizontal ) );

			$_Ligne += 1;
			$CelluleCourante = 'C' . $_Ligne;
			$this->objPHPExcel->getActiveSheet()->getStyle( $CelluleCourante )->applyFromArray( $this->styleBordureNoirAutour );
		}

		$Colonne = 3; // Numéro de la colonne courante

		foreach ($Liste_Risques as $Occurrence) {
			$CelluleCourante = $this->xlColumnValue( $Colonne ) . $_Ligne;

			$this->objPHPExcel->getActiveSheet()->setCellValue( $CelluleCourante, 'R' . $Occurrence->rcs_code );

			$this->objPHPExcel->getActiveSheet()->getColumnDimension( $this->xlColumnValue( $Colonne ) )->setWidth( 5 );

			$this->objPHPExcel->getActiveSheet()->getStyle( $CelluleCourante )->applyFromArray( $this->styleTitreNomVertical );

			$Indice_Risques[$Occurrence->rcs_id] = $this->xlColumnValue( $Colonne );

			$Colonne += 1;
		}


		// ==============================================
		// Construit la colonne des Actifs Primordiaux.
		$requete = $this->prepareSQL(
			'SELECT ' .
			'rlb.lbr_libelle AS "apr_type", ' .
			'apr_id, ' .
			'apr_nom ' .
			'FROM apr_actifs_primordiaux AS "apr" ' .
			'LEFT JOIN lbr_libelles_referentiel AS "rlb" ON rlb.lbr_code = \'APR_TYPE_\'||apr_type_code AND rlb.lng_id = :langue ' .
			'WHERE apr.crs_id = :crs_id ' .
			'ORDER BY apr_type DESC, apr_nom '
		);
			
		$Liste_Actifs_Primordiaux = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->bindSQL($requete, ':langue', $this->Langue, self::LANGUE_TYPE, self::LANGUE_LENGTH)
			->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);

		// Ajoute un titre principal pour annoncer les Actifs Primordiaux.
		$this->objPHPExcel->getActiveSheet()->setCellValue( 'A3', 'Actifs Primordiaux' );

		$_Ligne = 3; // Numéro de la ligne courante
		$Ligne = 3; // Numéro de la ligne courante
		$Derniere_Ligne = count( $Liste_Actifs_Primordiaux ) + $Ligne; // Dernière ligne de la Matrice

		$Type_Primordial = ''; // Type de l'Actif Primordial courant
		$_Type_Primordial = ''; // Ancien type de l'Actif Primordial
		$_Colonne_Titre_Principal = 'A';
		$_Colonne_Titre = $this->xlColumnValue( $this->xlColumnValue( $_Colonne_Titre_Principal ) + 1 );
		$_Colonne_Donnees = $this->xlColumnValue( $this->xlColumnValue( $_Colonne_Titre ) + 1 );

		foreach ($Liste_Actifs_Primordiaux as $Occurrence) {
			$Derniere_Ligne = $Ligne;
			$Type_Primordial = $Occurrence->apr_type;
			$DebutCellule = $_Colonne_Titre . $Ligne;

			if ( $_Type_Primordial != $Type_Primordial ) {
				$this->objPHPExcel->getActiveSheet()->setCellValue( $_Colonne_Titre.$Ligne, $Type_Primordial );

				// Etant le titre pour marquer la séparation
				$FinCellule = $this->xlColumnValue( $Derniere_Colonne ) . $Ligne;

				$this->objPHPExcel->getActiveSheet()->mergeCells( $DebutCellule . ':' . $FinCellule );
				$this->objPHPExcel->getActiveSheet()->getStyle( $DebutCellule . ':' . $FinCellule )->applyFromArray(
					array_merge( $this->styleTitreHorizontal, $this->styleBordureNoirAutour ) );

				$_Type_Primordial = $Type_Primordial;

				$Ligne += 1;

				$DebutCellule = $_Colonne_Titre . $Ligne;

				$_Debut_Cellule = $_Colonne_Donnees . $Ligne;
				$FinCellule = $this->xlColumnValue( $Derniere_Colonne ) . $Ligne;

				$this->objPHPExcel->getActiveSheet()->getStyle( $_Debut_Cellule . ':' . $FinCellule )->applyFromArray(
					$this->styleBordureNoirAutourGrisInterieur );				
			} else {
				$_Debut_Cellule = $_Colonne_Donnees . $Ligne;
				$FinCellule = $this->xlColumnValue( $Derniere_Colonne ) . $Ligne;

				$this->objPHPExcel->getActiveSheet()->getStyle( $_Debut_Cellule . ':' . $FinCellule )->applyFromArray(
					$this->styleBordureNoirAutourGrisInterieur );				
			}

			$this->objPHPExcel->getActiveSheet()->setCellValue( $DebutCellule, $Occurrence->apr_nom);
			$this->objPHPExcel->getActiveSheet()->getStyle( $DebutCellule )->applyFromArray(
				array_merge( $this->styleTitreNomHorizontal, $this->styleBordureNoirAutour ) );

			$Indice_Actifs_Primordiaux[ $Occurrence->apr_id ] = $Ligne;

			$Ligne += 1;
		}

		// Ajuste la taille de la colonne des Actifs Primordiaux.
		$this->objPHPExcel->getActiveSheet()->getColumnDimension( $_Colonne_Titre_Principal )->setAutoSize( TRUE );
		
		if ( $Derniere_Ligne > $_Ligne ) {
			$this->objPHPExcel->getActiveSheet()->mergeCells( $_Colonne_Titre_Principal . $_Ligne . ':' . $_Colonne_Titre_Principal . $Derniere_Ligne );

			$this->objPHPExcel->getActiveSheet()->getStyle( $_Colonne_Titre_Principal . $_Ligne . ':' . $_Colonne_Titre_Principal . $Derniere_Ligne )
				->applyFromArray( array_merge( $this->styleTitrePrincipalVertical, $this->styleBordureNoirAutour ) );
		} else {
			$this->objPHPExcel->getActiveSheet()->getStyle( 'A3' )->applyFromArray(
				array_merge( $this->styleTitrePrincipalVertical, $this->styleBordureNoirAutour ) );
		}


		// =============================================================================
		// Recherche les relations entre les Actifs Primordiaux et les Risques.
		$requete = $this->prepareSQL(
			'SELECT 
			apr.apr_id, 
			rcs.rcs_id 
			FROM apsp_apr_spp AS "apsp"
			LEFT JOIN apr_actifs_primordiaux AS "apr" ON apr.apr_id = apsp.apr_id 
			LEFT JOIN rcs_risques_cartographies AS "rcs" ON rcs.spp_id = apsp.spp_id 
			LEFT JOIN spcr_spp_crs AS "spcr" ON spcr.spp_id = apsp.spp_id 
			WHERE apr.crs_id = :crs_id AND spcr.crs_id = :crs_id AND rcs_a_verifier = FALSE '
		);
			
		$Resultat_Jointures = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);

		// Parcours les Actifs et met à jour l'Edition
		foreach( $Resultat_Jointures as $Occurrence ) {
			$CelluleCourante = $Indice_Risques[$Occurrence->rcs_id] . $Indice_Actifs_Primordiaux[$Occurrence->apr_id];

			$this->objPHPExcel->getActiveSheet()->setCellValue($CelluleCourante, 'X');
			$this->objPHPExcel->getActiveSheet()->getStyle($CelluleCourante)->applyFromArray($this->styleTextToutCentre);
		}


		// =====================================================
		// Ajoute et ajuste un titre pour annoncer les Risques.
		$_Ligne = $Derniere_Ligne + 2; // Numéro de la ligne de Base (relative à la dernière ligne des Actifs Primoridaux)
		$CelluleCourante = 'C' . $_Ligne;

		$this->objPHPExcel->getActiveSheet()->setCellValue($CelluleCourante, 'Risques');

		if ( $Derniere_Colonne > 2 ) {
			$FinCellule = $this->xlColumnValue( $Derniere_Colonne ) . $_Ligne;

			$this->objPHPExcel->getActiveSheet()->mergeCells( $CelluleCourante . ':' . $FinCellule );
			$this->objPHPExcel->getActiveSheet()->getStyle( $CelluleCourante . ':' . $FinCellule )->applyFromArray( $this->styleTitrePrincipalHorizontal );

			$_Ligne += 1;
			$CelluleCourante = 'C' . $_Ligne;
			$FinCellule = $this->xlColumnValue( $Derniere_Colonne ) . $_Ligne;
			$this->objPHPExcel->getActiveSheet()->getStyle( $CelluleCourante . ':' . $FinCellule )->applyFromArray( $this->styleBordureNoirAutourGrisInterieur );
		} else {
			$this->objPHPExcel->getActiveSheet()->getStyle( $CelluleCourante )->applyFromArray(
				array_merge( $this->styleBordureNoirAutour, $this->styleTitrePrincipalHorizontal ) );

			$_Ligne += 1;
			$CelluleCourante = 'C' . $_Ligne;
			$this->objPHPExcel->getActiveSheet()->getStyle( $CelluleCourante )->applyFromArray( $this->styleBordureNoirAutour );
		}

		$Colonne = 3; // Numéro de la colonne courante

		foreach ($Liste_Risques as $Occurrence) {
			$CelluleCourante = $this->xlColumnValue( $Colonne ) . $_Ligne;

			$this->objPHPExcel->getActiveSheet()->setCellValue( $CelluleCourante, 'R' . $Occurrence->rcs_code );

			$this->objPHPExcel->getActiveSheet()->getColumnDimension( $this->xlColumnValue( $Colonne ) )->setWidth( 5 );

			$this->objPHPExcel->getActiveSheet()->getStyle( $CelluleCourante )->applyFromArray( $this->styleTitreNomVertical );

			$Indice_Risques[$Occurrence->rcs_id] = $this->xlColumnValue( $Colonne );

			$Colonne += 1;
		}

		$_Ligne += 1;


		// ==============================================
		// Construit la colonne des Evénements Redoutés.
		$requete = $this->prepareSQL(
			'SELECT ' .
			'evr_id, ' .
			'evr_libelle ' .
			'FROM evr_evenements_redoutes AS "evr" ' .
			'WHERE evr.crs_id = :crs_id ' .
			'ORDER BY evr_libelle '
		);
			
		$Liste = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);

		$_Colonne_Titre_Principal = 'A'; // Colonne de Base

		// Ajoute un titre principal pour annoncer les Actifs Primordiaux.
		$this->objPHPExcel->getActiveSheet()->setCellValue( $_Colonne_Titre_Principal . $_Ligne, 'Evénements Redoutés' );

		$Ligne = $_Ligne; // Numéro de la ligne courante
		$Derniere_Ligne = count( $Liste ) + $_Ligne; // Dernière ligne de la Matrice

		$_Colonne_Titre = $this->xlColumnValue( $this->xlColumnValue( $_Colonne_Titre_Principal ) + 1 );
		$_Colonne_Donnees = $this->xlColumnValue( $this->xlColumnValue( $_Colonne_Titre ) + 1 );

		foreach ($Liste as $Occurrence) {
			$Derniere_Ligne = $Ligne;
			$DebutCellule = $_Colonne_Titre . $Ligne;

			$_Debut_Cellule = $_Colonne_Donnees . $Ligne;
			$FinCellule = $this->xlColumnValue( $Derniere_Colonne ) . $Ligne;

			$this->objPHPExcel->getActiveSheet()->getStyle( $_Debut_Cellule . ':' . $FinCellule )->applyFromArray(
				$this->styleBordureNoirAutourGrisInterieur );				

			$this->objPHPExcel->getActiveSheet()->setCellValue( $DebutCellule, $Occurrence->evr_libelle );
			$this->objPHPExcel->getActiveSheet()->getStyle( $DebutCellule )
				->applyFromArray( array_merge( $this->styleTitreNomHorizontal, $this->styleBordureNoirAutour ) )
				->getAlignment()->setWrapText( TRUE );

			$Indice_Evenements[ $Occurrence->evr_id ] = $Ligne;

			$Ligne += 1;
		}

		// Ajuste la taille de la colonne des Evénements Redoutés (Titre et Nom).
		$this->objPHPExcel->getActiveSheet()->getColumnDimension( $_Colonne_Titre_Principal )->setAutoSize( TRUE );
		$this->objPHPExcel->getActiveSheet()->getColumnDimension( $_Colonne_Titre )->setWidth( 40 );
		
		if ( $Derniere_Ligne > $_Ligne ) {
			$this->objPHPExcel->getActiveSheet()->mergeCells( $_Colonne_Titre_Principal . $_Ligne . ':' . $_Colonne_Titre_Principal . $Derniere_Ligne );

			$this->objPHPExcel->getActiveSheet()->getStyle( $_Colonne_Titre_Principal . $_Ligne . ':' . $_Colonne_Titre_Principal . $Derniere_Ligne )
				->applyFromArray( array_merge( $this->styleTitrePrincipalVertical, $this->styleBordureNoirAutour ) );
		} else {
			$this->objPHPExcel->getActiveSheet()->getStyle( $_Colonne_Titre_Principal . $_Ligne )->applyFromArray(
				array_merge( $this->styleTitrePrincipalVertical, $this->styleBordureNoirAutour ) );
		}


		// =============================================================================
		// Recherche les relations entre les Evénements Redoutés et les Risques.
		$requete = $this->prepareSQL(
			'SELECT ' .
			'evr.evr_id, ' .
			'rcs.rcs_id ' .
			'FROM evr_evenements_redoutes AS "evr" ' .
			'RIGHT JOIN rcev_rcs_evr AS "rcev" ON rcev.evr_id = evr.evr_id ' .
			'LEFT JOIN rcs_risques_cartographies AS "rcs" ON rcs.rcs_id = rcev.rcs_id ' .
			'WHERE evr.crs_id = :crs_id AND rcs_a_verifier = FALSE '
		);
			
		$Resultat_Jointures = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);

		// Parcours les Actifs et met à jour l'Edition
		foreach( $Resultat_Jointures as $Occurrence ) {
			$CelluleCourante = $Indice_Risques[ $Occurrence->rcs_id ] . $Indice_Evenements[ $Occurrence->evr_id ];

			$this->objPHPExcel->getActiveSheet()->setCellValue($CelluleCourante, 'X');
			$this->objPHPExcel->getActiveSheet()->getStyle($CelluleCourante)->applyFromArray($this->styleTextToutCentre);
		}

		return TRUE;
	}


	/* ================================================================================== */
	
	public function editionRisquesMesures( $crs_id ) {
	/**
	* Edite les Mesures ainsi que leur répartition sur les Risques.
	*
	* @author Pierre-Luc MARY
	* @date 2014-11-26
	*
	* @param[in] $crs_id ID de la Cartographie à utiliser
	*
	* @return Renvoi vrai si les informations ont bien été éditées.
	*			
	*/
		include_once( DIR_LIBRAIRIES . '/Class_CartographiesRisques_PDO.inc.php' );

		$objCartographie = new CartographiesRisques();


		$Titre_Onglet = 'Risques-Mesures';

		// Création d'un nouvel onglet et lui donne un nom.
		$this->objPHPExcel->createSheet()->setTitle( $Titre_Onglet );

		// Mise en page de l'onglet.
		$this->objPHPExcel->setActiveSheetIndexByName( $Titre_Onglet )->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$this->objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

		// Initialise le haut et le bas de page de l'onglet courant.
		$this->initialiseHautBasOnglet( $Titre_Onglet );

		// Répète sur chaque page les lignes 1 à 3.
		$this->objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 2);


		// ==============================================
		// Construit les lignes des Risques.
		$requete = $this->prepareSQL(
			'SELECT ' .
			'rcs.rcs_id, ' .
			'rcs.rcs_code, ' .
			'rcs.mgn_id ' .
			'FROM rcs_risques_cartographies AS "rcs" ' .
		    'LEFT JOIN spcr_spp_crs AS "spcr" ON spcr.spp_id = rcs.spp_id AND spcr.crs_id = rcs.crs_id ' .
			'LEFT JOIN spp_supports AS "spp" ON spp.spp_id = spcr.spp_id ' .
		    'WHERE spcr.crs_id = :crs_id AND rcs_a_verifier = FALSE '
		);
			
		$Liste_Risques = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);

		$_Ligne = 3; // Ligne de Base
		$_Colonne_Titre_Principal = 'A'; // Colonne de Base
		$_Colonne_Titre = $this->xlColumnValue( $this->xlColumnValue( $_Colonne_Titre_Principal ) + 1 ); // Colonne de Base
		$_Colonne_Donnees = $this->xlColumnValue( $this->xlColumnValue( $_Colonne_Titre ) + 1 ); // Colonne de Base
		$Derniere_Ligne = count( $Liste_Risques ) + 2 ; // Dernière ligne de la Matrice (+ 1 prend en compte le décalage du titre)

		
		// Ajoute et ajuste un titre pour annoncer les Risques.
		$CelluleCourante = $_Colonne_Titre_Principal . $_Ligne;

		$this->objPHPExcel->getActiveSheet()->setCellValue($CelluleCourante, 'Risques');

		if ( $Derniere_Ligne > $_Ligne ) {
			$FinCellule = $_Colonne_Titre_Principal . $Derniere_Ligne;

			$this->objPHPExcel->getActiveSheet()->mergeCells( $CelluleCourante . ':' . $FinCellule );

			$this->objPHPExcel->getActiveSheet()->getStyle( $CelluleCourante . ':' . $FinCellule )->applyFromArray(
				array_merge( $this->styleTitrePrincipalVertical, $this->styleBordureNoirAutour ) );
		} else {
			$this->objPHPExcel->getActiveSheet()->getStyle( $CelluleCourante )->applyFromArray(
				array_merge( $this->styleTitrePrincipalVertical, $this->styleBordureNoirAutour ) );
		}

		$Ligne = $_Ligne; // Numéro de la colonne courante

		foreach ($Liste_Risques as $Occurrence) {
			$CelluleCourante = $_Colonne_Titre . $Ligne;

			$objRichText = new PHPExcel_RichText();

			$objTexte1 = $objRichText->createTextRun( 'R' . $Occurrence->rcs_code );
			$objTexte1->getFont()->setBold( TRUE );

			$objRichText->createText( ' - ' );

			$Menace = $objCartographie->recupererLibelleMenace( $crs_id, $Occurrence->mgn_id, $this->Langue );

			$objTexte2 = $objRichText->createTextRun( $Menace );
			$objTexte2->getFont()->setItalic( TRUE );
			$objTexte2->getFont()->setColor( new PHPExcel_Style_Color( 'FFACACAC' ) );

			$this->objPHPExcel->getActiveSheet()->getCell( $CelluleCourante )->setValue( $objRichText );

			$this->objPHPExcel->getActiveSheet()->getStyle( $CelluleCourante )->applyFromArray(
				array_merge( $this->styleTitreNomHorizontal, $this->styleBordureNoirAutour ) );

			$Indice_Risques[$Occurrence->rcs_id] = $Ligne;

			$Ligne += 1;
		}

		$this->objPHPExcel->getActiveSheet()->getColumnDimension( $_Colonne_Titre )->setAutoSize( TRUE );


		// ==============================================
		// Construit les colonnes des Mesures.
		$sql = 'SELECT DISTINCT ' .
			'mgr.mgr_id, mgr.mgr_code, rlb.lbr_libelle AS "mgr_libelle", ' .
			'mcr.mcr_libelle ' .
			'FROM rcs_risques_cartographies AS "rcs" ' .
			'LEFT JOIN mcr_mesures_cartographies AS "mcr" ON mcr.rcs_id = rcs.rcs_id ' .
			'LEFT JOIN mgr_mesures_generiques AS "mgr" ON mgr.mgr_id = mcr.mgr_id ' .
			'LEFT JOIN lbr_libelles_referentiel AS "rlb" ON rlb.lbr_code = mgr_code AND rlb.lng_id = :langue ' .
			'WHERE rcs.crs_id = :crs_id ' .
			'AND rcs_a_verifier = FALSE ' .
			'AND mgr.mgr_id IS NOT NULL ' .
			'ORDER BY mgr.mgr_code ';
		$requete = $this->prepareSQL( $sql );
			
		$Liste_Mesures = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->bindSQL($requete, ':langue', $this->Langue, self::LANGUE_TYPE, self::LANGUE_LENGTH)
			->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);

		if ( $Liste_Mesures != [] ) {
			// Boucle de dédoublonage.
			$Precedant = '';
			$Tmp = [];
			foreach ( $Liste_Mesures as $Occurrence ) {
				if ( $Occurrence->mgr_code == $Precedant ) continue;

				$Precedant = $Occurrence->mgr_code;
				$Tmp[] = $Occurrence;
			}
			$Liste_Mesures = $Tmp;

			if ( is_array( $Liste_Mesures ) ) uasort( $Liste_Mesures, 'mesureSort' );

			$_Ligne_Titre = 1; // Numéro de la ligne de Base du Titre
			$_Ligne_Noms = $_Ligne_Titre + 1; // Numéro de la ligne de Base des Noms
			$_Ligne_Donnees = $_Ligne_Noms + 1; // Numéro de la ligne de Base des Données
			$_Colonne = 'C';

			$Colonne = $this->xlColumnValue( $_Colonne ); // Numéro de la ligne courante
			$Derniere_Colonne = count( $Liste_Mesures ) + ($this->xlColumnValue( $_Colonne ) - 1); // Dernière ligne de la Matrice

			// Ajoute un titre principal pour annoncer les Actifs Primordiaux.
			$this->objPHPExcel->getActiveSheet()->setCellValue( $_Colonne . $_Ligne_Titre, 'Mesures' );

			if ( $Derniere_Colonne > $this->xlColumnValue( $_Colonne ) ) {
				$DebutCellule = $_Colonne . $_Ligne_Titre;
				$FinCellule = $this->xlColumnValue( $Derniere_Colonne ) . $_Ligne_Titre;

				$this->objPHPExcel->getActiveSheet()->mergeCells( $DebutCellule . ':' . $FinCellule );
				$this->objPHPExcel->getActiveSheet()->getStyle( $DebutCellule . ':' . $FinCellule )->applyFromArray(
					array_merge( $this->styleTitrePrincipalHorizontal, $this->styleBordureNoirAutour ) );
			}

			$this->objPHPExcel->getActiveSheet()->getRowDimension( $_Ligne_Noms )->setRowHeight( 300 );


			foreach ($Liste_Mesures as $Occurrence) {
				$ColonneCourante = $this->xlColumnValue( $Colonne );
				$CelluleCourante = $ColonneCourante . $_Ligne_Noms;

				if ( $Occurrence->mcr_libelle == '' ) {
					$Libelle = $Occurrence->mgr_libelle;
				} else {
					$Libelle = $Occurrence->mcr_libelle;
				}

				$this->objPHPExcel->getActiveSheet()->getStyle( $CelluleCourante )
					->applyFromArray( array_merge( $this->styleTitreNomVertical, $this->styleBordureNoirAutour ) )
					->getAlignment()->setWrapText( TRUE );

				$objRichText = new PHPExcel_RichText();

				$objTexte1 = $objRichText->createTextRun( $Occurrence->mgr_code );
				$objTexte1->getFont()->setBold( TRUE );

				$objRichText->createText( ' - ' );

				$objTexte2 = $objRichText->createTextRun( $Libelle );
				$objTexte2->getFont()->setItalic( TRUE );
				$objTexte2->getFont()->setColor( new PHPExcel_Style_Color( 'FFACACAC' ) );

				$this->objPHPExcel->getActiveSheet()->getCell( $CelluleCourante )->setValue( $objRichText );

				$this->objPHPExcel->getActiveSheet()->getColumnDimension( $ColonneCourante )->setWidth( 8 );

				$Indice_Mesures[ $Occurrence->mgr_id ] = $ColonneCourante;

				$Colonne += 1;
			}

			if ( isset( $ColonneCourante ) ) {
				$this->objPHPExcel->getActiveSheet()->getStyle( $_Colonne . $_Ligne_Donnees . ':' . $ColonneCourante . $Derniere_Ligne )
					->applyFromArray( array_merge( $this->styleBordureNoirPartout, $this->styleTextToutCentre ) )
					->getAlignment()->setWrapText( TRUE );
			}


			// =============================================================================
			// Recherche les relations entre les Risques et les Mesures.
			$requete = $this->prepareSQL(
				'SELECT ' .
				'mcr.mgr_id, rcs.rcs_id ' .
				'FROM rcs_risques_cartographies AS "rcs" ' .
				'LEFT JOIN mcr_mesures_cartographies AS "mcr" ON mcr.rcs_id = rcs.rcs_id ' .
				'RIGHT JOIN mgr_mesures_generiques AS "mgr" ON mgr.mgr_id = mcr.mgr_id ' .
			    'WHERE rcs.crs_id = :crs_id AND rcs.rcs_a_verifier = FALSE '
			);
				
			$Resultat_Jointures = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
				->executeSQL($requete)
				->fetchAll(PDO::FETCH_CLASS);

			// Parcours les Actifs et met à jour l'Edition
			foreach( $Resultat_Jointures as $Occurrence ) {
				$CelluleCourante = $Indice_Mesures[$Occurrence->mgr_id] . $Indice_Risques[$Occurrence->rcs_id];

				$this->objPHPExcel->getActiveSheet()->setCellValue($CelluleCourante, 'X');
			}
		}

		return TRUE;
	}


	/* ================================================================================== */
	
	public function editionActifsMesures( $crs_id ) {
	/**
	* Edite les Actifs Primordiaux ainsi que leur répartition sur les Risques.
	*
	* @author Pierre-Luc MARY
	* @date 2014-11-26
	*
	* @param[in] $crs_id ID de la Cartographie à utiliser
	*
	* @return Renvoi vrai si les informations ont bien été éditées.
	*			
	*/
		$Titre_Onglet = 'Actifs-Mesures';

		// Création d'un nouvel onglet et lui donne un nom.
		$this->objPHPExcel->createSheet()->setTitle( $Titre_Onglet );

		// Mise en page de l'onglet.
		$this->objPHPExcel->setActiveSheetIndexByName( $Titre_Onglet )->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$this->objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

		// Initialise le haut et le bas de page de l'onglet courant.
		$this->initialiseHautBasOnglet( $Titre_Onglet );

		// Répète sur chaque page les lignes 1 à 3.
		$this->objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 2);


		// ==============================================
		// Construit les colonnes des Mesures.
		$requete = $this->prepareSQL(
			'SELECT DISTINCT ' .
			'mgr.mgr_id, mgr.mgr_code, rlb.lbr_libelle AS "mgr_libelle", ' .
			'mcr.mcr_libelle ' .
			'FROM spp_supports AS "spp" ' .
			'LEFT JOIN rcs_risques_cartographies AS "rcs" ON rcs.spp_id = spp.spp_id ' .
			'LEFT JOIN mcr_mesures_cartographies AS "mcr" ON mcr.rcs_id = rcs.rcs_id ' .
			'LEFT JOIN mgr_mesures_generiques AS "mgr" ON mgr.mgr_id = mcr.mgr_id ' .
			'LEFT JOIN lbr_libelles_referentiel AS "rlb" ON rlb.lbr_code = mgr_code AND rlb.lng_id = :langue ' .
		    //'LEFT JOIN spcr_spp_crs AS "spcr" ON spcr.spp_id = spp.spp_id ' .
		    'WHERE rcs.crs_id = :crs_id AND rcs_a_verifier = FALSE ' .
			'AND mgr.mgr_id IS NOT NULL ' .
			'ORDER BY mgr.mgr_code '
		);
			
		$Liste_Mesures = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->bindSQL($requete, ':langue', $this->Langue, self::LANGUE_TYPE, self::LANGUE_LENGTH)
			->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);

		if ( $Liste_Mesures != [] ) {
			// Boucle de dédoublonage.
			$Precedant = '';
			$Tmp = [];

			foreach ( $Liste_Mesures as $Occurrence ) {
				if ( $Occurrence->mgr_code == $Precedant ) continue;

				$Precedant = $Occurrence->mgr_code;
				$Tmp[] = $Occurrence;
			}
			$Liste_Mesures = $Tmp;

			uasort( $Liste_Mesures, 'mesureSort' );

			$_Ligne_Titre = 1; // Numéro de la ligne de Base du Titre
			$_Ligne_Noms = $_Ligne_Titre + 1; // Numéro de la ligne de Base des Noms
			$_Ligne_Donnees = $_Ligne_Noms + 1; // Numéro de la ligne de Base des Données
			$_Colonne = 'C';

			$Colonne = $this->xlColumnValue( $_Colonne ); // Numéro de la ligne courante
			$Derniere_Colonne = count( $Liste_Mesures ) + ($this->xlColumnValue( $_Colonne ) - 1); // Dernière ligne de la Matrice

			// Ajoute un titre principal pour annoncer les Actifs Primordiaux.
			$this->objPHPExcel->getActiveSheet()->setCellValue( $_Colonne . $_Ligne_Titre, 'Mesures' );

			if ( $Derniere_Colonne > $this->xlColumnValue( $_Colonne ) ) {
				$DebutCellule = $_Colonne . $_Ligne_Titre;
				$FinCellule = $this->xlColumnValue( $Derniere_Colonne ) . $_Ligne_Titre;

				$this->objPHPExcel->getActiveSheet()->mergeCells( $DebutCellule . ':' . $FinCellule );
				$this->objPHPExcel->getActiveSheet()->getStyle( $DebutCellule . ':' . $FinCellule )->applyFromArray(
					array_merge( $this->styleTitrePrincipalHorizontal, $this->styleBordureNoirAutour ) );
			}

			$this->objPHPExcel->getActiveSheet()->getRowDimension( $_Ligne_Noms )->setRowHeight( 300 );


			foreach ($Liste_Mesures as $Occurrence) {
				$ColonneCourante = $this->xlColumnValue( $Colonne );
				$CelluleCourante = $ColonneCourante . $_Ligne_Noms;

				if ( $Occurrence->mcr_libelle == '' ) {
					$Libelle = $Occurrence->mgr_libelle;
				} else {
					$Libelle = $Occurrence->mcr_libelle;
				}

				$this->objPHPExcel->getActiveSheet()->getStyle( $CelluleCourante )
					->applyFromArray( array_merge( $this->styleTitreNomVertical, $this->styleBordureNoirAutour ) )
					->getAlignment()->setWrapText( TRUE );

				$objRichText = new PHPExcel_RichText();

				$objTexte1 = $objRichText->createTextRun( $Occurrence->mgr_code );
				$objTexte1->getFont()->setBold( TRUE );

				$objRichText->createText( ' - ' );

				$objTexte2 = $objRichText->createTextRun( $Libelle );
				$objTexte2->getFont()->setItalic( TRUE );
				$objTexte2->getFont()->setColor( new PHPExcel_Style_Color( 'FFACACAC' ) );

				$this->objPHPExcel->getActiveSheet()->getCell( $CelluleCourante )->setValue( $objRichText );

				$this->objPHPExcel->getActiveSheet()->getColumnDimension( $ColonneCourante )->setWidth( 8 );

				$Indice_Mesures[ $Occurrence->mgr_id ] = $ColonneCourante;

				$Colonne += 1;
			}


			// ==============================================
			// Construit la colonne des Actifs Primordiaux.
			$requete = $this->prepareSQL(
				'SELECT ' .
				'rlb.lbr_libelle AS "apr_type", ' .
				'apr_id, ' .
				'apr_nom ' .
				'FROM apr_actifs_primordiaux AS "apr" ' .
				'LEFT JOIN lbr_libelles_referentiel AS "rlb" ON rlb.lbr_code = \'APR_TYPE_\'||apr_type_code AND rlb.lng_id = :langue ' .
				'WHERE apr.crs_id = :crs_id ' .
				'ORDER BY apr_type DESC, apr_nom '
			);
				
			$Liste_Actifs_Primordiaux = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
				->bindSQL($requete, ':langue', $this->Langue, self::LANGUE_TYPE, self::LANGUE_LENGTH)
				->executeSQL($requete)
				->fetchAll(PDO::FETCH_CLASS);

			// Ajoute un titre principal pour annoncer les Actifs Primordiaux.
			$_Colonne_Titre_Principal = 'A';
			$_Ligne = 3; // Numéro de la ligne courante

			$this->objPHPExcel->getActiveSheet()->setCellValue( $_Colonne_Titre_Principal . $_Ligne, 'Actifs Primordiaux' );

			$Ligne = $_Ligne; // Numéro de la ligne courante
			$Derniere_Ligne = count( $Liste_Actifs_Primordiaux ) + $Ligne; // Dernière ligne de la Matrice

			$Type_Primordial = ''; // Type de l'Actif Primordial courant
			$_Type_Primordial = ''; // Ancien type de l'Actif Primordial
			$_Colonne_Titre = $this->xlColumnValue( $this->xlColumnValue( $_Colonne_Titre_Principal ) + 1 );
			$_Colonne_Donnees = $this->xlColumnValue( $this->xlColumnValue( $_Colonne_Titre ) + 1 );

			foreach ($Liste_Actifs_Primordiaux as $Occurrence) {
				$Derniere_Ligne = $Ligne;
				$Type_Primordial = $Occurrence->apr_type;
				$DebutCellule = $_Colonne_Titre . $Ligne;

				if ( $_Type_Primordial != $Type_Primordial ) {
					$this->objPHPExcel->getActiveSheet()->setCellValue( $_Colonne_Titre.$Ligne, $Type_Primordial );

					// Etant le titre pour marquer la séparation
					$FinCellule = $this->xlColumnValue( $Derniere_Colonne ) . $Ligne;

					$this->objPHPExcel->getActiveSheet()->mergeCells( $DebutCellule . ':' . $FinCellule );
					$this->objPHPExcel->getActiveSheet()->getStyle( $DebutCellule . ':' . $FinCellule )->applyFromArray(
						array_merge( $this->styleTitreHorizontal, $this->styleBordureNoirAutour ) );

					$_Type_Primordial = $Type_Primordial;

					$Ligne += 1;

					$DebutCellule = $_Colonne_Titre . $Ligne;

					$_Debut_Cellule = $_Colonne_Donnees . $Ligne;
					$FinCellule = $this->xlColumnValue( $Derniere_Colonne ) . $Ligne;

					$this->objPHPExcel->getActiveSheet()->getStyle( $_Debut_Cellule . ':' . $FinCellule )->applyFromArray(
						$this->styleBordureNoirAutourGrisInterieur );				
				} else {
					$_Debut_Cellule = $_Colonne_Donnees . $Ligne;
					$FinCellule = $this->xlColumnValue( $Derniere_Colonne ) . $Ligne;

					$this->objPHPExcel->getActiveSheet()->getStyle( $_Debut_Cellule . ':' . $FinCellule )->applyFromArray(
						$this->styleBordureNoirAutourGrisInterieur );				
				}

				$this->objPHPExcel->getActiveSheet()->setCellValue( $DebutCellule, $Occurrence->apr_nom);
				$this->objPHPExcel->getActiveSheet()->getStyle( $DebutCellule )->applyFromArray(
					array_merge( $this->styleTitreNomHorizontal, $this->styleBordureNoirAutour ) );

				$Indice_Actifs_Primordiaux[ $Occurrence->apr_id ] = $Ligne;

				$Ligne += 1;
			}

			// Ajuste la taille de la colonne des Actifs Primordiaux.
			$this->objPHPExcel->getActiveSheet()->getColumnDimension( $_Colonne_Titre_Principal )->setAutoSize( TRUE );
			
			if ( $Derniere_Ligne > $_Ligne ) {
				$this->objPHPExcel->getActiveSheet()->mergeCells( $_Colonne_Titre_Principal . $_Ligne . ':' . $_Colonne_Titre_Principal . $Derniere_Ligne );

				$this->objPHPExcel->getActiveSheet()->getStyle( $_Colonne_Titre_Principal . $_Ligne . ':' . $_Colonne_Titre_Principal . $Derniere_Ligne )
					->applyFromArray( array_merge( $this->styleTitrePrincipalVertical, $this->styleBordureNoirAutour ) );
			} else {
				$this->objPHPExcel->getActiveSheet()->getStyle( 'A3' )->applyFromArray(
					array_merge( $this->styleTitrePrincipalVertical, $this->styleBordureNoirAutour ) );
			}


			// =============================================================================
			// Recherche les relations entre les Actifs Primordiaux et les Risques.
			$requete = $this->prepareSQL(
				'SELECT 
mcr.mgr_id, apr.apr_id 
FROM apr_actifs_primordiaux AS "apr" 
LEFT JOIN apsp_apr_spp AS "apsp" ON apsp.apr_id = apr.apr_id
LEFT JOIN spp_supports AS "spp" ON spp.spp_id = apsp.spp_id 
LEFT JOIN rcs_risques_cartographies AS "rcs" ON rcs.spp_id = spp.spp_id
LEFT JOIN mcr_mesures_cartographies AS "mcr" ON mcr.rcs_id = rcs.rcs_id 
RIGHT JOIN mgr_mesures_generiques AS "mgr" ON mgr.mgr_id = mcr.mgr_id 
WHERE apr.crs_id = :crs_id AND rcs.rcs_a_verifier = FALSE '
			);
				
			$Resultat_Jointures = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
				->executeSQL($requete)
				->fetchAll(PDO::FETCH_CLASS);

			// Parcours les Actifs et met à jour l'Edition
			foreach( $Resultat_Jointures as $Occurrence ) {
				$CelluleCourante = $Indice_Mesures[ $Occurrence->mgr_id ] . $Indice_Actifs_Primordiaux[ $Occurrence->apr_id ];

				$this->objPHPExcel->getActiveSheet()->setCellValue($CelluleCourante, 'X');
			}
		}

		return TRUE;
	}


	/* ================================================================================== */
	
	public function editionEvaluationRisques( $crs_id ) {
	/**
	* Edite les Actifs Primordiaux ainsi que leur répartition sur les Risques.
	*
	* @author Pierre-Luc MARY
	* @date 2014-11-26
	*
	* @param[in] $crs_id ID de la Cartographie à utiliser
	*
	* @return Renvoi vrai si les informations ont bien été éditées.
	*			
	*/
		include_once( DIR_LIBRAIRIES . '/Class_HTML.inc.php' );


		$Titre_Onglet = 'Répartition des risques';

		// Création d'un nouvel onglet et lui donne un nom.
		$this->objPHPExcel->createSheet()->setTitle( $Titre_Onglet );

		// Mise en page de l'onglet.
		$this->objPHPExcel->setActiveSheetIndexByName( $Titre_Onglet )->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$this->objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

		// Initialise le haut et le bas de page de l'onglet courant.
		$this->initialiseHautBasOnglet( $Titre_Onglet );

		// Répète sur chaque page les lignes 1 à 3.
		$this->objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 2);


		// ==============================================
		// Construit les colonnes des Vraisemblances.
		$requete = $this->prepareSQL(
			'SELECT ' .
			'vrs_poids, vrs.vrs_libelle '.
			'FROM vrs_vraisemblances_risques AS "vrs" ' .
			'ORDER BY vrs_poids '
		);
			
		$Liste_Vraisemblances = $this->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);

		$_Ligne_Titre = 1; // Numéro de la ligne de Base du Titre
		$_Ligne_Noms = $_Ligne_Titre + 1; // Numéro de la ligne de Base des Noms
		$_Ligne_Donnees = $_Ligne_Noms + 1; // Numéro de la ligne de Base des Données
		$_Colonne = 'C';

		$Colonne = $this->xlColumnValue( $_Colonne ); // Numéro de la ligne courante
		$Derniere_Colonne = count( $Liste_Vraisemblances ) + ($this->xlColumnValue( $_Colonne ) - 1); // Dernière ligne de la Matrice

		// Ajoute un titre principal pour annoncer les Actifs Primordiaux.
		$this->objPHPExcel->getActiveSheet()->setCellValue( $_Colonne . $_Ligne_Titre, 'Vraisemblances' );

		if ( $Derniere_Colonne > $this->xlColumnValue( $_Colonne ) ) {
			$DebutCellule = $_Colonne . $_Ligne_Titre;
			$FinCellule = $this->xlColumnValue( $Derniere_Colonne ) . $_Ligne_Titre;

			$this->objPHPExcel->getActiveSheet()->mergeCells( $DebutCellule . ':' . $FinCellule );
			$this->objPHPExcel->getActiveSheet()->getStyle( $DebutCellule . ':' . $FinCellule )->applyFromArray(
				array_merge( $this->styleTitrePrincipalHorizontal, $this->styleBordureNoirAutour ) );
		}


		foreach ($Liste_Vraisemblances as $Occurrence) {
			$ColonneCourante = $this->xlColumnValue( $Colonne );
			$CelluleCourante = $ColonneCourante . $_Ligne_Noms;

			$this->objPHPExcel->getActiveSheet()->setCellValue( $CelluleCourante, $Occurrence->vrs_libelle );

			$this->objPHPExcel->getActiveSheet()->getColumnDimension( $ColonneCourante )->setAutoSize( TRUE );

			$Indice_Vraisemblances[ $Occurrence->vrs_poids ] = $ColonneCourante;

			$Colonne += 1;
		}

		$DebutCellule = $_Colonne . $_Ligne_Noms;
		$FinCellule = $this->xlColumnValue( $Derniere_Colonne ) . $_Ligne_Noms;

		$this->objPHPExcel->getActiveSheet()->getStyle( $DebutCellule . ':' . $FinCellule )
			->applyFromArray( array_merge( $this->styleTitreNomHorizontal, $this->styleTextToutCentre, $this->styleBordureNoirAutourGrisInterieur ) )
			->getAlignment()->setWrapText( TRUE );


		// ==============================================
		// Construit la colonne des Impacts.
		$requete = $this->prepareSQL(
			'SELECT ' .
			'gri_poids, gri.gri_libelle '.
			'FROM gri_grilles_impact AS "gri" ' .
			'ORDER BY gri_poids '
		);
			
		$Liste_Impacts = $this->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);

		// Ajoute un titre principal pour annoncer les Actifs Primordiaux.
		$_Colonne_Titre_Principal = 'A';
		$_Ligne = 3; // Numéro de la ligne courante

		$this->objPHPExcel->getActiveSheet()->setCellValue( $_Colonne_Titre_Principal . $_Ligne, 'Impacts' );

		$Ligne = $_Ligne; // Numéro de la ligne courante
		$Derniere_Ligne = count( $Liste_Impacts ) + $Ligne; // Dernière ligne de la Matrice

		$Type_Primordial = ''; // Type de l'Actif Primordial courant
		$_Type_Primordial = ''; // Ancien type de l'Actif Primordial
		$_Colonne_Titre = $this->xlColumnValue( $this->xlColumnValue( $_Colonne_Titre_Principal ) + 1 );
		$_Colonne_Donnees = $this->xlColumnValue( $this->xlColumnValue( $_Colonne_Titre ) + 1 );
		$_ent_id = '';

		foreach ($Liste_Impacts as $Occurrence) {
			$Derniere_Ligne = $Ligne;
			$DebutCellule = $_Colonne_Titre . $Ligne;

			$_Debut_Cellule = $_Colonne_Donnees . $Ligne;
			$FinCellule = $this->xlColumnValue( $Derniere_Colonne ) . $Ligne;

			$this->objPHPExcel->getActiveSheet()->getStyle( $_Debut_Cellule . ':' . $FinCellule )->applyFromArray(
				$this->styleBordureNoirAutourGrisInterieur );

			$this->objPHPExcel->getActiveSheet()->setCellValue( $DebutCellule, $Occurrence->gri_libelle);
			$this->objPHPExcel->getActiveSheet()->getStyle( $DebutCellule )->applyFromArray(
				array_merge( $this->styleTitreNomHorizontal, $this->styleBordureNoirAutour ) );

			$Indice_Impacts[ $Occurrence->gri_poids ] = $Ligne;

			$Ligne += 1;
		}

		// Ajuste la taille de la colonne des Impacts.
		$this->objPHPExcel->getActiveSheet()->getColumnDimension( $_Colonne_Titre_Principal )->setAutoSize( TRUE );
		
		if ( $Derniere_Ligne > $_Ligne ) {
			$this->objPHPExcel->getActiveSheet()->mergeCells( $_Colonne_Titre_Principal . $_Ligne . ':' . $_Colonne_Titre_Principal . $Derniere_Ligne );

			$this->objPHPExcel->getActiveSheet()->getStyle( $_Colonne_Titre_Principal . $_Ligne . ':' . $_Colonne_Titre_Principal . $Derniere_Ligne )
				->applyFromArray( array_merge( $this->styleTitrePrincipalVerticalSpecial, $this->styleBordureNoirAutour ) );
		} else {
			$this->objPHPExcel->getActiveSheet()->getStyle( 'A3' )->applyFromArray(
				array_merge( $this->styleTitrePrincipalVertical, $this->styleBordureNoirAutour ) );
		}


		// =============================================================================
		// Récupère la représentation des niveaux de risque.
		$requete = $this->prepareSQL(
			'SELECT ' .
			'rnr_code_couleur, rnr_debut_poids, rnr_fin_poids '.
			'FROM rnr_representation_niveaux_risque AS "rnr" '
		);
			
		$NiveauxRisque = $this->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);


		$sql = 'SELECT gri.gri_poids||\'-\'||vrs.vrs_poids AS "car_code", car.car_poids ' .
			'FROM car_criteres_appreciation_risques AS "car" ' .
			'LEFT JOIN vrs_vraisemblances_risques AS "vrs" ON vrs.vrs_id = car.vrs_id ' .
			'LEFT JOIN gri_grilles_impact AS "gri" ON gri.gri_id = car.gri_id ';

		$requete = $this->prepareSQL($sql);

		$_Tmp = $this->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);

		$MatriceCriteresAppreciationRisques = [];

		foreach ( $_Tmp as $Occurrence ) {
			$MatriceCriteresAppreciationRisques[ $Occurrence->car_code ] = $Occurrence->car_poids;
		}


		// Parcours les Niveaux de Risque et met à jour l'Edition
		foreach( $Indice_Impacts as $Poids_Impact => $idxImpact ) {
			foreach ( $Indice_Vraisemblances as $Poids_Vraisemblance => $idxVraisemblance ) {
				$CelluleCourante = $idxVraisemblance . $idxImpact;


				// Mettre en place la bonne couleur.
				$Index = $Poids_Impact.'-'.$Poids_Vraisemblance;

				if ( ! array_key_exists( $Index, $MatriceCriteresAppreciationRisques ) ) {
					$PoidsRisque = $Poids_Impact + $Poids_Vraisemblance;
				} else {
					$PoidsRisque = $MatriceCriteresAppreciationRisques[$Index];
				}

				$Couleur_Risque = '';

				foreach ($NiveauxRisque as $NiveauRisque) {
					if ( $PoidsRisque >= $NiveauRisque->rnr_debut_poids and $PoidsRisque <= $NiveauRisque->rnr_fin_poids ) {
						$Couleur_Risque = $NiveauRisque->rnr_code_couleur;
					}
				}


				$this->objPHPExcel->getActiveSheet()->getStyle( $CelluleCourante )
					->applyFromArray(
						array_merge(
							$this->styleTextToutCentre,
							array( 
								'font' => array(
									'color' => array(
						 				'argb' => 'FF' . HTML::calculCouleurCelluleHexa( $Couleur_Risque )
						 				)
									),
								'fill' => array(
						 			'type' => PHPExcel_Style_Fill::FILL_SOLID,
						 			'startcolor' => array(
						 				'argb' => 'FF' . $Couleur_Risque
						 				)
						 		)
				 			)
			 			)
			 		);


				$requete = $this->prepareSQL(
					'SELECT ' .
					'rcs.rcs_code ' .
					//'FROM spp_supports AS "spp" ' .
					'FROM rcs_risques_cartographies AS "rcs" ' .
					'LEFT JOIN vrs_vraisemblances_risques AS "vrs" ON vrs.vrs_id = rcs.vrs_id ' .
					'LEFT JOIN gri_grilles_impact AS "gri" ON gri.gri_id = rcs.gri_id ' .
				    //'LEFT JOIN spcr_spp_crs AS "spcr" ON spcr.spp_id = spp.spp_id ' .
				    'WHERE rcs.crs_id = :crs_id ' .
					'AND gri.gri_poids = :gri_poids AND vrs.vrs_poids = :vrs_poids '
				);
					
				$Risques_Associes = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
					->bindSQL($requete, ':gri_poids', $Poids_Impact, self::ID_TYPE)
					->bindSQL($requete, ':vrs_poids', $Poids_Vraisemblance, self::ID_TYPE)
					->executeSQL($requete)
					->fetchAll(PDO::FETCH_CLASS);

				uasort( $Risques_Associes, 'codeRisqueSort' );

				$Liste_Risques = '';
				foreach( $Risques_Associes as $Risque ) {
					if ( $Liste_Risques != '' ) $Liste_Risques .= "\n";

					$Liste_Risques .= 'R' . $Risque->rcs_code;
				}

				$this->objPHPExcel->getActiveSheet()
					->setCellValue( $CelluleCourante, $Liste_Risques )
					->getStyle( $CelluleCourante )->getAlignment()->setWrapText( TRUE );
			}
		}

		return TRUE;
	}



	/* ================================================================================== */

	/* Gestion du Document Word */

	/* ================================================================================== */

	protected function Word_section( $Orientation, $PageGarde = FALSE ) {
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-EditionsRisques.php' );

		$Orientation = strtolower( $Orientation );
		if ( $Orientation == 'paysage' ) {
			$_Orientation = array('orientation' => 'landscape');
			$this->Largeur_Section = $this->CentimeterToTwip(24.5);
		} else {
			$_Orientation = array();
			$this->Largeur_Section = $this->CentimeterToTwip(16);
		}

		$styleTable = array('borderSize'=>6, 'borderColor'=>'006699', 'cellMargin'=>80);
		$this->objPHPWord->addTableStyle('styleTableauEntete', $styleTable);

		$section = $this->objPHPWord->createSection( $_Orientation );

		$header = $section->createHeader();
		$table = $header->addTable('styleTableauEntete');
		$table->addRow();

		if ( $PageGarde == TRUE ) {
			$table->addCell( $this->Largeur_Section )
				->addImage( DIR_IMAGES . '/Logo-Loxense.png', array('width'=>283, 'height'=>113, 'align'=>'center'));
		} else {
			$table->addCell( $this->CentimeterToTwip(4.5) )
				->addImage( DIR_IMAGES . '/Logo-Loxense.png', array('width'=>136, 'height'=>56, 'align'=>'center'));

			$Cellule = $table->addCell( $this->CentimeterToTwip(20), array('valign'=>'center') );
			$Cellule->addText( utf8_decode($this->TitreDocument), array('size'=>10) );
			$Cellule->addText( utf8_decode($this->SujetDocument), array('size'=>9, 'bold'=>true) );
		}

		$header->addTextBreak();


		$footer = $section->createFooter();
		$table = $footer->addTable();
		$table->addRow();
		$table->addCell( $this->Largeur_Section / 3 )->addPreserveText('{PAGE} / {NUMPAGES}', array('size'=>10), array('align'=>'left'));
		$table->addCell( $this->Largeur_Section / 3 )->addPreserveText( $L_Diffusion_Restreinte, array('size'=>10), array('align'=>'center'));
		$table->addCell( $this->Largeur_Section / 3 )->addPreserveText('{SAVEDATE \@ "dd/MM/yyyy HH:MM" \* MERGEFORMAT}', array('size'=>10), array('align'=>'right'));

		return $section;
	}
	

	public function Word_page_garde( $crs_id, $Niveau, $Orientation, $Limitation, $Organisation, $Flag_Chapitres ) {
	/**
	* Fabrique la page de garde du document Word
	*
	* @author Pierre-Luc MARY
	* @date 2017-08-22
	*
	* @param[in] $crs_id ID de la Cartographie à utiliser
	* @param[in] $Niveau Donne la profondeur du titre (si besoin)
	* @param[in] $Orientation Indique l'orientation du document (portrait ou paysage)
	* @param[in] $Limitation Indique une limitation à l'affichage (si nécessaire)
	* @param[in] $Organisation Indique s'il y a des sous-chapitres à gérer dans ce chapitre
	* @param[in] $Flag_Chapitres Indicateur pour déterminer si l'utilisateur a sélectionné les chapitres dans son rapport.
	*
	* @return Renvoi vrai si les informations ont bien été éditées.
	*			
	*/
		include_once( DIR_LIBRAIRIES . '/Class_HTML.inc.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-EditionsRisques.php' );


		$this->SectionWordCourante = $this->Word_section( $Orientation, TRUE );

		$this->SectionWordCourante->addTextBreak(9);

		// Création du texte de la page
		$table = $this->SectionWordCourante->addTable();
		$table->addRow();
		$table->addCell($this->Largeur_Section, array('borderLeftColor'=>'006699', 'borderTopColor'=>'006699', 'borderRightColor'=>'006699',
			'borderLeftSize'=>6, 'borderTopSize'=>6, 'borderRightSize'=>6))->addText( utf8_decode($this->TitreDocument), 'TitreDocument' );
		$table->addRow();
		$table->addCell($this->Largeur_Section, array('borderLeftColor'=>'006699', 'borderBottomColor'=>'006699', 'borderRightColor'=>'006699',
			'borderLeftSize'=>6, 'borderBottomSize'=>6, 'borderRightSize'=>6))->addText( utf8_decode($this->SujetDocument), 'SujetDocument' );

		$this->SectionWordCourante->addTextBreak();

		$this->SectionWordCourante->addText( utf8_decode($this->VersionLoxense), 'VersionLoxense' );

		//$this->SectionWordCourante->addPageBreak();

		return TRUE;
	}


	/* ================================================================================== */
	
	public function Word_table_matieres( $crs_id, $Niveau, $Orientation, $Limitation, $Organisation, $Flag_Chapitres ) {
	/**
	* Fabrique la table des matières du document Word
	*
	* @author Pierre-Luc MARY
	* @date 2017-08-22
	*
	* @param[in] $crs_id ID de la Cartographie à utiliser
	* @param[in] $Niveau Donne la profondeur du titre (si besoin)
	* @param[in] $Orientation Indique l'orientation du document (portrait ou paysage)
	* @param[in] $Limitation Indique une limitation à l'affichage (si nécessaire)
	* @param[in] $Organisation Indique s'il y a des sous-chapitres à gérer dans ce chapitre
	* @param[in] $Flag_Chapitres Indicateur pour déterminer si l'utilisateur a sélectionné les chapitres dans son rapport.
	*
	* @return Renvoi vrai si les informations ont bien été éditées.
	*			
	*/
		include_once( DIR_LIBRAIRIES . '/Class_HTML.inc.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-EditionsRisques.php' );

		$this->SectionWordCourante = $this->Word_section( $Orientation );

		$this->SectionWordCourante->addText( utf8_decode( $L_Table_Matieres ), array( 'size'=>16, 'bold'=>true, 'borderBottomSize'=>6, 'borderBottomColor'=>'006699' ),
			array( 'borderBottomSize'=>6, 'borderBottomColor'=>'006699' ) );
		$this->SectionWordCourante->addTextBreak(2);

		$this->SectionWordCourante->addTOC( $this->TOCFontStyle );

		return TRUE;
	}


	/* ================================================================================== */
	
	public function Word_perimetre_cartographie( $crs_id, $Niveau, $Orientation, $Limitation, $Organisation, $Flag_Chapitres ) {
	/**
	* Fabrique le titre qui matérialise le périmètre de la cartographie du document Word
	*
	* @author Pierre-Luc MARY
	* @date 2017-08-22
	*
	* @param[in] $crs_id ID de la Cartographie à utiliser
	* @param[in] $Niveau Donne la profondeur du titre (si besoin)
	* @param[in] $Orientation Indique l'orientation du document (portrait ou paysage)
	* @param[in] $Limitation Indique une limitation à l'affichage (si nécessaire)
	* @param[in] $Organisation Indique s'il y a des sous-chapitres à gérer dans ce chapitre
	* @param[in] $Flag_Chapitres Indicateur pour déterminer si l'utilisateur a sélectionné les chapitres dans son rapport.
	*
	* @return Renvoi vrai si les informations ont bien été éditées.
	*			
	*/
		include_once( DIR_LIBRAIRIES . '/Class_HTML.inc.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-EditionsRisques.php' );

		$this->SectionWordCourante = $this->Word_section( utf8_decode($Orientation) );

		$this->SectionWordCourante->addTitle( utf8_decode($L_Perimetre_Cartographie), 1);

		return TRUE;
	}


	/* ================================================================================== */
	
	public function Word_presentation( $crs_id, $Niveau, $Orientation, $Limitation, $Organisation, $Flag_Chapitres ) {
	/**
	* Fabrique le titre et le texte qui présente la cartographie du document Word
	*
	* @author Pierre-Luc MARY
	* @date 2017-08-22
	*
	* @param[in] $crs_id ID de la Cartographie à utiliser
	* @param[in] $Niveau Donne la profondeur du titre (si besoin)
	* @param[in] $Orientation Indique l'orientation du document (portrait ou paysage)
	* @param[in] $Limitation Indique une limitation à l'affichage (si nécessaire)
	* @param[in] $Organisation Indique s'il y a des sous-chapitres à gérer dans ce chapitre
	* @param[in] $Flag_Chapitres Indicateur pour déterminer si l'utilisateur a sélectionné les chapitres dans son rapport.
	*
	* @return Renvoi vrai si les informations ont bien été éditées.
	*			
	*/
		include_once( DIR_LIBRAIRIES . '/Class_HTML.inc.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-EditionsRisques.php' );

		$this->SectionWordCourante->addTitle( utf8_decode($L_Presentation), 2);

		$sql = 'SELECT crs_objectifs FROM crs_cartographies_risques WHERE crs_id = :crs_id ';

		$requete = $this->prepareSQL( $sql );
			
		$Objectif = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->executeSQL($requete)
			->fetchObject()->crs_objectifs;

		$this->SectionWordCourante->addText( strip_tags(utf8_decode($Objectif)) );

		return TRUE;
	}


	/* ================================================================================== */
	
	public function Word_valorisation_actifs_primordiaux( $crs_id, $Niveau, $Orientation, $Limitation, $Organisation, $Flag_Chapitres ) {
	/**
	* Fabrique le titre du chapitre et appelle toutes les sous-fonctions (voir feuille Excel)
	*
	* @author Pierre-Luc MARY
	* @date 2017-08-22
	*
	* @param[in] $crs_id ID de la Cartographie à utiliser
	* @param[in] $Niveau Donne la profondeur du titre (si besoin)
	* @param[in] $Orientation Indique l'orientation du document (portrait ou paysage)
	* @param[in] $Limitation Indique une limitation à l'affichage (si nécessaire)
	* @param[in] $Organisation Indique s'il y a des sous-chapitres à gérer dans ce chapitre
	* @param[in] $Flag_Chapitres Indicateur pour déterminer si l'utilisateur a sélectionné les chapitres dans son rapport.
	*
	* @return Renvoi vrai si les informations ont bien été éditées.
	*			
	*/
		if ( !isset($Flag_Chapitres['flag_apr']) or $Flag_Chapitres['flag_apr'] != 'o' ) return TRUE;

		include_once( DIR_LIBRAIRIES . '/Class_HTML.inc.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-EditionsRisques.php' );

		$this->SectionWordCourante->addTitle( utf8_decode($L_Valorisation_Actifs_Primordiaux), 2);

		foreach( explode(',', $Organisation) as $_Element ) {
			$SousFonction = 'Word_Sous_Fonction_' . strtolower(trim($_Element));
			$this->$SousFonction( $crs_id, $Niveau, $Orientation, $Limitation, $Organisation, $Flag_Chapitres );
		}

		return TRUE;
	}


	/* ================================================================================== */
	
	public function Word_Sous_Fonction_informations( $crs_id, $Niveau, $Orientation, $Limitation, $Organisation, $Flag_Chapitres ) {
	/**
	* Fabrique le sous-chapitre lié à la valorisation des actifs primordiaux de type "Information".
	*
	* @author Pierre-Luc MARY
	* @date 2017-08-22
	*
	* @param[in] $crs_id ID de la Cartographie à utiliser
	* @param[in] $Niveau Donne la profondeur du titre (si besoin)
	* @param[in] $Orientation Indique l'orientation du document (portrait ou paysage)
	* @param[in] $Limitation Indique une limitation à l'affichage (si nécessaire)
	* @param[in] $Organisation Indique s'il y a des sous-chapitres à gérer dans ce chapitre
	* @param[in] $Flag_Chapitres Indicateur pour déterminer si l'utilisateur a sélectionné les chapitres dans son rapport.
	*
	* @return Renvoi vrai si les informations ont bien été éditées.
	*			
	*/
		if ( !isset($Flag_Chapitres['flag_apr']) or $Flag_Chapitres['flag_apr'] != 'o' ) return;

		include_once( DIR_LIBRAIRIES . '/Class_HTML.inc.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-EditionsRisques.php' );


		// Récupère et affiche les Actifs Primordiaux de type "Information"
		$this->SectionWordCourante->addTitle( utf8_decode($L_Informations), 3);

		$sql = 'SELECT apr_code||\' - \'||apr_nom AS "apr_nom", cva_code||\' - \'||cva_nom AS "cva_nom", pea_cotation||\' - \'||pea_nom AS "pea_nom", vac_justification
FROM apr_actifs_primordiaux AS "apr"
LEFT JOIN vac_valorisation_actifs AS "vac" ON vac.apr_id = apr.apr_id
LEFT JOIN pea_poids_evaluation_actifs AS "pea" ON pea.pea_id = vac.pea_id
LEFT JOIN cva_criteres_valorisation_actifs AS "cva" ON cva.cva_id = pea.cva_id
WHERE crs_id = :crs_id AND apr_type_code = 1
ORDER BY apr_nom, cva_ordre ';

		$requete = $this->prepareSQL( $sql );

		$Liste = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);


		if ( $Liste != array() ) {
			$this->objPHPWord->addTableStyle('styleTableauAPR_I', $this->styleTable);

			$table = $this->SectionWordCourante->addTable('styleTableauAPR_I');

			// Entête du tableau.
			$table->addRow();
			$table->addCell($this->Largeur_Section / 4, $this->styleCellTitleBorder)->addText(utf8_decode($L_Actif), $this->styleTitleFont);
			$table->addCell($this->Largeur_Section / 4, $this->styleCellTitleBorder)->addText(utf8_decode($L_Critere_Valorisation), $this->styleTitleFont);
			$table->addCell($this->Largeur_Section / 4, $this->styleCellTitleBorder)->addText(utf8_decode($L_Valorisation), $this->styleTitleFont);
			$table->addCell($this->Largeur_Section / 4, $this->styleCellTitleBorder)->addText(utf8_decode($L_Justification), $this->styleTitleFont);

			$Precedant = '';
			$Courant = '';

			// Corps du tableau.
			foreach ($Liste as $Occurrence) {
				$table->addRow();

				if ( $Precedant == utf8_decode($Occurrence->apr_nom) ) {
					$Courant = '';
				} else {
					$Precedant = utf8_decode($Occurrence->apr_nom);
					$Courant = $Precedant;
				}

				$table->addCell($this->Largeur_Section / 4, $this->styleCellBorder)->addText($Courant);
				$table->addCell($this->Largeur_Section / 4, $this->styleCellBorder)->addText(utf8_decode($Occurrence->cva_nom));
				$table->addCell($this->Largeur_Section / 4, $this->styleCellBorder)->addText(utf8_decode($Occurrence->pea_nom));
				$table->addCell($this->Largeur_Section / 4, $this->styleCellBorder)->addText(utf8_decode($Occurrence->vac_justification));
			}
		} else {
			$this->SectionWordCourante->addText(utf8_decode($L_Aucune));
		}


		return TRUE;
	}


	/* ================================================================================== */
	
	public function Word_Sous_Fonction_fonctions( $crs_id, $Niveau, $Orientation, $Limitation, $Organisation, $Flag_Chapitres ) {
	/**
	* Fabrique le sous-chapitre lié à la valorisation des actifs primordiaux de type "Fonction".
	*
	* @author Pierre-Luc MARY
	* @date 2017-08-22
	*
	* @param[in] $crs_id ID de la Cartographie à utiliser
	* @param[in] $Niveau Donne la profondeur du titre (si besoin)
	* @param[in] $Orientation Indique l'orientation du document (portrait ou paysage)
	* @param[in] $Limitation Indique une limitation à l'affichage (si nécessaire)
	* @param[in] $Organisation Indique s'il y a des sous-chapitres à gérer dans ce chapitre
	* @param[in] $Flag_Chapitres Indicateur pour déterminer si l'utilisateur a sélectionné les chapitres dans son rapport.
	*
	* @return Renvoi vrai si les informations ont bien été éditées.
	*			
	*/
		if ( !isset($Flag_Chapitres['flag_apr']) or $Flag_Chapitres['flag_apr'] != 'o' ) return;

		include_once( DIR_LIBRAIRIES . '/Class_HTML.inc.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-EditionsRisques.php' );


		// Récupère et affiche les Actifs Primordiaux de type "Fonction"
		$this->SectionWordCourante->addTitle( utf8_decode($L_Fonctions), 3);

		$sql = 'SELECT apr_code||\' - \'||apr_nom AS "apr_nom", cva_code||\' - \'||cva_nom AS "cva_nom", pea_cotation||\' - \'||pea_nom AS "pea_nom", vac_justification
FROM apr_actifs_primordiaux AS "apr"
LEFT JOIN vac_valorisation_actifs AS "vac" ON vac.apr_id = apr.apr_id
LEFT JOIN pea_poids_evaluation_actifs AS "pea" ON pea.pea_id = vac.pea_id
LEFT JOIN cva_criteres_valorisation_actifs AS "cva" ON cva.cva_id = pea.cva_id
WHERE crs_id = :crs_id AND apr_type_code = 2
ORDER BY apr_nom, cva_ordre ';

		$requete = $this->prepareSQL( $sql );

		$Liste = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);


		if ( $Liste != array() ) {
			$this->objPHPWord->addTableStyle('styleTableauAPR_F', $this->styleTable);

			$table = $this->SectionWordCourante->addTable('styleTableauAPR_F');

			// Entête du tableau.
			$table->addRow();
			$table->addCell($this->Largeur_Section / 4, $this->styleCellTitleBorder)->addText(utf8_decode($L_Actif), $this->styleTitleFont);
			$table->addCell($this->Largeur_Section / 4, $this->styleCellTitleBorder)->addText(utf8_decode($L_Critere_Valorisation), $this->styleTitleFont);
			$table->addCell($this->Largeur_Section / 4, $this->styleCellTitleBorder)->addText(utf8_decode($L_Valorisation), $this->styleTitleFont);
			$table->addCell($this->Largeur_Section / 4, $this->styleCellTitleBorder)->addText(utf8_decode($L_Justification), $this->styleTitleFont);

			$Precedant = '';
			$Courant = '';

			foreach ($Liste as $Occurrence) {
				$table->addRow();

				if ( $Precedant == utf8_decode($Occurrence->apr_nom) ) {
					$Courant = '';
				} else {
					$Precedant = utf8_decode($Occurrence->apr_nom);
					$Courant = $Precedant;
				}

				$table->addCell($this->Largeur_Section / 4, $this->styleCellBorder)->addText($Courant);
				$table->addCell($this->Largeur_Section / 4, $this->styleCellBorder)->addText(utf8_decode($Occurrence->cva_nom));
				$table->addCell($this->Largeur_Section / 4, $this->styleCellBorder)->addText(utf8_decode($Occurrence->pea_nom));
				$table->addCell($this->Largeur_Section / 4, $this->styleCellBorder)->addText(utf8_decode($Occurrence->vac_justification));
			}
		} else {
			$this->SectionWordCourante->addText(utf8_decode($L_Aucune));
		}


		return TRUE;
	}


	/* ================================================================================== */
	
	public function Word_Sous_Fonction_tableau_synthese( $crs_id, $Niveau, $Orientation, $Limitation, $Organisation, $Flag_Chapitres ) {
	/**
	* Fabrique le sous-chapitre lié au tableau de synthèse.
	*
	* @author Pierre-Luc MARY
	* @date 2017-08-22
	*
	* @param[in] $crs_id ID de la Cartographie à utiliser
	* @param[in] $Niveau Donne la profondeur du titre (si besoin)
	* @param[in] $Orientation Indique l'orientation du document (portrait ou paysage)
	* @param[in] $Limitation Indique une limitation à l'affichage (si nécessaire)
	* @param[in] $Organisation Indique s'il y a des sous-chapitres à gérer dans ce chapitre
	* @param[in] $Flag_Chapitres Indicateur pour déterminer si l'utilisateur a sélectionné les chapitres dans son rapport.
	*
	* @return Renvoi vrai si les informations ont bien été éditées.
	*			
	*/
		include_once( DIR_LIBRAIRIES . '/Class_HTML.inc.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-EditionsRisques.php' );


		// Calcul de la taille des colonnes.
		$sql = 'SELECT cva_nom
FROM cva_criteres_valorisation_actifs AS "cva" 
ORDER BY cva_ordre ';

		$requete = $this->prepareSQL( $sql );

		$Criteres = $this->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);

		$Total_Criteres = count($Criteres);

		$Largeur_Colonne_Secondaire = 500;
		$Largeur_Colonne_Principale = $this->Largeur_Section - ($Total_Criteres * $Largeur_Colonne_Secondaire);


		// Récupère et affiche les Actifs Primordiaux avec leurs critères de valorisation.
		$this->SectionWordCourante->addTitle( utf8_decode($L_Tableau_Synthese_Valorisation_Actifs), 3);


		// Récupère les informations
		$sql = 'SELECT apr_code||\' - \'||apr_nom AS "apr_nom", pea_cotation
FROM apr_actifs_primordiaux AS "apr"
LEFT JOIN vac_valorisation_actifs AS "vac" ON vac.apr_id = apr.apr_id
LEFT JOIN pea_poids_evaluation_actifs AS "pea" ON pea.pea_id = vac.pea_id
LEFT JOIN cva_criteres_valorisation_actifs AS "cva" ON cva.cva_id = pea.cva_id
WHERE crs_id = :crs_id and apr_type_code = 1
ORDER BY apr_nom, cva_ordre ';

		$requete = $this->prepareSQL( $sql );

		$Liste = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);

		// Récupère les fonctions
		$sql = 'SELECT apr_code||\' - \'||apr_nom AS "apr_nom", pea_cotation
FROM apr_actifs_primordiaux AS "apr"
LEFT JOIN vac_valorisation_actifs AS "vac" ON vac.apr_id = apr.apr_id
LEFT JOIN pea_poids_evaluation_actifs AS "pea" ON pea.pea_id = vac.pea_id
LEFT JOIN cva_criteres_valorisation_actifs AS "cva" ON cva.cva_id = pea.cva_id
WHERE crs_id = :crs_id and apr_type_code = 2
ORDER BY apr_nom, cva_ordre ';

		$requete = $this->prepareSQL( $sql );

		$Liste1 = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);


		$this->objPHPWord->addTableStyle('styleTableauAPR_T', $this->styleTable);

		// Entête du tableau "informations".
		$table = $this->SectionWordCourante->addTable('styleTableauAPR_T');

		$table->addRow($this->CentimeterToTwip('3.3'));
		$table->addCell($Largeur_Colonne_Principale,
			array_merge($this->styleCellTitle2Border, array('valign'=>'bottom')))
			->addText(utf8_decode($L_Informations), array('color'=>'FFFFFF', 'bold'=>TRUE));

		foreach ($Criteres as $Critere) {
			$table->addCell($Largeur_Colonne_Secondaire,
				array_merge($this->styleCellTitleBorder, array('valign'=>'left', 'textDirection'=>PHPWord_Style_Cell::TEXT_DIR_BTLR)))
				->addText(utf8_decode($Critere->cva_nom), $this->styleTitleFont);
		}


		if ( $Liste != array() ) {
			$Precedant = '';

			foreach ($Liste as $Occurrence) {
				if ( $Precedant == utf8_decode($Occurrence->apr_nom) ) {
					$table->addCell($Largeur_Colonne_Secondaire,
						array_merge($this->styleCellBorder, array('align'=>'center')))
						->addText($Occurrence->pea_cotation);
				} else {
					$Precedant = utf8_decode($Occurrence->apr_nom);

					$table->addRow();

					$table->addCell($Largeur_Colonne_Principale,
						array_merge($this->styleCellBorder, array('align'=>'right')))
						->addText(utf8_decode($Occurrence->apr_nom));

					$table->addCell($Largeur_Colonne_Secondaire,
						array_merge($this->styleCellBorder, array('align'=>'center')))
						->addText(utf8_decode($Occurrence->pea_cotation));
				}
			}
		} else {
			$this->SectionWordCourante->addText(utf8_decode($L_Aucune));
		}


		// Entête du tableau "fonctions".
		$table = $this->SectionWordCourante->addTable('styleTableauAPR_T');

		$table->addRow($this->CentimeterToTwip('3.3'));
		$table->addCell($Largeur_Colonne_Principale,
			array_merge($this->styleCellTitle2Border, array('valign'=>'bottom')))
			->addText(utf8_decode($L_Fonctions), array('color'=>'FFFFFF', 'bold'=>TRUE));

		foreach ($Criteres as $Critere) {
			$table->addCell($Largeur_Colonne_Secondaire,
				array_merge($this->styleCellTitleBorder, array('valign'=>'left', 'textDirection'=>PHPWord_Style_Cell::TEXT_DIR_BTLR)))
				->addText(utf8_decode($Critere->cva_nom), $this->styleTitleFont);
		}


		if ( $Liste1 != array() ) {
			$Precedant = '';

			foreach ($Liste1 as $Occurrence) {
				if ( $Precedant == utf8_decode($Occurrence->apr_nom) ) {
					$table->addCell($Largeur_Colonne_Secondaire,
						array_merge($this->styleCellBorder, array('align'=>'center')))
						->addText($Occurrence->pea_cotation);
				} else {
					$Precedant = utf8_decode($Occurrence->apr_nom);

					$table->addRow();

					$table->addCell($Largeur_Colonne_Principale,
						array_merge($this->styleCellBorder, array('align'=>'right')))
						->addText(utf8_decode($Occurrence->apr_nom));

					$table->addCell($Largeur_Colonne_Secondaire,
						array_merge($this->styleCellBorder, array('align'=>'center')))
						->addText(utf8_decode($Occurrence->pea_cotation));
				}
			}
		} else {
			$this->SectionWordCourante->addText(utf8_decode($L_Aucune));
		}


		// ---------------------------------
		// Valorisation de la cartographie.
		$this->SectionWordCourante->addTitle( utf8_decode($L_Valorisation_Cartographie), 3);

		$sql = 'SELECT cva_code||\' - \'||cva_nom AS "cva_nom", MAX(pea_poids) AS "pea_poids"
FROM apr_actifs_primordiaux AS "apr"
LEFT JOIN vac_valorisation_actifs AS "vac" ON vac.apr_id = apr.apr_id
LEFT JOIN pea_poids_evaluation_actifs AS "pea" ON pea.pea_id = vac.pea_id
LEFT JOIN cva_criteres_valorisation_actifs AS "cva" ON cva.cva_id = pea.cva_id
WHERE crs_id = :crs_id
GROUP BY cva_code,cva_nom, cva_ordre
ORDER BY cva_ordre ';

		$requete = $this->prepareSQL( $sql );

		$Liste = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);


		$table = $this->SectionWordCourante->addTable('styleTableauAPR_T');

		// Entête.
		$table->addRow();

		$table->addCell($Largeur_Colonne_Principale,
			array_merge($this->styleCellTitleBorder, array('align'=>'left')))
			->addText(utf8_decode($L_Critere_Valorisation), array('color'=>'FFFFFF'));

		$table->addCell($Largeur_Colonne_Secondaire * 3,
			array_merge($this->styleCellTitleBorder, array('align'=>'center')))
			->addText(utf8_decode($L_Poids), array('color'=>'FFFFFF'));


		// Corps.
		foreach ($Liste as $Occurrence) {
			$table->addRow();

			$table->addCell($Largeur_Colonne_Principale,
				array_merge($this->styleCellBorder, array('align'=>'right')))
				->addText(utf8_decode($Occurrence->cva_nom));

			$table->addCell($Largeur_Colonne_Secondaire,
				array_merge($this->styleCellBorder, array('align'=>'center')))
				->addText(utf8_decode($Occurrence->pea_poids));
		}


		return TRUE;
	}


	/* ================================================================================== */
	
	public function Word_localisation_actifs_primordiaux( $crs_id, $Niveau, $Orientation, $Limitation, $Organisation, $Flag_Chapitres ) {
	/**
	* Fabrique le chapitre qui identifie sur quels actifs supports sont les actifs primordiaux
	*
	* @author Pierre-Luc MARY
	* @date 2017-08-22
	*
	* @param[in] $crs_id ID de la Cartographie à utiliser
	* @param[in] $Niveau Donne la profondeur du titre (si besoin)
	* @param[in] $Orientation Indique l'orientation du document (portrait ou paysage)
	* @param[in] $Limitation Indique une limitation à l'affichage (si nécessaire)
	* @param[in] $Organisation Indique s'il y a des sous-chapitres à gérer dans ce chapitre
	* @param[in] $Flag_Chapitres Indicateur pour déterminer si l'utilisateur a sélectionné les chapitres dans son rapport.
	*
	* @return Renvoi vrai si les informations ont bien été éditées.
	*			
	*/
		if ( !isset($Flag_Chapitres['flag_apr_spp']) or $Flag_Chapitres['flag_apr_spp'] != 'o' ) return;

		include_once( DIR_LIBRAIRIES . '/Class_HTML.inc.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_libelles_generiques.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-EditionsRisques.php' );


		$this->SectionWordCourante = $this->Word_section( utf8_decode($Orientation) );

		// Récupère et affiche les Actifs Primordiaux avec leurs critères de valorisation.
		$this->SectionWordCourante->addTitle( utf8_decode($L_Localisation_Actifs_Primordiaux), 2);


		// Entête du tableau
		$Largeur_Colonne = $this->Largeur_Section / 2;

		$table = $this->SectionWordCourante->addTable('styleTableauAPR_T');

		$table->addRow();

		$table->addCell($Largeur_Colonne, array_merge($this->styleCellTitleBorder, array('align'=>'left')))
			->addText(utf8_decode($L_Actifs_Primordiaux), $this->styleTitleFont);

		$table->addCell($Largeur_Colonne, array_merge($this->styleCellTitle3Border, array('align'=>'center')))
			->addText(utf8_decode($L_Actifs_Supports), $this->styleTitleFont);


		// Corps du tableau
		$SQL = 'SELECT apr_code||\' - \'||apr_nom AS "apr_nom", lbr1.lbr_libelle AS "apr_type", spp_code||\' - \'||spp_nom AS "spp_nom", lbr2.lbr_libelle AS "spp_type"
FROM apr_actifs_primordiaux AS "apr"
LEFT JOIN lbr_libelles_referentiel AS "lbr1" ON lbr1.lbr_code = \'APR_TYPE_\'||apr.apr_type_code AND lbr1.lng_id = :lng_id
LEFT JOIN apsp_apr_spp AS "apsp" ON apsp.apr_id = apr.apr_id
LEFT JOIN spp_supports AS "spp" ON spp.spp_id = apsp.spp_id
LEFT JOIN tsp_types_support AS "tsp" ON tsp.tsp_id = spp.tsp_id
LEFT JOIN lbr_libelles_referentiel AS "lbr2" ON lbr2.lbr_code = tsp_code AND lbr2.lng_id = :lng_id
WHERE apr.crs_id = :crs_id
ORDER BY apr_type,apr_nom,spp_type,spp_nom ';

		$Requete = $this->prepareSQL( $SQL );

		$Liste = $this->bindSQL( $Requete, ':crs_id', $crs_id, self::ID_TYPE )
			->bindSQL( $Requete, ':lng_id', $_SESSION['Language'], self::LANGUE_TYPE, self::LANGUE_LENGTH )
			->executeSQL( $Requete )
			->fetchAll( PDO::FETCH_CLASS );

		$APR_TYPE = '';
		$APR_NOM = '';
		$SPP_TYPE = '';

		$Total_APR = count($Liste);
		$Nbr_APR = 0;

		foreach ($Liste as $Occurrence) {
			$Nbr_APR += 1;

			if ( $APR_TYPE != $Occurrence->apr_type ) {
				$table->addRow();

				$table->addCell($Largeur_Colonne, array_merge($this->styleCellTitle2Border, array('align'=>'left')))
					->addText(utf8_decode($Occurrence->apr_type), $this->styleTitle2Font);

				$table->addCell($Largeur_Colonne, array_merge($this->styleCellTitle2Border, array('align'=>'left')))
					->addText('');

				$APR_TYPE = $Occurrence->apr_type;
			}


			if ( $APR_NOM != $Occurrence->apr_nom ) {
				$table->addRow();

				$table->addCell($Largeur_Colonne, array_merge($this->styleCellMiddleBorder, array('align'=>'left', 'borderTopSize'=>6)))
					->addText(utf8_decode($Occurrence->apr_nom));

				$table->addCell($Largeur_Colonne, array_merge($this->styleCellTitle4Border, array('align'=>'center')))
					->addText(utf8_decode($Occurrence->spp_type), $this->styleTitle2Font);

				$APR_NOM = $Occurrence->apr_nom;
				$SPP_TYPE = $Occurrence->spp_type;
			}


			if ( $SPP_TYPE != $Occurrence->spp_type ) {
				$table->addRow();

				$table->addCell($Largeur_Colonne, array_merge($this->styleCellMiddleBorder, array('align'=>'left')))
					->addText('');

				$table->addCell($Largeur_Colonne, array_merge($this->styleCellTitle4Border, array('align'=>'center')))
					->addText(utf8_decode($Occurrence->spp_type), $this->styleTitle2Font);

				$table->addRow();

				if ( $Nbr_APR == $Total_APR ) {
					$_Style = $this->styleCellBottomBorder;
				} else {
					$_Style = $this->styleCellMiddleBorder;
				}

				$table->addCell($Largeur_Colonne, array_merge($_Style, array('align'=>'left')))
					->addText('');

				$table->addCell($Largeur_Colonne, array_merge($_Style, array('align'=>'center')))
					->addText(utf8_decode($Occurrence->spp_nom));

				$SPP_TYPE = $Occurrence->spp_type;
			} else {
				$table->addRow();

				$table->addCell($Largeur_Colonne, array_merge($this->styleCellMiddleBorder, array('align'=>'left')))
					->addText('');

				$table->addCell($Largeur_Colonne, array_merge($this->styleCellMiddleBorder, array('align'=>'center')))
					->addText(utf8_decode($Occurrence->spp_nom));
			}

		}

		return TRUE;
	}


	/* ================================================================================== */
	
	public function Word_conclusion_analyse_cartographie( $crs_id, $Niveau, $Orientation, $Limitation, $Organisation, $Flag_Chapitres ) {
	/**
	* Fabrique le chapitre qui affiche juste le titre du paragraphe
	*
	* @author Pierre-Luc MARY
	* @date 2017-08-22
	*
	* @param[in] $crs_id ID de la Cartographie à utiliser
	* @param[in] $Niveau Donne la profondeur du titre (si besoin)
	* @param[in] $Orientation Indique l'orientation du document (portrait ou paysage)
	* @param[in] $Limitation Indique une limitation à l'affichage (si nécessaire)
	* @param[in] $Organisation Indique s'il y a des sous-chapitres à gérer dans ce chapitre
	* @param[in] $Flag_Chapitres Indicateur pour déterminer si l'utilisateur a sélectionné les chapitres dans son rapport.
	*
	* @return Renvoi vrai si les informations ont bien été éditées.
	*			
	*/
		include_once( DIR_LIBRAIRIES . '/Class_HTML.inc.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-EditionsRisques.php' );

		$this->SectionWordCourante = $this->Word_section( utf8_decode($Orientation) );

		// Récupère et affiche les Actifs Primordiaux avec leurs critères de valorisation.
		$this->SectionWordCourante->addTitle( utf8_decode($L_Conclusion_AR), 1);


		return TRUE;
	}


	/* ================================================================================== */
	
	public function Word_risques( $crs_id, $Niveau, $Orientation, $Limitation, $Organisation, $Flag_Chapitres ) {
	/**
	* Fabrique le chapitre qui rappelle l'ensemble des risques
	*
	* @author Pierre-Luc MARY
	* @date 2017-08-22
	*
	* @param[in] $crs_id ID de la Cartographie à utiliser
	* @param[in] $Niveau Donne la profondeur du titre (si besoin)
	* @param[in] $Orientation Indique l'orientation du document (portrait ou paysage)
	* @param[in] $Limitation Indique une limitation à l'affichage (si nécessaire)
	* @param[in] $Organisation Indique s'il y a des sous-chapitres à gérer dans ce chapitre
	* @param[in] $Flag_Chapitres Indicateur pour déterminer si l'utilisateur a sélectionné les chapitres dans son rapport.
	*
	* @return Renvoi vrai si les informations ont bien été éditées.
	*			
	*/
		include_once( DIR_LIBRAIRIES . '/Class_HTML.inc.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_libelles_generiques.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-EditionsRisques.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-AppreciationRisques.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-TraitementRisques.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-Actions.php' );


		$this->SectionWordCourante = $this->Word_section( utf8_decode($Orientation) );

		// Récupère et affiche les Actifs Primordiaux avec leurs critères de valorisation.
		$this->SectionWordCourante->addTitle( utf8_decode(ucfirst($L_Risques)), $Niveau);


		// ==========================================================
		// Vérifie si la Cartographie dispose d'Evénements Redoutés.
		$requete = $this->prepareSQL( 'SELECT count(evr_id) AS total FROM evr_evenements_redoutes AS "evr" WHERE crs_id = :crs_id ' );
			
		$Total_EVR = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->executeSQL($requete)
			->fetchObject()->total;

		if ( $Total_EVR > 0 ) { // Lister les risques associés à des Evénements Redoutés
			$SQL = 'SELECT 
rcs.rcs_id, rcs.rcs_code,
spp.spp_code||\' - \'||spp.spp_nom AS "spp_nom",
lbr1.lbr_libelle AS "spp_type",
rcs.rcs_scenario,
vrs.vrs_poids, vrs_trt.vrs_poids AS "vrs_poids_trt",
mgn_code,
pea.pea_cotation, pea.pea_nom, 
lbr2.lbr_libelle AS "mgn_libelle",
gri.gri_poids, gri_trt.gri_poids AS "gri_poids_trt",
car.car_poids AS "niveau_brut", car2.car_poids AS "niveau_net",
lbr6.lbr_libelle AS "rcs_type_traitement",
lbr7.lbr_libelle AS "rcs_couverture",
rcs.rcs_justif_risque_residuel,
evr.evr_libelle,
coalesce((CASE WHEN coalesce(car.car_poids,0) = 0 THEN vrs.vrs_poids + gri.gri_poids ELSE car.car_poids END)::int,0) AS "poids_brut",
string_agg( DISTINCT CASE WHEN rcvl.rcv_libelle IS NULL THEN (SELECT lbr3.lbr_libelle FROM lbr_libelles_referentiel AS "lbr3" WHERE lbr3.lbr_code = vln.vln_code AND lbr3.lng_id = :lng_id) ELSE rcvl.rcv_libelle END, \';;;\' )  AS "vln_libelles",
string_agg( DISTINCT apr_code||\' - \'||apr_nom, \';;;\' )  AS "apr_noms",
string_agg( DISTINCT lbr8.lbr_libelle, \';;;\' )  AS "srm_noms",
string_agg( DISTINCT (SELECT lbr5.lbr_libelle FROM lbr_libelles_referentiel AS "lbr5" WHERE lbr5.lbr_code = ign.ign_code AND lbr5.lng_id = :lng_id), \';;;\' )  AS "ign_noms"
FROM evr_evenements_redoutes AS "evr"
LEFT JOIN evap_evr_apr AS "evap" ON evap.evr_id = evr.evr_id
LEFT JOIN apr_actifs_primordiaux AS "apr" ON apr.apr_id = evap.apr_id
LEFT JOIN apsp_apr_spp AS "apsp" ON apsp.apr_id = evap.apr_id
LEFT JOIN spp_supports AS "spp" ON spp.spp_id = apsp.spp_id
LEFT JOIN rcs_risques_cartographies AS "rcs" ON rcs.spp_id = spp.spp_id AND rcs.crs_id = :crs_id
LEFT JOIN vrs_vraisemblances_risques AS "vrs" ON vrs.vrs_id = rcs.vrs_id 
LEFT JOIN mgn_menaces_generiques AS "mgn" ON mgn.mgn_id = rcs.mgn_id 
LEFT JOIN gri_grilles_impact AS "gri" ON gri.gri_id = evr.gri_id 
LEFT JOIN pea_poids_evaluation_actifs AS "pea" ON pea.pea_id = rcs.rcs_cotation_actif 
LEFT JOIN car_criteres_appreciation_risques AS "car" ON car.gri_id = rcs.gri_id AND car.vrs_id = rcs.vrs_id
LEFT JOIN car_criteres_appreciation_risques AS "car2" ON car2.gri_id = rcs.gri_id_trt AND car2.vrs_id = rcs.vrs_id_trt
LEFT JOIN tsp_types_support AS "tsp" ON tsp.tsp_id = spp.tsp_id
LEFT JOIN lbr_libelles_referentiel AS "lbr1" ON lbr1.lbr_code = tsp.tsp_code AND lbr1.lng_id = :lng_id
LEFT JOIN lbr_libelles_referentiel AS "lbr2" ON lbr2.lbr_code = mgn.mgn_code AND lbr2.lng_id = :lng_id
LEFT JOIN rcvl_rcs_vln AS "rcvl" ON rcvl.rcs_id = rcs.rcs_id
LEFT JOIN vln_vulnerabilites AS "vln" ON vln.vln_id = rcvl.vln_id
LEFT JOIN evsr_evr_srm AS "evsr" ON evsr.evr_id = evr.evr_id
LEFT JOIN srm_sources_menaces AS "srm" ON srm.srm_id = evsr.srm_id
LEFT JOIN evig_evr_ign AS "evig" ON evig.evr_id = evr.evr_id
LEFT JOIN ign_impacts_generiques AS "ign" ON ign.ign_id = evig.ign_id
LEFT JOIN gri_grilles_impact AS "gri_trt" ON gri_trt.gri_id = rcs.gri_id_trt
LEFT JOIN vrs_vraisemblances_risques AS "vrs_trt" ON vrs_trt.vrs_id = rcs.vrs_id_trt
LEFT JOIN lbr_libelles_referentiel AS "lbr6" ON lbr6.lbr_code = \'RCS_TT_\'||rcs.rcs_type_traitement_code AND lbr6.lng_id = :lng_id
LEFT JOIN lbr_libelles_referentiel AS "lbr7" ON lbr7.lbr_code = \'RCS_ETAT_\'||rcs.rcs_couverture_code AND lbr7.lng_id = :lng_id
LEFT JOIN lbr_libelles_referentiel AS "lbr8" ON lbr8.lbr_code = srm.srm_code AND lbr8.lng_id = :lng_id
WHERE evr.crs_id = :crs_id ';

		if ( !isset($Flag_Chapitres['flag_risques_evalues']) or $Flag_Chapitres['flag_risques_evalues'] == 'o' )
			$SQL .= 'AND ( vrs.vrs_poids IS NOT NULL OR car.car_poids IS NOT NULL ) ';

	$SQL .= 'GROUP BY rcs.rcs_id, rcs.rcs_code, rcs.rcs_scenario,
spp.spp_code, spp.spp_nom, lbr1.lbr_libelle,
vrs.vrs_poids, vrs_trt.vrs_poids, pea.pea_cotation, pea.pea_nom, 
mgn.mgn_code,mgn_libelle,
evr.evr_libelle,
gri.gri_poids, gri_trt.gri_poids,
car.car_poids, car2.car_poids, lbr6.lbr_libelle, lbr7.lbr_libelle, rcs.rcs_justif_risque_residuel
ORDER BY poids_brut DESC, rcs_code, mgn.mgn_code ';

	if ( isset($Flag_Chapitres['flag_a_risques_max']) and $Flag_Chapitres['flag_a_risques_max'] > 0 ) $SQL .= ' LIMIT ' . $Flag_Chapitres['flag_a_risques_max'] . ' ';

		} else { // Lister les risques (sans Evénement Redouté)

			$SQL = 'SELECT 
rcs.rcs_id, rcs.rcs_code,
spp.spp_code||\' - \'||spp.spp_nom AS "spp_nom",
lbr1.lbr_libelle AS "spp_type",
rcs.rcs_scenario,
vrs.vrs_poids, vrs_trt.vrs_poids AS "vrs_poids_trt",
mgn_code,
pea.pea_cotation, pea.pea_nom, 
lbr2.lbr_libelle AS "mgn_libelle",
gri.gri_poids, gri_trt.gri_poids AS "gri_poids_trt",
car.car_poids AS "niveau_brut", car2.car_poids AS "niveau_net",
lbr6.lbr_libelle AS "rcs_type_traitement",
lbr7.lbr_libelle AS "rcs_couverture",
rcs.rcs_justif_risque_residuel,
coalesce((CASE WHEN coalesce(car.car_poids,0) = 0 THEN vrs.vrs_poids + gri.gri_poids ELSE car.car_poids END)::int,0) AS "poids_brut",
string_agg( DISTINCT CASE WHEN rcvl.rcv_libelle IS NULL THEN (SELECT lbr3.lbr_libelle FROM lbr_libelles_referentiel AS "lbr3" WHERE lbr3.lbr_code = vln.vln_code AND lbr3.lng_id = :lng_id) ELSE rcvl.rcv_libelle END, \';;;\' )  AS "vln_libelles",
string_agg( DISTINCT apr_code||\' - \'||apr_nom, \';;;\' )  AS "apr_noms", ' .
//string_agg( DISTINCT (SELECT lbr4.lbr_libelle FROM lbr_libelles_referentiel AS "lbr4" WHERE lbr4.lbr_code = srm.srm_code AND lbr4.lng_id = :lng_id), \';;;\' )  AS "srm_noms",
'string_agg( DISTINCT lbr8.lbr_libelle, \';;;\' )  AS "srm_noms",
string_agg( DISTINCT (SELECT lbr5.lbr_libelle FROM lbr_libelles_referentiel AS "lbr5" WHERE lbr5.lbr_code = ign.ign_code AND lbr5.lng_id = :lng_id), \';;;\' )  AS "ign_noms"
FROM rcs_risques_cartographies AS "rcs"
LEFT JOIN spp_supports AS "spp" ON spp.spp_id = rcs.spp_id
LEFT JOIN vrs_vraisemblances_risques AS "vrs" ON vrs.vrs_id = rcs.vrs_id 
LEFT JOIN mgn_menaces_generiques AS "mgn" ON mgn.mgn_id = rcs.mgn_id 
LEFT JOIN gri_grilles_impact AS "gri" ON gri.gri_id = rcs.gri_id 
LEFT JOIN pea_poids_evaluation_actifs AS "pea" ON pea.pea_id = rcs.rcs_cotation_actif 
LEFT JOIN car_criteres_appreciation_risques AS "car" ON car.gri_id = rcs.gri_id AND car.vrs_id = rcs.vrs_id
LEFT JOIN car_criteres_appreciation_risques AS "car2" ON car2.gri_id = rcs.gri_id_trt AND car2.vrs_id = rcs.vrs_id_trt
LEFT JOIN apsp_apr_spp AS "apsp" ON apsp.spp_id = spp.spp_id
LEFT JOIN apr_actifs_primordiaux AS "apr" ON apr.apr_id = apsp.apr_id
LEFT JOIN tsp_types_support AS "tsp" ON tsp.tsp_id = spp.tsp_id
LEFT JOIN lbr_libelles_referentiel AS "lbr1" ON lbr1.lbr_code = tsp.tsp_code AND lbr1.lng_id = :lng_id
LEFT JOIN lbr_libelles_referentiel AS "lbr2" ON lbr2.lbr_code = mgn.mgn_code AND lbr2.lng_id = :lng_id
LEFT JOIN rcvl_rcs_vln AS "rcvl" ON rcvl.rcs_id = rcs.rcs_id
LEFT JOIN vln_vulnerabilites AS "vln" ON vln.vln_id = rcvl.vln_id
LEFT JOIN rcsr_rcs_srm AS "rcsr" ON rcsr.rcs_id = rcs.rcs_id
LEFT JOIN srm_sources_menaces AS "srm" ON srm.srm_id = rcsr.srm_id
LEFT JOIN rcig_rcs_ign AS "rcig" ON rcig.rcs_id = rcs.rcs_id
LEFT JOIN ign_impacts_generiques AS "ign" ON ign.ign_id = rcig.ign_id
LEFT JOIN gri_grilles_impact AS "gri_trt" ON gri_trt.gri_id = rcs.gri_id_trt
LEFT JOIN vrs_vraisemblances_risques AS "vrs_trt" ON vrs_trt.vrs_id = rcs.vrs_id_trt
LEFT JOIN lbr_libelles_referentiel AS "lbr6" ON lbr6.lbr_code = \'RCS_TT_\'||rcs.rcs_type_traitement_code AND lbr6.lng_id = :lng_id
LEFT JOIN lbr_libelles_referentiel AS "lbr7" ON lbr7.lbr_code = \'RCS_ETAT_\'||rcs.rcs_couverture_code AND lbr7.lng_id = :lng_id
LEFT JOIN lbr_libelles_referentiel AS "lbr8" ON lbr8.lbr_code = srm.srm_code AND lbr8.lng_id = :lng_id
WHERE rcs.crs_id = :crs_id ';

			if ( !isset($Flag_Chapitres['flag_risques_evalues']) or $Flag_Chapitres['flag_risques_evalues'] == 'o' )
				$SQL .= 'AND ( vrs.vrs_poids IS NOT NULL OR car.car_poids IS NOT NULL ) ';

			$SQL .= 'GROUP BY rcs.rcs_id, rcs.rcs_code, rcs.rcs_scenario,
spp.spp_code, spp.spp_nom, lbr1.lbr_libelle,
vrs.vrs_poids, vrs_trt.vrs_poids, pea.pea_cotation, pea.pea_nom, 
mgn.mgn_code,mgn_libelle,
gri.gri_poids, gri_trt.gri_poids,
car.car_poids, car2.car_poids, lbr6.lbr_libelle, lbr7.lbr_libelle, rcs.rcs_justif_risque_residuel
ORDER BY poids_brut DESC, rcs_code, mgn.mgn_code ';

			if ( isset($Flag_Chapitres['flag_a_risques_max']) and $Flag_Chapitres['flag_a_risques_max'] > 0 ) $SQL .= ' LIMIT ' . $Flag_Chapitres['flag_a_risques_max'] . ' ';
		}
//print_r(str_replace(':lng_id', "'fr'", str_replace(':crs_id', $crs_id, $SQL)));

		$Requete = $this->prepareSQL( $SQL );

		$Liste = $this->bindSQL( $Requete, ':crs_id', $crs_id, self::ID_TYPE )
			->bindSQL( $Requete, ':lng_id', $_SESSION['Language'], self::LANGUE_TYPE, self::LANGUE_LENGTH )
			->executeSQL( $Requete )
			->fetchAll( PDO::FETCH_CLASS );

		foreach ($Liste as $Occurrence) {
			$sectionStyle = $this->SectionWordCourante->getSettings();
			
			if ( $sectionStyle->getOrientation() == 'landscape' ) $this->SectionWordCourante = $this->Word_section( 'portrait' );

			$this->SectionWordCourante->addTitle(utf8_decode(ucfirst($L_Risque).' R'.$Occurrence->rcs_code), 2);

			foreach (explode(',', $Organisation) as $Sous_Chapitre) {
				switch($Sous_Chapitre) {
				 case 'evenement_redoutes':
					$this->SectionWordCourante->addTitle(utf8_decode($L_Evenement_Redoute), 3);

					if ( $Total_EVR == 0 ) $EVR_NOM = $L_Aucun;
					else $EVR_NOM = $Occurrence->evr_libelle;

					$this->SectionWordCourante->addText(utf8_decode($EVR_NOM));

					break;

				 case 'actifs_primordiaux':
					$this->SectionWordCourante->addTitle(utf8_decode($L_Actifs_Primordiaux), 3);

					foreach ( explode(';;;',$Occurrence->apr_noms) as $_apr_nom) {
						$this->SectionWordCourante->addListItem( utf8_decode($_apr_nom), 0 );
					}

					break;

				 case 'actif_support':
					$this->SectionWordCourante->addTitle(utf8_decode($L_Actif_Support), 3);

					$this->SectionWordCourante->addListItem( utf8_decode($Occurrence->spp_nom), 0 );

					break;

				 case 'menace':
					$this->SectionWordCourante->addTitle(utf8_decode($L_Menace), 3);

					$this->SectionWordCourante->addListItem( utf8_decode($Occurrence->mgn_libelle), 0 );

					break;

				 case 'vulnerabilites':
					$this->SectionWordCourante->addTitle(utf8_decode($L_Vulnerabilites), 3);

					foreach ( explode(';;;',$Occurrence->vln_libelles) as $_vln_nom) {
						$this->SectionWordCourante->addListItem( utf8_decode($_vln_nom), 0 );
					}

					break;

				 case 'sources_menaces':
					$this->SectionWordCourante->addTitle(utf8_decode($L_Sources_Menaces), 3);

					if ( $Occurrence->srm_noms == '' ) {
						$this->SectionWordCourante->addText( utf8_decode($L_Aucune) );
					} else {
						foreach ( explode(';;;',$Occurrence->srm_noms) as $_srm_nom) {
							$this->SectionWordCourante->addListItem( utf8_decode($_srm_nom), 0 );
						}
					}

					break;

				 case 'scenario':
					$this->SectionWordCourante->addTitle(utf8_decode($L_Scenario), 3);

					if ( $Occurrence->rcs_scenario == '' ) $_Tmp = $L_Aucun;
					else $_Tmp = strip_tags($Occurrence->rcs_scenario);

					$this->SectionWordCourante->addText( utf8_decode($_Tmp) );

					break;

				 case 'impacts':
					$this->SectionWordCourante->addTitle(utf8_decode($L_Impacts), 3);

					if ( $Occurrence->ign_noms == '' ) {
						$this->SectionWordCourante->addText( utf8_decode($L_Aucun) );
					} else {
						foreach ( explode(';;;',$Occurrence->ign_noms) as $_Tmp) {
							$this->SectionWordCourante->addListItem( utf8_decode($_Tmp), 0 );
						}
					}

					break;

				 case 'evaluation':
					if ( !isset($Flag_Chapitres['flag_risques_evalues']) or $Flag_Chapitres['flag_risques_evalues'] != 'o' ) break;

					$this->SectionWordCourante->addTitle(utf8_decode($L_Evaluation_Risque_Avant_Trt), 3);

					$Largeur_Colonne = $this->Largeur_Section / 4;

					$table = $this->SectionWordCourante->addTable('styleTableauAPR_T');


					$table->addRow();

					$table->addCell($Largeur_Colonne * 2, array_merge($this->styleCellBorder, array('align'=>'left')))
						->addText(utf8_decode($L_Niveau_Vraisemblance), $this->styleTitle2Font);

					$table->addCell($Largeur_Colonne, array_merge($this->styleCellBorder, array('align'=>'center')))
						->addText(utf8_decode($Occurrence->vrs_poids));


					$table->addRow();

					$table->addCell($Largeur_Colonne * 2, array_merge($this->styleCellBorder, array('align'=>'left')))
						->addText(utf8_decode($L_Niveau_Impact), $this->styleTitle2Font);

					$table->addCell($Largeur_Colonne, array_merge($this->styleCellBorder, array('align'=>'center')))
						->addText(utf8_decode($Occurrence->gri_poids));


					$table->addRow();

					$table->addCell($Largeur_Colonne * 2, array_merge($this->styleCellBorder, array('align'=>'left')))
						->addText(utf8_decode($L_Niveau_Risque), $this->styleTitle2Font);


					if ($Occurrence->niveau_brut == '') $_Poids = $Occurrence->gri_poids+$Occurrence->vrs_poids;
					else $_Poids = $Occurrence->niveau_brut;

					$Requete = $this->prepareSQL( 'SELECT rnr_code_couleur FROM rnr_representation_niveaux_risque AS "rnr"
WHERE :poids >= rnr_debut_poids AND :poids <= rnr_fin_poids ' );

					$_Tmp = $this->bindSQL( $Requete, ':poids', $_Poids, self::POIDS_TYPE )
						->executeSQL( $Requete )
						->fetchObject();

					if ( $_Tmp == '' ) $Couleur_Cellule = 'FFFFFF';
					else $Couleur_Cellule = $_Tmp->rnr_code_couleur;

					$table->addCell($Largeur_Colonne, array_merge($this->styleCellBorder, array('align'=>'center', 'bgColor'=>$Couleur_Cellule)))
						->addText(utf8_decode($_Poids), $this->styleTitle2Font);


					$table->addRow();

					$table->addCell($Largeur_Colonne * 2, array_merge($this->styleCellBorder, array('align'=>'left')))
						->addText(utf8_decode($L_Sensibilite_Actif_affecte), $this->styleTitle2Font);

					$table->addCell($Largeur_Colonne, array_merge($this->styleCellBorder, array('align'=>'center')))
						->addText(utf8_decode($Occurrence->pea_cotation.' - '.$Occurrence->pea_nom));

					break;

				 case 'traitement':
					if ( !isset($Flag_Chapitres['flag_t_risques']) or $Flag_Chapitres['flag_t_risques'] != 'o' ) break;

					$this->SectionWordCourante->addTitle(utf8_decode($L_Traitement_Risque), 3);

					$Largeur_Colonne = $this->Largeur_Section / 4;

					$table = $this->SectionWordCourante->addTable('styleTableauAPR_T');


					$table->addRow();

					$table->addCell($Largeur_Colonne * 2, array_merge($this->styleCellBorder, array('align'=>'left')))
						->addText(utf8_decode($L_Type_Traitement), $this->styleTitle2Font);

					$table->addCell($Largeur_Colonne, array_merge($this->styleCellBorder, array('align'=>'center')))
						->addText(utf8_decode($Occurrence->rcs_type_traitement));


					$table->addRow();

					$table->addCell($Largeur_Colonne * 2, array_merge($this->styleCellBorder, array('align'=>'left')))
						->addText(utf8_decode($L_Couverture), $this->styleTitle2Font);

					$table->addCell($Largeur_Colonne, array_merge($this->styleCellBorder, array('align'=>'center')))
						->addText(utf8_decode($Occurrence->rcs_couverture));


					$table->addRow();

					$table->addCell($Largeur_Colonne * 2, array_merge($this->styleCellBorder, array('align'=>'left')))
						->addText(utf8_decode($L_Niveau_Impact.' ('.mb_strtolower($L_Apres_Trt).')'), $this->styleTitle2Font);

					$table->addCell($Largeur_Colonne, array_merge($this->styleCellBorder, array('align'=>'center')))
						->addText(utf8_decode($Occurrence->gri_poids_trt));


					$table->addRow();

					$table->addCell($Largeur_Colonne * 2, array_merge($this->styleCellBorder, array('align'=>'left')))
						->addText(utf8_decode($L_Niveau_Vraisemblance.' ('.mb_strtolower($L_Apres_Trt).')'), $this->styleTitle2Font);

					$table->addCell($Largeur_Colonne, array_merge($this->styleCellBorder, array('align'=>'center')))
						->addText(utf8_decode($Occurrence->vrs_poids_trt));


					$table->addRow();

					$table->addCell($Largeur_Colonne * 2, array_merge($this->styleCellBorder, array('align'=>'left')))
						->addText(utf8_decode($L_Niveau_Risque), $this->styleTitle2Font);


					if ($Occurrence->niveau_net == '') $_Poids = $Occurrence->gri_poids_trt+$Occurrence->vrs_poids_trt;
					else $_Poids = $Occurrence->niveau_net;

					$Requete = $this->prepareSQL( 'SELECT rnr_code_couleur FROM rnr_representation_niveaux_risque AS "rnr"
WHERE :poids >= rnr_debut_poids AND :poids <= rnr_fin_poids ' );

					$_Tmp = $this->bindSQL( $Requete, ':poids', $_Poids, self::POIDS_TYPE )
						->executeSQL( $Requete )
						->fetchObject();

					if ( $_Tmp == '' ) $Couleur_Cellule = 'FFFFFF';
					else $Couleur_Cellule = $_Tmp->rnr_code_couleur;

					$table->addCell($Largeur_Colonne, array_merge($this->styleCellBorder, array('align'=>'center', 'bgColor'=>$Couleur_Cellule)))
						->addText(utf8_decode($_Poids), $this->styleTitle2Font);


					$table->addRow();

					$table->addCell($Largeur_Colonne * 2, array_merge($this->styleCellBorder, array('align'=>'left')))
						->addText(utf8_decode($L_Risque_Residuel.' ('.mb_strtolower($L_Apres_Trt).')'), $this->styleTitle2Font);

					$table->addCell($Largeur_Colonne, array_merge($this->styleCellBorder, array('align'=>'center')))
						->addText(strip_tags(utf8_decode($Occurrence->rcs_justif_risque_residuel)));

					break;

				 case 'mesures':
					if ( (!isset($Flag_Chapitres['flag_risques_mesures']) or $Flag_Chapitres['flag_risques_mesures'] != 'o')
						or (!isset($Flag_Chapitres['flag_actions']) or $Flag_Chapitres['flag_actions'] != 'o') ) break;

					$this->SectionWordCourante = $this->Word_section( 'paysage' );

					$this->SectionWordCourante->addTitle(utf8_decode($L_Mesures_Actions), 3);

					$SQL = 'SELECT 
						mcr_libelle,
						lbr.lbr_libelle AS "mgr_libelle",
						lbr1.lbr_libelle AS "mgr_etat_libelle",
						act.act_libelle,
						lbr2.lbr_libelle AS "act_statut",
						lbr3.lbr_libelle AS "act_frequence",
						idn.idn_login, cvl.cvl_nom, cvl.cvl_prenom,
						act.act_date_debut_p, act.act_date_debut_r,
						act.act_date_fin_p, act.act_date_fin_r
						FROM mcr_mesures_cartographies AS "mcr"
						LEFT JOIN mgr_mesures_generiques AS "mgr" ON mgr.mgr_id = mcr.mgr_id
						LEFT JOIN lbr_libelles_referentiel AS "lbr" ON lbr.lbr_code = mgr_code AND lbr.lng_id = :langue
						LEFT JOIN lbr_libelles_referentiel AS "lbr1" ON lbr1.lbr_code = \'MCR_ETAT_\'||mcr_etat_code AND lbr1.lng_id = :langue
						LEFT JOIN act_actions AS "act" ON act.mcr_id = mcr.mcr_id
						LEFT JOIN lbr_libelles_referentiel AS "lbr2" ON lbr.lbr_code = act.act_statut_code AND lbr.lng_id = :langue
						LEFT JOIN lbr_libelles_referentiel AS "lbr3" ON lbr1.lbr_code = act.act_frequence_code AND lbr1.lng_id = :langue
						LEFT JOIN idn_identites AS "idn" ON idn.idn_id = act.idn_id
						LEFT JOIN cvl_civilites AS "cvl" ON cvl.cvl_id = idn.cvl_id
						WHERE mcr.rcs_id = :rcs_id ';


					$requete = $this->prepareSQL( $SQL );
//print_r(str_replace(':rcs_id', $Occurrence->rcs_id, str_replace(':langue', "'".$this->Langue."'", $SQL)));

					$Liste_Mesures = $this->bindSQL($requete, ':rcs_id', $Occurrence->rcs_id, self::ID_TYPE)
						->bindSQL($requete, ':langue', $this->Langue, self::LANGUE_TYPE, self::LANGUE_LENGTH)
						->executeSQL($requete)
						->fetchAll(PDO::FETCH_CLASS);


					$Total_Mesures = count( $Liste_Mesures );
//print_r($Total_Mesures.'<br>');

					$Largeur_Colonne = $this->Largeur_Section / 8;

					$table = $this->SectionWordCourante->addTable('styleTableauAPR_T');


					// Titre principal
					$table->addRow();

					// Partie Mesures
					$table->addCell($Largeur_Colonne, array_merge($this->styleCellTitleBorderLeft, array('align'=>'left')))
						->addText(utf8_decode($L_Mesures), $this->styleTitleFont);

					$table->addCell($Largeur_Colonne, array_merge($this->styleCellTitleBorderRight, array('align'=>'center')))
						->addText('');

					// Partie Actions
					$table->addCell($Largeur_Colonne, array_merge($this->styleCellTitle3BorderLeft, array('align'=>'left')))
						->addText(utf8_decode($L_Actions), $this->styleTitleFont);

					$table->addCell($Largeur_Colonne, array_merge($this->styleCellTitle3BorderMiddle, array('align'=>'center')))
						->addText('');

					$table->addCell($Largeur_Colonne, array_merge($this->styleCellTitle3BorderMiddle, array('align'=>'center')))
						->addText('');

					$table->addCell($Largeur_Colonne, array_merge($this->styleCellTitle3BorderMiddle, array('align'=>'center')))
						->addText('');

					$table->addCell($Largeur_Colonne, array_merge($this->styleCellTitle3BorderMiddle, array('align'=>'center')))
						->addText('');

					$table->addCell($Largeur_Colonne, array_merge($this->styleCellTitle3BorderRight, array('align'=>'center')))
						->addText('');


					// Titre secondaire
					$table->addRow();

					$table->addCell($Largeur_Colonne, array_merge($this->styleCellTitle2Border, array('align'=>'left')))
						->addText(utf8_decode($L_Libelle), $this->styleTitle2Font);

					$table->addCell($Largeur_Colonne, array_merge($this->styleCellTitle2Border, array('align'=>'left')))
						->addText(utf8_decode($L_Status), $this->styleTitle2Font);

					$table->addCell($Largeur_Colonne, array_merge($this->styleCellTitle2Border, array('align'=>'left')))
						->addText(utf8_decode($L_Libelle), $this->styleTitle2Font);

					$table->addCell($Largeur_Colonne, array_merge($this->styleCellTitle2Border, array('align'=>'left')))
						->addText(utf8_decode($L_Acteur), $this->styleTitle2Font);

					$table->addCell($Largeur_Colonne, array_merge($this->styleCellTitle2Border, array('align'=>'left')))
						->addText(utf8_decode($L_Date_Debut), $this->styleTitle2Font);

					$table->addCell($Largeur_Colonne, array_merge($this->styleCellTitle2Border, array('align'=>'left')))
						->addText(utf8_decode($L_Date_Fin), $this->styleTitle2Font);

					$table->addCell($Largeur_Colonne, array_merge($this->styleCellTitle2Border, array('align'=>'left')))
						->addText(utf8_decode($L_Frequence), $this->styleTitle2Font);

					$table->addCell($Largeur_Colonne, array_merge($this->styleCellTitle2Border, array('align'=>'left')))
						->addText(utf8_decode($L_Status), $this->styleTitle2Font);


					// Toutes les mesures et actions associées.
					foreach ($Liste_Mesures as $Mesure) {
						$table->addRow();

						if ( $Mesure->mcr_libelle == '' ) $_Tmp = $Mesure->mgr_libelle;
						else $_Tmp = $Mesure->mcr_libelle;

						$table->addCell($Largeur_Colonne, array_merge($this->styleCellBorder, array('align'=>'left')))
							->addText(utf8_decode($_Tmp));

						$table->addCell($Largeur_Colonne, array_merge($this->styleCellBorder, array('align'=>'left')))
							->addText(utf8_decode($Mesure->mgr_etat_libelle));

						$table->addCell($Largeur_Colonne, array_merge($this->styleCellBorder, array('align'=>'left')))
							->addText(utf8_decode($Mesure->act_libelle));

						$table->addCell($Largeur_Colonne, array_merge($this->styleCellBorder, array('align'=>'left')))
							->addText(utf8_decode($Mesure->idn_login.' - '.$Mesure->cvl_prenom.' '.$Mesure->cvl_nom));

						if ( $Mesure->act_date_debut_r == '' ) $_Tmp = $Mesure->act_date_debut_p;
						else $_Tmp = $Mesure->act_date_debut_r;

						$table->addCell($Largeur_Colonne, array_merge($this->styleCellBorder, array('align'=>'left')))
							->addText(utf8_decode($_Tmp));

						if ( $Mesure->act_date_fin_r == '' ) $_Tmp = $Mesure->act_date_fin_p;
						else $_Tmp = $Mesure->act_date_fin_r;

						$table->addCell($Largeur_Colonne, array_merge($this->styleCellBorder, array('align'=>'left')))
							->addText(utf8_decode($_Tmp));

						$table->addCell($Largeur_Colonne, array_merge($this->styleCellBorder, array('align'=>'left')))
							->addText(utf8_decode($Mesure->act_frequence));

						$table->addCell($Largeur_Colonne, array_merge($this->styleCellBorder, array('align'=>'left')))
							->addText(utf8_decode($Mesure->act_statut));
					}


					break;
				}
			}
		}

		return TRUE;
	}


	/* ================================================================================== */
	
	public function Word_actifs_primordiaux_risques( $crs_id, $Niveau, $Orientation, $Limitation, $Organisation, $Flag_Chapitres ) {
	/**
	* Fabrique le chapitre qui liste tous les actifs primordiaux avec leurs risques associés
	*
	* @author Pierre-Luc MARY
	* @date 2018-03-26
	*
	* @param[in] $crs_id ID de la Cartographie à utiliser
	* @param[in] $Niveau Donne la profondeur du titre (si besoin)
	* @param[in] $Orientation Indique l'orientation du document (portrait ou paysage)
	* @param[in] $Limitation Indique une limitation à l'affichage (si nécessaire)
	* @param[in] $Organisation Indique s'il y a des sous-chapitres à gérer dans ce chapitre
	* @param[in] $Flag_Chapitres Indicateur pour déterminer si l'utilisateur a sélectionné les chapitres dans son rapport.
	*
	* @return Renvoi vrai si les informations ont bien été éditées.
	*			
	*/
		if ( !isset($Flag_Chapitres['flag_actifs_risques']) or $Flag_Chapitres['flag_actifs_risques'] != 'o' ) return TRUE;

		include_once( DIR_LIBRAIRIES . '/Class_HTML.inc.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_libelles_generiques.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-EditionsRisques.php' );


		$this->SectionWordCourante = $this->Word_section( utf8_decode($Orientation) );

		// Récupère et affiche les Actifs Primordiaux avec leurs critères de valorisation.
		$this->SectionWordCourante->addTitle( utf8_decode(ucfirst($L_Actifs_Risques)), $Niveau);

		$requete = $this->prepareSQL(
			'SELECT apr_code||\' - \'||apr_nom AS "apr_nom", lbr1.lbr_libelle AS "apr_type",
string_agg(rcs_code||\' : \'||spp_code||\' - \'||spp_nom||\' - \'||rcs_scenario, \'###\')  AS "risques"
FROM spp_supports AS "spp"
LEFT JOIN apsp_apr_spp AS "apsp" ON apsp.spp_id = spp.spp_id
LEFT JOIN apr_actifs_primordiaux AS "apr" ON apr.apr_id = apsp.apr_id
LEFT JOIN lbr_libelles_referentiel AS "lbr1" ON lbr1.lbr_code = \'APR_TYPE_\'||apr_type_code AND lbr1.lng_id = :lng_id
LEFT JOIN rcs_risques_cartographies AS "rcs" ON rcs.spp_id = spp.spp_id
WHERE rcs.crs_id = :crs_id and rcs_scenario IS NOT NULL
GROUP BY apr_code, apr_nom, apr_type '
		);
			
		$Resultats = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->bindSQL($requete, ':lng_id', $_SESSION['Language'], self::LANGUE_TYPE, self::LANGUE_LENGTH)
			->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);


		$Largeur_Colonne = $this->Largeur_Section / 2;

		$table = $this->SectionWordCourante->addTable('styleTableauAPR_T');


		// Titre principal
		$table->addRow();

		// Partie Mesures
		$table->addCell($Largeur_Colonne, array_merge($this->styleCellTitleBorderLeft, array('align'=>'left')))
			->addText(utf8_decode($L_Actifs_Primordiaux), $this->styleTitleFont);

		$table->addCell($Largeur_Colonne, array_merge($this->styleCellTitle3BorderRight, array('align'=>'left')))
			->addText(utf8_decode($L_Risques), $this->styleTitleFont);


		foreach ($Resultats as $Resultat) {
			$t_Risques = explode('###', $Resultat->risques);
			$Total_Risques = count($t_Risques);

			if ( $Total_Risques == 1 ) {
				$table->addRow();

				$table->addCell($Largeur_Colonne, array_merge($this->styleCellBorder, array('align'=>'left')))
					->addText(utf8_decode($Resultat->apr_nom));

				$table->addCell($Largeur_Colonne, array_merge($this->styleCellBorder, array('align'=>'left')))
					->addText(utf8_decode($Resultat->risques));
			} else {
				$table->addRow();

				$table->addCell($Largeur_Colonne, array_merge($this->styleCellTopBorder, array('align'=>'left')))
					->addText(utf8_decode($Resultat->apr_nom));

				$table->addCell($Largeur_Colonne, array_merge($this->styleCellTopBorder, array('align'=>'left')))
					->addText(utf8_decode($t_Risques[0]));

				for( $i = 1; $i < $Total_Risques; $i++ ) {
					$table->addRow();

					$table->addCell($Largeur_Colonne, array_merge($this->styleCellMiddleBorder, array('align'=>'left')))
						->addText('');

					$table->addCell($Largeur_Colonne, array_merge($this->styleCellMiddleBorder, array('align'=>'left')))
						->addText(utf8_decode($t_Risques[$i]));
				}
			}
		}

		return TRUE;
	}


	/* ================================================================================== */
	
	public function Word_risques_mesures( $crs_id, $Niveau, $Orientation, $Limitation, $Organisation, $Flag_Chapitres ) {
	/**
	* Fabrique le chapitre qui liste tous les risques avec leurs mesures associées
	*
	* @author Pierre-Luc MARY
	* @date 2017-08-22
	*
	* @param[in] $crs_id ID de la Cartographie à utiliser
	* @param[in] $Niveau Donne la profondeur du titre (si besoin)
	* @param[in] $Orientation Indique l'orientation du document (portrait ou paysage)
	* @param[in] $Limitation Indique une limitation à l'affichage (si nécessaire)
	* @param[in] $Organisation Indique s'il y a des sous-chapitres à gérer dans ce chapitre
	* @param[in] $Flag_Chapitres Indicateur pour déterminer si l'utilisateur a sélectionné les chapitres dans son rapport.
	*
	* @return Renvoi vrai si les informations ont bien été éditées.
	*			
	*/
		if ( !isset($Flag_Chapitres['flag_risques_mesures']) or $Flag_Chapitres['flag_risques_mesures'] != 'o' ) return TRUE;

		include_once( DIR_LIBRAIRIES . '/Class_HTML.inc.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_libelles_generiques.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-EditionsRisques.php' );


		$this->SectionWordCourante = $this->Word_section( utf8_decode($Orientation) );

		// Récupère et affiche les Actifs Primordiaux avec leurs critères de valorisation.
		$this->SectionWordCourante->addTitle( utf8_decode(ucfirst($L_Risques_Mesures)), $Niveau);

		$requete = $this->prepareSQL(
			'SELECT \'R\'||rcs_code||\' : \'||CASE WHEN rcs_libelle_menace != \'\' THEN rcs_libelle_menace ELSE lbr2.lbr_libelle END||\' (\'||spp_nom||\') \'||CASE WHEN rcs_scenario IS NULL THEN \'\' ELSE \' - \'||rcs_scenario END AS "risque", 
string_agg(mgr_code||\' : \'||lbr.lbr_libelle||\' (\'||lbr3.lbr_libelle||\')\', \'###\') AS "mesures"
FROM spp_supports AS "spp"
LEFT JOIN rcs_risques_cartographies AS "rcs" ON spp.spp_id = rcs.spp_id
LEFT JOIN mgn_menaces_generiques AS "mgn" ON mgn.mgn_id = rcs.mgn_id
LEFT JOIN mcr_mesures_cartographies AS "mcr" ON mcr.rcs_id = rcs.rcs_id
LEFT JOIN mgr_mesures_generiques AS "mgr" ON mgr.mgr_id = mcr.mgr_id 
LEFT JOIN lbr_libelles_referentiel AS "lbr" ON lbr.lbr_code = mgr_code AND lbr.lng_id = :lng_id
LEFT JOIN lbr_libelles_referentiel AS "lbr2" ON lbr2.lbr_code = mgn_code AND lbr2.lng_id = :lng_id
LEFT JOIN lbr_libelles_referentiel AS "lbr3" ON lbr3.lbr_code = \'MCR_ETAT_\'||mcr_etat_code AND lbr3.lng_id = :lng_id
WHERE rcs.crs_id = :crs_id and (rcs.rcs_id IS NOT NULL or rcs.rcs_scenario IS NOT NULL)
GROUP BY rcs_code, rcs_libelle_menace, lbr2.lbr_libelle, spp_nom, rcs_scenario
ORDER BY rcs_code '
		);
			
		$Resultats = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->bindSQL($requete, ':lng_id', $_SESSION['Language'], self::LANGUE_TYPE, self::LANGUE_LENGTH)
			->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);


		$Largeur_Colonne = $this->Largeur_Section / 2;

		$table = $this->SectionWordCourante->addTable('styleTableauAPR_T');


		// Titre principal
		$table->addRow();

		// Partie Mesures
		$table->addCell($Largeur_Colonne, array_merge($this->styleCellTitleBorderLeft, array('align'=>'left')))
			->addText(utf8_decode($L_Risque), $this->styleTitleFont);

		$table->addCell($Largeur_Colonne, array_merge($this->styleCellTitle3BorderRight, array('align'=>'left')))
			->addText(utf8_decode($L_Mesures), $this->styleTitleFont);


		foreach ($Resultats as $Resultat) {
			$t_Mesures = array_unique( explode('###', $Resultat->mesures) );
			sort( $t_Mesures );

			$Total_Mesures = count($t_Mesures);

			if ( $Total_Mesures == 1 ) {
				$table->addRow();

				$table->addCell($Largeur_Colonne, array_merge($this->styleCellBorder, array('align'=>'left')))
					->addText(utf8_decode($Resultat->risque));

				$table->addCell($Largeur_Colonne, array_merge($this->styleCellBorder, array('align'=>'left')))
					->addText(utf8_decode($Resultat->mesures));
			} else {
				$table->addRow();

				$table->addCell($Largeur_Colonne, array_merge($this->styleCellTopBorder, array('align'=>'left')))
					->addText(utf8_decode($Resultat->risque));

				$table->addCell($Largeur_Colonne, array_merge($this->styleCellTopBorder, array('align'=>'left')))
					->addText(utf8_decode($t_Mesures[0]));

				for( $i = 1; $i < $Total_Mesures; $i++ ) {
					$table->addRow();

					$table->addCell($Largeur_Colonne, array_merge($this->styleCellMiddleBorder, array('align'=>'left')))
						->addText('');

					$table->addCell($Largeur_Colonne, array_merge($this->styleCellMiddleBorder, array('align'=>'left')))
						->addText(utf8_decode($t_Mesures[$i]));
				}
			}
		}


		return TRUE;
	}


	/* ================================================================================== */
	
	public function Word_actifs_primordiaux_mesures( $crs_id, $Niveau, $Orientation, $Limitation, $Organisation, $Flag_Chapitres ) {
	/**
	* Fabrique le chapitre qui liste tous les actifs primordiaux et leurs mesures associées
	*
	* @author Pierre-Luc MARY
	* @date 2017-08-22
	*
	* @param[in] $crs_id ID de la Cartographie à utiliser
	* @param[in] $Niveau Donne la profondeur du titre (si besoin)
	* @param[in] $Orientation Indique l'orientation du document (portrait ou paysage)
	* @param[in] $Limitation Indique une limitation à l'affichage (si nécessaire)
	* @param[in] $Organisation Indique s'il y a des sous-chapitres à gérer dans ce chapitre
	* @param[in] $Flag_Chapitres Indicateur pour déterminer si l'utilisateur a sélectionné les chapitres dans son rapport.
	*
	* @return Renvoi vrai si les informations ont bien été éditées.
	*			
	*/
		if ( !isset($Flag_Chapitres['flag_actifs_mesures']) or $Flag_Chapitres['flag_actifs_mesures'] != 'o' ) return TRUE;

		include_once( DIR_LIBRAIRIES . '/Class_HTML.inc.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_libelles_generiques.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-EditionsRisques.php' );


		$this->SectionWordCourante = $this->Word_section( utf8_decode($Orientation) );

		// Récupère et affiche les Actifs Primordiaux avec leurs critères de valorisation.
		$this->SectionWordCourante->addTitle( utf8_decode(ucfirst($L_Actifs_Mesures)), $Niveau);

		$requete = $this->prepareSQL(
			'SELECT apr_code||\' - \'||apr_nom||\' [\'||lbr2.lbr_libelle||\']\' AS "actif",
string_agg(mgr_code||\' : \'||lbr.lbr_libelle||\' (\'||lbr3.lbr_libelle||\')\', \'###\') AS "mesures"
FROM apr_actifs_primordiaux AS "apr"
LEFT JOIN apsp_apr_spp AS "apsp" ON apsp.apr_id = apr.apr_id
LEFT JOIN spp_supports AS "spp" ON spp.spp_id = spp.spp_id
LEFT JOIN rcs_risques_cartographies AS "rcs" ON spp.spp_id = rcs.spp_id
LEFT JOIN mcr_mesures_cartographies AS "mcr" ON mcr.rcs_id = rcs.rcs_id
LEFT JOIN mgr_mesures_generiques AS "mgr" ON mgr.mgr_id = mcr.mgr_id 
LEFT JOIN lbr_libelles_referentiel AS "lbr" ON lbr.lbr_code = mgr_code AND lbr.lng_id = :lng_id
LEFT JOIN lbr_libelles_referentiel AS "lbr2" ON lbr2.lbr_code = \'APR_TYPE_\'||apr_type_code AND lbr2.lng_id = :lng_id
LEFT JOIN lbr_libelles_referentiel AS "lbr3" ON lbr3.lbr_code = \'MCR_ETAT_\'||mcr_etat_code AND lbr3.lng_id = :lng_id
WHERE apr.crs_id = :crs_id and rcs.rcs_id IS NOT NULL
GROUP BY apr_code, apr_nom, lbr2.lbr_libelle
ORDER BY apr_code '
		);
			
		$Resultats = $this->bindSQL($requete, ':crs_id', $crs_id, self::ID_TYPE)
			->bindSQL($requete, ':lng_id', $_SESSION['Language'], self::LANGUE_TYPE, self::LANGUE_LENGTH)
			->executeSQL($requete)
			->fetchAll(PDO::FETCH_CLASS);


		$Largeur_Colonne = $this->Largeur_Section / 2;

		$table = $this->SectionWordCourante->addTable('styleTableauAPR_T');


		// Titre principal
		$table->addRow();

		// Partie Mesures
		$table->addCell($Largeur_Colonne, array_merge($this->styleCellTitleBorderLeft, array('align'=>'left')))
			->addText(utf8_decode($L_Actif_Primordial), $this->styleTitleFont);

		$table->addCell($Largeur_Colonne, array_merge($this->styleCellTitle3BorderRight, array('align'=>'left')))
			->addText(utf8_decode($L_Mesures), $this->styleTitleFont);


		foreach ($Resultats as $Resultat) {
			$t_Mesures = array_unique( explode('###', $Resultat->mesures) );
			sort( $t_Mesures );

			$Total_Mesures = count($t_Mesures);

			if ( $Total_Mesures == 1 ) {
				$table->addRow();

				$table->addCell($Largeur_Colonne, array_merge($this->styleCellBorder, array('align'=>'left')))
					->addText(utf8_decode($Resultat->actif));

				$table->addCell($Largeur_Colonne, array_merge($this->styleCellBorder, array('align'=>'left')))
					->addText(utf8_decode($Resultat->mesures));
			} else {
				$table->addRow();

				$table->addCell($Largeur_Colonne, array_merge($this->styleCellTopBorder, array('align'=>'left')))
					->addText(utf8_decode($Resultat->actif));

				$table->addCell($Largeur_Colonne, array_merge($this->styleCellTopBorder, array('align'=>'left')))
					->addText(utf8_decode($t_Mesures[0]));

				for( $i = 1; $i < $Total_Mesures; $i++ ) {
					$table->addRow();

					$table->addCell($Largeur_Colonne, array_merge($this->styleCellMiddleBorder, array('align'=>'left')))
						->addText('');

					$table->addCell($Largeur_Colonne, array_merge($this->styleCellMiddleBorder, array('align'=>'left')))
						->addText(utf8_decode($t_Mesures[$i]));
				}
			}
		}


		return TRUE;
	}


	/* ================================================================================== */
	
	public function Word_repartition_risques( $crs_id, $Niveau, $Orientation, $Limitation, $Organisation, $Flag_Chapitres ) {
	/**
	* Fabrique le chapitre qui affiche les matrices de répartition des risques.
	*
	* @author Pierre-Luc MARY
	* @date 2017-08-22
	*
	* @param[in] $crs_id ID de la Cartographie à utiliser
	* @param[in] $Niveau Donne la profondeur du titre (si besoin)
	* @param[in] $Orientation Indique l'orientation du document (portrait ou paysage)
	* @param[in] $Limitation Indique une limitation à l'affichage (si nécessaire)
	* @param[in] $Organisation Indique s'il y a des sous-chapitres à gérer dans ce chapitre
	* @param[in] $Flag_Chapitres Indicateur pour déterminer si l'utilisateur a sélectionné les chapitres dans son rapport.
	*
	* @return Renvoi vrai si les informations ont bien été éditées.
	*			
	*/
		if ( !isset($Flag_Chapitres['flag_repartition_risques']) or $Flag_Chapitres['flag_repartition_risques'] != 'o' ) return TRUE;

		include_once( DIR_LIBRAIRIES . '/Class_HTML.inc.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_libelles_generiques.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-EditionsRisques.php' );
		include( DIR_LIBELLES . '/' . $_SESSION['Language'] . '_Loxense-CriteresAppreciationAcceptationRisques.php' );

		include( DIR_LIBRAIRIES . '/Class_CriteresAppreciationRisques_PDO.inc.php' );
		include( DIR_LIBRAIRIES . '/Class_HBL_Entites_PDO.inc.php' );
		

		$objCriteresAppreciationRisques = new CriteresAppreciationRisques();
		$objEntites = new HBL_Entites();

		$ent_id = $objEntites->recupererENT_IDparCRS_ID( $crs_id );

		// Récupère les Vraisemblances possibles.
		$Vraisemblances = $objCriteresAppreciationRisques->recupererVraisemblances();

		// Récupère les Impacts possibles.
		$Impacts = $objCriteresAppreciationRisques->recupererImpacts();

		// Récupère les Critères d'appréciation.
		$NiveauxRisque = $objCriteresAppreciationRisques->recupererRepresentationNiveauxRisque();

		// Récupère les Critères d'Appréciation des Risques.
		$MatriceCriteresAppreciationRisques = $objCriteresAppreciationRisques->recupererCriteresAppreciationRisques();

		// Récupère les risques par Vraisemblance et Impact.
		$Risques = $objCriteresAppreciationRisques->recupererRisquesVraisemblancesImpacts( $crs_id, 'B' );
		$Risques_N = $objCriteresAppreciationRisques->recupererRisquesVraisemblancesImpacts( $crs_id, 'N' );

		// Réorganise les tableaux
		$Risques_Tri = [];
		foreach ( $Risques as $Risque ) {
			$_Idx = $Risque->gri_poids . '-' . $Risque->vrs_poids;

			if ( ! isset( $Risques_Tri[ $_Idx ] ) ) $Risques_Tri[ $_Idx ] = '';

			if ( $Risques_Tri[ $_Idx ] != '' ) $Risques_Tri[ $_Idx ] .= ', ';

			$Risques_Tri[ $_Idx ] .= $Risque->rcs_code;
		}

		$Risques_N_Tri = [];
		foreach ( $Risques_N as $Risque ) {
			$_Idx = $Risque->gri_poids . '-' . $Risque->vrs_poids;

			if ( ! isset( $Risques_N_Tri[ $_Idx ] ) ) $Risques_N_Tri[ $_Idx ] = '';

			if ( $Risques_N_Tri[ $_Idx ] != '' ) $Risques_N_Tri[ $_Idx ] .= ', ';

			$Risques_N_Tri[ $_Idx ] .= $Risque->rcs_code;
		}

		$this->SectionWordCourante = $this->Word_section( utf8_decode($Orientation) );

		$this->SectionWordCourante->addTitle( utf8_decode(ucfirst($L_Repartition_Risques)), $Niveau);


		$Largeur_Colonne = $this->Largeur_Section / ( count($Vraisemblances) + 1 );


		// Répartition des Risques Bruts
		$this->SectionWordCourante->addTitle( utf8_decode(ucfirst($L_Risque_Brut)), $Niveau+1);

		// Titre principal des Vraisemblances
		$this->objPHPWord->addTableStyle('styleTableauTitre', $this->styleTable);
		$table = $this->SectionWordCourante->addTable('styleTableauTitre');

		$table->addRow();

		$table->addCell($Largeur_Colonne)->addText(''); // Cellule d'espacement.
		$table->addCell($Largeur_Colonne * count($Vraisemblances), array_merge($this->styleCellTitle2Border, array('align'=>'left')))
			->addText(utf8_decode($L_Vraisemblance), $this->styleTitleFont);

		// Liste les différents libellé des Vraisemblances.
		$this->objPHPWord->addTableStyle('styleTableauCorps', $this->styleTable);
		$table1 = $this->SectionWordCourante->addTable('styleTableauCorps');

		$table1->addRow();

		$table1->addCell($Largeur_Colonne, array_merge($this->styleCellTitle2Border, array('align'=>'left')))
			->addText(utf8_decode($L_Impact), $this->styleTitleFont);

		foreach ( $Vraisemblances as $Vraisemblance ) {
			$table1->addCell($Largeur_Colonne, array_merge($this->styleCellTitleBorder, array('align'=>'left')))
				->addText(utf8_decode($Vraisemblance->vrs_libelle), $this->styleTitleFont);
		}


		// Liste les impacts et les cellules du corps.
		$Style1 = $this->styleCellTitleBorder;
		$Style1['borderBottomSize'] = 6;

		$Style2 = $this->styleCellBorder;

		foreach( $Impacts as $Impact ) {
			$Poids_Impact = $Impact->gri_poids;

			$table1->addRow();

			$table1->addCell($Largeur_Colonne, array_merge($Style1, array('align'=>'left')))
				->addText(utf8_decode($Impact->gri_libelle), $this->styleTitleFont);

			foreach ( $Vraisemblances as $Vraisemblance ) {
				$Poids_Vraisemblance = $Vraisemblance->vrs_poids;

				if ( isset( $MatriceCriteresAppreciationRisques[$Poids_Impact.'-'.$Poids_Vraisemblance] ) ) {
					$Niveau_Risque = $MatriceCriteresAppreciationRisques[$Poids_Impact.'-'.$Poids_Vraisemblance];
				} else {
					$Niveau_Risque = $Poids_Impact + $Poids_Vraisemblance;
				}

				foreach ( $NiveauxRisque as $NiveauRisque ) {
					if ( $NiveauRisque->rnr_debut_poids >= $Niveau_Risque
					 && $Niveau_Risque <= $NiveauRisque->rnr_fin_poids ) {
						$Style2['bgColor'] = $NiveauRisque->rnr_code_couleur;
						break;
					}
				}

				if ( isset( $Risques_Tri[$Poids_Impact.'-'.$Poids_Vraisemblance] ) ) $Cellule = $Risques_Tri[$Poids_Impact.'-'.$Poids_Vraisemblance];
				else $Cellule = '';

				$table1->addCell($Largeur_Colonne, array_merge($Style2, array('align'=>'left')))
					->addText(utf8_decode($Cellule), $this->styleTitleFont);
			}
		}


		// Répartition des Risques Nets
		$this->SectionWordCourante->addTitle( utf8_decode(ucfirst($L_Risque_Net)), $Niveau+1);

		// Titre principal des Vraisemblances
		$this->objPHPWord->addTableStyle('styleTableauTitre', $this->styleTable);
		$table = $this->SectionWordCourante->addTable('styleTableauTitre');

		$table->addRow();

		$table->addCell($Largeur_Colonne)->addText(''); // Cellule d'espacement.
		$table->addCell($Largeur_Colonne * count($Vraisemblances), array_merge($this->styleCellTitle2Border, array('align'=>'left')))
			->addText(utf8_decode($L_Vraisemblance), $this->styleTitleFont);

		// Liste les différents libellé des Vraisemblances.
		$this->objPHPWord->addTableStyle('styleTableauCorps', $this->styleTable);
		$table1 = $this->SectionWordCourante->addTable('styleTableauCorps');

		$table1->addRow();

		$table1->addCell($Largeur_Colonne, array_merge($this->styleCellTitle2Border, array('align'=>'left')))
			->addText(utf8_decode($L_Impact), $this->styleTitleFont);

		foreach ( $Vraisemblances as $Vraisemblance ) {
			$table1->addCell($Largeur_Colonne, array_merge($this->styleCellTitleBorder, array('align'=>'left')))
				->addText(utf8_decode($Vraisemblance->vrs_libelle), $this->styleTitleFont);
		}


		// Liste les impacts et les cellules du corps.
		$Style1 = $this->styleCellTitleBorder;
		$Style1['borderBottomSize'] = 6;

		$Style2 = $this->styleCellBorder;

		foreach( $Impacts as $Impact ) {
			$Poids_Impact = $Impact->gri_poids;

			$table1->addRow();

			$table1->addCell($Largeur_Colonne, array_merge($Style1, array('align'=>'left')))
				->addText(utf8_decode($Impact->gri_libelle), $this->styleTitleFont);

			foreach ( $Vraisemblances as $Vraisemblance ) {
				$Poids_Vraisemblance = $Vraisemblance->vrs_poids;
				
				if ( isset( $MatriceCriteresAppreciationRisques[$Poids_Impact.'-'.$Poids_Vraisemblance] ) ) {
					$Niveau_Risque = $MatriceCriteresAppreciationRisques[$Poids_Impact.'-'.$Poids_Vraisemblance];
				} else {
					$Niveau_Risque = $Poids_Impact + $Poids_Vraisemblance;
				}

				foreach ( $NiveauxRisque as $NiveauRisque ) {
					if ( $NiveauRisque->rnr_debut_poids >= $Niveau_Risque
					 && $Niveau_Risque <= $NiveauRisque->rnr_fin_poids ) {
						$Style2['bgColor'] = $NiveauRisque->rnr_code_couleur;
						break;
					}
				}

				if ( isset( $Risques_N_Tri[$Poids_Impact.'-'.$Poids_Vraisemblance] ) ) $Cellule = $Risques_N_Tri[$Poids_Impact.'-'.$Poids_Vraisemblance];
				else $Cellule = '';

				$table1->addCell($Largeur_Colonne, array_merge($Style2, array('align'=>'left')))
					->addText(utf8_decode($Cellule), $this->styleTitleFont);
			}
		}


		return TRUE;
	}

	
	
	public function extraireCodeDeLibelle( $Libelle ) {
		$Position = mb_strpos( $Libelle, ':' );
		$Valeur = trim( mb_substr( $Libelle, 0, $Position) );
		$Information = trim( mb_substr( $Libelle, $Position + 1) );
		
		return [$Valeur, $Information];
	}
	
} // Fin class Editions

?>