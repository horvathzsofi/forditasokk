<?php
require_once '../altalanos/konfig.php';
include_once '../altalanos/fejlec.php'; //fejléc beszúrása
if (isset($_SESSION['felhasznalonev'])) {

    $id_kiado = '';
    $kiado = $yt = $twt = $face = $insta = "";
    $kiado_hiba = $yt_hiba = $twt_hiba = $face_hiba = $insta_hiba = '';

    if (isset($_GET['ID_kiado']) && !empty(trim($_GET['ID_kiado']))) {
        $id_kiado = htmlspecialchars(trim($_GET['ID_kiado']));
        $kiado_lekerdezes = $adatbazisom->prepare("SELECT * FROM kiadok
                                                WHERE ID_kiado=?");
        $kiado_lekerdezes->bindParam(1, $id_kiado);
        $kiado_lekerdezes->execute();
        $kiado_eredmeny = $kiado_lekerdezes->fetchAll(PDO::FETCH_ASSOC);
        if (count($kiado_eredmeny) > 0) {
            foreach ($kiado_eredmeny as $adat2) {
                $kiado_nev = $adat2['kiado_neve'];
                $yt = $adat2['youtube'];
                $face = $adat2['facebook'];
                $twt = $adat2['twitter'];
                $insta = $adat2['instagram'];
            }
        } else {
?>
            <script>
                location.href = "../altalanos/nem_talalhato.php"
            </script>
        <?php
        }
        if (isset($_POST['kiado_szerkeszt'])) {
            $bevitt_kiado = htmlspecialchars(trim($_POST["kiado"]));
            if (empty($bevitt_kiado)) {
                $kiado_hiba = "A kiadót meg kell adni!";
            } elseif (!filter_var($bevitt_kiado, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^[a-zA-Z0-9]+/")))) {
                $kiado_hiba = "A kiadó neve nem megfelelő karaktereket tartalmaz!";
            } else {
                $kiado = $bevitt_kiado;
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
            if (empty($kiado_hiba) && empty($face_hiba) && empty($yt_hiba) && empty($twt) && empty($insta_hiba)) { {
                    $frissit = $adatbazisom->prepare("UPDATE kiadok SET kiado_neve=?, youtube=?, facebook=?, twitter=?, instagram=?
                                                WHERE ID_kiado=?");
                    $frissit->bindParam(1, $kiado);
                    $frissit->bindParam(2, $yt);
                    $frissit->bindParam(3, $face);
                    $frissit->bindParam(4, $twt);
                    $frissit->bindParam(5, $insta);
                    $frissit->bindParam(6, $id_kiado);
                    if ($frissit->execute()) {
                        header("location: kiadok.php");
                        exit();
                    } else {
                        echo "<p class='hiba'>Valami hiba történt</p>";
                    }
                }
            }
        }
    } else {
        ?>
        <script>
            location.href = "../altalanos/hiba.php"
        </script>
    <?php
    }
    ?>
    <div class="tartalom">

        <div class="focim">
            <h2>Kiadó szerkesztése</h2>
        </div>
        <form class="minden_form" method="POST">

            <div <?php echo (!empty($kiado_hiba)) ? 'Hiba történt!' : ''; ?>>
                <label>Kiadó neve*</label><br>
                <input type="text" id="kiado" name="kiado" required value="<?php echo $kiado_nev; ?>"><br>
                <span class="hiba"><?php echo $kiado_hiba; ?></span>
            </div>

            <div <?php echo (!empty($yt_hiba)) ? 'Hiba történt!' : ''; ?>>
                <label>Hivatalos YouTube csatorna</label><br>
                <input type="text" id="youtube" name="youtube" value="<?php echo $yt; ?>"><br>
                <span class="hiba"><?php echo $yt_hiba; ?></span>
            </div>

            <div <?php echo (!empty($face_hiba)) ? 'Hiba történt!' : ''; ?>>
                <label>Hivatalos facebook oldal</label><br>
                <input type="text" id="facebook" name="facebook" value="<?php echo $face; ?>"><br>
                <span class="hiba"><?php echo $face_hiba; ?></span>
            </div>

            <div <?php echo (!empty($twt_hiba)) ? 'Hiba történt!' : ''; ?>>
                <label>Hivatalos twitter fiók</label><br>
                <input type="text" id="twitter" name="twitter" value="<?php echo $twt; ?>"><br>
                <span class="hiba"><?php echo $twt_hiba; ?></span>
            </div>

            <div <?php echo (!empty($insta_hiba)) ? 'Hiba történt!' : ''; ?>>
                <label>Hivatalos Instagram fiók</label><br>
                <input type="text" id="instagram" name="instagram" value="<?php echo $insta; ?>"><br>
                <span class="hiba"><?php echo $insta_hiba; ?></span>
            </div>


            <button class="gomb" type="submit" name="kiado_szerkeszt">Kiadó szerkesztése</button>
            <p class="vissza_link"><a href="kiadok.php">Vissza a kiadókhoz</a></p>
        </form>
    </div>
<?php
} else {
?>
    <script>
        location.href = "../index.php"
    </script>
<?php
}
include_once '../altalanos/lablec.php';
?>