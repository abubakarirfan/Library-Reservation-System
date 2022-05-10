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
            <input type="text" name="search_data" id="search_data" class="form-control" placeholder="Search..."/>
        </div>
        <div class="mb-3">
            <input type="submit" name="search_button" class="btn btn-success" value="Search"/>
        </div>
    </form>

    <?php
    if (isset($_POST["search_button"])) {

        $message = '';
        $statement = false;

        if (empty($_POST["search_data"])) {
            $message .= '<li>Search something.</li>';
        } else {
            $data = array(
                'search_data' => '%' . htmlspecialchars($_POST['search_data']) . '%'
            );

            // Check if name is like the book category, like the book author, or like the book name
            $query = "SELECT * FROM lms_book
                WHERE book_category LIKE :search_data OR book_author LIKE :search_data OR book_name LIKE :search_data";

            $statement = $connect->prepare($query);

            $statement->execute($data);

            if ($statement->rowCount() === 0) {
                $message .= 'No data found';
            }
        }


        ?>

        <div class="card-body">
            <?php if (empty($message)) : ?>
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
                    if ($statement->rowCount() > 0) {
                        function boldify($inValue, $toReplace) {
                            return str_ireplace($inValue, '<b>' . xssSanitize($inValue) . '</b>', xssSanitize($toReplace));
                        }

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
                            <td>' . boldify($_POST['search_data'], $row["book_name"]) . '</td>
                            <td>' . xssSanitize($row["book_isbn_number"]) . '</td>
                            <td>' . boldify($_POST['search_data'], $row["book_category"]) . '</td>
                            <td>' . boldify($_POST['search_data'], $row["book_author"]) . '</td>
                            <td>' . xssSanitize($row["book_no_of_copy"]) . '</td>
                            <td>' . $book_status . '</td>
                            <td><a href="view_book.php?book=' . $bookID . '">View</a></td>
                        </tr>
                    ';
                        }
                    }
                    ?>
                </tbody>
            </table>
        <?php else : ?>
            <div><?= $message ?></div>
        <?php endif; ?>
        </div>
        <?php
    }
    ?>

</div>

<?php

include 'footer.php';

?>
