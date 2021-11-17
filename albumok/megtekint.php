<?php
require_once '../altalanos/konfig.php';
include_once '../altalanos/fejlec.php';
// A további feldolgozás előtt az id paraméter meglétének ellenőrzése

if (isset($_GET['ID_album']) && !empty(trim($_GET['ID_album']))) {
    $id_album = htmlspecialchars(trim($_GET['ID_album']));
    // Select utasítás előkészítése
    $utasitas = $adatbazisom->prepare("SELECT
                                             albumok.*,
                                             eloadok.*,
                                             zeneszamok.*,
                                             kiadok.ID_kiado,
                                             kiadok.kiado_neve,
                                             kapcsolo_zene_album_eloado.focimdal_e
                                          FROM kapcsolo_zene_album_eloado
                                            INNER JOIN albumok
                                                ON kapcsolo_zene_album_eloado.album_ID = albumok.ID_album
                                            INNER JOIN eloadok
                                                ON kapcsolo_zene_album_eloado.eloado_ID = eloadok.ID_eloado
                                            INNER JOIN zeneszamok
                                                ON kapcsolo_zene_album_eloado.zene_ID = zeneszamok.ID_zenek
                                            INNER JOIN kiadok
                                                 ON eloadok.kiado_ID = kiadok.ID_kiado
                                        WHERE albumok.ID_album = :ID_album
                                        ORDER BY albumok.megjelenes ASC, zeneszamok.zene_cim ASC");


    // paraméterek kötése
    $utasitas->bindParam(':ID_album', $parameter_id);

    // paraméterek beállítása
    $parameter_id = trim($_GET["ID_album"]);

    $utasitas->execute();

    $eredmeny = $utasitas->fetchAll(PDO::FETCH_ASSOC);
    if (count($eredmeny) > 0) {
        $talal = false;
        $albumid = 0;
        foreach ($eredmeny as $adat) {
            $borito = $adat["borito"];
            $album = $adat["album_cime"];
            $megjelenes = $adat["megjelenes"];
            //főcímdal meglétének ellenőrzése
            if (($adat["focimdal_e"] != 0) && $talal == false) { //ha a focimdal nem 0 és a talal false, akkor
                $focimdal = $adat["zene_cim"]; //a focimdal-hoz rendeli a zene címét
                $focimdal_link = $adat["youtube_link"]; //elmenti a youtube linket ami a zenéhez tartozik
                $talal = true; //átálítja a talalt- true-ra , azaz megtalálta az utolsó album főcímdalát
            } else if ($talal == false) {
                $focimdal = "N\A"; //ha nem talált főcímdalt az adott albumhoz akkor nincs adat-ot jelenít meg
                $focimdal_link = "";
            }

            $profil = $adat["profil_kep"];
            $eloado = $adat["eloado_neve"];
            $debut = $adat["debut_ido"];
            $kiado = $adat["kiado_neve"];
            //fanclub név meglétének ellenőrzése
            if ($adat["fan_club"] != null) {   //ha van rajongóitábor név hozzárendeli
                $fanclub = $adat["fan_club"];
            } else {
                $fanclub = "N/A";  //ha nincs akkor kiírja hogy nincs adat
            }
        }
    } else {
        $utasitas3 = $adatbazisom->prepare("SELECT albumok.*, eloadok.*, 
                                                kiadok.ID_kiado, kiadok.kiado_neve
                                        FROM kapcsolo_zene_album_eloado
                                        INNER JOIN albumok
                                            ON kapcsolo_zene_album_eloado.album_ID = albumok.ID_album
                                        INNER JOIN eloadok
                                            ON kapcsolo_zene_album_eloado.eloado_ID = eloadok.ID_eloado
                                        INNER JOIN kiadok
                                            ON eloadok.kiado_ID = kiadok.ID_kiado
                                        WHERE ID_album=?");
        $utasitas3->bindParam(1, $id_album);
        $utasitas3->execute();
        $eredmeny3 = $utasitas3->fetchAll(PDO::FETCH_ASSOC);
        if (count($eredmeny3) > 0) {
            $talal = false;
            $albumid = 0;
            foreach ($eredmeny3 as $adat) {
                if (!empty($adat["borito"])) {
                    $borito = $adat["borito"];
                } else {
                    $borito = 'altalanos/kepek/album.png';
                }
                $album = $adat["album_cime"];
                $megjelenes = $adat["megjelenes"];
                //főcímdal meglétének ellenőrzése
                if (!empty(($adat["focimdal_e"])) && $talal == false) { //ha a focimdal nem 0 és a talal false, akkor
                    $focimdal = $adat["zene_cim"]; //a focimdal-hoz rendeli a zene címét
                    $focimdal_link = $adat["youtube_link"]; //elmenti a youtube linket ami a zenéhez tartozik
                    $talal = true; //átálítja a talalt- true-ra , azaz megtalálta az utolsó album főcímdalát
                } else if ($talal == false) {
                    $focimdal = "N\A"; //ha nem talált főcímdalt az adott albumhoz akkor nincs adat-ot jelenít meg
                    $focimdal_link = "";
                }

                $profil = $adat["profil_kep"];
                $eloado = $adat["eloado_neve"];
                if (!empty($adat["debut_ido"])) {
                    $debut = $adat["debut_ido"];
                } else {
                    $debut = 'N\A';
                }
                if (!empty($adat["kiado_neve"])) {
                    $kiado = $adat["kiado_neve"];
                } else {
                    $kiado = "N\A";
                }
                //fanclub név meglétének ellenőrzése
                if ($adat["fan_club"] != null) {   //ha van rajongóitábor név hozzárendeli
                    $fanclub = $adat["fan_club"];
                } else {
                    $fanclub = "N/A";  //ha nincs akkor kiírja hogy nincs adat
                }
            }
        } else {
?>
            <script>
                location.href = "../altalanos/nem_talalhato.php"
            </script>
    <?php
        }
    }


    ?>
    <div class="tartalom">
        <a class="vissza_link" href="albumok.php">Vissza az albumokhoz</a>
        <?php if (isset($_SESSION['felhasznalonev'])) { ?>
            <div class="muvelet">
                <a href="album_szerkesztese.php?ID_album=<?= $parameter_id; ?>">Album szerkesztése</a>
            </div>
        <?php } ?>

        <div class="osszegzes">
            <div class="info_sav_egy">
                <div class="kep_egy">
                    <?php echo "<img class='kep' src='../" . $borito . "'>"; ?>
                </div>
                <div class="leiras_egy">
                    <div class='cimsor'>
                        <h3>
                            <?php echo $album; ?>
                        </h3>
                    </div>

                    <div class='cim'>Előadó:</div>
                    <div class='cimleiras'>
                        <?php
                        echo " <a href='../eloadok/megtekint.php?ID_eloado=" . $adat["ID_eloado"] . "'>";
                        echo $eloado;
                        echo "</a>";
                        ?>
                    </div>

                    <div class='cim'>Megjelenés:</div>
                    <div class='cimleiras'>
                        <?php echo $megjelenes; ?>
                    </div>

                    <div class='cim'>Főcímdal:</div>
                    <div class='cimleiras'>
                        <?php
                        if ($focimdal_link != "") {
                            echo "<a href='" . $focimdal_link . "' target='_blank'>" . $focimdal . "  </a>";
                        } else {
                            echo $focimdal;
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="info_sav_ketto"></div>

            <div class="info_sav_harom">
                <div class="leiras_ketto">

                    <div class='cimsor'>
                        <h3>
                            <?php
                            echo " <a href='../eloadok/megtekint.php?ID_eloado=" . $adat["ID_eloado"] . "'>";
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

                    <div class='cim'>Kiadó:</div>
                    <div class='cimleiras'>
                        <?php
                        echo " <a href='../kiadok/megtekint.php?ID_kiado=" . $adat["ID_kiado"] . "'>";
                        echo $kiado;
                        echo "</a>";
                        ?>
                    </div>

                </div>
                <div class="kep_ketto">
                    <?php echo "<img class='kep' src=../" . $profil . ">"; ?>
                </div>
            </div>
        </div>

        <div class="focimsor">
            <div class="cimke1">
                <h4>Albumborító</h4>
            </div>
            <div class="cimke2">
                <h4>Zenék</h4>
            </div>
            <div class="cimke3">
                <h4>Előadó</h4>
            </div>
            <div class="cimke4">
                <h4>Lehetőségek</h4>
            </div>
        </div>
        <?php

        foreach ($eredmeny as $adat) {
            echo "<div class='sor'>"; //sorba illeszti be a következőket
            //borítokép beillesztése
            echo "<div class='cimke1'>";
            echo "<img class='kep' src=../" . $adat["borito"] . ">";
            echo "</div>";
            //dal címének beillesztése
            echo "<div>";
            echo "<div class='cimke2'>";
            echo " <a href='../zenek/megtekint.php?ID_zenek=" . $adat["ID_zenek"] . "'>";
            echo $adat["zene_cim"];
            echo "</a>";
            echo "</div>";
            echo "</div>";
            //előadó nevének beillesztése
            echo "<div>";
            echo "<div class='cimke3'>";
            echo " <a href='../eloadok/megtekint.php?ID_eloado=" . $adat["ID_eloado"] . "'>";
            echo $adat["eloado_neve"];
            echo "</a>";
            echo "</div>";
            echo "</div>";

            //lehetőségek beillesztése
            if (isset($_SESSION['felhasznalonev'])) {
                echo "<div class='ikonok'>";
                if ($adat["youtube_link"] != null) {
                    echo "<div class='video'><a href='" . $adat["youtube_link"] . "'title='Videó' class='fab videolink' target='_blank'>&#xf167;</a></div>";
                } else {
                    echo "<div class='video'><i class='fab nincs'>&#xf167;</i></div>";
                }
                echo "<div class='szoveg'><a href='../zenek/megtekint.php?ID_zenek=" . $adat['ID_zenek'] . "'title='Dalszöveg' class='fas dalszoveg'>&#xf15c;</a></div>";
                echo "<div class='szerkeszto'><a href='../zenek/zene_szerkeszt.php?ID_zenek=" . $adat["ID_zenek"] . "'title='Szerkesztés' class='fas szerkeszto'>&#xf303;</a></div>";
                echo "</div>";
            } else {
                echo "<div class='ikonok_lat'>";
                if ($adat["youtube_link"] != null) {
                    echo "<div class='video'><a href='" . $adat["youtube_link"] . "'title='Videó' class='fab videolink' target='_blank'>&#xf167;</a></div>";
                } else {
                    echo "<div class='video'><i class='fab nincs'>&#xf167;</i></div>";
                }
                echo "<div class='szoveg'><a href='../zenek/megtekint.php?ID_zenek=" . $adat['ID_zenek'] . "'title='Dalszöveg' class='fas dalszoveg'>&#xf15c;</a></div>";
                echo "</div>";
            }
            echo "</div>";
        }

        ?>
    </div>

<?php
} else {
    echo "<script>location.href = '../altalanos/hiba.php';</script>";
} // utasítás felszabadítása
unset($eredmeny);

// Adatbáziskapcsolat lezárása
unset($adatbazisom);
include_once '../altalanos/lablec.php';
?>