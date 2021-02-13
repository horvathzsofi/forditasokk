
<?php
require_once'../altalanos/konfig.php';
include_once '../altalanos/fejlec.php';
if(isset($_SESSION['felhasznalonev'])){
    $id_felhasznalo=$_SESSION['id_felhasznalo'];
}else{
    $id_felhasznalo='';
}
$hozzaadott=false; //ellenőrizni hogy a felhasználó már adott e hozzá fordítást

// A további feldolgozás előtt az id paraméter meglétének ellenőrzése
if (isset($_GET['ID_zenek']) && !empty(trim($_GET['ID_zenek']))) {
    $id_zene = htmlspecialchars(trim($_GET["ID_zenek"]));
    
    try {
        $lekerdezes = "SELECT
                    zeneszamok.*, albumok.*, eloadok.*, kiadok.*, forditasok.*,
                     felhasznalok.ID_felhasznalo, felhasznalok.felhasznalonev
                 FROM kapcsolo_zene_album_eloado
                   INNER JOIN albumok
                       ON kapcsolo_zene_album_eloado.album_ID = albumok.ID_album
                   INNER JOIN eloadok
                       ON kapcsolo_zene_album_eloado.eloado_ID = eloadok.ID_eloado
                   INNER JOIN zeneszamok
                       ON kapcsolo_zene_album_eloado.zene_ID = zeneszamok.ID_zenek
                   LEFT JOIN kiadok
                        ON eloadok.kiado_ID = kiadok.ID_kiado
                 LEFT JOIN forditasok
                 ON forditasok.zene_ID=zeneszamok.ID_zenek
                 LEFT JOIN felhasznalok
                 ON ID_felhasznalo=forditasok.felhasznalo_ID
               WHERE zeneszamok.ID_zenek = " . $id_zene;

        $utasitas = $adatbazisom->query($lekerdezes);
        $eredmeny = $utasitas->fetchAll(PDO::FETCH_ASSOC);

        if (count($eredmeny) > 0) {
            foreach ($eredmeny as $adat) {
                //album borítóképe
                if(!empty($adat['ID_album'])){
                    $id_album=$adat['ID_album'];
                    $borito = $adat["borito"];
                    $album = $adat["album_cime"];
                }else{
                    $id_album='';
                    $borito = $album = "N\A";
                }
                //album megjelenése
                if (!empty($adat["megjelenes"])) {
                    $megjelenes = $adat["megjelenes"];
                } else {
                    $megjelenes = "N\A";
                }
                //zene címe
                if (!empty($adat["zene_cim"])) {
                    $zene = $adat["zene_cim"];
                } else {
                    $zene = "";
                }
                //dalszöveg
                if (!empty($adat["dalszoveg"])) {
                    $dalszoveg = $adat["dalszoveg"];
                    $nincs_szoveg=false;
                } else {
                    $nincsszoveg = "Még nem érhető el dalszöveg";
                    $dalszoveg = "Dalszöveg hozzáadásához jelentkezz be!";
                    $nincs_szoveg=true;
                    if (isset($_SESSION['felhasznalonev'])) {
                        $dalszoveg = "<a href='http://localhost/forditasokk/zenek/zene_szerkeszt.php?ID_zenek=" . $id_zene . "'>Dalszöveg hozzáadása</a>";
                    }
                }
                //eloadó profilképe
                if (!empty($adat["profil_kep"])) {
                    $profil = $adat["profil_kep"];
                } else {
                    $profil = 'altalanos/kepek/artist.png';
                }
                //előadó
                if (!empty($adat["eloado_neve"])) {
                    $eloado = $adat["eloado_neve"];
                    $id_eloado=$adat["ID_eloado"];
                } else {
                    $eloado = "N/A";
                    $id_eloado='';
                }
                //debüt  idő
                if (!empty($adat["debut_ido"])) {
                    $debut = $adat["debut_ido"];
                } else {
                    $debut = "N\A";
                }
                //kiadó
                if (!empty($adat["kiado_neve"])) {
                    $kiado = $adat["kiado_neve"];
                    $id_kiado=$adat["ID_kiado"];
                } else {
                    $kiado = '"N\A"';
                    $id_kiado='';
                }
                //fanclub név meglétének ellenőrzése
                if ($adat["fan_club"] != null) {   //ha van rajongóitábor név hozzárendeli
                    $fanclub = $adat["fan_club"];
                } else {
                    $fanclub = "N/A";  //ha nincs akkor kiírja hogy nincs adat
                }
                if(!empty($adat['dal_forditott_cime'])){
                    $elerheto_forditas=true;
                    $forditott_cim=$adat['dal_forditott_cime'];
                    $forditas=$adat['forditas'];
                }else{
                    $elerheto_forditas=false;
                    $forditott_cim='Még nem érhető el fordítás';
                    $forditas='Fordítás hozzáadásához jelentkezz be!';
                    if (isset($_SESSION['felhasznalonev'])) {
                        $forditas = "<a href='http://localhost/forditasokk/forditasok/forditas_hozzaad.php?ID_zenek=" . $id_zene . "'>Fordítás hozzáadása</a>";
                    }
                }
                if($adat['ID_felhasznalo']==$id_felhasznalo)
                {
                    $hozzaadott=true;
                }
            }
        } else {
            ?>
            <script>location.href = "../altalanos/nem_talalhato.php"</script>
            <?php
        }
    } catch (Exception $exc) {
        echo $exc->getMessage();
    }
} else {
    ?>
    <script>location.href = "../altalanos/hiba.php"</script>
    <?php
}
?>

