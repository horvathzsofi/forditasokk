<?php
require_once '../altalanos/konfig.php';
include_once '../altalanos/fejlec.php'; //fejléc beszúrása

if (isset($_SESSION['felhasznalonev'])) {
    $id_kiado = $kiado_nev = '';
    $debut = $debut_hiba = '';
    $eloado = $kiado = $rajongok = $yt = $twt = $face = $insta = '';
    $eloado_hiba = $kiado_hiba = $rajongok_hiba = $yt_hiba = $twt_hiba = $face_hiba = $insta_hiba = '';

    if (isset($_GET['ID_eloado']) && !empty(trim($_GET['ID_eloado']))) {
        $id_eloado= htmlspecialchars(trim($_GET['ID_eloado']));

        $eloado_lekerdezes=$adatbazisom->prepare("SELECT
                                                    kiadok.ID_kiado,
                                                    kiadok.kiado_neve,
                                                    eloadok.*
                                                FROM eloadok
                                                LEFT JOIN kiadok
                                                  ON eloadok.kiado_ID = kiadok.ID_kiado
                                                WHERE ID_eloado=?");
        $eloado_lekerdezes->bindParam(1, $id_eloado);
        $eloado_lekerdezes->execute();
        $eloado_eredmeny=$eloado_lekerdezes->fetchAll(PDO::FETCH_ASSOC);
        if(count($eloado_eredmeny)>0){
            foreach ($eloado_eredmeny as $adatsor) {
                $eloado=$adatsor['eloado_neve'];
                $kiado=$adatsor['kiado_neve'];
                $id_kiado=$adatsor['ID_kiado'];
                $debut=$adatsor['debut_ido'];
                $rajongok=$adatsor['fan_club'];
                $yt = $adatsor['youtube'];
                $face = $adatsor['facebook'];
                $twt = $adatsor['twitter'];
                $insta = $adatsor['instagram'];
                
            }
        }else{
    ?>
    <script>location.href = "http://localhost/forditasokk/altalanos/nem_talalhato.php"</script>
    <?php
    } 


        if (isset($_POST["eloado_szerkeszt"])) {

            if (isset($_POST["debut"]) && !empty(htmlspecialchars(trim($_POST["debut"])))) {
                $bevitt_debut = htmlspecialchars(trim($_POST["debut"]));
                if (!filter_var($bevitt_debut, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^(?:19|20)[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-9])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:30))|(?:(?:0[13578]|1[02])-31))/")))) {
                    $debut_hiba = "Nem megfelelő formátum.";
                } else {
                    $debut = $bevitt_debut;
                }
            }
            $bevitt_kiado = htmlspecialchars(trim($_POST["kiado"]));
            if (empty($bevitt_kiado)) {
                $kiado_hiba = "A kiadót meg kell adni!";
            } elseif (!filter_var($bevitt_kiado, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^[a-zA-Z0-9]+/")))) {
                $kiado_hiba = "A kiadó neve nem megfelelő karaktereket tartalmaz!";
            } else {
                $kiado = $bevitt_kiado;
            }
            
            if (!empty($_POST["rajongok"])) {
                $bevitt_rajongok = htmlspecialchars(trim($_POST["rajongok"]));
                if (!filter_var($bevitt_rajongok, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^[a-zA-Z0-9 ()]/")))) {
                    $rajongok_hiba = "A rajongótábor neve nem megfelelő karaktereket tartalmaz!";
                } else {
                    $rajongok = $bevitt_rajongok;
                }
            }

            if (!empty($_POST["youtube"])) {
                $bevitt_youtube = htmlspecialchars(trim($_POST["youtube"]));
                if (!filter_var($bevitt_youtube, FILTER_VALIDATE_URL)) {
                    $yt_hiba = "Nem megfelelő formátumú hivatkozás!";
                } elseif (!strpos($bevitt_youtube, 'youtube.com')) {
                    $yt_hiba = "Nem megfelelő hivatkozás";
                } else {
                    $yt = $bevitt_youtube;
                }
            }

            if (!empty($_POST["facebook"])) {
                $bevitt_facebook = htmlspecialchars(trim($_POST["facebook"]));
                if (!filter_var($bevitt_facebook, FILTER_VALIDATE_URL)) {
                    $face_hiba = "Nem megfelelő formátumú hivatkozás!";
                } elseif (!strpos($bevitt_facebook, 'facebook.com')) {
                    $face_hiba = "Nem megfelelő hivatkozás";
                } else {
                    $face = $bevitt_facebook;
                }
            }

            if (!empty($_POST["twitter"])) {
                $bevitt_twitter = htmlspecialchars(trim($_POST["twitter"]));
                if (!filter_var($bevitt_twitter, FILTER_VALIDATE_URL)) {
                    $twt_hiba = "Nem megfelelő formátumú hivatkozás!";
                } elseif (!strpos($bevitt_twitter, 'twitter.com')) {
                    $twt_hiba = "Nem megfelelő hivatkozás";
                } else {
                    $twt = $bevitt_twitter;
                }
            }

            if (!empty($_POST["instagram"])) {
                $bevitt_instagram = htmlspecialchars(trim($_POST["instagram"]));
                if (!filter_var($bevitt_instagram, FILTER_VALIDATE_URL)) {
                    $insta_hiba = "Nem megfelelő formátumú hivatkozás!";
                } elseif (!strpos($bevitt_instagram, 'instagram.com')) {
                    $insta_hiba = "Nem megfelelő hivatkozás";
                } else {
                    $insta = $bevitt_instagram;
                }
            }
            
            if (empty($eloado_hiba) && empty($kiado_hiba) && empty($debut_hiba) && empty($rajongok_hiba) && empty($yt_hiba) && empty($face_hiba) && empty($twt_hiba) && empty($insta_hiba)) {
            $kiado_keres = "SELECT kiadok.ID_kiado, kiadok.kiado_neve
                    FROM kiadok
                    WHERE ";
            $kiado_keres .= "kiado_neve LIKE '%" . $kiado . "%'";
            $utasitas_kiado = $adatbazisom->query($kiado_keres);
            $kiado_eredmeny = $utasitas_kiado->fetchAll(PDO::FETCH_ASSOC);
            if (count($kiado_eredmeny) > 0) {
                foreach ($kiado_eredmeny as $kiado_adatok) {
                    $id_kiado = $kiado_adatok["ID_kiado"];
                    $kiado_nev = $kiado_adatok["kiado_neve"];
                }
            } else {
                $kiado_hozzaad = $adatbazisom->prepare("INSERT INTO kiadok (kiado_neve) VALUES (:kiado)");
                $kiado_hozzaad->bindParam(':kiado', $bevitt_kiado);
                $kiado_hozzaad->execute();
                $id_kiado = $adatbazisom->lastInsertId();
            }  
                
            $frissit = $adatbazisom->prepare("UPDATE eloadok SET debut_ido=?, kiado_ID=?, fan_club=?, youtube=?, facebook=?, twitter=?, instagram=?
                                            WHERE ID_eloado=?");
            $frissit->bindParam(1, $debut);
            $frissit->bindParam(2, $id_kiado);
            $frissit->bindParam(3, $rajongok);
            $frissit->bindParam(4, $yt);
            $frissit->bindParam(5, $face);
            $frissit->bindParam(6, $twt);
            $frissit->bindParam(7, $insta);
            $frissit->bindParam(8, $id_eloado);
            
            if ($frissit->execute()) {
                header("location: eloadok.php");
                exit();
            } else {
                echo "<p class='hiba'>Valami hiba történt</p>";
            }
            
            }
        }
    } else {
        ?>
        <script>location.href = "http://localhost/forditasokk/altalanos/hiba.php"</script>
        <?php
    }
    ?> 
    <div class="tartalom">
        <div class="focim">
            <h2>Előadó szerkesztése</h2>
        </div>

        <form class="eloado_hozzaad minden_form" method="POST">
            <div <?php echo (!empty($eloado_hiba)) ? 'Hiba történt!' : ''; ?> class="cimke1" >
                <label>Előadó neve*</label><br>
                <input type="text" id="eloado" name="eloado" required  value="<?php echo $eloado; ?>">
                <span class="hiba"><?php echo $eloado_hiba; ?></span>
            </div>

            <div <?php echo (!empty($debut_hiba)) ? 'Hiba történt!' : ''; ?> class="cimke2">
                <label>Debütálás ideje</label><br>
                <input type="date" id="debut" name="debut" value="<?php echo $debut; ?>">
                <span class="hiba"><?php echo $debut_hiba; ?></span>
            </div>

            <div <?php echo (!empty($kiado_hiba)) ? 'Hiba történt!' : ''; ?>  class="cimke1" >
                <label>Kiadó neve*</label><br>
                <input type="text" id="kiado" name="kiado" required value="<?php echo $kiado; ?>" >
                <span class="hiba"><?php echo $kiado_hiba; ?></span>
            </div>

            <div <?php echo (!empty($rajongok_hiba)) ? 'Hiba történt!' : ''; ?>  class="cimke2">
                <label>Rajongótábor neve</label><br>
                <input type="text" id="rajongok" name="rajongok" value="<?php echo $rajongok; ?>">
                <span class="hiba"><?php echo $rajongok_hiba; ?></span>
            </div>
            <hr class="vonal">
            <div <?php echo (!empty($yt_hiba)) ? 'Hiba történt!' : ''; ?>  class="cimke1">
                <label>Hivatalos YouTube csatorna</label><br>
                <input type="text" id="youtube" name="youtube" value="<?php echo $yt; ?>">
                <span class="hiba"><?php echo $yt_hiba; ?></span>
            </div>

            <div <?php echo (!empty($face_hiba)) ? 'Hiba történt!' : ''; ?>   class="cimke2">
                <label>Hivatalos facebook oldal</label><br>
                <input type="text" id="facebook" name="facebook" value="<?php echo $face; ?>">
                <span class="hiba"><?php echo $face_hiba; ?></span>
            </div>

            <div <?php echo (!empty($twt_hiba)) ? 'Hiba történt!' : ''; ?>   class="cimke1">
                <label>Hivatalos twitter fiók</label><br>
                <input type="text" id="twitter" name="twitter" value="<?php echo $twt; ?>">
                <span class="hiba"><?php echo $twt_hiba; ?></span>
            </div>

            <div <?php echo (!empty($insta_hiba)) ? 'Hiba történt!' : ''; ?>   class="cimke2">
                <label>Hivatalos Instagram fiók</label><br>
                <input type="text" id="instagram" name="instagram" value="<?php echo $insta; ?>">
                <span class="hiba"><?php echo $insta_hiba; ?></span>
            </div>
            <br>
            <button class="gomb" type="submit" name="eloado_szerkeszt">Előadó szerkesztése</button>
            <p class="link vissza_link"><a href="eloadok.php">Vissza az előadókhoz</a></p>
        </form>

    </div>      
    <?php
} else {
    ?>
    <script>location.href = "http://localhost/forditasokk/kezdolap.php"</script>
    <?php
}
include_once '../altalanos/lablec.php';
?>