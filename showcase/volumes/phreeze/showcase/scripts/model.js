/**
 * backbone model definitions for SHOWCASE
 */

/**
 * Use emulated HTTP if the server doesn't support PUT/DELETE or application/json requests
 */
Backbone.emulateHTTP = false;
Backbone.emulateJSON = false;

var model = {};

/**
 * long polling duration in miliseconds.  (5000 = recommended, 0 = disabled)
 * warning: setting this to a low number will increase server load
 */
model.longPollDuration = 5000;

/**
 * whether to refresh the collection immediately after a model is updated
 */
model.reloadCollectionOnModelUpdate = true;


/**
 * a default sort method for sorting collection items.  this will sort the collection
 * based on the orderBy and orderDesc property that was used on the last fetch call
 * to the server. 
 */
model.AbstractCollection = Backbone.Collection.extend({
	totalResults: 0,
	totalPages: 0,
	currentPage: 0,
	pageSize: 0,
	orderBy: '',
	orderDesc: false,
	lastResponseText: null,
	lastRequestParams: null,
	collectionHasChanged: true,
	
	/**
	 * fetch the collection from the server using the same options and 
	 * parameters as the previous fetch
	 */
	refetch: function() {
		this.fetch({ data: this.lastRequestParams })
	},
	
	/* uncomment to debug fetch event triggers
	fetch: function(options) {
            this.constructor.__super__.fetch.apply(this, arguments);
	},
	// */
	
	/**
	 * client-side sorting baesd on the orderBy and orderDesc parameters that
	 * were used to fetch the data from the server.  Backbone ignores the
	 * order of records coming from the server so we have to sort them ourselves
	 */
	comparator: function(a,b) {
		
		var result = 0;
		var options = this.lastRequestParams;
		
		if (options && options.orderBy) {
			
			// lcase the first letter of the property name
			var propName = options.orderBy.charAt(0).toLowerCase() + options.orderBy.slice(1);
			var aVal = a.get(propName);
			var bVal = b.get(propName);
			
			if (isNaN(aVal) || isNaN(bVal)) {
				// treat comparison as case-insensitive strings
				aVal = aVal ? aVal.toLowerCase() : '';
				bVal = bVal ? bVal.toLowerCase() : '';
			} else {
				// treat comparision as a number
				aVal = Number(aVal);
				bVal = Number(bVal);
			}
			
			if (aVal < bVal) {
				result = options.orderDesc ? 1 : -1;
			} else if (aVal > bVal) {
				result = options.orderDesc ? -1 : 1;
			}
		}
		
		return result;

	},
	/**
	 * override parse to track changes and handle pagination
	 * if the server call has returned page data
	 */
	parse: function(response, options) {

		// the response is already decoded into object form, but it's easier to
		// compary the stringified version.  some earlier versions of backbone did
		// not include the raw response so there is some legacy support here
		var responseText = options && options.xhr ? options.xhr.responseText : JSON.stringify(response);
		this.collectionHasChanged = (this.lastResponseText != responseText);
		this.lastRequestParams = options ? options.data : undefined;
		
		// if the collection has changed then we need to force a re-sort because backbone will
		// only resort the data if a property in the model has changed
		if (this.lastResponseText && this.collectionHasChanged) this.sort({ silent:true });
		
		this.lastResponseText = responseText;
		
		var rows;

		if (response.currentPage) {
			rows = response.rows;
			this.totalResults = response.totalResults;
			this.totalPages = response.totalPages;
			this.currentPage = response.currentPage;
			this.pageSize = response.pageSize;
			this.orderBy = response.orderBy;
			this.orderDesc = response.orderDesc;
		} else {
			rows = response;
			this.totalResults = rows.length;
			this.totalPages = 1;
			this.currentPage = 1;
			this.pageSize = this.totalResults;
			this.orderBy = response.orderBy;
			this.orderDesc = response.orderDesc;
		}

		return rows;
	}
});

/**
 * Alarm Backbone Model
 */
model.AlarmModel = Backbone.Model.extend({
	urlRoot: 'api/alarm',
	idAttribute: 'id',
	id: '',
	alarmRegel: '',
	observatie: '',
	defaults: {
		'id': null,
		'alarmRegel': '',
		'observatie': ''
	}
});

/**
 * Alarm Backbone Collection
 */
model.AlarmCollection = model.AbstractCollection.extend({
	url: 'api/alarms',
	model: model.AlarmModel
});

/**
 * Alarm_Notificatie Backbone Model
 */
model.Alarm_NotificatieModel = Backbone.Model.extend({
	urlRoot: 'api/alarm_notificatie',
	idAttribute: 'id',
	id: '',
	alarmRegel: '',
	kanaal: '',
	p1: '',
	p2: '',
	p3: '',
	p4: '',
	meldingtekst: '',
	defaults: {
		'id': null,
		'alarmRegel': '',
		'kanaal': '',
		'p1': '',
		'p2': '',
		'p3': '',
		'p4': '',
		'meldingtekst': ''
	}
});

