<div id="calendaradmin-cms-content" class="cms-content center cms-tabset $BaseCSSClasses" data-layout-type="border" data-pjax-fragment="Content">

	<!-- 	<div class="cms-content-header north">
		<% with EditForm %>
			<div class="cms-content-header-info">
				<% include BackLink_Button %>
		<% with Controller %>
			<% include CMSBreadcrumbs %>
		<% end_with %>
			</div>
		<% end_with %>
	</div> -->


	<div class="cms-content-header north">
		<div class="cms-content-header-info">
			<h2>
				<% include CMSSectionIcon %>
				<% if SectionTitle %>
					$SectionTitle:
				<% else %>
				<% end_if %>

								$SubTitle
			</h2>
		</div>



		<!--
		<div class="cms-content-header-tabs cms-tabset-nav-primary ss-ui-tabs-nav">
			<ul>
				<li class="tab-$ClassName $LinkOrCurrent<% if $Action == 'index' %> ui-tabs-active<% end_if %>">
					<a href="$Link" class="cms-panel-link">Coming Events</a>
				</li>
				<li class="tab-$ClassName $LinkOrCurrent<% if $Action == 'pastevents' %> ui-tabs-active<% end_if %>">
					<a href="{$Link}pastevents/" class="cms-panel-link">Past Events</a>
				</li>
			</ul>
		</div>
		-->

	</div>

	<div class="cms-content-fields center ui-widget-content" data-layout-type="border">
			$Tools

			<% if $Action == 'index' %>
				$ComingEventsForm
			<% end_if %>
			<% if $Action == 'pastevents' %>
				$PastEventsForm
			<% end_if %>
			<% if $Action == 'calendars' %>
				$CalendarsForm
			<% end_if %>
			<% if $Action == 'categories' %>
				$CategoriesForm
			<% end_if %>


	</div>


	<!--
		<div class="cms-content-footer south ui-widget-content" data-layout-type="border">
			Footer
		</div>
	 -->

</div>
