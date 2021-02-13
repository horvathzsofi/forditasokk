<?php
require_once'../altalanos/konfig.php';  // konfig.php tartalmának beszúrása
include_once '../altalanos/fejlec.php'; //fejléc beszúrása
//include_once '../altalanos/menu.php'; //navigációs sáv beszúrása
?>
<div class="tartalom">
<div class="focim">
    <h2>Kiadók</h2>
    </div>
    <?php if (isset($_SESSION['felhasznalonev'])) { ?>
        <a href="kiado_hozzaadasa.php" class="muvelet">Kiadó hozzáadása</a>
    <?php } ?>

    <div class="kartyak">
        <?php
        try {
            // A lekérdezés futtatása és adataink megjelenítése, ha vannak 
            $utasitas = $adatbazisom->query("SELECT *
                                             FROM kiadok
                                             ORDER BY kiadok.kiado_neve");

            $eredmeny = $utasitas->fetchAll(PDO::FETCH_ASSOC);

            if (count($eredmeny) > 0) {
                foreach ($eredmeny as $adat) {
                    echo "<div class='kartya'><a href='megtekint.php?ID_kiado=" . $adat['ID_kiado'] . "' title='Kiadó megtekintése'>";
                        echo "<img class='kep' src=../" . $adat['kiado_logo'] . ">";
                        echo"<div class='kartya_cim'>";
                            echo $adat['kiado_neve'];
                        echo"</div>";
                    echo"</a></div>";
                }
                // Az eredmény halmaz felszabadítása
                unset($eredmeny);
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