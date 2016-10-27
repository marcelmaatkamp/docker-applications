/**
 * View logic for Alarm_Regels
 */

/**
 * application logic specific to the Alarm_Regel listing page
 */
var page = {

	alarm_Regels: new model.Alarm_RegelCollection(),
	collectionView: null,
	alarm_Regel: null,
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
		$("#newAlarm_RegelButton").click(function(e) {
			e.preventDefault();
			page.showDetailDialog();
		});

		// let the page know when the dialog is open
		$('#alarm_RegelDetailDialog').on('show',function() {
			page.dialogIsOpen = true;
		});

		// when the model dialog is closed, let page know and reset the model view
		$('#alarm_RegelDetailDialog').on('hidden',function() {
			$('#modelAlert').html('');
			page.dialogIsOpen = false;
		});

		// save the model when the save button is clicked
		$("#saveAlarm_RegelButton").click(function(e) {
			e.preventDefault();
			page.updateModel();
		});

		// initialize the collection view
		this.collectionView = new view.CollectionView({
			el: $("#alarm_RegelCollectionContainer"),
			templateEl: $("#alarm_RegelCollectionTemplate"),
			collection: page.alarm_Regels
		});

		
					// initialize the collection view
		this.collectionView = new view.CollectionView({
			el: $("#FilterNodeTemplateContainer"),
			templateEl: $("#FilterNodeTemplate"),
			collection: page.alarm_Regels
		});
		
		// initialize the collection view
		this.collectionView = new view.CollectionView({
			el: $("#FilterSensorTemplateContainer"),
			templateEl: $("#FilterSensorTemplate"),
			collection: page.alarm_Regels
		});
		

		// initialize the search filter
		$('#filter').change(function(obj) {
			page.fetchParams.filter = $('#filter').val();
			page.fetchParams.page = 1;
			page.fetchAlarm_Regels(page.fetchParams);
		});
		
		
		
		
		
		
		// initialize the Node search filter
		$(document).on('change','#FilterNode',function(){
			
			console.log("FilterNode Used");
			page.fetchParams.FilterNode = $('#FilterNode').val();
			page.fetchParams.page = 1;
			page.fetchAlarm_Regels(page.fetchParams);
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
			page.fetchAlarm_Regels(page.fetchParams);
			
			
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
				var m = page.alarm_Regels.get(this.id);
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
 				page.fetchAlarm_Regels(page.fetchParams);
 			});

			// attach click handlers to the pagination controls
			$('.pageButton').click(function(e) {
				e.preventDefault();
				page.fetchParams.page = this.id.substr(5);
				page.fetchAlarm_Regels(page.fetchParams);
			});
			
			page.isInitialized = true;
			page.isInitializing = false;
		});

		// backbone docs recommend bootstrapping data on initial page load, but we live by our own rules!
		this.fetchAlarm_Regels({ page: 1 });

		// initialize the model view
		this.modelView = new view.ModelView({
			el: $("#alarm_RegelModelContainer")
		});

		// tell the model view where it's template is located
		this.modelView.templateEl = $("#alarm_RegelModelTemplate");

		if (model.longPollDuration > 0)	{
			setInterval(function () {

				if (!page.dialogIsOpen)	{
					page.fetchAlarm_Regels(page.fetchParams,true);
				}

			}, model.longPollDuration);
		}
	},

	/**
	 * Fetch the collection data from the server
	 * @param object params passed through to collection.fetch
	 * @param bool true to hide the loading animation
	 */
	fetchAlarm_Regels: function(params, hideLoader) {
		// persist the params so that paging/sorting/filtering will play together nicely
		page.fetchParams = params;

		if (page.fetchInProgress) {
			if (console) console.log('supressing fetch because it is already in progress');
		}

		page.fetchInProgress = true;

		if (!hideLoader) app.showProgress('loader');

		page.alarm_Regels.fetch({

			data: params,

			success: function() {

				if (page.alarm_Regels.collectionHasChanged) {
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
		$('#alarm_RegelDetailDialog').modal({ show: true });

		// if a model was specified then that means a user is editing an existing record
		// if not, then the user is creating a new record
		page.alarm_Regel = m ? m : new model.Alarm_RegelModel();

		page.modelView.model = page.alarm_Regel;

		if (page.alarm_Regel.id == null || page.alarm_Regel.id == '') {
			// this is a new record, there is no need to contact the server
			page.renderModelView(false);
		} else {
			app.showProgress('modelLoader');

			// fetch the model from the server so we are not updating stale data
			page.alarm_Regel.fetch({

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

		// populate the dropdown options for node
		// TODO: load only the selected value, then fetch all options when the drop-down is clicked
		var nodeValues = new model.NodeCollection();
		nodeValues.fetch({
			success: function(c){
				var dd = $('#node');
				dd.append('<option value=""></option>');
				c.forEach(function(item,index) {
					dd.append(app.getOptionHtml(
						item.get('devEui'),
						item.get('alias'),
						page.alarm_Regel.get('node') == item.get('devEui')
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

		// populate the dropdown options for sensor
		// TODO: load only the selected value, then fetch all options when the drop-down is clicked
		var sensorValues = new model.SensorCollection();
		sensorValues.fetch({
			success: function(c){
				var dd = $('#sensor');
				dd.append('<option value=""></option>');
				c.forEach(function(item,index) {
					dd.append(app.getOptionHtml(
						item.get('sensorId'),
						item.get('omschrijving'),
						page.alarm_Regel.get('sensor') == item.get('sensorId')
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

			$('#deleteAlarm_RegelButton').click(function(e) {
				e.preventDefault();
				$('#confirmDeleteAlarm_RegelContainer').show('fast');
			});

			$('#cancelDeleteAlarm_RegelButton').click(function(e) {
				e.preventDefault();
				$('#confirmDeleteAlarm_RegelContainer').hide('fast');
			});

			$('#confirmDeleteAlarm_RegelButton').click(function(e) {
				e.preventDefault();
				page.deleteModel();
			});

		} else {
			// no point in initializing the click handlers if we don't show the button
			$('#deleteAlarm_RegelButtonContainer').hide();
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
		var isNew = page.alarm_Regel.isNew();

		app.showProgress('modelLoader');

		page.alarm_Regel.save({

			'node': $('select#node').val(),
			'sensor': $('select#sensor').val(),
			'alarmTrigger': $('input#alarmTrigger').val()
		}, {
			wait: true,
			success: function(){
				$('#alarm_RegelDetailDialog').modal('hide');
				setTimeout("app.appendAlert('Alarm_Regel was sucessfully " + (isNew ? "inserted" : "updated") + "','alert-success',3000,'collectionAlert')",500);
				app.hideProgress('modelLoader');

				// if the collection was initally new then we need to add it to the collection now
				if (isNew) { page.alarm_Regels.add(page.alarm_Regel) }

				if (model.reloadCollectionOnModelUpdate) {
					// re-fetch and render the collection after the model has been updated
					page.fetchAlarm_Regels(page.fetchParams,true);
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

		page.alarm_Regel.destroy({
			wait: true,
			success: function(){
				$('#alarm_RegelDetailDialog').modal('hide');
				setTimeout("app.appendAlert('The Alarm_Regel record was deleted','alert-success',3000,'collectionAlert')",500);
				app.hideProgress('modelLoader');

				if (model.reloadCollectionOnModelUpdate) {
					// re-fetch and render the collection after the model has been updated
					page.fetchAlarm_Regels(page.fetchParams,true);
				}
			},
			error: function(model,response,scope) {
				app.appendAlert(app.getErrorMessage(response), 'alert-error',0,'modelAlert');
				app.hideProgress('modelLoader');
			}
		});
	}
};

