<?php

session_start();

$dsn = "mysql:host=localhost;dbname=iti";
$user = 'root';
$password = '';

$options = array(
	PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
);


try {

	$con = new PDO($dsn, $user, $password, $options);
	$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	//echo "connected";
	
	
} catch (Exception $e) {
	
	echo "Failed To Connect" . $e->getMessage;
}

?>

<html>
	<head>
        <title>Users</title>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	</head>
	<body>

<?php

if (isset($_GET['do'])) {
        $do = $_GET['do'];
    }else{
        $do = 'manage';
    }

     if($do == 'add') {?>
		<div class="edit">
            <h1 class="text-center">Add New User</h1>

            <div class="container">

                <form class="form-horizontal" action="?do=Insert" method="POST">

                    <!--Start Name-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-2 col-md-4">
                            <input type="text" name="name" autocomplete="off"  class="form-control" required="" />
                        </div>
                    </div>
                    <!-- End Name-->

                    <!--Start Description-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">email</label>
                        <div class="col-sm-2 col-md-4">
                            <input type="email" name="email"  placeholder=""  class="form-control" required="" />
                        </div>
                    </div>
                    <!-- End Description-->


                    <!--Start Ordering-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">password</label>
                        <div class="col-sm-2 col-md-4">
                            <input type="password" name="password" autocomplete="on"  class="form-control" required="" />
                        </div>
                    </div>
                    <!-- End Ordering-->

                    


                    <div class="form-group form-group-lg">
                        <div class="col-sm-offset-2 col-sm-10">
                            <input type="submit" name="save" class="btn btn-primary btn-lg" value="Save">
                        </div>
                    </div>


                </form>

            </div>

        </div>





    <?php  }  elseif ($do == 'manage') { ?>

    	

	<?php

		$stm = $con->prepare("SELECT * from users");
		$stm->execute();
		$rows = $stm->fetchAll();

	?>




		<?php if($_SESSION['yes'] == 'yes'){ ?>

    	<div class="alert alert-success alert-dismissible fade show" role="alert">
			  <strong>User</strong> Added Successfully.
			  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
			    <span aria-hidden="true">&times;</span>
			  </button>
		</div>


	<?php  } $_SESSION['yes'] = 'no';?>

    <?php if($_SESSION['update'] == 'update'){ ?>

        <div class="alert alert-success alert-dismissible fade show" role="alert">
              <strong>User</strong> Updated Successfully.
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
        </div>


    <?php  } $_SESSION['update'] = 'no';?>



    	<table class="table">
			  <thead>
			    <tr>
			      <th scope="col">#</th>
			      <th scope="col">name</th>
			      <th scope="col">email</th>
			    </tr>
			  </thead>
			  <tbody>
			  	<?php foreach($rows as $row){?>
			    <tr>
			      <th scope="row"><?php echo $row['id']?></th>
			      <td><?php echo $row['name']?></td>
			      <td><?php echo $row['email']?></td>
                  <td><a class="btn btn-primary" href="oop.php?do=edit&id=<?php echo($row['id']) ?>">Edit</td>
                    <td><a class="btn btn-danger" onclick="return confirm('Are you sure?')" href="oop.php?do=delete&id=<?php echo($row['id']) ?>">Delete</td>
			    </tr>
				<?php }?>
			  </tbody>
			</table>
    	
    	<?php




}elseif ($do == 'Insert') {
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		

		$formErrors = array();

        
            
            if (empty($_POST['name'])) {

                $formErrors[] = "<div class='alert alert-danger'><strong>Error: </strong> Name can not be empty</div>";
            }else{

                if (filter_var($_POST['name'],FILTER_SANITIZE_STRING) ) {
                    $name = $_POST['name'];
                }else{
                    $formErrors[] = "<div class='alert alert-danger'><strong>Error: </strong> Name Not Valid</div>";
                }


            }

            if (empty($_POST['email'])) {

                $formErrors[] = "<div class='alert alert-danger'><strong>Error: </strong> Email can not be empty</div>";
            }else{

               if (filter_var($_POST['email'],FILTER_VALIDATE_EMAIL) ) {
                    $email = $_POST['email'];
                }else{
                    $formErrors[] = "<div class='alert alert-danger'><strong>Error: </strong> Email Not Valid</div>";
                }
                
            }

            if (empty($_POST['password'])) {

                $formErrors[] = "<div class='alert alert-danger'><strong>Error: </strong> Password can not be empty</div>";
            }else{

                 if (filter_var($_POST['password'],FILTER_SANITIZE_STRING) ) {
                    $password = sha1($_POST['password']);
                }else{
                    $formErrors[] = "<div class='alert alert-danger'><strong>Error: </strong> Password Not Valid</div>";
                }
            }

            foreach ($formErrors as $error) {
                echo  $error;
            }


        if (empty($formErrors)) {

        	$stm = $con->prepare("INSERT into users (name,email,password) values (:name,:email,:password)");
        	$stm->execute(array(
        		'name' => $name,
        		'email' => $email,
        		'password' => $password

        	));

        	$_SESSION['yes'] = 'yes';
			header("Location: oop.php?do=manage"); 
			exit();
        	
        }


	}
}elseif($do == 'edit'){


    $user_id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0 ;

    $stm = $con->prepare('SELECT * from users where id = ? limit 1');
    $stm->execute(array($user_id));
    $row = $stm->fetch();
    $count = $stm->rowCount();

    if ($count > 0) {
        # code...
    


    ?>

    <div class="edit">
            <h1 class="text-center">Edit User</h1>

            <div class="container">

                <form class="form-horizontal" action="?do=update" method="POST">

                    <input type="hidden" value="<?php echo$row['id'] ?>" name='id'>

                    <!--Start Name-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-2 col-md-4">
                            <input value="<?php echo $row['name']?>" type="text" name="name" autocomplete="off"  class="form-control" required="" />
                        </div>
                    </div>
                    <!-- End Name-->

                    <!--Start Description-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">email</label>
                        <div class="col-sm-2 col-md-4">
                            <input value="<?php echo $row['email']?>" type="email" name="email"  placeholder=""  class="form-control" required="" />
                        </div>
                    </div>
                    <!-- End Description-->


                    <!--Start Ordering-->
                    <div class="form-group form-group-lg">
                        <label class="col-sm-2 control-label">password</label>
                        <div class="col-sm-2 col-md-4">
                            <input value="" type="password" name="password" autocomplete="on"  class="form-control"  />
                        </div>
                    </div>
                    <!-- End Ordering-->

                    


                    <div class="form-group form-group-lg">
                        <div class="col-sm-offset-2 col-sm-10">
                            <input type="submit" name="save" class="btn btn-primary btn-lg" value="Save">
                        </div>
                    </div>


                </form>

            </div>

        </div>






<?php }else{
    echo "User Not Found";
}



 }elseif($do == 'update'){

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
        

        $formErrors = array();

        
            
            if (empty($_POST['name'])) {

                $formErrors[] = "<div class='alert alert-danger'><strong>Error: </strong> Name can not be empty</div>";
            }else{

                if (filter_var($_POST['name'],FILTER_SANITIZE_STRING) ) {
                    $name = $_POST['name'];
                }else{
                    $formErrors[] = "<div class='alert alert-danger'><strong>Error: </strong> Name Not Valid</div>";
                }


            }

            if (empty($_POST['email'])) {

                $formErrors[] = "<div class='alert alert-danger'><strong>Error: </strong> Email can not be empty</div>";
            }else{

               if (filter_var($_POST['email'],FILTER_VALIDATE_EMAIL) ) {
                    $email = $_POST['email'];
                }else{
                    $formErrors[] = "<div class='alert alert-danger'><strong>Error: </strong> Email Not Valid</div>";
                }
                
            }

            if (!empty($_POST['password'])) {

                 if (filter_var($_POST['password'],FILTER_SANITIZE_STRING) ) {
                    $password = sha1($_POST['password']);
                }else{
                    $formErrors[] = "<div class='alert alert-danger'><strong>Error: </strong> Password Not Valid</div>";
                }
            }

            foreach ($formErrors as $error) {
                echo  $error;
            }

            $id = $_POST['id'];


        if (empty($formErrors)) {

            $stm = $con->prepare("UPDATE  users set name=? ,email=? , password=? where id=? ");
            $stm->execute(array(
                 $name,
                 $email,
                 $password,
                 $id

            ));

            $_SESSION['update'] = 'update';
            header("Location: oop.php?do=manage"); 
            exit();
            
        }


    }




}elseif ($do == 'delete') {
    
    $user_id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0 ;

    $stm = $con->prepare('SELECT * from users where id = ? limit 1');
    $stm->execute(array($user_id));
    $row = $stm->fetch();
    $count = $stm->rowCount();


    if ($count > 0) {

            $stmt = $con->prepare("DELETE FROM users WHERE id = :id");
            $stmt->bindParam(":id", $user_id);
            $stmt->execute();

            $_SESSION['delete'] = 'delete';
            header("Location: oop.php?do=manage"); 
            exit();
        }

}else{

	 header('Location:oop.php?manage');
    exit();
}



?>


		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	</body>
</html>

 





