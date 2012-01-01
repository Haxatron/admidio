<?php
/******************************************************************************
 * Sidebar Announcements
 *
 * Version 1.3.0
 *
 * Plugin das die letzten X Ankuendigungen in einer schlanken Oberflaeche auflistet
 * und so ideal in einer Seitenleiste eingesetzt werden kann
 *
 * Compatible with Admidio version 2.3.0
 *
 * Copyright    : (c) 2004 - 2012 The Admidio Team
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 *****************************************************************************/

// create path to plugin
$plugin_folder_pos = strpos(__FILE__, 'adm_plugins') + 11;
$plugin_file_pos   = strpos(__FILE__, 'sidebar_announcements.php');
$plugin_folder     = substr(__FILE__, $plugin_folder_pos+1, $plugin_file_pos-$plugin_folder_pos-2);

if(!defined('PLUGIN_PATH'))
{
    define('PLUGIN_PATH', substr(__FILE__, 0, $plugin_folder_pos));
}
require_once(PLUGIN_PATH. '/../adm_program/system/common.php');
require_once(PLUGIN_PATH. '/../adm_program/system/classes/table_announcement.php');
require_once(PLUGIN_PATH. '/'.$plugin_folder.'/config.php');

// pruefen, ob alle Einstellungen in config.php gesetzt wurden
// falls nicht, hier noch mal die Default-Werte setzen
if(isset($plg_announcements_count) == false || is_numeric($plg_announcements_count) == false)
{
    $plg_announcements_count = 2;
}
if(isset($plg_max_char_per_word) == false || is_numeric($plg_max_char_per_word) == false)
{
    $plg_max_char_per_word = 0;
}

if(isset($plg_link_class))
{
    $plg_link_class = strip_tags($plg_link_class);
}
else
{
    $plg_link_class = '';
}

if(isset($plg_link_target))
{
    $plg_link_target = strip_tags($plg_link_target);
}
else
{
    $plg_link_target = '_self';
}

if(isset($plg_headline))
{
    $plg_headline = strip_tags($plg_headline);
}
else
{
    $plg_headline = $gL10n->get('ANN_ANNOUNCEMENTS');
}

// set database to admidio, sometimes the user has other database connections at the same time
$gDb->setCurrentDB();

// alle Organisationen finden, in denen die Orga entweder Mutter oder Tochter ist
$plg_organizations = "";
$plg_arr_orgas = $gCurrentOrganization->getReferenceOrganizations(true, true);

foreach($plg_arr_orgas as $key => $value)
{
	$plg_organizations = $plg_organizations. '\''.$value.'\', ';
}
$plg_organizations = $plg_organizations. '\''. $gCurrentOrganization->getValue('org_shortname'). '\'';

// nun alle relevanten Ankuendigungen finden
$sql    = 'SELECT * FROM '. TBL_ANNOUNCEMENTS. '
			WHERE (  ann_org_shortname = \''. $gCurrentOrganization->getValue('org_shortname').'\'
				  OR (   ann_global   = 1
					 AND ann_org_shortname IN ('.$plg_organizations.') ))
			ORDER BY ann_timestamp_create DESC
			LIMIT '.$plg_announcements_count;
$plg_result = $gDb->query($sql);
$plg_announcement = new TableAnnouncement($gDb);

echo '<div id="plugin_'. $plugin_folder. '" class="admPluginContent">
<div class="admPluginHeader"><h3>'.$gL10n->get('ANN_ANNOUNCEMENTS').'</h3></div>
<div class="admPluginBody">';

if($gDb->num_rows($plg_result) > 0)
{
    while($plg_row = $gDb->fetch_object($plg_result))
    {
        $plg_announcement->clear();
        $plg_announcement->setArray($plg_row);
        
        echo '<a class="'. $plg_link_class. '" href="'. $g_root_path. '/adm_program/modules/announcements/announcements.php?id='. $plg_announcement->getValue("ann_id"). '&amp;headline='. $plg_headline. '" target="'. $plg_link_target. '">';
        
        if($plg_max_char_per_word > 0)
        {
            $plg_new_headline = "";
            unset($plg_words);
        
            // Woerter unterbrechen, wenn sie zu lang sind
            $plg_words = explode(" ", $plg_announcement->getValue('ann_headline'));
            
            foreach($plg_words as $plg_key => $plg_value)
            {
                if(strlen($plg_value) > $plg_max_char_per_word)
                {
                    $plg_new_headline = $plg_new_headline.' '. substr($plg_value, 0, $plg_max_char_per_word). '-<br />'. 
                                    substr($plg_value, $plg_max_char_per_word);
                }
                else
                {
                    $plg_new_headline = $plg_new_headline.' '. $plg_value;
                }
            }
            echo $plg_new_headline.'</a><br />';
        }
        else
        {
            echo $plg_announcement->getValue('ann_headline').'</a><br />';
        }
         
        echo '(&nbsp;'. $plg_announcement->getValue('ann_timestamp_create', $gPreferences['system_date']). '&nbsp;)<hr />';
    }
    
    echo '<a class="'.$plg_link_class.'" href="'.$g_root_path.'/adm_program/modules/announcements/announcements.php?headline='.$plg_headline.'" target="'.$plg_link_target.'">'.$gL10n->get('SYS_ALL').' '.$plg_headline.'</a>';
}
else
{
    echo $gL10n->get('SYS_NO_ENTRIES');
}

echo '</div></div>';

?>