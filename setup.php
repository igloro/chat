php
session_start();

 Define the setup file
$setupFile = 'setup.php';

 If the form is submitted, save the chat title and password
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = htmlspecialchars($_POST['title']);
    $password = isset($_POST['password'])  htmlspecialchars($_POST['password'])  '';

     Save the settings to session
    $_SESSION['chat_title'] = $title;
    if ($password !== '') {
        $_SESSION['chat_password'] = password_hash($password, PASSWORD_DEFAULT);
    }

     Rename this file to prevent setup from running again
    rename($setupFile, $setupFile . '.bak');

     Redirect to index.php
    header('Location index.php');
    exit();
}

 If setup is already done, redirect to index.php
if (!file_exists($setupFile)) {
    header('Location index.php');
    exit();
}



!DOCTYPE html
html lang=en
head
    meta charset=UTF-8
    titleChat Setuptitle
head
body
    h1Setup Your Chath1
    form method=POST
        labelChat Titlelabelbr
        input type=text name=title requiredbrbr
        
        labelOptional Passwordlabelbr
        input type=password name=passwordbrbr
        
        button type=submitSetup Chatbutton
    form
body
html
