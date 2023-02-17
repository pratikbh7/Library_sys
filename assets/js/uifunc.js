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
    var offsets, corres_id, el_id, get_id_int,set_top,set_left;
    function revert_css(parent){
        parent.css({"width":"100px","height":"58px"});
        parent.children("a.boxclose").css({"margin-top":"-44px","margin-right":"-86px"});
        parent.children('a').hide();
        parent.children('ul').css({"display":"block"});
        parent.hide();
    }
    $('.boxclose').on('click', function(e){
        const parent = $(e.target).parent('.book_action');
        revert_css(parent);
    })
    $('.book_desc').on( 'click', function(e){
        e.preventDefault();
        var parent = $('.book_action');
        parent.hide();
        revert_css(parent);
        $("#action_form").hide();
        offsets = $(this)[0].getBoundingClientRect();
        set_top = (offsets.top + 18);
        set_left = (offsets.left);
        el_id = this.id;
        get_id_int = parseInt(el_id.match(/\d+/)[0]);
        corres_id = "#action_"+get_id_int;
        $(corres_id).css({"top":set_top+"px", "left":set_left+"px"});
        $(corres_id).slideDown("slow", function(){
            $( corres_id ).children('a').css({"display":"inline-block"});
        });
        $(corres_id).css({ "display":"flex"})
    });
    $('.the_action').on('click',function(e){
        e.preventDefault();
        var parent = $(e.target).closest(".book_action");
        var the_form = $('#action_form');
        if( this.textContent === "Issue"){
            parent.children("ul").css({"display":"none"});
            parent.children("a.boxclose").css({"margin-top":"-58px","margin-right":"-270px"});
            parent.css({"width": "275px", "height":"71px"});
            parent.append(the_form);
            the_form.css({"display": "block"})
        }
    });
    $('#action_form').on('submit', function(e){
        e.preventDefault();
        var burrower = $('#burrower').val();
        var parent = $(e.target).closest(".book_action");
        var parent_id = $(parent).attr("id");
        get_id_int = parseInt(el_id.match(/\d+/)[0]);
        corres_id = "#identifier_"+get_id_int;
        var book_details = $(corres_id).children('td');
        const book_title = book_details.children('a')[0].textContent;
        const book_author = book_details[1].textContent;
        const book_ryear = book_details[2].textContent;
        method = "issue";
        formdata = { author: book_author, title: book_title, release_year: book_ryear, burrower: burrower };
        action_data = { action: method, data: formdata};
        $.ajax({
            method: 'POST',
            url: '/user-interface/homepghandlers.php',
            dataType: 'json',
            data: action_data,
            success: function(data){
                if( data.status === "success"){
                    $('.action_status > p').text('Book issued successfully');
                }
            }
        })
    })
   }); 
})(jQuery); 