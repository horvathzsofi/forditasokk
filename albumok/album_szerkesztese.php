<?php
require_once '../altalanos/konfig.php';
include_once '../altalanos/fejlec.php'; //fejléc beszúrása

if (isset($_SESSION['felhasznalonev'])) {
    $eloado = $album = '';
//hibaüzenet változói
    $eloado_hiba = $album_hiba = '';
    $megjelenes_hiba = $megjelenes = '';
    if (isset($_GET['ID_album']) && !empty(trim($_GET['ID_album']))) {
        $id_album = htmlspecialchars(trim($_GET['ID_album']));
        $album_lekerdezes = $adatbazisom->prepare("SELECT albumok.*,
                                                eloadok.ID_eloado,
                                                eloadok.eloado_neve
                                              FROM kapcsolo_zene_album_eloado
                                                INNER JOIN albumok
                                                  ON kapcsolo_zene_album_eloado.album_ID = albumok.ID_album
                                                INNER JOIN eloadok
                                                  ON kapcsolo_zene_album_eloado.eloado_ID = eloadok.ID_eloado
                                              WHERE ID_album=?");
        $album_lekerdezes->bindParam(1, $id_album);
        $album_lekerdezes->execute();
        $album_eredmeny = $album_lekerdezes->fetchAll(PDO::FETCH_ASSOC);
        if (count($album_eredmeny) > 0) {
            foreach ($album_eredmeny as $adatsor) {
                $id_eloado = $adatsor['ID_eloado'];
                $eloado = $adatsor['eloado_neve'];
                $album = $adatsor['album_cime'];
                $megjelenes = $adatsor['megjelenes'];
            }
        } else {
            ?>
            <script>location.href = "../altalanos/nem_talalhato.php"</script>
            <?php
        }
        if (isset($_POST['album_szerkesztese'])) {
            $bevitt_eloado = htmlspecialchars(trim($_POST["eloado"]));
            if (empty($bevitt_eloado)) {
                $eloado_hiba = "Az előadót meg kell adni!";
            } else {
                $eloado = $bevitt_eloado;
            }

            $bevitt_album = htmlspecialchars(trim($_POST["album"]));
            if (empty($bevitt_album)) {
                $album_hiba = "Az album címét meg kell adni!";
            } else {
                $album = $bevitt_album;
            }
            if (isset($_POST["megjelenes"]) && !empty(htmlspecialchars(trim($_POST["megjelenes"])))) {
                $bevitt_megjelenes = htmlspecialchars(trim($_POST["megjelenes"]));
                if (!filter_var($bevitt_megjelenes, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^(?:19|20)[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-9])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:30))|(?:(?:0[13578]|1[02])-31))/")))) {
                    $megjelenes_hiba = "Nem megfelelő formátum.";
                } else {
                    $megjelenes = $bevitt_megjelenes;
                }
            }
            if (empty($eloado_hiba) && empty($album_hiba) && empty($megjelenes_hiba)) {
                $frissit = $adatbazisom->prepare("UPDATE albumok SET album_cime=?, megjelenes=?
                                                WHERE ID_album=?");
                $frissit->bindParam(1, $album);
                $frissit->bindParam(2, $megjelenes);
                $frissit->bindParam(3, $id_album);
                if ($frissit->execute()) {
                    header("location: albumok.php");
                    exit();
                } else {
                    echo "<p class='hiba'>Valami hiba történt</p>";
                }
            }
        }
    } else {
        ?>
        <script>location.href = "../altalanos/hiba.php"</script>
        <?php
    }
    ?>
    <div class="tartalom"> 
        <div class="focim">
            <h2>Album szeresztése</h2>
        </div>

        <form class="album_hozzaad minden_form" method="POST">


            <div>
                <label>Előadó neve</label> <br>
                <input type="text" id="eloado" name="eloado" value="<?php echo $eloado; ?>" readonly=""><br>
            </div>
            <div <?php echo (!empty($album_hiba)) ? 'Hiba történt!' : ''; ?>>
                <label>Album címe*</label> <br>
                <input type="text" id="album" name="album" value="<?php echo $album; ?>" required><br>
                <span class="hiba"><?php echo $album_hiba; ?></span>
            </div>

            <div <?php echo (!empty($megjelenes_hiba)) ? 'Hiba történt!' : ''; ?> >
                <label>Megjelenés*</label> <br>
                <input type="date" id="megjelenes" name="megjelenes"  value="<?php echo $megjelenes; ?>" required><br>
                <span class="hiba"><?php echo $megjelenes_hiba; ?></span>
            </div>


            <button class="gomb" type="submit" name="album_szerkesztese">Album szerkesztése</button>
            <p class="link"><a href="albumok.php">Vissza az albumokhoz</a></p>

        </form>

    </div>
    <?php
} else {
    ?>
    <script>location.href = "../index.php"</script>
    <?php
}
include_once '../altalanos/lablec.php';
?>