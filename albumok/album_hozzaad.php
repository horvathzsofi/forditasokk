<?php
require_once '../altalanos/konfig.php';  // konfig.php tartalmának beszúrása
include_once '../altalanos/fejlec.php'; //fejléc beszúrása

if (isset($_SESSION['felhasznalonev'])) {
    //változók létrehozása
    $eloado = $album = '';
    //hibaüzenet változói
    $eloado_hiba = $album_hiba = '';
    $megjelenes_hiba = $megjelenes = '';
    $hiba = $uzenet = false;
    $hiba_uzenet = '';
    //A form elküldésekor ellenőrzi, hogy van e üres mező, hibát dob ha igen...
    if (isset($_POST["album_hozzaad"])) { //megnézi hogy POST kérést indítottak-e
        //bevitt értékek validálása

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

            $eloado_keres = "SELECT eloadok.ID_eloado, eloadok.eloado_neve
                    FROM eloadok
                    WHERE ";
            //        $eloado_keres.= 'eloado_neve LIKE "%'.stripslashes($eloado).'%"';
            $eloado_keres .= 'eloado_neve LIKE "%' . $eloado . '%"';
            $utasitas_eloado = $adatbazisom->query($eloado_keres);
            $eloado_eredmeny = $utasitas_eloado->fetchAll(PDO::FETCH_ASSOC);
            if (count($eloado_eredmeny) > 0) {
                foreach ($eloado_eredmeny as $eloado_adatok) {
                    $id_eloado = $eloado_adatok["ID_eloado"];
                    $eloado = $eloado_adatok["eloado_neve"];
                }
            } else {
                //            $eloado= stripcslashes($eloado);
                $eloado_hozzaad = $adatbazisom->prepare("INSERT INTO eloadok (eloado_neve) VALUES (:eloado)");
                $eloado_hozzaad->bindParam(':eloado', $eloado);
                $eloado_hozzaad->execute();
                $id_eloado = $adatbazisom->lastInsertId();
            }

            $album_keres = "SELECT albumok.ID_album, albumok.album_cime
                    FROM albumok
                    WHERE ";
            //        $album_keres.='album_cime LIKE "%'.stripslashes($album).'%"';
            $album_keres .= 'album_cime LIKE "%' . $album . '%"';
            $utasitas_album = $adatbazisom->query($album_keres);
            $album_eredmeny = $utasitas_album->fetchAll(PDO::FETCH_ASSOC);
            if (count($album_eredmeny) > 0) {
                foreach ($album_eredmeny as $album_adatok) {
                    $id_album = $album_adatok["ID_album"];
                    $album = $album_adatok["album_cime"];
                }
            } else {
                //            $album= stripcslashes($album);
                $album_hozzaad = $adatbazisom->prepare("INSERT INTO albumok (album_cime, megjelenes) VALUES (:album, :megjelenes)");
                $album_hozzaad->bindParam(':album', $album);
                $album_hozzaad->bindParam(':megjelenes', $megjelenes);
                $album_hozzaad->execute();
                $id_album = $adatbazisom->lastInsertId();
            }



            if (!empty($id_eloado) && !empty($id_album)) {
                $kapcsolat_lekerdezes = $adatbazisom->prepare("SELECT
                                                        eloadok.ID_eloado,
                                                        albumok.ID_album
                                                      FROM kapcsolo_zene_album_eloado
                                                        INNER JOIN albumok
                                                          ON kapcsolo_zene_album_eloado.album_ID = albumok.ID_album
                                                        INNER JOIN eloadok
                                                          ON kapcsolo_zene_album_eloado.eloado_ID = eloadok.ID_eloado
                                                      WHERE eloado_ID = ? AND album_ID = ?");
                $kapcsolat_lekerdezes->bindParam(1, $id_eloado);
                $kapcsolat_lekerdezes->bindParam(2, $id_album);

                $kapcsolat_lekerdezes->execute();
                $kapcsolat_eredmeny = $kapcsolat_lekerdezes->fetchAll(PDO::FETCH_ASSOC);
                if (count($kapcsolat_eredmeny) > 0) {
                    $hiba_uzenet = '';
                    $hiba = $uzenet = true;
                    $hiba_uzenet = "Az albumot már hozzáadták!";
                } else {
                    $osszakapcsol = $adatbazisom->prepare("INSERT INTO kapcsolo_zene_album_eloado (eloado_ID, album_ID) VALUES (:eloado, :album)");
                    $osszakapcsol->bindParam(':eloado', $id_eloado);
                    $osszakapcsol->bindParam(':album', $id_album);
                    if ($osszakapcsol->execute()) {
                        header("location: albumok.php");
                        exit();
                    } else {
                        echo "Valami hiba történt.";
                    }
                }
                unset($utasitas_album);
                unset($album_hozzaad);
                unset($eloado_hozzaad);
                unset($utasitas_eloado);
                unset($osszakapcsol);
                unset($kapcsolat_lekerdezes);
                unset($kapcsolat_eredmeny);
                unset($adatbazisom);
            }
        }
    }
?>
    <div class="tartalom">
        <?php if ($uzenet == true) { ?>
            <div class="uzenet_van">
                <?php
                if ($hiba) {
                    echo "<div class='hiba_uzi'>" . $hiba_uzenet . "</div>";
                    $hiba_uzenet = '';
                    $uzenet = false;
                }
                ?>
            </div>
        <?php
        }
        ?>

        <div class="focim">
            <h2>Album hozzáadása</h2>
        </div>

        <form class="album_hozzaad minden_form" method="POST">


            <div <?php echo (!empty($eloado_hiba)) ? 'Hiba történt!' : ''; ?>>
                <label>Előadó neve*</label> <br>
                <input type="text" id="eloado" name="eloado" value="<?php echo $eloado; ?>" required><br>
                <span class="hiba"><?php echo $eloado_hiba; ?></span>
            </div>
            <div <?php echo (!empty($album_hiba)) ? 'Hiba történt!' : ''; ?>>
                <label>Album címe*</label> <br>
                <input type="text" id="album" name="album" value="<?php echo $album; ?>" required><br>
                <span class="hiba"><?php echo $album_hiba; ?></span>
            </div>

            <div <?php echo (!empty($megjelenes_hiba)) ? 'Hiba történt!' : ''; ?>>
                <label>Megjelenés*</label> <br>
                <input type="date" id="megjelenes" name="megjelenes" required><br>
                <span class="hiba"><?php echo $megjelenes_hiba; ?></span>
            </div>


            <button class="gomb" type="submit" name="album_hozzaad">Album hozzáadása</button>
            <p class="link"><a href="albumok.php">Vissza az albumokhoz</a></p>

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