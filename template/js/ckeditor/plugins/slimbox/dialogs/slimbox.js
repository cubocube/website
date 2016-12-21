CKEDITOR.dialog.add( 'slimbox', function( editor )
{
	return {
		title : 'Настройки всплывающего изображения',
		minWidth : 450,
		minHeight : 80,
		contents : [
			{
				id : 'tab1',
				label : 'First Tab',
				title : 'First Tab',
				elements : [
					{
						id : 'text',
						type : 'html',
						html :'Вставьте сопроводительный текст для всплывающего изображения:'
					},
					{
						id : 'input0',
						type : 'html',
						html :'<input type="text" name="chili" style="width: 350px; border: 1px #000 solid;" />',
						validate : function(){
							if ( !this.getValue() )
							{
								alert( 'Текст не указан' );
								return false;
							}

							var element = editor.getSelection().getStartElement();

							if ( element.getName() == 'a' )
							{
								element.setAttribute( 'rel', 'lightbox' );
								element.setAttribute( 'title', this.getValue() );
								return true;
							}

							element = element.getParent();

							if ( element.getName() != 'a' )
							{
								alert( 'Выбранный элемент не является ссылкой' );
								return true;
							}

							element.setAttribute( 'rel', 'lightbox' );
							element.setAttribute( 'title', this.getValue() );
							return true;
						}
					}
				]
			}
		]
	};
} );
