<?php
require_once'../altalanos/konfig.php';
include_once '../altalanos/fejlec.php';

if(isset($_SESSION['felhasznalonev'])){ 
?>
    <script>location.href="../kezdolap.php"</script>
<?php
} // ha van felhasználó bejelentkezés a kezdőlapra irányít

$hiba=$van_uzenet =false;
$hiba_uzenet='';
$siker_uzenet='';

if(isset($_POST['elfelejtett_jelszo'])){
    $mail = $_POST['email'];
     
    $utasitas=$adatbazisom->prepare('SELECT * FROM felhasznalok WHERE email=?');
    $utasitas->bindParam(1,$mail);
    
    $utasitas->execute();
    $adat=$utasitas->fetch();
    
    $email=$adat['email'];
    $hash=$adat['hash'];
    $felhasznalo=$adat['felhasznalonev'];
    
    if($mail==$email){
        $kinek = $mail;
        $targy = "Új jelszó igénylése";
        $uzenet = '

            Az új jelszót a következő felhasználónévhez
            _________________________________

            Felhasználónév: '.$felhasznalo. ' 
            _________________________________

            a következő linkre kattintva tudja beállítani:
            http://localhost/forditasokk/felhasznalo/uj_jelszo.php?email='.$mail.'&hash='.$hash;
        
        $uzenet= str_replace('Ű','Û',$uzenet);
        $uzenet= str_replace('Ő','Õ',$uzenet);
        $uzenet= str_replace('ű','û',$uzenet);
        $uzenet= str_replace('ő','õ',$uzenet);
        $uzenet= utf8_decode($uzenet);
        
        $fejlec = 'From: admin@localhost'."\r\n"; //alapértelmezetten minden gépen rajta van, ha a weboldal felkerülne a netre módosítani kell az adott oldal e-mailjére
                
        mail($kinek, $targy, $uzenet, $fejlec);
        $siker_uzenet='';
        $van_uzenet =true;
        $siker_uzenet= "A megadott e-mail címre küldtük a linket az új jelszó beállításához.";

    }else{
        $hiba_uzenet='';
        $hiba=$van_uzenet=true;
        $hiba_uzenet = "Ehhez e-mail címhez nem található regisztráció.";
    }
}
?>
<div class="tartalom">
    <?php if($van_uzenet ==true){?>
    <div class="uzenet_van">
        <?php
            if($hiba){
                echo "<div class='hiba_uzi'>".$hiba_uzenet."</div>";
                $hiba_uzenet='';
                $van_uzenet =false;
            }
            if(!$hiba){
                echo "<div class='siker_uzi'>".$siker_uzenet."</div>";
                $siker_uzenet='';
                $van_uzenet =false;
            }
        ?>
    </div>
        <?php
        }
    ?>
<div class="focim">
    <h2>Elfelejtett jelszó</h2>
</div>
    <form method="POST" class="minden_form">
        <div>
            <label>E-mail cím</label> <br>
            <input type="email" name="email" required />
        </div>
        
        <button class="gomb" type="submit" name="elfelejtett_jelszo">Elküld</button>
        <div class="linkek">
            <div class="link bal"><a href="bejelentkezes.php">Bejelentkezés</a></div>
            <div class="link jobb"><a href="regisztracio.php">Regisztráció</a></div>
        </div>
    </form>
</div>
<?php
    include_once '../altalanos/lablec.php';
?>