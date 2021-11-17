<?php
require_once '../altalanos/konfig.php';
include_once '../altalanos/fejlec.php'; //fejléc beszúrása
//    include_once '../../altalanos/menu.php'; //navigációs sáv beszúrása
//if(isset($_SESSION['tipus'])){
if (isset($_SESSION['felhasznalonev']) && $_SESSION['tipus'] == 'Rendszergazda') {
    try {
        $utasitas = $adatbazisom->query("SELECT * FROM felhasznalok");
        $eredmeny = $utasitas->fetchAll(PDO::FETCH_ASSOC);

        if (count($eredmeny) > 0) {
?>
            <div class="tartalom">
                <div class="focim">
                    <h2>Felhasználók kezelése</h2>
                </div>

                <div class="focimsor us_kez">
                    <div class="cimke1">Felahsználónév</div>
                    <div class="cimke2">Típus</div>
                    <div class="cimke3">Aktivált fiók</div>
                    <div class="cimke4">Módosítás</div>
                </div>
                <?php
                foreach ($eredmeny as $adatsor) {
                    echo '<div class="sor us_kez">';
                    echo "<div class='cimke1'>";
                    echo $adatsor["felhasznalonev"];
                    echo "</div>";

                    echo "<div class='cimke2'>";
                    echo $adatsor["tipus"];
                    echo "</div>";

                    echo "<div class='cimke3'>";
                    if ($adatsor["aktiv"]) {
                        echo "IGEN";
                    } else {
                        echo "NEM";
                    }
                    echo "</div>";

                    echo "<div class='cimke4'>";
                    echo "<a href='modosit.php?ID_felhasznalo=" . $adatsor["ID_felhasznalo"] . "'>Módosít</a>";
                    echo "</div>";
                    echo "</div>";
                }
                ?>
            </div>
    <?php
        }
    } catch (Exception $exc) {
        echo $exc->getMessage();
        header("location: ../altalanos/hiba.php");
        exit();
    }
} else {
    ?>
    <script>
        location.href = "../index.php"
    </script>
<?php
}
include_once '../altalanos/lablec.php';
?>