<?php
session_start();
?>
<div class="menu">
    <div class="logo">
        <a href="../index.php">
            <img id="logo_kep" src="/altalanos/kepek/asdasd.png" >
        </a>

        <form method="GET" id="kereses" name="kereses" action="/altalanos/kereses_eredmenye.php">
            
                <input class="keres" id="keres" type="text" name="keres" placeholder="Keresés...">
                <button class="keres" id="kereso" type="submit" name="kereses" >Keresés</button>
            
        </form>   

    </div>
  
    <button onclick="legordules()" id="menu_gomb">
        <i class="fas fa-bars"> </i>
    </button>
<nav>
    <div id="legordulo_lista">
        <a href="/index.php">Kezdőlap</a>
        <a href="/albumok/albumok.php">Albumok</a> 
        <a href="/eloadok/eloadok.php">Előadók</a> 
        <a href="/zenek/zenek.php">Zenék</a>
        <a href="/forditasok/forditasok.php">Fordítások</a>
        <a href="/kiadok/kiadok.php">Kiadók</a>
    </div>
</nav>

</div>