<?php
/******************************************************************************
 * Ankuendigungen auflisten
 *
 * Copyright    : (c) 2004 - 2006 The Admidio Team
 * Homepage     : http://www.admidio.org
 * Module-Owner : Markus Fassbender
 *
 * Uebergaben:
 *
 * start     - Angabe, ab welchem Datensatz Ankuendigungen angezeigt werden sollen
 * headline  - Ueberschrift, die ueber den Ankuendigungen steht
 *             (Default) Ankuendigungen
 * id        - Nur eine einzige Annkuendigung anzeigen lassen.
 *
 ******************************************************************************
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 *****************************************************************************/

require("../../system/common.php");
require("../../system/bbcode.php");

if(!array_key_exists("start", $_GET))
{
    $_GET["start"] = 0;
}

if(!array_key_exists("headline", $_GET))
{
    $_GET["headline"] = "Ank&uuml;ndigungen";
}

if(!array_key_exists("id", $_GET))
{
    $_GET["id"] = 0;
}

if($g_preferences['enable_bbcode'] == 1)
{
    // Klasse fuer BBCode
    $bbcode = new ubbParser();
}

echo "
<!-- (c) 2004 - 2006 The Admidio Team - http://www.admidio.org - Version: ". getVersion(). " -->\n
<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
<html>
<head>
    <title>$g_current_organization->longname - ". $_GET["headline"]. "</title>
    <link rel=\"stylesheet\" type=\"text/css\" href=\"$g_root_path/adm_config/main.css\">";

    if($g_preferences['enable_rss'] == 1)
    {
        echo "<link type=\"application/rss+xml\" rel=\"alternate\" title=\"$g_current_organization->longname - Ankuendigungen\"
        href=\"$g_root_path/adm_program/modules/announcements/rss_announcements.php\">";
    };

    echo "
    <!--[if lt IE 7]>
    <script type=\"text/javascript\" src=\"$g_root_path/adm_program/system/correct_png.js\"></script>
    <![endif]-->";

    require("../../../adm_config/header.php");
echo "</head>";

