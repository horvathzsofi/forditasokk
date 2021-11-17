<?php
require_once '../altalanos/konfig.php';
include_once '../altalanos/fejlec.php';

$van_uzenet = $hiba = false;
$hiba_uzenet = '';
$siker_uzenet = '';
if (isset($_GET['email']) && isset($_GET['hash'])) {
    $mail = $_GET['email'];
    $hash = $_GET['hash'];
} else {
    $mail = $hash = '';
}
if (isset($_POST['uj_jelszo'])) {

    $jsz = $_POST['jelszo'];
    $jsz2 = $_POST['jelszo2'];

    if ($jsz == $jsz2) {
        if (!empty($mail) && !empty($hash)) {
            $jsz = password_hash($_POST['jelszo'], PASSWORD_DEFAULT);

            $utasitas = $adatbazisom->prepare('UPDATE felhasznalok SET jelszo=? WHERE email=? AND hash=?;');
            $utasitas->bindParam(1, $jsz);
            $utasitas->bindParam(2, $email);
            $utasitas->bindParam(3, $hash);
            if ($utasitas->execute()) {
                $siker_uzenet = '';
                $van_uzenet = true;
                $siker_uzenet = "Az új jelszó sikeresen beállítva.";
            } else {
                $hiba_uzenet = '';
                $hiba = $van_uzenet = true;
                $hiba_uzenet = "Az új jelszót nem sikerült beállítani.";
            }
        } else {
            $hiba_uzenet = '';
            $hiba = $van_uzenet = true;
            $hiba_uzenet = "Az új jelszót nem sikerült beállítani.";
        }
    } else {
        $hiba_uzenet = '';
        $hiba = $van_uzenet = true;
        $hiba_uzenet = "A megadott jelszavak nem egyeznek.";
    }
}
?>
<div class="tartalom">
    <?php if ($van_uzenet == true) { ?>
        <div class="uzenet_van">
            <?php
            if ($hiba) {
                echo "<div class='hiba_uzi'>" . $hiba_uzenet . "</div>";
                $hiba_uzenet = '';
                $van_uzenet = false;
            }
            if (!$hiba) {
                echo "<div class='siker_uzi'>" . $siker_uzenet . "</div>";
                $siker_uzenet = '';
                $van_uzenet = false;
            }
            ?>
        </div>
    <?php
    }
    ?>
    <div class="focim">
        <h2>Új jelszó beállítása</h2>
    </div>

    <form method="POST" class="minden_form">
        <div>
            <label for="jelszo">Új jelszó</label> <br>
            <input type="password" id="jelszo" name="jelszo" placeholder="Új jelszó" required />
        </div>
        <div>
            <label for="jelszo">Új jelszó újra</label> <br>
            <input type="password" id="jelszo2" name="jelszo2" placeholder="Ismételd meg a jelszót" required />
        </div>
        <button class="gomb" type="submit" name="uj_jelszo">Jelszó frissítése</button>
        <div class="linkek">
            <div class="link bal"></div>
            <div class="link jobb"><a href="bejelentkezes.php">Vissza a bejelentkezés</a></div>
        </div>
    </form>
</div>

<?php
include_once '../altalanos/lablec.php';
?>