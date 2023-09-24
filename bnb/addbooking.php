<?php
include "header.php";
include "menu.php";
echo '<div id="site_content">';
include "sidebar.php";

/*
 include "ChromePhp.php"; //For Development testing only
*/ 

checkUser();

$DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);
$searchresult = '';
echo "<pre>";

// var_dump($_POST);
// var_dump($_GET);

echo "</pre>";
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

//on submit check if empty or not string and is submited by POST
if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Book')) {

    $room = cleanInput($_POST['rooms']);
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $contactNumber = cleanInput($_POST['contactNumber']);
    $extra = cleanInput($_POST['extra']);
    $id = $_SESSION['userid']; // login session user id to store customerID in new booking 

    $error = 0; //clear our error flag
    $msg = 'Error: ';
    $in = new DateTime($checkin);
    $out = new DateTime($checkout);

    if ($in >= $out) {
        $error++;
        $msg .= "Arrival date cannot be earlier or equal to departure date";
        $checkout = '';
    }

    if ($error == 0) {
        //save the booking data if the error flag is still clean
        $query = "INSERT INTO `booking` (customerID, roomID, checkinDate, checkoutDate, contactNumber, extra) VALUES (?,?,?,?,?,?)";

        $stmt = mysqli_prepare($DBC, $query); //prepare the query
        // mysqli_stmt_bind_param($stmt, 'iissss', $id, $room, $checkin, $checkout, $contactNumber, $extra);
        mysqli_stmt_bind_param($stmt, 'iissss', $id, $room, $checkin, $checkout, $contactNumber, $extra);

        /*
        // Chromephp
        ChromePhp::log($id); //For Development testing only
        ChromePhp::log($room);
        ChromePhp::log($checkin);
        ChromePhp::log($checkout);
        ChromePhp::log($contactNumber);
        ChromePhp::log($extra);
        */

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        //print message
        echo "<h5>Booking made successfully</h5>";
    } else {
        //print error 
        echo "<h5>$msg</h5>" . PHP_EOL;
    }
}

// $query1 = 'SELECT customerID, firstname, lastname, email FROM customer ORDER BY customerID';
// $result1 = mysqli_query($DBC, $query1);
// $rowcount1 = mysqli_num_rows($result1);

$query = 'SELECT roomID, roomname, roomtype, beds FROM room ORDER BY roomID';
$result = mysqli_query($DBC, $query);
$rowcount = mysqli_num_rows($result);

?>

