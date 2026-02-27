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
                <th>component</th>
                <th>material</th>
                <th>thk_mm</th>
                <th>ply</th>
                <th>size_in</th>
                <th>sch</th>
                <th>type</th>
                <th>part_number</th>
                <th>description</th>
                <th>naming</th>
                <th>code1</th>
                <th>code2</th>
                <th>code3</th>
                <th>thk_text</th>
                <th>quality</th>
                <th>price_sqm</th>
                <th>price_kg</th>
                <th>price_gram</th>
                <th>weight_gr</th>
                <th>length_m</th>
                <th>weight_per_meter_gr</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    <td>{{ $row['component'] ?? '' }}</td>
                    <td>{{ $row['material'] ?? '' }}</td>
                    <td>{{ $row['thk_mm'] ?? '' }}</td>
                    <td>{{ $row['ply'] ?? '' }}</td>
                    <td>{{ $row['size_in'] ?? '' }}</td>
                    <td>{{ $row['sch'] ?? '' }}</td>
                    <td>{{ $row['type'] ?? '' }}</td>
                    <td>{{ $row['part_number'] ?? '' }}</td>
                    <td>{{ $row['description'] ?? '' }}</td>
                    <td>{{ $row['naming'] ?? '' }}</td>
                    <td>{{ $row['code1'] ?? '' }}</td>
                    <td>{{ $row['code2'] ?? '' }}</td>
                    <td>{{ $row['code3'] ?? '' }}</td>
                    <td>{{ $row['thk_text'] ?? '' }}</td>
                    <td>{{ $row['quality'] ?? '' }}</td>
                    <td>{{ $row['price_sqm'] ?? '' }}</td>
                    <td>{{ $row['price_kg'] ?? '' }}</td>
                    <td>{{ $row['price_gram'] ?? '' }}</td>
                    <td>{{ $row['weight_gr'] ?? '' }}</td>
                    <td>{{ $row['length_m'] ?? '' }}</td>
                    <td>{{ $row['weight_per_meter_gr'] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
