<?php
require_once '../altalanos/konfig.php';  // konfig.php tartalmának beszúrása
include_once '../altalanos/fejlec.php'; //fejléc beszúrása
?>
<div class="tartalom">
    <div class="focim">
        <h2>Albumok</h2>
    </div>
    <?php if (isset($_SESSION['felhasznalonev'])) { ?>
        <a href="album_hozzaad.php" class="muvelet">Album hozzáadása</a>
    <?php } ?>
    <div class="kartyak">
        <?php
        try {
            // A lekérdezés futtatása és adataink megjelenítése, ha vannak 
            $utasitas = $adatbazisom->query("SELECT
                                                albumok.ID_album,
                                                albumok.borito,
                                                albumok.album_cime,
                                                YEAR(albumok.megjelenes) AS megjelenes,
                                                eloadok.ID_eloado,
                                                eloadok.eloado_neve
                                              FROM kapcsolo_zene_album_eloado
                                                INNER JOIN eloadok
                                                      ON kapcsolo_zene_album_eloado.eloado_ID = eloadok.ID_eloado
                                                INNER JOIN albumok
                                                      ON kapcsolo_zene_album_eloado.album_ID = albumok.ID_album
                                              GROUP BY albumok.ID_album
                                              ORDER BY albumok.album_cime, eloadok.eloado_neve");

            $eredmeny = $utasitas->fetchAll(PDO::FETCH_ASSOC);

            if (count($eredmeny) > 0) {
                foreach ($eredmeny as $adat) {
                    echo "<div class='kartya'><a href='megtekint.php?ID_album=" . $adat['ID_album'] . "' title='Album megtekintése'>";
                    echo "<img class='kep' src=../" . $adat['borito'] . ">";
                    echo "<div class='kartya_cim'>";
                    echo $adat['album_cime'];
                    echo "</div>";
                    echo "<div class='kartya_leiras'>";
                    echo "<div class='kartya_alcim'>";
                    echo $adat['eloado_neve'];
                    echo "</div>";
                    echo "<div class='ev'>";
                    echo $adat['megjelenes'];
                    echo "</div>";
                    echo "</div>";
                    echo "</a></div>";
                }
                // Az eredmény halmaz felszabadítása
                unset($eredmeny);
                unset($utasitas);
            }
            // Adatbázis kapcsolat bezárása
            unset($adatbazisom);
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
        ?>
    </div>
</div>

<?php

include_once '../altalanos/lablec.php';
?>