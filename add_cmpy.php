<?php
// Include config file
require_once "config.php";
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}else{
    $logged_in_user = $_SESSION["username"];

// Define variables and initialize with empty values
$company_name = $company_code = $company_logo = "";
$company_name_err = $company_code_err = $company_logo_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // Validate company name
    if(empty(trim($_POST["company_code"]))){
        $username_err = "Please enter a company code.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM company_details WHERE company_code = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_company_code);
            
            // Set parameters
            $param_company_code = trim($_POST["company_code"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $company_name_err = "This company details are already taken.";
                } else{
                    $company_name = trim($_POST["company_name"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            
            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate company name
    if(empty(trim($_POST["company_name"]))){
        $company_name_err = "Please enter a company name.";
    } else{
        $company_name = trim($_POST["company_name"]);
    }
    
    // Validate company code
    if(empty(trim($_POST["company_code"]))){
        $company_code_err = "Please enter a company code.";
    } else{
        $company_code = trim($_POST["company_code"]);
    }
    
    // Validate company logo
    if(empty(trim($_POST["company_logo"]))){
        $company_logo_err = "Please enter a company logo url.";
    } else{
        $company_logo = trim($_POST["company_logo"]);
    }
    
    // Check input errors before inserting in database
    if(empty($company_name_err) && empty($company_code_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO company_details (company_name, company_code, company_termi, company_logo) VALUES (?, ?, ?, ?)";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssss", $param_company_name, $param_company_code, $param_company_termi, $param_company_logo);
            
            // Set parameters
            $param_company_name = $company_name;
            $param_company_code = $company_code;
            $param_company_logo = $company_logo;
            $param_company_termi = $company_name."/".$company_code;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                $_SESSION['success_message'] = "Company Details saved successfully.";
                header("location: add_cmpy.php");
            } else{
                echo "Something went wrong. Please try again later.";
            }
            
            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper-left{ width: 350px; padding: 20px; padding-top: 150px; }
        .wrapper-right{ width: 750px; padding: 20px; padding-top: 150px; }
        
        .header {
          overflow: hidden;
          background-color: #93b4cc;
          padding-bottom: 5px;
        }
        
        #trade {
          font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
          border-collapse: collapse;
          width: 100%;
        }
        
        #trade td, #trade th {
          border: 1px solid #ddd;
          padding: 8px;
          text-align: center;
        }
        
        #trade tr:nth-child(even){background-color: #f2f2f3;}
        
        #trade tr:hover {background-color: #ddd;}
        
        #trade th {
          padding-bottom: 12px;
          text-align: left;
          background-color: #93b4cc;
          color: white;
          text-align: center;
        }   
    </style>
</head>
<body>
    <div style="width: 100%; height: 100%; overflow: hidden;">
    	<div class="header"  style="width: 100%; height: 20%; overflow: hidden;">
    		<div style="float:left; height: 80%; padding-left:50px; padding-top:10px">
    			<h4>Welcome <?php echo $_SESSION["username"] ?>...!</h4>
    		</div>
    		<div style="float:right; padding-right:50px; padding-top:10px">
    			<a href="logout.php" class="btn btn-primary">Logout</a>
    		</div>
    	</div>
    	<div style="width: 100%;">
            <div style="float:left; width: 30%; padding: 50px;">
        		<center>
                    <div class="wrapper">
                        <h2>Add Company Details</h2>
                        <p>Please fill this form to add company details.</p>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="form-group <?php echo (!empty($company_name_err)) ? 'has-error' : ''; ?>">
                                <input type="text" name="company_name" placeholder="Company Name" class="form-control" value="<?php echo $username; ?>">
                                <span class="help-block"><?php echo $company_name_err; ?></span>
                            </div>    
                            <div class="form-group <?php echo (!empty($company_code_err)) ? 'has-error' : ''; ?>">
                                <input type="text" name="company_code" placeholder="Company Code(yahoo finance code)" class="form-control" value="<?php echo $password; ?>">
                                <span class="help-block"><?php echo $company_code_err; ?></span>
                            </div>
                            <div class="form-group <?php echo (!empty($company_logo_err)) ? 'has-error' : ''; ?>">
                                <input type="text" name="company_logo" placeholder="Company Logo URL" class="form-control" value="<?php echo $confirm_password; ?>">
                                <span class="help-block"><?php echo $company_logo_err; ?></span>
                            </div>
                            <div><?php echo $_SESSION['success_message'] ?></div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary" value="Submit">
                                <input type="reset" class="btn btn-default" value="Reset">
                            </div>
                        </form>
                    </div>  
                </center> 
            </div>
            <div style="float:right; width: 70%; padding: 50px;">
            	<center>
                    <div class="wrapper">
                        <h2>Company Details</h2>
                        <?php
                            $stmt = "select * from company_details;";
                            $result = mysqli_query($link, $stmt);
                            echo "<table id='trade'>
                            <tr>
                            <th>Company Logo</th>
                            <th>Company Name</th>
                            <th>Company Code</th>
                            </tr>";
                            while($row = mysqli_fetch_array($result))
                            {
                                echo "<tr>";
                                echo "<td><center><img src='" . $row['company_logo'] . "' width='30' height='30'></center></td>";
                                echo "<td>" . $row['company_name'] . "</td>";
                                echo "<td>" . $row['company_code'] . "</td>";
                                echo "</tr>";
                            }
                            echo "</table>";
                        ?>
                    </div>  
                </center> 
            </div>
        </div>
    </div> 
</body>
</html>