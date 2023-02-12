function updateQueryStringParameter(uri, key, value) {
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var separator = uri.indexOf('?') !== -1 ? "&" : "?";
    if (uri.match(re)) {
      return uri.replace(re, '$1' + key + "=" + value + '$2');
    }
    else {
      return uri + separator + key + "=" + value;
    }
  }
//revert back $ alias just in case another plugin uses it
jQuery.noConflict(); 
(function($){
   $(document).ready(function(){
    var username, password, uri, query_stat, value, query_ap;
    $('#input-form').on('submit', function(e){
        e.preventDefault();
        username = document.forms['input-form']['username'].value;
        password = document.forms['input-form']['password'].value;
        if( username === '' || password === ''){
            alert("fill out the required fields");
            return false;
        }
        const login_data = { login_input: { username: username, password: password} };
        //ajax submission to avoid the annoying form resubmission
        $.ajax({
            method: 'POST',
            url: 'user-interface/ajax-front-handlers.php',
            dataType: 'json',
            cache: false,
            data: login_data,
            success: function(data){
                if( data.status === "unauthorized"){
                    $('#input-form').css('outline', '3px solid red');
                    var append_errors;
                    const error_class = $('.error_class');
                    error_class.children().remove('p');
                    data.errors.forEach( function(element){
                        append_errors = document.createElement('p');
                        append_errors.textContent = element;
                        error_class.append(append_errors);
                    });
                }
                else if( data.status === "authorized" ){
                    //replace to remove previous page option
                    uri = '/user-interface/homepage.php';
                    query_stat = 'status';
                    value = "success";
                    query_ap = updateQueryStringParameter( uri, query_stat, value );
                    window.location.replace(query_ap);
                }
            }        
        })
    })
    $('#installation-form').on( 'submit', function(e){
        e.preventDefault();
        console.log('gg');
        username = document.forms['installation-form']['admin_name'].value;
        password = document.forms['installation-form']['admin_password'].value;
        if( username === '' || password === ''){
            alert("fill out the required fields");
            return false;
        }
        const installation_data = { installation_data: { username: username, password: password } };
        $.ajax({
            method: 'POST',
            url: 'user-interface/ajax-front-handlers.php',
            dataType: 'json',
            cache: false,
            data: installation_data,
            success: function(data){
                if( data.status === "success" ){
                    uri = '/index.nginx-debian.php';
                    query_stat = "status";
                    value = "success";
                    query_ap = updateQueryStringParameter( uri, query_stat, value );
                    window.location.replace(query_ap);
                }
                else if( data.status === "failure" ){
                    const error_class = $('.error_class');
                    error_class.children().remove('p');
                    const error = document.createElement('p');
                    error.textContent = "Some error encountered with server";
                    error_class.append(error); 
                }
            }
        })
    })
    $('#navbar > a').on( 'click', function(e){
        e.preventDefault();
        var message;
        var method = this.textContent;
        if(method === 'DASHBOARD'){
            window.location.href = "/user-interface/homepage.php";
        }
        // else{
        //     const action_data = { action : method };
        //     $.ajax({
        //         method: 'POST',
        //         url: '/user-interface/homepghandlers.php',
        //         dataType: 'json',
        //         data: action_data,
        //         success: function(data){
        //             if( data.status === "success"){
        //                 message = data.message;

        //             }
        //             else if( data.status === "failure"){
        //                 message= data.errormsg;
        //             }
        //         }
        //     });
        // }
        
    })
   }); 
})(jQuery); 