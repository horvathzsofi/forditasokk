<?php
require_once'../../altalanos/konfig.php';
include_once '../../altalanos/fejlec.php'; //fejléc beszúrása

if (isset($_SESSION['tipus']) && ($_SESSION['tipus'] == 'Rendszergazda' || $_SESSION['tipus'] == 'Admin')) {
    $id_felhasznalo = $_SESSION['id_felhasznalo'];
    $zene_cime = $dalszoveg = $youtube_link = $id = "";
    $forditott_cim = $forditott_szoveg = $forditott_id = "";
    $zene_cime_hiba = "";
    if (isset($_GET['ID_zenek']) && !empty(trim($_GET['ID_zenek'])) && isset($_GET['ID_felhasznalo'])) {
        $id = trim($_GET['ID_zenek']);
        $id_fordito=trim($_GET['ID_felhasznalo']);
        $lekerdezes= "SELECT
                                    forditasok.*,
                                    felhasznalok.ID_felhasznalo,
                                    felhasznalok.felhasznalonev,
                                    zeneszamok.*,
                                    eloadok.profil_kep,
                                    eloadok.eloado_neve,
                                    albumok.borito,
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
                                      ON forditasok.felhasznalo_ID = felhasznalok.ID_felhasznalo ";
        if(empty($_GET['ID_felhasznalo'])){
            $lekerdezes .= " WHERE zeneszamok.ID_zenek = ?;";
        }else{
            $lekerdezes .= " WHERE zeneszamok.ID_zenek = ? AND felhasznalok.ID_felhasznalo = ?;";
        }
        $utasitas = $adatbazisom->prepare($lekerdezes);
        $utasitas->bindParam(1, $id);
        if(!empty($_GET['ID_felhasznalo'])) {
            $utasitas->bindParam(2, $id_fordito);
        }

        $utasitas->execute();
        $eredmeny = $utasitas->fetchALL(PDO::FETCH_ASSOC);
        if (count($eredmeny) > 0) {
            foreach ($eredmeny as $adatsor) {
                $zene_cime = $adatsor['zene_cim'];
                $dalszoveg = $adatsor['dalszoveg'];
                $youtube_link = $adatsor['youtube_link'];
                $id = $adatsor['ID_zenek'];
                $forditott_cim = $adatsor['dal_forditott_cime'];
                $forditott_szoveg = $adatsor['forditas'];
                $forditott_id = $adatsor['ID_forditas'];
                $eloado = $adatsor['eloado_neve'];
                $album = $adatsor['album_cime'];
                $borito = $adatsor['borito'];
                
                $felhasznalo=$adatsor['felhasznalonev'];
            }
        } 
      
    } else {
        header("location: ../../altalanos/hiba.php");
    }
    unset($eredmeny);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {                     //megnézi hogy POST kérést indítottak-e
        //bevitt értékek validálása
        $bevitt_zene_cime = trim($_POST["zene_cime"]);              //a szuperglobális POST változóból kiolvassa a dal címét és hozzárendeli a bevitt_dal_cimhez
        if (empty($bevitt_zene_cime)) {                             //ha üresen hagyták a mezőt, akkor hibaüzenetet dob
            $zene_cime_hiba = "A zene címét meg kell adni!";
        } elseif (!filter_var(trim($_POST["zene_cime"]), FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^[a-zA-Z'-.\s 0-9]+$/")))) {
            $zene_cime_hiba = "A megadott zene cím nem megfelelő formátumú.";
        } //az else if ág kiszűri, hogy olyan karaktereket lehessen beírni
        else {
            $zene_cime = $bevitt_zene_cime;
        }

        $dalszoveg = htmlspecialchars($_POST['dalszoveg']);
        $youtube_link = htmlspecialchars($_POST['youtube_link']);

        $bevitt_forditott_cim = htmlspecialchars($_POST['forditott_cim']);
        $bevitt_forditott_szoveg = htmlspecialchars($_POST['forditott_szoveg']);

        if (isset($_POST["zene_cime"])) {
            $utasitas = $adatbazisom->prepare("UPDATE zeneszamok SET zene_cim=:zene_cime, dalszoveg=:dalszoveg, youtube_link=:youtube_link WHERE ID_zenek=:id;");
            $utasitas->bindParam(':zene_cime', $zene_cime);
            $utasitas->bindParam(':dalszoveg', $dalszoveg);
            $utasitas->bindParam(':youtube_link', $youtube_link);
            $utasitas->bindParam(':id', $id);

            $utasitas->execute();
        }unset($utasitas);

        if (isset($bevitt_forditott_cim)) {
            if ($forditott_cim == null) {
                $utasitas = $adatbazisom->prepare("INSERT INTO forditasok (dal_forditott_cime, forditas, felhasznalo_ID, zene_ID) VALUES (:forditott_cim, :forditott_szoveg,:felhasznalo_id, :id);");
                $utasitas->bindParam(':forditott_cim', $bevitt_forditott_cim);
                $utasitas->bindParam(':forditott_szoveg', $bevitt_forditott_szoveg);
                $utasitas->bindParam(':felhasznalo_id', $id_felhasznalo);
                $utasitas->bindParam(':id', $id);
            }
            if ($forditott_cim != null) {
                $utasitas = $adatbazisom->prepare("UPDATE forditasok SET dal_forditott_cime=:forditott_cim, forditas=:forditott_szoveg WHERE zene_ID = :id;");
                $utasitas->bindParam(':forditott_cim', $bevitt_forditott_cim);
                $utasitas->bindParam(':forditott_szoveg', $bevitt_forditott_szoveg);
                $utasitas->bindParam(':id', $id);
            }


            if ($utasitas->execute()) {
                header("location: zenek_es_forditasok_kezelese.php");
                exit();
            } else {
                echo "Valami hiba történt";
            }
        }
    }
    unset($adatbazisom);

?>
<div class='tartalom'>
    <div class="focim">
<h2>Zene adatainak módosítása</h2>
</div>
    <div class='szerkeszt_kartya'>
        <div class="uj_kartya">
            <?php
            echo "<img class='kep' src='../../".$borito."'>";
            echo "<div></div><div>".$eloado."</div>";
            echo "<div>".$album."</div><div></div>";
            ?>
        </div>
      
    </div>
    
    <form method="POST" class="zene_ford_szerkeszt">
        <div  class="dalszovegek">
        <div class="zeneszoveg">
            <div>
                <label> Zene címe</label><br>
                <input type="text" name="zene_cime" value="<?php echo $zene_cime; ?>">
            </div>
            <div>
                <label> Dalszöveg </label><br>
                <textarea name="dalszoveg"><?php echo $dalszoveg; ?></textarea>
            </div>
            <div>
                <label> YouTube link </label><br>
                <input type="text" name="youtube_link" value="<?php echo $youtube_link; ?>" pattern="^((?:https?:)?\/\/)?((?:www|m)\.)?((?:youtube\.com|youtu.be))(\/(?:[\w\-]+\?v=|embed\/|v\/)?)([\w\-]+)(\S+)?$">
            </div>
            <input type="hidden" name="id" value="<?php echo $id; ?>"/>
        </div>
        
        <div class="forditas">
            <div>
                <label> Dal fordított címe</label><br>
                <input type="text" name="forditott_cim" value="<?php echo $forditott_cim; ?>">
            </div>
            <div>
                <label> Fordított dalszöveg </label><br>
                <textarea name="forditott_szoveg"><?php echo $forditott_szoveg; ?></textarea>
            </div>
        </div>
        </div>

        <button class="gomb" id="zene_szerkeszt_gomb" type="submit" name="frissit">Adatok frissítése</button>
    </form>

</div>
<?php
} else{
?>
    <script>location.href="http://localhost/forditasokk/kezdolap.php"</script>
<?php
}
include_once '../../altalanos/lablec.php';
?>