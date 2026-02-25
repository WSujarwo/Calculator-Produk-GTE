<html>
<head>
    <meta charset="utf-8">
    <style>
        table { border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px; }
        th, td { border: 1px solid #777; padding: 4px 6px; text-align: center; }
        .head1 { background: #d9ead3; font-weight: 700; }
        .head2 { background: #fff2cc; font-weight: 700; }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr class="head1">
                <th colspan="4">SIZE</th>
                <th colspan="27">EXPANSION JOINT METAL</th>
                <th colspan="3">CIRCUMFERENCE</th>
                <th colspan="1">CAN LENGTH</th>
                <th colspan="1">CIRCUMFERENCE COLLAR</th>
            </tr>
            <tr class="head2">
                <th>INCH</th><th>NB</th><th>WIDTH</th><th>LENGTH</th>
                <th>ID</th><th>OD</th><th>THK</th><th>LY</th><th>NOC</th><th>LC</th><th>TC</th><th>P</th><th>TR</th><th>R</th>
                <th>OAL_B</th><th>BL</th><th>TL</th><th>SLC</th><th>LPE</th><th>PRES</th><th>TEMP_C</th><th>AXIAL_M</th>
                <th>LSR_N_PER</th><th>MP_CI_MPA</th><th>MP_II_MPA</th><th>MLC</th><th>GPF</th><th>OAL</th><th>AL</th>
                <th>WIDTH1</th><th>WIDTH2</th><th>SPARE</th>
                <th>CAN_LENGTH</th>
                <th>CIRCUMFERENCE_COLLAR</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    <td>{{ $row['inch'] }}</td><td>{{ $row['nb'] }}</td><td>{{ $row['width'] }}</td><td>{{ $row['length'] }}</td>
                    <td>{{ $row['id_mm'] }}</td><td>{{ $row['od_mm'] }}</td><td>{{ $row['thk'] }}</td><td>{{ $row['ly'] }}</td><td>{{ $row['noc'] }}</td>
                    <td>{{ $row['lc'] }}</td><td>{{ $row['tc'] }}</td><td>{{ $row['p'] }}</td><td>{{ $row['tr'] }}</td><td>{{ $row['r'] }}</td>
                    <td>{{ $row['oal_b'] }}</td><td>{{ $row['bl'] }}</td><td>{{ $row['tl'] }}</td><td>{{ $row['slc'] }}</td><td>{{ $row['lpe'] }}</td>
                    <td>{{ $row['pres'] }}</td><td>{{ $row['temp_c'] }}</td><td>{{ $row['axial_m'] }}</td><td>{{ $row['lsr_n_per'] }}</td>
                    <td>{{ $row['mp_ci_mpa'] }}</td><td>{{ $row['mp_ii_mpa'] }}</td><td>{{ $row['mlc'] }}</td><td>{{ $row['gpf'] }}</td>
                    <td>{{ $row['oal'] }}</td><td>{{ $row['al'] }}</td>
                    <td>{{ $row['width1'] }}</td><td>{{ $row['width2'] }}</td><td>{{ $row['spare'] }}</td>
                    <td>{{ $row['can_length'] }}</td>
                    <td>{{ $row['circumference_collar'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
