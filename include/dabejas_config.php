<?PHP
require_once("./include/autenticacion.php");

$autenticacion = new Autenticacion();

//Provide your site name here
$autenticacion->SetWebsiteName('localhost');

//Provide the email address where you want to get notifications
$autenticacion->SetAdminEmail('tguanangui@gmail.com');

//Provide your database login details here:
//hostname, user name, password, database name and table name
//note that the script will create the table (for example, fgusers in this case)
//by itself on submitting register.php for the first time
$autenticacion->InitDB(/*hostname*/'localhost',
                      /*username*/'tatianag',
                      /*password*/'Cpsr19770428',
                      /*database name*/'bdd_seguridades',
                      /*table name*/'usuarios');

//For better security. Get a random string from this link: http://tinyurl.com/randstr
// and put it here
$autenticacion->SetRandomKey('qSRcVS6DrTzrPvr');

?>