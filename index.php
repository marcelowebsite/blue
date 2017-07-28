
<?php include("includes/header.php"); ?>



        <div class="row">

            <!-- Blog Entries Column -->
            <div class="col-md-8">

    <?php

                $sql = "SELECT `program_name` FROM program";

    $db = new db();
    $db = $db->connect();

    $stmt = $db->query($sql);
    $programs = $stmt->fetchAll(PDO::FETCH_OBJ);
    print_r($programs);

         ?>

            </div>




            <!-- Blog Sidebar Widgets Column -->
            <div class="col-md-4">

            
                 <?php include("includes/sidebar.php"); ?>



        </div>
        <!-- /.row -->

        <?php include("includes/footer.php"); ?>
