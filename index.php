	<?php
		include 'database_connection.php';
		include 'function.php';
		include 'header.php';
	?>

	<div class="p-5 mb-4 bg-light rounded-3">
		<div class="container-fluid py-5">
			<h1 class="display-5 fw-bold">Swinburne Library Management System</h1>
			<p class="fs-4">Welcome to Swinburne library, where you can find and access the resources you need for study, teaching or research. Search the library to access all the online resources you need.</p>
		</div>

	</div>

	<div class="row align-items-md-stretch">
		<div class="col-md-6">
			<div class="h-100 p-5 text-white bg-dark rounded-3">
				<h2>Librarian</h2>
				<p></p>
				<a href="admin_login.php" class="btn btn-outline-light">Librarian Login</a>
			</div>

		</div>

	<div class="col-md-6">
		<div class="h-100 p-5 bg-red border rounded-3">
			<h2>Student</h2>
				<p></p>
				<a href="user_login.php" class="btn btn-outline-secondary">Student Login</a>
		</div>

	</div>
</div>

<?php
	include 'footer.php';
?>