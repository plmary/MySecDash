<?php
/**
* Libellés spécifiques à la gestion des identitiés (utilisateurs).
*
* @warning Ce fichier doit impérativement être au format UTF-8 pour une gestion cohérente des caractères accentués.
*
* @copyright Copyright Loxense
* @author Pierre-Luc MARY
* @date 2017-03-05
*/

	$L_Title = 'User Management';
	$L_List_Users = 'User List';
	
	$L_Super_Admin = "Superadmin";
	$L_Admin = "Admin";
	$L_Administrator = 'Administrator';
	$L_Assignment = "Member"; // // ex "assignation" apparait ds creation utilisateur pour le lier à 1 ou xx entités
	$L_Employer_Entity = "Employer's entity";
	$L_Email = 'Email';

	$L_User_Add = 'Add user';
	$L_User_Create = 'Create user';
	$L_User_Delete = 'Remove user';
	$L_User_Modify = 'Modify user';
	$L_User_View = 'Display user\'s information';

	$L_User_Added = 'User added' ;
	$L_User_Created = 'User created' ; // pas redondant avec added ?
	$L_User_Modified = 'User modified' ;
	$L_User_Deleted = 'User removed' ;

	$L_Change_Authenticator_Flag = 'Change your credentials';
	$L_Attempt = 'Attempt';
	$L_Disabled = 'Disable';
	$L_Enabled = 'Enable';
	$L_Last_Connection = 'Previous connection';
	$L_Expiration_Date = 'Expiration date';
	$L_Updated_Authentication = 'Last credential modification'; // on parle bien de la derniere date de modif des authentifiants ?
	$L_Users_List_Return = 'Return to User list';
	$L_Never_Connected = 'Never logged';
	$L_To_Activate_User = 'Enable user';
	$L_To_Deactivate_User = 'Disable user';

	$L_Authenticator_Reset = 'Reset the password';
	$L_Password_Reseted = 'The password has been reset';
	$L_Attempt_Reset = 'Reset the login attempt counter';
	$L_Attempt_Reseted = 'The login attempt counter has been reset';
	$L_Expiration_Date_Reset = 'Reset the expiration date';
	$L_Expiration_Date_Reseted = 'The expiration date has been reset';

	$L_ERR_CREA_Identity = 'An error occurred while creating the identity';
	$L_ERR_MODI_Identity = 'An error occurred while editing the identity';
	$L_ERR_DELE_Identity = 'An error occurred while deleting the identity';
	$L_ERR_DUPL_Identity = "'Username' already used";
	
	$L_ERR_RST_Password = 'An error occurred while resetting password'; // ou "during the reset password process".
	$L_ERR_RST_Attempt = 'An error occurred while resetting the login attempt counter';
	$L_ERR_RST_Expiration = 'An error occurred wile changing the expiration date';
	$L_ERR_RST_Disable = 'An error occurred while enabling or disabling the user';

	$L_User_Enabled = 'The user has been enabled';
	$L_User_Disabled = 'The user has been disabled';
	$L_Users_Associate = 'Join users'; // associate, bind, link ? selon contexte d'utilisation

	$L_User_Confirm_Deleted = 'Do you really want to delete the user "<span class="purple">%idn</span>" (<span class="purple">%cvl</span>)?';

?>