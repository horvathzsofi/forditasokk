<?php
require_once'../altalanos/konfig.php';  // konfig.php tartalmának beszúrása
include_once '../altalanos/fejlec.php'; //fejléc beszúrása

if (isset($_SESSION['felhasznalonev'])) {
    $profil = "altalanos/kepek/artist.png";
    $hiba=$uzenet=false;
    $hiba_uzenet='';
    $id_kiado = $kiado_nev = '';
    $debut = $debut_hiba = '';
    $eloado = $kiado = $rajongok = $yt = $twt = $face = $insta = '';
    $eloado_hiba = $kiado_hiba = $rajongok_hiba = $yt_hiba = $twt_hiba = $face_hiba = $insta_hiba = '';
    if (isset($_POST["eloado_hozzaad"])) {

        $bevitt_eloado = htmlspecialchars(trim($_POST["eloado"]));
        if (empty($bevitt_eloado)) {
            $eloado_hiba = "Az előadót meg kell adni!";
        } elseif (!filter_var($bevitt_eloado, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^[a-zA-Z0-9+(.!%_:*'()\- )]/")))) {
            $eloado_hiba = "Az előadó neve nem megfelelő karaktereket tartalmaz!";
        } else {
            $eloado = $bevitt_eloado;
        }

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
        }
        else {
            $yt = $bevitt_youtube;
        }
    }

    if (!empty($_POST["facebook"])) {
        $bevitt_facebook = htmlspecialchars(trim($_POST["facebook"]));
        if (!filter_var($bevitt_facebook, FILTER_VALIDATE_URL)) {
            $face_hiba = "Nem megfelelő formátumú hivatkozás!";
        }
        elseif (!strpos($bevitt_facebook, 'facebook.com')) {
            $face_hiba = "Nem megfelelő hivatkozás";
        }
        else {
            $face = $bevitt_facebook;
        }
    }

    if (!empty($_POST["twitter"])) {
        $bevitt_twitter = htmlspecialchars(trim($_POST["twitter"]));
        if (!filter_var($bevitt_twitter, FILTER_VALIDATE_URL)) {
            $twt_hiba = "Nem megfelelő formátumú hivatkozás!";
        }
        elseif (!strpos($bevitt_twitter, 'twitter.com')) {
            $twt_hiba = "Nem megfelelő hivatkozás";
        }
        else {
            $twt = $bevitt_twitter;
        }
    }

    if (!empty($_POST["instagram"])) {
        $bevitt_instagram = htmlspecialchars(trim($_POST["instagram"]));
        if (!filter_var($bevitt_instagram, FILTER_VALIDATE_URL)) {
            $insta_hiba = "Nem megfelelő formátumú hivatkozás!";
        }
        elseif (!strpos($bevitt_instagram, 'instagram.com')) {
            $insta_hiba = "Nem megfelelő hivatkozás";
        }
        else {
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

            $eloado_keres = "SELECT eloadok.ID_eloado, eloadok.eloado_neve
                    FROM eloadok
                    WHERE ";
            $eloado_keres .= "eloado_neve LIKE '%" . $eloado . "%'";
            $utasitas_eloado = $adatbazisom->query($eloado_keres);
            $eloado_eredmeny = $utasitas_eloado->fetchAll(PDO::FETCH_ASSOC);
            if (count($eloado_eredmeny) > 0) {
                $hiba_uzenet='';
                $hiba=$uzenet=true;
                $hiba_uzenet = "Az előadót már hozzáadták.";
            } else {

                $utasitas = $adatbazisom->prepare("INSERT INTO eloadok (eloado_neve, debut_ido, kiado_ID, profil_kep, fan_club, youtube, facebook, twitter, instagram)
                                         VALUES (:eloado, :debut, :kiado, :profil, :fanclub, :yt, :face, :twt, :insta)");
                $utasitas->bindParam(':eloado', $eloado);
                $utasitas->bindParam(':debut', $debut);
                $utasitas->bindParam(':kiado', $id_kiado);
                $utasitas->bindParam(':profil', $profil);
                $utasitas->bindParam(':fanclub', $rajongok);
                $utasitas->bindParam(':yt', $yt);
                $utasitas->bindParam(':face', $face);
                $utasitas->bindParam(':twt', $twt);
                $utasitas->bindParam(':insta', $insta);
                if ($utasitas->execute()) {
                    header("location: ../eloadok/eloadok.php");
                    exit();
                } else {
                    echo "Valami hiba történt.";
                }
            }
        }
    }
    ?>
   
    <div class="tartalom">
            <?php if($uzenet==true){?>
    <div class="uzenet_van">
        <?php
            if($hiba){
                echo "<div class='hiba_uzi'>".$hiba_uzenet."</div>";
                $hiba_uzenet='';
                $uzenet=false;
            }
        ?>
    </div>
        <?php
        }
    ?>
    <div class="focim">
        <h2>Előadó hozzáadása</h2>
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
            <button class="gomb" type="submit" name="eloado_hozzaad">Előadó hozzáadása</button>
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