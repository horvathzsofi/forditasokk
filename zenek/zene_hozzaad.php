<?php
require_once '../altalanos/konfig.php';  // konfig.php tartalmának beszúrása
include_once '../altalanos/fejlec.php'; //fejléc beszúrása


if (isset($_SESSION['felhasznalonev'])) {


    //változók létrehozása
    $dal_cim = $eloado = $album = $dalszoveg = $yt = '';
    //hibaüzenet változói
    $dal_cim_hiba = $eloado_hiba = $album_hiba = $dalszoveg_hiba = $yt_hiba = '';
    $focimdal = $focimdal_hiba = '';
    $hiba = $uzenet = false;
    $hiba_uzenet = '';
    //A form elküldésekor ellenőrzi, hogy van e üres mező, hibát dob ha igen...
    if (isset($_POST["zene_hozzaad"])) { //megnézi hogy POST kérést indítottak-e
        //bevitt értékek validálása
        if (isset($_POST["focimdal"]) && !empty(htmlspecialchars(trim($_POST["focimdal"])))) {
            $bevitt_focimdal = htmlspecialchars(trim($_POST["focimdal"]));
        } else {
            $bevitt_focimdal = '';
        }
        if (empty($bevitt_focimdal)) {
            $focimdal_hiba = "Meg kell adni!";
        } elseif (!filter_var($bevitt_focimdal, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "(\b(\w*igen|IGEN|Igen|nem|NEM|Nem\w*)\b)")))) {
            $focimdal_hiba = "Nem megfelelő formátum!";
        } else {
            if ($bevitt_focimdal == "igen" || $bevitt_focimdal == "IGEN" || $bevitt_focimdal == "Igen") {
                $focimdal = 1;
            } else {
                $focimdal = 0;
            }
        }


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


        $bevitt_dal_cim = htmlspecialchars(trim($_POST["dal_cim"])); //a szuperglobális POST változóból kiolvassa a dal címét és hozzárendeli a bevitt_dal_cimhez
        if (empty($bevitt_dal_cim)) { //ha üresen hagyták a mezőt, akkor hibaüzenetet dob
            $dal_cim_hiba = "A dal címét meg kell adni!";
        } else {
            $dal_cim = $bevitt_dal_cim;
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
        if (!empty($_POST["dalszoveg"])) {
            $bevitt_dalszoveg = htmlspecialchars(trim($_POST["dalszoveg"]));
            if (strlen($bevitt_dalszoveg) < 300) {
                $dalszoveg_hiba = 'Nem megfelelő tartalmat adott meg!';
            } else {
                $dalszoveg = $bevitt_dalszoveg;
            }
        }
        //ha nem hagytak üresen mezőt, azaz nem volt hiba
        if (empty($dal_cim_hiba) && empty($eloado_hiba) && empty($album_hiba) && empty($dalszoveg_hiba) && empty($focimdal_hiba) && empty($yt_hiba)) {
            $eloado_keres = "SELECT eloadok.ID_eloado, eloadok.eloado_neve
                    FROM eloadok
                    WHERE ";
            $eloado_keres .= "eloado_neve LIKE '%" . $eloado . "%'";
            $utasitas_eloado = $adatbazisom->query($eloado_keres);
            $eloado_eredmeny = $utasitas_eloado->fetchAll(PDO::FETCH_ASSOC);
            if (count($eloado_eredmeny) > 0) {
                foreach ($eloado_eredmeny as $eloado_adatok) {
                    $id_eloado = $eloado_adatok["ID_eloado"];
                    $eloado = $eloado_adatok["eloado_neve"];
                }
            } else {
                $eloado_hozzaad = $adatbazisom->prepare("INSERT INTO eloadok (eloado_neve) VALUES (:eloado)");
                $eloado_hozzaad->bindParam(':eloado', $eloado);
                $eloado_hozzaad->execute();
                $id_eloado = $adatbazisom->lastInsertId();
            }

            $album_keres = "SELECT albumok.ID_album, albumok.album_cime
                    FROM albumok
                    WHERE ";
            $album_keres .= "album_cime LIKE '%" . $album . "%'";
            $utasitas_album = $adatbazisom->query($album_keres);
            $album_eredmeny = $utasitas_album->fetchAll(PDO::FETCH_ASSOC);
            if (count($album_eredmeny) > 0) {
                foreach ($album_eredmeny as $album_adatok) {
                    $id_album = $album_adatok["ID_album"];
                    $album = $album_adatok["album_cime"];
                }
            } else {
                $album_hozzaad = $adatbazisom->prepare("INSERT INTO albumok (album_cime) VALUES (:album)");
                $album_hozzaad->bindParam(':album', $album);
                $album_hozzaad->execute();
                $id_album = $adatbazisom->lastInsertId();
            }
            $zene_lekerdezes = 'SELECT zeneszamok.zene_cim, albumok.ID_album, eloadok.ID_eloado
                    FROM kapcsolo_zene_album_eloado
                    INNER JOIN albumok
                        ON kapcsolo_zene_album_eloado.album_ID = albumok.ID_album
                    INNER JOIN eloadok
                        ON kapcsolo_zene_album_eloado.eloado_ID = eloadok.ID_eloado
                    INNER JOIN zeneszamok
                        ON kapcsolo_zene_album_eloado.zene_ID = zeneszamok.ID_zenek
                    WHERE ';
            $zene_lekerdezes .= 'ID_eloado=' . $id_eloado;
            $zene_lekerdezes .= ' AND ID_album=' . $id_album;
            $zene_lekerdezes .= ' AND zene_cim LIKE "%' . $dal_cim . '%"';

            $zene_utasitas = $adatbazisom->query($zene_lekerdezes);
            $zene_eredmeny = $zene_utasitas->fetchAll(PDO::FETCH_ASSOC);

            if (count($zene_eredmeny) > 0) {
                $hiba_uzenet = '';
                $hiba = $uzenet = true;
                $hiba_uzenet = "A zenét már hozzáadták!";
            } else {
                if (empty($yt)) {
                    $zene_hozzaadas = $adatbazisom->prepare("INSERT INTO zeneszamok (zene_cim, dalszoveg) VALUES (:dal_cim, :dalszoveg);");
                    $zene_hozzaadas->bindParam(':dal_cim', $dal_cim);
                    $zene_hozzaadas->bindParam(':dalszoveg', $dalszoveg);
                    $zene_hozzaadas->execute();
                    $id_zene = $adatbazisom->lastInsertId();
                } else {
                    $zene_hozzaadas = $adatbazisom->prepare("INSERT INTO zeneszamok (zene_cim, dalszoveg, youtube_link) VALUES (:dal_cim, :dalszoveg, :yt);");
                    $zene_hozzaadas->bindParam(':dal_cim', $dal_cim);
                    $zene_hozzaadas->bindParam(':dalszoveg', $dalszoveg);
                    $zene_hozzaadas->bindParam(':yt', $yt);
                    $zene_hozzaadas->execute();
                    $id_zene = $adatbazisom->lastInsertId();
                }
            }

            if (!empty($id_zene) && !empty($id_eloado) && !empty($id_album)) {
                $kapcsolat_lekerdezes = $adatbazisom->prepare("SELECT
                                                        kapcsolo_zene_album_eloado.eloado_ID,
                                                        kapcsolo_zene_album_eloado.album_ID,
                                                        kapcsolo_zene_album_eloado.zene_ID
                                                      FROM kapcsolo_zene_album_eloado");
                $kapcsolat_lekerdezes->bindParam(':id_eloado', $id_eloado);
                $kapcsolat_lekerdezes->bindParam(':id_album', $id_album);
                $kapcsolat_lekerdezes->bindParam(':id_album', $id_zene);
                if (count($kapcsolat_eredmeny) > 0) {
                    $hiba_uzenet = '';
                    $hiba = $uzenet = true;
                    $hiba_uzenet = "A zenét már hozzáadták!";
                } else {
                    $osszakapcsol = $adatbazisom->prepare("INSERT INTO kapcsolo_zene_album_eloado (eloado_ID, album_ID, zene_ID, focimdal_e) VALUES (:eloado, :album, :zene, :focimdal)");
                    $osszakapcsol->bindParam(':eloado', $id_eloado);
                    $osszakapcsol->bindParam(':album', $id_album);
                    $osszakapcsol->bindParam(':zene', $id_zene);
                    $osszakapcsol->bindParam(':focimdal', $focimdal);

                    if ($osszakapcsol->execute()) {
                        header("location: zenek.php");
                        exit();
                    } else {
                        echo "Valami hiba történt.";
                    }
                }
                unset($utasitas_album);
                unset($album_hozzaad);
                unset($eloado_hozzaad);
                unset($utasitas_eloado);
                unset($zene_hozzaadas);
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
            <h2>Zene hozzáadása</h2>
        </div>
        <form class="zene_hozzaad minden_form" method="POST">
            <div class="cimke1">
                <div <?php echo (!empty($eloado_hiba)) ? 'Hiba történt!' : ''; ?>>
                    <label>Előadó neve*</label> <br>
                    <input type="text" name="eloado" value="<?php echo $eloado; ?>">
                    <br>
                    <span class="hiba"><?php echo $eloado_hiba; ?></span>
                </div>
                <div <?php echo (!empty($album_hiba)) ? 'Hiba történt!' : ''; ?>>
                    <label>Album címe*</label> <br>
                    <input type="text" name="album" value="<?php echo $album; ?>">
                    <br>
                    <span class="hiba"><?php echo $album_hiba; ?></span>
                </div>

                <div <?php echo (!empty($dal_cim_hiba)) ? 'Hiba történt!' : ''; ?>>
                    <label>Dal címe*</label> <br>
                    <input type="text" name="dal_cim" value="<?php echo $dal_cim; ?>">
                    <br>
                    <span class="hiba"><?php echo $dal_cim_hiba; ?></span>
                </div>

                <div <?php echo (!empty($focimdal_hiba)) ? 'Hiba történt!' : ''; ?>>
                    <label>Főcímdal*</label> <br>
                    <div class="valaszto">
                        <div>
                            <input type="radio" id="focimdal_igen" name="focimdal" value="igen">
                            <label for="focimdal_igen">Igen</label>
                        </div>
                        <div>
                            <input type="radio" id="focimdal_nem" name="focimdal" value="nem">
                            <label for="focimdal_nem">Nem</label>
                        </div>
                    </div>
                    <span class="hiba"><?php echo $focimdal_hiba; ?></span><br>
                </div>


                <div <?php echo (!empty($yt_hiba)) ? 'Hiba történt!' : ''; ?>>
                    <label>Hivatalos YouTube videó</label>
                    <input type="text" name="youtube" value="<?php echo $yt; ?>">
                    <span class="hiba"><?php echo $yt_hiba; ?></span>
                </div>
            </div>
            <div class="cimke2">
                <div <?php echo (!empty($dalszoveg_hiba)) ? 'Hiba történt!' : ''; ?>>
                    <label>Dal szövege</label> <br>
                    <textarea id="zene_hozzaad_textarea" name="dalszoveg"><?php echo $dalszoveg; ?></textarea>
                    <span class="hiba"><?php echo $dalszoveg_hiba; ?></span>
                </div>
            </div>

            <button class="gomb" type="submit" name="zene_hozzaad">Zene hozzáadása</button>
            <p class="vissza_link"><a href="zenek.php">Vissza a zenékhez</a></p>

        </form>
    </div>
<?php } else {
?>
    <script>
        location.href = "../index.php"
    </script>
<?php
}
include_once '../altalanos/lablec.php';
?>