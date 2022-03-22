<?php

header('Content-Type: application/json');

include_once("../common/db.php");
include_once("../common/crypto.php");
include_once("../common/userData.php");

$siteSettings = getSiteSettings();
$userSettings = getUserSettings();

$displayLang = array_map("trim", explode(",", $userSettings['langService']));

if (empty($userSettings['langService']))
{
    $data = array_map("trim", explode(",",$_SERVER['HTTP_ACCEPT_LANGUAGE']));
    foreach ($data as $key => $value)
    {
        if (!startswith($value, "q=" ))
        {
            $displayLang[] = substr($value,0,2);
        }
    }

    $displayLang = array_filter(array_unique($displayLang));
}
else
{    
    $displayLang = array_map("trim", explode(",", $userSettings['langService']));
}

$i = 0;

$displayLang = array_map("trim", explode(",", $siteSettings['language']));

foreach ($displayLang as $key => $value)
{
    $order[] = "WHEN lang = '".$value."' THEN ".$i;
    $order_lang[] = "WHEN code = '".$value."' THEN ".$i;
    $i++;
}

function retriveTree($sql = null, $mode = 'default', $field = null)
{
    global $link;
    global $link_k;

    global $phrase;
    global $phrase_k;

    $lang = $settings['def_lang'] = 'sv';

    $lang = 'sv';

    //echo $sql."<br>";

    if ($mode == 'default')
    {
        $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
    }
    else if ($mode == 'customer')
    {
        $result = mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link_k));
    }

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        foreach ($row as $key => $value)
        {
            if ($key !== 'autoId' && $key !== $field)
            {
                $data[$row[$field]][$key] = $value;
            }

        }
    }

    return $data;
}

function getTree_menu()
{
    global $phrase;
    global $phrase_k;

    $table = PREFIX."menu";
    $table2 = PREFIX."menu_lang";

    $siteSettings = getSiteSettings();

    $userSettings = getUserSettings();

    $displayLang = array_map("trim", explode(",", $userSettings['langService']));

    if (empty($userSettings['langService']))
    {
        $data = array_map("trim", explode(",",$_SERVER['HTTP_ACCEPT_LANGUAGE']));
        foreach ($data as $key => $value)
        {
            if (!startswith($value, "q=" ))
            {
                $displayLang[] = substr($value,0,2);
            }
        }

        $displayLang = array_filter(array_unique($displayLang));
    }
    else
    {    
        $displayLang = array_map("trim", explode(",", $userSettings['langService']));
    }

    $i = 0;

    $displayLang = array_map("trim", explode(",", $siteSettings['language']));

    foreach ($displayLang as $key => $value)
    {
        $order[] = "WHEN lang = '".$value."' THEN ".$i;
        $order_lang[] = "WHEN code = '".$value."' THEN ".$i;
        $i++;
    }

    $sql = "SELECT * FROM (SELECT node.menuId, node.lft, node.rgt, CAST(AES_DECRYPT(node.folder, SHA2('".$phrase."', 512)) AS CHAR) as folder, 
                      CAST(AES_DECRYPT(node.file, SHA2('".$phrase."', 512)) AS CHAR) as file, node.type, node.tableKey, node.displayMenu, (COUNT(parent.menuId) - 1) AS depth
              FROM $table AS node,
                      $table AS parent
              WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
              ORDER BY node.lft LIMIT 18446744073709551615) as menu INNER JOIN 
              (SELECT * FROM (SELECT menuId, 
                      CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
                      CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2 
                      ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
                      GROUP BY menuId) AS lang ON menu.menuId = lang.menuId WHERE displayMenu > 0 ORDER BY menu.lft";
    //echo $sql."<br>";
    return retriveTree($sql, 'default', 'menuId');
}

function renderTree($tree = null)
{
    if ($_SERVER['SERVER_NAME'] === 'localhost')
    {
        $url = "http://localhost/lv/";
        $url_admin = "//localhost/lv/administrado/";
    }
    else if ($_SERVER['SERVER_NAME'] === 'server01')
    {
        $url = "//server01/flexshare/ep/";
        $url_admin = "//server01/flexshare/ep/administrado/";
    }
    else
    {
        $url = "//www.lvteknik.se/";
        $url_admin = "//www.lvteknik.se/administrado/";
    }
    
    $oldDepth = 0;
    
    $first = true;
    
    $data .= "[";
        foreach ($tree as $key => $row)
        {
            if ($oldDepth > (int)$row['depth'])
            {
                for ($i = 0 ; $i < ($oldDepth -(int)$row['depth'] ); $i++ )
                {
                    $data .= "]},";
                }
            }
            if ((int)$row['lft'] +1 < (int)$row['rgt'])
            {
                
                $data .= "{\"title\" : \"".$row['note']."\",  \"menu\" : [";
            }
            else
            {
                $data .= "{\"title\": \"".$row['note']."\", \"value\": \"";
                if ($row['type'] == "text" || $row['type'] == "gallery")
                {
                    $data .= $url."ownfile.php?pageId=".$row['tableKey'];
                }
                else
                {
                    $data .= $url;
                    if (!empty($row['folder']))
                    {
                        $data .= $row['folder']."/";
                    }
                    $data .= $row['file'];
                }
                $data .= "\"},";
            }
            $oldDepth = (int)$row['depth'];
        }
        for ($i = 1 ; $i < $oldDepth; $i++ )
        {
            $data .= "]},";
        }
    
    $data .= "]";
    
    $data = str_replace(",}", "}", $data);
    $data = str_replace(",]", "]", $data);
    
    /*$data = "[
   {
      \"title\":\"Tiny Home Page\",
      \"value\":\"https://www.tiny.cloud\"
   },
   {
      \"title\":\"Tiny Blog\",
      \"value\":\"https://www.tiny.cloud/blog\"
   },
   {
      \"title\":\"TinyMCE Support resources\",
      \"menu\":[
         {
            \"title\":\"TinyMCE Documentation\",
            \"value\":\"https://www.tiny.cloud/docs/\"
         },
         {
            \"title\":\"TinyMCE on Stack Overflow\",
            \"value\":\"https://stackoverflow.com/questions/tagged/tinymce\"
         },
         {
            \"title\":\"TinyMCE GitHub\",
            \"value\":\"https://github.com/tinymce/\"
         }
      ]
   }
]
";*/
    
    return $data;
}

$treeData = getTree_menu();

echo renderTree($treeData);