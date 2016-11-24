/**
 * View logic for Alarm_Notificaties
 */

/**
 * application logic specific to the Alarm_Notificatie listing page
 */
var page = {

	alarm_Notificaties: new model.Alarm_NotificatieCollection(),
	collectionView: null,
	alarm_Notificatie: null,
	modelView: null,
	isInitialized: false,
	isInitializing: false,

	fetchParams: { filter: '', orderBy: '', orderDesc: '', page: 1 },
	fetchInProgress: false,
	dialogIsOpen: false,

	/**
	 *
	 */
	init: function() {
		// ensure initialization only occurs once
		if (page.isInitialized || page.isInitializing) return;
		page.isInitializing = true;

		if (!$.isReady && console) console.warn('page was initialized before dom is ready.  views may not render properly.');

		// make the new button clickable
		$("#newAlarm_NotificatieButton").click(function(e) {
			e.preventDefault();
			page.showDetailDialog();
		});

		// let the page know when the dialog is open
		$('#alarm_NotificatieDetailDialog').on('show',function() {
			page.dialogIsOpen = true;
		});

		// when the model dialog is closed, let page know and reset the model view
		$('#alarm_NotificatieDetailDialog').on('hidden',function() {
			$('#modelAlert').html('');
			page.dialogIsOpen = false;
		});

		// save the model when the save button is clicked
		$("#saveAlarm_NotificatieButton").click(function(e) {
			e.preventDefault();
			page.updateModel();
		});

		// initialize the collection view
		this.collectionView = new view.CollectionView({
			el: $("#alarm_NotificatieCollectionContainer"),
			templateEl: $("#alarm_NotificatieCollectionTemplate"),
			collection: page.alarm_Notificaties
		});

						// initialize the collection view
		this.collectionView = new view.CollectionView({
			el: $("#FilterNodeTemplateContainer"),
			templateEl: $("#FilterNodeTemplate"),
			collection: page.alarm_Notificaties
		});
		
		// initialize the collection view
		this.collectionView = new view.CollectionView({
			el: $("#FilterSensorTemplateContainer"),
			templateEl: $("#FilterSensorTemplate"),
			collection: page.alarm_Notificaties
		});
		
		
		// initialize the search filter
		$('#filter').change(function(obj) {
			page.fetchParams.filter = $('#filter').val();
			page.fetchParams.page = 1;
			page.fetchAlarm_Notificaties(page.fetchParams);
				
			
		});
		
	
 
 $(document).on('change','#alarmRegel',function(){
	 console.log($('#alarmRegel').val()+' selected');
	 $('#kanaalInputContainer').fadeIn( "fast" );

 });
	
	
	

		
		// initialize the Node search filter
		

		
		
		
		$(document).on('change','#FilterNode',function(){
			
			console.log("FilterNode Used");
			page.fetchParams.FilterNode = $('#FilterNode').val();
			page.fetchParams.page = 1;
			page.fetchAlarm_Notificaties(page.fetchParams);
			//console.log($('#FilterNode').val()+' used');
					console.log("FilterNode Used");
			
			if ($('#FilterNode').val()){
				$("#FilterNodeDisplay").val("Filter: "+$('#FilterNode').val());
				$("#FilterNodeDisplay").css('color', 'red', 'important');
			}
			else{
			//$("#FilterAliasDisplay").hide();	
				$("#FilterNodeDisplay").val("-Geen Filter-");	
				$("#FilterNodeDisplay").removeAttr('style');
			}
			
		});
		
		// initialize the Sensor search filter
		$(document).on('change','#FilterSensor',function(){
			page.fetchParams.FilterSensor = $('#FilterSensor').val();
			page.fetchParams.page = 1;
			page.fetchAlarm_Notificaties(page.fetchParams);
			
			
			if ($('#FilterSensor').val()){
				$("#FilterSensorDisplay").val("Filter: "+$('#FilterSensor').val());
				$("#FilterSensorDisplay").css('color', 'red', 'important');
		
			}
			else{
				$("#FilterSensorDisplay").val("-Geen Filter-");	
				$("#FilterSensorDisplay").removeAttr('style');
			}
			
		});
		
		
		
		// make the rows clickable ('rendered' is a custom event, not a standard backbone event)
		this.collectionView.on('rendered',function(){

			// attach click handler to the table rows for editing
			$('table.collection tbody tr').click(function(e) {
				e.preventDefault();
				var m = page.alarm_Notificaties.get(this.id);
				page.showDetailDialog(m);
			});

			// make the headers clickable for sorting
 			$('table.collection thead tr th').click(function(e) {
 				e.preventDefault();
				var prop = this.id.replace('header_','');

				// toggle the ascending/descending before we change the sort prop
				page.fetchParams.orderDesc = (prop == page.fetchParams.orderBy && !page.fetchParams.orderDesc) ? '1' : '';
				page.fetchParams.orderBy = prop;
				page.fetchParams.page = 1;
 				page.fetchAlarm_Notificaties(page.fetchParams);
 			});

			// attach click handlers to the pagination controls
			$('.pageButton').click(function(e) {
				e.preventDefault();
				page.fetchParams.page = this.id.substr(5);
				page.fetchAlarm_Notificaties(page.fetchParams);
			});
			
			page.isInitialized = true;
			page.isInitializing = false;
		});

		// backbone docs recommend bootstrapping data on initial page load, but we live by our own rules!
		this.fetchAlarm_Notificaties({ page: 1 });

		// initialize the model view
		this.modelView = new view.ModelView({
			el: $("#alarm_NotificatieModelContainer")
		});

		// tell the model view where it's template is located
		this.modelView.templateEl = $("#alarm_NotificatieModelTemplate");

		if (model.longPollDuration > 0)	{
			setInterval(function () {

				if (!page.dialogIsOpen)	{
					page.fetchAlarm_Notificaties(page.fetchParams,true);
					
					
					
					var now = new Date().toLocaleTimeString();
					console.log(now);
					$("#last-refresh").text('Refresh ' + now + ' (CET)');
					
					
					
					
					
					
				}

			}, model.longPollDuration);
		}
	},

	/**
	 * Fetch the collection data from the server
	 * @param object params passed through to collection.fetch
	 * @param bool true to hide the loading animation
	 */
	fetchAlarm_Notificaties: function(params, hideLoader) {
		// persist the params so that paging/sorting/filtering will play together nicely
		page.fetchParams = params;

		if (page.fetchInProgress) {
			if (console) console.log('supressing fetch because it is already in progress');
		}

		page.fetchInProgress = true;

		if (!hideLoader) app.showProgress('loader');

		page.alarm_Notificaties.fetch({

			data: params,

			success: function() {

				if (page.alarm_Notificaties.collectionHasChanged) {
					// TODO: add any logic necessary if the collection has changed
					// the sync event will trigger the view to re-render
				}

				app.hideProgress('loader');
				page.fetchInProgress = false;
			},

			error: function(m, r) {
				app.appendAlert(app.getErrorMessage(r), 'alert-error',0,'collectionAlert');
				app.hideProgress('loader');
				page.fetchInProgress = false;
			}

		});
	},

	/**
	 * show the dialog for editing a model
	 * @param model
	 */
	showDetailDialog: function(m) {

		// show the modal dialog
		$('#alarm_NotificatieDetailDialog').modal({ show: true });

		// if a model was specified then that means a user is editing an existing record
		// if not, then the user is creating a new record
		page.alarm_Notificatie = m ? m : new model.Alarm_NotificatieModel();

		page.modelView.model = page.alarm_Notificatie;

		if (page.alarm_Notificatie.id == null || page.alarm_Notificatie.id == '') {
			// this is a new record, there is no need to contact the server
			page.renderModelView(false);
		} else {
			app.showProgress('modelLoader');

			// fetch the model from the server so we are not updating stale data
			page.alarm_Notificatie.fetch({

				success: function() {
					// data returned from the server.  render the model view
					page.renderModelView(true);
				},

				error: function(m, r) {
					app.appendAlert(app.getErrorMessage(r), 'alert-error',0,'modelAlert');
					app.hideProgress('modelLoader');
				}

			});
		}

	},

	/**
	 * Render the model template in the popup
	 * @param bool show the delete button
	 */
	renderModelView: function(showDeleteButton)	{
		page.modelView.render();
		
		
	if ($('#kanaal').val()!=''){	

	console.log('kanaal niet leeg, alles zichtbaar');
	
		$('#p1InputContainer').show();
		$('#p2InputContainer').hide();
		$('#p3InputContainer').hide();
		$('#p4InputContainer').hide();
		$('#meldingtekstInputContainer').show();
		$('#kanaalInputContainer').show();
	
			
	}


else {
	
	console.log('kanaal wel leeg, niet alles zichtbaar');
	
	
		$('#p1InputContainer').hide();
		$('#p2InputContainer').hide();
		$('#p3InputContainer').hide();
		$('#p4InputContainer').hide();
		$('#meldingtekstInputContainer').hide();
		$('#kanaalInputContainer').hide();
	
	$(document).on('input','#kanaal',function(){
			
				//$('#p1InputContainer').hide();
				//$('#meldingtekstInputContainer').hide();
				//$("#p1").val("");	
			
				if ($('#kanaal').val()){
					console.log($('#kanaal').val()+' selected');
					$("#p1").val("");	
										
					
					if ($('#kanaal').val().toLowerCase()=='sms'){
													
						$('#p1InputContainer').show();
						$('#meldingtekstInputContainer').fadeIn( "fast" );
						
						$("label[for='p1']").text("Internationale Telefoonnummers");
															
						 $("#p1").val("0031612345678, ..");
						 console.log($('#kanaal').val()+' selected after input');
						
					}
					
					if ($('#kanaal').val().toLowerCase()=='slack'){
													
						$('#p1InputContainer').show();
						$('#meldingtekstInputContainer').fadeIn( "fast" );
						
						$("label[for='p1']").text("SlackChannel Naam");
															
						 $("#p1").val("..");
						 console.log($('#kanaal').val()+' selected after input');
						
					}
					
					if ($('#kanaal').val().toLowerCase()=='telegram'){
													
						$('#p1InputContainer').show();
						$('#meldingtekstInputContainer').fadeIn( "fast" );
						
						$("label[for='p1']").text("TelegramChannel Naam");
															
						 $("#p1").val("..");
						 console.log($('#kanaal').val()+' selected after input');
						
					}
					
					
				
				
				
				}
			
})
	
}

;
		
		
		
		
		
		
		app.hideProgress('modelLoader');

	
		
		
		// initialize any special controls
		try {
			$('.date-picker')
				.datepicker()
				.on('changeDate', function(ev){
					$('.date-picker').datepicker('hide');
				});
		} catch (error) {
			// this happens if the datepicker input.value isn't a valid date
			if (console) console.log('datepicker error: '+error.message);
		}
		
		$('.timepicker-default').timepicker({ defaultTime: 'value' });

		// populate the dropdown options for alarmRegel
		// TODO: load only the selected value, then fetch all options when the drop-down is clicked
		var alarmRegelValues = new model.Alarm_RegelCollection();
		alarmRegelValues.fetch({
			success: function(c){
				var dd = $('#alarmRegel');
				dd.append('<option value=""></option>');
				c.forEach(function(item,index) {
					dd.append(app.getOptionHtml(
						item.get('id'),
						(item.get('nodeAlias'))  + " --> " + (item.get('sensorOmschrijving'))  + " --> " + (item.get('alarmTrigger')),
						page.alarm_Notificatie.get('alarmRegel') == item.get('id')
						
										
						
					));
					
				});
				
				
				
				if (!app.browserSucks()) {
					dd.combobox();
					$('div.combobox-container + span.help-inline').hide(); // TODO: hack because combobox is making the inline help div have a height
				}

			},
			error: function(collection,response,scope) {
				app.appendAlert(app.getErrorMessage(response), 'alert-error',0,'modelAlert');
			}
		});


		if (showDeleteButton) {
			// attach click handlers to the delete buttons

			$('#deleteAlarm_NotificatieButton').click(function(e) {
				e.preventDefault();
				$('#confirmDeleteAlarm_NotificatieContainer').show('fast');
			});

			$('#cancelDeleteAlarm_NotificatieButton').click(function(e) {
				e.preventDefault();
				$('#confirmDeleteAlarm_NotificatieContainer').hide('fast');
			});

			$('#confirmDeleteAlarm_NotificatieButton').click(function(e) {
				e.preventDefault();
				page.deleteModel();
			});

		} else {
			// no point in initializing the click handlers if we don't show the button
			$('#deleteAlarm_NotificatieButtonContainer').hide();
		}
	},

	/**
	 * update the model that is currently displayed in the dialog
	 */
	updateModel: function() {
		// reset any previous errors
		$('#modelAlert').html('');
		$('.control-group').removeClass('error');
		$('.help-inline').html('');

		// if this is new then on success we need to add it to the collection
		var isNew = page.alarm_Notificatie.isNew();

		app.showProgress('modelLoader');

		page.alarm_Notificatie.save({

			'alarmRegel': $('select#alarmRegel').val(),
			'kanaal': $('input#kanaal').val(),
			'p1': $('input#p1').val(),
			'p2': $('input#p2').val(),
			'p3': $('input#p3').val(),
			'p4': $('input#p4').val(),
			'meldingtekst': $('input#meldingtekst').val()
		}, {
			wait: true,
			success: function(){
				$('#alarm_NotificatieDetailDialog').modal('hide');
				setTimeout("app.appendAlert('Alarm_Notificatie was sucessfully " + (isNew ? "inserted" : "updated") + "','alert-success',3000,'collectionAlert')",500);
				app.hideProgress('modelLoader');

				// if the collection was initally new then we need to add it to the collection now
				if (isNew) { page.alarm_Notificaties.add(page.alarm_Notificatie) }

				if (model.reloadCollectionOnModelUpdate) {
					// re-fetch and render the collection after the model has been updated
					page.fetchAlarm_Notificaties(page.fetchParams,true);
				}
		},
			error: function(model,response,scope){

				app.hideProgress('modelLoader');

				app.appendAlert(app.getErrorMessage(response), 'alert-error',0,'modelAlert');

				try {
					var json = $.parseJSON(response.responseText);

					if (json.errors) {
						$.each(json.errors, function(key, value) {
							$('#'+key+'InputContainer').addClass('error');
							$('#'+key+'InputContainer span.help-inline').html(value);
							$('#'+key+'InputContainer span.help-inline').show();
						});
					}
				} catch (e2) {
					if (console) console.log('error parsing server response: '+e2.message);
				}
			}
		});
	},

	/**
	 * delete the model that is currently displayed in the dialog
	 */
	deleteModel: function()	{
		// reset any previous errors
		$('#modelAlert').html('');

		app.showProgress('modelLoader');

		page.alarm_Notificatie.destroy({
			wait: true,
			success: function(){
				$('#alarm_NotificatieDetailDialog').modal('hide');
				setTimeout("app.appendAlert('The Alarm_Notificatie record was deleted','alert-success',3000,'collectionAlert')",500);
				app.hideProgress('modelLoader');

				if (model.reloadCollectionOnModelUpdate) {
					// re-fetch and render the collection after the model has been updated
					page.fetchAlarm_Notificaties(page.fetchParams,true);
				}
			},
			error: function(model,response,scope) {
				app.appendAlert(app.getErrorMessage(response), 'alert-error',0,'modelAlert');
				app.hideProgress('modelLoader');
			}
		});
	}
};

