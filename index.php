<?php
function checkPort($ip, $port, $timeout = 3)
{
    $connection = @fsockopen($ip, $port, $errno, $errstr, $timeout);
    if ($connection) {
        fclose($connection);
        return true;
    }
    return false;
}

$result = "";
$ip = "";
$host = "";
$port = 0;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = trim($_POST["ip"] ?? "");
    $port = (int) ($_POST["port"] ?? 0);
    if (filter_var($input, FILTER_VALIDATE_IP)) {
        $ip = $input;
    } else {
        $host = $input;
        $resolvedIp = gethostbyname($host);
        if (!filter_var($resolvedIp, FILTER_VALIDATE_IP) || $resolvedIp === $host) {
            $result = "<div style='color:red'>Hostname or IP is not valid.</div>";
        } else {
            $ip = $resolvedIp;
        }
    }

    if (empty($result)) {
        if ($port < 1 || $port > 65535) {
            $result = "<div style='color:red'>Port must from 1 to 65535.</div>";
        } else {
            $isOpen = checkPort($ip, $port);
            $displayHost = $host ?: $ip;
            if ($isOpen) {
                $result = "<div style='color:cyan'>{$displayHost}:{$port} <b style='color:green'>is OPEN</b>.</div>";
            } else {
                $result = "<div style='color:cyan'>{$displayHost}:{$port} <b style='color:red'>is CLOSED</b>.</div>";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiểm tra Port</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 50px;
            background-color: #42474a;
        }

        form {
            display: inline-block;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background: #42474a;
        }

        input {
            padding: 8px;
            margin: 5px;
            width: 200px;
            border-radius: 8px;
        }

        button {
            padding: 8px 15px;
            background: green;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 8px;
        }

        button:hover {
            background: darkgreen;
        }

        .result {
            margin-top: 20px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h2 style="color:white">Kiểm Tra Cổng</h2>
    <form method="POST">
        <label style="color:white">IP Addres:</label><br>
        <input type="text" name="ip" required <?php if ($ip): ?> value="<?= $ip ?>" <?php endif ?>><br>
        <label style="color:white">Port:</label><br>
        <input type="number" name="port" min="1" max="65535" required <?php if ($port): ?> value="<?= $port ?>" <?php endif ?>><br>
        <button type="submit">CHECK</button>
    </form>

    <?php if ($result !== null): ?>
        <div class="result"><?= $result ?></div>
    <?php endif; ?>
</body>

</html>
