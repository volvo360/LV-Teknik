// JavaScript Document
$( document ).ready(function() {
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    });
	
	fancytree_fancyTreeClass();
	
	function fancytree_fancyTreeClass()
	{
        $.each( $( ".fancyTreeClass" ), function() {
            
            if ($.ui.fancytree.getTree("#"+$(this).attr("id")))
            {
                return;
            }
            
            $treeId = $(this).attr("id");
            
			$(this).fancytree({
				treeId : $(this).attr("id"),
				
				select: function (event, data) {
				if (data.targetType == 'checkbox') 
				{
					//I would like the update to be carried out here, but dosent want to work as intended on the page.
				  //extract_tree($tree_name, $replaceTable);
				}
			  },

				activate: function(event, data) {

                    var $formData = new Object;
                    
					var node = data.node;
					var id = data.node.key;
                    
                    //$("#"+id).text($("#"+id).text());
                   // $("#"+id).find("b").contents().unwrap();
                   //$.ui.fancytree.getTree("#"+$treeId).reload();
                    
					var $target = $(this).data("ajax_target");

                    var treeId = $(this).attr("id");
                    
                    if (data.node.data.target_div !== undefined)
                    {
                        $target = $(this).data("ajax_target");
                        if (!$target)
                        {
                            $target = "ajaxHeaderTree";
                        }
                        
                        if ($("#"+data.node.data.target_div))
                        {
                            /*console.log("#"+$target);
                            if ($("#"+$target).length > 0)
                            {
                                console.log("Jippie diven finns.....");
                            }
                            
                            console.log("#"+data.node.data.target_div);
                            if ($("#"+data.node.data.target_div).length > 0)
                            {
                                console.log("Jippie subdiven finns.....");
                            }*/
                            $("#"+$target).scrollTop($("#"+data.node.data.target_div).position().top);
                        }
                        
                        return false;
                    }
                    
                    if (data.node.extraClasses == "getTranslationLang")
                    {
                        if ($("#primaryLang").length > 0)
                        {
                            $formData['primary'] = $("#primaryLang").selectpicker("val");
                            $formData['secondary'] = $("#secondaryLang").selectpicker("val");
                        }
                    }
                    
					$formData['id'] = id;

                    if (data.node.data['replace_table'] !== undefined)
                    {
                       $formData['replaceTable'] = data.node.data['replace_table']; 
                    }
                    else
                    {
                        $formData['replaceTable'] = $(this).data("replace_table");
                    }
                    
                    if ($("#projectReplaceKey").length > 0)
                    {
                        $formData['projectReplaceKey'] = $("#projectReplaceKey").val();
                    }
					
					$url = "showAjax.php";
                    
					var request = $.ajax({
						url : $url,
						type: "POST",
						data: $formData,
						cache: false,
					});

					request.done (function( msg )
					{
                        if (data.node.data.badgecontact_messages)
                        {
                            console.log("Vi ska synka badgeContact_messages");
                            
                            $url = "syncBadge.php";

                            var request2 = $.ajax({
                                url : $url,
                                type: "POST",
                                data: $formData,
                                cache: false,
                            });

                            request2.done (function( msg )
                            {
                                $("#badgeContact_messages").html(msg);
                            });
                        }
                        else if (data.node.data.badgesupport_messages)
                        {
                            console.log("Vi ska synka badgeContact_messages");
                            
                            $url = "syncBadge.php";

                            var request2 = $.ajax({
                                url : $url,
                                type: "POST",
                                data: $formData,
                                cache: false,
                            });

                            request2.done (function( msg )
                            {
                                $("#badgeSupport_messages").html(msg);
                            });
                        }
                        
						$("#"+$target).html(msg);
						
						fancytree_fancyTreeClass();
	
						initTinymce();
                        inlineTinyMce();                        

                        initJeditable();

						initDataTable();
						
						fancytree_fancyTreeSelectClass();
						
						$(".selectpicker").selectpicker();
                        $(".selectpicker2").selectpicker({iconBase: 'fa',
    						tickIcon: 'fa-check'});
                        
                        if ($('#blockSelect').length){
                            validateSelectpicker();
                        }
                        /*if ($.ui.fancytree.getTree("#"+treeId).getNodeByKey(id) !== null)
                        {
                            $.ui.fancytree.getTree("#"+treeId).getNodeByKey(id).setFocus();
                        }*/
					});

					request.fail (function (msg)
					{
						$("#status-field").html(msg);
					});
				  }
			});
		});
	}
	
	function urlExists(url)
	{
		var http = new XMLHttpRequest();
		http.open('HEAD', url, false);
		http.send();
		return http.status!=404;
	}
   
    $(document).on("click", ".sendContatactMail", function (event){
        event.preventDefault();
        
        var $run = true;
        
        var $formData = new Object();
        
        if ($(this).data("form_input"))
        {
            var $inputs = $("form#"+$(this).data("form_input")+' :input');

            $inputs.each(function() {
                if ($(this).val().trim().length > 0)
				{
					$formData[$(this).attr("id")] = $(this).val();
				}
				else if ($(this).is( ":button" ))
				{
					//Do nothing....
				}
				else
				{
					$run = false;
					$(".contact__msg").hide();
				}
            });
        }
        
        console.log($formData);
        
        if ($run)
        {
            var $url = "./mail/sendMail.php";
            
            var request = $.ajax({
				url : $url,
				type: "POST",
				data: $formData,
				cache: false,
			});

			request.done (function( msg )
			{
				$(".contact__msg").show();
			});

			request.fail (function (msg)
			{
				$("#status-field").html(msg);
			});
        }
    });
    
    $(document).on("click", "#resetpassword", function (event){
        event.preventDefault();
        
        var $run = true;
        
        var $formData = new Object();
        
       
        if ($("#email").val().trim().length > 0)
        {
            $formData["email"] = $("#email").val();
        }
        else
        {
            $run = false;
        }
        
        console.log($formData);
        
        if ($run)
        {
            var $url = "./mail/resetPassword.php";
            
            var request = $.ajax({
				url : $url,
				type: "POST",
				data: $formData,
				cache: false,
			});

			request.done (function( msg )
			{
				alert($(msg).filter("#resetMailMessage").text());
			});

			request.fail (function (msg)
			{
				$("#status-field").html(msg);
			});
        }
    });
    
	$(document).on("click", "#submittNewsletter", function(event)
    {
		console.log("Hmmmmmmmmm.....");
		
        event.preventDefault();               
        
        $formData = new Object();
		
		var $inputs = $("form#add2newsletter :input");
		
		var $run = true;

        $inputs.each(function() {
			if($(this).prop('required'))
			{
				$formData[$(this).attr("id")] = $(this).val();
					
					if ($formData[$(this).attr("id")].trim().length == 0)
					{
						$run = false;
					}
			}
			else
			{
				if( $(this).is('input:text') ) 
				{
					if ($(this).val().trim().length > 0)
					{
						$formData[$(this).attr("id")] = $(this).val();
					}
				}
				else if( $(this).is('input:hidden') ) 
				{
					$formData[$(this).attr("id")] = $(this).val();
				}
				else if ($(this).is(':checkbox') )
				{
					if ($(this).is(":checked"))
					{
						$addChildNode = true;
					}
					else
					{
						$addChildNode = false;
					}
				}
				else if ($(this).hasClass("selectpicker2"))
				{
					if ($(this).selectpicker("val") !== "-1")
					{
						if ('note' in $formData && $formData['note'].length !== 0)
						{
							alert("Du kan inte addera egen nod och samtidigt valt ett menyalternativ!!!!");
							$block = true;
						}

						$formData[$(this).attr("id")] = $(this).selectpicker("val");
					}
				}
				else  if ($(this).attr("id") !== undefined)
				{
					$formData[$(this).attr("id")] = $(this).val();
				}
			}
        });
        
		if ($run)
		{
			$url = "addUserNewsletter.php";

			var request = $.ajax({
				url : $url,
				type: "POST",
				data: $formData,
				cache: false,
			});

			request.done (function( msg )
			{

			});

			request.fail (function (msg)
			{
				$("#status-field").html(msg);
			});
        }
		else
		{
			alert($("#error_text_newsletter").val());
		}
        return false;
    });
	
	$(document).on('change', '.syncData', function(event)	
	{
		var $id, $id2, $tableKey, $text, $url;
				
		var $formData = new Object();

        
		if ($(this).is(':checkbox'))
		{
			if ($(this).is(":checked"))
			{
				$formData[$(this).attr("id")] = 1;
			}
			else
			{
				$formData[$(this).attr("id")] = 0;
			}
		}
		else
		{
			$formData[$(this).attr("id")] = $(this).val();
		}
		
		
		if ($(this).data("replace_table"))
		{
			$formData['replaceTable'] = $(this).data("replace_table");
		}
		else
		{
			$formData['replaceTable'] = $("#replaceTable").val();
		}
        
        if ($(this).data("sync_nav"))
		{
			$sync_nav = $(this).data("sync_nav");
		}
		console.log($formData);
        
        $url = "./common/syncData.php";
        console.log($url);
        
        if (!urlExists($url))
        {
            $url = "./common/syncData.php";
        }
        
		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
			async: false
		});

		request.done (function( msg )
		{
			
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
	});	
	
	$(document).on("click", "[id ^= removeReaderNewsletter]", function (event){
		
		event.preventDefault();
		
		var $id, $id2, $tableKey, $text, $url;
				
		var $formData = new Object();

		$formData['tableKey'] = $(this).data("table_key");
		
		var matches = $(this).attr("id").match(/\[(.*?)\]/);

		if (matches) {
			var submatch = matches[1];
		}
		
		$formData['removeKey'] = submatch;
		
		if (!confirm("Vill du verkligen radera din informataion för nyhetsbrev? Notera att vi kan ha mer info om dig andra system, kontakta oss för att eventuellt radera dig i dessa system!"))
		{
			return false;
		}
		
        $url = "./addUserNewsletter.php";
        
		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
			async: false
		});

		request.done (function( msg )
		{
			window.location.replace("https://www.lvteknik.se/");
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
	});
});