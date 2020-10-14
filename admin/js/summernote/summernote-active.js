(function ($) {
 "use strict";

		$(".html-editor-cm")[0] && $(".html-editor-cm").summernote({
            height: 335
        }),
            
		$(".html-editor")[0] && $(".html-editor").summernote({
            height: 500
//            toolbar:[
//
//              // This is a Custom Button in a new Toolbar Area
//              ['custom', ['examplePlugin']],
//
//              // You can also add Interaction to an existing Toolbar Area
//              ['style', ['style' ,'examplePlugin']]
//            ]
        }),
        $(".html-editor-click")[0] && ($("body").on("click", ".hec-button", function() {
            $(".html-editor-click").summernote({
                focus: !0
            }), $(".hec-save").show()
        }), 
        $("body").on("click", ".hec-save", function() {
            $(".html-editor-click").code(), $(".html-editor-click").destroy(), $(".hec-save").hide(), notify("Content Saved Successfully!", "success")
        })), 
        $(".html-editor-airmod")[0] && $(".html-editor-airmod").summernote({
            airMode: !0
        });
 
	
 
})(jQuery); 