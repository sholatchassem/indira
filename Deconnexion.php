<?php
session_start();
session_destroy(); // Détruit la session en cours
header("Location: ENREGISTREMENT.PHP"); // Redirige vers la page de connexion
exit();
?>
<Style>
    a{
        text-decoration:none;
    }
</style>