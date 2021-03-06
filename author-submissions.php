<?php

      require_once('config.php');
      session_start();

      if(isset($_SESSION['email'])){
        $email = $_SESSION['email'];
        $user_type = $_SESSION['type'];

        if ($user_type != 2) {
            header('Location: main.php');
        }
        
      }else{
        header("location:index.php");
      }
 ?>
<html>
    <head>
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous" />
            <link rel="stylesheet" href="css/author-submissions.css" />
            <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.3.1.min.js"></script>
            <script src="js/author-submissions.js"></script>
            <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
            <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    </head>
    <body>
      <div id="top-panel" align="center">
  			<div id="nav-bar">
          <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="#">Scilib</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
              <ul class="navbar-nav">
                <li class="nav-item active">
                  <a class="nav-link" href="main.php">Home <span class="sr-only">(current)</span></a>
                </li>
                             <li class="nav-item">
               <a class="nav-link" id="navbar-subscriptions" href="subscriptions.php">My Subscriptions</a>
             </li>
                <?php
                    // Reviewer
                    if($user_type == 1){
                      echo '<li class="nav-item">
                              <a class="nav-link" id="reviewer-submission" href="reviewer-submission.php">My Invitations</a>
                            </li>';
                    // Author
                  }else if($user_type == 2){
                      echo '<li class="nav-item">
                                    <a class="nav-link" id="author-submission" href="author-submissions.php">My Submissions</a>
                                  </li>';
                      echo '<li class="nav-item">
                                    <a class="nav-link" id="author-submission" href="author-publications.php">My Publications</a>
                                  </li>';
                    }
                    // Editor
                    else if($user_type == 3){
                      echo '<li class="nav-item">
                              <a class="nav-link" id="submissions" href="editor-submission.php">My Submission</a>
                            </li>';
                    }else{ // Subscriber

                    }
                 ?>
                 <li class="nav-item">
                   <a class="nav-link" id="navbar-institution" href="institutions.php">Institutions</a>
                 </li>
                 <li class="nav-item">
                   <a class="nav-link" id="navbar-conferences" href="conferences.php">Conferences</a>
                 </li>
              </ul>
              <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                  <a class="nav-link" id="navbar-email" href="#"><i><?php echo $_SESSION['email']; ?></i></a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="navbar-logout" href="logout.php">Logout</a>
                </li>
              </ul>
            </div>
          </nav>
  			</div>
        <br />
        <div class="container" align="center">
            <div class="author_submissions_div">

            <div class="popup_div">

                <div class="popup-content">
                    <span class="close">&times;</span>
                    <h3 class="heading"></h3>
                </div>

            </div>

                    <?php
                    $email = $_SESSION['email'];

                    getAuthorSubmissions();

                    function getAuthorSubmissions()
                    {
                        global $dbc;
                        global $email;

                        $selAuthSubmissions = "select submission.s_id as s_id, title, p_name, status from submission, submits
                        where submission.s_id = submits.s_id and submission.status < 4
                        and submits.email = '".$email."' order by date DESC;";

                        $stmt = @mysqli_query($dbc,$selAuthSubmissions) or die(mysqli_error($dbc));
                        if (mysqli_num_rows($stmt) > 0) {
                          echo '<table class="table table-striped table-bordered">
                          <thead align="center"><tr><th>Title</th><th>Publisher</th><th>Status</th><th>Feedback</th><th colspan="2">Options</th></tr></thead><tbody align="center">';
                        }
                        while($row = @mysqli_fetch_array($stmt))
                        {
                            $publishBtn = 'disabled';
                            $cancelBtn = 'disabled';
                            $feedbackBtn = 'disabled';
                            $statusStr = '';

                            switch( $row['status'])
                            {
                                case 0:
                                {
                                    $statusStr = 'Submitted';
                                    $cancelBtn = '';
                                }
                                break;
                                case 1:
                                {
                                    $statusStr = 'Under reviewing';
                                    $cancelBtn = '';
                                }
                                break;
                                case 2:
                                {
                                    $statusStr = 'Under editor last check';
                                    $cancelBtn = '';
                                }
                                break;
                                case 3:
                                {
                                    $statusStr = 'Waiting approval from author(s)';
                                    $cancelBtn = '';
                                    $feedbackBtn = '';
                                    $publishBtn = '';
                                }
                                break;
                                case 4:
                                {
                                    $statusStr = 'Sent for publication';
                                    $cancelBtn = '';
                                    $feedbackBtn = '';
                                    $publishBtn = 'disabled';
                                }
                                break;
                                case 5:
                                {
                                    $statusStr = 'Published';
                                    $cancelBtn = 'disabled';
                                    $feedbackBtn = '';
                                    $publishBtn = 'disabled';
                                }
                                break;
                                case 6:
                                {
                                    $statusStr = 'Rejected';
                                    $cancelBtn = 'disabled';
                                    $feedbackBtn = '';
                                    $publishBtn = 'disabled';
                                }
                                break;
                            }

                            echo '<tr><td><a id="'.$row['s_id'].'">'.$row['title'].'</a></td>
                                    <td>'.$row['p_name'].'</td>
                                    <td>'.$statusStr.'</td>
                                    <td><button class="see_feedback_btn" '.$feedbackBtn.'>See feedback</button></td>
                                    <td><button class="publish_btn" '.$publishBtn.'>Publish</button></td>
                                    <td><button class="cancel_btn" '.$cancelBtn.'>Cancel</button></td></tr>';
                        }
                        echo '</tbody></table>';

                        @mysqli_stmt_close($stmt);

                    }

                    //@mysqli_close($dbc);
                    //onclick="functions.php?cancel=true&s_id='.$row['s_id'].'"

                    ?>
            </div>
            <br />
            <div class="make_nsubmission_div col-6">
                <form method="post" action="functions.php">
                    <div class="form-group row">
                        <input type="text" class="form-control" name="nsubmission_title" placeholder="Title" required>
                    </div>
                    <div class="form-group row">
                        <input type="text" class="form-control col-6" name="nsubmission_link" placeholder="Google Doc Link" required>

                        <?php

                            //require_once('config.php');
                            getPublishers();

                            function getPublishers()
                            {
                                global $dbc;

                                $selPublishers = "select p_name from publisher;";

                                $stmt = @mysqli_query($dbc,$selPublishers) or die(mysqli_error($dbc));

                                echo '<select class="form-control col-4 offset-2" name="nsubmission_publisher" placeholder="Publisher" required>';
                                while($row = @mysqli_fetch_array($stmt))
                                {
                                    echo '<option value="'.$row['p_name'].'">'.$row['p_name'].'</option>';
                                }
                                echo '</select>';

                                @mysqli_stmt_close($stmt);

                            }
                        ?>


                    </div>
                    <div class="form-group row">
                        <select class="form-control coauthors_select" name="coauthors_emails[]" multiple></select>
                        <!--<input type="email" class="form-control" name="coauthors_emails" placeholder="Co-Authors emails seperated by comma">-->
                    </div>
                    
                    <div class="form-group row">
                        <label for="expertises" style="display: block; text-align: left;">Field(s) of expertise (Hold CTRL to select more)</label>

                        <?php

                        //require_once('config.php');
                        getExpertises();

                        function getExpertises()
                        {
                            global $dbc;

                            $selExpertises = "select tag from expertise;";

                            $stmt = @mysqli_query($dbc,$selExpertises) or die(mysqli_error($dbc));

                            echo '<select class="form-control" name="expertises[]" placeholder="Expertise" required multiple>';
                            while($row = @mysqli_fetch_array($stmt))
                            {
                                echo '<option value="'.$row['tag'].'">'.$row['tag'].'</option>';
                            }
                            echo '</select>';

                            @mysqli_stmt_close($stmt);

                        }
                        ?>

                    </div>
                    <div class="form-group">
                        <button type="submit" name="make_a_submission" class="form-control btn btn-primary">Make a submission</button>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>
