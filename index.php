<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

<title>remove-text-formation</title>
<textarea id="txt"  ></textarea>
<br>
<button onclick="replaceAccents()">نفذ</button>
<hr>

<script>
function replaceAccents()
{
    var elem;
    var text = (elem = document.getElementById("txt")).value;
    elem.value = text.replace(new RegExp(String.fromCharCode(1617, 124, 1614, 124, 1611, 124, 1615, 124, 1612, 124, 1616, 124, 1613, 124, 1618), "g"), "");
}    
</script>
