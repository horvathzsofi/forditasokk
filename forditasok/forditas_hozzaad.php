<?php
require_once '../altalanos/konfig.php';  // konfig.php tartalmának beszúrása
include_once '../altalanos/fejlec.php'; //fejléc beszúrása

if (isset($_SESSION['felhasznalonev'])) {
    $id_felhasznalo = $_SESSION['id_felhasznalo'];
    $letezo_forditas = false;
    if (isset($_GET['ID_zenek']) && !empty(trim($_GET['ID_zenek']))) {
        if (!isset($forditott_cim_hiba)) {
            $forditott_cim_hiba = '';
        }
        if (!isset($forditott_szoveg_hiba)) {
            $forditott_szoveg_hiba = '';
        }
        if (!isset($eloado_hiba)) {
            $eloado_hiba = '';
        }
        if (!isset($yt_hiba)) {
            $yt_hiba = '';
        }
        if (!isset($album_hiba)) {
            $album_hiba = '';
        }
        if (!isset($focimdal_hiba)) {
            $focimdal_hiba = '';
        }
        $forditott_cim = $eloado = $album = $forditott_szoveg = $yt = '';
        //hibaüzenet változói

        $focimdal = '';
        $id_zene = htmlspecialchars(trim($_GET['ID_zenek']));
        $forditas_lekeredezes = $adatbazisom->prepare("SELECT * FROM forditasok WHERE felhasznalo_ID = ? AND zene_ID = ?");
        $forditas_lekeredezes->bindParam(1, $id_felhasznalo);
        $forditas_lekeredezes->bindParam(2, $id_zene);
        $forditas_lekeredezes->execute();
        $forditas_eredmeny = $forditas_lekeredezes->fetchAll(PDO::FETCH_ASSOC);
        if (count($forditas_eredmeny) > 0) {

            echo "<script>location.href ='../felhasznalo/forditasom_szerkesztese.php?ID_zenek=" . $id_zene . "'</script>";
        }
        $zene_lekerdezes = "SELECT zeneszamok.*, eloadok.ID_eloado, eloadok.eloado_neve
                    FROM kapcsolo_zene_album_eloado
                    INNER JOIN eloadok
                        ON kapcsolo_zene_album_eloado.eloado_ID = eloadok.ID_eloado
                    INNER JOIN zeneszamok
                        ON kapcsolo_zene_album_eloado.zene_ID = zeneszamok.ID_zenek
                    WHERE zeneszamok.ID_zenek=" . $id_zene;
        $zene_utasitas = $adatbazisom->query($zene_lekerdezes);
        $zene_eredmeny = $zene_utasitas->fetchAll(PDO::FETCH_ASSOC);
        //A form elküldésekor ellenőrzi, hogy van e üres mező, hibát dob ha igen...
        if (isset($_POST["forditas_hozzaad"])) { //megnézi hogy POST kérést indítottak-e
            $bevitt_forditott_cim = htmlspecialchars(trim($_POST["forditott_cim"])); //a szuperglobális POST változóból kiolvassa a dal címét és hozzárendeli a bevitt_dal_cimhez
            if (empty($bevitt_forditott_cim)) { //ha üresen hagyták a mezőt, akkor hibaüzenetet dob
                $forditott_cim_hiba = "A fordított címet meg kell adni!";
            } else {
                $forditott_cim = $bevitt_forditott_cim;
            }

            if (isset($_POST["forditott_szoveg"])) {
                $bevitt_forditott_szoveg = htmlspecialchars(trim($_POST["forditott_szoveg"]));
                if (empty($bevitt_forditott_szoveg)) {
                    $forditott_szoveg_hiba = "A fordítás hozzáadásához ki kell töltenie a mezőt!";
                } elseif (strlen($bevitt_forditott_szoveg) < 300) {
                    $forditott_szoveg_hiba = 'Nem megfelelő tartalmat adott meg!';
                } else {
                    $forditott_szoveg = $bevitt_forditott_szoveg;
                }
            }
            //ha nem hagytak üresen mezőt, azaz nem volt hiba
            if (empty($forditott_cim_hiba) && empty($forditott_szoveg_hiba)) {
                $forditas_lekeredezes = $adatbazisom->prepare("SELECT * FROM forditasok WHERE felhasznalo_ID = ? AND zene_ID = ?");
                $forditas_lekeredezes->bindParam(1, $id_felhasznalo);
                $forditas_lekeredezes->bindParam(2, $id_zene);
                $forditas_lekeredezes->execute();
                $forditas_eredmeny = $forditas_lekeredezes->fetchAll(PDO::FETCH_ASSOC);
                if (count($forditas_eredmeny) > 0) {

                    echo "<script>location.href ='../felhasznalo/forditasom_szerkesztese.php?ID_zenek=" . $id_zene . "'</script>";
                } else {
                    $forditas_hozzaad = $adatbazisom->prepare("INSERT INTO forditasok (dal_forditott_cime, forditas, felhasznalo_ID, zene_ID)
                                                     VALUES (:forditott_cim, :forditas, :fordito, :zene)");
                    $forditas_hozzaad->bindParam(':forditott_cim', $forditott_cim);
                    $forditas_hozzaad->bindParam(':forditas', $forditott_szoveg);
                    $forditas_hozzaad->bindParam(':fordito', $id_felhasznalo);
                    $forditas_hozzaad->bindParam(':zene', $id_zene);

                    if ($forditas_hozzaad->execute()) {
?>
                        <script>
                            location.href = "forditasok.php"
                        </script>
        <?php
                    } else {
                        echo "Valami hiba történt!";
                    }
                }
            }
        }
        ?>

        <div class="tartalom">
            <?php
            //változók létrehozása
            if (count($zene_eredmeny) > 0) {
                foreach ($zene_eredmeny as $zene_adatok) {
                    $id_zene = $zene_adatok['ID_zenek'];
                    $zene_cime = $zene_adatok['zene_cim'];
                    $dalszoveg = $zene_adatok['dalszoveg'];
                    $id_eloado = $zene_adatok['ID_eloado'];
                    $eloado = $zene_adatok['eloado_neve'];

                    if ($dalszoveg != null) {
            ?>
                        <div class="focim">
                            <h2>Fordítás hozzáadása</h2>
                        </div>

                        <div class="dalszovegek">
                            <div class="zeneszoveg">
                                <div>
                                    <h3>
                                        <?php echo $zene_cime; ?>
                                    </h3>
                                </div>
                                <div>
                                    <?php echo nl2br($dalszoveg); ?>
                                </div>
                            </div>
                            <div class="forditas">
                                <form class="forditas_hozzaadas" method="POST">
                                    <div <?php echo (!empty($forditott_cim_hiba)) ? 'Hiba történt!' : ''; ?>>
                                        <label>Dal fordított címe</label> <br>
                                        <input type="text" id="forditott_cim" name="forditott_cim" value="<?php echo $forditott_cim; ?>">
                                        <br><span class="hiba"> <?php echo $forditott_cim_hiba; ?> </span>
                                    </div>

                                    <div <?php echo (!empty($forditott_szoveg_hiba)) ? 'Hiba történt!' : ''; ?>>
                                        <label>Lefordított dalszöveg</label> <br>
                                        <span class="hiba"><?php echo $forditott_szoveg_hiba; ?></span>
                                        <textarea name="forditott_szoveg" value="<?php echo $forditott_szoveg; ?>"></textarea>

                                    </div>

                                    <input type="submit" class="gomb" name="forditas_hozzaad" value="Fordítás hozzáadása">
                                    <p class="link"><a href="forditasok.php">Vissza a fordításokhoz</a></p>
                                </form>
                            </div>
                        </div>

        <?php
                    } else {
                        echo "<h4 class='nincs_dalszoveg'>Nem érhető el dalszöveg a dalhoz.<br>Frissítsd a dal szövegét >>"
                            . "<a href='../zenek/zene_szerkeszt.php?ID_zenek=" . $id_zene . "'>IDE KATTINTVA</a>"
                            . "<<, hogy hozzáadhasd a fordításod!</h4>";
                    }
                }
            }
        }
        ?>


        </div>
    <?php
} else {
    ?>
        <script>
            location.href = "../index.php"
        </script>
    <?php
}
unset($adatbazisom);
include_once '../altalanos/lablec.php';
    ?>