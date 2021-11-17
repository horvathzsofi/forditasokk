<?php
require_once '../altalanos/konfig.php';
include_once '../altalanos/fejlec.php';  //fejléc beszúrása

if (isset($_SESSION['tipus']) && ($_SESSION['tipus'] == 'Rendszergazda' || $_SESSION['tipus'] == 'Admin')) {

    $utasitas = $adatbazisom->query("SELECT
                                    forditasok.*,
                                    felhasznalok.felhasznalonev,
                                    felhasznalok.ID_felhasznalo,
                                    zeneszamok.ID_zenek,
                                    zeneszamok.zene_cim,
                                    zeneszamok.dalszoveg,
                                    eloadok.eloado_neve,
                                    albumok.album_cime
                                    FROM kapcsolo_zene_album_eloado
                                    INNER JOIN zeneszamok
                                      ON kapcsolo_zene_album_eloado.zene_ID = zeneszamok.ID_zenek 
                                    INNER JOIN eloadok
                                      ON kapcsolo_zene_album_eloado.eloado_ID = eloadok.ID_eloado
                                    INNER JOIN albumok
                                      ON kapcsolo_zene_album_eloado.album_ID = albumok.ID_album
                                    LEFT JOIN forditasok
                                      ON forditasok.zene_ID = zeneszamok.ID_zenek
                                    LEFT JOIN felhasznalok
                                      ON forditasok.felhasznalo_ID = felhasznalok.ID_felhasznalo
                                    ORDER BY zeneszamok.zene_cim, eloadok.eloado_neve;");
    $eredmeny = $utasitas->fetchAll(PDO::FETCH_ASSOC);

    if (count($eredmeny) > 0) {
?>
        <div class="tartalom">
            <div class="focim">
                <h2>Zenék és Fordítások kezelése</h2>
            </div>

            <div class='focimsor zene_kez'>
                <div class="cimke1">Előadó</div>
                <div class="cimke2">Dal címe/Fordított címe</div>
                <div class="cimke3">Dalszöveg/Fordított szöveg</div>
                <div class="cimke4">Album</div>
                <div class="cimke5">Fordító</div>
                <div class="cimke6"><i class='fas szerkeszto'>&#xf303;</i></div>
            </div>
        <?php
        foreach ($eredmeny as $adatsor) {
            echo "<div class='sor zene_kez'>";
            echo "<div class='cimke1'>";
            echo $adatsor["eloado_neve"];
            echo "</div>";
            echo "<div class='cimke2'>";
            echo $adatsor["zene_cim"];
            echo "<br><br>";
            if ($adatsor["dal_forditott_cime"]) {
                echo $adatsor["dal_forditott_cime"];
            } else {
                echo "Nem érhető el fordítás";
            }

            echo "</div>";

            echo "<div class='cimke3'>";
            if ($adatsor["dalszoveg"] != null && strlen($adatsor["dalszoveg"]) < 450) {
                echo "Hibás tartalom. <br>";
            } elseif ($adatsor["dalszoveg"] != null) {
                $sorok = explode("\n", $adatsor["dalszoveg"]);
                if ($sorok != false) {
                    for ($i = 0; $i < count($sorok); $i++) {
                        if ($i >= 2) {
                            break;
                        }
                        echo $sorok[$i] . "<br>";
                    }
                }
            } else {
                echo "Még nem adtak hozzá dalszöveget. <br>";
            }
            echo "<br>";
            if ($adatsor["forditas"] != null && strlen($adatsor["forditas"]) < 450) {
                echo "<div class=''>Hibás tartalom.</div>";
            } elseif ($adatsor["forditas"] != null) {
                $sorok = explode("\n", $adatsor["forditas"]);
                if ($sorok != false) {
                    for ($i = 0; $i < count($sorok); $i++) {
                        if ($i >= 2) {
                            break;
                        }
                        echo $sorok[$i] . "<br>";
                    }
                }
            } else {
                echo "Még nem adtak hozzá fordítást.";
            }
            echo "</div>";

            echo "<div class='cimke4'>";
            echo $adatsor["album_cime"];
            echo "</div>";

            echo "<div class='cimke5'>";
            echo $adatsor["felhasznalonev"];
            echo "</div>";

            echo "<div class='cimke6'>";
            echo "<div class='szerkeszto'><a href='szerkeszt.php?ID_zenek=" . $adatsor["ID_zenek"] . "&ID_felhasznalo=" . $adatsor["ID_felhasznalo"] . "'title='Szerkesztés' class='fas szerkeszto'>&#xf303;</a></div>";
            echo "</div>";
            echo "</div>";
        }
    }

        ?>
        </div>
    <?php
} else {
    ?>
        <script>
            location.href = "../index.php"
        </script>
    <?php
} //else ág lezárása
include_once '../altalanos/lablec.php';
    ?>