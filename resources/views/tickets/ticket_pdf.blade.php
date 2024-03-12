<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Details</title>
</head>

<body>
    <h1>Ticket Details</h1>

    <!-- Display ticket details -->
    <p>Ticket ID: {{ $ticket->id }}</p>
    <p>Ticket Name: {{ $ticket->name }}</p>
    <!-- Add more ticket details as needed -->

    <!-- Display the PDF content -->
    <embed src="data:application/pdf;base64,{{ base64_encode($ticket->ticket_pdf) }}" type="application/pdf" width="100%" height="600px" />

    <!-- Provide a download link -->
    <a href="{{ route('downloadTicket', $ticket->id) }}" class="btn btn-primary">Download Ticket</a>
</body>

</html>