<script>
    //insert datepicker jQuery

    $(document).ready(function() {
        // Initialize the "From" datepicker
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

<script>
    $(document).ready(function() {
        $.datepicker.setDefaults({
            dateFormat: 'yy-mm-dd'
        });
        $("#from_date").datepicker({
            minDate: 0, // Minimum date allowed is today
            onSelect: function(selectedDate) {
                // Update minDate of "To" datepicker when "From" date is selected
                $("#to_date").datepicker("option", "minDate", selectedDate);
            },
        });

        $(function() {
            $("#from_date").datepicker();
            $("#to_date").datepicker();
        });

        $('#search').click(function() {
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();

            if (from_date != '' && to_date != '') {
                $.ajax({
                    url: "bookingsearch.php",
                    method: "POST",
                    data: {
                        from_date: from_date,
                        to_date: to_date
                    },
                    success: function(data) {
                        $('#search_table').html(data);
                    }
                });
            } else {
                alert("Please Select Date");
            }
        });
    });
</script>

<h1>Make a Booking</h1>
<h2>
    <a href='listbookings.php'>[Return to the Booking listing]</a>
    <a href="/bnb/">[Return to main page]</a>
</h2>

<div>
    <div>
        <form method="POST">
            <div>
                <label for="rooms">Room (name, type, beds):</label>
                <select name="rooms" id="rooms" required>
                    <?php
                    if ($rowcount > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $id = $row['flightcode']; ?>

                            <option value="<?php echo $row['roomID']; ?>">
                                <?php echo $row['roomname'] . ', ' . $row['roomtype'] . ', ' . $row['beds'] ?>
                            </option>
                    <?php }
                    } else echo "<option>No Rooms found</option>";
                    mysqli_free_result($result);
                    ?>
                </select>
            </div>

            <br>
            <div>
                <label for="checkin">Checkin date:</label>
                <input type="text" name="checkin" id="checkin" placeholder="yyyy-mm-dd" required>

            </div>
            <br>
            <div>
                <label for="checkout">Checkout Date:</label>
                <input type="text" id="checkout" name="checkout" placeholder="yyyy-mm-dd" required>
            </div>
            <br>
            <div>
                <label for="contactNumber">Contact Number:</label>
                <input type="tel" id="contactNumber" name="contactNumber" required placeholder="(###) ### ####" pattern="[\(]\d{3}[\)] \d{3} \d{4}">
            </div>
            <br>
            <div>
                <label for="extra">Booking Extras:</label>
                <textarea type="text" id="extra" name="extra" maxlength="100" rows="4" cols="30"></textarea>
            </div>
            <br>
            <br>
            <div>
                <input type="submit" name="submit" value="Book">
                <a href="listbookings.php">[Cancel]</a>
            </div>

        </form>
    </div>
</div>
<br><br>

<hr>
<!-- Available Room Search Container -->
<div>
    <h3>Search for Available Rooms</h3>
    <div>
        <form id="searchForm" method="post" name="searching">


            <input type="text" id="from_date" name="sqa" required placeholder="From Date">
            <input type="text" id="to_date" name="sqb" required placeholder="To Date">
            <input type="submit" name="search" id="search" value="Search">
    </div>
    </form>

    <br><br>

    <script>
        $(document).ready(function() {
            $('#searchForm').submit(function(event) {
                var formData = {
                    sqa: $('#from_date').val(),
                    sqb: $('#to_date').val()
                };
                $.ajax({
                    type: "POST",
                    url: "bookingsearch.php",
                    data: formData,
                    dataType: "json",
                    encode: true,

                }).done(function(data) {
                    var tbl = document.getElementById("tblbookings"); //find the table in the HTML  
                    var rowCount = tbl.rows.length;

                    for (var i = 1; i < rowCount; i++) {
                        //delete from the top - row 0 is the table header we keep
                        tbl.deleteRow(1);
                    }

                    //populate the table
                    //data.length is the size of our array

                    for (var i = 0; i < data.length; i++) {
                        var fid = data[i]['roomID'];
                        var fn = data[i]['roomname'];
                        var dl = data[i]['roomtype'];
                        var tl = data[i]['beds'];
                        //create a table row with four cells
                        //Insert new cell(s) with content at the end of a table row 
                        //https://www.w3schools.com/jsref/met_tablerow_insertcell.asp  
                        tr = tbl.insertRow(-1);
                        var tabCell = tr.insertCell(-1);
                        tabCell.innerHTML = fid; //roomID
                        var tabCell = tr.insertCell(-1);
                        tabCell.innerHTML = fn; //room name  
                        var tabCell = tr.insertCell(-1);
                        tabCell.innerHTML = dl; //room type       
                        var tabCell = tr.insertCell(-1);
                        tabCell.innerHTML = tl; //beds          
                    }
                });
                event.preventDefault();
            })
        })
    </script>
    <div class="row">
        <table id="tblbookings" border="1">
            <thead>
                <tr>
                    <th>Room#</th>
                    <th>Room Name</th>
                    <th>Room Type</th>
                    <th>Beds</th>
                </tr>
            </thead>
        </table>
    </div>

    <?php
    mysqli_close($DBC); //close the connection once done  // Displaying Selected Value
    ?>
</div>
<?php
echo '</div></div>';
include "footer.php";
?>