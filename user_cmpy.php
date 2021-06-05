<?php
// Include config file
require_once "config.php";

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
        $sql = "INSERT INTO company_details (company_name, company_code, company_logo) VALUES (?, ?, ?)";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sss", $param_company_name, $param_company_code, $param_company_logo);
            
            // Set parameters
            $param_company_name = $company_name;
            $param_company_code = $company_code;
            $param_company_logo = $company_logo;
            
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
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; padding-top: 150px; }
    </style>
</head>
<body>
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
                    <input type="text" name="company_code" placeholder="Company Code" class="form-control" value="<?php echo $password; ?>">
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
</body>
</html>