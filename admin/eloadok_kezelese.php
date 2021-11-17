<?php
require_once '../altalanos/konfig.php';
include_once '../altalanos/fejlec.php';  //fejléc beszúrása
include_once '../altalanos/menu.php'; //navigációs sáv beszúrása
//if(isset($_SESSION['tipus'])){

?>
<div class="tartalom">
    <div class="focim">
        <h2>Előadók kezelése</h2>
    </div>

    <div class="focimsor eloado_kez">
        <div class="cimke1">Profilkép</div>
        <div class="cimke2">Előadó</div>
        <div class="cimke3">Debüt</div>
        <div class="cimke4">Kiadó</div>
        <div class="cimke5">Fanclub</div>
        <div class="cimke6"><i class='fas szerkeszto'>&#xf303;</i></div>
    </div>
    <?php
    if (isset($_SESSION['felhasznalonev']) && $_SESSION['tipus'] == 'Admin' || $_SESSION['tipus'] == 'Rendszergazda') {
        try {
            $utasitas = $adatbazisom->query("SELECT eloadok.*, kiadok.ID_kiado, kiadok.kiado_neve
                                        FROM eloadok
                                          INNER JOIN kiadok
                                            ON eloadok.kiado_ID = kiadok.ID_kiado
                                       ORDER BY eloado_neve");
            $eredmeny = $utasitas->fetchAll(PDO::FETCH_ASSOC);

            if (count($eredmeny) > 0) {

                foreach ($eredmeny as $adatsor) {
                    echo '<div class="sor eloado_kez">';
                    echo '<div class="cimke1">';
                    echo "<img class='kep' src='../" . $adatsor["profil_kep"] . "' alt='előadó profilképe'>";
                    echo "</div>";

                    echo '<div class="cimke2">';
                    echo $adatsor["eloado_neve"];
                    echo "</div>";

                    echo '<div class="cimke3">';
                    echo $adatsor["debut_ido"];
                    echo "</div>";

                    echo '<div class="cimke4">';
                    echo $adatsor["kiado_neve"];
                    echo "</div>";

                    echo '<div class="cimke5">';
                    echo $adatsor["fan_club"];
                    echo "</div>";

                    echo '<div class="cimke6">';
                    echo "<a href='../eloadok/eloado_szerkesztese.php?ID_eloado=" . $adatsor["ID_eloado"] . "'><i class='fas szerkeszto'>&#xf303;</i></a>";
                    echo "</div>";
                    echo '</div>';
                }
    ?>


        <?php
            }
        } catch (Exception $exc) {
            echo $exc->getMessage();
            header("location: ../altalanos/hiba.php");
            exit();
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

    include_once '../altalanos/lablec.php';

?>