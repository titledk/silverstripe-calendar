
<% include CalendarPageMenu %>

<div class="EventSearch">



	You've searched for "<strong>$SearchQuery</strong>".


	<div class="Events">
		<% if $Events %>
			<h2>Results</h2>
			<% include EventListEvents %>
		<% else %>
			<em>We didn't find any results.</em>
		<% end_if %>		
	</div>



</div>