<div class="oldalsav">
    <div class="felhasznalo">
    <?php
    if (isset($_SESSION['felhasznalonev'])) {
        ?>
        <button id="felhasznalonev" onclick="legordules1()">
            
        <?php echo $_SESSION["felhasznalonev"]; ?>
        
        </button>
        <div id="legordulo_lista1">
        <a href="../felhasznalo/forditasaim.php">Fordításaim</a>

        <?php
        if ($_SESSION['tipus'] == 'Rendszergazda' || $_SESSION['tipus'] == 'Admin') {
            ?>
            <a href="../admin/eloadok_kezelese.php">Előadók kezelése</a>
            <a href="../admin/albumok_kezelese.php">Albumok kezelése</a>
            <a href="../admin/zenek_es_forditasok_kezelese.php">Zenék és fordítások kezelése</a>
        <?php } //admin és rendszergazda jogosultsággaé rendelkezők menüjének lezárása   ?>       

        <?php if ($_SESSION['tipus'] == 'Rendszergazda') { ?>
            <a href="../rendszergazda/felhasznalok/felhasznalok_kezelese.php">Felhasználók kezelése</a>
            
            <?php
        } //rendszergazda jogosultság menü lezárása
        ?>
            <div class="logout">
            <a href="../felhasznalo/kijelentkezes.php">Kijelentkezés</a>
            </div>
        </div>
            
            <?php
    } //if isset felhasználónév lezárása
    else {
        ?>
        <div class="login">
        <a  href="../felhasznalo/bejelentkezes.php">Bejelentkezés</a>
        
        <a href="../felhasznalo/regisztracio.php">Regisztráció</a>
        </div>
    <?php
} //else ág lezárása
?>
</div>
</div>