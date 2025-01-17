<?php
   require_once("dompdf/dompdf_config.inc.php");
   if(isset($_POST['submit']))
   {
      //echo 'posting: ' . $_POST['chart_html']; exit;
      //var_dump($_POST); exit;
      
      $html = '<html>
      <head>
        <title></title>
      </head>
      <body>' . 
        '<img src="' . $_POST['pie_div_hidden'] . '">' .
        '<img src="' . $_POST['line_div_hidden'] . '">' .
        '<img src="' . $_POST['treemap_div_hidden'] . '">' .
        '<img src="' . $_POST['map_div_hidden'] . '">' .
        '<img src="' . $_POST['scatter_div_hidden'] . '">' .
      '</body>
      </html>';
      $dompdf = new DOMPDF();
      $dompdf->load_html($html);
      $dompdf->render();
      $dompdf->stream("sample" . date('His') . ".pdf");
  }
?>

<html>
  <head>
    <script type="text/javascript" src="http://canvg.googlecode.com/svn/trunk/rgbcolor.js"></script> 
    <script type="text/javascript" src="http://canvg.googlecode.com/svn/trunk/canvg.js"></script>
    <script>
      function getImgData(chartContainer) {
        //var chartArea = chartContainer.getElementsByTagName('iframe')[0].
          //contentDocument.getElementById('chartArea');
        var chartArea =
           chartContainer.getElementsByTagName('svg')[0].parentNode;
        var svg = chartArea.innerHTML;        
        var doc = chartContainer.ownerDocument;
        var canvas = doc.createElement('canvas');
        canvas.setAttribute('width', chartArea.offsetWidth);
        canvas.setAttribute('height', chartArea.offsetHeight);
                
        canvas.setAttribute(
            'style',
            'position: absolute; ' +
            'top: ' + (-chartArea.offsetHeight * 2) + 'px;' +
            'left: ' + (-chartArea.offsetWidth * 2) + 'px;');
        doc.body.appendChild(canvas);
        canvg(canvas, svg);
        var imgData = canvas.toDataURL("image/png");
        canvas.parentNode.removeChild(canvas);
        return imgData;
      }
      
      function saveAsImg(chartContainer) {
        var imgData = getImgData(chartContainer);
        
        // Replacing the mime-type will force the browser to trigger a download
        // rather than displaying the image in the browser window.
        window.location = imgData.replace("image/png", "image/octet-stream");
      }
      
      function toImg(chartContainer, imgContainer) { 
        var doc = chartContainer.ownerDocument;
        var img = doc.createElement('img');
        img.src = imgData = getImgData(chartContainer);
        document.getElementById("chart_html").value = imgData;
        while (imgContainer.firstChild) {
          imgContainer.removeChild(imgContainer.firstChild);
        }
        imgContainer.appendChild(img);
      }


      function setImgData(hiddenField, chartContainer) { 
        var doc = chartContainer.ownerDocument;
        var imgData = getImgData(chartContainer);
        document.getElementById(hiddenField).value = imgData;
      }


      function lineChartReadyHandler(){
        setImgData("line_div_hidden", document.getElementById('line_div'));
      }

      function pieChartReadyHandler(){
        setImgData("pie_div_hidden", document.getElementById('pie_div'));
      }

      function scatterChartReadyHandler(){
        setImgData("scatter_div_hidden", document.getElementById('scatter_div'));
      }

      function treemapChartReadyHandler(){
        setImgData("treemap_div_hidden", document.getElementById('treemap_div'));
      }

      function mapChartReadyHandler(){
        setImgData("map_div_hidden", document.getElementById('map_div'));
      }

    </script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart", "treemap", "geochart"]});
      google.setOnLoadCallback(drawChart);

      function drawChart(){
        drawPieChart();
        drawLineChart();
      }

      function drawPieChart() {
        // Pie chart
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Task');
        data.addColumn('number', 'Hours per Day');
        data.addRows(5);
        data.setValue(0, 0, 'Work');
        data.setValue(0, 1, 11);
        data.setValue(1, 0, 'Eat');
        data.setValue(1, 1, 2);
        data.setValue(2, 0, 'Commute');
        data.setValue(2, 1, 2);
        data.setValue(3, 0, 'Watch TV');
        data.setValue(3, 1, 2);
        data.setValue(4, 0, 'Sleep');
        data.setValue(4, 1, 7);

        var piechart = new google.visualization.PieChart(document.getElementById('pie_div'));
        google.visualization.events.addListener(piechart, 'ready', pieChartReadyHandler);
        piechart.draw(data, {width: 450, height: 300, title: 'My Daily Activities'});
        
        
        
        // Treemap
        data = new google.visualization.DataTable();
        data.addColumn('string', 'Region');
        data.addColumn('string', 'Parent');
        data.addColumn('number', 'Market trade volume (size)');
        data.addColumn('number', 'Market increase/decrease (color)');
        data.addRows([
          ["Global",null,0,0],
          ["America","Global",0,0],
          ["Europe","Global",0,0],
          ["Asia","Global",0,0],
          ["Australia","Global",0,0],
          ["Africa","Global",0,0],
          ["Brazil","America",11,10],
          ["USA","America",52,31],
          ["Mexico","America",24,12],
          ["Canada","America",16,-23],
          ["France","Europe",42,-11],
          ["Germany","Europe",31,-2],
          ["Sweden","Europe",22,-13],
          ["Italy","Europe",17,4],
          ["UK","Europe",21,-5],
          ["China","Asia",36,4],
          ["Japan","Asia",20,-12],
          ["India","Asia",40,63],
          ["Laos","Asia",4,34],
          ["Mongolia","Asia",1,-5],
          ["Israel","Asia",12,24],
          ["Iran","Asia",18,13],
          ["Pakistan","Asia",11,-52],
          ["Egypt","Africa",21,0],
          ["S. Africa","Africa",30,43],
          ["Sudan","Africa",12,2],
          ["Congo","Africa",10,12],
          ["Zair","Africa",8,10]
        ]);

        var tree = new google.visualization.TreeMap(document.getElementById('treemap_div'));
        google.visualization.events.addListener(tree, 'ready', treemapChartReadyHandler);
        tree.draw(data, {
          minColor: '#f00',
          midColor: '#ddd',
          maxColor: '#0d0',
          headerHeight: 15,
          fontColor: 'black',
          showScale: true});
          
        data = new google.visualization.DataTable();
        data.addRows(6);
        data.addColumn('string', 'Country');
        data.addColumn('number', 'Popularity');
        data.setValue(0, 0, 'Germany');
        data.setValue(0, 1, 200);
        data.setValue(1, 0, 'United States');
        data.setValue(1, 1, 300);
        data.setValue(2, 0, 'Brazil');
        data.setValue(2, 1, 400);
        data.setValue(3, 0, 'Canada');
        data.setValue(3, 1, 500);
        data.setValue(4, 0, 'France');
        data.setValue(4, 1, 600);
        data.setValue(5, 0, 'RU');
        data.setValue(5, 1, 700);

        var options = {};
        var container = document.getElementById('map_div');
        var geochart = new google.visualization.GeoChart(container);
        google.visualization.events.addListener(geochart, 'ready', mapChartReadyHandler);
        geochart.draw(data, options);
        
        data = new google.visualization.DataTable();
        data.addColumn('number', 'Age');
        data.addColumn('number', 'Weight');
        data.addRows(6);
        data.setValue(0, 0, 8);
        data.setValue(0, 1, 12);
        data.setValue(1, 0, 4);
        data.setValue(1, 1, 5.5);
        data.setValue(2, 0, 11);
        data.setValue(2, 1, 14);
        data.setValue(3, 0, 4);
        data.setValue(3, 1, 4.5);
        data.setValue(4, 0, 3);
        data.setValue(4, 1, 3.5);
        data.setValue(5, 0, 6.5);
        data.setValue(5, 1, 7);

        var chart = new google.visualization.ScatterChart(document.getElementById('scatter_div'));
        google.visualization.events.addListener(chart, 'ready', scatterChartReadyHandler);
        chart.draw(data, {width: 400, height: 240,
                          title: 'Age vs. Weight comparison',
                          hAxis: {title: 'Age', minValue: 0, maxValue: 15},
                          vAxis: {title: 'Weight', minValue: 0, maxValue: 15},
                          legend: 'none'
                         });        
      }

      function drawLineChart(){
        // Line chart
        data = new google.visualization.DataTable();
        data.addColumn('string', 'Year');
        data.addColumn('number', 'Sales');
        data.addColumn('number', 'Expenses');
        data.addRows(4);
        data.setValue(0, 0, '2004');
        data.setValue(0, 1, 1000);
        data.setValue(0, 2, 400);
        data.setValue(1, 0, '2005');
        data.setValue(1, 1, 1170);
        data.setValue(1, 2, 460);
        data.setValue(2, 0, '2006');
        data.setValue(2, 1, 860);
        data.setValue(2, 2, 580);
        data.setValue(3, 0, '2007');
        data.setValue(3, 1, 1030);
        data.setValue(3, 2, 540);

        var lineChart = new google.visualization.LineChart(document.getElementById('line_div'));
        google.visualization.events.addListener(lineChart, 'ready', lineChartReadyHandler);
        lineChart.draw(data, {width: 450, height: 300, title: 'Company Performance'});
      }
    </script>
  </head>
  <body>
    <form name='pdf_form' aciton="index.php" method="post" >
    <div id="img_div" style="position: fixed; top: 0; right: 0; z-index: 10; border: 1px solid #b9b9b9">
      Image will be placed here
    </div>

    <button onclick="saveAsImg(document.getElementById('pie_div'));">Save as PNG Image</button>
    <button onclick="toImg(document.getElementById('pie_div'), document.getElementById('img_div'));">Convert to image</button>
    <input type="hidden" name="pie_div_hidden" id="pie_div_hidden" value="" />
    <div id="pie_div"></div>

    <button onclick="saveAsImg(document.getElementById('line_div'));">Save as PNG Image</button>
    <button onclick="toImg(document.getElementById('line_div'), document.getElementById('img_div'));">Convert to image</button>
    <input type="hidden" name="line_div_hidden" id="line_div_hidden" value="" />
    <div id="line_div"></div>    
    
    <button onclick="saveAsImg(document.getElementById('treemap_div'));">Save as PNG Image</button>
    <button onclick="toImg(document.getElementById('treemap_div'), document.getElementById('img_div'));">Convert to image</button>
    <input type="hidden" name="treemap_div_hidden" id="treemap_div_hidden" value="" />
    <div id="treemap_div" style="width: 450px; height: 300px;"></div>

    <button onclick="saveAsImg(document.getElementById('map_div'));">Save as PNG Image</button>
    <button onclick="toImg(document.getElementById('map_div'), document.getElementById('img_div'));">Convert to image</button>
    <input type="hidden" name="map_div_hidden" id="map_div_hidden" value="" />
    <div id="map_div" style="width: 500px; height: 300px"></div>
    
    <button onclick="saveAsImg(document.getElementById('scatter_div'));">Save as PNG Image</button>
    <button onclick="toImg(document.getElementById('scatter_div'), document.getElementById('img_div'));">Convert to image</button>
    <input type="hidden" name="scatter_div_hidden" id="scatter_div_hidden" value="" />
    <div id="scatter_div"></div>   

    
      <div>
          <!-- <textarea name="chart_html" id="chart_html" cols="25" rows="5"></textarea><br/> -->
          <input type="submit" name="submit" value="GENERATE">
      </div>
    </form>
</body>
</html>