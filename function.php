<?php

//function.php

$sslChecksRunThisLoad = false;

function xssSanitize($strIn) {
    return htmlspecialchars($strIn);
}

function doMissingEncryptionChecks() {
    global $sslChecksRunThisLoad;

    // Already run on this page.
    if ($sslChecksRunThisLoad) return;

    $sslChecksRunThisLoad = true;
    $missingMethods = false;

    if (!function_exists("openssl_encrypt")) {
        $missingMethods = true;

        // Very horrible hack to get this method to work when the plugin is not enabled
        function openssl_encrypt($data, $cipher_algo, $passphrase, $options, $iv) {
            return base64_encode($data);
        }
    }

    if (!function_exists("openssl_decrypt")) {
        $missingMethods = true;

        // Very horrible hack to get this method to work when the plugin is not enabled
        function openssl_decrypt($data, $cipher_algo, $passphrase, $options = 0, $iv = "") {
            return base64_decode($data);
        }
    }

    // We are probably either using a very old PHP version, or the openssl plugin hasn't been enabled
    // Display an error on the site to reflect this issue.
    if ($missingMethods) {
        fwrite(STDOUT, PHP_EOL);
        fwrite(STDOUT, '** Warning **' . PHP_EOL);
        fwrite(STDOUT, 'Please enable the openssl plugin in your php.ini file. ID encryption is not secure!!!' . PHP_EOL);

        echo '<div style="font-weight:bold; text-align: center; padding: 15px">Please enable the openssl plugin in your php.ini file. ID encryption is currently not secure!!!</div>';
    }
}

function is_admin_login()
{
	if(isset($_SESSION['admin_id']))
	{
		return true;
	}
	return false;
}

function is_user_login()
{
	if(isset($_SESSION['user_id']))
	{
		return true;
	}
	return false;
}


function get_book_issue_limit_per_user($connect)
{
	$output = '';
	$query = "
	SELECT library_issue_total_book_per_user FROM lms_setting 
	LIMIT 1
	";
	$result = $connect->query($query);
	foreach($result as $row)
	{
		$output = $row["library_issue_total_book_per_user"];
	}
	return $output;
}

function get_total_book_issue_per_user($connect, $user_unique_id)
{
	$output = 0;

	$query = "
	SELECT COUNT(issue_book_id) AS Total FROM lms_issue_book 
	WHERE user_id = '".$user_unique_id."' 
	AND book_issue_status = 'Issue'
	";

	$result = $connect->query($query);

	foreach($result as $row)
	{
		$output = $row["Total"];
	}
	return $output;
}

function get_total_book_issue_day($connect)
{
	$output = 0;

	$query = "
	SELECT library_total_book_issue_day FROM lms_setting 
	LIMIT 1
	";

	$result = $connect->query($query);

	foreach($result as $row)
	{
		$output = $row["library_total_book_issue_day"];
	}
	return $output;
}

function convert_data($string, $action = 'encrypt')
{
    // Check that the plugin needed for encryption is loaded
    // If not provide some INSECURE dummy methods and alert the admin.
    doMissingEncryptionChecks();

	$encrypt_method = "AES-256-CBC";
	$secret_key = 'AA74CDCC2BBRT935136HH7B63C27'; // user define private key
	$secret_iv = '5fgf5HJ5g27'; // user define secret key
	$key = hash('sha256', $secret_key);
	$iv = substr(hash('sha256', $secret_iv), 0, 16); // sha256 is hash_hmac_algo
	if ($action == 'encrypt') 
	{
		$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
	    $output = base64_encode($output);
	} 
	else if ($action == 'decrypt') 
	{
		$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
	}
	return $output;
}


function Count_total_issue_book_number($connect)
{
	$total = 0;

	$query = "SELECT COUNT(issue_book_id) AS Total FROM lms_issue_book";

	$result = $connect->query($query);

	foreach($result as $row)
	{
		$total = $row["Total"];
	}

	return $total;
}

function get_date_time($connect)
{
	return date("Y-m-d H:i:s",  STRTOTIME(date('h:i:sa')));
}

function Count_total_returned_book_number($connect)
{
	$total = 0;

	$query = "
	SELECT COUNT(issue_book_id) AS Total FROM lms_issue_book 
	WHERE book_issue_status = 'Return'
	";

	$result = $connect->query($query);

	foreach($result as $row)
	{
		$total = $row["Total"];
	}

	return $total;
}

function Count_total_not_returned_book_number($connect)
{
	$total = 0;

	$query = "
	SELECT COUNT(issue_book_id) AS Total FROM lms_issue_book 
	WHERE book_issue_status = 'Issue'
	";

	$result = $connect->query($query);

	foreach($result as $row)
	{
		$total = $row["Total"];
	}

	return $total;
}



function Count_total_book_number($connect)
{
	$total = 0;

	$query = "
	SELECT COUNT(book_id) AS Total FROM lms_book 
	WHERE book_status = 'Enable'
	";

	$result = $connect->query($query);

	foreach($result as $row)
	{
		$total = $row["Total"];
	}

	return $total;
}



?>