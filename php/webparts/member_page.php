<?php
    include "php/chat_functions.php";

    $consult_id = null;
    if(isset($_GET['id'])){
        $consult_id = intval($_GET['id']);
    }

    if($consult_id != null && $consult_id >= 0){
        $request = $database -> prepare('
            SELECT users.id, users.name, users.email, users.gender, messages.id, messages.text, messages.date
            FROM messages
            JOIN users
            ON messages.user_id = users.id
            WHERE users.id = ?
            ORDER BY messages.date DESC
        ');

        $request->execute(array( $consult_id ));
        $member_data = $request->fetchAll();
        // var_dump($member_data);

        echo '<div>';
            echo '<h2>Page de l\'utilisateur : ' . $member_data[0]['name'] .'</h2>';
        echo '</div>';
        echo '<div>';
            for($i = 0; $i < count($member_data); $i++){
                echo '<h3>Message posté le '. $member_data[$i]['date'] .'</h3>';
                echo '<p>';
                    echo convert_emoticon($member_data[$i]['text']);
                echo '</p>';
            }
        echo '</div>';
    }
?>