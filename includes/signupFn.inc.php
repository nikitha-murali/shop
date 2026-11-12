<?php
function emptyInputSignup($pwd, $rpwd) {
    return (empty($pwd) || empty($rpwd));
}

function pwdMatch($pwd, $rpwd) {
    return $pwd === $rpwd;
}

function createUser($conn, $name, $email, $address, $pwd, $number, $hostel, $course, $semester) {
    $hashedPassword = password_hash($pwd, PASSWORD_DEFAULT);
    if ($hashedPassword === false) {
        error_log('Unable to hash a new customer password.');
        return false;
    }

    $sql = $conn->prepare(
        'INSERT INTO customer
            (customer_fname, customer_email, customer_pwd, customer_phone, customer_address, hostel, course, semester)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );
    if (!$sql) {
        error_log('Unable to prepare customer signup insert: ' . $conn->error);
        return false;
    }

    $sql->bind_param(
        'ssssssss',
        $name,
        $email,
        $hashedPassword,
        $number,
        $address,
        $hostel,
        $course,
        $semester
    );
    $created = $sql->execute();
    if (!$created) {
        error_log('Unable to create customer account: ' . $sql->error);
    }
    $sql->close();

    return $created;
}
?>