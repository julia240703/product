<!DOCTYPE html>
<html>
<head>
    <title>Hasil Psikotes</title>
    <style>
        /* Add your styling for the PDF here */
        body {
            font-family: Arial, sans-serif;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <!-- <h1>User Results</h1> -->
    
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Pendidikan Terakhir</th>
                <th>Cabang yang dilamar</th>
                <th>Posisi yang dilamar</th>
                <th>Progress</th>
                <th>Hasil Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($export->query()->get() as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->education }}</td>
                    <td>{{ $user->{'Cabang yang dilamar'} }}</td>
                    <td>{{ $user->{'Posisi yang dilamar'} }}</td>
                    <td>{{ $user->progress }}%</td> <!-- Add "%" symbol here -->
                    <td>{{ $user->average_score }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
