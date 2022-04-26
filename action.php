<?php

//action.php

include 'database_connection.php';
include 'function.php';

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'search_book_isbn')
	{
        $dataIn = array(
            ':like'		=>	"%" . $_POST["request"] . "%"
        );

        $query = "SELECT book_isbn_number, book_name FROM lms_book WHERE book_isbn_number LIKE :like OR book_name LIKE :like AND book_status = 'Enable'";

        $statement = $connect->prepare($query);

        $statement->execute($dataIn);

        $data = array();

		foreach($statement->fetchAll() as $row)
		{
            $bolded = str_ireplace($_POST["request"], '<b>' . xssSanitize($_POST["request"]) . '</b>', xssSanitize($row["book_isbn_number"]));
            $boldedName = str_ireplace($_POST["request"], '<b>' . xssSanitize($_POST["request"]) . '</b>', xssSanitize($row['book_name']));

			$data[] = array(
				'isbn_no'		=>	$bolded,
				'book_name'		=>	$boldedName
			);
		}

        header('Content-Type: application/json');

		echo json_encode($data);
	}

	if($_POST["action"] == 'search_user_id')
	{
        $dataIn = array(
            ':like'		=>	"%" . $_POST["request"] . "%"
        );

        $query = "SELECT user_unique_id, user_name FROM lms_user WHERE user_unique_id LIKE :like OR user_name LIKE :like AND user_status = 'Enable'";

        $statement = $connect->prepare($query);
        $statement->execute($dataIn);

		$data = array();

		foreach($statement->fetchAll() as $row)
		{
            $bolded = str_ireplace($_POST["request"], '<b>' . xssSanitize($_POST["request"]) . '</b>', xssSanitize($row["user_unique_id"]));
            $boldedName = str_ireplace($_POST["request"], '<b>' . xssSanitize($_POST["request"]) . '</b>', xssSanitize($row["user_name"]));

			$data[] = array(
				'user_unique_id'	=>	$bolded,
				'user_name'			=>	$boldedName,
			);
		}

		echo json_encode($data);
	}
}

?>