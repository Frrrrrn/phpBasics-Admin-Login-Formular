<?php

	/* 
	 * phpBasics
	 * ---------
	 * 
	 * Script:        Login-Formular
	 * 
	 * Version:       1.0
	 * Release:       01.10.2019
	 * 
	 * Author:        numaek   
	 * Copyright (c): 2004-2019 by www.numaek.de
	 * 
	 * *********************************************************************************************************************************************************************************************
	 */


	// Die Zugangsdaten festlegen
	// ==========================
	$zugangsdaten = array();

	$zugangsdaten[0]['user'] = 'Mustermann';
	$zugangsdaten[0]['pass'] = 'passwort1';

	$zugangsdaten[1]['user'] = 'Meier';
	$zugangsdaten[1]['pass'] = 'passwort2';

	$zugangsdaten[2]['user'] = 'Schulz';
	$zugangsdaten[2]['pass'] = 'passwort3';

	// Fortführen für weitere User...


	// Die Session einstellen und starten
	// ==================================
	ini_set('session.use_cookies',     1);
	ini_set('session.cookie_lifetime', 1800);
	ini_set('session.gc_maxlifetime',  1800);

	session_start();


	// Login durch Cookie oder Session abfragen und definieren
	// =======================================================
	if( isset($_COOKIE['phpBasicsAdmin']) )
	{
		define('ADMIN_LOGIN', $_COOKIE['phpBasicsAdmin']);
		$accParts = explode("###", ADMIN_LOGIN);
		define('ADMIN_ID',    $accParts[0]);
		define('ADMIN_USER',  $accParts[1]);
		define('ADMIN_PASS',  $accParts[2]);
	} else
		if( isset($_SESSION['phpBasicsAdmin']) )
		{
			define('ADMIN_LOGIN', $_SESSION['phpBasicsAdmin']);
			$accParts = explode("###", ADMIN_LOGIN);
			define('ADMIN_ID',    $accParts[0]);
			define('ADMIN_USER',  $accParts[1]);
			define('ADMIN_PASS',  $accParts[2]);
		} else
		  {
			define('ADMIN_LOGIN', 'logout');
			define('ADMIN_ID',    -1);
			define('ADMIN_USER',  '');
			define('ADMIN_PASS',  '');
		  }


	// Automatisches Ausloggen bei falschen oder veralteten Daten
	// ==========================================================
	if( ADMIN_LOGIN != "logout" )
	{
		if( ADMIN_PASS != $zugangsdaten[ADMIN_ID]['pass'] )
		{
			header("Location: ".$_SERVER['PHP_SELF']."?action=logout");
		}
	}


	// Die Variable $action zur Fallunterscheidung definieren
	// ======================================================
	if( isset($_GET['action']) )
	{
		$action = htmlspecialchars(urldecode(trim($_GET['action'])));
	} else
		if( isset($_POST['action']) )
		{
			$action = $_POST['action'];
		} else
		  {
			// Default-Wert
			$action = "show";
		  }


	// Versuche einzuloggen und erneut auf die Seite weiterleiten
	// ==========================================================
	if( $action == "log" )
	{
		// Benutzereingaben einlesen und prüfen
		$getLogin = htmlspecialchars(strtolower(trim($_POST['log'])));
		$getPass  = htmlspecialchars(strtolower(trim($_POST['pass'])));

		for( $z = 0; $z < sizeof($zugangsdaten); $z++ )
		{
			// Groß- und Kleinschreibung ignorieren
			if( $getLogin == strtolower($zugangsdaten[$z]['user']) && $getPass == strtolower($zugangsdaten[$z]['pass']) )
			{
				// Alle Teile der zugangsdaten zur späteren Überprüfung mitspeichern
				$loginContent = $z."###".$zugangsdaten[$z]['user']."###".$zugangsdaten[$z]['pass'];

				// Login in Session für 1800 Sekunden speichern
				$_SESSION['phpBasicsAdmin'] = $loginContent;

				if( isset($_POST['loginsave']) )
				{
					// Login in Cookie für 1 Jahr speichern
					setcookie("phpBasicsAdmin", $loginContent, time() + ( 3600 * 24 * 365 ) );
				}

				header("Location: ".$_SERVER['PHP_SELF']);
			}
		}

		header("Location: ".$_SERVER['PHP_SELF']."?errorMsg=1");
	}


	// Ausloggen - Session und Cookie löschen
	// ======================================
	if( $action == "logout" )
	{
		setcookie("phpBasicsAdmin", "logout", time() - ( 3600 * 24 * 365 ) );

		unset($_SESSION['phpBasicsAdmin']);

		header("Location: ".$_SERVER['PHP_SELF']);
	}

	if( ADMIN_LOGIN == "logout" )
	{
		$action = "login";
	}


	// Beginn der Ausgabe im Browser. Alle Weiterleitungen und Cookies müssen oberhalb programmiert werden.
	// ##############################################################################################################################################################################################


	echo "<!DOCTYPE html>
	<html lang=\"de\">
		<head>
			<title>Administration</title>
			<META charset=\"utf-8\">
			<META NAME=\"viewport\" content=\"width=device-width, initial-scale=1.0\">

			<style type=\"text/css\">

				/* Tabellenzellen */
				.loginCell
				{
					background-color: #DCDCDC;
				}

			</style>

			<script language=\"javascript\">

				/* Sicherheitsabfrabe beim Ausloggen */
				function sure(target)
				{
					var Checkdelete = confirm('Sicher?');
					if( Checkdelete != false )
					{
						self.location.href = target;
					}
				}

			</script>

		</head>
	<body>

	<h1>Administration</h1>

	[<a href=\"".$_SERVER['PHP_SELF']."\">&Uuml;bersicht</a>]\n";

	if( ADMIN_LOGIN != "logout" )
	{
		echo "[<a href=\"".$_SERVER['PHP_SELF']."?action=view\">Anzeigen</a>]\n";
		echo "[<a href=\"".$_SERVER['PHP_SELF']."?action=edit\">Bearbeiten</a>]\n";
		echo "[<a href=\"javascript:sure('".$_SERVER['PHP_SELF']."?action=logout');\">Logout</a>]\n";
	} else
	  {
		echo "[<a href=\"".$_SERVER['PHP_SELF']."?action=login\">Login</a>]\n";
	  }

	echo "<br><br><br>\n";


	if( $action == "login" )
	{
		// Login-Formular anzeigen
		// =======================
		echo "<table border=\"0\" style=\"width: 450px; margin: 0px auto; border: 2px solid #336699; border-spacing: 0px; box-shadow: 15px 15px 5px #000000;\">
			<form name=\"login\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">
			<tr>
				<td class=\"loginCell\" style=\"text-align: center; height: 20px;\" colspan=\"2\">
					<br><span style=\"color: #336699; font-weight: bold;\">Einloggen in den Administrationsbereich</span><br><br>\n";

					if( isset($_GET['errorMsg']) )
					{
						echo "<span style=\"color: red; background-color: #000000; padding: 5px; border-radius: 15px;\">&nbsp;Die Zugangsdaten sind falsch!&nbsp;</span>\n";
					} else
					  {
						echo "&nbsp;";
					  }

				echo "<br><br></td>
			</tr>
			<tr>
				<td class=\"loginCell\" style=\"text-align: right; width: 50%;\">Login:</td>
				<td class=\"loginCell\" style=\"text-align: left;  width: 50%;\"><input type=\"text\" name=\"log\" value=\"\" style=\"width: 150px;\"></td>
			</tr>
			<tr>
				<td class=\"loginCell\" style=\"text-align: right;\">Passwort:</td>
				<td class=\"loginCell\" style=\"text-align: left;\"><input type=\"password\" name=\"pass\" value=\"\" style=\"width: 150px;\"></td>
			</tr>
			<tr>
				<td class=\"loginCell\" style=\"text-align: right; vertical-align: top;\">&nbsp;
					<input type=\"button\" value=\"Abbrechen\" onclick=\"self.location.href='http://www.numaek.de';\">
				</td>
				<td class=\"loginCell\" style=\"text-align: left; vertical-align: top;\">
					     <input type=\"submit\"                      value=\"Login\">
					<sub><input type=\"checkbox\" name=\"loginsave\" value=\"ja\" CHECKED></sub>speichern
					     <input type=\"hidden\"   name=\"action\"    value=\"log\">
				</td>
			</tr>
			<tr>
				<td class=\"loginCell\" style=\"text-align: center; height: 50px;\" colspan=\"2\">
					[<a href=\"http://www.numaek.de\" target=\"_blank\" title=\"Mehr davon gibt es bei...\">phpBasics by www.numaek.de</a>]
				</td>
			</tr>
			</form>
		</table><br><br>\n";
	}


	// ##############################################################################################################################################################################################


	if( $action == "view" )
	{
		echo "Hier k&ouml;nnte alles angezeigt werden...\n\n";
	}


	if( $action == "edit" )
	{
		echo "Hier k&ouml;nnte alles bearbeitet werden...\n\n";
	}


	if( $action == "show" )
	{
		echo "<h3>Startseite - Willkommen im Administrationsbereich ".ADMIN_USER."!</h3><br>\n\n";
		echo "Inhalt der Session: ".$_SESSION['phpBasicsAdmin']."<br><br>\n";
		echo "Inhalt des Cookies: ".$_COOKIE['phpBasicsAdmin']."<br><br>\n";
	}


	echo "<br><br>
	</body></html>\n";

?>