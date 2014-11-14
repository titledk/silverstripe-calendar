<ul class="CalendarPageMenu">
	<li class="calendarview <% if $CurrentMenu == 'calendarview'%>current<% end_if %>">
		<a href="{$CalendarViewLink}">Calendar View</a>
	</li>
	<li class="eventlist <% if $CurrentMenu == 'eventlist'%>current<% end_if %>">
		<a href="{$EventListLink}">List View</a>
	</li>
	<% if $RegistrationsEnabled %>
		<li class="registerableevents <% if $CurrentMenu == 'eventregistration'%>current<% end_if %>">
			<a href="{$Link}eventregistration/">Event Registration</a>
		</li>
	<% end_if %>
	<% if $SearchEnabled %>
		<li class="search">
			<form id="EventSearch" action="{$Link}search">
				<input type="text" name="q" value="$SearchQuery" />
				<input type="submit" value="search" />
			</form>
		</li>
	<% end_if %>
</ul>
