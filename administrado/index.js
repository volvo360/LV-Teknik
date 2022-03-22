// JavaScript Document
$( document ).ready(function() {
    /*$(function () {
      $('[data-toggle="tooltip"]').tooltip()
    });*/
    
    var $internalTinyMceLinks = null;
    
    $addUserCooperationId = 1;
    
    $(".datepicker").each(function() {
        $( this).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd'
        });
      } );
    
    //$.fn.selectpicker.Constructor.DEFAULTS.iconBase='fa';
    
    var selData, $targetTreeId;
    
    var $ajaxData = new Object();
    
	fancytree_fancyTreeClass();
    
    fancytree_fancyTreeSelectClass();
	
	initTinymce();
    inlineTinyMce();
	
	initJeditable();
	
	initDataTable();
	
	$(".selectpicker2").selectpicker();
	
	$( document ).ajaxStart(function() {
	  	console.log("Ajax anropat");
        $(".content").loading("start");
	});
    
    $( document ).ajaxSend(function( event, jqxhr, settings ) {
        console.log(settings.url);
    });
	
    function responsive_filemanager_callback(field_id){
            console.log(field_id);
            var url=jQuery('#'+field_id).val();
            alert('update '+field_id+" with "+url);
            //your code
        }
    
	$( document ).ajaxComplete(function( event, xhr, settings ) {
        $.fn.selectpicker.Constructor.DEFAULTS.iconBase='fa';
		$(".selectpicker2").selectpicker();
        $(".selectpicker").selectpicker();
		
        $(".datepicker").each(function() {
            $( this).datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'yy-mm-dd'
            });
          } 
        );
        /*
        function responsive_filemanager_callback(field_id){
            console.log(field_id);
            var url=jQuery('#'+field_id).val();
            alert('update '+field_id+" with "+url);
            //your code
        }*/
        
		initTinymce();
        inlineTinyMce();
		
		initJeditable();
		
		initDataTable();
        
        inlineTinyMce();
        
        initTinymceSm();
        
        fancytree_fancyTreeClass();
		
		console.log("Ajax avslutat");
        
        $(".content").loading("stop");
        
          
	});
    /*
    function responsive_filemanager_callback(field_id){
        console.log(field_id);
        var url=jQuery('#'+field_id).val();
        alert('update '+field_id+" with "+url);
        //your code
    }
    */
	function urlExists(url)
	{
		var http = new XMLHttpRequest();
		http.open('HEAD', url, false);
		http.send();
		return http.status!=404;
	}
    
    function displaySummary()
    {
        var $html = '<p>';
        
        $.each($ajaxData, function (index, value)
        {
            if ($(index))
            {
                if (Array.isArray(value))
                {
                    $html += index +" : "+value.join(", ")+"<br>";
                }
                else
                {
                    $html += index+" : "+value+"<br>"; 
                }
            }
        });
        
        $html += "</p>";
        
        $("#projectSummary").html($html);
    }
    
    function extractData($div = null, $mode = "normal")
    {
        if (!$div || !$mode)
        {
            return false;
        }
        $block = null;
        
        $div = $div.replace("-tab", "");
        
        if ($mode === "normal")
        {
            $("#"+$div +" :input").each(function (event){
                if ($(this).attr("id"))
                {
                    if ($block !== $(this).attr("id"))
                    {
                        if ($(this).is(" :text"))
                        {
                            $ajaxData[$(this).closest(".form-group").find("label").text()] = $(this).val();
                        }
                        else if ($(this).is("textarea"))
                        {
                            $ajaxData[$(this).closest(".form-group").find("label").text()] = $(this).val();
                        }
                        else if ($(this).hasClass("selectpicker2"))
                        {
                            $ajaxData[$(this).closest(".form-group").find("label").text()] = $("#"+$(this).attr("id") +" option:selected").text();
                        }
                    }
                    $block = $(this).attr("id");
                }
            });

            $("#"+$div +" .fancyTreeSelectClass").each(function (event){

                if ($(this).attr("id"))
                {
                    var selNodes = $.ui.fancytree.getTree("#"+$(this).attr("id")).getSelectedNodes();
                    selData = $.map(selNodes, function(n){
                        return n.title;
                    });   

                    $ajaxData[$("#"+$("#"+$(this).attr("id")).closest(".tab-pane").attr("id")+"-tab").text()] = selData;
                }

            });
        }
        else
        {
           $("#"+$div +" :input").each(function (event){
                if ($(this).attr("id"))
                {
                    if ($(this).is(" :text"))
                    {
                        $ajaxData[$(this).attr("id")] = $(this).val();
                    }
                    else if ($(this).is("textarea"))
                    {
                        $ajaxData[$(this).attr("id")] = $(this).val();
                    }
                    else if ($(this).hasClass("selectpicker2"))
                    {
                        $ajaxData[$(this).attr("id")] = $("#"+$(this).attr("id")).selectpicker("val");
                    }
                }
            });

            $("#"+$div +" .fancyTreeSelectClass").each(function (event){

                if ($(this).attr("id"))
                {
                    var selNodes = $.ui.fancytree.getTree("#"+$(this).attr("id")).getSelectedNodes();
                    selData = $.map(selNodes, function(n){
                        return n.key;
                    });   

                    $ajaxData[$(this).attr("id")] = selData;
                }

            });
        }
        
    }
    
    /*function example_image_upload_handler (blobInfo, success, failure, progress) 
    {
        var xhr, formData;

        xhr = new XMLHttpRequest();
        xhr.withCredentials = false;
        xhr.open('POST', 'postAcceptor.php');

        xhr.upload.onprogress = function (e) {
            progress(e.loaded / e.total * 100);
        };

        xhr.onload = function() {
        var json;

        if (xhr.status === 403) {
          failure('HTTP Error: ' + xhr.status, { remove: true });
          return;
        }

        if (xhr.status < 200 || xhr.status >= 300) {
          failure('HTTP Error: ' + xhr.status);
          return;
        }

        json = JSON.parse(xhr.responseText);

        if (!json || typeof json.location != 'string') {
          failure('Invalid JSON: ' + xhr.responseText);
          return;
        }

        success(json.location);
        };

        xhr.onerror = function () {
            failure('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
        };

        formData = new FormData();
        formData.append('file', blobInfo.blob(), blobInfo.filename());

        xhr.send(formData);
    }
*/
    function getNextStepVtab($vtab)
    {
        $tab = $("#"+$vtab).find(".active").next().attr("id");
        
        if ($tab)
        {
            return $tab;
        }
        else
        {
            return false;
        }
        
    }
    
	function initDataTable()
	{
		$(".DataTable").each(function(){
			if ( $.fn.dataTable.isDataTable( "#"+$(this).attr("id") ) ) {
				//table = $(this).DataTable().destroy();
                table = $(this).DataTable();
			}
			else 
            {
				table = $(this).DataTable( {
					aaSorting : [],
				} );
			}
		});
	}
	
	function initJeditable()
	{
        var $url;
            
        $url = "../common/syncData.php";

        if (!urlExists($url))
        {
            $url = "../"+$url;    
        }
        
		$(".jeditable").each(function() {
            
            var $reload_tree;
            
            var $formData = new Object();
            
            $formData['replaceTable'] = $(this).data("replace_table");
            
            if ($(this).data("reload_tree"))
            {
                $formData["reload_tree"] = $(this).data("replace_table");
                $reload_tree = $(this).data("reload_tree");
                $project_key2 = $(this).data("replace_project2");
            }
            
            if ($(this).data("replace_lang"))
            {
                $formData["lang_code"] = $(this).data("replace_lang");
            }
            
            if ($(this).data("replace_project"))
            {
                $formData["replaceProject"] = $(this).data("replace_project");
            }
            
			//var $current_element = $(this).attr("id");
            
			$(this).editable($url,
			{
				//onblur: 'submit',
				placeholder: '',
				cancel : 'Cancel',
				cssclass : 'custom-class',
				cancelcssclass : 'btn btn-danger',
				select : true,
				submitcssclass : 'btn btn-success',
				submit : 'Save',
				submitdata: function(value, settings) {
					return {
						replaceTable : $(this).data("replace_table"),
						replaceLang : $(this).data("replace_lang"),
						jEditable : 1,
					};
				},
                callback : function(event) 
                {
                    if ( typeof $reload_tree !== 'undefined')
                    {
                        $url = "reloadArea.php";
                        
                        if (typeof $project_key2 !== 'undefined')
                        {
                            $formData["project_key2"] = $project_key2;
                        }
                        
                        $formData["reload_tree"] = $(this).data("replace_table");
                
                        
                        if (!urlExists($url))
                        {
                            $url = "../../"+$url;
                        }
                        if (!urlExists($url))
                        {
                            $url = "../"+$url;
                        }
                        if (!urlExists($url))
                        {
                            $url = "../"+$url;
                        }

                        var request2 = $.ajax({
                            url : $url,
                            type: "POST",
                            data: $formData,
                            cache: false,
                            async: false
                        });

                        request2.done (function( msg )
                        {
                            console.log("#"+$reload_tree);
                            
                            if ( typeof $reload_tree !== 'undefined')
                            {
                                var tree = $.ui.fancytree.getTree("#"+$reload_tree);
                                tree.reload(msg);
                            }
                            else
                            {
                                $("#"+$reload_tree).html(msg);
                            }
                        });

                        request2.fail (function (msg)
                        {
                            $("#status-field").html(msg);
                        });
                    }
                }
			});
        });
	}

	function reloadTree($tree, $targetFile = null, $targetTree = null, $extraData = null)
	{
        var $url;
				
		var $formData = new Object();

		$formData['table'] = $tree;
        
        if ($extraData.length > 0)
        {
            $formData['extraData'] = $extraData;
        }
        
        if($targetFile  == null)
        {
            $url = "./common/resyncTree.php";
            
            if (!urlExists($url))
            {
                $url = "../common/resyncTree.php";
            }
            if (!urlExists($url))
            {
                $url = "../../common/resyncTree.php";
            }
            if (!urlExists($url))
            {
                $url = "../../../common/resyncTree.php";
            }
            else
            {
                $url = "../../administrado/common/resyncTree.php";
            }
            
        }
        else
        {
            $url = $targetFile;
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
			console.log("#tree_"+$tree);
            if($targetTree  == null)
            {
			     var tree = $.ui.fancytree.getTree("#tree_"+$tree);
            }
            else
            {
                var tree = $.ui.fancytree.getTree("#"+$targetTree);
            }
            console.log(msg);
  			tree.reload(msg);
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
	}
	
	
		
	function saveTinyMceEdit(inst) 
	{
        console.log("Hmmmm, ska spara editering......");
        
		if (tinyMCE.activeEditor.isDirty())
		{
    		var $id, $id2, $tableKey, $text;
				
			var $formData = new Object();

			$id2 = tinymce.activeEditor.id;

			$id = $id2.replace('[', '\\[');
			$id = $id.replace(']', '\\]');

			//$text = tinymce.activeEditor.getContent({format: 'raw'}); 
            $text = tinymce.activeEditor.getContent({}); 
			
            $formData[$id2] = $text;

            $tableKey = $("#"+$id).data("replace_table");
            
            if ($("#"+$id).data("replace_table"))
			{
                $formData['replaceTable'] = $("#"+$id).data("replace_table");
			}
			else
			{
				$formData['replaceTable'] = $("#replaTable").val();
			}

            $url = "../administrado/common/syncData.php";
			
			if (!urlExists($url))
            {
				$url = "../"+$url;
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
		}
	}
	
    
    function inlineTinyMce()
    {
        //Remove all active instance of TinyMCE, layout bug else
		tinymce.remove(".inlineTinyMce");
		
		tinymce.init({
			selector: '.inlineTinyMce',
            inline: true,
			language: 'sv_SE',	
			plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons responsivefilemanager ',
			imagetools_cors_hosts: ['picsum.photos'],
			menubar: 'file edit view insert format tools table help',
			toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
			toolbar_sticky: true,
			autosave_ask_before_unload: true,
			autosave_interval: '30s',
			autosave_prefix: '{path}{query}-{id}-',
			autosave_restore_when_empty: false,
			autosave_retention: '2m',
			image_advtab: true,
			save_enablewhendirty: true,
			save_onsavecallback: function () 
			{ 
				saveTinyMceEdit();
			},	
            importcss_append: true,

			init_instance_callback: function (editor) {
                editor.on('blur', function (e) {
					saveTinyMceEdit();
			  	});
			},
		  
/**/

		  height: 600,
		  image_caption: true,
		  quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
		  noneditable_noneditable_class: 'mceNonEditable',
		  toolbar_mode: 'sliding',
		  contextmenu: 'link image imagetools table',
		  content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
            image_advtab: true ,
          /*  external_filemanager_path:"/filemanager/",
           filemanager_title:"Responsive Filemanager" ,
           external_plugins: { "filemanager" : "/filemanager/plugin.min.js"}*/
            image_title: true,
          /* enable automatic uploads of images represented by blob or data URIs*/
          automatic_uploads: true,
          /*
            URL of our upload handler (for more details check: https://www.tiny.cloud/docs/configure/file-image-upload/#images_upload_url)
            
            here we add custom filepicker only to Image dialog
          */
            images_upload_url: 'postAcceptor.php',        
          file_picker_types: 'image',
          /* and here's our custom image picker*/
          file_picker_callback: function (cb, value, meta) {
            var input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');

            /*
              Note: In modern browsers input[type="file"] is functional without
              even adding it to the DOM, but that might not be the case in some older
              or quirky browsers like IE, so you might want to add it to the DOM
              just in case, and visually hide it. And do not forget do remove it
              once you do not need it anymore.
            */

            input.onchange = function () {
              var file = this.files[0];

              var reader = new FileReader();
              reader.onload = function () {
                /*
                  Note: Now we need to register the blob in TinyMCEs image blob
                  registry. In the next release this part hopefully won't be
                  necessary, as we are looking to handle it internally.
                */
                var id = 'blobid' + (new Date()).getTime();
                var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
                var base64 = reader.result.split(',')[1];
                var blobInfo = blobCache.create(id, file, base64);
                blobCache.add(blobInfo);

                /* call the callback and populate the Title field with the file name */
                cb(blobInfo.blobUri(), { title: file.name });
              };
              reader.readAsDataURL(file);
            };

            input.click();
          },
		 });
    }

    
    function getInternalLinks()
    {
        //$internalTinyMceLinks = [{title: 'My page 1', value: 'https://www.tiny.cloud'},{title: 'My page 2', value: 'https://about.tiny.cloud'}];
        
        if($internalTinyMceLinks == null)
        { 
            
            //console.log("Ska hämta länkar........");
            
            var $formData = new Object();
            
            $url = "./common/getInternalLinks.js.php";

            if (!urlExists($url))
            {
                $url = "./../../administrado/common/getInternalLinks.js.php";
            }
            
            var request = $.ajax({
                url : $url,
                type: "POST",
                data: $formData,
                 dataType: 'text',
                cache: false,
                async: false
            });

            request.done (function( msg )
            {
                $internalTinyMceLinks = jQuery.parseJSON(msg);
            });

            request.fail (function (msg)
            {
                $("#status-field").html(msg);
            });
        }
        else
        {
            $internalTinyMceLinks;
        }
        
    }
    
    var fetchLinkList = function() {
  return $internalTinyMceLinks;};
    
	function initTinymce()
	{
        getInternalLinks();
		//Remove all active instance of TinyMCE, layout bug else
		tinymce.remove(".tinyMceArea");
		
		tinymce.init({
            placeholder: $(this).placeholder,
			selector: '.tinyMceArea',
			language: 'sv_SE',	
			plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons  responsivefilemanager',
			imagetools_cors_hosts: ['picsum.photos'],
			menubar: 'file edit view insert format tools table help',
			toolbar: 'responsivefilemanager | undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
			toolbar_sticky: true,
			autosave_ask_before_unload: true,
			autosave_interval: '30s',
			autosave_prefix: '{path}{query}-{id}-',
			autosave_restore_when_empty: false,
			autosave_retention: '2m',
			image_advtab: true,
			save_enablewhendirty: true,
			save_onsavecallback: function () 
			{ 
				saveTinyMceEdit();
			},	
		  
		  importcss_append: true,
           
			init_instance_callback: function (editor) {
				editor.on('blur', function (e) {
                    saveTinyMceEdit();
			  	});
			},
		  
		  height: 300,
		  image_caption: true,
		  quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
		  noneditable_noneditable_class: 'mceNonEditable',
		  toolbar_mode: 'sliding',
		  contextmenu: 'link image imagetools table',
		  content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
            image_advtab: true ,
            
             external_filemanager_path:"/filemanager/",
       filemanager_title:"Responsive Filemanager" ,
       external_plugins: { "filemanager" : "/filemanager/plugin.min.js"},
            image_title: true,
          /* enable automatic uploads of images represented by blob or data URIs*/
          automatic_uploads: true,
          /*
            URL of our upload handler (for more details check: https://www.tiny.cloud/docs/configure/file-image-upload/#images_upload_url)
            
            here we add custom filepicker only to Image dialog
          */
            images_upload_url: 'postAcceptor.php',        
          file_picker_types: 'image',
          /* and here's our custom image picker*/
          file_picker_callback: function (cb, value, meta) {
            var input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');

            /*
              Note: In modern browsers input[type="file"] is functional without
              even adding it to the DOM, but that might not be the case in some older
              or quirky browsers like IE, so you might want to add it to the DOM
              just in case, and visually hide it. And do not forget do remove it
              once you do not need it anymore.
            */

            input.onchange = function () {
              var file = this.files[0];

              var reader = new FileReader();
              reader.onload = function () {
                /*
                  Note: Now we need to register the blob in TinyMCEs image blob
                  registry. In the next release this part hopefully won't be
                  necessary, as we are looking to handle it internally.
                */
                var id = 'blobid' + (new Date()).getTime();
                var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
                var base64 = reader.result.split(',')[1];
                var blobInfo = blobCache.create(id, file, base64);
                blobCache.add(blobInfo);

                /* call the callback and populate the Title field with the file name */
                cb(blobInfo.blobUri(), { title: file.name });
              };
              reader.readAsDataURL(file);
            };

            input.click();
          },
           link_list: './common/getInternalLinks.js.php',
            link_list: function(success) { // called on link dialog open
            var links = fetchLinkList(); // get link_list data
            success(links); // pass link_list data to TinyMCE
          },
		 });
	}
    
    function initTinymceSm()
	{
		//Remove all active instance of TinyMCE, layout bug else
		tinymce.remove(".tinyMceAreaSm");
		
		tinymce.init({
            placeholder: $(this).placeholder,
			selector: '.tinyMceAreaSm',
			language: 'sv_SE',	
			plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons responsivefilemanager ',
			imagetools_cors_hosts: ['picsum.photos'],
			menubar: 'file edit view insert format tools table help',
			toolbar: 'responsivefilemanager | undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
			toolbar_sticky: true,
			autosave_ask_before_unload: true,
			autosave_interval: '30s',
			autosave_prefix: '{path}{query}-{id}-',
			autosave_restore_when_empty: false,
			autosave_retention: '2m',
			image_advtab: true,
			save_enablewhendirty: true,
			save_onsavecallback: function () 
			{ 
				saveTinyMceEdit();
			},	
		  
		  importcss_append: true,
           
			init_instance_callback: function (editor) {
				editor.on('blur', function (e) {
                    saveTinyMceEdit();
			  	});
			},
		  
		  height: 300,
		  image_caption: true,
		  quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
		  noneditable_noneditable_class: 'mceNonEditable',
		  toolbar_mode: 'sliding',
		  contextmenu: 'link image imagetools table',
		  content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
            image_advtab: true ,
            
             external_filemanager_path:"/filemanager/",
       filemanager_title:"Responsive Filemanager" ,
       external_plugins: { "filemanager" : "/filemanager/plugin.min.js"}
		 });
	}
	
	function extract_tree($treeid, $replaceTable)
	{
		console.log($treeid);
		
		var tree = $.ui.fancytree.getTree("#"+$treeid);
		var d = tree.toDict(true);

		var $formData = new Object;
		//$formData['tree'] =JSON.stringify(d);
		$formData['tree'] =d;
        if($replaceTable == null)
        {
            $formData['replaceTable'] = $("#"+$treeid).data("replace_table");
        }
        else
        {
            $formData['replaceTable'] = $replaceTable;
        }
		

		// acces node attributes

        $url = "../../administrado/common/syncTreeData.php";
        
        if (!urlExists($url))
		{
            $url = "./common/syncTreeData.php";
        }
        
        
        if (!urlExists($url))
		{
            $url = "../common/syncTreeData.php";
        }
        
        if (!urlExists($url))
		{
			$url = "../../common/syncTreeData.php";
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
	}
	
	function refreshDiv($targetDiv, $replaceTable = null, $targetFile = null)
	{
		var $formData = new Object;

		$select = $(this);
		
		$formData['replaceTable'] = $replaceTable;
		
        if($targetFile == null)
        {
            $url = "../common/refreshDiv.php";
        }
		else
        {
            $url = $targetFile;
        }

		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
		});

		request.done (function( msg )
		{
			$("#"+$targetDiv).html(msg);
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
	}
	
	function fancytree_fancyTreeClass()
	{
        $.each( $( ".fancyTreeClass" ), function() {
            
            if ($.ui.fancytree.getTree("#"+$(this).attr("id")))
            {
                return;
            }
            
            $treeId = $(this).attr("id");
            
			$(this).fancytree({
				extensions: ["dnd5"],
                treeId : $(this).attr("id"),
				dnd5: {
				// autoExpandMS: 400,
				// preventForeignNodes: true,
				// preventNonNodes: true,
				// preventRecursiveMoves: true, // Prevent dropping nodes on own descendants
				// preventVoidMoves: true, // Prevent dropping nodes 'before self', etc.
				// scroll: true,
				// scrollSpeed: 7,
				// scrollSensitivity: 10,

				// --- Drag-support:

				dragStart: function(node, data) {
                    
                    if (node.hasClass("disableDrag"))
                    {
                        return false;
                    }
				  /* This function MUST be defined to enable dragging for the tree.
				   *
				   * Return false to cancel dragging of node.
				   * data.dataTransfer.setData() and .setDragImage() is available
				   * here.
				   */
		//          data.dataTransfer.setDragImage($("<div>hurz</div>").appendTo("body")[0], -10, -10);
				  return true;
				},
				dragDrag: function(node, data) {
				  data.dataTransfer.dropEffect = "move";
				},
				dragEnd: function(node, data) {
					/*console.log(node);
					console.log(data);
					console.log(node.tree);
                    console.log(node.data.replace_table);*/
					extract_tree(node.tree._id, node.data.replace_table);
				},

				// --- Drop-support:

				dragEnter: function(node, data) {
				  // node.debug("dragEnter", data);
				  data.dataTransfer.dropEffect = "move";
				  // data.dataTransfer.effectAllowed = "copy";
				  return true;
				},
				dragOver: function(node, data) {
				  data.dataTransfer.dropEffect = "move";
				  // data.dataTransfer.effectAllowed = "copy";
				},
				dragLeave: function(node, data) {
				},
				dragDrop: function(node, data) {
				  /* This function MUST be defined to enable dropping of items on
				   * the tree.
				   */
				  var transfer = data.dataTransfer;

				  node.debug("drop", data);

				  // alert("Drop on " + node + ":\n"
				  //   + "source:" + JSON.stringify(data.otherNodeData) + "\n"
				  //   + "hitMode:" + data.hitMode
				  //   + ", dropEffect:" + transfer.dropEffect
				  //   + ", effectAllowed:" + transfer.effectAllowed);

				  if( data.otherNode ) {
					// Drop another Fancytree node from same frame
					// (maybe from another tree however)
					var sameTree = (data.otherNode.tree === data.tree);

					data.otherNode.moveTo(node, data.hitMode);
				  } else if( data.otherNodeData ) {
					// Drop Fancytree node from different frame or window, so we only have
					// JSON representation available
					node.addChild(data.otherNodeData, data.hitMode);
				  } else {
					// Drop a non-nodenewRelativeFirstName
					node.addNode({
					  title: transfer.getData("text")
					}, data.hitMode);
				  }
				  node.setExpanded();
					
					//extract_tree($(this), $(this).data("replace_table"))
				}
			  },
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
	
	function fancytree_fancyTreeSelectClass()
	{
		$.each( $( ".fancyTreeSelectClass" ), function() {
			$(this).fancytree({
				
				checkbox: true,
      			selectMode: 2,
				
				select: function (event, data) {
                    if (data.targetType == 'checkbox') 
					{
						var selKeys = $.map(data.tree.getSelectedNodes(), function(node){
						  return node.key;
						});
						
						var $formData = new Object;

                        if ($(this).data("project_key"))
                        {
                            $formData['projectKey'] = $(this).data("project_key");
                        }
                        
                        if ($(this).data("header_key"))
                        {
                            $formData['headerKey'] = $(this).data("header_key");
                        }
                        
                        if ($("#competenceId"))
                        {
                            $formData['competenceId'] = $("#competenceId").val();
                        }
						
                        if (data.node.data['replace_table'] !== undefined)
                        {
                           $formData['replaceTable'] = data.node.data['replace_table']; 
                        }
                        else if ($(this).data("replace_table2"))
                        {
                            $formData['replaceTable'] = $(this).data("replace_table2");
                        }
                        else
                        {
                            $formData['replaceTable'] = $(this).data("replace_table");
                        }
                        
                        if ($("#projectReplaceKey").val().length > 0)
                        {
                            $formData['projectReplaceKey'] = $("#projectReplaceKey").val();
                        }
						
						$formData['selectedKeys'] = selKeys.toString();
						
                        console.log($formData);
                        
						$url = "../../administrado/common/syncData.php";
                        
                        console.log($url);
                        
                        if (!urlExists($url))
                        {
                            $url = "../../common/syncData.php";
                        }
                        if (!urlExists($url))
                        {
                            $url = "../common/syncData.php";
                        }
						
                        if (urlExists($url))
                        {
                            var request = $.ajax({
                                url : $url,
                                type: "POST",
                                data: $formData,
                                cache: false,
                            });

                            request.done (function( msg )
                            {
                                //Do nothing
                            });

                            request.fail (function (msg)
                            {
                                $("#status-field").html(msg);
                            });
                        }
					}
			  },

				activate: function(event, data) {

					var node = data.node;
					var id = data.node.key;
					var $target = $(this).data("ajax_target");

					var $formData = new Object;

					$formData['id'] = id;
					$formData['replaceTable'] = $(this).data("replace_table");
					// acces node attributes

					$url = "showAjax.php";

					var request = $.ajax({
						url : $url,
						type: "POST",
						data: $formData,
						cache: false,
					});

					request.done (function( msg )
					{
						$("#"+$target).html(msg);
						
						fancytree_fancyTreeClass();
	
						initTinymce();
                        inlineTinyMce();

						initJeditable();

						initDataTable();
						
						fancytree_fancyTreeSelectClass();
						
						$(".selectpicker").selectpicker();
					});

					request.fail (function (msg)
					{
						$("#status-field").html(msg);
					});
				  }
			});
		});
	}
	
    //https://stackoverflow.com/a/28191966 2021-02-17
    
    function getKeyByValue(object, value) {
      return Object.keys(object).find(key => object[key] === value);
    }
    
    //Part of function https://stackoverflow.com/a/7340091 2021-02-17
    
    function findMasterKey(object, value)
    {
        $findKey = false;
        
        for(var key in object) {
            if (object.hasOwnProperty(key)) 
            {
                $t = object[key];
                
                for(var key2 in $t) 
                {
                    if ($t.hasOwnProperty(key2)) {
                        if (key2 === value)
                        {
                            $findKey = key;
                            break;
                        }
                    }
                }
                
                if ($findKey !== false)
                {
                    break;
                }
            }
        }
        
        return $findKey;
    }
    
    function validateSelectpicker()
    {
        var $json;
        
        var $block = new Object();
        
        $json = $("#blockSelect").text();
        
        if($json == '')
        {
            return false;   
        }
        
        $block = jQuery.parseJSON($json);
        
        $find = $("#ajaxDefaultCollection .selectpicker2").selectpicker("val")[0];
        
        $selectOrg = $select = $("#ajaxDefaultCollection .selectpicker2").first().attr("id");
        
        $masterKey = findMasterKey($block, $find);
        
        console.log("Masterkey = "+$masterKey);
        if ( typeof $select !== 'undefined')
        {
            $select = $select.replace("[","\\[").replace("]","\\]");
        
            $("#"+$select+" > option").each(function() 
            {
                if (!$masterKey)
                {
                    $(this).prop('disabled', false);
                }
                else if (findMasterKey($block, this.value) !== $masterKey)
                {
                    $(this).prop('disabled', true);
                }
                else
                {
                    $(this).prop('disabled', false);
                }
            });
        }
        
        $("#"+$select).selectpicker("refresh");
    }
    
	
	
	$(document).on('change', '.syncPassword', function(event)	
	{
		var $formData = new Object();
		
		console.log($("[id^='password']").val());
		
		if (($("[id^='password']").val().length == 0) || $("[id^='repPassword']").val().length == 0)
		{
			return false;
		}
		
		if ($("[id^='password']").val().length < 6)
		{
			alert ("För kort lösenord");
			return false;
		}
		
		if ($("[id^='password']").val() !== $("[id^='repPassword']").val())
		{
			alert ("De angivna lösenorden är inte samma!");
			return false;
		}
		
		$formData[$("[id ^= 'password']").attr("id")] = $("[id^='password']").val();
		$formData[$("[id ^= 'repPassword']").attr("id")] = $("[id^='repPassword']").val();
		
		$formData['replaceTable'] = $("[id ^= 'password']").data("replace_table");
        
		$url = "../../administrado/common/syncData.php";
        console.log($url);
        
        if (!urlExists($url))
        {
            $url = "./common/syncData.php";
        }
        if (!urlExists($url))
		{
			$url = "../"+$url;
		}
		if (!urlExists($url))
		{
			$url = "../../"+$url;
		}
        if (!urlExists($url))
		{
			$url = "../"+$url;
		}
        if (!urlExists($url))
		{
			$url = "../"+$url;
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
			alert ("Lösenordet är ändrat!");
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
		
	});
	
	$(document).on('change', '.syncData', function(event)	
	{
		var $id, $id2, $tableKey, $text, $url;
				
		var $formData = new Object();

        console.log("Vi ska synka data....");
        
        if ($(this).data("extra_key"))
        {
            console.log("Hmmmm, vi ska hämta extra data....");
            
            $extraData = $(this).data("extra_key");
        }
        
        
        if ($(this).data("replace_id"))
        {
            $id2 = $(this).data("replace_id");
        }
        else
        {
            $id2 = $(this).attr("id");
        }
        
        if ($(this).data("reload_tree"))
        {
            console.log("Vi ska synka träd.....");
            $reload_tree = $(this).data("reload_tree");
            console.log("Vi ska synka träd....."+$reload_tree);
        }
           
        if ($(this).data("replace_lang"))
        {
            $formData["lang_code"] = $(this).data("replace_lang");
        }

        if ($(this).data("replace_project"))
        {
            $formData["replaceProject"] = $(this).data("replace_project");
        }
		
		if ($(this).is(':checkbox'))
		{
			if ($(this).is(":checked"))
			{
				$formData[$id2] = 1;
			}
			else
			{
				$formData[$id2] = 0;
			}
		}
		else
		{
			$formData[$id2] = $(this).val();
		}
		
		if ($(this).attr("id").startswith("date"))
		{
			console.log("Hmmmmm.....");
			var matches = $(this).attr("id").match(/\[(.*?)\]/);
			if (matches) {
				var submatch = matches[1];
				console.log($("#time\\["+submatch+"\\]").val());
				if ($("#time\\["+submatch+"\\]").length > 0)
				{
					$formData['date['+submatch+']'] = $("#date\\["+submatch+"\\]").val()+" "+$("#time\\["+submatch+"\\]").val();
					console.log($formData);
				}
			}
		}
		else if ($(this).attr("id").startswith("time"))
		{
			var matches = $(this).attr("id").match(/\[(.*?)\]/);

			if (matches) {
				var submatch = matches[1];
				
				if ($("#date\\["+submatch+"\\]").length > 0)
				{
					$formData['date['+submatch+']'] = $("#date\\["+submatch+"\\]").val()+" "+$("#time\\["+submatch+"\\]").val();
				}
				delete $formData['time['+submatch+']'];
			}
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
        
        $url = "../../administrado/common/syncData.php";
        console.log($url);
        
        if (!urlExists($url))
        {
            $url = "./common/syncData.php";
        }
        if (!urlExists($url))
		{
			$url = "../"+$url;
		}
		if (!urlExists($url))
		{
			$url = "../../"+$url;
		}
        if (!urlExists($url))
		{
			$url = "../"+$url;
		}
        if (!urlExists($url))
		{
			$url = "../"+$url;
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
			if ($(msg).filter("#resynctree").length > 0)
			{
                //console.log("Vi ska ladda om träd "+$(msg).filter("#resynctree").val())
                
                
                
				reloadTree($(msg).filter("#resynctree").val(), null, $extraData)
			}
            
            else if ( typeof $reload_tree !== 'undefined')
            {
                if (typeof $extraData !== 'undefined' && $extraData !== null) 
                {
                    $formData["extraData"] = $extraData;
                }
                $formData["reload_tree"] = $reload_tree;
                $url = "./common/resyncTree.php";
                if (!urlExists($url))
                {
                    $url = "../"+$url;
                }
                
                console.log($url);
                if (!urlExists($url))
                {
                    $url = "reloadArea.php";
                }
                if (!urlExists($url))
                {
                    $url = "../../"+$url;
                }
                if (!urlExists($url))
                {
                    $url = "../"+$url;
                }
                if (!urlExists($url))
                {
                    $url = "../"+$url;
                }

                var request2 = $.ajax({
                    url : $url,
                    type: "POST",
                    data: $formData,
                    cache: false,
                    async: false
                });

                request2.done (function( msg )
                {
                    var tree = $.ui.fancytree.getTree("#"+$reload_tree);
                    tree.reload(msg);
                    
                    if ( typeof $sync_nav !== 'undefined')
                    {
                        $formData["sync_nav"] = $sync_nav;
                        
                        var request3 = $.ajax({
                            url : $url,
                            type: "POST",
                            data: $formData,
                            cache: false,
                            async: false
                        });

                        request3.done (function( msg )
                        {
                            $("#"+$sync_nav).html(msg);
                        });
                        
                        request3.fail (function (msg)
                        {
                            $("#status-field").html(msg);
                        });
                    }
                });
                
                request2.fail (function (msg)
                {
                    $("#status-field").html(msg);
                });
            }
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
	});	
	
	$(document).on('click', ".editUserProfile", function(event){
		event.preventDefault();
		
		var $formData = new Object;

		$formData['tableKey'] = $(this).data("tablekey");
		$formData['replaceTable'] = $(this).data("replace_table");
		// acces node attributes

		$url = "../../common/editProfile.php";

		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
		});

		request.done (function( msg )
		{
			$("#modalXlLabel").html($(msg).filter("#ajaxHeaderModal"));
			$("#modalXlbody").html($(msg).filter("#ajaxBodyModal"));
			$("#modalXlfooter").html($(msg).filter("#ajaxFooterModal"));
            $(".selectpicker2").selectpicker();
			$("#modalXl").modal("show");
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
	});
	
	$(document).on('changed.bs.select', ".selectpicker2", function (event){
        
        if ($(this).attr("id").startswith("newMasterMenuId"))
        {
            return false;
        }
		
		if ($(this).attr("id").startswith("group2"))
        {
            return false;
        }
        
		var $formData = new Object;

        $formData['replaceTable'] = $(this).data("replace_table");
        $formData[$(this).attr("id")] = $(this).selectpicker('val');
        
		$selectdata = $select = $(this);
        
        $url = "../common/syncData.php";
        
        console.log($select);

		if (!urlExists($url))
		{
			$url = "../"+$url;
		}
        if (!urlExists($url))
		{
			$url = "../"+$url;
		}
        if (!urlExists($url))
		{
			$url = "../"+$url;
		}
        if (!urlExists($url))
		{
			$url = "../"+$url;
		}

        var request = $.ajax({
            url : $url,
            type: "POST",
            data: $formData,
            cache: false,
        });

        request.done (function( msg )
        {
            $("#translationTable").html(msg);
            initJeditable();
            validateSelectpicker();
        });

        request.fail (function (msg)
        {
            $("#status-field").html(msg);
        });
        
        var $formData = new Object;
        
        if ($select.data("reload_tree"))
        {
            $formData["reload_tree"] = $select.data("replace_table");
            $reload_tree = $select.data("reload_tree");
        }
           
        if ($select.data("replace_lang"))
        {
            $formData["lang_code"] = $select.data("replace_lang");
        }

        if ($select.data("replace_project"))
        {
            $formData["replaceProject"] = $select.data("replace_project");
        }
        
        if ($select.data("target_div"))
        {
            var $targetDiv = $select.data("target_div");
            console.log("Vi ska synka div.....");
        }
		
		$formData[$select.attr("id")] = $select.selectpicker("val");
        if ($select.selectpicker("val").length == 0)
        {
           $formData[$select.attr("id")] = ''; 
        }
		$formData['replaceTable'] = $selectdata.data("replace_table");
		// acces node attributes

		
        
        if ($("#"+$select.attr("id")).hasClass("getTranslationLang"))
        {
            $.each( $( ".fancyTreeClass" ), function() {
                //console.log($select.attr("id"));
                
                if ($.ui.fancytree.getTree("#"+$(this).attr("id")).getActiveNode() !== null)
                {
                    $node = $.ui.fancytree.getTree("#"+$(this).attr("id")).getActiveNode().key;
                    console.log($node);
                    $.ui.fancytree.getTree("#"+$(this).attr("id")).activateKey(null);
                    $.ui.fancytree.getTree("#"+$(this).attr("id")).activateKey($node);
                }
            });
            
            $url = "showAjax.php";
            
            $formData['primary'] = $("#primaryLang").selectpicker("val");
            $formData['secondary'] = $("#secondaryLang").selectpicker("val");
            $formData['replaceTable'] = $("#translationTable").data("replace_table");;
            
            console.log("Vi ska synka divar med rätt lang!!!!");
            
            var request = $.ajax({
                url : $url,
                type: "POST",
                data: $formData,
                cache: false,
            });

            request.done (function( msg )
            {
                $("#translationTable").html(msg);
                initJeditable();
                validateSelectpicker();
            });
            
            request.fail (function (msg)
            {
                $("#status-field").html(msg);
            });
            return false;
        }
        
		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
		});

		request.done (function( msg )
		{
            
            if ( typeof $targetDiv !== 'undefined')
            {
                console.log("Kommer vi hit, kan vi ladda om div???")
                
                var matches = $select.attr("id").match(/\[(.*?)\]/);

                if (matches) {
                    var submatch = matches[1];
                }
                
                if (typeof submatch !== 'undefined')
                {
                    $formData['id'] = submatch;
                }
                
                
                $url = "showAjax.php";
                
                var request2 = $.ajax({
                    url : $url,
                    type: "POST",
                    data: $formData,
                    cache: false,
                });
                
                request2.done (function( msg2 )
                {
                    $("#"+$targetDiv).html(msg2);
                });
                
                request2.fail (function (msg)
                {
                    $("#status-field").html(msg);
                });    
                            
            }
            
			if ($select.data("update_div"))
			{
            	refreshDiv($select.data("update_div"), $select.data("replace_table"), $select.data("target_ajax_div"));
            }
            else if ($select.data("update_tree"))
			{
                console.log("Vi ska ladda om träd "+$select.data("update_tree")+" "+$select.data("target_ajax_div"));
				reloadTree($select.data("update_tree"), $select.data("target_ajax_div"), $extraData)
			}
            else if ($select.data("reload_div"))
            {
                console.log("1198 Vi ska ladda om en div....");
                $url = "reloadArea.php";
                
                var request2 = $.ajax({
                    url : $url,
                    type: "POST",
                    data: $formData,
                    cache: false,
                });
                
                request2.done (function( msg2 )
                {
                    $id = $select.data("reload_div").replace('[', '\\[');
				    $id = $id.replace(']', '\\]');
                    
                    $textarea = $select.data("reload_div").replace('headerBodyArea', 'text');
                    
                    if ($("#"+$textarea).length > 0)
                    {
                        tinymce.get($select.data("reload_div")).remove();
                    }
                    
                    $("#"+$id).html(msg2);
                    
                    fancytree_fancyTreeClass();
    
                    fancytree_fancyTreeSelectClass();

                    initTinymce();
                    inlineTinyMce();

                    initJeditable();

                    initDataTable();

                    $(".selectpicker2").selectpicker();
                });
                
                request2.fail (function (msg)
                {
                    $("#status-field").html(msg);
                });    
                
            }
            
            if ($(msg).filter("#resynctree").length > 0)
			{
                console.log("Vi ska ladda om träd "+$(msg).filter("#resynctree").val())
				reloadTree($(msg).filter("#resynctree").val(), null, $extraData)
			}
            validateSelectpicker();
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
        
        if ( typeof $reload_tree !== 'undefined')
        {
            $url = "./common/resyncTree.php";

            /*if (!urlExists($url))
            {
                $url = "./common/"+$url;
            }*/
            if (!urlExists($url))
            {
                $url = "../"+$url;
            }
            if (!urlExists($url))
            {
                $url = "../"+$url;
            }
            if (!urlExists($url))
            {
                $url = "../"+$url;
            }

            var request2 = $.ajax({
                url : $url,
                type: "POST",
                data: $formData,
                cache: false,
                async: false
            });

            request2.done (function( msg )
            {
                if ($(msg).length == 0)
                {
                    return false;
                }
                
                var tree = $.ui.fancytree.getTree("#"+$reload_tree);
                tree.reload(msg);

                if ( typeof $sync_nav !== 'undefined')
                {
                    $formData["sync_nav"] = $sync_nav;

                    var request3 = $.ajax({
                        url : $url,
                        type: "POST",
                        data: $formData,
                        cache: false,
                        async: false
                    });

                    request3.done (function( msg )
                    {
                        $("#"+$sync_nav).html(msg);
                    });

                    request3.fail (function (msg)
                    {
                        $("#status-field").html(msg);
                    });
                }
            });

            request2.fail (function (msg)
            {
                $("#status-field").html(msg);
            });
        }
	});
	
	$(document).on("click", ".addToTree", function (event){
        var $formData = new Object();
        
		$target_tree = $(this).data("target_tree");
		$replaceTable = $(this).data("replace_table");
		$form_id = $(this.form).attr("id");
		
		console.log($form_id);
        $this = $(this);
        
        if ($("#projectReplaceKey").length > 0)
        {
            $formData['projectReplaceKey'] = $("#projectReplaceKey").val();
        }
		
        if ($(this).data("replace_lang"))
        {
            $formData['replace_lang'] = $(this).data("replace_lang");
        }
        
        if ($(this).data("nav_tab"))
        {    
            $navTabId = $(this).data("nav_tab_id"); 
            
            $syncNavTab = $(this).data("nav_tab");
        }
        
		$block = false;
		
        $url = "../../administrado/common/addDataTree.php";
        
        if (!urlExists($url))
		{
            $url = "./common/"+$url;
        }
        
		if (!urlExists($url))
		{
			$url = "../"+$url;
		}
        
        if (!urlExists($url))
		{
			$url = "../"+$url;
		}
        
        if (!urlExists($url))
		{
			$url = "../"+$url;
		}
		

        var $inputs = $("form#"+$form_id+' :input');

        $inputs.each(function() {
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
        });
        
        console.log($formData);
        
        if ($("#"+$target_tree).hasClass("treeSpecial"))
        {
            var tree = $.ui.fancytree.getTree("#"+$target_tree);
            node = tree.getActiveNode();
            
            if (node.extraClasses == "disableDrag")
            {
                $block = true;
                
                alert("Du kan inte addera en ny post under denna \"nod\"!!!!");
            }
        }
		
		if (!$block)
		{
			if(typeof $addChildNode === 'undefined')
			{
				$addChildNode = false;
			}

			$formData['replaceTable'] = $replaceTable;

			if ('note' in $formData)
			{
				$text = $formData['note'];
                
                console.log("2048 "+$text)
                
				if ($text.trim().length == 0)
				{
                    console.log("2052 What the fuck happend!!!!")
					return false;
				}
			}
            else if ('company' in $formData)
			{
				$text = $formData['company'];
				if ($text.trim().length == 0)
				{
					return false;
				}
			}
            else if ('date' in $formData)
			{
				$text = $formData['date'];
				if ($text.trim().length == 0)
				{
					return false;
				}
			}
			else if ('setting' in $formData)
			{
				$text = $formData['setting'];
				if ($text.trim().length == 0)
				{
					return false;
				}
			}
            else if ('folder' in $formData)
			{
				$text = $formData['folder'];
				if ($text.trim().length == 0)
				{
					return false;
				}
			}
			else if ('header' in $formData)
			{
				$text = $formData['header'];
				if ($text.trim().length == 0)
				{
					return false;
				}
			}
            else if ('dbTable' in $formData)
			{
				$text = $formData['dbTable'];
				if ($text.trim().length == 0)
				{
					return false;
				}
			}
            else if ('status_name' in $formData)
			{
				$text = $formData['status_name'];
				if ($text.trim().length == 0)
				{
					return false;
				}
			}
            else if ('title' in $formData)
			{
				$text = $formData['title'];
				if ($text.trim().length == 0)
				{
					return false;
				}
			}
			
			var request = $.ajax({
				url : $url,
				type: "POST",
				data: $formData,
				cache: false,
			});

			request.done (function( msg )
			{
				$tableKey = $(msg).filter("#replaceKey").text();
                
                console.log($tableKey);

				var tree = $.ui.fancytree.getTree("#"+$target_tree);
				node = tree.getActiveNode();
                
                if($(msg).filter('#blockInsertTree').length)
                {
                    $.ui.fancytree.getTree("#"+$target_tree).activateKey($tableKey);
                    return false;
                }
				
				/*if ($.ui.fancytree.getTree("#"+$target_tree).activateKey($tableKey))
				{
					return false;
				}*/

				//If we are to add a subnode in the tree

				if (typeof node != 'undefined' && node != null && $addChildNode )
				{
					node.addChildren({
						folder: false,
						title: $text, 
						key : $tableKey
					});

					$formData['key'] = node.key;
				}
				else if (typeof node != 'undefined' && node != null && !$addChildNode )
				{
                    var $formData = new Object();
                    
                    if ($("#masterMenuId").length > 0)
                    {
                        $text = $( "#masterMenuId :selected" ).text();
                    }
                    
					node.appendSibling({
						folder: false,
						insertBefore : true,
						title: $text, 
						key : $tableKey
					});

					$formData['key'] = node.key;
				}
				else
				{
                    console.log($tableKey);
					var rootNode = $.ui.fancytree.getTree("#"+$target_tree).getRootNode();
					var childNode = rootNode.addChildren({
						title: $text,
						folder: false,
						key : $tableKey
					});
				}

                console.log($tableKey);
                
				extract_tree($target_tree, $replaceTable);
                
                if($tableKey !== null)
                {
                    $.ui.fancytree.getTree("#"+$target_tree).activateKey($tableKey);
                }
				
				$inputs.each(function() {
					if( $(this).is('input:text') ) 
					{
						$(this).val('');
					}

				});
                
                if ($this.data("update_div"))
                {
                    var $formData = new Object();
                    
                    $targetDiv = $this.data("update_div");
                    $formData['replaceTable'] = $formData['replace_table'] = $replaceTable = $this.data("replace_table");
                    $formData['replace_lang'] = $replaceLang = $this.data("replace_lang");
                    $formData['projectReplaceKey'] = $this.data("project_replace_key");
                    $url = "showAjax.php";
                    var request2 = $.ajax({
                        url : $url,
                        type: "POST",
                        data: $formData,
                        cache: false,
                    });

                    request2.done (function( msg )
                    {
                        $("#"+$targetDiv).html(msg);
                        
                        fancytree_fancyTreeClass();

                        fancytree_fancyTreeSelectClass();

                        initTinymce();
                        inlineTinyMce();

                        initJeditable();

                        initDataTable();
                        
                        if ( typeof $syncNavTab !== 'undefined' ) 
                        {
                            $("#"+$syncNavTab+$tableKey+"-tab").click();
                        }
                    });
                    
                    request2.fail (function (msg)
                    {
                        $("#status-field").html(msg);
                    });
                }
			});

			request.fail (function (msg)
			{
				$("#status-field").html(msg);
			});
		}
    });
	
	$(document).on("click", ".addTranslation", function(event){
		event.preventDefault();
		
		var $formData = new Object();
		
		$form_id = $(this).data("target_form");
		$replaceTable = $(this).data("replace_table");
		$replaceVar = $(this).data("replace_var");
		$reloadTable = $(this).data("reload_table");
		
		var $inputs = $("form#"+$form_id+' :input');

		$block = false;
		
        $inputs.each(function() {
			if( $(this).is('input:text') ) 
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
			else if ($(this).is('textarea'))
			{
				$id = $(this).attr("id");
				$id = $id.replace('[', '\\[');
				$id = $id.replace(']', '\\]');
				
				if ($(this).val().trim().length > 0)
				{
					$formData[$(this).attr("id")] = $(this).val();
					$("#warning_"+$id).hide();
				}
				else
				{
					$block = true;
					$("#warning_"+$id).show();
				}
			}
        });
		
		console.log($formData)
		
		if (!$block)
		{
			$formData['replaceVar'] = $replaceVar;
			$formData['replaceTable']= $replaceTable;
			$url = "./addTranslation.php";

			var request = $.ajax({
				url : $url,
				type: "POST",
				data: $formData,
				cache: false,
				async: false
			});

			request.done (function( msg )
			{	
				$inputs.each(function() {
					$(this).val('');
				});
				
				$id = $(msg).filter("#tableKey").text();
				
				$formData['id'] = $id;
				
				$url = "ajaxResyncTable.php";
				
				var request = $.ajax({
					url : $url,
					type: "POST",
					data: $formData,
					cache: false,
					async: false
				});

				request.done (function( msg )
				{	
					$("#table_translation_length").html(msg);
                    fancytree_fancyTreeClass();

                    fancytree_fancyTreeSelectClass();

                    initTinymce();
                    inlineTinyMce();

                    initJeditable();

                    initDataTable();
				});

				request.fail (function (msg)
				{
					$("#status-field").html(msg);
				});
				/*console.log("#tree_"+$tree);
				var tree = $.ui.fancytree.getTree("#tree_"+$tree);
				tree.reload(msg);*/
			});

			request.fail (function (msg)
			{
				$("#status-field").html(msg);
			});
		}
		
	});
	
	$(document).on("click", "#addNewProject", function (event){
        
        if ($("#modalXlbody").is(':empty'))
        {
            var $formData = new Object();

            $url = "addNewProject.php";

            var request = $.ajax({
                url : $url,
                type: "POST",
                data: $formData,
                cache: false,
                async: false
            });

            request.done (function( msg )
            {	
                $("#modalXlLabel").html($(msg).filter("#ajaxHeaderModal"));
                $("#modalXlbody").html($(msg).filter("#ajaxBodyModal"));
                $("#modalXlfooter").html($(msg).filter("#ajaxFooterModal"));
                
                
                $step = getNextStepVtab($('#ajaxBodyModal .nav-pills').attr("id"))
                
                $("#modalXl").modal("show");

                $(".selectpicker2").selectpicker();

                fancytree_fancyTreeClass();

                fancytree_fancyTreeSelectClass();

                initTinymce();
                inlineTinyMce();

                initJeditable();

                initDataTable();
            });

            request.fail (function (msg)
            {
                $("#status-field").html(msg);
            });
        }
        else
        {
            $("#modalXl").modal("show");
        }
	});
    
    $(document).on("show.bs.tab", 'a[data-toggle="pill"]', function (event){
        
        if (event.target.id === "v-pills-header-tab")
        {
            if ($("#v-pills-header").is(':empty'))
            {
                var selNodes = $.ui.fancytree.getTree("#"+$('#ajaxBodyModal .fancyTreeSelectClass').attr("id")).getSelectedNodes();
                
                var selData = $.map(selNodes, function(n){
                    return n.key;
                 });
                
                var $formData = new Object();
		
                $formData['selected'] = selData;
                $formData['replaceTable'] = $('#ajaxBodyModal .fancyTreeSelectClass').data("replace_table");
                
                $url = "getHeaders.php";

                var request = $.ajax({
                    url : $url,
                    type: "POST",
                    data: $formData,
                    cache: false,
                    async: false
                });

                request.done (function( msg )
                {	
                    $("#v-pills-header").replaceWith(msg);

                    $(".selectpicker2").selectpicker();

                    fancytree_fancyTreeClass();

                    $.each( $( "#v-pills-header .fancyTreeSelectClass" ), function() {
                        $(this).fancytree({

                            checkbox: true,
                            selectMode: 2,

                            select: function (event, data) {
                                if (data.targetType == 'checkbox') 
                                {
                                    var selKeys = $.map(data.tree.getSelectedNodes(), function(node){
                                      return node.key;
                                    });
                                    console.log(selKeys);

                                    var $formData = new Object;

                                    $formData['competenceId'] = $("#competenceId").val();
                                    $formData['replaceTable'] = $(this).data("replace_table");

                                    $formData['selectedKeys'] = selKeys;

                                    $url = "syncCompetenceHeaders.php";

                                    var request = $.ajax({
                                        url : $url,
                                        type: "POST",
                                        data: $formData,
                                        cache: false,
                                    });

                                    request.done (function( msg )
                                    {
                                        //Do nothing
                                    });

                                    request.fail (function (msg)
                                    {
                                        $("#status-field").html(msg);
                                    });
                                }
                          },

                            activate: function(event, data) {

                                var node = data.node;
                                var id = data.node.key;
                                var $target = $(this).data("ajax_target");

                                var $formData = new Object;

                                $formData['id'] = id;
                                $formData['replaceTable'] = $(this).data("replace_table");
                                // acces node attributes

                                $url = "showAjax.php";

                                var request = $.ajax({
                                    url : $url,
                                    type: "POST",
                                    data: $formData,
                                    cache: false,
                                });

                                request.done (function( msg )
                                {
                                    $("#"+$target).html(msg);

                                    fancytree_fancyTreeClass();

                                    initTinymce();
                                    inlineTinyMce();

                                    initJeditable();

                                    initDataTable();

                                    fancytree_fancyTreeSelectClass();

                                    $(".selectpicker").selectpicker();
                                });

                                request.fail (function (msg)
                                {
                                    $("#status-field").html(msg);
                                });
                              }
                        });
                    });
                    
                    initTinymce();
                    inlineTinyMce();

                    initJeditable();

                    initDataTable();
                });

                request.fail (function (msg)
                {
                    $("#status-field").html(msg);
                });        
            }
        }
    });
    
    
    $(document).on("shown.bs.tab", 'a[data-toggle="pill"]', function (event)
    {
        if (event.target.id === "v-pills-finish-tab")
        {
            $ajaxData = new Object();
            $("#v-pills-tab .nav-link").each(function()
            {
                if ($(this).attr("id") !== "v-pills-finish-tab")
                {
                   extractData($(this).attr("id"), "normal");
                }
            });
            
            displaySummary();
        }
    });
    
    $(document).on("shown.bs.tab", 'a[data-toggle="pill"]', function (event){
        
       $step = getNextStepVtab($('#ajaxBodyModal .nav-pills').attr("id"));
        if ($step === false)
        {
            $("#nextStep").hide();
            $("#finishAddProject").show();
        }
        else
        {
            $("#nextStep").show();
            $("#finishAddProject").hide();
        }
    });
    
    $(document).on("show.bs.tab", 'a[data-toggle="tab"]', function (event)
    {
        $targetDiv = $(this).attr('href');
        $ajaxData = new Object();
            
        $ajaxData['replaceTable'] = $(this).data("replace_table");
        $ajaxData['id'] = $(this).attr("id");

        $ajaxData['projectReplaceKey'] = $("#projectReplaceKey").val();

        if ($(this).data("target_file"))
        {
            $insert = true;
            $url = $(this).data("target_file");
            $ajaxData['id'] = $(this).data("target_project");
        }
        else
        {
            $insert = false;
            $url = "changeSessionFolder.php";
        }
        
        var request = $.ajax({
            url : $url,
            type: "POST",
            data: $ajaxData,
            cache: false,
            async : true
        });

        request.done (function( msg )
        {
            console.log("Hmmmmmmmm");
            
            if ($insert)
            {
                console.log("Bingo.....");
                
                $($targetDiv).html(msg);
                /*$("#modalXlbody").html('');
                $("#modalXl").modal("hide");*/

                fancytree_fancyTreeClass();

                initTinymce();
                inlineTinyMce();

                initJeditable();

                initDataTable();

                fancytree_fancyTreeSelectClass();

                $(".selectpicker").selectpicker();
                $(".selectpicker2").selectpicker();
            }
        });

        request.fail (function (msg)
        {
            $("#status-field").html(msg);
        });

        if ($($(this).attr('href')).text().length == 0)
        {
            $ajaxData = new Object();
            
            $ajaxData['replaceTable'] = $(this).data("replace_table");
            $ajaxData['id'] = $(this).attr("id");
            
            $ajaxData['projectReplaceKey'] = $("#projectReplaceKey").val();
                               
            $url = "showAjax_sub.php";

            var request = $.ajax({
                url : $url,
                type: "POST",
                data: $ajaxData,
                cache: false,
            });

            request.done (function( msg )
            {
                $($targetDiv).html(msg);
                /*$("#modalXlbody").html('');
                $("#modalXl").modal("hide");*/
                
                fancytree_fancyTreeClass();
	
                initTinymce();
                inlineTinyMce();

                initJeditable();

                initDataTable();

                fancytree_fancyTreeSelectClass();

                $(".selectpicker").selectpicker();
                $(".selectpicker2").selectpicker();

            });

            request.fail (function (msg)
            {
                $("#status-field").html(msg);
            });
        }
        
        $id = $(this).attr("id");
        console.log($(this).parentsUntil("ul").parent().attr("id"));
        if ($(this).parentsUntil("ul").parent().hasClass("nav-index"))
        {
            console.log($id);
            $url = "syncReadNote.php";

            var $formData = new Object();
            
            $formData[$id] = $id;
            
            var request = $.ajax({
                url : $url,
                type: "POST",
                data: $ajaxData,
                cache: false,
                async : true
            });

            request.done (function( msg )
            {
                if ($id == "dashboard-tab")
                {
                    $("#badgeGlobalMessages").remove();
                }
            });
               
            request.fail (function (msg)
            {
                $("#status-field").html(msg);
            });
            
            //return false;
        }
    });
    
    $(document).on("click", "#nextStep", function(){
        $step = getNextStepVtab($('#ajaxBodyModal .nav-pills').attr("id"));
        $("#"+$step).tab("show");
    });
    
    $(document).on("click", "#finishAddProject", function(){
        $ajaxData = new Object();
        
        $("#v-pills-tab .nav-link").each(function()
        {
            if ($(this).attr("id") !== "v-pills-finish-tab")
            {
                extractData($(this).attr("id"), "post");
            }
        });
        
        $ajaxData['replaceTable'] = $(this).data("replace_table");
                               
        $url = "insertNewProject.php";

        var request = $.ajax({
            url : $url,
            type: "POST",
            data: $ajaxData,
            cache: false,
        });

        request.done (function( msg )
        {
            /*$("#modalXlbody").html('');
            $("#modalXl").modal("hide");*/
            
        });

        request.fail (function (msg)
        {
            $("#status-field").html(msg);
        });
    });
    
    $(document).on("click", ".addDocumentiHeaders", function(){
        event.preventDefault();
		
		var $formData = new Object;

		$formData['tableKey'] = $(this).data("tablekey");
		$formData['replaceTable'] = $(this).data("replace_table");
        $formData['projectTableKey'] = $("#projectReplaceKey").val();

		$url = "../kodaDokumentado/ajaxGetDocumentiHeaders.php";

		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
		});

		request.done (function( msg )
		{
			$("#modalXlLabel").html($(msg).filter("#ajaxHeaderModal"));
			$("#modalXlbody").html($(msg).filter("#ajaxBodyModal"));
			$("#modalXlfooter").html($(msg).filter("#ajaxFooterModal"));
            fancytree_fancyTreeClass();
	
            initTinymce();
            inlineTinyMce();

            initJeditable();

            initDataTable();

            fancytree_fancyTreeSelectClass();

            $(".selectpicker").selectpicker();
			$("#modalXl").modal("show");
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
    });
    
    $(document).on("click", ".ajaxDokumentiHeaders", function(){
        event.preventDefault();
		
		var $formData = new Object;

		$formData['replaceTable'] = $(this).data("replace_table");
        $formData['projectTableKey'] = $("#projectReplaceKey").val();
        
        $("#modalXlbody .fancyTreeSelectClass").each(function (event){

            if ($(this).attr("id"))
            {
                var selNodes = $.ui.fancytree.getTree("#"+$(this).attr("id")).getSelectedNodes();
                selData = $.map(selNodes, function(n){
                    return n.key;
                });   

                $formData['headers'] = selData;
            }

        });
        
        console.log($formData);
        
        $url = "../kodaDokumentado/ajaxAddHeadersDokumenti.php";

		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
		});

		request.done (function( msg )
		{
			$("#modalXlLabel").html($(msg).filter("#ajaxHeaderModal"));
			$("#modalXlbody").html($(msg).filter("#ajaxBodyModal"));
			$("#modalXlfooter").html($(msg).filter("#ajaxFooterModal"));
            fancytree_fancyTreeClass();
	
            initTinymce();
            initJeditable();

            initDataTable();

            fancytree_fancyTreeSelectClass();

            $(".selectpicker").selectpicker();
			$("#modalXl").modal("show");
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
        
        $("#modalXl").modal("hide");
    });
    
    $(document).on("click", ".supportButton", function(event){
        if ($(this).data("key") == "replay")
        {
            $("#"+$(this).data("target_div")).toggle();
            $("#"+$(this).data("target_div").replace('replay', 'finish')).hide();
        }
        else if ($(this).data("key") == "finish")
        {
            $("#"+$(this).data("target_div")).toggle();
            $("#"+$(this).data("target_div").replace('finish', 'replay')).show();
        }
        else if ($(this).data("key") == "forward")
        {
            console.log("1683 Hmmmmmmmmm");
            forwardGroup($(this));
            return true;
        }
        
        if ($("#"+$(this).data("target_div")).is(":visible"))
        {
            if ($(this).data("key") == "replay")
            {   
                $("#"+$(this).data("target_div").replace('replay', 'button_area')).show();
            }
            else if ($(this).data("key") == "finish")
            {   
                $("#"+$(this).data("target_div").replace('finish', 'button_area')).show();
            }
        }
        else if ($("#"+$(this).data("target_div")).is(":hidden"))
        {
            if ($(this).data("key") == "replay")
            {   
                $("#"+$(this).data("target_div").replace('replay', 'button_area')).hide();
            }            
        }
    });
    
    $(document).on("click", ".syncFinishArea", function(event)
    {
        if ($(this).is(":checked"))
        {
            $("#"+$(this).data("target_finish_area")).attr("disabled", true);
        }
        else
        {
            $("#"+$(this).data("target_finish_area")).attr("disabled", false);
        }
    });
    
    $(document).on("click", ".btnResponSupport", function(event)
    {
        event.preventDefault();
        
        var $url, $block, $targetForm;
        
        var $formData = new Object;
        
        $targetForm = $(this).data("target_form");
        
        $url = $("#"+$targetForm).attr('action');
        
        $block = false;
        
        $("#"+$(this).data("target_form")+" :input").each(function (index, value) {
            if ($(this).attr("id") !== undefined)
            {
                console.log($(this).attr("id"));
            
                if ($(this).is(":checkbox"))
                {
                    //Do nothing, we don't want tor registrer this.
                }

                else if ($(this).attr("id") !== undefined && $(this).attr("id").startswith("finishNote_"))
                {
                    if ($("#"+$targetForm.replace('form', 'finish')).is(":visible"))
                    {
                        if ($("#"+$targetForm.replace('form', 'noAction')).is(":checked"))
                        {
                            $formData[$(this).attr('id')] = null;
                        }

                        else if ($.trim($(this).val()).length == 0 && $(this).attr("id") !== undefined)
                        {
                            console.log("Hmmmmm "+$(this).attr('id'));
                            $block = true;
                        }
                        else if ($(this).attr("id") !== undefined)
                        {
                            $formData[$(this).attr('id')] = $(this).val();
                        }
                    }
                }
                else if ($.trim($(this).val()).length == 0 && $(this).attr("id") !== undefined)
                {
                    $block = true;
                }
                else if ($(this).attr("id") !== undefined)
                {
                    $formData[$(this).attr('id')] = $(this).val();
                }
            }
        });
        
        if (!$block)
        { 
            var request = $.ajax({
                url : $url,
                type: "POST",
                data: $formData,
                cache: false,
            });

            request.done (function( msg )
            {
                 $("#"+$(this).data("target_form")+" :input").each(function (index, value) {
                     $(this).val('');
                 });
                return false;
            });

            request.fail (function (msg)
            {
                $("#status-field").html(msg);
            });
        }
        
        else
        {
            alert($("#errorMessage").val());
        }
        
    });
    
    function forwardGroup($button)
    {
        var $formData = new Object();

        $formData[$button.attr("id")] = $button.attr("id");

        $url = "forwardGroup.php";

        var request = $.ajax({
            url : $url,
            type: "POST",
            data: $formData,
            cache: false,
            async: false
        });

        request.done (function( msg )
        {	
            $("#modalXlLabel").html($(msg).filter("#ajaxHeaderModal"));
            $("#modalXlbody").html($(msg).filter("#ajaxBodyModal"));
            $("#modalXlfooter").html($(msg).filter("#ajaxFooterModal"));

            $step = getNextStepVtab($('#ajaxBodyModal .nav-pills').attr("id"))

            $("#modalXl").modal("show");

            $(".selectpicker2").selectpicker();

            fancytree_fancyTreeClass();

            fancytree_fancyTreeSelectClass();

            initTinymce();
            inlineTinyMce();

            initJeditable();

            initDataTable();
        });

        request.fail (function (msg)
        {
            $("#status-field").html(msg);
        });
	};
    
    $(document).on("click", ".editCoworker", function(event)
    {
        var $formData = new Object();

        $url = "editCoworker.php";
        
        $formData['id'] = $(this).attr("id");
        $formData['replaceTable'] = $(this).data("replace_table");

        var request = $.ajax({
            url : $url,
            type: "POST",
            data: $formData,
            cache: false,
            async: false
        });

        request.done (function( msg )
        {	
            $("#modalXlLabel").html($(msg).filter("#ajaxHeaderModal"));
            $("#modalXlbody").html($(msg).filter("#ajaxBodyModal"));
            $("#modalXlfooter").html($(msg).filter("#ajaxFooterModal"));


            $step = getNextStepVtab($('#ajaxBodyModal .nav-pills').attr("id"))

            $("#modalXl").modal("show");

            $(".selectpicker2").selectpicker();

            fancytree_fancyTreeClass();

            fancytree_fancyTreeSelectClass();

            initTinymce();
            inlineTinyMce();

            initJeditable();

            initDataTable();
        });

        request.fail (function (msg)
        {
            $("#status-field").html(msg);
        });
    });
    
    $(document).on("click", ".showHeaders", function (event)
    {
        var $formData = new Object();
        
        var $targetTree;
        
        $formData['replaceTable'] = $(this).data("replace_table");
        $formData['targetCollection'] = $(this).data("target_collection");
        $formData['project_id'] = $(this).data("project_id");
        
        $targetTree = $(this).data("target_tree");
        //$formData[$(this).attr("id").replace("\"",'')] = $.ui.fancytree.getTree("#"+$(this).data("target_tree")).getActiveNode().key;
        
        $url = "../../common/displayHeaders.php";
        
        var request = $.ajax({
            url : $url,
            type: "POST",
            data: $formData,
            cache: false,
        });

        request.done (function( msg )
        {
            console.log($targetTree);

            $("#modalXlLabel").html($(msg).filter("#ajaxHeaderModal"));
			$("#modalXlbody").html($(msg).filter("#ajaxBodyModal"));
			$("#modalXlfooter").html($(msg).filter("#ajaxFooterModal"));
            $(".selectpicker2").selectpicker();
			$("#modalXl").modal("show");
            
            fancytree_fancyTreeClass();
	
            initTinymce();
            inlineTinyMce();

            initJeditable();

            initDataTable();

            fancytree_fancyTreeSelectClass();

            $(".selectpicker").selectpicker();
            $(".selectpicker2").selectpicker();

            if ($('#blockSelect').length){
                validateSelectpicker();
            }
            
        });

        request.fail (function (msg)
        {
            $("#status-field").html(msg);
        });
    });
    
    $(document).on("click", ".forwardInternalMessag", function (event)
    {
        var $formData = new Object();
        
        var $targetTree;
        
        $formData['replaceTable'] = $(this).data("replace_table2");
        $targetTree = $(this).data("target_tree2");
        $formData[$(this).attr("id").replace("\"",'')] = $.ui.fancytree.getTree("#"+$(this).data("target_tree")).getActiveNode().key;;
        var selNodes = $.ui.fancytree.getTree("#"+$(this).data("target_tree")).getActiveNode().key;
        
        if ($.ui.fancytree.getTree("#"+$(this).data("target_tree")).getActiveNode().key)
        {
            $url = "../../common/syncContactData.php";

            var request = $.ajax({
                url : $url,
                type: "POST",
                data: $formData,
                cache: false,
            });

            request.done (function( msg )
            {
                console.log($targetTree);
                
                var tree = $.ui.fancytree.getTree("#"+$targetTree);
                tree.reload(msg);
                $("#modalXl").modal("hide");
            });

            request.fail (function (msg)
            {
                $("#status-field").html(msg);
            });

            $("#modalXl").modal("hide");
        }
        
    });
    
    $(document).on("click", ".resyncMasterHeader", function (event)
    {
        var $formData = new Object();
        
        var $targetTree;
        
        $targetTree = $(this).data("target_tree");
        $formData['replaceTable'] = $(this).data("table_key");
        $formData['projectId'] = $(this).data("project_id");
        
        
        var selNodes = $.ui.fancytree.getTree("#"+$targetTree).getSelectedNodes();
        
        var selData = $.map(selNodes, function(n)
        {
            return n.key;
        });
        
        $formData['selNodes'] = JSON.stringify(selData);
        
        console.log($formData);
        $url = "../../common/syncHeaderDataProject.php";

        var request = $.ajax({
            url : $url,
            type: "POST",
            data: $formData,
            cache: false,
        });

        request.done (function( msg )
        {
            console.log(msg)
            $("#ajaxDefaultHeaders").html(msg);
            //$("#modalXl").modal("hide");
        });

        request.fail (function (msg)
        {
            $("#status-field").html(msg);
        });

        $("#modalXl").modal("hide");
    });
    
    $(document).on("click", ".linkToDocumentiTab", function (event)
    {
        $('#nav-tab a[href="#nav-projectDocumentation"]').tab('show') // Select tab by name
    });
    
    $(document).on("click", ".linkToFilesTab", function (event)
    {
        $('#nav-tab a[href="#nav-documentsFiles"]').tab('show') // Select tab by name
    });
    $(document).on("click", ".linkToManualTab", function (event)
    {
        $('#nav-tab a[href="#nav-manual"]').tab('show') // Select tab by name
    });
    
    $(document).on("click", ".btnAddSocialMedia", function (event)
    {
        var $formData = new Object();
        
        var $targetTree;
        
        $formData['replaceTable'] = $(this).data("replace_table");
        
        $url = "./getSocialMedia.php";

        var request = $.ajax({
            url : $url,
            type: "POST",
            data: $formData,
            cache: false,
        });

        request.done (function( msg )
        {
            console.log(msg)
            $("#modalXlLabel").html($(msg).filter("#ajaxHeaderModal"));
			$("#modalXlbody").html($(msg).filter("#ajaxBodyModal"));
			$("#modalXlfooter").html($(msg).filter("#ajaxFooterModal"));
            $(".selectpicker2").selectpicker();
			$("#modalXl").modal("show");
            
            fancytree_fancyTreeClass();
	
            initTinymce();
            inlineTinyMce();

            initJeditable();

            initDataTable();

            fancytree_fancyTreeSelectClass();

            $(".selectpicker").selectpicker();
            $(".selectpicker2").selectpicker();
            
            $("#modalXl").modal("show");
        });

        request.fail (function (msg)
        {
            $("#status-field").html(msg);
        });
    });
    
    $(document).on("click", ".addDocumentCV", function (event)
    {
        var $formData = new Object();
        
        $targetTreeId = $(this).data("target_tree");
        
        $formData['replaceTable'] = $(this).data("replace_table");
        
        $url = "./addDocumentCV.php";

        var request = $.ajax({
            url : $url,
            type: "POST",
            data: $formData,
            cache: false,
        });

        request.done (function( msg )
        {
            console.log(msg)
            $("#modalXlLabel").html($(msg).filter("#ajaxHeaderModal"));
			$("#modalXlbody").html($(msg).filter("#ajaxBodyModal"));
			$("#modalXlfooter").html($(msg).filter("#ajaxFooterModal"));
            $(".selectpicker2").selectpicker();
			$("#modalXl").modal("show");
            
            fancytree_fancyTreeClass();
	
            initTinymce();
            inlineTinyMce();

            initJeditable();

            initDataTable();

            fancytree_fancyTreeSelectClass();

            $(".selectpicker").selectpicker();
            $(".selectpicker2").selectpicker();
            
            $("#modalXl").modal("show");
            
            $('#modalXl').on('hidden.bs.modal', function () {
              
            });
        });

        request.fail (function (msg)
        {
            $("#status-field").html(msg);
        });
    });
    
    $(document).on("click", ".addDocumentFile", function (event)
    {
        var $formData = new Object();
        
        $targetTreeId = $(this).data("target_tree");
        
        $formData['replaceTable'] = $(this).data("replace_table");
        $formData['projectKey'] = $(this).data("project_id");
        
        $url = "./addDocumentFile.php";

        var request = $.ajax({
            url : $url,
            type: "POST",
            data: $formData,
            cache: false,
        });

        request.done (function( msg )
        {
            console.log(msg)
            $("#modalXlLabel").html($(msg).filter("#ajaxHeaderModal"));
			$("#modalXlbody").html($(msg).filter("#ajaxBodyModal"));
			$("#modalXlfooter").html($(msg).filter("#ajaxFooterModal"));
            $(".selectpicker2").selectpicker();
			$("#modalXl").modal("show");
            
            fancytree_fancyTreeClass();
	
            initTinymce();
            inlineTinyMce();

            initJeditable();

            initDataTable();

            fancytree_fancyTreeSelectClass();

            $(".selectpicker").selectpicker();
            $(".selectpicker2").selectpicker();
            
            $("#modalXl").modal("show");
            
            $('#modalXl').on('hidden.bs.modal', function () {
              
            });
        });

        request.fail (function (msg)
        {
            $("#status-field").html(msg);
        });
    });
    
    $(document).on("click", ".showHeadersFolder", function (event)
    {
        var $formData = new Object();
        
        var $targetTree;
        
        $formData['replaceTable'] = $(this).data("replace_table");
        $formData['targetCollection'] = $(this).data("target_collection");
        $formData['project_id'] = $(this).data("project_id");
        
        $targetTree = $(this).data("target_tree");
        //$formData[$(this).attr("id").replace("\"",'')] = $.ui.fancytree.getTree("#"+$(this).data("target_tree")).getActiveNode().key;
        
        $url = "../../common/displayHeaders.php";
        
        var request = $.ajax({
            url : $url,
            type: "POST",
            data: $formData,
            cache: false,
        });

        request.done (function( msg )
        {
            console.log($targetTree);

            $("#modalXlLabel").html($(msg).filter("#ajaxHeaderModal"));
			$("#modalXlbody").html($(msg).filter("#ajaxBodyModal"));
			$("#modalXlfooter").html($(msg).filter("#ajaxFooterModal"));
            $(".selectpicker2").selectpicker();
			$("#modalXl").modal("show");
            
            fancytree_fancyTreeClass();
	
            initTinymce();
            inlineTinyMce();

            initJeditable();

            initDataTable();

            fancytree_fancyTreeSelectClass();

            $(".selectpicker").selectpicker();
            $(".selectpicker2").selectpicker();

            if ($('#blockSelect').length){
                validateSelectpicker();
            }
            
        });

        request.fail (function (msg)
        {
            $("#status-field").html(msg);
        });
    });
    
    $(document).on("click", ".addUserCooperation", function (event)
    {
        event.preventDefault();
        $addUserCooperationId++;
        
        $temp = $(this).attr("id").split(/\s*\_\s*/g);
        
        console.log($temp);
        
        $data = $("#addUserRow_master_"+$temp[1]).clone();
        
        console.log($data.find("#addUserRow_master_"+$temp[1]));
        
        
        $data.attr("id", "removeUserArea\["+$addUserCooperationId+"\]");
        
        $data.find("#firstName\\[1\\]").attr("id", "firstName\["+$addUserCooperationId+"\]");
        $data.find("#sureName\\[1\\]").attr("id", "sureName\["+$addUserCooperationId+"\]");
        $data.find("#email\\[1\\]").attr("id", "email\["+$addUserCooperationId+"\]");
        
        $data.find("#removeUser\\[1\\]").show();
        $data.find("#removeUser\\[1\\]").attr("id", "removeUser\["+$addUserCooperationId+"\]");
        
        $("#newUserCooperation_"+$temp[1]).append($data);
        
        return false;
        
    });
    
    $(document).on("click", "#addUserCooperation", function (event)
    {
        event.preventDefault();
        $addUserCooperationId++;
        
        $data = $("#addUserRow_master").clone();
        
        console.log($data.find("#addUserRow_master"));
        
        
        $data.attr("id", "removeUserArea\["+$addUserCooperationId+"\]");
        
        $data.find("#firstName\\[1\\]").attr("id", "firstName\["+$addUserCooperationId+"\]");
        $data.find("#sureName\\[1\\]").attr("id", "sureName\["+$addUserCooperationId+"\]");
        $data.find("#email\\[1\\]").attr("id", "email\["+$addUserCooperationId+"\]");
        
        $data.find("#removeUser\\[1\\]").show();
        $data.find("#removeUser\\[1\\]").attr("id", "removeUser\["+$addUserCooperationId+"\]");
        
        $("#newUserCooperation").append($data);
        
        return false;
        
    });
    
    $(document).on("click", "[id ^= removeUser]", function (event)
    {
        event.preventDefault();
        
        $id = $(this).attr("id");
        
        $key = $id.match(/\d+/g);
        
        $("#removeUserArea\\["+$key[0]+"\\]").remove();
        
        return false;
    });
    
    $(document).on("click", "#createCooperation", function (event)
    {
        event.preventDefault();
        
        $formData = new Object();
        
        $run = true;
        
        $refreshDiv = $(this).data("reload_div");
        
        $formData['replaceTable'] = $(this).data("replace_table");
        
        $("form#show_account_cooperation :input:text").each(function(){
            
            var input = $(this); // This is the jquery object of the input, do what you will
            
            if ($.trim($(this).val()).length == 0)
            {
                alert("Tomt fält......"+$(this).attr("id"));
                
                $run = false;
                
                return false;
            }
            $formData[$(this).attr("id")] = $.trim($(this).val());
        });
        
        if ($run)
        {
            console.log($formData);
            
            $url = "addCooperation.php";
        
            var request = $.ajax({
                url : $url,
                type: "POST",
                data: $formData,
                cache: false,
            });

            request.done (function( msg )
            {
                if ($refreshDiv.length > 0)
                {
                    $url = "showAjax.php";
        
                    var request2 = $.ajax({
                        url : $url,
                        type: "POST",
                        data: $formData,
                        cache: false,
                    });

                    request2.done (function( msg )
                    {
                        $("#"+$refreshDiv).html(msg);
                    });

                    request2.fail (function (msg)
                    {
                        $("#status-field").html(msg);
                    });
                }
                
            });
            
            request.fail (function (msg)
            {
                $("#status-field").html(msg);
            });
        }
        
        return false;
    });
    
    
    
    
    $(document).on("click", ".addUsers2Cooperation", function (event)
    {
        event.preventDefault();
        
        $formData = new Object();
        
        $run = true;
        
        $temp = $(this).attr("id").split(/\s*\_\s*/g)
        
        $refreshDiv = $(this).data("reload_div");
        
        $formData['refreshDiv'] = $(this).data("reload_div");
        $formData['replaceTable'] = $(this).data("replace_table");
        $formData['projectKey'] = $temp[1];
        
        $("form#add_user_cooperation\\["+$temp[1]+"\\] :input:text").each(function(){
            
            var input = $(this); // This is the jquery object of the input, do what you will
            
            if ($.trim($(this).val()).length == 0)
            {
                alert("Tomt fält......"+$(this).attr("id"));
                
                $run = false;
                
                return false;
            }
            $formData[$(this).attr("id")] = $.trim($(this).val());
        });
        
        if ($run)
        {
            console.log($formData);
            
            $url = "addCooperation.php";
        
            var request = $.ajax({
                url : $url,
                type: "POST",
                data: $formData,
                cache: false,
            });

            request.done (function( msg )
            {
                if (typeof $refreshDiv !== 'undefined')
                {
                    if ($refreshDiv.length > 0)
                    {
                        $url = "showAjax.php";

                        var request2 = $.ajax({
                            url : $url,
                            type: "POST",
                            data: $formData,
                            cache: false,
                        });

                        request2.done (function( msg )
                        {
                            $("#"+$refreshDiv).html(msg);
                        });

                        request2.fail (function (msg)
                        {
                            $("#status-field").html(msg);
                        });
                    }
                }
                
            });
            
            request.fail (function (msg)
            {
                $("#status-field").html(msg);
            });
        }
        
        return false;
    });
    
    $(document).on("click", ".deleteUser", function (event)
    {
        event.preventDefault();
        
        $formData = new Object();
        
        $refreshDiv = $(this).data("reload_div");
        
        $formData['replaceTable'] = $(this).data("replace_table");
        $formData['refreshDiv'] = $(this).data("reload_div");
        $formData['projectKey'] = $(this).data("project_key");
        
        $temp = $(this).attr("id").match(/\[(.*?)\]/);
        var index = $formData['refreshDiv'].lastIndexOf("_");
        var $temp2 = $formData['refreshDiv'].substr(index+1);
        console.log($temp2);
        
        $projectKey = $formData['deleteUser'] = $temp[0].replace('[', '').replace(']', '');
        
        $confirmMessage = $("#confirmMessage").val();
        
        if (confirm($confirmMessage))
        {
            $url = "addCooperation.php";
        
            var request = $.ajax({
                url : $url,
                type: "POST",
                data: $formData,
                cache: false,
            });

            request.done (function( msg )
            {
                $url = "reloadArea.php";
                var request2 = $.ajax({
                    url : $url,
                    type: "POST",
                    data: $formData,
                    cache: false,
                });

                request2.done (function( msg )
                {
                    $("#table_"+$temp2).DataTable().destroy();
                    $("#table_"+$temp2).html(msg);
                });
            });    
            request.done (function( msg )
            {
                
            });
            
            request.fail (function (msg)
            {
                $("#status-field").html(msg);
            });
        }
        
        return false;
    });
    
    $(document).on("click", "#selectHeaderImage", function (event)
    {
        event.preventDefault();
        
        $formData = new Object();
        
        $refreshDiv = $(this).data("reload_div");
        
        $formData['replaceTable'] = $(this).data("replace_table");
        $formData['inputField'] = $(this).data("target_input");
        $url = "selectHeaderImage.php";
        
        var request = $.ajax({
            url : $url,
            type: "POST",
            data: $formData,
            cache: false,
        });

        request.done (function( msg )
        {
            $("#modalXlLabel").html($(msg).filter("#ajaxHeaderModal"));
			$("#modalXlbody").html($(msg).filter("#ajaxBodyModal"));
			$("#modalXlfooter").html($(msg).filter("#ajaxFooterModal"));
            $(".selectpicker2").selectpicker();
			$("#modalXl").modal("show");
        });

        request.fail (function (msg)
        {
            $("#status-field").html(msg);
        });
        
        return false;
    });
    
    $(document).on("click", ".contactUser", function (event)
    {
        event.preventDefault();
        
        $formData = new Object();
        
        $refreshDiv = $(this).data("reload_div");
        
        $formData['replaceTable'] = $(this).data("replace_table");
        $formData['projectKey'] = $(this).data("project_key");
        
        $temp = $(this).attr("id").match(/\[(.*?)\]/);
        
        $projectKey = $formData['conctactUser'] = $temp[0].replace('[', '').replace(']', '');
       
        $url = "contactMessage.php";
        
        var request = $.ajax({
            url : $url,
            type: "POST",
            data: $formData,
            cache: false,
        });

        request.done (function( msg )
        {
            $("#modalXlLabel").html($(msg).filter("#ajaxHeaderModal"));
			$("#modalXlbody").html($(msg).filter("#ajaxBodyModal"));
			$("#modalXlfooter").html($(msg).filter("#ajaxFooterModal"));
            $(".selectpicker2").selectpicker();
			$("#modalXl").modal("show");
        });

        request.fail (function (msg)
        {
            $("#status-field").html(msg);
        });
        
        return false;
    });
    
    $(document).on("click", ".ajaxContactMessageCollaboration", function (event)
    {
        event.preventDefault();
        
        $formData = new Object();
        
        $url = "contactMessage.php";
        
        $formData['reciver'] = $("#reciverMessage").selectpicker("val");
        $formData['contactMessage'] = tinymce.get('contactMessageCollaboration').getContent({});
        $formData['project_key'] = $(this).data("project_key");
        
        var request = $.ajax({
            url : $url,
            type: "POST",
            data: $formData,
            cache: false,
        });

        request.done (function( msg )
        {
            $("#modalXl").modal("hide");
        });

        request.fail (function (msg)
        {
            $("#status-field").html(msg);
        });
        
        return false;
    });
    
    $(document).on("click", ".changeAccount", function (event)
    {
        event.preventDefault();
        
        var $formData = new Object();
        
        $url = "./common/changeAccount.php";
        
        $temp = $(this).attr("id").match(/\[(.*?)\]/);
        
        $formData['accountId'] = $temp[1];
        
        $formData['replaceTable'] = $(this).data("replace_table");

        if (!urlExists($url))
        {
            $url = "../"+$url;
        }
        
        if (!urlExists($url))
        {
            $url = "../"+$url;
        }
        
        if (!urlExists($url))
        {
            $url = "../"+$url;
        }
        
        if (!urlExists($url))
        {
            $url = "../"+$url;
        }
        
        var request = $.ajax({
            url : $url,
            type: "POST",
            data: $formData,
            cache: false,
        });

        request.done (function( msg )
        {
            location.reload();
        });

        request.fail (function (msg)
        {
            $("#status-field").html(msg);
        });
        
        return false;
    });
    
    $(document).on("click", ".removeTaskColumn", function()
    {
        var $formData = new Object();

        $formData['removeTaskColumn'] = $(this).attr("id");
        $formData['replaceTable'] = $("#replace_table").val();
        $formData['formKey'] = $(this).data("replace_table");
        $target_tree = $(this).data("reload_tree");

        console.log($target_tree);
        
        $url = "syncNote.php";

        var request = $.ajax({
            url : $url,
            type: "POST",
            data: $formData,
            cache: false,
        });

        request.done (function( msg )
        {
            
            $url = "syncNote.php";
            delete $formData['removeTaskColumn'];
            
            console.log($formData);
            var request = $.ajax({
                url : $url,
                type: "POST",
                data: $formData,
                cache: false,
            });

            request.done (function( msg )
            {
                reloadTree($formData['formKey'], null, $target_tree);
                $temp = $("ul#myTab a.active").attr("id").replace("-tab",'');
                $("#ajaxArea_note_status").html('');
                $("#"+$temp).html(msg);
                //$("#modalXl").modal("hide");
            });

            request.fail (function (msg)
            {
                $("#status-field").html(msg);
            });
        });

        request.fail (function (msg)
        {
            $("#status-field").html(msg);
        });
    });
    
    $(document).on("click", "[id ^= imageGallery]", function(event){
        event.preventDefault();               
        
        $formData = new Object();
        
        $formData['replaceTable'] = $(this).data("replace_table");
        
        var matches = $(this).attr("id").match(/\[(.*?)\]/);

        if (matches) {
            var submatch = matches[1];
        }
        else
        {
            return false;
        }
        $formData['projectKey'] = submatch;
        
        $url = "editImageReferences.php";
        
        var request = $.ajax({
            url : $url,
            type: "POST",
            data: $formData,
            cache: false,
        });

        request.done (function( msg )
        {
            $("#modalXlLabel").html($(msg).filter("#ajaxHeaderModal"));
			$("#modalXlbody").html($(msg).filter("#ajaxBodyModal"));
			$("#modalXlfooter").html($(msg).filter("#ajaxFooterModal"));
            $(".selectpicker2").selectpicker();
			$("#modalXl").modal("show");
        });

        request.fail (function (msg)
        {
            $("#status-field").html(msg);
        });
        
        return false;
    
    });
    
    $(document).on("keydown", "form", function(event) { 
        if (event.keyCode === 13)
        {
            $(this).find(".btn").click();

            return event.key != "Enter";
        }
        else
        {
            return event.key != "Enter";
        }
    });
    
    $(document).on("click", ".addUserToTree", function(event)
    {
        event.preventDefault();               
        
        $formData = new Object();
        
        $formData['replaceTable'] = $(this).data("replace_table");
        
        $url = "addUser.php";
        
        var request = $.ajax({
            url : $url,
            type: "POST",
            data: $formData,
            cache: false,
        });

        request.done (function( msg )
        {
            $("#modalXlLabel").html($(msg).filter("#ajaxHeaderModal"));
			$("#modalXlbody").html($(msg).filter("#ajaxBodyModal"));
			$("#modalXlfooter").html($(msg).filter("#ajaxFooterModal"));
            $(".selectpicker2").selectpicker();
			$("#modalXl").modal("show");
        });

        request.fail (function (msg)
        {
            $("#status-field").html(msg);
        });
        
        return false;
    });
	
    $('#modalXl').on('hidden.bs.modal', function () {
        //reloadTree($("#"+$targetTreeId).data("replace_table"), null, $targetTreeId);
        if ($("#headerImage"))
        {
            $("#headerImage").trigger("change");
        }
    });
	
	function reloadNewsletterReaders()
	{
		var $formData = new Object;
		
		$url = "addReaderNewsletter.php";

		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
		});

		request.done (function( msg )
		{
			//console.log($(msg));
			$('#newsltterReaders').DataTable().destroy();
			$('#newsltterReaders').html(msg);
			initDataTable();
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
	}
	
	$(document).on("click", ".editNewsletter", function(event){
		console.log("Hmmmm, varför funkar detta????")
		
		event.preventDefault();
		
		var $formData = new Object;

		$formData['replaceTable'] = $(this).data("replace_table");
		var matches = $(this).attr("id").match(/\[(.*?)\]/);

		if (matches) {
			var submatch = matches[1];
		}
		
		$formData['id'] = submatch;
		// acces node attributes

		$url = "showAjax.php";

		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
		});

		request.done (function( msg )
		{
			$("#modalXlLabel").html($(msg).filter("#ajaxHeaderModal").text());
			$("#modalXlbody").html($(msg).filter("#ajaxBodyModal"));
			$("#modalXlfooter").html($(msg).filter("#ajaxFooterModal"));
            $(".selectpicker2").selectpicker();
			$("#modalXl").modal("show");
			
			$('#modalXl').on('hidden.bs.modal', function () {
              	reloadNewsletterReaders();
            });
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
		
		return false;
	});
	
	$(document).on("click", '[id ^= sendNewsletter]', function(event){
		event.preventDefault();
		
		var $formData = new Object;

		$formData['replaceTable'] = $(this).data("replace_table");
		var matches = $(this).attr("id").match(/\[(.*?)\]/);
		
		if (matches) {
			var submatch = matches[1];
		}
		
		$formData['sendNewsletter'] = submatch;
		// acces node attributes

		console.log($formData);
		
		$url = "showAjax.php";

		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
		});

		request.done (function( msg )
		{
			$("#ajax_"+$formData['replaceTable']).html(msg);
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
		
		return false;
	});
	
	$(document).on("click", "#addReaderNewsletter", function(event){
		event.preventDefault();
		
		var $formData = new Object;

		var $run = true;
		
		$formData['replaceTable'] = $(this).data("replace_table");
		
		$(this).closest("form").attr("id");
		
		$("form#"+$(this).closest("form").attr("id")+" :input").each(function(){
		 	
			if($(this).prop('required'))
			{
				if ($(this).hasClass("selectpicker2"))
				{
					$formData[$(this).attr("id")] = $(this).selectpicker("val").join(", ");
					
					if ($formData[$(this).attr("id")].length == 0)
					{
						$run = false;
					}
				}
				
				else if ($(this).val().trim().length > 0)
				{
					$formData[$(this).attr("id")] = $(this).val();
				}
				else
				{
					$run = false;
				}
				
			}
			else if ($(this).val().trim().length > 0)
			{
				$formData[$(this).attr("id")] = $(this).val();
			}
			var input = $(this); // This is the jquery object of the input, do what you will
		});
		
		if (!$run)
		{
			alert("Kontrollera alla inmatningsfält så att dessa inte är tomma!");
			return false;
		}
		
		$url = "addReaderNewsletter.php";

		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
		});

		request.done (function( msg )
		{
			//console.log($(msg));
			$('#newsltterReaders').DataTable().destroy();
			$('#newsltterReaders').html(msg);
			initDataTable();
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
		
		return false;
	});
});