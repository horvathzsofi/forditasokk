<?php
require_once'../../altalanos/konfig.php';
include_once '../../altalanos/fejlec.php'; //fejléc beszúrása
include_once '../../altalanos/menu.php'; //navigációs sáv beszúrása

if (isset($_SESSION['felhasznalonev']) && $_SESSION['tipus'] == 'Rendszergazda') {   //csak akkor megy tovább ha a bejelentkezett felhasználó
    //redndszergazda jogosultsággal rendelkezik
    $id = $felhasznalo = $tipusa = $aktiv_e = "";
    if (isset($_GET['ID_felhasznalo']) && !empty(trim($_GET['ID_felhasznalo']))) {  //akkor tölt be ha van egy ID aminek az adatait megjelenítse
    //ha nincs akkor átirányít egy nem található tartalom oldalra
        $id = trim($_GET['ID_felhasznalo']);

        $utasitas = $adatbazisom->prepare("SELECT ID_felhasznalo, felhasznalonev, tipus, aktiv
                                        FROM felhasznalok
                                        WHERE ID_felhasznalo = :ID_felhasznalo");
        $utasitas->bindParam(':ID_felhasznalo', $parameter_id);
        $parameter_id = $id;

        if ($utasitas->execute()) {
            $eredmeny = $utasitas->fetchALL(PDO::FETCH_ASSOC);
            if (count($eredmeny) > 0) {
                foreach ($eredmeny as $adatsor) {
                    $felhasznalo = $adatsor['felhasznalonev'];
                    $tipusa = $adatsor['tipus'];
                    $aktiv_e = $adatsor['aktiv'];
                    $id = $adatsor['ID_felhasznalo'];
                }
            } else {
                ?>
                <script>location.href = "http://localhost/forditasokk/altalanos/nem_talalhato.php";</script>
                <?php
                exit();
            }
        } 
    }  else {
    ?>
    <script>location.href = "../../altalanos/hiba.php"</script>
    <?php
}
    unset($eredmeny);

    if (isset($_POST['frissit'])) {
        //    $felhasznalo;
        //    $id;
        $tipusa = $_POST['jogosultsag'];
        $aktiv_e = $_POST['aktiv_e'];

        $utasitas = $adatbazisom->prepare("UPDATE felhasznalok SET felhasznalonev=:felhasznalonev, tipus=:tipus, aktiv=:aktiv WHERE ID_felhasznalo=:id;");
        $utasitas->bindParam(':felhasznalonev', $felhasznalo);
        $utasitas->bindParam(':tipus', $tipusa);
        $utasitas->bindParam(':aktiv', $aktiv_e);
        $utasitas->bindParam(':id', $id);

        if ($utasitas->execute()) {
            header("location: felhasznalok_kezelese.php");
            exit();
        } else {
            echo "Valami hiba történt";
        }
    }

?>
<div class="tartalom">
    <div class="focim">
        <h2>Felhasználó adatainak módosítása</h2>
    </div>
    
    <form method="POST" class="minden_form">
        <div>
            <label> Felhasználó neve</label> <br>
            <input type="text" name="felhasznalonev" value="<?php echo $felhasznalo; ?>" readonly>
        </div>
        <div>
            <label> Típusa </label> <br>
            <select name="jogosultsag">
                <?php
                    if ($tipusa == "Rendszergazda") {
                        echo "<option selected>" . $tipusa . "</option>";
                    } else {
                        echo "<option>Rendszergazda</option>";
                    }
                    if ($tipusa == "Admin") {
                        echo "<option selected>" . $tipusa . "</option>";
                    } else {
                        echo "<option>Admin</option>";
                    }
                    if ($tipusa == "Felhasználó") {
                        echo "<option selected>" . $tipusa . "</option>";
                    } else {
                        echo "<option>Felhasználó</option>";
                    }
                ?>
            </select>
        </div>
        <div>
            <label> Aktivált fiók </label> <br>
            <select name="aktiv_e">
                <?php
                if ($aktiv_e == 1) {
                    echo "<option value=1 selected>IGEN</option>";
                } else {
                    echo "<option value=1>IGEN</option>";
                }
                if ($aktiv_e == 0) {
                    echo "<option value=0 selected>NEM</option>";
                } else {
                    echo "<option value=0 >NEM</option>";
                }
                ?>
            </select>
        </div>
        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
       
        <button type="submit" class="gomb" name="frissit">Adatok frissítése</button>
    </form>
</div>
<?php
              } else{
?>
    <script>location.href="http://localhost/forditasokk/kezdolap.php"</script>
<?php
}
include_once '../../altalanos/lablec.php';
?>