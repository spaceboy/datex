# Datex
Simple command line tool for rapid development of database-related routines work (creating _entity_, _form_ and _model_ PHP classes/files) under PHP/Nette framework environment.

## 1 Installation

### 1.1 Install tool
In command line, type
```
>composer require spaceboy/datex
```

### 1.2 Copy script file
Copy file `datex.php` from
```
APP_ROOT/vendor/spaceboy/datex/bin/datex.php
```
to
```
APP_ROOT/app/bin/datex.php
```

### 1.3 Register service DatexModel
In `APP_ROOT/config/services.neon` add service into service list:
```
services:
	- Spaceboy\Datex\DatexModel(%datex%)
```


## 2 Configuration

### 2.1 Add configuration parameters
In `APP_ROOT/config/services.neon` add `datex` section into `parameters`:
```
parameters:
	datex:
		entity:
			namespace: App\Model\Entities
			path: app/model/entities
			template: templateEntity.phtml
		form:
			namespace: App\Forms
			path: app/forms
			template: templateForm.phtml
		model:
			namespace: App\Model
			path: app/model/models
			template: templateModel.phtml
```

### 2.2 Connect to database
Before first run, make sure your database connection is correctly set.

In `database` section of your config file (`APP_ROOT/config/local.neon` or `APP_ROOT/config/common.neon`) should be your DB connection described somehow like that:
```
database:
	dsn: 'mysql:host=127.0.0.1;dbname=my_database'
	user: db_user
	password: *****
```


## Use script
In command line terminal, go to the `APP_ROOT/bin` directory and try very first run:
```
>php datex.php
```

If anything goes wrong, **clear the cache** and try again.

Works? Great. You can use the script in simple, classic way:
```
>php datex.php command [--parameter] [--switch]
```

### Commands:
**tables**
```
>php datex.php tables
```
Writes list of accessible tables and views in database.

**columns**
```
>php datex.php columns --table table_name
>php datex.php columns -t=table_name
```
Writes list of columns of database table.

**entity**
```
>php datex.php entity --table table_name [--file path/to/EntityFile.php] [--overwrite] [--screen]
>php datex.php entity -t=table_name [-f="path/to/EntityFile.php"] [-o] [-s]
```
Creates PHP **entity** file based on colums of database table.

The file is placed in default directory declared in _config_ section, or specified if _file_ parameter of script.

If the target file already exists, script halts unless the `---overwite` (`-o`) switch is used.

If the `--screen` (`-s`) switch is used, source code of PHP file is shown on screen and no file is written.

**form**
```
>php datex.php form --table table_name [--file path/to/EntityFile.php] [--overwrite] [--screen]
>php datex.php form -t=table_name [-f="path/to/EntityFile.php"] [-o] [-s]
```
Works same way as _entity_ command, except that creates nette **form** file based on colums of database table.

**model**
```
>php datex.php model --table table_name [--file path/to/EntityFile.php] [--overwrite] [--screen]
>php datex.php model -t=table_name [-f="path/to/EntityFile.php"] [-o] [-s]
```
Works same way as _entity_ and _form_ command, except that creates PHP **model** file based on colums of database table.
