tinymce.init({
   	selector: 'textarea.wysiwyg',
	height: 200,
	theme: 'modern',
	statusbar: false,
	branding: false,
	menubar: false,
	plugins: [
		'advlist autolink lists link image charmap print preview hr anchor pagebreak',
		'searchreplace wordcount visualblocks visualchars code fullscreen',
		'insertdatetime media nonbreaking save table contextmenu directionality',
		'emoticons template paste textcolor colorpicker textpattern imagetools codesample toc help'
	],
	toolbar1: 'undo redo | insert | styleselect | fontsizeselect bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent',
	toolbar2: 'media link | forecolor backcolor emoticons | codesample code | help',
    fontsize_formats: "8px 10px 12px 14px 18px 24px 36px",
	setup:function(ed) {
       ed.on('change', function(e) {
           unsaved = true;
       });
   }
 });