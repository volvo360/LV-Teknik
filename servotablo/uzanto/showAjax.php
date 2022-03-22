<?php
    session_start();
    include_once("../../common/db.php");
    include_once("../../common/crypto.php");
    include_once("../../common/userData.php");
    include_once("../../administrado/ext/theme/nav.php");
    

    if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME']))
    {
        $replaceTable = getReplaceTable(false);
        if ($replaceTable[$_POST['replaceTable']] === PREFIX.'users')
        {
            showAjaxUsers();
        }
    }

    function showAjaxUsers()
    {
        include_once("../../administrado/editProfile.php");
        
        $_POST['editProfile'] = $_POST['id'];
        editUserProfile();
    }

?>