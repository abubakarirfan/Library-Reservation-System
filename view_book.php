<?php

//book_view.php

include 'database_connection.php';
include 'function.php';

// Disallow access if not logged in
if (!is_user_login() && !is_admin_login()) {
	header('location:user_login.php');
}

$homePage = (is_admin_login() ? 'admin_index' : 'issue_book_details') . '.php';

$message = '';

$error = '';

if (isset($_POST['return_book'], $_POST['book_id'])) {
    $bookID = convert_data($_POST['book_id'], 'decrypt');

    $data = array(
        ':return_date_time' => get_date_time($connect),
        ':book_issue_status' => 'Return',
        ':issue_book_id' => $bookID
    );
    $dataBasic = array(
        ':issue_book_id' => $bookID
    );

    $query = "
        UPDATE lms_issue_book 
        SET return_date_time = :return_date_time, 
        book_issue_status = :book_issue_status 
        WHERE book_id = :issue_book_id AND book_issue_status = 'Issue'";

    $statement = $connect->prepare($query);
    $statement->execute($data);

    if ($statement->rowCount() > 0) {
        $query = "
        UPDATE lms_book
        SET book_no_of_copy = book_no_of_copy + 1
        WHERE book_isbn_number = :issue_book_id";

        $statement = $connect->prepare($query);
        $statement->execute($dataBasic);

        header('location: issue_book_details.php');

        return;
    } else {
        $message = "You do not have this book on reservation";
    }
} elseif (isset($_POST['reserve_book'], $_POST['book_id'])) {
    $bookID = convert_data($_POST['book_id'], 'decrypt');

    $total_book_issue = get_total_book_issue_per_user($connect, $_SESSION['user_id']);

    $book_issue_limit = get_book_issue_limit_per_user($connect);
    $total_book_issue_day = get_total_book_issue_day($connect);

    if ($total_book_issue < $book_issue_limit) {
        $today_date = get_date_time($connect);

        $expected_return_date = date('Y-m-d H:i:s', strtotime($today_date . ' + ' . $total_book_issue_day . ' days'));

        $data = array(
            ':book_id'      =>  $bookID,
            ':user_id'      =>  $_SESSION['user_id'],
            ':issue_date_time'  =>  $today_date,
            ':expected_return_date' => $expected_return_date,
            ':return_date_time' =>  '',
            ':book_issue_status'    =>  'Issue',
            ':book_fines'           => '0'
        );

        $query = "INSERT INTO lms_issue_book 
                    (book_id, user_id, issue_date_time, expected_return_date, return_date_time, book_issue_status, book_fines) 
                    VALUES (:book_id, :user_id, :issue_date_time, :expected_return_date, :return_date_time, :book_issue_status, :book_fines)";

        $statement = $connect->prepare($query);
        $statement->execute($data);

        $query = "UPDATE lms_book 
            SET book_no_of_copy = book_no_of_copy - 1, 
                book_updated_on = '" . $today_date . "' 
            WHERE book_isbn_number = '" . $bookID . "'";

        $connect->query($query);

        header('location: issue_book_details.php');

        return;
    } else {
        $message = "You have already reached the maximum amount of books. Please return some to reserve more.";
    }
} else if (!isset($_GET['book'])) {
    header("location:${$homePage}");
}

$bookID = convert_data($_GET['book'], 'decrypt');
$alreadyReserved = false;

$data = array(
    ':book_id'		=>	$bookID
);

$query = "
	SELECT * FROM lms_issue_book 
	INNER JOIN lms_book 
	ON lms_book.book_isbn_number = lms_issue_book.book_id 
	WHERE lms_issue_book.user_id = '" . $_SESSION['user_id'] . "' 
	AND lms_book.book_id = :book_id AND book_issue_status = 'Issue'
	ORDER BY lms_issue_book.issue_book_id DESC
";

$statement = $connect->prepare($query);
$statement->execute($data);

$book_row = [];
$status = '';

if ($statement->rowCount() > 0) {
    $alreadyReserved = true;

    $book_row = $statement->fetch();
    $status = '<div class="badge bg-warning">Reserved</div>';
} else {
    $query = "SELECT * FROM lms_book WHERE lms_book.book_id = :book_id";

    $statement = $connect->prepare($query);

    $statement->execute($data);

    if ($statement->rowCount() > 0) {
        $book_row = $statement->fetch();

        $book_status = '';

        if ($book_row['book_no_of_copy'] > 0) {
            $status = '<div class="badge bg-success">Available</div>';
        } else {
            $status = '<div class="badge bg-danger">Not Available</div>';
        }
    }
}



include 'header.php';

?>
<div class="container-fluid py-4" style="min-height: 700px;">
	<h1>View Book</h1>
		<ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
			<li class="breadcrumb-item"><a href="<?= $homePage ?>">Dashboard</a></li>
			<li class="breadcrumb-item active">View Book</li>
		</ol>
		<div class="card mb-4">
			<div class="card-header">
				<div class="row">
					<div class="col col-md-6">
						<i class="fas fa-table me-1"> </i>
                        <span>Viewing Book</span>
					</div>
				</div>
			</div>
			<div class="card-body">
                <?php

                if ($message != '') {
                    echo '<div class="alert alert-danger"><ul>'.$message.'</ul></div>';
                }

                ?>

                <?php if ($statement->rowCount() > 0) : ?>
                <form method="post">
                    <div class="row">
                        <div class="col-md-6">
                            <h3>Book Name</h3>
                            <div><?= xssSanitize($book_row["book_name"]); ?></div>
                        </div>
                        <div class="col-md-6">
                            <h3>Book Author</h3>
                            <div><?= xssSanitize($book_row["book_author"]); ?></div>
                        </div>
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Book Category</h4>
                            <div><?= xssSanitize($book_row["book_category"]); ?></div>
                        </div>
                        <div class="col-md-6">
                            <h4>Book Status</h4>
                            <div><?= $status ?></div>
                        </div>
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Book Copies</h4>
                            <div><?= xssSanitize($book_row["book_no_of_copy"]); ?></div>
                        </div>
                    </div>
                    <div class="mt-4 mb-3 text-center">
                        <?php if (is_admin_login()) : ?>
                        <a class="btn btn-secondary" href="book.php?action=edit&code=<?= convert_data($book_row['book_id'], 'encrypt') ?>">Edit</a>
                        <?php elseif ($alreadyReserved) : ?>
                        <input type="hidden" name="book_id" value="<?= convert_data($book_row['book_isbn_number'], 'encrypt') ?>" />
                        <input type="submit" name="return_book" class="btn btn-secondary" value="Return Book" />
                        <?php else : ?>
                        <input type="hidden" name="book_id" value="<?= convert_data($book_row['book_isbn_number'], 'encrypt') ?>" />
                        <input type="submit" name="reserve_book" class="btn btn-primary" value="Reserve Copy" />
                        <?php endif; ?>
                    </div>
                </form>
			</div>
    <?php else : ?>
        <h3>No books found with that ID</h3>
    <?php endif; ?>
		</div>
</div>


<?php

include 'footer.php';

?>