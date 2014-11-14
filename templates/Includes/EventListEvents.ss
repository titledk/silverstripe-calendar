<% loop $Events %>
	<div class="event $EvenOdd" data-id="$ID">
		<div class="feedBox">
			<% if $Calendar %>
				<% with $Calendar %>
					<a href="{$Top.Link}calendar/$Link/" class="feed-checkbox" original-title="Subscribe to this Calendar on your Home computer or Mobile Phone" style="background-color:$ColorWithHash;"></a>
				<% end_with %>
			<% end_if %>
		</div>
		<div class="fatDate">
			<span class="dayOfMonth">$StartDateTime.DayOfMonth</span>
			<span class="month">$StartDateTime.Format('F, Y')</span>
		</div>
		<div class="content">
			<h4><a href="$InternalLink" class="eventHeadline">$Title</a></h4>
			<div class="datesAndTimeframe">
				<% if StartAndEndDates %>
					<span>$StartAndEndDates</span>
				<% else %>
					<% if AllDay %>
						<span>All Day</span>
					<% else %>
						<span>$FormattedTimeframe</span>
					<% end_if %>
				<% end_if %>
			</div>
			<div class="details">$DetailsSummary</div>
			<% if $Registerable %>
				<% if not $IsPastEvent %>
					<a href="$InternalLink" class="doRegister">Register</a>
				<% else %>
					<span style="color:#813d00;width:80px;text-align:center;font-size:12px;">Registration now closed</span>
				<% end_if %>
				<a href="$InternalLink" class="readMore">Read More</a>
			<% else %>
				<a href="$InternalLink" class="readMore">Read More</a>
			<% end_if %>
		</div>
	</div>
<% end_loop %>

<% if $Events && $Events.NextLink %>
	<div class='pagination-link'>
		<a href="$Events.NextLink">More...</a>
	</div>
<% end_if %>

