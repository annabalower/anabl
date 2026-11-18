<?php

$cacheDir = __DIR__ . '/ip-cache';

$allowed = 0;
$blocked = 0;
$total = 0;

$records = [];

if (is_dir($cacheDir)) {

    $files = glob($cacheDir . '/*.json');

    foreach ($files as $file) {

        $data = @json_decode(
            @file_get_contents($file),
            true
        );

        if (!is_array($data)) {
            continue;
        }

        $total++;

        $status = $data['_security_status'] ?? 'ALLOWED';
        $reason = $data['_security_reason'] ?? 'NORMAL';

        if ($status === 'BLOCKED') {
            $blocked++;
        } else {
            $allowed++;
        }

        $ip = $data['ip'] ?? '';

        if (empty($ip)) {
            $ip = 'Unknown';
        }

        $country = $data['location']['country'] ?? '';
        $city    = $data['location']['city'] ?? '';
        $org     = $data['asn']['org'] ?? '';
        $asn     = $data['asn']['asn'] ?? '';

        $checkedAt = $data['_checked_at'] ?? '';

        $records[] = [
            'ip' => $ip,
            'status' => $status,
            'reason' => $reason,
            'country' => $country,
            'city' => $city,
            'org' => $org,
            'asn' => $asn,
            'checked_at' => $checkedAt
        ];
    }
}

/*
 * Cele mai recente primele
 */

usort($records, function ($a, $b) {
    return strcmp($b['checked_at'], $a['checked_at']);
});

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>Security Panel</title>

<meta name="viewport" content="width=device-width, initial-scale=1">

<style>

body {
    font-family: Arial, sans-serif;
    background: #f5f6f8;
    margin: 0;
    padding: 30px;
    color: #222;
}

.container {
    max-width: 1200px;
    margin: auto;
}

h1 {
    margin-bottom: 25px;
}

.stats {
    display: flex;
    gap: 15px;
    margin-bottom: 25px;
}

.stat {
    background: white;
    padding: 20px;
    border-radius: 8px;
    min-width: 150px;
    box-shadow: 0 1px 5px rgba(0,0,0,.08);
}

.stat strong {
    display: block;
    font-size: 28px;
    margin-top: 5px;
}

.allowed {
    color: #16803c;
    font-weight: bold;
}

.blocked {
    color: #c62828;
    font-weight: bold;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    box-shadow: 0 1px 5px rgba(0,0,0,.08);
}

th,
td {
    padding: 12px;
    border-bottom: 1px solid #eee;
    text-align: left;
    font-size: 14px;
}

th {
    background: #f0f1f3;
}

tr:hover {
    background: #fafafa;
}

.badge {
    padding: 5px 9px;
    border-radius: 5px;
    font-size: 12px;
}

.badge.allowed {
    background: #e8f5e9;
}

.badge.blocked {
    background: #ffebee;
}

.empty {
    background: white;
    padding: 30px;
    text-align: center;
}

</style>

</head>

<body>

<div class="container">

<h1>Security Panel</h1>

<div class="stats">

    <div class="stat">
        Total IP
        <strong><?= $total ?></strong>
    </div>

    <div class="stat">
        Allowed
        <strong class="allowed"><?= $allowed ?></strong>
    </div>

    <div class="stat">
        Blocked
        <strong class="blocked"><?= $blocked ?></strong>
    </div>

</div>


<?php if (empty($records)): ?>

<div class="empty">
    No IP records yet.
</div>

<?php else: ?>

<table>

<thead>

<tr>
    <th>IP</th>
    <th>Status</th>
    <th>Reason</th>
    <th>Country</th>
    <th>City</th>
    <th>Provider</th>
    <th>Checked</th>
</tr>

</thead>

<tbody>

<?php foreach ($records as $record): ?>

<tr>

<td>
    <?= htmlspecialchars($record['ip']) ?>
</td>

<td>

<?php if ($record['status'] === 'BLOCKED'): ?>

<span class="badge blocked">
    BLOCKED
</span>

<?php else: ?>

<span class="badge allowed">
    ALLOWED
</span>

<?php endif; ?>

</td>

<td>
    <?= htmlspecialchars($record['reason']) ?>
</td>

<td>
    <?= htmlspecialchars($record['country']) ?>
</td>

<td>
    <?= htmlspecialchars($record['city']) ?>
</td>

<td>
    <?= htmlspecialchars($record['org']) ?>
    <?php if (!empty($record['asn'])): ?>
        <br>
        <small><?= htmlspecialchars($record['asn']) ?></small>
    <?php endif; ?>
</td>

<td>
    <?= htmlspecialchars($record['checked_at']) ?>
</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

<?php endif; ?>

</div>

</body>

</html>