function hambi_gomb(x) {
    if (x.matches) {
        document.getElementById("menu_gomb").style.display = "none";
        document.getElementById("felhasznalonev").disabled = true;


    } else {
        document.getElementById("menu_gomb").style.display = "block";
        document.getElementById("felhasznalonev").disabled = false;
        if (legordulojon == false) {
            document.getElementById("legordulo_lista1").style.display = "none";
        }
    }
}

var x = window.matchMedia("(min-width: 1000px)");
hambi_gomb(x);
x.addListener(hambi_gomb);
var legordulojon = false;
var legorduloBurger = false;

function legordules() {
    if(legorduloBurger == true){
        document.getElementById("legordulo_lista").style.display = "none";
        legorduloBurger = false;
    }
    else{
        document.getElementById("legordulo_lista").style.display = "block";
        legorduloBurger = true;
    }
}
function legordules1() {
    if(legordulojon == true){
        document.getElementById("legordulo_lista1").style.display = "none";
        document.getElementById("legordulo_lista1").style.position = "absolute";
        legordulojon = false;
    }
    else{
        document.getElementById("legordulo_lista1").style.display = "block";
        document.getElementById("legordulo_lista1").style.position = "absolute";
        legordulojon = true;
    }
}



