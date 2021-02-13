<?php
require_once'../altalanos/konfig.php';
include_once '../altalanos/fejlec.php';

if (isset($_SESSION['felhasznalonev'])) {
    ?>
    <script>location.href = "../kezdolap.php"</script>
    <?php
} // ha van felhasználó bejelentkezés a kezdőlapra irányít   
$hiba=$uzenet=false;
$hiba_uzenet='';
$siker_uzenet='';
if (isset($_POST['bejelentkezes'])) {
    $felhasznalo_neve = $_POST['felhasznalonev'];
    $jsz = $_POST['jelszo'];

    try {

        $utasitas = $adatbazisom->prepare('SELECT * FROM felhasznalok WHERE felhasznalonev=?');
        $utasitas->bindParam(1, $felhasznalo_neve);
        $utasitas->execute();

        $adat = $utasitas->fetch();
        $adb_jsz = $adat['jelszo'];
        $felhasznalo = $adat['felhasznalonev'];
        $tipusa = $adat['tipus'];
        $aktivalt = $adat['aktiv'];
        $id_felhasznalo = $adat['ID_felhasznalo'];

        $megerosit = password_verify($jsz, $adb_jsz);
        if ($megerosit && ($felhasznalo_neve == $felhasznalo)) {
            if ($aktivalt == '1') {
                $_SESSION['felhasznalonev'] = $felhasznalo;
                $_SESSION['tipus'] = $tipusa;
                $_SESSION['id_felhasznalo'] = $id_felhasznalo;
                ?>
                <script>location.href = "../kezdolap.php"</script>

                <?php
            } else {
                $hiba_uzenet='';
                $hiba=$uzenet=true;
                $hiba_uzenet = "A fiókot még nem aktiválták. Nyissa meg e-mail fiókját és kattintson az aktiválás linkjére.";
            }
        } else {
            $hiba_uzenet='';
            $hiba=$uzenet=true;
            $hiba_uzenet = "Nem megfelelő felhasználónév vagy jelszó.";
        }
    } catch (Exception $exc) {
        echo $exc->getMessage();
        header("location: ../altalanos/hiba.php");
        exit();
    }
}

if (isset($_GET['email']) && !empty($_GET['email'])) {
    if (isset($_GET['hash']) && !empty($_GET['hash'])) {
        $email = $_GET['email'];
        $hash = $_GET['hash'];
        $aktivalt_n = 0;
        $aktivalt_i = 1;

        $utasitas = $adatbazisom->prepare('SELECT * FROM felhasznalok WHERE email=? AND hash=? AND aktiv=?');
        $utasitas->bindParam(1, $email);
        $utasitas->bindParam(2, $hash);
        $utasitas->bindParam(3, $aktivalt_n);

        $utasitas->execute();

        if ($utasitas->rowCount()) {
            $utasitas = $adatbazisom->prepare('UPDATE felhasznalok SET aktiv=? WHERE email=? AND hash=?;');
            $utasitas->bindParam(1, $aktivalt_i);
            $utasitas->bindParam(2, $email);
            $utasitas->bindParam(3, $hash);
            if ($utasitas->execute()) {
                $siker_uzenet='';
                $uzenet=true;
                $siker_uzenet= 'Aktiváltad fiókod, már be tudsz jelentkezni!';
            } else {
                $hiba_uzenet='';
                $hiba=$uzenet=true;
                $hiba_uzenet = "Érvénytelen vagy már felhasznált aktiváló link!";
            }
        } else {
            $hiba_uzenet='';
            $hiba=$uzenet=true;
            $hiba_uzenet = "Érvénytelen próbálkozás, kérjük használja az e-mailben elküldött linket!";
        }
    }
}

?>
<div class="tartalom">
    <?php if($uzenet==true){?>
    <div class="uzenet_van">
        <?php
            if($hiba){
                echo "<div class='hiba_uzi'>".$hiba_uzenet."</div>";
                $hiba_uzenet='';
                $uzenet=false;
            }
            if(!$hiba){
                echo "<div class='siker_uzi'>".$siker_uzenet."</div>";
                $siker_uzenet='';
                $uzenet=false;
            }
        ?>
    </div>
        <?php
        }
    ?>
    <div class="focim">
        <h2>Bejelentkezés</h2>
    </div>
    
    <form method="POST" class="minden_form">
        <div>
            <label for="felhasznalonev">Felhasználónév</label> <br>
            <input type="text" name="felhasznalonev" placeholder="Felhasználónév" required />
        </div>

        <div>
            <label for="jelszo">Jelszó</label> <br>
            <input type="password" name="jelszo" placeholder="Jelszó" required />
        </div>

        <button class="gomb" type="submit" name="bejelentkezes">Bejelentkezés</button>
        <div class="linkek">
            <div class="link bal"><a href="elfelejtett_jelszo.php">Elfelejtett jelszó</a></div>
            <div class="link jobb"><a href="regisztracio.php">Regisztráció</a></div>
        </div>
    </form>
</div>
<?php

include_once '../altalanos/lablec.php';
?>