function keypress()
{
    var dir = (document.getElementById("textbox").value) ? document.getElementById("textbox").value : '.';
    var data = {"directory" : dir};
        var ajax = new XMLHttpRequest();
        ajax.open("POST", "fileupload.php", true);
        ajax.onreadystatechange = function()
        {
            if (this.readyState == 4 && this.status == 200) {
                const response = JSON.parse(this.responseText);
                if (response.response == "directory")
                {
                    document.getElementById("textbox").style.color = "green";
                    if (response.text == false)
                    {
                        document.getElementById("directory").innerHTML = "";
                    }
                    else 
                    {
                        document.getElementById("directory").innerHTML = response.text;
                    }
                }
                else if (response.response == "file")
                {
                    document.getElementById("textbox").style.color = "orange";
                }
                else if (response.response == "false")
                {
                    document.getElementById("textbox").style.color = "red";
                }
            }
        };
        ajax.setRequestHeader("Content-Type", "application/json");
        ajax.send(JSON.stringify(data));
}


function fileuploadsubmit(event) {
    event.preventDefault();
    var fOutput = document.getElementById("response"),
    oData = new FormData(document.getElementById("fileuploadform"));
    
    oData.append("username", "This is some extra data");
    
    var fReq = new XMLHttpRequest();
    fReq.open("POST", "upload_request.php", true);
    fReq.onload = function(event) {
        if (fReq.status == 200 && fReq.readyState == 4) {
            if (fReq.responseText) {
                const rt = JSON.parse(fReq.responseText);
                document.getElementById("directory").style.display = "none";
                fOutput.innerHTML = "Response: "+rt.success+"<br>Filename: "+rt.info.name+"<br>Size: "+rt.info.size/1000+"kb<br>";
                document.getElementById("response").style.display = "block";
            }
        } else {
        fOutput.innerHTML = "Error " + fReq.status + " occurred when trying to upload your file.<br \/>";
        }
        document.getElementById('userfile').value = '';
    };

    fReq.send(oData);
}