<?php
    $path = $_SERVER['DOCUMENT_ROOT'];
    $path .= "/php";

    include $path ."/chat_functions.php";
    include $path ."/data.php";

    // var_dump($db);

    // session_start();
    // Connexion à la base de données
    try{
        $database = new PDO("mysql:host=localhost;dbname=".$db['name'], $db['user'], $db['password']);
    }
    catch(PDOEXception $e){
        die($e->getMessage());
    }

    $result = $database->query('
        SELECT messages.id, messages.date, messages.text, messages.user_id, users.name, users.avatar
        FROM messages 
        JOIN users
        ON messages.user_id = users.id
        ORDER BY messages.date DESC
        LIMIT 15
    ');

    $messages_data = $result->fetchAll();
    
    for($i = 0; $i < count($messages_data); $i++){
        $message = $messages_data[$i];

        if($i == 0 || $message['name'] != $messages_data[$i - 1]['name']){
            echo '<div class="message';
                if(isset($_SESSION['user']) 
                && $message['name'] == $_SESSION['user']['name'] ){
                    echo ' myself'; // Permet de différencier nos messages de ceux des autres utilisateurs
                }
                echo '">';

                echo '<div class="message-header">';
                    echo '<a href="/?page=member&id='. $message['user_id'] .'">';
                    if($message['avatar'] != null && strlen($message['avatar']) > 0){
                        echo '<img class="avatar" src="'. $message['avatar'] .'" alt="avatar" />';
                    }
                    echo '<strong>'. $message['name'] .'</strong>';
                    echo '</a>';
                echo '</div>';
        }

        echo '<div class="message-body">';
            echo '<p';
                echo ' title="'. $message['name']. ', '. $message['date'] .'"';
                echo'>';
                echo convert_emoticon($message['text']);
                if(isset($_SESSION['user']['permission']) && $_SESSION['user']['permission'] > 0){
                    echo '<a class="delete" href="/?action=delete_message&id=';
                    echo $message['id'];
                    echo '" title="Supprimer ce message ?">x</a>';
                }
            echo '</p>';
        echo '</div>';

        if($i == (count($messages_data) - 1) 
        ||  $message['name'] != $messages_data[$i + 1]['name'] ){
            echo '</div>';
        }
    }