
<?php
require_once'../altalanos/konfig.php';
include_once '../altalanos/fejlec.php';

    // A további feldolgozás előtt az id paraméter meglétének ellenőrzése
    if (isset($_GET['ID_kiado']) && !empty(trim($_GET['ID_kiado']))) {

        // Select utasítás előkészítése
        $utasitas = $adatbazisom->prepare("SELECT
                                            kiadok.ID_kiado,
                                            eloadok.ID_eloado,
                                            eloadok.profil_kep,
                                            eloadok.eloado_neve,
                                            YEAR(eloadok.debut_ido) AS debut_ido
                                        FROM eloadok
                                           INNER JOIN kiadok
                                             ON eloadok.kiado_ID = kiadok.ID_kiado
                                        WHERE kiadok.ID_kiado = :ID_kiado
                                        ORDER BY eloadok.eloado_neve;");
                                        
        $utasitas2=$adatbazisom->prepare("SELECT * FROM kiadok
                                          WHERE kiadok.ID_kiado = :ID_kiado;");
        // paraméterek kötése
        $utasitas->bindParam(':ID_kiado', $id_kiado);
        $utasitas2->bindParam(':ID_kiado', $id_kiado);
        // paraméterek beállítása
        $id_kiado = trim($_GET["ID_kiado"]);

        $utasitas->execute();
        $utasitas2->execute();

        $eredmeny = $utasitas->fetchAll(PDO::FETCH_ASSOC);
        $eredmeny2 = $utasitas2->fetchAll(PDO::FETCH_ASSOC);

        $talal = false;
        $albumid = 0;
        if (count($eredmeny2) > 0) {
        foreach ($eredmeny2 as $adat) {
            $kiado_logo = $adat["kiado_logo"];
            $kiado = $adat["kiado_neve"];
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
        
        }else {
            ?>
            <script>location.href = "../altalanos/nem_talalhato.php"</script>
            <?php
        }
    

?>
            
<div  class="tartalom">	
   <a class="vissza_link" href="kiadok.php">Vissza a kiadókhoz</a>
   <div class='muvelet'>
<?php
if (isset($_SESSION['tipus']) == 'Rendszergazda' || isset($_SESSION['tipus']) == 'Admin' || isset($_SESSION['tipus']) == 'Felhasznalo') {
    echo "<a href='kiado_szerkesztese.php?ID_kiado=" . $id_kiado . "'>Kiadó szerkesztése</a> <br>";
    }
?>
       </div>
        <div class="osszegzes">
            <div class="info_sav_egy">
                <div class="kep_egy">
                    <?php echo "<img class='kep' src='../" . $adat['kiado_logo']. "'>"; ?>
                </div>
                <div class="leiras_egy">
                    
                        <div class='cimsor'>
                            <h3><?php echo $kiado; ?></h3>
                        </div>
                    
                </div>
            </div>
            <div class="info_sav_ketto">
                <h3>Kapcsolat</h3>
                
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
            <div class="info_sav_harom"></div>
                
        </div>
        

        <div class="kartyak">
           
<?php
  
    foreach ($eredmeny as $adat) {
        $profil = $adat["profil_kep"];
        $eloado = $adat["eloado_neve"];
        $debut = $adat["debut_ido"];
        
   
        echo "<div class='kartya'>";
            echo"<a href='http://localhost/forditasokk/eloadok/megtekint.php?ID_eloado=" . $adat['ID_eloado'] . "' title='Előadó megtekintése'>";
                echo "<img class='kep' src=../" . $adat['profil_kep'] . ">";
            
            echo "<div class='kartya_leiras'>";
                echo "<div class='kartya_alcim'>";
                    echo $adat['eloado_neve'];
                echo"</div>";   
                echo "<div class='ev'>";
                        echo $adat['debut_ido'];
                echo"</div>";   
            echo "</div>";
            echo "</a>";
        echo"</div>";
    }
 
?>
       
    </div>
    </div>

            <?php
            // utasítás felszabadítása
            unset($eredmeny);
}else {
            ?>
    <script>location.href = "../altalanos/hiba.php"</script>
    <?php
       }
       // Adatbáziskapcsolat lezárása
            unset($adatbazisom);
            include_once '../altalanos/lablec.php';
            ?>