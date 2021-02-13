<?php
require_once'../altalanos/konfig.php';  // konfig.php tartalmának beszúrása
include_once '../altalanos/fejlec.php'; //fejléc beszúrása
//include_once '../altalanos/menu.php'; //navigációs sáv beszúrása
?>

<div class="tartalom">
    <div class="focim">
        <h2>Zenék</h2>
    </div>        
            <?php if(isset($_SESSION['felhasznalonev'])){ ?>
    <a href="zene_hozzaad.php" class="muvelet">Zene hozzáadása</a>
            <?php }?>

        <div class="kartyak">
        <?php
        try {
            // A lekérdezés futtatása és adataink megjelenítése, ha vannak 
            $utasitas = $adatbazisom->query("SELECT
                                                albumok.ID_album,
                                                albumok.borito,
                                                YEAR(albumok.megjelenes) AS megjelenes,
                                                zeneszamok.ID_zenek,
                                                zeneszamok.zene_cim,
                                                eloadok.ID_eloado,
                                                eloadok.eloado_neve
                                              FROM kapcsolo_zene_album_eloado
                                                INNER JOIN eloadok
                                                      ON kapcsolo_zene_album_eloado.eloado_ID = eloadok.ID_eloado
                                                INNER JOIN albumok
                                                      ON kapcsolo_zene_album_eloado.album_ID = albumok.ID_album
                                              INNER JOIN zeneszamok
                                                      ON kapcsolo_zene_album_eloado.zene_ID = zeneszamok.ID_zenek
                                              ORDER BY zeneszamok.zene_cim, eloadok.eloado_neve");

            $eredmeny = $utasitas->fetchAll(PDO::FETCH_ASSOC);
			
            if (count($eredmeny) > 0) {
                foreach ($eredmeny as $adat) {
                        echo "<div class='kartya'><a href='http://localhost/forditasokk/zenek/megtekint.php?ID_zenek=" . $adat['ID_zenek'] . "' title='Zene megtekintése'>";
                            echo "<img class='kep' src=../" . $adat['borito'] . ">";
                            echo"<div class='kartya_cim'>";
                                echo $adat['zene_cim'];
                            echo"</div>";
                            echo "<div class='kartya_leiras'>";
                                echo "<div class='kartya_alcim'>";
                                   echo $adat['eloado_neve'];
                                   
                                echo"</div>";   
                                echo "<div class='ev'>";
                                        echo $adat['megjelenes'];
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