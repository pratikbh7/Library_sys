function add_admin_form(e){
    e.stopPropagation();
    const sel = document.getElementById.bind(document);
    var target = e.target || e.srcElement;
    var front_el, back_el;
    if( target.id === 'add-admin' ){
        front_el = sel('input-form');
        back_el = sel('add-admin-form');
    }
    else{
        front_el = sel('add-admin-form');
        back_el = sel('input-form');
    }
    const start_anim = new the_animation( front_el, back_el, 400 );
}
var elapsed;
const cancelAnimationFrame = window.cancelAnimationFrame || window.mozCancelAnimationFrame;
function the_animation( el, elb, end_point ){
    this.el = el;
    this.elb = elb;
    this.dx = ( this.end_point === 400 ) ? ( 90 / 400 ) : - ( 90/400 );
    this.end_point = end_point; 
    this.startTime = window.performance.now ? performance.now() : Date.now();
    requestAnimationFrame( this.tick.bind(this) );
}
the_animation.prototype.tick = function( time){
    this.now = ( !this.now ) ? time : this.now;
    elapsed = ( time - this.now );
    if( this.end_point === 0 ){
        elapsed = 400 - elapsed;
        if( elapsed < this.end_point){
            elapsed = 0;
        }
    }
    else{
        if( elapsed > this.end_point ){
            elapsed = 400;
        }
    }
    this.el.style.transform = "rotate3d(0,1,0," + ( elapsed * this.dx ) + "deg)";
    if( elapsed === this.end_point ){
        cancelAnimationFrame(this.req);
        if( this.end_point !== 0){
            this.el.style.zIndex = -1;
            this.elb.style.zIndex = 1;
            now = null;
            const second_anim = new the_animation( this.elb, this.el, 0 );
        }
    }
    else{
        this.req = requestAnimationFrame( this.tick.bind(this) );
    }
}
//revert back $ alias just in case another plugin uses it
jQuery.noConflict(); 
(function($){
   $(document).ready(function(){
    $('#input-form').on('submit', function(e){
        e.preventDefault();
        const username = document.forms['input-form']['username'].value;
        const password = document.forms['input-form']['password'].value;
        if( username === '' || password === ''){
            alert("fill out the required fields");
            return false;
        }
        const data = { login_input: { username: username, password: password} };
        //ajax submission to avoid the annoying form resubmission
        $.ajax({
            method: 'POST',
            url: 'user-interface/ajax-front-handlers.php',
            dataType: 'json',
            cache: false,
            data: data,
            success: function(data){
                if( data.status === "unauthorized"){
                    $('#input-form').css('outline', '3px solid red');
                    var append_errors;
                    const error_class = $('.error_class');
                    error_class.children().remove('p');
                    data.errors.forEach( function(element){
                        append_errors = document.createElement('p');
                        append_errors.innerHTML = element;
                        error_class.append(append_errors);
                    });
                }
                // else if( data.status === "authorized")
            }        
        })
    })
   }); 
})(jQuery); 