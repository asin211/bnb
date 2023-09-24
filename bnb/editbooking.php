<?php
include "header.php";
include "menu.php";
echo '<div id="site_content">';
include "sidebar.php";

checkUser();

$DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

//insert DB code from here onwards
//check if the connection was good
if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
    exit; //stop processing the page further
}


//function to clean input but not validate type and content
function cleanInput($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

//check if id exists
if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $id = $_GET['id'];
    if (empty($id) or !is_numeric($id)) {
        echo "<h5>Invalid Booking id</h5>";
        exit;
    }
}


//on submit check if empty or not string and is submited by POST
if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Update')) {

    $room = cleanInput($_POST['room']);

    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];

    $contactNumber = cleanInput($_POST['contactNumber']);
    $extras = cleanInput($_POST['extra']);
    $reviews = cleanInput($_POST['review']);
    $id = cleanInput($_POST['id']);


    $upd = "UPDATE `booking` SET roomID=?, checkinDate=?, checkoutDate=?, contactNumber=?, extra=?, review=? WHERE bookingID=?";

    $stmt = mysqli_prepare($DBC, $upd); //prepare the query
    mysqli_stmt_bind_param($stmt, 'isssssi', $room, $checkin, $checkout, $contactNumber, $extras, $reviews, $id);

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    //print message
    echo "<h5>Booking updated successfully</h5>";
}

$query = 'SELECT room.roomID, room.roomname, room.roomtype, room.beds, booking.checkinDate, booking.checkoutDate, booking.contactNumber, booking.extra, booking.review FROM `booking` INNER JOIN `room` ON booking.roomID=room.roomID WHERE bookingID=' . $id;


$result = mysqli_query($DBC, $query);
$rowcount = mysqli_num_rows($result);

?>

<script>
    //insert datepicker jQuery

    $(document).ready(function() {
        $("#checkin").datepicker({
            dateFormat: "yy-mm-dd",
            numberOfMonths: 2,
            changeYear: true,
            changeMonth: true,
            showOtherMonths: true,
            minDate: 0, // Minimum date allowed is today
            onSelect: function(selectedDate) {
                // Update minDate of "To" datepicker when "From" date is selected
                $("#checkout").datepicker("option", "minDate", selectedDate);
            },
        });
        // Initialize the "To" datepicker
        $("#checkout").datepicker({
            dateFormat: "yy-mm-dd",
            numberOfMonths: 2,
            changeYear: true,
            changeMonth: true,
            showOtherMonths: true,
            minDate: 0 // Minimum date allowed is today
        });
        // $.datepicker.setDefaults({
        //     dateFormat: 'yy-mm-dd'
        // });

        $(function() {
            checkin = $("#checkin").datepicker()
            checkout = $("#checkout").datepicker()

            function getDate(element) {
                var date;
                try {
                    date = $.datepicker.parseDate(dateFormat, element.value);
                } catch (error) {
                    date = null;
                }
                return date;
            }
        });
    });
</script>

<h1>Edit a booking</h1>
<h2>
    <a href='listbookings.php'>[Return to the Bookings listing]</a>
    <a href="index.php">[Return to main page]</a>
</h2>

<div>
    <div>
        <form method="POST">
            <div>
                <label for="room">Room (name, type, beds):</label>
                <select name="room" id="room" required>
                    <?php
                    if ($rowcount > 0) {
                        $row = mysqli_fetch_assoc($result);
                    ?>

                        <option value="<?php echo $row['roomID']; ?>">
                            <?php echo $row['roomname'] . ', '
                                . $row['roomtype'] . ', '
                                . $row['beds'] ?>
                        </option>

                    <?php
                    } else echo "<option>No room found</option>";
                    ?>
                </select>
            </div>

            <div>
                <input type="hidden" name="id" value="<?php echo $id; ?>">
            </div>

            <br>

            <br>
            <div>
                <label for="checkin">Checkin Date:</label>
                <input type="text" id="checkin" name="checkin" placeholder="2023-10-16" value="<?php echo $row['checkinDate'] ?>" required>
            </div>

            <br>
            <div>
                <label for="checkout">Checkout Date:</label>
                <input type="text" id="checkout" name="checkout" placeholder="2023-10-16" required value="<?php echo $row['checkoutDate'] ?>">
            </div>
            <br>

            <div>
                <label for="contactNumber">Contact Number:</label>
                <input type="tel" id="contactNumber" name="contactNumber" required placeholder="(001) 123 1234" pattern="[\(]\d{3}[\)] \d{3} \d{4}" value="<?php echo $row['contactNumber'] ?>">
            </div>

            <br>
            <div>
                <label for="extras">Booking Extras:</label>
                <textarea id="extra" name="extra" maxlength="100" rows="4" cols="30"><?php echo $row['extra'] ?></textarea>
            </div>

            <br>

            <div>
                <label for="reviews">Room Review:</label>
                <textarea id="review" name="review" maxlength="200" cols="30" rows="4"><?php echo $row['review'] ?></textarea>

            </div>

            <br>
            <div>
                <input type="submit" name="submit" value="Update">
                <a href="listbookings.php">[Cancel]</a>
            </div>

        </form>
        <?php
        mysqli_free_result($result);
        mysqli_close($DBC);
        ?>
        <?php
        echo '</div></div>';
        include "footer.php";
        ?>
    </div>
</div>
<?php
echo '</div></div>';
include "footer.php";
?>