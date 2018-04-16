
window.onload = () => {
    refreshChat(); // On charge une première fois le chat...
    // ... ensuite, on définit que le chat sera réactualisé toutes les 5 secondes
    window.setInterval(refreshChat, 5 * 1000);
};


let refreshChat = () => { // Fonction à appeller pour mettre le chat à jour (requête HTTP + modif de l'HTML)
    let messages = document.getElementsByClassName("items-collection");

    let httpRequest;
    // ancien code de compatibilité, aujourd’hui inutile
    if (window.XMLHttpRequest) { // Mozilla, Safari, ...
        httpRequest = new XMLHttpRequest();
    }
    else if (window.ActiveXObject) { // IE
        httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
    }

    httpRequest.onreadystatechange = (response) => { // Réactualisation du tchat
        if(httpRequest.readyState == 4){ // 4 = Done (opération accomplie), nécessaire pour ne réaliser le code qui suit que lorsque la requête HTTP est terminée
            // On récupère la position du scroll (utile pour que le raffraichissement soit plus discret)
            let currentScroll = messages[0].scrollTop;
            
            messages[0].innerHTML = response.currentTarget.response;
            messages[0].scrollTo(0, currentScroll);
        }
    };

    httpRequest.open('POST', "php/webparts/chat.php", true);
    httpRequest.send();
};