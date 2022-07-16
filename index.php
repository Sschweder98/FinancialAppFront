<!DOCTYPE html>
<html>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<style>
  body {
    font-size: 150%;
    font-family: arial;
    margin: 150px;
  }

  .chartBox {
    clear: both;
    display: flex;
    border: 2px solid lightgray;
    padding: 50px;
    border-radius: 15px;
  }

  table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
    height: 0px;
    margin-top: 100px;
  }

  td,
  th {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
  }

  tr:nth-child(even) {
    background-color: #dddddd;
  }

  canvas {
    max-width: 1000px;
  }
</style>

<body>
  <?php
  $date = $_GET['date'];
  $servername = "localhost";
  $username = "root";
  $password = "test#1234";
  $dbname = "finance_app";
  $conn = new mysqli($servername, $username, $password, $dbname);
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
  ?>

  <h1>Finance-Front</h1>
  <br>
  <h2><?php echo $date; ?></h2>
  <h3>Einnahmen</h3>
  <div class="chartBox">
    <canvas id="firstChart" ></canvas>
    <table id="tableEinnahmen">
      <tr>
        <th>Kategorie</th>
        <th>Summe</th>
      </tr>
    </table>
  </div>
  <br>
  <h3>Ausgaben</h3>
  <h4>Alle</h4>
  <div class="chartBox">
    <canvas id="secondChart" ></canvas>
    <table id="tableAusgabenAlle">
      <tr>
        <th>Kategorie</th>
        <th>Summe</th>
      </tr>
    </table>
  </div>
  <h4>Fix</h4>
  <div class="chartBox">
    <canvas id="thirdChart" ></canvas>
    <table id="tableAusgabenFix">
      <tr>
        <th>Kategorie</th>
        <th>Summe</th>
      </tr>
    </table>
  </div>
  <h4>Nicht Fix</h4>
  <div class="chartBox">
    <canvas id="fourthChart" ></canvas>
    <table id="tableAusgabenNichtFix">
      <tr>
        <th>Kategorie</th>
        <th>Summe</th>
      </tr>
    </table>
  </div>

  <script>
    //Einnahmen
    var x_einnahmen_1 = [];
    var y_einnahmen_1 = [];
    //Ausgaben
    var x_ausgaben_1 = [];
    var y_ausgaben_1 = [];
    var x_ausgaben_fix_1 = [];
    var y_ausgaben_fix_1 = [];
    var x_ausgaben_non_fix_1 = [];
    var y_ausgaben_non_fix_1 = [];

    <?php
    //Fülle Einnahmen
    $sql = "SELECT T1.category as cat, (SELECT ROUND(SUM(VALUE), 2) WHERE category = T1.category) AS summ FROM _finance_data T1 WHERE
    T1.category NOT LIKE 'Umbuchung' 
    AND T1.DATE LIKE '" . $date . "%' 
    AND T1.VALUE > 0.0 
    GROUP BY (T1.category)
    ORDER BY summ ASC
    ";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      // output data of each row
      while ($row = $result->fetch_assoc()) {
        echo 'x_einnahmen_1.push("' . $row["cat"] . '");';
        echo 'y_einnahmen_1.push("' . $row["summ"] . '");';
      }
    }
    //Fülle Ausgaben(alle)
    $sql = "SELECT T1.category as cat, (SELECT ROUND(SUM(VALUE), 2) WHERE category = T1.category) AS summ FROM _finance_data T1 WHERE
    T1.category NOT LIKE 'Umbuchung' 
    AND T1.DATE LIKE '" . $date . "%' 
    AND T1.VALUE < 0.0 
    GROUP BY (T1.category)
    ORDER BY summ ASC
    ";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      // output data of each row
      while ($row = $result->fetch_assoc()) {
        echo 'x_ausgaben_1.push("' . $row["cat"] . '");';
        echo 'y_ausgaben_1.push("' . $row["summ"] . '");';
      }
    }
    //Fülle Ausgaben(fix)
    $sql = "SELECT T1.category as cat, (SELECT ROUND(SUM(VALUE), 2) WHERE category = T1.category) AS summ FROM _finance_data T1 WHERE
    T1.category NOT LIKE 'Umbuchung' 
    AND T1.DATE LIKE '" . $date . "%' 
    AND T1.VALUE < 0.0 
    AND T1.cost_fixed = 1
    GROUP BY (T1.category)
    ORDER BY summ ASC
    ";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      // output data of each row
      while ($row = $result->fetch_assoc()) {
        echo 'x_ausgaben_fix_1.push("' . $row["cat"] . '");';
        echo 'y_ausgaben_fix_1.push("' . $row["summ"] . '");';
      }
    }
    //Fülle Ausgaben(nicht fix)
    $sql = "SELECT T1.category as cat, (SELECT ROUND(SUM(VALUE), 2) WHERE category = T1.category) AS summ FROM _finance_data T1 WHERE
    T1.category NOT LIKE 'Umbuchung' 
    AND T1.DATE LIKE '" . $date . "%' 
    AND T1.VALUE < 0.0 
    AND T1.cost_fixed = 0
    GROUP BY (T1.category)
    ORDER BY summ ASC
    ";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      // output data of each row
      while ($row = $result->fetch_assoc()) {
        echo 'x_ausgaben_non_fix_1.push("' . $row["cat"] . '");';
        echo 'y_ausgaben_non_fix_1.push("' . $row["summ"] . '");';
      }
    }
    ?>

    //Einnahmen  
    for (i = 0, len = x_einnahmen_1.length, text = ""; i < len; i++) {
      let tmp = document.getElementById("tableEinnahmen").innerHTML;
      document.getElementById("tableEinnahmen").innerHTML = tmp + "<tr><td>" + x_einnahmen_1[i] + "</td><td>" + formatEuro(y_einnahmen_1[i]) + "</td></tr>";
    }
    //Summe
    tmp = document.getElementById("tableEinnahmen").innerHTML;
    document.getElementById("tableEinnahmen").innerHTML = tmp + "<tr><td>Gesamt</td><td>" + formatEuro(getSum(y_einnahmen_1)) + "</td></tr>";


    //Ausgaben(alle)
    for (i = 0, len = x_ausgaben_1.length, text = ""; i < len; i++) {
      let tmp = document.getElementById("tableAusgabenAlle").innerHTML;
      document.getElementById("tableAusgabenAlle").innerHTML = tmp + "<tr><td>" + x_ausgaben_1[i] + "</td><td>" + formatEuro(y_ausgaben_1[i]) + "</td></tr>";
    }
    tmp = document.getElementById("tableAusgabenAlle").innerHTML;
    document.getElementById("tableAusgabenAlle").innerHTML = tmp + "<tr><td>Gesamt</td><td>" + formatEuro(getSum(y_ausgaben_1)) + "</td></tr>";

    //Ausgaben(fix)
    for (i = 0, len = x_ausgaben_fix_1.length, text = ""; i < len; i++) {
      let tmp = document.getElementById("tableAusgabenFix").innerHTML;
      document.getElementById("tableAusgabenFix").innerHTML = tmp + "<tr><td>" + x_ausgaben_fix_1[i] + "</td><td>" + formatEuro(y_ausgaben_fix_1[i]) + "</td></tr>";
    }
    tmp = document.getElementById("tableAusgabenFix").innerHTML;
    document.getElementById("tableAusgabenFix").innerHTML = tmp + "<tr><td>Gesamt</td><td>" + formatEuro(getSum(y_ausgaben_fix_1)) + "</td></tr>";

    //Ausgaben(nicht fix)
    for (i = 0, len = x_ausgaben_non_fix_1.length, text = ""; i < len; i++) {
      let tmp = document.getElementById("tableAusgabenNichtFix").innerHTML;
      document.getElementById("tableAusgabenNichtFix").innerHTML = tmp + "<tr><td>" + x_ausgaben_non_fix_1[i] + "</td><td>" + formatEuro(y_ausgaben_non_fix_1[i]) + "</td></tr>";
    }
    tmp = document.getElementById("tableAusgabenNichtFix").innerHTML;
    document.getElementById("tableAusgabenNichtFix").innerHTML = tmp + "<tr><td>Gesamt</td><td>" +formatEuro( getSum(y_ausgaben_non_fix_1)) + "</td></tr>";



    var barColors = [
      "#b91d47",
      "#00aba9",
      "#2b5797",
      "#e8c3b9",
      "#1e7145",
      "#006400",
      "#008B8B",
      "#B8860B",
      "#00008B",
      "#7FFF00",
      "#228B22"
    ];

    new Chart("firstChart", {
      type: "doughnut",
      data: {
        labels: x_einnahmen_1,
        datasets: [{
          backgroundColor: barColors,
          data: y_einnahmen_1
        }]
      },
      options: {
        title: {
          display: true,
          text: "Einnahmen"
        }
      }
    });

    new Chart("secondChart", {
      type: "doughnut",
      data: {
        labels: x_ausgaben_1,
        datasets: [{
          backgroundColor: barColors,
          data: y_ausgaben_1
        }]
      },
      options: {
        title: {
          display: true,
          text: "Ausgaben"
        }
      }
    });

    new Chart("thirdChart", {
      type: "doughnut",
      data: {
        labels: x_ausgaben_fix_1,
        datasets: [{
          backgroundColor: barColors,
          data: y_ausgaben_fix_1
        }]
      },
      options: {
        title: {
          display: true,
          text: "Ausgaben Fix"
        }
      }
    });

    new Chart("fourthChart", {
      type: "doughnut",
      data: {
        labels: x_ausgaben_non_fix_1,
        datasets: [{
          backgroundColor: barColors,
          data: y_ausgaben_non_fix_1
        }]
      },
      options: {
        title: {
          display: true,
          text: "Ausgaben nicht Fix"
        }
      }
    });

    function getSum(array) {
      var count = 0.0;
      for (var i = array.length; i--;) {
        count += parseFloat(array[i]);
      }
      return count;
    }

    function formatEuro(num){
      return new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR' }).format(num);
    }
  </script>

  <?php
  $conn->close();
  ?>
</body>

</html>