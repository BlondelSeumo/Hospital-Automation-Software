CKEDITOR.editorConfig = function(config){
    
config.allowedContent = true;
config.extraAllowedContent = 'p(*)[*]{*};div(*)[*]{*};li(*)[*]{*};ul(*)[*]{*}';
CKEDITOR.dtd.$removeEmpty['i'] = false;
CKEDITOR.dtd.$removeEmpty.i = 0;
config.protectedSource.push(/<i class="fa[s|r|l|b] [A-Za-z0-9\-]+"><\/i>/g);
CKEDITOR.dtd.$removeEmpty['span'] = false;
config.contentsCss = baseurl+'/backend/plugins/ckeditor/plugins/font-awesome/css/font-awesome.css';
config.enterMode = CKEDITOR.ENTER_BR // pressing the ENTER KEY input <br/>
        config.shiftEnterMode = CKEDITOR.ENTER_P; //pressing the SHIFT + ENTER KEYS input <p>
        config.autoParagraph = false; // stops automatic insertion of <p> on focus


config.toolbar = 'FrontCMS';       
config.toolbar_FrontCMS =[
    { name: 'document', items : ['Source','Preview','Print'] },
    { name: 'clipboard', items : ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo'] },
    { name: 'editing', items : ['Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt'] },
    { name: 'forms', items : ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'] },
    { name: 'basicstyles', items : ['Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat'] },
    { name: 'links', items : ['Link','Unlink','Anchor'] },
    { name: 'colors', items : ['TextColor','BGColor'] },
    '/',
    { name: 'insert', items : ['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe'] },
    { name: 'paragraph', items : ['NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl'] },
    { name: 'styles', items : ['Styles','Format','Font','FontSize'] },   
    ];



};