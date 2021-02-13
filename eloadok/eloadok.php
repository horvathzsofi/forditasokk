<?php
require_once'../altalanos/konfig.php';  // konfig.php tartalmának beszúrása
include_once '../altalanos/fejlec.php'; //fejléc beszúrása
//include_once '../altalanos/menu.php'; //navigációs sáv beszúrása
?>
<div class="tartalom">
    <div class="focim">
        <h2>Előadók</h2>
    </div>
    <?php if (isset($_SESSION['felhasznalonev'])) { ?>
        <a href="eloado_hozzaadasa.php" class="muvelet">Előadó hozzáadása</a>
    <?php } ?>

    <div class="kartyak">
        <?php
        try {
            // A lekérdezés futtatása és adataink megjelenítése, ha vannak 
            $utasitas = $adatbazisom->query("SELECT
                                            eloadok.ID_eloado,
                                            eloadok.profil_kep,
                                            eloadok.eloado_neve,
                                            kiadok.kiado_neve,
                                            kiadok.ID_kiado,
                                            YEAR(eloadok.debut_ido) AS debut_ido
                                         FROM eloadok
                                           INNER JOIN kiadok
                                             ON eloadok.kiado_ID = kiadok.ID_kiado
                                         ORDER BY eloadok.eloado_neve;");
            $eredmeny = $utasitas->fetchAll(PDO::FETCH_ASSOC);
            if (count($eredmeny) > 0) {
                foreach ($eredmeny as $sor) {
                    echo "<div class='kartya'><a href='megtekint.php?ID_eloado=" . $sor['ID_eloado'] . "' title='Előadó megtekintése'>";
                        echo "<img class='kep' src=../" . $sor['profil_kep'] . ">";
                        echo"<div class='kartya_cim'>";
                            echo $sor['eloado_neve'];
                        echo"</div>";
                        echo "<div class='kartya_leiras'>";
                            echo "<div class='kartya_alcim'>";
                                echo $sor['kiado_neve'];
                            echo"</div>";
                            echo "<div class='ev'>";
                                if(!empty($sor['debut_ido'])){
                                    echo $sor['debut_ido'];
                                }
                                else{
                                    echo "N\A";
                                }
                            echo"</div>";
                        echo "</div>";
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