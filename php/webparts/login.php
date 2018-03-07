<?php

echo '<form method="post">';

    // echo '<label>';
    //     echo 'Pseudo : <input type="text" name="user_name" id="user-name"';
    //     if(isset($user['name'])){
    //         echo ' value="'. $user['name'] .'"';
    //     }
    //     echo '>';
    // echo '</label>';

    echo '<label>';
        echo 'Mail : <input type="email" name="user_email" id="user-email"';
        if(isset($user['email'])){
            echo ' value="'. $user['email'] .'"';
        }
        echo '>';
    echo '</label>';


    echo '<label>';
        echo 'Mot de passe : <input type="password" name="user_password" id="user-password"';
        if(isset($user['password'])){
            echo ' value="'. $user['password'] .'"';
        }
        echo '>';
    echo '</label>';

    // echo '<button type="submit" value="login">Se connecter</button>';
    echo '<input  type="submit" name="login" value="Se connecter">';

echo '</form>';

?>