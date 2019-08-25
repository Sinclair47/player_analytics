crossroads.ignoreState= true;

// Define the routes
crossroads.addRoute('/', function() {
    pageLoader( "inc/getdashboard.php" );
});

crossroads.addRoute('/stats/locations', function() {
    pageLoader("inc/getlocation.php");
});
crossroads.addRoute('/stats/connections', function() {
    pageLoader( "inc/getconnections.php" );
});
crossroads.addRoute('/stats/players', function() {
   pageLoader( "inc/getplayers.php" );
});
crossroads.addRoute('/stats/players/info/{steamid}', function(steamid) {
    pageLoader( "inc/getplayerinfo.php?id="+steamid );
});
crossroads.addRoute('/stats/staff', function() {
    pageLoader( "inc/getStaff.php" );
});
crossroads.addRoute('/stats/maps', function() {
    pageLoader( "inc/getMap.php" );
});


crossroads.addRoute('/lab', function() {
    pageLoader( "inc/lab.php" );
});
crossroads.addRoute('/lab/{tab_id}', function(tab_id) {
        pageLoader( "inc/lab.php?tab_id="+tab_id );
});



crossroads.bypassed.add(function(request) {
    $( "#content" ).load( "error.php" );
    console.log(request + ' seems to be a dead end...');
});
crossroads.routed.add(console.log, console); //log all routes


function pageLoader(url) {
    $('#overlay').fadeIn("fast");
	$('#content').empty();
    $('#content').load( url , function() {
        $('#overlay').delay(200).fadeOut( "slow" );
    });
    //$('#overlay').delay(200).fadeOut( "slow" );

    //var cookie = getCookie("server");
    //console.log("js cookie: " + cookie);
    //$( "#header_server_ip" ).html(cookie);
}




//http://www.w3schools.com/js/js_cookies.asp
// function setCookie(cname, cvalue, exdays) {
//     console.log("setcookie: ");
//     var d = new Date();
//     d.setTime(d.getTime() + (exdays*24*60*60*1000));
//     console.log("D: "+ d.toGMTString());
//     var expires = "expires=" + d.toGMTString();
//     document.cookie = cname+"="+"cvalue"+"; "+expires;
// }

// function getCookie(cname) {
//     var name = cname + "=";
//     var ca = document.cookie.split(';');
//     for(var i=0; i<ca.length; i++) {
//         var c = ca[i];
//         while (c.charAt(0)==' ') {
//             c = c.substring(1);
//         }
//         if (c.indexOf(name) == 0) {
//             return c.substring(name.length, c.length);
//         }
//     }
//     return "";
// }

// function checkCookie() {
//     var user=getCookie("username");
//     if (user != "") {
//         alert("Welcome again " + user);
//     } else {
//        user = prompt("Please enter your name:","");
//        if (user != "" && user != null) {
//            setCookie("username", user, 30);
//        }
//     }
// }

//setup hasher
function parseHash(newHash, oldHash){
  crossroads.parse(newHash);
}
hasher.initialized.add(parseHash); // parse initial hash
//hasher.changed.add(console.log, console); //log all changes
hasher.changed.add(parseHash); //parse hash changes
hasher.init(); //start listening for history change

//hasher.setHash('');

 
//Listen to hash changes
// window.addEventListener("hashchange", function() {
//     console.log("asd");
//     var route = '/';
//     var hash = window.location.hash;
//     if (hash.length > 0) {
//         route = hash.split('#').pop();
//     }
//     crossroads.parse(route);
// });
 
// // trigger hashchange on first page load
// window.dispatchEvent(new CustomEvent("hashchange"));
