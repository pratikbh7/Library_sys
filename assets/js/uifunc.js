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
    query_ap = "http://localhost";
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
                    query_ap += updateQueryStringParameter( uri, query_stat, value );
                    window.location.replace(query_ap);
                }
            }        
        })
    })
    $('#installation-form').on( 'submit', function(e){
        e.preventDefault();
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
                    query_ap += updateQueryStringParameter( uri, query_stat, value );
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
    });
    var message, child, method, action_data, formdata;
    $('#navbar > a').on( 'click', function(e){
        e.preventDefault();
        console.log(query_ap);
        method = this.id;
        child = $('.books_data').children(':first-child');
        if(method === 'dash'){
            window.location.href = "/user-interface/homepage.php";
        }
        else if( method === 'list' || method === 'issued_list' || method === 'add_form'){
            uri = "/user-interface/homepage.php";
            query_stat = "getPage";
            switch(method){
                case 'list':
                    value = "list_books";
                    query_ap += updateQueryStringParameter( uri, query_stat, value);
                    window.location.href = query_ap;
                    break;
                
                case 'issued_list':
                    value = "list_issued";
                    query_ap += updateQueryStringParameter( uri, query_stat, value);
                    window.location.href = query_ap;
                    break;

                case 'add_form':
                    value = "add_form_get";
                    query_ap += updateQueryStringParameter( uri, query_stat, value);
                    window.location.href = query_ap;
                    break;
            }
        }
        else{
            $('.ajax-status').text('Invalid URL request');
        }
        
    });
    var author, title, release_year;
    $('#add-book').on( 'submit', function(e){
        e.preventDefault();
        method = "add";
        author = $('#book_author').val();
        title = $('#book_title').val();
        release_year = $('#book_year').val();
        formdata= { author: author, title: title, release_year: release_year };
        action_data = { action: method, data: formdata};
        $.ajax({
            method: 'POST',
            url: '/user-interface/homepghandlers.php',
            dataType: 'json',
            data: action_data,
            success: function(data){
                console.log(data);
                if( data.status === "success"){
                    $('.ajax-status').text('Book added successfully');
                }
                else if( data.status === "failure"){
                    $('.ajax-status').text('Book could not be added to database');
                }
                else if( data.message === "exists"){
                    $('.ajax-status').text('Book already exists');
                }
                else if( data.status === "invalid call"){
                    $('.ajax-status').text('Invalid Call');
                }
            }
        })
    })
   }); 
})(jQuery); 