<div  class="tartalom">
    <div class="vissza_link">
        <a href="zenek.php">Vissza a zenékhez</a>
    </div>
    <div class='muvelet'>
<?php
if (isset($_SESSION['tipus']) == 'Rendszergazda' || isset($_SESSION['tipus']) == 'Admin' || isset($_SESSION['tipus']) == 'Felhasznalo') {
    echo "<a href='zene_szerkeszt.php?ID_zenek=" . $id_zene . "'>Zene szerkesztése</a> <br>";
    
    if($hozzaadott==true){
         echo "<a href='../felhasznalo/forditasom_szerkesztese.php?ID_zenek=" . $id_zene . "'>Fordításom szerkesztése</a>";
    }else{
        echo "<a href='../forditasok/forditas_hozzaad.php?ID_zenek=" . $id_zene . "'>Új fordítás hozzáadása</a>";
    }
}
?>
    </div>
    <div class="osszegzes">
        <div class="info_sav_egy">
            <div class='kep_egy'>
                <?php echo "<img class='kep' src=../" . $borito . ">"; ?>
            </div>
            <div class="leiras_egy">
                <div class='cimsor'>
                    <h3>
                        <?php echo $zene; ?>
                    </h3>
                </div>
                <div class='cim'>Előadó:</div>
                <div class='cimleiras'>
                    <?php
                    echo " <a href='http://localhost/forditasokk/eloadok/megtekint.php?ID_eloado=" . $adat["ID_eloado"] . "'>";
                    echo $eloado;
                    echo "</a>";
                    ?>
                </div>
                <div class='cim'>Album:</div>
                <div class='cimleiras'>
                    <?php
                    echo " <a href='http://localhost/forditasokk/albumok/megtekint.php?ID_album=" . $adat["ID_album"] . "'>";
                    echo $album;
                    echo "</a>";
                    ?>
                </div>

                <div class='cim'>Megjelenés:</div>
                <div class='cimleiras'>
                    <?php echo $megjelenes; ?>
                </div>
            </div>
        </div>
        <div class="info_sav_ketto"></div>
        <div class="info_sav_harom">
            <div class="leiras_ketto">

                <div class='cimsor'>
                    <h3>
                        <?php
                        echo " <a href='http://localhost/forditasokk/eloadok/megtekint.php?ID_eloado=" . $adat["ID_eloado"] . "'>";
                        echo $eloado;
                        echo "</a>";
                        ?>
                    </h3>
                </div>

                <div class='cim'>Rajongótábor:</div>
                <div class='cimleiras'>
                    <?php echo $fanclub; ?><br>
                </div>

                <div class='cim'>Debüt:</div>
                <div class='cimleiras'>
                    <?php echo $debut; ?>
                </div>

                <div class='cim'>Kiado:</div>
                <div class='cimleiras'>
                    <?php
                    echo "<a href='http://localhost/forditasokk/kiadok/megtekint.php?ID_kiado=" . $adat['ID_kiado'] . "'>";
                    echo $kiado;
                    echo "</a>";
                    ?>
                </div>

            </div>
            <div class="kep_ketto">
                <?php echo"<img class='kep' src=../" . $profil . ">"; ?>
            </div>
        </div>
    </div>
    
    <div class='dalszovegek'>
        <?php
            if($elerheto_forditas){
        ?>
        <div class='forditas fordito'> Fordította:
            <form method="post" action="">
                <input type='hidden' name='muvelet' value="modosit" >
                <select name="forditok" onchange="this.form.submit()">
                    <option selected disabled>Válassz fordítót</option>
                        <?php
                        foreach ($eredmeny as $adat2) {
                            if ($_POST["forditok"] == $adat2["ID_felhasznalo"])
                            {
                                echo "<option value='" . $adat2["ID_felhasznalo"] . "' selected>" . $adat2["felhasznalonev"] . "</option>";
                            } else {
                                echo "<option value='" . $adat2["ID_felhasznalo"] . "'>" . $adat2["felhasznalonev"] . "</option>";
                            }
                        }
                        ?>
                </select>
            </form>
        </div>
        <?php
            }else{
                
                echo "<div class='forditas'>";
                echo "<h3>".$forditott_cim."</h3>";
                echo $forditas;
                echo "</div>";
            }
        ?>
                
        
            <div  class='zeneszoveg'>
              
                    <h3>
                    <?php
                    if($nincs_szoveg){
                        echo $nincsszoveg;
                    }
                    else{
                        echo $zene;
                    }
                    ?>
                    </h3>
         
           
                <?php echo nl2br($dalszoveg); ?>
            </div>
   
