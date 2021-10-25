<!doctype html>
<html>
    <head>
        <title>Check username availability with jQuery and AJAX</title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

        <script>
            $(document).ready(function(){
                $("#username").keyup(function(){
                   
                    var username = $(this).val().trim();
                    if(username != ''){
                        $.ajax({
                            url: '/verify/username_verify.php',
                            type: 'post',
                            data: {username: username},
                            success: function(response){
                                $('#uname_response').html(response);
                             }
                        });
                    }else{
                        $("#uname_response").html("");
                    }
                    
                });

            });
        </script>


    </head>
    <body>

        <div>
   <input type="text" class="textbox" id="username" name="username" placeholder="Enter Username" />
            <!-- Response -->
            <div id="uname_response" >
                gfddf
            </div>
        </div>

    </body>
</html>