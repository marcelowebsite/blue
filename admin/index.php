<?php include("includes/header.php"); ?>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <?php include("includes/top_nav.php"); ?>
</nav>

<div class="container-fluid">
    <div class="row">
        <?php include("includes/side_nav.php"); ?>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            <h1 class="page-header">Dashboard</h1>
            <ul class="nav nav-pills">
                <li role="presentation" class="active"><a href="#">Add a new Program</a></li>
                <li role="presentation"><a href="#">Add a new Study</a></li>
                <li role="presentation"><a href="#">Add a new Citation</a></li>
            </ul>
            <h2 class="sub-header">All BluePrints Programs </h2>
            <div class="table-responsive">

            <?php

            $sql = "SELECT * FROM program";

            $db = new db();
            $db = $db->connect();

            $stmt = $db->query($sql);
            $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
           // print_r($programs);

            function build_table($programs){
                // start table
                $html = '<table class="table table-striped">';
                // header row
                $html .= '<thead><tr>';
                foreach($programs[0] as $key=>$value){
                    $html .= '<th>' . htmlspecialchars($key) . '</th>';
                }
                $html .= '</tr></thead>';

                // data rows
                foreach( $programs as $key=>$value){
                    $html .= '<tbody><tr>';
                    foreach($value as $key2=>$value2){
                        $html .= '<td>' . htmlspecialchars($value2) . '</td>';
                    }
                    $html .= '</tr></tbody>';
                }

                // finish table and return it

                $html .= '</table>';
                return $html;
            }
            echo build_table($programs);
            ?>
        </div>
    </div>
    <?php include("includes/footer.php"); ?>
