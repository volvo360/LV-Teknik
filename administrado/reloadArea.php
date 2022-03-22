<?php
    session_start();

    error_reporting(E_ALL);

    include_once("../common/db.php");
    include_once("../common/crypto.php");
    include_once("../common/userData.php");
    include_once("./administrado/ext/theme/nav.php");

    include_once("./../../common/syncDB.php");

    if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME']))
    {
        $replaceTable = getReplaceTable(false);
        
        echo __LINE__." ".$replaceTable[$_POST['replaceTable']]."<br>";
        
        if ($replaceTable[$_POST['replaceTable']] === PREFIX."menu")
        {
            include_once("showAjax.php");
            
            syncFieldOwnPage();
            
            showAjax_menu();
        }
    }
?>