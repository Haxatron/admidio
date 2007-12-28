<?php
/******************************************************************************
 * Liste aller Module und Administrationsseiten von Admidio
 *
 * Copyright    : (c) 2004 - 2007 The Admidio Team
 * Homepage     : http://www.admidio.org
 * Module-Owner : Markus Fassbender
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 *****************************************************************************/

// wenn noch nicht installiert, dann Install-Dialog anzeigen
if(!file_exists("../config.php"))
{
    $location = "Location: ../adm_install/index.php";
    header($location);
    exit();
}

include("system/common.php");

if($g_current_user->isWebmaster())
{
    // der Installationsordner darf aus Sicherheitsgruenden nicht existieren
    if($g_debug == 0 && file_exists("../adm_install"))
    {
        $g_message->show("installFolderExists");
    }
}

// Url-Stack loeschen
$_SESSION['navigation']->clear();

// Html-Kopf ausgeben
$g_layout['title']  = "Admidio &Uuml;bersicht";
$g_layout['header'] = "<link rel=\"stylesheet\" href=\"". THEME_PATH. "/mainpage.css\" type=\"text/css\" />";

require(THEME_SERVER_PATH. "/overall_header.php");

// Html des Modules ausgeben
echo "
<h1 class=\"moduleHeadline\">". $g_current_organization->getValue("org_longname"). "</h1>

<ul class=\"iconTextLinkList\">";
    if($g_valid_login == 1)
    {
        echo "<li>
            <span class=\"iconTextLink\">
                <a href=\"$g_root_path/adm_program/system/logout.php\"><img
                src=\"". THEME_PATH. "/icons/door_in.png\" alt=\"Logout\" /></a>
                <a href=\"$g_root_path/adm_program/system/logout.php\">Logout</a>
            </span>
        </li>";
    }
    else
    {
        echo "<li>
            <span class=\"iconTextLink\">
                <a href=\"$g_root_path/adm_program/system/login.php\"><img
                src=\"". THEME_PATH. "/icons/key.png\" alt=\"Login\" /></a>
                <a href=\"$g_root_path/adm_program/system/login.php\">Login</a>
            </span>
        </li>";
        
        if($g_preferences['registration_mode'] > 0)
        {
            echo "<li>
                <span class=\"iconTextLink\">
                    <a href=\"$g_root_path/adm_program/system/registration.php\"><img
                    src=\"". THEME_PATH. "/icons/add.png\" alt=\"Registrieren\" /></a>
                    <a href=\"$g_root_path/adm_program/system/registration.php\">Registrieren</a>
                </span>
            </li>";
        }
    }
echo "</ul>

