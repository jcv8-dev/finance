function validateForm() {
    let betrag = document.forms["neueBuchungForm"]["betrag"].value;
    let konto = document.forms["neueBuchungForm"]["selectKonto"].value;
    let kategorie = document.forms["neueBuchungForm"]["selectKategorie"].value;
    if(!validateBetrag(betrag)){
        return false;
    }
    if ((betrag == null || betrag === "") || (konto == null || konto === 0) || (kategorie == null || kategorie === 0)) {
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
async function insertBuchung(einnahme) {
    if(validateForm()) {
        let date = document.getElementById("datePicker").value;
        let betrag = document.getElementById("betrag").value;
        let konto = document.getElementById("selectKonto").value;
        let kategorie = document.getElementById("selectKategorie").value;
        let kommentar = document.getElementById("kommentar").value;

        if(einnahme){
            e = "einnahme"
        } else {
            e = "ausgabe"
        }

        await postRequest("assets/scripts/writeDB",{
            type: e,
            date: date,
            betrag: betrag,
            kontoid: konto,
            kategorieid: kategorie,
            kommentar: kommentar
        })

        $("#neueBuchungForm").trigger("reset");
        location.reload();
    } else {
        console.log("wat is schief jelaufen");
    }
}

function notImplemented(){
    alert("whoops! that hasn't been implemented yet ¯\\_(ツ)_/¯")
}
async function submitFilter(einnahme) {
    let suche = $("#filterSuche")[0].value
    let order = $("#filterReihenfolge")[0].value
    if(suche!==""){
        notImplemented()
    }
    let e
    if(einnahme){
        e = 1
    } else {
        e = 0
    }
    await postRequest("assets/scripts/writeDB", {
        type: "setCookie",
        key: "order"+e,
        value: order
    })
    location.reload();

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
            return option.value
        }
    }
}

function editEntry(id, type){
    if(type === "einnahme" || type === "ausgabe"){
        editBuchung(id, type)
    } else if (type === "uebertrag"){
        editUebertrag(id)
    }
}

function editUebertrag(id){
    $('#editEntryModal').modal('show');
    $('#updateEntryForm').trigger("reset")
    let curDate = $('#'+id + " > td#datum")[0].textContent.trim()
    let curBetrag = $('#'+id + " > td#betrag")[0].textContent
    let curQuelle = $('#'+id + " > td#quelle")[0].textContent
    let curZiel = $('#'+id + " > td#ziel")[0].textContent
    // set fields tu current values of entry
    setField("editFormDate", curDate);
    setField("editFormBetrag", curBetrag);
    setField("editFormQuelle", curQuelle);
    setField("editFormZiel", curZiel);
    $('#submitEditUebertrag')[0].onclick = function(){submitEditUebertrag(id)}
}

function editBuchung(id, type) {
    $('#editEntryModal').modal('show');
    $('#updateEntryForm').trigger("reset")
    let curDate = $('#'+id + " > td#datum")[0].textContent.trim()
    let curBetrag = $('#'+id + " > td#betrag")[0].textContent
    let curKonto = $('#'+id + " > td#konto")[0].textContent
    let curKategorie = $('#'+id + " > td#kategorie")[0].textContent
    let curKommentar = $('#'+id + " > td#kommentar")[0].textContent
    // set fields tu current values of entry
    setField("editFormDate", curDate);
    setField("editFormBetrag", curBetrag);
    setField("editFormKonto", curKonto);
    setField("editFormKategorie", curKategorie);
    setField("editFormKommentar", curKommentar);
    $('#submitEditBuchung')[0].onclick = function(){submitEditBuchung(id, type)}
}

async function submitEditBuchung(id, einnahme){
    console.log("SubmitEdit")
    let curDate = $('#editFormDate')[0].value
    let curBetrag = $('#editFormBetrag')[0].value
    let curKonto = $('#editFormKonto')[0].value
    let curKategorie = $('#editFormKategorie')[0].value
    let curKommentar = $('#editFormKommentar')[0].value
    await postRequest("assets/scripts/writeDB",{
        type: "editBuchung",
        einnahme: einnahme,
        id: id,
        date: curDate,
        betrag: curBetrag,
        kontoid: curKonto,
        kategorieid: curKategorie,
        kommentar: curKommentar
    })
    $('#editEntryModal').modal('hide')
    location.reload();
}
async function submitEditUebertrag(id){
    let curDate = $('#editFormDate')[0].value.trim()
    let curBetrag = $('#editFormBetrag')[0].value
    let curQuelle = $('#editFormQuelle')[0].value
    let curZiel = $('#editFormZiel')[0].value
    await postRequest("assets/scripts/writeDB",{
        type: "editUebertrag",
        id: id,
        date: curDate,
        betrag: curBetrag,
        quelle: curQuelle,
        ziel: curZiel,
    })
    $('#editEntryModal').modal('hide')
    location.reload();
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

    await postRequest("assets/scripts/writeDB", {
        type: 'addUebertrag',
        date: date,
        betrag: betrag,
        source: source,
        destination: destination,
    })
    // reset form
    $('#uebertragForm').trigger("reset")
    //reload page
    location.reload()
}