<?php
if (isset($_POST["muvelet"]) && $_POST["muvelet"] == "modosit") {
    $lekerdezes .= " AND felhasznalok.ID_felhasznalo = " . $_POST["forditok"] . ";";
    $utasitas2 = $adatbazisom->query($lekerdezes);
    $utasitas2->execute();
    $eredmeny2 = $utasitas2->fetchAll(PDO::FETCH_ASSOC);

    foreach ($eredmeny2 as $adat2) {
        $forditott_cim = $adat2["dal_forditott_cime"];
        $forditas = $adat2["forditas"];
        $fordito = $adat2["felhasznalonev"];
    }
    if (isset($adat2["dal_forditott_cime"]) && isset($forditas) && !empty($adat2["dal_forditott_cime"]) && !empty($forditas)) {
        echo "<div class='forditas'>";
        if(strlen($forditas)>300){
        if($id_felhasznalo==$_POST['forditok']){
            echo "<div>"
                . "<h3>" . $adat2["dal_forditott_cime"];
            ?>
        <span class='szerkesztes'>
            <a href="<?php echo '../felhasznalo/forditasom_szerkesztese.php?ID_zenek=' . $id_zene; ?>" title='Szerkesztés' class='fas szerkesztes'>&#xf303;</a>
        </span>
            <?php
            echo "</h3></div>";
        }else{
            echo "<div>"
                . "<h3>" . $adat2["dal_forditott_cime"] . "</h3>"
                ."</div>";
        }
        echo "<div>" . nl2br($forditas) . "</div>";
        }else {
            echo "<div>"
                ."<h3 class='hiba'>Hibás tartalom.</h3>"
            ."</div>";
        }
        echo"</div>";
    }
}
?>


    
    </div>

</div>

<?php
// utasítás felszabadítása
unset($eredmeny);
unset($eredmeny2);

// Adatbáziskapcsolat lezárása
unset($adatbazisom);
include_once '../altalanos/lablec.php';
?>