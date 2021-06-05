<?php
// Initialize the session
require_once "config.php";
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}else{
    
    $logged_in_user = $_SESSION["username"];
    $company_termi = $trade_type = $price = $quantity = $trade_company_id = "";
    $company_termi_err = $trade_type_err = $price_err = $trade_company_id_err = $quantity_err = "";
    $errMsg= "";
    
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        try {
            // Validate
            if($_POST["$company_termi"] == 'Select Company'){
                $company_name_err = "Please select company.";
            } else{
                $company_termi = trim($_POST["company_termi"]);
            }
            
            if($_POST["$trade_type"] == 'Select Buy/Sale'){
                $trade_type_err = "Please select trading type.";
            } else{
                $trade_type = trim($_POST["trade_type"]);
            }
            
            
            // Validate
            if(empty(trim($_POST["price"]))){
                $price_err = "Please enter price.";
            } else{
                $price = trim($_POST["price"]);
            }
            
            if(empty(trim($_POST["trade_company_id"]))){
                $trade_company_id_err = "Please select trade company id.";
            } else{
                $trade_company_id = trim($_POST["trade_company_id"]);
            }
            
            // Validate
            if(empty(trim($_POST["quantity"]))){
                $quantity_err = "Please enter quantity.";
            } else{
                $quantity = trim($_POST["quantity"]);
            }
            
            if(!empty($trade_company_id)){
                $trade_company_id = trim($_POST["trade_company_id"]);
                //                     header("location: welcome.php");
            }
            
            if(empty($company_termi_err) && empty($price_err) && empty($quantity_err)){
                
                // Prepare an insert statement
                $sql = "INSERT INTO user_trading (company_id, user_id, trade_type, quantity, price) VALUES (?, ?, ?, ?, ?)";
                $user_id_sql = "SELECT id FROM users WHERE username='".$logged_in_user."';";
                $res = mysqli_query($link, $user_id_sql);
                $row = mysqli_fetch_array($res);
                $user_id = $row[0];
                
                
                
                if($stmt = mysqli_prepare($link, $sql)){
                    // Bind variables to the prepared statement as parameters
                    mysqli_stmt_bind_param($stmt, "sssss", $param_company_id, $param_user_id, $param_trade_type, $param_quantity, $param_price);
                    
                    // Set parameters
                    $param_company_id = $company_termi;
                    $param_user_id = $user_id;
                    $param_trade_type = $trade_type;
                    $param_quantity = $quantity;
                    $param_price = $price;
                    
                    // Attempt to execute the prepared statement
                    if(mysqli_stmt_execute($stmt)){
                        // Redirect to login page
                        $_SESSION['success_message'] = "Trading details saved successfully.";
                        header("location: welcome.php");
                    } else{
                        echo "Something went wrong. Please try again later.";
                    }
                    
                    // Close statement
                    mysqli_stmt_close($stmt);
                }
            }
        }catch(Exception $e) {
            echo 'Message: ' .$e->getMessage();
            $errMsg = $e->getMessage();
        }
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
        .wrapper{ width: 350px; padding: 20px; }
        .wrapper-left{ width: 350px; padding: 20px; }
        .wrapper-right{ width: 750px; padding: 20px; }
    </style>
    <style type="text/css">
                
        body {
          font-family: Arial;
          background-color: #f5f5f5;
          color: #808080;
          text-align: center;	
        }
        
        .header {
          overflow: hidden;
          background-color: #93b4cc;
          padding-bottom: 5px;
        }
        
        /*-=-=-=-=-=-=-=-=-=-=-=- */
        /* Column Grids */
        /*-=-=-=-=-=-=-=-=-=-=-=- */
        
        .col_half { width: 49%; }
        .col_third { width: 32%; }
        .col_fourth { width: 23.5%; }
        .col_fifth { width: 18.4%; }
        .col_sixth { width: 15%; }
        .col_three_fourth { width: 74.5%;}
        .col_twothird{ width: 66%;}
        .col_half,
        .col_third,
        .col_twothird,
        .col_fourth,
        .col_three_fourth,
        .col_fifth{
        	position: relative;
        	display:inline;
        	display: inline-block;
        	float: left;
        	margin-right: 9%;
        	margin-bottom: 20px;
        }
        .end { margin-right: 0 !important; }
        /* Column Grids End */
        
        .mainwrapper { width: 980px; margin: 30px auto; position: relative;}
        .counter { background-color: #ffffff; padding: 20px 0; border-radius: 5px;}
        .count-title { font-size: 40px; font-weight: normal;  margin-top: 10px; margin-bottom: 0; text-align: center; }
        .count-text { font-size: 13px; font-weight: normal;  margin-top: 10px; margin-bottom: 0; text-align: center; }
        .fa-2x { margin: 0 auto; float: none; display: table; color: #4ad1e5; }
        
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
        
        #trade tr:nth-child(even){background-color: #fffefe;}
        
        #trade tr:hover {background-color: #ddd;}
        
        #trade th {
          padding-top: 12px;
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
    	<div class="header" style="width: 100%; height: 20%; overflow: hidden;">
    		<div style="float:left; height: 80%; padding-left:50px; padding-top:10px">
    			<h4>Welcome <?php echo $_SESSION["username"] ?>...!</h4>
    			<p><?php echo $user_id ?></p>
    		</div>
    		<div style="float:right; padding-right:50px; padding-top:10px">
    			<a href="logout.php" class="btn btn-primary">Logout</a>
    		</div>
    	</div>
    	<div class="display" style="width: 100%; height: 30%; overflow: hidden;">
            <div class="mainwrapper">
                <div class="counter col_fourth">
                  <img src='https://www.clipartkey.com/mpngs/m/47-472676_indian-money-logo-png-transparent-images-indian-rupee.png' width='80' height='80'>
                  <?php 
                  $user_id_sql = "SELECT id FROM users WHERE username='".$logged_in_user."';";
                  $res = mysqli_query($link, $user_id_sql);
                  $row = mysqli_fetch_array($res);
                  $user_id = $row[0];
                  $total_price_sql = "select sum(quantity*price) from user_trading where user_id=". $user_id ." and trade_type='buy';";
                  $res = mysqli_query($link, $total_price_sql);
                  $row = mysqli_fetch_array($res);
                  $total_price_buy = $row[0];
                  $total_price_sql = "select sum(quantity*price) from user_trading where user_id=". $user_id ." and trade_type='sale';";
                  $res = mysqli_query($link, $total_price_sql);
                  $row = mysqli_fetch_array($res);
                  $total_price_sale = $row[0];
                  if(empty($total_price_buy)){
                      $total_price = 0;
                  } else{
                      $total_price = $total_price_buy-$total_price_sale;
                  }
                  $stmt = "select distinct(company_id) from user_trading where user_id = ". $user_id .";";
                  $result = mysqli_query($link, $stmt);
                  $curr_price = 0;
                  while($row = mysqli_fetch_array($result))
                  {
                      $curr_price_sql = "select curr_price from company_details where id = ". $row[0] .";";
                      $res = mysqli_query($link, $curr_price_sql);
                      $row1 = mysqli_fetch_array($res);
                      $curr_price_res = $row1[0];
                      $total_quantitis_sql = "select sum(quantity) from user_trading where trade_type ='buy' and user_id=". $user_id ." and company_id = ". $row[0] .";";
                      $res = mysqli_query($link, $total_quantitis_sql);
                      $row2 = mysqli_fetch_array($res);
                      $no_of_shares_buy = $row2[0];
                      $total_quantitis_sql = "select sum(quantity) from user_trading where trade_type ='sale' and user_id=". $user_id ." and company_id = ". $row[0] .";";
                      $res = mysqli_query($link, $total_quantitis_sql);
                      $row2 = mysqli_fetch_array($res);
                      $no_of_shares_sale = $row2[0];
                      $curr_price = $curr_price + $curr_price_res * ($no_of_shares_buy-$no_of_shares_sale);
                      
                  }
                  $diff_amt = $curr_price - $total_price;
                  echo "<h2 class='timer count-title count-number' data-to='300 data-speed='1500'>". $total_price ."</h2>";
                  ?>
                   <p class="count-text ">Total Amount Invested</p>
                </div>
            
                <div class="counter col_fourth">
                  <img src='https://banner2.cleanpng.com/20180406/aow/kisspng-computer-icons-money-finance-currency-symbol-services-5ac76953624606.3842125515230180674025.jpg' width='80' height='80'>
                  <?php 
                  echo "<h2 class='timer count-title count-number' data-to='300 data-speed='1500'>". $curr_price ."</h2>";
                  ?>
                  <p class="count-text ">Current Value</p>
                </div>
            
                <div class="counter col_fourth">
                  <img src='https://cdn.clipart.email/047d28eb456f02f5fe1789c20a64c139_pattern-background-clipart-market-text-orange-transparent-_900-520.jpeg' width='100' height='80'>
                  <h2 class="timer count-title count-number" data-to="11900" data-speed="1500"><?php echo round($diff_amt,2) ?></h2>
                  <p class="count-text ">Profit/Loss</p>
                </div>
            </div>
    	</div>
    	<div style="width: 100%; height: 50%; overflow: hidden;">
    		<div style="float:left; width: 30%; height: 100%; overflow: hidden;">
    			<center>
                    <div class="wrapper-left">
                        <h2>Add Investment Details</h2>
                        <p>Please fill this form to add investment details.</p>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        	<div class="form-group <?php echo (!empty($company_termi_err)) ? 'has-error' : ''; ?>">
                        		<select id="company" name="company_termi" class="form-control">
                        			  <option value="none" selected disabled hidden>Select Company</option>
                        			  <?php
                        			  $stmt = "select id, company_termi from company_details;";
                        			  $result = mysqli_query($link, $stmt);
                        			  while($row = mysqli_fetch_array($result))
                        			  {
                        			      echo "<option value=". $row['id'] .">". $row['company_termi'] ."</option>";
                        			  }
                        			  ?>
                                </select>
                                <span class="help-block"><?php echo $company_termi_err; ?></span>
                            </div>  
                            <div class="form-group <?php echo (!empty($trade_type_err)) ? 'has-error' : ''; ?>">
                        		<select id="trade_type" name="trade_type" class="form-control">
                        			  <option value="none" selected disabled hidden>Select Buy/Sale</option>
                        			  <option value="buy">Buy</option>
                                      <option value="sale">Sale</option>
                                </select>
                                <span class="help-block"><?php echo $trade_type_err; ?></span>
                            </div>
                            <div class="form-group <?php echo (!empty($price_err)) ? 'has-error' : ''; ?>">
                                <input type="text" name="price" placeholder="Price" class="form-control" value="<?php echo $confirm_password; ?>">
                                <span class="help-block"><?php echo $price_err; ?></span>
                            </div>
                            <div class="form-group <?php echo (!empty($quantity_err)) ? 'has-error' : ''; ?>">
                                <input type="text" name="quantity" placeholder="Quantities" class="form-control" value="<?php echo $confirm_password; ?>">
                                <span class="help-block"><?php echo $quantity_err; ?></span>
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
    		<div style="float:right; width: 70%; height: 100%; overflow: hidden;">
    			<center>
                    <div class="wrapper-right">
                        <h2>Trade Details</h2>
                        <div class="form-group">
                        	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            	<div style="float:left; width:75%;margin-bottom:15px;">
                            		<select id="trade_company_id" name="trade_company_id" class="form-control">
                            			  <option value="none" selected disabled hidden>Select Company</option>
                            			  <?php
                                			  $stmt = "select distinct(company_id) from user_trading where user_id=". $user_id .";";
                                			  $result = mysqli_query($link, $stmt);
                                			  while($row = mysqli_fetch_array($result))
                                			  {
                                			      $cmpy_stmt = "select id,company_termi from company_details where id=".$row['company_id'].";";
                                			      $res = mysqli_query($link, $cmpy_stmt);
                                			      while($row_data = mysqli_fetch_array($res))
                                			      {
                                			          echo "<option value=". $row_data['id'] .">". $row_data['company_termi'] ."</option>";
                                			      }
                                			  }
                                			  echo "<option value=0>All</option>";
                            			  ?>
                                    </select>
                                </div>
                                <div class="form-group" style="float:right; width:20%;">
                                    <input type="submit" class="btn btn-primary" value="Submit">
                                </div>
                            </form>
                        </div>
                        <?php
                            $user_id_sql = "SELECT id FROM users WHERE username='".$logged_in_user."';";
                            $res = mysqli_query($link, $user_id_sql);
                            $row = mysqli_fetch_array($res);
                            $user_id = $row[0];
                            $cmpy_id = $_POST['trade_company_id'];
                            $cmpy_name_sql = "SELECT company_name FROM company_details WHERE id='".$cmpy_id."';";
                            $res1 = mysqli_query($link, $cmpy_name_sql);
                            $row1 = mysqli_fetch_array($res1);
                            $cmpy_name = $row1[0];
                            if($cmpy_id==0){
                                $stmt = "select * from user_trading where user_id=".$user_id.";";
                                $result = mysqli_query($link, $stmt);
                            }else{
                                $cmpy_name_sql = "SELECT company_name FROM company_details WHERE id='". $cmpy_id ."';";
                                $res1 = mysqli_query($link, $cmpy_name_sql);
                                $row1 = mysqli_fetch_array($res1);
                                $cmpy_name = $row1[0];
                                
                                $stmt = "select sum(quantity) from user_trading where user_id=".$user_id." and company_id=".$cmpy_id." and trade_type='buy';";
                                $result = mysqli_query($link, $stmt);
                                $row = mysqli_fetch_array($result);
                                $qunatity_buy = $row[0];
                                $stmt = "select sum(quantity*price) from user_trading where user_id=".$user_id." and company_id=".$cmpy_id." and trade_type='buy';";
                                $result = mysqli_query($link, $stmt);
                                $row = mysqli_fetch_array($result);
                                $price_buy = $row[0]/$qunatity_buy;
                                
                                $stmt = "select sum(quantity) from user_trading where user_id=".$user_id." and company_id=".$cmpy_id." and trade_type='sale';";
                                $result = mysqli_query($link, $stmt);
                                $row = mysqli_fetch_array($result);
                                $qunatity_sale = $row[0];
                                $stmt = "select avg(price) from user_trading where user_id=".$user_id." and company_id=".$cmpy_id." and trade_type='sale';";
                                $result = mysqli_query($link, $stmt);
                                $row = mysqli_fetch_array($result);
                                $price_sale = $row[0];
                                
                                $curr_price_sql = "select curr_price from company_details where id = ". $cmpy_id .";";
                                $res2 = mysqli_query($link, $curr_price_sql);
                                $row2 = mysqli_fetch_array($res2);
                                $curr_price_res = $row2[0];
                                
                                
                                $cmpy_curr_quantities = $qunatity_buy - $qunatity_sale;
                                $cmpy_total_price = $cmpy_curr_quantities*$price_buy;
                                $cmpy_curr_price = $cmpy_curr_quantities*$curr_price_res;
                                $diff = $cmpy_curr_price - $cmpy_total_price;
                                
                                echo "<h4>Company Investment Details</h4>";
                                echo "<table id=trade >
                                <tr>
                                <th>Company</th>
                                <th>No.of Shares</th>
                                <th>Shares Bought At(Avg Price)</th>
                                <th>Amount Invested</th>
                                <th>Current Share Value</th>
                                <th>Current Worth</th>
                                <th>Profit/Loss</th>
                                </tr>";
                                echo "<tr>";
                                echo "<td>" . $cmpy_name . "</td>";
                                echo "<td>" . $cmpy_curr_quantities . "</td>";
                                echo "<td>" . round($price_buy,2) . "</td>";
                                echo "<td>" . $cmpy_total_price . "</td>";
                                echo "<td>" . $curr_price_res . "</td>";
                                echo "<td>" . $cmpy_curr_price . "</td>";
                                echo "<td>" . $diff . "</td>";
                                echo "</tr>";
                                echo "</table>";
                                $stmt = "select * from user_trading where user_id=".$user_id." and company_id=".$cmpy_id.";";
                            }
                            echo "<h4>Company Trading Sheet</h4>";
                            echo "<table id ='trade'>
                            <tr>
                            <th>Company</th>
                            <th>Trade Type</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            </tr>";
                            $cmpy_total_price = 0;
                            $cmpy_curr_price = 0;
                            $result = mysqli_query($link, $stmt);
                            while($row = mysqli_fetch_array($result))
                            {
                                $cmpy_name_sql = "SELECT company_name FROM company_details WHERE id='". $row['company_id'] ."';";
                                $res1 = mysqli_query($link, $cmpy_name_sql);
                                $row1 = mysqli_fetch_array($res1);
                                $cmpy_name = $row1[0];
                                echo "<tr>";
                                echo "<td>" . $cmpy_name . "</td>";
                                echo "<td>" . $row['trade_type'] . "</td>";
                                echo "<td>" . $row['price'] . "</td>";
                                echo "<td>" . $row['quantity'] . "</td>";
                                echo "</tr>";
                                $curr_price_sql = "select curr_price from company_details where id = ". $row['company_id'] .";";
                                $res2 = mysqli_query($link, $curr_price_sql);
                                $row2 = mysqli_fetch_array($res2);
                                $curr_price_res = $row2[0];
                                $cmpy_curr_price = $cmpy_curr_price + $row['quantity'] * $curr_price_res; 
                                $cmpy_total_price = $cmpy_total_price + $row['quantity']*$row['price'];
                            }
                            echo "</table>";
                        ?>
                    </div>  
                </center> 
    		</div>
    	</div>
    </div>