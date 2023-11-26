function validateForm() {
    var a = document.forms["neueBuchungForm"]["betrag"].value;
    var b = document.forms["neueBuchungForm"]["selectKonto"].value;
    var c = document.forms["neueBuchungForm"]["selectKategorie"].value;
    var d = document.forms["neueBuchungForm"]["kommentar"].value;
    if(!validateBetrag(a)){
        return false;
    }
    if ((a == null || a == "") || (b == null || b == 0) || (c == null || c == 0) || (d == null || d == "")) {
        alert("Nicht alle Felder ausgefüllt.");
        return false;
    }
    return true;
}

function validateBetrag(betrag){
    if(!betrag.match(/^\d*([.,]{1}\d{1,2}){0,1}€?$/g)){
        alert("Ungültiger Betrag");
        return false;
    }
    return true;
}
function insertBuchung(einnahme) {
    if(validateForm()) {
        let date = document.getElementById("datePicker").value;
        let betrag = document.getElementById("betrag").value;
        let konto = document.getElementById("selectKonto").value;
        let kategorie = document.getElementById("selectKategorie").value;
        let kommentar = document.getElementById("kommentar").value;
        const xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if(this.status == 200){
                console.log(this.responseText);
                $("#neueBuchungForm").trigger("reset");
                location.reload();
            }
        };
        xhttp.open("POST", "assets/scripts/api", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        let e
        if(einnahme){
            e = "einnahme"
        } else {
            e = "ausgabe"
        }
        let data = "type="+e+"&date=" + date + "&betrag=" + betrag + "&kontoid=" + konto + "&kategorieid=" + kategorie + "&kommentar=" + kommentar;
        console.log(data);
        xhttp.send(data);

    } else {
        console.log("wat is schief jelaufen");
    }
}

function notImplemented(){
    alert("whoops! that hasn't been implemented yet ¯\\_(ツ)_/¯")
}
function submitFilter(einnahme) {
    let suche = $("#filterSuche")[0].value
    let order = $("#filterReihenfolge")[0].value
    if(suche!==""){
        notImplemented()
    }
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if(this.status == 200){
            console.log(this.responseText);
            location.reload();
        }
    };
    xhttp.open("POST", "assets/scripts/api", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    let e
    if(einnahme){
        e = 1
    } else {
        e = 0
    }
    let data = "type=setCookie&key=order"+e+"&value="+order;
    console.log(data);
    xhttp.send(data);
}

function setField(id, value){
    let object = $('#'+id)[0]
    //console.log(object)
    if(object.nodeName === "SELECT"){
        object.value = getIdFromSelect(object,value)
    } else {
        object.setAttribute('value', value)
    }
}

function setPlaceholder(id, value){
    let object = $('#'+id)[0];
    object.placeholder = value;
}

function getIdFromSelect(object,textToFind){
    // get value="x" from the textContent of a select input
    for (var i = 0; i < object.options.length; i++) {
        var option = object.options[i];
        if (option.textContent === textToFind) {
            var value = option.value;
            return value
        }
    }
}

function editEntry(id, einnahme){
    console.log(id);
    $('#editEntryModal').modal('show');
    $('#updateEntryForm').trigger("reset")
    let curDate = $('#'+id + " > td#datum")[0].textContent
    let curBetrag = $('#'+id + " > td#betrag")[0].textContent
    let curKonto = $('#'+id + " > td#konto")[0].textContent
    let curKategorie = $('#'+id + " > td#kategorie")[0].textContent
    let curKommentar = $('#'+id + " > td#kommentar")[0].textContent
    $('#submitEditBuchung')[0].onclick = function(){submitEditBuchung(id, einnahme)}
    // set fields tu current values of entry
    setField("editFormDate", curDate);
    setField("editFormBetrag", curBetrag);
    setField("editFormKonto", curKonto);
    setField("editFormKategorie", curKategorie);
    setField("editFormKommentar", curKommentar);
}

function submitEditBuchung(id, einnahme){
    console.log("SubmitEdit")
    let curDate = $('#editFormDate')[0].value
    let curBetrag = $('#editFormBetrag')[0].value
    let curKonto = $('#editFormKonto')[0].value
    let curKategorie = $('#editFormKategorie')[0].value
    let curKommentar = $('#editFormKommentar')[0].value

    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if(this.status == 200){
            console.log(this.responseText);
            $('#editEntryModal').modal('hide')
            location.reload();
        }
    };
    xhttp.open("POST", "assets/scripts/api", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    let data = "type=editBuchung&einnahme="+einnahme+"&id="+id+"&date="+curDate+"&betrag="+curBetrag+"&kontoid="+curKonto+"&kategorieid="+curKategorie+"&kommentar="+curKommentar;
    console.log(data);
    xhttp.send(data);
}

async function addUebertrag(){
    let date = $('#uebertragFormDate')[0].value
    let betrag = $('#uebertragFormBetrag')[0].value
    let source = $('#uebertragFormSource')[0].value
    let destination = $('#uebertragFormDestination')[0].value
    if((date==null || date === "") ||
        (betrag==null || betrag === "") ||
        (source == null || source <= 0) ||
        (destination == null || destination <= 0)){
        alert("Nicht alle Felder korrekt ausgefüllt")
        return false
    }
    if(!validateBetrag(betrag)){
        return false
    }
    await fetch('assets/scripts/api.php', {
        method: 'POST',
        body: "type=addUebertrag&date="+date+"&betrag="+betrag+"&source="+source+"&destination="+destination,
        headers: {
            'Content-type': 'application/x-www-form-urlencoded',
        }
    }).then(async function(response) {
        return await response.text();
    })
    // reset form
    $('#uebertragForm').trigger("reset")
    //reload page
    location.reload()
}

function addKategorie(einnahme){
    $('#addKategorieModal').modal('show')
    let name = document.getElementById("addKategorieName").value;
    console.log(name)
    if(name != null || name != ""){
        const xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if(this.status == 200){
                console.log(this.responseText);
                $('#addKategorieModal').modal('hide');
                $('#addKategorieForm').trigger("reset");
                location.reload();
            }
        };
        xhttp.open("POST", "assets/scripts/api", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        let e
        if(einnahme){
            e = 1
        } else {
            e = 0
        }
        let data = "type=addKategorie&name="+name+"&einnahme="+e;
        console.log(data);
        xhttp.send(data);
    }
}

function prepareKategorieModal(einnahme){
    $('#addKategorieModal').modal('show')
    console.log(einnahme)
    if(einnahme){
        $('#addKategorieModalTitle')[0].innerText = "Neue Kategorie für Einnahmen"
        $('#addKategorieModalSubmit')[0].onclick = function() { addKategorie(true)}
    } else {
        $('#addKategorieModalTitle')[0].innerText = "Neue Kategorie für Ausgaben"
        $('#addKategorieModalSubmit')[0].onclick = function(einnahme) { addKategorie(false)}
    }


}
function addKonto(){
    let name = document.getElementById("kontoName").value;
    let startbetrag = document.getElementById("startBetrag").value;
    if(name != "" && startbetrag.match(/^\d*([.,]{1}\d{1,2}){0,1}€?$/g)){
        const xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if(this.status == 200){
                console.log(this.responseText);
                $('#newKonto').modal('hide');
                $('#kontoForm').trigger("reset");
                location.reload();
            }
        };
        xhttp.open("POST", "assets/scripts/api", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        let data = "type=addKonto&name="+name+"&startbetrag="+startbetrag;
        console.log(data);
        xhttp.send(data);
    }
}