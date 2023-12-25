/!\ Don't expect updates except fixes for critical bugs. I don't host TF2 servers anymore, so... 

And don't forget "GeoIPCity" for proper Location support! 
If you have permission errors try to create the /cache/SQL folder manually.

Oh and you can safely delete cache files at any time. There is no auto clean for old cache files!

# player_analytics
This is a webpanel for the sourcemod plugin **[ANY] Player Analytics** by Dr. McKay
https://forums.alliedmods.net/showthread.php?t=230832

forked from Sinclair47/player_analytics https://github.com/Sinclair47/player_analytics 

If you have suggestions or you found a bug -> **contact me: thechaoscoder+player-analytics[at]gmail.com**  
(replace [at] with @, bot protection) or open an issue here

## Performance
For better SQL performance run this 2 queries:  
`ALTER TABLE player_analytics ADD INDEX (server_ip, connect_date);`  
`ALTER TABLE player_analytics ADD INDEX (connect_date);`

## How to install?
Download as zip (or clone) and copy all files to your webserver.  
Rename the file config_db_RENAME_ME.php to config_db.php in /inc and add your DB credentials.  
 
That's it!

### Login
To activate login-only mode, edit `inc/config.php` line `16`:

Change the value of `MUST_LOG_IN` to `true` and to disable it change it to `false`.
Add each person (SteamID) to `inc/admins.php` who is allowed to log in.

## Connect method
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

## Todo
* Add area charts to see changes over time
* Maybe add a Chart as in HLStatsX

##Webpanel
![Player Analytics Webpanel](https://raw.githubusercontent.com/theChaosCoder/player_analytics/master/player_analytics.png)