<div class=\"formLayout\" id=\"modules_list_form\">
    <div class=\"formHead\">Module</div>
    <div class=\"formBody\">
        <ul class=\"formFieldList\">";
            if( $g_preferences['enable_announcements_module'] == 1 
            || ($g_preferences['enable_announcements_module'] == 2 && $g_valid_login))
            {
                echo "
                <li>
                    <dl>
                        <dt>
                            <a href=\"$g_root_path/adm_program/modules/announcements/announcements.php\"><img 
                            src=\"". THEME_PATH. "/icons/note_big.png\" alt=\"Ank&uuml;ndigungen\" /></a>
                        </dt>
                        <dd>
                            <span class=\"veryBigFontSize\"><a href=\"$g_root_path/adm_program/modules/announcements/announcements.php\">Ank&uuml;ndigungen</a></span><br />
                            <span class=\"smallFontSize\">Hier k&ouml;nnen Ank&uuml;ndigungen (News / Aktuelles) angeschaut, erstellt und bearbeitet werden.</span>
                        </dd>
                    </dl>
                </li>";
            }

            if($g_preferences['enable_download_module'] == 1)
            {
                echo "
                <li>
                    <dl>
                        <dt>
                            <a href=\"$g_root_path/adm_program/modules/download/download.php\"><img 
                            src=\"". THEME_PATH. "/icons/download_big.png\" alt=\"Download\" /></a>
                        </dt>
                        <dd>
                            <span class=\"veryBigFontSize\"><a href=\"$g_root_path/adm_program/modules/download/download.php\">Download</a></span><br />
                            <span class=\"smallFontSize\">Benutzer k&ouml;nnen Dateien aus bestimmten Verzeichnissen herunterladen.</span>
                        </dd>
                    </dl>
                </li>";
            }

            if($g_preferences['enable_mail_module'] == 1)
            {
                echo "
                <li>
                    <dl>
                        <dt>
                            <a href=\"$g_root_path/adm_program/modules/mail/mail.php\"><img 
                            src=\"". THEME_PATH. "/icons/mail_open_big.png\" alt=\"E-Mail\" /></a>
                        </dt>
                        <dd>
                            <span class=\"veryBigFontSize\"><a href=\"$g_root_path/adm_program/modules/mail/mail.php\">E-Mail</a></span><br />
                            <span class=\"smallFontSize\">E-Mails an Rollen (Gruppen / Kurse / Abteilungen) schreiben.</span>
                        </dd>
                    </dl>
                </li>";
            }

            if($g_preferences['enable_photo_module'] == 1)
            {
                echo "
                <li>
                    <dl>
                        <dt>
                            <a href=\"$g_root_path/adm_program/modules/photos/photos.php\"><img 
                            src=\"". THEME_PATH. "/icons/photo_big.png\" alt=\"Fotos\" /></a>
                        </dt>
                        <dd>
                            <span class=\"veryBigFontSize\"><a href=\"$g_root_path/adm_program/modules/photos/photos.php\">Fotos</a></span><br />
                            <span class=\"smallFontSize\">Eine Fotoverwaltung bei der berechtigte Benutzer online Fotos hochladen k&ouml;nnen.</span>
                        </dd>
                    </dl>
                </li>";
            }

            if( $g_preferences['enable_guestbook_module'] == 1 
            || ($g_preferences['enable_guestbook_module'] == 2 && $g_valid_login))
            {
                echo "
                <li>
                    <dl>
                        <dt>
                            <a href=\"$g_root_path/adm_program/modules/guestbook/guestbook.php\"><img 
                            src=\"". THEME_PATH. "/icons/guestbook_big.png\" alt=\"G&auml;stebuch\" /></a>
                        </dt>
                        <dd>
                            <span class=\"veryBigFontSize\"><a href=\"$g_root_path/adm_program/modules/guestbook/guestbook.php\">G&auml;stebuch</a></span><br />
                            <span class=\"smallFontSize\">Hier k&ouml;nnen Besucher ihre Gr&uuml;&szlig;e und Anmerkungen eintragen.</span>
                        </dd>
                    </dl>
                </li>";
            }

            echo "
            <li>
                <dl>
                    <dt>
                        <a href=\"$g_root_path/adm_program/modules/lists/lists.php\"><img 
                        src=\"". THEME_PATH. "/icons/list_big.png\" alt=\"Listen\" /></a>
                    </dt>
                    <dd>
                        <span class=\"veryBigFontSize\"><a href=\"$g_root_path/adm_program/modules/lists/lists.php\">Listen</a></span>&nbsp;&nbsp;
                        &#91; <a href=\"$g_root_path/adm_program/modules/lists/mylist.php\">Eigene Liste</a>&nbsp;|
                        <a href=\"$g_root_path/adm_program/modules/lists/lists.php?active_role=0\">Inaktive Rollen</a> &#93;<br />
                        <span class=\"smallFontSize\">Verschiedene Benutzerlisten der Rollen (Gruppen / Kurse / Abteilungen) anzeigen.</span>
                    </dd>
                </dl>
            </li>";

            echo "
            <li>
                <dl>
                    <dt>
                        <a href=\"$g_root_path/adm_program/modules/profile/profile.php\"><img 
                        src=\"". THEME_PATH. "/icons/person_big.png\" alt=\"Profil\" /></a>
                    </dt>
                    <dd>
                        <span class=\"veryBigFontSize\"><a href=\"$g_root_path/adm_program/modules/profile/profile.php\">Profil</a></span>";
                        if($g_valid_login)
                        {
                            echo "&nbsp;&nbsp;
                            &#91; <a href=\"$g_root_path/adm_program/modules/profile/profile_new.php?user_id=". $g_current_user->getValue("usr_id"). "\">Profil bearbeiten</a> &#93;";
                        }
                        echo "<br />
                        <span class=\"smallFontSize\">Das eigene Profil anschauen und bearbeiten.</span>
                    </dd>
                </dl>
            </li>";

            if( $g_preferences['enable_dates_module'] == 1 
            || ($g_preferences['enable_dates_module'] == 2 && $g_valid_login))
            {
                echo "
                <li>
                    <dl>
                        <dt>
                            <a href=\"$g_root_path/adm_program/modules/dates/dates.php\"><img 
                            src=\"". THEME_PATH. "/icons/history_big.png\" alt=\"Termine\" /></a>
                        </dt>
                        <dd>
                            <span class=\"veryBigFontSize\"><a href=\"$g_root_path/adm_program/modules/dates/dates.php\">Termine</a></span>&nbsp;&nbsp;
                            &#91; <a href=\"$g_root_path/adm_program/modules/dates/dates.php?mode=old\">Vergangene Termine</a> &#93;<br />
                            <span class=\"smallFontSize\">Hier k&ouml;nnen Termine angeschaut, erstellt und bearbeitet werden.</span>
                        </dd>
                    </dl>
                </li>";
            }

            if( $g_preferences['enable_weblinks_module'] == 1
            || ($g_preferences['enable_weblinks_module'] == 2 && $g_valid_login))
            {
                echo "
                <li>
                    <dl>
                        <dt>
                            <a href=\"$g_root_path/adm_program/modules/links/links.php\"><img 
                            src=\"". THEME_PATH. "/icons/globe_big.png\" alt=\"Weblinks\" /></a>
                        </dt>
                        <dd>
                            <span class=\"veryBigFontSize\"><a href=\"$g_root_path/adm_program/modules/links/links.php\">Weblinks</a></span><br />
                            <span class=\"smallFontSize\">Hier k&ouml;nnen Links zu interessanten Webseiten abgelegt werden.</span>
                        </dd>
                    </dl>
                </li>";
            }

            // Wenn das Forum aktiv ist, dieses auch in der Ã?bersicht anzeigen.
            if($g_forum_integriert)
            {
                if($g_forum->session_valid)
                {
                    $forumstext = "Sie sind als <b>".$g_forum->user."</b> im Forum <b>".$g_forum->sitename."</b> angemeldet ".
                                  $g_forum->getUserPM($g_current_user->getValue("usr_login_name"));
                }
                else
                {
                    $forumstext = "Der virtuelle Treffpunkt zum Austausch von Gedanken und Erfahrungen.";
                }
                echo "
                <li>
                    <dl>
                        <dt>
                            <a href=\"http://". $_SERVER['HTTP_HOST']. "/".$g_forum->path."/index.php\"><img 
                            src=\"". THEME_PATH. "/icons/forum_big.png\" alt=\"Forum\" /></a>
                        </dt>
                        <dd>
                            <span class=\"veryBigFontSize\"><a href=\"http://". $_SERVER['HTTP_HOST']. "/".$g_forum->path."/index.php\">Forum</a></span><br />
                            <span class=\"smallFontSize\">$forumstext</span>
                        </dd>
                    </dl>
                </li>";
            }
        echo "
        </ul>
    </div>
