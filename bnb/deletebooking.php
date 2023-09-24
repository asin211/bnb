<?php
include "header.php";
include "menu.php";
echo '<div id="site_content">';
include "sidebar.php";

checkUser();

$DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

if (mysqli_connect_errno()) {
    echo "Error:Unable to connect to MySql." . mysqli_connect_error();
    exit; //stop processing the page further.
}

function cleanInput($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

//check if id exists
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET['id'];
    if (empty($id) or !is_numeric($id)) {
        echo "<h2>Invalid Booking id</h2>";
        exit;
    }
}

if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Delete')) {
    $error = 0;
    $msg = "Error:";

    //we try to convert to number - intval function(return to the integer of a variable)
    if (isset($_POST['id']) and !empty($_POST['id']) and is_integer(intval($_POST['id']))) {
        $id = CleanInput($_POST['id']);
    } else {
        $error++;
        $msg .= 'Invalid Booking ID';
        $id = 0;
    }
    if ($error == 0 and $id > 0) {
        $query = "DELETE FROM booking WHERE bookingID=?";
        $stmt = mysqli_prepare($DBC, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        echo "<h5>Booking deleted.</h5>";
    } else {
        echo "<h5>$msg</h5>" . PHP_EOL;
    }
}

$query = 'SELECT room.roomID, room.roomname, room.roomtype, room.beds, booking.bookingID, booking.checkinDate, booking.checkoutDate, booking.contactNumber, booking.extra, booking.review FROM `booking` INNER JOIN `room` ON booking.roomID=room.roomID WHERE bookingID=' . $id;


$result = mysqli_query($DBC, $query);
$rowcount = mysqli_num_rows($result);
?>

<!-- We can add a menu bar here to go back -->
<h1>Booking Preview before Delete</h1>
<h2><a href="listbookings.php">[Return to the Booking listing]</a>
    <a href="/bnb/index.php">[Return to the main page]</a>
</h2>
<div>

</div>
<?php
if ($rowcount > 0) {

    echo "<fieldset><legend>Booking Detail #$id</legend><dl>";
    $row = mysqli_fetch_assoc($result);
    $id = $row['bookingID'];

    echo "<dt>Room name: </dt><dd>" . $row['roomname'] . "</dd>" . PHP_EOL;
    echo "<dt>Checkin Date: </dt><dd>" . $row['checkinDate'] . "</dd>" . PHP_EOL;
    echo "<dt>Checkout Date: </dt><dd>" . $row['checkoutDate'] . "</dd>" . PHP_EOL;
?><form method="POST" action="deletebooking.php">

        <h4>Are you sure you want to delete this Booking?</h4>
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <input type="submit" name="submit" value="Delete">
        <a href="listbookings.php">Cancel</a>
    </form>
<?php
} else echo "<h5>No Booking found! Possibly deleted!</h5>";
mysqli_free_result($result);
mysqli_close($DBC);
?>
<?php
echo '</div></div>';
include "footer.php";
?>