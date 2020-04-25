<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
#overlay {
  position: fixed;
  display: none;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0,0,0,0.5);
  z-index: 2;
  cursor: pointer;
}
</style>
</head>
<body>

<div id="overlay"></div>

<div style="padding:20px">
  <h2>Overlay</h2>
  <p>Add an overlay effect to the page content (100% width and height with a black background color with 50% opacity).</p>
  <!--<button onclick="on()">Turn on overlay effect</button>-->
        <?= $this->Html->link(
            'RUN JOB !',
            ['controller' => 'profiles', 'action' => 'loadoverlay'], 
            [ 'escape' => false, "onclick" => "on()"]) 
        ?>
</div>

<script>
function on() {
  document.getElementById("overlay").style.display = "block";
}

function off() {
  document.getElementById("overlay").style.display = "none";
}
</script>

</body>
