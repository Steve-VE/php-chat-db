<?php
    require "php/data.php";
    require "php/functions.php";

    session_start();

    // Connexion à la base de données
    try{
        $database = new PDO("mysql:host=localhost;dbname=".$db['name'], $db['user'], $db['password']);
    }
    catch(PDOEXception $e){
        die($e->getMessage());
    }

    // Gère l'affichage des pages
    $current_page = "login"; // Page à afficher par défaut
    $valid_pages = ["login", "register", "logout", "member"]; // Nom des pages autorisées
    if(isset($_GET['page']) && in_array($_GET['page'], $valid_pages)){
        $current_page = $_GET['page'];
    }

    // Gère les différentes actions
    $allowed_action = ["delete_message"];
    if( isset($_GET['action']) && in_array($_GET['action'], $allowed_action) ){
        $current_action = $_GET['action'];
    }

    // Gère la déconnexion
    if($current_page == "logout"){
        session_unset();
        session_destroy();
        $current_page = "login";
        header('Location:?page=login');
        exit;
    }
    
    if( !isset($_SESSION['user']) ){ // Si l'utilisateur n'est pas encore connecté, on regarde s'il veut se connecter/s'inscrire

        if(isset($_POST['register'])){ // Gère l'inscription
            $user['name'] = get_value('user_name');
            $user['password'] = get_value('user_password');
            $user['email'] = get_value('user_email', "email");
            
            if(!in_array(null, $user)){ // Si tous les champs d'inscription ont été correctement remplis par l'utilisateur
                $user_already_register = false;
                
                $result = $database -> prepare('SELECT name FROM users WHERE name = ? OR email = ?');
                $result->execute( array($user['name'], $user['email']) );
                if($result->fetch() == false){ // Inscription possible uniquement si adresse mail + pseudo pas encore dans la base de données
                    $request = $database -> prepare('INSERT INTO `users`(`name`, `email`, `password`) VALUES (:name, :email, :password)');
                    $request->execute( array(
                        ':name' => $user['name'],
                        ':email' => $user['email'],
                        ':password' => password_hash($user['password'], $hash_type)
                    ) );
                    // echo "Inscription effectué. Bienvenue ". $user['name'] ."!";
                    $current_page = "login";
                }
                else{
                    // echo "Inscription impossible ! Le pseudo et/ou l'adresse mail est déjà utilisé";
                }
            }
        }
        else if(isset($_POST['login'])){ // Gère la connexion
            $user['email'] = get_value('user_email');
            $user['password'] = get_value('user_password');
            
            if(!in_array(null, $user)){ // L'utilisateur a entré son pseudo et son mot de passe...
                $result = $database -> prepare('SELECT * FROM users WHERE email = ?');
                $result->execute( array($user['email']) );
                
                if( ($user_data = $result->fetch()) == true ){
                    
                    if( password_verify( $user['password'], $user_data['password'] ) ){ // Si le mot de passe correspond à celui de l'utilisateur...
                        // ... on définit les variables de session
                        $_SESSION['user']['id'] = $user_data['id'];
                        $_SESSION['user']['name'] = $user_data['name'];
                        $_SESSION['user']['permission'] = $user_data['permission'];
                        $_SESSION['user']['last_message'] = "";
                    }
                }
            }
        }
    }
    else{ // Si l'utilisateur est connecté...

        // S'il y a un message à envoyer sur le tchat
        if(isset($_POST['message-submit'])){
            $message_to_send = get_value("message_box");

            if($message_to_send != null && $message_to_send != $_SESSION['user']['last_message'] ){
                date_default_timezone_set('Europe/Amsterdam');
                $current_date = date("Y-m-d H:i:s");
                $rqst_sending_message = $database -> prepare('
                    INSERT INTO `messages`(`text`, `user_id`, `date`) 
                    VALUES (:text, :user_id, :date)
                ');
                $rqst_sending_message -> execute( array(
                    ':text' => $message_to_send,
                    ':user_id' => $_SESSION['user']['id'],
                    ':date' => $current_date
                ));
                $_SESSION['user']['last_message'] = $message_to_send;
            }
        }

        // S'il y a une action à effectuer
        if(isset($current_action)){
            // Demande de suppression d'un message
            if($current_action == "delete_message" && $_SESSION['user']['permission'] > 0){
                if(isset($_GET['id'])){
                    $message_to_delete = intval($_GET['id']);

                    if($message_to_delete >= 0){
                        $request = $database -> prepare('
                            DELETE FROM messages
                            WHERE id = ?
                        ');
                        $request -> execute(array($message_to_delete));
                        header('Location:?action=done');
                    }
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="stylesheet" href="stylesheets/main.css">

    <title>Chat</title>
</head>
<body>
    <header>
        <?php
        if(isset($_SESSION['user']['id']) && isset($_SESSION['user']['name'])){
            echo '<ul>';
            echo '<li><a href="?page=logout">';
            echo strtoupper("Se deconnecter");
            echo '</a></li>';
            echo '</ul>';
        }
        else{

            echo '<ul>';
            echo '<li><a href="?page=login">';
            echo strtoupper("Se connecter");
            echo '</a></li>';
            echo '<li><a href="?page=register">';
            echo strtoupper("S'enregistrer");
            echo '</a></li>';
            echo '</ul>';
            
            if($current_page == "login"){
                include "php/webparts/login.php";
            }
            else if($current_page == "register"){
                include "php/webparts/register.php";
            }
        }
        ?>
    </header>

    <main>
        <?php 
            if($current_page == "member" && isset($_SESSION['user'])){
                include("php/webparts/member_page.php");
            }
            else{
                echo '<div id="chat">';
                echo '<iframe src="php/webparts/chat.php" 
                scrolling="no" frameborder="0" 
                width="100%" height="auto">
                </iframe>
                ';
                // require("php/webparts/chat.php");
                if(isset($_SESSION['user'])){
                    include("php/webparts/message_box.php"); 
                }
                echo '</div>';
            }
        ?>
    </main>

    <footer>

    </footer>
</body>
</html>