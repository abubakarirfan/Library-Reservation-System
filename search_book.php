<?php
include 'database_connection.php';
include 'function.php';
include 'header.php';
?>

<ul class="list-inline mt-4" align="center">
	<li class="list-inline-item"><a href="issue_book_details.php">Current Booking</a></li>
	<li class="list-inline-item"><a href="search_book.php">Search Book</a></li>
	<li class="list-inline-item"><a href="logout.php">Logout</a></li>
</ul>

<div class="container-fluid py-4" style="min-height: 700px;">
	<h1>Search Book</h1>
	<div class="card mb-4">
		<div class="card-header">
			<div class="row">
				<div class="col col-md-6">
					<i class="fas fa-table me-1"></i> Book List
				</div>
				<div class="col col-md-6" align="right">
				</div>
			</div>
		</div>
		<div class="card-body">
			<table id="datatablesSimple">
				<thead>
					<tr>
						<th>Book Name</th>
						<th>ISBN No.</th>
						<th>Category</th>
						<th>Author</th>
						<th>No. of Available Copy</th>
						<th>Status</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>Book Name</th>
						<th>ISBN No.</th>
						<th>Category</th>
						<th>Author</th>
						<th>No. of Available Copy</th>
						<th>Status</th>
					</tr>
				</tfoot>
				<tbody>
					<?php

					$query = "
					SELECT * FROM lms_book;
				";

					$statement = $connect->prepare($query);
					$statement->execute();

					if ($statement->rowCount() > 0) {
						foreach ($statement->fetchAll() as $row) {
							$book_status = '';
							if ($row['book_no_of_copy'] > 0) {
								$book_status = '<div class="badge bg-success">Available</div>';
							} else {
								$book_status = '<div class="badge bg-danger">Not Available</div>';
							}
							echo '
							<tr>
								<td>' . xssSanitize($row["book_name"]) . '</td>
								<td>' . xssSanitize($row["book_isbn_number"]) . '</td>
								<td>' . xssSanitize($row["book_category"]) . '</td>
								<td>' . xssSanitize($row["book_author"]) . '</td>
								<td>' . xssSanitize($row["book_no_of_copy"]) . '</td>
								<td>' . $book_status . '</td>
							</tr>
						';
						}
					} else {
						echo '
					<tr>
						<td colspan="8" class="text-center">No Data Found</td>
					</tr>
					';
					}

					?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php

include 'footer.php';

?>