<div class="cms-content-tools west cms-panel cms-panel-layout" id="cms-content-tools-CalendarAdmin" data-expandOnClick="true" data-layout-type="border">
	<div class="cms-panel-content center">
		<h3 class="cms-panel-header">Options</h3>
	
		<% if $Action != "index" %>
			<div style="margin-bottom:5px;">
				<a href="$Link" class="ss-ui-button ss-ui-action-constructive cms-panel-link ui-corner-all">Coming Events</a>	
			</div>
		<% end_if %>
		<% if $Action != "pastevents" %>
			<div style="margin-bottom:5px;">
				<a href="{$Link}pastevents/" class="ss-ui-button ss-ui-action-constructive cms-panel-link ui-corner-all">Past Events</a>
			</div>
		<% end_if %>
		<% if $CalendarsEnabled %>
			<% if $Action != "calendars" %>
				<div style="margin-bottom:5px;">
					<a href="{$Link}calendars/" class="ss-ui-button ss-ui-action-constructive cms-panel-link ui-corner-all">Calendars</a>
				</div>
			<% end_if %>
		<% end_if %>
		<% if $CategoriesEnabled %>
			<% if $Action != "categories" %>
				<div style="margin-bottom:5px;">
					<a href="{$Link}categories/" class="ss-ui-button ss-ui-action-constructive cms-panel-link ui-corner-all">Categories</a>
				</div>
			<% end_if %>
		<% end_if %>

	</div>
	<div class="cms-panel-content-collapsed">
		<h3 class="cms-panel-header">Calendar Menu</h3>
	</div>
</div>
