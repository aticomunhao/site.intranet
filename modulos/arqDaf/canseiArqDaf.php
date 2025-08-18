<?php
session_name("arqAdm"); // sessão diferente da CEsB
session_destroy();
header("Location: ./indexc.php");