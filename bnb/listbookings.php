<?php
include "header.php";
include "menu.php";
echo '<div id="site_content">';
include "sidebar.php";

checkUser();

$DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

if (mysqli_connect_errno()) {
    echo "Error:unable to connect to Mysql." . mysqli_connect_error();
    exit; //stop processing the page further
}


//prepare a query and send it to the server
$query = 'SELECT bookingID, customerID, roomID, checkinDate, checkoutDate FROM booking ORDER BY checkinDate';
$result = mysqli_query($DBC, $query);
$rowcount = mysqli_num_rows($result);
?>

<h1>Current bookings</h1>
<h2><a href="addbooking.php">[Make a booking]</a><a href="/bnb/">[Return to main page]</a></h2>

<table border="1">
    <thead>
        <tr>
            <th>Booking (room, dates)</th>
            <th>Customer</th>
            <th>Action</th>
        </tr>
    </thead>

    <?php
    if ($rowcount > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $id = $row['bookingID'];
            $customer_id = $row['customerID'];
            $room_id = $row['roomID'];

            //https://www.php.net/manual/en/language.operators.execution.php
            $sql_room_query = 'SELECT roomname FROM `room` WHERE roomID=' . $room_id;
            $sql_customer_query = 'SELECT firstname, lastname FROM `customer` WHERE customerID=' . $customer_id;
            $res_room = mysqli_query($DBC, $sql_room_query);
            $row_room = mysqli_num_rows($res_room);

            $res_customer = mysqli_query($DBC, $sql_customer_query);
            $row_customer = mysqli_num_rows($res_customer);

            if ($row_room > 0) {
                $row_room_data = mysqli_fetch_assoc($res_room);
            }

            if ($row_customer > 0) {
                $row_customer_data = mysqli_fetch_assoc($res_customer);
            }

            echo '<tr><td>' . $row_room_data['roomname'] . ', ' . $row['checkinDate']
                . ', ' . $row['checkoutDate'] . '</td>';
            echo '<td>' . $row_customer_data['lastname'] . ', ' . $row_customer_data['firstname'] . '</td>';
            echo     '<td><a href="viewbooking.php?id=' . $id . '">[view] </a>';
            echo         '<a href="editbooking.php?id=' . $id . '">[edit] </a>';
            echo         '<a href="addreview.php?id=' . $id . '">[manage reviews] </a>';
            echo         '<a href="deletebooking.php?id=' . $id . '">[delete] </a></td>';
            echo '</tr>' . PHP_EOL;
            mysqli_free_result($res_room); //free any memory used by the query
            mysqli_free_result($res_customer);
        }
    } else echo "<h2>No Bookings found!</h2>";

    mysqli_free_result($result);
    mysqli_close($DBC);

    ?>

</table>

<?php
echo '</div></div>';
include "footer.php";
?>