</div>";

if($g_current_user->isWebmaster() || $g_current_user->assignRoles() || $g_current_user->approveUsers() || $g_current_user->editUser())
{
    echo "
    <div class=\"formLayout\" id=\"administration_list_form\">
        <div class=\"formHead\">Administration</div>
        <div class=\"formBody\">
            <ul class=\"formFieldList\">";
                if($g_current_user->approveUsers() && $g_preferences['registration_mode'] > 0)
                {
                    echo "
                    <li>
                        <dl>
                            <dt>
                                <a href=\"$g_root_path/adm_program/administration/new_user/new_user.php\"><img 
                                src=\"". THEME_PATH. "/icons/ok_big.png\" alt=\"Web-Anmeldungen\" /></a>
                            </dt>
                            <dd>
                                <span class=\"veryBigFontSize\"><a href=\"$g_root_path/adm_program/administration/new_user/new_user.php\">Neue Web-Anmeldungen verwalten</a></span><br />
                                <span class=\"smallFontSize\">Besucher, die sich auf der Homepage registriert haben, k&ouml;nnen hier freigeschaltet oder abgelehnt werden.</span>
                            </dd>
                        </dl>
                    </li>";
                }

                if($g_current_user->editUser())
                {
                    echo "
                    <li>
                        <dl>
                            <dt>
                                <a href=\"$g_root_path/adm_program/administration/members/members.php\"><img 
                                src=\"". THEME_PATH. "/icons/person_admin_big.png\" alt=\"Benutzerverwaltung\" /></a>
                            </dt>
                            <dd>
                                <span class=\"veryBigFontSize\"><a href=\"$g_root_path/adm_program/administration/members/members.php\">Benutzerverwaltung</a></span><br />
                                <span class=\"smallFontSize\">Mitglieder (Benutzer) k&ouml;nnen entfernt und neue Mitglieder (Benutzer) k&ouml;nnen in der Datenbank anlegt werden.</span>
                            </dd>
                        </dl>
                    </li>";
                }

                if($g_current_user->assignRoles())
                {
                    echo "
                    <li>
                        <dl>
                            <dt>
                                <a href=\"$g_root_path/adm_program/administration/roles/roles.php\"><img 
                                src=\"". THEME_PATH. "/icons/wand_big.png\" alt=\"Rollenverwaltung\" /></a>
                            </dt>
                            <dd>
                                <span class=\"veryBigFontSize\"><a href=\"$g_root_path/adm_program/administration/roles/roles.php\">Rollenverwaltung</a></span><br />
                                <span class=\"smallFontSize\">Rollen (Gruppen / Kurse / Abteilungen) k&ouml;nnen hier erstellt oder bearbeitet werden.</span>
                            </dd>
                        </dl>
                    </li>";
                }

                if($g_current_user->isWebmaster())
                {
                    echo "
                    <li>
                        <dl>
                            <dt>
                                <a href=\"$g_root_path/adm_program/administration/organization/organization.php\"><img 
                                src=\"". THEME_PATH. "/icons/options_big.png\" alt=\"Organisationseinstellungen\" /></a>
                            </dt>
                            <dd>
                                <span class=\"veryBigFontSize\"><a href=\"$g_root_path/adm_program/administration/organization/organization.php\">Organisationseinstellungen</a></span><br />
                                <span class=\"smallFontSize\">Einstellungen f&uuml;r die Organisation, spezifische Profilfelder und Rollenkategorien k&ouml;nnen hier bearbeitet werden.</span>
                            </dd>
                        </dl>
                    </li>";
                }
            echo "
            </ul>
        </div>
    </div>";
}

require(THEME_SERVER_PATH. "/overall_footer.php");

?>