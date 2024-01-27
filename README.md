# OpenVel
Laravel powered website package for OpenSimulator grids.

Orginally writen for Canadian Grid

## Robust.ini
```ini
[LoginService]
	;WelcomeMessage = "Welcome to virtual Canada eh!"
    MessageUrl = "https://yourdomain.whatever/getwelcome"

[GridInfoService]
	economy = https://helpers.yourdomain.whatever/
```

Some config changes are needed to get up and running, 
mostly in the .env changing canadiangrid.ca to your domain

Web map just open map.js and change the domains there.
If your grid is NOT SSL then best to connect to the map as http:// NOT https://

Make sure the follow folders are full perm (chmod 0666)

storage and its sub folders, public/imgs

Thats mostly it eh, if you know Laravel or are learning it then the rest SHOULD be easy.

oh one last thing. For some reason GitHub isnt picking up welcome.json in the storage/app folder so heres a sample
```json
[
	{
		"msg": "Welcome to virtual Canada eh"
	},
	{
		"msg": "No stealing our Timmys eh"
	}
]
```
With that and MessageUrl in your robust ini you can have randomish welcome messages without rebooting robust for WelcomeMessage change.
