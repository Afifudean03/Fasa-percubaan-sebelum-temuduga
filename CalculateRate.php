<?php

function calculateRates($voltage, $current, $rate) {

    $power = $voltage * $current;

   
    $energy = $power * 1;

    return array(
        'power' => $power,
        'energy' => $energy
    );
}

function calculateHourlyRates($voltage, $current, $rate, $hour) {
    $result = calculateRates($voltage, $current, $rate);

    if (!$result) {
        return false; 
    }

 
    $hourlyEnergy = $result['energy'] * $hour;

   
    $hourlyTotalCharge = $hourlyEnergy * ($rate / 100);

    return array(
        'hourlyEnergy' => $hourlyEnergy,
        'hourlyTotalCharge' => $hourlyTotalCharge
    );
}


$exchangeRate = 4.20;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    $voltage = $_POST['voltage'];
    $current = $_POST['current'];
    $rate = $_POST['rate'];

   
    if (!empty($voltage) && is_numeric($current) && !empty($rate)) {
        
        $result = calculateRates($voltage, $current, $rate);

        if ($result) {
           
            $totalChargeMYR = $result['energy'] * ($rate / 100) * $exchangeRate;
        } else {
            $error = "Please enter valid values for voltage, current, and rate.";
        }

        
        $hourlyResults = array();

        
        for ($hour = 1; $hour <= 24; $hour++) {
            
            $hourlyResult = calculateHourlyRates($voltage, $current, $rate, $hour);

            if ($hourlyResult) {
               
                $hourlyTotalChargeMYR = $hourlyResult['hourlyTotalCharge'] * $exchangeRate;

                
                $hourlyResults[$hour] = array(
                    'hourlyEnergy' => $hourlyResult['hourlyEnergy'],
                    'hourlyTotalChargeMYR' => $hourlyTotalChargeMYR
                );
            } else {
                $error = "Please enter a valid current value.";
                break;
            }
        }
    } else {
        $error = "Please fill in all the fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }

        h2 {
            color: #007bff;
        }

        form {
            margin-top: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        input {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

      .calculate {
        background-color: #007bff;
        color: #fff;
        cursor: pointer;
        font-size: 14px; 
        width: 20%;
    }

        .calculate:hover {
            background-color: #0056b3;
        }

        .alert {
            margin-top: 20px;
        }

        .mt-4 {
            margin-top: 20px;
        }

        h4 {
            color: #007bff;
        }

        table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: #fff;
        }

        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
    <title>Electricity Rates Calculator</title>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">Electricity Rates Calculator</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="voltage">Voltage (V):</label>
            <input type="text" class="form-control" id="voltage" name="voltage" pattern="[0-9]+(\.[0-9]{1,2})?" title="Enter a valid decimal number" required>
        </div>
        <div class="form-group">
            <label for="current">Current (A):</label>
            <input type="text" class="form-control" id="current" name="current" pattern="[0-9]+(\.[0-9]{1,2})?" title="Enter a valid decimal number" required>
        </div>
        <div class="form-group">
            <label for="rate">Current Rate (per kWh):</label>
            <input type="text" class="form-control" id="rate" name="rate" pattern="[0-9]+(\.[0-9]{1,2})?" title="Enter a valid decimal number" required>
        </div>
        <center>
        <div class="calculate">
        <button type="submit" class="btn btn-primary btn-block">Calculate</button>
    </div>
    </center>
    </form>

    <?php if (isset($result)): ?>
        <div class="mt-4">
            <h4>Overall Results:</h4>
            <p>Power: <?php echo $result['power']; ?> watts</p>
            <p>Energy: <?php echo $result['energy']; ?> kWh</p>
            <p>Total Charge: RM<?php echo number_format($totalChargeMYR, 2); ?></p>
        </div>
    <?php endif; ?>

    <?php if (isset($hourlyResults)): ?>
        <div class="mt-4">
            <h4>Hourly Results:</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Hour</th>
                        <th>Energy (kWh)</th>
                        <th>Total Charge (MYR)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($hourlyResults as $hour => $hourlyResult): ?>
                        <tr>
                            <td><?php echo $hour; ?></td>
                            <td><?php echo $hourlyResult['hourlyEnergy']; ?></td>
                            <td><?php echo number_format($hourlyResult['hourlyTotalChargeMYR'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
