<?php
/**
 ***********************************************************************************************
 * Various functions for categories
 *
 * @copyright 2004-2021 The Admidio Team
 * @see http://www.admidio.org/
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2.0 only
 ***********************************************************************************************
 */

/******************************************************************************
 * Parameters:
 *
 * menu_uuid : UUID of the menu that should be edited
 * mode      : 1 - Create or edit menu
 *             2 - Delete menu
 *             3 - Change sequence for parameter men_id
 * sequence  : New sequence for the parameter men_id
 *
 *****************************************************************************/

require_once(__DIR__ . '/../../system/common.php');

// Initialize and check the parameters
$getMenuUuid = admFuncVariableIsValid($_GET, 'menu_uuid','string');
$getMode     = admFuncVariableIsValid($_GET, 'mode',     'int',    array('requireValue' => true));
$getSequence = admFuncVariableIsValid($_GET, 'sequence', 'string', array('validValues' => array(TableMenu::MOVE_UP, TableMenu::MOVE_DOWN)));

// check rights
if(!$gCurrentUser->isAdministrator())
{
    $gMessage->show($gL10n->get('SYS_NO_RIGHTS'));
}

// create menu object
$menu = new TableMenu($gDb);

if($getMenuUuid !== '')
{
    $menu->readDataByUuid($getMenuUuid);
}

// create menu or update it
if($getMode === 1)
{
    $_SESSION['menu_request'] = $_POST;

    try
    {
        // check the CSRF token of the form against the session token
        SecurityUtils::validateCsrfToken($_POST['admidio-csrf-token']);
    }
    catch(AdmException $exception)
    {
        $exception->showHtml();
        // => EXIT
    }

    $postIdParent = admFuncVariableIsValid($_POST, 'men_men_id_parent', 'int');
    $postComId    = admFuncVariableIsValid($_POST, 'men_com_id',        'int');
    $postName     = admFuncVariableIsValid($_POST, 'men_name',          'string', array('default' => ''));
    $postDesc     = admFuncVariableIsValid($_POST, 'men_description',   'string', array('default' => ''));
    $postUrl      = admFuncVariableIsValid($_POST, 'men_url',           'string', array('default' => ''));
    $postIcon     = admFuncVariableIsValid($_POST, 'men_icon',          'string', array('default' => ''));

    // within standard menu items the url should not be changed
    if($menu->getValue('men_standard'))
    {
        $postUrl = $menu->getValue('men_url');
    }

    // Check if mandatory fields are filled
    if($postName === '')
    {
        $gMessage->show($gL10n->get('SYS_FIELD_EMPTY', array($gL10n->get('SYS_NAME'))));
        // => EXIT
    }

    if($postUrl === '')
    {
        $gMessage->show($gL10n->get('SYS_FIELD_EMPTY', array($gL10n->get('ORG_URL'))));
        // => EXIT
    }

    // check if font awesome syntax is used or if its a valid filename syntax
    if($postIcon !== '' && !preg_match('/fa-[a-zA-z0-9]/', $postIcon))
    {
        try
        {
            StringUtils::strIsValidFileName($postIcon, true);
        }
        catch (AdmException $e)
        {
            $gMessage->show($gL10n->get('SYS_INVALID_FONT_AWESOME'));
            // => EXIT
        }
    }

    $menu->setValue('men_icon', $postIcon);
    $menu->setValue('men_men_id_parent', $postIdParent);
    $menu->setValue('men_name', $postName);
    $menu->setValue('men_description', $postDesc);

    if(!$menu->getValue('men_standard'))
    {
        $menu->setValue('men_url', $postUrl);
        $menu->setValue('men_com_id', $postComId);
    }

    // save Data to Table
    $returnCode = $menu->save();

    if($returnCode < 0)
    {
        $gMessage->show($gL10n->get('SYS_NO_RIGHTS'));
    }

    // Read current roles of the menu
    $displayMenu = new RolesRights($gDb, 'menu_view', (int) $menu->getValue('men_id'));
    $rolesDisplayRight = $displayMenu->getRolesIds();

    if(!isset($_POST['menu_view']) || !is_array($_POST['menu_view']))
    {
        // remove all entries, so it is allowed without login
        $displayMenu->removeRoles($rolesDisplayRight);
    }
    else
    {
        // add new or update roles
        $displayMenu->addRoles(array_map('intval', $_POST['menu_view']));
    }

    if($gNavigation->count() > 1)
    {
        $gNavigation->deleteLastUrl();
    }
    else
    {
        $gNavigation->addUrl($gHomepage, 'Home');
    }

    unset($_SESSION['menu_request']);

    header('Location: '. $gNavigation->getUrl());
    exit();
}
elseif($getMode === 2)
{
    // delete menu
    if($menu->delete())
    {
        echo 'done';
    }
}
elseif($getMode === 3)
{
    // Update menu order
    $menu->moveSequence($getSequence);
    exit();
}
