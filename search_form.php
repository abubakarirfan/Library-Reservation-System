<?php
include 'database_connection.php';
include 'function.php';
include 'header.php';
?>

<ul class="list-inline mt-4" align="center">
	<li class="list-inline-item"><a href="issue_book_details.php">Current Booking</a></li>
	<li class="list-inline-item"><a href="catalog.php">Catalog</a></li>
    <li class="list-inline-item"><a href="search_form.php">Search</a></li>
	<li class="list-inline-item"><a href="logout.php">Logout</a></li>
</ul>

<div class="container-fluid py-4" style="min-height: 700px;">
	<h1></h1>
	<div class="card mb-4">
	</div>

    <form method="post">
        
    <div class="mb-3">
        <label class="form-label">Search Bar</label>
        <input type="text" name="search_data" id="search_data" class="form-control" placeholder="Search..." />
    </div> 
    <div class="mb-3">
        <input type="submit" name="search_button" class="btn btn-success" value="Search" />
    </div>
    </form>

    <?php
        if(isset($_POST["search_button"]))
        {


            ?>

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
                        <th>More</th>
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
						<th>More</th>
					</tr>
				</tfoot>
				<tbody>
                    <?php
            $formdata = array();

            if(empty($_POST["search_data"]))
            {
                $message .= '<li>Search something.</li>';
            }

            if($message == '')
            {
                $data = array(
                    ':search_data' => $formdata['search_data']
                );

                $query = "
                SELECT * FROM lms_book
                WHERE book_category LIKE '" . $formdata['search_data'] . "%' OR book_author LIKE '" . $formdata['search_data'] . "%' OR book_name LIKE '" . $formdata['search_data'] . "%'
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

                        $bookID = convert_data($row['book_id'], 'encrypt');

                        echo '
                        <tr>
                            <td>' . xssSanitize($row["book_name"]) . '</td>
                            <td>' . xssSanitize($row["book_isbn_number"]) . '</td>
                            <td>' . xssSanitize($row["book_category"]) . '</td>
                            <td>' . xssSanitize($row["book_author"]) . '</td>
                            <td>' . xssSanitize($row["book_no_of_copy"]) . '</td>
                            <td>' . $book_status . '</td>
                            <td><a href="view_book.php?book=' . $bookID . '">View</a></td>
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
            }
            ?>
            </tbody>
			</table>
		</div>
        <?php
        }
    ?>

</div>

<?php

include 'footer.php';

?>