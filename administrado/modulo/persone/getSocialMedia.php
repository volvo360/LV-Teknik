<?php
session_start();

error_reporting(E_ALL);

unset($_SESSION['replaceTempKey']);

include_once("../../../common/db.php");
include_once("./../../../administrado/ext/theme/nav.php");
include_once("../../../common/crypto.php");
include_once("../../../common/userData.php");

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME']))
{
    showAjaxsGetSocialMedia();
	
	unset($_SESSION['replaceTempKey']);
}

function showAjax_getSocialMedia()
{
    global $link;
    global $link_k;
    
    global $phrase;
    global $phrase_k;
    
    $replaceTable = getReplaceTable();
    
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

    foreach ($displayLang as $key => $value)
    {
        $order[] = "WHEN lang = '".$value."' THEN ".$i;
        $order_lang[] = "WHEN Code = '".$value."' THEN ".$i;
        $i++;
    }

    $langStrings = getlangstrings();
    $showAjax_social_media = $langStrings['showAjax_social_media'];

    $showAjax_social_media_array = getLangstringsArray('showAjax_social_media_array', $displayLang);
    
    $table = "`".PREFIX."social_media`";
    
    $table2 = "`".PREFIX."icons`";
    $table3 = "`".PREFIX."social_media2user`";
    
    echo "<div id = \"ajaxHeaderModal\">";
    
    echo "</div>";
    
    echo "<div id = \"ajaxBodyModal\">";
    
        $sql = "SELECT * FROM (SELECT node.tableKey, node.socialMediaId, node.lft, node.rgt, CAST(AES_DECRYPT(node.note, SHA2('".$phrase."', 512)) AS CHAR) as note, (COUNT(parent.lft) - 1) AS depth
                  FROM ".$table." AS node,
                          ".$table." AS parent
                  WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
                  ORDER BY node.lft LIMIT 18446744073709551615) as t1 LEFT OUTER JOIN (SELECT socialMediaId as socialMediaId, CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as userUrl FROM $table3 WHERE userId = '".$_SESSION['uid']."') as t3 ON t1.socialMediaId = t3.socialMediaId";
        //echo $sql."<br>";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        echo "Fetmarkerade socilamedier innehåller länk till din profil!<br><br>";
    
        echo "<div class = \"row\">";
			echo "<div class =\"col-md-4\" style=\"max-height : 66vh; height : 66vh; overflow : auto\">";
				echo "<div id = \"tree_".$replaceTable[PREFIX.'social_media2user']."\" class =\"fancyTreeClass\" data-ajax_target = \"ajaxSocial_media\" data-replace_table = \"".$replaceTable[PREFIX.'social_media']."\">";
					echo "<ul id = \"tree_".$replaceTable[PREFIX.'social_media']."-data\" style = \"display:none;\" >";
						while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
						{
							if ($oldDepth > (int)$row['depth'])
							{
								for ($i = 0; $i < ($oldDepth - (int)$row['depth']); $i++)
								{
									echo "</ul></li>";
								}
							}

							if (((int)$row['lft'] + 1 < (int)$row['rgt'])) 
							{
								echo "<li class = \"folder expanded\" id = \"".$row['tableKey']."\">";
                                    if (!empty(trim($row['userUrl'])))
                                    {
                                        echo "<b>";
                                    }
                                    echo $row['note'];
                                    if (!empty(trim($row['userUrl'])))
                                    {
                                        echo "</b>";
                                    }
                                echo "</li><ul>";
							}
							else
							{
								echo "<li id = \"".$row['tableKey']."\">";
                                    if (!empty(trim($row['userUrl'])))
                                    {
                                        echo "<b>";
                                    }
                                    echo $row['note'];
                                    if (!empty(trim($row['userUrl'])))
                                    {
                                        echo "</b>";
                                    }
                                echo "</li>";
							}

							$oldDepth = (int)$row['depth'];
						}

						if ($oldDepth > 0)
						{
							for ($i = 0; $i < ($oldDepth); $i++)
							{
								echo "</ul></li>";
							}
						}
					echo "</ul>";
				echo "</div><br><br>";
		      echo "</div>";
		
			echo "<div class = \"col-md-8\" id = \"ajaxSocial_media\">";
		
			echo "</div>";
		echo "</div>";
    echo "</div>";
    
    echo "<div id = \"ajaxFooterModal\">";
        echo "<button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Close</button>";
        //echo "<button type=\"button\" class=\"btn btn-primary\">Save changes</button>";
    echo "</div>";
    
}

//if (!(function_exists("showAjax")))
{
	function showAjaxsGetSocialMedia()
	{
		$replaceTable = getReplaceTable(false);
        if ($replaceTable[$_POST['replaceTable']] == PREFIX."social_media")
		{
            showAjax_getSocialMedia();
		}
		
        echo "<input type = \"hidden\" id = \"replaceTable\" value =\"".$_POST['replaceTable']."\">";
	}
}
?>