<!DOCTYPE html>
<html>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
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
</style>

<body>
  <?php
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
  <h2>Juni</h2>
  <h3>Einnahmen</h3>
  <div class="chartBox">
    <canvas id="firstChart" style="width:100%;max-width:600px"></canvas>
    <table id="tableEinnahmen">
      <tr>
        <th>Kategorie</th>
        <th>Summe</th>
      </tr>
    </table>
  </div>
  <br>
  <h3>Ausgaben</h3>
  <div class="chartBox">
    <canvas id="firstChart2" style="width:100%;max-width:600px"></canvas>
  </div>

  <script>
    var x_einnahmen_1 = [];
    var y_einnahmen_1 = [];

    <?php
    $sql = "SELECT T1.category as cat, (SELECT ROUND(SUM(VALUE), 2) WHERE category = T1.category) AS summ FROM _finance_data T1 WHERE
    T1.category NOT LIKE 'Umbuchung' 
    AND T1.DATE LIKE '2022-06%' 
    AND T1.VALUE > 0.0 
    GROUP BY (T1.category)
    ORDER BY T1.VALUE ASC
    ";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      // output data of each row
      while ($row = $result->fetch_assoc()) {

        echo 'x_einnahmen_1.push("' . $row["cat"] . '");';
        echo 'y_einnahmen_1.push("' . $row["summ"] . '");';
      }
    } else {
      echo "0 results";
    }
    ?>

    
    for (i = 0, len = x_einnahmen_1.length, text = ""; i < len; i++) {
      let tmp = document.getElementById("tableEinnahmen").innerHTML;
      document.getElementById("tableEinnahmen").innerHTML = tmp + "<tr><td>" + x_einnahmen_1[i] + "</td><td>" + y_einnahmen_1[i] + "</td></tr>";
    }



    var barColors = [
      "#b91d47",
      "#00aba9",
      "#2b5797",
      "#e8c3b9",
      "#1e7145"
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
          text: "World Wide Wine Production 2018"
        }
      }
    });

    new Chart("firstChart2", {
      type: "bar",
      data: {
        labels: x_einnahmen_1,
        datasets: [{
          backgroundColor: barColors,
          data: y_einnahmen_1
        }]
      },
      options: {
        legend: {
          display: false
        },
        title: {
          display: true,
          text: "Einnahmen"
        }
      }
    });
  </script>

  <?php
  $conn->close();
  ?>
</body>

</html>