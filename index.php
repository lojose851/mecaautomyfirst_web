<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Form</title>
</head>
<body>
    <h1>Contact Form</h1>
    <form action="procesar.php" method="post">
        <input type="text" name="name" value="" placeholder="Name" required>
        <input type="email" name="email" value="" placeholder="Email" required>
        <input type="text" name="subject" value="" placeholder="Subject" required>
        <textarea name="body" rows="8" cols="40" placeholder="Body" required></textarea>
        <button type="submit">Send</button>
    </form>
</body>
</html>
