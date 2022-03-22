// JavaScript Document
$( document ).ready(function() {
    
    $addUserCooperationId = 1;
    
    $(".datepicker").each(function() {
        $( this).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd'
        });
      } );
    
    $.fn.selectpicker.Constructor.DEFAULTS.iconBase='fa';
    
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
	
	$( document ).ajaxComplete(function( event, xhr, settings ) {
		$(".selectpicker2").selectpicker();
        $(".selectpicker").selectpicker();
		
		initTinymce();
        inlineTinyMce();
		
		initJeditable();
		
		initDataTable();
        
        inlineTinyMce();
		
		console.log("Ajax avslutat");
        
        $(".content").loading("stop");
	});
	
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
				table = $(this).DataTable();
			}
			else {
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

	function reloadTree($tree, $targetFile = null, $targetTree = null)
	{
        console.log($tree);
        
        console.log($targetTree);
        
		var $id, $id2, $tableKey, $text, $url;
				
		var $formData = new Object();

		$formData['table'] = $tree;
        
        if($targetFile  == null)
        {
            $url = "../common/resyncTree.php";

            if (!urlExists($url))
            {
                $url = "../../common/resyncTree.php";
            }
        }
        else
        {
            $url = $targetFile;
        }
        
        console.log($formData);
        
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
		console.log("Editering ska sparas till databasen....");

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

            $url = "../../common/syncData.php";

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
			plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
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
             images_upload_url: 'postAcceptor.php',
		  /*link_list: [
			{ title: 'My page 1', value: 'https://www.tiny.cloud' },
			{ title: 'My page 2', value: 'http://www.moxiecode.com' }
		  ],
		  image_list: [
			{ title: 'My page 1', value: 'https://www.tiny.cloud' },
			{ title: 'My page 2', value: 'http://www.moxiecode.com' }
		  ],
		  image_class_list: [
			{ title: 'None', value: '' },
			{ title: 'Some class', value: 'class-name' }
		  ],*/
		  importcss_append: true,
		  /*file_picker_callback: function (callback, value, meta) {
			/* Provide file and text for the link dialog */
			if (meta.filetype === 'file') {
			  callback('https://www.google.com/logos/google.jpg', { text: 'My text' });
			}

			/* Provide image and alt text for the image dialog */
			if (meta.filetype === 'image') {
			  callback('https://www.google.com/logos/google.jpg', { alt: 'My alt text' });
			}

			/* Provide alternative source and posted for the media dialog */
			if (meta.filetype === 'media') {
			  callback('movie.mp4', { source2: 'alt.ogg', poster: 'https://www.google.com/logos/google.jpg' });
			}
		  },
			init_instance_callback: function (editor) {
				editor.on('blur', function (e) {
					saveTinyMceEdit();
			  	});
			},
		  /*templates: [
				{ title: 'New Table', description: 'creates a new table', content: '<div class="mceTmpl"><table width="98%%"  border="0" cellspacing="0" cellpadding="0"><tr><th scope="col"> </th><th scope="col"> </th></tr><tr><td> </td><td> </td></tr></table></div>' },
			{ title: 'Starting my story', description: 'A cure for writers block', content: 'Once upon a time...' },
			{ title: 'New list with dates', description: 'New List with dates', content: '<div class="mceTmpl"><span class="cdate">cdate</span><br /><span class="mdate">mdate</span><h2>My List</h2><ul><li></li><li></li></ul></div>' }
		  ],*/
		  template_cdate_format: '[Date Created (CDATE): %m/%d/%Y : %H:%M:%S]',
		  template_mdate_format: '[Date Modified (MDATE): %m/%d/%Y : %H:%M:%S]',
		  height: 600,
		  image_caption: true,
		  quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
		  noneditable_noneditable_class: 'mceNonEditable',
		  toolbar_mode: 'sliding',
		  contextmenu: 'link image imagetools table',
		  content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
		 });
    }
    
	function initTinymce()
	{
		//Remove all active instance of TinyMCE, layout bug else
		tinymce.remove(".tinyMceArea");
		
		tinymce.init({
            placeholder: $(this).placeholder,
			selector: '.tinyMceArea',
			language: 'sv_SE',	
			plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
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
		  link_list: [
			{ title: 'My page 1', value: 'https://www.tiny.cloud' },
			{ title: 'My page 2', value: 'http://www.moxiecode.com' }
		  ],
		  image_list: [
			{ title: 'My page 1', value: 'https://www.tiny.cloud' },
			{ title: 'My page 2', value: 'http://www.moxiecode.com' }
		  ],
		  image_class_list: [
			{ title: 'None', value: '' },
			{ title: 'Some class', value: 'class-name' }
		  ],
		  importcss_append: true,
		  file_picker_callback: function (callback, value, meta) {
			/* Provide file and text for the link dialog */
			if (meta.filetype === 'file') {
			  callback('https://www.google.com/logos/google.jpg', { text: 'My text' });
			}

			/* Provide image and alt text for the image dialog */
			if (meta.filetype === 'image') {
			  callback('https://www.google.com/logos/google.jpg', { alt: 'My alt text' });
			}

			/* Provide alternative source and posted for the media dialog */
			if (meta.filetype === 'media') {
			  callback('movie.mp4', { source2: 'alt.ogg', poster: 'https://www.google.com/logos/google.jpg' });
			}
		  },
			init_instance_callback: function (editor) {
				editor.on('blur', function (e) {
                    saveTinyMceEdit();
			  	});
			},
		  templates: [
				{ title: 'New Table', description: 'creates a new table', content: '<div class="mceTmpl"><table width="98%%"  border="0" cellspacing="0" cellpadding="0"><tr><th scope="col"> </th><th scope="col"> </th></tr><tr><td> </td><td> </td></tr></table></div>' },
			{ title: 'Starting my story', description: 'A cure for writers block', content: 'Once upon a time...' },
			{ title: 'New list with dates', description: 'New List with dates', content: '<div class="mceTmpl"><span class="cdate">cdate</span><br /><span class="mdate">mdate</span><h2>My List</h2><ul><li></li><li></li></ul></div>' }
		  ],
		  template_cdate_format: '[Date Created (CDATE): %m/%d/%Y : %H:%M:%S]',
		  template_mdate_format: '[Date Modified (MDATE): %m/%d/%Y : %H:%M:%S]',
		  height: 600,
		  image_caption: true,
		  quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
		  noneditable_noneditable_class: 'mceNonEditable',
		  toolbar_mode: 'sliding',
		  contextmenu: 'link image imagetools table',
		  content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
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

		$url = "../common/syncTreeData.php";
        
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
					console.log("Hej på dig!!!!!");
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
                        $formData['primary'] = $("#primaryLang").selectpicker("val");
                        $formData['secondary'] = $("#secondaryLang").selectpicker("val");
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
                    
                    if ($("#projectReplaceKey"))
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
						$("#"+$target).html(msg);
						
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
                        
						$url = "syncCompetenceHeaders.php";
                        
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
    
	$(document).on('change', '.syncData', function(event)	
	{
		var $id, $id2, $tableKey, $text, $url;
				
		var $formData = new Object();

		$id2 = $(this).attr("id");
        
        if ($(this).data("reload_tree"))
        {
            
            $reload_tree = $(this).data("reload_tree");
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
		
        $url = "../common/syncData.php";

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
				reloadTree($(msg).filter("#resynctree").val())
			}
            
            else if ( typeof $reload_tree !== 'undefined')
            {
                $formData["reload_tree"] = $reload_tree;
                
                $url = "reloadArea.php";

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
		var $formData = new Object;

        $formData['replaceTable'] = $(this).data("replace_table");
        $formData[$(this).attr("id")] = $(this).selectpicker('val');
        
		$select = $(this);
        
        $url = "../common/syncData.php";

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
        }
		
		$formData[$select.attr("id")] = $select.selectpicker("val");
        if ($select.selectpicker("val").length == 0)
        {
           $formData[$select.attr("id")] = ''; 
        }
		$formData['replaceTable'] = $selectdata("replace_table");
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
				reloadTree($select.data("update_tree"), $select.data("target_ajax_div"))
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
				reloadTree($(msg).filter("#resynctree").val())
			}
            validateSelectpicker();
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
        
        if ( typeof $reload_tree !== 'undefined')
        {
            $url = "reloadArea.php";

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
	
	$(document).on("click", ".addToTree", function (event){
        var $formData = new Object();
        
		$target_tree = $(this).data("target_tree");
		$replaceTable = $(this).data("replace_table");
		$form_id = $(this.form).attr("id");
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
        
		console.log($target_tree);
		console.log($(this).data());
		
		$block = false;
		
        $url = "../common/addDataTree.php";
		
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
				$formData[$(this).attr("id")] = $(this).val();
                console.log($formData);
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
        });
        
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
				if ($text.trim().length == 0)
				{
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
            else if ('education' in $formData)
			{
				$text = $formData['education'];
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
			$url = "addTranslation.php";

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
					$("#translationTable").html(msg);
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
    
    $(document).on("show.bs.tab", 'a[data-toggle="tab"]', function (event){
        $targetDiv = $(this).attr('href')
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
        
        console.log("Vi ska sända ett kontakt formulär!!!!"+$url );
        
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
            console.log($formData);
            
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
        console.log("1766 Hmmmmmm......");
        
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
    
    $('#modalXl').on('hidden.bs.modal', function () {
        reloadTree($("#"+$targetTreeId).data("replace_table"), null, $targetTreeId);
    });
});