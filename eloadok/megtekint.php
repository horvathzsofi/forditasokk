<?php
require_once'../altalanos/konfig.php';
include_once '../altalanos/fejlec.php';


// A további feldolgozás előtt az id paraméter meglétének ellenőrzése
if (isset($_GET['ID_eloado']) && !empty(trim($_GET['ID_eloado']))) {

    // Select utasítás előkészítése
    $utasitas = $adatbazisom->prepare("SELECT albumok.*, eloadok.*, zeneszamok.*,
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
                               WHERE eloadok.ID_eloado = :ID_eloado
                               ORDER BY albumok.megjelenes ASC");

    $utasitas2 = $adatbazisom->prepare("SELECT
                                             albumok.*,
                                             eloadok.ID_eloado,
                                             eloadok.eloado_neve,
                                             zeneszamok.*,
                                             kapcsolo_zene_album_eloado.focimdal_e
                                        FROM kapcsolo_zene_album_eloado
                                            INNER JOIN albumok
                                                ON kapcsolo_zene_album_eloado.album_ID = albumok.ID_album
                                            INNER JOIN eloadok
                                                ON kapcsolo_zene_album_eloado.eloado_ID = eloadok.ID_eloado
                                            INNER JOIN zeneszamok
                                                ON kapcsolo_zene_album_eloado.zene_ID = zeneszamok.ID_zenek
                                        WHERE eloadok.ID_eloado = :ID_eloado
                                        ORDER BY albumok.megjelenes DESC, zeneszamok.zene_cim ASC");

    // paraméterek kötése
    $utasitas->bindParam(':ID_eloado', $parameter_id);
    $utasitas2->bindParam(':ID_eloado', $parameter_id);
    // paraméterek beállítása
    $parameter_id = trim($_GET["ID_eloado"]);

    $utasitas->execute();
    $utasitas2->execute();

    $eredmeny = $utasitas->fetchAll(PDO::FETCH_ASSOC);
    $eredmeny2 = $utasitas2->fetchAll(PDO::FETCH_ASSOC);

    $talal = false;
    $albumid = 0;
    if (count($eredmeny) > 0) {
        foreach ($eredmeny as $adat) {
            $borito = $adat["borito"];
            $megjelenes = $adat["megjelenes"]; //hozzárendeli az utolsó album megjelenését
            $eloado = $adat["eloado_neve"];
            $profil = $adat["profil_kep"];
            $debut = $adat["debut_ido"];
            $kiado = $adat["kiado_neve"];
            $id_kiado = $adat["kiado_ID"];
            $id_zene = $adat["ID_zenek"];
            $zene_cime = $adat["zene_cim"];
            if ($albumid != $adat["ID_album"]) {
                $albumid = $adat["ID_album"]; //az albumid-hoz rendeli az utolsó ID_album-ot
                $talal = false;
                $album_cime = $adat['album_cime'];
            }
            //fanclub név meglétének ellenőrzése
            if ($adat["fan_club"] != null) {   //ha van rajongóitábor név hozzárendeli
                $fanclub = $adat["fan_club"];
            } else {
                $fanclub = "N/A";  //ha nincs akkor kiírja hogy nincs adat
            }

            //főcímdal meglétének ellenőrzése
            if (($adat["focimdal_e"] != 0) && $talal == false) { //ha a focimdal nem 0 és a talal false, akkor
                $focimdal = $adat["zene_cim"]; //a focimdal-hoz rendeli a zene címét
                $focimdal_link = $adat["youtube_link"]; //elmenti a youtube linket ami a zenéhez tartozik
                $talal = true; //átálítja a talalt- true-ra , azaz megtalálta az utolsó album főcímdalát
            } else if ($talal == false) {
                $focimdal = "N\A"; //ha nem talált főcímdalt az adott albumhoz akkor nincs adat-ot jelenít meg
                $focimdal_link = "";
            }
            //kapcsolatok meglétének ellenőrzése
            if ($adat["youtube"] != null) {
                $youtube = "<a class='ikonszoveg' href='" . $adat["youtube"] . "'target='_blank'>YouTube</a>";
            } else {
                $youtube = "N\A";
            }
            if ($adat["facebook"] != null) {
                $facebook = "<a class='ikonszoveg' href='" . $adat["facebook"] . "'target='_blank'>facebook</a>";
            } else {
                $facebook = "N\A";
            }
            if ($adat["instagram"] != null) {
                $instagram = "<a class='ikonszoveg' href='" . $adat["instagram"] . "'target='_blank'>Instagram</a>";
            } else {
                $instagram = "N\A";
            }
            if ($adat["twitter"] != null) {
                $twitter = "<a class='ikonszoveg' href='" . $adat["twitter"] . "'target='_blank'>twitter</a>";
            } else {
                $twitter = "N\A";
            }
        }
    } else {
        $utasitas3 = $adatbazisom->prepare("SELECT eloadok.*, kiadok.ID_kiado, kiadok.kiado_neve FROM eloadok
                                         INNER JOIN kiadok
                                            ON eloadok.kiado_ID = kiadok.ID_kiado
                                        WHERE eloadok.ID_eloado=?");
        $utasitas3->bindParam(1, $parameter_id);
        $utasitas3->execute();
        $eredmeny3 = $utasitas3->fetchAll(PDO::FETCH_ASSOC);
        if (count($eredmeny3) > 0) {
            foreach ($eredmeny3 as $adat) {
                if (!empty($adat["ID_zenek"])) {
                    $id_zene = $adat["ID_zenek"];
                } else {
                    $id_zene = '';
                }
                if (!empty($adat["zene_cim"])) {
                    $zene_cime = $adat["zene_cim"];
                } else {
                    $zene_cime = '';
                }

                if (!empty($adat["borito"])) {
                    $borito = $adat["borito"];
                } else {
                    $borito = 'altalanos/kepek/album.png';
                }
                if (!empty($adat["megjelenes"])) {
                    $megjelenes = $adat["megjelenes"];
                } else {
                    $megjelenes = "N\A";
                }
                $eloado = $adat["eloado_neve"];

                $profil = $adat["profil_kep"];

                if (!empty($adat["debut_ido"])) {
                    $debut = $adat["debut_ido"];
                } else {
                    $debut = "N\A";
                }
                if (!empty($adat["kiado_neve"])) {
                    $kiado = $adat["kiado_neve"];
                } else {
                    $kiado = "N\A";
                }
                if (!empty($adat['kiado_ID'])) {
                    $id_kiado = $adat['kiado_ID'];
                } else {
                    $id_kiado = '';
                }
                if (!empty($adat["ID_album"]) && $albumid != $adat["ID_album"]) {
                    $albumid = $adat["ID_album"]; //az albumid-hoz rendeli az utolsó ID_album-ot
                    $talal = false;
                    $album_cime = $adat["album_cime"];
                } else {
                    $albumid = '';
                    $album_cime = "N\A";
                }
//fanclub név meglétének ellenőrzése
                if ($adat["fan_club"] != null) {   //ha van rajongóitábor név hozzárendeli
                    $fanclub = $adat["fan_club"];
                } else {
                    $fanclub = "N/A";  //ha nincs akkor kiírja hogy nincs adat
                }

//főcímdal meglétének ellenőrzése
                if (!empty($adat["focimdal_e"]) && ($adat["focimdal_e"] != 0) && $talal == false) { //ha a focimdal nem 0 és a talal false, akkor
                    $focimdal = $adat["zene_cim"]; //a focimdal-hoz rendeli a zene címét
                    $focimdal_link = $adat["youtube_link"]; //elmenti a youtube linket ami a zenéhez tartozik
                    $talal = true; //átálítja a talalt- true-ra , azaz megtalálta az utolsó album főcímdalát
                } else if ($talal == false) {
                    $focimdal = "N\A"; //ha nem talált főcímdalt az adott albumhoz akkor nincs adat-ot jelenít meg
                    $focimdal_link = "";
                }
//kapcsolatok meglétének ellenőrzése
                if (!empty($adat["youtube"])) {
                    $youtube = "<a class='ikonszoveg' href='" . $adat["youtube"] . "'target='_blank'>YouTube</a>";
                } else {
                    $youtube = "N\A";
                }
                if (!empty($adat["facebook"])) {
                    $facebook = "<a class='ikonszoveg' href='" . $adat["facebook"] . "'target='_blank'>facebook</a>";
                } else {
                    $facebook = "N\A";
                }
                if (!empty($adat["instagram"])) {
                    $instagram = "<a class='ikonszoveg' href='" . $adat["instagram"] . "'target='_blank'>Instagram</a>";
                } else {
                    $instagram = "N\A";
                }
                if ($adat["twitter"] != null) {
                    $twitter = "<a class='ikonszoveg' href='" . $adat["twitter"] . "'target='_blank'>twitter</a>";
                } else {
                    $twitter = "N\A";
                }
            }
        } else {
            ?>
            <script>location.href = "../altalanos/nem_talalhato.php"</script>
            <?php
        }
    }
    ?>

    <div  class="tartalom">
        <div class="vissza_link">
            <a href="eloadok.php">Vissza az előadókhoz</a>
        </div>
        <!--<a href="eloado_szerkeszt.php" class="muvelet">Előadó szerkesztése</a>-->
    <?php if (isset($_SESSION['felhasznalonev'])) { ?>
            <div class="muvelet">
                <a href="eloado_szerkesztese.php?ID_eloado=<?= $parameter_id; ?>">Előadó szerkesztése</a>
            </div>
        <?php } ?>
        <div class="osszegzes">
            <div class="info_sav_egy">
                <div class='kep_egy'>
        <?php echo "<img class='kep' src=../" . $profil . ">"; ?>
                </div>
                <div class="leiras_egy">

                    <div class='cimsor'>
                        <h3><?php echo $eloado; ?></h3>
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
                        echo "<a href='http://localhost/forditasokk/kiadok/megtekint.php?ID_kiado=" . $id_kiado . "'>";
                        echo $kiado;
                        echo "</a>";
                        ?>
                    </div>


                </div>
            </div>
            <div class="info_sav_ketto">
                <div class='cimsor'>
                    <h3>Kapcsolat</h3>
                </div>


                <div class="ikon">
                    <i class='fab facelink'>&#xf082;</i>
                </div>
                <div class="ikonszoveg">
    <?php echo $facebook; ?>
                </div>

                <div class="ikon">
                    <i class='fab videolink ikon'>&#xf167;</i>
                </div>
                <div class="ikonszoveg">
    <?php echo $youtube; ?>
                </div>

                <div class="ikon">
                    <i class='fab instalink ikon'>&#xf16d;</i>
                </div>
                <div class="ikonszoveg">
    <?php echo $instagram; ?>
                </div>

                <div class="ikon">
                    <i class='fab tweetlink ikon'>&#xf099;</i>
                </div>
                <div class="ikonszoveg">
    <?php echo $twitter; ?>
                </div>
            </div>
            <div class="info_sav_harom">
                <div class="leiras_ketto">
                    <div class='cimsor'>
                        <h3>
                            <?php
                            if (!empty($albumid)) {
                                echo "<a href='http://localhost/forditasokk/albumok/megtekint.php?ID_album=" . $albumid . "'>";
                                echo $album_cime;
                                echo "</a>";
                            } else {
                                echo $album_cime;
                            }
                            ?>
                        </h3>
                    </div>
                    <div class='cim1'>Előadó:</div>
                    <div class='cimleiras1'>
                        <?php
                         echo $eloado;
                         ?>
                    </div>

                    <div class='cim1'>Megjelenés:</div>
                    <div class='cimleiras1'>
                        <?php echo $megjelenes; ?>
                    </div>

                    <div class='cim1'>Főcímdal:</div>
                    <div class='cimleiras1'>
                        <?php
                        if ($focimdal_link != "") {
                            echo "<a href='" . $focimdal_link . "'target='_blank'>" . $focimdal . "  </a>";
                        } else {
                            echo $focimdal;
                        }
                        ?>
                    </div>
                 </div>
                <div class="kep_ketto">
                        <?php echo"<img class='kep' src=../" . $borito . ">"; ?>
                </div>
            </div>
        </div>

        <div class='focimsor'>
            <div class="cimke1"><h4>Albumborító</h4></div>
            <div class="cimke2"><h4>Zenék</h4></div>
            <div class="cimke3"><h4>Előadó</h4></div>
            <div class="cimke4"><h4>Lehetőségek</h4></div>
        </div>
    <?php
    if (count($eredmeny2)) {
        foreach ($eredmeny2 as $adat) {
            echo "<div class='sor'>"; //sorba illeszti be a következőket
            //borítokép beillesztése
            echo "<div class='cimke1'>";
            echo " <a href='http://localhost/forditasokk/albumok/megtekint.php?ID_album=" . $adat['ID_album'] . "' title='Album megtekintése'>";
            echo"<img class='kep' src=../" . $adat['borito'] . ">";
            echo "</a>";
            echo "</div>";
            //dal címének beillesztése
            echo "<div>";
            echo "<div class='cimke2'>";
            echo " <a href='http://localhost/forditasokk/zenek/megtekint.php?ID_zenek=" . $adat["ID_zenek"] . "'>";
            echo $adat["zene_cim"];
            echo "</a>";
            echo "</div>";
            echo "</div>";
            //előadó nevének beillesztése
            echo "<div>";
            echo "<div class='cimke3'>";
            echo " <a href='http://localhost/forditasokk/eloadok/megtekint.php?ID_eloado=" . $adat["ID_eloado"] . "'>";
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
                echo "<div class='szoveg'><a href='http://localhost/forditasokk/zenek/megtekint.php?ID_zenek=" . $adat['ID_zenek'] . "'title='Dalszöveg' class='fas dalszoveg'>&#xf15c;</a></div>";
                echo "<div class='szerkeszto'><a href='../zenek/zene_szerkeszt.php?ID_zenek=" . $adat["ID_zenek"] . "'title='Szerkesztés' class='fas szerkeszto'>&#xf303;</a></div>";
                echo "</div>";
            } else {
                echo "<div class='ikonok_lat'>";
                if ($adat["youtube_link"] != null) {
                    echo "<div class='video'><a href='" . $adat["youtube_link"] . "'title='Videó' class='fab videolink' target='_blank'>&#xf167;</a></div>";
                } else {
                    echo "<div class='video'><i class='fab nincs'>&#xf167;</i></div>";
                }
                echo "<div class='szoveg'><a href='http://localhost/forditasokk/zenek/megtekint.php?ID_zenek=" . $adat['ID_zenek'] . "'title='Dalszöveg' class='fas dalszoveg'>&#xf15c;</a></div>";
                echo "</div>";
            }
            echo "</div>";
        }
    }
    ?>
    </div>


        <?php
    } else {
        ?>
    <script>location.href = "../altalanos/hiba.php"</script>
    <?php
}
// utasítás felszabadítása
unset($eredmeny);
unset($eredmeny2);

// Adatbáziskapcsolat lezárása
unset($adatbazisom);
include_once '../altalanos/lablec.php';
?>