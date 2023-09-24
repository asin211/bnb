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
        echo "<h2>Invalid ticket id</h2>";
        exit;
    }
}


//on submit check if empty or not string and is submited by POST
if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Update')) {

    $review = cleanInput($_POST['review']);
    $id = cleanInput($_POST['id']);


    $upd = "UPDATE `booking` SET review=? WHERE bookingID=?";

    $stmt = mysqli_prepare($DBC, $upd); //prepare the query
    mysqli_stmt_bind_param($stmt, 'si', $review, $id);

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    //print message
    echo "<h5>Review updated </h5>";
}


$query = 'SELECT  review FROM `booking` WHERE bookingID=' . $id;


$result = mysqli_query($DBC, $query);
$rowcount = mysqli_num_rows($result);

?>
<h1>Add / Edit Room Review</h1>
<h2>
    <a href='listbookings.php'>[Return to the Booking listing]</a>
    <a href="index.php">[Return to main page]</a>
</h2>

<div>
    <div>
        <form method="POST">

            <div>
                <input type="hidden" name="id" value="<?php echo $id; ?>">
            </div>
            <?php
            if ($rowcount > 0) {
                $row = mysqli_fetch_assoc($result);
            ?>
                <div>
                    <label for="review">Review Options:</label>
                    <textarea type="text" id="review" name="review" maxlength="200" rows="4" cols="30"><?php echo $row['review'] ?></textarea>
                </div>


            <?php
            } else echo "<h5>No Booking found!</h5>"
            ?>
            <br> <br>

            <div>
                <input type="submit" name="submit" value="Update">
            </div>

        </form>
        <?php
        mysqli_free_result($result);
        mysqli_close($DBC);
        ?>
    </div>
</div>
<?php
echo '</div></div>';
include "footer.php";
?>