<html>
<head>
    <meta charset="utf-8">
    <style>
        table { border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px; }
        th, td { border: 1px solid #777; padding: 4px 6px; text-align: center; }
        .head1 { background: #d9ead3; font-weight: 700; }
        .head2 { background: #fff2cc; font-weight: 700; }
        .title { font-size: 16px; font-weight: 700; text-align: left; margin-bottom: 8px; }
    </style>
</head>
<body>
    <div class="title">ACTUAL DETAIL CALCULATION</div>
    <table>
        <thead>
            <tr class="head1">
                <th colspan="2">Size</th>
                <th colspan="3">TL</th>
                <th colspan="3">Spacer</th>
                <th colspan="4">Tool Radius</th>
                <th rowspan="2">TL + Spacer + Tool Radius</th>
                <th rowspan="2">GAP</th>
                <th rowspan="2">CAN Length</th>
            </tr>
            <tr class="head2">
                <th>Inche</th>
                <th>NB</th>
                <th>Width</th>
                <th>Qty</th>
                <th>Total</th>
                <th>Width</th>
                <th>Qty</th>
                <th>Total</th>
                <th>Pitch EJMA</th>
                <th>Pitch GTE</th>
                <th>Qty</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    <td>{{ $row['size_inch'] }}</td>
                    <td>{{ $row['nb'] }}</td>
                    <td>{{ $row['tl_width'] }}</td>
                    <td>{{ $row['tl_qty'] }}</td>
                    <td>{{ $row['tl_total'] }}</td>
                    <td>{{ $row['spacer_width'] }}</td>
                    <td>{{ $row['spacer_qty'] }}</td>
                    <td>{{ $row['spacer_total'] }}</td>
                    <td>{{ $row['pitch_ejma'] }}</td>
                    <td>{{ $row['pitch_gte'] }}</td>
                    <td>{{ $row['tool_radius_qty'] }}</td>
                    <td>{{ $row['tool_radius_total'] }}</td>
                    <td>{{ $row['tl_spacer_tool_total'] }}</td>
                    <td>{{ $row['gap'] }}</td>
                    <td>{{ $row['can_length'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="title" style="margin-top: 18px;">CALCULATION OF CAN LENGTH</div>
    <table>
        <thead>
            <tr class="head1">
                <th colspan="2">Size</th>
                <th colspan="2">Bellows</th>
                <th colspan="4">EJMA Calculation</th>
                <th colspan="3">Manual Circumference</th>
                <th colspan="4">Current Correction</th>
                <th colspan="2">CAN Length</th>
            </tr>
            <tr class="head2">
                <th>Inch</th>
                <th>NB</th>
                <th>ID</th>
                <th>THK</th>
                <th>LY</th>
                <th>Circm-1</th>
                <th>Circm-2</th>
                <th>GAP</th>
                <th>Circm-1</th>
                <th>Circm-2</th>
                <th>GAP</th>
                <th>Circm-1</th>
                <th>Circm-2</th>
                <th>GAP</th>
                <th>Circm - 2 Actual</th>
                <th>Calculation TL</th>
                <th>Actual</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    <td>{{ $row['size_inch'] }}</td>
                    <td>{{ $row['nb'] }}</td>
                    <td>{{ $row['id_bellows'] ?? '' }}</td>
                    <td>{{ $row['thk'] ?? '' }}</td>
                    <td>{{ $row['ly'] ?? '' }}</td>
                    <td>{{ $row['ejma_circm_1'] ?? '' }}</td>
                    <td>{{ $row['ejma_circm_2'] ?? '' }}</td>
                    <td>{{ $row['ejma_gap'] ?? '' }}</td>
                    <td>{{ $row['manual_circm_1'] ?? '' }}</td>
                    <td>{{ $row['manual_circm_2'] ?? '' }}</td>
                    <td>{{ $row['manual_gap'] ?? '' }}</td>
                    <td>{{ $row['correction_circm_1'] ?? '' }}</td>
                    <td>{{ $row['correction_circm_2'] ?? '' }}</td>
                    <td>{{ $row['correction_gap'] ?? '' }}</td>
                    <td>{{ $row['correction_circm_2_actual'] ?? '' }}</td>
                    <td>{{ $row['calculation_tl'] ?? '' }}</td>
                    <td>{{ $row['can_length_actual'] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
