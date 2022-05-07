<?php

//issue_book.php

include 'database_connection.php';

include 'function.php';

if (!is_admin_login()) {
    header('location:/admin_login.php');
}

$error = '';

if (isset($_POST["issue_book_button"])) {
    $formdata = array();

    if (empty($_POST["book_id"])) {
        $error .= '<li>Book ISBN Number is required</li>';
    } else {
        $formdata['book_id'] = trim($_POST['book_id']);
    }

    if (empty($_POST["user_id"])) {
        $error .= '<li>User Unique Number is required</li>';
    } else {
        $formdata['user_id'] = trim($_POST['user_id']);
    }

    if ($error == '') {
        //Check Book Available or Not

        $query = "
        SELECT * FROM lms_book 
        WHERE book_isbn_number = '" . $formdata['book_id'] . "'
        ";

        $statement = $connect->prepare($query);

        $statement->execute();

        if ($statement->rowCount() > 0) {
            foreach ($statement->fetchAll() as $book_row) {
                //check book is available or not
                if ($book_row['book_no_of_copy'] > 0) {
                    //Check User is exist

                    $query = "
                    SELECT user_id FROM lms_user 
                    WHERE user_unique_id = '" . $formdata['user_id'] . "'
                    ";

                    $statement = $connect->prepare($query);

                    $statement->execute();

                    if ($statement->rowCount() > 0) {
                        foreach ($statement->fetchAll() as $user_row) {
                            $book_issue_limit = get_book_issue_limit_per_user($connect);

                                $total_book_issue = get_total_book_issue_per_user($connect, $formdata['user_id']);

                                if ($total_book_issue < $book_issue_limit) {
                                    $total_book_issue_day = get_total_book_issue_day($connect);

                                    $today_date = get_date_time($connect);

                                    $expected_return_date = date('Y-m-d H:i:s', strtotime($today_date . ' + ' . $total_book_issue_day . ' days'));

                                    $data = array(
                                        ':book_id'      =>  $formdata['book_id'],
                                        ':user_id'      =>  $formdata['user_id'],
                                        ':issue_date_time'  =>  $today_date,
                                        ':expected_return_date' => $expected_return_date,
                                        ':return_date_time' =>  '',
                                        ':book_issue_status'    =>  'Issue',
                                    );

                                    $query = "
                                    INSERT INTO lms_issue_book 
                                    (book_id, user_id, issue_date_time, expected_return_date, return_date_time, book_issue_status) 
                                    VALUES (:book_id, :user_id, :issue_date_time, :expected_return_date, :return_date_time, :book_issue_status)
                                    ";

                                    $statement = $connect->prepare($query);

                                    $statement->execute($data);

                                    $query = "
                                    UPDATE lms_book 
                                    SET book_no_of_copy = book_no_of_copy - 1, 
                                    book_updated_on = '" . $today_date . "' 
                                    WHERE book_isbn_number = '" . $formdata['book_id'] . "' 
                                    ";

                                    $connect->query($query);

                                    header('location:admin_issue_book.php?msg=add');
                                
                                } else {
                                    $error .= 'User has already reached Book Issue Limit, First return pending book';
                                }
                        }
                    } else {
                        $error .= '<li>User not Found</li>';
                    }
                } else {
                    $error .= '<li>Book not Available</li>';
                }
            }
        } else {
            $error .= '<li>Book not Found</li>';
        }
    }
}

if (isset($_POST["book_return_button"])) {
    if (isset($_POST["book_return_confirmation"])) {
        $data = array(
            ':return_date_time'     =>  get_date_time($connect),
            ':book_issue_status'    =>  'Return',
            ':issue_book_id'        =>  $_POST['issue_book_id']
        );

        $query = "
        UPDATE lms_issue_book 
        SET return_date_time = :return_date_time, 
        book_issue_status = :book_issue_status 
        WHERE issue_book_id = :issue_book_id
        ";

        $statement = $connect->prepare($query);

        $statement->execute($data);

        $query = "
        UPDATE lms_book 
        SET book_no_of_copy = book_no_of_copy + 1 
        WHERE book_isbn_number = '" . $_POST["book_isbn_number"] . "'
        ";

        $connect->query($query);

        header("location:admin_issue_book.php?msg=return");
    } else {
        $error = 'Please first confirm return book received by click on checkbox';
    }
}

