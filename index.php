      
<?php
session_start();
require_once 'Controller.php';
Formo::getnote ();
?>
 
     
<style>
      
p.date   {
    text-align: justify;
    direction: rtl;
    font-size: x-large;
    line-height: 1.5;
    padding: 12px;
    margin: 3px;
    background: wheat;
    border-radius: 25px;
}

div.row{
    direction: rtl;
    font-size: larger;
    padding: 12px;
    margin: 23px;
    text-align: justify;
    background: rgba(8, 8, 8, 0.14);
    border-radius: 12px;
}

p{
    background: rgb(236, 234, 234);
    font-size: x-large;
    font-weight: bold;
    font-style: normal;
    border-radius: 25px;
    padding: 12px;
    margin: 12px;
}
    
    
div.comments {
    text-align: center;
    font-size: large;
    font-weight: bold;
    color: #0a88ec;
}  
      
      
</style>
