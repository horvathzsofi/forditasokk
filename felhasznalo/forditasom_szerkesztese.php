<?php
require_once '../altalanos/konfig.php';
include_once '../altalanos/fejlec.php'; //fejléc beszúrása

if (isset($_SESSION['felhasznalonev'])) {
    $id_felhasznalo = $_SESSION['id_felhasznalo'];
    $forditott_cim_hiba = $forditott_szoveg_hiba = '';

    if (isset($_GET['ID_zenek']) && !empty(trim($_GET['ID_zenek']))) {
        $id_zene = htmlspecialchars(trim($_GET['ID_zenek']));

        $forditas_lekerdezes = $adatbazisom->prepare("SELECT
                                    zeneszamok.*,
                                    forditasok.*,
                                    felhasznalok.felhasznalonev,
                                    felhasznalok.ID_felhasznalo
                                  FROM forditasok
                                    INNER JOIN zeneszamok
                                      ON forditasok.zene_ID = zeneszamok.ID_zenek
                                    INNER JOIN felhasznalok
                                      ON forditasok.felhasznalo_ID = felhasznalok.ID_felhasznalo
                                    WHERE ID_zenek=? AND ID_felhasznalo=?");
        $forditas_lekerdezes->bindParam(1, $id_zene);
        $forditas_lekerdezes->bindParam(2, $id_felhasznalo);

        $forditas_lekerdezes->execute();
        $forditas_eredmeny = $forditas_lekerdezes->fetchAll(PDO::FETCH_ASSOC);
        if (count($forditas_eredmeny) > 0) {
            foreach ($forditas_eredmeny as $adatsor) {
                $zene_cime = $adatsor['zene_cim'];
                $dalszoveg = $adatsor['dalszoveg'];
                $forditott_cim = $adatsor['dal_forditott_cime'];
                //        echo $forditott_cim;
                $forditas = $adatsor['forditas'];
                //        echo $forditas;
            }
        } else {
?>
            <script>
                location.href = "../altalanos/nem_talalhato.php"
            </script>
        <?php
        }
        if (isset($_POST['forditas_frissit'])) {
            $bevitt_forditott_cim = htmlspecialchars(trim($_POST['forditott_cim']));
            if (empty($bevitt_forditott_cim)) {
                $forditott_cim_hiba = "A fordítás címét meg kell adni!";
            } else {
                $forditott_cim = $bevitt_forditott_cim;
            }

            $bevitt_forditas = htmlspecialchars(trim($_POST['forditott_szoveg']));
            if (empty($bevitt_forditas)) {
                $forditott_szoveg_hiba = "A fordított szoveget meg kell adni!";
            } elseif (strlen($bevitt_forditas) < 300) {
                $forditott_szoveg_hiba = "Nem megfelelő tartalmat próbál hozzáadni.";
            } else {
                $forditas = $bevitt_forditas;
            }

            if (empty($forditott_szoveg_hiba) && empty($forditott_cim_hiba)) {
                $frissit = $adatbazisom->prepare("UPDATE forditasok SET dal_forditott_cime=?, forditas=? WHERE felhasznalo_ID=? AND zene_ID=?");
                $frissit->bindParam(1, $forditott_cim);
                $frissit->bindParam(2, $forditas);
                $frissit->bindParam(3, $id_felhasznalo);
                $frissit->bindParam(4, $id_zene);

                if ($frissit->execute()) {
                    header("location: forditasaim.php");
                    exit();
                } else {
                    echo "<p class='hiba'>Valami hiba történt</p>";
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
            <h2>Fordításom szerkesztése</h2>
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
                        <textarea name="forditott_szoveg"><?php echo $forditas; ?></textarea>
                    </div>

                    <input type="submit" class="gomb" name="forditas_frissit" value="Fordítás frissítése">
                    <p><a href="forditasaim.php">Vissza a fordításaimhoz</a></p>
                </form>
            </div>
        </div>
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