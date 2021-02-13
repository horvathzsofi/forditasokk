<?php
require_once'altalanos/konfig.php';  // konfig.php tartalmának beszúrása
include_once 'altalanos/fejlec.php'; //fejléc beszúrása

?>
  

 <div class="tartalom">
     <h2 class="focim">Újdonságok</h2>
        <?php
        try {
            // A lekérdezés futtatása és adataink megjelenítése, ha vannak 
            $utasitas = $adatbazisom->query("SELECT
                                            albumok.ID_album,
                                            albumok.borito,
                                            albumok.album_cime,
                                            eloadok.ID_eloado,
                                            eloadok.eloado_neve,
                                            albumok.megjelenes
                                        FROM kapcsolo_zene_album_eloado
                                            INNER JOIN albumok
                                                ON kapcsolo_zene_album_eloado.album_ID = albumok.ID_album
                                            INNER JOIN eloadok
                                                ON kapcsolo_zene_album_eloado.eloado_ID = eloadok.ID_eloado
                                        GROUP BY albumok.album_cime
                                        ORDER BY albumok.megjelenes DESC
                                        LIMIT 10;");
            $eredmeny = $utasitas->fetchAll(PDO::FETCH_ASSOC);
            if (count($eredmeny) > 0) {
                echo "<div class='focimsor'>";
                    echo "<div class='cimke1'><h4>Albumborító</h4></div>";
                    echo "<div class='cimke2'><h4>Album címe</h4></div>";
                    echo "<div class='cimke3'><h4>Előadó</h4></div>";
                    echo "<div class='cimke4'><h4>Megjelenés</h4></div>";
                echo "</div>";
                foreach ($eredmeny as $adat) {
                     echo "<div class='sor'>";
                       //album borito
                        echo "<div class='cimke1'>";
                            echo"<a href='http://localhost/forditasokk/albumok/megtekint.php?ID_album=" . $adat['ID_album'] . "' title='Album megtekintése'>";
                                echo "<img class='kep' src=" . $adat['borito'] . ">";
                            echo "</a>";
                        echo "</div>";
                        //album cime
                        echo "<div class='cimke2'>";
                            echo"<a href='http://localhost/forditasokk/albumok/megtekint.php?ID_album=" . $adat['ID_album'] . "'>";
                                echo $adat['album_cime'];
                            echo "</a>";
                        echo "</div>";
                        
                        //eloado
                        echo "<div class='cimke3'>";
                            echo"<a href='http://localhost/forditasokk/eloadok/megtekint.php?ID_eloado=" . $adat['ID_eloado'] . "'>";
                                echo $adat['eloado_neve'];
                            echo "</a>";
                        echo "</div>";
                        //megjelenés
                        echo "<div class='cimke4'>";
                            echo $adat['megjelenes'];
                        echo "</div>";
                
                    echo "</div>";
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



        <?php
//        include_once 'altalanos/kereses.php';
        include_once 'altalanos/lablec.php';
        ?>
