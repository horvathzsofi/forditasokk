<?php
require_once'../altalanos/konfig.php';
include_once '../altalanos/fejlec.php';

if(isset($_SESSION['felhasznalonev'])){ 
?>
    <script>location.href="../kezdolap.php"</script>
<?php
} // ha van felhasznááló bejelentkezés a kezdőlapra irányít


$foglalt_e = $foglalt_fn='';
$siker_uzenet='';
$van_uzenet=false;

if(isset($_POST['regisztral']))
{
    $felhasznalo = $_POST['felhasznalonev'];
    $mail = $_POST['email'];
    

    try{
        $utasitas=$adatbazisom->prepare("SELECT felhasznalonev, email FROM felhasznalok
                                        WHERE UPPER(felhasznalonev)=UPPER(:bevitt_felhasznalonev)
                                        OR UPPER(email)=UPPER(:bevitt_email);");
       
        $utasitas->bindParam(':bevitt_felhasznalonev', $felhasznalo);
        $utasitas->bindParam(':bevitt_email', $mail);
        
        $utasitas->execute();
        
        $eredmeny=$utasitas->fetchAll(PDO::FETCH_ASSOC);
        
        if(count($eredmeny)>0)
        {
            foreach ($eredmeny as $adat) {
                if($felhasznalo==$adat["felhasznalonev"])
                {
                   $foglalt_fn = "A(z) ".$felhasznalo." felhasználónév már foglalt.";
                }
                if($mail==$adat["email"])
                {
                   $foglalt_e = "A(z) ".$mail." e-mail címmel már regisztráltak.";
                }
            }
        }
        else{
            $jsz = password_hash($_POST['jelszo'], PASSWORD_DEFAULT);
            $tipus = 3; //az enum 3. elemét küldi tovább alapértelmezettnek
            $hash = md5(rand(0, 1000));
            $utasitas = $adatbazisom->prepare("INSERT INTO felhasznalok VALUES ('',?,?,?,?,'0',?)");

            $utasitas->bindParam(1, $felhasznalo);
            $utasitas->bindParam(2, $mail);
            $utasitas->bindParam(3, $jsz);
            $utasitas->bindParam(4, $tipus);
            $utasitas->bindParam(5, $hash);

            if($utasitas->execute()){
                $kinek = $mail;
                $targy = "Regisztrációt hitelesítő bejelentkezés";
                $uzenet = 'Üdvözlünk a FordításokK oldalon!
                    Már csak egy lépés választ el attól, hogy szabadon szerkeszthesd az oldal tartalmát, és gyarapítsd az információs készletünket.

                    Aktiváld fiókod az alábbi adatokkal
                    _________________________________

                    Felhasználónév: '.$felhasznalo. ' 
                    Jelszó: '.$_POST['jelszo'].'
                    _________________________________

                    a következő linkre kattintva: http://localhost/forditasokk/felhasznalo/bejelentkezes.php?email='.$mail.'&hash='.$hash;
                
                $uzenet= str_replace('Ű','Û',$uzenet);
                $uzenet= str_replace('Ő','Õ',$uzenet);
                $uzenet= str_replace('ű','û',$uzenet);
                $uzenet= str_replace('ő','õ',$uzenet);
                $uzenet= utf8_decode($uzenet);
                
                $fejlec = 'From: admin@localhost'."\r\n"; //alapértelmezetten minden gépen rajta van, ha a weboldal felkerülne a netre módosítani kell az adott oldal e-mailjére
                
                
                mail($kinek, $targy, $uzenet, $fejlec);
                
                $van_uzenet = true;
                $siker_uzenet= "Az aktiváláshoz szükséges azonosító linket elküldtük a megadott e-mail címre. <br>Az aktiváláshoz kattints rá, vagy másold a böngésző címsorába.";
            }
        }
    }catch (Exception $exc) {
        echo $exc->getMessage();
    }
}
?>
    <div class="tartalom">
        <?php if($van_uzenet==true){?>
    <div class="uzenet_van">
        <?php
                echo "<div class='siker_uzi'>".$siker_uzenet."</div>";
                $siker_uzenet='';
                $van_uzenet=false;
        ?>
    </div>
        <?php
        }
    ?>
        
    <div class="focim">
         <h2>Regisztráció</h2>
    </div>
   
    <form method="POST" class="minden_form">
        <div>
            <label>Felhasználónév</label> <br>
            <input type="text" name="felhasznalonev" required /><br>
            <span class="hiba"><?php echo $foglalt_fn;?></span>
        </div>
        <div>
            <label>E-mail cím</label> <br>
            <input type="email" name="email" required /><br>
            <span class="hiba"><?php echo $foglalt_e;?></span>
        </div>
        
        <div>
            <label>Jelszó</label> <br>
            <input type="password" name="jelszo" required />
        </div>
                       
        <button class="gomb" type="submit" name="regisztral">Regisztráció</button>
        
        <div class="linkek">
            <div class="link bal"></div>
            <div class="link jobb"><a href="bejelentkezes.php">Bejelentkezés</a></div>
        </div>
    </form>

</div>
<?php
include_once '../altalanos/lablec.php';
?>    

