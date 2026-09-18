<script type="text/javascript">
    function doDate()
    {
    var str = "";

    var days = new Array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
    var months = new Array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");

    var now = new Date();
    //var hour = c.getHours()%12;
    var am = now.getHours()/12;

    str += "Today is: " + days[now.getDay()] + ", " + months[now.getMonth()] + " " + now.getDate() + ", " + now.getFullYear() + " " + now.getHours()%12 +":" + now.getMinutes() + ":" + now.getSeconds() +" " + (am > 1 ? 'PM' : 'AM');
    document.getElementById("todaysDate").innerHTML = str;
}

setInterval(doDate, 1000);
</script>