(function() {
	 var ect_cats=JSON.parse(ect_cat_obj.category);
	   var categories=[];

	   for( var cat in ect_cats){
		   categories.push({"text":ect_cats[cat],"value":cat});
	   }
		var date_formats={
		   "formats":[
		  	   {"text":"Default (Day Month Year- D,j,Y)","value":"default"},
			   {"text":"Day Month(D,J)","value":"DM"},
			   {"text":"Month Day(M,D)","value":"MD"},
			   {"text":"Full","value":"full"},
			 	]};

	tinymce.PluginManager.add('ect_tc_button', function( editor, url ) {
		editor.addButton( 'ect_tc_button', {
			title: 'Events Calendar Templates',
			type: 'menubutton',
         	icon: 'icon ect-own-icon',
            menu:[{
            	text: 'Events Calendar Templates',
                value: 'Events Calendar Templates',
                onclick: function() {
                     editor.windowManager.open( {
				        title: 'Events Calendar Templates Shortcode Generator',
				        body: [
				       
				         { type: 'listbox',
				            name: 'category',
				            label: 'Events Categories',
				            values:categories
				        },
				         {
				            type: 'listbox',
				            name: 'template',
				            label: 'Select Template',
				            values: [
				           	   {text: 'Default(List)', value: 'default'},
				               {text: 'Simple Timeline', value: 'timeline'},
				               {text: 'Classic Timeline', value: 'classic-timeline'},
				       			 ]
							},
						/*	 {
				            type: 'listbox',
				            name: 'design',
				            label: 'Select Design',
				            values: [
				           	   {text: 'Default', value: 'default'},
				                {text: 'Classic', value: 'classic'},
				        		 ]
							}, */
							{
					            type: 'textbox',
					            name: 'title',
					            label: 'Title',
					            value:'Upcoming Events'
					        },
					        { type: 'listbox',
					         name: 'date_formats',
					         label: 'Date formats',
					         values:date_formats.formats
					        },
				    	   {
				            type: 'listbox',
				            name: 'time',
				            label: 'Events Time',
				            values: [
				                {text: 'Future', value: 'future'},
				                {text: 'Past', value: 'past'},
				                ]
							},
				           {
				            type: 'listbox',
				            name: 'order',
				            label: 'Events Order',
				            values: [
				                {text: 'ASC', value: 'ASC'},
				                {text: 'DESC', value: 'DESC'},
				                ]
						},
						 {
				            type: 'listbox',
				            name: 'venue',
				            label: 'Hide venue',
				            values: [
				            	 {text: 'NO', value: 'no'},
				                {text: 'YES', value: 'yes'},
				               
				                ]
						},
						
						{
				            type: 'textbox',
				            name: 'limit',
				            label: 'Limit the events',
				            value:"10"
				        },
						{
						    type   : 'container',
						    name   : 'container',
						    label  : 'Note',
						    html   : '<h1>Show events from date range e.g( 2017-01-01 to 2017-02-05).<br><small>Please dates in this format(YY-MM-DD)</small></h1>'
						},
						{
				            type: 'textbox',
				            name: 'start_date',
				            label: 'Start Date | format(YY-MM-DD)',
				            value:""
				        },
				        {
				            type: 'textbox',
				            name: 'end_date',
				            label: 'End Date | format(YY-MM-DD)',
				            value:""
				        },
				       ],
				        onsubmit: function( e ) {
				            editor.insertContent(
						'[events-calendar-templates  template="'+e.data.template+'"  category="' + e.data.category + '" date_format="' + e.data.date_formats + '" start_date="' + e.data.start_date + '"  end_date="' + e.data.end_date + '" limit="' + e.data.limit + '" order="' + e.data.order + '" hide-venue="' + e.data.venue + '"   time="' + e.data.time + '" title="' + e.data.title + '"]'
				         );
				        }
				    });
                    }
             }]       
              });
    });


})();