

<% with $Event %>
	<div class="EventRegistration">
		<% if not $IsPastEvent %>
			<h3>
				<span class="second">Register for: "$Title <% if $PaymentRequired %>($Cost.Nice)<% end_if %>"</span>
			</h3>
			<% if $TicketsRequired %>
				$RegistrationPaymentForm
			<% else %>
				$RegistrationForm
			<% end_if %>
		<% end_if %>
	</div>
<% end_with %>