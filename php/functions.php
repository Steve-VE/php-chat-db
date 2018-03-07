<?php

// Récupère une valeur dans $_POST, la sanétise et la retourne s'il y a quelque chose à retourner
function get_value($value_name, $filter="simple"){
    if($filter == "email"){
        $filter_sanitize = FILTER_SANITIZE_EMAIL;
        $filter_validate = FILTER_VALIDATE_EMAIL;
    }
    else{
        $filter_sanitize = FILTER_SANITIZE_STRING;
    }

    if(isset($_POST[$value_name])){
        $value = filter_var( $_POST[$value_name], $filter_sanitize );
        $value = trim($value);

        if( $filter != "simple"){
            $value = filter_var( $_POST[$value_name], $filter_validate );
        }

        if($value != null && $value != false && $value != ""){
            return $value;
        }
    }

    return null;
}

?>