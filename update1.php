<html>
<head>
<title>Police Emergency Service System</title>
<link href="header_style.css" rel="stylesheet" type="text/css">
<link href="content_style.css" rel="stylesheet" type="text/css">
</head>
<body>
<!-- Part 1 -->
<?php require_once 'nav.php'; ?>
<br><br>
<?php
if (!isset($_POST["btnSearch"]))
{
?>
<!--  create a form to search for patrol car based on id -->
<form name="form1" method="post"
	action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?> ">
	<table class="ContentStyle">
		<tr></tr>
		<tr>
			<td>Patrol Car ID :</td>
			<td><input type="text" name="patrolCarId" id="patrolCarId"></td>
			<!--  must validate for no empty entry -->
			<td><input type="submit" name="btnSearch" id="btnSearch" value="Search"></td>
		</tr>
	</table>
</form>
<?php
}
else 
// Part 2 /////////////////////////////////////////////
{ // post back here after clicking the btnSearch button
	require_once 'db.php';

	// create database connection
	$mysqli = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
	// Check connection
	if ($mysqli->connect_errno) {
	    die("Failed to connect to MySQL: ".$mysqli->connect_errno);
	}

	// retrieve patrol car detail
	$sql = "SELECT * FROM patrolcar WHERE patrolcar_id = ?";

	if (!($stmt = $mysqli->prepare($sql))){
		die("Prepare failed: ".$mysqli->errno);
	}
	
	if (!$stmt->bind_param('s', $_POST['patrolCarId'])){
		die("Binding parameters failed: ".$stmt->errno);
	}

	if (!$stmt->execute()) {
		die("Execute failed failed: ".$stmt->errno);
	}

	if (!($resultset = $stmt->get_result())) {
		die("Getting result set failed: ".$stmt->errno);
	}
	
	// if the patrol car does not exist, redirect back to update.php
	if  ($resultset->num_rows == 0) {
		?>
			<script type="text/javascript">window.location="./update1.php";</script>
		<?php } 
	
	// else if the patrol car found
	$patrolCarId;
	$patrolCarStatusId;
	
	while ($row = $resultset->fetch_assoc()) {
		$patrolCarId = $row['patrolcar_id'];
		$patrolCarStatusId = $row['patrolcar_status_id'];
	}

	// retrieve from patrolcar_status table for populating the combo box
	$sql = "SELECT * FROM patrolcar_status";
	if (!($stmt = $mysqli->prepare($sql))) {
		die("Prepare failed: ".$mysqli->errno);
	}
	
	if (!$stmt->execute()) {
		die("Execute failed: ".$stmt->errno);
	}
	
	if (!($resultset = $stmt->get_result())) {
		die("Getting result set failed: ".$stmt->errno);
	}
	
	$patrolCarStatusArray;;	// an array variable
	
	while ($row = $resultset->fetch_assoc()) {
		$patrolCarStatusArray[$row['patrolcar_status_id']] = $row['patrolcar_status_desc'];
	}
	
	$stmt->close();
	
	$resultset->close();
	
	$mysqli->close();

?>

<!-- display a form for operator to update status of patrol car --> 
<form name="form2" method="post"
	action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?> ">

	<table class="ContentStyle">
		<tr></tr>
		<tr>
			<td>ID :</td>
			<td><?php echo $patrolCarId ?>
				<input type="hidden" name="patrolCarId" id="patrolCarId"
				value="<?php echo $patrolCarId ?>">
			</td>
		</tr>
		<tr>
			<td>Status :</td>
			<td><select name="patrolCarStatus" id="patrolCarStatus">
				<?php foreach( $patrolCarStatusArray as $key => $value){ ?>
				<option value="<?php echo $key ?>"
					<?php if ($key==$patrolCarStatusId) {?> selected="selected"
					<?php }?>
				>
					<?php echo $value ?>
				</option>
				<?php } ?>
			</select></td>
		</tr>
		<tr>
			<td><input type="reset"
				name="btnCancel" id="btnCancel" value="Reset"></td>
			<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input
				type="submit" name="btnUpdate" id="btnUpdate" value="Update">
			</td>
		</tr>
	</table>
</form>

<?php } ?>

</html>