<html>
<head>
    <meta charset="utf-8">
    <style>
        table { border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px; }
        th, td { border: 1px solid #777; padding: 4px 6px; text-align: left; }
        th { background: #d9ead3; }
    </style>
</head>
<body>
    <h3>Template Validasi Material EJM</h3>
    <table>
        <thead>
            <tr>
                <th>material_role</th>
                <th>material_name</th>
                <th>thk_mm</th>
                <th>jumlah_ply</th>
                <th>size_in</th>
                <th>sch</th>
                <th>type</th>
                <th>part_number</th>
                <th>description</th>
                <th>naming</th>
                <th>quality</th>
                <th>price_sqm</th>
                <th>price_kg</th>
                <th>price_gram</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    <td>{{ $row['material_role'] ?? '' }}</td>
                    <td>{{ $row['material_name'] ?? '' }}</td>
                    <td>{{ $row['thk_mm'] ?? '' }}</td>
                    <td>{{ $row['jumlah_ply'] ?? '' }}</td>
                    <td>{{ $row['size_in'] ?? '' }}</td>
                    <td>{{ $row['sch'] ?? '' }}</td>
                    <td>{{ $row['type'] ?? '' }}</td>
                    <td>{{ $row['part_number'] ?? '' }}</td>
                    <td>{{ $row['description'] ?? '' }}</td>
                    <td>{{ $row['naming'] ?? '' }}</td>
                    <td>{{ $row['quality'] ?? '' }}</td>
                    <td>{{ $row['price_sqm'] ?? '' }}</td>
                    <td>{{ $row['price_kg'] ?? '' }}</td>
                    <td>{{ $row['price_gram'] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
