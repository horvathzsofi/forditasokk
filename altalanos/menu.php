<?php
session_start();
?>
<div class="menu">
    <div class="logo">
        <a href="http://localhost/forditasokk/kezdolap.php">
            <img id="logo_kep" src="http://localhost/forditasokk/altalanos/kepek/asdasd.png" >
        </a>

        <form method="GET" id="kereses" name="kereses" action="http://localhost/forditasokk/altalanos/kereses_eredmenye.php">
            
                <input class="keres" id="keres" type="text" name="keres" placeholder="Keresés...">
                <button class="keres" id="kereso" type="submit" name="kereses" >Keresés</button>
            
        </form>   

    </div>
  
    <button onclick="legordules()" id="menu_gomb">
        <i class="fas fa-bars"> </i>
    </button>
<nav>
    <div id="legordulo_lista">
        <a href="http://localhost/forditasokk/kezdolap.php">Kezdőlap</a>
        <a href="http://localhost/forditasokk/albumok/albumok.php">Albumok</a> 
        <a href="http://localhost/forditasokk/eloadok/eloadok.php">Előadók</a> 
        <a href="http://localhost/forditasokk/zenek/zenek.php">Zenék</a>
        <a href="http://localhost/forditasokk/forditasok/forditasok.php">Fordítások</a>
        <a href="http://localhost/forditasokk/kiadok/kiadok.php">Kiadók</a>
    </div>
</nav>

</div>