$query = "
	SELECT * FROM lms_issue_book 
    ORDER BY issue_book_id DESC
";

$statement = $connect->prepare($query);

$statement->execute();

include 'header.php';

?>
<div class="container-fluid py-4" style="min-height: 700px;">
    <h1>Issue Book Management</h1>
    <?php

    if (isset($_GET["action"])) {
        if ($_GET["action"] == 'add') {
    ?>
            <ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
                <li class="breadcrumb-item"><a href="admin_index.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="admin_issue_book.php">Issue Book Management</a></li>
                <li class="breadcrumb-item active">Issue New Book</li>
            </ol>
            <div class="row">
                <div class="col-md-6">
                    <?php
                    if ($error != '') {
                        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">' . $error . '</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                    }
                    ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-user-plus"></i> Issue New Book
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <div class="mb-3">
                                    <label class="form-label">ISBN Number</label>
                                    <input type="text" name="book_id" id="book_id" class="form-control" />
                                    <span id="book_isbn_result"></span>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Student ID</label>
                                    <input type="text" name="user_id" id="user_id" class="form-control" />
                                    <span id="user_unique_id_result"></span>
                                </div>
                                <div class="mt-4 mb-0">
                                    <input type="submit" name="issue_book_button" class="btn btn-success" value="Issue" />
                                </div>
                            </form>
                            <script>
                                var book_id = document.getElementById('book_id');
                                var user_id = document.getElementById('user_id');
                                var book_result = document.getElementById('book_isbn_result');
                                var user_result = document.getElementById('user_unique_id_result');

                                book_id.onkeyup = function() {
                                    if (this.value.length > 2) {
                                        var form_data = new FormData();

                                        form_data.append('action', 'search_book_isbn');

                                        form_data.append('request', this.value);

                                        fetch('action.php', {
                                            method: "POST",
                                            body: form_data
                                        }).then(function(response) {
                                            return response.json();
                                        }).then(function(responseData) {
                                            let listElement = document.createElement('div');

                                            listElement.classList.add("list-group");
                                            listElement.style.position = "absolute";
                                            listElement.style.width = "93%";

                                            if (responseData.length > 0) {
                                                for (var count = 0; count < responseData.length; count++) {
                                                    let generalButton = document.createElement('a');
                                                    let isbnText = document.createElement('span');
                                                    let separatorText = document.createTextNode(" - ");
                                                    let nameNode = document.createElement('span');

                                                    generalButton.href = "#";
                                                    generalButton.classList.add("list-group-item");
                                                    generalButton.classList.add("list-group-item-action");

                                                    nameNode.classList.add("text-muted");

                                                    isbnText.innerHTML = responseData[count].isbn_no;
                                                    nameNode.innerHTML = responseData[count].book_name;

                                                    generalButton.addEventListener('click', function() {
                                                        book_result.innerText = '';

                                                        book_id.value = isbnText.textContent;
                                                    })

                                                    generalButton.appendChild(isbnText);
                                                    generalButton.appendChild(separatorText);
                                                    generalButton.appendChild(nameNode);

                                                    listElement.appendChild(generalButton);
                                                }
                                            } else {
                                                let noChild = document.createElement('a');

                                                noChild.href = '#';
                                                noChild.classList.add("list-group-item");
                                                noChild.classList.add("list-group-item-action");
                                                noChild.innerText = "No book found";

                                                listElement.appendChild(noChild);
                                            }

                                            book_result.innerHTML = '';
                                            book_result.appendChild(listElement);
                                        });
                                    } else {
                                        book_result.innerHTML = '';
                                    }
                                }

                                user_id.onkeyup = function() {
                                    if (this.value.length > 2) {
                                        var form_data = new FormData();

                                        form_data.append('action', 'search_user_id');

                                        form_data.append('request', this.value);

                                        fetch('action.php', {
                                            method: "POST",
                                            body: form_data
                                        }).then(function(response) {
                                            return response.json();
                                        }).then(function(responseData) {
                                            let listElement = document.createElement('div');

                                            listElement.classList.add("list-group");
                                            listElement.style.position = "absolute";
                                            listElement.style.width = "93%";

                                            if (responseData.length > 0) {
                                                for (var count = 0; count < responseData.length; count++) {
                                                    let generalButton = document.createElement('a');
                                                    let idNode = document.createElement('span');
                                                    let separatorText = document.createTextNode(" - ");
                                                    let nameNode = document.createElement('span');

                                                    generalButton.href = "#";
                                                    generalButton.classList.add("list-group-item");
                                                    generalButton.classList.add("list-group-item-action");

                                                    nameNode.classList.add("text-muted");

                                                    idNode.innerHTML = responseData[count].user_unique_id;
                                                    nameNode.innerHTML = responseData[count].user_name;

                                                    generalButton.addEventListener('click', function() {
                                                        user_result.innerText = '';

                                                        user_id.value = idNode.textContent;
                                                    })

                                                    generalButton.appendChild(idNode);
                                                    generalButton.appendChild(separatorText);
                                                    generalButton.appendChild(nameNode);

                                                    listElement.appendChild(generalButton);
                                                }
                                            } else {
                                                let noChild = document.createElement('a');

                                                noChild.href = '#';
                                                noChild.classList.add("list-group-item");
                                                noChild.classList.add("list-group-item-action");
                                                noChild.innerText = "No user found";

                                                listElement.appendChild(noChild);
                                            }

                                            user_result.innerHTML = '';
                                            user_result.appendChild(listElement);
                                        });
                                    } else {
                                        user_result.innerHTML = '';
                                    }
                                }
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        } else if ($_GET["action"] == 'view') {
            $issue_book_id = convert_data($_GET["code"], 'decrypt');

            if ($issue_book_id > 0) {
                $query = "
                SELECT * FROM lms_issue_book 
                WHERE issue_book_id = '$issue_book_id'
                ";

                $result = $connect->query($query);

                foreach ($result as $row) {
                    $query = "
                    SELECT * FROM lms_book 
                    WHERE book_isbn_number = '" . $row["book_id"] . "'
                    ";

                    $book_result = $connect->query($query);

                    $query = "
                    SELECT * FROM lms_user 
                    WHERE user_unique_id = '" . $row["user_id"] . "'
                    ";

                    $user_result = $connect->query($query);

                    echo '
                    <ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
                        <li class="breadcrumb-item"><a href="admin_index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="admin_issue_book.php">Issue Book Management</a></li>
                        <li class="breadcrumb-item active">View Issue Book Details</li>
                    </ol>
                    ';

                    if ($error != '') {
                        echo '<div class="alert alert-danger">' . $error . '</div>';
                    }

                    foreach ($book_result as $book_data) {
                        echo '
                        <h2>Book Details</h2>
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Book ISBN Number</th>
                                <td width="70%">' . $book_data["book_isbn_number"] . '</td>
                            </tr>
                            <tr>
                                <th width="30%">Book Title</th>
                                <td width="70%">' . $book_data["book_name"] . '</td>
                            </tr>
                            <tr>
                                <th width="30%">Author</th>
                                <td width="70%">' . $book_data["book_author"] . '</td>
                            </tr>
                        </table>
                        <br />
                        ';
                    }

                    foreach ($user_result as $user_data) {
                        echo '
                        <h2>User Details</h2>
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Studeny ID</th>
                                <td width="70%">' . $user_data["user_unique_id"] . '</td>
                            </tr>
                            <tr>
                                <th width="30%">Student Name</th>
                                <td width="70%">' . $user_data["user_name"] . '</td>
                            </tr>
                            <tr>
                                <th width="30%">Address</th>
                                <td width="70%">' . $user_data["user_address"] . '</td>
                            </tr>
                            <tr>
                                <th width="30%">Contact No.</th>
                                <td width="70%">' . $user_data["user_contact_no"] . '</td>
                            </tr>
                            <tr>
                                <th width="30%">Email Address</th>
                                <td width="70%">' . $user_data["user_email_address"] . '</td>
                            </tr>
                        </table>
                        <br />
                        ';
                    }

                    $status = $row["book_issue_status"];

                    $form_item = '';

                    if ($status == "Issue") {
                        $status = '<span class="badge bg-warning">Issue</span>';

                        $form_item = '
                        <label><input type="checkbox" name="book_return_confirmation" value="Yes" /> I acknowledge that I have received the issued book.</label>
                        <br />
                        <div class="mt-4 mb-4">
                            <input type="submit" name="book_return_button" value="Book Return" class="btn btn-primary" />
                        </div>
                        ';
                    }

                    if ($status == 'Not Return') {
                        $status = '<span class="badge bg-danger">Not Return</span>';

                        $form_item = '
                        <label><input type="checkbox" name="book_return_confirmation" value="Yes" /> I aknowledge that I have received Issued Book</label><br />
                        <div class="mt-4 mb-4">
                            <input type="submit" name="book_return_button" value="Book Return" class="btn btn-primary" />
                        </div>
                        ';
                    }

                    if ($status == 'Return') {
                        $status = '<span class="badge bg-primary">Return</span>';
                    }

                    echo '
                    <h2>Issue Book Details</h2>
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Book Issue Date</th>
                            <td width="70%">' . $row["issue_date_time"] . '</td>
                        </tr>
                        <tr>
                            <th width="30%">Book Return Date</th>
                            <td width="70%">' . $row["return_date_time"] . '</td>
                        </tr>
                        <tr>
                            <th width="30%">Book Issue Status</th>
                            <td width="70%">' . $status . '</td>
                        </tr>
                    </table>
                    <form method="POST">
                        <input type="hidden" name="issue_book_id" value="' . $issue_book_id . '" />
                        <input type="hidden" name="book_isbn_number" value="' . $row["book_id"] . '" />
                        ' . $form_item . '
                    </form>
                    <br />
                    ';
                }
            }
        }
    } else {
        ?>
        <ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
            <li class="breadcrumb-item"><a href="admin_index.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Issue Book Management</li>
        </ol>

        <?php
        if (isset($_GET['msg'])) {
            if ($_GET['msg'] == 'add') {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">New Book Issue Successfully<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }

            if ($_GET["msg"] == 'return') {
                echo '
            <div class="alert alert-success alert-dismissible fade show" role="alert">Issued Book Successfully Return into Library <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
            ';
            }
        }
        ?>

        <div class="card mb-4">
            <div class="card-header">
                <div class="row">
                    <div class="col col-md-6">
                        <i class="fas fa-table me-1"></i> Issue Book Management
                    </div>
                    <div class="col col-md-6" align="right">
                        <a href="admin_issue_book.php?action=add" class="btn btn-success btn-sm">Add</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table id="datatablesSimple">
                    <thead>
                        <tr>
                            <th>Book ISBN Number</th>
                            <th>Student ID</th>
                            <th>Issue Date</th>
                            <th>Return Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>Book ISBN Number</th>
                            <th>Student ID</th>
                            <th>Issue Date</th>
                            <th>Return Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        if ($statement->rowCount() > 0) {


                            foreach ($statement->fetchAll() as $row) {
                                $status = $row["book_issue_status"];


                                if ($row["book_issue_status"] == "Issue") {
                                    $current_date_time = new DateTime(get_date_time($connect));
                                    $expected_return_date = new DateTime($row["expected_return_date"]);

                                    if ($current_date_time > $expected_return_date) {
                                        $interval = $current_date_time->diff($expected_return_date);

                                        $total_day = $interval->d;

                                        $book_fines = $total_day * $one_day_fine;

                                        $status = 'Not Return';

                                        $query = "
        						                    UPDATE lms_issue_book 
													SET book_fines = '" . $book_fines . "', 
													book_issue_status = '" . $status . "' 
													WHERE issue_book_id = '" . $row["issue_book_id"] . "'
        						";

                                        $connect->query($query);
                                    }
                                }

                                if ($status == 'Issue') {
                                    $status = '<span class="badge bg-warning">Issue</span>';
                                }

                                if ($status == 'Not Return') {
                                    $status = '<span class="badge bg-danger">Not Return</span>';
                                }

                                if ($status == 'Return') {
                                    $status = '<span class="badge bg-primary">Return</span>';
                                }

                                echo '
        				<tr>
        					<td>' . $row["book_id"] . '</td>
        					<td>' . $row["user_id"] . '</td>
        					<td>' . $row["issue_date_time"] . '</td>
        					<td>' . $row["return_date_time"] . '</td>
        					<td>' . $status . '</td>
        					<td>
                                <a href="admin_issue_book.php?action=view&code=' . convert_data($row["issue_book_id"]) . '" class="btn btn-info btn-sm">View</a>
                            </td>
        				</tr>
        				';
                            }
                        } else {
                            echo '
        			<tr>
        				<td colspan="7" class="text-center">No Data Found</td>
        			</tr>
        			';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php
    }
    ?>
</div>

<?php

include 'footer.php';

?>