/**
 * Event Fields JS Enhancements
 */

var EventFields = function(form) {
	var $this = this;

	//Date inputs
	var startDateInput = form.find('#StartDateTime input.date');
	var endDateInput = form.find('#EndDateTime input.date');


	//Time inputs
	var startTimeInput = form.find('#StartDateTime input.time');
	var endTimeInput = form.find('#EndDateTime input.time');
	var startEndTimeInputs = form.find('#StartDateTime input.time, #EndDateTime input.time');
	var durationTimeInput = form.find('#Duration input.time')


	this.init = function(){
		
		//initialize date pickers
		startDateInput.ssDatepicker();
		endDateInput.ssDatepicker();
		
		
		$this.init_timepicker();
		$this.init_alldaytoggling();
		$this.init_noendtoggling();
		$this.init_sanitychecks();
		
		if (form.find('#Title input').val().length == 0) {
			$this.addform_init();
		}
		
	}

	//Getters
	this.getStartDateInput = function(){
		return startDateInput;
	}
	this.getEndDateInput = function(){
		return endDateInput;
	}
	this.getStartTimeInput = function(){
		return startTimeInput;
	}
	this.getEndTimeInput = function(){
		return endTimeInput;
	}

	/**
	 * Initializing timepicker and time entry plugins
	 * http://jonthornton.github.com/jquery-timepicker/
	 * http://keith-wood.name/timeEntry.html
	 */
	this.init_timepicker = function(){

		startEndTimeInputs.timepicker({
			//'timeFormat': 'h:iA',
			'timeFormat': 'H:i',
			'minTime': '6:00',
			'maxTime': '11:30pm'
			//'scrollDefaultNow': true
		});
		
		startEndTimeInputs.timeEntry({
			spinnerImage: '',
			show24Hours: true, 
			//show24Hours: false, 
			timeSteps: [1, 5, 0]
		});

		durationTimeInput.timepicker({
			'timeFormat': 'H:i'
		});
		
		durationTimeInput.timeEntry({
			spinnerImage: '',
			show24Hours: true, 
			timeSteps: [1, 5, 0]
		});
	}
	/**
	 * Allday toggling initialization
	 * Only applicable if the allday checkbox exists - can be disabled through 
	 * the calendar config
	 */
	this.init_alldaytoggling = function() {
		var checkbox = form.find('#AllDay input.checkbox');
		if (checkbox.length) {
			if (checkbox.is(':checked')) {
				$this.allday_toggle(checkbox, 'hide');
				form.find('#Title input').focus();
			}
			checkbox.click(function() {
				$this.allday_toggle(checkbox, 'fade');
			});
		}
	}
	/**
	 * Allday toggle
	 * Run once on init - if the event is allday, and once every time the all day
	 * checkbox is toggled
	 */
	this.allday_toggle = function(checkbox, effect){
		var durationLi = form.find('#Duration').closest('li');
		
		if (checkbox.is(':checked')) {
			//selecting "DateTime" option
			form.find('#EndDateTime').closest('li').find('.selector').trigger('click');
			//hiding "Duration" option

			$this.do_toggle(durationLi,'hide', effect);
			$this.do_toggle(startEndTimeInputs,'hide', effect);
			
			//Set end date = start date if no end date has been set yet
			if (endDateInput.val().length == 0) {
				endDateInput.datepicker('setDate', startDateInput.datepicker('getDate'));
			}			
			
		} else {
			$this.do_toggle(durationLi,'show', effect);
			$this.do_toggle(startEndTimeInputs,'show', effect);
		}
	}
	
	/**
	 * If no end has been set, this triggers the NoEnd checkbox to appear
	 * Note that this only applies if end dates are not enforced
	 */
	this.init_noendtoggling = function() {
		var checkbox = form.find('#NoEnd input.checkbox');
		if (checkbox.length) {
			if (checkbox.is(':checked')) {
				//initial toggle
				$this.noend_toggle(checkbox, 'hide');
			}
			checkbox.click(function() {
				
				//in case allday is clicked make sure to unclick it
				var alldayCheckbox = form.find('#AllDay input.checkbox');
				if (alldayCheckbox.length && alldayCheckbox.is(':checked')) {
					//console.log('allday is checked');
					//alldayCheckbox.trigger('click');
					
					//$this.allday_toggle(checkbox, 'show');
					
					//$this.allday_toggle(checkbox, 'fade');
					
					alldayCheckbox.attr('checked', false);
					$this.do_toggle(form.find('#Duration').closest('li'),'show', 'show');
					$this.do_toggle(startEndTimeInputs,'show', 'show');
				}
				
				$this.noend_toggle(checkbox, 'fade');
				
			});
		}
	}	
	/**
	 * Noend toggle
	 * Run once on init - if the noend checkbox exists and is selected on init - and once every time the
	 * checkbox is toggled
	 */
	this.noend_toggle = function(checkbox, effect){
		var endInput = form.find('.SelectionGroup, #Form_ItemEditForm_TimeFrameHeader, #AllDay');
		
		if (checkbox.is(':checked')) {
			$this.do_toggle(endInput,'hide', effect);
		} else {
			$this.do_toggle(endInput,'show', effect);
		}
	}
	
	/**
	 * Toggle helper
	 * beyond toggling it takes care to properly attaching hidden attributes
	 * to items that have a hidden parent - to allow for allday toggle and noend toggle to work in conjunction
	 */
	this.do_toggle = function(item, action, effect) {
		
		if (action == 'hide') {
			if (effect == 'fade') {
				item.fadeOut(function(){
					//make sure that the hidden attribute is actually set
					item.css('display','none');
				});
			} else {
				item.hide();
			}
		} else {
			if (effect == 'fade') {
				item.fadeIn(function(){
					//item.css('display','block');
				});
			} else {
				item.show();
			}
		}
		
	}
	
	
	this.init_sanitychecks = function(){
		
		startDateInput.change(function(){
			var startDate = startDateInput.datepicker('getDate');
			
			//Set end date = start date if no end date has been set yet
			if (endDateInput.val().length == 0) {
				endDateInput.datepicker('setDate', startDate);
			}
		});
		
	}	
	
	
	this.addform_init = function(){
		//trigger duration time frame on first add
		form.find('#Duration').closest('li').find('.selector').trigger('click');
		form.find('#Title input').focus();
	}
	
}