<!-- start: sidebar -->
<aside id="sidebar-left" class="sidebar-left">

	<div class="sidebar-header">
		<div class="sidebar-title">
			Navigation
		</div>
		<div class="sidebar-toggle hidden-xs" data-toggle-class="sidebar-left-collapsed" data-target="html" data-fire-event="sidebar-left-toggle">
			<i class="fa fa-bars" aria-label="Toggle sidebar"></i>
		</div>
	</div>

	<div class="nano">
		<div class="nano-content">
			<nav id="menu" class="nav-main" role="navigation">
				<ul class="nav nav-main">
					<li>
						<a href="index-2.html">
							<i class="fa fa-home" aria-hidden="true"></i>
							<span>Dashboard</span>
						</a>
					</li>
					<li class="nav-parent">
						<a>
							<i class="fa fa-columns" aria-hidden="true"></i>
							<span>Layouts</span>
						</a>
						<ul class="nav nav-children">
							<li>
								<a href="layouts-default.html">
									 Default
								</a>
							</li>
						</ul>
					</li>
					<li>
						<a href="mailbox-folder.html">
							<span class="pull-right label label-primary">182</span>
							<i class="fa fa-envelope" aria-hidden="true"></i>
							<span>Mailbox</span>
						</a>
					</li>
				</ul>
			</nav>

			<hr class="separator" />

			<div class="sidebar-widget widget-tasks">
				<div class="widget-header">
					<h6>Projects</h6>
					<div class="widget-toggle">+</div>
				</div>
				<div class="widget-content">
					<ul class="list-unstyled m-none">
						<li><a href="#">Porto HTML5 Template</a></li>
						<li><a href="#">Tucson Template</a></li>
						<li><a href="#">Porto Admin</a></li>
					</ul>
				</div>
			</div>
		</div>

		<script>
			// Maintain Scroll Position
			if (typeof localStorage !== 'undefined') {
				if (localStorage.getItem('sidebar-left-position') !== null) {
					var initialPosition = localStorage.getItem('sidebar-left-position'),
						sidebarLeft = document.querySelector('#sidebar-left .nano-content');
					
					sidebarLeft.scrollTop = initialPosition;
				}
			}
		</script>

	</div>

</aside>
<!-- end: sidebar -->