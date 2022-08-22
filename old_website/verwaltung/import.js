if (document.getElementById('userfile')) {
    document.getElementById('userfile').onchange(
            'change',
            function () {
                var fr = new FileReader();
                fr.onload = function () {
                    document.getElementById('filepreview').textContent = this.result;
                };
                fr.readAsText(this.files[0]);
            }
        );
}