require("../../../adm_config/body_top.php");
    echo "<div style=\"margin-top: 10px; margin-bottom: 10px;\" align=\"center\">
        <h1>". strspace($_GET["headline"]). "</h1>";

        // alle Gruppierungen finden, in denen die Orga entweder Mutter oder Tochter ist
        $arr_ref_orgas = $g_current_organization->getReferenceOrganizations();
        $organizations = "";
        $i             = 0;

        while($orga = current($arr_ref_orgas))
        {
            if($i > 0)
            {
                $organizations = $organizations. ", ";
            }
            $organizations = $organizations. "'$orga'";
            next($arr_ref_orgas);
            $i++;
        }

        // damit das SQL-Statement nachher nicht auf die Nase faellt, muss $organizations gefuellt sein
        if(strlen($organizations) == 0)
        {
            $organizations = "'$g_current_organization->shortname'";
        }

        // falls eine id fuer eine bestimmte Ankuendigung uebergeben worden ist...
        if($_GET['id'] > 0)
        {
            $sql    = "SELECT * FROM ". TBL_ANNOUNCEMENTS. "
                        WHERE ann_id = $_GET[id]";
        }
        //...ansonsten alle fuer die Gruppierung passenden Ankuendigungen aus der DB holen.
        else
        {
            $sql    = "SELECT * FROM ". TBL_ANNOUNCEMENTS. "
                        WHERE (  ann_org_shortname = '$g_organization'
                              OR (   ann_global   = 1
                                 AND ann_org_shortname IN ($organizations) ))
                        ORDER BY ann_timestamp DESC
                        LIMIT ". $_GET["start"]. ", 10 ";
        }

        $announcements_result = mysql_query($sql, $g_adm_con);
        db_error($announcements_result);

        // Gucken wieviele Datensaetze die Abfrage ermittelt kann...
        $sql    = "SELECT COUNT(*) FROM ". TBL_ANNOUNCEMENTS. "
                    WHERE (  ann_org_shortname = '$g_organization'
                          OR (   ann_global   = 1
                             AND ann_org_shortname IN ($organizations) ))
                    ORDER BY ann_timestamp ASC ";
        $result = mysql_query($sql, $g_adm_con);
        db_error($result);
        $row = mysql_fetch_array($result);
        $num_announcements = $row[0];

        // Icon-Links und Navigation anzeigen

        if($_GET['id'] == 0
        && (editAnnouncements() || $g_preferences['enable_rss'] == true))
        {
            echo "<p>";

            // Neue Ankuendigung anlegen
            if(editAnnouncements())
            {
                echo "<span class=\"iconLink\">
                    <a class=\"iconLink\" href=\"announcements_new.php?headline=". $_GET["headline"]. "\"><img
                    class=\"iconLink\" src=\"$g_root_path/adm_program/images/add.png\" style=\"vertical-align: middle;\" border=\"0\" alt=\"Neu anlegen\"></a>
                    <a class=\"iconLink\" href=\"announcements_new.php?headline=". $_GET["headline"]. "\">Neu anlegen</a>
                </span>";
            }

            if(editAnnouncements() && $g_preferences['enable_rss'] == true)
            {
                echo "&nbsp;&nbsp;&nbsp;&nbsp;";
            }

            // Feed abonnieren
            if($g_preferences['enable_rss'] == true)
            {
                echo "<span class=\"iconLink\">
                    <a class=\"iconLink\" href=\"$g_root_path/adm_program/modules/announcements/rss_announcements.php\"><img
                    class=\"iconLink\" src=\"$g_root_path/adm_program/images/feed.png\" style=\"vertical-align: middle;\" border=\"0\" alt=\"". $_GET["headline"]. "-Feed abonnieren\"></a>
                    <a class=\"iconLink\" href=\"$g_root_path/adm_program/modules/announcements/rss_announcements.php\">". $_GET["headline"]. "-Feed abonnieren</a>
                </span>";
            }

            echo "</p>";

            // Navigation mit Vor- und Zurueck-Buttons
            $base_url = "$g_root_path/adm_program/modules/announcements/announcements.php?headline=". $_GET["headline"];
            echo generatePagination($base_url, $num_announcements, 10, $_GET["start"], TRUE);
        }

        if (mysql_num_rows($announcements_result) == 0)
        {
            // Keine Ankuendigungen gefunden
            if($_GET['id'] > 0)
            {
                echo "<p>Der angeforderte Eintrag exisitiert nicht (mehr) in der Datenbank.</p>";
            }
            else
            {
                echo "<p>Es sind keine Eintr&auml;ge vorhanden.</p>";
            }
        }
        else
        {
            // Ankuendigungen auflisten
            $i = 0;

            while($row = mysql_fetch_object($announcements_result))
            {
                $sql     = "SELECT * FROM ". TBL_USERS. " WHERE usr_id = $row->ann_usr_id";
                $result2 = mysql_query($sql, $g_adm_con);
                db_error($result2);

                $user = mysql_fetch_object($result2);

                echo "
                <div class=\"boxBody\" style=\"overflow: hidden;\">
                    <div class=\"boxHead\">
                        <div style=\"text-align: left; float: left;\">
                            <img src=\"$g_root_path/adm_program/images/note.png\" style=\"vertical-align: top;\" alt=\"". strSpecialChars2Html($row->ann_headline). "\">&nbsp;".
                            strSpecialChars2Html($row->ann_headline). "
                        </div>";

                        // aendern & loeschen duerfen nur User mit den gesetzten Rechten
                        if(editAnnouncements())
                        {
                            echo "<div style=\"text-align: right;\">" .
                                mysqldatetime("d.m.y", $row->ann_timestamp). "&nbsp;
                                <img src=\"$g_root_path/adm_program/images/edit.png\" style=\"cursor: pointer;\" width=\"16\" height=\"16\" border=\"0\" alt=\"Bearbeiten\" title=\"Bearbeiten\"
                                onclick=\"self.location.href='announcements_new.php?ann_id=$row->ann_id&amp;headline=". $_GET['headline']. "'\">";

                                // Loeschen darf man nur Ankuendigungen der eigenen Gliedgemeinschaft
                                if($row->ann_org_shortname == $g_organization)
                                {
                                    echo "
                                    <img src=\"$g_root_path/adm_program/images/cross.png\" style=\"cursor: pointer;\" width=\"16\" height=\"16\" border=\"0\" alt=\"L&ouml;schen\" title=\"L&ouml;schen\" ";
                                    $load_url = urlencode("$g_root_path/adm_program/modules/announcements/announcements_function.php?ann_id=$row->ann_id&amp;mode=2&amp;url=$g_root_path/adm_program/modules/announcements/announcements.php");
                                    echo " onclick=\"self.location.href='$g_root_path/adm_program/system/err_msg.php?err_code=delete_announcement&amp;err_text=". urlencode($row->ann_headline). "&amp;err_head=L&ouml;schen&amp;button=2&amp;url=$load_url'\">";
                                }
                            echo "&nbsp;</div>";
                        }
                        else
                        {
                            echo "<div style=\"text-align: right;\">". mysqldatetime("d.m.y", $row->ann_timestamp). "&nbsp;</div>";
                        }
                    echo "</div>

                    <div style=\"margin: 8px 4px 4px 4px; text-align: left;\">";
                        // wenn BBCode aktiviert ist, die Beschreibung noch parsen, ansonsten direkt ausgeben
                        if($g_preferences['enable_bbcode'] == 1)
                        {
                            echo strSpecialChars2Html($bbcode->parse($row->ann_description));
                        }
                        else
                        {
                            echo nl2br(strSpecialChars2Html($row->ann_description));
                        }
                    echo "</div>
                    <div style=\"margin: 8px 4px 4px 4px; font-size: 8pt; text-align: left;\">
                        Angelegt von ". strSpecialChars2Html($user->usr_first_name). " ". strSpecialChars2Html($user->usr_last_name).
                        " am ". mysqldatetime("d.m.y h:i", $row->ann_timestamp). "
                    </div>
                </div>

                <br />";
                $i++;
            }  // Ende While-Schleife
        }

        if($_GET['id'] == 0 && $i > 2)
        {
            // Navigation mit Vor- und Zurueck-Buttons
            $base_url = "$g_root_path/adm_program/modules/announcements/announcements.php?headline=". $_GET["headline"];
            echo generatePagination($base_url, $num_announcements, 10, $_GET["start"], TRUE);
        }
    echo "</div>";

    require("../../../adm_config/body_bottom.php");
echo "</body>
</html>";
?>