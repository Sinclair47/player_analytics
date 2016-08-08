# player_analytics
This is a webpanel for the sourcemod plugin **[ANY] Player Analytics** by Dr. McKay
https://forums.alliedmods.net/showthread.php?t=230832

forked from Sinclair47/player_analytics https://github.com/Sinclair47/player_analytics

##How to install?
Download as zip (or clone) and copy all files to your webserver.  
Add your DB credentials in config.php.   
That's it!

##Connect method
> ### What is the difference between Quickplay and Quickpick?
> **Quickplay** = You click a button and get placed into a random server that has QP enabled  
> **Quickpick** = You get presented a list of servers that have QP enabled, where you have to click "connect" yourself.
> 
> Both are mostly non-existant in TF2 anymore (apart from MvM, which only has Quickplay).

> _Quote from pcmaster - https://forums.alliedmods.net/showthread.php?t=230832&page=31_

-
> ### Why do I have some strange values for connect_method in my DB like - 20312 or asd?
> People can supply their own connection methods using the connect command. For example:  
> **connect 127.0.0.1 myownreason** 
> will list your connection method as "myownreason".
> 
> _Quote from Dr. McKay - https://forums.alliedmods.net/showpost.php?p=2350131&postcount=256_

##Todo
* Remove/replace "Hours Played" box with something more usefull.
* Add a simple cache function
* Add area charts to see changes over time
* Maybe add a Chart as in HLStatsX

##Webpanel
![Player Analytics Webpanel](https://raw.githubusercontent.com/theChaosCoder/player_analytics/master/player_analytics.png)
