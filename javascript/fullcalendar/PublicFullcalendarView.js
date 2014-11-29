var PublicFullcalendarView;

(function($) {
	PublicFullcalendarView = function(holder, calendarUrl, options) {
		var $this = this;
		$this.holder = holder;
		$this.calendarUrl = calendarUrl;

		//default options
		//Note that these are mostly for illustration purposes,
		//and will be overwritten by CalendarConfig, and in turn by each site's individual config
		$this.options = {
			controllerUrl: '/fullcalendar/',
			fullcalendar: {
				header: {
					left: 'prev, next',
					//center: 'title'
					right: 'title'
				},
				shadedevents: false
			}
		}


		$this.controllerUrl = $this.options.controllerUrl;
		$this.eventSources = null; //will be initialized
		$this.shadedEvents = null; //will be initialized if shaded events are enabled

		//can be set to true to prevent things like the edit event modal box appearing twice
		$this.loading = false;

		this.init = function(){
			//extending options
			$this.options = $.extend( {}, $this.options, options );

			$this.init_eventsources();

			//If shaded events are enabled, these are found via AJAX, and saved in a variable
			if ($this.options.shadedevents) {
				$.post($this.buildControllerUrl('shadedevents'), {
				}, function(shadedEvents){

					$this.shadedEvents = shadedEvents;
					$this.init_calendar();
					$this.init_customnav();
				});
			} else {
				$this.init_calendar();
				$this.init_customnav();
			}
		}

		/**
		 * Converting date to string - for AJAX calls
		 */
		this.dateToString = function(date, pattern) {
			if (!pattern) {
				pattern = 'yyyy-MM-dd HH:mm';
			}
			var xDate = new XDate(date);
			var dateStr = xDate.toString(pattern);
			return dateStr;
		}

		/*
		 * Building url for a specific action
		 */
		this.buildControllerUrl = function(action) {
			return $this.controllerUrl + action + '/';
		}
		this.buildCalendarUrl = function(action) {
			return $this.calendarUrl + action + '/';
		}

		/**
		 * Event source initialization
		 * For now we're only getting public events,
		 * but this can be extended to also support private events
		 */
		this.init_eventsources = function(){
			$this.eventSources = 	[
				//public events
				{
					url: $this.buildControllerUrl('publicevents'),
					type: 'POST',
					error: function() {
					},
					editable: false
				}
			];
		}


		this.init_calendar = function(){
			var date = new Date();
			var d = date.getDate();
			var m = date.getMonth();
			var y = date.getFullYear();

			holder.fullCalendar({
				header: $this.options.fullcalendar.header,
				weekMode: 'variable',
				columnFormat: {
						month: 'ddd',    // Mon
						week: 'ddd d/M', // Mon 9/7
						day: 'dddd d/M'  // Monday 9/7
				},
				firstDay: 1, //Start week on monday
				//time formatting - see more here: http://arshaw.com/fullcalendar/docs/text/timeFormat/
				timeFormat: {
					// for agendaWeek and agendaDay
					agenda: 'h:mm{ - h:mm}', // 5:00 - 6:30
					// for all other views
					'': 'h(:mm)tt'            // 7pm
				},
				dayRender: function(date, cell) {
					$this.dayRender(date, cell);
				},
				eventRender: function (event, element) {
				},
				eventClick: function(calEvent, jsEvent, view) {
					location.href = $this.buildCalendarUrl('detail') + calEvent.id;
				},
				eventSources: $this.eventSources
			});
		}


		/**
		 * Day Render (private method - only to be called from within fullcalendar init)
		 */
		this.dayRender = function(date, cell) {

			if ($this.shadedEvents) {
				$this.dayRender_shadedevents(date, cell);
			}
		}

		/**
		 * Rendering shaded events (private method - only to be called from within fullcalendar init)
		 */
		this.dayRender_shadedevents = function(date, cell) {

			//SETTING BACKGROUND SHADING
			var shading = null;

			var xDate = new XDate(date);
			var time = xDate.getTime();

			//Weekends
			if (xDate.getDay() == 6 || xDate.getDay() == 0) {
				//shading = '#fffff1';
				shading = '#FFFFE5';
			}


			//NOTE:
			//we loop through all shaded event each time a day is rendered
			//this could cause some heave calculation on the frontend, and there might
			//be a more elegant way of doing this
			$($this.shadedEvents).each(function() {
				//console.log(this);
				var start = new XDate(new XDate(this.start).toString('yyyy-MM-dd')).getTime();
				var end = new XDate(new XDate(this.end).toString('yyyy-MM-dd')).getTime();
				//console.log(start);
				//console.log(end);

				if((time >= start) && (time <= end)) {
					shading = this.backgroundColor;
					//console.log(new XDate(this.start).toString('yyyy-MM-dd'));
				}
			});

			//today
			//console.log(time - new XDate().getTime());
			if (xDate.toDateString() == new XDate().toDateString()) {
				shading = '#FFFFCC';
			}

			//coloring the background with the shading color
			if (shading) {
				cell.css('background-color', shading);
			}
		}



		this.set_customnavtitle = function(){
			var nav = $('#FullcalendarCustomNav');
			var view = holder.fullCalendar('getView');
			nav.find('.title').html(view.title);
		}

		/**
		 * Initializing the custom navigation
		 * //TODO this should be configurable - we don't always need a custom nav
		 */
		this.init_customnav = function(){
			var nav = $('#FullcalendarCustomNav');
			var cal = holder;

			//set the initial title
			$this.set_customnavtitle();

			nav.find('.today').click(function() {
				cal.fullCalendar('today');
				$this.set_customnavtitle();
				return false;
			});
			nav.find('.month').click(function() {
				cal.fullCalendar('changeView','month');
				nav.find('.date-tabs a').removeClass('current');
				$(this).addClass('current');
				$this.set_customnavtitle();
				return false;
			});
			nav.find('.week').click(function() {
				cal.fullCalendar('changeView','agendaWeek');
				nav.find('.date-tabs a').removeClass('current');
				$(this).addClass('current');
				$this.set_customnavtitle();
				return false;
			});
			nav.find('.day').click(function() {
				cal.fullCalendar('changeView','agendaDay');
				nav.find('.date-tabs a').removeClass('current');
				$(this).addClass('current');
				$this.set_customnavtitle();
				return false;
			});
			nav.find('.prev').click(function() {
				cal.fullCalendar('prev');
				$this.set_customnavtitle();
				return false;
			});
			nav.find('.next').click(function() {
				cal.fullCalendar('next');
				$this.set_customnavtitle();
				return false;
			});
		}


		//Automatic initialization on construction
		$this.init();


	}
})(jQuery);