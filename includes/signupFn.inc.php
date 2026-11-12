<?php
function emptyInputSignup($pwd, $rpwd) {
    return (empty($pwd) || empty($rpwd));
}

function pwdMatch($pwd, $rpwd) {
    return $pwd === $rpwd;
}

function createUser($conn, $name, $email, $address, $pwd, $number) {
    $sql = $conn->prepare("INSERT INTO customer (customer_fname, customer_email, customer_pwd, customer_phone, customer_address) VALUES (?,?,?,?,?)");
    $sql->bind_param('sssss', $name, $email, $pwd, $number, $address);
    $sql->execute();
    
    header("location: ../index.php?success=registered");
    
    $sql->close();
}
?>