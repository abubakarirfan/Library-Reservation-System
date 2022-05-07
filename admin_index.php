<?php

//index.php

include 'database_connection.php';

include 'function.php';

if (!is_admin_login()) {
	header('location:admin_login.php');
}

include 'header.php';

?>

<div class="nav">
	<a class="nav-link" href="book.php">Books</a>
	<a class="nav-link" href="admin_issue_book.php">Manage Reservations</a>
	<a class="nav-link" href="logout.php">Logout</a>
</div>


<div class="p-5 mb-4 bg-light rounded">
	<h1 class="mb-5">Dashboard</h1>
	<div class="row">
		<div class="col-xl-3 col-md-6">
			<div class="card bg-success text-white mb-4">
				<div class="card-body">
					<h1 class="text-center"><?php echo Count_total_book_number($connect); ?></h1>
					<h5 class="text-center">Total Books</h5>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-md-6">
			<div class="card bg-primary text-white mb-4">
				<div class="card-body">
					<h1 class="text-center"><?php echo Count_total_issue_book_number($connect); ?></h1>
					<h5 class="text-center">Total Books Issued</h5>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-md-6">
			<div class="card bg-warning text-white mb-4">
				<div class="card-body">
					<h1 class="text-center"><?php echo Count_total_returned_book_number($connect); ?></h1>
					<h5 class="text-center">Total Books Returned</h5>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-md-6">
			<div class="card bg-danger text-white mb-4">
				<div class="card-body">
					<h1 class="text-center"><?php echo Count_total_not_returned_book_number($connect); ?></h1>
					<h5 class="text-center">Total Books Not Returned</h5>
				</div>
			</div>
		</div>
	</div>
</div>

<?php

include 'footer.php';

?>