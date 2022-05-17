<?php
include 'database_connection.php';
include 'function.php';

if (!is_user_login()) {
	header('location:user_login.php');
}

$query = "
	SELECT * FROM lms_issue_book 
	INNER JOIN lms_book 
	ON lms_book.book_isbn_number = lms_issue_book.book_id 
	WHERE lms_issue_book.user_id = '" . $_SESSION['user_id'] . "' 
	ORDER BY lms_issue_book.issue_book_id DESC
";

$statement = $connect->prepare($query);

$statement->execute();

$fetchedRows = $statement->fetchAll();

$errorMessage = array();

foreach ($fetchedRows as $rowIn) {
    if ($rowIn['book_issue_status'] == 'Not Return') {
        $errorMessage[] = $rowIn['book_name'] . ' is overdue by ' . countDaysSince($rowIn['expected_return_date']) . ' days(s)';
    }
}

include 'header.php';
?>

<ul class="list-inline mt-4" align="center">
	<li class="list-inline-item"><a href="issue_book_details.php">Current Booking</a></li>
	<li class="list-inline-item"><a href="catalog.php">Catalog</a></li>
    <li class="list-inline-item"><a href="search_form.php">Search</a></li>
	<li class="list-inline-item"><a href="logout.php">Logout</a></li>
</ul>

<div class="container-fluid py-4" style="min-height: 700px;">

	<h1>Issue Book Detail</h1>
	<div class="card mb-4">
		<div class="card-header">
			<div class="row">
				<div class="col col-md-6">
					<i class="fas fa-table me-1"></i> Issue Book Detail
				</div>
				<div class="col col-md-6" align="right">
				</div>
			</div>
		</div>
		<div class="card-body">
            <?php if (sizeof($errorMessage) > 0) : ?>
            <div class="alert alert-danger">
            <?php foreach ($errorMessage as $val) : ?>
            <ul><?= $val ?></ul>
            <?php endforeach; ?>
            </div>
            <?php endif; ?>
			<table id="datatablesSimple">
				<thead>
					<tr>
						<th>Book ISBN No.</th>
						<th>Book Name</th>
						<th>Reservation Date</th>
						<th>Return Date</th>
						<th>Status</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>Book ISBN No.</th>
						<th>Book Name</th>
						<th>Reservation Date</th>
						<th>Return Date</th>
						<th>Status</th>
					</tr>
				</tfoot>
				<tbody>
					<?php
					if (sizeof($fetchedRows) > 0) {
						foreach ($fetchedRows as $row) {
							$statusText = getBookStatus($row['issue_book_id'], $row['book_issue_status'], $row['expected_return_date']);

                            $bookID = convert_data($row['book_id'], 'encrypt');

                            $issueDate = formatRow($row['issue_date_time']);
                            $returnDate = empty($row['return_date_time']) ? 'Not Returned' :  formatRow($row['return_date_time']);

							echo '
						<tr>
							<td>' . xssSanitize($row["book_isbn_number"]) . '</td>
							<td>' . xssSanitize($row["book_name"]) . '</td>
							<td>' . $issueDate . '</td>
							<td>' . $returnDate . '</td>
							<td>' . $statusText . '</td>
							<td><a href="view_book.php?book=' . $bookID . '">View</a></td>
						</tr>
						';
						}
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
