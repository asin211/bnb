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

    //check if id exists
    if ($_SERVER["REQUEST_METHOD"] == "GET") {

        $id = $_GET['id'];
        if (empty($id) or !is_numeric($id)) {
            echo "<h2>Invalid ticket id</h2>";
            exit;
        }
    }

    $query = 'SELECT room.roomID, room.roomname, room.roomtype, room.beds, booking.checkinDate, booking.checkoutDate, booking.contactNumber, booking.extra, booking.review FROM `booking` INNER JOIN `room` ON booking.roomID=room.roomID WHERE bookingID=' . $id;

    $result = mysqli_query($DBC, $query);
    $rowcount = mysqli_num_rows($result);
    ?>

    <!-- We can add a menu bar here to go back -->
    <h1>Booking Details View</h1>
    <h2><a href="listbookings.php">[Return to the Booking listing]</a>
        <a href="/bnb/index.php">[Return to the main page]</a>
    </h2>
    <?php
    if ($rowcount > 0) {
        echo "<fieldset><legend>Booking Detail #$id</legend><dl>";
        $row = mysqli_fetch_assoc($result);

        echo "<dt>Room name: </dt><dd>" . $row['roomname'] . "</dd>" . PHP_EOL;
        echo "<dt>Checkin Date: </dt><dd>" . $row['checkinDate'] . "</dd>" . PHP_EOL;
        echo "<dt>Checkout Date: </dt><dd>" . $row['checkoutDate'] . "</dd>" . PHP_EOL;

        echo "<dt>Contact Number: </dt><dd>" . $row['contactNumber'] . "</dd>" . PHP_EOL;
        echo "<dt>Extras: </dt><dd>" . $row['extra'] . "</dd>" . PHP_EOL;
        echo "<dt>Room Review: </dt><dd>" . $row['review'] . "</dd>" . PHP_EOL;
        echo '</dl></fieldset>' . PHP_EOL;
    } else echo "<h5>No Booking found! Possbily deleted!</h5>";
    mysqli_free_result($result);
    mysqli_close($DBC);
    ?>
    <?php
    echo '</div></div>';
    include "footer.php";
    ?>