/**
 * Alarm_Notificatie Backbone Collection
 */
model.Alarm_NotificatieCollection = model.AbstractCollection.extend({
	url: 'api/alarm_notificaties',
	model: model.Alarm_NotificatieModel
});

/**
 * Alarm_Regel Backbone Model
 */
model.Alarm_RegelModel = Backbone.Model.extend({
	urlRoot: 'api/alarm_regel',
	idAttribute: 'id',
	id: '',
	node: '',
	sensor: '',
	alarmTrigger: '',
	defaults: {
		'id': null,
		'node': '',
		'sensor': '',
		'alarmTrigger': ''
	}
});

/**
 * Alarm_Regel Backbone Collection
 */
model.Alarm_RegelCollection = model.AbstractCollection.extend({
	url: 'api/alarm_regels',
	model: model.Alarm_RegelModel
});

/**
 * Alarm Backbone Model
 */
model.AlarmModel = Backbone.Model.extend({
	urlRoot: 'api/alarm',
	idAttribute: 'id',
	id: '',
	node: '',
	sensor: '',
	alarmtrigger: '',
	observatiewaarde: '',
	observatietijdstip: '',
	defaults: {
		'id': null,
		'node': '',
		'sensor': '',
		'alarmtrigger': '',
		'observatiewaarde': '',
		'observatietijdstip': new Date()
	}
});

/**
 * Alarm Backbone Collection
 */
model.AlarmCollection = model.AbstractCollection.extend({
	url: 'api/alarmen',
	model: model.AlarmModel
});

/**
 * Laatste_Observatie Backbone Model
 */
model.Laatste_ObservatieModel = Backbone.Model.extend({
	urlRoot: 'api/laatste_observatie',
	idAttribute: 'observatieid',
	observatieid: '',
	node: '',
	sensor: '',
	observatiewaarde: '',
	observatiedatum: '',
	defaults: {
		'observatieid': null,
		'node': '',
		'sensor': '',
		'observatiewaarde': '',
		'observatiedatum': new Date()
	}
});

/**
 * Laatste_Observatie Backbone Collection
 */
model.Laatste_ObservatieCollection = model.AbstractCollection.extend({
	url: 'api/laatste_observaties',
	model: model.Laatste_ObservatieModel
});

/**
 * Node Backbone Model
 */
model.NodeModel = Backbone.Model.extend({
	urlRoot: 'api/node',
	idAttribute: 'devEui',
	devEui: '',
	omschrijving: '',
	defaults: {
		'devEui': null,
		'omschrijving': ''
	}
});

/**
 * Node Backbone Collection
 */
model.NodeCollection = model.AbstractCollection.extend({
	url: 'api/nodes',
	model: model.NodeModel
});

/**
 * Observatie Backbone Model
 */
model.ObservatieModel = Backbone.Model.extend({
	urlRoot: 'api/observatie',
	idAttribute: 'id',
	id: '',
	node: '',
	sensor: '',
	datumTijdAangemaakt: '',
	waarde: '',
	defaults: {
		'id': null,
		'node': '',
		'sensor': '',
		'datumTijdAangemaakt': new Date(),
		'waarde': ''
	}
});

/**
 * Observatie Backbone Collection
 */
model.ObservatieCollection = model.AbstractCollection.extend({
	url: 'api/observaties',
	model: model.ObservatieModel
});

/**
 * Sensor Backbone Model
 */
model.SensorModel = Backbone.Model.extend({
	urlRoot: 'api/sensor',
	idAttribute: 'sensorId',
	sensorId: '',
	omschrijving: '',
	defaults: {
		'sensorId': null,
		'omschrijving': ''
	}
});

/**
 * Sensor Backbone Collection
 */
model.SensorCollection = model.AbstractCollection.extend({
	url: 'api/sensoren',
	model: model.SensorModel
});

/**
 * SensorNodeObservation Backbone Model
 */
model.SensorNodeObservationModel = Backbone.Model.extend({
	urlRoot: 'api/sensornodeobservation',
	idAttribute: 'node',
	node: '',
	sensor: '',
	observatieid: '',
	observatiewaarde: '',
	observatiedatum: '',
	defaults: {
		'node': null,
		'sensor': '',
		'observatieid': '',
		'observatiewaarde': '',
		'observatiedatum': new Date()
	}
});

/**
 * SensorNodeObservation Backbone Collection
 */
model.SensorNodeObservationCollection = model.AbstractCollection.extend({
	url: 'api/sensornodeobservations',
	model: model.SensorNodeObservationModel
});