async function addKategorie(einnahme){
    let modal = $('#addKategorieModal')
    modal.modal('show')
    let name = document.getElementById("addKategorieName").value;
    if(name != null || name !== ""){
        let e
        if(einnahme){
            e = 1
        } else {
            e = 0
        }
        await postRequest("assets/scripts/writeDB",{
            type: "addKategorie",
            name: name,
            einnahme: e,
        })
        modal.modal('hide');
        $('#addKategorieForm').trigger("reset");
        location.reload();
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
        $('#addKategorieModalSubmit')[0].onclick = function() { addKategorie(false)}
    }
}
async function addKonto(){
    let name = document.getElementById("kontoName").value;
    let startbetrag = document.getElementById("startBetrag").value;
    if(name !== "" && startbetrag.match(/^\d*([.,]{1}\d{1,2}){0,1}€?$/g)){
        await postRequest("assets/scripts/writeDB", {
            type: "addKonto",
            name: name,
            startbetrag: startbetrag
        })
        $('#newKonto').modal('hide');
        $('#kontoForm').trigger("reset");
        location.reload();
    }
}

function encode(val){
    return encodeURIComponent(val)
}
async function postRequest(url, data){
    let formData = ""
    for (const key in data) {
        formData+=key+"="+encode(data[key])+"&";
    }
    try {
        await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: formData
        }).then(async function(response) {
            return await response.text();
        }).then(async function(text){
            console.log(text)
        })
    } catch (error) {
        console.error('Error:', error);
        alert("Netzwerkfehler!")
    }
}

function colorizeTableByRow(id, aufsteigend) {
    $("#"+id+" tr").each(function() {
        let columnValues = [];
        $(this).find("td").each(function() {
            let valueWithSymbol = $(this).text();
            let numericValue = parseFloat(valueWithSymbol.replace(/[^0-9.-]+/g,""))/100;
            columnValues.push(numericValue);
        });
        let min = Math.min(...columnValues);
        let max = Math.max(...columnValues);
        $(this).find("td").each(function() {
            let valueWithSymbol = $(this).text();
            let numericValue = parseFloat(valueWithSymbol.replace(/[^0-9.-]+/g,""))/100;
            let relativeValue = (numericValue - min) / (max - min);
            let red = Math.floor(255 * (1 - relativeValue)* 1.9 + 80);
            let green = Math.floor(255 * relativeValue * 1.5 + 10);
            if(!aufsteigend){
                let temp = red
                red = green
                green = temp
            }
            if(numericValue !== 0){
                $(this).attr("style", `color: rgba(${red}, ${green}, 20, 1) !important; text-shadow:
    0.5px 0.5px 2px var(--text-color),
    -0.5px 0.5px 2px var(--text-color),
    -0.5px -0.5px 2px var(--text-color),
    0.5px -0.5px 2px var(--text-color);`);
            }
            //TODO fix colors
        });
    });
}
function colorizeTableByColumn(id, aufsteigend) {
    for (let i = 0; i <= 12; i++) {
        let columnCells = []
        let columnValues = []
        $(id).find("tbody tr:not(:last)").each(function (){
                // i+1 um kategoriebezeichnung zu skippen
                columnCells.push($(this)[0].cells[i+1])
                let text = $(this)[0].cells[i+1].textContent
                columnValues.push(parseFloat(text.replace(/[^0-9.-]+/g,""))/100)
        })
        let min = Math.min(...columnValues);
        let max = Math.max(...columnValues);

        for (let j = 0; j < columnCells.length; j++) {
            let relativeValue = (columnValues[j] - min) / (max - min);
            let red = Math.floor(255 * (1 - relativeValue)* 1.9 + 80);
            let green = Math.floor(255 * relativeValue * 1.5 + 10);
            if(!aufsteigend){
                let temp = red
                red = green
                green = temp
            }
            if(columnValues[j] !== 0){
                $(columnCells[j]).attr("style",`color: rgba(${red}, ${green}, 20, 1) !important; 
                text-shadow: 0.5px 0.5px 2px var(--text-color), 
                -0.5px 0.5px 2px var(--text-color), 
                -0.5px -0.5px 2px var(--text-color),
                0.5px -0.5px 2px var(--text-color);`)
            }
        }
        columnCells = []
        columnValues = []
    }
}

function openInBackground(url){
    open(url);
    focus();
}