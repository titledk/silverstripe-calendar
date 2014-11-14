
<div class="CalendarDetails">
	<% with $CurrentCalendar %>
		<h2>$Title</h2>

		<h3>Subscription link:</h3>
		<span class="SubscriptionLink">
			{$BaseHref}ics/cal/{$getLink}
		</span>


	<% end_with %>

</div>