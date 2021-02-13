<?php
require_once'konfig.php';
include_once 'fejlec.php';
?>
<div class="tartalom">
    <h3 class="vissza_link">Találatok:</h3>
    <div class="kereses_eredmenye">
    
    <?php
    if (isset($_GET['keres']) && $_GET['keres'] != '') {
        
        $keresett = htmlspecialchars(trim($_GET['keres']));

        $eloado_lekerdezes = "SELECT eloadok.ID_eloado, eloadok.profil_kep, eloadok.eloado_neve FROM eloadok WHERE ";
        $album_lekerdezes = "SELECT * FROM albumok WHERE ";
        $zene_lekerdezes = "SELECT * FROM zeneszamok WHERE ";
        $kiado_lekerdezes = "SELECT * FROM kiadok WHERE ";
        $forditas_lekeredezes = "SELECT * FROM forditasok INNER JOIN zeneszamok ON forditasok.zene_ID = zeneszamok.ID_zenek WHERE ";
        
        $kulcsszo = explode(' ', $keresett);

        foreach ($kulcsszo as $szo) {
            $eloado_lekerdezes .= 'eloado_neve LIKE "%' . $szo . '%" OR ';
            $album_lekerdezes.='album_cime LIKE "%' . $szo . '%" OR ';
            $zene_lekerdezes.='zene_cim LIKE "%' . $szo . '%" OR ';
            $kiado_lekerdezes.='kiado_neve LIKE "%' . $szo . '%" OR ';
            $forditas_lekeredezes.='dal_forditott_cime LIKE "%' . $szo . '%" OR ';
        }
        
        $eloado_lekerdezes = substr($eloado_lekerdezes, 0, strlen($eloado_lekerdezes) - 3);
        $album_lekerdezes = substr($album_lekerdezes, 0, strlen($album_lekerdezes) - 3);
        $zene_lekerdezes = substr($zene_lekerdezes, 0, strlen($zene_lekerdezes) - 3);
        $kiado_lekerdezes = substr($kiado_lekerdezes, 0, strlen($kiado_lekerdezes) - 3);
        $forditas_lekeredezes = substr($forditas_lekeredezes, 0, strlen($forditas_lekeredezes) - 3);
        
        $utasitas = $adatbazisom->query($eloado_lekerdezes);
        $utasitas1 = $adatbazisom->query($album_lekerdezes);
        $utasitas2 = $adatbazisom->query($zene_lekerdezes);
        $utasitas3 = $adatbazisom->query($kiado_lekerdezes);
        $utasitas4 = $adatbazisom->query($forditas_lekeredezes);
        
        $eloado_eredmeny = $utasitas->fetchAll(PDO::FETCH_ASSOC);
        $album_eredmeny = $utasitas1->fetchAll(PDO::FETCH_ASSOC);
        $zene_eredmeny = $utasitas2->fetchAll(PDO::FETCH_ASSOC);
        $kiado_eredmeny = $utasitas3->fetchAll(PDO::FETCH_ASSOC);
        $forditas_eredmeny = $utasitas4->fetchAll(PDO::FETCH_ASSOC);
        
        $eloado_talalat = count($eloado_eredmeny);
        $album_talalat = count($album_eredmeny);
        $zene_talalat = count($zene_eredmeny);
        $kiado_talalat = count($kiado_eredmeny);
        $forditas_talalat = count($forditas_eredmeny);
        
        if(($eloado_talalat || $album_talalat || $zene_talalat || $kiado_talalat || $forditas_talalat)>0){
        if ($eloado_talalat > 0) {
            echo "<p class='kereses_cimke'>Előadók ( ".$eloado_talalat." )</p>";
            foreach ($eloado_eredmeny as $adatsor) {
                echo "<div class='kartya'><a href='../eloadok/megtekint.php?ID_eloado=" . $adatsor['ID_eloado'] . "' title='Előadó profiljának megtekintése'>";
                    echo "<img class='kep' src=../" . $adatsor['profil_kep'] . ">";
                    echo"<div class='kartya_cim'>";
                        echo $adatsor['eloado_neve'];
                    echo"</div>";
                echo"</a></div>";
            } unset($eloado_eredmeny);
        } 
        
        
        if ($album_talalat > 0) {
            echo "<p class='kereses_cimke'>Albumok ( ".$album_talalat." )</p>";
            foreach ($album_eredmeny as $adatsor) {
                echo "<div class='kartya'><a href='../albumok/megtekint.php?ID_album=" . $adatsor['ID_album'] . "' title='Album adatlapjának megtekintése'>";
                    echo "<img class='kep' src=../" . $adatsor['borito'] . ">";
                    echo"<div class='kartya_cim'>";
                        echo $adatsor['album_cime'];
                    echo"</div>";
                echo"</a></div>";
            } unset($album_eredmeny);
        }     
        
        if ($zene_talalat > 0) {
            echo "<p class='kereses_cimke'>Zenék ( ".$zene_talalat." )</p>";
            foreach ($zene_eredmeny as $adatsor) {
                echo "<div class='kartya'><a href='../zenek/megtekint.php?ID_zenek=" . $adatsor['ID_zenek'] . "' title='Zene adatlapjának megtekintése'>";
                    echo"<div class='kartya_cim'>";
                        echo $adatsor['zene_cim'];
                    echo"</div>";
                echo"</a></div>";
            } unset($zene_eredmeny);
        } 
        
        if ($kiado_talalat > 0) {
            echo "<p class='kereses_cimke'>Kiadók ( ".$kiado_talalat." )</p>";
            foreach ($kiado_eredmeny as $adatsor) {
                echo "<div class='kartya'><a href='../kiadok/megtekint.php?ID_kiado=" . $adatsor['ID_kiado'] . "' title='Kiadó profiljának megtekintése'>";
                    echo "<img class='kep' src=../" . $adatsor['kiado_logo'] . ">";
                    echo"<div class='kartya_cim'>";
                        echo $adatsor['kiado_neve'];
                    echo"</div>";
                echo"</a></div>";
            } unset($kiado_eredmeny);
        } 
        
        if ($forditas_talalat > 0) {
            echo "<p class='kereses_cimke'>Fordítások ( ".$forditas_talalat." )</p>";
            foreach ($forditas_eredmeny as $adatsor) {
                echo "<div class='kartya'><a href='../zenek/megtekint.php?ID_zenek=" . $adatsor['ID_zenek'] . "' title='Fordítás megtekintése'>";
                    echo"<div class='kartya_cim'>";
                        echo $adatsor['dal_forditott_cime'];
                    echo"</div>";
                echo"</a></div>";
            } unset($forditas_eredmeny);
        } 
        } else{
            echo "Nincs találat.";
        }
    }  else{
            echo "Nincs találat.";
        } ?>
</div>
</div>

<?php 
    unset($adatbazisom);
    include_once 'lablec.php';
?>