 
  
    // function Validate() {
    //   var password = document.getElementById("pass").value;
    //   var confirmPassword = document.getElementById("cpassword").value;
    //   if (password != confirmPassword) {
    //     alert("Passwords do not match.");
    //     return false;
    //   }
    //   return true;
    // }
    function Validate() {
      var password = document.getElementById("pass").value;
      var confirmPassword = document.getElementById("cNewPassword").value;
      if (password != confirmPassword) {
        alert("Passwords do not match.");
        return false;
      }
      return true;
    }
  
    $(document).ready(function(){
     $("#pass").keyup(function(){
      check_pass();
    });
   });

    function check_pass()
    {
     var val=document.getElementById("pass").value;
     var meter=document.getElementById("meter");
     var no=0;
     if(val!="")
     {
  // If the password length is less than or equal to 6
  if(val.length<=6)no=1;

  // If the password length is greater than 6 and contain any lowercase alphabet or any number or any special character
  if(val.length>6 && (val.match(/[a-z]/) || val.match(/\d+/) || val.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/)))no=2;

  // If the password length is greater than 6 and contain alphabet,number,special character respectively
  if(val.length>6 && ((val.match(/[a-z]/) && val.match(/\d+/)) || (val.match(/\d+/) && val.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/)) || (val.match(/[a-z]/) && val.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/))))no=3;

  // If the password length is greater than 6 and must contain alphabets,numbers and special characters
  if(val.length>6 && val.match(/[a-z]/) && val.match(/\d+/) && val.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/))no=4;

  if(no==1)
  {
   $("#meter").animate({width:'50px'},300);
   meter.style.backgroundColor="red";
   document.getElementById("pass_type").innerHTML="Very Weak!!";
   pass_type.style.color="red";
 }

 if(no==2)
 {
   $("#meter").animate({width:'100px'},300);
   meter.style.backgroundColor="#F5BCA9";
   document.getElementById("pass_type").innerHTML="Weak!!";
   pass_type.style.color="red";
 }

 if(no==3)
 {
   $("#meter").animate({width:'150px'},300);
   meter.style.backgroundColor="#FF8000";
   document.getElementById("pass_type").innerHTML="Good!!";
   pass_type.style.color="green";
 }

 if(no==4)
 {
   $("#meter").animate({width:'200px'},300);
   meter.style.backgroundColor="green";
   document.getElementById("pass_type").innerHTML="Strong!!";
   pass_type.style.color="green";
 }
}

else
{
  meter.style.backgroundColor="white";
  document.getElementById("pass_type").innerHTML="";
}
}

function doDate()
      {
        var str = "";

        var days = new Array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
        var months = new Array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");

        var now = new Date();
    //var hour = c.getHours()%12;
    var am = now.getHours()/12;

    str += "" + days[now.getDay()] + ", " + months[now.getMonth()] + " " + now.getDate() + ", " + now.getFullYear() + " " + now.getHours()%12 +":" + now.getMinutes() + ":" + now.getSeconds() +" " + (am > 1 ? 'PM' : 'AM');
    document.getElementById("todaysDate").innerHTML = str;
  }

  setInterval(doDate, 1000);

 
  

function showPass(){


  $(document).ready(function() {
    $("#show_hide_password a").on('click', function(event) {
        event.preventDefault();
        if($('#show_hide_password input').attr("type") == "text"){
            $('#show_hide_password input').attr('type', 'password');
            $('#show_hide_password i').addClass( "fa-eye-slash" );
            $('#show_hide_password i').removeClass( "fa-eye" );
        }else if($('#show_hide_password input').attr("type") == "password"){
            $('#show_hide_password input').attr('type', 'text');
            $('#show_hide_password i').removeClass( "fa-eye-slash" );
            $('#show_hide_password i').addClass( "fa-eye" );
        }
    });
});


}  