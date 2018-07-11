<?php

// Version v1.1
// Ce script permet de récupérer la liste des messages sur la messagerie vocale Free (ADSL)

// Les variables à passer en paramètre:
// [VAR1] = Login du compte Free
// [VAR2] = Mot de passe de compte Free


// Exemple d'appel du script avec variables: http://localhost/script/?exec=free_messagerie.php&login=[VAR1]&passwd=[VAR2]

// Le résultat est obtenu sous forme XML
// xPath pour récupérer simplement le nombres de messages: count(/FreeMessagerie/Messages/Message)

// Stocker les variables passées en argument
$login = getArg('login');
$passwd = getArg('passwd');

// Authentification et création de session
$url = "https://subscribe.free.fr/login/login.pl";
$post = 'login='.$login.'&pass='.$passwd.'&ok=Submit+Query';
$logindata = httpQuery($url, 'POST', $post);

// Gestion de la messagerie vocale
$urlpos = strpos($logindata, "phone_repondeur.pl");
$urlendpos = strpos($logindata, '"', $urlpos);
$url = "https://adsl.free.fr/".substr($logindata, $urlpos, $urlendpos - $urlpos);
$url = str_replace("&amp;", "&", $url);
$voicemaildata = httpQuery($url, 'GET');

// Généraation de l'XML
$content_type = 'text/xml';
sdk_header($content_type);

echo "<FreeMessagerie>";
echo "<Messages>";

if(!strpos($voicemaildata, "Pas de nouveau message"))
{
    preg_match_all('/<td nowrap>Nouveau message<\/td>\s*<td>(?P<telephone>\d+)<\/td>\s*<td nowrap>(?P<date>.*)<\/td>\s*<td nowrap>(?P<duree>\d+)/', $voicemaildata, $matches);
    for($i = 0; $i < count($matches[0]); $i++)
    {
        echo "<Message>";
        echo "<Provenance>".$matches["telephone"][$i]."</Provenance>";
        echo "<Date>".$matches["date"][$i]."</Date>";
        echo "<Duree>".$matches["duree"][$i]."</Duree>";
        echo "</Message>";
    }
}

echo "</Messages>";
echo "</FreeMessagerie>";
?> 
