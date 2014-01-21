		</div> <!-- end of container -->


			<header>
				<img src="img/logo.png" width=401 height=60 alt="MyEOffering" />
				
				<nav>
					<ul>
						<li id="nav-home" class="<?php if ($this->view == Views::HOME) echo "nav-selected"; ?>">
							<a href="/">Home</a>
						</li>
						<li id="nav-settings" class="<?php if ($this->view == Views::SETTINGS) echo "nav-selected"; ?>">
							<a href="/settings">Settings</a>
						</li>	
						<!-- only for admins -->
						<li id="nav-users" class="<?php if ($this->view == Views::USERS) echo "nav-selected"; ?>">
							<a href="/users">Manage Users</a>
						</li>
						<li id="nav-reports" class="<?php if ($this->view == Views::REPORTS) echo "nav-selected"; ?>">
							<a href="/reports">Run Reports</a>
						</li>
						<li id="nav-donate" class="<?php if ($this->view == Views::DONATE) echo "nav-selected"; ?>">
							<!-- img button -->
							<a href="/donate">Donate Now</a>
						</li>						
					</ul>
				</nav>
			</header>
			

			<footer>
				<p>
					&copy; Copyright 2014 MyEOffering
				</p>
			</footer>
		</div>
	</body>
</html>