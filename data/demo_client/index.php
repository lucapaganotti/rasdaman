<?php 
$HOST = explode(':',$_SERVER['HTTP_HOST'])[0];

//echo $HOST;

?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="initial-scale=1,user-scalable=no,maximum-scale=1,width=device-width">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <link rel="stylesheet" href="css/leaflet.css">
        <link rel="stylesheet" href="css/qgis2web.css">
        <style>
        #map {
            width: 909px;
            height: 592px;
        }
        </style>

        <link href="./fontawesome.com/releases/v.5.0.6/css/all.css" rel="stylesheet">



        <link rel="stylesheet" href="./bootstrap/dist/css/bootstrap.min.css">
        <script src="./bootstrap/dist/js/bootstrap.min.js"></script>


        <!--script src="https://code.jquery.com/jquery-3.3.1.min.js"></script-->
        <!--script src="https://code.jquery.com/jquery-1.9.1.min.js"></script-->
        <script src="./jquery-1.9.1.min.js"></script>

        
        <link rel="stylesheet" href="./bootstrap-datepicker/dist/css/bootstrap-datepicker.css">
        <script src="./bootstrap-datepicker/dist/js/bootstrap-datepicker.js"></script>



        <title></title>
    </head>
    <body>
        <h1> Demo client di visualizzazione web dei WMS degli indici AIB (Anti Incendio Boschivo)</h1>
        <h3>Versione RASDAMAN. Esiste anche un altra versione basata su mapserver, presente sui  <a href="http://demo.gter.it/demo_arpa2/">server</a> del fornitore Gter srl</h1>
        <h3> Host: - <?php echo $HOST ?> - Dati da dicembre 2018  </h3>
       <form class="form-horizontal" role="form" method="post" action="index.php">
 


<div class="form-group row">
                <label for="indice" class="col-sm-1 col-form-label">Indice da visualizzare in mappa</label>
           		<div class="col-sm-3">
                    <select class="form-control" id="indice" name="indice">
                      <option>fwi</option>
                      <option>bui</option>
                      <option>dc</option>
                      <option>dmc</option>
                      <option>ffmc</option>
                      <option>isi</option>
                    </select>
                </div>
            </div>

<div class="form-group row">
  <label for="data" class="col-sm-1 col-form-label" >Data</label>
  <div class="col-sm-3">
    <input class="form-control"  id="data" name="data" type="date" value="<?php echo date('Y-m-d', strtotime("-1 days")); ?>">
  </div>
</div>




        <button type="submit" name="submit" class="btn btn-primary">Carica</button>
        </form>

<?php

$titolo= "fwi " .date('d/m/Y', strtotime("-1 days"));
$nome= "fwi_" .date('Ymd', strtotime("-1 days"));
$indice="fwi";



if (isset($_POST["submit"])) {
#TODO
$indice = $_POST['indice'];
$data = $_POST['data'];

//echo $indice;
//echo "<br>";
//echo $data;
$data_nome = date("Ymd", strtotime($data));
$data_titolo = date("d/m/Y", strtotime($data));
//echo $data_nome;
$nome= $indice. "_" .$data_nome;
$titolo= $indice. " " .$data_titolo;
}
?>


<hr>
<br><br><br>

        <div id="map">
        </div>
        <script src="js/qgis2web_expressions.js"></script>
        <script src="js/leaflet.js"></script>
        <script src="js/leaflet.rotatedMarker.js"></script>
        <!--script src="js/leaflet.pattern.js"></script-->
        <script src="js/leaflet-hash.js"></script>
        <script src="js/Autolinker.min.js"></script>
        <script src="js/rbush.min.js"></script>
        <script src="js/labelgun.min.js"></script>
        <script src="js/labels.js"></script>
        <script src="js/leaflet.wms.js"></script>
        <script>
        /*var map = L.map('map', {
            zoomControl:true, maxZoom:28, minZoom:1
        }).fitBounds([[44.3309056656,7.22369874462],[46.7527954681,12.6618563362]]);*/
        var map = L.map('map', {
            zoomControl:true, maxZoom:28, minZoom:1
        }).setView([45.6, 9.5],7);

        var hash = new L.Hash(map);
        map.attributionControl.addAttribution('<a href="https://github.com/tomchadwin/qgis2web" target="_blank">qgis2web</a>');
        var bounds_group = new L.featureGroup([]);
        var basemap0 = L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors,<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>',
            maxZoom: 28
        });
        //basemap0.addTo(map);
        var basemap1 = L.tileLayer('http://{s}.www.toolserver.org/tiles/bw-mapnik/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>',
            maxZoom: 28
        });
        //basemap1.addTo(map);
        var basemap2 = L.tileLayer('http://a.tile.stamen.com/toner/{z}/{x}/{y}.png', {
            attribution: 'Map tiles by <a href="http://stamen.com">Stamen Design</a>,<a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a> &mdash; Mapdata: &copy; <a href="http://openstreetmap.org">OpenStreetMap</a>contributors,<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>',
            maxZoom: 28
        });
        //basemap2.addTo(map);
        var basemap3 = L.tileLayer('http://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors,<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>,Tiles courtesy of <a href="http://hot.openstreetmap.org/" target="_blank">Humanitarian OpenStreetMap Team</a>',
            maxZoom: 28
        });
        basemap3.addTo(map);
        function setBounds() {
        }

        var roma40GB = L.CRS.EPSG3003;

        /*var overlay_indici_0 = L.tileLayer.wms("http://www.gter.it:8080/rasdaman/ows", {
            layers: 'fwi_20171220',
            format: 'image/png',
            transparent: true,
            styles: 'indici',
            version:'1.3.0',            
            tiled: false,
            opacity: 0.5,
            identify: false,
            crs:roma40GB,
            attribution: "ARPA Lombardia"
        });*/



        //var overlay_indici_0 = L.WMS.layer("http://<?php echo $HOST ?>/rasdaman/ows?", "fwi_20171220", {
        var overlay_indici_0 = L.WMS.layer("http://<?php echo $HOST ?>:8080/rasdaman/ows?", "<?php echo $nome; ?>", {
            format: 'image/png',
            uppercase: true,
            transparent: true,
            continuousWorld : true,
            tiled: false,
            info_format: 'text/html',
            opacity: 0.5,
            identify: false,
            version:'1.3.0',
            crs:roma40GB,
	        styles: 'indici'
        });
        map.addLayer(overlay_indici_0);
        var baseMaps = {'OSM': basemap0, 'OSM B&W': basemap1, 'Stamen Toner': basemap2, 'OSM HOT': basemap3};
        L.control.layers(baseMaps,{"<?php echo $titolo; ?>": overlay_indici_0,},{collapsed:false}).addTo(map);
        setBounds();
        </script>
    </body>
</html>
