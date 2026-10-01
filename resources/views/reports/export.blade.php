<!DOCTYPE html>
<html>
<head>
    <title>Travel IMS Report</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body >
    <h2>Travel IMS Report</h2>
    <table>
        <thead>
            <tr>
                <th>ID #</th>
                <th>Customer Name</th>
                <th>Cell</th>
                <th>City</th>
                <th>Inquiry Type</th>
                <th>Sales Person</th>
                <th>Sales Reference</th>
                <th>Status</th>
                <th>Travel Date</th>
                <th>Followup Date</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inquiries as $inquiry)
            <tr>
                <td>{{ $inquiry->id_inquiry }}</td>
                <td>{{ optional($inquiry->customer)->customer_name }}</td>
                <td>{{ optional($inquiry->customer)->customer_cell }}</td>
                <td>{{ optional($inquiry->customer)->city }}</td>
                <td>{{ optional($inquiry->inquiryType)->type_name }}</td>
                <td>{{ optional($inquiry->salesPerson)->name }}</td>
                <td>{{ optional($inquiry->salesReference)->type_name }}</td>
                <td>{{ $inquiry->status }}</td>
                <td>{{ $inquiry->travel_date }}</td>
                <td>{{ $inquiry->followup_date }}</td>
                <td>{{ $inquiry->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>