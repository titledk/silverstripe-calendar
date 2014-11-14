

<div class="CalendarKeys">

	<h3>Events Calendar Key</h3>
	<ul class="CalendarKeys">
		<% loop $AllCalendars %>
			<li>
				<a class="feed-checkbox $CssClass subjectname-tooltip subscription-button" original-title="Subscribe to this Calendar on your Home computer or Mobile Phone" style="background-color:$ColorWithHash;"></a>
				$Title
				<br />
				<a href="{$Top.Link}calendar/$Link/">subscription options</a>

			</li>
		<% end_loop %>
	</ul>
</div>