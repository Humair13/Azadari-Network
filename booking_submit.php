<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "azadari_db"; // replace with your DB name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$name = $_POST['name'];
$contact = $_POST['contact'];
$date = $_POST['booking_date'];
$place = $_POST['place'];

$sql = "INSERT INTO bookings (name, contact, booking_date, place)
        VALUES ('$name', '$contact', '$date', '$place')";

if ($conn->query($sql) === TRUE) {
    // --- Send SMS using API ---
    $apiKey = "YOUR_FAST2SMS_API_KEY";  // 🔹 Get from fast2sms.com
    $message = "Dear $name, your booking for $place on $date has been received. - Azadari Network";
    $fields = array(
        "sender_id" => "FSTSMS",
        "message" => $message,
        "language" => "english",
        "route" => "v3",
        "numbers" => $contact,
    );

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://www.fast2sms.com/dev/bulkV2",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($fields),
        CURLOPT_HTTPHEADER => array(
            "authorization: $apiKey",
            "accept: */*",
            "cache-control: no-cache",
            "content-type: application/json"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    echo "<script>alert('Booking successful! Confirmation SMS sent.'); window.location.href='booking.html';</script>";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
