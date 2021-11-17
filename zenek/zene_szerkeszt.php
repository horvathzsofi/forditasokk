<?php
require_once '../altalanos/konfig.php';  // konfig.php tartalmának beszúrása
include_once '../altalanos/fejlec.php'; //fejléc beszúrása

if (isset($_SESSION['tipus']) == 'Rendszergazda' || isset($_SESSION['tipus']) == 'Admin' || isset($_SESSION['tipus']) == 'Felhasznalo') {
    $zene_cime = $dalszoveg = $youtube_link = $id = "";

    $zene_cime_hiba = "";
    if (isset($_GET['ID_zenek']) && !empty(trim($_GET['ID_zenek']))) {
        $id = htmlspecialchars(trim($_GET['ID_zenek']));

        $utasitas = $adatbazisom->prepare("SELECT zeneszamok.*
                                  FROM zeneszamok
                                  WHERE ID_zenek = :ID_zenek");
        $utasitas->bindParam(':ID_zenek', $id);
        $utasitas->execute();
        $eredmeny = $utasitas->fetchALL(PDO::FETCH_ASSOC);
        if (count($eredmeny) > 0) {
            foreach ($eredmeny as $adatsor) {
                $zene_cime = $adatsor['zene_cim'];
                $dalszoveg = $adatsor['dalszoveg'];
                $youtube_link = $adatsor['youtube_link'];
                $id = $adatsor['ID_zenek'];
            }
        } else {
?>
            <script>
                location.href = "../altalanos/nem_talalhato.php"
            </script>
    <?php
        }
    } else {
        header("location: ../altalanos/hiba.php");
    }


    if (isset($_POST['zene_frissit'])) {
        //bevitt értékek validálása
        $bevitt_zene_cime = htmlspecialchars(trim($_POST["zene_cime"]));
        if (empty($bevitt_zene_cime)) {
            $zene_cime_hiba = "A zene címét meg kell adni!";
        } else {
            $zene_cime = $bevitt_zene_cime;
        }

        $dalszoveg = htmlspecialchars($_POST['dalszoveg']);
        $youtube_link = htmlspecialchars($_POST['youtube_link']);

        if (isset($_POST["zene_cime"])) {
            $utasitas = $adatbazisom->prepare("UPDATE zeneszamok SET zene_cim=:zene_cime, dalszoveg=:dalszoveg, youtube_link=:youtube_link WHERE ID_zenek=:id;");
            $utasitas->bindParam(':zene_cime', $zene_cime);
            $utasitas->bindParam(':dalszoveg', $dalszoveg);
            $utasitas->bindParam(':youtube_link', $youtube_link);
            $utasitas->bindParam(':id', $id);


            if ($utasitas->execute()) {
                header("location: zenek.php");
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
        <form method="POST" class="zene_szerkeszt minden_form">
            <div>
                <label> Zene címe</label><br>
                <input type="text" name="zene_cime" value="<?php echo $zene_cime; ?>">
            </div>
            <div>
                <label> Dalszöveg </label><br>
                <textarea name="dalszoveg"><?php echo $dalszoveg; ?></textarea>
            </div>
            <div>
                <label>Hivatalos YouTube video</label><br>
                <input type="text" name="youtube_link" value="<?php echo $youtube_link; ?>" pattern="^((?:https?:)?\/\/)?((?:www|m)\.)?((?:youtube\.com|youtu.be))(\/(?:[\w\-]+\?v=|embed\/|v\/)?)([\w\-]+)(\S+)?$">
            </div>
            <input type="hidden" name="id" value="<?php echo $id; ?>" />


            <button class="gomb" type="submit" name="zene_frissit">Adatok frissítése</button>
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