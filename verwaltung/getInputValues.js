// HTML input example: <input type="text" id="txt_1" onkeyup='saveValue(this);'/>
// set old value to inputfield: document.getElementById("txt_1").value = getSavedValue("txt_1");
// Here you can add more inputs to set value. if it's saved

//Save the value function - save it to localStorage as (ID, VALUE)
function saveValue (e) {
    var id = e.id;  // get the sender's id to save it . 
    var val = e.value; // get the value. 
    localStorage.setItem(id, val);// Every time user writing something, the localStorage's value will override . 
}

//get the saved value function - return the value of "v" from localStorage. 
function getSavedValue (a = []) {
    for (i=0; i < a.length; i++) {
        v = a[i];
        document.getElementById(v).value = (localStorage.getItem(v)) ? localStorage.getItem(v) : document.getElementById(v).value;
    }
}

function delSavedValue(a = []) {
    for (i=0; i < a.length; i++) {
        localStorage.removeItem(a[i]);
    }
}
function clearAllSavedValues() {
    localStorage.clear();
}