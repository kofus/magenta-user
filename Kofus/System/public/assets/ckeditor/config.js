		CKEDITOR.editorConfig = function( config ) {
			config.toolbarGroups = [
				{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
				{ name: 'links' },
				{ name: 'insert' },
				{ name: 'forms' },
				{ name: 'tools' },
				{ name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
				{ name: 'others' },
				'/',
				{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
				{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
				{ name: 'styles' },
				{ name: 'colors' }
			];
			config.removeButtons = 'Underline,Strike,Indent,Outdent,Image,Styles';
			config.format_tags = 'p;h1;h2;h3;pre';
			config.height = '300px';
			config.removeDialogTabs = 'image:advanced;link:advanced';
			config.allowedContent='h1(*);h2(*);h3(*);h4(*);pre(*);p(*);br(*);span(*);table[border](*);tbody(*);thead(*);tr(*);th[width,align,valign](*);td[width,rowspan,align,valign](*);ul(*);ol(*);li(*);i(*);b(*);strong(*);em(*);u(*);a[!href,target](*);img[alt,src]{width,height}(*);hr(*);sub;sup;blockquote';
		}; 