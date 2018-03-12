<?php
    include "php/chat_functions.php";

    // Si l'utilisateur a uploadé une image pour son avatar
    if(isset($_FILES['avatar']) && $_FILES['avatar']['size'] > 0 && $_FILES['avatar']['size'] < 1000000 ){ 
        // var_dump($_FILES);

        $image_path = $_FILES['avatar']['tmp_name'];
        $image_extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $image_data = file_get_contents($image_path);
        $image_base64 = 'data:image/' . $image_extension .';base64,'.  base64_encode($image_data);

        $update_request = $database->prepare('
            UPDATE `users` 
            SET `avatar`= :image_data
            WHERE id = :id
        ');

        $update_request->execute(array(
            ':image_data' => $image_base64,
            ':id' => $_SESSION['user']['id']
        ));
    }

    $consult_id = null;
    if(isset($_GET['id'])){
        $consult_id = intval($_GET['id']);
    }

    if($consult_id != null && $consult_id >= 0){
        $request = $database -> prepare('
            SELECT users.id, users.name, users.email, users.gender, users.avatar, messages.id, messages.text, messages.date
            FROM messages
            JOIN users
            ON messages.user_id = users.id
            WHERE users.id = ?
            ORDER BY messages.date DESC
            LIMIT 10
        ');

        
        $request->execute(array( $consult_id ));
        $member_data = $request->fetchAll();
        // var_dump($member_data);
        
        $image_avatar = $member_data[0]['avatar'];
        
        echo '<div id="member-page">';
            echo '<header>';
            echo '<h2>Page de l\'utilisateur : ' . $member_data[0]['name'] .'</h2>';
                if($image_avatar != null && strlen($image_avatar) > 0){
                    echo '<img class="avatar" src="'. $image_avatar .'" alt="avatar" />';
                }
            echo '</header>';
            echo '<section>';
            echo '<h3>Les 10 derniers messages de '. $member_data[0]['name'] .'</h3>';
                echo '<div class="items-collection">';
                    for($i = 0; $i < count($member_data); $i++){
                        echo '<div class="item">';
                            echo '<h4>Message posté le '. $member_data[$i]['date'] .'</h4>';
                            echo '<p>';
                                echo convert_emoticon($member_data[$i]['text']);
                            echo '</p>';
                        echo '</div>';
                    }
                echo '</div>';
            echo '</section>';

            echo '<section>';
            if($_SESSION['user']['id'] == $consult_id){
                echo '<form method="post" enctype="multipart/form-data">';
                    echo '<label>';
                        echo 'Choisir un avatar : ';
                        echo '<input type="file" name="avatar" accept="image/*"/>';
                    echo '</label>';
                    echo '<input type="submit" name="upload_image" value="Envoyer" />';
                echo '</form>';
            }
            echo '</section>';
        echo '</div>';
    }
?>