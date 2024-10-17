<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Report for {{ $date }}</title>
</head>

<body>
    <h1>Daily Report for {{ $date }}</h1>

    <table border="1" cellspacing="0" cellpadding="5">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Type</th>
                <th>Description</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Due_date</th>
                <th>Assigned_to</th>
                <th>Created_at</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tasks as $task)
                <tr>
                    <td>{{ $task->id }}</td>
                    <td>{{ $task->title }}</td>
                    <td>{{ $task->type }}</td>
                    <td>{{ $task->description }}</td>
                    <td>{{ $task->status }}</td>
                    <td>{{ $task->priority }}</td>
                    <td>{{ $task->due_date }}</td>
                    <td>{{ $task->assignedTo->name }}</td>
                    <td>{{ $task->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
