
<?php
require_once'../../altalanos/konfig.php';
include_once '../../altalanos/fejlec.php'; //fejléc beszúrása
//    include_once '../../altalanos/menu.php'; //navigációs sáv beszúrása
//if(isset($_SESSION['tipus'])){
if (isset($_SESSION['felhasznalonev']) && $_SESSION['tipus'] == 'Rendszergazda' || $_SESSION['tipus'] == 'Admin') {
    try {
        $utasitas = $adatbazisom->query("SELECT
                                    albumok.*,
                                    eloadok.ID_eloado,
                                    eloadok.eloado_neve
                                  FROM kapcsolo_zene_album_eloado
                                    INNER JOIN albumok
                                      ON kapcsolo_zene_album_eloado.album_ID = albumok.ID_album
                                    INNER JOIN eloadok
                                      ON kapcsolo_zene_album_eloado.eloado_ID = eloadok.ID_eloado
                                  GROUP BY albumok.album_cime");
        $eredmeny = $utasitas->fetchAll(PDO::FETCH_ASSOC);

        if (count($eredmeny) > 0) {
            ?>
            <div class="tartalom">
                    <div class="focim">
       <h2>Albumok kezelése</h2>
    </div>
                
                <div class="focimsor album_kez">
                    <div class="cimke1">Boritó</div>
                    <div class="cimke2">Előadó</div>
                    <div class="cimke3">Cím</div>
                    <div class="cimke4">Megjelenés</div>
                    <div class="cimke5"><i class='fas szerkeszto'>&#xf303;</i></div>
                </div>

                <?php
                foreach ($eredmeny as $adatsor) {
                    ?>
                    <div class='sor album_kez'>
                        <div class='cimke1'>
                            <?php echo "<img class='kep' src='../../" . $adatsor["borito"] . "' alt='album borítóképe'>"; ?>
                        </div>

                        <div class='cimke2'>
                            <?php echo $adatsor["eloado_neve"];
                            ; ?>
                        </div>

                        <div class='cimke3'>
                <?php echo $adatsor["album_cime"]; ?>
                        </div>

                        <div class='cimke4'>
                <?php echo $adatsor["megjelenes"]; ?>
                        </div>

                        <div class='cimke5'>
                <?php echo "<div class='szerkeszto'><a href='../../albumok/album_szerkesztese.php?ID_album=" . $adatsor["ID_album"] . "'title='Szerkesztés' class='fas szerkeszto'>&#xf303;</a></div>"; ?>
                        </div>

                    </div>

                    <?php
                } //foreach vége
            ?>
            </div>
                <?php
                
                }
        } catch (Exception $exc) {
            echo $exc->getMessage();
            header("location: ../../../altalanos/hiba.php");
            exit();
        }
    } else {
        ?>
        <script>location.href = "../../kezdolap.php"</script>
    
<?php
}

include_once '../../altalanos/lablec.php';
?>
