<?php
require_once '../altalanos/konfig.php';
include_once '../altalanos/fejlec.php';
if (isset($_SESSION['felhasznalonev']) && isset($_SESSION['id_felhasznalo'])) {
  $id_felhasznalo = $_SESSION['id_felhasznalo'];
?>
  <div class="tartalom">
    <div class="focim">
      <h2>Fordításaim</h2>
    </div>
    <?php

    $forditas_lekerdezes = "SELECT
                        forditasok.*,
                        felhasznalok.felhasznalonev,
                        felhasznalok.ID_felhasznalo,
                        zeneszamok.ID_zenek,
                        zeneszamok.zene_cim,
                        zeneszamok.dalszoveg,
                        eloadok.eloado_neve,
                        albumok.borito,
                        albumok.album_cime
                        FROM kapcsolo_zene_album_eloado
                        INNER JOIN zeneszamok
                          ON kapcsolo_zene_album_eloado.zene_ID = zeneszamok.ID_zenek 
                        INNER JOIN eloadok
                          ON kapcsolo_zene_album_eloado.eloado_ID = eloadok.ID_eloado
                        INNER JOIN albumok
                          ON kapcsolo_zene_album_eloado.album_ID = albumok.ID_album
                        LEFT JOIN forditasok
                          ON forditasok.zene_ID = zeneszamok.ID_zenek
                        LEFT JOIN felhasznalok
                          ON forditasok.felhasznalo_ID = felhasznalok.ID_felhasznalo
                        WHERE felhasznalo_ID = " . $id_felhasznalo;
    $lekerdezes = $adatbazisom->query($forditas_lekerdezes);
    $eredmeny = $lekerdezes->fetchAll(PDO::FETCH_ASSOC);
    if (count($eredmeny) > 0) {
    ?>
      <div class='kartyak'>
        <?php
        foreach ($eredmeny as $adatsor) {
        ?>
          <div class="uj_kartya">
            <?php echo "<img  class='kep' src='../" . $adatsor['borito'] . "'>"; ?>
            <div class="kartya_cim"><?php echo $adatsor['zene_cim'] ?></div>
            <div class="kartya_alcim"><?php echo $adatsor['eloado_neve']; ?></div>
            <div class="lehetoseg">
              <?php
              echo "<a href='forditasom_szerkesztese.php?ID_zenek=" . $adatsor['ID_zenek'] . "'>Szerkeszt</a>";
              echo "|";
              echo "<a href=''>Töröl</a>";
              ?>
            </div>
          </div>
        <?php
        }
        ?>
      </div>
    <?php
    } else {
      echo "<p class='focim'>Még nem adtál hozzá fordítást!</p>";
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