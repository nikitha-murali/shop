<?php
   session_start();
   session_unset();
   session_destroy();
   header("location:index.php?با_موفقیت_خارج_شدید");
?>