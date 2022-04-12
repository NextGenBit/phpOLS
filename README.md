# phpOLS
A **very simple** binary reader/editor for automotive files using WinOLS map pack. 

All maps must have an ID assigned to read/edit them. 
To export the map pack from winOLS in order to be compatible with this project go to **Project -> Export & Import -> Export Csv/map list**

Be sure to check the **All Columns** and the **Addresses as hex** options.

Example

```php
  $phpOLS->getMapById( "PQAV" )->display();
  $phpOLS->getMapById( "PQAV" )->Change(0,0,500)->display();
```

![](https://i.ibb.co/8DYJ43W/Screenshot-1.png|width=100)

